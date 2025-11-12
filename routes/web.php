<?php

Route::get('/login', 'ViewController@login');
Route::get('/logout', 'UsuarioController@logout');
Route::get('/ge/bar/code', 'ConsultaDelcontroller@generarBarCode');
Route::get('/ge/bar/code2', 'ConsultaDelcontroller@generarBarCode2');

Route::get('/cargardireccion', 'LocationController@getDepartments');
Route::get('/cargarprovincia', 'LocationController@getProvincesByDepartment');
Route::get('/cargardistrito', 'LocationController@getDistritosByProvincias');

Route::get('/cargartiposproducto', 'ProductosController@getTiposProducto');
Route::post('/guardarTipoProducto', 'ProductosController@guardarTipoProducto');
Route::get('/obtenerTodosProductos', 'ProductosController@obtenerTodosProductos');
Route::post('/guardarProducto', 'ProductosController@guardarProducto');
Route::get('/buscarAlmacen', 'ProductosController@buscarProductos');
Route::post('/guardarCategoriaProducto', 'ProductosController@guardarCategoriaProducto');
Route::get('/conductorPago', 'RegistrarConductorController@obtenerConductor');
Route::get('/verdetalleconductor', 'ConductorController@verDetalleConductor');
Route::post('/descargar-documento', 'ConductorController@descargarDocumento');
Route::get('/getdataForBarcode', 'ProductosController@getdataForBarcode');

/*
 * SUPERPRUEBA
 * Route::post("/ajs/registrar/conductor","RegistrarConductorController@registrarTodo")->Middleware([ValidarTokenMiddleware::class]);
 */
Route::post('/ajs/registrar/conductor', 'RegistrarConductorController@registrarTodo')->Middleware([ValidarTokenMiddleware::class]);

// Ruta para obtener las categor as de producto
Route::get('/cargarcategoriaproductos', 'ProductosController@getCategoriasProducto');

Route::get('/obtenerTipoProducto', 'ProductosController@obtenerTipoProducto');

Route::get('/obtenerFinanciamientoDetalle', 'GenerarContratosController@obtenerFinanciamientoDetalle');
Route::get('/obtenerMetodosPago', 'RegistroPagoController@obtenerMetodosPago');
Route::post('/guardarRegistroPago', 'RegistroPagoController@guardarRegistroPago');
Route::post('/generarContratosRegistro', 'GenerarContratosController@generarContratosRegistro');

// Rutas para pagos pendientes de inscripción
Route::get('/listarPagosPendientesInscripcion', 'PagosPendientesInscripcionController@listarPagosPendientes');
Route::get('/listarPagosRechazadosInscripcion', 'PagosPendientesInscripcionController@listarPagosRechazados');
Route::get('/contarPagosPendientesInscripcion', 'PagosPendientesInscripcionController@contarPagosPendientes');
Route::post('/aprobarPagoInscripcion', 'PagosPendientesInscripcionController@aprobarPago');
Route::post('/rechazarPagoInscripcion', 'PagosPendientesInscripcionController@rechazarPago');
Route::post('/reactivarPagoInscripcion', 'PagosPendientesInscripcionController@reactivarPago');
Route::post('/eliminarPagoInscripcion', 'PagosPendientesInscripcionController@eliminarPago');
Route::get('/verComprobante/:id', 'PagosPendientesInscripcionController@verComprobante');

Route::get('/downloadReport', 'ProductosController@downloadReport');

Route::get('/obtenerDetallesProducto', 'ProductosController@obtenerDetallesProducto');

Route::post('/busquedaFinanciamientos', 'GenerarContratosController@searchFinanciamientos');
Route::post('/eliminar-masivo', 'ProductosController@saveProductsMassive');
// Ruta para la b squeda de planes mensuales

Route::get('/buscarConductor', 'RegistrarFinanciamientoController@buscarConductor');
// Ruta para guardar el financiamiento
Route::post('/guardarFinanciamiento', 'RegistrarFinanciamientoController@guardarFinanciamiento');
Route::post('/obtenerFinanciamientosPorFecha', 'GenerarContratosController@obtenerFinanciamientosPorFecha');
Route::post('/generarContratos', 'GenerarContratosController@generar');

Route::get('/venta/comprobante/pdf/ma4/:venta', 'ReportesVentaController@comprobanteVentaMa4');
Route::get('/venta/comprobante/pdf/ma4/:venta/:nombre', 'ReportesVentaController@comprobanteVentaMa4');
Route::get('/venta/comprobante/pdf/:venta', 'ReportesVentaController@comprobanteVenta');
Route::get('/venta/comprobante/pdf/:venta/:nombre', 'ReportesVentaController@comprobanteVenta');
Route::get('/venta/comprobante/pdfd/:venta/:nombre', 'ReportesVentaController@comprobanteVentaBinario');
Route::get('/guia/remision/pdf/:guia', 'ReportesVentaController@guiaRemision');
Route::get('/nota/electronica/pdf/:nota', 'ReportesVentaController@comprobanteNotaE');
Route::get('/nota/electronica/pdf/:nota/:nombre', 'ReportesVentaController@comprobanteNotaE');
Route::get('/guia/remision/pdf/:guia/:nombre', 'ReportesVentaController@guiaRemision');

// pdf para voucher de venta
/* Route::get('/venta/comprobante/pdf/:voucher',"ReportesVentaController@comprobanteVenta"); */
Route::get('/r/cotizaciones/reporte/:coti', 'ReportesVentaController@comprobanteCotizacion');
Route::get('/reporte/ventas/pdf/:periodo', 'GeneradoresController@reportePeriodoVenta');
Route::get('/reporte/ventas/producto/lista/pdf/', 'ReportesVentaController@reporteVentaPorProducto');

Route::get('/venta/pdf/voucher/8cm/:voucher', 'ReportesVentaController@imprimirvoucher8cm');
Route::get('/venta/pdf/voucher/8cm/:voucher/:nom', 'ReportesVentaController@imprimirvoucher8cm');
Route::get('/venta/pdf/voucher/5.6cm/:voucher', 'ReportesVentaController@imprimirvoucher5_6cm');
Route::get('/venta/pdf/voucher/5.6cm/:voucher/:nom', 'ReportesVentaController@imprimirvoucher5_6cm');
Route::postBase('/reporte-almacen', 'FragmentController@reporteAlmacen');
Route::postBase('/mostrarReportes', 'FragmentController@reportesview');

Route::get('/escanear/codigobarra/:empresa/:sucursal', 'ViewController@escanearBarra');
Route::postBase('/conductores', 'FragmentController@viewConductores');
Route::get('/datoPagoConductor', 'ConductorController@datoPagoConductor');
Route::post('/deleteInfoPagoConductor', 'ConductorController@deleteInfoPagoConductor');
Route::baseStatic('ViewController@index', [ValidarTokenMiddleware::class]);
Route::post('/deleteConductor', 'ConductorController@deleteConductor');

Route::postBase('/', 'FragmentController@home');
Route::postBase('/administrarempresas', 'FragmentController@adminEmpresas');
Route::postBase('/administrarempresas/ventas/:empresa', 'FragmentController@adminEmpresasVentas');
Route::postBase('/pagos', 'FragmentController@pagos');

Route::postBase('/caja/flujo', 'FragmentController@cajaFlujo');
Route::postBase('/cajaRegistros', 'FragmentController@cajaRegistros');

Route::postBase('/compras', 'FragmentController@compras');
Route::postBase('/compras/add', 'FragmentController@comprasAdd');

Route::postBase('/cobranzas', 'FragmentController@cobranzas');

Route::postBase('/cotizaciones', 'FragmentController@cotizaciones');
Route::postBase('/regisconductor', 'FragmentController@regisconductor');
Route::PostBase('/searchconductor', 'FragmentController@listConductor');
Route::postBase('/cotizaciones/add', 'FragmentController@cotizacionesAdd');
Route::postBase('/regiscliente', 'FragmentController@regiscliente');

Route::postBase('/module-financiamiento', 'FragmentController@abrirFinanciamiento');
Route::postBase('/nuevo-pago', 'FragmentController@ingresarPagoInscripcion');
Route::postBase('/pago-inscripcion', 'FragmentController@openPagoInscripcionConductor');
Route::postBase('/editar-conductor', 'FragmentController@editarConductor');
Route::postBase('/editar-conductor-asesor', 'FragmentController@editarConductorAsesor');
Route::postBase('/editar-producto', 'FragmentController@editarProducto');
Route::postBase('/ver-clientes', 'FragmentController@viewClientes');

Route::get('/dataBaseConductor', 'ConductorController@generarDataBaseConductors');

Route::postBase('/cotizaciones/edt/:coti', 'FragmentController@cotizacionesEdt');

Route::postBase('/nota/electronica', 'FragmentController@notaElectronica');
Route::postBase('/nota/electronica/lista', 'FragmentController@notaElectronicaLista');

Route::postBase('/almacen/productos', 'FragmentController@almacenProductos');
Route::postBase('/almacen/productos/add', 'FragmentController@productoAdd');
Route::postBase('/test', 'FragmentController@test');

Route::postBase('/almacen/intercambio/productos', 'FragmentController@almacenIntercambioProductos');
/* Route::postBase("/almacen/intercambio/productos/add","FragmentController@productoAdd"); */

Route::postBase('/calendario', 'FragmentController@calendarioCliente');
Route::postBase('/clientes', 'FragmentController@clientesLista');
Route::postBase('/ventas', 'FragmentController@ventas');
Route::postBase('/guias/remision', 'FragmentController@guiaRemision');
Route::postBase('/ventas/productos', 'FragmentController@ventasProductos');
Route::postBase('/ventas/servicios', 'FragmentController@ventasServicios');
Route::postBase('/guia/remision/registrar', 'FragmentController@guiaRemisionAdd');

/* Route::postBase("/guia/remision/registrar/coti","FragmentController@guiaRemisionAddByCoti"); */
Route::postBase('/cuentas/cobrar', 'FragmentController@cuentasPorCobrar');

Route::postBase('/editar-venta-producto/:idVenta', 'FragmentController@editarVentaProducto');
Route::postBase('/editar-venta-servicio/:idVenta', 'FragmentController@editarVentaServicio');

Route::postBase('/financimientosAprobar', 'FragmentController@financimientosAprobar');

Route::postBase('/creditScore', 'FragmentController@creditScore');

Route::get('/chargedData', 'ConductorController@allConductors');
Route::get('/chargedData-asesor', 'ConductorController@allConductorsva');

Route::get('/reporte/cliente/:id', 'ReportesVentaController@reporteCliente');

Route::get('/reporte/compras/pdf/:id', 'ReportesVentaController@reporteCompra');

Route::get('/reporte/productos/pdf/:id', 'ReportesVentaController@reporteProductos');
Route::get('/reporte/ventasganancias/pdf/:id', 'GeneradoresController@reportePeriodoVentaGanancias');

Route::get('/reporte/compras', 'ReportesVentaController@reporteCompraAll');

// Rutas para Dashboard de Analíticas CrediGo
Route::postBase('/reportes-credigo', 'FragmentController@reporteCredigo');
Route::get('/api/credigo/datos-graficas', 'ReportesCredigoController@obtenerDatosGraficas');
Route::get('/api/credigo/exportar-pdf', 'ReportesCredigoController@exportarPDF');

Route::postBase('/usuarios', 'FragmentController@usuariosLista');
Route::postBase('/grupo-financiamiento', 'FragmentController@openGruposFinance');
Route::postBase('/pago-financiamiento', 'FragmentController@pagoFinanciamiento');
Route::postBase('/conductores-cuotas-vencidas', 'FragmentController@conductoresCuotasVencidas');

Route::postBase('/financiamientosAprobar', 'FragmentController@financimientosAprobar');

Route::postBase('/comisiones', 'FragmentController@comisiones');

Route::post('/addUser', 'UsuariosController@addUser');
Route::post('/reactivarUsuario', 'UsuariosController@reactivarUsuario');
Route::get('/getBarCode', 'ProductosController@getBarCode');
Route::get('/generateBarcode', 'ProductosController@generateBarCode');
Route::post('/deleteProducts', 'ProductosController@deleteProducts');
Route::get('/getDataSelets', 'ProductosController@getEditsSeletProducto');
Route::get('/consultar-productos-venta', 'VentasController@buscarProductoController');
Route::post('/verificar-codigo-duplicado', 'ProductosController@verificarCodigoDuplicado');
Route::get('/obtenerVehiculos', 'ProductosController@obtenerVehiculos');

route::post('/busquedaPorDni', 'ConductorController@buscarPorDni');
route::post('/paymentMade', 'ConductorController@paymentMade');

Route::post('/generarEnlacePDF', 'ConductorController@generarEnlacePDF');
Route::get('/obtenerReportesPagos', 'ConductorController@obtenerReportesPagos');
Route::post('/eliminarReportePago', 'ConductorController@eliminarReportePago');
Route::post('/actualizarProducto', 'ProductosController@actualizarProducto');
// Ruta para obtener datos del producto para edición (nueva)
Route::post('/dataEditProducto', 'ProductosController@obtenerDatosProducto');

// Ruta para obtener opciones de selects - tipos y categorías (nueva)
Route::get('/getDataSelets', 'ProductosController@getEditsSeletProducto');

Route::get('/chargedReportAlmacen', 'ReportesMovimientosController@chargedReportAlmacen');
Route::get('/chargedUsuarios', 'ReportesMovimientosController@chargedUsuarios');
Route::get('/filtrarMovimientos', 'ReportesMovimientosController@filtrarMovimientos');
Route::post('/getIdConductorforDni', 'ConductorController@getIdConductorforDni');
Route::post('/financiamientoVehicular', 'RegistrarFinanciamientoController@SaveFinanciamientoVehicular');
Route::get('/obtenerProductoPorCodigo', 'ReportesMovimientosController@verProductoReporte');
Route::post('/downloadReportFinance', 'ReportFinanciamientoController@downloadReportFinance');

Route::post('/generateBoletaFinance', 'ReportFinanciamientoController@generateBoletaFinance');
Route::post('/obtenerBoletasPagoInicial', 'ReportFinanciamientoController@obtenerBoletasPagoInicial');
Route::post('/generarBoletaPagoInicial', 'ReportFinanciamientoController@generarBoletaPagoInicial');
Route::get('/numUnidad', 'RegistrarConductorController@obtenerNumeroLibre');
Route::get('/reportPagosUnificado', 'ConductorController@reportPagosUnificado');
Route::post('/generatePdf', 'ConductorController@generatePdfFromTable');
Route::post('/buscarClienteExiste', 'RegistrarFinanciamientoController@buscarClienteExiste');
Route::post('/buscaroCrearCliente', 'RegistrarFinanciamientoController@buscarOCrearCliente');
Route::post('/guardarCliente', 'ClientesController@guardarCliente');
Route::post('/cargardatosClientes', 'ClientesController@cargarDatosClientes');
Route::post('/verClienteModal', 'ClientesController@verCliente');
Route::post('/actualizarCliente', 'ClientesController@editarCliente');
Route::post('/clientesObtenerDepartamnentos', 'ClientesController@obtenerDepartamentos');
Route::post('/clienteObtenerProvincias', 'ClientesController@obtenerProvincias');
Route::post('/clienesObtenerDistritos', 'ClientesController@obtenerDistritos');
Route::post('/deleteCliente', 'ClientesController@eliminarCliente');
Route::post('/editarCliente', 'ClientesController@verEditarCliente');
Route::post('/obtenerComprobantePago', 'ClientesController@obtenerComprobantePago');

// NUEVO: Rutas para generar contratos de clientes
Route::get('/obtenerTodosLosClientes', 'ClientesController@obtenerTodosLosClientes');
Route::post('/buscarClientesContratos', 'ClientesController@buscarClientesContratos');
Route::post('/filtrarClientesPorFecha', 'ClientesController@filtrarClientesPorFecha');
Route::post('/generarContratoCliente', 'GenerarContratosController@generarContratoCliente');

// rutas de GruposFinanciamientoController
Route::post('/getVariantesGrupo', 'GruposFinanciamientoController@obtenerVariantesGrupo');
Route::post('/updateVariante', 'GruposFinanciamientoController@actualizarVariante');
route::post('/save-newGroupFinance', 'GruposFinanciamientoController@guardarPlanFinanciamiento');
route::get('/getAllPlanes', 'GruposFinanciamientoController@getAllPlanes');
route::post('/asociar', 'GruposFinanciamientoController@asociarProducto');
Route::post('/editGroup', 'GruposFinanciamientoController@editarGrupo');
Route::post('/getTipoVehicular', 'GruposFinanciamientoController@obtenerTipoVehicular');
Route::post('/deleteGroup', 'GruposFinanciamientoController@deleteGroup');
Route::post('/getDetallesPlan', 'GruposFinanciamientoController@obtenerDetallesPlan');

// RUTAS DE GenerarReporte
Route::get('/get-empleados', 'GenerarReporte@getEmpleados');
Route::post('/download-excel', 'GenerarReporte@downloadExcel');
Route::post('/download-pdf', 'GenerarReporte@downloadPDF');
Route::post('/financiamientos', 'GenerarReporte@financiamientos');
Route::post('/ventas-generales', 'GenerarReporte@ventasGenerales');
Route::post('/ventas-por-empleado', 'GenerarReporte@ventasPorEmpleado');
Route::post('/cuotas-pagadas', 'GenerarReporte@cuotasPagadas');
Route::post('/ingresos', 'GenerarReporte@ingresos');
Route::get('/get-reporte-pagos-finan', 'GenerarReporte@getReportePagoFinan');
Route::get('/get-categorias', 'GenerarReporte@getCategorias');
Route::post('/get-productos-por-categoria', 'GenerarReporte@getProductosPorCategoria');
Route::get('/get-grupos-financiamiento', 'GenerarReporte@getGruposFinanciamiento');
Route::post('/get-variantes-por-grupo', 'GenerarReporte@getVariantesPorGrupo');
Route::post('/ventas-por-categoria', 'GenerarReporte@ventasPorCategoria');
Route::get('/reporte/excel/:fecha', 'GenerarReporte@generarExcel');
Route::get('/reporte/producto/excel', 'GenerarReporte@generarExcelProducto');
Route::get('/reporte/rvta/excel/:fecha', 'GenerarReporte@generarExcelRVTA');
Route::post('/dataEditProducto', 'ProductosController@obtenerDatosProducto');
/* Route::get("/reporte/excel/test2","GenerarReporte@testExcel"); */
Route::get('/reporte/ingresos/egresos/:id', 'GenerarReporte@ingresosEgresos');
Route::postBase('/reporte/cotizaciones/vendedores', 'GenerarReporte@reporteVentaPorVendedor');
Route::get('/reporte/producto/guia', 'GenerarReporte@generarExcelProductoImporte');
Route::get('/reporte/caja/excel/:id', 'GenerarReporte@generarExcelCaja');

Route::post('changePasswordUser', 'UsuariosController@changePasswordUser');

Route::get('contarPagosPendientes', 'PagosController@contarPagosPendientes');

Route::get('getPagosFinancePendiente', 'PagosController@getPagosFinancePendiente');
Route::get('getPagosFinanceRechazados', 'PagosController@getPagosFinanceRechazados');
Route::post('verDetallePagoPendiente', 'PagosController@verDetallePagoPendiente');
Route::post('rechazarPagoPendiente', 'PagosController@rechazarPagoPendiente');
Route::post('reactivarPagoPendiente', 'PagosController@reactivarPagoPendiente');
Route::post('eliminarPagoPendiente', 'PagosController@eliminarPagoPendiente');

// Rutas para motivos
Route::get('/guia/motivos/obtener', 'GuiaRemisionController@obtenerMotivos');
Route::post('/guia/motivos/crear', 'GuiaRemisionController@crearMotivo');

// Rutas para choferes
Route::get('/guia/choferes/obtener', 'GuiaRemisionController@obtenerChoferes');
Route::post('/guia/choferes/crear', 'GuiaRemisionController@crearChofer');

// Rutas para veh culos
Route::get('/guia/vehiculos/obtener', 'GuiaRemisionController@obtenerVehiculos');
Route::post('/guia/vehiculos/crear', 'GuiaRemisionController@crearVehiculo');

// Rutas para licencias
Route::get('/guia/licencias/obtener', 'GuiaRemisionController@obtenerLicencias');
Route::post('/guia/licencias/crear', 'GuiaRemisionController@crearLicencia');

// Despu s de las rutas existentes, agregar:
Route::post('/guia/motivos/eliminar', 'GuiaRemisionController@eliminarMotivo');
Route::post('/guia/choferes/eliminar', 'GuiaRemisionController@eliminarChofer');
Route::post('/guia/vehiculos/eliminar', 'GuiaRemisionController@eliminarVehiculo');
Route::post('/guia/licencias/eliminar', 'GuiaRemisionController@eliminarLicencia');

Route::post('/guia/remision/insertar3', 'GuiaRemisionController@insertar3');

Route::post('/cargarComisiones', 'ComisionesController@cargarComisiones');
// Cambiar de POST a GET
Route::post('/exportarComisiones', 'ComisionesController@exportarComisiones');
// Agregar estas rutas junto con las otras rutas de comisiones
Route::post('/obtenerDetalleComision', 'ComisionesController@obtenerDetalleComision');
Route::get('/chargedUsuarios', 'ComisionesController@chargedUsuarios');
Route::get('/numUnidadLima', 'RegistrarConductorController@obtenerNumeroLibreLima');
Route::post('/verificarConductorExiste', 'RegistrarConductorController@verificarConductorExiste');
Route::post('/cambiarEstadoComision', 'ComisionesController@cambiarEstadoComision');
Route::post('eliminarComision', 'ComisionesController@eliminarComision');
Route::post('/accionMasivaComisiones', 'ComisionesController@accionMasivaComisiones');

// PUNTAJE CREDITICIO:
Route::get('/obtenerEstadisticasPuntaje', 'PuntajeCrediticioController@obtenerEstadisticasPuntaje');
Route::get('/obtenerClientesPuntaje', 'PuntajeCrediticioController@obtenerClientesPuntaje');
Route::get('/obtenerHistorialPuntaje', 'PuntajeCrediticioController@obtenerHistorialPuntaje');
Route::post('/actualizarPuntajesCrediticios', 'PuntajeCrediticioController@actualizarPuntajesCrediticios');
Route::get('/obtenerDetalleCliente', 'PuntajeCrediticioController@obtenerDetalleCliente');
// DESHABILITADO: El puntaje se actualiza automáticamente. El botón ahora solo refresca datos desde el frontend.
// Route::post('/actualizarPuntajeIndividual', 'PuntajeCrediticioController@actualizarPuntajeIndividual');
Route::post('/restablecerPuntajeIndividual', 'PuntajeCrediticioController@restablecerPuntajeIndividual');

// RUTAS DE EXPORTACI N
Route::get('/exportarPuntajes', 'PuntajeCrediticioController@exportarPuntajes');

// RUTAS ADICIONALES (opcionales - para funciones avanzadas)
Route::get('/obtenerMetricasAvanzadas', 'PuntajeCrediticioController@obtenerMetricasAvanzadas');
Route::get('/obtenerAlertasRiesgo', 'PuntajeCrediticioController@obtenerAlertasRiesgo');
Route::post('/simularPagoCuota', 'PuntajeCrediticioController@simularPagoCuota');
Route::get('/obtenerLogs', 'PuntajeCrediticioController@obtenerLogs');

Route::postBase('/cupones', 'FragmentController@cuponesDrivers');

// OBTENER PUNTAJES POR ID �NICO
Route::get('/obtenerPuntajeYDatos', 'PuntajeCrediticioController@obtenerPuntajeYDatos');
Route::postBase('/financiamiento-eliminados', 'FragmentController@pepeleraFinanciamientos');

// rutas de FinanciamientoController
Route::post('/marcarIncobrable', 'FinanciamientoController@marcarIncobrable');
Route::post('/marcarComoCobrable', 'FinanciamientoController@marcarComoCobrable');
Route::post('/obtenerDetalleCuotas', 'FinanciamientoController@obtenerDetalleCuotas');
Route::post('/obtenerCuotasVencidasFiltradas', 'FinanciamientoController@obtenerCuotasVencidasFiltradas');
Route::get('/tipoProducto', 'FinanciamientoController@obtenerTipoProducto');

// Route::get('/cargarGruposFinanciamiento', 'FinanciamientoController@cargarGruposFinanciamiento');
Route::post('/guardarGrupoFinanciamiento', 'FinanciamientoController@guardarGrupoFinanciamiento');
Route::post('/generarCronogramaPDF', 'FinanciamientoController@generarCronogramaPDF');
Route::get('/TipoCambio', 'FinanciamientoController@obtenerTipoCambio');

Route::get('/obtenerClientesFinanciamiento', 'FinanciamientoController@obtenerClientesFinanciamiento');
Route::get('/obtenerClientesBuscados', 'FinanciamientoController@obtenerClientesFiltrados');
Route::get('/obtenerFinanciamientoPorCliente', 'FinanciamientoController@obtenerFinanciamientoPorCliente');
Route::get('/obtenerCuotasPorCliente', 'FinanciamientoController@obtenerCuotasPorCliente');
Route::get('/obtenerClienteDetalle', 'FinanciamientoController@obtenerClienteDetalle');
Route::get('/obtenerClientesAutocompletado', 'FinanciamientoController@obtenerClientesAutocompletado');
Route::get('/obtenerNumDocClientesAutocompletado', 'FinanciamientoController@obtenerNumDocClientesAutocompletado');
Route::get('/obtenerNumDocAutocompletado', 'FinanciamientoController@obtenerNumDocAutocompletado');

Route::get('/obtenerProductos', 'FinanciamientoController@obtenerProductos');
Route::get('/busquedaProductos', 'FinanciamientoController@searchProductos');
Route::get('/tipoProducto', 'FinanciamientoController@obtenerTipoProducto');
Route::post('/buscarPlanesMensuales', 'FinanciamientoController@buscarPlanesMensuales');
Route::post('/obtener-plan-financiamiento', 'FinanciamientoController@obtenerPlanFinanciamiento');
route::post('/deleteFinance', 'FinanciamientoController@deleteFinance');
Route::post('/obtenerPlanFinanciamiento', 'FinanciamientoController@getPlanFinanciamiento');
Route::post('/newPagofinance', 'FinanciamientoController@newPagofinance');
Route::post('/getReportFinance', 'FinanciamientoController@getReportFinance');
Route::post('/deleteReportFinance', 'FinanciamientoController@deleteReportFinance');
Route::post('/anularPagoFinanciamiento', 'FinanciamientoController@anularPago');
Route::get('/cargarGruposFinanciamiento1', 'FinanciamientoController@cargarGruposFinanciamiento1');
Route::post('/deleteMassiveReportFinance', 'FinanciamientoController@deleteMassive');
Route::post('/obtenerDatosFinanciamientoCliente', 'FinanciamientoController@obtenerDatosFinanciamientoCliente');
Route::get('getFinanciamientos-pendientes', 'FinanciamientoController@getFinanciamientos_pendientes');

// RUTAS ANTIGUAS - Mantener por compatibilidad
Route::post('getFinanciamientos-aprobar', 'FinanciamientoController@getFinanciamientosAprobar');
Route::post('financiamiento-aprobado', 'FinanciamientoController@financiamientoAprobado');
Route::post('rechazarFinanciamiento', 'FinanciamientoController@rechazarFinanciamiento');
Route::post('reactivaFinanciamiento', 'FinanciamientoController@reactivaFinanciamiento');
Route::post('deleteFinanceRechazado', 'FinanciamientoController@deleteFinanciamientoRechazado');
Route::get('contarFinanciamientosRechazados', 'FinanciamientoController@contarFinanciamientosRechazados');
Route::post('/verificarCodigoAsociado', 'FinanciamientoController@verificarCodigoAsociado');
Route::post('/toggleDesvincularConductor', 'ConductorController@toggleDesvincularConductor');

Route::post('verificar-stock-logo', 'ProductosController@verificarStockLogo');
Route::post('/validar-conductor-logo-yango', 'VentasController@validarConductorLogoYango');

Route::post('/getEstadoPlan', 'GruposFinanciamientoController@obtenerEstadoPlan');
Route::post('/guardarPago', 'ClientesController@guardarPago');

Route::post('/get-financiamientos-eliminados', 'FinanciamientoController@getFinanciamientosEliminadosPapelera');
Route::post('/financiamientos/restaurar', 'FinanciamientoController@restaurarFinanciamiento');
Route::post('/financiamientos/eliminar-permanentemente', 'FinanciamientoController@eliminarPermanentemente');
Route::post('/financiamientos/vaciar-papelera', 'FinanciamientoController@vaciarPapelera');

Route::postBase('/beneficios', 'FragmentController@beneficiosUsuarios');

Route::get('/obtenerProductosVehiculos', 'FinanciamientoController@obtenerProductosVehiculos');
Route::get('/buscarProductosVehiculos', 'FinanciamientoController@buscarProductosVehiculos');
Route::post('/entregarVehiculo', 'FinanciamientoController@entregarVehiculo');
Route::post('/entregarVehiculoSoloFecha', 'FinanciamientoController@entregarVehiculoSoloFecha'); // ✅ NUEVO
Route::post('/generarContratoEntregaVehiculo', 'GenerarContratosController@generarContratoEntregaVehiculo');

// =====================================================
// RUTAS PARA CONFIGURACIÓN DE DEPARTAMENTOS (Solo Director - Rol 3)
// =====================================================

// Vista de configuración de departamentos
Route::postBase('/config/departamentos', 'FragmentController@configDepartamentos');

// API para obtener todos los departamentos con su estado
Route::get('/config/departamentos/obtener', 'DepartamentosConfigController@obtenerTodosDepartamentos');

// API para cambiar el estado de un departamento
Route::post('/config/departamentos/cambiar-estado', 'DepartamentosConfigController@cambiarEstadoDepartamento');

// API para obtener historial de cambios
Route::get('/config/departamentos/historial', 'DepartamentosConfigController@obtenerHistorial');

// API para habilitar múltiples departamentos a la vez
Route::post('/config/departamentos/habilitar-multiples', 'DepartamentosConfigController@habilitarMultiples');

Route::PostBase('vehiculos', 'FragmentController@viewVehiculos');

Route::postBase('/resumen-financiamientos', 'FragmentController@resumenFinanciamientos');

// ============ RUTAS DEL EDITOR DE CONTRATOS ============
// Gestión de plantillas de contratos
Route::get('/api/contratos/plantillas', 'EditorContratosController@listarPlantillas');
Route::get('/api/contratos/plantilla', 'EditorContratosController@obtenerPlantilla'); // ?id=X
Route::get('/api/contratos/plantilla-por-grupo', 'EditorContratosController@obtenerPlantillaPorGrupo'); // ?grupo_id=X
Route::post('/api/contratos/plantilla/crear', 'EditorContratosController@crearPlantilla');
Route::post('/api/contratos/plantilla/actualizar', 'EditorContratosController@actualizarPlantilla');
Route::post('/api/contratos/plantilla/eliminar', 'EditorContratosController@eliminarPlantilla');
Route::post('/api/contratos/plantilla/preview', 'EditorContratosController@vistaPrevia');
Route::post('/api/contratos/backup', 'EditorContratosController@crearBackup');
Route::get('/api/contratos/variables', 'EditorContratosController@listarVariables');

// Vista del editor de contratos
Route::postBase('/editor-contratos', 'FragmentController@editorContratos');

// ==================== RUTAS DEL EDITOR DE CONTRATOS ====================
Route::get('/api/contratos/plantillas', 'EditorContratosController@listarPlantillas');
Route::get('/api/contratos/plantilla/:id', 'EditorContratosController@obtenerPlantilla');
Route::post('/api/contratos/plantilla/crear', 'EditorContratosController@crearPlantilla');
Route::post('/api/contratos/plantilla/actualizar', 'EditorContratosController@actualizarPlantilla');
Route::delete('/api/contratos/plantilla/:id', 'EditorContratosController@eliminarPlantilla');
Route::post('/api/contratos/plantilla/preview', 'EditorContratosController@vistaPrevia');
Route::post('/api/contratos/plantilla/preview-pdf', 'EditorContratosController@generarPDFPreview');
Route::post('/api/contratos/hardcoded-preview', 'EditorContratosController@hardcodedPreview');
Route::post('/api/contratos/backup', 'EditorContratosController@crearBackup');
Route::get('/api/contratos/variables', 'EditorContratosController@listarVariables');
Route::get('/api/contratos/plantilla-por-grupo', 'EditorContratosController@obtenerPlantillaPorGrupo');

Route::post('/api/contratos/hardcoded-preview', 'EditorContratosController@hardcodedPreview');

Route::postBase('/adjudicaciones', 'FragmentController@adjudicaciones');