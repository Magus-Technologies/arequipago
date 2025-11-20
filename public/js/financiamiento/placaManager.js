// public/js/financiamiento/placaManager.js
// ✅ Gestión del campo Placa para Mantenimiento IncaMotors (Plan ID 44)

/**
 * Verifica si debe mostrarse el campo de placa
 * Condiciones:
 * 1. Plan seleccionado es Mantenimiento IncaMotors (ID 44)
 * 2. Producto seleccionado es de categoría "Aceites"
 */
function verificarMostrarCampoPlaca() {
    console.log("🔍 Verificando si mostrar campo placa...");
    
    // Obtener el plan seleccionado
    const grupoSelect = document.getElementById('grupo');
    const idPlan = grupoSelect ? parseInt(grupoSelect.value) : null;
    
    // Verificar si es plan Mantenimiento IncaMotors (ID 44)
    const esIncaMotors = idPlan === 44;
    
    console.log("📋 Plan seleccionado:", idPlan, "- Es IncaMotors:", esIncaMotors);
    
    if (!esIncaMotors) {
        ocultarCampoPlaca();
        return;
    }
    
    // Verificar si hay un producto seleccionado
    if (!productoSeleccionado || !productoSeleccionado.id) {
        console.log("⚠️ No hay producto seleccionado");
        ocultarCampoPlaca();
        return;
    }
    
    // Obtener la categoría del producto
    const categoriaProducto = productoSeleccionado.categoria || '';
    const esAceite = categoriaProducto.toLowerCase().includes('aceite');
    
    console.log("🛢️ Categoría del producto:", categoriaProducto, "- Es aceite:", esAceite);
    
    if (esAceite) {
        mostrarCampoPlaca();
    } else {
        ocultarCampoPlaca();
    }
}

/**
 * Muestra el campo de placa con animación
 */
function mostrarCampoPlaca() {
    const placaContainer = document.getElementById('placaVehiculoContainer');
    if (placaContainer) {
        placaContainer.classList.remove('d-none');
        placaContainer.style.animation = 'fadeIn 0.3s ease-in';
        
        // Hacer el campo obligatorio
        const placaInput = document.getElementById('placaVehiculo');
        if (placaInput) {
            placaInput.required = true;
        }
        
        console.log("✅ Campo placa mostrado");
    }
}

/**
 * Oculta el campo de placa y limpia su valor
 */
function ocultarCampoPlaca() {
    const placaContainer = document.getElementById('placaVehiculoContainer');
    if (placaContainer && !placaContainer.classList.contains('d-none')) {
        placaContainer.classList.add('d-none');
        
        // Limpiar el valor y quitar obligatoriedad
        const placaInput = document.getElementById('placaVehiculo');
        if (placaInput) {
            placaInput.value = '';
            placaInput.required = false;
        }
        
        console.log("🔒 Campo placa ocultado");
    }
}

/**
 * Obtiene el valor de la placa (si está visible)
 * @returns {string|null} Valor de la placa o null si no está visible
 */
function obtenerPlacaVehiculo() {
    const placaContainer = document.getElementById('placaVehiculoContainer');
    const placaInput = document.getElementById('placaVehiculo');
    
    // Solo retornar valor si el campo está visible
    if (placaContainer && !placaContainer.classList.contains('d-none') && placaInput) {
        const placa = placaInput.value.trim().toUpperCase();
        return placa || null;
    }
    
    return null;
}

/**
 * Valida el formato de la placa
 * @param {string} placa - Placa a validar
 * @returns {boolean} True si es válida
 */
function validarFormatoPlaca(placa) {
    if (!placa) return false;
    
    // Formato peruano: ABC-123 o ABC123 (3 letras + 3 números)
    const formatoPeruano = /^[A-Z]{3}-?\d{3}$/;
    
    return formatoPeruano.test(placa);
}

/**
 * Formatea la placa automáticamente mientras el usuario escribe
 */
function configurarFormateoPlaca() {
    const placaInput = document.getElementById('placaVehiculo');
    if (!placaInput) return;
    
    placaInput.addEventListener('input', function(e) {
        let valor = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        
        // Limitar a 6 caracteres (3 letras + 3 números)
        if (valor.length > 6) {
            valor = valor.substring(0, 6);
        }
        
        // Agregar guión automáticamente después de 3 caracteres
        if (valor.length > 3) {
            valor = valor.substring(0, 3) + '-' + valor.substring(3);
        }
        
        e.target.value = valor;
    });
    
    console.log("✅ Formateo automático de placa configurado");
}

// ✅ Inicializar cuando el DOM esté listo
$(document).ready(function() {
    configurarFormateoPlaca();
    console.log("✅ placaManager.js inicializado");
});
