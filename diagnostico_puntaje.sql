-- DIAGNÓSTICO: Verificar estado de financiamientos

-- 1. Ver distribución de valores de 'aprobado'
SELECT 
    aprobado,
    COUNT(*) as cantidad,
    CASE 
        WHEN aprobado = 0 THEN 'Pendiente'
        WHEN aprobado = 1 THEN 'Aprobado'
        WHEN aprobado = 2 THEN 'Rechazado'
        WHEN aprobado IS NULL THEN 'NULL (Aprobado por defecto)'
        ELSE 'Otro'
    END as descripcion
FROM financiamiento
WHERE estado IN ('En Progreso', 'En progreso', 'Finalizado')
AND estado_eliminado = 0
GROUP BY aprobado;

-- 2. Ver cuántos financiamientos se están excluyendo por los filtros
SELECT 
    'Total financiamientos activos' as tipo,
    COUNT(*) as cantidad
FROM financiamiento
WHERE estado IN ('En Progreso', 'En progreso', 'Finalizado')
AND estado_eliminado = 0

UNION ALL

SELECT 
    'Financiamientos VÁLIDOS (con filtros)' as tipo,
    COUNT(*) as cantidad
FROM financiamiento
WHERE estado IN ('En Progreso', 'En progreso', 'Finalizado')
AND estado_eliminado = 0
AND (aprobado = 1 OR aprobado IS NULL)

UNION ALL

SELECT 
    'Financiamientos EXCLUIDOS por filtro aprobado' as tipo,
    COUNT(*) as cantidad
FROM financiamiento
WHERE estado IN ('En Progreso', 'En progreso', 'Finalizado')
AND estado_eliminado = 0
AND aprobado NOT IN (1) AND aprobado IS NOT NULL;

-- 3. Ver ejemplo de un conductor específico (DNI: 73894799)
SELECT 
    f.idfinanciamiento,
    f.aprobado,
    f.estado,
    f.estado_eliminado,
    f.fecha_inicio,
    p.nombre as producto,
    CASE 
        WHEN f.aprobado = 1 OR f.aprobado IS NULL THEN 'SE CUENTA'
        ELSE 'NO SE CUENTA'
    END as se_incluye_en_calculo
FROM financiamiento f
LEFT JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
WHERE c.nro_documento = '73894799'
AND f.estado IN ('En Progreso', 'En progreso', 'Finalizado')
AND f.estado_eliminado = 0
ORDER BY f.fecha_inicio DESC;

-- 4. Ver cuotas vencidas del conductor
SELECT 
    cf.idcuotas_financiamiento,
    cf.numero_cuota,
    cf.fecha_vencimiento,
    cf.fecha_pago,
    cf.estado,
    CASE 
        WHEN cf.fecha_vencimiento < CURDATE() AND cf.fecha_pago IS NULL THEN 'VENCIDA'
        WHEN cf.fecha_pago > cf.fecha_vencimiento THEN 'PAGADA CON RETRASO'
        WHEN cf.fecha_pago <= cf.fecha_vencimiento THEN 'PAGADA A TIEMPO'
        ELSE 'PENDIENTE'
    END as estado_real
FROM cuotas_financiamiento cf
INNER JOIN financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
INNER JOIN conductores c ON f.id_conductor = c.id_conductor
WHERE c.nro_documento = '73894799'
AND f.estado_eliminado = 0
AND (f.aprobado = 1 OR f.aprobado IS NULL)
ORDER BY cf.fecha_vencimiento DESC;
