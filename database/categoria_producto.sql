/*
 Navicat Premium Dump SQL

 Source Server         : AREQIPAGO
 Source Server Type    : MySQL
 Source Server Version : 100527 (10.5.27-MariaDB)
 Source Host           : 195.26.251.142:3306
 Source Schema         : magusqao_arequipa

 Target Server Type    : MySQL
 Target Server Version : 100527 (10.5.27-MariaDB)
 File Encoding         : 65001

 Date: 07/11/2025 17:14:20
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for categoria_producto
-- ----------------------------
DROP TABLE IF EXISTS `categoria_producto`;
CREATE TABLE `categoria_producto`  (
  `idcategoria_producto` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`idcategoria_producto`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of categoria_producto
-- ----------------------------
INSERT INTO `categoria_producto` VALUES (5, 'Celular');
INSERT INTO `categoria_producto` VALUES (6, 'Chip (Linea corporativa)');
INSERT INTO `categoria_producto` VALUES (10, 'OTROS');
INSERT INTO `categoria_producto` VALUES (11, 'POLO');
INSERT INTO `categoria_producto` VALUES (12, 'CASQUETE');
INSERT INTO `categoria_producto` VALUES (13, 'CASACA');
INSERT INTO `categoria_producto` VALUES (14, 'LOGOS');
INSERT INTO `categoria_producto` VALUES (15, 'Vehículo');
INSERT INTO `categoria_producto` VALUES (16, 'Baterías');
INSERT INTO `categoria_producto` VALUES (17, 'AMBIENTADOR');
INSERT INTO `categoria_producto` VALUES (18, 'CAMISA');
INSERT INTO `categoria_producto` VALUES (19, 'GORRO');
INSERT INTO `categoria_producto` VALUES (20, 'CHIP');
INSERT INTO `categoria_producto` VALUES (21, 'LANYARD + PORTA FOTOCHECK');
INSERT INTO `categoria_producto` VALUES (22, 'MOTO LINEAL');
INSERT INTO `categoria_producto` VALUES (23, 'SOAT');
INSERT INTO `categoria_producto` VALUES (24, 'Seguro');
INSERT INTO `categoria_producto` VALUES (25, 'Llantas');
INSERT INTO `categoria_producto` VALUES (26, 'Aceites');

SET FOREIGN_KEY_CHECKS = 1;
