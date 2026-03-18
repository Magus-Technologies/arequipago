// ========================================
// CUOTAS ADELANTADAS PARA PLANES ESPECIALES
// ========================================
// Este archivo maneja la lógica de "Cuotas Adelantadas" para:
// - Plan ID 22: CREDI MOTOS
// - Plan ID 38: CrediGo Autos Grupo 4
// - Plan ID 49: Credi Ahorros Autos
// En lugar de ingresar un monto, el usuario ingresa cantidad de cuotas
// ========================================

/**
 * Configurar el campo "Cuota Inicial" como "Cuotas Adelantadas"
 * Se activa para planes: 22 (CREDI MOTOS), 38 (CrediGo Autos Grupo 4), 49 (Credi Ahorros Autos)
 */
function configurarCuotasAdelantadasCrediMotos() {
    const planId = planGlobal ? parseInt(planGlobal.idplan_financiamiento) : null;
    const nombrePlan = planId === 22 ? "CREDI MOTOS" : planId === 38 ? "CrediGo Autos Grupo 4" : planId === 49 ? "Credi Ahorros Autos" : "Plan Especial";
    
    console.log(`🚗 ${nombrePlan} - Configurando campo de Cuotas Adelantadas`);

    // 1. Cambiar el label del campo
    const label = document.querySelector('label[for="cuotaInicial"]');
    if (label) {
        const textoAdicional = planId === 49 
            ? '(cuotas a pagar, se aplican al final)' 
            : '(cantidad de cuotas a pagar ahora)';
        label.innerHTML = `
            Cuotas Adelantadas
            <small class="text-muted">${textoAdicional}</small>
        `;
        label.setAttribute('data-original-text', 'Cuota Inicial'); // Guardar texto original
    }

    // 2. Cambiar el placeholder del input
    const input = document.getElementById("cuotaInicial");
    if (input) {
        input.setAttribute('data-original-placeholder', input.placeholder); // Guardar placeholder original
        input.placeholder = "Ej: 2 (cuotas a adelantar)";
        input.value = ""; // Limpiar el valor

        // 3. Agregar atributo para identificar que está en modo "cuotas adelantadas"
        input.setAttribute('data-modo-cuotas-adelantadas', 'true');

        // 4. Cambiar el tipo de input a number para facilitar la entrada
        input.type = "number";
        input.min = "0";
        input.step = "1";

        console.log("✅ Campo configurado como 'Cuotas Adelantadas'");
    }

    // 5. Agregar event listener para calcular el monto automáticamente
    agregarListenerCuotasAdelantadas();
}

/**
 * Restaurar el campo "Cuotas Adelantadas" a "Cuota Inicial" normal
 * Se ejecuta cuando se cambia a otro plan que no sea ID 22
 */
function restaurarCuotaInicialNormal() {
    const input = document.getElementById("cuotaInicial");

    // Solo restaurar si está en modo "cuotas adelantadas"
    if (!input || input.getAttribute('data-modo-cuotas-adelantadas') !== 'true') {
        return;
    }

    console.log("🔄 Restaurando campo a 'Cuota Inicial' normal");

    // 1. Restaurar el label
    const label = document.querySelector('label[for="cuotaInicial"]');
    if (label) {
        const originalText = label.getAttribute('data-original-text') || 'Cuota Inicial';
        label.textContent = originalText;
    }

    // 2. Restaurar el placeholder
    if (input) {
        const originalPlaceholder = input.getAttribute('data-original-placeholder') || 'Cuota inicial';
        input.placeholder = originalPlaceholder;

        // 3. Restaurar el tipo de input
        input.type = "text";
        input.removeAttribute('min');
        input.removeAttribute('step');

        // 4. Remover el atributo de modo cuotas adelantadas
        input.removeAttribute('data-modo-cuotas-adelantadas');

        console.log("✅ Campo restaurado a 'Cuota Inicial' normal");
    }

    // 5. Remover event listener específico
    removerListenerCuotasAdelantadas();
}

/**
 * Agregar event listener para calcular monto desde cantidad de cuotas
 */
function agregarListenerCuotasAdelantadas() {
    const input = document.getElementById("cuotaInicial");
    if (!input) return;

    // Remover listener anterior si existe
    if (window.cuotasAdelantadasListener) {
        input.removeEventListener('input', window.cuotasAdelantadasListener);
    }

    // Crear nuevo listener
    window.cuotasAdelantadasListener = function(e) {
        const cantidadCuotas = parseInt(e.target.value) || 0;

        // Validar que sea un número positivo
        if (cantidadCuotas < 0) {
            e.target.value = 0;
            return;
        }

        console.log("🏍️ Cantidad de cuotas adelantadas:", cantidadCuotas);

        // Calcular el monto basado en el valor de la cuota
        if (planGlobal && planGlobal.monto_cuota) {
            const valorCuota = parseFloat(planGlobal.monto_cuota);
            const montoCalculado = cantidadCuotas * valorCuota;

            // Guardar el monto calculado como atributo data
            e.target.setAttribute('data-monto-calculado', montoCalculado.toFixed(2));

            console.log(`💰 Monto calculado: ${cantidadCuotas} × ${valorCuota} = ${montoCalculado.toFixed(2)}`);

            // Mostrar el monto calculado en algún lugar visible (opcional)
            mostrarMontoCalculado(montoCalculado);
        }
    };

    // Agregar el listener
    input.addEventListener('input', window.cuotasAdelantadasListener);
    console.log("✅ Listener de cuotas adelantadas agregado");
}

/**
 * Remover event listener de cuotas adelantadas
 */
function removerListenerCuotasAdelantadas() {
    const input = document.getElementById("cuotaInicial");
    if (input && window.cuotasAdelantadasListener) {
        input.removeEventListener('input', window.cuotasAdelantadasListener);
        window.cuotasAdelantadasListener = null;
        console.log("✅ Listener de cuotas adelantadas removido");
    }
}

/**
 * Mostrar el monto calculado al usuario (opcional)
 * Muestra un pequeño texto debajo del campo indicando el monto
 */
function mostrarMontoCalculado(monto) {
    const input = document.getElementById("cuotaInicial");
    if (!input) return;

    const contenedor = input.closest('.col-md-4');
    if (!contenedor) return;

    // Buscar o crear el elemento de ayuda
    let ayuda = contenedor.querySelector('.monto-calculado-ayuda');

    if (!ayuda) {
        ayuda = document.createElement('small');
        ayuda.className = 'monto-calculado-ayuda text-muted mt-1 d-block';
        contenedor.appendChild(ayuda);
    }

    // Obtener la moneda del plan
    const moneda = planGlobal && planGlobal.moneda === "$" ? "$" : "S/.";

    if (monto > 0) {
        ayuda.innerHTML = `
            <i class="fas fa-calculator me-1"></i>
            Monto a pagar ahora: <strong>${moneda} ${monto.toFixed(2)}</strong>
        `;
        ayuda.style.color = '#28a745';
    } else {
        ayuda.innerHTML = `
            <i class="fas fa-info-circle me-1"></i>
            Sin cuotas adelantadas
        `;
        ayuda.style.color = '#6c757d';
    }
}

/**
 * Obtener el monto de cuota inicial calculado
 * Esta función se llama desde otros archivos para obtener el monto correcto
 */
function obtenerMontoCuotaInicial() {
    const input = document.getElementById("cuotaInicial");

    // Si está en modo cuotas adelantadas (plan 22)
    if (input && input.getAttribute('data-modo-cuotas-adelantadas') === 'true') {
        const montoCalculado = input.getAttribute('data-monto-calculado');
        return parseFloat(montoCalculado) || 0;
    }

    // Si es modo normal (otros planes)
    const valor = input ? input.value : "0";
    return parseFloat(valor.replace(/[^\d.-]/g, "")) || 0;
}

// ========================================
// Exponer funciones globalmente
// ========================================
window.configurarCuotasAdelantadasCrediMotos = configurarCuotasAdelantadasCrediMotos;
window.restaurarCuotaInicialNormal = restaurarCuotaInicialNormal;
window.obtenerMontoCuotaInicial = obtenerMontoCuotaInicial;
