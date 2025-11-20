<?php

class Comision
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function obtenerMontoComision($tipo_comision, $tipo_vehiculo, $usuario_id = null)
    {
        $sql = 'SELECT monto_comision FROM configuracion_comisiones 
                WHERE tipo_comision = ? AND tipo_vehiculo = ? AND estado = 1 
                AND (usuario_id = ? OR usuario_id IS NULL) 
                ORDER BY usuario_id DESC LIMIT 1';

        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param('ssi', $tipo_comision, $tipo_vehiculo, $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row['monto_comision'];
        }

        return 0;
    }

    // Reemplazar con:
    public function registrarComision($usuario_id, $tipo_comision, $referencia_id, $monto_comision, $tipo_vehiculo = null, $observaciones = null, $moneda = 'S/.')
    {
        // NUEVO: Verificar el rol del usuario - NO generar comisión para directores (rol 3)
        $sqlRol = 'SELECT id_rol FROM usuarios WHERE usuario_id = ?';
        $stmtRol = $this->conectar->prepare($sqlRol);
        $stmtRol->bind_param('i', $usuario_id);
        $stmtRol->execute();
        $resultRol = $stmtRol->get_result();

        if ($rowRol = $resultRol->fetch_assoc()) {
            // Si el usuario es director (rol 3), NO crear comisión
            if ($rowRol['id_rol'] == 3) {
                return false;  // No se crea comisión para directores
            }
        }

        $fecha_comision = date('Y-m-d H:i:s');

        $sql = 'INSERT INTO comisiones (usuario_id, tipo_comision, referencia_id, monto_comision, fecha_comision, tipo_vehiculo, observaciones, moneda) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)';

        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param('isidssss', $usuario_id, $tipo_comision, $referencia_id, $monto_comision, $fecha_comision, $tipo_vehiculo, $observaciones, $moneda);

        if ($stmt->execute()) {
            return $this->conectar->insert_id;
        }

        return false;
    }

    /**
     * Calcula la comisión por ingreso de cliente
     */
    public function calcularComisionIngresoCliente()
    {
        return ['monto' => 30.0, 'moneda' => 'S/.', 'aplica' => true];
    }

    /**
     * Calcula la comisión según las reglas de negocio
     * ✅ MODIFICADO: Ahora lee los valores de la base de datos en lugar de hardcodear
     */
    public function calcularComisionFinanciamiento($grupo_financiamiento, $id_variante = null)
    {
        $comision = ['monto' => 0, 'moneda' => 'S/.', 'aplica' => false];

        // Convertir grupo_financiamiento a entero si es string numérico
        $planId = is_numeric($grupo_financiamiento) ? intval($grupo_financiamiento) : null;

        if (!$planId) {
            return $comision;
        }

        // ✅ PASO 1: Intentar obtener comisión de la variante (si existe y tiene comisión específica)
        if ($id_variante) {
            $sqlVariante = "SELECT monto_comision, moneda_comision 
                           FROM grupos_variantes 
                           WHERE idgrupos_variantes = ? 
                           AND monto_comision IS NOT NULL";

            $stmtVariante = $this->conectar->prepare($sqlVariante);
            $stmtVariante->bind_param('i', $id_variante);
            $stmtVariante->execute();
            $resultVariante = $stmtVariante->get_result();

            if ($rowVariante = $resultVariante->fetch_assoc()) {
                // La variante tiene comisión específica configurada
                return [
                    'monto' => floatval($rowVariante['monto_comision']),
                    'moneda' => $rowVariante['moneda_comision'] ?? 'S/.',
                    'aplica' => true,
                ];
            }
        }

        // ✅ PASO 2: Si no hay comisión en variante, obtener del plan principal
        $sqlPlan = "SELECT aplica_comision, monto_comision, moneda_comision 
                   FROM planes_financiamiento 
                   WHERE idplan_financiamiento = ?";

        $stmtPlan = $this->conectar->prepare($sqlPlan);
        $stmtPlan->bind_param('i', $planId);
        $stmtPlan->execute();
        $resultPlan = $stmtPlan->get_result();

        if ($rowPlan = $resultPlan->fetch_assoc()) {
            // Verificar si el plan tiene comisión activada
            if ($rowPlan['aplica_comision'] == 1 && $rowPlan['monto_comision'] !== null) {
                return [
                    'monto' => floatval($rowPlan['monto_comision']),
                    'moneda' => $rowPlan['moneda_comision'] ?? 'S/.',
                    'aplica' => true,
                ];
            } elseif ($rowPlan['aplica_comision'] == 0) {
                // El plan tiene desactivada la comisión explícitamente
                return ['monto' => 0, 'moneda' => 'S/.', 'aplica' => false];
            }
        }

        // ⚠️ FALLBACK: Si no hay configuración en BD, usar valores por defecto (legacy)
        // Esto mantiene compatibilidad con planes antiguos que no tienen comisión configurada
        error_log("⚠️ COMISIÓN: Plan ID $planId no tiene comisión configurada en BD, usando valores por defecto");

        switch ($planId) {
            // CREDI GO AUTOS - Grupo 3
            case 19:
                if ($id_variante) {
                    switch (intval($id_variante)) {
                        case 4:
                            return ['monto' => 30.0, 'moneda' => '$', 'aplica' => true];
                        case 5:
                            return ['monto' => 40.0, 'moneda' => '$', 'aplica' => true];
                        case 6:
                            return ['monto' => 50.0, 'moneda' => '$', 'aplica' => true];
                    }
                }
                break;

            // CREDI GO AUTOS - Grupo 4
            case 38:
                if ($id_variante) {
                    switch (intval($id_variante)) {
                        case 21:
                            return ['monto' => 30.0, 'moneda' => '$', 'aplica' => true];
                        case 22:
                            return ['monto' => 40.0, 'moneda' => '$', 'aplica' => true];
                        case 23:
                            return ['monto' => 50.0, 'moneda' => '$', 'aplica' => true];
                    }
                }
                break;

            // CREDI GO MOTOS
            case 22:
                return ['monto' => 50.0, 'moneda' => 'S/.', 'aplica' => true];
            case 45:
                return ['monto' => 25.0, 'moneda' => '$', 'aplica' => true];

            // CELULARES (TODOS)
            case 2:  // Redmi 14
            case 3:  // Redmi 14 PRO
            case 4:  // Redmi 14 PRO 5G
                return ['monto' => 50.0, 'moneda' => 'S/.', 'aplica' => true];

            // LLANTAS
            case 14:
                return ['monto' => 15.0, 'moneda' => 'S/.', 'aplica' => true];

            // ACEITES
            case 15:
                return ['monto' => 15.0, 'moneda' => 'S/.', 'aplica' => true];

            // BATERÍAS
            case 16:
                return ['monto' => 15.0, 'moneda' => 'S/.', 'aplica' => true];

            // MOTO YA
            case 33:
                return ['monto' => 100.0, 'moneda' => 'S/.', 'aplica' => true];
        }

        return $comision;
    }

    /**
     * Obtiene las comisiones con filtros aplicados
     */
    public function obtenerComisiones($usuario_id = null, $tipo = '', $estado = '', $fecha_desde = '', $fecha_hasta = '')
    {
        $sql = "SELECT 
                    c.*,
                    u.nombres as nombre_usuario,
                    CASE 
                        WHEN c.tipo_comision = 'inscripcion' THEN 
                            CONCAT('Comisión por inscripción - Conductor ID: ', cp.id_conductor)
                        WHEN c.tipo_comision = 'financiamiento' THEN 
                            CONCAT('Comisión por financiamiento - ',
                                CASE 
                                    WHEN f.grupo_financiamiento = '19' THEN 'CREDI GO Autos'
                                    WHEN f.grupo_financiamiento = '38' THEN 'CREDI GO Autos Grupo 4'
                                    WHEN f.grupo_financiamiento = '22' THEN 'CREDI GO Motos'
                                    WHEN f.grupo_financiamiento = '2' THEN 'Celulares'
                                    WHEN f.grupo_financiamiento = '3' THEN 'Celulares'
                                    WHEN f.grupo_financiamiento = '4' THEN 'Celulares'
                                    WHEN f.grupo_financiamiento = '14' THEN 'Llantas'
                                    WHEN f.grupo_financiamiento = '15' THEN 'Aceites'
                                    WHEN f.grupo_financiamiento = '16' THEN 'Baterías'
                                    WHEN f.grupo_financiamiento = '33' THEN 'MOTO YA'
                                    ELSE CONCAT('Plan ID: ', f.grupo_financiamiento)
                                END
                            )
                        ELSE c.observaciones
                    END as descripcion_detallada
                FROM comisiones c
                LEFT JOIN usuarios u ON c.usuario_id = u.usuario_id
                LEFT JOIN conductor_pago cp ON (c.tipo_comision = 'inscripcion' AND c.referencia_id = cp.id_conductorpago)
                LEFT JOIN financiamiento f ON (c.tipo_comision = 'financiamiento' AND c.referencia_id = f.idfinanciamiento)
                WHERE 1=1";

        $params = [];
        $types = '';

        if ($usuario_id !== null) {
            $sql .= ' AND c.usuario_id = ?';
            $params[] = $usuario_id;
            $types .= 'i';
        }

        if (!empty($tipo)) {
            $sql .= ' AND c.tipo_comision = ?';
            $params[] = $tipo;
            $types .= 's';
        }

        if (!empty($estado)) {
            $sql .= ' AND c.estado_comision = ?';
            $params[] = $estado;
            $types .= 's';
        }

        if (!empty($fecha_desde)) {
            $sql .= ' AND DATE(c.fecha_comision) >= ?';
            $params[] = $fecha_desde;
            $types .= 's';
        }

        if (!empty($fecha_hasta)) {
            $sql .= ' AND DATE(c.fecha_comision) <= ?';
            $params[] = $fecha_hasta;
            $types .= 's';
        }

        $sql .= ' ORDER BY c.fecha_comision DESC';

        $stmt = $this->conectar->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $comisiones = [];
        while ($row = $result->fetch_assoc()) {
            $comisiones[] = $row;
        }

        return $comisiones;
    }

    /**
     * Obtiene estadísticas de comisiones
     */
    public function obtenerEstadisticasComisiones($usuario_id = null)
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado_comision = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado_comision = 'pagada' THEN 1 ELSE 0 END) as pagadas,
                    SUM(CASE WHEN estado_comision = 'cancelada' THEN 1 ELSE 0 END) as canceladas
                FROM comisiones 
                WHERE 1=1";

        if ($usuario_id !== null) {
            $sql .= ' AND usuario_id = ?';
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param('i', $usuario_id);
        } else {
            $stmt = $this->conectar->prepare($sql);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row;
        }

        return [
            'total' => 0,
            'pendientes' => 0,
            'pagadas' => 0,
            'canceladas' => 0
        ];
    }

    /**
     * Obtiene detalles completos de una comisión específica
     */
    public function obtenerDetalleComision($id_comision, $usuario_id, $rol_usuario)
    {
        $sql = "SELECT 
                    c.*,
                    u.nombres as nombre_usuario,
                    u.apellidos as apellido_usuario,
                    CASE 
                        WHEN c.tipo_comision = 'inscripcion' THEN
                            CONCAT(cond.nombres, ' ', cond.apellido_paterno, ' ', cond.apellido_materno)
                        WHEN c.tipo_comision = 'financiamiento' THEN
                            COALESCE(CONCAT(cond_f.nombres, ' ', cond_f.apellido_paterno, ' ', cond_f.apellido_materno), 
                                    CONCAT(cli.nombres, ' ', cli.apellido_paterno, ' ', cli.apellido_materno))
                        ELSE 'N/A'
                    END as nombre_beneficiario,
                    CASE 
                        WHEN c.tipo_comision = 'inscripcion' THEN
                            CONCAT('Pago de inscripción - Monto: ', cp.monto_pago, ' - Fecha: ', cp.fecha_pago)
                        WHEN c.tipo_comision = 'financiamiento' THEN
                            CONCAT('Financiamiento - Plan: ', 
                                CASE 
                                    WHEN f.grupo_financiamiento = '19' THEN 'CREDI GO Autos'
                                    WHEN f.grupo_financiamiento = '38' THEN 'CREDI GO Autos Grupo 4'
                                    WHEN f.grupo_financiamiento = '22' THEN 'CREDI GO Motos'
                                    WHEN f.grupo_financiamiento = '2' THEN 'Celulares'
                                    WHEN f.grupo_financiamiento = '3' THEN 'Celulares'
                                    WHEN f.grupo_financiamiento = '4' THEN 'Celulares'
                                    WHEN f.grupo_financiamiento = '14' THEN 'Llantas'
                                    WHEN f.grupo_financiamiento = '15' THEN 'Aceites'
                                    WHEN f.grupo_financiamiento = '16' THEN 'Baterías'
                                    WHEN f.grupo_financiamiento = '33' THEN 'MOTO YA'
                                    ELSE CONCAT('Plan ID: ', f.grupo_financiamiento)
                                END,
                                ' - Monto Total: ', f.monto_total, ' ', COALESCE(f.moneda, 'S/.'))
                        ELSE c.observaciones
                    END as descripcion_completa,
                    CASE 
                        WHEN c.tipo_comision = 'financiamiento' AND f.id_variante IS NOT NULL THEN
                            gv.nombre_variante
                        ELSE NULL
                    END as nombre_variante,
                    CASE 
                        WHEN c.tipo_comision = 'financiamiento' THEN
                            pf.nombre_plan
                        ELSE NULL
                    END as nombre_plan
                FROM comisiones c
                LEFT JOIN usuarios u ON c.usuario_id = u.usuario_id
                LEFT JOIN conductor_pago cp ON (c.tipo_comision = 'inscripcion' AND c.referencia_id = cp.id_conductorpago)
                LEFT JOIN conductores cond ON cp.id_conductor = cond.id_conductor
                LEFT JOIN financiamiento f ON (c.tipo_comision = 'financiamiento' AND c.referencia_id = f.idfinanciamiento)
                LEFT JOIN conductores cond_f ON f.id_conductor = cond_f.id_conductor
            LEFT JOIN clientes_financiar cli ON f.id_cliente = cli.id
            LEFT JOIN grupos_variantes gv ON f.id_variante = gv.idgrupos_variantes
            LEFT JOIN planes_financiamiento pf ON gv.idplan_financiamiento = pf.idplan_financiamiento
            WHERE c.id_comision = ?";

        // Si no es director, solo puede ver sus propias comisiones
        if ($rol_usuario != 3) {
            $sql .= ' AND c.usuario_id = ?';
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param('ii', $id_comision, $usuario_id);
        } else {
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param('i', $id_comision);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row;
        }

        return null;
    }

    /**
     * Cambia el estado de una comisión
     */
    public function cambiarEstadoComision($id_comision, $nuevo_estado)
    {
        $sql = 'UPDATE comisiones SET estado_comision = ? WHERE id_comision = ?';
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param('si', $nuevo_estado, $id_comision);

        return $stmt->execute();
    }

    /**
     * Elimina definitivamente una comisión
     */
    public function eliminarComision($id_comision)
    {
        $sql = 'DELETE FROM comisiones WHERE id_comision = ?';
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param('i', $id_comision);

        return $stmt->execute();
    }
}
