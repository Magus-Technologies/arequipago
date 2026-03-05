-- ============================================
-- MIGRACIÓN: CREAR TABLA ADJUDICACIONES
-- Base de datos: magusqao_arequipa
-- Ejecutar este archivo en phpMyAdmin o MySQL
-- ============================================

-- Eliminar tabla si existe (para limpiar instalación anterior)
DROP TABLE IF EXISTS `adjudicaciones`;

-- Crear tabla adjudicaciones
CREATE TABLE `adjudicaciones` (
  `id_adjudicacion` INT AUTO_INCREMENT PRIMARY KEY,
  `id_financiamiento` INT NOT NULL,
  `tipo_adjudicacion` ENUM('sorteo', 'directo_con_inicial', 'crediyango') NOT NULL DEFAULT 'directo_con_inicial',
  `fecha_adjudicacion` DATE NOT NULL COMMENT 'Fecha en que ganó o se registró',
  `fecha_entrega_programada` DATE NULL COMMENT 'Fecha programada de entrega',
  `fecha_entrega_real` DATE NULL COMMENT 'Fecha real de entrega (desde financiamiento.fecha_entrega)',
  `dias_demora` INT NULL COMMENT 'Días entre adjudicación y entrega',
  `mes_sorteo` VARCHAR(7) NULL COMMENT 'Mes del sorteo (YYYY-MM)',
  `observaciones` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_financiamiento`) REFERENCES `financiamiento`(`idfinanciamiento`) ON DELETE CASCADE,
  INDEX `idx_tipo_adjudicacion` (`tipo_adjudicacion`),
  INDEX `idx_fecha_adjudicacion` (`fecha_adjudicacion`),
  INDEX `idx_mes_sorteo` (`mes_sorteo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Registro de adjudicaciones de vehículos';

-- ============================================
-- INSERTAR DATOS DE PRUEBA (OPCIONAL)
-- Comentado por defecto - descomenta si necesitas datos de prueba
-- ============================================

/*
INSERT INTO adjudicaciones (id_financiamiento, tipo_adjudicacion, fecha_adjudicacion, fecha_entrega_programada, fecha_entrega_real, dias_demora, mes_sorteo, observaciones)
SELECT
    f.idfinanciamiento,
    CASE
        WHEN RAND() < 0.3 THEN 'sorteo'
        WHEN RAND() < 0.6 THEN 'directo_con_inicial'
        ELSE 'crediyango'
    END as tipo_adjudicacion,
    f.fecha_inicio as fecha_adjudicacion,
    DATE_ADD(f.fecha_inicio, INTERVAL 30 DAY) as fecha_entrega_programada,
    f.fecha_entrega as fecha_entrega_real,
    CASE
        WHEN f.fecha_entrega IS NOT NULL THEN DATEDIFF(f.fecha_entrega, f.fecha_inicio)
        ELSE NULL
    END as dias_demora,
    DATE_FORMAT(f.fecha_inicio, '%Y-%m') as mes_sorteo,
    CONCAT('Adjudicación automática - Financiamiento #', f.idfinanciamiento) as observaciones
FROM financiamiento f
INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
WHERE f.estado_eliminado = 0
  AND p.categoria IN ('Vehiculo', 'Moto')
  AND (f.estado_entrega = 'entregado' OR f.estado_entrega = 'pendiente')
LIMIT 110;
*/

-- ============================================
-- VERIFICACIÓN
-- ============================================
-- Ejecuta estas consultas después de la migración para verificar

-- Ver estructura de la tabla
-- DESCRIBE adjudicaciones;

-- Contar registros
-- SELECT COUNT(*) as total_adjudicaciones FROM adjudicaciones;

-- Ver primeros registros
-- SELECT * FROM adjudicaciones LIMIT 10;
