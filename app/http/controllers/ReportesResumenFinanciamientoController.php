<?php

require_once 'utils/lib/exel/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ReportesResumenFinanciamientoController extends Controller
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    /**
     * Exportar detalle de un plan de financiamiento a Excel
     */
    public function exportarResumenFinanciamientoPlan()
    {
        try {
            // Verificar permisos (solo directores)
            if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 3) {
                echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
                return;
            }

            $planId = $_GET['planId'] ?? null;
            $nombrePlan = $_GET['nombrePlan'] ?? 'Plan de Financiamiento';

            if (!$planId) {
                echo json_encode(['success' => false, 'error' => 'ID de plan requerido']);
                return;
            }

            // Obtener datos del plan
            $query = "
                SELECT
                    COALESCE(c.nombres, cl.nombres) as nombres,
                    COALESCE(c.apellido_paterno, cl.apellido_paterno) as apellido_paterno,
                    COALESCE(c.apellido_materno, cl.apellido_materno) as apellido_materno,
                    COALESCE(c.nro_documento, cl.n_documento) as nro_documento,
                    c.numUnidad as numero_unidad,
                    p.nombre as producto_nombre,
                    COALESCE(NULLIF(f.cantidad_producto, ''), '1') as cantidad_unidades,
                    f.monto_total,
                    f.estado,
                    DATE(f.fecha_creacion) as fecha_registro,
                    f.moneda,
                    CASE
                        WHEN f.id_conductor IS NOT NULL AND f.id_conductor != 0 THEN 'Conductor'
                        WHEN f.id_cliente IS NOT NULL AND f.id_cliente != 0 THEN 'Cliente'
                        ELSE 'Desconocido'
                    END as tipo_persona
                FROM financiamiento f
                LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
                LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
                LEFT JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                WHERE CAST(f.grupo_financiamiento AS UNSIGNED) = ?
                AND f.estado != 'eliminado'
                AND f.estado_eliminado = 0
                AND f.grupo_financiamiento REGEXP '^[0-9]+$'
                ORDER BY COALESCE(c.nombres, cl.nombres) ASC
            ";

            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $planId);
            $stmt->execute();
            $result = $stmt->get_result();

            $detalles = [];
            while ($row = $result->fetch_assoc()) {
                $detalles[] = $row;
            }

            // Generar Excel
            $this->generarExcel($detalles, $nombrePlan);

        } catch (Exception $e) {
            header('Content-Type: text/html; charset=utf-8');
            echo "Error al exportar: " . $e->getMessage();
            exit();
        }
    }

    /**
     * Generar archivo Excel con los datos
     */
    private function generarExcel($detalles, $nombrePlan)
    {
        // Crear nuevo spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Configurar propiedades del documento
        $spreadsheet->getProperties()
            ->setCreator("ArequipaGo")
            ->setTitle("Resumen de Financiamientos - " . $nombrePlan)
            ->setSubject("Detalle de financiamientos")
            ->setDescription("Reporte generado por ArequipaGo");

        // ENCABEZADO PRINCIPAL
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'RESUMEN DE FINANCIAMIENTOS');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Subtítulo con nombre del plan
        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', $nombrePlan);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '5B9BD5']
            ]
        ]);
        $sheet->getRowDimension(2)->setRowHeight(25);

        // Información adicional
        $sheet->mergeCells('A3:J3');
        $sheet->setCellValue('A3', 'Fecha de generación: ' . date('d/m/Y H:i:s') . ' | Total de registros: ' . count($detalles));
        $sheet->getStyle('A3')->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 10
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);

        // ENCABEZADOS DE COLUMNAS (fila 5)
        $headers = [
            'A5' => 'Tipo',
            'B5' => 'Nombres',
            'C5' => 'Apellido Paterno',
            'D5' => 'Apellido Materno',
            'E5' => 'Documento',
            'F5' => 'Nº Unidad',
            'G5' => 'Producto',
            'H5' => 'Cantidad',
            'I5' => 'Monto Total',
            'J5' => 'Fecha'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Estilo de encabezados
        $sheet->getStyle('A5:J5')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '70AD47']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        $sheet->getRowDimension(5)->setRowHeight(20);

        // DATOS
        $row = 6;
        $totalGeneral = 0;
        $totalUnidades = 0;

        foreach ($detalles as $detalle) {
            // Formatear fecha
            $fecha = 'N/A';
            if (!empty($detalle['fecha_registro'])) {
                $fecha = date('d/m/Y', strtotime($detalle['fecha_registro']));
            }

            // Datos de la fila
            $sheet->setCellValue('A' . $row, $detalle['tipo_persona']);
            $sheet->setCellValue('B' . $row, $detalle['nombres']);
            $sheet->setCellValue('C' . $row, $detalle['apellido_paterno']);
            $sheet->setCellValue('D' . $row, $detalle['apellido_materno']);
            $sheet->setCellValue('E' . $row, $detalle['nro_documento']);
            $sheet->setCellValue('F' . $row, $detalle['numero_unidad'] ?: 'N/A');
            $sheet->setCellValue('G' . $row, $detalle['producto_nombre'] ?: 'N/A');
            $sheet->setCellValue('H' . $row, $detalle['cantidad_unidades']);

            // Monto con moneda
            $moneda = $detalle['moneda'] ?: 'S/.';
            $monto = number_format((float)$detalle['monto_total'], 2, '.', ',');
            $sheet->setCellValue('I' . $row, $moneda . ' ' . $monto);

            $sheet->setCellValue('J' . $row, $fecha);

            // Estilo de datos
            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);

            // Alineación específica
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Acumular totales
            $totalGeneral += (float)$detalle['monto_total'];
            $totalUnidades += (int)$detalle['cantidad_unidades'];

            $row++;
        }

        // FILA DE TOTALES
        $row++;
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->setCellValue('A' . $row, 'TOTALES');
        $sheet->setCellValue('H' . $row, $totalUnidades);
        $sheet->setCellValue('I' . $row, 'S/. ' . number_format($totalGeneral, 2, '.', ','));

        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12
            ],
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

        // Ajustar anchos de columna
        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(12);

        // Configurar salida
        $filename = 'Resumen_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $nombrePlan) . '_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}
