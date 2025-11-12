-- Modificaciones para CrediYango: Separar fecha de entrega y fecha de inicio de pagos
-- Ejecutar este script para agregar los nuevos campos necesarios

-- Agregar campos para fecha de entrega y fecha de inicio de pagos calculada
ALTER TABLE financiamiento 
ADD COLUMN fecha_entrega DATE NULL,
ADD COLUMN fecha_inicio_pagos_calculada DATE NULL;

-- Agregar índices para mejorar el rendimiento
CREATE INDEX idx_financiamiento_fecha_entrega ON financiamiento(fecha_entrega);
CREATE INDEX idx_financiamiento_fecha_inicio_pagos ON financiamiento(fecha_inicio_pagos_calculada);

