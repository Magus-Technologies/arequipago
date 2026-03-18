<?php

require_once 'utils/lib/exel/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportFinanciamientosController extends Controller
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    public function exportarFinanciamientosExcel()
    {
        try {
            // Verificar permisos (solo directores)
            if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 3) {
                echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
                return;
            }

            $searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';
            $asesorId = isset($_GET['asesor_id']) && $_GET['asesor_id'] !== '' ? (int)$_GET['asesor_id'] : null;

            $where = "WHERE f.estado_eliminado = 0 AND (f.aprobado = 1 OR f.aprobado IS NULL)";
            $params = [];
            $types = '';

            if ($asesorId) {
                $where .= " AND f.usuario_id = ?";
                $params[] = $asesorId;
                $types .= 'i';
            }

            if ($searchTerm) {
                $searchLike = '%' . str_replace(' ', '%', trim($searchTerm)) . '%';
                $where .= " AND (c.nombres LIKE ? OR c.apellido_paterno LIKE ? OR c.nro_documento LIKE ?
                            OR cl.nombres LIKE ? OR cl.apellido_paterno LIKE ? OR cl.n_documento LIKE ?)";
                $params = array_merge($params, [$searchLike, $searchLike, $searchLike, $searchLike, $searchLike, $searchLike]);
                $types .= 'ssssss';
            }

            $query = "SELECT
                        f.idfinanciamiento,
                        f.codigo_asociado,
                        COALESCE(CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno),
                                 CONCAT(cl.nombres, ' ', cl.apellido_paterno, ' ', cl.apellido_materno)) AS cliente,
                        COALESCE(c.nro_documento, cl.n_documento) AS documento,
                        COALESCE(c.numUnidad, '') AS num_unidad,
                        pf.nombre_plan AS grupo_financiamiento,
                        f.monto_total,
                        f.cuotas,
                        f.estado,
                        f.fecha_creacion,
                        f.moneda,
                        CONCAT(COALESCE(u.nombres, ''), ' ', COALESCE(u.apellidos, '')) AS asesor
                      FROM financiamiento f
                      LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
                      LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
                      LEFT JOIN planes_financiamiento pf ON f.grupo_financiamiento = pf.idplan_financiamiento
                      LEFT JOIN usuarios u ON f.usuario_id = u.usuario_id
                      $where
                      ORDER BY f.fecha_creacion DESC";

            $stmt = $this->conexion->prepare($query);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            $financiamientos = [];
            while ($row = $result->fetch_assoc()) {
                $financiamientos[] = $row;
            }

            // Obtener nombre del asesor si hay filtro
            $nombreAsesor = 'Todos';
            if ($asesorId) {
                $stmtAsesor = $this->conexion->prepare("SELECT CONCAT(COALESCE(nombres,''), ' ', COALESCE(apellidos,'')) FROM usuarios WHERE usuario_id = ?");
                $stmtAsesor->bind_param('i', $asesorId);
                $stmtAsesor->execute();
                $stmtAsesor->bind_result($nombreAsesor);
                $stmtAsesor->fetch();
                $stmtAsesor->close();
                $nombreAsesor = trim($nombreAsesor);
            }

            // Generar Excel
            $this->generarExcel($financiamientos, $nombreAsesor, $searchTerm, $asesorId);

        } catch (Exception $e) {
            header('Content-Type: text/html; charset=utf-8');
            echo "Error al exportar: " . $e->getMessage();
            exit();
        }
    }

    private function generarExcel($financiamientos, $nombreAsesor, $searchTerm, $asesorId)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getProperties()
            ->setCreator("ArequipaGo")
            ->setTitle("Reporte de Financiamientos")
            ->setDescription("Reporte generado por ArequipaGo");

        // ENCABEZADO PRINCIPAL
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'REPORTE DE FINANCIAMIENTOS');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E217A']
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Info del reporte
        $infoTexto = 'Fecha: ' . date('d/m/Y H:i:s') . ' | Asesor: ' . $nombreAsesor . ' | Total: ' . count($financiamientos) . ' registros';
        if (!empty($searchTerm)) {
            $infoTexto .= ' | Busqueda: "' . $searchTerm . '"';
        }
        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', $infoTexto);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // ENCABEZADOS DE COLUMNAS (fila 4)
        $headers = [
            'A4' => 'ID',
            'B4' => 'Codigo Asociado',
            'C4' => 'Cliente',
            'D4' => 'Documento',
            'E4' => 'N Unidad',
            'F4' => 'Plan de Financiamiento',
            'G4' => 'Monto Total',
            'H4' => 'Cuotas',
            'I4' => 'Estado',
            'J4' => 'Fecha Creacion',
            'K4' => 'Moneda',
            'L4' => 'Asesor'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getStyle('A4:L4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F4A020']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        $sheet->getRowDimension(4)->setRowHeight(22);

        // DATOS
        $row = 5;
        $totalMonto = 0;

        foreach ($financiamientos as $f) {
            $fechaCreacion = !empty($f['fecha_creacion']) ? date('d/m/Y', strtotime($f['fecha_creacion'])) : 'N/A';
            $moneda = $f['moneda'] ?: 'S/.';
            $monto = (float)$f['monto_total'];

            $sheet->setCellValue('A' . $row, $f['idfinanciamiento']);
            $sheet->setCellValue('B' . $row, $f['codigo_asociado'] ?: 'N/A');
            $sheet->setCellValue('C' . $row, trim($f['cliente']) ?: 'N/A');
            $sheet->setCellValue('D' . $row, $f['documento'] ?: 'N/A');
            $sheet->setCellValue('E' . $row, $f['num_unidad'] ?: 'N/A');
            $sheet->setCellValue('F' . $row, $f['grupo_financiamiento'] ?: 'Sin Plan');
            $sheet->setCellValue('G' . $row, $moneda . ' ' . number_format($monto, 2, '.', ','));
            $sheet->setCellValue('H' . $row, $f['cuotas']);
            $sheet->setCellValue('I' . $row, $f['estado']);
            $sheet->setCellValue('J' . $row, $fechaCreacion);
            $sheet->setCellValue('K' . $row, $moneda);
            $sheet->setCellValue('L' . $row, trim($f['asesor']) ?: 'N/A');

            $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ]);

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $totalMonto += $monto;
            $row++;
        }

        // FILA DE TOTALES
        $row++;
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->setCellValue('A' . $row, 'TOTAL GENERAL');
        $sheet->setCellValue('G' . $row, 'S/. ' . number_format($totalMonto, 2, '.', ','));
        $sheet->setCellValue('H' . $row, count($financiamientos) . ' registros');

        $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E7E6E6']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Anchos de columna
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(16);
        $sheet->getColumnDimension('C')->setWidth(32);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(28);
        $sheet->getColumnDimension('G')->setWidth(16);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(16);
        $sheet->getColumnDimension('J')->setWidth(14);
        $sheet->getColumnDimension('K')->setWidth(10);
        $sheet->getColumnDimension('L')->setWidth(22);

        // Descargar
        $filename = 'Financiamientos_' . ($asesorId ? $nombreAsesor . '_' : '') . date('Ymd_His') . '.xlsx';
        $filename = str_replace(' ', '_', $filename);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}
