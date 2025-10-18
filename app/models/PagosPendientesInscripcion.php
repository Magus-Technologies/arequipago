<?php

require_once 'config/Conexion.php';

class PagosPendientesInscripcion
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    /**
     * Registrar un pago pendiente de inscripción
     */
    public function registrarPagoPendiente($tipo_inscripcion, $id_pago_inscripcion, $id_cliente_pago, $cuotas_json, $id_usuario_registro, $observaciones = null)
    {
        try {
            $sql = "INSERT INTO pagos_pendientes_inscripcion 
                    (tipo_inscripcion, id_pago_inscripcion, id_cliente_pago, cuotas_json, id_usuario_registro, observaciones) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param(
                "siisis",
                $tipo_inscripcion,
                $id_pago_inscripcion,
                $id_cliente_pago,
                $cuotas_json,
                $id_usuario_registro,
                $observaciones
            );
            
            if ($stmt->execute()) {
                return $this->conexion->insert_id;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error al registrar pago pendiente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener todos los pagos pendientes
     */
    public function obtenerPagosPendientes()
    {
        try {
            $sql = "SELECT 
                        ppi.*,
                        u.nombres as usuario_nombres,
                        u.apellidos as usuario_apellidos
                    FROM pagos_pendientes_inscripcion ppi
                    LEFT JOIN usuarios u ON ppi.id_usuario_registro = u.usuario_id
                    WHERE ppi.estado = 'pendiente'
                    ORDER BY ppi.fecha_registro DESC";
            
            $result = $this->conexion->query($sql);
            $pagos = [];
            
            while ($row = $result->fetch_assoc()) {
                // Inicializar valores por defecto
                $row['cliente_nombres'] = 'N/A';
                $row['cliente_apellidos'] = '';
                $row['cliente_documento'] = 'N/A';
                
                // Obtener datos del conductor o cliente desde observaciones
                $observaciones = json_decode($row['observaciones'], true);
                
                if ($row['tipo_inscripcion'] === 'conductor' && isset($observaciones['id_conductor'])) {
                    $id_conductor = intval($observaciones['id_conductor']);
                    $sqlConductor = "SELECT nombres, apellido_paterno, apellido_materno, nro_documento 
                                    FROM conductores WHERE id_conductor = ?";
                    $stmt = $this->conexion->prepare($sqlConductor);
                    $stmt->bind_param("i", $id_conductor);
                    $stmt->execute();
                    $conductor = $stmt->get_result()->fetch_assoc();
                    if ($conductor) {
                        $row['cliente_nombres'] = $conductor['nombres'];
                        $row['cliente_apellidos'] = trim($conductor['apellido_paterno'] . ' ' . $conductor['apellido_materno']);
                        $row['cliente_documento'] = $conductor['nro_documento'];
                    }
                } else if ($row['tipo_inscripcion'] === 'cliente' && isset($observaciones['id_cliente'])) {
                    $id_cliente = intval($observaciones['id_cliente']);
                    $sqlCliente = "SELECT nombres, apellido_paterno, apellido_materno, n_documento 
                                  FROM clientes_financiar WHERE id = ?";
                    $stmt = $this->conexion->prepare($sqlCliente);
                    $stmt->bind_param("i", $id_cliente);
                    $stmt->execute();
                    $cliente = $stmt->get_result()->fetch_assoc();
                    if ($cliente) {
                        $row['cliente_nombres'] = $cliente['nombres'];
                        $row['cliente_apellidos'] = trim($cliente['apellido_paterno'] . ' ' . $cliente['apellido_materno']);
                        $row['cliente_documento'] = $cliente['n_documento'];
                    }
                }
                
                $pagos[] = $row;
            }
            
            return $pagos;
        } catch (Exception $e) {
            error_log("Error al obtener pagos pendientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener pagos rechazados
     */
    public function obtenerPagosRechazados()
    {
        try {
            $sql = "SELECT 
                        ppi.*,
                        u.nombres as usuario_nombres,
                        u.apellidos as usuario_apellidos,
                        ua.nombres as aprobador_nombres,
                        ua.apellidos as aprobador_apellidos
                    FROM pagos_pendientes_inscripcion ppi
                    LEFT JOIN usuarios u ON ppi.id_usuario_registro = u.usuario_id
                    LEFT JOIN usuarios ua ON ppi.id_usuario_aprobacion = ua.usuario_id
                    WHERE ppi.estado = 'rechazado'
                    ORDER BY ppi.fecha_aprobacion DESC";
            
            $result = $this->conexion->query($sql);
            $pagos = [];
            
            while ($row = $result->fetch_assoc()) {
                // Inicializar valores por defecto
                $row['cliente_nombres'] = 'N/A';
                $row['cliente_apellidos'] = '';
                $row['cliente_documento'] = 'N/A';
                
                // Obtener datos del conductor o cliente desde observaciones
                $observaciones = json_decode($row['observaciones'], true);
                
                if (is_array($observaciones)) {
                    if ($row['tipo_inscripcion'] === 'conductor' && isset($observaciones['id_conductor'])) {
                        $id_conductor = intval($observaciones['id_conductor']);
                        $sqlConductor = "SELECT nombres, apellido_paterno, apellido_materno, nro_documento 
                                        FROM conductores WHERE id_conductor = ?";
                        $stmt = $this->conexion->prepare($sqlConductor);
                        $stmt->bind_param("i", $id_conductor);
                        $stmt->execute();
                        $conductor = $stmt->get_result()->fetch_assoc();
                        if ($conductor) {
                            $row['cliente_nombres'] = $conductor['nombres'];
                            $row['cliente_apellidos'] = trim($conductor['apellido_paterno'] . ' ' . $conductor['apellido_materno']);
                            $row['cliente_documento'] = $conductor['nro_documento'];
                        }
                    } else if ($row['tipo_inscripcion'] === 'cliente' && isset($observaciones['id_cliente'])) {
                        $id_cliente = intval($observaciones['id_cliente']);
                        $sqlCliente = "SELECT nombres, apellido_paterno, apellido_materno, n_documento 
                                      FROM clientes_financiar WHERE id = ?";
                        $stmt = $this->conexion->prepare($sqlCliente);
                        $stmt->bind_param("i", $id_cliente);
                        $stmt->execute();
                        $cliente = $stmt->get_result()->fetch_assoc();
                        if ($cliente) {
                            $row['cliente_nombres'] = $cliente['nombres'];
                            $row['cliente_apellidos'] = trim($cliente['apellido_paterno'] . ' ' . $cliente['apellido_materno']);
                            $row['cliente_documento'] = $cliente['n_documento'];
                        }
                    }
                }
                
                $pagos[] = $row;
            }
            
            return $pagos;
        } catch (Exception $e) {
            error_log("Error al obtener pagos rechazados: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener un pago pendiente por ID
     */
    public function obtenerPagoPendientePorId($id)
    {
        try {
            $sql = "SELECT * FROM pagos_pendientes_inscripcion WHERE id = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_assoc();
        } catch (Exception $e) {
            error_log("Error al obtener pago pendiente: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualizar estado del pago pendiente
     */
    public function actualizarEstado($id, $estado, $id_usuario_aprobacion, $motivo_rechazo = null)
    {
        try {
            if ($motivo_rechazo !== null && $estado === 'rechazado') {
                // Si hay motivo de rechazo, agregarlo al JSON de observaciones
                $sqlGet = "SELECT observaciones FROM pagos_pendientes_inscripcion WHERE id = ?";
                $stmtGet = $this->conexion->prepare($sqlGet);
                $stmtGet->bind_param("i", $id);
                $stmtGet->execute();
                $result = $stmtGet->get_result();
                $row = $result->fetch_assoc();
                
                $observaciones = json_decode($row['observaciones'], true);
                if (!is_array($observaciones)) {
                    $observaciones = [];
                }
                $observaciones['motivo_rechazo'] = $motivo_rechazo;
                $observaciones_json = json_encode($observaciones);
                
                $sql = "UPDATE pagos_pendientes_inscripcion 
                        SET estado = ?, 
                            id_usuario_aprobacion = ?, 
                            fecha_aprobacion = NOW(),
                            observaciones = ?
                        WHERE id = ?";
                
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param("sisi", $estado, $id_usuario_aprobacion, $observaciones_json, $id);
            } else {
                // Sin motivo de rechazo (aprobación o reactivación)
                $sql = "UPDATE pagos_pendientes_inscripcion 
                        SET estado = ?, 
                            id_usuario_aprobacion = ?, 
                            fecha_aprobacion = NOW()
                        WHERE id = ?";
                
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param("sii", $estado, $id_usuario_aprobacion, $id);
            }
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error al actualizar estado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar ID de pago de inscripción
     */
    public function actualizarIdPagoInscripcion($id, $id_pago_inscripcion)
    {
        try {
            $sql = "UPDATE pagos_pendientes_inscripcion 
                    SET id_pago_inscripcion = ?
                    WHERE id = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("ii", $id_pago_inscripcion, $id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error al actualizar ID pago inscripción: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar ID de cliente pago
     */
    public function actualizarIdClientePago($id, $id_cliente_pago)
    {
        try {
            $sql = "UPDATE pagos_pendientes_inscripcion 
                    SET id_cliente_pago = ?
                    WHERE id = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("ii", $id_cliente_pago, $id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error al actualizar ID cliente pago: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar un pago pendiente
     */
    public function eliminarPagoPendiente($id)
    {
        try {
            $sql = "DELETE FROM pagos_pendientes_inscripcion WHERE id = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error al eliminar pago pendiente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Contar pagos pendientes
     */
    public function contarPagosPendientes()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM pagos_pendientes_inscripcion WHERE estado = 'pendiente'";
            $result = $this->conexion->query($sql);
            $row = $result->fetch_assoc();
            
            return $row['total'];
        } catch (Exception $e) {
            error_log("Error al contar pagos pendientes: " . $e->getMessage());
            return 0;
        }
    }
}
