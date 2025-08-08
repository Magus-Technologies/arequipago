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

class EditarConductorController extends Controller
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
            
            $id_conductor = $_POST['id_conductor'];
            if (!$id_conductor) {
                $this->enviarRespuesta(false, 'Error al registrar el conductor.');
                return;
            }

            if (!$this->registrarConductor($id_conductor)) {
                $this->enviarRespuesta(false, 'Error al actualizar los datos del conductor.');
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

            // 4. Registrar requisitos (con archivos)
            if (!$this->registrarRequisitos($id_inscripcion, $_FILES)) { // Enviamos $_FILES con los datos de los archivos
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

    private function registrarConductor($id_conductor)
    {
        try {
            

            $conductor = new Conductor();
    
            // Validar fecha de nacimiento
            if (!isset($_POST['fechaNac']) || empty($_POST['fechaNac'])) {
                throw new Exception('La fecha de nacimiento es requerida');
            }
        
           
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) { // Verificar si la foto está presente y no tiene errores
                // Procesar la foto
                
                if (!$conductor->editarFoto($id_conductor,$_FILES['photo'])) { // Guardar la foto solo si está presente
                    throw new Exception('Error al guardar la foto del conductor'); // Error si no se pudo guardar la foto
                }
            } else {
                
                // La foto no es obligatoria, se puede continuar sin ella
                $conductor->setFoto(null);  // Establecer un valor nulo o predeterminado si no se envió la foto
            }
        
            $conductor->setIdConductor($_POST['id_conductor']);
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
            $conductor->setNumeroCodFi((string) $_POST['numerocodfi']);
            $conductor->setCategoriaLicencia($_POST['licenciaCa']);

            
            // Formatear la fecha antes de guardarla
            $fecha = date('Y-m-d', strtotime($_POST['fechaNac']));
            $conductor->setFechNac($fecha);
        
            
        
            $resultado = $conductor->editar();
            
            if (!$resultado) {
                throw new Exception('Error al insertar el conductor en la base de datos');
            }

            return $resultado;
        
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
            $tipoVehiculo = isset($_POST['tipo_vehiculo']) ? $_POST['tipo_vehiculo'] : 'auto';
            $vehiculo->setTipoVehiculo($tipoVehiculo);
            $vehiculo->setColor($_POST['color']);
    
            $id_vehiculo = $vehiculo->editar();
            
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

            $nro_unidad = isset($_POST['nro_unidad']) ? $_POST['nro_unidad'] : null;
    
            // Validar que los campos requeridos no sean nulos
            if (!$tipo_serv || !$nro_unidad) {
                throw new Exception('Faltan campos requeridos para la inscripción');
            }
    
            $inscripcion = new Inscripcion();
            $inscripcion->setIdConductor($id_conductor);
            $inscripcion->setIdVehiculo($_SESSION['id_vehiculo']);
            $inscripcion->setSetare($tipo_serv);
         
            $inscripcion->setNroUnidad($nro_unidad);
    
            $id_inscripcion = $inscripcion->editar();
            
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
    
    public function registrarRequisitos($id_inscripcion, $archivos)
    {
        // Inicializamos las variables de los archivos como null por defecto
        $recibo_servicio = null;
        $carta_desvinculacion = null;
        $revision_tecnica = null;
        $soatdoc = null;
        $seguroDoc = null;
        $tarjeta_propiedad = null;
        $licenciadoc = null;
        $docIdentidad = null;
        $docotro1 = null;
        $docotro2 = null;
        $docotro3 = null;

        // Verificamos si los archivos fueron enviados y asignamos el valor de cada uno
        if (isset($archivos['recibo_servicio']) && $archivos['recibo_servicio']['error'] == 0) {
            $recibo_servicio = $archivos['recibo_servicio'];
        }
        if (isset($archivos['carta_desvinculacion']) && $archivos['carta_desvinculacion']['error'] == 0) {
            $carta_desvinculacion = $archivos['carta_desvinculacion'];
        }
        if (isset($archivos['revision_tecnica']) && $archivos['revision_tecnica']['error'] == 0) {
            $revision_tecnica = $archivos['revision_tecnica'];
        }
        if (isset($archivos['soatdoc']) && $archivos['soatdoc']['error'] == 0) {
            $soatdoc = $archivos['soatdoc'];
        }
        if (isset($archivos['seguroDoc']) && $archivos['seguroDoc']['error'] == 0) {
            $seguroDoc = $archivos['seguroDoc'];
        }
        if (isset($archivos['tarjeta_propiedad']) && $archivos['tarjeta_propiedad']['error'] == 0) {
            $tarjeta_propiedad = $archivos['tarjeta_propiedad'];
        }
        if (isset($archivos['licenciadoc']) && $archivos['licenciadoc']['error'] == 0) {
            $licenciadoc = $archivos['licenciadoc'];
        }
        if (isset($archivos['docIdentidad']) && $archivos['docIdentidad']['error'] == 0) {
            $docIdentidad = $archivos['docIdentidad'];
        }
        if (isset($archivos['docotro1']) && $archivos['docotro1']['error'] == 0) {
            $docotro1 = $archivos['docotro1'];
        }
        if (isset($archivos['docotro2']) && $archivos['docotro2']['error'] == 0) {
            $docotro2 = $archivos['docotro2'];
        }
        if (isset($archivos['docotro3']) && $archivos['docotro3']['error'] == 0) {
            $docotro3 = $archivos['docotro3'];
        }

        $requisitoModel = new Requisito(); // Se debe usar Requisito() en lugar de requistoModel

        // Llamamos al método getRutes y guardamos los resultados en la variable $requisitos
        $requisitos = $requisitoModel->getRutes($id_inscripcion); 

        // Mostramos los datos obtenidos para depuración
        //var_dump($requisitos);

        // Ahora comparamos los archivos recibidos con los existentes
        // Función para manejar la subida de archivos

        $uploadDir = "public" . DIRECTORY_SEPARATOR . "uploadFiles" . DIRECTORY_SEPARATOR;

        if (!file_exists($uploadDir)) { // Verifica si el directorio existe
            mkdir($uploadDir, 0777, true); // Crea el directorio con permisos adecuados si no existe
        }

        function subirArchivo($file, $uploadDir, $oldFilePath) {
            if ($file !== null) {
                if ($oldFilePath !== null && file_exists($oldFilePath)) {
                    unlink($oldFilePath); // Elimina el archivo antiguo si existe y se está subiendo uno nuevo
                }
                
                $targetFile = $uploadDir . basename($file["name"]);
                $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION)); 
        
                if ($file["size"] > 0 && in_array($fileType, ['pdf', 'jpg', 'png', 'docx'])) {
                    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
                        return $targetFile;
                    }
                }
                return false; // Si hubo un error al subir el nuevo archivo
            }
            return $oldFilePath; // Si no se subió un nuevo archivo, mantén el antiguo
        }

        // Modificamos cada sección de subida de archivos para pasar la ruta del archivo antiguo
        if ($recibo_servicio !== null) {
            $recibo_servicioRuta = subirArchivo($recibo_servicio, $uploadDir, $requisitos["recibo_servicio"]);
        } else {
            $recibo_servicioRuta = $requisitos["recibo_servicio"]; // Mantiene la ruta antigua si no se sube un nuevo archivo
        }

        if ($carta_desvinculacion !== null) {
            $carta_desvinculacionRuta = subirArchivo($carta_desvinculacion, $uploadDir, $requisitos["carta_desvinculacion"]);
        } else {
            $carta_desvinculacionRuta = $requisitos["carta_desvinculacion"];
        }

        if ($revision_tecnica !== null) {
            $revision_tecnicaRuta = subirArchivo($revision_tecnica, $uploadDir, $requisitos["revision_tecnica"]);
        } else {
            $revision_tecnicaRuta = $requisitos["revision_tecnica"];
        }

        if ($soatdoc !== null) {
            $soatdocRuta = subirArchivo($soatdoc, $uploadDir, $requisitos["soatdoc"]);
        } else {
            $soatdocRuta = $requisitos["soatdoc"];
        }

        if ($seguroDoc !== null) {
            $seguroDocRuta = subirArchivo($seguroDoc, $uploadDir, $requisitos["seguroDoc"]);
        } else {
            $seguroDocRuta = $requisitos["seguroDoc"];
        }

        if ($tarjeta_propiedad !== null) {
            $tarjeta_propiedadRuta = subirArchivo($tarjeta_propiedad, $uploadDir, $requisitos["tarjeta_propiedad"]);
        } else {
            $tarjeta_propiedadRuta = $requisitos["tarjeta_propiedad"];
        }

        if ($licenciadoc !== null) {
            $licenciadocRuta = subirArchivo($licenciadoc, $uploadDir, $requisitos["licenciadoc"]);
        } else {
            $licenciadocRuta = $requisitos["licenciadoc"];
        }

        if ($docIdentidad !== null) {
            $docIdentidadRuta = subirArchivo($docIdentidad, $uploadDir, $requisitos["docIdentidad"]);
        } else {
            $docIdentidadRuta = $requisitos["docIdentidad"];
        }

        if ($docotro1 !== null) {
            $docotro1Ruta = subirArchivo($docotro1, $uploadDir, $requisitos["docotro1"]);
        } else {
            $docotro1Ruta = $requisitos["docotro1"];
        }

        if ($docotro2 !== null) {
            $docotro2Ruta = subirArchivo($docotro2, $uploadDir, $requisitos["docotro2"]);
        } else {
            $docotro2Ruta = $requisitos["docotro2"];
        }

        if ($docotro3 !== null) {
            $docotro3Ruta = subirArchivo($docotro3, $uploadDir, $requisitos["docotro3"]);
        } else {
            $docotro3Ruta = $requisitos["docotro3"];
        }

        $rutasActualizadas = [
            'recibo_servicios' => $recibo_servicioRuta,
            'carta_desvinculacion' => $carta_desvinculacionRuta,
            'revision_tecnica' => $revision_tecnicaRuta,
            'soat_doc' => $soatdocRuta,
            'seguro_doc' => $seguroDocRuta,
            'tarjeta_propiedad' => $tarjeta_propiedadRuta,
            'licencia_doc' => $licenciadocRuta,
            'doc_identidad' => $docIdentidadRuta,
            'doc_otro1' => $docotro1Ruta,
            'doc_otro2' => $docotro2Ruta,
            'doc_otro3' => $docotro3Ruta
        ];
    
        $resultado = $requisitoModel->updateRuta($id_inscripcion, $rutasActualizadas);
    
        return $resultado;


    }

    private function registrarKit($id_inscripcion)
    {
        try {
            $kit = new Kit();
            $kit->setIdInscripcion($id_inscripcion);
    
            // Procesamiento de polo (habilitarSelect)
            $polo = isset($_POST['polo']) ? $_POST['polo'] : '0';
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
    
            if (!$kit->editar($id_inscripcion)) {
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

        return $contacto->editar();
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

        return $direccion->editar();
    }

    private function registrarObservacion($id_inscripcion)
    {
        
        $observacion = new Observacion();
        $observacion->setIdInscripcion($id_inscripcion);
        $observacion->setDescripcion($_POST['comentarios']);

        return $observacion->editar($id_inscripcion);
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

            $response = [
                'success' => true,
                'data' => [
                    'foto' => $conductor->getFoto(), // Modificado: Obtener la ruta completa de 
                    'nombre_completo' => $conductor->getNombres() . ' ' . $conductor->getApellidoPaterno() . ' ' . $conductor->getApellidoMaterno()
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

    public function registrarTodoAsesor()
    {
        $id_conductor = $_POST['id_conductor'];
        if (!$id_conductor) {
            return ['status' => 'error', 'message' => 'El ID del conductor es obligatorio.'];
        }

        // Obtener la foto actual antes de cualquier modificación
        $sql = "SELECT foto FROM conductores WHERE id_conductor = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id_conductor);
        $stmt->execute();
        $stmt->bind_result($fotoActual);
        $stmt->fetch();
        $stmt->close();
        

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) { 
            try {
                // PROCESO DE GUARDADO DE LA NUEVA FOTO
                $rutaPublica = 'public/fotos/conductores/';
                
                
                if (!file_exists($rutaPublica)) {
                    if (!mkdir($rutaPublica, 0755, true)) {
                        error_log("Error al crear el directorio: " . $rutaPublica);
                    } else {
                        //var_dump("Directorio creado:", $rutaPublica);
                    }
                }

                // Generar nombre único para el archivo
                $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $nombreArchivo = uniqid('conductor_', true) . '.' . $extension;
                $rutaCompleta = $rutaPublica . $nombreArchivo;

                // Mover el archivo
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $rutaCompleta)) {
                        
                    // Guardar solo la ruta relativa en la base de datos
                    $foto = 'fotos/conductores/' . $nombreArchivo; 

                    // Si existe una foto anterior, eliminarla
                    if (!empty($fotoActual)) {
                        $rutaFotoAnterior = 'public/' . $fotoActual;
                        if (file_exists($rutaFotoAnterior)) {
                            unlink($rutaFotoAnterior);
                        }
                    }
                } else {
                    error_log("Error al mover el archivo a: " . $rutaCompleta);
                }
            } catch (Exception $e) {
                error_log("Error en editarFoto: " . $e->getMessage());
            }
        } else {
            $foto = $fotoActual; // Comentario: Si no se sube una nueva foto, se mantiene la actual
        }

        // Actualizar la tabla conductores
        $sql = "UPDATE conductores SET apellido_paterno=?, apellido_materno=?, telefono=?, correo=?, foto=? WHERE id_conductor=?";
        $stmt = $this->conectar->prepare($sql);

        // Asigna los valores de $_POST a variables
        $apellido_paterno = $_POST['apellido_paterno'];
        $apellido_materno = $_POST['apellido_materno'];
        $telefono = $_POST['telefono'];
        $correo = $_POST['correo'];
    
        // Ahora llama a bind_param usando las variables
        $stmt->bind_param("sssssi", $apellido_paterno, $apellido_materno, $telefono, $correo, $foto, $id_conductor);
        $stmt->execute();
        
        // Obtener id_inscripcion
        $sql = "SELECT id_inscripcion FROM inscripciones WHERE id_conductor = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id_conductor);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $id_inscripcion = $row['id_inscripcion'] ?? null;
        
        if (!$id_inscripcion) {
            return ['status' => 'error', 'message' => 'No se encontró inscripción asociada.'];
        }
        
        // Verificar si existe una observación para la inscripción
        $sqlCheck = "SELECT COUNT(*) FROM observaciones WHERE id_inscripcion=?"; // Verificar si hay observación existente
        $stmtCheck = $this->conectar->prepare($sqlCheck); // Preparar la consulta
        $stmtCheck->bind_param("i", $id_inscripcion); // Asignar el parámetro
        $stmtCheck->execute(); // Ejecutar la consulta
        $stmtCheck->bind_result($count); // Obtener el resultado
        $stmtCheck->fetch(); // Fetch para obtener el valor
        $stmtCheck->close(); // Cerrar la consulta

        if ($count > 0) { // Si ya existe una observación, actualizarla
            $sql = "UPDATE observaciones SET descripcion=? WHERE id_inscripcion=?"; // Consulta de actualización
            $stmt = $this->conectar->prepare($sql); // Preparar la consulta
            $stmt->bind_param("si", $_POST['observacion'], $id_inscripcion); // Asignar los valores
            $stmt->execute(); // Ejecutar la consulta
            $stmt->close(); // Cerrar la consulta
        } else { // Si no existe una observación, insertar una nueva
            $sqlInsert = "INSERT INTO observaciones (id_inscripcion, descripcion) VALUES (?, ?)"; // Consulta de inserción
            $stmtInsert = $this->conectar->prepare($sqlInsert); // Preparar la consulta
            $stmtInsert->bind_param("is", $id_inscripcion, $_POST['observacion']); // Asignar los valores
            $stmtInsert->execute(); // Ejecutar la consulta
            $stmtInsert->close(); // Cerrar la consulta
        }
        
        // Actualizar dirección
        $sql = "UPDATE direccion_conductor SET departamento=?, provincia=?, distrito=?, direccion_detalle=? WHERE id_conductor=?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("ssssi", $_POST['departamento'], $_POST['provincia'], $_POST['distrito'], $_POST['direccion'], $id_conductor);
        $stmt->execute();
        
        // Actualizar vehículo
        $sql = "UPDATE vehiculos SET placa=?, marca=?, modelo=?, anio=?, condicion=?, vehiculo_flota=?, fech_soat=?, fech_seguro=?, color=? WHERE id_conductor=?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("sssisssssi", $_POST['n_placa'], $_POST['marca'], $_POST['modelo'], $_POST['anio'], $_POST['tipo_condicion'], $_POST['vehicle_flota'], $_POST['fechSoat'], $_POST['fechSeguro'], $_POST['color'], $id_conductor);
        $stmt->execute();
        
        // Actualizar kits
        $sql = "UPDATE kits SET logo_yango=?, fotocheck=?, polo=?, talla=?, logo_aqpgo=?, casquete=? WHERE id_inscripcion=?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("iiisiii", $_POST['logo_yango'], $_POST['fotocheck'], $_POST['polo'], $_POST['talla'], $_POST['logo_aqp'], $_POST['casquete'], $id_inscripcion);
        $stmt->execute();
        
        

        // Actualizar requisitos (archivos)
        $requisitos = ['recibo_servicios', 'carta_desvinculacion', 'revision_tecnica', 'soat_doc', 'seguro_doc', 'tarjeta_propiedad', 'licencia_doc', 'doc_identidad', 'doc_otro1', 'doc_otro2', 'doc_otro3'];
        
        $updates = [];
        foreach ($requisitos as $campo) {
            if (isset($_FILES[$campo])) { // Cambio en la extracción desde $_FILES directamente
                $rutaArchivo = $this->subirArchivoEditar($_FILES[$campo], $id_conductor, $campo); // Cambio de función subirArchivo a subirArchivoEditar
                $updates[] = "$campo='$rutaArchivo'";
            }
        }
        if (!empty($updates)) {
            $sql = "UPDATE requisitos SET " . implode(", ", $updates) . " WHERE id_inscripcion=?";
            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) { // Depuración de errores
                die("Error en la consulta SQL: " . $this->conectar->error);
            }

            $stmt->bind_param("i", $id_inscripcion);
            $stmt->execute();
        }
        
        return ['status' => 'success', 'message' => 'Datos actualizados correctamente.'];
    }

    private function subirArchivoEditar($file, $id_conductor, $campo) // Cambio de nombre de función a subirArchivoEditar
    {
        if (!$file || $file["size"] == 0) return null;
        
        $uploadDir = "public" . DIRECTORY_SEPARATOR . "uploadFiles" . DIRECTORY_SEPARATOR;
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Obtener ruta del archivo anterior desde la BD
        $sql = "SELECT $campo FROM requisitos WHERE id_inscripcion = (SELECT id_inscripcion FROM inscripciones WHERE id_conductor = ? LIMIT 1)";

        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id_conductor);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $oldFilePath = $row[$campo] ?? null;
        
        // Verificar si el archivo previo debe ser eliminado
        if ($oldFilePath && file_exists($oldFilePath)) {
            $fileModTime = filemtime($oldFilePath);
            $limiteFecha = strtotime("2025-03-14");
            if ($fileModTime >= $limiteFecha) {
                unlink($oldFilePath); // Eliminar solo si fue subido después del 14 de marzo de 2025
            }
        }
        
        $fileExt = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $newFileName = $id_conductor . "_" . $campo . "_" . time() . "." . $fileExt; // Generar nombre único

        $targetFile = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newFileName; // ✅ Asegurar solo una barra separadora

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return str_replace("\\", "/", $targetFile); // Convertir cualquier barra invertida a barra normal
        }
        return null;
    }

}
?>