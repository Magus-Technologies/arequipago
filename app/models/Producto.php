<?php
class Producto
{
    private $id_producto;
    private $nombre;
    private $cantidad;
    private $razon_social;
    private $ruc;
    private $codigo;
    private $tipo_producto;
    private $categoria;
    private $conectar;

    /**
     * Producto constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    // Getter y Setter para cada propiedad
    public function getIdProducto() { return $this->id_producto; }
    public function setIdProducto($id_producto) { $this->id_producto = $id_producto; }

    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }

    public function getCantidad() { return $this->cantidad; }
    public function setCantidad($cantidad) { $this->cantidad = $cantidad; }

    public function getRazonSocial() { return $this->razon_social; }
    public function setRazonSocial($razon_social) { $this->razon_social = $razon_social; }

    public function getRuc() { return $this->ruc; }
    public function setRuc($ruc) { $this->ruc = $ruc; }

    public function getCodigo() { return $this->codigo; }
    public function setCodigo($codigo) { $this->codigo = $codigo; }

    public function getTipoProducto() { return $this->tipo_producto; }
    public function setTipoProducto($tipo_producto) { $this->tipo_producto = $tipo_producto; }

    public function getCategoria() { return $this->categoria; }
    public function setCategoria($categoria) { $this->categoria = $categoria; }

    /**
     * Obtener todos los productos de la base de datos
     */
    public function obtenerProductos()
    {
        $sql = "SELECT * FROM productos WHERE estado = '1' ORDER BY id_producto DESC";
        return $this->conectar->query($sql); // Devuelve un array de productos
    }


    /*

    
    public function obtenerDatos()
    {
        $sql = "SELECT * FROM productos WHERE id_producto = '$this->id_producto'";
        $fila = $this->conectar->get_Row($sql);
        $this->nombre = $fila['nombre'];
        $this->cantidad = $fila['cantidad'];
        $this->razon_social = $fila['razon_social'];
        $this->ruc = $fila['ruc'];
        $this->codigo = $fila['codigo'];
        $this->tipo_producto = $fila['tipo_producto'];
        $this->categoria = $fila['categoria'];
    }

    
    public function BuscarProductos($term)
    {
        $sql = "SELECT * FROM productos 
                WHERE nombre LIKE '%$term%'
                ORDER BY nombre ASC";
        return $this->conectar->get_Cursor($sql); // Devuelve un cursor de productos
    }*/
}
