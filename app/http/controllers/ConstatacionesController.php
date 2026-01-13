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
            // Obtener datos de constataciones verificadas y pendientes
            $verificadas = $this->constatacionModel->obtenerTodosParaReporte();
            $pendientes = $this->constatacionModel->obtenerPendientesParaReporte();

            if (empty($verificadas) && empty($pendientes)) {
                header('Content-Type: text/html; charset=utf-8');
                echo "No hay registros para generar el reporte.";
                exit();
            }

            // Generar Excel con dos hojas
            $this->generarExcel($verificadas, $pendientes);

        } catch (Exception $e) {
            header('Content-Type: text/html; charset=utf-8');
            echo "Error al exportar: " . $e->getMessage();
            exit();
        }
    }

    /**
     * Generar archivo Excel con los datos de constataciones
     */
    private function generarExcel($verificadas, $pendientes)
    {
        // Crear nuevo spreadsheet
        $spreadsheet = new Spreadsheet();

        // Configurar propiedades del documento
        $spreadsheet->getProperties()
            ->setCreator("ArequipaGo")
            ->setTitle("Reporte de Constataciones Domiciliarias")
            ->setSubject("Constataciones Domiciliarias")
            ->setDescription("Reporte generado por ArequipaGo");

        // ==================== HOJA 1: VERIFICADAS ====================
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Verificadas');

        // ENCABEZADO PRINCIPAL
        $sheet1->mergeCells('A1:L1');
        $sheet1->setCellValue('A1', 'CONSTATACIONES VERIFICADAS');
        $sheet1->getStyle('A1')->applyFromArray([
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
                'startColor' => ['rgb' => '28A745']
            ]
        ]);
        $sheet1->getRowDimension(1)->setRowHeight(30);

        // Subtítulo
        $sheet1->mergeCells('A2:L2');
        $sheet1->setCellValue('A2', 'Fecha de generación: ' . date('d/m/Y H:i:s') . ' | Total: ' . count($verificadas));
        $sheet1->getStyle('A2')->applyFromArray([
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
                'startColor' => ['rgb' => '20C997']
            ]
        ]);
        $sheet1->getRowDimension(2)->setRowHeight(25);

        // ENCABEZADOS DE COLUMNAS
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
            $sheet1->setCellValue($cell, $value);
        }

        $sheet1->getStyle('A4:L4')->applyFromArray([
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
                'startColor' => ['rgb' => '28A745']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        $sheet1->getRowDimension(4)->setRowHeight(20);

        // DATOS VERIFICADAS
        $row = 5;
        foreach ($verificadas as $item) {
            $fechaConstatacion = !empty($item['fecha_constatacion']) 
                ? date('d/m/Y H:i', strtotime($item['fecha_constatacion'])) 
                : 'N/A';

            $sheet1->setCellValue('A' . $row, $item['id_constatacion']);
            $sheet1->setCellValue('B' . $row, $fechaConstatacion);
            $sheet1->setCellValue('C' . $row, $item['tipo_persona']);
            $sheet1->setCellValue('D' . $row, $item['cliente_nombre']);
            $sheet1->setCellValue('E' . $row, $item['documento'] ?: 'N/A');
            $sheet1->setCellValue('F' . $row, $item['telefono'] ?: 'N/A');
            $sheet1->setCellValue('G' . $row, $item['numero_unidad'] ?: 'N/A');
            $sheet1->setCellValue('H' . $row, $item['producto_nombre'] ?: 'N/A');
            $sheet1->setCellValue('I' . $row, $item['departamento'] ?: 'N/A');
            $sheet1->setCellValue('J' . $row, $item['provincia'] ?: 'N/A');
            $sheet1->setCellValue('K' . $row, $item['distrito'] ?: 'N/A');
            $sheet1->setCellValue('L' . $row, $item['direccion'] ?: 'N/A');

            $sheet1->getStyle('A' . $row . ':L' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ]
            ]);

            $sheet1->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($row % 2 == 0) {
                $sheet1->getStyle('A' . $row . ':L' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E8F5E9']
                    ]
                ]);
            }

            $row++;
        }

        // TOTAL VERIFICADAS
        $row++;
        $sheet1->mergeCells('A' . $row . ':K' . $row);
        $sheet1->setCellValue('A' . $row, 'TOTAL VERIFICADAS');
        $sheet1->setCellValue('L' . $row, count($verificadas));
        $sheet1->getStyle('A' . $row . ':L' . $row)->applyFromArray([
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

        // Ajustar anchos
        $sheet1->getColumnDimension('A')->setWidth(8);
        $sheet1->getColumnDimension('B')->setWidth(18);
        $sheet1->getColumnDimension('C')->setWidth(12);
        $sheet1->getColumnDimension('D')->setWidth(30);
        $sheet1->getColumnDimension('E')->setWidth(12);
        $sheet1->getColumnDimension('F')->setWidth(12);
        $sheet1->getColumnDimension('G')->setWidth(10);
        $sheet1->getColumnDimension('H')->setWidth(25);
        $sheet1->getColumnDimension('I')->setWidth(15);
        $sheet1->getColumnDimension('J')->setWidth(15);
        $sheet1->getColumnDimension('K')->setWidth(15);
        $sheet1->getColumnDimension('L')->setWidth(40);

        // ==================== HOJA 2: PENDIENTES ====================
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Pendientes');

        // ENCABEZADO PRINCIPAL
        $sheet2->mergeCells('A1:H1');
        $sheet2->setCellValue('A1', 'CONSTATACIONES PENDIENTES');
        $sheet2->getStyle('A1')->applyFromArray([
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
                'startColor' => ['rgb' => 'FD7E14']
            ]
        ]);
        $sheet2->getRowDimension(1)->setRowHeight(30);

        // Subtítulo
        $sheet2->mergeCells('A2:H2');
        $sheet2->setCellValue('A2', 'Fecha de generación: ' . date('d/m/Y H:i:s') . ' | Total: ' . count($pendientes));
        $sheet2->getStyle('A2')->applyFromArray([
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
                'startColor' => ['rgb' => 'FF9800']
            ]
        ]);
        $sheet2->getRowDimension(2)->setRowHeight(25);

        // ENCABEZADOS PENDIENTES
        $headersPendientes = [
            'A4' => 'ID Financ.',
            'B4' => 'Fecha Entrega',
            'C4' => 'Tipo',
            'D4' => 'Cliente',
            'E4' => 'Documento',
            'F4' => 'Teléfono',
            'G4' => 'Nº Unidad',
            'H4' => 'Producto/Vehículo'
        ];

        foreach ($headersPendientes as $cell => $value) {
            $sheet2->setCellValue($cell, $value);
        }

        $sheet2->getStyle('A4:H4')->applyFromArray([
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
                'startColor' => ['rgb' => 'FD7E14']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        $sheet2->getRowDimension(4)->setRowHeight(20);

        // DATOS PENDIENTES
        $row = 5;
        foreach ($pendientes as $item) {
            $fechaEntrega = !empty($item['fecha_entrega']) 
                ? date('d/m/Y', strtotime($item['fecha_entrega'])) 
                : 'N/A';

            $sheet2->setCellValue('A' . $row, $item['idfinanciamiento']);
            $sheet2->setCellValue('B' . $row, $fechaEntrega);
            $sheet2->setCellValue('C' . $row, $item['tipo_persona']);
            $sheet2->setCellValue('D' . $row, $item['cliente_nombre']);
            $sheet2->setCellValue('E' . $row, $item['documento'] ?: 'N/A');
            $sheet2->setCellValue('F' . $row, $item['telefono'] ?: 'N/A');
            $sheet2->setCellValue('G' . $row, $item['numero_unidad'] ?: 'N/A');
            $sheet2->setCellValue('H' . $row, $item['producto_nombre'] ?: 'N/A');

            $sheet2->getStyle('A' . $row . ':H' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ]
            ]);

            $sheet2->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet2->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet2->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet2->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet2->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet2->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($row % 2 == 0) {
                $sheet2->getStyle('A' . $row . ':H' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF3E0']
                    ]
                ]);
            }

            $row++;
        }

        // TOTAL PENDIENTES
        $row++;
        $sheet2->mergeCells('A' . $row . ':G' . $row);
        $sheet2->setCellValue('A' . $row, 'TOTAL PENDIENTES');
        $sheet2->setCellValue('H' . $row, count($pendientes));
        $sheet2->getStyle('A' . $row . ':H' . $row)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FD7E14']
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

        // Ajustar anchos
        $sheet2->getColumnDimension('A')->setWidth(12);
        $sheet2->getColumnDimension('B')->setWidth(15);
        $sheet2->getColumnDimension('C')->setWidth(12);
        $sheet2->getColumnDimension('D')->setWidth(30);
        $sheet2->getColumnDimension('E')->setWidth(12);
        $sheet2->getColumnDimension('F')->setWidth(12);
        $sheet2->getColumnDimension('G')->setWidth(10);
        $sheet2->getColumnDimension('H')->setWidth(25);

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
