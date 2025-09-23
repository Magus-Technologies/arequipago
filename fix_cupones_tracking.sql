-- Corregir la tabla cupones_uso_tracking para permitir NULL en id_conductor
-- Esto permite que se puedan registrar usos tanto para conductores como para clientes

-- Primero, eliminar la restricción de clave foránea
ALTER TABLE `cupones_uso_tracking`
DROP FOREIGN KEY `cupones_uso_tracking_ibfk_2`;

-- Modificar la columna id_conductor para permitir NULL
ALTER TABLE `cupones_uso_tracking`
MODIFY COLUMN `id_conductor` int(11) DEFAULT NULL;

-- Volver a agregar la restricción de clave foránea
ALTER TABLE `cupones_uso_tracking`
ADD CONSTRAINT `cupones_uso_tracking_ibfk_2`
FOREIGN KEY (`id_conductor`) REFERENCES `conductores` (`id_conductor`) ON DELETE CASCADE;

-- Agregar índice para id_cliente también (para mejorar consultas)
ALTER TABLE `cupones_uso_tracking`
ADD KEY `idx_cupones_uso_cliente` (`id_cliente`);