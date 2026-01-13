-- ========================================
-- SOLUCIÓN: COMISIONES FALTANTES DE INSCRIPCIONES
-- Usuario: YONNY (usuario_id = 111)
-- ========================================

-- PROBLEMA IDENTIFICADO:
-- 1. YONNY registró 2 inscripciones en noviembre:
--    - Conductor 424 (Jose Rodrigo) - Aprobado el 18/11, pero NO tiene comisión
--    - Conductor 426 (Sergio Andres) - AÚN PENDIENTE de aprobación
--
-- 2. Cuando se aprobó la inscripción 424:
--    - Se registró en conductor_pago (id 453) con usuario_registro = 110 (ArequipaGo - Director)
--    - ❌ NO se creó comisión porque el código VIEJO usaba id_usuario_aprobacion (director)
--    - ✅ DEBERÍA haber creado comisión para YONNY (usuario 111)

-- ========================================
-- PASO 1: CREAR COMISIÓN PARA INSCRIPCIÓN YA APROBADA
-- ========================================

-- Inscripción del conductor 424 (Jose Rodrigo Velazco) - Aprobada 18/11/2025
-- Registrada por: YONNY (usuario 111)
-- Aprobada por: ArequipaGo (usuario 110)
-- Pago registrado: conductor_pago ID 453

INSERT INTO comisiones
(usuario_id, tipo_comision, referencia_id, monto_comision, fecha_comision, tipo_vehiculo, observaciones, moneda, estado_comision)
VALUES
(111, 'inscripcion', 453, 50.00, '2025-11-18 11:03:32', 'moto', 'Comisión por inscripción - Pago contado (Retroactiva)', 'S/.', 'pendiente');

-- Verificar creación
SELECT
    'Comisión creada para YONNY - Conductor 424' as mensaje,
    id_comision,
    usuario_id,
    monto_comision,
    moneda,
    fecha_comision
FROM comisiones
WHERE tipo_comision = 'inscripcion'
AND referencia_id = 453;

-- ========================================
-- PASO 2: APROBAR INSCRIPCIÓN PENDIENTE (ID 18)
-- ========================================

-- IMPORTANTE: Esta inscripción está PENDIENTE desde el 19/11/2025
-- Conductor: 426 (Sergio Andres Sanchez Alcos)
-- Registrado por: YONNY (usuario 111)
-- Estado: pendiente

-- Opción 1: Aprobar desde la interfaz web (RECOMENDADO)
-- - Ir a "Pagos Pendientes de Inscripción"
-- - Buscar el pago del conductor "SERGIO ANDRES SANCHEZ ALCOS"
-- - Aprobar
-- - ✅ Con el código corregido, SE CREARÁ AUTOMÁTICAMENTE la comisión para YONNY

-- Opción 2: Aprobar manualmente (NO RECOMENDADO - mejor usar la interfaz)
-- Si necesitas aprobarlo manualmente por alguna razón, ejecuta:
/*
-- 1. Registrar en conductor_pago
INSERT INTO conductor_pago (id_conductor, id_tipopago, fecha_pago, monto_pago, usuario_registro)
VALUES (426, 1, '2025-12-31', 150.00, 111);

-- 2. Obtener el ID del pago recién creado
SET @id_pago = LAST_INSERT_ID();

-- 3. Crear comisión para YONNY
INSERT INTO comisiones
(usuario_id, tipo_comision, referencia_id, monto_comision, fecha_comision, tipo_vehiculo, observaciones, moneda, estado_comision)
VALUES
(111, 'inscripcion', @id_pago, 50.00, NOW(), 'moto', 'Comisión por inscripción - Pago contado', 'S/.', 'pendiente');

-- 4. Marcar como aprobado
UPDATE pagos_pendientes_inscripcion
SET estado = 'aprobado',
    id_usuario_aprobacion = 110,  -- ID del director que aprueba
    fecha_aprobacion = NOW()
WHERE id = 18;
*/

-- ========================================
-- VERIFICACIÓN FINAL
-- ========================================

-- Ver todas las comisiones de YONNY
SELECT
    c.id_comision,
    c.tipo_comision,
    c.referencia_id,
    c.monto_comision,
    c.moneda,
    c.estado_comision,
    c.fecha_comision,
    CASE
        WHEN c.tipo_comision = 'inscripcion' THEN CONCAT('Inscripción - Conductor ID: ', cp.id_conductor)
        WHEN c.tipo_comision = 'financiamiento' THEN CONCAT('Financiamiento ID: ', f.idfinanciamiento)
    END as detalle
FROM comisiones c
LEFT JOIN conductor_pago cp ON (c.tipo_comision = 'inscripcion' AND c.referencia_id = cp.id_conductorpago)
LEFT JOIN financiamiento f ON (c.tipo_comision = 'financiamiento' AND c.referencia_id = f.idfinanciamiento)
WHERE c.usuario_id = 111
ORDER BY c.fecha_comision DESC;

-- Resumen de comisiones de YONNY
SELECT
    COUNT(*) as total_comisiones,
    SUM(CASE WHEN tipo_comision = 'inscripcion' THEN 1 ELSE 0 END) as inscripciones,
    SUM(CASE WHEN tipo_comision = 'financiamiento' THEN 1 ELSE 0 END) as financiamientos,
    SUM(CASE WHEN moneda = 'S/.' THEN monto_comision ELSE 0 END) as total_soles,
    SUM(CASE WHEN moneda = '$' THEN monto_comision ELSE 0 END) as total_dolares
FROM comisiones
WHERE usuario_id = 111;

-- RESULTADO ESPERADO:
-- - 2 comisiones de inscripción (conductores 424 y 426)
-- - 1 comisión de financiamiento
-- - Total: 3 comisiones
