-- ============================================
-- FIX: Identificar y Analizar Duplicados en Ventas
-- ============================================

-- PASO 1: Ver duplicados por serie y número
SELECT 
    serie, 
    numero, 
    COUNT(*) as cantidad,
    GROUP_CONCAT(id_venta ORDER BY id_venta) as ids_ventas,
    GROUP_CONCAT(fecha_emision ORDER BY id_venta) as fechas
FROM ventas
WHERE id_empresa = 12
GROUP BY serie, numero
HAVING COUNT(*) > 1
ORDER BY serie, numero;

-- PASO 2: Ver detalles de B001-126 específicamente
SELECT 
    id_venta,
    serie,
    numero,
    fecha_emision,
    total,
    estado,
    enviado_sunat,
    id_cliente
FROM ventas
WHERE serie = 'B001' AND numero = 126 AND id_empresa = 12
ORDER BY id_venta DESC;

-- PASO 3: Ver si hay notas de crédito asociadas a estos duplicados
SELECT 
    ne.nota_id,
    ne.id_venta,
    ne.serie as serie_nc,
    ne.numero as numero_nc,
    v.serie as serie_venta,
    v.numero as numero_venta,
    v.fecha_emision
FROM notas_electronicas ne
JOIN ventas v ON ne.id_venta = v.id_venta
WHERE v.serie = 'B001' AND v.numero = 126
ORDER BY ne.nota_id DESC;

-- ============================================
-- SOLUCIÓN: Cambiar Serie de Documentos Nuevos
-- ============================================

-- OPCIÓN 1: Cambiar la serie en documentos_empresas para futuros documentos
-- Esto evita crear más duplicados
UPDATE documentos_empresas 
SET serie = 'B002'  -- Nueva serie
WHERE id_empresa = 12 
  AND sucursal = 1 
  AND id_tido = 1;  -- 1 = Boleta

-- Verificar el cambio
SELECT * FROM documentos_empresas 
WHERE id_empresa = 12 AND sucursal = 1 AND id_tido = 1;

-- ============================================
-- ANÁLISIS: ¿Cuál duplicado mantener?
-- ============================================

-- Ver cuál tiene productos/servicios asociados
SELECT 
    v.id_venta,
    v.serie,
    v.numero,
    v.fecha_emision,
    v.total,
    (SELECT COUNT(*) FROM productos_ventas pv WHERE pv.id_venta = v.id_venta) as cant_productos,
    (SELECT COUNT(*) FROM ventas_servicios vs WHERE vs.id_venta = v.id_venta) as cant_servicios,
    (SELECT COUNT(*) FROM notas_electronicas ne WHERE ne.id_venta = v.id_venta) as cant_nc
FROM ventas v
WHERE v.serie = 'B001' AND v.numero = 126 AND v.id_empresa = 12
ORDER BY v.id_venta DESC;

-- ============================================
-- LIMPIEZA: Eliminar Duplicados Antiguos (CUIDADO!)
-- ============================================

-- IMPORTANTE: Solo ejecutar después de verificar cuál mantener
-- Mantener el más reciente (id_venta más alto)

-- Ver qué se va a eliminar (SIN ELIMINAR AÚN)
SELECT 
    v.id_venta,
    v.serie,
    v.numero,
    v.fecha_emision,
    'SE ELIMINARÁ' as accion
FROM ventas v
WHERE v.serie = 'B001' 
  AND v.numero = 126 
  AND v.id_empresa = 12
  AND v.id_venta < (
      SELECT MAX(id_venta) 
      FROM ventas 
      WHERE serie = 'B001' AND numero = 126 AND id_empresa = 12
  );

-- Si estás seguro, descomentar para eliminar:
/*
DELETE FROM ventas
WHERE serie = 'B001' 
  AND numero = 126 
  AND id_empresa = 12
  AND id_venta < (
      SELECT MAX(id_venta) 
      FROM ventas 
      WHERE serie = 'B001' AND numero = 126 AND id_empresa = 12
  );
*/

-- ============================================
-- RECOMENDACIÓN
-- ============================================

-- 1. NO eliminar duplicados si tienen NC asociadas
-- 2. Cambiar la serie a B002 para evitar más duplicados
-- 3. La consulta ahora usa ORDER BY + LIMIT 1 para obtener el más reciente

-- ============================================
-- VERIFICACIÓN FINAL
-- ============================================

-- Ver que ya no hay duplicados (o están identificados)
SELECT 
    serie, 
    numero, 
    COUNT(*) as cantidad
FROM ventas
WHERE id_empresa = 12
GROUP BY serie, numero
HAVING COUNT(*) > 1
ORDER BY cantidad DESC, serie, numero;
