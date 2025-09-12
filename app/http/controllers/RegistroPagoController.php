<?php

require_once "app/models/ConductorPagoModel.php";
require_once "app/models/ConductorRegFinanciamientoModel.php";
require_once "app/models/ConductorCuotaModel.php";
require_once "utils/lib/mpdf/vendor/autoload.php";
require_once "app/models/Conductor.php";
require_once "app/models/Usuario.php";
require_once "app/models/PagoInscripcion.php";
require_once 'app/models/Comision.php';
require_once "app/models/Vehiculo.php";

use Mpdf\Mpdf;

class RegistroPagoController extends Controller
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    public function guardarRegistroPago()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['id_conductor']) || !isset($data['tipo_pago'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        $id_conductor = $data['id_conductor'];
        $tipo_pago = $data['tipo_pago'];
        $fecha_actual = date('Y-m-d');
        $monto_pago = $data['monto_pago'];
        $monto_inicial = isset($data['monto_inicial']) ? $data['monto_inicial'] : null;

        $conductorPagoModel = new ConductorPagoModel();

        // Verificar si ya existe un registro para este conductor
        if ($conductorPagoModel->existeRegistro($id_conductor)) {
            echo json_encode(['success' => false, 'message' => 'Ya existe un registro de pago para este conductor']);
            return;
        }

        $id_pago = $conductorPagoModel->registrarPago($id_conductor, $tipo_pago, $fecha_actual, $monto_pago);

        // Registrar comisión automáticamente
        if ($id_pago) {
            // Obtener tipo de vehículo
            $vehiculoModel = new Vehiculo();
            $vehiculo = $vehiculoModel->obtenerPlacaPorConductor($id_conductor);
            $tipo_vehiculo = $vehiculo ? $vehiculo['tipo_vehiculo'] : 'auto';
            
            // Registrar comisión
            $comisionModel = new Comision();
            $usuario_id = $_SESSION['usuario_id'];
            $monto_comision = $comisionModel->obtenerMontoComision('inscripcion', $tipo_vehiculo, $usuario_id);
            
            if ($monto_comision > 0) {
                $id_comision = $comisionModel->registrarComision(
                    $usuario_id,
                    'inscripcion',
                    $id_pago,
                    $monto_comision,
                    $tipo_vehiculo,
                    "Comisión por inscripción - Pago " . ($tipo_pago === 'contado' ? 'contado' : 'financiado')
                );
                
                if (!$id_comision) {
                    error_log("Error al registrar comisión para el pago ID: $id_pago");
                }
            }
        }

        if (!$id_pago) {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el pago']);
            return;
        }

        if ($tipo_pago == 'financiado') {
            $conductorRegFinanciamientoModel = new ConductorRegFinanciamientoModel();
            $id_financiamiento = $conductorRegFinanciamientoModel->registrarFinanciamiento(
                $id_conductor,
                $data['numero_cuotas'],
                $data['frecuencia_pago'],
                $data['fechas_vencimiento'][0],
                end($data['fechas_vencimiento']),
                $data['monto_cuota'],
                $data['tasa_interes'],
                $monto_inicial // Modificado:
            );

            if (!$id_financiamiento) {
                echo json_encode(['success' => false, 'message' => 'Error al registrar el financiamiento']);
                return;
            }

            $conductorCuotaModel = new ConductorCuotaModel();
            foreach ($data['cuotas'] as $index => $monto_cuota) {
                $fecha_vencimiento = $data['fechas_vencimiento'][$index];
                // Validar y formatear la fecha
                if (!$this->validarFormatoFecha($fecha_vencimiento)) {
                    echo json_encode(['success' => false, 'message' => 'Formato de fecha inválido']);
                    return;
                }
                $result = $conductorCuotaModel->registrarCuota(
                    $id_financiamiento,
                    $index + 1,
                    $fecha_vencimiento,
                    $monto_cuota,
                    'pendiente'
                );
                if (!$result) {
                    echo json_encode(['success' => false, 'message' => 'Error al registrar las cuotas']);
                    return;
                }
            }

            // Verificar si el monto inicial es "0" o "0.00", en ese caso, salir del if
            if ($monto_inicial == "0" || $monto_inicial == "0.00") { // Agregado: Condición para evitar la ejecución si el monto es 0
                echo json_encode(['success' => true, 'message' => 'Registro de financiamiento completado, pero sin pago inicial']); // Agregado: Mensaje de éxito sin pago inicial
                return; // Agregado: Salir del proceso
            }

            $fechaHora = date("Y-m-d H:i:s");

            // Obtener datos del conductor
            $conductorModel = new Conductor();
            $conductorData = $conductorModel->obtenerDetalleConductor($id_conductor);
            if (!$conductorData) {
                echo json_encode(['success' => false, 'message' => 'No se encontraron datos del conductor']);
                return;
            }

            $idAsesor = $_SESSION['usuario_id'];
            $usuarioModel = new Usuario();
            $asesorData = $usuarioModel->getData($idAsesor);
            $nombreAsesor = $asesorData['nombres'] . ' ' . $asesorData['apellidos'];

            // Cargar plantilla HTML
            $rutaBase = "app" . DIRECTORY_SEPARATOR . "contratos" . DIRECTORY_SEPARATOR . "nota_venta_inscripcion.html";
            $html = file_get_contents($rutaBase);

            $rutaLogo = 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'logo-ticket.png'; // Definir la ruta del logo
            $html = str_replace('{LOGO}', $rutaLogo, $html); // Reemplazar {LOGO} con la ruta

            // Reemplazar etiquetas en el HTML
            $html = str_replace([
                '<span id="fecha"></span>',
                '<span id="nombre_conductor"></span>',
                '<span id="documento"></span>',
                '<span id="nro_documento"></span>',
                '<span id="monto_pagado"></span>',
                '<span id="total_pagar"></span>',
                '<span id="vuelto"></span>',
                '<span id="total_ingresado"></span>',
                '<span id="metodo_pago"></span>',
                '<span id="asesor"></span>',
                '<div id="detalle_cuotas"></div>'
            ], [
                $fechaHora,
                $conductorData['nombre_completo'],
                $conductorData['tipo_doc'],
                $conductorData['nro_documento'],
                number_format($monto_inicial, 2),
                number_format($monto_inicial, 2),
                "0.00",
                number_format($monto_inicial, 2),
                "Efectivo",
                $nombreAsesor,
                "Cuota Inicial: S/. " . number_format($monto_inicial, 2)
            ], $html);

            // Generar y guardar el PDF
            $mpdf = new \Mpdf\Mpdf([
                'format' => [132, 210],
                'default_font_size' => 9
            ]);
            $mpdf->WriteHTML("<style> body { font-size: 11px; } </style>" . $html);

            $pdfContent = base64_encode($mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN));
            $uploadDir = "files" . DIRECTORY_SEPARATOR . "notasPagoInscripcion" . DIRECTORY_SEPARATOR;
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $pdfPath = $uploadDir . "nota_venta_$id_pago.pdf";
            file_put_contents($pdfPath, base64_decode($pdfContent));

            // Registrar el pago
            $pagoModel = new PagoInscripcion();
            $metodoPago = "Efectivo";
            $efectivoRecibido = "0.00";
            $vuelto = "0.00";

            $idPago = $pagoModel->registrarPago($id_financiamiento, $metodoPago, $monto_inicial, $id_conductor, $idAsesor, $fechaHora, $efectivoRecibido, $vuelto);
        
            if (!$idPago) {
                echo json_encode(['success' => false, 'message' => 'Error al registrar el pago en la base de datos']);
                return;
            }
    
            // Guardar nota de venta
            $pagoModel->guardarNotaVenta($idPago, $id_conductor, $idAsesor, $monto_inicial, $fechaHora, $pdfPath);
    
            header('Content-Type: application/json');

            echo json_encode([
                "success" => true,
                "message" => "Detalles de Pago y actualización de cuotas registrado correctamente",
                "pdf" => $pdfPath,
                "pdf_base64" => $pdfContent
            ]);

            exit;
        
        } else {
            // Obtener datos del conductor
            $conductorModel = new Conductor();
            $conductorData = $conductorModel->obtenerDetalleConductor($id_conductor);
            if (!$conductorData) {
                echo json_encode(['success' => false, 'message' => 'No se encontraron datos del conductor']);
                return;
            }

            $fechaHora = date("Y-m-d H:i:s");
            $idAsesor = $_SESSION['usuario_id'];
            $usuarioModel = new Usuario();
            $asesorData = $usuarioModel->getData($idAsesor);
            $nombreAsesor = $asesorData['nombres'] . ' ' . $asesorData['apellidos'];

            // Cargar plantilla HTML
            $rutaBase = "app" . DIRECTORY_SEPARATOR . "contratos" . DIRECTORY_SEPARATOR . "nota_venta_inscripcion.html";
            $html = file_get_contents($rutaBase);

            $rutaLogo = 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'logo-ticket.png'; // Definir la ruta del logo
            $html = str_replace('{LOGO}', $rutaLogo, $html); // Reemplazar {LOGO} con la ruta
            
            // Reemplazar etiquetas en el HTML
            $html = str_replace([
                '<span id="fecha"></span>',
                '<span id="nombre_conductor"></span>',
                '<span id="documento"></span>',
                '<span id="nro_documento"></span>',
                '<span id="monto_pagado"></span>',
                '<span id="total_pagar"></span>',
                '<span id="vuelto"></span>',
                '<span id="total_ingresado"></span>',
                '<span id="metodo_pago"></span>',
                '<span id="asesor"></span>',
                '<div id="detalle_cuotas"></div>'
            ], [
                $fechaHora, // Modificado: Fecha actual del sistema
                $conductorData['nombre_completo'], // Modificado: Nombre del conductor
                $conductorData['tipo_doc'], // Modificado: Tipo de documento
                $conductorData['nro_documento'], // Modificado: Número de documento
                number_format($monto_pago, 2), // Modificado: Monto pagado
                number_format($monto_pago, 2), // Modificado: Total a pagar
                "0.00", // Modificado: Vuelto en 0.00
                number_format($monto_pago, 2), // Modificado: Total ingresado
                "Efectivo", // Modificado: Método de pago por defecto
                $nombreAsesor, // Modificado: Nombre del asesor
                "Pago al contado: S/. " . number_format($monto_pago, 2) // Modificado: Detalle de pago
            ], $html);

            // Generar y guardar el PDF
            $mpdf = new \Mpdf\Mpdf([
                'format' => [132, 210],
                'default_font_size' => 9
            ]);
            $mpdf->WriteHTML("<style> body { font-size: 11px; } </style>" . $html);

            $pdfContent = base64_encode($mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN));
            $uploadDir = "files" . DIRECTORY_SEPARATOR . "notasPagoInscripcion" . DIRECTORY_SEPARATOR;
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $pdfPath = $uploadDir . "nota_venta_$id_pago.pdf"; // Modificado: Se guarda con el ID del pago
            file_put_contents($pdfPath, base64_decode($pdfContent));

            // Registrar el pago en la base de datos
            $pagoModel = new PagoInscripcion();
            $metodoPago = "Efectivo"; // Modificado: Método de pago por defecto
            $efectivoRecibido = "0.00"; // Modificado: Se coloca en 0.00
            $vuelto = "0.00"; // Modificado: Se coloca en 0.00

            $idPagoNV = $pagoModel->registrarPago(
                $id_pago, // Modificado: Se usa el ID de pago registrado
                $metodoPago, // Modificado: Método de pago "Efectivo"
                $monto_pago, // Modificado: Monto pagado
                $id_conductor, // Modificado: ID del conductor
                $idAsesor, // Modificado: ID del asesor
                $fechaHora, // Modificado: Fecha del sistema
                $efectivoRecibido, // Modificado: Efectivo recibido
                $vuelto // Modificado: Vuelto
            );

            if (!$idPagoNV) {
                echo json_encode(['success' => false, 'message' => 'Error al registrar el pago en la base de datos']);
                return;
            }

            // Guardar la nota de venta
            $pagoModel->guardarNotaVenta(
                $idPagoNV, // Modificado: Se usa el ID del pago registrado
                $id_conductor, // Modificado: ID del conductor
                $idAsesor, // Modificado: ID del asesor
                $monto_pago, // Modificado: Monto pagado
                $fechaHora, // Modificado: Fecha del sistema
                $pdfPath // Modificado: Ruta del PDF
            );

            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');

            $response = [
                "success" => true,
                "message" => "Detalles de Pago y actualización de cuotas registrado correctamente",
                "pdf" => $pdfPath,
                "pdf_base64" => $pdfContent
            ];


            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); // ✅ Evita caracteres unicode y barras invertidas
            exit; // ✅ Asegura que no haya salida extra
            
        }

       
    }


    private function validarFormatoFecha($fecha) {
        $formato = 'Y-m-d';
        $fecha_obj = DateTime::createFromFormat($formato, $fecha);
        return $fecha_obj && $fecha_obj->format($formato) === $fecha;
    }


}

