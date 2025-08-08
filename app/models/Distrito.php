<?php


class Distrito {

    private $iddistrito;
    private $nombre;
    private $conectar;

    public function __construct() {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function obtenerDistritos($idprovincia){
        $sql="SELECT iddistritot, nombre FROM distritot WHERE idprovincet = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i",$idprovincia);

        if($stmt->execute()){
            $result =  $stmt->get_result();
            $provincias = $result->fetch_all(MYSQLI_ASSOC);
            return $provincias;
        } else {
            return [];
        }
    } 

}
