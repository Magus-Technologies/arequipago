<!-- Modal Editar Producto - Componente Reutilizable -->
<div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-labelledby="modalEditarProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#626ed4; color: white;">
                <h5 class="modal-title" id="modalEditarProductoLabel">
                    <i class="fas fa-edit"></i> Editar Producto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="loadingEditProducto" class="text-center py-5" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Cargando datos del producto...</p>
                </div>

                <form id="formEditarProductoModal">
                    <input type="hidden" id="edit_id_producto" name="ID_PRODUCTO">
                    
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs mb-3" id="editProductoTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-general" data-bs-toggle="tab" data-bs-target="#content-general" type="button" role="tab">
                                <i class="fas fa-info-circle"></i> INF. GENERAL
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-comercial" data-bs-toggle="tab" data-bs-target="#content-comercial" type="button" role="tab">
                                <i class="fas fa-dollar-sign"></i> INF. COMERCIAL
                            </button>
                        </li>
                        <li class="nav-item" role="presentation" id="tab-vehiculo-nav" style="display: none;">
                            <button class="nav-link" id="tab-vehiculo" data-bs-toggle="tab" data-bs-target="#content-vehiculo" type="button" role="tab">
                                <i class="fas fa-car"></i> DETALLES VEHÍCULO
                            </button>
                        </li>
                        <li class="nav-item" role="presentation" id="tab-caracteristicas-nav" style="display: none;">
                            <button class="nav-link" id="tab-caracteristicas" data-bs-toggle="tab" data-bs-target="#content-caracteristicas" type="button" role="tab">
                                <i class="fas fa-cogs"></i> CARACTERÍSTICAS
                            </button>
                        </li>
                    </ul>

                    <!-- Tabs Content -->
                    <div class="tab-content" id="editProductoTabsContent">
                        <!-- Tab 1: Información General -->
                        <div class="tab-pane fade show active" id="content-general" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-12">
                                    <!-- Nombre -->
                                    <div class="mb-3">
                                        <label for="edit_nombre" class="form-label">
                                            <i class="fas fa-tag text-primary"></i> Nombre del Producto
                                        </label>
                                        <input type="text" class="form-control" id="edit_nombre" name="NOMBRE" required>
                                    </div>

                                    <!-- Marca y Modelo -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_marca" class="form-label">
                                                <i class="fas fa-copyright text-primary"></i> Marca
                                            </label>
                                            <input type="text" class="form-control" id="edit_marca" name="MARCA">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_modelo" class="form-label">
                                                <i class="fas fa-cube text-primary"></i> Modelo
                                            </label>
                                            <input type="text" class="form-control" id="edit_modelo" name="MODELO">
                                        </div>
                                    </div>

                                    <!-- Código y Cantidad -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_codigo" class="form-label">
                                                <i class="fas fa-barcode text-primary"></i> Código
                                            </label>
                                            <input type="text" class="form-control" id="edit_codigo" name="CODIGO">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_cantidad" class="form-label">
                                                <i class="fas fa-boxes text-primary"></i> Cantidad
                                            </label>
                                            <input type="number" class="form-control" id="edit_cantidad" name="CANTIDAD" required>
                                        </div>
                                    </div>

                                    <!-- Categoría y Tipo -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_categoria" class="form-label">
                                                <i class="fas fa-tags text-primary"></i> Categoría
                                            </label>
                                            <select class="form-select" id="edit_categoria" name="CATEGORIA" required>
                                                <option value="">Seleccionar...</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_tipo_producto" class="form-label">
                                                <i class="fas fa-cube text-primary"></i> Tipo de Producto
                                            </label>
                                            <select class="form-select" id="edit_tipo_producto" name="TIPO_PRODUCTO" required>
                                                <option value="">Seleccionar...</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Oficina -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_oficina" class="form-label">
                                                <i class="fas fa-building text-primary"></i> Oficina
                                            </label>
                                            <select class="form-select" id="edit_oficina" name="OFICINA" required>
                                                <option value="1">Oficina 1</option>
                                                <option value="2">Oficina 2</option>
                                                <option value="3">Oficina Lima</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Campos de volumen (ocultos por defecto) -->
                                    <div id="edit_campos_volumen" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="edit_cantidad_unidad" class="form-label">
                                                    <i class="fas fa-weight text-primary"></i> Cantidad por Unidad
                                                </label>
                                                <input type="number" step="0.01" class="form-control" id="edit_cantidad_unidad" name="CANTIDAD_UNIDAD">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="edit_unidad_medida" class="form-label">
                                                    <i class="fas fa-balance-scale text-primary"></i> Unidad de Medida
                                                </label>
                                                <select class="form-select" id="edit_unidad_medida" name="UNIDAD_MEDIDA">
                                                    <option value="">Seleccionar...</option>
                                                    <option value="Litros">Litros</option>
                                                    <option value="Galones (3.785 litros)">Galones (3.785 litros)</option>
                                                    <option value="Kilogramos">Kilogramos</option>
                                                    <option value="OZ">OZ</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Información Comercial -->
                        <div class="tab-pane fade" id="content-comercial" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-12">
                                    <!-- RUC y Razón Social -->
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="edit_ruc" class="form-label">
                                                <i class="fas fa-id-card text-primary"></i> RUC
                                            </label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="edit_ruc" name="RUC" maxlength="11">
                                                <button type="button" class="btn btn-info" onclick="consultarRUCEdit()">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-8 mb-3">
                                            <label for="edit_razon_social" class="form-label">
                                                <i class="fas fa-building text-primary"></i> Razón Social
                                            </label>
                                            <input type="text" class="form-control" id="edit_razon_social" name="RAZON_SOCIAL">
                                        </div>
                                    </div>

                                    <!-- Precios -->
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="edit_moneda" class="form-label">
                                                <i class="fas fa-coins text-primary"></i> Moneda
                                            </label>
                                            <select class="form-select" id="edit_moneda" name="MONEDA">
                                                <option value="S/.">S/.</option>
                                                <option value="$">$</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="edit_precio" class="form-label">
                                                <i class="fas fa-dollar-sign text-primary"></i> Precio Compra
                                            </label>
                                            <input type="number" step="0.01" class="form-control" id="edit_precio" name="PRECIO">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="edit_precio_venta" class="form-label">
                                                <i class="fas fa-money-bill-wave text-primary"></i> Precio Venta
                                            </label>
                                            <input type="number" step="0.01" class="form-control" id="edit_precio_venta" name="PRECIO_VENTA">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_descuento_cuota" class="form-label">
                                            <i class="fas fa-percentage text-primary"></i> Descuento por Cuota
                                        </label>
                                        <input type="number" step="0.01" class="form-control" id="edit_descuento_cuota" name="DESCUENTO_CUOTA" placeholder="0.00">
                                    </div>

                                    <!-- Fechas -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_fecha_registro" class="form-label">
                                                <i class="fas fa-calendar text-primary"></i> Fecha Registro
                                            </label>
                                            <input type="date" class="form-control" id="edit_fecha_registro" name="FECHA_REGISTRO">
                                        </div>
                                        <div class="col-md-6 mb-3" id="edit_campo_fecha_vencimiento" style="display: none;">
                                            <label for="edit_fecha_vencimiento" class="form-label">
                                                <i class="fas fa-calendar-times text-danger"></i> Fecha Vencimiento
                                            </label>
                                            <input type="date" class="form-control" id="edit_fecha_vencimiento" name="FECHA_VENCIMIENTO">
                                        </div>
                                    </div>

                                    <!-- Guía de Remisión -->
                                    <div class="mb-3">
                                        <label for="edit_guia_remision" class="form-label">
                                            <i class="fas fa-file-alt text-primary"></i> Guía de Remisión
                                        </label>
                                        <input type="text" class="form-control" id="edit_guia_remision" name="GUIA_REMISION">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Detalles del Vehículo -->
                        <div class="tab-pane fade" id="content-vehiculo" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_vin" class="form-label">
                                        <i class="fas fa-fingerprint text-primary"></i> VIN
                                    </label>
                                    <input type="text" class="form-control" id="edit_vin" name="vin" placeholder="Número de identificación vehicular">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_chasis" class="form-label">
                                        <i class="fas fa-cog text-primary"></i> Nº de Motor/Chasis
                                    </label>
                                    <input type="text" class="form-control" id="edit_chasis" name="chasis" placeholder="Número de motor">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_placa" class="form-label">
                                        <i class="fas fa-id-card text-primary"></i> Placa
                                    </label>
                                    <input type="text" class="form-control" id="edit_placa" name="placa" placeholder="Ej: ABC-123">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_color" class="form-label">
                                        <i class="fas fa-palette text-primary"></i> Color
                                    </label>
                                    <input type="text" class="form-control" id="edit_color" name="color" placeholder="Ej: Blanco, Negro">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_anio" class="form-label">
                                        <i class="fas fa-calendar text-primary"></i> Año
                                    </label>
                                    <input type="number" class="form-control" id="edit_anio" name="anio" min="1900" max="2099" placeholder="Ej: 2024">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_transmision" class="form-label">
                                        <i class="fas fa-cogs text-primary"></i> Transmisión
                                    </label>
                                    <select class="form-select" id="edit_transmision" name="transmision">
                                        <option value="">Seleccionar</option>
                                        <option value="Manual">Manual</option>
                                        <option value="Automático">Automático</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_fecha_venc_soat" class="form-label">
                                        <i class="fas fa-calendar-check text-primary"></i> Fecha Vencimiento SOAT
                                    </label>
                                    <input type="date" class="form-control" id="edit_fecha_venc_soat" name="fecha_venc_soat">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_fecha_venc_seguro" class="form-label">
                                        <i class="fas fa-calendar-check text-primary"></i> Fecha Vencimiento Seguro
                                    </label>
                                    <input type="date" class="form-control" id="edit_fecha_venc_seguro" name="fecha_venc_seguro">
                                </div>
                            </div>
                        </div>
                            </div>
                        </div>

                        <!-- Tab 4: Otras Características -->
                        <div class="tab-pane fade" id="content-caracteristicas" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div id="edit_caracteristicas_container">
                                        <p class="text-muted text-center">
                                            <i class="fas fa-info-circle"></i> Seleccione una categoría para ver las características específicas
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarCambiosProducto()">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #modalEditarProducto .nav-tabs {
        border-bottom: 2px solid #dee2e6;
        background: #f8f9fa;
        padding: 10px 10px 0;
        border-radius: 8px 8px 0 0;
    }
    
    #modalEditarProducto .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 600;
        padding: 12px 20px;
        margin: 0 5px;
        border-radius: 8px 8px 0 0;
        transition: all 0.3s ease;
    }
    
    #modalEditarProducto .nav-tabs .nav-link:hover {
        background: #e9ecef;
        color: #495057;
    }
    
    #modalEditarProducto .nav-tabs .nav-link.active {
        background: white;
        color: #02a398;
        border-bottom: 3px solid #02a398;
    }
    
    #modalEditarProducto .nav-tabs .nav-link i {
        margin-right: 5px;
    }
    
    #modalEditarProducto .tab-content {
        padding: 20px;
        background: white;
        border-radius: 0 0 8px 8px;
        min-height: 400px;
    }
    
    #modalEditarProducto .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }
    
    #modalEditarProducto .form-control,
    #modalEditarProducto .form-select {
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 10px 12px;
        transition: all 0.3s ease;
    }
    
    #modalEditarProducto .form-control:focus,
    #modalEditarProducto .form-select:focus {
        border-color: #02a398;
        box-shadow: 0 0 0 0.2rem rgba(2, 163, 152, 0.15);
    }
    
    #modalEditarProducto .input-group .btn {
        border-color: #ced4da;
    }
    
    #modalEditarProducto .input-group .btn:hover {
        background: #02a398;
        border-color: #02a398;
        color: white;
    }
</style>

<script>
// Variables globales para el modal de edición
let productDataEdit = {};
let tiposProductoDataEdit = [];
let categoriasDataEdit = [];
let categoriaOriginalEdit = '';
let tipoProductoOriginalEdit = '';

// Función para abrir el modal de edición
function abrirModalEditarProducto(idProducto) {
    $('#modalEditarProducto').modal('show');
    $('#loadingEditProducto').show();
    $('#formEditarProductoModal').hide();
    
    // Cargar datos del producto y catálogos
    Promise.all([
        cargarDatosProductoEdit(idProducto),
        cargarTiposYCategoriasEdit()
    ]).then(() => {
        $('#loadingEditProducto').hide();
        $('#formEditarProductoModal').show();
        
        // Evaluar tipo de producto y categoría
        setTimeout(() => {
            evaluarTipoProductoEdit();
            evaluarCategoriaEdit();
        }, 300);
    }).catch(error => {
        console.error('Error cargando datos:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar los datos del producto'
        });
        $('#modalEditarProducto').modal('hide');
    });
}

// Cargar datos del producto
function cargarDatosProductoEdit(idProducto) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "/arequipago/dataEditProducto",
            type: "POST",
            data: { id: idProducto },
            dataType: "json",
            success: function(response) {
                if (response.error) {
                    reject(response.error);
                    return;
                }
                
                productDataEdit = response.producto;
                categoriaOriginalEdit = productDataEdit.CATEGORIA;
                tipoProductoOriginalEdit = productDataEdit.TIPO_PRODUCTO;
                
                // Llenar formulario
                $('#edit_id_producto').val(productDataEdit.ID_PRODUCTO);
                $('#edit_nombre').val(productDataEdit.NOMBRE || '');
                $('#edit_marca').val(productDataEdit.MARCA || '');
                $('#edit_modelo').val(productDataEdit.MODELO || '');
                $('#edit_codigo').val(productDataEdit.CODIGO || '');
                $('#edit_cantidad').val(productDataEdit.CANTIDAD || '');
                $('#edit_cantidad_unidad').val(productDataEdit.CANTIDAD_UNIDAD || '');
                $('#edit_unidad_medida').val(productDataEdit.UNIDAD_MEDIDA || '');
                $('#edit_ruc').val(productDataEdit.RUC || '');
                $('#edit_razon_social').val(productDataEdit.RAZON_SOCIAL || '');
                $('#edit_precio').val(productDataEdit.PRECIO || '');
                $('#edit_precio_venta').val(productDataEdit.PRECIO_VENTA || '');
                $('#edit_descuento_cuota').val(productDataEdit.DESCUENTO_CUOTA || '');
                $('#edit_moneda').val(productDataEdit.MONEDA || 'S/.');
                $('#edit_fecha_registro').val(productDataEdit.FECHA_REGISTRO || '');
                $('#edit_fecha_vencimiento').val(productDataEdit.FECHA_VENCIMIENTO || '');
                $('#edit_guia_remision').val(productDataEdit.GUIA_REMISION || '');
                $('#edit_oficina').val(productDataEdit.OFICINA || '1');

                // Renderizar características
                if (response.caracteristicas) {
                    renderizarCaracteristicasEdit(response.caracteristicas);
                }
                
                resolve(response);
            },
            error: function(xhr, status, error) {
                reject(error);
            }
        });
    });
}

// Cargar tipos y categorías
function cargarTiposYCategoriasEdit() {
    return new Promise((resolve, reject) => {
        Promise.all([
            $.ajax({ url: "/arequipago/cargartiposproducto", method: "GET", dataType: "json" }),
            $.ajax({ url: "/arequipago/cargarcategoriaproductos", method: "GET", dataType: "json" })
        ]).then(([tiposResponse, categoriasResponse]) => {
            tiposProductoDataEdit = Array.isArray(tiposResponse) ? tiposResponse : [];
            categoriasDataEdit = Array.isArray(categoriasResponse) ? categoriasResponse : [];
            
            actualizarSelectoresEdit();
            resolve({ tiposProducto: tiposProductoDataEdit, categorias: categoriasDataEdit });
        }).catch(error => {
            reject(error);
        });
    });
}

// Actualizar selectores
function actualizarSelectoresEdit() {
    // Categorías
    const categoriaSelect = $('#edit_categoria');
    categoriaSelect.empty();
    categoriaSelect.append('<option value="">Seleccionar...</option>');
    
    categoriasDataEdit.forEach(categoria => {
        const selected = (categoriaOriginalEdit === categoria.nombre || 
                         parseInt(categoriaOriginalEdit) === categoria.idcategoria_producto) ? 'selected' : '';
        categoriaSelect.append(`<option value="${categoria.idcategoria_producto}" ${selected}>${categoria.nombre}</option>`);
    });
    
    // Tipos de producto
    const tipoSelect = $('#edit_tipo_producto');
    tipoSelect.empty();
    
    tiposProductoDataEdit.forEach(tipo => {
        const selected = (tipoProductoOriginalEdit === tipo.tipo_productocol || 
                         parseInt(tipoProductoOriginalEdit) === tipo.idtipo_producto) ? 'selected' : '';
        tipoSelect.append(`<option value="${tipo.idtipo_producto}" data-tipo-venta="${tipo.tipo_venta}" ${selected}>${tipo.tipo_productocol}</option>`);
    });
}

// Evaluar tipo de producto
function evaluarTipoProductoEdit() {
    const select = $('#edit_tipo_producto');
    const option = select.find(':selected');
    const tipoVenta = option.data('tipo-venta');
    
    if (tipoVenta === 'volumen') {
        $('#edit_campos_volumen').show();
    } else {
        $('#edit_campos_volumen').hide();
    }
}

// Evaluar categoría
function evaluarCategoriaEdit() {
    const categoriaSelect = $('#edit_categoria');
    const categoriaSeleccionada = categoriaSelect.find(':selected').text().trim();
    const categoriaNorm = normalizarTextoEdit(categoriaSeleccionada);
    
    // Mostrar/ocultar fecha de vencimiento
    if (categoriaNorm.includes('soat') || categoriaNorm.includes('seguro')) {
        $('#edit_campo_fecha_vencimiento').show();
    } else {
        $('#edit_campo_fecha_vencimiento').hide();
    }
    
    // Mostrar/ocultar tabs según categoría
    if (categoriaNorm.includes('vehiculo')) {
        $('#tab-vehiculo-nav').show();
        $('#tab-caracteristicas-nav').hide();
    } else if (categoriaNorm.includes('celular') || categoriaNorm.includes('llanta') || categoriaNorm.includes('chip')) {
        $('#tab-vehiculo-nav').hide();
        $('#tab-caracteristicas-nav').show();
    } else {
        $('#tab-vehiculo-nav').hide();
        $('#tab-caracteristicas-nav').hide();
    }
}

// Renderizar características
function renderizarCaracteristicasEdit(caracteristicas) {
    const categoriaNorm = normalizarTextoEdit(categoriaOriginalEdit);
    const esVehiculo = categoriaNorm.includes('vehiculo');
    
    // Si es vehículo, llenar los campos específicos de vehículo y mostrar tab
    if (esVehiculo) {
        $('#tab-vehiculo-nav').show();
        $('#tab-caracteristicas-nav').hide();
        
        // Limpiar campos de vehículo primero
        $('#edit_vin, #edit_chasis, #edit_placa, #edit_color, #edit_anio, #edit_transmision, #edit_fecha_venc_soat, #edit_fecha_venc_seguro').val('');
        
        // Llenar campos de vehículo desde las características
        if (Array.isArray(caracteristicas) && caracteristicas.length > 0) {
            caracteristicas.forEach((car) => {
                const nombre = car.nombre_caracteristicas || '';
                const valor = car.valor_caracteristica || '';

                // Mapear características a campos (usar nombres exactos de BD)
                switch(nombre.toLowerCase()) {
                    case 'vin':
                        $('#edit_vin').val(valor);
                        break;
                    case 'chasis':
                    case 'numero_motor':
                    case 'nº de motor':
                        $('#edit_chasis').val(valor);
                        break;
                    case 'placa':
                        $('#edit_placa').val(valor);
                        break;
                    case 'color':
                        $('#edit_color').val(valor);
                        break;
                    case 'anio':
                    case 'año':
                    case 'año del vehiculo':
                        $('#edit_anio').val(valor);
                        break;
                    case 'transmision':
                    case 'transmisión':
                        $('#edit_transmision').val(valor);
                        break;
                    case 'fecha_venc_soat':
                    case 'fecha de vencimiento':
                    case 'soat':
                        $('#edit_fecha_venc_soat').val(valor);
                        break;
                    case 'fecha_venc_seguro':
                    case 'fechas de vencimiento del seguro':
                    case 'fecha vencimiento seguro':
                        $('#edit_fecha_venc_seguro').val(valor);
                        break;
                }
            });
        }
        
        // NOTA: Marca y modelo ya se llenaron en cargarDatosProductoEdit, no es necesario llenarlos aquí
    } else {
        // Para otras categorías, usar el contenedor de características genéricas
        $('#tab-vehiculo-nav').hide();
        
        const container = $('#edit_caracteristicas_container');
        let html = '';
        
        if (Array.isArray(caracteristicas) && caracteristicas.length > 0) {
            $('#tab-caracteristicas-nav').show();
            
            caracteristicas.forEach((car, index) => {
                const nombre = car.nombre_caracteristicas || `Característica ${index + 1}`;
                const valor = car.valor_caracteristica || '';
                const campo = car.campo || `caracteristica_${index + 1}`;
                
                // Detectar tipo de campo
                const nombreNorm = normalizarTextoEdit(nombre);
                let tipo = 'text';
                if (nombreNorm.includes('fecha')) {
                    tipo = 'date';
                } else if (nombreNorm.includes('anio') || nombreNorm.includes('año')) {
                    tipo = 'number';
                }
                
                html += `
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-cog text-primary"></i> ${nombre}
                        </label>
                        <input type="${tipo}" class="form-control" name="${campo}" value="${valor}" placeholder="Ingrese ${nombre.toLowerCase()}">
                    </div>
                `;
            });
        } else {
            $('#tab-caracteristicas-nav').hide();
            html = '<p class="text-muted text-center"><i class="fas fa-info-circle"></i> No hay características específicas para esta categoría.</p>';
        }
        
        container.html(html);
    }
}

// Normalizar texto
function normalizarTextoEdit(texto) {
    return texto.toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim();
}

// Consultar RUC
function consultarRUCEdit() {
    const rucInput = $('#edit_ruc').val().trim();
    
    if (rucInput.length !== 11) {
        Swal.fire({
            icon: 'warning',
            title: 'RUC Inválido',
            text: 'El RUC debe tener exactamente 11 dígitos.'
        });
        return;
    }
    
    Swal.fire({
        title: 'Consultando RUC...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: _URL + "/ajs/conductor/doc/cliente",
        type: "POST",
        data: { doc: rucInput },
        dataType: "json",
        success: function(resp) {
            Swal.close();
            if (resp.razonSocial) {
                $('#edit_razon_social').val(resp.razonSocial);
                Swal.fire({
                    icon: 'success',
                    title: 'RUC Encontrado',
                    text: `Razón social: ${resp.razonSocial}`,
                    timer: 2000
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'RUC No Encontrado',
                    text: 'No se encontró información para este RUC.'
                });
            }
        },
        error: function() {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al consultar el RUC.'
            });
        }
    });
}

// Guardar cambios
function guardarCambiosProducto() {
    const formData = new FormData();
    const idProducto = $('#edit_id_producto').val();
    
    // Validaciones
    const nombre = $('#edit_nombre').val().trim();
    const categoria = $('#edit_categoria').val();
    const precio = $('#edit_precio').val();
    
    if (!nombre || !categoria || !precio) {
        Swal.fire({
            icon: 'error',
            title: 'Campos Requeridos',
            text: 'El nombre, categoría y precio son obligatorios.'
        });
        return;
    }
    
    // Agregar ID
    formData.append('ID_PRODUCTO', idProducto);
    
    // Obtener categoría para determinar el tipo de características
    const categoriaTexto = $('#edit_categoria').find(':selected').text().trim();
    const categoriaNorm = normalizarTextoEdit(categoriaTexto);
    
    // Recopilar datos del formulario principal (excluyendo campos de vehículo que se manejan aparte)
    $('#formEditarProductoModal input, #formEditarProductoModal select').each(function() {
        const element = $(this);
        const name = element.attr('name');
        
        // Excluir campos que se manejarán por separado
        if (name && name !== 'ID_PRODUCTO' && 
            !['vin', 'chasis', 'placa', 'color', 'anio', 'transmision', 'fecha_venc_soat', 'fecha_venc_seguro'].includes(name)) {
            formData.append(name, element.val());
        }
    });
    
    // Manejar características según el tipo de categoría
    if (categoriaNorm.includes('vehiculo')) {
        // Para vehículos, enviar marca y modelo directamente
        formData.append('MARCA', $('#edit_marca').val());
        formData.append('MODELO', $('#edit_modelo').val());
        
        // Enviar características de vehículo como JSON
        const caracteristicasVehiculo = [];

        const camposVehiculo = {
            'edit_vin': 'vin',
            'edit_chasis': 'chasis',
            'edit_placa': 'placa',
            'edit_color': 'color',
            'edit_anio': 'anio',
            'edit_transmision': 'transmision',
            'edit_fecha_venc_soat': 'fecha_venc_soat',
            'edit_fecha_venc_seguro': 'fecha_venc_seguro'
        };
        
        Object.keys(camposVehiculo).forEach(function(campoId) {
            const valor = $('#' + campoId).val();
            if (valor) {  // Solo agregar si tiene valor
                caracteristicasVehiculo.push({
                    nombre_caracteristica: camposVehiculo[campoId],
                    valor_caracteristica: valor
                });
            }
        });
        
        if (caracteristicasVehiculo.length > 0) {
            formData.append('caracteristicas', JSON.stringify(caracteristicasVehiculo));
        }
        
    } else if (categoriaNorm.includes('celular')) {
        // Características de celular se manejan directamente
        $('#edit_caracteristicas_container input').each(function() {
            const element = $(this);
            if (element.attr('name')) {
                formData.append(element.attr('name'), element.val());
            }
        });
    } else {
        // Otras características se envían como JSON
        const caracteristicas = [];
        $('#edit_caracteristicas_container input').each(function() {
            const element = $(this);
            const labelText = element.siblings('label').text();
            const nombre = labelText.replace(/^\s*[\u2713\u2611\u2713\uFE0E]\s*/, '').trim();
            const valor = element.val().trim();
            
            if (nombre) {
                caracteristicas.push({
                    nombre_caracteristica: nombre,
                    valor_caracteristica: valor
                });
            }
        });
        
        if (caracteristicas.length > 0) {
            formData.append('caracteristicas', JSON.stringify(caracteristicas));
        }
    }
    
    // Mostrar loading
    Swal.fire({
        title: 'Guardando cambios...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Enviar datos
    $.ajax({
        url: "/arequipago/actualizarProducto",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function(response) {
            Swal.close();
            
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message || 'Producto actualizado correctamente',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    $('#modalEditarProducto').modal('hide');
                    // Recargar la lista de vehículos si existe la función
                    if (typeof cargarVehiculos === 'function') {
                        cargarVehiculos();
                    }
                    // O recargar productos si existe
                    if (typeof cargarProductos === 'function') {
                        cargarProductos();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: response.message || 'Error al actualizar el producto',
                    confirmButtonText: 'Aceptar'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error("Error:", error);
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'Hubo un problema al actualizar el producto.',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}

// Función para limpiar el formulario al cerrar el modal
function limpiarFormularioEdit() {
    // Limpiar todos los campos del formulario
    $('#formEditarProductoModal')[0].reset();
    
    // Limpiar variables globales
    productDataEdit = {};
    categoriaOriginalEdit = '';
    tipoProductoOriginalEdit = '';
    
    // Ocultar tabs opcionales
    $('#tab-vehiculo-nav').hide();
    $('#tab-caracteristicas-nav').hide();
    
    // Volver al primer tab
    $('#tab-general').tab('show');
    
    // Limpiar contenedor de características
    $('#edit_caracteristicas_container').html('<p class="text-muted text-center"><i class="fas fa-info-circle"></i> Seleccione una categoría para ver las características específicas</p>');
}

// Event listeners
$(document).ready(function() {
    // Cambio de tipo de producto
    $('#edit_tipo_producto').on('change', function() {
        evaluarTipoProductoEdit();
    });
    
    // Cambio de categoría
    $('#edit_categoria').on('change', function() {
        evaluarCategoriaEdit();
    });
    
    // Limpiar formulario al cerrar el modal
    $('#modalEditarProducto').on('hidden.bs.modal', function() {
        limpiarFormularioEdit();
    });
});
</script>
