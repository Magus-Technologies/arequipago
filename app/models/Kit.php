<?php

class Kit
{
    private $id_kit;
    private $id_inscripcion;
    private $logo_yango;
    private $fotocheck;
    private $polo;
    private $talla;
    private $logo_aqpgo;
    private $casquete;

    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    // Métodos getter y setter...
    
    public function getIdKit()
    {
        return $this->id_kit;
    }

    public function setIdKit($id_kit)
    {
        $this->id_kit = $id_kit;
    }

    public function getIdInscripcion()
    {
        return $this->id_inscripcion;
    }

    public function setIdInscripcion($id_inscripcion)
    {
        $this->id_inscripcion = $id_inscripcion;
    }

    public function getLogoYango()
    {
        return $this->logo_yango;
    }

    public function setLogo_yango($logo_yango)
    {
        $this->logo_yango = (int)$logo_yango;
    }

    public function getFotocheck()
    {
        return $this->fotocheck;
    }

    public function setFotocheck($fotocheck)
    {
        $this->fotocheck = (int)$fotocheck;
    }

    public function getPolo()
    {
        return $this->polo;
    }

    public function setPolo($polo)
    {
        $this->polo = $polo === '1' ? 1 : 0;
    }

    public function getTalla()
    {
        return $this->talla;
    }

    public function setTalla($talla)
    {
        $this->talla = $talla;
    }

    public function setLogoAqpgo($logo_aqpgo)
    {
        $this->logo_aqpgo = (int)$logo_aqpgo;
    }

    public function getLogoAqpgo()
    {
        return $this->logo_aqpgo;
    }

    public function getCasquete()
    {
        return $this->casquete;
    }

    public function setCasquete($casquete)
    {
        $this->casquete = (int)$casquete;
    }

    /**
     * Insertar los datos del kit en la base de datos.
     * 
     * @return bool
     */
    public function insertar()
    {
        try {
            $sql = "INSERT INTO kits (id_inscripcion, logo_yango, fotocheck, polo, talla, logo_aqpgo, casquete) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $this->conectar->error);
            }

            $stmt->bind_param(
                "iiissii", 
                $this->id_inscripcion,
                $this->logo_yango,
                $this->fotocheck,
                $this->polo,
                $this->talla,
                $this->logo_aqpgo,
                $this->casquete
            );

            // Log para depuración
            error_log("Valores a insertar en la base de datos: " . print_r([
                $this->id_inscripcion,
                $this->logo_yango,
                $this->fotocheck,
                $this->polo,
                $this->talla,
                $this->logo_aqpgo,
                $this->casquete
            ], true));

            $resultado = $stmt->execute();

            if (!$resultado) {
                throw new Exception("Error ejecutando la consulta: " . $stmt->error);
            }

            $stmt->close();

            return true;
        } catch (Exception $e) {
            error_log("Error en Kit::insertar: " . $e->getMessage());
            return false;
        }
    }

    public function modificar()
    {
        $sql = "UPDATE kits 
                SET id_inscripcion = '$this->id_inscripcion', logo_yango = '$this->logo_yango', fotocheck = '$this->fotocheck', 
                polo = '$this->polo', talla = '$this->talla',
                logo_aqpgo = '$this->logo_aqpgo',  -- Nuevo campo
                casquete = '$this->casquete'  -- Nuevo campo 
                WHERE id_kit = '$this->id_kit'";
        
        return $this->conectar->query($sql);
    }

    public function obtenerDatos()
    {
        $sql = "SELECT * FROM kits WHERE id_kit = '$this->id_kit'";
        $fila = $this->conectar->get_Row($sql);
        $this->id_inscripcion = $fila['id_inscripcion'];
        $this->logo_yango = $fila['logo_yango'];
        $this->fotocheck = $fila['fotocheck'];
        $this->polo = $fila['polo'];
        $this->talla = $fila['talla'];
        $this->logo_aqpgo = $fila['logo_aqpgo'];
        $this->casquete = $fila['casquete'];
    }

    public function verFilas()
    {
        $sql = "SELECT * FROM kits ORDER BY id_kit DESC";
        return $this->conectar->query($sql);
    }

    public function verFilasId($id)
    {
        $sql = "SELECT * FROM kits WHERE id_kit = '$id'";
        return $this->conectar->query($sql)->fetch_assoc();
    }

    public function obtenerKitPorConductor($id_conductor) {
        $sql = "SELECT k.*
                FROM kits k
                JOIN inscripciones i ON k.id_inscripcion = i.id_inscripcion
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
                throw new Exception("Error preparando la consulta de inscripciones: " . $this->conectar->error);
            }

            $stmt_inscripcion->bind_param("i", $id_inscripcion); // Se enlaza el id_conductor
            $stmt_inscripcion->execute();
            $stmt_inscripcion->bind_result($id_inscripcion_real);
            $stmt_inscripcion->fetch();
            $stmt_inscripcion->close();

            if (!$id_inscripcion_real) {
                throw new Exception("No se encontró una inscripción para el conductor con ID: " . $id_inscripcion);
            }

            // Ahora usamos el id_inscripcion_real para actualizar la tabla kits
            $sql = "UPDATE kits SET 
            logo_yango = ?, fotocheck = ?, polo = ?, talla = ?, logo_aqpgo = ?, casquete = ?
            WHERE id_inscripcion = ?";
            
            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $this->conectar->error);
            }

            $stmt->bind_param(
                "iiissii", 
                $this->logo_yango,
                $this->fotocheck,
                $this->polo,
                $this->talla,
                $this->logo_aqpgo,
                $this->casquete,
                $id_inscripcion_real // Se usa el id_inscripcion_real en la actualización
            );
    
            $resultado = $stmt->execute();

            if (!$resultado) {
                throw new Exception("Error ejecutando la consulta: " . $stmt->error);
            }

            $stmt->close();

            return true;
        } catch (Exception $e) {
            error_log("Error en Kit::editar: " . $e->getMessage());
            return false;
        }
    }
}
?>

