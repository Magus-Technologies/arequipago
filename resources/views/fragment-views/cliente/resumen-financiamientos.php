<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificamos si el usuario tiene sesión activa
$id_rol = $_SESSION['id_rol'] ?? null;

?>


    <div class="container-fluid mt-3">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2><i class="fas fa-chart-bar me-2"></i>Resumen General de Financiamientos</h2>
                    <button class="btn btn-secondary" onclick="volverAFinanciamiento()">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading -->
        <div id="loadingResumen" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando resumen de financiamientos...</p>
        </div>

        <!-- Contenido principal -->
        <div id="contenidoResumen" style="display: none;">
            <!-- Tarjetas de resumen -->
            <div class="row mb-4" id="tarjetasResumen">
                <!-- Se llenarán dinámicamente -->
            </div>

            <!-- ✅ NUEVO: Botón para ver vehículos entregados -->
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-center">
                    <button class="btn btn-success btn-lg" onclick="verVehiculosEntregados()" style="min-width: 400px; max-width: 600px;">
                        <i class="fas fa-car me-2"></i>Ver Todos los Vehículos Entregados
                        <span class="badge bg-white text-success ms-2" id="badgeTotalVehiculos" style="font-size: 1rem; padding: 0.4rem 0.8rem;">0</span>
                    </button>
                </div>
            </div>

            <!-- Tabla de planes -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-table me-2"></i>Resumen por Planes de Financiamiento</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tablaPlanes">
                            <thead>
                                <tr>
                                    <th>Plan de Financiamiento</th>
                                    <th>Conductores/Clientes</th>
                                    <th>Financiamientos</th>
                                    <th>Total Unidades</th>
                                    <th>Monto Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Se llenará dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error -->
        <div id="errorResumen" class="alert alert-danger" style="display: none;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <span id="mensajeError"></span>
        </div>
    </div>

    <!-- Modal para Ver Detalles -->
    <div class="modal fade" id="modalDetallePlan" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-list me-2"></i><span id="tituloDetalle">Detalle del Plan</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info" id="infoDetalle">
                        <!-- Se llenará dinámicamente -->
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="tablaDetalle">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Nombre</th>
                                    <th>Documento</th>
                                    <th>Nº Unidad</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Se llenará dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="exportarDetallePlan()">
                        <i class="fas fa-download me-2"></i>Exportar Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ NUEVO: Modal para Ver Vehículos Entregados -->
    <div class="modal fade" id="modalVehiculosEntregados" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-car me-2"></i>Vehículos Entregados
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Resumen de montos -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5>Total Vehículos</h5>
                                    <h3 id="totalVehiculosModal">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5>Monto Total (S/.)</h5>
                                    <h3 id="montoTotalSoles">S/ 0.00</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5>Monto Total ($)</h5>
                                    <h3 id="montoTotalDolares">$ 0.00</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de vehículos -->
                    <div class="table-responsive">
                        <table class="table table-striped table-sm table-hover" id="tablaVehiculosEntregados">
                            <thead class="table-success">
                                <tr>
                                    <th>ID</th>
                                    <th>Tipo</th>
                                    <th>Nombre</th>
                                    <th>Documento</th>
                                    <th>Nº Unidad</th>
                                    <th>Vehículo</th>
                                    <th>Marca/Modelo</th>
                                    <th>Monto</th>
                                    <th>Plan</th>
                                    <th>Fecha Entrega</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Se llenará dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cerrar
                    </button>
                    <button type="button" class="btn btn-success" onclick="exportarVehiculosEntregados()">
                        <i class="fas fa-file-excel me-2"></i>Exportar Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Scripts del sistema -->
    
    <!-- ✅ Librería XLSX para exportar a Excel -->

    <script src="<?= URL::to('public/js/resumen-financiamientos.js') ?>"></script>
