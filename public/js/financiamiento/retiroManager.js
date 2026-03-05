/**
 * Gestor de Retiros de Financiamientos
 * Solo aplica para planes 19 (CrediGo auto Grupo 3) y 38 (CrediGo Autos Grupo 4)
 */

// Función para mostrar modal de retiro
function mostrarModalRetiro(idFinanciamiento) {
    // Calcular penalidad
    $.ajax({
        url: _URL + '/ajs/retiro/calcularPenalidad',
        type: 'POST',
        data: { id_financiamiento: idFinanciamiento },
        dataType: 'json',
        beforeSend: function() {
            Swal.fire({
                title: 'Calculando penalidad...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(response) {
            Swal.close();
            
            if (response.success) {
                const data = response.data;
                
                // Construir HTML del modal
                let htmlComision = '';
                if (data.comision_asesor.existe) {
                    const estadoComision = data.comision_asesor.estado_comision;
                    const montoComision = parseFloat(data.comision_asesor.monto_comision).toFixed(2);
                    
                    if (estadoComision === 'pagada') {
                        htmlComision = `
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Comisión ya pagada:</strong><br>
                                Asesor: ${data.comision_asesor.nombre_asesor}<br>
                                Monto: ${data.comision_asesor.moneda} ${montoComision}<br>
                                <small>Se creará un descuento en su próximo pago de comisión</small>
                            </div>
                        `;
                    } else if (estadoComision === 'pendiente') {
                        htmlComision = `
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Comisión pendiente:</strong><br>
                                Asesor: ${data.comision_asesor.nombre_asesor}<br>
                                Monto: ${data.comision_asesor.moneda} ${montoComision}<br>
                                <small>Se cancelará automáticamente</small>
                            </div>
                        `;
                    }
                }

                Swal.fire({
                    title: '<i class="fas fa-sign-out-alt me-2"></i>Procesar Retiro de Financiamiento',
                    html: `
                        <div class="text-start">
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Información del Conductor</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1"><strong>Nombre:</strong> ${data.nombre_conductor}</p>
                                    <p class="mb-1"><strong>Documento:</strong> ${data.nro_documento}</p>
                                    <p class="mb-0"><strong>Plan:</strong> ${data.nombre_plan}</p>
                                </div>
                            </div>

                            <div class="card mb-3">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Cálculo de Penalidad</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1"><strong>Monto del Certificado:</strong> ${data.moneda} ${data.monto_total}</p>
                                    <p class="mb-1"><strong>Fecha de Inicio:</strong> ${data.fecha_inicio}</p>
                                    <p class="mb-1"><strong>Tiempo de Permanencia:</strong> ${data.meses_permanencia} meses</p>
                                    <hr>
                                    <h5 class="text-danger mb-0">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        Penalidad: ${data.moneda} ${data.penalidad}
                                    </h5>
                                </div>
                            </div>

                            ${htmlComision}

                            <div class="form-group">
                                <label for="observaciones-retiro" class="form-label">
                                    <i class="fas fa-comment me-2"></i>Observaciones (opcional):
                                </label>
                                <textarea id="observaciones-retiro" class="form-control" rows="3" 
                                          placeholder="Ingrese observaciones sobre el retiro..."></textarea>
                            </div>

                            <div class="alert alert-danger mt-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Advertencia:</strong> Esta acción no se puede deshacer. 
                                El financiamiento será marcado como "Retirado" y se generará la deuda de penalidad.
                            </div>
                        </div>
                    `,
                    width: '700px',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-check me-2"></i>Confirmar Retiro',
                    cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar',
                    customClass: {
                        popup: 'swal-wide'
                    },
                    preConfirm: () => {
                        return {
                            id_financiamiento: idFinanciamiento,
                            observaciones: document.getElementById('observaciones-retiro').value
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        procesarRetiroFinanciamiento(result.value);
                    }
                });
                
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo calcular la penalidad',
                    confirmButtonColor: '#02a499'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al calcular la penalidad: ' + error,
                confirmButtonColor: '#02a499'
            });
        }
    });
}

// Función para procesar el retiro
function procesarRetiroFinanciamiento(datos) {
    $.ajax({
        url: _URL + '/ajs/retiro/procesarRetiro',
        type: 'POST',
        data: datos,
        dataType: 'json',
        beforeSend: function() {
            Swal.fire({
                title: 'Procesando retiro...',
                html: 'Por favor espere mientras se procesa el retiro del financiamiento',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Retiro Procesado!',
                    html: `
                        <div class="text-start">
                            <p><strong>Penalidad aplicada:</strong> $ ${response.data.penalidad}</p>
                            <p><strong>Meses de permanencia:</strong> ${response.data.meses_permanencia}</p>
                            ${response.data.comision_descontada ? 
                                '<p class="text-warning"><i class="fas fa-info-circle me-2"></i>Se ha creado un descuento de comisión para el asesor</p>' : 
                                '<p class="text-info"><i class="fas fa-info-circle me-2"></i>Comisión cancelada</p>'
                            }
                        </div>
                    `,
                    confirmButtonColor: '#02a499'
                }).then(() => {
                    // Recargar la lista de clientes
                    if (typeof cargarClientesFinanciamiento === 'function') {
                        cargarClientesFinanciamiento();
                    }
                    
                    // Cerrar modal de detalles si está abierto
                    $('#financingDetailsModal').modal('hide');
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo procesar el retiro',
                    confirmButtonColor: '#02a499'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al procesar el retiro: ' + error,
                confirmButtonColor: '#02a499'
            });
        }
    });
}

// Función para verificar si un financiamiento puede retirarse
function puedeRetirarse(grupoFinanciamiento, estadoRetiro) {
    // Solo planes 19 (CrediGo auto Grupo 3) y 38 (CrediGo Autos Grupo 4)
    const planesPermitidos = [19, 38];
    const grupo = parseInt(grupoFinanciamiento);
    
    return planesPermitidos.includes(grupo) && estadoRetiro !== 'retirado';
}

// Función para mostrar lista de financiamientos retirados
function mostrarFinanciamientosRetirados() {
    $.ajax({
        url: _URL + '/ajs/retiro/listarRetirados',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            Swal.fire({
                title: 'Cargando...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(response) {
            Swal.close();
            
            if (response.success && response.data.length > 0) {
                let htmlTabla = `
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>ID</th>
                                    <th>Conductor</th>
                                    <th>Plan</th>
                                    <th>Meses</th>
                                    <th>Penalidad</th>
                                    <th>Fecha Retiro</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                response.data.forEach(function(item) {
                    htmlTabla += `
                        <tr>
                            <td>${item.idfinanciamiento}</td>
                            <td>${item.nombre_conductor}<br><small class="text-muted">${item.nro_documento}</small></td>
                            <td>${item.nombre_plan}</td>
                            <td>${item.meses_permanencia}</td>
                            <td class="text-danger"><strong>${item.moneda} ${parseFloat(item.monto_penalidad).toFixed(2)}</strong></td>
                            <td>${item.fecha_retiro}</td>
                            <td><small>${item.usuario_proceso}</small></td>
                        </tr>
                    `;
                });

                htmlTabla += `
                            </tbody>
                        </table>
                    </div>
                `;

                Swal.fire({
                    title: '<i class="fas fa-list me-2"></i>Financiamientos Retirados',
                    html: htmlTabla,
                    width: '900px',
                    confirmButtonText: 'Cerrar',
                    confirmButtonColor: '#02a499'
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Sin registros',
                    text: 'No hay financiamientos retirados',
                    confirmButtonColor: '#02a499'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al cargar los financiamientos retirados: ' + error,
                confirmButtonColor: '#02a499'
            });
        }
    });
}
