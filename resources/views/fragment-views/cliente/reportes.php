<!-- resources\views\fragment-views\cliente\reportes.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Reportes de Ventas</title>
    
    
    <link rel="stylesheet" href="<?= URL::to('/public/css/reporte.css') ?>?v=<?= time() ?>">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 mb-4">
                <h1 class="text-center my-4">Sistema de Reportes de Ventas</h1>
                
                <!-- Progress Indicator -->
                <div class="wizard-progress mb-4">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar" role="progressbar" style="width: 33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="wizard-steps mt-2">
                        <div class="wizard-step active" id="step-indicator-1">
                            <div class="wizard-step-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="wizard-step-label">Tipo de Reporte</div>
                        </div>
                        <div class="wizard-step" id="step-indicator-2">
                            <div class="wizard-step-icon">
                                <i class="fas fa-filter"></i>
                            </div>
                            <div class="wizard-step-label">Filtros</div>
                        </div>
                        <div class="wizard-step" id="step-indicator-3">
                            <div class="wizard-step-icon">
                                <i class="fas fa-table"></i>
                            </div>
                            <div class="wizard-step-label">Vista Previa</div>
                        </div>
                    </div>
                </div>
                
                <!-- Wizard Content -->
                <div class="wizard-container">
                    <!-- Step 1: Report Type Selection -->
                    <div class="wizard-step-content active" id="step-1">
                        <h3 class="mb-4">Selecciona el tipo de reporte</h3>
                        <div class="row row-cols-1 row-cols-md-2 g-4">
                            <div class="col">
                                <div class="card h-100 report-card" data-report-type="ventas-generales">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-chart-line fa-3x mb-3 text-primary"></i>
                                        <h5 class="card-title">Ventas Generales</h5>
                                        <p class="card-text">Reporte detallado de todas las ventas con información de clientes, productos y vendedores.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card h-100 report-card" data-report-type="ventas-por-empleado">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-user-tie fa-3x mb-3 text-success"></i>
                                        <h5 class="card-title">Ventas por Empleado</h5>
                                        <p class="card-text">Análisis de ventas desglosado por asesor, productos vendidos y rendimiento individual.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card h-100 report-card" data-report-type="financiamientos">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-money-bill-wave fa-3x mb-3 text-warning"></i>
                                        <h5 class="card-title">Financiamientos de Productos</h5>
                                        <p class="card-text">Estado de financiamientos, cuotas pendientes y pagadas para clientes y conductores.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card h-100 report-card" data-report-type="cuotas-pagadas">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-calendar-check fa-3x mb-3 text-info"></i>
                                        <h5 class="card-title">Cuotas Pagadas</h5>
                                        <p class="card-text">Seguimiento de pagos realizados y clientes morosos con detalle de saldos pendientes.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card h-100 report-card" data-report-type="ingresos">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-money-bill-alt fa-3x mb-3 text-danger"></i>
                                        <h5 class="card-title">Ingresos</h5>
                                        <p class="card-text">Reporte detallado de todos los ingresos: ventas, financiamientos de inscripciones y financiamientos.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card h-100 report-card" data-report-type="ventas-por-categoria">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-tags fa-3x mb-3 text-primary"></i>
                                        <h5 class="card-title">Ventas por Producto (por categoría)</h5>
                                        <p class="card-text">Ver productos vendidos agrupados por categoría, con filtros por fecha, tipo de venta y moneda.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 2: Filter Configuration -->
                    <div class="wizard-step-content" id="step-2">
                        <h3 class="mb-4">Configura los filtros del reporte</h3>
                        
                        <!-- Common Filters -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Filtros Generales</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                        <input type="date" class="form-control" id="fecha_inicio" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                        <input type="date" class="form-control" id="fecha_fin" required>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="todos_los_tiempos">
                                            <label class="form-check-label" for="todos_los_tiempos">
                                                <i class="fas fa-infinity me-1"></i>
                                                <strong>Todos los tiempos</strong>
                                                <small class="text-muted d-block">Incluir registros desde el inicio del sistema hasta hoy</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dynamic Filters - Will be loaded based on report type -->
                        <div id="dynamic-filters">
                            <!-- Ventas por Empleado Filters -->
                            <div class="filter-section" id="ventas-por-empleado-filters" style="display: none;">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Filtros Específicos</h5>
                                        <div class="mb-3">
                                            <label class="form-label">Seleccionar Asesores</label>
                                            <div id="empleados-container" class="mb-3">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Cargando...</span>
                                                </div>
                                                <p>Cargando lista de asesores...</p>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="select-all-empleados" checked>
                                                <label class="form-check-label" for="select-all-empleados">
                                                    Seleccionar todos
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Financiamientos Filters -->
                            <div class="filter-section" id="financiamientos-filters" style="display: none;">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Filtros Específicos</h5>
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Cliente</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipo_cliente" id="tipo_todos" value="todos" checked>
                                                <label class="form-check-label" for="tipo_todos">
                                                    Todos
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipo_cliente" id="tipo_clientes" value="clientes">
                                                <label class="form-check-label" for="tipo_clientes">
                                                    Clientes
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipo_cliente" id="tipo_conductores" value="conductores">
                                                <label class="form-check-label" for="tipo_conductores">
                                                    Conductores
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Ventas Generales - No additional filters -->
                            <div class="filter-section" id="ventas-generales-filters" style="display: none;">
                                <!-- No additional filters needed -->
                            </div>
                            
                            <!-- Cuotas Pagadas Filters -->
                            <div class="filter-section" id="cuotas-pagadas-filters" style="display: none;">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Filtros Específicos</h5>
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Cliente</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipo_cliente_cuotas" id="tipo_todos_cuotas" value="todos" checked>
                                                <label class="form-check-label" for="tipo_todos_cuotas">
                                                    Todos
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipo_cliente_cuotas" id="tipo_clientes_cuotas" value="clientes">
                                                <label class="form-check-label" for="tipo_clientes_cuotas">
                                                    Clientes
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipo_cliente_cuotas" id="tipo_conductores_cuotas" value="conductores">
                                                <label class="form-check-label" for="tipo_conductores_cuotas">
                                                    Conductores
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Estado de Cuotas</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="incluir_morosos" checked>
                                                <label class="form-check-label" for="incluir_morosos">
                                                    Incluir clientes morosos
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ingresos Filters -->
                            <div class="filter-section" id="ingresos-filters" style="display: none;">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Filtros Específicos</h5>
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Ingreso</label>
                                            <div class="mb-3">
                                                <h6>Inscripciones</h6>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox" id="inscripcion_contado" checked>
                                                    <label class="form-check-label" for="inscripcion_contado">
                                                        Al contado
                                                    </label>
                                                </div>
                                                <div class="ms-3">
                                                    <h6>Financiadas:</h6>
                                                    <div class="form-check ms-3">
                                                        <input class="form-check-input" type="checkbox" id="inscripcion_financiada_cuotas" checked>
                                                        <label class="form-check-label" for="inscripcion_financiada_cuotas">
                                                            Cuotas
                                                        </label>
                                                    </div>
                                                    <div class="form-check ms-3">
                                                        <input class="form-check-input" type="checkbox" id="inscripcion_financiada_inicial" checked>
                                                        <label class="form-check-label" for="inscripcion_financiada_inicial">
                                                            Cuota inicial
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <h6>Financiamiento de productos:</h6>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox" id="financiamiento_cuota_inicial" checked>
                                                    <label class="form-check-label" for="financiamiento_cuota_inicial">
                                                        Cuota inicial
                                                    </label>
                                                </div>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox" id="financiamiento_cuotas" checked>
                                                    <label class="form-check-label" for="financiamiento_cuotas">
                                                        Cuotas
                                                    </label>
                                                </div>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox" id="financiamiento_monto_inscripcion" checked>
                                                    <label class="form-check-label" for="financiamiento_monto_inscripcion">
                                                        Monto de inscripción (cuando aplica)
                                                    </label>
                                                </div>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox" id="financiamiento_monto_recalculado" checked>
                                                    <label class="form-check-label" for="financiamiento_monto_recalculado">
                                                        Monto recalculado (Cuando aplica)
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <h6>Ventas</h6>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox" id="ventas" checked>
                                                    <label class="form-check-label" for="ventas">
                                                        Incluir ventas
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Forma de Pago</label>
                                            <div id="metodos-pago-container" class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input metodo-pago-checkbox" type="checkbox" id="metodo_efectivo" value="Efectivo" checked>
                                                    <label class="form-check-label" for="metodo_efectivo">Efectivo</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input metodo-pago-checkbox" type="checkbox" id="metodo_qr" value="QR" checked>
                                                    <label class="form-check-label" for="metodo_qr">QR</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input metodo-pago-checkbox" type="checkbox" id="metodo_bono" value="Pago Bono" checked>
                                                    <label class="form-check-label" for="metodo_bono">Pago Bono</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input metodo-pago-checkbox" type="checkbox" id="metodo_transferencia" value="Transferencia" checked>
                                                    <label class="form-check-label" for="metodo_transferencia">Transferencia</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input metodo-pago-checkbox" type="checkbox" id="metodo_tarjeta" value="Tarjeta" checked>
                                                    <label class="form-check-label" for="metodo_tarjeta">Tarjeta</label>
                                                </div>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="select-all-metodos" checked>
                                                <label class="form-check-label" for="select-all-metodos">
                                                    Seleccionar todos
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ventas por Categoría Filters -->
                            <div class="filter-section" id="ventas-por-categoria-filters" style="display: none;">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Filtros Específicos</h5>
                                        
                                        <!-- Filtro por Categoría -->
                                        <div class="mb-3">
                                            <label class="form-label">Categoría de Producto</label>
                                            <div id="categorias-container" class="mb-3">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Cargando...</span>
                                                </div>
                                                <p>Cargando categorías...</p>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="select-all-categorias" checked>
                                                <label class="form-check-label" for="select-all-categorias">
                                                    Seleccionar todas las categorías
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <!-- Filtro por Productos -->
                                        <div class="mb-3">
                                            <label class="form-label">Productos</label>
                                            <div id="productos-container" class="mb-3">
                                                <p class="text-muted">Selecciona una categoría para ver los productos disponibles</p>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="select-all-productos" checked>
                                                <label class="form-check-label" for="select-all-productos">
                                                    Seleccionar todos los productos
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <!-- Filtro por Tipo de Venta -->
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Venta</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipo_venta_categoria" id="tipo_todos_categoria" value="todos" checked>
                                                <label class="form-check-label" for="tipo_todos_categoria">
                                                    Todos
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipo_venta_categoria" id="tipo_venta_normal" value="venta">
                                                <label class="form-check-label" for="tipo_venta_normal">
                                                    Venta Normal
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipo_venta_categoria" id="tipo_financiamiento" value="financiamiento">
                                                <label class="form-check-label" for="tipo_financiamiento">
                                                    Financiamiento
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <!-- Filtros adicionales para Financiamiento -->
                                        <div id="filtros-financiamiento" style="display: none;">
                                            <!-- Filtro por Grupos de Financiamiento -->
                                            <div class="mb-3">
                                                <label class="form-label">Grupos de Financiamiento</label>
                                                <div id="grupos-container" class="mb-3">
                                                    <p class="text-muted">Cargando grupos de financiamiento...</p>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="select-all-grupos" checked>
                                                    <label class="form-check-label" for="select-all-grupos">
                                                        Seleccionar todos los grupos
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <!-- Filtro por Variantes -->
                                            <div class="mb-3">
                                                <label class="form-label">Variantes de Financiamiento</label>
                                                <div id="variantes-container" class="mb-3">
                                                    <p class="text-muted">Selecciona un grupo para ver las variantes disponibles</p>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="select-all-variantes" checked>
                                                    <label class="form-check-label" for="select-all-variantes">
                                                        Seleccionar todas las variantes
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Filtro por Moneda -->
                                        <div class="mb-3">
                                            <label class="form-label">Moneda</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="moneda_categoria" id="moneda_todos" value="todos" checked>
                                                <label class="form-check-label" for="moneda_todos">
                                                    Todos
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="moneda_categoria" id="moneda_soles" value="soles">
                                                <label class="form-check-label" for="moneda_soles">
                                                    Soles (S/.)
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="moneda_categoria" id="moneda_dolares" value="dolares">
                                                <label class="form-check-label" for="moneda_dolares">
                                                    Dólares ($)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    
                    <!-- Step 3: Preview and Download -->
                    <div class="wizard-step-content" id="step-3">
                        <h3 class="mb-4">Vista Previa del Reporte</h3>
                        
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-success me-2" id="download-excel">
                                <i class="fas fa-file-excel me-1"></i> Exportar Excel
                            </button>
                            <button type="button" class="btn btn-danger" id="download-pdf">
                                <i class="fas fa-file-pdf me-1"></i> Exportar PDF
                            </button>
                        </div>
                        
                        <div class="card">
                            <div class="card-body">
                                <div id="report-preview">
                                    <div class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando vista previa...</span>
                                        </div>
                                        <p class="mt-3">Generando vista previa del reporte...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation Buttons -->
                <div class="wizard-navigation mt-4">
                    <button type="button" class="btn btn-secondary" id="prev-btn" disabled>
                        <i class="fas fa-arrow-left me-1"></i> Anterior
                    </button>
                    <button type="button" class="btn btn-primary" id="next-btn">
                        Siguiente <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Custom JS -->
    
    <script>
        $(document).ready(function() {
            // Global variables
            let currentStep = 1;
            let selectedReportType = '';
            let reportData = null;
            const totalSteps = 3;
            
            // Initialize date inputs with default values (current month)
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            
            // Format dates to YYYY-MM-DD
            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
            
            $('#fecha_inicio').val(formatDate(firstDay));
            $('#fecha_fin').val(formatDate(lastDay));
            
            // Event handlers
            $('#next-btn').on('click', nextStep);
            $('#prev-btn').on('click', prevStep);
            $('.report-card').on('click', selectReportType);
            $('#download-excel').on('click', downloadExcel);
            $('#download-pdf').on('click', downloadPDF);
            $('#select-all-empleados').on('change', toggleAllEmpleados);
            $('#todos_los_tiempos').on('change', toggleTodosLosTiempos);
            // Busca donde están los otros event handlers y agrega estos:
            $('#select-all-categorias').on('change', toggleAllCategorias);
            $('#select-all-productos').on('change', toggleAllProductos);

            // Y agrega estas funciones también:
            function toggleAllCategorias() {
                const isChecked = $(this).prop('checked');
                $('.categoria-checkbox').prop('checked', isChecked);
                loadProductosPorCategoria(); // Recargar productos cuando cambian las categorías
            }

            function toggleAllProductos() {
                const isChecked = $(this).prop('checked');
                $('.producto-checkbox').prop('checked', isChecked);
            }


            // Handle report card selection
            function selectReportType() {
                $('.report-card').removeClass('selected');
                $(this).addClass('selected');
                selectedReportType = $(this).data('report-type');
            }
            
            // Load employees for filter (only when needed)
            function loadEmpleados() {
                $.ajax({
                    url: '/arequipago/get-empleados',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        const empleadosContainer = $('#empleados-container');
                        empleadosContainer.empty();
                        
                        if (response.success && response.data.length > 0) {
                            const checkboxContainer = $('<div class="empleado-checkbox-container"></div>');
                            
                            // Create checkboxes for each employee
                            response.data.forEach(function(empleado) {
                                const checkboxDiv = $(`
                                    <div class="form-check mb-2">
                                        <input class="form-check-input empleado-checkbox" type="checkbox" 
                                            id="empleado-${empleado.usuario_id}" value="${empleado.usuario_id}" checked>
                                        <label class="form-check-label" for="empleado-${empleado.usuario_id}">
                                            ${empleado.nombres} ${empleado.apellidos}
                                        </label>
                                    </div>
                                `);
                                checkboxContainer.append(checkboxDiv);
                            });
                            
                            empleadosContainer.append(checkboxContainer);
                            
                            // Event for individual checkboxes
                            $('.empleado-checkbox').on('change', function() {
                                updateSelectAllCheckbox();
                            });
                        } else {
                            empleadosContainer.html('<div class="alert alert-warning">No se encontraron asesores disponibles.</div>');
                        }
                    },
                    error: function() {
                        $('#empleados-container').html('<div class="alert alert-danger">Error al cargar la lista de asesores.</div>');
                    }
                });
            }
            
            // Update "Select All" checkbox state
            function updateSelectAllCheckbox() {
                const allChecked = $('.empleado-checkbox:checked').length === $('.empleado-checkbox').length;
                $('#select-all-empleados').prop('checked', allChecked);
            }
            
            // Toggle all employee checkboxes
            function toggleAllEmpleados() {
                const isChecked = $(this).prop('checked');
                $('.empleado-checkbox').prop('checked', isChecked);
            }
            
            // AGREGAR ESTA FUNCIÓN COMPLETA
            // Toggle "Todos los tiempos" functionality
            function toggleTodosLosTiempos() {
                const isChecked = $(this).prop('checked');
                const fechaInicioInput = $('#fecha_inicio');
                const fechaFinInput = $('#fecha_fin');
                
                if (isChecked) {
                    // Deshabilitar inputs y mostrar fechas de "todos los tiempos"
                    fechaInicioInput.prop('disabled', true);
                    fechaFinInput.prop('disabled', true);
                    
                    // Guardar valores actuales para restaurar si se desmarca
                    fechaInicioInput.data('valor-original', fechaInicioInput.val());
                    fechaFinInput.data('valor-original', fechaFinInput.val());
                    
                    // Establecer fechas para "todos los tiempos"
                    fechaInicioInput.val('2005-06-05');
                    fechaFinInput.val(formatDate(new Date()));
                    
                    // Agregar estilo visual para indicar que está en modo "todos los tiempos"
                    fechaInicioInput.addClass('bg-light fecha-todos-tiempos');
                    fechaFinInput.addClass('bg-light');
                } else {
                    // Habilitar inputs y restaurar valores originales
                    fechaInicioInput.prop('disabled', false);
                    fechaFinInput.prop('disabled', false);
                    
                    // Restaurar valores originales (primer día del mes actual)
                    const today = new Date();
                    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    
                    fechaInicioInput.val(formatDate(firstDay));
                    fechaFinInput.val(formatDate(lastDay));
                    
                    // Remover estilo visual
                    fechaInicioInput.removeClass('bg-light fecha-todos-tiempos');
                    fechaFinInput.removeClass('bg-light');
                }
            }

            // Move to next step
            function nextStep() {
                // Validate current step
                if (!validateStep(currentStep)) {
                    return;
                }
                
                // If we're on step 1, show the appropriate filters for the selected report
                if (currentStep === 1) {
                    showFiltersForReport(selectedReportType);
                }
                
                // If we're moving to step 3, load report preview
                if (currentStep === 2) {
                    loadReportPreview();
                }
                
                // Increment step counter
                if (currentStep < totalSteps) {
                    currentStep++;
                    updateWizardUI();
                }
            }
            
            // Move to previous step
            function prevStep() {
                if (currentStep > 1) {
                    currentStep--;
                    updateWizardUI();
                }
            }
            
            // Show filters based on selected report type
            function showFiltersForReport(reportType) {
                // Hide all filter sections first
                $('.filter-section').hide();
                
                // Show the relevant filter section
                $(`#${reportType}-filters`).show();
                
                // Load additional data if needed
                if (reportType === 'ventas-por-empleado') {
                    loadEmpleados();
                }

                // AGREGAR ESTA NUEVA CONDICIÓN
                else if (reportType === 'ventas-por-categoria') {
                    loadCategorias();
                }
            }
            
            // Validate current step before proceeding
            function validateStep(step) {
                if (step === 1) {
                    if (!selectedReportType) {
                        alert('Por favor, selecciona un tipo de reporte para continuar.');
                        return false;
                    }
                    return true;
                } else if (step === 2) {

                    // MODIFICAR ESTA SECCIÓN - Si "todos los tiempos" está marcado, no validar fechas
                    if ($('#todos_los_tiempos').prop('checked')) {
                        return true; // No validar fechas cuando está en modo "todos los tiempos"
                    }

                    const fechaInicio = $('#fecha_inicio').val();
                    const fechaFin = $('#fecha_fin').val();
                    
                    if (!fechaInicio || !fechaFin) {
                        alert('Por favor, selecciona un rango de fechas válido.');
                        return false;
                    }
                    
                    // Check if end date is greater than or equal to start date
                    if (new Date(fechaFin) < new Date(fechaInicio)) {
                        alert('La fecha final debe ser mayor o igual a la fecha inicial.');
                        return false;
                    }
                    
                    return true;
                }
                
                return true;
            }
            
            // Update the wizard UI based on current step
            function updateWizardUI() {
                // Update progress bar
                const progressPercent = (currentStep / totalSteps) * 100;
                $('.progress-bar').css('width', `${progressPercent}%`);
                $('.progress-bar').attr('aria-valuenow', progressPercent);
                
                // Update step indicators
                $('.wizard-step').removeClass('active');
                $(`#step-indicator-${currentStep}`).addClass('active');
                
                // Show/hide step content
                $('.wizard-step-content').removeClass('active');
                $(`#step-${currentStep}`).addClass('active');
                
                // Update navigation buttons
                $('#prev-btn').prop('disabled', currentStep === 1);
                
                if (currentStep === totalSteps) {
                    $('#next-btn').hide();
                } else {
                    $('#next-btn').show();
                }
            }
            
            // Load report preview data
            function loadReportPreview() {
                const reportPreview = $('#report-preview');
                reportPreview.html(`
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando vista previa...</span>
                        </div>
                        <p class="mt-3">Generando vista previa del reporte...</p>
                    </div>
                `);
                
                // Collect filter data
                const filterData = {
                    fecha_inicio: $('#todos_los_tiempos').prop('checked') ? '2005-06-05' : $('#fecha_inicio').val(),
                    fecha_fin: $('#todos_los_tiempos').prop('checked') ? formatDate(new Date()) : $('#fecha_fin').val()
                };
                
                // Add report-specific filters
                if (selectedReportType === 'ventas-por-empleado') {
                    const selectedEmpleados = [];
                    $('.empleado-checkbox:checked').each(function() {
                        selectedEmpleados.push($(this).val());
                    });
                    filterData.empleados = selectedEmpleados;
                } else if (selectedReportType === 'financiamientos') {
                    filterData.tipo_cliente = $('input[name="tipo_cliente"]:checked').val();
                } else if (selectedReportType === 'cuotas-pagadas') {
                    filterData.tipo_cliente = $('input[name="tipo_cliente_cuotas"]:checked').val();
                    filterData.incluir_morosos = $('#incluir_morosos').prop('checked');
                } else if (selectedReportType === 'ingresos') {
                    // Filtros de tipo de ingreso
                    filterData.inscripcion_contado = $('#inscripcion_contado').prop('checked');
                    filterData.inscripcion_financiada_cuotas = $('#inscripcion_financiada_cuotas').prop('checked');
                    filterData.inscripcion_financiada_inicial = $('#inscripcion_financiada_inicial').prop('checked');
                    filterData.financiamiento_cuota_inicial = $('#financiamiento_cuota_inicial').prop('checked');
                    filterData.financiamiento_cuotas = $('#financiamiento_cuotas').prop('checked');
                    filterData.financiamiento_monto_inscripcion = $('#financiamiento_monto_inscripcion').prop('checked');
                    filterData.financiamiento_monto_recalculado = $('#financiamiento_monto_recalculado').prop('checked');
                    filterData.ventas = $('#ventas').prop('checked');
                    
                    // Métodos de pago
                    const metodosPago = [];
                    $('.metodo-pago-checkbox:checked').each(function() {
                        metodosPago.push($(this).val());
                    });
                    filterData.metodos_pago = metodosPago;
                } else if (selectedReportType === 'ventas-por-categoria') {
                    // Filtros específicos para ventas por categoría
                    const categoriasSeleccionadas = [];
                    $('.categoria-checkbox:checked').each(function() {
                        categoriasSeleccionadas.push($(this).val());
                    });
                    filterData.categorias = categoriasSeleccionadas;
                    
                    const productosSeleccionados = [];
                    $('.producto-checkbox:checked').each(function() {
                        productosSeleccionados.push($(this).val());
                    });
                    filterData.productos = productosSeleccionados;
                    
                    filterData.tipo_venta = $('input[name="tipo_venta_categoria"]:checked').val();
                    filterData.moneda = $('input[name="moneda_categoria"]:checked').val();
                    
                    // Solo agregar filtros de financiamiento si el tipo de venta es financiamiento o todos
                    if (filterData.tipo_venta === 'financiamiento' || filterData.tipo_venta === 'todos') {
                        const gruposSeleccionados = [];
                        $('.grupo-checkbox:checked').each(function() {
                            gruposSeleccionados.push($(this).val());
                        });
                        filterData.grupos = gruposSeleccionados;
                        
                        const variantesSeleccionadas = [];
                        $('.variante-checkbox:checked').each(function() {
                            variantesSeleccionadas.push($(this).val());
                        });
                        filterData.variantes = variantesSeleccionadas;
                    }
                }
                // Make AJAX request for report data
                $.ajax({
                    url: `/arequipago/${selectedReportType}`,
                    method: 'POST',
                    data: filterData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            reportData = response.data;
                            renderReportPreview(reportData);
                        } else {
                            reportPreview.html(`
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    Error: ${response.message || 'Ocurrió un error al generar el reporte.'}
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        reportPreview.html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Error de conexión. Por favor, intenta nuevamente.
                            </div>
                        `);
                    }
                });
            }
            
            
            // Render report preview based on report type
            function renderReportPreview(data) {
                const reportPreview = $('#report-preview');
                
                // Clear previous content
                reportPreview.empty();
                
                // Create table header based on report type
                let tableHTML = '';
                
                switch (selectedReportType) {
                    case 'ventas-generales':
                        tableHTML = renderVentasGeneralesTable(data);
                        break;
                    case 'ventas-por-empleado':
                        tableHTML = renderVentasPorEmpleadoTable(data);
                        break;
                    case 'financiamientos':
                        tableHTML = renderFinanciamientosTable(data);
                        break;
                    case 'cuotas-pagadas':
                        tableHTML = renderCuotasPagadasTable(data);
                        break;
                    case 'ingresos':
                        tableHTML = renderIngresosTable(data);
                        break;
                    case 'ventas-por-categoria':
                        tableHTML = renderVentasPorCategoriaTable(data);
                        break;
                    default:
                        tableHTML = '<div class="alert alert-warning">Tipo de reporte no reconocido.</div>';
                }
                
                reportPreview.html(tableHTML);
            }
            
            // Render table for Ventas Generales
            function renderVentasGeneralesTable(data) {
                let html = `
                    <div class="table-responsive">
                        <table class="table table-report">
                            <tr class="total-row">
                                <td colspan="3">TOTAL GENERAL</td>
                                <td>${data.total_general}</td>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Productos</th>
                                <th>Total</th>
                                <th>Vendedor</th>
                                <th>Método de Pago</th>
                            </tr>
                `;
                
                // Add data rows
                data.registros.forEach(function(venta) {
                    html += `
                        <tr>
                            <td>${venta.fecha}</td>
                            <td>${venta.cliente}</td>
                            <td>${venta.productos.join(', ')}</td>
                            <td>${venta.total}</td>
                            <td>${venta.vendedor}</td>
                            <td>${venta.metodo_pago}</td>
                        </tr>
                    `;
                });
                
                html += `
                        </table>
                    </div>
                `;
                
                return html;
            }
            
            // Render table for Ventas por Empleado
            function renderVentasPorEmpleadoTable(data) {
                let html = `
                    <div class="table-responsive">
                        <table class="table table-report">
                `;
                
                // Add data rows grouped by asesor
                data.registros.forEach(function(asesor) {
                    html += `
                        <tr class="asesor-row">
                            <td colspan="5">${asesor.asesor}</td>
                        </tr>
                        <tr>
                            <th></th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Total Producto</th>
                        </tr>
                    `;
                    
                    asesor.productos.forEach(function(producto) {
                        html += `
                            <tr>
                                <td></td>
                                <td>${producto.nombre}</td>
                                <td>${producto.cantidad}</td>
                                <td>${producto.precio_unitario}</td>
                                <td>${producto.total_producto}</td>
                            </tr>
                        `;
                    });
                    
                    html += `
                        <tr class="subtotal-row">
                            <td colspan="4">Subtotal</td>
                            <td>${asesor.total}</td>
                        </tr>
                        <tr><td colspan="5">&nbsp;</td></tr>
                    `;
                });
                
                // Add total general
                html += `
                        <tr class="total-row">
                            <td colspan="4">TOTAL GENERAL</td>
                            <td>${data.total_general}</td>
                        </tr>
                    </table>
                </div>
                `;
                
                return html;
            }
            
            // Render table for Financiamientos
            function renderFinanciamientosTable(data) {
                // Check if we need to include numUnidad column
                const incluirNumUnidad = data.registros.some(item => item.tipo_cliente === 'Conductor' && item.numUnidad);
                
                let html = `
                    <div class="table-responsive">
                        <table class="table table-report">
                            <tr>
                                <th>Nro. Documento</th>
                                <th>Cliente</th>
                                <th>Código Asociado</th>
                                <th>Producto</th>
                                <th>Grupo</th>
                                <th>Cuotas Totales</th>
                                <th>Cuotas Pagadas</th>
                                <th>Cuotas Pendientes</th>
                                <th>Saldo Pendiente</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                ${incluirNumUnidad ? '<th>Número Unidad</th>' : ''}
                            </tr>
                `;
                
                // Add data rows
                data.registros.forEach(function(financiamiento) {
                    html += `
                        <tr>
                            <td>${financiamiento.nro_documento}</td>
                            <td>${financiamiento.cliente}</td>
                            <td>${financiamiento.codigo_asociado}</td>
                            <td>${financiamiento.producto}</td>
                            <td>${financiamiento.grupo}</td>
                            <td>${financiamiento.cuotas_totales}</td>
                            <td>${financiamiento.cuotas_pagadas}</td>
                            <td>${financiamiento.cuotas_pendientes}</td>
                            <td>${financiamiento.saldo_pendiente}</td>
                            <td>${financiamiento.fecha_inicio}</td>
                            <td>${financiamiento.fecha_fin}</td>
                            ${incluirNumUnidad ? `<td>${financiamiento.numUnidad || ''}</td>` : ''}
                        </tr>
                    `;
                });
                
                html += `
                        </table>
                    </div>
                `;
                
                return html;
            }
            
            // Render table for Cuotas Pagadas
            function renderCuotasPagadasTable(data) {
                // Check if we need to include numUnidad column
                const incluirNumUnidad = data.cuotas_por_cliente.some(item => item.tipo_cliente === 'Conductor' && item.numUnidad);
                
                let html = `
                    <div class="mb-4">
                        <h4>Cuotas por Cliente</h4>
                        <div class="table-responsive">
                            <table class="table table-report">
                                <tr>
                                    <th>Nro. Documento</th>
                                    <th>Cliente</th>
                                    <th>Tipo</th>
                                    <th>Producto</th>
                                    <th>Cuotas Totales</th>
                                    <th>Cuotas Pagadas</th>
                                    <th>Cuotas Pendientes</th>
                                    <th>Saldo Pendiente</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    ${incluirNumUnidad ? '<th>Número Unidad</th>' : ''}
                                </tr>
                `;
                
                // Add data rows for cuotas por cliente
                data.cuotas_por_cliente.forEach(function(cliente) {
                    cliente.financiamientos.forEach(function(financiamiento) {
                        html += `
                            <tr>
                                <td>${cliente.nro_documento}</td>
                                <td>${cliente.cliente}</td>
                                <td>${cliente.tipo_cliente}</td>
                                <td>${financiamiento.producto}</td>
                                <td>${financiamiento.cuotas_totales}</td>
                                <td>${financiamiento.cuotas_pagadas}</td>
                                <td>${financiamiento.cuotas_pendientes}</td>
                                <td>${financiamiento.saldo_pendiente}</td>
                                <td>${financiamiento.fecha_inicio}</td>
                                <td>${financiamiento.fecha_fin}</td>
                                ${incluirNumUnidad ? `<td>${cliente.numUnidad || ''}</td>` : ''}
                            </tr>
                        `;
                    });
                });
                
                html += `
                            </table>
                        </div>
                    </div>
                `;
                
                // Add clientes morosos section if there are any
                if (data.clientes_morosos && data.clientes_morosos.length > 0) {
                    html += `
                        <div>
                            <h4>Clientes Morosos</h4>
                            <div class="table-responsive">
                                <table class="table table-report">
                                    <tr>
                                        <th>Nro. Documento</th>
                                        <th>Cliente</th>
                                        <th>Tipo</th>
                                        <th>Producto</th>
                                        <th>Cuotas Vencidas</th>
                                        <th>Monto Vencido</th>
                                        ${incluirNumUnidad ? '<th>Número Unidad</th>' : ''}
                                    </tr>
                    `;
                    
                    // Add data rows for clientes morosos
                    data.clientes_morosos.forEach(function(moroso) {
                        html += `
                            <tr>
                                <td>${moroso.nro_documento}</td>
                                <td>${moroso.cliente}</td>
                                <td>${moroso.tipo_cliente}</td>
                                <td>${moroso.producto}</td>
                                <td>${moroso.cuotas_vencidas}</td>
                                <td>${moroso.monto_vencido.toFixed(2)}</td>
                                ${incluirNumUnidad ? `<td>${moroso.numUnidad || ''}</td>` : ''}
                            </tr>
                        `;
                    });
                    
                    html += `
                                </table>
                            </div>
                        </div>
                    `;
                }
                
                return html;
            }
            
            // Download Excel report
            function downloadExcel() {
                console.log('Click en botón exportar');
                console.log('selectedReportType:', selectedReportType);
                console.log('reportData:', reportData);

                if (!reportData) {
                    alert('No hay datos para exportar. Por favor, genera el reporte primero.');
                    return;
                }
                
                // Prepare data for download
                const downloadData = {
                    tipo_reporte: selectedReportType,
                    data: reportData
                };
                
                // Create a form to submit the data
                const form = $('<form></form>')
                    .attr('method', 'post')
                    .attr('action', '/arequipago/download-excel')
                    .css('display', 'none');
                
                // Add data as hidden inputs
                form.append($('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'tipo_reporte')
                    .attr('value', downloadData.tipo_reporte));
                
                form.append($('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'data')
                    .attr('value', JSON.stringify(downloadData.data)));
                
                // Append form to body, submit it, and remove it
                $('body').append(form);
                form.submit();
                form.remove();
            }
            
            // Download PDF report
            function downloadPDF() {
                if (!reportData) {
                    alert('No hay datos para exportar. Por favor, genera el reporte primero.');
                    return;
                }
                
                // Prepare data for download
                const downloadData = {
                    tipo_reporte: selectedReportType,
                    data: reportData
                };
                
                // Create a form to submit the data
                const form = $('<form></form>')
                    .attr('method', 'post')
                    .attr('action', '/arequipago/download-pdf')
                    .css('display', 'none');
                
                // Add data as hidden inputs
                form.append($('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'tipo_reporte')
                    .attr('value', downloadData.tipo_reporte));
                
                form.append($('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'data')
                    .attr('value', JSON.stringify(downloadData.data)));
                
                // Append form to body, submit it, and remove it
                $('body').append(form);
                form.submit();
                form.remove();
            }

            // 🔴 Evento para seleccionar/deseleccionar todos los métodos de pago
            $('#select-all-metodos').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('.metodo-pago-checkbox').prop('checked', isChecked);
            });

            // 🔴 Evento para actualizar el estado del checkbox "seleccionar todos"
            $('.metodo-pago-checkbox').on('change', function() {
                updateSelectAllMetodosCheckbox();
            });

            // 🔴 Función para actualizar el estado del checkbox "seleccionar todos"
            function updateSelectAllMetodosCheckbox() {
                const allChecked = $('.metodo-pago-checkbox:checked').length === $('.metodo-pago-checkbox').length;
                $('#select-all-metodos').prop('checked', allChecked);
            }

            // Función para cargar categorías
            function loadCategorias() {
                $.ajax({
                    url: '/arequipago/get-categorias',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        const categoriasContainer = $('#categorias-container');
                        categoriasContainer.empty();
                        
                        if (response.success && response.data.length > 0) {
                            const checkboxContainer = $('<div class="empleado-checkbox-container"></div>');
                            
                            // Create checkboxes for each category
                            response.data.forEach(function(categoria) {
                                const checkboxDiv = $(`
                                    <div class="form-check mb-2">
                                        <input class="form-check-input categoria-checkbox" type="checkbox" 
                                            id="categoria-${categoria.idcategoria_producto}" value="${categoria.nombre}" checked>
                                        <label class="form-check-label" for="categoria-${categoria.idcategoria_producto}">
                                            ${categoria.nombre}
                                        </label>
                                    </div>
                                `);
                                checkboxContainer.append(checkboxDiv);
                            });
                            
                            categoriasContainer.append(checkboxContainer);
                            
                            // Event for individual checkboxes
                            $('.categoria-checkbox').on('change', function() {
                                updateSelectAllCategoriasCheckbox();
                                loadProductosPorCategoria(); // Cargar productos cuando cambian las categorías
                            });
                            
                            // Cargar productos inicialmente
                            loadProductosPorCategoria();
                        } else {
                            categoriasContainer.html('<div class="alert alert-warning">No se encontraron categorías disponibles.</div>');
                        }
                    },
                    error: function() {
                        $('#categorias-container').html('<div class="alert alert-danger">Error al cargar las categorías.</div>');
                    }
                });
            }

            // Función para cargar productos por categoría
            function loadProductosPorCategoria() {
                const categoriasSeleccionadas = [];
                $('.categoria-checkbox:checked').each(function() {
                    categoriasSeleccionadas.push($(this).val());
                });
                
                if (categoriasSeleccionadas.length === 0) {
                    $('#productos-container').html('<p class="text-muted">Selecciona al menos una categoría para ver los productos</p>');
                    return;
                }
                
                $.ajax({
                    url: '/arequipago/get-productos-por-categoria',
                    method: 'POST',
                    data: { categorias: categoriasSeleccionadas },
                    dataType: 'json',
                    success: function(response) {
                        const productosContainer = $('#productos-container');
                        productosContainer.empty();
                        
                        if (response.success && response.data.length > 0) {
                            const checkboxContainer = $('<div class="empleado-checkbox-container"></div>');
                            
                            // Create checkboxes for each product
                            response.data.forEach(function(producto) {
                                const checkboxDiv = $(`
                                    <div class="form-check mb-2">
                                        <input class="form-check-input producto-checkbox" type="checkbox" 
                                            id="producto-${producto.idproductosv2}" value="${producto.idproductosv2}" checked>
                                        <label class="form-check-label" for="producto-${producto.idproductosv2}">
                                            <small class="text-muted">[${producto.categoria}]</small> ${producto.nombre}
                                        </label>
                                    </div>
                                `);
                                checkboxContainer.append(checkboxDiv);
                            });
                            
                            productosContainer.append(checkboxContainer);
                            
                            // Event for individual checkboxes
                            $('.producto-checkbox').on('change', function() {
                                updateSelectAllProductosCheckbox();
                            });
                        } else {
                            productosContainer.html('<div class="alert alert-warning">No se encontraron productos para las categorías seleccionadas.</div>');
                        }
                    },
                    error: function() {
                        $('#productos-container').html('<div class="alert alert-danger">Error al cargar los productos.</div>');
                    }
                });
            }

            // Update "Select All" categorias checkbox state
            function updateSelectAllCategoriasCheckbox() {
                const allChecked = $('.categoria-checkbox:checked').length === $('.categoria-checkbox').length;
                $('#select-all-categorias').prop('checked', allChecked);
            }

            // Update "Select All" productos checkbox state
            function updateSelectAllProductosCheckbox() {
                const allChecked = $('.producto-checkbox:checked').length === $('.producto-checkbox').length;
                $('#select-all-productos').prop('checked', allChecked);
            }

        });

// 🔴 Función para renderizar tabla de ingresos
function renderIngresosTable(data) {
    let html = `
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-2">💰 Total en soles (PEN): S/ ${data.total_soles}</h5>
                    <h5 class="mb-0">💵 Total en dólares (USD): $${data.total_dolares}</h5>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-primary me-2" id="btn-convertir-soles">
                        Convertir todo a soles
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" id="btn-convertir-dolares">
                        Convertir todo a dólares
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-report" id="tabla-ingresos">
                <tr>
                    <th>FECHA</th>
                    <th>TIPO DE INGRESO</th>
                    <th>CATEGORÍA</th>
                    <th>DETALLE</th>
                    <th>CLIENTE</th>
                    <th>Nº DOCUMENTO</th>
                    <th>MONEDA</th>
                    <th>MONTO</th>
                    <th>FORMA DE PAGO</th>
                    <th>Nº CUOTA</th>
                    <th>TOTAL CUOTAS</th>
                    <th>ASESOR DE COBRO</th>
                </tr>
    `;
    
    // Agregar datos
    data.registros.forEach(function(ingreso) {
        html += `
            <tr data-moneda="${ingreso.moneda}" data-monto="${ingreso.monto}">
                <td>${ingreso.fecha}</td>
                <td>${ingreso.tipo_ingreso}</td>
                <td>${ingreso.categoria}</td>
                <td>${ingreso.detalle}</td>
                <td>${ingreso.cliente}</td>
                <td>${ingreso.nro_documento}</td>
                <td>${ingreso.moneda}</td>
                <td>${ingreso.monto}</td>
                <td>${ingreso.forma_pago}</td>
                <td>${ingreso.nro_cuota}</td>
                <td>${ingreso.total_cuotas}</td>
                <td>${ingreso.asesor_cobro}</td>
            </tr>
        `;
    });
    
    html += `
            </table>
        </div>
    `;
    
    // Agregar al contenedor
    const reportPreview = $('#report-preview');
    reportPreview.html(html);
    
    // Agregar eventos para los botones de conversión
    $('#btn-convertir-soles').on('click', function() {
        convertirMoneda('soles', data.tipo_cambio);
    });
    
    $('#btn-convertir-dolares').on('click', function() {
        convertirMoneda('dolares', data.tipo_cambio);
    });
}

// 🔴 Función para convertir moneda
function convertirMoneda(tipoConversion, tipoCambio) {
    const tabla = $('#tabla-ingresos');
    let totalSoles = 0;
    let totalDolares = 0;
    
    // Recorrer todas las filas de la tabla
    tabla.find('tr').each(function(index) {
        // Omitir la fila de encabezado
        if (index === 0) return;
        
        const fila = $(this);
        const monedaOriginal = fila.data('moneda');
        const montoOriginal = parseFloat(fila.data('monto'));
        
        let montoConvertido = montoOriginal;
        let nuevaMoneda = monedaOriginal;
        
        // Convertir según el tipo solicitado
        if (tipoConversion === 'soles') {
            if (monedaOriginal === '$') {
                montoConvertido = montoOriginal * tipoCambio;
                nuevaMoneda = 'S/.';
            }
            totalSoles += montoConvertido;
        } else if (tipoConversion === 'dolares') {
            if (monedaOriginal === 'S/.') {
                montoConvertido = montoOriginal / tipoCambio;
                nuevaMoneda = '$';
            }
            totalDolares += montoConvertido;
        }
        
        // Actualizar la celda de moneda y monto
        fila.find('td:eq(6)').text(nuevaMoneda);
        fila.find('td:eq(7)').text(montoConvertido.toFixed(2));
    });
    
    // Actualizar totales
    if (tipoConversion === 'soles') {
        $('h5:contains("Total en soles")').text(`💰 Total en soles (PEN): S/ ${totalSoles.toFixed(2)}`);
        $('h5:contains("Total en dólares")').text(`💵 Total en dólares (USD): $0.00`);
    } else if (tipoConversion === 'dolares') {
        $('h5:contains("Total en soles")').text(`💰 Total en soles (PEN): S/ 0.00`);
        $('h5:contains("Total en dólares")').text(`💵 Total en dólares (USD): $${totalDolares.toFixed(2)}`);
    }
}


// Función para renderizar tabla de ventas por categoría
function renderVentasPorCategoriaTable(data) {
    let html = `
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-2">💰 Total en soles (PEN): S/ ${data.total_soles}</h5>
                    <h5 class="mb-0">💵 Total en dólares (USD): $${data.total_dolares}</h5>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-report">
                <tr>
                    <th>CATEGORÍA</th>
                    <th>PRODUCTO</th>
                    <th>TIPO DE VENTA</th>
                    <th>CANTIDAD VENDIDA</th>
                    <th>PRECIO UNITARIO</th>
                    <th>TOTAL PRODUCTO</th>
                    <th>MONEDA</th>
                    <th>GRUPO/VARIANTE</th>
                    <th>FECHA EMISIÓN</th>
                    <th>VENDEDOR</th>
                </tr>
    `;
    
    // Agrupar por categoría
    const categorias = {};
    data.registros.forEach(function(item) {
        if (!categorias[item.categoria]) {
            categorias[item.categoria] = [];
        }
        categorias[item.categoria].push(item);
    });
    
    // Renderizar por categoría
    Object.keys(categorias).forEach(function(categoria) {
        // Fila de categoría
        html += `
            <tr class="table-secondary">
                <td colspan="10"><strong>${categoria}</strong></td>
            </tr>
        `;
        
        let totalCategoriaSoles = 0;
        let totalCategoriaDolares = 0;
        
        // Productos de la categoría
        categorias[categoria].forEach(function(producto) {
            html += `
                <tr>
                    <td></td>
                    <td>${producto.producto}</td>
                    <td>${producto.tipo_venta}</td>
                    <td>${producto.cantidad}</td>
                    <td>${producto.precio_unitario}</td>
                    <td>${producto.total_producto}</td>
                    <td>${producto.moneda}</td>
                    <td>${producto.grupo_variante || ''}</td>
                    <td>${new Date(producto.fecha_emision).toLocaleDateString('es-ES')}</td>
                    <td>${producto.nombre_vendedor}</td>
                </tr>
            `;
            
            // Sumar totales por categoría
            if (producto.moneda === 'S/.') {
                totalCategoriaSoles += parseFloat(producto.total_producto.replace(/,/g, ''));
            } else {
                totalCategoriaDolares += parseFloat(producto.total_producto.replace(/,/g, ''));
            }
        });
        
        // Subtotal por categoría
        html += `
            <tr class="table-info">
                <td colspan="7"><strong>Subtotal ${categoria}</strong></td>
                <td><strong>S/ ${totalCategoriaSoles.toFixed(2)} | $ ${totalCategoriaDolares.toFixed(2)}</strong></td>
                <td colspan="2"></td>
            </tr>
            <tr><td colspan="10">&nbsp;</td></tr>
        `;
    });
    
    // Total general
    html += `
        <tr class="table-dark">
            <td colspan="7"><strong>TOTAL GENERAL</strong></td>
            <td><strong>S/ ${data.total_soles} | $ ${data.total_dolares}</strong></td>
            <td colspan="2"></td>
        </tr>
    `;
    
    html += `
            </table>
        </div>
    `;
    
    return html;
}


    </script>
</body>
</html>
