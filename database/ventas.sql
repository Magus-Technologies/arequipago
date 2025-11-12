/*
 Navicat Premium Dump SQL

 Source Server         : localhist
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : localhost:3306
 Source Schema         : magusqao_arequipa

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 27/10/2025 21:54:11
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ventas
-- ----------------------------
DROP TABLE IF EXISTS `ventas`;
CREATE TABLE `ventas`  (
  `id_venta` int NOT NULL AUTO_INCREMENT,
  `id_tido` int NOT NULL,
  `id_tipo_pago` int NULL DEFAULT NULL,
  `fecha_emision` date NULL DEFAULT NULL,
  `fecha_vencimiento` date NULL DEFAULT NULL,
  `dias_pagos` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `direccion` varchar(220) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `serie` varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `numero` int NULL DEFAULT NULL,
  `id_cliente` int NOT NULL,
  `total` double(10, 2) NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `enviado_sunat` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_empresa` int NOT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `apli_igv` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '1',
  `observacion` varchar(220) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `igv` double(10, 2) NULL DEFAULT 0.18,
  `medoto_pago_id` int NULL DEFAULT NULL,
  `pagado` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `is_segun_pago` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `medoto_pago2_id` int NULL DEFAULT NULL,
  `pagado2` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `moneda` int NULL DEFAULT 1,
  `cm_tc` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_coti` int NULL DEFAULT NULL,
  `id_vendedor` int NULL DEFAULT NULL,
  PRIMARY KEY (`id_venta`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 367 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ventas
-- ----------------------------
INSERT INTO `ventas` VALUES (1, 1, 1, '2025-03-11', '2025-03-11', '', 'AREQUIPA', 'B001', 622, 5, 5.00, '1', '0', 12, 1, '0', '', 0.18, 12, '5', NULL, NULL, NULL, 1, '', NULL, 81);
INSERT INTO `ventas` VALUES (2, 1, 1, '2025-03-11', '2025-03-11', '', 'AQP', 'B001', 623, 1, 5.00, '1', '0', 12, 1, '0', '', 0.18, 12, '5', NULL, NULL, NULL, 1, '', NULL, 81);
INSERT INTO `ventas` VALUES (3, 1, 1, '2025-03-14', '2025-03-14', '', 'AREQUIPA', 'B001', 624, 7, 40.00, '2', '0', 12, 1, '1', '', 0.18, 10, '40', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (4, 1, 1, '2025-03-17', '2025-03-17', '', 'AREQUIPA', 'B001', 625, 8, 20.00, '1', '0', 12, 1, '0', '', 0.18, 10, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (5, 1, 1, '2025-03-17', '2025-03-17', '', '-', 'B001', 626, 9, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20.00', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (6, 1, 1, '2025-03-17', '2025-03-17', '', '-', 'B001', 627, 10, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (7, 1, 1, '2025-03-17', '2025-03-17', '', 'AREQUIPA GO', 'B001', 628, 11, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (10, 1, 1, '2025-03-18', '2025-03-18', '', 'arequipa', 'B001', 631, 12, 40.00, '1', '0', 12, 1, '0', '', 0.18, 10, '40', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (12, 1, 1, '2025-03-20', '2025-03-20', '', 'AREQUIPA', 'B001', 633, 13, 5.00, '1', '0', 12, 1, '0', '', 0.18, 5, '5', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (13, 1, 1, '2025-03-21', '2025-03-21', '', '-', 'B001', 634, 14, 20.00, '1', '0', 12, 1, '1', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (14, 1, 1, '2025-03-21', '2025-03-21', '', 'AREQUIPA', 'B001', 635, 15, 20.00, '1', '0', 12, 1, '0', '', 0.18, 10, '20.00', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (15, 1, 1, '2025-03-24', '2025-03-24', '', '-', 'B001', 636, 16, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (16, 1, 1, '2025-03-27', '2025-03-27', '', 'AREQUIPA ', 'B001', 637, 17, 20.00, '1', '0', 12, 1, '1', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (17, 1, 1, '2025-03-28', '2025-03-28', '', 'AREQUIPA', 'B001', 638, 18, 40.00, '2', '0', 12, 1, '0', '', 0.18, 12, '50', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (18, 1, 1, '2025-03-28', '2025-03-28', '', 'AREQUIPA', 'B001', 639, 18, 40.00, '1', '0', 12, 1, '0', '', 0.18, 12, '40', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (19, 1, 1, '2025-03-31', '2025-03-31', '', 'AREQUIPA', 'B001', 640, 19, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (20, 1, 1, '2025-03-31', '2025-03-31', '', 'AREQUIPA ', 'B001', 641, 20, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (21, 1, 1, '2025-03-31', '2025-03-31', '', 'AREQUIPA', 'B001', 642, 21, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (23, 2, 1, '2025-04-01', '2025-04-01', '', 'AREQUIPA ', 'F001', 2359, 22, 20.00, '2', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (24, 1, 1, '2025-04-03', '2025-04-03', '', 'arequipa', 'B001', 644, 23, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 81);
INSERT INTO `ventas` VALUES (25, 6, 1, '2025-04-03', '2025-04-03', '', '-', 'NV01', 2945, 24, 20.00, '1', '0', 12, 1, '1', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (26, 6, 1, '2025-04-07', '2025-04-07', '', '-', 'NV01', 1, 11, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (27, 6, 1, '2025-04-07', '2025-04-07', '', '-', 'NV01', 2, 25, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (28, 1, 1, '2025-04-08', '2025-04-08', '', 'arequipa', 'B001', 1, 26, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (29, 6, 1, '2025-04-08', '2025-04-08', '', '-', 'NV01', 3, 27, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (30, 6, 1, '2025-04-08', '2025-04-08', '', '-', 'NV01', 4, 28, 40.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (34, 1, 1, '2025-04-09', '2025-04-09', '', '-', 'B001', 4, 30, 275.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 80);
INSERT INTO `ventas` VALUES (35, 6, 1, '2025-04-09', '2025-04-09', '', '-', 'NV01', 5, 31, 70.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 80);
INSERT INTO `ventas` VALUES (36, 6, 1, '2025-04-10', '2025-04-10', '', '-', 'NV01', 6, 32, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (37, 6, 1, '2025-04-10', '2025-04-10', '', '-', 'NV01', 7, 33, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (38, 6, 1, '2025-04-10', '2025-04-10', '', '-', 'NV01', 8, 34, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (39, 6, 3, '2025-04-10', '2025-04-10', '', 'arequipa', 'NV01', 9, 35, 40.00, '1', '0', 12, 1, '0', '', 0.18, 99, '', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (40, 6, 3, '2025-04-10', '2025-04-10', '', 'AREQUIPA', 'NV01', 10, 35, 20.00, '1', '0', 12, 1, '0', '', 0.18, 99, '', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (41, 1, 1, '2025-04-10', '2025-04-10', '', 'AREQUIPA', 'B001', 5, 36, 40.00, '1', '0', 12, 1, '0', '', 0.18, 10, '40', NULL, NULL, NULL, 1, '', NULL, 81);
INSERT INTO `ventas` VALUES (42, 6, 1, '2025-04-11', '2025-04-11', '', '-', 'NV01', 11, 37, 40.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (43, 1, 1, '2025-04-11', '2025-04-11', '', 'AREQUIPA', 'B001', 6, 38, 40.00, '1', '0', 12, 1, '0', '', 0.18, 5, '40', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (44, 1, 1, '2025-04-11', '2025-04-11', '', 'AREQUIPA', 'B001', 7, 39, 70.00, '1', '0', 12, 1, '0', '', 0.18, 12, '70', NULL, NULL, NULL, 1, '', NULL, 81);
INSERT INTO `ventas` VALUES (45, 1, 1, '2025-04-11', '2025-04-11', '', 'AREQUIPA', 'B001', 8, 40, 303.74, '1', '0', 12, 1, '0', '', 0.18, 12, '303.74', NULL, NULL, NULL, 1, '', NULL, 81);
INSERT INTO `ventas` VALUES (46, 1, 1, '2025-04-14', '2025-04-14', '', '-', 'B001', 9, 41, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (47, 6, 1, '2025-04-14', '2025-04-14', '', '-', 'NV01', 12, 42, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (48, 6, 1, '2025-04-14', '2025-04-14', '', '-', 'NV01', 13, 43, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (49, 6, 3, '2025-04-15', '2025-04-15', '', '-', 'NV01', 14, 44, 40.00, '1', '0', 12, 1, '0', 'INSCRIPSCION A FLOTA', 0.18, 99, '', NULL, NULL, NULL, 1, '', NULL, 80);
INSERT INTO `ventas` VALUES (50, 1, 1, '2025-04-15', '2025-04-15', '', '-', 'B001', 10, 44, 70.00, '1', '0', 12, 1, '0', '', 0.18, 5, '70', NULL, NULL, NULL, 1, '', NULL, 80);
INSERT INTO `ventas` VALUES (51, 6, 1, '2025-04-21', '2025-04-21', '', '-', 'NV01', 15, 45, 40.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (52, 6, 1, '2025-04-22', '2025-04-22', '', '-', 'NV01', 16, 46, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (53, 6, 1, '2025-04-22', '2025-04-22', '', '-', 'NV01', 17, 47, 70.00, '1', '0', 12, 1, '0', '', 0.18, 5, '70', NULL, NULL, NULL, 1, '', NULL, 79);
INSERT INTO `ventas` VALUES (54, 2, 1, '2025-04-26', '2025-04-26', '', 'AREQUIPA', 'F001', 2, 48, 202.00, '1', '0', 12, 1, '0', '', 0.18, 5, '202', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (55, 6, 1, '2025-04-28', '2025-04-28', '', '-', 'NV01', 18, 49, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (56, 6, 1, '2025-04-28', '2025-04-28', '', '-', 'NV01', 19, 50, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (57, 6, 1, '2025-04-30', '2025-04-30', '', '-', 'NV01', 20, 51, 145.00, '1', '0', 12, 1, '0', 'COMPRA DE ACEITE ', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 80);
INSERT INTO `ventas` VALUES (58, 6, 1, '2025-04-30', '2025-04-30', '', '-', 'NV01', 21, 52, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (59, 6, 1, '2025-05-05', '2025-05-05', '', '-', 'NV01', 22, 53, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (60, 6, 1, '2025-05-05', '2025-05-05', '', '-', 'NV01', 23, 54, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (61, 6, 1, '2025-05-06', '2025-05-06', '', '-', 'NV01', 24, 55, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (62, 6, 1, '2025-05-07', '2025-05-07', '', '-', 'NV01', 25, 56, 40.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (63, 6, 1, '2025-05-07', '2025-05-07', '', '-', 'NV01', 26, 57, 40.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 80);
INSERT INTO `ventas` VALUES (64, 1, 1, '2025-05-12', '2025-05-12', '', '-', 'B001', 11, 58, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (65, 6, 1, '2025-05-12', '2025-05-12', '', '-', 'NV01', 27, 59, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (66, 6, 1, '2025-05-12', '2025-05-12', '', '-', 'NV01', 28, 60, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (67, 6, 1, '2025-05-15', '2025-05-15', '', '-', 'NV01', 29, 61, 40.00, '1', '0', 12, 1, '0', '', 0.18, 99, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (68, 6, 1, '2025-05-16', '2025-05-16', '', '-', 'NV01', 30, 62, 40.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (69, 6, 1, '2025-05-20', '2025-05-20', '', '-', 'NV01', 31, 54, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (70, 6, 1, '2025-05-20', '2025-05-20', '', '-', 'NV01', 32, 63, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (71, 1, 1, '2025-05-20', '2025-05-20', '', '-', 'B001', 12, 5, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '50', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (72, 6, 1, '2025-05-21', '2025-05-21', '', '-', 'NV01', 33, 64, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (73, 6, 1, '2025-05-21', '2025-05-21', '', '-', 'NV01', 34, 65, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (74, 6, 1, '2025-05-26', '2025-05-26', '', '-', 'NV01', 35, 66, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (75, 1, 1, '2025-05-27', '2025-05-27', '', '-', 'B001', 13, 62, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (76, 1, 1, '2025-05-27', '2025-05-27', '', '-', 'B001', 14, 62, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (77, 1, 1, '2025-05-27', '2025-05-27', '', '-', 'B001', 15, 62, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (80, 1, 1, '2025-05-27', '2025-05-27', '', '-', 'B001', 18, 62, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (81, 1, 1, '2025-05-27', '2025-05-27', '', '-', 'B001', 19, 68, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (82, 6, 1, '2025-05-28', '2025-05-28', '', 'Arequipa', 'NV01', 36, 69, 40.00, '1', '0', 12, 1, '0', 'No registra su C.E.', 0.18, 12, '40', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (83, 6, 1, '2025-05-29', '2025-05-29', '', '-', 'NV01', 37, 70, 165.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (84, 6, 3, '2025-05-28', '2025-05-30', '', '-', 'NV01', 38, 68, 40.00, '1', '0', 12, 1, '0', '', 0.18, 99, '0', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (85, 1, 1, '2025-06-03', '2025-06-03', '', '-', 'B001', 20, 71, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 85);
INSERT INTO `ventas` VALUES (86, 1, 1, '2025-06-03', '2025-06-03', '', '-', 'B001', 21, 72, 20.00, '1', '0', 12, 1, '0', '', 0.18, 99, '20', NULL, NULL, NULL, 1, '', NULL, 85);
INSERT INTO `ventas` VALUES (87, 6, 1, '2025-06-05', '2025-06-05', '', '-', 'NV01', 39, 73, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (88, 6, 1, '2025-06-05', '2025-06-05', '', '-', 'NV01', 40, 73, 40.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (89, 1, 3, '2025-06-09', '2025-06-09', '', 'Arequipa', 'B001', 22, 74, 0.00, '1', '0', 12, 1, '0', '', 0.18, 99, '', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (90, 6, 1, '2025-06-10', '2025-06-10', '', '-', 'NV01', 41, 75, 45.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (91, 1, 1, '2025-06-10', '2025-06-10', '', '-', 'B001', 23, 76, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (92, 6, 1, '2025-06-11', '2025-06-11', '', '-', 'NV01', 42, 77, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (93, 6, 1, '2025-06-12', '2025-06-12', '', '-', 'NV01', 43, 78, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (94, 6, 1, '2025-06-12', '2025-06-12', '', '-', 'NV01', 44, 70, 70.00, '1', '0', 12, 1, '0', 'SE VENDIO UNA CASACA TALLA M EN EFECTIVO, Y SE PROCEDE A DESCONTAR CON LA AUTORIZACION DE GIAN UNA PARTE DE LA DEVOLUCION DEL SOAT. QUEDANDO PENDIENTE 20 SOLES', 0.18, 12, '70', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (95, 6, 1, '2025-06-16', '2025-06-16', '', '-', 'NV01', 45, 79, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (96, 6, 1, '2025-06-16', '2025-06-16', '', '-', 'NV01', 46, 80, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (97, 1, 3, '2025-06-16', '2025-06-16', '', '-', 'B001', 24, 37, 0.00, '1', '0', 12, 1, '0', '', 0.18, 99, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (98, 6, 1, '2025-06-17', '2025-06-17', '', '-', 'NV01', 47, 81, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (99, 6, 1, '2025-06-17', '2025-06-17', '', '-', 'NV01', 48, 70, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (100, 6, 1, '2025-06-17', '2025-06-17', '', '-', 'NV01', 49, 70, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (101, 6, 1, '2025-06-18', '2025-06-18', '', '-', 'NV01', 50, 82, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (102, 6, 1, '2025-06-18', '2025-06-18', '', '-', 'NV01', 51, 59, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (103, 6, 1, '2025-06-18', '2025-06-18', '', '-', 'NV01', 52, 72, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (104, 6, 1, '2025-06-19', '2025-06-19', '', '-', 'NV01', 53, 83, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (105, 6, 2, '2025-06-19', '2025-06-19', '', '-', 'NV01', 54, 84, 40.00, '1', '0', 12, 1, '0', '', 0.18, 99, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (106, 6, 1, '2025-06-23', '2025-06-23', '', '-', 'NV01', 55, 85, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (107, 6, 1, '2025-06-23', '2025-06-23', '', '-', 'NV01', 56, 86, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 92);
INSERT INTO `ventas` VALUES (108, 1, 1, '2025-06-23', '2025-06-23', '', 'AREQUIPA', 'B001', 25, 18, 80.00, '1', '0', 12, 1, '1', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (109, 6, 1, '2025-06-25', '2025-06-25', '', 'AREQUIPA', 'NV01', 57, 69, 40.00, '1', '0', 12, 1, '0', '10 SOLES INICIAL 30 SOLES YAPE 25/06/2025', 0.18, 5, '40', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (110, 6, 1, '2025-06-25', '2025-06-25', '', '-', 'NV01', 58, 87, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (111, 6, 1, '2025-06-25', '2025-06-25', '', '-', 'NV01', 59, 88, 40.00, '1', '0', 12, 1, '0', 'PAGO YAPE 20 EL 10/06/2025 Y 20 YAPE EL 25/06/2025', 0.18, 5, '40', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (112, 6, 1, '2025-06-25', '2025-06-25', '', '-', 'NV01', 60, 89, 70.00, '1', '0', 12, 1, '0', 'PAGO YAPE 20 EL 10/06/2025 Y 50 EL 25/06/2025', 0.18, 12, '70', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (113, 6, 1, '2025-06-25', '2025-06-25', '', '-', 'NV01', 61, 90, 40.00, '1', '0', 12, 1, '0', 'PAGO 20 EN YAPE EL 10/06/2025 Y 20 YAPE EL 25/06/2025', 0.18, 5, '40', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (114, 6, 1, '2025-06-25', '2025-06-25', '', '-', 'NV01', 62, 91, 40.00, '1', '0', 12, 1, '0', 'PAGO YAPE 20 EL 09/06/2025 Y 20 YAPE EL 25/06/2025', 0.18, 5, '40', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (115, 6, 3, '2025-06-25', '2025-06-25', '', '-', 'NV01', 63, 92, 70.00, '1', '0', 12, 1, '0', 'GANADOR DEL SORTEO DEL DIA 23/06/2025', 0.18, 99, '', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (116, 1, 1, '2025-06-25', '2025-06-25', '', '-', 'B001', 26, 62, 40.00, '1', '0', 12, 1, '0', '', 0.18, 5, '40', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (117, 6, 1, '2025-06-25', '2025-06-25', '', 'CALLE FEDERICO BARRETO 112', 'NV01', 64, 93, 70.00, '1', '0', 12, 1, '0', '', 0.18, 12, '70', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (118, 6, 1, '2025-06-26', '2025-06-26', '', '-', 'NV01', 65, 94, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (119, 6, 1, '2025-06-26', '2025-06-26', '', '-', 'NV01', 66, 83, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (120, 6, 1, '2025-06-26', '2025-06-26', '', '-', 'NV01', 67, 95, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (121, 6, 1, '2025-06-26', '2025-06-26', '', '-', 'NV01', 68, 95, 70.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (122, 6, 3, '2025-06-27', '2025-06-27', '', '-', 'NV01', 69, 96, 45.00, '1', '0', 12, 1, '0', 'inscripcion a flota', 0.18, 99, '0', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (123, 6, 3, '2025-06-27', '2025-06-27', '', '-', 'NV01', 70, 97, 40.00, '1', '0', 12, 1, '0', 'inscripcion a flota', 0.18, 99, '0', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (124, 6, 3, '2025-06-27', '2025-06-27', '', '-', 'NV01', 71, 98, 40.00, '1', '0', 12, 1, '0', 'Ganador al 1er lugar del premio del sorteo del lunes 23.06.25', 0.18, 99, '', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (125, 6, 1, '2025-06-30', '2025-06-30', '', '-', 'NV01', 72, 70, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (126, 6, 1, '2025-06-30', '2025-06-30', '', '-', 'NV01', 73, 70, 40.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 90);
INSERT INTO `ventas` VALUES (127, 6, 1, '2025-07-04', '2025-07-04', '', '-', 'NV01', 74, 99, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (128, 1, 1, '2025-07-04', '2025-07-04', '', '-', 'B001', 27, 100, 30.00, '1', '0', 12, 1, '0', '', 0.18, 12, '30', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (129, 1, 1, '2025-07-05', '2025-07-05', '', '-', 'B001', 28, 101, 40.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 78);
INSERT INTO `ventas` VALUES (130, 6, 1, '2025-07-07', '2025-07-07', '', '-', 'NV01', 75, 102, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (131, 6, 1, '2025-07-07', '2025-07-07', '', '-', 'NV01', 76, 103, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (132, 6, 1, '2025-07-09', '2025-07-09', '', '-', 'NV01', 77, 104, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (133, 6, 1, '2025-07-09', '2025-07-09', '', '-', 'NV01', 78, 105, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (134, 6, 1, '2025-07-09', '2025-07-09', '', '-', 'NV01', 79, 43, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (135, 6, 1, '2025-07-11', '2025-07-11', '', 'arequipa', 'NV01', 80, 106, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '50', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (136, 6, 1, '2025-07-11', '2025-07-11', '', '-', 'NV01', 81, 107, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (137, 6, 1, '2025-07-14', '2025-07-14', '', '-', 'NV01', 82, 108, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 92);
INSERT INTO `ventas` VALUES (138, 6, 1, '2025-07-14', '2025-07-14', '', '-', 'NV01', 83, 109, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (139, 6, 1, '2025-07-14', '2025-07-14', '', '-', 'NV01', 84, 110, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (140, 6, 1, '2025-07-14', '2025-07-14', '', '-', 'NV01', 85, 73, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (141, 6, 1, '2025-07-14', '2025-07-14', '', '-', 'NV01', 86, 111, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (142, 6, 1, '2025-07-14', '2025-07-14', '', '-', 'NV01', 87, 112, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (143, 6, 1, '2025-07-17', '2025-07-17', '', '-', 'NV01', 88, 113, 20.00, '1', '0', 12, 1, '0', 'ROJAS ACOSTA BRAYIAM YORDAN', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (144, 6, 1, '2025-07-17', '2025-07-17', '', '-', 'NV01', 89, 56, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (145, 6, 1, '2025-07-17', '2025-07-17', '', '-', 'NV01', 90, 114, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (146, 6, 1, '2025-07-17', '2025-07-17', '', '-', 'NV01', 91, 31, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (147, 6, 1, '2025-07-18', '2025-07-18', '', '-', 'NV01', 92, 72, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (148, 6, 1, '2025-07-19', '2025-07-19', '', '-', 'NV01', 93, 115, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (149, 6, 1, '2025-07-19', '2025-07-19', '', '-', 'NV01', 94, 43, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (150, 6, 1, '2025-07-19', '2025-07-19', '', '-', 'NV01', 95, 116, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (151, 1, 1, '2025-07-22', '2025-07-22', '', 'AREQUIPA', 'B001', 29, 106, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (152, 6, 1, '2025-07-22', '2025-07-22', '', '-', 'NV01', 96, 106, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (153, 1, 1, '2025-07-23', '2025-07-23', '', '-', 'B001', 30, 45, 29.00, '1', '0', 12, 1, '1', '', 0.18, 5, '29', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (157, 6, 1, '2025-07-24', '2025-07-24', '', '-', 'NV01', 97, 109, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (158, 1, 1, '2025-07-24', '2025-07-24', '', '-', 'B001', 34, 117, 12.00, '1', '0', 12, 1, '1', '', 0.18, 12, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (159, 1, 1, '2025-07-25', '2025-07-25', '', '-', 'B001', 35, 118, 12.00, '1', '0', 12, 1, '1', '', 0.18, 12, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (160, 6, 1, '2025-07-25', '2025-07-25', '', '-', 'NV01', 98, 119, 30.00, '1', '0', 12, 1, '0', '', 0.18, 12, '30', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (161, 1, 1, '2025-07-29', '2025-07-29', '', '-', 'B001', 36, 57, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (162, 6, 1, '2025-07-30', '2025-07-30', '', '-', 'NV01', 99, 43, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (163, 6, 1, '2025-07-30', '2025-07-30', '', '-', 'NV01', 100, 120, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (164, 1, 1, '2025-07-31', '2025-07-31', '', '-', 'B001', 37, 121, 40.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (165, 6, 1, '2025-08-01', '2025-08-01', '', '-', 'NV01', 101, 122, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (166, 6, 1, '2025-08-01', '2025-08-01', '', '-', 'NV01', 102, 122, 40.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (167, 6, 1, '2025-08-01', '2025-08-01', '', '-', 'NV01', 103, 122, 40.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (168, 1, 1, '2025-08-01', '2025-08-01', '', 'AREQUIPA', 'B001', 38, 106, 20.00, '1', '0', 12, 1, '1', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (169, 6, 3, '2025-08-02', '2025-08-02', '', '-', 'NV01', 104, 123, 20.00, '1', '0', 12, 1, '0', '', 0.18, 99, '0', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (170, 6, 1, '2025-08-02', '2025-08-02', '', '-', 'NV01', 105, 124, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (171, 6, 1, '2025-08-02', '2025-08-02', '', '-', 'NV01', 106, 111, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (172, 6, 1, '2025-08-02', '2025-08-02', '', '-', 'NV01', 107, 111, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (173, 1, 1, '2025-08-02', '2025-08-02', '', '-', 'B001', 39, 123, 12.00, '1', '0', 12, 1, '1', '', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (174, 6, 1, '2025-08-04', '2025-08-04', '', '-', 'NV01', 108, 125, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (175, 6, 1, '2025-08-05', '2025-08-05', '', '-', 'NV01', 109, 126, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (176, 6, 1, '2025-08-05', '2025-08-05', '', '-', 'NV01', 110, 127, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (177, 1, 1, '2025-08-05', '2025-08-05', '', '-', 'B001', 40, 123, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (178, 6, 1, '2025-08-05', '2025-08-05', '', '-', 'NV01', 111, 128, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (179, 6, 1, '2025-08-06', '2025-08-06', '', '-', 'NV01', 112, 129, 140.00, '1', '0', 12, 1, '0', '', 0.18, 5, '140', NULL, NULL, NULL, 1, '', NULL, 83);
INSERT INTO `ventas` VALUES (180, 6, 1, '2025-08-07', '2025-08-07', '', '-', 'NV01', 113, 130, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (181, 6, 1, '2025-08-07', '2025-08-07', '', '-', 'NV01', 114, 131, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (182, 6, 1, '2025-08-07', '2025-08-07', '', '-', 'NV01', 115, 132, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (183, 6, 1, '2025-08-07', '2025-08-07', '', '-', 'NV01', 116, 132, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (184, 1, 3, '2025-08-07', '2025-08-07', '', 'URB. SANTA MARIA II ', 'B001', 41, 133, 0.00, '1', '0', 12, 1, '1', 'GRATIS ', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 108);
INSERT INTO `ventas` VALUES (185, 6, 1, '2025-08-08', '2025-08-08', '', '-', 'NV01', 117, 134, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (186, 1, 1, '2025-08-08', '2025-08-08', '', '-', 'B001', 42, 43, 20.00, '1', '0', 12, 1, '1', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (187, 6, 1, '2025-08-08', '2025-08-08', '', '-', 'NV01', 118, 135, 24.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (188, 6, 1, '2025-08-08', '2025-08-08', '', '-', 'NV01', 119, 136, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (189, 6, 1, '2025-08-08', '2025-08-08', '', '-', 'NV01', 120, 137, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (190, 6, 1, '2025-08-08', '2025-08-08', '', '-', 'NV01', 121, 138, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (191, 1, 3, '2025-08-10', '2025-08-10', '', 'AV. CAYMA 3556', 'B001', 43, 139, 0.00, '1', '0', 12, 1, '1', 'POLO GRATIS', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (192, 6, 1, '2025-08-11', '2025-08-11', '', '-', 'NV01', 122, 140, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (193, 6, 1, '2025-08-11', '2025-08-11', '', '-', 'NV01', 123, 140, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (194, 6, 1, '2025-08-11', '2025-08-11', '', '-', 'NV01', 124, 5, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (195, 6, 1, '2025-08-12', '2025-08-12', '', '-', 'NV01', 125, 141, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (196, 6, 1, '2025-08-12', '2025-08-12', '', '-', 'NV01', 126, 142, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (197, 6, 1, '2025-08-12', '2025-08-12', '', '-', 'NV01', 127, 143, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (198, 1, 3, '2025-08-13', '2025-08-13', '', '-', 'B001', 44, 144, 0.00, '1', '0', 12, 1, '1', 'ENTREGA POR INSCRIPCIÓN A FLOTA', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 108);
INSERT INTO `ventas` VALUES (199, 6, 1, '2025-08-13', '2025-08-13', '', '-', 'NV01', 128, 145, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (200, 1, 1, '2025-08-14', '2025-08-14', '', '-', 'B001', 45, 146, 17.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (201, 1, 1, '2025-08-14', '2025-08-14', '', '-', 'B001', 46, 146, 34.00, '2', '0', 12, 1, '1', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (202, 6, 1, '2025-08-14', '2025-08-14', '', '-', 'NV01', 129, 146, 40.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (203, 6, 1, '2025-08-14', '2025-08-14', '', '-', 'NV01', 130, 147, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (204, 1, 1, '2025-08-14', '2025-08-14', '', '-', 'B001', 47, 146, 34.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (205, 6, 1, '2025-08-14', '2025-08-14', '', '-', 'NV01', 131, 148, 40.00, '1', '0', 12, 1, '0', '', 0.18, 12, '40', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (206, 1, 1, '2025-08-17', '2025-08-17', '', '-', 'B001', 48, 149, 12.00, '1', '0', 12, 1, '0', 'Ambientador C74', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (207, 1, 1, '2025-08-17', '2025-08-17', '', '-', 'B001', 49, 42, 12.00, '1', '0', 12, 1, '0', 'C74', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (208, 1, 1, '2025-08-17', '2025-08-17', '', '-', 'B001', 50, 46, 12.00, '1', '0', 12, 1, '0', 'AMBIENTADOR C74', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (209, 1, 1, '2025-08-17', '2025-08-17', '', '-', 'B001', 51, 150, 36.00, '1', '0', 12, 1, '0', 'Ambientador C74 - C138 - C49', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (210, 1, 1, '2025-08-17', '2025-08-17', '', '-', 'B001', 52, 118, 12.00, '1', '0', 12, 1, '0', 'ambientador ', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (211, 6, 1, '2025-08-18', '2025-08-18', '', '-', 'NV01', 132, 151, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (212, 6, 1, '2025-08-18', '2025-08-18', '', '-', 'NV01', 133, 152, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (213, 6, 1, '2025-08-18', '2025-08-18', '', '-', 'NV01', 134, 153, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (214, 6, 1, '2025-08-18', '2025-08-18', '', '-', 'NV01', 135, 153, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (215, 6, 1, '2025-08-18', '2025-08-18', '', '-', 'NV01', 136, 104, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (216, 6, 1, '2025-08-19', '2025-08-19', '', '-', 'NV01', 137, 154, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (217, 6, 1, '2025-08-19', '2025-08-19', '', '-', 'NV01', 138, 30, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (218, 6, 1, '2025-08-20', '2025-08-20', '', '-', 'NV01', 139, 43, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (219, 6, 1, '2025-08-20', '2025-08-20', '', '-', 'NV01', 140, 106, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (220, 6, 1, '2025-08-21', '2025-08-21', '', '-', 'NV01', 141, 155, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (221, 6, 1, '2025-08-22', '2025-08-22', '', '-', 'NV01', 142, 156, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (222, 6, 1, '2025-08-22', '2025-08-22', '', '-', 'NV01', 143, 157, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (223, 6, 1, '2025-08-22', '2025-08-22', '', '-', 'NV01', 144, 158, 5.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (224, 1, 1, '2025-08-22', '2025-08-22', '', '-', 'B001', 53, 57, 17.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (225, 6, 1, '2025-08-22', '2025-08-22', '', '-', 'NV01', 145, 159, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (226, 6, 1, '2025-08-22', '2025-08-22', '', '-', 'NV01', 146, 160, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (227, 6, 3, '2025-08-25', '2025-08-25', '', '-', 'NV01', 147, 161, 20.00, '1', '0', 12, 1, '0', '', 0.18, 99, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (228, 6, 1, '2025-08-25', '2025-08-25', '', '-', 'NV01', 148, 162, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (229, 6, 1, '2025-08-25', '2025-08-25', '', '-', 'NV01', 149, 163, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (230, 1, 1, '2025-08-25', '2025-08-25', '', '-', 'B001', 54, 164, 40.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (231, 1, 1, '2025-08-25', '2025-08-25', '', '-', 'B001', 55, 164, 50.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (232, 1, 1, '2025-08-26', '2025-08-26', '', '-', 'B001', 56, 126, 50.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (233, 1, 1, '2025-08-26', '2025-08-26', '', '-', 'B001', 57, 126, 50.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (234, 6, 1, '2025-08-26', '2025-08-26', '', '-', 'NV01', 150, 165, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (235, 6, 1, '2025-08-27', '2025-08-27', '', '-', 'NV01', 151, 160, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (236, 6, 1, '2025-08-27', '2025-08-27', '', '-', 'NV01', 152, 37, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (237, 6, 1, '2025-08-28', '2025-08-28', '', '-', 'NV01', 153, 106, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (238, 1, 1, '2025-08-28', '2025-08-28', '', 'AREQUIPA', 'B001', 58, 15, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (239, 6, 1, '2025-08-28', '2025-08-28', '', '-', 'NV01', 154, 166, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (240, 1, 1, '2025-08-28', '2025-08-28', '', '-', 'B001', 59, 122, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (241, 1, 3, '2025-08-28', '2025-08-28', '', '-', 'B001', 60, 167, 0.00, '1', '0', 12, 1, '0', 'POLO GRATIS INGRESO A FLOTA', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (242, 1, 3, '2025-08-28', '2025-08-28', '', '-', 'B001', 61, 167, 0.00, '1', '0', 12, 1, '0', 'INGRESO A FLOTA PAGO AL CONTADO', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (243, 1, 3, '2025-08-28', '2025-08-28', '', '-', 'B001', 62, 167, 0.00, '1', '0', 12, 1, '0', 'INGRESO A FLOTA ', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (244, 1, 1, '2025-08-29', '2025-08-29', '', '-', 'B001', 63, 132, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (245, 1, 1, '2025-08-29', '2025-08-29', '', '-', 'B001', 64, 168, 24.00, '1', '0', 12, 1, '0', '', 0.18, 12, '24', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (246, 1, 1, '2025-08-29', '2025-08-29', '', '-', 'B001', 65, 164, 45.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (247, 6, 1, '2025-08-29', '2025-08-29', '', '-', 'NV01', 155, 169, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (248, 1, 1, '2025-09-01', '2025-09-01', '', '-', 'B001', 66, 170, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (249, 1, 1, '2025-09-01', '2025-09-01', '', '-', 'B001', 67, 171, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (250, 6, 1, '2025-09-01', '2025-09-01', '', '-', 'NV01', 156, 172, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (251, 1, 3, '2025-09-01', '2025-09-01', '', '-', 'B001', 68, 171, 0.00, '1', '0', 12, 1, '0', 'INGRESO A FLOTA', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (252, 6, 1, '2025-09-01', '2025-09-01', '', '-', 'NV01', 157, 163, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (253, 6, 1, '2025-09-01', '2025-09-01', '', '-', 'NV01', 158, 173, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (254, 6, 1, '2025-09-01', '2025-09-01', '', '-', 'NV01', 159, 137, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (255, 1, 1, '2025-09-02', '2025-09-02', '', '-', 'B001', 69, 174, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (256, 6, 1, '2025-09-02', '2025-09-02', '', '-', 'NV01', 160, 175, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (257, 6, 1, '2025-09-02', '2025-09-02', '', '-', 'NV01', 161, 75, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (258, 6, 1, '2025-09-03', '2025-09-03', '', '-', 'NV01', 162, 43, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (259, 1, 1, '2025-09-03', '2025-09-03', '', 'Arequips', 'B001', 70, 164, 17.00, '1', '0', 12, 1, '1', '', 0.18, 12, '17.00', NULL, NULL, NULL, 1, '', NULL, 108);
INSERT INTO `ventas` VALUES (260, 1, 3, '2025-09-03', '2025-09-03', '', '-', 'B001', 71, 176, 0.00, '1', '0', 12, 1, '1', 'INGRESO A FLOTA ', 0.18, 99, '0', NULL, NULL, NULL, 1, '', NULL, 108);
INSERT INTO `ventas` VALUES (261, 6, 1, '2025-09-03', '2025-09-03', '', '-', 'NV01', 163, 177, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (262, 6, 1, '2025-09-04', '2025-09-04', '', '-', 'NV01', 164, 178, 40.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (263, 6, 1, '2025-09-04', '2025-09-04', '', '-', 'NV01', 165, 178, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (264, 1, 1, '2025-09-05', '2025-09-05', '', '-', 'B001', 72, 179, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (265, 1, 3, '2025-09-05', '2025-09-05', '', '-', 'B001', 73, 180, 0.00, '1', '0', 12, 1, '0', 'ingreso a flota', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (266, 1, 3, '2025-09-05', '2025-09-05', '', '-', 'B001', 74, 180, 0.00, '1', '0', 12, 1, '0', 'INGRESO A FLOTA', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (267, 1, 1, '2025-09-05', '2025-09-05', '', '-', 'B001', 75, 181, 40.00, '2', '0', 12, 1, '0', 'INGRESO A FLOTA', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (268, 1, 3, '2025-09-05', '2025-09-05', '', '-', 'B001', 76, 181, 0.00, '1', '0', 12, 1, '0', 'INGRESO A FLOTA', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (269, 1, 3, '2025-09-05', '2025-09-05', '', '-', 'B001', 77, 181, 0.00, '1', '0', 12, 1, '0', 'ingreso a flota', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (270, 1, 3, '2025-09-05', '2025-09-05', '', '-', 'B001', 78, 181, 0.00, '1', '0', 12, 1, '0', 'INGRESO A FLOTA', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (271, 1, 3, '2025-09-05', '2025-09-05', '', '-', 'B001', 79, 182, 0.00, '1', '0', 12, 1, '0', 'INGRESO A FLOTA', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (272, 1, 3, '2025-09-05', '2025-09-05', '', '-', 'B001', 80, 182, 0.00, '1', '0', 12, 1, '0', 'INGRESO A FLOTA', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (273, 1, 3, '2025-09-05', '2025-09-05', '', '-', 'B001', 81, 183, 0.00, '2', '0', 12, 1, '1', 'INGRESO A FLOTA', 0.18, 12, '0', NULL, NULL, NULL, 1, '', NULL, 108);
INSERT INTO `ventas` VALUES (274, 1, 3, '2025-09-05', '2025-09-05', '', '-', 'B001', 82, 184, 0.00, '1', '0', 12, 1, '1', 'INGRESO A FLOTA ', 0.18, 12, '0', NULL, NULL, NULL, 1, '', NULL, 108);
INSERT INTO `ventas` VALUES (275, 1, 3, '2025-09-05', '2025-09-05', '', '-', 'B001', 83, 184, 0.00, '1', '0', 12, 1, '1', 'INGRESOO A FLOTA ', 0.18, 12, '0', NULL, NULL, NULL, 1, '', NULL, 108);
INSERT INTO `ventas` VALUES (276, 1, 3, '2025-09-05', '2025-09-05', '', '-', 'B001', 84, 184, 0.00, '1', '0', 12, 1, '1', 'INGRESO A FLOTA', 0.18, 12, '0', NULL, NULL, NULL, 1, '', NULL, 108);
INSERT INTO `ventas` VALUES (277, 1, 3, '2025-09-06', '2025-09-06', '', '-', 'B001', 85, 183, 0.00, '1', '0', 12, 1, '1', 'INGRESO A FLOTA', 0.18, 12, '0', NULL, NULL, NULL, 1, '', NULL, 108);
INSERT INTO `ventas` VALUES (278, 1, 3, '2025-09-06', '2025-09-06', '', '-', 'B001', 86, 183, 0.00, '1', '0', 12, 1, '1', 'INGRESO A FLOTA', 0.18, 12, '0', NULL, NULL, NULL, 1, '', NULL, 108);
INSERT INTO `ventas` VALUES (279, 1, 3, '2025-09-06', '2025-09-06', '', '-', 'B001', 87, 183, 0.00, '1', '0', 12, 1, '1', 'INGRESO A FLOTA', 0.18, 12, '0', NULL, NULL, NULL, 1, '', NULL, 108);
INSERT INTO `ventas` VALUES (280, 6, 3, '2025-09-06', '2025-09-06', '', '-', 'NV01', 166, 185, 70.00, '2', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 110);
INSERT INTO `ventas` VALUES (281, 1, 3, '2025-09-08', '2025-09-08', '', '-', 'B001', 88, 186, 0.00, '1', '0', 12, 1, '1', 'INGRESO DE PERSONAL', 0.18, 12, '0', NULL, NULL, NULL, 1, '', NULL, 108);
INSERT INTO `ventas` VALUES (282, 6, 1, '2025-09-08', '2025-09-08', '', '-', 'NV01', 167, 187, 34.00, '1', '0', 12, 1, '0', '', 0.18, 5, '34', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (283, 1, 1, '2025-09-09', '2025-09-09', '', '-', 'B001', 89, 101, 12.00, '1', '0', 12, 1, '1', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (284, 1, 1, '2025-09-09', '2025-09-09', '', '-', 'B001', 90, 184, 70.00, '1', '0', 12, 1, '0', 'Pago con QR', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 111);
INSERT INTO `ventas` VALUES (285, 6, 1, '2025-09-09', '2025-09-09', '', '-', 'NV01', 168, 119, 30.00, '1', '0', 12, 1, '0', '', 0.18, 5, '30', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (286, 6, 1, '2025-09-10', '2025-09-10', '', '-', 'NV01', 169, 142, 29.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (287, 1, 1, '2025-09-11', '2025-09-11', '', '-', 'B001', 91, 188, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (288, 1, 1, '2025-09-11', '2025-09-11', '', '-', 'B001', 92, 164, 40.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (289, 1, 1, '2025-09-12', '2025-09-12', '', '-', 'B001', 93, 189, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (290, 1, 3, '2025-09-12', '2025-09-12', '', '-', 'B001', 94, 190, 0.00, '1', '0', 12, 1, '0', 'oficina 2 ingreso de personal', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 121);
INSERT INTO `ventas` VALUES (291, 1, 3, '2025-09-12', '2025-09-12', '', '-', 'B001', 95, 190, 0.00, '1', '0', 12, 1, '0', 'ingreso personal ofc 2', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 121);
INSERT INTO `ventas` VALUES (292, 1, 3, '2025-09-12', '2025-09-12', '', '-', 'B001', 96, 190, 0.00, '1', '0', 12, 1, '0', 'ingreso personal ofc 2', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 121);
INSERT INTO `ventas` VALUES (293, 1, 3, '2025-09-12', '2025-09-12', '', '-', 'B001', 97, 190, 0.00, '1', '0', 12, 1, '0', 'ingreso personal ofc 2', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 121);
INSERT INTO `ventas` VALUES (294, 1, 1, '2025-09-12', '2025-09-12', '', 'AREQUIPA ', 'B001', 98, 4, 40.00, '2', '0', 12, 1, '1', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 77);
INSERT INTO `ventas` VALUES (295, 1, 3, '2025-09-12', '2025-09-12', '', '-', 'B001', 99, 190, 0.00, '1', '0', 12, 1, '0', 'OFICINA 2 PERSONAL', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 121);
INSERT INTO `ventas` VALUES (296, 1, 1, '2025-09-12', '2025-09-12', '', '-', 'B001', 100, 191, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (297, 1, 1, '2025-09-12', '2025-09-12', '', '-', 'B001', 101, 192, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (298, 1, 1, '2025-09-12', '2025-09-12', '', '-', 'B001', 102, 43, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (299, 1, 1, '2025-09-12', '2025-09-12', '', '-', 'B001', 103, 185, 20.00, '2', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 85);
INSERT INTO `ventas` VALUES (300, 1, 1, '2025-09-12', '2025-09-12', '', '-', 'B001', 104, 139, 20.00, '2', '0', 12, 1, '0', 'prueba ', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 99);
INSERT INTO `ventas` VALUES (304, 1, 1, '2025-09-12', '2025-09-12', '', '-', 'B001', 106, 194, 20.00, '1', '0', 12, 1, '0', 'pago con QR', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 111);
INSERT INTO `ventas` VALUES (305, 1, 1, '2025-09-12', '2025-09-12', '', '-', 'B001', 107, 195, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (306, 6, 1, '2025-09-12', '2025-09-12', '', '-', 'NV01', 172, 163, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (307, 6, 1, '2025-09-13', '2025-09-13', '', '-', 'NV01', 173, 174, 36.00, '1', '0', 12, 1, '0', '', 0.18, 5, '36', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (308, 6, 1, '2025-09-15', '2025-09-15', '', '-', 'NV01', 174, 196, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (309, 1, 1, '2025-09-15', '2025-09-15', '', '-', 'B001', 108, 134, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (310, 1, 1, '2025-09-16', '2025-09-16', '', '-', 'B001', 109, 197, 12.00, '1', '0', 12, 1, '0', 'AMBIENTADOR C76', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 111);
INSERT INTO `ventas` VALUES (311, 1, 1, '2025-09-17', '2025-09-17', '', '-', 'B001', 110, 145, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (312, 1, 1, '2025-09-19', '2025-09-19', '', '-', 'B001', 111, 59, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (313, 6, 1, '2025-09-19', '2025-09-19', '', '-', 'NV01', 175, 122, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (314, 6, 1, '2025-09-20', '2025-09-20', '', '-', 'NV01', 176, 198, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (315, 1, 1, '2025-09-22', '2025-09-22', '', '-', 'B001', 112, 199, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (316, 1, 1, '2025-09-23', '2025-09-23', '', '-', 'B001', 113, 200, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (317, 1, 1, '2025-09-23', '2025-09-23', '', '-', 'B001', 114, 179, 50.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (318, 1, 1, '2025-09-23', '2025-09-23', '', '-', 'B001', 115, 31, 12.00, '2', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (319, 6, 1, '2025-09-23', '2025-09-23', '', '-', 'NV01', 177, 201, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (320, 6, 1, '2025-09-25', '2025-09-25', '', '-', 'NV01', 178, 57, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (321, 1, 3, '2025-09-25', '2025-09-25', '', '-', 'B001', 116, 202, 0.00, '2', '0', 12, 1, '0', '', 0.18, 99, '', NULL, NULL, NULL, 1, '', NULL, 110);
INSERT INTO `ventas` VALUES (322, 1, 1, '2025-09-29', '2025-09-29', '', '-', 'B001', 117, 194, 40.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (323, 1, 1, '2025-09-29', '2025-09-29', '', '-', 'B001', 118, 203, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (324, 6, 1, '2025-09-29', '2025-09-29', '', '-', 'NV01', 179, 204, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (325, 1, 1, '2025-09-30', '2025-09-30', '', 'AREQUIPA ', 'B001', 119, 4, 420.00, '2', '0', 12, 1, '1', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 77);
INSERT INTO `ventas` VALUES (326, 1, 3, '2025-10-01', '2025-10-01', '', '-', 'B001', 120, 202, 0.00, '2', '0', 12, 1, '1', '', 0.18, 99, '', NULL, NULL, NULL, 1, '', NULL, 110);
INSERT INTO `ventas` VALUES (327, 1, 3, '2025-10-01', '2025-10-01', '', '-', 'B001', 121, 202, 0.00, '1', '0', 12, 1, '1', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 110);
INSERT INTO `ventas` VALUES (328, 1, 3, '2025-10-01', '2025-10-01', '', '-', 'B001', 122, 202, 0.00, '1', '0', 12, 1, '1', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 110);
INSERT INTO `ventas` VALUES (329, 1, 1, '2025-10-02', '2025-10-02', '', '-', 'B001', 123, 175, 10.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (330, 6, 1, '2025-10-02', '2025-10-02', '', '-', 'NV01', 180, 205, 5.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (331, 6, 1, '2025-10-02', '2025-10-02', '', '-', 'NV01', 181, 18, 5.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (332, 1, 1, '2025-10-03', '2025-10-03', '', '-', 'B001', 124, 206, 40.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (333, 6, 1, '2025-10-04', '2025-10-04', '', '-', 'NV01', 182, 174, 12.00, '1', '0', 12, 1, '0', '', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (334, 6, 1, '2025-10-06', '2025-10-06', '', '-', 'NV01', 183, 118, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (335, 6, 1, '2025-10-06', '2025-10-06', '', '-', 'NV01', 184, 207, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (336, 6, 1, '2025-10-07', '2025-10-07', '', '-', 'NV01', 185, 208, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (337, 6, 1, '2025-10-08', '2025-10-08', '', '-', 'NV01', 186, 151, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (338, 1, 1, '2025-10-09', '2025-10-09', '', '-', 'B001', 125, 45, 40.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (339, 1, 1, '2025-10-10', '2025-10-10', '', '-', 'B001', 126, 209, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (340, 1, 1, '2025-10-11', '2025-10-11', '', '-', 'B001', 127, 210, 20.00, '1', '0', 12, 1, '1', 'Logo yango', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 111);
INSERT INTO `ventas` VALUES (341, 1, 1, '2025-10-11', '2025-10-11', '', '-', 'B001', 128, 210, 12.00, '1', '0', 12, 1, '0', 'Ambientador C143', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 111);
INSERT INTO `ventas` VALUES (342, 1, 1, '2025-10-11', '2025-10-11', '', '-', 'B001', 129, 136, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 111);
INSERT INTO `ventas` VALUES (343, 1, 1, '2025-10-11', '2025-10-11', '', '-', 'B001', 130, 136, 12.00, '1', '0', 12, 1, '0', 'C143', 0.18, 5, '12', NULL, NULL, NULL, 1, '', NULL, 111);
INSERT INTO `ventas` VALUES (344, 1, 1, '2025-10-13', '2025-10-13', '', '-', 'B001', 131, 178, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (345, 1, 1, '2025-10-13', '2025-10-13', '', '-', 'B001', 132, 122, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '12', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (346, 6, 1, '2025-10-13', '2025-10-13', '', '-', 'NV01', 187, 211, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (347, 6, 1, '2025-10-13', '2025-10-13', '', '-', 'NV01', 188, 212, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (348, 1, 1, '2025-10-14', '2025-10-14', '', '-', 'B001', 133, 213, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (349, 6, 1, '2025-10-15', '2025-10-15', '', '-', 'NV01', 189, 214, 5.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 123);
INSERT INTO `ventas` VALUES (350, 6, 1, '2025-10-15', '2025-10-15', '', '-', 'NV01', 190, 215, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (351, 6, 1, '2025-10-15', '2025-10-15', '', '-', 'NV01', 191, 215, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (352, 6, 1, '2025-10-15', '2025-10-15', '', '-', 'NV01', 192, 215, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (353, 6, 1, '2025-10-16', '2025-10-16', '', '-', 'NV01', 193, 194, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (354, 1, 3, '2025-10-17', '2025-10-17', '', '-', 'B001', 134, 216, 0.00, '1', '0', 12, 1, '0', '', 0.18, 12, '45', NULL, NULL, NULL, 1, '', NULL, 122);
INSERT INTO `ventas` VALUES (355, 6, 1, '2025-10-17', '2025-10-17', '', '-', 'NV01', 194, 217, 12.00, '1', '0', 12, 1, '0', '', 0.18, 12, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (356, 6, 1, '2025-10-18', '2025-10-18', '', '-', 'NV01', 195, 119, 22.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (357, 6, 1, '2025-10-20', '2025-10-20', '', '-', 'NV01', 196, 180, 57.00, '1', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (358, 6, 1, '2025-10-20', '2025-10-20', '', '-', 'NV01', 197, 180, 57.00, '2', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (359, 6, 1, '2025-10-20', '2025-10-20', '', '-', 'NV01', 198, 218, 20.00, '1', '0', 12, 1, '0', '', 0.18, 5, '20', NULL, NULL, NULL, 1, '', NULL, 105);
INSERT INTO `ventas` VALUES (360, 6, 1, '2025-10-21', '2025-10-21', '', '-', 'NV01', 199, 219, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (361, 1, 1, '2025-10-21', '2025-10-21', '', '-', 'B001', 135, 216, 45.00, '1', '0', 12, 1, '0', '', 0.18, 12, '45', NULL, NULL, NULL, 1, '', NULL, 122);
INSERT INTO `ventas` VALUES (362, 6, 1, '2025-10-22', '2025-10-22', '', '-', 'NV01', 200, 220, 17.00, '2', '0', 12, 1, '0', '', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (363, 6, 1, '2025-10-24', '2025-10-24', '', '-', 'NV01', 201, 129, 20.00, '1', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (364, 6, 1, '2025-10-24', '2025-10-24', '', '-', 'NV01', 202, 159, 20.00, '2', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 82);
INSERT INTO `ventas` VALUES (365, 1, 1, '2025-10-27', '2025-10-27', '', '-', 'B001', 136, 210, 20.00, '2', '0', 12, 1, '0', '', 0.18, 12, '', NULL, NULL, NULL, 1, '', NULL, 106);
INSERT INTO `ventas` VALUES (366, 1, 1, '2025-10-27', '2025-10-27', '', '-', 'B001', 137, 165, 40.00, '1', '0', 12, 1, '0', 'Talla M', 0.18, 5, '', NULL, NULL, NULL, 1, '', NULL, 111);

-- ----------------------------
-- Triggers structure for table ventas
-- ----------------------------
DROP TRIGGER IF EXISTS `ti_ventas`;
delimiter ;;
CREATE TRIGGER `ti_ventas` AFTER INSERT ON `ventas` FOR EACH ROW BEGIN
DECLARE idtido_ INT;
DECLARE idempresa_ INT;
DECLARE idcliente_ INT;
DECLARE total_ FLOAT;
DECLARE fecha_ DATE;
DECLARE sucursal_ INT;
SET idtido_ = new.id_tido;
SET idempresa_ = new.id_empresa;
SET idcliente_ = new.id_cliente;
SET fecha_ = new.fecha_emision;
SET total_ = new.total;
SET sucursal_ = new.sucursal;
UPDATE documentos_empresas AS de 
SET de.numero = de.numero + 1 
WHERE de.id_empresa = idempresa_ AND de.id_tido = idtido_ AND sucursal = sucursal_;
UPDATE clientes AS c 
SET c.ultima_venta = fecha_, c.total_venta = c.total_venta + total_
WHERE c.id_cliente = idcliente_;
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
