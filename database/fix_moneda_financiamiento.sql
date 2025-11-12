-- Script para corregir el problema de moneda en financiamientos y pagos
-- Fecha: 23 de octubre, 2025
-- Problema: La moneda se está guardando como "0" o vacío en lugar de "S/." o "$"

-- ========================================
-- PASO 1: Actualizar tabla financiamiento
-- ========================================

-- Ver cuántos registros tienen moneda incorrecta
SELECT 
    COUNT(*) as total_incorrectos,
    moneda
FROM financiamiento 
WHERE moneda = '0' OR moneda = '' OR moneda IS NULL
GROUP BY moneda;

-- Actualizar financiamientos con moneda "0" o vacío a "S/." (por defecto Soles)
UPDATE financiamiento 
SET moneda = 'S/.' 
WHERE moneda = '0' ;

-- Verificar la actualización
SELECT 
    idfinanciamiento,
    codigo_asociado,
    monto_total,
    moneda,
    fecha_creacion
FROM financiamiento 
WHERE fecha_creacion >= '2025-10-01'
ORDER BY idfinanciamiento DESC
LIMIT 20;

-- ========================================
-- PASO 2: Actualizar tabla pagos_financiamiento
-- ========================================

-- Ver cuántos pagos tienen moneda incorrecta
SELECT 
    COUNT(*) as total_incorrectos,
    moneda
FROM pagos_financiamiento 
WHERE moneda = '0' OR moneda = '' OR moneda IS NULL
GROUP BY moneda;

-- Actualizar pagos con moneda "0" o vacío a "S/." (por defecto Soles)
UPDATE pagos_financiamiento 
SET moneda = 'S/.' 
WHERE moneda = '0' OR moneda = '' OR moneda IS NULL;

-- Verificar la actualización
SELECT 
    idpagos_financiamiento,
    id_financiamiento,
    monto,
    moneda,
    concepto,
    fecha_pago
FROM pagos_financiamiento 
WHERE fecha_pago >= '2025-10-01'
ORDER BY idpagos_financiamiento DESC
LIMIT 20;

-- ========================================
-- PASO 3: Actualizar tabla cuotas_financiamiento
-- ========================================

-- Ver cuántas cuotas tienen moneda incorrecta
SELECT 
    COUNT(*) as total_incorrectos,
    moneda_cuota
FROM cuotas_financiamiento 
WHERE moneda_cuota = '0' OR moneda_cuota = '' OR moneda_cuota IS NULL
GROUP BY moneda_cuota;

-- Actualizar cuotas con moneda "0" o vacío a "S/." (por defecto Soles)
UPDATE cuotas_financiamiento 
SET moneda_cuota = 'S/.' 
WHERE moneda_cuota = '0' OR moneda_cuota = '' OR moneda_cuota IS NULL;

-- Verificar la actualización
SELECT 
    idcuotas_financiamiento,
    id_financiamiento,
    numero_cuota,
    monto,
    moneda_cuota,
    fecha_vencimiento
FROM cuotas_financiamiento 
WHERE id_financiamiento IN (
    SELECT idfinanciamiento 
    FROM financiamiento 
    WHERE fecha_creacion >= '2025-10-01'
)
ORDER BY idcuotas_financiamiento DESC
LIMIT 20;

-- ========================================
-- PASO 4: Verificación final
-- ========================================

-- Contar registros por tipo de moneda en financiamiento
SELECT 
    moneda,
    COUNT(*) as total
FROM financiamiento 
GROUP BY moneda;

-- Contar registros por tipo de moneda en pagos
SELECT 
    moneda,
    COUNT(*) as total
FROM pagos_financiamiento 
GROUP BY moneda;

-- Contar registros por tipo de moneda en cuotas
SELECT 
    moneda_cuota,
    COUNT(*) as total
FROM cuotas_financiamiento 
GROUP BY moneda_cuota;

-- ========================================
-- NOTAS IMPORTANTES:
-- ========================================
-- 1. Este script asume que todos los registros con moneda incorrecta son en Soles (S/.)
-- 2. Si algunos registros deberían ser en Dólares ($), deberás identificarlos manualmente
-- 3. Ejecuta este script en un entorno de prueba primero
-- 4. Haz un backup de la base de datos antes de ejecutar
-- 5. Después de ejecutar este script, el problema de la moneda "0" debería estar resuelto
