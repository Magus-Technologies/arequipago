<?php



Route::get("/data/cotizaciones/lista/ss","ConsultaDelcontroller@getDataCotizacionSS")->Middleware([ValidarTokenMiddleware::class]);
Route::get("/ajs/asearch/provedor/data","ConsultasController@buscarDataProveedor")->Middleware([ValidarTokenMiddleware::class]);

Route::post("/ajs/admin/cliente/add","AdminDataController@agregarCliente")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/admin/cliente/edt","AdminDataController@actualizarCliente")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/admin/cliente/info","AdminDataController@infoCliemt")->Middleware([ValidarTokenMiddleware::class]);


Route::post('/ajs/cargar/productos/precios',"ConsultasController@cargarPreciosProd")->Middleware([ValidarTokenMiddleware::class]);


Route::post("/ajs/admin/cliente/estado/edt","AdminDataController@guardarEstado")->Middleware([ValidarTokenMiddleware::class]);


Route::post("/ajs/data/producto/add","ProductosController@agregar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/producto/delete","ProductosController@delete")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/confirmar/traslado","ProductosController@confirmarTraslado")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/producto/add/lista","ProductosController@agregarPorLista")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/producto/lista","ProductosController@listaProducto")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/producto/edt","ProductosController@actualizar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/producto/edt/precios","ProductosController@actualizarPrecios")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/producto/info/code","ProductosController@informacionPorCodigo")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/producto/info","ProductosController@informacion")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/producto/restock","ProductosController@restock")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/producto/add/exel","ProductosController@importarExel")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/doc/venta/info","ConsultasController@functionbuscarDocumentoVentasSN")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/nota/electronica/add","ConsultasController@guardarNotaElectronica")->Middleware([ValidarTokenMiddleware::class]);


Route::post("/ajs/send/sunat/venta","VentasController@enviarDocumentoSunat")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/send/sunat/notaelectronica","ConsultasController@enviarDocumentoSunatNE")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/send/sunat/guiaremision","GuiaRemisionController@enviarDocumentoSunat")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/sucursales/empresa","ConsultasController@listasucursaleEmpresa")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/sucursales/empresa/add","ConsultasController@agregarSusucursal")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/sucursales/empresa/info","ConsultasController@getInfoSucursal")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/sucursales/empresa/info/detalle","ConsultasController@getInfoSucursalDetalle")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/sucursales/empresa/edt","ConsultasController@actualizarSucursal")->Middleware([ValidarTokenMiddleware::class]);

Route::post("/ajs/consulta/metodo/pago","ConsultasController@getMetodoPago")->Middleware([ValidarTokenMiddleware::class]);


Route::post("/ajs/consulta/stock/almacen","ConsultasController@consultaStockAlmacen")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/stock/almacen","ConsultasController@consultaStockAlmacen")->Middleware([ValidarTokenMiddleware::class]);

Route::post("/ajs/send/comprobante/email","ConsultasController@enviarcomprobanteEmail");

Route::post("/ajs/informacion/venta/fb","ConsultasController@informacionVentaFb");

Route::post("/ajs/verificador/token","ConsultasController@verificadorToken");


Route::post("/ajs/cotizaciones","CotizacionesController@listar");
Route::post("/ajs/cotizaciones/add","CotizacionesController@agregar");
Route::post("/ajs/cotizaciones/edt","CotizacionesController@actualizar");
Route::post("/ajs/cotizaciones/info","CotizacionesController@getInformacion");
Route::post("/ajs/cotizaciones/del","CotizacionesController@eliminarCotizacion");
Route::post("/ajs/cotizaciones/getvendedores","CotizacionesController@getVendedores");


Route::post('/ajs/cuentas/cobrar/render',"CobranzaController@render");
Route::post('/ajas/getAllCuotas/byIdVenta',"CobranzaController@getAllByIdVenta");
Route::post('/ajs/pagar/cuota/cobranza',"CobranzaController@pagarCuota");
Route::post('/ajs/pagar/cuota/ventas',"PagosController@pagarCuotaVentas");

Route::post('/ajs/caja/apertura',"CajaController@aperturarCaja");
Route::post('/ajs/caja/apertura/listar',"CajaController@listar");
Route::post('/ajs/caja/chica/add',"CajaController@agregarMovimiento");
Route::post('/ajs/caja/chica/cerrar',"CajaController@cerrarCajaChica");


Route::post('/ajs/prodcutos/compras/render',"ComprasController@getAll");
Route::post('/ajas/compra/detalle',"ComprasController@getDetalle");
Route::post('/ajas/compra/buscar/producto',"ComprasController@buscarProducto");
Route::post('/ajas/compras/add',"ComprasController@guardarCompras");



Route::post('/ajas/cuentas/ventas/render',"PagosController@render");
Route::post('/ajas/getAllCuotas/byIdCompra',"PagosController@getAllByIdCompra");
Route::post('/ajs/pagar/cuota/pago',"PagosController@pagarCuota");


Route::post("/ajas/ventas/porempresa","VentasController@listaVentasPorEmpresa");
Route::post("/ajas/ventas/porempresa/regenxml","VentasController@regenerarXML");
Route::post("/ajas/ventas/porempresa/sendsunat","VentasController@enviarDocumentoSunatPorEmpresa");
Route::post("/ajas/ventas/porempresa/sendsunatresumen","VentasController@envioResumenDiarioPorEmpresa");
Route::post("/ajas/ventas/porempresa/sendsunatcomubaja","VentasController@envioComunicacionBajaPorEmpresa");

Route::post("/ajs/getroles","ConsultasController@getRoles");
Route::post("/ajs/add/users","ConsultasController@saveUser");


Route::post('/ajs/aprobarPagoPendiente', 'PagosController@aprobarPagoPendiente');

// RUTAS PARA MORAS PENDIENTES
Route::get('/ajs/getMorasPendientes', 'FinanciamientoController@getMorasPendientes');
Route::get('/ajs/getContadorMorasPendientes', 'FinanciamientoController@getContadorMorasPendientes');
Route::post('/ajs/pagarMoraPendiente', 'FinanciamientoController@pagarMoraPendiente');
Route::get('/ajs/getHistorialCuotasPagadas', 'FinanciamientoController@getHistorialCuotasPagadas');

// RUTA PARA ENTREGAR VEHÍCULO CREDIYANGO Y GENERAR CRONOGRAMA
Route::post('/ajs/entregarVehiculoCrediYango', 'FinanciamientoController@entregarVehiculoCrediYango');

// RUTA PARA ENTREGAR VEHÍCULO CREDI AHORRO AUTOS Y REGISTRAR EXCEDENTE
Route::post('/ajs/entregarVehiculoCrediAhorrosAutos', 'FinanciamientoController@entregarVehiculoCrediAhorrosAutos');
Route::post('/ajs/generarReciboExcedente', 'FinanciamientoController@generarReciboExcedente');

// RUTAS PARA RESUMEN DE FINANCIAMIENTOS
Route::get('/ajs/obtenerResumenFinanciamientos', 'FinanciamientoController@obtenerResumenFinanciamientos');
Route::get('/ajs/obtenerDetalleFinanciamientoPlan', 'FinanciamientoController@obtenerDetalleFinanciamientoPlan');
Route::get('/ajs/exportarResumenFinanciamientoPlan', 'ReportesResumenFinanciamientoController@exportarResumenFinanciamientoPlan');


Route::post('/ajs/newPagofinance', 'FinanciamientoController@newPagofinance');

// RUTAS PARA APROBACIÓN DE FINANCIAMIENTOS
Route::post('/ajs/aprobacion/obtenerPendientes', 'AprobacionFinanciamientoController@obtenerFinanciamientosPendientes');
Route::post('/ajs/aprobacion/obtenerDetalle', 'AprobacionFinanciamientoController@obtenerDetalleFinanciamiento');
Route::post('/ajs/aprobacion/aprobar', 'AprobacionFinanciamientoController@aprobarFinanciamiento');
Route::post('/ajs/aprobacion/rechazar', 'AprobacionFinanciamientoController@rechazarFinanciamiento');
Route::post('/ajs/aprobacion/eliminar', 'AprobacionFinanciamientoController@eliminarFinanciamiento');
Route::post('/ajs/aprobacion/reactivar', 'AprobacionFinanciamientoController@reactivarFinanciamiento');

// RUTAS PARA CONSTATACIONES DOMICILIARIAS
Route::get('/ajs/constataciones/listar', 'ConstatacionesController@listar');
Route::get('/ajs/constataciones/contador', 'ConstatacionesController@contador');
Route::post('/ajs/constataciones/guardar', 'ConstatacionesController@guardar');
Route::get('/ajs/constataciones/detalle', 'ConstatacionesController@detalle');
Route::get('/ajs/constataciones/foto', 'ConstatacionesController@servirFoto');
Route::get('/ajs/constataciones/pdf', 'ConstatacionesController@generarPDF');
Route::post('/ajs/constataciones/eliminar', 'ConstatacionesController@eliminar');
Route::get('/ajs/constataciones/info-financiamiento', 'ConstatacionesController@infoFinanciamiento');
Route::get('/ajs/constataciones/exportar-excel', 'ConstatacionesController@exportarExcel');

// RUTA PARA BÚSQUEDA DE SUGERENCIAS DE CONDUCTORES
Route::get('/ajs/buscarSugerenciasConductores', 'FinanciamientoController@buscarSugerenciasConductores');

// RUTA PARA GENERAR BOLETA DE CUOTA (pagos por app o registrados)
Route::post('/ajs/generarBoletaCuota', 'FinanciamientoController@generarBoletaCuota');

// RUTAS PARA RETIRO DE FINANCIAMIENTOS (PLANES 19 Y 38)
Route::post('/ajs/retiro/calcularPenalidad', 'RetiroFinanciamientoController@calcularPenalidad');
Route::post('/ajs/retiro/procesarRetiro', 'RetiroFinanciamientoController@procesarRetiro');
Route::get('/ajs/retiro/listarRetirados', 'RetiroFinanciamientoController@obtenerFinanciamientosRetirados');

// RUTAS PARA RECAUDACIONES DE CAJA AREQUIPA
Route::post('/ajs/recaudaciones/listar', 'RecaudacionesController@listarRecaudaciones');
Route::post('/ajs/recaudaciones/resumen', 'RecaudacionesController@obtenerResumen');
Route::post('/ajs/recaudaciones/detalle', 'RecaudacionesController@obtenerDetalle');
Route::post('/ajs/recaudaciones/exportar', 'RecaudacionesController@exportarExcel');
Route::post('/ajs/recaudaciones/serie-numero', 'RecaudacionesController@obtenerSerieNumero');
Route::post('/ajs/recaudaciones/facturar', 'RecaudacionesController@generarFacturaPago');

// Facturación de pagos de financiamiento
Route::post('/ajs/financiamiento/pago/detalle', 'FinanciamientoController@obtenerDetallePagoFinanciamiento');
Route::post('/ajs/financiamiento/pago/serie-numero', 'FinanciamientoController@obtenerSerieNumeroFinanciamiento');
Route::post('/ajs/financiamiento/pago/facturar', 'FinanciamientoController@generarFacturaPagoFinanciamiento');
