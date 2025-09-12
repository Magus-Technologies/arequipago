<?php

class CuponBanner
{
    private $id;
    private $nombre_banner;
    private $ruta_imagen;
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function crear($nombre, $ruta)
    {
        try {
            $sql = "INSERT INTO cupones_banners (nombre_banner, ruta_imagen) VALUES (?, ?)";
            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }
            
            $stmt->bind_param('ss', $nombre, $ruta);
            
            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }
            
            $insertId = $stmt->insert_id;
            $stmt->close();
            
            return $insertId;
        } catch (Exception $e) {
            error_log('Error en CuponBanner::crear(): ' . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id)
    {
        try {
            $sql = "DELETE FROM cupones_banners WHERE id = ?";
            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }
            
            $stmt->bind_param('i', $id);
            
            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }
            
            $affected = $stmt->affected_rows;
            $stmt->close();
            
            return $affected > 0;
        } catch (Exception $e) {
            error_log('Error en CuponBanner::eliminar(): ' . $e->getMessage());
            return false;
        }
    }

    // Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getNombreBanner() { return $this->nombre_banner; }
    public function setNombreBanner($nombre_banner) { $this->nombre_banner = $nombre_banner; }
    public function getRutaImagen() { return $this->ruta_imagen; }
    public function setRutaImagen($ruta_imagen) { $this->ruta_imagen = $ruta_imagen; }
}