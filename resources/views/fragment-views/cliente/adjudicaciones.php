<div class="adjudicaciones-container">
    <!-- Dashboard de Indicadores -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-3">
                <i class="fas fa-trophy"></i> Módulo de Adjudicaciones
            </h2>
        </div>
    </div>

    <!-- Tarjetas de Indicadores -->
    <div class="row mb-4" id="indicadores-cards">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card card-stats bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-white mb-0">Total Adjudicados</h5>
                            <span class="h2 font-weight-bold mb-0" id="total-adjudicaciones">0</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-white text-primary rounded-circle shadow">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card card-stats bg-gradient-success text-white">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-white mb-0">Stock Disponible</h5>
                            <span class="h2 font-weight-bold mb-0" id="total-en-stock">0</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-white text-success rounded-circle shadow">
                                <i class="fas fa-warehouse"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card card-stats bg-gradient-info text-white">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-white mb-0">Vehículos Entregados</h5>
                            <span class="h2 font-weight-bold mb-0" id="total-entregados">0</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-white text-info rounded-circle shadow">
                                <i class="fas fa-car"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card card-stats bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-white mb-0">SOAT/Seguros por Vencer</h5>
                            <span class="h2 font-weight-bold mb-0">
                                <span id="soat-vencer-count">0</span> / <span id="seguro-vencer-count">0</span>
                            </span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-white text-warning rounded-circle shadow">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs de Navegación -->
    <ul class="nav nav-tabs mb-3" id="adjudicacionesTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="adjudicados-tab" data-bs-toggle="tab" href="#adjudicados" role="tab">
                <i class="fas fa-list"></i> Lista de Adjudicados
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cuotas-vencidas-tab" data-bs-toggle="tab" href="#cuotas-vencidas" role="tab">
                <i class="fas fa-clock"></i> Cuotas Vencidas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="morosos-tab" data-bs-toggle="tab" href="#morosos" role="tab">
                <i class="fas fa-user-times"></i> Morosos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="proximos-terminar-tab" data-bs-toggle="tab" href="#proximos-terminar" role="tab">
                <i class="fas fa-check-circle"></i> Próximos a Terminar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="ganadores-mes-tab" data-bs-toggle="tab" href="#ganadores-mes" role="tab">
                <i class="fas fa-trophy"></i> Ganadores por Mes
            </a>
        </li>
        <!-- <li class="nav-item">
            <a class="nav-link" id="velocidad-entrega-tab" data-bs-toggle="tab" href="#velocidad-entrega" role="tab">
                <i class="fas fa-tachometer-alt"></i> Velocidad de Entrega
            </a>
        </li> -->
        <li class="nav-item">
            <a class="nav-link" id="soat-vencer-tab" data-bs-toggle="tab" href="#soat-vencer" role="tab">
                <i class="fas fa-shield-alt"></i> SOAT
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="seguro-vencer-tab" data-bs-toggle="tab" href="#seguro-vencer" role="tab">
                <i class="fas fa-file-shield"></i> Seguros
            </a>
        </li>
    </ul>

    <!-- Contenido de Tabs -->
    <div class="tab-content" id="adjudicacionesTabContent">
        <!-- Tab: Lista de Adjudicados -->
        <div class="tab-pane fade show active" id="adjudicados" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Lista de Adjudicados</h3>
                        <button class="btn btn-success btn-sm" onclick="exportarAdjudicadosExcel()" title="Descargar Excel">
                            <i class="fas fa-file-excel me-1"></i> Descargar Excel
                        </button>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">
                            <select class="form-control" id="filtro-tipo-adjudicacion">
                                <option value="">Todos los tipos</option>
                                <option value="sorteo">Sorteo</option>
                                <option value="directo_con_inicial">Directo con Inicial</option>
                                <option value="crediyango">CrediYango</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="filtro-estado-entrega">
                                <option value="">Todos los estados</option>
                                <option value="entregado">Entregado</option>
                                <option value="pendiente">Pendiente</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="filtro-fecha-entrega">
                                <option value="">Todas las fechas de entrega</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabla-adjudicados">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Cliente/Conductor</th>
                                    <th>Vehículo</th>
                                    <th>Tipo Adjudicación</th>
                                    <th>Fecha Entrega</th>
                                    <th>Estado</th>
                                    <th>Cuotas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-adjudicados">
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <div class="spinner-border" role="status">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Cuotas Vencidas -->
        <div class="tab-pane fade" id="cuotas-vencidas" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Cuotas Vencidas</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabla-cuotas-vencidas">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Vehículo</th>
                                    <th>Nº Cuota</th>
                                    <th>Monto</th>
                                    <th>Fecha Vencimiento</th>
                                    <th>Días Vencidos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-cuotas-vencidas">
                                <tr>
                                    <td colspan="7" class="text-center">Cargando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Morosos -->
        <div class="tab-pane fade" id="morosos" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Adjudicados Morosos (Más de 30 días)</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabla-morosos">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Documento</th>
                                    <th>Vehículo</th>
                                    <th>Cuotas Vencidas</th>
                                    <th>Monto Total</th>
                                    <th>Días Mora Máxima</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-morosos">
                                <tr>
                                    <td colspan="7" class="text-center">Cargando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Próximos a Terminar -->
        <div class="tab-pane fade" id="proximos-terminar" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Próximos a Terminar de Pagar (10 cuotas o menos)</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabla-proximos">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Vehículo</th>
                                    <th>Total Cuotas</th>
                                    <th>Pagadas</th>
                                    <th>Pendientes</th>
                                    <th>Última Cuota</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-proximos">
                                <tr>
                                    <td colspan="7" class="text-center">Cargando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Ganadores por Mes -->
        <div class="tab-pane fade" id="ganadores-mes" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Ganadores por Mes</h3>
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <input type="month" class="form-control" id="filtro-mes-ganadores" value="<?php echo date(
                                "Y-m",
                            ); ?>">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabla-ganadores">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Vehículo</th>
                                    <th>Tipo</th>
                                    <th>Fecha Adjudicación</th>
                                    <th>Fecha Entrega</th>
                                    <th>Días Demora</th>
                                    <th>Inicial</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-ganadores">
                                <tr>
                                    <td colspan="7" class="text-center">Cargando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Velocidad de Entrega -->
        <div class="tab-pane fade" id="velocidad-entrega" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Reporte de Velocidad de Entrega (Últimos 12 meses)</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabla-velocidad">
                            <thead>
                                <tr>
                                    <th>Mes</th>
                                    <th>Total Adjudicaciones</th>
                                    <th>Promedio Días</th>
                                    <th>Mínimo Días</th>
                                    <th>Máximo Días</th>
                                    <th>Entregas Rápidas (≤7 días)</th>
                                    <th>Entregas Lentas (>30 días)</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-velocidad">
                                <tr>
                                    <td colspan="7" class="text-center">Cargando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: SOAT por Vencer -->
        <div class="tab-pane fade" id="soat-vencer" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">SOAT por Vencer (Próximos 60 días)</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabla-soat">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Vehículo</th>
                                    <th>Código</th>
                                    <th>Fecha Vencimiento</th>
                                    <th>Días para Vencer</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-soat">
                                <tr>
                                    <td colspan="6" class="text-center">Cargando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Seguros por Vencer -->
        <div class="tab-pane fade" id="seguro-vencer" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Seguros por Vencer (Próximos 60 días)</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabla-seguro">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Vehículo</th>
                                    <th>Código</th>
                                    <th>Fecha Vencimiento</th>
                                    <th>Días para Vencer</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-seguro">
                                <tr>
                                    <td colspan="6" class="text-center">Cargando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.adjudicaciones-container {
    padding: 20px;
}

.card-stats {
    border: none;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.icon-shape {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-gradient-primary {
    background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%);
}

.bg-gradient-success {
    background: linear-gradient(87deg, #2dce89 0, #2dcecc 100%);
}

.bg-gradient-info {
    background: linear-gradient(87deg, #11cdef 0, #1171ef 100%);
}

.bg-gradient-warning {
    background: linear-gradient(87deg, #fb6340 0, #fbb140 100%);
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.badge-tipo-sorteo {
    background-color: #5e72e4;
}

.badge-tipo-directo {
    background-color: #2dce89;
}

.badge-tipo-crediyango {
    background-color: #11cdef;
}
</style>

<script>
function exportarAdjudicadosExcel() {
    var tipoAdj = $('#filtro-tipo-adjudicacion').val();
    var estadoEntrega = $('#filtro-estado-entrega').val();
    var fechaEntrega = $('#filtro-fecha-entrega').val();
    var url = _URL + '/ajs/adjudicaciones/exportar-excel?';
    var params = [];
    if (tipoAdj) params.push('tipo_adjudicacion=' + encodeURIComponent(tipoAdj));
    if (estadoEntrega) params.push('estado_entrega=' + encodeURIComponent(estadoEntrega));
    if (fechaEntrega) params.push('fecha_entrega=' + encodeURIComponent(fechaEntrega));
    url += params.join('&');
    window.open(url, '_blank');
}
</script>

<!-- Módulos JavaScript de Adjudicaciones -->
<!-- IMPORTANTE: Cargar en este orden específico -->
<script src="<?= URL::to(
    "public/js/adjudicaciones/helpers.js",
) ?>?v=<?= time() ?>"></script>
<script src="<?= URL::to(
    "public/js/adjudicaciones/indicadores.js",
) ?>?v=<?= time() ?>"></script>
<script src="<?= URL::to(
    "public/js/adjudicaciones/navigation.js",
) ?>?v=<?= time() ?>"></script>
<script src="<?= URL::to(
    "public/js/adjudicaciones/dataTable-adjudicados.js",
) ?>?v=<?= time() ?>"></script>
<script src="<?= URL::to(
    "public/js/adjudicaciones/actions.js",
) ?>?v=<?= time() ?>"></script>
<script src="<?= URL::to(
    "public/js/adjudicaciones/adjudicaciones.js",
) ?>?v=<?= time() ?>"></script>
