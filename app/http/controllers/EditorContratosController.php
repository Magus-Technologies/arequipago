<?php

require_once 'app/models/GrupoFinanciamientoModel.php';

class EditorContratosController extends controller
{
    private $conexion;
    private $templatesPath;
    private $backupPath;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->templatesPath = 'app/contratos/templates.json';
        $this->backupPath = 'app/contratos/backup/';

        // Crear carpeta de backup si no existe
        if (!file_exists($this->backupPath)) {
            mkdir($this->backupPath, 0777, true);
        }
    }

    /**
     * Listar todas las plantillas
     */
    public function listarPlantillas()
    {
        try {
            $templates = $this->cargarTemplates();

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'plantillas' => $templates['plantillas'] ?? [],
                'version' => $templates['version'] ?? '1.0',
                'ultima_actualizacion' => $templates['ultima_actualizacion'] ?? date('Y-m-d\TH:i:s')
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener una plantilla específica por ID
     */
    public function obtenerPlantilla()
    {
        try {
            $id = $_GET['id'] ?? null;

            if (!$id) {
                throw new Exception('ID de plantilla no proporcionado');
            }

            $templates = $this->cargarTemplates();
            $plantilla = null;

            foreach ($templates['plantillas'] as $template) {
                if ($template['id'] == $id) {
                    $plantilla = $template;
                    break;
                }
            }

            if (!$plantilla) {
                throw new Exception('Plantilla no encontrada');
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'plantilla' => $plantilla
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener plantilla por grupo de financiamiento
     */
    public function obtenerPlantillaPorGrupo()
    {
        try {
            $grupoId = $_GET['grupo_id'] ?? null;

            if (!$grupoId) {
                throw new Exception('ID de grupo no proporcionado');
            }

            $templates = $this->cargarTemplates();
            $plantilla = null;

            // Buscar plantilla por grupo de financiamiento
            foreach ($templates['plantillas'] as $template) {
                if ($template['grupo_financiamiento'] == $grupoId && $template['activo']) {
                    $plantilla = $template;
                    break;
                }
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'plantilla' => $plantilla,
                'tiene_plantilla' => $plantilla !== null
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Crear una nueva plantilla
     */
    public function crearPlantilla()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            // Validar datos requeridos
            if (empty($input['nombre']) || empty($input['html_template'])) {
                throw new Exception('Nombre y contenido HTML son obligatorios');
            }

            // Crear backup antes de modificar
            $this->crearBackup();

            $templates = $this->cargarTemplates();

            // Generar nuevo ID
            $nuevoId = 1;
            if (!empty($templates['plantillas'])) {
                $ids = array_column($templates['plantillas'], 'id');
                $nuevoId = max($ids) + 1;
            }

            // Crear nueva plantilla
            $nuevaPlantilla = [
                'id' => $nuevoId,
                'nombre' => $input['nombre'],
                'descripcion' => $input['descripcion'] ?? '',
                'categoria' => $input['categoria'] ?? null,
                'grupo_financiamiento' => $input['grupo_financiamiento'] ?? null,
                'activo' => $input['activo'] ?? true,
                'html_template' => $input['html_template'],
                'css_personalizado' => $input['css_personalizado'] ?? '',
                'variables_requeridas' => $this->extraerVariables($input['html_template']),
                'metadata' => [
                    'creado_por' => $_SESSION['usuario'] ?? 'sistema',
                    'fecha_creacion' => date('Y-m-d\TH:i:s'),
                    'modificado_por' => $_SESSION['usuario'] ?? 'sistema',
                    'fecha_modificacion' => date('Y-m-d\TH:i:s'),
                    'version' => '1.0',
                    'historial' => [
                        [
                            'version' => '1.0',
                            'fecha' => date('Y-m-d\TH:i:s'),
                            'cambios' => 'Creación inicial',
                            'usuario' => $_SESSION['usuario'] ?? 'sistema'
                        ]
                    ]
                ]
            ];

            $templates['plantillas'][] = $nuevaPlantilla;
            $templates['ultima_actualizacion'] = date('Y-m-d\TH:i:s');

            $this->guardarTemplates($templates);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'mensaje' => 'Plantilla creada exitosamente',
                'plantilla' => $nuevaPlantilla
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Actualizar una plantilla existente
     */
    public function actualizarPlantilla()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;

            if (!$id) {
                throw new Exception('ID de plantilla no proporcionado');
            }

            // Crear backup antes de modificar
            $this->crearBackup();

            $templates = $this->cargarTemplates();
            $plantillaEncontrada = false;

            foreach ($templates['plantillas'] as &$template) {
                if ($template['id'] == $id) {
                    $plantillaEncontrada = true;

                    // Guardar versión anterior en historial
                    $versionAnterior = $template['metadata']['version'] ?? '1.0';
                    $nuevaVersion = $this->incrementarVersion($versionAnterior);

                    $historialEntry = [
                        'version' => $nuevaVersion,
                        'fecha' => date('Y-m-d\TH:i:s'),
                        'cambios' => $input['cambios'] ?? 'Actualización de plantilla',
                        'usuario' => $_SESSION['usuario'] ?? 'sistema'
                    ];

                    // Actualizar campos
                    if (isset($input['nombre']))
                        $template['nombre'] = $input['nombre'];
                    if (isset($input['descripcion']))
                        $template['descripcion'] = $input['descripcion'];
                    if (isset($input['categoria']))
                        $template['categoria'] = $input['categoria'];
                    if (isset($input['grupo_financiamiento']))
                        $template['grupo_financiamiento'] = $input['grupo_financiamiento'];
                    if (isset($input['activo']))
                        $template['activo'] = $input['activo'];
                    if (isset($input['html_template'])) {
                        $template['html_template'] = $input['html_template'];
                        $template['variables_requeridas'] = $this->extraerVariables($input['html_template']);
                    }
                    if (isset($input['css_personalizado']))
                        $template['css_personalizado'] = $input['css_personalizado'];

                    // Actualizar metadata
                    $template['metadata']['modificado_por'] = $_SESSION['usuario'] ?? 'sistema';
                    $template['metadata']['fecha_modificacion'] = date('Y-m-d\TH:i:s');
                    $template['metadata']['version'] = $nuevaVersion;
                    $template['metadata']['historial'][] = $historialEntry;

                    break;
                }
            }

            if (!$plantillaEncontrada) {
                throw new Exception('Plantilla no encontrada');
            }

            $templates['ultima_actualizacion'] = date('Y-m-d\TH:i:s');
            $this->guardarTemplates($templates);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'mensaje' => 'Plantilla actualizada exitosamente'
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Eliminar una plantilla (soft delete)
     */
    public function eliminarPlantilla()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;

            if (!$id) {
                throw new Exception('ID de plantilla no proporcionado');
            }

            // Crear backup antes de modificar
            $this->crearBackup();

            $templates = $this->cargarTemplates();
            $plantillaEncontrada = false;

            foreach ($templates['plantillas'] as &$template) {
                if ($template['id'] == $id) {
                    $template['activo'] = false;
                    $template['metadata']['eliminado_por'] = $_SESSION['usuario'] ?? 'sistema';
                    $template['metadata']['fecha_eliminacion'] = date('Y-m-d\TH:i:s');
                    $plantillaEncontrada = true;
                    break;
                }
            }

            if (!$plantillaEncontrada) {
                throw new Exception('Plantilla no encontrada');
            }

            $templates['ultima_actualizacion'] = date('Y-m-d\TH:i:s');
            $this->guardarTemplates($templates);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'mensaje' => 'Plantilla eliminada exitosamente'
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Vista previa de una plantilla con datos de prueba
     */
    public function vistaPrevia()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $htmlTemplate = $input['html_template'] ?? '';
            $datosPrueba = $input['datos_prueba'] ?? $this->obtenerDatosPrueba();

            // Reemplazar variables en el template
            $htmlFinal = $this->reemplazarVariables($htmlTemplate, $datosPrueba);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'html' => $htmlFinal
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generar PDF de vista previa
     */
    public function generarPDFPreview()
    {
        try {
            // Aceptar datos POST normales o JSON
            if (isset($_POST['html_template'])) {
                $htmlTemplate = $_POST['html_template'];
                $nombrePlantilla = $_POST['nombre'] ?? 'Vista Previa';
            } else {
                $input = json_decode(file_get_contents('php://input'), true);
                $htmlTemplate = $input['html_template'] ?? '';
                $nombrePlantilla = $input['nombre'] ?? 'Vista Previa';
            }
            
            $datosPrueba = $this->obtenerDatosPrueba();

            // Reemplazar variables en el template
            $htmlFinal = $this->reemplazarVariables($htmlTemplate, $datosPrueba);

            // Cargar mPDF
            if (!class_exists('Mpdf\Mpdf')) {
                require_once 'utils/lib/mpdf/vendor/autoload.php';
            }

            // Crear instancia de mPDF
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 20,
                'margin_right' => 20,
                'margin_top' => 20,
                'margin_bottom' => 20,
                'margin_header' => 10,
                'margin_footer' => 10
            ]);

            // Configurar propiedades del PDF
            $mpdf->SetTitle($nombrePlantilla);
            $mpdf->SetAuthor('Sistema de Contratos');
            $mpdf->SetCreator('CrediGO');

            // Escribir HTML al PDF
            $mpdf->WriteHTML($htmlFinal);

            // Salida del PDF al navegador
            $mpdf->Output($nombrePlantilla . '.pdf', 'I'); // 'I' = inline en el navegador
            exit;

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generar PDF de contrato hardcodeado
     */
    public function hardcodedPreview()
    {
        try {
            // Aceptar datos POST normales
            $grupoId = $_POST['grupo_id'] ?? null;
            
            if (!$grupoId) {
                throw new Exception('ID de grupo no proporcionado');
            }
            
            // Mapeo de grupos a archivos de contrato
            $contratoMap = [
                19 => 'credigo_autos.html',
                22 => 'contrato_Motos.html',
                33 => 'contrato_MotosYa.html',
                35 => 'contrato_MotosYa.html',
                44 => 'contrato_incamotors.html'
            ];
            
            if (!isset($contratoMap[$grupoId])) {
                throw new Exception('No hay contrato hardcodeado para este grupo');
            }
            
            $archivoContrato = 'app/contratos/' . $contratoMap[$grupoId];
            
            if (!file_exists($archivoContrato)) {
                throw new Exception('Archivo de contrato no encontrado: ' . $contratoMap[$grupoId]);
            }
            
            // Leer el HTML del contrato
            $htmlTemplate = file_get_contents($archivoContrato);
            
            // Reemplazar variables con datos de ejemplo
            $datosPrueba = $this->obtenerDatosPrueba();
            $htmlFinal = $this->reemplazarVariables($htmlTemplate, $datosPrueba);
            
            // Cargar mPDF
            if (!class_exists('Mpdf\Mpdf')) {
                require_once 'utils/lib/mpdf/vendor/autoload.php';
            }
            
            // Configurar márgenes según el grupo
            if ($grupoId == 22) {
                // Sin márgenes para motos
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'margin_left' => 0,
                    'margin_right' => 0,
                    'margin_top' => 0,
                    'margin_bottom' => 0
                ]);
            } else {
                // Márgenes normales
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'margin_left' => 20,
                    'margin_right' => 20,
                    'margin_top' => 20,
                    'margin_bottom' => 20
                ]);
            }
            
            // Configurar propiedades del PDF
            $mpdf->SetTitle('Contrato Grupo ' . $grupoId);
            $mpdf->SetAuthor('Sistema de Contratos');
            
            // Escribir HTML al PDF
            $mpdf->WriteHTML($htmlFinal);
            
            // Salida del PDF al navegador
            $mpdf->Output('Contrato_Grupo_' . $grupoId . '.pdf', 'I');
            exit;
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    /**
     * Crear backup del archivo templates.json
     */
    public function crearBackup()
    {
        try {
            if (file_exists($this->templatesPath)) {
                $fecha = date('Y-m-d_H-i-s');
                $backupFile = $this->backupPath . "templates_backup_{$fecha}.json";
                copy($this->templatesPath, $backupFile);

                // Mantener solo los últimos 10 backups
                $this->limpiarBackupsAntiguos();

                return [
                    'success' => true,
                    'backup_file' => $backupFile
                ];
            }

            return ['success' => false, 'error' => 'Archivo de plantillas no encontrado'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Listar variables disponibles
     */
    public function listarVariables()
    {
        $variables = [
            // Datos del cliente/conductor
            '{{nombre_cliente}}' => 'Nombre completo del cliente',
            '{{dni}}' => 'Número de documento',
            '{{telefono}}' => 'Teléfono',
            '{{direccion}}' => 'Dirección completa',
            '{{direccion_detalle}}' => 'Dirección detallada',
            '{{distrito}}' => 'Distrito',
            '{{provincia}}' => 'Provincia',
            '{{departamento}}' => 'Departamento',
            '{{correo}}' => 'Correo electrónico',
            '{{nro_licencia}}' => 'Número de licencia (solo conductores)',
            // Datos del financiamiento
            '{{codigo_asociado}}' => 'Código de asociado',
            '{{grupo_financiamiento}}' => 'Nombre del grupo de financiamiento',
            '{{monto_total}}' => 'Monto total del financiamiento',
            '{{monto_sin_intereses}}' => 'Monto sin intereses',
            '{{cuota_inicial}}' => 'Cuota inicial',
            '{{numero_cuotas}}' => 'Número de cuotas',
            '{{valor_cuota}}' => 'Valor de cada cuota',
            '{{fecha_inicio}}' => 'Fecha de inicio del financiamiento',
            '{{fecha_fin}}' => 'Fecha de fin del financiamiento',
            '{{frecuencia_pago}}' => 'Frecuencia de pago (semanal/mensual)',
            '{{tasa_interes}}' => 'Tasa de interés',
            '{{moneda}}' => 'Moneda (S/. o $)',
            // Datos del producto
            '{{nombre_producto}}' => 'Nombre del producto',
            '{{codigo_producto}}' => 'Código del producto',
            '{{categoria_producto}}' => 'Categoría del producto',
            '{{caracteristicas}}' => 'Características del producto',
            // Fechas
            '{{fecha_actual}}' => 'Fecha actual',
            '{{hora_actual}}' => 'Hora actual',
            '{{dia}}' => 'Día actual',
            '{{mes}}' => 'Mes actual',
            '{{anio}}' => 'Año actual',
            '{{fecha_creacion}}' => 'Fecha de creación del financiamiento',
            // Otros
            '{{num_unidad}}' => 'Número de unidad (solo conductores)',
            '{{tipo_documento}}' => 'Tipo de documento'
        ];

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'variables' => $variables
        ]);
    }

    // ==================== MÉTODOS PRIVADOS ====================

    /**
     * Cargar templates desde el archivo JSON
     */
    private function cargarTemplates()
    {
        if (!file_exists($this->templatesPath)) {
            // Si no existe, crear archivo inicial
            $templatesInicial = [
                'version' => '1.0',
                'ultima_actualizacion' => date('Y-m-d\TH:i:s'),
                'plantillas' => []
            ];
            $this->guardarTemplates($templatesInicial);
            return $templatesInicial;
        }

        $json = file_get_contents($this->templatesPath);
        return json_decode($json, true);
    }

    /**
     * Guardar templates en el archivo JSON
     */
    private function guardarTemplates($templates)
    {
        $json = json_encode($templates, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($this->templatesPath, $json);
    }

    /**
     * Extraer variables del template HTML
     */
    private function extraerVariables($html)
    {
        preg_match_all('/\{\{([^}]+)\}\}/', $html, $matches);
        return array_unique($matches[1]);
    }

    /**
     * Reemplazar variables en el template
     */
    private function reemplazarVariables($html, $datos)
    {
        foreach ($datos as $variable => $valor) {
            $html = str_replace('{{' . $variable . '}}', $valor, $html);
        }
        return $html;
    }

    /**
     * Obtener datos de prueba para vista previa
     */
    private function obtenerDatosPrueba()
    {
        return [
            'nombre_cliente' => 'Juan Pérez García',
            'dni' => '12345678',
            'telefono' => '987654321',
            'direccion' => 'Av. Principal 123, Cercado, Arequipa, Arequipa',
            'direccion_detalle' => 'Av. Principal 123',
            'distrito' => 'Cercado',
            'provincia' => 'Arequipa',
            'departamento' => 'Arequipa',
            'correo' => 'juan.perez@example.com',
            'nro_licencia' => 'Q12345678',
            'codigo_asociado' => 'ASO-001',
            'grupo_financiamiento' => 'CrediGo Autos Grupo 1',
            'monto_total' => 'S/. 15,000.00',
            'monto_sin_intereses' => 'S/. 14,000.00',
            'cuota_inicial' => 'S/. 2,000.00',
            'numero_cuotas' => '12',
            'valor_cuota' => 'S/. 1,083.33',
            'fecha_inicio' => '01/01/2025',
            'fecha_fin' => '01/12/2025',
            'frecuencia_pago' => 'Mensual',
            'tasa_interes' => '7.14%',
            'moneda' => 'S/.',
            'nombre_producto' => 'Vehículo Toyota Yaris',
            'codigo_producto' => 'VEH-001',
            'categoria_producto' => 'Vehículo',
            'caracteristicas' => 'Año 2020, Color Blanco, Automático',
            'fecha_actual' => date('d/m/Y'),
            'hora_actual' => date('H:i A'),
            'dia' => date('d'),
            'mes' => date('m'),
            'anio' => date('Y'),
            'fecha_creacion' => date('d/m/Y H:i A'),
            'num_unidad' => 'U-123',
            'tipo_documento' => 'DNI'
        ];
    }

    /**
     * Incrementar versión
     */
    private function incrementarVersion($version)
    {
        $partes = explode('.', $version);
        $partes[count($partes) - 1]++;
        return implode('.', $partes);
    }

    /**
     * Limpiar backups antiguos (mantener solo los últimos 10)
     */
    private function limpiarBackupsAntiguos()
    {
        $archivos = glob($this->backupPath . 'templates_backup_*.json');

        if (count($archivos) > 10) {
            // Ordenar por fecha de modificación
            usort($archivos, function ($a, $b) {
                return filemtime($a) - filemtime($b);
            });

            // Eliminar los más antiguos
            $aEliminar = array_slice($archivos, 0, count($archivos) - 10);
            foreach ($aEliminar as $archivo) {
                unlink($archivo);
            }
        }
    }
}
