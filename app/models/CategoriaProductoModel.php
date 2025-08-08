<?php

class CategoriaProductoModel {
    private $conectar;

    public function __construct() {
        $this->conectar = (new Conexion())->getConexion(); // Conexión a la base de datos
    }

    public function guardarCategoriaProducto($categoriaProducto) {
        $sql = "INSERT INTO categoria_producto (nombre) VALUES (?)"; // Consulta SQL
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("s", $categoriaProducto); // Vincular parámetro
        return $stmt->execute(); // Ejecutar la consulta
    }

    public function getUltimoIdInsertado() {
        return $this->conectar->insert_id; // Obtener el último ID insertado
    }

       
    public function obtenerCategoriasProducto() {
        $sql = "SELECT idcategoria_producto, nombre FROM categoria_producto";
        $stmt = $this->conectar->prepare($sql);
            
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $categorias = $result->fetch_all(MYSQLI_ASSOC);
            return $categorias;
        } else {
            return [];
        }
    }
    
    public function getCategoriesforId($idCategoria)
    {
        $sql = "SELECT * FROM categoria_producto WHERE idcategoria_producto = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $idCategoria);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc(); // Retorna un array con los datos
    }

    public function verificarCambioCategorie($idProducto, $nuevaCategoria) {
        // ✅ NUEVO - Consulta la categoría actual en la base de datos
        $query = "SELECT categoria FROM productosv2 WHERE idproductosv2 = ?";
        $stmt = $this->conectar->prepare($query); // ✅ MODIFICADO - Usamos conectar en lugar de db
        $stmt->bind_param("i", $idProducto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();

        if ($fila) {
            return $fila['categoria'] === $nuevaCategoria; // ✅ NUEVO - Compara las categorías
        }
        return false; // ✅ NUEVO - Si no encuentra el producto, devuelve falso
    }
    
}
