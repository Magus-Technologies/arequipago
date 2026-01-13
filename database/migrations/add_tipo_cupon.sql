-- Migración: Agregar campo tipo_cupon a tabla cupones
-- Fecha: 2026-01-08
-- Descripción: Permite distinguir entre cupones públicos (acceso global) y exclusivos (solo logueados)

ALTER TABLE cupones
ADD COLUMN tipo_cupon ENUM('publico', 'exclusivo') NOT NULL DEFAULT 'exclusivo'
AFTER activo;

-- Crear índice para mejorar consultas de cupones públicos
CREATE INDEX idx_tipo_cupon_activo ON cupones(tipo_cupon, activo);

-- Comentario descriptivo
ALTER TABLE cupones
MODIFY COLUMN tipo_cupon ENUM('publico', 'exclusivo') NOT NULL DEFAULT 'exclusivo'
COMMENT 'Tipo de cupón: publico (visible para todos sin login) o exclusivo (solo conductores/clientes logueados)';
