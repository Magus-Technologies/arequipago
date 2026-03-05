-- ============================================
-- FIX: Credi Ahorros Autos - Fechas Dinámicas
-- ============================================
-- 
-- Problema: El plan "Credi Ahorros Autos" (ID 49) y sus VARIANTES tienen 
-- fecha_inicio y fecha_fin fijas en la BD, lo que impide que las 215 semanas 
-- se calculen desde el momento del registro del financiamiento.
--
-- Solución: Poner fecha_inicio y fecha_fin en NULL tanto en el PLAN como en 
-- las VARIANTES para que el sistema calcule las fechas dinámicamente como lo 
-- hace con CrediGo Motos (ID 33).
--
-- Fecha: 2026-02-10
-- ============================================

USE magusqao_arequipa;

-- ============================================
-- PASO 1: Actualizar PLAN principal
-- ============================================
UPDATE planes_financiamiento 
SET 
    fecha_inicio = NULL,
    fecha_fin = NULL
WHERE idplan_financiamiento = 49;

-- ============================================
-- PASO 2: Actualizar VARIANTES del plan
-- ============================================
-- Variantes del plan 49:
-- ID 35: '13,000' - Cuota $80, 215 semanas
-- ID 36: '15,000' - Cuota S/.90, 215 semanas  
-- ID 37: '17,000' - Cuota $100, 215 semanas

UPDATE grupos_variantes
SET 
    fecha_inicio = NULL,
    fecha_fin = NULL
WHERE idplan_financiamiento = 49;

-- ============================================
-- VERIFICACIÓN
-- ============================================

-- Verificar el PLAN
SELECT 
    idplan_financiamiento,
    nombre_plan,
    cantidad_cuotas,
    fecha_inicio,
    fecha_fin,
    frecuencia
FROM planes_financiamiento
WHERE idplan_financiamiento = 49;

-- Verificar las VARIANTES
SELECT 
    idgrupos_variantes,
    idplan_financiamiento,
    nombre_variante,
    cuota_inicial,
    monto_cuota,
    cantidad_cuotas,
    fecha_inicio,
    fecha_fin,
    moneda
FROM grupos_variantes
WHERE idplan_financiamiento = 49
ORDER BY idgrupos_variantes;

-- ============================================
-- Resultado esperado del PLAN:
-- ============================================
-- idplan_financiamiento: 49
-- nombre_plan: Credi Ahorros Autos
-- cantidad_cuotas: 215
-- fecha_inicio: NULL  ✅
-- fecha_fin: NULL     ✅
-- frecuencia: semanal

-- ============================================
-- Resultado esperado de las VARIANTES:
-- ============================================
-- ID 35 | Plan 49 | 13,000 | 260.00 | 80.00  | 215 | NULL ✅ | NULL ✅ | $
-- ID 36 | Plan 49 | 15,000 | 300.00 | 90.00  | 215 | NULL ✅ | NULL ✅ | S/.
-- ID 37 | Plan 49 | 17,000 | 340.00 | 100.00 | 215 | NULL ✅ | NULL ✅ | $
-- ============================================
