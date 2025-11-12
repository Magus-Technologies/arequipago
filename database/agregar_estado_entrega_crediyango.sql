-- =====================================================
-- Script: Agregar columna estado_entrega para CrediYango
-- Fecha: 2025-11-05
-- Descripción: Permite trackear si el vehículo fue entregado
--              sin necesidad de usar producto placeholder (ID 37)
-- =====================================================

-- 1. Agregar columna estado_entrega a tabla financiamiento
-- ✅ CORREGIDO: DEFAULT NULL para que solo CrediYango use esta columna
ALTER TABLE `financiamiento`
ADD COLUMN `estado_entrega` ENUM('pendiente', 'entregado') NULL DEFAULT NULL
COMMENT 'Estado de entrega del vehículo para CrediYango (NULL para otros planes)'
AFTER `cobrar_mora`;

-- 2. Actualizar financiamientos CrediYango existentes
-- Los que tienen producto ID 37 (no entregados) → estado_entrega = 'pendiente'
UPDATE `financiamiento`
SET `estado_entrega` = 'pendiente'
WHERE `grupo_financiamiento` = 45
  AND `idproductosv2` = 37;

-- Los que tienen estado "Vehiculo Entregado" → estado_entrega = 'entregado'
UPDATE `financiamiento`
SET `estado_entrega` = 'entregado'
WHERE `grupo_financiamiento` = 45
  AND `estado` = 'Vehiculo Entregado';

-- 3. Verificar resultados
SELECT
    idfinanciamiento,
    grupo_financiamiento,
    estado,
    estado_entrega,
    idproductosv2,
    fecha_entrega,
    fecha_inicio_pagos_calculada
FROM `financiamiento`
WHERE `grupo_financiamiento` = 45
ORDER BY idfinanciamiento DESC;

-- =====================================================
-- Comandos útiles para verificación:
-- =====================================================

-- Ver total de CrediYango por estado de entrega
/*
SELECT
    estado_entrega,
    COUNT(*) as total,
    SUM(monto_total) as monto_total_sum
FROM financiamiento
WHERE grupo_financiamiento = 45
GROUP BY estado_entrega;
*/

-- Ver CrediYango pendientes de entrega
/*
SELECT
    f.idfinanciamiento,
    CONCAT(c.nombres, ' ', c.apellido_paterno) as conductor,
    f.monto_total,
    f.estado,
    f.estado_entrega,
    f.fecha_inicio as fecha_registro
FROM financiamiento f
LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
WHERE f.grupo_financiamiento = 45
  AND f.estado_entrega = 'pendiente'
ORDER BY f.idfinanciamiento DESC;
*/

-- Ver CrediYango ya entregados
/*
SELECT
    f.idfinanciamiento,
    CONCAT(c.nombres, ' ', c.apellido_paterno) as conductor,
    f.monto_total,
    f.estado,
    f.estado_entrega,
    f.fecha_entrega,
    p.nombre as vehiculo_entregado,
    COUNT(cf.idcuotas_financiamiento) as total_cuotas
FROM financiamiento f
LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
LEFT JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
LEFT JOIN cuotas_financiamiento cf ON f.idfinanciamiento = cf.id_financiamiento
WHERE f.grupo_financiamiento = 45
  AND f.estado_entrega = 'entregado'
GROUP BY f.idfinanciamiento
ORDER BY f.fecha_entrega DESC;
*/
