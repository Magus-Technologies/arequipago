 <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Vehículos</title>

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .card-custom {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead {
            background: linear-gradient(135deg, #D6D64F 0%, #F5F527 100%);
            color: #000000;
        }

        .table tbody tr:hover {
            background-color: #f8f9ff;
            cursor: pointer;
        }

        .btn-action {
            margin: 2px;
            padding: 5px 10px;
            font-size: 14px;
        }

        .badge-estado {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding-left: 40px;
            border-radius: 25px;
            border: 2px solid #e0e0e0;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <!-- Header Section -->
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-car"></i> Gestión de Vehículos</h2>
                    <p class="mb-0">Administra el inventario de vehículos</p>
                </div>
                <div>
                    <!-- ✅ NUEVO: Botón para descargar reporte Excel de vehículos -->
                    <button class="btn btn-success btn-lg me-2" onclick="downloadReportVehiculos()">
                        <i class="fa fa-file-excel"></i> Descargar Excel
                    </button>
                    <button class="btn btn-light btn-lg" onclick="abrirModalAgregarVehiculo()">
                        <i class="fas fa-plus"></i> Agregar Vehículo
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Buscar vehículo</label>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text"
                               class="form-control"
                               id="buscadorVehiculos"
                               placeholder="Buscar por marca, modelo, placa, chasis, VIN...">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Oficina</label>
                    <select class="form-select" id="filtroOficinaVehiculo" onchange="cambiarOficinaVehiculo()">
                        <option value="" selected>Todas las oficinas</option>
                        <option value="1">Oficina 1</option>
                        <option value="2">Oficina 2</option>
                        <option value="3">Oficina Lima</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Marca</label>
                    <select class="form-select" id="filtroMarca" onchange="filtrarVehiculos()">
                        <option value="">Todas las marcas</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Año</label>
                    <select class="form-select" id="filtroAnio" onchange="filtrarVehiculos()">
                        <option value="">Todos los años</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="card card-custom">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tablaVehiculos">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Año</th>
                                <th>Placa</th>
                                <th>Color</th>
                                <th>Transmisión</th>
                                <th>Stock</th>
                                <th>Precio Venta</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-vehiculos">
                            <tr>
                                <td colspan="12" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-3 text-muted">Cargando vehículos...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div id="info-pagination" class="text-muted">
                Mostrando 0 de 0 vehículos
            </div>
            <nav>
                <ul class="pagination mb-0" id="pagination-container">
                    <!-- Se llenará dinámicamente -->
                </ul>
            </nav>
        </div>
    </div>

    <!-- Modal Ver Detalles -->
    <div class="modal fade" id="modalDetallesVehiculo" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-car me-2"></i>Detalles del Vehículo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Información Básica -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información Básica</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">Nombre del Vehículo</label>
                                    <p class="fw-bold mb-0" id="detalle-nombre"></p>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted small">Código</label>
                                    <p class="fw-bold mb-0" id="detalle-codigo"></p>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted small">Stock Disponible</label>
                                    <p class="fw-bold mb-0" id="detalle-stock"></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">Marca</label>
                                    <p class="mb-0" id="detalle-marca"></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">Modelo</label>
                                    <p class="mb-0" id="detalle-modelo"></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">Año</label>
                                    <p class="mb-0" id="detalle-anio"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Especificaciones Técnicas -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Especificaciones Técnicas</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">VIN</label>
                                    <p class="mb-0" id="detalle-vin"></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Chasis</label>
                                    <p class="mb-0" id="detalle-chasis"></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">Placa</label>
                                    <p class="mb-0" id="detalle-placa"></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">Color</label>
                                    <p class="mb-0" id="detalle-color"></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">Transmisión</label>
                                    <p class="mb-0" id="detalle-transmision"></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">Kilometraje</label>
                                    <p class="mb-0" id="detalle-kilometraje"></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">GPS</label>
                                    <p class="mb-0" id="detalle-gps"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Precios -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Información de Precios</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="text-muted small">Precio de Venta</label>
                                    <p class="fw-bold text-primary mb-0" id="detalle-precio"></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">Descuento por Cuota</label>
                                    <p class="fw-bold text-warning mb-0" id="detalle-descuento-cuota"></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">Moneda</label>
                                    <p class="mb-0" id="detalle-moneda"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div class="card mb-3" id="detalle-info-adicional" style="display: none;">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Información Adicional</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">Vencimiento SOAT</label>
                                    <p class="mb-0" id="detalle-soat"></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Vencimiento Seguro</label>
                                    <p class="mb-0" id="detalle-seguro"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Vehículo -->
    <div class="modal fade" id="modal-add-prod-vehiculo" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title"><i class="fas fa-car"></i> Agregar Nuevo Vehículo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formVehiculo">
                        <!-- Información Básica -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="nombre_producto_vehiculo" class="form-label">Nombre del Vehículo <span class="text-danger">*</span></label>
                                <input type="text" id="nombre_producto_vehiculo" class="form-control" required placeholder="Ej: Toyota Corolla 2024">
                            </div>
                            <div class="col-md-4">
                                <label for="codigo_producto_vehiculo" class="form-label">Código</label>
                                <input type="text" id="codigo_producto_vehiculo" class="form-control" placeholder="Opcional">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="categoria_producto_vehiculo" class="form-label">Categoría <span class="text-danger">*</span></label>
                                <select id="categoria_producto_vehiculo" class="form-select" onchange="mostrarIntfechaVehiculo()" required disabled>
                                    <option value="">Cargando...</option>
                                </select>
                                <small class="form-text text-muted">La categoría está fija como Vehículo</small>
                            </div>
                            <div class="col-md-4">
                                <label for="oficina_vehiculo" class="form-label">Oficina <span class="text-danger">*</span></label>
                                <select id="oficina_vehiculo" class="form-select" required>
                                    <option value="1" selected>Oficina 1</option>
                                    <option value="2">Oficina 2</option>
                                    <option value="3">Oficina Lima</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="cantidad_producto_vehiculo" class="form-label">Cantidad <span class="text-danger">*</span></label>
                                <input type="number" id="cantidad_producto_vehiculo" class="form-control" required value="1" min="1">
                            </div>
                        </div>

                        <!-- Detalles del Vehículo -->
                        <div id="vehiculo_wrapper_modal" style="display: none;">
                            <hr class="my-3">
                            <h6 class="mb-3"><i class="fas fa-car-side"></i> Detalles del Vehículo</h6>

                            <!-- Marca y Modelo -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="marca_vehiculo_modal" class="form-label">Marca</label>
                                    <input type="text" id="marca_vehiculo_modal" class="form-control" placeholder="Ej: Toyota, Nissan">
                                </div>
                                <div class="col-md-6">
                                    <label for="modelo_vehiculo_modal" class="form-label">Modelo</label>
                                    <input type="text" id="modelo_vehiculo_modal" class="form-control" placeholder="Ej: Corolla, Sentra">
                                </div>
                            </div>

                            <!-- VIN y Chasis -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="vin_modal" class="form-label">VIN</label>
                                    <input type="text" id="vin_modal" class="form-control" placeholder="Número de identificación vehicular">
                                </div>
                                <div class="col-md-6">
                                    <label for="chasis_modal" class="form-label">Nº de Motor/Chasis</label>
                                    <input type="text" id="chasis_modal" class="form-control" placeholder="Número de motor">
                                </div>
                            </div>

                            <!-- Color y Año -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="color_modal" class="form-label">Color</label>
                                    <input type="text" id="color_modal" class="form-control" placeholder="Ej: Blanco, Negro">
                                </div>
                                <div class="col-md-6">
                                    <label for="anio_modal" class="form-label">Año</label>
                                    <input type="number" id="anio_modal" class="form-control" min="1900" max="2099" placeholder="Ej: 2024">
                                </div>
                            </div>

                            <!-- Placa y Transmisión -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="placa_vehiculo_modal" class="form-label">Placa</label>
                                    <input type="text" id="placa_vehiculo_modal" class="form-control" placeholder="Ej: ABC-123 o 'En trámite'">
                                    <small class="form-text text-muted">Si no tiene placa, escriba "En trámite"</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="transmision_vehiculo_modal" class="form-label">Transmisión</label>
                                    <select id="transmision_vehiculo_modal" class="form-select">
                                        <option value="">Seleccionar</option>
                                        <option value="Manual">Manual</option>
                                        <option value="Automático">Automático</option>
                                        <!-- <option value="Automática">Automática</option> -->
                                    </select>
                                </div>
                            </div>

                            <!-- Kilometraje -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="kilometraje_modal" class="form-label">Kilometraje</label>
                                    <input type="number" id="kilometraje_modal" class="form-control" min="0" placeholder="Ej: 15000">
                                    <small class="form-text text-muted">Kilometraje actual del vehículo</small>
                                </div>
                            </div>

                            <!-- GPS -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="gps_activo_modal" name="gps_activo" value="1">
                                        <label class="form-check-label" for="gps_activo_modal">
                                            <i class="fas fa-map-marker-alt me-1"></i> GPS Activo
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Fechas SOAT y Seguro -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="fecha_venc_soat_modal" class="form-label">Fecha Vencimiento SOAT</label>
                                    <input type="date" id="fecha_venc_soat_modal" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="fecha_venc_seguro_modal" class="form-label">Fecha Vencimiento Seguro</label>
                                    <input type="date" id="fecha_venc_seguro_modal" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Información del Proveedor -->
                        <hr class="my-3">
                        <h6 class="mb-3"><i class="fas fa-building"></i> Información del Proveedor</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="rucInput_vehiculo" class="form-label">RUC <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" id="rucInput_vehiculo" class="form-control" maxlength="11" required>
                                    <button type="button" class="btn btn-secondary" onclick="consultarRUCVehiculo()">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="razon_vehiculo" class="form-label">Razón Social <span class="text-danger">*</span></label>
                                <input type="text" id="razon_vehiculo" class="form-control" required readonly>
                            </div>
                        </div>

                        <!-- Información de Precios -->
                        <hr class="my-3">
                        <h6 class="mb-3"><i class="fas fa-dollar-sign"></i> Información de Precios</h6>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="moneda_vehiculo" class="form-label">Moneda</label>
                                <select id="moneda_vehiculo" class="form-select">
                                    <option value="S/.">S/.</option>
                                    <option value="$">$</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="precio_vehiculo" class="form-label">Precio Compra <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" id="precio_vehiculo" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label for="precioVenta_vehiculo" class="form-label">Precio Venta <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" id="precioVenta_vehiculo" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label for="descuentoCuota_vehiculo" class="form-label">Descuento por Cuota</label>
                                <input type="number" step="0.01" id="descuentoCuota_vehiculo" class="form-control" placeholder="0.00">
                            </div>
                        </div>

                        <!-- Información Adicional -->
                        <hr class="my-3">
                        <h6 class="mb-3"><i class="fas fa-info-circle"></i> Información Adicional</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="guia_remision_vehiculo" class="form-label">Guía de Remisión</label>
                                <input type="text" id="guia_remision_vehiculo" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="fechaActual_vehiculo" class="form-label">Fecha de Registro</label>
                                <input type="date" id="fechaActual_vehiculo" class="form-control" readonly>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="guardarVehiculo()">
                        <i class="fas fa-save"></i> Guardar Vehículo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let vehiculos = [];
        let vehiculosFiltrados = [];
        const itemsPorPagina = 10;
        let paginaActual = 1;

        // Cargar vehículos al iniciar
        $(document).ready(function() {
            cargarVehiculos();
            cargarCategoriasVehiculo();
            setFechaActualVehiculo();

            // Evento de búsqueda
            $('#buscadorVehiculos').on('keyup', function() {
                filtrarVehiculos();
            });
        });

        // Función para cargar vehículos
        function cargarVehiculos() {
            // Obtener la oficina seleccionada del filtro
            var oficinaSeleccionada = $('#filtroOficinaVehiculo').val() || 1;

            $.ajax({
                url: '/arequipago/obtenerVehiculos',
                type: 'GET',
                data: { oficina: oficinaSeleccionada },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        vehiculos = response.vehiculos;
                        vehiculosFiltrados = vehiculos;
                        llenarFiltros();
                        mostrarVehiculos();
                    } else {
                        mostrarError('No se pudieron cargar los vehículos');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar vehículos:', error);
                    mostrarError('Error al cargar los vehículos');
                }
            });
        }

        // Función global para cambiar de oficina en vehículos
        window.cambiarOficinaVehiculo = function() {
            cargarVehiculos(); // Recargar los vehículos de la oficina seleccionada
        }

        // Función para llenar los filtros
        function llenarFiltros() {
            // Llenar filtro de marcas
            const marcas = [...new Set(vehiculos.map(v => v.marca).filter(m => m && m !== 'N/A'))].sort();
            const selectMarca = $('#filtroMarca');
            selectMarca.find('option:not(:first)').remove();
            marcas.forEach(marca => {
                selectMarca.append(`<option value="${marca}">${marca}</option>`);
            });

            // Llenar filtro de años
            const anios = [...new Set(vehiculos.map(v => v.anio).filter(a => a && a !== 'N/A'))].sort().reverse();
            const selectAnio = $('#filtroAnio');
            selectAnio.find('option:not(:first)').remove();
            anios.forEach(anio => {
                selectAnio.append(`<option value="${anio}">${anio}</option>`);
            });
        }

        // Función para filtrar vehículos
        function filtrarVehiculos() {
            const busqueda = $('#buscadorVehiculos').val().toLowerCase();
            const marcaFiltro = $('#filtroMarca').val();
            const anioFiltro = $('#filtroAnio').val();

            vehiculosFiltrados = vehiculos.filter(vehiculo => {
                const matchBusqueda = !busqueda ||
                    (vehiculo.nombre && vehiculo.nombre.toLowerCase().includes(busqueda)) ||
                    (vehiculo.marca && vehiculo.marca.toLowerCase().includes(busqueda)) ||
                    (vehiculo.modelo && vehiculo.modelo.toLowerCase().includes(busqueda)) ||
                    (vehiculo.placa && vehiculo.placa.toLowerCase().includes(busqueda)) ||
                    (vehiculo.chasis && vehiculo.chasis.toLowerCase().includes(busqueda)) ||
                    (vehiculo.vin && vehiculo.vin.toLowerCase().includes(busqueda));

                const matchMarca = !marcaFiltro || vehiculo.marca === marcaFiltro;
                const matchAnio = !anioFiltro || vehiculo.anio === anioFiltro;

                return matchBusqueda && matchMarca && matchAnio;
            });

            paginaActual = 1;
            mostrarVehiculos();
        }

        // Función para mostrar vehículos
        function mostrarVehiculos() {
            const tbody = $('#tbody-vehiculos');
            tbody.empty();

            if (vehiculosFiltrados.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="12" class="text-center py-5">
                            <i class="fas fa-car fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No se encontraron vehículos</p>
                        </td>
                    </tr>
                `);
                $('#info-pagination').text('Mostrando 0 de 0 vehículos');
                $('#pagination-container').empty();
                return;
            }

            // Calcular índices de paginación
            const inicio = (paginaActual - 1) * itemsPorPagina;
            const fin = Math.min(inicio + itemsPorPagina, vehiculosFiltrados.length);
            const vehiculosPagina = vehiculosFiltrados.slice(inicio, fin);

            // Mostrar vehículos
            vehiculosPagina.forEach(vehiculo => {
                // Determinar símbolo de moneda (el backend envía "$" o "S/.")
                const simboloMoneda = (vehiculo.moneda === '$' || vehiculo.moneda === 'USD') ? '$' : 'S/';
                
                const fila = `
                    <tr>
                        <td>${vehiculo.idproductosv2}</td>
                        <td>${vehiculo.codigo || 'N/A'}</td>
                        <td><strong>${vehiculo.nombre}</strong></td>
                        <td>${vehiculo.marca || 'N/A'}</td>
                        <td>${vehiculo.modelo || 'N/A'}</td>
                        <td>${vehiculo.anio || 'N/A'}</td>
                        <td>${vehiculo.placa || 'En trámite'}</td>
                        <td>${vehiculo.color || 'N/A'}</td>
                        <td>${vehiculo.transmision || 'N/A'}</td>
                        <td><span class="badge bg-primary">${vehiculo.cantidad || 0}</span></td>
                        <td><strong>${simboloMoneda} ${parseFloat(vehiculo.precio_venta || 0).toFixed(2)}</strong></td>
                        <td>
                            <button class="btn btn-sm btn-info btn-action" onclick="verDetalles(${vehiculo.idproductosv2})" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning btn-action" onclick="editarVehiculo(${vehiculo.idproductosv2})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-action" onclick="eliminarVehiculo(${vehiculo.idproductosv2})" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(fila);
            });

            // Actualizar información de paginación
            $('#info-pagination').text(`Mostrando ${inicio + 1} a ${fin} de ${vehiculosFiltrados.length} vehículos`);

            // Generar paginación
            generarPaginacion();
        }

        // Función para generar paginación
        function generarPaginacion() {
            const totalPaginas = Math.ceil(vehiculosFiltrados.length / itemsPorPagina);
            const container = $('#pagination-container');
            container.empty();

            if (totalPaginas <= 1) return;

            // Botón anterior
            container.append(`
                <li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPagina(${paginaActual - 1}); return false;">Anterior</a>
                </li>
            `);

            // Páginas
            for (let i = 1; i <= totalPaginas; i++) {
                if (i === 1 || i === totalPaginas || (i >= paginaActual - 2 && i <= paginaActual + 2)) {
                    container.append(`
                        <li class="page-item ${i === paginaActual ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="cambiarPagina(${i}); return false;">${i}</a>
                        </li>
                    `);
                } else if (i === paginaActual - 3 || i === paginaActual + 3) {
                    container.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
                }
            }

            // Botón siguiente
            container.append(`
                <li class="page-item ${paginaActual === totalPaginas ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPagina(${paginaActual + 1}); return false;">Siguiente</a>
                </li>
            `);
        }

        // Función para cambiar página
        function cambiarPagina(pagina) {
            const totalPaginas = Math.ceil(vehiculosFiltrados.length / itemsPorPagina);
            if (pagina < 1 || pagina > totalPaginas) return;
            paginaActual = pagina;
            mostrarVehiculos();
        }

        // Función para ver detalles
        function verDetalles(id) {
            // Hacer AJAX para obtener datos frescos del producto
            $.ajax({
                url: '/arequipago/dataEditProducto',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        Swal.fire('Error', 'No se pudieron cargar los detalles', 'error');
                        return;
                    }

                    const producto = response.producto;
                    const caracteristicas = response.caracteristicas || [];

                    // Crear objeto vehiculo combinando producto y características
                    const vehiculo = {
                        codigo: producto.CODIGO,
                        nombre: producto.NOMBRE,
                        marca: producto.MARCA,
                        modelo: producto.MODELO,
                        cantidad: producto.CANTIDAD,
                        precio_venta: producto.PRECIO_VENTA,
                        descuento_cuota: producto.DESCUENTO_CUOTA,
                        moneda: producto.MONEDA
                    };

                    // Agregar características al objeto vehiculo
                    caracteristicas.forEach(function(car) {
                        const nombre = car.nombre_caracteristicas || '';
                        const valor = car.valor_caracteristica || '';
                        vehiculo[nombre.toLowerCase()] = valor;
                    });

                    // Determinar símbolo de moneda
                    const simboloMoneda = (vehiculo.moneda === '$' || vehiculo.moneda === 'USD') ? '$' : 'S/';

                    // Información Básica
                    $('#detalle-codigo').text(vehiculo.codigo || 'N/A');
                    $('#detalle-nombre').text(vehiculo.nombre || 'N/A');
                    $('#detalle-marca').text(vehiculo.marca || 'No especificada');
                    $('#detalle-modelo').text(vehiculo.modelo || 'No especificado');
                    $('#detalle-anio').text(vehiculo.anio || 'No especificado');
                    $('#detalle-stock').text(vehiculo.cantidad || 0);

                    // Especificaciones Técnicas
                    $('#detalle-vin').text(vehiculo.vin || 'No especificado');
                    $('#detalle-chasis').text(vehiculo.chasis || 'No especificado');
                    $('#detalle-placa').text(vehiculo.placa || 'En trámite');
                    $('#detalle-color').text(vehiculo.color || 'No especificado');
                    $('#detalle-transmision').text(vehiculo.transmision || 'No especificado');
                    $('#detalle-kilometraje').text(vehiculo.kilometraje ? vehiculo.kilometraje + ' km' : 'No especificado');
                    $('#detalle-gps').html(vehiculo.gps_activo === '1' ? '<span class="badge bg-success"><i class="fas fa-map-marker-alt me-1"></i>Activo</span>' : '<span class="badge bg-secondary"><i class="fas fa-map-marker-alt me-1"></i>Inactivo</span>');

                    // Información de Precios
                    $('#detalle-precio').text(simboloMoneda + ' ' + parseFloat(vehiculo.precio_venta || 0).toFixed(2));

                    // Mostrar descuento por cuota
                    if (vehiculo.descuento_cuota && vehiculo.descuento_cuota > 0) {
                        $('#detalle-descuento-cuota').text(simboloMoneda + ' ' + parseFloat(vehiculo.descuento_cuota).toFixed(2));
                    } else {
                        $('#detalle-descuento-cuota').text('Sin descuento');
                    }

                    // Mostrar moneda
                    const textoMoneda = (vehiculo.moneda === '$' || vehiculo.moneda === 'USD') ? 'Dólares (USD)' : 'Soles (PEN)';
                    $('#detalle-moneda').text(textoMoneda);

                    // Información Adicional (SOAT y Seguro)
                    if (vehiculo.fecha_venc_soat || vehiculo.fecha_venc_seguro) {
                        $('#detalle-info-adicional').show();
                        $('#detalle-soat').text(vehiculo.fecha_venc_soat || 'No especificado');
                        $('#detalle-seguro').text(vehiculo.fecha_venc_seguro || 'No especificado');
                    } else {
                        $('#detalle-info-adicional').hide();
                    }

                    $('#modalDetallesVehiculo').modal('show');
                },
                error: function() {
                    Swal.fire('Error', 'Error al cargar los detalles del vehículo', 'error');
                }
            });
        }

        // Función para abrir modal agregar vehículo
        function abrirModalAgregarVehiculo() {
            // Limpiar el formulario
            $('#formVehiculo')[0].reset();
            setFechaActualVehiculo();

            // Cargar la categoría vehículo (ya se selecciona automáticamente)
            cargarCategoriasVehiculo();

            // Establecer la oficina del modal según el filtro seleccionado
            var oficinaSeleccionada = $('#filtroOficinaVehiculo').val() || 1;
            $('#oficina_vehiculo').val(oficinaSeleccionada);

            // Abrir el modal de agregar producto
            $('#modal-add-prod-vehiculo').modal('show');
        }

        // Función para editar vehículo
        function editarVehiculo(id) {
            // Abrir el modal de edición de producto
            abrirModalEditarProducto(id);
        }

        // Función para eliminar vehículo
        function eliminarVehiculo(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede revertir",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Llamada AJAX para eliminar el vehículo
                    $.ajax({
                        url: '/arequipago/deleteProducts',
                        type: 'POST',
                        data: JSON.stringify({ ids: [id] }),
                        contentType: 'application/json',
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado',
                                    text: 'El vehículo ha sido eliminado correctamente',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                // Recargar la lista de vehículos
                                cargarVehiculos();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'No se pudo eliminar el vehículo'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error al eliminar vehículo:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al eliminar el vehículo. Intenta nuevamente.'
                            });
                        }
                    });
                }
            });
        }

        // Función para mostrar errores
        function mostrarError(mensaje) {
            $('#tbody-vehiculos').html(`
                <tr>
                    <td colspan="12" class="text-center py-5 text-danger">
                        <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                        <p>${mensaje}</p>
                    </td>
                </tr>
            `);
        }

        // Función para cargar categorías (solo Vehículo)
        function cargarCategoriasVehiculo() {
            $.ajax({
                url: '/arequipago/cargarcategoriaproductos',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (Array.isArray(response)) {
                        var select = $('#categoria_producto_vehiculo');
                        select.empty();

                        // Filtrar solo las categorías que contengan "vehiculo" o "vehículo"
                        var categoriaVehiculo = response.filter(function(categoria) {
                            var nombre = categoria.nombre.toLowerCase();
                            return nombre.includes('vehiculo') ||
                                   nombre.includes('vehículo') ||
                                   nombre.includes('moto lineal') ||
                                   nombre.includes('motokar') ||
                                   nombre.includes('trimovil') ||
                                   nombre.includes('cuatrimoto');
                        });

                        // Si encontramos la categoría vehículo, agregarla y seleccionarla automáticamente
                        if (categoriaVehiculo.length > 0) {
                            categoriaVehiculo.forEach(function(categoria) {
                                select.append($('<option>', {
                                    value: categoria.idcategoria_producto,
                                    text: categoria.nombre,
                                    selected: true // Seleccionar automáticamente
                                }));
                            });
                            // Mostrar los campos de vehículo automáticamente
                            mostrarIntfechaVehiculo();
                        } else {
                            select.append('<option value="">No hay categoría de vehículos</option>');
                        }
                    }
                },
                error: function() {
                    console.error('Error al cargar categorías');
                }
            });
        }

        // Establecer fecha actual
        function setFechaActualVehiculo() {
            const fechaInput = document.getElementById('fechaActual_vehiculo');
            if (fechaInput) {
                const hoy = new Date().toISOString().split('T')[0];
                fechaInput.value = hoy;
            }
        }

        // Función para mostrar campos de vehículo
        function mostrarIntfechaVehiculo() {
            const select = document.getElementById('categoria_producto_vehiculo');
            const vehiculoWrapper = document.getElementById('vehiculo_wrapper_modal');

            if (select && vehiculoWrapper) {
                const selectedText = select.options[select.selectedIndex].text.toLowerCase().trim();
                const vehiculoRegex = /veh[íi]cul[o]?[s]?|moto\s*lineal|motokar|trimovil|cuatrimoto/i;

                if (vehiculoRegex.test(selectedText)) {
                    vehiculoWrapper.style.display = 'block';
                } else {
                    vehiculoWrapper.style.display = 'none';
                }
            }
        }

        // Función para consultar RUC
        function consultarRUCVehiculo() {
            const rucInput = document.getElementById("rucInput_vehiculo").value;

            if (rucInput.length === 11) {
                // Mostrar loader con SweetAlert
                Swal.fire({
                    title: 'Consultando RUC...',
                    text: 'Por favor espere mientras verificamos la información',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: _URL + "/ajs/conductor/doc/cliente",
                    type: "POST",
                    data: { doc: rucInput },
                    dataType: 'json', // Asegurar que jQuery parsee como JSON
                    success: function(resp) {
                        // Cerrar el loader
                        Swal.close();
                        
                        console.log('Respuesta RUC (tipo):', typeof resp);
                        console.log('Respuesta RUC (contenido):', resp);
                        console.log('razonSocial existe?:', 'razonSocial' in resp);
                        console.log('razonSocial valor:', resp.razonSocial);
                        console.log('razonSocial tipo:', typeof resp.razonSocial);
                        
                        // Si la respuesta es string, intentar parsear
                        let parsedResp = resp;
                        if (typeof resp === 'string') {
                            try {
                                parsedResp = JSON.parse(resp);
                                console.log('Respuesta parseada:', parsedResp);
                            } catch (e) {
                                console.error('Error al parsear JSON:', e);
                                Swal.fire('Error', 'Error al procesar respuesta del servidor', 'error');
                                return;
                            }
                        }
                        
                        // Verificar si la respuesta tiene razonSocial y no está vacía
                        if (parsedResp && 
                            parsedResp.razonSocial && 
                            parsedResp.razonSocial.toString().trim() !== '' &&
                            parsedResp.razonSocial !== null &&
                            parsedResp.razonSocial !== undefined) {
                            
                            const razonSocial = parsedResp.razonSocial.toString().trim();
                            document.getElementById("razon_vehiculo").value = razonSocial;
                            console.log('RUC encontrado exitosamente:', razonSocial);
                            
                            // Mostrar mensaje de éxito
                            Swal.fire({
                                icon: 'success',
                                title: '¡RUC encontrado!',
                                text: `Razón Social: ${razonSocial}`,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            
                        } else {
                            // Mostrar más información sobre por qué falló
                            console.log('RUC no encontrado. Respuesta completa:', parsedResp);
                            console.log('Condiciones de validación:');
                            console.log('- parsedResp existe:', !!parsedResp);
                            console.log('- razonSocial existe:', !!parsedResp?.razonSocial);
                            console.log('- razonSocial no vacío:', parsedResp?.razonSocial?.toString().trim() !== '');
                            console.log('- razonSocial no null:', parsedResp?.razonSocial !== null);
                            console.log('- razonSocial no undefined:', parsedResp?.razonSocial !== undefined);
                            
                            Swal.fire('Advertencia', 'RUC no encontrado o sin razón social válida', 'warning');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Cerrar el loader en caso de error
                        Swal.close();
                        
                        console.error('Error al consultar RUC:', error);
                        console.error('Respuesta del servidor:', xhr.responseText);
                        Swal.fire('Error', 'Error al consultar RUC', 'error');
                    }
                });
            } else {
                Swal.fire('Advertencia', 'El RUC debe tener 11 dígitos', 'warning');
            }
        }

        // Función para guardar vehículo
        function guardarVehiculo() {
            const formData = new FormData();

            // Habilitar temporalmente el select para obtener su valor
            const categoriaSelect = $('#categoria_producto_vehiculo');
            categoriaSelect.prop('disabled', false);
            const categoriaValue = categoriaSelect.val();
            
            // Obtener el texto de la categoría (nombre) en lugar del value (ID)
            const categoriaText = categoriaSelect.find('option:selected').text();
            categoriaSelect.prop('disabled', true);

            // Datos básicos
            formData.append('nombre_producto', $('#nombre_producto_vehiculo').val());
            formData.append('codigo_producto', $('#codigo_producto_vehiculo').val());
            formData.append('tipo_producto', 'fisico');
            formData.append('cantidad_producto', $('#cantidad_producto_vehiculo').val());
            formData.append('categoria_producto', categoriaText);
            formData.append('oficina', $('#oficina_vehiculo').val()); // Agregar oficina

            // Datos de vehículo
            formData.append('marca_vehiculo', $('#marca_vehiculo_modal').val());
            formData.append('modelo_vehiculo', $('#modelo_vehiculo_modal').val());
            formData.append('vin', $('#vin_modal').val());
            formData.append('chasis', $('#chasis_modal').val());
            formData.append('color', $('#color_modal').val());
            formData.append('anio', $('#anio_modal').val());
            formData.append('placa_vehiculo', $('#placa_vehiculo_modal').val());
            formData.append('transmision_vehiculo', $('#transmision_vehiculo_modal').val());
            formData.append('kilometraje', $('#kilometraje_modal').val());
            formData.append('gps_activo', document.getElementById('gps_activo_modal').checked ? '1' : '0');
            formData.append('fecha_venc_soat', $('#fecha_venc_soat_modal').val());
            formData.append('fecha_venc_seguro', $('#fecha_venc_seguro_modal').val());

            // Proveedor
            formData.append('ruc', $('#rucInput_vehiculo').val());
            formData.append('razonsocial', $('#razon_vehiculo').val());

            // Precios
            formData.append('moneda', $('#moneda_vehiculo').val());
            formData.append('precio', $('#precio_vehiculo').val());
            formData.append('precio_venta', $('#precioVenta_vehiculo').val());
            formData.append('descuento_cuota', $('#descuentoCuota_vehiculo').val());

            // Adicionales
            formData.append('guia_remision', $('#guia_remision_vehiculo').val());
            formData.append('fecha_registro', $('#fechaActual_vehiculo').val());

            $.ajax({
                url: '/arequipago/guardarProducto',
                type: 'POST',
                data: Object.fromEntries(formData),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Vehículo guardado correctamente',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            $('#modal-add-prod-vehiculo').modal('hide');
                            cargarVehiculos(); // Recargar la lista
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Error al guardar el vehículo', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Error al guardar el vehículo', 'error');
                    console.error(xhr.responseText);
                }
            });
        }

        // ✅ NUEVA FUNCIÓN: Descargar reporte Excel de vehículos
        function downloadReportVehiculos() {
            // Recopilar filtros activos
            var params = new URLSearchParams();
            var oficina = $('#filtroOficinaVehiculo').val() || 1;
            params.set('oficina', oficina);
            
            // ✅ CRÍTICO: Filtrar solo productos con categoría "Vehículo"
            params.set('categorias', 'Vehículo');
            
            // Agregar filtros adicionales si están activos
            var marca = $('#filtroMarca').val();
            if (marca) {
                params.set('marca', marca);
            }
            
            var anio = $('#filtroAnio').val();
            if (anio) {
                params.set('anio', anio);
            }
            
            var busqueda = $('#buscadorVehiculos').val().trim();
            if (busqueda) {
                params.set('busqueda', busqueda);
            }

            fetch('/arequipago/downloadReport?' + params.toString(), {
                method: 'GET',
                headers: {
                    'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                },
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('No se pudo descargar el reporte.');
                }
                
                // Obtener el nombre del archivo desde el encabezado Content-Disposition
                const contentDisposition = response.headers.get('Content-Disposition');
                const fileName = contentDisposition ? contentDisposition.split('filename=')[1].replace(/"/g, '') : 'reporte_vehiculos.xlsx';

                return response.blob().then(blob => ({ fileName, blob }));
            })
            .then(({ fileName, blob }) => {
                const url = window.URL.createObjectURL(blob);
                
                // Crear enlace de descarga
                const a = document.createElement('a');
                a.href = url;
                a.download = fileName;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            })
            .catch(error => {
                console.error('Error al descargar el reporte:', error);
                Swal.fire('Error', 'No se pudo descargar el reporte de vehículos', 'error');
            });
        }
    </script>

    <!-- Incluir el componente modal de edición de producto -->
    <?php include __DIR__ . '/../../components/modal-editar-producto.php'; ?>
</body>
</html>
