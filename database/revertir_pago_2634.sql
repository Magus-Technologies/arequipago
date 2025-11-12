-- ========================================
-- REVERTIR PAGO APROBADO POR ERROR
-- ========================================
-- Pago ID: 2634
-- Conductor: 245 (JUAN CARLOS QUISPE CCALLO)
-- Monto: $300.00
-- Fecha: 2025-10-20 14:06:21
-- ========================================

-- PASO 1: VERIFICAR DATOS ANTES DE MODIFICAR
-- ========================================

SELECT '=== VERIFICANDO PAGO ===' AS paso;

SELECT
    pf.idpagos_financiamiento,
    pf.id_conductor,
    pf.monto,
    pf.metodo_pago,
    pf.fecha_pago,
    pf.estado AS estado_actual,
    CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS conductor
FROM pagos_financiamiento pf
LEFT JOIN conductores c ON pf.id_conductor = c.id_conductor
WHERE pf.idpagos_financiamiento = 2634;

SELECT '=== VERIFICANDO CUOTAS ===' AS paso;

SELECT
    cf.idcuotas_financiamiento,
    cf.id_financiamiento,
    cf.numero_cuota,
    cf.monto,
    cf.fecha_vencimiento,
    cf.estado AS estado_actual,
    cf.fecha_pago
FROM cuotas_financiamiento cf
WHERE cf.idcuotas_financiamiento IN (22963, 22964, 22965);

SELECT '=== VERIFICANDO REGISTRO DE APROBACIÓN ===' AS paso;

SELECT * FROM pagos_pendientes_financiamientos
WHERE idpagos_financiamiento = 2634;

-- ========================================
-- PASO 2: REVERTIR EL PAGO (TRANSACCIÓN)
-- ========================================

START TRANSACTION;

-- 2.1 Cambiar estado del pago de Aprobado (1) a Pendiente (0)
-- YA LO HICISTE, pero lo incluyo por si necesitas ejecutar todo de nuevo
UPDATE pagos_financiamiento
SET estado = 2
WHERE idpagos_financiamiento = 2634;

-- 2.2 Revertir las 3 cuotas a estado "En Progreso" y quitar fecha_pago
-- ESTO ES LO QUE FALTA HACER
UPDATE cuotas_financiamiento
SET estado = 'En Progreso',
    fecha_pago = NULL
WHERE idcuotas_financiamiento IN (22963, 22964, 22965);

-- 2.3 Limpiar el usuario de aprobación
UPDATE pagos_pendientes_financiamientos
SET id_usuario_aprobacion = NULL
WHERE idpagos_financiamiento = 2634;

-- ========================================
-- PASO 3: VERIFICAR LOS CAMBIOS ANTES DE CONFIRMAR
-- ========================================

SELECT '=== VERIFICANDO CAMBIOS EN PAGO ===' AS verificacion;

SELECT
    idpagos_financiamiento,
    id_conductor,
    monto,
    estado,
    fecha_pago
FROM pagos_financiamiento
WHERE idpagos_financiamiento = 2634;

SELECT '=== VERIFICANDO CAMBIOS EN CUOTAS ===' AS verificacion;

SELECT
    idcuotas_financiamiento,
    numero_cuota,
    monto,
    estado,
    fecha_pago
FROM cuotas_financiamiento
WHERE idcuotas_financiamiento IN (22963, 22964, 22965);

-- ========================================
-- PASO 4: CONFIRMAR O REVERTIR
-- ========================================

-- Si todo está correcto, ejecuta esto:
COMMIT;

-- Si algo salió mal, ejecuta esto en su lugar:
-- ROLLBACK;

-- ========================================
-- PASO 5: VERIFICAR Y REVERTIR PUNTOS (SI APLICA)
-- ========================================

SELECT '=== VERIFICANDO PUNTOS CREDITICIOS ===' AS verificacion;

SELECT * FROM puntaje_crediticio
WHERE id_conductor = 245
AND DATE(fecha_creacion) = '2025-10-20'
ORDER BY fecha_creacion DESC;

-- Si hay puntos relacionados con este pago, ejecuta:
-- DELETE FROM puntaje_crediticio WHERE id = [ID_DEL_REGISTRO];
-- (Reemplaza [ID_DEL_REGISTRO] con el ID correcto después de revisar)

-- ========================================
-- VERIFICACIÓN FINAL
-- ========================================

SELECT '=== VERIFICACIÓN FINAL ===' AS fin;

SELECT
    pf.idpagos_financiamiento,
    pf.estado AS estado_pago,
    GROUP_CONCAT(cf.estado SEPARATOR ', ') AS estados_cuotas,
    GROUP_CONCAT(cf.fecha_pago SEPARATOR ', ') AS fechas_pago_cuotas
FROM pagos_financiamiento pf
LEFT JOIN pagos_pendientes_financiamientos ppf ON pf.idpagos_financiamiento = ppf.idpagos_financiamiento
LEFT JOIN cuotas_financiamiento cf ON cf.idcuotas_financiamiento IN (22963, 22964, 22965)
WHERE pf.idpagos_financiamiento = 2634
GROUP BY pf.idpagos_financiamiento;

-- ========================================
-- RESULTADO ESPERADO:
-- ========================================
-- estado_pago: 0 (Pendiente)
-- estados_cuotas: En Progreso, En Progreso, En Progreso
-- fechas_pago_cuotas: NULL, NULL, NULL
-- ========================================
