<?php
require_once "app/models/PuntajeCrediticioModel.php";

class PuntajeCrediticioController extends Controller
{
    private $conexion;
    private $puntajeModel;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->puntajeModel = new PuntajeCrediticioModel();
    }

    // Mostrar la vista principal del sistema de puntaje crediticio
//public function index()
  //  {
    //    $data = [
    //      'title' => 'Sistema de Puntaje Crediticio',
    //        'css' => ['puntaje-crediticio.css'],
    //        'js' => ['puntaje-crediticio.js']
    //    ];
        
    //    $this->loadView('puntaje-crediticio/index', $data);
    //} 

    // Obtener estadísticas generales
    public function obtenerEstadisticasPuntaje()
    {
        try {
            header('Content-Type: application/json');
            
            $estadisticas = $this->puntajeModel->obtenerEstadisticasGenerales();
            
            echo json_encode([
                'success' => true,
                'data' => $estadisticas
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Obtener clientes con paginación y filtros
    public function obtenerClientesPuntaje()
    {
        try {
            header('Content-Type: application/json');
            
            // Obtener parámetros de la petición
            $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            $limite = isset($_GET['limite']) ? max(1, min(50, intval($_GET['limite']))) : 12;
            
            $filtros = [
                'tipo' => isset($_GET['tipo']) ? $_GET['tipo'] : 'todos',
                'busqueda' => isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '',
                'rango' => isset($_GET['rango']) ? $_GET['rango'] : '',
                'fecha' => isset($_GET['fecha']) ? $_GET['fecha'] : ''
            ];

            $resultado = $this->puntajeModel->obtenerClientesPuntaje($filtros, $pagina, $limite);
            
            echo json_encode([
                'success' => true,
                'data' => $resultado
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Obtener detalle de un cliente específico
    public function obtenerDetalleCliente()
    {
        try {
            header('Content-Type: application/json');
            
            $tipo = $_GET['tipo'] ?? '';
            $id = $_GET['id'] ?? 0;

            if (empty($tipo) || !in_array($tipo, ['cliente', 'conductor']) || !$id) {
                throw new Exception("Parámetros inválidos");
            }

            $detalle = $this->puntajeModel->obtenerDetalleCliente($tipo, $id);
            
            echo json_encode([
                'success' => true,
                'data' => $detalle
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Obtener historial de puntaje crediticio
    public function obtenerHistorialPuntaje()
    {
        try {
            header('Content-Type: application/json');
            
            $tipo = $_GET['tipo'] ?? '';
            $id = $_GET['id'] ?? 0;

            if (empty($tipo) || !in_array($tipo, ['cliente', 'conductor']) || !$id) {
                throw new Exception("Parámetros inválidos");
            }

            $filtros = [
                'mes' => $_GET['mes'] ?? '',
                'estado' => $_GET['estado'] ?? ''
            ];

            $historial = $this->puntajeModel->obtenerHistorialPuntaje($tipo, $id, $filtros);
            
            echo json_encode([
                'success' => true,
                'data' => $historial
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Actualizar todos los puntajes crediticios
    public function actualizarPuntajesCrediticios()
    {
        try {
            header('Content-Type: application/json');
            
            // Verificar que sea una petición POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }

            $actualizados = $this->puntajeModel->actualizarTodosPuntajes();
            
            echo json_encode([
                'success' => true,
                'message' => "Se actualizaron $actualizados registros correctamente",
                'data' => ['actualizados' => $actualizados]
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Actualizar puntaje de un cliente específico
    public function actualizarPuntajeIndividual()
    {
        try {
            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }

            $input = json_decode(file_get_contents('php://input'), true);
            
            $tipo = $input['tipo'] ?? '';
            $id = $input['id'] ?? 0;

            if (empty($tipo) || !in_array($tipo, ['cliente', 'conductor']) || !$id) {
                throw new Exception("Parámetros inválidos");
            }

            // Obtener puntaje anterior
            $puntajeAnterior = $this->obtenerPuntajeActual($tipo, $id);

            // Calcular nuevo puntaje
            $nuevoPuntaje = $this->puntajeModel->calcularPuntajeIndividual($tipo, $id);
            
            // Actualizar puntaje
            $puntajeCrediticioId = $this->puntajeModel->actualizarPuntajeCrediticio($tipo, $id, $nuevoPuntaje);

            // Registrar en historial si hubo cambio
            if ($puntajeAnterior != $nuevoPuntaje['puntaje']) {
                $puntosPerdidos = max(0, $puntajeAnterior - $nuevoPuntaje['puntaje']);
                $motivo = "Actualización manual del sistema";
                
                $this->puntajeModel->registrarHistorialPuntaje(
                    $puntajeCrediticioId,
                    $puntajeAnterior,
                    $nuevoPuntaje['puntaje'],
                    $puntosPerdidos,
                    $motivo
                );
            }

            echo json_encode([
                'success' => true,
                'message' => 'Puntaje actualizado correctamente',
                'data' => [
                    'puntaje_anterior' => $puntajeAnterior,
                    'puntaje_nuevo' => $nuevoPuntaje['puntaje'],
                    'cambio' => $nuevoPuntaje['puntaje'] - $puntajeAnterior
                ]
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Obtener resumen por rangos de puntaje
    public function obtenerResumenRangos()
    {
        try {
            header('Content-Type: application/json');
            
            $resumen = $this->puntajeModel->obtenerResumenPorRangos();
            
            echo json_encode([
                'success' => true,
                'data' => $resumen
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Obtener alertas de clientes en riesgo
    public function obtenerAlertasRiesgo()
    {
        try {
            header('Content-Type: application/json');
            
            $alertas = $this->puntajeModel->obtenerAlertasRiesgo();
            
            echo json_encode([
                'success' => true,
                'data' => $alertas
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Procesar puntajes diarios (para cron job)
    public function procesarPuntajesDiarios()
    {
        try {
            header('Content-Type: application/json');
            
            // Verificar que sea ejecutado desde línea de comandos o con una clave especial
            if (php_sapi_name() !== 'cli' && !isset($_GET['cron_key'])) {
                throw new Exception("Acceso no autorizado");
            }

            $resultado = $this->puntajeModel->procesarPuntajesDiarios();
            
            // Log del proceso
            $this->logProcesamiento($resultado);
            
            echo json_encode([
                'success' => true,
                'message' => "Procesamiento diario completado",
                'data' => $resultado
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Exportar datos de puntaje crediticio
    public function exportarPuntajes()
    {
        try {
            $filtros = [
                'tipo' => $_GET['tipo'] ?? 'todos',
                'rango' => $_GET['rango'] ?? '',
                'fecha' => $_GET['fecha'] ?? ''
            ];

            // Obtener todos los datos sin paginación
            $resultado = $this->puntajeModel->obtenerClientesPuntaje($filtros, 1, 10000);
            
            $filename = 'puntajes_crediticios_' . date('Y-m-d') . '.csv';
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, must-revalidate');

            $output = fopen('php://output', 'w');
            
            // Escribir encabezados
            fputcsv($output, [
                'Tipo Cliente',
                'Documento',
                'Número Documento',
                'Nombres',
                'Apellido Paterno',
                'Apellido Materno',
                'Teléfono',
                'Puntaje Actual',
                'Total Financiamientos',
                'Total Retrasos',
                'Nivel',
                'Fecha Actualización'
            ]);

            // Escribir datos
            foreach ($resultado['clientes'] as $cliente) {
                $nivel = $this->determinarNivel($cliente['puntaje_actual']);
                
                fputcsv($output, [
                    ucfirst($cliente['tipo_cliente']),
                    $cliente['tipo_doc'],
                    $cliente['numero_documento'],
                    $cliente['nombres'],
                    $cliente['apellido_paterno'],
                    $cliente['apellido_materno'],
                    $cliente['telefono'] ?: 'N/A',
                    $cliente['puntaje_actual'],
                    $cliente['total_financiamientos'],
                    $cliente['total_retrasos'],
                    $nivel,
                    $cliente['fecha_actualizacion']
                ]);
            }

            fclose($output);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Método privado para obtener puntaje actual
    private function obtenerPuntajeActual($tipo, $id)
    {
        $campoId = ($tipo === 'cliente') ? 'id_cliente' : 'id_conductor';
        
        $sql = "SELECT puntaje_actual FROM puntaje_crediticio WHERE tipo_cliente = ? AND $campoId = ?";
        $stmt = mysqli_prepare($this->conexion, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $tipo, $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return $row ? $row['puntaje_actual'] : 100;
    }

    // Método privado para determinar nivel de puntaje
    private function determinarNivel($puntaje)
    {
        if ($puntaje >= 76) return 'Excelente';
        if ($puntaje >= 51) return 'Bueno';
        if ($puntaje >= 26) return 'Regular';
        return 'Malo';
    }

    // Método privado para registrar logs de procesamiento
    private function logProcesamiento($resultado)
    {
        $logFile = 'logs/puntaje_crediticio_' . date('Y-m-d') . '.log';
        $logDir = dirname($logFile);
        
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logEntry = date('Y-m-d H:i:s') . " - Procesamiento diario completado\n";
        $logEntry .= "Procesados: {$resultado['procesados']}\n";
        $logEntry .= "Errores: {$resultado['errores']}\n";
        $logEntry .= "Detalles:\n" . implode("\n", $resultado['log']) . "\n";
        $logEntry .= str_repeat('-', 50) . "\n";

        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    // Obtener logs de procesamiento
    public function obtenerLogs()
    {
        try {
            header('Content-Type: application/json');
            
            $fecha = $_GET['fecha'] ?? date('Y-m-d');
            $logFile = 'logs/puntaje_crediticio_' . $fecha . '.log';
            
            if (file_exists($logFile)) {
                $contenido = file_get_contents($logFile);
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'fecha' => $fecha,
                        'contenido' => $contenido,
                        'existe' => true
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'fecha' => $fecha,
                        'contenido' => 'No hay logs para esta fecha',
                        'existe' => false
                    ]
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Simular procesamiento de cuota (para testing)
    public function simularPagoCuota()
    {
        try {
            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }

            $input = json_decode(file_get_contents('php://input'), true);
            
            $idCuota = $input['id_cuota'] ?? 0;
            $fechaPago = $input['fecha_pago'] ?? date('Y-m-d');

            if (!$idCuota) {
                throw new Exception("ID de cuota requerido");
            }

            // Simular pago de cuota
            $sqlUpdateCuota = "UPDATE cuotas_financiamiento SET fecha_pago = ?, estado = 'pagado' WHERE idcuotas_financiamiento = ?";
            $stmt = mysqli_prepare($this->conexion, $sqlUpdateCuota);
            mysqli_stmt_bind_param($stmt, 'si', $fechaPago, $idCuota);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Obtener información de la cuota y financiamiento
            $sqlCuota = "SELECT cf.*, f.id_cliente, f.id_conductor,
                                CASE 
                                    WHEN f.id_cliente IS NOT NULL THEN 'cliente'
                                    ELSE 'conductor'
                                END as tipo_cliente,
                                CASE 
                                    WHEN f.id_cliente IS NOT NULL THEN f.id_cliente
                                    ELSE f.id_conductor
                                END as id_referencia
                         FROM cuotas_financiamiento cf
                         INNER JOIN financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
                         WHERE cf.idcuotas_financiamiento = ?";
            
            $stmt = mysqli_prepare($this->conexion, $sqlCuota);
            mysqli_stmt_bind_param($stmt, 'i', $idCuota);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $cuota = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if (!$cuota) {
                throw new Exception("Cuota no encontrada");
            }

            // Recalcular puntaje
            $puntajeAnterior = $this->obtenerPuntajeActual($cuota['tipo_cliente'], $cuota['id_referencia']);
            $nuevoPuntaje = $this->puntajeModel->calcularPuntajeIndividual($cuota['tipo_cliente'], $cuota['id_referencia']);
            
            // Actualizar puntaje
            $puntajeCrediticioId = $this->puntajeModel->actualizarPuntajeCrediticio($cuota['tipo_cliente'], $cuota['id_referencia'], $nuevoPuntaje);

            // Determinar motivo y puntos perdidos
            $puntosPerdidos = max(0, $puntajeAnterior - $nuevoPuntaje['puntaje']);
            $motivo = $this->determinarMotivoPago($cuota, $fechaPago);

            // Registrar en historial
            $this->puntajeModel->registrarHistorialPuntaje(
                $puntajeCrediticioId,
                $puntajeAnterior,
                $nuevoPuntaje['puntaje'],
                $puntosPerdidos,
                $motivo,
                $idCuota
            );

            echo json_encode([
                'success' => true,
                'message' => 'Pago simulado y puntaje actualizado correctamente',
                'data' => [
                    'puntaje_anterior' => $puntajeAnterior,
                    'puntaje_nuevo' => $nuevoPuntaje['puntaje'],
                    'puntos_perdidos' => $puntosPerdidos,
                    'motivo' => $motivo
                ]
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Método privado para determinar motivo del pago
    private function determinarMotivoPago($cuota, $fechaPago)
    {
        $fechaVencimiento = $cuota['fecha_vencimiento'];

        if ($fechaPago <= $fechaVencimiento) {
            return "Cuota #{$cuota['numero_cuota']} pagada puntualmente";
        } else {
            $diasRetraso = (strtotime($fechaPago) - strtotime($fechaVencimiento)) / (60 * 60 * 24);
            return "Cuota #{$cuota['numero_cuota']} pagada con {$diasRetraso} días de retraso";
        }
    }

    // Obtener métricas avanzadas
    public function obtenerMetricasAvanzadas()
    {
        try {
            header('Content-Type: application/json');
            
            $metricas = [];

            // Distribución por rangos de puntaje
            $sqlDistribucion = "SELECT 
                                    CASE 
                                        WHEN puntaje_actual >= 76 THEN 'Excelente (76-100)'
                                        WHEN puntaje_actual >= 51 THEN 'Bueno (51-75)'
                                        WHEN puntaje_actual >= 26 THEN 'Regular (26-50)'
                                        ELSE 'Malo (0-25)'
                                    END as rango,
                                    COUNT(*) as cantidad
                                FROM puntaje_crediticio
                                GROUP BY rango
                                ORDER BY MIN(puntaje_actual) DESC";

            $result = mysqli_query($this->conexion, $sqlDistribucion);
            $metricas['distribucion'] = mysqli_fetch_all($result, MYSQLI_ASSOC);

            // Tendencia mensual (últimos 6 meses)
            $sqlTendencia = "SELECT 
                                DATE_FORMAT(fecha_evento, '%Y-%m') as mes,
                                AVG(puntaje_nuevo) as puntaje_promedio,
                                COUNT(*) as cambios
                            FROM historial_puntaje 
                            WHERE fecha_evento >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                            GROUP BY mes
                            ORDER BY mes";

            $result = mysqli_query($this->conexion, $sqlTendencia);
            $metricas['tendencia'] = mysqli_fetch_all($result, MYSQLI_ASSOC);

            // Top 10 clientes con mejor puntaje
            $sqlTop = "SELECT 
                          pc.*,
                          CASE 
                              WHEN pc.tipo_cliente = 'cliente' THEN CONCAT(cf.nombres, ' ', cf.apellido_paterno)
                              ELSE CONCAT(c.nombres, ' ', c.apellido_paterno)
                          END as nombre_completo
                       FROM puntaje_crediticio pc
                       LEFT JOIN clientes_financiar cf ON pc.id_cliente = cf.id AND pc.tipo_cliente = 'cliente'
                       LEFT JOIN conductores c ON pc.id_conductor = c.id_conductor AND pc.tipo_cliente = 'conductor'
                       ORDER BY pc.puntaje_actual DESC
                       LIMIT 10";

            $result = mysqli_query($this->conexion, $sqlTop);
            $metricas['top_clientes'] = mysqli_fetch_all($result, MYSQLI_ASSOC);

            // Clientes que más han mejorado en el último mes
            $sqlMejores = "SELECT 
                              pc.*,
                              CASE 
                                  WHEN pc.tipo_cliente = 'cliente' THEN CONCAT(cf.nombres, ' ', cf.apellido_paterno)
                                  ELSE CONCAT(c.nombres, ' ', c.apellido_paterno)
                              END as nombre_completo,
                              (SELECT puntaje_nuevo - puntaje_anterior 
                               FROM historial_puntaje hp 
                               WHERE hp.id_puntaje_crediticio = pc.id 
                               AND hp.fecha_evento >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                               ORDER BY hp.fecha_evento DESC LIMIT 1) as mejora
                           FROM puntaje_crediticio pc
                           LEFT JOIN clientes_financiar cf ON pc.id_cliente = cf.id AND pc.tipo_cliente = 'cliente'
                           LEFT JOIN conductores c ON pc.id_conductor = c.id_conductor AND pc.tipo_cliente = 'conductor'
                           HAVING mejora > 0
                           ORDER BY mejora DESC
                           LIMIT 10";

            $result = mysqli_query($this->conexion, $sqlMejores);
            $metricas['mejores_mes'] = mysqli_fetch_all($result, MYSQLI_ASSOC);

            echo json_encode([
                'success' => true,
                'data' => $metricas
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}