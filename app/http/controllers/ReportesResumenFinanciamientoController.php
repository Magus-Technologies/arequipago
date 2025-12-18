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

    /**
     * ✅ NUEVO: Exportar vehículos entregados a Excel
     */
    public function exportarVehiculosEntregados()
    {
        try {
            // Obtener datos de vehículos entregados
            $query = "SELECT 
                f.idfinanciamiento,
                f.id_conductor,
                f.id_cliente,
                f.monto_total,
                f.monto_sin_interes,
                f.fecha_entrega,
                f.fecha_creacion,
                f.estado,
                f.estado_entrega,
                f.moneda,
                p.idproductosv2,
                p.nombre AS producto_nombre,
                p.categoria AS producto_categoria,
                p.marca,
                p.modelo,
                p.precio_venta,
                CASE 
                    WHEN f.id_conductor IS NOT NULL THEN CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno)
                    WHEN f.id_cliente IS NOT NULL THEN CONCAT(cl.nombres, ' ', cl.apellido_paterno, ' ', cl.apellido_materno)
                    ELSE 'Sin nombre'
                END AS nombre_completo,
                CASE 
                    WHEN f.id_conductor IS NOT NULL THEN c.nro_documento
                    WHEN f.id_cliente IS NOT NULL THEN cl.n_documento
                    ELSE NULL
                END AS numero_documento,
                CASE 
                    WHEN f.id_conductor IS NOT NULL THEN 'Conductor'
                    WHEN f.id_cliente IS NOT NULL THEN 'Cliente'
                    ELSE 'Desconocido'
                END AS tipo_persona,
                c.numUnidad AS numero_unidad,
                pl.nombre_plan
            FROM financiamiento f
            LEFT JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
            LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
            LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
            LEFT JOIN planes_financiamiento pl ON f.grupo_financiamiento = pl.idplan_financiamiento
            WHERE f.estado_eliminado = 0
            AND f.estado_entrega = 'entregado'  -- ✅ SOLO vehículos con estado_entrega = 'entregado'
            AND f.idproductosv2 NOT IN (37, 312)  -- ✅ Excluir productos pendientes (37: Vehículo no entregado, 312: kia soluto pendiente)
            AND (
                -- Vehículos entregados con estado_entrega o por categoría
                f.estado_entrega = 'entregado'
                OR
                (
                    p.categoria IS NOT NULL 
                    AND (
                        LOWER(p.categoria) LIKE '%vehiculo%' 
                        OR LOWER(p.categoria) LIKE '%vehículo%'
                        OR LOWER(p.categoria) = 'moto lineal'
                        OR LOWER(p.categoria) LIKE '%moto%'
                    )
                )
            )
            ORDER BY f.fecha_entrega DESC, f.fecha_creacion DESC";

            $stmt = $this->conexion->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();

            $vehiculos = [];
            while ($row = $result->fetch_assoc()) {
                $vehiculos[] = $row;
            }

            // Generar Excel
            $this->generarExcelVehiculos($vehiculos);

        } catch (Exception $e) {
            header('Content-Type: text/html; charset=utf-8');
            echo "Error al exportar: " . $e->getMessage();
            exit();
        }
    }

    /**
     * ✅ NUEVO: Generar archivo Excel con los vehículos entregados
     */
    private function generarExcelVehiculos($vehiculos)
    {
        // Crear nuevo spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Configurar propiedades del documento
        $spreadsheet->getProperties()
            ->setCreator("ArequipaGo")
            ->setTitle("Vehículos Entregados")
            ->setSubject("Reporte de vehículos entregados")
            ->setDescription("Reporte generado por ArequipaGo");

        // ENCABEZADO PRINCIPAL
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'VEHÍCULOS ENTREGADOS');
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
                'startColor' => ['rgb' => '28A745'] // Verde
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Calcular totales
        $totalVehiculos = count($vehiculos);
        $montoTotalSoles = 0;
        $montoTotalDolares = 0;

        foreach ($vehiculos as $vehiculo) {
            $monto = floatval($vehiculo['monto_sin_interes'] ?? $vehiculo['monto_total']);
            if ($vehiculo['moneda'] === 'S/.') {
                $montoTotalSoles += $monto;
            } else {
                $montoTotalDolares += $monto;
            }
        }

        // Subtítulo con resumen
        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A2', 'Total: ' . $totalVehiculos . ' vehículos | S/. ' . number_format($montoTotalSoles, 2, '.', ',') . ' | $ ' . number_format($montoTotalDolares, 2, '.', ','));
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '5CB85C'] // Verde claro
            ]
        ]);
        $sheet->getRowDimension(2)->setRowHeight(25);

        // Información adicional
        $sheet->mergeCells('A3:K3');
        $sheet->setCellValue('A3', 'Fecha de generación: ' . date('d/m/Y H:i:s'));
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
            'A5' => 'ID',
            'B5' => 'Tipo',
            'C5' => 'Nombre Completo',
            'D5' => 'Documento',
            'E5' => 'Nº Unidad',
            'F5' => 'Vehículo',
            'G5' => 'Marca',
            'H5' => 'Modelo',
            'I5' => 'Monto',
            'J5' => 'Plan',
            'K5' => 'Fecha Entrega'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Estilo de encabezados
        $sheet->getStyle('A5:K5')->applyFromArray([
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
                'startColor' => ['rgb' => '28A745'] // Verde
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
        foreach ($vehiculos as $vehiculo) {
            // Formatear fecha de entrega
            $fechaEntrega = 'Sin fecha';
            if (!empty($vehiculo['fecha_entrega'])) {
                $fechaEntrega = date('d/m/Y', strtotime($vehiculo['fecha_entrega']));
            }

            // Monto
            $monto = floatval($vehiculo['monto_sin_interes'] ?? $vehiculo['monto_total']);
            $moneda = $vehiculo['moneda'] ?: 'S/.';
            $montoFormateado = $moneda . ' ' . number_format($monto, 2, '.', ',');

            // Datos de la fila
            $sheet->setCellValue('A' . $row, $vehiculo['idfinanciamiento']);
            $sheet->setCellValue('B' . $row, $vehiculo['tipo_persona']);
            $sheet->setCellValue('C' . $row, $vehiculo['nombre_completo']);
            $sheet->setCellValue('D' . $row, $vehiculo['numero_documento'] ?: 'N/A');
            $sheet->setCellValue('E' . $row, $vehiculo['numero_unidad'] ?: 'N/A');
            $sheet->setCellValue('F' . $row, $vehiculo['producto_nombre'] ?: 'N/A');
            $sheet->setCellValue('G' . $row, $vehiculo['marca'] ?: 'N/A');
            $sheet->setCellValue('H' . $row, $vehiculo['modelo'] ?: 'N/A');
            $sheet->setCellValue('I' . $row, $montoFormateado);
            $sheet->setCellValue('J' . $row, $vehiculo['nombre_plan'] ?: 'N/A');
            $sheet->setCellValue('K' . $row, $fechaEntrega);

            // Estilo de datos
            $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
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
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Color de fondo alternado
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8F9FA']
                    ]
                ]);
            }

            $row++;
        }

        // FILA DE TOTALES
        $row++;
        $sheet->mergeCells('A' . $row . ':H' . $row);
        $sheet->setCellValue('A' . $row, 'TOTALES');
        $sheet->setCellValue('I' . $row, 'S/. ' . number_format($montoTotalSoles, 2, '.', ',') . ' | $ ' . number_format($montoTotalDolares, 2, '.', ','));

        $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '28A745']
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
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(25);
        $sheet->getColumnDimension('K')->setWidth(15);

        // Configurar salida
        $filename = 'Vehiculos_Entregados_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}
