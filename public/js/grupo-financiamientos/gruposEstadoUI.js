/**
 * gruposEstadoUI.js
 * Módulo de manejo de estado y controles de UI
 */

/**
 * Obtener el tipo vehicular seleccionado
 * @returns {string|null} - 'auto', 'moto' o null si no está seleccionado
 */
function getTipoVehicular() {
    const radioAuto = document.getElementById('radioAuto');
    const radioMoto = document.getElementById('radioMoto');

    if (!radioAuto || !radioMoto) {
        return null;
    }

    if (radioAuto.checked) {
        return 'auto';
    }

    if (radioMoto.checked) {
        return 'moto';
    }

    return null;
}

/**
 * Verificar si es financiamiento vehicular
 * @returns {boolean} - true si hay tipo vehicular seleccionado
 */
function esFinanciamientoVehicular() {
    return getTipoVehicular() !== null;
}

/**
 * Obtener el valor del checkbox de cobrar mora
 * @returns {number} - 1 si está marcado, 0 si no
 */
function getCobrarMora() {
    const checkbox = document.getElementById('cobrarMoraCheckbox');
    return checkbox && checkbox.checked ? 1 : 0;
}

/**
 * Alternar el estado visual del checkbox de visibilidad del grupo
 */
function toggleEstadoGrupo() {
    const checkbox = document.getElementById('estadoActivo');
    const icon = document.getElementById('estadoIcon');
    const texto = document.getElementById('estadoTexto');
    
    if (!checkbox || !icon || !texto) {
        return;
    }
    
    if (checkbox.checked) {
        icon.className = 'fas fa-eye me-2';
        icon.style.color = '#28a745';
        texto.textContent = 'Grupo Visible';
        texto.style.color = '#28a745';
    } else {
        icon.className = 'fas fa-eye-slash me-2';
        icon.style.color = '#dc3545';
        texto.textContent = 'Grupo Oculto';
        texto.style.color = '#dc3545';
    }
}

/**
 * Configurar el manejo del checkbox vehicular
 */
function manejarCheckboxVehicular() {
    const esVehicularCheckbox = $('#esVehicularCheckbox');
    
    if (esVehicularCheckbox.length) {
        esVehicularCheckbox.on('change', function() {
            const isChecked = $(this).prop('checked');
            const radioAuto = $('#radioAuto');
            const radioMoto = $('#radioMoto');

            if (radioAuto.length && radioMoto.length) {
                // Habilitar/deshabilitar los radio buttons
                radioAuto.prop('disabled', !isChecked);
                radioMoto.prop('disabled', !isChecked);

                if (!isChecked) {
                    // Si se desmarca el checkbox, limpiar las selecciones
                    radioAuto.prop('checked', false);
                    radioMoto.prop('checked', false);
                }
                
                // Mostrar u ocultar las fechas según el estado del checkbox
                const fechasDiv = $('#fechasVehicular');
                if (isChecked) {
                    fechasDiv.css('display', 'flex');
                } else {
                    fechasDiv.css('display', 'none');
                }
            }
        });
    }
}

/**
 * Configurar el manejo del checkbox Yango
 */
function manejarCheckboxYango() {
    const esYangoCheckbox = $('#esYangoCheckbox');
    const esVehicularCheckbox = $('#esVehicularCheckbox');
    
    if (esYangoCheckbox.length) {
        esYangoCheckbox.on('change', function() {
            const isYango = $(this).prop('checked');
            
            if (isYango) {
                // Deshabilitar checkbox vehicular
                esVehicularCheckbox.prop('checked', false).prop('disabled', true);
                
                // Ocultar fechas vehiculares
                $('#fechasVehicular').hide();
                
                // Deshabilitar radio buttons vehiculares
                $('#radioAuto').prop('disabled', true).prop('checked', false);
                $('#radioMoto').prop('disabled', true).prop('checked', false);
                
                // Establecer valores predeterminados Yango (EDITABLES)
                $('#cuota_inicial').val('2000');
                $('#monto_cuota').val('100');
                $('#cantidad_cuotas').val('200');
                $('#moneda').val('$');
                
                // Sugerir nombre si está vacío
                if ($('#nombre_plan').val() === '') {
                    $('#nombre_plan').val('Credit Yango');
                }
                
                // Calcular automáticamente
                calcularFinanciamiento();
            } else {
                // Restaurar estado normal
                esVehicularCheckbox.prop('disabled', false);
            }
        });
    }
}

/**
 * Configurar el manejo del campo de penalización de mora
 */
function manejarCampoPenalizacionMora() {
    const cobrarMoraCheckbox = $('#cobrarMoraCheckbox');
    
    if (cobrarMoraCheckbox.length) {
        cobrarMoraCheckbox.on('change', function() {
            const isChecked = $(this).prop('checked');
            const contenedor = $('#contenedorPenalizacionMora');
            
            if (isChecked) {
                contenedor.slideDown(300);
            } else {
                contenedor.slideUp(300);
                $('#penalizacion_mora').val('');
            }
        });
        
        // Inicializar estado al cargar
        if (!cobrarMoraCheckbox.prop('checked')) {
            $('#contenedorPenalizacionMora').hide();
        }
    }
}

/**
 * Configurar el manejo de campos de comisión
 */
function manejarCamposComision() {
    const aplicaComisionCheckbox = $('#aplicaComisionCheckbox');
    
    if (aplicaComisionCheckbox.length) {
        aplicaComisionCheckbox.on('change', function() {
            const isChecked = $(this).prop('checked');
            const camposComision = $('#camposComision');
            
            if (isChecked) {
                camposComision.slideDown(300);
            } else {
                camposComision.slideUp(300);
                // Limpiar valores cuando se desactiva
                $('#monto_comision').val('');
            }
        });
        
        // Inicializar estado al cargar la página
        if (!aplicaComisionCheckbox.prop('checked')) {
            $('#camposComision').hide();
        }
    }
}

/**
 * Configurar el manejo del checkbox de estado (activo/inactivo)
 */
function configurarCheckboxEstado() {
    const estadoCheckbox = $('#estadoActivo');
    if (estadoCheckbox.length) {
        estadoCheckbox.on('change', toggleEstadoGrupo);
    }
}

/**
 * Resetear todos los controles de UI a su estado inicial
 */
function resetearControlesUI() {
    // Resetear checkbox vehicular
    $('#esVehicularCheckbox').prop('checked', false);
    $('#radioAuto').prop('disabled', true).prop('checked', false);
    $('#radioMoto').prop('disabled', true).prop('checked', false);
    $('#fechasVehicular').hide();
    
    // Resetear checkbox Yango
    $('#esYangoCheckbox').prop('checked', false);
    
    // Resetear estado a activo
    $('#estadoActivo').prop('checked', true);
    toggleEstadoGrupo();
    
    // Resetear checkbox cobrar mora y penalización
    $('#cobrarMoraCheckbox').prop('checked', true);
    $('#mora_semanal').val('');
    $('#mora_quincenal').val('');
    $('#mora_mensual').val('');
    $('#contenedorPenalizacionMora').show();
    
    // Resetear comisión
    $('#aplicaComisionCheckbox').prop('checked', true);
    $('#camposComision').show();
    $('#monto_comision').val('');
    $('#moneda_comision').val('S/.');
}

/**
 * Establecer valores predeterminados para producto Yango
 */
function establecerValoresYango() {
    $('#cuota_inicial').val('2000');
    $('#monto_cuota').val('100');
    $('#cantidad_cuotas').val('200');
    $('#moneda').val('$');
    
    if ($('#nombre_plan').val() === '') {
        $('#nombre_plan').val('Credit Yango');
    }
    
    calcularFinanciamiento();
}
