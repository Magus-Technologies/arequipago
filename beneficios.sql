DROP TABLE IF EXISTS `beneficios`;

CREATE TABLE `beneficios` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(255) NOT NULL,
    `plan_financiamiento_id` INT(11) NOT NULL,
    `categoria` INT(11) NULL DEFAULT NULL,
    `descripcion` TEXT NULL DEFAULT NULL,
    `cuota_inicial` DECIMAL(10,2) NOT NULL,
    `cantidad_cuotas` INT(3) NOT NULL,
    `cuota_mensual` DECIMAL(10,2) NOT NULL,
    `imagen` VARCHAR(500) NULL DEFAULT NULL,
    `disponible` TINYINT(1) NOT NULL DEFAULT 1,
    `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_plan_financiamiento` (`plan_financiamiento_id`),
    KEY `idx_categoria` (`categoria`),
    KEY `idx_disponible` (`disponible`),
    KEY `idx_nombre` (`nombre`),
    KEY `idx_plan_disponible` (`plan_financiamiento_id`, `disponible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;