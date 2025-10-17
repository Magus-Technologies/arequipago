<?php

require_once "app/models/PagosPendientesInscripcion.php";
require_once "app/models/PagoInscripcion.php";
require_once "app/models/ConductorPagoModel.php";
require_once "app/models/ConductorCuotaModel.php";
require_once "app/models/ConductorRegFinanciamientoModel.php";
require_once "app/models/Conductor.php";
require_once "app/models/Usuario.php";
require_once "app/models/Vehiculo.php";
require_once "app/models/Comision.php";
require_once "utils/lib/mpdf/vendor/autoload.php";

use Mpdf\Mpdf;

class PagosPendientesInscripcionController extends Controller
{
    private $conexion;
    private $model;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->model = new PagosPendientesInscripcion();
    }

    /**
     * Listar pagos pendientes
     */
    public function listarPagosPendientes()
    {
        try {
            header('Content-Type: application/json');
            ob_clean();
            $pagos = $this->model->obtenerPagosPendientes();
            echo json_encode(['success' => true, 'data' => $pagos]);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            ob_clean();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * Listar pagos rechazados
     */
    public function listarPagosRechazados()
    {
        try {
            header('Content-Type: application/json');
            ob_clean();
            $pagos = $this->model->obtenerPagosRechazados();
            echo json_encode(['success' => true, 'data' => $pagos]);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            ob_clean();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * Contar pagos pendientes
     */
    public function contarPagosPendientes()
    {
        try {
            header('Content-Type: application/json');
            ob_clean();
            $total = $this->model->contarPagosPendientes();
            echo json_encode(['success' => true, 'total' => $total]);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            ob_clean();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * Aprobar un pago pendiente
     */
    public function aprobarPago()
    {
        try {
            header('Content-Type: application/json');
            ob_clean();
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID de pago no proporcionado']);
                exit;
            }

            $id = $data['id'];
            $id_usuario_aprobacion = $_SESSION['usuario_id'];

            // Obtener el pago pendiente
            $pagoPendiente = $this->model->obtenerPagoPendientePorId($id);
            
            if (!$pagoPendiente) {
                echo json_encode(['success' => false, 'message' => 'Pago pendiente no encontrado']);
                exit;
            }

            if ($pagoPendiente['estado'] !== 'pendiente') {
                echo json_encode(['success' => false, 'message' => 'El pago no está en estado pendiente']);
                exit;
            }

            // Iniciar transacción
            $this->conexion->begin_transaction();

            try {
                if ($pagoPendiente['tipo_inscripcion'] === 'conductor') {
                    $this->aprobarPagoConductor($pagoPendiente, $id_usuario_aprobacion);
                } else if ($pagoPendiente['tipo_inscripcion'] === 'cliente') {
                    $this->aprobarPagoCliente($pagoPendiente, $id_usuario_aprobacion);
                }

                // Actualizar estado del pago pendiente
                $this->model->actualizarEstado($id, 'aprobado', $id_usuario_aprobacion);

                $this->conexion->commit();
                echo json_encode(['success' => true, 'message' => 'Pago aprobado correctamente']);
                exit;
            } catch (Exception $e) {
                $this->conexion->rollback();
                throw $e;
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Error al aprobar pago: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Aprobar pago de conductor
     */
    private function aprobarPagoConductor($pagoPendiente, $id_usuario_aprobacion)
    {
        $observaciones = json_decode($pagoPendiente['observaciones'], true);
        $id_conductor = $observaciones['id_conductor'];
        $tipo_pago = $observaciones['tipo_pago'];
        $monto_pago = $observaciones['monto_pago'];
        $fecha_actual = date('Y-m-d');

        // 1. Registrar en conductor_pago
        $conductorPagoModel = new ConductorPagoModel();
        $id_tipopago = ($tipo_pago === 'contado') ? 1 : 2;
        $id_pago = $conductorPagoModel->registrarPago($id_conductor, $tipo_pago, $fecha_actual, $monto_pago);

        if (!$id_pago) {
            throw new Exception('Error al registrar el pago del conductor');
        }

        // Registrar comisión
        $vehiculoModel = new Vehiculo();
        $vehiculo = $vehiculoModel->obtenerPlacaPorConductor($id_conductor);
        $tipo_vehiculo = $vehiculo ? $vehiculo['tipo_vehiculo'] : 'auto';
        
        $comisionModel = new Comision();
        $monto_comision = $comisionModel->obtenerMontoComision('inscripcion', $tipo_vehiculo, $id_usuario_aprobacion);
        
        if ($monto_comision > 0) {
            $comisionModel->registrarComision(
                $id_usuario_aprobacion,
                'inscripcion',
                $id_pago,
                $monto_comision,
                $tipo_vehiculo,
                "Comisión por inscripción - Pago " . ($tipo_pago === 'contado' ? 'contado' : 'financiado')
            );
        }

        if ($tipo_pago === 'financiado') {
            // 2. Registrar financiamiento
            $conductorRegFinanciamientoModel = new ConductorRegFinanciamientoModel();
            $id_financiamiento = $conductorRegFinanciamientoModel->registrarFinanciamiento(
                $id_conductor,
                $observaciones['numero_cuotas'],
                $observaciones['frecuencia_pago'],
                $observaciones['fecha_inicio'],
                $observaciones['fecha_fin'],
                $observaciones['monto_cuota'],
                $observaciones['tasa_interes'],
                $observaciones['monto_inicial']
            );

            if (!$id_financiamiento) {
                throw new Exception('Error al registrar el financiamiento');
            }

            // 3. Registrar cuotas
            $conductorCuotaModel = new ConductorCuotaModel();
            $cuotas_json = json_decode($pagoPendiente['cuotas_json'], true);
            
            if ($cuotas_json) {
                foreach ($cuotas_json as $cuota) {
                    $result = $conductorCuotaModel->registrarCuota(
                        $id_financiamiento,
                        $cuota['numero_cuota'],
                        $cuota['fecha_vencimiento'],
                        $cuota['monto'],
                        'pendiente'
                    );
                    if (!$result) {
                        throw new Exception('Error al registrar las cuotas');
                    }
                }
            }

            // 4. Si hay monto inicial, generar nota de venta
            $monto_inicial = $observaciones['monto_inicial'];
            if ($monto_inicial > 0) {
                $this->generarNotaVentaFinanciado($id_conductor, $id_pago, $id_financiamiento, $monto_inicial, $id_usuario_aprobacion);
            }
        } else {
            // Pago al contado - generar nota de venta
            $this->generarNotaVentaContado($id_conductor, $id_pago, $monto_pago, $id_usuario_aprobacion);
        }
    }

    /**
     * Aprobar pago de cliente
     */
    private function aprobarPagoCliente($pagoPendiente, $id_usuario_aprobacion)
    {
        require_once "app/models/Cliente.php";
        
        $observaciones = json_decode($pagoPendiente['observaciones'], true);
        $id_cliente = $observaciones['id_cliente'];
        $monto_pago = $observaciones['monto_pago'];
        $metodo_pago = $observaciones['metodo_pago'] ?? 12;
        $monto_pagado = $observaciones['monto_pagado'] ?? $monto_pago;
        $vuelto = $observaciones['vuelto'] ?? 0;

        // Insertar en cliente_pago
        $sql = "INSERT INTO cliente_pago 
                (cliente_id, monto_total, metodo_pago_id, monto_pagado, vuelto, usuario_id, estado) 
                VALUES (?, ?, ?, ?, ?, ?, '1')";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iddddi", $id_cliente, $monto_pago, $metodo_pago, $monto_pagado, $vuelto, $id_usuario_aprobacion);
        
        if (!$stmt->execute()) {
            throw new Exception('Error al registrar el pago del cliente');
        }

        $id_cliente_pago = $this->conexion->insert_id;

        // Actualizar el ID en pagos_pendientes_inscripcion
        $this->model->actualizarIdClientePago($pagoPendiente['id'], $id_cliente_pago);
        
        // Generar boleta de pago
        $this->generarBoletaPagoCliente($id_cliente_pago, $id_cliente);
    }
    
    /**
     * Generar boleta de pago para cliente
     */
    private function generarBoletaPagoCliente($pagoId, $clienteId)
    {
        try {
            require_once "app/models/Cliente.php";
            $clienteModel = new Cliente();
            
            // Obtener datos del cliente y del pago
            $cliente = $clienteModel->obtenerClientePorId($clienteId);
            $pago = $clienteModel->obtenerPagoPorId($pagoId);
            
            if (!$cliente || !$pago) {
                error_log("No se encontraron datos del cliente o pago");
                return;
            }
            
            // Cargar plantilla HTML
            $rutaBase = "app" . DIRECTORY_SEPARATOR . "contratos" . DIRECTORY_SEPARATOR . "nota_venta_inscrip-cliente.html";
            if (!file_exists($rutaBase)) {
                error_log("No se encontró la plantilla de nota de venta");
                return;
            }
            
            $html = file_get_contents($rutaBase);
            
            // Reemplazar variables en la plantilla
            $nombreCompleto = $cliente['nombres'] . ' ' . $cliente['apellido_paterno'] . ' ' . $cliente['apellido_materno'];
            $fechaActual = date('Y-m-d H:i:s');
            
            $html = str_replace('{NOMBRE_CLIENTE}', $nombreCompleto, $html);
            $html = str_replace('{DOCUMENTO}', $cliente['n_documento'], $html);
            $html = str_replace('{MONTO}', number_format($pago['monto_total'], 2), $html);
            $html = str_replace('{FECHA}', $fechaActual, $html);
            $html = str_replace('{METODO_PAGO}', $pago['metodo_pago'], $html);
            
            // Generar PDF
            $mpdf = new Mpdf([
                'format' => [132, 210],
                'default_font_size' => 9
            ]);
            $mpdf->WriteHTML($html);
            
            // Guardar PDF
            $uploadDir = "files" . DIRECTORY_SEPARATOR . "notasPagoInscripcion" . DIRECTORY_SEPARATOR;
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $pdfPath = $uploadDir . "nota_venta_cliente_$pagoId.pdf";
            $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);
            
        } catch (Exception $e) {
            error_log("Error al generar boleta de pago: " . $e->getMessage());
        }
    }

    /**
     * Generar nota de venta para pago financiado
     */
    private function generarNotaVentaFinanciado($id_conductor, $id_pago, $id_financiamiento, $monto_inicial, $id_usuario)
    {
        $fechaHora = date("Y-m-d H:i:s");

        // Obtener datos del conductor
        $conductorModel = new Conductor();
        $conductorData = $conductorModel->obtenerDetalleConductor($id_conductor);
        if (!$conductorData) {
            throw new Exception('No se encontraron datos del conductor');
        }

        $usuarioModel = new Usuario();
        $asesorData = $usuarioModel->getData($id_usuario);
        $nombreAsesor = $asesorData['nombres'] . ' ' . $asesorData['apellidos'];

        // Cargar plantilla HTML
        $rutaBase = "app" . DIRECTORY_SEPARATOR . "contratos" . DIRECTORY_SEPARATOR . "nota_venta_inscripcion.html";
        $html = file_get_contents($rutaBase);

        $rutaLogo = 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'logo-ticket.png';
        $html = str_replace('{LOGO}', $rutaLogo, $html);

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
        $mpdf = new Mpdf([
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

        // Registrar el pago en pagos_inscripcion
        $pagoModel = new PagoInscripcion();
        $metodoPago = "Efectivo";
        $efectivoRecibido = "0.00";
        $vuelto = "0.00";

        $idPago = $pagoModel->registrarPago($id_financiamiento, $metodoPago, $monto_inicial, $id_conductor, $id_usuario, $fechaHora, $efectivoRecibido, $vuelto);

        if (!$idPago) {
            throw new Exception('Error al registrar el pago en pagos_inscripcion');
        }

        // Guardar nota de venta
        $pagoModel->guardarNotaVenta($idPago, $id_conductor, $id_usuario, $monto_inicial, $fechaHora, $pdfPath);
    }

    /**
     * Generar nota de venta para pago al contado
     */
    private function generarNotaVentaContado($id_conductor, $id_pago, $monto_pago, $id_usuario)
    {
        $fechaHora = date("Y-m-d H:i:s");

        // Obtener datos del conductor
        $conductorModel = new Conductor();
        $conductorData = $conductorModel->obtenerDetalleConductor($id_conductor);
        if (!$conductorData) {
            throw new Exception('No se encontraron datos del conductor');
        }

        $usuarioModel = new Usuario();
        $asesorData = $usuarioModel->getData($id_usuario);
        $nombreAsesor = $asesorData['nombres'] . ' ' . $asesorData['apellidos'];

        // Cargar plantilla HTML
        $rutaBase = "app" . DIRECTORY_SEPARATOR . "contratos" . DIRECTORY_SEPARATOR . "nota_venta_inscripcion.html";
        $html = file_get_contents($rutaBase);

        $rutaLogo = 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'logo-ticket.png';
        $html = str_replace('{LOGO}', $rutaLogo, $html);

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
            number_format($monto_pago, 2),
            number_format($monto_pago, 2),
            "0.00",
            number_format($monto_pago, 2),
            "Efectivo",
            $nombreAsesor,
            "Pago al contado: S/. " . number_format($monto_pago, 2)
        ], $html);

        // Generar y guardar el PDF
        $mpdf = new Mpdf([
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

        // Registrar el pago en pagos_inscripcion
        $pagoModel = new PagoInscripcion();
        $metodoPago = "Efectivo";
        $efectivoRecibido = "0.00";
        $vuelto = "0.00";

        $idPagoNV = $pagoModel->registrarPago(
            $id_pago,
            $metodoPago,
            $monto_pago,
            $id_conductor,
            $id_usuario,
            $fechaHora,
            $efectivoRecibido,
            $vuelto
        );

        if (!$idPagoNV) {
            throw new Exception('Error al registrar el pago en pagos_inscripcion');
        }

        // Guardar la nota de venta
        $pagoModel->guardarNotaVenta(
            $idPagoNV,
            $id_conductor,
            $id_usuario,
            $monto_pago,
            $fechaHora,
            $pdfPath
        );
    }

    /**
     * Rechazar un pago pendiente
     */
    public function rechazarPago()
    {
        try {
            header('Content-Type: application/json');
            ob_clean();
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID de pago no proporcionado']);
                exit;
            }

            $id = $data['id'];
            $observaciones = $data['observaciones'] ?? 'Pago rechazado';
            $id_usuario_aprobacion = $_SESSION['usuario_id'];

            // Obtener el pago pendiente
            $pagoPendiente = $this->model->obtenerPagoPendientePorId($id);
            
            if (!$pagoPendiente) {
                echo json_encode(['success' => false, 'message' => 'Pago pendiente no encontrado']);
                exit;
            }

            if ($pagoPendiente['estado'] !== 'pendiente') {
                echo json_encode(['success' => false, 'message' => 'El pago no está en estado pendiente']);
                exit;
            }

            // Actualizar estado a rechazado con motivo
            if ($this->model->actualizarEstado($id, 'rechazado', $id_usuario_aprobacion, $observaciones)) {
                echo json_encode(['success' => true, 'message' => 'Pago rechazado correctamente']);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al rechazar el pago']);
                exit;
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Error al rechazar pago: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Reactivar un pago rechazado
     */
    public function reactivarPago()
    {
        try {
            header('Content-Type: application/json');
            ob_clean();
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID de pago no proporcionado']);
                exit;
            }

            $id = $data['id'];
            $id_usuario_aprobacion = $_SESSION['usuario_id'];

            // Obtener el pago pendiente
            $pagoPendiente = $this->model->obtenerPagoPendientePorId($id);
            
            if (!$pagoPendiente) {
                echo json_encode(['success' => false, 'message' => 'Pago no encontrado']);
                exit;
            }

            if ($pagoPendiente['estado'] !== 'rechazado') {
                echo json_encode(['success' => false, 'message' => 'El pago no está rechazado']);
                exit;
            }

            // Actualizar estado a pendiente (sin motivo de rechazo)
            if ($this->model->actualizarEstado($id, 'pendiente', $id_usuario_aprobacion, null)) {
                echo json_encode(['success' => true, 'message' => 'Pago reactivado correctamente']);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al reactivar el pago']);
                exit;
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Error al reactivar pago: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Eliminar un pago pendiente
     */
    public function eliminarPago()
    {
        try {
            header('Content-Type: application/json');
            ob_clean();
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID de pago no proporcionado']);
                exit;
            }

            $id = $data['id'];

            // Obtener el pago pendiente
            $pagoPendiente = $this->model->obtenerPagoPendientePorId($id);
            
            if (!$pagoPendiente) {
                echo json_encode(['success' => false, 'message' => 'Pago no encontrado']);
                exit;
            }

            // Eliminar el pago
            if ($this->model->eliminarPagoPendiente($id)) {
                echo json_encode(['success' => true, 'message' => 'Pago eliminado correctamente']);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar el pago']);
                exit;
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Error al eliminar pago: ' . $e->getMessage()]);
            exit;
        }
    }
}
