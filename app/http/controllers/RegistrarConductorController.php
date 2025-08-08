<?php

require_once "app/models/Conductor.php";
require_once "app/models/ContactoEmergencia.php";
require_once "app/models/Vehiculo.php";
require_once "app/models/Inscripcion.php";
require_once "app/models/Requisito.php";
require_once "app/models/Kit.php";
require_once "app/models/PagoInscripcion.php";
require_once "app/models/Observacion.php";
require_once "app/models/DireccionConductor.php";

class RegistrarConductorController extends Controller
{
    private $conectar;
    private $uploadDir = 'files/uploadFiles/';

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function registrarTodo()
    {
        try {
            // 1. Registrar conductor
            $id_conductor = $this->registrarConductor();
            if (!$id_conductor) {
                $this->enviarRespuesta(false, 'Error al registrar el conductor.');
                return;
            }

            // 2. Registrar vehículo
            if (!$this->registrarVehiculo($id_conductor)) {
                $this->enviarRespuesta(false, 'Error al registrar el vehículo.');
                return;
            }

            // 3. Registrar inscripción
            $id_inscripcion = $this->registrarInscripcion($id_conductor);
            if (!$id_inscripcion) {
                $this->enviarRespuesta(false, 'Error al registrar la inscripción.');
                return;
            }

            // 4. Registrar requisitos
            if (!$this->registrarRequisitos($id_inscripcion)) {
                $this->enviarRespuesta(false, 'Error al registrar los requisitos.');
                return;
            }

            // 5. Registrar kit
            if (!$this->registrarKit($id_inscripcion)) {
                $this->enviarRespuesta(false, 'Error al registrar el kit.');
                return;
            }

            // 6. Registrar contacto de emergencia
            if (!$this->registrarContactoEmergencia($id_conductor)) {
                $this->enviarRespuesta(false, 'Error al registrar el contacto de emergencia.');
                return;
            }

            // 7. Registrar dirección
            if (!$this->registrarDireccion($id_conductor)) {
                $this->enviarRespuesta(false, 'Error al registrar la dirección.');
                return;
            }

            // 8. Registrar observación
            if (!empty($_POST['comentarios'])) {
                if (!$this->registrarObservacion($id_inscripcion)) {
                    $this->enviarRespuesta(false, 'Error al registrar la observación.');
                    return;
                }
            }

            $this->enviarRespuesta(true, 'Registro completado con éxito.', ['id_conductor' => $id_conductor]);

        } catch (Exception $e) {
            $this->enviarRespuesta(false, 'Error: ' . $e->getMessage());
        }
    }

    private function registrarConductor()
    {
        try {
            // Verificar si ya existe el conductor
            $sql = "SELECT id_conductor FROM conductores WHERE nro_documento = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("s", $_POST['n_document']);
            $stmt->execute();
            $result = $stmt->get_result();

            $conductor = new Conductor();
            
            if ($result->num_rows > 0) {
                throw new Exception('Ya existe un conductor con este número de documento.');
            }
    
            // Validar fecha de nacimiento
            if (!isset($_POST['fechaNac']) || empty($_POST['fechaNac'])) {
                throw new Exception('La fecha de nacimiento es requerida');
            }
        
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) { // Verificar si la foto está presente y no tiene errores
                // Procesar la foto
                if (!$conductor->guardarFoto($_FILES['photo'])) { // Guardar la foto solo si está presente
                    throw new Exception('Error al guardar la foto del conductor'); // Error si no se pudo guardar la foto
                }
            } else {
                // La foto no es obligatoria, se puede continuar sin ella
                $conductor->setFoto(null);  // Establecer un valor nulo o predeterminado si no se envió la foto
            }
        
            
            // Setear todos los campos
            $conductor->setTipoDoc($_POST['tipo_doc']);
            $conductor->setNroDocumento($_POST['n_document']);
            $conductor->setNombres($_POST['nombres']);
            $conductor->setApellidoPaterno($_POST['apellido_paterno']);
            $conductor->setApellidoMaterno($_POST['apellido_materno']);
            $conductor->setNacionalidad($_POST['nacionalidad']);
            $conductor->setNroLicencia($_POST['licencia']);
            $conductor->setTelefono($_POST['telefono']);
            $conductor->setCorreo(isset($_POST['correo']) ? $_POST['correo'] : '');
            $conductor->setNumUnidad($_POST['numUnidad']);
            $conductor->setNumeroCodFi($_POST['numerocodfi']);
            $conductor->setCategoriaLicencia($_POST['licenciaCa']);
            
            // Formatear la fecha antes de guardarla
            $fecha = date('Y-m-d', strtotime($_POST['fechaNac']));
            $conductor->setFechNac($fecha);
        
            
        
            // Insertar el conductor
            $id_conductor = $conductor->insertar();
            
            if (!$id_conductor) {
                throw new Exception('Error al insertar el conductor en la base de datos');
            }

                    
            
           
            
            return $id_conductor;
        
        } catch (Exception $e) {
            error_log("Error en registrarConductor: " . $e->getMessage());
            $this->enviarRespuesta(false, $e->getMessage());
            return false;
        }
    }

    private function registrarVehiculo($id_conductor)
    {
        try {
            $vehiculo = new Vehiculo();
            $vehiculo->setIdConductor($id_conductor);
            $vehiculo->setPlaca($_POST['placa']);
            $vehiculo->setMarca($_POST['marca']);
            $vehiculo->setModelo($_POST['modelo']);
            $vehiculo->setAnio($_POST['anio']);
            $vehiculo->setNumeroUnidad($_POST['numUnidad']);
            $vehiculo->setCondicion($_POST['condicion']);
            $vehiculo->setVehiculoFlota($_POST['vehiculo_flota']);
            $vehiculo->setFechSoat($_POST['vencimiento']);
            $vehiculo->setFechSeguro($_POST['vencimiento_seguro']);
            $vehiculo->setColor($_POST['color']);
            $vehiculo->setTipoVehiculo($_POST['tipo_vehiculo']); // Nueva línea

            $id_vehiculo = $vehiculo->insertar();

            
            
            if (!$id_vehiculo) {
                throw new Exception('Error al insertar el vehículo');
            }
            
            $_SESSION['id_vehiculo'] = $id_vehiculo;
            
            return true;
        } catch (Exception $e) {
            error_log("Error en registrarVehiculo: " . $e->getMessage());
            return false;
        }
    }
    
    private function registrarInscripcion($id_conductor)
    {
        try {
           
            if (!isset($_SESSION['id_vehiculo'])) {
                throw new Exception('ID del vehículo no encontrado');
            }
    

            // Validar que existan los índices necesarios
            $tipo_serv = isset($_POST['tipo_serv']) ? $_POST['tipo_serv'] : null;
            $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : null;
            $nro_unidad = isset($_POST['nro_unidad']) ? $_POST['nro_unidad'] : null;
    
            // Validar que los campos requeridos no sean nulos
            if (!$tipo_serv || !$fecha || !$nro_unidad) {
                throw new Exception('Faltan campos requeridos para la inscripción');
            }
    
        

            $inscripcion = new Inscripcion();
            $inscripcion->setIdConductor($id_conductor);
            $inscripcion->setIdVehiculo($_SESSION['id_vehiculo']);
            $inscripcion->setSetare($tipo_serv);
            $inscripcion->setFechaInscripcion($fecha); 
            $inscripcion->setNroUnidad($nro_unidad);
    
            $id_inscripcion = $inscripcion->insertar();
            
            if (!$id_inscripcion) {
                throw new Exception('Error al insertar la inscripción');
            }
           
            
            unset($_SESSION['id_vehiculo']);
            
            
            return $id_inscripcion;
        } catch (Exception $e) {
            error_log("Error en registrarInscripcion: " . $e->getMessage());
         
            throw $e;
        }
    }
    
    private function registrarRequisitos($id_inscripcion)
    {
        try {
            // Definir la carpeta donde se guardarán los archivos
            $uploadDir = "public" . DIRECTORY_SEPARATOR . "uploadFiles" . DIRECTORY_SEPARATOR;


            // Verificar si la carpeta de destino existe, si no, crearla
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Función para manejar la subida de archivos
            function subirArchivo($file, $uploadDir) {
                $uniqueFileName = uniqid() . '.' . strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
                $targetFile = $uploadDir . $uniqueFileName;
                $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                // Verificar si el archivo es válido
                if ($file["size"] > 0 && in_array($fileType, ['pdf', 'jpg', 'png', 'docx'])) {
                    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
                        return $targetFile;
                    } else {
                        return false;
                    }
                }
                return false; // Archivo no válido
            }

            // Procesar los archivos
            $recibo_servicio = isset($_FILES['recibo_servicio']) ? subirArchivo($_FILES['recibo_servicio'], $uploadDir) : null;
            $carta_desvinculacion = isset($_FILES['carta_desvinculacion']) ? subirArchivo($_FILES['carta_desvinculacion'], $uploadDir) : null;
            $revision_tecnica = isset($_FILES['revision_tecnica']) ? subirArchivo($_FILES['revision_tecnica'], $uploadDir) : null;
            $soat_doc = isset($_FILES['soatdoc']) ? subirArchivo($_FILES['soatdoc'], $uploadDir) : null;
            $seguro_doc = isset($_FILES['seguroDoc']) ? subirArchivo($_FILES['seguroDoc'], $uploadDir) : null;
            $tarjeta_propiedad = isset($_FILES['tarjeta_propiedad']) ? subirArchivo($_FILES['tarjeta_propiedad'], $uploadDir) : null;
            $licencia = isset($_FILES['licenciadoc']) ? subirArchivo($_FILES['licenciadoc'], $uploadDir) : null;
            $doc_identidad = isset($_FILES['docIdentidad']) ? subirArchivo($_FILES['docIdentidad'], $uploadDir) : null;
            $doc_otro1 = isset($_FILES['docotro1']) ? subirArchivo($_FILES['docotro1'], $uploadDir) : null;
            $doc_otro2 = isset($_FILES['docotro2']) ? subirArchivo($_FILES['docotro2'], $uploadDir) : null;
            $doc_otro3 = isset($_FILES['docotro3']) ? subirArchivo($_FILES['docotro3'], $uploadDir) : null;

            // Crear y configurar el objeto Requisito
            $requisito = new Requisito();
            $requisito->setIdInscripcion($id_inscripcion);
            $requisito->setReciboServicios($recibo_servicio);
            $requisito->setCartaDesvinculacion($carta_desvinculacion);
            $requisito->setRevisionTecnica($revision_tecnica);
            $requisito->setSoatDoc($soat_doc);
            $requisito->setSeguroDoc($seguro_doc);
            $requisito->setTarjetaPropiedad($tarjeta_propiedad);
            $requisito->setLicenciaDoc($licencia);
            $requisito->setDocIdentidad($doc_identidad);
            $requisito->setDocOtro1($doc_otro1);
            $requisito->setDocOtro2($doc_otro2);
            $requisito->setDocOtro3($doc_otro3);

            // Intentar insertar en la base de datos
            if (!$requisito->insertar()) {
                throw new Exception('Error al insertar los requisitos en la base de datos');
            }

            return true;

        } catch (Exception $e) {
            error_log("Error en registrarRequisitos: " . $e->getMessage());
            return false;
        }
    }

    private function registrarKit($id_inscripcion)
    {
        try {
            $kit = new Kit();
            $kit->setIdInscripcion($id_inscripcion);
    
            // Procesamiento de polo (habilitarSelect)
            $polo = isset($_POST['polo']) ? $_POST['polo'] : '0';
            $kit->setPolo($polo);
    
            // Procesamiento de talla
            $talla = isset($_POST['talla']) ? trim($_POST['talla']) : null;
            if (empty($talla) || $talla === "Seleccionar") {
                $talla = null;
            }
            $kit->setTalla($talla);

    
            // Procesamiento de otros checkboxes
            $checkboxes = [
                'logo_yango' => 'setLogo_yango',
                'logo_aqp' => 'setLogoAqpgo',
                'casquete' => 'setCasquete',
                'fotocheck' => 'setFotocheck'
            ];
    
            foreach ($checkboxes as $checkbox => $method) {
                $value = isset($_POST[$checkbox]) ? $_POST[$checkbox] : '0';
                $kit->$method($value);
            }
    
            // Log para depuración
            error_log("Datos del kit a insertar: " . print_r([
                'id_inscripcion' => $kit->getIdInscripcion(),
                'polo' => $kit->getPolo(),
                'talla' => $kit->getTalla(),
                'logo_yango' => $kit->getLogoYango(),
                'logo_aqp' => $kit->getLogoAqpgo(), // Ahora usamos el método correcto
                'casquete' => $kit->getCasquete(),
                'fotocheck' => $kit->getFotocheck()
            ], true));
    
            if (!$kit->insertar()) {
                throw new Exception('Error al insertar el kit en la base de datos');
            }
    
            return true;
        } catch (Exception $e) {
            error_log("Error en registrarKit: " . $e->getMessage());
            return false;
        }
    }
    
    private function registrarContactoEmergencia($id_conductor)
    {
        $contacto = new ContactoEmergencia();
        $contacto->setIdConductor($id_conductor);
        $contacto->setNombres($_POST['emergencia_nombre']);
        $contacto->setTelefono($_POST['emergencia_telefono']);
        $contacto->setParentesco($_POST['parentesco']);

        return $contacto->insertar();
    }

    private function registrarDireccion($id_conductor)
    {
        if (empty($_POST['departamento']) || empty($_POST['provincia']) || 
            empty($_POST['distrito']) || empty($_POST['direccion'])) {
            return false;
        }

        $direccion = new DireccionConductor();
        $direccion->setIdConductor($id_conductor);
        $direccion->setDepartamento($_POST['departamento']);
        $direccion->setProvincia($_POST['provincia']);
        $direccion->setDistrito($_POST['distrito']);
        $direccion->setDireccionDetalle($_POST['direccion']);

        return $direccion->insertar();
    }

    private function registrarObservacion($id_inscripcion)
    {
        $observacion = new Observacion();
        $observacion->setIdInscripcion($id_inscripcion);
        $observacion->setDescripcion($_POST['comentarios']);

        return $observacion->insertar();
    }

  
private function enviarRespuesta($success, $message, $data = [])
{
    $response = [
        'success' => $success,
        'message' => $message,
        'data' => $data
                     
    ];
    
    if (!$success) {
        error_log("Error en la respuesta: " . $message);
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

public function buscarConductor()
{
    $nroDocumento = $_GET['nro_documento'];
    $query = "SELECT id_conductor FROM conductores WHERE nro_documento = ?";
    $stmt = $this->conectar->prepare($query);
    $stmt->bind_param("s", $nroDocumento);
    $stmt->execute();
    $stmt->bind_result($idConductor);
    $stmt->fetch();

    if ($idConductor) {
        echo json_encode(['success' => true, 'id_conductor' => $idConductor]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Conductor no encontrado']);
    }
}

    public function obtenerConductor()
    {
        try {
            $id_conductor = $_GET['id'] ?? null;
            if (!$id_conductor) {
                throw new Exception('ID de conductor no proporcionado');
            }

            $conductor = (new Conductor())->obtenerDatosConductor($id_conductor);
            if (!$conductor) {
                throw new Exception('Conductor no encontrado');
            }

            // Obtener información del vehículo
            $vehiculo = (new Vehiculo())->obtenerPlacaPorConductor($id_conductor);
            $montoDefecto = 200; // Valor por defecto si no se encuentra el tipo de vehículo
            
            if ($vehiculo && isset($vehiculo['tipo_vehiculo'])) {
                $montoDefecto = ($vehiculo['tipo_vehiculo'] === 'auto') ? 250 : 150;
            }

            $response = [
                'success' => true,
                'data' => [
                    'foto' => $conductor->getFoto(),
                    'nombre_completo' => $conductor->getNombres() . ' ' . $conductor->getApellidoPaterno() . ' ' . $conductor->getApellidoMaterno(),
                    'monto_defecto' => $montoDefecto
                ]
            ];

            header('Content-Type: application/json');
            echo json_encode($response);
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }

    public function obtenerNumeroLibre() {
        // Verificar que la solicitud sea GET
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Obtener el tipo de vehículo del parámetro GET
            $tipoVehiculo = isset($_GET['tipo']) ? $_GET['tipo'] : 'auto';
            
            // Validar que el tipo sea válido
            if (!in_array($tipoVehiculo, ['auto', 'moto'])) {
                $tipoVehiculo = 'auto';
            }
            
            // Crear instancia del modelo
            $conductorModel = new Conductor();
            
            // Obtener el número de unidad libre para el tipo específico
            $numeroLibre = $conductorModel->obtenerNumUnidadPorTipo($tipoVehiculo);
            
            // Establecer cabecera para JSON
            header('Content-Type: application/json');
            
            // Devolver el número como JSON
            echo json_encode(['numeroLibre' => $numeroLibre]);
        } else {
            // Si no es GET, devolver error
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function obtenerNumeroLibreLima() {
        // Verificar que la solicitud sea GET
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Obtener el tipo de vehículo del parámetro GET
            $tipoVehiculo = isset($_GET['tipo']) ? $_GET['tipo'] : 'auto';
            
            // Validar que el tipo sea válido
            if (!in_array($tipoVehiculo, ['auto', 'moto'])) {
                $tipoVehiculo = 'auto';
            }
            
            // Crear instancia del modelo
            $conductorModel = new Conductor();
            
            // Obtener el número de unidad libre para Lima y tipo específico
            $numeroLibre = $conductorModel->obtenerNumUnidadLimaPorTipo($tipoVehiculo);
            
            // Establecer cabecera para JSON
            header('Content-Type: application/json');
            
            // Devolver el número como JSON
            echo json_encode(['numeroLibre' => $numeroLibre]);
        } else {
            // Si no es GET, devolver error
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['error' => 'Método no permitido']);
        }
    }
}
?>