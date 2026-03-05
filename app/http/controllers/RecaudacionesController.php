<?php

require_once "app/models/PagoCajaArequipa.php";
require_once 'app/models/Cliente.php';
require_once 'app/models/Venta.php';
require_once 'app/models/DocumentoEmpresa.php';
require_once 'app/models/VentaServicio.php';
require_once 'app/models/Varios.php';
require_once 'app/models/VentaSunat.php';
require_once 'app/clases/SunatApi.php';

class RecaudacionesController extends Controller
{
    private $pagoCajaArequipaModel;
    private $sunatApi;

    public function __construct()
    {
        $this->pagoCajaArequipaModel = new PagoCajaArequipa();
        $this->sunatApi = new SunatApi();
    }

    /**
     * Obtener todas las recaudaciones con filtros
     */
    public function listarRecaudaciones()
    {
        try {
            $filtros = [];

            // Obtener filtros del request
            if (isset($_POST['fecha_inicio']) && !empty($_POST['fecha_inicio'])) {
                $filtros['fecha_inicio'] = $_POST['fecha_inicio'];
            }

            if (isset($_POST['fecha_fin']) && !empty($_POST['fecha_fin'])) {
                $filtros['fecha_fin'] = $_POST['fecha_fin'];
            }

            if (isset($_POST['estado']) && !empty($_POST['estado'])) {
                $filtros['estado'] = $_POST['estado'];
            }

            if (isset($_POST['moneda']) && !empty($_POST['moneda'])) {
                $filtros['moneda'] = $_POST['moneda'];
            }

            if (isset($_POST['busqueda']) && !empty($_POST['busqueda'])) {
                $filtros['busqueda'] = $_POST['busqueda'];
            }

            $recaudaciones = $this->pagoCajaArequipaModel->obtenerRecaudaciones($filtros);

            echo json_encode([
                'success' => true,
                'data' => $recaudaciones,
                'total' => count($recaudaciones)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener recaudaciones: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener resumen de recaudaciones
     */
    public function obtenerResumen()
    {
        try {
            $filtros = [];

            if (isset($_POST['fecha_inicio']) && !empty($_POST['fecha_inicio'])) {
                $filtros['fecha_inicio'] = $_POST['fecha_inicio'];
            }

            if (isset($_POST['fecha_fin']) && !empty($_POST['fecha_fin'])) {
                $filtros['fecha_fin'] = $_POST['fecha_fin'];
            }

            if (isset($_POST['estado']) && !empty($_POST['estado'])) {
                $filtros['estado'] = $_POST['estado'];
            }

            $resumen = $this->pagoCajaArequipaModel->obtenerResumenRecaudaciones($filtros);

            echo json_encode([
                'success' => true,
                'data' => $resumen
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener resumen: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener detalle de una recaudación específica
     */
    public function obtenerDetalle()
    {
        try {
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de recaudación requerido'
                ]);
                return;
            }

            $id = $_POST['id'];
            $detalle = $this->pagoCajaArequipaModel->obtenerDetallePorId($id);

            if ($detalle) {
                echo json_encode([
                    'success' => true,
                    'data' => $detalle
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Recaudación no encontrada'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener detalle: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Exportar recaudaciones a Excel
     */
    public function exportarExcel()
    {
        try {
            $filtros = [];

            if (isset($_POST['fecha_inicio']) && !empty($_POST['fecha_inicio'])) {
                $filtros['fecha_inicio'] = $_POST['fecha_inicio'];
            }

            if (isset($_POST['fecha_fin']) && !empty($_POST['fecha_fin'])) {
                $filtros['fecha_fin'] = $_POST['fecha_fin'];
            }

            if (isset($_POST['estado']) && !empty($_POST['estado'])) {
                $filtros['estado'] = $_POST['estado'];
            }

            if (isset($_POST['moneda']) && !empty($_POST['moneda'])) {
                $filtros['moneda'] = $_POST['moneda'];
            }

            $recaudaciones = $this->pagoCajaArequipaModel->obtenerRecaudaciones($filtros);

            // Configurar headers para descarga de Excel
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="recaudaciones_caja_arequipa_' . date('Y-m-d') . '.xls"');
            header('Cache-Control: max-age=0');

            // Crear contenido HTML para Excel
            echo '<table border="1">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Fecha Pago</th>';
            echo '<th>N° Trace</th>';
            echo '<th>Cliente</th>';
            echo '<th>DNI</th>';
            echo '<th>Código Asociado</th>';
            echo '<th>Plan</th>';
            echo '<th>Moneda</th>';
            echo '<th>Monto</th>';
            echo '<th>Comisión</th>';
            echo '<th>Monto Recibido</th>';
            echo '<th>Estado</th>';
            echo '<th>Canal</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($recaudaciones as $rec) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($rec['fecha_pago']) . '</td>';
                echo '<td>' . htmlspecialchars($rec['numero_trace']) . '</td>';
                echo '<td>' . htmlspecialchars($rec['nombre_cliente']) . '</td>';
                echo '<td>' . htmlspecialchars($rec['dni_cliente']) . '</td>';
                echo '<td>' . htmlspecialchars($rec['codigo_asociado']) . '</td>';
                echo '<td>' . htmlspecialchars($rec['nombre_plan']) . '</td>';
                echo '<td>' . htmlspecialchars($rec['moneda']) . '</td>';
                echo '<td>' . number_format($rec['monto'], 2) . '</td>';
                echo '<td>' . number_format($rec['comision_asumida'], 2) . '</td>';
                echo '<td>' . number_format($rec['monto_recibido'], 2) . '</td>';
                echo '<td>' . htmlspecialchars($rec['estado']) . '</td>';
                echo '<td>' . $this->getNombreCanal($rec['canal']) . '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';

            exit;
        } catch (Exception $e) {
            echo "Error al exportar: " . $e->getMessage();
        }
    }

    /**
     * Obtener nombre descriptivo del canal
     */
    private function getNombreCanal($canal)
    {
        $canales = [
            '1' => 'Ventanilla',
            '2' => 'Cajeros',
            '3' => 'Home Banking',
            '4' => 'Corresponsal',
            '5' => 'Débito Automático',
            '6' => 'Banca Móvil'
        ];

        return isset($canales[$canal]) ? $canales[$canal] : 'Desconocido';
    }

    /**
     * Obtener serie y número siguiente para un tipo de documento
     */
    public function obtenerSerieNumero()
    {
        try {
            if (!isset($_POST['tipo_doc']) || empty($_POST['tipo_doc'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Tipo de documento requerido'
                ]);
                return;
            }

            require_once 'app/models/DocumentoEmpresa.php';
            
            $c_tido = new DocumentoEmpresa();
            $id_empresa = $_SESSION['id_empresa'];
            $tipo_doc = $_POST['tipo_doc'];

            $c_tido->setIdEmpresa($id_empresa);
            $c_tido->setIdTido($tipo_doc);
            $c_tido->obtenerDatos();

            echo json_encode([
                'success' => true,
                'serie' => $c_tido->getSerie(),
                'numero' => $c_tido->getNumero()
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener serie/número: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generar factura/boleta para un pago de Caja Arequipa
     */
    public function generarFacturaPago()
    {
        try {
            // Validar datos requeridos
            if (!isset($_POST['id_pago']) || empty($_POST['id_pago'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de pago requerido'
                ]);
                return;
            }

            $id_pago = $_POST['id_pago'];
            
            // Verificar que el pago no esté ya facturado
            if ($this->pagoCajaArequipaModel->estaFacturado($id_pago)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Este pago ya ha sido facturado'
                ]);
                return;
            }

            // Obtener detalle completo del pago
            $detalle = $this->pagoCajaArequipaModel->obtenerDetallePorId($id_pago);
            
            if (!$detalle) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Pago no encontrado'
                ]);
                return;
            }

            // Validar que el pago esté en estado PAGADO
            if (strtoupper($detalle['estado']) !== 'PAGADO') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Solo se pueden facturar pagos en estado PAGADO'
                ]);
                return;
            }

            // Validar fecha de emisión (hasta 5 días atrás)
            $fecha_emision = $_POST['fecha_emision'] ?? $detalle['fecha_pago'];
            $fecha_minima = date('Y-m-d', strtotime('-5 days'));
            $fecha_actual = date('Y-m-d');

            if ($fecha_emision < $fecha_minima) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Solo puede emitir comprobantes con fecha de hasta 5 días atrás'
                ]);
                return;
            }

            if ($fecha_emision > $fecha_actual) {
                echo json_encode([
                    'success' => false,
                    'message' => 'No puede emitir comprobantes con fecha futura'
                ]);
                return;
            }

            // Preparar datos para la venta (los require_once están al inicio del archivo)
            $c_cliente = new Cliente();
            $c_venta = new Venta();
            $c_tido = new DocumentoEmpresa();
            $c_servicio = new VentaServicio();
            $c_varios = new Varios();

            $id_empresa = $_SESSION['id_empresa'];
            $tipo_doc = $_POST['tipo_doc'] ?? '1'; // 1=Boleta por defecto
            $descripcion = $_POST['descripcion'] ?? "Pago Cuota N° {$detalle['numero_cuota']} - {$detalle['nombre_plan']} - Código: {$detalle['codigo_asociado']}";

            // Convertir moneda de texto a número (PEN=1, USD=2)
            $moneda_numero = ($detalle['moneda'] === 'PEN') ? 1 : 2;

            // Configurar cliente
            $c_cliente->setIdEmpresa($id_empresa);
            $c_cliente->setDocumento($detalle['dni_cliente']);
            $c_cliente->setDatos($detalle['nombre_cliente']);
            $c_cliente->setDireccion('-');
            $c_cliente->setDireccion2('-');

            // Procesar cliente
            if (!$c_cliente->verificarDocumento()) {
                $nuevo_id = $c_cliente->insertarCliente(
                    $c_cliente->getDocumento(),
                    $c_cliente->getDatos(),
                    '',
                    $detalle['celular_cliente'] ?? '',
                    '-',
                    $id_empresa
                );

                if ($nuevo_id) {
                    $c_cliente->setIdCliente($nuevo_id);
                } else {
                    throw new Exception('Error al procesar cliente');
                }
            }

            // Configurar documento
            $c_tido->setIdEmpresa($id_empresa);
            $c_tido->setIdTido($tipo_doc);
            $c_tido->obtenerDatos();

            // Obtener datos completos de la empresa
            $sql = "SELECT * FROM empresas WHERE id_empresa = " . $id_empresa;
            $respEmpre = $c_venta->exeSQL($sql)->fetch_assoc();
            
            if (!$respEmpre) {
                throw new Exception('No se encontraron datos de la empresa');
            }
            
            $igv_empresa = $respEmpre['igv'];

            // Calcular montos con redondeo a 2 decimales
            $monto_servicio = round(floatval($detalle['monto_recibido']), 2);
            $aplicar_igv = 1; // Aplicar IGV por defecto
            
            if ($aplicar_igv == 1) {
                // El monto incluye IGV, calcular base imponible (IGV 18% = 1.18)
                $base_imponible = round($monto_servicio / 1.18, 2);
                $igv_monto = round($monto_servicio - $base_imponible, 2);
            } else {
                $base_imponible = $monto_servicio;
                $igv_monto = 0;
            }

            // Configurar venta
            $c_venta->setDireccion('-');
            $c_venta->setApliIgv($aplicar_igv);
            $c_venta->setIdEmpresa($id_empresa);
            $c_venta->setFecha($fecha_emision);
            $c_venta->setFechaVenc($fecha_emision);
            $c_venta->setDiasPagos(0);
            $c_venta->setIdTipoPago(1); // Contado
            $c_venta->setMetodo(1); // Método de pago por defecto
            $c_venta->setObserva("Pago Caja Arequipa - Trace: {$detalle['numero_trace']}");
            $c_venta->setIdTido($c_tido->getIdTido());
            $c_venta->setSerie($c_tido->getSerie());
            
            // GUARDAR el número ORIGINAL antes de cualquier incremento
            $numero_original = $c_tido->getNumero();
            $c_venta->setNumero($numero_original);
            
            $c_venta->setIdCliente($c_cliente->getIdCliente());
            $c_venta->setIgv($igv_empresa);
            $c_venta->setTotal($monto_servicio);
            $c_venta->setIdCoti(null);

            // Configurar $_POST para el método insertar() de Venta
            $_POST['moneda'] = $moneda_numero;
            $_POST['tc'] = '1.00'; // Tipo de cambio por defecto
            $_POST['pagacon'] = $monto_servicio;

            // Iniciar transacción
            $conexion = (new Conexion())->getConexion();
            $conexion->begin_transaction();

            try {
                // Insertar venta
                if (!$c_venta->insertar()) {
                    throw new Exception('Error al insertar la venta');
                }

                $id_venta = $c_venta->getIdVenta();

                // Insertar detalle del servicio
                $c_servicio->setIdVenta($id_venta);
                $c_servicio->setIdItem(1);
                $c_servicio->setDescripcion($descripcion);
                $c_servicio->setMonto($base_imponible);
                $c_servicio->setCantidad(1);
                $c_servicio->setCodSunat(''); // Código SUNAT de servicio

                if (!$c_servicio->insertar()) {
                    throw new Exception('Error al insertar detalle del servicio');
                }

                // ============================================
                // GENERAR XML PARA SUNAT (solo para Boletas y Facturas)
                // ============================================
                if ($tipo_doc == '1' || $tipo_doc == '2') {
                    // Preparar datos para generar XML
                    $dataSend = [];
                    $dataSend['certGlobal'] = false;

                    // Datos del cliente
                    $direccion_cliente = $c_cliente->getDireccion() ?: '-';
                    if (strlen(trim($direccion_cliente)) == 0) {
                        $direccion_cliente = '-';
                    }

                    $nombre_cliente = $c_cliente->getDatos() ?: '-';
                    if (trim($nombre_cliente) == '') {
                        $nombre_cliente = '-';
                    }

                    $dataSend['cliente'] = json_encode([
                        'doc_num' => $c_cliente->getDocumento(),
                        'nom_RS' => $nombre_cliente,
                        'direccion' => $direccion_cliente
                    ]);

                    // Datos de la venta - USAR EL NÚMERO ACTUAL (antes de incrementar)
                    $dataSend['apli_igv'] = ($aplicar_igv == 1);
                    $dataSend['total'] = number_format($monto_servicio, 2, '.', '');
                    $dataSend['serie'] = $c_tido->getSerie();
                    $dataSend['numero'] = $c_tido->getNumero(); // NÚMERO ACTUAL (118)
                    $dataSend['fechaE'] = $fecha_emision;
                    $dataSend['fechaV'] = $fecha_emision;
                    $dataSend['tipo_pago'] = 1; // Contado
                    $dataSend['igv_venta'] = $igv_empresa;
                    $dataSend['moneda'] = ($moneda_numero == 1) ? 'PEN' : 'USD';
                    $dataSend['dias_pagos'] = json_encode([]); // Sin cuotas

                    // Datos del servicio - USAR BASE IMPONIBLE (sin IGV)
                    $dataSend['productos'] = json_encode([[
                        'precio' => number_format($base_imponible, 2, '.', ''),  // Precio SIN IGV
                        'cantidad' => 1,
                        'cod_pro' => 1,
                        'cod_sunat' => '',
                        'descripcion' => $descripcion
                    ]]);

                    // Datos de la empresa
                    $dataSend['endpoints'] = $respEmpre['modo'];
                    $dataSend['empresa'] = json_encode([
                        'ruc' => $respEmpre['ruc'],
                        'razon_social' => $respEmpre['razon_social'],
                        'direccion' => $respEmpre['direccion'],
                        'ubigeo' => $respEmpre['ubigeo'],
                        'distrito' => $respEmpre['distrito'],
                        'provincia' => $respEmpre['provincia'],
                        'departamento' => $respEmpre['departamento'],
                        'clave_sol' => $respEmpre['clave_sol'],
                        'usuario_sol' => $respEmpre['user_sol']
                    ]);

                    // Generar XML según tipo de documento
                    if ($tipo_doc == '1') {
                        $dataResp = $this->sunatApi->genBoletaXML($dataSend);
                    } else {
                        $dataResp = $this->sunatApi->genFacturaXML($dataSend);
                    }

                    if ($dataResp['res']) {
                        // Insertar en ventas_sunat
                        $c_sunat = new VentaSunat();
                        $c_sunat->setIdVenta($id_venta);
                        $c_sunat->setHash($dataResp['data']['hash']);
                        $c_sunat->setNombreXml($dataResp['data']['nombre_archivo']);
                        $c_sunat->setQrData($dataResp['data']['qr']);

                        if (!$c_sunat->insertar()) {
                            throw new Exception('Error al guardar datos de SUNAT: ' . $c_sunat->getSqlError());
                        }

                        // AHORA SÍ incrementar el número del documento (DESPUÉS de generar XML)
                        $numero_actual = intval($c_tido->getNumero());
                        $numero_siguiente = $numero_actual + 1;
                        $c_tido->setNumero($numero_siguiente);
                        $c_tido->modificar();

                    } else {
                        throw new Exception('Error al generar XML: ' . ($dataResp['msg'] ?? 'Error desconocido'));
                    }
                } else {
                    // Para otros tipos de documentos (Nota de Venta, etc.)
                    $c_sunat = new VentaSunat();
                    $c_sunat->setIdVenta($id_venta);
                    $c_sunat->setHash('-');
                    $c_sunat->setNombreXml('-');
                    $c_sunat->setQrData('-');
                    
                    if (!$c_sunat->insertar()) {
                        throw new Exception('Error al guardar datos de documento: ' . $c_sunat->getSqlError());
                    }

                    // Incrementar número también para otros tipos de documentos
                    $numero_actual = intval($c_tido->getNumero());
                    $numero_siguiente = $numero_actual + 1;
                    $c_tido->setNumero($numero_siguiente);
                    $c_tido->modificar();
                }

                // Marcar pago como facturado
                if (!$this->pagoCajaArequipaModel->marcarComoFacturado($id_pago, $id_venta)) {
                    throw new Exception('Error al marcar pago como facturado');
                }

                // Commit de la transacción
                $conexion->commit();

                // Devolver el número ORIGINAL (el que se usó en la factura), no el incrementado
                echo json_encode([
                    'success' => true,
                    'message' => 'Factura generada correctamente. Debe enviarla manualmente a SUNAT desde el módulo de Ventas.',
                    'id_venta' => $id_venta,
                    'serie' => $c_tido->getSerie(),
                    'numero' => $numero_original, // NÚMERO ORIGINAL (120), no el incrementado (121)
                    'tipo_doc' => $tipo_doc
                ]);

            } catch (Exception $e) {
                $conexion->rollback();
                throw $e;
            }

        } catch (Exception $e) {
            error_log("Error en generarFacturaPago: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al generar factura: ' . $e->getMessage()
            ]);
        }
    }
}
