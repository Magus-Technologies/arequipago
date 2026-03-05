-- ============================================
-- FIX: Trigger que incrementa TODAS las series
-- ============================================
-- Problema: El trigger ti_notas_e incrementa todas las series
-- sin filtrar por serie específica
-- ============================================

-- Opción 1: ELIMINAR el trigger (el código PHP ya incrementa)
DROP TRIGGER IF EXISTS ti_notas_e;

-- Verificar que se eliminó
SHOW TRIGGERS LIKE 'notas_electronicas';
