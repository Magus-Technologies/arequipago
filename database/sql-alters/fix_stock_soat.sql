-- ============================================
-- FIX: Corregir stock de productos SOAT
-- ============================================
-- Fecha: 2 de febrero de 2026
-- Descripción: Los productos SOAT son tipo "Intangible" y no deben tener stock.
--              Este script corrige el stock a 0 si fue descontado por error.

-- Verificar estado actual
SELECT 
    idproductosv2,
    nombre,
    codigo,
    cantidad,
    tipo_producto
FROM productosv2
WHERE tipo_producto = 'Intangible';

-- Corregir stock de productos Intangibles a 0
UPDATE productosv2 
SET cantidad = 0 
WHERE tipo_producto = 'Intangible' 
AND cantidad != 0;

-- Verificar corrección
SELECT 
    idproductosv2,
    nombre,
    codigo,
    cantidad,
    tipo_producto
FROM productosv2
WHERE tipo_producto = 'Intangible';

-- ============================================
-- NOTA: Después de ejecutar este script, el método
-- actualizarStock() en Productov2.php ya NO descontará
-- stock de productos Intangibles en futuras ventas.
-- ============================================
