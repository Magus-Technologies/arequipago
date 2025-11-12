-- Script para agregar funcionalidad de Mora Pendiente
-- Fecha: 2025-11-04
-- Descripción: Permite registrar moras como pendientes cuando el cliente paga solo la cuota

-- Agregar columna estado_mora a la tabla detalle_pago_financiamiento
ALTER TABLE detalle_pago_financiamiento 
ADD COLUMN estado_mora ENUM('pagada', 'pendiente') DEFAULT 'pagada' 
COMMENT 'Estado de la mora: pagada (se cobró) o pendiente (se dejó para después)';

-- Agregar columna monto_mora_original para guardar el monto que se debe cuando está pendiente
ALTER TABLE detalle_pago_financiamiento 
ADD COLUMN monto_mora_original DECIMAL(10,2) NULL DEFAULT NULL 
COMMENT 'Monto original de la mora cuando está pendiente';

-- Crear índice para consultas rápidas de moras pendientes
CREATE INDEX idx_estado_mora ON detalle_pago_financiamiento(estado_mora);