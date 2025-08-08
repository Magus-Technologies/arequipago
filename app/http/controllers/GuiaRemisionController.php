<?php
require_once "app/models/GuiaRemision.php";
require_once "app/models/GuiaDetalle.php";
require_once "app/models/DocumentoEmpresa.php";
require_once "app/models/GuiaSunat.php";
require_once "app/clases/SendURL.php";
require_once "app/clases/SunatApi.php";
require_once "app/clases/SunatApi2.php";

class GuiaRemisionController extends Controller
{
    private $sunatApi;
    private $sunatApi2;
    private $conexion;
    public function __construct()
    {
        $this->sunatApi2 = new SunatApi2();
        $this->sunatApi = new SunatApi();
        $this->conexion = (new Conexion())->getConexion();
    }

    public function enviarDocumentoSunat()
    {
        $conexion = (new Conexion())->getConexion();
        $sql = "select * from guia_sunat where id_guia = '{$_POST['cod']}'";
        $dataGuia = $conexion->query($sql)->fetch_assoc();
        $resultado = ["res" => false];
        if ($this->sunatApi2->envioIndividualGuiaRemi($dataGuia['nombre_xml'])) {
            $sql = "update guia_remision set  enviado_sunat='1' where id_guia_remision= '{$_POST["cod"]}'";
            $conexion->query($sql);
            $resultado['res'] = true;
        } else {
            //echo "Error1";
            $resultado['msg'] = $this->sunatApi2->getMensaje();
        }
        return json_encode($resultado);
    }

    public function insertar()
    {
        $c_guia = new GuiaRemision();
        $c_documentos = new DocumentoEmpresa();
        $guiaDetalle = new GuiaDetalle();
        $guiaSunat = new GuiaSunat();
        $sendURL = new SendURL();

        $dataSend = [];
        $dataSend["certGlobal"] = false;


        //$sendGuia = new SendCurlGuia();
        /*   $data = $_POST['data'];
        $datosGuiaRemosion = json_decode($data['datosGuiaRemosion'], true);
        return json_encode($datosGuiaRemosion);

        return; */

        $c_guia->setFecha(filter_input(INPUT_POST, 'fecha_emision'));
        $c_guia->setIdVenta(filter_input(INPUT_POST, 'venta'));
        $c_guia->setDirLlegada(filter_input(INPUT_POST, 'dir_cli'));
        $c_guia->setUbigeo(filter_input(INPUT_POST, 'ubigeo'));
        $c_guia->setTipoTransporte(filter_input(INPUT_POST, 'tipo_trans'));
        $c_guia->setRucTransporte(filter_input(INPUT_POST, 'ruc'));
        $c_guia->setRazTransporte(filter_input(INPUT_POST, 'razon_social'));
        $c_guia->setVehiculo(filter_input(INPUT_POST, 'veiculo'));
        $c_guia->setChofer(filter_input(INPUT_POST, 'chofer_dni'));


        $c_guia->setPeso(filter_input(INPUT_POST, 'peso'));
        $c_guia->setNroBultos(filter_input(INPUT_POST, 'num_bultos'));

        $c_guia->setIdEmpresa($_SESSION['id_empresa']);

        $c_documentos->setIdTido(11);
        $c_documentos->setIdEmpresa($c_guia->getIdEmpresa());
        $c_documentos->obtenerDatos();

        $c_guia->setSerie($c_documentos->getSerie());
        $c_guia->setNumero($c_documentos->getNumero());

        $dataSend['peso'] = $c_guia->getPeso();
        $dataSend['ubigeo'] = $c_guia->getUbigeo();
        $dataSend['direccion'] = $c_guia->getDirLlegada();
        $dataSend['serie'] = $c_guia->getSerie();
        $dataSend['numero'] = $c_guia->getNumero();
        $dataSend['fecha'] = $c_guia->getFecha();

        // $c_guia->obtenerId();
        $resultado = ["res" => false];
        if ($c_guia->insertar()) {
            //echo "xsssss";

            $resultado["res"] = true;
            $resultado["guia"] = $c_guia->getIdGuia();
            $listaProd = json_decode($_POST['productos'], true);
            $guiaDetalle->setIdGuia($c_guia->getIdGuia());

            $dataSend['productos'] = [];
            foreach ($listaProd as $prodG) {
                $guiaDetalle->setCantidad($prodG['cantidad']);
                $guiaDetalle->setDetalles($prodG['descripcion']);
                $guiaDetalle->setIdProducto($prodG['idproducto']);
                $guiaDetalle->setPrecio($prodG['precio']);
                $guiaDetalle->setUnidad("NIU");
                $guiaDetalle->insertar();
                $dataSend['productos'][] = [
                    'cantidad' => $prodG['cantidad'],
                    'cod_pro' => $prodG['idproducto'],
                    'cod_sunat' => "000",
                    'descripcion' => $prodG['descripcion']
                ];
            }

            $dataSend['productos'] = json_encode($dataSend['productos']);

            $sql = "SELECT * from empresas where id_empresa = " . $_SESSION['id_empresa'];
            $respEmpre = $c_guia->exeSQL($sql)->fetch_assoc();

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

            $dataSend['venta'] = json_encode([
                'serie' => filter_input(INPUT_POST, 'serie'),
                'numero' => filter_input(INPUT_POST, 'numero')
            ]);
            $dataSend['cliente'] = json_encode([
                'doc_num' => filter_input(INPUT_POST, 'doc_cli'),
                'nom_RS' => filter_input(INPUT_POST, 'nom_cli')
            ]);
            $dataSend['transporte'] = json_encode([
                'ruc' => filter_input(INPUT_POST, 'ruc'),
                'razon_social' => filter_input(INPUT_POST, 'razon_social'),
                'placa' => filter_input(INPUT_POST, 'veiculo'),
                'doc_chofer' => filter_input(INPUT_POST, 'chofer_dni')
            ]);

            $dataResp = $this->sunatApi->genGuiaRemision($dataSend);

            /*$respCURL =SendURL::SendGuiaRemision($dataSend);
            $respCURL = json_decode($respCURL,true);
            $dataResp= $respCURL["data"];

            $rutaFileXML="file/xml/".$respEmpre['ruc'];
            if (!file_exists($rutaFileXML)){
                mkdir($rutaFileXML, 0777, true);
            }

            $myfile = fopen($rutaFileXML.'/'.$dataResp['nombre_archivo'].".xml", "w");
            fwrite($myfile,$dataResp['consten_XML']);
            fclose($myfile);*/

            if ($dataResp["res"]) {
                $guiaSunat->setIdGuia($c_guia->getIdGuia());
                $guiaSunat->setHash($dataResp["data"]['hash']);
                $guiaSunat->setNombreXml($dataResp["data"]['nombre_archivo']);
                $guiaSunat->setQrData($dataResp["data"]['qr']);
                $guiaSunat->insertar();
            }
        }
        return json_encode($resultado);
    }
    public function insertar2()
    {
        $c_guia = new GuiaRemision();
        $c_documentos = new DocumentoEmpresa();
        $guiaDetalle = new GuiaDetalle();
        $guiaSunat = new GuiaSunat();
        $sendURL = new SendURL();

        $dataSend = [];
        $dataSend["certGlobal"] = false;


        //$sendGuia = new SendCurlGuia();
        /* return json_encode($_POST['idVenta']);
        return; */
        $data = $_POST['data'];
        $datosGuiaRemosion = json_decode($data['datosGuiaRemosion'], true);
        $datosTransporteGuiaRemosion = json_decode($data['datosTransporteGuiaRemosion'], true);
        $sql = "SELECT * FROM ventas WHERE id_venta = '{$_POST['data']['idVenta']}'";
        $result = $this->conexion->query($sql)->fetch_assoc();

        /*  return json_encode($result['id_venta']);
        return; */
        /*      return json_encode($data);
        return; */
        /*   $c_guia->setFecha(filter_input(INPUT_POST, 'fecha_emision')); 
        $c_guia->setIdVenta(filter_input(INPUT_POST, 'venta'));
           $c_guia->setDirLlegada(filter_input(INPUT_POST, 'dir_cli'));
             $c_guia->setUbigeo(filter_input(INPUT_POST, 'ubigeo'));
              $c_guia->setTipoTransporte(filter_input(INPUT_POST, 'tipo_trans'));
               $c_guia->setRucTransporte(filter_input(INPUT_POST, 'ruc'));
      */
        $c_guia->setFecha($datosGuiaRemosion['fecha_emision']);
        $c_guia->setIdVenta($result['id_venta']);
        $c_guia->setDirLlegada($datosGuiaRemosion['dir_cli']);
        $c_guia->setUbigeo($data['datosUbigeoGuiaRemosion']);
        $c_guia->setTipoTransporte($datosTransporteGuiaRemosion['tipo_trans']);
        $c_guia->setRucTransporte($datosTransporteGuiaRemosion['ruc']);
        $c_guia->setRazTransporte($datosTransporteGuiaRemosion['razon_social']);
        $c_guia->setVehiculo($datosTransporteGuiaRemosion['veiculo']);
        $c_guia->setChofer($datosTransporteGuiaRemosion['chofer_dni']);


        $c_guia->setPeso($datosGuiaRemosion['peso']);
        $c_guia->setNroBultos($datosGuiaRemosion['num_bultos']);

        $c_guia->setIdEmpresa($_SESSION['id_empresa']);

        $c_documentos->setIdTido(11);
        $c_documentos->setIdEmpresa($c_guia->getIdEmpresa());
        $c_documentos->obtenerDatos();

        $c_guia->setSerie($c_documentos->getSerie());
        $c_guia->setNumero($c_documentos->getNumero());

        $dataSend['peso'] = $c_guia->getPeso();
        $dataSend['ubigeo'] = $c_guia->getUbigeo();
        $dataSend['direccion'] = $c_guia->getDirLlegada();
        $dataSend['serie'] = $c_guia->getSerie();
        $dataSend['numero'] = $c_guia->getNumero();
        $dataSend['fecha'] = $c_guia->getFecha();

        // $c_guia->obtenerId();
        $resultado = ["res" => false];
        if ($c_guia->insertar()) {
            //echo "xsssss";

            $resultado["res"] = true;
            $resultado["guia"] = $c_guia->getIdGuia();
            $listaProd = json_decode($data['listaPro'], true);
            $guiaDetalle->setIdGuia($c_guia->getIdGuia());

            $dataSend['productos'] = [];
            foreach ($listaProd as $prodG) {
                $guiaDetalle->setCantidad($prodG['cantidad']);
                $guiaDetalle->setDetalles($prodG['descripcion']);
                $guiaDetalle->setIdProducto($prodG['productoid']);
                $guiaDetalle->setPrecio($prodG['precio']);
                $guiaDetalle->setUnidad("NIU");
                $guiaDetalle->insertar();
                $dataSend['productos'][] = [
                    'cantidad' => $prodG['cantidad'],
                    'cod_pro' => $prodG['productoid'],
                    'cod_sunat' => "000",
                    'descripcion' => $prodG['descripcion']
                ];
            }

            $dataSend['productos'] = json_encode($dataSend['productos']);

            $sql = "SELECT * from empresas where id_empresa = " . $_SESSION['id_empresa'];
            $respEmpre = $c_guia->exeSQL($sql)->fetch_assoc();

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

            $dataSend['venta'] = json_encode([
                'serie' => $result['serie'],
                'numero' => $result['numero']
            ]);
            $dataSend['cliente'] = json_encode([
                'doc_num' => $data['num_doc'],
                'nom_RS' => $data['nom_cli']
            ]);
            $dataSend['transporte'] = json_encode([
                'ruc' => $datosTransporteGuiaRemosion['ruc'],
                'razon_social' => $datosTransporteGuiaRemosion['razon_social'],
                'placa' => $datosTransporteGuiaRemosion['veiculo'],
                'doc_chofer' => $datosTransporteGuiaRemosion['chofer_dni']
            ]);

            $dataResp = $this->sunatApi->genGuiaRemision($dataSend);

            /*$respCURL =SendURL::SendGuiaRemision($dataSend);
            $respCURL = json_decode($respCURL,true);
            $dataResp= $respCURL["data"];

            $rutaFileXML="file/xml/".$respEmpre['ruc'];
            if (!file_exists($rutaFileXML)){
                mkdir($rutaFileXML, 0777, true);
            }

            $myfile = fopen($rutaFileXML.'/'.$dataResp['nombre_archivo'].".xml", "w");
            fwrite($myfile,$dataResp['consten_XML']);
            fclose($myfile);*/

            if ($dataResp["res"]) {
                $guiaSunat->setIdGuia($c_guia->getIdGuia());
                $guiaSunat->setHash($dataResp["data"]['hash']);
                $guiaSunat->setNombreXml($dataResp["data"]['nombre_archivo']);
                $guiaSunat->setQrData($dataResp["data"]['qr']);
                $guiaSunat->insertar();
            }
        }
        return json_encode($resultado);
    }

        // === GESTIÓN DE MOTIVOS ===
    public function obtenerMotivos()
    {
        $model = new GuiaRemision();
        $motivos = $model->obtenerTodosMotivos();
        
        header('Content-Type: application/json');
        echo json_encode($motivos);
    }

    // Después de la función crearMotivo(), agregar:
    public function eliminarMotivo()
    {
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            return;
        }

        $model = new GuiaRemision();
        $resultado = $model->eliminarMotivo($id);
        
        echo json_encode(['success' => $resultado]);
    }
    public function crearMotivo()
    {
        $nombre = trim($_POST['nombre'] ?? '');
        
        if (empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'El nombre es requerido']);
            return;
        }

        $model = new GuiaRemision();
        $nuevoMotivo = $model->crearMotivo($nombre);
        
        echo json_encode(['success' => true, 'data' => $nuevoMotivo]);
    }

    // === GESTIÓN DE CHOFERES ===
    public function obtenerChoferes()
    {
        $model = new GuiaRemision();
        $choferes = $model->obtenerTodosChoferes();
        
        header('Content-Type: application/json');
        echo json_encode($choferes);
    }

    public function crearChofer()
    {
        $nombre = trim($_POST['nombre'] ?? '');
        
        if (empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'El nombre es requerido']);
            return;
        }

        $model = new GuiaRemision();
        $nuevoChofer = $model->crearChofer($nombre);
        
        echo json_encode(['success' => true, 'data' => $nuevoChofer]);
    }

    // Después de la función crearChofer(), agregar:
    public function eliminarChofer()
    {
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            return;
        }

        $model = new GuiaRemision();
        $resultado = $model->eliminarChofer($id);
        
        echo json_encode(['success' => $resultado]);
    }

    // === GESTIÓN DE VEHÍCULOS ===
    public function obtenerVehiculos()
    {
        $model = new GuiaRemision();
        $vehiculos = $model->obtenerTodosVehicle();
        
        header('Content-Type: application/json');
        echo json_encode($vehiculos);
    }

    // Después de la función crearVehiculo(), agregar:
    public function eliminarVehiculo()
    {
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            return;
        }

        $model = new GuiaRemision();
        $resultado = $model->eliminarVehiculo($id);
        
        echo json_encode(['success' => $resultado]);
    }

    public function crearVehiculo()
    {
        $placa = trim($_POST['placa'] ?? '');
        
        if (empty($placa)) {
            echo json_encode(['success' => false, 'message' => 'La placa es requerida']);
            return;
        }

        $model = new GuiaRemision();
        $nuevoVehiculo = $model->crearVehicle($placa);
        
        echo json_encode(['success' => true, 'data' => $nuevoVehiculo]);
    }

    // === GESTIÓN DE LICENCIAS ===
    public function obtenerLicencias()
    {
        $model = new GuiaRemision();
        $licencias = $model->obtenerTodosLicencias();
        
        header('Content-Type: application/json');
        echo json_encode($licencias);
    }

    public function crearLicencia()
    {
        $numero = trim($_POST['numero'] ?? '');
        
        if (empty($numero)) {
            echo json_encode(['success' => false, 'message' => 'El número es requerido']);
            return;
        }

        $model = new GuiaRemision();
        $nuevaLicencia = $model->crearLicencia($numero);
        
        echo json_encode(['success' => true, 'data' => $nuevaLicencia]);
    }

    // Después de la función crearLicencia(), agregar:
    public function eliminarLicencia()
    {
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            return;
        }

        $model = new GuiaRemision();
        $resultado = $model->eliminarLicencia($id);
        
        echo json_encode(['success' => $resultado]);
    }

    public function insertar3()
    {
        $c_guia = new GuiaRemision();
        $c_documentos = new DocumentoEmpresa();
        $guiaDetalle = new GuiaDetalle();
        $guiaSunat = new GuiaSunat();

        $dataSend = [];
        $dataSend["certGlobal"] = false;

        // Configurar datos de la guía desde los datos enviados por Ajax
        $c_guia->setFecha(filter_input(INPUT_POST, 'fecha_emision'));
        $c_guia->setIdVenta(filter_input(INPUT_POST, 'venta'));
        $c_guia->setDirLlegada(filter_input(INPUT_POST, 'dir_cli'));
        $c_guia->setUbigeo(filter_input(INPUT_POST, 'ubigeo'));
        $c_guia->setTipoTransporte(filter_input(INPUT_POST, 'tipo_trans'));
        $c_guia->setRucTransporte(filter_input(INPUT_POST, 'ruc') ?: '');
        $c_guia->setRazTransporte(filter_input(INPUT_POST, 'razon_social') ?: '');
        $c_guia->setVehiculo(filter_input(INPUT_POST, 'veiculo'));
        // Lógica de guardado según tipo de transportista
        $tipo_transporte = filter_input(INPUT_POST, 'tipo_trans');
        $chofer_brevete = filter_input(INPUT_POST, 'chofer_brevete'); // Valor del select licencia

        if ($tipo_transporte === '2') {
            // Transportista externo: usar DNI del campo y construir chofer_datos
            $chofer_dni = filter_input(INPUT_POST, 'chofer_dni');
            $tipo_documento = filter_input(INPUT_POST, 'tipo_documento') ?: 'DNI';
            $chofer_datos = $tipo_documento . ' | ' . $chofer_dni;
            
            $c_guia->setChofer($chofer_dni);
           
        } else {
            // Transportista propio: usar valor del select chofer
            $chofer_seleccionado = filter_input(INPUT_POST, 'chofer_dni'); // Viene del select
            $c_guia->setChofer($chofer_seleccionado);
            // chofer_datos ya se setea más abajo
        }

        // Establecer el brevete (licencia) correctamente
        $c_guia->setChoferBrevete($chofer_brevete);
        $c_guia->setPeso(filter_input(INPUT_POST, 'peso'));
        $c_guia->setNroBultos(filter_input(INPUT_POST, 'num_bultos'));

        // Campos adicionales para tu implementación
        $c_guia->setDestinatarioNombre(filter_input(INPUT_POST, 'nom_cli'));
        $c_guia->setDestinatarioDocumento(filter_input(INPUT_POST, 'doc_cli'));
        $c_guia->setDirPartida(filter_input(INPUT_POST, 'dir_part'));
        $c_guia->setMotivoTraslado(filter_input(INPUT_POST, 'motivo'));
        $c_guia->setObservaciones(filter_input(INPUT_POST, 'observacion'));
        $c_guia->setDocReferencia(filter_input(INPUT_POST, 'doc_referencia'));
        $chofer_datos_final = filter_input(INPUT_POST, 'chofer_datos') ?: '';
        $c_guia->setChoferDatos($chofer_datos_final);

        $c_guia->setIdEmpresa($_SESSION['id_empresa']);
        $c_guia->setSucursal($_SESSION['sucursal']);

        // Obtener serie y número automáticamente
        $c_documentos->setIdTido(11);
        $c_documentos->setIdEmpresa($c_guia->getIdEmpresa());
        $c_documentos->obtenerDatos();

        $c_guia->setSerie(filter_input(INPUT_POST, 'serie_g') ?: $c_documentos->getSerie());
        $c_guia->setNumero(filter_input(INPUT_POST, 'numero_g') ?: $c_documentos->getNumero());

        // Preparar datos para SUNAT
        $dataSend['peso'] = $c_guia->getPeso();
        $dataSend['ubigeo'] = $c_guia->getUbigeo();
        $dataSend['direccion'] = $c_guia->getDirLlegada();
        $dataSend['serie'] = $c_guia->getSerie();
        $dataSend['numero'] = $c_guia->getNumero();
        $dataSend['fecha'] = $c_guia->getFecha();

        $resultado = ["res" => false];
        
        if ($c_guia->insertar()) {
            $resultado["res"] = true;
            $resultado["guia"] = $c_guia->getIdGuia();
            
            // Procesar productos
            $listaProd = json_decode($_POST['productos'], true);
          
          
            $guiaDetalle->setIdGuia($c_guia->getIdGuia());

            $dataSend['productos'] = [];
            foreach ($listaProd as $prodG) {
                $guiaDetalle->setCantidad($prodG['cantidad']);
                $guiaDetalle->setDetalles($prodG['descripcion']);
                $guiaDetalle->setIdProducto($prodG['productoid'] ?? $prodG['idproducto']);
                $guiaDetalle->setPrecio($prodG['precio'] ?? 0);
                $guiaDetalle->setUnidad("NIU");
                $guiaDetalle->insertar();
                
                $dataSend['productos'][] = [
                    'cantidad' => $prodG['cantidad'],
                    'cod_pro' => $prodG['productoid'] ?? $prodG['idproducto'],
                    'cod_sunat' => "000",
                    'descripcion' => $prodG['descripcion']
                ];
            }

            $dataSend['productos'] = json_encode($dataSend['productos']);

            // Obtener datos de la empresa
            $sql = "SELECT * from empresas where id_empresa = " . $_SESSION['id_empresa'];
            $respEmpre = $c_guia->exeSQL($sql)->fetch_assoc();

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

            $dataSend['venta'] = json_encode([
                'serie' => filter_input(INPUT_POST, 'serie'),
                'numero' => filter_input(INPUT_POST, 'numero')
            ]);
            
            $dataSend['cliente'] = json_encode([
                'doc_num' => filter_input(INPUT_POST, 'doc_cli'),
                'nom_RS' => filter_input(INPUT_POST, 'nom_cli')
            ]);
            
            $dataSend['transporte'] = json_encode([
                'ruc' => filter_input(INPUT_POST, 'ruc'),
                'razon_social' => filter_input(INPUT_POST, 'razon_social'),
                'placa' => filter_input(INPUT_POST, 'veiculo'),
                'doc_chofer' => filter_input(INPUT_POST, 'chofer_dni')
            ]);

            // Generar XML para SUNAT
            $dataResp = $this->sunatApi->genGuiaRemision($dataSend);

            if ($dataResp["res"]) {
                $guiaSunat->setIdGuia($c_guia->getIdGuia());
                $guiaSunat->setHash($dataResp["data"]['hash']);
                $guiaSunat->setNombreXml($dataResp["data"]['nombre_archivo']);
                $guiaSunat->setQrData($dataResp["data"]['qr']);
                $guiaSunat->insertar();
            }
        }
        
        return json_encode($resultado);
    }
    
    // Agrega esta función después de la función buscarDocInfo() existente
    public function buscarDocInfo()
    {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InN5c3RlbWNyYWZ0LnBlQGdtYWlsLmNvbSJ9.yuNS5hRaC0hCwymX_PjXRoSZJWLNNBeOdlLRSUGlHGA';
        
        // Validar y sanitizar el documento
        $doc = filter_var($_POST['doc'], FILTER_SANITIZE_STRING);
        
        if (strlen($doc) == 8) {
            $url = 'https://dniruc.apisperu.com/api/v1/dni/' . $doc . '?token=' . $token;
        } else {
            $url = 'https://dniruc.apisperu.com/api/v1/ruc/' . $doc . '?token=' . $token;
        }

        $data = $this->apiRequest($url);
        
        if (isset($data['data'])) {
            if (strlen($doc) == 8) {
                $data["data"]["nombre"] = $data["data"]["nombres"] . " " . $data["data"]["apellidoPaterno"] . " " . $data["data"]["apellidoMaterno"];
            } else {
                $data["data"]["nombre"] = $data["data"]["razonSocial"];
            }
        }

        echo json_encode($data);
    }

}
