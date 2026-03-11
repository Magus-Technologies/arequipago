<!-- resources\views\components\modal-detalles-cliente.php -->
<!-- Modal para Ver Detalles -->
<div class="modal fade" id="financingDetailsModal" tabindex="-1"
    aria-labelledby="financingDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="financingDetailsModalLabel">
                    <i class="fas fa-info-circle me-2"></i>Detalles del Cliente y Financiamiento

                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Selector de financiamiento -->
                <div class="mb-3">
                    <div id="selectBoxDetalle" onclick="toggleDropdownDetalle()"
                        class="form-select d-flex justify-content-between align-items-center">
                        <span>Seleccionar un financiamiento</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>

                <!-- Tabla que simula el select (se oculta inicialmente) -->
                <div class="table-responsive mb-4">
                    <table id="detalleSelect" class="table table-hover" style="display: none;">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Grupo</th>
                                <th>Cantidad</th>
                                <th>Monto de Compra</th>
                                <th>Monto Total</th>
                                <th>Categoría</th>
                                <th>Estado</th>
                                <th>Estado de Entrega</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se llenará dinámicamente -->
                        </tbody>
                    </table>
                </div>

                <div id="detalleFinanciamientoContainer" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-white" style="color: #2E217A;">
                                    <h5><i class="fas fa-user me-2"></i>Datos del Cliente</h5>
                                </div>
                                <div class="card-body">
                                    <p><i class="fas fa-id-card me-2"></i><strong>Documento:
                                        </strong><span id="modalClienteDocumento"></span></p>
                                    <p><i class="fas fa-user me-2"></i><strong>Nombres: </strong><span
                                            id="modalClienteNombres"></span></p>
                                    <p><i class="fas fa-map-marker-alt me-2"></i><strong>Dirección:
                                        </strong><span id="modalClienteDireccion"></span></p>
                                    <p><i class="fas fa-phone me-2"></i><strong>Teléfono: </strong><span
                                            id="modalClienteTelefono"></span></p>

                                    <!-- Botones de descarga organizados en 2 columnas y 2 filas -->
                                    <div class="row mt-3 g-2">
                                        <!-- Primera fila - Documentos PDF -->
                                        <div class="col-6" id="btnDescargarContratoEntrega" style="display: none;">
                                            <button type="button" class="btn btn-sm w-100" 
                                                    style="background-color: white; color: #dc3545; border: 2px solid #dc3545; font-weight: 500;"
                                                    onclick="descargarContratoEntregaDesdeModal()"
                                                    onmouseover="this.style.backgroundColor='#fff5f5'; this.style.borderColor='#c82333'; this.style.color='#c82333'"
                                                    onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#dc3545'; this.style.color='#dc3545'">
                                                <i class="fas fa-file-pdf me-2"></i>Acta de Entrega
                                            </button>
                                        </div>

                                        <div class="col-6" id="btnDescargarActaCelular" style="display: none;">
                                            <button type="button" class="btn btn-sm w-100" 
                                                    style="background-color: white; color: #dc3545; border: 2px solid #dc3545; font-weight: 500;"
                                                    onclick="descargarActaEntregaCelular()"
                                                    onmouseover="this.style.backgroundColor='#fff5f5'; this.style.borderColor='#c82333'; this.style.color='#c82333'"
                                                    onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#dc3545'; this.style.color='#dc3545'">
                                                <i class="fas fa-mobile-alt me-2"></i>Acta de Entrega Celular
                                            </button>
                                        </div>

                                        <div class="col-6" id="btnDescargarBoletasIniciales" style="display: none;">
                                            <button type="button" class="btn btn-sm w-100" 
                                                    style="background-color: white; color: #dc3545; border: 2px solid #dc3545; font-weight: 500;"
                                                    onclick="mostrarBoletasPagoInicial()"
                                                    onmouseover="this.style.backgroundColor='#fff5f5'; this.style.borderColor='#c82333'; this.style.color='#c82333'"
                                                    onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#dc3545'; this.style.color='#dc3545'">
                                                <i class="fas fa-file-pdf me-2"></i>Boletas de Pago
                                            </button>
                                        </div>

                                        <!-- Segunda fila -->
                                        <div class="col-6" id="btnDescargarCronograma" style="display: none;">
                                            <button type="button" class="btn btn-sm w-100" 
                                                    style="background-color: white; color: #dc3545; border: 2px solid #dc3545; font-weight: 500;"
                                                    onclick="descargarCronogramaDesdeModal()"
                                                    onmouseover="this.style.backgroundColor='#fff5f5'; this.style.borderColor='#c82333'; this.style.color='#c82333'"
                                                    onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#dc3545'; this.style.color='#dc3545'">
                                                <i class="fas fa-file-pdf me-2"></i>Cronograma
                                            </button>
                                        </div>

                                        <div class="col-6" id="btnDescargarContratoExcel" style="display: none;">
                                            <button type="button" class="btn btn-sm w-100" 
                                                    style="background-color: white; color: #217346; border: 2px solid #217346; font-weight: 500;"
                                                    onclick="descargarContratoExcelDesdeModal()"
                                                    onmouseover="this.style.backgroundColor='#f0f8f4'; this.style.borderColor='#1a5c37'; this.style.color='#1a5c37'"
                                                    onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#217346'; this.style.color='#217346'">
                                                <i class="fas fa-file-excel me-2"></i>Contrato vehiculo
                                            </button>
                                        </div>

                                        <!-- Botón para descargar contrato PDF del grupo 49 -->
                                        <div class="col-6" id="btnDescargarContratoPDF49" style="display: none;">
                                            <button type="button" class="btn btn-sm w-100"
                                                    style="background-color: white; color: #dc3545; border: 2px solid #dc3545; font-weight: 500;"
                                                    onclick="descargarContratoPDF49DesdeModal()"
                                                    onmouseover="this.style.backgroundColor='#fff5f5'; this.style.borderColor='#c82333'; this.style.color='#c82333'"
                                                    onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#dc3545'; this.style.color='#dc3545'">
                                                <i class="fas fa-file-pdf me-2"></i>Contrato PDF
                                            </button>
                                        </div>

                                        <!-- Botón para descargar acta de entrega de chip (Plan 36 CORPORATIVO CLARO) -->
                                        <div class="col-6" id="btnDescargarActaChip" style="display: none;">
                                            <button type="button" class="btn btn-sm w-100"
                                                    style="background-color: white; color: #6f42c1; border: 2px solid #6f42c1; font-weight: 500;"
                                                    onclick="descargarActaEntregaChip()"
                                                    onmouseover="this.style.backgroundColor='#f8f5fd'; this.style.borderColor='#5a32a3'; this.style.color='#5a32a3'"
                                                    onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#6f42c1'; this.style.color='#6f42c1'">
                                                <i class="fas fa-sim-card me-2"></i>Acta Entrega Chip
                                            </button>
                                        </div>

                                        <!-- Botón para descargar boleta del excedente (Plan 49 entregado) -->
                                        <div class="col-6" id="btnDescargarBoletaExcedente" style="display: none;">
                                            <button type="button" class="btn btn-sm w-100"
                                                    style="background-color: white; color: #e67e22; border: 2px solid #e67e22; font-weight: 500;"
                                                    onclick="descargarBoletaExcedente()"
                                                    onmouseover="this.style.backgroundColor='#fef5ec'; this.style.borderColor='#d35400'; this.style.color='#d35400'"
                                                    onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#e67e22'; this.style.color='#e67e22'">
                                                <i class="fas fa-file-invoice-dollar me-2"></i>Boleta Excedente
                                            </button>
                                        </div>
                                    </div>

                                    <!-- NUEVO: Estado de entrega del vehículo -->
                                    <div id="estadoEntregaVehiculo" style="display: none; margin-top: 15px;">
                                        <!-- Se llenará dinámicamente -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
    <div class="card mb-3">
        <div class="card-header bg-white" style="color: #2E217A;">
            <h5><i class="fas fa-file-invoice-dollar me-2"></i>Financiamiento</h5>
        </div>
        <div class="card-body">
            <!-- ID del Financiamiento -->
            <p><i class="fas fa-fingerprint me-2"></i><strong>ID Financiamiento: </strong>
                <span id="modalFinanciamientoID" class="badge bg-primary" style="font-size: 0.9em;">N/A</span>
            </p>

            <!-- Campo normal para todos los planes -->
            <p><i class="fas fa-dollar-sign me-2"></i><strong>Monto de Compra:</strong>
                <span id="modalFinanciamientoMontoCompra" class="text-primary fw-bold">$0.00</span>
            </p>

            <p><i class="fas fa-calculator me-2"></i><strong>Monto Total (con intereses):</strong>
                <span id="modalFinanciamientoMontoTotal" class="text-warning fw-bold">$0.00</span>
            </p>

            <!-- Información adicional para vehículos -->
            <div id="infoVehiculo" style="display: none;">
                <hr>
                <div id="campoCapacidadCompra">
                    <p><i class="fas fa-shopping-cart me-2"></i><strong>Capacidad de Compra Actual:</strong>
                        <span id="modalFinanciamientoCapacidadCompra" class="text-success fw-bold">$0.00</span>
                    </p>
                </div>
                <p><i class="fas fa-car me-2"></i><strong>Costo del Vehículo:</strong>
                    <span id="modalFinanciamientoCostoVehiculo" class="text-primary fw-bold">$0.00</span>
                </p>
                <p id="campoExcedente" style="display: none;">
                    <i class="fas fa-coins me-2" style="color: #e67e22;"></i><strong>Excedente:</strong>
                    <span id="modalFinanciamientoExcedente" class="fw-bold" style="color: #e67e22;">$0.00</span>
                </p>
                <hr>
                <p><i class="fas fa-calendar-alt me-2"></i><strong>Plan Original:</strong>
                    <span id="modalFinanciamientoPlanOriginal" class="text-info">$0.00</span>
                </p>
                <p><i class="fas fa-clock me-2"></i><strong>Semanas Perdidas:</strong>
                    <span id="modalFinanciamientoSemanasPerdidas" class="text-danger">0</span>
                </p>
                <p><i class="fas fa-exclamation-triangle me-2"></i><strong>Dinero Perdido:</strong>
                    <span id="modalFinanciamientoDineroPerdido" class="text-danger">$0.00</span>
                </p>
            </div>

            <!-- Campos originales -->
            <p><i class="fas fa-hashtag me-2"></i><strong>Código Asociado:</strong>
                <span id="modalFinanciamientoCodigo"></span></p>
            <p><i class="fas fa-layer-group me-2"></i><strong>Grupo de Financiamiento: </strong><span
                    id="modalFinanciamientoGrupo"></span></p>
            
            <!-- ✅ NUEVO: Campo Placa del Vehículo (solo si existe) -->
            <p id="campoPlacaVehiculo" style="display: none;">
                <i class="fas fa-car me-2" style="color: #28a745;"></i>
                <strong>Placa del Vehículo: </strong>
                <span id="modalPlacaVehiculo" class="badge bg-success" style="font-size: 0.95em; padding: 6px 12px;"></span>
            </p>
            
            <!-- Campo Número Corporativo (solo Plan 36 CORPORATIVO CLARO) -->
            <p id="campoNumeroCorporativo" style="display: none;">
                <i class="fas fa-phone-alt me-2" style="color: #6f42c1;"></i>
                <strong>N° Corporativo: </strong>
                <span id="modalNumeroCorporativo" class="badge" style="background-color: #6f42c1; font-size: 0.95em; padding: 6px 12px;"></span>
            </p>

            <p><i class="fas fa-check-circle me-2"></i><strong>Estado:</strong><span
                    id="modalFinanciamientoEstado"></span></p>

            <!-- ✅ NUEVO: Información de Vinculación -->
            <div id="seccionVinculacion" style="display: none;">
                <hr style="margin: 15px 0; border-color: #007bff;">
                <div style="background-color: #e7f3ff; padding: 12px; border-radius: 8px; border-left: 4px solid #007bff;">
                    <h6 style="color: #004085; margin-bottom: 10px;">
                        <i class="fas fa-link me-2"></i><strong>Financiamiento Vinculado</strong>
                    </h6>
                    <p style="margin-bottom: 8px;">
                        <i class="fas fa-user me-2" style="color: #007bff;"></i>
                        <strong>Vinculado con:</strong><br>
                        <span id="modalNombreVinculado" class="text-primary fw-bold"></span>
                    </p>
                    <p style="margin-bottom: 8px;">
                        <i class="fas fa-id-card me-2" style="color: #007bff;"></i>
                        <strong>Documento:</strong>
                        <span id="modalDocumentoVinculado" class="text-primary"></span>
                    </p>
                    <p style="margin-bottom: 8px;">
                        <i class="fas fa-hashtag me-2" style="color: #007bff;"></i>
                        <strong>ID Financiamiento:</strong>
                        <span class="badge bg-primary" id="modalIdVinculado"></span>
                    </p>
                    <p style="margin-bottom: 0;">
                        <i class="fas fa-star me-2" style="color: #007bff;"></i>
                        <strong>Tipo:</strong>
                        <span id="modalTipoVinculacion" class="badge"></span>
                    </p>
                </div>
            </div>

            <!-- NUEVO: Sección de Contrato Finalizado -->
            <div id="seccionContratoFinalizado" style="display: none;">
                <hr style="margin: 15px 0; border-color: #28a745;">
                <div style="background-color: #d4edda; padding: 12px; border-radius: 8px; border-left: 4px solid #28a745;">
                    <h6 style="color: #155724; margin-bottom: 10px;">
                        <i class="fas fa-check-double me-2"></i><strong>Contrato Finalizado</strong>
                    </h6>
                    <p style="margin-bottom: 8px;">
                        <i class="fas fa-calendar-check me-2" style="color: #28a745;"></i>
                        <strong>Fecha de Finalización:</strong><br>
                        <span id="modalFechaFinalizacion" class="text-success fw-bold"></span>
                    </p>
                    <p style="margin-bottom: 0;">
                        <i class="fas fa-user-check me-2" style="color: #28a745;"></i>
                        <strong>Finalizado por:</strong><br>
                        <span id="modalUsuarioFinalizo" class="text-success fw-bold"></span>
                    </p>
                </div>
            </div>

            <!-- NUEVO: Sección específica para CrediYango -->
            <div id="seccionCrediYango" style="display: none;">
                <hr style="margin: 15px 0; border-color: #28a745;">
                <div style="background-color: #e8f5e8; padding: 12px; border-radius: 8px; border-left: 4px solid #28a745;">
                    <h6 style="color: #155724; margin-bottom: 10px;">
                        <i class="fas fa-truck me-2"></i><strong>Información de Entrega CrediYango</strong>
                    </h6>

                    <div class="row">
                        <div class="col-md-6">
                            <p style="margin-bottom: 8px;">
                                <i class="fas fa-calendar-alt me-2" style="color: #28a745;"></i>
                                <strong>Fecha de Entrega:</strong><br>
                                <span id="modalFechaEntrega" class="text-success fw-bold">No programada</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p style="margin-bottom: 8px;">
                                <i class="fas fa-calendar-check me-2" style="color: #17a2b8;"></i>
                                <strong>Inicio de Pagos:</strong><br>
                                <span id="modalFechaInicioPagos" class="text-info fw-bold">No calculado</span>
                            </p>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <div id="estadoEntregaCrediYango">
                                <!-- Se llenará dinámicamente según el estado -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <p><i class="fas fa-calendar-day me-2"></i><strong>Fecha Inicio:</strong><span
                    id="modalFechaInicio"></span></p>
            <p><i class="fas fa-calendar-check me-2"></i><strong>Fecha Fin:</strong><span
                    id="modalFechaFin"></span></p>
            <p><i class="fas fa-user-check me-2"></i><strong>Registrado por:</strong><span
                    id="modalFinanciamientoUsuarioRegistro"></span></p>
        </div>
    </div>
</div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-money-bill-wave me-2"></i>Cuotas</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-striped" id="modalCuotasTable">
                                    <thead class="sticky-top bg-white">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Monto</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Las cuotas se agregarán dinámicamente aquí -->
                                    </tbody>
                                </table>
                            </div>

                            <?php if ($id_rol == 3): ?>
                                <!-- Nuevo botón de editar -->
                                <button type="button" class="btn btn-warning me-2"
                                    onclick="editarFinanciamiento()" style="margin-top: 15px;">
                                    <i class="fas fa-edit me-2"></i>Editar este financiamiento
                                </button>

                                <!-- NUEVO: Botón Entregar vehículo - solo para productos con ID 37 -->
                                <button type="button" class="btn btn-success me-2" id="btnEntregarVehiculo"
                                    onclick="mostrarModalEntregarVehiculo()" style="margin-top: 15px; display: none;">
                                    <i class="fas fa-car me-2"></i>Entregar vehículo
                                </button>

                                <!-- ✅ MODIFICADO: Botón eliminar - SOLO para usuarios 78, 77, 79 -->
                                <?php if (in_array($usuario_id, [78, 77, 79])): ?>
                                    <button type="button" class="btn btn-danger mt-3" onclick="deleteFinance()">
                                        <i class="fas fa-trash-alt me-2"></i>Eliminar este financiamiento
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <?php if ($id_rol == 3): ?>
                    <!-- Botón para vincular financiamiento -->
                    <button type="button" class="btn btn-primary me-2" id="btnVincularFinanciamiento"
                        onclick="VinculacionManager.abrirModalVincular(window.currentFinanciamientoId)" style="display: none;">
                        <i class="fas fa-link me-2"></i>Vincular Financiamiento
                    </button>
                    
                    <!-- Botón para desvincular financiamiento -->
                    <button type="button" class="btn btn-warning me-2" id="btnDesvincularFinanciamiento"
                        onclick="VinculacionManager.desvincular(window.currentFinanciamientoId)" style="display: none;">
                        <i class="fas fa-unlink me-2"></i>Desvincular
                    </button>
                
                    <!-- Botón para procesar retiro (solo planes 19 y 38) -->
                    <button type="button" class="btn btn-danger me-2" id="btnProcesarRetiro"
                        onclick="mostrarModalRetiro(window.currentFinanciamientoId)" style="display: none;">
                        <i class="fas fa-sign-out-alt me-2"></i>Procesar Retiro
                    </button>
                    
                    <!-- Botón para finalizar contrato (solo se muestra si NO está finalizado) -->
                    <button type="button" class="btn btn-success me-2" id="btnFinalizarContrato"
                        onclick="finalizarContrato()" style="display: none;">
                        <i class="fas fa-check-double me-2"></i>Finalizar Contrato
                    </button>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
