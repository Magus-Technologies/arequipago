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

 Date: 13/01/2026 10:16:32
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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Almacenes por sucursal' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 55 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Catálogo de categorías de productos' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Kardex - Movimientos de inventario' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

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
INSERT INTO `personal_access_tokens` VALUES (25, 'App\\Models\\User', 1, 'auth_token', '93a05901c7a49c988e2e73995a6ebc0d2d0a11239577740120864dd2bf8c7518', '[\"*\"]', '2026-01-13 15:14:47', '2026-01-13 21:35:08', '2026-01-13 13:35:08', '2026-01-13 15:14:47');
INSERT INTO `personal_access_tokens` VALUES (26, 'App\\Models\\User', 2, 'auth_token', '96352a02f15e3964dfab2a57628454455344be74ab8b7398a3838fb1910b920f', '[\"*\"]', '2026-01-13 15:15:18', '2026-01-13 23:15:03', '2026-01-13 15:15:03', '2026-01-13 15:15:18');

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Catálogo principal de productos' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Características genéricas de productos (EAV)' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Características de productos celulares' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Características de productos vehículos' ROW_FORMAT = Dynamic;

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
INSERT INTO `sessions` VALUES ('Ulhua9YTPvJklDQuU9BnpDaApzWjKEpwUo3QXChD', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRXQ0S29MbTdJM2VZT2tiWkRHSFRkdmo4dWY2Sm5sUVNjYU1uOWhkbyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly9jcmVkaWdvLnRlc3QvYXBpL21lIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1768317318);
INSERT INTO `sessions` VALUES ('z7QQ5XFhzEWEO3gUhSxtqENVPWnmq8N2RZLLlO7S', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT01hYkVsdnVZZDE5dTRCU29rcXF2aTQzZlh5NThTWlM0NHRrelJISiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly9jcmVkaWdvLnRlc3QvYXBpL3Blcm1pc29zLXJvbGVzIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1768317287);

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

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
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Catálogo de tipos de características' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Catálogo de tipos de productos' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ubigeo_inei
-- ----------------------------
INSERT INTO `ubigeo_inei` VALUES (1, '01', '00', '00', 'AMAZONAS');
INSERT INTO `ubigeo_inei` VALUES (2, '01', '01', '00', 'CHACHAPOYAS');
INSERT INTO `ubigeo_inei` VALUES (3, '01', '01', '01', 'CHACHAPOYAS');
INSERT INTO `ubigeo_inei` VALUES (4, '01', '01', '02', 'ASUNCION');
INSERT INTO `ubigeo_inei` VALUES (5, '01', '01', '03', 'BALSAS');
INSERT INTO `ubigeo_inei` VALUES (6, '01', '01', '04', 'CHETO');
INSERT INTO `ubigeo_inei` VALUES (7, '01', '01', '05', 'CHILIQUIN');
INSERT INTO `ubigeo_inei` VALUES (8, '01', '01', '06', 'CHUQUIBAMBA');
INSERT INTO `ubigeo_inei` VALUES (9, '01', '01', '07', 'GRANADA');
INSERT INTO `ubigeo_inei` VALUES (10, '01', '01', '08', 'HUANCAS');
INSERT INTO `ubigeo_inei` VALUES (11, '01', '01', '09', 'LA JALCA');
INSERT INTO `ubigeo_inei` VALUES (12, '01', '01', '10', 'LEIMEBAMBA');
INSERT INTO `ubigeo_inei` VALUES (13, '01', '01', '11', 'LEVANTO');
INSERT INTO `ubigeo_inei` VALUES (14, '01', '01', '12', 'MAGDALENA');
INSERT INTO `ubigeo_inei` VALUES (15, '01', '01', '13', 'MARISCAL CASTILLA');
INSERT INTO `ubigeo_inei` VALUES (16, '01', '01', '14', 'MOLINOPAMPA');
INSERT INTO `ubigeo_inei` VALUES (17, '01', '01', '15', 'MONTEVIDEO');
INSERT INTO `ubigeo_inei` VALUES (18, '01', '01', '16', 'OLLEROS');
INSERT INTO `ubigeo_inei` VALUES (19, '01', '01', '17', 'QUINJALCA');
INSERT INTO `ubigeo_inei` VALUES (20, '01', '01', '18', 'SAN FRANCISCO DE DAGUAS');
INSERT INTO `ubigeo_inei` VALUES (21, '01', '01', '19', 'SAN ISIDRO DE MAINO');
INSERT INTO `ubigeo_inei` VALUES (22, '01', '01', '20', 'SOLOCO');
INSERT INTO `ubigeo_inei` VALUES (23, '01', '01', '21', 'SONCHE');
INSERT INTO `ubigeo_inei` VALUES (24, '01', '02', '00', 'BAGUA');
INSERT INTO `ubigeo_inei` VALUES (25, '01', '02', '01', 'BAGUA');
INSERT INTO `ubigeo_inei` VALUES (26, '01', '02', '02', 'ARAMANGO');
INSERT INTO `ubigeo_inei` VALUES (27, '01', '02', '03', 'COPALLIN');
INSERT INTO `ubigeo_inei` VALUES (28, '01', '02', '04', 'EL PARCO');
INSERT INTO `ubigeo_inei` VALUES (29, '01', '02', '05', 'IMAZA');
INSERT INTO `ubigeo_inei` VALUES (30, '01', '02', '06', 'LA PECA');
INSERT INTO `ubigeo_inei` VALUES (31, '01', '03', '00', 'BONGARA');
INSERT INTO `ubigeo_inei` VALUES (32, '01', '03', '01', 'JUMBILLA');
INSERT INTO `ubigeo_inei` VALUES (33, '01', '03', '02', 'CHISQUILLA');
INSERT INTO `ubigeo_inei` VALUES (34, '01', '03', '03', 'CHURUJA');
INSERT INTO `ubigeo_inei` VALUES (35, '01', '03', '04', 'COROSHA');
INSERT INTO `ubigeo_inei` VALUES (36, '01', '03', '05', 'CUISPES');
INSERT INTO `ubigeo_inei` VALUES (37, '01', '03', '06', 'FLORIDA');
INSERT INTO `ubigeo_inei` VALUES (38, '01', '03', '07', 'JAZÁN');
INSERT INTO `ubigeo_inei` VALUES (39, '01', '03', '08', 'RECTA');
INSERT INTO `ubigeo_inei` VALUES (40, '01', '03', '09', 'SAN CARLOS');
INSERT INTO `ubigeo_inei` VALUES (41, '01', '03', '10', 'SHIPASBAMBA');
INSERT INTO `ubigeo_inei` VALUES (42, '01', '03', '11', 'VALERA');
INSERT INTO `ubigeo_inei` VALUES (43, '01', '03', '12', 'YAMBRASBAMBA');
INSERT INTO `ubigeo_inei` VALUES (44, '01', '04', '00', 'CONDORCANQUI');
INSERT INTO `ubigeo_inei` VALUES (45, '01', '04', '01', 'NIEVA');
INSERT INTO `ubigeo_inei` VALUES (46, '01', '04', '02', 'EL CENEPA');
INSERT INTO `ubigeo_inei` VALUES (47, '01', '04', '03', 'RIO SANTIAGO');
INSERT INTO `ubigeo_inei` VALUES (48, '01', '05', '00', 'LUYA');
INSERT INTO `ubigeo_inei` VALUES (49, '01', '05', '01', 'LAMUD');
INSERT INTO `ubigeo_inei` VALUES (50, '01', '05', '02', 'CAMPORREDONDO');
INSERT INTO `ubigeo_inei` VALUES (51, '01', '05', '03', 'COCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (52, '01', '05', '04', 'COLCAMAR');
INSERT INTO `ubigeo_inei` VALUES (53, '01', '05', '05', 'CONILA');
INSERT INTO `ubigeo_inei` VALUES (54, '01', '05', '06', 'INGUILPATA');
INSERT INTO `ubigeo_inei` VALUES (55, '01', '05', '07', 'LONGUITA');
INSERT INTO `ubigeo_inei` VALUES (56, '01', '05', '08', 'LONYA CHICO');
INSERT INTO `ubigeo_inei` VALUES (57, '01', '05', '09', 'LUYA');
INSERT INTO `ubigeo_inei` VALUES (58, '01', '05', '10', 'LUYA VIEJO');
INSERT INTO `ubigeo_inei` VALUES (59, '01', '05', '11', 'MARIA');
INSERT INTO `ubigeo_inei` VALUES (60, '01', '05', '12', 'OCALLI');
INSERT INTO `ubigeo_inei` VALUES (61, '01', '05', '13', 'OCUMAL');
INSERT INTO `ubigeo_inei` VALUES (62, '01', '05', '14', 'PISUQUIA');
INSERT INTO `ubigeo_inei` VALUES (63, '01', '05', '15', 'PROVIDENCIA');
INSERT INTO `ubigeo_inei` VALUES (64, '01', '05', '16', 'SAN CRISTOBAL');
INSERT INTO `ubigeo_inei` VALUES (65, '01', '05', '17', 'SAN FRANCISCO DEL YESO');
INSERT INTO `ubigeo_inei` VALUES (66, '01', '05', '18', 'SAN JERONIMO');
INSERT INTO `ubigeo_inei` VALUES (67, '01', '05', '19', 'SAN JUAN DE LOPECANCHA');
INSERT INTO `ubigeo_inei` VALUES (68, '01', '05', '20', 'SANTA CATALINA');
INSERT INTO `ubigeo_inei` VALUES (69, '01', '05', '21', 'SANTO TOMAS');
INSERT INTO `ubigeo_inei` VALUES (70, '01', '05', '22', 'TINGO');
INSERT INTO `ubigeo_inei` VALUES (71, '01', '05', '23', 'TRITA');
INSERT INTO `ubigeo_inei` VALUES (72, '01', '06', '00', 'RODRIGUEZ DE MENDOZA');
INSERT INTO `ubigeo_inei` VALUES (73, '01', '06', '01', 'SAN NICOLAS');
INSERT INTO `ubigeo_inei` VALUES (74, '01', '06', '02', 'CHIRIMOTO');
INSERT INTO `ubigeo_inei` VALUES (75, '01', '06', '03', 'COCHAMAL');
INSERT INTO `ubigeo_inei` VALUES (76, '01', '06', '04', 'HUAMBO');
INSERT INTO `ubigeo_inei` VALUES (77, '01', '06', '05', 'LIMABAMBA');
INSERT INTO `ubigeo_inei` VALUES (78, '01', '06', '06', 'LONGAR');
INSERT INTO `ubigeo_inei` VALUES (79, '01', '06', '07', 'MARISCAL BENAVIDES');
INSERT INTO `ubigeo_inei` VALUES (80, '01', '06', '08', 'MILPUC');
INSERT INTO `ubigeo_inei` VALUES (81, '01', '06', '09', 'OMIA');
INSERT INTO `ubigeo_inei` VALUES (82, '01', '06', '10', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (83, '01', '06', '11', 'TOTORA');
INSERT INTO `ubigeo_inei` VALUES (84, '01', '06', '12', 'VISTA ALEGRE');
INSERT INTO `ubigeo_inei` VALUES (85, '01', '07', '00', 'UTCUBAMBA');
INSERT INTO `ubigeo_inei` VALUES (86, '01', '07', '01', 'BAGUA GRANDE');
INSERT INTO `ubigeo_inei` VALUES (87, '01', '07', '02', 'CAJARURO');
INSERT INTO `ubigeo_inei` VALUES (88, '01', '07', '03', 'CUMBA');
INSERT INTO `ubigeo_inei` VALUES (89, '01', '07', '04', 'EL MILAGRO');
INSERT INTO `ubigeo_inei` VALUES (90, '01', '07', '05', 'JAMALCA');
INSERT INTO `ubigeo_inei` VALUES (91, '01', '07', '06', 'LONYA GRANDE');
INSERT INTO `ubigeo_inei` VALUES (92, '01', '07', '07', 'YAMON');
INSERT INTO `ubigeo_inei` VALUES (93, '02', '00', '00', 'ANCASH');
INSERT INTO `ubigeo_inei` VALUES (94, '02', '01', '00', 'HUARAZ');
INSERT INTO `ubigeo_inei` VALUES (95, '02', '01', '01', 'HUARAZ');
INSERT INTO `ubigeo_inei` VALUES (96, '02', '01', '02', 'COCHABAMBA');
INSERT INTO `ubigeo_inei` VALUES (97, '02', '01', '03', 'COLCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (98, '02', '01', '04', 'HUANCHAY');
INSERT INTO `ubigeo_inei` VALUES (99, '02', '01', '05', 'INDEPENDENCIA');
INSERT INTO `ubigeo_inei` VALUES (100, '02', '01', '06', 'JANGAS');
INSERT INTO `ubigeo_inei` VALUES (101, '02', '01', '07', 'LA LIBERTAD');
INSERT INTO `ubigeo_inei` VALUES (102, '02', '01', '08', 'OLLEROS');
INSERT INTO `ubigeo_inei` VALUES (103, '02', '01', '09', 'PAMPAS');
INSERT INTO `ubigeo_inei` VALUES (104, '02', '01', '10', 'PARIACOTO');
INSERT INTO `ubigeo_inei` VALUES (105, '02', '01', '11', 'PIRA');
INSERT INTO `ubigeo_inei` VALUES (106, '02', '01', '12', 'TARICA');
INSERT INTO `ubigeo_inei` VALUES (107, '02', '02', '00', 'AIJA');
INSERT INTO `ubigeo_inei` VALUES (108, '02', '02', '01', 'AIJA');
INSERT INTO `ubigeo_inei` VALUES (109, '02', '02', '02', 'CORIS');
INSERT INTO `ubigeo_inei` VALUES (110, '02', '02', '03', 'HUACLLAN');
INSERT INTO `ubigeo_inei` VALUES (111, '02', '02', '04', 'LA MERCED');
INSERT INTO `ubigeo_inei` VALUES (112, '02', '02', '05', 'SUCCHA');
INSERT INTO `ubigeo_inei` VALUES (113, '02', '03', '00', 'ANTONIO RAYMONDI');
INSERT INTO `ubigeo_inei` VALUES (114, '02', '03', '01', 'LLAMELLIN');
INSERT INTO `ubigeo_inei` VALUES (115, '02', '03', '02', 'ACZO');
INSERT INTO `ubigeo_inei` VALUES (116, '02', '03', '03', 'CHACCHO');
INSERT INTO `ubigeo_inei` VALUES (117, '02', '03', '04', 'CHINGAS');
INSERT INTO `ubigeo_inei` VALUES (118, '02', '03', '05', 'MIRGAS');
INSERT INTO `ubigeo_inei` VALUES (119, '02', '03', '06', 'SAN JUAN DE RONTOY');
INSERT INTO `ubigeo_inei` VALUES (120, '02', '04', '00', 'ASUNCION');
INSERT INTO `ubigeo_inei` VALUES (121, '02', '04', '01', 'CHACAS');
INSERT INTO `ubigeo_inei` VALUES (122, '02', '04', '02', 'ACOCHACA');
INSERT INTO `ubigeo_inei` VALUES (123, '02', '05', '00', 'BOLOGNESI');
INSERT INTO `ubigeo_inei` VALUES (124, '02', '05', '01', 'CHIQUIAN');
INSERT INTO `ubigeo_inei` VALUES (125, '02', '05', '02', 'ABELARDO PARDO LEZAMETA');
INSERT INTO `ubigeo_inei` VALUES (126, '02', '05', '03', 'ANTONIO RAYMONDI');
INSERT INTO `ubigeo_inei` VALUES (127, '02', '05', '04', 'AQUIA');
INSERT INTO `ubigeo_inei` VALUES (128, '02', '05', '05', 'CAJACAY');
INSERT INTO `ubigeo_inei` VALUES (129, '02', '05', '06', 'CANIS');
INSERT INTO `ubigeo_inei` VALUES (130, '02', '05', '07', 'COLQUIOC');
INSERT INTO `ubigeo_inei` VALUES (131, '02', '05', '08', 'HUALLANCA');
INSERT INTO `ubigeo_inei` VALUES (132, '02', '05', '09', 'HUASTA');
INSERT INTO `ubigeo_inei` VALUES (133, '02', '05', '10', 'HUAYLLACAYAN');
INSERT INTO `ubigeo_inei` VALUES (134, '02', '05', '11', 'LA PRIMAVERA');
INSERT INTO `ubigeo_inei` VALUES (135, '02', '05', '12', 'MANGAS');
INSERT INTO `ubigeo_inei` VALUES (136, '02', '05', '13', 'PACLLON');
INSERT INTO `ubigeo_inei` VALUES (137, '02', '05', '14', 'SAN MIGUEL DE CORPANQUI');
INSERT INTO `ubigeo_inei` VALUES (138, '02', '05', '15', 'TICLLOS');
INSERT INTO `ubigeo_inei` VALUES (139, '02', '06', '00', 'CARHUAZ');
INSERT INTO `ubigeo_inei` VALUES (140, '02', '06', '01', 'CARHUAZ');
INSERT INTO `ubigeo_inei` VALUES (141, '02', '06', '02', 'ACOPAMPA');
INSERT INTO `ubigeo_inei` VALUES (142, '02', '06', '03', 'AMASHCA');
INSERT INTO `ubigeo_inei` VALUES (143, '02', '06', '04', 'ANTA');
INSERT INTO `ubigeo_inei` VALUES (144, '02', '06', '05', 'ATAQUERO');
INSERT INTO `ubigeo_inei` VALUES (145, '02', '06', '06', 'MARCARA');
INSERT INTO `ubigeo_inei` VALUES (146, '02', '06', '07', 'PARIAHUANCA');
INSERT INTO `ubigeo_inei` VALUES (147, '02', '06', '08', 'SAN MIGUEL DE ACO');
INSERT INTO `ubigeo_inei` VALUES (148, '02', '06', '09', 'SHILLA');
INSERT INTO `ubigeo_inei` VALUES (149, '02', '06', '10', 'TINCO');
INSERT INTO `ubigeo_inei` VALUES (150, '02', '06', '11', 'YUNGAR');
INSERT INTO `ubigeo_inei` VALUES (151, '02', '07', '00', 'CARLOS FERMIN FITZCARRALD');
INSERT INTO `ubigeo_inei` VALUES (152, '02', '07', '01', 'SAN LUIS');
INSERT INTO `ubigeo_inei` VALUES (153, '02', '07', '02', 'SAN NICOLAS');
INSERT INTO `ubigeo_inei` VALUES (154, '02', '07', '03', 'YAUYA');
INSERT INTO `ubigeo_inei` VALUES (155, '02', '08', '00', 'CASMA');
INSERT INTO `ubigeo_inei` VALUES (156, '02', '08', '01', 'CASMA');
INSERT INTO `ubigeo_inei` VALUES (157, '02', '08', '02', 'BUENA VISTA ALTA');
INSERT INTO `ubigeo_inei` VALUES (158, '02', '08', '03', 'COMANDANTE NOEL');
INSERT INTO `ubigeo_inei` VALUES (159, '02', '08', '04', 'YAUTAN');
INSERT INTO `ubigeo_inei` VALUES (160, '02', '09', '00', 'CORONGO');
INSERT INTO `ubigeo_inei` VALUES (161, '02', '09', '01', 'CORONGO');
INSERT INTO `ubigeo_inei` VALUES (162, '02', '09', '02', 'ACO');
INSERT INTO `ubigeo_inei` VALUES (163, '02', '09', '03', 'BAMBAS');
INSERT INTO `ubigeo_inei` VALUES (164, '02', '09', '04', 'CUSCA');
INSERT INTO `ubigeo_inei` VALUES (165, '02', '09', '05', 'LA PAMPA');
INSERT INTO `ubigeo_inei` VALUES (166, '02', '09', '06', 'YANAC');
INSERT INTO `ubigeo_inei` VALUES (167, '02', '09', '07', 'YUPAN');
INSERT INTO `ubigeo_inei` VALUES (168, '02', '10', '00', 'HUARI');
INSERT INTO `ubigeo_inei` VALUES (169, '02', '10', '01', 'HUARI');
INSERT INTO `ubigeo_inei` VALUES (170, '02', '10', '02', 'ANRA');
INSERT INTO `ubigeo_inei` VALUES (171, '02', '10', '03', 'CAJAY');
INSERT INTO `ubigeo_inei` VALUES (172, '02', '10', '04', 'CHAVIN DE HUANTAR');
INSERT INTO `ubigeo_inei` VALUES (173, '02', '10', '05', 'HUACACHI');
INSERT INTO `ubigeo_inei` VALUES (174, '02', '10', '06', 'HUACCHIS');
INSERT INTO `ubigeo_inei` VALUES (175, '02', '10', '07', 'HUACHIS');
INSERT INTO `ubigeo_inei` VALUES (176, '02', '10', '08', 'HUANTAR');
INSERT INTO `ubigeo_inei` VALUES (177, '02', '10', '09', 'MASIN');
INSERT INTO `ubigeo_inei` VALUES (178, '02', '10', '10', 'PAUCAS');
INSERT INTO `ubigeo_inei` VALUES (179, '02', '10', '11', 'PONTO');
INSERT INTO `ubigeo_inei` VALUES (180, '02', '10', '12', 'RAHUAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (181, '02', '10', '13', 'RAPAYAN');
INSERT INTO `ubigeo_inei` VALUES (182, '02', '10', '14', 'SAN MARCOS');
INSERT INTO `ubigeo_inei` VALUES (183, '02', '10', '15', 'SAN PEDRO DE CHANA');
INSERT INTO `ubigeo_inei` VALUES (184, '02', '10', '16', 'UCO');
INSERT INTO `ubigeo_inei` VALUES (185, '02', '11', '00', 'HUARMEY');
INSERT INTO `ubigeo_inei` VALUES (186, '02', '11', '01', 'HUARMEY');
INSERT INTO `ubigeo_inei` VALUES (187, '02', '11', '02', 'COCHAPETI');
INSERT INTO `ubigeo_inei` VALUES (188, '02', '11', '03', 'CULEBRAS');
INSERT INTO `ubigeo_inei` VALUES (189, '02', '11', '04', 'HUAYAN');
INSERT INTO `ubigeo_inei` VALUES (190, '02', '11', '05', 'MALVAS');
INSERT INTO `ubigeo_inei` VALUES (191, '02', '12', '00', 'HUAYLAS');
INSERT INTO `ubigeo_inei` VALUES (192, '02', '12', '01', 'CARAZ');
INSERT INTO `ubigeo_inei` VALUES (193, '02', '12', '02', 'HUALLANCA');
INSERT INTO `ubigeo_inei` VALUES (194, '02', '12', '03', 'HUATA');
INSERT INTO `ubigeo_inei` VALUES (195, '02', '12', '04', 'HUAYLAS');
INSERT INTO `ubigeo_inei` VALUES (196, '02', '12', '05', 'MATO');
INSERT INTO `ubigeo_inei` VALUES (197, '02', '12', '06', 'PAMPAROMAS');
INSERT INTO `ubigeo_inei` VALUES (198, '02', '12', '07', 'PUEBLO LIBRE');
INSERT INTO `ubigeo_inei` VALUES (199, '02', '12', '08', 'SANTA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (200, '02', '12', '09', 'SANTO TORIBIO');
INSERT INTO `ubigeo_inei` VALUES (201, '02', '12', '10', 'YURACMARCA');
INSERT INTO `ubigeo_inei` VALUES (202, '02', '13', '00', 'MARISCAL LUZURIAGA');
INSERT INTO `ubigeo_inei` VALUES (203, '02', '13', '01', 'PISCOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (204, '02', '13', '02', 'CASCA');
INSERT INTO `ubigeo_inei` VALUES (205, '02', '13', '03', 'ELEAZAR GUZMAN BARRON');
INSERT INTO `ubigeo_inei` VALUES (206, '02', '13', '04', 'FIDEL OLIVAS ESCUDERO');
INSERT INTO `ubigeo_inei` VALUES (207, '02', '13', '05', 'LLAMA');
INSERT INTO `ubigeo_inei` VALUES (208, '02', '13', '06', 'LLUMPA');
INSERT INTO `ubigeo_inei` VALUES (209, '02', '13', '07', 'LUCMA');
INSERT INTO `ubigeo_inei` VALUES (210, '02', '13', '08', 'MUSGA');
INSERT INTO `ubigeo_inei` VALUES (211, '02', '14', '00', 'OCROS');
INSERT INTO `ubigeo_inei` VALUES (212, '02', '14', '01', 'OCROS');
INSERT INTO `ubigeo_inei` VALUES (213, '02', '14', '02', 'ACAS');
INSERT INTO `ubigeo_inei` VALUES (214, '02', '14', '03', 'CAJAMARQUILLA');
INSERT INTO `ubigeo_inei` VALUES (215, '02', '14', '04', 'CARHUAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (216, '02', '14', '05', 'COCHAS');
INSERT INTO `ubigeo_inei` VALUES (217, '02', '14', '06', 'CONGAS');
INSERT INTO `ubigeo_inei` VALUES (218, '02', '14', '07', 'LLIPA');
INSERT INTO `ubigeo_inei` VALUES (219, '02', '14', '08', 'SAN CRISTOBAL DE RAJAN');
INSERT INTO `ubigeo_inei` VALUES (220, '02', '14', '09', 'SAN PEDRO');
INSERT INTO `ubigeo_inei` VALUES (221, '02', '14', '10', 'SANTIAGO DE CHILCAS');
INSERT INTO `ubigeo_inei` VALUES (222, '02', '15', '00', 'PALLASCA');
INSERT INTO `ubigeo_inei` VALUES (223, '02', '15', '01', 'CABANA');
INSERT INTO `ubigeo_inei` VALUES (224, '02', '15', '02', 'BOLOGNESI');
INSERT INTO `ubigeo_inei` VALUES (225, '02', '15', '03', 'CONCHUCOS');
INSERT INTO `ubigeo_inei` VALUES (226, '02', '15', '04', 'HUACASCHUQUE');
INSERT INTO `ubigeo_inei` VALUES (227, '02', '15', '05', 'HUANDOVAL');
INSERT INTO `ubigeo_inei` VALUES (228, '02', '15', '06', 'LACABAMBA');
INSERT INTO `ubigeo_inei` VALUES (229, '02', '15', '07', 'LLAPO');
INSERT INTO `ubigeo_inei` VALUES (230, '02', '15', '08', 'PALLASCA');
INSERT INTO `ubigeo_inei` VALUES (231, '02', '15', '09', 'PAMPAS');
INSERT INTO `ubigeo_inei` VALUES (232, '02', '15', '10', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (233, '02', '15', '11', 'TAUCA');
INSERT INTO `ubigeo_inei` VALUES (234, '02', '16', '00', 'POMABAMBA');
INSERT INTO `ubigeo_inei` VALUES (235, '02', '16', '01', 'POMABAMBA');
INSERT INTO `ubigeo_inei` VALUES (236, '02', '16', '02', 'HUAYLLAN');
INSERT INTO `ubigeo_inei` VALUES (237, '02', '16', '03', 'PAROBAMBA');
INSERT INTO `ubigeo_inei` VALUES (238, '02', '16', '04', 'QUINUABAMBA');
INSERT INTO `ubigeo_inei` VALUES (239, '02', '17', '00', 'RECUAY');
INSERT INTO `ubigeo_inei` VALUES (240, '02', '17', '01', 'RECUAY');
INSERT INTO `ubigeo_inei` VALUES (241, '02', '17', '02', 'CATAC');
INSERT INTO `ubigeo_inei` VALUES (242, '02', '17', '03', 'COTAPARACO');
INSERT INTO `ubigeo_inei` VALUES (243, '02', '17', '04', 'HUAYLLAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (244, '02', '17', '05', 'LLACLLIN');
INSERT INTO `ubigeo_inei` VALUES (245, '02', '17', '06', 'MARCA');
INSERT INTO `ubigeo_inei` VALUES (246, '02', '17', '07', 'PAMPAS CHICO');
INSERT INTO `ubigeo_inei` VALUES (247, '02', '17', '08', 'PARARIN');
INSERT INTO `ubigeo_inei` VALUES (248, '02', '17', '09', 'TAPACOCHA');
INSERT INTO `ubigeo_inei` VALUES (249, '02', '17', '10', 'TICAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (250, '02', '18', '00', 'SANTA');
INSERT INTO `ubigeo_inei` VALUES (251, '02', '18', '01', 'CHIMBOTE');
INSERT INTO `ubigeo_inei` VALUES (252, '02', '18', '02', 'CACERES DEL PERU');
INSERT INTO `ubigeo_inei` VALUES (253, '02', '18', '03', 'COISHCO');
INSERT INTO `ubigeo_inei` VALUES (254, '02', '18', '04', 'MACATE');
INSERT INTO `ubigeo_inei` VALUES (255, '02', '18', '05', 'MORO');
INSERT INTO `ubigeo_inei` VALUES (256, '02', '18', '06', 'NEPEÑA');
INSERT INTO `ubigeo_inei` VALUES (257, '02', '18', '07', 'SAMANCO');
INSERT INTO `ubigeo_inei` VALUES (258, '02', '18', '08', 'SANTA');
INSERT INTO `ubigeo_inei` VALUES (259, '02', '18', '09', 'NUEVO CHIMBOTE');
INSERT INTO `ubigeo_inei` VALUES (260, '02', '19', '00', 'SIHUAS');
INSERT INTO `ubigeo_inei` VALUES (261, '02', '19', '01', 'SIHUAS');
INSERT INTO `ubigeo_inei` VALUES (262, '02', '19', '02', 'ACOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (263, '02', '19', '03', 'ALFONSO UGARTE');
INSERT INTO `ubigeo_inei` VALUES (264, '02', '19', '04', 'CASHAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (265, '02', '19', '05', 'CHINGALPO');
INSERT INTO `ubigeo_inei` VALUES (266, '02', '19', '06', 'HUAYLLABAMBA');
INSERT INTO `ubigeo_inei` VALUES (267, '02', '19', '07', 'QUICHES');
INSERT INTO `ubigeo_inei` VALUES (268, '02', '19', '08', 'RAGASH');
INSERT INTO `ubigeo_inei` VALUES (269, '02', '19', '09', 'SAN JUAN');
INSERT INTO `ubigeo_inei` VALUES (270, '02', '19', '10', 'SICSIBAMBA');
INSERT INTO `ubigeo_inei` VALUES (271, '02', '20', '00', 'YUNGAY');
INSERT INTO `ubigeo_inei` VALUES (272, '02', '20', '01', 'YUNGAY');
INSERT INTO `ubigeo_inei` VALUES (273, '02', '20', '02', 'CASCAPARA');
INSERT INTO `ubigeo_inei` VALUES (274, '02', '20', '03', 'MANCOS');
INSERT INTO `ubigeo_inei` VALUES (275, '02', '20', '04', 'MATACOTO');
INSERT INTO `ubigeo_inei` VALUES (276, '02', '20', '05', 'QUILLO');
INSERT INTO `ubigeo_inei` VALUES (277, '02', '20', '06', 'RANRAHIRCA');
INSERT INTO `ubigeo_inei` VALUES (278, '02', '20', '07', 'SHUPLUY');
INSERT INTO `ubigeo_inei` VALUES (279, '02', '20', '08', 'YANAMA');
INSERT INTO `ubigeo_inei` VALUES (280, '03', '00', '00', 'APURIMAC');
INSERT INTO `ubigeo_inei` VALUES (281, '03', '01', '00', 'ABANCAY');
INSERT INTO `ubigeo_inei` VALUES (282, '03', '01', '01', 'ABANCAY');
INSERT INTO `ubigeo_inei` VALUES (283, '03', '01', '02', 'CHACOCHE');
INSERT INTO `ubigeo_inei` VALUES (284, '03', '01', '03', 'CIRCA');
INSERT INTO `ubigeo_inei` VALUES (285, '03', '01', '04', 'CURAHUASI');
INSERT INTO `ubigeo_inei` VALUES (286, '03', '01', '05', 'HUANIPACA');
INSERT INTO `ubigeo_inei` VALUES (287, '03', '01', '06', 'LAMBRAMA');
INSERT INTO `ubigeo_inei` VALUES (288, '03', '01', '07', 'PICHIRHUA');
INSERT INTO `ubigeo_inei` VALUES (289, '03', '01', '08', 'SAN PEDRO DE CACHORA');
INSERT INTO `ubigeo_inei` VALUES (290, '03', '01', '09', 'TAMBURCO');
INSERT INTO `ubigeo_inei` VALUES (291, '03', '02', '00', 'ANDAHUAYLAS');
INSERT INTO `ubigeo_inei` VALUES (292, '03', '02', '01', 'ANDAHUAYLAS');
INSERT INTO `ubigeo_inei` VALUES (293, '03', '02', '02', 'ANDARAPA');
INSERT INTO `ubigeo_inei` VALUES (294, '03', '02', '03', 'CHIARA');
INSERT INTO `ubigeo_inei` VALUES (295, '03', '02', '04', 'HUANCARAMA');
INSERT INTO `ubigeo_inei` VALUES (296, '03', '02', '05', 'HUANCARAY');
INSERT INTO `ubigeo_inei` VALUES (297, '03', '02', '06', 'HUAYANA');
INSERT INTO `ubigeo_inei` VALUES (298, '03', '02', '07', 'KISHUARA');
INSERT INTO `ubigeo_inei` VALUES (299, '03', '02', '08', 'PACOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (300, '03', '02', '09', 'PACUCHA');
INSERT INTO `ubigeo_inei` VALUES (301, '03', '02', '10', 'PAMPACHIRI');
INSERT INTO `ubigeo_inei` VALUES (302, '03', '02', '11', 'POMACOCHA');
INSERT INTO `ubigeo_inei` VALUES (303, '03', '02', '12', 'SAN ANTONIO DE CACHI');
INSERT INTO `ubigeo_inei` VALUES (304, '03', '02', '13', 'SAN JERONIMO');
INSERT INTO `ubigeo_inei` VALUES (305, '03', '02', '14', 'SAN MIGUEL DE CHACCRAMPA');
INSERT INTO `ubigeo_inei` VALUES (306, '03', '02', '15', 'SANTA MARIA DE CHICMO');
INSERT INTO `ubigeo_inei` VALUES (307, '03', '02', '16', 'TALAVERA');
INSERT INTO `ubigeo_inei` VALUES (308, '03', '02', '17', 'TUMAY HUARACA');
INSERT INTO `ubigeo_inei` VALUES (309, '03', '02', '18', 'TURPO');
INSERT INTO `ubigeo_inei` VALUES (310, '03', '02', '19', 'KAQUIABAMBA');
INSERT INTO `ubigeo_inei` VALUES (311, '03', '03', '00', 'ANTABAMBA');
INSERT INTO `ubigeo_inei` VALUES (312, '03', '03', '01', 'ANTABAMBA');
INSERT INTO `ubigeo_inei` VALUES (313, '03', '03', '02', 'EL ORO');
INSERT INTO `ubigeo_inei` VALUES (314, '03', '03', '03', 'HUAQUIRCA');
INSERT INTO `ubigeo_inei` VALUES (315, '03', '03', '04', 'JUAN ESPINOZA MEDRANO');
INSERT INTO `ubigeo_inei` VALUES (316, '03', '03', '05', 'OROPESA');
INSERT INTO `ubigeo_inei` VALUES (317, '03', '03', '06', 'PACHACONAS');
INSERT INTO `ubigeo_inei` VALUES (318, '03', '03', '07', 'SABAINO');
INSERT INTO `ubigeo_inei` VALUES (319, '03', '04', '00', 'AYMARAES');
INSERT INTO `ubigeo_inei` VALUES (320, '03', '04', '01', 'CHALHUANCA');
INSERT INTO `ubigeo_inei` VALUES (321, '03', '04', '02', 'CAPAYA');
INSERT INTO `ubigeo_inei` VALUES (322, '03', '04', '03', 'CARAYBAMBA');
INSERT INTO `ubigeo_inei` VALUES (323, '03', '04', '04', 'CHAPIMARCA');
INSERT INTO `ubigeo_inei` VALUES (324, '03', '04', '05', 'COLCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (325, '03', '04', '06', 'COTARUSE');
INSERT INTO `ubigeo_inei` VALUES (326, '03', '04', '07', 'HUAYLLO');
INSERT INTO `ubigeo_inei` VALUES (327, '03', '04', '08', 'JUSTO APU SAHUARAURA');
INSERT INTO `ubigeo_inei` VALUES (328, '03', '04', '09', 'LUCRE');
INSERT INTO `ubigeo_inei` VALUES (329, '03', '04', '10', 'POCOHUANCA');
INSERT INTO `ubigeo_inei` VALUES (330, '03', '04', '11', 'SAN JUAN DE CHACÑA');
INSERT INTO `ubigeo_inei` VALUES (331, '03', '04', '12', 'SAÑAYCA');
INSERT INTO `ubigeo_inei` VALUES (332, '03', '04', '13', 'SORAYA');
INSERT INTO `ubigeo_inei` VALUES (333, '03', '04', '14', 'TAPAIRIHUA');
INSERT INTO `ubigeo_inei` VALUES (334, '03', '04', '15', 'TINTAY');
INSERT INTO `ubigeo_inei` VALUES (335, '03', '04', '16', 'TORAYA');
INSERT INTO `ubigeo_inei` VALUES (336, '03', '04', '17', 'YANACA');
INSERT INTO `ubigeo_inei` VALUES (337, '03', '05', '00', 'COTABAMBAS');
INSERT INTO `ubigeo_inei` VALUES (338, '03', '05', '01', 'TAMBOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (339, '03', '05', '02', 'COTABAMBAS');
INSERT INTO `ubigeo_inei` VALUES (340, '03', '05', '03', 'COYLLURQUI');
INSERT INTO `ubigeo_inei` VALUES (341, '03', '05', '04', 'HAQUIRA');
INSERT INTO `ubigeo_inei` VALUES (342, '03', '05', '05', 'MARA');
INSERT INTO `ubigeo_inei` VALUES (343, '03', '05', '06', 'CHALLHUAHUACHO');
INSERT INTO `ubigeo_inei` VALUES (344, '03', '06', '00', 'CHINCHEROS');
INSERT INTO `ubigeo_inei` VALUES (345, '03', '06', '01', 'CHINCHEROS');
INSERT INTO `ubigeo_inei` VALUES (346, '03', '06', '02', 'ANCO-HUALLO');
INSERT INTO `ubigeo_inei` VALUES (347, '03', '06', '03', 'COCHARCAS');
INSERT INTO `ubigeo_inei` VALUES (348, '03', '06', '04', 'HUACCANA');
INSERT INTO `ubigeo_inei` VALUES (349, '03', '06', '05', 'OCOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (350, '03', '06', '06', 'ONGOY');
INSERT INTO `ubigeo_inei` VALUES (351, '03', '06', '07', 'URANMARCA');
INSERT INTO `ubigeo_inei` VALUES (352, '03', '06', '08', 'RANRACANCHA');
INSERT INTO `ubigeo_inei` VALUES (353, '03', '07', '00', 'GRAU');
INSERT INTO `ubigeo_inei` VALUES (354, '03', '07', '01', 'CHUQUIBAMBILLA');
INSERT INTO `ubigeo_inei` VALUES (355, '03', '07', '02', 'CURPAHUASI');
INSERT INTO `ubigeo_inei` VALUES (356, '03', '07', '03', 'GAMARRA');
INSERT INTO `ubigeo_inei` VALUES (357, '03', '07', '04', 'HUAYLLATI');
INSERT INTO `ubigeo_inei` VALUES (358, '03', '07', '05', 'MAMARA');
INSERT INTO `ubigeo_inei` VALUES (359, '03', '07', '06', 'MICAELA BASTIDAS');
INSERT INTO `ubigeo_inei` VALUES (360, '03', '07', '07', 'PATAYPAMPA');
INSERT INTO `ubigeo_inei` VALUES (361, '03', '07', '08', 'PROGRESO');
INSERT INTO `ubigeo_inei` VALUES (362, '03', '07', '09', 'SAN ANTONIO');
INSERT INTO `ubigeo_inei` VALUES (363, '03', '07', '10', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (364, '03', '07', '11', 'TURPAY');
INSERT INTO `ubigeo_inei` VALUES (365, '03', '07', '12', 'VILCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (366, '03', '07', '13', 'VIRUNDO');
INSERT INTO `ubigeo_inei` VALUES (367, '03', '07', '14', 'CURASCO');
INSERT INTO `ubigeo_inei` VALUES (368, '04', '00', '00', 'AREQUIPA');
INSERT INTO `ubigeo_inei` VALUES (369, '04', '01', '00', 'AREQUIPA');
INSERT INTO `ubigeo_inei` VALUES (370, '04', '01', '01', 'AREQUIPA');
INSERT INTO `ubigeo_inei` VALUES (371, '04', '01', '02', 'ALTO SELVA ALEGRE');
INSERT INTO `ubigeo_inei` VALUES (372, '04', '01', '03', 'CAYMA');
INSERT INTO `ubigeo_inei` VALUES (373, '04', '01', '04', 'CERRO COLORADO');
INSERT INTO `ubigeo_inei` VALUES (374, '04', '01', '05', 'CHARACATO');
INSERT INTO `ubigeo_inei` VALUES (375, '04', '01', '06', 'CHIGUATA');
INSERT INTO `ubigeo_inei` VALUES (376, '04', '01', '07', 'JACOBO HUNTER');
INSERT INTO `ubigeo_inei` VALUES (377, '04', '01', '08', 'LA JOYA');
INSERT INTO `ubigeo_inei` VALUES (378, '04', '01', '09', 'MARIANO MELGAR');
INSERT INTO `ubigeo_inei` VALUES (379, '04', '01', '10', 'MIRAFLORES');
INSERT INTO `ubigeo_inei` VALUES (380, '04', '01', '11', 'MOLLEBAYA');
INSERT INTO `ubigeo_inei` VALUES (381, '04', '01', '12', 'PAUCARPATA');
INSERT INTO `ubigeo_inei` VALUES (382, '04', '01', '13', 'POCSI');
INSERT INTO `ubigeo_inei` VALUES (383, '04', '01', '14', 'POLOBAYA');
INSERT INTO `ubigeo_inei` VALUES (384, '04', '01', '15', 'QUEQUEÑA');
INSERT INTO `ubigeo_inei` VALUES (385, '04', '01', '16', 'SABANDIA');
INSERT INTO `ubigeo_inei` VALUES (386, '04', '01', '17', 'SACHACA');
INSERT INTO `ubigeo_inei` VALUES (387, '04', '01', '18', 'SAN JUAN DE SIGUAS');
INSERT INTO `ubigeo_inei` VALUES (388, '04', '01', '19', 'SAN JUAN DE TARUCANI');
INSERT INTO `ubigeo_inei` VALUES (389, '04', '01', '20', 'SANTA ISABEL DE SIGUAS');
INSERT INTO `ubigeo_inei` VALUES (390, '04', '01', '21', 'SANTA RITA DE SIGUAS');
INSERT INTO `ubigeo_inei` VALUES (391, '04', '01', '22', 'SOCABAYA');
INSERT INTO `ubigeo_inei` VALUES (392, '04', '01', '23', 'TIABAYA');
INSERT INTO `ubigeo_inei` VALUES (393, '04', '01', '24', 'UCHUMAYO');
INSERT INTO `ubigeo_inei` VALUES (394, '04', '01', '25', 'VITOR');
INSERT INTO `ubigeo_inei` VALUES (395, '04', '01', '26', 'YANAHUARA');
INSERT INTO `ubigeo_inei` VALUES (396, '04', '01', '27', 'YARABAMBA');
INSERT INTO `ubigeo_inei` VALUES (397, '04', '01', '28', 'YURA');
INSERT INTO `ubigeo_inei` VALUES (398, '04', '01', '29', 'JOSE LUIS BUSTAMANTE Y RIVERO');
INSERT INTO `ubigeo_inei` VALUES (399, '04', '02', '00', 'CAMANA');
INSERT INTO `ubigeo_inei` VALUES (400, '04', '02', '01', 'CAMANA');
INSERT INTO `ubigeo_inei` VALUES (401, '04', '02', '02', 'JOSE MARIA QUIMPER');
INSERT INTO `ubigeo_inei` VALUES (402, '04', '02', '03', 'MARIANO NICOLAS VALCARCEL');
INSERT INTO `ubigeo_inei` VALUES (403, '04', '02', '04', 'MARISCAL CACERES');
INSERT INTO `ubigeo_inei` VALUES (404, '04', '02', '05', 'NICOLAS DE PIEROLA');
INSERT INTO `ubigeo_inei` VALUES (405, '04', '02', '06', 'OCOÑA');
INSERT INTO `ubigeo_inei` VALUES (406, '04', '02', '07', 'QUILCA');
INSERT INTO `ubigeo_inei` VALUES (407, '04', '02', '08', 'SAMUEL PASTOR');
INSERT INTO `ubigeo_inei` VALUES (408, '04', '03', '00', 'CARAVELI');
INSERT INTO `ubigeo_inei` VALUES (409, '04', '03', '01', 'CARAVELI');
INSERT INTO `ubigeo_inei` VALUES (410, '04', '03', '02', 'ACARI');
INSERT INTO `ubigeo_inei` VALUES (411, '04', '03', '03', 'ATICO');
INSERT INTO `ubigeo_inei` VALUES (412, '04', '03', '04', 'ATIQUIPA');
INSERT INTO `ubigeo_inei` VALUES (413, '04', '03', '05', 'BELLA UNION');
INSERT INTO `ubigeo_inei` VALUES (414, '04', '03', '06', 'CAHUACHO');
INSERT INTO `ubigeo_inei` VALUES (415, '04', '03', '07', 'CHALA');
INSERT INTO `ubigeo_inei` VALUES (416, '04', '03', '08', 'CHAPARRA');
INSERT INTO `ubigeo_inei` VALUES (417, '04', '03', '09', 'HUANUHUANU');
INSERT INTO `ubigeo_inei` VALUES (418, '04', '03', '10', 'JAQUI');
INSERT INTO `ubigeo_inei` VALUES (419, '04', '03', '11', 'LOMAS');
INSERT INTO `ubigeo_inei` VALUES (420, '04', '03', '12', 'QUICACHA');
INSERT INTO `ubigeo_inei` VALUES (421, '04', '03', '13', 'YAUCA');
INSERT INTO `ubigeo_inei` VALUES (422, '04', '04', '00', 'CASTILLA');
INSERT INTO `ubigeo_inei` VALUES (423, '04', '04', '01', 'APLAO');
INSERT INTO `ubigeo_inei` VALUES (424, '04', '04', '02', 'ANDAGUA');
INSERT INTO `ubigeo_inei` VALUES (425, '04', '04', '03', 'AYO');
INSERT INTO `ubigeo_inei` VALUES (426, '04', '04', '04', 'CHACHAS');
INSERT INTO `ubigeo_inei` VALUES (427, '04', '04', '05', 'CHILCAYMARCA');
INSERT INTO `ubigeo_inei` VALUES (428, '04', '04', '06', 'CHOCO');
INSERT INTO `ubigeo_inei` VALUES (429, '04', '04', '07', 'HUANCARQUI');
INSERT INTO `ubigeo_inei` VALUES (430, '04', '04', '08', 'MACHAGUAY');
INSERT INTO `ubigeo_inei` VALUES (431, '04', '04', '09', 'ORCOPAMPA');
INSERT INTO `ubigeo_inei` VALUES (432, '04', '04', '10', 'PAMPACOLCA');
INSERT INTO `ubigeo_inei` VALUES (433, '04', '04', '11', 'TIPAN');
INSERT INTO `ubigeo_inei` VALUES (434, '04', '04', '12', 'UÑON');
INSERT INTO `ubigeo_inei` VALUES (435, '04', '04', '13', 'URACA');
INSERT INTO `ubigeo_inei` VALUES (436, '04', '04', '14', 'VIRACO');
INSERT INTO `ubigeo_inei` VALUES (437, '04', '05', '00', 'CAYLLOMA');
INSERT INTO `ubigeo_inei` VALUES (438, '04', '05', '01', 'CHIVAY');
INSERT INTO `ubigeo_inei` VALUES (439, '04', '05', '02', 'ACHOMA');
INSERT INTO `ubigeo_inei` VALUES (440, '04', '05', '03', 'CABANACONDE');
INSERT INTO `ubigeo_inei` VALUES (441, '04', '05', '04', 'CALLALLI');
INSERT INTO `ubigeo_inei` VALUES (442, '04', '05', '05', 'CAYLLOMA');
INSERT INTO `ubigeo_inei` VALUES (443, '04', '05', '06', 'COPORAQUE');
INSERT INTO `ubigeo_inei` VALUES (444, '04', '05', '07', 'HUAMBO');
INSERT INTO `ubigeo_inei` VALUES (445, '04', '05', '08', 'HUANCA');
INSERT INTO `ubigeo_inei` VALUES (446, '04', '05', '09', 'ICHUPAMPA');
INSERT INTO `ubigeo_inei` VALUES (447, '04', '05', '10', 'LARI');
INSERT INTO `ubigeo_inei` VALUES (448, '04', '05', '11', 'LLUTA');
INSERT INTO `ubigeo_inei` VALUES (449, '04', '05', '12', 'MACA');
INSERT INTO `ubigeo_inei` VALUES (450, '04', '05', '13', 'MADRIGAL');
INSERT INTO `ubigeo_inei` VALUES (451, '04', '05', '14', 'SAN ANTONIO DE CHUCA');
INSERT INTO `ubigeo_inei` VALUES (452, '04', '05', '15', 'SIBAYO');
INSERT INTO `ubigeo_inei` VALUES (453, '04', '05', '16', 'TAPAY');
INSERT INTO `ubigeo_inei` VALUES (454, '04', '05', '17', 'TISCO');
INSERT INTO `ubigeo_inei` VALUES (455, '04', '05', '18', 'TUTI');
INSERT INTO `ubigeo_inei` VALUES (456, '04', '05', '19', 'YANQUE');
INSERT INTO `ubigeo_inei` VALUES (457, '04', '05', '20', 'MAJES');
INSERT INTO `ubigeo_inei` VALUES (458, '04', '06', '00', 'CONDESUYOS');
INSERT INTO `ubigeo_inei` VALUES (459, '04', '06', '01', 'CHUQUIBAMBA');
INSERT INTO `ubigeo_inei` VALUES (460, '04', '06', '02', 'ANDARAY');
INSERT INTO `ubigeo_inei` VALUES (461, '04', '06', '03', 'CAYARANI');
INSERT INTO `ubigeo_inei` VALUES (462, '04', '06', '04', 'CHICHAS');
INSERT INTO `ubigeo_inei` VALUES (463, '04', '06', '05', 'IRAY');
INSERT INTO `ubigeo_inei` VALUES (464, '04', '06', '06', 'RIO GRANDE');
INSERT INTO `ubigeo_inei` VALUES (465, '04', '06', '07', 'SALAMANCA');
INSERT INTO `ubigeo_inei` VALUES (466, '04', '06', '08', 'YANAQUIHUA');
INSERT INTO `ubigeo_inei` VALUES (467, '04', '07', '00', 'ISLAY');
INSERT INTO `ubigeo_inei` VALUES (468, '04', '07', '01', 'MOLLENDO');
INSERT INTO `ubigeo_inei` VALUES (469, '04', '07', '02', 'COCACHACRA');
INSERT INTO `ubigeo_inei` VALUES (470, '04', '07', '03', 'DEAN VALDIVIA');
INSERT INTO `ubigeo_inei` VALUES (471, '04', '07', '04', 'ISLAY');
INSERT INTO `ubigeo_inei` VALUES (472, '04', '07', '05', 'MEJIA');
INSERT INTO `ubigeo_inei` VALUES (473, '04', '07', '06', 'PUNTA DE BOMBON');
INSERT INTO `ubigeo_inei` VALUES (474, '04', '08', '00', 'LA UNION');
INSERT INTO `ubigeo_inei` VALUES (475, '04', '08', '01', 'COTAHUASI');
INSERT INTO `ubigeo_inei` VALUES (476, '04', '08', '02', 'ALCA');
INSERT INTO `ubigeo_inei` VALUES (477, '04', '08', '03', 'CHARCANA');
INSERT INTO `ubigeo_inei` VALUES (478, '04', '08', '04', 'HUAYNACOTAS');
INSERT INTO `ubigeo_inei` VALUES (479, '04', '08', '05', 'PAMPAMARCA');
INSERT INTO `ubigeo_inei` VALUES (480, '04', '08', '06', 'PUYCA');
INSERT INTO `ubigeo_inei` VALUES (481, '04', '08', '07', 'QUECHUALLA');
INSERT INTO `ubigeo_inei` VALUES (482, '04', '08', '08', 'SAYLA');
INSERT INTO `ubigeo_inei` VALUES (483, '04', '08', '09', 'TAURIA');
INSERT INTO `ubigeo_inei` VALUES (484, '04', '08', '10', 'TOMEPAMPA');
INSERT INTO `ubigeo_inei` VALUES (485, '04', '08', '11', 'TORO');
INSERT INTO `ubigeo_inei` VALUES (486, '05', '00', '00', 'AYACUCHO');
INSERT INTO `ubigeo_inei` VALUES (487, '05', '01', '00', 'HUAMANGA');
INSERT INTO `ubigeo_inei` VALUES (488, '05', '01', '01', 'AYACUCHO');
INSERT INTO `ubigeo_inei` VALUES (489, '05', '01', '02', 'ACOCRO');
INSERT INTO `ubigeo_inei` VALUES (490, '05', '01', '03', 'ACOS VINCHOS');
INSERT INTO `ubigeo_inei` VALUES (491, '05', '01', '04', 'CARMEN ALTO');
INSERT INTO `ubigeo_inei` VALUES (492, '05', '01', '05', 'CHIARA');
INSERT INTO `ubigeo_inei` VALUES (493, '05', '01', '06', 'OCROS');
INSERT INTO `ubigeo_inei` VALUES (494, '05', '01', '07', 'PACAYCASA');
INSERT INTO `ubigeo_inei` VALUES (495, '05', '01', '08', 'QUINUA');
INSERT INTO `ubigeo_inei` VALUES (496, '05', '01', '09', 'SAN JOSE DE TICLLAS');
INSERT INTO `ubigeo_inei` VALUES (497, '05', '01', '10', 'SAN JUAN BAUTISTA');
INSERT INTO `ubigeo_inei` VALUES (498, '05', '01', '11', 'SANTIAGO DE PISCHA');
INSERT INTO `ubigeo_inei` VALUES (499, '05', '01', '12', 'SOCOS');
INSERT INTO `ubigeo_inei` VALUES (500, '05', '01', '13', 'TAMBILLO');
INSERT INTO `ubigeo_inei` VALUES (501, '05', '01', '14', 'VINCHOS');
INSERT INTO `ubigeo_inei` VALUES (502, '05', '01', '15', 'JESÚS NAZARENO');
INSERT INTO `ubigeo_inei` VALUES (503, '05', '01', '16', 'ANDRÉS AVELINO CÁCERES DORREGAY');
INSERT INTO `ubigeo_inei` VALUES (504, '05', '02', '00', 'CANGALLO');
INSERT INTO `ubigeo_inei` VALUES (505, '05', '02', '01', 'CANGALLO');
INSERT INTO `ubigeo_inei` VALUES (506, '05', '02', '02', 'CHUSCHI');
INSERT INTO `ubigeo_inei` VALUES (507, '05', '02', '03', 'LOS MOROCHUCOS');
INSERT INTO `ubigeo_inei` VALUES (508, '05', '02', '04', 'MARIA PARADO DE BELLIDO');
INSERT INTO `ubigeo_inei` VALUES (509, '05', '02', '05', 'PARAS');
INSERT INTO `ubigeo_inei` VALUES (510, '05', '02', '06', 'TOTOS');
INSERT INTO `ubigeo_inei` VALUES (511, '05', '03', '00', 'HUANCA SANCOS');
INSERT INTO `ubigeo_inei` VALUES (512, '05', '03', '01', 'SANCOS');
INSERT INTO `ubigeo_inei` VALUES (513, '05', '03', '02', 'CARAPO');
INSERT INTO `ubigeo_inei` VALUES (514, '05', '03', '03', 'SACSAMARCA');
INSERT INTO `ubigeo_inei` VALUES (515, '05', '03', '04', 'SANTIAGO DE LUCANAMARCA');
INSERT INTO `ubigeo_inei` VALUES (516, '05', '04', '00', 'HUANTA');
INSERT INTO `ubigeo_inei` VALUES (517, '05', '04', '01', 'HUANTA');
INSERT INTO `ubigeo_inei` VALUES (518, '05', '04', '02', 'AYAHUANCO');
INSERT INTO `ubigeo_inei` VALUES (519, '05', '04', '03', 'HUAMANGUILLA');
INSERT INTO `ubigeo_inei` VALUES (520, '05', '04', '04', 'IGUAIN');
INSERT INTO `ubigeo_inei` VALUES (521, '05', '04', '05', 'LURICOCHA');
INSERT INTO `ubigeo_inei` VALUES (522, '05', '04', '06', 'SANTILLANA');
INSERT INTO `ubigeo_inei` VALUES (523, '05', '04', '07', 'SIVIA');
INSERT INTO `ubigeo_inei` VALUES (524, '05', '04', '08', 'LLOCHEGUA');
INSERT INTO `ubigeo_inei` VALUES (525, '05', '04', '09', 'CANAYRE');
INSERT INTO `ubigeo_inei` VALUES (526, '05', '04', '10', 'UCHURACCAY');
INSERT INTO `ubigeo_inei` VALUES (527, '05', '04', '11', 'PUCACOLPA');
INSERT INTO `ubigeo_inei` VALUES (528, '05', '05', '00', 'LA MAR');
INSERT INTO `ubigeo_inei` VALUES (529, '05', '05', '01', 'SAN MIGUEL');
INSERT INTO `ubigeo_inei` VALUES (530, '05', '05', '02', 'ANCO');
INSERT INTO `ubigeo_inei` VALUES (531, '05', '05', '03', 'AYNA');
INSERT INTO `ubigeo_inei` VALUES (532, '05', '05', '04', 'CHILCAS');
INSERT INTO `ubigeo_inei` VALUES (533, '05', '05', '05', 'CHUNGUI');
INSERT INTO `ubigeo_inei` VALUES (534, '05', '05', '06', 'LUIS CARRANZA');
INSERT INTO `ubigeo_inei` VALUES (535, '05', '05', '07', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (536, '05', '05', '08', 'TAMBO');
INSERT INTO `ubigeo_inei` VALUES (537, '05', '05', '09', 'SAMUGARI');
INSERT INTO `ubigeo_inei` VALUES (538, '05', '05', '10', 'ANCHIHUAY');
INSERT INTO `ubigeo_inei` VALUES (539, '05', '06', '00', 'LUCANAS');
INSERT INTO `ubigeo_inei` VALUES (540, '05', '06', '01', 'PUQUIO');
INSERT INTO `ubigeo_inei` VALUES (541, '05', '06', '02', 'AUCARA');
INSERT INTO `ubigeo_inei` VALUES (542, '05', '06', '03', 'CABANA');
INSERT INTO `ubigeo_inei` VALUES (543, '05', '06', '04', 'CARMEN SALCEDO');
INSERT INTO `ubigeo_inei` VALUES (544, '05', '06', '05', 'CHAVIÑA');
INSERT INTO `ubigeo_inei` VALUES (545, '05', '06', '06', 'CHIPAO');
INSERT INTO `ubigeo_inei` VALUES (546, '05', '06', '07', 'HUAC-HUAS');
INSERT INTO `ubigeo_inei` VALUES (547, '05', '06', '08', 'LARAMATE');
INSERT INTO `ubigeo_inei` VALUES (548, '05', '06', '09', 'LEONCIO PRADO');
INSERT INTO `ubigeo_inei` VALUES (549, '05', '06', '10', 'LLAUTA');
INSERT INTO `ubigeo_inei` VALUES (550, '05', '06', '11', 'LUCANAS');
INSERT INTO `ubigeo_inei` VALUES (551, '05', '06', '12', 'OCAÑA');
INSERT INTO `ubigeo_inei` VALUES (552, '05', '06', '13', 'OTOCA');
INSERT INTO `ubigeo_inei` VALUES (553, '05', '06', '14', 'SAISA');
INSERT INTO `ubigeo_inei` VALUES (554, '05', '06', '15', 'SAN CRISTOBAL');
INSERT INTO `ubigeo_inei` VALUES (555, '05', '06', '16', 'SAN JUAN');
INSERT INTO `ubigeo_inei` VALUES (556, '05', '06', '17', 'SAN PEDRO');
INSERT INTO `ubigeo_inei` VALUES (557, '05', '06', '18', 'SAN PEDRO DE PALCO');
INSERT INTO `ubigeo_inei` VALUES (558, '05', '06', '19', 'SANCOS');
INSERT INTO `ubigeo_inei` VALUES (559, '05', '06', '20', 'SANTA ANA DE HUAYCAHUACHO');
INSERT INTO `ubigeo_inei` VALUES (560, '05', '06', '21', 'SANTA LUCIA');
INSERT INTO `ubigeo_inei` VALUES (561, '05', '07', '00', 'PARINACOCHAS');
INSERT INTO `ubigeo_inei` VALUES (562, '05', '07', '01', 'CORACORA');
INSERT INTO `ubigeo_inei` VALUES (563, '05', '07', '02', 'CHUMPI');
INSERT INTO `ubigeo_inei` VALUES (564, '05', '07', '03', 'CORONEL CASTAÑEDA');
INSERT INTO `ubigeo_inei` VALUES (565, '05', '07', '04', 'PACAPAUSA');
INSERT INTO `ubigeo_inei` VALUES (566, '05', '07', '05', 'PULLO');
INSERT INTO `ubigeo_inei` VALUES (567, '05', '07', '06', 'PUYUSCA');
INSERT INTO `ubigeo_inei` VALUES (568, '05', '07', '07', 'SAN FRANCISCO DE RAVACAYCO');
INSERT INTO `ubigeo_inei` VALUES (569, '05', '07', '08', 'UPAHUACHO');
INSERT INTO `ubigeo_inei` VALUES (570, '05', '08', '00', 'PAUCAR DEL SARA SARA');
INSERT INTO `ubigeo_inei` VALUES (571, '05', '08', '01', 'PAUSA');
INSERT INTO `ubigeo_inei` VALUES (572, '05', '08', '02', 'COLTA');
INSERT INTO `ubigeo_inei` VALUES (573, '05', '08', '03', 'CORCULLA');
INSERT INTO `ubigeo_inei` VALUES (574, '05', '08', '04', 'LAMPA');
INSERT INTO `ubigeo_inei` VALUES (575, '05', '08', '05', 'MARCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (576, '05', '08', '06', 'OYOLO');
INSERT INTO `ubigeo_inei` VALUES (577, '05', '08', '07', 'PARARCA');
INSERT INTO `ubigeo_inei` VALUES (578, '05', '08', '08', 'SAN JAVIER DE ALPABAMBA');
INSERT INTO `ubigeo_inei` VALUES (579, '05', '08', '09', 'SAN JOSE DE USHUA');
INSERT INTO `ubigeo_inei` VALUES (580, '05', '08', '10', 'SARA SARA');
INSERT INTO `ubigeo_inei` VALUES (581, '05', '09', '00', 'SUCRE');
INSERT INTO `ubigeo_inei` VALUES (582, '05', '09', '01', 'QUEROBAMBA');
INSERT INTO `ubigeo_inei` VALUES (583, '05', '09', '02', 'BELEN');
INSERT INTO `ubigeo_inei` VALUES (584, '05', '09', '03', 'CHALCOS');
INSERT INTO `ubigeo_inei` VALUES (585, '05', '09', '04', 'CHILCAYOC');
INSERT INTO `ubigeo_inei` VALUES (586, '05', '09', '05', 'HUACAÑA');
INSERT INTO `ubigeo_inei` VALUES (587, '05', '09', '06', 'MORCOLLA');
INSERT INTO `ubigeo_inei` VALUES (588, '05', '09', '07', 'PAICO');
INSERT INTO `ubigeo_inei` VALUES (589, '05', '09', '08', 'SAN PEDRO DE LARCAY');
INSERT INTO `ubigeo_inei` VALUES (590, '05', '09', '09', 'SAN SALVADOR DE QUIJE');
INSERT INTO `ubigeo_inei` VALUES (591, '05', '09', '10', 'SANTIAGO DE PAUCARAY');
INSERT INTO `ubigeo_inei` VALUES (592, '05', '09', '11', 'SORAS');
INSERT INTO `ubigeo_inei` VALUES (593, '05', '10', '00', 'VICTOR FAJARDO');
INSERT INTO `ubigeo_inei` VALUES (594, '05', '10', '01', 'HUANCAPI');
INSERT INTO `ubigeo_inei` VALUES (595, '05', '10', '02', 'ALCAMENCA');
INSERT INTO `ubigeo_inei` VALUES (596, '05', '10', '03', 'APONGO');
INSERT INTO `ubigeo_inei` VALUES (597, '05', '10', '04', 'ASQUIPATA');
INSERT INTO `ubigeo_inei` VALUES (598, '05', '10', '05', 'CANARIA');
INSERT INTO `ubigeo_inei` VALUES (599, '05', '10', '06', 'CAYARA');
INSERT INTO `ubigeo_inei` VALUES (600, '05', '10', '07', 'COLCA');
INSERT INTO `ubigeo_inei` VALUES (601, '05', '10', '08', 'HUAMANQUIQUIA');
INSERT INTO `ubigeo_inei` VALUES (602, '05', '10', '09', 'HUANCARAYLLA');
INSERT INTO `ubigeo_inei` VALUES (603, '05', '10', '10', 'HUAYA');
INSERT INTO `ubigeo_inei` VALUES (604, '05', '10', '11', 'SARHUA');
INSERT INTO `ubigeo_inei` VALUES (605, '05', '10', '12', 'VILCANCHOS');
INSERT INTO `ubigeo_inei` VALUES (606, '05', '11', '00', 'VILCAS HUAMAN');
INSERT INTO `ubigeo_inei` VALUES (607, '05', '11', '01', 'VILCAS HUAMAN');
INSERT INTO `ubigeo_inei` VALUES (608, '05', '11', '02', 'ACCOMARCA');
INSERT INTO `ubigeo_inei` VALUES (609, '05', '11', '03', 'CARHUANCA');
INSERT INTO `ubigeo_inei` VALUES (610, '05', '11', '04', 'CONCEPCION');
INSERT INTO `ubigeo_inei` VALUES (611, '05', '11', '05', 'HUAMBALPA');
INSERT INTO `ubigeo_inei` VALUES (612, '05', '11', '06', 'INDEPENDENCIA');
INSERT INTO `ubigeo_inei` VALUES (613, '05', '11', '07', 'SAURAMA');
INSERT INTO `ubigeo_inei` VALUES (614, '05', '11', '08', 'VISCHONGO');
INSERT INTO `ubigeo_inei` VALUES (615, '06', '00', '00', 'CAJAMARCA');
INSERT INTO `ubigeo_inei` VALUES (616, '06', '01', '00', 'CAJAMARCA');
INSERT INTO `ubigeo_inei` VALUES (617, '06', '01', '01', 'CAJAMARCA');
INSERT INTO `ubigeo_inei` VALUES (618, '06', '01', '02', 'ASUNCION');
INSERT INTO `ubigeo_inei` VALUES (619, '06', '01', '03', 'CHETILLA');
INSERT INTO `ubigeo_inei` VALUES (620, '06', '01', '04', 'COSPAN');
INSERT INTO `ubigeo_inei` VALUES (621, '06', '01', '05', 'ENCAÑADA');
INSERT INTO `ubigeo_inei` VALUES (622, '06', '01', '06', 'JESUS');
INSERT INTO `ubigeo_inei` VALUES (623, '06', '01', '07', 'LLACANORA');
INSERT INTO `ubigeo_inei` VALUES (624, '06', '01', '08', 'LOS BAÑOS DEL INCA');
INSERT INTO `ubigeo_inei` VALUES (625, '06', '01', '09', 'MAGDALENA');
INSERT INTO `ubigeo_inei` VALUES (626, '06', '01', '10', 'MATARA');
INSERT INTO `ubigeo_inei` VALUES (627, '06', '01', '11', 'NAMORA');
INSERT INTO `ubigeo_inei` VALUES (628, '06', '01', '12', 'SAN JUAN');
INSERT INTO `ubigeo_inei` VALUES (629, '06', '02', '00', 'CAJABAMBA');
INSERT INTO `ubigeo_inei` VALUES (630, '06', '02', '01', 'CAJABAMBA');
INSERT INTO `ubigeo_inei` VALUES (631, '06', '02', '02', 'CACHACHI');
INSERT INTO `ubigeo_inei` VALUES (632, '06', '02', '03', 'CONDEBAMBA');
INSERT INTO `ubigeo_inei` VALUES (633, '06', '02', '04', 'SITACOCHA');
INSERT INTO `ubigeo_inei` VALUES (634, '06', '03', '00', 'CELENDIN');
INSERT INTO `ubigeo_inei` VALUES (635, '06', '03', '01', 'CELENDIN');
INSERT INTO `ubigeo_inei` VALUES (636, '06', '03', '02', 'CHUMUCH');
INSERT INTO `ubigeo_inei` VALUES (637, '06', '03', '03', 'CORTEGANA');
INSERT INTO `ubigeo_inei` VALUES (638, '06', '03', '04', 'HUASMIN');
INSERT INTO `ubigeo_inei` VALUES (639, '06', '03', '05', 'JORGE CHAVEZ');
INSERT INTO `ubigeo_inei` VALUES (640, '06', '03', '06', 'JOSE GALVEZ');
INSERT INTO `ubigeo_inei` VALUES (641, '06', '03', '07', 'MIGUEL IGLESIAS');
INSERT INTO `ubigeo_inei` VALUES (642, '06', '03', '08', 'OXAMARCA');
INSERT INTO `ubigeo_inei` VALUES (643, '06', '03', '09', 'SOROCHUCO');
INSERT INTO `ubigeo_inei` VALUES (644, '06', '03', '10', 'SUCRE');
INSERT INTO `ubigeo_inei` VALUES (645, '06', '03', '11', 'UTCO');
INSERT INTO `ubigeo_inei` VALUES (646, '06', '03', '12', 'LA LIBERTAD DE PALLAN');
INSERT INTO `ubigeo_inei` VALUES (647, '06', '04', '00', 'CHOTA');
INSERT INTO `ubigeo_inei` VALUES (648, '06', '04', '01', 'CHOTA');
INSERT INTO `ubigeo_inei` VALUES (649, '06', '04', '02', 'ANGUIA');
INSERT INTO `ubigeo_inei` VALUES (650, '06', '04', '03', 'CHADIN');
INSERT INTO `ubigeo_inei` VALUES (651, '06', '04', '04', 'CHIGUIRIP');
INSERT INTO `ubigeo_inei` VALUES (652, '06', '04', '05', 'CHIMBAN');
INSERT INTO `ubigeo_inei` VALUES (653, '06', '04', '06', 'CHOROPAMPA');
INSERT INTO `ubigeo_inei` VALUES (654, '06', '04', '07', 'COCHABAMBA');
INSERT INTO `ubigeo_inei` VALUES (655, '06', '04', '08', 'CONCHAN');
INSERT INTO `ubigeo_inei` VALUES (656, '06', '04', '09', 'HUAMBOS');
INSERT INTO `ubigeo_inei` VALUES (657, '06', '04', '10', 'LAJAS');
INSERT INTO `ubigeo_inei` VALUES (658, '06', '04', '11', 'LLAMA');
INSERT INTO `ubigeo_inei` VALUES (659, '06', '04', '12', 'MIRACOSTA');
INSERT INTO `ubigeo_inei` VALUES (660, '06', '04', '13', 'PACCHA');
INSERT INTO `ubigeo_inei` VALUES (661, '06', '04', '14', 'PION');
INSERT INTO `ubigeo_inei` VALUES (662, '06', '04', '15', 'QUEROCOTO');
INSERT INTO `ubigeo_inei` VALUES (663, '06', '04', '16', 'SAN JUAN DE LICUPIS');
INSERT INTO `ubigeo_inei` VALUES (664, '06', '04', '17', 'TACABAMBA');
INSERT INTO `ubigeo_inei` VALUES (665, '06', '04', '18', 'TOCMOCHE');
INSERT INTO `ubigeo_inei` VALUES (666, '06', '04', '19', 'CHALAMARCA');
INSERT INTO `ubigeo_inei` VALUES (667, '06', '05', '00', 'CONTUMAZA');
INSERT INTO `ubigeo_inei` VALUES (668, '06', '05', '01', 'CONTUMAZA');
INSERT INTO `ubigeo_inei` VALUES (669, '06', '05', '02', 'CHILETE');
INSERT INTO `ubigeo_inei` VALUES (670, '06', '05', '03', 'CUPISNIQUE');
INSERT INTO `ubigeo_inei` VALUES (671, '06', '05', '04', 'GUZMANGO');
INSERT INTO `ubigeo_inei` VALUES (672, '06', '05', '05', 'SAN BENITO');
INSERT INTO `ubigeo_inei` VALUES (673, '06', '05', '06', 'SANTA CRUZ DE TOLED');
INSERT INTO `ubigeo_inei` VALUES (674, '06', '05', '07', 'TANTARICA');
INSERT INTO `ubigeo_inei` VALUES (675, '06', '05', '08', 'YONAN');
INSERT INTO `ubigeo_inei` VALUES (676, '06', '06', '00', 'CUTERVO');
INSERT INTO `ubigeo_inei` VALUES (677, '06', '06', '01', 'CUTERVO');
INSERT INTO `ubigeo_inei` VALUES (678, '06', '06', '02', 'CALLAYUC');
INSERT INTO `ubigeo_inei` VALUES (679, '06', '06', '03', 'CHOROS');
INSERT INTO `ubigeo_inei` VALUES (680, '06', '06', '04', 'CUJILLO');
INSERT INTO `ubigeo_inei` VALUES (681, '06', '06', '05', 'LA RAMADA');
INSERT INTO `ubigeo_inei` VALUES (682, '06', '06', '06', 'PIMPINGOS');
INSERT INTO `ubigeo_inei` VALUES (683, '06', '06', '07', 'QUEROCOTILLO');
INSERT INTO `ubigeo_inei` VALUES (684, '06', '06', '08', 'SAN ANDRES DE CUTERVO');
INSERT INTO `ubigeo_inei` VALUES (685, '06', '06', '09', 'SAN JUAN DE CUTERVO');
INSERT INTO `ubigeo_inei` VALUES (686, '06', '06', '10', 'SAN LUIS DE LUCMA');
INSERT INTO `ubigeo_inei` VALUES (687, '06', '06', '11', 'SANTA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (688, '06', '06', '12', 'SANTO DOMINGO DE LA CAPILLA');
INSERT INTO `ubigeo_inei` VALUES (689, '06', '06', '13', 'SANTO TOMAS');
INSERT INTO `ubigeo_inei` VALUES (690, '06', '06', '14', 'SOCOTA');
INSERT INTO `ubigeo_inei` VALUES (691, '06', '06', '15', 'TORIBIO CASANOVA');
INSERT INTO `ubigeo_inei` VALUES (692, '06', '07', '00', 'HUALGAYOC');
INSERT INTO `ubigeo_inei` VALUES (693, '06', '07', '01', 'BAMBAMARCA');
INSERT INTO `ubigeo_inei` VALUES (694, '06', '07', '02', 'CHUGUR');
INSERT INTO `ubigeo_inei` VALUES (695, '06', '07', '03', 'HUALGAYOC');
INSERT INTO `ubigeo_inei` VALUES (696, '06', '08', '00', 'JAEN');
INSERT INTO `ubigeo_inei` VALUES (697, '06', '08', '01', 'JAEN');
INSERT INTO `ubigeo_inei` VALUES (698, '06', '08', '02', 'BELLAVISTA');
INSERT INTO `ubigeo_inei` VALUES (699, '06', '08', '03', 'CHONTALI');
INSERT INTO `ubigeo_inei` VALUES (700, '06', '08', '04', 'COLASAY');
INSERT INTO `ubigeo_inei` VALUES (701, '06', '08', '05', 'HUABAL');
INSERT INTO `ubigeo_inei` VALUES (702, '06', '08', '06', 'LAS PIRIAS');
INSERT INTO `ubigeo_inei` VALUES (703, '06', '08', '07', 'POMAHUACA');
INSERT INTO `ubigeo_inei` VALUES (704, '06', '08', '08', 'PUCARA');
INSERT INTO `ubigeo_inei` VALUES (705, '06', '08', '09', 'SALLIQUE');
INSERT INTO `ubigeo_inei` VALUES (706, '06', '08', '10', 'SAN FELIPE');
INSERT INTO `ubigeo_inei` VALUES (707, '06', '08', '11', 'SAN JOSE DEL ALTO');
INSERT INTO `ubigeo_inei` VALUES (708, '06', '08', '12', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (709, '06', '09', '00', 'SAN IGNACIO');
INSERT INTO `ubigeo_inei` VALUES (710, '06', '09', '01', 'SAN IGNACIO');
INSERT INTO `ubigeo_inei` VALUES (711, '06', '09', '02', 'CHIRINOS');
INSERT INTO `ubigeo_inei` VALUES (712, '06', '09', '03', 'HUARANGO');
INSERT INTO `ubigeo_inei` VALUES (713, '06', '09', '04', 'LA COIPA');
INSERT INTO `ubigeo_inei` VALUES (714, '06', '09', '05', 'NAMBALLE');
INSERT INTO `ubigeo_inei` VALUES (715, '06', '09', '06', 'SAN JOSE DE LOURDES');
INSERT INTO `ubigeo_inei` VALUES (716, '06', '09', '07', 'TABACONAS');
INSERT INTO `ubigeo_inei` VALUES (717, '06', '10', '00', 'SAN MARCOS');
INSERT INTO `ubigeo_inei` VALUES (718, '06', '10', '01', 'PEDRO GALVEZ');
INSERT INTO `ubigeo_inei` VALUES (719, '06', '10', '02', 'CHANCAY');
INSERT INTO `ubigeo_inei` VALUES (720, '06', '10', '03', 'EDUARDO VILLANUEVA');
INSERT INTO `ubigeo_inei` VALUES (721, '06', '10', '04', 'GREGORIO PITA');
INSERT INTO `ubigeo_inei` VALUES (722, '06', '10', '05', 'ICHOCAN');
INSERT INTO `ubigeo_inei` VALUES (723, '06', '10', '06', 'JOSE MANUEL QUIROZ');
INSERT INTO `ubigeo_inei` VALUES (724, '06', '10', '07', 'JOSE SABOGAL');
INSERT INTO `ubigeo_inei` VALUES (725, '06', '11', '00', 'SAN MIGUEL');
INSERT INTO `ubigeo_inei` VALUES (726, '06', '11', '01', 'SAN MIGUEL');
INSERT INTO `ubigeo_inei` VALUES (727, '06', '11', '02', 'BOLIVAR');
INSERT INTO `ubigeo_inei` VALUES (728, '06', '11', '03', 'CALQUIS');
INSERT INTO `ubigeo_inei` VALUES (729, '06', '11', '04', 'CATILLUC');
INSERT INTO `ubigeo_inei` VALUES (730, '06', '11', '05', 'EL PRADO');
INSERT INTO `ubigeo_inei` VALUES (731, '06', '11', '06', 'LA FLORIDA');
INSERT INTO `ubigeo_inei` VALUES (732, '06', '11', '07', 'LLAPA');
INSERT INTO `ubigeo_inei` VALUES (733, '06', '11', '08', 'NANCHOC');
INSERT INTO `ubigeo_inei` VALUES (734, '06', '11', '09', 'NIEPOS');
INSERT INTO `ubigeo_inei` VALUES (735, '06', '11', '10', 'SAN GREGORIO');
INSERT INTO `ubigeo_inei` VALUES (736, '06', '11', '11', 'SAN SILVESTRE DE COCHAN');
INSERT INTO `ubigeo_inei` VALUES (737, '06', '11', '12', 'TONGOD');
INSERT INTO `ubigeo_inei` VALUES (738, '06', '11', '13', 'UNION AGUA BLANCA');
INSERT INTO `ubigeo_inei` VALUES (739, '06', '12', '00', 'SAN PABLO');
INSERT INTO `ubigeo_inei` VALUES (740, '06', '12', '01', 'SAN PABLO');
INSERT INTO `ubigeo_inei` VALUES (741, '06', '12', '02', 'SAN BERNARDINO');
INSERT INTO `ubigeo_inei` VALUES (742, '06', '12', '03', 'SAN LUIS');
INSERT INTO `ubigeo_inei` VALUES (743, '06', '12', '04', 'TUMBADEN');
INSERT INTO `ubigeo_inei` VALUES (744, '06', '13', '00', 'SANTA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (745, '06', '13', '01', 'SANTA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (746, '06', '13', '02', 'ANDABAMBA');
INSERT INTO `ubigeo_inei` VALUES (747, '06', '13', '03', 'CATACHE');
INSERT INTO `ubigeo_inei` VALUES (748, '06', '13', '04', 'CHANCAYBAÑOS');
INSERT INTO `ubigeo_inei` VALUES (749, '06', '13', '05', 'LA ESPERANZA');
INSERT INTO `ubigeo_inei` VALUES (750, '06', '13', '06', 'NINABAMBA');
INSERT INTO `ubigeo_inei` VALUES (751, '06', '13', '07', 'PULAN');
INSERT INTO `ubigeo_inei` VALUES (752, '06', '13', '08', 'SAUCEPAMPA');
INSERT INTO `ubigeo_inei` VALUES (753, '06', '13', '09', 'SEXI');
INSERT INTO `ubigeo_inei` VALUES (754, '06', '13', '10', 'UTICYACU');
INSERT INTO `ubigeo_inei` VALUES (755, '06', '13', '11', 'YAUYUCAN');
INSERT INTO `ubigeo_inei` VALUES (756, '07', '00', '00', 'CALLAO');
INSERT INTO `ubigeo_inei` VALUES (757, '07', '01', '00', 'PROV. CONST. DEL CALLAO');
INSERT INTO `ubigeo_inei` VALUES (758, '07', '01', '01', 'CALLAO');
INSERT INTO `ubigeo_inei` VALUES (759, '07', '01', '02', 'BELLAVISTA');
INSERT INTO `ubigeo_inei` VALUES (760, '07', '01', '03', 'CARMEN DE LA LEGUA REYNOSO');
INSERT INTO `ubigeo_inei` VALUES (761, '07', '01', '04', 'LA PERLA');
INSERT INTO `ubigeo_inei` VALUES (762, '07', '01', '05', 'LA PUNTA');
INSERT INTO `ubigeo_inei` VALUES (763, '07', '01', '06', 'VENTANILLA');
INSERT INTO `ubigeo_inei` VALUES (764, '07', '01', '07', 'MI PERÚ');
INSERT INTO `ubigeo_inei` VALUES (765, '08', '00', '00', 'CUSCO');
INSERT INTO `ubigeo_inei` VALUES (766, '08', '01', '00', 'CUSCO');
INSERT INTO `ubigeo_inei` VALUES (767, '08', '01', '01', 'CUSCO');
INSERT INTO `ubigeo_inei` VALUES (768, '08', '01', '02', 'CCORCA');
INSERT INTO `ubigeo_inei` VALUES (769, '08', '01', '03', 'POROY');
INSERT INTO `ubigeo_inei` VALUES (770, '08', '01', '04', 'SAN JERONIMO');
INSERT INTO `ubigeo_inei` VALUES (771, '08', '01', '05', 'SAN SEBASTIAN');
INSERT INTO `ubigeo_inei` VALUES (772, '08', '01', '06', 'SANTIAGO');
INSERT INTO `ubigeo_inei` VALUES (773, '08', '01', '07', 'SAYLLA');
INSERT INTO `ubigeo_inei` VALUES (774, '08', '01', '08', 'WANCHAQ');
INSERT INTO `ubigeo_inei` VALUES (775, '08', '02', '00', 'ACOMAYO');
INSERT INTO `ubigeo_inei` VALUES (776, '08', '02', '01', 'ACOMAYO');
INSERT INTO `ubigeo_inei` VALUES (777, '08', '02', '02', 'ACOPIA');
INSERT INTO `ubigeo_inei` VALUES (778, '08', '02', '03', 'ACOS');
INSERT INTO `ubigeo_inei` VALUES (779, '08', '02', '04', 'MOSOC LLACTA');
INSERT INTO `ubigeo_inei` VALUES (780, '08', '02', '05', 'POMACANCHI');
INSERT INTO `ubigeo_inei` VALUES (781, '08', '02', '06', 'RONDOCAN');
INSERT INTO `ubigeo_inei` VALUES (782, '08', '02', '07', 'SANGARARA');
INSERT INTO `ubigeo_inei` VALUES (783, '08', '03', '00', 'ANTA');
INSERT INTO `ubigeo_inei` VALUES (784, '08', '03', '01', 'ANTA');
INSERT INTO `ubigeo_inei` VALUES (785, '08', '03', '02', 'ANCAHUASI');
INSERT INTO `ubigeo_inei` VALUES (786, '08', '03', '03', 'CACHIMAYO');
INSERT INTO `ubigeo_inei` VALUES (787, '08', '03', '04', 'CHINCHAYPUJIO');
INSERT INTO `ubigeo_inei` VALUES (788, '08', '03', '05', 'HUAROCONDO');
INSERT INTO `ubigeo_inei` VALUES (789, '08', '03', '06', 'LIMATAMBO');
INSERT INTO `ubigeo_inei` VALUES (790, '08', '03', '07', 'MOLLEPATA');
INSERT INTO `ubigeo_inei` VALUES (791, '08', '03', '08', 'PUCYURA');
INSERT INTO `ubigeo_inei` VALUES (792, '08', '03', '09', 'ZURITE');
INSERT INTO `ubigeo_inei` VALUES (793, '08', '04', '00', 'CALCA');
INSERT INTO `ubigeo_inei` VALUES (794, '08', '04', '01', 'CALCA');
INSERT INTO `ubigeo_inei` VALUES (795, '08', '04', '02', 'COYA');
INSERT INTO `ubigeo_inei` VALUES (796, '08', '04', '03', 'LAMAY');
INSERT INTO `ubigeo_inei` VALUES (797, '08', '04', '04', 'LARES');
INSERT INTO `ubigeo_inei` VALUES (798, '08', '04', '05', 'PISAC');
INSERT INTO `ubigeo_inei` VALUES (799, '08', '04', '06', 'SAN SALVADOR');
INSERT INTO `ubigeo_inei` VALUES (800, '08', '04', '07', 'TARAY');
INSERT INTO `ubigeo_inei` VALUES (801, '08', '04', '08', 'YANATILE');
INSERT INTO `ubigeo_inei` VALUES (802, '08', '05', '00', 'CANAS');
INSERT INTO `ubigeo_inei` VALUES (803, '08', '05', '01', 'YANAOCA');
INSERT INTO `ubigeo_inei` VALUES (804, '08', '05', '02', 'CHECCA');
INSERT INTO `ubigeo_inei` VALUES (805, '08', '05', '03', 'KUNTURKANKI');
INSERT INTO `ubigeo_inei` VALUES (806, '08', '05', '04', 'LANGUI');
INSERT INTO `ubigeo_inei` VALUES (807, '08', '05', '05', 'LAYO');
INSERT INTO `ubigeo_inei` VALUES (808, '08', '05', '06', 'PAMPAMARCA');
INSERT INTO `ubigeo_inei` VALUES (809, '08', '05', '07', 'QUEHUE');
INSERT INTO `ubigeo_inei` VALUES (810, '08', '05', '08', 'TUPAC AMARU');
INSERT INTO `ubigeo_inei` VALUES (811, '08', '06', '00', 'CANCHIS');
INSERT INTO `ubigeo_inei` VALUES (812, '08', '06', '01', 'SICUANI');
INSERT INTO `ubigeo_inei` VALUES (813, '08', '06', '02', 'CHECACUPE');
INSERT INTO `ubigeo_inei` VALUES (814, '08', '06', '03', 'COMBAPATA');
INSERT INTO `ubigeo_inei` VALUES (815, '08', '06', '04', 'MARANGANI');
INSERT INTO `ubigeo_inei` VALUES (816, '08', '06', '05', 'PITUMARCA');
INSERT INTO `ubigeo_inei` VALUES (817, '08', '06', '06', 'SAN PABLO');
INSERT INTO `ubigeo_inei` VALUES (818, '08', '06', '07', 'SAN PEDRO');
INSERT INTO `ubigeo_inei` VALUES (819, '08', '06', '08', 'TINTA');
INSERT INTO `ubigeo_inei` VALUES (820, '08', '07', '00', 'CHUMBIVILCAS');
INSERT INTO `ubigeo_inei` VALUES (821, '08', '07', '01', 'SANTO TOMAS');
INSERT INTO `ubigeo_inei` VALUES (822, '08', '07', '02', 'CAPACMARCA');
INSERT INTO `ubigeo_inei` VALUES (823, '08', '07', '03', 'CHAMACA');
INSERT INTO `ubigeo_inei` VALUES (824, '08', '07', '04', 'COLQUEMARCA');
INSERT INTO `ubigeo_inei` VALUES (825, '08', '07', '05', 'LIVITACA');
INSERT INTO `ubigeo_inei` VALUES (826, '08', '07', '06', 'LLUSCO');
INSERT INTO `ubigeo_inei` VALUES (827, '08', '07', '07', 'QUIÑOTA');
INSERT INTO `ubigeo_inei` VALUES (828, '08', '07', '08', 'VELILLE');
INSERT INTO `ubigeo_inei` VALUES (829, '08', '08', '00', 'ESPINAR');
INSERT INTO `ubigeo_inei` VALUES (830, '08', '08', '01', 'ESPINAR');
INSERT INTO `ubigeo_inei` VALUES (831, '08', '08', '02', 'CONDOROMA');
INSERT INTO `ubigeo_inei` VALUES (832, '08', '08', '03', 'COPORAQUE');
INSERT INTO `ubigeo_inei` VALUES (833, '08', '08', '04', 'OCORURO');
INSERT INTO `ubigeo_inei` VALUES (834, '08', '08', '05', 'PALLPATA');
INSERT INTO `ubigeo_inei` VALUES (835, '08', '08', '06', 'PICHIGUA');
INSERT INTO `ubigeo_inei` VALUES (836, '08', '08', '07', 'SUYCKUTAMBO');
INSERT INTO `ubigeo_inei` VALUES (837, '08', '08', '08', 'ALTO PICHIGUA');
INSERT INTO `ubigeo_inei` VALUES (838, '08', '09', '00', 'LA CONVENCION');
INSERT INTO `ubigeo_inei` VALUES (839, '08', '09', '01', 'SANTA ANA');
INSERT INTO `ubigeo_inei` VALUES (840, '08', '09', '02', 'ECHARATE');
INSERT INTO `ubigeo_inei` VALUES (841, '08', '09', '03', 'HUAYOPATA');
INSERT INTO `ubigeo_inei` VALUES (842, '08', '09', '04', 'MARANURA');
INSERT INTO `ubigeo_inei` VALUES (843, '08', '09', '05', 'OCOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (844, '08', '09', '06', 'QUELLOUNO');
INSERT INTO `ubigeo_inei` VALUES (845, '08', '09', '07', 'KIMBIRI');
INSERT INTO `ubigeo_inei` VALUES (846, '08', '09', '08', 'SANTA TERESA');
INSERT INTO `ubigeo_inei` VALUES (847, '08', '09', '09', 'VILCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (848, '08', '09', '10', 'PICHARI');
INSERT INTO `ubigeo_inei` VALUES (849, '08', '09', '11', 'INKAWASI');
INSERT INTO `ubigeo_inei` VALUES (850, '08', '09', '12', 'VILLA VIRGEN');
INSERT INTO `ubigeo_inei` VALUES (851, '08', '10', '00', 'PARURO');
INSERT INTO `ubigeo_inei` VALUES (852, '08', '10', '01', 'PARURO');
INSERT INTO `ubigeo_inei` VALUES (853, '08', '10', '02', 'ACCHA');
INSERT INTO `ubigeo_inei` VALUES (854, '08', '10', '03', 'CCAPI');
INSERT INTO `ubigeo_inei` VALUES (855, '08', '10', '04', 'COLCHA');
INSERT INTO `ubigeo_inei` VALUES (856, '08', '10', '05', 'HUANOQUITE');
INSERT INTO `ubigeo_inei` VALUES (857, '08', '10', '06', 'OMACHA');
INSERT INTO `ubigeo_inei` VALUES (858, '08', '10', '07', 'PACCARITAMBO');
INSERT INTO `ubigeo_inei` VALUES (859, '08', '10', '08', 'PILLPINTO');
INSERT INTO `ubigeo_inei` VALUES (860, '08', '10', '09', 'YAURISQUE');
INSERT INTO `ubigeo_inei` VALUES (861, '08', '11', '00', 'PAUCARTAMBO');
INSERT INTO `ubigeo_inei` VALUES (862, '08', '11', '01', 'PAUCARTAMBO');
INSERT INTO `ubigeo_inei` VALUES (863, '08', '11', '02', 'CAICAY');
INSERT INTO `ubigeo_inei` VALUES (864, '08', '11', '03', 'CHALLABAMBA');
INSERT INTO `ubigeo_inei` VALUES (865, '08', '11', '04', 'COLQUEPATA');
INSERT INTO `ubigeo_inei` VALUES (866, '08', '11', '05', 'HUANCARANI');
INSERT INTO `ubigeo_inei` VALUES (867, '08', '11', '06', 'KOSÑIPATA');
INSERT INTO `ubigeo_inei` VALUES (868, '08', '12', '00', 'QUISPICANCHI');
INSERT INTO `ubigeo_inei` VALUES (869, '08', '12', '01', 'URCOS');
INSERT INTO `ubigeo_inei` VALUES (870, '08', '12', '02', 'ANDAHUAYLILLAS');
INSERT INTO `ubigeo_inei` VALUES (871, '08', '12', '03', 'CAMANTI');
INSERT INTO `ubigeo_inei` VALUES (872, '08', '12', '04', 'CCARHUAYO');
INSERT INTO `ubigeo_inei` VALUES (873, '08', '12', '05', 'CCATCA');
INSERT INTO `ubigeo_inei` VALUES (874, '08', '12', '06', 'CUSIPATA');
INSERT INTO `ubigeo_inei` VALUES (875, '08', '12', '07', 'HUARO');
INSERT INTO `ubigeo_inei` VALUES (876, '08', '12', '08', 'LUCRE');
INSERT INTO `ubigeo_inei` VALUES (877, '08', '12', '09', 'MARCAPATA');
INSERT INTO `ubigeo_inei` VALUES (878, '08', '12', '10', 'OCONGATE');
INSERT INTO `ubigeo_inei` VALUES (879, '08', '12', '11', 'OROPESA');
INSERT INTO `ubigeo_inei` VALUES (880, '08', '12', '12', 'QUIQUIJANA');
INSERT INTO `ubigeo_inei` VALUES (881, '08', '13', '00', 'URUBAMBA');
INSERT INTO `ubigeo_inei` VALUES (882, '08', '13', '01', 'URUBAMBA');
INSERT INTO `ubigeo_inei` VALUES (883, '08', '13', '02', 'CHINCHERO');
INSERT INTO `ubigeo_inei` VALUES (884, '08', '13', '03', 'HUAYLLABAMBA');
INSERT INTO `ubigeo_inei` VALUES (885, '08', '13', '04', 'MACHUPICCHU');
INSERT INTO `ubigeo_inei` VALUES (886, '08', '13', '05', 'MARAS');
INSERT INTO `ubigeo_inei` VALUES (887, '08', '13', '06', 'OLLANTAYTAMBO');
INSERT INTO `ubigeo_inei` VALUES (888, '08', '13', '07', 'YUCAY');
INSERT INTO `ubigeo_inei` VALUES (889, '09', '00', '00', 'HUANCAVELICA');
INSERT INTO `ubigeo_inei` VALUES (890, '09', '01', '00', 'HUANCAVELICA');
INSERT INTO `ubigeo_inei` VALUES (891, '09', '01', '01', 'HUANCAVELICA');
INSERT INTO `ubigeo_inei` VALUES (892, '09', '01', '02', 'ACOBAMBILLA');
INSERT INTO `ubigeo_inei` VALUES (893, '09', '01', '03', 'ACORIA');
INSERT INTO `ubigeo_inei` VALUES (894, '09', '01', '04', 'CONAYCA');
INSERT INTO `ubigeo_inei` VALUES (895, '09', '01', '05', 'CUENCA');
INSERT INTO `ubigeo_inei` VALUES (896, '09', '01', '06', 'HUACHOCOLPA');
INSERT INTO `ubigeo_inei` VALUES (897, '09', '01', '07', 'HUAYLLAHUARA');
INSERT INTO `ubigeo_inei` VALUES (898, '09', '01', '08', 'IZCUCHACA');
INSERT INTO `ubigeo_inei` VALUES (899, '09', '01', '09', 'LARIA');
INSERT INTO `ubigeo_inei` VALUES (900, '09', '01', '10', 'MANTA');
INSERT INTO `ubigeo_inei` VALUES (901, '09', '01', '11', 'MARISCAL CACERES');
INSERT INTO `ubigeo_inei` VALUES (902, '09', '01', '12', 'MOYA');
INSERT INTO `ubigeo_inei` VALUES (903, '09', '01', '13', 'NUEVO OCCORO');
INSERT INTO `ubigeo_inei` VALUES (904, '09', '01', '14', 'PALCA');
INSERT INTO `ubigeo_inei` VALUES (905, '09', '01', '15', 'PILCHACA');
INSERT INTO `ubigeo_inei` VALUES (906, '09', '01', '16', 'VILCA');
INSERT INTO `ubigeo_inei` VALUES (907, '09', '01', '17', 'YAULI');
INSERT INTO `ubigeo_inei` VALUES (908, '09', '01', '18', 'ASCENSIÓN');
INSERT INTO `ubigeo_inei` VALUES (909, '09', '01', '19', 'HUANDO');
INSERT INTO `ubigeo_inei` VALUES (910, '09', '02', '00', 'ACOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (911, '09', '02', '01', 'ACOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (912, '09', '02', '02', 'ANDABAMBA');
INSERT INTO `ubigeo_inei` VALUES (913, '09', '02', '03', 'ANTA');
INSERT INTO `ubigeo_inei` VALUES (914, '09', '02', '04', 'CAJA');
INSERT INTO `ubigeo_inei` VALUES (915, '09', '02', '05', 'MARCAS');
INSERT INTO `ubigeo_inei` VALUES (916, '09', '02', '06', 'PAUCARA');
INSERT INTO `ubigeo_inei` VALUES (917, '09', '02', '07', 'POMACOCHA');
INSERT INTO `ubigeo_inei` VALUES (918, '09', '02', '08', 'ROSARIO');
INSERT INTO `ubigeo_inei` VALUES (919, '09', '03', '00', 'ANGARAES');
INSERT INTO `ubigeo_inei` VALUES (920, '09', '03', '01', 'LIRCAY');
INSERT INTO `ubigeo_inei` VALUES (921, '09', '03', '02', 'ANCHONGA');
INSERT INTO `ubigeo_inei` VALUES (922, '09', '03', '03', 'CALLANMARCA');
INSERT INTO `ubigeo_inei` VALUES (923, '09', '03', '04', 'CCOCHACCASA');
INSERT INTO `ubigeo_inei` VALUES (924, '09', '03', '05', 'CHINCHO');
INSERT INTO `ubigeo_inei` VALUES (925, '09', '03', '06', 'CONGALLA');
INSERT INTO `ubigeo_inei` VALUES (926, '09', '03', '07', 'HUANCA-HUANCA');
INSERT INTO `ubigeo_inei` VALUES (927, '09', '03', '08', 'HUAYLLAY GRANDE');
INSERT INTO `ubigeo_inei` VALUES (928, '09', '03', '09', 'JULCAMARCA');
INSERT INTO `ubigeo_inei` VALUES (929, '09', '03', '10', 'SAN ANTONIO DE ANTAPARCO');
INSERT INTO `ubigeo_inei` VALUES (930, '09', '03', '11', 'SANTO TOMAS DE PATA');
INSERT INTO `ubigeo_inei` VALUES (931, '09', '03', '12', 'SECCLLA');
INSERT INTO `ubigeo_inei` VALUES (932, '09', '04', '00', 'CASTROVIRREYNA');
INSERT INTO `ubigeo_inei` VALUES (933, '09', '04', '01', 'CASTROVIRREYNA');
INSERT INTO `ubigeo_inei` VALUES (934, '09', '04', '02', 'ARMA');
INSERT INTO `ubigeo_inei` VALUES (935, '09', '04', '03', 'AURAHUA');
INSERT INTO `ubigeo_inei` VALUES (936, '09', '04', '04', 'CAPILLAS');
INSERT INTO `ubigeo_inei` VALUES (937, '09', '04', '05', 'CHUPAMARCA');
INSERT INTO `ubigeo_inei` VALUES (938, '09', '04', '06', 'COCAS');
INSERT INTO `ubigeo_inei` VALUES (939, '09', '04', '07', 'HUACHOS');
INSERT INTO `ubigeo_inei` VALUES (940, '09', '04', '08', 'HUAMATAMBO');
INSERT INTO `ubigeo_inei` VALUES (941, '09', '04', '09', 'MOLLEPAMPA');
INSERT INTO `ubigeo_inei` VALUES (942, '09', '04', '10', 'SAN JUAN');
INSERT INTO `ubigeo_inei` VALUES (943, '09', '04', '11', 'SANTA ANA');
INSERT INTO `ubigeo_inei` VALUES (944, '09', '04', '12', 'TANTARA');
INSERT INTO `ubigeo_inei` VALUES (945, '09', '04', '13', 'TICRAPO');
INSERT INTO `ubigeo_inei` VALUES (946, '09', '05', '00', 'CHURCAMPA');
INSERT INTO `ubigeo_inei` VALUES (947, '09', '05', '01', 'CHURCAMPA');
INSERT INTO `ubigeo_inei` VALUES (948, '09', '05', '02', 'ANCO');
INSERT INTO `ubigeo_inei` VALUES (949, '09', '05', '03', 'CHINCHIHUASI');
INSERT INTO `ubigeo_inei` VALUES (950, '09', '05', '04', 'EL CARMEN');
INSERT INTO `ubigeo_inei` VALUES (951, '09', '05', '05', 'LA MERCED');
INSERT INTO `ubigeo_inei` VALUES (952, '09', '05', '06', 'LOCROJA');
INSERT INTO `ubigeo_inei` VALUES (953, '09', '05', '07', 'PAUCARBAMBA');
INSERT INTO `ubigeo_inei` VALUES (954, '09', '05', '08', 'SAN MIGUEL DE MAYOCC');
INSERT INTO `ubigeo_inei` VALUES (955, '09', '05', '09', 'SAN PEDRO DE CORIS');
INSERT INTO `ubigeo_inei` VALUES (956, '09', '05', '10', 'PACHAMARCA');
INSERT INTO `ubigeo_inei` VALUES (957, '09', '05', '11', 'COSME');
INSERT INTO `ubigeo_inei` VALUES (958, '09', '06', '00', 'HUAYTARA');
INSERT INTO `ubigeo_inei` VALUES (959, '09', '06', '01', 'HUAYTARA');
INSERT INTO `ubigeo_inei` VALUES (960, '09', '06', '02', 'AYAVI');
INSERT INTO `ubigeo_inei` VALUES (961, '09', '06', '03', 'CORDOVA');
INSERT INTO `ubigeo_inei` VALUES (962, '09', '06', '04', 'HUAYACUNDO ARMA');
INSERT INTO `ubigeo_inei` VALUES (963, '09', '06', '05', 'LARAMARCA');
INSERT INTO `ubigeo_inei` VALUES (964, '09', '06', '06', 'OCOYO');
INSERT INTO `ubigeo_inei` VALUES (965, '09', '06', '07', 'PILPICHACA');
INSERT INTO `ubigeo_inei` VALUES (966, '09', '06', '08', 'QUERCO');
INSERT INTO `ubigeo_inei` VALUES (967, '09', '06', '09', 'QUITO-ARMA');
INSERT INTO `ubigeo_inei` VALUES (968, '09', '06', '10', 'SAN ANTONIO DE CUSICANCHA');
INSERT INTO `ubigeo_inei` VALUES (969, '09', '06', '11', 'SAN FRANCISCO DE SANGAYAICO');
INSERT INTO `ubigeo_inei` VALUES (970, '09', '06', '12', 'SAN ISIDRO');
INSERT INTO `ubigeo_inei` VALUES (971, '09', '06', '13', 'SANTIAGO DE CHOCORVOS');
INSERT INTO `ubigeo_inei` VALUES (972, '09', '06', '14', 'SANTIAGO DE QUIRAHUARA');
INSERT INTO `ubigeo_inei` VALUES (973, '09', '06', '15', 'SANTO DOMINGO DE CAPILLAS');
INSERT INTO `ubigeo_inei` VALUES (974, '09', '06', '16', 'TAMBO');
INSERT INTO `ubigeo_inei` VALUES (975, '09', '07', '00', 'TAYACAJA');
INSERT INTO `ubigeo_inei` VALUES (976, '09', '07', '01', 'PAMPAS');
INSERT INTO `ubigeo_inei` VALUES (977, '09', '07', '02', 'ACOSTAMBO');
INSERT INTO `ubigeo_inei` VALUES (978, '09', '07', '03', 'ACRAQUIA');
INSERT INTO `ubigeo_inei` VALUES (979, '09', '07', '04', 'AHUAYCHA');
INSERT INTO `ubigeo_inei` VALUES (980, '09', '07', '05', 'COLCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (981, '09', '07', '06', 'DANIEL HERNANDEZ');
INSERT INTO `ubigeo_inei` VALUES (982, '09', '07', '07', 'HUACHOCOLPA');
INSERT INTO `ubigeo_inei` VALUES (983, '09', '07', '09', 'HUARIBAMBA');
INSERT INTO `ubigeo_inei` VALUES (984, '09', '07', '10', 'ÑAHUIMPUQUIO');
INSERT INTO `ubigeo_inei` VALUES (985, '09', '07', '11', 'PAZOS');
INSERT INTO `ubigeo_inei` VALUES (986, '09', '07', '13', 'QUISHUAR');
INSERT INTO `ubigeo_inei` VALUES (987, '09', '07', '14', 'SALCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (988, '09', '07', '15', 'SALCAHUASI');
INSERT INTO `ubigeo_inei` VALUES (989, '09', '07', '16', 'SAN MARCOS DE ROCCHAC');
INSERT INTO `ubigeo_inei` VALUES (990, '09', '07', '17', 'SURCUBAMBA');
INSERT INTO `ubigeo_inei` VALUES (991, '09', '07', '18', 'TINTAY PUNCU');
INSERT INTO `ubigeo_inei` VALUES (992, '10', '00', '00', 'HUANUCO');
INSERT INTO `ubigeo_inei` VALUES (993, '10', '01', '00', 'HUANUCO');
INSERT INTO `ubigeo_inei` VALUES (994, '10', '01', '01', 'HUANUCO');
INSERT INTO `ubigeo_inei` VALUES (995, '10', '01', '02', 'AMARILIS');
INSERT INTO `ubigeo_inei` VALUES (996, '10', '01', '03', 'CHINCHAO');
INSERT INTO `ubigeo_inei` VALUES (997, '10', '01', '04', 'CHURUBAMBA');
INSERT INTO `ubigeo_inei` VALUES (998, '10', '01', '05', 'MARGOS');
INSERT INTO `ubigeo_inei` VALUES (999, '10', '01', '06', 'QUISQUI');
INSERT INTO `ubigeo_inei` VALUES (1000, '10', '01', '07', 'SAN FRANCISCO DE CAYRAN');
INSERT INTO `ubigeo_inei` VALUES (1001, '10', '01', '08', 'SAN PEDRO DE CHAULAN');
INSERT INTO `ubigeo_inei` VALUES (1002, '10', '01', '09', 'SANTA MARIA DEL VALLE');
INSERT INTO `ubigeo_inei` VALUES (1003, '10', '01', '10', 'YARUMAYO');
INSERT INTO `ubigeo_inei` VALUES (1004, '10', '01', '11', 'PILLCO MARCA');
INSERT INTO `ubigeo_inei` VALUES (1005, '10', '01', '12', 'YACUS');
INSERT INTO `ubigeo_inei` VALUES (1006, '10', '02', '00', 'AMBO');
INSERT INTO `ubigeo_inei` VALUES (1007, '10', '02', '01', 'AMBO');
INSERT INTO `ubigeo_inei` VALUES (1008, '10', '02', '02', 'CAYNA');
INSERT INTO `ubigeo_inei` VALUES (1009, '10', '02', '03', 'COLPAS');
INSERT INTO `ubigeo_inei` VALUES (1010, '10', '02', '04', 'CONCHAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1011, '10', '02', '05', 'HUACAR');
INSERT INTO `ubigeo_inei` VALUES (1012, '10', '02', '06', 'SAN FRANCISCO');
INSERT INTO `ubigeo_inei` VALUES (1013, '10', '02', '07', 'SAN RAFAEL');
INSERT INTO `ubigeo_inei` VALUES (1014, '10', '02', '08', 'TOMAY KICHWA');
INSERT INTO `ubigeo_inei` VALUES (1015, '10', '03', '00', 'DOS DE MAYO');
INSERT INTO `ubigeo_inei` VALUES (1016, '10', '03', '01', 'LA UNION');
INSERT INTO `ubigeo_inei` VALUES (1017, '10', '03', '07', 'CHUQUIS');
INSERT INTO `ubigeo_inei` VALUES (1018, '10', '03', '11', 'MARIAS');
INSERT INTO `ubigeo_inei` VALUES (1019, '10', '03', '13', 'PACHAS');
INSERT INTO `ubigeo_inei` VALUES (1020, '10', '03', '16', 'QUIVILLA');
INSERT INTO `ubigeo_inei` VALUES (1021, '10', '03', '17', 'RIPAN');
INSERT INTO `ubigeo_inei` VALUES (1022, '10', '03', '21', 'SHUNQUI');
INSERT INTO `ubigeo_inei` VALUES (1023, '10', '03', '22', 'SILLAPATA');
INSERT INTO `ubigeo_inei` VALUES (1024, '10', '03', '23', 'YANAS');
INSERT INTO `ubigeo_inei` VALUES (1025, '10', '04', '00', 'HUACAYBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1026, '10', '04', '01', 'HUACAYBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1027, '10', '04', '02', 'CANCHABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1028, '10', '04', '03', 'COCHABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1029, '10', '04', '04', 'PINRA');
INSERT INTO `ubigeo_inei` VALUES (1030, '10', '05', '00', 'HUAMALIES');
INSERT INTO `ubigeo_inei` VALUES (1031, '10', '05', '01', 'LLATA');
INSERT INTO `ubigeo_inei` VALUES (1032, '10', '05', '02', 'ARANCAY');
INSERT INTO `ubigeo_inei` VALUES (1033, '10', '05', '03', 'CHAVIN DE PARIARCA');
INSERT INTO `ubigeo_inei` VALUES (1034, '10', '05', '04', 'JACAS GRANDE');
INSERT INTO `ubigeo_inei` VALUES (1035, '10', '05', '05', 'JIRCAN');
INSERT INTO `ubigeo_inei` VALUES (1036, '10', '05', '06', 'MIRAFLORES');
INSERT INTO `ubigeo_inei` VALUES (1037, '10', '05', '07', 'MONZON');
INSERT INTO `ubigeo_inei` VALUES (1038, '10', '05', '08', 'PUNCHAO');
INSERT INTO `ubigeo_inei` VALUES (1039, '10', '05', '09', 'PUÑOS');
INSERT INTO `ubigeo_inei` VALUES (1040, '10', '05', '10', 'SINGA');
INSERT INTO `ubigeo_inei` VALUES (1041, '10', '05', '11', 'TANTAMAYO');
INSERT INTO `ubigeo_inei` VALUES (1042, '10', '06', '00', 'LEONCIO PRADO');
INSERT INTO `ubigeo_inei` VALUES (1043, '10', '06', '01', 'RUPA-RUPA');
INSERT INTO `ubigeo_inei` VALUES (1044, '10', '06', '02', 'DANIEL ALOMIAS ROBLES');
INSERT INTO `ubigeo_inei` VALUES (1045, '10', '06', '03', 'HERMILIO VALDIZAN');
INSERT INTO `ubigeo_inei` VALUES (1046, '10', '06', '04', 'JOSE CRESPO Y CASTILLO');
INSERT INTO `ubigeo_inei` VALUES (1047, '10', '06', '05', 'LUYANDO');
INSERT INTO `ubigeo_inei` VALUES (1048, '10', '06', '06', 'MARIANO DAMASO BERAUN');
INSERT INTO `ubigeo_inei` VALUES (1049, '10', '07', '00', 'MARAÑON');
INSERT INTO `ubigeo_inei` VALUES (1050, '10', '07', '01', 'HUACRACHUCO');
INSERT INTO `ubigeo_inei` VALUES (1051, '10', '07', '02', 'CHOLON');
INSERT INTO `ubigeo_inei` VALUES (1052, '10', '07', '03', 'SAN BUENAVENTURA');
INSERT INTO `ubigeo_inei` VALUES (1053, '10', '08', '00', 'PACHITEA');
INSERT INTO `ubigeo_inei` VALUES (1054, '10', '08', '01', 'PANAO');
INSERT INTO `ubigeo_inei` VALUES (1055, '10', '08', '02', 'CHAGLLA');
INSERT INTO `ubigeo_inei` VALUES (1056, '10', '08', '03', 'MOLINO');
INSERT INTO `ubigeo_inei` VALUES (1057, '10', '08', '04', 'UMARI');
INSERT INTO `ubigeo_inei` VALUES (1058, '10', '09', '00', 'PUERTO INCA');
INSERT INTO `ubigeo_inei` VALUES (1059, '10', '09', '01', 'PUERTO INCA');
INSERT INTO `ubigeo_inei` VALUES (1060, '10', '09', '02', 'CODO DEL POZUZO');
INSERT INTO `ubigeo_inei` VALUES (1061, '10', '09', '03', 'HONORIA');
INSERT INTO `ubigeo_inei` VALUES (1062, '10', '09', '04', 'TOURNAVISTA');
INSERT INTO `ubigeo_inei` VALUES (1063, '10', '09', '05', 'YUYAPICHIS');
INSERT INTO `ubigeo_inei` VALUES (1064, '10', '10', '00', 'LAURICOCHA');
INSERT INTO `ubigeo_inei` VALUES (1065, '10', '10', '01', 'JESUS');
INSERT INTO `ubigeo_inei` VALUES (1066, '10', '10', '02', 'BAÑOS');
INSERT INTO `ubigeo_inei` VALUES (1067, '10', '10', '03', 'JIVIA');
INSERT INTO `ubigeo_inei` VALUES (1068, '10', '10', '04', 'QUEROPALCA');
INSERT INTO `ubigeo_inei` VALUES (1069, '10', '10', '05', 'RONDOS');
INSERT INTO `ubigeo_inei` VALUES (1070, '10', '10', '06', 'SAN FRANCISCO DE ASIS');
INSERT INTO `ubigeo_inei` VALUES (1071, '10', '10', '07', 'SAN MIGUEL DE CAURI');
INSERT INTO `ubigeo_inei` VALUES (1072, '10', '11', '00', 'YAROWILCA');
INSERT INTO `ubigeo_inei` VALUES (1073, '10', '11', '01', 'CHAVINILLO');
INSERT INTO `ubigeo_inei` VALUES (1074, '10', '11', '02', 'CAHUAC');
INSERT INTO `ubigeo_inei` VALUES (1075, '10', '11', '03', 'CHACABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1076, '10', '11', '04', 'CHUPAN');
INSERT INTO `ubigeo_inei` VALUES (1077, '10', '11', '05', 'JACAS CHICO');
INSERT INTO `ubigeo_inei` VALUES (1078, '10', '11', '06', 'OBAS');
INSERT INTO `ubigeo_inei` VALUES (1079, '10', '11', '07', 'PAMPAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1080, '10', '11', '08', 'CHORAS');
INSERT INTO `ubigeo_inei` VALUES (1081, '11', '00', '00', 'ICA');
INSERT INTO `ubigeo_inei` VALUES (1082, '11', '01', '00', 'ICA');
INSERT INTO `ubigeo_inei` VALUES (1083, '11', '01', '01', 'ICA');
INSERT INTO `ubigeo_inei` VALUES (1084, '11', '01', '02', 'LA TINGUIÑA');
INSERT INTO `ubigeo_inei` VALUES (1085, '11', '01', '03', 'LOS AQUIJES');
INSERT INTO `ubigeo_inei` VALUES (1086, '11', '01', '04', 'OCUCAJE');
INSERT INTO `ubigeo_inei` VALUES (1087, '11', '01', '05', 'PACHACUTEC');
INSERT INTO `ubigeo_inei` VALUES (1088, '11', '01', '06', 'PARCONA');
INSERT INTO `ubigeo_inei` VALUES (1089, '11', '01', '07', 'PUEBLO NUEVO');
INSERT INTO `ubigeo_inei` VALUES (1090, '11', '01', '08', 'SALAS');
INSERT INTO `ubigeo_inei` VALUES (1091, '11', '01', '09', 'SAN JOSE DE LOS MOLINOS');
INSERT INTO `ubigeo_inei` VALUES (1092, '11', '01', '10', 'SAN JUAN BAUTISTA');
INSERT INTO `ubigeo_inei` VALUES (1093, '11', '01', '11', 'SANTIAGO');
INSERT INTO `ubigeo_inei` VALUES (1094, '11', '01', '12', 'SUBTANJALLA');
INSERT INTO `ubigeo_inei` VALUES (1095, '11', '01', '13', 'TATE');
INSERT INTO `ubigeo_inei` VALUES (1096, '11', '01', '14', 'YAUCA DEL ROSARIO');
INSERT INTO `ubigeo_inei` VALUES (1097, '11', '02', '00', 'CHINCHA');
INSERT INTO `ubigeo_inei` VALUES (1098, '11', '02', '01', 'CHINCHA ALTA');
INSERT INTO `ubigeo_inei` VALUES (1099, '11', '02', '02', 'ALTO LARAN');
INSERT INTO `ubigeo_inei` VALUES (1100, '11', '02', '03', 'CHAVIN');
INSERT INTO `ubigeo_inei` VALUES (1101, '11', '02', '04', 'CHINCHA BAJA');
INSERT INTO `ubigeo_inei` VALUES (1102, '11', '02', '05', 'EL CARMEN');
INSERT INTO `ubigeo_inei` VALUES (1103, '11', '02', '06', 'GROCIO PRADO');
INSERT INTO `ubigeo_inei` VALUES (1104, '11', '02', '07', 'PUEBLO NUEVO');
INSERT INTO `ubigeo_inei` VALUES (1105, '11', '02', '08', 'SAN JUAN DE YANAC');
INSERT INTO `ubigeo_inei` VALUES (1106, '11', '02', '09', 'SAN PEDRO DE HUACARPANA');
INSERT INTO `ubigeo_inei` VALUES (1107, '11', '02', '10', 'SUNAMPE');
INSERT INTO `ubigeo_inei` VALUES (1108, '11', '02', '11', 'TAMBO DE MORA');
INSERT INTO `ubigeo_inei` VALUES (1109, '11', '03', '00', 'NAZCA');
INSERT INTO `ubigeo_inei` VALUES (1110, '11', '03', '01', 'NAZCA');
INSERT INTO `ubigeo_inei` VALUES (1111, '11', '03', '02', 'CHANGUILLO');
INSERT INTO `ubigeo_inei` VALUES (1112, '11', '03', '03', 'EL INGENIO');
INSERT INTO `ubigeo_inei` VALUES (1113, '11', '03', '04', 'MARCONA');
INSERT INTO `ubigeo_inei` VALUES (1114, '11', '03', '05', 'VISTA ALEGRE');
INSERT INTO `ubigeo_inei` VALUES (1115, '11', '04', '00', 'PALPA');
INSERT INTO `ubigeo_inei` VALUES (1116, '11', '04', '01', 'PALPA');
INSERT INTO `ubigeo_inei` VALUES (1117, '11', '04', '02', 'LLIPATA');
INSERT INTO `ubigeo_inei` VALUES (1118, '11', '04', '03', 'RIO GRANDE');
INSERT INTO `ubigeo_inei` VALUES (1119, '11', '04', '04', 'SANTA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (1120, '11', '04', '05', 'TIBILLO');
INSERT INTO `ubigeo_inei` VALUES (1121, '11', '05', '00', 'PISCO');
INSERT INTO `ubigeo_inei` VALUES (1122, '11', '05', '01', 'PISCO');
INSERT INTO `ubigeo_inei` VALUES (1123, '11', '05', '02', 'HUANCANO');
INSERT INTO `ubigeo_inei` VALUES (1124, '11', '05', '03', 'HUMAY');
INSERT INTO `ubigeo_inei` VALUES (1125, '11', '05', '04', 'INDEPENDENCIA');
INSERT INTO `ubigeo_inei` VALUES (1126, '11', '05', '05', 'PARACAS');
INSERT INTO `ubigeo_inei` VALUES (1127, '11', '05', '06', 'SAN ANDRES');
INSERT INTO `ubigeo_inei` VALUES (1128, '11', '05', '07', 'SAN CLEMENTE');
INSERT INTO `ubigeo_inei` VALUES (1129, '11', '05', '08', 'TUPAC AMARU INCA');
INSERT INTO `ubigeo_inei` VALUES (1130, '12', '00', '00', 'JUNIN');
INSERT INTO `ubigeo_inei` VALUES (1131, '12', '01', '00', 'HUANCAYO');
INSERT INTO `ubigeo_inei` VALUES (1132, '12', '01', '01', 'HUANCAYO');
INSERT INTO `ubigeo_inei` VALUES (1133, '12', '01', '04', 'CARHUACALLANGA');
INSERT INTO `ubigeo_inei` VALUES (1134, '12', '01', '05', 'CHACAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1135, '12', '01', '06', 'CHICCHE');
INSERT INTO `ubigeo_inei` VALUES (1136, '12', '01', '07', 'CHILCA');
INSERT INTO `ubigeo_inei` VALUES (1137, '12', '01', '08', 'CHONGOS ALTO');
INSERT INTO `ubigeo_inei` VALUES (1138, '12', '01', '11', 'CHUPURO');
INSERT INTO `ubigeo_inei` VALUES (1139, '12', '01', '12', 'COLCA');
INSERT INTO `ubigeo_inei` VALUES (1140, '12', '01', '13', 'CULLHUAS');
INSERT INTO `ubigeo_inei` VALUES (1141, '12', '01', '14', 'EL TAMBO');
INSERT INTO `ubigeo_inei` VALUES (1142, '12', '01', '16', 'HUACRAPUQUIO');
INSERT INTO `ubigeo_inei` VALUES (1143, '12', '01', '17', 'HUALHUAS');
INSERT INTO `ubigeo_inei` VALUES (1144, '12', '01', '19', 'HUANCAN');
INSERT INTO `ubigeo_inei` VALUES (1145, '12', '01', '20', 'HUASICANCHA');
INSERT INTO `ubigeo_inei` VALUES (1146, '12', '01', '21', 'HUAYUCACHI');
INSERT INTO `ubigeo_inei` VALUES (1147, '12', '01', '22', 'INGENIO');
INSERT INTO `ubigeo_inei` VALUES (1148, '12', '01', '24', 'PARIAHUANCA');
INSERT INTO `ubigeo_inei` VALUES (1149, '12', '01', '25', 'PILCOMAYO');
INSERT INTO `ubigeo_inei` VALUES (1150, '12', '01', '26', 'PUCARA');
INSERT INTO `ubigeo_inei` VALUES (1151, '12', '01', '27', 'QUICHUAY');
INSERT INTO `ubigeo_inei` VALUES (1152, '12', '01', '28', 'QUILCAS');
INSERT INTO `ubigeo_inei` VALUES (1153, '12', '01', '29', 'SAN AGUSTIN');
INSERT INTO `ubigeo_inei` VALUES (1154, '12', '01', '30', 'SAN JERONIMO DE TUNAN');
INSERT INTO `ubigeo_inei` VALUES (1155, '12', '01', '32', 'SAÑO');
INSERT INTO `ubigeo_inei` VALUES (1156, '12', '01', '33', 'SAPALLANGA');
INSERT INTO `ubigeo_inei` VALUES (1157, '12', '01', '34', 'SICAYA');
INSERT INTO `ubigeo_inei` VALUES (1158, '12', '01', '35', 'SANTO DOMINGO DE ACOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1159, '12', '01', '36', 'VIQUES');
INSERT INTO `ubigeo_inei` VALUES (1160, '12', '02', '00', 'CONCEPCION');
INSERT INTO `ubigeo_inei` VALUES (1161, '12', '02', '01', 'CONCEPCION');
INSERT INTO `ubigeo_inei` VALUES (1162, '12', '02', '02', 'ACO');
INSERT INTO `ubigeo_inei` VALUES (1163, '12', '02', '03', 'ANDAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1164, '12', '02', '04', 'CHAMBARA');
INSERT INTO `ubigeo_inei` VALUES (1165, '12', '02', '05', 'COCHAS');
INSERT INTO `ubigeo_inei` VALUES (1166, '12', '02', '06', 'COMAS');
INSERT INTO `ubigeo_inei` VALUES (1167, '12', '02', '07', 'HEROINAS TOLEDO');
INSERT INTO `ubigeo_inei` VALUES (1168, '12', '02', '08', 'MANZANARES');
INSERT INTO `ubigeo_inei` VALUES (1169, '12', '02', '09', 'MARISCAL CASTILLA');
INSERT INTO `ubigeo_inei` VALUES (1170, '12', '02', '10', 'MATAHUASI');
INSERT INTO `ubigeo_inei` VALUES (1171, '12', '02', '11', 'MITO');
INSERT INTO `ubigeo_inei` VALUES (1172, '12', '02', '12', 'NUEVE DE JULIO');
INSERT INTO `ubigeo_inei` VALUES (1173, '12', '02', '13', 'ORCOTUNA');
INSERT INTO `ubigeo_inei` VALUES (1174, '12', '02', '14', 'SAN JOSE DE QUERO');
INSERT INTO `ubigeo_inei` VALUES (1175, '12', '02', '15', 'SANTA ROSA DE OCOPA');
INSERT INTO `ubigeo_inei` VALUES (1176, '12', '03', '00', 'CHANCHAMAYO');
INSERT INTO `ubigeo_inei` VALUES (1177, '12', '03', '01', 'CHANCHAMAYO');
INSERT INTO `ubigeo_inei` VALUES (1178, '12', '03', '02', 'PERENE');
INSERT INTO `ubigeo_inei` VALUES (1179, '12', '03', '03', 'PICHANAQUI');
INSERT INTO `ubigeo_inei` VALUES (1180, '12', '03', '04', 'SAN LUIS DE SHUARO');
INSERT INTO `ubigeo_inei` VALUES (1181, '12', '03', '05', 'SAN RAMON');
INSERT INTO `ubigeo_inei` VALUES (1182, '12', '03', '06', 'VITOC');
INSERT INTO `ubigeo_inei` VALUES (1183, '12', '04', '00', 'JAUJA');
INSERT INTO `ubigeo_inei` VALUES (1184, '12', '04', '01', 'JAUJA');
INSERT INTO `ubigeo_inei` VALUES (1185, '12', '04', '02', 'ACOLLA');
INSERT INTO `ubigeo_inei` VALUES (1186, '12', '04', '03', 'APATA');
INSERT INTO `ubigeo_inei` VALUES (1187, '12', '04', '04', 'ATAURA');
INSERT INTO `ubigeo_inei` VALUES (1188, '12', '04', '05', 'CANCHAYLLO');
INSERT INTO `ubigeo_inei` VALUES (1189, '12', '04', '06', 'CURICACA');
INSERT INTO `ubigeo_inei` VALUES (1190, '12', '04', '07', 'EL MANTARO');
INSERT INTO `ubigeo_inei` VALUES (1191, '12', '04', '08', 'HUAMALI');
INSERT INTO `ubigeo_inei` VALUES (1192, '12', '04', '09', 'HUARIPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1193, '12', '04', '10', 'HUERTAS');
INSERT INTO `ubigeo_inei` VALUES (1194, '12', '04', '11', 'JANJAILLO');
INSERT INTO `ubigeo_inei` VALUES (1195, '12', '04', '12', 'JULCAN');
INSERT INTO `ubigeo_inei` VALUES (1196, '12', '04', '13', 'LEONOR ORDOÑEZ');
INSERT INTO `ubigeo_inei` VALUES (1197, '12', '04', '14', 'LLOCLLAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1198, '12', '04', '15', 'MARCO');
INSERT INTO `ubigeo_inei` VALUES (1199, '12', '04', '16', 'MASMA');
INSERT INTO `ubigeo_inei` VALUES (1200, '12', '04', '17', 'MASMA CHICCHE');
INSERT INTO `ubigeo_inei` VALUES (1201, '12', '04', '18', 'MOLINOS');
INSERT INTO `ubigeo_inei` VALUES (1202, '12', '04', '19', 'MONOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1203, '12', '04', '20', 'MUQUI');
INSERT INTO `ubigeo_inei` VALUES (1204, '12', '04', '21', 'MUQUIYAUYO');
INSERT INTO `ubigeo_inei` VALUES (1205, '12', '04', '22', 'PACA');
INSERT INTO `ubigeo_inei` VALUES (1206, '12', '04', '23', 'PACCHA');
INSERT INTO `ubigeo_inei` VALUES (1207, '12', '04', '24', 'PANCAN');
INSERT INTO `ubigeo_inei` VALUES (1208, '12', '04', '25', 'PARCO');
INSERT INTO `ubigeo_inei` VALUES (1209, '12', '04', '26', 'POMACANCHA');
INSERT INTO `ubigeo_inei` VALUES (1210, '12', '04', '27', 'RICRAN');
INSERT INTO `ubigeo_inei` VALUES (1211, '12', '04', '28', 'SAN LORENZO');
INSERT INTO `ubigeo_inei` VALUES (1212, '12', '04', '29', 'SAN PEDRO DE CHUNAN');
INSERT INTO `ubigeo_inei` VALUES (1213, '12', '04', '30', 'SAUSA');
INSERT INTO `ubigeo_inei` VALUES (1214, '12', '04', '31', 'SINCOS');
INSERT INTO `ubigeo_inei` VALUES (1215, '12', '04', '32', 'TUNAN MARCA');
INSERT INTO `ubigeo_inei` VALUES (1216, '12', '04', '33', 'YAULI');
INSERT INTO `ubigeo_inei` VALUES (1217, '12', '04', '34', 'YAUYOS');
INSERT INTO `ubigeo_inei` VALUES (1218, '12', '05', '00', 'JUNIN');
INSERT INTO `ubigeo_inei` VALUES (1219, '12', '05', '01', 'JUNIN');
INSERT INTO `ubigeo_inei` VALUES (1220, '12', '05', '02', 'CARHUAMAYO');
INSERT INTO `ubigeo_inei` VALUES (1221, '12', '05', '03', 'ONDORES');
INSERT INTO `ubigeo_inei` VALUES (1222, '12', '05', '04', 'ULCUMAYO');
INSERT INTO `ubigeo_inei` VALUES (1223, '12', '06', '00', 'SATIPO');
INSERT INTO `ubigeo_inei` VALUES (1224, '12', '06', '01', 'SATIPO');
INSERT INTO `ubigeo_inei` VALUES (1225, '12', '06', '02', 'COVIRIALI');
INSERT INTO `ubigeo_inei` VALUES (1226, '12', '06', '03', 'LLAYLLA');
INSERT INTO `ubigeo_inei` VALUES (1227, '12', '06', '04', 'MAZAMARI');
INSERT INTO `ubigeo_inei` VALUES (1228, '12', '06', '05', 'PAMPA HERMOSA');
INSERT INTO `ubigeo_inei` VALUES (1229, '12', '06', '06', 'PANGOA');
INSERT INTO `ubigeo_inei` VALUES (1230, '12', '06', '07', 'RIO NEGRO');
INSERT INTO `ubigeo_inei` VALUES (1231, '12', '06', '08', 'RIO TAMBO');
INSERT INTO `ubigeo_inei` VALUES (1232, '12', '06', '99', 'MAZAMARI-PANGOA');
INSERT INTO `ubigeo_inei` VALUES (1233, '12', '07', '00', 'TARMA');
INSERT INTO `ubigeo_inei` VALUES (1234, '12', '07', '01', 'TARMA');
INSERT INTO `ubigeo_inei` VALUES (1235, '12', '07', '02', 'ACOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1236, '12', '07', '03', 'HUARICOLCA');
INSERT INTO `ubigeo_inei` VALUES (1237, '12', '07', '04', 'HUASAHUASI');
INSERT INTO `ubigeo_inei` VALUES (1238, '12', '07', '05', 'LA UNION');
INSERT INTO `ubigeo_inei` VALUES (1239, '12', '07', '06', 'PALCA');
INSERT INTO `ubigeo_inei` VALUES (1240, '12', '07', '07', 'PALCAMAYO');
INSERT INTO `ubigeo_inei` VALUES (1241, '12', '07', '08', 'SAN PEDRO DE CAJAS');
INSERT INTO `ubigeo_inei` VALUES (1242, '12', '07', '09', 'TAPO');
INSERT INTO `ubigeo_inei` VALUES (1243, '12', '08', '00', 'YAULI');
INSERT INTO `ubigeo_inei` VALUES (1244, '12', '08', '01', 'LA OROYA');
INSERT INTO `ubigeo_inei` VALUES (1245, '12', '08', '02', 'CHACAPALPA');
INSERT INTO `ubigeo_inei` VALUES (1246, '12', '08', '03', 'HUAY-HUAY');
INSERT INTO `ubigeo_inei` VALUES (1247, '12', '08', '04', 'MARCAPOMACOCHA');
INSERT INTO `ubigeo_inei` VALUES (1248, '12', '08', '05', 'MOROCOCHA');
INSERT INTO `ubigeo_inei` VALUES (1249, '12', '08', '06', 'PACCHA');
INSERT INTO `ubigeo_inei` VALUES (1250, '12', '08', '07', 'SANTA BARBARA DE CARHUACAYAN');
INSERT INTO `ubigeo_inei` VALUES (1251, '12', '08', '08', 'SANTA ROSA DE SACCO');
INSERT INTO `ubigeo_inei` VALUES (1252, '12', '08', '09', 'SUITUCANCHA');
INSERT INTO `ubigeo_inei` VALUES (1253, '12', '08', '10', 'YAULI');
INSERT INTO `ubigeo_inei` VALUES (1254, '12', '09', '00', 'CHUPACA');
INSERT INTO `ubigeo_inei` VALUES (1255, '12', '09', '01', 'CHUPACA');
INSERT INTO `ubigeo_inei` VALUES (1256, '12', '09', '02', 'AHUAC');
INSERT INTO `ubigeo_inei` VALUES (1257, '12', '09', '03', 'CHONGOS BAJO');
INSERT INTO `ubigeo_inei` VALUES (1258, '12', '09', '04', 'HUACHAC');
INSERT INTO `ubigeo_inei` VALUES (1259, '12', '09', '05', 'HUAMANCACA CHICO');
INSERT INTO `ubigeo_inei` VALUES (1260, '12', '09', '06', 'SAN JUAN DE ISCOS');
INSERT INTO `ubigeo_inei` VALUES (1261, '12', '09', '07', 'SAN JUAN DE JARPA');
INSERT INTO `ubigeo_inei` VALUES (1262, '12', '09', '08', '3 DE DICIEMBRE');
INSERT INTO `ubigeo_inei` VALUES (1263, '12', '09', '09', 'YANACANCHA');
INSERT INTO `ubigeo_inei` VALUES (1264, '13', '00', '00', 'LA LIBERTAD');
INSERT INTO `ubigeo_inei` VALUES (1265, '13', '01', '00', 'TRUJILLO');
INSERT INTO `ubigeo_inei` VALUES (1266, '13', '01', '01', 'TRUJILLO');
INSERT INTO `ubigeo_inei` VALUES (1267, '13', '01', '02', 'EL PORVENIR');
INSERT INTO `ubigeo_inei` VALUES (1268, '13', '01', '03', 'FLORENCIA DE MORA');
INSERT INTO `ubigeo_inei` VALUES (1269, '13', '01', '04', 'HUANCHACO');
INSERT INTO `ubigeo_inei` VALUES (1270, '13', '01', '05', 'LA ESPERANZA');
INSERT INTO `ubigeo_inei` VALUES (1271, '13', '01', '06', 'LAREDO');
INSERT INTO `ubigeo_inei` VALUES (1272, '13', '01', '07', 'MOCHE');
INSERT INTO `ubigeo_inei` VALUES (1273, '13', '01', '08', 'POROTO');
INSERT INTO `ubigeo_inei` VALUES (1274, '13', '01', '09', 'SALAVERRY');
INSERT INTO `ubigeo_inei` VALUES (1275, '13', '01', '10', 'SIMBAL');
INSERT INTO `ubigeo_inei` VALUES (1276, '13', '01', '11', 'VICTOR LARCO HERRERA');
INSERT INTO `ubigeo_inei` VALUES (1277, '13', '02', '00', 'ASCOPE');
INSERT INTO `ubigeo_inei` VALUES (1278, '13', '02', '01', 'ASCOPE');
INSERT INTO `ubigeo_inei` VALUES (1279, '13', '02', '02', 'CHICAMA');
INSERT INTO `ubigeo_inei` VALUES (1280, '13', '02', '03', 'CHOCOPE');
INSERT INTO `ubigeo_inei` VALUES (1281, '13', '02', '04', 'MAGDALENA DE CAO');
INSERT INTO `ubigeo_inei` VALUES (1282, '13', '02', '05', 'PAIJAN');
INSERT INTO `ubigeo_inei` VALUES (1283, '13', '02', '06', 'RAZURI');
INSERT INTO `ubigeo_inei` VALUES (1284, '13', '02', '07', 'SANTIAGO DE CAO');
INSERT INTO `ubigeo_inei` VALUES (1285, '13', '02', '08', 'CASA GRANDE');
INSERT INTO `ubigeo_inei` VALUES (1286, '13', '03', '00', 'BOLIVAR');
INSERT INTO `ubigeo_inei` VALUES (1287, '13', '03', '01', 'BOLIVAR');
INSERT INTO `ubigeo_inei` VALUES (1288, '13', '03', '02', 'BAMBAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1289, '13', '03', '03', 'CONDORMARCA');
INSERT INTO `ubigeo_inei` VALUES (1290, '13', '03', '04', 'LONGOTEA');
INSERT INTO `ubigeo_inei` VALUES (1291, '13', '03', '05', 'UCHUMARCA');
INSERT INTO `ubigeo_inei` VALUES (1292, '13', '03', '06', 'UCUNCHA');
INSERT INTO `ubigeo_inei` VALUES (1293, '13', '04', '00', 'CHEPEN');
INSERT INTO `ubigeo_inei` VALUES (1294, '13', '04', '01', 'CHEPEN');
INSERT INTO `ubigeo_inei` VALUES (1295, '13', '04', '02', 'PACANGA');
INSERT INTO `ubigeo_inei` VALUES (1296, '13', '04', '03', 'PUEBLO NUEVO');
INSERT INTO `ubigeo_inei` VALUES (1297, '13', '05', '00', 'JULCAN');
INSERT INTO `ubigeo_inei` VALUES (1298, '13', '05', '01', 'JULCAN');
INSERT INTO `ubigeo_inei` VALUES (1299, '13', '05', '02', 'CALAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1300, '13', '05', '03', 'CARABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1301, '13', '05', '04', 'HUASO');
INSERT INTO `ubigeo_inei` VALUES (1302, '13', '06', '00', 'OTUZCO');
INSERT INTO `ubigeo_inei` VALUES (1303, '13', '06', '01', 'OTUZCO');
INSERT INTO `ubigeo_inei` VALUES (1304, '13', '06', '02', 'AGALLPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1305, '13', '06', '04', 'CHARAT');
INSERT INTO `ubigeo_inei` VALUES (1306, '13', '06', '05', 'HUARANCHAL');
INSERT INTO `ubigeo_inei` VALUES (1307, '13', '06', '06', 'LA CUESTA');
INSERT INTO `ubigeo_inei` VALUES (1308, '13', '06', '08', 'MACHE');
INSERT INTO `ubigeo_inei` VALUES (1309, '13', '06', '10', 'PARANDAY');
INSERT INTO `ubigeo_inei` VALUES (1310, '13', '06', '11', 'SALPO');
INSERT INTO `ubigeo_inei` VALUES (1311, '13', '06', '13', 'SINSICAP');
INSERT INTO `ubigeo_inei` VALUES (1312, '13', '06', '14', 'USQUIL');
INSERT INTO `ubigeo_inei` VALUES (1313, '13', '07', '00', 'PACASMAYO');
INSERT INTO `ubigeo_inei` VALUES (1314, '13', '07', '01', 'SAN PEDRO DE LLOC');
INSERT INTO `ubigeo_inei` VALUES (1315, '13', '07', '02', 'GUADALUPE');
INSERT INTO `ubigeo_inei` VALUES (1316, '13', '07', '03', 'JEQUETEPEQUE');
INSERT INTO `ubigeo_inei` VALUES (1317, '13', '07', '04', 'PACASMAYO');
INSERT INTO `ubigeo_inei` VALUES (1318, '13', '07', '05', 'SAN JOSE');
INSERT INTO `ubigeo_inei` VALUES (1319, '13', '08', '00', 'PATAZ');
INSERT INTO `ubigeo_inei` VALUES (1320, '13', '08', '01', 'TAYABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1321, '13', '08', '02', 'BULDIBUYO');
INSERT INTO `ubigeo_inei` VALUES (1322, '13', '08', '03', 'CHILLIA');
INSERT INTO `ubigeo_inei` VALUES (1323, '13', '08', '04', 'HUANCASPATA');
INSERT INTO `ubigeo_inei` VALUES (1324, '13', '08', '05', 'HUAYLILLAS');
INSERT INTO `ubigeo_inei` VALUES (1325, '13', '08', '06', 'HUAYO');
INSERT INTO `ubigeo_inei` VALUES (1326, '13', '08', '07', 'ONGON');
INSERT INTO `ubigeo_inei` VALUES (1327, '13', '08', '08', 'PARCOY');
INSERT INTO `ubigeo_inei` VALUES (1328, '13', '08', '09', 'PATAZ');
INSERT INTO `ubigeo_inei` VALUES (1329, '13', '08', '10', 'PIAS');
INSERT INTO `ubigeo_inei` VALUES (1330, '13', '08', '11', 'SANTIAGO DE CHALLAS');
INSERT INTO `ubigeo_inei` VALUES (1331, '13', '08', '12', 'TAURIJA');
INSERT INTO `ubigeo_inei` VALUES (1332, '13', '08', '13', 'URPAY');
INSERT INTO `ubigeo_inei` VALUES (1333, '13', '09', '00', 'SANCHEZ CARRION');
INSERT INTO `ubigeo_inei` VALUES (1334, '13', '09', '01', 'HUAMACHUCO');
INSERT INTO `ubigeo_inei` VALUES (1335, '13', '09', '02', 'CHUGAY');
INSERT INTO `ubigeo_inei` VALUES (1336, '13', '09', '03', 'COCHORCO');
INSERT INTO `ubigeo_inei` VALUES (1337, '13', '09', '04', 'CURGOS');
INSERT INTO `ubigeo_inei` VALUES (1338, '13', '09', '05', 'MARCABAL');
INSERT INTO `ubigeo_inei` VALUES (1339, '13', '09', '06', 'SANAGORAN');
INSERT INTO `ubigeo_inei` VALUES (1340, '13', '09', '07', 'SARIN');
INSERT INTO `ubigeo_inei` VALUES (1341, '13', '09', '08', 'SARTIMBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1342, '13', '10', '00', 'SANTIAGO DE CHUCO');
INSERT INTO `ubigeo_inei` VALUES (1343, '13', '10', '01', 'SANTIAGO DE CHUCO');
INSERT INTO `ubigeo_inei` VALUES (1344, '13', '10', '02', 'ANGASMARCA');
INSERT INTO `ubigeo_inei` VALUES (1345, '13', '10', '03', 'CACHICADAN');
INSERT INTO `ubigeo_inei` VALUES (1346, '13', '10', '04', 'MOLLEBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1347, '13', '10', '05', 'MOLLEPATA');
INSERT INTO `ubigeo_inei` VALUES (1348, '13', '10', '06', 'QUIRUVILCA');
INSERT INTO `ubigeo_inei` VALUES (1349, '13', '10', '07', 'SANTA CRUZ DE CHUCA');
INSERT INTO `ubigeo_inei` VALUES (1350, '13', '10', '08', 'SITABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1351, '13', '11', '00', 'GRAN CHIMU');
INSERT INTO `ubigeo_inei` VALUES (1352, '13', '11', '01', 'CASCAS');
INSERT INTO `ubigeo_inei` VALUES (1353, '13', '11', '02', 'LUCMA');
INSERT INTO `ubigeo_inei` VALUES (1354, '13', '11', '03', 'MARMOT');
INSERT INTO `ubigeo_inei` VALUES (1355, '13', '11', '04', 'SAYAPULLO');
INSERT INTO `ubigeo_inei` VALUES (1356, '13', '12', '00', 'VIRU');
INSERT INTO `ubigeo_inei` VALUES (1357, '13', '12', '01', 'VIRU');
INSERT INTO `ubigeo_inei` VALUES (1358, '13', '12', '02', 'CHAO');
INSERT INTO `ubigeo_inei` VALUES (1359, '13', '12', '03', 'GUADALUPITO');
INSERT INTO `ubigeo_inei` VALUES (1360, '14', '00', '00', 'LAMBAYEQUE');
INSERT INTO `ubigeo_inei` VALUES (1361, '14', '01', '00', 'CHICLAYO');
INSERT INTO `ubigeo_inei` VALUES (1362, '14', '01', '01', 'CHICLAYO');
INSERT INTO `ubigeo_inei` VALUES (1363, '14', '01', '02', 'CHONGOYAPE');
INSERT INTO `ubigeo_inei` VALUES (1364, '14', '01', '03', 'ETEN');
INSERT INTO `ubigeo_inei` VALUES (1365, '14', '01', '04', 'ETEN PUERTO');
INSERT INTO `ubigeo_inei` VALUES (1366, '14', '01', '05', 'JOSE LEONARDO ORTIZ');
INSERT INTO `ubigeo_inei` VALUES (1367, '14', '01', '06', 'LA VICTORIA');
INSERT INTO `ubigeo_inei` VALUES (1368, '14', '01', '07', 'LAGUNAS');
INSERT INTO `ubigeo_inei` VALUES (1369, '14', '01', '08', 'MONSEFU');
INSERT INTO `ubigeo_inei` VALUES (1370, '14', '01', '09', 'NUEVA ARICA');
INSERT INTO `ubigeo_inei` VALUES (1371, '14', '01', '10', 'OYOTUN');
INSERT INTO `ubigeo_inei` VALUES (1372, '14', '01', '11', 'PICSI');
INSERT INTO `ubigeo_inei` VALUES (1373, '14', '01', '12', 'PIMENTEL');
INSERT INTO `ubigeo_inei` VALUES (1374, '14', '01', '13', 'REQUE');
INSERT INTO `ubigeo_inei` VALUES (1375, '14', '01', '14', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (1376, '14', '01', '15', 'SAÑA');
INSERT INTO `ubigeo_inei` VALUES (1377, '14', '01', '16', 'CAYALTÍ');
INSERT INTO `ubigeo_inei` VALUES (1378, '14', '01', '17', 'PATAPO');
INSERT INTO `ubigeo_inei` VALUES (1379, '14', '01', '18', 'POMALCA');
INSERT INTO `ubigeo_inei` VALUES (1380, '14', '01', '19', 'PUCALÁ');
INSERT INTO `ubigeo_inei` VALUES (1381, '14', '01', '20', 'TUMÁN');
INSERT INTO `ubigeo_inei` VALUES (1382, '14', '02', '00', 'FERREÑAFE');
INSERT INTO `ubigeo_inei` VALUES (1383, '14', '02', '01', 'FERREÑAFE');
INSERT INTO `ubigeo_inei` VALUES (1384, '14', '02', '02', 'CAÑARIS');
INSERT INTO `ubigeo_inei` VALUES (1385, '14', '02', '03', 'INCAHUASI');
INSERT INTO `ubigeo_inei` VALUES (1386, '14', '02', '04', 'MANUEL ANTONIO MESONES MURO');
INSERT INTO `ubigeo_inei` VALUES (1387, '14', '02', '05', 'PITIPO');
INSERT INTO `ubigeo_inei` VALUES (1388, '14', '02', '06', 'PUEBLO NUEVO');
INSERT INTO `ubigeo_inei` VALUES (1389, '14', '03', '00', 'LAMBAYEQUE');
INSERT INTO `ubigeo_inei` VALUES (1390, '14', '03', '01', 'LAMBAYEQUE');
INSERT INTO `ubigeo_inei` VALUES (1391, '14', '03', '02', 'CHOCHOPE');
INSERT INTO `ubigeo_inei` VALUES (1392, '14', '03', '03', 'ILLIMO');
INSERT INTO `ubigeo_inei` VALUES (1393, '14', '03', '04', 'JAYANCA');
INSERT INTO `ubigeo_inei` VALUES (1394, '14', '03', '05', 'MOCHUMI');
INSERT INTO `ubigeo_inei` VALUES (1395, '14', '03', '06', 'MORROPE');
INSERT INTO `ubigeo_inei` VALUES (1396, '14', '03', '07', 'MOTUPE');
INSERT INTO `ubigeo_inei` VALUES (1397, '14', '03', '08', 'OLMOS');
INSERT INTO `ubigeo_inei` VALUES (1398, '14', '03', '09', 'PACORA');
INSERT INTO `ubigeo_inei` VALUES (1399, '14', '03', '10', 'SALAS');
INSERT INTO `ubigeo_inei` VALUES (1400, '14', '03', '11', 'SAN JOSE');
INSERT INTO `ubigeo_inei` VALUES (1401, '14', '03', '12', 'TUCUME');
INSERT INTO `ubigeo_inei` VALUES (1402, '15', '00', '00', 'LIMA');
INSERT INTO `ubigeo_inei` VALUES (1403, '15', '01', '00', 'LIMA');
INSERT INTO `ubigeo_inei` VALUES (1404, '15', '01', '01', 'LIMA');
INSERT INTO `ubigeo_inei` VALUES (1405, '15', '01', '02', 'ANCON');
INSERT INTO `ubigeo_inei` VALUES (1406, '15', '01', '03', 'ATE');
INSERT INTO `ubigeo_inei` VALUES (1407, '15', '01', '04', 'BARRANCO');
INSERT INTO `ubigeo_inei` VALUES (1408, '15', '01', '05', 'BREÑA');
INSERT INTO `ubigeo_inei` VALUES (1409, '15', '01', '06', 'CARABAYLLO');
INSERT INTO `ubigeo_inei` VALUES (1410, '15', '01', '07', 'CHACLACAYO');
INSERT INTO `ubigeo_inei` VALUES (1411, '15', '01', '08', 'CHORRILLOS');
INSERT INTO `ubigeo_inei` VALUES (1412, '15', '01', '09', 'CIENEGUILLA');
INSERT INTO `ubigeo_inei` VALUES (1413, '15', '01', '10', 'COMAS');
INSERT INTO `ubigeo_inei` VALUES (1414, '15', '01', '11', 'EL AGUSTINO');
INSERT INTO `ubigeo_inei` VALUES (1415, '15', '01', '12', 'INDEPENDENCIA');
INSERT INTO `ubigeo_inei` VALUES (1416, '15', '01', '13', 'JESUS MARIA');
INSERT INTO `ubigeo_inei` VALUES (1417, '15', '01', '14', 'LA MOLINA');
INSERT INTO `ubigeo_inei` VALUES (1418, '15', '01', '15', 'LA VICTORIA');
INSERT INTO `ubigeo_inei` VALUES (1419, '15', '01', '16', 'LINCE');
INSERT INTO `ubigeo_inei` VALUES (1420, '15', '01', '17', 'LOS OLIVOS');
INSERT INTO `ubigeo_inei` VALUES (1421, '15', '01', '18', 'LURIGANCHO');
INSERT INTO `ubigeo_inei` VALUES (1422, '15', '01', '19', 'LURIN');
INSERT INTO `ubigeo_inei` VALUES (1423, '15', '01', '20', 'MAGDALENA DEL MAR');
INSERT INTO `ubigeo_inei` VALUES (1424, '15', '01', '21', 'PUEBLO LIBRE (MAGDALENA VIEJA)');
INSERT INTO `ubigeo_inei` VALUES (1425, '15', '01', '22', 'MIRAFLORES');
INSERT INTO `ubigeo_inei` VALUES (1426, '15', '01', '23', 'PACHACAMAC');
INSERT INTO `ubigeo_inei` VALUES (1427, '15', '01', '24', 'PUCUSANA');
INSERT INTO `ubigeo_inei` VALUES (1428, '15', '01', '25', 'PUENTE PIEDRA');
INSERT INTO `ubigeo_inei` VALUES (1429, '15', '01', '26', 'PUNTA HERMOSA');
INSERT INTO `ubigeo_inei` VALUES (1430, '15', '01', '27', 'PUNTA NEGRA');
INSERT INTO `ubigeo_inei` VALUES (1431, '15', '01', '28', 'RIMAC');
INSERT INTO `ubigeo_inei` VALUES (1432, '15', '01', '29', 'SAN BARTOLO');
INSERT INTO `ubigeo_inei` VALUES (1433, '15', '01', '30', 'SAN BORJA');
INSERT INTO `ubigeo_inei` VALUES (1434, '15', '01', '31', 'SAN ISIDRO');
INSERT INTO `ubigeo_inei` VALUES (1435, '15', '01', '32', 'SAN JUAN DE LURIGANCHO');
INSERT INTO `ubigeo_inei` VALUES (1436, '15', '01', '33', 'SAN JUAN DE MIRAFLORES');
INSERT INTO `ubigeo_inei` VALUES (1437, '15', '01', '34', 'SAN LUIS');
INSERT INTO `ubigeo_inei` VALUES (1438, '15', '01', '35', 'SAN MARTIN DE PORRES');
INSERT INTO `ubigeo_inei` VALUES (1439, '15', '01', '36', 'SAN MIGUEL');
INSERT INTO `ubigeo_inei` VALUES (1440, '15', '01', '37', 'SANTA ANITA');
INSERT INTO `ubigeo_inei` VALUES (1441, '15', '01', '38', 'SANTA MARIA DEL MAR');
INSERT INTO `ubigeo_inei` VALUES (1442, '15', '01', '39', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (1443, '15', '01', '40', 'SANTIAGO DE SURCO');
INSERT INTO `ubigeo_inei` VALUES (1444, '15', '01', '41', 'SURQUILLO');
INSERT INTO `ubigeo_inei` VALUES (1445, '15', '01', '42', 'VILLA EL SALVADOR');
INSERT INTO `ubigeo_inei` VALUES (1446, '15', '01', '43', 'VILLA MARIA DEL TRIUNFO');
INSERT INTO `ubigeo_inei` VALUES (1447, '15', '02', '00', 'BARRANCA');
INSERT INTO `ubigeo_inei` VALUES (1448, '15', '02', '01', 'BARRANCA');
INSERT INTO `ubigeo_inei` VALUES (1449, '15', '02', '02', 'PARAMONGA');
INSERT INTO `ubigeo_inei` VALUES (1450, '15', '02', '03', 'PATIVILCA');
INSERT INTO `ubigeo_inei` VALUES (1451, '15', '02', '04', 'SUPE');
INSERT INTO `ubigeo_inei` VALUES (1452, '15', '02', '05', 'SUPE PUERTO');
INSERT INTO `ubigeo_inei` VALUES (1453, '15', '03', '00', 'CAJATAMBO');
INSERT INTO `ubigeo_inei` VALUES (1454, '15', '03', '01', 'CAJATAMBO');
INSERT INTO `ubigeo_inei` VALUES (1455, '15', '03', '02', 'COPA');
INSERT INTO `ubigeo_inei` VALUES (1456, '15', '03', '03', 'GORGOR');
INSERT INTO `ubigeo_inei` VALUES (1457, '15', '03', '04', 'HUANCAPON');
INSERT INTO `ubigeo_inei` VALUES (1458, '15', '03', '05', 'MANAS');
INSERT INTO `ubigeo_inei` VALUES (1459, '15', '04', '00', 'CANTA');
INSERT INTO `ubigeo_inei` VALUES (1460, '15', '04', '01', 'CANTA');
INSERT INTO `ubigeo_inei` VALUES (1461, '15', '04', '02', 'ARAHUAY');
INSERT INTO `ubigeo_inei` VALUES (1462, '15', '04', '03', 'HUAMANTANGA');
INSERT INTO `ubigeo_inei` VALUES (1463, '15', '04', '04', 'HUAROS');
INSERT INTO `ubigeo_inei` VALUES (1464, '15', '04', '05', 'LACHAQUI');
INSERT INTO `ubigeo_inei` VALUES (1465, '15', '04', '06', 'SAN BUENAVENTURA');
INSERT INTO `ubigeo_inei` VALUES (1466, '15', '04', '07', 'SANTA ROSA DE QUIVES');
INSERT INTO `ubigeo_inei` VALUES (1467, '15', '05', '00', 'CAÑETE');
INSERT INTO `ubigeo_inei` VALUES (1468, '15', '05', '01', 'SAN VICENTE DE CAÑETE');
INSERT INTO `ubigeo_inei` VALUES (1469, '15', '05', '02', 'ASIA');
INSERT INTO `ubigeo_inei` VALUES (1470, '15', '05', '03', 'CALANGO');
INSERT INTO `ubigeo_inei` VALUES (1471, '15', '05', '04', 'CERRO AZUL');
INSERT INTO `ubigeo_inei` VALUES (1472, '15', '05', '05', 'CHILCA');
INSERT INTO `ubigeo_inei` VALUES (1473, '15', '05', '06', 'COAYLLO');
INSERT INTO `ubigeo_inei` VALUES (1474, '15', '05', '07', 'IMPERIAL');
INSERT INTO `ubigeo_inei` VALUES (1475, '15', '05', '08', 'LUNAHUANA');
INSERT INTO `ubigeo_inei` VALUES (1476, '15', '05', '09', 'MALA');
INSERT INTO `ubigeo_inei` VALUES (1477, '15', '05', '10', 'NUEVO IMPERIAL');
INSERT INTO `ubigeo_inei` VALUES (1478, '15', '05', '11', 'PACARAN');
INSERT INTO `ubigeo_inei` VALUES (1479, '15', '05', '12', 'QUILMANA');
INSERT INTO `ubigeo_inei` VALUES (1480, '15', '05', '13', 'SAN ANTONIO');
INSERT INTO `ubigeo_inei` VALUES (1481, '15', '05', '14', 'SAN LUIS');
INSERT INTO `ubigeo_inei` VALUES (1482, '15', '05', '15', 'SANTA CRUZ DE FLORES');
INSERT INTO `ubigeo_inei` VALUES (1483, '15', '05', '16', 'ZUÑIGA');
INSERT INTO `ubigeo_inei` VALUES (1484, '15', '06', '00', 'HUARAL');
INSERT INTO `ubigeo_inei` VALUES (1485, '15', '06', '01', 'HUARAL');
INSERT INTO `ubigeo_inei` VALUES (1486, '15', '06', '02', 'ATAVILLOS ALTO');
INSERT INTO `ubigeo_inei` VALUES (1487, '15', '06', '03', 'ATAVILLOS BAJO');
INSERT INTO `ubigeo_inei` VALUES (1488, '15', '06', '04', 'AUCALLAMA');
INSERT INTO `ubigeo_inei` VALUES (1489, '15', '06', '05', 'CHANCAY');
INSERT INTO `ubigeo_inei` VALUES (1490, '15', '06', '06', 'IHUARI');
INSERT INTO `ubigeo_inei` VALUES (1491, '15', '06', '07', 'LAMPIAN');
INSERT INTO `ubigeo_inei` VALUES (1492, '15', '06', '08', 'PACARAOS');
INSERT INTO `ubigeo_inei` VALUES (1493, '15', '06', '09', 'SAN MIGUEL DE ACOS');
INSERT INTO `ubigeo_inei` VALUES (1494, '15', '06', '10', 'SANTA CRUZ DE ANDAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1495, '15', '06', '11', 'SUMBILCA');
INSERT INTO `ubigeo_inei` VALUES (1496, '15', '06', '12', 'VEINTISIETE DE NOVIEMBRE');
INSERT INTO `ubigeo_inei` VALUES (1497, '15', '07', '00', 'HUAROCHIRI');
INSERT INTO `ubigeo_inei` VALUES (1498, '15', '07', '01', 'MATUCANA');
INSERT INTO `ubigeo_inei` VALUES (1499, '15', '07', '02', 'ANTIOQUIA');
INSERT INTO `ubigeo_inei` VALUES (1500, '15', '07', '03', 'CALLAHUANCA');
INSERT INTO `ubigeo_inei` VALUES (1501, '15', '07', '04', 'CARAMPOMA');
INSERT INTO `ubigeo_inei` VALUES (1502, '15', '07', '05', 'CHICLA');
INSERT INTO `ubigeo_inei` VALUES (1503, '15', '07', '06', 'CUENCA');
INSERT INTO `ubigeo_inei` VALUES (1504, '15', '07', '07', 'HUACHUPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1505, '15', '07', '08', 'HUANZA');
INSERT INTO `ubigeo_inei` VALUES (1506, '15', '07', '09', 'HUAROCHIRI');
INSERT INTO `ubigeo_inei` VALUES (1507, '15', '07', '10', 'LAHUAYTAMBO');
INSERT INTO `ubigeo_inei` VALUES (1508, '15', '07', '11', 'LANGA');
INSERT INTO `ubigeo_inei` VALUES (1509, '15', '07', '12', 'LARAOS');
INSERT INTO `ubigeo_inei` VALUES (1510, '15', '07', '13', 'MARIATANA');
INSERT INTO `ubigeo_inei` VALUES (1511, '15', '07', '14', 'RICARDO PALMA');
INSERT INTO `ubigeo_inei` VALUES (1512, '15', '07', '15', 'SAN ANDRES DE TUPICOCHA');
INSERT INTO `ubigeo_inei` VALUES (1513, '15', '07', '16', 'SAN ANTONIO');
INSERT INTO `ubigeo_inei` VALUES (1514, '15', '07', '17', 'SAN BARTOLOME');
INSERT INTO `ubigeo_inei` VALUES (1515, '15', '07', '18', 'SAN DAMIAN');
INSERT INTO `ubigeo_inei` VALUES (1516, '15', '07', '19', 'SAN JUAN DE IRIS');
INSERT INTO `ubigeo_inei` VALUES (1517, '15', '07', '20', 'SAN JUAN DE TANTARANCHE');
INSERT INTO `ubigeo_inei` VALUES (1518, '15', '07', '21', 'SAN LORENZO DE QUINTI');
INSERT INTO `ubigeo_inei` VALUES (1519, '15', '07', '22', 'SAN MATEO');
INSERT INTO `ubigeo_inei` VALUES (1520, '15', '07', '23', 'SAN MATEO DE OTAO');
INSERT INTO `ubigeo_inei` VALUES (1521, '15', '07', '24', 'SAN PEDRO DE CASTA');
INSERT INTO `ubigeo_inei` VALUES (1522, '15', '07', '25', 'SAN PEDRO DE HUANCAYRE');
INSERT INTO `ubigeo_inei` VALUES (1523, '15', '07', '26', 'SANGALLAYA');
INSERT INTO `ubigeo_inei` VALUES (1524, '15', '07', '27', 'SANTA CRUZ DE COCACHACRA');
INSERT INTO `ubigeo_inei` VALUES (1525, '15', '07', '28', 'SANTA EULALIA');
INSERT INTO `ubigeo_inei` VALUES (1526, '15', '07', '29', 'SANTIAGO DE ANCHUCAYA');
INSERT INTO `ubigeo_inei` VALUES (1527, '15', '07', '30', 'SANTIAGO DE TUNA');
INSERT INTO `ubigeo_inei` VALUES (1528, '15', '07', '31', 'SANTO DOMINGO DE LOS OLLEROS');
INSERT INTO `ubigeo_inei` VALUES (1529, '15', '07', '32', 'SURCO');
INSERT INTO `ubigeo_inei` VALUES (1530, '15', '08', '00', 'HUAURA');
INSERT INTO `ubigeo_inei` VALUES (1531, '15', '08', '01', 'HUACHO');
INSERT INTO `ubigeo_inei` VALUES (1532, '15', '08', '02', 'AMBAR');
INSERT INTO `ubigeo_inei` VALUES (1533, '15', '08', '03', 'CALETA DE CARQUIN');
INSERT INTO `ubigeo_inei` VALUES (1534, '15', '08', '04', 'CHECRAS');
INSERT INTO `ubigeo_inei` VALUES (1535, '15', '08', '05', 'HUALMAY');
INSERT INTO `ubigeo_inei` VALUES (1536, '15', '08', '06', 'HUAURA');
INSERT INTO `ubigeo_inei` VALUES (1537, '15', '08', '07', 'LEONCIO PRADO');
INSERT INTO `ubigeo_inei` VALUES (1538, '15', '08', '08', 'PACCHO');
INSERT INTO `ubigeo_inei` VALUES (1539, '15', '08', '09', 'SANTA LEONOR');
INSERT INTO `ubigeo_inei` VALUES (1540, '15', '08', '10', 'SANTA MARIA');
INSERT INTO `ubigeo_inei` VALUES (1541, '15', '08', '11', 'SAYAN');
INSERT INTO `ubigeo_inei` VALUES (1542, '15', '08', '12', 'VEGUETA');
INSERT INTO `ubigeo_inei` VALUES (1543, '15', '09', '00', 'OYON');
INSERT INTO `ubigeo_inei` VALUES (1544, '15', '09', '01', 'OYON');
INSERT INTO `ubigeo_inei` VALUES (1545, '15', '09', '02', 'ANDAJES');
INSERT INTO `ubigeo_inei` VALUES (1546, '15', '09', '03', 'CAUJUL');
INSERT INTO `ubigeo_inei` VALUES (1547, '15', '09', '04', 'COCHAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1548, '15', '09', '05', 'NAVAN');
INSERT INTO `ubigeo_inei` VALUES (1549, '15', '09', '06', 'PACHANGARA');
INSERT INTO `ubigeo_inei` VALUES (1550, '15', '10', '00', 'YAUYOS');
INSERT INTO `ubigeo_inei` VALUES (1551, '15', '10', '01', 'YAUYOS');
INSERT INTO `ubigeo_inei` VALUES (1552, '15', '10', '02', 'ALIS');
INSERT INTO `ubigeo_inei` VALUES (1553, '15', '10', '03', 'AYAUCA');
INSERT INTO `ubigeo_inei` VALUES (1554, '15', '10', '04', 'AYAVIRI');
INSERT INTO `ubigeo_inei` VALUES (1555, '15', '10', '05', 'AZANGARO');
INSERT INTO `ubigeo_inei` VALUES (1556, '15', '10', '06', 'CACRA');
INSERT INTO `ubigeo_inei` VALUES (1557, '15', '10', '07', 'CARANIA');
INSERT INTO `ubigeo_inei` VALUES (1558, '15', '10', '08', 'CATAHUASI');
INSERT INTO `ubigeo_inei` VALUES (1559, '15', '10', '09', 'CHOCOS');
INSERT INTO `ubigeo_inei` VALUES (1560, '15', '10', '10', 'COCHAS');
INSERT INTO `ubigeo_inei` VALUES (1561, '15', '10', '11', 'COLONIA');
INSERT INTO `ubigeo_inei` VALUES (1562, '15', '10', '12', 'HONGOS');
INSERT INTO `ubigeo_inei` VALUES (1563, '15', '10', '13', 'HUAMPARA');
INSERT INTO `ubigeo_inei` VALUES (1564, '15', '10', '14', 'HUANCAYA');
INSERT INTO `ubigeo_inei` VALUES (1565, '15', '10', '15', 'HUANGASCAR');
INSERT INTO `ubigeo_inei` VALUES (1566, '15', '10', '16', 'HUANTAN');
INSERT INTO `ubigeo_inei` VALUES (1567, '15', '10', '17', 'HUAÑEC');
INSERT INTO `ubigeo_inei` VALUES (1568, '15', '10', '18', 'LARAOS');
INSERT INTO `ubigeo_inei` VALUES (1569, '15', '10', '19', 'LINCHA');
INSERT INTO `ubigeo_inei` VALUES (1570, '15', '10', '20', 'MADEAN');
INSERT INTO `ubigeo_inei` VALUES (1571, '15', '10', '21', 'MIRAFLORES');
INSERT INTO `ubigeo_inei` VALUES (1572, '15', '10', '22', 'OMAS');
INSERT INTO `ubigeo_inei` VALUES (1573, '15', '10', '23', 'PUTINZA');
INSERT INTO `ubigeo_inei` VALUES (1574, '15', '10', '24', 'QUINCHES');
INSERT INTO `ubigeo_inei` VALUES (1575, '15', '10', '25', 'QUINOCAY');
INSERT INTO `ubigeo_inei` VALUES (1576, '15', '10', '26', 'SAN JOAQUIN');
INSERT INTO `ubigeo_inei` VALUES (1577, '15', '10', '27', 'SAN PEDRO DE PILAS');
INSERT INTO `ubigeo_inei` VALUES (1578, '15', '10', '28', 'TANTA');
INSERT INTO `ubigeo_inei` VALUES (1579, '15', '10', '29', 'TAURIPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1580, '15', '10', '30', 'TOMAS');
INSERT INTO `ubigeo_inei` VALUES (1581, '15', '10', '31', 'TUPE');
INSERT INTO `ubigeo_inei` VALUES (1582, '15', '10', '32', 'VIÑAC');
INSERT INTO `ubigeo_inei` VALUES (1583, '15', '10', '33', 'VITIS');
INSERT INTO `ubigeo_inei` VALUES (1584, '16', '00', '00', 'LORETO');
INSERT INTO `ubigeo_inei` VALUES (1585, '16', '01', '00', 'MAYNAS');
INSERT INTO `ubigeo_inei` VALUES (1586, '16', '01', '01', 'IQUITOS');
INSERT INTO `ubigeo_inei` VALUES (1587, '16', '01', '02', 'ALTO NANAY');
INSERT INTO `ubigeo_inei` VALUES (1588, '16', '01', '03', 'FERNANDO LORES');
INSERT INTO `ubigeo_inei` VALUES (1589, '16', '01', '04', 'INDIANA');
INSERT INTO `ubigeo_inei` VALUES (1590, '16', '01', '05', 'LAS AMAZONAS');
INSERT INTO `ubigeo_inei` VALUES (1591, '16', '01', '06', 'MAZAN');
INSERT INTO `ubigeo_inei` VALUES (1592, '16', '01', '07', 'NAPO');
INSERT INTO `ubigeo_inei` VALUES (1593, '16', '01', '08', 'PUNCHANA');
INSERT INTO `ubigeo_inei` VALUES (1594, '16', '01', '09', 'PUTUMAYO');
INSERT INTO `ubigeo_inei` VALUES (1595, '16', '01', '10', 'TORRES CAUSANA');
INSERT INTO `ubigeo_inei` VALUES (1596, '16', '01', '12', 'BELÉN');
INSERT INTO `ubigeo_inei` VALUES (1597, '16', '01', '13', 'SAN JUAN BAUTISTA');
INSERT INTO `ubigeo_inei` VALUES (1598, '16', '01', '14', 'TENIENTE MANUEL CLAVERO');
INSERT INTO `ubigeo_inei` VALUES (1599, '16', '02', '00', 'ALTO AMAZONAS');
INSERT INTO `ubigeo_inei` VALUES (1600, '16', '02', '01', 'YURIMAGUAS');
INSERT INTO `ubigeo_inei` VALUES (1601, '16', '02', '02', 'BALSAPUERTO');
INSERT INTO `ubigeo_inei` VALUES (1602, '16', '02', '05', 'JEBEROS');
INSERT INTO `ubigeo_inei` VALUES (1603, '16', '02', '06', 'LAGUNAS');
INSERT INTO `ubigeo_inei` VALUES (1604, '16', '02', '10', 'SANTA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (1605, '16', '02', '11', 'TENIENTE CESAR LOPEZ ROJAS');
INSERT INTO `ubigeo_inei` VALUES (1606, '16', '03', '00', 'LORETO');
INSERT INTO `ubigeo_inei` VALUES (1607, '16', '03', '01', 'NAUTA');
INSERT INTO `ubigeo_inei` VALUES (1608, '16', '03', '02', 'PARINARI');
INSERT INTO `ubigeo_inei` VALUES (1609, '16', '03', '03', 'TIGRE');
INSERT INTO `ubigeo_inei` VALUES (1610, '16', '03', '04', 'TROMPETEROS');
INSERT INTO `ubigeo_inei` VALUES (1611, '16', '03', '05', 'URARINAS');
INSERT INTO `ubigeo_inei` VALUES (1612, '16', '04', '00', 'MARISCAL RAMON CASTILLA');
INSERT INTO `ubigeo_inei` VALUES (1613, '16', '04', '01', 'RAMON CASTILLA');
INSERT INTO `ubigeo_inei` VALUES (1614, '16', '04', '02', 'PEBAS');
INSERT INTO `ubigeo_inei` VALUES (1615, '16', '04', '03', 'YAVARI');
INSERT INTO `ubigeo_inei` VALUES (1616, '16', '04', '04', 'SAN PABLO');
INSERT INTO `ubigeo_inei` VALUES (1617, '16', '05', '00', 'REQUENA');
INSERT INTO `ubigeo_inei` VALUES (1618, '16', '05', '01', 'REQUENA');
INSERT INTO `ubigeo_inei` VALUES (1619, '16', '05', '02', 'ALTO TAPICHE');
INSERT INTO `ubigeo_inei` VALUES (1620, '16', '05', '03', 'CAPELO');
INSERT INTO `ubigeo_inei` VALUES (1621, '16', '05', '04', 'EMILIO SAN MARTIN');
INSERT INTO `ubigeo_inei` VALUES (1622, '16', '05', '05', 'MAQUIA');
INSERT INTO `ubigeo_inei` VALUES (1623, '16', '05', '06', 'PUINAHUA');
INSERT INTO `ubigeo_inei` VALUES (1624, '16', '05', '07', 'SAQUENA');
INSERT INTO `ubigeo_inei` VALUES (1625, '16', '05', '08', 'SOPLIN');
INSERT INTO `ubigeo_inei` VALUES (1626, '16', '05', '09', 'TAPICHE');
INSERT INTO `ubigeo_inei` VALUES (1627, '16', '05', '10', 'JENARO HERRERA');
INSERT INTO `ubigeo_inei` VALUES (1628, '16', '05', '11', 'YAQUERANA');
INSERT INTO `ubigeo_inei` VALUES (1629, '16', '06', '00', 'UCAYALI');
INSERT INTO `ubigeo_inei` VALUES (1630, '16', '06', '01', 'CONTAMANA');
INSERT INTO `ubigeo_inei` VALUES (1631, '16', '06', '02', 'INAHUAYA');
INSERT INTO `ubigeo_inei` VALUES (1632, '16', '06', '03', 'PADRE MARQUEZ');
INSERT INTO `ubigeo_inei` VALUES (1633, '16', '06', '04', 'PAMPA HERMOSA');
INSERT INTO `ubigeo_inei` VALUES (1634, '16', '06', '05', 'SARAYACU');
INSERT INTO `ubigeo_inei` VALUES (1635, '16', '06', '06', 'VARGAS GUERRA');
INSERT INTO `ubigeo_inei` VALUES (1636, '16', '07', '00', 'DATEM DEL MARAÑÓN');
INSERT INTO `ubigeo_inei` VALUES (1637, '16', '07', '01', 'BARRANCA');
INSERT INTO `ubigeo_inei` VALUES (1638, '16', '07', '02', 'CAHUAPANAS');
INSERT INTO `ubigeo_inei` VALUES (1639, '16', '07', '03', 'MANSERICHE');
INSERT INTO `ubigeo_inei` VALUES (1640, '16', '07', '04', 'MORONA');
INSERT INTO `ubigeo_inei` VALUES (1641, '16', '07', '05', 'PASTAZA');
INSERT INTO `ubigeo_inei` VALUES (1642, '16', '07', '06', 'ANDOAS');
INSERT INTO `ubigeo_inei` VALUES (1643, '16', '08', '00', 'PUTUMAYO');
INSERT INTO `ubigeo_inei` VALUES (1644, '16', '08', '01', 'PUTUMAYO');
INSERT INTO `ubigeo_inei` VALUES (1645, '16', '08', '02', 'ROSA PANDURO');
INSERT INTO `ubigeo_inei` VALUES (1646, '16', '08', '03', 'TENIENTE MANUEL CLAVERO');
INSERT INTO `ubigeo_inei` VALUES (1647, '16', '08', '04', 'YAGUAS');
INSERT INTO `ubigeo_inei` VALUES (1648, '17', '00', '00', 'MADRE DE DIOS');
INSERT INTO `ubigeo_inei` VALUES (1649, '17', '01', '00', 'TAMBOPATA');
INSERT INTO `ubigeo_inei` VALUES (1650, '17', '01', '01', 'TAMBOPATA');
INSERT INTO `ubigeo_inei` VALUES (1651, '17', '01', '02', 'INAMBARI');
INSERT INTO `ubigeo_inei` VALUES (1652, '17', '01', '03', 'LAS PIEDRAS');
INSERT INTO `ubigeo_inei` VALUES (1653, '17', '01', '04', 'LABERINTO');
INSERT INTO `ubigeo_inei` VALUES (1654, '17', '02', '00', 'MANU');
INSERT INTO `ubigeo_inei` VALUES (1655, '17', '02', '01', 'MANU');
INSERT INTO `ubigeo_inei` VALUES (1656, '17', '02', '02', 'FITZCARRALD');
INSERT INTO `ubigeo_inei` VALUES (1657, '17', '02', '03', 'MADRE DE DIOS');
INSERT INTO `ubigeo_inei` VALUES (1658, '17', '02', '04', 'HUEPETUHE');
INSERT INTO `ubigeo_inei` VALUES (1659, '17', '03', '00', 'TAHUAMANU');
INSERT INTO `ubigeo_inei` VALUES (1660, '17', '03', '01', 'IÑAPARI');
INSERT INTO `ubigeo_inei` VALUES (1661, '17', '03', '02', 'IBERIA');
INSERT INTO `ubigeo_inei` VALUES (1662, '17', '03', '03', 'TAHUAMANU');
INSERT INTO `ubigeo_inei` VALUES (1663, '18', '00', '00', 'MOQUEGUA');
INSERT INTO `ubigeo_inei` VALUES (1664, '18', '01', '00', 'MARISCAL NIETO');
INSERT INTO `ubigeo_inei` VALUES (1665, '18', '01', '01', 'MOQUEGUA');
INSERT INTO `ubigeo_inei` VALUES (1666, '18', '01', '02', 'CARUMAS');
INSERT INTO `ubigeo_inei` VALUES (1667, '18', '01', '03', 'CUCHUMBAYA');
INSERT INTO `ubigeo_inei` VALUES (1668, '18', '01', '04', 'SAMEGUA');
INSERT INTO `ubigeo_inei` VALUES (1669, '18', '01', '05', 'SAN CRISTOBAL');
INSERT INTO `ubigeo_inei` VALUES (1670, '18', '01', '06', 'TORATA');
INSERT INTO `ubigeo_inei` VALUES (1671, '18', '02', '00', 'GENERAL SANCHEZ CERRO');
INSERT INTO `ubigeo_inei` VALUES (1672, '18', '02', '01', 'OMATE');
INSERT INTO `ubigeo_inei` VALUES (1673, '18', '02', '02', 'CHOJATA');
INSERT INTO `ubigeo_inei` VALUES (1674, '18', '02', '03', 'COALAQUE');
INSERT INTO `ubigeo_inei` VALUES (1675, '18', '02', '04', 'ICHUÑA');
INSERT INTO `ubigeo_inei` VALUES (1676, '18', '02', '05', 'LA CAPILLA');
INSERT INTO `ubigeo_inei` VALUES (1677, '18', '02', '06', 'LLOQUE');
INSERT INTO `ubigeo_inei` VALUES (1678, '18', '02', '07', 'MATALAQUE');
INSERT INTO `ubigeo_inei` VALUES (1679, '18', '02', '08', 'PUQUINA');
INSERT INTO `ubigeo_inei` VALUES (1680, '18', '02', '09', 'QUINISTAQUILLAS');
INSERT INTO `ubigeo_inei` VALUES (1681, '18', '02', '10', 'UBINAS');
INSERT INTO `ubigeo_inei` VALUES (1682, '18', '02', '11', 'YUNGA');
INSERT INTO `ubigeo_inei` VALUES (1683, '18', '03', '00', 'ILO');
INSERT INTO `ubigeo_inei` VALUES (1684, '18', '03', '01', 'ILO');
INSERT INTO `ubigeo_inei` VALUES (1685, '18', '03', '02', 'EL ALGARROBAL');
INSERT INTO `ubigeo_inei` VALUES (1686, '18', '03', '03', 'PACOCHA');
INSERT INTO `ubigeo_inei` VALUES (1687, '19', '00', '00', 'PASCO');
INSERT INTO `ubigeo_inei` VALUES (1688, '19', '01', '00', 'PASCO');
INSERT INTO `ubigeo_inei` VALUES (1689, '19', '01', '01', 'CHAUPIMARCA');
INSERT INTO `ubigeo_inei` VALUES (1690, '19', '01', '02', 'HUACHON');
INSERT INTO `ubigeo_inei` VALUES (1691, '19', '01', '03', 'HUARIACA');
INSERT INTO `ubigeo_inei` VALUES (1692, '19', '01', '04', 'HUAYLLAY');
INSERT INTO `ubigeo_inei` VALUES (1693, '19', '01', '05', 'NINACACA');
INSERT INTO `ubigeo_inei` VALUES (1694, '19', '01', '06', 'PALLANCHACRA');
INSERT INTO `ubigeo_inei` VALUES (1695, '19', '01', '07', 'PAUCARTAMBO');
INSERT INTO `ubigeo_inei` VALUES (1696, '19', '01', '08', 'SAN FCO. DE ASÍS DE YARUSYACÁN');
INSERT INTO `ubigeo_inei` VALUES (1697, '19', '01', '09', 'SIMON BOLIVAR');
INSERT INTO `ubigeo_inei` VALUES (1698, '19', '01', '10', 'TICLACAYAN');
INSERT INTO `ubigeo_inei` VALUES (1699, '19', '01', '11', 'TINYAHUARCO');
INSERT INTO `ubigeo_inei` VALUES (1700, '19', '01', '12', 'VICCO');
INSERT INTO `ubigeo_inei` VALUES (1701, '19', '01', '13', 'YANACANCHA');
INSERT INTO `ubigeo_inei` VALUES (1702, '19', '02', '00', 'DANIEL ALCIDES CARRION');
INSERT INTO `ubigeo_inei` VALUES (1703, '19', '02', '01', 'YANAHUANCA');
INSERT INTO `ubigeo_inei` VALUES (1704, '19', '02', '02', 'CHACAYAN');
INSERT INTO `ubigeo_inei` VALUES (1705, '19', '02', '03', 'GOYLLARISQUIZGA');
INSERT INTO `ubigeo_inei` VALUES (1706, '19', '02', '04', 'PAUCAR');
INSERT INTO `ubigeo_inei` VALUES (1707, '19', '02', '05', 'SAN PEDRO DE PILLAO');
INSERT INTO `ubigeo_inei` VALUES (1708, '19', '02', '06', 'SANTA ANA DE TUSI');
INSERT INTO `ubigeo_inei` VALUES (1709, '19', '02', '07', 'TAPUC');
INSERT INTO `ubigeo_inei` VALUES (1710, '19', '02', '08', 'VILCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1711, '19', '03', '00', 'OXAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1712, '19', '03', '01', 'OXAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1713, '19', '03', '02', 'CHONTABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1714, '19', '03', '03', 'HUANCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1715, '19', '03', '04', 'PALCAZU');
INSERT INTO `ubigeo_inei` VALUES (1716, '19', '03', '05', 'POZUZO');
INSERT INTO `ubigeo_inei` VALUES (1717, '19', '03', '06', 'PUERTO BERMUDEZ');
INSERT INTO `ubigeo_inei` VALUES (1718, '19', '03', '07', 'VILLA RICA');
INSERT INTO `ubigeo_inei` VALUES (1719, '19', '03', '08', 'CONSTITUCION');
INSERT INTO `ubigeo_inei` VALUES (1720, '20', '00', '00', 'PIURA');
INSERT INTO `ubigeo_inei` VALUES (1721, '20', '01', '00', 'PIURA');
INSERT INTO `ubigeo_inei` VALUES (1722, '20', '01', '01', 'PIURA');
INSERT INTO `ubigeo_inei` VALUES (1723, '20', '01', '04', 'CASTILLA');
INSERT INTO `ubigeo_inei` VALUES (1724, '20', '01', '05', 'CATACAOS');
INSERT INTO `ubigeo_inei` VALUES (1725, '20', '01', '07', 'CURA MORI');
INSERT INTO `ubigeo_inei` VALUES (1726, '20', '01', '08', 'EL TALLAN');
INSERT INTO `ubigeo_inei` VALUES (1727, '20', '01', '09', 'LA ARENA');
INSERT INTO `ubigeo_inei` VALUES (1728, '20', '01', '10', 'LA UNION');
INSERT INTO `ubigeo_inei` VALUES (1729, '20', '01', '11', 'LAS LOMAS');
INSERT INTO `ubigeo_inei` VALUES (1730, '20', '01', '14', 'TAMBO GRANDE');
INSERT INTO `ubigeo_inei` VALUES (1731, '20', '01', '15', 'VEINTISÉIS DE OCTUBRE');
INSERT INTO `ubigeo_inei` VALUES (1732, '20', '02', '00', 'AYABACA');
INSERT INTO `ubigeo_inei` VALUES (1733, '20', '02', '01', 'AYABACA');
INSERT INTO `ubigeo_inei` VALUES (1734, '20', '02', '02', 'FRIAS');
INSERT INTO `ubigeo_inei` VALUES (1735, '20', '02', '03', 'JILILI');
INSERT INTO `ubigeo_inei` VALUES (1736, '20', '02', '04', 'LAGUNAS');
INSERT INTO `ubigeo_inei` VALUES (1737, '20', '02', '05', 'MONTERO');
INSERT INTO `ubigeo_inei` VALUES (1738, '20', '02', '06', 'PACAIPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1739, '20', '02', '07', 'PAIMAS');
INSERT INTO `ubigeo_inei` VALUES (1740, '20', '02', '08', 'SAPILLICA');
INSERT INTO `ubigeo_inei` VALUES (1741, '20', '02', '09', 'SICCHEZ');
INSERT INTO `ubigeo_inei` VALUES (1742, '20', '02', '10', 'SUYO');
INSERT INTO `ubigeo_inei` VALUES (1743, '20', '03', '00', 'HUANCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1744, '20', '03', '01', 'HUANCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1745, '20', '03', '02', 'CANCHAQUE');
INSERT INTO `ubigeo_inei` VALUES (1746, '20', '03', '03', 'EL CARMEN DE LA FRONTERA');
INSERT INTO `ubigeo_inei` VALUES (1747, '20', '03', '04', 'HUARMACA');
INSERT INTO `ubigeo_inei` VALUES (1748, '20', '03', '05', 'LALAQUIZ');
INSERT INTO `ubigeo_inei` VALUES (1749, '20', '03', '06', 'SAN MIGUEL DE EL FAIQUE');
INSERT INTO `ubigeo_inei` VALUES (1750, '20', '03', '07', 'SONDOR');
INSERT INTO `ubigeo_inei` VALUES (1751, '20', '03', '08', 'SONDORILLO');
INSERT INTO `ubigeo_inei` VALUES (1752, '20', '04', '00', 'MORROPON');
INSERT INTO `ubigeo_inei` VALUES (1753, '20', '04', '01', 'CHULUCANAS');
INSERT INTO `ubigeo_inei` VALUES (1754, '20', '04', '02', 'BUENOS AIRES');
INSERT INTO `ubigeo_inei` VALUES (1755, '20', '04', '03', 'CHALACO');
INSERT INTO `ubigeo_inei` VALUES (1756, '20', '04', '04', 'LA MATANZA');
INSERT INTO `ubigeo_inei` VALUES (1757, '20', '04', '05', 'MORROPON');
INSERT INTO `ubigeo_inei` VALUES (1758, '20', '04', '06', 'SALITRAL');
INSERT INTO `ubigeo_inei` VALUES (1759, '20', '04', '07', 'SAN JUAN DE BIGOTE');
INSERT INTO `ubigeo_inei` VALUES (1760, '20', '04', '08', 'SANTA CATALINA DE MOSSA');
INSERT INTO `ubigeo_inei` VALUES (1761, '20', '04', '09', 'SANTO DOMINGO');
INSERT INTO `ubigeo_inei` VALUES (1762, '20', '04', '10', 'YAMANGO');
INSERT INTO `ubigeo_inei` VALUES (1763, '20', '05', '00', 'PAITA');
INSERT INTO `ubigeo_inei` VALUES (1764, '20', '05', '01', 'PAITA');
INSERT INTO `ubigeo_inei` VALUES (1765, '20', '05', '02', 'AMOTAPE');
INSERT INTO `ubigeo_inei` VALUES (1766, '20', '05', '03', 'ARENAL');
INSERT INTO `ubigeo_inei` VALUES (1767, '20', '05', '04', 'COLAN');
INSERT INTO `ubigeo_inei` VALUES (1768, '20', '05', '05', 'LA HUACA');
INSERT INTO `ubigeo_inei` VALUES (1769, '20', '05', '06', 'TAMARINDO');
INSERT INTO `ubigeo_inei` VALUES (1770, '20', '05', '07', 'VICHAYAL');
INSERT INTO `ubigeo_inei` VALUES (1771, '20', '06', '00', 'SULLANA');
INSERT INTO `ubigeo_inei` VALUES (1772, '20', '06', '01', 'SULLANA');
INSERT INTO `ubigeo_inei` VALUES (1773, '20', '06', '02', 'BELLAVISTA');
INSERT INTO `ubigeo_inei` VALUES (1774, '20', '06', '03', 'IGNACIO ESCUDERO');
INSERT INTO `ubigeo_inei` VALUES (1775, '20', '06', '04', 'LANCONES');
INSERT INTO `ubigeo_inei` VALUES (1776, '20', '06', '05', 'MARCAVELICA');
INSERT INTO `ubigeo_inei` VALUES (1777, '20', '06', '06', 'MIGUEL CHECA');
INSERT INTO `ubigeo_inei` VALUES (1778, '20', '06', '07', 'QUERECOTILLO');
INSERT INTO `ubigeo_inei` VALUES (1779, '20', '06', '08', 'SALITRAL');
INSERT INTO `ubigeo_inei` VALUES (1780, '20', '07', '00', 'TALARA');
INSERT INTO `ubigeo_inei` VALUES (1781, '20', '07', '01', 'PARIÑAS');
INSERT INTO `ubigeo_inei` VALUES (1782, '20', '07', '02', 'EL ALTO');
INSERT INTO `ubigeo_inei` VALUES (1783, '20', '07', '03', 'LA BREA');
INSERT INTO `ubigeo_inei` VALUES (1784, '20', '07', '04', 'LOBITOS');
INSERT INTO `ubigeo_inei` VALUES (1785, '20', '07', '05', 'LOS ORGANOS');
INSERT INTO `ubigeo_inei` VALUES (1786, '20', '07', '06', 'MANCORA');
INSERT INTO `ubigeo_inei` VALUES (1787, '20', '08', '00', 'SECHURA');
INSERT INTO `ubigeo_inei` VALUES (1788, '20', '08', '01', 'SECHURA');
INSERT INTO `ubigeo_inei` VALUES (1789, '20', '08', '02', 'BELLAVISTA DE LA UNION');
INSERT INTO `ubigeo_inei` VALUES (1790, '20', '08', '03', 'BERNAL');
INSERT INTO `ubigeo_inei` VALUES (1791, '20', '08', '04', 'CRISTO NOS VALGA');
INSERT INTO `ubigeo_inei` VALUES (1792, '20', '08', '05', 'VICE');
INSERT INTO `ubigeo_inei` VALUES (1793, '20', '08', '06', 'RINCONADA LLICUAR');
INSERT INTO `ubigeo_inei` VALUES (1794, '21', '00', '00', 'PUNO');
INSERT INTO `ubigeo_inei` VALUES (1795, '21', '01', '00', 'PUNO');
INSERT INTO `ubigeo_inei` VALUES (1796, '21', '01', '01', 'PUNO');
INSERT INTO `ubigeo_inei` VALUES (1797, '21', '01', '02', 'ACORA');
INSERT INTO `ubigeo_inei` VALUES (1798, '21', '01', '03', 'AMANTANI');
INSERT INTO `ubigeo_inei` VALUES (1799, '21', '01', '04', 'ATUNCOLLA');
INSERT INTO `ubigeo_inei` VALUES (1800, '21', '01', '05', 'CAPACHICA');
INSERT INTO `ubigeo_inei` VALUES (1801, '21', '01', '06', 'CHUCUITO');
INSERT INTO `ubigeo_inei` VALUES (1802, '21', '01', '07', 'COATA');
INSERT INTO `ubigeo_inei` VALUES (1803, '21', '01', '08', 'HUATA');
INSERT INTO `ubigeo_inei` VALUES (1804, '21', '01', '09', 'MAÑAZO');
INSERT INTO `ubigeo_inei` VALUES (1805, '21', '01', '10', 'PAUCARCOLLA');
INSERT INTO `ubigeo_inei` VALUES (1806, '21', '01', '11', 'PICHACANI');
INSERT INTO `ubigeo_inei` VALUES (1807, '21', '01', '12', 'PLATERIA');
INSERT INTO `ubigeo_inei` VALUES (1808, '21', '01', '13', 'SAN ANTONIO');
INSERT INTO `ubigeo_inei` VALUES (1809, '21', '01', '14', 'TIQUILLACA');
INSERT INTO `ubigeo_inei` VALUES (1810, '21', '01', '15', 'VILQUE');
INSERT INTO `ubigeo_inei` VALUES (1811, '21', '02', '00', 'AZANGARO');
INSERT INTO `ubigeo_inei` VALUES (1812, '21', '02', '01', 'AZANGARO');
INSERT INTO `ubigeo_inei` VALUES (1813, '21', '02', '02', 'ACHAYA');
INSERT INTO `ubigeo_inei` VALUES (1814, '21', '02', '03', 'ARAPA');
INSERT INTO `ubigeo_inei` VALUES (1815, '21', '02', '04', 'ASILLO');
INSERT INTO `ubigeo_inei` VALUES (1816, '21', '02', '05', 'CAMINACA');
INSERT INTO `ubigeo_inei` VALUES (1817, '21', '02', '06', 'CHUPA');
INSERT INTO `ubigeo_inei` VALUES (1818, '21', '02', '07', 'JOSE DOMINGO CHOQUEHUANCA');
INSERT INTO `ubigeo_inei` VALUES (1819, '21', '02', '08', 'MUÑANI');
INSERT INTO `ubigeo_inei` VALUES (1820, '21', '02', '09', 'POTONI');
INSERT INTO `ubigeo_inei` VALUES (1821, '21', '02', '10', 'SAMAN');
INSERT INTO `ubigeo_inei` VALUES (1822, '21', '02', '11', 'SAN ANTON');
INSERT INTO `ubigeo_inei` VALUES (1823, '21', '02', '12', 'SAN JOSE');
INSERT INTO `ubigeo_inei` VALUES (1824, '21', '02', '13', 'SAN JUAN DE SALINAS');
INSERT INTO `ubigeo_inei` VALUES (1825, '21', '02', '14', 'SANTIAGO DE PUPUJA');
INSERT INTO `ubigeo_inei` VALUES (1826, '21', '02', '15', 'TIRAPATA');
INSERT INTO `ubigeo_inei` VALUES (1827, '21', '03', '00', 'CARABAYA');
INSERT INTO `ubigeo_inei` VALUES (1828, '21', '03', '01', 'MACUSANI');
INSERT INTO `ubigeo_inei` VALUES (1829, '21', '03', '02', 'AJOYANI');
INSERT INTO `ubigeo_inei` VALUES (1830, '21', '03', '03', 'AYAPATA');
INSERT INTO `ubigeo_inei` VALUES (1831, '21', '03', '04', 'COASA');
INSERT INTO `ubigeo_inei` VALUES (1832, '21', '03', '05', 'CORANI');
INSERT INTO `ubigeo_inei` VALUES (1833, '21', '03', '06', 'CRUCERO');
INSERT INTO `ubigeo_inei` VALUES (1834, '21', '03', '07', 'ITUATA');
INSERT INTO `ubigeo_inei` VALUES (1835, '21', '03', '08', 'OLLACHEA');
INSERT INTO `ubigeo_inei` VALUES (1836, '21', '03', '09', 'SAN GABAN');
INSERT INTO `ubigeo_inei` VALUES (1837, '21', '03', '10', 'USICAYOS');
INSERT INTO `ubigeo_inei` VALUES (1838, '21', '04', '00', 'CHUCUITO');
INSERT INTO `ubigeo_inei` VALUES (1839, '21', '04', '01', 'JULI');
INSERT INTO `ubigeo_inei` VALUES (1840, '21', '04', '02', 'DESAGUADERO');
INSERT INTO `ubigeo_inei` VALUES (1841, '21', '04', '03', 'HUACULLANI');
INSERT INTO `ubigeo_inei` VALUES (1842, '21', '04', '04', 'KELLUYO');
INSERT INTO `ubigeo_inei` VALUES (1843, '21', '04', '05', 'PISACOMA');
INSERT INTO `ubigeo_inei` VALUES (1844, '21', '04', '06', 'POMATA');
INSERT INTO `ubigeo_inei` VALUES (1845, '21', '04', '07', 'ZEPITA');
INSERT INTO `ubigeo_inei` VALUES (1846, '21', '05', '00', 'EL COLLAO');
INSERT INTO `ubigeo_inei` VALUES (1847, '21', '05', '01', 'ILAVE');
INSERT INTO `ubigeo_inei` VALUES (1848, '21', '05', '02', 'CAPASO');
INSERT INTO `ubigeo_inei` VALUES (1849, '21', '05', '03', 'PILCUYO');
INSERT INTO `ubigeo_inei` VALUES (1850, '21', '05', '04', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (1851, '21', '05', '05', 'CONDURIRI');
INSERT INTO `ubigeo_inei` VALUES (1852, '21', '06', '00', 'HUANCANE');
INSERT INTO `ubigeo_inei` VALUES (1853, '21', '06', '01', 'HUANCANE');
INSERT INTO `ubigeo_inei` VALUES (1854, '21', '06', '02', 'COJATA');
INSERT INTO `ubigeo_inei` VALUES (1855, '21', '06', '03', 'HUATASANI');
INSERT INTO `ubigeo_inei` VALUES (1856, '21', '06', '04', 'INCHUPALLA');
INSERT INTO `ubigeo_inei` VALUES (1857, '21', '06', '05', 'PUSI');
INSERT INTO `ubigeo_inei` VALUES (1858, '21', '06', '06', 'ROSASPATA');
INSERT INTO `ubigeo_inei` VALUES (1859, '21', '06', '07', 'TARACO');
INSERT INTO `ubigeo_inei` VALUES (1860, '21', '06', '08', 'VILQUE CHICO');
INSERT INTO `ubigeo_inei` VALUES (1861, '21', '07', '00', 'LAMPA');
INSERT INTO `ubigeo_inei` VALUES (1862, '21', '07', '01', 'LAMPA');
INSERT INTO `ubigeo_inei` VALUES (1863, '21', '07', '02', 'CABANILLA');
INSERT INTO `ubigeo_inei` VALUES (1864, '21', '07', '03', 'CALAPUJA');
INSERT INTO `ubigeo_inei` VALUES (1865, '21', '07', '04', 'NICASIO');
INSERT INTO `ubigeo_inei` VALUES (1866, '21', '07', '05', 'OCUVIRI');
INSERT INTO `ubigeo_inei` VALUES (1867, '21', '07', '06', 'PALCA');
INSERT INTO `ubigeo_inei` VALUES (1868, '21', '07', '07', 'PARATIA');
INSERT INTO `ubigeo_inei` VALUES (1869, '21', '07', '08', 'PUCARA');
INSERT INTO `ubigeo_inei` VALUES (1870, '21', '07', '09', 'SANTA LUCIA');
INSERT INTO `ubigeo_inei` VALUES (1871, '21', '07', '10', 'VILAVILA');
INSERT INTO `ubigeo_inei` VALUES (1872, '21', '08', '00', 'MELGAR');
INSERT INTO `ubigeo_inei` VALUES (1873, '21', '08', '01', 'AYAVIRI');
INSERT INTO `ubigeo_inei` VALUES (1874, '21', '08', '02', 'ANTAUTA');
INSERT INTO `ubigeo_inei` VALUES (1875, '21', '08', '03', 'CUPI');
INSERT INTO `ubigeo_inei` VALUES (1876, '21', '08', '04', 'LLALLI');
INSERT INTO `ubigeo_inei` VALUES (1877, '21', '08', '05', 'MACARI');
INSERT INTO `ubigeo_inei` VALUES (1878, '21', '08', '06', 'NUÑOA');
INSERT INTO `ubigeo_inei` VALUES (1879, '21', '08', '07', 'ORURILLO');
INSERT INTO `ubigeo_inei` VALUES (1880, '21', '08', '08', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (1881, '21', '08', '09', 'UMACHIRI');
INSERT INTO `ubigeo_inei` VALUES (1882, '21', '09', '00', 'MOHO');
INSERT INTO `ubigeo_inei` VALUES (1883, '21', '09', '01', 'MOHO');
INSERT INTO `ubigeo_inei` VALUES (1884, '21', '09', '02', 'CONIMA');
INSERT INTO `ubigeo_inei` VALUES (1885, '21', '09', '03', 'HUAYRAPATA');
INSERT INTO `ubigeo_inei` VALUES (1886, '21', '09', '04', 'TILALI');
INSERT INTO `ubigeo_inei` VALUES (1887, '21', '10', '00', 'SAN ANTONIO DE PUTINA');
INSERT INTO `ubigeo_inei` VALUES (1888, '21', '10', '01', 'PUTINA');
INSERT INTO `ubigeo_inei` VALUES (1889, '21', '10', '02', 'ANANEA');
INSERT INTO `ubigeo_inei` VALUES (1890, '21', '10', '03', 'PEDRO VILCA APAZA');
INSERT INTO `ubigeo_inei` VALUES (1891, '21', '10', '04', 'QUILCAPUNCU');
INSERT INTO `ubigeo_inei` VALUES (1892, '21', '10', '05', 'SINA');
INSERT INTO `ubigeo_inei` VALUES (1893, '21', '11', '00', 'SAN ROMAN');
INSERT INTO `ubigeo_inei` VALUES (1894, '21', '11', '01', 'JULIACA');
INSERT INTO `ubigeo_inei` VALUES (1895, '21', '11', '02', 'CABANA');
INSERT INTO `ubigeo_inei` VALUES (1896, '21', '11', '03', 'CABANILLAS');
INSERT INTO `ubigeo_inei` VALUES (1897, '21', '11', '04', 'CARACOTO');
INSERT INTO `ubigeo_inei` VALUES (1898, '21', '12', '00', 'SANDIA');
INSERT INTO `ubigeo_inei` VALUES (1899, '21', '12', '01', 'SANDIA');
INSERT INTO `ubigeo_inei` VALUES (1900, '21', '12', '02', 'CUYOCUYO');
INSERT INTO `ubigeo_inei` VALUES (1901, '21', '12', '03', 'LIMBANI');
INSERT INTO `ubigeo_inei` VALUES (1902, '21', '12', '04', 'PATAMBUCO');
INSERT INTO `ubigeo_inei` VALUES (1903, '21', '12', '05', 'PHARA');
INSERT INTO `ubigeo_inei` VALUES (1904, '21', '12', '06', 'QUIACA');
INSERT INTO `ubigeo_inei` VALUES (1905, '21', '12', '07', 'SAN JUAN DEL ORO');
INSERT INTO `ubigeo_inei` VALUES (1906, '21', '12', '08', 'YANAHUAYA');
INSERT INTO `ubigeo_inei` VALUES (1907, '21', '12', '09', 'ALTO INAMBARI');
INSERT INTO `ubigeo_inei` VALUES (1908, '21', '12', '10', 'SAN PEDRO DE PUTINA PUNCO');
INSERT INTO `ubigeo_inei` VALUES (1909, '21', '13', '00', 'YUNGUYO');
INSERT INTO `ubigeo_inei` VALUES (1910, '21', '13', '01', 'YUNGUYO');
INSERT INTO `ubigeo_inei` VALUES (1911, '21', '13', '02', 'ANAPIA');
INSERT INTO `ubigeo_inei` VALUES (1912, '21', '13', '03', 'COPANI');
INSERT INTO `ubigeo_inei` VALUES (1913, '21', '13', '04', 'CUTURAPI');
INSERT INTO `ubigeo_inei` VALUES (1914, '21', '13', '05', 'OLLARAYA');
INSERT INTO `ubigeo_inei` VALUES (1915, '21', '13', '06', 'TINICACHI');
INSERT INTO `ubigeo_inei` VALUES (1916, '21', '13', '07', 'UNICACHI');
INSERT INTO `ubigeo_inei` VALUES (1917, '22', '00', '00', 'SAN MARTIN');
INSERT INTO `ubigeo_inei` VALUES (1918, '22', '01', '00', 'MOYOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1919, '22', '01', '01', 'MOYOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1920, '22', '01', '02', 'CALZADA');
INSERT INTO `ubigeo_inei` VALUES (1921, '22', '01', '03', 'HABANA');
INSERT INTO `ubigeo_inei` VALUES (1922, '22', '01', '04', 'JEPELACIO');
INSERT INTO `ubigeo_inei` VALUES (1923, '22', '01', '05', 'SORITOR');
INSERT INTO `ubigeo_inei` VALUES (1924, '22', '01', '06', 'YANTALO');
INSERT INTO `ubigeo_inei` VALUES (1925, '22', '02', '00', 'BELLAVISTA');
INSERT INTO `ubigeo_inei` VALUES (1926, '22', '02', '01', 'BELLAVISTA');
INSERT INTO `ubigeo_inei` VALUES (1927, '22', '02', '02', 'ALTO BIAVO');
INSERT INTO `ubigeo_inei` VALUES (1928, '22', '02', '03', 'BAJO BIAVO');
INSERT INTO `ubigeo_inei` VALUES (1929, '22', '02', '04', 'HUALLAGA');
INSERT INTO `ubigeo_inei` VALUES (1930, '22', '02', '05', 'SAN PABLO');
INSERT INTO `ubigeo_inei` VALUES (1931, '22', '02', '06', 'SAN RAFAEL');
INSERT INTO `ubigeo_inei` VALUES (1932, '22', '03', '00', 'EL DORADO');
INSERT INTO `ubigeo_inei` VALUES (1933, '22', '03', '01', 'SAN JOSE DE SISA');
INSERT INTO `ubigeo_inei` VALUES (1934, '22', '03', '02', 'AGUA BLANCA');
INSERT INTO `ubigeo_inei` VALUES (1935, '22', '03', '03', 'SAN MARTIN');
INSERT INTO `ubigeo_inei` VALUES (1936, '22', '03', '04', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (1937, '22', '03', '05', 'SHATOJA');
INSERT INTO `ubigeo_inei` VALUES (1938, '22', '04', '00', 'HUALLAGA');
INSERT INTO `ubigeo_inei` VALUES (1939, '22', '04', '01', 'SAPOSOA');
INSERT INTO `ubigeo_inei` VALUES (1940, '22', '04', '02', 'ALTO SAPOSOA');
INSERT INTO `ubigeo_inei` VALUES (1941, '22', '04', '03', 'EL ESLABON');
INSERT INTO `ubigeo_inei` VALUES (1942, '22', '04', '04', 'PISCOYACU');
INSERT INTO `ubigeo_inei` VALUES (1943, '22', '04', '05', 'SACANCHE');
INSERT INTO `ubigeo_inei` VALUES (1944, '22', '04', '06', 'TINGO DE SAPOSOA');
INSERT INTO `ubigeo_inei` VALUES (1945, '22', '05', '00', 'LAMAS');
INSERT INTO `ubigeo_inei` VALUES (1946, '22', '05', '01', 'LAMAS');
INSERT INTO `ubigeo_inei` VALUES (1947, '22', '05', '02', 'ALONSO DE ALVARADO');
INSERT INTO `ubigeo_inei` VALUES (1948, '22', '05', '03', 'BARRANQUITA');
INSERT INTO `ubigeo_inei` VALUES (1949, '22', '05', '04', 'CAYNARACHI');
INSERT INTO `ubigeo_inei` VALUES (1950, '22', '05', '05', 'CUÑUMBUQUI');
INSERT INTO `ubigeo_inei` VALUES (1951, '22', '05', '06', 'PINTO RECODO');
INSERT INTO `ubigeo_inei` VALUES (1952, '22', '05', '07', 'RUMISAPA');
INSERT INTO `ubigeo_inei` VALUES (1953, '22', '05', '08', 'SAN ROQUE DE CUMBAZA');
INSERT INTO `ubigeo_inei` VALUES (1954, '22', '05', '09', 'SHANAO');
INSERT INTO `ubigeo_inei` VALUES (1955, '22', '05', '10', 'TABALOSOS');
INSERT INTO `ubigeo_inei` VALUES (1956, '22', '05', '11', 'ZAPATERO');
INSERT INTO `ubigeo_inei` VALUES (1957, '22', '06', '00', 'MARISCAL CACERES');
INSERT INTO `ubigeo_inei` VALUES (1958, '22', '06', '01', 'JUANJUI');
INSERT INTO `ubigeo_inei` VALUES (1959, '22', '06', '02', 'CAMPANILLA');
INSERT INTO `ubigeo_inei` VALUES (1960, '22', '06', '03', 'HUICUNGO');
INSERT INTO `ubigeo_inei` VALUES (1961, '22', '06', '04', 'PACHIZA');
INSERT INTO `ubigeo_inei` VALUES (1962, '22', '06', '05', 'PAJARILLO');
INSERT INTO `ubigeo_inei` VALUES (1963, '22', '07', '00', 'PICOTA');
INSERT INTO `ubigeo_inei` VALUES (1964, '22', '07', '01', 'PICOTA');
INSERT INTO `ubigeo_inei` VALUES (1965, '22', '07', '02', 'BUENOS AIRES');
INSERT INTO `ubigeo_inei` VALUES (1966, '22', '07', '03', 'CASPISAPA');
INSERT INTO `ubigeo_inei` VALUES (1967, '22', '07', '04', 'PILLUANA');
INSERT INTO `ubigeo_inei` VALUES (1968, '22', '07', '05', 'PUCACACA');
INSERT INTO `ubigeo_inei` VALUES (1969, '22', '07', '06', 'SAN CRISTOBAL');
INSERT INTO `ubigeo_inei` VALUES (1970, '22', '07', '07', 'SAN HILARION');
INSERT INTO `ubigeo_inei` VALUES (1971, '22', '07', '08', 'SHAMBOYACU');
INSERT INTO `ubigeo_inei` VALUES (1972, '22', '07', '09', 'TINGO DE PONASA');
INSERT INTO `ubigeo_inei` VALUES (1973, '22', '07', '10', 'TRES UNIDOS');
INSERT INTO `ubigeo_inei` VALUES (1974, '22', '08', '00', 'RIOJA');
INSERT INTO `ubigeo_inei` VALUES (1975, '22', '08', '01', 'RIOJA');
INSERT INTO `ubigeo_inei` VALUES (1976, '22', '08', '02', 'AWAJUN');
INSERT INTO `ubigeo_inei` VALUES (1977, '22', '08', '03', 'ELIAS SOPLIN VARGAS');
INSERT INTO `ubigeo_inei` VALUES (1978, '22', '08', '04', 'NUEVA CAJAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1979, '22', '08', '05', 'PARDO MIGUEL');
INSERT INTO `ubigeo_inei` VALUES (1980, '22', '08', '06', 'POSIC');
INSERT INTO `ubigeo_inei` VALUES (1981, '22', '08', '07', 'SAN FERNANDO');
INSERT INTO `ubigeo_inei` VALUES (1982, '22', '08', '08', 'YORONGOS');
INSERT INTO `ubigeo_inei` VALUES (1983, '22', '08', '09', 'YURACYACU');
INSERT INTO `ubigeo_inei` VALUES (1984, '22', '09', '00', 'SAN MARTIN');
INSERT INTO `ubigeo_inei` VALUES (1985, '22', '09', '01', 'TARAPOTO');
INSERT INTO `ubigeo_inei` VALUES (1986, '22', '09', '02', 'ALBERTO LEVEAU');
INSERT INTO `ubigeo_inei` VALUES (1987, '22', '09', '03', 'CACATACHI');
INSERT INTO `ubigeo_inei` VALUES (1988, '22', '09', '04', 'CHAZUTA');
INSERT INTO `ubigeo_inei` VALUES (1989, '22', '09', '05', 'CHIPURANA');
INSERT INTO `ubigeo_inei` VALUES (1990, '22', '09', '06', 'EL PORVENIR');
INSERT INTO `ubigeo_inei` VALUES (1991, '22', '09', '07', 'HUIMBAYOC');
INSERT INTO `ubigeo_inei` VALUES (1992, '22', '09', '08', 'JUAN GUERRA');
INSERT INTO `ubigeo_inei` VALUES (1993, '22', '09', '09', 'LA BANDA DE SHILCAYO');
INSERT INTO `ubigeo_inei` VALUES (1994, '22', '09', '10', 'MORALES');
INSERT INTO `ubigeo_inei` VALUES (1995, '22', '09', '11', 'PAPAPLAYA');
INSERT INTO `ubigeo_inei` VALUES (1996, '22', '09', '12', 'SAN ANTONIO');
INSERT INTO `ubigeo_inei` VALUES (1997, '22', '09', '13', 'SAUCE');
INSERT INTO `ubigeo_inei` VALUES (1998, '22', '09', '14', 'SHAPAJA');
INSERT INTO `ubigeo_inei` VALUES (1999, '22', '10', '00', 'TOCACHE');
INSERT INTO `ubigeo_inei` VALUES (2000, '22', '10', '01', 'TOCACHE');
INSERT INTO `ubigeo_inei` VALUES (2001, '22', '10', '02', 'NUEVO PROGRESO');
INSERT INTO `ubigeo_inei` VALUES (2002, '22', '10', '03', 'POLVORA');
INSERT INTO `ubigeo_inei` VALUES (2003, '22', '10', '04', 'SHUNTE');
INSERT INTO `ubigeo_inei` VALUES (2004, '22', '10', '05', 'UCHIZA');
INSERT INTO `ubigeo_inei` VALUES (2005, '23', '00', '00', 'TACNA');
INSERT INTO `ubigeo_inei` VALUES (2006, '23', '01', '00', 'TACNA');
INSERT INTO `ubigeo_inei` VALUES (2007, '23', '01', '01', 'TACNA');
INSERT INTO `ubigeo_inei` VALUES (2008, '23', '01', '02', 'ALTO DE LA ALIANZA');
INSERT INTO `ubigeo_inei` VALUES (2009, '23', '01', '03', 'CALANA');
INSERT INTO `ubigeo_inei` VALUES (2010, '23', '01', '04', 'CIUDAD NUEVA');
INSERT INTO `ubigeo_inei` VALUES (2011, '23', '01', '05', 'INCLAN');
INSERT INTO `ubigeo_inei` VALUES (2012, '23', '01', '06', 'PACHIA');
INSERT INTO `ubigeo_inei` VALUES (2013, '23', '01', '07', 'PALCA');
INSERT INTO `ubigeo_inei` VALUES (2014, '23', '01', '08', 'POCOLLAY');
INSERT INTO `ubigeo_inei` VALUES (2015, '23', '01', '09', 'SAMA');
INSERT INTO `ubigeo_inei` VALUES (2016, '23', '01', '10', 'CORONEL GREGORIO ALBARRACÍN L');
INSERT INTO `ubigeo_inei` VALUES (2017, '23', '02', '00', 'CANDARAVE');
INSERT INTO `ubigeo_inei` VALUES (2018, '23', '02', '01', 'CANDARAVE');
INSERT INTO `ubigeo_inei` VALUES (2019, '23', '02', '02', 'CAIRANI');
INSERT INTO `ubigeo_inei` VALUES (2020, '23', '02', '03', 'CAMILACA');
INSERT INTO `ubigeo_inei` VALUES (2021, '23', '02', '04', 'CURIBAYA');
INSERT INTO `ubigeo_inei` VALUES (2022, '23', '02', '05', 'HUANUARA');
INSERT INTO `ubigeo_inei` VALUES (2023, '23', '02', '06', 'QUILAHUANI');
INSERT INTO `ubigeo_inei` VALUES (2024, '23', '03', '00', 'JORGE BASADRE');
INSERT INTO `ubigeo_inei` VALUES (2025, '23', '03', '01', 'LOCUMBA');
INSERT INTO `ubigeo_inei` VALUES (2026, '23', '03', '02', 'ILABAYA');
INSERT INTO `ubigeo_inei` VALUES (2027, '23', '03', '03', 'ITE');
INSERT INTO `ubigeo_inei` VALUES (2028, '23', '04', '00', 'TARATA');
INSERT INTO `ubigeo_inei` VALUES (2029, '23', '04', '01', 'TARATA');
INSERT INTO `ubigeo_inei` VALUES (2030, '23', '04', '02', 'CHUCATAMANI');
INSERT INTO `ubigeo_inei` VALUES (2031, '23', '04', '03', 'ESTIQUE');
INSERT INTO `ubigeo_inei` VALUES (2032, '23', '04', '04', 'ESTIQUE-PAMPA');
INSERT INTO `ubigeo_inei` VALUES (2033, '23', '04', '05', 'SITAJARA');
INSERT INTO `ubigeo_inei` VALUES (2034, '23', '04', '06', 'SUSAPAYA');
INSERT INTO `ubigeo_inei` VALUES (2035, '23', '04', '07', 'TARUCACHI');
INSERT INTO `ubigeo_inei` VALUES (2036, '23', '04', '08', 'TICACO');
INSERT INTO `ubigeo_inei` VALUES (2037, '24', '00', '00', 'TUMBES');
INSERT INTO `ubigeo_inei` VALUES (2038, '24', '01', '00', 'TUMBES');
INSERT INTO `ubigeo_inei` VALUES (2039, '24', '01', '01', 'TUMBES');
INSERT INTO `ubigeo_inei` VALUES (2040, '24', '01', '02', 'CORRALES');
INSERT INTO `ubigeo_inei` VALUES (2041, '24', '01', '03', 'LA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (2042, '24', '01', '04', 'PAMPAS DE HOSPITAL');
INSERT INTO `ubigeo_inei` VALUES (2043, '24', '01', '05', 'SAN JACINTO');
INSERT INTO `ubigeo_inei` VALUES (2044, '24', '01', '06', 'SAN JUAN DE LA VIRGEN');
INSERT INTO `ubigeo_inei` VALUES (2045, '24', '02', '00', 'CONTRALMIRANTE VILLAR');
INSERT INTO `ubigeo_inei` VALUES (2046, '24', '02', '01', 'ZORRITOS');
INSERT INTO `ubigeo_inei` VALUES (2047, '24', '02', '02', 'CASITAS');
INSERT INTO `ubigeo_inei` VALUES (2048, '24', '02', '03', 'CANOAS DE PUNTA SAL');
INSERT INTO `ubigeo_inei` VALUES (2049, '24', '03', '00', 'ZARUMILLA');
INSERT INTO `ubigeo_inei` VALUES (2050, '24', '03', '01', 'ZARUMILLA');
INSERT INTO `ubigeo_inei` VALUES (2051, '24', '03', '02', 'AGUAS VERDES');
INSERT INTO `ubigeo_inei` VALUES (2052, '24', '03', '03', 'MATAPALO');
INSERT INTO `ubigeo_inei` VALUES (2053, '24', '03', '04', 'PAPAYAL');
INSERT INTO `ubigeo_inei` VALUES (2054, '25', '00', '00', 'UCAYALI');
INSERT INTO `ubigeo_inei` VALUES (2055, '25', '01', '00', 'CORONEL PORTILLO');
INSERT INTO `ubigeo_inei` VALUES (2056, '25', '01', '01', 'CALLARIA');
INSERT INTO `ubigeo_inei` VALUES (2057, '25', '01', '02', 'CAMPOVERDE');
INSERT INTO `ubigeo_inei` VALUES (2058, '25', '01', '03', 'IPARIA');
INSERT INTO `ubigeo_inei` VALUES (2059, '25', '01', '04', 'MASISEA');
INSERT INTO `ubigeo_inei` VALUES (2060, '25', '01', '05', 'YARINACOCHA');
INSERT INTO `ubigeo_inei` VALUES (2061, '25', '01', '06', 'NUEVA REQUENA');
INSERT INTO `ubigeo_inei` VALUES (2062, '25', '01', '07', 'MANANTAY');
INSERT INTO `ubigeo_inei` VALUES (2063, '25', '02', '00', 'ATALAYA');
INSERT INTO `ubigeo_inei` VALUES (2064, '25', '02', '01', 'RAYMONDI');
INSERT INTO `ubigeo_inei` VALUES (2065, '25', '02', '02', 'SEPAHUA');
INSERT INTO `ubigeo_inei` VALUES (2066, '25', '02', '03', 'TAHUANIA');
INSERT INTO `ubigeo_inei` VALUES (2067, '25', '02', '04', 'YURUA');
INSERT INTO `ubigeo_inei` VALUES (2068, '25', '03', '00', 'PADRE ABAD');
INSERT INTO `ubigeo_inei` VALUES (2069, '25', '03', '01', 'PADRE ABAD');
INSERT INTO `ubigeo_inei` VALUES (2070, '25', '03', '02', 'IRAZOLA');
INSERT INTO `ubigeo_inei` VALUES (2071, '25', '03', '03', 'CURIMANA');
INSERT INTO `ubigeo_inei` VALUES (2072, '25', '04', '00', 'PURUS');
INSERT INTO `ubigeo_inei` VALUES (2073, '25', '04', '01', 'PURUS');
INSERT INTO `ubigeo_inei` VALUES (2074, '99', '00', '00', 'EXTRANJERO');
INSERT INTO `ubigeo_inei` VALUES (2075, '99', '99', '00', 'EXTRANJERO');
INSERT INTO `ubigeo_inei` VALUES (2076, '99', '99', '99', 'EXTRANJERO');

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

SET FOREIGN_KEY_CHECKS = 1;
