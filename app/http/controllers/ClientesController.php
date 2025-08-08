<?php

use Mpdf\Utils\Arrays;

require_once "app/models/Cliente.php";
require_once "utils/lib/exel/vendor/autoload.php";


class ClientesController extends Controller
{

    private $cliente;

    public function __construct()
    {
        $this->cliente = new Cliente();
        $this->conectar = (new Conexion())->getConexion();
    }



    public function insertarXLista()
    {
      /*   $lista = json_decode($_POST['lista'], true);
        echo json_encode($lista);
        die(); */
        $lista = json_decode($_POST['lista'], true);
        //var_dump($lista);
        $respuesta = ["res" => false];
        foreach ($lista as $item) {
           

            $datos = $item['datos'];
            $direccion = $item['direccion'];
            $direccion2 = $item['direccion2'];
            $sql = "INSERT into clientes set datos=?,
  documento='{$item['documento']}',
  direccion=?,
  direccion2=?,
  email='{$item['email']}',
  id_empresa='{$_SESSION['id_empresa']}',
  telefono='{$item['telefono']}',
  telefono2='{$item['telefono2']}'";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param('sss', $datos, $direccion, $direccion2);
            if ($stmt->execute()) {
                $respuesta["res"] = true;
            }
        }
        return json_encode($respuesta);
    }
    public function insertar()
    {
        if (!empty($_POST)) {
            $doc = trim(filter_var($_POST['documentoAgregar'], FILTER_SANITIZE_NUMBER_INT));
            $datosAgregar = trim(filter_var($_POST['datosAgregar'], FILTER_SANITIZE_STRING));
            $direccionAgregar = trim(filter_var($_POST['direccionAgregar'], FILTER_SANITIZE_STRING));
            $direccionAgregar2 = trim(filter_var($_POST['direccionAgregar2'], FILTER_SANITIZE_STRING));
            $telefonoAgregar = trim(filter_var($_POST['telefonoAgregar'], FILTER_SANITIZE_NUMBER_INT));
            $telefonoAgregar2 = trim(filter_var($_POST['telefonoAgregar2'], FILTER_SANITIZE_NUMBER_INT));
            $direccion = trim(filter_var($_POST['direccion'], FILTER_VALIDATE_EMAIL, FILTER_SANITIZE_EMAIL));
            $telefonoIntVal = intval($telefonoAgregar);
            $docIntVal = intval($doc);
            if ($doc !== "" && $datosAgregar !== "") {
                $telefonoTrueInt = filter_var($telefonoIntVal, FILTER_VALIDATE_INT);
                $doctTrueInt = filter_var($docIntVal, FILTER_VALIDATE_INT);
                if ($doctTrueInt == true) {
                    $this->cliente->setDocumento($doc);
                    $this->cliente->setDatos($datosAgregar);
                    $this->cliente->setDireccion($direccionAgregar);
                    $this->cliente->setDireccion2($direccionAgregar2);
                    $this->cliente->setTelefono($telefonoAgregar);
                    $this->cliente->setTelefono2($telefonoAgregar2);
                    $this->cliente->setEmail($direccion);
                    $save = $this->cliente->insertar();
                    if ($save == true) {
                        echo json_encode($this->cliente->idLast());
                    } else {
                        echo json_encode("Ocurrio un Error");
                    }
                } else {
                    echo json_encode('Llene el formulario correctamente 39');
                }
            } else {
                echo json_encode('Llene el formulario correctamente 42');
            }
        } else {
            echo json_encode('Error');
        }
    }
    public function render()
    {
        $getAll = $this->cliente->getAllData();
        echo json_encode($getAll);
    }
    public function getOne()
    {
        /* $presupuesto = new PresupuestosModel(); */
        $data = $_POST;
        $id = $data['id'];
        $getOne = $this->cliente->getOne($id);
        echo json_encode($getOne);
    }
    public function cuentasCobrar()
    {
        /* $presupuesto = new PresupuestosModel(); */

        $getAll = $this->cliente->cuentasCobrar();
        echo json_encode($getAll);
    }
    public function cuentasCobrarEstado()
    {
        $getAll = $this->cliente->cuentasCobrarEstado($_POST['id']);
        echo json_encode($getAll);
    }
    public function editar()
    {
        if (!empty($_POST)) {
            $doc = trim(filter_var($_POST['documentoEditar'], FILTER_SANITIZE_STRING));
            $datosEditar = trim(filter_var($_POST['datosEditar'], FILTER_SANITIZE_STRING));
            $direccionEditar = trim(filter_var($_POST['direccionEditar'], FILTER_SANITIZE_STRING));
            $direccionEditar2 = trim(filter_var($_POST['direccionEditar2'], FILTER_SANITIZE_STRING));
            $telefonoEditar = trim(filter_var($_POST['telefonoEditar'], FILTER_SANITIZE_STRING));
            $telefonoEditar2 = trim(filter_var($_POST['telefonoEditar2'], FILTER_SANITIZE_STRING));
            $emailEditar = trim(filter_var($_POST['emailEditar'], FILTER_SANITIZE_EMAIL));
            $emailValidate = filter_var($emailEditar, FILTER_VALIDATE_EMAIL);
            $telefonoIntVal = intval($telefonoEditar);
            $docIntVal = intval($doc);
            $id = $_POST['idCliente'];
            if ($doc !== "" && $datosEditar !== "") {
                $telefonoTrueInt = filter_var($telefonoIntVal, FILTER_VALIDATE_INT);
                $doctTrueInt = filter_var($docIntVal, FILTER_VALIDATE_INT);

                if ($doctTrueInt == true && strlen($docIntVal) == 8 || strlen($docIntVal) == 11) {
                    $this->cliente->setDocumento($doc);
                    $this->cliente->setDatos($datosEditar);
                    $this->cliente->setDireccion($direccionEditar);
                    $this->cliente->setDireccion2($direccionEditar2);
                    $this->cliente->setTelefono($telefonoEditar);
                    $this->cliente->setTelefono2($telefonoEditar2);
                    $this->cliente->setEmail($emailEditar);
                    $save = $this->cliente->editar($_POST['idCliente']);
                    if ($save == true) {
                        echo json_encode($this->cliente->getOne($id));
                    } else {
                        echo json_encode("Ocurrio un Error");
                    }
                } else {
                    echo json_encode('Llene el formulario correctamente');
                }
            } else {
                echo json_encode('Llene el formulario correctamente');
            }
        } else {
            echo json_encode('Error');
        }
    }
    public function borrar()
    {
        $dataId = $_POST["value"];
        $save = $this->cliente->delete($dataId);
        if ($save) {
            echo json_encode("nice");
        } else {
            echo json_encode("error");
        }
    }

    public function importarExcel()
    {
        $respuesta = ["res" => false];
        $filename = $_FILES['file']['name'];

        $path_parts = pathinfo($filename, PATHINFO_EXTENSION);
        $newName = Tools::getToken(80);
        /* Location */
        $loc_ruta = "files/temp";
        if (!file_exists($loc_ruta)) {
            mkdir($loc_ruta, 0777, true);
        }
        $location = $loc_ruta . "/" . $newName . '.' . $path_parts;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
            $nombre_logo = $newName . "." . $path_parts;

            $respuesta["res"] = true;
            $type = $path_parts;

            if ($type == "xlsx") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            } elseif ($type == "xls") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } elseif ($type == "csv") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            }

            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load("files/temp/" . $nombre_logo);

            $schdeules = $spreadsheet->getActiveSheet()->toArray();
            // array_shift($schdeules);
            $respuesta["data"] = $schdeules;

            unlink($location);
            //return $schdeules;
            /*   $last = $this->cliente->idLast();
            $arr = array($respuesta, $last); */
        }

        return json_encode($respuesta);
    }
    /*   public function importAdd(){
        echo json_encode($_POST);
    } */
 
     
 public function guardarCliente()
    {
        $clienteModel = new Cliente();
        // Verificar que la solicitud sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        // Validar campos requeridos
        $camposRequeridos = [
            'tipo_doc', 'n_documento', 'nombres', 'apellido_paterno', 
            'apellido_materno', 'fecha_nacimiento', 'departamento', 
            'provincia', 'distrito', 'direccion_detallada'
        ];
        
        foreach ($camposRequeridos as $campo) {
            if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
                echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios']);
                return;
            }
        }
        
        // Verificar si el número de documento ya existe
        if ($clienteModel->documentoExistente($_POST['n_documento'])) {
            echo json_encode(['success' => false, 'message' => 'El número de documento ya está registrado']);
            return;
        }
        
        // Preparar datos del cliente
        $datosCliente = [
            'tipo_doc' => $_POST['tipo_doc'],
            'n_documento' => $_POST['n_documento'],
            'nombres' => $_POST['nombres'],
            'apellido_paterno' => $_POST['apellido_paterno'],
            'apellido_materno' => $_POST['apellido_materno'],
            'num_cod_finan' => isset($_POST['num_cod_finan']) ? $_POST['num_cod_finan'] : '',
            'nacionalidad' => isset($_POST['nacionalidad']) ? $_POST['nacionalidad'] : '',
            'fecha_nacimiento' => $_POST['fecha_nacimiento'],
            'telefono' => isset($_POST['telefono']) ? $_POST['telefono'] : '',
            'correo' => isset($_POST['correo']) ? $_POST['correo'] : '',
            'departamento' => $_POST['departamento'],
            'provincia' => $_POST['provincia'],
            'distrito' => $_POST['distrito'],
            'direccion_detallada' => $_POST['direccion_detallada'],
            'emergencia_nombre' => isset($_POST['emergencia_nombre']) ? $_POST['emergencia_nombre'] : '',
            'emergencia_telefono' => isset($_POST['emergencia_telefono']) ? $_POST['emergencia_telefono'] : '',
            'emergencia_parentesco' => isset($_POST['emergencia_parentesco']) ? $_POST['emergencia_parentesco'] : '',
            'laboral_nombre' => isset($_POST['laboral_nombre']) ? $_POST['laboral_nombre'] : '',
            'laboral_telefono' => isset($_POST['laboral_telefono']) ? $_POST['laboral_telefono'] : '',
            'laboral_puesto' => isset($_POST['laboral_puesto']) ? $_POST['laboral_puesto'] : '',
            'laboral_empresa' => isset($_POST['laboral_empresa']) ? $_POST['laboral_empresa'] : '',
            'comentarios' => isset($_POST['comentarios']) ? $_POST['comentarios'] : '',
        ];
        
        // Procesar archivos adjuntos
        $archivos = [
            'recibo_servicios',
            'doc_identidad',
            'otro_doc_1',
            'otro_doc_2',
            'otro_doc_3'
        ];
        
        $rutasArchivos = [];
        $directorio =  "public" . DIRECTORY_SEPARATOR . "clientesFiles" . DIRECTORY_SEPARATOR;
        
        // Crear directorio si no existe
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        
        foreach ($archivos as $archivo) {
            if (isset($_FILES[$archivo]) && $_FILES[$archivo]['error'] === UPLOAD_ERR_OK) {
                $nombreOriginal = $_FILES[$archivo]['name'];
                $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
                $nombreUnico = uniqid($archivo . '_') . '.' . $extension;
                $rutaDestino = $directorio . $nombreUnico;
                
                // Mover archivo a carpeta destino
                if (move_uploaded_file($_FILES[$archivo]['tmp_name'], $rutaDestino)) {
                    $rutasArchivos[$archivo] = $rutaDestino;
                } else {
                    // Error al mover el archivo
                    echo json_encode(['success' => false, 'message' => 'Error al subir el archivo ' . $nombreOriginal]);
                    return;
                }
            } else {
                // Si no se subió archivo o hubo error, asignar vacío
                $rutasArchivos[$archivo] = '';
            }
        }
        
        // Agregar rutas de archivos a datos del cliente
        $datosCliente = array_merge($datosCliente, $rutasArchivos);
        
        // Guardar cliente en la base de datos
        $resultado = $clienteModel->guardarCliente($datosCliente);
        
        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Cliente registrado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el cliente']);
        }
    }

    /**
     * MÃ©todo para consultar datos de un cliente por nÃºmero de documento
     * 
     * @param string $numeroDocumento NÃºmero de documento del cliente
     * @return void
     */
    public function consultarCliente($numeroDocumento)
    {
        $cliente = $this->clienteModel->obtenerClientePorDocumento($numeroDocumento);
        
        if ($cliente) {
            echo json_encode(['success' => true, 'data' => $cliente]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cliente no encontrado']);
        }
    }

    public function cargarDatosClientes() {

        $clienteModel = new Cliente();

        // ParÃ¡metros de paginaciÃ³n y bÃºsqueda
        $pagina = isset($_POST['pagina']) ? intval($_POST['pagina']) : 1;
        $registrosPorPagina = isset($_POST['registrosPorPagina']) ? intval($_POST['registrosPorPagina']) : 10;
        $busqueda = isset($_POST['busqueda']) ? $_POST['busqueda'] : "";
        
        $inicio = ($pagina - 1) * $registrosPorPagina;
        
        // Obtener clientes con paginaciÃ³n
        $clientes = $clienteModel->obtenerClientes($inicio, $registrosPorPagina, $busqueda);
        $totalClientes = $clienteModel->totalClientes($busqueda);
        $totalPaginas = ceil($totalClientes / $registrosPorPagina);
        
        // Preparar respuesta JSON
        $response = [
            'clientes' => $clientes,
            'totalRegistros' => $totalClientes,
            'totalPaginas' => $totalPaginas,
            'paginaActual' => $pagina
        ];
        
        echo json_encode($response);
    }
    
    /**
     * Obtiene los datos de un cliente para mostrar en el modal de detalles
     */
    public function verCliente() {

        $clienteModel = new Cliente();

        if (!isset($_POST['id']) || empty($_POST['id'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de cliente no proporcionado']);
            return;
        }
        
        $id = intval($_POST['id']);
        $cliente = $clienteModel->obtenerCliente($id);
        
        if ($cliente) {
            // Preparar la direcciÃ³n completa
            $cliente['direccion_completa'] = "";
            if (!empty($cliente['departamento_nombre'])) {
                $cliente['direccion_completa'] .= $cliente['departamento_nombre'];
            }
            if (!empty($cliente['provincia_nombre'])) {
                $cliente['direccion_completa'] .= ", " . $cliente['provincia_nombre'];
            }
            if (!empty($cliente['distrito_nombre'])) {
                $cliente['direccion_completa'] .= ", " . $cliente['distrito_nombre'];
            }
            if (!empty($cliente['direccion_detallada'])) {
                $cliente['direccion_completa'] .= ", " . $cliente['direccion_detallada'];
            }
            
            echo json_encode(['success' => true, 'cliente' => $cliente]);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'Cliente no encontrado']);
        }
    }
    
    /**
     * Actualiza los datos de un cliente
     */
    public function editarCliente() {

        
        $clienteModel = new Cliente();

        // Verificar los datos requeridos
        $camposObligatorios = ['id', 'tipo_doc', 'n_documento', 'nombres', 'apellido_paterno', 'apellido_materno', 
                              'fecha_nacimiento', 'departamento', 'provincia', 'distrito', 'direccion_detallada'];
        
        foreach ($camposObligatorios as $campo) {
            if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
                echo json_encode(['success' => false, 'mensaje' => "El campo $campo es obligatorio"]);
                return;
            }
        }
        
        $id = intval($_POST['id']);
        $clienteActual = $clienteModel->obtenerCliente($id);
        
        if (!$clienteActual) {
            echo json_encode(['success' => false, 'mensaje' => 'Cliente no encontrado']);
            return;
        }
        
        // Verificar si el documento pertenece a otro cliente
        if ($clienteActual['n_documento'] != $_POST['n_documento'] && $clienteModel->documentoExistente($_POST['n_documento'])) {
            echo json_encode(['success' => false, 'mensaje' => 'El nÃºmero de documento ya existe en la base de datos']);
            return;
        }
        
        // Preparar datos para actualizar
        $datos = [
            'id' => $id,
            'tipo_doc' => $_POST['tipo_doc'],
            'n_documento' => $_POST['n_documento'],
            'nombres' => $_POST['nombres'],
            'apellido_paterno' => $_POST['apellido_paterno'],
            'apellido_materno' => $_POST['apellido_materno'],
            'nacionalidad' => isset($_POST['nacionalidad']) ? $_POST['nacionalidad'] : "",
            'fecha_nacimiento' => $_POST['fecha_nacimiento'],
            'telefono' => isset($_POST['telefono']) ? $_POST['telefono'] : "",
            'correo' => isset($_POST['correo']) ? $_POST['correo'] : "",
            'departamento' => $_POST['departamento'],
            'provincia' => $_POST['provincia'],
            'distrito' => $_POST['distrito'],
            'direccion_detallada' => $_POST['direccion_detallada'],
            'emergencia_nombre' => isset($_POST['emergencia_nombre']) ? $_POST['emergencia_nombre'] : "",
            'emergencia_telefono' => isset($_POST['emergencia_telefono']) ? $_POST['emergencia_telefono'] : "",
            'emergencia_parentesco' => isset($_POST['emergencia_parentesco']) ? $_POST['emergencia_parentesco'] : "",
            'laboral_nombre' => isset($_POST['laboral_nombre']) ? $_POST['laboral_nombre'] : "",
            'laboral_telefono' => isset($_POST['laboral_telefono']) ? $_POST['laboral_telefono'] : "",
            'laboral_puesto' => isset($_POST['laboral_puesto']) ? $_POST['laboral_puesto'] : "",
            'laboral_empresa' => isset($_POST['laboral_empresa']) ? $_POST['laboral_empresa'] : "",
            'comentarios' => isset($_POST['comentarios']) ? $_POST['comentarios'] : "",
            'recibo_servicios' => $clienteActual['recibo_servicios'],
            'doc_identidad' => $clienteActual['doc_identidad'],
            'otro_doc_1' => $clienteActual['otro_doc_1'],
            'otro_doc_2' => $clienteActual['otro_doc_2'],
            'otro_doc_3' => $clienteActual['otro_doc_3']
        ];
        
        // Verificar y procesar los archivos subidos
        $campos_archivos = [
            'recibo_servicios' => 'recibo_servicios_file',
            'doc_identidad' => 'doc_identidad_file',
            'otro_doc_1' => 'otro_doc_1_file',
            'otro_doc_2' => 'otro_doc_2_file',
            'otro_doc_3' => 'otro_doc_3_file'
        ];
        
        $directorio = "public" . DIRECTORY_SEPARATOR . "clientesFiles" . DIRECTORY_SEPARATOR;
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        
        foreach ($campos_archivos as $campo_bd => $campo_file) {
            if (isset($_FILES[$campo_file]) && $_FILES[$campo_file]['error'] == 0) {
                // Si hay un archivo nuevo, eliminamos el anterior
                if (!empty($clienteActual[$campo_bd]) && file_exists($clienteActual[$campo_bd])) {
                    unlink($clienteActual[$campo_bd]);
                }
                
                // Subimos el nuevo archivo
                $extension = pathinfo($_FILES[$campo_file]['name'], PATHINFO_EXTENSION);
                $nombre_archivo = uniqid() . '_' . $id . '.' . $extension;
                $ruta_completa = $directorio . $nombre_archivo;
                
                if (move_uploaded_file($_FILES[$campo_file]['tmp_name'], $ruta_completa)) {
                    $datos[$campo_bd] = $ruta_completa;
                }
            }
        }
        
        // Actualizar cliente
        if ($clienteModel->actualizarCliente($datos)) {
            echo json_encode(['success' => true, 'mensaje' => 'Cliente actualizado correctamente']);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'Error al actualizar cliente']);
        }
    }
    
    /**
     * Elimina un cliente y sus archivos asociados
     */
    public function eliminarCliente() {

        $clienteModel = new Cliente();

        if (!isset($_POST['id']) || empty($_POST['id'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de cliente no proporcionado']);
            return;
        }
        
        $id = intval($_POST['id']);
        
        if ($clienteModel->eliminarCliente($id)) {
            echo json_encode(['success' => true, 'mensaje' => 'Cliente eliminado correctamente']);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'Error al eliminar cliente']);
        }
    }
    
    /**
     * Obtiene la lista de departamentos
     */
    public function obtenerDepartamentos() {

        $clienteModel = new Cliente();

        $departamentos = $clienteModel->obtenerDepartamentos();
        echo json_encode(['success' => true, 'departamentos' => $departamentos]);
    }
    
    /**
     * Obtiene la lista de provincias de un departamento
     */
    public function obtenerProvincias() {

        $clienteModel = new Cliente();

        if (!isset($_POST['departamento']) || empty($_POST['departamento'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de departamento no proporcionado']);
            return;
        }
        
        $idDepartamento = intval($_POST['departamento']);
        $provincias = $clienteModel->obtenerProvincias($idDepartamento);
        echo json_encode(['success' => true, 'provincias' => $provincias]);
    }
    
    /**
     * Obtiene la lista de distritos de una provincia
     */
    public function obtenerDistritos() {

        $clienteModel = new Cliente();

        if (!isset($_POST['provincia']) || empty($_POST['provincia'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de provincia no proporcionado']);
            return;
        }
        
        $idProvincia = intval($_POST['provincia']);
        $distritos = $clienteModel->obtenerDistritos($idProvincia);
        echo json_encode(['success' => true, 'distritos' => $distritos]);
    }

    public function verEditarCliente()
    {
        $clienteModel = new Cliente();
        // Verificar si es una solicitud AJAX y POST
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && 
            $_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Asegurar que tenemos un ID
            if (empty($_POST['id'])) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'ID de cliente no proporcionado'
                ]);
                return;
            }
            
            $id = intval($_POST['id']);
            $cliente = $clienteModel->verEditarCliente($id);
            
            if ($cliente) {
                // Agregar la direcciÃ³n completa para mostrarla
                echo json_encode([
                    'success' => true,
                    'cliente' => $cliente
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'Cliente no encontrado'
                ]);
            }
        } else {
            // Si no es una solicitud AJAX, redirigir a la lista de clientes
            header('Location: /arequipago/listarClientes');
            exit;
        }
    }

}
