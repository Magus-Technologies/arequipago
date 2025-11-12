-- Agregar campo pago_inscripcion a la tabla beneficios
ALTER TABLE `beneficios` 
ADD COLUMN `pago_inscripcion` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Monto del pago de inscripción' 
AFTER `cuota_mensual`;

-- Verificar que el campo se agregó correctamente
DESCRIBE beneficios;