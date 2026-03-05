-- ============================================
-- CONSULTAS PARA MÓDULO DE ADJUDICACIONES
-- Base de datos: magusqao_arequipa
-- ============================================

-- ============================================
-- 1. CREAR TABLA ADJUDICACIONES
-- ============================================
CREATE TABLE IF NOT EXISTS `adjudicaciones` (
  `id_adjudicacion` INT AUTO_INCREMENT PRIMARY KEY,
  `id_financiamiento` INT NOT NULL,
  `tipo_adjudicacion` ENUM('sorteo', 'directo_con_inicial', 'crediyango') NOT NULL DEFAULT 'directo_con_inicial',
  `fecha_adjudicacion` DATE NOT NULL COMMENT 'Fecha en que ganó o se registró',
  `fecha_entrega_programada` DATE NULL COMMENT 'Fecha programada de entrega',
  `fecha_entrega_real` DATE NULL COMMENT 'Fecha real de entrega (desde financiamiento.fecha_entrega)',
  `dias_demora` INT NULL COMMENT 'Días entre adjudicación y entrega',
  `mes_sorteo` VARCHAR(7) NULL COMMENT 'Mes del sorteo (YYYY-MM)',
  `observaciones` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_financiamiento`) REFERENCES `financiamiento`(`idfinanciamiento`) ON DELETE CASCADE,
  INDEX `idx_tipo_adjudicacion` (`tipo_adjudicacion`),
  INDEX `idx_fecha_adjudicacion` (`fecha_adjudicacion`),
  INDEX `idx_mes_sorteo` (`mes_sorteo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Registro de adjudicaciones de vehículos';

-- ============================================
-- 2. CONSULTA: OBTENER TODOS LOS ADJUDICADOS
-- ============================================
-- Lista completa de financiamientos con vehículos entregados o por entregar
SELECT 
    f.idfinanciamiento,
    f.id_conductor,
    f.id_cliente,
    f.codigo_asociado,
    f.grupo_financiamiento,
    f.monto_total,
    f.cuota_inicial,
    f.cuotas,
    f.estado,
    f.fecha_inicio,
    f.fecha_fin,
    f.fecha_creacion,
    f.fecha_entrega,
    f.estado_entrega,
    f.moneda,
    -- Producto/Vehículo
    p.idproductosv2,
    p.nombre as producto_nombre,
    p.codigo as producto_codigo,
    p.categoria as producto_categoria,
    p.precio_venta,
    -- Conductor
    c.nombres as conductor_nombres,
    c.apellido_paterno as conductor_apellido_paterno,
    c.apellido_materno as conductor_apellido_materno,
    c.nro_documento as conductor_documento,
    c.foto as conductor_foto,
    -- Cliente
    cl.nombres as cliente_nombres,
    cl.apellido_paterno as cliente_apellido_paterno,
    cl.apellido_materno as cliente_apellido_materno,
    cl.n_documento as cliente_documento,
    -- Plan
    pf.nombre_plan,
    pf.tipo_vehicular,
    -- Adjudicación
    a.tipo_adjudicacion,
    a.fecha_adjudicacion,
    a.fecha_entrega_programada,
    a.dias_demora,
    a.mes_sorteo,
    -- Cuotas
    (SELECT COUNT(*) FROM cuotas_financiamiento cf WHERE cf.id_financiamiento = f.idfinanciamiento) as total_cuotas,
    (SELECT COUNT(*) FROM cuotas_financiamiento cf WHERE cf.id_financiamiento = f.idfinanciamiento AND cf.estado = 'pagado') as cuotas_pagadas,
    (SELECT COUNT(*) FROM cuotas_financiamiento cf WHERE cf.id_financiamiento = f.idfinanciamiento AND cf.estado = 'pendiente') as cuotas_pendientes,
    (SELECT COUNT(*) FROM cuotas_financiamiento cf WHERE cf.id_financiamiento = f.idfinanciamiento AND cf.fecha_vencimiento < CURDATE() AND cf.estado != 'pagado') as cuotas_vencidas,
    -- Características del vehículo (SOAT, Seguro)
    (SELECT valor_caracteristica FROM caracteristicas_producto WHERE idproductosv2 = p.idproductosv2 AND nombre_caracteristicas = 'fecha_venc_soat' LIMIT 1) as fecha_venc_soat,
    (SELECT valor_caracteristica FROM caracteristicas_producto WHERE idproductosv2 = p.idproductosv2 AND nombre_caracteristicas = 'fecha_venc_seguro' LIMIT 1) as fecha_venc_seguro,
    (SELECT valor_caracteristica FROM caracteristicas_producto WHERE idproductosv2 = p.idproductosv2 AND nombre_caracteristicas = 'chasis' LIMIT 1) as chasis,
    (SELECT valor_caracteristica FROM caracteristicas_producto WHERE idproductosv2 = p.idproductosv2 AND nombre_caracteristicas = 'vin' LIMIT 1) as vin,
    (SELECT valor_caracteristica FROM caracteristicas_producto WHERE idproductosv2 = p.idproductosv2 AND nombre_caracteristicas = 'color' LIMIT 1) as color,
    (SELECT valor_caracteristica FROM caracteristicas_producto WHERE idproductosv2 = p.idproductosv2 AND nombre_caracteristicas = 'anio' LIMIT 1) as anio
FROM financiamiento f
INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
LEFT JOIN planes_financiamiento pf ON f.grupo_financiamiento = pf.idplan_financiamiento
LEFT JOIN adjudicaciones a ON f.idfinanciamiento = a.id_financiamiento
WHERE f.estado_eliminado = 0
  AND p.categoria IN ('Vehiculo', 'Moto')
  AND (f.estado_entrega = 'entregado' OR f.estado_entrega = 'pendiente')
ORDER BY f.fecha_creacion DESC;

-- ============================================
-- 3. CONSULTA: INDICADORES DEL DASHBOARD
-- ============================================
-- Total de adjudicados
SELECT COUNT(DISTINCT f.idfinanciamiento) as total_adjudicados
FROM financiamiento f
INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
WHERE f.estado_eliminado = 0
  AND p.categoria IN ('Vehiculo', 'Moto')
  AND f.estado_entrega IN ('entregado', 'pendiente');

-- Vehículos disponibles en stock
SELECT COUNT(*) as vehiculos_disponibles
FROM productosv2
WHERE categoria IN ('Vehiculo', 'Moto')
  AND cantidad > 0
  AND estado = 'Activo';

-- Vehículos entregados
SELECT COUNT(DISTINCT f.idfinanciamiento) as vehiculos_entregados
FROM financiamiento f
INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
WHERE f.estado_eliminado = 0
  AND p.categoria IN ('Vehiculo', 'Moto')
  AND f.estado_entrega = 'entregado';

-- SOATS por vencer (próximos 30 días)
SELECT COUNT(DISTINCT p.idproductosv2) as soats_por_vencer
FROM productosv2 p
INNER JOIN caracteristicas_producto cp ON p.idproductosv2 = cp.idproductosv2
INNER JOIN financiamiento f ON p.idproductosv2 = f.idproductosv2
WHERE cp.nombre_caracteristicas = 'fecha_venc_soat'
  AND cp.valor_caracteristica IS NOT NULL
  AND STR_TO_DATE(cp.valor_caracteristica, '%Y-%m-%d') BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
  AND f.estado_entrega = 'entregado'
  AND f.estado_eliminado = 0;

-- Seguros por vencer (próximos 30 días)
SELECT COUNT(DISTINCT p.idproductosv2) as seguros_por_vencer
FROM productosv2 p
INNER JOIN caracteristicas_producto cp ON p.idproductosv2 = cp.idproductosv2
INNER JOIN financiamiento f ON p.idproductosv2 = f.idproductosv2
WHERE cp.nombre_caracteristicas = 'fecha_venc_seguro'
  AND cp.valor_caracteristica IS NOT NULL
  AND STR_TO_DATE(cp.valor_caracteristica, '%Y-%m-%d') BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
  AND f.estado_entrega = 'entregado'
  AND f.estado_eliminado = 0;

-- ============================================
-- 4. CONSULTA: CUOTAS VENCIDAS DE ADJUDICADOS
-- ============================================
SELECT 
    f.idfinanciamiento,
    f.id_conductor,
    f.id_cliente,
    f.codigo_asociado,
    COALESCE(c.nombres, cl.nombres) as nombres,
    COALESCE(c.apellido_paterno, cl.apellido_paterno) as apellido_paterno,
    COALESCE(c.apellido_materno, cl.apellido_materno) as apellido_materno,
    p.nombre as producto_nombre,
    cf.idcuotas_financiamiento,
    cf.numero_cuota,
    cf.monto,
    cf.fecha_vencimiento,
    cf.estado,
    DATEDIFF(CURDATE(), cf.fecha_vencimiento) as dias_vencidos,
    f.moneda
FROM cuotas_financiamiento cf
INNER JOIN financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
WHERE cf.fecha_vencimiento < CURDATE()
  AND cf.estado != 'pagado'
  AND f.estado_entrega = 'entregado'
  AND f.estado_eliminado = 0
  AND f.incobrable = 0
  AND p.categoria IN ('Vehiculo', 'Moto')
ORDER BY cf.fecha_vencimiento ASC;

-- ============================================
-- 5. CONSULTA: ADJUDICADOS MOROSOS
-- ============================================
SELECT 
    f.idfinanciamiento,
    f.id_conductor,
    f.id_cliente,
    f.codigo_asociado,
    COALESCE(c.nombres, cl.nombres) as nombres,
    COALESCE(c.apellido_paterno, cl.apellido_paterno) as apellido_paterno,
    COALESCE(c.apellido_materno, cl.apellido_materno) as apellido_materno,
    COALESCE(c.nro_documento, cl.n_documento) as documento,
    p.nombre as producto_nombre,
    COUNT(cf.idcuotas_financiamiento) as total_cuotas_vencidas,
    SUM(cf.monto) as monto_total_vencido,
    MIN(cf.fecha_vencimiento) as primera_cuota_vencida,
    MAX(DATEDIFF(CURDATE(), cf.fecha_vencimiento)) as dias_mora_maxima,
    f.moneda
FROM financiamiento f
INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
INNER JOIN cuotas_financiamiento cf ON f.idfinanciamiento = cf.id_financiamiento
LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
WHERE cf.fecha_vencimiento < CURDATE()
  AND cf.estado != 'pagado'
  AND f.estado_entrega = 'entregado'
  AND f.estado_eliminado = 0
  AND f.incobrable = 0
  AND p.categoria IN ('Vehiculo', 'Moto')
  AND DATEDIFF(CURDATE(), cf.fecha_vencimiento) > 30
GROUP BY f.idfinanciamiento
ORDER BY dias_mora_maxima DESC;

-- ============================================
-- 6. CONSULTA: PRÓXIMOS A TERMINAR DE PAGAR
-- ============================================
SELECT 
    f.idfinanciamiento,
    f.id_conductor,
    f.id_cliente,
    f.codigo_asociado,
    COALESCE(c.nombres, cl.nombres) as nombres,
    COALESCE(c.apellido_paterno, cl.apellido_paterno) as apellido_paterno,
    COALESCE(c.apellido_materno, cl.apellido_materno) as apellido_materno,
    p.nombre as producto_nombre,
    f.cuotas as total_cuotas,
    COUNT(CASE WHEN cf.estado = 'pagado' THEN 1 END) as cuotas_pagadas,
    COUNT(CASE WHEN cf.estado != 'pagado' THEN 1 END) as cuotas_pendientes,
    MAX(cf.fecha_vencimiento) as fecha_ultima_cuota,
    f.moneda,
    f.monto_total
FROM financiamiento f
INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
INNER JOIN cuotas_financiamiento cf ON f.idfinanciamiento = cf.id_financiamiento
LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
WHERE f.estado_entrega = 'entregado'
  AND f.estado_eliminado = 0
  AND p.categoria IN ('Vehiculo', 'Moto')
GROUP BY f.idfinanciamiento
HAVING cuotas_pendientes > 0 AND cuotas_pendientes <= 10
ORDER BY cuotas_pendientes ASC;

-- ============================================
-- 7. CONSULTA: GANADORES POR MES
-- ============================================
SELECT 
    f.idfinanciamiento,
    f.id_conductor,
    f.id_cliente,
    f.codigo_asociado,
    COALESCE(c.nombres, cl.nombres) as nombres,
    COALESCE(c.apellido_paterno, cl.apellido_paterno) as apellido_paterno,
    COALESCE(c.apellido_materno, cl.apellido_materno) as apellido_materno,
    p.nombre as producto_nombre,
    a.tipo_adjudicacion,
    a.fecha_adjudicacion,
    a.fecha_entrega_real,
    a.dias_demora,
    f.cuota_inicial,
    f.monto_total,
    f.moneda
FROM adjudicaciones a
INNER JOIN financiamiento f ON a.id_financiamiento = f.idfinanciamiento
INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
WHERE a.mes_sorteo = ? -- Parámetro: 'YYYY-MM'
  AND f.estado_eliminado = 0
ORDER BY a.tipo_adjudicacion, a.fecha_adjudicacion DESC;

-- ============================================
-- 8. CONSULTA: REPORTE DE VELOCIDAD DE ENTREGA
-- ============================================
SELECT 
    DATE_FORMAT(a.fecha_adjudicacion, '%Y-%m') as mes,
    COUNT(*) as total_adjudicaciones,
    AVG(a.dias_demora) as promedio_dias_demora,
    MIN(a.dias_demora) as minimo_dias_demora,
    MAX(a.dias_demora) as maximo_dias_demora,
    COUNT(CASE WHEN a.dias_demora <= 7 THEN 1 END) as entregas_rapidas,
    COUNT(CASE WHEN a.dias_demora > 30 THEN 1 END) as entregas_lentas
FROM adjudicaciones a
WHERE a.fecha_entrega_real IS NOT NULL
  AND a.dias_demora IS NOT NULL
GROUP BY DATE_FORMAT(a.fecha_adjudicacion, '%Y-%m')
ORDER BY mes DESC
LIMIT 12;

-- ============================================
-- 9. CONSULTA: VEHÍCULOS CON SOAT POR VENCER
-- ============================================
SELECT 
    p.idproductosv2,
    p.nombre as producto_nombre,
    p.codigo as producto_codigo,
    f.idfinanciamiento,
    f.id_conductor,
    f.id_cliente,
    COALESCE(c.nombres, cl.nombres) as nombres,
    COALESCE(c.apellido_paterno, cl.apellido_paterno) as apellido_paterno,
    COALESCE(c.apellido_materno, cl.apellido_materno) as apellido_materno,
    cp.valor_caracteristica as fecha_venc_soat,
    DATEDIFF(STR_TO_DATE(cp.valor_caracteristica, '%Y-%m-%d'), CURDATE()) as dias_para_vencer
FROM productosv2 p
INNER JOIN caracteristicas_producto cp ON p.idproductosv2 = cp.idproductosv2
INNER JOIN financiamiento f ON p.idproductosv2 = f.idproductosv2
LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
WHERE cp.nombre_caracteristicas = 'fecha_venc_soat'
  AND cp.valor_caracteristica IS NOT NULL
  AND STR_TO_DATE(cp.valor_caracteristica, '%Y-%m-%d') BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 60 DAY)
  AND f.estado_entrega = 'entregado'
  AND f.estado_eliminado = 0
ORDER BY STR_TO_DATE(cp.valor_caracteristica, '%Y-%m-%d') ASC;

-- ============================================
-- 10. CONSULTA: VEHÍCULOS CON SEGURO POR VENCER
-- ============================================
SELECT 
    p.idproductosv2,
    p.nombre as producto_nombre,
    p.codigo as producto_codigo,
    f.idfinanciamiento,
    f.id_conductor,
    f.id_cliente,
    COALESCE(c.nombres, cl.nombres) as nombres,
    COALESCE(c.apellido_paterno, cl.apellido_paterno) as apellido_paterno,
    COALESCE(c.apellido_materno, cl.apellido_materno) as apellido_materno,
    cp.valor_caracteristica as fecha_venc_seguro,
    DATEDIFF(STR_TO_DATE(cp.valor_caracteristica, '%Y-%m-%d'), CURDATE()) as dias_para_vencer
FROM productosv2 p
INNER JOIN caracteristicas_producto cp ON p.idproductosv2 = cp.idproductosv2
INNER JOIN financiamiento f ON p.idproductosv2 = f.idproductosv2
LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
WHERE cp.nombre_caracteristicas = 'fecha_venc_seguro'
  AND cp.valor_caracteristica IS NOT NULL
  AND STR_TO_DATE(cp.valor_caracteristica, '%Y-%m-%d') BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 60 DAY)
  AND f.estado_entrega = 'entregado'
  AND f.estado_eliminado = 0
ORDER BY STR_TO_DATE(cp.valor_caracteristica, '%Y-%m-%d') ASC;
