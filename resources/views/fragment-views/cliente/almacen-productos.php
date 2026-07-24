<?php

require_once "app/models/Producto.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificamos si el usuario tiene sesión activa
if (!isset($_SESSION['id_rol'])) {
    header("Location: /login"); // Redirige al login si no está autenticado
    exit();
}

// Verificamos que el usuario tenga el rol adecuado
if ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 3) {
    header("Location: /arequipago"); // Redirige a la página principal si no tiene permiso
    exit();
}

?>
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= URL::to('/public/css/almacen-productos.css') ?>?v=<?= time() ?>">

 

</head>
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h6 class="page-title">Productos</h6>
            <!-- <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Almacen</a></li>
            </ol> -->
        </div>
        <div class="col-md-4">
            <div class="float-end d-none d-md-block">
                <div hidden class="dropdown">
                    <button class="btn btn-primary  dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-cog me-2"></i> Settings
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <a class="dropdown-item" href="#">Something else here</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Separated link</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <div class="row">
    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-body">
                <div class="alert alert-warning" role="alert">
                    <strong>ALERTA DE ACTUALIZACION!</strong> a partir del año 2021, sunat exige el codigo SUNAT (Código de productos y servicios estándar de las Naciones Unidas - UNSPSC v14_0801, a que hace referencia el catálogo N° 25 del Anexo V de la Resolución de Superintendencia N° 340-2017/SUNAT y modificatorias.). Modifique el valor en Productos
                </div>
            </div>
        </div>
    </div>
 
</div> -->


<div >
    <!--<input type="hidden" name="almacenId" id="almacenId" value="<?php echo $almacenProducto ?>">-->

    <div class="row">
        <div class="col-12">
            <div class="card"  style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
                <div class="card-header">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h4 class="card-title">Lista de Productos</h4>
                            <input type="text" id="buscadorProductos" class="form-control" placeholder="Buscar por Nombre, Código, Razon Social, Categoría o Tipo..."> <!-- Agregado campo de búsqueda -->
                        </div>
                        <div class="col-md-6 text-end mt-4" >
                            
                            <button onclick="downloadReport()" class="btn btn-success"><i class="fa fa-file-excel"></i> Descargar reporte Inventario</button>
                                <?php if ($_SESSION['id_rol'] == 3 || $_SESSION['id_rol'] == 1): ?> <!-- Solo el rol 3 puede ver estos botones -->
                                    <button data-bs-toggle="modal" data-bs-target="#importModal" class="btn btn-primary"><i class="fa fa-file-excel"></i> Importar</button>
                                    <button data-bs-toggle="modal" data-bs-target="#modal-add-prod" class="btn btn-primary mt-2 mb-2" onclick="inicializarPlaceholderDescuento()">
                                        <i class="fa fa-plus"></i> Agregar Producto
                                    </button>
                                    <button class="btn btn-danger btnBorrar" onclick="deleteProducto()">
                                        <i class="fa fa-times"></i> Borrar
                                    </button>
                                <?php endif; ?>
                        </div>
                        
                    </div>

                    <!-- NUEVO: Filtros por Stock y Tipo de Producto -->
                    <div class="row mb-3 filtros-container">
                        <div class="col-md-4">
                            <label class="form-label">Stock</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="stock-filter-buttons">
                                    <button type="button" class="btn-stock-filter active" data-value="todos" onclick="cambiarFiltroStock('todos', this)">
                                        <span class="stock-indicator-active"></span>Todos
                                    </button>
                                    <button type="button" class="btn-stock-filter" data-value="con_stock" onclick="cambiarFiltroStock('con_stock', this)">
                                        <span class="stock-indicator-active"></span>En stock
                                    </button>
                                    <button type="button" class="btn-stock-filter" data-value="sin_stock" onclick="cambiarFiltroStock('sin_stock', this)">
                                        <span class="stock-indicator-active"></span>Agotado
                                    </button>
                                    <button type="button" class="btn-stock-filter" data-value="stock_bajo" onclick="cambiarFiltroStock('stock_bajo', this)">
                                        <span class="stock-indicator-active"></span>Stock Bajo
                                    </button>
                                </div>
                                <button type="button" class="btn-limpiar-filtros" id="btnLimpiarFiltros" onclick="limpiarFiltros()" style="display: none;">
                                    Limpiar
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="filtroTipoProducto" class="form-label">Filtrar por Tipo</label>
                            <select id="filtroTipoProducto" class="form-select form-select-sm" onchange="actualizarBotonLimpiar(); aplicarFiltros();">
                                <option value="todos">Todos los tipos</option>
                                <!-- Las opciones se cargarán dinámicamente -->
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filtroOficina" class="form-label">Filtrar por Oficina</label>
                            <select id="filtroOficina" class="form-select form-select-sm" onchange="cambiarOficina();">
                                <option value="" selected>Todas las oficinas</option>
                                <option value="1">Oficina 1</option>
                                <option value="2">Oficina 2</option>
                                <option value="3">Oficina Lima</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Filtrar por Categoría</label>
                            <!-- Dropdown personalizado con checkboxes -->
                            <div class="dropdown-categoria-wrapper">
                                <button class="btn-dropdown-categoria" type="button" id="btnCategoriaDropdown" onclick="toggleCategoriaDropdown()">
                                    <span id="categoriasSeleccionadasText">Todas las categorías</span>
                                    <i class="fa fa-chevron-down"></i>
                                </button>
                                <div class="dropdown-categoria-menu" id="categoriaDropdownMenu">
                                    <div class="dropdown-categoria-header">
                                        <span class="dropdown-categoria-title">Seleccionar Categorías</span>
                                        <button type="button" class="btn-limpiar-categoria" onclick="limpiarCategoriasSeleccionadas()">
                                            Limpiar <span id="contadorCategorias"></span>
                                        </button>
                                    </div>
                                    <div class="dropdown-categoria-search">
                                        <input type="text" id="buscarCategoria" class="form-control form-control-sm" placeholder="Buscar categoría...">
                                    </div>
                                    <div class="dropdown-categoria-list" id="listaCategorias">
                                        <!-- Las categorías se cargarán dinámicamente aquí -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row d-flex justify-content-end">
                        <button hidden class="btn btn-danger" @click="agregarIds"><i class="fa fa-times"></i> Seleccionar Todos</button>
                    </div>

                </div>
                <div class="card-body">
                    <div hidden class="row">

                        <div class="form-group col-md-2" style="margin:  1rem 0;">
                            <label for="">Almacen</label>
                            <select name="almacenSelect" id="almacenSelect" class="form-control" @change="changeAlmacen($event)" v-model="almacen">
                                <option value="1">Almacen 1</option>
                                <option value="2">Tienda 1</option>
                            </select>
                        </div>
                    </div>


                    <div class="table-responsive">
                        <table id="datatable" class="table table-sm table-bordered text-center" cellspacing="0" width="100%">
                            <thead class="table-header"> 
                                <tr>
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Código</th>
                                    <th>Cantidad</th>
                                    <th>Categoría</th>
                                    <th>RUC</th>
                                    <th>Razón Social</th>
                                    <th>Fecha Vencimiento</th> <!-- Nueva columna para la fecha de vencimiento -->
                                    <th>Tipo de Producto</th> <!-- Nueva columna para tipo_producto -->
                                    <th>Editar</th>
                                    <th>Eliminar <input type="checkbox" class='btnSeleccionarTodos'></th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyProductos">
                                <!-- Los productos se cargarán aquí mediante Ajax -->
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!---

    <div class="modal fade" id="modal-precios" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Precios</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form @submit.prevent="agregarPrecios">

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Precio Unidad: </label>
                                <input v-model="edt.precio_unidad" id="precio_unidad" class="form-control">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Precio Club: </label>
                                <input v-model="edt.precio4" id="precio4" class="form-control">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Precio 1: </label>
                                <input v-model="edt.precio" id="precio1" class="form-control">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Precio 2: </label>
                                <input v-model="edt.precio2" id="precio2" class="form-control">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Precio 3: </label>
                                <input v-model="edt.precio3" id="precio3" class="form-control">
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>

            </div>
        </div>
    </div> --->
        
</div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Nuevo Tipo de Producto</h5>
                        <button type="button" class="btn-close" onclick="cerrarModalTipoProducto()" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <label class="mb-2">Agregar nuevo tipo de Producto:</label>
                        <input id="tipoProducto" type="text" class="form-control mb-4" placeholder="Ingrese tipo de producto">

                        <!-- Radio buttons para seleccionar tipo de venta -->
                        <div class="mt-3">
                            <label>Seleccione el tipo de venta:</label>
                            <div class="d-flex align-items-center mt-2">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="tipoVenta" id="ventaVolumen" value="volumen">
                                    <label class="form-check-label" for="ventaVolumen">
                                        Venta por volumen
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipoVenta" id="ventaUnidad" value="unidad">
                                    <label class="form-check-label" for="ventaUnidad">
                                        Venta por unidad
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="atras()">Cancelar</button>
                        <button onclick="guardarTypeProduct()" type="button" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </div>
    </div>

    <div class="modal fade" id="exampleModalCategoria" tabindex="-1" aria-labelledby="exampleModalLabelCategoria" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #d5d696;">
                    <h5 class="modal-title" id="exampleModalLabel">Nueva Categoría de Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="atras()"></button>
                </div>

                <div class="modal-body" style="background-color: #f7f3e4;">
                    <label class="mb-2">Agregar nueva categoría de Producto:</label>
                    <input id="categoriaProducto" type="text" class="form-control mb-4" placeholder="Ingrese categoría de producto">
                </div>

                <div class="modal-footer" style="background-color: #d5d696;">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalTipoProducto()">Cancelar</button>
                    <button onclick="guardarCategoryProduct()" type="button" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Importar Productos con EXCEL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick=CerrarImportModal()></button>
                </div>
                <div class="modal-body">
                    <form enctype='multipart/form-data'>
                        <div class="mb-3">
                            <p>Descargue el modelo en <span class="fw-bold">EXCEL</span> para importar, no
                                modifique los campos en el archivo, <span class="fw-bold">click para
                                    descargar</span> <a href="<?=URL::to('/reporte/producto/guia')?>">plantilla.xlsx</a></p>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label">Importar Excel:</label>
                        </div>
                        <input id="file-import-excel" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" type="file" class="form-control" onchange="viewImport()" onclick="handleFileClick()">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="CerrarImportModal()">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sliding Panel -->
    <div id="sliding-panel" class="sliding-panel">
        <div class="panel-header">
            <span class="panel-title">Productos importados</span>
            <span class="panel-close" onclick="closePanel()">X</span>
        </div>
        <div class="panel-body">
            <div class="table-responsive"> <!-- Agregado: contenedor con clase table-responsive para scroll -->
                <table id="excel-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Código</th>
                            <th>Cantidad</th>
                            <th>Precio unitario</th>
                            <th>Categoría</th>
                            <th>Razón social</th>
                            <th>Guía de remisión</th>
                            <th>Fecha de registro</th>
                            <th>Fecha de vencimiento</th>
                            <th>Tipo de producto</th>
                            <th>Acciones</th> <!-- Columna para las acciones de eliminar -->
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los datos se agregarán dinámicamente aquí -->
                    </tbody>
                </table>
            </div>
            <button id="import-btn" class="btn btn-primary" onclick="saveProductsMassive()">Importar</button>
        </div>
    </div>

    <div class="modal fade" id="modal-lista-productos" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Lista de productos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm table-bordered text-center" id="tabla-productos">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Código</th>
                                <th>Cantidad</th>
                                <th>Cantidad Unidad</th>
                                <th>Unidad Medida</th>
                                <th>Tipo Producto</th>
                                <th>Perfil</th>
                                <th>Aro</th>
                                <th>Categoría</th>
                                <th>Fecha Vencimiento</th>
                                <th>RUC</th>
                                <th>Razón Social</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="productos-body">
                            <!-- Las filas de productos se agregarán aquí dinámicamente con JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button onclick="guardarListaProductos()" type="button" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCodigoBarras" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Codigo de Barras</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-4 mt-3 text-center">
                        <img id="idCodigoBarras">
                        <p id="codigoProducto" class="mt-2 fw-bold"></p>
                    </div>
                    <!--
                        <div class="mb-3">
                            <label  class="form-label">Escalar</label>
                            <select id="scalimg" class="form-control" >
                                <option value="1">NO</option>
                                <option value="2">SI</option>
                            </select>
                        </div>
                    -->
                    <div class="text-center">
                        <button class="btn btn-primary" id="btnImprimir" onclick="imprimir()">Imprimir</button>
                        <button class="btn btn-primary" id="btnImprimir2" onclick="imprimir2()">Imprimir 2</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para mostrar detalles del producto -->
<div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDetallesLabel"><i class="fas fa-box-open me-2"></i>Detalles del Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <label class="text-muted small">Nombre del Producto</label>
                                <p class="fw-bold mb-0" id="detalle-nombre"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small">Código</label>
                                <p class="fw-bold mb-0" id="detalle-codigo"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small">Cantidad</label>
                                <p class="fw-bold mb-0" id="detalle-cantidad"></p>
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
                                <label class="text-muted small">Categoría</label>
                                <p class="mb-0" id="detalle-categoria"></p>
                            </div>
                            <div class="col-md-12">
                                <label class="text-muted small">Tipo de Producto</label>
                                <p class="mb-0" id="detalle-tipo"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de Proveedor -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-building me-2"></i>Información del Proveedor</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="text-muted small">RUC</label>
                                <p class="mb-0" id="detalle-ruc"></p>
                            </div>
                            <div class="col-md-8">
                                <label class="text-muted small">Razón Social</label>
                                <p class="mb-0" id="detalle-razon-social"></p>
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
                            <div class="col-md-3">
                                <label class="text-muted small">Precio de Compra</label>
                                <p class="fw-bold text-success mb-0" id="detalle-precio"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small">Precio de Venta</label>
                                <p class="fw-bold text-primary mb-0" id="detalle-precio-venta"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small">Descuento por Cuota</label>
                                <p class="fw-bold text-warning mb-0" id="detalle-descuento-cuota"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small">Moneda</label>
                                <p class="mb-0" id="detalle-moneda"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de Fechas -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Fechas y Documentos</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="text-muted small">Fecha de Registro</label>
                                <p class="mb-0" id="detalle-fecha-registro"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Fecha de Vencimiento</label>
                                <p class="mb-0" id="detalle-fecha-vencimiento"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Guía de Remisión</label>
                                <p class="mb-0" id="detalle-guia-remision"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sección condicional para llantas -->
                <div id="detalle-llantas-section" class="card mb-3" style="display: none;">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-circle-notch me-2"></i>Especificaciones de Llanta</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Aro</label>
                                <p class="mb-0" id="detalle-aro"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Perfil</label>
                                <p class="mb-0" id="detalle-perfil"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección condicional para celulares -->
                <div id="detalle-celular-section" class="card mb-3" style="display: none;">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-mobile-alt me-2"></i>Especificaciones del Celular</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="text-muted small">Chip/Línea</label>
                                <p class="mb-0" id="detalle-chip-linea"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Marca</label>
                                <p class="mb-0" id="detalle-marca-equipo"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Color</label>
                                <p class="mb-0" id="detalle-color"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">IMEI</label>
                                <p class="mb-0" id="detalle-imei"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">IMEI 2</label>
                                <p class="mb-0" id="detalle-serie"></p>
                            </div>
                        </div>
                        <hr class="my-3">
                        <h6 class="text-muted small mb-3">Especificaciones Técnicas</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Memoria RAM</label>
                                <p class="mb-0" id="detalle-memoria-ram"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Almacenamiento</label>
                                <p class="mb-0" id="detalle-almacenamiento"></p>
                            </div>
                        </div>
                        <hr class="my-3">
                        <h6 class="text-muted small mb-3">Accesorios Incluidos</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="text-muted small">Cargador</label>
                                <p class="mb-0" id="detalle-cargador"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small">Cable USB</label>
                                <p class="mb-0" id="detalle-cable-usb"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small">Manual</label>
                                <p class="mb-0" id="detalle-manual"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small">Estuche</label>
                                <p class="mb-0" id="detalle-estuche"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección para vehículos -->
                <div id="detalle-vehiculo-section" class="card mb-3" style="display: none;">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-car me-2"></i>Características del Vehículo</h6>
                    </div>
                    <div class="card-body">
                        <div id="detalle-vehiculo-caracteristicas"></div>
                    </div>
                </div>

                <!-- Sección condicional para chip/plan móvil -->
                <div id="detalle-chip-section" class="card mb-3" style="display: none;">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-sim-card me-2"></i>Detalles del Plan</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Plan Mensual</label>
                                <p class="mb-0" id="detalle-plan-mensual"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Operadora</label>
                                <p class="mb-0" id="detalle-operadora"></p>
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

    <div class="modal fade" id="modal-add-prod" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #dbdbb6">
                    <h5 class="modal-title" id="exampleModalLabel">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="focusBody()"></button>
                </div>
                <form>
                    <div class="modal-body">
                        
                        <!-- Sección 1: Información Básica del Producto -->
                        <div class="row mb-3">
                            <div class="col-md-8 ">
                                <label for="nombre_producto" class="form-label">Nombre del Producto</label>
                                <input type="text" id="nombre_producto" name="nombre_producto" class="form-control" required>
                            </div>
                              <div class="col-md-4">
                                <label for="codigo_producto" class="form-label">Código del Producto</label>
                                <input type="text" id="codigo_producto" class="form-control" required placeholder="Escanear o ingresar código"/>
                                <span id="mensaje_duplicado_codigo" style="color: red; font-weight: bold; display: none;"></span>
                            </div>

                        </div>

                        <!-- Marca y Modelo (Opcionales) -->
                        <!-- <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="marca_producto" class="form-label">Marca <span class="text-muted">(Opcional)</span></label>
                                <input type="text" id="marca_producto" name="marca_producto" class="form-control" placeholder="Ingrese la marca">
                            </div>
                            <div class="col-md-6">
                                <label for="modelo_producto" class="form-label">Modelo <span class="text-muted">(Opcional)</span></label>
                                <input type="text" id="modelo_producto" name="modelo_producto" class="form-control" placeholder="Ingrese el modelo">
                            </div>
                        </div> -->

                        <div class="row mb-2">
                          <div class="col-md-3">
                                <label for="tipo_producto" class="form-label">Tipo de producto</label>
                                <div class="d-flex gap-0">
                                    <select name="tipo_producto" id="tipo_producto" required class="form-select" onchange="verificarTipo()">
                                        <option value="notTipo">Seleccionar</option>
                                        <option value="fisico">Físico</option>
                                        <option value="intangible">Intangible</option>
                                    </select>
                                    <button type="button" onclick="abrirModalTipoProducto()" class="btn btn-primary btn-sm btn-add-option"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-md-2" id="cantidad">
                                <label for="cantidad_producto" class="form-label">Cantidad</label>
                                <input type="number" id="cantidad_producto" name="cantidad_producto" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label for="categoria_producto" class="form-label">Categoría</label>
                                <div class="d-flex gap-0">
                                <select name="categoria_producto" id="categoria_producto" class="form-select" onchange="mostrarIntfecha()">
                                <option value="seleccionar_categoría">Seleccionar Categoría</option>
                                </select>
                                <button type="button" onclick="abrirModalCategoria()" class="btn btn-primary btn-sm btn-add-option">
                                <i class="fa fa-plus"></i>
                                </button>
                                </div>
                                </div>
                                 <div class="col-md-3">
                                <label for="oficina_producto" class="form-label">Oficina</label>
                                <select name="oficina" id="oficina_producto" class="form-select" required>
                                    <option value="1" selected>Oficina 1</option>
                                    <option value="2">Oficina 2</option>
                                    <option value="3">Oficina Lima</option>
                                </select>
                            </div>
                        </div>

                   
                        <!-- Sección 2: Unidad de Medida (Condicional) -->
                        <div id="unidad_medida_wrapper" style="display: none;">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="cantidad_unidad" class="form-label">Cantidad por unidad</label>
                                    <input type="number" id="cantidad_unidad" name="cantidad_unidad" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="unidad_medida" class="form-label">Unidad de medida</label>
                                    <select name="unidad_medida" id="unidad_medida" class="form-select">
                                        <option value="notUM">Seleccionar</option>
                                        <option value="Litros">Litros</option>
                                        <option value="Galones (3.785 litros)">Galones (3.785 litros)</option>
                                        <option value="Kilogramos">Kilogramos</option>
                                        <option value="OZ">OZ</option>
                                    </select>
                                </div>
                            </div>
                        </div>   
                        
                        <!-- Sección 3: Detalles de Llantas (Condicional) -->
                        <div id="llantas_wrapper" style="display: none;">
                            <hr class="my-3">
                            <h6 class="mb-3 text-muted">Detalles de Llanta</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="aro" class="form-label">Aro</label>
                                    <input id="aro" name="aro" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="perfil" class="form-label">Perfil</label>
                                    <input id="perfil" name="perfil" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Sección 4: Detalles de Celular (Condicional) -->
                        <div id="celular_wrapper" style="display: none;">
                            <hr class="my-3">
                            <h6 class="mb-3 text-muted">Detalles del Celular</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="chip_linea" class="form-label">Chip de la línea</label>
                                    <input id="chip_linea" name="chip_linea" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="marca_equipo" class="form-label">Marca de Equipo</label>
                                    <input id="marca_equipo" name="marca_equipo" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="modelo" class="form-label">Modelo</label>
                                    <input id="modelo" name="modelo" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="colorc" class="form-label">Color</label>
                                    <input id="colorc" name="colorc" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="memoria_ram" class="form-label">Memoria RAM</label>
                                    <select id="memoria_ram" name="memoria_ram" class="form-control">
                                        <option value="">Seleccionar</option>
                                        <option value="2GB">2 GB</option>
                                        <option value="3GB">3 GB</option>
                                        <option value="4GB">4 GB</option>
                                        <option value="6GB">6 GB</option>
                                        <option value="8GB">8 GB</option>
                                        <option value="12GB">12 GB</option>
                                        <option value="16GB">16 GB</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="almacenamiento" class="form-label">Almacenamiento</label>
                                    <select id="almacenamiento" name="almacenamiento" class="form-control">
                                        <option value="">Seleccionar</option>
                                        <option value="16GB">16 GB</option>
                                        <option value="32GB">32 GB</option>
                                        <option value="64GB">64 GB</option>
                                        <option value="128GB">128 GB</option>
                                        <option value="256GB">256 GB</option>
                                        <option value="512GB">512 GB</option>
                                        <option value="1TB">1 TB</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nro_imei" class="form-label">Nº IMEI</label>
                                    <input id="nro_imei" name="nro_imei" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="nro_serie" class="form-label">Nº IMEI 2</label>
                                    <input id="nro_serie" name="nro_serie" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="cargador" class="form-label">Cargador</label>
                                    <input id="cargador" name="cargador" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="cable_usb" class="form-label">Cable USB</label>
                                    <input id="cable_usb" name="cable_usb" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="manual_usuario" class="form-label">Manual del Usuario</label>
                                    <input id="manual_usuario" name="manual_usuario" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="estuche" class="form-label">Caja / Estuche</label>
                                    <input id="estuche" name="estuche" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Sección 5: Detalles de Vehículo (Condicional) -->
                        <div id="vehiculo_wrapper" style="display: none;">
                            <hr class="my-3">
                            <h6 class="mb-3 text-muted">Detalles del Vehículo</h6>

                            <!-- Fila 1: Marca y Modelo -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="marca_vehiculo" class="form-label">Marca</label>
                                    <input id="marca_vehiculo" name="marca_vehiculo" class="form-control" placeholder="Ej: Toyota, Nissan">
                                </div>
                                <div class="col-md-6">
                                    <label for="modelo_vehiculo" class="form-label">Modelo</label>
                                    <input id="modelo_vehiculo" name="modelo_vehiculo" class="form-control" placeholder="Ej: Corolla, Sentra">
                                </div>
                            </div>

                            <!-- Fila 2: VIN y Chasis -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="vin" class="form-label">VIN</label>
                                    <input id="vin" name="vin" class="form-control" placeholder="Número de identificación vehicular">
                                </div>
                                <div class="col-md-6">
                                    <label for="chasis" class="form-label">Nº de Motor</label>
                                    <input id="chasis" name="chasis" class="form-control" placeholder="Número de motor/chasis">
                                </div>
                            </div>

                            <!-- Fila 3: Color y Año -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="color" class="form-label">Color</label>
                                    <input id="color" name="color" class="form-control" placeholder="Ej: Blanco, Negro">
                                </div>
                                <div class="col-md-6">
                                    <label for="anio" class="form-label">Año</label>
                                    <input id="anio" name="anio" type="number" min="1900" max="2099" class="form-control" placeholder="Ej: 2024">
                                </div>
                            </div>

                            <!-- Fila 4: Placa y Transmisión -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="placa_vehiculo" class="form-label">Placa</label>
                                    <input id="placa_vehiculo" name="placa_vehiculo" class="form-control" placeholder="Ej: ABC-123 o 'En trámite'">
                                    <small class="form-text text-muted">Si no tiene placa, escriba "En trámite"</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="transmision_vehiculo" class="form-label">Transmisión</label>
                                    <select id="transmision_vehiculo" name="transmision_vehiculo" class="form-select">
                                        <option value="">Seleccionar</option>
                                        <option value="Manual">Manual</option>
                                        <option value="Automático">Automático</option>
                                        <option value="Automática">Automática</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Fila 5: Kilometraje -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="kilometraje" class="form-label">Kilometraje</label>
                                    <input id="kilometraje" name="kilometraje" type="number" min="0" class="form-control" placeholder="Ej: 15000">
                                    <small class="form-text text-muted">Kilometraje actual del vehículo</small>
                                </div>
                            </div>

                            <!-- Fila GPS -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="gps_activo" name="gps_activo" value="1">
                                        <label class="form-check-label" for="gps_activo">
                                            <i class="fas fa-map-marker-alt me-1"></i> GPS Activo
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Fila 6: Fechas de Vencimiento -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="fecha_venc_soat" class="form-label">Fecha Vencimiento SOAT</label>
                                    <input id="fecha_venc_soat" name="fecha_venc_soat" type="date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="fecha_venc_seguro" class="form-label">Fecha Vencimiento Seguro</label>
                                    <input id="fecha_venc_seguro" name="fecha_venc_seguro" type="date" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Sección 6: Detalles de Chip/Plan Móvil (Condicional) -->
                        <div id="chip_plan_movil_wrapper" style="display: none;">
                            <hr class="my-3">
                            <h6 class="mb-3 text-muted">Detalles del Plan Móvil</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="plan_mensual" class="form-label">Plan Mensual</label>
                                    <input id="plan_mensual" name="plan_mensual" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="operadora" class="form-label">Operadora</label>
                                    <input id="operadora" name="operadora" class="form-control">
                                </div>
                            </div>
                        </div>
                        <!-- Sección 7: Fecha de Vencimiento (Condicional) -->
                        <div id="fecha_vencimiento_wrapper">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label id="lfecha_vencimiento" class="form-label" style="display: none">Fecha de Vencimiento</label>
                                    <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" style="display: none;">
                                </div>
                            </div>
                        </div>

                        <!-- Sección 8: Información del Proveedor -->
                        <hr class="my-3">
                        <h6 class="mb-3 text-muted">Información del Proveedor</h6>
                        <div class="row mb-3">
                            <div class="col-md-6" id="ruc">
                                <label class="form-label">RUC</label>
                                <div class="d-flex gap-2">
                                    <input id="rucInput" required onkeypress="onlyNumber(event)" type="text" class="form-control" maxlength="11">
                                    <button type="button" onclick="consultarRUC()" class="btn btn-secondary btn-sm"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                            <div class="col-md-6" id="razonsocial">
                                <label class="form-label">Razón Social</label>
                                <input id="razon" required type="text" class="form-control" readonly />
                            </div>
                        </div>

                        <!-- Sección 9: Información de Precios -->
                        <hr class="my-3">
                        <h6 class="mb-3 text-muted">Información de Precios</h6>
                        <div class="row mb-3">
                              <div class="col-md-3">
                                <label for="moneda" class="form-label">Moneda</label>
                                <select id="moneda" required class="form-select" onchange="actualizarPlaceholderDescuento()">
                                    <option value="S/.">S/.</option>
                                    <option value="$">$</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="precio" class="form-label">Precio</label>
                                <input id="precio" required type="text" class="form-control"/>
                            </div>
                            <div class="col-md-3">
                                <label for="precioVenta" class="form-label">Precio Venta</label>
                                <input id="precioVenta" required type="text" class="form-control"/>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="descuentoCuota" class="form-label">Descuento por cuota</label>
                                <input id="descuentoCuota" type="number" step="0.01" min="0" class="form-control" placeholder="0.00"/>
                            </div>
                        </div>

                     

                        <!-- Sección 10: Información Adicional -->
                        <hr class="my-3">
                        <h6 class="mb-3 text-muted">Información Adicional</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="guia_remision" class="form-label">Guía Remisión</label>
                                <input id="guia_remision" name="guia_remision" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="fechaActual" class="form-label">Fecha de registro</label>
                                <input id="fechaActual" type="date" class="form-control" readonly>
                            </div>
                        </div>
                     
                </form>  
                
            </div>
            <div class="modal-footer" style="background-color: #dbdbb6;">
                        <button type="button" class="btn btn-primary" onclick="guardarProducto()">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="focusBody()">Cerrar</button>
            </div>
        </div>
    </div>
<!-- Select2 JS (debe estar después de jQuery) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

    function onlyNumber(event) {
        const charCode = event.which ? event.which : event.keyCode;
        if (charCode < 48 || charCode > 57) {
            event.preventDefault();
        }
    }

    // Función global para cambiar de oficina
    window.cambiarOficina = function() {
        // Obtener la oficina seleccionada del filtro
        var oficinaSeleccionada = $('#filtroOficina').val() || 1;

        $.ajax({
            url: '/obtenerTodosProductos',
            type: 'GET',
            data: { oficina: oficinaSeleccionada },
            dataType: 'json',
            success: function(productos) {
                todosLosProductos = productos; // Guardar en variable global
                mostrarProductos(productos); // Mostrar todos inicialmente
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar los productos:", error);
                alert("Hubo un error al cargar los productos. Por favor, intenta de nuevo más tarde.");
            }
        });
    }

    function consultarRUC() {
        const rucInput = document.getElementById("rucInput").value;

        if (rucInput.length === 11) {
            // Mostrar el loader menor
            $("#loader-menor").show();

            // Realizar la solicitud AJAX
            _ajax("/ajs/conductor/doc/cliente", "POST", { doc: rucInput }, (resp) => {
                // Ocultar el loader menor
                $("#loader-menor").hide();

                console.log(resp);

                if (resp.razonSocial) {
                    // Mostrar la razón social en el input correspondiente
                    document.getElementById("razon").value = resp.razonSocial;
                } else {
                    // Manejar el caso de RUC no encontrado
                    alertAdvertencia("RUC no encontrado.");
                }
            });
        } else {
            // Manejar el caso de RUC inválido
            alertAdvertencia("El RUC debe ser de 11 dígitos.");
        }
    }

    function mostrarIntfecha() {
        var select = document.getElementById('categoria_producto');
        var label = document.getElementById('lfecha_vencimiento');
        var dateInput = document.getElementById('fecha_vencimiento');
        var celularWrapper = document.getElementById('celular_wrapper');
        var chipPlanMovilWrapper = document.getElementById('chip_plan_movil_wrapper');
        var vehiculoWrapper = document.getElementById('vehiculo_wrapper');

        const selectedTextRaw = select.options[select.selectedIndex].text.toLowerCase().trim();

        if (selectedTextRaw === 'soat' || selectedTextRaw === 'seguro') {
            label.style.display = 'block';
            dateInput.style.display = 'block';
        } else {
            label.style.display = 'none';
            dateInput.style.display = 'none';
        }

        const llantasWrapper = document.getElementById("llantas_wrapper");

        // Verificar la opción seleccionada usando el TEXTO, no el value (que es un ID numérico)
        if (selectedTextRaw === "llantas") {
            llantasWrapper.style.display = "block";
        } else {
            llantasWrapper.style.display = "none";
        }

        // Mostrar/ocultar campos para Celular
        if (select.options[select.selectedIndex].text === 'Celular') {
            celularWrapper.style.display = 'block';
        } else {
            celularWrapper.style.display = 'none';
        }

        // Mostrar/ocultar campos para Chip (Línea corporativa)
        if (select.options[select.selectedIndex].text === 'Chip (Linea corporativa)') {
            chipPlanMovilWrapper.style.display = 'block';
        } else {
            chipPlanMovilWrapper.style.display = 'none';
        }

        // Mostrar/ocultar campos para Vehículo de manera flexible (ignorar mayúsculas, acentos, espacios)
        const vehiculoRegex = /veh[íi]cul[o]?[s]?|moto\s*lineal|motokar|trimovil|cuatrimoto/i;
        const selectedText = select.options[select.selectedIndex].text.toLowerCase().trim();
        if (vehiculoRegex.test(selectedText)) {
            vehiculoWrapper.style.display = 'block';
        } else {
            vehiculoWrapper.style.display = 'none';
        }
    }

    function atras(){
        // Cerrar el modal hijo
        $('#exampleModal').modal('hide');
        
        // Restaurar el z-index del modal padre
        setTimeout(function() {
            $('#modal-add-prod').css('z-index', '1050');
            $('.modal-backdrop').css('z-index', '1040');
        }, 300);
    }

    function abrirModalTipoProducto() {
        // Ocultar temporalmente el modal padre sin cerrarlo completamente
        $('#modal-add-prod').css('z-index', '1040');
        $('.modal-backdrop').css('z-index', '1039');
        
        // Mostrar el modal hijo con z-index más alto
        $('#exampleModal').css('z-index', '1060').modal('show');
    }

    function cerrarModalTipoProducto() {
        $('#exampleModal').modal('hide');
        setTimeout(function() {
            $('#modal-add-prod').css('z-index', '1050');
            $('.modal-backdrop').css('z-index', '1040');
        }, 300);
    }

    function abrirModalCategoria() {
        // Ocultar temporalmente el modal padre sin cerrarlo completamente
        $('#modal-add-prod').css('z-index', '1040');
        $('.modal-backdrop').css('z-index', '1039');
        
        // Mostrar el modal hijo con z-index más alto
        $('#exampleModalCategoria').css('z-index', '1060').modal('show');
    }

    function focusBody() {
        var modalPadre = document.getElementById('modal-add-prod'); // Referencia al modal
        var modal = new bootstrap.Modal(modalPadre); // Crea una instancia de modal de Bootstrap
        modal.hide(); // Cierra el modal
        console.log("Funciona Focus");

        document.getElementById('descuentoCuota').value = '';
        // NUEVA LÍNEA: Restaurar placeholder por defecto
        document.getElementById('descuentoCuota').placeholder = '0.50';

        // Usamos el evento 'hidden.bs.modal' para realizar las acciones después de que el modal se haya cerrado
        $(modalPadre).on('hidden.bs.modal', function () {
            $('body').removeClass('modal-open'); // Elimina la clase modal-open si persiste
            $('body').css('overflow', 'auto');  // Restaura el scroll

            // Limpia manualmente los backdrops solo si Bootstrap no lo hace automáticamente
            var backdrops = document.querySelectorAll('.modal-backdrop'); // Selecciona todos los backdrops
            if (backdrops.length > 1) { // Si hay más de un backdrop, elimina los extras
                for (let i = 1; i < backdrops.length; i++) {
                    backdrops[i].remove(); // Elimina el exceso de backdrops
                }
            }

            $(modalPadre).off('hidden.bs.modal'); // Desuscribirse del evento después de ejecutarlo para evitar conflictos futuros
        });
}

  

      
    function descarFunccc(){
        window.open(_URL +
            `/reporte/producto/excel?texto=${$("#datatable_filter input").val()}`)
    }

    var codProdT=''
    async function printBarcode() {
        try {
            const printer = await EscPosPrinter.requestPrinter();

            // Conectar a la impresora
            await printer.connect();

            // Configurar el tamaño del ticket (50 mm x 25 mm)
            await printer.setPageFormat(50, 25);

            // Imprimir el título
            await printer.printText('Barcode Title\n');

            // Generar el código de barras utilizando JsBarcode
            const svgData = JsBarcode.generateSvg('123456789', {
                format: 'CODE128',
                displayValue: true,
            });

            // Imprimir el código de barras
            await printer.printImage(svgData);

            // Cortar el ticket
            await printer.cut();

            // Desconectar la impresora
            await printer.disconnect();
        } catch (error) {
            console.error(error);
        }
    }
    
    function imprimir2() {
        let imageSrc = $("#idCodigoBarras").attr("src"); // Obtener la imagen del código de barras desde el modal
        let codigo = $("#codigoProducto").text(); // Obtener el código del producto desde el modal

        if (!imageSrc || !codigo) {
            alert("No hay un código de barras para imprimir.");
            return;
        }

        // Hacer una solicitud AJAX para obtener el nombre y el precio del producto
        $.ajax({
            url: "/getdataForBarcode", // Ruta al controlador
            type: "GET", // Método GET para obtener los datos
            data: { codigo: codigo }, // Enviar el código como parámetro
            dataType: "json", // Esperamos una respuesta en formato JSON
            success: function (response) {
                if (response.success) { // Verificar si la respuesta es exitosa
                    let nombre = response.nombre; // Obtener el nombre del producto
                    let precio = response.precio_venta; // Obtener el precio del producto

                    // Crear una nueva ventana emergente en pantalla completa
                    let myWindow = window.open("", "_blank", `width=${screen.width},height=${screen.height},top=0,left=0`);

                    // Contenido de la ventana con el código de barras, nombre y precio
                    myWindow.document.write(`
                        <html>
                        <head>
                            <title>Imprimir Código de Barras</title>
                            <style>
                                * { text-align: center; font-family: Arial, sans-serif; }
                                .contenedor {
                                    width: 5.5cm;
                                    min-height: 3cm;
                                    display: flex;
                                    flex-direction: column;
                                    align-items: center;
                                    justify-content: center;
                                    padding: 10px;
                                    border: 1px solid black;
                                    word-wrap: break-word; 
                                }
                                img { max-width: 100%; height: auto; }
                                p { font-size: 14px; margin: 5px 0 0; } /* Eliminé font-weight: bold para que no esté en negrita */
                                .nombre { font-size: 14px; margin-bottom: 2px; word-break: break-word;} /* Eliminé font-weight: bold para que no esté en negrita */
                                .precio { font-size: 14px; font-weight: bold; color: black; }
                            </style>
                        </head>
                        <body onload="window.print(); window.close();">
                            <div class="contenedor">
                                <p class="nombre">${nombre}</p> <!-- Nombre centrado -->
                                <p class="precio">S/. ${precio}</p> <!-- Precio centrado y en negrita -->
                                <img src="${imageSrc}"> <!-- Imagen del código de barras -->
                                <p>${codigo}</p> <!-- Código del producto -->
                            </div>
                        </body>
                        </html>
                    `);

                    myWindow.document.close();

                } else {
                    alert("Error: No se pudo obtener los datos del producto.");
                }
            },
            error: function () {
                alert("Error al conectar con el servidor.");
            }
        });
    }

    function imprimir() {
       
        let imageSrc = $("#idCodigoBarras").attr("src"); // Obtener la imagen del código de barras
        let codigo = $("#codigoProducto").text(); // Obtener el código del producto

        if (!imageSrc || !codigo) {
            alert("No hay un código de barras para imprimir.");
            return;
        }

        let screenWidth = screen.availWidth; // Ancho total de la pantalla
        let screenHeight = screen.availHeight; // Alto total de la pantalla

        // Crear una nueva ventana emergente en pantalla completa
        let myWindow = window.open("", "_blank", `width=${screenWidth},height=${screenHeight},top=0,left=0`); // MODIFICADO: ahora la ventana se abre en pantalla completa
        // Contenido de la ventana con el código de barras y su código debajo
        myWindow.document.write(`
            <html>
            <head>
                <title>Imprimir Código de Barras</title>
                <style>
                    * { text-align: center; font-family: Arial, sans-serif; }
                    .contenedor {
                        width: 5cm;
                        height: 2.5cm;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        padding: 10px;
                        border: 1px solid black;
                    }
                    img { max-width: 100%; height: auto; }
                    p { font-size: 14px; font-weight: bold; margin: 5px 0 0; }
                </style>
            </head>
            <body onload="window.print(); window.close();">
                <div class="contenedor">
                    <img src="${imageSrc}">
                    <p>${codigo}</p>
                </div>
            </body>
            </html>
        `);

        myWindow.document.close();

    }

    function cargarTiposProducto() {
        $.ajax({
            url: "/cargartiposproducto",
            method: "GET",
            dataType: "json",
            success: function (response) {
                if (Array.isArray(response)) {
                    var select = $('#tipo_producto');
                    response.forEach(function(tipo) {
                        select.append($('<option>', {
                            value: tipo.idtipo_producto,
                            text: tipo.tipo_productocol
                        }));
                    });
                } else {
                    console.error("La respuesta no es un arreglo");
                }
            },
            error: function () {
                alert('Ocurrió un error al obtener los tipos de producto.');
            }
        });
    }

    function guardarTypeProduct(){

        const tipoProducto = document.getElementById('tipoProducto').value.trim();

        if (!tipoProducto) {
            Swal.fire({
                icon: 'warning',
                title: 'Campo vacío',
                text: 'Por favor, ingrese un tipo de producto antes de guardar.',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        // Validación para asegurarse de que uno de los dos radio buttons haya sido seleccionado
        const tipoVenta = document.querySelector('input[name="tipoVenta"]:checked');
        
        if (!tipoVenta) {
            Swal.fire({
                icon: 'warning',
                title: 'Selección requerida',
                text: 'Por favor, seleccione un tipo de venta (volumen o unidad).',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        // Obtener el valor del radio button seleccionado (volumen o unidad)
        const tipoVentaValue = tipoVenta.value;

        $.ajax({
            url: '/guardarTipoProducto',
            type: 'POST',
            data: { tipo_producto: tipoProducto,
                    tipo_venta: tipoVentaValue
             },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Tipo de producto guardado correctamente.',
                        confirmButtonText: 'Aceptar'
                    });
                    document.getElementById('tipoProducto').value = ''; // Limpiar el campo
                    // Limpiar los radio buttons (desmarcar ambos)
                    const radioButtons = document.querySelectorAll('input[name="tipoVenta"]');
                    radioButtons.forEach((radio) => {
                        radio.checked = false;
                    });
                    
                    // Actualizar el select con el nuevo tipo de producto
                    actualizarSelectTipoProducto(response.nuevoTipoProducto);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: response.message || 'Hubo un problema al guardar el tipo de producto.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Error al guardar el tipo de producto. Intenta nuevamente.',
                    confirmButtonText: 'Aceptar'
                });
                console.error('Detalles del error:', xhr.responseText);
            }
        });
    }

    function actualizarSelectTipoProducto(nuevoTipoProducto) {
        const select = document.getElementById('tipo_producto');
        
        // Verificar si el nuevo tipo de producto ya existe en el select
        let existe = false;
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value == nuevoTipoProducto.idtipo_producto) {
                existe = true;
                break;
            }
        }
        
        // Si no existe, añadir el nuevo tipo de producto
        if (!existe) {
            const nuevaOpcion = document.createElement('option');
            nuevaOpcion.value = nuevoTipoProducto.idtipo_producto;
            nuevaOpcion.textContent = nuevoTipoProducto.tipo_productocol;
            select.appendChild(nuevaOpcion);
        }
    }

    function verificarTipo() {
        console.log("Si se ejecuta la función");

        const tipoProducto = document.getElementById('tipo_producto').value;
        
        // Ocultar los campos al principio
        
        document.getElementById('unidad_medida_wrapper').style.display = 'none';

        // Solicitar el tipo de venta asociado al tipo de producto seleccionado
        $.ajax({
            url: "/obtenerTipoProducto", // Ruta en web.php
            method: "GET",
            data: { tipoProducto: tipoProducto }, // Enviar el valor seleccionado
            dataType: "json",
            success: function (response) {
                if (response.tipo_venta) {
                    // Si se recibe el tipo de venta, manejar el valor recibido
                    if (response.tipo_venta === 'volumen') {
                        // Mostrar campos relacionados con volumen
                        console.log("Tipo de venta: Volumen");
                        document.getElementById('unidad_medida_wrapper').style.display = 'block';
                    } else if (response.tipo_venta === 'unidad') {
                        // Mostrar campos relacionados con unidad
                        console.log("Tipo de venta: Unidad");
                        
                    }
                } else {
                    console.error("No se pudo obtener el tipo de venta.");
                }
            },
            error: function () {
                console.error("Error en la solicitud AJAX.");
            }
        });
    }




    function cargarProductos() {
    $.ajax({
        url: '/obtenerTodosProductos',
        type: 'GET',
        dataType: 'json',
        success: function(productos) {
            var tbody = $('#tbodyProductos');
            tbody.empty();
            $.each(productos, function(i, producto) {
                tbody.append(`
                    <tr>
                        <td>${producto.idproductosv2}</td>
                        <td>
                            <a href="#" onclick="event.preventDefault(); mostrarCodigoBarras( // Evita la recarga de l
                                '${producto.idproductosv2}',
                                '${producto.nombre}',
                                '${producto.codigo}',
                                '${producto.precio}'
                            )">${producto.nombre}</a>
                        </td>
                        <td>${producto.codigo}</td>
                        <td>${producto.cantidad}</td>
                        <td>${producto.categoria}</td>
                        <td>${producto.ruc}</td>
                        <td>${producto.razon_social}</td>
                        <td>${producto.fecha_vencimiento ? producto.fecha_vencimiento : 'No disponible'}</td>
                        <td>${producto.tipo_producto}</td>
                        <td><button class="btn btn-sm btn-primary editar-producto" data-id="${producto.idproductosv2}" onclick="redirigirEditar(${producto.idproductosv2})">Editar</button></td>
                        <td><input type="checkbox" class="eliminar-producto" data-id="${producto.idproductosv2}"></td>
                        <td><button class="btn btn-sm btn-info" onclick="mostrarDetallesProducto(${producto.idproductosv2})">Detalles</button></td>
                    </tr>
                `);
            });
            toggleSeleccionarTodos();
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar los productos:", error);
            alert("Hubo un error al cargar los productos. Por favor, intenta de nuevo más tarde.");
        }
    });
}

    function guardarProducto() {
        // Obtener los datos del formulario
        var nombre = document.getElementById('nombre_producto').value;

        // Obtener marca y modelo si existen (pueden estar en campos generales o de vehículo)
        var marcaElement = document.getElementById('marca_producto') || document.getElementById('marca_vehiculo');
        var modeloElement = document.getElementById('modelo_producto') || document.getElementById('modelo_vehiculo');
        var marca = marcaElement ? marcaElement.value : '';
        var modelo = modeloElement ? modeloElement.value : '';

        // Obtener el tipo de producto usando el texto visible
        var tipo_producto_select = document.getElementById('tipo_producto');
        var tipo_producto_text = tipo_producto_select.options[tipo_producto_select.selectedIndex].text;

        var codigo = document.getElementById('codigo_producto').value;
        var cantidad = document.getElementById('cantidad_producto').value;

        // Validar que el código no contenga espacios
        if (codigo.includes(' ')) {
            const mensajeDuplicado = document.getElementById('mensaje_duplicado_codigo');
            mensajeDuplicado.textContent = "⚠ EL CÓDIGO NO PUEDE CONTENER ESPACIOS";
            mensajeDuplicado.style.color = "#dc3545";
            mensajeDuplicado.style.display = "inline";
            document.getElementById('codigo_producto').focus();
            return;
        }

        // Obtener la categoría usando el texto visible
        var categoria_select = document.getElementById('categoria_producto');
        var categoria_text = categoria_select.options[categoria_select.selectedIndex].text;

        var ruc = document.getElementById('rucInput').value;
        var razon_social = document.getElementById('razon').value;
        var fecha_vencimiento = document.getElementById('fecha_vencimiento').value; // Obtener el valor de la fecha
        var precio = document.getElementById('precio').value;
        var fecha_registro = document.getElementById('fechaActual').value;
        var guia_remision = document.getElementById('guia_remision').value;

        var precio_venta = document.getElementById('precioVenta').value.trim(); // Eliminar espacios en blanco

        var moneda = document.getElementById('moneda').value;
        var descuento_cuota = document.getElementById('descuentoCuota').value.trim();

        // Convertir descuento_cuota a null si está vacío
        if (descuento_cuota === '') {
            descuento_cuota = null;
        }

        // Verificar si está vacío
        if (precio_venta === '') {
            Swal.fire({
                icon: 'error',
                title: 'Campo vacío',
                text: 'Por favor, ingrese un precio de venta válido.',
            });
            return; // Detener ejecución si está vacío
        }

        // Validar que solo contenga números y un solo punto decimal
        if (!/^\d+(\.\d{0,2})?$/.test(precio_venta)) { // Nueva validación con regex
            Swal.fire({
                icon: 'error',
                title: 'Entrada inválida',
                text: 'Ingrese un precio de venta válido con hasta 2 decimales.',
            });
            return; // Detener ejecución si no es válido
        }

        precio_venta = parseFloat(precio_venta).toFixed(2); // Convertir a número y redondear a 2 decimales

        console.log(precio_venta); // Mostrar en consola para verificar


        console.log({
            nombre,
            cantidad,
            categoria_text,
            ruc,
            precio,
            razon_social,
            tipo_producto_text
        });

        if (!nombre || !cantidad || !categoria_text || !ruc || !precio || !razon_social || tipo_producto_text === 'Seleccionar') {
        Swal.fire({
            icon: 'warning',
            title: '¡Atención!',
            text: 'Todos los campos son obligatorios.',
            confirmButtonText: 'Aceptar'
        });
        return;
    }
        
        var cantidad_unidad = null;
        var unidad_medida = null;
        var unidad_medida_wrapper = document.getElementById('unidad_medida_wrapper');
        var celular_wrapper = document.getElementById('celular_wrapper');

        console.log("Inicio de la validación");
        
        if (unidad_medida_wrapper && unidad_medida_wrapper.style.display !== 'none') {
            var cantidad_unidad_input = document.getElementById('cantidad_unidad');
            var unidad_medida_select = document.getElementById('unidad_medida');

            if (cantidad_unidad_input && unidad_medida_select) {
                cantidad_unidad = cantidad_unidad_input.value;
                unidad_medida = unidad_medida_select.value;

                if (!cantidad_unidad || unidad_medida === 'notUM') {
                    Swal.fire({
                        icon: 'warning',
                        title: '¡Atención!',
                        text: 'Cantidad por unidad y unidad de medida son obligatorios.',
                        confirmButtonText: 'Aceptar'
                    });
                    return;
                }
            } else {
                console.error('Los elementos cantidad_unidad o unidad_medida no existen en el DOM.');
                return;
            }
        }

        console.log(cantidad_unidad, unidad_medida);

        // Validar los campos "aro" y "perfil" si "llantas_wrapper" está visible
        var llantas_wrapper = document.getElementById('llantas_wrapper'); // Obtenemos el div de llantas_wrapper
        if (llantas_wrapper && llantas_wrapper.style.display !== 'none') {
            var aro = document.getElementById('aro').value; // Obtenemos el valor del campo aro
            var perfil = document.getElementById('perfil').value; // Obtenemos el valor del campo perfil

            if (!aro || !perfil) { // Validamos que ambos campos no estén vacíos
                Swal.fire({
                    icon: 'warning',
                    title: '¡Atención!',
                    text: 'Los campos Aro y Perfil son obligatorios cuando seleccionas Llantas.',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
        }

        var chip_wrapper = document.getElementById('chip_plan_movil_wrapper'); // Obtenemos el div de chip_wrapper
        if (chip_wrapper && chip_wrapper.style.display !== 'none') {
            var plan_mensual = document.getElementById('plan_mensual').value;
            var operator = document.getElementById('operadora').value; // Obtener el valor del campo plan_mensual
            if (!plan_mensual) { // Validamos que el campo no esté vacío
                Swal.fire({
                    icon: 'warning',
                    title: '¡Atención!',
                    text: 'El campo Plan mensual es obligatorio cuando seleccionas Chip (Linea corporativa).',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
        }

        
        console.log("Inicio de la validación");
        // Enviar los datos al servidor mediante AJAX
        var formData = new FormData();
        formData.append('nombre_producto', nombre);
        formData.append('marca_producto', marca);
        formData.append('modelo_producto', modelo);
        formData.append('tipo_producto', tipo_producto_text); // Incluir tipo_producto (texto)
        formData.append('codigo_producto', codigo);
        formData.append('cantidad_producto', cantidad);
        formData.append('categoria_producto', categoria_text); // Incluir categoria (texto)
        formData.append('ruc', ruc);
        formData.append('precio_venta', precio_venta);
        formData.append('razonsocial', razon_social);
        formData.append('moneda', moneda);
        formData.append('descuento_cuota', descuento_cuota);
        formData.append('fecha_vencimiento', fecha_vencimiento); // Incluir fecha_vencimiento
        formData.append('cantidad_unidad', cantidad_unidad); // Agregar cantidad_unidad
        formData.append('unidad_medida', unidad_medida); // Agregar unidad_medida
        formData.append('precio', precio); // Agregar precio
        formData.append('fecha_registro', fecha_registro);
        formData.append('guia_remision',guia_remision);
        formData.append('oficina', document.getElementById('oficina_producto').value); // Agregar oficina
        // Agregar valores de aro y perfil si llantas_wrapper está visible
        if (llantas_wrapper && llantas_wrapper.style.display !== 'none') {
            formData.append('aro', document.getElementById('aro').value); // Añadido aro al formData
            formData.append('perfil', document.getElementById('perfil').value); // Añadido perfil al formData
        }

        // Agregar el campo de plan mensual si chip_wrapper está visible
        if (chip_wrapper && chip_wrapper.style.display !== 'none') {
            formData.append('plan_mensual', document.getElementById('plan_mensual').value); 
            formData.append('operator', document.getElementById('operadora').value);// Añadido plan_mensual al formData
        }

        if (celular_wrapper && celular_wrapper.style.display !== 'none') {
            formData.append('chip_linea', document.getElementById('chip_linea').value);
            formData.append('marca_equipo', document.getElementById('marca_equipo').value);
            formData.append('modelo', document.getElementById('modelo').value);
            formData.append('nro_imei', document.getElementById('nro_imei').value);
            formData.append('nro_serie', document.getElementById('nro_serie').value);
            formData.append('colorc', document.getElementById('colorc').value);
            formData.append('cargador', document.getElementById('cargador').value);
            formData.append('cable_usb', document.getElementById('cable_usb').value);
            formData.append('manual_usuario', document.getElementById('manual_usuario').value);
            formData.append('estuche', document.getElementById('estuche').value);
            formData.append('memoria_ram', document.getElementById('memoria_ram').value);
            formData.append('almacenamiento', document.getElementById('almacenamiento').value);
        }

        // Obtener el wrapper de vehículo
        var vehiculo_wrapper = document.getElementById('vehiculo_wrapper');
        if (vehiculo_wrapper && vehiculo_wrapper.style.display !== 'none') {
            var fecha_venc_soat = document.getElementById('fecha_venc_soat').value;
            var fecha_venc_seguro = document.getElementById('fecha_venc_seguro').value;
            var chasis = document.getElementById('chasis').value;
            var vin = document.getElementById('vin').value; 

            if (!fecha_venc_soat) { // Validar que el campo no esté vacío
                Swal.fire({
                    icon: 'warning',
                    title: '¡Atención!',
                    text: 'La fecha de vencimiento de SOAT es obligatoria cuando seleccionas Vehículo.',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }

            // Agregar el valor al FormData si todo es válido
            formData.append('fecha_venc_soat', fecha_venc_soat);
            formData.append('fecha_venc_seguro', fecha_venc_seguro);
            formData.append('chasis', chasis);
            formData.append('vin', vin);
            formData.append('color', document.getElementById('color').value);  // Color añadido
            formData.append('anio', document.getElementById('anio').value);    // Año añadido
            formData.append('placa_vehiculo', document.getElementById('placa_vehiculo').value);  // Placa añadido
            formData.append('transmision_vehiculo', document.getElementById('transmision_vehiculo').value);  // Transmisión añadido
            formData.append('kilometraje', document.getElementById('kilometraje').value);  // Kilometraje añadido
            formData.append('gps_activo', document.getElementById('gps_activo').checked ? '1' : '0');  // GPS añadido
        }

        $.ajax({
            url: '/guardarProducto',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log("Respuesta recibida:", response);
                var result = typeof response === 'string' ? JSON.parse(response) : response;
                console.log("Resultado procesado:", result);
                
                if (result.status === 'success') { // Se cambió de == a === para una comparación estricta
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: result.message,
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Limpiar los campos
                            document.getElementById('nombre_producto').value = '';
                            document.getElementById('codigo_producto').value = '';
                            document.getElementById("tipo_producto").value = "notTipo";
                            const unidadMedidaWrapper = document.getElementById("unidad_medida_wrapper");
                            if (unidadMedidaWrapper.style.display !== "none") {
                                // Resetear el campo "cantidad_unidad"
                                document.getElementById("cantidad_unidad").value = "";

                                // Resetear el select "unidad_medida" a su valor por defecto
                                document.getElementById("unidad_medida").value = "notUM";
                            }
                            document.getElementById("cantidad_producto").value = "";
                            document.getElementById("categoria_producto").value = "seleccionar_categoría";
                            document.getElementById('rucInput').value = '';
                            document.getElementById('razon').value = '';
                            document.getElementById('fecha_vencimiento').value = ''; // Limpiar la fecha
                            document.getElementById('precio').value = ''; // Limpiar precio 
                            document.getElementById('precioVenta').value = '';
                            document.getElementById('moneda').value = 'S/.'; // Restablecer a valor por defecto
                            document.getElementById('descuentoCuota').value = '';
                            document.getElementById('aro').value = ''; // Limpiar aro
                            document.getElementById('perfil').value = ''; // Limpiar perfil
                            document.getElementById('guia_remision').value = '';

                            unidadMedidaWrapper.style.display = "none";

                            const unidadMedidaWrapperDos = document.getElementById("llantas_wrapper");
                            if (unidadMedidaWrapperDos.style.display !== "none") {
                                // Resetear el campo "cantidad_unidad"
                                document.getElementById("aro").value = "";

                                // Resetear el campo "cantidad_unidad"
                                document.getElementById("perfil").value = "";
                            }
                            unidadMedidaWrapperDos.style.display = "none";

                            // Limpiar y ocultar celular_wrapper si está visible
                            const celularWrapper = document.getElementById('celular_wrapper');
                            if (celularWrapper && celularWrapper.style.display !== 'none') {
                                celularWrapper.querySelectorAll('input').forEach(input => input.value = '');
                                celularWrapper.style.display = 'none';
                            }

                            // Limpiar y ocultar chip_plan_movil_wrapper si está visible
                            const chipWrapper = document.getElementById('chip_plan_movil_wrapper');
                            if (chipWrapper && chipWrapper.style.display !== 'none') {
                                chipWrapper.querySelectorAll('input').forEach(input => input.value = '');
                                chipWrapper.style.display = 'none';
                            }

                            const vehiculoWrapper = document.getElementById('vehiculo_wrapper');
                            if(vehiculoWrapper && vehiculoWrapper.style.display !=='none'){
                                vehiculoWrapper.querySelectorAll('input').forEach(input => input.value = ''); // Corregido: función flecha correcta
                                vehiculoWrapper.style.display = 'none'; // Corregido: Cambiado 'displa' a 'display'
                            } 

                            cargarProductos();

                            // Cerrar el modal
                            $('#modal-add-prod').modal('hide');
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: result.message,
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Error al enviar los datos. Intenta nuevamente.',
                    confirmButtonText: 'Aceptar'
                });
                console.error('Detalles del error:', xhr.responseText);
            }
        });
    }

    function CerrarImportModal() {
        var modalElement = document.getElementById('importModal'); // Referencia al modal
        var modal = new bootstrap.Modal(modalElement); // Crea una instancia de modal de Bootstrap
        modal.hide(); // Cierra el modal
        console.log("Funciona Focus");

        // Usamos el evento 'hidden.bs.modal' para realizar las acciones después de que el modal se haya cerrado
        $(modalElement).on('hidden.bs.modal', function () {
            $('body').removeClass('modal-open'); // Elimina la clase modal-open si persiste
            $('body').css('overflow', 'auto');  // Restaura el scroll

            // Limpia manualmente los backdrops solo si Bootstrap no lo hace automáticamente
            var backdrops = document.querySelectorAll('.modal-backdrop'); // Selecciona todos los backdrops
            if (backdrops.length > 1) { // Si hay más de un backdrop, elimina los extras
                for (let i = 1; i < backdrops.length; i++) {
                    backdrops[i].remove(); // Elimina el exceso de backdrops
                }
            }

            $(modalElement).off('hidden.bs.modal'); // Desuscribirse del evento después de ejecutarlo para evitar conflictos futuros
        });
    }


    let deletedProducts = [];
    // Función que se activa cuando se carga un archivo Excel
    function viewImport() {

        if (typeof XLSX === 'undefined') {  // ✅ Agregado: Validación de la librería
            console.error("Error: La librería XLSX no está definida. Asegúrate de incluir SheetJS en tu HTML.");
            return;
        }

        // Cerrar el modal antes de abrir el panel
        const modal = document.getElementById('importModal');
        if (modal) {
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.hide();  // Cierra el modal
        }
        const fileInput = document.getElementById('file-import-excel');
        const file = fileInput.files[0];

        if (file) {

            console.log("si hay un file");
            const reader = new FileReader();
            
            // Leer el archivo Excel
            reader.onload = function(event) {
                const data = event.target.result;
                const workbook = XLSX.read(data, { type: 'binary' });

                // Obtener la primera hoja
                const sheet = workbook.Sheets[workbook.SheetNames[0]];
                const rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });

                // Limpiar la tabla antes de cargar los datos
                const tableBody = document.getElementById('excel-table').getElementsByTagName('tbody')[0];
                tableBody.innerHTML = '';

                // Iterar sobre las filas y agregar los datos a la tabla
                rows.forEach((row, index) => {
                    if (index === 0) return; // Saltar la primera fila (encabezados)

                     // Corregir las posiciones de los datos según los encabezados del Excel
                const rowData = {
                    nombre: row[0],  // Cambié el índice a 0 para que 'nombre' esté en la columna correcta
                    codigo: row[1],  // Cambié el índice a 1 para que 'codigo' esté en la columna correcta
                    cantidad: row[2],  // Cambié el índice a 2 para que 'cantidad' esté en la columna correcta
                    precio: row[10],  // Cambié el índice a 3 para 'cantidadPorUnidad'
                    categoria: row[6],  // Cambié el índice a 4 para 'unidadMedida'
                    razonSocial: row[9],  // Cambié el índice a 5 para 'tipoProducto'
                    guiaRemision: row[12],  // Cambié el índice a 6 para 'categoria'
                    fechaRegistro: row[11],  // Cambié el índice a 7 para 'fechaVencimiento'
                    fechaVencimiento: row[7],  // Cambié el índice a 8 para 'ruc'
                    TipoProducto: row[5]
                };

                    const tr = document.createElement('tr');

                    // Crear las celdas para cada columna
                    Object.values(rowData).forEach(value => {
                        const td = document.createElement('td');
                        td.textContent = value || '-';
                        tr.appendChild(td);
                    });

                    // Columna de acciones (eliminar)
                    const tdActions = document.createElement('td');
                    const deleteBtn = document.createElement('span');
                    deleteBtn.classList.add('delete-btn');
                    deleteBtn.textContent = 'X';
                    deleteBtn.onclick = function() {
                        deletedProducts.push(rowData.codigo);
                        tr.remove();
                        console.log(deletedProducts); // Elimina la fila de la tabla
                    };
                    tdActions.appendChild(deleteBtn);
                    tr.appendChild(tdActions);

                    tableBody.appendChild(tr);
                });
                console.log("se abrirá el panel");
                // Mostrar el panel deslizante
                openPanel();
                  
            };

            // Leer el archivo como binario
            reader.readAsBinaryString(file);
        }
    }
// Función para mostrar los detalles del producto
function mostrarDetallesProducto(idProducto) {
    // Realizar la petición AJAX para obtener los detalles del producto
    $.ajax({
        url: '/obtenerDetallesProducto',
        type: 'GET',
        data: { id: idProducto },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const producto = response.producto;
                
                console.log(producto);

                // Determinar símbolo de moneda
                const simboloMoneda = producto.moneda === 'USD' ? '$' : 'S/';
                
                // Llenar los campos básicos
                $('#detalle-nombre').text(producto.nombre || 'No disponible');
                $('#detalle-marca').text(producto.marca || 'No especificada');
                $('#detalle-modelo').text(producto.modelo || 'No especificado');
                $('#detalle-codigo').text(producto.codigo || 'No disponible');
                $('#detalle-cantidad').text(producto.cantidad || '0');
                $('#detalle-categoria').text(producto.categoria || 'No disponible');
                $('#detalle-tipo').text(producto.tipo_producto || 'No disponible');
                $('#detalle-ruc').text(producto.ruc || 'No disponible');
                $('#detalle-razon-social').text(producto.razon_social || 'No disponible');
                $('#detalle-precio').text(simboloMoneda + ' ' + (producto.precio || '0.00'));
                $('#detalle-precio-venta').text(simboloMoneda + ' ' + (producto.precio_venta || '0.00'));
                
                // Mostrar descuento por cuota
                if (producto.descuento_cuota && producto.descuento_cuota > 0) {
                    $('#detalle-descuento-cuota').text(simboloMoneda + ' ' + parseFloat(producto.descuento_cuota).toFixed(2));
                } else {
                    $('#detalle-descuento-cuota').text('Sin descuento');
                }
                
                // Mostrar moneda
                $('#detalle-moneda').text(producto.moneda === 'USD' ? 'Dólares (USD)' : 'Soles (PEN)');
                
                $('#detalle-fecha-registro').text(producto.fecha_registro || 'No disponible');
                $('#detalle-fecha-vencimiento').text(producto.fecha_vencimiento || 'No disponible');
                $('#detalle-guia-remision').text(producto.guia_remision || 'No disponible');

                // Asegurar que todas las secciones ocultas se oculten al inicio
                $('#detalle-llantas-section').hide(); // Agregado para evitar que se muestren erróneamente
                $('#detalle-celular-section').hide(); // Agregado para evitar que se muestren erróneamente
                $('#detalle-chip-section').hide();
                $('#detalle-vehiculo-section').hide();

                // MODIFICACIÓN: MEJORAR DETECCIÓN DE CATEGORÍA CELULAR
                const categoriaLower = producto.categoria.toLowerCase().trim();
                const esCelular = categoriaLower.includes('celular') || categoriaLower === 'celulares';
                const esVehiculo = categoriaLower.includes('vehiculo') || categoriaLower.includes('vehículo');
                
                // Mostrar/ocultar sección de llantas
                if (producto.categoria.toLowerCase() === 'llantas') {   
                    $('#detalle-llantas-section').show();
                    $('#detalle-aro').text(producto.aro || producto.Aro || 'No disponible'); // Asegura que se tome tanto "aro" como "Aro"
                    $('#detalle-perfil').text(producto.perfil || producto.Perfil || 'No disponible'); // Asegura que se tome tanto "perfil" como "Perfil"

                } else {
                    $('#detalle-llantas-section').hide();
                }

                // MODIFICACIÓN: MEJORAR SECCIÓN DE CELULARES PARA USAR LOS DATOS DE LA TABLA CELULARES
                if (esCelular) {
                    $('#detalle-celular-section').show();
                    $('#detalle-chip-linea').text(producto.chip_linea || 'No disponible');
                    $('#detalle-marca-equipo').text(producto.marca_equipo || 'No disponible');
                    $('#detalle-modelo').text(producto.modelo || 'No disponible');
                    $('#detalle-imei').text(producto.nro_imei || 'No disponible');
                    $('#detalle-serie').text(producto.nro_serie || 'No disponible');
                    $('#detalle-color').text(producto.colorc || 'No disponible');
                    $('#detalle-cargador').text(producto.cargador || 'No disponible');
                    $('#detalle-cable-usb').text(producto.cable_usb || 'No disponible');
                    $('#detalle-manual').text(producto.manual_usuario || 'No disponible');
                    $('#detalle-estuche').text(producto.estuche || 'No disponible');
                    $('#detalle-memoria-ram').text(producto.memoria_ram || 'No disponible');
                    $('#detalle-almacenamiento').text(producto.almacenamiento || 'No disponible');
                }

                // Mostrar/ocultar sección de chip/plan móvil
                if (producto.categoria === 'Chip (Linea corporativa)') {
                    $('#detalle-chip-section').show();
                    $('#detalle-plan-mensual').text(producto.plan_mensual || 'No disponible');
                    $('#detalle-operadora').text(producto.operadora || 'No disponible');
                } else {
                    $('#detalle-chip-section').hide();
                }

                // Sección para vehículos
                if (esVehiculo && producto.caracteristicas && producto.caracteristicas.length > 0) {
                    $('#detalle-vehiculo-section').show();
                    
                    // Limpiar contenido previo
                    $('#detalle-vehiculo-caracteristicas').empty();
                    
                    // Ordenar características
                    const ordenCaracteristicas = {
                        'placa': 1,
                        'transmision': 2,
                        'kilometraje': 3,
                        'anio': 4,
                        'color': 5,
                        'fecha_venc_soat': 6,
                        'fecha_venc_seguro': 7,
                        'chasis': 8,
                        'vin': 9
                    };
                    
                    producto.caracteristicas.sort(function(a, b) {
                        const ordenA = ordenCaracteristicas[a.nombre] || 999;
                        const ordenB = ordenCaracteristicas[b.nombre] || 999;
                        return ordenA - ordenB;
                    });
                    
                    // Crear grid de características
                    const $row = $('<div class="row g-3"></div>');
                    
                    producto.caracteristicas.forEach(function(item) {
                        // Formatear nombre de característica
                        let nombreFormateado = item.nombre.replace(/_/g, ' ').replace(/\b\w/g, function(l) { 
                            return l.toUpperCase(); 
                        });
                        
                        // Nombres más amigables
                        const nombresAmigables = {
                            'placa': 'Placa',
                            'transmision': 'Transmisión',
                            'kilometraje': 'Kilometraje',
                            'fecha_venc_soat': 'Vencimiento SOAT',
                            'fecha_venc_seguro': 'Vencimiento Seguro',
                            'anio': 'Año',
                            'chasis': 'Nº de Motor',
                            'vin': 'VIN',
                            'color': 'Color'
                        };
                        
                        nombreFormateado = nombresAmigables[item.nombre] || nombreFormateado;
                        
                        // Crear columna
                        const $col = $('<div class="col-md-6"></div>');
                        $col.append($('<label class="text-muted small"></label>').text(nombreFormateado));
                        $col.append($('<p class="mb-0"></p>').text(item.valor || '-'));
                        $row.append($col);
                    });
                    
                    $('#detalle-vehiculo-caracteristicas').append($row);
                }

                // Mostrar el modal
                $('#modalDetalles').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'No se pudieron obtener los detalles del producto.',
                    confirmButtonText: 'Aceptar'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'Hubo un problema al obtener los detalles del producto.',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}
    // Función para abrir el Sliding Panel
    function openPanel() {
        const panel = document.getElementById('sliding-panel');
        panel.classList.add('open');
        console.log('Clase "open" agregada al panel:', panel.classList);
        // Verificar si el panel tiene las dimensiones correctas
        console.log('Dimensiones del panel:', panel.offsetWidth, panel.offsetHeight);
        
        // Verificar si el panel es visible en el DOM
        console.log('Panel está visible:', panel.offsetParent !== null);  // Debería ser true si es visible

        const zIndex = window.getComputedStyle(panel).zIndex;
        console.log('Z-index del panel:', zIndex);

        // Verificar si el panel está siendo cubierto por otro elemento
        const modal = document.querySelector('.modal'); // Ajusta este selector si el modal tiene otro nombre
        if (modal) {
            const modalZIndex = window.getComputedStyle(modal).zIndex;
            console.log('Z-index del modal:', modalZIndex);
        }
    }

    // Función para cerrar el Sliding Panel
    function closePanel() {
        const panel = document.getElementById('sliding-panel');
        panel.classList.remove('open');
        
    }

    function handleFileClick() {
        const fileInput = document.getElementById('file-import-excel');
        fileInput.value = ''; // Limpia el input para permitir cargar el mismo archivo
    }

    function guardarCategoryProduct() {
        const categoriaProducto = document.getElementById('categoriaProducto').value.trim(); // Obtener valor del input

        if (!categoriaProducto) {
            Swal.fire({
                icon: 'warning',
                title: 'Campo vacío',
                text: 'Por favor, ingrese una categoría de producto antes de guardar.',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        $.ajax({
            url: '/guardarCategoriaProducto', // Ruta del Ajax
            type: 'POST',
            data: { categoria_producto: categoriaProducto }, // Enviar solo el dato de la categoría
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Categoría de producto guardada correctamente.',
                        confirmButtonText: 'Aceptar'
                    });

                    document.getElementById('categoriaProducto').value = ''; // Limpiar el campo

                    // Actualizar el select con la nueva categoría
                    actualizarSelectCategoriaProducto(response.nuevaCategoriaProducto);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: response.message || 'Hubo un problema al guardar la categoría de producto.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Error al guardar la categoría de producto. Intenta nuevamente.',
                    confirmButtonText: 'Aceptar'
                });
                console.error('Detalles del error:', xhr.responseText);
            }
        });
    }

function actualizarSelectCategoriaProducto(nuevaCategoriaProducto) {
    const select = $('#categoria_producto'); // Usar jQuery

    // Verificar si la nueva categoría ya existe
    let existe = false;
    select.find('option').each(function() {
        if ($(this).val() == nuevaCategoriaProducto.idcategoria_producto) {
            existe = true;
            return false; // Salir del each
        }
    });

    // Si no existe, añadir la nueva categoría
    if (!existe) {
        const nuevaOpcion = $('<option>', {
            value: nuevaCategoriaProducto.idcategoria_producto,
            text: nuevaCategoriaProducto.nombre
        });
        select.append(nuevaOpcion);

        // Actualizar Select2 solo si está inicializado
        if (typeof $.fn.select2 !== 'undefined' && select.hasClass('select2-hidden-accessible')) {
            select.trigger('change.select2');
        }
    }
}

function cargarCategoriaProductos() {
    $.ajax({
        url: "/cargarcategoriaproductos",
        method: "GET",
        dataType: "json",
        success: function (response) {
            if (Array.isArray(response)) {
                var select = $('#categoria_producto');
                select.empty(); // Limpiar opciones anteriores

                // Agregar opción por defecto
                select.append($('<option>', {
                    value: 'seleccionar_categoría',
                    text: 'Seleccionar Categoría'
                }));

                // Agregar las categorías
                response.forEach(function(categoria) {
                    select.append($('<option>', {
                        value: categoria.idcategoria_producto,
                        text: categoria.nombre
                    }));
                });

                // Inicializar Select2 solo si está disponible
                if (typeof $.fn.select2 !== 'undefined') {
                    try {
                        select.select2({
                            placeholder: "Seleccionar Categoría",
                            allowClear: false,
                            width: '100%',
                            dropdownParent: $('#modal-add-prod') // Importante para que funcione en modales
                        });
                    } catch (error) {
                        console.warn('Select2 no se pudo inicializar:', error);
                    }
                } else {
                    console.warn('Select2 no está disponible, usando select normal');
                }
            } else {
                console.error("La respuesta no es un arreglo");
            }
        },
        error: function () {
            alert('Ocurrió un error al obtener las categorías de producto.');
        }
    });
}

    function timeActually() {
        // Crear un objeto Date con la fecha y hora actual
        var date = new Date();

        // Ajustar la fecha a la zona horaria de Perú (UTC-5)
        var peruOffset = -5; // Perú está en UTC-5
        var peruDate = new Date(date.getTime() + (peruOffset * 60 * 60 * 1000));

        // Formatear la fecha en el formato YYYY-MM-DD (requiere formato de fecha de tipo "date")
        var year = peruDate.getFullYear();
        var month = ("0" + (peruDate.getMonth() + 1)).slice(-2); // Mes con 2 dígitos
        var day = ("0" + peruDate.getDate()).slice(-2); // Día con 2 dígitos

        var formattedDate = year + "-" + month + "-" + day;

        // Establecer la fecha en el input
        document.getElementById("fechaActual").value = formattedDate;
    }

    function inicializarPlaceholderDescuento() {
        actualizarPlaceholderDescuento();
        // Establecer la oficina del modal según el filtro seleccionado
        var oficinaSeleccionada = $('#filtroOficina').val() || 1;
        $('#oficina_producto').val(oficinaSeleccionada);
    }

    function saveProductsMassive(){
        
        const fileInput = document.getElementById('file-import-excel'); // Modificación: se obtiene el archivo directamente del input
        const file = fileInput.files[0];

        const formData = new FormData();
        formData.append('file', file); // Adjuntamos el archivo Excel
        formData.append('deletedProducts', JSON.stringify(deletedProducts));
        console.log(deletedProducts);
        
        fetch('/eliminar-masivo', { // <-- Ruta al controlador
        method: 'POST',
        body: formData,
        })
        .then(response => {
            if (!response.ok) { // Verificar si la respuesta HTTP es exitosa
                throw new Error('Network response was not ok'); // Lanzar error si la respuesta no es exitosa
            }
            return response.json(); // Convertir la respuesta a JSON
        })
        .then(data => {
            console.log('Respuesta del servidor:', data); // Debug: Mostrar la respuesta del servidor en la consola
            if (data.success) {
                closePanel();
                Swal.fire({ // Mostrar mensaje de éxito con SweetAlert2
                    title: '¡Éxito!', // Título del mensaje
                    text: data.message, // Usar el mensaje del servidor
                    icon: 'success', // Icono de éxito
                    confirmButtonText: 'Aceptar' // Texto del botón
                });
                cargarProductos();
            } else {
                alert("Ocurrió un error al procesar el archivo Excel: " + data.message); // Mostrar alerta en caso de error del servidor
            }
        })
        .catch(error => {
            console.error("Error al enviar la solicitud:", error); // Debug: Mostrar el error en la consola
            alert("Hubo un problema al enviar la solicitud."); // Mostrar alerta en caso de error en la solicitud
        });
    }    

    function downloadReport() {
        // Recopilar filtros activos
        var params = new URLSearchParams();
        var oficina = $('#filtroOficina').val() || 1;
        params.set('oficina', oficina);
        params.set('filtroStock', filtroStockActual || 'todos');
        params.set('filtroTipo', $('#filtroTipoProducto').val() || 'todos');
        if (categoriasSeleccionadas.length > 0) {
            params.set('categorias', categoriasSeleccionadas.join(','));
        }
        var busqueda = $('#buscadorProductos').val().trim();
        if (busqueda) {
            params.set('busqueda', busqueda);
        }

        fetch('/downloadReport?' + params.toString(), {
            method: 'GET',
            headers: {
                'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            },
        })
        .then(response => {
        if (!response.ok) {
            throw new Error('No se pudo descargar el reporte.'); // Manejar errores HTTP
        }
        
        // Obtener el nombre del archivo directamente desde el encabezado Content-Disposition
        const contentDisposition = response.headers.get('Content-Disposition');
        const fileName = contentDisposition ? contentDisposition.split('filename=')[1].replace(/"/g, '') : 'reporte_inventario.xlsx';

        // Retornar el archivo como un blob
        return response.blob().then(blob => ({ fileName, blob })); // Retornamos tanto el nombre como el blob
        })
        .then(({ fileName, blob }) => {
            const url = window.URL.createObjectURL(blob); // Crear una URL para el archivo Blob
            
            // Crear un enlace de descarga y simular el clic
            const a = document.createElement('a');
            a.href = url;
            a.download = fileName; // Asignar el nombre del archivo
            document.body.appendChild(a); // Agregar el enlace al DOM
            a.click(); // Simular el clic para iniciar la descarga
            a.remove(); // Eliminar el enlace temporal
            window.URL.revokeObjectURL(url); // Liberar el objeto URL
        })
        .catch(error => {
            console.error('Error al descargar el reporte:', error); // Mostrar el error en la consola
            alert('Ocurrió un error al intentar descargar el reporte.'); // Mostrar alerta en caso de error
        });
    }

        function buscadorProductos() {
            let query = $("#buscadorProductos").val().trim();

            if (query === "") {
                cargarProductos(); // Si está vacío, carga todos los productos nuevamente
                return;
            }

            let url = "/buscarAlmacen?search=" + encodeURIComponent(query); // Se concatena manualmente el parámetro de búsqueda

            $.ajax({
                url: url, // Ruta al controlador que maneja la búsqueda
                type: "GET",
                dataType: "json",
                success: function(productos) {
                    var tbody = $('#tbodyProductos');
                    tbody.empty(); // Vaciar la tabla antes de agregar los productos filtrados
                    $.each(productos, function(i, producto) {
                        tbody.append(`
                            <tr>
                                <td>${producto.idproductosv2}</td>
                                <td>
                                    <a href="#" onclick="event.preventDefault(); mostrarCodigoBarras(
                                        '${producto.idproductosv2}', // Pasar el ID del producto
                                        '${producto.nombre}', // Pasar el nombre como parámetro
                                        '${producto.codigo}', // Pasar el código de barras como parámetro
                                        '${producto.precio}'  // Pasar el precio como parámetro
                                    )">${producto.nombre}</a> 
                                </td>
                                <td>${producto.codigo}</td>
                                <td>${producto.cantidad}</td>
                                <td>${producto.categoria}</td>
                                <td>${producto.ruc}</td>
                                <td>${producto.razon_social}</td>
                                <td>${producto.fecha_vencimiento ? producto.fecha_vencimiento : 'No disponible'}</td>
                                <td>${producto.tipo_producto}</td>
                                <td><button class="btn btn-sm btn-primary editar-producto" data-id="${producto.idproductosv2}" onclick="redirigirEditar(${producto.idproductosv2})">Editar</button></td>
                                <td><input type="checkbox" class="eliminar-producto" data-id="${producto.idproductosv2}"></td>
                                <td><button class="btn btn-sm btn-info" onclick="mostrarDetallesProducto(${producto.idproductosv2})">Detalles</button></td>
                            </tr>
                        `);
                    });
                    toggleSeleccionarTodos();
                },
                error: function(xhr, status, error) {
                    console.error("Error en la búsqueda:", error);
                    alert("Hubo un error en la búsqueda. Inténtalo de nuevo.");
                }
            });
        }

        function mostrarCodigoBarras(idproductosv2, nombre, codigo, precio) {
            if (!codigo || codigo === "null") { // Si el código es null o vacío, obtenerlo mediante AJAX
                $.ajax({
                    url: "/getBarCode",
                    type: "GET",
                    data: { id_producto: idproductosv2 },
                    dataType: "json",
                    success: function(response) {
                        if (response.codigo) {
                            obtenerImagenCodigoBarras(response.codigo, nombre, precio);
                        } else {
                            alert("No se pudo obtener el código de barras.");
                        }
                    },
                    error: function() {
                        alert("Error al obtener el código de barras.");
                    }
                });
            } else {
                obtenerImagenCodigoBarras(codigo, nombre, precio); // Si el código existe, usarlo directamente
            }
        }

        function obtenerImagenCodigoBarras(codigo, nombre, precio) {
            $.ajax({
                url: "/generateBarcode",
                type: "GET",
                data: { codigo: codigo },
                dataType: "json",
                success: function(response) {
                    if (response.image) {
                        $("#idCodigoBarras").attr("src", response.image); // Setear imagen en el modal
                        $("#codigoProducto").text("Código: " + codigo); // Agregar código debajo de la imagen
                        $(".modal-backdrop").remove(); 
                        $("#modalCodigoBarras").modal("show"); // Mostrar el modal
                    } else {
                        alert("No se pudo generar la imagen del código de barras.");
                    }
                },
                error: function() {
                    alert("Error al generar la imagen del código de barras.");
                }
            });
        }

        function toggleSeleccionarTodos() {
            let isChecked = $(".btnSeleccionarTodos").prop("checked"); // Obtener el estado del checkbox de la cabecera
            $(".eliminar-producto").prop("checked", isChecked); // Marcar o desmarcar todos los checkboxes de los productos
        }

        function deleteProducto() {
            let idsProductos = [];

            // Recorre todos los checkboxes seleccionados y obtiene los IDs
            $(".eliminar-producto:checked").each(function () {
                idsProductos.push($(this).data("id"));
            });

            // Verifica si hay productos seleccionados
            if (idsProductos.length === 0) {
                Swal.fire({
                    icon: "warning",
                    title: "Atención",
                    text: "Selecciona al menos un producto para eliminar.",
                    confirmButtonText: "Aceptar"
                });
                return;
            }

            // Confirmación antes de eliminar
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Esta acción eliminará los productos seleccionados.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar los IDs mediante AJAX
                    $.ajax({
                        url: "/deleteProducts",
                        type: "POST",
                        data: JSON.stringify({ ids: idsProductos }),
                        contentType: "application/json",
                        dataType: "json",
                        success: function (response) {
                            Swal.fire({
                                icon: "success",
                                title: "Eliminado",
                                text: "Los productos han sido eliminados correctamente.",
                                confirmButtonText: "Aceptar"
                            }).then(() => {
                                cargarProductos(); // Recargar la tabla después de eliminar
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error("Error al eliminar productos:", error);
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "Hubo un error al eliminar los productos. Inténtalo de nuevo.",
                                confirmButtonText: "Aceptar"
                            });
                        }
                    });
                }
            });
        }

        function redirigirEditar(id_producto) { // Nueva función agregada
            window.location.href = '/editar-producto?id=' + id_producto; // Redirigir a la página de edición
        }

        function actualizarPlaceholderDescuento() {
                const moneda = document.getElementById('moneda').value;
                const descuentoCuotaInput = document.getElementById('descuentoCuota');
                
                if (moneda === 'S/.') {
                    descuentoCuotaInput.placeholder = 'Ej: 0.50 (opcional)';
                } else if (moneda === '$') {
                    descuentoCuotaInput.placeholder = 'Ej: 0.20 (opcional)';
                }
            }
           
    var nombreBarraTemps=''
    var codeBarraTemps=''
    var datatable
    
    // ========== VARIABLES Y FUNCIONES GLOBALES PARA FILTROS ==========
    // Variable global para almacenar todos los productos
    var todosLosProductos = [];
    var categoriasSeleccionadas = []; // Array para almacenar categorías seleccionadas
    var todasLasCategorias = []; // Array para almacenar todas las categorías disponibles
    var filtroStockActual = 'todos'; // Variable para almacenar el filtro de stock actual

    // Función global para cambiar filtro de stock (botones)
    function cambiarFiltroStock(valor, boton) {
        // Actualizar variable global
        filtroStockActual = valor;
        
        // Remover clase active de todos los botones
        document.querySelectorAll('.btn-stock-filter').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Agregar clase active al botón clickeado
        boton.classList.add('active');
        
        // Mostrar/ocultar botón limpiar
        actualizarBotonLimpiar();
        
        // Aplicar filtros
        aplicarFiltros();
    }

    // Función para mostrar/ocultar botón "Limpiar"
    function actualizarBotonLimpiar() {
        const btnLimpiar = document.getElementById('btnLimpiarFiltros');
        const hayFiltrosActivos = 
            filtroStockActual !== 'todos' || 
            $('#filtroTipoProducto').val() !== 'todos' ||
            categoriasSeleccionadas.length > 0 ||
            $('#buscadorProductos').val().trim() !== '';
        
        if (hayFiltrosActivos) {
            btnLimpiar.style.display = 'inline-block';
        } else {
            btnLimpiar.style.display = 'none';
        }
    }

    // Función global para aplicar filtros
    function aplicarFiltros() {
        const filtroStock = filtroStockActual; // Usar variable global en lugar de select
        const filtroTipo = $('#filtroTipoProducto').val();
        const busqueda = $('#buscadorProductos').val().toLowerCase().trim();

        let productosFiltrados = todosLosProductos;

        // Filtrar por búsqueda de texto
        if (busqueda !== '') {
            productosFiltrados = productosFiltrados.filter(function(producto) {
                return (
                    (producto.nombre && producto.nombre.toLowerCase().includes(busqueda)) ||
                    (producto.codigo && producto.codigo.toLowerCase().includes(busqueda)) ||
                    (producto.razon_social && producto.razon_social.toLowerCase().includes(busqueda)) ||
                    (producto.categoria && producto.categoria.toLowerCase().includes(busqueda)) ||
                    (producto.tipo_producto && producto.tipo_producto.toLowerCase().includes(busqueda))
                );
            });
        }

        // Filtrar por stock
        if (filtroStock !== 'todos') {
            productosFiltrados = productosFiltrados.filter(function(producto) {
                const cantidad = parseInt(producto.cantidad) || 0;
                
                switch(filtroStock) {
                    case 'con_stock':
                        return cantidad > 0;
                    case 'sin_stock':
                        return cantidad === 0;
                    case 'stock_bajo':
                        return cantidad > 0 && cantidad < 10;
                    default:
                        return true;
                }
            });
        }

        // Filtrar por tipo de producto
        if (filtroTipo !== 'todos') {
            productosFiltrados = productosFiltrados.filter(function(producto) {
                return producto.tipo_producto === filtroTipo;
            });
        }

        // Filtrar por categorías (múltiples selecciones)
        if (categoriasSeleccionadas.length > 0) {
            productosFiltrados = productosFiltrados.filter(function(producto) {
                return categoriasSeleccionadas.includes(producto.categoria);
            });
        }

        mostrarProductos(productosFiltrados);
    }

    // Función global para limpiar filtros
    function limpiarFiltros() {
        // Resetear filtro de stock a "Todos"
        filtroStockActual = 'todos';
        document.querySelectorAll('.btn-stock-filter').forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('data-value') === 'todos') {
                btn.classList.add('active');
            }
        });
        
        // Resetear otros filtros
        $('#filtroTipoProducto').val('todos');
        $('#buscadorProductos').val('');
        limpiarCategoriasSeleccionadas();
        
        // Ocultar botón limpiar
        actualizarBotonLimpiar();
        
        mostrarProductos(todosLosProductos);
    }

    // ========== FUNCIONES PARA DROPDOWN PERSONALIZADO DE CATEGORÍAS ==========
    
    // Toggle del dropdown de categorías
    function toggleCategoriaDropdown() {
        const menu = document.getElementById('categoriaDropdownMenu');
        const btn = document.getElementById('btnCategoriaDropdown');
        const isOpen = menu.classList.contains('show');
        
        if (isOpen) {
            menu.classList.remove('show');
            btn.classList.remove('active');
        } else {
            menu.classList.add('show');
            btn.classList.add('active');
        }
    }

    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', function(event) {
        const wrapper = document.querySelector('.dropdown-categoria-wrapper');
        const menu = document.getElementById('categoriaDropdownMenu');
        const btn = document.getElementById('btnCategoriaDropdown');
        
        if (wrapper && !wrapper.contains(event.target)) {
            menu.classList.remove('show');
            btn.classList.remove('active');
        }
    });

    // Actualizar texto del botón con categorías seleccionadas
    function actualizarTextoCategoriasSeleccionadas() {
        const texto = document.getElementById('categoriasSeleccionadasText');
        const contador = document.getElementById('contadorCategorias');
        
        if (categoriasSeleccionadas.length === 0) {
            texto.textContent = 'Todas las categorías';
            contador.textContent = '';
            contador.style.display = 'none';
        } else if (categoriasSeleccionadas.length === 1) {
            texto.textContent = categoriasSeleccionadas[0];
            contador.textContent = '1';
            contador.style.display = 'inline-block';
            contador.classList.add('updated');
            setTimeout(() => contador.classList.remove('updated'), 300);
        } else {
            texto.textContent = `Categoría (${categoriasSeleccionadas.length})`;
            contador.textContent = categoriasSeleccionadas.length;
            contador.style.display = 'inline-block';
            contador.classList.add('updated');
            setTimeout(() => contador.classList.remove('updated'), 300);
        }
    }

    // Manejar selección de categoría
    function toggleCategoria(categoria, checkbox) {
        const index = categoriasSeleccionadas.indexOf(categoria);
        
        if (checkbox.checked && index === -1) {
            categoriasSeleccionadas.push(categoria);
        } else if (!checkbox.checked && index > -1) {
            categoriasSeleccionadas.splice(index, 1);
        }
        
        actualizarTextoCategoriasSeleccionadas();
        actualizarBotonLimpiar();
        aplicarFiltros();
    }

    // Limpiar categorías seleccionadas
    function limpiarCategoriasSeleccionadas() {
        categoriasSeleccionadas = [];
        
        // Desmarcar todos los checkboxes
        const checkboxes = document.querySelectorAll('.categoria-item input[type="checkbox"]');
        checkboxes.forEach(cb => {
            cb.checked = false;
            cb.closest('.categoria-item').classList.remove('selected');
        });
        
        actualizarTextoCategoriasSeleccionadas();
        actualizarBotonLimpiar();
        aplicarFiltros();
    }

    // Buscar categorías en el dropdown
    function buscarEnCategorias() {
        const busqueda = document.getElementById('buscarCategoria').value.toLowerCase();
        const items = document.querySelectorAll('.categoria-item');
        let hayResultados = false;
        
        items.forEach(item => {
            const texto = item.querySelector('label').textContent.toLowerCase();
            if (texto.includes(busqueda)) {
                item.style.display = 'flex';
                hayResultados = true;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Mostrar mensaje si no hay resultados
        const lista = document.getElementById('listaCategorias');
        let mensajeNoResultados = lista.querySelector('.no-categorias-found');
        
        if (!hayResultados) {
            if (!mensajeNoResultados) {
                mensajeNoResultados = document.createElement('div');
                mensajeNoResultados.className = 'no-categorias-found';
                mensajeNoResultados.textContent = 'No se encontraron categorías';
                lista.appendChild(mensajeNoResultados);
            }
        } else {
            if (mensajeNoResultados) {
                mensajeNoResultados.remove();
            }
        }
    }

    // Función global para mostrar productos en la tabla
    function mostrarProductos(productos) {
        var tbody = $('#tbodyProductos');
        tbody.empty();
        
        if (productos.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="12" class="text-center">No se encontraron productos con los filtros seleccionados</td>
                </tr>
            `);
            return;
        }
        
        $.each(productos, function(i, producto) {
            tbody.append(`
                <tr>
                    <td>${producto.idproductosv2}</td>
                    <td>
                        <a href="#" onclick="event.preventDefault(); mostrarCodigoBarras(
                            '${producto.idproductosv2}',
                            '${producto.nombre}',
                            '${producto.codigo}',
                            '${producto.precio}'
                        )">${producto.nombre}</a>
                    </td>
                    <td>${producto.codigo}</td>
                    <td>${producto.cantidad}</td>
                    <td>${producto.categoria}</td>
                    <td>${producto.ruc}</td>
                    <td>${producto.razon_social}</td>
                    <td>${producto.fecha_vencimiento ? producto.fecha_vencimiento : 'No disponible'}</td>
                    <td>${producto.tipo_producto}</td>
                    <td><button class="btn btn-sm btn-primary editar-producto" data-id="${producto.idproductosv2}" onclick="redirigirEditar(${producto.idproductosv2})">Editar</button></td>
                    <td><input type="checkbox" class="eliminar-producto" data-id="${producto.idproductosv2}"></td>
                    <td><button class="btn btn-sm btn-info" onclick="mostrarDetallesProducto(${producto.idproductosv2})">Detalles</button></td>
                </tr>
            `);
        });
        toggleSeleccionarTodos();
    }
    
    $(document).ready(function() {

        cargarTiposProducto();
        cargarCategoriaProductos() 
        cargarProductos();
        timeActually();
        toggleSeleccionarTodos();

        $(document).on("change", ".btnSeleccionarTodos", function () { // <-- Evento global para que funcione siempre
            let isChecked = $(this).prop("checked"); // Obtener si está marcado o no
            $(".eliminar-producto").prop("checked", isChecked); // Marcar o desmarcar todos los checkboxes de los productos
        });

        $("#buscadorProductos").on("keyup", buscadorProductos); // Evento al escribir en la barra de búsqueda

        // 🔹 VALIDACIÓN DE ESPACIOS EN CÓDIGO
        $("#codigo_producto").on("input", function() {
            const codigo = $(this).val();
            const mensajeDuplicado = $("#mensaje_duplicado_codigo");

            // Verificar si hay espacios en el código
            if (codigo.includes(" ")) {
                mensajeDuplicado.text("⚠ EL CÓDIGO NO PUEDE CONTENER ESPACIOS");
                mensajeDuplicado.css("color", "#dc3545"); // Color rojo
                mensajeDuplicado.show();
                return;
            } else {
                // Si no hay espacios y el mensaje era de espacios, ocultarlo
                if (mensajeDuplicado.text().includes("ESPACIOS")) {
                    mensajeDuplicado.hide();
                }
            }
        });

        // 🔹 VALIDACIÓN DE CÓDIGO DUPLICADO
        $("#codigo_producto").on("blur", function() {
            const codigo = $(this).val().trim();
            const mensajeDuplicado = $("#mensaje_duplicado_codigo");

            if (codigo === "") {
                mensajeDuplicado.hide();
                return;
            }

            // Verificar si hay espacios en el código (doble verificación)
            if (codigo.includes(" ")) {
                mensajeDuplicado.text("⚠ EL CÓDIGO NO PUEDE CONTENER ESPACIOS");
                mensajeDuplicado.css("color", "#dc3545");
                mensajeDuplicado.show();
                return;
            }

            // Hacer petición AJAX para verificar si el código ya existe
            $.ajax({
                url: "/verificar-codigo-duplicado",
                type: "POST",
                data: { codigo: codigo },
                success: function(response) {
                    if (response.existe && response.productos.length > 0) {
                        // Construir lista de productos duplicados
                        let listaProductos = "";
                        response.productos.forEach(function(prod) {
                            listaProductos += "• ID: " + prod.idproductosv2 + " - " + prod.nombre + " (Stock: " + prod.cantidad + ")<br>";
                        });

                        // Mostrar mensaje en etiqueta roja
                        mensajeDuplicado.text("⚠ CÓDIGO DUPLICADO en " + response.productos.length + " producto(s)");
                        mensajeDuplicado.show();

                        // Mostrar alerta visual con detalles
                        Swal.fire({
                            icon: 'warning',
                            title: 'Código Duplicado',
                            html: "<strong>Este código ya existe en " + response.productos.length + " producto(s):</strong><br><br>" + listaProductos,
                            confirmButtonText: 'Entendido'
                        });
                    } else {
                        mensajeDuplicado.hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error al verificar código:", error);
                }
            });
        });

        // Asegúrate de eliminar cualquier manejador previo del evento 'change' para evitar duplicados

        // ========== FUNCIONES AUXILIARES PARA FILTROS (dentro de document.ready) ==========
        
        // Función para cargar los tipos de producto en el filtro
        function cargarTiposProductoFiltro() {
            $.ajax({
                url: "/cargartiposproducto",
                method: "GET",
                dataType: "json",
                success: function (response) {
                    if (Array.isArray(response)) {
                        var select = $('#filtroTipoProducto');
                        select.empty();
                        select.append($('<option>', {
                            value: 'todos',
                            text: 'Todos los tipos'
                        }));
                        
                        response.forEach(function(tipo) {
                            select.append($('<option>', {
                                value: tipo.tipo_productocol,
                                text: tipo.tipo_productocol
                            }));
                        });
                    }
                },
                error: function () {
                    console.error('Error al cargar tipos de producto para filtro');
                }
            });
        }

        // Función para cargar las categorías en el dropdown personalizado
        function cargarCategoriasFiltro() {
            $.ajax({
                url: "/cargarcategoriaproductos",
                method: "GET",
                dataType: "json",
                success: function (response) {
                    if (Array.isArray(response)) {
                        todasLasCategorias = response; // Guardar en variable global
                        const lista = $('#listaCategorias');
                        lista.empty();
                        
                        response.forEach(function(categoria) {
                            const item = $(`
                                <div class="categoria-item">
                                    <input type="checkbox" 
                                           id="cat_${categoria.idcategoria_producto}" 
                                           value="${categoria.nombre}"
                                           onchange="toggleCategoria('${categoria.nombre}', this)">
                                    <label for="cat_${categoria.idcategoria_producto}">${categoria.nombre}</label>
                                </div>
                            `);
                            
                            // Agregar evento click al item completo
                            item.on('click', function(e) {
                                if (e.target.tagName !== 'INPUT') {
                                    const checkbox = $(this).find('input[type="checkbox"]');
                                    checkbox.prop('checked', !checkbox.prop('checked'));
                                    toggleCategoria(categoria.nombre, checkbox[0]);
                                }
                                
                                // Toggle clase selected
                                if ($(this).find('input[type="checkbox"]').prop('checked')) {
                                    $(this).addClass('selected');
                                } else {
                                    $(this).removeClass('selected');
                                }
                            });
                            
                            lista.append(item);
                        });
                        
                        // Agregar evento de búsqueda
                        $('#buscarCategoria').off('keyup').on('keyup', buscarEnCategorias);
                    }
                },
                error: function () {
                    console.error('Error al cargar categorías para filtro');
                }
            });
        }

        // Función mejorada para cargar productos y almacenarlos (usa variable global)
        function cargarProductos() {
            // Obtener la oficina seleccionada del filtro
            var oficinaSeleccionada = $('#filtroOficina').val() || 1;

            $.ajax({
                url: '/obtenerTodosProductos',
                type: 'GET',
                data: { oficina: oficinaSeleccionada },
                dataType: 'json',
                success: function(productos) {
                    todosLosProductos = productos; // Guardar en variable global
                    mostrarProductos(productos); // Mostrar todos inicialmente
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar los productos:", error);
                    alert("Hubo un error al cargar los productos. Por favor, intenta de nuevo más tarde.");
                }
            });
        }


        // Modificar el buscador para que use los filtros
        $('#buscadorProductos').on('keyup', function() {
            actualizarBotonLimpiar();
            aplicarFiltros();
        });

        // Inicializar filtros al cargar la página
        cargarTiposProductoFiltro();
        cargarCategoriasFiltro();

    })
</script>
