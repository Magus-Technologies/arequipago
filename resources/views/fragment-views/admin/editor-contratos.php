<!-- Summernote Editor CSS -->
<link href="<?= URL::to('public/plugin/summernote/summernote-lite.min.css') ?>" rel="stylesheet" type="text/css">
    
    <style>
        .editor-container {
            max-width: 1400px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .editor-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px 10px 0 0;
            margin-bottom: 0;
        }
        
        .editor-body {
            background: white;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .variables-panel {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .variable-tag {
            display: inline-block;
            background: #e7f3ff;
            border: 1px solid #90caf9;
            padding: 5px 10px;
            margin: 5px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
        }
        
        .variable-tag:hover {
            background: #90caf9;
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-save {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn-preview {
            background: #17a2b8;
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .btn-back {
            background: #6c757d;
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .info-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        /* Estilos para simular página A4 */
        .a4-page-container {
            background: #525659;
            padding: 20px;
            min-height: 400px;
        }
        
        .a4-page {
            width: 21cm;
            min-height: 29.7cm;
            padding: 2cm;
            margin: 0 auto 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            position: relative;
        }
        
        .a4-page:last-child {
            margin-bottom: 0;
        }
        
        /* Estilos para el editor Summernote dentro de A4 */
        .note-editor {
            border: none !important;
        }
        
        /* Toolbar fijo en la parte superior */
        .note-toolbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: white;
            border-bottom: 1px solid #ddd;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .note-editor .note-editing-area .note-editable {
            background: white;
            min-height: 800px;
            max-height: none !important;
            overflow-y: visible !important;
            padding: 2cm;
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
        }
        
        /* Remover scrollbar del editor */
        .note-editor .note-editing-area {
            overflow: visible !important;
        }
        
        /* Vista previa estilo PDF */
        .preview-container {
            background: #525659;
            padding: 20px;
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .preview-page {
            width: 21cm;
            min-height: 29.7cm;
            padding: 2cm;
            margin: 0 auto 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            page-break-after: always;
        }
        
        .preview-page:last-child {
            margin-bottom: 0;
        }
        
        @media print {
            .preview-page {
                margin: 0;
                box-shadow: none;
                page-break-after: always;
            }
        }
        
        /* Botón para agregar salto de página */
        .btn-page-break {
            background: #6c757d;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 4px;
            font-size: 11px;
            margin: 5px 0;
        }
        
        .btn-page-break:hover {
            background: #5a6268;
        }
    </style>

<div class="editor-container">
        <div class="editor-header">
            <h2><i class="fas fa-file-signature me-2"></i>Editor de Plantillas de Contratos</h2>
            <p class="mb-0">Crea y edita plantillas de contratos para tus grupos de financiamiento</p>
        </div>
        
        <div class="editor-body">
            <!-- Información del grupo -->
            <div class="info-box">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Grupo de Financiamiento:</strong> <span id="grupoNombre">Cargando...</span>
            </div>
            
            <!-- Formulario -->
            <form id="formPlantilla">
                <input type="hidden" id="plantillaId">
                <input type="hidden" id="grupoId">
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-tag me-2"></i>Nombre de la Plantilla
                        </label>
                        <input type="text" class="form-control" id="nombrePlantilla" 
                               placeholder="Ej: Contrato de Financiamiento Vehicular" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-align-left me-2"></i>Descripción
                        </label>
                        <input type="text" class="form-control" id="descripcionPlantilla" 
                               placeholder="Breve descripción de la plantilla">
                    </div>
                </div>
                
                <!-- Panel de Variables -->
                <div class="mb-4">
                    <h5><i class="fas fa-code me-2"></i>Variables Disponibles</h5>
                    <p class="text-muted small">Haz clic en una variable para copiarla al portapapeles</p>
                    <div class="variables-panel" id="variablesPanel">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Editor HTML con formato A4 -->
                <div class="mb-4">
                    <label class="form-label fw-bold">
                        <i class="fas fa-file-code me-2"></i>Contenido del Contrato
                    </label>
                    <p class="text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        El editor simula una página A4. El contenido se ajustará automáticamente a múltiples páginas si es necesario.
                    </p>
                    <div class="a4-page-container">
                        <div class="a4-page">
                            <textarea id="htmlEditor" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="d-flex gap-3 justify-content-between">
                    <button type="button" class="btn btn-back" onclick="volverAGrupos()">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Grupos
                    </button>
                    
                    <div class="d-flex gap-3">
                        <button type="button" class="btn btn-preview" onclick="vistaPrevia()">
                            <i class="fas fa-eye me-2"></i>Vista Previa
                        </button>
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-save me-2"></i>Guardar Plantilla
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<!-- Cargar Summernote después de que el DOM esté listo -->
<script>
    let grupoId;
    let plantillaActual;
    
    // Cargar Summernote dinámicamente y luego inicializar
    $.getScript('<?= URL::to('public/plugin/summernote/summernote-lite.min.js') ?>', function() {
        // Inicializar Summernote después de cargar el script
        $('#htmlEditor').summernote({
            height: null, // Sin altura fija
            minHeight: 800, // Altura mínima
            maxHeight: null, // Sin altura máxima (crece automáticamente)
            placeholder: 'Escribe el contenido del contrato aquí...',
            disableResizeEditor: true, // Deshabilitar resize manual
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
        
        // Cargar datos al iniciar
        grupoId = sessionStorage.getItem('editor_grupo_id');
        const plantillaData = sessionStorage.getItem('editor_plantilla');
        
        if (!grupoId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se especificó un grupo de financiamiento'
            }).then(() => {
                _ajaxDOM('/arequipago/grupo-financiamiento', 'contenedor-app');
            });
            return;
        }
        
        $('#grupoId').val(grupoId);
        
        // Si hay plantilla existente, cargarla
        if (plantillaData) {
            plantillaActual = JSON.parse(plantillaData);
            cargarPlantilla(plantillaActual);
        }
        
        // Cargar variables disponibles
        cargarVariables();
        
        // Cargar nombre del grupo
        cargarNombreGrupo(grupoId);
    });
    
    // Cargar variables disponibles
    function cargarVariables() {
            $.ajax({
                url: '/arequipago/api/contratos/variables',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        mostrarVariables(response.variables);
                    }
                },
                error: function() {
                    $('#variablesPanel').html('<p class="text-danger">Error al cargar variables</p>');
                }
            });
        }
        
        // Mostrar variables en el panel
        function mostrarVariables(variables) {
            const panel = $('#variablesPanel');
            panel.empty();
            
            for (const [variable, descripcion] of Object.entries(variables)) {
                const tag = $(`
                    <span class="variable-tag" title="${descripcion}" data-variable="${variable}">
                        ${variable}
                    </span>
                `);
                
                tag.on('click', function() {
                    const varText = $(this).data('variable');
                    copiarAlPortapapeles(varText);
                    
                    // Feedback visual
                    $(this).css('background', '#4caf50').css('color', 'white');
                    setTimeout(() => {
                        $(this).css('background', '').css('color', '');
                    }, 500);
                });
                
                panel.append(tag);
            }
        }
        
        // Copiar al portapapeles
        function copiarAlPortapapeles(texto) {
            navigator.clipboard.writeText(texto).then(() => {
                // Mostrar toast
                const toast = $(`
                    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                        <div class="toast show" role="alert">
                            <div class="toast-body bg-success text-white">
                                <i class="fas fa-check me-2"></i>Variable copiada: ${texto}
                            </div>
                        </div>
                    </div>
                `);
                $('body').append(toast);
                setTimeout(() => toast.remove(), 2000);
            });
        }
        
        // Cargar plantilla existente
        function cargarPlantilla(plantilla) {
            $('#plantillaId').val(plantilla.id);
            $('#nombrePlantilla').val(plantilla.nombre);
            $('#descripcionPlantilla').val(plantilla.descripcion || '');
            
            // Cargar contenido en Summernote
            if (plantilla.html_template) {
                $('#htmlEditor').summernote('code', plantilla.html_template);
            }
        }
        
        // Cargar nombre del grupo
        function cargarNombreGrupo(id) {
            $.ajax({
                url: '/arequipago/getDetallesPlan',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#grupoNombre').text(response.plan.nombre_plan);
                    }
                }
            });
        }
        
        // Vista previa
        function vistaPrevia() {
            const htmlContent = $('#htmlEditor').summernote('code');
            
            $.ajax({
                url: '/arequipago/api/contratos/plantilla/preview',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    html_template: htmlContent
                }),
                success: function(response) {
                    if (response.success) {
                        mostrarModalPreview(response.html);
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo generar la vista previa'
                    });
                }
            });
        }
        
        // Mostrar modal de preview
        function mostrarModalPreview(html) {
            const modal = $(`
                <div class="modal fade" id="modalPreview" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-eye me-2"></i>Vista Previa del Contrato
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Esta es una vista previa con datos de ejemplo
                                </div>
                                <div class="border p-4" style="background: white;">
                                    ${html}
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            
            $('#modalPreview').remove();
            $('body').append(modal);
            const modalInstance = new bootstrap.Modal(document.getElementById('modalPreview'));
            modalInstance.show();
        }
        
        // Guardar plantilla
        $('#formPlantilla').on('submit', function(e) {
            e.preventDefault();
            
            const plantillaId = $('#plantillaId').val();
            const nombre = $('#nombrePlantilla').val();
            const descripcion = $('#descripcionPlantilla').val();
            const htmlContent = $('#htmlEditor').summernote('code');
            const grupoId = $('#grupoId').val();
            
            if (!nombre || !htmlContent) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos Requeridos',
                    text: 'El nombre y el contenido son obligatorios'
                });
                return;
            }
            
            const url = plantillaId ? 
                '/arequipago/api/contratos/plantilla/actualizar' : 
                '/arequipago/api/contratos/plantilla/crear';
            
            const data = {
                nombre: nombre,
                descripcion: descripcion,
                html_template: htmlContent,
                grupo_financiamiento: grupoId,
                activo: true
            };
            
            if (plantillaId) {
                data.id = plantillaId;
                data.cambios = 'Actualización desde el editor';
            }
            
            $.ajax({
                url: url,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Plantilla guardada correctamente',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Limpiar sessionStorage
                            sessionStorage.removeItem('editor_grupo_id');
                            sessionStorage.removeItem('editor_plantilla');
                            
                            // Volver a la página anterior
                            window.history.back();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.error || 'No se pudo guardar la plantilla'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al guardar la plantilla'
                    });
                }
            });
        });
        
    // Volver a grupos
    function volverAGrupos() {
        Swal.fire({
            title: '¿Salir del editor?',
            text: 'Los cambios no guardados se perderán',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, salir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                sessionStorage.removeItem('editor_grupo_id');
                sessionStorage.removeItem('editor_plantilla');
                
                // Volver a la página anterior
                window.history.back();
            }
        });
    }
</script>
