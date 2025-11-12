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
            // $this->conexion->begin_transaction();

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
            // Convertir fechas a formato MySQL (YYYY-MM-DD)
            $fecha_inicio = $this->convertirFechaAMySQL($observaciones['fecha_inicio']);
            $fecha_fin = $this->convertirFechaAMySQL($observaciones['fecha_fin']);

            // 2. Registrar financiamiento
            $conductorRegFinanciamientoModel = new ConductorRegFinanciamientoModel();
            $id_financiamiento = $conductorRegFinanciamientoModel->registrarFinanciamiento(
                $id_conductor,
                $observaciones['numero_cuotas'],
                $observaciones['frecuencia_pago'],
                $fecha_inicio,
                $fecha_fin,
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
                    // Convertir fecha de vencimiento a formato MySQL
                    $fecha_vencimiento = $this->convertirFechaAMySQL($cuota['fecha_vencimiento']);

                    $result = $conductorCuotaModel->registrarCuota(
                        $id_financiamiento,
                        $cuota['numero_cuota'],
                        $fecha_vencimiento,
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

        // 🔥 DEBUG: Ver todos los datos del pago pendiente
        error_log("=== APROBAR PAGO CLIENTE DEBUG ===");
        error_log("Pago Pendiente completo: " . json_encode($pagoPendiente));
        error_log("ID Usuario Aprobacion (quien aprueba): " . $id_usuario_aprobacion);

        $observaciones = json_decode($pagoPendiente['observaciones'], true);
        $id_cliente = $observaciones['id_cliente'];
        $monto_pago = $observaciones['monto_pago'];
        $metodo_pago = $observaciones['metodo_pago'] ?? 12;
        $monto_pagado = $observaciones['monto_pagado'] ?? $monto_pago;
        $vuelto = $observaciones['vuelto'] ?? 0;

        // 🔥 CORREGIDO: El campo se llama 'id_usuario_registro', NO 'id_usuario'
        $id_usuario_original = $pagoPendiente['id_usuario_registro'] ?? null; // El asesor que registró el pago

        // 🔥 DEBUG: Ver qué asesor se va a guardar
        error_log("ID Usuario Original (asesor que registro): " . ($id_usuario_original ?? 'NULL'));

        // 🔥 VALIDACIÓN: Si no hay usuario original, usar el que aprueba
        if (empty($id_usuario_original)) {
            error_log("⚠️ ADVERTENCIA: No hay id_usuario_registro en pagoPendiente, usando id_usuario_aprobacion");
            $id_usuario_original = $id_usuario_aprobacion;
        }

        // Insertar en cliente_pago
        $sql = "INSERT INTO cliente_pago
                (cliente_id, monto_total, metodo_pago_id, monto_pagado, vuelto, usuario_id, estado)
                VALUES (?, ?, ?, ?, ?, ?, '1')";

        $stmt = $this->conexion->prepare($sql);
        // 🔥 CORREGIDO: Usar $id_usuario_original en lugar de $id_usuario_aprobacion
        $stmt->bind_param("iddddi", $id_cliente, $monto_pago, $metodo_pago, $monto_pagado, $vuelto, $id_usuario_original);

        // 🔥 DEBUG: Ver los datos que se van a insertar
        error_log("Insertando en cliente_pago - Cliente: $id_cliente, Monto: $monto_pago, Usuario: $id_usuario_original");

        if (!$stmt->execute()) {
            error_log("❌ ERROR al insertar en cliente_pago: " . $stmt->error);
            throw new Exception('Error al registrar el pago del cliente');
        }

        $id_cliente_pago = $this->conexion->insert_id;
        error_log("✅ Pago insertado correctamente con ID: $id_cliente_pago");

        // 🔥 DEBUG: Verificar qué se guardó en la BD
        $sqlVerificar = "SELECT * FROM cliente_pago WHERE id = $id_cliente_pago";
        $resultVerificar = $this->conexion->query($sqlVerificar);
        if ($rowVerificar = $resultVerificar->fetch_assoc()) {
            error_log("📋 Verificación BD - Registro guardado: " . json_encode($rowVerificar));
        }

        // Actualizar el ID en pagos_pendientes_inscripcion
        $this->model->actualizarIdClientePago($pagoPendiente['id'], $id_cliente_pago);

        // Generar boleta de pago con el ID del asesor original
        $this->generarBoletaPagoCliente($id_cliente_pago, $id_cliente, $id_usuario_original);

        error_log("=== FIN APROBAR PAGO CLIENTE ===");
    }
    
    /**
     * Generar boleta de pago para cliente
     */
    private function generarBoletaPagoCliente($pagoId, $clienteId, $id_usuario_original = null)
    {
        try {
            require_once "app/models/Cliente.php";
            $clienteModel = new Cliente();

            // Obtener datos del cliente y del pago
            $cliente = $clienteModel->obtenerClientePorId($clienteId);
            $pago = $clienteModel->obtenerPagoPorId($pagoId);

            if (!$cliente || !$pago) {
                error_log("No se encontraron datos del cliente o pago. PagoId: $pagoId, ClienteId: $clienteId");
                return;
            }

            // 🔥 CORREGIDO: Usar el ID del asesor original si se proporciona, sino usar el del pago
            $usuarioModel = new Usuario();
            $idUsuarioAsesor = $id_usuario_original ?? $pago['usuario_id'];
            $usuario = $usuarioModel->getData($idUsuarioAsesor);
            $nombreAsesor = $usuario ? ($usuario['nombres'] . ' ' . $usuario['apellidos']) : 'N/A';

            error_log("Generando PDF - Pago: $pagoId, Cliente: $clienteId, Asesor: $idUsuarioAsesor ($nombreAsesor)");

            // Cargar plantilla HTML
            $rutaBase = "app" . DIRECTORY_SEPARATOR . "contratos" . DIRECTORY_SEPARATOR . "nota_venta_inscrip-cliente.html";
            if (!file_exists($rutaBase)) {
                error_log("No se encontró la plantilla de nota de venta");
                return;
            }

            $html = file_get_contents($rutaBase);

            // Preparar datos
            $nombreCompleto = trim($cliente['nombres'] . ' ' . $cliente['apellido_paterno'] . ' ' . $cliente['apellido_materno']);
            $fechaActual = date('d/m/Y H:i:s');
            $tipoDocumento = !empty($cliente['tipo_doc']) ? strtoupper($cliente['tipo_doc']) : 'DNI';
            $nroDocumento = $cliente['n_documento'];
            $metodoPago = !empty($pago['metodo_pago']) ? strtoupper($pago['metodo_pago']) : 'EFECTIVO';

            // Formatear montos
            $montoPagado = number_format($pago['monto_pagado'], 2);
            $totalPagar = number_format($pago['monto_total'], 2);
            $vuelto = number_format($pago['vuelto'], 2);
            $totalIngresado = number_format($pago['monto_total'], 2);

            // Logo
            $rutaLogo = 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'logo-ticket.png';

            // Reemplazar TODAS las variables en la plantilla
            $html = str_replace('{LOGO}', $rutaLogo, $html);
            $html = str_replace('{FECHA}', $fechaActual, $html);
            $html = str_replace('{NOMBRE_CLIENTE}', $nombreCompleto, $html);
            $html = str_replace('{TIPO_DOCUMENTO}', $tipoDocumento, $html);
            $html = str_replace('{NRO_DOCUMENTO}', $nroDocumento, $html);
            $html = str_replace('{METODO_PAGO}', $metodoPago, $html);
            $html = str_replace('{MONTO_PAGADO}', $montoPagado, $html);
            $html = str_replace('{TOTAL_PAGAR}', $totalPagar, $html);
            $html = str_replace('{VUELTO}', $vuelto, $html);
            $html = str_replace('{TOTAL_INGRESADO}', $totalIngresado, $html);
            $html = str_replace('{ASESOR}', $nombreAsesor, $html);

            // Guardar PDF
            $uploadDir = "files" . DIRECTORY_SEPARATOR . "notasPagoInscripcion" . DIRECTORY_SEPARATOR;
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $pdfPath = $uploadDir . "nota_venta_cliente_$pagoId.pdf";

            // Eliminar PDF antiguo si existe
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
                error_log("PDF antiguo eliminado: $pdfPath");
            }

            // Generar PDF
            $mpdf = new Mpdf([
                'format' => [132, 210],
                'default_font_size' => 9
            ]);
            $mpdf->WriteHTML($html);
            $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);

            error_log("PDF generado correctamente: $pdfPath");

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

    /**
     * Ver comprobante de pago de cliente
     * Si el PDF no existe, lo regenera automáticamente desde la BD
     */
    public function verComprobante($pagoId, $request = null)
    {
        try {
            // El router pasa el ID directamente como primer parámetro
            if (!$pagoId) {
                http_response_code(404);
                echo "ID de pago no proporcionado";
                exit;
            }

            $pdfPath = "files" . DIRECTORY_SEPARATOR . "notasPagoInscripcion" . DIRECTORY_SEPARATOR . "nota_venta_cliente_$pagoId.pdf";

            // Si el PDF no existe, intentar regenerarlo
            if (!file_exists($pdfPath)) {
                error_log("PDF no encontrado, intentando regenerar: $pdfPath");

                // Buscar el pago en cliente_pago
                require_once "app/models/Cliente.php";
                $clienteModel = new Cliente();
                $pago = $clienteModel->obtenerPagoPorId($pagoId);

                if ($pago && isset($pago['cliente_id'])) {
                    error_log("Regenerando PDF para pago ID: $pagoId, cliente ID: {$pago['cliente_id']}, usuario ID: {$pago['usuario_id']}");
                    // 🔥 CORREGIDO: Pasar también el usuario_id del pago para usar el asesor correcto
                    $this->generarBoletaPagoCliente($pagoId, $pago['cliente_id'], $pago['usuario_id']);
                } else {
                    // Si no se encuentra en cliente_pago, buscar en pagos_pendientes_inscripcion
                    $pagoPendiente = $this->model->obtenerPagoPendientePorId($pagoId);

                    if ($pagoPendiente && $pagoPendiente['id_cliente_pago']) {
                        // Si tiene id_cliente_pago, buscar con ese ID
                        $pago = $clienteModel->obtenerPagoPorId($pagoPendiente['id_cliente_pago']);
                        if ($pago && isset($pago['cliente_id'])) {
                            error_log("Regenerando PDF desde pago pendiente: {$pagoPendiente['id_cliente_pago']}, usuario ID: {$pago['usuario_id']}");
                            // 🔥 CORREGIDO: Pasar también el usuario_id del pago
                            $this->generarBoletaPagoCliente($pagoPendiente['id_cliente_pago'], $pago['cliente_id'], $pago['usuario_id']);
                            // Actualizar la ruta del PDF porque usamos el id_cliente_pago
                            $pdfPath = "files" . DIRECTORY_SEPARATOR . "notasPagoInscripcion" . DIRECTORY_SEPARATOR . "nota_venta_cliente_{$pagoPendiente['id_cliente_pago']}.pdf";
                        }
                    }
                }

                // Verificar nuevamente si el PDF se generó
                if (!file_exists($pdfPath)) {
                    error_log("No se pudo regenerar el PDF: $pdfPath");
                    http_response_code(404);
                    echo "No se pudo encontrar o generar el comprobante de pago.";
                    exit;
                }
            }

            // Enviar el PDF al navegador
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="comprobante_pago_' . $pagoId . '.pdf"');
            header('Content-Length: ' . filesize($pdfPath));
            readfile($pdfPath);
            exit;

        } catch (Exception $e) {
            error_log("Error en verComprobante: " . $e->getMessage());
            http_response_code(500);
            echo "Error al procesar el comprobante: " . $e->getMessage();
            exit;
        }
    }

    /**
     * Convertir fecha a formato MySQL (YYYY-MM-DD)
     * Acepta múltiples formatos: DD/MM/YYYY, DD-MM-YYYY, YYYY-MM-DD, etc.
     */
    private function convertirFechaAMySQL($fecha)
    {
        if (empty($fecha)) {
            return null;
        }

        // Detectar y corregir formato incorrecto YYYY-DD-MM (día y mes invertidos)
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $fecha, $matches)) {
            $year = (int)$matches[1];
            $valor1 = (int)$matches[2];
            $valor2 = (int)$matches[3];

            // Si el segundo valor es > 12, probablemente es el día (formato incorrecto YYYY-DD-MM)
            if ($valor1 > 12 && $valor2 <= 12) {
                // Está en formato YYYY-DD-MM, corregir a YYYY-MM-DD
                $fechaCorregida = sprintf('%04d-%02d-%02d', $year, $valor2, $valor1);
                error_log("🔧 Fecha corregida de '$fecha' a '$fechaCorregida'");
                return $fechaCorregida;
            }

            // Si ambos valores son <= 12, asumir que ya está correcto
            if ($valor1 <= 12 && $valor2 <= 12) {
                return $fecha;
            }

            // Si el primer valor es > 12, está mal formateado
            if ($valor1 > 12) {
                error_log("❌ ERROR: Fecha '$fecha' tiene mes inválido ($valor1)");
                return null;
            }
        }

        // Intentar parsear diferentes formatos
        $formatos = [
            'd/m/Y',    // 24/11/2025
            'd-m-Y',    // 24-11-2025
            'Y/m/d',    // 2025/11/24
            'd/m/y',    // 24/11/25
            'd-m-y',    // 24-11-25
        ];

        foreach ($formatos as $formato) {
            $fechaObj = DateTime::createFromFormat($formato, $fecha);
            if ($fechaObj !== false) {
                return $fechaObj->format('Y-m-d');
            }
        }

        // Si no se pudo convertir, registrar error y retornar null
        error_log("⚠️ ADVERTENCIA: No se pudo convertir la fecha '$fecha' a formato MySQL");
        return null;
    }
}
