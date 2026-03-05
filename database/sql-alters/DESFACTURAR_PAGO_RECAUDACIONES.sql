-- ============================================
-- DESFACTURAR PAGO EN RECAUDACIONES
-- ============================================
-- Usar cuando una factura fue anulada en Ventas
-- pero sigue apareciendo como "facturada" en Recaudaciones
-- ============================================

-- PASO 1: Verificar el pago que quieres desfacturar
SELECT 
    id_financiamiento,
    numero_documento,
    monto_recibido,
    id_venta,
    facturado,
    fecha_facturacion
FROM pagos_caja_arequipa
WHERE id_financiamiento = 170 AND facturado = 1;

-- PASO 2: Verificar que la venta está anulada
SELECT 
    id_venta,
    serie,
    numero,
    estado,
    total
FROM ventas
WHERE id_venta = 624;
-- Si estado = 0, significa que está anulada

-- PASO 3: Desfacturar el pago (quitar la relación con la venta anulada)
UPDATE pagos_caja_arequipa
SET 
    id_venta = NULL,
    facturado = 0,
    fecha_facturacion = NULL
WHERE id_financiamiento = 170 AND id_venta = 624;

-- PASO 4: Verificar que se desfacturó correctamente
SELECT 
    id_financiamiento,
    numero_documento,
    monto_recibido,
    id_venta,
    facturado,
    fecha_facturacion
FROM pagos_caja_arequipa
WHERE id_financiamiento = 170;
-- Ahora debe mostrar: id_venta = NULL, facturado = 0, fecha_facturacion = NULL

-- ============================================
-- RESULTADO ESPERADO:
-- ============================================
-- El pago volverá a aparecer con el botón "Facturar" 
-- en el módulo de Recaudaciones
-- ============================================
