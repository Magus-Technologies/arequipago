-- =====================================================
-- AGREGAR CAMPOS PARA FACTURACIÓN EN PAGOS CAJA AREQUIPA
-- =====================================================
-- Estos campos NO afectan la app externa que registra pagos
-- Son campos adicionales NULL por defecto

ALTER TABLE pagos_caja_arequipa 
ADD COLUMN id_venta INT NULL DEFAULT NULL COMMENT 'ID de la venta/factura generada' AFTER estado,
ADD COLUMN facturado TINYINT(1) DEFAULT 0 COMMENT '0=No facturado, 1=Facturado' AFTER id_venta,
ADD COLUMN fecha_facturacion DATETIME NULL DEFAULT NULL COMMENT 'Fecha en que se generó la factura' AFTER facturado;

-- Agregar índices para mejorar consultas
ALTER TABLE pagos_caja_arequipa
ADD INDEX idx_id_venta (id_venta),
ADD INDEX idx_facturado (facturado);

-- Verificar cambios
DESCRIBE pagos_caja_arequipa;
