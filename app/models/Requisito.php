<?php

class Requisito
{
    private $id_requisito;
    private $id_inscripcion;
    private $recibo_servicios;
    private $carta_desvinculacion;
    private $revision_tecnica;
    private $soat_doc;
    private $seguro_doc;
    private $tarjeta_propiedad;
    private $licencia_doc;
    private $doc_identidad;
    private $doc_otro1;
    private $doc_otro2;
    private $doc_otro3;
    private $conectar;

    /**
     * Requisito constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }
    

    public function getIdRequisito() {
        return $this->id_requisito;
    }
    
    public function setIdRequisito($id_requisito) {
        $this->id_requisito = $id_requisito;
    }
    
    public function getIdInscripcion() {
        return $this->id_inscripcion;
    }
    
    public function setIdInscripcion($id_inscripcion) {
        $this->id_inscripcion = $id_inscripcion;
    }
    
    public function getReciboServicios() {
        return $this->recibo_servicios;
    }
    
    public function setReciboServicios($recibo_servicios) {
        $this->recibo_servicios = $recibo_servicios;
    }
    
    public function getCartaDesvinculacion() {
        return $this->carta_desvinculacion;
    }
    
    public function setCartaDesvinculacion($carta_desvinculacion) {
        $this->carta_desvinculacion = $carta_desvinculacion;
    }
    
    public function getRevisionTecnica() {
        return $this->revision_tecnica;
    }
    
    public function setRevisionTecnica($revision_tecnica) {
        $this->revision_tecnica = $revision_tecnica;
    }
    
    public function getSoatDoc() {
        return $this->soat_doc;
    }
    
    public function setSoatDoc($soat_doc) {
        $this->soat_doc = $soat_doc;
    }
    
    public function getSeguroDoc() {
        return $this->seguro_doc;
    }
    
    public function setSeguroDoc($seguro_doc) {
        $this->seguro_doc = $seguro_doc;
    }
    
    public function getTarjetaPropiedad() {
        return $this->tarjeta_propiedad;
    }
    
    public function setTarjetaPropiedad($tarjeta_propiedad) {
        $this->tarjeta_propiedad = $tarjeta_propiedad;
    }
    
    public function getLicenciaDoc() {
        return $this->licencia_doc;
    }
    
    public function setLicenciaDoc($licencia_doc) {
        $this->licencia_doc = $licencia_doc;
    }
    
    public function getDocIdentidad() {
        return $this->doc_identidad;
    }
    
    public function setDocIdentidad($doc_identidad) {
        $this->doc_identidad = $doc_identidad;
    }
    
    public function getDocOtro1() {
        return $this->doc_otro1;
    }
    
    public function setDocOtro1($doc_otro1) {
        $this->doc_otro1 = $doc_otro1;
    }
    
    public function getDocOtro2() {
        return $this->doc_otro2;
    }
    
    public function setDocOtro2($doc_otro2) {
        $this->doc_otro2 = $doc_otro2;
    }
    
    public function getDocOtro3() {
        return $this->doc_otro3;
    }
    
    public function setDocOtro3($doc_otro3) {
        $this->doc_otro3 = $doc_otro3;
    }

    public function insertar() 
    {
        try {
            $sql = "INSERT INTO requisitos (
                id_inscripcion, 
                recibo_servicios, 
                carta_desvinculacion, 
                revision_tecnica, 
                soat_doc, 
                seguro_doc, 
                tarjeta_propiedad, 
                licencia_doc, 
                doc_identidad, 
                doc_otro1, 
                doc_otro2, 
                doc_otro3
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $this->conectar->error);
            }

            // Asegúrate de que los valores no sean null antes de pasarlos
            $recibo_servicios = $this->recibo_servicios ?: null;
            $carta_desvinculacion = $this->carta_desvinculacion ?: null;
            $revision_tecnica = $this->revision_tecnica ?: null;
            $soat_doc = $this->soat_doc ?: null;
            $seguro_doc = $this->seguro_doc ?: null;
            $tarjeta_propiedad = $this->tarjeta_propiedad ?: null;
            $licencia_doc = $this->licencia_doc ?: null;
            $doc_identidad = $this->doc_identidad ?: null;
            $doc_otro1 = $this->doc_otro1 ?: null;
            $doc_otro2 = $this->doc_otro2 ?: null;
            $doc_otro3 = $this->doc_otro3 ?: null;

            // El primer parámetro (id_inscripcion) es un entero (i)
            $stmt->bind_param("isssssssssss", 
                $this->id_inscripcion,   // id_inscripcion (entero)
                $recibo_servicios,       // recibo_servicios (cadena)
                $carta_desvinculacion,   // carta_desvinculacion (cadena)
                $revision_tecnica,       // revision_tecnica (cadena)
                $soat_doc,               // soat_doc (cadena)
                $seguro_doc,             // seguro_doc (cadena)
                $tarjeta_propiedad,      // tarjeta_propiedad (cadena)
                $licencia_doc,           // licencia_doc (cadena)
                $doc_identidad,          // doc_identidad (cadena)
                $doc_otro1,              // doc_otro1 (cadena)
                $doc_otro2,              // doc_otro2 (cadena)
                $doc_otro3               // doc_otro3 (cadena)
            );

            // Ejecutar la consulta
            $resultado = $stmt->execute();

            if (!$resultado) {
                throw new Exception("Error ejecutando la consulta: " . $stmt->error);
            }

            $stmt->close();
            return true;

        } catch (Exception $e) {
            error_log("Error en Requisito::insertar: " . $e->getMessage());
            return false;
        }
    }




    public function modificar()
    {
        $sql = "UPDATE requisitos 
                SET id_inscripcion = '$this->id_inscripcion', recibo_servicios = '$this->recibo_servicios', carta_desvinculacion = '$this->carta_desvinculacion', 
                revision_tecnica = '$this->revision_tecnica', soat = '$this->soat', tarjeta_propiedad = '$this->tarjeta_propiedad', 
                licencia = '$this->licencia', doc_identidad = '$this->doc_identidad',
                doc1 = '$this->doc1',       -- Nuevo campo doc1
                doc2 = '$this->doc2',       -- Nuevo campo doc2
                doc3 = '$this->doc3',       -- Nuevo campo doc3
                docotro1 = '$this->docotro1',  -- Nuevo campo docotro1
                docotro2 = '$this->docotro2',  -- Nuevo campo docotro2
                docotro3 = '$this->docotro3' 
                WHERE id_requisito = '$this->id_requisito'";
        
        return $this->conectar->ejecutar_idu($sql);
    }

    public function obtenerDatos()
    {
        $sql = "SELECT * FROM requisitos WHERE id_requisito = '$this->id_requisito'";
        $fila = $this->conectar->get_Row($sql);
        $this->id_inscripcion = $fila['id_inscripcion'];
        $this->recibo_servicios = $fila['recibo_servicios'];
        $this->carta_desvinculacion = $fila['carta_desvinculacion'];
        $this->revision_tecnica = $fila['revision_tecnica'];
        $this->soat = $fila['soat'];
        $this->tarjeta_propiedad = $fila['tarjeta_propiedad'];
        $this->licencia = $fila['licencia'];
        $this->doc_identidad = $fila['doc_identidad'];
        $this->seguro_doc = $fila['seguro_doc'];
        $this->doc_otro1 = $fila['doc_otro1'];
        $this->doc_otro2 = $fila['doc_otro2'];
        $this->doc_otro3 = $fila['doc_otro3'];

    }

    public function obtenerEstadoRequisitos($idConductor) {
        // Paso 1: Obtener el id_inscripcion basado en el id_conductor
        $sqlInscripcion = "
            SELECT id_inscripcion
            FROM inscripciones
            WHERE id_conductor = ?
        ";
        $stmtInscripcion = $this->conectar->prepare($sqlInscripcion);
    
        if (!$stmtInscripcion) {
            die('Error al preparar la consulta inscripcion: ' . $this->conectar->error);
        }
    
        $stmtInscripcion->bind_param('i', $idConductor);
        $stmtInscripcion->execute();
        $resultInscripcion = $stmtInscripcion->get_result();
        $inscripcion = $resultInscripcion->fetch_assoc();
    
        if (!$inscripcion) {
            return []; // Si no se encuentra la inscripción, devolver un array vacío
        }
    
        $idInscripcion = $inscripcion['id_inscripcion'];
    
        // Paso 2: Obtener los datos de la tabla requisitos basado en el id_inscripcion
        $sqlRequisitos = "
            SELECT recibo_servicios, carta_desvinculacion, revision_tecnica, soat_doc, seguro_doc, 
                   tarjeta_propiedad, licencia_doc, doc_identidad, doc_otro1, doc_otro2, doc_otro3
            FROM requisitos
            WHERE id_inscripcion = ?
        ";
        $stmtRequisitos = $this->conectar->prepare($sqlRequisitos);
    
        if (!$stmtRequisitos) {
            die('Error al preparar la consulta requisitos: ' . $this->conectar->error);
        }
    
        $stmtRequisitos->bind_param('i', $idInscripcion);
        $stmtRequisitos->execute();
        $resultRequisitos = $stmtRequisitos->get_result();
        $requisitos = $resultRequisitos->fetch_assoc();
    
        // Si no hay requisitos registrados, devolver un array con todos los estados en 0
        if (!$requisitos) {
            return [
                'recibo_servicios' => 0,
                'carta_desvinculacion' => 0,
                'revision_tecnica' => 0,
                'soat_doc' => 0,
                'seguro_doc' => 0,
                'tarjeta_propiedad' => 0,
                'licencia_doc' => 0,
                'doc_identidad' => 0,
                'doc_otro1' => 0,
                'doc_otro2' => 0,
                'doc_otro3' => 0,
            ];
        }
    
        // Paso 3: Convertir las rutas en estados (1 si existe, 0 si no)
        $estados = [];
        foreach ($requisitos as $campo => $ruta) {
            $estados[$campo] = !empty($ruta) ? 1 : 0;
        }
    
        return $estados;
    }

    public function verFilas()
    {
        $sql = "SELECT * FROM requisitos ORDER BY id_requisito DESC";
        return $this->conectar->query($sql);
    }

    public function verFilasId($id)
    {
        $sql = "SELECT * FROM requisitos WHERE id_requisito = '$id'";
        return $this->conectar->query($sql)->fetch_assoc();
    }

    public function obtenerDatosRequisitos($idConductor)
    {
        try {
            $sql = "
                SELECT 
                    r.recibo_servicios,
                    r.carta_desvinculacion,
                    r.revision_tecnica,
                    r.soat_doc,
                    r.seguro_doc,
                    r.tarjeta_propiedad,
                    r.licencia_doc,
                    r.doc_identidad,
                    r.doc_otro1,
                    r.doc_otro2,
                    r.doc_otro3
                FROM requisitos r
                INNER JOIN inscripciones i ON r.id_inscripcion = i.id_inscripcion
                WHERE i.id_conductor = ?
            ";

            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $this->conectar->error);
            }

            $stmt->bind_param("i", $idConductor);
            $stmt->execute();
            $result = $stmt->get_result();
            

            return $result->fetch_assoc() ?: null; // Retorna un array asociativo o null

            

        } catch (Exception $e) {
            error_log("Error en Requisito::obtenerDatosRequisitos: " . $e->getMessage());
            return null;
        }
    }

    public function getRutes($id_inscripcion)
    {
        // Buscamos el id_inscripcion real de la tabla inscripciones
        $query = "SELECT id_inscripcion FROM inscripciones WHERE id_conductor = ?";
        $stmt = $this->conectar->prepare($query);
        
        // Verificamos si la preparación fue exitosa
        if ($stmt === false) {
            die('Error en la preparación de la consulta: ' . $this->conectar->error);
        }
        
        // Asociamos el parámetro
        $stmt->bind_param("i", $id_inscripcion);
        
        // Ejecutamos la consulta
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Verificamos si se encontró un id_inscripcion
        if ($result->num_rows > 0) {
            $inscripcion = $result->fetch_assoc();
            $real_id_inscripcion = $inscripcion['id_inscripcion']; // El id_inscripcion correcto

            // Ahora, con el id_inscripcion real, buscamos en la tabla requisitos
            $query_requisitos = "SELECT * FROM requisitos WHERE id_inscripcion = ?";
            $stmt_requisitos = $this->conectar->prepare($query_requisitos);

            // Verificamos si la preparación fue exitosa
            if ($stmt_requisitos === false) {
                die('Error en la preparación de la consulta: ' . $this->conectar->error);
            }
            
            // Asociamos el parámetro para la consulta de requisitos
            $stmt_requisitos->bind_param("i", $real_id_inscripcion);
            
            // Ejecutamos la consulta de requisitos
            $stmt_requisitos->execute();
            $result_requisitos = $stmt_requisitos->get_result();

            // Verificamos si encontramos los datos de los requisitos
            if ($result_requisitos->num_rows > 0) {
                $requisitos = $result_requisitos->fetch_assoc();

                // Regresamos los resultados con los datos de los archivos o null si no existen
                $archivos_requisitos = [
                    'recibo_servicio' => isset($requisitos['recibo_servicios']) ? $requisitos['recibo_servicios'] : null,
                    'carta_desvinculacion' => isset($requisitos['carta_desvinculacion']) ? $requisitos['carta_desvinculacion'] : null,
                    'revision_tecnica' => isset($requisitos['revision_tecnica']) ? $requisitos['revision_tecnica'] : null,
                    'soatdoc' => isset($requisitos['soat_doc']) ? $requisitos['soat_doc'] : null,
                    'seguroDoc' => isset($requisitos['seguro_doc']) ? $requisitos['seguro_doc'] : null,
                    'tarjeta_propiedad' => isset($requisitos['tarjeta_propiedad']) ? $requisitos['tarjeta_propiedad'] : null,
                    'licenciadoc' => isset($requisitos['licencia_doc']) ? $requisitos['licencia_doc'] : null,
                    'docIdentidad' => isset($requisitos['doc_identidad']) ? $requisitos['doc_identidad'] : null,
                    'docotro1' => isset($requisitos['doc_otro1']) ? $requisitos['doc_otro1'] : null,
                    'docotro2' => isset($requisitos['doc_otro2']) ? $requisitos['doc_otro2'] : null,
                    'docotro3' => isset($requisitos['doc_otro3']) ? $requisitos['doc_otro3'] : null,
                ];


                return $archivos_requisitos;
            } else {
                // Si no se encuentra el registro en la tabla requisitos, devolvemos los valores por defecto (null)
                return [
                    'recibo_servicio' => null,
                    'carta_desvinculacion' => null,
                    'revision_tecnica' => null,
                    'soatdoc' => null,
                    'seguroDoc' => null,
                    'tarjeta_propiedad' => null,
                    'licenciadoc' => null,
                    'docIdentidad' => null,
                    'docotro1' => null,
                    'docotro2' => null,
                    'docotro3' => null,
                ];
            }
        } else {
            // Si no se encuentra el id_conductor en la tabla inscripciones, devolvemos los valores por defecto (null)
            return [
                'recibo_servicio' => null,
                'carta_desvinculacion' => null,
                'revision_tecnica' => null,
                'soatdoc' => null,
                'seguroDoc' => null,
                'tarjeta_propiedad' => null,
                'licenciadoc' => null,
                'docIdentidad' => null,
                'docotro1' => null,
                'docotro2' => null,
                'docotro3' => null,
            ];
        }
    }

    public function updateRuta($id_conductor, $rutasActualizadas)
    {
        try {
            // First, get the real id_inscripcion from the inscripciones table
            $stmt = $this->conectar->prepare("SELECT id_inscripcion FROM inscripciones WHERE id_conductor = ?");
            $stmt->bind_param("i", $id_conductor);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception("No se encontró la inscripción para el conductor con ID: $id_conductor");
            }

            $row = $result->fetch_assoc();
            $id_inscripcion = $row['id_inscripcion'];

            // Now, update the requisitos table
            $sql = "UPDATE requisitos SET 
                    recibo_servicios = ?,
                    carta_desvinculacion = ?,
                    revision_tecnica = ?,
                    soat_doc = ?,
                    seguro_doc = ?,
                    tarjeta_propiedad = ?,
                    licencia_doc = ?,
                    doc_identidad = ?,
                    doc_otro1 = ?,
                    doc_otro2 = ?,
                    doc_otro3 = ?
                    WHERE id_inscripcion = ?";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("sssssssssssi", 
                $rutasActualizadas['recibo_servicios'],
                $rutasActualizadas['carta_desvinculacion'],
                $rutasActualizadas['revision_tecnica'],
                $rutasActualizadas['soat_doc'],
                $rutasActualizadas['seguro_doc'],
                $rutasActualizadas['tarjeta_propiedad'],
                $rutasActualizadas['licencia_doc'],
                $rutasActualizadas['doc_identidad'],
                $rutasActualizadas['doc_otro1'],
                $rutasActualizadas['doc_otro2'],
                $rutasActualizadas['doc_otro3'],
                $id_inscripcion
            );

            $resultado = $stmt->execute();

            if (!$resultado) {
                throw new Exception("Error al actualizar las rutas: " . $stmt->error);
            }

            return true;
        } catch (Exception $e) {
            error_log("Error en updateRuta: " . $e->getMessage());
            return false;
        }
    }

}
?>
