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


