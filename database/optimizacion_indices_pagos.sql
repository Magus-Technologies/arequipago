-- ========================================
-- OPTIMIZACIÓN DE ÍNDICES PARA PAGOS_FINANCIAMIENTO
-- ========================================
-- Este script agrega índices para mejorar el rendimiento de las consultas
-- Ejecutar en la base de datos para acelerar las búsquedas

-- Índice en id_conductor (usado en LEFT JOIN)
ALTER TABLE `pagos_financiamiento` 
ADD INDEX `idx_id_conductor` (`id_conductor`) USING BTREE;

-- Índice en id_cliente (usado en LEFT JOIN)
ALTER TABLE `pagos_financiamiento` 
ADD INDEX `idx_id_cliente` (`id_cliente`) USING BTREE;

-- Índice en id_asesor (usado en LEFT JOIN)
ALTER TABLE `pagos_financiamiento` 
ADD INDEX `idx_id_asesor` (`id_asesor`) USING BTREE;

-- Índice en fecha_pago (usado en ORDER BY y filtros de fecha)
ALTER TABLE `pagos_financiamiento` 
ADD INDEX `idx_fecha_pago` (`fecha_pago`) USING BTREE;

-- Índice en estado (usado en WHERE)
ALTER TABLE `pagos_financiamiento` 
ADD INDEX `idx_estado` (`estado`) USING BTREE;

-- Índice compuesto para optimizar consultas con estado y fecha
ALTER TABLE `pagos_financiamiento` 
ADD INDEX `idx_estado_fecha` (`estado`, `fecha_pago`) USING BTREE;

-- ========================================
-- ÍNDICES PARA TABLA CONDUCTORES
-- ========================================

-- Índice en nro_documento (usado en búsquedas)
ALTER TABLE `conductores` 
ADD INDEX `idx_nro_documento` (`nro_documento`) USING BTREE;

-- Índice en numUnidad (usado en búsquedas)
ALTER TABLE `conductores` 
ADD INDEX `idx_numUnidad` (`numUnidad`) USING BTREE;

-- ========================================
-- ÍNDICES PARA TABLA CLIENTES_FINANCIAR
-- ========================================

-- Índice en n_documento (usado en búsquedas)
ALTER TABLE `clientes_financiar` 
ADD INDEX `idx_n_documento` (`n_documento`) USING BTREE;

-- ========================================
-- VERIFICAR ÍNDICES EXISTENTES
-- ========================================
-- Para verificar que los índices se crearon correctamente:
-- SHOW INDEX FROM pagos_financiamiento;
-- SHOW INDEX FROM conductores;
-- SHOW INDEX FROM clientes_financiar;
