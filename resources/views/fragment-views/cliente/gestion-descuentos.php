 <?php
// Verificar que solo el Director pueda acceder
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 3) {
    header('Location: ' . URL::to('/'));
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Descuentos por Cuota</title>
    <style>
        .header-section {
            background: #667eea;
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        .stats-card h4 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 5px 0;
        }

        .stats-card small {
            color: #6c757d;
            font-size: 0.8rem;
        }

        .stats-card i {
            font-size: 1.2rem;
        }

        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .table thead {
            background-color: #f8f9fa;
            color: #495057;
        }

        .table thead th {
            font-weight: 600;
            font-size: 0.9rem;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody tr:hover {
            background-color: #f8f9ff;
        }

        .btn-aplicar-masivo {
            background: #667eea;
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-aplicar-masivo:hover {
            background: #5568d3;
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
        }

        .descuento-input {
            width: 100px;
            text-align: center;
        }

        .badge-moneda {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <!-- Header -->
      <div class="clearfix mb-2">
            <h1 class="page-title text-center">Descuento Cuotas</h1>
            <ol class="breadcrumb m-0 float-start">
                <li class="breadcrumb-item"><a href="/" class="button-link" style="color:#626ed4">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Descuento Cuota</li>

            </ol>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <i class="fas fa-box text-primary mb-1"></i>
                    <h4 class="text-primary mb-0" id="stat-total-productos">0</h4>
                    <small class="text-muted">Productos con Descuento</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <i class="fas fa-coins text-warning mb-1"></i>
                    <h4 class="text-warning mb-0" id="stat-total-soles">S/ 0.00</h4>
                    <small class="text-muted">Total Subsidiado (Soles)</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <i class="fas fa-dollar-sign text-success mb-1"></i>
                    <h4 class="text-success mb-0" id="stat-total-dolares">$ 0.00</h4>
                    <small class="text-muted">Total Subsidiado (Dólares)</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <i class="fas fa-check-circle text-info mb-1"></i>
                    <h4 class="text-info mb-0" id="stat-seleccionados">0</h4>
                    <small class="text-muted">Productos Seleccionados</small>
                </div>
            </div>
        </div>

        <!-- Filtros Personalizados -->
        <div class="filter-section">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Categoría</label>
                    <select id="filtroCategoria" class="form-select">
                        <option value="">Todas las categorías</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Moneda</label>
                    <select id="filtroMoneda" class="form-select">
                        <option value="">Todas las monedas</option>
                        <option value="$">Dólares ($)</option>
                        <option value="S/.">Soles (S/.)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Con Descuento</label>
                    <select id="filtroDescuento" class="form-select">
                        <option value="">Todos los productos</option>
                        <option value="si">Con descuento</option>
                        <option value="no">Sin descuento</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-secondary w-100" onclick="limpiarFiltros()">
                        <i class="fas fa-undo me-2"></i>Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Acciones Masivas -->
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-outline-primary" onclick="seleccionarTodos()" id="btnSeleccionarTodos">
                    <i class="far fa-square me-2" id="iconSeleccionar"></i>Seleccionar Todos
                </button>
            </div>
            <div>
                <button class="btn btn-aplicar-masivo" onclick="abrirModalAplicarMasivo()" id="btnAplicarMasivo" disabled>
                    <i class="fas fa-magic me-2"></i>Aplicar Descuento a Seleccionados
                </button>
            </div>
        </div>

        <!-- Tabla de Productos con DataTables -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover table-striped" id="tablaProductos">
                    <thead style="background-color: #FCF34B">
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="checkTodos" onchange="toggleTodos(this)">
                            </th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio Venta</th>
                            <th>Moneda</th>
                            <th>Descuento Actual</th>
                            <th width="100">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables llenará esto automáticamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Aplicar Descuento Masivo -->
    <div class="modal fade" id="modalAplicarMasivo" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-magic me-2"></i>Aplicar Descuento Masivo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Se aplicará el descuento a <strong id="cantidadSeleccionados">0</strong> productos seleccionados
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descuento por Cuota</label>
                        <input type="number" class="form-control" id="descuentoMasivo" step="0.01" min="0" placeholder="Ej: 0.50 para soles, 0.20 para dólares">
                        <small class="text-muted">
                            <i class="fas fa-lightbulb me-1"></i>
                            Recomendado: S/ 0.50 para soles, $ 0.20 para dólares
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="aplicarDescuentoMasivo()">
                        <i class="fas fa-save me-2"></i>Aplicar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Individual -->
    <div class="modal fade" id="modalEditarIndividual" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Descuento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editarIdProducto">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Producto</label>
                        <p id="editarNombreProducto" class="form-control-plaintext"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Moneda</label>
                        <p id="editarMonedaProducto" class="form-control-plaintext"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descuento por Cuota</label>
                        <input type="number" class="form-control" id="editarDescuento" step="0.01" min="0">
                        <small class="text-muted">
                            <i class="fas fa-lightbulb me-1"></i>
                            Recomendado: S/ 0.50 para soles, $ 0.20 para dólares
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarDescuentoIndividual()">
                        <i class="fas fa-save me-2"></i>Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Usar var en lugar de let para evitar errores de redeclaración en navegación SPA
        var productos = [];
        var tablaProductos;

        $(document).ready(function() {
            // Limpiar filtros personalizados previos de DataTables
            if ($.fn.dataTable.ext.search.length > 0) {
                $.fn.dataTable.ext.search.pop();
            }
            
            cargarProductos();
        });

        // Cargar productos
        function cargarProductos() {
            $.ajax({
                url: _URL + '/ajs/obtenerProductosConDescuento',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        productos = response.productos;
                        actualizarEstadisticas();
                        llenarFiltros();
                        inicializarDataTable();
                    } else {
                        Swal.fire('Error', 'No se pudieron cargar los productos', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al cargar productos', 'error');
                }
            });
        }

        // Inicializar DataTable
        function inicializarDataTable() {
            if ($.fn.DataTable.isDataTable('#tablaProductos')) {
                $('#tablaProductos').DataTable().destroy();
            }

            tablaProductos = $('#tablaProductos').DataTable({
                data: productos,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                pageLength: 25,
                order: [[2, 'asc']], // Ordenar por nombre
                columnDefs: [
                    { orderable: false, targets: [0, 7] } // Checkbox y Acciones no ordenables
                ],
                columns: [
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="producto-checkbox" value="${row.idproductosv2}" onchange="actualizarSeleccionados()">`;
                        }
                    },
                    {
                        data: 'codigo',
                        render: function(data) {
                            return data || 'N/A';
                        }
                    },
                    {
                        data: 'nombre',
                        render: function(data) {
                            return '<strong>' + data + '</strong>';
                        }
                    },
                    { data: 'categoria' },
                    {
                        data: null,
                        render: function(data, type, row) {
                            const simboloMoneda = (row.moneda === '$' || row.moneda === 'USD') ? '$' : 'S/';
                            return simboloMoneda + ' ' + parseFloat(row.precio_venta).toFixed(2);
                        }
                    },
                    {
                        data: 'moneda',
                        render: function(data) {
                            const esDolar = (data === '$' || data === 'USD');
                            return esDolar 
                                ? '<span class="badge badge-moneda bg-success">Dólares</span>'
                                : '<span class="badge badge-moneda bg-warning">Soles</span>';
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (row.descuento_cuota && parseFloat(row.descuento_cuota) > 0) {
                                const simboloMoneda = (row.moneda === '$' || row.moneda === 'USD') ? '$' : 'S/';
                                return simboloMoneda + ' ' + parseFloat(row.descuento_cuota).toFixed(2);
                            }
                            return '<span class="text-muted">Sin descuento</span>';
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `<button class="btn btn-sm btn-primary" onclick="abrirModalEditar(${row.idproductosv2})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>`;
                        }
                    }
                ],
                drawCallback: function() {
                    // Restaurar checkboxes seleccionados después de redibujar
                    actualizarSeleccionados();
                }
            });

            // Aplicar filtros personalizados
            aplicarFiltrosDataTable();
        }

        // Actualizar estadísticas
        function actualizarEstadisticas() {
            let totalSoles = 0;
            let totalDolares = 0;
            let totalProductos = 0;

            productos.forEach(p => {
                if (p.descuento_cuota && parseFloat(p.descuento_cuota) > 0) {
                    totalProductos++;
                    if (p.moneda === '$' || p.moneda === 'USD') {
                        totalDolares += parseFloat(p.descuento_cuota);
                    } else {
                        totalSoles += parseFloat(p.descuento_cuota);
                    }
                }
            });

            $('#stat-total-productos').text(totalProductos);
            $('#stat-total-soles').text('S/ ' + totalSoles.toFixed(2));
            $('#stat-total-dolares').text('$ ' + totalDolares.toFixed(2));
        }

        // Llenar filtros
        function llenarFiltros() {
            const categorias = [...new Set(productos.map(p => p.categoria))].sort();
            const selectCategoria = $('#filtroCategoria');
            categorias.forEach(cat => {
                selectCategoria.append(`<option value="${cat}">${cat}</option>`);
            });
        }

        // Aplicar filtros personalizados a DataTable
        function aplicarFiltrosDataTable() {
            $('#filtroCategoria, #filtroMoneda, #filtroDescuento').on('change', function() {
                tablaProductos.draw();
            });

            // Filtro personalizado de DataTables
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const categoria = $('#filtroCategoria').val();
                const moneda = $('#filtroMoneda').val();
                const descuento = $('#filtroDescuento').val();
                
                const producto = productos[dataIndex];
                
                // Filtro por categoría
                if (categoria && producto.categoria !== categoria) {
                    return false;
                }
                
                // Filtro por moneda
                if (moneda && producto.moneda !== moneda) {
                    return false;
                }
                
                // Filtro por descuento
                if (descuento === 'si') {
                    if (!producto.descuento_cuota || parseFloat(producto.descuento_cuota) === 0) {
                        return false;
                    }
                } else if (descuento === 'no') {
                    if (producto.descuento_cuota && parseFloat(producto.descuento_cuota) > 0) {
                        return false;
                    }
                }
                
                return true;
            });
        }

        // Limpiar filtros
        function limpiarFiltros() {
            $('#filtroCategoria').val('');
            $('#filtroMoneda').val('');
            $('#filtroDescuento').val('');
            tablaProductos.draw();
        }

        // Seleccionar/Deseleccionar todos
        function toggleTodos(checkbox) {
            $('.producto-checkbox').prop('checked', checkbox.checked);
            actualizarSeleccionados();
            actualizarIconoBotonSeleccionar();
        }

        function seleccionarTodos() {
            const todosSeleccionados = $('.producto-checkbox:checked').length === $('.producto-checkbox').length;
            
            if (todosSeleccionados) {
                // Si todos están seleccionados, deseleccionar
                $('.producto-checkbox').prop('checked', false);
                $('#checkTodos').prop('checked', false);
            } else {
                // Si no todos están seleccionados, seleccionar todos
                $('.producto-checkbox').prop('checked', true);
                $('#checkTodos').prop('checked', true);
            }
            
            actualizarSeleccionados();
            actualizarIconoBotonSeleccionar();
        }



        // Actualizar contador de seleccionados
        function actualizarSeleccionados() {
            const seleccionados = $('.producto-checkbox:checked').length;
            $('#stat-seleccionados').text(seleccionados);
            $('#btnAplicarMasivo').prop('disabled', seleccionados === 0);
            actualizarIconoBotonSeleccionar();
        }

        // Actualizar icono del botón Seleccionar Todos
        function actualizarIconoBotonSeleccionar() {
            const totalCheckboxes = $('.producto-checkbox').length;
            const seleccionados = $('.producto-checkbox:checked').length;
            const iconoSeleccionar = $('#iconSeleccionar');
            
            if (seleccionados === totalCheckboxes && totalCheckboxes > 0) {
                // Todos seleccionados - mostrar check
                iconoSeleccionar.removeClass('far fa-square').addClass('fas fa-check-square');
            } else {
                // No todos seleccionados - mostrar cuadrado vacío
                iconoSeleccionar.removeClass('fas fa-check-square').addClass('far fa-square');
            }
        }

        // Abrir modal aplicar masivo
        function abrirModalAplicarMasivo() {
            const seleccionados = $('.producto-checkbox:checked').length;
            $('#cantidadSeleccionados').text(seleccionados);
            $('#descuentoMasivo').val('');
            $('#modalAplicarMasivo').modal('show');
        }

        // Aplicar descuento masivo
        function aplicarDescuentoMasivo() {
            const descuento = $('#descuentoMasivo').val();
            
            if (!descuento || parseFloat(descuento) < 0) {
                Swal.fire('Error', 'Ingrese un descuento válido', 'error');
                return;
            }

            const ids = $('.producto-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            Swal.fire({
                title: 'Procesando...',
                text: 'Aplicando descuento a productos seleccionados',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: _URL + '/ajs/aplicarDescuentoMasivo',
                type: 'POST',
                data: {
                    ids: ids,
                    descuento: descuento
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Éxito', response.message, 'success');
                        $('#modalAplicarMasivo').modal('hide');
                        cargarProductos();
                        $('.producto-checkbox').prop('checked', false);
                        $('#checkTodos').prop('checked', false);
                        actualizarSeleccionados();
                        actualizarIconoBotonSeleccionar();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo aplicar el descuento', 'error');
                }
            });
        }

        // Abrir modal editar individual
        function abrirModalEditar(id) {
            const producto = productos.find(p => p.idproductosv2 == id);
            if (!producto) return;

            const simboloMoneda = (producto.moneda === '$' || producto.moneda === 'USD') ? 'Dólares ($)' : 'Soles (S/.)';
            
            $('#editarIdProducto').val(id);
            $('#editarNombreProducto').text(producto.nombre);
            $('#editarMonedaProducto').text(simboloMoneda);
            $('#editarDescuento').val(producto.descuento_cuota || '0.00');
            $('#modalEditarIndividual').modal('show');
        }

        // Guardar descuento individual
        function guardarDescuentoIndividual() {
            const id = $('#editarIdProducto').val();
            const descuento = $('#editarDescuento').val();

            if (!descuento || parseFloat(descuento) < 0) {
                Swal.fire('Error', 'Ingrese un descuento válido', 'error');
                return;
            }

            Swal.fire({
                title: 'Guardando...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: _URL + '/ajs/actualizarDescuentoProducto',
                type: 'POST',
                data: {
                    id: id,
                    descuento: descuento
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Éxito', 'Descuento actualizado correctamente', 'success');
                        $('#modalEditarIndividual').modal('hide');
                        cargarProductos();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo actualizar el descuento', 'error');
                }
            });
        }
    </script>
</body>
</html>
