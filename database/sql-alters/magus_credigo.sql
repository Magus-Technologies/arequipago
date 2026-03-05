/*
 Navicat Premium Dump SQL

 Source Server         : localhist
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : localhost:3306
 Source Schema         : magus_credigo

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 23/01/2026 12:04:40
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for almacenes
-- ----------------------------
DROP TABLE IF EXISTS `almacenes`;
CREATE TABLE `almacenes`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sucursal_id` int NOT NULL COMMENT 'Sucursal a la que pertenece',
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre del almacén (ej: Almacén Oficina 1)',
  `codigo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Código único del almacén (ej: ALM-AQP-001)',
  `direccion` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Dirección física del almacén',
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Teléfono del almacén',
  `es_principal` tinyint(1) NULL DEFAULT 0 COMMENT 'Si es el almacén principal de la sucursal',
  `capacidad_m3` decimal(10, 2) NULL DEFAULT NULL COMMENT 'Capacidad en metros cúbicos (opcional)',
  `responsable_id` int NULL DEFAULT NULL COMMENT 'Usuario responsable del almacén',
  `estado` tinyint(1) NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `codigo`(`codigo` ASC) USING BTREE,
  INDEX `idx_sucursal`(`sucursal_id` ASC) USING BTREE,
  INDEX `idx_codigo`(`codigo` ASC) USING BTREE,
  INDEX `idx_responsable`(`responsable_id` ASC) USING BTREE,
  INDEX `idx_estado`(`estado` ASC) USING BTREE,
  CONSTRAINT `almacenes_ibfk_1` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `almacenes_ibfk_2` FOREIGN KEY (`responsable_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Almacenes por sucursal' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of almacenes
-- ----------------------------

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache
-- ----------------------------

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache_locks
-- ----------------------------

-- ----------------------------
-- Table structure for categorias_producto
-- ----------------------------
DROP TABLE IF EXISTS `categorias_producto`;
CREATE TABLE `categorias_producto`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre de la categoría',
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Descripción de la categoría',
  `requiere_caracteristicas` tinyint(1) NULL DEFAULT 0 COMMENT 'Si requiere tabla especializada de características',
  `tipo_caracteristica_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'Tipo de características que requiere',
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Nombre del icono para la UI (lucide-react)',
  `color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Color para la UI (hex)',
  `estado` tinyint(1) NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `nombre`(`nombre` ASC) USING BTREE,
  INDEX `idx_nombre`(`nombre` ASC) USING BTREE,
  INDEX `idx_estado`(`estado` ASC) USING BTREE,
  INDEX `idx_tipo_caracteristica`(`tipo_caracteristica_id` ASC) USING BTREE,
  CONSTRAINT `categorias_producto_ibfk_1` FOREIGN KEY (`tipo_caracteristica_id`) REFERENCES `tipos_caracteristicas` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 47 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Catálogo de categorías de productos' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of categorias_producto
-- ----------------------------
INSERT INTO `categorias_producto` VALUES (1, 'Celular', 'Equipos celulares y smartphones', 1, 1, 'Smartphone', NULL, 1, '2026-01-12 23:34:24', '2026-01-12 23:34:24');
INSERT INTO `categorias_producto` VALUES (2, 'Vehículo', 'Vehículos motorizados (autos, motos)', 1, 2, 'Car', NULL, 1, '2026-01-12 23:34:24', '2026-01-12 23:34:24');
INSERT INTO `categorias_producto` VALUES (3, 'Llantas', 'Llantas y neumáticos', 1, 3, 'Circle', NULL, 1, '2026-01-12 23:34:24', '2026-01-12 23:34:24');
INSERT INTO `categorias_producto` VALUES (4, 'Chip/Línea Corporativa', 'Chips y planes móviles corporativos', 0, NULL, 'SimCard', NULL, 1, '2026-01-12 23:34:24', '2026-01-12 23:34:24');
INSERT INTO `categorias_producto` VALUES (5, 'Vestimenta', 'Ropa y accesorios (polos, casacas, etc)', 0, NULL, 'Shirt', NULL, 1, '2026-01-12 23:34:24', '2026-01-12 23:34:24');
INSERT INTO `categorias_producto` VALUES (6, 'Baterías', 'Baterías para vehículos', 0, NULL, 'Battery', NULL, 1, '2026-01-12 23:34:24', '2026-01-12 23:34:24');
INSERT INTO `categorias_producto` VALUES (7, 'Aceites y Lubricantes', 'Aceites, lubricantes por volumen', 0, NULL, 'Droplet', NULL, 1, '2026-01-12 23:34:24', '2026-01-12 23:34:24');
INSERT INTO `categorias_producto` VALUES (8, 'Accesorios', 'Accesorios varios', 0, NULL, 'Package', NULL, 1, '2026-01-12 23:34:24', '2026-01-12 23:34:24');
INSERT INTO `categorias_producto` VALUES (9, 'Otros', 'Productos generales', 0, 4, 'Box', NULL, 1, '2026-01-12 23:34:24', '2026-01-12 23:34:24');

-- ----------------------------
-- Table structure for clientes_conductores
-- ----------------------------
DROP TABLE IF EXISTS `clientes_conductores`;
CREATE TABLE `clientes_conductores`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo` tinyint(1) NOT NULL COMMENT '1 = Conductor, 2 = Cliente',
  `usuario_id` int NULL DEFAULT NULL,
  `tipo_doc` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nro_documento` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombres` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `apellido_paterno` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `apellido_materno` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nacionalidad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `fecha_nacimiento` date NULL DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `correo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `num_cod_finan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'Codigo de financiamiento',
  `verificacion_domiciliaria` tinyint(1) NULL DEFAULT NULL,
  `departamento` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `provincia` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `distrito` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `direccion_detallada` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `emergencia_nombre` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `emergencia_telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `emergencia_parentesco` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `laboral_nombre` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `laboral_telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `laboral_puesto` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `laboral_empresa` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `nro_licencia` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `categoria_licencia` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `num_unidad` int NULL DEFAULT NULL,
  `desvinculado` tinyint(1) NULL DEFAULT 0,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `foto_perfil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `foto_perfil_cambiada` smallint NULL DEFAULT 0,
  `logo_yango_asignado_cod` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `recibo_servicios` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `doc_identidad` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `selfie` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `otro_doc_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `otro_doc_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `otro_doc_3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `comentarios` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_nro_documento`(`nro_documento` ASC) USING BTREE,
  INDEX `idx_tipo`(`tipo` ASC) USING BTREE,
  INDEX `idx_tipo_doc`(`tipo_doc` ASC) USING BTREE,
  INDEX `idx_apellidos`(`apellido_paterno` ASC, `apellido_materno` ASC) USING BTREE,
  INDEX `idx_nombres`(`nombres` ASC, `apellido_paterno` ASC, `apellido_materno` ASC) USING BTREE,
  INDEX `idx_nro_licencia`(`nro_licencia` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 575 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of clientes_conductores
-- ----------------------------
INSERT INTO `clientes_conductores` VALUES (1, 1, NULL, 'DNI', '43931817', 'ESTRELLA MARIA DEL CARMEN', 'LOPEZ', 'TURPO', 'PERUANA', '1986-10-27', '916840651', 'marypazalmanzaxxx@gmail.com', '', '0', NULL, '8', '89', '792', 'UPIS SAN JOSE F-7 PAMPAS DE POLANCO T', '-', '-', '-', NULL, NULL, NULL, NULL, 'H43931817', 'AIIA', 36, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (2, 1, NULL, 'DNI', '29704977', 'MAURO', 'CARRILLO', 'LAYME', 'PERUANA', '1975-09-29', '978595735', 'MAURO.CARRILLO@GMAIL.COM', '$2y$12$F/2jS1tTCnjPJO60Lhu5yurAsA86PQxD9OudGbu4.zHBtLe7M83Hu', '0', NULL, '8', '89', '794', '11 DE MAYO MZ.L LT.15 ALTO CAYMA', 'MAURO', '978595735', 'TITULAR', NULL, NULL, NULL, NULL, 'H29704977', 'AIIB', 87, 0, 'fotos/conductores/conductor_67c20cbc579154.56175416.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (3, 1, NULL, 'DNI', '46188666', 'JONATHAN DENNIS', 'HERESI', 'GONZALES', 'PERUANO', '1990-02-09', '984566403', 'xxjdhgxx@gmail.com', '', '0', NULL, '8', '89', '818', 'URB. BUENA VISTA A 45 ', 'GERMAN HERESI', '958747536', 'PADRE', NULL, NULL, NULL, NULL, 'H46188666', 'AIIB', 89, 0, 'fotos/conductores/conductor_67e6d78fbab562.47003142.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (4, 1, NULL, 'DNI', '47006116', 'LEONARDO ELVIS', 'ENRIQUEZ', 'VILCA', 'PERUANA', '1991-06-30', '991327768', 'Lenriquezvilca@gmail.com', '', '0', NULL, '8', '89', '801', 'CALLE MISTI 202', 'DEYSI', '982899854', 'ESPOSA', NULL, NULL, NULL, NULL, 'H47006116', 'AIIB', 35, 0, 'fotos/conductores/conductor_67c2097165b0b7.05342986.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (5, 1, NULL, 'DNI', '40946936', 'MIGUEL ANGEL', 'COAGUILA', 'QUISPE', 'PERUANA', '1981-07-13', '988657078', 'miguelcoaguila278@gmail.com', '', '0', 0, '8', '89', '799', 'APIS LAS ESMERALDAS ZON. C MZ. Ñ LT 6', 'JOHANNA', '936356174', 'ESPOSA', NULL, NULL, NULL, NULL, 'H40946936', 'AIIB', 10, 0, 'fotos/conductores/conductor_67b3da50986c53.45429876.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (6, 1, NULL, 'DNI', '70405740', 'DIEGO ALONSO', 'CARDEÑA', 'CUELLAR', 'PERUANA', '1991-05-28', '906587727', 'DICARDENA2@gmail', '', '0', NULL, '8', '89', '799', 'COOP.JUAN MANUEL POLAR C-22', 'NELLY CARDEÑA', '999471247', 'HERMANA', NULL, NULL, NULL, NULL, 'H70405740', 'AIIB', 90, 0, 'fotos/conductores/conductor_67b896936b60c2.94995820.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (7, 1, NULL, 'DNI', '73482260', 'JENSON JHONATAN', 'CONDORI', 'MAMANI', 'PERUANA', '1992-09-28', '940690367', 'librajjcm@gmail.com', '', '0', NULL, '8', '89', '801', 'ARIAS ARAGUEZ 603', 'CONDUCTOR ', '940690367', 'CONDUCTOR', NULL, NULL, NULL, NULL, 'H73482260', 'AIIB', 70, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (8, 1, NULL, 'DNI', '44111807', 'YORK JOSE', 'VERA', 'OVIEDO', 'PERUANA', '1987-02-13', '982900996', 'yorkveraveraoviedo@gmail.com', '$2y$12$e8QJNok6ihuSUsbIUbQZMOYxvU0xUvHEyuoUbg.p0.VwSRDP3nP/u', '0', NULL, '8', '89', '795', 'AV LIMA 109', 'AURORA LAZARO ', '973639297', 'ESPOSA', NULL, NULL, NULL, NULL, 'H44111807', 'AIIB', 32, 0, 'fotos/conductores/conductor_67bc88ecee1b98.61127527.jpg', 'fotos_perfil/perfil_44111807_1755306370.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (9, 1, NULL, 'DNI', '45125182', 'JAVIER LISANDRO', 'CHAHUAYO', 'DUEÑAS', 'PERUANA', '1988-06-28', '933802027', 'Chahuayoduenasjavier@gmail.com ', '$2y$12$vBJyTFohF1Udh1QKUgmKpOBsZHRFf4dnOLly9VQnbAMolsi2Dk6oe', '0', NULL, '8', '89', '804', 'PJ.ALTO JESUS MZ.E LT. 4', 'JAVIER', '933802027', 'CONDUCTOR', NULL, NULL, NULL, NULL, 'H45125182', 'AIIIC', 65, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (10, 1, NULL, 'DNI', '45481150', 'ELVIS', 'RAMOS', 'SAYA', 'PERUANA', '1987-08-30', '979554306', 'Ramossaya.30@gmail.com', '$2y$12$3jwQNjb4mfwqYgp36rICTOWwwjeb7.Qs6JGRU9/M23O4A6EcEp21W', '0', 0, '8', '89', '804', 'PSJ. LOS CLAVELES  PJ. MIGUEL GRAU ZONA D V 32- A 2', 'LESLY MARIA TURPO', '974189976', 'ESPOSA', NULL, NULL, NULL, NULL, 'H45481150', 'AIIIC', 31, 0, 'fotos/conductores/conductor_67c1d5016943b7.90726985.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (11, 1, NULL, 'DNI', '48223104', 'NESTOR SEGUNDO', 'RAMOS', 'CARRASCO', 'PERUANA', '1991-11-01', '981211010', 'nestornsrc2016@gmail.com', '$2y$12$ysrgHFZTBbwuEFARofOJKeJlUgQyuuuj5/0gaAcDSZLur3kd4a262', '0', NULL, '8', '89', '795', 'AV. MANCO CAPAC MZ 13 LT 1AZN C SEMI RURAL PACHACUTEC', 'LUZMILA SAAVEDRA', '941207527', 'ESPOSA', NULL, NULL, NULL, NULL, 'H48223104', 'AIIB', 95, 0, 'fotos/conductores/conductor_67c21185e8bc03.10266528.jpg', 'fotos_perfil/perfil_48223104_1756479678.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (12, 1, NULL, 'DNI', '72000838', 'VICTOR EMILIO LUDGARDO', 'FUENTES', 'CENTTY', 'PERUANA', '2004-04-26', '943948080', 'victorfc0426@gmail.com', '$2y$12$CIRsL2aYdEPsSp13TGxStePtvIQAoa.fwvG4j0AsqA.7n.2nrFvp6', '0', 0, '8', '89', '802', 'CALLE SAENZ PEÑA 332', 'ANNE ', '994719154', 'MAMA', NULL, NULL, NULL, NULL, 'H72000838', 'AIIB', 26, 0, 'fotos/conductores/conductor_679e809f1efb57.02987538.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (13, 1, NULL, 'DNI', '42847681', 'JUAN CARLOS', 'LOPEZ', '-', 'PERUANA', '1977-01-10', '956299109', 'JUAN5432151@HOTMAIL.COM', '$2y$12$X4lCI2R2HjFzWC9pH6SuQuOpbTDfWzBt0BRpwA3D5EVPQKT2pJWOG', '0', 0, '8', '89', '804', 'C. MARISCAL CASTILLA SUCRE N°103', 'JUAN CARLOS', '942379627', 'CONDUCTOR', NULL, NULL, NULL, NULL, 'H42847681', 'AIIIC', 54, 0, 'fotos/conductores/conductor_67c20174a00a84.43136909.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (14, 1, NULL, 'DNI', '44892041', 'VICTOR HUGO', 'PACCO', 'MAYHUA', 'PERUANO', '1987-05-07', '949105426', 'PACOSUPERVISOR1@GMAIL.COM', '', '0', NULL, '8', '89', '796', 'VIRGEN DE LA CANDELARIA MZE LT 9 ZN A ', 'JULIO CESAR ', '933525329', 'SOBRINO', NULL, NULL, NULL, NULL, 'H44892041', 'AIIB', 5, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (15, 1, NULL, 'DNI', '46710456', 'IVAN', 'BAUTISTA', 'MONROY', 'PERUANA', '1983-01-29', '929744885', 'ivanbautistamonroy62@gmail.com', '', '0', NULL, '8', '89', '795', 'VILLA MAGISTERIAL ZONA 4 MZ. N LT. 6', 'ISACC BAUTISTA', '982054491', 'HIJO', NULL, NULL, NULL, NULL, 'H46710456', 'AIIIC', 24, 0, 'fotos/conductores/conductor_67bca4383c7785.14930606.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (16, 1, NULL, 'DNI', '48736062', 'HENRY', 'SANCHEZ', 'TAPIA', 'PERUANA', '1995-02-28', '945612744', 'Henrysanchez48star@gmail.com', '', '0', 0, '8', '89', '799', 'URB. VILLA MANUELITO MZ C LT 8', 'MILAGROS TACO', '927420689', 'ESPOSA', NULL, NULL, NULL, NULL, 'H48736062', 'AIIA', 97, 0, 'fotos/conductores/conductor_679e86ae0177a1.46030232.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (17, 1, NULL, 'DNI', '29709761', 'MARTIN JOSE', 'RAMOS', 'PACCO', 'PERUANA ', '1976-08-29', '910351251', 'MARTINAUTOPREMIUN@GMAIL.COM', '$2y$12$hXfeZy9YoyGubV1aRGqg5e6WgSCkMhDDHRPDzoRlcQnStYyaYZbKa', '0', NULL, '8', '89', '814', 'CENTRO POBLADO EL PASTO MZ.E LT.08', 'LEILI', '955421729', 'CONVIVIENTE', NULL, NULL, NULL, NULL, 'Q29709761', 'AIIB', 53, 0, 'fotos/conductores/conductor_679e86bbd4f370.46400845.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (18, 1, NULL, 'DNI', '42861306', 'WILBUR JAVIER', 'RAMOS', 'LLANOS', 'PERUANO', '1982-10-13', '980037770', 'Wilja10@gmail.com', '', '0', NULL, '8', '89', '792', 'UPIS RAMIRO PRIALE C5 ', 'LILIANA', '936811133', 'CONVIVIENTE', NULL, NULL, NULL, NULL, 'U42861306', 'AIIB', 2, 0, 'fotos/conductores/conductor_679e8a3856db99.11220673.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (19, 1, NULL, 'DNI', '40405973', 'JOSE', 'PANDIA', 'PANDIA', 'PERUANA ', '1976-09-18', '929289400', 'PANDIA.PANDIA@GMAIL.COM', '', '0', NULL, '8', '89', '795', 'PERBARBO MZ. E LT. 9 SECTOR PERU II ', 'TERESA', '954243233', 'ESPOSA', NULL, NULL, NULL, NULL, 'H40405973', 'AIIB', 43, 0, 'fotos/conductores/conductor_679e8c4248d170.16491219.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (20, 1, NULL, 'DNI', '44499839', 'MIGUEL GUSTAVO', 'TURPO', 'TURPO', 'PERUANA', '1986-05-20', '964123519', 'mtt.tavo@gmail.com', '', '0', 0, '8', '89', '804', 'CALLE ALTO ALIANZA 117 - MIGUEL GRAU', 'NANCY HUANCA', '932424725', 'ESPOSA', NULL, NULL, NULL, NULL, 'H44499839', 'AIIIC', 20, 0, 'fotos/conductores/conductor_67b89889386b63.76763855.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (21, 1, NULL, 'DNI', '44612278', 'RONAL IVAN', 'LAURA', 'CCAMA', 'PERUANA', '1987-10-08', '982921763', 'lronalivan@gmail.com ', '', '17', NULL, 'notdepartamento', '89', '814', 'P.J. LA UNION MZ. F L. 5 ', 'RONAL ', '982921763', 'CONDUCTOR', NULL, NULL, NULL, NULL, 'U44612278', 'AIIB', 42, 0, 'fotos/conductores/conductor_679e915383a779.59303442.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (22, 1, NULL, 'DNI', '29275996', 'CESAR BAYARDO', 'SUCAPUCA', 'CAYO', 'PERUANO', '1962-09-16', '959162224', 'rasec.bayardo16@gmail.com', '', '0', NULL, '8', '89', '801', 'JUAN MANUEL POLAR 518', 'KAREN SOFIA', '980522937', 'HIJA', NULL, NULL, NULL, NULL, 'H29275996', 'AIIB', 1, 0, 'fotos/conductores/conductor_679e9b21257cf3.73921114.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (23, 1, NULL, 'DNI', '80503761', 'JOSE WILFREDO', 'VIZCARRA', 'CALSIN', 'PERUANA', '1974-08-04', '921321006', 'sionvizcarracalsin@gmail.com', '$2y$12$yAq9oOZLh0UVYZX5BaBxQufEhMbTZmgkDEfOHmNa1AnY7rRlanR3q', '0', NULL, '8', '89', '792', 'PORTALES DEL MIRADOR MZ. O LT. 1', 'IRVIN VIZCARRA', '924402809', 'HIJO', NULL, NULL, NULL, NULL, 'H80503761', 'AIIA', 16, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (24, 1, NULL, 'DNI', '46627540', 'JULIO CESAR', 'CAHUATA', 'HUARANGA', 'PERUANA', '1990-11-25', '903365128', 'juliocahuata30@gmail.com', '', '0', NULL, '8', '89', '804', 'VALLE FELIPE PARDO 308 URB. PROGRESISTA', 'JENNY HUARANGA', '969342241', 'MADRE', NULL, NULL, NULL, NULL, 'H46627540', 'AIIA', 79, 0, 'fotos/conductores/conductor_67a01987996543.27602535.jpg', '$2y$12$liMHSUBW5jDjgDt0Wcguh.kkaMo21POu300pwv6iHah5qzAPR5LE.', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (25, 1, NULL, 'DNI', '74526461', 'VICTOR FROILAN', 'AYALA', 'PARI', 'PERUANA', '1997-10-07', '923342606', 'victorayala2018@gmail.com', '$2y$12$y4ZDI3FPXtJAPqZrDfIZsuvxQ72LxpUtHXTMu2BRW01H0LUjekocS', '0', 0, '8', '89', '804', 'CALLE PLATON 112 MANUEL PRADO ', 'YAHAIRA', '983858108', 'ESPOSA', NULL, NULL, NULL, NULL, 'H74526461', 'AIIB', 78, 0, 'fotos/conductores/conductor_67a01dcaac3227.29033126.jpg', 'fotos_perfil/perfil_74526461_1757383872.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (26, 1, NULL, 'DNI', '30962992', 'VICTOR MIGUEL', 'BERNAL', 'LLAMOCA', 'PERUANA', '1977-05-23', '906574471', 'vickober35@gmail.com', '$2y$12$cjTGUfhNDuOV3IqfI.Ib6eaBoeQy4RGBmMrHXcgr0/uoy.gkWYppa', '0', 0, '8', '89', '795', 'PPJJ ALTO LIBERTAD JR ICA N°105', 'BLANCA ', '906186103', 'ESPOSA', NULL, NULL, NULL, NULL, 'H30962992', 'AIIB', 77, 0, 'fotos/conductores/conductor_67a02074612155.34297817.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (27, 1, NULL, 'DNI', '72314107', 'JOHN RENATO', 'ARROE', 'CORNEJO', 'PERUANA', '1976-02-10', '959766018', 'jr.arroe@gmail.com', '$2y$12$H9ZFQe6vfaARvtRNYNGPte77l/q54L3oLhW9sADp3PCDf4wauX3SO', '0', 0, '8', '89', '799', 'URB. BARTOLOME HERRERA G 12 ', 'JOHN ARROE', '959766018', 'MISMO', NULL, NULL, NULL, NULL, 'Y29658364', 'AIIB', 76, 0, 'fotos/conductores/conductor_67a02340c9ce20.95618426.jpg', 'fotos_perfil/perfil_29658364_1757374758.png', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (28, 1, NULL, 'DNI', '40499792', 'VICTOR EVARISTO', 'CAJO', 'MAMANI', 'PERUANO', '1996-10-26', '960991738', 'victorcajo00@gmail.com', '$2y$12$c5rxcy20XgATTfiXZ52rxesQ7W85FSCHhWDGVoYe1TPcQSsuq3AX6', '0', 0, '8', '89', '802', 'urb alameda de salaverry Mz Q Lt 4', 'VICKY CHAMBI HOLGUIN ', '986148827', 'ESPOSA', NULL, NULL, NULL, NULL, 'H40499792', 'AIIB', 75, 0, 'fotos/conductores/conductor_67bca7196dd728.23988208.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (29, 1, NULL, 'DNI', '46959043', 'MISAEL GABRIEL', 'LOPEZ', 'CLAVIJO', 'PERUANA', '1992-11-27', '942037871', 'mglc926@gmail.com', '$2y$12$H9ZFQe6vfaARvtRNYNGPte77l/q54L3oLhW9sADp3PCDf4wauX3SO', '0', NULL, '8', '89', '804', 'urb. progresista calle juan del valle 102', 'mayky lopez', '960203743', 'hermano', NULL, NULL, NULL, NULL, 'H46959043', 'AIIB', 169, 0, 'fotos/conductores/conductor_67a0dd8c147121.02490610.jpeg', 'fotos_perfil/perfil_46959043_1756478599.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (30, 1, NULL, 'DNI', '73521909', 'RUDY WILBER', 'HUANCA', 'FLORES', 'PERUANA', '1993-07-28', '950985787', 'huancafloresrudywilber@gmail.com', '$2y$12$y49zWWRsU/D8UHZjv39Nfe8py6OUXGluvRgvk0hyuZ4KmlID8Ejca', '0', 0, '8', '89', '801', 'CALLE TUPAC AMARU 214 ', 'RUDY', '950985787', 'TITULAR', NULL, NULL, NULL, NULL, 'H73521909', 'AIIB', 63, 0, 'fotos/conductores/conductor_67a0ddce1c78e3.32556565.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (31, 1, NULL, 'DNI', '43352946', 'RAUL', 'MAMANI', 'MENDOZA', 'PERUANA', '1984-02-15', '981914506', 'mamanimendozaraul33@gmail.com', '', '0', NULL, '8', '89', '795', 'ASOC. CENTRO INDUSTRIAL LAS CANTERAS MZ. G LT. 7 E', 'ANA LUCIA MAMANI TRUJILLANO', '955538705', 'HIJA', NULL, NULL, NULL, NULL, 'H43352946', 'AIIB', 15, 0, 'fotos/conductores/conductor_67c8a507abdea9.70585831.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (32, 1, NULL, 'DNI', '73172044', 'DARWIN WILLIAM', 'CALCINA', 'CUNO', 'PERUANA', '1998-06-11', '982922764', 'darwincc58@gmail.com', '$2y$12$.oJ.oVVtp7cGfqPHs/jD9eDv2V2rMRwYTjXnXa0cjyMH.UW7leyTa', '0', NULL, '8', '89', '798', 'CALLE MONTEVIDEO 200 ', 'EGDILIA CUNO MONTALVO', '971918313', 'MADRE', NULL, NULL, NULL, NULL, 'H73172044', 'AIIB', 62, 0, 'fotos/conductores/conductor_690b7d63064251.38846760.jpeg', 'fotos_perfil/perfil_73172044_1762287414.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (33, 1, NULL, 'DNI', '71248649', 'CHRISTIAN RODRIGO', 'SOSA', 'SOTO', 'PERUANO', '1993-06-11', '959889449', 'christiansosa20@outlook.com', '', '0', NULL, '8', '89', '804', 'COOP. CRISTO REY MZ LT.7', 'ARMANDO PARI VILLAFUERTE', '982857212', 'AMIGO ', NULL, NULL, NULL, NULL, 'H71248649', 'AIIB', 74, 1, 'fotos/conductores/conductor_67c1d2852ab634.88067004.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (34, 1, NULL, 'DNI', '73355297', 'MAYKI', 'LOPEZ', 'CLAVIJO', 'PERUANA', '1995-03-30', '960203743', 'mlopezclavijo.unsa@gmail.com', '', '0', NULL, '8', '89', '804', 'urb. progresista calle juan del valle 102', 'MISAEL LOPEZ', '942037871', 'hermano', NULL, NULL, NULL, NULL, 'H73355297', 'AIIB', 168, 0, 'fotos/conductores/conductor_67a0e444275832.25619528.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (35, 1, NULL, 'DNI', '41038308', 'ARMANDO', 'QUISPE', 'CONDORI', 'PERUANA', '1980-11-27', '965119999', 'armando.271180.ayb@gmail.com', '', '0', NULL, '8', '89', '808', 'AA.VV. EL PARAISO DE CHUCA MZ. D7 LT. 17', 'BIANCA GUTIERREZ', '959842755', 'ESPOSA', NULL, NULL, NULL, NULL, 'H41038308', 'AIIIC', 14, 1, 'fotos/conductores/conductor_67a0e521d59662.78630850.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (36, 1, NULL, 'DNI', '45769798', 'JOSE MIGUEL', 'ROSAS', 'PONCE', 'PERUANA', '1989-08-09', '959751982', 'MVZJMROSASP@GMAIL.COM', '$2y$12$THNfEx0hZ.YEE.1b7WjDseuZ9GdAkSlvFGnBPrDeeYcpiI8/dEnfS', '0', NULL, '8', '89', '795', 'ASOC. PEDRO P.DIAZ MZ. 24 LT. 1', 'YONI', '973285335', 'CONVIVIENTE', NULL, NULL, NULL, NULL, 'H45769798', 'AIIB', 236, 0, 'fotos/conductores/conductor_67df7cd4527dd2.07820032.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (37, 1, NULL, 'DNI', '07851990', 'ALVARO HERNAN', 'ASCARZA', 'VIDAL', 'PERUANA', '1956-12-24', '925362954', 'AHAVASCARZA@gmail.com', '$2y$12$vu6ZPNDvdCqHcbBzuqGUW.i5Q2MLnQ5gQ0O7GCaKGPCyHKEQcvzfG', '0', 0, '8', '89', '804', 'LOS ALAMOS B-5 ', '963328197', 'ROSSANA', 'ESPOSA', NULL, NULL, NULL, NULL, 'Q07851990', 'AIIA', 61, 0, 'fotos/conductores/conductor_67c8a5287b4268.04434423.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (38, 1, NULL, 'DNI', '44145283', 'LUIS OSWALDO', 'MENENDEZ', 'TAPIA', 'PERUANO', '1987-02-24', '994799721', 'COMQUISTADORHIPHOP@HOTMAIL.COM', '', '0', NULL, 'notdepartamento', '89', '814', 'URB. CAMPIÑA III MZ L LT 5 EX CORDEA ', 'CARMEN BEATRIS TAPIA SEÑA', '994799691', 'FAMILIAR', NULL, NULL, NULL, NULL, 'H44145283', 'AIIB', 73, 0, 'fotos/conductores/conductor_67f03bcd5d5a98.81290864.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (39, 1, NULL, 'DNI', '72188784', 'YRVIN ANTHONY', 'VIZCARRA', 'SILES', 'PERUANA', '1996-12-18', '929402809', 'anthonyvizcarrasiles1996@gmail.com', '$2y$12$HPIcaXyjE//030hXgGHhve0al1ifZO2VYlo/NtEfZxo684YF3PAJ6', '0', 0, '8', '89', '804', 'AAHH. CERRO BUENA VISTA III MZ. V LT. 8 SEC. POZO NEGRO', 'CECILIA SOLEDAD', '928114814', 'PAREJA', NULL, NULL, NULL, NULL, 'H72188784', 'AIIB', 80, 0, 'fotos/conductores/conductor_67a0eab12b6e48.09910975.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (40, 1, NULL, 'DNI', '29400185', 'MARIA CAROLINA', 'LECAROS', 'ESTRADA', 'PERUANO', '1962-05-13', '943830333', 'carolecaros13@gmail.com', '', '0', 0, '8', '89', '799', 'PRESIDENCIAL VILLA MEDICA  TORRE 1, DPT 302', 'JOSE SALAS', '957888340', 'EX ESPOSO', NULL, NULL, NULL, NULL, 'h29400185', 'AIIB', 167, 0, 'fotos/conductores/conductor_67a0ea207f01c8.39371091.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (41, 1, NULL, 'DNI', '62593589', 'CARLOS ANTONIO', 'MANZANARES', 'MOLINA', 'PERUANA', '2001-12-27', '901729548', 'Carlos_271201@hotmail.com', '', '0', NULL, '8', '89', '794', 'VILLA CONTINENTALMZ.T LT. 11 COMITE 2', 'LUZ MARINA', '958662999', 'MAMÁ', NULL, NULL, NULL, NULL, 'H62593589', 'AIIB', 235, 1, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (42, 1, NULL, 'DNI', '71501135', 'JORGE EDUARDO', 'MACHACA', 'ARAPA', 'PERUANA', '1992-10-30', '979953330', 'acrota01@gmail.com', '', '0', NULL, '8', '89', '795', 'JOSE LUIS BUSTAMANTE Y RIVERO SECTOR 11', 'BETTY ACROTA', '988700807', 'ESPOSA', NULL, NULL, NULL, NULL, 'H71501135', 'AIIA', 199, 0, 'fotos/conductores/conductor_67e6cdd3bdd7e2.97120982.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (43, 1, NULL, 'DNI', '41629052', 'JUAN CARLOS', 'ZEGARRA', 'AGUARO', 'PERUANO', '1983-05-14', '990373413', 'ARAOS.22@HOTMAIL.COM', '', '0', NULL, '8', '89', '796', 'ASOC. DE VIVIENDA VIRGEN DE LOURDES MZ. B LT 9', 'CHIPANA FERNANDEZ MARIBEL ', '994995401', 'ESPOSA', NULL, NULL, NULL, NULL, 'H41629052', 'AIIB', 71, 1, 'fotos/conductores/conductor_67a0f0c043c8b1.92426451.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (44, 1, NULL, 'DNI', '48087514', 'ROBINSON BRIAN', 'RAMOS', 'APAZA', 'PERUANA', '1984-01-07', '992744592', 'brianramos27a@gmail.com', '$2y$12$crc1fBd/JiiJgex6NN/eTemvdJn2nZuYzPFh0EKEzo08t38meDUgq', '0', NULL, '8', '89', '795', 'sorana de los angeles mz 2 lote 5 zn semirural  pachacutec', 'ANA CECILIA PARAHUAYO MARROQUIN', '992743721', 'ESPOSA', NULL, NULL, NULL, NULL, 'H48087514', 'AIIB', 166, 0, 'fotos/conductores/conductor_67a0f19154bc57.85075218.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (45, 1, NULL, 'DNI', '80623394', 'WILLY DAVID', 'RAMOS', 'TANTALEAN', 'PERUANA', '1979-10-28', '979011063', 'CARLONCHO11236@gmail.com', '', '0', NULL, '8', '89', '814', 'HORACIO ZEVALLOS GAMEZ MZ 15 LT 1 SECTOR 16 ', 'MARIA ADELA', '945651598', 'MADRE', NULL, NULL, NULL, NULL, 'H80623394', 'AIIB', 60, 0, 'fotos/conductores/conductor_67c0bce1524c80.26738338.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (46, 1, NULL, 'DNI', '41424451', 'FREDY CARLOS', 'PERALTA', 'CCALLO', 'PERUANA', '2002-08-21', '946721794', 'Cazador_2011@hotmail.com', '', '0', NULL, 'notdepartamento', '89', '804', 'AV. LOS INCAS 240 CIUDAD BLANCA ', 'RAQUEL CHURATA', '931688969', 'ESPOSA', NULL, NULL, NULL, NULL, 'H41424451', 'AIIA', 136, 0, 'fotos/conductores/conductor_67e6c01ced9e87.15587012.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (47, 1, NULL, 'DNI', '75125448', 'ANTHONI JESUS', 'RAMOS', 'CABRERA', 'PERUANA', '2003-07-13', '986574461', 'anthoniramos13@gmail.com', '$2y$12$A1ZpNIt.obHgtXvH3mB6Ue1hqFakx/tXz6hgd/3xzyyDMDMXiG/5a', '0', NULL, '8', '89', '814', 'ASENT H. MANSION DE SOCABAYA E 9', 'SOFIA QUISPE YUCA', '949704769', 'PAREJA', NULL, NULL, NULL, NULL, 'H75125448', 'AIIB', 59, 0, 'fotos/conductores/conductor_67c1f015aac9e4.96474091.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (48, 1, NULL, 'DNI', '72233219', 'JHON YURI', 'CJURO', 'CONDORI', 'PERUANO', '2001-01-08', '906402632', 'Yurigorgoroth15@gmail.com', '', '0', NULL, '8', '89', '794', 'ASOC. ANDRES AVELINO CACERES MZ L LT 12', 'MARIA BELEN HUAYLLANI ', '948107003', 'CONVIENTE ', NULL, NULL, NULL, NULL, 'H72233219', 'AIIB', 69, 0, 'fotos/conductores/conductor_67a0fd027a8681.32417869.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (49, 1, NULL, 'DNI', '44802574', 'LUIS', 'ECHEVARRIA', 'GARCIA', 'PERUANA', '1997-11-15', '948632828', 'Lui.echevag@gmail.com', '$2y$12$ltAOKUaPiKkDF0jF3St0Ge07hF75ewJ/hLAIX8zL7FTMpMpilhps.', '0', NULL, '8', '89', '792', 'EL MIRADOR I ETAPA MZ H LT 12', 'LUIS', '948632828', 'TITULAR', NULL, NULL, NULL, NULL, 'H44802574', 'AIIIC', 165, 0, 'fotos/conductores/conductor_67a1011cde9c27.04925900.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (50, 1, NULL, 'DNI', '41856774', 'JOSE ANTONIO', 'HUAMANTUMA', 'HANCCO', 'PERUANA', '1983-02-25', '951473358', 'NOTIENE@gmail.com', '', '0', NULL, '8', '89', '799', 'FUNDA LA LLOSA S/N A. AVELINO CACERES', 'JOSE ANTONIO HUAMANTUMA', '951473358', 'TITULAR', NULL, NULL, NULL, NULL, 'H41856774', 'AIIB', 129, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (51, 1, NULL, 'DNI', '29223141', 'GUSTAVO ADOLFO', 'PACHECO', 'CHAVEZ', 'PERUANA', '1963-08-24', '974362541', 'gusi62@gmail.com', '', '0', NULL, '8', '89', '793', 'SAENZ PEÑA 206-208', 'JUANA YISELA', '969718394', 'ESPOSA', NULL, NULL, NULL, NULL, 'H29223141', 'AIIB', 58, 0, 'fotos/conductores/conductor_67e5af67419ff3.45766287.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (52, 1, NULL, 'Carnet', '005247955', 'MIGUEL EDUARDO', 'PEREZ', 'GONZALEZ', 'VENEZOLANO', '1987-07-17', '940280140', 'NOTIENE@GMAIL.COM', '', '0', NULL, '8', '89', '799', 'PSJ. LEONES DEL MISTI MZ D LT 1 CALLE JOSE GALVEZ N 112', 'DIANA FERNANDEZ', '941681237', 'ESPOSA', NULL, NULL, NULL, NULL, 'H005247955', 'AIIIC', 170, 1, 'fotos/conductores/conductor_67a10849943783.01798181.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (53, 1, NULL, 'DNI', '30586086', 'ENGELBERT THOMAS', 'VILLAFUERTE', 'MEDINA', 'PERUANA', '1974-07-30', '962691924', 'tvillafuerte2000@gmail.com', '', '0', NULL, '8', '89', '799', 'URB. VILLA JARIBU D-10', 'LEYDY VILLAFUERTE', '978373996', 'HERMANA', NULL, NULL, NULL, NULL, 'H30586086', 'AIIB', 127, 0, 'fotos/conductores/conductor_67a1084a975b23.89926050.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (54, 1, NULL, 'DNI', '73270954', 'MICHAEL ELFER', 'PERALTA', 'QUISPE', 'PERUANA', '1999-08-23', '925870230', 'maycol_peralta@outlook.com', '$2y$12$0SQ/hVI0RrixKq3eQQ6m6uoy6xqCqa.Tl0V6VBT7GZAhWPQngqtQG', '0', NULL, '8', '89', '809', 'AV. PROGRESO 412 INT. ', 'MICHAEL', '925870230', 'TITULAR', NULL, NULL, NULL, NULL, 'H73270954', 'AIIB', 57, 0, 'fotos/conductores/conductor_67c1e879cfd5c5.51283739.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (55, 1, NULL, 'DNI', '74125292', 'ANGEL ANDRE', 'MUÑOZ', 'MOTTA', 'PERUANA', '2000-06-01', '915150753', 'mangelandre@gmail.com', '', '', 0, '8', '89', '804', 'AV. JHON F. KENNEDY 2100C', 'GIORGIA FABIOLA MARIN PARICAHUA', '955760644', 'PAREJA', NULL, NULL, NULL, NULL, 'H74125292', 'AIIB', 56, 0, 'fotos/conductores/conductor_67a10bd41d1599.18580563.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (56, 1, NULL, 'Carnet', '04172952', 'GONZALO RAFAEL', 'ZABALA', 'VILLARROEL', 'VENEZOLANA', '1982-06-12', '919165847', 'gonzabalr@hotmail.com', '$2y$12$Cd9.wzJBZmXNWyIRsh6uL.bs7gfHfAM2ZziMM1miVe1VKnrVBDr7G', '0', 0, '8', '89', '792', 'AV. HUAYNA CAPAC N°406B', 'GONZALO ZABALA LEON', '933541083', 'PADRE', NULL, NULL, NULL, NULL, 'H004172952', 'AIIIA', 8, 0, 'fotos/conductores/conductor_67a10eb395c570.78625160.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (57, 1, NULL, 'DNI', '71074337', 'BRYAM JEAN PIER', 'MOSCOSO', 'FLORES', 'PERUANA', '1997-01-22', '916136221', 'bryamjempier3@gmail.com', '', '0', NULL, '8', '89', '802', 'calle bolognesi 118', 'ismael flores', '959285970', 'familiar', NULL, NULL, NULL, NULL, 'H71074337', 'AIIB', 164, 0, 'fotos/conductores/conductor_67a10ec5f38954.78742969.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (58, 1, NULL, 'DNI', '77427007', 'JULIAN ENRIQUE', 'CHOQUE', 'TACO', 'PERUANO', '2005-10-07', '916652997', 'FRANCO24200@HOTMAIL.COM', '', '0', NULL, '8', '89', '792', 'AV ESPAÑA 240-A', 'ROSARIO AMPARO ', '9596717070', 'ESPOSA', NULL, NULL, NULL, NULL, 'H77427007', 'AIIB', 68, 0, 'fotos/conductores/conductor_68928198bebc22.73166256.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (59, 1, NULL, 'DNI', '72704453', 'MANUEL IVAN', 'SALAS', 'YAULI', 'PERUANA', '1993-05-26', '971885896', 'msmsalas721@gmail.com', '', '0', NULL, '8', '89', '795', 'CAMPO VERDE MZ L LT. 12 DPTO 101 ', 'CLAUDIA MENENDEZ', '924190606', 'ESPOSA', NULL, NULL, NULL, NULL, 'H72704453', 'AIIB', 55, 0, NULL, 'fotos_perfil/perfil_72704453_1758216553.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (60, 1, NULL, 'DNI', '47134155', 'GUSTAVO JAVIER', 'CUBA', 'HUAMAN', 'PERUANA', '1991-07-10', '906530296', 'gcuba923@gmail.com', '', '0', 0, '8', '89', '799', 'EL PARAISO C-15', 'BRIGUIT DELGADO', '906530296', 'ESPOSA', NULL, NULL, NULL, NULL, 'H47134155', 'AIIB', 126, 0, 'fotos/conductores/conductor_67c1d393889f18.92399953.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (61, 1, NULL, 'DNI', '75570794', 'LUIS EDUARDO', 'BELTRAN', 'OLAYUNCA', 'PERUANA', '1998-10-17', '944395717', 'Notiene@gmail.com', '', '0', NULL, '8', '89', '804', 'jr del sol 203 campo marte', 'virgilio beltran', '940191550', 'padre', NULL, NULL, NULL, NULL, 'H75570794', 'AIIB', 163, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (62, 1, NULL, 'Carnet', '002228032', 'YONNY MANUEL ', 'CASTILLA', 'AREVALO', 'VENEZOLANO ', '1979-08-29', '932384826', 'YONNYCASTILLA24@GMAIL.COM', '', '0', NULL, '8', '89', '799', 'URB FECIA CALLE BRASIL #100', 'ALEJANDRA RAFFO ', '915128576', 'ESPOSA', NULL, NULL, NULL, NULL, 'H002228032', 'AIIA', 72, 1, 'fotos/conductores/conductor_67a12270a57a37.74917185.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (63, 1, NULL, 'DNI', '80624395', 'DEMBER', 'COAGUILA', 'ROMERO', 'PERUANA', '1979-07-14', '900039526', 'NOTIENE@gmail.com', '', '0', NULL, 'notdepartamento', '89', '808', 'CALLE TUPAC AMARU MZ. C LT. 5', 'JAMEL ANDRES', '984331182', 'HIJO', NULL, NULL, NULL, NULL, 'H80624395', 'AIIB', 122, 0, 'fotos/conductores/conductor_67fe9a40ce1ae5.29931507.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (64, 1, NULL, 'DNI', '29369389', 'FREDY ANTONIO', 'SALAS', 'ZEGARRA', 'PERUANA', '1961-04-16', '982700012', 'fredysalasz61@gmail.com', '', '0', NULL, '8', '89', '801', 'AMAZONAS 602', 'GABRIELA SALAS', '982700012', 'HIJA', NULL, NULL, NULL, NULL, 'H29369389', 'AIIB', 52, 0, 'fotos/conductores/conductor_67e6be6ac35d58.85053551.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (65, 1, NULL, 'DNI', '43505216', 'FRANKLIN MIGUEL', 'CONDORI', 'PACCO', 'PERUANA', '1986-02-18', '942268619', 'franklin.condori.pacco@gmail.com', '$2y$12$S8EnjfAj/2N3DL7Ohq42HuRTuuObcijGmbWC5/KLHoDxk06.n28.W', '0', 0, '8', '89', '795', 'jr piura 100 con restauración  semi rural pachacutec', 'elizabeth ', '944080532', 'esposa', NULL, NULL, NULL, NULL, 'H43505216', 'AIIB', 162, 0, 'fotos/conductores/conductor_67a124d256ab65.63235651.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (66, 1, NULL, 'DNI', '47059023', 'GIOMAR ANTONIO', 'VARGAS', 'HUACHANI', 'PERUANA', '1992-04-29', '952635016', 'giomar.vh@hotmail.com', '$2y$12$QqaJUA1.zozWP1Q0m4xtpOucrLhaacWT1x320Mo3tbDjZd32zVCvW', '0', NULL, 'notdepartamento', '89', '794', 'AH JOSE OLAYA ZONA B MZ. H LT. 6', 'MADELEYN LUQUE ZUÑIGA', '935088031', 'ESPOSA', NULL, NULL, NULL, NULL, 'H47059023', 'AIIB', 120, 0, 'fotos/conductores/conductor_67e5ae2c71b376.31701150.jpeg', 'fotos_perfil/perfil_47059023_1756766476.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (67, 1, NULL, 'DNI', '45537123', 'LUIS ADISON', 'SUCA', 'VALERIANO', 'PERUANO', '1987-11-02', '993892655', 'elinocentelav@hotmail.com', '', '0', 0, '8', '89', '799', 'MDO. NUEVO AMANECER FDO. EL SOLAR ', 'MARUJA VALERIANO LIMA', '990392456', 'MADRE', NULL, NULL, NULL, NULL, 'H45537123', 'AIIB', 67, 0, 'fotos/conductores/conductor_67e59f9ee60980.05383646.jpeg', 'fotos_perfil/perfil_45537123_1757780368.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (68, 1, NULL, 'DNI', '45508925', 'WILSON JOSE', 'ROSAS', 'PILCO', 'PERUANA', '1989-01-28', '949914114', 'Notiene@gmail.com', '', '0', NULL, 'notdepartamento', '89', '799', 'calle benigno ballon farfan 507 urnb. simon bolivar', 'jhon rosas', '958813582', 'hermano ', NULL, NULL, NULL, NULL, 'H45508925', 'AIIB', 161, 0, 'fotos/conductores/conductor_67a12c35a0d9d1.65751309.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (69, 1, NULL, 'DNI', '61327002', 'JANPIER JOSEMARIA', 'QUIJAHUAMAN', 'MAMANI', 'PERUANA', '2004-03-19', '951165477', 'jeanpierquijahuamanmamani@gmail.com', '$2y$12$z4cRJep9uadpHZTKfQFzreScmN2Jtgu6jfuaOHgSgCYkGESwJpaP2', '0', NULL, '8', '89', '809', 'PSJ. MOQUEGUA 102 INT HUARANGUILLO ', 'JANPIER', '951165477', 'TITULAR', NULL, NULL, NULL, NULL, 'H61327002', 'AIIB', 51, 0, 'fotos/conductores/conductor_67c20aa6c71808.40948125.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (70, 1, NULL, 'Carnet', '006411725', 'NAYCLE GIOVANNY', 'ROJAS', 'OROPEZA', 'VENEZOLANA', '1981-12-31', '990560139', 'rong3112@gmail.com', '', '0', NULL, '8', '89', '807', 'PSJ. 04 DE OCTUBRE 102 MZ. D LT. 16', 'VANESSA MAMANI', '970053234', 'NOVIA', NULL, NULL, NULL, NULL, 'H006411725', 'AIIIC', 117, 0, 'fotos/conductores/conductor_67b898d7ac35e1.09517896.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (71, 1, NULL, 'DNI', '75982935', 'JOHAN JOUSSEP', 'GALLEGOS', 'PINTO', 'PERUANA', '2009-03-16', '939724855', 'SABABRI12@gmail.com', '', '0', NULL, '8', '89', '792', 'CALLE AMAZONAS 401 - B ', 'DIANA TACO', '918556907', 'PAREJA', NULL, NULL, NULL, NULL, 'Q75982935', 'AIIB', 50, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (72, 1, NULL, 'DNI', '46260874', 'RODD HENDRYZ', 'COAQUIRA', 'MENDOZA', 'PERUANA', '1980-04-06', '974406331', 'hendryzmendoza@gmail.com', '', '0', NULL, '8', '89', '804', 'AV. ARGENTINA 309 APINA', 'LUDGARDA MENDOZA', '955055864', 'MADRE', NULL, NULL, NULL, NULL, 'H46260874', 'AIIIA', 115, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (73, 1, NULL, 'DNI', '42706440', 'LUIS ALBERTHO', 'LOAYZA', 'SERRUDO', 'PERUANO', '1984-10-29', '912018188', 'luisalbertholoayzaserrudo@gmail.com', '', '0', 0, '8', '89', '799', 'ASOC. LA ALBORADA MZ C LT 11', 'MELIZA MELENDEZ ALVAREZ ', '960668696', 'ESPOSA', NULL, NULL, NULL, NULL, 'H42706440', 'AIIIC', 66, 0, 'fotos/conductores/conductor_67b89848c0c926.90849096.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (74, 1, NULL, 'DNI', '71311166', 'ANYELO DANIEL', 'CASANI', 'APAZA', 'PERUANA', '1997-01-01', '986567747', 'casaniapazaa@gmail.com', '$2y$12$b7EOcnkC3GV8OInoNUqBxe2OwBcy8fo5xJiv1wHlEPg8TS/9J/5X2', '0', 0, '8', '89', '792', 'AAHH. JAVIER HERAUD. MZ U LT 1', 'MARISOL CHOQQUE', '965681543', 'ESPOSA', NULL, NULL, NULL, NULL, 'H71311166', 'AIIB', 160, 0, 'fotos/conductores/conductor_67a1325e13f466.76947974.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (75, 1, NULL, 'DNI', '41158738', 'EDWIN JESUS', 'SURCO', 'LLAMOCA', 'PERUANA', '1980-07-21', '907250690', 'edwin_surco@hotmail.com', '$2y$12$m.ue19/DRDm3yhr1nppH/e8dQWbtqQEDGfskbBp0o8VhGPe.4ODT.', '0', NULL, '8', '89', '795', 'URB LA LIBERTAD CALLE SOSA RUIZ NRO 424 ', 'MILER', '968613839', 'HERMANO', NULL, NULL, NULL, NULL, 'H41158738', 'AIIB', 49, 0, 'fotos/conductores/conductor_67a13433cd92f4.85361007.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (76, 1, NULL, 'DNI', '47430740', 'LUDVING DANIEL', 'HUACCOTO', 'GOIZUETA', 'PERUANO', '1992-10-11', '977434596', 'daronielhg@hotmail.com', '', '0', NULL, '8', '89', '815', 'PUEBLO J. SANTA RITA MZ. H LT. 6 ', 'SANCHEZ ROSAS GIOVANNA RUTH ', '912100178', 'ESPOSA', NULL, NULL, NULL, NULL, 'H47430740', 'AIIA', 64, 0, 'fotos/conductores/conductor_67c1d0c72a8384.88809165.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (77, 1, NULL, 'DNI', '72685770', 'BRANCO NELSON', 'SERGO', 'QQUECCAÑO', 'PERUANA', '1996-03-20', '968268886', 'brancosergo888@gmail.com', '', '0', NULL, '8', '89', '795', 'VILLA PARAISO MZ D4 LT 10 ', 'MARIO SERGO', '95903047', 'PADRE', NULL, NULL, NULL, NULL, 'H72685770', 'AIIB', 48, 0, 'fotos/conductores/conductor_67c8a3a3231307.89471929.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (78, 1, NULL, 'DNI', '74307492', 'ISMAEL JOSUE', 'FLORES', 'RODRIGUEZ', 'PERUANA', '1997-10-12', '959285970', 'Floresrodriguezjosue21@gmail.com', '', '0', NULL, '8', '89', '802', 'BOLOGNESI 418', 'ANGELA LASTARIA CASERES', '980757274', 'PAREJA', NULL, NULL, NULL, NULL, 'H74307492', 'AIIB', 159, 0, 'fotos/conductores/conductor_67a13812086dc4.73504615.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (79, 1, NULL, 'DNI', '41481176', 'JOSE ANTONIO', 'CACERES', 'MALDONADO', 'PERUANA', '1982-08-26', '958830588', 'joanca66@gmail.com', '', '0', NULL, '8', '89', '799', 'PASAJE MAQUEGUA POLAR 405 URB. MI PERU ', 'LOURDES VICTORIA ', '958419888', 'ESPOSA', NULL, NULL, NULL, NULL, 'H41481176', 'AIIIC', 105, 0, NULL, 'fotos_perfil/perfil_41481176_1759268631.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (80, 1, NULL, 'DNI', '47451776', 'BELISARIO VICTOR', 'YUCRA', 'VILCAHUAMAN', 'PERUANO', '1991-03-06', '979562213', 'AV 15 DE AGOSTO MZ 29 LT 12 PSJ INDEPENCIA ALTO SELVA ALEGRE ', '', '0', NULL, '8', '89', '792', 'AV 15 DE AGOSTO MZ 29 LT 12 PSJ INDEPENCIA ', 'EDY ', '931824011', 'HERMANO', NULL, NULL, NULL, NULL, 'H47451776', 'AIIB', 47, 0, 'fotos/conductores/conductor_67b8a0073817b1.48270053.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (81, 1, NULL, 'DNI', '46529719', 'ISACC GUILLERMO', 'TICONA', 'HUMPIRE', 'PERUANO', '1990-02-03', '953606107', 'guillermoticona65@gmail.com', '', '0', NULL, '8', '89', '792', 'COOP.LA ESTRELLA MZ. D LT.17', 'MARISOL BERENICE TORRE BLANCA FLORES', '969779016', 'CONVIVIENTE ', NULL, NULL, NULL, NULL, 'H46522719', 'AIIB', 46, 0, 'fotos/conductores/conductor_67c0b6c5c12e94.53162111.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (82, 1, NULL, 'DNI', '43373493', 'JUAN AGUSTO', 'SANJINES', 'HUALPA', 'PERUANA', '1965-04-02', '974521012', 'jsanjines49@gmail.com', '', '0', NULL, '8', '89', '804', 'las americas A-5 san salvador ', 'katherine guevara', '968224250', 'esposa', NULL, NULL, NULL, NULL, 'H43373493', 'AIIB', 158, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (83, 1, NULL, 'Carnet', '005261451', 'JEFERSON JOSE', 'CHOPITA', 'AREVALO', 'VENEZOLANA', '1995-08-11', '952365783', 'Chopitejeferson@gmail.com', '$2y$12$eDkQ0BFO8JrrfCNTNWlNGOWAH2vh.4Si686mRuKL7LrnCzKLLh5s.', '0', 0, '8', '89', '802', 'EL GOLFO 116 - MIRAFLORES', 'JEFERSON', '952365783', 'TITULAR', NULL, NULL, NULL, NULL, 'V-27.028.232', 'AIIB', 33, 0, 'fotos/conductores/conductor_67b89f99d992d0.47470728.jpg', 'fotos_perfil/perfil_005261451_1757426998.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (84, 1, NULL, 'DNI', '41226025', 'CARLOS VICTOR', 'PALOMINO', 'MUÑIZ', 'PERUANO', '1982-01-07', '950733731', 'MKR5M3@GMAIL.COM', '', '0', NULL, '8', '89', '793', 'CALLE ANGAMOS 125 MARIA ISABEL ', 'GISELLA PALOMINO MUÑIZ', '929432997', 'HERMANA ', NULL, NULL, NULL, NULL, 'H41226025', 'AIIA', 44, 0, 'fotos/conductores/conductor_67e6d74ea3e1f7.16902241.jpg', 'fotos_perfil/perfil_41226025_1757436656.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (85, 1, NULL, 'DNI', '76787498', 'DIEGO ABEL', 'CASTRO', 'FLORES', 'PERUANA', '1997-02-25', '971591460', 'diegocastroflores9725@gmail.com', '$2y$12$yk2wMwuLKbvtRHftqPMFxepL7Yf0L9wwh0oHGypsbO7GDUm7349g2', '0', NULL, '8', '89', '795', 'AAHH LAS LOMAS', 'DANIA GALVEZ', '921190124', 'PAREJA', NULL, NULL, NULL, NULL, 'H76787498', 'AIIB', 157, 0, 'fotos/conductores/conductor_67b89627ea61a9.65709625.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (86, 1, NULL, 'DNI', '43174792', 'JUSTO ANTONIO', 'CONCHA', 'QUISPE', 'PERUANA', '1985-07-12', '958317747', 'anton_rock1@hotmail.com', '', '0', NULL, '8', '89', '793', 'CALLE LOS NARANJOS 206 URB  OBANDO ', 'JUSTO ANTONIO', '958317747', 'TITULAR', NULL, NULL, NULL, NULL, 'H43174792', 'AIIB', 17, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (87, 1, NULL, 'DNI', '73028073', 'JUAN JOSE', 'GARCIA', 'CONDORI', 'PERUANA', '1992-05-31', '982930653', 'yuan.itprika3@gmail.com', '$2y$12$O4vSxo.ak2wzkTM1KvGaW.zPznuZ6R5OOXR96ZzrYvr3WgZATuu2K', '0', 0, '8', '89', '798', 'JR. ZARUMILLA ZONA A PUEBLO JOVEN SAN JUAN DE DIOS MZ. P LT. 21', 'SATURNINA CONDORI', '930954929', 'MAMA', NULL, NULL, NULL, NULL, 'H73028073', 'AIIB', 30, 0, 'fotos/conductores/conductor_67b895c44d0891.77376020.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (88, 1, NULL, 'DNI', '42259886', 'EDWIN EDUARDO', 'CONDORI', 'QUISPE', 'PERUANA', '1984-01-29', '983499689', 'eduardopoeta9@gmail.com', '$2y$12$H7px8sN8nHaSXFGEoCqOn.P7trvT4TfdKvznoY6V/ESdMtweh0OU6', '0', 0, '8', '89', '794', 'UPIS 19 DE ENERO MZ G LT 15 ', 'RUTH CCASA', '972115284', 'ESPOSA', NULL, NULL, NULL, NULL, 'Q42259886', 'AIIB', 13, 0, 'fotos/conductores/conductor_67b3db4609f041.25859704.jpg', 'fotos_perfil/perfil_42259886_1757364493.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (89, 1, NULL, 'DNI', '29560193', 'CARLOS ALBERTO', 'LINARES', 'GONZALES', 'PERUANO', '1973-01-03', '928350747', 'clinaresmm@gmail.com', '$2y$12$nTy27O4HW8VPPSgoJDy33uMtMtMwOLs5hm85TaEQKvQGm.TzMp/Fy', '0', NULL, '8', '89', '795', 'CALLE JORGE CHAVEZ 116 URB LA LIBERTAD', 'CARLOS ALBERTO LINARES GONZALES ', '928350747', 'TITULAR', NULL, NULL, NULL, NULL, 'H29560193', 'AIIB', 45, 0, 'fotos/conductores/conductor_67b8966bf2aeb4.61759788.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (90, 1, NULL, 'DNI', '45388630', 'ALFREDO CELSO', 'FERREL', 'LLANOS', 'PERUANA', '1988-10-20', '964203438', 'Calefferrel@gmail.com', '$2y$12$34FEND241Wyja3GNWtQeYeoyJamov6yxV4DUx/uKStkEOvX5aC7Mq', '0', 0, '8', '89', '792', 'pj. villa asunción 2da etapa mz 0 lt6', 'gabriela quispe', ' 960762190', 'esposa', NULL, NULL, NULL, NULL, 'H45388630', 'AIIB', 156, 0, 'fotos/conductores/conductor_67cc6452a45a92.32269891.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (91, 1, NULL, 'DNI', '46081194', 'DIEGO ARMANDO', 'QUISPE', 'CHARRES', 'PERUANA', '1985-12-15', '921905008', 'quisped192@gmail.com', '$2y$12$RMXrBzPelWNuzG6ayd0KLObZEIRkPwlddJ13Vw9W.HcLgtQVr.LQi', '0', NULL, '8', '89', '792', 'ASOC. SAN LUIS GONZAGA ZONA A Q-15', 'ROSIBEL RANILLA', '907215587', 'PAREJA', NULL, NULL, NULL, NULL, 'H46081194', 'AIIIC', 29, 0, 'fotos/conductores/conductor_67c20d27793978.05299195.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (92, 1, NULL, 'DNI', '29734423', 'ROLANDO HILARIO', 'HUAMAN', 'ALVAREZ', 'PERUANA', '1977-08-12', '941447155', 'rolandohilariohuamanalvarez@gmail.com', '', '0', 0, '8', '89', '804', 'ASOC. DE VIVIENDA STA ROSA T 7 ', 'ROLANDO HILARIO', '941447155', 'TITULAR', NULL, NULL, NULL, NULL, 'H29734423', 'AIIB', 12, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (93, 1, NULL, 'DNI', '44773880', 'RENE JOSE', 'CALLA', 'CHAMBI', 'PERUANO', '1988-01-08', '943408434', 'URB. ISRAEL MZ Z  LT J COM. S PAUCARPATA', '$2y$12$G9dVj6j7jG7IVt0O2elYe.Y.VK2LZNDuQOkNsuqXWJoLDSgzGpcpu', '0', NULL, '8', '89', '804', 'URB ISRAEL MZ Z LOTE J COM. S ', 'MAGALY MILAGROS PASTOR FIGUEROA ', '957566006', 'ESPOSA', NULL, NULL, NULL, NULL, 'H44773880', 'AIIB', 41, 0, 'fotos/conductores/conductor_67bca6d229a1c4.02532041.jpg', 'fotos_perfil/perfil_44773880_1757346457.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (94, 1, NULL, 'DNI', '70291105', 'JUAN LINO', 'QUISPE', 'APAZA', 'PERUANA', '1990-06-12', '967216825', 'quispe112@gmail.com', '$2y$12$PrfOT3b7omjbVZ.9telrEOnO.ZyxhbfU8Zm884d.11C1M04kv6ICC', '0', NULL, '8', '89', '803', 'calle los rosales lt f23 programa municipal de vivienda pampa santa ana ', 'JUAN LINO', '967216825', 'TITULAR', NULL, NULL, NULL, NULL, 'H70291105', 'AIIB', 155, 0, 'fotos/conductores/conductor_680fb83177fa52.79918147.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (95, 1, NULL, 'DNI', '72439417', 'HEBERT ALEXIS', 'MACEDO', 'CONDORI', 'PERUANA', '1993-09-15', '913028150', 'hmc159@gmail.com', '', '0', NULL, '8', '89', '798', 'URB. SAN JUAN DE DIOS CALLE JERUSALEN MZ. V LT 21 ', 'AMPARO RODRIGUEZ', '997702142', 'CONVIVIENTE', NULL, NULL, NULL, NULL, 'H72439417', 'AIIB', 9, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (96, 1, NULL, 'Carnet', '005212170', 'KRISTOPHER DANIEL', 'SALGADO', 'GONZALES', 'VENEZOLANA', '1991-12-28', '982907263', 'kristophersalgado@gmail.com', '$2y$12$fA3b20ZllW7NlO5TEPHfMuMRnqz36Bex9I7qM6PM5Drgy2LV8zpru', '0', 0, '8', '89', '802', 'EL GOLFO 116', 'BRIGITTE', '0584241254109', 'MADRE', NULL, NULL, NULL, NULL, 'V20114258', 'AIIIB', 28, 0, 'fotos/conductores/conductor_67b89da2e4e4f4.14489023.jpg', 'fotos_perfil/perfil_005212170_1760558768.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (97, 1, NULL, 'DNI', '42013880', 'JUAN BRAULIO', 'CHAIÑA', 'COLQUEHUANCA', 'PERUANO', '1983-07-24', '924203635', 'jchaina77@gmail.com', '$2y$12$eTPlKSrQzcA.qq6DrA9ON.f0Qj5iWbl8JV.Akxv62GKl9yRzerQLm', '0', NULL, '8', '89', '801', 'CALLE CALVARIO N 204 ', 'JUAN CHAIÑA', '924203635', 'TITULAR ', NULL, NULL, NULL, NULL, 'H4213880', 'AIIA', 40, 0, 'fotos/conductores/conductor_67c1d569611d20.89206382.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (98, 1, NULL, 'Carnet', '005756263', 'DANIEL JOSE', 'BELLO', 'YENDI', 'VENEZOLANA', '1993-03-06', '949599608', 'danieljby@gmail.com', '$2y$12$LP8nFM9gxOLaVZuH4d8Q5Ozm..1pZI35XdusUSbHamvx3nP6ilk32', '0', 0, '8', '89', '804', 'CALLE 17 DE DICIEMBRE 203 PJ CIUDAD BLANCA ', 'JOHANA TARAZONA', '949133317', 'ESPOSA', NULL, NULL, NULL, NULL, 'H005756263', 'AIIIC', 7, 0, 'fotos/conductores/conductor_67b3d9aaea4e49.59279787.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (99, 1, NULL, 'DNI', '44744230', 'GERSON HERNAN', 'CONDORI', 'TANTALEAN', 'PERUANA', '1987-11-29', '950663654', 'stormmagenw@gmail.com', '$2y$12$Bd8qiQ9JbbB77WFWdvrUm.c8oXQQoHppwp1UQYfJoRuWdkSfi9I1i', '0', 0, '8', '89', '798', 'MARISCAL NIETO F-7 URB. VILLA SEVILLA', 'SONIA AMBROCIO ', '971278641', 'ESPOSA', NULL, NULL, NULL, NULL, 'H44744230', 'AIIB', 27, 0, 'fotos/conductores/conductor_67c1d47a1e9a17.77356525.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (100, 1, NULL, 'DNI', '72189395', 'DIEGO', 'SOTO', 'DUEÑAS', 'PERUANA', '1996-08-06', '932802403', 'andarielsoto7@gmail.com', '$2y$12$oRn9tAibGCmkm.xx5.YV2O2biXZ1U18ndPJBzGuZn6HI5pnKyb0LS', '0', NULL, '8', '89', '804', 'URB. ALEJANDRO VON HUMBOLT D-8 ', 'KARINA SOTO', '914962348', 'HERMANA', NULL, NULL, NULL, NULL, 'H7218935', 'AIIB', 6, 0, 'fotos/conductores/conductor_67b3d96ebacf30.24087480.jpg', 'fotos_perfil/perfil_72189395_1757208757.png', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (101, 1, NULL, 'DNI', '41346775', 'JESUS RICHARD', 'CAJO', 'MAMANI', 'peruana', '1961-04-14', '921563306', 'Notiene@gmail.com', '$2y$12$92YUJixaNdGRN0myeuJDDOyFzYtmuJlvaQ9IwCxRP081Abz7IUCVW', '0', NULL, '8', '89', '792', 'urb.juan velazco alvarado zn b mz m lt 11', 'victor cajo', '960991738', 'hermano', NULL, NULL, NULL, NULL, 'k41346775', 'AIIB', 154, 0, 'fotos/conductores/conductor_67e6d6e3b659c9.20317194.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (102, 1, NULL, 'DNI', '70515675', 'REYNALDO', 'AÑARI', 'MENDOZA', 'PERUANO', '1992-06-06', '901658240', 'ranari@unsa.edu.pe', '', '0', 0, '8', '89', '802', 'TENIENTE RODRIGUEZ ', 'CALIA ALVAREZ ', '958140453', 'PAREJA', NULL, NULL, NULL, NULL, 'H70515675', 'AIIA', 39, 0, 'fotos/conductores/conductor_67c1d550e014f1.61768742.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (103, 1, NULL, 'DNI', '44279037', 'JOSE LUIS', 'FLORES', 'TISNADO', 'PERUANA', '1987-05-16', '933573094', 'Florestisnadojoseluis1987@gmail.com ', '$2y$12$.jHR0ud/v4980HVjEXwv7OM8YKEFUF1oN4VjWNcJJU7PZKXTmXCr6', '0', NULL, '8', '89', '818', 'AV.BRASIL 210 PAMPA DE CAMARONES', 'JOSE DOMINGO', '054- 774924', 'PADRE', NULL, NULL, NULL, NULL, 'H44279037', 'AIIIC', 233, 0, 'fotos/conductores/conductor_67bca3f276cd60.87721371.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (104, 1, NULL, 'DNI', '46254272', 'LEONARDO CARLOS', 'CHOQUEHUANCA', 'RIVERA', 'PERUANA', '1989-11-04', '974273676', 'LEOCARLOS416@gmail.com', '', '0', NULL, '8', '89', '794', 'CALLE ALFONSO UGARTE 306 BUENOS AIRES MZ I LT 12', 'ERNESTO ', '929720252', 'PADRE', NULL, NULL, NULL, NULL, 'H46254272', 'AIIB', 4, 0, 'fotos/conductores/conductor_67b3d7fc811ea2.85858721.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (105, 1, NULL, 'DNI', '75745908', 'DARWIN EDWAR', 'BENAVENTE', 'SOLORZANO', 'PERUANA', '1997-10-25', '965680646', 'revo_965@homail.com', '$2y$12$PKG7CTWFR7YrlVPmqBme/.oet0RbBTJOU7BRSu4Gj9pdYyy6O6SAS', '0', 0, '8', '89', '804', 'PSJ. ALAMEDA CHORIILLOS A-4 ', 'ANA SOLORZANO', '958755775', 'MADRE', NULL, NULL, NULL, NULL, 'H75745908', 'AIIB', 25, 0, 'fotos/conductores/conductor_67c0b11ca43f00.21365488.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (106, 1, NULL, 'DNI', '75822613', 'GUSTAVO EDUARDO', 'CONDORI', 'CALCINA', 'PERUANA', '1994-09-29', '937533900', 'gustavosarmientocondori@gmail.com', '$2y$12$YKatc2oaSJfi31ggzhDLa.MJJ54SERYqIo5JuoVZqsqpuD8wUkpa6', '0', NULL, '8', '89', '814', 'calle arias araguez psj las lomas zn b  bellapampa mz e lt41 ', 'magaly apaza ', '936026221', 'esposa', NULL, NULL, NULL, NULL, 'H75822613', 'AIIIC', 153, 0, 'fotos/conductores/conductor_67e6c07cb719e7.49682676.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (107, 1, NULL, 'DNI', '70379367', 'LUIS FERNANDO', 'CUTIPA', 'MAMANI', 'PERUANA', '1996-11-09', '970297099', 'luisfernandocutipamamani8@gmail.com', '$2y$12$d2E0tuBwXMSJqnhczCbNR.k3wA81RoAItug8YmgqZx273UjbnLR7a', '0', NULL, 'notdepartamento', '89', '794', 'PPJJ BUENOS AIRES CALLE ALFONSO UGARTE # 306 COM 15 CAYMA ', 'LIZBETH CHOQUEHUANCA RIVERA ', '902763787', 'ESPOSA', NULL, NULL, NULL, NULL, 'H70379367', 'AIIB', 38, 0, 'fotos/conductores/conductor_67e5b02d6c3543.63855778.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (108, 1, NULL, 'DNI', '61174616', 'ROBERTH', 'PUMACALLAHUI', 'NINAQUISPE', 'PERUANA', '2005-06-12', '939026376', 'ROBENIN66@gmail.com', '$2y$12$v30Dk1YquOCCSjx1YPch8ORikKFSu81Dk9kxcFJbpfdpifnFcPMY6', '0', 0, '8', '89', '794', 'ASENT. VIRGEN DE CHAPI MZ. F LT. 10 ', 'MARIA P.', '942802934', 'HERMANA', NULL, NULL, NULL, NULL, 'H61174616', 'AIIB', 3, 0, 'fotos/conductores/conductor_67bca44e01daa7.12516649.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (109, 1, NULL, 'DNI', '44879104', 'GLEENT FIDEL', 'PIZARRO', 'FLORES', 'PERUANA', '1987-07-06', '982934321', 'melliso17@hotmail.com', '$2y$12$xXdZmyX9fl9ZSykqwQ6kaebfsOYA131diokyW07QiQ8gCXDxrBBiS', '0', NULL, '8', '89', '794', 'DEAN VALDIVIA (ENACE) MZ M LT 5 SECTOR 8', 'GLEENT FIDEL PIZARRO FLORES ', '982934321', 'TITULAR', NULL, NULL, NULL, NULL, 'H44879104', 'AIIB', 37, 0, 'fotos/conductores/conductor_67b7874860e0e8.17692974.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (110, 1, NULL, 'DNI', '29568592', 'GRUBER FELIX', 'CHARCA', 'VERA', 'PERUANA', '1972-07-06', '953849233', 'gruberfelix@hotmail.com', '$2y$12$vROGApi3zNj/DeMqShlvk.Sk9pXCGmJnF3gbEQx3s.4vMA.UUfIgO', '0', 0, '8', '89', '801', 'PARQUE BOLIVAR 110 ALTO SAN MARTIN', 'BRYAN GRUBER', '926610537', 'HIJO', NULL, NULL, NULL, NULL, 'H29568592', 'AIIA', 23, 0, 'fotos/conductores/conductor_67b897198e4400.25064536.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (111, 1, NULL, 'DNI', '45508912', 'MIGUEL ANGEL', 'BOLAÑOS', 'LIZARRAGA', 'PERUANA', '1988-12-20', '902894375', 'miguelbl2019@gmail.com', '$2y$12$iE3NZ6e1xhH9LWOTASRoxOiZ/23fVeuTUeM5cCXZ8pERUnyH48g8a', '0', NULL, '8', '89', '799', 'CALLE  AYARSA 313 URB CERRO DE JULY', 'ANGEL BOLAÑOS', '902894375', 'HERMANO', NULL, NULL, NULL, NULL, '45508912', 'AIIB', 151, 0, 'fotos/conductores/conductor_67e6c63146ff34.89541924.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (112, 1, NULL, 'DNI', '45663920', 'SAN VALENTIN', 'HUAMAN', 'CHAMBE', 'PERUANA', '1984-02-24', '929866300', 'hchsvalentin@hotmail.com', '$2y$12$jEBuvTTzUA0y935xfAqJPeqO3tS/qvaitaZlBl6yuE8mtoStUzQHK', '0', NULL, '8', '89', '795', 'ASOC. EL EDEN B-3  ', 'SAN VALENTIN', '929866300', 'TITULAR', NULL, NULL, NULL, NULL, 'H45663920', 'AIIB', 18, 0, 'fotos/conductores/conductor_67b3dea897ea69.05394449.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (113, 1, NULL, 'DNI', '71077371', 'LELIS IRIS', 'CHURO', 'VEGA', 'PERUANA', '1991-04-28', '940298501', 'churovegalelis@gmail.com', '', '0', 0, '8', '89', '795', 'ASC. SOR ANA DE LOS ANGELES Y MONTEAGUDO ZONA IV MZ. H LT 10', 'GREGORIA VEGA', '981613011', 'MAMA', NULL, NULL, NULL, NULL, 'H71077371', 'AIIA', 152, 0, 'fotos/conductores/conductor_67c1d45f529753.05003692.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (114, 1, NULL, 'DNI', '45634940', 'HAMMERLY YONATHAN', 'GUEVARA', 'AVENDAÑO', 'PERUANA', '1988-02-21', '940849853', 'yonahammerly@gmail.com', '$2y$12$ORbkMAuPPwLMUhVvo.5BB.ti0am48wySYajbMvgCFg47dbVmBzmEm', '0', NULL, '8', '89', '795', 'CALLE LUNA ZN A GRUPO 1 MZ A LT 3D SEMI RURAL PACHCACUTEC ', 'NATALY LAMAZA BELIZARIA', '991438519', 'ESPOSA', NULL, NULL, NULL, NULL, 'H45634940', 'AIIB', 34, 0, 'fotos/conductores/conductor_67c1d515201fe2.37301630.jpg', 'fotos_perfil/perfil_45634940_1757349021.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (115, 1, NULL, 'DNI', '78287248', 'LUIS ANGEL', 'VELASQUEZ', 'MAMANI', 'PERUANA', '2005-08-02', '957175905', 'luisanhel85@gmail.com', '$2y$12$oVFrVwOTncP7LwaF8Z/qwekD1AYbH1wJKpDrCJU1guTWwWA7R2OVa', '0', NULL, '8', '89', '795', 'PSJ. PRIMAVERA 140 BR ', 'EDUGINA MAMANI', '938364242', 'MADRE', NULL, NULL, NULL, NULL, 'U78287248', 'AIIB', 22, 0, 'fotos/conductores/conductor_67b89865a58338.43123500.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (116, 1, NULL, 'DNI', '44239163', 'ALEJANDRO HERNAN', 'VERA', 'CAHUANA', 'PERUANA', '1985-08-31', '982941320', 'VERALANZ78@gmail.com', '$2y$12$Laue9jYLZ3y32rToEeEX/O173JPthMtrZMniohlBW/x2hgJbOGbNW', '0', 0, '8', '89', '818', 'JIRON HUANCAVELICA 210 PAMPA DE CAMARONES ', 'LIDIA INCA ', '941245274', 'ESPOSA', NULL, NULL, NULL, NULL, 'K44239163', 'AIIB', 21, 0, 'fotos/conductores/conductor_67a26c18d1aac0.88593681.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (117, 1, NULL, 'DNI', '47568511', 'URIEL', 'QUISPE', 'PACSI', 'PERUANA', '1992-09-21', '957830377', 'Uriel_gojqn_93@hotmail.com', '$2y$12$NWk.MkZ3G1boaqU.YLehleJytwm45IOY6bvcwIu4yE2TEcAfIv2Kq', '0', NULL, 'notdepartamento', '89', '795', 'ASOC. JOSE LUIS BUSTAMANTE Y R. SECTOR IV MZ 9D LT 17 ', 'ESTHER PACSI', '956014225', 'MADRE', NULL, NULL, NULL, NULL, 'H47568511', 'AIIIC', 19, 0, 'fotos/conductores/conductor_67fea77565ccb6.88811297.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (118, 1, NULL, 'DNI', '43704885', 'CARLOS RAUL', 'TAPIA', 'CONTRERAS', 'PERUANA', '1986-08-27', '953167527', 'Ing.cartacon2@gmail.com', '$2y$12$CIKBZ6in97YZAYWZyTvGHOX9z4T3I37ACIw9TtKPPySE/AG4oWPpW', '0', NULL, '8', '89', '792', 'ASOCIACION CRUCE CHILINA MZ. I LT. 29', 'FIORELLA CHAMPI ', '951534161', 'PAREJA', NULL, NULL, NULL, NULL, 'H43704885', 'AIIB', 150, 1, 'fotos/conductores/conductor_67a274487bc2a8.74380498.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (119, 1, NULL, 'DNI', '40777109', 'RODOLFO JAMES', 'PALACIOS', 'SOTO', 'PERUANA', '1980-03-26', '953441545', 'James260380@hotmail.com', '$2y$12$ykiJ/.z88imyQBu95GEAQuWzV60g1pp9eC50qzGVaWa4wJVfwVJtS', '0', NULL, '8', '89', '794', 'URB. DEAN VALDIVIA Q8-5', 'MANUELA MERMA', '937742287', 'ESPOSA', NULL, NULL, NULL, NULL, 'H40777109', 'AIIIC', 148, 0, 'fotos/conductores/conductor_67c1d4381d6e09.45977018.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (120, 1, NULL, 'DNI', '46403908', 'JORGE LUIS', 'QUIÑONEZ', 'NINA', 'PERUANA', '1991-05-31', '926948806', 'Jorgequinonez3190@gmail.com', '', '0', NULL, '8', '89', '814', 'URB. DEAN VALDIVIA Q8-5 ', 'MANUELA MERMA', '937742287', 'ESPOSA', NULL, NULL, NULL, NULL, 'H46403908', 'AIIB', 149, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (121, 1, NULL, 'DNI', '48228242', 'JUAN GABRIEL', 'SANTAMARIA', 'CRISOSTOMO', 'PERUANA', '1994-05-06', '925554440', 'Santamariacrisostomogabriel@gmail.com', '', '0', NULL, '8', '89', '796', 'PUEBLO JOVEN LAHUAYA 1 Y 2', 'ANNA QUISPE ', '928493160', 'NOVIA', NULL, NULL, NULL, NULL, 'H48228242', 'AIIB', 147, 0, 'fotos/conductores/conductor_67a281f74fb6c6.58002979.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (122, 1, NULL, 'DNI', '72152666', 'HENRY OMAR', 'LAURA', 'MOROCCO', 'PERUANA', '1993-12-10', '933037080', 'henrylauramorocco@gmail.com', '$2y$12$klgusVSz5duxSlu623cwNOSP89t/qubTvmX9HrqFl0oJsxQoY77i6', '0', 0, '8', '89', '802', 'CALLE INDEPENDENCIA 302 - COOPERATIVA 14', 'JOYSIE RIVERA ', '950151265', 'ESPOSA', NULL, NULL, NULL, NULL, 'H72152666', 'AIIB', 145, 0, 'fotos/conductores/conductor_67a28724b467c0.12649470.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (123, 1, NULL, 'Carnet', '006389521', 'YUVAIN ENRIQUE', 'NATERA', 'HERRADE', 'VENEZOLANA', '1988-01-25', '925832390', 'yuvainnatera2501@gmail.com', '', '0', NULL, '8', '89', '814', 'CALLE TARATA PASJ ROMMEL 109 - SAN MARTIN DE SOCABAYA', 'YUVANI NATERA', '965443947', 'HERMANA', NULL, NULL, NULL, NULL, 'V-18.622.994', 'AIIB', 146, 0, 'fotos/conductores/conductor_67e6bf0f79bfc0.18822317.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (124, 1, NULL, 'DNI', '73060440', 'NELSON CESAR AUGUSTO', 'USCCA', 'RAMOS', 'PERUANA', '1992-10-30', '939580414', 'ncavr@hotmail.com', '', '0', NULL, '8', '89', '798', 'NICARAGUA 101 B ', 'ZAIDA BERNEDO', '938598650', 'ESPOSA', NULL, NULL, NULL, NULL, 'H73060440', 'AIIB', 144, 0, 'fotos/conductores/conductor_67e6bdd7ba7ef4.75375050.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (125, 1, NULL, 'Carnet', '006800714', 'ANDRY JOSE', 'BOSCAN', 'NAVARRO', 'VENEZOLANA', '1986-12-02', '966712137', 'andryboscan0212@gmail.com', '', '0', NULL, '8', '89', '799', 'URB. PEDRO DIAZ CANSECO MZ R LT 21 ', 'ALWIN BOSCAN', '955356817', 'HERMANO', NULL, NULL, NULL, NULL, 'H006800714', 'AIIB', 143, 0, 'fotos/conductores/conductor_67cc6527293f68.04482761.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (126, 1, NULL, 'DNI', '29631159', 'EDILBERTO OBDULIO', 'GUTIERREZ', 'GONZALES', 'PERUANA', '1974-05-04', '962916740', 'gcg.gutierrez@gmail.com', '', '0', NULL, '8', '89', '809', 'RICARDO PALMA 105 ', 'PATRICIA ZEA ', '945029880', 'ESPOSA', NULL, NULL, NULL, NULL, 'H29631159', 'AIIIC', 141, 0, 'fotos/conductores/conductor_67c1d4113bd527.94991274.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (127, 1, NULL, 'DNI', '74227494', 'ELISBAN', 'VILCA', 'CUSICUNA', 'PERUANA', '2021-07-17', '930416485', 'elisbanvilca9@gmail.com', '', '0', NULL, '8', '89', '814', 'HORACIO ZEBALLOS VII ZN D MZ 14 LT 55', 'DIANA GUTIERREZ', '952690876', 'PAREJA', NULL, NULL, NULL, NULL, 'H74227494', 'AIIB', 140, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (128, 1, NULL, 'DNI', '73247581', 'SIXTO ALEXANDER', 'PLATERO', 'CONDORI', 'PERUANA', '1993-10-24', '951391083', 'lmeliodasl@gmail.com', '', '0', NULL, '8', '89', '796', 'AV. AREQUIPA S/N ', 'NORMA CONDORI', '975413696', 'MAMA', NULL, NULL, NULL, NULL, 'H73247581', 'AIIB', 139, 1, 'fotos/conductores/conductor_67bca6fea7a245.08369690.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (129, 1, NULL, 'DNI', '47497306', 'JHON WILLIAM', 'ROSAS', 'PILCO', 'PERUANA', '1992-08-07', '958813582', '', '', '0', NULL, '8', '89', '793', 'CALLE SIGLO XX NRO 206', 'YULIETH DIAZ', '947168739', 'PAREJA', NULL, NULL, NULL, NULL, 'H47497306', 'AIIB', 138, 1, 'fotos/conductores/conductor_67e6c12ed95893.40486004.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (130, 1, NULL, 'DNI', '45687490', 'CESAR RAUL', 'CORAHUA', 'YANA', 'PERUANA', '1988-12-20', '982072266', 'csrlcy12@gmail.com', '$2y$12$Af9hlyCpRE/p1coqszdE6eLia.9SoH7QNbFEzQbdnolWPsxjD9IBW', '0', NULL, 'notdepartamento', '89', '794', 'ASC. VILLA CONTINENTAL ZON. C COM 1 MZ. K LT 5 ', 'MARIANO CORAHUA', '974237810', 'PAPÁ', NULL, NULL, NULL, NULL, 'H45687490', 'AIIA', 137, 0, 'fotos/conductores/conductor_67db03d3e84a45.47472628.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (131, 1, NULL, 'DNI', '44988194', 'ALVARO YBRIN', 'ROJAS', 'HUARCA', 'PERUANA', '1988-04-02', '973107335', 'Alvaro_ro16@hotmail.com', '$2y$12$ayBzJRxuS2f3Qk2u8cmaxeClQTiziOvYEN1D7ycJwT7Xk1ueoVOdW', '0', NULL, '8', '89', '794', 'AV. AREQUIPA B-11', 'TERESA JARA', '940972844', 'ESPOSA', NULL, NULL, NULL, NULL, 'H44988194', 'AIIIC', 135, 0, 'fotos/conductores/conductor_67c1d3f101a2c1.32919884.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (132, 1, NULL, 'DNI', '78011745', 'RODRIGO JOSE', 'ROSAS', 'PILCO', 'PERUANA', '2000-06-28', '960170753', 'RODRIGOROSAS14.RR@GMAIL.COM', '', '0', NULL, '8', '89', '799', 'CALLE BENIGNO BALLON FARFAN 507 URB. SIMON BOLIVAR', 'WILSON ROSAS', '959348822', 'HERMANO', NULL, NULL, NULL, NULL, 'H78011745', 'AIIB', 134, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (133, 1, NULL, 'DNI', '42465062', 'RICARDO ORLANDINI', 'ALTAMIRANO', 'QUISPE', 'PERUANA', '1980-11-27', '985140084', 'resilientesiempre40@gmail.com', '', '0', NULL, 'notdepartamento', '89', '804', 'CALLE CHE GUEVARA MIGUEL GRAU ZN D MZ 1 LT 27 ', 'JUSTA COAGUILA', '958705607', 'PAREJA', NULL, NULL, NULL, NULL, 'E42465062', 'AIIB', 133, 0, 'fotos/conductores/conductor_67e6bce58122d5.86397799.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (134, 1, NULL, 'DNI', '71860171', 'YUHLINIO', 'VILLANUEVA', 'DELGADO', 'PERUANA', '1996-11-22', '980237191', 'yulihniovillanueva@gmail.com', '$2y$12$GDsvNHsb13O9bh0P2LtSWOCLrhcxEk0gkfuW5UUln1wHLWGmBDhbm', '0', 0, 'notdepartamento', '89', '804', 'JOSE CARLOS MARIATEGUI 206 ', 'ERICKA DELGADO ', '957751650', 'MADRE', NULL, NULL, NULL, NULL, 'H71860171', 'AIIB', 132, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (135, 1, NULL, 'DNI', '80317139', 'ALGER REYNADO', 'GOMEZ', 'VELASQUEZ', 'PERUANA', '1977-08-22', '940280210', 'algergomezvelasquez@gmail.com', '', '0', 0, '8', '89', '802', 'ASOC. DE VIV. TALLER GRANDA LOS GIRASOLES ZN A MZ K LT 6', 'MAGDA APAZA', '989331020', 'ESPOSA', NULL, NULL, NULL, NULL, 'H80317139', 'AIIB', 131, 0, 'fotos/conductores/conductor_67c1d3c5e292f3.85520305.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (136, 1, NULL, 'DNI', '47899624', 'HENRY', 'CHOQUE', 'CALSIN', 'PERUANA', '1993-07-25', '928292582', 'th47899624@gmail.com', '$2y$12$GyrLd93S3IDuOriNpRDiEuR0hO8PEhDNYEtXx7/rQcMrP4YsJMtKm', '0', NULL, '8', '89', '799', 'PSJ. SAN AGUSTIN ASENT. H. MALECON BUENA VISTA MZ.  C LT 6', 'JOSE LUIS CHOQUE', '931422273', 'HERMANO', NULL, NULL, NULL, NULL, 'H47899624', 'AIIB', 130, 0, 'fotos/conductores/conductor_67e6c41cd3f7c7.26219850.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (137, 1, NULL, 'DNI', '46566533', 'LUIS ANGEL', 'HALANOCCA', 'HANCCO', 'PERUANA', '1990-06-22', '982905533', 'Angelhalanocva@gmail.com', '$2y$12$D2o3TzZ1xLv3dslP6ia7/Ozf69dZUXcpuwIjjfltJ9fter61iqYyu', '0', 0, '8', '89', '804', 'CALLE CONCORDIA ZONA C P.J. CAMPO DE MARTE N- 5 ', 'JORGE HALANOCCA', '997702536', 'HERMANO', NULL, NULL, NULL, NULL, 'H46566533', 'AIIB', 128, 0, 'fotos/conductores/conductor_67c1d3b41294c7.45161015.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (138, 1, NULL, 'DNI', '71478954', 'BRAYAN FABRICIO', 'BECERRA', 'IDME', 'PERUANA', '1999-04-21', '969696227', 'Becerraidmefabricio@gmail.com', '$2y$12$8hiIJbla7HMW8DznFObF.OeYqMsG6kGGTLcrshOXcY6V7IdW0mNbW', '0', NULL, '8', '89', '792', 'ASOC. CRUCE DE CHILINA P. DE POLANCO MZ.G  LT.12', 'MARIA CHAMPI ', '954193645', 'ESPOSA', NULL, NULL, NULL, NULL, 'H71478954', 'AIIB', 125, 0, 'fotos/conductores/conductor_67c1d370df4cc6.34651861.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (139, 1, NULL, 'DNI', '47414743', 'VICTOR ALFONSO', 'FLORES', 'MAMANI', 'PERUANA', '1991-12-23', '951582588', 'benjaminrapsen@gmail.com', '$2y$12$OAqy32qeFbPHZ.dvVDbZAOOlNbMXxK3RaD4PK03/qqCbCNB7IbaqK', '0', NULL, '8', '89', '794', 'JR. HUCAYALY MZ 12 LT 11 ', 'KEVIN FLORES', '927649716', 'AMIGO', NULL, NULL, NULL, NULL, 'H47414743', 'AIIB', 124, 1, 'fotos/conductores/conductor_67c1d345683745.85109548.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (140, 1, NULL, 'DNI', '45227638', 'MOISES', 'MAMANI', 'YUCA', 'PERUANA', '1988-08-16', '958586836', 'MOISESMAYU7@gmail.com', '', '0', NULL, '8', '89', '814', 'URB. LA CAMPIÑA PSJ. LAS NINFAS SECTOR II J-13', 'HAYDEE MAMANI', '953147941', 'ESPOSA', NULL, NULL, NULL, NULL, 'H45227638', 'AIIB', 116, 0, 'fotos/conductores/conductor_67bca66c131907.90015339.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (141, 1, NULL, 'DNI', '74200300', 'FELIPE MANUEL', 'VALDIVIA', 'CARPIO', 'PERUANA', '1993-11-17', '928028972', 'Felipin93_manuel17@hotmail.com', '$2y$12$sLWcpL3BJowio6lCtLZQU.r1BI5dIdaRp5AmEhAEOtADMkzGn8zES', '0', 0, '8', '89', '795', 'PSJ. ALTO LIBERTAD AV. MARIANO MELGAR NRO 203', 'MARIA DIAZ', '946741109', 'PAREJA', NULL, NULL, NULL, NULL, 'H74200300', 'AIIB', 123, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (142, 1, NULL, 'DNI', '44356681', 'HECTOR', 'ROCCA', 'MEZA', 'PERUANA', '1981-09-16', '919749970', 'Roccamezah@gmail.com ', '', '0', NULL, '8', '89', '799', 'AV CARACAS 905 - SIMON BOLIVAR ', 'VANESSA LEON', '958451502', 'ESPOSA', NULL, NULL, NULL, NULL, 'H44356681', 'AIIB', 119, 0, 'fotos/conductores/conductor_67c1d3347bf4a9.92458267.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (143, 1, NULL, 'DNI', '70269044', 'BRUNO EDSON', 'CHOQUEHUAYTA', 'TITO', 'PERUANA', '1991-01-04', '944751444', 'b4expeditions@gmail.com', '$2y$12$3Nb.DuxJObxCbgAmDn3vAueisPhq9swzJSDYGPGnh4tFijWIVLutK', '0', NULL, '8', '89', '794', 'ASOC. 1RO DE JUNIO ZONA A MZ B-3 LOTE 6', 'HAYDEE CHOQUEHUAYTA', '962909058', 'HERMANA', NULL, NULL, NULL, NULL, 'H70269044', 'AIIB', 118, 0, 'fotos/conductores/conductor_67b89644c51ed6.49301803.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (144, 1, NULL, 'DNI', '29646500', 'ALEJO FUSTINO', 'PACHECO', 'TEJADA', 'PERUANA', '1973-02-25', '969918183', 'alejopachecotejada26@gmail.com', '', '0', NULL, '8', '89', '801', 'CALLE MONTONERO MZ C LT 4 URB. MARIANO BUSTAMANTE', 'JENNE SARA', '998870336', 'ESPOSA', NULL, NULL, NULL, NULL, 'H29646500', 'AIIIC', 114, 0, 'fotos/conductores/conductor_67d1e185727581.21537042.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (145, 1, NULL, 'DNI', '47020796', 'KEVIN ERNESTO', 'HALANOCA', 'FLORES', 'PERUANA', '1991-04-09', '927649716', '', '', '0', NULL, '8', '89', '794', 'SOCIEDAD 1RO DE JUNIO ZONA A MZ E1 LT.8', 'ALICIA FLORES', '983918904', 'MADRE', NULL, NULL, NULL, NULL, 'H47020796', 'AIIIC', 113, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (146, 1, NULL, 'DNI', '77329579', 'JHON GABRIEL', 'VEGA', 'VARGAS', 'PERUANA', '1995-05-25', '910246712', 'eminentedk8@gmail.com', '', '0', NULL, 'notdepartamento', '89', '814', 'URB. SAN MARTIN DE SOCABAYA - PUCALLPA 619', 'MARCIA ALVAREZ', '901055731', 'NOVIA', NULL, NULL, NULL, NULL, 'H77329579', 'AIIB', 112, 1, 'fotos/conductores/conductor_67e6c834434484.14047756.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (147, 1, NULL, 'DNI', '29659199', 'MARCO ANTONIO', 'MEDINA', 'HUACOTO', 'PERUANA', '1975-06-30', '959301567', 'mmedinaha@gmail.com', '', '0', 0, '8', '89', '801', 'CALLE LONDRES 501', 'ANA SOSA', '958379749', 'ESPOSA', NULL, NULL, NULL, NULL, 'H29659199', 'AIIA', 111, 0, 'fotos/conductores/conductor_67c0b530512098.12532937.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (148, 1, NULL, 'DNI', '41328419', 'JULIO CESAR', 'ORTIZ', 'CHOQUE', 'PERUANA', '1981-10-02', '931269047', 'julioortizchoque@gmail.com', '', '0', NULL, '8', '89', '792', 'PAMPAS DE POLANCO D 22 – ALTO SELVA ALEGRE', 'YENI', '978166440', 'ESPOSA ', NULL, NULL, NULL, NULL, 'H41328419', 'AIIA', 110, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (149, 1, NULL, 'DNI', '46568313', 'LUIS MIGUEL', 'GOMEZ', 'ALVAREZ', 'PERUANA', '1990-09-13', '969090960', 'virgo1824@gmail.com', '', '0', NULL, 'notdepartamento', '89', '802', 'CALLE TUPC AMARU MZ 5 LT 2 EL PORVENIR - MIRAFLORES', 'GLORIA VELAZCO ', '924104510', 'ESPOSA ', NULL, NULL, NULL, NULL, 'H46568313', 'AIIB', 109, 0, 'fotos/conductores/conductor_67f6a9ef209390.94989173.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (150, 1, NULL, 'DNI', '75079581', 'DARIEN ALEXANDER', 'CUTIPA', 'APAZA', 'PERUANA', '1997-10-17', '907226181', 'cutipa208@gmail.com', '$2y$12$gOLBmwuBn5NLhwwj3BHcFezRhqj7Bbh11rrUlMm6Iu5p1c8xa7VKC', '0', NULL, '8', '89', '799', 'TERRENO TURISTICO AÑASPATA GRANDE CERRO JULI PARCELA 3 MZ B LT 5 - J.L.B. Y R. ', 'SAN PARI', '927266310', 'PAREJA ', NULL, NULL, NULL, NULL, 'U75079581', 'AIIB', 108, 0, 'fotos/conductores/conductor_67e5a6240e3326.09282726.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (151, 1, NULL, 'DNI', '43205700', 'MELVIN ROOSEVELT', 'MANRIQUE', 'OVIEDO', 'PERUANA', '1962-07-18', '908577152', 'aqpmelvin@gmail.com', '$2y$12$f0YegytnMXUon4BEOCqMte86qLTff59awbM8XUPFhQmPBpXGJhWXS', '0', NULL, '8', '89', '795', 'CALLE LA LIBERTAD 204 URB MARISCAL CASTILLA ', 'HILDA QUISPE ', '908592426', 'ESPOSA ', NULL, NULL, NULL, NULL, 'H43205700', 'AIIB', 107, 0, 'fotos/conductores/conductor_67bca62e9e94f1.93488669.jpg', 'fotos_perfil/perfil_43205700_1757442225.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (152, 1, NULL, 'DNI', '70363459', 'NOEL', 'CHILO', 'CONDORI', 'PERUANA', '1996-05-28', '920709884', 'RICARDO_XD987@HOTMAIL.COM', '', '0', NULL, '8', '89', '795', 'ASOC. ASOTAVIC MZ D LT 5B', '-', '-', '-', NULL, NULL, NULL, NULL, 'H70363459', 'AIIB', 106, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (153, 1, NULL, 'DNI', '72194930', 'LEONARDO DAVID', 'ARIAS', 'ALVARO', 'PERUANA', '1994-09-14', '974280476', '', '', '0', NULL, '8', '89', '794', 'CALLE  JOSE OLAYA MZ M LT 3 - CAYMA ', 'ALEJANDRA YUCRA', '997370770', 'ESPOSA ', NULL, NULL, NULL, NULL, 'H72194930', 'AIIB', 104, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (154, 1, NULL, 'DNI', '43122857', 'JUAN CARLOS', 'AROQUIPA', 'PACHO', 'PERUANA', '1985-06-09', '926690082', 'Juan.c.aroquipa@gmail.com', '', '0', NULL, '8', '89', '799', 'CALLE VENEZUELA CDA. 4 URB SIMON BOLIVAR MZ 42 LT 5 ', 'SUSANA LARICO', '974663139', 'ESPOSA', NULL, NULL, NULL, NULL, 'H43122857', 'AIIA', 103, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (155, 1, NULL, 'Carnet', '001745007', 'PAUL STEVENS', 'RODRIGUEZ', 'ORDINOLA', 'VENEZOLANA', '1985-06-18', '923473147', 'paulrodriguezordinola@gmail.com', '', '0', NULL, '8', '89', '793', 'ASENT. H. HORACION ZEBALLOSGAMEZ ZN 1 SEC - F MZ 14', 'PAUL STEVENS', '923473147', 'TITULAR', NULL, NULL, NULL, NULL, 'H000040466', 'AIIB', 102, 0, 'fotos/conductores/conductor_67e5aeb34df548.83730999.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (156, 1, NULL, 'Carnet', '006346332', 'LUIS ELIEZER ', 'CARMONA', 'HERNANDEZ', 'VENEZOLANA', '1992-07-05', '918758586', 'ELIEZERCARMONA1992@gmail.com', '', '0', 0, '8', '89', '799', 'PSJ. AREQUIPA 209 MI PERU ', 'DANIELIS BRACHO', '941121342', 'ESPOSA', NULL, NULL, NULL, NULL, 'V-20.545.527', 'AIIB', 98, 0, 'fotos/conductores/conductor_67c200ad672f38.72535520.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (157, 1, NULL, 'DNI', '29709340', 'MANUEL ALEJANDRO', 'MACEDO', 'CASAPIA', 'PERUANA', '1976-06-10', '900586205', 'macedomanuel420@gmail.com', '$2y$12$/B0umjBIwRWaSiK2068.su5scwTld/D4s9hIozLmvWzAkXjoZkOme', '0', NULL, '8', '89', '794', 'CALLE CUSCO 144 CARMEN ALTO ', 'SANDRA SANTILLAN', '959841605', 'ESPOSA', NULL, NULL, NULL, NULL, 'H29709340', 'AIIB', 96, 0, 'fotos/conductores/conductor_67abe07d9b2884.71828225.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (158, 1, NULL, 'DNI', '43312761', 'VICTOR HUGO', 'ALFARO', 'GUILLEN', 'PERUANA', '1975-12-26', '976109984', 'thunder76@live.com', '', '0', NULL, '8', '89', '793', 'BLOCK.A-5 DPTO. 13 RES. NICOLAS DE PIEROLA ETAPA 2', 'VICTOR HUGO', '976109984', 'TITULAR', NULL, NULL, NULL, NULL, 'Q43312761', 'AIIB', 94, 0, 'fotos/conductores/conductor_67c86b7d60c584.92773153.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (159, 1, NULL, 'DNI', '75423774', 'GIAN MARCO', 'MAMANI', 'MAMANI', 'PERUANA', '2000-01-15', '916855825', 'jheanmarco135@gmail.com ', '$2y$12$V/97Lkqu0ygjjCMZgkD8yeSxat4a8rbcRCyLdy1mPKRdhKvtYzQFq', '0', NULL, '8', '89', '804', 'PJ. CIUDAD BLANCA ZN D. MZ. W LT 3 ', 'ESTEBAN MAMANI', '945142999', 'PAPÁ', NULL, NULL, NULL, NULL, 'H75423774', 'AIIB', 93, 0, 'fotos/conductores/conductor_67c1d2f5e00585.00866961.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (160, 1, NULL, 'DNI', '42753811', 'MILLER FREDY', 'SURCO', 'LLAMOCA', 'PERUANA', '1984-10-02', '968613839', 'milsurco@gmail.com', '$2y$12$kdhQYhQ3BB/LA/dIH1IgYOQVAHQSzDm1iJUbwUaWEYoAOTlfwDcWq', '0', NULL, '8', '89', '795', 'URB LA LIBERTAD CALLE SOSA RUIZ NRO 424 ', 'EDWIN', '907250690', 'HERMANO', NULL, NULL, NULL, NULL, 'H42753811', 'AIIA', 92, 0, 'fotos/conductores/conductor_67ad4480877284.09720352.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (161, 1, NULL, 'DNI', '72521661', 'LUIS DIEGO', 'CALLA', 'SOTO', 'PERUANA', '1990-12-12', '922771071', 'luisdiegosoto90@gmail.com', '', '0', NULL, '8', '89', '802', 'CALLE 234 ALAMEDA SALAVERRY MIRAFLORES T° 13', 'ANNIE CONDORI', '970429933', '970429933', NULL, NULL, NULL, NULL, 'H72521661', 'AIIIB', 91, 0, 'fotos/conductores/conductor_67b894a47cf6a0.22883534.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (162, 1, NULL, 'DNI', '45938425', 'MOISES', 'HANCCO', 'YUCRA', 'PERUANA', '1986-12-12', '937298912', 'NOTIENE@gmail.com', '', '0', NULL, '8', '89', '801', 'PP.JJ. JERUSALEN MZ F LT 01 CALLE RODRIGUEZ BALLON MARIANO MELGAR', 'SILVIA CALL', '995889314', 'ESPOSA', NULL, NULL, NULL, NULL, 'H45938425', 'AIIIC', 82, 0, 'fotos/conductores/conductor_67ad5021165ed1.21292737.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (163, 1, NULL, 'DNI', '47927440', 'YEFERSON', 'DIAZ', 'VASQUEZ', 'PERUANA', '1993-07-30', '976392908', 'Y_LOVE3@hotmail.com', '', '0', 0, '8', '89', '792', 'URB. 3 BALCONES DEL MISTI MZ. M LT. 3 - A.S.A', 'HECTOR DIAZ ', '929517139', 'PADRE', NULL, NULL, NULL, NULL, 'H47927440', 'AIIA', 81, 0, 'fotos/conductores/conductor_67bca74fca53a0.18374953.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (164, 1, NULL, 'DNI', '46670465', 'ERICK MANUEL', 'CONDORI', 'TANTALEAN', 'PERUANA', '1990-10-27', '929811941', 'Erick271090@gmail.com', '', '0', NULL, '8', '89', '798', 'calle mariscal nieto urb. villa sevilla mz F lt 7', 'Gerson condori', '950663654', 'hermano', NULL, NULL, NULL, NULL, 'H46670465', 'AIIB', 242, 0, 'fotos/conductores/conductor_67cc61ff432994.54961960.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (165, 1, NULL, 'DNI', '73341621', 'JORGE EDUARDO', 'CISNEROS', 'PAREDES', 'PERUANA', '1996-03-01', '986650794', 'jorgeducisneros96@gmail.com', '', '0', NULL, '8', '89', '799', 'ALAS DEL SUR NRO 10', 'CRISTHIAN CALLA GUERRA', '910658792', 'COMPADRE', NULL, NULL, NULL, NULL, 'H73341621', 'AIIB', 240, 0, 'fotos/conductores/conductor_67cc61997bf374.34320142.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (166, 1, NULL, 'DNI', '70441684', 'JEAN PIERRE JAIME', 'MANCHEGO', 'VALVERDE', 'PERUANA', '2005-04-21', '963162821', 'jeanmanchegovalverde@gmail.com', '', '0', NULL, '8', '89', '814', 'calle mariano melgar 227', 'angeline retamozo', '906547978', 'pareja', NULL, NULL, NULL, NULL, 'Q70441684', 'AIIB', 239, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (167, 1, NULL, 'DNI', '61050712', 'LA CRUZ ARMANDO ALBERTO', 'ZUÑIGA', 'DE', 'PERUANA', '1993-10-26', '907687273', 'aadelacruz2610@gmail.com', '', '0', NULL, '8', '89', '809', 'calle marcarani 116 A', 'MARIELA CABRERA', '947173274', 'ESPOSA', NULL, NULL, NULL, NULL, 'H61050712', 'AIIB', 238, 1, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (168, 1, NULL, 'Carnet', '005535234', 'ENRIQUE GERMAIN', 'CALDERON', 'GASPAR', 'VENEZOLANA', '1987-08-01', '982939763', 'ENRIQUEGCGC2@GMAIL.COM', '', '0', NULL, '8', '89', '794', 'AAHH MANUELA A ODRIA MZ A LT3 ', 'MARIANELA CALDERON', '979512884', 'HERMANA', NULL, NULL, NULL, NULL, 'H005535234', 'AIIIC', 88, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (169, 1, NULL, 'DNI', '72246624', 'REYMUNDO EULOGIO', 'TICONA', 'CHOQUE', 'PERUANA', '1994-11-12', '929699872', 'Reysito1218@gmail.com', '$2y$12$NVs0lK20r5DOadksyh4VnO8T9QrMBu6MZV8Ql3iLEI4oC1uEU/FpS', '0', NULL, '8', '89', '794', 'ASOC. 1ERO DE JUNIO ZONA B MZ. S-2 LT. 6', 'NICOL CUTIPA', '929699872', 'PAREJA', NULL, NULL, NULL, NULL, 'U72246624', 'AIIB', 232, 0, NULL, 'fotos_perfil/perfil_72246624_1757690011.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (170, 1, NULL, 'Carnet', '004664336', 'RAFAEL ABRAHAN', 'PEÑA', 'ROADES', 'VENEZOLANA', '1977-08-24', '944045604', 'rafaelroades@gmail.com', '$2y$12$NWqEISS051DAyeJE.1AjWugCYdkyA28WkD8WxIV38jHAfb5y.KgsG', '0', NULL, '8', '89', '799', 'URB. LAS ALBORADAS MZ. A LT. 3 ', 'TAMARA TAMOY', '927772189', 'PAREJA', NULL, NULL, NULL, NULL, 'V12828049', 'AIIIA', 231, 0, 'fotos/conductores/conductor_67bca6aedbe659.40961636.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (171, 1, NULL, 'DNI', '70430494', 'CLAUDIO SEBASTIAN', 'PAREDES', 'ZEBALLOS', 'PERUANO', '1996-03-04', '959116479', 'Clauxman77@gmail.com', '', '0', NULL, '8', '89', '795', 'DPTO.2 URB COLEGIO DE INGENIEROS MZ B LT 7', 'KATIUZKA ZEBALLOS ', '959130550', 'MADRE', NULL, NULL, NULL, NULL, 'H70430494', 'AIIB', 86, 1, 'fotos/conductores/conductor_67c1f80dead7f5.50728146.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (172, 1, NULL, 'DNI', '71040221', 'JULIO HECTOR', 'BOLAÑOS', 'LIZARRAGA', 'PERUANA', '1991-02-12', '981418004', 'julianbolanos29@gmail.com', '', '0', NULL, '8', '89', '795', 'av. lima 1300 - alto libertad', 'miguel bolaños', '902894375', 'hermano', NULL, NULL, NULL, NULL, 'H71040221', 'AIIB', 241, 1, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (173, 1, NULL, 'DNI', '46497741', 'RUSSELL MARCELL', 'PALOMINO', 'CACERES', 'PERUANA', '1990-08-10', '959192429', 'sanicarock@gmail.com', '', '0', NULL, '8', '89', '802', 'CALLE PUENTE ARNAO 1405', 'LELSY VARGAS', '996347032', 'PAREJA', NULL, NULL, NULL, NULL, 'H46497741', 'AIIB', 222, 1, 'fotos/conductores/conductor_67c217666615b4.21410205.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (174, 1, NULL, 'DNI', '42757336', 'OSCAR LUIS', 'ALDAMA', 'QUIÑONEZ', 'PERUANO', '1984-12-02', '948787818', 'aldama.oscar2d@gmail.com', '', '0', NULL, '8', '89', '792', 'CALLE DANIEL ALCIDES CARRION #102', 'OSCAR LUIS ALDAMA QUIÑONEZ', '983339000', 'PADRE', NULL, NULL, NULL, NULL, 'K42757336', 'AIIA', 85, 0, 'fotos/conductores/conductor_67c1d2bd0a5255.49525000.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (175, 1, NULL, 'DNI', '73144601', 'LUIS FERNANDO', 'HERRERA', 'HUAMAN', 'PERUANA', '1998-01-17', '939753442', 'hluisfernandoh@gmail.com ', '$2y$12$D.7bQHf1JN7EyAzy4TGq3.skWPFuLiZ73HsXw3Nf3nDuiOw5r1dJW', '0', NULL, '8', '89', '792', 'CALLE DANIEL ALCIDES CARRION 106', 'FERNANDO HERRERA', '934969788', 'PADRE', NULL, NULL, NULL, NULL, 'H73144601', 'AIIB', 230, 0, 'fotos/conductores/conductor_67c21806c6db16.73163784.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (176, 1, NULL, 'Carnet', '007571986', 'JHONATHAN ALEJANDRO', 'MARQUEZ', 'BURGOS', 'PERUANA', '1995-02-22', '901923410', 'jhonathanmarquez28@gmail.com', '', '0', 0, 'notdepartamento', '89', '799', 'PASAJE AREQUIPA 211', 'LUIS CARMONA', '918758586', 'PRIMO', NULL, NULL, NULL, NULL, 'Q007571986', 'AIIIC', 237, 0, 'fotos/conductores/conductor_67fea6cdd1df82.28062333.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (177, 1, NULL, 'DNI', '29721920', 'CARLOS ALBERTO', 'CATARI', 'CALLOAPAZA', 'PERUANO', '1973-06-14', '976044700', 'carloscatari@yahoo.es', '$2y$12$ph1ndYF92.lrHRsZiK3iWOSwtDINkzIq5iRwQsZDkX8ovobB5HewG', '0', NULL, '8', '89', '799', 'URB GENERAL PEDRO DIEZ CANSECO MZ P LT9', 'ITAY LUPITA MORENO VASQUEZ', '990655277', 'ESPOSA', NULL, NULL, NULL, NULL, 'H29721920', 'AIIB', 84, 0, 'fotos/conductores/conductor_67c20d9e085d54.93212705.jpg', 'fotos_perfil/perfil_29721920_1757345207.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (178, 1, NULL, 'DNI', '45051137', 'SILVIA ROSARIO', 'CALLA', 'CHAMBI', 'PERUANO', '1988-03-18', '995889314', 'SRCCH38@GMAIL.COM', '', '0', NULL, '8', '89', '801', 'CALLE COLGATA 123 ALTO SAN MARTIN ', 'MOISES HANCCO YUCRA', '937298912', 'ESPOSO', NULL, NULL, NULL, NULL, 'H45051137', 'AIIB', 83, 0, 'fotos/conductores/conductor_67b89fbf762442.79220121.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (179, 1, NULL, 'DNI', '76171578', 'PRISCILA IVONNE', 'GONZALEZ', 'VICENTE', 'PERUANA', '2000-03-07', '989304290', 'gonzalezvicentepriscivon@gmail.com', '', '0', NULL, '8', '89', '814', 'MATEO PUMACAHUA 213 LL', 'ALEXZANDER MACHACA', '997657289', 'AMIGO', NULL, NULL, NULL, NULL, 'H76171578', 'AIIB', 229, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (180, 1, NULL, 'Carnet', '73240449', 'RONALD LUIS', 'ZAPANA', 'COQUEÑA', 'PERUANA', '1994-05-09', '936826142', 'Reypoderoso_luis3@outlook.es', '$2y$12$5l3tRJF7Q/jl85j0x8Dv7.glcpQUGZWxdSIzcuJNYq3szrSj9dXWa', '0', NULL, 'notdepartamento', '89', '799', 'PUEBLO JOVEN LAS ESMERALDAS MZ. O LT. 13', 'LUCIA COQUEÑA', '920676727', 'MADRE', NULL, NULL, NULL, NULL, 'H73240449', 'AIIA', 228, 0, 'fotos/conductores/conductor_67df7c946f5d77.82789070.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (181, 1, NULL, 'DNI', '74211340', 'FERNANDO JUNIOR', 'HERNANI', 'PINEDO ', 'PERUANA', '1994-03-10', '994778003', 'fernandojupi@gmail.com', '', '0', NULL, '8', '89', '801', 'CALLE HUASCA 100', 'PAOLA COLQUE ', '924 806 502', 'PAREJA', NULL, NULL, NULL, NULL, 'H74211340', 'AIIB', 221, 0, 'fotos/conductores/conductor_67db0cb11ac401.24303466.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (182, 1, NULL, 'DNI', '44614705', 'JOBELL JAIR EDU', 'BENAVENTE', 'ARIZAGA', 'PERUANA', '1986-01-18', '907612777', 'Edubenavente0@gmail.com', '', '0', NULL, '8', '89', '799', 'URB. LAS ESMERALDAS O-13', 'JESSICA ZAPANA', '982972290', 'PAREJA', NULL, NULL, NULL, NULL, 'H44614705', 'AIIB', 227, 0, 'fotos/conductores/conductor_67bca406d889e3.02943176.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (183, 1, NULL, 'DNI', '48843372', 'NEIL BORJN', 'BRAVO', 'PARCO', 'PERUANA', '1996-10-30', '930190481', 'weedneil@gmail.com', '', '0', NULL, '8', '89', '794', 'ASOC. VIRGEN DE CHAPI MZ N LT 5 ', 'CLAUDIA ALCA', '964968767', 'PAREJA', NULL, NULL, NULL, NULL, 'H48843372', 'AIIB', 220, 0, 'fotos/conductores/conductor_67c0b4bf1b28e9.97543710.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (184, 1, NULL, 'DNI', '47141222', 'ILDER', 'INUMA', 'ARCE', 'PERUANA', '1991-02-05', '900361540', 'jimmy.ok2012@gmail.com', '$2y$12$dnw.u1XAVm.JtXATAEr5YO0CsfEuj4ot9I29FzthwqgUMJAIMnos.', '0', NULL, '8', '89', '804', 'PUEBLO JOVEN MIGUEL GRAU AV AREQUIPA 1333', 'LEONELA MAMANI', '949325326', 'ESPOSA', NULL, NULL, NULL, NULL, 'H47141222', 'AIIB', 219, 0, 'fotos/conductores/conductor_67df7d6691d9b5.92508324.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (185, 1, NULL, 'DNI', '75483244', 'JHONY VIDAL', 'MAMANI', 'MAMANI', 'PERUANA', '1999-10-04', '937531119', 'airado.dios.broclesnar@gmail.com', '', '0', 0, '8', '89', '792', 'ASEN.H.VILLA UNION MZ. B LT. 5', 'EVA FERNANDEZ TAIPE ', '967253894', 'ESPOSA', NULL, NULL, NULL, NULL, 'H75483244', 'AIIB', 226, 0, NULL, 'fotos_perfil/perfil_75483244_1757697939.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (186, 1, NULL, 'DNI', '42191963', 'MIGUEL ANGEL', 'CHIRE', 'FLORES', 'PERUANA', '1983-12-24', '925123914', 'miguel.chireflores@gmail.com', '$2y$12$cWT1DoOOKhYKzrpAVMIiD.v3PjqsXhHe12b/T2/tKEEwOh4TUSVoi', '0', NULL, '8', '89', '808', 'AMPLIACION LA ISLA MZ B LT 4 ', 'YULIANA ADALI QUISPE MAMANI', '972419140', 'ESPOSA', NULL, NULL, NULL, NULL, 'Q42191963', 'AIIB', 218, 0, 'fotos/conductores/conductor_67cc60f4e01588.42190810.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (187, 1, NULL, 'DNI', '60865426', 'MANUEL ENRIQUE ', 'LAZO', 'VILLANUEVA', 'PERUANA', '2001-05-15', '940805733', 'manuelenriquelazovillanueva@gmail.com', '$2y$12$H2Adc5yiX.I.KUdVXXt6LuOtMiUcJ4xZGbCjWNlq8fyKCyZM9s/HK', '0', NULL, '8', '89', '794', 'JOHNNY GALLEGOS R-3C LA TOMILLA', 'SUSY DIAZ BOMBILLA', '947713084', 'PAREJA', NULL, NULL, NULL, NULL, 'H60865426', 'AIIB', 225, 0, 'fotos/conductores/conductor_67c0b42ec16864.50569181.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (188, 1, NULL, 'DNI', '42187225', 'GLEN CARLO', 'ALVAREZ', 'NUÑEZ', 'PERUANA', '1963-03-23', '906238986', 'chisitex15@gmail.com', '$2y$12$3pGmtALn4ZSPUpsT673/z.48Cz1mYrRL5zi.bYrYxloIYRalmUfZS', '0', NULL, '8', '89', '795', 'VALLE BLANCO PREMIUM TORRE 3 DPT 306 ', 'LORENA AGUILERA', '918234986', 'ESPOSA', NULL, NULL, NULL, NULL, 'H42187225', 'AIIIC', 217, 0, 'fotos/conductores/conductor_67e162af3443e4.17279221.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (189, 1, NULL, 'DNI', '75150703', 'JEAN CARLO FERNANDO', 'CHIRINOS', 'GARCIA', 'PERUANA', '1998-09-21', '920144917', 'chirinosjan69@gmail.com', '$2y$12$yER5p1zOYmUKmzu9rZPztOIXeq/9cewA9QIhWvCpx50Ozpo8Mycrm', '0', NULL, '8', '89', '801', 'calle londres 504 ', 'diego chirinos', '918259566', 'hermano', NULL, NULL, NULL, NULL, 'H75150703', 'AIIB', 234, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (190, 1, NULL, 'Carnet', '41656784', 'JUAN CARLOS ', 'HANCCO', 'YUCRA', 'PERUANA', '1983-02-04', '968197626', 'juancarloshanccoyucra04@gmail.com', '', '0', NULL, '8', '89', '804', 'ASOC. DE VIVIENDA SANTA MARIA II CMT 4 MZ. S LT. 1', 'CLAUDIA CAPIA', '995994015', 'ESPOSA', NULL, NULL, NULL, NULL, 'H41656784', 'AIIB', 224, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (191, 1, NULL, 'DNI', '45646502', 'DIETHER OBED', 'BELTRAN', 'APAZA', 'PERUANA', '1989-12-14', '928628826', 'Dobagasperjpds@gmail.com', '$2y$12$RhxHF8cd6UOQUqxtqBxO5OwDWvAGo2eMCsuV54EwasYbjM9l5BNGO', '0', NULL, '8', '89', '794', 'ASOC. JOSE CARLOS MARIATEGUI MZ. G LT. 4 CASIMIRO CUADROS', 'DAYANA HUAYLA', '981133457', 'ESPOSA', NULL, NULL, NULL, NULL, 'H45646502', 'AIIB', 223, 0, 'fotos/conductores/conductor_67cc625d38a706.02297475.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (192, 1, NULL, 'DNI', '29551093', 'ANGELICA MILAGROS', 'MONTOYA', 'ESCOBAR', 'PERUANA', '1969-02-20', '977974149', 'milimontoya69@gmail.com', '', '0', NULL, '8', '89', '793', 'CALLE PORCEL 105 URB. MARIA ISABEL', 'LUIS MONTOYA ', '959962100', 'HERMANO', NULL, NULL, NULL, NULL, 'H29551093', 'AIIIA', 216, 0, 'fotos/conductores/conductor_67bcaa0c96f701.22834146.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (193, 1, NULL, 'Carnet', '73501053', 'DANIEL RICHARD', 'CHAYÑA', 'ILLANES', 'PERUANA', '1994-05-27', '944088769', 'Danielch7350@gmail.com', '', '0', NULL, '8', '89', '793', 'REPUBLICA CHILE 2012', 'INGRIT', '943546461', 'PAREJA', NULL, NULL, NULL, NULL, 'U73501053', 'AIIB', 213, 0, 'fotos/conductores/conductor_67bca522216750.30204198.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (194, 1, NULL, 'DNI', '45354419', 'GIANCARLO', 'MARAZO ', 'COSY', 'PERUANA', '1988-09-16', '982942663', 'GIANCARLOMARAZOCOSY@GMAIL.COM', '$2y$12$rlacGsrc8IU7i.wE62nHqesctDYDLAAFFi95emlRVoefMkauv3c3a', '0', NULL, '8', '89', '799', 'URB. CAMPO REAL II ETAPA MZ E LT6', 'LEYSON MONTOYA', '9166599641', 'PRIMO HERMANO', NULL, NULL, NULL, NULL, 'H45354419', 'AIIB', 215, 0, 'fotos/conductores/conductor_67c215eb796a82.66490171.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (195, 1, NULL, 'DNI', '42464578', 'LINO ENRIQUE', 'CENTENO', 'HUARILLOCLLA', 'PERUANA', '1984-01-21', '925765342', 'enrique.centeno1984@gmail.com', '$2y$12$2jYF5xXVzabWnbcH08GT3OoE/xnVCeyXYXRzC4OFUMdVpGObKeojK', '0', NULL, 'notdepartamento', '89', '793', 'COMPLEJO HABITACIONAL FRANCISCO MOSTAJO E-66', 'LIZBETH TACO RAMOS', '957414333', 'ESPOSA', NULL, NULL, NULL, NULL, 'H42464578', 'AIIB', 212, 1, 'fotos/conductores/conductor_67e6d54d6f07c2.16815893.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (196, 1, NULL, 'Carnet', '48194897', 'MIGUEL ANGEL', 'ITO', 'ARIZAPANA', 'PERUANA', '1994-01-04', '986187732', 'angel_2007_xq@hotmail.com', '$2y$12$uKMVcoY7dpnj2.mYSSBlauoLL5.hNuFCVIVB4Lj6IorijC1Ofddaa', '0', NULL, '8', '89', '804', 'URB. NUEVO PERO MZ. Q LT. 1', 'LIZBETH', '992611640', 'PAREJA', NULL, NULL, NULL, NULL, 'H48194897', 'AIIB', 211, 0, 'fotos/conductores/conductor_67c0b4f9897e58.35120276.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (197, 1, NULL, 'DNI', '75459225', 'JHON MARTIN', 'AQUINO', 'YAPO', 'PERUANA', '1994-05-29', '982430763', 'JHONYAPO2994@gmail.com', '', '0', 0, '8', '89', '804', 'JR. BUENOS AIRES 117 PUEBLO J. CIUDAD BLANCA ZON B', 'MELANI PUMACAYO', '901760122', 'PAREJA', NULL, NULL, NULL, NULL, 'H75459225', 'AIIB', 210, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (198, 1, NULL, 'DNI', '46549235', 'LEYSON FRANCKLIN', 'MONTOYA', 'ANCO', 'PERUANA', '1990-08-08', '916599641', 'leysonmontoya@gmail.com', '$2y$12$R4l4.x/kJGe66Zh3z4EW7.WZ7b3iWyVOXjPAgHfBnibGTQxVzIibu', '0', NULL, '8', '89', '814', 'av.nicolas de pierola 321 urb. ciudad mi trabajo', 'giancarlo marazo', '948001739', 'primo hermano', NULL, NULL, NULL, NULL, 'H46549235', 'AIIB', 214, 0, 'fotos/conductores/conductor_67c2154cc30123.41322420.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (199, 1, NULL, 'DNI', '29614322', 'JOSE CARLOS', 'CHAVEZ', 'ORBEGOSO', 'PERUANA', '1973-02-20', '924345002', 'ppcharly2025@gmail.com', '', '0', NULL, '8', '89', '814', 'CALLE LAS ROCAS W-5 LA CAMPIÑA ', 'TERESA ORBEGOSO', '933305372', 'MADRE', NULL, NULL, NULL, NULL, 'H29614322', 'AIIB', 198, 0, 'fotos/conductores/conductor_67ba1438338491.03926738.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (200, 1, NULL, 'Carnet', '05777111', 'LEONARDO ALEXANDER', 'NAVARRO', 'ROSAL', 'VENEZOLANA', '1978-09-18', '964188521', 'leonardo.navarrorosal@gmail.com', '$2y$12$b0qXzHBeJqQjPJ1OGADEhudFvYQ/FBy4wa.fu5MMnyPX4mfw0oM46', '0', 0, '8', '89', '792', 'AV. misti 303 mz d lt 26 urb ASOC. artesanal misti alto selva', 'maria jose veiea', '923244350', 'esposa', NULL, NULL, NULL, NULL, 'H005777111', 'AIIIC', 209, 0, 'fotos/conductores/conductor_67e6d4c5e99fc3.02296515.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (201, 1, NULL, 'DNI', '75213781', 'MARCO', 'POLO', 'APAZA', 'PERUANA', '2000-03-22', '982933908', 'poloapazamarco68@gmail.com', '$2y$12$hvE8xVW0kCJ4wrxiTg.fPuTlb2SHH8uQ5.z4f9xJhOG7o02xjHPRm', '0', NULL, '8', '89', '801', 'CALLE ANCASH 404 ', 'SAMUEL NUÑEZ', '933453551', 'HERMANO', NULL, NULL, NULL, NULL, 'H75213781', 'AIIB', 197, 0, 'fotos/conductores/conductor_67e6ca4c5cf6d9.44832224.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (202, 1, NULL, 'DNI', '43892716', 'ELVIS BERLY', 'PILARES', 'ABERANGA', 'PERUANA', '1986-11-03', '990613191', 'pilareselvis11@gmail.com', '$2y$12$k2gOrGzzMlJzsPwBz0YUfeLlzzg.FFZE8J4R05Swjbqx2UwXbNjgS', '0', 0, '8', '89', '795', 'ASOC. AMAZONAS ZON A MZ C LT 6 ', 'ANA CHUNGA', '923379608', 'ESPOSA', NULL, NULL, NULL, NULL, 'H43892716', 'AIIIA', 196, 0, 'fotos/conductores/conductor_67c0b6a7158a27.89494896.jpg', 'fotos_perfil/perfil_43892716_1756463484.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (203, 1, NULL, 'DNI', '74029795', 'ALEXZANDERT WILBER', 'MACHACA', 'HUAYTA', 'PERUANA', '1996-09-09', '997657289', '', '', '0', NULL, '8', '89', '804', 'URB. MELITON CARBAJAL A-4', 'MAXIMILIANA HUAYTA', '958457109', 'MADRE', NULL, NULL, NULL, NULL, 'H74029795', 'AIIB', 195, 1, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (204, 1, NULL, 'DNI', '47301919', 'JONATHAN ENRIQUE', 'CHOQUEHUANCA', 'HUANCA', 'PERUANA', '1991-03-13', '917231901', 'Jonathan130391@gmail.com', '', '0', NULL, 'notdepartamento', '89', '795', 'AV MANCO CAPAC ZONA C GPR 12 URB SEMI RURAL PACHACUTEC MZ 3', 'ENRIQUE CHOQUEHUANCA', '959955445', 'PADRE', NULL, NULL, NULL, NULL, 'H47301919', 'AIIB', 188, 0, 'fotos/conductores/conductor_67dc4beae1f823.64378103.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (205, 1, NULL, 'DNI', '45926234', 'ERIC JOHAN', 'GARATE', 'VELAZCO', 'PERUANA', '1989-07-30', '951207289', 'Garatevlzc@gmail.com', '', '0', NULL, '8', '89', '794', 'DH DEAN VALDIVIA ENACE MZ Q7 LT 13', 'JULIANA  LOBON', '933544495', 'ESPOSA', NULL, NULL, NULL, NULL, 'H454926234', 'AIIA', 194, 0, 'fotos/conductores/conductor_67e5a042606385.74441932.jpeg', 'fotos_perfil/perfil_45926234_1757207124.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (206, 1, NULL, 'DNI', '47674105', 'YOHON', 'CJURO', 'MENDOZA', 'PERUANA', '1993-03-25', '944215917', 'cjuromendozay@gmail.com', '', '0', NULL, '8', '89', '795', 'JR. NAPO 206 URB SEMI RURAL PACHACUTEC ', 'ALIPIO CJURO ', '958997808', 'HERMANO ', NULL, NULL, NULL, NULL, 'H47674105', 'AIIB', 187, 1, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (207, 1, NULL, 'DNI', '73349296', 'JOAU ANTONIO', 'LLACHO', 'MERCADO', 'PERUANA', '1997-05-22', '902004103', 'londer_654@hotmail.com', '$2y$12$eEXWIr7JRGqFRRbGSD56EOdpdPrP4OXS9gbkFL.LIxLcINEBDbK8q', '0', NULL, 'notdepartamento', '89', '795', 'AV. PERU 1009 PUEBLO JOVEN ALTO LIBERTAD', 'MARIA KANA', '957788039', 'ESPOSA', NULL, NULL, NULL, NULL, 'H73349296', 'AIIA', 193, 0, 'fotos/conductores/conductor_67f6aa52d375f6.50042634.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (208, 1, NULL, 'DNI', '46731458', 'JHIULENY JEIDY', 'CCALLA', 'MACHACA', 'PERUANA', '1987-02-14', '907095396', 'Jhiulenyccalla3@gmail.com', '', '0', NULL, 'notdepartamento', '89', '804', 'AV EL SOL CAMPO MARTE ZONA C MZ O LT 5 ', 'LIZBERH CCALLA ', '955555059', 'HERMANA ', NULL, NULL, NULL, NULL, 'H46731458', 'AIIB', 186, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (209, 1, NULL, 'DNI', '29727449', 'JUAN', 'CORNEJO', 'LEON', 'PERUANA', '1977-09-30', '902984166', 'danflocl77@gmail.com', '', '0', NULL, '8', '89', '802', 'URB. TORRES DE LA ALAMEDA BLOQUE 11B DPT 704', 'FLOR RAMIREZ', '972089783', 'ESPOSA', NULL, NULL, NULL, NULL, 'H29727449', 'AIIB', 192, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (210, 1, NULL, 'DNI', '41737584', 'MIGUEL ANGEL', 'LLERENA', 'AYBAR', 'PERUANO', '1982-12-16', '923881234', 'Mlleren82@gmail.com', '$2y$12$vnu7SLpzPiweuTuNvTjzu.5u0F1/WbHNDNPDyWGJvn4By4Y8mWo6u', '0', NULL, '8', '89', '795', 'JR 2 DE MAYO 604 ALTO LIBERTAD', 'JEANETTE ROMERO ', '955026862', 'ESPOSA', NULL, NULL, NULL, NULL, 'h41737584', 'AIIA', 185, 0, 'fotos/conductores/conductor_67ba1c6327c092.74331047.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (211, 1, NULL, 'DNI', '40660902', 'HENRY', 'TOLA', 'FLORES', 'PERUANA', '1979-10-07', '934119825', 'HENRYTOLA9@gmail.com', '', '0', NULL, '8', '89', '804', 'AA.HH. SEÑOR DE LOS MILAGROS MZ U LT 5', 'SUSANA CHOQUE', '999913723', 'ESPOSA', NULL, NULL, NULL, NULL, 'H40660902', 'AIIB', 191, 0, 'fotos/conductores/conductor_67e6cc578f6600.67040616.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (212, 1, NULL, 'DNI', '43272829', 'PEDRO ANTONIO', 'SUYO', 'ANCONEYRA', 'PERUANA', '1985-12-05', '983153696', 'pedrosuyo551@gmail.com', '', '0', NULL, 'notdepartamento', '89', '804', 'AV. revolución 404- ciudad blanca', 'milagros licona', '920182626', 'pareja', NULL, NULL, NULL, NULL, 'H43272829', 'AIIB', 208, 0, 'fotos/conductores/conductor_67e6cf26eefbf1.99716188.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (213, 1, NULL, 'DNI', '45855681', 'JULIO CESAR', 'BELLIDO', 'BEJARANO', 'PERUANA', '1989-08-16', '922952884', 'Julbejar17@hotmail.com', '', '0', NULL, 'notdepartamento', '89', '792', 'AV. ALIANZA 501 URB. GRAFICOS ', 'YAKELY BEJARANO', '965181449', 'MAMA', NULL, NULL, NULL, NULL, 'H45855681', 'AIIB', 190, 0, 'fotos/conductores/conductor_67e6c99fc1d151.20117449.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (214, 1, NULL, 'DNI', '45895513', 'GUSTAVO LEONARDO', 'PUCHO', 'CONDORI', 'PERUANA', '1989-05-22', '996190731', 'gustavo45pc@gmail.com', '$2y$12$UQt1IoeubbuhTrRK.r0iYu1A.KNn7A4nK6fRl5E4zZiqudz1aUmxm', '0', 0, '8', '89', '794', 'URB. AVIDGE A 8', 'YOSELYN FLORES', '984076602', 'CONVIVIENTE', NULL, NULL, NULL, NULL, 'H45895513', 'AIIB', 207, 0, 'fotos/conductores/conductor_67f6aaa8de24e9.13004268.jpg', 'fotos_perfil/perfil_45895513_1757710093.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (215, 1, NULL, 'DNI', '76368409', 'RICHARD EDILBERTO', 'VARA', 'SENCCA', 'PERUANA', '0001-01-01', '949326180', 'el.richardvara@gmail.com', '', '0', NULL, '8', '89', '801', 'PJ.generalismo jose de san martin ZN c calle paraguay 117', 'ANA TARQUI', '949326180', 'PAREJA', NULL, NULL, NULL, NULL, 'V76368409', 'AIIB', 206, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (216, 1, NULL, 'DNI', '72524100', 'RICARDO JUNIOR', 'CHAMPI', 'PARI', 'PERUANA', '1994-08-11', '956331176', 'Ricardo.champi@tecsup.esu.pe', '', '0', 0, '8', '89', '804', 'PUEBLO JOVEN MIGUEL GRAU 4 ETAPA ZN D CMT 17 MZ 25 LT 14 ', 'ANGIE MAMANI', '929712132', 'PAREJA', NULL, NULL, NULL, NULL, 'H72524100', 'AIIB', 189, 0, 'fotos/conductores/conductor_67e6c8a426dbe7.54198311.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (217, 1, NULL, 'DNI', '46208542', 'EDILBERTO', 'CANAZA', 'CARI', 'PERUANA', '1990-02-24', '951974849', 'canazacariedy54@gmail.com', '', '0', NULL, '8', '89', '814', 'parcela agricola N 1327', 'ayde zela ', '977709114', 'esposa', NULL, NULL, NULL, NULL, 'H46208542', 'AIIB', 205, 0, 'fotos/conductores/conductor_67bca93564e372.35689870.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (218, 1, NULL, 'DNI', '74026319', 'JORGE LUIS', 'MAMANI', 'CHAVEZ', 'PERUANA', '1998-04-14', '931532537', 'jlvrocio170715@gmail.com', '$2y$12$CUe4nah8vR0xh.UeGrQ97OzwEycUqjQoCJN1YBDb2K1BuyJuO4t7u', '0', NULL, '8', '89', '792', 'ASOC. LAS ROCAS DEL MIRADOR J-8', 'ROCIO PAOLA CHOQUE ', '955668823', 'ESPOSA', NULL, NULL, NULL, NULL, 'H74026319', 'AIIB', 183, 0, 'fotos/conductores/conductor_67e6c7434a3095.35348846.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (219, 1, NULL, 'DNI', '71486516', 'KEVIN JEFFERSON', 'ZAPANA', 'CAPIRA', 'PERUANA', '1996-12-20', '907527327', 'kzapanacapira@gmailcom', '', '0', NULL, '8', '89', '804', 'MALECON BALTA 202 B 15 DE AGOSTO', 'LESLY PILCO ', '963327469', 'ESPOSA', NULL, NULL, NULL, NULL, 'H71486516', 'AIIB', 204, 0, 'fotos/conductores/conductor_67e6d584207b82.85687009.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (220, 1, NULL, 'DNI', '76252187', 'ISMAEL', 'HUARACHA', 'MAMANI', 'PERUANA', '1985-01-21', '944551324', 'Ismael.hm2195@gmail.com', '', '0', NULL, '8', '89', '804', 'AV MIGUEL GRAU 402 BP MIGUEL GRAU ZONA B ', 'CAROL ROJAS ', '934988509', 'ESPOSA ', NULL, NULL, NULL, NULL, 'H76252187', 'AIIB', 184, 0, 'fotos/conductores/conductor_67df7da6303725.13126731.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (221, 1, NULL, 'DNI', '70040425', 'CHRISTIAN NIELS', 'NAVARRETE', 'PEREZ', 'PERUANA', '1990-12-17', '954394256', 'CHRISSNAVARRETE@GMAIL.COM', '', '0', NULL, '8', '89', '795', 'Semi rural calle francisco bolognesi MZ d lt 14', 'maria rosa peres alcantara', '972882712', 'mama', NULL, NULL, NULL, NULL, 'D70040425', 'AIIB', 180, 0, 'fotos/conductores/conductor_67e5a21bd2d168.32865216.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (222, 1, NULL, 'DNI', '46667039', 'EDWIN', 'VIZCARRA', 'APAZA', 'PERUANA', '1990-10-05', '935422396', 'EW.VICAR@GMAIL.COM', '$2y$12$Mf8n.6FkN1yXq2RD44LEJe9/ddmOHSCRJVX4rEFH28JTeuFkrMtWi', '0', NULL, '8', '89', '799', 'URB. villa hermosa cerro july ZN a mz g  lt 3', 'rosario del pilar salinas valdivia ', '925325868', 'esposa', NULL, NULL, NULL, NULL, 'H46667039', 'AIIA', 181, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (223, 1, NULL, 'DNI', '42961107', 'DAVID', 'LLAMOCA', 'GASPAR', 'PERUANA', '1985-03-24', '959126108', 'dllamoca85@gmail.com', '', '0', NULL, '8', '89', '792', 'ASOC. VILLA ECOLOGICA ZN C MZ B LT 5 ', 'LOURDES CHOQUE', '958727158', 'ESPOSA', NULL, NULL, NULL, NULL, 'H42961107', 'AIIIC', 182, 0, 'fotos/conductores/conductor_67cc614b9920a6.77325650.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (224, 1, NULL, 'DNI', '47409600', 'GONZALO ALONSO', 'TOLEDO', 'VALENCIA', 'PERUANA', '1992-06-26', '985638836', 'ga.toledov1992@gmailcom', '', '0', NULL, '8', '89', '796', 'ASENT.URB MUNICIPALIDAD GUSTAVO MOHME LLONA', 'LUCY  QUISPE ', '904229853', 'ESPOSA', NULL, NULL, NULL, NULL, 'H47409600', 'AIIB', 203, 0, 'fotos/conductores/conductor_67fea68a069750.61616293.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (225, 1, NULL, 'DNI', '43405475', 'WILLINGTON DIONI', 'MARTINEZ', 'HUAMAN', 'PERUANA', '1981-08-21', '931136321', 'eros4144@gmail.com', '', '0', NULL, '8', '89', '793', 'av alfonso hugarte s/n pueblo tradicional tio chico', 'gabi capa soncco ', '9100145456', 'amiga', NULL, NULL, NULL, NULL, 'Q43405475', 'AIIB', 202, 0, 'fotos/conductores/conductor_67e6cac27fd429.53492295.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (226, 1, NULL, 'DNI', '71318267', 'JOHN MANUEL', 'TORRES', 'MAMANI', 'PERUANA', '1997-02-01', '935591159', 'j.manueltm02@gmail.com', '$2y$12$jE5HuIGwzVeHKvxLYagbh.PKS7CfW7t6gpAd3NfWkfhlnJgQi5Lr2', '0', 0, '8', '89', '795', 'asoc.ciudad municipal zona VI mz D', 'NATHALY COELLO', '951167954', 'ESPOSA', NULL, NULL, NULL, NULL, 'H71318267', 'AIIB', 201, 0, 'fotos/conductores/conductor_67e6ce62084c50.45520720.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (227, 1, NULL, 'DNI', '45653526', 'EDER', 'HUANCAPAZA', 'MAMANI', 'PERUANA', '1988-04-22', '934981581', 'Cachorro_aqp_@hotmail.com', '$2y$12$h7XwJdotvtgTJRcZT4XsHORvo8eM9RRW..uYpjYkitektWR9IpOZC', '0', 0, '8', '89', '804', 'ASC. de vivi. viergen de copacabana G-07', 'mamani', '962799528', 'mama', NULL, NULL, NULL, NULL, 'H45653526', 'AIIB', 200, 0, 'fotos/conductores/conductor_67c0b79d377340.60006001.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (228, 1, NULL, 'DNI', '06038537', 'BERNARDINO', 'PAREDES', 'RAMIREZ', 'PERUANA', '1958-05-20', '993898527', 'paredeslarry@gmail.com', '', '0', NULL, 'notdepartamento', '89', '798', 'AV. LOS ANGELES 121', 'RUFINA CABANA', '989693222', 'ESPOSA', NULL, NULL, NULL, NULL, 'H06038537', 'AIIB', 171, 0, 'fotos/conductores/conductor_67c0bb894a4fb3.12910961.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (229, 1, NULL, 'DNI', '76967248', 'KEVIN ABEL', 'APAZA', 'APAZA', 'PERUANA', '1994-12-22', '918919967', 'kevinabel2212@gmail.com', '$2y$12$fUBx/Y27wqAiMudccOIaseKzQYdREw2VeejhVgJz.dHUSbpk4sfuO', '0', NULL, '8', '89', '796', 'ASTH.RECIDENCIAL VILLA ZEGARRA MZ I LT 7', 'ANGELO DANIEL CASANI APAZA', '983301149', 'PRIMO', NULL, NULL, NULL, NULL, 'J76967248', 'AIIB', 172, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (230, 1, NULL, 'DNI', '41930342', 'JIMMY MICHAEL', 'ZUÑIGA', 'COLLADO', 'PERUANA', '1983-03-23', '908522900', 'jizuco3@gmail.com', '$2y$12$BGrXACwMLcheQtuMC/EUcumTPUyvMjIBHTY.6/bdg6W0sQ0FE5Qy6', '0', NULL, '8', '89', '818', 'URB.EL REMANSO MZB LT 8', 'JIMMY ZUÑIGA ', '930198960', 'FAMILIAR', NULL, NULL, NULL, NULL, 'H41930342', 'AIIA', 173, 0, 'fotos/conductores/conductor_67df7e32e44ce4.51303382.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (231, 1, NULL, 'DNI', '74917700', 'CARLOS ALONSO', 'APAZA', 'PALLI', 'PERUANA', '1996-07-10', '985533136', 'carlosapazapalli@gmail.com', '', '0', NULL, 'notdepartamento', '89', '804', 'PPJJ. MIGUEL GRAU ZONA B ETAPA IV MZ 41.LT 36', ' EMILDA PALLE', '946608460', 'MADRE', NULL, NULL, NULL, NULL, 'J74917700', 'AIIB', 174, 0, 'fotos/conductores/conductor_67e6d72606a649.97068818.jpg', 'fotos_perfil/perfil_74917700_1757688756.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (232, 1, NULL, 'DNI', '74943392', 'RONALD JUNIOR', 'APAZA', 'SUCAPUCA', 'PERUANA', '1997-07-19', '991463865', 'ronaldapaza9@gmail.com', '', '0', NULL, 'notdepartamento', '89', '801', 'psj buenos vistas s/n pueblo j. cerro la chilca mz D lt 8', 'REYNA SUCAPUCA', '950943752', 'MADRE', NULL, NULL, NULL, NULL, 'H74943392', 'AIIB', 175, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (233, 1, NULL, 'DNI', '47750093', 'JEAN PIERRE', 'PUMA', 'PILLCO', 'PERUANA', '1993-03-19', '993907458', 'jeanpierre1933@gmail.com', '$2y$12$LvUydIzu.AWhDpGT6mfl2.2e1VQKbjIttls2.TNTXYuRw2XG2gqoy', '0', NULL, '8', '89', '802', 'CALLE  FRANCISCO BOLOGNESI 731 C.P. ', 'JOSHUA PUMA', '987939854', 'HERMANO', NULL, NULL, NULL, NULL, 'H47750093', 'AIIB', 179, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (234, 1, NULL, 'DNI', '77144330', 'JOEL JOSHUA', 'PUMA', 'PILLCO', 'PERUANA', '1995-07-07', '987939854', 'jpumap@unsa.edu.pe', '', '0', NULL, '8', '89', '802', 'CALLE BOLOGNESI 725', 'MIRIAN CARELIA AGULAR', '900402500', 'PAREJA', NULL, NULL, NULL, NULL, 'H77144330', 'AIIB', 178, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (235, 1, NULL, 'DNI', '46180474', 'JUAN CARLOS', 'QUISPE', 'CCALLO', 'PERUANA', '1989-09-02', '916913275', 'juancarlosquispeccallo699@gmail.com', '', '0', 0, '8', '89', '794', 'JOSE OLAYA ZN B MZ B LT 7 -BOLOGNESI', 'YAQUI QUISPE ', '923910485', 'HERMANA', NULL, NULL, NULL, NULL, 'Q46180474', 'AIIB', 177, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (236, 1, NULL, 'DNI', '44872788', 'ALVARO GREGORIO', 'ALVAREZ', 'QUISPE', 'PERUANA', '1987-03-22', '934111189', 'alvaalva469@gmail.com', '$2y$12$5jAuC/zXyJRcA1VkQcn6.OPvVQ1v2QjleVuG31PZ8MfkFfirstEVm', '0', NULL, '8', '89', '802', 'ALAMEDA  SALAVERRY O 16', 'GREGORIO ALVAREZ', '944192379', 'PADRE', NULL, NULL, NULL, NULL, 'H44872788', 'AIIA', 176, 0, 'fotos/conductores/conductor_67bca41b4300e4.51980602.jpg', 'fotos_perfil/perfil_44872788_1758630725.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (237, 1, NULL, 'DNI', '44240229', 'WILSON ALEX', 'COPARA', 'QUISPE', 'PERUANA', '1987-03-07', '950308230', 'wilsoncoparaquispe@gmail.com', '', '0', 0, 'notdepartamento', '89', '804', 'CALLE CULTURA 103 URB JOSE CARLOS MARIATEGUI', 'ARIANE PORTUGAL MAMANI', '939430051', 'ESPOSA', NULL, NULL, NULL, NULL, 'H44240229', 'AIIA', 99, 0, 'fotos/conductores/conductor_67f03b359ead14.28413901.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (238, 1, NULL, 'DNI', '44224652', 'LUIS REY', 'QUISPE', 'MAMANI', 'PERUANA', '1986-12-25', '981768754', 'quispe8624@gmail.com', '', '0', NULL, '8', '89', '796', 'VILLA ZEGARRA MZ D LT 1', 'FELICITAS', '985495400', 'MAMA', NULL, NULL, NULL, NULL, 'K44224652', 'AIIB', 100, 0, 'fotos/conductores/conductor_67d1e164e298a6.68564928.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (239, 1, NULL, 'Carnet', '002569034', 'ENDERSON ANTONIO', 'GARCIA ', 'ORELLANA', 'VENEZOLANA', '1989-09-23', '925308230', 'endersong23@gmail.com', '', '0', NULL, 'notdepartamento', '89', '792', 'AV. ATAHUALPA 445', 'RICARDO MONITO', '929418401', 'PRIMO', NULL, NULL, NULL, NULL, 'V-19.956.388', 'AIIB', 101, 0, 'fotos/conductores/conductor_67dd8c629ad7b3.91627766.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (240, 1, NULL, 'DNI', '70194858', 'DANIEL ALBERTO', 'VILA', 'DIAZ', 'PERUANA ', '1992-04-27', '51908060473', 'danielviladiaz1@gmail.com', '$2y$12$InC5S1uUFZKA903l7kIwjeYxqXyncRypI8/ihMeDlo3EY./GsFs8e', '0', NULL, '8', '89', '804', 'CALLE LIBERTAD 411 ', 'PAMELA TUMI TORRES', '917677649', 'ESPOSA', NULL, NULL, NULL, NULL, 'PA-221874', 'AIIB', 245, 0, 'fotos/conductores/conductor_67c2162d13b1d6.28245509.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (241, 1, NULL, 'DNI', '46617912', 'JOHNNY ABEL', 'YEPEZ', 'CHURA', 'PERUANA', '1989-01-01', '946289842', 'johnnyyepez01@gmail.com', '', '0', NULL, '8', '89', '804', 'ASENT. H AMPLI. P J. CIUDAD BLANCA ZN B MZ E LT 7', 'MELY ESTHER', '91830810', 'ESPOSA', NULL, NULL, NULL, NULL, 'H46617912', 'AIIIA', 244, 0, 'fotos/conductores/conductor_67db0d965514b5.66406855.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (242, 1, NULL, 'Carnet', '001494887', 'ALEJANDRO', 'MESIAS', 'OSORIO', 'VENEZOLANA', '1992-05-31', '958111641', 'alejo.marinhico@gmail.com', '$2y$12$oqbCoDCuGRoHDYM26h9hOerDjdi/5A59ZksDB9OV0sTq589v38hHK', '0', NULL, '8', '89', '795', 'GIRON2 DE MAYO 200 ALTO LIBERTAD', 'ROSARIO PALACIO PAREDES', '943238884', 'ESPOSA', NULL, NULL, NULL, NULL, 'H001494887', 'AIIB', 246, 0, 'fotos/conductores/conductor_67c21315657d74.94371977.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (243, 1, NULL, 'DNI', '43480906', 'JUAN CARLOS', 'RIVERA', 'SUCLLA', 'PERUANA', '1986-03-12', '981940331', 'JRIVERA130@GMAIL.COM', '$2y$12$eYPb7rigoEXP4RzAkoHKQ.jbJgH3FLFAFtA822J/Ypep.aGginTRK', '0', NULL, '8', '89', '792', 'PSJ MOLLENDO 109', 'JUAN GARCIA', '982930653', 'AMIGO', NULL, NULL, NULL, NULL, 'H43480906', 'AIIIC', 142, 0, 'fotos/conductores/conductor_67b53178694026.58173519.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (244, 1, NULL, 'DNI', '76585536', 'JOSUE RANDU', 'CABANA', 'PALOMINO', 'PERUANA', '1996-05-20', '917983217', 'JCABANAPALOMINO@gmail.com', '$2y$12$pfkyFa7UV4qq/PhYkKNB4uCP28kHtpXlSC33GIE.WNc7scW4k.CRq', '0', NULL, '8', '89', '804', 'INDUSTRIAL 625 PARQUE INDUSTRIAL APIMA', 'ROSA PALOMINO', '951123256', 'MADRE', NULL, NULL, NULL, NULL, 'H76585536', 'AIIB', 247, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (245, 1, NULL, 'DNI', '40198998', 'HUBERT JESUS', 'ALVAREZ', 'MAMANI', 'PERUANA', '1978-06-06', '980976660', 'hubertjesus78@gmail.com', '', '0', NULL, '8', '89', '802', 'MARIA NIEVES BUSTAMANTE 111', 'CYNTHIA NUÑEZ', '944244894', 'ESPOSA', NULL, NULL, NULL, NULL, 'H40198998', 'AIIA', 248, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (246, 1, NULL, 'DNI', '76822310', 'EDGAR MAURO', 'CANAZA', 'YUCRA', 'PERUANA', '1997-11-03', '906150705', 'mauro.canaza.03@gmail.com', '$2y$12$qyZnwWSw.O9KMzMcQdopdOKtj5dCT7giBgWwa1yxohpZTwD6ioHuS', '0', NULL, '8', '89', '799', 'URB. MI PERU MZ G LT 8', 'MARYOTI', '989685318', 'PAREJA', NULL, NULL, NULL, NULL, 'H76822310', 'AIIB', 249, 0, 'fotos/conductores/conductor_67c21948a76476.66787108.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (247, 1, NULL, 'DNI', '76408958', 'SERGIO ANDRE', 'CALCINA', 'MANCHEGO', 'PERUANA', '2000-08-28', '900747263', 'castielandre07@gmail.com', '', '0', NULL, '8', '89', '792', 'URB. INDEPENDENCIA - ZONA B 21 15 ', 'PAOLA MANCHEGO', '925311594', 'MADRE', NULL, NULL, NULL, NULL, 'H76408958', 'AIIB', 250, 0, 'fotos/conductores/conductor_67c21854560242.17489373.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (248, 1, NULL, 'DNI', '29566254', 'JAIME GREGORIO', 'ARENAS', 'SANDOVAL', 'PERUANA', '1970-01-02', '977999408', 'Notiene@gmail.com', '', '0', NULL, '8', '89', '801', 'CALLE ATENAS 310', 'JAIME GREGORIO', '975742410', 'TITULAR', NULL, NULL, NULL, NULL, 'Q29566254', 'AIIB', 251, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (249, 1, NULL, 'DNI', '47391287', 'ARTURO PEDRO', 'LLACHO', 'QUISPE', 'PERUANA', '1990-10-05', '902481555', 'arturollacho.q@gmail.com', '$2y$10$fTCHXUJRNCESdCFwSk51aeudozcUiieug.k.OeecZ0HqK8NPtEpHu', '0', 0, '8', '89', '792', 'JR. LEONCIO PRADO ZN. B PUEBLO J. INDEPENDENCIA MZ. 8 LT. 17', 'BEATRIZ QUISPE', '960898845', 'MADRE', NULL, NULL, NULL, NULL, 'H47391287', 'AIIB', 252, 0, 'fotos/conductores/conductor_67db0d6ec68f41.94796381.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (250, 1, NULL, 'DNI', '47844273', 'GIANMARCO EDSON', 'NIETO', 'LINARES', 'PERUANA', '1991-11-07', '908977740', 'Gnmarconielin@gmail.com', '$2y$12$nIBlucp6zwV3CgAXfJlYq.4h0JxNsrgON.ZUusLzqeBd9yirdnH/S', '0', NULL, '8', '89', '794', 'JR FRANCISCO BOLOGNESI PUEBLO T ACEQUIA ALTA R 1 5C', 'YOBANNA LINARES', '930536342', 'MADRE', NULL, NULL, NULL, NULL, 'H47844273', 'AIIB', 253, 0, 'fotos/conductores/conductor_67c21504ac5232.60885381.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (251, 1, NULL, 'DNI', '72481612', 'DAVID', 'POCOHUANCA', 'ALEJO', 'PERUANA', '2003-08-24', '990586307', 'pocohuancaalejodani@gmail.com', '$2y$12$xJUoFuvUeSzIt1N/G09Eg.KOe4gAWhLdwbd8fVueZk4jI6IyYQ/1K', '0', 0, '8', '89', '804', 'CALLE CUNORADA 102 URB. CIUDAD BLANCA', 'FABIANA', '956927896', 'CUÑADA', NULL, NULL, NULL, NULL, 'H72481612', 'AIIB', 254, 0, 'fotos/conductores/conductor_68c433db37b812.39410930.jpeg', 'fotos_perfil/perfil_72481612_1757509003.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (252, 1, NULL, 'DNI', '42161827', 'CARLOS ELMER', 'VILCA', 'ASQUI', 'PERUANA', '1983-10-28', '960657608', 'CARLOSEVILCAA@GMAIL.COM', '', '0', NULL, '8', '89', '792', 'AV. LOS ANGELES 54-5 COMITE 18 P. JOVEN INDEPENDENCIA ', 'RAFAEL VILCA ', '924244564', 'HERMANO ', NULL, NULL, NULL, NULL, 'H42161827', 'AIIA', 255, 0, 'fotos/conductores/conductor_67c214eeef7f64.45150981.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (253, 1, NULL, 'DNI', '73061472', 'DIEGO MIGUEL', 'ARRIARAN', 'HERRERA', 'PERUANA', '1996-08-07', '944432941', 'LUMI.COPORATION1@GMAIL.COM', '', '0', NULL, '8', '89', '794', 'AA HH ASOC. DE VIVIENDA CIUDAD DE LOS PIONEROS ZN C MZ B LT 16', 'DEYSI FIORELA HUAMANI HUARANCA', '963976667', 'CONVIVIENTE', NULL, NULL, NULL, NULL, 'H73061472', 'AIIB', 256, 0, 'fotos/conductores/conductor_67d1e13a6c7914.04756737.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (254, 1, NULL, 'DNI', '41461862', 'FRANK GABRIEL', 'CANO', 'MULLIZACA', 'PERUANA', '1982-06-25', '952399649', 'EROSDERSON@GMAIL.COM', '', '0', NULL, '8', '89', '802', 'AV. SAN MARTIN 313-A', 'CARMEN CANO', '986765535', 'PRIMA', NULL, NULL, NULL, NULL, 'H41461862', 'AIIB', 258, 0, 'fotos/conductores/conductor_68a8cab2381c66.79372958.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (255, 1, NULL, 'DNI', '42918607', 'MIGUEL ANGEL', 'ZAVALA', 'BARRA', 'PERUANA', '1985-03-12', '9559608321', 'ZAVALA8512@gmail.com', '', '0', NULL, '8', '89', '792', 'AAHH ASOC RAFAEL HOYOS RUBIO ZON B COM 7 MZ Q LT 30', 'HUGO LAZARTE', '983790431', 'CUÑADO', NULL, NULL, NULL, NULL, 'K42918607', 'AIIB', 259, 1, 'fotos/conductores/conductor_67c218c9a47a42.40614052.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (256, 1, NULL, 'DNI', '43616046', 'ELVIS GODOFREDO', 'ALVAREZ', 'SALAZAR', 'PERUANA', '1983-10-09', '921357240', 'egalvarezs@gmail.com', '', '0', NULL, '8', '89', '798', 'CALLE EGIPTO 206', 'XIOMARA SHUAN', '972559434', 'ESPOSA', NULL, NULL, NULL, NULL, 'H43616046', 'AIIA', 257, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (257, 1, NULL, 'DNI', '47385683', 'ULISES AVELINO', 'CHAMBI', 'VALDIVIA', 'PERUANA', '1992-10-15', '901343680', 'ULISES_12_23@HOTMAIL.COM', '', '0', 0, '8', '89', '792', 'AV. AMERICA URB. ALTO SELVA ALEGRE Z/N B ', 'CINDY CASTRO', '933818880', 'ESPOSA', NULL, NULL, NULL, NULL, 'H47385683', 'AIIA', 260, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (258, 1, NULL, 'DNI', '43741218', 'OSCAR ENRIQUE', 'NUÑEZ', 'BUTRON', 'PERUANA', '1986-09-19', '987321575', 'oscarenrique_nunez@hotmail.com', '$2y$12$rQR71JitLDpdILAxDM8mGuUoVAdsCFJ.kqGOvnyObK1OBmH6G4R.u', '0', 0, '8', '89', '793', 'CALLE FEDERICO BARRETO 112', 'ROSA DEL CARPIO', '947715588', 'ESPOSA', NULL, NULL, NULL, NULL, 'H43741218', 'AIIB', 261, 0, 'fotos/conductores/conductor_67db0d1d3c6d92.17898552.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (259, 1, NULL, 'DNI', '61839879', 'WILBERT', 'UCHASARA', 'GONZALES', 'PERUANA', '1993-08-30', '967184021', '-', '', '0', NULL, '8', '89', '802', 'PUEBLO TRADICIONAL MIRAFLORES D-15A - MIRAFLORES', 'WILBERT', '967184021', 'TITULAR', NULL, NULL, NULL, NULL, 'H61839879', 'AIIB', 11, 1, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (260, 1, NULL, 'DNI', '41201628', 'DEIVIS AURELIO', 'IBARRA', 'MACEDO', 'PERUANA', '1992-03-20', '997671389', 'IDEIVIS_147@hotmail.com', '', '0', NULL, '8', '89', '797', 'ASOC. VIV. SANTO DOMINGO SAN BERNARDO MZ. L LT 1 ', 'DEIVIS AURELIO', '997671389', 'TITULAR', NULL, NULL, NULL, NULL, 'H41201628', 'AIIIC', 121, 1, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (261, 1, NULL, 'DNI', '42159844', 'RICHARD PEDRO', 'SANJINES', 'HUALPA', 'PERUANA', '1981-10-11', '910229001', '-', '', '0', NULL, '8', '89', '804', 'JIRON LAS AMERICAS A5 ', 'JUAN SANJINES', '974521012', 'HERMANO', NULL, NULL, NULL, NULL, 'U42159844', 'AIIIC', 243, 1, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (262, 1, NULL, 'Carnet', '03165444', 'LUIS DANIEL', 'HURTADO', 'BLANCO', 'VENEZOLANA', '1975-07-31', '925405733', 'LUISHBLANCO@GMAIL.COM', '$2y$12$6cnTzbjfToijgtvbbk.f9eekXM4kt5yc9LZx850ScqmkmVy9/i4iK', '0', NULL, '8', '89', '795', 'CALLE AREQUIPA 106 ALTO LIBERTAD', 'HEIDY MAIZO', '924072344', 'ESPOSA', NULL, NULL, NULL, NULL, 'H003165444', 'AIIA', 262, 0, 'fotos/conductores/conductor_67df7d257b1614.27421425.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (263, 1, NULL, 'Carnet', '17737101', 'ANDREI ENRIQUE', 'RINCON', 'MONTIEL', 'VENEZOLANA', '1985-01-07', '951859297', 'andreirinconmontiel@gmail.com', '', '0', NULL, '8', '89', '802', 'ALAMEDA SALAVERRY - CALLE 4 - B52', 'YARLENI GARCES', '902128670', 'SUEGRA', NULL, NULL, NULL, NULL, 'V17737101', 'AIIIC', 263, 0, 'fotos/conductores/conductor_67cc6014617556.21172685.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (264, 1, NULL, 'DNI', '48054882', 'JOEL WILSON', 'CHILO', 'CONDORI', 'PERUANA', '1993-11-24', '935433765', 'joelchilocondori@gmail.com', '', '0', NULL, '8', '89', '792', 'ASENT. H. EL MIRADOR ETAPA 1 MZ. D LT. 12', 'VILMA PACCO', '934322707', 'PAREJA', NULL, NULL, NULL, NULL, 'H48054882', 'AIIB', 264, 1, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (265, 1, NULL, 'DNI', '44935157', 'RENE ALFONSO', 'PUMA', 'HUANCARA', 'PERUANA', '1987-12-26', '901077047', 'RENEREMIX2612@GMAIL.COM', '', '0', NULL, '8', '89', '795', 'AUTOPISTA LA JOYA MZ 23 LT 05', 'DINA PUMA', '933247396', 'HERMANA', NULL, NULL, NULL, NULL, 'Q44935157', 'AIIB', 265, 0, 'fotos/conductores/conductor_67cc60931f0381.91010281.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (266, 1, NULL, 'DNI', '76055912', 'ITALO LUCIANO', 'CCAMA', 'JUAREZ', 'PERUANA', '1999-12-02', '964782090', 'ITALOCCAMA@GMAIL.COM', '', '0', NULL, '8', '89', '795', 'APVIS PRIMERO DE NOVIEMBRE MZ A LT 5', 'MARIA LUZ JUAREZ', '954988719', 'MADRE', NULL, NULL, NULL, NULL, 'H76055912', 'AIIB', 266, 1, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (267, 1, NULL, 'DNI', '46113743', 'MIRKO MOISES', 'FLORES', 'CASTILLO', 'PERUANA', '1989-06-18', '974891369', 'mirkoflores91@gmail.com', '$2y$12$8aJSFrF.xP95ZZM5fDGd1usBf4WTbadeZxEZD096fF.qQCr1zCQYG', '0', NULL, '8', '89', '795', 'ASC. GUILLERMO MERCADO MZ. L LT. 7', 'CRISTINA TAIPE', '912151340', 'PAREJA', NULL, NULL, NULL, NULL, 'H46113743', 'AIIIB', 267, 0, NULL, 'fotos_perfil/perfil_46113743_1759453496.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (268, 1, NULL, 'DNI', '44285524', 'ADOLFO RODRIGO', 'LARICO', 'MAMANI', 'PERUANA', '1986-10-20', '917659910', 'laricomamaniadolforodrigo@gmail.com', '$2y$12$EumdvXbduI0Em9FXpaIVgusVho2x4gLkNkX/5un7S7Vzt.n0I5/0G', '0', NULL, '8', '89', '804', 'P. JOVEN MIGUEL GRAU ZN-C MZ-13 LT-8', 'ADOLFO', '917659910', 'TITULAR', NULL, NULL, NULL, NULL, 'H44285524', 'AIIIC', 268, 0, 'fotos/conductores/conductor_67db0c5ac80725.66132825.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (269, 1, NULL, 'Carnet', '003280193', 'OBERTO SEGUNDO', 'ROMERO ', 'GARCES', 'VENEZOLANA', '1980-05-30', '929930572', 'OBERTOSEGUNDOROMEROGARCES@GMAIL.COM', '', '0', NULL, '8', '89', '799', 'URB FECIA CALLE BRAZIL N100', 'MICHELL PIÑA', '932050448', 'PAREJA ', NULL, NULL, NULL, NULL, 'H003280193', 'AIIB', 269, 1, 'fotos/conductores/conductor_67df7ea07134b4.92732998.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (270, 1, NULL, 'Carnet', '006512363', 'GABRIEL ANTONIO', 'MONTAÑO', 'RUIZ', 'VENEZOLANA', '1998-12-28', '907804321', 'GABRIELANTONIOMONTANO3@GMAIL.COM', '', '0', NULL, '8', '89', '799', 'calle paz soldan 620 mz s4 lt 13', 'PAMELA RIVAZ ', '970111368', 'ESPOSA ', NULL, NULL, NULL, NULL, 'H006512363', 'AIIB', 270, 0, 'fotos/conductores/conductor_67e6e6139877f9.65196429.jpg', 'fotos_perfil/perfil_006512363_1757538716.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (271, 1, NULL, 'DNI', '75185244', 'MIGUEL ANGEL', 'CONDORI', 'SALAS', 'PERUANA', '1996-11-06', '922184525', 'ryudara_paint@hotmail.com', '$2y$12$IaTGDwqoQW00S/0.Ko5ihuS8Q90KSFBkocbCaFhqZvkeTnuSitzCi', '0', 0, '8', '89', '801', 'ASOC. SAN JERÓNIMO MZ. H LT. 12', 'RITA CONDORI', '924376409', 'MADRE', NULL, NULL, NULL, NULL, 'H75185244', 'AIIB', 271, 0, 'fotos/conductores/conductor_68a8c652e48c45.08507891.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (272, 1, NULL, 'DNI', '46735447', 'CARLOS ALBERTO', 'LLUTARI', 'HERRERA', 'PERUANO', '1990-03-05', '944464055', 'CARLOSLUTARI@GMAIL.COM', '', '0', NULL, '8', '89', '804', 'ASOC VIRGEN DEL CARMEN MZ F LT 3 PAUCARPTA ', 'RUBI ORTIZ ', '916727794', 'ESPOSA', NULL, NULL, NULL, NULL, 'H46735447', 'AIIB', 272, 0, 'fotos/conductores/conductor_67f6aaeeb9c2a2.81244681.jpg', 'fotos_perfil/perfil_46735447_1760646128.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (273, 1, NULL, 'Carnet', '005622617', 'CARLOS MARTIN ', 'RIVA', 'CASTILLO', 'URUGUAYO', '1988-01-16', '946198141', 'TINCHO-CAR @HOTMAIL.COM', '$2y$12$k7X3RsbQKWbtpbSetBt/KezIGcnMuY2r.S30oSS1ao/MmoXbk8fMG', '0', NULL, '8', '89', '792', 'AV ARGENTINA 131 ', 'LILI HERRERA ', '962246754', 'ESPOSA', NULL, NULL, NULL, NULL, 'H005622617', 'AIIB', 273, 0, 'fotos/conductores/conductor_67fe9a9714aa97.47682346.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (274, 1, NULL, 'DNI', '22100610', 'MARCO ANTONIO', 'RIOS', 'SANTOS', 'PERUANA', '1975-02-08', '981127809', '197540marcoantonio@gmail.com', '', '0', NULL, 'notdepartamento', '89', '795', 'JIRON MOQUEGUA LT. 8 MZ. 4 ZONA F ', 'LUZ MARINA ANCO', '957408477', 'ESPOSA', NULL, NULL, NULL, NULL, 'H22100610', 'AIIA', 274, 0, 'fotos/conductores/conductor_67fe9ae61e1bd0.61258828.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (275, 1, 82, 'DNI', '76583585', 'JORGE LUIS', 'ENRIQUEZ', 'GUTIERREZ', 'PERUANA', '2001-11-15', '901313977', 'JORGE.MONITOR.94@GMAIL.COM', '', '0', 0, '8', '89', '814', 'AV LAS PEÑAS 901 CERRO SALAVERRY', 'VIANCA JAKELIN ENRIQUEZ ', '950023620', 'MADRE', NULL, NULL, NULL, NULL, 'H76583585', 'AIIB', 275, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (276, 1, 80, 'DNI', '45573648', 'JESUS JORDAN', 'SALAS', 'VALENCIA', 'PERUANA', '1998-04-01', '958207889', 'jjsalasv@gmail.com', '', '0', NULL, '8', '89', '802', 'ALAMEDA SALAVERRY MZ A1 LT 33 ', 'KERELI VALENCIA', '961404552', 'HERMANA', NULL, NULL, NULL, NULL, 'H45573648', 'AIIIB', 276, 0, 'fotos/conductores/conductor_67fea7143789b2.84189293.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (277, 1, 81, 'DNI', '76644271', 'NESTOR EDUARDO', 'ARANCIBIA', 'PINEDA', 'PERUANA', '1995-10-09', '966471212', 'arancibiapinedanestor2020@gmail.com', '', '0', NULL, 'notdepartamento', '89', '798', 'AV. PAISAJISTA S/N PJ. PAMPAS DEL CUSCO', 'VERONICA HERRERA', '963880724', 'PAREJA', NULL, NULL, NULL, NULL, 'H76644271', 'AIIB', 277, 0, 'fotos/conductores/conductor_6838865a9575d6.63027096.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (278, 1, 80, 'DNI', '44781781', 'EDWIN ALCANTARA', 'CHURATA', 'SULCA', 'PERUANA', '2025-04-15', '956758424', 'edwin_29mas@hotmail.com', '', '0', NULL, 'notdepartamento', '89', '795', 'APIPA SECTOR III MZ I LT 10 ', 'MIRIAN ANCCO', '958106867', 'CONVIVIENTE', NULL, NULL, NULL, NULL, 'H44781781', 'AIIB', 278, 0, 'fotos/conductores/conductor_680fb75e515c25.30456521.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (279, 1, 83, 'DNI', '74837672', 'JHON FREDY', 'MALDONADO', 'CJURO', 'PERUANA', '2005-06-20', '972546606', 'JHON.MCJ.2005@GMAIL.COM', '', '0', NULL, '8', '89', '808', 'AV PARAISO DE CHUPA MZ D 7 LT 4 ', 'VIVIA CJURO MENDOZA', '964512232', 'MADRE', NULL, NULL, NULL, NULL, 'H74837672', 'AIIB', 279, 0, 'fotos/conductores/conductor_680fb8c257b985.55375635.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (280, 1, 82, 'DNI', '41506842', 'EFRAIN OMAR', 'BALBIN', 'RAMIREZ', 'PERUANO', '1982-09-11', '961876625', 'efrain.balbinr@gmail.com', '', '0', 0, '8', '89', '794', 'urb. bello campo A-6', 'SHEYLA QUELOPANA ', '984777207', 'CONYUGUE ', NULL, NULL, NULL, NULL, 'Q41506842', 'AIIA', 280, 0, 'fotos/conductores/conductor_680fb7d74812d2.46782594.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (281, 1, 80, 'DNI', '47020552', 'SMIT MARIO', 'ROQUE', 'ITO', 'PERUANA', '1992-05-30', '995930275', 'smit.roque.189@gmail.com', '', '0', NULL, '8', '89', '792', 'Aa.H. 1RO DE ENERO', 'MARISOL ZERESO', '900338402', 'PAREJA', NULL, NULL, NULL, NULL, 'U47020552', 'AIIB', 281, 0, 'fotos/conductores/conductor_680fb85d647e43.28863838.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (282, 1, 80, 'DNI', '40535394', 'MANUEL ENRIQUE', 'CHAFLOQUE', 'AQUIJE', 'PERUANA', '1980-01-23', '986243345', 'ECHAFLOQUEA@gmail.com', '', '0', NULL, 'notdepartamento', '89', '799', 'URB AGRICULTURA J-15', 'KARIA SANTA CRUZ', '993417404', 'ESPOSA', NULL, NULL, NULL, NULL, 'E40535394', 'AIIB', 282, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (283, 1, 82, 'DNI', '23980346', 'MIGUEL ANGEL', 'PINO', 'LUNA', 'PERUANO', '1974-08-09', '999222047', 'MIGUELPINOLUNA@GMAIL.COM', '', '0', 0, '8', '89', '793', 'MOORE 210  - TINGO ', 'SANDRA CUBA', '999444119', 'ESPOSA', NULL, NULL, NULL, NULL, 'H23980346', 'AIIB', 283, 0, 'fotos/conductores/conductor_682b4f8bad1900.46319338.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (284, 1, 80, 'DNI', '73861769', 'FRANCO RYAN', 'MONTES', 'DAVID', 'PERUANA', '2024-08-15', '914159621', 'Mateomontesdavid341@gmail.com', '', '0', NULL, 'notdepartamento', '89', '793', 'PAMPITA ZEBALLOS 221', 'Aleyni Montes David', '999800133', 'FAMILIAR', NULL, NULL, NULL, NULL, 'H73861769', 'AIIB', 284, 1, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (285, 1, 82, 'DNI', '42575441', 'EDGAR JUAN', 'HUANCACHOQUE', 'CHIJCHEAPAZA', 'PERUANA', '1982-03-25', '907551305', 'HUANCACHOQUECHIJCHEAPAZAE@GMAIL.COM', '', '0', NULL, 'notdepartamento', '89', '794', 'ASOC MICRO EMORESARIOS 11 DE MAYO MZ N LT 16 ZN B ', 'ELOY MAMANI', '952217285', 'AMIGO', NULL, NULL, NULL, NULL, 'H42575441', 'AIIIC', 285, 1, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (286, 1, 82, 'DNI', '74325995', 'WILDER AMERICO', 'TIPULA', 'GUTIERREZ', 'PERUANA', '1995-06-29', '977811292', 'WILDERTIPULA@GMAIL.COM', '$2y$12$xE2hMtyaT6x9pKmisrytaeJV8rXiJ09mbf89O/w1.W0LtY7U3i4Q6', '0', NULL, '8', '89', '793', 'CALLE PERU 518', 'GROVER TIPULA GUTIERREZ ', '916156009', 'HERMANO', NULL, NULL, NULL, NULL, 'H74325995', 'AIIB', 286, 0, NULL, 'fotos_perfil/perfil_74325995_1756755482.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (287, 1, 80, 'DNI', '40198061', 'EDUARDO OMAR', 'AGOSTINELLI', 'SAMANEZ', 'PERUANA', '1979-02-18', '959887362', 'EOAGOSTINELLI@gmail.com', '', '0', NULL, '8', '89', '795', 'VICTOR ANDRES BELAUNDE I 5 CMTE 3 ZONA A', 'DEYSI GAMONAL', '923768899', 'ESPOSA', NULL, NULL, NULL, NULL, 'H40198061', 'AIIB', 287, 0, 'fotos/conductores/conductor_682b4fc3e6a416.63753918.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (288, 1, 80, 'DNI', '29703708', 'CESAR AUGUSTO', 'MORAN', 'DIAZ', 'PERUANA', '1967-10-15', '971747314', 'cesarmorandiaz068@gmail.com', NULL, '0', NULL, 'notdepartamento', '89', '798', 'PSJ. PROGRESO 106', 'ANDRI MORAN', '963142160', 'HIJO', NULL, NULL, NULL, NULL, 'H29703708', 'AIIIC', 288, 0, 'fotos/conductores/conductor_682b4fe2965e34.62726417.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (289, 1, 82, 'DNI', '47301907', 'IVAN ELIAZAR', 'CRUZ', 'NINA', 'PERUANA', '1991-09-24', '976297707', 'IVANCNKG@GMAIL.COM', '$2y$12$Q1cF3vf2CneYgBsihW8ZJuPayS5dVnYYHicVUHsATmz20CDIgYibC', '0', 0, '8', '89', '821', 'CALLE ASOC. VIRGEN DE ASUNCION. 2 BLOCK  2 PISO 1 ASOC VIRGEN DE LA ASUSNCION  ETAPA 2  MZ E LT 2', 'GLORIA MARIA SARA GONZALES', '933327435', 'ESPOSA', NULL, NULL, NULL, NULL, 'H47301907', 'AIIB', 289, 0, 'fotos/conductores/conductor_683886ebaa72a7.36382982.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (290, 1, 82, 'DNI', '29721214', 'JAVIER ALONSO', 'SALAS', 'SIERRA', 'PERUANA', '1976-11-27', '942981703', 'JAVIERSALAS1976@GMAIL.COM', '$2y$12$yREVrC7cPo6APuQdU0gwOOvA3xsQhlttPOx.o7Z2Y2h6AGLT5zt0u', '0', NULL, '8', '89', '799', 'URB. JUAN PABLO VIZCARDO Y GUZMAN  MZ S LT 8', 'MARIELA SALAS SIERRA', '959285793', 'HERMANA', NULL, NULL, NULL, NULL, 'H29721214', 'AIIIC', 290, 0, 'fotos/conductores/conductor_68348d98ae1a88.67999729.jpg', 'fotos_perfil/perfil_29721214_1757249029.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (291, 1, 82, 'DNI', '43242132', 'ROLANDO DAVID', 'VILCA', 'MAMANI', 'PERUANO', '1985-05-08', '961135628', 'rolando.vilca85m@gmail.com', NULL, '0', NULL, '8', '89', '798', 'aso de viv el buen panorama mz H lt 7', 'yamilet mendoza chipana', '942620373', 'pareja', NULL, NULL, NULL, NULL, 'h43242132', 'AIIIC', 291, 0, 'fotos/conductores/conductor_6838869dea72f6.07322387.jpg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (292, 1, 83, 'DNI', '76384781', 'JONATHAN ALFREDO', 'CLAVERIAS', 'ROMAN', 'PERUANA', '1998-05-29', '957135329', 'JONATHANCLAVERIAS7@GMAIL.COM', NULL, '0', NULL, '8', '89', '795', 'URB RESIDENCIAL MONTEBELLO MZ H LT 8 ', 'ALFREDO ', '958150151', 'PADRE', NULL, NULL, NULL, NULL, 'H76384781', 'AIIB', 292, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (293, 1, 90, 'Carnet', '007309912', 'Jose Alejandro', 'Rodriguez', 'Velasquez', 'VENEZUELA', '1995-11-20', '926881516', 'JOSEALEJANDRORODRIGUEZ.95@GMAIL.COM', NULL, '5', NULL, '8', '89', '793', 'URB. SAN SALVADOR PSJ LAS AMERICAS E2', 'MARIARNYS GUERRA', '926071525', 'CONCUBINA', NULL, NULL, NULL, NULL, 'ANT007309912', 'AIIIC', 2, 0, 'fotos/conductores/conductor_68a8c9d11e6597.06382244.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (294, 1, 83, 'DNI', '48363685', 'KAREN', 'CCOA', 'CACERES', 'PERUANA', '1993-07-08', '935054834', 'KAREN.CCOA@GMAIL.COM', NULL, '0', NULL, '8', '89', '804', 'AV AMAUTA 730 MANCO CAPAC ', 'JESUS', '944123264', 'PAREJA', NULL, NULL, NULL, NULL, 'H48363685', 'AIIB', 293, 1, 'fotos/conductores/conductor_68a8ca95c10358.40220512.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (295, 1, 85, 'DNI', '45930482', 'JUAN MANUEL', 'FOLLANO', 'GRANADA', 'Peruana ', '1989-09-07', '997603574', 'juanmanuelfollanogranada@gmail.com', NULL, '0', NULL, '8', '89', '799', 'Calle ricardo palma 512 Asent.H. Simon bolivar zona A', ' Adolfo Follano Quispe', '993472576', 'Padre ', NULL, NULL, NULL, NULL, 'H45930482', 'AIIB', 294, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (296, 1, 90, 'DNI', '45686567', 'ARNOLD RAMIRO', 'NARVAEZ', 'INFA', 'PERUANO', '1989-04-17', '979305661', 'arnoldramiro.89@gmail.com', NULL, '0', NULL, '8', '89', '792', 'ASC. VILLACONQUISTADOR II F-3', 'jose narvaez', '958218087', 'PADRE', NULL, NULL, NULL, NULL, 'H45686567', 'AIIB', 295, 0, 'fotos/conductores/conductor_68a8cafbe2d5c7.33671819.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (297, 1, 82, 'DNI', '80614085', 'JUAN CARLOS', 'MAMANI', 'HUAYNAPATA', 'PERUANA', '1980-05-04', '948803028', 'CARLOSMHRKT@gmail.com', NULL, '0', NULL, '8', '89', '792', 'PJ PAMPA DE POLANCO ZN B SECTOR JUAN VELASCO ALVARADO MZ M LT 11', 'VICTOR CAJO MAMANI', '982938411', 'CUÑADO', NULL, NULL, NULL, NULL, 'H80614085', 'AIIA', 296, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (298, 1, 82, 'DNI', '77821864', 'PETER TOTO', 'ORTEGA', 'TICONA', 'PERUANA', '1999-11-29', '900540992', 'jordanortega557@gmail,com', NULL, '0', NULL, '8', '89', '815', 'av santa maria de guadalupe etapa B mz B lt 2', 'hermana', '900660956', 'vilma ortega ticona', NULL, NULL, NULL, NULL, '77821864', 'AIIB', 297, 0, 'fotos/conductores/conductor_68a8cb14dfadf3.31659586.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (299, 1, 82, 'DNI', '47259596', 'DANIEL EDUARDO', 'MOLLEPAZA', 'CHURAPA', 'PERUANO', '1990-11-14', '924233650', 'dani142005@hotmail.com', NULL, '0', 0, '8', '89', '804', 'ca esmeralda 100 manuel prado', 'cesar corahua', '+51 982 072 266', 'amigo', NULL, NULL, NULL, NULL, 'H47259596', 'AIIB', 298, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (300, 1, 83, 'DNI', '75594783', 'AGSEL GUILLERMO', 'GONZALES', 'ARCE', 'PERUANA', '2004-10-11', '930839344', 'GONZALESARCEAGSENGUILLERMO@GMAIL.COM', NULL, '0', NULL, '8', '89', '792', 'ASOC. AGUSTO SALAZAR BONDY MZ D LT 6', 'KATERINE PATRICIA', '959124872', 'MADRE', NULL, NULL, NULL, NULL, 'T-75594783', 'B-IIc', 1, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (301, 1, 97, 'DNI', '70360289', 'GUSTAVO ALONSO', 'BANDA', 'RIVERA', 'PERUANA', '1995-12-11', '924395431', 'bandarivera1995@gmail.com', NULL, '0', 0, '8', '89', '801', 'calle arica mz L lt 1', 'jorge banda', '933774185', 'hermano', NULL, NULL, NULL, NULL, 'H70360289', 'AIIB', 299, 0, NULL, 'fotos_perfil/perfil_70360289_1756495719.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (302, 1, 82, 'DNI', '72378529', 'KATHERINE', 'JIMENEZ', 'VALDERRAMA', 'PERUANA', '1992-04-28', '943843937', 'jvkaty123@gmail.com', '$2y$12$lRWNI./2bAWVWk.0Xn7IT.2XsCCWQqQZMo/PyFQNRqaIcyQGO.oBK', '0', NULL, '8', '89', '798', 'calle morro de arica 228 - tingo ', 'marianela valderrama de jimenez', '974154506', 'MADRE', NULL, NULL, NULL, NULL, 'h72378529', 'AIIB', 300, 0, 'fotos/conductores/conductor_68a8c9a09507e8.56638126.jpeg', 'fotos_perfil/perfil_72378529_1757342627.webp', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (303, 1, 82, 'DNI', '72907234', 'PAUL EDINSON', 'LLAYQUI', 'CUTIPA', 'PERUANA', '2002-05-04', '912908485', 'BOSSIE98711@GMAIL.COM', '$2y$12$B12FUty0K5ald/wOxJ9Ub.WRsYnkIB63alyHXlDnsHoXeomRPfW5C', '0', NULL, '8', '89', '794', 'SOL DE ORO MZ B LT 13', 'GILSON LLAYQUI CUTIPA', '983040218', 'HERMANO', NULL, NULL, NULL, NULL, 'Q72907234', 'AIIB', 301, 0, 'fotos/conductores/conductor_68a8c8a0d08d24.61319424.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (304, 1, 83, 'DNI', '72856944', 'EDIMAR EDUARDO', 'VASQUEZ', 'SOLIS', 'PERUANA', '1996-11-13', '981336452', 'VASQUEZDTB@GMAIL.COM', '$2y$12$mslwkNgynURRrArmmrPNQ.ZBKWNPFnxav2NBnvB6DGSsS26RIXd0e', '0', NULL, '8', '89', '801', 'AV PERU 506', 'JOSELIN TEJADA MORALES ', '987823328', 'PAREJA', NULL, NULL, NULL, NULL, 'H092073', 'B-IIb', 3, 0, 'fotos/conductores/conductor_68a8c839d69ea7.53918142.jpeg', 'fotos_perfil/perfil_72856944_1757652877.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (305, 1, 105, 'DNI', '70603636', 'RICARDO', 'DELGADO', 'CENTTY', 'PERUANO', '1995-03-09', '915071099', 'richy.delgatty@gmail.com', NULL, '0', NULL, '8', '89', '801', 'juan manuel polar 500', 'genny maritza', '915071191', 'madre', NULL, NULL, NULL, NULL, 'H70603636', 'AIIB', 302, 0, 'fotos/conductores/conductor_68c3708b964d15.28076133.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (306, 1, 97, 'DNI', '75410342', 'JOSEPH AUGUSTO', 'CUSI', 'QUIÑO', 'PERUANA', '1996-02-06', '956286707', 'josephaugustoc@gmail.com', NULL, '0', NULL, '8', '89', '792', 'pj villa asunción MZ D1 LT5', 'AGUSTIN CUSI GUZMAN', '958842837', 'PAPA', NULL, NULL, NULL, NULL, 'H75410342', 'AIIB', 303, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (307, 1, 85, 'DNI', '72570984', 'RAUL HERNAN', 'CONDORI', 'LUQUE', '33', '1992-03-24', '991942441', 'raul.c.luque24@gmail.com', NULL, '0', NULL, '8', '89', '798', 'CALLE JOSE MARIA CORBACHO MZ H LT 3 ASENT.H LEON DEL SUR', 'N.N', '000000000', 'OTROS', NULL, NULL, NULL, NULL, 'H091388', 'B-IIa', 4, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (308, 1, 82, 'DNI', '75112767', 'BENJAMIN HUGO', 'CHURA', 'RUELAS', 'PERUANA', '2005-05-10', '957897845', 'BC15957@gmail.com', '$2y$12$pENHZ97Fmh4471RZ4tUsSewJ1we2jjR9hHWcidL2qTyOT/Z4eXCsq', '0', 0, '8', '89', '802', 'AV. SAN MARTIN  4915  PORVENIR ', 'TANIA RUELAS ', '993305989', 'MAMA', NULL, NULL, NULL, NULL, 'H75112767', 'AIIB', 304, 0, 'fotos/conductores/conductor_68a8c7b38f9229.24791810.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (309, 1, 105, 'DNI', '76850988', 'SANDRO HAKIM', 'PERALTA', 'VALENCIA', 'PERUANA', '2005-12-12', '914465970', 'PERALTASANDRO123@gmail.com', NULL, '0', NULL, '8', '89', '802', 'ALAMEDA DE SALAVERRI MZ A -15 CALLE 2 ', 'KARELI VALENCIA V.', '961404552', 'MAMA', NULL, NULL, NULL, NULL, 'H76850988', 'AIIB', 306, 1, 'fotos/conductores/conductor_68a8c5c52774a2.58589936.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (310, 1, 83, 'DNI', '72895471', 'WILBER ALONSO', 'VILLALTA', 'ZEGARRA', 'PERUANA', '1998-07-09', '913591052', 'WILVERVILLANTA98@GMAIL.COM', NULL, '0', NULL, '8', '89', '792', 'AV MARTINELI TISSON 229 ', 'MIGUEL ANGEL  VILLANTA', '993118331', 'HERMANO', NULL, NULL, NULL, NULL, 'H72895471', 'AIIB', 307, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (311, 1, 97, 'DNI', '72100555', 'JOSE VICTOR', 'DEL PORTAL', 'ALVA', 'PERUANA', '1998-10-10', '922870702', 'delportaljose@gmail,com', NULL, '0', NULL, '8', '89', '793', 'Pro los andez Mz Z lt1 Ramiro Priale', 'Nathaly Flores', '904564857', 'Pareja', NULL, NULL, NULL, NULL, 'Q72100555', 'AIIB', 308, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (312, 1, 83, 'DNI', '72522530', 'ANGEL SAMUEL', 'RAMOS', 'PILARES', 'PERUANA', '1999-03-15', '912910257', 'ANGELRAMOSPILARES86@GMAIL.COM', NULL, '0', NULL, '8', '89', '795', 'ASOC AMAZONAS LT 6 MZ C ZONA A ', 'ELVIS ', '990613191', 'HERMANO', NULL, NULL, NULL, NULL, 'H0272522530', 'B-IIb', 5, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (313, 1, 83, 'Carnet', '006610634', 'MARIANNYS DE  JESUS', 'CEDEÑO', 'TAMOY', 'VENEZOLANA', '1989-06-17', '960310945', 'TAMOYMARIANNYS@GMAIL.COM', NULL, '0', NULL, '8', '89', '804', 'AV AMAUTA 108 PAUCARTA ', 'TAMARA CEDEÑO', '927721894', 'HERMANA', NULL, NULL, NULL, NULL, 'H01005419685', 'B-IIb', 6, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (314, 1, 83, 'DNI', '73302059', 'WALTER DANIEL', 'NAVARRO', 'TUESTA', 'PERUANA', '1999-12-24', '928273836', 'WADANT12@HOTMAIL.COM', NULL, '0', NULL, '8', '89', '802', 'LAS PALMERAS G17 ', 'ELIZ JOSE AVILA RAGA', '940931524', 'COMPAÑERO', NULL, NULL, NULL, NULL, 'U73302059', 'B-IIc', 7, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (315, 1, 82, 'DNI', '71844563', 'LUIS ENRIQUE', 'PAREDES', 'APAZA', 'peruana', '1996-08-28', '938757600', 'luisparedesapaza16@gmail.com', '$2y$12$NWDF2D2f6pAUkO7oavy7SOMw1jxPkzUHUbjN/8Rjl4SrDS.oUUAIO', '0', NULL, '8', '89', '795', 'urb. el rosario II', 'jaquelyn apaza', '946680579', 'madre', NULL, NULL, NULL, NULL, 'h71844563', 'AIIB', 309, 0, 'fotos/conductores/conductor_68a8c3ea2367e2.50950246.jpeg', 'fotos_perfil/perfil_71844563_1759168524.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (316, 1, 82, 'DNI', '72162233', 'ALEXIS GRIMALDO', 'MERCADO', 'SOTO', 'PERUANA', '2001-08-05', '960647108', 'alexis.soto.5831@gmail.com', NULL, '0', NULL, '8', '89', '795', 'asoc.amazonas zn A mnz J lote 2', 'victor andres quenta turpo', '988441033', 'papa', NULL, NULL, NULL, NULL, 'H72162233', 'AIIB', 310, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (317, 1, 104, 'DNI', '09901322', 'JOEL RICHARD', 'OJEDA', 'TUYA', 'PERUANA', '1974-09-03', '987385843', 'joelojeda030610@gmail.com', '$2y$12$jRmpEj6J47fCH6Bh6x51J.1nhxZLaXgz6dHxV5r5HZ5CW0Im79DN2', '0', 0, '19', '173', '1635', 'TACNA 4024 SMP', 'KIMBERLEY', '977776593', 'HIJA', NULL, NULL, NULL, NULL, 'Q09901322', 'AIIB', 1, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (318, 1, 106, 'DNI', '77036056', 'KEVYN WILLY', 'LUPO', 'DIAZ', 'peruana', '1996-07-10', '994715929', 'klupodi27@gmail.com', NULL, '0', NULL, '8', '89', '804', 'av.8 de octubre zona. A 105 Pueblo J. Miguel grau MZ. 16 LT.16', 'Lucy Díaz Arenas', '993582262', 'Madre', NULL, NULL, NULL, NULL, 'H0177036056', 'B-IIb', 8, 0, 'fotos/conductores/conductor_68a8c46451d006.12115652.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (319, 1, 108, 'DNI', '45619110', 'MIGUEL ANGEL', 'MATEO', 'DUFFOO', 'PERUANA', '1986-04-12', '977138315', 'mateovalientemassimoenrique@gmail.com', NULL, '0', NULL, '8', '89', '801', 'CALLE PRADA 226 ', 'LUISA MARIA VALIENTE VERGARA ', '51962382199', 'PAREJA', NULL, NULL, NULL, NULL, 'ANT45619110', 'B-IIb', 9, 1, 'fotos/conductores/conductor_68a8c5a5c73cc2.55919198.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (320, 1, 108, 'DNI', '77813178', 'LUIS MIGUEL', 'CRUZ', 'INCA', 'PERUANO', '1999-09-22', '987821539', 'LUCHITO_X99@HOTMAIL.COM', '$2y$12$ozifokRR9vM99NQhpw8GdORRZjjIeea.hv36Cz07hRsVKrlMqHnOK', '0', NULL, '8', '89', '793', 'URB. CASA LAGO SAN JOSE QUINTA 3 MZ A LT 17', 'ALEJANDRO VERA', '982941320', 'PADRE ', NULL, NULL, NULL, NULL, 'A77813178', 'B-IIb', 10, 0, 'fotos/conductores/conductor_68a8c55d17fe31.19808821.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (321, 1, 82, 'DNI', '74355744', 'LUIS FERNANDO', 'PAGAZA', 'CASTRO', 'PERUANO', '2000-09-26', '939465009', 'luisfernandopagaza@gmail.com', NULL, '0', NULL, '8', '89', '799', 'calle 13 de junio B-11B', 'MARIA ALICIA ARROE LAZARO', '995759596', 'ESPOSA', NULL, NULL, NULL, NULL, 'H74355744', 'AIIB', 311, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (322, 1, 82, 'DNI', '75657179', 'GUIDO ROLANDO', 'YUTO', 'ZEGARRA', 'PERUANO', '1999-03-31', '941034229', 'yuuiku123@gmail.com', NULL, '0', NULL, '8', '89', '804', 'virgen de copacabana MZ a LT 20', 'DIANA MARISOL CONDORPUSA AYME', '965016473', 'ESPOSA ', NULL, NULL, NULL, NULL, 'H75657179', 'AIIB', 312, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (323, 1, 82, 'DNI', '47941003', 'YASMANI FREDY', 'BUSTINZA', 'GUTIERREZ', 'PERUANO', '2026-05-17', '944522308', 'CHICHOBG6@GMAIL.COM', NULL, '0', 0, '8', '89', '804', 'AV PACIFICO MIGUEL GRAU MZ 32 LT 2 ', 'JESICA', '980100768', 'PAREJA', NULL, NULL, NULL, NULL, 'H47941003', 'AIIB', 313, 0, NULL, 'fotos_perfil/perfil_47941003_1759201064.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (324, 1, 109, 'DNI', '72612854', 'CRISTOFFER RAUL', 'BERLANGA', 'SALAZAR', 'Peruana', '1996-09-13', '51966713575', 'ghtght160@gmail.com', NULL, '0', NULL, '8', '89', '801', 'calle belisario flores225', 'darwin calcina', '982922764', 'primo', NULL, NULL, NULL, NULL, 'H72612854', 'AIIB', 314, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (325, 1, 85, 'DNI', '46561071', 'DANIEL GONZALO', 'RAMOS', 'MAQUERA', 'PERUANO', '1990-09-08', '944136057', 'DRAMOSMAQ@GMAIL.COM', NULL, '0', NULL, '8', '89', '795', 'Av. los incas a.h. semi rural pachacutec Mz. 7 Lt 9-O', 'Janeth Miriam Lazarte Alejo', '991342566', 'Esposa ', NULL, NULL, NULL, NULL, 'H46561071', 'AIIB', 315, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (326, 1, 99, 'DNI', '47046048', 'SERGIO EDGAR', 'HUAYNASI', 'CONDORI', 'PERUANA', '1988-04-26', '931254205', 'sergi1654@gmail.com', NULL, '0', NULL, '8', '89', '798', 'ASC SANTA MONICA MZ F LT 10', 'WILBERT HUAINASI', '935842942', 'HERMANO', NULL, NULL, NULL, NULL, 'VM47046048', 'B-IIc', 11, 0, 'fotos/conductores/conductor_68c3718caa0254.98244953.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (327, 1, 106, 'DNI', '30425596', 'JOSE ROGER', 'LARA', 'FLORES', 'PERUANA', '1977-11-19', '958329434', 'joserlf19@hotmail.com', NULL, '0', NULL, '8', '89', '792', 'Av argentina 137 alto Selva Alegre', 'Victoria Doris Zarate Delgado', '920536470', 'ESPOSA', NULL, NULL, NULL, NULL, 'H30425596', 'AIIIB', 316, 0, 'fotos/conductores/conductor_68c36f36ccaaa8.51976560.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (328, 1, 105, 'DNI', '71978313', 'YOE MARCO', 'VALDIVIA', 'ANCO', 'PERUANO', '1997-12-06', '925676625', 'joevaldivia83@gmail.com', NULL, '0', NULL, '8', '89', '799', 'las esmeraldas ñ 18', 'madelene medina salas', '922216232', 'pareja', NULL, NULL, NULL, NULL, 'Q71978313', 'AIIB', 317, 0, 'fotos/conductores/conductor_68c37180521568.16933190.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (329, 1, 106, 'DNI', '42562329', 'DANIEL EDUARDO', 'RODRIGUEZ', 'GARCIA', 'Peruana', '1984-01-07', '953761729', 'daneroga@hotmail.com', NULL, '0', NULL, '8', '89', '794', 'Pasaje cesar Vallejo 104 San Jacinto ', 'Daniel Rodríguez García ', '953 761 729', 'Titular', NULL, NULL, NULL, NULL, 'H42562329', 'AIIB', 318, 0, 'fotos/conductores/conductor_68c37062805b74.20707199.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (330, 1, 105, 'DNI', '43070307', 'EYCOL DULAN', 'VELASQUEZ', 'MERINO', 'PERUANO', '1984-09-23', '982917931', 'WORKAHOLIC.AQP@GMAIL.COM', NULL, '0', 0, '8', '89', '792', 'AV JUSTICIA 208', 'LIZETH CASTRO', '968575697', 'CONYUGE', NULL, NULL, NULL, NULL, 'H43070307', 'AIIA', 319, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (331, 1, 105, 'DNI', '70991526', 'LUIS EDUARDO', 'RABANAL', 'RAMOS', 'PERUANO', '1995-09-09', '914419738', 'LRABANALR@GMAIL.COM', NULL, '0', NULL, '8', '89', '804', 'CALLE 6 ESTRELLAS MZ P LT 3', 'BRIGUITTE MAMANI', '956731326', 'PAREJA', NULL, NULL, NULL, NULL, 'H70991526', 'AIIB', 320, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (332, 1, 105, 'DNI', '29610319', 'YURI WALTER', 'MEJIA', 'TORRES', 'PERUANO', '1973-12-28', '960448510', 'yuwal28@hotmail.com', NULL, '0', NULL, '8', '89', '799', 'urb villa jabiru d-14', 'amalia manzanares medina', '969124724', 'esposa', NULL, NULL, NULL, NULL, 'h29610319', 'AIIB', 321, 0, 'fotos/conductores/conductor_68c37117ceb326.87664653.jpeg', 'fotos_perfil/perfil_29610319_1758282376.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (333, 1, 99, 'DNI', '41405968', 'ALEXANDER YAKU', 'VARGAS', 'HUANCA', 'PERUANA', '1982-05-16', '947272929', 'alexvargas2021@gmail.com', '$2y$12$ExNR//AMifqvwHJJvPY15OD4m1I.ZF47PYED7wptgP461x2wJF/py', '0', NULL, '8', '89', '793', 'URB CALIFORNIA AV AREQUIPA NRO 304 B ZON B', 'MARIA HUAMAN', '956614805', 'ESPOSA', NULL, NULL, NULL, NULL, 'H41405968', 'AIIA', 322, 0, 'fotos/conductores/conductor_68c370f00d0b18.60148586.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (334, 1, 99, 'DNI', '74226987', 'RENATO ANDRE', 'NAVARRO', 'VARGAS', 'PERUANA', '1996-02-18', '996705906', 'renatonavarrovargas1996@gmail.com', NULL, '0', NULL, '8', '89', '818', 'URB SEMI RURAL PACHACUTEC CALLE MIGUEL GRAU NRO 103', 'FÁTIMA HURTADO', '902195949', 'ESPOSA', NULL, NULL, NULL, NULL, 'H74226987', 'AIIB', 323, 0, 'fotos/conductores/conductor_68c36fc8741769.58183189.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (335, 1, 99, 'DNI', '70664292', 'ERICK MARTIN', 'ZAMORA', 'VARGAS', 'PERUANA', '1991-02-17', '965840711', 'erick.zamora.v@gmail.com', NULL, '0', NULL, '8', '89', '804', 'JESUS NAZARENO II MZ B LT 10', 'ANA ROMERO', '999291404', 'ESPOSA', NULL, NULL, NULL, NULL, 'D70664292', 'AIIB', 324, 0, 'fotos/conductores/conductor_68c37243a8e462.17832134.jpeg', 'fotos_perfil/perfil_70664292_1757699467.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (336, 1, 99, 'DNI', '44545306', 'FERNANDO JUNIOR', 'MACEDO', 'CASAPIA', 'PERUANA', '1987-09-14', '944334537', 'carewmeijer@gmail.com', '$2y$12$9siqpkVtHwxT0N9nh/WbHOwKzy58heixY398dN7xKwjek0tWs46xa', '0', NULL, '8', '89', '794', 'LOS PIONEROS MZ A LT 7 ALTO CAYMA', 'DEYSI TACO', '928924252', 'ESPOSA', NULL, NULL, NULL, NULL, 'H44545306', 'AIIB', 325, 0, 'fotos/conductores/conductor_68c37213204b75.64255304.jpeg', 'fotos_perfil/perfil_44545306_1758211981.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (337, 1, 109, 'DNI', '01544723', 'RIGOBERTO DANTE', 'CARI', 'CHECA', 'Peruana', '1968-11-23', '965484529', '', NULL, '0', NULL, '8', '89', '798', 'calle montevideo 204', 'darwin calcina', '982922764', 'SOBRINO', NULL, NULL, NULL, NULL, 'H01544723', 'AIIB', 326, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (338, 1, 82, 'DNI', '44930451', 'MAGALY MARGOT', 'MEDINA', 'DIAZ', 'PERUANA', '1988-03-13', '969744283', '', NULL, '0', NULL, '8', '89', '802', 'calle ricardo palma 307 edificaciones misti', 'WALTER ACILLO', '913171421', 'ESPOSO', NULL, NULL, NULL, NULL, 'H44930451', 'AIIB', 327, 1, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (339, 1, 106, 'DNI', '73450609', 'SIXTO RAUL', 'MENDIZABAL', 'OCSA', 'Peruana', '1994-08-07', '977130771', 'srmendizabalocsa@gmail.com', NULL, '0', NULL, '8', '89', '795', 'JR.MIGUEL GRAU 303 PUEBLO JOVEN ALTO LIBERTAD', 'Sixto Raúl Mendizabal', '977130771', 'Titular ', NULL, NULL, NULL, NULL, 'H73450609', 'AIIIC', 328, 0, 'fotos/conductores/conductor_68c371d0a508d5.99623785.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (340, 1, 106, 'DNI', '29590459', 'PERCY CESAR', 'GONZALES', 'ASIN', 'PERUANA', '1969-01-16', '923651627', 'Cesargonzalesasin@gmail.com', NULL, '0', NULL, '8', '89', '799', 'JUAN PABLO VIZCARDO Y GUZMAN H-20', 'Luz Gonzales', '959263885', 'Hermana', NULL, NULL, NULL, NULL, 'H29590459', 'AIIA', 329, 0, 'fotos/conductores/conductor_68c3714349dd54.41304666.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (341, 1, 82, 'DNI', '80265683', 'FERNANDO RICHARD', 'CRUZ', 'SOSA', 'PERUANO', '1978-06-10', '974001786', 'fernandocruzsosa@gmail.com', NULL, '0', NULL, '8', '89', '792', 'JUAN VVELAZC0 ALVARADO V-8 PAMPAS DE POLANCO ', 'ROSMERY CRUZ', '962705043', 'HERMANA', NULL, NULL, NULL, NULL, 'H80265683', 'AIIIC', 330, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (342, 1, 108, 'Carnet', '003190775', 'JEFRYN JOSE ', 'LEAÑEZ', 'GARCIA', 'VENEZOLANO', '1995-12-15', '918816353', 'jefryn230715@gmail.com', NULL, '0', NULL, '8', '89', '799', 'URB. ALTO DE LA LUNA IV ETAPA MZ C LT 18', 'Franlly Erika Rosana lizardo melean', '916212400', 'ESPOSA', NULL, NULL, NULL, NULL, 'H01003190775', 'B-IIb', 12, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (343, 1, 108, 'DNI', '49004053', 'HENRY KEIVI', 'CHAFLOQUE', 'ROMERO', 'PERUANO', '1997-03-18', '986543823', 'henrykeivi@gmail.com', '$2y$12$VZi5wFHckX9gH0/A.WlTheJb3GQka1Y6QoyN5AkfAFHEWFV6IvlB6', '0', NULL, '8', '89', '804', 'CALLE PINGLO 112', 'MONICA YESENIA BAUTISTA', '929323381', 'CONVIVIENTE ', NULL, NULL, NULL, NULL, '0001376219', 'B-IIb', 13, 0, 'fotos/conductores/conductor_68c370006f79e1.93396276.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (344, 1, 108, 'DNI', '76420519', 'CARLOS ALBERTO', 'DELGADO', 'BURGOS', 'PERUANO', '1996-11-07', '984498303', 'carlosbhurgos96@outlook.com', NULL, '0', NULL, '8', '89', '798', 'CALLE PEDRO DIAZ CANSECO 303 PPJJ SAN JUAN DE DIOS MZ 8 LT 14', 'KATIA GABRIEL CHOQUE MUÑOZ', '983750807', 'ENAMORADA', NULL, NULL, NULL, NULL, 'H095074', 'B-IIb', 14, 0, 'fotos/conductores/conductor_68c37276a6c491.75441649.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (345, 1, 97, 'DNI', '72080663', 'JUAN JHOEL', 'QUISPE', 'MAMANI', 'Peruana', '1996-05-06', '924326252', 'jhoel.qm123@gmail.com', NULL, '0', NULL, '8', '89', '793', 'ASOCIOCIACION CRUCE DE CHILINA MZJ LT11', 'leidy', '940185208', 'hermana', NULL, NULL, NULL, NULL, 'h72080663', 'AIIB', 332, 0, NULL, NULL, 0, 'LYAN000001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (346, 1, 105, 'DNI', '41249285', 'DARWIN FRANKS', 'CHALCO', 'MANSILLA', 'PERUANO', '1981-11-30', '958651042', 'darwinchalco81@gmail.com', NULL, '0', NULL, '8', '89', '795', 'valle blanco torre 11 104', 'ROSA CHOQUE', '982802910', 'ESPOSA', NULL, NULL, NULL, NULL, 'K41249285', 'AIIB', 333, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (347, 1, 82, 'DNI', '72566838', 'RONY BRANK', 'SUBIA', 'ALIAGA', 'PERUANA', '1997-01-25', '945159389', 'branco3225@gmail.com', NULL, '0', NULL, '8', '89', '804', 'URB.LUZ Y ALEGRIAMZ C2 LT 1', 'Beatriz Aliaga ', '946679083', 'MAMA', NULL, NULL, NULL, NULL, 'H72566838', 'AIIA', 334, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (348, 1, 110, 'DNI', '10266553', 'CESIBEL', 'SALAS', 'SANCHEZ', 'PERUANA', '1979-11-17', '981327830', 'CESIBEL30@HOTMAIL.COM', NULL, '0', NULL, '19', '173', '1609', 'ASOC LA CHIRA MZ A LT6 CEDROS DE VIÑA CHORRILLOS', 'LUCIANA ESPINOZA', '990883331', 'HIJA', NULL, NULL, NULL, NULL, 'Q10266553', 'AIIB', 2, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (349, 1, 77, 'Carnet', '4422585524', 'ALERK', 'LARICO', 'EYES', 'Venezolano', '1978-12-27', '946845454', 'charajarafdsd.caud@gmail.com', NULL, '445', NULL, '19', '173', '1615', 'av. jsfdijf', 'Leydy', '946545454', 'hermana', NULL, NULL, NULL, NULL, 'K442391696', 'AIIB', 10, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (350, 1, 97, 'DNI', '76609220', 'SAMUEL ANDRESS', 'CHARCA', 'QUISPE', 'Peruana', '2004-06-17', '961099917', 'charcaalejandro7@gmail.com', NULL, '0', 0, '8', '89', '794', 'villa continental mz r lt 8', 'freddy desa', '935330172', 'Tio', NULL, NULL, NULL, NULL, 'Q76609220', 'AIIB', 335, 0, NULL, NULL, 0, 'LYAN000001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (351, 1, 97, 'DNI', '29630796', 'JOE PAUL', 'CHARCA', 'MOLINA', 'PERUANA', '1975-02-01', '961112041', 'charcalejandro7@gmail.com', NULL, '0', 0, '8', '89', '794', 'villa continental mz r lt 8', 'sonia', 'quispe', 'esposa', NULL, NULL, NULL, NULL, 'H29630796', 'AIIIC', 336, 0, NULL, NULL, 0, 'LYAN000001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (352, 1, 104, 'Carnet', '006821198', 'carlos luis', 'segarra', 'guerrero', 'Venezolana', '1978-12-27', '904955110', 'csegarra2012@gmail.com', NULL, '0', NULL, '19', '173', '1968', 'mz a lt9 ah ', 'angelys', '992354345', 'hermana ', NULL, NULL, NULL, NULL, 'Q006821198', 'AIIB', 3, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (353, 1, 100, 'DNI', '29670558', 'JAVIER AMADOR', 'CHIRINOS', 'SARDI', 'PERUANO', '1976-06-21', '961747375', 'JAVCHISA@HOTMAIL.COM', NULL, '0', NULL, '8', '89', '795', 'URB. CERRO COLORADO  V -17', 'RUTH VALERIA ARCE PONCE', '963728506', 'ESPOSA', NULL, NULL, NULL, NULL, 'H29670558', 'AIIB', 337, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (354, 1, 104, 'Carnet', '003720136', 'ANIUSKA KARINA', 'RODRIGUEZ', 'MARQUEZ', 'venezolana', '1986-11-06', '910593885', 'anirodri638@gmail.com', NULL, '0', 0, '19', '173', '1636', 'av costanera 2438', 'ANA MERCEDESMARQUEZ', '933694215', 'MADRE', NULL, NULL, NULL, NULL, 'Q003720136', 'AIIA', 4, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (355, 1, 105, 'DNI', '42904616', 'LEONIDAS', 'BEJAR', 'CALLAÑAUPA', 'PERUANO', '1985-03-20', '958414151', 'leonidasbejar72@gmail.com', NULL, '0', NULL, '8', '89', '802', ' UPIS LA GALAXIA Mz J LT 9', 'juana turpo', '930242945', 'ESPOSA', NULL, NULL, NULL, NULL, 'H42904616', 'AIIA', 305, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (356, 1, 106, 'DNI', '43095128', 'JUAN CARLOS', 'CONDORIMAY', 'QUISPE', 'PERUANO', '1983-10-04', '927333570', 'tackllanquito@gmail.com', NULL, '0', NULL, '8', '89', '794', 'Santa Rosa de Lima H5 Alto Cayma', 'Silvestrina Quispe', '959501023', 'MADRE', NULL, NULL, NULL, NULL, 'H43095128', 'AIIIC', 338, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (357, 1, 105, 'DNI', '76293171', 'GEANFRANCO LEONARDO', 'MORA', 'TICONA', 'PERUANA', '1996-01-27', '918029581', 'canex15a@gmail.com', NULL, '0', 0, '8', '89', '802', 'urb carlos garcia ronceros BLOCK L dept 104', 'andrea martinez', '906246607', 'ESPOSA', NULL, NULL, NULL, NULL, 'ln76293171', 'B-IIb', 15, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (358, 1, 111, 'DNI', '76610693', 'Jose Rodrigo', 'Velazco', 'Zuñiga', 'Peruana', '2002-10-05', '935485429', 'velazcor84@gmail.com', '$2y$12$uvUvo5vvXz0meyoWeW.Nou6mE3Pm38PwrRWq.8Zr.nvF3xMcDOmmC', '0', NULL, '8', '92', '844', 'COSOS', 'Griselda Carlota  Zúñiga Cuba', '921 353 496', 'Madre', NULL, NULL, NULL, NULL, 'A-76610693', 'B-IIb', 16, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (359, 1, 77, 'DNI', '77425200', 'EMER RODRIGO', 'YARLEQUE', 'ZAPATA', 'YARLEQUE', '2004-01-17', '993321920', 'akame17ga20kill@gmail.com', '$2y$12$7c3f6t.3g8.49g7C1iktWe4n/vMIqegtla0M1sy7/wEePS/oUxu92', '0', 0, '19', '173', '1639', 'MZ A LT9A A.H ', 'emer', '9933321920', 'hermana ', NULL, NULL, NULL, NULL, 'Q00682119823', 'AIIB', 5, 0, 'fotos/conductores/conductor_691e36b5d19444.67354558.jpeg', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (360, 1, 111, 'DNI', '72693765', 'SERGIO ANDRES', 'SANCHEZ', 'ALCOS', 'Peruana ', '1997-05-18', '998332743', 'srg456@hotmail.com', NULL, '0', 0, '8', '89', '795', 'Residencial Los Alamos MZ.', 'Antonella Laura', '967304389', 'Pareja ', NULL, NULL, NULL, NULL, 'H0172693765', 'B-IIa', 17, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23 10:21:04', NULL);
INSERT INTO `clientes_conductores` VALUES (512, 2, NULL, 'DNI', '45399830', 'FRANKLIN', 'HUAMAN', 'ALVAREZ', 'PERUANO', '1987-04-15', '+51 929 449 945', 'FRANKY152020@GMAIL.COM', '$2y$12$rHWnWtagCglK0.g/dnRtruQnjuFwTOJ0YZsGhKPrOkY/OcLo4cNQS', '10', NULL, '8', '89', '804', 'CALLE AMAZONAS 119 ZN C ', '', '', '', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_6808018697503.pdf', 'public/clientesFiles/doc_identidad_680801869778f.pdf', NULL, '', '', '', '', '2025-04-22 16:52:22', '2025-05-20 21:06:08');
INSERT INTO `clientes_conductores` VALUES (513, 2, NULL, 'DNI', '47971388', 'FREDY', 'TEVES', 'AGUILAR', 'PERUANO', '1993-10-26', '+51 902026608', 'TEVES.AQP@GMAIL.COM', '$2y$10$bCUSDc84gPRBlI1Ye1S8muIHesSZvHke5idiDw863.sF/F7vlkosq', '28', NULL, '8', '89', '808', 'URB.NUEVA CAMPIÑA MZ - X , LT 10 SABANDIA ', '', '', '', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_68083410daf2d.pdf', 'public/clientesFiles/doc_identidad_68083410db083.pdf', NULL, '', '', '', '', '2025-04-22 20:28:00', '2025-10-11 15:24:34');
INSERT INTO `clientes_conductores` VALUES (514, 2, NULL, 'DNI', '42879435', 'HECTOR ALFONSO', 'CUENTAS', 'ARMENGOD', 'PERUANO', '1985-03-12', '918862855', 'axlhelios@gmail.com', '$2y$10$LhDXjBwQeEb1tNAPaC6mrOWeEQ7k5AYrTrbmnyyAK5y0oj/XG3QXm', '36', NULL, '8', '89', '814', 'urb. salaberry coronel del solar 414  ', '', '', '', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_6808376e013a3.pdf', 'public/clientesFiles/doc_identidad_6808376e015b5.pdf', NULL, '', '', '', '', '2025-04-22 20:42:22', '2025-07-23 13:22:45');
INSERT INTO `clientes_conductores` VALUES (515, 2, NULL, 'DNI', '71559833', 'KEVYN EDINHIO', 'CORNEJO', 'MORALES', 'PERUANO', '1997-03-04', '963713711', 'kevyncor@gmail.com', '$2y$12$N/B0ogOyZo83lKIXqUT.zuRmVLEqoFV0NvPSBUBrhvD9bY2lnOlLi', '4', NULL, '8', '89', '799', 'urb. santa catalina mz. p lt.6', '', '', '', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_6808397ab9a99.pdf', 'public/clientesFiles/doc_identidad_6808397ab9c1a.pdf', NULL, '', '', '', '', '2025-04-22 20:51:06', '2025-05-22 12:00:30');
INSERT INTO `clientes_conductores` VALUES (516, 2, NULL, 'DNI', '29481856', 'VICKY BEATRIZ', 'CHAMBI', 'HOLGUIN', 'PERUANO', '1974-11-13', '+51986148827', 'VICKYLINDA40@GMAIL.COM', '$2y$10$GGb6vvIg/R0IPD2ZNxR2suhg7sI4kdBC/fuIapnOfEN2H2YP6MUDa', '86', NULL, '8', '89', '804', 'URB AV SALAVERRY Q4  - MIRAFLORES ', 'VICTOR CAJO', '982938411', 'ESPOSO', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, '', '', NULL, '', '', '', '', '2025-05-14 13:58:36', '2025-07-23 13:22:45');
INSERT INTO `clientes_conductores` VALUES (517, 2, NULL, 'DNI', '72693626', 'DIANA LORENA', 'SANCHEZ', 'DIAZ', 'PERUANO', '1993-11-30', '936458574', 'lorena.diaz3028@gmail.com', '$2y$12$TD1PoLVunRp2alsQzmNZ7ub5GMyh.WsejNPbffF.i5q91Mij2rwru', '31', NULL, '8', '89', '799', 'urb villa jabiru a7 ', 'gian jesus ', '931724072', 'ESPOSO', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, '', '', NULL, '', '', '', '', '2025-05-15 14:18:24', NULL);
INSERT INTO `clientes_conductores` VALUES (518, 2, NULL, 'DNI', '45589803', 'NATALY MERCEDES', 'CANAZA', 'BELIZARIO', 'PERUANO', '1988-06-24', '+51 991 438 519', 'naty_lm24@hotmail', '$2y$12$dbF7jKGs.krQkXxioxFOhejlE8v95G9P6VLtKk5jOFksv2TIrwzSu', '14', NULL, '8', '89', '795', 'calle luna Zona 4 MZ 4 SEMI RURAL  ( PACHACUTEC )', 'HAMMERLY ', '940849853', 'ESPOSO', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, '', '', NULL, '', '', '', '', '2025-05-15 15:03:42', NULL);
INSERT INTO `clientes_conductores` VALUES (519, 2, NULL, 'DNI', '47606585', 'GIANCARLOS JESUS', 'RAMOS', 'LEZAMA', 'PERUANO', '1991-11-07', '931724072', 'gian_jesus@hotmail.com', '$2y$12$QWt2Hp/V/w/TejsuqwTn3uWN.Y3QCh9synjHLhcJFUAFl7eVxWm8C', '31', 0, '8', '89', '799', 'URB VILLA JABIRU A7 ', 'LORENA SANCHEZ ', '950308205', 'ESPOSA ', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, '', '', NULL, '', '', '', '', '2025-05-19 11:41:11', '2025-07-23 13:22:45');
INSERT INTO `clientes_conductores` VALUES (520, 2, NULL, 'Carnet de Extranjería', '007655917', 'BERNIE JAVIER', 'CEBALLOS', 'PERES', 'VENEZOLANA', '1993-03-06', '946365624', '', '$2y$10$6pRnxh1lSFSbFKimC6d8OehS75H3WaZg4FE4HAa2PbKEDz1u08Mc.', '', NULL, '8', '89', '793', 'AREQUIPA', 'DANIEL JOSE BELLO YENDI', '949599608', 'VENDEDOR', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, '', '', NULL, '', '', '', '', '2025-06-25 10:56:09', '2025-07-23 13:22:45');
INSERT INTO `clientes_conductores` VALUES (521, 2, NULL, 'DNI', '73362691', 'JAVIER MARCELO', 'DIAZ', 'SUBIA', 'PERUANO', '1996-02-08', '989213232', 'siegraim58@gmail.com', NULL, '58', NULL, '8', '89', '804', 'calle 3 volcanes 107 CUIDAD BLANCA ', '', '', '', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, '', '', NULL, '', '', '', 'PENDIENTE ENTREGAR LOS DOCUMENTES ', '2025-08-12 13:38:26', NULL);
INSERT INTO `clientes_conductores` VALUES (522, 2, NULL, 'Carnet de Extranjería', '006512242', 'EGNYS', 'JOUSEPH', 'HERNANDEZ', '', '1986-11-25', '923696983', '', NULL, '', 0, '8', '89', '814', 'AVENIDA URB LARA 1MZ F LOTE 1 URB LARA', '', '', '', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, '', '', NULL, '', '', '', '', '2025-08-26 21:37:05', '2025-08-26 21:45:16');
INSERT INTO `clientes_conductores` VALUES (523, 2, NULL, 'DNI', '29550826', 'JOSE GAVINO', 'FLORES', 'COAGUILA', 'PERUANO', '1966-10-25', '936252655', 'gavino.66@gmail.com', NULL, '', NULL, '8', '89', '799', 'calle andres razuri 214 urb.13 de enero', 'HIJO', '982945619', 'HIJO', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, '', '', NULL, '', '', '', '', '2025-08-27 16:00:55', NULL);
INSERT INTO `clientes_conductores` VALUES (524, 2, NULL, 'DNI', '70462464', 'ROBERT ALONSO', 'CORNEJO', 'LUNA', 'PERUANO', '1991-02-22', '959727405', '', NULL, '', NULL, '8', '89', '799', 'URB.BANCARIOS H-14', 'COORPORATIVO', '982920717', 'COORPORATIVO', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, '', '', NULL, '', '', '', '', '2025-08-27 16:14:23', NULL);
INSERT INTO `clientes_conductores` VALUES (525, 2, NULL, 'DNI', '74203312', 'PAUL DEYBIS', 'HUANCOLLO', 'HUMPIRE', 'PERUANO', '1985-04-05', '902789141', '', NULL, '', NULL, '8', '89', '801', 'CALLE TUPAC AMARU 111 PP.JJ ATALAYA', 'COORPORATIVO', '982901380', 'COORPORATIVO', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, '', '', NULL, '', '', '', '', '2025-08-27 16:42:31', NULL);
INSERT INTO `clientes_conductores` VALUES (526, 2, NULL, 'DNI', '70580837', 'ANTONY STEVE', 'CARLOS', 'MAMANI', 'PERUANO', '1985-12-01', '982902169', '', '$2y$12$R7jZha9AwvpahtaYgEmbU.ZEsIshql2Judrmtv.8tP.5nwzwEcF2u', '', NULL, '8', '93', '870', 'PARCELA 97 QUINTO RAMAL E7 IRRIGACIONES MAJES', '', '', '', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, '', '', NULL, '', '', '', '', '2025-08-27 17:09:31', NULL);
INSERT INTO `clientes_conductores` VALUES (527, 2, NULL, 'DNI', '45751372', 'carlos enrique ', 'chambi', 'ccahuari', 'peruana', '1988-07-29', '942149113', 'charajara.caud@gmail.com', NULL, '', NULL, '8', '89', '793', 'villa la pradera mz l lt 3 miraflores ', 'maria elena', '959538383', 'esposa', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, '', '', NULL, '', '', '', '', '2025-09-02 10:10:28', NULL);
INSERT INTO `clientes_conductores` VALUES (528, 2, NULL, 'DNI', '47860234', 'ALEJANDRO PORFIDIO', 'VALDIVIA', 'VALDIVIA', 'peruana', '1993-06-02', '973290492', 'Janoalejo128@gmail.com', NULL, '', NULL, '8', '89', '804', 'av amauta 304 paucarpata ', 'maria del carmen', '946683766', 'esposa', 'giancarlos ramos', '931724072', 'gerente', 'arequipa go', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, '', '', NULL, '', '', '', 'trabajador de arequipa go ', '2025-09-11 11:45:28', NULL);
INSERT INTO `clientes_conductores` VALUES (529, 2, NULL, 'DNI', '40285714', 'WILBER ANGEIL', 'RUIZ', 'SALCEDO', 'PERUANA', '1986-12-02', '982909540', 'ruiz_wilber@gmail.com', NULL, '', 0, '8', '89', '794', 'Urb la rocas d12 ', 'WILBER RUIZ', '982909540', 'Otro', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_68d6e586bb8e4.png', '', NULL, '', '', '', '', '2025-09-26 14:12:06', NULL);
INSERT INTO `clientes_conductores` VALUES (530, 2, NULL, 'DNI', '45412922', 'LUIS ALBERTO', 'QUISPE', 'MAMANI', 'PERUANA', '1989-02-25', '940280031', 'quispe_mamani@hotmail.com', NULL, '', 0, '8', '89', '792', 'urb alto de la luna MZ G lt 24 jlbyr', 'LUIS ALBERTO', '940280031', 'Otro', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_68d6e94f4e5f9.png', '', NULL, '', '', '', '', '2025-09-26 14:28:15', NULL);
INSERT INTO `clientes_conductores` VALUES (531, 2, NULL, 'DNI', '43012900', 'ANDRE', 'VIVAR', 'ORBEGOZO', 'peruana ', '1985-05-06', '938270727', 'andre.vivar2@gmail.com', NULL, '', 0, '8', '89', '794', 'Av.Aviacion 208 P.joven Francisco Bolognesi', 'Yaquelin', '940013282', 'Hermano/a', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_68dee9c169b00.jpg', 'public/clientesFiles/doc_identidad_68dee9c169edd.jpg', NULL, '', '', '', '', '2025-10-02 16:08:17', NULL);
INSERT INTO `clientes_conductores` VALUES (532, 2, NULL, 'DNI', '42728921', 'VICTOR', 'MONASTERIO', 'CCOSI', 'PERUANO', '1980-05-30', '930535716', 'monasteriovictor99@gmail.com', NULL, '', NULL, '8', '89', '801', 'CALLE NICOLAS DE PIEROLA 813', 'Rosalin Huayhua Huayhua', '930980376', 'Esposo/a', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_68e5940e426a7.pdf', 'public/clientesFiles/doc_identidad_68e5940e42d7a.pdf', NULL, 'public/clientesFiles/otro_doc_1_68e5940e43a43.pdf', 'public/clientesFiles/otro_doc_2_68e5940e440b8.pdf', 'public/clientesFiles/otro_doc_3_68e5940e444a7.pdf', '', '2025-10-07 17:28:30', NULL);
INSERT INTO `clientes_conductores` VALUES (533, 2, NULL, 'DNI', '46584978', 'LESLIE ELIZABETH', 'MACHACA', 'FLORES', 'PERUANA', '1990-10-02', '965476932', 'limf416@gmail.com', NULL, '', NULL, '8', '89', '799', 'calle colombia 728 ', 'elizabet flores', '965476932', 'Madre', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_68e91f7a113d6.jpeg', '', 'public/clientesFiles/selfie_68e91f7a1176b.jpeg', '', '', '', '', '2025-10-10 10:00:10', NULL);
INSERT INTO `clientes_conductores` VALUES (534, 2, NULL, 'DNI', '77143373', 'MARIA FERNANDA', 'YUCRA', 'LOPEZ', 'PERUANA', '2001-04-04', '944053229', 'fernandayucralopez04@gmail.com', NULL, '', NULL, '8', '89', '804', 'mariscal sucre 104 ampliacion paucarpata', 'Juan Carlos', '956299109', 'Esposo/a', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_68e921f3bd96c.jpeg', '', 'public/clientesFiles/selfie_68e921f3bdb72.jpeg', '', '', '', '', '2025-10-10 10:10:43', NULL);
INSERT INTO `clientes_conductores` VALUES (535, 2, NULL, 'DNI', '40177337', 'JAVIER EDUARDO', 'TALAVERA', 'MOTTA', 'PERUANA', '1978-05-04', '999984666', 'jtalaveramotta@gmail.com', NULL, '', NULL, '8', '89', '792', 'AV Argentina 116 alto selva alegre', 'Maidan Salas Yagua', '997400891', 'Esposo/a', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_68e931050d6fa.jpeg', 'public/clientesFiles/doc_identidad_68e931050dab9.pdf', 'public/clientesFiles/selfie_68e931050dbd9.jpeg', '', '', '', '', '2025-10-10 11:15:01', '2025-10-13 11:16:15');
INSERT INTO `clientes_conductores` VALUES (536, 2, NULL, 'DNI', '42796891', 'Dania Heimy', 'Copara', 'Padilla', 'PERUANA', '1982-02-26', '957095623', 'dcoparapadilla@gmail.com', NULL, '', NULL, '8', '89', '819', 'jr. colombia 202 pamapa de camarones   yanahura', 'mirihan teresa padilla diaz', '947053578', 'Madre', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_68eff6232194e.jpeg', '', 'public/clientesFiles/selfie_68eff62322a3d.jpeg', '', '', '', '', '2025-10-15 14:29:39', NULL);
INSERT INTO `clientes_conductores` VALUES (537, 2, NULL, 'DNI', '40676766', 'SANTIAGO', 'CCAPA', 'LAROTA', 'PERUANO', '1979-01-10', '997766534', 'ccapalarotasantiago@gmail.com', NULL, '', NULL, '8', '89', '802', 'upis heroes del pacifico mz Q lt 13', 'juana mamani rojas', '986249377', 'Esposo/a', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_68f6c087b1a8d.pdf', 'public/clientesFiles/doc_identidad_68f6c087b2305.pdf', 'public/clientesFiles/selfie_68f6c087b28d7.jpeg', 'public/clientesFiles/otro_doc_1_68f6c087b2a12.jpeg', 'public/clientesFiles/otro_doc_2_68f6c087b2ac2.pdf', 'public/clientesFiles/otro_doc_3_68f6c087b318d.pdf', '', '2025-10-20 18:06:47', NULL);
INSERT INTO `clientes_conductores` VALUES (538, 2, NULL, 'DNI', '47971550', 'ELVIS DAVID', 'CAYLLAHUA', 'MAMANI', 'PERUANA', '1993-09-10', '954129414', 'elvisdavidsalvatorre@gmail.com', NULL, '', NULL, '8', '89', '792', 'UPIS GARCILAZO DE LA VEGA MZ. A LT. 20', 'WILMA TICONA', '914689193', 'Esposo/a', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_68ffabf72bc2f.jpeg', 'public/clientesFiles/doc_identidad_68ffabf72c1be.pdf', 'public/clientesFiles/selfie_68ffabf72cc7f.jpeg', '', '', '', '', '2025-10-27 12:29:27', NULL);
INSERT INTO `clientes_conductores` VALUES (539, 2, NULL, 'DNI', '46206748', 'JOSE LUIS', 'BETANCUR', 'ACSARA', 'PERUANO', '1990-01-16', '969696459', 'jose_xjkl@hotmail.com', NULL, '', 0, '8', '89', '799', 'residencial villa aurora  h-3', 'carla fabiola rojas benavente', '928629554', 'Esposo/a', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_690150bda3ba4.pdf', 'public/clientesFiles/doc_identidad_690150bda450a.pdf', 'public/clientesFiles/selfie_690150bda4a4d.jpeg', 'public/clientesFiles/otro_doc_1_690150bda4bbe.pdf', 'public/clientesFiles/otro_doc_2_690150bda50a5.pdf', 'public/clientesFiles/otro_doc_3_690150bda57d0.pdf', '', '2025-10-28 18:24:45', NULL);
INSERT INTO `clientes_conductores` VALUES (540, 2, NULL, 'DNI', '75321040', 'MIJAEL LENNINS', 'TURPO', 'TURPO', 'PERUANA', '2003-05-06', '900007601', 'YAIETURPOTURPO@GMAIL.COM', NULL, '1', 0, '8', '89', '804', 'PP.JJ MIGUEL GRAU ZONA D ETAPA IV MZ LT. 2 ', 'MARTINA TURPO', '986999690', 'Madre', 'AREQUIPA GO ', '9885252952', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_690399f7d2763.jpeg', 'public/clientesFiles/doc_identidad_690399f7d2b27.jpeg', 'public/clientesFiles/selfie_690399f7d2c69.jpeg', '', '', '', '', '2025-10-30 12:01:43', NULL);
INSERT INTO `clientes_conductores` VALUES (541, 2, NULL, 'DNI', '47175951', 'LUZ MARINA', 'TURPO', 'TURPO', 'PERUANA', '1988-01-09', '925726854', 'LUZMARINATURPOTURP25@GMAIL.COM', NULL, '9', 0, '8', '89', '801', 'CUSCO MZ.H LT. 7 AMPLIACION JERUSALEN ', 'ALEXANDER  AROSQUIPA TURPO ', '901354857', 'Hijo/a', 'ALEXANDER  AROSQUIPA TURPO ', '901354857', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_69039c278cf83.jpeg', 'public/clientesFiles/doc_identidad_69039c278d1a5.jpeg', 'public/clientesFiles/selfie_69039c278d345.jpeg', '', '', '', '', '2025-10-30 12:11:03', NULL);
INSERT INTO `clientes_conductores` VALUES (542, 2, NULL, 'DNI', '06716504', 'WALTER ANDRES', 'CUBAS', 'VALERA', 'PERUANO', '1962-09-27', '944688015', '', NULL, '', NULL, '19', '173', '1635', 'JIRON FELIPE ARIAS 557', 'IRENE GALVEZ', '968619246', 'Esposo/a', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_6904ee2eae116.jpeg', '', 'public/clientesFiles/selfie_6904ee2eae3c8.jpeg', '', '', '', '', '2025-10-31 12:13:18', NULL);
INSERT INTO `clientes_conductores` VALUES (543, 2, NULL, 'Carnet de Extranjería', '06411421', 'GILBERTO ANTONIO', '-', 'MONTAÑO', 'VENEZOLANO', '1966-11-02', '917171317', 'GILBERTOMONTANO663@GMAIL.COM', NULL, '', 0, '8', '89', '802', 'MZ S4 LT 13 PAZ SOLDAN 620', 'Erika Ruiz', '922120225', 'Esposo/a', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_6909372512706.pdf', 'public/clientesFiles/doc_identidad_6909372513151.pdf', 'public/clientesFiles/selfie_6909372513c39.jpeg', '', '', '', '', '2025-11-03 17:13:41', '2025-11-05 14:05:19');
INSERT INTO `clientes_conductores` VALUES (544, 2, NULL, 'DNI', '46860785', 'VICTOR FERNANDO', 'TACO', 'PACHECO', 'PERUANA', '1991-10-17', '919528791', 'fer.nandosurf099@gmail.com', '$2y$12$lxA/2UrL9vK5dUe7uBFZCeNIYULaG1vF0jS7dEYJVG/geqT/Azf26', '', 0, '8', '89', '804', 'MALECON BALTA 202 15 DE AGOSTO PAUCARPATA', 'CONSUELO ESPERANZA MARTINEZ MACUADO', '918083617', 'Esposo/a', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_690a4e60bb3d4.jpg', 'public/clientesFiles/doc_identidad_690a4e60bb8e1.pdf', 'public/clientesFiles/selfie_690a4e60bc69f.jpg', '', '', '', '', '2025-11-04 13:05:04', NULL);
INSERT INTO `clientes_conductores` VALUES (545, 2, NULL, 'DNI', '47490530', 'jose carlos', 'arestegui', 'mollepaza', 'peruana', '1991-12-08', '941212509', 'jos.arest.d@gmail.com', NULL, '', 0, '8', '89', '794', 'calle tupac amaru 144 A', 'duani choquehuanca', '946704765', 'Esposo/a', 'arequipa go', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_690f72651ca6a.jpeg', 'public/clientesFiles/doc_identidad_690f72651cdd6.jpeg', 'public/clientesFiles/selfie_690f72651cfb3.jpeg', 'public/clientesFiles/otro_doc_1_690f72651d0c7.jpeg', '', '', '', '2025-11-08 10:40:05', NULL);
INSERT INTO `clientes_conductores` VALUES (546, 2, NULL, 'DNI', '43208793', 'ALAN WILFREDO', 'LANZA', 'SUCNO', 'PERUANO', '1984-04-28', '992639278', 'Wilfredro.lanza.sucno@gmail.com', NULL, '', 0, '8', '89', '798', 'CALLE URUGUAY 311 JACOBO HUNTER', 'LESLIE RAMOS', '921327363', 'Esposo/a', '', '', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_691227174a485.pdf', '', 'public/clientesFiles/selfie_691227174b1ed.jpeg', '', '', '', '', '2025-11-10 11:55:35', NULL);
INSERT INTO `clientes_conductores` VALUES (547, 2, NULL, 'DNI', '29443124', 'MOISES FAUSTO', 'ZAPANA', 'CHARRES', 'PERUANA', '1958-02-14', '932037635', 'alessandra_2488@hotmail.com', '$2y$12$eQ9dRMECFgbAfjvKEuU3je4G02wSXFizrg150FxZI9iPCh3ssMw.6', '13', 0, '8', '89', '799', 'APIS LAS ESMERALDAS ZON C MZ.O LT. 13 ', 'YESSICA ZAPANA ', '982972290', 'Hijo/a', 'AREQUIPA GO ', '9885252952', '', '', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_69136a49e9127.jpeg', 'public/clientesFiles/doc_identidad_69136a49e96c5.jpeg', 'public/clientesFiles/selfie_69136a49e9aa1.jpeg', '', '', '', '', '2025-11-11 10:54:33', NULL);
INSERT INTO `clientes_conductores` VALUES (548, 2, NULL, 'Carnet de Extranjería', '005411300', 'Jesus Rodrigo ', 'Riaño', 'Ereu', 'Venezuela ', '1999-05-31', '948230093', 'carolina392020@hotmail.com', NULL, '', 0, '8', '89', '795', 'Jirón Arequipa 106', 'Carolina Suárez ', '900094621', 'Madre', 'Taxi AQP GO', '993570000', 'Conductor ', 'Arequipa GO I.R.L', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'public/clientesFiles/recibo_servicios_691ce89f81504.jpg', 'public/clientesFiles/doc_identidad_691ce89f818de.jpg', 'public/clientesFiles/selfie_691ce89f84ba9.jpg', 'public/clientesFiles/otro_doc_1_691ce89f84f77.jpg', '', '', '', '2025-11-18 15:43:59', '2025-11-18 15:47:32');

-- ----------------------------
-- Table structure for departamentos
-- ----------------------------
DROP TABLE IF EXISTS `departamentos`;
CREATE TABLE `departamentos`  (
  `codigo` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nombre` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`codigo`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of departamentos
-- ----------------------------
INSERT INTO `departamentos` VALUES ('01', 'AMAZONAS');
INSERT INTO `departamentos` VALUES ('02', 'ANCASH');
INSERT INTO `departamentos` VALUES ('03', 'APURIMAC');
INSERT INTO `departamentos` VALUES ('04', 'AREQUIPA');
INSERT INTO `departamentos` VALUES ('05', 'AYACUCHO');
INSERT INTO `departamentos` VALUES ('06', 'CAJAMARCA');
INSERT INTO `departamentos` VALUES ('07', 'CALLAO');
INSERT INTO `departamentos` VALUES ('08', 'CUSCO');
INSERT INTO `departamentos` VALUES ('09', 'HUANCAVELICA');
INSERT INTO `departamentos` VALUES ('10', 'HUANUCO');
INSERT INTO `departamentos` VALUES ('11', 'ICA');
INSERT INTO `departamentos` VALUES ('12', 'JUNIN');
INSERT INTO `departamentos` VALUES ('13', 'LA LIBERTAD');
INSERT INTO `departamentos` VALUES ('14', 'LAMBAYEQUE');
INSERT INTO `departamentos` VALUES ('15', 'LIMA');
INSERT INTO `departamentos` VALUES ('16', 'LORETO');
INSERT INTO `departamentos` VALUES ('17', 'MADRE DE DIOS');
INSERT INTO `departamentos` VALUES ('18', 'MOQUEGUA');
INSERT INTO `departamentos` VALUES ('19', 'PASCO');
INSERT INTO `departamentos` VALUES ('20', 'PIURA');
INSERT INTO `departamentos` VALUES ('21', 'PUNO');
INSERT INTO `departamentos` VALUES ('22', 'SAN MARTIN');
INSERT INTO `departamentos` VALUES ('23', 'TACNA');
INSERT INTO `departamentos` VALUES ('24', 'TUMBES');
INSERT INTO `departamentos` VALUES ('25', 'UCAYALI');

-- ----------------------------
-- Table structure for distritos
-- ----------------------------
DROP TABLE IF EXISTS `distritos`;
CREATE TABLE `distritos`  (
  `codigo` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `codigo_provincia` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nombre` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`codigo`) USING BTREE,
  INDEX `codigo_provincia`(`codigo_provincia` ASC) USING BTREE,
  CONSTRAINT `distritos_ibfk_1` FOREIGN KEY (`codigo_provincia`) REFERENCES `provincias` (`codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of distritos
-- ----------------------------
INSERT INTO `distritos` VALUES ('010101', '0101', 'CHACHAPOYAS');
INSERT INTO `distritos` VALUES ('010102', '0101', 'ASUNCION');
INSERT INTO `distritos` VALUES ('010103', '0101', 'BALSAS');
INSERT INTO `distritos` VALUES ('010104', '0101', 'CHETO');
INSERT INTO `distritos` VALUES ('010105', '0101', 'CHILIQUIN');
INSERT INTO `distritos` VALUES ('010106', '0101', 'CHUQUIBAMBA');
INSERT INTO `distritos` VALUES ('010107', '0101', 'GRANADA');
INSERT INTO `distritos` VALUES ('010108', '0101', 'HUANCAS');
INSERT INTO `distritos` VALUES ('010109', '0101', 'LA JALCA');
INSERT INTO `distritos` VALUES ('010110', '0101', 'LEIMEBAMBA');
INSERT INTO `distritos` VALUES ('010111', '0101', 'LEVANTO');
INSERT INTO `distritos` VALUES ('010112', '0101', 'MAGDALENA');
INSERT INTO `distritos` VALUES ('010113', '0101', 'MARISCAL CASTILLA');
INSERT INTO `distritos` VALUES ('010114', '0101', 'MOLINOPAMPA');
INSERT INTO `distritos` VALUES ('010115', '0101', 'MONTEVIDEO');
INSERT INTO `distritos` VALUES ('010116', '0101', 'OLLEROS');
INSERT INTO `distritos` VALUES ('010117', '0101', 'QUINJALCA');
INSERT INTO `distritos` VALUES ('010118', '0101', 'SAN FRANCISCO DE DAGUAS');
INSERT INTO `distritos` VALUES ('010119', '0101', 'SAN ISIDRO DE MAINO');
INSERT INTO `distritos` VALUES ('010120', '0101', 'SOLOCO');
INSERT INTO `distritos` VALUES ('010121', '0101', 'SONCHE');
INSERT INTO `distritos` VALUES ('010201', '0102', 'BAGUA');
INSERT INTO `distritos` VALUES ('010202', '0102', 'ARAMANGO');
INSERT INTO `distritos` VALUES ('010203', '0102', 'COPALLIN');
INSERT INTO `distritos` VALUES ('010204', '0102', 'EL PARCO');
INSERT INTO `distritos` VALUES ('010205', '0102', 'IMAZA');
INSERT INTO `distritos` VALUES ('010206', '0102', 'LA PECA');
INSERT INTO `distritos` VALUES ('010301', '0103', 'JUMBILLA');
INSERT INTO `distritos` VALUES ('010302', '0103', 'CHISQUILLA');
INSERT INTO `distritos` VALUES ('010303', '0103', 'CHURUJA');
INSERT INTO `distritos` VALUES ('010304', '0103', 'COROSHA');
INSERT INTO `distritos` VALUES ('010305', '0103', 'CUISPES');
INSERT INTO `distritos` VALUES ('010306', '0103', 'FLORIDA');
INSERT INTO `distritos` VALUES ('010307', '0103', 'JAZÁN');
INSERT INTO `distritos` VALUES ('010308', '0103', 'RECTA');
INSERT INTO `distritos` VALUES ('010309', '0103', 'SAN CARLOS');
INSERT INTO `distritos` VALUES ('010310', '0103', 'SHIPASBAMBA');
INSERT INTO `distritos` VALUES ('010311', '0103', 'VALERA');
INSERT INTO `distritos` VALUES ('010312', '0103', 'YAMBRASBAMBA');
INSERT INTO `distritos` VALUES ('010401', '0104', 'NIEVA');
INSERT INTO `distritos` VALUES ('010402', '0104', 'EL CENEPA');
INSERT INTO `distritos` VALUES ('010403', '0104', 'RIO SANTIAGO');
INSERT INTO `distritos` VALUES ('010501', '0105', 'LAMUD');
INSERT INTO `distritos` VALUES ('010502', '0105', 'CAMPORREDONDO');
INSERT INTO `distritos` VALUES ('010503', '0105', 'COCABAMBA');
INSERT INTO `distritos` VALUES ('010504', '0105', 'COLCAMAR');
INSERT INTO `distritos` VALUES ('010505', '0105', 'CONILA');
INSERT INTO `distritos` VALUES ('010506', '0105', 'INGUILPATA');
INSERT INTO `distritos` VALUES ('010507', '0105', 'LONGUITA');
INSERT INTO `distritos` VALUES ('010508', '0105', 'LONYA CHICO');
INSERT INTO `distritos` VALUES ('010509', '0105', 'LUYA');
INSERT INTO `distritos` VALUES ('010510', '0105', 'LUYA VIEJO');
INSERT INTO `distritos` VALUES ('010511', '0105', 'MARIA');
INSERT INTO `distritos` VALUES ('010512', '0105', 'OCALLI');
INSERT INTO `distritos` VALUES ('010513', '0105', 'OCUMAL');
INSERT INTO `distritos` VALUES ('010514', '0105', 'PISUQUIA');
INSERT INTO `distritos` VALUES ('010515', '0105', 'PROVIDENCIA');
INSERT INTO `distritos` VALUES ('010516', '0105', 'SAN CRISTOBAL');
INSERT INTO `distritos` VALUES ('010517', '0105', 'SAN FRANCISCO DEL YESO');
INSERT INTO `distritos` VALUES ('010518', '0105', 'SAN JERONIMO');
INSERT INTO `distritos` VALUES ('010519', '0105', 'SAN JUAN DE LOPECANCHA');
INSERT INTO `distritos` VALUES ('010520', '0105', 'SANTA CATALINA');
INSERT INTO `distritos` VALUES ('010521', '0105', 'SANTO TOMAS');
INSERT INTO `distritos` VALUES ('010522', '0105', 'TINGO');
INSERT INTO `distritos` VALUES ('010523', '0105', 'TRITA');
INSERT INTO `distritos` VALUES ('010601', '0106', 'SAN NICOLAS');
INSERT INTO `distritos` VALUES ('010602', '0106', 'CHIRIMOTO');
INSERT INTO `distritos` VALUES ('010603', '0106', 'COCHAMAL');
INSERT INTO `distritos` VALUES ('010604', '0106', 'HUAMBO');
INSERT INTO `distritos` VALUES ('010605', '0106', 'LIMABAMBA');
INSERT INTO `distritos` VALUES ('010606', '0106', 'LONGAR');
INSERT INTO `distritos` VALUES ('010607', '0106', 'MARISCAL BENAVIDES');
INSERT INTO `distritos` VALUES ('010608', '0106', 'MILPUC');
INSERT INTO `distritos` VALUES ('010609', '0106', 'OMIA');
INSERT INTO `distritos` VALUES ('010610', '0106', 'SANTA ROSA');
INSERT INTO `distritos` VALUES ('010611', '0106', 'TOTORA');
INSERT INTO `distritos` VALUES ('010612', '0106', 'VISTA ALEGRE');
INSERT INTO `distritos` VALUES ('010701', '0107', 'BAGUA GRANDE');
INSERT INTO `distritos` VALUES ('010702', '0107', 'CAJARURO');
INSERT INTO `distritos` VALUES ('010703', '0107', 'CUMBA');
INSERT INTO `distritos` VALUES ('010704', '0107', 'EL MILAGRO');
INSERT INTO `distritos` VALUES ('010705', '0107', 'JAMALCA');
INSERT INTO `distritos` VALUES ('010706', '0107', 'LONYA GRANDE');
INSERT INTO `distritos` VALUES ('010707', '0107', 'YAMON');
INSERT INTO `distritos` VALUES ('020101', '0201', 'HUARAZ');
INSERT INTO `distritos` VALUES ('020102', '0201', 'COCHABAMBA');
INSERT INTO `distritos` VALUES ('020103', '0201', 'COLCABAMBA');
INSERT INTO `distritos` VALUES ('020104', '0201', 'HUANCHAY');
INSERT INTO `distritos` VALUES ('020105', '0201', 'INDEPENDENCIA');
INSERT INTO `distritos` VALUES ('020106', '0201', 'JANGAS');
INSERT INTO `distritos` VALUES ('020107', '0201', 'LA LIBERTAD');
INSERT INTO `distritos` VALUES ('020108', '0201', 'OLLEROS');
INSERT INTO `distritos` VALUES ('020109', '0201', 'PAMPAS');
INSERT INTO `distritos` VALUES ('020110', '0201', 'PARIACOTO');
INSERT INTO `distritos` VALUES ('020111', '0201', 'PIRA');
INSERT INTO `distritos` VALUES ('020112', '0201', 'TARICA');
INSERT INTO `distritos` VALUES ('020201', '0202', 'AIJA');
INSERT INTO `distritos` VALUES ('020202', '0202', 'CORIS');
INSERT INTO `distritos` VALUES ('020203', '0202', 'HUACLLAN');
INSERT INTO `distritos` VALUES ('020204', '0202', 'LA MERCED');
INSERT INTO `distritos` VALUES ('020205', '0202', 'SUCCHA');
INSERT INTO `distritos` VALUES ('020301', '0203', 'LLAMELLIN');
INSERT INTO `distritos` VALUES ('020302', '0203', 'ACZO');
INSERT INTO `distritos` VALUES ('020303', '0203', 'CHACCHO');
INSERT INTO `distritos` VALUES ('020304', '0203', 'CHINGAS');
INSERT INTO `distritos` VALUES ('020305', '0203', 'MIRGAS');
INSERT INTO `distritos` VALUES ('020306', '0203', 'SAN JUAN DE RONTOY');
INSERT INTO `distritos` VALUES ('020401', '0204', 'CHACAS');
INSERT INTO `distritos` VALUES ('020402', '0204', 'ACOCHACA');
INSERT INTO `distritos` VALUES ('020501', '0205', 'CHIQUIAN');
INSERT INTO `distritos` VALUES ('020502', '0205', 'ABELARDO PARDO LEZAMETA');
INSERT INTO `distritos` VALUES ('020503', '0205', 'ANTONIO RAYMONDI');
INSERT INTO `distritos` VALUES ('020504', '0205', 'AQUIA');
INSERT INTO `distritos` VALUES ('020505', '0205', 'CAJACAY');
INSERT INTO `distritos` VALUES ('020506', '0205', 'CANIS');
INSERT INTO `distritos` VALUES ('020507', '0205', 'COLQUIOC');
INSERT INTO `distritos` VALUES ('020508', '0205', 'HUALLANCA');
INSERT INTO `distritos` VALUES ('020509', '0205', 'HUASTA');
INSERT INTO `distritos` VALUES ('020510', '0205', 'HUAYLLACAYAN');
INSERT INTO `distritos` VALUES ('020511', '0205', 'LA PRIMAVERA');
INSERT INTO `distritos` VALUES ('020512', '0205', 'MANGAS');
INSERT INTO `distritos` VALUES ('020513', '0205', 'PACLLON');
INSERT INTO `distritos` VALUES ('020514', '0205', 'SAN MIGUEL DE CORPANQUI');
INSERT INTO `distritos` VALUES ('020515', '0205', 'TICLLOS');
INSERT INTO `distritos` VALUES ('020601', '0206', 'CARHUAZ');
INSERT INTO `distritos` VALUES ('020602', '0206', 'ACOPAMPA');
INSERT INTO `distritos` VALUES ('020603', '0206', 'AMASHCA');
INSERT INTO `distritos` VALUES ('020604', '0206', 'ANTA');
INSERT INTO `distritos` VALUES ('020605', '0206', 'ATAQUERO');
INSERT INTO `distritos` VALUES ('020606', '0206', 'MARCARA');
INSERT INTO `distritos` VALUES ('020607', '0206', 'PARIAHUANCA');
INSERT INTO `distritos` VALUES ('020608', '0206', 'SAN MIGUEL DE ACO');
INSERT INTO `distritos` VALUES ('020609', '0206', 'SHILLA');
INSERT INTO `distritos` VALUES ('020610', '0206', 'TINCO');
INSERT INTO `distritos` VALUES ('020611', '0206', 'YUNGAR');
INSERT INTO `distritos` VALUES ('020701', '0207', 'SAN LUIS');
INSERT INTO `distritos` VALUES ('020702', '0207', 'SAN NICOLAS');
INSERT INTO `distritos` VALUES ('020703', '0207', 'YAUYA');
INSERT INTO `distritos` VALUES ('020801', '0208', 'CASMA');
INSERT INTO `distritos` VALUES ('020802', '0208', 'BUENA VISTA ALTA');
INSERT INTO `distritos` VALUES ('020803', '0208', 'COMANDANTE NOEL');
INSERT INTO `distritos` VALUES ('020804', '0208', 'YAUTAN');
INSERT INTO `distritos` VALUES ('020901', '0209', 'CORONGO');
INSERT INTO `distritos` VALUES ('020902', '0209', 'ACO');
INSERT INTO `distritos` VALUES ('020903', '0209', 'BAMBAS');
INSERT INTO `distritos` VALUES ('020904', '0209', 'CUSCA');
INSERT INTO `distritos` VALUES ('020905', '0209', 'LA PAMPA');
INSERT INTO `distritos` VALUES ('020906', '0209', 'YANAC');
INSERT INTO `distritos` VALUES ('020907', '0209', 'YUPAN');
INSERT INTO `distritos` VALUES ('021001', '0210', 'HUARI');
INSERT INTO `distritos` VALUES ('021002', '0210', 'ANRA');
INSERT INTO `distritos` VALUES ('021003', '0210', 'CAJAY');
INSERT INTO `distritos` VALUES ('021004', '0210', 'CHAVIN DE HUANTAR');
INSERT INTO `distritos` VALUES ('021005', '0210', 'HUACACHI');
INSERT INTO `distritos` VALUES ('021006', '0210', 'HUACCHIS');
INSERT INTO `distritos` VALUES ('021007', '0210', 'HUACHIS');
INSERT INTO `distritos` VALUES ('021008', '0210', 'HUANTAR');
INSERT INTO `distritos` VALUES ('021009', '0210', 'MASIN');
INSERT INTO `distritos` VALUES ('021010', '0210', 'PAUCAS');
INSERT INTO `distritos` VALUES ('021011', '0210', 'PONTO');
INSERT INTO `distritos` VALUES ('021012', '0210', 'RAHUAPAMPA');
INSERT INTO `distritos` VALUES ('021013', '0210', 'RAPAYAN');
INSERT INTO `distritos` VALUES ('021014', '0210', 'SAN MARCOS');
INSERT INTO `distritos` VALUES ('021015', '0210', 'SAN PEDRO DE CHANA');
INSERT INTO `distritos` VALUES ('021016', '0210', 'UCO');
INSERT INTO `distritos` VALUES ('021101', '0211', 'HUARMEY');
INSERT INTO `distritos` VALUES ('021102', '0211', 'COCHAPETI');
INSERT INTO `distritos` VALUES ('021103', '0211', 'CULEBRAS');
INSERT INTO `distritos` VALUES ('021104', '0211', 'HUAYAN');
INSERT INTO `distritos` VALUES ('021105', '0211', 'MALVAS');
INSERT INTO `distritos` VALUES ('021201', '0212', 'CARAZ');
INSERT INTO `distritos` VALUES ('021202', '0212', 'HUALLANCA');
INSERT INTO `distritos` VALUES ('021203', '0212', 'HUATA');
INSERT INTO `distritos` VALUES ('021204', '0212', 'HUAYLAS');
INSERT INTO `distritos` VALUES ('021205', '0212', 'MATO');
INSERT INTO `distritos` VALUES ('021206', '0212', 'PAMPAROMAS');
INSERT INTO `distritos` VALUES ('021207', '0212', 'PUEBLO LIBRE');
INSERT INTO `distritos` VALUES ('021208', '0212', 'SANTA CRUZ');
INSERT INTO `distritos` VALUES ('021209', '0212', 'SANTO TORIBIO');
INSERT INTO `distritos` VALUES ('021210', '0212', 'YURACMARCA');
INSERT INTO `distritos` VALUES ('021301', '0213', 'PISCOBAMBA');
INSERT INTO `distritos` VALUES ('021302', '0213', 'CASCA');
INSERT INTO `distritos` VALUES ('021303', '0213', 'ELEAZAR GUZMAN BARRON');
INSERT INTO `distritos` VALUES ('021304', '0213', 'FIDEL OLIVAS ESCUDERO');
INSERT INTO `distritos` VALUES ('021305', '0213', 'LLAMA');
INSERT INTO `distritos` VALUES ('021306', '0213', 'LLUMPA');
INSERT INTO `distritos` VALUES ('021307', '0213', 'LUCMA');
INSERT INTO `distritos` VALUES ('021308', '0213', 'MUSGA');
INSERT INTO `distritos` VALUES ('021401', '0214', 'OCROS');
INSERT INTO `distritos` VALUES ('021402', '0214', 'ACAS');
INSERT INTO `distritos` VALUES ('021403', '0214', 'CAJAMARQUILLA');
INSERT INTO `distritos` VALUES ('021404', '0214', 'CARHUAPAMPA');
INSERT INTO `distritos` VALUES ('021405', '0214', 'COCHAS');
INSERT INTO `distritos` VALUES ('021406', '0214', 'CONGAS');
INSERT INTO `distritos` VALUES ('021407', '0214', 'LLIPA');
INSERT INTO `distritos` VALUES ('021408', '0214', 'SAN CRISTOBAL DE RAJAN');
INSERT INTO `distritos` VALUES ('021409', '0214', 'SAN PEDRO');
INSERT INTO `distritos` VALUES ('021410', '0214', 'SANTIAGO DE CHILCAS');
INSERT INTO `distritos` VALUES ('021501', '0215', 'CABANA');
INSERT INTO `distritos` VALUES ('021502', '0215', 'BOLOGNESI');
INSERT INTO `distritos` VALUES ('021503', '0215', 'CONCHUCOS');
INSERT INTO `distritos` VALUES ('021504', '0215', 'HUACASCHUQUE');
INSERT INTO `distritos` VALUES ('021505', '0215', 'HUANDOVAL');
INSERT INTO `distritos` VALUES ('021506', '0215', 'LACABAMBA');
INSERT INTO `distritos` VALUES ('021507', '0215', 'LLAPO');
INSERT INTO `distritos` VALUES ('021508', '0215', 'PALLASCA');
INSERT INTO `distritos` VALUES ('021509', '0215', 'PAMPAS');
INSERT INTO `distritos` VALUES ('021510', '0215', 'SANTA ROSA');
INSERT INTO `distritos` VALUES ('021511', '0215', 'TAUCA');
INSERT INTO `distritos` VALUES ('021601', '0216', 'POMABAMBA');
INSERT INTO `distritos` VALUES ('021602', '0216', 'HUAYLLAN');
INSERT INTO `distritos` VALUES ('021603', '0216', 'PAROBAMBA');
INSERT INTO `distritos` VALUES ('021604', '0216', 'QUINUABAMBA');
INSERT INTO `distritos` VALUES ('021701', '0217', 'RECUAY');
INSERT INTO `distritos` VALUES ('021702', '0217', 'CATAC');
INSERT INTO `distritos` VALUES ('021703', '0217', 'COTAPARACO');
INSERT INTO `distritos` VALUES ('021704', '0217', 'HUAYLLAPAMPA');
INSERT INTO `distritos` VALUES ('021705', '0217', 'LLACLLIN');
INSERT INTO `distritos` VALUES ('021706', '0217', 'MARCA');
INSERT INTO `distritos` VALUES ('021707', '0217', 'PAMPAS CHICO');
INSERT INTO `distritos` VALUES ('021708', '0217', 'PARARIN');
INSERT INTO `distritos` VALUES ('021709', '0217', 'TAPACOCHA');
INSERT INTO `distritos` VALUES ('021710', '0217', 'TICAPAMPA');
INSERT INTO `distritos` VALUES ('021801', '0218', 'CHIMBOTE');
INSERT INTO `distritos` VALUES ('021802', '0218', 'CACERES DEL PERU');
INSERT INTO `distritos` VALUES ('021803', '0218', 'COISHCO');
INSERT INTO `distritos` VALUES ('021804', '0218', 'MACATE');
INSERT INTO `distritos` VALUES ('021805', '0218', 'MORO');
INSERT INTO `distritos` VALUES ('021806', '0218', 'NEPEÑA');
INSERT INTO `distritos` VALUES ('021807', '0218', 'SAMANCO');
INSERT INTO `distritos` VALUES ('021808', '0218', 'SANTA');
INSERT INTO `distritos` VALUES ('021809', '0218', 'NUEVO CHIMBOTE');
INSERT INTO `distritos` VALUES ('021901', '0219', 'SIHUAS');
INSERT INTO `distritos` VALUES ('021902', '0219', 'ACOBAMBA');
INSERT INTO `distritos` VALUES ('021903', '0219', 'ALFONSO UGARTE');
INSERT INTO `distritos` VALUES ('021904', '0219', 'CASHAPAMPA');
INSERT INTO `distritos` VALUES ('021905', '0219', 'CHINGALPO');
INSERT INTO `distritos` VALUES ('021906', '0219', 'HUAYLLABAMBA');
INSERT INTO `distritos` VALUES ('021907', '0219', 'QUICHES');
INSERT INTO `distritos` VALUES ('021908', '0219', 'RAGASH');
INSERT INTO `distritos` VALUES ('021909', '0219', 'SAN JUAN');
INSERT INTO `distritos` VALUES ('021910', '0219', 'SICSIBAMBA');
INSERT INTO `distritos` VALUES ('022001', '0220', 'YUNGAY');
INSERT INTO `distritos` VALUES ('022002', '0220', 'CASCAPARA');
INSERT INTO `distritos` VALUES ('022003', '0220', 'MANCOS');
INSERT INTO `distritos` VALUES ('022004', '0220', 'MATACOTO');
INSERT INTO `distritos` VALUES ('022005', '0220', 'QUILLO');
INSERT INTO `distritos` VALUES ('022006', '0220', 'RANRAHIRCA');
INSERT INTO `distritos` VALUES ('022007', '0220', 'SHUPLUY');
INSERT INTO `distritos` VALUES ('022008', '0220', 'YANAMA');
INSERT INTO `distritos` VALUES ('030101', '0301', 'ABANCAY');
INSERT INTO `distritos` VALUES ('030102', '0301', 'CHACOCHE');
INSERT INTO `distritos` VALUES ('030103', '0301', 'CIRCA');
INSERT INTO `distritos` VALUES ('030104', '0301', 'CURAHUASI');
INSERT INTO `distritos` VALUES ('030105', '0301', 'HUANIPACA');
INSERT INTO `distritos` VALUES ('030106', '0301', 'LAMBRAMA');
INSERT INTO `distritos` VALUES ('030107', '0301', 'PICHIRHUA');
INSERT INTO `distritos` VALUES ('030108', '0301', 'SAN PEDRO DE CACHORA');
INSERT INTO `distritos` VALUES ('030109', '0301', 'TAMBURCO');
INSERT INTO `distritos` VALUES ('030201', '0302', 'ANDAHUAYLAS');
INSERT INTO `distritos` VALUES ('030202', '0302', 'ANDARAPA');
INSERT INTO `distritos` VALUES ('030203', '0302', 'CHIARA');
INSERT INTO `distritos` VALUES ('030204', '0302', 'HUANCARAMA');
INSERT INTO `distritos` VALUES ('030205', '0302', 'HUANCARAY');
INSERT INTO `distritos` VALUES ('030206', '0302', 'HUAYANA');
INSERT INTO `distritos` VALUES ('030207', '0302', 'KISHUARA');
INSERT INTO `distritos` VALUES ('030208', '0302', 'PACOBAMBA');
INSERT INTO `distritos` VALUES ('030209', '0302', 'PACUCHA');
INSERT INTO `distritos` VALUES ('030210', '0302', 'PAMPACHIRI');
INSERT INTO `distritos` VALUES ('030211', '0302', 'POMACOCHA');
INSERT INTO `distritos` VALUES ('030212', '0302', 'SAN ANTONIO DE CACHI');
INSERT INTO `distritos` VALUES ('030213', '0302', 'SAN JERONIMO');
INSERT INTO `distritos` VALUES ('030214', '0302', 'SAN MIGUEL DE CHACCRAMPA');
INSERT INTO `distritos` VALUES ('030215', '0302', 'SANTA MARIA DE CHICMO');
INSERT INTO `distritos` VALUES ('030216', '0302', 'TALAVERA');
INSERT INTO `distritos` VALUES ('030217', '0302', 'TUMAY HUARACA');
INSERT INTO `distritos` VALUES ('030218', '0302', 'TURPO');
INSERT INTO `distritos` VALUES ('030219', '0302', 'KAQUIABAMBA');
INSERT INTO `distritos` VALUES ('030301', '0303', 'ANTABAMBA');
INSERT INTO `distritos` VALUES ('030302', '0303', 'EL ORO');
INSERT INTO `distritos` VALUES ('030303', '0303', 'HUAQUIRCA');
INSERT INTO `distritos` VALUES ('030304', '0303', 'JUAN ESPINOZA MEDRANO');
INSERT INTO `distritos` VALUES ('030305', '0303', 'OROPESA');
INSERT INTO `distritos` VALUES ('030306', '0303', 'PACHACONAS');
INSERT INTO `distritos` VALUES ('030307', '0303', 'SABAINO');
INSERT INTO `distritos` VALUES ('030401', '0304', 'CHALHUANCA');
INSERT INTO `distritos` VALUES ('030402', '0304', 'CAPAYA');
INSERT INTO `distritos` VALUES ('030403', '0304', 'CARAYBAMBA');
INSERT INTO `distritos` VALUES ('030404', '0304', 'CHAPIMARCA');
INSERT INTO `distritos` VALUES ('030405', '0304', 'COLCABAMBA');
INSERT INTO `distritos` VALUES ('030406', '0304', 'COTARUSE');
INSERT INTO `distritos` VALUES ('030407', '0304', 'HUAYLLO');
INSERT INTO `distritos` VALUES ('030408', '0304', 'JUSTO APU SAHUARAURA');
INSERT INTO `distritos` VALUES ('030409', '0304', 'LUCRE');
INSERT INTO `distritos` VALUES ('030410', '0304', 'POCOHUANCA');
INSERT INTO `distritos` VALUES ('030411', '0304', 'SAN JUAN DE CHACÑA');
INSERT INTO `distritos` VALUES ('030412', '0304', 'SAÑAYCA');
INSERT INTO `distritos` VALUES ('030413', '0304', 'SORAYA');
INSERT INTO `distritos` VALUES ('030414', '0304', 'TAPAIRIHUA');
INSERT INTO `distritos` VALUES ('030415', '0304', 'TINTAY');
INSERT INTO `distritos` VALUES ('030416', '0304', 'TORAYA');
INSERT INTO `distritos` VALUES ('030417', '0304', 'YANACA');
INSERT INTO `distritos` VALUES ('030501', '0305', 'TAMBOBAMBA');
INSERT INTO `distritos` VALUES ('030502', '0305', 'COTABAMBAS');
INSERT INTO `distritos` VALUES ('030503', '0305', 'COYLLURQUI');
INSERT INTO `distritos` VALUES ('030504', '0305', 'HAQUIRA');
INSERT INTO `distritos` VALUES ('030505', '0305', 'MARA');
INSERT INTO `distritos` VALUES ('030506', '0305', 'CHALLHUAHUACHO');
INSERT INTO `distritos` VALUES ('030601', '0306', 'CHINCHEROS');
INSERT INTO `distritos` VALUES ('030602', '0306', 'ANCO-HUALLO');
INSERT INTO `distritos` VALUES ('030603', '0306', 'COCHARCAS');
INSERT INTO `distritos` VALUES ('030604', '0306', 'HUACCANA');
INSERT INTO `distritos` VALUES ('030605', '0306', 'OCOBAMBA');
INSERT INTO `distritos` VALUES ('030606', '0306', 'ONGOY');
INSERT INTO `distritos` VALUES ('030607', '0306', 'URANMARCA');
INSERT INTO `distritos` VALUES ('030608', '0306', 'RANRACANCHA');
INSERT INTO `distritos` VALUES ('030701', '0307', 'CHUQUIBAMBILLA');
INSERT INTO `distritos` VALUES ('030702', '0307', 'CURPAHUASI');
INSERT INTO `distritos` VALUES ('030703', '0307', 'GAMARRA');
INSERT INTO `distritos` VALUES ('030704', '0307', 'HUAYLLATI');
INSERT INTO `distritos` VALUES ('030705', '0307', 'MAMARA');
INSERT INTO `distritos` VALUES ('030706', '0307', 'MICAELA BASTIDAS');
INSERT INTO `distritos` VALUES ('030707', '0307', 'PATAYPAMPA');
INSERT INTO `distritos` VALUES ('030708', '0307', 'PROGRESO');
INSERT INTO `distritos` VALUES ('030709', '0307', 'SAN ANTONIO');
INSERT INTO `distritos` VALUES ('030710', '0307', 'SANTA ROSA');
INSERT INTO `distritos` VALUES ('030711', '0307', 'TURPAY');
INSERT INTO `distritos` VALUES ('030712', '0307', 'VILCABAMBA');
INSERT INTO `distritos` VALUES ('030713', '0307', 'VIRUNDO');
INSERT INTO `distritos` VALUES ('030714', '0307', 'CURASCO');
INSERT INTO `distritos` VALUES ('040101', '0401', 'AREQUIPA');
INSERT INTO `distritos` VALUES ('040102', '0401', 'ALTO SELVA ALEGRE');
INSERT INTO `distritos` VALUES ('040103', '0401', 'CAYMA');
INSERT INTO `distritos` VALUES ('040104', '0401', 'CERRO COLORADO');
INSERT INTO `distritos` VALUES ('040105', '0401', 'CHARACATO');
INSERT INTO `distritos` VALUES ('040106', '0401', 'CHIGUATA');
INSERT INTO `distritos` VALUES ('040107', '0401', 'JACOBO HUNTER');
INSERT INTO `distritos` VALUES ('040108', '0401', 'LA JOYA');
INSERT INTO `distritos` VALUES ('040109', '0401', 'MARIANO MELGAR');
INSERT INTO `distritos` VALUES ('040110', '0401', 'MIRAFLORES');
INSERT INTO `distritos` VALUES ('040111', '0401', 'MOLLEBAYA');
INSERT INTO `distritos` VALUES ('040112', '0401', 'PAUCARPATA');
INSERT INTO `distritos` VALUES ('040113', '0401', 'POCSI');
INSERT INTO `distritos` VALUES ('040114', '0401', 'POLOBAYA');
INSERT INTO `distritos` VALUES ('040115', '0401', 'QUEQUEÑA');
INSERT INTO `distritos` VALUES ('040116', '0401', 'SABANDIA');
INSERT INTO `distritos` VALUES ('040117', '0401', 'SACHACA');
INSERT INTO `distritos` VALUES ('040118', '0401', 'SAN JUAN DE SIGUAS');
INSERT INTO `distritos` VALUES ('040119', '0401', 'SAN JUAN DE TARUCANI');
INSERT INTO `distritos` VALUES ('040120', '0401', 'SANTA ISABEL DE SIGUAS');
INSERT INTO `distritos` VALUES ('040121', '0401', 'SANTA RITA DE SIGUAS');
INSERT INTO `distritos` VALUES ('040122', '0401', 'SOCABAYA');
INSERT INTO `distritos` VALUES ('040123', '0401', 'TIABAYA');
INSERT INTO `distritos` VALUES ('040124', '0401', 'UCHUMAYO');
INSERT INTO `distritos` VALUES ('040125', '0401', 'VITOR');
INSERT INTO `distritos` VALUES ('040126', '0401', 'YANAHUARA');
INSERT INTO `distritos` VALUES ('040127', '0401', 'YARABAMBA');
INSERT INTO `distritos` VALUES ('040128', '0401', 'YURA');
INSERT INTO `distritos` VALUES ('040129', '0401', 'JOSE LUIS BUSTAMANTE Y RIVERO');
INSERT INTO `distritos` VALUES ('040201', '0402', 'CAMANA');
INSERT INTO `distritos` VALUES ('040202', '0402', 'JOSE MARIA QUIMPER');
INSERT INTO `distritos` VALUES ('040203', '0402', 'MARIANO NICOLAS VALCARCEL');
INSERT INTO `distritos` VALUES ('040204', '0402', 'MARISCAL CACERES');
INSERT INTO `distritos` VALUES ('040205', '0402', 'NICOLAS DE PIEROLA');
INSERT INTO `distritos` VALUES ('040206', '0402', 'OCOÑA');
INSERT INTO `distritos` VALUES ('040207', '0402', 'QUILCA');
INSERT INTO `distritos` VALUES ('040208', '0402', 'SAMUEL PASTOR');
INSERT INTO `distritos` VALUES ('040301', '0403', 'CARAVELI');
INSERT INTO `distritos` VALUES ('040302', '0403', 'ACARI');
INSERT INTO `distritos` VALUES ('040303', '0403', 'ATICO');
INSERT INTO `distritos` VALUES ('040304', '0403', 'ATIQUIPA');
INSERT INTO `distritos` VALUES ('040305', '0403', 'BELLA UNION');
INSERT INTO `distritos` VALUES ('040306', '0403', 'CAHUACHO');
INSERT INTO `distritos` VALUES ('040307', '0403', 'CHALA');
INSERT INTO `distritos` VALUES ('040308', '0403', 'CHAPARRA');
INSERT INTO `distritos` VALUES ('040309', '0403', 'HUANUHUANU');
INSERT INTO `distritos` VALUES ('040310', '0403', 'JAQUI');
INSERT INTO `distritos` VALUES ('040311', '0403', 'LOMAS');
INSERT INTO `distritos` VALUES ('040312', '0403', 'QUICACHA');
INSERT INTO `distritos` VALUES ('040313', '0403', 'YAUCA');
INSERT INTO `distritos` VALUES ('040401', '0404', 'APLAO');
INSERT INTO `distritos` VALUES ('040402', '0404', 'ANDAGUA');
INSERT INTO `distritos` VALUES ('040403', '0404', 'AYO');
INSERT INTO `distritos` VALUES ('040404', '0404', 'CHACHAS');
INSERT INTO `distritos` VALUES ('040405', '0404', 'CHILCAYMARCA');
INSERT INTO `distritos` VALUES ('040406', '0404', 'CHOCO');
INSERT INTO `distritos` VALUES ('040407', '0404', 'HUANCARQUI');
INSERT INTO `distritos` VALUES ('040408', '0404', 'MACHAGUAY');
INSERT INTO `distritos` VALUES ('040409', '0404', 'ORCOPAMPA');
INSERT INTO `distritos` VALUES ('040410', '0404', 'PAMPACOLCA');
INSERT INTO `distritos` VALUES ('040411', '0404', 'TIPAN');
INSERT INTO `distritos` VALUES ('040412', '0404', 'UÑON');
INSERT INTO `distritos` VALUES ('040413', '0404', 'URACA');
INSERT INTO `distritos` VALUES ('040414', '0404', 'VIRACO');
INSERT INTO `distritos` VALUES ('040501', '0405', 'CHIVAY');
INSERT INTO `distritos` VALUES ('040502', '0405', 'ACHOMA');
INSERT INTO `distritos` VALUES ('040503', '0405', 'CABANACONDE');
INSERT INTO `distritos` VALUES ('040504', '0405', 'CALLALLI');
INSERT INTO `distritos` VALUES ('040505', '0405', 'CAYLLOMA');
INSERT INTO `distritos` VALUES ('040506', '0405', 'COPORAQUE');
INSERT INTO `distritos` VALUES ('040507', '0405', 'HUAMBO');
INSERT INTO `distritos` VALUES ('040508', '0405', 'HUANCA');
INSERT INTO `distritos` VALUES ('040509', '0405', 'ICHUPAMPA');
INSERT INTO `distritos` VALUES ('040510', '0405', 'LARI');
INSERT INTO `distritos` VALUES ('040511', '0405', 'LLUTA');
INSERT INTO `distritos` VALUES ('040512', '0405', 'MACA');
INSERT INTO `distritos` VALUES ('040513', '0405', 'MADRIGAL');
INSERT INTO `distritos` VALUES ('040514', '0405', 'SAN ANTONIO DE CHUCA');
INSERT INTO `distritos` VALUES ('040515', '0405', 'SIBAYO');
INSERT INTO `distritos` VALUES ('040516', '0405', 'TAPAY');
INSERT INTO `distritos` VALUES ('040517', '0405', 'TISCO');
INSERT INTO `distritos` VALUES ('040518', '0405', 'TUTI');
INSERT INTO `distritos` VALUES ('040519', '0405', 'YANQUE');
INSERT INTO `distritos` VALUES ('040520', '0405', 'MAJES');
INSERT INTO `distritos` VALUES ('040601', '0406', 'CHUQUIBAMBA');
INSERT INTO `distritos` VALUES ('040602', '0406', 'ANDARAY');
INSERT INTO `distritos` VALUES ('040603', '0406', 'CAYARANI');
INSERT INTO `distritos` VALUES ('040604', '0406', 'CHICHAS');
INSERT INTO `distritos` VALUES ('040605', '0406', 'IRAY');
INSERT INTO `distritos` VALUES ('040606', '0406', 'RIO GRANDE');
INSERT INTO `distritos` VALUES ('040607', '0406', 'SALAMANCA');
INSERT INTO `distritos` VALUES ('040608', '0406', 'YANAQUIHUA');
INSERT INTO `distritos` VALUES ('040701', '0407', 'MOLLENDO');
INSERT INTO `distritos` VALUES ('040702', '0407', 'COCACHACRA');
INSERT INTO `distritos` VALUES ('040703', '0407', 'DEAN VALDIVIA');
INSERT INTO `distritos` VALUES ('040704', '0407', 'ISLAY');
INSERT INTO `distritos` VALUES ('040705', '0407', 'MEJIA');
INSERT INTO `distritos` VALUES ('040706', '0407', 'PUNTA DE BOMBON');
INSERT INTO `distritos` VALUES ('040801', '0408', 'COTAHUASI');
INSERT INTO `distritos` VALUES ('040802', '0408', 'ALCA');
INSERT INTO `distritos` VALUES ('040803', '0408', 'CHARCANA');
INSERT INTO `distritos` VALUES ('040804', '0408', 'HUAYNACOTAS');
INSERT INTO `distritos` VALUES ('040805', '0408', 'PAMPAMARCA');
INSERT INTO `distritos` VALUES ('040806', '0408', 'PUYCA');
INSERT INTO `distritos` VALUES ('040807', '0408', 'QUECHUALLA');
INSERT INTO `distritos` VALUES ('040808', '0408', 'SAYLA');
INSERT INTO `distritos` VALUES ('040809', '0408', 'TAURIA');
INSERT INTO `distritos` VALUES ('040810', '0408', 'TOMEPAMPA');
INSERT INTO `distritos` VALUES ('040811', '0408', 'TORO');
INSERT INTO `distritos` VALUES ('050101', '0501', 'AYACUCHO');
INSERT INTO `distritos` VALUES ('050102', '0501', 'ACOCRO');
INSERT INTO `distritos` VALUES ('050103', '0501', 'ACOS VINCHOS');
INSERT INTO `distritos` VALUES ('050104', '0501', 'CARMEN ALTO');
INSERT INTO `distritos` VALUES ('050105', '0501', 'CHIARA');
INSERT INTO `distritos` VALUES ('050106', '0501', 'OCROS');
INSERT INTO `distritos` VALUES ('050107', '0501', 'PACAYCASA');
INSERT INTO `distritos` VALUES ('050108', '0501', 'QUINUA');
INSERT INTO `distritos` VALUES ('050109', '0501', 'SAN JOSE DE TICLLAS');
INSERT INTO `distritos` VALUES ('050110', '0501', 'SAN JUAN BAUTISTA');
INSERT INTO `distritos` VALUES ('050111', '0501', 'SANTIAGO DE PISCHA');
INSERT INTO `distritos` VALUES ('050112', '0501', 'SOCOS');
INSERT INTO `distritos` VALUES ('050113', '0501', 'TAMBILLO');
INSERT INTO `distritos` VALUES ('050114', '0501', 'VINCHOS');
INSERT INTO `distritos` VALUES ('050115', '0501', 'JESÚS NAZARENO');
INSERT INTO `distritos` VALUES ('050116', '0501', 'ANDRÉS AVELINO CÁCERES DORREGAY');
INSERT INTO `distritos` VALUES ('050201', '0502', 'CANGALLO');
INSERT INTO `distritos` VALUES ('050202', '0502', 'CHUSCHI');
INSERT INTO `distritos` VALUES ('050203', '0502', 'LOS MOROCHUCOS');
INSERT INTO `distritos` VALUES ('050204', '0502', 'MARIA PARADO DE BELLIDO');
INSERT INTO `distritos` VALUES ('050205', '0502', 'PARAS');
INSERT INTO `distritos` VALUES ('050206', '0502', 'TOTOS');
INSERT INTO `distritos` VALUES ('050301', '0503', 'SANCOS');
INSERT INTO `distritos` VALUES ('050302', '0503', 'CARAPO');
INSERT INTO `distritos` VALUES ('050303', '0503', 'SACSAMARCA');
INSERT INTO `distritos` VALUES ('050304', '0503', 'SANTIAGO DE LUCANAMARCA');
INSERT INTO `distritos` VALUES ('050401', '0504', 'HUANTA');
INSERT INTO `distritos` VALUES ('050402', '0504', 'AYAHUANCO');
INSERT INTO `distritos` VALUES ('050403', '0504', 'HUAMANGUILLA');
INSERT INTO `distritos` VALUES ('050404', '0504', 'IGUAIN');
INSERT INTO `distritos` VALUES ('050405', '0504', 'LURICOCHA');
INSERT INTO `distritos` VALUES ('050406', '0504', 'SANTILLANA');
INSERT INTO `distritos` VALUES ('050407', '0504', 'SIVIA');
INSERT INTO `distritos` VALUES ('050408', '0504', 'LLOCHEGUA');
INSERT INTO `distritos` VALUES ('050409', '0504', 'CANAYRE');
INSERT INTO `distritos` VALUES ('050410', '0504', 'UCHURACCAY');
INSERT INTO `distritos` VALUES ('050411', '0504', 'PUCACOLPA');
INSERT INTO `distritos` VALUES ('050501', '0505', 'SAN MIGUEL');
INSERT INTO `distritos` VALUES ('050502', '0505', 'ANCO');
INSERT INTO `distritos` VALUES ('050503', '0505', 'AYNA');
INSERT INTO `distritos` VALUES ('050504', '0505', 'CHILCAS');
INSERT INTO `distritos` VALUES ('050505', '0505', 'CHUNGUI');
INSERT INTO `distritos` VALUES ('050506', '0505', 'LUIS CARRANZA');
INSERT INTO `distritos` VALUES ('050507', '0505', 'SANTA ROSA');
INSERT INTO `distritos` VALUES ('050508', '0505', 'TAMBO');
INSERT INTO `distritos` VALUES ('050509', '0505', 'SAMUGARI');
INSERT INTO `distritos` VALUES ('050510', '0505', 'ANCHIHUAY');
INSERT INTO `distritos` VALUES ('050601', '0506', 'PUQUIO');
INSERT INTO `distritos` VALUES ('050602', '0506', 'AUCARA');
INSERT INTO `distritos` VALUES ('050603', '0506', 'CABANA');
INSERT INTO `distritos` VALUES ('050604', '0506', 'CARMEN SALCEDO');
INSERT INTO `distritos` VALUES ('050605', '0506', 'CHAVIÑA');
INSERT INTO `distritos` VALUES ('050606', '0506', 'CHIPAO');
INSERT INTO `distritos` VALUES ('050607', '0506', 'HUAC-HUAS');
INSERT INTO `distritos` VALUES ('050608', '0506', 'LARAMATE');
INSERT INTO `distritos` VALUES ('050609', '0506', 'LEONCIO PRADO');
INSERT INTO `distritos` VALUES ('050610', '0506', 'LLAUTA');
INSERT INTO `distritos` VALUES ('050611', '0506', 'LUCANAS');
INSERT INTO `distritos` VALUES ('050612', '0506', 'OCAÑA');
INSERT INTO `distritos` VALUES ('050613', '0506', 'OTOCA');
INSERT INTO `distritos` VALUES ('050614', '0506', 'SAISA');
INSERT INTO `distritos` VALUES ('050615', '0506', 'SAN CRISTOBAL');
INSERT INTO `distritos` VALUES ('050616', '0506', 'SAN JUAN');
INSERT INTO `distritos` VALUES ('050617', '0506', 'SAN PEDRO');
INSERT INTO `distritos` VALUES ('050618', '0506', 'SAN PEDRO DE PALCO');
INSERT INTO `distritos` VALUES ('050619', '0506', 'SANCOS');
INSERT INTO `distritos` VALUES ('050620', '0506', 'SANTA ANA DE HUAYCAHUACHO');
INSERT INTO `distritos` VALUES ('050621', '0506', 'SANTA LUCIA');
INSERT INTO `distritos` VALUES ('050701', '0507', 'CORACORA');
INSERT INTO `distritos` VALUES ('050702', '0507', 'CHUMPI');
INSERT INTO `distritos` VALUES ('050703', '0507', 'CORONEL CASTAÑEDA');
INSERT INTO `distritos` VALUES ('050704', '0507', 'PACAPAUSA');
INSERT INTO `distritos` VALUES ('050705', '0507', 'PULLO');
INSERT INTO `distritos` VALUES ('050706', '0507', 'PUYUSCA');
INSERT INTO `distritos` VALUES ('050707', '0507', 'SAN FRANCISCO DE RAVACAYCO');
INSERT INTO `distritos` VALUES ('050708', '0507', 'UPAHUACHO');
INSERT INTO `distritos` VALUES ('050801', '0508', 'PAUSA');
INSERT INTO `distritos` VALUES ('050802', '0508', 'COLTA');
INSERT INTO `distritos` VALUES ('050803', '0508', 'CORCULLA');
INSERT INTO `distritos` VALUES ('050804', '0508', 'LAMPA');
INSERT INTO `distritos` VALUES ('050805', '0508', 'MARCABAMBA');
INSERT INTO `distritos` VALUES ('050806', '0508', 'OYOLO');
INSERT INTO `distritos` VALUES ('050807', '0508', 'PARARCA');
INSERT INTO `distritos` VALUES ('050808', '0508', 'SAN JAVIER DE ALPABAMBA');
INSERT INTO `distritos` VALUES ('050809', '0508', 'SAN JOSE DE USHUA');
INSERT INTO `distritos` VALUES ('050810', '0508', 'SARA SARA');
INSERT INTO `distritos` VALUES ('050901', '0509', 'QUEROBAMBA');
INSERT INTO `distritos` VALUES ('050902', '0509', 'BELEN');
INSERT INTO `distritos` VALUES ('050903', '0509', 'CHALCOS');
INSERT INTO `distritos` VALUES ('050904', '0509', 'CHILCAYOC');
INSERT INTO `distritos` VALUES ('050905', '0509', 'HUACAÑA');
INSERT INTO `distritos` VALUES ('050906', '0509', 'MORCOLLA');
INSERT INTO `distritos` VALUES ('050907', '0509', 'PAICO');
INSERT INTO `distritos` VALUES ('050908', '0509', 'SAN PEDRO DE LARCAY');
INSERT INTO `distritos` VALUES ('050909', '0509', 'SAN SALVADOR DE QUIJE');
INSERT INTO `distritos` VALUES ('050910', '0509', 'SANTIAGO DE PAUCARAY');
INSERT INTO `distritos` VALUES ('050911', '0509', 'SORAS');
INSERT INTO `distritos` VALUES ('051001', '0510', 'HUANCAPI');
INSERT INTO `distritos` VALUES ('051002', '0510', 'ALCAMENCA');
INSERT INTO `distritos` VALUES ('051003', '0510', 'APONGO');
INSERT INTO `distritos` VALUES ('051004', '0510', 'ASQUIPATA');
INSERT INTO `distritos` VALUES ('051005', '0510', 'CANARIA');
INSERT INTO `distritos` VALUES ('051006', '0510', 'CAYARA');
INSERT INTO `distritos` VALUES ('051007', '0510', 'COLCA');
INSERT INTO `distritos` VALUES ('051008', '0510', 'HUAMANQUIQUIA');
INSERT INTO `distritos` VALUES ('051009', '0510', 'HUANCARAYLLA');
INSERT INTO `distritos` VALUES ('051010', '0510', 'HUAYA');
INSERT INTO `distritos` VALUES ('051011', '0510', 'SARHUA');
INSERT INTO `distritos` VALUES ('051012', '0510', 'VILCANCHOS');
INSERT INTO `distritos` VALUES ('051101', '0511', 'VILCAS HUAMAN');
INSERT INTO `distritos` VALUES ('051102', '0511', 'ACCOMARCA');
INSERT INTO `distritos` VALUES ('051103', '0511', 'CARHUANCA');
INSERT INTO `distritos` VALUES ('051104', '0511', 'CONCEPCION');
INSERT INTO `distritos` VALUES ('051105', '0511', 'HUAMBALPA');
INSERT INTO `distritos` VALUES ('051106', '0511', 'INDEPENDENCIA');
INSERT INTO `distritos` VALUES ('051107', '0511', 'SAURAMA');
INSERT INTO `distritos` VALUES ('051108', '0511', 'VISCHONGO');
INSERT INTO `distritos` VALUES ('060101', '0601', 'CAJAMARCA');
INSERT INTO `distritos` VALUES ('060102', '0601', 'ASUNCION');
INSERT INTO `distritos` VALUES ('060103', '0601', 'CHETILLA');
INSERT INTO `distritos` VALUES ('060104', '0601', 'COSPAN');
INSERT INTO `distritos` VALUES ('060105', '0601', 'ENCAÑADA');
INSERT INTO `distritos` VALUES ('060106', '0601', 'JESUS');
INSERT INTO `distritos` VALUES ('060107', '0601', 'LLACANORA');
INSERT INTO `distritos` VALUES ('060108', '0601', 'LOS BAÑOS DEL INCA');
INSERT INTO `distritos` VALUES ('060109', '0601', 'MAGDALENA');
INSERT INTO `distritos` VALUES ('060110', '0601', 'MATARA');
INSERT INTO `distritos` VALUES ('060111', '0601', 'NAMORA');
INSERT INTO `distritos` VALUES ('060112', '0601', 'SAN JUAN');
INSERT INTO `distritos` VALUES ('060201', '0602', 'CAJABAMBA');
INSERT INTO `distritos` VALUES ('060202', '0602', 'CACHACHI');
INSERT INTO `distritos` VALUES ('060203', '0602', 'CONDEBAMBA');
INSERT INTO `distritos` VALUES ('060204', '0602', 'SITACOCHA');
INSERT INTO `distritos` VALUES ('060301', '0603', 'CELENDIN');
INSERT INTO `distritos` VALUES ('060302', '0603', 'CHUMUCH');
INSERT INTO `distritos` VALUES ('060303', '0603', 'CORTEGANA');
INSERT INTO `distritos` VALUES ('060304', '0603', 'HUASMIN');
INSERT INTO `distritos` VALUES ('060305', '0603', 'JORGE CHAVEZ');
INSERT INTO `distritos` VALUES ('060306', '0603', 'JOSE GALVEZ');
INSERT INTO `distritos` VALUES ('060307', '0603', 'MIGUEL IGLESIAS');
INSERT INTO `distritos` VALUES ('060308', '0603', 'OXAMARCA');
INSERT INTO `distritos` VALUES ('060309', '0603', 'SOROCHUCO');
INSERT INTO `distritos` VALUES ('060310', '0603', 'SUCRE');
INSERT INTO `distritos` VALUES ('060311', '0603', 'UTCO');
INSERT INTO `distritos` VALUES ('060312', '0603', 'LA LIBERTAD DE PALLAN');
INSERT INTO `distritos` VALUES ('060401', '0604', 'CHOTA');
INSERT INTO `distritos` VALUES ('060402', '0604', 'ANGUIA');
INSERT INTO `distritos` VALUES ('060403', '0604', 'CHADIN');
INSERT INTO `distritos` VALUES ('060404', '0604', 'CHIGUIRIP');
INSERT INTO `distritos` VALUES ('060405', '0604', 'CHIMBAN');
INSERT INTO `distritos` VALUES ('060406', '0604', 'CHOROPAMPA');
INSERT INTO `distritos` VALUES ('060407', '0604', 'COCHABAMBA');
INSERT INTO `distritos` VALUES ('060408', '0604', 'CONCHAN');
INSERT INTO `distritos` VALUES ('060409', '0604', 'HUAMBOS');
INSERT INTO `distritos` VALUES ('060410', '0604', 'LAJAS');
INSERT INTO `distritos` VALUES ('060411', '0604', 'LLAMA');
INSERT INTO `distritos` VALUES ('060412', '0604', 'MIRACOSTA');
INSERT INTO `distritos` VALUES ('060413', '0604', 'PACCHA');
INSERT INTO `distritos` VALUES ('060414', '0604', 'PION');
INSERT INTO `distritos` VALUES ('060415', '0604', 'QUEROCOTO');
INSERT INTO `distritos` VALUES ('060416', '0604', 'SAN JUAN DE LICUPIS');
INSERT INTO `distritos` VALUES ('060417', '0604', 'TACABAMBA');
INSERT INTO `distritos` VALUES ('060418', '0604', 'TOCMOCHE');
INSERT INTO `distritos` VALUES ('060419', '0604', 'CHALAMARCA');
INSERT INTO `distritos` VALUES ('060501', '0605', 'CONTUMAZA');
INSERT INTO `distritos` VALUES ('060502', '0605', 'CHILETE');
INSERT INTO `distritos` VALUES ('060503', '0605', 'CUPISNIQUE');
INSERT INTO `distritos` VALUES ('060504', '0605', 'GUZMANGO');
INSERT INTO `distritos` VALUES ('060505', '0605', 'SAN BENITO');
INSERT INTO `distritos` VALUES ('060506', '0605', 'SANTA CRUZ DE TOLED');
INSERT INTO `distritos` VALUES ('060507', '0605', 'TANTARICA');
INSERT INTO `distritos` VALUES ('060508', '0605', 'YONAN');
INSERT INTO `distritos` VALUES ('060601', '0606', 'CUTERVO');
INSERT INTO `distritos` VALUES ('060602', '0606', 'CALLAYUC');
INSERT INTO `distritos` VALUES ('060603', '0606', 'CHOROS');
INSERT INTO `distritos` VALUES ('060604', '0606', 'CUJILLO');
INSERT INTO `distritos` VALUES ('060605', '0606', 'LA RAMADA');
INSERT INTO `distritos` VALUES ('060606', '0606', 'PIMPINGOS');
INSERT INTO `distritos` VALUES ('060607', '0606', 'QUEROCOTILLO');
INSERT INTO `distritos` VALUES ('060608', '0606', 'SAN ANDRES DE CUTERVO');
INSERT INTO `distritos` VALUES ('060609', '0606', 'SAN JUAN DE CUTERVO');
INSERT INTO `distritos` VALUES ('060610', '0606', 'SAN LUIS DE LUCMA');
INSERT INTO `distritos` VALUES ('060611', '0606', 'SANTA CRUZ');
INSERT INTO `distritos` VALUES ('060612', '0606', 'SANTO DOMINGO DE LA CAPILLA');
INSERT INTO `distritos` VALUES ('060613', '0606', 'SANTO TOMAS');
INSERT INTO `distritos` VALUES ('060614', '0606', 'SOCOTA');
INSERT INTO `distritos` VALUES ('060615', '0606', 'TORIBIO CASANOVA');
INSERT INTO `distritos` VALUES ('060701', '0607', 'BAMBAMARCA');
INSERT INTO `distritos` VALUES ('060702', '0607', 'CHUGUR');
INSERT INTO `distritos` VALUES ('060703', '0607', 'HUALGAYOC');
INSERT INTO `distritos` VALUES ('060801', '0608', 'JAEN');
INSERT INTO `distritos` VALUES ('060802', '0608', 'BELLAVISTA');
INSERT INTO `distritos` VALUES ('060803', '0608', 'CHONTALI');
INSERT INTO `distritos` VALUES ('060804', '0608', 'COLASAY');
INSERT INTO `distritos` VALUES ('060805', '0608', 'HUABAL');
INSERT INTO `distritos` VALUES ('060806', '0608', 'LAS PIRIAS');
INSERT INTO `distritos` VALUES ('060807', '0608', 'POMAHUACA');
INSERT INTO `distritos` VALUES ('060808', '0608', 'PUCARA');
INSERT INTO `distritos` VALUES ('060809', '0608', 'SALLIQUE');
INSERT INTO `distritos` VALUES ('060810', '0608', 'SAN FELIPE');
INSERT INTO `distritos` VALUES ('060811', '0608', 'SAN JOSE DEL ALTO');
INSERT INTO `distritos` VALUES ('060812', '0608', 'SANTA ROSA');
INSERT INTO `distritos` VALUES ('060901', '0609', 'SAN IGNACIO');
INSERT INTO `distritos` VALUES ('060902', '0609', 'CHIRINOS');
INSERT INTO `distritos` VALUES ('060903', '0609', 'HUARANGO');
INSERT INTO `distritos` VALUES ('060904', '0609', 'LA COIPA');
INSERT INTO `distritos` VALUES ('060905', '0609', 'NAMBALLE');
INSERT INTO `distritos` VALUES ('060906', '0609', 'SAN JOSE DE LOURDES');
INSERT INTO `distritos` VALUES ('060907', '0609', 'TABACONAS');
INSERT INTO `distritos` VALUES ('061001', '0610', 'PEDRO GALVEZ');
INSERT INTO `distritos` VALUES ('061002', '0610', 'CHANCAY');
INSERT INTO `distritos` VALUES ('061003', '0610', 'EDUARDO VILLANUEVA');
INSERT INTO `distritos` VALUES ('061004', '0610', 'GREGORIO PITA');
INSERT INTO `distritos` VALUES ('061005', '0610', 'ICHOCAN');
INSERT INTO `distritos` VALUES ('061006', '0610', 'JOSE MANUEL QUIROZ');
INSERT INTO `distritos` VALUES ('061007', '0610', 'JOSE SABOGAL');
INSERT INTO `distritos` VALUES ('061101', '0611', 'SAN MIGUEL');
INSERT INTO `distritos` VALUES ('061102', '0611', 'BOLIVAR');
INSERT INTO `distritos` VALUES ('061103', '0611', 'CALQUIS');
INSERT INTO `distritos` VALUES ('061104', '0611', 'CATILLUC');
INSERT INTO `distritos` VALUES ('061105', '0611', 'EL PRADO');
INSERT INTO `distritos` VALUES ('061106', '0611', 'LA FLORIDA');
INSERT INTO `distritos` VALUES ('061107', '0611', 'LLAPA');
INSERT INTO `distritos` VALUES ('061108', '0611', 'NANCHOC');
INSERT INTO `distritos` VALUES ('061109', '0611', 'NIEPOS');
INSERT INTO `distritos` VALUES ('061110', '0611', 'SAN GREGORIO');
INSERT INTO `distritos` VALUES ('061111', '0611', 'SAN SILVESTRE DE COCHAN');
INSERT INTO `distritos` VALUES ('061112', '0611', 'TONGOD');
INSERT INTO `distritos` VALUES ('061113', '0611', 'UNION AGUA BLANCA');
INSERT INTO `distritos` VALUES ('061201', '0612', 'SAN PABLO');
INSERT INTO `distritos` VALUES ('061202', '0612', 'SAN BERNARDINO');
INSERT INTO `distritos` VALUES ('061203', '0612', 'SAN LUIS');
INSERT INTO `distritos` VALUES ('061204', '0612', 'TUMBADEN');
INSERT INTO `distritos` VALUES ('061301', '0613', 'SANTA CRUZ');
INSERT INTO `distritos` VALUES ('061302', '0613', 'ANDABAMBA');
INSERT INTO `distritos` VALUES ('061303', '0613', 'CATACHE');
INSERT INTO `distritos` VALUES ('061304', '0613', 'CHANCAYBAÑOS');
INSERT INTO `distritos` VALUES ('061305', '0613', 'LA ESPERANZA');
INSERT INTO `distritos` VALUES ('061306', '0613', 'NINABAMBA');
INSERT INTO `distritos` VALUES ('061307', '0613', 'PULAN');
INSERT INTO `distritos` VALUES ('061308', '0613', 'SAUCEPAMPA');
INSERT INTO `distritos` VALUES ('061309', '0613', 'SEXI');
INSERT INTO `distritos` VALUES ('061310', '0613', 'UTICYACU');
INSERT INTO `distritos` VALUES ('061311', '0613', 'YAUYUCAN');
INSERT INTO `distritos` VALUES ('070101', '0701', 'CALLAO');
INSERT INTO `distritos` VALUES ('070102', '0701', 'BELLAVISTA');
INSERT INTO `distritos` VALUES ('070103', '0701', 'CARMEN DE LA LEGUA REYNOSO');
INSERT INTO `distritos` VALUES ('070104', '0701', 'LA PERLA');
INSERT INTO `distritos` VALUES ('070105', '0701', 'LA PUNTA');
INSERT INTO `distritos` VALUES ('070106', '0701', 'VENTANILLA');
INSERT INTO `distritos` VALUES ('070107', '0701', 'MI PERÚ');
INSERT INTO `distritos` VALUES ('080101', '0801', 'CUSCO');
INSERT INTO `distritos` VALUES ('080102', '0801', 'CCORCA');
INSERT INTO `distritos` VALUES ('080103', '0801', 'POROY');
INSERT INTO `distritos` VALUES ('080104', '0801', 'SAN JERONIMO');
INSERT INTO `distritos` VALUES ('080105', '0801', 'SAN SEBASTIAN');
INSERT INTO `distritos` VALUES ('080106', '0801', 'SANTIAGO');
INSERT INTO `distritos` VALUES ('080107', '0801', 'SAYLLA');
INSERT INTO `distritos` VALUES ('080108', '0801', 'WANCHAQ');
INSERT INTO `distritos` VALUES ('080201', '0802', 'ACOMAYO');
INSERT INTO `distritos` VALUES ('080202', '0802', 'ACOPIA');
INSERT INTO `distritos` VALUES ('080203', '0802', 'ACOS');
INSERT INTO `distritos` VALUES ('080204', '0802', 'MOSOC LLACTA');
INSERT INTO `distritos` VALUES ('080205', '0802', 'POMACANCHI');
INSERT INTO `distritos` VALUES ('080206', '0802', 'RONDOCAN');
INSERT INTO `distritos` VALUES ('080207', '0802', 'SANGARARA');
INSERT INTO `distritos` VALUES ('080301', '0803', 'ANTA');
INSERT INTO `distritos` VALUES ('080302', '0803', 'ANCAHUASI');
INSERT INTO `distritos` VALUES ('080303', '0803', 'CACHIMAYO');
INSERT INTO `distritos` VALUES ('080304', '0803', 'CHINCHAYPUJIO');
INSERT INTO `distritos` VALUES ('080305', '0803', 'HUAROCONDO');
INSERT INTO `distritos` VALUES ('080306', '0803', 'LIMATAMBO');
INSERT INTO `distritos` VALUES ('080307', '0803', 'MOLLEPATA');
INSERT INTO `distritos` VALUES ('080308', '0803', 'PUCYURA');
INSERT INTO `distritos` VALUES ('080309', '0803', 'ZURITE');
INSERT INTO `distritos` VALUES ('080401', '0804', 'CALCA');
INSERT INTO `distritos` VALUES ('080402', '0804', 'COYA');
INSERT INTO `distritos` VALUES ('080403', '0804', 'LAMAY');
INSERT INTO `distritos` VALUES ('080404', '0804', 'LARES');
INSERT INTO `distritos` VALUES ('080405', '0804', 'PISAC');
INSERT INTO `distritos` VALUES ('080406', '0804', 'SAN SALVADOR');
INSERT INTO `distritos` VALUES ('080407', '0804', 'TARAY');
INSERT INTO `distritos` VALUES ('080408', '0804', 'YANATILE');
INSERT INTO `distritos` VALUES ('080501', '0805', 'YANAOCA');
INSERT INTO `distritos` VALUES ('080502', '0805', 'CHECCA');
INSERT INTO `distritos` VALUES ('080503', '0805', 'KUNTURKANKI');
INSERT INTO `distritos` VALUES ('080504', '0805', 'LANGUI');
INSERT INTO `distritos` VALUES ('080505', '0805', 'LAYO');
INSERT INTO `distritos` VALUES ('080506', '0805', 'PAMPAMARCA');
INSERT INTO `distritos` VALUES ('080507', '0805', 'QUEHUE');
INSERT INTO `distritos` VALUES ('080508', '0805', 'TUPAC AMARU');
INSERT INTO `distritos` VALUES ('080601', '0806', 'SICUANI');
INSERT INTO `distritos` VALUES ('080602', '0806', 'CHECACUPE');
INSERT INTO `distritos` VALUES ('080603', '0806', 'COMBAPATA');
INSERT INTO `distritos` VALUES ('080604', '0806', 'MARANGANI');
INSERT INTO `distritos` VALUES ('080605', '0806', 'PITUMARCA');
INSERT INTO `distritos` VALUES ('080606', '0806', 'SAN PABLO');
INSERT INTO `distritos` VALUES ('080607', '0806', 'SAN PEDRO');
INSERT INTO `distritos` VALUES ('080608', '0806', 'TINTA');
INSERT INTO `distritos` VALUES ('080701', '0807', 'SANTO TOMAS');
INSERT INTO `distritos` VALUES ('080702', '0807', 'CAPACMARCA');
INSERT INTO `distritos` VALUES ('080703', '0807', 'CHAMACA');
INSERT INTO `distritos` VALUES ('080704', '0807', 'COLQUEMARCA');
INSERT INTO `distritos` VALUES ('080705', '0807', 'LIVITACA');
INSERT INTO `distritos` VALUES ('080706', '0807', 'LLUSCO');
INSERT INTO `distritos` VALUES ('080707', '0807', 'QUIÑOTA');
INSERT INTO `distritos` VALUES ('080708', '0807', 'VELILLE');
INSERT INTO `distritos` VALUES ('080801', '0808', 'ESPINAR');
INSERT INTO `distritos` VALUES ('080802', '0808', 'CONDOROMA');
INSERT INTO `distritos` VALUES ('080803', '0808', 'COPORAQUE');
INSERT INTO `distritos` VALUES ('080804', '0808', 'OCORURO');
INSERT INTO `distritos` VALUES ('080805', '0808', 'PALLPATA');
INSERT INTO `distritos` VALUES ('080806', '0808', 'PICHIGUA');
INSERT INTO `distritos` VALUES ('080807', '0808', 'SUYCKUTAMBO');
INSERT INTO `distritos` VALUES ('080808', '0808', 'ALTO PICHIGUA');
INSERT INTO `distritos` VALUES ('080901', '0809', 'SANTA ANA');
INSERT INTO `distritos` VALUES ('080902', '0809', 'ECHARATE');
INSERT INTO `distritos` VALUES ('080903', '0809', 'HUAYOPATA');
INSERT INTO `distritos` VALUES ('080904', '0809', 'MARANURA');
INSERT INTO `distritos` VALUES ('080905', '0809', 'OCOBAMBA');
INSERT INTO `distritos` VALUES ('080906', '0809', 'QUELLOUNO');
INSERT INTO `distritos` VALUES ('080907', '0809', 'KIMBIRI');
INSERT INTO `distritos` VALUES ('080908', '0809', 'SANTA TERESA');
INSERT INTO `distritos` VALUES ('080909', '0809', 'VILCABAMBA');
INSERT INTO `distritos` VALUES ('080910', '0809', 'PICHARI');
INSERT INTO `distritos` VALUES ('080911', '0809', 'INKAWASI');
INSERT INTO `distritos` VALUES ('080912', '0809', 'VILLA VIRGEN');
INSERT INTO `distritos` VALUES ('081001', '0810', 'PARURO');
INSERT INTO `distritos` VALUES ('081002', '0810', 'ACCHA');
INSERT INTO `distritos` VALUES ('081003', '0810', 'CCAPI');
INSERT INTO `distritos` VALUES ('081004', '0810', 'COLCHA');
INSERT INTO `distritos` VALUES ('081005', '0810', 'HUANOQUITE');
INSERT INTO `distritos` VALUES ('081006', '0810', 'OMACHA');
INSERT INTO `distritos` VALUES ('081007', '0810', 'PACCARITAMBO');
INSERT INTO `distritos` VALUES ('081008', '0810', 'PILLPINTO');
INSERT INTO `distritos` VALUES ('081009', '0810', 'YAURISQUE');
INSERT INTO `distritos` VALUES ('081101', '0811', 'PAUCARTAMBO');
INSERT INTO `distritos` VALUES ('081102', '0811', 'CAICAY');
INSERT INTO `distritos` VALUES ('081103', '0811', 'CHALLABAMBA');
INSERT INTO `distritos` VALUES ('081104', '0811', 'COLQUEPATA');
INSERT INTO `distritos` VALUES ('081105', '0811', 'HUANCARANI');
INSERT INTO `distritos` VALUES ('081106', '0811', 'KOSÑIPATA');
INSERT INTO `distritos` VALUES ('081201', '0812', 'URCOS');
INSERT INTO `distritos` VALUES ('081202', '0812', 'ANDAHUAYLILLAS');
INSERT INTO `distritos` VALUES ('081203', '0812', 'CAMANTI');
INSERT INTO `distritos` VALUES ('081204', '0812', 'CCARHUAYO');
INSERT INTO `distritos` VALUES ('081205', '0812', 'CCATCA');
INSERT INTO `distritos` VALUES ('081206', '0812', 'CUSIPATA');
INSERT INTO `distritos` VALUES ('081207', '0812', 'HUARO');
INSERT INTO `distritos` VALUES ('081208', '0812', 'LUCRE');
INSERT INTO `distritos` VALUES ('081209', '0812', 'MARCAPATA');
INSERT INTO `distritos` VALUES ('081210', '0812', 'OCONGATE');
INSERT INTO `distritos` VALUES ('081211', '0812', 'OROPESA');
INSERT INTO `distritos` VALUES ('081212', '0812', 'QUIQUIJANA');
INSERT INTO `distritos` VALUES ('081301', '0813', 'URUBAMBA');
INSERT INTO `distritos` VALUES ('081302', '0813', 'CHINCHERO');
INSERT INTO `distritos` VALUES ('081303', '0813', 'HUAYLLABAMBA');
INSERT INTO `distritos` VALUES ('081304', '0813', 'MACHUPICCHU');
INSERT INTO `distritos` VALUES ('081305', '0813', 'MARAS');
INSERT INTO `distritos` VALUES ('081306', '0813', 'OLLANTAYTAMBO');
INSERT INTO `distritos` VALUES ('081307', '0813', 'YUCAY');
INSERT INTO `distritos` VALUES ('090101', '0901', 'HUANCAVELICA');
INSERT INTO `distritos` VALUES ('090102', '0901', 'ACOBAMBILLA');
INSERT INTO `distritos` VALUES ('090103', '0901', 'ACORIA');
INSERT INTO `distritos` VALUES ('090104', '0901', 'CONAYCA');
INSERT INTO `distritos` VALUES ('090105', '0901', 'CUENCA');
INSERT INTO `distritos` VALUES ('090106', '0901', 'HUACHOCOLPA');
INSERT INTO `distritos` VALUES ('090107', '0901', 'HUAYLLAHUARA');
INSERT INTO `distritos` VALUES ('090108', '0901', 'IZCUCHACA');
INSERT INTO `distritos` VALUES ('090109', '0901', 'LARIA');
INSERT INTO `distritos` VALUES ('090110', '0901', 'MANTA');
INSERT INTO `distritos` VALUES ('090111', '0901', 'MARISCAL CACERES');
INSERT INTO `distritos` VALUES ('090112', '0901', 'MOYA');
INSERT INTO `distritos` VALUES ('090113', '0901', 'NUEVO OCCORO');
INSERT INTO `distritos` VALUES ('090114', '0901', 'PALCA');
INSERT INTO `distritos` VALUES ('090115', '0901', 'PILCHACA');
INSERT INTO `distritos` VALUES ('090116', '0901', 'VILCA');
INSERT INTO `distritos` VALUES ('090117', '0901', 'YAULI');
INSERT INTO `distritos` VALUES ('090118', '0901', 'ASCENSIÓN');
INSERT INTO `distritos` VALUES ('090119', '0901', 'HUANDO');
INSERT INTO `distritos` VALUES ('090201', '0902', 'ACOBAMBA');
INSERT INTO `distritos` VALUES ('090202', '0902', 'ANDABAMBA');
INSERT INTO `distritos` VALUES ('090203', '0902', 'ANTA');
INSERT INTO `distritos` VALUES ('090204', '0902', 'CAJA');
INSERT INTO `distritos` VALUES ('090205', '0902', 'MARCAS');
INSERT INTO `distritos` VALUES ('090206', '0902', 'PAUCARA');
INSERT INTO `distritos` VALUES ('090207', '0902', 'POMACOCHA');
INSERT INTO `distritos` VALUES ('090208', '0902', 'ROSARIO');
INSERT INTO `distritos` VALUES ('090301', '0903', 'LIRCAY');
INSERT INTO `distritos` VALUES ('090302', '0903', 'ANCHONGA');
INSERT INTO `distritos` VALUES ('090303', '0903', 'CALLANMARCA');
INSERT INTO `distritos` VALUES ('090304', '0903', 'CCOCHACCASA');
INSERT INTO `distritos` VALUES ('090305', '0903', 'CHINCHO');
INSERT INTO `distritos` VALUES ('090306', '0903', 'CONGALLA');
INSERT INTO `distritos` VALUES ('090307', '0903', 'HUANCA-HUANCA');
INSERT INTO `distritos` VALUES ('090308', '0903', 'HUAYLLAY GRANDE');
INSERT INTO `distritos` VALUES ('090309', '0903', 'JULCAMARCA');
INSERT INTO `distritos` VALUES ('090310', '0903', 'SAN ANTONIO DE ANTAPARCO');
INSERT INTO `distritos` VALUES ('090311', '0903', 'SANTO TOMAS DE PATA');
INSERT INTO `distritos` VALUES ('090312', '0903', 'SECCLLA');
INSERT INTO `distritos` VALUES ('090401', '0904', 'CASTROVIRREYNA');
INSERT INTO `distritos` VALUES ('090402', '0904', 'ARMA');
INSERT INTO `distritos` VALUES ('090403', '0904', 'AURAHUA');
INSERT INTO `distritos` VALUES ('090404', '0904', 'CAPILLAS');
INSERT INTO `distritos` VALUES ('090405', '0904', 'CHUPAMARCA');
INSERT INTO `distritos` VALUES ('090406', '0904', 'COCAS');
INSERT INTO `distritos` VALUES ('090407', '0904', 'HUACHOS');
INSERT INTO `distritos` VALUES ('090408', '0904', 'HUAMATAMBO');
INSERT INTO `distritos` VALUES ('090409', '0904', 'MOLLEPAMPA');
INSERT INTO `distritos` VALUES ('090410', '0904', 'SAN JUAN');
INSERT INTO `distritos` VALUES ('090411', '0904', 'SANTA ANA');
INSERT INTO `distritos` VALUES ('090412', '0904', 'TANTARA');
INSERT INTO `distritos` VALUES ('090413', '0904', 'TICRAPO');
INSERT INTO `distritos` VALUES ('090501', '0905', 'CHURCAMPA');
INSERT INTO `distritos` VALUES ('090502', '0905', 'ANCO');
INSERT INTO `distritos` VALUES ('090503', '0905', 'CHINCHIHUASI');
INSERT INTO `distritos` VALUES ('090504', '0905', 'EL CARMEN');
INSERT INTO `distritos` VALUES ('090505', '0905', 'LA MERCED');
INSERT INTO `distritos` VALUES ('090506', '0905', 'LOCROJA');
INSERT INTO `distritos` VALUES ('090507', '0905', 'PAUCARBAMBA');
INSERT INTO `distritos` VALUES ('090508', '0905', 'SAN MIGUEL DE MAYOCC');
INSERT INTO `distritos` VALUES ('090509', '0905', 'SAN PEDRO DE CORIS');
INSERT INTO `distritos` VALUES ('090510', '0905', 'PACHAMARCA');
INSERT INTO `distritos` VALUES ('090511', '0905', 'COSME');
INSERT INTO `distritos` VALUES ('090601', '0906', 'HUAYTARA');
INSERT INTO `distritos` VALUES ('090602', '0906', 'AYAVI');
INSERT INTO `distritos` VALUES ('090603', '0906', 'CORDOVA');
INSERT INTO `distritos` VALUES ('090604', '0906', 'HUAYACUNDO ARMA');
INSERT INTO `distritos` VALUES ('090605', '0906', 'LARAMARCA');
INSERT INTO `distritos` VALUES ('090606', '0906', 'OCOYO');
INSERT INTO `distritos` VALUES ('090607', '0906', 'PILPICHACA');
INSERT INTO `distritos` VALUES ('090608', '0906', 'QUERCO');
INSERT INTO `distritos` VALUES ('090609', '0906', 'QUITO-ARMA');
INSERT INTO `distritos` VALUES ('090610', '0906', 'SAN ANTONIO DE CUSICANCHA');
INSERT INTO `distritos` VALUES ('090611', '0906', 'SAN FRANCISCO DE SANGAYAICO');
INSERT INTO `distritos` VALUES ('090612', '0906', 'SAN ISIDRO');
INSERT INTO `distritos` VALUES ('090613', '0906', 'SANTIAGO DE CHOCORVOS');
INSERT INTO `distritos` VALUES ('090614', '0906', 'SANTIAGO DE QUIRAHUARA');
INSERT INTO `distritos` VALUES ('090615', '0906', 'SANTO DOMINGO DE CAPILLAS');
INSERT INTO `distritos` VALUES ('090616', '0906', 'TAMBO');
INSERT INTO `distritos` VALUES ('090701', '0907', 'PAMPAS');
INSERT INTO `distritos` VALUES ('090702', '0907', 'ACOSTAMBO');
INSERT INTO `distritos` VALUES ('090703', '0907', 'ACRAQUIA');
INSERT INTO `distritos` VALUES ('090704', '0907', 'AHUAYCHA');
INSERT INTO `distritos` VALUES ('090705', '0907', 'COLCABAMBA');
INSERT INTO `distritos` VALUES ('090706', '0907', 'DANIEL HERNANDEZ');
INSERT INTO `distritos` VALUES ('090707', '0907', 'HUACHOCOLPA');
INSERT INTO `distritos` VALUES ('090709', '0907', 'HUARIBAMBA');
INSERT INTO `distritos` VALUES ('090710', '0907', 'ÑAHUIMPUQUIO');
INSERT INTO `distritos` VALUES ('090711', '0907', 'PAZOS');
INSERT INTO `distritos` VALUES ('090713', '0907', 'QUISHUAR');
INSERT INTO `distritos` VALUES ('090714', '0907', 'SALCABAMBA');
INSERT INTO `distritos` VALUES ('090715', '0907', 'SALCAHUASI');
INSERT INTO `distritos` VALUES ('090716', '0907', 'SAN MARCOS DE ROCCHAC');
INSERT INTO `distritos` VALUES ('090717', '0907', 'SURCUBAMBA');
INSERT INTO `distritos` VALUES ('090718', '0907', 'TINTAY PUNCU');
INSERT INTO `distritos` VALUES ('100101', '1001', 'HUANUCO');
INSERT INTO `distritos` VALUES ('100102', '1001', 'AMARILIS');
INSERT INTO `distritos` VALUES ('100103', '1001', 'CHINCHAO');
INSERT INTO `distritos` VALUES ('100104', '1001', 'CHURUBAMBA');
INSERT INTO `distritos` VALUES ('100105', '1001', 'MARGOS');
INSERT INTO `distritos` VALUES ('100106', '1001', 'QUISQUI');
INSERT INTO `distritos` VALUES ('100107', '1001', 'SAN FRANCISCO DE CAYRAN');
INSERT INTO `distritos` VALUES ('100108', '1001', 'SAN PEDRO DE CHAULAN');
INSERT INTO `distritos` VALUES ('100109', '1001', 'SANTA MARIA DEL VALLE');
INSERT INTO `distritos` VALUES ('100110', '1001', 'YARUMAYO');
INSERT INTO `distritos` VALUES ('100111', '1001', 'PILLCO MARCA');
INSERT INTO `distritos` VALUES ('100112', '1001', 'YACUS');
INSERT INTO `distritos` VALUES ('100201', '1002', 'AMBO');
INSERT INTO `distritos` VALUES ('100202', '1002', 'CAYNA');
INSERT INTO `distritos` VALUES ('100203', '1002', 'COLPAS');
INSERT INTO `distritos` VALUES ('100204', '1002', 'CONCHAMARCA');
INSERT INTO `distritos` VALUES ('100205', '1002', 'HUACAR');
INSERT INTO `distritos` VALUES ('100206', '1002', 'SAN FRANCISCO');
INSERT INTO `distritos` VALUES ('100207', '1002', 'SAN RAFAEL');
INSERT INTO `distritos` VALUES ('100208', '1002', 'TOMAY KICHWA');
INSERT INTO `distritos` VALUES ('100301', '1003', 'LA UNION');
INSERT INTO `distritos` VALUES ('100307', '1003', 'CHUQUIS');
INSERT INTO `distritos` VALUES ('100311', '1003', 'MARIAS');
INSERT INTO `distritos` VALUES ('100313', '1003', 'PACHAS');
INSERT INTO `distritos` VALUES ('100316', '1003', 'QUIVILLA');
INSERT INTO `distritos` VALUES ('100317', '1003', 'RIPAN');
INSERT INTO `distritos` VALUES ('100321', '1003', 'SHUNQUI');
INSERT INTO `distritos` VALUES ('100322', '1003', 'SILLAPATA');
INSERT INTO `distritos` VALUES ('100323', '1003', 'YANAS');
INSERT INTO `distritos` VALUES ('100401', '1004', 'HUACAYBAMBA');
INSERT INTO `distritos` VALUES ('100402', '1004', 'CANCHABAMBA');
INSERT INTO `distritos` VALUES ('100403', '1004', 'COCHABAMBA');
INSERT INTO `distritos` VALUES ('100404', '1004', 'PINRA');
INSERT INTO `distritos` VALUES ('100501', '1005', 'LLATA');
INSERT INTO `distritos` VALUES ('100502', '1005', 'ARANCAY');
INSERT INTO `distritos` VALUES ('100503', '1005', 'CHAVIN DE PARIARCA');
INSERT INTO `distritos` VALUES ('100504', '1005', 'JACAS GRANDE');
INSERT INTO `distritos` VALUES ('100505', '1005', 'JIRCAN');
INSERT INTO `distritos` VALUES ('100506', '1005', 'MIRAFLORES');
INSERT INTO `distritos` VALUES ('100507', '1005', 'MONZON');
INSERT INTO `distritos` VALUES ('100508', '1005', 'PUNCHAO');
INSERT INTO `distritos` VALUES ('100509', '1005', 'PUÑOS');
INSERT INTO `distritos` VALUES ('100510', '1005', 'SINGA');
INSERT INTO `distritos` VALUES ('100511', '1005', 'TANTAMAYO');
INSERT INTO `distritos` VALUES ('100601', '1006', 'RUPA-RUPA');
INSERT INTO `distritos` VALUES ('100602', '1006', 'DANIEL ALOMIAS ROBLES');
INSERT INTO `distritos` VALUES ('100603', '1006', 'HERMILIO VALDIZAN');
INSERT INTO `distritos` VALUES ('100604', '1006', 'JOSE CRESPO Y CASTILLO');
INSERT INTO `distritos` VALUES ('100605', '1006', 'LUYANDO');
INSERT INTO `distritos` VALUES ('100606', '1006', 'MARIANO DAMASO BERAUN');
INSERT INTO `distritos` VALUES ('100701', '1007', 'HUACRACHUCO');
INSERT INTO `distritos` VALUES ('100702', '1007', 'CHOLON');
INSERT INTO `distritos` VALUES ('100703', '1007', 'SAN BUENAVENTURA');
INSERT INTO `distritos` VALUES ('100801', '1008', 'PANAO');
INSERT INTO `distritos` VALUES ('100802', '1008', 'CHAGLLA');
INSERT INTO `distritos` VALUES ('100803', '1008', 'MOLINO');
INSERT INTO `distritos` VALUES ('100804', '1008', 'UMARI');
INSERT INTO `distritos` VALUES ('100901', '1009', 'PUERTO INCA');
INSERT INTO `distritos` VALUES ('100902', '1009', 'CODO DEL POZUZO');
INSERT INTO `distritos` VALUES ('100903', '1009', 'HONORIA');
INSERT INTO `distritos` VALUES ('100904', '1009', 'TOURNAVISTA');
INSERT INTO `distritos` VALUES ('100905', '1009', 'YUYAPICHIS');
INSERT INTO `distritos` VALUES ('101001', '1010', 'JESUS');
INSERT INTO `distritos` VALUES ('101002', '1010', 'BAÑOS');
INSERT INTO `distritos` VALUES ('101003', '1010', 'JIVIA');
INSERT INTO `distritos` VALUES ('101004', '1010', 'QUEROPALCA');
INSERT INTO `distritos` VALUES ('101005', '1010', 'RONDOS');
INSERT INTO `distritos` VALUES ('101006', '1010', 'SAN FRANCISCO DE ASIS');
INSERT INTO `distritos` VALUES ('101007', '1010', 'SAN MIGUEL DE CAURI');
INSERT INTO `distritos` VALUES ('101101', '1011', 'CHAVINILLO');
INSERT INTO `distritos` VALUES ('101102', '1011', 'CAHUAC');
INSERT INTO `distritos` VALUES ('101103', '1011', 'CHACABAMBA');
INSERT INTO `distritos` VALUES ('101104', '1011', 'CHUPAN');
INSERT INTO `distritos` VALUES ('101105', '1011', 'JACAS CHICO');
INSERT INTO `distritos` VALUES ('101106', '1011', 'OBAS');
INSERT INTO `distritos` VALUES ('101107', '1011', 'PAMPAMARCA');
INSERT INTO `distritos` VALUES ('101108', '1011', 'CHORAS');
INSERT INTO `distritos` VALUES ('110101', '1101', 'ICA');
INSERT INTO `distritos` VALUES ('110102', '1101', 'LA TINGUIÑA');
INSERT INTO `distritos` VALUES ('110103', '1101', 'LOS AQUIJES');
INSERT INTO `distritos` VALUES ('110104', '1101', 'OCUCAJE');
INSERT INTO `distritos` VALUES ('110105', '1101', 'PACHACUTEC');
INSERT INTO `distritos` VALUES ('110106', '1101', 'PARCONA');
INSERT INTO `distritos` VALUES ('110107', '1101', 'PUEBLO NUEVO');
INSERT INTO `distritos` VALUES ('110108', '1101', 'SALAS');
INSERT INTO `distritos` VALUES ('110109', '1101', 'SAN JOSE DE LOS MOLINOS');
INSERT INTO `distritos` VALUES ('110110', '1101', 'SAN JUAN BAUTISTA');
INSERT INTO `distritos` VALUES ('110111', '1101', 'SANTIAGO');
INSERT INTO `distritos` VALUES ('110112', '1101', 'SUBTANJALLA');
INSERT INTO `distritos` VALUES ('110113', '1101', 'TATE');
INSERT INTO `distritos` VALUES ('110114', '1101', 'YAUCA DEL ROSARIO');
INSERT INTO `distritos` VALUES ('110201', '1102', 'CHINCHA ALTA');
INSERT INTO `distritos` VALUES ('110202', '1102', 'ALTO LARAN');
INSERT INTO `distritos` VALUES ('110203', '1102', 'CHAVIN');
INSERT INTO `distritos` VALUES ('110204', '1102', 'CHINCHA BAJA');
INSERT INTO `distritos` VALUES ('110205', '1102', 'EL CARMEN');
INSERT INTO `distritos` VALUES ('110206', '1102', 'GROCIO PRADO');
INSERT INTO `distritos` VALUES ('110207', '1102', 'PUEBLO NUEVO');
INSERT INTO `distritos` VALUES ('110208', '1102', 'SAN JUAN DE YANAC');
INSERT INTO `distritos` VALUES ('110209', '1102', 'SAN PEDRO DE HUACARPANA');
INSERT INTO `distritos` VALUES ('110210', '1102', 'SUNAMPE');
INSERT INTO `distritos` VALUES ('110211', '1102', 'TAMBO DE MORA');
INSERT INTO `distritos` VALUES ('110301', '1103', 'NAZCA');
INSERT INTO `distritos` VALUES ('110302', '1103', 'CHANGUILLO');
INSERT INTO `distritos` VALUES ('110303', '1103', 'EL INGENIO');
INSERT INTO `distritos` VALUES ('110304', '1103', 'MARCONA');
INSERT INTO `distritos` VALUES ('110305', '1103', 'VISTA ALEGRE');
INSERT INTO `distritos` VALUES ('110401', '1104', 'PALPA');
INSERT INTO `distritos` VALUES ('110402', '1104', 'LLIPATA');
INSERT INTO `distritos` VALUES ('110403', '1104', 'RIO GRANDE');
INSERT INTO `distritos` VALUES ('110404', '1104', 'SANTA CRUZ');
INSERT INTO `distritos` VALUES ('110405', '1104', 'TIBILLO');
INSERT INTO `distritos` VALUES ('110501', '1105', 'PISCO');
INSERT INTO `distritos` VALUES ('110502', '1105', 'HUANCANO');
INSERT INTO `distritos` VALUES ('110503', '1105', 'HUMAY');
INSERT INTO `distritos` VALUES ('110504', '1105', 'INDEPENDENCIA');
INSERT INTO `distritos` VALUES ('110505', '1105', 'PARACAS');
INSERT INTO `distritos` VALUES ('110506', '1105', 'SAN ANDRES');
INSERT INTO `distritos` VALUES ('110507', '1105', 'SAN CLEMENTE');
INSERT INTO `distritos` VALUES ('110508', '1105', 'TUPAC AMARU INCA');
INSERT INTO `distritos` VALUES ('120101', '1201', 'HUANCAYO');
INSERT INTO `distritos` VALUES ('120104', '1201', 'CARHUACALLANGA');
INSERT INTO `distritos` VALUES ('120105', '1201', 'CHACAPAMPA');
INSERT INTO `distritos` VALUES ('120106', '1201', 'CHICCHE');
INSERT INTO `distritos` VALUES ('120107', '1201', 'CHILCA');
INSERT INTO `distritos` VALUES ('120108', '1201', 'CHONGOS ALTO');
INSERT INTO `distritos` VALUES ('120111', '1201', 'CHUPURO');
INSERT INTO `distritos` VALUES ('120112', '1201', 'COLCA');
INSERT INTO `distritos` VALUES ('120113', '1201', 'CULLHUAS');
INSERT INTO `distritos` VALUES ('120114', '1201', 'EL TAMBO');
INSERT INTO `distritos` VALUES ('120116', '1201', 'HUACRAPUQUIO');
INSERT INTO `distritos` VALUES ('120117', '1201', 'HUALHUAS');
INSERT INTO `distritos` VALUES ('120119', '1201', 'HUANCAN');
INSERT INTO `distritos` VALUES ('120120', '1201', 'HUASICANCHA');
INSERT INTO `distritos` VALUES ('120121', '1201', 'HUAYUCACHI');
INSERT INTO `distritos` VALUES ('120122', '1201', 'INGENIO');
INSERT INTO `distritos` VALUES ('120124', '1201', 'PARIAHUANCA');
INSERT INTO `distritos` VALUES ('120125', '1201', 'PILCOMAYO');
INSERT INTO `distritos` VALUES ('120126', '1201', 'PUCARA');
INSERT INTO `distritos` VALUES ('120127', '1201', 'QUICHUAY');
INSERT INTO `distritos` VALUES ('120128', '1201', 'QUILCAS');
INSERT INTO `distritos` VALUES ('120129', '1201', 'SAN AGUSTIN');
INSERT INTO `distritos` VALUES ('120130', '1201', 'SAN JERONIMO DE TUNAN');
INSERT INTO `distritos` VALUES ('120132', '1201', 'SAÑO');
INSERT INTO `distritos` VALUES ('120133', '1201', 'SAPALLANGA');
INSERT INTO `distritos` VALUES ('120134', '1201', 'SICAYA');
INSERT INTO `distritos` VALUES ('120135', '1201', 'SANTO DOMINGO DE ACOBAMBA');
INSERT INTO `distritos` VALUES ('120136', '1201', 'VIQUES');
INSERT INTO `distritos` VALUES ('120201', '1202', 'CONCEPCION');
INSERT INTO `distritos` VALUES ('120202', '1202', 'ACO');
INSERT INTO `distritos` VALUES ('120203', '1202', 'ANDAMARCA');
INSERT INTO `distritos` VALUES ('120204', '1202', 'CHAMBARA');
INSERT INTO `distritos` VALUES ('120205', '1202', 'COCHAS');
INSERT INTO `distritos` VALUES ('120206', '1202', 'COMAS');
INSERT INTO `distritos` VALUES ('120207', '1202', 'HEROINAS TOLEDO');
INSERT INTO `distritos` VALUES ('120208', '1202', 'MANZANARES');
INSERT INTO `distritos` VALUES ('120209', '1202', 'MARISCAL CASTILLA');
INSERT INTO `distritos` VALUES ('120210', '1202', 'MATAHUASI');
INSERT INTO `distritos` VALUES ('120211', '1202', 'MITO');
INSERT INTO `distritos` VALUES ('120212', '1202', 'NUEVE DE JULIO');
INSERT INTO `distritos` VALUES ('120213', '1202', 'ORCOTUNA');
INSERT INTO `distritos` VALUES ('120214', '1202', 'SAN JOSE DE QUERO');
INSERT INTO `distritos` VALUES ('120215', '1202', 'SANTA ROSA DE OCOPA');
INSERT INTO `distritos` VALUES ('120301', '1203', 'CHANCHAMAYO');
INSERT INTO `distritos` VALUES ('120302', '1203', 'PERENE');
INSERT INTO `distritos` VALUES ('120303', '1203', 'PICHANAQUI');
INSERT INTO `distritos` VALUES ('120304', '1203', 'SAN LUIS DE SHUARO');
INSERT INTO `distritos` VALUES ('120305', '1203', 'SAN RAMON');
INSERT INTO `distritos` VALUES ('120306', '1203', 'VITOC');
INSERT INTO `distritos` VALUES ('120401', '1204', 'JAUJA');
INSERT INTO `distritos` VALUES ('120402', '1204', 'ACOLLA');
INSERT INTO `distritos` VALUES ('120403', '1204', 'APATA');
INSERT INTO `distritos` VALUES ('120404', '1204', 'ATAURA');
INSERT INTO `distritos` VALUES ('120405', '1204', 'CANCHAYLLO');
INSERT INTO `distritos` VALUES ('120406', '1204', 'CURICACA');
INSERT INTO `distritos` VALUES ('120407', '1204', 'EL MANTARO');
INSERT INTO `distritos` VALUES ('120408', '1204', 'HUAMALI');
INSERT INTO `distritos` VALUES ('120409', '1204', 'HUARIPAMPA');
INSERT INTO `distritos` VALUES ('120410', '1204', 'HUERTAS');
INSERT INTO `distritos` VALUES ('120411', '1204', 'JANJAILLO');
INSERT INTO `distritos` VALUES ('120412', '1204', 'JULCAN');
INSERT INTO `distritos` VALUES ('120413', '1204', 'LEONOR ORDOÑEZ');
INSERT INTO `distritos` VALUES ('120414', '1204', 'LLOCLLAPAMPA');
INSERT INTO `distritos` VALUES ('120415', '1204', 'MARCO');
INSERT INTO `distritos` VALUES ('120416', '1204', 'MASMA');
INSERT INTO `distritos` VALUES ('120417', '1204', 'MASMA CHICCHE');
INSERT INTO `distritos` VALUES ('120418', '1204', 'MOLINOS');
INSERT INTO `distritos` VALUES ('120419', '1204', 'MONOBAMBA');
INSERT INTO `distritos` VALUES ('120420', '1204', 'MUQUI');
INSERT INTO `distritos` VALUES ('120421', '1204', 'MUQUIYAUYO');
INSERT INTO `distritos` VALUES ('120422', '1204', 'PACA');
INSERT INTO `distritos` VALUES ('120423', '1204', 'PACCHA');
INSERT INTO `distritos` VALUES ('120424', '1204', 'PANCAN');
INSERT INTO `distritos` VALUES ('120425', '1204', 'PARCO');
INSERT INTO `distritos` VALUES ('120426', '1204', 'POMACANCHA');
INSERT INTO `distritos` VALUES ('120427', '1204', 'RICRAN');
INSERT INTO `distritos` VALUES ('120428', '1204', 'SAN LORENZO');
INSERT INTO `distritos` VALUES ('120429', '1204', 'SAN PEDRO DE CHUNAN');
INSERT INTO `distritos` VALUES ('120430', '1204', 'SAUSA');
INSERT INTO `distritos` VALUES ('120431', '1204', 'SINCOS');
INSERT INTO `distritos` VALUES ('120432', '1204', 'TUNAN MARCA');
INSERT INTO `distritos` VALUES ('120433', '1204', 'YAULI');
INSERT INTO `distritos` VALUES ('120434', '1204', 'YAUYOS');
INSERT INTO `distritos` VALUES ('120501', '1205', 'JUNIN');
INSERT INTO `distritos` VALUES ('120502', '1205', 'CARHUAMAYO');
INSERT INTO `distritos` VALUES ('120503', '1205', 'ONDORES');
INSERT INTO `distritos` VALUES ('120504', '1205', 'ULCUMAYO');
INSERT INTO `distritos` VALUES ('120601', '1206', 'SATIPO');
INSERT INTO `distritos` VALUES ('120602', '1206', 'COVIRIALI');
INSERT INTO `distritos` VALUES ('120603', '1206', 'LLAYLLA');
INSERT INTO `distritos` VALUES ('120604', '1206', 'MAZAMARI');
INSERT INTO `distritos` VALUES ('120605', '1206', 'PAMPA HERMOSA');
INSERT INTO `distritos` VALUES ('120606', '1206', 'PANGOA');
INSERT INTO `distritos` VALUES ('120607', '1206', 'RIO NEGRO');
INSERT INTO `distritos` VALUES ('120608', '1206', 'RIO TAMBO');
INSERT INTO `distritos` VALUES ('120699', '1206', 'MAZAMARI-PANGOA');
INSERT INTO `distritos` VALUES ('120701', '1207', 'TARMA');
INSERT INTO `distritos` VALUES ('120702', '1207', 'ACOBAMBA');
INSERT INTO `distritos` VALUES ('120703', '1207', 'HUARICOLCA');
INSERT INTO `distritos` VALUES ('120704', '1207', 'HUASAHUASI');
INSERT INTO `distritos` VALUES ('120705', '1207', 'LA UNION');
INSERT INTO `distritos` VALUES ('120706', '1207', 'PALCA');
INSERT INTO `distritos` VALUES ('120707', '1207', 'PALCAMAYO');
INSERT INTO `distritos` VALUES ('120708', '1207', 'SAN PEDRO DE CAJAS');
INSERT INTO `distritos` VALUES ('120709', '1207', 'TAPO');
INSERT INTO `distritos` VALUES ('120801', '1208', 'LA OROYA');
INSERT INTO `distritos` VALUES ('120802', '1208', 'CHACAPALPA');
INSERT INTO `distritos` VALUES ('120803', '1208', 'HUAY-HUAY');
INSERT INTO `distritos` VALUES ('120804', '1208', 'MARCAPOMACOCHA');
INSERT INTO `distritos` VALUES ('120805', '1208', 'MOROCOCHA');
INSERT INTO `distritos` VALUES ('120806', '1208', 'PACCHA');
INSERT INTO `distritos` VALUES ('120807', '1208', 'SANTA BARBARA DE CARHUACAYAN');
INSERT INTO `distritos` VALUES ('120808', '1208', 'SANTA ROSA DE SACCO');
INSERT INTO `distritos` VALUES ('120809', '1208', 'SUITUCANCHA');
INSERT INTO `distritos` VALUES ('120810', '1208', 'YAULI');
INSERT INTO `distritos` VALUES ('120901', '1209', 'CHUPACA');
INSERT INTO `distritos` VALUES ('120902', '1209', 'AHUAC');
INSERT INTO `distritos` VALUES ('120903', '1209', 'CHONGOS BAJO');
INSERT INTO `distritos` VALUES ('120904', '1209', 'HUACHAC');
INSERT INTO `distritos` VALUES ('120905', '1209', 'HUAMANCACA CHICO');
INSERT INTO `distritos` VALUES ('120906', '1209', 'SAN JUAN DE ISCOS');
INSERT INTO `distritos` VALUES ('120907', '1209', 'SAN JUAN DE JARPA');
INSERT INTO `distritos` VALUES ('120908', '1209', '3 DE DICIEMBRE');
INSERT INTO `distritos` VALUES ('120909', '1209', 'YANACANCHA');
INSERT INTO `distritos` VALUES ('130101', '1301', 'TRUJILLO');
INSERT INTO `distritos` VALUES ('130102', '1301', 'EL PORVENIR');
INSERT INTO `distritos` VALUES ('130103', '1301', 'FLORENCIA DE MORA');
INSERT INTO `distritos` VALUES ('130104', '1301', 'HUANCHACO');
INSERT INTO `distritos` VALUES ('130105', '1301', 'LA ESPERANZA');
INSERT INTO `distritos` VALUES ('130106', '1301', 'LAREDO');
INSERT INTO `distritos` VALUES ('130107', '1301', 'MOCHE');
INSERT INTO `distritos` VALUES ('130108', '1301', 'POROTO');
INSERT INTO `distritos` VALUES ('130109', '1301', 'SALAVERRY');
INSERT INTO `distritos` VALUES ('130110', '1301', 'SIMBAL');
INSERT INTO `distritos` VALUES ('130111', '1301', 'VICTOR LARCO HERRERA');
INSERT INTO `distritos` VALUES ('130201', '1302', 'ASCOPE');
INSERT INTO `distritos` VALUES ('130202', '1302', 'CHICAMA');
INSERT INTO `distritos` VALUES ('130203', '1302', 'CHOCOPE');
INSERT INTO `distritos` VALUES ('130204', '1302', 'MAGDALENA DE CAO');
INSERT INTO `distritos` VALUES ('130205', '1302', 'PAIJAN');
INSERT INTO `distritos` VALUES ('130206', '1302', 'RAZURI');
INSERT INTO `distritos` VALUES ('130207', '1302', 'SANTIAGO DE CAO');
INSERT INTO `distritos` VALUES ('130208', '1302', 'CASA GRANDE');
INSERT INTO `distritos` VALUES ('130301', '1303', 'BOLIVAR');
INSERT INTO `distritos` VALUES ('130302', '1303', 'BAMBAMARCA');
INSERT INTO `distritos` VALUES ('130303', '1303', 'CONDORMARCA');
INSERT INTO `distritos` VALUES ('130304', '1303', 'LONGOTEA');
INSERT INTO `distritos` VALUES ('130305', '1303', 'UCHUMARCA');
INSERT INTO `distritos` VALUES ('130306', '1303', 'UCUNCHA');
INSERT INTO `distritos` VALUES ('130401', '1304', 'CHEPEN');
INSERT INTO `distritos` VALUES ('130402', '1304', 'PACANGA');
INSERT INTO `distritos` VALUES ('130403', '1304', 'PUEBLO NUEVO');
INSERT INTO `distritos` VALUES ('130501', '1305', 'JULCAN');
INSERT INTO `distritos` VALUES ('130502', '1305', 'CALAMARCA');
INSERT INTO `distritos` VALUES ('130503', '1305', 'CARABAMBA');
INSERT INTO `distritos` VALUES ('130504', '1305', 'HUASO');
INSERT INTO `distritos` VALUES ('130601', '1306', 'OTUZCO');
INSERT INTO `distritos` VALUES ('130602', '1306', 'AGALLPAMPA');
INSERT INTO `distritos` VALUES ('130604', '1306', 'CHARAT');
INSERT INTO `distritos` VALUES ('130605', '1306', 'HUARANCHAL');
INSERT INTO `distritos` VALUES ('130606', '1306', 'LA CUESTA');
INSERT INTO `distritos` VALUES ('130608', '1306', 'MACHE');
INSERT INTO `distritos` VALUES ('130610', '1306', 'PARANDAY');
INSERT INTO `distritos` VALUES ('130611', '1306', 'SALPO');
INSERT INTO `distritos` VALUES ('130613', '1306', 'SINSICAP');
INSERT INTO `distritos` VALUES ('130614', '1306', 'USQUIL');
INSERT INTO `distritos` VALUES ('130701', '1307', 'SAN PEDRO DE LLOC');
INSERT INTO `distritos` VALUES ('130702', '1307', 'GUADALUPE');
INSERT INTO `distritos` VALUES ('130703', '1307', 'JEQUETEPEQUE');
INSERT INTO `distritos` VALUES ('130704', '1307', 'PACASMAYO');
INSERT INTO `distritos` VALUES ('130705', '1307', 'SAN JOSE');
INSERT INTO `distritos` VALUES ('130801', '1308', 'TAYABAMBA');
INSERT INTO `distritos` VALUES ('130802', '1308', 'BULDIBUYO');
INSERT INTO `distritos` VALUES ('130803', '1308', 'CHILLIA');
INSERT INTO `distritos` VALUES ('130804', '1308', 'HUANCASPATA');
INSERT INTO `distritos` VALUES ('130805', '1308', 'HUAYLILLAS');
INSERT INTO `distritos` VALUES ('130806', '1308', 'HUAYO');
INSERT INTO `distritos` VALUES ('130807', '1308', 'ONGON');
INSERT INTO `distritos` VALUES ('130808', '1308', 'PARCOY');
INSERT INTO `distritos` VALUES ('130809', '1308', 'PATAZ');
INSERT INTO `distritos` VALUES ('130810', '1308', 'PIAS');
INSERT INTO `distritos` VALUES ('130811', '1308', 'SANTIAGO DE CHALLAS');
INSERT INTO `distritos` VALUES ('130812', '1308', 'TAURIJA');
INSERT INTO `distritos` VALUES ('130813', '1308', 'URPAY');
INSERT INTO `distritos` VALUES ('130901', '1309', 'HUAMACHUCO');
INSERT INTO `distritos` VALUES ('130902', '1309', 'CHUGAY');
INSERT INTO `distritos` VALUES ('130903', '1309', 'COCHORCO');
INSERT INTO `distritos` VALUES ('130904', '1309', 'CURGOS');
INSERT INTO `distritos` VALUES ('130905', '1309', 'MARCABAL');
INSERT INTO `distritos` VALUES ('130906', '1309', 'SANAGORAN');
INSERT INTO `distritos` VALUES ('130907', '1309', 'SARIN');
INSERT INTO `distritos` VALUES ('130908', '1309', 'SARTIMBAMBA');
INSERT INTO `distritos` VALUES ('131001', '1310', 'SANTIAGO DE CHUCO');
INSERT INTO `distritos` VALUES ('131002', '1310', 'ANGASMARCA');
INSERT INTO `distritos` VALUES ('131003', '1310', 'CACHICADAN');
INSERT INTO `distritos` VALUES ('131004', '1310', 'MOLLEBAMBA');
INSERT INTO `distritos` VALUES ('131005', '1310', 'MOLLEPATA');
INSERT INTO `distritos` VALUES ('131006', '1310', 'QUIRUVILCA');
INSERT INTO `distritos` VALUES ('131007', '1310', 'SANTA CRUZ DE CHUCA');
INSERT INTO `distritos` VALUES ('131008', '1310', 'SITABAMBA');
INSERT INTO `distritos` VALUES ('131101', '1311', 'CASCAS');
INSERT INTO `distritos` VALUES ('131102', '1311', 'LUCMA');
INSERT INTO `distritos` VALUES ('131103', '1311', 'MARMOT');
INSERT INTO `distritos` VALUES ('131104', '1311', 'SAYAPULLO');
INSERT INTO `distritos` VALUES ('131201', '1312', 'VIRU');
INSERT INTO `distritos` VALUES ('131202', '1312', 'CHAO');
INSERT INTO `distritos` VALUES ('131203', '1312', 'GUADALUPITO');
INSERT INTO `distritos` VALUES ('140101', '1401', 'CHICLAYO');
INSERT INTO `distritos` VALUES ('140102', '1401', 'CHONGOYAPE');
INSERT INTO `distritos` VALUES ('140103', '1401', 'ETEN');
INSERT INTO `distritos` VALUES ('140104', '1401', 'ETEN PUERTO');
INSERT INTO `distritos` VALUES ('140105', '1401', 'JOSE LEONARDO ORTIZ');
INSERT INTO `distritos` VALUES ('140106', '1401', 'LA VICTORIA');
INSERT INTO `distritos` VALUES ('140107', '1401', 'LAGUNAS');
INSERT INTO `distritos` VALUES ('140108', '1401', 'MONSEFU');
INSERT INTO `distritos` VALUES ('140109', '1401', 'NUEVA ARICA');
INSERT INTO `distritos` VALUES ('140110', '1401', 'OYOTUN');
INSERT INTO `distritos` VALUES ('140111', '1401', 'PICSI');
INSERT INTO `distritos` VALUES ('140112', '1401', 'PIMENTEL');
INSERT INTO `distritos` VALUES ('140113', '1401', 'REQUE');
INSERT INTO `distritos` VALUES ('140114', '1401', 'SANTA ROSA');
INSERT INTO `distritos` VALUES ('140115', '1401', 'SAÑA');
INSERT INTO `distritos` VALUES ('140116', '1401', 'CAYALTÍ');
INSERT INTO `distritos` VALUES ('140117', '1401', 'PATAPO');
INSERT INTO `distritos` VALUES ('140118', '1401', 'POMALCA');
INSERT INTO `distritos` VALUES ('140119', '1401', 'PUCALÁ');
INSERT INTO `distritos` VALUES ('140120', '1401', 'TUMÁN');
INSERT INTO `distritos` VALUES ('140201', '1402', 'FERREÑAFE');
INSERT INTO `distritos` VALUES ('140202', '1402', 'CAÑARIS');
INSERT INTO `distritos` VALUES ('140203', '1402', 'INCAHUASI');
INSERT INTO `distritos` VALUES ('140204', '1402', 'MANUEL ANTONIO MESONES MURO');
INSERT INTO `distritos` VALUES ('140205', '1402', 'PITIPO');
INSERT INTO `distritos` VALUES ('140206', '1402', 'PUEBLO NUEVO');
INSERT INTO `distritos` VALUES ('140301', '1403', 'LAMBAYEQUE');
INSERT INTO `distritos` VALUES ('140302', '1403', 'CHOCHOPE');
INSERT INTO `distritos` VALUES ('140303', '1403', 'ILLIMO');
INSERT INTO `distritos` VALUES ('140304', '1403', 'JAYANCA');
INSERT INTO `distritos` VALUES ('140305', '1403', 'MOCHUMI');
INSERT INTO `distritos` VALUES ('140306', '1403', 'MORROPE');
INSERT INTO `distritos` VALUES ('140307', '1403', 'MOTUPE');
INSERT INTO `distritos` VALUES ('140308', '1403', 'OLMOS');
INSERT INTO `distritos` VALUES ('140309', '1403', 'PACORA');
INSERT INTO `distritos` VALUES ('140310', '1403', 'SALAS');
INSERT INTO `distritos` VALUES ('140311', '1403', 'SAN JOSE');
INSERT INTO `distritos` VALUES ('140312', '1403', 'TUCUME');
INSERT INTO `distritos` VALUES ('150101', '1501', 'LIMA');
INSERT INTO `distritos` VALUES ('150102', '1501', 'ANCON');
INSERT INTO `distritos` VALUES ('150103', '1501', 'ATE');
INSERT INTO `distritos` VALUES ('150104', '1501', 'BARRANCO');
INSERT INTO `distritos` VALUES ('150105', '1501', 'BREÑA');
INSERT INTO `distritos` VALUES ('150106', '1501', 'CARABAYLLO');
INSERT INTO `distritos` VALUES ('150107', '1501', 'CHACLACAYO');
INSERT INTO `distritos` VALUES ('150108', '1501', 'CHORRILLOS');
INSERT INTO `distritos` VALUES ('150109', '1501', 'CIENEGUILLA');
INSERT INTO `distritos` VALUES ('150110', '1501', 'COMAS');
INSERT INTO `distritos` VALUES ('150111', '1501', 'EL AGUSTINO');
INSERT INTO `distritos` VALUES ('150112', '1501', 'INDEPENDENCIA');
INSERT INTO `distritos` VALUES ('150113', '1501', 'JESUS MARIA');
INSERT INTO `distritos` VALUES ('150114', '1501', 'LA MOLINA');
INSERT INTO `distritos` VALUES ('150115', '1501', 'LA VICTORIA');
INSERT INTO `distritos` VALUES ('150116', '1501', 'LINCE');
INSERT INTO `distritos` VALUES ('150117', '1501', 'LOS OLIVOS');
INSERT INTO `distritos` VALUES ('150118', '1501', 'LURIGANCHO');
INSERT INTO `distritos` VALUES ('150119', '1501', 'LURIN');
INSERT INTO `distritos` VALUES ('150120', '1501', 'MAGDALENA DEL MAR');
INSERT INTO `distritos` VALUES ('150121', '1501', 'PUEBLO LIBRE (MAGDALENA VIEJA)');
INSERT INTO `distritos` VALUES ('150122', '1501', 'MIRAFLORES');
INSERT INTO `distritos` VALUES ('150123', '1501', 'PACHACAMAC');
INSERT INTO `distritos` VALUES ('150124', '1501', 'PUCUSANA');
INSERT INTO `distritos` VALUES ('150125', '1501', 'PUENTE PIEDRA');
INSERT INTO `distritos` VALUES ('150126', '1501', 'PUNTA HERMOSA');
INSERT INTO `distritos` VALUES ('150127', '1501', 'PUNTA NEGRA');
INSERT INTO `distritos` VALUES ('150128', '1501', 'RIMAC');
INSERT INTO `distritos` VALUES ('150129', '1501', 'SAN BARTOLO');
INSERT INTO `distritos` VALUES ('150130', '1501', 'SAN BORJA');
INSERT INTO `distritos` VALUES ('150131', '1501', 'SAN ISIDRO');
INSERT INTO `distritos` VALUES ('150132', '1501', 'SAN JUAN DE LURIGANCHO');
INSERT INTO `distritos` VALUES ('150133', '1501', 'SAN JUAN DE MIRAFLORES');
INSERT INTO `distritos` VALUES ('150134', '1501', 'SAN LUIS');
INSERT INTO `distritos` VALUES ('150135', '1501', 'SAN MARTIN DE PORRES');
INSERT INTO `distritos` VALUES ('150136', '1501', 'SAN MIGUEL');
INSERT INTO `distritos` VALUES ('150137', '1501', 'SANTA ANITA');
INSERT INTO `distritos` VALUES ('150138', '1501', 'SANTA MARIA DEL MAR');
INSERT INTO `distritos` VALUES ('150139', '1501', 'SANTA ROSA');
INSERT INTO `distritos` VALUES ('150140', '1501', 'SANTIAGO DE SURCO');
INSERT INTO `distritos` VALUES ('150141', '1501', 'SURQUILLO');
INSERT INTO `distritos` VALUES ('150142', '1501', 'VILLA EL SALVADOR');
INSERT INTO `distritos` VALUES ('150143', '1501', 'VILLA MARIA DEL TRIUNFO');
INSERT INTO `distritos` VALUES ('150201', '1502', 'BARRANCA');
INSERT INTO `distritos` VALUES ('150202', '1502', 'PARAMONGA');
INSERT INTO `distritos` VALUES ('150203', '1502', 'PATIVILCA');
INSERT INTO `distritos` VALUES ('150204', '1502', 'SUPE');
INSERT INTO `distritos` VALUES ('150205', '1502', 'SUPE PUERTO');
INSERT INTO `distritos` VALUES ('150301', '1503', 'CAJATAMBO');
INSERT INTO `distritos` VALUES ('150302', '1503', 'COPA');
INSERT INTO `distritos` VALUES ('150303', '1503', 'GORGOR');
INSERT INTO `distritos` VALUES ('150304', '1503', 'HUANCAPON');
INSERT INTO `distritos` VALUES ('150305', '1503', 'MANAS');
INSERT INTO `distritos` VALUES ('150401', '1504', 'CANTA');
INSERT INTO `distritos` VALUES ('150402', '1504', 'ARAHUAY');
INSERT INTO `distritos` VALUES ('150403', '1504', 'HUAMANTANGA');
INSERT INTO `distritos` VALUES ('150404', '1504', 'HUAROS');
INSERT INTO `distritos` VALUES ('150405', '1504', 'LACHAQUI');
INSERT INTO `distritos` VALUES ('150406', '1504', 'SAN BUENAVENTURA');
INSERT INTO `distritos` VALUES ('150407', '1504', 'SANTA ROSA DE QUIVES');
INSERT INTO `distritos` VALUES ('150501', '1505', 'SAN VICENTE DE CAÑETE');
INSERT INTO `distritos` VALUES ('150502', '1505', 'ASIA');
INSERT INTO `distritos` VALUES ('150503', '1505', 'CALANGO');
INSERT INTO `distritos` VALUES ('150504', '1505', 'CERRO AZUL');
INSERT INTO `distritos` VALUES ('150505', '1505', 'CHILCA');
INSERT INTO `distritos` VALUES ('150506', '1505', 'COAYLLO');
INSERT INTO `distritos` VALUES ('150507', '1505', 'IMPERIAL');
INSERT INTO `distritos` VALUES ('150508', '1505', 'LUNAHUANA');
INSERT INTO `distritos` VALUES ('150509', '1505', 'MALA');
INSERT INTO `distritos` VALUES ('150510', '1505', 'NUEVO IMPERIAL');
INSERT INTO `distritos` VALUES ('150511', '1505', 'PACARAN');
INSERT INTO `distritos` VALUES ('150512', '1505', 'QUILMANA');
INSERT INTO `distritos` VALUES ('150513', '1505', 'SAN ANTONIO');
INSERT INTO `distritos` VALUES ('150514', '1505', 'SAN LUIS');
INSERT INTO `distritos` VALUES ('150515', '1505', 'SANTA CRUZ DE FLORES');
INSERT INTO `distritos` VALUES ('150516', '1505', 'ZUÑIGA');
INSERT INTO `distritos` VALUES ('150601', '1506', 'HUARAL');
INSERT INTO `distritos` VALUES ('150602', '1506', 'ATAVILLOS ALTO');
INSERT INTO `distritos` VALUES ('150603', '1506', 'ATAVILLOS BAJO');
INSERT INTO `distritos` VALUES ('150604', '1506', 'AUCALLAMA');
INSERT INTO `distritos` VALUES ('150605', '1506', 'CHANCAY');
INSERT INTO `distritos` VALUES ('150606', '1506', 'IHUARI');
INSERT INTO `distritos` VALUES ('150607', '1506', 'LAMPIAN');
INSERT INTO `distritos` VALUES ('150608', '1506', 'PACARAOS');
INSERT INTO `distritos` VALUES ('150609', '1506', 'SAN MIGUEL DE ACOS');
INSERT INTO `distritos` VALUES ('150610', '1506', 'SANTA CRUZ DE ANDAMARCA');
INSERT INTO `distritos` VALUES ('150611', '1506', 'SUMBILCA');
INSERT INTO `distritos` VALUES ('150612', '1506', 'VEINTISIETE DE NOVIEMBRE');
INSERT INTO `distritos` VALUES ('150701', '1507', 'MATUCANA');
INSERT INTO `distritos` VALUES ('150702', '1507', 'ANTIOQUIA');
INSERT INTO `distritos` VALUES ('150703', '1507', 'CALLAHUANCA');
INSERT INTO `distritos` VALUES ('150704', '1507', 'CARAMPOMA');
INSERT INTO `distritos` VALUES ('150705', '1507', 'CHICLA');
INSERT INTO `distritos` VALUES ('150706', '1507', 'CUENCA');
INSERT INTO `distritos` VALUES ('150707', '1507', 'HUACHUPAMPA');
INSERT INTO `distritos` VALUES ('150708', '1507', 'HUANZA');
INSERT INTO `distritos` VALUES ('150709', '1507', 'HUAROCHIRI');
INSERT INTO `distritos` VALUES ('150710', '1507', 'LAHUAYTAMBO');
INSERT INTO `distritos` VALUES ('150711', '1507', 'LANGA');
INSERT INTO `distritos` VALUES ('150712', '1507', 'LARAOS');
INSERT INTO `distritos` VALUES ('150713', '1507', 'MARIATANA');
INSERT INTO `distritos` VALUES ('150714', '1507', 'RICARDO PALMA');
INSERT INTO `distritos` VALUES ('150715', '1507', 'SAN ANDRES DE TUPICOCHA');
INSERT INTO `distritos` VALUES ('150716', '1507', 'SAN ANTONIO');
INSERT INTO `distritos` VALUES ('150717', '1507', 'SAN BARTOLOME');
INSERT INTO `distritos` VALUES ('150718', '1507', 'SAN DAMIAN');
INSERT INTO `distritos` VALUES ('150719', '1507', 'SAN JUAN DE IRIS');
INSERT INTO `distritos` VALUES ('150720', '1507', 'SAN JUAN DE TANTARANCHE');
INSERT INTO `distritos` VALUES ('150721', '1507', 'SAN LORENZO DE QUINTI');
INSERT INTO `distritos` VALUES ('150722', '1507', 'SAN MATEO');
INSERT INTO `distritos` VALUES ('150723', '1507', 'SAN MATEO DE OTAO');
INSERT INTO `distritos` VALUES ('150724', '1507', 'SAN PEDRO DE CASTA');
INSERT INTO `distritos` VALUES ('150725', '1507', 'SAN PEDRO DE HUANCAYRE');
INSERT INTO `distritos` VALUES ('150726', '1507', 'SANGALLAYA');
INSERT INTO `distritos` VALUES ('150727', '1507', 'SANTA CRUZ DE COCACHACRA');
INSERT INTO `distritos` VALUES ('150728', '1507', 'SANTA EULALIA');
INSERT INTO `distritos` VALUES ('150729', '1507', 'SANTIAGO DE ANCHUCAYA');
INSERT INTO `distritos` VALUES ('150730', '1507', 'SANTIAGO DE TUNA');
INSERT INTO `distritos` VALUES ('150731', '1507', 'SANTO DOMINGO DE LOS OLLEROS');
INSERT INTO `distritos` VALUES ('150732', '1507', 'SURCO');
INSERT INTO `distritos` VALUES ('150801', '1508', 'HUACHO');
INSERT INTO `distritos` VALUES ('150802', '1508', 'AMBAR');
INSERT INTO `distritos` VALUES ('150803', '1508', 'CALETA DE CARQUIN');
INSERT INTO `distritos` VALUES ('150804', '1508', 'CHECRAS');
INSERT INTO `distritos` VALUES ('150805', '1508', 'HUALMAY');
INSERT INTO `distritos` VALUES ('150806', '1508', 'HUAURA');
INSERT INTO `distritos` VALUES ('150807', '1508', 'LEONCIO PRADO');
INSERT INTO `distritos` VALUES ('150808', '1508', 'PACCHO');
INSERT INTO `distritos` VALUES ('150809', '1508', 'SANTA LEONOR');
INSERT INTO `distritos` VALUES ('150810', '1508', 'SANTA MARIA');
INSERT INTO `distritos` VALUES ('150811', '1508', 'SAYAN');
INSERT INTO `distritos` VALUES ('150812', '1508', 'VEGUETA');
INSERT INTO `distritos` VALUES ('150901', '1509', 'OYON');
INSERT INTO `distritos` VALUES ('150902', '1509', 'ANDAJES');
INSERT INTO `distritos` VALUES ('150903', '1509', 'CAUJUL');
INSERT INTO `distritos` VALUES ('150904', '1509', 'COCHAMARCA');
INSERT INTO `distritos` VALUES ('150905', '1509', 'NAVAN');
INSERT INTO `distritos` VALUES ('150906', '1509', 'PACHANGARA');
INSERT INTO `distritos` VALUES ('151001', '1510', 'YAUYOS');
INSERT INTO `distritos` VALUES ('151002', '1510', 'ALIS');
INSERT INTO `distritos` VALUES ('151003', '1510', 'AYAUCA');
INSERT INTO `distritos` VALUES ('151004', '1510', 'AYAVIRI');
INSERT INTO `distritos` VALUES ('151005', '1510', 'AZANGARO');
INSERT INTO `distritos` VALUES ('151006', '1510', 'CACRA');
INSERT INTO `distritos` VALUES ('151007', '1510', 'CARANIA');
INSERT INTO `distritos` VALUES ('151008', '1510', 'CATAHUASI');
INSERT INTO `distritos` VALUES ('151009', '1510', 'CHOCOS');
INSERT INTO `distritos` VALUES ('151010', '1510', 'COCHAS');
INSERT INTO `distritos` VALUES ('151011', '1510', 'COLONIA');
INSERT INTO `distritos` VALUES ('151012', '1510', 'HONGOS');
INSERT INTO `distritos` VALUES ('151013', '1510', 'HUAMPARA');
INSERT INTO `distritos` VALUES ('151014', '1510', 'HUANCAYA');
INSERT INTO `distritos` VALUES ('151015', '1510', 'HUANGASCAR');
INSERT INTO `distritos` VALUES ('151016', '1510', 'HUANTAN');
INSERT INTO `distritos` VALUES ('151017', '1510', 'HUAÑEC');
INSERT INTO `distritos` VALUES ('151018', '1510', 'LARAOS');
INSERT INTO `distritos` VALUES ('151019', '1510', 'LINCHA');
INSERT INTO `distritos` VALUES ('151020', '1510', 'MADEAN');
INSERT INTO `distritos` VALUES ('151021', '1510', 'MIRAFLORES');
INSERT INTO `distritos` VALUES ('151022', '1510', 'OMAS');
INSERT INTO `distritos` VALUES ('151023', '1510', 'PUTINZA');
INSERT INTO `distritos` VALUES ('151024', '1510', 'QUINCHES');
INSERT INTO `distritos` VALUES ('151025', '1510', 'QUINOCAY');
INSERT INTO `distritos` VALUES ('151026', '1510', 'SAN JOAQUIN');
INSERT INTO `distritos` VALUES ('151027', '1510', 'SAN PEDRO DE PILAS');
INSERT INTO `distritos` VALUES ('151028', '1510', 'TANTA');
INSERT INTO `distritos` VALUES ('151029', '1510', 'TAURIPAMPA');
INSERT INTO `distritos` VALUES ('151030', '1510', 'TOMAS');
INSERT INTO `distritos` VALUES ('151031', '1510', 'TUPE');
INSERT INTO `distritos` VALUES ('151032', '1510', 'VIÑAC');
INSERT INTO `distritos` VALUES ('151033', '1510', 'VITIS');
INSERT INTO `distritos` VALUES ('160101', '1601', 'IQUITOS');
INSERT INTO `distritos` VALUES ('160102', '1601', 'ALTO NANAY');
INSERT INTO `distritos` VALUES ('160103', '1601', 'FERNANDO LORES');
INSERT INTO `distritos` VALUES ('160104', '1601', 'INDIANA');
INSERT INTO `distritos` VALUES ('160105', '1601', 'LAS AMAZONAS');
INSERT INTO `distritos` VALUES ('160106', '1601', 'MAZAN');
INSERT INTO `distritos` VALUES ('160107', '1601', 'NAPO');
INSERT INTO `distritos` VALUES ('160108', '1601', 'PUNCHANA');
INSERT INTO `distritos` VALUES ('160109', '1601', 'PUTUMAYO');
INSERT INTO `distritos` VALUES ('160110', '1601', 'TORRES CAUSANA');
INSERT INTO `distritos` VALUES ('160112', '1601', 'BELÉN');
INSERT INTO `distritos` VALUES ('160113', '1601', 'SAN JUAN BAUTISTA');
INSERT INTO `distritos` VALUES ('160114', '1601', 'TENIENTE MANUEL CLAVERO');
INSERT INTO `distritos` VALUES ('160201', '1602', 'YURIMAGUAS');
INSERT INTO `distritos` VALUES ('160202', '1602', 'BALSAPUERTO');
INSERT INTO `distritos` VALUES ('160205', '1602', 'JEBEROS');
INSERT INTO `distritos` VALUES ('160206', '1602', 'LAGUNAS');
INSERT INTO `distritos` VALUES ('160210', '1602', 'SANTA CRUZ');
INSERT INTO `distritos` VALUES ('160211', '1602', 'TENIENTE CESAR LOPEZ ROJAS');
INSERT INTO `distritos` VALUES ('160301', '1603', 'NAUTA');
INSERT INTO `distritos` VALUES ('160302', '1603', 'PARINARI');
INSERT INTO `distritos` VALUES ('160303', '1603', 'TIGRE');
INSERT INTO `distritos` VALUES ('160304', '1603', 'TROMPETEROS');
INSERT INTO `distritos` VALUES ('160305', '1603', 'URARINAS');
INSERT INTO `distritos` VALUES ('160401', '1604', 'RAMON CASTILLA');
INSERT INTO `distritos` VALUES ('160402', '1604', 'PEBAS');
INSERT INTO `distritos` VALUES ('160403', '1604', 'YAVARI');
INSERT INTO `distritos` VALUES ('160404', '1604', 'SAN PABLO');
INSERT INTO `distritos` VALUES ('160501', '1605', 'REQUENA');
INSERT INTO `distritos` VALUES ('160502', '1605', 'ALTO TAPICHE');
INSERT INTO `distritos` VALUES ('160503', '1605', 'CAPELO');
INSERT INTO `distritos` VALUES ('160504', '1605', 'EMILIO SAN MARTIN');
INSERT INTO `distritos` VALUES ('160505', '1605', 'MAQUIA');
INSERT INTO `distritos` VALUES ('160506', '1605', 'PUINAHUA');
INSERT INTO `distritos` VALUES ('160507', '1605', 'SAQUENA');
INSERT INTO `distritos` VALUES ('160508', '1605', 'SOPLIN');
INSERT INTO `distritos` VALUES ('160509', '1605', 'TAPICHE');
INSERT INTO `distritos` VALUES ('160510', '1605', 'JENARO HERRERA');
INSERT INTO `distritos` VALUES ('160511', '1605', 'YAQUERANA');
INSERT INTO `distritos` VALUES ('160601', '1606', 'CONTAMANA');
INSERT INTO `distritos` VALUES ('160602', '1606', 'INAHUAYA');
INSERT INTO `distritos` VALUES ('160603', '1606', 'PADRE MARQUEZ');
INSERT INTO `distritos` VALUES ('160604', '1606', 'PAMPA HERMOSA');
INSERT INTO `distritos` VALUES ('160605', '1606', 'SARAYACU');
INSERT INTO `distritos` VALUES ('160606', '1606', 'VARGAS GUERRA');
INSERT INTO `distritos` VALUES ('160701', '1607', 'BARRANCA');
INSERT INTO `distritos` VALUES ('160702', '1607', 'CAHUAPANAS');
INSERT INTO `distritos` VALUES ('160703', '1607', 'MANSERICHE');
INSERT INTO `distritos` VALUES ('160704', '1607', 'MORONA');
INSERT INTO `distritos` VALUES ('160705', '1607', 'PASTAZA');
INSERT INTO `distritos` VALUES ('160706', '1607', 'ANDOAS');
INSERT INTO `distritos` VALUES ('160801', '1608', 'PUTUMAYO');
INSERT INTO `distritos` VALUES ('160802', '1608', 'ROSA PANDURO');
INSERT INTO `distritos` VALUES ('160803', '1608', 'TENIENTE MANUEL CLAVERO');
INSERT INTO `distritos` VALUES ('160804', '1608', 'YAGUAS');
INSERT INTO `distritos` VALUES ('170101', '1701', 'TAMBOPATA');
INSERT INTO `distritos` VALUES ('170102', '1701', 'INAMBARI');
INSERT INTO `distritos` VALUES ('170103', '1701', 'LAS PIEDRAS');
INSERT INTO `distritos` VALUES ('170104', '1701', 'LABERINTO');
INSERT INTO `distritos` VALUES ('170201', '1702', 'MANU');
INSERT INTO `distritos` VALUES ('170202', '1702', 'FITZCARRALD');
INSERT INTO `distritos` VALUES ('170203', '1702', 'MADRE DE DIOS');
INSERT INTO `distritos` VALUES ('170204', '1702', 'HUEPETUHE');
INSERT INTO `distritos` VALUES ('170301', '1703', 'IÑAPARI');
INSERT INTO `distritos` VALUES ('170302', '1703', 'IBERIA');
INSERT INTO `distritos` VALUES ('170303', '1703', 'TAHUAMANU');
INSERT INTO `distritos` VALUES ('180101', '1801', 'MOQUEGUA');
INSERT INTO `distritos` VALUES ('180102', '1801', 'CARUMAS');
INSERT INTO `distritos` VALUES ('180103', '1801', 'CUCHUMBAYA');
INSERT INTO `distritos` VALUES ('180104', '1801', 'SAMEGUA');
INSERT INTO `distritos` VALUES ('180105', '1801', 'SAN CRISTOBAL');
INSERT INTO `distritos` VALUES ('180106', '1801', 'TORATA');
INSERT INTO `distritos` VALUES ('180201', '1802', 'OMATE');
INSERT INTO `distritos` VALUES ('180202', '1802', 'CHOJATA');
INSERT INTO `distritos` VALUES ('180203', '1802', 'COALAQUE');
INSERT INTO `distritos` VALUES ('180204', '1802', 'ICHUÑA');
INSERT INTO `distritos` VALUES ('180205', '1802', 'LA CAPILLA');
INSERT INTO `distritos` VALUES ('180206', '1802', 'LLOQUE');
INSERT INTO `distritos` VALUES ('180207', '1802', 'MATALAQUE');
INSERT INTO `distritos` VALUES ('180208', '1802', 'PUQUINA');
INSERT INTO `distritos` VALUES ('180209', '1802', 'QUINISTAQUILLAS');
INSERT INTO `distritos` VALUES ('180210', '1802', 'UBINAS');
INSERT INTO `distritos` VALUES ('180211', '1802', 'YUNGA');
INSERT INTO `distritos` VALUES ('180301', '1803', 'ILO');
INSERT INTO `distritos` VALUES ('180302', '1803', 'EL ALGARROBAL');
INSERT INTO `distritos` VALUES ('180303', '1803', 'PACOCHA');
INSERT INTO `distritos` VALUES ('190101', '1901', 'CHAUPIMARCA');
INSERT INTO `distritos` VALUES ('190102', '1901', 'HUACHON');
INSERT INTO `distritos` VALUES ('190103', '1901', 'HUARIACA');
INSERT INTO `distritos` VALUES ('190104', '1901', 'HUAYLLAY');
INSERT INTO `distritos` VALUES ('190105', '1901', 'NINACACA');
INSERT INTO `distritos` VALUES ('190106', '1901', 'PALLANCHACRA');
INSERT INTO `distritos` VALUES ('190107', '1901', 'PAUCARTAMBO');
INSERT INTO `distritos` VALUES ('190108', '1901', 'SAN FCO. DE ASÍS DE YARUSYACÁN');
INSERT INTO `distritos` VALUES ('190109', '1901', 'SIMON BOLIVAR');
INSERT INTO `distritos` VALUES ('190110', '1901', 'TICLACAYAN');
INSERT INTO `distritos` VALUES ('190111', '1901', 'TINYAHUARCO');
INSERT INTO `distritos` VALUES ('190112', '1901', 'VICCO');
INSERT INTO `distritos` VALUES ('190113', '1901', 'YANACANCHA');
INSERT INTO `distritos` VALUES ('190201', '1902', 'YANAHUANCA');
INSERT INTO `distritos` VALUES ('190202', '1902', 'CHACAYAN');
INSERT INTO `distritos` VALUES ('190203', '1902', 'GOYLLARISQUIZGA');
INSERT INTO `distritos` VALUES ('190204', '1902', 'PAUCAR');
INSERT INTO `distritos` VALUES ('190205', '1902', 'SAN PEDRO DE PILLAO');
INSERT INTO `distritos` VALUES ('190206', '1902', 'SANTA ANA DE TUSI');
INSERT INTO `distritos` VALUES ('190207', '1902', 'TAPUC');
INSERT INTO `distritos` VALUES ('190208', '1902', 'VILCABAMBA');
INSERT INTO `distritos` VALUES ('190301', '1903', 'OXAPAMPA');
INSERT INTO `distritos` VALUES ('190302', '1903', 'CHONTABAMBA');
INSERT INTO `distritos` VALUES ('190303', '1903', 'HUANCABAMBA');
INSERT INTO `distritos` VALUES ('190304', '1903', 'PALCAZU');
INSERT INTO `distritos` VALUES ('190305', '1903', 'POZUZO');
INSERT INTO `distritos` VALUES ('190306', '1903', 'PUERTO BERMUDEZ');
INSERT INTO `distritos` VALUES ('190307', '1903', 'VILLA RICA');
INSERT INTO `distritos` VALUES ('190308', '1903', 'CONSTITUCION');
INSERT INTO `distritos` VALUES ('200101', '2001', 'PIURA');
INSERT INTO `distritos` VALUES ('200104', '2001', 'CASTILLA');
INSERT INTO `distritos` VALUES ('200105', '2001', 'CATACAOS');
INSERT INTO `distritos` VALUES ('200107', '2001', 'CURA MORI');
INSERT INTO `distritos` VALUES ('200108', '2001', 'EL TALLAN');
INSERT INTO `distritos` VALUES ('200109', '2001', 'LA ARENA');
INSERT INTO `distritos` VALUES ('200110', '2001', 'LA UNION');
INSERT INTO `distritos` VALUES ('200111', '2001', 'LAS LOMAS');
INSERT INTO `distritos` VALUES ('200114', '2001', 'TAMBO GRANDE');
INSERT INTO `distritos` VALUES ('200115', '2001', 'VEINTISÉIS DE OCTUBRE');
INSERT INTO `distritos` VALUES ('200201', '2002', 'AYABACA');
INSERT INTO `distritos` VALUES ('200202', '2002', 'FRIAS');
INSERT INTO `distritos` VALUES ('200203', '2002', 'JILILI');
INSERT INTO `distritos` VALUES ('200204', '2002', 'LAGUNAS');
INSERT INTO `distritos` VALUES ('200205', '2002', 'MONTERO');
INSERT INTO `distritos` VALUES ('200206', '2002', 'PACAIPAMPA');
INSERT INTO `distritos` VALUES ('200207', '2002', 'PAIMAS');
INSERT INTO `distritos` VALUES ('200208', '2002', 'SAPILLICA');
INSERT INTO `distritos` VALUES ('200209', '2002', 'SICCHEZ');
INSERT INTO `distritos` VALUES ('200210', '2002', 'SUYO');
INSERT INTO `distritos` VALUES ('200301', '2003', 'HUANCABAMBA');
INSERT INTO `distritos` VALUES ('200302', '2003', 'CANCHAQUE');
INSERT INTO `distritos` VALUES ('200303', '2003', 'EL CARMEN DE LA FRONTERA');
INSERT INTO `distritos` VALUES ('200304', '2003', 'HUARMACA');
INSERT INTO `distritos` VALUES ('200305', '2003', 'LALAQUIZ');
INSERT INTO `distritos` VALUES ('200306', '2003', 'SAN MIGUEL DE EL FAIQUE');
INSERT INTO `distritos` VALUES ('200307', '2003', 'SONDOR');
INSERT INTO `distritos` VALUES ('200308', '2003', 'SONDORILLO');
INSERT INTO `distritos` VALUES ('200401', '2004', 'CHULUCANAS');
INSERT INTO `distritos` VALUES ('200402', '2004', 'BUENOS AIRES');
INSERT INTO `distritos` VALUES ('200403', '2004', 'CHALACO');
INSERT INTO `distritos` VALUES ('200404', '2004', 'LA MATANZA');
INSERT INTO `distritos` VALUES ('200405', '2004', 'MORROPON');
INSERT INTO `distritos` VALUES ('200406', '2004', 'SALITRAL');
INSERT INTO `distritos` VALUES ('200407', '2004', 'SAN JUAN DE BIGOTE');
INSERT INTO `distritos` VALUES ('200408', '2004', 'SANTA CATALINA DE MOSSA');
INSERT INTO `distritos` VALUES ('200409', '2004', 'SANTO DOMINGO');
INSERT INTO `distritos` VALUES ('200410', '2004', 'YAMANGO');
INSERT INTO `distritos` VALUES ('200501', '2005', 'PAITA');
INSERT INTO `distritos` VALUES ('200502', '2005', 'AMOTAPE');
INSERT INTO `distritos` VALUES ('200503', '2005', 'ARENAL');
INSERT INTO `distritos` VALUES ('200504', '2005', 'COLAN');
INSERT INTO `distritos` VALUES ('200505', '2005', 'LA HUACA');
INSERT INTO `distritos` VALUES ('200506', '2005', 'TAMARINDO');
INSERT INTO `distritos` VALUES ('200507', '2005', 'VICHAYAL');
INSERT INTO `distritos` VALUES ('200601', '2006', 'SULLANA');
INSERT INTO `distritos` VALUES ('200602', '2006', 'BELLAVISTA');
INSERT INTO `distritos` VALUES ('200603', '2006', 'IGNACIO ESCUDERO');
INSERT INTO `distritos` VALUES ('200604', '2006', 'LANCONES');
INSERT INTO `distritos` VALUES ('200605', '2006', 'MARCAVELICA');
INSERT INTO `distritos` VALUES ('200606', '2006', 'MIGUEL CHECA');
INSERT INTO `distritos` VALUES ('200607', '2006', 'QUERECOTILLO');
INSERT INTO `distritos` VALUES ('200608', '2006', 'SALITRAL');
INSERT INTO `distritos` VALUES ('200701', '2007', 'PARIÑAS');
INSERT INTO `distritos` VALUES ('200702', '2007', 'EL ALTO');
INSERT INTO `distritos` VALUES ('200703', '2007', 'LA BREA');
INSERT INTO `distritos` VALUES ('200704', '2007', 'LOBITOS');
INSERT INTO `distritos` VALUES ('200705', '2007', 'LOS ORGANOS');
INSERT INTO `distritos` VALUES ('200706', '2007', 'MANCORA');
INSERT INTO `distritos` VALUES ('200801', '2008', 'SECHURA');
INSERT INTO `distritos` VALUES ('200802', '2008', 'BELLAVISTA DE LA UNION');
INSERT INTO `distritos` VALUES ('200803', '2008', 'BERNAL');
INSERT INTO `distritos` VALUES ('200804', '2008', 'CRISTO NOS VALGA');
INSERT INTO `distritos` VALUES ('200805', '2008', 'VICE');
INSERT INTO `distritos` VALUES ('200806', '2008', 'RINCONADA LLICUAR');
INSERT INTO `distritos` VALUES ('210101', '2101', 'PUNO');
INSERT INTO `distritos` VALUES ('210102', '2101', 'ACORA');
INSERT INTO `distritos` VALUES ('210103', '2101', 'AMANTANI');
INSERT INTO `distritos` VALUES ('210104', '2101', 'ATUNCOLLA');
INSERT INTO `distritos` VALUES ('210105', '2101', 'CAPACHICA');
INSERT INTO `distritos` VALUES ('210106', '2101', 'CHUCUITO');
INSERT INTO `distritos` VALUES ('210107', '2101', 'COATA');
INSERT INTO `distritos` VALUES ('210108', '2101', 'HUATA');
INSERT INTO `distritos` VALUES ('210109', '2101', 'MAÑAZO');
INSERT INTO `distritos` VALUES ('210110', '2101', 'PAUCARCOLLA');
INSERT INTO `distritos` VALUES ('210111', '2101', 'PICHACANI');
INSERT INTO `distritos` VALUES ('210112', '2101', 'PLATERIA');
INSERT INTO `distritos` VALUES ('210113', '2101', 'SAN ANTONIO');
INSERT INTO `distritos` VALUES ('210114', '2101', 'TIQUILLACA');
INSERT INTO `distritos` VALUES ('210115', '2101', 'VILQUE');
INSERT INTO `distritos` VALUES ('210201', '2102', 'AZANGARO');
INSERT INTO `distritos` VALUES ('210202', '2102', 'ACHAYA');
INSERT INTO `distritos` VALUES ('210203', '2102', 'ARAPA');
INSERT INTO `distritos` VALUES ('210204', '2102', 'ASILLO');
INSERT INTO `distritos` VALUES ('210205', '2102', 'CAMINACA');
INSERT INTO `distritos` VALUES ('210206', '2102', 'CHUPA');
INSERT INTO `distritos` VALUES ('210207', '2102', 'JOSE DOMINGO CHOQUEHUANCA');
INSERT INTO `distritos` VALUES ('210208', '2102', 'MUÑANI');
INSERT INTO `distritos` VALUES ('210209', '2102', 'POTONI');
INSERT INTO `distritos` VALUES ('210210', '2102', 'SAMAN');
INSERT INTO `distritos` VALUES ('210211', '2102', 'SAN ANTON');
INSERT INTO `distritos` VALUES ('210212', '2102', 'SAN JOSE');
INSERT INTO `distritos` VALUES ('210213', '2102', 'SAN JUAN DE SALINAS');
INSERT INTO `distritos` VALUES ('210214', '2102', 'SANTIAGO DE PUPUJA');
INSERT INTO `distritos` VALUES ('210215', '2102', 'TIRAPATA');
INSERT INTO `distritos` VALUES ('210301', '2103', 'MACUSANI');
INSERT INTO `distritos` VALUES ('210302', '2103', 'AJOYANI');
INSERT INTO `distritos` VALUES ('210303', '2103', 'AYAPATA');
INSERT INTO `distritos` VALUES ('210304', '2103', 'COASA');
INSERT INTO `distritos` VALUES ('210305', '2103', 'CORANI');
INSERT INTO `distritos` VALUES ('210306', '2103', 'CRUCERO');
INSERT INTO `distritos` VALUES ('210307', '2103', 'ITUATA');
INSERT INTO `distritos` VALUES ('210308', '2103', 'OLLACHEA');
INSERT INTO `distritos` VALUES ('210309', '2103', 'SAN GABAN');
INSERT INTO `distritos` VALUES ('210310', '2103', 'USICAYOS');
INSERT INTO `distritos` VALUES ('210401', '2104', 'JULI');
INSERT INTO `distritos` VALUES ('210402', '2104', 'DESAGUADERO');
INSERT INTO `distritos` VALUES ('210403', '2104', 'HUACULLANI');
INSERT INTO `distritos` VALUES ('210404', '2104', 'KELLUYO');
INSERT INTO `distritos` VALUES ('210405', '2104', 'PISACOMA');
INSERT INTO `distritos` VALUES ('210406', '2104', 'POMATA');
INSERT INTO `distritos` VALUES ('210407', '2104', 'ZEPITA');
INSERT INTO `distritos` VALUES ('210501', '2105', 'ILAVE');
INSERT INTO `distritos` VALUES ('210502', '2105', 'CAPASO');
INSERT INTO `distritos` VALUES ('210503', '2105', 'PILCUYO');
INSERT INTO `distritos` VALUES ('210504', '2105', 'SANTA ROSA');
INSERT INTO `distritos` VALUES ('210505', '2105', 'CONDURIRI');
INSERT INTO `distritos` VALUES ('210601', '2106', 'HUANCANE');
INSERT INTO `distritos` VALUES ('210602', '2106', 'COJATA');
INSERT INTO `distritos` VALUES ('210603', '2106', 'HUATASANI');
INSERT INTO `distritos` VALUES ('210604', '2106', 'INCHUPALLA');
INSERT INTO `distritos` VALUES ('210605', '2106', 'PUSI');
INSERT INTO `distritos` VALUES ('210606', '2106', 'ROSASPATA');
INSERT INTO `distritos` VALUES ('210607', '2106', 'TARACO');
INSERT INTO `distritos` VALUES ('210608', '2106', 'VILQUE CHICO');
INSERT INTO `distritos` VALUES ('210701', '2107', 'LAMPA');
INSERT INTO `distritos` VALUES ('210702', '2107', 'CABANILLA');
INSERT INTO `distritos` VALUES ('210703', '2107', 'CALAPUJA');
INSERT INTO `distritos` VALUES ('210704', '2107', 'NICASIO');
INSERT INTO `distritos` VALUES ('210705', '2107', 'OCUVIRI');
INSERT INTO `distritos` VALUES ('210706', '2107', 'PALCA');
INSERT INTO `distritos` VALUES ('210707', '2107', 'PARATIA');
INSERT INTO `distritos` VALUES ('210708', '2107', 'PUCARA');
INSERT INTO `distritos` VALUES ('210709', '2107', 'SANTA LUCIA');
INSERT INTO `distritos` VALUES ('210710', '2107', 'VILAVILA');
INSERT INTO `distritos` VALUES ('210801', '2108', 'AYAVIRI');
INSERT INTO `distritos` VALUES ('210802', '2108', 'ANTAUTA');
INSERT INTO `distritos` VALUES ('210803', '2108', 'CUPI');
INSERT INTO `distritos` VALUES ('210804', '2108', 'LLALLI');
INSERT INTO `distritos` VALUES ('210805', '2108', 'MACARI');
INSERT INTO `distritos` VALUES ('210806', '2108', 'NUÑOA');
INSERT INTO `distritos` VALUES ('210807', '2108', 'ORURILLO');
INSERT INTO `distritos` VALUES ('210808', '2108', 'SANTA ROSA');
INSERT INTO `distritos` VALUES ('210809', '2108', 'UMACHIRI');
INSERT INTO `distritos` VALUES ('210901', '2109', 'MOHO');
INSERT INTO `distritos` VALUES ('210902', '2109', 'CONIMA');
INSERT INTO `distritos` VALUES ('210903', '2109', 'HUAYRAPATA');
INSERT INTO `distritos` VALUES ('210904', '2109', 'TILALI');
INSERT INTO `distritos` VALUES ('211001', '2110', 'PUTINA');
INSERT INTO `distritos` VALUES ('211002', '2110', 'ANANEA');
INSERT INTO `distritos` VALUES ('211003', '2110', 'PEDRO VILCA APAZA');
INSERT INTO `distritos` VALUES ('211004', '2110', 'QUILCAPUNCU');
INSERT INTO `distritos` VALUES ('211005', '2110', 'SINA');
INSERT INTO `distritos` VALUES ('211101', '2111', 'JULIACA');
INSERT INTO `distritos` VALUES ('211102', '2111', 'CABANA');
INSERT INTO `distritos` VALUES ('211103', '2111', 'CABANILLAS');
INSERT INTO `distritos` VALUES ('211104', '2111', 'CARACOTO');
INSERT INTO `distritos` VALUES ('211201', '2112', 'SANDIA');
INSERT INTO `distritos` VALUES ('211202', '2112', 'CUYOCUYO');
INSERT INTO `distritos` VALUES ('211203', '2112', 'LIMBANI');
INSERT INTO `distritos` VALUES ('211204', '2112', 'PATAMBUCO');
INSERT INTO `distritos` VALUES ('211205', '2112', 'PHARA');
INSERT INTO `distritos` VALUES ('211206', '2112', 'QUIACA');
INSERT INTO `distritos` VALUES ('211207', '2112', 'SAN JUAN DEL ORO');
INSERT INTO `distritos` VALUES ('211208', '2112', 'YANAHUAYA');
INSERT INTO `distritos` VALUES ('211209', '2112', 'ALTO INAMBARI');
INSERT INTO `distritos` VALUES ('211210', '2112', 'SAN PEDRO DE PUTINA PUNCO');
INSERT INTO `distritos` VALUES ('211301', '2113', 'YUNGUYO');
INSERT INTO `distritos` VALUES ('211302', '2113', 'ANAPIA');
INSERT INTO `distritos` VALUES ('211303', '2113', 'COPANI');
INSERT INTO `distritos` VALUES ('211304', '2113', 'CUTURAPI');
INSERT INTO `distritos` VALUES ('211305', '2113', 'OLLARAYA');
INSERT INTO `distritos` VALUES ('211306', '2113', 'TINICACHI');
INSERT INTO `distritos` VALUES ('211307', '2113', 'UNICACHI');
INSERT INTO `distritos` VALUES ('220101', '2201', 'MOYOBAMBA');
INSERT INTO `distritos` VALUES ('220102', '2201', 'CALZADA');
INSERT INTO `distritos` VALUES ('220103', '2201', 'HABANA');
INSERT INTO `distritos` VALUES ('220104', '2201', 'JEPELACIO');
INSERT INTO `distritos` VALUES ('220105', '2201', 'SORITOR');
INSERT INTO `distritos` VALUES ('220106', '2201', 'YANTALO');
INSERT INTO `distritos` VALUES ('220201', '2202', 'BELLAVISTA');
INSERT INTO `distritos` VALUES ('220202', '2202', 'ALTO BIAVO');
INSERT INTO `distritos` VALUES ('220203', '2202', 'BAJO BIAVO');
INSERT INTO `distritos` VALUES ('220204', '2202', 'HUALLAGA');
INSERT INTO `distritos` VALUES ('220205', '2202', 'SAN PABLO');
INSERT INTO `distritos` VALUES ('220206', '2202', 'SAN RAFAEL');
INSERT INTO `distritos` VALUES ('220301', '2203', 'SAN JOSE DE SISA');
INSERT INTO `distritos` VALUES ('220302', '2203', 'AGUA BLANCA');
INSERT INTO `distritos` VALUES ('220303', '2203', 'SAN MARTIN');
INSERT INTO `distritos` VALUES ('220304', '2203', 'SANTA ROSA');
INSERT INTO `distritos` VALUES ('220305', '2203', 'SHATOJA');
INSERT INTO `distritos` VALUES ('220401', '2204', 'SAPOSOA');
INSERT INTO `distritos` VALUES ('220402', '2204', 'ALTO SAPOSOA');
INSERT INTO `distritos` VALUES ('220403', '2204', 'EL ESLABON');
INSERT INTO `distritos` VALUES ('220404', '2204', 'PISCOYACU');
INSERT INTO `distritos` VALUES ('220405', '2204', 'SACANCHE');
INSERT INTO `distritos` VALUES ('220406', '2204', 'TINGO DE SAPOSOA');
INSERT INTO `distritos` VALUES ('220501', '2205', 'LAMAS');
INSERT INTO `distritos` VALUES ('220502', '2205', 'ALONSO DE ALVARADO');
INSERT INTO `distritos` VALUES ('220503', '2205', 'BARRANQUITA');
INSERT INTO `distritos` VALUES ('220504', '2205', 'CAYNARACHI');
INSERT INTO `distritos` VALUES ('220505', '2205', 'CUÑUMBUQUI');
INSERT INTO `distritos` VALUES ('220506', '2205', 'PINTO RECODO');
INSERT INTO `distritos` VALUES ('220507', '2205', 'RUMISAPA');
INSERT INTO `distritos` VALUES ('220508', '2205', 'SAN ROQUE DE CUMBAZA');
INSERT INTO `distritos` VALUES ('220509', '2205', 'SHANAO');
INSERT INTO `distritos` VALUES ('220510', '2205', 'TABALOSOS');
INSERT INTO `distritos` VALUES ('220511', '2205', 'ZAPATERO');
INSERT INTO `distritos` VALUES ('220601', '2206', 'JUANJUI');
INSERT INTO `distritos` VALUES ('220602', '2206', 'CAMPANILLA');
INSERT INTO `distritos` VALUES ('220603', '2206', 'HUICUNGO');
INSERT INTO `distritos` VALUES ('220604', '2206', 'PACHIZA');
INSERT INTO `distritos` VALUES ('220605', '2206', 'PAJARILLO');
INSERT INTO `distritos` VALUES ('220701', '2207', 'PICOTA');
INSERT INTO `distritos` VALUES ('220702', '2207', 'BUENOS AIRES');
INSERT INTO `distritos` VALUES ('220703', '2207', 'CASPISAPA');
INSERT INTO `distritos` VALUES ('220704', '2207', 'PILLUANA');
INSERT INTO `distritos` VALUES ('220705', '2207', 'PUCACACA');
INSERT INTO `distritos` VALUES ('220706', '2207', 'SAN CRISTOBAL');
INSERT INTO `distritos` VALUES ('220707', '2207', 'SAN HILARION');
INSERT INTO `distritos` VALUES ('220708', '2207', 'SHAMBOYACU');
INSERT INTO `distritos` VALUES ('220709', '2207', 'TINGO DE PONASA');
INSERT INTO `distritos` VALUES ('220710', '2207', 'TRES UNIDOS');
INSERT INTO `distritos` VALUES ('220801', '2208', 'RIOJA');
INSERT INTO `distritos` VALUES ('220802', '2208', 'AWAJUN');
INSERT INTO `distritos` VALUES ('220803', '2208', 'ELIAS SOPLIN VARGAS');
INSERT INTO `distritos` VALUES ('220804', '2208', 'NUEVA CAJAMARCA');
INSERT INTO `distritos` VALUES ('220805', '2208', 'PARDO MIGUEL');
INSERT INTO `distritos` VALUES ('220806', '2208', 'POSIC');
INSERT INTO `distritos` VALUES ('220807', '2208', 'SAN FERNANDO');
INSERT INTO `distritos` VALUES ('220808', '2208', 'YORONGOS');
INSERT INTO `distritos` VALUES ('220809', '2208', 'YURACYACU');
INSERT INTO `distritos` VALUES ('220901', '2209', 'TARAPOTO');
INSERT INTO `distritos` VALUES ('220902', '2209', 'ALBERTO LEVEAU');
INSERT INTO `distritos` VALUES ('220903', '2209', 'CACATACHI');
INSERT INTO `distritos` VALUES ('220904', '2209', 'CHAZUTA');
INSERT INTO `distritos` VALUES ('220905', '2209', 'CHIPURANA');
INSERT INTO `distritos` VALUES ('220906', '2209', 'EL PORVENIR');
INSERT INTO `distritos` VALUES ('220907', '2209', 'HUIMBAYOC');
INSERT INTO `distritos` VALUES ('220908', '2209', 'JUAN GUERRA');
INSERT INTO `distritos` VALUES ('220909', '2209', 'LA BANDA DE SHILCAYO');
INSERT INTO `distritos` VALUES ('220910', '2209', 'MORALES');
INSERT INTO `distritos` VALUES ('220911', '2209', 'PAPAPLAYA');
INSERT INTO `distritos` VALUES ('220912', '2209', 'SAN ANTONIO');
INSERT INTO `distritos` VALUES ('220913', '2209', 'SAUCE');
INSERT INTO `distritos` VALUES ('220914', '2209', 'SHAPAJA');
INSERT INTO `distritos` VALUES ('221001', '2210', 'TOCACHE');
INSERT INTO `distritos` VALUES ('221002', '2210', 'NUEVO PROGRESO');
INSERT INTO `distritos` VALUES ('221003', '2210', 'POLVORA');
INSERT INTO `distritos` VALUES ('221004', '2210', 'SHUNTE');
INSERT INTO `distritos` VALUES ('221005', '2210', 'UCHIZA');
INSERT INTO `distritos` VALUES ('230101', '2301', 'TACNA');
INSERT INTO `distritos` VALUES ('230102', '2301', 'ALTO DE LA ALIANZA');
INSERT INTO `distritos` VALUES ('230103', '2301', 'CALANA');
INSERT INTO `distritos` VALUES ('230104', '2301', 'CIUDAD NUEVA');
INSERT INTO `distritos` VALUES ('230105', '2301', 'INCLAN');
INSERT INTO `distritos` VALUES ('230106', '2301', 'PACHIA');
INSERT INTO `distritos` VALUES ('230107', '2301', 'PALCA');
INSERT INTO `distritos` VALUES ('230108', '2301', 'POCOLLAY');
INSERT INTO `distritos` VALUES ('230109', '2301', 'SAMA');
INSERT INTO `distritos` VALUES ('230110', '2301', 'CORONEL GREGORIO ALBARRACÍN L');
INSERT INTO `distritos` VALUES ('230201', '2302', 'CANDARAVE');
INSERT INTO `distritos` VALUES ('230202', '2302', 'CAIRANI');
INSERT INTO `distritos` VALUES ('230203', '2302', 'CAMILACA');
INSERT INTO `distritos` VALUES ('230204', '2302', 'CURIBAYA');
INSERT INTO `distritos` VALUES ('230205', '2302', 'HUANUARA');
INSERT INTO `distritos` VALUES ('230206', '2302', 'QUILAHUANI');
INSERT INTO `distritos` VALUES ('230301', '2303', 'LOCUMBA');
INSERT INTO `distritos` VALUES ('230302', '2303', 'ILABAYA');
INSERT INTO `distritos` VALUES ('230303', '2303', 'ITE');
INSERT INTO `distritos` VALUES ('230401', '2304', 'TARATA');
INSERT INTO `distritos` VALUES ('230402', '2304', 'CHUCATAMANI');
INSERT INTO `distritos` VALUES ('230403', '2304', 'ESTIQUE');
INSERT INTO `distritos` VALUES ('230404', '2304', 'ESTIQUE-PAMPA');
INSERT INTO `distritos` VALUES ('230405', '2304', 'SITAJARA');
INSERT INTO `distritos` VALUES ('230406', '2304', 'SUSAPAYA');
INSERT INTO `distritos` VALUES ('230407', '2304', 'TARUCACHI');
INSERT INTO `distritos` VALUES ('230408', '2304', 'TICACO');
INSERT INTO `distritos` VALUES ('240101', '2401', 'TUMBES');
INSERT INTO `distritos` VALUES ('240102', '2401', 'CORRALES');
INSERT INTO `distritos` VALUES ('240103', '2401', 'LA CRUZ');
INSERT INTO `distritos` VALUES ('240104', '2401', 'PAMPAS DE HOSPITAL');
INSERT INTO `distritos` VALUES ('240105', '2401', 'SAN JACINTO');
INSERT INTO `distritos` VALUES ('240106', '2401', 'SAN JUAN DE LA VIRGEN');
INSERT INTO `distritos` VALUES ('240201', '2402', 'ZORRITOS');
INSERT INTO `distritos` VALUES ('240202', '2402', 'CASITAS');
INSERT INTO `distritos` VALUES ('240203', '2402', 'CANOAS DE PUNTA SAL');
INSERT INTO `distritos` VALUES ('240301', '2403', 'ZARUMILLA');
INSERT INTO `distritos` VALUES ('240302', '2403', 'AGUAS VERDES');
INSERT INTO `distritos` VALUES ('240303', '2403', 'MATAPALO');
INSERT INTO `distritos` VALUES ('240304', '2403', 'PAPAYAL');
INSERT INTO `distritos` VALUES ('250101', '2501', 'CALLARIA');
INSERT INTO `distritos` VALUES ('250102', '2501', 'CAMPOVERDE');
INSERT INTO `distritos` VALUES ('250103', '2501', 'IPARIA');
INSERT INTO `distritos` VALUES ('250104', '2501', 'MASISEA');
INSERT INTO `distritos` VALUES ('250105', '2501', 'YARINACOCHA');
INSERT INTO `distritos` VALUES ('250106', '2501', 'NUEVA REQUENA');
INSERT INTO `distritos` VALUES ('250107', '2501', 'MANANTAY');
INSERT INTO `distritos` VALUES ('250201', '2502', 'RAYMONDI');
INSERT INTO `distritos` VALUES ('250202', '2502', 'SEPAHUA');
INSERT INTO `distritos` VALUES ('250203', '2502', 'TAHUANIA');
INSERT INTO `distritos` VALUES ('250204', '2502', 'YURUA');
INSERT INTO `distritos` VALUES ('250301', '2503', 'PADRE ABAD');
INSERT INTO `distritos` VALUES ('250302', '2503', 'IRAZOLA');
INSERT INTO `distritos` VALUES ('250303', '2503', 'CURIMANA');
INSERT INTO `distritos` VALUES ('250401', '2504', 'PURUS');

-- ----------------------------
-- Table structure for empresas
-- ----------------------------
DROP TABLE IF EXISTS `empresas`;
CREATE TABLE `empresas`  (
  `id_empresa` int NOT NULL AUTO_INCREMENT,
  `ruc` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `razon_social` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `comercial` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `cod_sucursal` varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `direccion` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `email` varchar(145) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `telefono` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `password` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `user_sol` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `clave_sol` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `logo` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `ubigeo` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `distrito` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `provincia` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `departamento` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `tipo_impresion` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `modo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `igv` double(10, 2) NULL DEFAULT 0.18,
  `propaganda` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `telefono2` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `telefono3` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_empresa`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of empresas
-- ----------------------------
INSERT INTO `empresas` VALUES (1, '20612112763', 'CREDIGO E.I.R.L', 'CREDIGO', NULL, 'AV. AREQUIPA NRO. 400 AREQUIPA', 'contacto@credigo.com', '993570000', '1', NULL, 'demo', 'demo', NULL, '40101', 'AREQUIPA', 'AREQUIPA', 'AREQUIPA', NULL, 'production', 0.18, '', NULL, NULL);

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `failed_jobs_uuid_unique`(`uuid` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for job_batches
-- ----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `cancelled_at` int NULL DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of job_batches
-- ----------------------------

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED NULL DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jobs_queue_index`(`queue` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jobs
-- ----------------------------

-- ----------------------------
-- Table structure for kardex
-- ----------------------------
DROP TABLE IF EXISTS `kardex`;
CREATE TABLE `kardex`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `producto_id` bigint UNSIGNED NOT NULL COMMENT 'Producto del movimiento',
  `almacen_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'Almacén donde ocurre el movimiento',
  `usuario_id` int NOT NULL COMMENT 'Usuario que registra el movimiento',
  `tipo_movimiento` enum('entrada','salida') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo: entrada o salida',
  `subtipo_movimiento` enum('compra','devolucion_cliente','ajuste_entrada','venta','financiamiento','devolucion_proveedor','ajuste_salida','transferencia_entrada','transferencia_salida','baja_producto') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Subtipo específico del movimiento',
  `cantidad` int NOT NULL COMMENT 'Cantidad del movimiento',
  `stock_anterior` int NOT NULL COMMENT 'Stock antes del movimiento',
  `stock_posterior` int NOT NULL COMMENT 'Stock después del movimiento',
  `precio_unitario` decimal(10, 2) NULL DEFAULT NULL COMMENT 'Precio unitario en el momento del movimiento',
  `referencia_tipo` enum('venta','compra','ajuste','transferencia') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Tipo de documento relacionado',
  `referencia_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'ID del documento relacionado',
  `documento_tipo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Tipo de documento (Factura, Boleta, Guía)',
  `documento_numero` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Número del documento',
  `proveedor_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'Proveedor (solo para compras)',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Observaciones del movimiento',
  `fecha_movimiento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora del movimiento',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_producto`(`producto_id` ASC) USING BTREE,
  INDEX `idx_almacen`(`almacen_id` ASC) USING BTREE,
  INDEX `idx_usuario`(`usuario_id` ASC) USING BTREE,
  INDEX `idx_tipo`(`tipo_movimiento` ASC) USING BTREE,
  INDEX `idx_subtipo`(`subtipo_movimiento` ASC) USING BTREE,
  INDEX `idx_fecha`(`fecha_movimiento` ASC) USING BTREE,
  INDEX `idx_referencia`(`referencia_tipo` ASC, `referencia_id` ASC) USING BTREE,
  INDEX `proveedor_id`(`proveedor_id` ASC) USING BTREE,
  CONSTRAINT `kardex_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `kardex_ibfk_2` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `kardex_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `kardex_ibfk_4` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Kardex - Movimientos de inventario' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of kardex
-- ----------------------------

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` VALUES (4, '2026_01_08_235530_create_personal_access_tokens_table', 2);

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens`  (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for permisos_roles
-- ----------------------------
DROP TABLE IF EXISTS `permisos_roles`;
CREATE TABLE `permisos_roles`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `rol_id` int NOT NULL,
  `modulo_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tiene_permiso` tinyint(1) NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `unique_rol_modulo`(`rol_id` ASC, `modulo_id` ASC) USING BTREE,
  CONSTRAINT `permisos_roles_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`rol_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 53 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of permisos_roles
-- ----------------------------
INSERT INTO `permisos_roles` VALUES (51, 2, 'facturacion', 1, '2026-01-13 15:14:47', '2026-01-13 15:14:47');
INSERT INTO `permisos_roles` VALUES (52, 2, 'ventas', 1, '2026-01-13 15:14:47', '2026-01-13 15:14:47');

-- ----------------------------
-- Table structure for personal_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `personal_access_tokens_token_unique`(`token` ASC) USING BTREE,
  INDEX `personal_access_tokens_tokenable_type_tokenable_id_index`(`tokenable_type` ASC, `tokenable_id` ASC) USING BTREE,
  INDEX `personal_access_tokens_expires_at_index`(`expires_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of personal_access_tokens
-- ----------------------------
INSERT INTO `personal_access_tokens` VALUES (1, 'App\\Models\\User', 1, 'auth_token', 'dcdb35c59c59bee708a2ce42dcb79c771257066b7782d66886d8f84041fb362c', '[\"*\"]', '2026-01-09 04:12:04', '2026-01-09 07:56:35', '2026-01-08 23:56:35', '2026-01-09 04:12:04');
INSERT INTO `personal_access_tokens` VALUES (2, 'App\\Models\\User', 1, 'auth_token', '3eb3da8de4e08320f9ac8e757dd533d38a61b36f5e117f8e22e9d806dfd132fe', '[\"*\"]', NULL, '2026-01-09 08:35:53', '2026-01-09 00:35:53', '2026-01-09 00:35:53');
INSERT INTO `personal_access_tokens` VALUES (3, 'App\\Models\\User', 1, 'auth_token', '9be05ec7a961012ce19467d6bb03d1271c57bc6602362ffda077ba8a5a0d9f9f', '[\"*\"]', NULL, '2026-01-09 08:35:56', '2026-01-09 00:35:56', '2026-01-09 00:35:56');
INSERT INTO `personal_access_tokens` VALUES (4, 'App\\Models\\User', 1, 'auth_token', '761042610194ddcfe2b87a04b363d7ae3c5b9e5f9f3e31e90f80f64cb089cf63', '[\"*\"]', NULL, '2026-01-09 08:35:58', '2026-01-09 00:35:58', '2026-01-09 00:35:58');
INSERT INTO `personal_access_tokens` VALUES (5, 'App\\Models\\User', 1, 'auth_token', 'af5bffa1d681b9a60b5114ba11e9a6de7c53dd9256ccdb0a7b61d8d2dd89bcf2', '[\"*\"]', '2026-01-09 00:36:39', '2026-01-09 08:36:02', '2026-01-09 00:36:02', '2026-01-09 00:36:39');
INSERT INTO `personal_access_tokens` VALUES (6, 'App\\Models\\User', 1, 'auth_token', 'd9990422d8ac40814b4e33f6e0164b4ff32af105911f9cc460c079ad6d3ea384', '[\"*\"]', '2026-01-09 00:37:12', '2026-01-09 08:37:04', '2026-01-09 00:37:04', '2026-01-09 00:37:12');
INSERT INTO `personal_access_tokens` VALUES (7, 'App\\Models\\User', 1, 'auth_token', 'ad58f73bd343635c12f7caede2afc6d68eec7caa20459c9a4b2cf89838f7b1de', '[\"*\"]', '2026-01-09 00:38:41', '2026-01-09 08:38:32', '2026-01-09 00:38:32', '2026-01-09 00:38:41');
INSERT INTO `personal_access_tokens` VALUES (8, 'App\\Models\\User', 1, 'auth_token', 'ec663669c9440d49fd8a3228ccf05249717dc8f47bdb37e7fa125a6a8cc40ca3', '[\"*\"]', '2026-01-09 00:41:02', '2026-01-09 08:41:01', '2026-01-09 00:41:01', '2026-01-09 00:41:02');
INSERT INTO `personal_access_tokens` VALUES (9, 'App\\Models\\User', 1, 'auth_token', '9b90146a3820d11bc26f614ebfffa75241073595f4d57f0132f7869087ea5399', '[\"*\"]', '2026-01-09 00:41:25', '2026-01-09 08:41:19', '2026-01-09 00:41:19', '2026-01-09 00:41:25');
INSERT INTO `personal_access_tokens` VALUES (10, 'App\\Models\\User', 1, 'auth_token', '9caa85703c23f4b2e1c1b3cf66499c615c041291c30822cd022735c29ca19ece', '[\"*\"]', '2026-01-09 22:23:30', '2026-01-09 23:19:57', '2026-01-09 15:19:57', '2026-01-09 22:23:30');
INSERT INTO `personal_access_tokens` VALUES (11, 'App\\Models\\User', 1, 'auth_token', '3b068df9aecee1bda771627ceef5c6167e72c7458463c4b1c077256d802b9c85', '[\"*\"]', '2026-01-09 21:00:20', '2026-01-10 05:00:18', '2026-01-09 21:00:18', '2026-01-09 21:00:20');
INSERT INTO `personal_access_tokens` VALUES (12, 'App\\Models\\User', 1, 'auth_token', '4f402797bb47d22b7c050b3e79cf12b278bfbf4fdf1b32494036a02ff15581c1', '[\"*\"]', '2026-01-10 03:02:56', '2026-01-10 10:01:16', '2026-01-10 02:01:16', '2026-01-10 03:02:56');
INSERT INTO `personal_access_tokens` VALUES (13, 'App\\Models\\User', 1, 'auth_token', 'b91b88b187fa4522dbd7f1ed95ed504625b5ff273a74a5dfcd85b22b399db8e1', '[\"*\"]', '2026-01-10 14:03:32', '2026-01-10 22:02:53', '2026-01-10 14:02:53', '2026-01-10 14:03:32');
INSERT INTO `personal_access_tokens` VALUES (14, 'App\\Models\\User', 1, 'auth_token', '81a0e17014697e284a5612131f23fc26384509fd172521a59b1e5efd76f4a4c1', '[\"*\"]', '2026-01-11 00:14:12', '2026-01-11 00:24:04', '2026-01-10 16:24:04', '2026-01-11 00:14:12');
INSERT INTO `personal_access_tokens` VALUES (15, 'App\\Models\\User', 1, 'auth_token', '1bf1d0d5bd91a6752dc076d5d60cc31b87c6e85e7a75a8d4d99b35f56b0689ce', '[\"*\"]', '2026-01-11 03:02:03', '2026-01-11 11:01:02', '2026-01-11 03:01:02', '2026-01-11 03:02:03');
INSERT INTO `personal_access_tokens` VALUES (16, 'App\\Models\\User', 1, 'auth_token', '73de1cc649201292acaeeaa8c9990f6bb3aa00cb6e97c5973a8116987542d5c6', '[\"*\"]', '2026-01-11 04:17:05', '2026-01-11 11:07:46', '2026-01-11 03:07:46', '2026-01-11 04:17:05');
INSERT INTO `personal_access_tokens` VALUES (17, 'App\\Models\\User', 2, 'auth_token', '2d278ffd201b62e9ca03285abbb46c421cd3b2e1a2046fe947f19c42fc86094d', '[\"*\"]', '2026-01-11 03:33:33', '2026-01-11 11:27:04', '2026-01-11 03:27:04', '2026-01-11 03:33:33');
INSERT INTO `personal_access_tokens` VALUES (18, 'App\\Models\\User', 1, 'auth_token', '656849e0317f1e9a9923c4755f5c9c849b32759d45cd51c6d04f26412aa0fad7', '[\"*\"]', '2026-01-11 03:37:57', '2026-01-11 11:37:56', '2026-01-11 03:37:56', '2026-01-11 03:37:57');
INSERT INTO `personal_access_tokens` VALUES (19, 'App\\Models\\User', 2, 'auth_token', '565f4b03abb69f110609968aed9dbf62127cf9f8f149c0120d4ba3201e3cf283', '[\"*\"]', '2026-01-11 03:39:43', '2026-01-11 11:38:19', '2026-01-11 03:38:19', '2026-01-11 03:39:43');
INSERT INTO `personal_access_tokens` VALUES (20, 'App\\Models\\User', 2, 'auth_token', '85c49a4fb560c916b057d7b50dd53d408807aaaf00c29bef16ad0a7e1abe8618', '[\"*\"]', '2026-01-11 04:09:26', '2026-01-11 11:52:00', '2026-01-11 03:52:00', '2026-01-11 04:09:26');
INSERT INTO `personal_access_tokens` VALUES (21, 'App\\Models\\User', 2, 'auth_token', 'b8a96c5291e985187edb997b5635a721f95deb2cbf1042bf8bc9bb043011305f', '[\"*\"]', '2026-01-11 04:18:37', '2026-01-11 12:09:42', '2026-01-11 04:09:42', '2026-01-11 04:18:37');
INSERT INTO `personal_access_tokens` VALUES (22, 'App\\Models\\User', 1, 'auth_token', 'd192cd0cb410ea7bc224d0a88dcaaab422483eae573a8bdace33b488ad9f93a7', '[\"*\"]', '2026-01-11 20:59:17', '2026-01-12 04:59:01', '2026-01-11 20:59:02', '2026-01-11 20:59:17');
INSERT INTO `personal_access_tokens` VALUES (23, 'App\\Models\\User', 1, 'auth_token', '8eb1be15dd7f3f13352aae94d692574646141812c3ef64c71db7410d2694f5dd', '[\"*\"]', '2026-01-13 04:26:44', '2026-01-13 04:27:58', '2026-01-12 20:27:58', '2026-01-13 04:26:44');
INSERT INTO `personal_access_tokens` VALUES (24, 'App\\Models\\User', 1, 'auth_token', '0abb2d0d95bee8c9aa1301a23b1f0fd3253f012183c97ac4983880ea9a1d85ac', '[\"*\"]', '2026-01-13 04:53:19', '2026-01-13 12:00:11', '2026-01-13 04:00:11', '2026-01-13 04:53:19');
INSERT INTO `personal_access_tokens` VALUES (25, 'App\\Models\\User', 1, 'auth_token', '93a05901c7a49c988e2e73995a6ebc0d2d0a11239577740120864dd2bf8c7518', '[\"*\"]', '2026-01-13 15:26:08', '2026-01-13 21:35:08', '2026-01-13 13:35:08', '2026-01-13 15:26:08');
INSERT INTO `personal_access_tokens` VALUES (26, 'App\\Models\\User', 2, 'auth_token', '96352a02f15e3964dfab2a57628454455344be74ab8b7398a3838fb1910b920f', '[\"*\"]', '2026-01-13 15:15:18', '2026-01-13 23:15:03', '2026-01-13 15:15:03', '2026-01-13 15:15:18');
INSERT INTO `personal_access_tokens` VALUES (27, 'App\\Models\\User', 1, 'auth_token', '2ab4caed2370df7962fb2da7fed093753e71357b905c829594c4b319341df8a5', '[\"*\"]', '2026-01-13 15:44:44', '2026-01-13 23:44:39', '2026-01-13 15:44:39', '2026-01-13 15:44:44');
INSERT INTO `personal_access_tokens` VALUES (28, 'App\\Models\\User', 1, 'auth_token', 'defb9a2ffd8c8195e48f94f2749a1c58f9a9d51cb3f58185cdfc91782e155789', '[\"*\"]', '2026-01-20 22:06:33', '2026-01-21 02:53:03', '2026-01-20 18:53:03', '2026-01-20 22:06:33');

-- ----------------------------
-- Table structure for productos
-- ----------------------------
DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre del producto',
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Descripción detallada del producto',
  `marca` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Marca del producto',
  `modelo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Modelo del producto',
  `codigo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Código manual del producto',
  `codigo_barra` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Código de barras generado',
  `categoria_id` bigint UNSIGNED NOT NULL COMMENT 'Categoría del producto',
  `tipo_producto_id` bigint UNSIGNED NOT NULL COMMENT 'Tipo de producto',
  `stock_actual` int NULL DEFAULT 0 COMMENT 'Stock disponible actual',
  `stock_minimo` int NULL DEFAULT 0 COMMENT 'Stock mínimo para alertas',
  `almacen_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'Almacén donde se encuentra',
  `cantidad_unidad` decimal(10, 2) NULL DEFAULT NULL COMMENT 'Cantidad por unidad (ej: 3.5 litros)',
  `unidad_medida` enum('Litros','Galones','Kilogramos','Onzas','Metros','Otros') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Unidad de medida',
  `proveedor_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'Proveedor del producto',
  `moneda` enum('PEN','USD') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'PEN' COMMENT 'Moneda (PEN=Soles, USD=Dólares)',
  `precio_compra` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Precio de compra',
  `precio_venta` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Precio de venta',
  `descuento_cuota` decimal(10, 2) NULL DEFAULT NULL COMMENT 'Descuento por cuota en financiamiento',
  `fecha_vencimiento` date NULL DEFAULT NULL COMMENT 'Fecha de vencimiento del producto',
  `fecha_registro` date NULL DEFAULT NULL COMMENT 'Fecha de registro del producto',
  `guia_remision` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Número de guía de remisión',
  `estado` tinyint(1) NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
  `created_by` int NULL DEFAULT NULL COMMENT 'Usuario que creó el registro',
  `updated_by` int NULL DEFAULT NULL COMMENT 'Usuario que actualizó el registro',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `codigo`(`codigo` ASC) USING BTREE,
  UNIQUE INDEX `codigo_barra`(`codigo_barra` ASC) USING BTREE,
  INDEX `idx_codigo`(`codigo` ASC) USING BTREE,
  INDEX `idx_codigo_barra`(`codigo_barra` ASC) USING BTREE,
  INDEX `idx_nombre`(`nombre` ASC) USING BTREE,
  INDEX `idx_marca`(`marca` ASC) USING BTREE,
  INDEX `idx_categoria`(`categoria_id` ASC) USING BTREE,
  INDEX `idx_tipo`(`tipo_producto_id` ASC) USING BTREE,
  INDEX `idx_proveedor`(`proveedor_id` ASC) USING BTREE,
  INDEX `idx_almacen`(`almacen_id` ASC) USING BTREE,
  INDEX `idx_estado`(`estado` ASC) USING BTREE,
  INDEX `idx_deleted_at`(`deleted_at` ASC) USING BTREE,
  INDEX `created_by`(`created_by` ASC) USING BTREE,
  INDEX `updated_by`(`updated_by` ASC) USING BTREE,
  FULLTEXT INDEX `idx_search`(`nombre`, `marca`, `modelo`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_producto` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`tipo_producto_id`) REFERENCES `tipos_producto` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `productos_ibfk_3` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `productos_ibfk_4` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `productos_ibfk_5` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`usuario_id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `productos_ibfk_6` FOREIGN KEY (`updated_by`) REFERENCES `usuarios` (`usuario_id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Catálogo principal de productos' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of productos
-- ----------------------------

-- ----------------------------
-- Table structure for productos_caracteristicas
-- ----------------------------
DROP TABLE IF EXISTS `productos_caracteristicas`;
CREATE TABLE `productos_caracteristicas`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `producto_id` bigint UNSIGNED NOT NULL COMMENT 'Producto asociado',
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre de la característica (ej: Aro, Perfil, Operadora)',
  `valor` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Valor de la característica',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_producto`(`producto_id` ASC) USING BTREE,
  INDEX `idx_nombre`(`nombre` ASC) USING BTREE,
  CONSTRAINT `productos_caracteristicas_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Características genéricas de productos (EAV)' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of productos_caracteristicas
-- ----------------------------

-- ----------------------------
-- Table structure for productos_celulares
-- ----------------------------
DROP TABLE IF EXISTS `productos_celulares`;
CREATE TABLE `productos_celulares`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `producto_id` bigint UNSIGNED NOT NULL COMMENT 'Producto asociado',
  `chip_linea` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Número de chip/línea',
  `imei` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'IMEI del equipo',
  `imei2` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'IMEI 2 (dual SIM)',
  `color` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Color del equipo',
  `capacidad_gb` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Capacidad de almacenamiento (ej: 128GB)',
  `cargador` enum('Si','No','Completo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Incluye cargador',
  `cable_usb` enum('Si','No','USB Tipo-C','Lightning','Micro-USB') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Tipo de cable USB',
  `manual_usuario` enum('Si','No') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Incluye manual',
  `estuche` enum('Si','No') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Incluye estuche',
  `audifonos` enum('Si','No') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Incluye audífonos',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `producto_id`(`producto_id` ASC) USING BTREE,
  INDEX `idx_imei`(`imei` ASC) USING BTREE,
  INDEX `idx_imei2`(`imei2` ASC) USING BTREE,
  CONSTRAINT `productos_celulares_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Características de productos celulares' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of productos_celulares
-- ----------------------------

-- ----------------------------
-- Table structure for productos_vehiculos
-- ----------------------------
DROP TABLE IF EXISTS `productos_vehiculos`;
CREATE TABLE `productos_vehiculos`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `producto_id` bigint UNSIGNED NOT NULL COMMENT 'Producto asociado',
  `vin` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'VIN (Vehicle Identification Number)',
  `numero_motor` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Número de motor',
  `numero_chasis` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Número de chasis',
  `placa` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Placa del vehículo',
  `color` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Color del vehículo',
  `anio` year NULL DEFAULT NULL COMMENT 'Año de fabricación',
  `transmision` enum('Manual','Automático','Automática') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Tipo de transmisión',
  `combustible` enum('Gasolina','Diesel','GLP','Gas Natural','Eléctrico','Híbrido') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Tipo de combustible',
  `kilometraje` int NULL DEFAULT NULL COMMENT 'Kilometraje actual',
  `fecha_venc_soat` date NULL DEFAULT NULL COMMENT 'Fecha de vencimiento del SOAT',
  `fecha_venc_seguro` date NULL DEFAULT NULL COMMENT 'Fecha de vencimiento del seguro',
  `fecha_venc_revision_tecnica` date NULL DEFAULT NULL COMMENT 'Fecha de vencimiento de revisión técnica',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `producto_id`(`producto_id` ASC) USING BTREE,
  INDEX `idx_vin`(`vin` ASC) USING BTREE,
  INDEX `idx_placa`(`placa` ASC) USING BTREE,
  INDEX `idx_numero_motor`(`numero_motor` ASC) USING BTREE,
  CONSTRAINT `productos_vehiculos_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Características de productos vehículos' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of productos_vehiculos
-- ----------------------------

-- ----------------------------
-- Table structure for proveedores
-- ----------------------------
DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE `proveedores`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ruc` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'RUC del proveedor',
  `razon_social` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Razón social del proveedor',
  `nombre_comercial` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Nombre comercial',
  `direccion` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Dirección fiscal',
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Teléfono principal',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Email del proveedor',
  `departamento` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Departamento (ej: LIMA)',
  `provincia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Provincia (ej: LIMA)',
  `distrito` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Distrito (ej: MIRAFLORES)',
  `ubigeo` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Código ubigeo de 6 dígitos (ej: 150122)',
  `contacto_nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Nombre del contacto',
  `contacto_telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Teléfono del contacto',
  `contacto_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Email del contacto',
  `estado` tinyint(1) NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `ruc`(`ruc` ASC) USING BTREE,
  INDEX `idx_ruc`(`ruc` ASC) USING BTREE,
  INDEX `idx_razon_social`(`razon_social` ASC) USING BTREE,
  INDEX `idx_ubigeo`(`ubigeo` ASC) USING BTREE,
  INDEX `idx_estado`(`estado` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Catálogo de proveedores' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of proveedores
-- ----------------------------
INSERT INTO `proveedores` VALUES (1, '20100070970', 'SUPERMERCADOS PERUANOS SOCIEDAD ANONIMA \'O \' S.P.S.A.', 'SUPERMERCADOS PERUANOS SOCIEDAD ANONIMA \'O \' S.P.S.A.', 'CAL. MORELLI NRO. 181 INT. P-2 LIMA LIMA SAN BORJA', NULL, NULL, 'ANCASH', 'BOLOGNESI', 'CANIS', '020506', NULL, NULL, NULL, 1, '2026-01-13 14:02:22', '2026-01-13 14:05:51');

-- ----------------------------
-- Table structure for provincias
-- ----------------------------
DROP TABLE IF EXISTS `provincias`;
CREATE TABLE `provincias`  (
  `codigo` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `codigo_departamento` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nombre` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`codigo`) USING BTREE,
  INDEX `codigo_departamento`(`codigo_departamento` ASC) USING BTREE,
  CONSTRAINT `provincias_ibfk_1` FOREIGN KEY (`codigo_departamento`) REFERENCES `departamentos` (`codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of provincias
-- ----------------------------
INSERT INTO `provincias` VALUES ('0101', '01', 'CHACHAPOYAS');
INSERT INTO `provincias` VALUES ('0102', '01', 'BAGUA');
INSERT INTO `provincias` VALUES ('0103', '01', 'BONGARA');
INSERT INTO `provincias` VALUES ('0104', '01', 'CONDORCANQUI');
INSERT INTO `provincias` VALUES ('0105', '01', 'LUYA');
INSERT INTO `provincias` VALUES ('0106', '01', 'RODRIGUEZ DE MENDOZA');
INSERT INTO `provincias` VALUES ('0107', '01', 'UTCUBAMBA');
INSERT INTO `provincias` VALUES ('0201', '02', 'HUARAZ');
INSERT INTO `provincias` VALUES ('0202', '02', 'AIJA');
INSERT INTO `provincias` VALUES ('0203', '02', 'ANTONIO RAYMONDI');
INSERT INTO `provincias` VALUES ('0204', '02', 'ASUNCION');
INSERT INTO `provincias` VALUES ('0205', '02', 'BOLOGNESI');
INSERT INTO `provincias` VALUES ('0206', '02', 'CARHUAZ');
INSERT INTO `provincias` VALUES ('0207', '02', 'CARLOS FERMIN FITZCARRALD');
INSERT INTO `provincias` VALUES ('0208', '02', 'CASMA');
INSERT INTO `provincias` VALUES ('0209', '02', 'CORONGO');
INSERT INTO `provincias` VALUES ('0210', '02', 'HUARI');
INSERT INTO `provincias` VALUES ('0211', '02', 'HUARMEY');
INSERT INTO `provincias` VALUES ('0212', '02', 'HUAYLAS');
INSERT INTO `provincias` VALUES ('0213', '02', 'MARISCAL LUZURIAGA');
INSERT INTO `provincias` VALUES ('0214', '02', 'OCROS');
INSERT INTO `provincias` VALUES ('0215', '02', 'PALLASCA');
INSERT INTO `provincias` VALUES ('0216', '02', 'POMABAMBA');
INSERT INTO `provincias` VALUES ('0217', '02', 'RECUAY');
INSERT INTO `provincias` VALUES ('0218', '02', 'SANTA');
INSERT INTO `provincias` VALUES ('0219', '02', 'SIHUAS');
INSERT INTO `provincias` VALUES ('0220', '02', 'YUNGAY');
INSERT INTO `provincias` VALUES ('0301', '03', 'ABANCAY');
INSERT INTO `provincias` VALUES ('0302', '03', 'ANDAHUAYLAS');
INSERT INTO `provincias` VALUES ('0303', '03', 'ANTABAMBA');
INSERT INTO `provincias` VALUES ('0304', '03', 'AYMARAES');
INSERT INTO `provincias` VALUES ('0305', '03', 'COTABAMBAS');
INSERT INTO `provincias` VALUES ('0306', '03', 'CHINCHEROS');
INSERT INTO `provincias` VALUES ('0307', '03', 'GRAU');
INSERT INTO `provincias` VALUES ('0401', '04', 'AREQUIPA');
INSERT INTO `provincias` VALUES ('0402', '04', 'CAMANA');
INSERT INTO `provincias` VALUES ('0403', '04', 'CARAVELI');
INSERT INTO `provincias` VALUES ('0404', '04', 'CASTILLA');
INSERT INTO `provincias` VALUES ('0405', '04', 'CAYLLOMA');
INSERT INTO `provincias` VALUES ('0406', '04', 'CONDESUYOS');
INSERT INTO `provincias` VALUES ('0407', '04', 'ISLAY');
INSERT INTO `provincias` VALUES ('0408', '04', 'LA UNION');
INSERT INTO `provincias` VALUES ('0501', '05', 'HUAMANGA');
INSERT INTO `provincias` VALUES ('0502', '05', 'CANGALLO');
INSERT INTO `provincias` VALUES ('0503', '05', 'HUANCA SANCOS');
INSERT INTO `provincias` VALUES ('0504', '05', 'HUANTA');
INSERT INTO `provincias` VALUES ('0505', '05', 'LA MAR');
INSERT INTO `provincias` VALUES ('0506', '05', 'LUCANAS');
INSERT INTO `provincias` VALUES ('0507', '05', 'PARINACOCHAS');
INSERT INTO `provincias` VALUES ('0508', '05', 'PAUCAR DEL SARA SARA');
INSERT INTO `provincias` VALUES ('0509', '05', 'SUCRE');
INSERT INTO `provincias` VALUES ('0510', '05', 'VICTOR FAJARDO');
INSERT INTO `provincias` VALUES ('0511', '05', 'VILCAS HUAMAN');
INSERT INTO `provincias` VALUES ('0601', '06', 'CAJAMARCA');
INSERT INTO `provincias` VALUES ('0602', '06', 'CAJABAMBA');
INSERT INTO `provincias` VALUES ('0603', '06', 'CELENDIN');
INSERT INTO `provincias` VALUES ('0604', '06', 'CHOTA');
INSERT INTO `provincias` VALUES ('0605', '06', 'CONTUMAZA');
INSERT INTO `provincias` VALUES ('0606', '06', 'CUTERVO');
INSERT INTO `provincias` VALUES ('0607', '06', 'HUALGAYOC');
INSERT INTO `provincias` VALUES ('0608', '06', 'JAEN');
INSERT INTO `provincias` VALUES ('0609', '06', 'SAN IGNACIO');
INSERT INTO `provincias` VALUES ('0610', '06', 'SAN MARCOS');
INSERT INTO `provincias` VALUES ('0611', '06', 'SAN MIGUEL');
INSERT INTO `provincias` VALUES ('0612', '06', 'SAN PABLO');
INSERT INTO `provincias` VALUES ('0613', '06', 'SANTA CRUZ');
INSERT INTO `provincias` VALUES ('0701', '07', 'PROV. CONST. DEL CALLAO');
INSERT INTO `provincias` VALUES ('0801', '08', 'CUSCO');
INSERT INTO `provincias` VALUES ('0802', '08', 'ACOMAYO');
INSERT INTO `provincias` VALUES ('0803', '08', 'ANTA');
INSERT INTO `provincias` VALUES ('0804', '08', 'CALCA');
INSERT INTO `provincias` VALUES ('0805', '08', 'CANAS');
INSERT INTO `provincias` VALUES ('0806', '08', 'CANCHIS');
INSERT INTO `provincias` VALUES ('0807', '08', 'CHUMBIVILCAS');
INSERT INTO `provincias` VALUES ('0808', '08', 'ESPINAR');
INSERT INTO `provincias` VALUES ('0809', '08', 'LA CONVENCION');
INSERT INTO `provincias` VALUES ('0810', '08', 'PARURO');
INSERT INTO `provincias` VALUES ('0811', '08', 'PAUCARTAMBO');
INSERT INTO `provincias` VALUES ('0812', '08', 'QUISPICANCHI');
INSERT INTO `provincias` VALUES ('0813', '08', 'URUBAMBA');
INSERT INTO `provincias` VALUES ('0901', '09', 'HUANCAVELICA');
INSERT INTO `provincias` VALUES ('0902', '09', 'ACOBAMBA');
INSERT INTO `provincias` VALUES ('0903', '09', 'ANGARAES');
INSERT INTO `provincias` VALUES ('0904', '09', 'CASTROVIRREYNA');
INSERT INTO `provincias` VALUES ('0905', '09', 'CHURCAMPA');
INSERT INTO `provincias` VALUES ('0906', '09', 'HUAYTARA');
INSERT INTO `provincias` VALUES ('0907', '09', 'TAYACAJA');
INSERT INTO `provincias` VALUES ('1001', '10', 'HUANUCO');
INSERT INTO `provincias` VALUES ('1002', '10', 'AMBO');
INSERT INTO `provincias` VALUES ('1003', '10', 'DOS DE MAYO');
INSERT INTO `provincias` VALUES ('1004', '10', 'HUACAYBAMBA');
INSERT INTO `provincias` VALUES ('1005', '10', 'HUAMALIES');
INSERT INTO `provincias` VALUES ('1006', '10', 'LEONCIO PRADO');
INSERT INTO `provincias` VALUES ('1007', '10', 'MARAÑON');
INSERT INTO `provincias` VALUES ('1008', '10', 'PACHITEA');
INSERT INTO `provincias` VALUES ('1009', '10', 'PUERTO INCA');
INSERT INTO `provincias` VALUES ('1010', '10', 'LAURICOCHA');
INSERT INTO `provincias` VALUES ('1011', '10', 'YAROWILCA');
INSERT INTO `provincias` VALUES ('1101', '11', 'ICA');
INSERT INTO `provincias` VALUES ('1102', '11', 'CHINCHA');
INSERT INTO `provincias` VALUES ('1103', '11', 'NAZCA');
INSERT INTO `provincias` VALUES ('1104', '11', 'PALPA');
INSERT INTO `provincias` VALUES ('1105', '11', 'PISCO');
INSERT INTO `provincias` VALUES ('1201', '12', 'HUANCAYO');
INSERT INTO `provincias` VALUES ('1202', '12', 'CONCEPCION');
INSERT INTO `provincias` VALUES ('1203', '12', 'CHANCHAMAYO');
INSERT INTO `provincias` VALUES ('1204', '12', 'JAUJA');
INSERT INTO `provincias` VALUES ('1205', '12', 'JUNIN');
INSERT INTO `provincias` VALUES ('1206', '12', 'SATIPO');
INSERT INTO `provincias` VALUES ('1207', '12', 'TARMA');
INSERT INTO `provincias` VALUES ('1208', '12', 'YAULI');
INSERT INTO `provincias` VALUES ('1209', '12', 'CHUPACA');
INSERT INTO `provincias` VALUES ('1301', '13', 'TRUJILLO');
INSERT INTO `provincias` VALUES ('1302', '13', 'ASCOPE');
INSERT INTO `provincias` VALUES ('1303', '13', 'BOLIVAR');
INSERT INTO `provincias` VALUES ('1304', '13', 'CHEPEN');
INSERT INTO `provincias` VALUES ('1305', '13', 'JULCAN');
INSERT INTO `provincias` VALUES ('1306', '13', 'OTUZCO');
INSERT INTO `provincias` VALUES ('1307', '13', 'PACASMAYO');
INSERT INTO `provincias` VALUES ('1308', '13', 'PATAZ');
INSERT INTO `provincias` VALUES ('1309', '13', 'SANCHEZ CARRION');
INSERT INTO `provincias` VALUES ('1310', '13', 'SANTIAGO DE CHUCO');
INSERT INTO `provincias` VALUES ('1311', '13', 'GRAN CHIMU');
INSERT INTO `provincias` VALUES ('1312', '13', 'VIRU');
INSERT INTO `provincias` VALUES ('1401', '14', 'CHICLAYO');
INSERT INTO `provincias` VALUES ('1402', '14', 'FERREÑAFE');
INSERT INTO `provincias` VALUES ('1403', '14', 'LAMBAYEQUE');
INSERT INTO `provincias` VALUES ('1501', '15', 'LIMA');
INSERT INTO `provincias` VALUES ('1502', '15', 'BARRANCA');
INSERT INTO `provincias` VALUES ('1503', '15', 'CAJATAMBO');
INSERT INTO `provincias` VALUES ('1504', '15', 'CANTA');
INSERT INTO `provincias` VALUES ('1505', '15', 'CAÑETE');
INSERT INTO `provincias` VALUES ('1506', '15', 'HUARAL');
INSERT INTO `provincias` VALUES ('1507', '15', 'HUAROCHIRI');
INSERT INTO `provincias` VALUES ('1508', '15', 'HUAURA');
INSERT INTO `provincias` VALUES ('1509', '15', 'OYON');
INSERT INTO `provincias` VALUES ('1510', '15', 'YAUYOS');
INSERT INTO `provincias` VALUES ('1601', '16', 'MAYNAS');
INSERT INTO `provincias` VALUES ('1602', '16', 'ALTO AMAZONAS');
INSERT INTO `provincias` VALUES ('1603', '16', 'LORETO');
INSERT INTO `provincias` VALUES ('1604', '16', 'MARISCAL RAMON CASTILLA');
INSERT INTO `provincias` VALUES ('1605', '16', 'REQUENA');
INSERT INTO `provincias` VALUES ('1606', '16', 'UCAYALI');
INSERT INTO `provincias` VALUES ('1607', '16', 'DATEM DEL MARAÑÓN');
INSERT INTO `provincias` VALUES ('1608', '16', 'PUTUMAYO');
INSERT INTO `provincias` VALUES ('1701', '17', 'TAMBOPATA');
INSERT INTO `provincias` VALUES ('1702', '17', 'MANU');
INSERT INTO `provincias` VALUES ('1703', '17', 'TAHUAMANU');
INSERT INTO `provincias` VALUES ('1801', '18', 'MARISCAL NIETO');
INSERT INTO `provincias` VALUES ('1802', '18', 'GENERAL SANCHEZ CERRO');
INSERT INTO `provincias` VALUES ('1803', '18', 'ILO');
INSERT INTO `provincias` VALUES ('1901', '19', 'PASCO');
INSERT INTO `provincias` VALUES ('1902', '19', 'DANIEL ALCIDES CARRION');
INSERT INTO `provincias` VALUES ('1903', '19', 'OXAPAMPA');
INSERT INTO `provincias` VALUES ('2001', '20', 'PIURA');
INSERT INTO `provincias` VALUES ('2002', '20', 'AYABACA');
INSERT INTO `provincias` VALUES ('2003', '20', 'HUANCABAMBA');
INSERT INTO `provincias` VALUES ('2004', '20', 'MORROPON');
INSERT INTO `provincias` VALUES ('2005', '20', 'PAITA');
INSERT INTO `provincias` VALUES ('2006', '20', 'SULLANA');
INSERT INTO `provincias` VALUES ('2007', '20', 'TALARA');
INSERT INTO `provincias` VALUES ('2008', '20', 'SECHURA');
INSERT INTO `provincias` VALUES ('2101', '21', 'PUNO');
INSERT INTO `provincias` VALUES ('2102', '21', 'AZANGARO');
INSERT INTO `provincias` VALUES ('2103', '21', 'CARABAYA');
INSERT INTO `provincias` VALUES ('2104', '21', 'CHUCUITO');
INSERT INTO `provincias` VALUES ('2105', '21', 'EL COLLAO');
INSERT INTO `provincias` VALUES ('2106', '21', 'HUANCANE');
INSERT INTO `provincias` VALUES ('2107', '21', 'LAMPA');
INSERT INTO `provincias` VALUES ('2108', '21', 'MELGAR');
INSERT INTO `provincias` VALUES ('2109', '21', 'MOHO');
INSERT INTO `provincias` VALUES ('2110', '21', 'SAN ANTONIO DE PUTINA');
INSERT INTO `provincias` VALUES ('2111', '21', 'SAN ROMAN');
INSERT INTO `provincias` VALUES ('2112', '21', 'SANDIA');
INSERT INTO `provincias` VALUES ('2113', '21', 'YUNGUYO');
INSERT INTO `provincias` VALUES ('2201', '22', 'MOYOBAMBA');
INSERT INTO `provincias` VALUES ('2202', '22', 'BELLAVISTA');
INSERT INTO `provincias` VALUES ('2203', '22', 'EL DORADO');
INSERT INTO `provincias` VALUES ('2204', '22', 'HUALLAGA');
INSERT INTO `provincias` VALUES ('2205', '22', 'LAMAS');
INSERT INTO `provincias` VALUES ('2206', '22', 'MARISCAL CACERES');
INSERT INTO `provincias` VALUES ('2207', '22', 'PICOTA');
INSERT INTO `provincias` VALUES ('2208', '22', 'RIOJA');
INSERT INTO `provincias` VALUES ('2209', '22', 'SAN MARTIN');
INSERT INTO `provincias` VALUES ('2210', '22', 'TOCACHE');
INSERT INTO `provincias` VALUES ('2301', '23', 'TACNA');
INSERT INTO `provincias` VALUES ('2302', '23', 'CANDARAVE');
INSERT INTO `provincias` VALUES ('2303', '23', 'JORGE BASADRE');
INSERT INTO `provincias` VALUES ('2304', '23', 'TARATA');
INSERT INTO `provincias` VALUES ('2401', '24', 'TUMBES');
INSERT INTO `provincias` VALUES ('2402', '24', 'CONTRALMIRANTE VILLAR');
INSERT INTO `provincias` VALUES ('2403', '24', 'ZARUMILLA');
INSERT INTO `provincias` VALUES ('2501', '25', 'CORONEL PORTILLO');
INSERT INTO `provincias` VALUES ('2502', '25', 'ATALAYA');
INSERT INTO `provincias` VALUES ('2503', '25', 'PADRE ABAD');
INSERT INTO `provincias` VALUES ('2504', '25', 'PURUS');

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles`  (
  `rol_id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`rol_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES (1, 'ADMIN');
INSERT INTO `roles` VALUES (2, 'ASESOR DE VENTA');
INSERT INTO `roles` VALUES (3, 'DIRECTOR');

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sessions_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `sessions_last_activity_index`(`last_activity` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sessions
-- ----------------------------
INSERT INTO `sessions` VALUES ('OQuSSLeECEbEZUEklc5nE3599J1gQ1ygaBTCYdwb', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS09GUUV2d1QxeTZvbzRBamtSQkIzbVZudXVnTmpidTRGNDdUT0JjNSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly9jcmVkaWdvLnRlc3QvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1769025053);

-- ----------------------------
-- Table structure for sucursales
-- ----------------------------
DROP TABLE IF EXISTS `sucursales`;
CREATE TABLE `sucursales`  (
  `id_sucursal` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NULL DEFAULT NULL,
  `direccion` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `distrito` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `provincia` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `departamento` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `ubigeo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `cod_sucursal` int NULL DEFAULT NULL,
  PRIMARY KEY (`id_sucursal`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of sucursales
-- ----------------------------

-- ----------------------------
-- Table structure for tipos_caracteristicas
-- ----------------------------
DROP TABLE IF EXISTS `tipos_caracteristicas`;
CREATE TABLE `tipos_caracteristicas`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre del tipo (celular, vehiculo, llanta, generico)',
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Descripción del tipo de característica',
  `tabla_asociada` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Nombre de la tabla especializada (productos_celulares, productos_vehiculos, etc)',
  `estado` tinyint(1) NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `nombre`(`nombre` ASC) USING BTREE,
  INDEX `idx_nombre`(`nombre` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 26 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Catálogo de tipos de características' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tipos_caracteristicas
-- ----------------------------
INSERT INTO `tipos_caracteristicas` VALUES (1, 'celular', 'Características especializadas para equipos celulares', 'productos_celulares', 1, '2026-01-12 23:34:24', '2026-01-12 23:34:24');
INSERT INTO `tipos_caracteristicas` VALUES (2, 'vehiculo', 'Características especializadas para vehículos', 'productos_vehiculos', 1, '2026-01-12 23:34:24', '2026-01-12 23:34:24');
INSERT INTO `tipos_caracteristicas` VALUES (3, 'llanta', 'Características para llantas (aro, perfil)', 'productos_caracteristicas', 1, '2026-01-12 23:34:24', '2026-01-12 23:34:24');
INSERT INTO `tipos_caracteristicas` VALUES (4, 'generico', 'Características genéricas (sistema EAV)', 'productos_caracteristicas', 1, '2026-01-12 23:34:24', '2026-01-12 23:34:24');

-- ----------------------------
-- Table structure for tipos_producto
-- ----------------------------
DROP TABLE IF EXISTS `tipos_producto`;
CREATE TABLE `tipos_producto`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre del tipo',
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Descripción del tipo',
  `tipo_venta` enum('unidad','volumen') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Cómo se vende: por unidad o por volumen',
  `requiere_unidad_medida` tinyint(1) NULL DEFAULT 0 COMMENT 'Si requiere especificar unidad de medida',
  `estado` tinyint(1) NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `nombre`(`nombre` ASC) USING BTREE,
  INDEX `idx_nombre`(`nombre` ASC) USING BTREE,
  INDEX `idx_estado`(`estado` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Catálogo de tipos de productos' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tipos_producto
-- ----------------------------
INSERT INTO `tipos_producto` VALUES (1, 'Físico', 'Producto físico vendido por unidad', 'unidad', 0, 1, '2026-01-12 23:34:24', '2026-01-12 23:34:24');
INSERT INTO `tipos_producto` VALUES (2, 'Físico (Por Volumen)', 'Producto físico vendido por volumen (litros, kilos, etc)', 'volumen', 1, 1, '2026-01-12 23:34:24', '2026-01-12 23:34:24');
INSERT INTO `tipos_producto` VALUES (3, 'Intangible', 'Producto intangible o servicio', 'unidad', 0, 1, '2026-01-12 23:34:24', '2026-01-12 23:34:24');

-- ----------------------------
-- Table structure for transferencias_stock
-- ----------------------------
DROP TABLE IF EXISTS `transferencias_stock`;
CREATE TABLE `transferencias_stock`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `almacen_origen_id` bigint UNSIGNED NOT NULL COMMENT 'Almacén de origen',
  `almacen_destino_id` bigint UNSIGNED NOT NULL COMMENT 'Almacén de destino',
  `usuario_solicita_id` int NOT NULL COMMENT 'Usuario que solicita la transferencia',
  `usuario_autoriza_id` int NULL DEFAULT NULL COMMENT 'Usuario que autoriza la transferencia',
  `usuario_recibe_id` int NULL DEFAULT NULL COMMENT 'Usuario que recibe la transferencia',
  `estado` enum('pendiente','autorizada','en_transito','recibida','cancelada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'pendiente' COMMENT 'Estado de la transferencia',
  `fecha_solicitud` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de solicitud',
  `fecha_autorizado` datetime NULL DEFAULT NULL COMMENT 'Fecha de autorización',
  `fecha_enviado` datetime NULL DEFAULT NULL COMMENT 'Fecha de envío',
  `fecha_recibido` datetime NULL DEFAULT NULL COMMENT 'Fecha de recepción',
  `guia_remision` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Número de guía de remisión',
  `documento_transporte` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Documento del transportista',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Observaciones de la transferencia',
  `motivo_cancelacion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Motivo de cancelación (si aplica)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_origen`(`almacen_origen_id` ASC) USING BTREE,
  INDEX `idx_destino`(`almacen_destino_id` ASC) USING BTREE,
  INDEX `idx_estado`(`estado` ASC) USING BTREE,
  INDEX `idx_fecha_solicitud`(`fecha_solicitud` ASC) USING BTREE,
  INDEX `idx_usuario_solicita`(`usuario_solicita_id` ASC) USING BTREE,
  INDEX `usuario_autoriza_id`(`usuario_autoriza_id` ASC) USING BTREE,
  INDEX `usuario_recibe_id`(`usuario_recibe_id` ASC) USING BTREE,
  CONSTRAINT `transferencias_stock_ibfk_1` FOREIGN KEY (`almacen_origen_id`) REFERENCES `almacenes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `transferencias_stock_ibfk_2` FOREIGN KEY (`almacen_destino_id`) REFERENCES `almacenes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `transferencias_stock_ibfk_3` FOREIGN KEY (`usuario_solicita_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `transferencias_stock_ibfk_4` FOREIGN KEY (`usuario_autoriza_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `transferencias_stock_ibfk_5` FOREIGN KEY (`usuario_recibe_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Transferencias de stock entre almacenes' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of transferencias_stock
-- ----------------------------

-- ----------------------------
-- Table structure for transferencias_stock_detalle
-- ----------------------------
DROP TABLE IF EXISTS `transferencias_stock_detalle`;
CREATE TABLE `transferencias_stock_detalle`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `transferencia_id` bigint UNSIGNED NOT NULL COMMENT 'Transferencia asociada',
  `producto_id` bigint UNSIGNED NOT NULL COMMENT 'Producto transferido',
  `cantidad_solicitada` int NOT NULL COMMENT 'Cantidad solicitada',
  `cantidad_enviada` int NULL DEFAULT NULL COMMENT 'Cantidad realmente enviada',
  `cantidad_recibida` int NULL DEFAULT NULL COMMENT 'Cantidad recibida',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Observaciones del producto en esta transferencia',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_transferencia`(`transferencia_id` ASC) USING BTREE,
  INDEX `idx_producto`(`producto_id` ASC) USING BTREE,
  CONSTRAINT `transferencias_stock_detalle_ibfk_1` FOREIGN KEY (`transferencia_id`) REFERENCES `transferencias_stock` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `transferencias_stock_detalle_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Detalle de productos en transferencias' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of transferencias_stock_detalle
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Administrador Sistema', 'admin@credigo.com', NULL, '$2y$12$eGctiuYBOQPV9I54ydGIpuceUyrfM1dLx5Gowt2dpkrQS1ZugVvca', NULL, '2026-01-08 23:56:35', '2026-01-08 23:56:35');
INSERT INTO `users` VALUES (2, 'EMER RODRIGO YARLEQUE ZAPATA', 'kiyotakahitori@gmail.com', NULL, '$2y$12$aVSsG/0XBjpktKiMewKXce2s.dXtAIKdd.p5sm1ZdEMAQVD8ZmcJq', NULL, '2026-01-11 03:27:04', '2026-01-11 03:27:04');

-- ----------------------------
-- Table structure for usuarios
-- ----------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios`  (
  `usuario_id` int NOT NULL AUTO_INCREMENT,
  `id_empresa` int NULL DEFAULT NULL,
  `id_rol` int NULL DEFAULT NULL,
  `num_doc` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `usuario` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `clave` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `email` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `nombres` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `apellidos` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `rubro` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `telefono` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `token_reset` varchar(130) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '1',
  `mensaje` varchar(220) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `rotativo` smallint NULL DEFAULT 0,
  PRIMARY KEY (`usuario_id`) USING BTREE,
  INDEX `idx_usuario`(`usuario` ASC) USING BTREE,
  INDEX `idx_email`(`email` ASC) USING BTREE,
  INDEX `idx_id_rol`(`id_rol` ASC) USING BTREE,
  INDEX `idx_id_empresa`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of usuarios
-- ----------------------------
INSERT INTO `usuarios` VALUES (1, 1, 3, '00000000', 'admin', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'admin@credigo.com', 'Administrador', 'Sistema', NULL, 1, NULL, NULL, '1', NULL, 1);
INSERT INTO `usuarios` VALUES (2, 1, 2, '11111111', 'asesor', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'asesor@credigo.com', 'Asesor', 'Prueba', NULL, 1, NULL, NULL, '0', NULL, 1);
INSERT INTO `usuarios` VALUES (4, 1, 2, '77425200', 'testuser', '7c222fb2927d828af22f592134e8932480637c0d', 'kiyotakahitori@gmail.com', 'EMER RODRIGO', 'YARLEQUE ZAPATA', NULL, 1, '993321920', NULL, '1', NULL, 0);
INSERT INTO `usuarios` VALUES (5, 1, 1, '77426200', 'admin1', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'rodrigoyarleque7@gmail.com', 'BRENDY YOSELY', 'ZAPATA TORRES', NULL, 1, '993321920', NULL, '0', NULL, 0);

-- ----------------------------
-- Table structure for ubigeo_inei
-- ----------------------------
DROP TABLE IF EXISTS `ubigeo_inei`;
CREATE TABLE `ubigeo_inei`  (
  `id_ubigeo` int NOT NULL,
  `departamento` varchar(2) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `provincia` varchar(2) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `distrito` varchar(2) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nombre` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id_ubigeo`) USING BTREE
);
