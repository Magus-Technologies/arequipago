-- =====================================================
-- Script: Agregar campo es_entrega_especial
-- Fecha: 2025-11-07
-- Descripción: Campo para marcar financiamientos del plan
--              EDITABLE (ID 42) que incluyen entrega de vehículo
-- =====================================================

-- Agregar columna es_entrega_especial a tabla financiamiento
-- Este campo marca si un financiamiento editable incluye entrega de vehículo


-- Verificar que el campo fue agregado
SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT, COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'financiamiento'
  AND COLUMN_NAME = 'es_entrega_especial';

-- =====================================================
-- Comandos útiles para verificación:
-- =====================================================

-- Ver financiamientos del plan editable (ID 42) con entrega especial
/*
SELECT
    idfinanciamiento,
    grupo_financiamiento,
    nombre_personalizado,
    es_entrega_especial,
    estado_entrega,
    idproductosv2,
    monto_total
FROM `financiamiento`
WHERE `grupo_financiamiento` = '42'
ORDER BY idfinanciamiento DESC;
*/

-- Actualizar financiamientos existentes si es necesario
-- (ejecutar manualmente solo si hay financiamientos que deben marcarse)
/*
UPDATE `financiamiento`
SET `es_entrega_especial` = 1
WHERE `grupo_financiamiento` = '42'
  AND `idproductosv2` IN (
    SELECT idproductosv2 FROM productosv2
    WHERE LOWER(categoria) LIKE '%vehicul%' OR LOWER(categoria) LIKE '%moto%'
  );
*/
