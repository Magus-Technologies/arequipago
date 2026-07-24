/**
 * gruposVariantes.js
 * Módulo de gestión de variantes de grupos de financiamiento
 */

// Variables globales para manejo de variantes
let currentVariantes = [];
let selectedVarianteId = null;
let contadorVariantes = 1;

/**
 * Cargar variantes de un grupo desde el servidor
 * @param {number} idGrupo - ID del grupo de financiamiento
 * @returns {Promise<Array>} - Array de variantes
 */
async function cargarVariantesGrupo(idGrupo) {
    try {
        const response = await $.ajax({
            url: '/getVariantesGrupo',
            type: 'POST',
            data: { idplan_financiamiento: idGrupo },
            dataType: 'json'
        });
        
        if (response.status === 'success' && response.variantes && response.variantes.length > 0) {
            currentVariantes = response.variantes.map(v => ({
                ...v,
                es_nueva: false
            }));
            renderVariantes(currentVariantes);
            return currentVariantes;
        }
        
        currentVariantes = [];
        $('#variantesContainer').html('<p class="text-muted">No hay variantes asociadas a este grupo.</p>');
        return [];
    } catch (error) {
        console.error('Error al cargar variantes:', error);
        return [];
    }
}

/**
 * Renderizar variantes en el contenedor
 * @param {Array} variantes - Array de variantes a renderizar
 */
function renderVariantes(variantes) {
    const container = $('#variantesContainer');
    container.empty();

    if (variantes.length === 0) {
        container.html('<p class="text-muted">No hay variantes asociadas a este grupo.</p>');
        return;
    }

    variantes.forEach((variante) => {
        const esNueva = variante.es_nueva === true;
        const identificador = esNueva ? variante.temp_id : variante.idgrupos_variantes;

        const botonesAccion = esNueva ?
            `<button class="delete-btn" type="button" onclick="eliminarVarianteEdicion('${identificador}')">❌</button>` :
            `<div style="display:flex;gap:4px;">
                <button class="btn btn-sm btn-edit-variante" title="Editar variante">
                    <i class="fas fa-pencil"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger" title="Eliminar variante" onclick="eliminarVarianteExistente(${variante.idgrupos_variantes})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>`;

        const card = `
            <div class="variante-card" data-id="${variante.idgrupos_variantes || ''}" data-temp-id="${identificador}">
                <div class="variante-header">
                    <h5>${variante.nombre_variante}</h5>
                    ${botonesAccion}
                </div>
                <div class="variante-body">
                    <p><strong>Cuota inicial:</strong> ${variante.cuota_inicial ? `${variante.moneda} ${variante.cuota_inicial}` : 'Sin cuota inicial'}</p>
                    <p><strong>Monto cuota:</strong> ${variante.monto_cuota ? `${variante.moneda} ${variante.monto_cuota}` : 'No especificado'}</p>
                    <p><strong>Número cuotas:</strong> ${variante.cantidad_cuotas || 'No especificado'}</p>
                    <p><strong>Tasa interés:</strong> ${variante.tasa_interes ? `${variante.tasa_interes}%` : 'No especificado'}</p>
                </div>
            </div>`;

        container.append(card);
    });
}

/**
 * Abrir modal para agregar nueva variante
 */
function abrirModalVariante() {
    // Limpiar todos los campos del modal
    restaurarModalVariante();
    $('#formVariante')[0].reset();
    $('#nombre_variante').val(`Variante ${contadorVariantes}`);
    $('#moneda_var').val('S/.');
    
    // Verificar si es financiamiento vehicular
    const tipoVehicular = getTipoVehicular();
    const infoDiv = $('#infoFinanciamientoVar');
    const tipoInfo = $('#tipoVehicularInfo');
    const fechasDiv = $('#fechasVariante');

    if (tipoVehicular) {
        infoDiv.show();
        tipoInfo.text(tipoVehicular === 'auto' ? 'Vehículo (Auto)' : 'Motocicleta');
        fechasDiv.css('display', 'flex');

        // Copiar fechas del formulario principal si existen
        const fechaInicioPrincipal = $('#fecha_inicio').val();
        const fechaFinPrincipal = $('#fecha_fin').val();
        if (fechaInicioPrincipal) $('#fecha_inicio_var').val(fechaInicioPrincipal);
        if (fechaFinPrincipal) $('#fecha_fin_var').val(fechaFinPrincipal);
    } else {
        infoDiv.hide();
        fechasDiv.hide();
    }

    const modal = new bootstrap.Modal(document.getElementById('modalVariante'));
    modal.show();
}

/**
 * Guardar nueva variante
 */
function guardarVariante() {
    if (!validarFormularioVariante()) {
        return;
    }

    const tipoVehicular = getTipoVehicular();
    const variante = {
        nombre_variante: $('#nombre_variante').val(),
        cuota_inicial: $('#cuota_inicial_var').val() || null,
        monto_inscripcion: $('#monto_inscripcion_var').val() || null,
        monto_cuota: $('#monto_cuota_var').val() || null,
        cantidad_cuotas: $('#cantidad_cuotas_var').val() || null,
        frecuencia_pago: $('#frecuencia_pago_var').val(),
        moneda: $('#moneda_var').val(),
        tasa_interes: $('#tasa_interes_var').val() || null,
        monto: $('#monto_var').val() || null,
        monto_sin_interes: $('#monto_sin_interes_var').val() || null,
        fecha_inicio: tipoVehicular ? $('#fecha_inicio_var').val() : null,
        fecha_fin: tipoVehicular ? $('#fecha_fin_var').val() : null,
        monto_comision: $('#monto_comision_var').val() || null,
        moneda_comision: $('#moneda_comision_var').val() || 'S/.',
        es_nueva: true,
        temp_id: 'temp_' + Date.now() + '_' + Math.random()
    };

    currentVariantes.push(variante);
    renderVariantes(currentVariantes);
    contadorVariantes++;

    bootstrap.Modal.getInstance(document.getElementById('modalVariante')).hide();
}

/**
 * Editar variante existente
 * @param {number} idVariante - ID de la variante a editar
 */
function editarVariante(idVariante) {
    selectedVarianteId = idVariante;
    const variante = currentVariantes.find(v => v.idgrupos_variantes == idVariante);
    
    if (!variante) return;

    // Limpiar y llenar el formulario
    $('#formVariante')[0].reset();
    $('#nombre_variante').val(variante.nombre_variante);
    $('#cuota_inicial_var').val(variante.cuota_inicial);
    $('#monto_inscripcion_var').val(variante.monto_inscripcion);
    $('#monto_cuota_var').val(variante.monto_cuota);
    $('#cantidad_cuotas_var').val(variante.cantidad_cuotas);
    $('#frecuencia_pago_var').val(variante.frecuencia_pago);
    $('#moneda_var').val(variante.moneda);
    $('#tasa_interes_var').val(variante.tasa_interes);
    $('#monto_var').val(variante.monto);
    $('#monto_sin_interes_var').val(variante.monto_sin_interes);
    $('#monto_comision_var').val(variante.monto_comision);
    $('#moneda_comision_var').val(variante.moneda_comision || 'S/.');

    // Verificar y mostrar información de financiamiento vehicular
    const tipoVehicular = getTipoVehicular();
    const infoDiv = $('#infoFinanciamientoVar');
    const tipoInfo = $('#tipoVehicularInfo');
    const fechasDiv = $('#fechasVariante');

    if (tipoVehicular) {
        infoDiv.show();
        tipoInfo.text(tipoVehicular === 'auto' ? 'Vehículo (Auto)' : 'Motocicleta');
        fechasDiv.css('display', 'flex');
        
        if (variante.fecha_inicio) $('#fecha_inicio_var').val(variante.fecha_inicio);
        if (variante.fecha_fin) $('#fecha_fin_var').val(variante.fecha_fin);
    } else {
        infoDiv.hide();
        fechasDiv.hide();
    }

    // Cambiar título y botón del modal
    $('#modalVarianteLabel').text('Editar Variante');
    $('#btnGuardarVariante').hide();
    
    // Eliminar botón anterior si existe
    $('#btnGuardarCambiosVariante').remove();
    
    // Crear nuevo botón de guardar cambios
    const nuevoBoton = $('<button type="button" id="btnGuardarCambiosVariante" class="btn btn-primary">Guardar Cambios</button>');
    $('#btnGuardarVariante').parent().append(nuevoBoton);

    // Mostrar modal
    const modalVariante = new bootstrap.Modal(document.getElementById('modalVariante'));
    modalVariante.show();
}

/**
 * Actualizar variante existente en el servidor
 */
function actualizarVariante() {
    if (!validarFormularioVariante()) {
        return;
    }

    const tipoVehicular = getTipoVehicular();
    const formData = {
        id: selectedVarianteId,
        idplan_financiamiento: selectedPlanId,
        nombre_variante: $('#nombre_variante').val().trim(),
        cuota_inicial: $('#cuota_inicial_var').val() || null,
        monto_inscripcion: $('#monto_inscripcion_var').val() || null,
        monto_cuota: $('#monto_cuota_var').val() || null,
        cantidad_cuotas: $('#cantidad_cuotas_var').val() || null,
        frecuencia_pago: $('#frecuencia_pago_var').val() || null,
        moneda: $('#moneda_var').val(),
        monto: $('#monto_var').val() || null,
        monto_sin_interes: $('#monto_sin_interes_var').val() || null,
        tasa_interes: $('#tasa_interes_var').val() || null,
        tipo_vehicular: tipoVehicular,
        monto_comision: $('#monto_comision_var').val() || null,
        moneda_comision: $('#moneda_comision_var').val() || 'S/.'
    };

    if ($('#fechasVariante').is(':visible')) {
        formData.fecha_inicio = $('#fecha_inicio_var').val() || null;
        formData.fecha_fin = $('#fecha_fin_var').val() || null;
    } else {
        formData.fecha_inicio = null;
        formData.fecha_fin = null;
    }

    $.ajax({
        url: '/updateVariante',
        type: 'POST',
        data: formData,
        success: function (response) {
            try {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    const index = currentVariantes.findIndex(v => v.idgrupos_variantes == selectedVarianteId);
                    if (index !== -1) {
                        currentVariantes[index] = { ...currentVariantes[index], ...formData };
                    }

                    renderVariantes(currentVariantes);
                    bootstrap.Modal.getInstance(document.getElementById('modalVariante')).hide();

                    Swal.fire({
                        icon: "success",
                        title: "Éxito",
                        text: "La variante ha sido actualizada correctamente.",
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: result.message
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Hubo un problema al procesar la respuesta del servidor."
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Error de conexión al servidor."
            });
        }
    });
}

/**
 * Eliminar variante nueva (local, antes de guardar el grupo)
 * @param {string} identificador - Identificador único temporal de la variante
 */
function eliminarVarianteEdicion(identificador) {
    const index = currentVariantes.findIndex(variante => {
        if (variante.es_nueva === true) {
            return variante.temp_id === identificador;
        }
        return false;
    });

    if (index !== -1) {
        currentVariantes.splice(index, 1);
        renderVariantes(currentVariantes);
    }
}

/**
 * Restaurar modal de variante a su estado original
 */
function restaurarModalVariante() {
    $('#btnGuardarCambiosVariante').remove();
    $('#btnGuardarVariante').show();
    $('#modalVarianteLabel').text('Agregar Variante');
    selectedVarianteId = null;
}

/**
 * Eliminar variante existente (guardada en BD) con confirmación
 * @param {number} idVariante - ID de la variante en grupos_variantes
 */
function eliminarVarianteExistente(idVariante) {
    Swal.fire({
        title: '¿Eliminar variante?',
        text: 'Esta acción eliminará permanentemente la variante del sistema.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fas fa-trash me-2"></i>Sí, eliminar',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/eliminarVariante',
                type: 'POST',
                data: { id: idVariante },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        const index = currentVariantes.findIndex(v => v.idgrupos_variantes == idVariante);
                        if (index !== -1) {
                            currentVariantes.splice(index, 1);
                        }
                        renderVariantes(currentVariantes);
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminada',
                            text: 'La variante ha sido eliminada correctamente.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'No se pudo eliminar la variante.'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de conexión al servidor.'
                    });
                }
            });
        }
    });
}

/**
 * Limpiar variantes al cancelar o finalizar edición
 */
function limpiarVariantes() {
    currentVariantes = [];
    contadorVariantes = 1;
    $('#variantesContainer').empty();
}
