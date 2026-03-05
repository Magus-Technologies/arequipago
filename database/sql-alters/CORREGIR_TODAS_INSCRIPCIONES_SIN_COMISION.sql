

INSERT INTO comisiones
(usuario_id, tipo_comision, referencia_id, monto_comision, fecha_comision, tipo_vehiculo, observaciones, moneda, estado_comision)
VALUES

-- 1. Sebastian - Conductor 423 (GEANFRANCO LEONARDO) - Aprobado 08/11/2025
(105, 'inscripcion', 448, 50.00, '2025-11-08 13:38:26', 'moto', 'Comisión por inscripción - Pago contado (Retroactiva)', 'S/.', 'pendiente'),

-- 2-5. Maria Fernanda - Conductor 422 (4 pagos del mismo conductor) - Aprobado 07/11/2025
(106, 'inscripcion', 452, 50.00, '2025-11-07 11:50:31', 'moto', 'Comisión por inscripción - Pago 1/4 (Retroactiva)', 'S/.', 'pendiente'),
(106, 'inscripcion', 451, 50.00, '2025-11-07 11:50:31', 'moto', 'Comisión por inscripción - Pago 2/4 (Retroactiva)', 'S/.', 'pendiente'),
(106, 'inscripcion', 450, 50.00, '2025-11-07 11:50:31', 'moto', 'Comisión por inscripción - Pago 3/4 (Retroactiva)', 'S/.', 'pendiente'),
(106, 'inscripcion', 449, 50.00, '2025-11-07 11:50:31', 'moto', 'Comisión por inscripción - Pago 4/4 (Retroactiva)', 'S/.', 'pendiente'),

-- 6. Sebastian - Conductor 421 - Aprobado 05/11/2025
(105, 'inscripcion', 447, 50.00, '2025-11-05 16:27:22', 'moto', 'Comisión por inscripción - Pago contado (Retroactiva)', 'S/.', 'pendiente');

-- 7. YONNY - Conductor 424 (Jose Rodrigo) - Ya creada anteriormente
-- (111, 'inscripcion', 453, 50.00, '2025-11-18 11:03:32', 'moto', 'Comisión por inscripción - Pago contado (Retroactiva)', 'S/.', 'pendiente');

-- 8-13. Pagos SIN id_conductorpago (probablemente rechazados o eliminados)
-- Estos NO tienen registro en conductor_pago, por lo que NO se pueden crear comisiones
-- Pago pendiente IDs: 27, 26, 17, 14, 12, 8, 7

-- ========================================
-- NOTA IMPORTANTE SOBRE PAGOS SIN conductor_pago
-- ========================================

/*
Los siguientes pagos aprobados NO tienen registro en conductor_pago:
- ID 27 (Sebastian) - Sin conductor_pago
- ID 26 (Leones Go) - Sin conductor_pago
- ID 17 (Kristopher) - Sin conductor_pago
- ID 14 (Maria Fernanda) - Sin conductor_pago
- ID 12 (DANIA COPARA) - Sin conductor_pago
- ID 8 (Maria Fernanda) - Sin conductor_pago
- ID 7 (Sebastian) - Sin conductor_pago

POSIBLES CAUSAS:
1. Error al aprobar (no se completó el proceso)
2. Fueron eliminados posteriormente
3. Error en el código de aprobación

RECOMENDACIÓN:
Verificar manualmente cada caso y decidir si:
a) Re-aprobar desde la interfaz
b) Crear manualmente los registros faltantes
c) Marcar como rechazados si no proceden
*/

-- ========================================
-- VERIFICACIÓN
-- ========================================

-- Ver todas las comisiones creadas
SELECT
    c.id_comision,
    c.usuario_id,
    u.nombres as asesor,
    c.referencia_id as id_conductorpago,
    c.monto_comision,
    c.moneda,
    c.fecha_comision,
    cp.id_conductor,
    CONCAT(cond.nombres, ' ', cond.apellido_paterno) as conductor
FROM comisiones c
LEFT JOIN usuarios u ON c.usuario_id = u.usuario_id
LEFT JOIN conductor_pago cp ON c.referencia_id = cp.id_conductorpago
LEFT JOIN conductores cond ON cp.id_conductor = cond.id_conductor
WHERE c.tipo_comision = 'inscripcion'
AND c.fecha_comision >= '2025-11-01'
ORDER BY c.fecha_comision DESC;

-- Resumen por asesor
SELECT
    u.nombres as asesor,
    COUNT(*) as total_comisiones,
    SUM(c.monto_comision) as total_monto
FROM comisiones c
INNER JOIN usuarios u ON c.usuario_id = u.usuario_id
WHERE c.tipo_comision = 'inscripcion'
AND c.fecha_comision >= '2025-11-01'
GROUP BY u.usuario_id, u.nombres
ORDER BY total_comisiones DESC;

-- Inscripciones aprobadas que AÚN faltan (sin conductor_pago)
SELECT
    ppi.id,
    ppi.id_usuario_registro,
    u.nombres as asesor,
    ppi.fecha_registro,
    JSON_EXTRACT(ppi.observaciones, '$.monto_pago') as monto,
    'SIN conductor_pago - Verificar manualmente' as estado_problema
FROM pagos_pendientes_inscripcion ppi
LEFT JOIN usuarios u ON ppi.id_usuario_registro = u.usuario_id
LEFT JOIN conductor_pago cp ON JSON_EXTRACT(ppi.observaciones, '$.id_conductor') = cp.id_conductor
WHERE ppi.estado = 'aprobado'
AND ppi.fecha_registro >= '2025-11-01'
AND u.id_rol IN (1,2)
AND cp.id_conductorpago IS NULL
ORDER BY ppi.fecha_registro DESC;
