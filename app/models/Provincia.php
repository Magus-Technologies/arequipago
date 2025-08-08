<?php


class Provincia {

    private $iddprovincias;
    private $nombre;
    private $conectar;

    public function __construct() {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function obtenerProvincias($iddepartamento){
        $sql="SELECT idprovincet, nombre FROM provincet WHERE iddepast = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i",$iddepartamento);

        if($stmt->execute()){
            $result =  $stmt->get_result();
            $provincias = $result->fetch_all(MYSQLI_ASSOC);
            return $provincias;
        } else {
            return [];
        }
    } 

}

