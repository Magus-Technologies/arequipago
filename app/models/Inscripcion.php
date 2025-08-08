<?php

class Inscripcion
{
    private $id_inscripcion;
    private $id_conductor;
    private $id_vehiculo;
    private $setare;  // Cambiado de tipo_servicio a setare
    private $fecha_inscripcion;
    private $nro_unidad;
    private $conectar;

    /**
     * Inscripcion constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function getSetare() {
        return $this->setare;
    }

    public function setSetare($setare)
    {
        $this->setare = $setare;
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
    public function getIdConductor()
    {
        return $this->id_conductor;
    }

    /**
     * @param mixed $id_conductor
     */
    public function setIdConductor($id_conductor)
    {
        $this->id_conductor = $id_conductor;
    }

    /**
     * @return mixed
     */
    public function getIdVehiculo()
    {
        return $this->id_vehiculo;
    }

    /**
     * @param mixed $id_vehiculo
     */
    public function setIdVehiculo($id_vehiculo)
    {
        $this->id_vehiculo = $id_vehiculo;
    }

    /**
     * @return mixed
     */
    public function getFechaInscripcion()
    {
        return $this->fecha_inscripcion;
    }

    /**
     * @param mixed $fecha_inscripcion
     */
    public function setFechaInscripcion($fecha_inscripcion)
    {
        $this->fecha_inscripcion = $fecha_inscripcion;
    }

    /**
     * @return mixed
     */
    public function getNroUnidad()
    {
        return $this->nro_unidad;
    }

    /**
     * @param mixed $nro_unidad
     */
    public function setNroUnidad($nro_unidad)
    {
        $this->nro_unidad = $nro_unidad;
    }

    public function obtenerSetarePorConductor($id_conductor) {
        try {
            $sql = "SELECT setare 
                    FROM inscripciones 
                    WHERE id_conductor = ?";
            
            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $this->conectar->error);
            }
    
            $stmt->bind_param("i", $id_conductor);
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando la consulta: " . $stmt->error);
            }
    
            $result = $stmt->get_result();
            $datos = $result->fetch_assoc();
            
            $stmt->close();
            return $datos ? $datos['setare'] : null;
            
        } catch (Exception $e) {
            error_log("Error en Inscripcion::obtenerSetarePorConductor(): " . $e->getMessage());
            return false;
        }
    }

    public function insertar()
    {
        try {
            $sql = "INSERT INTO inscripciones 
                    (id_conductor, id_vehiculo, setare, fecha_inscripcion, nro_unidad) 
                    VALUES 
                    (?, ?, ?, ?, ?)";
    
            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
               
                return false;
            }
    
            
            $stmt->bind_param("iisss", 
                $this->id_conductor,
                $this->id_vehiculo,
                $this->setare,
                $this->fecha_inscripcion,
                $this->nro_unidad
            );
    
            if (!$stmt->execute()) {
                error_log("Error ejecutando la consulta: " . $stmt->error);
                
                return false;
            }
    
            $id = $stmt->insert_id;
           
            $stmt->close();
            return $id;
    
        } catch (Exception $e) {
            error_log("Error en Inscripcion::insertar(): " . $e->getMessage());
                ($e->getMessage()); 
            return false;
        }
    }

    public function modificar()
    {
        try {
            $sql = "UPDATE inscripciones 
                    SET id_conductor = ?, id_vehiculo = ?, setare = ?, fecha_inscripcion = ?, nro_unidad = ?
                    WHERE id_inscripcion = ?";
            
            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando la consulta: " . $this->conectar->error);
                return false;
            }
    
            $stmt->bind_param("iisssi", 
                $this->id_conductor,
                $this->id_vehiculo,
                $this->setare,
                $this->fecha_inscripcion,
                $this->nro_unidad,
                $this->id_inscripcion
            );
    
            if (!$stmt->execute()) {
                error_log("Error ejecutando la consulta: " . $stmt->error);
                return false;
            }
    
            $stmt->close();
            return true;
    
        } catch (Exception $e) {
            error_log("Error en Inscripcion::modificar(): " . $e->getMessage());
            return false;
        }
    }

    public function obtenerDatos()
    {
        try {
            $sql = "SELECT * FROM inscripciones WHERE id_inscripcion = ?";
            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando la consulta: " . $this->conectar->error);
                return false;
            }
    
            $stmt->bind_param("i", $this->id_inscripcion);
    
            if (!$stmt->execute()) {
                error_log("Error ejecutando la consulta: " . $stmt->error);
                return false;
            }
    
            $result = $stmt->get_result();
            $fila = $result->fetch_assoc();
    
            if ($fila) {
                $this->id_conductor = $fila['id_conductor'];
                $this->id_vehiculo = $fila['id_vehiculo'];
                $this->setare = $fila['setare'];
                $this->fecha_inscripcion = $fila['fecha_inscripcion'];
                $this->nro_unidad = $fila['nro_unidad'];
            }
    
            $stmt->close();
            return true;
    
        } catch (Exception $e) {
            error_log("Error en Inscripcion::obtenerDatos(): " . $e->getMessage());
            return false;
        }
    }

    public function verFilas()
    {
        $sql = "SELECT * FROM inscripciones ORDER BY id_inscripcion DESC";
        return $this->conectar->query($sql);
    }

    public function verFilasId($id)
    {
        try {
            $sql = "SELECT * FROM inscripciones WHERE id_inscripcion = ?";
            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando la consulta: " . $this->conectar->error);
                return false;
            }
    
            $stmt->bind_param("i", $id);
    
            if (!$stmt->execute()) {
                error_log("Error ejecutando la consulta: " . $stmt->error);
                return false;
            }
    
            $result = $stmt->get_result();
            $fila = $result->fetch_assoc();
    
            $stmt->close();
            return $fila;
    
        } catch (Exception $e) {
            error_log("Error en Inscripcion::verFilasId(): " . $e->getMessage());
            return false;
        }
    }

    public function buscarInscripciones($term)
    {
        try {
            $sql = "SELECT * FROM inscripciones 
                    WHERE setare LIKE ?
                    ORDER BY id_inscripcion ASC";
            
            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando la consulta: " . $this->conectar->error);
                return false;
            }
    
            $termLike = "%$term%";
            $stmt->bind_param("s", $termLike);
    
            if (!$stmt->execute()) {
                error_log("Error ejecutando la consulta: " . $stmt->error);
                return false;
            }
    
            $result = $stmt->get_result();
            $inscripciones = [];
    
            while ($fila = $result->fetch_assoc()) {
                $inscripciones[] = $fila;
            }
    
            $stmt->close();
            return $inscripciones;
    
        } catch (Exception $e) {
            error_log("Error en Inscripcion::buscarInscripciones(): " . $e->getMessage());
            return false;
        }
    }

    public function obtenerInscripcionPorConductor($idConductor) {
        $sql = "
            SELECT id_inscripcion, id_vehiculo, setare, fecha_inscripcion, nro_unidad
            FROM inscripciones
            WHERE id_conductor = ?
        ";
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            die('Error al preparar la consulta inscripcion: ' . $this->conectar->error);
        }
        
        $stmt->bind_param('i', $idConductor);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc() ?: []; // Devuelve todos los datos o un array vacío si no hay resultados
    }
    
    public function editar()
    {
        try {
            $sql = "UPDATE inscripciones SET 
                   id_vehiculo = ?, setare = ?, nro_unidad = ?
                    WHERE id_conductor = ?";

            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando la consulta: " . $this->conectar->error);
                return false;
            }

            $stmt->bind_param("issi", 
                $this->id_vehiculo,
                $this->setare,
                $this->nro_unidad,
                $this->id_conductor
            );

            if (!$stmt->execute()) {
                error_log("Error ejecutando la consulta: " . $stmt->error);
                return false;
            }

            $stmt->close();
            return $this->id_conductor;

        } catch (Exception $e) {
            error_log("Error en Inscripcion::editar(): " . $e->getMessage());
            return false;
        }
    }
}
?>