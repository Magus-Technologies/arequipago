<?php
require_once "app/models/Conductor.php";
require_once "app/models/DireccionConductor.php";
require_once "app/models/ContactoEmergencia.php";
require_once "app/models/PagoInscripcion.php";
require_once "app/models/Inscripcion.php";
require_once "app/models/Vehiculo.php";
require_once "app/models/Requisito.php";
require_once "app/models/Kit.php";
require_once "app/models/Observacion.php";
require_once 'app/models/Usuario.php';
require_once 'utils/lib/vendor/autoload.php'; // Importar PhpSpreadsheet
require_once 'utils/lib/exel/vendor/autoload.php'; // Importar PhpSpreadsheet
require_once "utils/lib/mpdf/vendor/autoload.php";  // Incluir el autoload de MPDF

use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet; // Importar la clase Spreadsheet
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ConductorController extends Controller
{

    private $conexion;
    //private $conductor;   

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
       // $this->conductor = new Conductor();
        $this->mpdf = new Mpdf();
    }

    
    // Método para mostrar la lista de conductores
   /* public function index()
    {
        $conductores = $this->conductor->verFilas();
        // Aquí deberías incluir la vista donde se mostrará la lista de conductores
        include_once 'app/views/conductor/index.php';
    }

    // Método para mostrar el formulario de creación de un nuevo conductor
    public function create()
    {
        // Mostrar formulario para agregar conductor
        include_once 'app/views/conductor/create.php';
    }

    // Método para almacenar un nuevo conductor
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recibimos los datos del formulario
            $this->conductor->setTipoDoc($_POST['tipo_doc']);
            $this->conductor->setNroDocumento($_POST['nro_documento']);
            $this->conductor->setNombres($_POST['nombres']);
            $this->conductor->setApellidoPaterno($_POST['apellido_paterno']);
            $this->conductor->setApellidoMaterno($_POST['apellido_materno']);
            $this->conductor->setNacionalidad($_POST['nacionalidad']);
            $this->conductor->setNroLicencia($_POST['nro_licencia']);
            $this->conductor->setTelefono($_POST['telefono']);
            $this->conductor->setCorreo($_POST['correo']);

            // Insertar conductor en la base de datos
            $resultado = $this->conductor->insertar();

            // Redirigir a la lista de conductores o mostrar un mensaje de éxito
            if ($resultado) {
                header("Location: /conductores"); // Redirigir a la lista
            } else {
                // Aquí podrías mostrar un mensaje de error
                echo "Error al insertar conductor";
            }
        }
    }

    // Método para mostrar el formulario de edición de un conductor
    public function edit($id)
    {
        $this->conductor->setIdConductor($id);
        $this->conductor->obtenerDatos();
        
        // Mostrar formulario de edición con los datos del conductor
        include_once 'app/views/conductor/edit.php';
    }

    // Método para actualizar los datos de un conductor
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recibimos los datos del formulario
            $this->conductor->setIdConductor($id);
            $this->conductor->setTipoDoc($_POST['tipo_doc']);
            $this->conductor->setNroDocumento($_POST['nro_documento']);
            $this->conductor->setNombres($_POST['nombres']);
            $this->conductor->setApellidoPaterno($_POST['apellido_paterno']);
            $this->conductor->setApellidoMaterno($_POST['apellido_materno']);
            $this->conductor->setNacionalidad($_POST['nacionalidad']);
            $this->conductor->setNroLicencia($_POST['nro_licencia']);
            $this->conductor->setTelefono($_POST['telefono']);
            $this->conductor->setCorreo($_POST['correo']);

            // Actualizar los datos del conductor en la base de datos
            $resultado = $this->conductor->modificar();

            // Redirigir a la lista de conductores o mostrar un mensaje de éxito
            if ($resultado) {
                header("Location: /conductores"); // Redirigir a la lista
            } else {
                // Aquí podrías mostrar un mensaje de error
                echo "Error al actualizar conductor";
            }
        }
    }

    // Método para buscar conductores por nombre o apellido
    public function buscar()
    {
        if (isset($_GET['term'])) {
            $term = $_GET['term'];
            $resultados = $this->conductor->buscarConductores($term);
            echo json_encode($resultados); // Retornar los resultados en formato JSON
        }
    }*/
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
    
    public function apiRequest($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }

   
    public function verDetalleConductor()
    {
        header('Content-Type: application/json');
        try {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0; // Cambié 'id_conductor' para que coincida con la variable de la URL (comentado para claridad)

            // Verificar si el ID del conductor es válido
            if ($id <= 0) {  // Verificar si el ID es inválido
                throw new Exception("ID del conductor no válido");
            }

            // Instanciar modelos
            $conductor = new Conductor();
            $direccionConductor = new DireccionConductor();
            $vehiculo = new Vehiculo();
            $requisito = new Requisito();
            $estadosRequisitos = $requisito->obtenerEstadoRequisitos($id);
            $inscripcion = new Inscripcion(); 

            // Obtener el valor de 'setare' desde el modelo Inscripcion
            $inscripcionData = $inscripcion->obtenerInscripcionPorConductor($id);

            // NUEVO: Determinar si tiene documentación completa
            $documentosObligatorios = ['doc_identidad', 'licencia_doc', 'seguro_doc', 'revision_tecnica', 'soat_doc', 'tarjeta_propiedad', 'carta_desvinculacion'];

            // Si el tipo de servicio es 'Particular', excluir carta_desvinculacion
            if (isset($inscripcionData['setare']) && $inscripcionData['setare'] === 'Particular') {
                $documentosObligatorios = array_filter($documentosObligatorios, function($doc) {
                    return $doc !== 'carta_desvinculacion';
                });
            }

            $documentacionCompleta = true;
            foreach ($documentosObligatorios as $doc) {
                if (!isset($estadosRequisitos[$doc]) || $estadosRequisitos[$doc] == 0) {
                    $documentacionCompleta = false;
                    break;
                }
            }

            $observacion = new Observacion();
            $contactoEmergencia = new ContactoEmergencia();

            // Obtener datos del conductor
            $conductor->setIdConductor($id);
            if (!$conductor->obtenerDatos()) {
                throw new Exception("No se encontraron datos para el conductor con ID $id");
            }
            $conductorArray = []; // Definir un array vacío para almacenar los datos
            foreach ((array) $conductor as $key => $value) { // Recorrer las propiedades del objeto
                $key = preg_replace('/^\x00.+\x00/', '', $key); // Eliminar prefijos de propiedades privadas
                $conductorArray[$key] = $value; // Asignar al array con las claves limpias
            }

            // NUEVO: Agregar estados de verificación al array del conductor (MOVIDO AQUÍ)
            $conductorArray['documentacion_completa'] = $documentacionCompleta;
            $conductorArray['verificacion_domiciliaria'] = isset($conductorArray['verificacion_domiciliaria']) ? 
                (bool)$conductorArray['verificacion_domiciliaria'] : false;
                

            $query = "SELECT usuario_id FROM conductores WHERE id_conductor = ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $conductorData = $result->fetch_assoc();

            // NUEVO: Si existe usuario_id, obtener datos del asesor
            if ($conductorData && isset($conductorData['usuario_id'])) {
                $usuario = new Usuario();
                $datosAsesor = $usuario->getData($conductorData['usuario_id']);
                
                // NUEVO: Agregar nombre completo del asesor al array del conductor
                if ($datosAsesor) {
                    $nombreCompleto = $datosAsesor['nombres'] . ' ' . $datosAsesor['apellidos'];
                    $conductorArray['asesor'] = $nombreCompleto;
                }
            }

            // NUEVO: Buscar inscripción relacionada con el conductor
            $inscripcionQuery = "SELECT id_inscripcion FROM inscripciones WHERE id_conductor = ?";
            $stmtInscripcion = $this->conexion->prepare($inscripcionQuery);
            $stmtInscripcion->bind_param("i", $id);
            $stmtInscripcion->execute();
            $resultInscripcion = $stmtInscripcion->get_result();
            $inscripcionData = $resultInscripcion->fetch_assoc();

            // NUEVO: Variable para almacenar datos del kit
            $kitArray = [];
            
            // NUEVO: Si encontramos la inscripción, buscar el kit asociado
            if ($inscripcionData && isset($inscripcionData['id_inscripcion'])) {
                $idInscripcion = $inscripcionData['id_inscripcion'];
                
                // NUEVO: Consulta directa para obtener los datos del kit
                $kitQuery = "SELECT * FROM kits WHERE id_inscripcion = ?";
                $stmtKit = $this->conexion->prepare($kitQuery);
                $stmtKit->bind_param("i", $idInscripcion);
                $stmtKit->execute();
                $resultKit = $stmtKit->get_result();
                $kitArray = $resultKit->fetch_assoc() ?: [];
            }


            // Obtener dirección del conductor
            $direccion = $direccionConductor->obtenerDatosDireccion($id);
            
            // Obtener datos del vehículo
            $datosVehiculo = $vehiculo->obtenerDatosVehiculo($id);

            // Obtener requisitos
            $datosRequisitos = $requisito->obtenerDatosRequisitos($id);

            $datoobservacion = $observacion->obtenerObservacion($id);
            

            // Obtener el valor de 'setare' desde el modelo Inscripcion
            $inscripcion = $inscripcion->obtenerInscripcionPorConductor($id);
           
            // Obtener contacto de emergencia
            $contactoEmergencia->setIdConductor($id); // Se cambió setIdConductor en vez de id_contacto
            $contactoEmergencia->obtenerDatosporConductor();

            $contactoEmergenciaArray = [ // Agregado: Crear el array con los datos de contacto
                'nombres' => $contactoEmergencia->getNombres(),
                'telefono' => $contactoEmergencia->getTelefono(),
                'parentesco' => $contactoEmergencia->getParentesco()
            ];

            // INICIO: Verificación de cuotas vencidas por financiamiento de inscripción
            $financiamientoInscripcion = $this->verificarFinanciamientoInscripcion($id);
            
            // INICIO: Verificación de cuotas vencidas por financiamiento de productos
            $financiamientoProductos = $this->verificarFinanciamientoProductos($id);

            $response = [
                'success' => true,
                'data' => [
                    'conductor' => $conductorArray, // Asegurarse de que el array tenga los datos correctos
                    'direccion' => $direccion ?: [], // Asegurar que no sea null
                    'vehiculo' => $datosVehiculo ?: [], // Asegurar que no sea null
                    'requisitos' => $datosRequisitos ?: [], // Asegurar que no sea null
                    'observacion' => $datoobservacion,
                    'inscripcion' => $inscripcionData,
                    'contactoEmergencia' => $contactoEmergenciaArray,
                    'financiamientoInscripcion' => $financiamientoInscripcion, // Nuevo: Añadimos información sobre financiamiento de inscripción
                    'financiamientoProductos' => $financiamientoProductos,
                    'kit' => $kitArray,
                    'estadosRequisitos' => $estadosRequisitos 
                ]
            ];
        } catch (Exception $e) {
            // Manejar errores
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    private function verificarFinanciamientoInscripcion($idConductor)
    {
        // Inicializar respuesta
        $resultado = [
            'tiene_cuotas_vencidas' => false,
            'cantidad_cuotas_vencidas' => 0,
            'monto_total_vencido' => 0
        ];
        
        try {
            // Verificar si el conductor tiene financiamiento por inscripción
            $query = "SELECT id_conductorpago, id_tipopago FROM conductor_pago WHERE id_conductor = ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $idConductor);
            $stmt->execute();
            $resultPago = $stmt->get_result();
            
            // Si no hay registros, retornar el resultado por defecto
            if ($resultPago->num_rows === 0) {
                return $resultado;
            }
            
            $datosPago = $resultPago->fetch_assoc();
            
            // Si id_tipopago es 1, pagó al contado, no tiene financiamiento
            if ($datosPago['id_tipopago'] == 1) {
                return $resultado;
            }
            
            // Si id_tipopago es 2, tiene financiamiento, buscar en conductor_regfinanciamiento
            $query = "SELECT idconductor_regfinanciamiento FROM conductor_regfinanciamiento WHERE id_conductor = ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $idConductor);
            $stmt->execute();
            $resultFinanciamiento = $stmt->get_result();
            
            // Si no hay registros de financiamiento, retornar el resultado por defecto
            if ($resultFinanciamiento->num_rows === 0) {
                return $resultado;
            }
            
            $datosFinanciamiento = $resultFinanciamiento->fetch_assoc();
            $idFinanciamiento = $datosFinanciamiento['idconductor_regfinanciamiento'];
            
            // Verificar cuotas vencidas
            $fechaActual = date('Y-m-d');
            $query = "SELECT COUNT(*) as cantidad_cuotas, SUM(monto_cuota) as monto_total 
                    FROM conductor_cuotas 
                    WHERE idconductor_Financiamiento = ? 
                    AND fecha_vencimiento < ? 
                    AND estado_cuota = 'pendiente'";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("is", $idFinanciamiento, $fechaActual);
            $stmt->execute();
            $resultCuotas = $stmt->get_result();
            $datosCuotas = $resultCuotas->fetch_assoc();
            
            // Si hay cuotas vencidas, actualizar el resultado
            if ($datosCuotas['cantidad_cuotas'] > 0) {
                $resultado['tiene_cuotas_vencidas'] = true;
                $resultado['cantidad_cuotas_vencidas'] = $datosCuotas['cantidad_cuotas'];
                $resultado['monto_total_vencido'] = $datosCuotas['monto_total'];
            }
            
            return $resultado;
            
        } catch (Exception $e) {
            // En caso de error, retornar el resultado por defecto
            return $resultado;
        }
    }

    /**
     * Verifica si un conductor tiene cuotas vencidas de financiamiento de productos
     * @param int $idConductor ID del conductor a verificar
     * @return array Información sobre los financiamientos con cuotas vencidas
      */
      
 private function verificarFinanciamientoProductos($idConductor)
{
    $resultado = [
        'tiene_cuotas_vencidas' => false,
        'financiamientos' => []
    ];
    
    try {
        $query = "SELECT f.idfinanciamiento, f.idproductosv2, f.moneda, p.nombre as nombre_producto
                FROM financiamiento f
                JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                WHERE f.id_conductor = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $idConductor);
        $stmt->execute();
        $resultFinanciamientos = $stmt->get_result();

        if ($resultFinanciamientos->num_rows === 0) {
            return $resultado;
        }

        $fechaActual = date('Y-m-d');

        while ($financiamiento = $resultFinanciamientos->fetch_assoc()) {
            $idFinanciamiento = $financiamiento['idfinanciamiento'];

            $query = "SELECT COUNT(*) as cantidad_cuotas, SUM(monto) as monto_total 
                FROM cuotas_financiamiento 
                WHERE id_financiamiento = ? 
                AND fecha_vencimiento < ? 
                AND estado != 'pagado'";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("is", $idFinanciamiento, $fechaActual);
            $stmt->execute();
            $resultCuotas = $stmt->get_result();
            $datosCuotas = $resultCuotas->fetch_assoc();

            if ($datosCuotas['cantidad_cuotas'] > 0 && $datosCuotas['monto_total'] > 0) {
                $resultado['tiene_cuotas_vencidas'] = true;
                $resultado['financiamientos'][] = [
                    'idfinanciamiento' => $idFinanciamiento,
                    'nombre_producto' => $financiamiento['nombre_producto'],
                    'cantidad_cuotas_vencidas' => $datosCuotas['cantidad_cuotas'],
                    'monto_total_vencido' => $datosCuotas['monto_total'],
                    'moneda' => $financiamiento['moneda']
                ];
            }
        }

        return $resultado;

    } catch (Exception $e) {
        return $resultado;
    }
}



    
    public function descargarDocumento()
    {
        // Validate and sanitize the input
        $filePath = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_STRING);
    
        if (!$filePath) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid file path']);
            exit;
        }

        // Asegurar que la ruta no incluya "public/uploadFiles/"
        $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
            
        // Obtener la ruta absoluta
        $fullPath = realpath($filePath);

            // DEPURACIÓN: Ver valores de las rutas
            

            if ($fullPath === false || !file_exists($fullPath)) { // <-- Verificación corregida
                http_response_code(404);
                echo json_encode(['error' => 'File not found']);
                exit;
            }

        // Obtener el tipo MIME del archivo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fullPath);
        finfo_close($finfo);
    
        // Configurar las cabeceras para la descarga
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . basename($fullPath) . '"');
        header('Content-Length: ' . filesize($fullPath));

    
        // Output file contents
        readfile($fullPath);
        exit;
    } 

    public function allConductors() {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID de conductor no proporcionado']);
            return;
        }

        $id_conductor = $_GET['id'];

        // Obtener datos del conductor
        $conductor = new Conductor();
        $conductor->setIdConductor($id_conductor);
        $datosConductor = $conductor->obtenerDatosEdit();

        // Verificar si tiene financiamiento vehicular
        $tieneFinanciamientoVehicular = $this->tieneFinanciamientoVehicular($id_conductor);
        $datosConductor['tiene_financiamiento_vehicular'] = $tieneFinanciamientoVehicular;

        if ($datosConductor === false) { // Changed condition to check for false explicitly
            echo json_encode(['success' => false, 'message' => 'Error al obtener datos del conductor']);
            return;
        }      

        // Obtener dirección del conductor
        $direccion = new DireccionConductor();
        $datosDireccion = $direccion->getEditdirection($id_conductor);

        // Obtener contacto de emergencia
        $contactoEmergencia = new ContactoEmergencia();
        $contactoEmergencia->setIdConductor($id_conductor);
        $contactoEmergencia->obtenerDatosporConductor();

        //var_dump($contactoEmergencia->toArray());

        // Obtener datos del vehículo
        $vehiculo = new Vehiculo();
        $datosVehiculo = $vehiculo->obtenerDatosVehiculo($id_conductor);

        // Obtener tipo de vehículo específico
        $tipoVehiculo = $vehiculo->obtenerPlacaPorConductor($id_conductor);
        $datosVehiculo['tipo_vehiculo'] = $tipoVehiculo['tipo_vehiculo'] ?? 'auto';

        // Obtener datos de inscripción
        $inscripcion = new Inscripcion();
        $datosInscripcion = $inscripcion->obtenerInscripcionPorConductor($id_conductor);

        //var_dump($datosInscripcion);

        // Obtener datos de requisitos (rutas de archivos)
        $requisito = new Requisito();
        $datosRequisitos = $requisito->obtenerDatosRequisitos($id_conductor);
        
        // Si no hay datos, inicializar con valores vacíos
        if (!$datosRequisitos) {
            $datosRequisitos = [
                'recibo_servicios' => null,
                'carta_desvinculacion' => null,
                'revision_tecnica' => null,
                'soat_doc' => null,
                'seguro_doc' => null,
                'tarjeta_propiedad' => null,
                'licencia_doc' => null,
                'doc_identidad' => null,
                'doc_otro1' => null,
                'doc_otro2' => null,
                'doc_otro3' => null,
            ];
        }

        //var_dump($estadoRequisitos);

        // Obtener datos del kit
        $kit = new Kit();
        $datosKit = $kit->obtenerKitPorConductor($id_conductor);

        //var_dump($datosKit);

        // Obtener observaciones
        $observacion = new Observacion();
        $datosObservacion = $observacion->obtenerObservacionPorConductor($id_conductor);

        //var_dump($datosObservacion);

        // Combinar todos los datos
        $datosCombinados = array_merge(
            ['conductor' => $datosConductor],
            ['direccion' => $datosDireccion],
            ['contacto_emergencia' => $contactoEmergencia->toArray()],
            ['vehiculo' => $datosVehiculo],
            ['inscripcion' => $datosInscripcion],
            ['requisitos' => $datosRequisitos],
            ['kit' => $datosKit],
            ['observacion' => $datosObservacion]
        );

        echo json_encode(['success' => true, 'data' => $datosCombinados]);
    }

    public function allConductorsva(){

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID de conductor no proporcionado']);
            return;
        }
    
        $id_conductor = $_GET['id'];
    
        // Obtener datos del conductor
        $conductor = new Conductor();
        $conductor->setIdConductor($id_conductor);
        $datosConductor = $conductor->obtenerDatosEdit();
    
        if ($datosConductor === false) {
            echo json_encode(['success' => false, 'message' => 'Error al obtener datos del conductor']);
            return;
        }
    
        // Obtener observaciones
        $observacion = new Observacion();
        $datosObservacion = $observacion->obtenerObservacionPorConductor($id_conductor);
    
        // Filtrar solo los datos requeridos del conductor
        $datosFiltrados = [
            'nombres'          => $datosConductor['nombres'] ?? null,
            'apellido_paterno' => $datosConductor['apellido_paterno'] ?? null,
            'apellido_materno' => $datosConductor['apellido_materno'] ?? null,
            'correo'           => $datosConductor['correo'] ?? null,
            'observacion'      => $datosObservacion ?? null,
        ];
    
        echo json_encode(['success' => true, 'data' => $datosFiltrados]);

    }

    public function datoPagoConductor()
    {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            echo json_encode([]);
            return;
        }

        $id_conductor = intval($_GET['id']);

        
        $conductor = new Conductor();
        $datosPago = $conductor->obtenerDatosPago($id_conductor);

        if ($datosPago) {
            echo json_encode($datosPago);
        } else {
            echo json_encode([]);
        }
    }

    public function deleteInfoPagoConductor() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_conductor'])) {
            $id_conductor = intval($_POST['id_conductor']); // Sanitiza el ID
            $conductor = new Conductor();
            
            $resultado = $conductor->eliminarPago($id_conductor);

            echo json_encode([
                'success' => $resultado,
                'message' => $resultado ? 'Información eliminada correctamente' : 'Error al eliminar la información'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }
    }

    public function deleteConductor() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") { // Verifica que la solicitud sea POST
            $id_conductor = $_POST["id_conductor"] ?? null; // Obtiene el ID del conductor

            if ($id_conductor) {
                $conductor = new Conductor(); // Instancia el modelo
                $resultado = $conductor->eliminarConductor($id_conductor); // Llama al método en el modelo y pasa el ID
    
                echo json_encode($resultado); 
                
            } else {
                echo json_encode(["success" => false, "message" => "No se recibió un ID válido"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Método no permitido"]);
        }
    }
    
    public function generarDataBaseConductors() {
        $conductorModel = new Conductor();
        $usuarioModel = new Usuario();
        $conductores = $conductorModel->obtenerConductoresDataBase();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $headers = ['ID', 'Tipo Doc', 'Nro Documento', 'Nombre Completo', 'Fecha Nacimiento', 'Categoría Licencia', 'Nro Licencia', 'Teléfono', 'Correo', 'Unidad', 'Condición', 'Vehículo Flota', 'Fecha SOAT', 'Fecha Seguro', 'Dirección', 'Placa', 'Marca', 'Modelo', 'Año', 'Color', 'Tipo Servicio', 'Tipo Pago', 'Monto', 'Cronograma de Pagos', 'Observaciones', 'Fecha de Inscripción', // Nueva columna
            'Asesor', // Nueva columna
            'Fecha de Pago Inicial', // Nueva columna
            'Contacto de Emergencia - Nombre', // Nueva columna
            'Contacto de Emergencia - Teléfono', // Nueva columna
            'Contacto de Emergencia - Parentesco', // Nueva columna
            'Resumen del Kit Entregado' // Nueva columna
        ];
        $sheet->fromArray([$headers], null, 'A1');

        // Aplicar estilo de centrado a los encabezados
        $sheet->getStyle('A1:Y1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Centra los encabezados

        // Ajustar el ancho de las columnas especificadas
       // Ajustar el ancho de las columnas específicas
        $sheet->getColumnDimension('Z')->setWidth(23); // Ancho para "Fecha de Inscripción" (duplicado)
        $sheet->getColumnDimension('AA')->setWidth(22);
        $sheet->getColumnDimension('AB')->setWidth(35); // Ancho para "Fecha de Pago Inicial" (duplicado)
        $sheet->getColumnDimension('AC')->setWidth(30); // Ancho para "Contacto de Emergencia - Nombre" (duplicado)
        $sheet->getColumnDimension('AD')->setWidth(30); // Ancho para "Contacto de Emergencia - Teléfono" (duplicado)
        $sheet->getColumnDimension('AE')->setWidth(30); // Ancho para "Contacto de Emergencia - Parentesco" (duplicado)
        $sheet->getColumnDimension('AF')->setWidth(48); // Ancho para "Resumen del Kit Entregado" (triplicado)

        
        $rowIndex = 2;
        foreach ($conductores as $conductor) {
            $id = $conductor['id_conductor'];
            $nombreCompleto = $conductor['nombre_completo'];
            $datosPago = $conductorModel->obtenerDatosPago($id);

            // Obtener fecha de inscripción
            $queryInscripcion = "SELECT fecha_inscripcion, setare FROM inscripciones WHERE id_conductor = ?";
            $stmtInscripcion = $this->conexion->prepare($queryInscripcion);
            $stmtInscripcion->bind_param("i", $id);
            $stmtInscripcion->execute();
            $resultInscripcion = $stmtInscripcion->get_result();
            $inscripcion = $resultInscripcion->fetch_assoc();
            $fechaInscripcion = $inscripcion['fecha_inscripcion'] ?? '';
            $fechaPagoInicial = $inscripcion['setare'] ?? '';

            // Nueva lógica: Obtener monto al contado o inicial y fecha de pago
            $queryPago = "SELECT monto, fecha_pago FROM pagos_inscripcion WHERE id_conductor = ? AND id_pago NOT IN (SELECT idpagos_inscripcion FROM detalle_pago_inscripcion)";
            $stmtPago = $this->conexion->prepare($queryPago);
            $stmtPago->bind_param("i", $id);
            $stmtPago->execute();
            $resultPago = $stmtPago->get_result();
            $pago = $resultPago->fetch_assoc();
            $montoInicial = isset($pago['monto']) ? "S/" . number_format($pago['monto'], 2) : 'Sin datos';
            $fechaPago = isset($pago['fecha_pago']) ? $pago['fecha_pago'] : 'Sin datos';
            $montoYFecha = $montoInicial . " | Fecha de Pago: " . $fechaPago;

            // Obtener asesor
            $asesorId = $conductor['usuario_id'];
            $asesorData = $usuarioModel->getData($asesorId);
            $nombreAsesor = (!empty($asesorData['nombres']) ? $asesorData['nombres'] : '') . ' ' . (!empty($asesorData['apellidos']) ? $asesorData['apellidos'] : '');

            // Obtener contacto de emergencia
            $queryContactoEmergencia = "SELECT nombres, telefono, parentesco FROM contacto_emergencia WHERE id_conductor = ?";
            $stmtContactoEmergencia = $this->conexion->prepare($queryContactoEmergencia);
            $stmtContactoEmergencia->bind_param("i", $id);
            $stmtContactoEmergencia->execute();
            $resultContactoEmergencia = $stmtContactoEmergencia->get_result();
            $contactoEmergencia = $resultContactoEmergencia->fetch_assoc();
            $nombreContactoEmergencia = $contactoEmergencia['nombres'] ?? '';
            $telefonoContactoEmergencia = $contactoEmergencia['telefono'] ?? '';
            $parentescoContactoEmergencia = $contactoEmergencia['parentesco'] ?? '';


            // Obtener resumen del kit entregado
            $queryKit = "SELECT logo_yango, fotocheck, polo, talla, logo_aqpgo, casquete 
                        FROM kits 
                        WHERE id_inscripcion = (SELECT id_inscripcion FROM inscripciones WHERE id_conductor = ?)";
            $stmtKit = $this->conexion->prepare($queryKit);
            $stmtKit->bind_param("i", $id);
            $stmtKit->execute();
            $resultKit = $stmtKit->get_result();
            $kit = $resultKit->fetch_assoc();
            $resumenKit = '';
            if ($kit) {
                if ($kit['logo_yango'] == 1) $resumenKit .= "✅ Logo Yango\n";
                if ($kit['fotocheck'] == 1) $resumenKit .= "✅ Fotocheck\n";
                if ($kit['polo'] == 1) $resumenKit .= "✅ Polo (Talla: " . $kit['talla'] . ")\n";
                if ($kit['logo_aqpgo'] == 1) $resumenKit .= "✅ Logo AQP\n";
                if ($kit['casquete'] == 1) $resumenKit .= "✅ Casquete\n";
                if (!$resumenKit) $resumenKit = "❌ Sin kit entregado";
            } else {
                $resumenKit = "❌ Sin kit entregado";
            }

            $tipoPago = $datosPago['tipo_pago'] ?? '';
            $monto = isset($datosPago['monto_pago']) ? "S/" . number_format($datosPago['monto_pago'], 2) : '';
            $cronogramaPagos = '';

            if ($tipoPago === 'Financiamiento' && !empty($datosPago['cuotas'])) {
                $cronogramaPagos = "📌 Fecha V. - Monto\n";
                foreach ($datosPago['cuotas'] as $cuota) {
                    $cronogramaPagos .= $cuota['fecha_vencimiento'] . ' - S/' . number_format($cuota['monto_cuota'], 2) . "\n"; // Cambio 'monto' a 'monto_cuota'
                }
            } elseif ($tipoPago === 'Contado') {
                $cronogramaPagos = 'No aplica';
            }

            $sheet->setCellValue("A$rowIndex", $id);
            $sheet->setCellValue("B$rowIndex", $conductor['tipo_doc']);
            $sheet->setCellValue("C$rowIndex", $conductor['nro_documento']);
            $sheet->setCellValue("D$rowIndex", $nombreCompleto);
            $sheet->setCellValue("E$rowIndex", $conductor['fech_nac']);
            $sheet->setCellValue("F$rowIndex", $conductor['categoria_licencia']);
            $sheet->setCellValue("G$rowIndex", $conductor['nro_licencia']);
            $sheet->setCellValue("H$rowIndex", $conductor['telefono']);
            $sheet->setCellValue("I$rowIndex", $conductor['correo']);
            $sheet->setCellValue("J$rowIndex", $conductor['numUnidad']);
            $sheet->setCellValue("K$rowIndex", $conductor['condicion']);
            $sheet->setCellValue("L$rowIndex", $conductor['vehiculo_flota']);
            $sheet->setCellValue("M$rowIndex", $conductor['fech_soat']);
            $sheet->setCellValue("N$rowIndex", $conductor['fech_seguro']);
            $sheet->setCellValue("O$rowIndex", $conductor['direccion']);
            $sheet->setCellValue("P$rowIndex", $conductor['placa']);
            $sheet->setCellValue("Q$rowIndex", $conductor['marca']);
            $sheet->setCellValue("R$rowIndex", $conductor['modelo']);
            $sheet->setCellValue("S$rowIndex", $conductor['anio']);
            $sheet->setCellValue("T$rowIndex", $conductor['color']);
            $sheet->setCellValue("U$rowIndex", $conductor['tipo_servicio']);
            $sheet->setCellValue("V$rowIndex", $tipoPago);
            $sheet->setCellValue("W$rowIndex", $monto);
            $sheet->setCellValue("X$rowIndex", $cronogramaPagos);
            $sheet->setCellValue("Y$rowIndex", $conductor['observaciones']);
            $sheet->setCellValue("Z$rowIndex", $fechaInscripcion);
            $sheet->setCellValue("AA$rowIndex", $nombreAsesor);
            $sheet->setCellValue("AB$rowIndex", $montoYFecha);
            $sheet->setCellValue("AC$rowIndex", $nombreContactoEmergencia);
            $sheet->setCellValue("AD$rowIndex", $telefonoContactoEmergencia); // Asignar contenido primero
            $sheet->getStyle("AD$rowIndex")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("AD$rowIndex", $telefonoContactoEmergencia);
            $sheet->setCellValue("AE$rowIndex", $parentescoContactoEmergencia);
            $sheet->setCellValue("AF$rowIndex", $resumenKit);
            
            $rowIndex++;
        }

        // Autoajustar el tamaño de las columnas
        foreach (range('A', 'Y') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Aplicar alineación centrada a todas las celdas de datos
        $sheet->getStyle("A2:Y$rowIndex")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Centra todas las columnas

        // Generar contenido del archivo Excel en memoria
        ob_start(); // Captura el contenido en el buffer
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $excelContent = ob_get_clean(); // Obtiene el contenido generado

        // Convertir el archivo a base64
        $base64Excel = base64_encode($excelContent);

        // Retornar los datos como JSON
        echo json_encode([
            'excel' => $base64Excel,
            'nombre_excel' => 'Reporte_Conductores.xlsx'
        ]);
        exit();
    }

    public function buscarPorDni() {
        if (!isset($_POST['dni']) || empty($_POST['dni'])) {
            echo json_encode(["success" => false, "message" => "Debe ingresar un documento válido."]);
            return;
        }

        $dni = trim($_POST['dni']);

        // Primero buscar en conductores
        $conductor = new Conductor();
        $idConductor = $conductor->buscarPorDocumento($dni);

        if ($idConductor) {
            // Si se encuentra en conductores, procesar como antes
            $conductor->setIdConductor($idConductor);
            if (!$conductor->obtenerDatos()) {
                echo json_encode(["success" => false, "message" => "No se pudieron obtener los datos del conductor."]);
                return;
            }

            $datosPago = $conductor->obtenerDatosPago($idConductor);

            echo json_encode([
                "success" => true,
                "tipo" => "conductor",
                "conductor" => [
                    "nombres" => $conductor->getNombres(),
                    "apellido_paterno" => $conductor->getApellidoPaterno(),
                    "apellido_materno" => $conductor->getApellidoMaterno()
                ],
                "cuotas" => $datosPago && isset($datosPago['cuotas']) ? $datosPago['cuotas'] : []
            ]);
            return;
        }

        // NUEVO: Si no se encuentra en conductores, buscar en clientes_financiar
        require_once "app/models/Cliente.php";
        $cliente = new Cliente();
        $datosCliente = $cliente->buscarClienteFinanciar($dni);

        if ($datosCliente) {
            $clienteId = $datosCliente['id'];

            // Verificar estado de pago del cliente
            $yaPago = $cliente->verificarPagoCliente($clienteId);
            $pagoPendiente = $cliente->verificarPagoPendiente($clienteId);

            $estadoPago = [
                "tiene_pago" => $yaPago,
                "tiene_pendiente" => ($pagoPendiente !== false),
                "pago_data" => null,
                "pendiente_data" => null
            ];

            // Si ya pagó, obtener datos del pago
            if ($yaPago) {
                $datosPago = $cliente->obtenerDatosPagoCliente($clienteId);
                if ($datosPago) {
                    $estadoPago["pago_data"] = [
                        "id" => $datosPago['id'],
                        "monto_total" => $datosPago['monto_total'],
                        "monto_pagado" => $datosPago['monto_pagado'],
                        "vuelto" => $datosPago['vuelto'],
                        "metodo_pago" => $datosPago['metodo_pago_nombre'],
                        "fecha_pago" => $datosPago['fecha_pago']
                    ];
                }
            }

            // Si tiene pago pendiente, incluir datos básicos
            if ($pagoPendiente) {
                $observaciones = json_decode($pagoPendiente['observaciones'], true);
                $estadoPago["pendiente_data"] = [
                    "id" => $pagoPendiente['id'],
                    "fecha_registro" => $pagoPendiente['fecha_registro'],
                    "monto" => isset($observaciones['monto_pago']) ? $observaciones['monto_pago'] : '100.00'
                ];
            }

            // Cliente encontrado en clientes_financiar
            echo json_encode([
                "success" => true,
                "tipo" => "cliente", // Identificar que es un cliente, no un conductor
                "conductor" => [
                    "nombres" => $datosCliente['nombres'],
                    "apellido_paterno" => $datosCliente['apellido_paterno'],
                    "apellido_materno" => $datosCliente['apellido_materno']
                ],
                "cuotas" => [], // Los clientes no tienen cuotas de inscripción
                "estado_pago" => $estadoPago // NUEVO: Estado de pago del cliente
            ]);
            return;
        }

        // Si no se encuentra ni en conductores ni en clientes_financiar
        echo json_encode(["success" => false, "message" => "Conductor no encontrado."]);
    }

    public function paymentMade() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
            $dni = trim($_POST['dni']);
            $metodoPago = $_POST['metodo_pago'];
            $monto = $_POST['monto'];
            $efectivoRecibido = $_POST['efectivo_recibido'];
            $vuelto = $_POST['vuelto'];
            $cuotas = $_POST['cuotas'];
            $idAsesor = $_SESSION['usuario_id'];
            $fechaPago = date("Y-m-d H:i:s");

            // Primero buscar en conductores
            $conductor = new Conductor();
            $idConductor = $conductor->buscarPorDocumento($dni);

            if ($idConductor) {
                // ES UN CONDUCTOR - Proceso existente
                $datosPago = $conductor->obtenerDatosPago($idConductor);
        
                if (!$datosPago || !isset($datosPago['financiamiento']['idconductor_regfinanciamiento'])) {
                    echo json_encode(["success" => false, "message" => "No se encontraron datos de financiamiento"]);
                    return;
                }
        
                $idInscripcion = $datosPago['financiamiento']['idconductor_regfinanciamiento'];
                
                // Instanciar el modelo PagoInscripcion y guardar los datos
                $pago = new PagoInscripcion();
                $idPago = $pago->registrarPago($idInscripcion, $metodoPago, $monto, $idConductor, $idAsesor, $fechaPago, $efectivoRecibido, $vuelto);
        
                if ($idPago) {
                    $pago->registrarDetallePago($idPago, $idInscripcion, $cuotas);
                    $pago->actualizarCuotas($idInscripcion, $cuotas, $fechaPago, $metodoPago); 

                    // Obtener datos del conductor
                    $conductor->setIdConductor($idConductor);
                    $conductor->obtenerDatos();
                    $nombreCompleto = $conductor->getNombres() . " " . $conductor->getApellidoPaterno() . " " . $conductor->getApellidoMaterno();
                    $tipoDoc = $conductor->getTipoDoc();
                    $nroDocumento = $conductor->getNroDocumento();
                    $tipoPago = "Financiamiento";
                    $tipoPersona = "Conductor";

                    // Generar PDF
                    $pdfContent = $this->generarPDFPago($nombreCompleto, $tipoDoc, $nroDocumento, $metodoPago, $monto, $efectivoRecibido, $vuelto, $fechaPago, $idAsesor, $cuotas, $datosPago, $tipoPersona, $tipoPago);
                    
                    $uploadDir = "files" . DIRECTORY_SEPARATOR . "notasPagoInscripcion" . DIRECTORY_SEPARATOR;
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $pdfPath = $uploadDir . "nota_venta_$idPago.pdf";
                    file_put_contents($pdfPath, base64_decode($pdfContent));

                    $pago->guardarNotaVenta($idPago, $idConductor, $idAsesor, $monto, $fechaPago, $pdfPath);
                    
                    echo json_encode(["success" => true, "message" => "Pago de conductor registrado correctamente", "pdf" => $pdfPath, "pdf_base64" => $pdfContent]);    
                } else {
                    echo json_encode(["success" => false, "message" => "Error al registrar el pago"]);
                }
                return;
            }

            // Si no es conductor, buscar en clientes
            $cliente = new Cliente();
            $datosCliente = $cliente->buscarClienteFinanciar($dni);

            if ($datosCliente) {
                // ES UN CLIENTE - Siempre pago al contado
                $idCliente = $datosCliente['id'];
                $nombreCompleto = $datosCliente['nombres'] . " " . $datosCliente['apellido_paterno'] . " " . $datosCliente['apellido_materno'];
                $tipoDoc = $datosCliente['tipo_doc'];
                $nroDocumento = $datosCliente['n_documento'];
                $tipoPago = "Pago al Contado";
                $tipoPersona = "Cliente";

                // Registrar pago en cliente_pago
                $sqlPago = "INSERT INTO cliente_pago (cliente_id, monto_pagado, monto_total, vuelto, metodo_pago_id, fecha_pago, usuario_id) 
                           VALUES (?, ?, ?, ?, (SELECT id_metodo_pago FROM metodo_pago WHERE nombre = ?), ?, ?)";
                $stmt = $this->conexion->prepare($sqlPago);
                $stmt->bind_param("idddssi", $idCliente, $monto, $monto, $vuelto, $metodoPago, $fechaPago, $idAsesor);
                
                if ($stmt->execute()) {
                    $idPago = $stmt->insert_id;

                    // Generar PDF (sin cuotas para clientes)
                    $pdfContent = $this->generarPDFPago($nombreCompleto, $tipoDoc, $nroDocumento, $metodoPago, $monto, $efectivoRecibido, $vuelto, $fechaPago, $idAsesor, [], null, $tipoPersona, $tipoPago);
                    
                    $uploadDir = "files" . DIRECTORY_SEPARATOR . "notasPagoInscripcion" . DIRECTORY_SEPARATOR;
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $pdfPath = $uploadDir . "nota_venta_cliente_$idPago.pdf";
                    file_put_contents($pdfPath, base64_decode($pdfContent));
                    
                    echo json_encode(["success" => true, "message" => "Pago de cliente registrado correctamente", "pdf" => $pdfPath, "pdf_base64" => $pdfContent]);
                } else {
                    echo json_encode(["success" => false, "message" => "Error al registrar el pago del cliente"]);
                }
                return;
            }

            // No se encontró ni conductor ni cliente
            echo json_encode(["success" => false, "message" => "No se encontró ningún conductor o cliente con ese documento"]);
        }
    }

    private function generarPDFPago($nombreCompleto, $tipoDoc, $nroDocumento, $metodoPago, $monto, $efectivoRecibido, $vuelto, $fechaPago, $idAsesor, $cuotas, $datosPago, $tipoPersona, $tipoPago) {
        $rutaBase = "app" . DIRECTORY_SEPARATOR . "contratos" . DIRECTORY_SEPARATOR . "nota_venta_inscripcion.html";
        $html = file_get_contents($rutaBase);
        
        if ($html === false) {
            throw new Exception("Error al leer la plantilla HTML");
        }
        
        $usuarioModel = new Usuario();
        $asesorData = $usuarioModel->getData($idAsesor);
        $nombreAsesordate = $asesorData ? $asesorData['nombres'] . ' ' . $asesorData['apellidos'] : 'Asesor no encontrado';

        $rutaLogo = 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'logo-ticket.png';
        $html = str_replace('{LOGO}', $rutaLogo, $html);
        
        // Generar detalle de cuotas o mensaje de pago al contado
        $detalleCuotas = "";
        if (!empty($cuotas) && $datosPago) {
            $detalleCuotas = implode("", array_map(function($cuota) use ($datosPago) {
                return $cuota['pagoH'] == "1" ? "Cuota " . $cuota['numero_cuota'] . ": S/. " . $datosPago['financiamiento']['monto_cuota'] . "<br>" . (!empty($cuota['mora']) ? "Mora de la Cuota " . $cuota['numero_cuota'] . ": S/. " . $cuota['mora'] . "<br>" : "") : "";
            }, $cuotas));
        } else {
            $detalleCuotas = "<p><strong>Tipo de Pago:</strong> " . $tipoPago . "</p>";
        }

        // Reemplazar en HTML - Cambiar "Conductor:" por el tipo de persona dinámicamente
        $html = str_replace('Conductor:', $tipoPersona . ':', $html);
        
        $html = str_replace([
            '<span id="fecha"></span>',
            '<span id="nombre_conductor"></span>',
            '<span id="documento"></span>',
            '<span id="nro_documento"></span>',
            '<span id="monto_pagado"></span>',
            '<span id="total_pagar"></span>',
            '<span id="vuelto"></span>',
            '<span id="total_ingresado"></span>',
            '<span id="metodo_pago"></span>',
            '<span id="asesor"></span>', 
            '<div id="detalle_cuotas"></div>'
        ], [
            $fechaPago,
            $nombreCompleto,
            $tipoDoc,
            $nroDocumento,
            $efectivoRecibido,
            $monto,
            $vuelto,
            $monto,
            $metodoPago,
            $nombreAsesordate,
            $detalleCuotas
        ], $html);

        $mpdf = new \Mpdf\Mpdf([
            'format' => [132, 210],
            'default_font_size' => 9
        ]);
        $mpdf->WriteHTML("<style> body { font-size: 11px; } </style>" . $html);
        
        return base64_encode($mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN));
    }

    public function generarEnlacePDF() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pdf_base64'])) {
            $pdfBase64 = $_POST['pdf_base64'];
           
            $pdfData = base64_decode($pdfBase64);
           
            // Generar un nombre único para el archivo
            $fileName = 'comprobante_' . uniqid() . '.pdf';
            $uploadDir = "files" . DIRECTORY_SEPARATOR . "compartir" . DIRECTORY_SEPARATOR;
            
            // Crear el directorio si no existe
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $filePath = $uploadDir . $fileName;
            
            // Guardar el PDF en el servidor
            if (file_put_contents($filePath, $pdfData)) {
                // Crear una URL pública para el archivo
                $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                $publicUrl = $baseUrl . "/files/compartir/" . $fileName;
                
                echo json_encode([
                    'success' => true,
                    'pdf_url' => $publicUrl
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al guardar el archivo'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Datos incompletos'
            ]);
        }
    }
    
    

    public function toggleDesvincularConductor() {
        // Establecer el header de Content-Type al inicio
        header('Content-Type: application/json');
    
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 3) {
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para realizar esta acción']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_conductor = $_POST['id_conductor'] ?? null;
            $estado = $_POST['estado'] ?? null;
    
            if ($id_conductor === null || $estado === null) {
                echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
                exit;
            }
    
            try {
                $sql = "UPDATE conductores SET desvinculado = ? WHERE id_conductor = ?";
                $stmt = $this->conexion->prepare($sql);
                
                if (!$stmt) {
                    throw new Exception("Error preparando la consulta");
                }
    
                if (!$stmt->bind_param("ii", $estado, $id_conductor)) {
                    throw new Exception("Error vinculando parámetros");
                }
    
                if (!$stmt->execute()) {
                    throw new Exception("Error ejecutando la consulta");
                }
    
                // Verificar si se actualizó alguna fila
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el estado del conductor']);
                }
                
                $stmt->close();
                
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
            }
            exit; // Asegurar que el script termine después de enviar la respuesta
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
    }
    public function obtenerReportesPagos() {
        header('Content-Type: application/json');
        
        try {
            // Consulta para pagos de conductores (notas_venta_inscripcion)
            $sqlConductores = "SELECT 
                    nv.idnotas_venta_inscripcion AS id_pago,
                    nv.monto,
                    nv.fecha_emision,
                    nv.ruta,
                    nv.id_asesor,
                    CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS nombre_persona,
                    c.numUnidad AS num_unidad,
                    CONCAT(u.nombres, ' ', IFNULL(u.apellidos, '')) AS nombre_asesor,
                    'conductor' AS tipo_persona
                    FROM notas_venta_inscripcion nv
                    INNER JOIN conductores c ON nv.id_conductor = c.id_conductor
                    LEFT JOIN usuarios u ON nv.id_asesor = u.usuario_id";
            
            // Consulta para pagos de clientes (cliente_pago)
            $sqlClientes = "SELECT 
                    cp.id AS id_pago,
                    cp.monto_pagado AS monto,
                    cp.fecha_pago AS fecha_emision,
                    '' AS ruta,
                    cp.usuario_id AS id_asesor,
                    CONCAT(clf.nombres, ' ', clf.apellido_paterno, ' ', clf.apellido_materno) AS nombre_persona,
                    NULL AS num_unidad,
                    CONCAT(u.nombres, ' ', IFNULL(u.apellidos, '')) AS nombre_asesor,
                    'cliente' AS tipo_persona
                    FROM cliente_pago cp
                    INNER JOIN clientes_financiar clf ON cp.cliente_id = clf.id
                    LEFT JOIN usuarios u ON cp.usuario_id = u.usuario_id";
            
            // Combinar ambas consultas ordenadas por fecha
            $sqlCombinada = "($sqlConductores) UNION ALL ($sqlClientes) ORDER BY fecha_emision DESC";
            
            error_log("SQL Query: " . $sqlCombinada);
            
            $result = $this->conexion->query($sqlCombinada);
            
            if (!$result) {
                error_log("Error en la consulta: " . $this->conexion->error);
                throw new Exception("Error en la consulta: " . $this->conexion->error);
            }
            
            $reportes = [];
            while ($row = $result->fetch_assoc()) {
                error_log("Fila encontrada: " . json_encode($row));
                
                if (empty($row['nombre_asesor'])) {
                    $idAsesor = $row['id_asesor'];

                    // 🔥 CORREGIDO: Verificar que $idAsesor no sea null o vacío antes de hacer la consulta
                    if (!empty($idAsesor) && $idAsesor !== null) {
                        $sqlAsesor = "SELECT CONCAT(nombres, ' ', IFNULL(apellidos, '')) AS nombre_asesor
                                    FROM usuarios WHERE usuario_id = ?";
                        $stmtAsesor = $this->conexion->prepare($sqlAsesor);
                        $stmtAsesor->bind_param("i", $idAsesor);
                        $stmtAsesor->execute();
                        $resultAsesor = $stmtAsesor->get_result();

                        if ($resultAsesor && $rowAsesor = $resultAsesor->fetch_assoc()) {
                            $row['nombre_asesor'] = $rowAsesor['nombre_asesor'];
                        } else {
                            $row['nombre_asesor'] = 'Sin asignar';
                        }
                    } else {
                        // Si no hay ID de asesor, poner "Sin asignar"
                        $row['nombre_asesor'] = 'Sin asignar';
                    }
                }
                
                $reportes[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'reportes' => $reportes
            ]);
            
        } catch (Exception $e) {
            error_log("Exception: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function eliminarReportePago() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Solicitud inválida'
            ]);
            return;
        }
        
        $id = (int)$_POST['id'];
        
        try {
            // Primero obtenemos la ruta del archivo PDF para eliminarlo
            $sql = "SELECT ruta FROM notas_venta_inscripcion WHERE idnotas_venta_inscripcion = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $rutaPdf = $row['ruta'];
                
                // Eliminar el archivo físico si existe
                if (file_exists($rutaPdf)) {
                    unlink($rutaPdf);
                }
                
                // Eliminar el registro de la base de datos
                $sql = "DELETE FROM notas_venta_inscripcion WHERE idnotas_venta_inscripcion = ?";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Reporte eliminado correctamente'
                    ]);
                } else {
                    throw new Exception("Error al eliminar el registro: " . $stmt->error);
                }
            } else {
                throw new Exception("No se encontró el reporte con ID: " . $id);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getIdConductorforDni() {
        // Verificar si se ha recibido el DNI
        if (isset($_POST['dni']) && !empty($_POST['dni'])) {
            $dni = $_POST['dni'];

            // Instanciar el modelo Conductor
            $conductorModel = new Conductor();

            // Buscar el ID del conductor
            $idConductor = $conductorModel->buscarPorDocumento($dni);

            // Verificar si se encontró un ID
            if ($idConductor) {
                // Retornar el id al frontend
                echo json_encode($idConductor);
            } else {
                // Si no se encontró el id, retornar un mensaje de error
                echo json_encode(['error' => 'Conductor no encontrado']);
            }
        } else {
            echo json_encode(['error' => 'DNI no proporcionado']);
        }
    }

    public function reportPagos() {
        // Desactivar la visualización de errores para evitar que se envíen al navegador
        ini_set('display_errors', 0); 
        
        ob_clean(); 

        // Crear un nuevo objeto Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte de Pagos');
        
        // Establecer encabezados
        $headers = ['Ítem', 'Conductor', 'DNI', 'Nº Unidad', 'Asesor', 'Monto Inicial / Al contado', 'Cuotas Pagadas', 'Cuotas No Pagadas'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        
        // Estilo para encabezados
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DDDDDD']
            ]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        
        // MODIFICADO: Obtener datos de conductores sin repetir usando GROUP BY para evitar duplicados
        $query = "SELECT pi.id_conductor, 
                    CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS nombre_completo,
                    c.nro_documento, c.numUnidad, pi.id_asesor, MAX(pi.monto) as monto
                  FROM pagos_inscripcion pi
                  JOIN conductores c ON pi.id_conductor = c.id_conductor
                  GROUP BY pi.id_conductor, nombre_completo, c.nro_documento, c.numUnidad, pi.id_asesor
                  ORDER BY c.apellido_paterno, c.apellido_materno, c.nombres"; // MODIFICADO: Agregado GROUP BY para evitar duplicados
        
        $result = mysqli_query($this->conexion, $query);
        
        // Modelo de Usuario para obtener datos del asesor
        $usuarioModel = new Usuario();
        
        $row = 2; // Comenzar desde la fila 2 (después de los encabezados)
        $item = 1; // Contador para la columna Ítem
        
        // Array para rastrear conductores ya procesados
        $conductoresProcesados = []; // AÑADIDO: Para evitar duplicados
        
        while ($conductor = mysqli_fetch_assoc($result)) {
            // AÑADIDO: Verificar si ya procesamos este conductor
            if (in_array($conductor['id_conductor'], $conductoresProcesados)) {
                continue; // Saltar este conductor si ya fue procesado
            }
            
            // Añadir conductor al array de procesados
            $conductoresProcesados[] = $conductor['id_conductor']; // AÑADIDO: Marcar conductor como procesado
            
            // Obtener datos del asesor
            $asesorData = $usuarioModel->getData($conductor['id_asesor']);
            $nombreAsesor = isset($asesorData['nombres']) ? $asesorData['nombres'] . ' ' . $asesorData['apellidos'] : 'No asignado';
            
            // Verificar si tiene cuota inicial (pago sin detalles)
            $queryPagoInicial = "SELECT pi.* FROM pagos_inscripcion pi
                                LEFT JOIN detalle_pago_inscripcion dpi ON pi.id_pago = dpi.idpagos_inscripcion
                                WHERE pi.id_conductor = {$conductor['id_conductor']} AND dpi.id_detallepago IS NULL
                                LIMIT 1";
            $resultPagoInicial = mysqli_query($this->conexion, $queryPagoInicial);
            $montoInicial = 0;
            $fechaPagoInicial = ''; 

            if ($pagoInicial = mysqli_fetch_assoc($resultPagoInicial)) {
                $montoInicial = $pagoInicial['monto'];
                $fechaPagoInicial = date('d/m/Y', strtotime($pagoInicial['fecha_pago']));
            }
            
            // Obtener cuotas pagadas
            $queryCuotasPagadas = "SELECT cc.numero_cuota, cc.monto_cuota, cc.metodo_pago, cc.fecha_pago
                                  FROM detalle_pago_inscripcion dpi
                                  JOIN pagos_inscripcion pi ON dpi.idpagos_inscripcion = pi.id_pago
                                  JOIN conductor_cuotas cc ON dpi.id_cuota = cc.id_conductorcuota
                                  WHERE pi.id_conductor = {$conductor['id_conductor']} AND cc.estado_cuota = 'pagado'
                                  ORDER BY cc.numero_cuota";
            $resultCuotasPagadas = mysqli_query($this->conexion, $queryCuotasPagadas);
            
            $cuotasPagadas = "";
            while ($cuota = mysqli_fetch_assoc($resultCuotasPagadas)) {
                $fechaPago = date('d/m/Y', strtotime($cuota['fecha_pago']));
                $cuotasPagadas .= "Cuota {$cuota['numero_cuota']} S/.{$cuota['monto_cuota']} {$cuota['metodo_pago']} {$fechaPago}\n";
            }
            
            // Obtener cuotas no pagadas usando la relación correcta entre tablas
            $queryCuotasNoPagadas = "SELECT cc.numero_cuota, cc.monto_cuota, cc.fecha_vencimiento
                                    FROM conductor_regfinanciamiento crf
                                    JOIN conductor_cuotas cc ON crf.idconductor_regfinanciamiento = cc.idconductor_Financiamiento
                                    WHERE crf.id_conductor = {$conductor['id_conductor']} AND cc.estado_cuota = 'pendiente'
                                    ORDER BY cc.numero_cuota";
            
            $resultCuotasNoPagadas = mysqli_query($this->conexion, $queryCuotasNoPagadas);
            
            $cuotasNoPagadas = "";
            
            // Verificar si la consulta fue exitosa
            if ($resultCuotasNoPagadas) {
                if (mysqli_num_rows($resultCuotasNoPagadas) > 0) {
                    while ($cuota = mysqli_fetch_assoc($resultCuotasNoPagadas)) {
                        $fechaVencimiento = date('d/m/Y', strtotime($cuota['fecha_vencimiento']));
                        $cuotasNoPagadas .= "Cuota {$cuota['numero_cuota']} S/.{$cuota['monto_cuota']} FV: {$fechaVencimiento}\n";
                    }
                } else {
                    $cuotasNoPagadas = "No hay cuotas pendientes";
                }
            } else {
                // Si hay error en la consulta, registrarlo para depuración
                error_log("Error en consulta de cuotas no pagadas: " . mysqli_error($this->conexion));
                $cuotasNoPagadas = "No hay cuotas pendientes";
            }
            
            // Llenar la fila con los datos
            $sheet->setCellValue('A' . $row, $item);
            $sheet->setCellValue('B' . $row, $conductor['nombre_completo']);
            $sheet->setCellValue('C' . $row, $conductor['nro_documento']);
            $sheet->setCellValue('D' . $row, $conductor['numUnidad']);
            $sheet->setCellValue('E' . $row, $nombreAsesor);
            // MODIFICADO: Agregar fecha de pago al monto inicial
            if ($montoInicial != 0) {
                $sheet->setCellValue('F' . $row, 'S/.' . $montoInicial . ' | Fecha de Pago: ' . $fechaPagoInicial);
            } else {
                $sheet->setCellValue('F' . $row, 'S/.' . $montoInicial);
            }
            $sheet->setCellValue('G' . $row, $cuotasPagadas);
            $sheet->setCellValue('H' . $row, $cuotasNoPagadas);
            
            // Ajustar el formato para las celdas con múltiples líneas
            $sheet->getStyle('G' . $row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('H' . $row)->getAlignment()->setWrapText(true);
            
            $row++;
            $item++;
        }
        
        // Autoajustar anchos de columna
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Crear el escritor para guardar el archivo
        $writer = new Xlsx($spreadsheet);
        
        // Nombre del archivo
        $filename = 'Reporte_Pagos_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Asegurarse de que no haya salida previa
        if (ob_get_length()) ob_end_clean();
        
        // Configurar encabezados para la descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: 0');
        header('Pragma: public');
        
        // Guardar el archivo al output
        $writer->save('php://output');
        exit; // Importante para evitar que se envíe contenido adicional
    }

    public function reportPagosUnificado() {
        // Desactivar la visualización de errores para evitar que se envíen al navegador
        ini_set('display_errors', 0); 
        
        ob_clean(); 

        // Crear un nuevo objeto Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte Pagos Unificado');
        
        // Establecer encabezados
        $headers = ['Ítem', 'Persona', 'Documento', 'Nº Unidad', 'Asesor', 'Monto Inicial / Al contado', 'Cuotas Pagadas', 'Cuotas No Pagadas'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        
        // Estilo para encabezados - Mejorado
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '02a499']
            ]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);
        
        // Estilo para datos
        $dataStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
        ];
        
        // Estilo para números (derecha alineados)
        $numberStyle = array_merge($dataStyle, [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
            'numberFormat' => ['formatCode' => '$#,##0.00']
        ]);
        
        $row = 2;
        $item = 1;
        $conductoresProcesados = [];
        
        // ========== CONDUCTORES ==========
        $queryConductores = "SELECT pi.id_conductor, 
                            CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS nombre_completo,
                            c.nro_documento, c.numUnidad, pi.id_asesor, MAX(pi.monto) as monto
                        FROM pagos_inscripcion pi
                        JOIN conductores c ON pi.id_conductor = c.id_conductor
                        GROUP BY pi.id_conductor, nombre_completo, c.nro_documento, c.numUnidad, pi.id_asesor
                        ORDER BY c.apellido_paterno, c.apellido_materno, c.nombres";
        
        $resultConductores = mysqli_query($this->conexion, $queryConductores);
        $usuarioModel = new Usuario();
        
        while ($conductor = mysqli_fetch_assoc($resultConductores)) {
            // Evitar duplicados
            if (in_array($conductor['id_conductor'], $conductoresProcesados)) {
                continue;
            }
            $conductoresProcesados[] = $conductor['id_conductor'];
            
            // Obtener asesor
            $asesorData = $usuarioModel->getData($conductor['id_asesor']);
            $nombreAsesor = isset($asesorData['nombres']) ? $asesorData['nombres'] . ' ' . $asesorData['apellidos'] : 'No asignado';
            
            // Pago inicial (sin detalles)
            $queryPagoInicial = "SELECT pi.* FROM pagos_inscripcion pi
                                LEFT JOIN detalle_pago_inscripcion dpi ON pi.id_pago = dpi.idpagos_inscripcion
                                WHERE pi.id_conductor = {$conductor['id_conductor']} AND dpi.id_detallepago IS NULL
                                LIMIT 1";
            $resultPagoInicial = mysqli_query($this->conexion, $queryPagoInicial);
            $montoInicial = 0;
            $fechaPagoInicial = '';

            if ($pagoInicial = mysqli_fetch_assoc($resultPagoInicial)) {
                $montoInicial = $pagoInicial['monto'];
                $fechaPagoInicial = date('d/m/Y', strtotime($pagoInicial['fecha_pago']));
            }
            
            // Cuotas pagadas
            $queryCuotasPagadas = "SELECT cc.numero_cuota, cc.monto_cuota, cc.metodo_pago, cc.fecha_pago
                                FROM detalle_pago_inscripcion dpi
                                JOIN pagos_inscripcion pi ON dpi.idpagos_inscripcion = pi.id_pago
                                JOIN conductor_cuotas cc ON dpi.id_cuota = cc.id_conductorcuota
                                WHERE pi.id_conductor = {$conductor['id_conductor']} AND cc.estado_cuota = 'pagado'
                                ORDER BY cc.numero_cuota";
            $resultCuotasPagadas = mysqli_query($this->conexion, $queryCuotasPagadas);
            
            $cuotasPagadas = "";
            while ($cuota = mysqli_fetch_assoc($resultCuotasPagadas)) {
                $fechaPago = date('d/m/Y', strtotime($cuota['fecha_pago']));
                $cuotasPagadas .= "Cuota {$cuota['numero_cuota']} S/.{$cuota['monto_cuota']} {$cuota['metodo_pago']} {$fechaPago}\n";
            }
            
            // Cuotas no pagadas
            $queryCuotasNoPagadas = "SELECT cc.numero_cuota, cc.monto_cuota, cc.fecha_vencimiento
                                    FROM conductor_regfinanciamiento crf
                                    JOIN conductor_cuotas cc ON crf.idconductor_regfinanciamiento = cc.idconductor_Financiamiento
                                    WHERE crf.id_conductor = {$conductor['id_conductor']} AND cc.estado_cuota = 'pendiente'
                                    ORDER BY cc.numero_cuota";
            
            $resultCuotasNoPagadas = mysqli_query($this->conexion, $queryCuotasNoPagadas);
            
            $cuotasNoPagadas = "";
            if ($resultCuotasNoPagadas) {
                if (mysqli_num_rows($resultCuotasNoPagadas) > 0) {
                    while ($cuota = mysqli_fetch_assoc($resultCuotasNoPagadas)) {
                        $fechaVencimiento = date('d/m/Y', strtotime($cuota['fecha_vencimiento']));
                        $cuotasNoPagadas .= "Cuota {$cuota['numero_cuota']} S/.{$cuota['monto_cuota']} FV: {$fechaVencimiento}\n";
                    }
                } else {
                    $cuotasNoPagadas = "No hay cuotas pendientes";
                }
            } else {
                $cuotasNoPagadas = "No hay cuotas pendientes";
            }
            
            // Llenar fila CONDUCTOR
            $sheet->setCellValue('A' . $row, $item);
            $sheet->setCellValue('B' . $row, $conductor['nombre_completo']);
            $sheet->setCellValue('C' . $row, $conductor['nro_documento']);
            $sheet->setCellValue('D' . $row, $conductor['numUnidad']);
            $sheet->setCellValue('E' . $row, $nombreAsesor);
            $sheet->setCellValue('F' . $row, ($montoInicial != 0) ? 'S/.' . $montoInicial . ' | Fecha: ' . $fechaPagoInicial : 'S/.' . $montoInicial);
            $sheet->setCellValue('G' . $row, $cuotasPagadas);
            $sheet->setCellValue('H' . $row, $cuotasNoPagadas);
            
            $sheet->getStyle('G' . $row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('H' . $row)->getAlignment()->setWrapText(true);
            
            $row++;
            $item++;
        }
        
        // ========== CLIENTES ==========
        $queryClientes = "SELECT cp.id, 
                        CONCAT(cf.nombres, ' ', cf.apellido_paterno, ' ', cf.apellido_materno) AS nombre_completo,
                        cf.n_documento,
                        cp.monto_pagado,
                        cp.fecha_pago,
                        cp.usuario_id,
                        u.nombres AS asesor_nombres,
                        u.apellidos AS asesor_apellidos
                FROM cliente_pago cp
                JOIN clientes_financiar cf ON cp.cliente_id = cf.id
                LEFT JOIN usuarios u ON cp.usuario_id = u.usuario_id
                ORDER BY cp.fecha_pago DESC";
        
        $resultClientes = mysqli_query($this->conexion, $queryClientes);
        
        while ($cliente = mysqli_fetch_assoc($resultClientes)) {
            $nombreAsesor = 'No asignado';
            if (!empty($cliente['asesor_nombres'])) {
                $nombreAsesor = $cliente['asesor_nombres'] . ' ' . (!empty($cliente['asesor_apellidos']) ? $cliente['asesor_apellidos'] : '');
            }
            
            $fechaPago = date('d/m/Y', strtotime($cliente['fecha_pago']));
            
            // Llenar fila CLIENTE
            $sheet->setCellValue('A' . $row, $item);
            $sheet->setCellValue('B' . $row, $cliente['nombre_completo']);
            $sheet->setCellValue('C' . $row, $cliente['n_documento']);
            $sheet->setCellValue('D' . $row, 'N/A');
            $sheet->setCellValue('E' . $row, $nombreAsesor);
            $sheet->setCellValue('F' . $row, 'S/.' . number_format($cliente['monto_pagado'], 2) . ' | Fecha: ' . $fechaPago);
            $sheet->setCellValue('G' . $row, 'Al Contado');
            $sheet->setCellValue('H' . $row, 'N/A');
            
            $row++;
            $item++;
        }
        
        // Autoajustar anchos de columna
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Crear el escritor para guardar el archivo
        $writer = new Xlsx($spreadsheet);
        
        // Nombre del archivo
        $filename = 'Reporte_Pagos_Completo_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Asegurarse de que no haya salida previa
        if (ob_get_length()) ob_end_clean();
        
        // Configurar encabezados para la descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: 0');
        header('Pragma: public');
        
        // Guardar el archivo al output
        $writer->save('php://output');
        exit;
    }    

    /**
 * Función para generar un PDF a partir de HTML de una tabla usando mPDF
 * 
 * @return void Envía el PDF directamente al navegador
 */
    function generatePdfFromTable() {
        try {
            // Extraer datos de $_POST
            $tableHtml = isset($_POST['tableHtml']) ? $_POST['tableHtml'] : '';
            $title = isset($_POST['title']) ? $_POST['title'] : 'Cronograma';
            
            // Verificar que se recibió el HTML de la tabla
            if (empty($tableHtml)) {
                header('HTTP/1.1 400 Bad Request');
                exit('No se recibieron datos de la tabla');
            }
            
            // Crear instancia de mPDF
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4-L', // Formato apaisado (landscape)
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 25,
                'margin_bottom' => 25
            ]);
            
            // Configurar encabezado y pie de página
            $mpdf->SetHTMLHeader('
                <div style="text-align: right; font-weight: bold;">
                    AREQUIPAGO
                </div>
            ');
            
            $mpdf->SetHTMLFooter('
                <div style="text-align: center; font-size: 10px;">
                    Página {PAGENO} de {nb}
                    <br>
                    Documento generado el ' . date('d/m/Y H:i:s') . '
                </div>
            ');
            
            // Estilos CSS para mejorar la apariencia de la tabla
            $css = '
                body {
                    font-family: Arial, sans-serif;
                    font-size: 14px;
                    line-height: 1.5;
                }
                h1 {
                    text-align: center;
                    font-size: 22px;
                    margin-bottom: 20px;
                    color: #343a40;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                table, th, td {
                    border: 1px solid #dee2e6;
                }
                th {
                    background-color: #343a40;
                    color: white;
                    font-weight: bold;
                    padding: 10px;
                    text-align: center;
                }
                td {
                    padding: 8px;
                    text-align: center;
                }
                tr:nth-child(even) {
                    background-color: #f8f9fa;
                }
                .estado-pendiente {
                    color: #ffc107;
                    font-weight: bold;
                }
                .estado-pagado {
                    color: #28a745;
                    font-weight: bold;
                }
                .estado-vencido {
                    color: #dc3545;
                    font-weight: bold;
                }
            ';
            
            // Agregar CSS
            $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
            
            // Crear el HTML completo
            $html = '
                <h1>' . htmlspecialchars($title) . '</h1>
                ' . $tableHtml . '
            ';
            
            // Agregar el HTML al PDF
            $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
            
            // Generar nombre del archivo
            $filename = 'Cronograma_Inscripcion_' . date('Ymd_His') . '.pdf';
            
            // Configurar encabezados para la descarga
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            // Salida del PDF
            $mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
            
        } catch (\Mpdf\MpdfException $e) {
            // Manejar errores de mPDF
            header('HTTP/1.1 500 Internal Server Error');
            exit('Error al generar el PDF: ' . $e->getMessage());
        } catch (Exception $e) {
            // Manejar otros errores
            header('HTTP/1.1 500 Internal Server Error');
            exit('Error: ' . $e->getMessage());
        }
    }

    private function tieneFinanciamientoVehicular($id_conductor)
    {
        $sql = "SELECT f.grupo_financiamiento 
                FROM financiamiento f
                WHERE f.id_conductor = ? AND f.estado_eliminado = 0";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id_conductor);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $grupo_financiamiento = $row['grupo_financiamiento'];
            
            // Verificar si este grupo de financiamiento es vehicular
            $sqlPlan = "SELECT tipo_vehicular 
                        FROM planes_financiamiento 
                        WHERE idplan_financiamiento = ?";
            
            $stmtPlan = $this->conexion->prepare($sqlPlan);
            $stmtPlan->bind_param("i", $grupo_financiamiento);
            $stmtPlan->execute();
            $resultPlan = $stmtPlan->get_result();
            
            if ($rowPlan = $resultPlan->fetch_assoc()) {
                if ($rowPlan['tipo_vehicular'] === 'vehiculo') {
                    $stmtPlan->close();
                    $stmt->close();
                    return true; // Tiene al menos un financiamiento vehicular
                }
            }
            $stmtPlan->close();
        }
        
        $stmt->close();
        return false; // No tiene financiamientos vehiculares
    }
}
