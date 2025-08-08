<?php

class CaracteristicaProducto {
    private $conectar;

    public function __construct() {
        $this->conectar = (new Conexion())->getConexion(); // Conexión a la base de datos
    }

    public function insertarCaracteristica($caracteristica) { // Nuevo método para insertar características
        try {

            $sql = "INSERT INTO caracteristicas_producto (idproductosv2, nombre_caracteristicas, valor_caracteristica) 
                    VALUES (?, ?, ?)"; // SQL para insertar una característica
            $stmt = $this->conectar->prepare($sql); // Preparar la consulta
            $stmt->bind_param(
                "iss", 
                $caracteristica['idproductosv2'], 
                $caracteristica['nombre_caracteristica'], 
                $caracteristica['valor_caracteristica']
            ); // Asignar valores a los parámetros
            if (!$stmt->execute()) {
                throw new Exception("Error al insertar característica: " . $stmt->error); // Manejo de errores
            }
        } catch (Exception $e) {
            error_log("Error en CaracteristicaProducto::insertarCaracteristica(): " . $e->getMessage()); // Loguear errores
            throw $e; // Re-lanzar la excepción
        }
    }

    public function obtenerCaracteristicas($idproductosv2) {
        $caracteristicas = [];

        try {
            // Consulta SQL para obtener las características del producto
            $sql = "SELECT idcaracteristica, idproductosv2, nombre_caracteristicas, valor_caracteristica 
                    FROM caracteristicas_producto 
                    WHERE idproductosv2 = ?";
            $stmt = $this->conectar->prepare($sql); 
            $stmt->bind_param("i", $idproductosv2);// Vincular el parámetro
            $stmt->execute(); 
            $stmt->bind_result($idcaracteristica, $idproductosv2, $nombre_caracteristicas, $valor_caracteristica); // <--- Cambio
            while ($stmt->fetch()) { // <--- Cambio
                $caracteristicas[] = [
                    'idcaracteristica' => $idcaracteristica,
                    'idproductosv2' => $idproductosv2,
                    'nombre_caracteristicas' => $nombre_caracteristicas,
                    'valor_caracteristica' => $valor_caracteristica,
                ];
            }

            $stmt->close(); // Cerramos el statement
        } catch (Exception $e) {
            // Manejo de errores
            error_log("Error en obtenerCaracteristicas: " . $e->getMessage());
        }

        return $caracteristicas;
    }

    public function guardarCaracteristicasMasivas(array $caracteristicas)
    {
        $query = "INSERT INTO caracteristicas_producto (idproductosv2, nombre_caracteristicas, valor_caracteristica) VALUES (?, ?, ?)";
        $stmt = $this->conectar->prepare($query);

        foreach ($caracteristicas as $caracteristica) {
            $stmt->bind_param(
                "iss",
                $caracteristica['idproductosv2'],
                $caracteristica['nombre_caracteristicas'],
                $caracteristica['valor_caracteristica']
            );
            $stmt->execute();
        }

        $stmt->close();
    }

    public function actualizarCaracteristica($caracteristica)
    {
        try {
           
            $sql = "UPDATE caracteristicas_producto SET 
                    valor_caracteristica = ? 
                    WHERE idcaracteristica = ?";
                    
            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->conectar->error);
            }
            
            $stmt->bind_param(
                'si',
                $caracteristica['valor_caracteristica'],
                $caracteristica['idcaracteristica']
            );
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error en CaracteristicaProducto::actualizarCaracteristica(): " . $e->getMessage());
            return false;
        }
    }

    public function eliminarCaracteristicasPorProducto($idProducto) {
        try {
            $sql = "DELETE FROM caracteristicas_producto WHERE idproductosv2 = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $idProducto);
    
            if (!$stmt->execute()) {
                throw new Exception("Error al eliminar características: " . $stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error en CaracteristicaProducto::eliminarCaracteristicasPorProducto(): " . $e->getMessage());
            throw $e;
        }
    }

}