<?php

require_once "config/Conexion.php";

class Constatacion
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    /**
     * Obtener vehículos entregados pendientes de constatación
     */
    public function obtenerPendientes()
    {
        $query = "
            SELECT
                f.idfinanciamiento,
                f.id_conductor,
                f.id_cliente,
                f.estado_entrega,
                f.fecha_entrega,
                f.moneda,
                f.monto_total,
                p.nombre as producto_nombre,
                p.categoria,
                CASE
                    WHEN f.id_conductor IS NOT NULL THEN CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno)
                    ELSE CONCAT(cf.nombres, ' ', cf.apellido_paterno, ' ', cf.apellido_materno)
                END as cliente_nombre,
                CASE
                    WHEN f.id_conductor IS NOT NULL THEN c.nro_documento
                    ELSE cf.n_documento
                END as documento,
                CASE
                    WHEN f.id_conductor IS NOT NULL THEN c.telefono
                    ELSE cf.telefono
                END as telefono,
                CASE
                    WHEN f.id_conductor IS NOT NULL THEN 'conductor'
                    ELSE 'cliente'
                END as tipo_persona,
                CASE
                    WHEN f.id_conductor IS NOT NULL THEN 1
                    ELSE 2
                END as tipo_usuario,
                CASE
                    WHEN f.id_conductor IS NOT NULL THEN f.id_conductor
                    ELSE f.id_cliente
                END as id_tipo_usuario,
                CASE
                    WHEN f.id_conductor IS NOT NULL THEN c.verificacion_domiciliaria
                    ELSE cf.verificacion_domiciliaria
                END as verificacion_domiciliaria,
                cd.id_constatacion,
                cd.fecha_constatacion,
                cd.foto_domicilio
            FROM financiamiento f
            LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
            LEFT JOIN clientes_financiar cf ON f.id_cliente = cf.id
            JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
            LEFT JOIN constataciones_domiciliarias cd ON f.idfinanciamiento = cd.id_financiamiento
            WHERE f.estado_entrega = 'entregado'
            AND f.estado_eliminado = 0
            ORDER BY cd.id_constatacion IS NULL DESC, f.fecha_entrega DESC
        ";

        $result = $this->conexion->query($query);

        if (!$result) {
            error_log("Error en obtenerPendientes: " . $this->conexion->error);
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Contar vehículos pendientes de constatación
     */
    public function contarPendientes()
    {
        $query = "
            SELECT COUNT(*) as total
            FROM financiamiento f
            LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
            LEFT JOIN clientes_financiar cf ON f.id_cliente = cf.id
            LEFT JOIN constataciones_domiciliarias cd ON f.idfinanciamiento = cd.id_financiamiento
            WHERE f.estado_entrega = 'entregado'
            AND f.estado_eliminado = 0
            AND cd.id_constatacion IS NULL
        ";

        $result = $this->conexion->query($query);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }

    /**
     * Contar total de vehículos entregados
     */
    public function contarTotalEntregados()
    {
        $query = "
            SELECT COUNT(*) as total
            FROM financiamiento
            WHERE estado_entrega = 'entregado'
            AND estado_eliminado = 0
        ";

        $result = $this->conexion->query($query);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }

    /**
     * Contar constataciones realizadas
     */
    public function contarRealizadas()
    {
        $query = "SELECT COUNT(*) as total FROM constataciones_domiciliarias";
        $result = $this->conexion->query($query);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }

    /**
     * Guardar nueva constatación
     */
    public function guardar($datos)
    {
        $query = "
            INSERT INTO constataciones_domiciliarias
            (id_financiamiento, tipo_usuario, id_tipo_usuario, foto_domicilio, departamento, provincia, distrito, direccion, 
             link_google_maps, captura_google_maps, latitud, longitud, fecha_constatacion, observaciones, usuario_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)
        ";

        $stmt = $this->conexion->prepare($query);

        if (!$stmt) {
            error_log("Error preparando query guardar: " . $this->conexion->error);
            return ['success' => false, 'message' => 'Error al preparar la consulta'];
        }

        $stmt->bind_param(
            "iiisssssssddsi",
            $datos['id_financiamiento'],
            $datos['tipo_usuario'],
            $datos['id_tipo_usuario'],
            $datos['foto_domicilio'],
            $datos['departamento'],
            $datos['provincia'],
            $datos['distrito'],
            $datos['direccion'],
            $datos['link_google_maps'],
            $datos['captura_google_maps'],
            $datos['latitud'],
            $datos['longitud'],
            $datos['observaciones'],
            $datos['usuario_id']
        );

        if ($stmt->execute()) {
            $id_constatacion = $stmt->insert_id;

            // Actualizar verificacion_domiciliaria en conductor o cliente
            if ($datos['tipo_usuario'] == 1) {
                $this->actualizarVerificacionConductor($datos['id_tipo_usuario']);
            } elseif ($datos['tipo_usuario'] == 2) {
                $this->actualizarVerificacionCliente($datos['id_tipo_usuario']);
            }

            return [
                'success' => true,
                'id_constatacion' => $id_constatacion,
                'message' => 'Constatación guardada correctamente'
            ];
        } else {
            error_log("Error ejecutando guardar: " . $stmt->error);
            return ['success' => false, 'message' => 'Error al guardar la constatación'];
        }
    }

    /**
     * Actualizar verificación domiciliaria del conductor
     */
    private function actualizarVerificacionConductor($id_conductor)
    {
        $query = "UPDATE conductores SET verificacion_domiciliaria = 1 WHERE id_conductor = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $id_conductor);
        $stmt->execute();
    }

    /**
     * Actualizar verificación domiciliaria del cliente
     */
    private function actualizarVerificacionCliente($id_cliente)
    {
        $query = "UPDATE clientes_financiar SET verificacion_domiciliaria = 1 WHERE id = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
    }

    /**
     * Obtener detalle de una constatación
     */
    public function obtenerDetalle($id_constatacion)
    {
        $query = "
            SELECT
                cd.*,
                f.monto_total,
                f.moneda,
                p.nombre as producto_nombre,
                CASE
                    WHEN cd.tipo_usuario = 1 THEN CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno)
                    ELSE CONCAT(cf.nombres, ' ', cf.apellido_paterno, ' ', cf.apellido_materno)
                END as cliente_nombre,
                u.usuario as usuario_nombre
            FROM constataciones_domiciliarias cd
            JOIN financiamiento f ON cd.id_financiamiento = f.idfinanciamiento
            JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
            LEFT JOIN conductores c ON cd.tipo_usuario = 1 AND cd.id_tipo_usuario = c.id_conductor
            LEFT JOIN clientes_financiar cf ON cd.tipo_usuario = 2 AND cd.id_tipo_usuario = cf.id
            LEFT JOIN usuarios u ON cd.usuario_id = u.usuario_id
            WHERE cd.id_constatacion = ?
        ";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $id_constatacion);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Obtener información del financiamiento para constatación
     */
    public function obtenerInfoFinanciamiento($id_financiamiento)
    {
        $query = "
            SELECT
                f.idfinanciamiento,
                f.id_conductor,
                f.id_cliente,
                f.estado_entrega,
                f.fecha_entrega,
                p.nombre as producto_nombre,
                CASE
                    WHEN f.id_conductor IS NOT NULL THEN CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno)
                    ELSE CONCAT(cf.nombres, ' ', cf.apellido_paterno, ' ', cf.apellido_materno)
                END as cliente_nombre,
                CASE
                    WHEN f.id_conductor IS NOT NULL THEN c.telefono
                    ELSE cf.telefono
                END as telefono
            FROM financiamiento f
            LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
            LEFT JOIN clientes_financiar cf ON f.id_cliente = cf.id
            JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
            WHERE f.idfinanciamiento = ?
        ";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $id_financiamiento);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Eliminar constatación
     */
    public function eliminar($id_constatacion)
    {
        $query = "DELETE FROM constataciones_domiciliarias WHERE id_constatacion = ?";
        $stmt = $this->conexion->prepare($query);

        if (!$stmt) {
            error_log("Error preparando query eliminar: " . $this->conexion->error);
            return ['success' => false, 'message' => 'Error al preparar la consulta'];
        }

        $stmt->bind_param("i", $id_constatacion);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Constatación eliminada correctamente'
            ];
        } else {
            error_log("Error ejecutando eliminar: " . $stmt->error);
            return ['success' => false, 'message' => 'Error al eliminar la constatación'];
        }
    }

    /**
     * Obtener todos los datos de constataciones para reporte Excel
     */
    public function obtenerTodosParaReporte()
    {
        $query = "
            SELECT
                cd.id_constatacion,
                cd.fecha_constatacion,
                f.idfinanciamiento,
                f.monto_total,
                f.moneda,
                f.fecha_entrega,
                p.nombre as producto_nombre,
                p.categoria,
                CASE
                    WHEN cd.tipo_usuario = 1 THEN CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno)
                    ELSE CONCAT(cf.nombres, ' ', cf.apellido_paterno, ' ', cf.apellido_materno)
                END as cliente_nombre,
                CASE
                    WHEN cd.tipo_usuario = 1 THEN c.nro_documento
                    ELSE cf.n_documento
                END as documento,
                CASE
                    WHEN cd.tipo_usuario = 1 THEN c.telefono
                    ELSE cf.telefono
                END as telefono,
                CASE
                    WHEN cd.tipo_usuario = 1 THEN 'Conductor'
                    ELSE 'Cliente'
                END as tipo_persona,
                cd.departamento,
                cd.provincia,
                cd.distrito,
                cd.direccion,
                cd.link_google_maps,
                cd.observaciones,
                u.usuario as usuario_nombre,
                c.numUnidad as numero_unidad
            FROM constataciones_domiciliarias cd
            JOIN financiamiento f ON cd.id_financiamiento = f.idfinanciamiento
            JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
            LEFT JOIN conductores c ON cd.tipo_usuario = 1 AND cd.id_tipo_usuario = c.id_conductor
            LEFT JOIN clientes_financiar cf ON cd.tipo_usuario = 2 AND cd.id_tipo_usuario = cf.id
            LEFT JOIN usuarios u ON cd.usuario_id = u.usuario_id
            ORDER BY cd.fecha_constatacion DESC
        ";

        $result = $this->conexion->query($query);

        if (!$result) {
            error_log("Error en obtenerTodosParaReporte: " . $this->conexion->error);
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtener vehículos pendientes de constatación para reporte Excel
     */
    public function obtenerPendientesParaReporte()
    {
        $query = "
            SELECT
                f.idfinanciamiento,
                f.fecha_entrega,
                f.monto_total,
                f.moneda,
                p.nombre as producto_nombre,
                p.categoria,
                CASE
                    WHEN f.id_conductor IS NOT NULL THEN CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno)
                    ELSE CONCAT(cf.nombres, ' ', cf.apellido_paterno, ' ', cf.apellido_materno)
                END as cliente_nombre,
                CASE
                    WHEN f.id_conductor IS NOT NULL THEN c.nro_documento
                    ELSE cf.n_documento
                END as documento,
                CASE
                    WHEN f.id_conductor IS NOT NULL THEN c.telefono
                    ELSE cf.telefono
                END as telefono,
                CASE
                    WHEN f.id_conductor IS NOT NULL THEN 'Conductor'
                    ELSE 'Cliente'
                END as tipo_persona,
                c.numUnidad as numero_unidad
            FROM financiamiento f
            LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
            LEFT JOIN clientes_financiar cf ON f.id_cliente = cf.id
            JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
            LEFT JOIN constataciones_domiciliarias cd ON f.idfinanciamiento = cd.id_financiamiento
            WHERE f.estado_entrega = 'entregado'
            AND f.estado_eliminado = 0
            AND cd.id_constatacion IS NULL
            ORDER BY f.fecha_entrega DESC
        ";

        $result = $this->conexion->query($query);

        if (!$result) {
            error_log("Error en obtenerPendientesParaReporte: " . $this->conexion->error);
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
