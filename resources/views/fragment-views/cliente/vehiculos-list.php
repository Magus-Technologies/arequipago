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
                <button class="btn btn-light btn-lg" onclick="abrirModalAgregarVehiculo()">
                    <i class="fas fa-plus"></i> Agregar Vehículo
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row align-items-end">
                <div class="col-md-6">
                    <label class="form-label">Buscar vehículo</label>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text"
                               class="form-control"
                               id="buscadorVehiculos"
                               placeholder="Buscar por marca, modelo, placa, chasis, VIN...">
                    </div>
                </div>
                <div class="col-md-3">
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-car"></i> Detalles del Vehículo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Código:</label>
                            <p id="detalle-codigo"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Nombre:</label>
                            <p id="detalle-nombre"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Marca:</label>
                            <p id="detalle-marca"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Modelo:</label>
                            <p id="detalle-modelo"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">VIN:</label>
                            <p id="detalle-vin"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Chasis:</label>
                            <p id="detalle-chasis"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Placa:</label>
                            <p id="detalle-placa"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Color:</label>
                            <p id="detalle-color"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Año:</label>
                            <p id="detalle-anio"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Transmisión:</label>
                            <p id="detalle-transmision"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Stock:</label>
                            <p id="detalle-stock"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Precio Venta:</label>
                            <p id="detalle-precio"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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
                            <div class="col-md-6">
                                <label for="categoria_producto_vehiculo" class="form-label">Categoría <span class="text-danger">*</span></label>
                                <select id="categoria_producto_vehiculo" class="form-select" onchange="mostrarIntfechaVehiculo()" required disabled>
                                    <option value="">Cargando...</option>
                                </select>
                                <small class="form-text text-muted">La categoría está fija como Vehículo</small>
                            </div>
                            <div class="col-md-6">
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
                                        <option value="Automática">Automática</option>
                                    </select>
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
            $.ajax({
                url: '/arequipago/obtenerVehiculos',
                type: 'GET',
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
                        <td><strong>S/ ${parseFloat(vehiculo.precio_venta || 0).toFixed(2)}</strong></td>
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
            const vehiculo = vehiculos.find(v => v.idproductosv2 == id);
            if (!vehiculo) return;

            $('#detalle-codigo').text(vehiculo.codigo || 'N/A');
            $('#detalle-nombre').text(vehiculo.nombre || 'N/A');
            $('#detalle-marca').text(vehiculo.marca || 'No especificada');
            $('#detalle-modelo').text(vehiculo.modelo || 'No especificado');
            $('#detalle-vin').text(vehiculo.vin || 'No especificado');
            $('#detalle-chasis').text(vehiculo.chasis || 'No especificado');
            $('#detalle-placa').text(vehiculo.placa || 'En trámite');
            $('#detalle-color').text(vehiculo.color || 'No especificado');
            $('#detalle-anio').text(vehiculo.anio || 'No especificado');
            $('#detalle-transmision').text(vehiculo.transmision || 'No especificado');
            $('#detalle-stock').text(vehiculo.cantidad || 0);
            $('#detalle-precio').text('S/ ' + parseFloat(vehiculo.precio_venta || 0).toFixed(2));

            $('#modalDetallesVehiculo').modal('show');
        }

        // Función para abrir modal agregar vehículo
        function abrirModalAgregarVehiculo() {
            // Limpiar el formulario
            $('#formVehiculo')[0].reset();
            setFechaActualVehiculo();

            // Cargar la categoría vehículo (ya se selecciona automáticamente)
            cargarCategoriasVehiculo();

            // Abrir el modal de agregar producto
            $('#modal-add-prod-vehiculo').modal('show');
        }

        // Función para editar vehículo
        function editarVehiculo(id) {
            Swal.fire({
                icon: 'info',
                title: 'Editar Vehículo',
                text: 'Esta funcionalidad se integrará con el formulario de edición existente',
                confirmButtonText: 'Entendido'
            });
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
                    // Aquí iría la llamada AJAX para eliminar
                    Swal.fire(
                        'Eliminado',
                        'El vehículo ha sido eliminado',
                        'success'
                    );
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
                            return categoria.nombre.toLowerCase().includes('vehiculo') ||
                                   categoria.nombre.toLowerCase().includes('vehículo');
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
                const vehiculoRegex = /veh[íi]cul[o]?[s]?/i;

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
                $.ajax({
                    url: "/ajs/conductor/doc/cliente",
                    type: "POST",
                    data: { doc: rucInput },
                    success: function(resp) {
                        if (resp.razonSocial) {
                            document.getElementById("razon_vehiculo").value = resp.razonSocial;
                        } else {
                            Swal.fire('Advertencia', 'RUC no encontrado', 'warning');
                        }
                    },
                    error: function() {
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
            categoriaSelect.prop('disabled', true);

            // Datos básicos
            formData.append('nombre_producto', $('#nombre_producto_vehiculo').val());
            formData.append('codigo_producto', $('#codigo_producto_vehiculo').val());
            formData.append('tipo_producto', 'fisico');
            formData.append('cantidad_producto', $('#cantidad_producto_vehiculo').val());
            formData.append('categoria_producto', categoriaValue);

            // Datos de vehículo
            formData.append('marca_vehiculo', $('#marca_vehiculo_modal').val());
            formData.append('modelo_vehiculo', $('#modelo_vehiculo_modal').val());
            formData.append('vin', $('#vin_modal').val());
            formData.append('chasis', $('#chasis_modal').val());
            formData.append('color', $('#color_modal').val());
            formData.append('anio', $('#anio_modal').val());
            formData.append('placa_vehiculo', $('#placa_vehiculo_modal').val());
            formData.append('transmision_vehiculo', $('#transmision_vehiculo_modal').val());
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
    </script>
</body>
</html>
