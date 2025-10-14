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
        // Obtener solo los departamentos habilitados desde la tabla de configuración
        $sql = "SELECT d.iddepast, d.nombre 
                FROM depast d
                INNER JOIN departamentos_habilitados dh ON d.iddepast = dh.iddepast
                WHERE dh.habilitado = 1
                ORDER BY d.nombre ASC";
        $stmt  = $this->conectar->prepare($sql);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $departments = $result->fetch_All(MYSQLI_ASSOC);
            return $departments;    
        } else {
            return [];
        }
    }

    // Método para obtener TODOS los departamentos (para uso futuro)
    public function obtenerTodosDepartamentos(){
        $sql = "SELECT iddepast, nombre FROM depast ORDER BY nombre ASC";
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