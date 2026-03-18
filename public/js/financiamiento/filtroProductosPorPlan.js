// ============================================
// FILTRO DE PRODUCTOS POR PLAN
// ============================================
// Cuando se selecciona un plan específico (ej: SOAT),
// filtra los productos para mostrar solo los del plan

$(document).ready(function() {
    // Detectar cambio en el select de plan/grupo
    $('#grupo').on('change', function() {
        const planSeleccionado = $(this).val();
        console.log('📋 Plan seleccionado:', planSeleccionado);

        // ID del plan SOAT (creado en la base de datos)
        const PLAN_SOAT_ID = 48;

        // Si selecciona el plan SOAT, filtrar productos por id_plan
        if (parseInt(planSeleccionado) === PLAN_SOAT_ID) {
            console.log('🔍 Filtrando productos SOAT (id_plan = 48)');
            cargarProductosPorPlan(PLAN_SOAT_ID);
        } else {
            // Para otros planes, cargar productos normalmente
            console.log('📦 Cargando todos los productos');
            cargarProductos();
        }
    });
});
