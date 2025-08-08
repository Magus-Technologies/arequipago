<?php

require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/vendor/autoload.php';
require_once "app/models/Venta.php";
require_once "app/models/Cliente.php";
require_once "app/models/DocumentoEmpresa.php";
require_once "app/models/ProductoVenta.php";
require_once "app/models/VentaServicio.php";
require_once "app/models/Varios.php";
require_once "app/models/VentaSunat.php";
require_once "app/models/VentaAnulada.php";
require_once "app/clases/SendURL.php";


use Endroid\QrCode\QrCode;
use Luecano\NumeroALetras\NumeroALetras;

class ReportesVentaController extends Controller
{
  private $mpdf;
  private $conexion;

  public function __construct()
  {
    $this->mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 0]);
    $this->conexion = (new Conexion())->getConexion();
    $this->venta = new Venta();
  }

  public function reporteVentaPorProducto()
  {
    $sql = "";

    if (strlen($_GET['fecha2']) == 0) {
      $sql = "select  p.descripcion,v.fecha_emision,ds.nombre nombre_documento,concat(v.serie,'-',v.numero) venta_sn, pv.cantidad,pv.precio,pv.precio_usado ,tp.nombre nom_pago
            from productos_ventas pv
            join productos p on p.id_producto = pv.id_producto
            join ventas v on v.id_venta = pv.id_venta
            join tipo_pago tp on tp.tipo_pago_id = v.id_tipo_pago
            join documentos_sunat ds on v.id_tido = ds.id_tido
            where trim(p.codigo)='{$_GET['codprod']}' and v.fecha_emision >= '{$_GET['fecha1']}'  and v.estado<>'2'
                ";
    } else {
      $sql = "select  p.descripcion,v.fecha_emision,ds.nombre nombre_documento,concat(v.serie,'-',v.numero) venta_sn, pv.cantidad,pv.precio,pv.precio_usado ,tp.nombre nom_pago
            from productos_ventas pv
            join productos p on p.id_producto = pv.id_producto
            join ventas v on v.id_venta = pv.id_venta
            join tipo_pago tp on tp.tipo_pago_id = v.id_tipo_pago
            join documentos_sunat ds on v.id_tido = ds.id_tido
            where trim(p.codigo)='{$_GET['codprod']}' and v.fecha_emision between '{$_GET['fecha1']}' and '{$_GET['fecha2']}' and v.estado<>'2'";

    }

    $rowHmtl = '';
    $rows = $this->conexion->query($sql);

    foreach ($rows as $row) {
      $rowHmtl .= "
          <tr>
          <td>{$row['descripcion']}</td>
          <td>{$row['nom_pago']}</td>
          <td>{$row['fecha_emision']}</td>
          <td>{$row['nombre_documento']}</td>
          <td>{$row['venta_sn']}</td>
          <td>{$row['cantidad']}</td>
          <td>{$row['precio']}</td>
            </tr>
          ";
    }

    $html = "
     
    <div style='width: 100%; '>
        <div style='width: 100%; text-align: center;'>
                <h2 style=''>REPORTE DE PRODUCTOS POR VENTA</h2>
              
        </div> 
        
        <div style='width: 100%; margin-top:40px;'>
            <table border='1' style='width: 100%; text-align: center;' >
                <thead>
                <tr>
                  
                    <th style=''>Producto</th>
                    <th style=''>Pago</th>
                    <th style=''>Fecha</th>
                    <th style=''>Doc.</th>
                    <th style=''>S-N</th>
                    <th style=''>Cantidad</th>
                    <th style=''>Precio</th>
                  
              
                </tr>
                </thead>
               <tbody>
                $rowHmtl
                </tbody>
            </table>
        </div>
        
    </div>
    ";
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $this->mpdf->Output();

  }


  public function reporteCompra($id)
  {



    $sql = "SELECT c.fecha_emision,c.direccion,CONCAT( ds.abreviatura , ' | ' , c.serie , ' - ', c.numero)AS factura,p.razon_social,
    c.total,tp.nombre as tipoPago,c.dias_pagos,c.id_empresa
     FROM compras c
    	LEFT JOIN documentos_sunat ds ON c.id_tido = ds.id_tido
	LEFT JOIN proveedores p ON p.proveedor_id = c.id_proveedor
	LEFT JOIN tipo_pago tp ON tp.tipo_pago_id = c.id_tipo_pago 
    WHERE c.id_compra = $id";
    $result = $this->conexion->query($sql);


    $rowHmtl = "";
    $idEmpresa = "";
    foreach ($result as $fila) {
      $total = number_format($fila['total'], 2, ".", "");
      $idEmpresa = $fila['id_empresa'];
      $rowHmtl .= "<tr>
      <td style='font-size: 9px'>{$fila['fecha_emision']}</td>
      <td style='font-size: 9px'>{$fila['direccion']}</td>
      <td style='font-size: 9px'>{$fila['factura']}</td>
      <td style='font-size: 9px'>{$fila['razon_social']}</td>
      <td style='font-size: 9px'>{$fila['tipoPago']}</td>
      <td style='font-size: 9px'>{$fila['dias_pagos']}</td>
      <td style='font-size: 9px'>{$total}</td>
  </tr>";
    }
    $this->mpdf->WriteHTML("
    table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
    }
    ", \Mpdf\HTMLParserMode::HEADER_CSS);


    $empresa = $this->conexion->query("SELECT * from empresas
    where id_empresa = '{$idEmpresa}'")->fetch_assoc();


    $sql = "SELECT * FROM clientes WHERE id_cliente = $id";
    $result = $this->conexion->query($sql)->fetch_assoc();

    $html = "
     
    <div style='width: 100%; '>
        <div style='width: 100%; text-align: center;'>
                <h2 style=''>REPORTE DE VENTAS POR COMPRAS</h2>
              
        </div>
        <div style='width: 100%;'>
            <table style='width: 100%;'>
            <tr>
            <td>EMPRESA:</td>
            <td>{$empresa["ruc"]} | {$empresa['razon_social']}</td>
        </tr>
            </table>
        </div>
        
        <div style='width: 100%; margin-top:40px;'>
            <table style='width: 100%; text-align: center;' >
                <thead>
                <tr>
                  
                    <th style='width: 10%;'>Fecha</th>
                    <th style='width: auto;'>Dirección</th>
                    <th style='width: auto;'>Factura</th>
                    <th style='width: auto;'>Razon Social</th>
                    <th style='width: 10%;'>Tipo Pago</th>
                    <th style='width: 10%;'>Días Pagos</th>
                    <th style='width: 10%;'>Total</th>
                  
              
                </tr>
                </thead>
               <tbody>
                $rowHmtl
                </tbody>
            </table>
        </div>
        
    </div>
    ";
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $this->mpdf->Output();
  }

  public function reporteCompraAll()
  {
    $sql = "SELECT c.fecha_emision,c.direccion,CONCAT( ds.abreviatura , ' | ' , c.serie , ' - ', c.numero)AS factura,p.razon_social,
    c.total,tp.nombre as tipoPago,c.dias_pagos,c.id_empresa
     FROM compras c
     LEFT JOIN documentos_sunat ds ON c.id_tido = ds.id_tido
	    LEFT JOIN proveedores p ON p.proveedor_id = c.id_proveedor
	    LEFT JOIN tipo_pago tp ON tp.tipo_pago_id = c.id_tipo_pago";
    $result = $this->conexion->query($sql);


    $rowHmtl = "";
    $idEmpresa = "";
    foreach ($result as $fila) {
      $total = number_format($fila['total'], 2, ".", "");
      $idEmpresa = $fila['id_empresa'];
      $rowHmtl .= "<tr>
      <td style='font-size: 9px'>{$fila['fecha_emision']}</td>
      <td style='font-size: 9px'>{$fila['direccion']}</td>
      <td style='font-size: 9px'>{$fila['factura']}</td>
      <td style='font-size: 9px'>{$fila['razon_social']}</td>
      <td style='font-size: 9px'>{$fila['tipoPago']}</td>
      <td style='font-size: 9px'>{$fila['dias_pagos']}</td>
      <td style='font-size: 9px'>{$total}</td>
  </tr>";
    }
    $this->mpdf->WriteHTML("
    table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
    }
    ", \Mpdf\HTMLParserMode::HEADER_CSS);


    $empresa = $this->conexion->query("SELECT * from empresas
    where id_empresa = '{$idEmpresa}'")->fetch_assoc();




    $html = "
     
    <div style='width: 100%; '>
        <div style='width: 100%; text-align: center;'>
                <h2 style=''>REPORTE DE VENTAS POR COMPRAS</h2>
              
        </div>
        <div style='width: 100%;'>
            <table style='width: 100%;'>
            <tr>
            <td>EMPRESA:</td>
            <td>{$empresa["ruc"]} | {$empresa['razon_social']}</td>
        </tr>
            </table>
        </div>
        
        <div style='width: 100%; margin-top:40px;'>
            <table style='width: 100%; text-align: center;' >
                <thead>
                <tr>
                  
                    <th style='width: 10%;'>Fecha</th>
                    <th style='width: auto;'>Dirección</th>
                    <th style='width: auto;'>Factura</th>
                    <th style='width: auto;'>Razon Social</th>
                    <th style='width: 10%;'>Tipo Pago</th>
                    <th style='width: 10%;'>Días Pagos</th>
                    <th style='width: 10%;'>Total</th>
                  
              
                </tr>
                </thead>
               <tbody>
                $rowHmtl
                </tbody>
            </table>
        </div>
        
    </div>
    ";
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $this->mpdf->Output();
  }

  public function reporteCliente($id)
  {

    $sql = "SELECT *,metodo_pago.nombre AS metodoPago,tipo_pago.nombre AS tipoPago FROM VENTAS 
    LEFT JOIN metodo_pago ON metodo_pago.id_metodo_pago=ventas.medoto_pago_id
    LEFT JOIN tipo_pago ON tipo_pago.tipo_pago_id=ventas.id_tipo_pago WHERE id_cliente = $id";
    $result = $this->conexion->query($sql);

    $rowHmtl = "";
    foreach ($result as $fila) {
      $total = number_format($fila['total'], 2, ".", "");
      $rowHmtl .= "<tr>
      <td style='font-size: 9px'>{$fila['id_venta']}</td>
      <td style='font-size: 9px'>{$fila['fecha_emision']}</td>
      <td style='font-size: 9px'>{$fila['direccion']}</td>
      <td style='font-size: 9px'>{$fila['tipoPago']}</td>
      <td style='font-size: 9px'>{$fila['dias_pagos']}</td>
      <td style='font-size: 9px'>{$total}</td>
      <td style='font-size: 9px'>{$fila['metodoPago']}</td>
  </tr>";
    }
    $this->mpdf->WriteHTML("
    table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
    }
    ", \Mpdf\HTMLParserMode::HEADER_CSS);


    $sql = "SELECT * FROM clientes WHERE id_cliente = $id";
    $result = $this->conexion->query($sql)->fetch_assoc();

    $html = "
     
    <div style='width: 100%; '>
        <div style='width: 100%; text-align: center;'>
                <h2 style=''>REPORTE DE VENTAS POR CLIENTE</h2>
              
        </div>
        <div style='width: 100%;'>
            <table style='width: 100%;'>
                <tr>
                    <td>Documento:</td>
                    <td>{$result['documento']}</td>
                </tr>
                <tr>
                    <td>Cliente:</td>
                    <td>{$result['datos']}</td>
                </tr>
                <tr>
                    <td>Dirección:</td>
                    <td>{$result['direccion']}</td>
                </tr>
                <tr>
                    <td>Dirección:</td>
                    <td>{$result['telefono']}</td>
                </tr>
            </table>
        </div>
        
        <div style='width: 100%; margin-top:40px;'>
            <table style='width: 100%; text-align: center;' >
                <thead>
                <tr>
                    <th style='width: 10%;'>Codigo</th>
                    <th style='width: 10%;'>Fecha</th>
                    <th style='width: auto;'>Dirección</th>
                    <th style='width: 10%;'>Tipo Pago</th>
                    <th style='width: 10%;'>Dias Pagos</th>
                    <th style='width: 10%;'>Total</th>
                    <th style='width:auto;'>Metodo Pago</th>
              
                </tr>
                </thead>
               <tbody>
                $rowHmtl
                </tbody>
            </table>
        </div>
        
    </div>
    ";
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $this->mpdf->Output();
  }

  public function reporteProductos($id)
  {
    $rpart = explode("-", $_GET["fecha"]);
    //var_dump($rpart);
    if ($rpart[1] == 'nn') {
      $sql = "SELECT pv.id_producto,c.datos,c.documento,v.id_venta,v.serie,v.numero,v.fecha_emision,pv.cantidad,pv.precio FROM ventas v 
    JOIN productos_ventas pv ON v.id_venta = pv.id_venta
    LEFT JOIN clientes c ON c.id_cliente= v.id_cliente 
    WHERE pv.id_producto= $id and concat(year(v.fecha_emision),month(v.fecha_emision))='" . $rpart[0] . "'";
    } else {
      $sql = "SELECT pv.id_producto,c.datos,c.documento,v.id_venta,v.serie,v.numero,v.fecha_emision,pv.cantidad,pv.precio FROM ventas v 
    JOIN productos_ventas pv ON v.id_venta = pv.id_venta
    LEFT JOIN clientes c ON c.id_cliente= v.id_cliente 
    WHERE pv.id_producto= $id and concat(year(v.fecha_emision),month(v.fecha_emision), day(v.fecha_emision))='" . $rpart[0] . $rpart[1] . "'";
    }
    //var_dump($sql);
    //die();

    $result = $this->conexion->query($sql);

    $rowHmtl = "";
    $totalSuma = 0;
    foreach ($result as $fila) {
      $cantidad = number_format($fila['cantidad'], 2, ".", "");
      $precio = number_format($fila['precio'], 2, ".", "");
      $total = $cantidad * $precio;
      $total = number_format($total, 2, ".", "");
      $rowHmtl .= "<tr>
      <td style='font-size: 9px'>{$fila['documento']}</td>
      <td style='font-size: 9px'>{$fila['datos']}</td>
      <td style='font-size: 9px'>{$fila['id_venta']}</td>
      <td style='font-size: 9px'>{$fila['serie']}</td>
      <td style='font-size: 9px'>{$fila['numero']}</td>
      <td style='font-size: 9px'>{$fila['fecha_emision']}</td>
      <td style='font-size: 9px'>{$cantidad}</td>
      <td style='font-size: 9px'>{$precio}</td>
      <td style='font-size: 9px'>{$total}</td>
    </tr>";
      $totalSuma += $total;
    }
    $this->mpdf->WriteHTML("
    table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
    }
    ", \Mpdf\HTMLParserMode::HEADER_CSS);


    $sql = "SELECT * FROM productos WHERE id_producto = $id";
    $result = $this->conexion->query($sql)->fetch_assoc();

    $html = "
     
    <div style='width: 100%; '>
        <div style='width: 100%; text-align: center;'>
                <h2 style=''>REPORTE DE VENTAS POR PRODUCTO</h2>
              
        </div>
        <div style='width: 100%;'>
            <table style='width: 100%;'>
                <tr>
                    <td>Producto:</td>
                    <td>{$result['descripcion']}</td>
                </tr>
            </table>
        </div>
        
        <div style='width: 100%; margin-top:40px;'>
            <table style='width: 100%; text-align: center;' >
                <thead>
                <tr>
                    <th style='width: 10%;'>Documento</th>
                    <th style='width: 10%;'>Datos</th>
                    <th style='width: auto;'>Id venta</th>
                    <th style='width: 10%;'>Serie</th>
                    <th style='width: 10%;'>Numero</th>
                    <th style='width: 10%;'>Fecha Emision</th>
                    <th style='width:auto;'>Cantidad</th>
                    <th style='width:auto;'>Precio</th>
                    <th style='width:auto;'>Total</th>
              
                </tr>
                </thead>
               <tbody>
                $rowHmtl
                </tbody>
                <tfoot>
                <tr>
                <td colspan='8' style='text-align: right;font-size: 13px'>Total</td>
                <td  style='font-size: 13px'>$totalSuma</td>
                </tr>
                </tfoot>
            </table>
        </div>
        
    </div>
    ";
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $this->mpdf->Output();
  }

  public function comprobanteCotizacion($coti)
  {


    $listaProd1 = $this->conexion->query("SELECT pc.*,p.descripcion,TRIM(p.codigo) codigo  from productos_cotis pc 
            join productos p on p.id_producto = pc.id_producto where pc.id_coti='$coti' order by codigo ASC");



    $sql = "select * from cotizaciones where cotizacion_id=" . $coti;
    $datoVenta = $this->conexion->query($sql)->fetch_assoc();

    $datoEmpresa = $this->conexion->query("select * from empresas where id_empresa=" . $_SESSION['id_empresa'])->fetch_assoc();

    $resultC = $this->conexion->query("select * from clientes where id_cliente = " . $datoVenta['id_cliente'])->fetch_assoc();
    $dataDocumento = strlen($resultC['documento']) == 8 ? "DNI" : strlen($resultC['documento'] == 11 ? 'RUC' : '');

    $fecha_emision = Tools::formatoFechaVisual($datoVenta['fecha']);

    // MODIFICADO: Añadida condición para tipo de pago 3 (GRATIS)
    if ($datoVenta["id_tipo_pago"] == '1') {
      $tipo_pagoC = 'CONTADO';
    } elseif ($datoVenta["id_tipo_pago"] == '3') {
      $tipo_pagoC = 'GRATIS';
    } else {
      $tipo_pagoC = 'CREDITO';
    }

    $tabla_cuotas = '';

    $menosRowsNumH = 0;

    if ($datoVenta["id_tipo_pago"] == '2') {
      $rowTempCuo = '';
      $sql = "SELECT * FROM cuotas_cotizacion WHERE id_coti='$coti'";
      $resulTempCuo = $this->conexion->query($sql);
      $contadorCuota = 0;
      $menosRowsNumH = 1;
      foreach ($resulTempCuo as $cuotTemp) {
        $menosRowsNumH++;
        $contadorCuota++;
        $tempNum = Tools::numeroParaDocumento($contadorCuota, 2);
        $tempFecha = Tools::formatoFechaVisual($cuotTemp['fecha']);
        $tempMonto = Tools::money($cuotTemp['monto']);
        $rowTempCuo .= "
            <tr>
                <td>Cuota $tempNum</td>
                <td>$tempFecha </td>
                <td>S/ $tempMonto</td>
            </tr>
            ";
      }
      $tabla_cuotas = '<div style="width: 100%;">
        <table style="width:50%;margin:auto;display: block;text-align:center;font-size: 12px;">
                <thead>
                <tr>
                    <th>CUOTA</th>
                    <th>FECHA</th>
                    <th>MONTO</th>
                </tr>
                </thead>
                <tbody>
                    ' . $rowTempCuo . '
                </tbody>
        </table>
        </div>';
    }

    $formatter = new NumeroALetras;



    $qrImage = '';
    $hash_Doc = '';


    $tipo_documeto_venta = "COTIZACIÓN #: ";


    $htmlDOM = '';
    $totalLetras = 'SOLES';

    $totalOpGratuita = 0;
    $totalOpExonerada = 0;
    $totalOpinafec = 0;
    $totalOpgravado = 0;
    $totalDescuento = 0;
    $totalOpinafecta = 0;
    $SC = 0;
    $percepcion = 0;
    $total = 0;
    $contador = 1;
    $igv = 0;

    $rowHTML = '';
    $rowHTMLTERT = '';

    foreach ($listaProd1 as $prod) {

      //$datoVenta['cm_tc']

      if ($datoVenta['moneda'] == 2) {
        $prod['precio'] = $prod['precio'] / $datoVenta['cm_tc'];
      }
      $precio = $prod['precio'];
      $importe = $precio * $prod['cantidad'];
      //$subtotal = $subtotal + $importe;
      $total += $importe;
      $tempDescuento = 0;
      $importe -= $tempDescuento;
      $totalDescuento += $tempDescuento;

      $precio = number_format($precio, 2, '.', ',');
      $importe = number_format($importe, 2, '.', ',');
      $tempDescuento = number_format($tempDescuento, 2, '.', ',');
      $prod['codigo'] = trim($prod['codigo']);
      $rowHTML = $rowHTML . "
              <tr>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;'>{$prod['cantidad']}</td>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;'></td>
                <td class='' style=' font-size: 11px; text-align: left;border-left: 1px solid #363636;'>{$prod['descripcion']}</td>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;'>$precio</td>
                 
                
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-right: 1px solid #363636;'>$importe</td>
              </tr>
            ";
      $contador++;
    }

    $cntRowEE = 37;
    $rowHTMLTERT = "";
    for ($tert = 0; $tert < ($cntRowEE - $contador) - $menosRowsNumH; $tert++) {
      $rowHTMLTERT = $rowHTMLTERT . " <tr>
        <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636; color: white'>.</td>

        <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636; '> </td>
        <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636; '> </td> 
        <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636; '> </td>
        
        
        <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-right: 1px solid #363636;'> </td>
      </tr>";
    }




    $totalLetras = $formatter->toInvoice(number_format($total, 2, '.', ''), 2, $datoVenta['moneda'] == 1 ? 'SOLES' : 'DOLARES');

    $htmlCuadroHead = "<div style=' width: 34%;text-align: center; background-color: #ffffff ; float: right;'>

            <div style='padding: 5px;width: 100%; height: 100px; border: 2px solid #1e1e1e' class=''>
            <div style='margin-top:10px'></div>
            <span>RUC: {$datoEmpresa['ruc']}</span><br>
            <div style='margin-top: 10px'></div>
            <span><strong>$tipo_documeto_venta {$datoVenta['numero']}</strong></span><br>
            <div style='margin-top: 10px'></div>
            <span> </span>
            </div>
            </div>
            </div>";

    $this->mpdf->WriteFixedPosHTML("<img style='max-width: 300px;max-height: 85px' src='" . URL::to('files/logos/' . $datoEmpresa['logo']) . "'>", 15, 5, 150, 120);
    $this->mpdf->WriteFixedPosHTML($htmlCuadroHead, 0, 5, 195, 130);
    $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Central Telefónica: </strong> {$datoEmpresa['telefono']}</span>", 15, 27, 210, 130);
    $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Email: </strong> arequipagosac@gmail.com | Web: http://www.arequipago.com/</span>", 15, 32, 210, 130);
    $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Dirección:</strong> <span style='font-size: 10px'>{$datoEmpresa['direccion']}</span></span>", 15, 37, 120, 130);



    $totalOpGratuita = number_format($totalOpGratuita, 2, '.', ',');
    $totalOpExonerada = number_format($totalOpExonerada, 2, '.', ',');
    $totalOpinafec = number_format($totalOpinafec, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');
    $totalDescuento = number_format($totalDescuento, 2, '.', ',');
    $totalOpinafecta = number_format($totalOpinafecta, 2, '.', ',');
    $SC = number_format($SC, 2, '.', ',');
    $percepcion = number_format($percepcion, 2, '.', ',');
    $igv = $total / 1.18 * 0.18;
    $totalOpgravado = $total - $igv;
    $total = number_format($total, 2, '.', ',');
    $igv = number_format($igv, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');



    //$total = number_format($total, 2, '.', ',');


    $monedaVisual = $datoVenta['moneda'] == 1 ? 'SOLES' : 'DOLARES';
    $html = "<div style='width: 1000%;padding-top: 110px; overflow: hidden;clear: both;'>
        <div style='width: 100%;border: 1px solid black'>
        <div style='width: 55%; float: left; '>
        
        <table style='width:100%'>
          <tr>
            <td style=' font-size: 11px;text-align: left'><strong>RUC/DNI:</strong></td>
            <td style=' font-size: 11px;'>{$resultC['documento']}</td>
          </tr>
          <tr>
            <td style=' font-size: 11px;text-align: left'><strong>CLIENTE:</strong></td>
            <td style=' font-size: 11px;'>{$resultC['datos']}</td>
          </tr>
          <tr>
            <td style=' font-size: 11px;text-align: left'><strong>DIRECCIÓN:</strong></td>
            <td style=' font-size: 11px;'>{$resultC['direccion']}</td>
          </tr>
        </table>
        </div>
        <div style='width: 45%; float: left'>
        <table style='width:100%'>
        
          <tr>
            <td style=' font-size: 11px;text-align: left'><strong>FECHA:</strong></td>
            <td style=' font-size: 11px;'>$fecha_emision</td>
          </tr>
          
           <tr>
            <td style=' font-size: 11px;text-align: left'><strong>MONEDA:</strong></td>
            <td style=' font-size: 11px;'>$monedaVisual</td>
          </tr>
          <tr>
            <td style=' font-size: 11px;text-align: left'><strong>PAGO:</strong></td>
            <td style=' font-size: 11px;'>$tipo_pagoC</td>
          </tr>
        </table>
        </div>
        </div>
        
        
        </div>
        $tabla_cuotas
        <div style='width: 100%; padding-top: 20px;'>
        <table style='width:100%;border-bottom: 1px solid #363636;border-collapse: collapse;'>
            <tr style='border-bottom: 1px solid #363636;border-collapse: collapse;'>
            <td style=' font-size: 12px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>CANT</strong></td>
            <td style=' font-size: 12px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>COD</strong></td>
            <td style=' font-size: 12px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>DESCRIPCION</strong></td>
            <td style=' font-size: 12px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>PRECIO U.</strong></td> 
            <td style=' font-size: 12px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>IMPORTE</strong></td>
            
          </tr>
          $rowHTML
          $rowHTMLTERT
              <tr>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-bottom: 1px solid #363636;color: white'>.</td>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-bottom: 1px solid #363636;'> </td>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-bottom: 1px solid #363636;'> </td>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-bottom: 1px solid #363636;'> </td> 
                
                
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-right: 1px solid #363636;border-bottom: 1px solid #363636;'> </td>
              </tr>
         
        
        </table>
        </div>
        
        ";
    $dominio = '';
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $monedahtmlDol = '';

    if ($datoVenta['moneda'] == 2) {
      if ($datoVenta['moneda'] == 2) {
        $totalDolar = number_format($total * $datoVenta['cm_tc'], 2, '.', ",");
      } else {
        $totalDolar = number_format($total / $datoVenta['cm_tc'], 2, '.', ",");
      }
      $simbolfff = $datoVenta['moneda'] == 2 ? 'S/' : '$';
      $monedahtmlDol = "<tr>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px; text-align: right'>Total a Pagar</td>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px;  text-align: right' >$simbolfff $totalDolar</td>
          </tr>";
    }

    $simbolfff22 = $datoVenta['moneda'] == 1 ? 'S/' : '$';

    $this->mpdf->SetHTMLFooter("
        
        <div style='height: 10px;width: 100%; padding-bottom: 0px;font-size: 10px;border: 1px solid black;'>. SON: | $totalLetras</div>
        
        
        <div style='width: 100%; height: 10px;margin-top: 3px;'>
        <div style='float: left; width: 20%;height: 10px '>
        $qrImage
        
        <div style='position: absolute; left: 80px; top: 90px;'></div>
        
        </div>
         <div style='width: 50%; padding-bottom: 5px;font-size: 12px; float: left; padding-top: 10px;'>
            <div style='width: 100%'></div>
            <div style='width: 95%; padding: 3px; font-size: 10px;height: 90px '>
            $hash_Doc
            Detalle:<br>
            Representación impresa de la $tipo_documeto_venta <br>Este documento puede ser validado en $dominio
            </div>
         </div>
         <div style='width: 30%;'>
         <table style='width: 100%;border-top: 1px solid #363636;border-bottom: 1px solid #363636;border-right: 1px solid #363636;border-collapse: collapse;'>
          
          <tr>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px; text-align: right'>Total Op. Gravado:</td>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px;  text-align: right' >$totalOpgravado</td>
          </tr>
          <tr>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px; text-align: right'>IGV:</td>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px;  text-align: right' >$igv</td>
          </tr>
          
          <tr>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px; text-align: right'>Total a Pagar</td>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px;  text-align: right' >$simbolfff22 $total</td>
          </tr>
          $monedahtmlDol
          
        </table>
            </div>
        </div> 
        ");
    /*$this->mpdf->WriteHTML($htmlDOM,\Mpdf\HTMLParserMode::HTML_BODY);*/
    $this->mpdf->Output("Cotizacion{$datoVenta['numero']}.pdf", 'I');
  }

  public function comprobanteNotaE($venta, $nombreXML = '')
  {


    $sql = "SELECT ne.*,ds.nombre as 'nota_nombre',v.id_cliente FROM notas_electronicas ne
      join documentos_sunat ds on ne.tido = ds.id_tido
      join ventas v on ne.id_venta = v.id_venta
      where ne.nota_id =" . $venta;
    $datoVenta = $this->conexion->query($sql)->fetch_assoc();
    $datoEmpresa = $this->conexion->query("select * from empresas where id_empresa=" . $_SESSION['id_empresa'])->fetch_assoc();

    $S_N = $datoVenta['serie'] . '-' . Tools::numeroParaDocumento($datoVenta['numero'], 6);
    $tipoDocNom = $datoVenta['nota_nombre'];
    $resultC = $this->conexion->query("select * from clientes where id_cliente = " . $datoVenta['id_cliente'])->fetch_assoc();
    $dataDocumento = strlen($resultC['documento']) == 8 ? "DNI" : strlen($resultC['documento'] == 11 ? 'RUC' : '');
    $fecha_emision = Tools::formatoFechaVisual($datoVenta['fecha']);

    $formatter = new NumeroALetras;




    $sql = "SELECT * FROM notas_electronicas_sunat where id_notas_electronicas = '$venta' ";
    $qrImage = '';
    $hash_Doc = '';
    if ($rowVS = $this->conexion->query($sql)->fetch_assoc()) {
      $hash_Doc = "HASH: " . $rowVS['hash'] . "<br>";
      $qrCode = new QrCode($rowVS["qr_data"]);
      $qrCode->setSize(150);
      $image = $qrCode->writeString(); //Salida en formato de texto
      $imageData = base64_encode($image);
      $qrImage = '<img style="width: 130px;" src="data:image/png;base64,' . $imageData . '">';
    }

    $tipo_documeto_venta = "";

    if ($datoVenta['tido'] == 3) {
      $tipo_documeto_venta = "NOTA DE CREDITO ELECTRÓNICA";
    } elseif ($datoVenta['tido'] == 4) {
      $tipo_documeto_venta = "NOTA DE DEBITO ELECTRÓNICA";
    }

    $htmlDOM = '';
    $totalLetras = 'SOLES';

    $totalOpGratuita = 0;
    $totalOpExonerada = 0;
    $totalOpinafec = 0;
    $totalOpgravado = 0;
    $totalDescuento = 0;
    $totalOpinafecta = 0;
    $SC = 0;
    $percepcion = 0;
    $total = 0;
    $contador = 1;
    $igv = 0;

    $rowHTML = '';
    $rowHTMLTERT = '';
    $listaProd1 = json_decode($datoVenta['productos'], true);

    foreach ($listaProd1 as $prod) {

      $precio = $prod['precio'];
      $importe = $precio * $prod['cantidad'];
      //$subtotal = $subtotal + $importe;
      $total += $importe;
      $tempDescuento = 0;
      $importe -= $tempDescuento;
      $totalDescuento += $tempDescuento;

      $precio = number_format($precio, 2, '.', ',');
      $importe = number_format($importe, 2, '.', ',');
      $tempDescuento = number_format($tempDescuento, 2, '.', ',');

      $rowHTML = $rowHTML . "
              <tr>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;'>$contador</td>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;'>{$prod['cantidad']}</td>
                <td class='' style=' font-size: 11px; text-align: left;border-left: 1px solid #363636;'>{$prod['descripcion']}</td>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;'>$precio</td>
                 
                
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-right: 1px solid #363636;'>$importe</td>
              </tr>
            ";
      $contador++;
    }

    $cntRowEE = 40;
    $rowHTMLTERT = "";
    for ($tert = 0; $tert < $cntRowEE - $contador; $tert++) {
      $rowHTMLTERT = $rowHTMLTERT . " <tr>
        <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636; color: white'>.</td>
        <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636; '> </td>
        <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636; '> </td> 
        <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636; '> </td>
        
        
        <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-right: 1px solid #363636;'> </td>
      </tr>";
    }

    $totalLetras = $formatter->toInvoice(number_format($total, 2, '.', ''), 2, 'SOLES');

    $htmlCuadroHead = "<div style=' width: 34%;text-align: center; background-color: #ffffff ; float: right;'>

            <div style='width: 100%; height: 100px; border: 2px solid #1e1e1e' class=''>
            <div style='margin-top:10px'></div>
            <span>RUC: {$datoEmpresa['ruc']}</span><br>
            <div style='margin-top: 10px'></div>
            <span><strong>$tipoDocNom ELECTRONICA</strong></span><br>
            <div style='margin-top: 10px'></div>
            <span>Nro. $S_N </span>
            </div>
            </div>
            </div>";
    $dominio = DOMINIO;

    $this->mpdf->WriteFixedPosHTML("<img style='max-width: 300px;max-height: 85px' src='" . URL::to('files/logos/' . $datoEmpresa['logo']) . "'>", 15, 5, 150, 120);
    $this->mpdf->WriteFixedPosHTML($htmlCuadroHead, 0, 5, 195, 130);
    $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Central Telefónica: </strong> {$datoEmpresa['telefono']}</span>", 15, 27, 210, 130);
    $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Email: </strong> arequipagosac@gmail.com | Web: http://www.arequipago.com/</span>", 15, 32, 210, 130);
    $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Dirección:</strong> <span style='font-size: 10px'>{$datoEmpresa['direccion']}</span></span>", 15, 37, 120, 130);



    $totalOpGratuita = number_format($totalOpGratuita, 2, '.', ',');
    $totalOpExonerada = number_format($totalOpExonerada, 2, '.', ',');
    $totalOpinafec = number_format($totalOpinafec, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');
    $totalDescuento = number_format($totalDescuento, 2, '.', ',');
    $totalOpinafecta = number_format($totalOpinafecta, 2, '.', ',');
    $SC = number_format($SC, 2, '.', ',');
    $percepcion = number_format($percepcion, 2, '.', ',');
    $igv = $total / 1.18 * 0.18;
    $totalOpgravado = $total - $igv;
    $total = number_format($total, 2, '.', ',');
    $igv = number_format($igv, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');


    $html = "<div style='width: 1000%;padding-top: 110px; overflow: hidden;clear: both;'>
        <div style='width: 100%;border: 1px solid black'>
        <div style='width: 55%; float: left; '>
        
        <table style='width:100%'>
          <tr>
            <td style=' font-size: 11px;text-align: left'><strong>DOCUMENTO:</strong></td>
            <td style=' font-size: 11px;'>{$resultC['documento']}</td>
          </tr>
          <tr>
            <td style=' font-size: 11px;text-align: left'><strong>CLIENTE:</strong></td>
            <td style=' font-size: 11px;'>{$resultC['datos']}</td>
          </tr>
          <tr>
            <td style=' font-size: 11px;text-align: left'><strong>DIRECCIÓN:</strong></td>
            <td style=' font-size: 11px;'>{$resultC['direccion']}</td>
          </tr>
        </table>
        </div>
        <div style='width: 45%; float: left'>
        <table style='width:100%'>
          <tr>
            <td style=' font-size: 11px;text-align: left'><strong>FECHA EMISIÓN:</strong></td>
            <td style=' font-size: 11px;'>$fecha_emision</td>
          </tr>
          
          </tr>
           <tr>
            <td style=' font-size: 11px;text-align: left'><strong>MONEDA:</strong></td>
            <td style=' font-size: 11px;'>SOLES</td>
          </tr>
        </table>
        </div>
        </div>
        
        
        </div>
        
        <div style='width: 100%; padding-top: 20px;'>
        <table style='width:100%;border-bottom: 1px solid #363636;border-collapse: collapse;'>
            <tr style='border-bottom: 1px solid #363636;border-collapse: collapse;'>
            <td style=' font-size: 12px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>ITEM</strong></td>
            <td style=' font-size: 12px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>CANT</strong></td>
            <td style=' font-size: 12px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>DESCRIPCION</strong></td>
            <td style=' font-size: 12px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>PRECIO U.</strong></td> 
            <td style=' font-size: 12px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>IMPORTE</strong></td>
            
          </tr>
          $rowHTML
          $rowHTMLTERT
              <tr>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-bottom: 1px solid #363636;color: white'>.</td>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-bottom: 1px solid #363636;'> </td>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-bottom: 1px solid #363636;'> </td>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-bottom: 1px solid #363636;'> </td> 
                
                
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-right: 1px solid #363636;border-bottom: 1px solid #363636;'> </td>
              </tr>
         
        
        </table>
        </div>
        
        ";
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $this->mpdf->SetHTMLFooter("
        
        <div style='height: 10px;width: 100%; padding-bottom: 0px;font-size: 10px;border: 1px solid black;'>. SON: | $totalLetras</div>
        
        
        <div style='width: 100%; height: 10px;margin-top: 3px;'>
        <div style='float: left; width: 20%;height: 10px '>
        $qrImage
        
        <div style='position: absolute; left: 80px; top: 90px;'></div>
        
        </div>
         <div style='width: 50%; padding-bottom: 5px;font-size: 12px; float: left; padding-top: 10px;'>
            <div style='width: 100%'></div>
            <div style='width: 95%; padding: 3px; font-size: 10px;height: 90px '>
            $hash_Doc
            Detalle:<br>
            Representación impresa de la $tipo_documeto_venta <br>Este documento puede ser validado en $dominio
            </div>
         </div>
         <div style='width: 30%;'>
         <table style='width: 100%;border-top: 1px solid #363636;border-bottom: 1px solid #363636;border-right: 1px solid #363636;border-collapse: collapse;'>
          
          
          <tr>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px; text-align: right'>Total Op. Gravado:</td>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px;  text-align: right' >$totalOpgravado</td>
          </tr>
          <tr>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px; text-align: right'>IGV:</td>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px;  text-align: right' >$igv</td>
          </tr>
           <tr>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px; text-align: right'>Importe Total:</td>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px;  text-align: right' >$total</td>
          </tr>
          
        </table>
            </div>
        </div> 
        ");
    /*$this->mpdf->WriteHTML($htmlDOM,\Mpdf\HTMLParserMode::HTML_BODY);*/
    $this->mpdf->Output($nombreXML . ".pdf", 'I');
  }

public function guiaRemision($guia, $nombreXML = null)
{
    // Configuramos los márgenes del PDF
    $this->mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 8,
        'margin_right' => 8,
        'margin_top' => 15,
        'margin_bottom' => 5,
        'margin_header' => 0,
        'margin_footer' => 8
    ]);

    try {
        // Validar entrada
        if (!is_numeric($guia) || $guia <= 0) {
            throw new Exception("ID de guía inválido");
        }

        // Check if user is logged in
        $isLoggedIn = isset($_SESSION['id_empresa']);

        // Obtener datos de la guía (CON JOIN correcto a guia_motivos)
        $sql = "SELECT gr.*, gm.nombre as motivo_traslado_nombre 
                FROM guia_remision gr
                LEFT JOIN guia_motivos gm ON gr.motivo_traslado = gm.id
                WHERE gr.id_guia_remision = ?";
        
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar consulta de guía: " . $this->conexion->error);
        }
        
        $stmt->bind_param("i", $guia);
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar consulta de guía: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $datosGuia = $result->fetch_assoc();
        $stmt->close();

        if (!$datosGuia) {
            throw new Exception("No se encontró la guía de remisión con ID: " . $guia);
        }

        // Obtener datos de la empresa
        $empresaSql = "SELECT * FROM empresas WHERE id_empresa = ?";
        $stmt = $this->conexion->prepare($empresaSql);
        if (!$stmt) {
            throw new Exception("Error al preparar consulta de empresa: " . $this->conexion->error);
        }
        
        $empresaId = $isLoggedIn ? $_SESSION['id_empresa'] : $datosGuia['id_empresa'];
        $stmt->bind_param("i", $empresaId);
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar consulta de empresa: " . $stmt->error);
        }
        
        $resultEmpresa = $stmt->get_result();
        $datoEmpresa = $resultEmpresa->fetch_assoc();
        $stmt->close();

        if (!$datoEmpresa) {
            throw new Exception("No se encontraron datos de la empresa");
        }

        // Inicializar variables del cliente
        $nombreCliente = '';
        $numDoc = '';
        $direccionCliente = $datosGuia['dir_llegada'] ?? '';
        $ubigeoTexto = '';

        // Obtener ubigeo textual si existe
        if (!empty($datosGuia['ubigeo'])) {
            $sqlUbigeo = "SELECT 
                            d.nombre AS departamento,
                            p.nombre AS provincia,
                            di.nombre AS distrito
                          FROM ubigeo_inei d 
                          JOIN ubigeo_inei p ON p.departamento = d.departamento AND p.provincia != '00' AND p.distrito = '00' 
                          JOIN ubigeo_inei di ON di.departamento = p.departamento AND di.provincia = p.provincia AND di.distrito != '00' 
                          WHERE d.provincia = '00' AND d.distrito = '00' 
                          AND CONCAT(di.departamento, di.provincia, di.distrito) = ?";
            
            $stmtUbigeo = $this->conexion->prepare($sqlUbigeo);
            if ($stmtUbigeo) {
                $stmtUbigeo->bind_param("s", $datosGuia['ubigeo']);
                if ($stmtUbigeo->execute()) {
                    $resultUbigeo = $stmtUbigeo->get_result();
                    if ($resultUbigeo->num_rows > 0) {
                        $datosUbigeo = $resultUbigeo->fetch_assoc();
                        $ubigeoTexto = strtoupper(trim($datosUbigeo['distrito'])) . ', ' . 
                                      strtoupper(trim($datosUbigeo['provincia'])) . ', ' . 
                                      strtoupper(trim($datosUbigeo['departamento']));
                    }
                }
                $stmtUbigeo->close();
            }
        }

        // Combinar dirección con ubigeo
        $direccionCompleta = $direccionCliente;
        if (!empty($ubigeoTexto)) {
            $direccionCompleta .= ' ' . $ubigeoTexto;
        }

        // Obtener datos del cliente según el tipo de guía
        if (!empty($datosGuia['id_venta'])) {
            // Para guías normales (asociadas a venta)
            $sql = "SELECT v.*, c.* 
                    FROM ventas v 
                    JOIN clientes c ON v.id_cliente = c.id_cliente 
                    WHERE v.id_venta = ?";
            
            $stmt = $this->conexion->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $datosGuia['id_venta']);
                if ($stmt->execute()) {
                    $resultVenta = $stmt->get_result();
                    if ($resultVenta->num_rows > 0) {
                        $datoVenta = $resultVenta->fetch_assoc();
                        $nombreCliente = $datoVenta['datos'] ?? '';
                        $numDoc = (strlen($datoVenta["documento"] ?? '') > 7) ? $datoVenta["documento"] : '';
                    }
                }
                $stmt->close();
            }
        } elseif (!empty($datosGuia['id_cotizacion'])) {
            // Para guías de cotización
            $sql = "SELECT cot.*, c.* 
                    FROM cotizaciones cot 
                    JOIN clientes c ON cot.id_cliente = c.id_cliente 
                    WHERE cot.cotizacion_id = ?";
            
            $stmt = $this->conexion->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $datosGuia['id_cotizacion']);
                if ($stmt->execute()) {
                    $resultCoti = $stmt->get_result();
                    if ($resultCoti->num_rows > 0) {
                        $datoCoti = $resultCoti->fetch_assoc();
                        $nombreCliente = $datoCoti['datos'] ?? '';
                        $numDoc = (strlen($datoCoti["documento"] ?? '') > 7) ? $datoCoti["documento"] : '';
                    }
                }
                $stmt->close();
            }
        } else {
            // Para guías manuales
            $nombreCliente = $datosGuia['destinatario_nombre'] ?? '';
            $numDoc = $datosGuia['destinatario_documento'] ?? '';
        }

        // Obtener placa del vehículo si existe
        $placaVehiculo = '';
        if (!empty($datosGuia['vehiculo'])) {
            $sqlVehiculo = "SELECT placa FROM guia_vehiculo WHERE id = ?";
            $stmtVehiculo = $this->conexion->prepare($sqlVehiculo);
            if ($stmtVehiculo) {
                $stmtVehiculo->bind_param("i", $datosGuia['vehiculo']);
                if ($stmtVehiculo->execute()) {
                    $resultVehiculo = $stmtVehiculo->get_result();
                    if ($resultVehiculo->num_rows > 0) {
                        $datosVehiculo = $resultVehiculo->fetch_assoc();
                        $placaVehiculo = $datosVehiculo['placa'];
                    }
                }
                $stmtVehiculo->close();
            }
        }

        // Obtener productos de la guía (usando la tabla correcta productosv2)
        $query = "SELECT gd.*, p.nombre, p.codigo
                  FROM guia_detalles gd
                  LEFT JOIN productosv2 p ON gd.id_producto = p.idproductosv2
                  WHERE gd.id_guia = ?";
        
        $stmt = $this->conexion->prepare($query);
        if (!$stmt) {
            throw new Exception("Error al preparar consulta de productos: " . $this->conexion->error);
        }

        $stmt->bind_param("i", $guia);
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar consulta de productos: " . $stmt->error);
        }

        $listaProductos = $stmt->get_result();
        if (!$listaProductos) {
            throw new Exception("Error al obtener productos: " . $stmt->error);
        }

        // Generar contenido HTML del PDF
        $html = $this->generarHTMLGuiaRemision($datosGuia, $datoEmpresa, $nombreCliente, $numDoc, $direccionCompleta, $listaProductos, $placaVehiculo);
        
        // Configurar el PDF
        $this->mpdf->WriteHTML($html);
        
        // Generar el PDF
        $nombreArchivo = "GuiaRemision_" . $datosGuia['serie'] . "-" . $datosGuia['numero'] . ".pdf";
        $this->mpdf->Output($nombreArchivo, 'I'); // 'I' para mostrar en navegador, 'D' para descargar
        
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("Error en guiaRemision: " . $e->getMessage());
        
        // Mostrar error amigable al usuario
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Error al generar PDF</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .error { background: #f8d7da; color: #721c24; padding: 20px; border-radius: 5px; }
                .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px; }
            </style>
        </head>
        <body>
            <div class='error'>
                <h3>Error al generar el PDF</h3>
                <p>Ha ocurrido un error al generar la guía de remisión. Por favor, intente nuevamente.</p>
                <p><strong>Detalles técnicos:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
            </div>
            <br>
            <a href='javascript:history.back()' class='btn'>Volver</a>
        </body>
        </html>";
    }
}

// Método auxiliar para generar el HTML del PDF
private function generarHTMLGuiaRemision($datosGuia, $datoEmpresa, $nombreCliente, $numDoc, $direccionCompleta, $listaProductos, $placaVehiculo)
{
    // Parsear datos del chofer
    $nombreChofer = '';
    $tipoDocChofer = '';
    $numDocChofer = '';
    
    if (!empty($datosGuia['chofer_datos'])) {
        $partesChofer = explode(' | ', $datosGuia['chofer_datos']);
        if (count($partesChofer) >= 3) {
            $tipoDocChofer = trim($partesChofer[0]);
            $numDocChofer = trim($partesChofer[1]);
            $nombreChofer = trim($partesChofer[2]);
        }
    }

    // Parsear documento del destinatario
    $tipoDocDestinatario = '';
    $numDocDestinatario = '';
    
    if (!empty($numDoc)) {
        $partesDoc = explode(' | ', $numDoc);
        if (count($partesDoc) >= 2) {
            $tipoDocDestinatario = trim($partesDoc[0]);
            $numDocDestinatario = trim($partesDoc[1]);
        } else {
            $numDocDestinatario = $numDoc;
            $tipoDocDestinatario = 'DNI'; // Por defecto
        }
    }

    // Construir ruta del logo compatible con servidor
    $logoPath = DIRECTORY_SEPARATOR . 'arequipago' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'logo_guide-remision.png';
    
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { 
                font-family: Arial, sans-serif; 
                font-size: 11px; 
                margin: 0; 
                padding: 0; 
                color: #000; 
            }
            
            .header-container {
                width: 100%;
                margin-bottom: 20px;
                position: relative;
            }
            
            .header-left {
                float: left;
                width: 65%;
            }
            
            .header-right {
                float: right;
                width: 30%;
                border: 2px solid #000;
                padding: 10px;
                text-align: center;
            }
            
            .logo-section {
                width: 100%;
                height: 80px;
                position: relative;
            }
            
            .logo-cell {
                width: 90px;
                height: 80px;
                float: left;
            }
            
            .logo {
                width: 90px;
                height: auto;
                margin-top: 2px;
            }
            
            .pipe {
            background-color: red;
                width: 100%;
                border-right: 3px solid #000;    
                margin-left: 0px;
                margin-right: -20px;
                z-index: -900;
            }

            .company-data {
                margin-left: 130px;
                padding-top: 5px;
            }
            
            .clearfix {
                clear: both;
            }
            
            .company-name {
                font-weight: bold;
                font-size: 14px;
                margin-bottom: 5px;
            }
            
            .company-details {
                font-size: 10px;
                line-height: 1.3;
            }
            
            .ruc-box {
                font-weight: bold;
                font-size: 12px;
            }
            
            .guia-title {
                font-weight: bold;
                font-size: 12px;
                margin: 5px 0;
            }
            
            .serie-numero {
                font-weight: bold;
                font-size: 11px;
            }
            
            .fechas-section {
                margin: 15px 0;
                text-align: left;
            }
            
            .section-title {
                font-weight: bold;
                margin: 15px 0 8px 0;
                font-size: 12px;
            }
            
            .section-content {
                margin-bottom: 15px;
                line-height: 1.4;
            }
            
            .data-block {
                border: 1px solid #000;
                padding: 10px;
                margin: 10px 0;
            }
            
            .block-title {
                font-weight: bold;
                font-size: 12px;
                margin-bottom: 8px;
                text-decoration: underline;
            }
            
            .products-table { 
                width: 100%; 
                border-collapse: collapse; 
                margin: 15px 0;
            }
            
            .products-table th, .products-table td { 
                border: 1px solid #000; 
                padding: 6px; 
                text-align: left; 
                font-size: 10px;
            }
            
            .products-table th { 
                background-color: #f0f0f0; 
                font-weight: bold;
                text-align: center;
            }
            
            .firmas-section {
                margin: 30px 0;
                border: 1px solid #000;
                padding: 15px;
            }
            
            .firmas-title {
                text-align: center;
                font-weight: bold;
                font-size: 14px;
                margin-bottom: 20px;
            }
            
            .firmas-container {
                width: 100%;
            }
            
            .firma-left {
                width: 48%;
                float: left;
                text-align: center;
            }
            
            .firma-right {
                width: 48%;
                float: right;
                text-align: center;
            }
            
            .firma-line {
                border-bottom: 1px solid #000;
                height: 40px;
                margin-bottom: 10px;
            }
            
            .firma-label {
                font-weight: bold;
                margin-bottom: 15px;
            }
            
            .firma-data {
                text-align: left;
                line-height: 1.6;
            }
        </style>
    </head>
    <body>';
    
    // Encabezado con logo y datos de empresa
    $html .= '<div class="header-container">
        <div class="header-left">
            <div class="logo-section">
                <div class="logo-cell" style="border-right: 1px solid black; padding-right: 20px">
                    <img src="' . $logoPath . '" alt="Logo" class="logo">
                </div>
          
                    <div class="company-data">
                        <div class="company-name">Arequipa Go</div>
                        <div class="company-details">
                            Urb. Adepa Mz L Lt 15 Distrito de José Luis Bustamante y Rivero<br>
                            Provincia y Departamento de Arequipa<br>
                            Teléfono: +51 993570000<br>
                            https://www.arequipago.com/
                        </div>   
                    </div>

                 
            </div>
        </div>
        <div class="header-right">
            <div class="ruc-box">R.U.C. Nº 20454562349</div>
            <div class="guia-title">GUÍA DE REMISIÓN<br>ELECTRÓNICA REMITENTE</div>
            <div class="serie-numero">' . htmlspecialchars($datosGuia['serie'] ?? '') . ' - ' . str_pad($datosGuia['numero'] ?? '0', 8, '0', STR_PAD_LEFT) . '</div>
        </div>
        <div class="clearfix"></div>
    </div>';
    
    // Fechas
    $html .= '<div class="fechas-section">
        <strong>Fecha Emisión:</strong> ' . htmlspecialchars(date('d/m/Y', strtotime($datosGuia['fecha_emision'] ?? ''))) . '<br>
        <strong>Fecha de Traslado / Entrega al transportista:</strong> ' . htmlspecialchars(date('d/m/Y', strtotime($datosGuia['fecha_emision'] ?? ''))) . '
    </div>';

    // Motivo de traslado
    $motivoTraslado = !empty($datosGuia['motivo_traslado_nombre']) ? htmlspecialchars($datosGuia['motivo_traslado_nombre']) : '______________________________________________________________';
    $html .= '<div class="section-title">Motivo de Traslado</div>
    <div class="section-content">' . $motivoTraslado . '</div>';
    
    // Dirección origen
    $html .= '<div class="section-title">Dirección Origen</div>
    <div class="section-content">' . htmlspecialchars($datosGuia['dir_partida'] ?? '') . '</div>';
    
    // Datos del destinatario
    $html .= '<div class="data-block">
        <div class="block-title">DATOS DEL DESTINATARIO</div>
        <strong>Razón Social / Señor:</strong> ______________________________________________________________<br>
        <strong>Tipo y Nro. Documento:</strong> ______________________________________________________________<br>
        <strong>Dirección Llegada:</strong> ' . htmlspecialchars($direccionCompleta) . '
    </div>';
    
   
    // Datos del transportista
    $razonTransporte = !empty($datosGuia['razon_transporte']) ? htmlspecialchars($datosGuia['razon_transporte']) : '______________________________________________________________';
    $nombreChoferDisplay = !empty($nombreChofer) ? htmlspecialchars($nombreChofer) : '______________________________________________________________';
    $tipoNumDocChofer = (!empty($tipoDocChofer) && !empty($numDocChofer)) ? htmlspecialchars($tipoDocChofer . ' | ' . $numDocChofer) : '______________________________________________________________';
    $licenciaChofer = !empty($datosGuia['chofer_brevete']) ? htmlspecialchars($datosGuia['chofer_brevete']) : '______________________________________________________________';
    $placaDisplay = !empty($placaVehiculo) ? htmlspecialchars($placaVehiculo) : '______________________________________________________________';
    $observacionesDisplay = !empty($datosGuia['observaciones']) ? htmlspecialchars($datosGuia['observaciones']) : '______________________________________________________________';
    
    // Preparar peso y bultos
    $pesoDisplay = !empty($datosGuia['peso']) ? htmlspecialchars($datosGuia['peso']) . ' kg' : '______________________________________________________________';
    $bultosDisplay = !empty($datosGuia['nro_bultos']) ? htmlspecialchars($datosGuia['nro_bultos']) : '______________________________________________________________';
    
    $html .= '<div class="data-block">
        <div class="block-title">DATOS DEL TRANSPORTISTA</div>
        <strong>Razón Social:</strong> ' . $razonTransporte . '<br>
        <strong>Nombre Chofer:</strong> ' . $nombreChoferDisplay . '<br>
        <strong>Tipo y Nro de Documento (conductor):</strong> ' . $tipoNumDocChofer . '<br>
        <strong>No. Licencia de Conducir:</strong> ' . $licenciaChofer . '<br>
        <strong>Placa / Carreta:</strong> ' . $placaDisplay . '<br>
        <strong>Peso Total:</strong> ' . $pesoDisplay . '<br>
        <strong>Nro. de Bultos:</strong> ' . $bultosDisplay . '<br>
        <strong>Observaciones:</strong> ' . $observacionesDisplay . '
    </div>';
    
    // Tabla de productos
    $html .= '<table class="products-table">
        <thead>
            <tr>
                <th>Ítems</th>
                <th>Código</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Unidad</th>
            </tr>
        </thead>
        <tbody>';
    
    if ($listaProductos->num_rows > 0) {
        $contador = 1;
        while ($producto = $listaProductos->fetch_assoc()) {
            // Separar código y descripción del campo detalles
            $codigoProducto = '';
            $nombreProducto = '';
            
            if (!empty($producto['detalles'])) {
                $partes = explode(' | ', $producto['detalles']);
                if (count($partes) >= 2) {
                    $codigoProducto = trim($partes[0]);
                    $nombreProducto = trim($partes[1]);
                } else {
                    // Si no hay separador, usar todo como descripción
                    $nombreProducto = trim($producto['detalles']);
                }
            }
            
            // Usar datos del JOIN si están disponibles, sino usar los separados del campo detalles
            $codigoFinal = !empty($producto['codigo']) ? $producto['codigo'] : $codigoProducto;
            $nombreFinal = !empty($producto['nombre']) ? $producto['nombre'] : $nombreProducto;
            
            $html .= '<tr>
                <td style="text-align: center;">' . $contador . '</td>
                <td>' . htmlspecialchars($codigoFinal) . '</td>
                <td>' . htmlspecialchars($nombreFinal) . '</td>
                <td style="text-align: center;">' . htmlspecialchars($producto['cantidad'] ?? '') . '</td>
                <td style="text-align: center;">' . htmlspecialchars($producto['unidad'] ?? 'UND') . '</td>
            </tr>';
            $contador++;
        }
    } else {
        $html .= '<tr><td colspan="5" style="text-align: center;">No hay productos registrados</td></tr>';
    }
    
    $html .= '</tbody></table>';
    
    // Sección de firmas
    $nombreChoferFirma = !empty($nombreChofer) ? htmlspecialchars($nombreChofer) : '';
    $dniChoferFirma = !empty($numDocChofer) ? htmlspecialchars($numDocChofer) : '';
    $nombreDestinatarioFirma = !empty($datosGuia['destinatario_nombre']) ? htmlspecialchars($datosGuia['destinatario_nombre']) : (!empty($nombreCliente) ? htmlspecialchars($nombreCliente) : '');
    $dniDestinatarioFirma = !empty($numDocDestinatario) ? htmlspecialchars($numDocDestinatario) : '';
    
    $html .= '<div class="firmas-section">
        <div class="firmas-title">RECIBÍ CONFORME</div>
        <div class="firmas-container">
            <div class="firma-left">
                <div class="firma-line"></div>
                <div class="firma-label">FIRMA CHOFER</div>
                <div class="firma-data">
                    <strong>NOMBRE:</strong> ' . $nombreChoferFirma . '<br>
                    <strong>DNI:</strong> ' . $dniChoferFirma . '
                </div>
            </div>
            <div class="firma-right">
                <div class="firma-line"></div>
                <div class="firma-label">FIRMA DESTINATARIO</div>
                <div class="firma-data">
                    <strong>Razón Social / Señor:</strong> <br>
                    <strong>Tipo y Nro. Documento:</strong> <br>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>';
    
    $html .= '</body></html>';
    
    return $html;
}

  
  public function comprobanteVentaMa4($venta, $nombreXML = '-')
  {



    $this->mpdf = new \Mpdf\Mpdf([
      //"orientation"=>"P",
      //'margin_bottom' => 5,
      //'margin_top' => 2,
      //'margin_left' => 4,
      'format' => [210, 148],
      //'margin_right' => 4,
      'mode' => 'utf-8',
    ]);



    $listaProd1 = $this->conexion->query("
        SELECT productos_ventas.*, 
              REPLACE(productos_ventas.descripcion, '-', '|') as descripcion, -- Se reemplazan los '-' por '|' en la descripción
              p.codigo 
        FROM productos_ventas 
        JOIN productosv2 p ON p.idproductosv2 = productos_ventas.id_producto 
        WHERE id_venta=" . $venta
    ); // Se mantiene el JOIN pero ahora se usa la descripción de productos_ventas en lugar de la de productosv2

    $listaProd2 = $this->conexion->query("SELECT * FROM ventas_servicios WHERE id_venta=" . $venta);
    $ventaSunat = $this->conexion->query("SELECT * FROM ventas_sunat WHERE id_venta=" . $venta)->fetch_assoc();
    $guiaRealionada = '';
    $sql = "SELECT * FROM guia_remision where id_venta = $venta";
    if ($rowGuia = $this->conexion->query($sql)->fetch_assoc()) {
      $guiaRealionada = $rowGuia["serie"] . '-' . Tools::numeroParaDocumento($rowGuia["numero"], 6);
    }

    $sql = "select * from ventas where id_venta=" . $venta;
    $datoVenta = $this->conexion->query($sql)->fetch_assoc();
    $monedaVisual = $datoVenta["moneda"] == "1" ? "SOLES" : 'DOLAR';
    $datoEmpresa = $this->conexion->query("select * from empresas where id_empresa=" . $datoVenta['id_empresa'])->fetch_assoc();


    /*   var_dump("SELECT * FROM sucursales WHERE cod_sucursal ='{$_SESSION['sucursal']}' AND empresa_id=" . $datoVenta['id_empresa']);
    die();  */
    /*   if (is_null($datoSucursal)) {
      var_dump('es nulo');
      die();
    } else {
      var_dump($datoSucursal);
      die();
    } */


    $igv_venta_sel = $datoVenta['igv'];

    $S_N = $datoVenta['serie'] . '-' . Tools::numeroParaDocumento($datoVenta['numero'], 6);
    $tipoDocNom = $datoVenta['id_tido'] == 1 ? 'BOLETA' : 'FACTURA';
    $resultC = $this->conexion->query("select * from clientes where id_cliente = " . $datoVenta['id_cliente'])->fetch_assoc();
    $dataDocumento = strlen($resultC['documento']) == 8 ? "DNI" : strlen($resultC['documento'] == 11 ? 'RUC' : '');
    $fecha_emision = Tools::formatoFechaVisual($datoVenta['fecha_emision']);
    $fecha_vencimiento = Tools::formatoFechaVisual($datoVenta['fecha_vencimiento']);

    // MODIFICADO: Añadida condición para tipo de pago 3 (GRATIS)
    if ($datoVenta["id_tipo_pago"] == '1') {
      $tipo_pagoC = 'CONTADO';
    } elseif ($datoVenta["id_tipo_pago"] == '3') {
      $tipo_pagoC = 'GRATIS';
    } else {
      $tipo_pagoC = 'CREDITO';
    }


    $tabla_cuotas = '';

    $menosRowsNumH = 0;

    if ($datoVenta["id_tipo_pago"] == '2') {
      $rowTempCuo = '';
      $sql = "SELECT * FROM dias_ventas WHERE id_venta='$venta'";
      $resulTempCuo = $this->conexion->query($sql);
      $contadorCuota = 0;
      $menosRowsNumH = 1;
      foreach ($resulTempCuo as $cuotTemp) {
        $menosRowsNumH++;
        $contadorCuota++;
        $tempNum = Tools::numeroParaDocumento($contadorCuota, 2);
        $tempFecha = Tools::formatoFechaVisual($cuotTemp['fecha']);
        $tempMonto = Tools::money($cuotTemp['monto']);
        $rowTempCuo .= "
            <tr>
                <td>Cuota $tempNum</td>
                <td>$tempFecha </td>
                <td>S/ $tempMonto</td>
            </tr>
            ";
      }
      $tabla_cuotas = '<div style="width: 100%;">
        <table style="width:50%;margin:auto;display: block;text-align:center;font-size: 10px;">
                <thead>
                <tr>
                    <th>CUOTA</th>
                    <th>FECHA</th>
                    <th>MONTO</th>
                </tr>
                </thead>
                <tbody>
                    ' . $rowTempCuo . '
                </tbody>
        </table>
        </div>';
    }

    $formatter = new NumeroALetras;


    $sql = "SELECT * FROM ventas_sunat where id_venta = '$venta' ";
    $qrImage = '';
    $hash_Doc = '';
    if ($rowVS = $this->conexion->query($sql)->fetch_assoc()) {
      $hash_Doc = "HASH: " . $rowVS['hash'] . "<br>";
      $qrCode = new QrCode($rowVS["qr_data"]);
      $qrCode->setSize(150);
      $image = $qrCode->writeString(); //Salida en formato de texto
      $imageData = base64_encode($image);
      $qrImage = '<img style="width: 100px;" src="data:image/png;base64,' . $imageData . '">';
    }

    $tipo_documeto_venta = "";

    if ($datoVenta['id_tido'] == 1) {
      $tipo_documeto_venta = "BOLETA DE VENTA ELECTRÓNICA";
    } elseif ($datoVenta['id_tido'] == 2) {
      $tipo_documeto_venta = "FACTURA DE VENTA ELECTRÓNICA";
    } elseif ($datoVenta['id_tido'] == 6) {
      $qrImage = '';
      $tipo_documeto_venta = "NOTA DE VENTA  ELECTRÓNICA";
    }

    $htmlDOM = '';
    $totalLetras = 'SOLES';

    $totalOpGratuita = 0;
    $totalOpExonerada = 0;
    $totalOpinafec = 0;
    $totalOpgravado = 0;
    $totalDescuento = 0;
    $totalOpinafecta = 0;
    $SC = 0;
    $percepcion = 0;
    $total = 0;
    $contador = 1;
    $igv = 0;

    $rowHTML = '';
    $rowHTMLTERT = '';

    foreach ($listaProd1 as $prod) {

      $precio = $prod['precio'];
      $importe = $precio * $prod['cantidad'];
      //$subtotal = $subtotal + $importe;
      $total += $importe;
      $tempDescuento = 0;
      $importe -= $tempDescuento;
      $totalDescuento += $tempDescuento;

      $precio = $precio;
      $importe = number_format($importe, 2, '.', ',');
      $tempDescuento = number_format($tempDescuento, 2, '.', ',');
      $descripcionFormateada = $prod['descripcion'];


      $rowHTML = $rowHTML . "
              <tr>
                <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636;'>$contador</td>
                <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636;'>{$prod['cantidad']}</td>
                <td class='' style=' font-size: 10px; text-align: left;border-left: 1px solid #363636;'>{$descripcionFormateada}</td>
                <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636;'>$precio</td>
                 
                
                <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636;border-right: 1px solid #363636;'>$importe</td>
              </tr>
            ";
      $contador++;
    }
    foreach ($listaProd2 as $prod) {

      $precio = $prod['monto'];
      $importe = $precio * $prod['cantidad'];
      //$subtotal = $subtotal + $importe;
      $total += $importe;
      $tempDescuento = 0;
      $importe -= $tempDescuento;
      $totalDescuento += $tempDescuento;

      $precio = number_format($precio, 2, '.', ',');
      $importe = number_format($importe, 2, '.', ',');
      $tempDescuento = number_format($tempDescuento, 2, '.', ',');

      $rowHTML = $rowHTML . "
              <tr>
                <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636;'>$contador</td>
                <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636;'>{$prod['cantidad']}</td>
                <td class='' style=' font-size: 10px; text-align: left;border-left: 1px solid #363636;'>{$descripcionFormateada}</td>
                <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636;'>$precio</td>
                
                
                <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636;border-right: 1px solid #363636;'>$importe</td>
              </tr>
            ";
      $contador++;
    }
    $cntRowEE = 9;
    $rowHTMLTERT = "";
    for ($tert = 0; $tert < ($cntRowEE - $contador) - $menosRowsNumH; $tert++) {
      $rowHTMLTERT = $rowHTMLTERT . " <tr>
        <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636; color: white'>.</td>
        <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636; '> </td>
        <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636; '> </td> 
        <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636; '> </td>
        
        
        <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636;border-right: 1px solid #363636;'> </td>
      </tr>";
    }




    $totalLetras = $formatter->toInvoice(number_format($total, 2, '.', ''), 2, $datoVenta["moneda"] == "1" ? "SOLES" : 'DOLARES');

    $htmlCuadroHead = "<div style=' width: 34%;text-align: center; background-color: #ffffff ; float: right;'>

            <div style='padding: 5px;width: 100%; height: 70px; border: 2px solid #1e1e1e' class=''>
                <div style='margin-top:5px'></div>
            <span style='font-size: 12px;'>RUC: {$datoEmpresa['ruc']}</span><br>
            <div style='margin-top: 5px'></div>
            <span style='font-size: 12px;'><strong>$tipo_documeto_venta</strong></span><br>
            <div style='margin-top: 5px'></div>
            <span style='font-size: 12px;'>Nro. $S_N </span>
            </div>
            </div>
            </div>";


    $this->mpdf->WriteFixedPosHTML("<div ><img style='height: 95px;width: 360px;' src='" .
      URL::to('files/logos/' . $datoEmpresa['logo']) . "'></div>", 15, 5, 100, 120);

    $this->mpdf->WriteFixedPosHTML($htmlCuadroHead, 0, 5, 195, 130);
    $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Central Telefónica: </strong> {$datoEmpresa['telefono']}</span>", 15, 32, 210, 130);




    $datoSucursal = $this->conexion->query("SELECT * FROM sucursales WHERE cod_sucursal ='{$datoVenta['sucursal']}' AND empresa_id=" . $datoVenta['id_empresa'])->fetch_assoc();
    if ($datoVenta['sucursal'] == '1') {
      $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Dirección:</strong> <span style='font-size: 10px'>{$datoEmpresa['direccion']}</span></span>", 15, 36, 120, 130);
    } else {
      if (is_null($datoSucursal)) {
        $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Dirección:</strong> <span style='font-size: 10px'>{$datoEmpresa['direccion']}</span></span>", 15, 36, 120, 130);
      } else {
        $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Dirección:</strong> <span style='font-size: 10px'>{$datoSucursal['direccion']}</span></span>", 15, 36, 120, 130);
      }
    }


    $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Email: </strong> arequipagosac@gmail.com | Web: http://www.arequipago.com/</span>", 15, 40, 210, 130);




    $totalOpGratuita = number_format($totalOpGratuita, 2, '.', ',');
    $totalOpExonerada = number_format($totalOpExonerada, 2, '.', ',');
    $totalOpinafec = number_format($totalOpinafec, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');
    $totalDescuento = number_format($totalDescuento, 2, '.', ',');
    $totalOpinafecta = number_format($totalOpinafecta, 2, '.', ',');
    $SC = number_format($SC, 2, '.', ',');
    $percepcion = number_format($percepcion, 2, '.', ',');
    $igv = $total / ($igv_venta_sel + 1) * $igv_venta_sel;
    $totalOpgravado = $total - $igv;
    $total = number_format($total, 2, '.', '.');
    $igv = number_format($igv, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');



    //$total = number_format($total, 2, '.', ',');
    /*   $datoSucursal = $this->conexion->query("SELECT * FROM sucursales WHERE cod_sucursal ='{$_SESSION['sucursal']}' AND empresa_id=" . $datoVenta['id_empresa'])->fetch_assoc(); */
    /*  $as = $this->conexion->query("SELECT * FROM sucursales WHERE cod_sucursal ='2' AND empresa_id=" . 28)->fetch_assoc();
    var_dump($as);
    die(); */

    if ($datoVenta['sucursal'] != '1') {
      if (is_null($datoSucursal)) {
        $resultC['direccion'] = $resultC['direccion'];
      } else {
        $resultC['direccion'] = $datoSucursal['direccion'];
      }
    }


    $html = "<div style='width: 100%;padding-top: 120px; overflow: hidden;clear: both;'>
        <div style='width: 100%;border: 1px solid black;'>
        <div style='width: 55%; float: left; '>
        
        <table style='width:100%'>
          <tr>
            <td style=' font-size: 10px;text-align: left'><strong>RUC/DNI:</strong></td>
            <td style=' font-size: 10px;'>{$resultC['documento']}</td>
          </tr>
          <tr>
            <td style=' font-size: 10px;text-align: left'><strong>CLIENTE:</strong></td>
            <td style=' font-size: 10px;'>{$resultC['datos']}</td>
          </tr>
          <tr>
            <td style=' font-size: 10px;text-align: left'><strong>DIRECCIÓN:</strong></td>
            <td style=' font-size: 10px;'>{$resultC['direccion']}</td>
          </tr>
          <tr>
            <td style=' font-size: 10px;text-align: left'><strong>NRO GUÍA:</strong></td>
            <td style=' font-size: 10px;'>$guiaRealionada</td>
          </tr>
        </table>
        </div>
        <div style='width: 45%; float: left'>
        <table style='width:100%'>
        
          <tr>
            <td style=' font-size: 10px;text-align: left'><strong>FECHA EMISIÓN:</strong></td>
            <td style=' font-size: 10px;'>$fecha_emision</td>
          </tr>
          <tr>
            <td style=' font-size: 10px;text-align: left'><strong>FECHA VENCIMIENTO:</strong></td>
            <td style=' font-size: 10px;'>$fecha_vencimiento</td>
          </tr>
          
           <tr>
            <td style=' font-size: 10px;text-align: left'><strong>MONEDA:</strong></td>
            <td style=' font-size: 10px;'>$monedaVisual</td>
          </tr>
          <tr>
            <td style=' font-size: 10px;text-align: left'><strong>PAGO:</strong></td>
            <td style=' font-size: 10px;'>$tipo_pagoC</td>
          </tr>
        </table>
        </div>
        </div>
        
        
        </div>
        $tabla_cuotas
        <div style='width: 100%; padding-top: 5px;'>
        <table style='width:100%;border-bottom: 1px solid #363636;border-collapse: collapse;'>
            <tr style='border-bottom: 1px solid #363636;border-collapse: collapse;'>
            <td style=' font-size: 10px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>ITEM</strong></td>
            <td style=' font-size: 10px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>CANT</strong></td>
            <td style=' font-size: 10px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>DESCRIPCION</strong></td>
            <td style=' font-size: 10px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>PRECIO U.</strong></td> 
            <td style=' font-size: 10px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>IMPORTE</strong></td>
            
          </tr>
          $rowHTML
          $rowHTMLTERT
             
         
        
        </table>
        </div>
        
        ";
    $dominio = DOMINIO;
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

    /*$this->mpdf->SetHTMLFooter("<div style=' width: 100%;'>
        <div style='height: 10px;width: 100%; padding-bottom: 0px;font-size: 9px;border: 1px solid black;'>. SON: | $totalLetras</div>
        <div style='width: 100%;margin-top: 5px;'>
                <div style='width: 18%;float: left;'>
                    $qrImage
                </div>
                <div style='width: 58%;float: left; font-size: 12px;'>
                     $hash_Doc
                        Detalle:<br>
                        Representación impresa de la $tipo_documeto_venta <br>Este documento puede ser validado en $dominio
                </div>
                <div style='width: 24%;float: left; font-size: 12px;'>
                <table style='width: 100%;border-top: 1px solid #363636;border-bottom: 1px solid #363636;border-right: 1px solid #363636;border-collapse: collapse;'>
                  <tr>
                    <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px; text-align: right'>Total Op. Gravado:</td>
                    <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px;  text-align: right' >$totalOpgravado</td>
                  </tr>
                  <tr>
                    <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px; text-align: right'>IGV:</td>
                    <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px;  text-align: right' >$igv</td>
                  </tr>
                  
                  <tr>
                    <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px; text-align: right'>Total a Pagar</td>
                    <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px;  text-align: right' >$total</td>
                  </tr>
                  
                </table>
                </div>
        </div>
 </div>");*/
    if ($datoVenta['apli_igv'] == '0') {
      $totalOpgravado = $total;
      $igv = '0.00';
    }
    //die();

    $this->mpdf->SetHTMLFooter("
        <div style='height: 3px; width:100%;'></div>
        <div style='height: 10px;width: 100%; padding-bottom: 0px;font-size: 9px;border: 1px solid black;'>. SON: | $totalLetras</div>
        
        
        <div style='width: 100%; height: 10px;  '>
        
        <div style='float: left; width: 20%; '>
        $qrImage
         
        
        </div>
         <div style='width: 50%; padding-bottom:  0px;font-size: 12px; float: left; padding-top: 5px;'>
            <div style='width: 100%'></div>
            <div style='width: 95%; padding: 3px; font-size: 10px;height: 90px '>
            $hash_Doc
            Detalle:<br>
            Representación impresa de la $tipo_documeto_venta <br>Este documento puede ser validado en $dominio
            <br><b>Observaciones:</b>{$datoVenta['observacion']}
            </div>
         </div>
         <div style='width: 30%; padding-top: 5px;'>
         <table style='width: 100%;border-top: 1px solid #363636;border-bottom: 1px solid #363636;border-right: 1px solid #363636;border-collapse: collapse;'>
          
          <tr>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px; text-align: right'>Total Op. Gravado:</td>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px;  text-align: right' >$totalOpgravado</td>
          </tr>
          <tr>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px; text-align: right'>IGV:</td>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px;  text-align: right' >$igv</td>
          </tr>
          
          <tr>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px; text-align: right'>Total a Pagar</td>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px;  text-align: right' >$total</td>
          </tr>
          
        </table>
            </div>
        </div> 
        ");

    $this->mpdf->Output($nombreXML . ".pdf", 'I');
  }
  public function comprobanteVenta($venta, $nombreXML = '-')
  {
    $this->comprobanteVentaGen("I", $venta, $nombreXML ? $nombreXML : '-');
  }

  public function comprobanteVentaBinario($venta, $nombreXML = '-')
  {
    $this->comprobanteVentaGen("F", $venta, $nombreXML ? $nombreXML : '-');
  }

  private function comprobanteVentaGen($dist, $venta, $nombreXML)
  {


    $guiaRealionada = '';

    $listaProd1 = $this->conexion->query("
        SELECT productos_ventas.*, 
              REPLACE(productos_ventas.descripcion, '-', '|') AS descripcion, -- Se reemplazan los '-' por '|' en la descripción
              p.codigo 
        FROM productos_ventas 
        JOIN productosv2 p ON p.idproductosv2 = productos_ventas.id_producto 
        WHERE id_venta=" . $venta
    );
    
    $listaProd2 = $this->conexion->query("SELECT * FROM ventas_servicios WHERE id_venta=" . $venta);
    $ventaSunat = $this->conexion->query("SELECT * FROM ventas_sunat WHERE id_venta=" . $venta)->fetch_assoc();

    $sql = "SELECT * FROM guia_remision where id_venta = $venta";
    if ($rowGuia = $this->conexion->query($sql)->fetch_assoc()) {
      $guiaRealionada = $rowGuia["serie"] . '-' . Tools::numeroParaDocumento($rowGuia["numero"], 6);
    }

    $sql = "select * from ventas where id_venta=" . $venta;
    $datoVenta = $this->conexion->query($sql)->fetch_assoc();
    $datoEmpresa = $this->conexion->query("select * from empresas where id_empresa=" . $datoVenta['id_empresa'])->fetch_assoc();

    $igv_venta_sel = $datoVenta['igv'];

    $isSEgundoPago = false;
    $pagoData = '';
    if ($datoVenta['pagado2']) {
      $isSEgundoPago = true;
      $sql = "select *  from metodo_pago where id_metodo_pago='{$datoVenta['medoto_pago2_id']}'";
      $metodo2 = $this->conexion->query($sql)->fetch_assoc();
      $sql = "select *  from metodo_pago where id_metodo_pago='{$datoVenta['medoto_pago_id']}'";
      $metodo1 = $this->conexion->query($sql)->fetch_assoc();

      $pagoData = "<b>METODO DE PAGO 1 \"{$metodo1['nombre']}\"</b>: S/{$datoVenta['pagado']}, <b>Y METODO DE PAGO 2 \"{$metodo2['nombre']}\"</b>: S/{$datoVenta['pagado2']}";
      } else {
      $sql = "select *  from metodo_pago where id_metodo_pago='{$datoVenta['medoto_pago_id']}'";
      
      $metodo1 = $this->conexion->query($sql)->fetch_assoc();
      if (!$metodo1) {
          // Puedes poner "Gratis", "Efectivo", o el nombre que tú quieras
          $metodo1 = [
              'nombre' => 'FLOTA' // o 'Efectivo', 'Gratis', etc.
          ];
      }
      $montoPagadoooo = $datoVenta['pagado'] ? $datoVenta['pagado'] : $datoVenta["total"];
      $pagoData = "<b>METODO DE PAGO \"{$metodo1['nombre']}\"</b>: S/$montoPagadoooo";
    }


    $S_N = $datoVenta['serie'] . '-' . Tools::numeroParaDocumento($datoVenta['numero'], 6);
    $tipoDocNom = $datoVenta['id_tido'] == 1 ? 'BOLETA' : 'FACTURA';
    $resultC = $this->conexion->query("select * from clientes where id_cliente = " . $datoVenta['id_cliente'])->fetch_assoc();
    $dataDocumento = strlen($resultC['documento']) == 8 ? "DNI" : strlen($resultC['documento'] == 11 ? 'RUC' : '');
    $fecha_emision = Tools::formatoFechaVisual($datoVenta['fecha_emision']);
    $fecha_vencimiento = Tools::formatoFechaVisual($datoVenta['fecha_vencimiento']);

   // MODIFICADO: Añadida condición para tipo de pago 3 (GRATIS)
    if ($datoVenta["id_tipo_pago"] == '1') {
      $tipo_pagoC = 'CONTADO';
    } elseif ($datoVenta["id_tipo_pago"] == '3') {
      $tipo_pagoC = 'GRATIS';
    } else {
      $tipo_pagoC = 'CREDITO';
    }

    $tabla_cuotas = '';

    $menosRowsNumH = 0;

    if ($datoVenta["id_tipo_pago"] == '2') {
      $rowTempCuo = '';
      $sql = "SELECT * FROM dias_ventas WHERE id_venta='$venta'";
      $resulTempCuo = $this->conexion->query($sql);
      $contadorCuota = 0;
      $menosRowsNumH = 1;
      foreach ($resulTempCuo as $cuotTemp) {
        $menosRowsNumH++;
        $contadorCuota++;
        $tempNum = Tools::numeroParaDocumento($contadorCuota, 2);
        $tempFecha = Tools::formatoFechaVisual($cuotTemp['fecha']);
        $tempMonto = Tools::money($cuotTemp['monto']);
        $rowTempCuo .= "
            <tr>
                <td>Cuota $tempNum</td>
                <td>$tempFecha </td>
                <td>S/ $tempMonto</td>
            </tr>
            ";
      }
      $tabla_cuotas = '<div style="width: 100%;">
        <table style="width:50%;margin:auto;display: block;text-align:center;font-size: 12px;">
                <thead>
                <tr>
                    <th>CUOTA</th>
                    <th>FECHA</th>
                    <th>MONTO</th>
                </tr>
                </thead>
                <tbody>
                    ' . $rowTempCuo . '
                </tbody>
        </table>
        </div>';
    }

    $formatter = new NumeroALetras;


    $sql = "SELECT * FROM ventas_sunat where id_venta = '$venta' ";
    $qrImage = '';
    $hash_Doc = '';
    if ($rowVS = $this->conexion->query($sql)->fetch_assoc()) {
      $hash_Doc = "HASH: " . $rowVS['hash'] . "<br>";
      $qrCode = new QrCode($rowVS["qr_data"]);
      $qrCode->setSize(150);
      $image = $qrCode->writeString(); //Salida en formato de texto
      $imageData = base64_encode($image);
      $qrImage = '<img style="width: 130px;" src="data:image/png;base64,' . $imageData . '">';
    }

    $tipo_documeto_venta = "";

    if ($datoVenta['id_tido'] == 1) {
      $tipo_documeto_venta = "BOLETA DE VENTA ELECTRÓNICA";
    } elseif ($datoVenta['id_tido'] == 2) {
      $tipo_documeto_venta = "FACTURA DE VENTA ELECTRÓNICA";
    } elseif ($datoVenta['id_tido'] == 6) {
      $qrImage = '';
      $tipo_documeto_venta = "NOTA DE VENTA  ELECTRÓNICA";
    }

    $htmlDOM = '';
    $totalLetras = 'SOLES';

    $totalOpGratuita = 0;
    $totalOpExonerada = 0;
    $totalOpinafec = 0;
    $totalOpgravado = 0;
    $totalDescuento = 0;
    $totalOpinafecta = 0;
    $SC = 0;
    $percepcion = 0;
    $total = 0;
    $contador = 1;
    $igv = 0;

    $rowHTML = '';
    $rowHTMLTERT = '';

    foreach ($listaProd1 as $prod) {

      $precio = $prod['precio'];
      $importe = $precio * $prod['cantidad'];
      //$subtotal = $subtotal + $importe;
      $total += $importe;
      $tempDescuento = 0;
      $importe -= $tempDescuento;
      $totalDescuento += $tempDescuento;

      $precio = $precio;
      $importe = number_format($importe, 2, '.', ',');
      $tempDescuento = number_format($tempDescuento, 2, '.', ',');

      $rowHTML = $rowHTML . "
              <tr>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;'>$contador</td>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;'>{$prod['cantidad']}</td>
                <td class='' style=' font-size: 11px; text-align: left;border-left: 1px solid #363636;'>{$prod['descripcion']}</td>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;'>$precio</td>
                 
                
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-right: 1px solid #363636;'>$importe</td>
              </tr>
            ";
      $contador++;
    }
    foreach ($listaProd2 as $prod) {

      $precio = $prod['monto'];
      $importe = $precio * $prod['cantidad'];
      //$subtotal = $subtotal + $importe;
      $total += $importe;
      $tempDescuento = 0;
      $importe -= $tempDescuento;
      $totalDescuento += $tempDescuento;

      $precio = number_format($precio, 2, '.', ',');
      $importe = number_format($importe, 2, '.', ',');
      $tempDescuento = number_format($tempDescuento, 2, '.', ',');

      $rowHTML = $rowHTML . "
              <tr>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;'>$contador</td>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;'>{$prod['cantidad']}</td>
                <td class='' style=' font-size: 11px; text-align: left;border-left: 1px solid #363636;'>{$prod['descripcion']}</td>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;'>$precio</td>
                
                
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-right: 1px solid #363636;'>$importe</td>
              </tr>
            ";
      $contador++;
    }
    $cntRowEE = 37;
    $rowHTMLTERT = "";
    for ($tert = 0; $tert < ($cntRowEE - $contador) - $menosRowsNumH; $tert++) {
      $rowHTMLTERT = $rowHTMLTERT . " <tr>
        <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636; color: white'>.</td>
        <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636; '> </td>
        <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636; '> </td> 
        <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636; '> </td>
        
        
        <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-right: 1px solid #363636;'> </td>
      </tr>";
    }




    $totalLetras = $formatter->toInvoice(number_format($total, 2, '.', ''), 2, $datoVenta["moneda"] == "1" ? "SOLES" : 'DOLARES');

    $htmlCuadroHead = "<div style=' width: 34%;text-align: center; background-color: #ffffff ; float: right;'>

            <div style='padding: 5px;width: 100%; height: 100px; border: 2px solid #1e1e1e' class=''>
            <div style='margin-top:10px'></div>
            <span>RUC: {$datoEmpresa['ruc']}</span><br>
            <div style='margin-top: 10px'></div>
            <span><strong>$tipo_documeto_venta</strong></span><br>
            <div style='margin-top: 10px'></div>
            <span>Nro. $S_N </span>
            </div>
            </div>
            </div>";
    /**/
    $this->mpdf->WriteFixedPosHTML("<div><img style='width: 410px;height: 120px;' src='" .
      URL::to('files/logos/' . $datoEmpresa['logo']) . "'></div>", 15, 5, 110, 120);

    $this->mpdf->WriteFixedPosHTML($htmlCuadroHead, 0, 5, 195, 130);
    $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Central Telefónica: </strong> 993570000</span>", 15, 38, 210, 130);

    /* $sql = "select * from ventas where id_venta=" . $venta;
    $datoVenta = $this->conexion->query($sql)->fetch_assoc(); */

    $datoSucursal = $this->conexion->query("SELECT * FROM sucursales WHERE cod_sucursal ='{$datoVenta['sucursal']}' AND empresa_id=" . $datoVenta['id_empresa'])->fetch_assoc();
    if ($datoVenta['sucursal'] == '1') {
      $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Dirección:</strong> <span style='font-size: 10px'>URB LA ESPERANZA MZ L LT. 15 / JLBR - Arequipa</span></span>", 15, 42, 120, 130);
    } else {
      if (is_null($datoSucursal)) {
        $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Dirección:</strong> <span style='font-size: 10px'>{$datoEmpresa['direccion']}</span></span>", 15, 42, 120, 130);
      } else {
        $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Dirección:</strong> <span style='font-size: 10px'>{$datoSucursal['direccion']}</span></span>", 15, 42, 120, 130);
      }
    }

    $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Email: </strong>arequipagosac@gmail.com
| http://www.arequipago.com/</span>", 15, 46, 210, 130);


    $totalOpGratuita = number_format($totalOpGratuita, 2, '.', ',');
    $totalOpExonerada = number_format($totalOpExonerada, 2, '.', ',');
    $totalOpinafec = number_format($totalOpinafec, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');
    $totalDescuento = number_format($totalDescuento, 2, '.', ',');
    $totalOpinafecta = number_format($totalOpinafecta, 2, '.', ',');
    $SC = number_format($SC, 2, '.', ',');
    $percepcion = number_format($percepcion, 2, '.', ',');
    $igv = $total / ($igv_venta_sel + 1) * $igv_venta_sel;
    $totalOpgravado = $total - $igv;
    $total = number_format($total, 2, '.', ',');
    $igv = number_format($igv, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');



    //$total = number_format($total, 2, '.', ',');

    $monedaVisual = $datoVenta["moneda"] == "1" ? "SOLES" : 'DOLAR';

    $html = "<div style='width: 1000%;padding-top: 150px; overflow: hidden;clear: both;'>
        <div style='width: 100%;border: 1px solid black'>
        <div style='width: 55%; float: left; '>
        
        <table style='width:100%'>
          <tr>
            <td style=' font-size: 11px;text-align: left'><strong>RUC/DNI:</strong></td>
            <td style=' font-size: 11px;'>{$resultC['documento']}</td>
          </tr>
          <tr>
            <td style=' font-size: 11px;text-align: left'><strong>CLIENTE:</strong></td>
            <td style=' font-size: 11px;'>{$resultC['datos']}</td>
          </tr>
          <tr>
            <td style=' font-size: 11px;text-align: left'><strong>DIRECCIÓN:</strong></td>
            <td style=' font-size: 11px;'>{$resultC['direccion']}</td>
          </tr>
          <tr>
            <td style=' font-size: 11px;text-align: left'><strong>NRO GUÍA:</strong></td>
            <td style=' font-size: 11px;'>$guiaRealionada</td>
          </tr>
         
        </table>
        </div>
        <div style='width: 45%; float: left'>
        <table style='width:100%'>
        
          <tr>
            <td style=' font-size: 11px;text-align: left'><strong>FECHA EMISIÓN:</strong></td>
            <td style=' font-size: 11px;'>$fecha_emision</td>
          </tr>
          <tr>
            <td style=' font-size: 11px;text-align: left'><strong>FECHA VENCIMIENTO:</strong></td>
            <td style=' font-size: 11px;'>$fecha_vencimiento</td>
          </tr>
          
           <tr>
            <td style=' font-size: 11px;text-align: left'><strong>MONEDA:</strong></td>
            <td style=' font-size: 11px;'>$monedaVisual</td>
          </tr>
          <tr>
            <td style=' font-size: 11px;text-align: left'><strong>PAGO:</strong></td>
            <td style=' font-size: 11px;'>$tipo_pagoC</td>
          </tr>
        </table>
        </div>
       <div style='text-align: center'><span style='font-size: 11px;'> $pagoData</span></div>
        </div>
        
        
        </div>
        $tabla_cuotas
        <div style='width: 100%; padding-top: 20px;'>
        <table style='width:100%;border-bottom: 1px solid #363636;border-collapse: collapse;'>
            <tr style='border-bottom: 1px solid #363636;border-collapse: collapse;'>
            <td style=' font-size: 12px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>ITEM</strong></td>
            <td style=' font-size: 12px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>CANT</strong></td>
            <td style=' font-size: 12px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>DESCRIPCION</strong></td>
            <td style=' font-size: 12px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>PRECIO U.</strong></td> 
            <td style=' font-size: 12px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>IMPORTE</strong></td>
            
          </tr>
          $rowHTML
          $rowHTMLTERT
              <tr>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-bottom: 1px solid #363636;color: white'>.</td>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-bottom: 1px solid #363636;'> </td>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-bottom: 1px solid #363636;'> </td>
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-bottom: 1px solid #363636;'> </td> 
                
                
                <td class='' style=' font-size: 11px; text-align: center;border-left: 1px solid #363636;border-right: 1px solid #363636;border-bottom: 1px solid #363636;'> </td>
              </tr>
         
        
        </table>
        </div>



        
        ";

    if ($datoVenta['apli_igv'] == '0') {
      $igv = '0.00';
      $totalOpgravado = $total;
    }
    $dominio = DOMINIO;
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $this->mpdf->SetHTMLFooter("
        
        <div style='height: 10px;width: 100%; padding-bottom: 0px;font-size: 10px;border: 1px solid black;'>. SON: | $totalLetras</div>
        
        
        <div style='width: 100%; height: 10px;margin-top: 3px;'>
        <div style='float: left; width: 20%;height: 10px '>
        $qrImage
        
        <div style='position: absolute; left: 80px; top: 90px;'></div>
        
        </div>
         <div style='width: 50%; padding-bottom: 5px;font-size: 12px; float: left; padding-top: 10px;'>
            <div style='width: 100%'></div>
            <div style='width: 95%; padding: 3px; font-size: 10px;height: 90px '>
            $hash_Doc
            Detalle:<br>
            Representación impresa de la $tipo_documeto_venta <br>Este documento puede ser validado en $dominio
            <br><br><b>Observaciones:</b> {$datoVenta['observacion']}
            </div> 
         </div>
         <div style='width: 30%;'>
         <table style='width: 100%;border-top: 1px solid #363636;border-bottom: 1px solid #363636;border-right: 1px solid #363636;border-collapse: collapse;'>
          
          <tr>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px; text-align: right'>Total Op. Gravado:</td>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px;  text-align: right' >$totalOpgravado</td>
          </tr>
          <tr>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px; text-align: right'>IGV:</td>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px;  text-align: right' >$igv</td>
          </tr>
          
          <tr>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px; text-align: right'>Total a Pagar</td>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 12px;  text-align: right' >$total</td>
          </tr>
          
        </table>
         </div>
         
        </div> 
        ");
    /*$this->mpdf->WriteHTML($htmlDOM,\Mpdf\HTMLParserMode::HTML_BODY);*/
    if ($dist == 'I') {
      $this->mpdf->Output((is_string($nombreXML) ? $nombreXML : '') . ".pdf", $dist);
    } elseif ($dist == 'F') {
      $this->mpdf->Output(base64_decode((is_string($nombreXML) ? $nombreXML : '')), $dist);
    }
  }

  public function imprimirvoucher5_6cm($id)
  {
    $this->venta->setIdVenta($id);

    /* echo "<pre>"; */
    $this->mpdf = new \Mpdf\Mpdf([
      'margin_bottom' => 5,
      'margin_top' => 7,
      'margin_left' => 4,
      'margin_right' => 4,
      'mode' => 'utf-8',
    ]);

    $this->venta->setIdVenta($id);
    $sql = "SELECT * FROM ventas where id_venta =$id ";
    $dataVenta = $this->conexion->query($sql)->fetch_assoc();
    $igv_venta_sel = $dataVenta['igv'];
    $sql = "SELECT * FROM empresas where id_empresa = '{$dataVenta['id_empresa']}' ";
    $dataEmpresa = $this->conexion->query($sql)->fetch_assoc();

    $sql = "SELECT * FROM clientes where id_cliente = '{$dataVenta['id_cliente']}' ";
    $dataCliente = $this->conexion->query($sql)->fetch_assoc();

    $sql = "SELECT pv.*, 
               REPLACE(pv.descripcion, '-', '|') AS descripcion, -- Se reemplazan los '-' por '|' en la descripción
               p.codigo 
        FROM productos_ventas pv 
        JOIN productosv2 p ON p.idproductosv2 = pv.id_producto 
        WHERE pv.id_venta = $id";
    $dataProVenta = $this->conexion->query($sql);

    $sql = "SELECT * FROM ventas_servicios where id_venta =$id ";
    $dataServVenta = $this->conexion->query($sql);

    $guiaRealionada = '';
    $sql = "SELECT * FROM guia_remision where id_venta = $id";
    if ($rowGuia = $this->conexion->query($sql)->fetch_assoc()) {
      $guiaRealionada = $rowGuia["serie"] . '-' . Tools::numeroParaDocumento($rowGuia["numero"], 6);
    }

    $clienteDoc = $dataCliente['documento'];

    $rowsHTML = '';
    $contador = 1;

    // MODIFICADO: Añadida condición para tipo de pago 3 (GRATIS)
    if ($dataVenta["id_tipo_pago"] == '1') {
      $tipo_pagoC = 'CONTADO';
    } elseif ($dataVenta["id_tipo_pago"] == '3') {
      $tipo_pagoC = 'GRATIS';
    } else {
      $tipo_pagoC = 'CREDITO';
    }
    $tabla_cuotas = '';
    $menosRowsNumH = 0;

    $totalImporte = 0;

    if ($dataVenta["id_tipo_pago"] == '2') {
      $rowTempCuo = '';
      $sql = "SELECT * FROM dias_ventas WHERE id_venta='$id'";
      $resulTempCuo = $this->conexion->query($sql);
      $contadorCuota = 0;
      $menosRowsNumH = 10;
      foreach ($resulTempCuo as $cuotTemp) {
        $menosRowsNumH += 11;
        $menosRowsNumH++;
        $contadorCuota++;
        $tempNum = Tools::numeroParaDocumento($contadorCuota, 2);
        $tempFecha = Tools::formatoFechaVisual($cuotTemp['fecha']);
        $tempMonto = Tools::money($cuotTemp['monto']);
        $rowTempCuo .= "
            <tr>
                <td>Cuota $tempNum</td>
                <td>$tempFecha </td>
                <td>S/ $tempMonto</td>
            </tr>
            ";
      }
      $tabla_cuotas = '

<div style="width: 100%; text-align: center;margin-top:3px">
<strong><span style="font-size:10px">Cuotas de pago</span></strong>
</div>
<div style="width: 100%;">
        <table style="width:90%;margin:auto;display: block;text-align:center;font-size: 10px;">
                <thead>
                <tr>
                    <th>CUOTA</th>
                    <th>FECHA</th>
                    <th>MONTO</th>
                </tr>
                </thead>
                <tbody>
                    ' . $rowTempCuo . '
                </tbody>
        </table>
        </div>';
    }

    $rowTamanioExtra = 0;

    foreach ($dataServVenta as $ser) {
      $totalM = $ser['cantidad'] * $ser['monto'];
      $totalImporte += $totalM;
      $motoFor = number_format($ser['monto'], 2, ".", "");
      $totalM = number_format($totalM, 2, ".", "");
      $cantidadss = number_format($ser['cantidad'], 0, "", "");
      $rowsHTML .= "<tr>
            <td style='font-size: 8px'>$cantidadss</td>
            <td style='font-size: 8px'>{$ser['descripcion']}</td>
            <td style='font-size: 8px'>$motoFor</td>
            <td style='font-size: 8px'>$totalM</td>
            </tr>";
      $contador++;
      $rowTamanioExtra += 23;
    }

    foreach ($dataProVenta as $ser) {
      $totalM = $ser['cantidad'] * $ser['precio'];
      $totalImporte += $totalM;
      $motoFor = number_format($ser['precio'], 2, ".", "");
      $totalM = number_format($totalM, 2, ".", "");
      $cantidadss = number_format($ser['cantidad'], 0, "", "");
      $rowsHTML .= "<tr>
            <td style='font-size: 8px'>$cantidadss</td>
            <td style='font-size: 8px'>{$ser['descripcion']}</td>
            <td style='font-size: 8px'>$motoFor</td>
            <td style='font-size: 8px'>$totalM</td>
            </tr>";
      $contador++;
      $rowTamanioExtra += 23;
    }


    $sql = "SELECT * FROM ventas_sunat where id_venta = '$id' ";
    $qrImage = '';
    if ($rowVS = $this->conexion->query($sql)->fetch_assoc()) {
      $qrCode = new QrCode($rowVS["qr_data"]);
      $qrCode->setSize(150);
      $image = $qrCode->writeString(); //Salida en formato de texto
      $imageData = base64_encode($image);
      $qrImage = '<img style="width: 130px;" src="data:image/png;base64,' . $imageData . '">';
    }

    $data = '';
    $detalles = [];
    $fecha = date('d/m/Y', strtotime($dataVenta['fecha_emision']));
    $fechaVenc = date('d/m/Y', strtotime($dataVenta['fecha_vencimiento']));
    $vendedor = '';
    $cliente = $dataCliente['datos'];
    $telefono_ = '';
    $direccion_ = $dataVenta['direccion'];
    $puesto = '';
    $zona = '';

    $doc_S_N = $dataVenta["serie"] . "-" . Tools::numeroParaDocumento($dataVenta['numero'], 6);
    $formatter = new NumeroALetras;
    $totalLetras = $formatter->toInvoice(number_format($totalImporte, 2, '.', ''), 2, $dataVenta["moneda"] == "1" ? "SOLES" : 'DOLARES');
    $totalIGVNumeros = number_format($totalImporte / ($igv_venta_sel + 1) * $igv_venta_sel, 2, '.', '');
    $totalNumeros = number_format($totalImporte, 2, '.', '');

    $nom_emp = $dataEmpresa['razon_social'];
    $telefono = $dataEmpresa['telefono'];
    $direccion = $dataEmpresa['direccion'];
    $propaganda = $dataEmpresa['propaganda'];

    $tipo_documeto_venta = "";

    if ($dataVenta['id_tido'] == 1) {
      $tipo_documeto_venta = "BOLETA DE VENTA ELECTRÓNICA";
    } elseif ($dataVenta['id_tido'] == 2) {
      $tipo_documeto_venta = "FACTURA DE VENTA ELECTRÓNICA";
    } elseif ($dataVenta['id_tido'] == 6) {
      $qrImage = '';
      $tipo_documeto_venta = "NOTA DE VENTA  ELECTRÓNICA";
      $rowTamanioExtra -= 40;
    }


    $this->mpdf->AddPageByArray([
      "orientation" => "P",
      "newformat" => [56, 190 + $rowTamanioExtra + $menosRowsNumH]
    ]);
    $dominio = DOMINIO;


    if ($dataVenta['apli_igv'] == '0') {
      $totalIGVNumeros = '0.00';
    }
    /*var_dump($totalIGVNumeros);
      die();*/
    $sql = "select * from ventas where id_venta=" . $id;
    $datoVenta = $this->conexion->query($sql)->fetch_assoc();
    $datoSucursal = $this->conexion->query("SELECT * FROM sucursales WHERE cod_sucursal ='{$datoVenta['sucursal']}' AND empresa_id=" . $datoVenta['id_empresa'])->fetch_assoc();
    if ($datoVenta['sucursal'] != '1') {
      if (!is_null($datoSucursal)) {
        $direccion_ = $datoSucursal['direccion'];
      }
    }


    $html = "
<div style='width: 100%'>
<table style='width:100%;margin-bottom: 10px'>
  <tr>
    <td align='center'>
      <img style=' max-width: 80%;' src='" . URL::to('files/logos/' . $dataEmpresa['logo']) . "'>
</td>
</tr>
</table>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 10px;font-weight: bold'>{$dataEmpresa["razon_social"]} </span>
    </div>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 9px'>RUC: {$dataEmpresa["ruc"]}</span>
    </div>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 9px'>$direccion</span>
    </div>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 9px'>$telefono</span>
    </div>
    
    <div style='width: 100%;text-align: center;margin-top: 10px;'>
    <span style='font-size: 9px;font-weight: bold'>$propaganda</span><br>
        <span style='font-size: 9px;font-weight: bold'>$tipo_documeto_venta</span><br>
        <span style='font-size: 9px;'>$doc_S_N</span>
        
    </div>
    <hr>
    <div style='width: 100%;text-align: center'>
        <table style='width:100%'>
          <tr>
            <td style='font-size: 8px;width: 25%'><strong>Fecha E:</strong></td>
            <td style='font-size: 8px;'>$fecha</td>
          </tr>
          <tr>
            <td style='font-size: 8px;width: 25%'><strong>Fecha V:</strong></td>
            <td style='font-size: 8px;'>$fechaVenc</td>
          </tr>
          <tr>
            <td style='font-size: 8px;width: 25%'><strong>RUC/DNI:</strong></td>
            <td style='font-size: 8px;'>$clienteDoc</td>
          </tr>
          <tr>
            <td style='font-size: 8px'><strong>Cliente:</strong></td>
            <td style='font-size: 8px'>$cliente</td>
          </tr>
          <tr>
            <td style='font-size: 7.5px'><strong>Dirección:</strong></td>
            <td style='font-size: 7.5px'>$direccion_</td>
          </tr>
           <tr>
            <td style='font-size: 7.5px'><strong>Pago:</strong></td>
            <td style='font-size: 7.5px'>$tipo_pagoC</td>
          </tr>
          <tr>
            <td style='font-size: 8px'><strong>Nro. Guia:</strong></td>
            <td style='font-size: 8px'>$guiaRealionada</td>
          </tr>
        </table>
    </div>
    
     <div style='width: 100%;text-align: center'>
        <span style='font-size: 10px;'>--------------------- Productos --------------------</span>
    </div>
    <div style='width: 100%;text-align: center'>
        <table style='width: 100%'>
            <tr>
                <td style='border-bottom:1px solid black;font-size: 8px'>CNT</td>
                <td style='border-bottom:1px solid black;font-size: 8px'>DESCRIPCION</td>
                <td style='border-bottom:1px solid black;font-size: 8px'>PR.U.</td>
                <td style='border-bottom:1px solid black;font-size: 8px;text-align: center'>IMPR.</td>
            </tr>
            $rowsHTML
            <tr>
                <td style='border-top:1px solid black; font-size: 8px;text-align: right' colspan='3'>IGV</td>
                <td style='border-top:1px solid black;font-size: 8px;text-align: center' >$totalIGVNumeros</td>
            </tr>
            <tr>
                <td style=' font-size: 8px;text-align: right' colspan='3'>Total</td>
                <td style='font-size: 8px;text-align: center' >$totalNumeros</td>
            </tr>
        </table>
    </div>
    <br>
    <div style='width: 100%;'>
        <span style='font-size: 8px'>SON: $totalLetras</span>
    </div>
    $tabla_cuotas
    <div style='width: 100%;'>
        <span style='font-size: 8px'><b>Observaciones:</b> {$dataVenta['observacion']}</span>
    </div>
    <br>
     <div style='width: 100%;text-align: center'>
        <span style='font-size: 8px'>Representación impresa de la $tipo_documeto_venta <br>Este documento puede ser validado en $dominio</span>
    </div>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 8px'>Gracias por su preferencia....</span>
    </div>
    <div style='width: 100%; '>
        $qrImage
    </div>
    
    
</div>
";
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $this->mpdf->Output();
  }
public function imprimirvoucher8cm($id)
  {
    $this->venta->setIdVenta($id);

    /* echo "<pre>"; */
    $this->mpdf = new \Mpdf\Mpdf([
      'margin_bottom' => 5,
      'margin_top' => 10,
      'margin_left' => 4,
      'margin_right' => 4,
      'mode' => 'utf-8',
    ]);

    $this->venta->setIdVenta($id);
    $sql = "SELECT * FROM ventas where id_venta =$id ";
    $dataVenta = $this->conexion->query($sql)->fetch_assoc();

    $sql = "SELECT * FROM usuarios where usuario_id = '{$dataVenta["id_vendedor"]}' ";
    $cajero = $this->conexion->query($sql)->fetch_assoc();

    $sql = "SELECT u.nombres FROM cotizaciones c
    INNER JOIN usuarios u on u.usuario_id =  c.id_usuario
    where c.cotizacion_id = '{$dataVenta["id_coti"]}'";
    $vendor = $this->conexion->query($sql)->fetch_assoc();

    $trCajero = "";
    $trVendor = "";
    if ($cajero["nombres"]) {
      $trCajero = " <tr>
                <td style='font-size: 11px'><strong>Cajero:</strong></td>
                <td style='font-size: 11px'>{$cajero["nombres"]}</td>
              </tr>";
    }

    if ($vendor["nombres"]) {
      $trVendor = " <tr>
                <td style='font-size: 11px'><strong>Vendedor:</strong></td>
                <td style='font-size: 11px'>{$vendor["nombres"]}</td>
              </tr>";
    }

    $igv_venta_sel = $dataVenta['igv'];

    $sql = "SELECT * FROM empresas where id_empresa = '{$dataVenta['id_empresa']}' ";
    $dataEmpresa = $this->conexion->query($sql)->fetch_assoc();

    $sql = "SELECT * FROM clientes where id_cliente = '{$dataVenta['id_cliente']}' ";
    $dataCliente = $this->conexion->query($sql)->fetch_assoc();

    $sql = "SELECT pv.*, 
               REPLACE(pv.descripcion, '-', '|') AS descripcion, -- Se reemplazan los '-' por '|' en la descripción
               p.codigo 
        FROM productos_ventas pv 
        JOIN productosv2 p ON p.idproductosv2 = pv.id_producto 
        WHERE pv.id_venta = $id";

    $dataProVenta = $this->conexion->query($sql);

    $sql = "SELECT * FROM ventas_servicios where id_venta =$id ";
    $dataServVenta = $this->conexion->query($sql);

    $guiaRealionada = '';
    $sql = "SELECT * FROM guia_remision where id_venta = $id";
    if ($rowGuia = $this->conexion->query($sql)->fetch_assoc()) {
      $guiaRealionada = $rowGuia["serie"] . '-' . Tools::numeroParaDocumento($rowGuia["numero"], 6);
    }

    $rowsHTML = '';
    $contador = 1;

    if ($dataVenta["id_tipo_pago"] == '1') {
      $tipo_pagoC = 'CONTADO';
    } elseif ($dataVenta["id_tipo_pago"] == '3') {
      $tipo_pagoC = 'GRATIS';
    } else {
      $tipo_pagoC = 'CREDITO';
    }

    $tabla_cuotas = '';
    $menosRowsNumH = 0;

    $totalImporte = 0;

    if ($dataVenta["id_tipo_pago"] == '2') {
      $rowTempCuo = '';
      $sql = "SELECT * FROM dias_ventas WHERE id_venta='$id'";
      $resulTempCuo = $this->conexion->query($sql);
      $contadorCuota = 0;
      $menosRowsNumH = 10;
      foreach ($resulTempCuo as $cuotTemp) {
        $menosRowsNumH += 10;
        $menosRowsNumH++;
        $contadorCuota++;
        $tempNum = Tools::numeroParaDocumento($contadorCuota, 2);
        $tempFecha = Tools::formatoFechaVisual($cuotTemp['fecha']);
        $tempMonto = Tools::money($cuotTemp['monto']);
        $rowTempCuo .= "
            <tr>
                <td>Cuota $tempNum</td>
                <td>$tempFecha </td>
                <td>S/ $tempMonto</td>
            </tr>
            ";
      }
      $tabla_cuotas = '

<div style="width: 100%; text-align: center;margin-top:3px;">
<strong><span  >Cuotas de pago</span></strong>
</div>
<div style="width: 100%;">
        <table style="width:90%;margin:auto;display: block;text-align:center;font-size: 10px;">
                <thead>
                <tr>
                    <th>CUOTA</th>
                    <th>FECHA</th>
                    <th>MONTO</th>
                </tr>
                </thead>
                <tbody>
                    ' . $rowTempCuo . '
                </tbody>
        </table>
        </div>';
    }

    $rowTamanioExtra = 0;

    foreach ($dataServVenta as $ser) {
      $totalM = $ser['cantidad'] * $ser['monto'];
      $totalImporte += $totalM;
      $motoFor = number_format($ser['monto'], 2, ".", "");
      $totalM = number_format($totalM, 2, ".", "");
      $cantidadss = number_format($ser['cantidad'], 0, "", "");
      $rowsHTML .= "<tr>
            <td style='font-size: 10px'>$cantidadss</td>
            <td style='font-size: 10px'>{$ser['descripcion']}</td>
            <td style='font-size: 10px'>$motoFor</td>
            <td style='font-size: 10px'>$totalM</td>
            </tr>";
      $contador++;
      $rowTamanioExtra += 10;
    }

    foreach ($dataProVenta as $ser) {
      $totalM = $ser['cantidad'] * $ser['precio'];
      $totalImporte += $totalM;
      $motoFor = number_format($ser['precio'], 2, ".", "");
      $totalM = number_format($totalM, 2, ".", "");
      $cantidadss = number_format($ser['cantidad'], 0, "", "");
      $rowsHTML .= "<tr>
            <td style='font-size: 10px'>$cantidadss</td>
            <td style='font-size: 10px'>{$ser['descripcion']}</td>
            <td style='font-size: 10px'>$motoFor</td>
            <td style='font-size: 10px'>$totalM</td>
            </tr>";
      $contador++;
      $rowTamanioExtra += 10;
    }


    $sql = "SELECT * FROM ventas_sunat where id_venta = '$id' ";
    $qrImage = '';
    if ($rowVS = $this->conexion->query($sql)->fetch_assoc()) {
      $qrCode = new QrCode($rowVS["qr_data"]);
      $qrCode->setSize(150);
      $image = $qrCode->writeString(); //Salida en formato de texto
      $imageData = base64_encode($image);
      $qrImage = '<img style="width: 130px;" src="data:image/png;base64,' . $imageData . '">';
    }

    $data = '';
    $detalles = [];
    $fecha = date('d/m/Y', strtotime($dataVenta['fecha_emision']));
    $fechaVenc = date('d/m/Y', strtotime($dataVenta['fecha_vencimiento']));
    $vendedor = '';
    $cliente = $dataCliente['datos'];

    $clienteDoc = $dataCliente['documento'];

    $telefono_ = '';
    $direccion_ = $dataVenta['direccion'];
    $puesto = '';
    $zona = '';

    $doc_S_N = $dataVenta["serie"] . "-" . Tools::numeroParaDocumento($dataVenta['numero'], 6);
    $formatter = new NumeroALetras;
    $totalLetras = $formatter->toInvoice(number_format($totalImporte, 2, '.', ''), 2, $dataVenta["moneda"] == "1" ? "SOLES" : 'DOLARES');
    $totalIGVNumeros = number_format($totalImporte / ($igv_venta_sel + 1) * $igv_venta_sel, 2, '.', '');
    $totalNumeros = number_format($totalImporte, 2, '.', '');

    $nom_emp = $dataEmpresa['razon_social'];
    $telefono = $dataEmpresa['telefono'];
    $direccion = $dataEmpresa['direccion'];
    $propaganda = $dataEmpresa['propaganda'];
    $tipo_documeto_venta = "";

    if ($dataVenta['id_tido'] == 1) {
      $tipo_documeto_venta = "BOLETA DE VENTA ELECTRÓNICA";
    } elseif ($dataVenta['id_tido'] == 2) {
      $tipo_documeto_venta = "FACTURA DE VENTA ELECTRÓNICA";
    } elseif ($dataVenta['id_tido'] == 6) {
      $qrImage = '';
      $tipo_documeto_venta = "NOTA DE VENTA  ELECTRÓNICA";
      $rowTamanioExtra -= 30;
    }

    $this->mpdf->AddPageByArray([
      "orientation" => "P",
      "newformat" => [80, 240 + $rowTamanioExtra + $menosRowsNumH]
    ]);
    $dominio = DOMINIO;

    if ($dataVenta['apli_igv'] == '0') {
      $totalIGVNumeros = '0.00';
    }

    $sql = "select * from ventas where id_venta=" . $id;
    $datoVenta = $this->conexion->query($sql)->fetch_assoc();
    $datoSucursal = $this->conexion->query("SELECT * FROM sucursales WHERE cod_sucursal ='{$datoVenta['sucursal']}' AND empresa_id=" . $datoVenta['id_empresa'])->fetch_assoc();
    if ($datoVenta['sucursal'] != '1') {
      if (!is_null($datoSucursal)) {
        $direccion_ = $datoSucursal['direccion'];
      }
    }


    $html = "
<div style='width: 100%'>
<table style='width:100%;margin-bottom: 10px'>
  <tr>
    <td align='center'>
      <img style=' max-width: 85%;' src='" . URL::to('files/logos/' . $dataEmpresa['logo']) . "'>
</td>
</tr>
</table>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 13px;font-weight: bold'>{$dataEmpresa["razon_social"]} </span>
    </div>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 12px'>RUC: {$dataEmpresa["ruc"]}</span>
    </div>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 12px'>$direccion</span>
    </div>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 12px'>$telefono</span>
    </div>
    
    <div style='width: 100%;text-align: center;margin-top: 10px;'>
        <span style='font-size: 13px;font-weight: bold'>$propaganda</span><br>
        <span style='font-size: 13px;font-weight: bold'>$tipo_documeto_venta</span><br>
        <span style='font-size: 13px;'>$doc_S_N</span>
        
    </div>
    <hr>
    <div style='width: 100%;text-align: center'>
        <table style='width:100%'>
          <tr>
            <td style='font-size: 11px;width: 25%'><strong>Fecha E:</strong></td>
            <td style='font-size: 11px;'>$fecha</td>
          </tr>
          <tr>
            <td style='font-size: 11px;width: 25%'><strong>Fecha V:</strong></td>
            <td style='font-size: 11px;'>$fechaVenc</td>
          </tr>
           <tr>
            <td style='font-size: 11px;width: 25%'><strong>RUC/DNI:</strong></td>
            <td style='font-size: 11px;'>$clienteDoc</td>
          </tr>
          <tr>
            <td style='font-size: 11px'><strong>Cliente:</strong></td>
            <td style='font-size: 11px'>$cliente</td>
          </tr>
          <tr>
            <td style='font-size: 11px'><strong>Dirección:</strong></td>
            <td style='font-size: 11px'>$direccion_</td>
          </tr>
          <tr>
            <td style='font-size: 11px'><strong>Pago:</strong></td>
            <td style='font-size: 11px'>$tipo_pagoC</td>
          </tr>
          <tr>
            <td style='font-size: 11px'><strong>Nro. Guia:</strong></td>
            <td style='font-size: 11px'>$guiaRealionada</td>
          </tr>
          $trCajero
          $trVendor
        </table>
    </div>
    
     <div style='width: 100%;text-align: center'>
        <span style='font-size: 13px;'>---------------------- Productos -----------------------</span>
    </div>
    <div style='width: 100%;text-align: center'>
        <table style='width: 100%'>
            <tr>
                <td style='border-bottom:1px solid black;font-size: 11px'>CNT</td>
                <td style='border-bottom:1px solid black;font-size: 11px'>DESCRIPCION</td>
                <td style='border-bottom:1px solid black;font-size: 11px'>PR.U.</td>
                <td style='border-bottom:1px solid black;font-size: 11px;text-align: center'>IMPR.</td>
            </tr>
            $rowsHTML
            <tr>
                <td style='border-top:1px solid black; font-size: 11px;text-align: right' colspan='3'>IGV</td>
                <td style='border-top:1px solid black;font-size: 11px;text-align: center' >$totalIGVNumeros</td>
            </tr>
            <tr>
                <td style=' font-size: 11px;text-align: right' colspan='3'>Total</td>
                <td style='font-size: 11px;text-align: center' >$totalNumeros</td>
            </tr>
        </table>
    </div>
    <br>
    <div style='width: 100%;'>
        <span style='font-size: 11px'>SON: $totalLetras</span>
    </div>
    $tabla_cuotas
     <div style='width: 100%;'>
        <span style='font-size: 12px'><b>Observaciones:</b> {$dataVenta['observacion']}</span>
    </div>
    <br>
     <div style='width: 100%;text-align: center'>
        <span style='font-size: 12px'>Representación impresa de la $tipo_documeto_venta <br>Este documento puede ser validado en $dominio</span>
    </div>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 12px'>Gracias por su preferencia....</span>
    </div>
    <div style='width: 100%; '>
        $qrImage
    </div>
    
    
</div>
";
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $this->mpdf->Output();
  }
}
