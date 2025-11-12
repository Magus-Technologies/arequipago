-- ============================================
-- Script para crear tabla ventas_anuladas
-- Base de datos: magusqao_arequipa
-- Fecha: 2025-10-27
-- ============================================

USE magusqao_arequipa;

-- Crear tabla ventas_anuladas
DROP TABLE IF EXISTS `ventas_anuladas`;
CREATE TABLE `ventas_anuladas` (
  `id_venta` int(11) NOT NULL,
  `fecha` date NOT NULL COMMENT 'Fecha de anulación',
  `motivo` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'Motivo de la anulación',
  PRIMARY KEY (`id_venta`) USING BTREE,
  CONSTRAINT `fk_ventas_anuladas_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'Tabla para registrar ventas anuladas' ROW_FORMAT = Dynamic;

-- Crear índice para búsquedas por fecha
CREATE INDEX `idx_fecha_anulacion` ON `ventas_anuladas`(`fecha` ASC) USING BTREE;

-- Verificar que la tabla se creó correctamente
SELECT 'Tabla ventas_anuladas creada exitosamente' AS resultado;
