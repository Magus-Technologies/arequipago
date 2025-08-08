<?php
class VentaSunat
{
    private $id_venta;
    private $hash;
    private $nombre_xml;
    private $conectar;
    private $qr_data;
    private $sql;
private $sql_error;

    /**
     * VentaSunat constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getIdVenta()
    {
        return $this->id_venta;
    }

    /**
     * @return mixed
     */
    public function getQrData()
    {
        return $this->qr_data;
    }

    /**
     * @param mixed $qr_data
     */
    public function setQrData($qr_data): void
    {
        $this->qr_data = $qr_data;
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
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return mixed
     */
    public function getNombreXml()
    {
        return $this->nombre_xml;
    }

    /**
     * @param mixed $nombre_xml
     */
    public function setNombreXml($nombre_xml)
    {
        $this->nombre_xml = $nombre_xml;
    }
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
    try {
        // Asegurar que los campos no sean null
        $id_venta = $this->id_venta ?: 0;
        $hash = $this->hash ?: '';
        $nombre_xml = $this->nombre_xml ?: '';
        $qr_data = $this->qr_data ?: '';
        
        $this->sql = "INSERT INTO ventas_sunat 
            (id_venta, hash, nombre_xml, qr_data)
            VALUES (?, ?, ?, ?)";
            
        $stmt = $this->conectar->prepare($this->sql);
        
        if (!$stmt) {
            $this->sql_error = $this->conectar->error;
            error_log("Error preparando consulta ventas_sunat: " . $this->sql_error);
            return false;
        }
        
        $stmt->bind_param("isss", $id_venta, $hash, $nombre_xml, $qr_data);
        
        $result = $stmt->execute();
        
        if (!$result) {
            $this->sql_error = $stmt->error;
            error_log("Error ejecutando consulta ventas_sunat: " . $this->sql_error);
            return false;
        }
        
        return true;
        
    } catch (Exception $e) {
        $this->sql_error = $e->getMessage();
        error_log("Excepción en ventas_sunat::insertar: " . $this->sql_error);
        return false;
    }
}

public function obtenerDatos()
{
    $this->sql = "select * 
    from ventas_sunat 
    where id_venta = '$this->id_venta'";
    
    $fila = $this->conectar->get_Row($this->sql);
    
    if (!$fila) {
        $this->sql_error = $this->conectar->error;
        error_log("Error al obtener datos de venta_sunat: " . $this->sql_error . "\nConsulta: " . $this->sql);
        return false;
    }
    
    $this->hash = $fila['hash'];
    $this->nombre_xml = $fila['nombre_xml'];
    return true;
}
}