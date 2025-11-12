-- ========================================
-- CONSULTAS PARA VERIFICAR MONEDA EN FINANCIAMIENTO
-- ========================================

-- 1. Ver el último financiamiento registrado con todos sus datos
SELECT 
    idfinanciamiento,
    codigo_asociado,
    monto_total,
    moneda,
    cuota_inicial,
    cuotas,
    fecha_creacion,
    fecha_inicio,
    fecha_fin
FROM financiamiento 
ORDER BY idfinanciamiento DESC 
LIMIT 1;

-- 2. Ver los últimos 5 financiamientos con su moneda
SELECT 
    idfinanciamiento,
    codigo_asociado,
    monto_total,
    moneda,
    fecha_creacion
FROM financiamiento 
ORDER BY idfinanciamiento DESC 
LIMIT 5;

-- 3. Ver financiamientos de hoy con moneda incorrecta
SELECT 
    idfinanciamiento,
    codigo_asociado,
    monto_total,
    moneda,
    DATE_FORMAT(fecha_creacion, '%H:%i:%s') as hora
FROM financiamiento 
WHERE DATE(fecha_creacion) = CURDATE()
  AND (moneda = '0' OR moneda = '' OR moneda IS NULL)
ORDER BY idfinanciamiento DESC;

-- 4. Ver financiamientos de hoy con moneda correcta
SELECT 
    idfinanciamiento,
    codigo_asociado,
    monto_total,
    moneda,
    DATE_FORMAT(fecha_creacion, '%H:%i:%s') as hora
FROM financiamiento 
WHERE DATE(fecha_creacion) = CURDATE()
  AND moneda IN ('S/.', '$')
ORDER BY idfinanciamiento DESC;

-- 5. Contar financiamientos por tipo de moneda (hoy)
SELECT 
    CASE 
        WHEN moneda = 'S/.' THEN 'Soles'
        WHEN moneda = '$' THEN 'Dólares'
        WHEN moneda = '0' THEN 'Cero (Error)'
        WHEN moneda = '' THEN 'Vacío (Error)'
        WHEN moneda IS NULL THEN 'NULL (Error)'
        ELSE 'Otro'
    END as tipo_moneda,
    COUNT(*) as cantidad
FROM financiamiento 
WHERE DATE(fecha_creacion) = CURDATE()
GROUP BY moneda;

-- 6. Ver el financiamiento específico con ID 605 (el último que registraste)
SELECT 
    f.idfinanciamiento,
    f.codigo_asociado,
    f.monto_total,
    f.moneda as moneda_financiamiento,
    f.cuota_inicial,
    f.cuotas,
    f.fecha_creacion,
    p.idpagos_financiamiento,
    p.monto as monto_pago,
    p.moneda as moneda_pago,
    p.concepto,
    p.fecha_pago
FROM financiamiento f
LEFT JOIN pagos_financiamiento p ON f.idfinanciamiento = p.id_financiamiento
WHERE f.idfinanciamiento = 605;

-- 7. Ver las cuotas del financiamiento 605
SELECT 
    idcuotas_financiamiento,
    numero_cuota,
    monto,
    moneda_cuota,
    fecha_vencimiento,
    estado
FROM cuotas_financiamiento
WHERE id_financiamiento = 605
ORDER BY numero_cuota;

-- 8. Actualizar manualmente el financiamiento 605 a Soles (si quieres corregirlo)
-- DESCOMENTA LA SIGUIENTE LÍNEA PARA EJECUTAR:
-- UPDATE financiamiento SET moneda = 'S/.' WHERE idfinanciamiento = 605;

-- 9. Actualizar manualmente el pago 2800 a Soles (si quieres corregirlo)
-- DESCOMENTA LA SIGUIENTE LÍNEA PARA EJECUTAR:
-- UPDATE pagos_financiamiento SET moneda = 'S/.' WHERE idpagos_financiamiento = 2800;

-- 10. Actualizar manualmente las cuotas del financiamiento 605 a Soles
-- DESCOMENTA LA SIGUIENTE LÍNEA PARA EJECUTAR:
-- UPDATE cuotas_financiamiento SET moneda_cuota = 'S/.' WHERE id_financiamiento = 605;
