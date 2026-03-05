/**
 * gruposCRUD.js
 * Módulo de operaciones CRUD para grupos de financiamiento
 */

// Variable global para el ID del plan seleccionado en modo edición
let selectedPlanId = null;

/**
 * Registrar nuevo grupo de financiamiento
 */
function registrarGrupoFinanciamiento() {
    if (!validarFormularioCompleto()) {
        return;
    }

    const nombrePlan = $('#nombre_plan').val().trim();
    const cuotaInicial = $('#cuota_inicial').val().trim() || "0";
    const montoCuota = $('#monto_cuota').val().trim() || "0";
    const cantidadCuotas = $('#cantidad_cuotas').val().trim() || "0";
    const frecuenciaPago = $('#frecuencia_pago').val();
    const moneda = $('#moneda').val();
    const tasaInteres = $('#tasa_interes').val().trim();
    const monto = $('#monto').val().trim();
    const montoSinInteres = $('#monto_sin_interes').val().trim();
    const tipoVehicular = getTipoVehicular();
    const fechaInicio = $('#fecha_inicio').val();
    const fechaFin = $('#fecha_fin').val();
    const estado = $('#estadoActivo').is(':checked') ? 'activo' : 'inactivo';
    const esYango = $('#esYangoCheckbox').is(':checked') ? '1' : '0';
    const aplicaComision = $('#aplicaComisionCheckbox').is(':checked') ? 1 : 0;
    const montoComision = $('#monto_comision').val() || null;
    const monedaComision = $('#moneda_comision').val() || 'S/.';

    const formData = new FormData();
    formData.append("nombre_plan", nombrePlan);
    formData.append("cuota_inicial", cuotaInicial);
    formData.append("monto_cuota", montoCuota);
    formData.append("cantidad_cuotas", cantidadCuotas);
    formData.append("frecuencia_pago", frecuenciaPago);
    formData.append("moneda", moneda);
    formData.append("tasa_interes", tasaInteres);
    formData.append("monto", monto);
    formData.append("monto_sin_interes", montoSinInteres);
    formData.append("estado", estado);
    formData.append("cobrar_mora", getCobrarMora());
    formData.append("mora_semanal", $('#mora_semanal').val() || '');
    formData.append("mora_quincenal", $('#mora_quincenal').val() || '');
    formData.append("mora_mensual", $('#mora_mensual').val() || '');
    formData.append("es_yango", esYango);
    formData.append("aplica_comision", aplicaComision);
    formData.append("monto_comision", montoComision);
    formData.append("moneda_comision", monedaComision);

    if (tipoVehicular) {
        formData.append("tipo_vehicular", tipoVehicular);
        formData.append("fecha_inicio", fechaInicio);
        formData.append("fecha_fin", fechaFin);
    }

    if (currentVariantes.length > 0) {
        formData.append("variantes", JSON.stringify(currentVariantes));
    }

    fetch("/arequipago/save-newGroupFinance", {
        method: "POST",
        body: formData,
    })
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);

            if (data && typeof data.success !== 'undefined') {
                if (data.success) {
                    Swal.fire({
                        icon: "success",
                        title: "¡Éxito!",
                        text: "Grupo de financiamiento registrado correctamente.",
                    });
                    recargarTabla();
                    resetearFormulario();
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: data.message || "Hubo un problema al registrar.",
                    });
                }
            } else {
                throw new Error("Respuesta no válida del servidor");
            }
        } catch (parseError) {
            if (text.includes('"success":true') || text.includes('"success": true')) {
                Swal.fire({
                    icon: "success",
                    title: "¡Éxito!",
                    text: "Grupo de financiamiento registrado correctamente.",
                });
                recargarTabla();
                resetearFormulario();
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Respuesta del servidor no válida.",
                });
            }
        }
    })
    .catch(error => {
        Swal.fire({
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo conectar con el servidor.",
        });
    });
}

/**
 * Poblar formulario con datos del grupo para edición
 * @param {number} idGrupo - ID del grupo a editar
 * @param {Object} rowData - Datos de la fila del DataTable (opcional)
 */
async function poblarFormularioEdicion(idGrupo, rowData) {
    selectedPlanId = idGrupo;

    if (!selectedPlanId) {
        Swal.fire('Error', 'No se puede editar el plan porque falta el ID del plan', 'error');
        return;
    }

    const row = $(`tr[data-plan-id="${idGrupo}"]`);
    
    // Si no se pasó rowData, obtenerlo del DataTable
    if (!rowData && tabla) {
        rowData = tabla.row(row).data();
    }
    
    // Si aún no hay rowData, salir con error
    if (!rowData) {
        Swal.fire('Error', 'No se pudieron obtener los datos del grupo', 'error');
        return;
    }
    
    const cells = row.find('td');

    $('#nombre_plan').val(rowData.nombre_plan);
    $('#moneda').val(cells.eq(5).text());

    const cuotaInicial = cells.eq(1).text().split(' ')[1];
    $('#cuota_inicial').val(cuotaInicial);

    const montoCuota = cells.eq(2).text().split(' ')[1];
    $('#monto_cuota').val(montoCuota);

    $('#cantidad_cuotas').val(cells.eq(3).text());
    $('#frecuencia_pago').val(cells.eq(4).text());

    const montoText = cells.eq(6).text();
    $('#monto').val(montoText !== "N/A" ? montoText.split(' ')[1] : '');

    const montoSinInteresText = cells.eq(7).text();
    $('#monto_sin_interes').val(montoSinInteresText !== "N/A" ? montoSinInteresText.split(' ')[1] : '');

    const tasaInteres = cells.eq(8).text();
    $('#tasa_interes').val(tasaInteres !== "N/A" ? tasaInteres : '');

    const fechaInicio = cells.eq(9).text();
    const fechaFin = cells.eq(10).text();

    $('#fecha_inicio').val(fechaInicio !== "No especificado" && fechaInicio !== "Inicio dinámico" ? fechaInicio : '');
    $('#fecha_fin').val(fechaFin !== "No especificado" && fechaFin !== "Calculado automáticamente" ? fechaFin : '');

    // Obtener estado del plan
    $.ajax({
        url: '/arequipago/getEstadoPlan',
        type: 'POST',
        data: { idplan_financiamiento: selectedPlanId },
        dataType: 'json',
        success: function (result) {
            if (result.status === 'success') {
                $('#estadoActivo').prop('checked', result.estado === 'activo');
                toggleEstadoGrupo();
            }
        }
    });

    // Obtener cobrar_mora, es_yango y datos de comisión
    $.ajax({
        url: '/arequipago/obtenerPlanFinanciamiento',
        type: 'POST',
        data: { id_plan: selectedPlanId },
        dataType: 'json',
        success: function(result) {
            if (result.success && result.plan) {
                $('#cobrarMoraCheckbox').prop('checked', result.plan.cobrar_mora == 1);
                $('#mora_semanal').val(result.plan.mora_semanal || '');
                $('#mora_quincenal').val(result.plan.mora_quincenal || '');
                $('#mora_mensual').val(result.plan.mora_mensual || '');
                $('#esYangoCheckbox').prop('checked', result.plan.es_yango == 1);
                
                $('#aplicaComisionCheckbox').prop('checked', result.plan.aplica_comision == 1);
                
                if (result.plan.aplica_comision == 1) {
                    $('#camposComision').show();
                } else {
                    $('#camposComision').hide();
                }
                
                $('#monto_comision').val(result.plan.monto_comision || '');
                $('#moneda_comision').val(result.plan.moneda_comision || 'S/.');
            }
        }
    });
    
    // Cargar variantes UNA SOLA VEZ
    await cargarVariantesGrupo(selectedPlanId);

    // Obtener tipo vehicular
    $.ajax({
        url: '/arequipago/getTipoVehicular',
        type: 'POST',
        data: { idplan_financiamiento: selectedPlanId },
        dataType: 'json',
        success: function (result) {
            const esVehicularCheckbox = $('#esVehicularCheckbox');
            const radioAuto = $('#radioAuto');
            const radioMoto = $('#radioMoto');
            const fechasDiv = $('#fechasVehicular');

            if (result.status === 'success') {
                if (result.tipo_vehicular === 'vehiculo') {
                    esVehicularCheckbox.prop('checked', true);
                    radioAuto.prop('disabled', false).prop('checked', true);
                    radioMoto.prop('disabled', false);
                    fechasDiv.show();
                } else if (result.tipo_vehicular === 'moto') {
                    esVehicularCheckbox.prop('checked', true);
                    radioAuto.prop('disabled', false);
                    radioMoto.prop('disabled', false).prop('checked', true);
                    fechasDiv.show();
                } else {
                    esVehicularCheckbox.prop('checked', false);
                    radioAuto.prop('disabled', true).prop('checked', false);
                    radioMoto.prop('disabled', true).prop('checked', false);
                    fechasDiv.hide();
                }
            }
        }
    });

    activarModoEdicion();
}

/**
 * Activar modo edición en la interfaz
 */
function activarModoEdicion() {
    $("#tituloRegistro").hide();
    $("#btnRegistrar").hide();
    
    if (!$("#tituloEdicion").length) {
        const tituloEdicion = $('<h5 id="tituloEdicion" class="mb-4"><i class="fas fa-edit me-2"></i>Editar Grupo de Financiamiento</h5>');
        $("#tituloRegistro").after(tituloEdicion);
    }
    
    if (!$("#guardarCambios").length) {
        const botonesEdicion = $(`
            <div class="mt-4" id="contenedorBotonesEdicion">
                <button type="button" class="btn btn-success me-2" id="guardarCambios">
                    <i class="fas fa-save me-2"></i>Guardar Cambios
                </button>
                <button type="button" class="btn btn-secondary" id="cancelarEdicion">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
            </div>
        `);
        $("#btnRegistrar").parent().append(botonesEdicion);
    }
    
    $("#financiamientoTabs a[href='#planFinanciamiento']").tab("show");
}

/**
 * Guardar cambios del grupo editado
 */
function guardarCambiosGrupo() {
    const nombrePlan = $('#nombre_plan').val().trim();
    const moneda = $('#moneda').val().trim();

    if (!nombrePlan || !moneda) {
        Swal.fire('Error', 'El nombre del plan y la moneda son campos obligatorios', 'error');
        return;
    }

    if (!selectedPlanId) {
        Swal.fire('Error', 'No se puede editar el plan porque falta el ID del plan', 'error');
        return;
    }

    const formData = {
        id: selectedPlanId,
        nombre_plan: nombrePlan,
        cuota_inicial: $('#cuota_inicial').val() || null,
        monto_cuota: $('#monto_cuota').val() || null,
        cantidad_cuotas: $('#cantidad_cuotas').val() || null,
        frecuencia_pago: $('#frecuencia_pago').val() || null,
        moneda: moneda,
        monto: $('#monto').val() || null,
        monto_sin_interes: $('#monto_sin_interes').val() || null,
        tasa_interes: $('#tasa_interes').val() || null,
        fecha_inicio: $('#fecha_inicio').val() || null,
        fecha_fin: $('#fecha_fin').val() || null,
        tipo_vehicular: getTipoVehicular(),
        estado: $('#estadoActivo').is(':checked') ? 'activo' : 'inactivo',
        cobrar_mora: getCobrarMora(),
        mora_semanal: $('#mora_semanal').val() || null,
        mora_quincenal: $('#mora_quincenal').val() || null,
        mora_mensual: $('#mora_mensual').val() || null,
        es_yango: $('#esYangoCheckbox').is(':checked') ? 1 : 0,
        aplica_comision: $('#aplicaComisionCheckbox').is(':checked') ? 1 : 0,
        monto_comision: $('#monto_comision').val() || null,
        moneda_comision: $('#moneda_comision').val() || 'S/.',
        variantes: currentVariantes,
        nuevas_variantes: currentVariantes.filter(v => v.es_nueva === true)
    };

    $.ajax({
        url: '/arequipago/editGroup',
        type: 'POST',
        data: formData,
        success: function (response) {
            try {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    Swal.fire({
                        icon: "success",
                        title: "Éxito",
                        text: result.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        recargarTabla();
                        cancelarEdicion();
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: result.message,
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Hubo un problema al procesar la respuesta del servidor.",
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Error de conexión al servidor.",
            });
        }
    });
}

/**
 * Cancelar edición y volver a modo registro
 */
function cancelarEdicion() {
    resetearFormulario();
    
    $("#btnRegistrar").show();
    $("#guardarCambios, #cancelarEdicion").remove();
    $("#contenedorBotonesEdicion").remove();
    $("#tituloEdicion").remove();
    $("#tituloRegistro").show();
    
    $('#btnAgregarVariante').show();
    
    limpiarVariantes();
    limpiarErrores();
    
    restaurarModalVariante();
    
    selectedPlanId = null;
    
    $("#financiamientoTabs a[href='#asociarProducto']").tab("show");
}

/**
 * Eliminar grupo de financiamiento (Soft Delete - cambiar estado a inactivo)
 * @param {number} idGrupo - ID del grupo a eliminar
 */
function eliminarGrupoFinanciamiento(idGrupo) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "El grupo de financiamiento será desactivado y no aparecerá en la lista principal.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: '<i class="fas fa-ban me-2"></i>Sí, desactivar',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/arequipago/cambiarEstadoGrupo",
                type: "POST",
                data: { 
                    id: idGrupo,
                    estado: 'inactivo'
                },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Desactivado",
                            text: "Grupo de financiamiento desactivado correctamente.",
                            showConfirmButton: false,
                            timer: 1500
                        });
                        recargarTabla();
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: response.message || "No se pudo desactivar el grupo de financiamiento.",
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Hubo un problema con la solicitud.",
                    });
                }
            });
        }
    });
}

/**
 * ✅ NUEVA FUNCIÓN: Reactivar grupo de financiamiento
 * @param {number} idGrupo - ID del grupo a reactivar
 */
function reactivarGrupoFinanciamiento(idGrupo) {
    Swal.fire({
        title: "¿Reactivar grupo?",
        text: "El grupo volverá a aparecer en la lista principal.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: '<i class="fas fa-check me-2"></i>Sí, reactivar',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/arequipago/cambiarEstadoGrupo",
                type: "POST",
                data: { 
                    id: idGrupo,
                    estado: 'activo'
                },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Reactivado",
                            text: "Grupo de financiamiento reactivado correctamente.",
                            showConfirmButton: false,
                            timer: 1500
                        });
                        // Recargar modal de grupos eliminados
                        cargarGruposEliminados();
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: response.message || "No se pudo reactivar el grupo de financiamiento.",
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Hubo un problema con la solicitud.",
                    });
                }
            });
        }
    });
}

/**
 * ✅ NUEVA FUNCIÓN: Cargar grupos eliminados (inactivos)
 */
function cargarGruposEliminados() {
    $.ajax({
        url: "/arequipago/obtenerGruposInactivos",
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.success) {
                mostrarGruposEliminados(response.grupos);
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "No se pudieron cargar los grupos eliminados.",
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Hubo un problema al cargar los grupos eliminados.",
            });
        }
    });
}

/**
 * ✅ NUEVA FUNCIÓN: Mostrar modal con grupos eliminados
 */
function mostrarGruposEliminados(grupos) {
    const tbody = $('#tablaGruposEliminados tbody');
    tbody.empty();

    if (grupos.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="6" class="text-center text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No hay grupos eliminados</p>
                </td>
            </tr>
        `);
    } else {
        grupos.forEach(grupo => {
            tbody.append(`
                <tr>
                    <td>${grupo.idplan_financiamiento}</td>
                    <td>${grupo.nombre_plan}</td>
                    <td>${grupo.frecuencia_pago}</td>
                    <td>${grupo.cantidad_cuotas}</td>
                    <td>${grupo.moneda} ${parseFloat(grupo.monto_cuota).toFixed(2)}</td>
                    <td>
                        <button class="btn btn-sm btn-success" onclick="reactivarGrupoFinanciamiento(${grupo.idplan_financiamiento})">
                            <i class="fas fa-undo me-1"></i>Reactivar
                        </button>
                    </td>
                </tr>
            `);
        });
    }

    $('#modalGruposEliminados').modal('show');
}

/**
 * Obtener y mostrar detalles completos de un grupo
 * @param {number} idGrupo - ID del grupo
 */
function obtenerDetallesGrupo(idGrupo) {
    $.ajax({
        url: '/arequipago/getDetallesPlan',
        type: 'POST',
        data: { id: idGrupo },
        dataType: 'json',
        success: function (result) {
            if (result.status === 'success') {
                mostrarDetallesEnModal(result.plan, result.variantes);
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: result.message || 'El servidor devolvió una respuesta inesperada.'
                });
            }
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: "error",
                title: "Error de Comunicación",
                text: "No se pudo obtener el detalle del plan."
            });
        }
    });
}

/**
 * Mostrar detalles del grupo en modal
 * @param {Object} plan - Datos del plan
 * @param {Array} variantes - Array de variantes
 */
function mostrarDetallesEnModal(plan, variantes) {
    const esYango = plan.es_yango == 1;
    
    let nombrePlanHtml = plan.nombre_plan;
    if (esYango) {
        nombrePlanHtml = `<span class="badge bg-warning text-dark me-2">YANGO</span>${plan.nombre_plan}`;
    }
    $('#detalle-nombre-plan').html(nombrePlanHtml);
    
    $('#detalle-moneda').text(plan.moneda);
    $('#detalle-cuota-inicial').text(plan.cuota_inicial ? `${plan.moneda} ${plan.cuota_inicial}` : 'N/A');
    $('#detalle-monto-cuota').text(plan.monto_cuota ? `${plan.moneda} ${plan.monto_cuota}` : 'N/A');
    $('#detalle-cantidad-cuotas').text(plan.cantidad_cuotas || 'N/A');
    $('#detalle-frecuencia-pago').text(plan.frecuencia_pago || 'N/A');
    $('#detalle-tasa-interes').text(plan.tasa_interes ? `${plan.tasa_interes}%` : 'N/A');
    $('#detalle-monto').text(plan.monto ? `${plan.moneda} ${plan.monto}` : 'N/A');
    $('#detalle-monto-sin-interes').text(plan.monto_sin_interes ? `${plan.moneda} ${plan.monto_sin_interes}` : 'N/A');
    $('#detalle-tipo-vehicular').text(plan.tipo_vehicular || 'No especificado');
    $('#detalle-estado').text(plan.estado === 'activo' ? 'Visible' : 'Oculto');
    const morasTexto = [];
    if (plan.mora_semanal) morasTexto.push(`Semanal: ${plan.moneda} ${plan.mora_semanal}`);
    if (plan.mora_quincenal) morasTexto.push(`Quincenal: ${plan.moneda} ${plan.mora_quincenal}`);
    if (plan.mora_mensual) morasTexto.push(`Mensual: ${plan.moneda} ${plan.mora_mensual}`);
    $('#detalle-penalizacion-mora').text(morasTexto.length > 0 ? morasTexto.join(' | ') : 'Automática (según reglas del sistema)');
    
    if (esYango) {
        $('#detalle-fecha-inicio').html('<span class="text-muted fst-italic">Inicio dinámico (1 mes después del pago inicial)</span>');
        $('#detalle-fecha-fin').html('<span class="text-muted fst-italic">Calculado automáticamente según cronograma</span>');
    } else {
        $('#detalle-fecha-inicio').text(plan.fecha_inicio || 'No especificado');
        $('#detalle-fecha-fin').text(plan.fecha_fin || 'No especificado');
    }

    const variantesContainer = $('#detalle-variantes');
    variantesContainer.empty();

    if (variantes && variantes.length > 0) {
        variantes.forEach(variante => {
            const varianteHtml = `
                <div class="border rounded p-3 mb-3">
                    <h6 class="text-primary">${variante.nombre_variante}</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><small><strong>Cuota Inicial:</strong> ${variante.cuota_inicial ? `${variante.moneda} ${variante.cuota_inicial}` : 'N/A'}</small></p>
                            <p><small><strong>Monto Cuota:</strong> ${variante.monto_cuota ? `${variante.moneda} ${variante.monto_cuota}` : 'N/A'}</small></p>
                            <p><small><strong>Cantidad Cuotas:</strong> ${variante.cantidad_cuotas || 'N/A'}</small></p>
                        </div>
                        <div class="col-md-6">
                            <p><small><strong>Frecuencia:</strong> ${variante.frecuencia_pago || 'N/A'}</small></p>
                            <p><small><strong>Tasa Interés:</strong> ${variante.tasa_interes ? `${variante.tasa_interes}%` : 'N/A'}</small></p>
                            <p><small><strong>Monto Total:</strong> ${variante.monto ? `${variante.moneda} ${variante.monto}` : 'N/A'}</small></p>
                        </div>
                    </div>
                </div>
            `;
            variantesContainer.append(varianteHtml);
        });
    } else {
        variantesContainer.html('<p class="text-muted">No hay variantes asociadas a este plan.</p>');
    }

    const modal = new bootstrap.Modal(document.getElementById('modalDetalles'));
    modal.show();
}

/**
 * Resetear formulario a estado inicial
 */
function resetearFormulario() {
    $("#formFinanciamiento")[0].reset();
    resetearControlesUI();
    limpiarVariantes();
}
