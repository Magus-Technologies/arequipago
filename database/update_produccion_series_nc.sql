-- ============================================
-- ACTUALIZAR SERIES DE NOTA DE CRÉDITO EN PRODUCCIÓN
-- ============================================
-- Estado actual: F001 con número 1
-- Estado deseado: BC01 con número 1
-- ============================================

-- Verificar estado actual
SELECT id_empresa, id_tido, sucursal, serie, numero 
FROM documentos_empresas 
WHERE id_empresa = 12 AND id_tido IN (3, 4)
ORDER BY sucursal, id_tido;

-- Actualizar serie de Nota de Crédito en sucursal 1
UPDATE documentos_empresas 
SET serie = 'BC01'
WHERE id_empresa = 12 AND id_tido = 3 AND sucursal = 1;

-- Actualizar serie de Nota de Crédito en sucursal 2
UPDATE documentos_empresas 
SET serie = 'BC02'
WHERE id_empresa = 12 AND id_tido = 3 AND sucursal = 2;

-- Verificar cambios
SELECT id_empresa, id_tido, sucursal, serie, numero 
FROM documentos_empresas 
WHERE id_empresa = 12 AND id_tido IN (3, 4)
ORDER BY sucursal, id_tido;

-- Resultado esperado:
-- id_tido=3, sucursal=1, serie='BC01', numero=1
-- id_tido=3, sucursal=2, serie='BC02', numero=1
-- id_tido=4, sucursal=1, serie='F001', numero=1 (sin cambios)
-- id_tido=4, sucursal=2, serie='F002', numero=1 (sin cambios)
