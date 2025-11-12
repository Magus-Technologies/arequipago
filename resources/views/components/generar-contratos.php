<!-- resources\views\components\generar-contratos.php -->
<div class="card border rounded shadow-sm">
    <div class="card-header" style="background-color: #fcf3cf; color: #2E217A;">
        <h5><i class="fas fa-file-contract me-2"></i>Generar Contratos</h5>
    </div>
    <div class="card-body">
        <!-- Filtro -->
        <div class="card mb-4 border rounded shadow-sm">
            <div class="card-header bg-white border rounded shadow-sm" style="color: #2E217A;">
                <h6><i class="fas fa-search me-2"></i>Buscar Financiamientos</h6>
            </div>
            <div class="card-body">
                <div class="input-group mb-1">
                    <input type="text" id="buscar-financiamientos" class="form-control"
                        placeholder="Ingrese criterios de búsqueda">
                    <button class="btn" id="btn-buscar" onclick="buscarFinanciamientos()"
                        style="background-color: #f4f750; color: #2E217A;">
                        <i class="fas fa-search me-2"></i>Buscar
                    </button>
                </div>
                <p id="error-busqueda" class="text-danger small mt-1" style="display: none;"></p>
            </div>
        </div>

        <!-- Tabla de financiamientos -->
        <div class="card mb-4 border rounded shadow-sm">
            <div class="card-header bg-white border rounded shadow-sm" style="color: #2E217A;">
                <h6><i class="fas fa-list me-2"></i>Lista de Financiamientos</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyContratos">
                            <!-- Las filas se cargarán dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Rango de fechas -->
        <div class="card mb-4 border rounded shadow-sm">
            <div class="card-header bg-white border rounded shadow-sm" style="color: #2E217A;">
                <h6><i class="fas fa-calendar-alt me-2"></i>Rango de fechas</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        <label for="fecha-inicio" class="form-label">Fecha de inicio</label>
                        <div class="input-group mb-1">
                            <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                            <input type="date" id="fecha-inicio" class="form-control" required>
                        </div>
                        <p id="error-fecha-inicio" class="text-danger small mt-1"
                            style="display: none;"></p>
                    </div>
                    <div class="col-md-5">
                        <label for="fecha-fin" class="form-label">Fecha de fin</label>
                        <div class="input-group mb-1">
                            <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                            <input type="date" id="fecha-fin" class="form-control" required>
                        </div>
                        <p id="error-fecha-fin" class="text-danger small mt-1" style="display: none;">
                        </p>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" onclick="limpiarFechas()"
                            class="btn btn-secondary w-100 mb-1">
                            <i class="fas fa-eraser me-2"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Botón de acción -->
        <div class="text-center">
            <button id="btn-generar" onclick="GenerarContratos()" class="btn btn-primary btn-lg"
                style="background-color: #f4f750; color: #2E217A;">
                <i class="fas fa-file-contract me-2"></i>Generar Contratos
            </button>
        </div>
    </div>
</div>
