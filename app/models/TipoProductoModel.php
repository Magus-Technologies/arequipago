<?php
// models/TipoProductoModel.php



class TipoProductoModel {
    private $tipoVenta;
    private $conectar;

    public function __construct() {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function setTipoVenta($tipoVenta) {
        $this->tipoVenta = $tipoVenta;
    }

    public function obtenerTiposProducto() {
        $sql = "SELECT idtipo_producto, tipo_productocol FROM tipo_producto";
        $stmt = $this->conectar->prepare($sql);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $tipos = $result->fetch_All(MYSQLI_ASSOC);
            return $tipos;    
        } else {
            return [];
        }
        
    }

    public function guardarTipoProducto($tipoProducto) {
        $tipoVenta = $this->tipoVenta;
        $sql = "INSERT INTO tipo_producto (tipo_productocol, tipo_venta) VALUES (?, ?)";
        $stmt = $this->conectar->prepare($sql);
    
        if ($stmt) {
            $stmt->bind_param("ss", $tipoProducto, $tipoVenta); // El primer "s" es para tipo_productocol, el segundo "s" es para tipo_venta
            return $stmt->execute(); // Ejecutar la consulta
        }
        return false;
    }

    public function getUltimoIdInsertado() {
        return $this->conectar->insert_id;
    }

    public function obtenerTipoVentaPorTipoProducto($tipoProducto) {
    
        $sql = "SELECT tipo_venta FROM tipo_producto WHERE idtipo_producto = ?"; // Línea modificada
        $stmt = $this->conectar->prepare($sql);
        
        // Asegurarse de que el parámetro sea seguro
        $stmt->bind_param("i", $tipoProducto); // Cambié el tipo de parámetro de "s" a "i" ya que es un entero
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $row['tipo_venta']; // Devolver el tipo_venta
            }
        }
        
        return null; // Si no se encuentra, devolver null
    }

    public function getdataForId($idTipoProducto)
    {
        $sql = "SELECT * FROM tipo_producto WHERE idtipo_producto = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $idTipoProducto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc(); // Devuelve el array con los datos
    }
    
}
