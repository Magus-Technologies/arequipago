<?php

require_once 'app/models/Constatacion.php';
require_once 'utils/lib/exel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ConstatacionesController extends Controller
{
    private $constatacionModel;

    public function __construct()
    {
        $this->constatacionModel = new Constatacion();
    }

    /**
     * Verificar permisos de acceso (rol 1, 2 y 3)
     */
    private function verificarPermisos()
    {
        $rol = $_SESSION['id_rol'] ?? null;
        return in_array($rol, [1, 2, 3]);
    }

    /**
     * Obtener lista de vehículos entregados (pendientes y realizados)
     */
    public function listar()
    {
        if (!$this->verificarPermisos()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para acceder']);
            return;
        }

        try {
            $registros = $this->constatacionModel->obtenerPendientes();

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $registros
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener los registros: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener contador para dashboard
     */
    public function contador()
    {
        if (!$this->verificarPermisos()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No tiene permisos']);
            return;
        }

        try {
            $pendientes = $this->constatacionModel->contarPendientes();
            $total = $this->constatacionModel->contarTotalEntregados();
            $realizadas = $this->constatacionModel->contarRealizadas();

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'total' => $total,
                'pendientes' => $pendientes,
                'realizadas' => $realizadas
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener contadores: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Guardar nueva constatación
     */
    public function guardar()
    {
        if (!$this->verificarPermisos()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No tiene permisos']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        try {
            // Validar datos requeridos
            $id_financiamiento = $_POST['id_financiamiento'] ?? null;
            $departamento = $_POST['departamento'] ?? null;
            $provincia = $_POST['provincia'] ?? null;
            $distrito = $_POST['distrito'] ?? null;
            $direccion = $_POST['direccion'] ?? null;

            if (!$id_financiamiento || !$departamento || !$provincia || !$distrito || !$direccion) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos (departamento, provincia, distrito, dirección)']);
                return;
            }

            // Procesar foto obligatoria
            if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Debe subir una foto del domicilio']);
                return;
            }

            $foto = $_FILES['foto'];

            // Validar tamaño (5MB máximo)
            if ($foto['size'] > 5 * 1024 * 1024) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'La foto excede el tamaño máximo de 5MB']);
                return;
            }

            // Validar tipo de archivo
            $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $tipoReal = finfo_file($finfo, $foto['tmp_name']);
            finfo_close($finfo);

            if (!in_array($tipoReal, $tiposPermitidos)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Formato de imagen no permitido. Use JPG o PNG']);
                return;
            }

            // Crear directorio si no existe
            $directorio = 'storage/constataciones/';
            if (!file_exists($directorio)) {
                mkdir($directorio, 0755, true);
            }

            // Generar nombre único para foto
            $extension = pathinfo($foto['name'], PATHINFO_EXTENSION);
            $nombreArchivo = time() . '_' . md5(uniqid()) . '.' . $extension;
            $rutaCompleta = $directorio . $nombreArchivo;

            // Mover archivo
            if (!move_uploaded_file($foto['tmp_name'], $rutaCompleta)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Error al guardar la foto']);
                return;
            }

            // Procesar captura de Google Maps (opcional)
            $capturaGoogleMaps = null;
            if (isset($_FILES['captura_google_maps']) && $_FILES['captura_google_maps']['error'] === UPLOAD_ERR_OK) {
                $captura = $_FILES['captura_google_maps'];

                // Validar tamaño
                if ($captura['size'] <= 5 * 1024 * 1024) {
                    $finfoCaptura = finfo_open(FILEINFO_MIME_TYPE);
                    $tipoCaptura = finfo_file($finfoCaptura, $captura['tmp_name']);
                    finfo_close($finfoCaptura);

                    if (in_array($tipoCaptura, $tiposPermitidos)) {
                        $extCaptura = pathinfo($captura['name'], PATHINFO_EXTENSION);
                        $nombreCaptura = time() . '_maps_' . md5(uniqid()) . '.' . $extCaptura;
                        $rutaCaptura = $directorio . $nombreCaptura;

                        if (move_uploaded_file($captura['tmp_name'], $rutaCaptura)) {
                            $capturaGoogleMaps = $rutaCaptura;
                        }
                    }
                }
            }

            // Obtener info del financiamiento
            $infoFinanciamiento = $this->constatacionModel->obtenerInfoFinanciamiento($id_financiamiento);

            if (!$infoFinanciamiento) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Financiamiento no encontrado']);
                return;
            }

            // Determinar tipo_usuario e id_tipo_usuario
            $tipo_usuario = $infoFinanciamiento['id_conductor'] ? 1 : 2;
            $id_tipo_usuario = $infoFinanciamiento['id_conductor'] ?: $infoFinanciamiento['id_cliente'];

            // Preparar datos para guardar
            $datos = [
                'id_financiamiento' => $id_financiamiento,
                'tipo_usuario' => $tipo_usuario,
                'id_tipo_usuario' => $id_tipo_usuario,
                'foto_domicilio' => $rutaCompleta,
                'departamento' => $departamento,
                'provincia' => $provincia,
                'distrito' => $distrito,
                'direccion' => $direccion,
                'link_google_maps' => $_POST['link_google_maps'] ?? null,
                'captura_google_maps' => $capturaGoogleMaps,
                'latitud' => !empty($_POST['latitud']) ? floatval($_POST['latitud']) : null,
                'longitud' => !empty($_POST['longitud']) ? floatval($_POST['longitud']) : null,
                'observaciones' => $_POST['observaciones'] ?? null,
                'usuario_id' => $_SESSION['usuario_id']
            ];

            $resultado = $this->constatacionModel->guardar($datos);

            header('Content-Type: application/json');
            echo json_encode($resultado);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al procesar la constatación: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener detalle de una constatación
     */
    public function detalle()
    {
        if (!$this->verificarPermisos()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No tiene permisos']);
            return;
        }

        $id_constatacion = $_GET['id'] ?? null;

        if (!$id_constatacion) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            return;
        }

        try {
            $detalle = $this->constatacionModel->obtenerDetalle($id_constatacion);

            if (!$detalle) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Constatación no encontrada']);
                return;
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $detalle
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener detalle: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Servir foto protegida
     */
    public function servirFoto()
    {
        if (!$this->verificarPermisos()) {
            http_response_code(403);
            exit('No autorizado');
        }

        $id_constatacion = $_GET['id'] ?? null;

        if (!$id_constatacion) {
            http_response_code(400);
            exit('ID no proporcionado');
        }

        $detalle = $this->constatacionModel->obtenerDetalle($id_constatacion);

        if (!$detalle || !$detalle['foto_domicilio']) {
            http_response_code(404);
            exit('Foto no encontrada');
        }

        $rutaFoto = $detalle['foto_domicilio'];

        if (!file_exists($rutaFoto)) {
            http_response_code(404);
            exit('Archivo no encontrado');
        }

        // Determinar tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $rutaFoto);
        finfo_close($finfo);

        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($rutaFoto));
        readfile($rutaFoto);
        exit;
    }

    /**
     * Generar PDF de constatación (PÚBLICO - accesible sin login)
     */
    public function generarPDF()
    {
        // ELIMINADO: Validación de permisos para permitir acceso público desde WhatsApp
        // if (!$this->verificarPermisos()) {
        //     http_response_code(403);
        //     exit('No autorizado');
        // }

        $id_constatacion = $_GET['id'] ?? null;

        if (!$id_constatacion) {
            http_response_code(400);
            exit('ID no proporcionado');
        }

        try {
            $detalle = $this->constatacionModel->obtenerDetalle($id_constatacion);

            if (!$detalle) {
                http_response_code(404);
                exit('Constatación no encontrada');
            }

            // Inicializar mPDF
            require_once 'utils/lib/mpdf/vendor/autoload.php';
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 15,
                'margin_bottom' => 15
            ]);

            // Formatear fecha
            $fecha = date('d/m/Y H:i', strtotime($detalle['fecha_constatacion']));

            // Preparar ruta de la foto
            $fotoPath = file_exists($detalle['foto_domicilio']) ? $detalle['foto_domicilio'] : '';

            // Preparar imagen de captura de Google Maps si existe
            $capturaHTML = '';
            if ($detalle['captura_google_maps'] && file_exists($detalle['captura_google_maps'])) {
                $capturaPath = $detalle['captura_google_maps'];
                $capturaHTML = "
                    <div style='page-break-inside: avoid;'>
                        <div class='section-title'>CAPTURA DE GOOGLE MAPS</div>
                        <div style='text-align: center; margin-top: 10px;'>
                            <img src='$capturaPath' style='max-width: 100%; max-height: 400px; height: auto;'>
                        </div>
                    </div>
                ";
            }

            // Obtener documento del cliente (DNI/RUC)
            $conexion = (new Conexion())->getConexion();
            $documento = '';
            if ($detalle['tipo_usuario'] == 1) {
                // Es conductor
                $queryDoc = "SELECT nro_documento FROM conductores WHERE id_conductor = {$detalle['id_tipo_usuario']}";
            } else {
                // Es cliente
                $queryDoc = "SELECT n_documento as nro_documento FROM clientes_financiar WHERE id = {$detalle['id_tipo_usuario']}";
            }
            $resultDoc = $conexion->query($queryDoc);
            if ($resultDoc && $row = $resultDoc->fetch_assoc()) {
                $documento = $row['nro_documento'];
            }

            // HTML del PDF
            $html = "
            <style>
                body { font-family: Arial, sans-serif; }
                .header { text-align: center; margin-bottom: 25px; }
                .section-title { 
                    color: #2e217a; 
                    border-bottom: 1px solid #2e217a;
                    padding: 5px 0; 
                    margin-top: 20px; 
                    margin-bottom: 15px;
                    font-size: 13px;
                    font-weight: bold;
                }
                .info-row {
                    margin-bottom: 10px;
                    overflow: hidden;
                }
                .info-col {
                    width: 48%;
                    float: left;
                    margin-right: 2%;
                }
                .info-col:nth-child(2n) {
                    margin-right: 0;
                }
                .info-col-3 {
                    width: 32%;
                    float: left;
                    margin-right: 2%;
                }
                .info-col-3:nth-child(3n) {
                    margin-right: 0;
                }
                .info-label {
                    font-weight: bold;
                    color: #2e217a;
                    font-size: 10px;
                }
                .info-value {
                    color: #333;
                    font-size: 11px;
                    margin-top: 2px;
                    padding: 5px;
                }
                .obs-box {
                    padding: 10px;
                    min-height: 50px;
                    font-size: 11px;
                    background-color: #f9f9f9;
                }
                .footer {
                    margin-top: 20px;
                    text-align: center;
                    font-size: 9px;
                    color: #666;
                    border-top: 1px solid #ddd;
                    padding-top: 10px;
                }
            </style>

            <div class='header'>
                <h1 style='color: #2e217a; margin: 0; font-size: 18px; border: 2px solid #2e217a; padding: 10px;'>CONSTATACIÓN DOMICILIARIA N° " . str_pad($id_constatacion, 3, '0', STR_PAD_LEFT) . '/' . date('m/Y', strtotime($detalle['fecha_constatacion'])) . "</h1>
            </div>

            <div class='section-title'>INFORMACIÓN DEL CLIENTE</div>
            <div class='info-row'>
                <div class='info-col'>
                    <div class='info-label'>N° Documento:</div>
                    <div class='info-value'>$documento</div>
                </div>
                <div class='info-col'>
                    <div class='info-label'>Nombre Cliente:</div>
                    <div class='info-value'>{$detalle['cliente_nombre']}</div>
                </div>
            </div>
            <div class='info-row'>
                <div class='info-col'>
                    <div class='info-label'>Producto/Vehículo:</div>
                    <div class='info-value'>{$detalle['producto_nombre']}</div>
                </div>
                <div class='info-col'>
                    <div class='info-label'>Fecha de Constatación:</div>
                    <div class='info-value'>$fecha</div>
                </div>
            </div>
            <div class='info-row'>
                <div class='info-col'>
                    <div class='info-label'>Realizado por:</div>
                    <div class='info-value'>{$detalle['usuario_nombre']}</div>
                </div>
            </div>

            <div class='section-title'>UBICACIÓN DEL DOMICILIO</div>
            <div class='info-row'>
                <div class='info-col-3'>
                    <div class='info-label'>Departamento:</div>
                    <div class='info-value'>{$detalle['departamento']}</div>
                </div>
                <div class='info-col-3'>
                    <div class='info-label'>Provincia:</div>
                    <div class='info-value'>{$detalle['provincia']}</div>
                </div>
                <div class='info-col-3'>
                    <div class='info-label'>Distrito:</div>
                    <div class='info-value'>{$detalle['distrito']}</div>
                </div>
            </div>
            <div class='info-row'>
                <div class='info-col'>
                    <div class='info-label'>Dirección Completa:</div>
                    <div class='info-value'>{$detalle['direccion']}</div>
                </div>
                " . ($detalle['link_google_maps'] ? "
                <div class='info-col'>
                    <div class='info-label'>Link Google Maps:</div>
                    <div class='info-value' style='word-break: break-all;'><a href='{$detalle['link_google_maps']}' style='color: #007bff;'>{$detalle['link_google_maps']}</a></div>
                </div>
                " : '') . "
            </div>

            <div class='section-title'>OBSERVACIONES</div>
            <div class='obs-box'>
                " . ($detalle['observaciones'] ?: 'Sin observaciones') . "
            </div>

            <div style='page-break-inside: avoid;'>
                <div class='section-title'>FOTO DEL DOMICILIO</div>
                <div style='text-align: center; margin-top: 10px;'>
                    <img src='$fotoPath' style='max-width: 100%; max-height: 300px; height: auto;'>
                </div>
            </div>

            $capturaHTML

            <div class='footer'>
                <p style='margin: 5px 0;'>Este documento certifica la verificación domiciliaria realizada.</p>
                <p style='margin: 5px 0;'>Generado el " . date('d/m/Y H:i:s') . '</p>
            </div>
            ';

            $mpdf->WriteHTML($html);

            // Nombre del archivo
            $nombreArchivo = 'Constatacion_' . $id_constatacion . '_' . date('Ymd') . '.pdf';

            $mpdf->Output($nombreArchivo, 'I');  // I = Inline (mostrar en navegador)
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            exit('Error al generar PDF: ' . $e->getMessage());
        }
    }

    /**
     * Obtener información de financiamiento para el modal
     */
    public function infoFinanciamiento()
    {
        if (!$this->verificarPermisos()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No tiene permisos']);
            return;
        }

        $id_financiamiento = $_GET['id'] ?? null;

        if (!$id_financiamiento) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            return;
        }

        try {
            $info = $this->constatacionModel->obtenerInfoFinanciamiento($id_financiamiento);

            if (!$info) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Financiamiento no encontrado']);
                return;
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $info
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Eliminar constatación
     */
    public function eliminar()
    {
        if (!$this->verificarPermisos()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No tiene permisos']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $id_constatacion = $_POST['id'] ?? null;

        if (!$id_constatacion) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            return;
        }

        try {
            // Obtener detalle para eliminar archivos
            $detalle = $this->constatacionModel->obtenerDetalle($id_constatacion);

            if (!$detalle) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Constatación no encontrada']);
                return;
            }

            // Eliminar archivos físicos
            if ($detalle['foto_domicilio'] && file_exists($detalle['foto_domicilio'])) {
                unlink($detalle['foto_domicilio']);
            }

            if ($detalle['captura_google_maps'] && file_exists($detalle['captura_google_maps'])) {
                unlink($detalle['captura_google_maps']);
            }

            // Eliminar registro de la base de datos
            $resultado = $this->constatacionModel->eliminar($id_constatacion);

            header('Content-Type: application/json');
            echo json_encode($resultado);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Exportar reporte de constataciones a Excel
     */
    public function exportarExcel()
    {
        if (!$this->verificarPermisos()) {
            http_response_code(403);
            exit('No autorizado');
        }

        try {
            // Obtener todos los datos de constataciones
            $constataciones = $this->constatacionModel->obtenerTodosParaReporte();

            if (empty($constataciones)) {
                header('Content-Type: text/html; charset=utf-8');
                echo "No hay constataciones registradas para generar el reporte.";
                exit();
            }

            // Generar Excel
            $this->generarExcel($constataciones);

        } catch (Exception $e) {
            header('Content-Type: text/html; charset=utf-8');
            echo "Error al exportar: " . $e->getMessage();
            exit();
        }
    }

    /**
     * Generar archivo Excel con los datos de constataciones
     */
    private function generarExcel($constataciones)
    {
        // Crear nuevo spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Configurar propiedades del documento
        $spreadsheet->getProperties()
            ->setCreator("ArequipaGo")
            ->setTitle("Reporte de Constataciones Domiciliarias")
            ->setSubject("Constataciones Domiciliarias")
            ->setDescription("Reporte generado por ArequipaGo");

        // ENCABEZADO PRINCIPAL
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'REPORTE DE CONSTATACIONES DOMICILIARIAS');
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
                'startColor' => ['rgb' => '667EEA']
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Subtítulo con información
        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', 'Fecha de generación: ' . date('d/m/Y H:i:s') . ' | Total de registros: ' . count($constataciones));
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '764BA2']
            ]
        ]);
        $sheet->getRowDimension(2)->setRowHeight(25);

        // ENCABEZADOS DE COLUMNAS (fila 4)
        $headers = [
            'A4' => 'ID',
            'B4' => 'Fecha Constatación',
            'C4' => 'Tipo',
            'D4' => 'Cliente',
            'E4' => 'Documento',
            'F4' => 'Teléfono',
            'G4' => 'Nº Unidad',
            'H4' => 'Producto/Vehículo',
            'I4' => 'Departamento',
            'J4' => 'Provincia',
            'K4' => 'Distrito',
            'L4' => 'Dirección'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Estilo de encabezados
        $sheet->getStyle('A4:L4')->applyFromArray([
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
                'startColor' => ['rgb' => 'FFC107']
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
        foreach ($constataciones as $constatacion) {
            // Formatear fecha de constatación
            $fechaConstatacion = 'N/A';
            if (!empty($constatacion['fecha_constatacion'])) {
                $fechaConstatacion = date('d/m/Y H:i', strtotime($constatacion['fecha_constatacion']));
            }

            // Datos de la fila
            $sheet->setCellValue('A' . $row, $constatacion['id_constatacion']);
            $sheet->setCellValue('B' . $row, $fechaConstatacion);
            $sheet->setCellValue('C' . $row, $constatacion['tipo_persona']);
            $sheet->setCellValue('D' . $row, $constatacion['cliente_nombre']);
            $sheet->setCellValue('E' . $row, $constatacion['documento'] ?: 'N/A');
            $sheet->setCellValue('F' . $row, $constatacion['telefono'] ?: 'N/A');
            $sheet->setCellValue('G' . $row, $constatacion['numero_unidad'] ?: 'N/A');
            $sheet->setCellValue('H' . $row, $constatacion['producto_nombre'] ?: 'N/A');
            $sheet->setCellValue('I' . $row, $constatacion['departamento'] ?: 'N/A');
            $sheet->setCellValue('J' . $row, $constatacion['provincia'] ?: 'N/A');
            $sheet->setCellValue('K' . $row, $constatacion['distrito'] ?: 'N/A');
            $sheet->setCellValue('L' . $row, $constatacion['direccion'] ?: 'N/A');

            // Estilo de datos
            $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
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
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Color de fondo alternado
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

        // FILA DE TOTALES
        $row++;
        $sheet->mergeCells('A' . $row . ':K' . $row);
        $sheet->setCellValue('A' . $row, 'TOTAL DE CONSTATACIONES');
        $sheet->setCellValue('L' . $row, count($constataciones));

        $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
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
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(15);
        $sheet->getColumnDimension('L')->setWidth(40);

        // Configurar salida
        $filename = 'Constataciones_Domiciliarias_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}
