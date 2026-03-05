/**
 * gruposValidaciones.js
 * Módulo de validaciones para grupos de financiamiento
 */

/**
 * Validar un campo individual del formulario
 * @param {string} campo - Nombre del campo a validar
 * @returns {boolean} - true si es válido, false si no
 */
function validarCampo(campo) {
    const valor = document.getElementById(campo).value.trim();
    const errorElement = document.getElementById('error_' + campo);
    const inputElement = document.getElementById(campo);

    // Campos que no son obligatorios
    if (campo === 'cuota_inicial' || campo === 'monto_cuota' || campo === 'cantidad_cuotas') {
        if (errorElement) errorElement.style.display = 'none';
        if (inputElement) inputElement.classList.remove('is-invalid');
        return true;
    }

    if (!valor) {
        if (errorElement) errorElement.style.display = 'block';
        if (inputElement) inputElement.classList.add('is-invalid');
        return false;
    } else {
        if (errorElement) errorElement.style.display = 'none';
        if (inputElement) inputElement.classList.remove('is-invalid');
        return true;
    }
}

/**
 * Validar fechas del formulario principal
 * Solo valida para financiamiento vehicular tipo "auto"
 * @returns {boolean} - true si es válido, false si no
 */
function validarFechas() {
    const tipoVehicular = getTipoVehicular();

    // Solo validar fechas para tipo "auto"
    if (!tipoVehicular || tipoVehicular === 'moto') {
        return true;
    }

    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin = document.getElementById('fecha_fin').value;
    const errorInicio = document.getElementById('error_fecha_inicio');
    const errorFin = document.getElementById('error_fecha_fin');
    const inputInicio = document.getElementById('fecha_inicio');
    const inputFin = document.getElementById('fecha_fin');

    let isValid = true;

    if (!fechaInicio) {
        if (errorInicio) errorInicio.style.display = 'block';
        if (inputInicio) inputInicio.classList.add('is-invalid');
        isValid = false;
    } else {
        if (errorInicio) errorInicio.style.display = 'none';
        if (inputInicio) inputInicio.classList.remove('is-invalid');
    }

    if (!fechaFin) {
        if (errorFin) errorFin.style.display = 'block';
        if (inputFin) inputFin.classList.add('is-invalid');
        isValid = false;
    } else {
        if (errorFin) errorFin.style.display = 'none';
        if (inputFin) inputFin.classList.remove('is-invalid');
    }

    if (fechaInicio && fechaFin && fechaFin < fechaInicio) {
        if (errorFin) {
            errorFin.textContent = 'La fecha de fin no puede ser anterior a la fecha de inicio';
            errorFin.style.display = 'block';
        }
        if (inputFin) inputFin.classList.add('is-invalid');
        isValid = false;
    }

    return isValid;
}

/**
 * Validar fechas en el modal de variantes
 * Solo valida para financiamiento vehicular tipo "auto"
 * @returns {boolean} - true si es válido, false si no
 */
function validarFechasVariante() {
    const tipoVehicular = getTipoVehicular();

    // Solo validar fechas para tipo "auto"
    if (!tipoVehicular || tipoVehicular === 'moto') {
        return true;
    }

    const fechaInicio = document.getElementById('fecha_inicio_var').value;
    const fechaFin = document.getElementById('fecha_fin_var').value;

    if (fechaInicio && fechaFin && fechaFin < fechaInicio) {
        Swal.fire({
            icon: "warning",
            title: "Fechas inválidas",
            text: "La fecha de fin no puede ser anterior a la fecha de inicio."
        });
        return false;
    }

    return true;
}

/**
 * Validar todos los campos obligatorios del formulario
 * @returns {boolean} - true si todos son válidos, false si alguno falla
 */
function validarFormularioCompleto() {
    const campos = ['nombre_plan', 'frecuencia_pago', 'tasa_interes'];
    let isValid = true;

    campos.forEach(campo => {
        if (!validarCampo(campo)) {
            isValid = false;
        }
    });

    if (!validarFechas()) {
        isValid = false;
    }

    return isValid;
}

/**
 * Limpiar todos los mensajes de error del formulario
 */
function limpiarErrores() {
    const errores = document.querySelectorAll('.error-message');
    errores.forEach(error => {
        error.style.display = 'none';
    });

    const inputs = document.querySelectorAll('.is-invalid');
    inputs.forEach(input => {
        input.classList.remove('is-invalid');
    });
}

/**
 * Validar formulario de variante antes de guardar
 * @returns {boolean} - true si es válido, false si no
 */
function validarFormularioVariante() {
    const nombreVariante = $('#nombre_variante').val().trim();
    
    if (!nombreVariante) {
        Swal.fire({
            icon: 'warning',
            title: 'Campo requerido',
            text: 'El nombre de la variante es obligatorio'
        });
        return false;
    }

    return validarFechasVariante();
}
