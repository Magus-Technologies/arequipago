-- ============================================
-- LIMPIAR TABLAS DE NOTAS ELECTRÓNICAS
-- ============================================
-- Usar SOLO en desarrollo/local antes de subir a producción
-- En producción las tablas estarán vacías
-- ============================================

-- 1. Limpiar notas electrónicas SUNAT (XMLs, hash, qr)
TRUNCATE TABLE notas_electronicas_sunat;

-- 2. Limpiar notas electrónicas (registros principales)
TRUNCATE TABLE notas_electronicas;

-- 3. Resetear el correlativo de NC a 1 (para empezar desde BC01-1 en producción)
UPDATE documentos_empresas 
SET numero = 1 
WHERE id_empresa = 12 AND id_tido = 3;

-- 4. Verificar que quedó limpio
SELECT 'Notas Electrónicas' AS tabla, COUNT(*) AS registros FROM notas_electronicas
UNION ALL
SELECT 'Notas Electrónicas SUNAT' AS tabla, COUNT(*) AS registros FROM notas_electronicas_sunat
UNION ALL
SELECT 'Correlativo NC' AS info, numero AS registros FROM documentos_empresas WHERE id_empresa = 12 AND id_tido = 3;
