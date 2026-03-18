/**
 * gruposCalculos.js
 * Módulo de cálculos financieros para grupos de financiamiento
 */

/**
 * Formatear fecha para input de tipo date (YYYY-MM-DD)
 * @param {Date} fecha - Objeto Date a formatear
 * @returns {string} - Fecha en formato YYYY-MM-DD
 */
function formatFechaInput(fecha) {
    return fecha.toISOString().split('T')[0];
}

/**
 * Formatear valor monetario según el tipo de moneda
 * @param {number} valor - Valor a formatear
 * @param {string} tipoMoneda - Tipo de moneda ("S/." o "$")
 * @returns {string} - Valor formateado con símbolo de moneda
 */
function formatMoneda(valor, tipoMoneda) {
    return tipoMoneda === 'S/.' ? `S/. ${valor.toFixed(2)}` : `$ ${valor.toFixed(2)}`;
}

/**
 * Calcular financiamiento principal
 * Realiza cálculos automáticos basados en los valores ingresados:
 * - Calcula monto total si hay monto sin interés + tasa
 * - Calcula monto sin interés si hay monto total + tasa
 * - Calcula monto de cuota si hay monto total + cantidad de cuotas
 * - Calcula fecha de fin según frecuencia de pago
 */
function calcularFinanciamiento() {
    // Obtener valores desde el formulario
    let montoTotal = parseFloat(document.getElementById('monto').value) || 0;
    let montoSinInteres = parseFloat(document.getElementById('monto_sin_interes').value) || 0;
    const cuotaInicial = parseFloat(document.getElementById('cuota_inicial').value) || 0;
    const tasaInteres = (parseFloat(document.getElementById('tasa_interes').value) || 0) / 100;
    let cantidadCuotas = parseInt(document.getElementById('cantidad_cuotas').value) || 0;
    let montoCuota = parseFloat(document.getElementById('monto_cuota').value) || 0;
    const frecuenciaPago = document.getElementById('frecuencia_pago').value;

    // Si montoTotal está vacío pero hay valores suficientes, calcularlo automáticamente
    if (montoTotal === 0 && montoSinInteres > 0 && cantidadCuotas > 0 && cuotaInicial >= 0) {
        montoTotal = montoSinInteres * (1 + tasaInteres);
        document.getElementById('monto').value = montoTotal.toFixed(2);
    }

    // Si se ingresan monto total y tasa de interés, calcular monto sin interés una sola vez
    if (montoTotal > 0 && tasaInteres > 0 && montoSinInteres === 0) {
        montoSinInteres = montoTotal / (1 + tasaInteres);
        document.getElementById('monto_sin_interes').value = montoSinInteres.toFixed(2);
    }

    // No recalcular montoTotal ni montoSinInteres cuando se ingresa cuota y cantidad de cuotas
    if (!(montoCuota > 0 && cantidadCuotas > 0)) {
        // Si se ingresan cuota inicial, monto de cuota y cantidad de cuotas (sin tasa), actualizar montoTotal
        if (cuotaInicial >= 0 && montoCuota > 0 && cantidadCuotas > 0 && montoTotal === 0) {
            montoTotal = (montoCuota * cantidadCuotas) + cuotaInicial;
            document.getElementById('monto').value = montoTotal.toFixed(2);
        }
    }

    // Si se ingresan monto total, cantidad de cuotas y frecuencia de pago, recalcular montoCuota
    if (montoTotal > 0 && cantidadCuotas > 0 && frecuenciaPago) {
        montoCuota = (montoTotal - cuotaInicial) / cantidadCuotas;
        document.getElementById('monto_cuota').value = montoCuota.toFixed(2);
    }

    // Calcular fechas de vencimiento si hay fecha de inicio y cantidad de cuotas
    const fechaInicio = document.getElementById('fecha_inicio').value;
    if (fechaInicio && cantidadCuotas > 0 && frecuenciaPago) {
        const fechaInicioObj = new Date(fechaInicio);
        fechaInicioObj.setDate(fechaInicioObj.getDate() + 1);

        const diasIntervalo = frecuenciaPago === 'semanal' ? 7 : (frecuenciaPago === 'quincenal' ? 15 : 30);
        let fechasVencimiento = [];

        for (let i = 1; i <= cantidadCuotas; i++) {
            const fechaVencimiento = new Date(fechaInicioObj);
            fechaVencimiento.setDate(fechaInicioObj.getDate() + (i * diasIntervalo));
            fechasVencimiento.push(fechaVencimiento);
        }

        // Actualizar automáticamente la fecha de fin
        const fechaFin = fechasVencimiento[fechasVencimiento.length - 1];
        document.getElementById('fecha_fin').value = formatFechaInput(fechaFin);
    }
}

/**
 * Calcular tasa de interés efectiva usando método de bisección
 * @param {number} montoFinanciado - Monto financiado (sin cuota inicial)
 * @param {number} montoCuota - Monto de cada cuota
 * @param {number} numeroCuotas - Número de cuotas
 * @returns {number} - Tasa de interés en porcentaje
 */
function calcularTasaInteresEfectivaModal(montoFinanciado, montoCuota, numeroCuotas) {
    if (montoFinanciado <= 0 || montoCuota <= 0 || numeroCuotas <= 0) {
        return 0;
    }

    // Método de aproximación numérica (bisección) para encontrar la tasa
    let tasaMin = 0; // 0%
    let tasaMax = 1; // 100%
    let precision = 0.0001; // Precisión del 0.01%
    let maxIteraciones = 100;

    for (let i = 0; i < maxIteraciones; i++) {
        let tasaMedia = (tasaMin + tasaMax) / 2;

        // Calcular el valor presente de las cuotas con la tasa media
        let valorPresente = 0;
        for (let j = 1; j <= numeroCuotas; j++) {
            valorPresente += montoCuota / Math.pow(1 + tasaMedia, j);
        }

        // Comparar con el monto financiado
        let diferencia = valorPresente - montoFinanciado;

        if (Math.abs(diferencia) < precision) {
            return tasaMedia * 100; // Retornar en porcentaje
        }

        if (diferencia > 0) {
            tasaMin = tasaMedia;
        } else {
            tasaMax = tasaMedia;
        }
    }

    return ((tasaMin + tasaMax) / 2) * 100; // Retornar en porcentaje
}

/**
 * Cálculos automáticos para el modal de variantes
 * Calcula monto total y tasa de interés efectiva
 */
function calculoModal() {
    // Obtener valores desde el formulario modal
    let precioBase = parseFloat(document.getElementById('monto_sin_interes_var').value) || 0;
    let montoCuota = parseFloat(document.getElementById('monto_cuota_var').value) || 0;
    let numeroCuotas = parseInt(document.getElementById('cantidad_cuotas_var').value) || 0;
    let montoInicial = parseFloat(document.getElementById('cuota_inicial_var').value) || 0;

    // Calcular el monto financiado
    let montoFinanciado = precioBase - montoInicial;

    // Calcular el total pagado en cuotas
    let totalPagadoCuotas = montoCuota * numeroCuotas;

    // Calcular el monto total (para mostrar en el campo correspondiente)
    let montoTotal = montoInicial + totalPagadoCuotas;
    document.getElementById('monto_var').value = montoTotal.toFixed(2);

    // Calcular la tasa de interés efectiva si tenemos todos los datos necesarios
    if (montoFinanciado > 0 && montoCuota > 0 && numeroCuotas > 0) {
        let tasaEfectiva = calcularTasaInteresEfectivaModal(montoFinanciado, montoCuota, numeroCuotas);
        document.getElementById('tasa_interes_var').value = tasaEfectiva.toFixed(2);
    } else {
        document.getElementById('tasa_interes_var').value = '';
    }
}
