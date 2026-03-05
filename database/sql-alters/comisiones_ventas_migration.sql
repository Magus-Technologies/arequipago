-- ============================================
-- MIGRACIÓN: SISTEMA DE COMISIONES PARA VENTAS
-- Base de datos: magusqao_arequipa
-- Fecha: 2026-01-27
-- Descripción: Amplía el sistema de comisiones existente para soportar comisiones de NOTAS DE VENTA
-- ============================================

-- ============================================
-- 1. MODIFICAR TABLA `comisiones` EXISTENTE
-- ============================================

-- Ampliar el ENUM de tipo_comision para incluir 'venta'
ALTER TABLE comisiones
MODIFY COLUMN tipo_comision ENUM('inscripcion','financiamiento','venta')
CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
COMMENT 'Tipo de comisión: inscripcion, financiamiento o venta';

-- Agregar columnas necesarias para comisiones de ventas
-- Nota: Si la columna ya existe, este comando dará error pero puedes continuar
ALTER TABLE comisiones
ADD COLUMN porcentaje_aplicado DECIMAL(5,2) NULL
COMMENT 'Porcentaje usado para calcular la comisión' AFTER monto_comision;

ALTER TABLE comisiones
ADD COLUMN tipo_calculo ENUM('automatica','manual')
DEFAULT 'manual'
COMMENT 'Cómo se calculó: automatica (por escala) o manual (fija)' AFTER porcentaje_aplicado;

-- Agregar índice para mejorar consultas
ALTER TABLE comisiones
ADD INDEX idx_tipo_calculo (tipo_calculo);

-- ============================================
-- 2. MODIFICAR TABLA `configuracion_comisiones` EXISTENTE
-- ============================================

-- Ampliar el ENUM de tipo_comision
ALTER TABLE configuracion_comisiones
MODIFY COLUMN tipo_comision ENUM('inscripcion','financiamiento','venta')
CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

-- Agregar columnas para configuración de ventas
ALTER TABLE configuracion_comisiones
ADD COLUMN modo_calculo ENUM('escala','fijo')
DEFAULT 'fijo'
COMMENT 'Modo de cálculo: escala (por rangos) o fijo (monto/porcentaje fijo)' AFTER monto_comision;

ALTER TABLE configuracion_comisiones
ADD COLUMN porcentaje_fijo DECIMAL(5,2) NULL
COMMENT 'Porcentaje fijo a aplicar (si modo_calculo=fijo)' AFTER modo_calculo;

-- ============================================
-- 3. CREAR TABLA `comisiones_escalas` (NUEVA)
-- ============================================

CREATE TABLE IF NOT EXISTS comisiones_escalas (
    id_escala INT AUTO_INCREMENT PRIMARY KEY,
    nombre_escala VARCHAR(100) NOT NULL COMMENT 'Nombre descriptivo de la escala',
    monto_desde DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Monto mínimo de ventas para esta escala',
    monto_hasta DECIMAL(10,2) NULL COMMENT 'Monto máximo (NULL = sin límite superior)',
    porcentaje_comision DECIMAL(5,2) NOT NULL COMMENT 'Porcentaje de comisión a aplicar',
    activo TINYINT(1) DEFAULT 1 COMMENT '1=activa, 0=inactiva',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_activo (activo),
    INDEX idx_montos (monto_desde, monto_hasta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Escalas de comisión por volumen de ventas';

-- ============================================
-- 4. INSERTAR DATOS INICIALES
-- ============================================

-- Insertar configuración inicial para comisiones de ventas
-- Modo por defecto: ESCALA AUTOMÁTICA
INSERT INTO configuracion_comisiones
    (tipo_comision, tipo_vehiculo, usuario_id, monto_comision, modo_calculo, porcentaje_fijo, estado, fecha_creacion)
VALUES
    ('venta', NULL, NULL, 0.00, 'escala', NULL, 1, NOW());

-- Insertar escalas de comisión por defecto
INSERT INTO comisiones_escalas (nombre_escala, monto_desde, monto_hasta, porcentaje_comision, activo) VALUES
('Básico', 0.00, 5000.00, 3.00, 1),
('Intermedio', 5000.01, 10000.00, 5.00, 1),
('Avanzado', 10000.01, 20000.00, 7.00, 1),
('Premium', 20000.01, NULL, 10.00, 1);

-- ============================================
-- 5. VERIFICACIÓN (QUERIES PARA REVISAR)
-- ============================================

-- Verificar estructura de tabla comisiones
-- SHOW COLUMNS FROM comisiones;

-- Verificar estructura de tabla configuracion_comisiones
-- SHOW COLUMNS FROM configuracion_comisiones;

-- Verificar escalas creadas
-- SELECT * FROM comisiones_escalas ORDER BY monto_desde;

-- Verificar configuración de ventas
-- SELECT * FROM configuracion_comisiones WHERE tipo_comision = 'venta';

-- ============================================
-- 6. NOTAS IMPORTANTES
-- ============================================

/*
INSTRUCCIONES DE EJECUCIÓN:

1. Si algún ALTER TABLE da error "Duplicate column name", es porque la columna ya existe.
   Simplemente ignora ese error y continúa con el siguiente comando.

2. Ejecuta los comandos uno por uno en phpMyAdmin para identificar cuál falla.

3. Si ya ejecutaste parte del script antes, algunas columnas pueden existir.

FLUJO DEL SISTEMA:

1. CONFIGURACIÓN (Una vez - Director):
   - Accede a vista de configuración de comisiones
   - Elige modo:
     * ESCALA: Comisiones automáticas según rangos (tabla comisiones_escalas)
     * FIJO: Porcentaje o monto fijo para todas las ventas

2. AL GUARDAR NOTA DE VENTA (Automático):
   - Si usuario = Asesor (rol 2) Y tipo_doc = 6 (NOTA DE VENTA)
   - VentasController consulta configuracion_comisiones WHERE tipo_comision='venta'
   - Calcula comisión según modo configurado
   - Inserta en tabla comisiones con tipo_comision='venta'

3. VISUALIZACIÓN:
   - Vista comisiones.php ya lee de tabla comisiones
   - Solo agregar filtro para tipo_comision='venta'
   - Mostrar número de venta (serie-numero)

EJEMPLO:
- Venta S/. 3,000  → Escala Básico (3%)     → Comisión: S/. 90
- Venta S/. 7,500  → Escala Intermedio (5%) → Comisión: S/. 375
- Venta S/. 15,000 → Escala Avanzado (7%)   → Comisión: S/. 1,050
- Venta S/. 25,000 → Escala Premium (10%)   → Comisión: S/. 2,500

CAMBIAR A MODO FIJO (5% para todas las ventas):
UPDATE configuracion_comisiones
SET modo_calculo = 'fijo', porcentaje_fijo = 5.00
WHERE tipo_comision = 'venta';
*/
