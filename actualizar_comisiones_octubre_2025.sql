-- =====================================================
-- Script para actualizar comisiones retroactivas
-- Octubre 2025 - Nuevas reglas de comisiones
-- =====================================================

-- Deshabilitar modo seguro temporalmente
SET SQL_SAFE_UPDATES = 0;

-- 1. ACTUALIZAR MOTO YA de 150 a 100 soles
UPDATE comisiones c
INNER JOIN financiamiento f ON c.referencia_id = f.idfinanciamiento
SET c.monto_comision = 100.00
WHERE c.tipo_comision = 'financiamiento'
    AND f.grupo_financiamiento = '33'
    AND c.monto_comision = 150.00
    AND DATE(c.fecha_comision) >= '2025-10-01';

-- 2. INSERTAR comisiones faltantes para LLANTAS (Plan 14)
INSERT INTO comisiones (
    usuario_id, 
    tipo_comision, 
    referencia_id, 
    monto_comision, 
    fecha_comision, 
    tipo_vehiculo, 
    estado_comision, 
    observaciones, 
    moneda
)
SELECT 
    f.usuario_id,
    'financiamiento' as tipo_comision,
    f.idfinanciamiento as referencia_id,
    15.00 as monto_comision,
    f.fecha_creacion as fecha_comision,
    NULL as tipo_vehiculo,
    'pendiente' as estado_comision,
    'Comisión retroactiva - Llantas' as observaciones,
    'S/.' as moneda
FROM financiamiento f
INNER JOIN usuarios u ON f.usuario_id = u.usuario_id
WHERE f.grupo_financiamiento = '14'
    AND DATE(f.fecha_creacion) BETWEEN '2025-10-01' AND '2025-10-31'
    AND u.id_rol != 3
    AND f.estado_eliminado = 0
    AND NOT EXISTS (
        SELECT 1 
        FROM comisiones c 
        WHERE c.tipo_comision = 'financiamiento' 
        AND c.referencia_id = f.idfinanciamiento
    );

-- 3. INSERTAR comisiones faltantes para ACEITES (Plan 15)
INSERT INTO comisiones (
    usuario_id, 
    tipo_comision, 
    referencia_id, 
    monto_comision, 
    fecha_comision, 
    tipo_vehiculo, 
    estado_comision, 
    observaciones, 
    moneda
)
SELECT 
    f.usuario_id,
    'financiamiento' as tipo_comision,
    f.idfinanciamiento as referencia_id,
    15.00 as monto_comision,
    f.fecha_creacion as fecha_comision,
    NULL as tipo_vehiculo,
    'pendiente' as estado_comision,
    'Comisión retroactiva - Aceites' as observaciones,
    'S/.' as moneda
FROM financiamiento f
INNER JOIN usuarios u ON f.usuario_id = u.usuario_id
WHERE f.grupo_financiamiento = '15'
    AND DATE(f.fecha_creacion) BETWEEN '2025-10-01' AND '2025-10-31'
    AND u.id_rol != 3
    AND f.estado_eliminado = 0
    AND NOT EXISTS (
        SELECT 1 
        FROM comisiones c 
        WHERE c.tipo_comision = 'financiamiento' 
        AND c.referencia_id = f.idfinanciamiento
    );

-- 4. INSERTAR comisiones faltantes para BATERÍAS (Plan 16)
INSERT INTO comisiones (
    usuario_id, 
    tipo_comision, 
    referencia_id, 
    monto_comision, 
    fecha_comision, 
    tipo_vehiculo, 
    estado_comision, 
    observaciones, 
    moneda
)
SELECT 
    f.usuario_id,
    'financiamiento' as tipo_comision,
    f.idfinanciamiento as referencia_id,
    15.00 as monto_comision,
    f.fecha_creacion as fecha_comision,
    NULL as tipo_vehiculo,
    'pendiente' as estado_comision,
    'Comisión retroactiva - Baterías' as observaciones,
    'S/.' as moneda
FROM financiamiento f
INNER JOIN usuarios u ON f.usuario_id = u.usuario_id
WHERE f.grupo_financiamiento = '16'
    AND DATE(f.fecha_creacion) BETWEEN '2025-10-01' AND '2025-10-31'
    AND u.id_rol != 3
    AND f.estado_eliminado = 0
    AND NOT EXISTS (
        SELECT 1 
        FROM comisiones c 
        WHERE c.tipo_comision = 'financiamiento' 
        AND c.referencia_id = f.idfinanciamiento
    );

-- 5. INSERTAR comisiones faltantes para CREDI GO AUTOS GRUPO 4 (Plan 38)
INSERT INTO comisiones (
    usuario_id, 
    tipo_comision, 
    referencia_id, 
    monto_comision, 
    fecha_comision, 
    tipo_vehiculo, 
    estado_comision, 
    observaciones, 
    moneda
)
SELECT 
    f.usuario_id,
    'financiamiento' as tipo_comision,
    f.idfinanciamiento as referencia_id,
    CASE 
        WHEN f.id_variante = 21 THEN 30.00
        WHEN f.id_variante = 22 THEN 40.00
        WHEN f.id_variante = 23 THEN 50.00
        ELSE 0.00
    END as monto_comision,
    f.fecha_creacion as fecha_comision,
    'vehiculo' as tipo_vehiculo,
    'pendiente' as estado_comision,
    CONCAT('Comisión retroactiva - CREDI GO Autos Grupo 4 - Variante ', 
        CASE 
            WHEN f.id_variante = 21 THEN '$13,000'
            WHEN f.id_variante = 22 THEN '$15,000'
            WHEN f.id_variante = 23 THEN '$17,000'
        END
    ) as observaciones,
    '$' as moneda
FROM financiamiento f
INNER JOIN usuarios u ON f.usuario_id = u.usuario_id
WHERE f.grupo_financiamiento = '38'
    AND f.id_variante IN (21, 22, 23)
    AND DATE(f.fecha_creacion) BETWEEN '2025-10-01' AND '2025-10-31'
    AND u.id_rol != 3
    AND f.estado_eliminado = 0
    AND NOT EXISTS (
        SELECT 1 
        FROM comisiones c 
        WHERE c.tipo_comision = 'financiamiento' 
        AND c.referencia_id = f.idfinanciamiento
    );

-- 6. INSERTAR comisiones faltantes para INGRESO DE CLIENTES (cliente_pago)
INSERT INTO comisiones (
    usuario_id, 
    tipo_comision, 
    referencia_id, 
    monto_comision, 
    fecha_comision, 
    tipo_vehiculo, 
    estado_comision, 
    observaciones, 
    moneda
)
SELECT 
    cp.usuario_id,
    'inscripcion' as tipo_comision,
    cp.id as referencia_id,
    30.00 as monto_comision,
    cp.fecha_pago as fecha_comision,
    NULL as tipo_vehiculo,
    'pendiente' as estado_comision,
    'Comisión retroactiva - Ingreso de Cliente' as observaciones,
    'S/.' as moneda
FROM cliente_pago cp
INNER JOIN usuarios u ON cp.usuario_id = u.usuario_id
WHERE DATE(cp.fecha_pago) BETWEEN '2025-10-01' AND '2025-10-31'
    AND u.id_rol != 3
    AND cp.estado = '1'
    AND NOT EXISTS (
        SELECT 1 
        FROM comisiones c 
        WHERE c.tipo_comision = 'inscripcion' 
        AND c.referencia_id = cp.id
    );

-- =====================================================
-- CONSULTAS DE VERIFICACIÓN
-- =====================================================

-- Verificar comisiones actualizadas de MOTO YA
SELECT 
    COUNT(*) as total_actualizadas,
    SUM(monto_comision) as monto_total
FROM comisiones c
INNER JOIN financiamiento f ON c.referencia_id = f.idfinanciamiento
WHERE c.tipo_comision = 'financiamiento'
    AND f.grupo_financiamiento = '33'
    AND c.monto_comision = 100.00
    AND DATE(c.fecha_comision) >= '2025-10-01';

-- Verificar comisiones insertadas por producto
SELECT 
    CASE 
        WHEN f.grupo_financiamiento = '14' THEN 'Llantas'
        WHEN f.grupo_financiamiento = '15' THEN 'Aceites'
        WHEN f.grupo_financiamiento = '16' THEN 'Baterías'
        WHEN f.grupo_financiamiento = '38' THEN 'CREDI GO Autos Grupo 4'
    END as producto,
    COUNT(*) as total_comisiones,
    SUM(c.monto_comision) as monto_total,
    c.moneda
FROM comisiones c
INNER JOIN financiamiento f ON c.referencia_id = f.idfinanciamiento
WHERE c.tipo_comision = 'financiamiento'
    AND f.grupo_financiamiento IN ('14', '15', '16', '38')
    AND DATE(c.fecha_comision) BETWEEN '2025-10-01' AND '2025-10-31'
GROUP BY f.grupo_financiamiento, c.moneda;

-- Verificar comisiones de ingreso de clientes
SELECT 
    COUNT(*) as total_comisiones_clientes,
    SUM(monto_comision) as monto_total
FROM comisiones c
WHERE c.tipo_comision = 'inscripcion'
    AND c.observaciones LIKE '%Ingreso de Cliente%'
    AND DATE(c.fecha_comision) BETWEEN '2025-10-01' AND '2025-10-31';

-- Resumen general de comisiones de octubre 2025
SELECT 
    c.tipo_comision,
    COUNT(*) as total,
    SUM(c.monto_comision) as monto_total,
    c.moneda
FROM comisiones c
WHERE DATE(c.fecha_comision) BETWEEN '2025-10-01' AND '2025-10-31'
GROUP BY c.tipo_comision, c.moneda
ORDER BY c.tipo_comision, c.moneda;

-- Reactivar modo seguro
SET SQL_SAFE_UPDATES = 1;
