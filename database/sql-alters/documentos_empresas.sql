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

 Date: 01/02/2026 19:46:19
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for documentos_empresas
-- ----------------------------
DROP TABLE IF EXISTS `documentos_empresas`;
CREATE TABLE `documentos_empresas`  (
  `id_empresa` int NOT NULL,
  `id_tido` int NOT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `serie` varchar(4) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL DEFAULT NULL,
  `numero` int NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of documentos_empresas
-- ----------------------------
INSERT INTO `documentos_empresas` VALUES (12, 1, 1, 'B001', 108);
INSERT INTO `documentos_empresas` VALUES (12, 2, 1, 'F001', 14);
INSERT INTO `documentos_empresas` VALUES (12, 3, 1, 'F001', 1);
INSERT INTO `documentos_empresas` VALUES (12, 4, 1, 'F001', 1);
INSERT INTO `documentos_empresas` VALUES (12, 6, 1, 'NV01', 313);
INSERT INTO `documentos_empresas` VALUES (12, 11, 1, 'T001', 1);
INSERT INTO `documentos_empresas` VALUES (12, 1, 2, 'B002', 1);
INSERT INTO `documentos_empresas` VALUES (12, 2, 2, 'F002', 1);
INSERT INTO `documentos_empresas` VALUES (12, 3, 2, 'F002', 1);
INSERT INTO `documentos_empresas` VALUES (12, 4, 2, 'F002', 1);
INSERT INTO `documentos_empresas` VALUES (12, 6, 2, 'NV02', 1);
INSERT INTO `documentos_empresas` VALUES (12, 11, 2, 'T002', 1);

SET FOREIGN_KEY_CHECKS = 1;
