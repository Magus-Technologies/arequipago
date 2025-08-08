<?php

// Verifica si la URL contiene "/arequipago/"
if (strpos($_SERVER['REQUEST_URI'], '/arequipago/') === 0) {
    // Extrae la ruta después de "/arequipago/"
    $newPath = substr($_SERVER['REQUEST_URI'], strlen('/arequipago'));
    
    // Construye la nueva URL
    $newUrl = 'https://arequipago-ventas.pe' . $newPath;
    
    // Preserva el método HTTP original
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Inicia la salida del buffer
    ob_start();
    
    // Genera un formulario oculto para preservar el método y los datos POST
    echo "<html><body><form id='redirect_form' method='$method' action='$newUrl'>";
    foreach ($_POST as $key => $value) {
        echo "<input type='hidden' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($value) . "'>";
    }
    echo "</form>";
    echo "<script>document.getElementById('redirect_form').submit();</script></body></html>";
    
    // Envía los headers de redirección
    header("HTTP/1.1 307 Temporary Redirect");
    header("Location: $newUrl");
    
    // Envía el contenido del buffer y termina el script
    ob_end_flush();
    exit;
}

// Si no hay redirección, continúa con el procesamiento normal de la solicitud
// Aquí puedes agregar tu lógica para manejar las rutas específicas
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($_SERVER['REQUEST_URI'] === '/login') {
        // Lógica para la vista de login
    } elseif ($_SERVER['REQUEST_URI'] === '/logout') {
        // Lógica para el logout
    }
    // Agrega más rutas GET según sea necesario
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Maneja las solicitudes POST aquí
    // Por ejemplo:
    // if ($_SERVER['REQUEST_URI'] === '/submit-form') {
    //     // Lógica para manejar el envío del formulario
    // }
}


Route::get('/login',"ViewController@login");
Route::get('/logout',"UsuarioController@logout");
Route::get('/ge/bar/code',"ConsultaDelcontroller@generarBarCode");
Route::get('/ge/bar/code2',"ConsultaDelcontroller@generarBarCode2");

Route::get('/cargardireccion',"LocationController@getDepartments");
Route::get('/cargarprovincia',"LocationController@getProvincesByDepartment");
Route::get('/cargardistrito',"LocationController@getDistritosByProvincias");

Route::get('/cargartiposproducto', 'ProductosController@getTiposProducto');
Route::post('/guardarTipoProducto', 'ProductosController@guardarTipoProducto');
Route::get('/obtenerTodosProductos', 'ProductosController@obtenerTodosProductos');
Route::post('/guardarProducto', 'ProductosController@guardarProducto');
Route::get('/buscarAlmacen', 'ProductosController@buscarProductos');
Route::get('/tipoProducto', 'FinanciamientoController@obtenerTipoProducto');
Route::post('/guardarCategoriaProducto', 'ProductosController@guardarCategoriaProducto');
Route::get('/conductorPago', 'RegistrarConductorController@obtenerConductor');
Route::get('/verdetalleconductor', 'ConductorController@verDetalleConductor');
Route::post('/descargar-documento', 'ConductorController@descargarDocumento');
Route::get('/getdataForBarcode', 'ProductosController@getdataForBarcode');

/*SUPERPRUEBA
Route::post("/ajs/registrar/conductor","RegistrarConductorController@registrarTodo")->Middleware([ValidarTokenMiddleware::class]);
*/
Route::post("/ajs/registrar/conductor","RegistrarConductorController@registrarTodo")->Middleware([ValidarTokenMiddleware::class]);

// Ruta para obtener las categor�as de producto
Route::get('/cargarcategoriaproductos', 'ProductosController@getCategoriasProducto');

Route::get('/obtenerTipoProducto', 'ProductosController@obtenerTipoProducto');

Route::get('/obtenerFinanciamientoDetalle', 'GenerarContratosController@obtenerFinanciamientoDetalle');
Route::get('/cargarGruposFinanciamiento', 'FinanciamientoController@cargarGruposFinanciamiento');

Route::post('/guardarGrupoFinanciamiento', 'FinanciamientoController@guardarGrupoFinanciamiento');

Route::post('/guardarRegistroPago', 'RegistroPagoController@guardarRegistroPago');
Route::post('/generarCronogramaPDF', 'FinanciamientoController@generarCronogramaPDF');
Route::post('/generarContratosRegistro', 'GenerarContratosController@generarContratosRegistro');
Route::get('/TipoCambio', 'FinanciamientoController@obtenerTipoCambio');

Route::get('/obtenerClientesFinanciamiento', 'FinanciamientoController@obtenerClientesFinanciamiento');
Route::get('/obtenerClientesBuscados', 'FinanciamientoController@obtenerClientesFiltrados');
Route::get('/obtenerFinanciamientoPorCliente', 'FinanciamientoController@obtenerFinanciamientoPorCliente');
Route::get('/obtenerCuotasPorCliente', 'FinanciamientoController@obtenerCuotasPorCliente');
Route::get('/obtenerClienteDetalle', 'FinanciamientoController@obtenerClienteDetalle'); 
Route::get('/obtenerClientesAutocompletado', 'FinanciamientoController@obtenerClientesAutocompletado');
Route::get('/obtenerNumDocAutocompletado', 'FinanciamientoController@obtenerNumDocAutocompletado');

Route::get('/obtenerProductos', 'FinanciamientoController@obtenerProductos');
Route::get('/busquedaProductos', 'FinanciamientoController@searchProductos');
Route::get('/tipoProducto', 'FinanciamientoController@obtenerTipoProducto');
Route::get('/downloadReport', 'ProductosController@downloadReport');

Route::get("/obtenerDetallesProducto", "ProductosController@obtenerDetallesProducto");

Route::post('/busquedaFinanciamientos', 'GenerarContratosController@searchFinanciamientos');
Route::get('/buscarConductor', 'RegistrarFinanciamientoController@buscarConductor');
// Ruta para la b�squeda de planes mensuales
Route::post('/buscarPlanesMensuales', 'FinanciamientoController@buscarPlanesMensuales');
Route::post('/eliminar-masivo', 'ProductosController@saveProductsMassive');
Route::post('/obtener-plan-financiamiento', 'FinanciamientoController@obtenerPlanFinanciamiento');

// Ruta para guardar el financiamiento
Route::post('/guardarFinanciamiento', 'RegistrarFinanciamientoController@guardarFinanciamiento');
Route::post('/obtenerFinanciamientosPorFecha', 'GenerarContratosController@obtenerFinanciamientosPorFecha');
Route::post('/generarContratos', 'GenerarContratosController@generar');

Route::get('/venta/comprobante/pdf/ma4/:venta',"ReportesVentaController@comprobanteVentaMa4");
Route::get('/venta/comprobante/pdf/ma4/:venta/:nombre',"ReportesVentaController@comprobanteVentaMa4");
Route::get('/venta/comprobante/pdf/:venta',"ReportesVentaController@comprobanteVenta");
Route::get('/venta/comprobante/pdf/:venta/:nombre',"ReportesVentaController@comprobanteVenta");
Route::get('/venta/comprobante/pdfd/:venta/:nombre',"ReportesVentaController@comprobanteVentaBinario");
Route::get('/guia/remision/pdf/:guia','ReportesVentaController@guiaRemision');
Route::get('/nota/electronica/pdf/:nota','ReportesVentaController@comprobanteNotaE');
Route::get('/nota/electronica/pdf/:nota/:nombre','ReportesVentaController@comprobanteNotaE');
Route::get('/guia/remision/pdf/:guia/:nombre','ReportesVentaController@guiaRemision');


//pdf para voucher de venta
/* Route::get('/venta/comprobante/pdf/:voucher',"ReportesVentaController@comprobanteVenta"); */
Route::get("/r/cotizaciones/reporte/:coti","ReportesVentaController@comprobanteCotizacion");
Route::get("/reporte/ventas/pdf/:periodo","GeneradoresController@reportePeriodoVenta");
Route::get("/reporte/ventas/producto/lista/pdf/","ReportesVentaController@reporteVentaPorProducto");

Route::get('/venta/pdf/voucher/8cm/:voucher',"ReportesVentaController@imprimirvoucher8cm");
Route::get('/venta/pdf/voucher/8cm/:voucher/:nom',"ReportesVentaController@imprimirvoucher8cm");
Route::get('/venta/pdf/voucher/5.6cm/:voucher',"ReportesVentaController@imprimirvoucher5_6cm");
Route::get('/venta/pdf/voucher/5.6cm/:voucher/:nom',"ReportesVentaController@imprimirvoucher5_6cm");
Route::postBase("/reporte/cotizaciones/vendedores","GenerarReporte@reporteVentaPorVendedor");
Route::postBase("/reporte-almacen", "FragmentController@reporteAlmacen");
Route::postBase('/mostrarReportes','FragmentController@reportesview');

Route::get("/escanear/codigobarra/:empresa/:sucursal","ViewController@escanearBarra");
Route::postBase("/conductores", "FragmentController@viewConductores");
Route::get("/datoPagoConductor", "ConductorController@datoPagoConductor");
Route::post("/deleteInfoPagoConductor", "ConductorController@deleteInfoPagoConductor");
Route::baseStatic("ViewController@index",[ValidarTokenMiddleware::class]);
Route::post("/deleteConductor", "ConductorController@deleteConductor");

Route::postBase("/","FragmentController@home");
Route::postBase("/administrarempresas","FragmentController@adminEmpresas");
Route::postBase("/administrarempresas/ventas/:empresa","FragmentController@adminEmpresasVentas");
Route::postBase("/pagos","FragmentController@pagos");

Route::postBase("/caja/flujo","FragmentController@cajaFlujo");
Route::postBase("/cajaRegistros","FragmentController@cajaRegistros");

Route::postBase("/compras","FragmentController@compras");
Route::postBase("/compras/add","FragmentController@comprasAdd");

Route::postBase("/cobranzas","FragmentController@cobranzas");


Route::postBase("/cotizaciones","FragmentController@cotizaciones");
Route::postBase("/regisconductor", "FragmentController@regisconductor");
Route::PostBase("/searchconductor", "FragmentController@listConductor");
Route::postBase("/cotizaciones/add","FragmentController@cotizacionesAdd");
Route::postBase("/regiscliente", "FragmentController@regiscliente");

Route::postBase('/module-financiamiento',"FragmentController@abrirFinanciamiento");
Route::postBase('/nuevo-pago', "FragmentController@ingresarPagoInscripcion");
Route::postBase('/pago-inscripcion',"FragmentController@openPagoInscripcionConductor");
Route::postBase('/editar-conductor',"FragmentController@editarConductor");
Route::postBase('/editar-conductor-asesor', "FragmentController@editarConductorAsesor");
Route::postBase('/editar-producto',"FragmentController@editarProducto");
Route::postBase('/ver-clientes',"FragmentController@viewClientes");

Route::get('/dataBaseConductor', "ConductorController@generarDataBaseConductors");

Route::postBase("/cotizaciones/edt/:coti","FragmentController@cotizacionesEdt");



Route::postBase("/nota/electronica","FragmentController@notaElectronica");
Route::postBase("/nota/electronica/lista","FragmentController@notaElectronicaLista");

Route::postBase("/almacen/productos","FragmentController@almacenProductos");
Route::postBase("/almacen/productos/add","FragmentController@productoAdd");
Route::postBase("/test","FragmentController@test");

Route::postBase("/almacen/intercambio/productos","FragmentController@almacenIntercambioProductos");
/* Route::postBase("/almacen/intercambio/productos/add","FragmentController@productoAdd"); */

Route::postBase("/calendario","FragmentController@calendarioCliente");
Route::postBase("/clientes","FragmentController@clientesLista");
Route::postBase("/ventas","FragmentController@ventas");
Route::postBase("/guias/remision","FragmentController@guiaRemision");
Route::postBase("/ventas/productos","FragmentController@ventasProductos");
Route::postBase("/ventas/servicios","FragmentController@ventasServicios");
Route::postBase("/guia/remision/registrar","FragmentController@guiaRemisionAdd");

/* Route::postBase("/guia/remision/registrar/coti","FragmentController@guiaRemisionAddByCoti"); */
Route::postBase("/cuentas/cobrar","FragmentController@cuentasPorCobrar");


Route::postBase("/editar-venta-producto/:idVenta","FragmentController@editarVentaProducto");
Route::postBase("/editar-venta-servicio/:idVenta","FragmentController@editarVentaServicio");

Route::postBase("/financimientosAprobar", "FragmentController@financimientosAprobar");

Route::postBase("/creditScore","FragmentController@creditScore");

Route::get("/reporte/excel/:fecha","GenerarReporte@generarExcel");
Route::get("/reporte/producto/excel","GenerarReporte@generarExcelProducto");

Route::get("/reporte/rvta/excel/:fecha","GenerarReporte@generarExcelRVTA");
Route::post("/dataEditProducto","ProductosController@obtenerDatosProducto");
/* Route::get("/reporte/excel/test2","GenerarReporte@testExcel"); */

Route::get("/reporte/ingresos/egresos/:id","GenerarReporte@ingresosEgresos");
Route::get("/chargedData", "ConductorController@allConductors");
Route::get("/chargedData-asesor", "ConductorController@allConductorsva");


Route::get("/reporte/cliente/:id","ReportesVentaController@reporteCliente");


Route::get("/reporte/compras/pdf/:id","ReportesVentaController@reporteCompra");


Route::get("/reporte/productos/pdf/:id","ReportesVentaController@reporteProductos");
Route::get("/reporte/ventasganancias/pdf/:id","GeneradoresController@reportePeriodoVentaGanancias");

Route::get("/reporte/producto/guia","GenerarReporte@generarExcelProductoImporte");

Route::get("/reporte/caja/excel/:id","GenerarReporte@generarExcelCaja");
Route::get("/reporte/compras","ReportesVentaController@reporteCompraAll");
Route::postBase("/usuarios","FragmentController@usuariosLista");
Route::postBase("/grupo-financiamiento", "FragmentController@openGruposFinance");
Route::postBase("/pago-financiamiento", "FragmentController@pagoFinanciamiento");
Route::postBase("/conductores-cuotas-vencidas", "FragmentController@conductoresCuotasVencidas");

        
Route::postBase("/financiamientosAprobar", "FragmentController@financimientosAprobar");

Route::postBase("/comisiones", "FragmentController@comisiones");

Route::post("/addUser","ConsultasController@saveUser");
Route::get("/getBarCode", "ProductosController@getBarCode");
Route::get("/generateBarcode", "ProductosController@generateBarCode");
Route::post("/deleteProducts", "ProductosController@deleteProducts");
Route::get("/getDataSelets", "ProductosController@getEditsSeletProducto");
Route::get("/consultar-productos-venta", "VentasController@buscarProductoController");
route::post("/deleteFinance", "FinanciamientoController@deleteFinance");
route::post("/busquedaPorDni", "ConductorController@buscarPorDni");
route::post("/paymentMade", "ConductorController@paymentMade");
route::post("/save-newGroupFinance", "GruposFinanciamientoController@guardarPlanFinanciamiento");
route::get("/getAllPlanes", "GruposFinanciamientoController@getAllPlanes");
route::post("/asociar", "GruposFinanciamientoController@asociarProducto");

Route::post("/generarEnlacePDF", "ConductorController@generarEnlacePDF");
Route::get("/obtenerReportesPagos", "ConductorController@obtenerReportesPagos");
Route::post("/eliminarReportePago", "ConductorController@eliminarReportePago");
Route::post('/actualizarProducto', 'ProductosController@actualizarProducto');
Route::post('/obtenerPlanFinanciamiento', 'FinanciamientoController@getPlanFinanciamiento');
Route::get('/chargedReportAlmacen', 'ReportesMovimientosController@chargedReportAlmacen');
Route::get('/chargedUsuarios','ReportesMovimientosController@chargedUsuarios');
Route::get('/filtrarMovimientos','ReportesMovimientosController@filtrarMovimientos');
Route::post('/editGroup','GruposFinanciamientoController@editarGrupo');
Route::post('/getTipoVehicular', 'GruposFinanciamientoController@obtenerTipoVehicular');
Route::post('/deleteGroup','GruposFinanciamientoController@deleteGroup');
Route::post('/getIdConductorforDni', 'ConductorController@getIdConductorforDni');
Route::post('/financiamientoVehicular', 'RegistrarFinanciamientoController@SaveFinanciamientoVehicular');
Route::post('/newPagofinance', 'FinanciamientoController@newPagofinance');
Route::post('/getReportFinance', 'FinanciamientoController@getReportFinance');
Route::post('/deleteReportFinance', 'FinanciamientoController@deleteReportFinance');
Route::post('/downloadReportFinance', 'ReportFinanciamientoController@downloadReportFinance');
Route::get('/obtenerProductoPorCodigo', 'ReportesMovimientosController@verProductoReporte');

Route::get('/cargarGruposFinanciamiento1', 'FinanciamientoController@cargarGruposFinanciamiento1');
Route::post('/generateBoletaFinance', 'ReportFinanciamientoController@generateBoletaFinance');
Route::get('/numUnidad','RegistrarConductorController@obtenerNumeroLibre');
Route::get('/reportPagos', 'ConductorController@reportPagos');
Route::get('/get-reporte-pagos-finan', 'GenerarReporte@getReportePagoFinan');
Route::post('/deleteMassiveReportFinance', 'FinanciamientoController@deleteMassive');
Route::post('/generatePdf', 'ConductorController@generatePdfFromTable');
Route::post('/buscarClienteExiste', 'RegistrarFinanciamientoController@buscarClienteExiste');
Route::post('/buscaroCrearCliente', 'RegistrarFinanciamientoController@buscarOCrearCliente');
Route::post('/guardarCliente', 'ClientesController@guardarCliente');
Route::post('/cargardatosClientes','ClientesController@cargarDatosClientes');
Route::post('/verClienteModal','ClientesController@verCliente');
Route::post('/actualizarCliente','ClientesController@editarCliente');
Route::post('/clientesObtenerDepartamnentos', 'ClientesController@obtenerDepartamentos');
Route::post('/clienteObtenerProvincias','ClientesController@obtenerProvincias');
Route::post('/clienesObtenerDistritos','ClientesController@obtenerDistritos');
Route::post('/deleteCliente', 'ClientesController@eliminarCliente');
Route::post('/editarCliente', 'ClientesController@verEditarCliente');
Route::post('/getVariantesGrupo', 'GruposFinanciamientoController@obtenerVariantesGrupo');
Route::post('/updateVariante', 'GruposFinanciamientoController@actualizarVariante');
Route::get('/get-empleados', 'GenerarReporte@getEmpleados');
Route::post('/download-excel', 'GenerarReporte@downloadExcel');
Route::post('/download-pdf', 'GenerarReporte@downloadPDF');
Route::post('/financiamientos', 'GenerarReporte@financiamientos');
Route::post('/ventas-generales', 'GenerarReporte@ventasGenerales');
Route::post('/ventas-por-empleado', 'GenerarReporte@ventasPorEmpleado');
Route::post('/cuotas-pagadas', 'GenerarReporte@cuotasPagadas');
Route::post('/ingresos', 'GenerarReporte@ingresos');

Route::post('/obtenerDatosFinanciamientoCliente', 'FinanciamientoController@obtenerDatosFinanciamientoCliente');

Route::get('getFinanciamientos-pendientes', 'FinanciamientoController@getFinanciamientos_pendientes');

Route::post('getFinanciamientos-aprobar', 'FinanciamientoController@getFinanciamientosAprobar');
Route::post('getDetalleFinanciamiento', 'FinanciamientoController@getDetalleFinanciamiento');
Route::post('financiamiento-aprobado', 'FinanciamientoController@financiamientoAprobado');
Route::post('rechazarFinanciamiento', 'FinanciamientoController@rechazarFinanciamiento');
Route::post('reactivaFinanciamiento', 'FinanciamientoController@reactivaFinanciamiento');
Route::post('deleteFinanceRechazado', 'FinanciamientoController@deleteFinanciamientoRechazado');
Route::get('contarFinanciamientosRechazados', 'FinanciamientoController@contarFinanciamientosRechazados');

Route::post('changePasswordUser', 'UsuariosController@changePasswordUser');

Route::get('contarPagosPendientes', 'PagosController@contarPagosPendientes');

Route::get('getPagosFinancePendiente', 'PagosController@getPagosFinancePendiente');
Route::get('getPagosFinanceRechazados', 'PagosController@getPagosFinanceRechazados');
Route::post('verDetallePagoPendiente', 'PagosController@verDetallePagoPendiente');
Route::post('aprobarPagoPendiente', 'PagosController@aprobarPagoPendiente');
Route::post('rechazarPagoPendiente', 'PagosController@rechazarPagoPendiente');
Route::post('reactivarPagoPendiente', 'PagosController@reactivarPagoPendiente');
Route::post('eliminarPagoPendiente', 'PagosController@eliminarPagoPendiente');

// Rutas para motivos
Route::get('/guia/motivos/obtener', 'GuiaRemisionController@obtenerMotivos');
Route::post('/guia/motivos/crear', 'GuiaRemisionController@crearMotivo');

// Rutas para choferes
Route::get('/guia/choferes/obtener', 'GuiaRemisionController@obtenerChoferes');
Route::post('/guia/choferes/crear', 'GuiaRemisionController@crearChofer');

// Rutas para veh�culos
Route::get('/guia/vehiculos/obtener', 'GuiaRemisionController@obtenerVehiculos');
Route::post('/guia/vehiculos/crear', 'GuiaRemisionController@crearVehiculo');

// Rutas para licencias
Route::get('/guia/licencias/obtener', 'GuiaRemisionController@obtenerLicencias');
Route::post('/guia/licencias/crear', 'GuiaRemisionController@crearLicencia');

// Despu�s de las rutas existentes, agregar:
Route::post('/guia/motivos/eliminar', 'GuiaRemisionController@eliminarMotivo');
Route::post('/guia/choferes/eliminar', 'GuiaRemisionController@eliminarChofer');
Route::post('/guia/vehiculos/eliminar', 'GuiaRemisionController@eliminarVehiculo');
Route::post('/guia/licencias/eliminar', 'GuiaRemisionController@eliminarLicencia');

Route::post('/guia/remision/insertar3', 'GuiaRemisionController@insertar3');

Route::post('/verificarCodigoAsociado', 'FinanciamientoController@verificarCodigoAsociado');

Route::post('/cargarComisiones', 'ComisionesController@cargarComisiones');
// Cambiar de POST a GET
Route::post('/exportarComisiones', 'ComisionesController@exportarComisiones');
// Agregar estas rutas junto con las otras rutas de comisiones
Route::post('/obtenerDetalleComision', 'ComisionesController@obtenerDetalleComision');
Route::get('/chargedUsuarios', 'ComisionesController@chargedUsuarios'); 
Route::get('/numUnidadLima', 'RegistrarConductorController@obtenerNumeroLibreLima');


Route::get('/get-categorias', 'GenerarReporte@getCategorias');
Route::post('/get-productos-por-categoria', 'GenerarReporte@getProductosPorCategoria');
Route::get('/get-grupos-financiamiento', 'GenerarReporte@getGruposFinanciamiento');
Route::post('/get-variantes-por-grupo', 'GenerarReporte@getVariantesPorGrupo');
Route::post('/ventas-por-categoria', 'GenerarReporte@ventasPorCategoria');


//PUNTAJE CREDITICIO:
Route::get('/obtenerEstadisticasPuntaje', 'PuntajeCrediticioController@obtenerEstadisticasPuntaje');
Route::get('/obtenerClientesPuntaje', 'PuntajeCrediticioController@obtenerClientesPuntaje');
Route::get('/obtenerHistorialPuntaje', 'PuntajeCrediticioController@obtenerHistorialPuntaje');
Route::post('/actualizarPuntajesCrediticios', 'PuntajeCrediticioController@actualizarPuntajesCrediticios');
Route::get('/obtenerDetalleCliente', 'PuntajeCrediticioController@obtenerDetalleCliente');
Route::post('/actualizarPuntajeIndividual', 'PuntajeCrediticioController@actualizarPuntajeIndividual');

// RUTAS DE EXPORTACI�N
Route::get('/exportarPuntajes', 'PuntajeCrediticioController@exportarPuntajes');

// RUTAS ADICIONALES (opcionales - para funciones avanzadas)
Route::get('/obtenerMetricasAvanzadas', 'PuntajeCrediticioController@obtenerMetricasAvanzadas');
Route::get('/obtenerAlertasRiesgo', 'PuntajeCrediticioController@obtenerAlertasRiesgo');
Route::post('/simularPagoCuota', 'PuntajeCrediticioController@simularPagoCuota');
Route::get('/obtenerLogs', 'PuntajeCrediticioController@obtenerLogs');