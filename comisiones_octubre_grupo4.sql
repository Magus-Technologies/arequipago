-- Script para generar comisiones retroactivas de octubre 2025
-- CrediGo Autos Grupo 4 (Plan ID 38)
-- Variantes: 21 ($30), 22 ($40), 23 ($50)

-- Insertar comisiones faltantes para financiamientos del Grupo 4 en octubre 2025
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
    CONCAT('Comisión retroactiva - CrediGo Autos Grupo 4 - Variante ', 
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
    AND u.id_rol != 3  -- Excluir directores
    AND f.estado_eliminado = 0
    AND NOT EXISTS (
        -- Verificar que no exista ya una comisión para este financiamiento
        SELECT 1 
        FROM comisiones c 
        WHERE c.tipo_comision = 'financiamiento' 
        AND c.referencia_id = f.idfinanciamiento
    );

-- Consulta para verificar cuántas comisiones se insertarán
SELECT 
    COUNT(*) as total_comisiones,
    SUM(CASE WHEN f.id_variante = 21 THEN 30.00
             WHEN f.id_variante = 22 THEN 40.00
             WHEN f.id_variante = 23 THEN 50.00
             ELSE 0.00 END) as monto_total_dolares,
    f.id_variante,
    CASE 
        WHEN f.id_variante = 21 THEN '$13,000 - $30 comisión'
        WHEN f.id_variante = 22 THEN '$15,000 - $40 comisión'
        WHEN f.id_variante = 23 THEN '$17,000 - $50 comisión'
    END as descripcion_variante
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
    )
GROUP BY f.id_variante;

-- Consulta para ver el detalle de los financiamientos que recibirán comisión
SELECT 
    f.idfinanciamiento,
    f.fecha_creacion,
    f.id_variante,
    CASE 
        WHEN f.id_variante = 21 THEN 30.00
        WHEN f.id_variante = 22 THEN 40.00
        WHEN f.id_variante = 23 THEN 50.00
    END as comision_dolares,
    COALESCE(CONCAT(cond.nombres, ' ', cond.apellido_paterno), 
             CONCAT(cli.nombres, ' ', cli.apellido_paterno)) as cliente,
    CONCAT(u.nombres, ' ', u.apellidos) as asesor,
    u.id_rol as rol_asesor
FROM financiamiento f
INNER JOIN usuarios u ON f.usuario_id = u.usuario_id
LEFT JOIN conductores cond ON f.id_conductor = cond.id_conductor
LEFT JOIN clientes_financiar cli ON f.id_cliente = cli.id
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
    )
ORDER BY f.fecha_creacion;
