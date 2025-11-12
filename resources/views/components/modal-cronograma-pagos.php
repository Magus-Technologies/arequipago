<!-- resources\views\components\modal-cronograma-pagos.php -->
<!-- Modal para ver cronograma -->
<div class="modal fade" id="paymentScheduleModal" tabindex="-1"
    aria-labelledby="paymentScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentScheduleModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>Cronograma de Pagos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Selector de financiamiento -->
                <div class="mb-3">
                    <div id="selectBox" onclick="toggleDropdown()"
                        class="form-select d-flex justify-content-between align-items-center">
                        <span>Seleccionar un financiamiento</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>

                <!-- Tabla que simula el select (se oculta inicialmente) -->
                <div class="table-responsive mb-4">
                    <table id="cronogramaSelect" class="table table-hover" style="display: none;">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Grupo</th>
                                <th>Cantidad</th>
                                <th>Monto</th>
                                <th>Categoría</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se llenará dinámicamente -->
                        </tbody>
                    </table>
                </div>

                <!-- Mensaje cuando no hay cronograma disponible -->
                <div id="noCronogramaMessage" class="alert alert-info text-center"
                    style="display: none;">
                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                    <p>No hay cronograma de pagos disponible para este cliente.</p>
                </div>

                <!-- Tabla de cuotas -->
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table id="tablaCuotas" class="table table-striped" style="display: none;">
                        <thead class="sticky-top bg-white">
                            <tr>
                                <th>Fecha de vencimiento</th>
                                <th>Monto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Las filas se llenan dinámicamente con JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
