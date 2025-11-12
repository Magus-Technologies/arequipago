-- ============================================
-- Script para Corregir la Vista view_ventas
-- Problema: Devuelve id_venta NULL cuando no hay registro en ventas_sunat
-- Solución: Usar IFNULL para manejar valores NULL
-- ============================================

USE magusqao_arequipa;

-- Eliminar la vista existente
DROP VIEW IF EXISTS `view_ventas`;

-- Crear la vista corregida
CREATE ALGORITHM = UNDEFINED SQL SECURITY INVOKER VIEW `view_ventas` AS 
SELECT 
    `v`.`id_venta` AS `cod_v`,
    CONCAT(`ds`.`abreviatura`, ' | ', `v`.`serie`, ' - ', `v`.`numero`) AS `sn_v`,
    CONCAT(`c`.`documento`, ' | ', `c`.`datos`) AS `datos_cl`,
    CONCAT(
        IF(`v`.`moneda` = 1, 'S/ ', '$ '),
        IF(`v`.`apli_igv` = '1', `v`.`total` / (`v`.`igv` + 1), `v`.`total`)
    ) AS `subtotal`,
    CONCAT(
        IF(`v`.`moneda` = 1, 'S/ ', '$ '),
        IF(`v`.`apli_igv` = '1', `v`.`total` / (`v`.`igv` + 1) * `v`.`igv`, 0)
    ) AS `igv_v`,
    CONCAT(`v`.`enviado_sunat`, '-', `v`.`id_tido`, '-', `v`.`id_venta`) AS `doc_ventae`,
    
    -- CORREGIDO: Usar IFNULL para evitar que id_venta sea NULL
    CONCAT(`v`.`id_venta`, '--', IFNULL(`vs`.`nombre_xml`, '-')) AS `id_venta`,
    
    `v`.`fecha_emision` AS `fecha_emision`,
    `ds`.`abreviatura` AS `abreviatura`,
    `v`.`apli_igv` AS `apli_igv`,
    `v`.`igv` AS `igv`,
    `v`.`id_tido` AS `id_tido`,
    `v`.`serie` AS `serie`,
    `v`.`numero` AS `numero`,
    `c`.`documento` AS `documento`,
    `c`.`datos` AS `datos`,
    CONCAT(IF(`v`.`moneda` = 1, 'S/ ', '$ '), `v`.`total`) AS `total`,
    `v`.`estado` AS `estado`,
    `v`.`enviado_sunat` AS `enviado_sunat`,
    
    -- CORREGIDO: Usar IFNULL para nombre_xml
    IFNULL(`vs`.`nombre_xml`, '-') AS `nombre_xml`
    
FROM `ventas` `v`
LEFT JOIN `documentos_sunat` `ds` ON `v`.`id_tido` = `ds`.`id_tido`
LEFT JOIN `clientes` `c` ON `v`.`id_cliente` = `c`.`id_cliente`
LEFT JOIN `ventas_sunat` `vs` ON `v`.`id_venta` = `vs`.`id_venta`
WHERE `v`.`id_empresa` = '12' 
  AND `v`.`sucursal` = '1'
ORDER BY `v`.`fecha_emision`, `v`.`numero`;

-- Verificar que la vista se creó correctamente
SELECT 'Vista view_ventas corregida exitosamente' AS resultado;

-- Probar con la venta problemática
SELECT id_venta, cod_v, sn_v, datos_cl, fecha_emision
FROM view_ventas
WHERE serie = 'B001' AND numero = '137';
