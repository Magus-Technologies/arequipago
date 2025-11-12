-- =====================================================
-- SCRIPT PARA HABILITAR/DESHABILITAR DEPARTAMENTOS
-- Creado para permitir al Director gestionar departamentos
-- =====================================================

-- 1. Crear tabla de configuración de departamentos habilitados
CREATE TABLE IF NOT EXISTS `departamentos_habilitados` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `iddepast` INT(11) NOT NULL COMMENT 'ID del departamento',
  `nombre` VARCHAR(100) NOT NULL COMMENT 'Nombre del departamento',
  `habilitado` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Habilitado, 0=Deshabilitado',
  `fecha_modificacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modificado_por` INT(11) NULL COMMENT 'ID del usuario que modificó',
  PRIMARY KEY (`id`),
  UNIQUE KEY `iddepast` (`iddepast`),
  KEY `idx_habilitado` (`habilitado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuración de departamentos habilitados/deshabilitados';

-- 2. Insertar los 3 departamentos iniciales como habilitados
INSERT INTO `departamentos_habilitados` (`iddepast`, `nombre`, `habilitado`) VALUES
(8, 'AREQUIPA', 1),
(17, 'LA LIBERTAD', 1),
(19, 'LIMA', 1)
ON DUPLICATE KEY UPDATE 
  `nombre` = VALUES(`nombre`),
  `habilitado` = VALUES(`habilitado`);

-- 3. Opcional: Insertar todos los departamentos (deshabilitados por defecto)
-- Descomenta las siguientes líneas si quieres tener todos los departamentos en la tabla
/*
INSERT INTO `departamentos_habilitados` (`iddepast`, `nombre`, `habilitado`) VALUES
(5, 'AMAZONAS', 0),
(6, 'ANCASH', 0),
(7, 'APURIMAC', 0),
(8, 'AREQUIPA', 1),
(9, 'AYACUCHO', 0),
(10, 'CAJAMARCA', 0),
(11, 'CUSCO', 0),
(12, 'HUANCAVELICA', 0),
(13, 'CALLAO', 0),
(14, 'HUANUCO', 0),
(15, 'ICA', 0),
(16, 'JUNIN', 0),
(17, 'LA LIBERTAD', 1),
(18, 'LAMBAYEQUE', 0),
(19, 'LIMA', 1),
(20, 'LORETO', 0),
(21, 'MADRE DE DIOS', 0),
(22, 'MOQUEGUA', 0),
(23, 'PASCO', 0),
(24, 'PIURA', 0),
(25, 'PUNO', 0),
(26, 'SAN MARTÍN', 0),
(27, 'TACNA', 0),
(28, 'TUMBES', 0),
(29, 'UCAYALI', 0)
ON DUPLICATE KEY UPDATE 
  `nombre` = VALUES(`nombre`);
*/

-- 4. Crear tabla de historial de cambios (opcional pero recomendado)
CREATE TABLE IF NOT EXISTS `departamentos_historial` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `iddepast` INT(11) NOT NULL,
  `nombre_departamento` VARCHAR(100) NOT NULL,
  `accion` VARCHAR(50) NOT NULL COMMENT 'HABILITADO o DESHABILITADO',
  `usuario_id` INT(11) NOT NULL,
  `usuario_nombre` VARCHAR(255) NOT NULL,
  `fecha` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_iddepast` (`iddepast`),
  KEY `idx_fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Historial de cambios en departamentos';

-- =====================================================
-- CONSULTAS ÚTILES PARA VERIFICAR
-- =====================================================

-- Ver departamentos habilitados
-- SELECT * FROM departamentos_habilitados WHERE habilitado = 1 ORDER BY nombre;

-- Ver todos los departamentos con su estado
-- SELECT * FROM departamentos_habilitados ORDER BY nombre;

-- Ver historial de cambios
-- SELECT * FROM departamentos_historial ORDER BY fecha DESC LIMIT 20;

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================
