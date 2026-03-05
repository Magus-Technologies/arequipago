-- ============================================
-- CREAR PLAN DE FINANCIAMIENTO PARA SOAT
-- ============================================

-- PASO 1: Crear el plan de financiamiento SOAT
INSERT INTO planes_financiamiento (
    nombre_plan,
    cuota_inicial,
    monto_cuota,
    cantidad_cuotas,
    penalizacion_mora,
    frecuencia_pago,
    moneda,
    tasa_interes,
    monto,
    monto_sin_interes,
    tipo_vehicular,
    es_yango,
    cobrar_mora,
    estado,
    aplica_comision,
    monto_comision,
    moneda_comision
)
VALUES (
    'FINANCIAMIENTO SOAT',      -- nombre_plan
    0.00,                        -- cuota_inicial (puede ser 0 o un monto fijo)
    NULL,                        -- monto_cuota (se calcula según el monto total)
    NULL,                        -- cantidad_cuotas (se define al crear el financiamiento)
    5.00,                        -- penalizacion_mora (5 soles por día de mora)
    'mensual',                   -- frecuencia_pago
    'S/.',                       -- moneda
    0.00,                        -- tasa_interes (0% si no cobran interés)
    NULL,                        -- monto (se define al crear el financiamiento)
    NULL,                        -- monto_sin_interes
    NULL,                        -- tipo_vehicular (NULL porque aplica a carros y motos)
    0,                           -- es_yango (NO)
    1,                           -- cobrar_mora (SÍ)
    'activo',                    -- estado
    0,                           -- aplica_comision (NO, a menos que quieran comisión)
    NULL,                        -- monto_comision
    'S/.'                        -- moneda_comision
);

-- Obtener el ID del plan recién creado
SET @id_plan_soat = LAST_INSERT_ID();

-- PASO 2: Crear los productos SOAT vinculados al plan
INSERT INTO productosv2 (
    nombre, 
    codigo, 
    cantidad, 
    tipo_producto, 
    categoria, 
    precio, 
    precio_venta, 
    moneda, 
    estado, 
    oficina, 
    fecha_registro,
    id_plan,
    ruc,
    razon_social
)
VALUES 
(
    'SOAT CARRO', 
    'SOAT-C-001', 
    0, 
    'Intangible', 
    'SOAT', 
    0.00, 
    0.00, 
    'S/.', 
    '1', 
    1, 
    CURDATE(),
    @id_plan_soat,
    '',
    ''
),
(
    'SOAT MOTO', 
    'SOAT-M-001', 
    0, 
    'Intangible', 
    'SOAT', 
    0.00, 
    0.00, 
    'S/.', 
    '1', 
    1, 
    CURDATE(),
    @id_plan_soat,
    '',
    ''
);

-- PASO 3: Verificar que se crearon correctamente
SELECT 
    idplan_financiamiento, 
    nombre_plan, 
    estado 
FROM planes_financiamiento 
WHERE nombre_plan = 'FINANCIAMIENTO SOAT';

SELECT 
    idproductosv2, 
    nombre, 
    codigo, 
    tipo_producto, 
    categoria, 
    id_plan 
FROM productosv2 
WHERE categoria = 'SOAT' 
ORDER BY idproductosv2 DESC 
LIMIT 2;
