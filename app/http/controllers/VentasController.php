<?php

require_once "app/models/Venta.php";
require_once "app/models/Cliente.php";
require_once "app/models/DocumentoEmpresa.php";
require_once "app/models/ProductoVenta.php";
require_once "app/models/VentaServicio.php";
require_once "app/models/Varios.php";
require_once "app/models/VentaSunat.php";
require_once "app/models/VentaAnulada.php";
require_once "app/models/GuiaRemision.php";
require_once "app/clases/SendURL.php";
require_once "app/clases/SunatApi.php";
require_once "app/models/Productov2.php";
require_once "app/models/Reportes.php";

class VentasController extends Controller
{
    private $venta;
    private $sunatApi;
    private $conexion;
    private $guia;
    public function __construct()
    {
        $this->venta = new Venta();
        $this->sunatApi = new SunatApi();
        $this->guia = new GuiaRemision();
        $this->conexion = (new Conexion())->getConexion();
    }


    public function ingresosEgresosRender()
    {
        $lista = [];
        $sql = "SELECT
                	ingreso_egreso.*,
                	productos.descripcion,
                	productos.codigo,
                	usuario 
                FROM
                	ingreso_egreso
                	JOIN productos ON ingreso_egreso.id_producto = productos.id_producto
                	INNER JOIN usuarios on usuarios.usuario_id = ingreso_egreso.id_usuario
                ORDER BY
                	intercambio_id ASC";
        $result = $this->conexion->query($sql);
        /*  foreach ($result as $res) {
            $lista[] = $res;
        }
 */
        return $result;
    }


    /*  public function  */
    public function ingresoAlmacen()
    {
        $respuesta['res'] = false;
        $sql = "INSERT INTO ingreso_egreso set id_producto = '{$_POST['productoid']}', tipo = '{$_POST['tipo']}',cantidad = '{$_POST['cantidad']}', id_usuario = '{$_SESSION['usuario_fac']}', almacen_ingreso = '{$_POST['almacen']}'";
        if ($this->conexion->query($sql)) {
            $sql = "update productos set cantidad=cantidad+'{$_POST['cantidad']}' where id_producto= '{$_POST['productoid']}'";
            $this->conexion->query($sql);
            $respuesta['res'] = true;
        }
        echo json_encode($respuesta);
    }
    public function egresoAlmacen()
    {
        $respuesta['res'] = false;
        $sql = "INSERT INTO ingreso_egreso set id_producto = '{$_POST['productoid']}', tipo = '{$_POST['tipo']}',cantidad = '{$_POST['cantidad']}', id_usuario = '{$_SESSION['usuario_fac']}', almacen_ingreso = '{$_POST['alAlmacen']}', almacen_egreso = '{$_POST['almacen']}', estado = 0";
        if ($this->conexion->query($sql)) {
            //$sql="select  * from productos where id_producto= '{$_POST['productoid']}'";
            //$result =  $this->conexion->query($sql)->fetch_assoc();
//
            //$sql="update productos set cantidad=cantidad-'{$_POST['cantidad']}' where id_producto= '{$_POST['productoid']}'";
            //$this->conexion->query($sql);
            //$sql="update productos set cantidad=cantidad+'{$_POST['cantidad']}' where codigo= '{$result['codigo']}' and almacen='{$_POST['alAlmacen']}'";
            //$this->conexion->query($sql);
            $respuesta['res'] = true;
        }
        echo json_encode($respuesta);
    }
    public function envioComunicacionBajaPorEmpresa()
    {
        $listaBoletas = [];
        foreach (json_decode($_POST['boletas'], true) as $bol) {
            $listaBoletas[] = "v.id_venta='$bol'";
        }

        $sql = "select v.id_venta, v.enviado_sunat,vs.nombre_xml from ventas v
        join ventas_sunat vs on v.id_venta = vs.id_venta
        where " . implode(" OR ", $listaBoletas);

        $listaPorEnviar = $this->venta->exeSQL($sql);

        foreach ($listaPorEnviar as $vpr) {
            if ($vpr['enviado_sunat'] == '0') {
                if ($this->sunatApi->envioIndividualDocumentoVPorEmpresa($vpr['nombre_xml'], $_POST['empresa'])) {
                    $sql = "update ventas set enviado_sunat='1' where id_venta='{$vpr['id_venta']}'";
                    $this->venta->exeSQL($sql);
                }
                sleep(2);
            }
        }
        $respuesta = [];
        $respuesta['msg_resumen'] = $this->sunatApi->comunicacionBajaPorEmpresa(
            $listaBoletas,
            $_POST['empresa'],
            $_POST['fecharesumen'],
            $_POST["fechagen"],
            $_POST['correlativo1']
        );

        return json_encode($respuesta);
    }

    public function envioResumenDiarioPorEmpresa()
    {
        $listaBoletas = [];
        foreach (json_decode($_POST['boletas'], true) as $bol) {
            $listaBoletas[] = "v.id_venta='$bol'";
        }
        return json_encode([
            $this->sunatApi->resumenDiarioPorEmpresa(
                $listaBoletas,
                $_POST['empresa'],
                $_POST['fechagen'],
                $_POST['fecharesumen'],
                $_POST['correlativo1']
            ),
            $this->sunatApi->resumenDiarioBajaPorEmpresa(
                $listaBoletas,
                $_POST['empresa'],
                $_POST['fechagen'],
                $_POST['fecharesumen'],
                $_POST['correlativo2']
            )
        ]);
    }

    public function enviarDocumentoSunatPorEmpresa()
    {
        $sql = "select vs.*,v.id_empresa from ventas_sunat vs
        join ventas v on v.id_venta = vs.id_venta
        where vs.id_venta = '{$_POST["cod"]}'";
        $resultado = ["res" => false];
        if ($row = $this->venta->exeSQL($sql)->fetch_assoc()) {
            if ($this->sunatApi->envioIndividualDocumentoVPorEmpresa($row["nombre_xml"], $row['id_empresa'])) {
                $sql = "update ventas set  enviado_sunat='1'
                where id_venta = '{$_POST["cod"]}'";
                $this->venta->exeSQL($sql);
                $resultado['res'] = true;
            } else {
                $resultado['msg'] = $this->sunatApi->getMensaje();
            }
        }
        return json_encode($resultado);
    }

    public function regenerarXML()
    {
        $venta = $_POST["venta"];

        $sql = "SELECT * from ventas where id_venta='$venta'";
        $ventaData = $this->venta->exeSQL($sql)->fetch_assoc();
        $empresa = $this->venta->exeSQL("select * from empresas where id_empresa='{$ventaData['id_empresa']}'")->fetch_assoc();
        $cliente = $this->venta->exeSQL("select * from clientes where id_cliente='{$ventaData['id_cliente']}'")->fetch_assoc();


        $dataSend = [];
        $dataSend["certGlobal"] = false;

        $direccionselk = $cliente["direccion"];



        if (strlen(trim($direccionselk)) == "") {
            $direccionselk = '-';
        }
        if (trim($cliente["datos"]) == "") {
            $cliente["datos"] = '-';
        }

        $dataSend['cliente'] = json_encode([
            'doc_num' => $cliente["documento"],
            'nom_RS' => $cliente["datos"],
            'direccion' => $direccionselk
        ]);
        $dataSend['productos'] = [];
        $dataSend['apli_igv'] = $ventaData['apli_igv'] == 1;
        $dataSend['total'] = $ventaData["total"];
        $dataSend['serie'] = $ventaData["serie"];
        $dataSend['numero'] = $ventaData["numero"];
        $dataSend['fechaE'] = $ventaData["fecha_emision"];
        $dataSend['fechaV'] = $ventaData["fecha_vencimiento"];
        $dataSend['tipo_pago'] = $ventaData["id_tipo_pago"];
        $dataSend['igv_venta'] = $ventaData["igv"];
        $dataSend['dias_pagos'] = [];
        $dataSend['moneda'] = "PEN";

        $sql = "select * from dias_ventas where id_venta='$venta'";
        $cuotasVentas = $this->venta->exeSQL($sql);

        foreach ($cuotasVentas as $cuotas) {
            $dataSend['dias_pagos'][] = [
                "monto" => $cuotas['monto'],
                "fecha" => $cuotas['fecha']
            ];
        }

        $sql = "select pv.*,p.descripcion from productos_ventas pv
        join productos p on p.id_producto = pv.id_producto
        where pv.id_venta='$venta'";
        $listaProductos = $this->venta->exeSQL($sql);
        foreach ($listaProductos as $prod) {
            $dataSend['productos'][] = [
                "precio" => number_format($prod['precio'], 2, ".", ""),
                "cantidad" => number_format($prod['cantidad'], 0),
                "cod_pro" => $prod['id_producto'],
                "cod_sunat" => "",
                "descripcion" => $prod['descripcion']
            ];
        }

        $sql = "select * from ventas_servicios where  id_venta='$venta'";
        $listaProductos = $this->venta->exeSQL($sql);
        foreach ($listaProductos as $prod) {
            $dataSend['productos'][] = [
                "precio" => number_format($prod['monto'], 2, ".", ""),
                "cantidad" => number_format($prod['cantidad'], 0),
                "cod_pro" => $prod['id_item'],
                "cod_sunat" => $prod['codsunat'],
                "descripcion" => $prod['descripcion']
            ];
        }

        $dataSend["endpoints"] = $empresa['modo'];

        $dataSend['empresa'] = json_encode([
            'ruc' => $empresa['ruc'],
            'razon_social' => $empresa['razon_social'],
            'direccion' => $empresa['direccion'],
            'ubigeo' => $empresa['ubigeo'],
            'distrito' => $empresa['distrito'],
            'provincia' => $empresa['provincia'],
            'departamento' => $empresa['departamento'],
            'clave_sol' => $empresa['clave_sol'],
            'usuario_sol' => $empresa['user_sol']
        ]);
        $respuesta = ["res" => false];

        if ($ventaData['id_tido'] == 1 || $ventaData['id_tido'] == 2) {
            $dataSend['dias_pagos'] = json_encode($dataSend['dias_pagos']);

            $dataSend['productos'] = json_encode($dataSend['productos']);
            file_put_contents("Dataaaaaaaaaaaaaaaaaaaa.json", json_encode($dataSend));
            if ($ventaData['id_tido'] == 1) {
                $dataResp = $this->sunatApi->genBoletaXML($dataSend);
            } else {
                $dataResp = $this->sunatApi->genFacturaXML($dataSend);
            }
            if ($dataResp["res"]) {
                $respuesta["res"] = true;
                $sql = "select * from ventas_sunat where id_venta = '$venta'";
                if ($rrroooo = $this->venta->exeSQL($sql)->fetch_assoc()) {
                    $sql = "update ventas_sunat set hash='{$dataResp['data']['hash']}',
                      nombre_xml='{$dataResp['data']['nombre_archivo']}',
                      qr_data='{$dataResp['data']['qr']}' where id_venta = '$venta' ";
                    $this->venta->exeSQL($sql);
                } else {
                    $sql = "insert into ventas_sunat set hash='{$dataResp['data']['hash']}',
                      nombre_xml='{$dataResp['data']['nombre_archivo']}',
                      qr_data='{$dataResp['data']['qr']}',  id_venta = '$venta' ";
                    $this->venta->exeSQL($sql);
                }
            }
        }

        return json_encode($respuesta);
    }

    public function listaVentasPorEmpresa()
    {
        return json_encode($this->venta->verFilasPorEmpresas($_POST["empresa"], $_POST["sucursal"]));
    }


    public function enviarDocumentoSunat()
    {
        $sql = "select * from ventas_sunat where id_venta = '{$_POST["cod"]}'";
        $resultado = ["res" => false];
        if ($row = $this->venta->exeSQL($sql)->fetch_assoc()) {
            if ($this->sunatApi->envioIndividualDocumentoV($row["nombre_xml"])) {
                $sql = "update ventas set  enviado_sunat='1' where id_venta = '{$_POST["cod"]}'";
                $this->venta->exeSQL($sql);
                $resultado['res'] = true;
            } else {
                $resultado['msg'] = $this->sunatApi->getMensaje();
            }
        }
        return json_encode($resultado);
    }

    public function anularVenta()
    {
        $this->venta->setIdVenta($_POST['iventa']);
        $c_anulada = new VentaAnulada();
        $c_producto = new ProductoVenta();

        /*$c_producto->setIdVenta($this->venta->getIdVenta());
        $c_producto->eliminar();*/

        $c_anulada->setIdVenta($this->venta->getIdVenta());
        $c_anulada->setFecha(date("Y-m-d"));
        $c_anulada->setMotivo("-");
        $resultado = ["res" => false];
        if ($this->venta->anular()) {
            $resultado['res'] = true;
            $c_anulada->insertar();


        }
        return json_encode($resultado);
    }
  
    public function listarVentas() {
        try {
            header('Pragma: no-cache');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Content-Type: application/json');
    
            // Verificar sesión
            if (!isset($_SESSION['id_rol']) || !isset($_SESSION['sucursal'])) {
                echo json_encode([
                    "sEcho" => isset($_GET['sEcho']) ? intval($_GET['sEcho']) : 1,
                    "iTotalRecords" => 0,
                    "iTotalDisplayRecords" => 0,
                    "aaData" => []
                ]);
                exit;
            }
    
            $id_rol = intval($_SESSION['id_rol']);
            $sucursal = intval($_SESSION['sucursal']);
            
            // CORREGIDO: Verificar correctamente los parámetros de DataTables
            $searchTerm = isset($_GET['search']['value']) ? $_GET['search']['value'] : ''; 
            
            // CORREGIDO: Usar los nombres de parámetros correctos y verificar ambos formatos
            $start = isset($_GET['start']) ? intval($_GET['start']) : 
                    (isset($_GET['iDisplayStart']) ? intval($_GET['iDisplayStart']) : 0);
            
            $length = isset($_GET['length']) ? intval($_GET['length']) : 
                     (isset($_GET['iDisplayLength']) ? intval($_GET['iDisplayLength']) : 10);
            
            $orderColumn = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 
                          (isset($_GET['iSortCol_0']) ? intval($_GET['iSortCol_0']) : 0);
            
            $orderDir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 
                       (isset($_GET['sSortDir_0']) ? $_GET['sSortDir_0'] : 'DESC');
            
            // CORREGIDO: Añadir depuración para verificar los parámetros recibidos
            error_log("Búsqueda: '$searchTerm', Start: $start, Length: $length, OrderCol: $orderColumn, OrderDir: $orderDir");
            
            // MODIFICADO: Siempre usamos nuestro modelo personalizado
            $ventasModel = new Venta();
            
            // MODIFICADO: Determinar si aplicar filtro de sucursal
            $sucursalFiltro = ($id_rol === 1 || $id_rol === 2 || $id_rol === 3) ? null : $sucursal;
            
            // MODIFICADO: Obtener datos usando el modelo
            $resultado = $ventasModel->buscarVentas($searchTerm, $start, $length, $orderColumn, $orderDir, $sucursalFiltro);
            
            // CORREGIDO: Usar los nombres de parámetros correctos en la respuesta
            $ventas = [
                "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 
                        (isset($_GET['sEcho']) ? intval($_GET['sEcho']) : 1),
                "recordsTotal" => $resultado['recordsTotal'],
                "recordsFiltered" => $resultado['recordsFiltered'],
                "data" => $resultado['data']
            ];
            
            // CORREGIDO: Para compatibilidad con versiones anteriores
            if (isset($_GET['sEcho'])) {
                $ventas["sEcho"] = intval($_GET['sEcho']);
                $ventas["iTotalRecords"] = $resultado['recordsTotal'];
                $ventas["iTotalDisplayRecords"] = $resultado['recordsFiltered'];
                $ventas["aaData"] = $resultado['data'];
            }
            
            echo json_encode($ventas);
            
        } catch (Exception $e) {
            error_log("Error en listarVentas: " . $e->getMessage());
            echo json_encode([
                "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 
                        (isset($_GET['sEcho']) ? intval($_GET['sEcho']) : 1),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "error" => "Error al procesar la solicitud: " . $e->getMessage()
            ]);
        }
        exit;
    }
    
    public function detalleVenta()
    {
        //echo $_POST['iventa'];
        $this->venta->setIdVenta($_POST['iventa']);
        return $this->venta->verDetalle();
    }
    public function tipoVenta()
    {
        //echo $_POST['iventa'];
        $idVenta = $_POST['iventa'];
        $sqlProducto = "SELECT * FROM productos_ventas WHERE id_venta = $idVenta";
        $sqlServicio = "SELECT * FROM ventas_servicios WHERE id_venta = $idVenta";
        $returnFetch = $this->venta->exeSQL($sqlProducto)->fetch_assoc();
        $respuesta['tipo'] = '';
        $respuesta['res'] = false;
        if (empty($returnFetch)) {
            $returnFetchServicios = $this->venta->exeSQL($sqlServicio)->fetch_assoc();
            $respuesta['tipo'] = 'servicio';
            $respuesta['data'] = $returnFetchServicios;
            $respuesta['res'] = true;
            return json_encode($respuesta);
        } else {
            $respuesta['tipo'] = 'productos';
            $respuesta['data'] = $returnFetch;
            $respuesta['res'] = true;
            return json_encode($respuesta);
        }
    }


    public function detalleVenta2()
    {
        //echo $_POST['iventa'];
        $this->venta->setIdVenta($_POST['iventa']);
        return $this->venta->verDetalle2();
    }

    public function editVentaServicio()
    {
        $resultado = ["res" => false];



        $dataSend = [];
        $dataSend["certGlobal"] = false;


        $c_cliente = new Cliente();
        $c_venta = new Venta();
        $c_tido = new DocumentoEmpresa();
        $c_detalle = new ProductoVenta();
        $c_servicio = new VentaServicio();
        // $c_curl = new SendCurlVenta();
        $c_sunat = new VentaSunat();
        $c_varios = new Varios();

        $id_empresa = $_SESSION['id_empresa'];

        $sql = "SELECT * from empresas where id_empresa = " . $id_empresa;

        $respEmpre = $c_venta->exeSQL($sql)->fetch_assoc();

        $igv_empr_sel = $respEmpre['igv'];


        $c_cliente->setIdEmpresa($id_empresa);
        $c_cliente->setDocumento(filter_input(INPUT_POST, 'num_doc'));
        $c_cliente->setDatos(filter_input(INPUT_POST, 'nom_cli'));
        $c_cliente->setDireccion(filter_input(INPUT_POST, 'dir_cli'));
        $c_cliente->setDireccion2(filter_input(INPUT_POST, 'dir2_cli'));

        if ($c_cliente->getDocumento() == "") {
            $numDoc = $_POST['num_doc'] == '' ? '' : $_POST['num_doc'];
            $nombre = $_POST['nom_cli'] == '' ? '' : $_POST['nom_cli'];
            $c_cliente->modificar("SD" . $c_varios->generarCodigo(5), $nombre, $_POST['id_cliente']);
            /*             $c_cliente->setDocumento("SD" . $c_varios->generarCodigo(5));
            $c_cliente->insertar(); */
        } else {
            $numDoc = $_POST['num_doc'] == '' ? '' : $_POST['num_doc'];
            $nombre = $_POST['nom_cli'] == '' ? '' : $_POST['nom_cli'];
            $c_cliente->modificar($numDoc, $nombre, $_POST['id_cliente']);
            /*  $numDoc = $_POST['num_doc'] == '' ? '' : $_POST['num_doc'];
            $nombre = $_POST['nom_cli'] == '' ? '' : $_POST['nom_cli'];
            $c_cliente->modificar($numDoc, $nombre, $_POST['id_cliente']); */
            /*  if (!$c_cliente->verificarDocumento()) {
                $c_cliente->insertar();
            } else {
                $numDoc = $_POST['num_doc'] == '' ? '' : $_POST['num_doc'];
                $nombre = $_POST['nom_cli'] == '' ? '' : $_POST['nom_cli'];
                $c_cliente->modificar($numDoc, $nombre, $_POST['id_cliente']);
            } */
        }
        /*  $numDoc = $_POST['num_doc'] == '' ? '' : $_POST['num_doc'];
        $nombre = $_POST['nom_cli'] == '' ? '' : $_POST['nom_cli'];
        $c_cliente->modificar($numDoc, $nombre, $_POST['id_cliente']); */


        $resultado["email"] = $c_cliente->getEmail() ? $c_cliente->getEmail() : '';
        $resultado["cel"] = $c_cliente->getTelefono() ? $c_cliente->getTelefono() : '';

        $direccionselk = '';
        if ($_POST['dir_pos'] == 1) {
            $direccionselk = $_POST['dir_cli'];
        } elseif ($_POST['dir_pos'] == 2) {
            $direccionselk = $_POST['dir2_cli'];
        }

        if (trim($c_cliente->getDocumento()) == "") {
            $c_cliente->setDocumento('');
        }
        if (strlen(trim($direccionselk)) == "") {
            $direccionselk = '-';
        }
        if (trim($c_cliente->getDatos()) == "") {
            $c_cliente->setDatos('-');
        }

        $dataSend['cliente'] = json_encode([
            'doc_num' => $c_cliente->getDocumento(),
            'nom_RS' => $c_cliente->getDatos(),
            'direccion' => $direccionselk
        ]);
        $c_venta->setDireccion($direccionselk);
        /*   $dataSend['productos'] = []; */

        $c_venta->setApliIgv($_POST['apli_igv']);
        $c_venta->setIdEmpresa($id_empresa);
        $c_venta->setFecha($_POST['fecha']);
        $c_venta->setFechaVenc($_POST['tipo_pago'] == '1' ? $_POST['fecha'] : $_POST['fechaVen']);
        $c_venta->setDiasPagos($_POST['dias_pago']);
        $c_venta->setIdTipoPago($_POST['tipo_pago']);
        $c_venta->setObserva($_POST['observ']);

        $c_venta->setIdCliente($_POST['id_cliente']);
        $c_venta->setIgv($igv_empr_sel);
        $c_venta->setTotal(filter_input(INPUT_POST, 'total'));
        /*     $c_venta->setIdVenta(); */
        $tipoventa = filter_input(INPUT_POST, 'tipoventa');
        /* 

        $dataSend['apli_igv'] = $_POST['apli_igv'] == 1;
        $dataSend['total'] = $c_venta->getTotal();
        $dataSend['serie'] = $c_tido->getSerie();
        $dataSend['numero'] = $c_tido->getNumero();
        $dataSend['fechaE'] = $c_venta->getFecha();
        $dataSend['fechaV'] = $c_venta->getFechaVenc();
        $dataSend['tipo_pago'] = $c_venta->getIdTipoPago();
        $dataSend['igv_venta'] = $igv_empr_sel;
        $dataSend['dias_pagos'] = [];
        $dataSend['moneda'] = "PEN"; */

        $listaPagos = json_decode($_POST['dias_lista'], true);

        if ($c_venta->editar($_POST['idVenta'])) {

            $resultado["res"] = true;
            $array_detalle = json_decode($_POST['listaPro'], true);
            foreach ($listaPagos as $diaP) {
                $sql = "insert into dias_ventas set id_venta='{$c_venta->getIdVenta()}',
                    monto='{$diaP['monto']}',fecha='{$diaP['fecha']}',estado='0'";
                $c_venta->exeSQL($sql);
                /*  $dataSend['dias_pagos'][] = [
                    "monto" => $diaP['monto'],
                    "fecha" => $diaP['fecha']
                ]; */
            }
            /*    $dataSend['dias_pagos'] = json_encode($dataSend['dias_pagos']); */

            $nroitem = 1;


            /*  $c_servicio->setIdventa(); */
            $c_servicio->eliminar($_POST['idVenta']);

            foreach ($array_detalle as $fila) {
                $c_servicio->setDescripcion($fila['descripcion']);
                $c_servicio->setCantidad($fila['cantidad']);
                $c_servicio->setMonto($fila['precioVenta']);
                $c_servicio->setCodsunat(isset($fila['codsunat']) ? $fila['codsunat'] : '');
                $c_servicio->setIditem($nroitem);
                /*  $c_servicio->setIdventa($_POST['idVenta']); */
                $c_servicio->editar($_POST['idVenta']);
                $nroitem++;
                /*     $dataSend['productos'][] = [
                    "precio" => $fila['precio'],
                    "cantidad" => $fila['cantidad'],
                    "cod_pro" => $nroitem,
                    "cod_sunat" => isset($fila['codsunat']) ? $fila['codsunat'] : '',
                    "descripcion" => $fila['descripcion']
                ]; */
            }

            //definir url segun el tipo de documento sunat
            if ($c_venta->getIdTido() == 1) {
                $archivo = "boleta";
            }
            if ($c_venta->getIdTido() == 2) {
                $archivo = "factura";
            }

            /*   if ($c_venta->getIdTido() == 1 || $c_venta->getIdTido() == 2) { */

            /* 
                $dataSend["endpoints"] = $respEmpre['modo'];

                $dataSend['empresa'] = json_encode([
                    'ruc' => $respEmpre['ruc'],
                    'razon_social' => $respEmpre['razon_social'],
                    'direccion' => $respEmpre['direccion'],
                    'ubigeo' => $respEmpre['ubigeo'],
                    'distrito' => $respEmpre['distrito'],
                    'provincia' => $respEmpre['provincia'],
                    'departamento' => $respEmpre['departamento'],
                    'clave_sol' => $respEmpre['clave_sol'],
                    'usuario_sol' => $respEmpre['user_sol']
                ]);



                $dataSend['productos'] = json_encode($dataSend['productos']); */
            /* 
                if ($c_venta->getIdTido() == 1) {
                    $dataResp = $this->sunatApi->genBoletaXML($dataSend);
                } else {
                    $dataResp = $this->sunatApi->genFacturaXML($dataSend);
                }



                if ($dataResp["res"]) {
                    $c_sunat->setIdVenta($c_venta->getIdVenta());
                    $c_sunat->setHash($dataResp['data']['hash']);
                    $c_sunat->setNombreXml($dataResp['data']['nombre_archivo']);
                    $c_sunat->setQrData($dataResp['data']['qr']);
                    $c_sunat->insertar();
                } else {
                } */
            /* } */ /* else {
         $c_sunat->setIdVenta($c_venta->getIdVenta());
         $c_sunat->setHash("-");
         $c_sunat->setNombreXml("-");
         $c_sunat->setQrData('-');
         $c_sunat->insertar();

         $resultado["valor"] = $c_venta->getIdVenta();
     } */
            /*    $resultado["nomFact"] = $c_sunat->getNombreXml() . ".pdf";
            $resultado["urlFact"] = URL::to('/venta/comprobante/pdf/' . $c_sunat->getIdVenta() . '/' . $c_sunat->getNombreXml());
            $resultado["urlFactd"] = URL::to('/venta/comprobante/pdfd/' . $c_sunat->getIdVenta() . '/' . $c_sunat->getNombreXml());
        } */
        }
        /*  $_REQUEST */
        $resultado["nomFact"] = '2020' . ".pdf";
        $resultado["urlFact"] = URL::to('/venta/comprobante/pdf/' . $_POST['idVenta'] . '/' . '2020');
        $resultado["urlFactd"] = URL::to('/venta/comprobante/pdfd/' . $_POST['idVenta'] . '/2020');

        return json_encode($resultado);
    }
    public function editVentaProducto()
    {


        $resultado = ["res" => false];



        $dataSend = [];
        $dataSend["certGlobal"] = false;


        $c_cliente = new Cliente();
        $c_venta = new Venta();
        $c_tido = new DocumentoEmpresa();
        $c_detalle = new ProductoVenta();
        /*  $c_servicio = new VentaServicio(); */
        // $c_curl = new SendCurlVenta();
        $c_sunat = new VentaSunat();
        $c_varios = new Varios();

        $id_empresa = $_SESSION['id_empresa'];

        $sql = "SELECT * from empresas where id_empresa = " . $id_empresa;

        $respEmpre = $c_venta->exeSQL($sql)->fetch_assoc();

        $igv_empr_sel = $respEmpre['igv'];


        $c_cliente->setIdEmpresa($id_empresa);
        $c_cliente->setDocumento(filter_input(INPUT_POST, 'num_doc'));
        $c_cliente->setDatos(filter_input(INPUT_POST, 'nom_cli'));
        $c_cliente->setDireccion(filter_input(INPUT_POST, 'dir_cli'));
        $c_cliente->setDireccion2(filter_input(INPUT_POST, 'dir2_cli'));


        if ($c_cliente->getDocumento() == "") {
            $numDoc = $_POST['num_doc'] == '' ? '' : $_POST['num_doc'];
            $nombre = $_POST['nom_cli'] == '' ? '' : $_POST['nom_cli'];
            $c_cliente->modificar("SD" . $c_varios->generarCodigo(5), $nombre, $_POST['id_cliente']);
            /*             $c_cliente->setDocumento("SD" . $c_varios->generarCodigo(5));
            $c_cliente->insertar(); */
        } else {
            $numDoc = $_POST['num_doc'] == '' ? '' : $_POST['num_doc'];
            $nombre = $_POST['nom_cli'] == '' ? '' : $_POST['nom_cli'];
            $c_cliente->modificar($numDoc, $nombre, $_POST['id_cliente']);
            /*  $numDoc = $_POST['num_doc'] == '' ? '' : $_POST['num_doc'];
            $nombre = $_POST['nom_cli'] == '' ? '' : $_POST['nom_cli'];
            $c_cliente->modificar($numDoc, $nombre, $_POST['id_cliente']); */
            /*  if (!$c_cliente->verificarDocumento()) {
                $c_cliente->insertar();
            } else {
                $numDoc = $_POST['num_doc'] == '' ? '' : $_POST['num_doc'];
                $nombre = $_POST['nom_cli'] == '' ? '' : $_POST['nom_cli'];
                $c_cliente->modificar($numDoc, $nombre, $_POST['id_cliente']);
            } */
        }

        $resultado["email"] = $c_cliente->getEmail() ? $c_cliente->getEmail() : '';
        $resultado["cel"] = $c_cliente->getTelefono() ? $c_cliente->getTelefono() : '';

        $direccionselk = '';
        if ($_POST['dir_pos'] == 1) {
            $direccionselk = $_POST['dir_cli'];
        } elseif ($_POST['dir_pos'] == 2) {
            $direccionselk = $_POST['dir2_cli'];
        }

        if (trim($c_cliente->getDocumento()) == "") {
            $c_cliente->setDocumento('');
        }
        if (strlen(trim($direccionselk)) == "") {
            $direccionselk = '-';
        }
        if (trim($c_cliente->getDatos()) == "") {
            $c_cliente->setDatos('-');
        }

        /*  $dataSend['cliente'] = json_encode([
            'doc_num' => $c_cliente->getDocumento(),
            'nom_RS' => $c_cliente->getDatos(),
            'direccion' => $direccionselk
        ]); */
        $c_venta->setDireccion($direccionselk);
        $c_tido->setIdEmpresa($id_empresa);
        $c_tido->setIdTido(filter_input(INPUT_POST, 'tipo_doc'));
        $c_tido->obtenerDatos();
        $c_venta->setApliIgv($_POST['apli_igv']);
        $c_venta->setIdEmpresa($id_empresa);
        $c_venta->setFecha($_POST['fecha']);
        $c_venta->setFechaVenc($_POST['tipo_pago'] == '1' ? $_POST['fecha'] : $_POST['fechaVen']);
        $c_venta->setDiasPagos($_POST['dias_pago']);
        $c_venta->setIdTipoPago($_POST['tipo_pago']);
        $c_venta->setObserva($_POST['observ']);
        $c_venta->setIdTido($c_tido->getIdTido());
        $c_venta->setSerie($c_tido->getSerie());
        $c_venta->setNumero($c_tido->getNumero());
        $c_venta->setIdCliente($_POST['id_cliente']);
        $c_venta->setIgv($igv_empr_sel);
        $c_venta->setTotal(filter_input(INPUT_POST, 'total'));


        /*      $dataSend['apli_igv'] = $_POST['apli_igv'] == 1;
        $dataSend['total'] = $c_venta->getTotal();
        $dataSend['serie'] = $c_tido->getSerie();
        $dataSend['numero'] = $c_tido->getNumero();
        $dataSend['fechaE'] = $c_venta->getFecha();
        $dataSend['fechaV'] = $c_venta->getFechaVenc();
        $dataSend['tipo_pago'] = $c_venta->getIdTipoPago();
        $dataSend['igv_venta'] = $igv_empr_sel;
        $dataSend['dias_pagos'] = [];
        $dataSend['moneda'] = "PEN"; */

        $listaPagos = json_decode($_POST['dias_lista'], true);

        if ($c_venta->editar($_POST['idVenta'])) {

            $resultado["res"] = true;
            $array_detalle = json_decode($_POST['listaPro'], true);
            foreach ($listaPagos as $diaP) {
                $sql = "insert into dias_ventas set id_venta='{$c_venta->getIdVenta()}',
                    monto='{$diaP['monto']}',fecha='{$diaP['fecha']}',estado='0'";
                $c_venta->exeSQL($sql);
                /*  $dataSend['dias_pagos'][] = [
                    "monto" => $diaP['monto'],
                    "fecha" => $diaP['fecha']
                ]; */
            }
            /*  $dataSend['dias_pago'] = json_encode($dataSend['dias_pagos']); */


            /* $c_detalle->setIdVenta($c_venta->getIdVenta()); */
            $c_detalle->eliminar($_POST['idVenta']);

            /*  $c_servicio->eliminar($_POST['idVenta']);   */

            foreach ($array_detalle as $fila) {
                $c_detalle->setIdProducto($fila['productoid']);
                $c_detalle->setCantidad($fila['cantidad']);
                $c_detalle->setCosto($fila['costo']);
                $c_detalle->setPrecio($fila['precio']);
                $c_detalle->setIdVenta($_POST['idVenta']);
                $c_detalle->setPrecioUsado(isset($fila['precio_usado']) ? $fila['precio_usado'] : 1);
                $c_detalle->insertar();
                /*   $dataSend['productos'][] = [
                    "precio" => $fila['precio'],
                    "cantidad" => $fila['cantidad'],
                    "cod_pro" => $fila['productoid'],
                    "cod_sunat" => "",
                    "descripcion" => $fila['descripcion']
                ]; */
            }


            //definir url segun el tipo de documento sunat
            /*   if ($c_venta->getIdTido() == 1) {
                $archivo = "boleta";
            }
            if ($c_venta->getIdTido() == 2) {
                $archivo = "factura";
            }

            if ($c_venta->getIdTido() == 1 || $c_venta->getIdTido() == 2) {


                $dataSend["endpoints"] = $respEmpre['modo'];

                $dataSend['empresa'] = json_encode([
                    'ruc' => $respEmpre['ruc'],
                    'razon_social' => $respEmpre['razon_social'],
                    'direccion' => $respEmpre['direccion'],
                    'ubigeo' => $respEmpre['ubigeo'],
                    'distrito' => $respEmpre['distrito'],
                    'provincia' => $respEmpre['provincia'],
                    'departamento' => $respEmpre['departamento'],
                    'clave_sol' => $respEmpre['clave_sol'],
                    'usuario_sol' => $respEmpre['user_sol']
                ]);



                $dataSend['productos'] = json_encode($dataSend['productos']);

                if ($c_venta->getIdTido() == 1) {
                    $dataResp = $this->sunatApi->genBoletaXML($dataSend);
                } else {
                    $dataResp = $this->sunatApi->genFacturaXML($dataSend);
                }



                if ($dataResp["res"]) {
                    $c_sunat->setIdVenta($c_venta->getIdVenta());
                    $c_sunat->setHash($dataResp['data']['hash']);
                    $c_sunat->setNombreXml($dataResp['data']['nombre_archivo']);
                    $c_sunat->setQrData($dataResp['data']['qr']);
                    $c_sunat->insertar();
                } else {
                }
            } else {
                $c_sunat->setIdVenta($c_venta->getIdVenta());
                $c_sunat->setHash("-");
                $c_sunat->setNombreXml("-");
                $c_sunat->setQrData('-');
                $c_sunat->insertar();

                $resultado["valor"] = $c_venta->getIdVenta();
            } */
            $resultado["nomFact"] = '2020' . ".pdf";
            $resultado["urlFact"] = URL::to('/venta/comprobante/pdf/' . $_POST['idVenta'] . '/' . '2020');
            $resultado["urlFactd"] = URL::to('/venta/comprobante/pdfd/' . $_POST['idVenta'] . '/2020');
        }

        return json_encode($resultado);
    }

    public function guardarVentas()
    {
        try {
            if (!isset($_SESSION['usuario_fac'])) {
                return json_encode([
                    "res" => false,
                    "mensaje" => "Sesión no iniciada"
                ]);
            }
    
            $resultado = ["res" => false];
    
            // Validar productos
            if (!isset($_POST['listaPro']) || empty($_POST['listaPro'])) {
                return json_encode([
                    "res" => false,
                    "mensaje" => "No hay productos agregados a la lista"
                ]);
            }
    
            // Inicializar componentes
            $c_cliente = new Cliente();
            $c_venta = new Venta();
            $c_tido = new DocumentoEmpresa();
            $c_detalle = new ProductoVenta();
            $c_servicio = new VentaServicio();
            $c_sunat = new VentaSunat();
            $c_varios = new Varios();
            $c_guia = new GuiaRemision();
    
            $id_empresa = $_SESSION['id_empresa'];
    
            // Obtener datos de empresa
            $sql = "SELECT * from empresas where id_empresa = " . $id_empresa;
            $respEmpre = $c_venta->exeSQL($sql)->fetch_assoc();
            $igv_empr_sel = $respEmpre['igv'];
    
            // Configurar cliente
            $c_cliente->setIdEmpresa($id_empresa);
            $c_cliente->setDocumento(filter_input(INPUT_POST, 'num_doc'));
            $c_cliente->setDatos(filter_input(INPUT_POST, 'nom_cli'));
            $c_cliente->setDireccion(filter_input(INPUT_POST, 'dir_cli'));
            $c_cliente->setDireccion2(filter_input(INPUT_POST, 'dir2_cli'));
    
            // Procesar cliente
            if ($c_cliente->getDocumento() == "") {
                $c_cliente->setDocumento("SD" . $c_varios->generarCodigo(5));
                $c_cliente->insertar();
            } else {
                if (!$c_cliente->verificarDocumento()) {
                    $c_cliente->insertar();
                }
            }
    
            $resultado["email"] = $c_cliente->getEmail() ?: '';
            $resultado["cel"] = $c_cliente->getTelefono() ?: '';
    
            // Configurar dirección
            $direccionselk = '';
            if ($_POST['dir_pos'] == 1) {
                $direccionselk = $_POST['dir_cli'];
            } elseif ($_POST['dir_pos'] == 2) {
                $direccionselk = $_POST['dir2_cli'];
            }
    
            if (trim($c_cliente->getDocumento()) == "") {
                $c_cliente->setDocumento('');
            }
            if (strlen(trim($direccionselk)) == "") {
                $direccionselk = '-';
            }
            if (trim($c_cliente->getDatos()) == "") {
                $c_cliente->setDatos('-');
            }
    
            // Configurar documento
            $c_tido->setIdEmpresa($id_empresa);
            $c_tido->setIdTido(filter_input(INPUT_POST, 'tipo_doc'));
            $c_tido->obtenerDatos();
    
            // Configurar venta
            $c_venta->setDireccion($direccionselk);
            $c_venta->setApliIgv($_POST['apli_igv']);
            $c_venta->setIdEmpresa($id_empresa);
            $c_venta->setFecha($_POST['fecha']);
            $c_venta->setFechaVenc($_POST['tipo_pago'] == '1' ? $_POST['fecha'] : $_POST['fechaVen']);
            $c_venta->setDiasPagos($_POST['dias_pago']);
            $c_venta->setIdTipoPago($_POST['tipo_pago']);
            $metodo = intval($_POST['metodo']);
            $c_venta->setMetodo($metodo);
            $c_venta->setObserva($_POST['observ']);
            $c_venta->setIdTido($c_tido->getIdTido());
            $c_venta->setSerie($c_tido->getSerie());
            $c_venta->setNumero($c_tido->getNumero());
            $c_venta->setIdCliente($c_cliente->getIdCliente());
            $c_venta->setIgv($igv_empr_sel);
            $c_venta->setTotal(filter_input(INPUT_POST, 'total'));
            $c_venta->setIdCoti($_POST['idCoti'] ?? null);
            $tipoventa = filter_input(INPUT_POST, 'tipoventa') ?: 1;
    
            // Iniciar transacción
            $this->conexion->begin_transaction();
    
            try {
                // Insertar venta
                if (!$c_venta->insertar()) {
                    throw new Exception("Error al insertar la venta");
                }
    
                // Procesar pagos
                if (isset($_POST["cantidadPagos"]) && intval($_POST["cantidadPagos"]) > 0) {
                    $cantidadPagos = intval($_POST["cantidadPagos"]);
    
                    // Verificar si existe el array de pagos
                    if (isset($_POST["pagos"]) && is_array($_POST["pagos"])) {
                        for ($i = 0; $i < $cantidadPagos; $i++) {
                            // Verificar que exista el pago y tenga método y monto
                            if (
                                isset($_POST["pagos"][$i]) &&
                                isset($_POST["pagos"][$i]["metodoPago"]) &&
                                isset($_POST["pagos"][$i]["montoPago"]) &&
                                !empty($_POST["pagos"][$i]["metodoPago"]) &&
                                !empty($_POST["pagos"][$i]["montoPago"])
                            ) {
    
                                $metodoPago = $this->conexion->real_escape_string($_POST["pagos"][$i]["metodoPago"]);
                                $montoPago = floatval($_POST["pagos"][$i]["montoPago"]);
                                $npago = $i + 1;
    
                                $sql = "INSERT INTO ventas_pagos SET id_venta='{$c_venta->getIdVenta()}',
                                        metodo_pago='{$metodoPago}', monto='{$montoPago}', npago='{$npago}'";
    
                                if (!$c_venta->exeSQL($sql)) {
                                    throw new Exception("Error al registrar pago #{$npago}");
                                }
                            }
                        }
                    } else {
                        // Si no hay array de pagos pero se especificó cantidad, usar método principal
                        $sql = "INSERT INTO ventas_pagos SET id_venta='{$c_venta->getIdVenta()}',
                                metodo_pago='{$metodo}', monto='{$c_venta->getTotal()}', npago='1'";
    
                        if (!$c_venta->exeSQL($sql)) {
                            throw new Exception("Error al registrar pago #1");
                        }
                    }
                } else {
                    // Si no se especificó cantidad de pagos, usar método principal
                    $sql = "INSERT INTO ventas_pagos SET id_venta='{$c_venta->getIdVenta()}',
                            metodo_pago='{$metodo}', monto='{$c_venta->getTotal()}', npago='1'";
    
                    if (!$c_venta->exeSQL($sql)) {
                        throw new Exception("Error al registrar pago #1");
                    }
                }
    
                // Procesar días de pago
                $listaPagos = [];
                if (isset($_POST['dias_lista']) && !empty($_POST['dias_lista'])) {
                    $listaPagos = json_decode($_POST['dias_lista'], true);
                    if (is_array($listaPagos)) {
                        foreach ($listaPagos as $diaP) {
                            if (isset($diaP['monto']) && isset($diaP['fecha'])) {
                                $sql = "INSERT INTO dias_ventas SET id_venta='{$c_venta->getIdVenta()}',
                                        monto='{$diaP['monto']}',fecha='{$diaP['fecha']}',estado='0'";
                                if (!$c_venta->exeSQL($sql)) {
                                    throw new Exception("Error al registrar día de pago");
                                }
                            }
                        }
                    }
                }
    
                // Procesar productos
                $array_detalle = json_decode($_POST['listaPro'], true);
    
                if ($tipoventa == 1) {
                    // Venta de productos
                    foreach ($array_detalle as $fila) {
                        
                        // Buscar siempre por código
                        $codigo = $this->conexion->real_escape_string($fila['productoid']);
                        $sql = "SELECT idproductosv2, cantidad FROM productosv2 WHERE codigo = '{$codigo}' OR codigo_barra = '{$codigo}'";

                        
                        $result = $c_venta->exeSQL($sql);
                        
                        if ($result && $result->num_rows > 0) {
                            $stockData = $result->fetch_assoc();
                            
                            // Obtener el ID numérico real del producto
                            $idProductoReal = $stockData['idproductosv2'];
                            
                            if ($stockData['cantidad'] < $fila['cantidad']) {
                                throw new Exception("Stock insuficiente para el producto: {$fila['descripcion']}");
                            }
                            
                            // Insertar detalle de venta con el ID numérico real
                            $c_detalle->setIdVenta($c_venta->getIdVenta());
                            $c_detalle->setIdProducto($idProductoReal); // Usar el ID numérico real
                            $c_detalle->setCantidad($fila['cantidad']);
                            $c_detalle->setCosto(isset($fila['costo']) ? $fila['costo'] : 0);
                            $c_detalle->setDescripcion($fila['descripcion']);

                           
                            // Calcular precio según moneda
                            $precio = isset($fila['precioVenta']) ? $fila['precioVenta'] : 0;

                            // Verificar si el tipo de pago es 3 (gratis), si es así, poner el precio a 0
                            if ($c_venta->getIdTipoPago() == 3) { // Modificado: Comprobación del tipo de pago 3 (Gratis)
                                $precio = 0; // Modificado: Asignar precio 0 si es tipo de pago 3 (Gratis)
                            }
                         
                            if (isset($_POST['moneda']) && $_POST['moneda'] == 2 && isset($_POST['tc']) && !empty($_POST['tc'])) {
                                $precio = $precio / floatval($_POST['tc']);
                            }
                            $c_detalle->setPrecio($precio);
                            
                            $c_detalle->setPrecioUsado(isset($fila['precio_usado']) ? $fila['precio_usado'] : '1');
                
                            if (!$c_detalle->insertar()) {
                                throw new Exception("Error al insertar detalle de producto: " . $c_detalle->getSqlError());
                            }

                            // Obtener el nombre del producto desde Productov2.php
                            $c_producto = new Productov2(); // Modificado: Instancia del modelo Productov2
                            $productoInfo = $c_producto->obtenerProductoPorId($idProductoReal); // Modificado: Obtener datos del producto
                            
                            if (!$productoInfo) {
                                throw new Exception("No se pudo obtener la información del producto con ID: {$idProductoReal}");
                            }

                            // Insertar movimiento en Reportes.php
                            $c_reporte = new Reportes(); // Modificado: Instancia del modelo Reportes
                            
                            // Obtener usuario_id de la sesión
                            $user_id = $_SESSION['usuario_id'] ?? null;
                            if (!$user_id) {
                                echo json_encode(['status' => 'error', 'message' => 'No se pudo obtener el ID del usuario.']);
                                return;
                            }

                            $c_reporte->RegistrarMovimiento(
                                $user_id, // Usuario desde la sesión
                                $idProductoReal, // ID del producto
                                $codigo, // Código del producto
                                $productoInfo['NOMBRE'], // Nombre del producto
                                "Salida", // Tipo de movimiento "Salida"
                                "Venta", // Subtipo de movimiento "Venta"
                                $fila['cantidad'], // Cantidad vendida
                                $productoInfo['RAZON_SOCIAL'] // Razon social del proveedor
                            );
                
                            // ELIMINADO: No actualizar stock aquí, ya se actualiza en ProductoVenta::insertar()
                        } else {
                            // Si no se encuentra el producto, intentar buscar por otros campos
                            $codigo = $this->conexion->real_escape_string($fila['productoid']);
                            $sql = "SELECT idproductosv2, cantidad FROM productosv2 WHERE 
                                   codigo = '{$codigo}' OR 
                                   codigo_barra = '{$codigo}' OR 
                                   nombre LIKE '%{$codigo}%'";
                            
                            $result = $c_venta->exeSQL($sql);
                            
                            if ($result && $result->num_rows > 0) {
                                $stockData = $result->fetch_assoc();
                                
                                // Continuar con el mismo proceso que arriba...
                                $idProductoReal = $stockData['idproductosv2'];
                                
                                if ($stockData['cantidad'] < $fila['cantidad']) {
                                    throw new Exception("Stock insuficiente para el producto: {$fila['descripcion']}");
                                }
                                
                                // Insertar detalle de venta con el ID numérico real
                                $c_detalle->setDescripcion($fila['descripcion']);
                                $c_detalle->setIdVenta($c_venta->getIdVenta());
                                $c_detalle->setIdProducto($idProductoReal);
                                $c_detalle->setCantidad($fila['cantidad']);
                                $c_detalle->setCosto(isset($fila['costo']) ? $fila['costo'] : 0);
                                
                                
                                $precio = isset($fila['precioVenta']) ? $fila['precioVenta'] : 0;
                                if (isset($_POST['moneda']) && $_POST['moneda'] == 2 && isset($_POST['tc']) && !empty($_POST['tc'])) {
                                    $precio = $precio / floatval($_POST['tc']);
                                }
                                $c_detalle->setPrecio($precio);
                                
                                $c_detalle->setPrecioUsado(isset($fila['precio_usado']) ? $fila['precio_usado'] : '1');
                
                                if (!$c_detalle->insertar()) {
                                    throw new Exception("Error al insertar detalle de producto: " . $c_detalle->getSqlError());
                                }
                
                                // ELIMINADO: No actualizar stock aquí, ya se actualiza en ProductoVenta::insertar()
                            } else {
                                throw new Exception("No se encontró el producto con ID: {$fila['productoid']}");
                            }
                        }
                    }
                
                } elseif ($tipoventa == 2) {
                    // Venta de servicios
                    $nroitem = 1;
                    foreach ($array_detalle as $fila) {
                        $c_servicio->setIdventa($c_venta->getIdVenta());
                        $c_servicio->setDescripcion($fila['descripcion']);
                        $c_servicio->setCantidad($fila['cantidad']);
                        $c_servicio->setMonto($fila['precioVenta']);
                        $c_servicio->setCodsunat(isset($fila['codsunat']) ? $fila['codsunat'] : '');
                        $c_servicio->setIditem($nroitem);
    
                        if (!$c_servicio->insertar()) {
                            throw new Exception("Error al insertar servicio");
                        }
                        $nroitem++;
                    }
                }
                // MODIFICADO: Añadida condición para omitir generación SUNAT cuando tipo_pago es 3 (Gratis)
                // Procesar documentos SUNAT si aplica
                if (($c_venta->getIdTido() == 1 || $c_venta->getIdTido() == 2) && $c_venta->getIdTipoPago() != 3) {
                    $dataSend = [];
                    $dataSend["certGlobal"] = false;
                
                    // Configurar datos del cliente
                    if (strlen(trim($direccionselk)) == "") {
                        $direccionselk = '-';
                    }
                    if (trim($c_cliente->getDatos()) == "") {
                        $c_cliente->setDatos('-');
                    }
                
                    $dataSend['cliente'] = json_encode([
                        'doc_num' => $c_cliente->getDocumento(),
                        'nom_RS' => $c_cliente->getDatos(),
                        'direccion' => $direccionselk
                    ]);
                
                    // Configurar datos de la venta
                    $dataSend['apli_igv'] = $_POST['apli_igv'] == 1;
                    $dataSend['total'] = number_format($c_venta->getTotal(), 2, '.', '');
                    $dataSend['serie'] = $c_venta->getSerie();
                    $dataSend['numero'] = $c_venta->getNumero();
                    $dataSend['fechaE'] = $c_venta->getFecha();
                    $dataSend['fechaV'] = $c_venta->getFechaVenc();
                    $dataSend['tipo_pago'] = $c_venta->getIdTipoPago();
                    $dataSend['igv_venta'] = $igv_empr_sel;
                    $dataSend['moneda'] = isset($_POST['moneda']) && $_POST['moneda'] == 2 ? "USD" : "PEN";
                    
                    // IMPORTANTE: Inicializar dias_pagos como array vacío para evitar el error
                    $dataSend['dias_pagos'] = [];
                    
                    // Agregar días de pago si existen
                    if (!empty($listaPagos) && is_array($listaPagos)) {
                        foreach ($listaPagos as $diaP) {
                            if (isset($diaP['monto']) && isset($diaP['fecha'])) {
                                $dataSend['dias_pagos'][] = [
                                    "monto" => $diaP['monto'],
                                    "fecha" => $diaP['fecha']
                                ];
                            }
                        }
                    }
                    
                    // Convertir a JSON (incluso si está vacío)
                    $dataSend['dias_pagos'] = json_encode($dataSend['dias_pagos']);
                    
                    // Configurar productos
                    $dataSend['productos'] = [];
                    if ($tipoventa == 1) {
                        foreach ($array_detalle as $fila) {
                           
                            $dataSend['productos'][] = [
                                "precio" => number_format($fila['precioVenta'], 2, ".", ""),
                                "cantidad" => number_format($fila['cantidad'], 0),
                                "cod_pro" => $fila['productoid'],
                                "cod_sunat" => "",
                                "descripcion" => $fila['descripcion']
                            ];
                        }
                    } else {
                        foreach ($array_detalle as $fila) {
                           
                            $dataSend['productos'][] = [
                                "precio" => number_format($fila['precioVenta'], 2, ".", ""),
                                "cantidad" => number_format($fila['cantidad'], 0),
                                "cod_pro" => isset($fila['id_item']) ? $fila['id_item'] : $nroitem,
                                "cod_sunat" => isset($fila['codsunat']) ? $fila['codsunat'] : '',
                                "descripcion" => $fila['descripcion']
                            ];
                        }
                    }
                
                    // Configurar datos de la empresa
                    $dataSend["endpoints"] = $respEmpre['modo'];
                    
                    // Usar datos de sucursal si no es la principal
                    if (isset($_SESSION['sucursal']) && $_SESSION['sucursal'] != '1') {
                        $datoSucursal = $this->conexion->query("SELECT * FROM sucursales WHERE cod_sucursal ='{$_SESSION['sucursal']}' AND empresa_id=" . $_SESSION['id_empresa'])->fetch_assoc();
                        $dataSend['empresa'] = json_encode([
                            'ruc' => $respEmpre['ruc'],
                            'razon_social' => $respEmpre['razon_social'],
                            'direccion' => $datoSucursal['direccion'],
                            'ubigeo' => $datoSucursal['ubigeo'],
                            'distrito' => $datoSucursal['distrito'],
                            'provincia' => $datoSucursal['provincia'],
                            'departamento' => $datoSucursal['departamento'],
                            'clave_sol' => $respEmpre['clave_sol'],
                            'usuario_sol' => $respEmpre['user_sol']
                        ]);
                    } else {
                        $dataSend['empresa'] = json_encode([
                            'ruc' => $respEmpre['ruc'],
                            'razon_social' => $respEmpre['razon_social'],
                            'direccion' => $respEmpre['direccion'],
                            'ubigeo' => $respEmpre['ubigeo'],
                            'distrito' => $respEmpre['distrito'],
                            'provincia' => $respEmpre['provincia'],
                            'departamento' => $respEmpre['departamento'],
                            'clave_sol' => $respEmpre['clave_sol'],
                            'usuario_sol' => $respEmpre['user_sol']
                        ]);
                    }
                
                    $dataSend['productos'] = json_encode($dataSend['productos']);
                    
                    // Generar XML según tipo de documento
                    if ($c_venta->getIdTido() == 1) {
                        $dataResp = $this->sunatApi->genBoletaXML($dataSend);
                    } else {
                        $dataResp = $this->sunatApi->genFacturaXML($dataSend);
                    }
                
                    if ($dataResp["res"]) {
                        $c_sunat->setIdVenta($c_venta->getIdVenta());
                        $c_sunat->setHash($dataResp['data']['hash']);
                        $c_sunat->setNombreXml($dataResp['data']['nombre_archivo']);
                        $c_sunat->setQrData($dataResp['data']['qr']);
                        
                        if (!$c_sunat->insertar()) {
                            throw new Exception("Error al guardar datos de SUNAT: " . $c_sunat->getSqlError());
                        }
                        
                        $nom_xmlFac = $dataResp['data']['nombre_archivo'];
                    } else {
                        throw new Exception("Error al generar XML: " . ($dataResp['mensaje'] ?? 'Error desconocido'));
                    }
                } else {
                    // Para documentos que no son SUNAT
                    $c_sunat->setIdVenta($c_venta->getIdVenta());
                    $c_sunat->setHash("-");
                    $c_sunat->setNombreXml("-");
                    $c_sunat->setQrData('-');
                    if (!$c_sunat->insertar()) {
                        throw new Exception("Error al guardar datos de documento: " . $c_sunat->getSqlError());
                    }
                    
                    $nom_xmlFac = "-";
                }
    
                $this->conexion->commit();
                $resultado["res"] = true;
                $resultado["venta"] = $c_venta->getIdVenta();
                $resultado["nomxml"] = $nom_xmlFac;
    
            } catch (Exception $e) {
                $this->conexion->rollback();
                error_log("Error en guardarVentas: " . $e->getMessage());
                return json_encode([
                    "res" => false,
                    "mensaje" => $e->getMessage()
                ]);
            }
    
            return json_encode($resultado);
    
        } catch (Exception $e) {
            error_log("Error general en guardarVentas: " . $e->getMessage());
            return json_encode([
                "res" => false,
                "mensaje" => "Error interno del servidor"
            ]);
        }
    }
    function buscarProductoController()
    {
        try {
            // Verificar si se recibió el término de búsqueda
            if (!isset($_GET['searchTerm']) || empty(trim($_GET['searchTerm']))) {
                echo json_encode(["error" => "No se proporcionó un término de búsqueda."]);
                return;
            }

            $searchTerm = trim($_GET['searchTerm']); // Obtener el término de búsqueda

            // Instanciar el modelo
            $productoModel = new Productov2();

            // Llamar al método del modelo
            $productos = $productoModel->buscarProductosPorNombreOCodigo($searchTerm);

            // Devolver la respuesta en JSON
            echo json_encode(["success" => true, "productos" => $productos]);
        } catch (Exception $e) {
            // Manejo de errores
            echo json_encode(["error" => "Ocurrió un error al buscar productos."]);
        }
    }

}
