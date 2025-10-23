-- SCRIPT PARA CORREGIR FINANCIAMIENTOS QUE DEBERÍAN ESTAR APROBADOS
-- EJECUTAR SOLO DESPUÉS DE VERIFICAR CON diagnostico_puntaje.sql

-- OPCIÓN 1: Actualizar financiamientos antiguos (creados antes de implementar aprobación)
-- que están activos pero tienen aprobado = 0 o NULL
-- Estos deberían estar aprobados porque ya están en uso

UPDATE financiamiento
SET aprobado = 1
WHERE estado IN ('En Progreso', 'En progreso', 'Finalizado')
AND estado_eliminado = 0
AND (aprobado = 0 OR aprobado IS NULL)
AND fecha_creacion < '2025-01-18';  -- Ajusta esta fecha según cuando implementaste el sistema de aprobación

-- VERIFICAR CUÁNTOS SE ACTUALIZARÍAN (ejecutar esto ANTES del UPDATE):
-- SELECT COUNT(*) as total_a_actualizar
-- FROM financiamiento
-- WHERE estado IN ('En Progreso', 'En progreso', 'Finalizado')
-- AND estado_eliminado = 0
-- AND (aprobado = 0 OR aprobado IS NULL)
-- AND fecha_creacion < '2025-01-18';

-- DESPUÉS DE ACTUALIZAR, recalcular todos los puntajes ejecutando:
-- Ir a la vista Credit Score y hacer clic en "Actualizar Puntajes"
