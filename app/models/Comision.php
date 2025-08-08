<?php

class Comision {
    private $conectar;

    public function __construct() {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function obtenerMontoComision($tipo_comision, $tipo_vehiculo, $usuario_id = null) {
        $sql = "SELECT monto_comision FROM configuracion_comisiones 
                WHERE tipo_comision = ? AND tipo_vehiculo = ? AND estado = 1 
                AND (usuario_id = ? OR usuario_id IS NULL) 
                ORDER BY usuario_id DESC LIMIT 1";
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("ssi", $tipo_comision, $tipo_vehiculo, $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['monto_comision'];
        }
        
        return 0;
    }

    // Reemplazar con:
    public function registrarComision($usuario_id, $tipo_comision, $referencia_id, $monto_comision, $tipo_vehiculo = null, $observaciones = null, $moneda = 'S/.') {
        $fecha_comision = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO comisiones (usuario_id, tipo_comision, referencia_id, monto_comision, fecha_comision, tipo_vehiculo, observaciones, moneda) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("isidssss", $usuario_id, $tipo_comision, $referencia_id, $monto_comision, $fecha_comision, $tipo_vehiculo, $observaciones, $moneda);
        
        if ($stmt->execute()) {
            return $this->conectar->insert_id;
        }
        
        return false;
    }

    /**
     * Calcula la comisión según las reglas de negocio
     */
    public function calcularComisionFinanciamiento($grupo_financiamiento, $id_variante = null) {
        $comision = ['monto' => 0, 'moneda' => 'S/.', 'aplica' => false];
        
        // Convertir grupo_financiamiento a entero si es string numérico
        $planId = is_numeric($grupo_financiamiento) ? intval($grupo_financiamiento) : null;
        
        if (!$planId) {
            return $comision;
        }
        
        switch ($planId) {
            case 19: // CREDI GO VEHÍCULO
                if ($id_variante) {
                    switch (intval($id_variante)) {
                        case 4: // CERT $13,000
                            return ['monto' => 30.00, 'moneda' => '$', 'aplica' => true];
                        case 5: // CERT $15,000
                            return ['monto' => 40.00, 'moneda' => '$', 'aplica' => true];
                        case 6: // CERT $17,000
                            return ['monto' => 50.00, 'moneda' => '$', 'aplica' => true];
                    }
                }
                break;
                
            case 22: // CREDI GO MOTO
                return ['monto' => 50.00, 'moneda' => 'S/.', 'aplica' => true];
                
            case 2: // Redmi 14
                return ['monto' => 50.00, 'moneda' => 'S/.', 'aplica' => true];
                
            case 3: // Redmi 14 PRO
                return ['monto' => 50.00, 'moneda' => 'S/.', 'aplica' => true]; // 400 + 50
                
            case 4: // Redmi 14 PRO 5G
                return ['monto' => 50.00, 'moneda' => 'S/.', 'aplica' => true]; // 500 + 50
                
            case 33: // MOTO YA
                return ['monto' => 150.00, 'moneda' => 'S/.', 'aplica' => true];
        }
        
        return $comision;
    }
    
    /**
     * Obtiene las comisiones con filtros aplicados
     */
    public function obtenerComisiones($usuario_id = null, $tipo = '', $estado = '', $fecha_desde = '', $fecha_hasta = '') {
        $sql = "SELECT 
                    c.*,
                    u.nombres as nombre_usuario,
                    CASE 
                        WHEN c.tipo_comision = 'inscripcion' THEN 
                            CONCAT('Comisión por inscripción - Conductor ID: ', cp.id_conductor)
                        WHEN c.tipo_comision = 'financiamiento' THEN 
                            CONCAT('Comisión por financiamiento - ',
                                CASE 
                                    WHEN f.grupo_financiamiento = '19' THEN 'CREDI GO Vehículo'
                                    WHEN f.grupo_financiamiento = '22' THEN 'CREDI GO Moto'
                                    WHEN f.grupo_financiamiento = '2' THEN 'Redmi 14'
                                    WHEN f.grupo_financiamiento = '3' THEN 'Redmi 14 Pro'
                                    WHEN f.grupo_financiamiento = '4' THEN 'Redmi 14 Pro 5G'
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
            $sql .= " AND c.usuario_id = ?";
            $params[] = $usuario_id;
            $types .= 'i';
        }
        
        if (!empty($tipo)) {
            $sql .= " AND c.tipo_comision = ?";
            $params[] = $tipo;
            $types .= 's';
        }
        
        if (!empty($estado)) {
            $sql .= " AND c.estado_comision = ?";
            $params[] = $estado;
            $types .= 's';
        }
        
        if (!empty($fecha_desde)) {
            $sql .= " AND DATE(c.fecha_comision) >= ?";
            $params[] = $fecha_desde;
            $types .= 's';
        }
        
        if (!empty($fecha_hasta)) {
            $sql .= " AND DATE(c.fecha_comision) <= ?";
            $params[] = $fecha_hasta;
            $types .= 's';
        }
        
        $sql .= " ORDER BY c.fecha_comision DESC";
        
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
    public function obtenerEstadisticasComisiones($usuario_id = null) {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado_comision = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado_comision = 'pagada' THEN 1 ELSE 0 END) as pagadas,
                    SUM(CASE WHEN estado_comision = 'cancelada' THEN 1 ELSE 0 END) as canceladas
                FROM comisiones 
                WHERE 1=1";
        
        if ($usuario_id !== null) {
            $sql .= " AND usuario_id = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $usuario_id);
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
                                    WHEN f.grupo_financiamiento = '19' THEN 'CREDI GO Vehículo'
                                    WHEN f.grupo_financiamiento = '22' THEN 'CREDI GO Moto'
                                    WHEN f.grupo_financiamiento = '2' THEN 'Redmi 14'
                                    WHEN f.grupo_financiamiento = '3' THEN 'Redmi 14 Pro'
                                    WHEN f.grupo_financiamiento = '4' THEN 'Redmi 14 Pro 5G'
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
        $sql .= " AND c.usuario_id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("ii", $id_comision, $usuario_id);
    } else {
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id_comision);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row;
    }
    
    return null;
}
}














