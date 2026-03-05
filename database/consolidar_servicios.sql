-- =====================================================
-- CONSOLIDACIÓN DE GRUPOS DE SERVICIOS
-- =====================================================
-- Este script consolida los grupos separados de servicios
-- (SOAT, Revisión Técnica, GPS, Mantenimiento) en un solo
-- grupo llamado "SERVICIOS" con variantes.
-- =====================================================

-- Paso 1: Crear el nuevo grupo "SERVICIOS"
INSERT INTO planes_financiamiento (
    nombre_plan,
    cuota_inicial,
    monto_cuota,
    cantidad_cuotas,
    frecuencia_pago,
    moneda,
    tasa_interes,
    monto,
    monto_sin_interes,
    cobrar_mora,
    estado,
    aplica_comision
) VALUES (
    'SERVICIOS',
    NULL,
    NULL,
    NULL,
    'semanal',
    'S/.',
    0.00,
    NULL,
    NULL,
    1,
    'activo',
    1
);

-- Obtener el ID del nuevo plan (será el último insertado)
SET @nuevo_plan_id = LAST_INSERT_ID();

-- Paso 2: Migrar variantes de Mantenimiento IncaMotors (Plan 44)
INSERT INTO grupos_variantes (
    idplan_financiamiento,
    nombre_variante,
    cuota_inicial,
    monto_inscripcion,
    monto_cuota,
    cantidad_cuotas,
    penalizacion_mora,
    frecuencia_pago,
    moneda,
    tasa_interes,
    monto,
    monto_sin_interes,
    fecha_inicio,
    fecha_fin,
    monto_comision,
    moneda_comision
)
SELECT 
    @nuevo_plan_id,
    CONCAT('Mantenimiento - ', nombre_variante),
    cuota_inicial,
    monto_inscripcion,
    monto_cuota,
    cantidad_cuotas,
    penalizacion_mora,
    frecuencia_pago,
    moneda,
    tasa_interes,
    monto,
    monto_sin_interes,
    fecha_inicio,
    fecha_fin,
    monto_comision,
    moneda_comision
FROM grupos_variantes
WHERE idplan_financiamiento = 44;

-- Paso 3: Migrar variantes de Revisión Técnica (Plan 47)
INSERT INTO grupos_variantes (
    idplan_financiamiento,
    nombre_variante,
    cuota_inicial,
    monto_inscripcion,
    monto_cuota,
    cantidad_cuotas,
    penalizacion_mora,
    frecuencia_pago,
    moneda,
    tasa_interes,
    monto,
    monto_sin_interes,
    fecha_inicio,
    fecha_fin,
    monto_comision,
    moneda_comision
)
SELECT 
    @nuevo_plan_id,
    nombre_variante,
    cuota_inicial,
    monto_inscripcion,
    monto_cuota,
    cantidad_cuotas,
    penalizacion_mora,
    frecuencia_pago,
    moneda,
    tasa_interes,
    monto,
    monto_sin_interes,
    fecha_inicio,
    fecha_fin,
    monto_comision,
    moneda_comision
FROM grupos_variantes
WHERE idplan_financiamiento = 47;

-- Paso 4: Crear variante para GPS VEHÍCULAR (Plan 46 no tiene variantes, crear una)
INSERT INTO grupos_variantes (
    idplan_financiamiento,
    nombre_variante,
    cuota_inicial,
    monto_inscripcion,
    monto_cuota,
    cantidad_cuotas,
    penalizacion_mora,
    frecuencia_pago,
    moneda,
    tasa_interes,
    monto,
    monto_sin_interes,
    fecha_inicio,
    fecha_fin,
    monto_comision,
    moneda_comision
)
SELECT 
    @nuevo_plan_id,
    'GPS VEHÍCULAR',
    cuota_inicial,
    NULL,
    monto_cuota,
    cantidad_cuotas,
    penalizacion_mora,
    frecuencia_pago,
    moneda,
    tasa_interes,
    monto,
    monto_sin_interes,
    fecha_inicio,
    fecha_fin,
    NULL,
    'S/.'
FROM planes_financiamiento
WHERE idplan_financiamiento = 46;

-- Paso 5: Crear variante para SOAT (Plan 48 no tiene variantes, crear una genérica)
INSERT INTO grupos_variantes (
    idplan_financiamiento,
    nombre_variante,
    cuota_inicial,
    monto_inscripcion,
    monto_cuota,
    cantidad_cuotas,
    penalizacion_mora,
    frecuencia_pago,
    moneda,
    tasa_interes,
    monto,
    monto_sin_interes,
    fecha_inicio,
    fecha_fin,
    monto_comision,
    moneda_comision
) VALUES (
    @nuevo_plan_id,
    'SOAT',
    0.00,
    NULL,
    NULL,
    NULL,
    NULL,
    'semanal',
    'S/.',
    0.00,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'S/.'
);

-- Paso 6: Desactivar los planes antiguos (NO eliminarlos para mantener historial)
-- ⚠️ NO actualizamos la tabla financiamiento porque tiene id_variante y otras relaciones
-- Los financiamientos existentes seguirán funcionando con los planes desactivados
UPDATE planes_financiamiento 
SET estado = 'inactivo' 
WHERE idplan_financiamiento IN (44, 46, 47, 48);

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Mostrar el nuevo plan creado
SELECT 'NUEVO PLAN CREADO:' as mensaje;
SELECT * FROM planes_financiamiento WHERE idplan_financiamiento = @nuevo_plan_id;

-- Mostrar las variantes migradas
SELECT 'VARIANTES MIGRADAS:' as mensaje;
SELECT * FROM grupos_variantes WHERE idplan_financiamiento = @nuevo_plan_id;

-- Mostrar planes antiguos desactivados
SELECT 'PLANES ANTIGUOS DESACTIVADOS:' as mensaje;
SELECT idplan_financiamiento, nombre_plan, estado 
FROM planes_financiamiento 
WHERE idplan_financiamiento IN (44, 46, 47, 48);

-- =====================================================
-- NOTAS IMPORTANTES:
-- =====================================================
-- 1. Los planes antiguos se desactivan pero NO se eliminan
-- 2. Los financiamientos existentes NO se tocan (siguen con sus planes originales)
-- 3. Los planes desactivados seguirán funcionando para financiamientos existentes
-- 4. Solo los NUEVOS financiamientos usarán el grupo "SERVICIOS"
-- 5. Las variantes mantienen sus configuraciones originales
-- 6. Si necesitas revertir, solo reactiva los planes antiguos
-- =====================================================
