// public/js/financiamiento/numeroCorporativoManager.js
// ✅ Gestión del campo Número Corporativo para CORPORATIVO CLARO (Plan ID 36)

/**
 * Verifica si debe mostrarse el campo de número corporativo
 * Condición: Plan seleccionado es CORPORATIVO CLARO (ID 36)
 */
function verificarMostrarCampoNumeroCorporativo() {
    console.log("🔍 Verificando si mostrar campo número corporativo...");

    // Obtener el plan seleccionado
    const grupoSelect = document.getElementById('grupo');
    const idPlan = grupoSelect ? parseInt(grupoSelect.value) : null;

    // Verificar si es plan CORPORATIVO CLARO (ID 36)
    const esCorporativoClaro = idPlan === 36;

    console.log("📋 Plan seleccionado:", idPlan, "- Es Corporativo CLARO:", esCorporativoClaro);

    if (esCorporativoClaro) {
        mostrarCampoNumeroCorporativo();
    } else {
        ocultarCampoNumeroCorporativo();
    }
}

/**
 * Muestra el campo de número corporativo con animación
 */
function mostrarCampoNumeroCorporativo() {
    const numeroContainer = document.getElementById('numeroCorporativoContainer');
    if (numeroContainer) {
        numeroContainer.classList.remove('d-none');
        numeroContainer.style.animation = 'fadeIn 0.3s ease-in';

        // Hacer el campo obligatorio
        const numeroInput = document.getElementById('numeroCorporativo');
        if (numeroInput) {
            numeroInput.required = true;
        }

        console.log("✅ Campo número corporativo mostrado");
    }
}

/**
 * Oculta el campo de número corporativo y limpia su valor
 */
function ocultarCampoNumeroCorporativo() {
    const numeroContainer = document.getElementById('numeroCorporativoContainer');
    if (numeroContainer && !numeroContainer.classList.contains('d-none')) {
        numeroContainer.classList.add('d-none');

        // Limpiar el valor y quitar obligatoriedad
        const numeroInput = document.getElementById('numeroCorporativo');
        if (numeroInput) {
            numeroInput.value = '';
            numeroInput.required = false;
        }

        console.log("🔒 Campo número corporativo ocultado");
    }
}

/**
 * Obtiene el valor del número corporativo (si está visible)
 * @returns {string|null} Valor del número corporativo o null si no está visible
 */
function obtenerNumeroCorporativo() {
    const numeroContainer = document.getElementById('numeroCorporativoContainer');
    const numeroInput = document.getElementById('numeroCorporativo');

    console.log("🔍 obtenerNumeroCorporativo() llamado");
    console.log("📦 Container existe:", !!numeroContainer);
    console.log("📦 Container visible:", numeroContainer && !numeroContainer.classList.contains('d-none'));
    console.log("📦 Input existe:", !!numeroInput);

    // Solo retornar valor si el campo está visible
    if (numeroContainer && !numeroContainer.classList.contains('d-none') && numeroInput) {
        const numero = numeroInput.value.trim();
        console.log("✅ Número corporativo capturado:", numero || "(vacío)");
        return numero || null;
    }

    console.log("❌ Campo no visible o no existe - retornando null");
    return null;
}

/**
 * Valida el formato del número corporativo
 * @param {string} numero - Número a validar
 * @returns {boolean} True si es válido
 */
function validarFormatoNumeroCorporativo(numero) {
    if (!numero) return false;

    // Solo números, longitud entre 7 y 20 caracteres
    const formatoNumero = /^\d{7,20}$/;

    return formatoNumero.test(numero);
}

/**
 * Formatea el número corporativo automáticamente mientras el usuario escribe
 * Solo permite números
 */
function configurarFormateoNumeroCorporativo() {
    const numeroInput = document.getElementById('numeroCorporativo');
    if (!numeroInput) return;

    numeroInput.addEventListener('input', function(e) {
        // Solo permitir números
        let valor = e.target.value.replace(/[^0-9]/g, '');

        // Limitar a 20 caracteres
        if (valor.length > 20) {
            valor = valor.substring(0, 20);
        }

        e.target.value = valor;
    });

    console.log("✅ Formateo automático de número corporativo configurado");
}

// ✅ Inicializar cuando el DOM esté listo
$(document).ready(function() {
    configurarFormateoNumeroCorporativo();
    console.log("✅ numeroCorporativoManager.js inicializado");
});
