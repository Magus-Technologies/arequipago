<?php

require_once 'utils/lib/exel/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ExportContratosFinalizadosController extends Controller
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    /**
     * Exportar contratos finalizados a Excel
     */
    public function exportarContratosFinalizados()
    {
        try {
            // Verificar permisos (solo directores)
            if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 3) {
                echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
                return;
            }

            // Obtener parámetro de búsqueda si existe
            $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

            // Construir query
            $queryBase = "FROM financiamiento f
                         LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
                         LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
                         LEFT JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                         LEFT JOIN planes_financiamiento pf ON f.grupo_financiamiento = pf.idplan_financiamiento
                         LEFT JOIN usuarios u ON f.usuario_finalizo_contrato = u.usuario_id
                         WHERE f.contrato_finalizado = 1 AND f.estado_eliminado = 0";

            // Agregar condiciones de búsqueda si existe
            if (!empty($busqueda)) {
                $queryBase .= " AND (
                    c.nombres LIKE ? OR
                    c.apellido_paterno LIKE ? OR
                    c.apellido_materno LIKE ? OR
                    c.nro_documento LIKE ? OR
                    cl.nombres LIKE ? OR
                    cl.apellido_paterno LIKE ? OR
                    cl.apellido_materno LIKE ? OR
                    cl.n_documento LIKE ? OR
                    f.codigo_asociado LIKE ? OR
                    CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) LIKE ? OR
                    CONCAT(cl.nombres, ' ', cl.apellido_paterno, ' ', cl.apellido_materno) LIKE ?
                )";
            }

            // Query completo
            $query = "SELECT
                f.idfinanciamiento,
                f.codigo_asociado,
                f.monto_total,
                f.moneda,
                f.fecha_creacion,
                f.fecha_finalizacion_contrato,
                COALESCE(c.nro_documento, cl.n_documento) as documento,
                CONCAT(COALESCE(c.nombres, cl.nombres), ' ',
                       COALESCE(c.apellido_paterno, cl.apellido_paterno), ' ',
                       COALESCE(c.apellido_materno, cl.apellido_materno)) as nombre_completo,
                p.nombre as producto_nombre,
                pf.nombre_plan,
                TRIM(CONCAT(IFNULL(u.nombres, ''), ' ', IFNULL(u.apellidos, ''))) as usuario_finalizo
                " . $queryBase . "
                ORDER BY f.fecha_finalizacion_contrato DESC";

            $stmt = $this->conexion->prepare($query);

            if (!empty($busqueda)) {
                $busquedaParam = "%{$busqueda}%";
                $stmt->bind_param("sssssssssss",
                    $busquedaParam, $busquedaParam, $busquedaParam, $busquedaParam,
                    $busquedaParam, $busquedaParam, $busquedaParam, $busquedaParam,
                    $busquedaParam, $busquedaParam, $busquedaParam
                );
            }

            $stmt->execute();
            $result = $stmt->get_result();

            $contratos = [];
            while ($row = $result->fetch_assoc()) {
                $contratos[] = $row;
            }

            // Generar Excel
            $this->generarExcel($contratos, $busqueda);

        } catch (Exception $e) {
            header('Content-Type: text/html; charset=utf-8');
            echo "Error al exportar: " . $e->getMessage();
            exit();
        }
    }

    /**
     * Generar archivo Excel con los datos
     */
    private function generarExcel($contratos, $busqueda)
    {
        // Crear nuevo spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Configurar propiedades del documento
        $spreadsheet->getProperties()
            ->setCreator("ArequipaGo")
            ->setTitle("Contratos Finalizados")
            ->setSubject("Listado de contratos finalizados")
            ->setDescription("Reporte generado por ArequipaGo");

        // ENCABEZADO PRINCIPAL
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'CONTRATOS FINALIZADOS');
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
                'startColor' => ['rgb' => '28a745'] // Verde
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Información adicional
        $infoTexto = 'Fecha de generación: ' . date('d/m/Y H:i:s') . ' | Total de registros: ' . count($contratos);
        if (!empty($busqueda)) {
            $infoTexto .= ' | Búsqueda: "' . $busqueda . '"';
        }

        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', $infoTexto);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 10
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);

        // ENCABEZADOS DE COLUMNAS (fila 4)
        $headers = [
            'A4' => 'ID',
            'B4' => 'Cliente',
            'C4' => 'DNI',
            'D4' => 'Producto',
            'E4' => 'Plan',
            'F4' => 'Código Asociado',
            'G4' => 'Monto Total',
            'H4' => 'Fecha Creación',
            'I4' => 'Fecha Finalización',
            'J4' => 'Finalizado Por'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Estilo de encabezados
        $sheet->getStyle('A4:J4')->applyFromArray([
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
                'startColor' => ['rgb' => '28a745'] // Verde
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        $sheet->getRowDimension(4)->setRowHeight(20);

        // DATOS
        $row = 5;
        $totalGeneral = 0;

        foreach ($contratos as $contrato) {
            // Formatear fechas
            $fechaCreacion = 'N/A';
            if (!empty($contrato['fecha_creacion'])) {
                $fechaCreacion = date('d/m/Y', strtotime($contrato['fecha_creacion']));
            }

            $fechaFinalizacion = 'N/A';
            if (!empty($contrato['fecha_finalizacion_contrato'])) {
                $fechaFinalizacion = date('d/m/Y H:i', strtotime($contrato['fecha_finalizacion_contrato']));
            }

            // Datos de la fila
            $sheet->setCellValue('A' . $row, $contrato['idfinanciamiento']);
            $sheet->setCellValue('B' . $row, $contrato['nombre_completo'] ?: 'N/A');
            $sheet->setCellValue('C' . $row, $contrato['documento'] ?: 'N/A');
            $sheet->setCellValue('D' . $row, $contrato['producto_nombre'] ?: 'N/A');
            $sheet->setCellValue('E' . $row, $contrato['nombre_plan'] ?: 'N/A');
            $sheet->setCellValue('F' . $row, $contrato['codigo_asociado'] ?: 'N/A');

            // Monto con moneda
            $moneda = $contrato['moneda'] ?: 'S/.';
            $monto = number_format((float)$contrato['monto_total'], 2, '.', ',');
            $sheet->setCellValue('G' . $row, $moneda . ' ' . $monto);

            $sheet->setCellValue('H' . $row, $fechaCreacion);
            $sheet->setCellValue('I' . $row, $fechaFinalizacion);
            $sheet->setCellValue('J' . $row, $contrato['usuario_finalizo'] ?: 'N/A');

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
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Acumular total
            $totalGeneral += (float)$contrato['monto_total'];

            $row++;
        }

        // FILA DE TOTALES
        $row++;
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->setCellValue('A' . $row, 'TOTAL GENERAL');
        $sheet->setCellValue('G' . $row, 'S/. ' . number_format($totalGeneral, 2, '.', ','));

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
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(14);
        $sheet->getColumnDimension('I')->setWidth(18);
        $sheet->getColumnDimension('J')->setWidth(20);

        // Configurar salida
        $filename = 'Contratos_Finalizados_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}
