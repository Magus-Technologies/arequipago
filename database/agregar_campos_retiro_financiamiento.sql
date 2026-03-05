 -- ============================================================================
-- AGREGAR CAMPOS PARA RETIRO DE FINANCIAMIENTOS
-- Planes: CrediGo auto Grupo 3 (ID 19) y CrediGo Autos Grupo 4 (ID 38)
-- ============================================================================

ALTER TABLE financiamiento 
ADD COLUMN estado_retiro ENUM('activo', 'retirado') DEFAULT 'activo' AFTER estado, 
ADD COLUMN fecha_retiro DATETIME NULL AFTER estado_retiro, 
ADD COLUMN monto_penalidad DECIMAL(10,2) NULL AFTER fecha_retiro, 
ADD COLUMN meses_permanencia INT NULL AFTER monto_penalidad, 
ADD COLUMN usuario_proceso_retiro INT NULL AFTER meses_permanencia, 
ADD COLUMN observaciones_retiro TEXT NULL AFTER usuario_proceso_retiro;
