<?php

// Obtiene la URI actual
$currentUri = $_SERVER['REQUEST_URI'];

// Verifica si la URL contiene "/arequipago/"
if (strpos($currentUri, '/arequipago/') === 0) {
    // Obtiene el mÃ©todo de la solicitud
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Elimina el prefijo '/arequipago' manteniendo el resto de la ruta
    $newPath = substr($currentUri, strlen('/arequipago'));
    
    // Construye la nueva URL
    $newUrl = 'https://arequipago-ventas.pe' . $newPath;
    
    // Para solicitudes POST
    if ($method === 'POST') {
        // Configura los headers para mantener el mÃ©todo POST
        header('HTTP/1.1 307 Temporary Redirect');
        header('Location: ' . $newUrl);
    } else {
        // Para solicitudes GET
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $newUrl);
    }
    
    // Asegura que la redirecciÃ³n se ejecute
    exit();
}

// Si no hay redirecciÃ³n, continÃºa con el procesamiento normal
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Maneja las solicitudes GET aquÃ­
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Maneja las solicitudes POST aquÃ­
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

// Rutas básicas de cupones
Route::post('/ajs/cupones/crear', 'CuponController@crearCupon'); // Para crear un nuevo cupón
Route::get('/ajs/cupones/listar', 'CuponController@listarCupones'); // Para listar todos los cupones
Route::get('/ajs/cupones/listar-con-conductores', 'CuponController@listarCuponesConConductores'); // Para listar todos los cupones y los conductores asociados
Route::post('/ajs/cupones/buscar/usuarios', 'CuponController@buscarUsuarios');
Route::post('/ajs/cupones/usuarios', 'CuponController@obtenerUsuariosCupon');
// Mantener compatibilidad hacia atrás
Route::post('/ajs/cupones/buscar/conductores', 'CuponController@buscarConductores'); // Para buscar conductores
Route::post('/ajs/cupones/conductores', 'CuponController@obtenerConductoresCupon'); // Para obtener los conductores asociados a un cupón
// Rutas de verificación
Route::post('/ajs/cupones/verificar/conductores', 'CuponController@verificarConductoresConCupones'); // cupones de un conductor en especifico
Route::get('/ajs/cupones/verificar/conductor/:id_conductor', 'CuponController@verificarCuponConductor'); // verifica si ese conductor tiene cupones
// Rutas de estadísticas y uso
Route::get('/ajs/cupones/estadisticas', 'CuponController@obtenerEstadisticasUso'); // Para obtener estadísticas de uso de todos los cupones
Route::get('/ajs/cupones/estadisticas/:id', 'CuponController@obtenerEstadisticasUso'); // Para obtener estadísticas de uso de un cupón específico
Route::post('/ajs/cupones/registrar-uso', 'CuponController@registrarUsoCupon'); // Para registrar el uso de un cupón
// Ruta para verificar uso específico
Route::get('/ajs/cupones/verificar/uso/:tipo/:id_usuario/:id_cupon', 'CuponController@verificarUsoCupon'); // Para verificar si un usuario (conductor/cliente) ya usó un cupón específico
// Route::post('/ajs/cupones/verificar/uso', 'CuponController@verificarUsoCupon');
Route::post('/ajs/cupones/usar-codigo/:tipo/:id_usuario/:id_cupon', 'CuponController@usarCuponPorCodigo'); // registrar cupon con el codigo por params
// Route::post('/ajs/cupones/usar-codigo/:id_conductor/:id_cupon', 'CuponController@usarCuponPorCodigo'); // registrar cupon con el codigo por params

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

Route::get('/ajs/resumen-crediticio/:tipo/:id', 'PuntajeCrediticioController@obtenerResumenCrediticio');
