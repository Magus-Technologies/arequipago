<?php

// Obtiene la URI actual
$currentUri = $_SERVER['REQUEST_URI'];

// Verifica si la URL contiene "/arequipago/"
if (strpos($currentUri, '/arequipago/') === 0) {
    // Obtiene el método de la solicitud
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Elimina el prefijo '/arequipago' manteniendo el resto de la ruta
    $newPath = substr($currentUri, strlen('/arequipago'));
    
    // Construye la nueva URL
    $newUrl = 'https://arequipago-ventas.pe' . $newPath;
    
    // Para solicitudes POST
    if ($method === 'POST') {
        // Configura los headers para mantener el método POST
        header('HTTP/1.1 307 Temporary Redirect');
        header('Location: ' . $newUrl);
    } else {
        // Para solicitudes GET
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $newUrl);
    }
    
    // Asegura que la redirección se ejecute
    exit();
}

// Si no hay redirección, continúa con el procesamiento normal
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Maneja las solicitudes GET aquí
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Maneja las solicitudes POST aquí
}


Route::post('/ajs/generar/txt/ventareporte',"GeneradoresController@generarTextLibroVentas")->Middleware([ValidarTokenMiddleware::class]);

Route::get('/ajs/ventas',"VentasController@listarVentas")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/ventas/add',"VentasController@guardarVentas")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/ingreso/almacen/add',"VentasController@ingresoAlmacen")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/egreso/almacen/add',"VentasController@egresoAlmacen")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/ventas/servicios/edit',"VentasController@editVentaServicio")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/ventas/productos/edit',"VentasController@editVentaProducto")->Middleware([ValidarTokenMiddleware::class]);
Route::get('/ajs/cargar/productos/:id',"ConsultasController@buscarProducto")->Middleware([ValidarTokenMiddleware::class]);
Route::get('/ajs/cargar/productos',"ConsultasController@buscarProductoCoti")->Middleware([ValidarTokenMiddleware::class]);
Route::get('/conductor', 'ListarConductoresController@listarConductores');
Route::post('/conductor/eliminar/{id}','ListarConductoresController@eliminarConductor'); 
Route::post('/conductor-buscar', 'ListarConductoresController@buscarConductores');
Route::post('/listarConductoresPorFecha', 'ListarConductoresController@listarConductoresPorFecha');
Route::post('/toggleDesvincularConductor', 'ConductorController@toggleDesvincularConductor');




/* Route::post('/ajs/cargar/productos/precios',"ConsultasController@cargarPreciosProd")->Middleware([ValidarTokenMiddleware::class]); */

Route::post('/ajs/cargar/venta/servicios',"ConsultasController@cargarVentaServicios")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/cargar/venta/productos',"ConsultasController@cargarVentaProductos")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/cargar/venta/info',"ConsultasController@cargarVentaDetalles")->Middleware([ValidarTokenMiddleware::class]);

Route::post('/login',"UsuarioController@login")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/sn","ConsultasController@buscarSNdoc")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/ruc","ConsultasController@consultaRuc")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/venta/detalle","VentasController@detalleVenta")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/venta/consultas/tipo/venta","VentasController@tipoVenta")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/venta/anular","VentasController@anularVenta")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/lista/provincias","ConsultasController@listarProvincias")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/lista/distrito","ConsultasController@listarDistri")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/guia/documentofb","ConsultasController@consultvfb")->Middleware([ValidarTokenMiddleware::class]);


Route::post("/ajs/guia/remision/coti/:id","ConsultasController@consultarGuiaXCoti")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/guia/remision/coti/cliente/:id","ConsultasController@consultarGuiaXCotiCliente")->Middleware([ValidarTokenMiddleware::class]);

Route::post('/ajs/guia/remision/add',"GuiaRemisionController@insertar")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/guia/remision/add2',"GuiaRemisionController@insertar2")->Middleware([ValidarTokenMiddleware::class]);

//CRUD AJAX PARA CLIENTES
Route::post("/ajs/clientes/add","ClientesController@insertar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/clientes/add/por/lista","ClientesController@insertarXLista")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/clientes/render","ClientesController@render")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/clientes/getOne","ClientesController@getOne")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/clientes/editar","ClientesController@editar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/clientes/borrar","ClientesController@borrar")->Middleware([ValidarTokenMiddleware::class]);
/* Route::post("/ajs/clientes/importAdd","ClientesController@importAdd"); */

Route::post("/ajs/usuarios/render","UsuariosController@render")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/usuarios/getOne","UsuariosController@getOne")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/usuarios/editar","UsuariosController@editar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/usuarios/borrar","UsuariosController@borrar")->Middleware([ValidarTokenMiddleware::class]);


Route::post('/ajs/consulta/doc/cliente',"ConsultasController@buscarDocInfo")->Middleware([ValidarTokenMiddleware::class]);

Route::get('/ajs/consulta/buscar/dtatranspor',"ConsultasController@buscarTransporteGui")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/consulta/add/dtatranspor',"ConsultasController@agregarTransportista")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/consulta/prod/coti',"ConsultasController@buscarProdId")->Middleware([ValidarTokenMiddleware::class]);

Route::get('/ajs/buscar/cliente/datos',"ConsultasController@buscarDataCliente")->Middleware([ValidarTokenMiddleware::class]);

//importal excel
Route::post("/ajs/clientes/add/exel","ClientesController@importarExcel")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/cuentas/cobrar","ClientesController@cuentasCobrar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/cuentas/cobrar/estado","ClientesController@cuentasCobrarEstado")->Middleware([ValidarTokenMiddleware::class]);

Route::post("/ajs/registrar/conductor","RegistrarConductorController@registrarTodo")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/actualizar/conductor","EditarConductorController@registrarTodo")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/actualizar/conductorofasesor","EditarConductorController@registrarTodoAsesor")->Middleware([ValidarTokenMiddleware::class]);

Route::post("/ajs/ingresos/egresos/render","VentasController@ingresosEgresosRender")->Middleware([ValidarTokenMiddleware::class]);


Route::get("/ajs/server/sider/productos","ProductosController@listaProductoServerSide");

// API DNI

Route::post('/ajs/conductor/doc/cliente',"ConductorController@buscarDocInfo")->Middleware([ValidarTokenMiddleware::class]);
// Cambiar esta ruta de POST a GET
Route::get('/ajs/obtenerFinanciamientoParaEditar', 'FinanciamientoController@obtenerFinanciamientoParaEditar');

// Cambiar esta ruta de GET a POST
Route::post('/ajs/actualizarFinanciamiento', 'FinanciamientoController@actualizarFinanciamiento');

// Agrega esta ruta junto con las otras rutas AJAX
//$router->post('/ajs/buscar/doc/infoGuia', 'GuiaRemisionController@buscarDocInfo');
Route::post('/ajs/buscar/doc/infoGuia', 'GuiaRemisionController@buscarDocInfo');