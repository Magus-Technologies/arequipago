<?php

class DireccionConductor
{
    private $id_direccion;
    private $id_conductor;
    private $departamento;
    private $provincia;
    private $distrito;
    private $direccion_detalle;
    private $conectar;

    /**
     * DireccionConductor constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getIdDireccion()
    {
        return $this->id_direccion;
    }

    /**
     * @param mixed $id_direccion
     */
    public function setIdDireccion($id_direccion)
    {
        $this->id_direccion = $id_direccion;
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
    public function getDepartamento()
    {
        return $this->departamento;
    }

    /**
     * @param mixed $departamento
     */
    public function setDepartamento($departamento)
    {
        $this->departamento = $departamento;
    }

    /**
     * @return mixed
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * @param mixed $provincia
     */
    public function setProvincia($provincia)
    {
        $this->provincia = $provincia;
    }

    /**
     * @return mixed
     */
    public function getDistrito()
    {
        return $this->distrito;
    }

    /**
     * @param mixed $distrito
     */
    public function setDistrito($distrito)
    {
        $this->distrito = $distrito;
    }

    /**
     * @return mixed
     */
    public function getDireccionDetalle()
    {
        return $this->direccion_detalle;
    }

    /**
     * @param mixed $direccion_detalle
     */
    public function setDireccionDetalle($direccion_detalle)
    {
        $this->direccion_detalle = $direccion_detalle;
    }

    public function insertar()
    {
        $sql = "INSERT INTO direccion_conductor 
                (id_conductor, departamento, provincia, distrito, direccion_detalle) 
                VALUES 
                ('$this->id_conductor', '$this->departamento', '$this->provincia', '$this->distrito', '$this->direccion_detalle')";
        
        return $this->conectar->query($sql);
    }

    public function modificar()
    {
        $sql = "UPDATE direccion_conductor 
                SET id_conductor = '$this->id_conductor', departamento = '$this->departamento', provincia = '$this->provincia', distrito = '$this->distrito', direccion_detalle = '$this->direccion_detalle'
                WHERE id_direccion = '$this->id_direccion'";
        
        return $this->conectar->query($sql);
    }

    public function obtenerId()
    {
        $sql = "SELECT IFNULL(MAX(id_direccion) + 1, 1) AS codigo 
                FROM direccion_conductor";
        $this->id_direccion = $this->conectar->get_valor_query($sql, 'codigo');
    }

    public function obtenerDatos()
    {
        $sql = "SELECT * 
                FROM direccion_conductor 
                WHERE id_direccion = '$this->id_direccion'";
        $fila = $this->conectar->get_Row($sql);
        $this->id_conductor = $fila['id_conductor'];
        $this->departamento = $fila['departamento'];
        $this->provincia = $fila['provincia'];
        $this->distrito = $fila['distrito'];
        $this->direccion_detalle = $fila['direccion_detalle'];
    }

    public function verFilas()
    {
        $sql = "SELECT * FROM direccion_conductor ORDER BY id_direccion DESC";
        return $this->conectar->query($sql);
    }

    public function verFilasId($id)
    {
        $sql = "SELECT * FROM direccion_conductor WHERE id_direccion = '$id'";
        return $this->conectar->query($sql)->fetch_assoc();
    }

    public function buscarDireccionConductor($term)
    {
        $sql = "SELECT * FROM direccion_conductor 
                WHERE departamento LIKE '%$term%' OR provincia LIKE '%$term%' 
                ORDER BY departamento ASC";
        return $this->conectar->get_Cursor($sql);
    }

    public function obtenerDireccionConductor($idConductor)
    {
        try {
            $sql = "SELECT direccion_detalle, departamento, provincia, distrito FROM direccion_conductor WHERE id_conductor = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $idConductor);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_assoc() ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function obtenerDatosDireccion($idConductor) {
        $sql = "
            SELECT 
                dc.direccion_detalle,
                dist.nombre AS distrito,
                prov.nombre AS provincia,
                dep.nombre AS departamento
            FROM direccion_conductor dc
            LEFT JOIN distritot dist ON dc.distrito = dist.iddistritot
            LEFT JOIN provincet prov ON dc.provincia = prov.idprovincet
            LEFT JOIN depast dep ON dc.departamento = dep.iddepast
            WHERE dc.id_conductor = ?
        ";
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            die('Error al preparar la consulta direccion conductor: ' . $this->conectar->error);
        }
        
        $stmt->bind_param('i', $idConductor);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc(); // Devuelve un array asociativo con los datos
    }

    public function getEditdirection($id_conductor) {
        $sql = "SELECT dc.*, d.nombre as nombre_departamento, p.nombre as nombre_provincia, di.nombre as nombre_distrito
                FROM direccion_conductor dc
                LEFT JOIN depast d ON dc.departamento = d.iddepast
                LEFT JOIN provincet p ON dc.provincia = p.idprovincet
                LEFT JOIN distritot di ON dc.distrito = di.iddistritot
                WHERE dc.id_conductor = ?";
        
        $stmt = $this->conectar->prepare($sql);
        if (!$stmt) {
            die('Error al preparar la consulta: ' . $this->conectar->error);
        }
        
        $stmt->bind_param('i', $id_conductor);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc() ?: [];
    }

    public function editar()
    {
        try {
            $sql = "UPDATE direccion_conductor SET 
                    departamento = ?, provincia = ?, distrito = ?, direccion_detalle = ?
                    WHERE id_conductor = ?";

            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $this->conectar->error);
            }

            $stmt->bind_param("ssssi", 
                $this->departamento,
                $this->provincia,
                $this->distrito,
                $this->direccion_detalle,
                $this->id_conductor
            );

            $resultado = $stmt->execute();

            if (!$resultado) {
                throw new Exception("Error ejecutando la consulta: " . $stmt->error);
            }

            $stmt->close();

            return true;
        } catch (Exception $e) {
            error_log("Error en DireccionConductor::editar: " . $e->getMessage());
            return false;
        }
    }
    
}



?>
