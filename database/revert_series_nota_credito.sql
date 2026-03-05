-- ============================================
-- REVERTIR: Cambio de serie BC01 a B001
-- ============================================
-- La serie puede ser cualquiera que empiece con B o F
-- No es necesario usar BC01/FC01 específicamente
-- SUNAT solo valida que la primera letra coincida con el tipo de documento
-- ============================================

-- Revertir serie de NC en sucursal 1 a B001
UPDATE documentos_empresas 
SET serie = 'B001' 
WHERE id_empresa = 12 AND id_tido = 3 AND sucursal = 1;

-- Revertir serie de NC en sucursal 2 a B002
UPDATE documentos_empresas 
SET serie = 'B002' 
WHERE id_empresa = 12 AND id_tido = 3 AND sucursal = 2;

-- Verificar las series
SELECT id_empresa, id_tido, sucursal, serie, numero 
FROM documentos_empresas 
WHERE id_empresa = 12 AND id_tido IN (1,2,3,4)
ORDER BY sucursal, id_tido;
