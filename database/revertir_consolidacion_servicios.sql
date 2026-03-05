-- =====================================================
-- SCRIPT DE REVERSIÓN - CONSOLIDACIÓN DE SERVICIOS
-- =====================================================
-- Este script revierte la consolidación de servicios
-- y restaura los grupos originales
-- =====================================================

-- IMPORTANTE: Reemplaza @id_plan_servicios con el ID real del plan "SERVICIOS"
-- Puedes obtenerlo con: SELECT idplan_financiamiento FROM planes_financiamiento WHERE nombre_plan = 'SERVICIOS';

SET @id_plan_servicios = (SELECT idplan_financiamiento FROM planes_financiamiento WHERE nombre_plan = 'SERVICIOS' LIMIT 1);

-- Paso 1: Reactivar los planes antiguos
UPDATE planes_financiamiento 
SET estado = 'activo' 
WHERE idplan_financiamiento IN (44, 46, 47, 48);

-- Paso 2: Restaurar financiamientos a sus planes originales
-- (Esto es complicado porque no sabemos qué financiamiento pertenecía a qué plan)
-- Por seguridad, NO revertimos automáticamente los financiamientos
-- Se debe hacer manualmente si es necesario

-- Paso 3: Desactivar el plan "SERVICIOS"
UPDATE planes_financiamiento 
SET estado = 'inactivo' 
WHERE idplan_financiamiento = @id_plan_servicios;

-- Paso 4: Eliminar las variantes del plan "SERVICIOS"
-- CUIDADO: Esto eliminará las variantes, solo ejecutar si estás seguro
-- DELETE FROM grupos_variantes WHERE idplan_financiamiento = @id_plan_servicios;

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
SELECT 'PLANES REACTIVADOS:' as mensaje;
SELECT idplan_financiamiento, nombre_plan, estado 
FROM planes_financiamiento 
WHERE idplan_financiamiento IN (44, 46, 47, 48);

SELECT 'PLAN SERVICIOS DESACTIVADO:' as mensaje;
SELECT idplan_financiamiento, nombre_plan, estado 
FROM planes_financiamiento 
WHERE idplan_financiamiento = @id_plan_servicios;

-- =====================================================
-- ADVERTENCIA:
-- =====================================================
-- Los financiamientos que se actualizaron al plan "SERVICIOS"
-- NO se revierten automáticamente. Debes actualizarlos manualmente
-- si necesitas restaurarlos a sus planes originales.
-- =====================================================
