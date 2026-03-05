-- Script para corregir el tamaño de la columna 'ruta' en notas_venta_inscripcion
-- Problema: La columna era VARCHAR(50) y las rutas de archivos PDF excedían este límite
-- Solución: Ampliar a VARCHAR(255) para soportar rutas completas

USE magusqao_arequipa;

ALTER TABLE notas_venta_inscripcion 
MODIFY COLUMN ruta VARCHAR(255);

-- Verificar el cambio
DESCRIBE notas_venta_inscripcion;
