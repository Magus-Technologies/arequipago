<?php

class ProductoVenta
{
    private $id_producto;
    private $id_venta;
    private $cantidad;
    private $precio;
    private $costo;
    private $conectar;
    private $precio_usado;
    private $descripcion;


    private $sql;
    private $sql_error;
    /**
     * ProductoVenta constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @param mixed $sql
     */
    public function setSql($sql): void
    {
        $this->sql = $sql;
    }

    /**
     * @return mixed
     */
    public function getSqlError()
    {
        return $this->sql_error;
    }

    /**
     * @param mixed $sql_error
     */
    public function setSqlError($sql_error): void
    {
        $this->sql_error = $sql_error;
    }

    /**
     * @return mixed
     */
    public function getIdProducto()
    {
        return $this->id_producto;
    }

    /**
     * @param mixed $id_producto
     */
    public function setIdProducto($id_producto)
    {
        // Limpia el valor de entrada
        $this->id_producto = trim($id_producto);
        
        // Si es un código alfanumérico, asegúrate de que esté en el formato correcto
        if (!is_numeric($this->id_producto)) {
            $this->id_producto = $this->conectar->real_escape_string($this->id_producto);
        }
    }

    /**
     * @return mixed
     */
    public function getIdVenta()
    {
        return $this->id_venta;
    }

    /**
     * @param mixed $id_venta
     */
    public function setIdVenta($id_venta)
    {
        $this->id_venta = $id_venta;
    }

    /**
     * @return mixed
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * @param mixed $cantidad
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }

    /**
     * @return mixed
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * @param mixed $precio
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }
    /**
     * @return mixed
     */
    public function getPrecioUsado()
    {
        return $this->precio_usado;
    }

    /**
     * @param mixed $precio
     */
    public function setPrecioUsado($precio_usado)
    {
        $this->precio_usado = $precio_usado;
    }

    /**
     * @return mixed
     */
    public function getCosto()
    {
        return $this->costo;
    }

    /**
     * @param mixed $costo
     */
    public function setCosto($costo)
    {
        $this->costo = $costo;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $this->conectar->real_escape_string($descripcion);
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function insertar()
    {
        // Agregar logging
        error_log("=== Inicio insertar() ProductoVenta ===");
        error_log("ID Producto recibido: " . $this->id_producto);
        
        $idProductoReal = null;
        
        // Verificar si es un ID numérico o un código
        if (is_numeric($this->id_producto)) {
            $sql = "SELECT idproductosv2 FROM productosv2 WHERE idproductosv2 = {$this->id_producto}";
            error_log("Buscando por ID numérico: " . $sql);
            $result = $this->conectar->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $idProductoReal = $this->id_producto;
                error_log("Producto encontrado por ID numérico");
            }
        }
        
        // Si no se encontró como ID numérico o no es numérico, buscar por código
        if ($idProductoReal === null) {
            $codigo = $this->conectar->real_escape_string($this->id_producto);
            $sql = "SELECT idproductosv2 FROM productosv2 WHERE codigo = '{$codigo}' OR codigo_barra = '{$codigo}'";
            error_log("Buscando por código: " . $sql);
            $result = $this->conectar->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $idProductoReal = $row['idproductosv2'];
                error_log("Producto encontrado por código");
            }
        }
        
        // Si no se encontró el producto, devolver error
        if ($idProductoReal === null) {
            $this->sql_error = "No se encontró el producto con ID/código: " . $this->id_producto;
            error_log("Error: " . $this->sql_error);
            return false;
        }
        
        error_log("ID Producto real: " . $idProductoReal);
        error_log("ID Venta: " . $this->id_venta);
        error_log("Cantidad: " . $this->cantidad);
        error_log("Precio: " . $this->precio);
        error_log("Costo: " . $this->costo);
        error_log("Precio usado: " . $this->precio_usado);

        $descripcionEscapada = $this->descripcion ? "'" . $this->conectar->real_escape_string($this->descripcion) . "'" : 'NULL'; // 
        
        // Ahora insertar usando el ID numérico real
        $sql = "INSERT INTO productos_ventas (id_producto, id_venta, cantidad, precio, costo, precio_usado, descripcion) 
        VALUES (
            {$idProductoReal}, 
            {$this->id_venta}, 
            {$this->cantidad}, 
            " . ($this->precio === '' ? 'NULL' : $this->precio) . ", 
            " . ($this->costo === '' ? 'NULL' : $this->costo) . ", 
            " . ($this->precio_usado ? "'{$this->precio_usado}'" : 'NULL') . ", 
            {$descripcionEscapada} 
        )"; // <-- Se eliminó el comentario dentro del SQL para evitar errores de sintaxis

        error_log("SQL Insert: " . $sql);
        
        $this->sql = $sql;
        $result = $this->conectar->query($sql);
    
        if (!$result) {
            $this->sql_error = $this->conectar->error;
            error_log("Error en INSERT: " . $this->sql_error);
            return false;
        }
        
        error_log("INSERT exitoso");
    
        // Actualizar el stock usando el ID correcto
        $sql = "UPDATE productosv2 
                SET cantidad = cantidad - {$this->cantidad} 
                WHERE idproductosv2 = {$idProductoReal}";
        
        error_log("SQL Update stock: " . $sql);
        
        if (!$this->conectar->query($sql)) {
            $this->sql_error = "Error actualizando stock: " . $this->conectar->error;
            error_log("Error en UPDATE: " . $this->sql_error);
            return false;
        }
        
        error_log("UPDATE stock exitoso");
        error_log("=== Fin insertar() ProductoVenta ===");
    
        return true;
    }

    public function eliminar($id_venta)
    {
        $sql = "delete from productos_ventas 
        where id_venta =  '$id_venta'";
        return $this->conectar->query($sql);
    }

    public function verFilas()
    {
        $sql = "select pv.id_producto, p.descripcion, p.iscbp, pv.precio, pv.cantidad, pv.costo, p.codsunat 
        from productos_ventas as pv 
        inner join productosv2 p on pv.id_producto = p.id_producto 
        where pv.id_venta = '$this->id_venta'";
        //echo $sql;
        return $this->conectar->query($sql);
    }
}
