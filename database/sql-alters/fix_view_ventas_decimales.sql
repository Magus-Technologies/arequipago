-- =====================================================
-- CORREGIR VISTA view_ventas - Decimales en Subtotal e IGV
-- =====================================================
-- Problema: La vista muestra subtotal/IGV con muchos decimales
-- Causa: No redondea a 2 decimales
-- Solución: Agregar ROUND(..., 2) a los cálculos

DROP VIEW IF EXISTS `view_ventas`;

CREATE VIEW `view_ventas` AS 
SELECT 
    v.id_venta AS cod_v,
    CONCAT(ds.abreviatura, ' | ', v.serie, ' - ', v.numero) AS sn_v,
    CONCAT(c.documento, ' | ', c.datos) AS datos_cl,
    
    -- CORREGIDO: Subtotal con redondeo a 2 decimales
    CONCAT(
        IF(v.moneda = 1, 'S/ ', '$ '),
        ROUND(
            IF(v.apli_igv = '1', 
                v.total / (v.igv + 1),  -- IGV ya está en decimal (0.18)
                v.total
            ),
            2  -- Redondear a 2 decimales
        )
    ) AS subtotal,
    
    -- CORREGIDO: IGV con redondeo a 2 decimales
    CONCAT(
        IF(v.moneda = 1, 'S/ ', '$ '),
        ROUND(
            IF(v.apli_igv = '1',
                (v.total / (v.igv + 1)) * v.igv,  -- IGV = Subtotal * tasa_igv
                0
            ),
            2  -- Redondear a 2 decimales
        )
    ) AS igv_v,
    
    CONCAT(v.enviado_sunat, '-', v.id_tido, '-', v.id_venta) AS doc_ventae,
    CONCAT(v.id_venta, '--', IFNULL(vs.nombre_xml, '-')) AS id_venta,
    v.fecha_emision AS fecha_emision,
    ds.abreviatura AS abreviatura,
    v.apli_igv AS apli_igv,
    v.igv AS igv,
    v.id_tido AS id_tido,
    v.serie AS serie,
    v.numero AS numero,
    c.documento AS documento,
    c.datos AS datos,
    CONCAT(IF(v.moneda = 1, 'S/ ', '$ '), v.total) AS total,
    v.estado AS estado,
    v.enviado_sunat AS enviado_sunat,
    IFNULL(vs.nombre_xml, '-') AS nombre_xml
FROM ventas v
LEFT JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
LEFT JOIN ventas_sunat vs ON v.id_venta = vs.id_venta
WHERE v.id_empresa = '12' AND v.sucursal = '1'
ORDER BY v.fecha_emision, v.numero;

-- Verificar que la vista se creó correctamente
SELECT 'Vista view_ventas corregida exitosamente' AS resultado;

-- Probar con la venta 610
SELECT cod_v, subtotal, igv_v, total 
FROM view_ventas 
WHERE cod_v = 610;

