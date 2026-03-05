-- ============================================
-- FIX: Correlativos Independientes para NC
-- ============================================
-- Problema: BC01 y FC01 comparten el mismo correlativo
-- Solución: Crear filas separadas para cada serie
-- ============================================

-- PASO 1: Verificar estado actual
SELECT 'ANTES DEL CAMBIO' AS estado;
SELECT id_empresa, id_tido, sucursal, serie, numero 
FROM documentos_empresas 
WHERE id_empresa = 12 AND id_tido = 3
ORDER BY sucursal, serie;

-- PASO 2: Eliminar la fila actual de NC (id_tido=3)
DELETE FROM documentos_empresas 
WHERE id_empresa = 12 AND id_tido = 3;

-- PASO 3: Insertar filas separadas para BC01 y FC01
-- Sucursal 1
INSERT INTO documentos_empresas (id_empresa, id_tido, sucursal, serie, numero)
VALUES 
(12, 3, 1, 'BC01', 1),  -- NC de Boleta
(12, 3, 1, 'FC01', 1);  -- NC de Factura

-- Sucursal 2
INSERT INTO documentos_empresas (id_empresa, id_tido, sucursal, serie, numero)
VALUES 
(12, 3, 2, 'BC02', 1),  -- NC de Boleta
(12, 3, 2, 'FC02', 1);  -- NC de Factura

-- PASO 4: Verificar resultado
SELECT 'DESPUÉS DEL CAMBIO' AS estado;
SELECT id_empresa, id_tido, sucursal, serie, numero 
FROM documentos_empresas 
WHERE id_empresa = 12 AND id_tido = 3
ORDER BY sucursal, serie;

-- Resultado esperado:
-- id_empresa=12, id_tido=3, sucursal=1, serie='BC01', numero=1
-- id_empresa=12, id_tido=3, sucursal=1, serie='FC01', numero=1
-- id_empresa=12, id_tido=3, sucursal=2, serie='BC02', numero=1
-- id_empresa=12, id_tido=3, sucursal=2, serie='FC02', numero=1
