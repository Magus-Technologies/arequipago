-- =====================================================
-- CORREGIR FACTURAS ESPECÍFICAS 624 Y 625
-- =====================================================

-- Ver los datos actuales
SELECT 
    v.id_venta,
    v.serie,
    v.numero,
    v.total,
    v.moneda,
    vs.monto as precio_actual,
    ROUND(v.total / 1.18, 2) as precio_correcto,
    vs.descripcion
FROM ventas v
INNER JOIN ventas_servicios vs ON v.id_venta = vs.id_venta
WHERE v.id_venta IN (624, 625);

-- Resultado esperado ANTES de corregir:
-- id_venta | total  | precio_actual | precio_correcto
-- 624      | 105.00 | 104.81        | 88.98
-- 625      | 105.00 | 88.82         | 88.98

-- =====================================================
-- CORREGIR LAS FACTURAS
-- =====================================================

UPDATE ventas_servicios 
SET monto = ROUND(105.00 / 1.18, 2)  -- 88.98
WHERE id_venta IN (624, 625);

-- =====================================================
-- VERIFICAR QUE SE CORRIGIERON
-- =====================================================

SELECT 
    v.id_venta,
    v.serie,
    v.numero,
    v.total as total_con_igv,
    vs.monto as precio_sin_igv,
    ROUND(v.total - vs.monto, 2) as igv_calculado,
    CASE 
        WHEN v.moneda = 1 THEN 'PEN'
        WHEN v.moneda = 2 THEN 'USD'
        ELSE 'OTRO'
    END as moneda
FROM ventas v
INNER JOIN ventas_servicios vs ON v.id_venta = vs.id_venta
WHERE v.id_venta IN (624, 625);

-- Resultado esperado DESPUÉS de corregir:
-- id_venta | total_con_igv | precio_sin_igv | igv_calculado | moneda
-- 624      | 105.00        | 88.98          | 16.02         | USD
-- 625      | 105.00        | 88.98          | 16.02         | USD

-- =====================================================
-- NOTA: Los XMLs ya generados seguirán teniendo el precio
-- incorrecto. Si necesitas corregirlos, tendrías que
-- regenerar los XMLs desde el sistema.
-- =====================================================
