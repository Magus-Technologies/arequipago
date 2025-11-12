-- ============================================
-- FIX: Optimización de rendimiento para pagos
-- ============================================
-- Problema: Algunos pagos tardan 50 segundos en aprobarse
-- Causa: Falta de índices causa table locks y scans lentos
-- ============================================

-- 1. Agregar índice en estado de pagos_financiamiento
-- Esto acelera las consultas WHERE estado = 0
ALTER TABLE `pagos_financiamiento` 
ADD INDEX `idx_estado` (`estado` ASC);

-- 2. Agregar índice compuesto para consultas de pagos pendientes
-- Esto optimiza: WHERE estado = 0 ORDER BY fecha_pago DESC
ALTER TABLE `pagos_financiamiento` 
ADD INDEX `idx_estado_fecha` (`estado` ASC, `fecha_pago` DESC);

-- 3. Agregar índice en id_conductor para JOINs
ALTER TABLE `pagos_financiamiento` 
ADD INDEX `idx_id_conductor` (`id_conductor` ASC);

-- 4. Agregar índice en id_cliente para JOINs
ALTER TABLE `pagos_financiamiento` 
ADD INDEX `idx_id_cliente` (`id_cliente` ASC);

-- 5. Agregar índice en id_asesor para JOINs
ALTER TABLE `pagos_financiamiento` 
ADD INDEX `idx_id_asesor` (`id_asesor` ASC);

-- 6. Optimizar índice en cuotas_financiamiento para batch updates
-- Ya existe idx_cuota_puntos, pero agreguemos uno más específico
ALTER TABLE `cuotas_financiamiento` 
ADD INDEX `idx_id_financiamiento_estado` (`id_financiamiento` ASC, `estado` ASC, `estado_eliminado` ASC);

-- 7. Verificar que el índice de puntos_aplicados existe
-- (Ya existe según el schema: idx_puntos_aplicados)

-- ============================================
-- Verificación de índices creados
-- ============================================
SHOW INDEX FROM `pagos_financiamiento`;
SHOW INDEX FROM `cuotas_financiamiento`;

-- ============================================
-- Opcional: Analizar tablas para actualizar estadísticas
-- ============================================
ANALYZE TABLE `pagos_financiamiento`;
ANALYZE TABLE `cuotas_financiamiento`;
ANALYZE TABLE `pagos_pendientes_financiamientos`;
ANALYZE TABLE `financiamiento`;

-- ============================================
-- Notas:
-- ============================================
-- Estos índices mejorarán:
-- 1. Consultas de pagos pendientes (estado = 0)
-- 2. JOINs con conductores, clientes y asesores
-- 3. Batch updates de cuotas
-- 4. Verificación de foreign keys
-- 
-- Resultado esperado:
-- - Reducción de locks de tabla
-- - Queries más rápidas (< 100ms)
-- - Aprobaciones consistentemente rápidas
-- ============================================
