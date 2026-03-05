/**
 * gruposContratos.js
 * Módulo de gestión de contratos y plantillas
 */

// Grupos con contratos hardcodeados del sistema anterior
const GRUPOS_HARDCODEADOS = [19, 22, 33, 35, 44];

// Variable global para el plan actual
let currentPlanId = null;

/**
 * Verificar si un grupo tiene contrato hardcodeado
 * @param {number} grupoId - ID del grupo
 * @returns {boolean} - true si tiene contrato hardcodeado
 */
function tieneContratoHardcodeado(grupoId) {
    return GRUPOS_HARDCODEADOS.includes(parseInt(grupoId));
}

/**
 * Ver contrato hardcodeado (genera PDF)
 * @param {number} grupoId - ID del grupo
 */
function verContratoHardcodeado(grupoId) {
    Swal.fire({
        title: 'Generando PDF...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '/arequipago/api/contratos/hardcoded-preview',
        type: 'POST',
        data: { grupo_id: grupoId },
        dataType: 'json',
        success: function(response) {
            Swal.close();
            
            if (response.success === false) {
                Swal.fire({
                    icon: 'info',
                    title: 'Contrato no disponible',
                    text: 'No hay contrato configurado para este grupo de financiamiento.',
                    confirmButtonColor: '#02a499',
                    confirmButtonText: 'Entendido'
                });
            } else {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/arequipago/api/contratos/hardcoded-preview';
                form.target = '_blank';
                
                const inputGrupo = document.createElement('input');
                inputGrupo.type = 'hidden';
                inputGrupo.name = 'grupo_id';
                inputGrupo.value = grupoId;
                form.appendChild(inputGrupo);
                
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            }
        },
        error: function(xhr) {
            Swal.close();
            
            try {
                const errorResponse = JSON.parse(xhr.responseText);
                if (errorResponse.success === false) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Contrato no disponible',
                        text: 'No hay contrato configurado para este grupo de financiamiento.',
                        confirmButtonColor: '#02a499',
                        confirmButtonText: 'Entendido'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al generar el contrato',
                        confirmButtonColor: '#ec4561'
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al generar el contrato',
                    confirmButtonColor: '#ec4561'
                });
            }
        }
    });
}

/**
 * Ver plantilla de contrato del sistema nuevo
 * @param {number} grupoId - ID del grupo
 */
function verPlantillaContrato(grupoId) {
    $.ajax({
        url: '/arequipago/api/contratos/plantilla-por-grupo',
        type: 'GET',
        data: { grupo_id: grupoId },
        success: function(response) {
            if (response.success && response.tiene_plantilla) {
                mostrarVistaPreviaPlantilla(response.plantilla);
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Sin Plantilla',
                    text: 'Este grupo no tiene una plantilla de contrato asignada. ¿Desea crear una?',
                    showCancelButton: true,
                    confirmButtonText: 'Crear Plantilla',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        abrirEditorPlantilla(grupoId, null);
                    }
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo verificar la plantilla'
            });
        }
    });
}

/**
 * Editar plantilla de contrato
 * @param {number} grupoId - ID del grupo
 */
function editarPlantillaContrato(grupoId) {
    $.ajax({
        url: '/arequipago/api/contratos/plantilla-por-grupo',
        type: 'GET',
        data: { grupo_id: grupoId },
        success: function(response) {
            if (response.success && response.tiene_plantilla) {
                abrirEditorPlantilla(grupoId, response.plantilla);
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Nueva Plantilla',
                    text: 'Este grupo no tiene plantilla. Se creará una nueva.',
                    confirmButtonText: 'Continuar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        abrirEditorPlantilla(grupoId, null);
                    }
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo verificar la plantilla'
            });
        }
    });
}

/**
 * Mostrar vista previa de plantilla como PDF
 * @param {Object} plantilla - Objeto con datos de la plantilla
 */
function mostrarVistaPreviaPlantilla(plantilla) {
    Swal.fire({
        title: 'Generando PDF...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/arequipago/api/contratos/plantilla/preview-pdf';
    form.target = '_blank';
    
    const inputTemplate = document.createElement('input');
    inputTemplate.type = 'hidden';
    inputTemplate.name = 'html_template';
    inputTemplate.value = plantilla.html_template;
    form.appendChild(inputTemplate);
    
    const inputNombre = document.createElement('input');
    inputNombre.type = 'hidden';
    inputNombre.name = 'nombre';
    inputNombre.value = plantilla.nombre;
    form.appendChild(inputNombre);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    setTimeout(() => Swal.close(), 1000);
}

/**
 * Abrir editor de plantillas de contratos
 * @param {number} grupoId - ID del grupo
 * @param {Object|null} plantilla - Datos de la plantilla o null para nueva
 */
function abrirEditorPlantilla(grupoId, plantilla) {
    sessionStorage.setItem('editor_grupo_id', grupoId);
    if (plantilla) {
        sessionStorage.setItem('editor_plantilla', JSON.stringify(plantilla));
    } else {
        sessionStorage.removeItem('editor_plantilla');
    }
    
    window.location.href = '/arequipago/editor-contratos';
}

/**
 * Configurar dropdown global de acciones
 */
function configurarDropdownGlobal() {
    // Handler para el botón de acciones
    $(document).on('click', '.btn-acciones-global', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $button = $(this);
        const $dropdown = $('#globalDropdownMenu');
        const isVisible = $dropdown.is(':visible');
        
        currentPlanId = $button.data('plan-id');
        
        const tieneContratoHard = tieneContratoHardcodeado(currentPlanId);
        
        if (tieneContratoHard) {
            $('.btn-view-hardcoded-contract').show();
            $('.btn-view-template-action').hide();
            $('.btn-edit-template-action').hide();
            $('#contrato-header-text').text('Contrato (Sistema Anterior)');
        } else {
            $('.btn-view-hardcoded-contract').show();
            $('#contrato-header-text').text('Contrato');
        }
        
        if (isVisible) {
            $dropdown.hide();
        } else {
            const buttonRect = $button[0].getBoundingClientRect();
            
            $dropdown.css({
                'position': 'fixed',
                'visibility': 'hidden',
                'display': 'block'
            });
            
            const dropdownHeight = $dropdown.outerHeight();
            const windowHeight = $(window).height();
            
            const spaceBelow = windowHeight - buttonRect.bottom;
            const spaceAbove = buttonRect.top;
            
            let topPosition;
            
            if (spaceBelow < dropdownHeight && spaceAbove > dropdownHeight) {
                topPosition = buttonRect.top - dropdownHeight - 2;
            } else {
                topPosition = buttonRect.bottom + 2;
            }
            
            $dropdown.css({
                'position': 'fixed',
                'top': topPosition + 'px',
                'left': buttonRect.left + 'px',
                'visibility': 'visible',
                'display': 'block'
            });
        }
    });
    
    // Cerrar dropdown al hacer click fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.btn-acciones-global').length && 
            !$(e.target).closest('#globalDropdownMenu').length) {
            $('#globalDropdownMenu').hide();
        }
    });
    
    // Handler para ver contrato hardcodeado
    $(document).on('click', '.btn-view-hardcoded-contract', function(e) {
        e.preventDefault();
        $('#globalDropdownMenu').hide();
        verContratoHardcodeado(currentPlanId);
    });
    
    // Handlers comentados (funcionalidad oculta en el front)
    /*
    $(document).on('click', '.btn-view-template-action', function(e) {
        e.preventDefault();
        $('#globalDropdownMenu').hide();
        verPlantillaContrato(currentPlanId);
    });
    
    $(document).on('click', '.btn-edit-template-action', function(e) {
        e.preventDefault();
        $('#globalDropdownMenu').hide();
        editarPlantillaContrato(currentPlanId);
    });
    */
}
