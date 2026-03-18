/**
 * gruposEventHandlers.js
 * Módulo de configuración de event handlers
 */

/**
 * Configurar event handlers del formulario principal
 */
function setupFormularioPrincipal() {
    // Validación en tiempo real
    const campos = ['nombre_plan', 'frecuencia_pago', 'tasa_interes'];
    
    campos.forEach(campo => {
        $(`#${campo}`).on('blur', function() {
            validarCampo(campo);
        });
    });

    // Cálculos automáticos
    $('#cantidad_cuotas').on('input', calcularFinanciamiento);
    $('#monto').on('input', calcularFinanciamiento);
    $('#cuota_inicial').on('input', calcularFinanciamiento);
    $('#tasa_interes').on('input', calcularFinanciamiento);
    $('#monto_cuota').on('input', calcularFinanciamiento);
    $('#monto_sin_interes').on('input', calcularFinanciamiento);
    $('#fecha_inicio').on('change', calcularFinanciamiento);
    $('#frecuencia_pago').on('change', calcularFinanciamiento);
}

/**
 * Configurar event handlers de botones de acción
 */
function setupBotonesAccion() {
    // Handler ÚNICO para editar (consolidado)
    $(document).on('click', '.btn-edit, .btn-edit-action', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const row = $(this).closest('tr');
        const planId = row.data("plan-id") || currentPlanId;
        
        if (!planId) {
            Swal.fire('Error', 'No se puede editar el plan', 'error');
            return;
        }
        
        $('#globalDropdownMenu').hide();
        // Pasar solo el planId, la función obtendrá rowData del DataTable
        poblarFormularioEdicion(planId);
    });

    // Handler ÚNICO para eliminar (consolidado)
    $(document).on('click', '.btn-delete, .btn-delete-action', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const row = $(this).closest('tr');
        const planId = row.data("plan-id") || currentPlanId;
        
        $('#globalDropdownMenu').hide();
        eliminarGrupoFinanciamiento(planId);
    });

    // Handler para ver detalles
    $(document).on('click', '.btn-view, .btn-view-action', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const row = $(this).closest('tr');
        const planId = row.data("plan-id") || currentPlanId;

        if (!planId) {
            Swal.fire('Error', 'No se puede obtener el detalle', 'error');
            return;
        }

        $('#globalDropdownMenu').hide();
        obtenerDetallesGrupo(planId);
    });

    // Handler para botón registrar
    $(document).on('click', '#btnRegistrar', function() {
        registrarGrupoFinanciamiento();
    });

    // Handler para guardar cambios (edición)
    $(document).on('click', '#guardarCambios', function() {
        guardarCambiosGrupo();
    });

    // Handler para cancelar edición
    $(document).on('click', '#cancelarEdicion', function() {
        cancelarEdicion();
    });
}

/**
 * Configurar event handlers del modal de variantes
 */
function setupModalVariantes() {
    // Botón para abrir modal
    $('#btnAgregarVariante').on('click', function() {
        abrirModalVariante();
    });

    // Botón para guardar variante nueva
    $('#btnGuardarVariante').on('click', function() {
        guardarVariante();
    });

    // Handler para editar variante (delegado)
    $(document).on('click', '.btn-edit-variante', function(e) {
        e.preventDefault();
        const varianteId = $(this).closest('.variante-card').data('id');
        editarVariante(varianteId);
    });

    // Handler para guardar cambios de variante (delegado)
    $(document).on('click', '#btnGuardarCambiosVariante', function() {
        actualizarVariante();
    });

    // Restaurar modal cuando se cierra
    $('#modalVariante').on('hidden.bs.modal', function() {
        restaurarModalVariante();
    });

    // Cálculos automáticos en modal
    $('#monto_var').on('input', calculoModal);
    $('#monto_sin_interes_var').on('input', calculoModal);
    $('#cuota_inicial_var').on('input', calculoModal);
    $('#tasa_interes_var').on('input', calculoModal);
    $('#cantidad_cuotas_var').on('input', calculoModal);
    $('#monto_cuota_var').on('input', calculoModal);
    $('#frecuencia_pago_var').on('change', calculoModal);
    $('#fecha_inicio_var').on('change', calculoModal);
}

/**
 * Configurar event handlers de tabs
 */
function setupTabs() {
    $(".nav-link").on("click", function() {
        $(".nav-link").removeClass("tab-button-active");
        $(this).addClass("tab-button-active");
    });
}
