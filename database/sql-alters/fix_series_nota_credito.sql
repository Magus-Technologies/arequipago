-- ============================================
-- FIX: Cambiar serie de Nota de Crédito a BC01
-- ============================================
-- Problema: Serie F001 se usa para NC de Boletas y Facturas
-- Según SUNAT:
--   - NC de Factura → Serie con F (F001, FC01)
--   - NC de Boleta → Serie con B (B001, BC01)
--
-- Solución: Cambiar F001 a BC01 (el código dinámicamente usará BC01 o FC01)
-- ============================================

-- Actualizar serie de NC en sucursal 1 a BC01
UPDATE documentos_empresas 
SET serie = 'BC01' 
WHERE id_empresa = 12 AND id_tido = 3 AND sucursal = 1;

-- Actualizar serie de NC en sucursal 2 a BC02
UPDATE documentos_empresas 
SET serie = 'BC02' 
WHERE id_empresa = 12 AND id_tido = 3 AND sucursal = 2;

-- Verificar las series
SELECT id_empresa, id_tido, sucursal, serie, numero 
FROM documentos_empresas 
WHERE id_empresa = 12 AND id_tido IN (1,2,3,4)
ORDER BY sucursal, id_tido;
