-- =====================================================
-- CORREGIR FACTURAS GENERADAS DESDE RECAUDACIONES
-- =====================================================
-- Problema: El campo monto en ventas_servicios tiene el total CON IGV
--           cuando debería tener la base imponible SIN IGV
-- 
-- Este script corrige todas las facturas que fueron generadas desde
-- Recaudaciones con el cálculo incorrecto del IGV
-- =====================================================

-- PASO 1: Ver las facturas afectadas (REVISAR ANTES DE EJECUTAR)
SELECT 
    v.id_venta,
    v.serie,
    v.numero,
    v.total as total_con_igv,
    vs.monto as precio_actual,
    ROUND(v.total / 1.18, 2) as precio_correcto,
    vs.monto - ROUND(v.total / 1.18, 2) as diferencia,
    v.fecha_emision,
    vs.descripcion
FROM ventas v
INNER JOIN ventas_servicios vs ON v.id_venta = vs.id_venta
WHERE vs.descripcion LIKE '%Pago Caja Arequipa%'
  AND v.apli_igv = 1
  AND ABS(vs.monto - ROUND(v.total / 1.18, 2)) > 0.10  -- Solo las que tienen diferencia significativa
ORDER BY v.id_venta DESC;

-- =====================================================
-- PASO 2: CORREGIR LAS FACTURAS (EJECUTAR DESPUÉS DE REVISAR)
-- =====================================================

-- IMPORTANTE: Este UPDATE corregirá el precio en ventas_servicios
-- para que sea la base imponible (sin IGV) en lugar del total con IGV

UPDATE ventas_servicios vs
INNER JOIN ventas v ON vs.id_venta = v.id_venta
SET vs.monto = ROUND(v.total / 1.18, 2)
WHERE vs.descripcion LIKE '%Pago Caja Arequipa%'
  AND v.apli_igv = 1
  AND ABS(vs.monto - ROUND(v.total / 1.18, 2)) > 0.10;

-- =====================================================
-- PASO 3: VERIFICAR QUE SE CORRIGIERON (EJECUTAR DESPUÉS)
-- =====================================================

SELECT 
    v.id_venta,
    v.serie,
    v.numero,
    v.total as total_con_igv,
    vs.monto as precio_sin_igv,
    ROUND(v.total - vs.monto, 2) as igv_calculado,
    v.fecha_emision
FROM ventas v
INNER JOIN ventas_servicios vs ON v.id_venta = vs.id_venta
WHERE vs.descripcion LIKE '%Pago Caja Arequipa%'
  AND v.apli_igv = 1
ORDER BY v.id_venta DESC
LIMIT 10;

-- =====================================================
-- EJEMPLO DE RESULTADO ESPERADO:
-- =====================================================
-- Si total = 105.00:
--   precio_sin_igv = 88.98
--   igv_calculado = 16.02
--
-- Si total = 100.00:
--   precio_sin_igv = 84.75
--   igv_calculado = 15.25
-- =====================================================

-- =====================================================
-- NOTA IMPORTANTE SOBRE LOS XMLs:
-- =====================================================
-- Los XMLs ya generados NO se actualizarán con este script.
-- Si necesitas regenerar los XMLs, tendrías que:
-- 1. Anular las facturas en SUNAT
-- 2. Eliminar los registros de ventas_sunat
-- 3. Regenerar los XMLs con los nuevos precios
-- 
-- Sin embargo, si las facturas NO han sido enviadas a SUNAT,
-- puedes regenerar los XMLs manualmente desde el sistema.
-- =====================================================
