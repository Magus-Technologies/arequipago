<?php




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

// Rutas de edición y eliminación de cupones
Route::post('/ajs/cupones/obtener', 'CuponController@obtenerCupon'); // Para obtener un cupón específico para edición
Route::post('/ajs/cupones/actualizar', 'CuponController@actualizarCupon'); // Para actualizar un cupón existente
Route::post('/ajs/cupones/eliminar', 'CuponController@eliminarCupon'); // Para eliminar un cupón (soft delete)
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


// ============ RUTAS DE BENEFICIOS ============
// Rutas para gestión de beneficios (catálogo de productos financiados)

// Listar y buscar beneficios
Route::get('/ajs/beneficios/listar', 'BeneficioController@listarBeneficios'); // Obtener todos los beneficios con filtros
Route::post('/ajs/beneficios/buscar', 'BeneficioController@buscarBeneficios'); // Buscar beneficios por término y categoría

// CRUD de beneficios
Route::post('/ajs/beneficios/crear', 'BeneficioController@crearBeneficio'); // Crear nuevo beneficio
Route::post('/ajs/beneficios/obtener', 'BeneficioController@obtenerBeneficio'); // Obtener beneficio por ID
Route::get('/ajs/beneficios/obtener/:id', 'BeneficioController@obtenerBeneficio'); // Obtener beneficio por ID (GET)
Route::post('/ajs/beneficios/actualizar', 'BeneficioController@actualizarBeneficio'); // Actualizar beneficio existente
Route::post('/ajs/beneficios/eliminar', 'BeneficioController@eliminarBeneficio'); // Eliminar beneficio (soft delete)

// Gestión de disponibilidad
Route::post('/ajs/beneficios/cambiar-disponibilidad', 'BeneficioController@cambiarDisponibilidad'); // Cambiar disponibilidad de un beneficio

// Estadísticas y categorías
Route::get('/ajs/beneficios/estadisticas', 'BeneficioController@obtenerEstadisticas'); // Estadísticas generales de beneficios
Route::get('/ajs/beneficios/categorias', 'BeneficioController@obtenerCategorias'); // Obtener categorías disponibles

// Solicitudes de beneficios (para futuras funcionalidades)
Route::post('/ajs/beneficios/solicitar', 'BeneficioController@solicitarBeneficio'); // Solicitar un beneficio (placeholder)


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

// RUTAS DE 

Route::post('/ajs/conductor/doc/cliente',"ConductorController@buscarDocInfo")->Middleware([ValidarTokenMiddleware::class]);
// Cambiar esta ruta de POST a GET
Route::get('/ajs/obtenerFinanciamientoParaEditar', 'FinanciamientoController@obtenerFinanciamientoParaEditar');

// Cambiar esta ruta de GET a POST
Route::post('/ajs/actualizarFinanciamiento', 'FinanciamientoController@actualizarFinanciamiento');

// Agrega esta ruta junto con las otras rutas AJAX
//$router->post('/ajs/buscar/doc/infoGuia', 'GuiaRemisionController@buscarDocInfo');
Route::post('/ajs/buscar/doc/infoGuia', 'GuiaRemisionController@buscarDocInfo');

Route::get('/ajs/resumen-crediticio/:tipo/:id', 'PuntajeCrediticioController@obtenerResumenCrediticio');
