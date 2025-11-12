-- ============================================
-- Script de Diagnóstico para Venta con Problema
-- Cliente: YASMANI FREDY BUSTINZA GUTIERREZ
-- Documento: 47941003
-- Comprobante: BT | B001 - 137
-- ============================================

USE magusqao_arequipa;

-- ============================================
-- 1. VERIFICAR SI LA VENTA EXISTE
-- ============================================
SELECT '=== 1. VERIFICAR SI LA VENTA EXISTE ===' AS paso;

SELECT v.id_venta, v.serie, v.numero, v.fecha_emision, v.total, 
       v.id_vendedor, v.id_cliente, v.estado, v.enviado_sunat
FROM ventas v
WHERE v.serie = 'B001' AND v.numero = '137';

-- Si no aparece nada, la venta no existe
-- Si aparece pero id_venta es NULL, ese es el problema

-- ============================================
-- 2. VERIFICAR DATOS DEL CLIENTE
-- ============================================
SELECT '=== 2. VERIFICAR DATOS DEL CLIENTE ===' AS paso;

SELECT c.id_cliente, c.documento, c.datos, c.direccion
FROM clientes c
WHERE c.documento = '47941003';

-- ============================================
-- 3. VERIFICAR LA VENTA CON DATOS DEL CLIENTE
-- ============================================
SELECT '=== 3. VERIFICAR LA VENTA CON DATOS DEL CLIENTE ===' AS paso;

SELECT v.id_venta, v.serie, v.numero, v.fecha_emision, v.total,
       v.id_vendedor, v.estado, v.enviado_sunat,
       c.documento, c.datos AS nombre_cliente,
       u.nombres, u.apellidos, u.usuario_id
FROM ventas v
LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
LEFT JOIN usuarios u ON v.id_vendedor = u.usuario_id
WHERE c.documento = '47941003'
  AND v.serie = 'B001' 
  AND v.numero = '137';

-- ============================================
-- 4. VERIFICAR QUÉ DEVUELVE LA VISTA view_ventas
-- ============================================
SELECT '=== 4. VERIFICAR QUÉ DEVUELVE LA VISTA view_ventas ===' AS paso;

-- Primero verificar si la vista existe
SHOW TABLES LIKE 'view_ventas';

-- Si existe, ver qué devuelve para esta venta
SELECT *
FROM view_ventas
WHERE documento = '47941003'
  AND serie = 'B001'
  AND numero = '137';

-- ============================================
-- 5. VERIFICAR TODAS LAS VENTAS DEL CLIENTE
-- ============================================
SELECT '=== 5. VERIFICAR TODAS LAS VENTAS DEL CLIENTE ===' AS paso;

SELECT v.id_venta, v.serie, v.numero, v.fecha_emision, v.total,
       v.estado, v.enviado_sunat,
       CASE 
           WHEN v.estado = 1 THEN 'Normal'
           WHEN v.estado = 2 THEN 'Anulado'
           ELSE 'Desconocido'
       END AS estado_texto
FROM ventas v
LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
WHERE c.documento = '47941003'
ORDER BY v.fecha_emision DESC;

-- ============================================
-- 6. VERIFICAR VENTAS SIN id_venta (NO DEBERÍA HABER)
-- ============================================
SELECT '=== 6. VERIFICAR VENTAS SIN id_venta ===' AS paso;

SELECT COUNT(*) AS ventas_sin_id
FROM ventas
WHERE id_venta IS NULL OR id_venta = '' OR id_venta = 0;

-- Si hay alguna, mostrarlas
SELECT v.*, c.datos
FROM ventas v
LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
WHERE v.id_venta IS NULL OR v.id_venta = '' OR v.id_venta = 0;

-- ============================================
-- 7. VERIFICAR VENTAS SIN VENDEDOR
-- ============================================
SELECT '=== 7. VERIFICAR VENTAS SIN VENDEDOR ===' AS paso;

SELECT COUNT(*) AS ventas_sin_vendedor
FROM ventas
WHERE id_vendedor IS NULL OR id_vendedor = 0;

-- Mostrar algunas ventas sin vendedor
SELECT v.id_venta, v.serie, v.numero, v.fecha_emision, 
       v.id_vendedor, c.documento, c.datos
FROM ventas v
LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
WHERE v.id_vendedor IS NULL OR v.id_vendedor = 0
LIMIT 10;

-- ============================================
-- 8. VERIFICAR LA ESTRUCTURA DE LA TABLA ventas
-- ============================================
SELECT '=== 8. VERIFICAR LA ESTRUCTURA DE LA TABLA ventas ===' AS paso;

DESCRIBE ventas;

-- ============================================
-- 9. VERIFICAR SI EXISTE LA VISTA view_ventas
-- ============================================
SELECT '=== 9. VERIFICAR SI EXISTE LA VISTA view_ventas ===' AS paso;

SELECT TABLE_NAME, TABLE_TYPE
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'magusqao_arequipa'
  AND TABLE_NAME = 'view_ventas';

-- Si existe, ver su definición
SHOW CREATE VIEW view_ventas;

-- ============================================
-- 10. VERIFICAR PRODUCTOS DE LA VENTA
-- ============================================
SELECT '=== 10. VERIFICAR PRODUCTOS DE LA VENTA ===' AS paso;

-- Primero necesitamos el id_venta
SET @id_venta = (SELECT id_venta FROM ventas WHERE serie = 'B001' AND numero = '137' LIMIT 1);

SELECT @id_venta AS id_venta_encontrado;

-- Ver productos de la venta
SELECT pv.*, p.descripcion, p.codigo
FROM productos_ventas pv
LEFT JOIN productos p ON pv.id_producto = p.id_producto
WHERE pv.id_venta = @id_venta;

-- ============================================
-- RESUMEN Y RECOMENDACIONES
-- ============================================
SELECT '=== RESUMEN Y RECOMENDACIONES ===' AS paso;

SELECT 
    CASE 
        WHEN EXISTS (SELECT 1 FROM ventas WHERE serie = 'B001' AND numero = '137') 
        THEN '✅ La venta existe'
        ELSE '❌ La venta NO existe'
    END AS verificacion_venta,
    
    CASE 
        WHEN EXISTS (SELECT 1 FROM ventas WHERE serie = 'B001' AND numero = '137' AND id_venta IS NOT NULL AND id_venta != '') 
        THEN '✅ La venta tiene id_venta válido'
        ELSE '❌ La venta NO tiene id_venta válido'
    END AS verificacion_id_venta,
    
    CASE 
        WHEN EXISTS (SELECT 1 FROM clientes WHERE documento = '47941003') 
        THEN '✅ El cliente existe'
        ELSE '❌ El cliente NO existe'
    END AS verificacion_cliente,
    
    CASE 
        WHEN EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'magusqao_arequipa' AND TABLE_NAME = 'view_ventas') 
        THEN '✅ La vista view_ventas existe'
        ELSE '❌ La vista view_ventas NO existe'
    END AS verificacion_vista;

-- ============================================
-- FIN DEL DIAGNÓSTICO
-- ============================================
