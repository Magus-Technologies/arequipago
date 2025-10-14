<?php

class DepartamentoConfig
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * Obtener todos los departamentos de la tabla depast con su estado de habilitación
     */
    public function obtenerTodosDepartamentos()
    {
        $sql = "SELECT 
                    d.iddepast,
                    d.nombre,
                    COALESCE(dh.habilitado, 0) as habilitado,
                    dh.fecha_modificacion
                FROM depast d
                LEFT JOIN departamentos_habilitados dh ON d.iddepast = dh.iddepast
                ORDER BY d.nombre ASC";
        
        $stmt = $this->conectar->prepare($sql);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }

    /**
     * Obtener solo los departamentos habilitados
     */
    public function obtenerDepartamentosHabilitados()
    {
        $sql = "SELECT 
                    d.iddepast,
                    d.nombre
                FROM depast d
                INNER JOIN departamentos_habilitados dh ON d.iddepast = dh.iddepast
                WHERE dh.habilitado = 1
                ORDER BY d.nombre ASC";
        
        $stmt = $this->conectar->prepare($sql);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }

    /**
     * Cambiar el estado de un departamento
     */
    public function cambiarEstado($iddepast, $habilitado, $usuario_id)
    {
        // Primero verificar si existe el registro
        $sqlCheck = "SELECT id FROM departamentos_habilitados WHERE iddepast = ?";
        $stmtCheck = $this->conectar->prepare($sqlCheck);
        $stmtCheck->bind_param("i", $iddepast);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            // Actualizar registro existente
            $sql = "UPDATE departamentos_habilitados 
                    SET habilitado = ?, 
                        modificado_por = ?,
                        fecha_modificacion = CURRENT_TIMESTAMP
                    WHERE iddepast = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("iii", $habilitado, $usuario_id, $iddepast);
        } else {
            // Insertar nuevo registro
            $nombre = $this->obtenerNombreDepartamento($iddepast);
            $sql = "INSERT INTO departamentos_habilitados (iddepast, nombre, habilitado, modificado_por) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("isii", $iddepast, $nombre, $habilitado, $usuario_id);
        }

        return $stmt->execute();
    }

    /**
     * Obtener el nombre de un departamento
     */
    public function obtenerNombreDepartamento($iddepast)
    {
        $sql = "SELECT nombre FROM depast WHERE iddepast = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $iddepast);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row ? $row['nombre'] : 'Desconocido';
        }
        
        return 'Desconocido';
    }

    /**
     * Registrar cambio en el historial
     */
    public function registrarHistorial($iddepast, $nombre_departamento, $accion, $usuario_id, $usuario_nombre)
    {
        $sql = "INSERT INTO departamentos_historial 
                (iddepast, nombre_departamento, accion, usuario_id, usuario_nombre) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("issis", $iddepast, $nombre_departamento, $accion, $usuario_id, $usuario_nombre);
        
        return $stmt->execute();
    }

    /**
     * Obtener historial de cambios
     */
    public function obtenerHistorial($limit = 20)
    {
        $sql = "SELECT * FROM departamentos_historial 
                ORDER BY fecha DESC 
                LIMIT ?";
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $limit);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }

    /**
     * Contar departamentos habilitados
     */
    public function contarHabilitados()
    {
        $sql = "SELECT COUNT(*) as total FROM departamentos_habilitados WHERE habilitado = 1";
        $stmt = $this->conectar->prepare($sql);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row['total'];
        }
        
        return 0;
    }
}
