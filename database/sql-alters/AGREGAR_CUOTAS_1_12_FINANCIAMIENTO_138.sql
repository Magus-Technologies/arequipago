-- ============================================================================
-- AGREGAR CUOTAS 1-12 AL FINANCIAMIENTO 138
-- Conductor: GUSTAVO EDUARDO CONDORI CALCINA (DNI: 75822613)
-- Problema: El financiamiento comenzó en la semana 13, faltan las semanas 1-12
-- Solución: Insertar las 12 cuotas faltantes con estado 'pendiente'
-- ============================================================================

-- Verificar datos del financiamiento
SELECT 
    idfinanciamiento,
    id_conductor,
    fecha_inicio,
    cuotas,
    monto_total,
    frecuencia,
    estado
FROM financiamiento 
WHERE idfinanciamiento = 138;

-- Verificar cuotas existentes
SELECT 
    MIN(numero_cuota) as primera_cuota,
    MAX(numero_cuota) as ultima_cuota,
    COUNT(*) as total_cuotas
FROM cuotas_financiamiento 
WHERE id_financiamiento = 138;

-- ============================================================================
-- INSERTAR LAS 12 CUOTAS FALTANTES (SEMANAS 1-12)
-- ============================================================================
-- Nota: La primera cuota existente (13) tiene fecha 2024-12-02
-- Entonces la cuota 12 sería 2024-11-25, cuota 11 sería 2024-11-18, etc.
-- Cada semana retrocede 7 días

INSERT INTO cuotas_financiamiento 
(id_financiamiento, numero_cuota, monto, comision_canal_digital, moneda_cuota, mora, fecha_vencimiento, estado, fecha_pago, estado_eliminado, puntos_aplicados, penalizacion_perdonada)
VALUES
-- Semana 1: 2024-09-09 (12 semanas antes del 02/12/2024)
(138, 1, 100.00, 0.20, NULL, 0.00, '2024-09-09', 'pendiente', NULL, 0, 0, 0),

-- Semana 2: 2024-09-16
(138, 2, 100.00, 0.20, NULL, 0.00, '2024-09-16', 'pendiente', NULL, 0, 0, 0),

-- Semana 3: 2024-09-23
(138, 3, 100.00, 0.20, NULL, 0.00, '2024-09-23', 'pendiente', NULL, 0, 0, 0),

-- Semana 4: 2024-09-30
(138, 4, 100.00, 0.20, NULL, 0.00, '2024-09-30', 'pendiente', NULL, 0, 0, 0),

-- Semana 5: 2024-10-07
(138, 5, 100.00, 0.20, NULL, 0.00, '2024-10-07', 'pendiente', NULL, 0, 0, 0),

-- Semana 6: 2024-10-14
(138, 6, 100.00, 0.20, NULL, 0.00, '2024-10-14', 'pendiente', NULL, 0, 0, 0),

-- Semana 7: 2024-10-21
(138, 7, 100.00, 0.20, NULL, 0.00, '2024-10-21', 'pendiente', NULL, 0, 0, 0),

-- Semana 8: 2024-10-28
(138, 8, 100.00, 0.20, NULL, 0.00, '2024-10-28', 'pendiente', NULL, 0, 0, 0),

-- Semana 9: 2024-11-04
(138, 9, 100.00, 0.20, NULL, 0.00, '2024-11-04', 'pendiente', NULL, 0, 0, 0),

-- Semana 10: 2024-11-11
(138, 10, 100.00, 0.20, NULL, 0.00, '2024-11-11', 'pendiente', NULL, 0, 0, 0),

-- Semana 11: 2024-11-18
(138, 11, 100.00, 0.20, NULL, 0.00, '2024-11-18', 'pendiente', NULL, 0, 0, 0),

-- Semana 12: 2024-11-25
(138, 12, 100.00, 0.20, NULL, 0.00, '2024-11-25', 'pendiente', NULL, 0, 0, 0);

-- ============================================================================
-- VERIFICAR QUE SE INSERTARON CORRECTAMENTE
-- ============================================================================
SELECT 
    numero_cuota,
    fecha_vencimiento,
    monto,
    estado,
    mora
FROM cuotas_financiamiento 
WHERE id_financiamiento = 138 
AND numero_cuota BETWEEN 1 AND 15
ORDER BY numero_cuota;

-- ============================================================================
-- RESUMEN FINAL
-- ============================================================================
SELECT 
    COUNT(*) as total_cuotas,
    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as cuotas_pendientes,
    SUM(CASE WHEN estado = 'pagado' THEN 1 ELSE 0 END) as cuotas_pagadas,
    SUM(CASE WHEN estado = 'pendiente' THEN monto ELSE 0 END) as monto_pendiente
FROM cuotas_financiamiento 
WHERE id_financiamiento = 138;
