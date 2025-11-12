-- Agregar campo para guardar la ruta del PDF de la boleta
ALTER TABLE pagos_financiamiento 
ADD COLUMN ruta_pdf VARCHAR(255) NULL DEFAULT NULL AFTER concepto;

-- Comentario: Este campo guardará la ruta del archivo PDF generado para cada pago
-- Ejemplo: 'files/compartir/comprobante_68fac41e2cda3.pdf'
