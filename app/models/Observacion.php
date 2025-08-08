<?php

class Observacion
{
    private $id_observacion;
    private $id_inscripcion;
    private $descripcion;
    private $conectar;

    /**
     * Observacion constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getIdObservacion()
    {
        return $this->id_observacion;
    }

    /**
     * @param mixed $id_observacion
     */
    public function setIdObservacion($id_observacion)
    {
        $this->id_observacion = $id_observacion;
    }

    /**
     * @return mixed
     */
    public function getIdInscripcion()
    {
        return $this->id_inscripcion;
    }

    /**
     * @param mixed $id_inscripcion
     */
    public function setIdInscripcion($id_inscripcion)
    {
        $this->id_inscripcion = $id_inscripcion;
    }

    /**
     * @return mixed
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param mixed $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    public function insertar()
    {
        $sql = "INSERT INTO observaciones (id_inscripcion, descripcion) 
                VALUES ('$this->id_inscripcion', '$this->descripcion')";
        
        return $this->conectar->query($sql);
    }

    public function modificar()
    {
        $sql = "UPDATE observaciones 
                SET id_inscripcion = '$this->id_inscripcion', descripcion = '$this->descripcion' 
                WHERE id_observacion = '$this->id_observacion'";
        
        return $this->conectar->query($sql);
    }

    public function obtenerDatos()
    {
        $sql = "SELECT * FROM observaciones WHERE id_observacion = '$this->id_observacion'";
        $fila = $this->conectar->get_Row($sql);
        $this->id_inscripcion = $fila['id_inscripcion'];
        $this->descripcion = $fila['descripcion'];
    }

    public function verFilas()
    {
        $sql = "SELECT * FROM observaciones ORDER BY id_observacion DESC";
        return $this->conectar->query($sql);
    }

    public function verFilasId($id)
    {
        $sql = "SELECT * FROM observaciones WHERE id_observacion = '$id'";
        return $this->conectar->query($sql)->fetch_assoc();
    }

    public function obtenerObservacion($idConductor) {
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
            return null; // Si no se encuentra la inscripción, devolver null
        }

        $idInscripcion = $inscripcion['id_inscripcion'];

        // Paso 2: Obtener la descripción desde la tabla observaciones basado en el id_inscripcion
        $sqlObservacion = "
            SELECT descripcion
            FROM observaciones
            WHERE id_inscripcion = ?
        ";
        $stmtObservacion = $this->conectar->prepare($sqlObservacion);

        if (!$stmtObservacion) {
            die('Error al preparar la consulta observaciones: ' . $this->conectar->error);
        }

        $stmtObservacion->bind_param('i', $idInscripcion);
        $stmtObservacion->execute();
        $resultObservacion = $stmtObservacion->get_result();
        $observacion = $resultObservacion->fetch_assoc();

        // Retornar la descripción o null si no existe
        return $observacion['descripcion'] ?? null;
    }


    public function obtenerObservacionPorConductor($id_conductor) {
        $sql = "SELECT o.*
                FROM observaciones o
                JOIN inscripciones i ON o.id_inscripcion = i.id_inscripcion
                WHERE i.id_conductor = ?";
        
        $stmt = $this->conectar->prepare($sql);
        if (!$stmt) {
            die('Error al preparar la consulta: ' . $this->conectar->error);
        }
        
        $stmt->bind_param('i', $id_conductor);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc() ?: [];
    }

    public function editar($id_inscripcion)
    {
        try {
            $sql_inscripcion = "SELECT id_inscripcion FROM inscripciones WHERE id_conductor = ?";

            $stmt_inscripcion = $this->conectar->prepare($sql_inscripcion);

            if (!$stmt_inscripcion) {
                throw new Exception("Error preparando la consulta de inscripción: " . $this->conectar->error);
            }

            $stmt_inscripcion->bind_param("i", $id_inscripcion); // Se enlaza el id_conductor recibido
            $stmt_inscripcion->execute(); // Se ejecuta la consulta
            $stmt_inscripcion->bind_result($id_inscripcion_real); // Se obtiene el resultado
            $stmt_inscripcion->fetch(); // Se almacena el valor en $id_inscripcion_real
            $stmt_inscripcion->close(); // Se cierra la consulta
            
            if (!$id_inscripcion_real) {
                throw new Exception("No se encontró la inscripción para el id_conductor: " . $id_inscripcion); // Se maneja el error si no se encuentra el id_inscripcion
            }

            $sql = "UPDATE observaciones SET descripcion = ? WHERE id_inscripcion = ?";
            
            $stmt = $this->conectar->prepare($sql);


            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $this->conectar->error);
            }

            $stmt->bind_param("si", 
                $this->descripcion,
                $id_inscripcion_real // Se usa el id_inscripcion_real obtenido antes
            );

            $resultado = $stmt->execute();

            if (!$resultado) {
                throw new Exception("Error ejecutando la consulta: " . $stmt->error);
            }

            $stmt->close();

            return true;
        } catch (Exception $e) {
            error_log("Error en Observacion::editar: " . $e->getMessage());
            return false;
        }
    }

}


?>
