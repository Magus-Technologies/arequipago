<?php

require_once 'utils/lib/vendor/autoload.php';
require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/exel/vendor/autoload.php';
require_once 'app/models/TipoProductoModel.php';
require_once 'app/models/CategoriaProductoModel.php';
require_once 'app/models/Usuario.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GenerarReporte extends Controller
{
    private $conexion;
    /*  private $mpdf; */

    public function __construct()
    {
        /*  $this->mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']); */
        $this->conexion = (new Conexion())->getConexion();
    }


    public function ingresosEgresos($id)
    {
        $mpdf = new \Mpdf\Mpdf([
            'margin_bottom' => 5,
            'margin_top' => 10,
            'margin_left' => 4,
            'margin_right' => 4,
            'mode' => 'utf-8',
        ]);

        $empresa = $this->conexion->query("select * from empresas
        where id_empresa = '{$_SESSION['id_empresa']}'")->fetch_assoc();

        $rowHmtl = '';
        $sql = "SELECT ingreso_egreso.*,productos.descripcion,IF(ingreso_egreso.tipo = 'e', 'Egreso', 'Ingreso') AS tipoIntercambio FROM ingreso_egreso JOIN productos ON ingreso_egreso.id_producto=productos.id_producto WHERE intercambio_id = '$id'";
        $result = $this->conexion->query($sql)->fetch_assoc();

        $sql = "SELECT * FROM usuarios WHERE usuario_id = {$result['id_usuario']}";
        $resul2 = $this->conexion->query($sql)->fetch_assoc();
        /*  $sql */
        $dominio = DOMINIO;
        $rowHmtl .= "<tr>
        <td style='border-bottom:1px solid black;font-size: 11px'>{$result['cantidad']}</td>
        <td style='border-bottom:1px solid black;font-size: 11px'>Almacen {$result['almacen_ingreso']}</td>
        <td style='border-bottom:1px solid black;font-size: 11px'>Almacen {$result['almacen_egreso']}</td>
        <td style='border-bottom:1px solid black;font-size: 11px'>{$resul2['nombres']}</td>
    </tr>";

        $html = "
        <div style='width: 100%'>
        <table style='width:100%;margin-bottom: 10px'>
          <tr>
            <td align='center'>
              <img style=' max-width: 85%;' src='" . URL::to('files/logos/' . $empresa['logo']) . "'>
        </td>
        </tr>
        </table>
            <div style='width: 100%;text-align: center'>
                <span style='font-size: 13px;font-weight: bold'>{$empresa["razon_social"]} </span>
            </div>
            <div style='width: 100%;text-align: center'>
                <span style='font-size: 12px'>RUC: {$empresa["ruc"]}</span>
            </div>
            <div style='width: 100%;text-align: center'>
                <span style='font-size: 12px'>{$empresa["direccion"]}</span>
            </div>
            <div style='width: 100%;text-align: center'>
                <span style='font-size: 12px'>{$empresa["telefono"]}</span>
            </div>
            <hr>
            <div style='width: 100%;text-align: center'>
                <table style='width:100%'>
                  <tr>
                    <td style='font-size: 11px;width: 25%'><strong>Codigo:</strong></td>
                    <td style='font-size: 11px;'>{$result['intercambio_id']}</td>
                  </tr>
                  <tr>
                    <td style='font-size: 11px;width: 25%'><strong>Producto:</strong></td>
                    <td style='font-size: 11px;'>{$result['descripcion']}</td>
                  </tr>
                  <tr>
                    <td style='font-size: 11px;width: 25%'><strong>Tipo:</strong></td>
                    <td style='font-size: 11px;'>{$result['tipoIntercambio']}</td>
                  </tr>
                </table>
            </div>
            <div style='width: 100%;text-align: center'>
                <span style='font-size: 13px;'>---------------------- Detalle -----------------------</span>
            </div>
            <div style='width: 100%;'>
                <table style='width: 100%; text-align: center;' >
                    <thead>
                    <tr>
                        <th style='border-bottom:1px solid black;font-size: 11px'>Cantidad</th>
                        <th style='border-bottom:1px solid black;font-size: 11px'>Ingreso</th>
                        <th style='border-bottom:1px solid black;font-size: 11px'>Egreso</th>
                        <th style='border-bottom:1px solid black;font-size: 11px'>Hecho por</th>
                    </tr>
                    </thead>
                   <tbody>
                   $rowHmtl
                    </tbody>
                </table>
            </div>
            <div style='width: 100%;'>
        <span style='font-size: 12px'><b>Observaciones:</b></span>
    </div>
    <br>
     <div style='width: 100%;text-align: center; margin-top:40px'>
        <span style='font-size: 12px'>Representación impresa de la Intercambio de Productos <br>Este documento puede ser validado en $dominio</span>
    </div>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 12px'>Gracias por su preferencia....</span>
    </div>
        </div>";

        $mpdf->AddPageByArray([
            "orientation" => "P",
            "newformat" => [80, 240 - 20]
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output();

    }
    public function generarExcelProducto()
    {

        $texto = $_GET['texto'];
        $sql = "select descripcion,MIN(codigo) AS codigo,
 MIN(costo) as costo,
       SUM(CASE WHEN almacen = 1 THEN cantidad ELSE 0 END) AS cantidad1, SUM(CASE WHEN almacen = 2 THEN cantidad ELSE 0 END) AS cantidad2 
        from productos where descripcion like '%$texto%' or codigo like '%$texto%' GROUP BY descripcion;";

        $result = $this->conexion->query($sql);

        foreach ($result as $fila) {

            // $tbody .= '
            $tbody = '
            <tr>
                <td>' . $fila['codigo'] . '</td>            
                <td>' . $fila['descripcion'] . '</td>            
                <td>' . $fila['costo'] . '</td>            
                <td>' . $fila['cantidad1'] . '</td>            
                <td>' . $fila['cantidad2'] . '</td>         
                         
            </tr>';
        }

        $tabla = "
        <table>
            <tr>
                    <th style='background-color: #90BFEB;width:10px'>Codigo</th>
                    <th style='background-color: #90BFEB;width:85px'>Descripcion</th>
                    <th style='background-color: #90BFEB;width:7px'>Costo</th>
                    <th style='background-color: #90BFEB;width:7px'>CNT A1</th>
                    <th style='background-color: #90BFEB;width:8px'>CNT A2</th> 
            </tr>
            <tbody>
                " . $tbody . "
            </tbody>
        </table>";




        /*   return ($arrayRes);  */
        $nombre_exel = "reporteproductosstock.xlsx";
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString($tabla);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");


        $writer->save($nombre_exel);
        header('Location: ' . URL::to($nombre_exel));
    }
    public function generarExcel($id)
    {

        $explodeFecha = explode('-', $id);
        $anio = $explodeFecha[0];
        $mes = $explodeFecha[1];
        $sql = 'SELECT *,CASE
        WHEN v1.cnt_pv > 0 THEN "VENTA DE MERCADERIA"
        ELSE "VENTA DE SERVICIO"
        END GLOSA
        FROM
        (SELECT v.id_venta,v.id_tido,ds.abreviatura,CONCAT(v.serie , "-" ,LPAD(v.numero,8,0)) AS documento, 
            v.fecha_emision,v.fecha_vencimiento,
            IF(ISNULL(c.documento), "", c.documento) AS codigocliente,IF(ISNULL(c.datos), "", c.datos) AS datos,
            IF(v.enviado_sunat = 1, "Si", "No") AS enviado,v.total,
            IF(ISNULL(gr.serie) ,"",CONCAT(gr.serie , "-" ,LPAD(v.numero,8,0))) AS guia,
            (
        SELECT COUNT(*) FROM productos_ventas pv WHERE pv.id_venta= v.id_venta
        ) cnt_pv,
        (SELECT COUNT(*) FROM ventas_servicios vs WHERE vs.id_venta= v.id_venta) cnt_sv
            FROM ventas AS v 
            JOIN documentos_sunat AS ds
            ON ds.id_tido=v.id_tido
            LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
            LEFT JOIN guia_remision AS gr ON gr.id_venta=v.id_venta
            WHERE  YEAR(v.fecha_emision) =' . $anio . ' AND MONTH(v.fecha_emision) = ' . $mes . ' AND v.id_empresa=' . $_SESSION['id_empresa'] . ')v1';

        $result = $this->conexion->query($sql);
        $tabla = '';
        $tbody = '';
        foreach ($result as $fila) {
            if ($fila['id_tido'] != '1' && $fila['id_tido'] != '2') {
                continue;
            }
            $tbody .= '
            <tr>
                <td>' . $fila['id_venta'] . '</td>            
                <td>' . $fila['abreviatura'] . '</td>            
                <td>' . $fila['documento'] . '</td>            
                <td>' . $fila['fecha_emision'] . '</td>            
                <td>' . $fila['fecha_vencimiento'] . '</td>            
                <td style="text-align:center">' . $fila['codigocliente'] . '</td>            
                <td>' . $fila['datos'] . '</td>            
                <td>' . $fila['enviado'] . '</td>            
                <td>S</td>            
                <td>' . $fila['total'] . '</td>            
                <td>' . $fila['total'] . '</td>            
                <td>' . $fila['GLOSA'] . '</td>            
                      
                <td>' . $fila['total'] . '</td>             
                <td>E</td>             
                <td>' . $fila['guia'] . '</td>             
                <td>Oficina</td>             
                <td>' . $fila['fecha_emision'] . '</td>             
                <td>' . $fila['fecha_emision'] . '</td>             
                <td>admin</td>             
                <td></td>             
                <td></td>             
                <td></td>             
            </tr>';
        }

        $tabla .= "
        <table>
            <tr>
                    <th style='background-color: #90BFEB;width:10px'>Nº Registro</th>
                    <th style='background-color: #90BFEB;width:10px'>Tipo Doc.</th>
                    <th style='background-color: #90BFEB;width:15px'>Documento</th>
                    <th style='background-color: #90BFEB;width:15px'>Fecha Registro</th>
                    <th style='background-color: #90BFEB;width:15px'>F. Vencimiento</th>
                    <th style='background-color: #90BFEB;width:16px;text-align:center'>Codigo Cliente</th>
                    <th style='background-color: #90BFEB;width:85px'>Nombre Cliente</th>
                    <th style='background-color: #90BFEB;width:7px'>Sunat</th>
                    <th style='background-color: #90BFEB;width:7px'>Moneda</th>
                    <th style='background-color: #90BFEB;width:8px'>Total</th>
                    <th style='background-color: #90BFEB;width:8px'>Saldo</th>
                    <th style='background-color: #90BFEB;width:22px'>Glosa</th>
                    <th style='background-color: #90BFEB;width:8px'>Total Convertido</th>
                    <th style='background-color: #90BFEB;width:5px'>E</th>
                    <th style='background-color: #90BFEB;width:14px'>Con Guía</th>
                    <th style='background-color: #90BFEB;width:10px'>Vendedor</th>
                    <th style='background-color: #90BFEB;width:12px'>Orden Compra</th>
                    <th style='background-color: #90BFEB;width:12px'>Fecha Crea.</th>
                    <th style='background-color: #90BFEB;width:10px'>Usuario Crea.</th>
                    <th style='background-color: #90BFEB;width:10px'>Fecha Act.</th>
                    <th style='background-color: #90BFEB;width:10px'>Usuario Act.</th>
                    <th style='background-color: #90BFEB;width:10px'>Historial</th>
            </tr>
            <tbody>
                " . $tbody . "
            </tbody>
        </table>";




        /*   return ($arrayRes);  */
        $nombre_exel = "Venta de " . $anio . "-" . $mes . ".xlsx";
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString($tabla);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");


        $writer->save($nombre_exel);
        header('Location: ' . URL::to($nombre_exel));
    }

    public function generarExcelRVTA($fecha)
    {


        $explodeFecha = explode('-', $fecha);
        $anio = $explodeFecha[0];
        $mes = $explodeFecha[1];
        $sql = 'SELECT v.id_tido,v.id_venta,v.fecha_emision,v.fecha_vencimiento,ds.nombre AS tipoDocPago,v.serie,v.numero AS numeroVenta,v.enviado_sunat,v.igv,
       IF(v.enviado_sunat = 0,"No enviado","Enviado") AS enviadoSunat,
        (CASE 
        WHEN LENGTH(c.documento) = 8 THEN "DNI" 
         WHEN LENGTH(c.documento) = 11 THEN "RUC"
        END ) AS tipoDocumento,
        c.documento,c.datos AS cliente,v.total
         FROM ventas v 
        LEFT JOIN documentos_sunat ds ON v.id_tido=ds.id_tido
        LEFT JOIN clientes c ON c.id_cliente=v.id_cliente 
        WHERE  YEAR(v.fecha_emision) =' . $anio . ' AND MONTH(v.fecha_emision) = ' . $mes . ' AND v.id_empresa=' . $_SESSION['id_empresa'];

        /*   var_dump($sql);
        die();
 */
        $result = $this->conexion->query($sql);
        $tabla = '';
        $tbody = '';
        $totalOpgravado = 0;
        /*   $total = 0; */
        foreach ($result as $fila) {
            if ($fila['id_tido'] != '1' && $fila['id_tido'] != '2') {
                continue;
            }
            $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');
            $igv = $fila['total'] / ($fila['igv'] + 1) * $fila['igv'];
            $totalOpgravado = $fila['total'] - $igv;
            $total = number_format((float) $fila['total'], 2, '.', '');
            $igv = number_format($igv, 2, '.', ',');
            $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');
            $style = '';
            if ($fila['enviado_sunat'] == '0') {
                $style = "red";
            } else {
                $style = "green";
            }
            $tbody .= '
               <tr>
               <td style="font-size: 10px;border:1px solid black;">' . $fila['id_venta'] . '</td>
               <td style="font-size: 10px;border:1px solid black;">' . $fila['fecha_emision'] . '</td>
               <td style="font-size: 10px;border:1px solid black;">' . $fila['fecha_vencimiento'] . '</td>
               <td style="font-size: 10px;border:1px solid black;">' . $fila['tipoDocPago'] . '</td>
               <td style="font-size: 10px;border:1px solid black;">' . $fila['serie'] . '</td>
               <td style="font-size: 10px;border:1px solid black;">' . $fila['numeroVenta'] . '</td>
               <td style="font-size: 10px;border:1px solid black;">' . $fila['tipoDocumento'] . '</td>
               <td style="font-size: 10px;border:1px solid black;">' . $fila['documento'] . '</td>
               <td style="font-size: 10px;border:1px solid black;" colspan="2">' . $fila['cliente'] . '</td>
               <td style="font-size: 10px;border:1px solid black;">0</td>
               <td style="font-size: 10px;border:1px solid black;">' . $totalOpgravado . '</td>
               <td style="font-size: 10px;border:1px solid black;">0</td>
               <td style="font-size: 10px;border:1px solid black;">0</td>
               <td style="font-size: 10px;border:1px solid black;"colspan="2"></td>
               <td style="font-size: 10px;border:1px solid black;" colspan="2">' . ($fila['igv'] * 100) . ' %</td>
               <td style="font-size: 10px;border:1px solid black;" colspan="2">0</td>
               <td style="font-size: 10px;border:1px solid black;" colspan="2">' . $total . '</td>
               <td style="font-size: 10px;border:1px solid black;" colspan="2"></td>
               <td style="font-size: 10px;border:1px solid black;"></td>
               <td style="font-size: 10px;border:1px solid black;"></td>
               <td style="font-size: 10px;border:1px solid black;"></td>
               <td style="font-size: 10px;border:1px solid black;"></td>
               <td style="font-size: 10px;border:1px solid black;width:10px;background-color:' . $style . '">' . $fila['enviadoSunat'] . '</td>
               </tr>
                ';
        }
        $tabla = '  <table style="width:100%">
        <tr>
        </tr>
        <tr>
            <th rowspan="3" colspan="1" style="font-weight:bold;border:1px solid black;text-align: center;font-size: 10px;width:20px;word-wrap: break-word">NUMERO DEL REGISTRO O CODIGO UNICO DE OPERACIÓN</th>
            <th rowspan="2" colspan="5" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;">COMPROBANTE DE PAGO O DOCUMENTO</th>
            <th colspan="4" rowspan="1" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;">INFORMACION DEL CLIENTE</th>
            <th rowspan="3" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;width:20px;text-align: center;word-wrap: break-word">VALOR FACTURADO DE LA EXPORTACION</th>
            <th rowspan="3" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;width:20px;text-align: center;word-wrap: break-word">BASE IMPONIBLE DE LA OPERACIÓN GRAVADA</th>
            <th rowspan="2" colspan="2" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;width:20px;text-align: center;word-wrap: break-word">IMPORTE DE LA OPERACIÓN EXONERADA O INAFECTA</th>
            <th rowspan="3" colspan="2" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;">ISC</th>
            <th rowspan="3" colspan="2" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;">IGV Y/O IPM</th>
            <th rowspan="3" colspan="2" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;width:20px;text-align: center;word-wrap: break-word">OTROS TRIBUTOS Y CARGOS QUE NO FORMAN PARTE DE LA BASE IMPONIBLE</th>
            <th rowspan="3" colspan="2" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;width:20px;text-align: center;word-wrap: break-word">IMPORTE TOTAL DEL COMPROBANTE DE PAGO</th>
            <th rowspan="3" colspan="2" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;">TIPO DE CAMBIO</th>
            <th rowspan="2" colspan="4" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;width:20px;text-align: center;word-wrap: break-word">REF. COMPROBANTE DE PAGO QUE SE MODIFICA</th>
        </tr>
        <tr>
            <td colspan="2" style="font-size: 10px;font-weight:bold;border:1px solid black;width:20px;text-align: center;word-wrap: break-word">DOC. IDENTIDAD</td>
            <td rowspan="2" colspan="2" style="font-size: 10px;font-weight:bold;border:1px solid black;width:30px;text-align: center;word-wrap: break-word">APELLIDOS Y NOMBRES O RAZON SOCIAL</td>
          
        </tr>
        <tr>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:16px;text-align: center;word-wrap: break-word">FECHA DE EMISION</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:13px;text-align: center;word-wrap: break-word">FECHA DE VCTO</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:16px;text-align: center;word-wrap: break-word">TIPO</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:10px;text-align: center;word-wrap: break-word">SERIE</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:10px;text-align: center;word-wrap: break-word">NUMERO</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;text-align: center;width:8px;">TIPO</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;text-align: center;width:13px;">NUMERO</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;text-align: center;">EXONERADA</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;text-align: center;">INAFECTA</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:15px;text-align: center;">FECHA EMISION</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:12px;text-align: center;">TIPO</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:12px;text-align: center;">SERIE</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:12px;text-align: center;">NUMERO</td>
        </tr>
       ' . $tbody . '
    </table>';

        $nombre_exel = "RVTA " . $anio . "-" . $mes . ".xlsx";
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString($tabla);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($nombre_exel);
        header('Location: ' . URL::to($nombre_exel));
    }

    public function generarExcelProductoImporte()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // Primera hoja: Detalles de productos
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Productos');
        $headers1 = [
            'Nombre',
            'Código',
            'Cantidad',
            'Cantidad por unidad',
            'Unidad de medida',
            'Tipo de Producto',
            'Categoría',
            'Fecha de Vencimiento',
            'RUC',
            'Razón Social',
            'Precio',
            'Precio de Venta',
            'Fecha de Registro',
            'Guía de Remisión'
        ];

        foreach ($headers1 as $index => $header) {
            $sheet1->setCellValueByColumnAndRow($index + 1, 1, $header);
        }

        $sheet1->getColumnDimension('A')->setWidth(30); // Columna Nombre (más ancha)
        $sheet1->getColumnDimension('B')->setWidth(30); // Columna Código (más ancha)
        $sheet1->getColumnDimension('I')->setWidth(35); // Columna RUC (más ancha)

        // Aplicar setAutoSize a las columnas de la C a la H
        foreach (range('C', 'H') as $columnID) {
            $sheet1->getColumnDimension($columnID)->setAutoSize(true); // Ajusta automáticamente el ancho
        }

        // Aplicar setAutoSize a las columnas de la J a la N
        foreach (range('J', 'N') as $columnID) {
            $sheet1->getColumnDimension($columnID)->setAutoSize(true); // Ajusta automáticamente el ancho
        }

        // Obtener categorías desde la base de datos
        $categoriaModel = new CategoriaProductoModel();
        $categoriasDB = $categoriaModel->obtenerCategoriasProducto();

        // Convertir las opciones predefinidas en un array para evitar duplicados // 🔹 Modificado
        $categorias = ['Llantas', 'Celular', 'Chip (Linea Corporativa)', 'Aceites', 'Seguros', 'SOAT']; // 🔹 Modificado

        // Agregar categorías desde la base de datos si no existen en el array
        foreach ($categoriasDB as $categoria) {
            if (!in_array($categoria['nombre'], $categorias)) { // 🔹 Se usa in_array para evitar duplicados correctamente
                $categorias[] = $categoria['nombre']; // 🔹 Se agregan solo las categorías nuevas
            }
        }

        // Convertir el array en una cadena separada por comas
        $opcionesCategoria = '"' . implode(',', $categorias) . '"'; // 🔹 Se genera correctamente la cadena sin duplicados

        // Validación de Categoría con lista desplegable
        $validationCategoria = $sheet1->getCell('G2')->getDataValidation();
        $validationCategoria->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validationCategoria->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
        $validationCategoria->setAllowBlank(false);
        $validationCategoria->setShowDropDown(true);
        $validationCategoria->setFormula1($opcionesCategoria); // 🔹 Se usa la cadena generada correctamente
        $sheet1->setDataValidation('G2:G101', $validationCategoria);

        // Validación de Unidad de Medida con lista desplegable
        $validationUnidadMedida = $sheet1->getCell('E2')->getDataValidation();
        $validationUnidadMedida->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validationUnidadMedida->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
        $validationUnidadMedida->setAllowBlank(false);
        $validationUnidadMedida->setShowDropDown(true);
        $validationUnidadMedida->setFormula1('"Litros,Galones (3.785 litros),Kilogramos,OZ"');
        $sheet1->setDataValidation('E2:E101', $validationUnidadMedida);

        // Validación de Tipo de Producto con opciones predefinidas más las obtenidas de la base de datos
        $tipos = new TipoProductoModel();
        $tiposProducto = $tipos->obtenerTiposProducto();
        $opcionesTipoProducto = '"Físico,Intangible';

        foreach ($tiposProducto as $tipo) {
            $opcionesTipoProducto .= ',' . $tipo['tipo_productocol'];
        }
        $opcionesTipoProducto .= '"';

        $validationTipoProducto = $sheet1->getCell('F2')->getDataValidation();
        $validationTipoProducto->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validationTipoProducto->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
        $validationTipoProducto->setAllowBlank(false);
        $validationTipoProducto->setShowDropDown(true);
        $validationTipoProducto->setFormula1($opcionesTipoProducto);
        $sheet1->setDataValidation('F2:F101', $validationTipoProducto);

        // Validación para que Precio y Precio de Venta acepten solo números con 2 decimales
        foreach (['K', 'L'] as $column) { // Precio (K) y Precio de Venta (L)
            $validationPrecio = $sheet1->getCell($column . '2')->getDataValidation();
            $validationPrecio->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_DECIMAL);
            $validationPrecio->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validationPrecio->setAllowBlank(false);
            $validationPrecio->setShowInputMessage(true);
            $validationPrecio->setPromptTitle('Formato incorrecto');
            $validationPrecio->setPrompt('Ingrese un número con hasta 2 decimales.');
            $validationPrecio->setFormula1(0);
            $validationPrecio->setFormula2(9999999.99);
            $sheet1->setDataValidation($column . '2:' . $column . '101', $validationPrecio);
        }

        // Validación para que Fecha de Vencimiento y Fecha de Registro solo acepten fechas
        foreach (['H', 'M'] as $column) {
            $validationFecha = $sheet1->getCell($column . '2')->getDataValidation();
            $validationFecha->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_DATE);
            $validationFecha->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validationFecha->setAllowBlank(false);
            $validationFecha->setShowInputMessage(true);
            $validationFecha->setPromptTitle('Formato de fecha');
            $validationFecha->setPrompt('Ingrese una fecha válida en el formato correcto.');
            $sheet1->setDataValidation($column . '2:' . $column . '101', $validationFecha);
        }

        // Segunda hoja: Características de productos
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Características');
        $headers2 = ['Código', 'Categoría', 'Característica', 'Valor'];

        foreach ($headers2 as $index => $header) {
            $sheet2->setCellValueByColumnAndRow($index + 1, 1, $header);
        }

        $sheet1->getColumnDimension('A')->setWidth(20); // Columna Nombre
        $sheet1->getColumnDimension('B')->setWidth(20); // Columna Código
        $sheet1->getColumnDimension('I')->setWidth(20); // Columna RUC

        foreach (range('A', $sheet2->getHighestColumn()) as $columnID) {
            $sheet2->getColumnDimension($columnID)->setAutoSize(true);
        }


        // Crear hoja para listas (categorías y características)
        $sheetCategorias = $spreadsheet->createSheet();
        $sheetCategorias->setTitle('ListaCategorias');
        $categorias = [
            'Llantas' => ['Aro', 'Perfil'],
            'Celular' => ['Chip Linea', 'Marca', 'Modelo', 'Nro IMEI', 'Nro Serie', 'Color', 'Cargador', 'Cable USB', 'Manual de Usuario', 'Estuche'],
            'Chip (Linea Corporativa)' => ['Plan Mensual', 'Operadora'],
            'Aceites' => [],
            'Seguros' => [],
            'SOAT' => []
        ];

        // Obtener categorías desde la base de datos
        $categoriaModel = new CategoriaProductoModel();
        $categoriasDB = $categoriaModel->obtenerCategoriasProducto(); // 🔹 Obtenemos las categorías desde la BD

        // Agregar categorías de la base de datos si no existen
        foreach ($categoriasDB as $categoria) {
            if (!array_key_exists($categoria['nombre'], $categorias)) { // 🔹 Verifica si ya existe para evitar duplicados
                $categorias[$categoria['nombre']] = []; // 🔹 Agrega la categoría sin características por defecto
            }
        }

        // Poner las categorías en la columna A y las características en las filas correspondientes
        $row = 1;
        foreach ($categorias as $categoria => $caracteristicas) {
            $sheetCategorias->setCellValue('A' . $row, $categoria);

            // Colocar las características en las filas de la columna B en adelante
            $col = 2;
            foreach ($caracteristicas as $caracteristica) {
                $sheetCategorias->setCellValueByColumnAndRow($col, $row, $caracteristica);
                $col++;
            }
            $row++;
        }
        $sheetCategorias->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        // Validación de Categoría
        $validationCategoria = $sheet2->getCell('B2')->getDataValidation();
        $validationCategoria->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validationCategoria->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
        $validationCategoria->setAllowBlank(false);
        $validationCategoria->setShowDropDown(true);
        $validationCategoria->setFormula1('ListaCategorias!$A$1:$A$' . count($categorias));

        $sheet2->setDataValidation('B2:B101', $validationCategoria);

        // Validación de Característica (dependiente de Categoría)
        for ($i = 2; $i <= 101; $i++) {
            $validationCaracteristica = $sheet2->getCell('C' . $i)->getDataValidation();
            $validationCaracteristica->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validationCaracteristica->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validationCaracteristica->setAllowBlank(false);
            $validationCaracteristica->setShowDropDown(true);
            $validationCaracteristica->setFormula1(
                'OFFSET(ListaCategorias!$B$1, MATCH(B' . $i . ',ListaCategorias!$A$1:$A$' . count($categorias) . ',0)-1, 0, 1, COUNTA(OFFSET(ListaCategorias!$B$1, MATCH(B' . $i . ',ListaCategorias!$A$1:$A$' . count($categorias) . ',0)-1, 0, 1, 100)))'
            );

            $sheet2->setDataValidation('C' . $i, $validationCaracteristica);
        }

        // Guardar el archivo Excel generado
        $nombre_excel = "Plantilla_productos_actualizada.xlsx";
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($nombre_excel);

        // Configurar encabezados para la descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nombre_excel . '"');
        header('Cache-Control: max-age=0');
        readfile($nombre_excel);

        // Eliminar el archivo temporal
        unlink($nombre_excel);
    }

    public function getReportePagoFinan()
    {

        // Crear una nueva hoja de cálculo
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte de Pagos');

        // Establecer los encabezados de columna
        $headers = [
            'A1' => 'Ítem',
            'B1' => 'Conductor',
            'C1' => 'DNI',
            'D1' => 'Nº Unidad',
            'E1' => 'Asesor',
            'F1' => 'Monto Inicial',
            'G1' => 'Monto Recalculado',
            'H1' => 'Grupo',
            'I1' => 'Producto',
            'J1' => 'Cuotas Pagadas',
            'K1' => 'Cuotas No Pagadas'
        ];

        // Aplicar encabezados a la hoja
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Estilo para los encabezados
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        // Aplicar estilo a los encabezados
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

        // Obtener datos para el reporte
        $query = "SELECT DISTINCT pf.idpagos_financiamiento, pf.id_financiamiento, pf.id_conductor, 
                  pf.id_asesor, pf.monto, pf.metodo_pago, pf.fecha_pago, pf.moneda
                  FROM pagos_financiamiento pf
                  ORDER BY pf.fecha_pago DESC";

        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        // Array para controlar financiamientos ya procesados
        $financiamientosYaProcesados = [];

        // Iniciar fila para datos
        $row = 2;
        $item = 1;

        $usuarioModel = new Usuario();

        // Procesar resultados
        while ($pago = $result->fetch_assoc()) {
            $id_financiamiento = $pago['id_financiamiento'];

            // Si id_financiamiento es null, obtenerlo desde las tablas relacionadas
            if ($id_financiamiento === null) {
                $id_financiamiento = $this->buscarIdFinanciamiento($pago['idpagos_financiamiento']);
            }

            // Si no se puede obtener id_financiamiento o ya fue procesado, continuar con el siguiente registro
            if ($id_financiamiento === null || in_array($id_financiamiento, $financiamientosYaProcesados)) {
                continue;
            }

            // Marcar este id_financiamiento como procesado
            $financiamientosYaProcesados[] = $id_financiamiento;

            // 3. Obtener datos del financiamiento
            $financiamientoData = $this->obtenerDatosFinanciamiento($id_financiamiento);

            // 1. Obtener datos del conductor
            if ($pago['id_conductor'] !== null) {
                // Llamar a la función para obtener datos del conductor
                $conductorData = $this->obtenerDatosConductor($pago['id_conductor']);
            } else {
                // Verificar si id_cliente existe y no es null
                if (isset($financiamientoData['id_cliente']) && $financiamientoData['id_cliente'] !== null) {
                    // Si no hay ID de conductor, obtener datos del cliente
                    $queryCliente = "SELECT * FROM clientes_financiar WHERE id = ?";
                    $stmtCliente = $this->conexion->prepare($queryCliente);
                    $stmtCliente->bind_param("i", $financiamientoData['id_cliente']);
                    $stmtCliente->execute();
                    $resultCliente = $stmtCliente->get_result();

                    if ($resultCliente->num_rows > 0) {
                        $clienteData = $resultCliente->fetch_assoc();
                        $nombreCompleto = $clienteData['nombres'] . ' ' . $clienteData['apellido_paterno'] . ' ' . $clienteData['apellido_materno'];

                        $conductorData = [
                            'nombre_completo' => $nombreCompleto,
                            'nro_documento' => $clienteData['n_documento'],
                            'numUnidad' => ''
                        ];
                    } else {
                        // Si no se encontró el cliente, dejar los campos vacíos
                        $conductorData = [
                            'nombre_completo' => '',
                            'nro_documento' => '',
                            'numUnidad' => ''
                        ];
                    }
                } else {
                    // Si id_cliente no está o es null
                    $conductorData = [
                        'nombre_completo' => '',
                        'nro_documento' => '',
                        'numUnidad' => ''
                    ];
                }
            }


            // 2. Obtener datos del asesor
            $asesorData = $usuarioModel->getData($pago['id_asesor']);
            $nombreAsesor = $asesorData ? $asesorData['nombres'] . ' ' . $asesorData['apellidos'] : 'No registrado';


            // 4. Obtener información del grupo
            $grupoInfo = $this->obtenerGrupoInfo($financiamientoData['grupo_financiamiento']);

            // 5. Obtener nombre del producto
            $nombreProducto = $this->obtenerNombreProducto($financiamientoData['idproductosv2']);



            $moneda = isset($financiamientoData['moneda']) && $financiamientoData['moneda'] !== null
                ? $financiamientoData['moneda']
                : 'S/.';


            // 6. Obtener cuotas pagadas y pendientes
            $cuotasPagadas = $this->obtenerCuotasPagadas($id_financiamiento, $moneda);
            $cuotasPendientes = $this->obtenerCuotasPendientes($id_financiamiento, $moneda);

            // Llenar la fila con datos
            $sheet->setCellValue('A' . $row, $item);
            $sheet->setCellValue('B' . $row, $conductorData['nombre_completo']);
            $sheet->setCellValue('C' . $row, $conductorData['nro_documento']);
            $sheet->setCellValue('D' . $row, $conductorData['numUnidad']);
            $sheet->setCellValue('E' . $row, $nombreAsesor);

            // MODIFICADO: Obtener la fecha de pago de la cuota inicial
            $queryFechaPagoInicial = "SELECT fecha_pago 
                                    FROM pagos_financiamiento 
                                    WHERE id_financiamiento = ? AND concepto = 'Cuota Inicial' 
                                    LIMIT 1";
            $stmtFechaPagoInicial = $this->conexion->prepare($queryFechaPagoInicial);
            $stmtFechaPagoInicial->bind_param("i", $id_financiamiento);
            $stmtFechaPagoInicial->execute();
            $resultFechaPagoInicial = $stmtFechaPagoInicial->get_result();
            $fechaPagoInicial = '';
            if ($pagoInicial = $resultFechaPagoInicial->fetch_assoc()) {
                $fechaPagoInicial = date('d/m/Y', strtotime($pagoInicial['fecha_pago'])); // MODIFICADO: Formatear la fecha
            }

            // MODIFICADO: Concatenar el monto inicial con la fecha de pago
            if ($financiamientoData['cuota_inicial'] > 0) { // MODIFICADO: Validación para agregar la fecha de pago solo cuando el monto es mayor que 0
                $sheet->setCellValue('F' . $row, $financiamientoData['cuota_inicial'] . ' | Fecha de Pago: ' . $fechaPagoInicial);
            } else {
                $sheet->setCellValue('F' . $row, $financiamientoData['cuota_inicial']); // MODIFICADO: Solo mostrar el monto cuando es 0
            }

            $sheet->setCellValue('G' . $row, $financiamientoData['monto_recalculado']);
            $sheet->setCellValue('H' . $row, $grupoInfo);
            $sheet->setCellValue('I' . $row, $nombreProducto);
            $sheet->setCellValue('J' . $row, $cuotasPagadas);
            $sheet->setCellValue('K' . $row, $cuotasPendientes);

            // Aplicar alineación para cuotas (para manejar múltiples líneas)
            $sheet->getStyle('J' . $row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('K' . $row)->getAlignment()->setWrapText(true);

            $row++;
            $item++;
        }

        // Ajustar ancho de columnas automáticamente
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Configurar la respuesta HTTP
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte_Pagos_Financiamiento.xlsx"');
        header('Cache-Control: max-age=0');
        header('Expires: 0');
        header('Pragma: public');

        // Limpiar cualquier salida anterior
        ob_clean();
        flush();

        // Crear el archivo Excel en PHP memory y enviarlo al navegador
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Método para buscar el ID de financiamiento a partir del ID de pago
     */
    private function buscarIdFinanciamiento($idPagoFinanciamiento)
    {
        // Paso 1: Buscar en detalle_pago_financiamiento
        $query = "SELECT dpf.id_cuota 
                  FROM detalle_pago_financiamiento dpf 
                  WHERE dpf.idfinanciamiento = ?";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $idPagoFinanciamiento);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $idCuota = $row['id_cuota'];

            // Paso 2: Con el ID de cuota, buscar en cuotas_financiamiento
            $query2 = "SELECT cf.id_financiamiento 
                      FROM cuotas_financiamiento cf 
                      WHERE cf.idcuotas_financiamiento = ?";

            $stmt2 = $this->conexion->prepare($query2);
            $stmt2->bind_param("i", $idCuota);
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            if ($row2 = $result2->fetch_assoc()) {
                return $row2['id_financiamiento'];
            }
        }

        return null;
    }

    /**
     * Método para obtener los datos del conductor
     */
    private function obtenerDatosConductor($idConductor)
    {
        $query = "SELECT CONCAT(nombres, ' ', apellido_paterno, ' ', apellido_materno) AS nombre_completo, 
                 nro_documento, numUnidad 
                 FROM conductores 
                 WHERE id_conductor = ?";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $idConductor);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($conductor = $result->fetch_assoc()) {
            return $conductor;
        }

        return [
            'nombre_completo' => 'No registrado',
            'nro_documento' => 'No registrado',
            'numUnidad' => 'N/A'
        ];
    }

    /**
     * Método para obtener los datos del financiamiento
     */
    private function obtenerDatosFinanciamiento($idFinanciamiento)
    {
        $query = "SELECT cuota_inicial, monto_recalculado, grupo_financiamiento, idproductosv2, moneda, id_cliente 
                 FROM financiamiento 
                 WHERE idfinanciamiento = ?";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $idFinanciamiento);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($financiamiento = $result->fetch_assoc()) {
            return $financiamiento;
        }

        return [
            'cuota_inicial' => 0,
            'monto_recalculado' => 0,
            'grupo_financiamiento' => null,
            'idproductosv2' => null,
            'moneda' => 'S/',
            'id_cliente' => null
        ];
    }

    /**
     * Método para obtener información del grupo
     */
    private function obtenerGrupoInfo($grupoFinanciamiento)
    {
        // Comprobar si grupo_financiamiento es numérico
        if (is_numeric($grupoFinanciamiento)) {
            $query = "SELECT nombre_plan 
                     FROM planes_financiamiento 
                     WHERE idplan_financiamiento = ?";

            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $grupoFinanciamiento);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($plan = $result->fetch_assoc()) {
                return $plan['nombre_plan'];
            }
        }

        return $grupoFinanciamiento ?: 'Sin Grupo';
    }

    /**
     * Método para obtener el nombre del producto
     */
    private function obtenerNombreProducto($idProducto)
    {
        if (!$idProducto) {
            return 'No registrado';
        }

        $query = "SELECT nombre FROM productosv2 WHERE idproductosv2 = ?";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $idProducto);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($producto = $result->fetch_assoc()) {
            return $producto['nombre'];
        }

        return 'No registrado';
    }

    /**
     * Método para obtener las cuotas pagadas
     */
    private function obtenerCuotasPagadas($idFinanciamiento, $moneda)
    {
        $query = "SELECT cf.numero_cuota, cf.monto, cf.fecha_pago, cf.idcuotas_financiamiento 
                 FROM cuotas_financiamiento cf 
                 WHERE cf.id_financiamiento = ? AND cf.estado = 'pagado' 
                 ORDER BY cf.numero_cuota ASC";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $idFinanciamiento);
        $stmt->execute();
        $result = $stmt->get_result();

        $cuotasPagadas = '';

        while ($cuota = $result->fetch_assoc()) {
            // Obtener método de pago
            $metodoPago = $this->obtenerMetodoPago($cuota['idcuotas_financiamiento']);

            // Formatear fecha
            $fecha = date('d/m/Y', strtotime($cuota['fecha_pago']));

            // Agregar a la lista de cuotas pagadas
            $cuotasPagadas .= "Cuota {$cuota['numero_cuota']} {$moneda}{$cuota['monto']} {$metodoPago} {$fecha}\n";
        }

        return $cuotasPagadas ?: 'No hay cuotas pagadas';
    }

    /**
     * Método para obtener las cuotas pendientes
     */
    private function obtenerCuotasPendientes($idFinanciamiento, $moneda)
    {
        $query = "SELECT cf.numero_cuota, cf.monto, cf.fecha_vencimiento 
                 FROM cuotas_financiamiento cf 
                 WHERE cf.id_financiamiento = ? AND cf.estado = 'En Progreso' 
                 ORDER BY cf.numero_cuota ASC";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $idFinanciamiento);
        $stmt->execute();
        $result = $stmt->get_result();

        $cuotasPendientes = '';

        while ($cuota = $result->fetch_assoc()) {
            // Formatear fecha
            $fecha = date('d/m/Y', strtotime($cuota['fecha_vencimiento']));

            // Agregar a la lista de cuotas pendientes
            $cuotasPendientes .= "Cuota {$cuota['numero_cuota']} {$moneda}{$cuota['monto']} FV: {$fecha}\n";
        }

        return $cuotasPendientes ?: 'No hay cuotas pendientes';
    }

    /**
     * Método para obtener el método de pago
     */
    private function obtenerMetodoPago($idCuotaFinanciamiento)
    {
        $query = "SELECT pf.metodo_pago 
                 FROM detalle_pago_financiamiento dpf 
                 JOIN pagos_financiamiento pf ON dpf.idfinanciamiento = pf.idpagos_financiamiento 
                 WHERE dpf.id_cuota = ?";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $idCuotaFinanciamiento);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row['metodo_pago'];
        }

        return 'N/A';
    }

    /*CODIGO PARA LOS REPORTES NUEVOS*/
    public function ventasGenerales()
    {
        // Validar y obtener parámetros
        $fechaInicio = $_POST['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $_POST['fecha_fin'] ?? date('Y-m-d');

        // Validar formato de fechas
        if (!$this->validarFecha($fechaInicio) || !$this->validarFecha($fechaFin)) {
            $this->responderError('Formato de fecha inválido');
            return;
        }

        try {
            // Consulta principal - Agregando cod_v
            $query = "SELECT 
                        vv.fecha_emision, 
                        vv.datos AS cliente, 
                        vv.total,
                        vv.id_venta,
                        vv.cod_v
                    FROM 
                        view_ventas vv
                    WHERE 
                        vv.fecha_emision BETWEEN '$fechaInicio' AND '$fechaFin'
                    ORDER BY 
                        vv.fecha_emision DESC";

            // Ejecutar con MySQLi
            $resultado = $this->conexion->query($query);

            if (!$resultado) {
                throw new Exception("Error en la consulta: " . $this->conexion->error);
            }

            $ventas = [];
            while ($fila = $resultado->fetch_assoc()) {
                $ventas[] = $fila;
            }

            // Procesar cada venta para obtener datos adicionales
            $resultados = [];
            $total_general = 0;

            foreach ($ventas as $venta) {
                $id_venta = $venta['id_venta'];
                $cod_v = $venta['cod_v'];

                // Usar cod_v cuando id_venta es null o vacío
                if ($id_venta === null || $id_venta === '') {
                    $id_para_consultas = $cod_v;
                } else {
                    $id_para_consultas = $id_venta;
                }

                // Obtener vendedor
                $vendedor = $this->obtenerVendedor($id_para_consultas);

                // Obtener método de pago
                $metodoPago = $this->obtenerMetodoPagoparaReporte($id_para_consultas);

                // Obtener productos
                $productos = $this->obtenerProductosVenta($id_para_consultas);

                // Agregar a resultados
                $ventaData = [
                    'fecha' => $venta['fecha_emision'],
                    'cliente' => $venta['cliente'],
                    'productos' => $productos,
                    'total' => $venta['total'],
                    'vendedor' => $vendedor,
                    'metodo_pago' => $metodoPago
                ];

                $resultados[] = $ventaData;

                // Eliminar "S/" y convertir a número
                $total_venta = preg_replace('/^S\/\s?/', '', $venta['total']); // Eliminar "S/" y el espacio

                // Convertir a float y acumular
                $total_general += floatval($total_venta);
            }

            // Agregar total general
            $this->responderExito([
                'registros' => $resultados,
                'total_general' => number_format($total_general, 2)
            ]);
        } catch (Exception $e) {
            $this->responderError('Error al procesar las ventas: ' . $e->getMessage());
        }
    }

    public function ventasPorEmpleado()
    {
        // Validar y obtener parámetros
        $fechaInicio = $_POST['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $_POST['fecha_fin'] ?? date('Y-m-d');
        $empleados = isset($_POST['empleados']) ? $_POST['empleados'] : [];

        // Validar formato de fechas
        if (!$this->validarFecha($fechaInicio) || !$this->validarFecha($fechaFin)) {
            $this->responderError('Formato de fecha inválido');
            return;
        }

        try {
            // Consulta principal
            $condicionEmpleados = '';
            if (!empty($empleados)) {
                $empleadosStr = implode(',', $empleados);
                $condicionEmpleados = " AND v.id_vendedor IN ($empleadosStr)";
            }

            $query = "SELECT 
                    u.usuario_id,
                    CONCAT_WS(' ', u.nombres, u.apellidos) AS asesor,
                    v.id_venta,
                    v.fecha_emision,
                    v.total
                FROM 
                    ventas v
                JOIN 
                    usuarios u ON v.id_vendedor = u.usuario_id
                WHERE 
                    v.fecha_emision BETWEEN '$fechaInicio' AND '$fechaFin'
                    $condicionEmpleados
                ORDER BY 
                    u.usuario_id, v.fecha_emision DESC";

            $resultado = $this->conexion->query($query);

            if (!$resultado) {
                throw new Exception("Error en la consulta: " . $this->conexion->error);
            }

            $ventas = [];
            while ($fila = $resultado->fetch_assoc()) {
                $ventas[] = $fila;
            }

            // Estructura para organizar datos por asesor
            $ventasPorAsesor = [];
            $total_general = 0;

            foreach ($ventas as $venta) {
                $id_venta = $venta['id_venta'];
                $id_asesor = $venta['usuario_id'];

                // Inicializar array para este asesor si no existe
                if (!isset($ventasPorAsesor[$id_asesor])) {
                    $ventasPorAsesor[$id_asesor] = [
                        'asesor' => $venta['asesor'],
                        'productos' => [],
                        'total' => 0
                    ];
                }

                // Obtener productos de esta venta
                $detallesProductos = $this->obtenerDetallesProductosVenta($id_venta);

                // Agregar productos al asesor
                foreach ($detallesProductos as $detalle) {
                    $productoExistente = false;
                    foreach ($ventasPorAsesor[$id_asesor]['productos'] as &$prod) {
                        if ($prod['id_producto'] == $detalle['id_producto']) {
                            $prod['cantidad'] += $detalle['cantidad'];
                            $prod['total_producto'] += $detalle['total_producto'];
                            $productoExistente = true;
                            break;
                        }
                    }

                    // Si no existe, agregar nuevo producto
                    if (!$productoExistente) {
                        $ventasPorAsesor[$id_asesor]['productos'][] = $detalle;
                    }

                    // Actualizar total del asesor
                    $ventasPorAsesor[$id_asesor]['total'] += $detalle['total_producto'];
                    $total_general += $detalle['total_producto'];
                }
            }

            // Convertir a formato de resultado
            $resultados = array_values($ventasPorAsesor);

            $this->responderExito([
                'registros' => $resultados,
                'total_general' => number_format($total_general, 2)
            ]);
        } catch (Exception $e) {
            $this->responderError('Error al procesar las ventas por empleado: ' . $e->getMessage());
        }
    }

    public function financiamientos()
    {
        // Validar y obtener parámetros
        $fechaInicio = $_POST['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $_POST['fecha_fin'] ?? date('Y-m-d');
        $tipoCliente = $_POST['tipo_cliente'] ?? 'todos';

        // Validar formato de fechas
        if (!$this->validarFecha($fechaInicio) || !$this->validarFecha($fechaFin)) {
            $this->responderError('Formato de fecha inválido');
            return;
        }

        try {
            // Construir condición de tipo de cliente
            $condicionTipoCliente = '';
            if ($tipoCliente == 'clientes') {
                $condicionTipoCliente = " AND f.id_cliente IS NOT NULL AND f.id_conductor IS NULL";
            } elseif ($tipoCliente == 'conductores') {
                $condicionTipoCliente = " AND f.id_conductor IS NOT NULL AND f.id_cliente IS NULL";
            }

            // Consulta principal
            $query = "SELECT 
                    f.idfinanciamiento,
                    f.id_cliente,
                    f.id_conductor,
                    f.idproductosv2,
                    f.monto_total,
                    f.cuotas,
                    f.fecha_inicio,
                    f.fecha_fin,
                    f.id_variante,       
                    f.grupo_financiamiento,
                    f.codigo_asociado
                FROM 
                    financiamiento f
                WHERE 
                    DATE(f.fecha_creacion) BETWEEN '$fechaInicio' AND '$fechaFin'
                    $condicionTipoCliente
                ORDER BY 
                     f.fecha_creacion DESC";

            $resultado = $this->conexion->query($query);
            if (!$resultado) {
                throw new Exception("Error en la consulta: " . $this->conexion->error);
            }

            $financiamientos = [];
            while ($fila = $resultado->fetch_assoc()) {
                $financiamientos[] = $fila;
            }

            // Procesar cada financiamiento
            $resultados = [];

            foreach ($financiamientos as $financiamiento) {
                $id_financiamiento = $financiamiento['idfinanciamiento'];

                // Obtener información del cliente o conductor
                $clienteData = [];
                if (!empty($financiamiento['id_cliente'])) {
                    $clienteData = $this->obtenerDatosCliente($financiamiento['id_cliente']);
                    $clienteData['tipo'] = 'Cliente';
                    $clienteData['numUnidad'] = '';
                } elseif (!empty($financiamiento['id_conductor'])) {
                    $clienteData = $this->obtenerDatosConductorparaReporte($financiamiento['id_conductor']);
                    $clienteData['tipo'] = 'Conductor';
                }

                // Obtener producto
                $producto = $this->obtenerDatosProducto($financiamiento['idproductosv2']);

                // Obtener información de cuotas
                $cuotasInfo = $this->obtenerInfoCuotas($id_financiamiento);

                // Calcular saldo pendiente
                $saldoPendiente = $cuotasInfo['cuotas_pendientes'] * ($financiamiento['monto_total'] / $financiamiento['cuotas']);

                $nombreGrupo = $this->obtenerNombreGrupoFinanciamiento($financiamiento['id_variante'], $financiamiento['grupo_financiamiento']); // 🐱 Obtener nombre del grupo
                // Agregar a resultados
                $financiamientoData = [
                    'nro_documento' => $clienteData['documento'] ?? '',
                    'cliente' => $clienteData['nombre_completo'] ?? '',
                    'producto' => $producto['nombre'] ?? '',
                    'cuotas_totales' => $financiamiento['cuotas'],
                    'cuotas_pagadas' => $cuotasInfo['cuotas_pagadas'],
                    'cuotas_pendientes' => $cuotasInfo['cuotas_pendientes'],
                    'saldo_pendiente' => number_format($saldoPendiente, 2),
                    'fecha_inicio' => $financiamiento['fecha_inicio'],
                    'fecha_fin' => $financiamiento['fecha_fin'],
                    'tipo_cliente' => $clienteData['tipo'] ?? '',
                    'numUnidad' => $clienteData['numUnidad'] ?? '',
                    'grupo' => $nombreGrupo, // 🐱 Agregado campo grupo
                    'codigo_asociado' => $financiamiento['codigo_asociado'] ?? ''
                ];

                $resultados[] = $financiamientoData;
            }

            $this->responderExito([
                'registros' => $resultados
            ]);
        } catch (Exception $e) {
            $this->responderError('Error al procesar los financiamientos: ' . $e->getMessage());
        }
    }

    public function cuotasPagadas()
    {
        // Validar y obtener parámetros
        $fechaInicio = $_POST['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $_POST['fecha_fin'] ?? date('Y-m-d');
        $tipoCliente = $_POST['tipo_cliente'] ?? 'todos';
        $incluirMorosos = isset($_POST['incluir_morosos']) ? filter_var($_POST['incluir_morosos'], FILTER_VALIDATE_BOOLEAN) : true;

        // Validar formato de fechas
        if (!$this->validarFecha($fechaInicio) || !$this->validarFecha($fechaFin)) {
            $this->responderError('Formato de fecha inválido');
            return;
        }

        try {
            // Construir condición de tipo de cliente
            $condicionTipoCliente = '';
            if ($tipoCliente == 'clientes') {
                $condicionTipoCliente = " AND f.id_cliente IS NOT NULL AND f.id_conductor IS NULL";
            } elseif ($tipoCliente == 'conductores') {
                $condicionTipoCliente = " AND f.id_conductor IS NOT NULL AND f.id_cliente IS NULL";
            }

            // Obtener financiamientos
            $query = "SELECT 
                    f.idfinanciamiento,
                    f.id_cliente,
                    f.id_conductor,
                    f.idproductosv2,
                    f.monto_total,
                    f.cuotas,
                    f.fecha_inicio,
                    f.fecha_fin
                FROM 
                    financiamiento f
                WHERE 
                    f.fecha_inicio BETWEEN '$fechaInicio' AND '$fechaFin'
                    $condicionTipoCliente
                ORDER BY 
                    f.fecha_inicio DESC";

            $resultado = $this->conexion->query($query);
            if (!$resultado) {
                throw new Exception("Error en la consulta: " . $this->conexion->error);
            }

            $financiamientos = [];
            while ($row = $resultado->fetch_assoc()) {
                $financiamientos[] = $row;
            }

            // Procesar para obtener cuotas pagadas
            $cuotasPorCliente = [];
            $clientesMorosos = [];

            foreach ($financiamientos as $financiamiento) {
                $id_financiamiento = $financiamiento['idfinanciamiento'];

                // Obtener información del cliente o conductor
                $clienteData = [];
                if (!empty($financiamiento['id_cliente'])) {
                    $clienteData = $this->obtenerDatosCliente($financiamiento['id_cliente']);
                    $clienteData['tipo'] = 'Cliente';
                    $clienteData['numUnidad'] = '';
                    $clienteData['id'] = $financiamiento['id_cliente'];
                } elseif (!empty($financiamiento['id_conductor'])) {
                    $clienteData = $this->obtenerDatosConductorparaReporte($financiamiento['id_conductor']);
                    $clienteData['tipo'] = 'Conductor';
                    $clienteData['id'] = $financiamiento['id_conductor'];
                }

                // Obtener producto
                $producto = $this->obtenerDatosProducto($financiamiento['idproductosv2']);

                // Obtener información de cuotas
                $cuotasInfo = $this->obtenerInfoCuotas($id_financiamiento);

                // Calcular saldo pendiente
                $saldoPendiente = $cuotasInfo['cuotas_pendientes'] * ($financiamiento['monto_total'] / $financiamiento['cuotas']);

                // Verificar si tiene cuotas vencidas
                $cuotasVencidas = $this->obtenerCuotasVencidas($id_financiamiento);

                // Agregar a arreglo de cuotas por cliente
                $clienteKey = $clienteData['tipo'] . '-' . $clienteData['id'];

                if (!isset($cuotasPorCliente[$clienteKey])) {
                    $cuotasPorCliente[$clienteKey] = [
                        'nro_documento' => $clienteData['documento'] ?? '',
                        'cliente' => $clienteData['nombre_completo'] ?? '',
                        'tipo_cliente' => $clienteData['tipo'] ?? '',
                        'numUnidad' => $clienteData['numUnidad'] ?? '',
                        'financiamientos' => []
                    ];
                }

                // Agregar financiamiento a este cliente
                $cuotasPorCliente[$clienteKey]['financiamientos'][] = [
                    'idfinanciamiento' => $id_financiamiento,
                    'producto' => $producto['nombre'] ?? '',
                    'cuotas_totales' => $financiamiento['cuotas'],
                    'cuotas_pagadas' => $cuotasInfo['cuotas_pagadas'],
                    'cuotas_pendientes' => $cuotasInfo['cuotas_pendientes'],
                    'saldo_pendiente' => number_format($saldoPendiente, 2),
                    'fecha_inicio' => $financiamiento['fecha_inicio'],
                    'fecha_fin' => $financiamiento['fecha_fin']
                ];

                // Si tiene cuotas vencidas, agregar a morosos
                if ($cuotasVencidas > 0) {
                    if (!isset($clientesMorosos[$clienteKey])) {
                        $clientesMorosos[$clienteKey] = [
                            'nro_documento' => $clienteData['documento'] ?? '',
                            'cliente' => $clienteData['nombre_completo'] ?? '',
                            'tipo_cliente' => $clienteData['tipo'] ?? '',
                            'numUnidad' => $clienteData['numUnidad'] ?? '',
                            'cuotas_vencidas' => $cuotasVencidas,
                            'monto_vencido' => $cuotasVencidas * ($financiamiento['monto_total'] / $financiamiento['cuotas']),
                            'producto' => $producto['nombre'] ?? '',
                            'idfinanciamiento' => $id_financiamiento
                        ];
                    } else {
                        $clientesMorosos[$clienteKey]['cuotas_vencidas'] += $cuotasVencidas;
                        $clientesMorosos[$clienteKey]['monto_vencido'] += $cuotasVencidas * ($financiamiento['monto_total'] / $financiamiento['cuotas']);
                    }
                }
            }

            $this->responderExito([
                'cuotas_por_cliente' => array_values($cuotasPorCliente),
                'clientes_morosos' => array_values($clientesMorosos)
            ]);
        } catch (Exception $e) {
            $this->responderError('Error al procesar las cuotas pagadas: ' . $e->getMessage());
        }
    }


    public function getEmpleados()
    {
        try {
            $query = "SELECT 
                        usuario_id, 
                        nombres, 
                        apellidos
                    FROM 
                        usuarios
                    WHERE 
                        estado = '1'
                    ORDER BY 
                        nombres ASC";

            $resultado = $this->conexion->query($query);

            if ($resultado) {
                $empleados = [];
                while ($fila = $resultado->fetch_assoc()) {
                    // Modificación: Reemplazar null por cadena vacía en apellidos
                    $fila['apellidos'] = $fila['apellidos'] ?? ''; // <-- Esta línea fue modificada
                    $empleados[] = $fila;
                }
                $this->responderExito($empleados);
            } else {
                throw new Exception("Error en la consulta: " . $this->conexion->error);
            }
        } catch (Exception $e) {
            $this->responderError('Error al obtener empleados: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint para descargar reporte en Excel
     */
    public function downloadExcel()
    {
        // Validar y obtener parámetros (los mismos que se usaron para generar el reporte)
        $tipoReporte = $_POST['tipo_reporte'] ?? '';
        $jsonData = $_POST['data'] ?? '{}';

        // Decodificar el JSON a un array asociativo
        $data = json_decode($jsonData, true);

        if (empty($tipoReporte) || empty($data)) {
            $this->responderError('Parámetros inválidos');
            return;
        }

        try {
            // Crear nuevo archivo Excel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Configurar el reporte según el tipo
            switch ($tipoReporte) {
                case 'ventas-generales':
                    $this->generarExcelVentasGenerales($spreadsheet, $data);
                    break;
                case 'ventas-por-empleado':
                    $this->generarExcelVentasPorEmpleado($spreadsheet, $data);
                    break;
                case 'financiamientos':
                    $this->generarExcelFinanciamientos($spreadsheet, $data);
                    break;
                case 'cuotas-pagadas':
                    $this->generarExcelCuotasPagadas($spreadsheet, $data);
                    break;
                case 'ventas-por-categoria':  // ← AGREGAR ESTA LÍNEA
                    $this->generarExcelVentasPorCategoria($spreadsheet, $data);  // ← AGREGAR ESTA LÍNEA
                    break;  // ← AGREGAR ESTA LÍNEA
                case 'ingresos':
                    $this->generarExcelIngresos($spreadsheet, $data);
                    break;
                default:
                    $this->responderError('Tipo de reporte no válido');
                    return;
            }

            // Configurar cabeceras para descarga
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Reporte_' . $tipoReporte . '_' . date('Y-m-d') . '.xlsx"');
            header('Cache-Control: max-age=0');

            // Crear el archivo Excel y enviarlo al navegador
            $writer = new Xlsx($spreadsheet);
            // Limpiar buffers antes de enviar el archivo
            if (ob_get_length())
                ob_end_clean(); // Finaliza buffer si está activo
            ob_start();                          // Inicia uno nuevo para asegurarse
            $writer->save('php://output');
            ob_end_flush(); // Envía el contenido y limpia

            exit;
        } catch (Exception $e) {
            $this->responderError('Error al generar el archivo Excel: ' . $e->getMessage());
        }
    }


    public function downloadPDF()
    {
        // Validar y obtener parámetros
        $tipoReporte = $_POST['tipo_reporte'] ?? '';
        $jsonData = $_POST['data'] ?? '{}';

        // Decodificar el JSON a un array asociativo
        $data = json_decode($jsonData, true);

        if (empty($tipoReporte) || empty($data)) {
            $this->responderError('Parámetros inválidos');
            return;
        }

        try {
            // Crear instancia de mPDF
            $this->mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 15,
                'margin_bottom' => 15
            ]);

            // Configurar el reporte según el tipo
            switch ($tipoReporte) {
                case 'ventas-generales':
                    $this->generarPDFVentasGenerales($data);
                    break;
                case 'ventas-por-empleado':
                    $this->generarPDFVentasPorEmpleado($data);
                    break;
                case 'financiamientos':
                    $this->generarPDFFinanciamientos($data);
                    break;
                case 'cuotas-pagadas':
                    $this->generarPDFCuotasPagadas($data);
                    break;
                case 'ventas-por-categoria':  // ← AGREGAR ESTA LÍNEA
                    $this->generarPDFVentasPorCategoria($data);  // ← AGREGAR ESTA LÍNEA
                    break;  // ← AGREGAR ESTA LÍNEA
                case 'ingresos':
                    $this->generarPDFIngresos($data);
                    break;
                default:
                    $this->responderError('Tipo de reporte no válido');
                    return;
            }

            // Salida del PDF
            $this->mpdf->Output('Reporte_' . $tipoReporte . '_' . date('Y-m-d') . '.pdf', 'D');
            exit;
        } catch (Exception $e) {
            $this->responderError('Error al generar el archivo PDF: ' . $e->getMessage());
        }
    }


    private function generarExcelVentasGenerales($spreadsheet, $data)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ventas Generales');

        // Definir encabezados
        $headers = ['Fecha', 'Cliente', 'Productos', 'Total', 'Vendedor', 'Método de Pago'];
        $this->configurarEncabezadosExcel($sheet, $headers);

        // Fila para el total general
        $sheet->setCellValue('A2', 'TOTAL GENERAL');
        $sheet->setCellValue('D2', $data['total_general'] ?? '0.00');
        $sheet->mergeCells('A2:C2');
        $sheet->getStyle('A2:F2')->getFont()->setBold(true);
        $sheet->getStyle('A2:F2')->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('f8f8fa');

        // Datos de ventas
        $row = 3;
        if (isset($data['registros']) && is_array($data['registros'])) {
            foreach ($data['registros'] as $venta) {
                $sheet->setCellValue('A' . $row, $venta['fecha'] ?? '');
                $sheet->setCellValue('B' . $row, $venta['cliente'] ?? '');
                $sheet->setCellValue('C' . $row, isset($venta['productos']) && is_array($venta['productos']) ?
                    implode(', ', $venta['productos']) : '');
                $sheet->setCellValue('D' . $row, $venta['total'] ?? '');
                $sheet->setCellValue('E' . $row, $venta['vendedor'] ?? '');
                $sheet->setCellValue('F' . $row, $venta['metodo_pago'] ?? '');
                $row++;
            }
        }

        // Ajustar anchos de columna
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function generarExcelVentasPorEmpleado($spreadsheet, $data)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ventas por Empleado');

        // Definir encabezados
        $headers = ['Asesor', 'Producto', 'Cantidad', 'Precio Unitario', 'Total Producto'];
        $this->configurarEncabezadosExcel($sheet, $headers);

        // Datos por asesor
        $row = 2;
        foreach ($data['registros'] as $asesor) {
            // Nombre del asesor
            $sheet->setCellValue('A' . $row, $asesor['asesor']);
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':E' . $row)->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('f8f8fa');
            $row++;

            // Productos del asesor
            foreach ($asesor['productos'] as $producto) {
                $sheet->setCellValue('A' . $row, '');
                $sheet->setCellValue('B' . $row, $producto['nombre']);
                $sheet->setCellValue('C' . $row, $producto['cantidad']);
                $sheet->setCellValue('D' . $row, $producto['precio_unitario']);
                $sheet->setCellValue('E' . $row, $producto['total_producto']);
                $row++;
            }

            // Subtotal por asesor
            $sheet->setCellValue('A' . $row, 'Subtotal');
            $sheet->setCellValue('E' . $row, $asesor['total']);
            $sheet->mergeCells('A' . $row . ':D' . $row);
            $sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
            $row++;

            // Espacio entre asesores
            $row++;
        }

        // Total general
        $sheet->setCellValue('A' . $row, 'TOTAL GENERAL');
        $sheet->setCellValue('E' . $row, $data['total_general']);
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':E' . $row)->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('f8f8fa');

        // Ajustar anchos de columna
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /* MÉTODOS AUXILIARES PARA OBTENER DATOS */

    /**
     * Obtiene el vendedor asociado a una venta
     */
    private function obtenerVendedor($id_venta)
    {
        try {
            $id_venta = $this->conexion->real_escape_string($id_venta); // Seguridad básica

            // Modificación: Cambiar CONCAT por CONCAT_WS para manejar valores NULL
            $query = "SELECT 
                        CONCAT_WS(' ', u.nombres, u.apellidos) AS vendedor
                    FROM 
                        ventas v
                    JOIN 
                        usuarios u ON v.id_vendedor = u.usuario_id
                    WHERE 
                        v.id_venta = '$id_venta'";

            $resultado = $this->conexion->query($query);

            if ($resultado && $fila = $resultado->fetch_assoc()) {
                return $fila['vendedor'];
            } else {
                return 'No asignado';
            }
        } catch (Exception $e) {
            return 'Error al obtener vendedor';
        }
    }

    /**
     * Obtiene el método de pago asociado a una venta
     */
    /**
     * Obtiene el método de pago asociado a una venta
     */
    private function obtenerMetodoPagoparaReporte($id_venta)
    {
        try {
            // Sanitizar y extraer la parte válida del id_venta
            $id_venta = $this->conexion->real_escape_string($id_venta);
            $id_venta = explode('-', $id_venta)[0]; // Eliminar desde el primer guion hacia la derecha
            $id_venta = preg_replace('/[^0-9]/', '', $id_venta); // Asegurar que sea numérico

            $query = "SELECT metodo_pago FROM ventas_pagos WHERE id_venta = '$id_venta' LIMIT 1";
            $resultado = $this->conexion->query($query);

            if ($resultado && $fila = $resultado->fetch_assoc()) {
                $id_metodo_pago = $this->conexion->real_escape_string($fila['metodo_pago']);

                $queryNombre = "SELECT nombre FROM metodo_pago WHERE id_metodo_pago = '$id_metodo_pago'";
                $resultadoNombre = $this->conexion->query($queryNombre);

                if ($resultadoNombre && $filaNombre = $resultadoNombre->fetch_assoc()) {
                    return $filaNombre['nombre'];
                }
            }

            $query2 = "SELECT metodo_pago FROM ventas_pagos WHERE id_venta = '$id_venta' AND metodo_pago = '99'";
            $resultado2 = $this->conexion->query($query2);

            if ($resultado2 && $resultado2->num_rows > 0) {
                return 'FLOTA';
            }

            return 'No especificado';

        } catch (Exception $e) {
            return 'Error al obtener método de pago';
        }
    }


    private function obtenerProductosVenta($id_venta)
    {
        try {
            $id_venta = $this->conexion->real_escape_string($id_venta); // Seguridad básica

            $query = "SELECT 
                    pv.descripcion,
                    p.nombre
                FROM 
                    productos_ventas pv
                LEFT JOIN 
                    productosv2 p ON pv.id_producto = p.idproductosv2
                WHERE 
                    pv.id_venta = '$id_venta'";

            $resultado = $this->conexion->query($query);

            $nombresProductos = [];
            if ($resultado) {
                while ($producto = $resultado->fetch_assoc()) {
                    $nombreProducto = !empty($producto['nombre']) ? $producto['nombre'] : $producto['descripcion'];
                    $nombresProductos[] = $nombreProducto;
                }
            }

            return $nombresProductos;
        } catch (Exception $e) {
            return ['Error al obtener productos'];
        }
    }

    private function obtenerDetallesProductosVenta($id_venta)
    {
        try {
            $id_venta = $this->conexion->real_escape_string($id_venta);

            $query = "SELECT 
                    pv.id_producto,
                    p.nombre,
                    pv.cantidad,
                    p.precio_venta AS precio_unitario,
                    (pv.cantidad * p.precio_venta) AS total_producto
                FROM 
                    productos_ventas pv
                JOIN 
                    productosv2 p ON pv.id_producto = p.idproductosv2
                WHERE 
                    pv.id_venta = '$id_venta'";

            $resultado = $this->conexion->query($query);

            if (!$resultado) {
                throw new Exception("Error en la consulta: " . $this->conexion->error);
            }

            $detalles = [];
            while ($fila = $resultado->fetch_assoc()) {
                $detalles[] = $fila;
            }

            return $detalles;
        } catch (Exception $e) {
            return [];
        }
    }


    private function obtenerDatosCliente($id_cliente)
    {
        try {
            $id_cliente = $this->conexion->real_escape_string($id_cliente);

            $query = "SELECT 
                    n_documento AS documento,
                    CONCAT(nombres, ' ', apellido_paterno, ' ', apellido_materno) AS nombre_completo
                FROM 
                    clientes_financiar
                WHERE 
                    id = '$id_cliente'";

            $resultado = $this->conexion->query($query);
            if (!$resultado) {
                throw new Exception("Error en la consulta: " . $this->conexion->error);
            }

            $fila = $resultado->fetch_assoc();

            return $fila ?: [
                'documento' => 'No disponible',
                'nombre_completo' => 'Cliente no encontrado'
            ];
        } catch (Exception $e) {
            return [
                'documento' => 'Error',
                'nombre_completo' => 'Error al obtener datos del cliente'
            ];
        }
    }

    /**
     * Obtiene datos de un conductor
     */
    private function obtenerDatosConductorparaReporte($id_conductor)
    {
        try {
            $id_conductor = $this->conexion->real_escape_string($id_conductor);

            $query = "SELECT 
                        nro_documento AS documento,
                        CONCAT(nombres, ' ', apellido_paterno, ' ', apellido_materno) AS nombre_completo,
                        numUnidad
                    FROM 
                        conductores
                    WHERE 
                        id_conductor = '$id_conductor'";

            $resultado = $this->conexion->query($query);

            if (!$resultado) {
                throw new Exception("Error en la consulta: " . $this->conexion->error);
            }

            $fila = $resultado->fetch_assoc();

            return $fila ?: [
                'documento' => 'No disponible',
                'nombre_completo' => 'Conductor no encontrado',
                'numUnidad' => 'N/A'
            ];
        } catch (Exception $e) {
            return [
                'documento' => 'Error',
                'nombre_completo' => 'Error al obtener datos del conductor',
                'numUnidad' => 'N/A'
            ];
        }
    }


    private function obtenerDatosProducto($id_producto)
    {
        try {
            $id_producto = $this->conexion->real_escape_string($id_producto);

            $query = "SELECT 
                        nombre,
                        precio_venta
                    FROM 
                        productosv2
                    WHERE 
                        idproductosv2 = '$id_producto'";

            $resultado = $this->conexion->query($query);
            if (!$resultado) {
                throw new Exception("Error en la consulta: " . $this->conexion->error);
            }

            $fila = $resultado->fetch_assoc();

            return $fila ?: [
                'nombre' => 'Producto no encontrado',
                'precio_venta' => 0
            ];
        } catch (Exception $e) {
            return [
                'nombre' => 'Error al obtener producto',
                'precio_venta' => 0
            ];
        }
    }

    /**
     * Obtiene información de cuotas de un financiamiento
     */
    private function obtenerInfoCuotas($id_financiamiento)
    {
        try {
            $id_financiamiento = $this->conexion->real_escape_string($id_financiamiento);

            // Contar cuotas totales
            $query1 = "SELECT 
                        COUNT(*) as total
                    FROM 
                        cuotas_financiamiento
                    WHERE 
                        id_financiamiento = '$id_financiamiento'";

            $resultado1 = $this->conexion->query($query1);
            if (!$resultado1) {
                throw new Exception("Error en la consulta 1: " . $this->conexion->error);
            }

            $totalCuotas = $resultado1->fetch_assoc()['total'] ?? 0;

            // Contar cuotas pagadas
            $query2 = "SELECT 
                        COUNT(*) as pagadas
                    FROM 
                        cuotas_financiamiento
                    WHERE 
                        id_financiamiento = '$id_financiamiento'
                        AND estado = 'pagado'";

            $resultado2 = $this->conexion->query($query2);
            if (!$resultado2) {
                throw new Exception("Error en la consulta 2: " . $this->conexion->error);
            }

            $cuotasPagadas = $resultado2->fetch_assoc()['pagadas'] ?? 0;

            // Calcular cuotas pendientes
            $cuotasPendientes = $totalCuotas - $cuotasPagadas;

            return [
                'cuotas_totales' => $totalCuotas,
                'cuotas_pagadas' => $cuotasPagadas,
                'cuotas_pendientes' => $cuotasPendientes
            ];
        } catch (Exception $e) {
            return [
                'cuotas_totales' => 0,
                'cuotas_pagadas' => 0,
                'cuotas_pendientes' => 0
            ];
        }
    }


    /**
     * Obtiene el número de cuotas vencidas de un financiamiento
     */
    private function obtenerCuotasVencidas($id_financiamiento)
    {
        try {
            $fechaActual = date('Y-m-d');

            // Construimos la consulta SQL
            $query = "SELECT 
                        COUNT(*) as vencidas
                    FROM 
                        cuotas_financiamiento
                    WHERE 
                        id_financiamiento = '$id_financiamiento'
                        AND fecha_vencimiento < '$fechaActual'
                        AND estado = 'En Progreso'";

            // Ejecutamos la consulta
            $resultado = $this->conexion->query($query);

            // Verificamos si hubo error en la consulta
            if (!$resultado) {
                throw new Exception("Error en la consulta: " . $this->conexion->error);
            }

            // Obtenemos el resultado de la consulta
            $row = $resultado->fetch_assoc();

            // Devolvemos el número de cuotas vencidas, si existe
            return $row['vencidas'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }


    /**
     * Valida el formato de una fecha
     */
    private function validarFecha($fecha)
    {
        $formato = 'Y-m-d';
        $fechaObj = DateTime::createFromFormat($formato, $fecha);
        return $fechaObj && $fechaObj->format($formato) === $fecha;
    }

    /**
     * Configura los encabezados para un archivo Excel
     */
    private function configurarEncabezadosExcel($sheet, $headers)
    {
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Estilo para encabezados
        $lastCol = chr(ord('A') + count($headers) - 1);
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $lastCol . '1')->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('f8f8fa');
        $sheet->getStyle('A1:' . $lastCol . '1')->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
    }

    /**
     * Genera el Excel para el reporte de financiamientos
     */
    private function generarExcelFinanciamientos($spreadsheet, $data)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Financiamientos');

        // Definir encabezados
        $headers = [
            'Nro. Documento',
            'Cliente',
            'Código Asociado',
            'Producto',
            'Grupo',
            'Cuotas Totales',
            'Cuotas Pagadas',
            'Cuotas Pendientes',
            'Saldo Pendiente',
            'Fecha Inicio',
            'Fecha Fin'
        ];

        // Agregar columna para conductores
        if (in_array('Conductor', array_column($data['registros'], 'tipo_cliente'))) {
            $headers[] = 'Número Unidad';
        }

        $this->configurarEncabezadosExcel($sheet, $headers);

        // Datos de financiamientos
        $row = 2;
        foreach ($data['registros'] as $financiamiento) {
            $col = 'A';
            $sheet->setCellValue($col++ . $row, $financiamiento['nro_documento']);
            $sheet->setCellValue($col++ . $row, $financiamiento['cliente']);
            $sheet->setCellValue($col++ . $row, $financiamiento['codigo_asociado']);
            $sheet->setCellValue($col++ . $row, $financiamiento['producto']);
            $sheet->setCellValue($col++ . $row, $financiamiento['grupo']);
            $sheet->setCellValue($col++ . $row, $financiamiento['cuotas_totales']);
            $sheet->setCellValue($col++ . $row, $financiamiento['cuotas_pagadas']);
            $sheet->setCellValue($col++ . $row, $financiamiento['cuotas_pendientes']);
            $sheet->setCellValue($col++ . $row, $financiamiento['saldo_pendiente']);
            $sheet->setCellValue($col++ . $row, $financiamiento['fecha_inicio']);
            $sheet->setCellValue($col++ . $row, $financiamiento['fecha_fin']);

            // Agregar número de unidad para conductores
            if (isset($financiamiento['numUnidad']) && in_array('Número Unidad', $headers)) {
                $sheet->setCellValue($col++ . $row, $financiamiento['numUnidad']);
            }

            $row++;
        }

        // Ajustar anchos de columna
        foreach (range('A', chr(ord('A') + count($headers) - 1)) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Método para obtener el nombre del grupo de financiamiento
     */
    private function obtenerNombreGrupoFinanciamiento($idVariante, $grupoFinanciamiento)
    {
        // Si id_variante no está vacío, buscar en grupos_variantes
        if (!empty($idVariante)) {
            $query = "SELECT nombre_variante 
                    FROM grupos_variantes 
                    WHERE idgrupos_variantes = ?";

            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $idVariante);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($variante = $result->fetch_assoc()) {
                return $variante['nombre_variante'];
            }
        }

        // Si id_variante está vacío o no se encontró, usar grupo_financiamiento
        if (is_numeric($grupoFinanciamiento)) {
            $query = "SELECT nombre_plan 
                    FROM planes_financiamiento 
                    WHERE idplan_financiamiento = ?";

            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $grupoFinanciamiento);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($plan = $result->fetch_assoc()) {
                return $plan['nombre_plan'];
            }
        }

        // Si no se encontró o grupo_financiamiento no es válido
        return $grupoFinanciamiento ?: 'Sin grupo';
    }

    /**
     * Genera el Excel para el reporte de cuotas pagadas
     */
    private function generarExcelCuotasPagadas($spreadsheet, $data)
    {
        // Hoja 1: Cuotas por cliente
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Cuotas por Cliente');

        // Definir encabezados para la primera hoja
        $headers1 = [
            'Nro. Documento',
            'Cliente',
            'Tipo',
            'Producto',
            'Cuotas Totales',
            'Cuotas Pagadas',
            'Cuotas Pendientes',
            'Saldo Pendiente',
            'Fecha Inicio',
            'Fecha Fin'
        ];

        // Agregar columna para conductores
        $incluirNumUnidad = false;
        foreach ($data['cuotas_por_cliente'] as $cliente) {
            if ($cliente['tipo_cliente'] == 'Conductor' && !empty($cliente['numUnidad'])) {
                $incluirNumUnidad = true;
                break;
            }
        }

        if ($incluirNumUnidad) {
            $headers1[] = 'Número Unidad';
        }

        $this->configurarEncabezadosExcel($sheet1, $headers1);

        // Datos de cuotas por cliente
        $row = 2;
        foreach ($data['cuotas_por_cliente'] as $cliente) {
            foreach ($cliente['financiamientos'] as $financiamiento) {
                $col = 'A';
                $sheet1->setCellValue($col++ . $row, $cliente['nro_documento']);
                $sheet1->setCellValue($col++ . $row, $cliente['cliente']);
                $sheet1->setCellValue($col++ . $row, $cliente['tipo_cliente']);
                $sheet1->setCellValue($col++ . $row, $financiamiento['producto']);
                $sheet1->setCellValue($col++ . $row, $financiamiento['cuotas_totales']);
                $sheet1->setCellValue($col++ . $row, $financiamiento['cuotas_pagadas']);
                $sheet1->setCellValue($col++ . $row, $financiamiento['cuotas_pendientes']);
                $sheet1->setCellValue($col++ . $row, $financiamiento['saldo_pendiente']);
                $sheet1->setCellValue($col++ . $row, $financiamiento['fecha_inicio']);
                $sheet1->setCellValue($col++ . $row, $financiamiento['fecha_fin']);

                if ($incluirNumUnidad) {
                    $sheet1->setCellValue($col++ . $row, $cliente['numUnidad']);
                }

                $row++;
            }
        }

        // Ajustar anchos de columna
        foreach (range('A', chr(ord('A') + count($headers1) - 1)) as $col) {
            $sheet1->getColumnDimension($col)->setAutoSize(true);
        }

        // Hoja 2: Clientes morosos
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Clientes Morosos');

        // Definir encabezados para la segunda hoja
        $headers2 = [
            'Nro. Documento',
            'Cliente',
            'Tipo',
            'Producto',
            'Cuotas Vencidas',
            'Monto Vencido'
        ];

        if ($incluirNumUnidad) {
            $headers2[] = 'Número Unidad';
        }

        $this->configurarEncabezadosExcel($sheet2, $headers2);

        // Datos de clientes morosos
        $row = 2;
        foreach ($data['clientes_morosos'] as $moroso) {
            $col = 'A';
            $sheet2->setCellValue($col++ . $row, $moroso['nro_documento']);
            $sheet2->setCellValue($col++ . $row, $moroso['cliente']);
            $sheet2->setCellValue($col++ . $row, $moroso['tipo_cliente']);
            $sheet2->setCellValue($col++ . $row, $moroso['producto']);
            $sheet2->setCellValue($col++ . $row, $moroso['cuotas_vencidas']);
            $sheet2->setCellValue($col++ . $row, number_format($moroso['monto_vencido'], 2));

            if ($incluirNumUnidad) {
                $sheet2->setCellValue($col++ . $row, $moroso['numUnidad']);
            }

            $row++;
        }

        // Ajustar anchos de columna
        foreach (range('A', chr(ord('A') + count($headers2) - 1)) as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Genera el PDF para el reporte de ventas generales
     */
    private function generarPDFVentasGenerales($data)
    {
        // Configurar encabezado
        $this->mpdf->SetHeader('Reporte de Ventas Generales|' . date('d/m/Y') . '|Página {PAGENO}');

        // Iniciar HTML
        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th { background-color: #f8f8fa; font-weight: bold; text-align: left; padding: 8px; border-bottom: 2px solid #ddd; }
            td { padding: 8px; border-bottom: 1px solid #ddd; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .total-row { background-color: #f8f8fa; font-weight: bold; }
            h1 { text-align: center; margin-bottom: 20px; }
        </style>
        
        <h1>Reporte de Ventas Generales</h1>
        
        <table>
            <tr class="total-row">
                <td colspan="3">TOTAL GENERAL</td>
                <td>' . ($data['total_general'] ?? '0.00') . '</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Productos</th>
                <th>Total</th>
                <th>Vendedor</th>
                <th>Método de Pago</th>
            </tr>';

        // Agregar filas de datos si existen registros
        if (isset($data['registros']) && is_array($data['registros'])) {
            foreach ($data['registros'] as $venta) {
                $html .= '
                <tr>
                    <td>' . ($venta['fecha'] ?? '') . '</td>
                    <td>' . ($venta['cliente'] ?? 'Sin cliente') . '</td>
                    <td>' . (isset($venta['productos']) && is_array($venta['productos']) ?
                        htmlspecialchars(implode(', ', $venta['productos'])) : '') . '</td>
                    <td>' . ($venta['total'] ?? '0.00') . '</td>
                    <td>' . ($venta['vendedor'] ?? 'No asignado') . '</td>
                    <td>' . ($venta['metodo_pago'] ?? 'No especificado') . '</td>
                </tr>';
            }
        }

        $html .= '</table>';

        // Escribir HTML al PDF
        $this->mpdf->WriteHTML($html);
    }


    /**
     * Genera el PDF para el reporte de ventas por empleado
     */
    private function generarPDFVentasPorEmpleado($data)
    {
        // Configurar encabezado
        $this->mpdf->SetHeader('Reporte de Ventas por Empleado|' . date('d/m/Y') . '|Página {PAGENO}');

        // Iniciar HTML
        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th { background-color: #f8f8fa; font-weight: bold; text-align: left; padding: 8px; border-bottom: 2px solid #ddd; }
            td { padding: 8px; border-bottom: 1px solid #ddd; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .asesor-row { background-color: #f8f8fa; font-weight: bold; }
            .subtotal-row { font-weight: bold; }
            .total-row { background-color: #f8f8fa; font-weight: bold; }
            h1 { text-align: center; margin-bottom: 20px; }
        </style>
        
        <h1>Reporte de Ventas por Empleado</h1>
        
        <table>';

        // Agregar filas de datos por asesor
        foreach ($data['registros'] as $asesor) {
            $html .= '
            <tr class="asesor-row">
                <td colspan="5">' . $asesor['asesor'] . '</td>
            </tr>
            <tr>
                <th></th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Total Producto</th>
            </tr>';

            foreach ($asesor['productos'] as $producto) {
                $html .= '
                <tr>
                    <td></td>
                    <td>' . $producto['nombre'] . '</td>
                    <td>' . $producto['cantidad'] . '</td>
                    <td>' . $producto['precio_unitario'] . '</td>
                    <td>' . $producto['total_producto'] . '</td>
                </tr>';
            }

            $html .= '
            <tr class="subtotal-row">
                <td colspan="4">Subtotal</td>
                <td>' . $asesor['total'] . '</td>
            </tr>
            <tr><td colspan="5">&nbsp;</td></tr>';
        }

        // Agregar total general
        $html .= '
        <tr class="total-row">
            <td colspan="4">TOTAL GENERAL</td>
            <td>' . $data['total_general'] . '</td>
        </tr>';

        $html .= '</table>';

        // Escribir HTML al PDF
        $this->mpdf->WriteHTML($html);
    }

    /**
     * Genera el PDF para el reporte de financiamientos
     */
    private function generarPDFFinanciamientos($data)
    {
        // Configurar encabezado
        $this->mpdf->SetHeader('Reporte de Financiamientos|' . date('d/m/Y') . '|Página {PAGENO}');

        // Determinar si incluir columna de número de unidad
        $incluirNumUnidad = false;
        foreach ($data['registros'] as $financiamiento) {
            if ($financiamiento['tipo_cliente'] == 'Conductor' && !empty($financiamiento['numUnidad'])) {
                $incluirNumUnidad = true;
                break;
            }
        }

        // Iniciar HTML
        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th { background-color: #f8f8fa; font-weight: bold; text-align: left; padding: 8px; border-bottom: 2px solid #ddd; }
            td { padding: 8px; border-bottom: 1px solid #ddd; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            h1 { text-align: center; margin-bottom: 20px; }
        </style>
        
        <h1>Reporte de Financiamientos</h1>
        
        <table>
            <tr>
                <th>Nro. Documento</th>
                <th>Cliente</th>
                <th>Código Asociado</th>
                <th>Producto</th>
                <th>Grupo</th>
                <th>Cuotas Totales</th>
                <th>Cuotas Pagadas</th>
                <th>Cuotas Pendientes</th>
                <th>Saldo Pendiente</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>';

        if ($incluirNumUnidad) {
            $html .= '<th>Número Unidad</th>';
        }

        $html .= '</tr>';

        // Agregar filas de datos
        foreach ($data['registros'] as $financiamiento) {
            $html .= '
            <tr>
                <td>' . $financiamiento['nro_documento'] . '</td>
                <td>' . $financiamiento['cliente'] . '</td>
                <td>' . $financiamiento['codigo_asociado'] . '</td>
                <td>' . $financiamiento['producto'] . '</td>
                <td>' . $financiamiento['grupo'] . '</td>
                <td>' . $financiamiento['cuotas_totales'] . '</td>
                <td>' . $financiamiento['cuotas_pagadas'] . '</td>
                <td>' . $financiamiento['cuotas_pendientes'] . '</td>
                <td>' . $financiamiento['saldo_pendiente'] . '</td>
                <td>' . $financiamiento['fecha_inicio'] . '</td>
                <td>' . $financiamiento['fecha_fin'] . '</td>';

            if ($incluirNumUnidad) {
                $html .= '<td>' . $financiamiento['numUnidad'] . '</td>';
            }

            $html .= '</tr>';
        }

        $html .= '</table>';

        // Escribir HTML al PDF
        $this->mpdf->WriteHTML($html);
    }

    /**
     * Genera el PDF para el reporte de cuotas pagadas
     */
    private function generarPDFCuotasPagadas($data)
    {
        // Configurar encabezado
        $this->mpdf->SetHeader('Reporte de Cuotas Pagadas|' . date('d/m/Y') . '|Página {PAGENO}');

        // Determinar si incluir columna de número de unidad
        $incluirNumUnidad = false;
        foreach ($data['cuotas_por_cliente'] as $cliente) {
            if ($cliente['tipo_cliente'] == 'Conductor' && !empty($cliente['numUnidad'])) {
                $incluirNumUnidad = true;
                break;
            }
        }

        // Iniciar HTML
        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th { background-color: #f8f8fa; font-weight: bold; text-align: left; padding: 8px; border-bottom: 2px solid #ddd; }
            td { padding: 8px; border-bottom: 1px solid #ddd; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            h1, h2 { text-align: center; }
            h1 { margin-bottom: 10px; }
            h2 { margin-top: 30px; margin-bottom: 20px; }
        </style>
        
        <h1>Reporte de Cuotas Pagadas</h1>
        
        <h2>Cuotas por Cliente</h2>
        
        <table>
            <tr>
                <th>Nro. Documento</th>
                <th>Cliente</th>
                <th>Tipo</th>
                <th>Producto</th>
                <th>Cuotas Totales</th>
                <th>Cuotas Pagadas</th>
                <th>Cuotas Pendientes</th>
                <th>Saldo Pendiente</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>';

        if ($incluirNumUnidad) {
            $html .= '<th>Número Unidad</th>';
        }

        $html .= '</tr>';

        // Agregar filas de datos de cuotas por cliente
        foreach ($data['cuotas_por_cliente'] as $cliente) {
            foreach ($cliente['financiamientos'] as $financiamiento) {
                $html .= '
                <tr>
                    <td>' . $cliente['nro_documento'] . '</td>
                    <td>' . $cliente['cliente'] . '</td>
                    <td>' . $cliente['tipo_cliente'] . '</td>
                    <td>' . $financiamiento['producto'] . '</td>
                    <td>' . $financiamiento['cuotas_totales'] . '</td>
                    <td>' . $financiamiento['cuotas_pagadas'] . '</td>
                    <td>' . $financiamiento['cuotas_pendientes'] . '</td>
                    <td>' . $financiamiento['saldo_pendiente'] . '</td>
                    <td>' . $financiamiento['fecha_inicio'] . '</td>
                    <td>' . $financiamiento['fecha_fin'] . '</td>';

                if ($incluirNumUnidad) {
                    $html .= '<td>' . $cliente['numUnidad'] . '</td>';
                }

                $html .= '</tr>';
            }
        }

        $html .= '</table>';

        // Sección de clientes morosos
        if (!empty($data['clientes_morosos'])) {
            $html .= '
            <h2>Clientes Morosos</h2>
            
            <table>
                <tr>
                    <th>Nro. Documento</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Producto</th>
                    <th>Cuotas Vencidas</th>
                    <th>Monto Vencido</th>';

            if ($incluirNumUnidad) {
                $html .= '<th>Número Unidad</th>';
            }

            $html .= '</tr>';

            // Agregar filas de datos de clientes morosos
            foreach ($data['clientes_morosos'] as $moroso) {
                $html .= '
                <tr>
                    <td>' . $moroso['nro_documento'] . '</td>
                    <td>' . $moroso['cliente'] . '</td>
                    <td>' . $moroso['tipo_cliente'] . '</td>
                    <td>' . $moroso['producto'] . '</td>
                    <td>' . $moroso['cuotas_vencidas'] . '</td>
                    <td>' . number_format($moroso['monto_vencido'], 2) . '</td>';

                if ($incluirNumUnidad) {
                    $html .= '<td>' . $moroso['numUnidad'] . '</td>';
                }

                $html .= '</tr>';
            }

            $html .= '</table>';
        }

        // Escribir HTML al PDF
        $this->mpdf->WriteHTML($html);
    }

    /**
     * Responde con un mensaje de éxito y datos
     */
    private function responderExito($data)
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
        exit;
    }

    /**
     * Responde con un mensaje de error
     */
    private function responderError($mensaje)
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $mensaje
        ]);
        exit;
    }

    public function ingresos()
    {
        // 🔴 Validar y obtener parámetros
        $fechaInicio = $_POST['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $_POST['fecha_fin'] ?? date('Y-m-d');

        // 🔴 Obtener filtros de tipo de ingreso
        $inscripcionContado = isset($_POST['inscripcion_contado']) ? filter_var($_POST['inscripcion_contado'], FILTER_VALIDATE_BOOLEAN) : true;
        $inscripcionFinanciadaCuotas = isset($_POST['inscripcion_financiada_cuotas']) ? filter_var($_POST['inscripcion_financiada_cuotas'], FILTER_VALIDATE_BOOLEAN) : true;
        $inscripcionFinanciadaInicial = isset($_POST['inscripcion_financiada_inicial']) ? filter_var($_POST['inscripcion_financiada_inicial'], FILTER_VALIDATE_BOOLEAN) : true;
        $financiamientoCuotaInicial = isset($_POST['financiamiento_cuota_inicial']) ? filter_var($_POST['financiamiento_cuota_inicial'], FILTER_VALIDATE_BOOLEAN) : true;
        $financiamientoCuotas = isset($_POST['financiamiento_cuotas']) ? filter_var($_POST['financiamiento_cuotas'], FILTER_VALIDATE_BOOLEAN) : true;
        $financiamientoMontoInscripcion = isset($_POST['financiamiento_monto_inscripcion']) ? filter_var($_POST['financiamiento_monto_inscripcion'], FILTER_VALIDATE_BOOLEAN) : true;
        $financiamientoMontoRecalculado = isset($_POST['financiamiento_monto_recalculado']) ? filter_var($_POST['financiamiento_monto_recalculado'], FILTER_VALIDATE_BOOLEAN) : true;
        $incluirVentas = isset($_POST['ventas']) ? filter_var($_POST['ventas'], FILTER_VALIDATE_BOOLEAN) : true;

        // 🔴 Obtener filtros de método de pago
        $metodosPago = isset($_POST['metodos_pago']) ? $_POST['metodos_pago'] : ['Efectivo', 'QR', 'Pago Bono', 'Transferencia', 'Tarjeta'];

        try {
            $resultados = [];
            $totalSoles = 0;
            $totalDolares = 0;

            // 🔴 1. Obtener inscripciones al contado
            if ($inscripcionContado) {
                $inscripcionesContado = $this->obtenerInscripcionesContado($fechaInicio, $fechaFin, $metodosPago);
                $resultados = array_merge($resultados, $inscripcionesContado['registros']);
                $totalSoles += $inscripcionesContado['total_soles'];
                $totalDolares += $inscripcionesContado['total_dolares'];
            }

            // 🔴 2. Obtener inscripciones financiadas - cuota inicial
            if ($inscripcionFinanciadaInicial) {
                $inscripcionesFinanciadasInicial = $this->obtenerInscripcionesFinanciadasInicial($fechaInicio, $fechaFin, $metodosPago);
                $resultados = array_merge($resultados, $inscripcionesFinanciadasInicial['registros']);
                $totalSoles += $inscripcionesFinanciadasInicial['total_soles'];
                $totalDolares += $inscripcionesFinanciadasInicial['total_dolares'];
            }

            // 🔴 3. Obtener inscripciones financiadas - cuotas
            if ($inscripcionFinanciadaCuotas) {
                $inscripcionesFinanciadasCuotas = $this->obtenerInscripcionesFinanciadasCuotas($fechaInicio, $fechaFin, $metodosPago);
                $resultados = array_merge($resultados, $inscripcionesFinanciadasCuotas['registros']);
                $totalSoles += $inscripcionesFinanciadasCuotas['total_soles'];
                $totalDolares += $inscripcionesFinanciadasCuotas['total_dolares'];
            }

            // 🔴 4. Obtener financiamientos - cuota inicial
            if ($financiamientoCuotaInicial) {
                $financiamientosCuotaInicial = $this->obtenerFinanciamientosCuotaInicial($fechaInicio, $fechaFin, $metodosPago);
                $resultados = array_merge($resultados, $financiamientosCuotaInicial['registros']);
                $totalSoles += $financiamientosCuotaInicial['total_soles'];
                $totalDolares += $financiamientosCuotaInicial['total_dolares'];
            }

            // 🔴 5. Obtener financiamientos - cuotas
            if ($financiamientoCuotas) {
                $financiamientosCuotas = $this->obtenerFinanciamientosCuotas($fechaInicio, $fechaFin, $metodosPago);
                $resultados = array_merge($resultados, $financiamientosCuotas['registros']);
                $totalSoles += $financiamientosCuotas['total_soles'];
                $totalDolares += $financiamientosCuotas['total_dolares'];
            }

            // 🔴 6. Obtener financiamientos - monto inscripción
            if ($financiamientoMontoInscripcion) {
                $financiamientosMontoInscripcion = $this->obtenerFinanciamientosMontoInscripcion($fechaInicio, $fechaFin, $metodosPago);
                $resultados = array_merge($resultados, $financiamientosMontoInscripcion['registros']);
                $totalSoles += $financiamientosMontoInscripcion['total_soles'];
                $totalDolares += $financiamientosMontoInscripcion['total_dolares'];
            }

            // 🔴 7. Obtener financiamientos - monto recalculado
            if ($financiamientoMontoRecalculado) {
                $financiamientosMontoRecalculado = $this->obtenerFinanciamientosMontoRecalculado($fechaInicio, $fechaFin, $metodosPago);
                $resultados = array_merge($resultados, $financiamientosMontoRecalculado['registros']);
                $totalSoles += $financiamientosMontoRecalculado['total_soles'];
                $totalDolares += $financiamientosMontoRecalculado['total_dolares'];
            }

            // 🔴 8. Obtener ventas
            if ($incluirVentas) {
                $ventas = $this->obtenerVentas($fechaInicio, $fechaFin, $metodosPago);
                $resultados = array_merge($resultados, $ventas['registros']);
                $totalSoles += $ventas['total_soles'];
                $totalDolares += $ventas['total_dolares'];
            }

            // 🔴 Ordenar resultados por fecha
            usort($resultados, function ($a, $b) {
                return strtotime($a['fecha']) - strtotime($b['fecha']);
            });

            $this->responderExito([
                'registros' => $resultados,
                'total_soles' => number_format($totalSoles, 2),
                'total_dolares' => number_format($totalDolares, 2),
                'tipo_cambio' => $this->obtenerTipoCambio()
            ]);
        } catch (Exception $e) {
            $this->responderError('Error al procesar los ingresos: ' . $e->getMessage());
        }
    }

    // 🔴 Función para obtener inscripciones al contado
    private function obtenerInscripcionesContado($fechaInicio, $fechaFin, $metodosPago)
    {
        $registros = [];
        $totalSoles = 0;
        $totalDolares = 0;

        // Obtener pagos al contado
        $query = "SELECT cp.id_conductorpago, cp.id_conductor, cp.fecha_pago 
                FROM conductor_pago cp 
                WHERE cp.id_tipopago = 1 
                AND cp.fecha_pago BETWEEN ? AND ?";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ss", $fechaInicio, $fechaFin);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($pago = $result->fetch_assoc()) {
            // Obtener detalles del pago de inscripción
            $queryPago = "SELECT pi.id_pago, pi.medio_pago, pi.monto, pi.id_asesor, pi.fecha_pago, pi.id_conductor 
                        FROM pagos_inscripcion pi 
                        WHERE pi.id_conductor = ?";

            $stmtPago = $this->conexion->prepare($queryPago);
            $stmtPago->bind_param("i", $pago['id_conductor']);
            $stmtPago->execute();
            $resultPago = $stmtPago->get_result();

            while ($detallePago = $resultPago->fetch_assoc()) {
                // Verificar si el método de pago está en los filtros
                if (!$this->verificarMetodoPago($detallePago['medio_pago'], $metodosPago)) {
                    continue;
                }

                // Obtener datos del conductor
                $conductor = $this->obtenerDatosConductorparaReporte($detallePago['id_conductor']);

                // Obtener datos del asesor
                $asesor = $this->obtenerDatosAsesor($detallePago['id_asesor']);

                // Formatear fecha
                $fechaFormateada = date('Y-m-d', strtotime($detallePago['fecha_pago']));

                // Determinar moneda (por defecto soles)
                $moneda = "S/.";

                // Agregar al total correspondiente
                if ($moneda == "S/.") {
                    $totalSoles += $detallePago['monto'];
                } else {
                    $totalDolares += $detallePago['monto'];
                }

                // Crear registro
                $registros[] = [
                    'fecha' => $fechaFormateada,
                    'tipo_ingreso' => 'Al contado',
                    'categoria' => 'Inscripción',
                    'detalle' => '',
                    'cliente' => $conductor['nombre_completo'],
                    'nro_documento' => $conductor['documento'],
                    'moneda' => $moneda,
                    'monto' => $detallePago['monto'],
                    'forma_pago' => $detallePago['medio_pago'],
                    'nro_cuota' => '',
                    'total_cuotas' => '',
                    'asesor_cobro' => $asesor['nombre_completo']
                ];
            }
        }

        return [
            'registros' => $registros,
            'total_soles' => $totalSoles,
            'total_dolares' => $totalDolares
        ];
    }

    // 🔴 Función para obtener inscripciones financiadas - cuota inicial
    private function obtenerInscripcionesFinanciadasInicial($fechaInicio, $fechaFin, $metodosPago)
    {
        $registros = [];
        $totalSoles = 0;
        $totalDolares = 0;

        // Obtener pagos financiados
        $query = "SELECT cp.id_conductorpago, cp.id_conductor, cp.fecha_pago 
                FROM conductor_pago cp 
                WHERE cp.id_tipopago = 2 
                AND cp.fecha_pago BETWEEN ? AND ?";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ss", $fechaInicio, $fechaFin);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($pago = $result->fetch_assoc()) {
            // Obtener pagos de inscripción
            $queryPagos = "SELECT pi.id_pago, pi.medio_pago, pi.monto, pi.id_asesor, pi.fecha_pago, pi.id_conductor 
                        FROM pagos_inscripcion pi 
                        WHERE pi.id_conductor = ?";

            $stmtPagos = $this->conexion->prepare($queryPagos);
            $stmtPagos->bind_param("i", $pago['id_conductor']);
            $stmtPagos->execute();
            $resultPagos = $stmtPagos->get_result();

            while ($detallePago = $resultPagos->fetch_assoc()) {
                // Verificar si es cuota inicial (no tiene registro en detalle_pago_inscripcion)
                $queryDetalle = "SELECT id_detallepago FROM detalle_pago_inscripcion 
                                WHERE idpagos_inscripcion = ?";

                $stmtDetalle = $this->conexion->prepare($queryDetalle);
                $stmtDetalle->bind_param("i", $detallePago['id_pago']);
                $stmtDetalle->execute();
                $resultDetalle = $stmtDetalle->get_result();

                // Si hay registros, no es cuota inicial
                if ($resultDetalle->num_rows > 0) {
                    continue;
                }

                // Verificar si el método de pago está en los filtros
                if (!$this->verificarMetodoPago($detallePago['medio_pago'], $metodosPago)) {
                    continue;
                }

                // Obtener datos del conductor
                $conductor = $this->obtenerDatosConductorparaReporte($detallePago['id_conductor']);

                // Obtener datos del asesor
                $asesor = $this->obtenerDatosAsesor($detallePago['id_asesor']);

                // Formatear fecha
                $fechaFormateada = date('Y-m-d', strtotime($detallePago['fecha_pago']));

                // Determinar moneda (por defecto soles)
                $moneda = "S/.";

                // Agregar al total correspondiente
                if ($moneda == "S/.") {
                    $totalSoles += $detallePago['monto'];
                } else {
                    $totalDolares += $detallePago['monto'];
                }

                // Crear registro
                $registros[] = [
                    'fecha' => $fechaFormateada,
                    'tipo_ingreso' => 'Financiado',
                    'categoria' => 'Inscripción',
                    'detalle' => 'Cuota Inicial',
                    'cliente' => $conductor['nombre_completo'],
                    'nro_documento' => $conductor['documento'],
                    'moneda' => $moneda,
                    'monto' => $detallePago['monto'],
                    'forma_pago' => $detallePago['medio_pago'],
                    'nro_cuota' => '',
                    'total_cuotas' => '1',
                    'asesor_cobro' => $asesor['nombre_completo']
                ];
            }
        }

        return [
            'registros' => $registros,
            'total_soles' => $totalSoles,
            'total_dolares' => $totalDolares
        ];
    }

    // 🔴 Función para obtener inscripciones financiadas - cuotas
    private function obtenerInscripcionesFinanciadasCuotas($fechaInicio, $fechaFin, $metodosPago)
    {
        $registros = [];
        $totalSoles = 0;
        $totalDolares = 0;

        // Obtener pagos de inscripción con detalle (cuotas)
        $query = "SELECT pi.id_pago, pi.medio_pago, pi.monto, pi.id_asesor, pi.fecha_pago, pi.id_conductor 
                FROM pagos_inscripcion pi 
                JOIN detalle_pago_inscripcion dpi ON pi.id_pago = dpi.idpagos_inscripcion 
                WHERE pi.fecha_pago BETWEEN ? AND ? 
                GROUP BY pi.id_pago";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ss", $fechaInicio, $fechaFin);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($pago = $result->fetch_assoc()) {
            // Verificar si el método de pago está en los filtros
            if (!$this->verificarMetodoPago($pago['medio_pago'], $metodosPago)) {
                continue;
            }

            // Obtener detalles de cuotas
            $queryDetalles = "SELECT dpi.id_detallepago, dpi.id_cuota 
                            FROM detalle_pago_inscripcion dpi 
                            WHERE dpi.idpagos_inscripcion = ?";

            $stmtDetalles = $this->conexion->prepare($queryDetalles);
            $stmtDetalles->bind_param("i", $pago['id_pago']);
            $stmtDetalles->execute();
            $resultDetalles = $stmtDetalles->get_result();

            $numerosCuota = [];
            $totalCuotas = 0;

            while ($detalle = $resultDetalles->fetch_assoc()) {
                // Obtener información de la cuota
                $queryCuota = "SELECT cc.numero_cuota 
                            FROM conductor_cuotas cc 
                            WHERE cc.id_conductorcuota = ?";

                $stmtCuota = $this->conexion->prepare($queryCuota);
                $stmtCuota->bind_param("i", $detalle['id_cuota']);
                $stmtCuota->execute();
                $resultCuota = $stmtCuota->get_result();

                if ($cuota = $resultCuota->fetch_assoc()) {
                    $numerosCuota[] = $cuota['numero_cuota'];
                    $totalCuotas++;
                }
            }

            // Obtener datos del conductor
            $conductor = $this->obtenerDatosConductorparaReporte($pago['id_conductor']);

            // Obtener datos del asesor
            $asesor = $this->obtenerDatosAsesor($pago['id_asesor']);

            // Formatear fecha
            $fechaFormateada = date('Y-m-d', strtotime($pago['fecha_pago']));

            // Determinar moneda (por defecto soles)
            $moneda = "S/.";

            // Agregar al total correspondiente
            if ($moneda == "S/.") {
                $totalSoles += $pago['monto'];
            } else {
                $totalDolares += $pago['monto'];
            }

            // Crear registro
            $registros[] = [
                'fecha' => $fechaFormateada,
                'tipo_ingreso' => 'Financiado',
                'categoria' => 'Inscripción',
                'detalle' => 'Cuotas',
                'cliente' => $conductor['nombre_completo'],
                'nro_documento' => $conductor['documento'],
                'moneda' => $moneda,
                'monto' => $pago['monto'],
                'forma_pago' => $pago['medio_pago'],
                'nro_cuota' => implode(' | ', $numerosCuota),
                'total_cuotas' => $totalCuotas,
                'asesor_cobro' => $asesor['nombre_completo']
            ];
        }

        return [
            'registros' => $registros,
            'total_soles' => $totalSoles,
            'total_dolares' => $totalDolares
        ];
    }

    // 🔴 Función para obtener financiamientos - cuota inicial
    private function obtenerFinanciamientosCuotaInicial($fechaInicio, $fechaFin, $metodosPago)
    {
        $registros = [];
        $totalSoles = 0;
        $totalDolares = 0;

        // Obtener pagos de financiamiento - cuota inicial
        $query = "SELECT pf.idpagos_financiamiento, pf.id_financiamiento, pf.id_conductor, pf.id_cliente, 
                        pf.id_asesor, pf.monto, pf.metodo_pago, pf.fecha_pago, pf.moneda, pf.concepto 
                FROM pagos_financiamiento pf 
                WHERE pf.concepto = 'Cuota Inicial' 
                AND pf.fecha_pago BETWEEN ? AND ?";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ss", $fechaInicio, $fechaFin);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($pago = $result->fetch_assoc()) {
            // Verificar si el método de pago está en los filtros
            if (!$this->verificarMetodoPago($pago['metodo_pago'], $metodosPago)) {
                continue;
            }

            // Obtener información del producto financiado
            $producto = $this->obtenerProductoFinanciado($pago['id_financiamiento']);

            // Obtener datos del cliente o conductor
            $cliente = [];
            $tipoCliente = '';

            if (!empty($pago['id_conductor'])) {
                $cliente = $this->obtenerDatosConductorparaReporte($pago['id_conductor']);
                $tipoCliente = 'Conductor';
            } elseif (!empty($pago['id_cliente'])) {
                $cliente = $this->obtenerDatosCliente($pago['id_cliente']);
                $tipoCliente = 'Cliente';
            }

            // Obtener datos del asesor
            $asesor = $this->obtenerDatosAsesor($pago['id_asesor']);

            // Formatear fecha
            $fechaFormateada = date('Y-m-d', strtotime($pago['fecha_pago']));

            // Determinar moneda
            $moneda = $pago['moneda'] ?: "S/.";

            // Agregar al total correspondiente
            if ($moneda == "S/.") {
                $totalSoles += $pago['monto'];
            } else {
                $totalDolares += $pago['monto'];
            }

            // Crear registro
            $registros[] = [
                'fecha' => $fechaFormateada,
                'tipo_ingreso' => 'Financiado',
                'categoria' => 'Financiamiento de ' . $producto,
                'detalle' => 'Cuota Inicial',
                'cliente' => $cliente['nombre_completo'],
                'nro_documento' => $cliente['documento'],
                'moneda' => $moneda,
                'monto' => $pago['monto'],
                'forma_pago' => $pago['metodo_pago'],
                'nro_cuota' => '',
                'total_cuotas' => '1',
                'asesor_cobro' => $asesor['nombre_completo']
            ];
        }

        return [
            'registros' => $registros,
            'total_soles' => $totalSoles,
            'total_dolares' => $totalDolares
        ];
    }

    // 🔴 Función para obtener financiamientos - cuotas
    private function obtenerFinanciamientosCuotas($fechaInicio, $fechaFin, $metodosPago)
    {
        $registros = [];
        $totalSoles = 0;
        $totalDolares = 0;

        // Obtener pagos de financiamiento - cuotas (sin concepto específico)
        $query = "SELECT pf.idpagos_financiamiento, pf.id_financiamiento, pf.id_conductor, pf.id_cliente, 
                        pf.id_asesor, pf.monto, pf.metodo_pago, pf.fecha_pago, pf.moneda, pf.concepto 
                FROM pagos_financiamiento pf 
                WHERE pf.concepto IS NULL 
                AND pf.fecha_pago BETWEEN ? AND ?";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ss", $fechaInicio, $fechaFin);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($pago = $result->fetch_assoc()) {
            // Verificar si el método de pago está en los filtros
            if (!$this->verificarMetodoPago($pago['metodo_pago'], $metodosPago)) {
                continue;
            }

            // Obtener detalles de cuotas
            $queryDetalles = "SELECT dpf.iddetalle_pago_financiamiento, dpf.id_cuota 
                            FROM detalle_pago_financiamiento dpf 
                            WHERE dpf.idfinanciamiento = ?";

            $stmtDetalles = $this->conexion->prepare($queryDetalles);
            $stmtDetalles->bind_param("i", $pago['idpagos_financiamiento']);
            $stmtDetalles->execute();
            $resultDetalles = $stmtDetalles->get_result();

            $numerosCuota = [];
            $totalCuotas = 0;
            $idFinanciamiento = null;

            while ($detalle = $resultDetalles->fetch_assoc()) {
                // Obtener información de la cuota
                $queryCuota = "SELECT cf.id_financiamiento, cf.numero_cuota 
                            FROM cuotas_financiamiento cf 
                            WHERE cf.idcuotas_financiamiento = ?";

                $stmtCuota = $this->conexion->prepare($queryCuota);
                $stmtCuota->bind_param("i", $detalle['id_cuota']);
                $stmtCuota->execute();
                $resultCuota = $stmtCuota->get_result();

                if ($cuota = $resultCuota->fetch_assoc()) {
                    $numerosCuota[] = $cuota['numero_cuota'];
                    $totalCuotas++;
                    $idFinanciamiento = $cuota['id_financiamiento'];
                }
            }

            // Si no se encontró id_financiamiento en las cuotas, usar el de la tabla pagos_financiamiento
            if (!$idFinanciamiento) {
                $idFinanciamiento = $pago['id_financiamiento'];
            }

            // Obtener información del producto financiado
            $producto = $this->obtenerProductoFinanciado($idFinanciamiento);

            // Obtener datos del cliente o conductor
            $cliente = [];
            $tipoCliente = '';

            if (!empty($pago['id_conductor'])) {
                $cliente = $this->obtenerDatosConductorparaReporte($pago['id_conductor']);
                $tipoCliente = 'Conductor';
            } elseif (!empty($pago['id_cliente'])) {
                $cliente = $this->obtenerDatosCliente($pago['id_cliente']);
                $tipoCliente = 'Cliente';
            }

            // Obtener datos del asesor
            $asesor = $this->obtenerDatosAsesor($pago['id_asesor']);

            // Formatear fecha
            $fechaFormateada = date('Y-m-d', strtotime($pago['fecha_pago']));

            // Determinar moneda
            $moneda = $pago['moneda'] ?: "S/.";

            // Agregar al total correspondiente
            if ($moneda == "S/.") {
                $totalSoles += $pago['monto'];
            } else {
                $totalDolares += $pago['monto'];
            }

            // Crear registro
            $registros[] = [
                'fecha' => $fechaFormateada,
                'tipo_ingreso' => 'Financiado',
                'categoria' => 'Financiamiento de ' . $producto,
                'detalle' => 'Pago de Cuotas',
                'cliente' => $cliente['nombre_completo'],
                'nro_documento' => $cliente['documento'],
                'moneda' => $moneda,
                'monto' => $pago['monto'],
                'forma_pago' => $pago['metodo_pago'],
                'nro_cuota' => implode(' | ', $numerosCuota),
                'total_cuotas' => $totalCuotas,
                'asesor_cobro' => $asesor['nombre_completo']
            ];
        }

        return [
            'registros' => $registros,
            'total_soles' => $totalSoles,
            'total_dolares' => $totalDolares
        ];
    }

    // 🔴 Función para obtener financiamientos - monto inscripción
    private function obtenerFinanciamientosMontoInscripcion($fechaInicio, $fechaFin, $metodosPago)
    {
        $registros = [];
        $totalSoles = 0;
        $totalDolares = 0;

        // Obtener pagos de financiamiento - monto inscripción
        $query = "SELECT pf.idpagos_financiamiento, pf.id_financiamiento, pf.id_conductor, pf.id_cliente, 
                        pf.id_asesor, pf.monto, pf.metodo_pago, pf.fecha_pago, pf.moneda, pf.concepto 
                FROM pagos_financiamiento pf 
                WHERE pf.concepto = 'Monto de Inscripción' 
                AND pf.fecha_pago BETWEEN ? AND ?";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ss", $fechaInicio, $fechaFin);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($pago = $result->fetch_assoc()) {
            // Verificar si el método de pago está en los filtros
            if (!$this->verificarMetodoPago($pago['metodo_pago'], $metodosPago)) {
                continue;
            }

            // Obtener información del producto financiado
            $producto = $this->obtenerProductoFinanciado($pago['id_financiamiento']);

            // Obtener datos del cliente o conductor
            $cliente = [];
            $tipoCliente = '';

            if (!empty($pago['id_conductor'])) {
                $cliente = $this->obtenerDatosConductorparaReporte($pago['id_conductor']);
                $tipoCliente = 'Conductor';
            } elseif (!empty($pago['id_cliente'])) {
                $cliente = $this->obtenerDatosCliente($pago['id_cliente']);
                $tipoCliente = 'Cliente';
            }

            // Obtener datos del asesor
            $asesor = $this->obtenerDatosAsesor($pago['id_asesor']);

            // Formatear fecha
            $fechaFormateada = date('Y-m-d', strtotime($pago['fecha_pago']));

            // Determinar moneda
            $moneda = $pago['moneda'] ?: "S/.";

            // Agregar al total correspondiente
            if ($moneda == "S/.") {
                $totalSoles += $pago['monto'];
            } else {
                $totalDolares += $pago['monto'];
            }

            // Crear registro
            $registros[] = [
                'fecha' => $fechaFormateada,
                'tipo_ingreso' => 'Financiado',
                'categoria' => 'Financiamiento de ' . $producto,
                'detalle' => 'Monto de Inscripción',
                'cliente' => $cliente['nombre_completo'],
                'nro_documento' => $cliente['documento'],
                'moneda' => $moneda,
                'monto' => $pago['monto'],
                'forma_pago' => $pago['metodo_pago'],
                'nro_cuota' => '',
                'total_cuotas' => '',
                'asesor_cobro' => $asesor['nombre_completo']
            ];
        }

        return [
            'registros' => $registros,
            'total_soles' => $totalSoles,
            'total_dolares' => $totalDolares
        ];
    }

    // 🔴 Función para obtener financiamientos - monto recalculado
    private function obtenerFinanciamientosMontoRecalculado($fechaInicio, $fechaFin, $metodosPago)
    {
        $registros = [];
        $totalSoles = 0;
        $totalDolares = 0;

        // Obtener pagos de financiamiento - monto recalculado
        $query = "SELECT pf.idpagos_financiamiento, pf.id_financiamiento, pf.id_conductor, pf.id_cliente, 
                        pf.id_asesor, pf.monto, pf.metodo_pago, pf.fecha_pago, pf.moneda, pf.concepto 
                FROM pagos_financiamiento pf 
                WHERE pf.concepto = 'Monto Recalculado' 
                AND pf.fecha_pago BETWEEN ? AND ?";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ss", $fechaInicio, $fechaFin);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($pago = $result->fetch_assoc()) {
            // Verificar si el método de pago está en los filtros
            if (!$this->verificarMetodoPago($pago['metodo_pago'], $metodosPago)) {
                continue;
            }

            // Obtener información del producto financiado
            $producto = $this->obtenerProductoFinanciado($pago['id_financiamiento']);

            // Obtener datos del cliente o conductor
            $cliente = [];
            $tipoCliente = '';

            if (!empty($pago['id_conductor'])) {
                $cliente = $this->obtenerDatosConductorparaReporte($pago['id_conductor']);
                $tipoCliente = 'Conductor';
            } elseif (!empty($pago['id_cliente'])) {
                $cliente = $this->obtenerDatosCliente($pago['id_cliente']);
                $tipoCliente = 'Cliente';
            }

            // Obtener datos del asesor
            $asesor = $this->obtenerDatosAsesor($pago['id_asesor']);

            // Formatear fecha
            $fechaFormateada = date('Y-m-d', strtotime($pago['fecha_pago']));

            // Determinar moneda
            $moneda = $pago['moneda'] ?: "S/.";

            // Agregar al total correspondiente
            if ($moneda == "S/.") {
                $totalSoles += $pago['monto'];
            } else {
                $totalDolares += $pago['monto'];
            }

            // Crear registro
            $registros[] = [
                'fecha' => $fechaFormateada,
                'tipo_ingreso' => 'Financiado',
                'categoria' => 'Financiamiento de ' . $producto,
                'detalle' => 'Monto Recalculado',
                'cliente' => $cliente['nombre_completo'],
                'nro_documento' => $cliente['documento'],
                'moneda' => $moneda,
                'monto' => $pago['monto'],
                'forma_pago' => $pago['metodo_pago'],
                'nro_cuota' => '',
                'total_cuotas' => '',
                'asesor_cobro' => $asesor['nombre_completo']
            ];
        }

        return [
            'registros' => $registros,
            'total_soles' => $totalSoles,
            'total_dolares' => $totalDolares
        ];
    }

    // 🔴 Función para obtener ventas
    private function obtenerVentas($fechaInicio, $fechaFin, $metodosPago)
    {
        $registros = [];
        $totalSoles = 0;
        $totalDolares = 0;

        // Obtener ventas
        $query = "SELECT v.id_venta, v.fecha_emision, v.id_cliente, v.total, v.id_vendedor, v.moneda 
                FROM ventas v 
                WHERE v.fecha_emision BETWEEN ? AND ?";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ss", $fechaInicio, $fechaFin);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($venta = $result->fetch_assoc()) {
            // Obtener método de pago
            $metodoPago = $this->obtenerMetodoPagoVenta($venta['id_venta']);

            // Verificar si el método de pago está en los filtros
            if (!$this->verificarMetodoPago($metodoPago, $metodosPago)) {
                continue;
            }

            // Obtener productos de la venta
            $productos = $this->obtenerProductosVentaParaReporte($venta['id_venta']);

            // Obtener datos del cliente
            $cliente = $this->obtenerDatosClienteVenta($venta['id_cliente']);

            // Obtener datos del vendedor
            $vendedor = $this->obtenerDatosAsesor($venta['id_vendedor']);

            // Formatear fecha
            $fechaFormateada = date('Y-m-d', strtotime($venta['fecha_emision']));

            // Determinar moneda
            $moneda = "S/."; // Las ventas son en soles por defecto

            // Agregar al total correspondiente
            if ($moneda == "S/.") {
                $totalSoles += $venta['total'];
            } else {
                $totalDolares += $venta['total'];
            }

            // Crear registro
            $registros[] = [
                'fecha' => $fechaFormateada,
                'tipo_ingreso' => 'Al contado',
                'categoria' => 'Venta',
                'detalle' => implode(' | ', $productos),
                'cliente' => $cliente['nombre_completo'],
                'nro_documento' => $cliente['documento'],
                'moneda' => $moneda,
                'monto' => $venta['total'],
                'forma_pago' => $metodoPago,
                'nro_cuota' => '',
                'total_cuotas' => '',
                'asesor_cobro' => $vendedor['nombre_completo']
            ];
        }

        return [
            'registros' => $registros,
            'total_soles' => $totalSoles,
            'total_dolares' => $totalDolares
        ];
    }

    // 🔴 Función para obtener datos del asesor
    private function obtenerDatosAsesor($idAsesor)
    {
        try {
            $idAsesor = $this->conexion->real_escape_string($idAsesor);

            $query = "SELECT nombres, apellidos 
                    FROM usuarios 
                    WHERE usuario_id = ?";

            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $idAsesor);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($asesor = $result->fetch_assoc()) {
                return [
                    'nombre_completo' => $asesor['nombres'] . ' ' . $asesor['apellidos']
                ];
            }

            return [
                'nombre_completo' => 'No registrado'
            ];
        } catch (Exception $e) {
            return [
                'nombre_completo' => 'Error al obtener asesor'
            ];
        }
    }

    // 🔴 Función para obtener producto financiado
    private function obtenerProductoFinanciado($idFinanciamiento)
    {
        try {
            if (!$idFinanciamiento) {
                return 'Producto no especificado';
            }

            $idFinanciamiento = $this->conexion->real_escape_string($idFinanciamiento);

            $query = "SELECT f.idproductosv2 
                    FROM financiamiento f 
                    WHERE f.idfinanciamiento = ?";

            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $idFinanciamiento);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($financiamiento = $result->fetch_assoc()) {
                $idProducto = $financiamiento['idproductosv2'];

                $queryProducto = "SELECT nombre 
                                FROM productosv2 
                                WHERE idproductosv2 = ?";

                $stmtProducto = $this->conexion->prepare($queryProducto);
                $stmtProducto->bind_param("i", $idProducto);
                $stmtProducto->execute();
                $resultProducto = $stmtProducto->get_result();

                if ($producto = $resultProducto->fetch_assoc()) {
                    return $producto['nombre'];
                }
            }

            return 'Producto no encontrado';
        } catch (Exception $e) {
            return 'Error al obtener producto';
        }
    }

    // 🔴 Función para obtener método de pago de venta
    private function obtenerMetodoPagoVenta($idVenta)
    {
        try {
            $idVenta = $this->conexion->real_escape_string($idVenta);

            $query = "SELECT vp.metodo_pago 
                    FROM ventas_pagos vp 
                    WHERE vp.id_venta = ? 
                    LIMIT 1";

            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $idVenta);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($pago = $result->fetch_assoc()) {
                $idMetodoPago = $pago['metodo_pago'];

                // Si es 99, es FLOTA
                if ($idMetodoPago == '99') {
                    return 'FLOTA';
                }

                $queryMetodo = "SELECT nombre 
                                FROM metodo_pago 
                                WHERE id_metodo_pago = ?";

                $stmtMetodo = $this->conexion->prepare($queryMetodo);
                $stmtMetodo->bind_param("i", $idMetodoPago);
                $stmtMetodo->execute();
                $resultMetodo = $stmtMetodo->get_result();

                if ($metodo = $resultMetodo->fetch_assoc()) {
                    return $metodo['nombre'];
                }
            }

            return 'No especificado';
        } catch (Exception $e) {
            return 'Error al obtener método de pago';
        }
    }

    // Renombrada para evitar conflicto con la función existente
    private function obtenerProductosVentaParaReporte($id_venta)
    {
        try {
            $id_venta = $this->conexion->real_escape_string($id_venta); // Seguridad básica

            $query = "SELECT 
                        pv.descripcion,
                        p.nombre
                    FROM 
                        productos_ventas pv
                    LEFT JOIN 
                        productosv2 p ON pv.id_producto = p.idproductosv2
                    WHERE 
                        pv.id_venta = '$id_venta'";

            $resultado = $this->conexion->query($query);

            $nombresProductos = [];
            if ($resultado) {
                while ($producto = $resultado->fetch_assoc()) {
                    $nombreProducto = !empty($producto['nombre']) ? $producto['nombre'] : $producto['descripcion'];
                    $nombresProductos[] = $nombreProducto;
                }
            }

            return $nombresProductos;
        } catch (Exception $e) {
            return ['Error al obtener productos'];
        }
    }

    // 🔴 Función para obtener datos del cliente de venta
    private function obtenerDatosClienteVenta($idCliente)
    {
        try {
            $idCliente = $this->conexion->real_escape_string($idCliente);

            $query = "SELECT documento, datos 
                    FROM clientes 
                    WHERE id_cliente = ?";

            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $idCliente);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($cliente = $result->fetch_assoc()) {
                return [
                    'documento' => $cliente['documento'],
                    'nombre_completo' => $cliente['datos']
                ];
            }

            return [
                'documento' => 'No disponible',
                'nombre_completo' => 'Cliente no encontrado'
            ];
        } catch (Exception $e) {
            return [
                'documento' => 'Error',
                'nombre_completo' => 'Error al obtener cliente'
            ];
        }
    }

    // 🔴 Función para verificar si un método de pago está en los filtros
    private function verificarMetodoPago($metodoPago, $metodosFiltro)
    {
        // Normalizar método de pago
        $metodoPago = strtolower($metodoPago);

        // Verificar si es efectivo
        if (strpos($metodoPago, 'efectivo') !== false && in_array('Efectivo', $metodosFiltro)) {
            return true;
        }

        // Verificar si es QR (Yape, Plin)
        if ((strpos($metodoPago, 'yape') !== false || strpos($metodoPago, 'plin') !== false) && in_array('QR', $metodosFiltro)) {
            return true;
        }

        // Verificar si es transferencia
        if (strpos($metodoPago, 'transferencia') !== false && in_array('Transferencia', $metodosFiltro)) {
            return true;
        }

        // Verificar si es tarjeta
        if (
            (strpos($metodoPago, 'tarjeta') !== false || strpos($metodoPago, 'visa') !== false ||
                strpos($metodoPago, 'mastercard') !== false || strpos($metodoPago, 'dinners') !== false ||
                strpos($metodoPago, 'pos') !== false) && in_array('Tarjeta', $metodosFiltro)
        ) {
            return true;
        }

        // Verificar si es bono
        if (strpos($metodoPago, 'bono') !== false && in_array('Pago Bono', $metodosFiltro)) {
            return true;
        }

        return false;
    }

    // 🔴 Función para obtener tipo de cambio
    private function obtenerTipoCambio()
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "/arequipago/TipoCambio");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($response, true);

            if (isset($data['tipo_cambio'])) {
                return $data['tipo_cambio'];
            }

            return 3.70; // Valor por defecto si no se puede obtener
        } catch (Exception $e) {
            return 3.70; // Valor por defecto en caso de error
        }
    }

    // 🔴 Función para generar Excel de ingresos
    private function generarExcelIngresos($spreadsheet, $data)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte de Ingresos');

        // Definir encabezados
        $headers = [
            'FECHA',
            'TIPO DE INGRESO',
            'CATEGORÍA',
            'DETALLE',
            'CLIENTE',
            'Nº DOCUMENTO',
            'MONEDA',
            'MONTO',
            'FORMA DE PAGO',
            'Nº CUOTA',
            'TOTAL CUOTAS',
            'ASESOR DE COBRO'
        ];

        // Agregar totales en la parte superior
        $sheet->setCellValue('A1', '💰 Total en soles (PEN): S/ ' . $data['total_soles']);
        $sheet->setCellValue('A2', '💵 Total en dólares (USD): $' . $data['total_dolares']);
        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        $sheet->getStyle('A1:L2')->getFont()->setBold(true);

        // Agregar encabezados en la fila 4
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }

        // Estilo para encabezados
        $sheet->getStyle('A4:L4')->getFont()->setBold(true);
        $sheet->getStyle('A4:L4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('f8f8fa');

        // Datos de ingresos
        $row = 5;
        foreach ($data['registros'] as $ingreso) {
            $col = 'A';
            $sheet->setCellValue($col++ . $row, $ingreso['fecha']);
            $sheet->setCellValue($col++ . $row, $ingreso['tipo_ingreso']);
            $sheet->setCellValue($col++ . $row, $ingreso['categoria']);
            $sheet->setCellValue($col++ . $row, $ingreso['detalle']);
            $sheet->setCellValue($col++ . $row, $ingreso['cliente']);
            $sheet->setCellValue($col++ . $row, $ingreso['nro_documento']);
            $sheet->setCellValue($col++ . $row, $ingreso['moneda']);
            $sheet->setCellValue($col++ . $row, $ingreso['monto']);
            $sheet->setCellValue($col++ . $row, $ingreso['forma_pago']);
            $sheet->setCellValue($col++ . $row, $ingreso['nro_cuota']);
            $sheet->setCellValue($col++ . $row, $ingreso['total_cuotas']);
            $sheet->setCellValue($col++ . $row, $ingreso['asesor_cobro']);
            $row++;
        }

        // Ajustar anchos de columna
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    // 🔴 Función para generar PDF de ingresos
    private function generarPDFIngresos($data)
    {
        // Configurar encabezado
        $this->mpdf->SetHeader('Reporte de Ingresos|' . date('d/m/Y') . '|Página {PAGENO}');

        // Iniciar HTML
        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th { background-color: #f8f8fa; font-weight: bold; text-align: left; padding: 8px; border-bottom: 2px solid #ddd; }
            td { padding: 8px; border-bottom: 1px solid #ddd; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            h1 { text-align: center; margin-bottom: 20px; }
            .totales { margin-bottom: 20px; }
            .total-row { font-weight: bold; margin-bottom: 5px; }
            .conversion-buttons { margin-bottom: 20px; }
            .btn { display: inline-block; padding: 8px 16px; background-color: #02a499; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px; }
        </style>
        
        <h1>Reporte de Ingresos</h1>
        
        <div class="totales">
            <div class="total-row">💰 Total en soles (PEN): S/ ' . $data['total_soles'] . '</div>
            <div class="total-row">💵 Total en dólares (USD): $' . $data['total_dolares'] . '</div>
        </div>
        
        <div class="conversion-buttons">
            <a href="#" class="btn" id="convertir-soles">Convertir todo a soles</a>
            <a href="#" class="btn" id="convertir-dolares">Convertir todo a dólares</a>
        </div>
        
        <table>
            <tr>
                <th>FECHA</th>
                <th>TIPO DE INGRESO</th>
                <th>CATEGORÍA</th>
                <th>DETALLE</th>
                <th>CLIENTE</th>
                <th>Nº DOCUMENTO</th>
                <th>MONEDA</th>
                <th>MONTO</th>
                <th>FORMA DE PAGO</th>
                <th>Nº CUOTA</th>
                <th>TOTAL CUOTAS</th>
                <th>ASESOR DE COBRO</th>
            </tr>';

        // Agregar filas de datos
        foreach ($data['registros'] as $ingreso) {
            $html .= '
            <tr>
                <td>' . $ingreso['fecha'] . '</td>
                <td>' . $ingreso['tipo_ingreso'] . '</td>
                <td>' . $ingreso['categoria'] . '</td>
                <td>' . $ingreso['detalle'] . '</td>
                <td>' . $ingreso['cliente'] . '</td>
                <td>' . $ingreso['nro_documento'] . '</td>
                <td>' . $ingreso['moneda'] . '</td>
                <td>' . $ingreso['monto'] . '</td>
                <td>' . $ingreso['forma_pago'] . '</td>
                <td>' . $ingreso['nro_cuota'] . '</td>
                <td>' . $ingreso['total_cuotas'] . '</td>
                <td>' . $ingreso['asesor_cobro'] . '</td>
            </tr>';
        }

        $html .= '</table>';

        // Agregar script para conversión de moneda
        $html .= '
        <script>
            document.getElementById("convertir-soles").addEventListener("click", function() {
                convertirMoneda("soles");
            });
            
            document.getElementById("convertir-dolares").addEventListener("click", function() {
                convertirMoneda("dolares");
            });
            
            function convertirMoneda(tipo) {
                const tipoCambio = ' . $data['tipo_cambio'] . ';
                // Lógica de conversión
            }
        </script>';

        // Escribir HTML al PDF
        $this->mpdf->WriteHTML($html);
    }

    private function generarExcelVentasPorCategoria($spreadsheet, $data)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ventas por Categoría');

        // Agregar totales en la parte superior
        $sheet->setCellValue('A1', '💰 Total en soles (PEN): S/ ' . $data['total_soles']);
        $sheet->setCellValue('A2', '💵 Total en dólares (USD): $' . $data['total_dolares']);
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A1:J2')->getFont()->setBold(true);

        // Definir encabezados en la fila 4
        $headers = [
            'CATEGORÍA',
            'PRODUCTO',
            'TIPO DE VENTA',
            'CANTIDAD VENDIDA',
            'PRECIO UNITARIO',
            'TOTAL PRODUCTO',
            'MONEDA',
            'GRUPO/VARIANTE',
            'FECHA EMISIÓN',
            'VENDEDOR'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }

        // Estilo para encabezados
        $sheet->getStyle('A4:J4')->getFont()->setBold(true);
        $sheet->getStyle('A4:J4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('f8f8fa');

        // Agrupar datos por categoría
        $categorias = [];
        foreach ($data['registros'] as $item) {
            if (!isset($categorias[$item['categoria']])) {
                $categorias[$item['categoria']] = [];
            }
            $categorias[$item['categoria']][] = $item;
        }

        // Datos por categoría
        $row = 5;
        foreach ($categorias as $categoria => $productos) {
            // Fila de categoría
            $sheet->setCellValue('A' . $row, $categoria);
            $sheet->mergeCells('A' . $row . ':J' . $row);
            $sheet->getStyle('A' . $row . ':J' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('e9ecef');
            $row++;

            $totalCategoriaSoles = 0;
            $totalCategoriaDolares = 0;

            // Productos de la categoría
            foreach ($productos as $producto) {
                $col = 'A';
                $sheet->setCellValue($col++ . $row, '');
                $sheet->setCellValue($col++ . $row, $producto['producto']);
                $sheet->setCellValue($col++ . $row, $producto['tipo_venta']);
                $sheet->setCellValue($col++ . $row, $producto['cantidad']);
                $sheet->setCellValue($col++ . $row, $producto['precio_unitario']);
                $sheet->setCellValue($col++ . $row, $producto['total_producto']);
                $sheet->setCellValue($col++ . $row, $producto['moneda']);
                $sheet->setCellValue($col++ . $row, $producto['grupo_variante']);
                $sheet->setCellValue($col++ . $row, date('d/m/Y', strtotime($producto['fecha_emision'])));
                $sheet->setCellValue($col++ . $row, $producto['nombre_vendedor']);

                // Sumar totales por categoría
                if ($producto['moneda'] === 'S/.') {
                    $totalCategoriaSoles += floatval(str_replace([',', 'S/', '$'], '', $producto['total_producto']));
                } else {
                    $totalCategoriaDolares += floatval(str_replace([',', 'S/', '$'], '', $producto['total_producto']));
                }

                $row++;
            }

            // Subtotal por categoría
            $sheet->setCellValue('A' . $row, 'Subtotal ' . $categoria);
            $sheet->setCellValue('F' . $row, 'S/ ' . number_format($totalCategoriaSoles, 2) . ' | $ ' . number_format($totalCategoriaDolares, 2));
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $sheet->getStyle('A' . $row . ':J' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('d1ecf1');
            $row++;

            // Espacio entre categorías
            $row++;
        }

        // Total general
        $sheet->setCellValue('A' . $row, 'TOTAL GENERAL');
        $sheet->setCellValue('F' . $row, 'S/ ' . $data['total_soles'] . ' | $ ' . $data['total_dolares']);
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->getStyle('A' . $row . ':J' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('343a40');
        $sheet->getStyle('A' . $row . ':J' . $row)->getFont()->getColor()->setRGB('ffffff');

        // Ajustar anchos de columna
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Genera el PDF para el reporte de ventas por categoría
     */
    private function generarPDFVentasPorCategoria($data)
    {
        // Configurar encabezado
        $this->mpdf->SetHeader('Reporte de Ventas por Categoría|' . date('d/m/Y') . '|Página {PAGENO}');

        // Iniciar HTML
        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th { background-color: #f8f8fa; font-weight: bold; text-align: left; padding: 8px; border-bottom: 2px solid #ddd; }
            td { padding: 8px; border-bottom: 1px solid #ddd; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .categoria-row { background-color: #e9ecef; font-weight: bold; }
            .subtotal-row { background-color: #d1ecf1; font-weight: bold; }
            .total-row { background-color: #343a40; color: white; font-weight: bold; }
            h1 { text-align: center; margin-bottom: 20px; }
            .totales { margin-bottom: 20px; }
            .total-item { font-weight: bold; margin-bottom: 5px; }
        </style>
        
        <h1>Reporte de Ventas por Categoría</h1>
        
        <div class="totales">
            <div class="total-item">💰 Total en soles (PEN): S/ ' . $data['total_soles'] . '</div>
            <div class="total-item">💵 Total en dólares (USD): $' . $data['total_dolares'] . '</div>
        </div>
        
        <table>
            <tr>
                <th>CATEGORÍA</th>
                <th>PRODUCTO</th>
                <th>TIPO DE VENTA</th>
                <th>CANTIDAD</th>
                <th>PRECIO UNIT.</th>
                <th>TOTAL</th>
                <th>MONEDA</th>
              <th>GRUPO/VARIANTE</th>
<th>FECHA EMISIÓN</th>
<th>VENDEDOR</th>
            </tr>';

        // Agrupar por categoría
        $categorias = [];
        foreach ($data['registros'] as $item) {
            if (!isset($categorias[$item['categoria']])) {
                $categorias[$item['categoria']] = [];
            }
            $categorias[$item['categoria']][] = $item;
        }

        // Renderizar por categoría
        foreach ($categorias as $categoria => $productos) {
            // Fila de categoría
            $html .= '
            <tr class="categoria-row">
                <td colspan="8">' . $categoria . '</td>
            </tr>';

            $totalCategoriaSoles = 0;
            $totalCategoriaDolares = 0;

            // Productos de la categoría
            foreach ($productos as $producto) {
                $html .= '
                <tr>
                    <td></td>
                    <td>' . $producto['producto'] . '</td>
                    <td>' . $producto['tipo_venta'] . '</td>
                    <td>' . $producto['cantidad'] . '</td>
                    <td>' . $producto['precio_unitario'] . '</td>
                    <td>' . $producto['total_producto'] . '</td>
                    <td>' . $producto['moneda'] . '</td>
                   <td>' . ($producto['grupo_variante'] ?: '') . '</td>
<td>' . date('d/m/Y', strtotime($producto['fecha_emision'])) . '</td>
<td>' . $producto['nombre_vendedor'] . '</td>
                </tr>';

                // Sumar totales por categoría
                if ($producto['moneda'] === 'S/.') {
                    $totalCategoriaSoles += floatval(str_replace([',', 'S/', '$'], '', $producto['total_producto']));
                } else {
                    $totalCategoriaDolares += floatval(str_replace([',', 'S/', '$'], '', $producto['total_producto']));
                }

            }

            // Subtotal por categoría
            $html .= '
            <tr class="subtotal-row">
                <td colspan="5">Subtotal ' . $categoria . '</td>
                <td>S/ ' . number_format($totalCategoriaSoles, 2) . ' | $ ' . number_format($totalCategoriaDolares, 2) . '</td>
                <td colspan="2"></td>
            </tr>
            <tr><td colspan="8">&nbsp;</td></tr>';
        }

        // Total general
        $html .= '
        <tr class="total-row">
            <td colspan="5">TOTAL GENERAL</td>
            <td>S/ ' . $data['total_soles'] . ' | $ ' . $data['total_dolares'] . '</td>
            <td colspan="2"></td>
        </tr>';

        $html .= '</table>';

        // Escribir HTML al PDF
        $this->mpdf->WriteHTML($html);
    }

    public function getCategorias()
    {
        try {
            $query = "SELECT 
                        idcategoria_producto, 
                        nombre
                    FROM 
                        categoria_producto
                    ORDER BY 
                        nombre ASC";

            $resultado = $this->conexion->query($query);

            if ($resultado) {
                $categorias = [];
                while ($fila = $resultado->fetch_assoc()) {
                    $categorias[] = $fila;
                }
                $this->responderExito($categorias);
            } else {
                throw new Exception("Error en la consulta: " . $this->conexion->error);
            }
        } catch (Exception $e) {
            $this->responderError('Error al obtener categorías: ' . $e->getMessage());
        }
    }

    public function getProductosPorCategoria()
    {
        try {
            $categorias = $_POST['categorias'] ?? [];

            if (empty($categorias)) {
                $this->responderError('No se especificaron categorías');
                return;
            }

            // Escapar categorías para seguridad
            $categoriasEscapadas = array_map(function ($cat) {
                return "'" . $this->conexion->real_escape_string($cat) . "'";
            }, $categorias);

            $categoriasStr = implode(',', $categoriasEscapadas);

            $query = "SELECT 
                        idproductosv2, 
                        nombre,
                        categoria
                    FROM 
                        productosv2
                    WHERE 
                        categoria IN ($categoriasStr)
                        AND estado = '1'
                    ORDER BY 
                        categoria, nombre ASC";

            $resultado = $this->conexion->query($query);

            if ($resultado) {
                $productos = [];
                while ($fila = $resultado->fetch_assoc()) {
                    $productos[] = $fila;
                }
                $this->responderExito($productos);
            } else {
                throw new Exception("Error en la consulta: " . $this->conexion->error);
            }
        } catch (Exception $e) {
            $this->responderError('Error al obtener productos: ' . $e->getMessage());
        }
    }

    public function getGruposFinanciamiento()
    {
        try {
            $query = "SELECT 
                        idplan_financiamiento, 
                        nombre_plan
                    FROM 
                        planes_financiamiento
                    ORDER BY 
                        nombre_plan ASC";

            $resultado = $this->conexion->query($query);

            if ($resultado) {
                $grupos = [];
                while ($fila = $resultado->fetch_assoc()) {
                    $grupos[] = $fila;
                }
                $this->responderExito($grupos);
            } else {
                throw new Exception("Error en la consulta: " . $this->conexion->error);
            }
        } catch (Exception $e) {
            $this->responderError('Error al obtener grupos de financiamiento: ' . $e->getMessage());
        }
    }

    public function getVariantesPorGrupo()
    {
        try {
            $grupos = $_POST['grupos'] ?? [];

            if (empty($grupos)) {
                $this->responderError('No se especificaron grupos');
                return;
            }

            // Escapar grupos para seguridad
            $gruposEscapados = array_map(function ($grupo) {
                return intval($grupo);
            }, $grupos);

            $gruposStr = implode(',', $gruposEscapados);

            $query = "SELECT 
                        idgrupos_variantes, 
                        nombre_variante,
                        idplan_financiamiento
                    FROM 
                        grupos_variantes
                    WHERE 
                        idplan_financiamiento IN ($gruposStr)
                    ORDER BY 
                        nombre_variante ASC";

            $resultado = $this->conexion->query($query);

            if ($resultado) {
                $variantes = [];
                while ($fila = $resultado->fetch_assoc()) {
                    $variantes[] = $fila;
                }
                $this->responderExito($variantes);
            } else {
                throw new Exception("Error en la consulta: " . $this->conexion->error);
            }
        } catch (Exception $e) {
            $this->responderError('Error al obtener variantes: ' . $e->getMessage());
        }
    }

    public function ventasPorCategoria()
    {

        // Validar y obtener parámetros
        $fechaInicio = $_POST['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $_POST['fecha_fin'] ?? date('Y-m-d');
        $categorias = $_POST['categorias'] ?? [];
        $productos = $_POST['productos'] ?? [];
        $tipoVenta = $_POST['tipo_venta'] ?? 'todos';
        $moneda = $_POST['moneda'] ?? 'todos';
        $grupos = $_POST['grupos'] ?? [];
        $variantes = $_POST['variantes'] ?? [];

        // Validar formato de fechas
        if (!$this->validarFecha($fechaInicio) || !$this->validarFecha($fechaFin)) {
            $this->responderError('Formato de fecha inválido');
            return;
        }

        try {
            $resultados = [];
            $totalSoles = 0;
            $totalDolares = 0;

            // 1. Obtener ventas normales si aplica
            if ($tipoVenta === 'todos' || $tipoVenta === 'venta') {
                $ventasNormales = $this->obtenerVentasNormalesPorCategoria($fechaInicio, $fechaFin, $categorias, $productos, $moneda);
                $resultados = array_merge($resultados, $ventasNormales['registros']);
                $totalSoles += $ventasNormales['total_soles'];
                $totalDolares += $ventasNormales['total_dolares'];
            }

            // 2. Obtener financiamientos si aplica
            if ($tipoVenta === 'todos' || $tipoVenta === 'financiamiento') {
                $financiamientos = $this->obtenerFinanciamientosPorCategoria($fechaInicio, $fechaFin, $categorias, $productos, $moneda, $grupos, $variantes);
                $resultados = array_merge($resultados, $financiamientos['registros']);
                $totalSoles += $financiamientos['total_soles'];
                $totalDolares += $financiamientos['total_dolares'];
            }

            // Ordenar resultados por categoría y producto
            usort($resultados, function ($a, $b) {
                if ($a['categoria'] === $b['categoria']) {
                    return strcmp($a['producto'], $b['producto']);
                }
                return strcmp($a['categoria'], $b['categoria']);
            });

            $this->responderExito([
                'registros' => $resultados,
                'total_soles' => number_format($totalSoles, 2),
                'total_dolares' => number_format($totalDolares, 2)
            ]);
        } catch (Exception $e) {
            $this->responderError('Error al procesar las ventas por categoría: ' . $e->getMessage());
        }
    }

    private function obtenerVentasNormalesPorCategoria($fechaInicio, $fechaFin, $categorias, $productos, $moneda)
    {
        $registros = [];
        $totalSoles = 0;
        $totalDolares = 0;

        try {
            // Construir condiciones WHERE
            $condicionCategorias = '';
            if (!empty($categorias)) {
                $categoriasEscapadas = array_map(function ($cat) {
                    return "'" . $this->conexion->real_escape_string($cat) . "'";
                }, $categorias);
                $condicionCategorias = " AND p.categoria IN (" . implode(',', $categoriasEscapadas) . ")";
            }

            $condicionProductos = '';
            if (!empty($productos)) {
                $productosEscapados = array_map('intval', $productos);
                $condicionProductos = " AND p.idproductosv2 IN (" . implode(',', $productosEscapados) . ")";
            }

            // Consulta principal para ventas normales
            $query = "SELECT 
            v.id_venta,
            v.fecha_emision,
            v.total as total_venta,
            pv.id_producto,
            pv.cantidad,
            pv.precio,
            p.nombre as producto_nombre,
            p.categoria,
            p.precio_venta,
            u.usuario as nombre_vendedor
        FROM 
            ventas v
       JOIN 
            productos_ventas pv ON v.id_venta = pv.id_venta
        JOIN 
            productosv2 p ON pv.id_producto = p.idproductosv2
        JOIN 
            usuarios u ON v.id_vendedor =u.usuario_id
        WHERE 
            v.fecha_emision BETWEEN '$fechaInicio' AND '$fechaFin'
            $condicionCategorias
            $condicionProductos
       ORDER BY 
            p.categoria, p.nombre";

            $resultado = $this->conexion->query($query);

            if (!$resultado) {
                throw new Exception("Error en la consulta de ventas normales: " . $this->conexion->error);
            }

            while ($fila = $resultado->fetch_assoc()) {
                // Calcular total del producto
                $totalProducto = $fila['cantidad'] * $fila['precio'];

                // Filtrar por moneda (ventas normales siempre en soles)
                if ($moneda === 'dolares') {
                    continue; // Saltar si solo queremos dólares
                }

                $registros[] = [
                    'categoria' => $fila['categoria'],
                    'producto' => $fila['producto_nombre'],
                    'tipo_venta' => 'Venta Normal',
                    'cantidad' => $fila['cantidad'],
                    'precio_unitario' => number_format($fila['precio'], 2),
                    'total_producto' => number_format($totalProducto, 2),
                    'total_producto_raw' => $totalProducto, // Agregar valor sin formato
                    'moneda' => 'S/.',
                    'grupo_variante' => '',
                    'fecha_emision' => $fila['fecha_emision'],
                    'nombre_vendedor' => $fila['nombre_vendedor'] ?? 'No asignado'
                ];


                $totalSoles += $totalProducto;
            }

        } catch (Exception $e) {
            throw new Exception("Error al obtener ventas normales: " . $e->getMessage());
        }

        return [
            'registros' => $registros,
            'total_soles' => $totalSoles,
            'total_dolares' => $totalDolares
        ];
    }

    private function obtenerFinanciamientosPorCategoria($fechaInicio, $fechaFin, $categorias, $productos, $moneda, $grupos, $variantes)
    {
        $registros = [];
        $totalSoles = 0;
        $totalDolares = 0;

        try {
            // Construir condiciones WHERE
            $condicionCategorias = '';
            if (!empty($categorias)) {
                $categoriasEscapadas = array_map(function ($cat) {
                    return "'" . $this->conexion->real_escape_string($cat) . "'";
                }, $categorias);
                $condicionCategorias = " AND p.categoria IN (" . implode(',', $categoriasEscapadas) . ")";
            }

            $condicionProductos = '';
            if (!empty($productos)) {
                $productosEscapados = array_map('intval', $productos);
                $condicionProductos = " AND p.idproductosv2 IN (" . implode(',', $productosEscapados) . ")";
            }

            $condicionGrupos = '';
            if (!empty($grupos)) {
                $gruposEscapados = array_map('intval', $grupos);
                $condicionGrupos = " AND f.grupo_financiamiento IN (" . implode(',', $gruposEscapados) . ")";
            }

            $condicionVariantes = '';
            if (!empty($variantes)) {
                $variantesEscapadas = array_map('intval', $variantes);
                $condicionVariantes = " AND f.id_variante IN (" . implode(',', $variantesEscapadas) . ")";
            }

            $query = "SELECT
                         f.idfinanciamiento,
                         f.fecha_creacion,
                         f.monto_total,
                         f.cantidad_producto,
                         f.grupo_financiamiento,
                         f.id_variante,
                         f.moneda,
                         p.nombre as producto_nombre,
                         p.categoria,
                         p.precio_venta,
                         u.usuario as nombre_vendedor
                     FROM
                         financiamiento f
                     JOIN
                         productosv2 p ON f.idproductosv2 = p.idproductosv2
                     LEFT JOIN
                         pagos_financiamiento pf ON f.idfinanciamiento = pf.id_financiamiento
                     LEFT JOIN
                         usuarios u ON pf.id_asesor = u.usuario_id
                     WHERE
                         DATE(f.fecha_creacion) BETWEEN '$fechaInicio' AND '$fechaFin'
                         $condicionCategorias
                         $condicionProductos
                         $condicionGrupos
                         $condicionVariantes
                     GROUP BY f.idfinanciamiento
                     ORDER BY
                         p.categoria, p.nombre";


            $resultado = $this->conexion->query($query);

            if (!$resultado) {
                throw new Exception("Error en la consulta de financiamientos: " . $this->conexion->error);
            }

            while ($fila = $resultado->fetch_assoc()) {
                // Determinar moneda del financiamiento
                $monedaFinanciamiento = $fila['moneda'] ?: 'S/.';

                // Filtrar por moneda si se especificó
                if ($moneda === 'soles' && $monedaFinanciamiento !== 'S/.') {
                    continue;
                } elseif ($moneda === 'dolares' && $monedaFinanciamiento !== '$') {
                    continue;
                }

                // Obtener nombre del grupo/variante
                $grupoVariante = $this->obtenerNombreGrupoFinanciamiento($fila['id_variante'], $fila['grupo_financiamiento']);

                // Calcular precio unitario
                $cantidad = intval($fila['cantidad_producto']) ?: 1;
                $precioUnitario = $fila['monto_total'] / $cantidad;

                $registros[] = [
                    'categoria' => $fila['categoria'],
                    'producto' => $fila['producto_nombre'],
                    'tipo_venta' => 'Financiamiento',
                    'cantidad' => $cantidad,
                    'precio_unitario' => number_format($precioUnitario, 2),
                    'total_producto' => number_format($fila['monto_total'], 2),
                    'total_producto_raw' => floatval($fila['monto_total']), // Agregar valor sin formato
                    'moneda' => $monedaFinanciamiento,
                    'grupo_variante' => $grupoVariante,
                    'fecha_emision' => $fila['fecha_creacion'],
                    'nombre_vendedor' => $fila['nombre_vendedor'] ?? 'No asignado'
                ];


                // Sumar al total correspondiente
                if ($monedaFinanciamiento === 'S/.') {
                    $totalSoles += $fila['monto_total'];
                } else {
                    $totalDolares += $fila['monto_total'];
                }
            }

        } catch (Exception $e) {
            throw new Exception("Error al obtener financiamientos: " . $e->getMessage());
        }

        return [
            'registros' => $registros,
            'total_soles' => $totalSoles,
            'total_dolares' => $totalDolares
        ];
    }

}
