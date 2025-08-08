<?php



class Departamento
{

    private $iddepartamentos;
    private $nombre;
    private $conectar;

    public function __construct() {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function obtenerDepartamentos(){
        $sql = "SELECT iddepast, nombre FROM depast";
        $stmt  = $this->conectar->prepare($sql);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $departments = $result->fetch_All(MYSQLI_ASSOC);
            return $departments;    
        } else {
            return [];
        }

        

        
    }
  
}