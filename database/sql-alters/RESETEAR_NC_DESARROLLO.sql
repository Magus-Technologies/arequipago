-- ============================================
-- RESETEAR NOTAS DE CRÉDITO EN DESARROLLO
-- ============================================
-- FECHA: 2026-02-02
-- PROPOSITO: Limpiar NC de prueba y resetear correlativos
-- ⚠️ SOLO EJECUTAR EN DESARROLLO, NUNCA EN PRODUCCIÓN
-- ============================================

-- PASO 1: Ver qué NC existen antes de borrar
-- ============================================
SELECT
    ne.nota_id,
    ne.serie,
    ne.numero,
    ne.monto,
    ne.fecha,
    nes.nombre_xml,
    ne.estado_sunat,
    v.serie AS venta_serie,
    v.numero AS venta_numero
FROM notas_electronicas ne
LEFT JOIN notas_electronicas_sunat nes ON ne.nota_id = nes.id_notas_electronicas
LEFT JOIN ventas v ON ne.id_venta = v.id_venta
WHERE ne.id_empresa = 12
ORDER BY ne.nota_id DESC;

-- ============================================
-- PASO 2: BORRAR DATOS DE NC
-- ============================================

-- 2.1: Borrar XMLs y datos SUNAT
DELETE FROM notas_electronicas_sunat
WHERE id_notas_electronicas IN (
    SELECT nota_id
    FROM notas_electronicas
    WHERE id_empresa = 12
);

-- 2.2: Borrar las NC
DELETE FROM notas_electronicas
WHERE id_empresa = 12;

-- ============================================
-- PASO 3: RESETEAR CORRELATIVOS POR SERIE
-- ============================================

-- 3.1: Resetear BC01 (Notas de Crédito de Boletas)
UPDATE documentos_empresas
SET numero = 1
WHERE id_empresa = 12
  AND id_tido = 3           -- Nota de Crédito
  AND serie = 'BC01'        -- Serie para Boletas
  AND sucursal = 1;

-- 3.2: Resetear FC01 (Notas de Crédito de Facturas)
UPDATE documentos_empresas
SET numero = 1
WHERE id_empresa = 12
  AND id_tido = 3           -- Nota de Crédito
  AND serie = 'FC01'        -- Serie para Facturas
  AND sucursal = 1;

-- ============================================
-- PASO 4: VERIFICAR QUE SE RESETEO CORRECTAMENTE
-- ============================================

SELECT
    id_empresa,
    id_tido,
    serie,
    numero,
    sucursal,
    CASE id_tido
        WHEN 1 THEN 'Boleta'
        WHEN 2 THEN 'Factura'
        WHEN 3 THEN 'Nota de Crédito'
        WHEN 4 THEN 'Nota de Débito'
    END AS tipo_documento
FROM documentos_empresas
WHERE id_empresa = 12
  AND id_tido = 3
ORDER BY serie;

-- ============================================
-- RESULTADO ESPERADO:
-- ============================================
-- id_empresa | id_tido | serie | numero | tipo_documento
-- -----------|---------|-------|--------|----------------
-- 12         | 3       | BC01  | 1      | Nota de Crédito
-- 12         | 3       | FC01  | 1      | Nota de Crédito
-- ============================================

-- ============================================
-- PASO 5: BORRAR ARCHIVOS XML DEL DISCO (MANUAL)
-- ============================================
-- ⚠️ Esto debes hacerlo manualmente en el explorador de archivos:
--
-- Ubicación: C:\laragon\www\arequipago\files\facturacion\xml\20612112763\
--
-- Borrar archivos:
-- - 20612112763-07-BC01-1.xml
-- - 20612112763-07-BC01-2.xml
-- - 20612112763-07-BC01-3.xml
-- - 20612112763-07-BC01-4.xml
-- - 20612112763-07-FC01-*.xml (si existen)
--
-- Ubicación CDR: C:\laragon\www\arequipago\files\facturacion\cdr\20612112763\
-- Borrar archivos:
-- - R-20612112763-07-BC01-*.zip (si existen)
-- ============================================

-- ============================================
-- ALTERNATIVA: Si solo quieres resetear BC01
-- ============================================
/*
-- Solo BC01 (Boletas)
UPDATE documentos_empresas
SET numero = 1
WHERE id_empresa = 12
  AND id_tido = 3
  AND serie = 'BC01';

-- Borrar solo NC de boletas
DELETE FROM notas_electronicas_sunat
WHERE id_notas_electronicas IN (
    SELECT nota_id
    FROM notas_electronicas
    WHERE id_empresa = 12
      AND serie = 'BC01'
);

DELETE FROM notas_electronicas
WHERE id_empresa = 12
  AND serie = 'BC01';
*/

-- ============================================
-- NOTAS IMPORTANTES:
-- ============================================
-- 1. Ejecutar en orden (PASO 1 → PASO 2 → PASO 3 → PASO 4)
-- 2. Verificar PASO 4 para confirmar que número = 1
-- 3. Borrar XMLs manualmente del disco (PASO 5)
-- 4. NUNCA ejecutar en producción sin backup
-- 5. Si tienes NC enviadas a SUNAT, consultar con contador primero
-- ============================================
