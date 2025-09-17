
CREATE TABLE IF NOT EXISTS `beneficios` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    
    -- Información básica del producto
    `nombre` VARCHAR(255) NOT NULL,
    `categoria` INT(11) NOT NULL,
    `descripcion` TEXT NULL DEFAULT NULL,
    `codigo_producto` VARCHAR(100) NULL DEFAULT NULL,
    
    -- Información de marca y modelo
    `marca` VARCHAR(100) NULL DEFAULT NULL,
    `modelo` VARCHAR(100) NULL DEFAULT NULL,
    `proveedor` VARCHAR(200) NULL DEFAULT NULL,
    
    -- Precios y financiamiento
    `precio_contado` DECIMAL(10,2) NOT NULL,
    `precio_financiado` DECIMAL(10,2) NULL DEFAULT NULL,
    `cuotas_disponibles` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Ej: 12,18,24',
    `tasa_interes` DECIMAL(5,2) NULL DEFAULT NULL COMMENT 'Porcentaje de tasa de interés',
    
    -- Información del producto
    `especificaciones` JSON NULL DEFAULT NULL COMMENT 'Especificaciones técnicas en JSON',
    `peso` DECIMAL(8,2) NULL DEFAULT NULL COMMENT 'Peso en kilogramos',
    `dimensiones` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Dimensiones del producto',
    `garantia_meses` INT(3) NULL DEFAULT NULL COMMENT 'Meses de garantía',
    
    -- Imágenes
    `imagen_principal` VARCHAR(500) NULL DEFAULT NULL,
    `galeria_imagenes` JSON NULL DEFAULT NULL COMMENT 'Array de URLs de imágenes adicionales',
    
    -- Stock y disponibilidad
    `stock_disponible` INT(11) NULL DEFAULT NULL,
    `disponible` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Disponible, 0=No disponible',
    
    -- Información adicional
    `requisitos` TEXT NULL DEFAULT NULL COMMENT 'Requisitos para obtener el beneficio',
    
    -- Estados del registro
    `activo` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Eliminado (soft delete)',
    
    -- Auditoría
    `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Claves
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_codigo_producto` (`codigo_producto`),
    KEY `idx_categoria` (`categoria`),
    KEY `idx_disponible` (`disponible`),
    KEY `idx_activo` (`activo`),
    KEY `idx_fecha_creacion` (`fecha_creacion`),
    KEY `idx_nombre` (`nombre`),
    KEY `idx_marca_modelo` (`marca`, `modelo`),
    KEY `idx_categoria_disponible_activo` (`categoria`, `disponible`, `activo`),
    KEY `idx_precio_contado` (`precio_contado`),
    
    -- Clave foránea
    CONSTRAINT `fk_beneficios_categoria` 
        FOREIGN KEY (`categoria`) 
        REFERENCES `categoria_producto` (`idcategoria_producto`)
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
        
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci 
COMMENT='Tabla para almacenar productos disponibles para financiamiento';