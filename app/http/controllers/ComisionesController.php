<?php
require_once "app/models/Comision.php";
// Agregar estos imports para PhpSpreadsheet
require_once 'utils/lib/exel/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ComisionesController extends Controller
{
    private $comision;

    public function __construct()
    {
        $this->comision = new Comision();
        $this->conectar = (new Conexion())->getConexion();
    }

    public function cargarComisiones()
    {
        try {
            $usuario_id = $_SESSION['usuario_id'];
            $rol_usuario = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null;
            
            // Obtener filtros del POST
            $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
            $estado = isset($_POST['estado']) ? $_POST['estado'] : '';
            $fecha_desde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : '';
            $fecha_hasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : '';
            $usuario_filtro = isset($_POST['usuario_filtro']) ? $_POST['usuario_filtro'] : '';

            // Si no es director (rol 3), solo mostrar sus comisiones
            // Si es director y seleccionó un usuario específico, filtrar por ese usuario
            if ($rol_usuario != 3) {
                $filtro_usuario = $usuario_id;
            } else {
                $filtro_usuario = !empty($usuario_filtro) ? $usuario_filtro : null;
            }

            $comisiones = $this->comision->obtenerComisiones($filtro_usuario, $tipo, $estado, $fecha_desde, $fecha_hasta);
            $estadisticas = $this->comision->obtenerEstadisticasComisiones($filtro_usuario);
            
            echo json_encode([
                'success' => true,
                'data' => $comisiones,
                'estadisticas' => $estadisticas
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

        public function exportarComisiones()
    {
        // Limpiar buffer de salida
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        try {
            $usuario_id = $_SESSION['usuario_id'];
            $rol_usuario = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null;
            
            // Obtener filtros del POST
            $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
            $estado = isset($_POST['estado']) ? $_POST['estado'] : '';
            $fecha_desde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : '';
            $fecha_hasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : '';
            $usuario_filtro = isset($_POST['usuario_filtro']) ? $_POST['usuario_filtro'] : '';
            
            // Determinar filtro de usuario
            if ($rol_usuario != 3) {
                $filtro_usuario = $usuario_id;
            } else {
                $filtro_usuario = !empty($usuario_filtro) ? $usuario_filtro : null;
            }
            
            // Obtener comisiones
            $comisiones = $this->comision->obtenerComisiones($filtro_usuario, $tipo, $estado, $fecha_desde, $fecha_hasta);
            $estadisticas = $this->comision->obtenerEstadisticasComisiones($filtro_usuario);
            
            // Generar Excel
            $this->generarExcelComisiones($comisiones, $estadisticas, $rol_usuario);
            
        } catch (Exception $e) {
            // Limpiar buffer en caso de error
            if (ob_get_level()) {
                ob_end_clean();
            }
            header('Content-Type: text/html; charset=utf-8');
            echo "Error al exportar: " . $e->getMessage();
            exit();
        }
    }
    
    private function generarExcelComisiones($comisiones, $estadisticas, $rol_usuario)
    {
        // Crear nuevo spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configurar propiedades del documento
        $spreadsheet->getProperties()
            ->setCreator("Sistema de Comisiones")
            ->setTitle("Reporte de Comisiones")
            ->setSubject("Reporte de Comisiones")
            ->setDescription("Reporte detallado de comisiones generadas");
        
        // Título principal
        $sheet->setCellValue('A1', 'REPORTE DE COMISIONES');
        $sheet->mergeCells('A1:' . ($rol_usuario == 3 ? 'I1' : 'H1'));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Fecha del reporte
        $sheet->setCellValue('A2', 'Generado el: ' . date('d/m/Y H:i:s'));
        $sheet->mergeCells('A2:' . ($rol_usuario == 3 ? 'I2' : 'H2'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Estadísticas
        $row = 4;
        $sheet->setCellValue('A' . $row, 'RESUMEN ESTADÍSTICO');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Total de Comisiones:');
        $sheet->setCellValue('B' . $row, $estadisticas['total']);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Pendientes:');
        $sheet->setCellValue('B' . $row, $estadisticas['pendientes']);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Pagadas:');
        $sheet->setCellValue('B' . $row, $estadisticas['pagadas']);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Canceladas:');
        $sheet->setCellValue('B' . $row, $estadisticas['canceladas']);
        $row += 2;
        
        // Headers de la tabla
        $headers = ['ID', 'Fecha', 'Tipo'];
        if ($rol_usuario == 3) {
            $headers[] = 'Usuario';
        }
        $headers = array_merge($headers, ['Monto', 'Moneda', 'Estado', 'Tipo Vehículo', 'Observaciones']);
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('38a4f8');
            $sheet->getStyle($col . $row)->getFont()->getColor()->setRGB('FFFFFF');
            $col++;
        }
        
        // Aplicar bordes a los headers
        $lastCol = chr(ord('A') + count($headers) - 1);
        $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
        
        $row++;
        
        // Datos de comisiones
        $totalSoles = 0;
        $totalDolares = 0;
        
        foreach ($comisiones as $comision) {
            // Calcular totales
            if ($comision['moneda'] === 'S/.') {
                $totalSoles += $comision['monto_comision'];
            } else {
                $totalDolares += $comision['monto_comision'];
            }
            
            $col = 'A';
            
            // ID
            $sheet->setCellValue($col . $row, $comision['id_comision']);
            $col++;
            
            // Fecha
            $sheet->setCellValue($col . $row, date('d/m/Y H:i', strtotime($comision['fecha_comision'])));
            $col++;
            
            // Tipo
            $sheet->setCellValue($col . $row, ucfirst($comision['tipo_comision']));
            $col++;
            
            // Usuario (solo si es director)
            if ($rol_usuario == 3) {
                $sheet->setCellValue($col . $row, $comision['nombre_usuario'] ?? 'N/A');
                $col++;
            }
            
            // Monto
            $sheet->setCellValue($col . $row, $comision['monto_comision']);
            $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            // Color según moneda
            if ($comision['moneda'] === '$') {
                $sheet->getStyle($col . $row)->getFont()->getColor()->setRGB('38a4f8');
            } else {
                $sheet->getStyle($col . $row)->getFont()->getColor()->setRGB('02a499');
            }
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $col++;
            
            // Moneda
            $sheet->setCellValue($col . $row, $comision['moneda']);
            $col++;
            
            // Estado
            $sheet->setCellValue($col . $row, ucfirst($comision['estado_comision']));
            // Color según estado
            switch ($comision['estado_comision']) {
                case 'pendiente':
                    $sheet->getStyle($col . $row)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('fcf34b');
                    break;
                case 'pagada':
                    $sheet->getStyle($col . $row)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('02a499');
                    $sheet->getStyle($col . $row)->getFont()->getColor()->setRGB('FFFFFF');
                    break;
                case 'cancelada':
                    $sheet->getStyle($col . $row)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('ec4561');
                    $sheet->getStyle($col . $row)->getFont()->getColor()->setRGB('FFFFFF');
                    break;
            }
            $col++;
            
            // Tipo Vehículo
            $sheet->setCellValue($col . $row, $comision['tipo_vehiculo'] ?? 'N/A');
            $col++;
            
            // Observaciones
            $sheet->setCellValue($col . $row, $comision['observaciones'] ?? '');
            $col++;
            
            // Aplicar bordes a la fila
            $sheet->getStyle('A' . $row . ':' . chr(ord($col) - 1) . $row)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            
            $row++;
        }
        
        // Fila de totales
        $row++;
        $col = 'A';
        $sheet->setCellValue($col . $row, 'TOTALES:');
        $sheet->getStyle($col . $row)->getFont()->setBold(true);
        
        // Avanzar hasta la columna de monto
        $montoCol = $rol_usuario == 3 ? 'E' : 'D';
        $sheet->setCellValue($montoCol . $row, 'S/. ' . number_format($totalSoles, 2));
        $sheet->getStyle($montoCol . $row)->getFont()->setBold(true)->getColor()->setRGB('02a499');
        
        $row++;
        $sheet->setCellValue($montoCol . $row, '$ ' . number_format($totalDolares, 2));
        $sheet->getStyle($montoCol . $row)->getFont()->setBold(true)->getColor()->setRGB('38a4f8');
        
        // Ajustar ancho de columnas
        foreach (range('A', $lastCol) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Configurar headers para descarga
        $filename = 'comisiones_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        
        // Generar y enviar el archivo
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        
        // Limpiar memoria
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        
        exit();
    }
    
    /**
     * Obtiene lista de usuarios para el filtro (solo para directores)
     */
    public function obtenerUsuariosParaFiltro()
    {
        try {
            $rol_usuario = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null;
            
            // Solo directores pueden ver esta lista
            if ($rol_usuario != 3) {
                echo json_encode([
                    'success' => false,
                    'message' => 'No autorizado'
                ]);
                return;
            }
            
            $sql = "SELECT DISTINCT u.usuario_id, u.nombres 
                    FROM usuarios u 
                    INNER JOIN comisiones c ON u.usuario_id = c.usuario_id 
                    WHERE u.estado = 1 
                    ORDER BY u.nombres ASC";
            
            $stmt = $this->conectar->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $usuarios = [];
            while ($row = $result->fetch_assoc()) {
                $usuarios[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $usuarios
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

        /**
     * Obtiene detalles completos de una comisión específica
     */
    public function obtenerDetalleComision()
    {
        try {
            $id_comision = isset($_POST['id_comision']) ? intval($_POST['id_comision']) : 0;
            $usuario_id = $_SESSION['usuario_id'];
            $rol_usuario = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null;
            
            if ($id_comision <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de comisión inválido'
                ]);
                return;
            }
            
            $detalle = $this->comision->obtenerDetalleComision($id_comision, $usuario_id, $rol_usuario);
            
            if ($detalle) {
                echo json_encode([
                    'success' => true,
                    'data' => $detalle
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Comisión no encontrada o sin permisos'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}