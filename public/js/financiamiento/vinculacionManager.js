/**
 * Gestor de Vinculación de Financiamientos
 * Permite vincular dos financiamientos como un solo producto (ej: esposos comprando un vehículo)
 */

const VinculacionManager = {
    /**
     * Abrir modal para vincular financiamiento
     */
    abrirModalVincular: function(idFinanciamiento) {
        // Obtener financiamientos disponibles
        $.ajax({
            url: '/obtenerFinanciamientosDisponibles',
            type: 'GET',
            data: { id_financiamiento: idFinanciamiento },
            success: function(response) {
                if (response.success) {
                    VinculacionManager.mostrarModalVincular(idFinanciamiento, response.financiamientos);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'No se pudieron cargar los financiamientos disponibles'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar financiamientos disponibles'
                });
            }
        });
    },

    /**
     * Mostrar modal con lista de financiamientos disponibles
     */
    mostrarModalVincular: function(idPrincipal, financiamientos) {
        let html = `
            <div class="modal fade" id="modalVincularFinanciamiento" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-link"></i> Vincular Financiamiento
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                            <div class="alert alert-info mb-3" style="padding: 0.75rem;">
                                <i class="fas fa-info-circle"></i>
                                <strong>¿Qué es vincular?</strong> Permite unir dos financiamientos como un solo producto (ej: esposos comprando un vehículo). Solo el financiamiento principal contará en el stock.
                            </div>

                            ${financiamientos.length === 0 ? `
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    No hay financiamientos disponibles para vincular.
                                </div>
                            ` : `
                                <!-- Buscador -->
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" 
                                               id="buscarFinanciamiento" 
                                               class="form-control" 
                                               placeholder="Buscar por nombre, documento, producto o ID...">
                                    </div>
                                </div>

                                <!-- Tabla con scroll -->
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-hover table-sm" id="tablaFinanciamientos">
                                        <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                                            <tr>
                                                <th style="padding: 0.5rem;">ID</th>
                                                <th style="padding: 0.5rem;">Cliente/Conductor</th>
                                                <th style="padding: 0.5rem;">Documento</th>
                                                <th style="padding: 0.5rem;">Producto</th>
                                                <th style="padding: 0.5rem;">Monto</th>
                                                <th style="padding: 0.5rem;">Fecha</th>
                                                <th style="padding: 0.5rem; text-align: center;">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${financiamientos.map(f => `
                                                <tr class="fila-financiamiento" 
                                                    data-search="${f.idfinanciamiento} ${f.nombres} ${f.apellido_paterno} ${f.apellido_materno} ${f.documento} ${f.producto_nombre}">
                                                    <td style="padding: 0.5rem; vertical-align: middle;">
                                                        <span class="badge bg-primary">${f.idfinanciamiento}</span>
                                                    </td>
                                                    <td style="padding: 0.5rem; vertical-align: middle;">
                                                        <div>
                                                            <strong>${f.nombres} ${f.apellido_paterno} ${f.apellido_materno}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="fas fa-${f.id_conductor ? 'car' : 'user'}"></i>
                                                                ${f.id_conductor ? 'Conductor' : 'Cliente'}
                                                            </small>
                                                        </div>
                                                    </td>
                                                    <td style="padding: 0.5rem; vertical-align: middle;">
                                                        <code>${f.documento}</code>
                                                    </td>
                                                    <td style="padding: 0.5rem; vertical-align: middle;">
                                                        <small>${f.producto_nombre}</small>
                                                    </td>
                                                    <td style="padding: 0.5rem; vertical-align: middle;">
                                                        <strong>${f.moneda} ${parseFloat(f.monto_total).toFixed(2)}</strong>
                                                    </td>
                                                    <td style="padding: 0.5rem; vertical-align: middle;">
                                                        <small>${new Date(f.fecha_creacion).toLocaleDateString('es-PE')}</small>
                                                    </td>
                                                    <td style="padding: 0.5rem; vertical-align: middle; text-align: center;">
                                                        <button class="btn btn-sm btn-success" 
                                                                onclick="VinculacionManager.confirmarVinculacion(${idPrincipal}, ${f.idfinanciamiento})"
                                                                style="padding: 0.25rem 0.75rem;">
                                                            <i class="fas fa-link"></i> Vincular
                                                        </button>
                                                    </td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Mostrando <span id="totalResultados">${financiamientos.length}</span> financiamiento(s) disponible(s)
                                    </small>
                                </div>
                            `}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i> Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remover modal anterior si existe
        $('#modalVincularFinanciamiento').remove();

        // Agregar y mostrar modal
        $('body').append(html);
        
        // Inicializar buscador
        $('#buscarFinanciamiento').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            let visibleCount = 0;
            
            $('.fila-financiamiento').each(function() {
                const searchData = $(this).data('search').toString().toLowerCase();
                if (searchData.includes(searchTerm)) {
                    $(this).show();
                    visibleCount++;
                } else {
                    $(this).hide();
                }
            });
            
            $('#totalResultados').text(visibleCount);
        });
        
        $('#modalVincularFinanciamiento').modal('show');
    },

    /**
     * Confirmar vinculación
     */
    confirmarVinculacion: function(idPrincipal, idSecundario) {
        Swal.fire({
            title: '¿Vincular financiamientos?',
            html: `
                <p>Se vinculará el financiamiento <strong>#${idSecundario}</strong> como secundario.</p>
                <p class="text-muted">Solo el financiamiento principal (#${idPrincipal}) contará en el stock.</p>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, vincular',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                VinculacionManager.vincular(idPrincipal, idSecundario);
            }
        });
    },

    /**
     * Ejecutar vinculación
     */
    vincular: function(idPrincipal, idSecundario) {
        $.ajax({
            url: '/vincularFinanciamientos',
            type: 'POST',
            data: {
                id_principal: idPrincipal,
                id_secundario: idSecundario
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Vinculado',
                        text: 'Los financiamientos han sido vinculados correctamente',
                        timer: 2000
                    }).then(() => {
                        $('#modalVincularFinanciamiento').modal('hide');
                        // Recargar la página o actualizar la vista
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'No se pudo vincular los financiamientos'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al vincular financiamientos'
                });
            }
        });
    },

    /**
     * Desvincular financiamiento
     */
    desvincular: function(idFinanciamiento) {
        Swal.fire({
            title: '¿Desvincular financiamiento?',
            text: 'Ambos financiamientos volverán a contar como productos independientes en el stock.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, desvincular',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/desvincularFinanciamientos',
                    type: 'POST',
                    data: { id_financiamiento: idFinanciamiento },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Desvinculado',
                                text: 'Los financiamientos han sido desvinculados',
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'No se pudo desvincular'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al desvincular financiamientos'
                        });
                    }
                });
            }
        });
    }
};
