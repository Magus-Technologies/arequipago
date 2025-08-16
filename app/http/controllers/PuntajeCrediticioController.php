<?php
require_once "app/models/PuntajeCrediticioModel.php";
require_once "app/models/Financiamiento.php";
require_once "app/models/Cupon.php";
// Agregar las librerías necesarias al inicio del método
require_once 'utils/lib/vendor/autoload.php';
require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/exel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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
            
            // DESPUÉS:
            $filtros = [
                'tipo' => isset($_GET['tipo']) ? $_GET['tipo'] : 'todos',
                'busqueda' => isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '',
                'rango' => isset($_GET['rango']) ? $_GET['rango'] : '',
                'fechaInicio' => isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : '',
                'fechaFin' => isset($_GET['fechaFin']) ? $_GET['fechaFin'] : ''
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

            $formato = $_GET['formato'] ?? 'excel';
            $clienteIndividual = isset($_GET['id']) ? $_GET['id'] : null;

            // Obtener datos
            if ($clienteIndividual) {
                // Exportar cliente individual
                $data = $this->puntajeModel->obtenerDetalleCliente($filtros['tipo'], $clienteIndividual);
                $resultado = [
                    'clientes' => [
                        array_merge($data['cliente'], [
                            'puntaje_actual' => $data['puntaje']['puntaje_actual'] ?? 100,
                            'total_financiamientos' => $data['puntaje']['total_financiamientos'] ?? 0,
                            'total_retrasos' => $data['puntaje']['total_retrasos'] ?? 0,
                            'fecha_actualizacion' => $data['puntaje']['fecha_actualizacion'] ?? date('Y-m-d'),
                            'tipo_cliente' => $filtros['tipo'],
                            'numero_documento' => $data['cliente']['n_documento'] ?? $data['cliente']['nro_documento']
                        ])
                    ]
                ];
            } else {
                // Exportar múltiples clientes
                $resultado = $this->puntajeModel->obtenerClientesPuntaje($filtros, 1, 10000);
            }

            if ($formato === 'pdf') {
                $this->exportarPDF($resultado, $clienteIndividual);
            } else {
                $this->exportarExcel($resultado, $clienteIndividual);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Método para exportar a Excel
    private function exportarExcel($resultado, $clienteIndividual = null)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Configurar título
        $titulo = $clienteIndividual ? 'Detalle de Puntaje Crediticio - Cliente Individual' : 'Reporte de Puntajes Crediticios';
        $sheet->setCellValue('A1', $titulo);
        $sheet->mergeCells('A1:L1');
        
        // Estilo del título
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E86AB']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Información adicional
        $sheet->setCellValue('A2', 'Fecha de generación:');
        $sheet->setCellValue('B2', date('d/m/Y H:i:s'));
        $sheet->getStyle('A2')->getFont()->setBold(true);

        // Encabezados
        $headers = [
            'A4' => 'Tipo Cliente',
            'B4' => 'Documento',
            'C4' => 'Número Documento',
            'D4' => 'Nombres',
            'E4' => 'Apellido Paterno',
            'F4' => 'Apellido Materno',
            'G4' => 'Teléfono',
            'H4' => 'Puntaje Actual',
            'I4' => 'Total Financiamientos',
            'J4' => 'Total Retrasos',
            'K4' => 'Nivel',
            'L4' => 'Fecha Actualización'
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        // Estilo de encabezados
        $sheet->getStyle('A4:L4')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Datos
        $row = 5;
        foreach ($resultado['clientes'] as $cliente) {
            $nivel = $this->determinarNivel($cliente['puntaje_actual']);
            
            $sheet->setCellValue('A' . $row, ucfirst($cliente['tipo_cliente']));
            $sheet->setCellValue('B' . $row, $cliente['tipo_doc']);
            $sheet->setCellValue('C' . $row, $cliente['numero_documento']);
            $sheet->setCellValue('D' . $row, $cliente['nombres']);
            $sheet->setCellValue('E' . $row, $cliente['apellido_paterno']);
            $sheet->setCellValue('F' . $row, $cliente['apellido_materno']);
            $sheet->setCellValue('G' . $row, $cliente['telefono'] ?: 'N/A');
            $sheet->setCellValue('H' . $row, $cliente['puntaje_actual']);
            $sheet->setCellValue('I' . $row, $cliente['total_financiamientos']);
            $sheet->setCellValue('J' . $row, $cliente['total_retrasos']);
            $sheet->setCellValue('K' . $row, $nivel);
            $sheet->setCellValue('L' . $row, date('d/m/Y', strtotime($cliente['fecha_actualizacion'])));

            // Colorear puntaje según nivel
            $colorPuntaje = $this->obtenerColorPuntaje($cliente['puntaje_actual']);
            $sheet->getStyle('H' . $row)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => $colorPuntaje]]
            ]);

            // Estilo alternado de filas
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8F9FA']
                    ]
                ]);
            }

            $row++;
        }

        // Bordes para todos los datos
        $sheet->getStyle('A4:L' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);

        // Ajustar ancho de columnas
        foreach (range('A', 'L') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Configurar respuesta
        $filename = $clienteIndividual ? 'detalle_puntaje_cliente_' . date('Y-m-d') . '.xlsx' : 'puntajes_crediticios_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    // Método para exportar a PDF
    private function exportarPDF($resultado, $clienteIndividual = null)
    {
        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4-L', // Formato horizontal
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 20,
            'margin_bottom' => 20
        ]);

        $titulo = $clienteIndividual ? 'Detalle de Puntaje Crediticio - Cliente Individual' : 'Reporte de Puntajes Crediticios';
        
        $html = '
        <style>
            body { font-family: Arial, sans-serif; font-size: 10px; }
            .header { background-color: #2E86AB; color: white; padding: 10px; text-align: center; margin-bottom: 20px; }
            .header h1 { margin: 0; font-size: 18px; }
            .info { margin-bottom: 15px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th { background-color: #4472C4; color: white; padding: 8px; text-align: center; font-weight: bold; }
            td { padding: 6px; text-align: center; border: 1px solid #ddd; }
            tr:nth-child(even) { background-color: #f8f9fa; }
            .puntaje-excelente { color: #28a745; font-weight: bold; }
            .puntaje-bueno { color: #007bff; font-weight: bold; }
            .puntaje-regular { color: #ffc107; font-weight: bold; }
            .puntaje-malo { color: #dc3545; font-weight: bold; }
            .nivel-badge { padding: 3px 8px; border-radius: 4px; color: white; font-size: 9px; }
            .nivel-excelente { background-color: #28a745; }
            .nivel-bueno { background-color: #007bff; }
            .nivel-regular { background-color: #ffc107; }
            .nivel-malo { background-color: #dc3545; }
        </style>
        
        <div class="header">
            <h1>' . $titulo . '</h1>
        </div>
        
        <div class="info">
            <strong>Fecha de generación:</strong> ' . date('d/m/Y H:i:s') . '<br>
            <strong>Total de registros:</strong> ' . count($resultado['clientes']) . '
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Documento</th>
                    <th>Número</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Teléfono</th>
                    <th>Puntaje</th>
                    <th>Financiamientos</th>
                    <th>Retrasos</th>
                    <th>Nivel</th>
                    <th>Última Actualización</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($resultado['clientes'] as $cliente) {
            $nivel = $this->determinarNivel($cliente['puntaje_actual']);
            $clasePuntaje = $this->obtenerClasePuntaje($cliente['puntaje_actual']);
            $claseNivel = $this->obtenerClaseNivel($cliente['puntaje_actual']);
            
            $html .= '
                <tr>
                    <td>' . ucfirst($cliente['tipo_cliente']) . '</td>
                    <td>' . $cliente['tipo_doc'] . '</td>
                    <td>' . $cliente['numero_documento'] . '</td>
                    <td>' . $cliente['nombres'] . '</td>
                    <td>' . $cliente['apellido_paterno'] . ' ' . $cliente['apellido_materno'] . '</td>
                    <td>' . ($cliente['telefono'] ?: 'N/A') . '</td>
                    <td class="' . $clasePuntaje . '">' . $cliente['puntaje_actual'] . '</td>
                    <td>' . $cliente['total_financiamientos'] . '</td>
                    <td>' . $cliente['total_retrasos'] . '</td>
                    <td><span class="nivel-badge ' . $claseNivel . '">' . $nivel . '</span></td>
                    <td>' . date('d/m/Y', strtotime($cliente['fecha_actualizacion'])) . '</td>
                </tr>';
        }

        $html .= '
            </tbody>
        </table>';

        $mpdf->WriteHTML($html);
        
        $filename = $clienteIndividual ? 'detalle_puntaje_cliente_' . date('Y-m-d') . '.pdf' : 'puntajes_crediticios_' . date('Y-m-d') . '.pdf';
        $mpdf->Output($filename, 'D');
    }

        // Métodos auxiliares para estilos
        private function obtenerColorPuntaje($puntaje)
        {
            if ($puntaje >= 76) return '28A745'; // Verde
            if ($puntaje >= 51) return '007BFF'; // Azul
            if ($puntaje >= 26) return 'FFC107'; // Amarillo
            return 'DC3545'; // Rojo
        }

        private function obtenerClasePuntaje($puntaje)
        {
            if ($puntaje >= 76) return 'puntaje-excelente';
            if ($puntaje >= 51) return 'puntaje-bueno';
            if ($puntaje >= 26) return 'puntaje-regular';
            return 'puntaje-malo';
        }

        private function obtenerClaseNivel($puntaje)
        {
            if ($puntaje >= 76) return 'nivel-excelente';
            if ($puntaje >= 51) return 'nivel-bueno';
            if ($puntaje >= 26) return 'nivel-regular';
            return 'nivel-malo';
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

    public function obtenerPuntajeYDatos()
    {
    try {
    header('Content-Type: application/json');
    $tipo = $_GET['tipo'] ?? '';
    $id = $_GET['id'] ?? 0;
    if (empty($tipo) || !in_array($tipo, ['cliente', 'conductor']) || !$id) {
    throw new Exception("Parámetros inválidos: se requiere 'tipo' (cliente o conductor) e 'id'");
    }
    $detalle = $this->puntajeModel->obtenerDatosCompletos($tipo, $id);
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

    public function obtenerResumenCrediticio($tipo, $id)
    {
        try {
            header('Content-Type: application/json');

            // Validar parámetros de entrada
            if (empty($tipo) || !in_array($tipo, ['cliente', 'conductor']) || empty($id) || !is_numeric($id)) {
                throw new Exception("Parámetros inválidos: se requiere 'tipo' (cliente o conductor) y un 'id' numérico.");
            }

            // 1. Créditos Activos
            $financiamientoModel = new Financiamiento();
            $creditosActivos = $financiamientoModel->contarCreditosActivos($tipo, $id);

            // 2. Puntaje Crediticio
            $puntajeData = $this->puntajeModel->calcularPuntajeIndividual($tipo, $id);
            $puntaje = $puntajeData['puntaje'];

            // 3. Cupones Disponibles
            $cuponModel = new Cupon();
            if ($tipo === 'cliente') {
                $cupones = $cuponModel->verificarClienteTieneCupon($id);
            } else {
                $cupones = $cuponModel->verificarConductorTieneCupon($id);
            }
            $cuponesDisponibles = count($cupones);

            $respuesta = [
                'creditosActivos' => $creditosActivos,
                'puntaje' => $puntaje,
                'cuponesDisponibles' => $cuponesDisponibles
            ];

            echo json_encode($respuesta);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }
}