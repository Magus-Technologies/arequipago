<?php
class VentaServicio
{
    private $idventa;
    private $iditem;
    private $descripcion;
    private $monto;
    private $cantidad;
    private $codsunat;
    private $conectar;
    private $sql;
    private $sql_error;
    /**
     * VentaServicio constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getIdventa()
    {
        return $this->idventa;
    }

    /**
     * @param mixed $idventa
     */
    public function setIdventa($idventa)
    {
        $this->idventa = $idventa;
    }

    /**
     * @return mixed
     */
    public function getIditem()
    {
        return $this->iditem;
    }

    /**
     * @param mixed $iditem
     */
    public function setIditem($iditem)
    {
        $this->iditem = $iditem;
    }

    /**
     * @return mixed
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param mixed $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    /**
     * @return mixed
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * @param mixed $monto
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;
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
    public function getCodsunat()
    {
        return $this->codsunat;
    }

    /**
     * @param mixed $codsunat
     */
    public function setCodsunat($codsunat)
    {
        $this->codsunat = $codsunat;
    }
    // Agregar a la clase Cliente, ProductoVenta, VentaServicio, etc.
    public function getSql()
    {
        return $this->sql;
    }
    
    public function setSql($sql)
    {
        $this->sql = $sql;
    }
    
    public function getSqlError()
    {
        return $this->sql_error;
    }
    
    public function setSqlError($sql_error)
    {
        $this->sql_error = $sql_error;
    }
    public function insertar()
    {
        $this->sql = "insert into ventas_servicios 
        values ('$this->idventa', '$this->iditem', '$this->descripcion', '$this->monto', '$this->cantidad', '$this->codsunat')";
        
        $result = $this->conectar->query($this->sql);
        
        if (!$result) {
            $this->sql_error = $this->conectar->error;
        }
        
        return $result;
    }
    public function editar($idventa)
    {
        $this->sql = "insert into ventas_servicios 
        values ('$idventa', '$this->iditem', '$this->descripcion', '$this->monto', '$this->cantidad', '$this->codsunat')";
        //echo $sql;
        $result= $this->conectar->query($this->sql);
        if (!$result) {
            $this->sql_error = $this->conectar->error;
        }
        return $result;
    }
 /*    public function ()
    {
        $sql = "insert into ventas_servicios 
        values ('$this->idventa', '$this->iditem', '$this->descripcion', '$this->monto', '$this->cantidad', '$this->codsunat')";
        //echo $sql;
        return $this->conectar->query($sql);
    } */

    public function obtenerId()
    {
        $sql = "select ifnull(max(id_item) + 1, 1) as codigo 
            from ventas_servicios
            where id_venta = '$this->idventa'";
        $this->iditem = $this->conectar->get_valor_query($sql, 'codigo');
    }

    public function eliminar($idventa)
    {
        $this->sql = "delete from ventas_servicios 
        where id_venta =  '$idventa' ";
        $result = $this->conectar->query($this->sql);
        if (!$result) {
            $this-> sql_error = $this->conectar->error;
        
        }
        return $result;
    }

    public function verFilas()
    {
        $sql = "select vs.id_item, vs.descripcion, vs.monto as precio, vs.monto, vs.cantidad, vs.codsunat 
        from ventas_servicios as vs 
        inner join ventas v on vs.id_venta = v.id_venta
        where v.id_venta = '$this->idventa'";
        return $this->conectar->get_Cursor($sql);
    }


}