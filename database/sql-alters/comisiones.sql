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

 Date: 31/12/2025 15:11:41
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for comisiones
-- ----------------------------
DROP TABLE IF EXISTS `comisiones`;
CREATE TABLE `comisiones`  (
  `id_comision` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `tipo_comision` enum('inscripcion','financiamiento') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `referencia_id` int NOT NULL,
  `monto_comision` decimal(10, 2) NOT NULL,
  `fecha_comision` datetime NOT NULL,
  `tipo_vehiculo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `estado_comision` enum('pendiente','pagada','cancelada') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'pendiente',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `moneda` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'S/.',
  PRIMARY KEY (`id_comision`) USING BTREE,
  INDEX `usuario_id`(`usuario_id` ASC) USING BTREE,
  INDEX `tipo_comision`(`tipo_comision` ASC) USING BTREE,
  INDEX `referencia_id`(`referencia_id` ASC) USING BTREE,
  INDEX `idx_comisiones_moneda`(`moneda` ASC) USING BTREE,
  CONSTRAINT `comisiones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 179 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of comisiones
-- ----------------------------
INSERT INTO `comisiones` VALUES (1, 97, 'inscripcion', 398, 50.00, '2025-07-23 18:17:14', 'auto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (2, 97, 'inscripcion', 399, 50.00, '2025-07-23 18:32:24', 'auto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (3, 97, 'inscripcion', 400, 50.00, '2025-07-23 18:48:11', 'auto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (4, 82, 'financiamiento', 256, 50.00, '2025-07-24 10:05:16', NULL, 'pagada', 'Comisión por financiamiento - Financiamiento Celular', 'S/.');
INSERT INTO `comisiones` VALUES (5, 83, 'inscripcion', 401, 30.00, '2025-07-24 12:23:13', 'moto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (6, 83, 'inscripcion', 402, 30.00, '2025-07-24 15:38:54', 'moto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (8, 85, 'financiamiento', 258, 50.00, '2025-07-24 18:24:21', 'moto', 'pagada', 'Comisión por financiamiento - CREDI GO Moto', 'S/.');
INSERT INTO `comisiones` VALUES (9, 85, 'financiamiento', 259, 30.00, '2025-07-25 16:35:56', 'vehiculo', 'pagada', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 4)', '$');
INSERT INTO `comisiones` VALUES (10, 105, 'financiamiento', 260, 50.00, '2025-07-25 17:35:12', NULL, 'pagada', 'Comisión por financiamiento - Financiamiento Celular', 'S/.');
INSERT INTO `comisiones` VALUES (11, 83, 'inscripcion', 403, 30.00, '2025-07-25 19:21:34', 'moto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (12, 85, 'financiamiento', 261, 50.00, '2025-07-29 11:09:08', 'moto', 'pagada', 'Comisión por financiamiento - CREDI GO Moto', 'S/.');
INSERT INTO `comisiones` VALUES (13, 82, 'inscripcion', 404, 50.00, '2025-08-01 14:49:12', 'auto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (14, 82, 'financiamiento', 262, 30.00, '2025-08-04 10:53:44', 'vehiculo', 'pagada', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 4)', '$');
INSERT INTO `comisiones` VALUES (15, 82, 'financiamiento', 263, 50.00, '2025-08-04 16:39:13', NULL, 'pagada', 'Comisión por financiamiento - Financiamiento Celular', 'S/.');
INSERT INTO `comisiones` VALUES (16, 82, 'inscripcion', 405, 50.00, '2025-08-05 13:58:51', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (18, 82, 'financiamiento', 265, 50.00, '2025-08-11 14:12:01', NULL, 'pagada', 'Comisión por financiamiento - Financiamiento Celular', 'S/.');
INSERT INTO `comisiones` VALUES (19, 106, 'inscripcion', 406, 30.00, '2025-08-12 13:14:56', 'moto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (20, 108, 'inscripcion', 407, 30.00, '2025-08-12 14:32:43', 'moto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (21, 82, 'financiamiento', 267, 50.00, '2025-08-12 16:35:47', NULL, 'pagada', 'Comisión por financiamiento - Financiamiento Celular', 'S/.');
INSERT INTO `comisiones` VALUES (22, 79, 'financiamiento', 268, 50.00, '2025-08-18 17:23:54', 'moto', 'pagada', 'Comisión por financiamiento - CREDI GO Moto', 'S/.');
INSERT INTO `comisiones` VALUES (23, 108, 'inscripcion', 408, 30.00, '2025-08-19 18:21:34', 'moto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (24, 82, 'financiamiento', 273, 50.00, '2025-08-21 14:45:14', NULL, 'pagada', 'Comisión por financiamiento - Financiamiento Celular', 'S/.');
INSERT INTO `comisiones` VALUES (25, 82, 'inscripcion', 409, 50.00, '2025-08-25 12:10:49', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (26, 79, 'financiamiento', 274, 150.00, '2025-08-25 14:35:27', 'moto', 'pagada', 'Comisión por financiamiento - MOTO YA', 'S/.');
INSERT INTO `comisiones` VALUES (27, 79, 'financiamiento', 277, 150.00, '2025-08-26 16:08:18', 'moto', 'pagada', 'Comisión por financiamiento - MOTO YA', 'S/.');
INSERT INTO `comisiones` VALUES (28, 82, 'inscripcion', 410, 50.00, '2025-08-27 11:46:55', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (29, 82, 'inscripcion', 411, 50.00, '2025-08-27 17:26:25', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (30, 109, 'inscripcion', 412, 50.00, '2025-08-27 19:30:47', 'auto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (31, 85, 'inscripcion', 413, 50.00, '2025-08-27 20:52:01', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (36, 99, 'inscripcion', 414, 30.00, '2025-08-28 18:03:33', 'moto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (37, 106, 'inscripcion', 415, 50.00, '2025-08-29 12:24:03', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (38, 105, 'inscripcion', 416, 50.00, '2025-08-29 16:00:50', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (39, 106, 'inscripcion', 417, 50.00, '2025-08-29 17:14:30', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (40, 105, 'inscripcion', 418, 50.00, '2025-08-29 17:38:40', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (41, 105, 'inscripcion', 419, 50.00, '2025-08-29 18:36:00', 'auto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (42, 105, 'inscripcion', 420, 50.00, '2025-08-29 19:39:12', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (43, 99, 'inscripcion', 421, 50.00, '2025-08-29 22:58:08', 'auto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (44, 99, 'inscripcion', 422, 50.00, '2025-08-30 01:13:04', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (45, 99, 'inscripcion', 423, 50.00, '2025-08-30 01:34:07', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (46, 99, 'inscripcion', 424, 50.00, '2025-08-30 01:54:49', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (47, 82, 'financiamiento', 311, 50.00, '2025-09-01 10:09:48', NULL, 'pagada', 'Comisión por financiamiento - Financiamiento Celular', 'S/.');
INSERT INTO `comisiones` VALUES (48, 82, 'financiamiento', 311, 50.00, '2025-09-01 10:25:46', NULL, 'pagada', 'Comisión por financiamiento - Financiamiento Celular', 'S/.');
INSERT INTO `comisiones` VALUES (49, 109, 'inscripcion', 425, 50.00, '2025-09-01 15:17:51', 'auto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (50, 82, 'inscripcion', 426, 50.00, '2025-09-02 11:25:54', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (51, 106, 'inscripcion', 427, 50.00, '2025-09-02 13:15:17', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (52, 106, 'inscripcion', 428, 50.00, '2025-09-02 13:41:15', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (53, 82, 'inscripcion', 429, 50.00, '2025-09-02 14:28:56', 'auto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (54, 99, 'inscripcion', 430, 50.00, '2025-09-02 17:58:10', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (55, 99, 'financiamiento', 312, 50.00, '2025-09-02 18:33:37', NULL, 'pagada', 'Comisión por financiamiento - Financiamiento Celular', 'S/.');
INSERT INTO `comisiones` VALUES (56, 108, 'inscripcion', 431, 30.00, '2025-09-03 17:59:02', 'moto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (58, 108, 'inscripcion', 432, 30.00, '2025-09-04 13:43:36', 'moto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (59, 108, 'inscripcion', 433, 30.00, '2025-09-05 15:55:59', 'moto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (60, 110, 'financiamiento', 321, 40.00, '2025-09-08 10:08:28', 'vehiculo', 'pagada', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 5)', '$');
INSERT INTO `comisiones` VALUES (61, 106, 'financiamiento', 334, 50.00, '2025-09-08 14:55:11', NULL, 'pagada', 'Comisión por financiamiento - Financiamiento Celular', 'S/.');
INSERT INTO `comisiones` VALUES (62, 106, 'financiamiento', 334, 50.00, '2025-09-08 14:56:15', NULL, 'pagada', 'Comisión por financiamiento - Financiamiento Celular', 'S/.');
INSERT INTO `comisiones` VALUES (63, 79, 'financiamiento', 339, 50.00, '2025-09-08 17:20:02', 'vehiculo', 'pagada', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 6)', '$');
INSERT INTO `comisiones` VALUES (64, 97, 'inscripcion', 434, 50.00, '2025-09-08 18:35:54', 'auto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (65, 82, 'financiamiento', 492, 50.00, '2025-09-10 11:22:44', 'vehiculo', 'pagada', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 6)', '$');
INSERT INTO `comisiones` VALUES (66, 82, 'financiamiento', 492, 50.00, '2025-09-10 11:25:12', 'vehiculo', 'pagada', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 6)', '$');
INSERT INTO `comisiones` VALUES (67, 105, 'inscripcion', 435, 50.00, '2025-09-10 19:05:33', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (68, 106, 'financiamiento', 497, 40.00, '2025-09-11 11:25:51', 'vehiculo', 'pagada', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 5)', '$');
INSERT INTO `comisiones` VALUES (69, 106, 'financiamiento', 498, 50.00, '2025-09-11 11:35:30', 'vehiculo', 'pagada', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 6)', '$');
INSERT INTO `comisiones` VALUES (70, 79, 'financiamiento', 499, 50.00, '2025-09-11 12:09:06', 'vehiculo', 'pagada', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 6)', '$');
INSERT INTO `comisiones` VALUES (71, 79, 'financiamiento', 500, 50.00, '2025-09-11 12:24:53', 'vehiculo', 'pagada', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 6)', '$');
INSERT INTO `comisiones` VALUES (74, 82, 'inscripcion', 436, 50.00, '2025-09-15 11:58:51', 'auto', 'pagada', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (76, 104, 'inscripcion', 437, 50.00, '2025-09-25 13:31:19', 'auto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (77, 110, 'inscripcion', 438, 50.00, '2025-09-26 11:46:15', 'auto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (78, 110, 'financiamiento', 558, 50.00, '2025-09-26 14:41:55', 'vehiculo', 'pagada', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 6)', '$');
INSERT INTO `comisiones` VALUES (79, 110, 'financiamiento', 559, 50.00, '2025-09-26 14:48:57', 'vehiculo', 'pagada', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 6)', '$');
INSERT INTO `comisiones` VALUES (80, 97, 'inscripcion', 439, 50.00, '2025-09-29 17:48:32', 'auto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (81, 97, 'inscripcion', 440, 50.00, '2025-09-30 15:12:11', 'auto', 'pagada', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (82, 100, 'inscripcion', 441, 50.00, '2025-10-13 18:26:22', 'auto', 'pendiente', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (83, 110, 'inscripcion', 442, 50.00, '2025-10-14 13:00:26', 'auto', 'pendiente', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (84, 110, 'inscripcion', 443, 50.00, '2025-10-14 16:15:56', 'auto', 'pendiente', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `comisiones` VALUES (85, 110, 'inscripcion', 444, 50.00, '2025-10-14 16:22:40', 'auto', 'pendiente', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (87, 110, 'inscripcion', 446, 50.00, '2025-10-16 17:18:28', 'auto', 'pendiente', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `comisiones` VALUES (88, 79, 'financiamiento', 609, 40.00, '2025-10-30 12:18:38', 'vehiculo', 'pendiente', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 5)', '$');
INSERT INTO `comisiones` VALUES (89, 79, 'financiamiento', 610, 50.00, '2025-10-30 12:29:22', 'vehiculo', 'pendiente', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 6)', '$');
INSERT INTO `comisiones` VALUES (91, 97, 'financiamiento', 572, 50.00, '2025-10-01 12:30:00', 'vehiculo', 'pendiente', 'Comisión retroactiva - CrediGo Autos Grupo 4 - Variante $17,000', '$');
INSERT INTO `comisiones` VALUES (92, 82, 'financiamiento', 578, 30.00, '2025-10-09 14:35:00', 'vehiculo', 'pendiente', 'Comisión retroactiva - CrediGo Autos Grupo 4 - Variante $13,000', '$');
INSERT INTO `comisiones` VALUES (97, 105, 'financiamiento', 608, 15.00, '2025-10-28 18:12:00', NULL, 'pendiente', 'Comisión retroactiva - Aceites', 'S/.');
INSERT INTO `comisiones` VALUES (98, 82, 'financiamiento', 593, 15.00, '2025-10-20 11:56:00', NULL, 'pendiente', 'Comisión retroactiva - Baterías', 'S/.');
INSERT INTO `comisiones` VALUES (99, 82, 'financiamiento', 599, 15.00, '2025-10-22 10:37:00', NULL, 'pendiente', 'Comisión retroactiva - Baterías', 'S/.');
INSERT INTO `comisiones` VALUES (100, 82, 'financiamiento', 607, 15.00, '2025-10-28 12:13:00', NULL, 'pendiente', 'Comisión retroactiva - Baterías', 'S/.');
INSERT INTO `comisiones` VALUES (101, 105, 'financiamiento', 611, 15.00, '2025-10-30 15:30:00', NULL, 'pendiente', 'Comisión retroactiva - Baterías', 'S/.');
INSERT INTO `comisiones` VALUES (105, 102, 'inscripcion', 10, 30.00, '2025-10-02 16:08:43', NULL, 'pendiente', 'Comisión retroactiva - Ingreso de Cliente', 'S/.');
INSERT INTO `comisiones` VALUES (106, 105, 'inscripcion', 13, 30.00, '2025-10-07 17:28:35', NULL, 'pendiente', 'Comisión retroactiva - Ingreso de Cliente', 'S/.');
INSERT INTO `comisiones` VALUES (107, 82, 'inscripcion', 14, 30.00, '2025-10-08 11:44:20', NULL, 'pendiente', 'Comisión retroactiva - Ingreso de Cliente', 'S/.');
INSERT INTO `comisiones` VALUES (108, 105, 'inscripcion', 15, 30.00, '2025-10-10 10:00:17', NULL, 'pendiente', 'Comisión retroactiva - Ingreso de Cliente', 'S/.');
INSERT INTO `comisiones` VALUES (109, 105, 'inscripcion', 16, 30.00, '2025-10-10 10:10:53', NULL, 'pendiente', 'Comisión retroactiva - Ingreso de Cliente', 'S/.');
INSERT INTO `comisiones` VALUES (110, 106, 'inscripcion', 17, 30.00, '2025-10-10 11:17:02', NULL, 'pendiente', 'Comisión retroactiva - Ingreso de Cliente', 'S/.');
INSERT INTO `comisiones` VALUES (113, 82, 'financiamiento', 612, 40.00, '2025-10-31 16:08:59', 'vehiculo', 'pendiente', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 5)', '$');
INSERT INTO `comisiones` VALUES (115, 82, 'financiamiento', 613, 15.00, '2025-11-03 10:29:10', NULL, 'pendiente', 'Comisión por financiamiento', 'S/.');
INSERT INTO `comisiones` VALUES (118, 106, 'financiamiento', 621, 50.00, '2025-11-04 14:37:16', 'vehiculo', 'pendiente', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 6)', '$');
INSERT INTO `comisiones` VALUES (123, 106, 'financiamiento', 636, 15.00, '2025-11-10 11:55:31', NULL, 'pendiente', 'Comisión por financiamiento', 'S/.');
INSERT INTO `comisiones` VALUES (129, 105, 'financiamiento', 750, 50.00, '2025-12-30 18:09:00', 'vehiculo', 'pendiente', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$');
INSERT INTO `comisiones` VALUES (130, 105, 'financiamiento', 741, 15.00, '2025-12-19 16:56:00', NULL, 'pendiente', 'Comisión por financiamiento - Mantenimiento IncaMotors', 'S/.');
INSERT INTO `comisiones` VALUES (131, 105, 'financiamiento', 735, 50.00, '2025-12-17 20:15:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.');
INSERT INTO `comisiones` VALUES (132, 105, 'financiamiento', 734, 50.00, '2025-12-17 20:15:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.');
INSERT INTO `comisiones` VALUES (133, 105, 'financiamiento', 733, 15.00, '2025-12-17 19:33:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO LLANTAS', 'S/.');
INSERT INTO `comisiones` VALUES (134, 105, 'financiamiento', 729, 50.00, '2025-12-16 18:27:00', 'vehiculo', 'pendiente', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$');
INSERT INTO `comisiones` VALUES (135, 105, 'financiamiento', 727, 15.00, '2025-12-15 09:40:00', NULL, 'pendiente', 'Comisión por financiamiento - Mantenimiento IncaMotors', 'S/.');
INSERT INTO `comisiones` VALUES (136, 105, 'financiamiento', 719, 25.00, '2025-12-13 10:00:00', NULL, 'pendiente', 'Comisión por financiamiento - Credit Yango', '$');
INSERT INTO `comisiones` VALUES (137, 105, 'financiamiento', 706, 50.00, '2025-12-04 17:46:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.');
INSERT INTO `comisiones` VALUES (138, 105, 'financiamiento', 696, 25.00, '2025-11-27 17:32:00', NULL, 'pendiente', 'Comisión por financiamiento - Credit Yango', '$');
INSERT INTO `comisiones` VALUES (139, 105, 'financiamiento', 695, 25.00, '2025-11-26 17:20:00', NULL, 'pendiente', 'Comisión por financiamiento - Credit Yango', '$');
INSERT INTO `comisiones` VALUES (140, 105, 'financiamiento', 694, 40.00, '2025-11-26 15:48:00', 'vehiculo', 'pendiente', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$');
INSERT INTO `comisiones` VALUES (141, 105, 'financiamiento', 680, 15.00, '2025-11-19 19:35:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO LLANTAS', 'S/.');
INSERT INTO `comisiones` VALUES (142, 105, 'financiamiento', 677, 15.00, '2025-11-19 16:11:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO BATERIAS', 'S/.');
INSERT INTO `comisiones` VALUES (143, 105, 'financiamiento', 674, 50.00, '2025-11-18 18:56:00', 'vehiculo', 'pendiente', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$');
INSERT INTO `comisiones` VALUES (144, 105, 'financiamiento', 664, 15.00, '2025-11-13 21:47:00', NULL, 'pendiente', 'Comisión por financiamiento - Mantenimiento IncaMotors', 'S/.');
INSERT INTO `comisiones` VALUES (145, 105, 'financiamiento', 663, 15.00, '2025-11-13 21:16:00', NULL, 'pendiente', 'Comisión por financiamiento - Mantenimiento IncaMotors', 'S/.');
INSERT INTO `comisiones` VALUES (146, 105, 'financiamiento', 631, 25.00, '2025-11-08 14:57:00', NULL, 'pendiente', 'Comisión por financiamiento - Credit Yango', '$');
INSERT INTO `comisiones` VALUES (147, 82, 'financiamiento', 744, 50.00, '2025-12-24 12:08:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.');
INSERT INTO `comisiones` VALUES (148, 82, 'financiamiento', 737, 50.00, '2025-12-18 14:48:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.');
INSERT INTO `comisiones` VALUES (149, 82, 'financiamiento', 731, 50.00, '2025-12-17 12:05:00', 'vehiculo', 'pendiente', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$');
INSERT INTO `comisiones` VALUES (150, 82, 'financiamiento', 711, 15.00, '2025-12-11 13:49:00', NULL, 'pendiente', 'Comisión por financiamiento - Mantenimiento IncaMotors', 'S/.');
INSERT INTO `comisiones` VALUES (151, 82, 'financiamiento', 707, 50.00, '2025-12-05 12:22:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.');
INSERT INTO `comisiones` VALUES (152, 82, 'financiamiento', 704, 50.00, '2025-12-04 16:20:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.');
INSERT INTO `comisiones` VALUES (153, 82, 'financiamiento', 676, 50.00, '2025-11-19 11:47:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.');
INSERT INTO `comisiones` VALUES (154, 82, 'financiamiento', 668, 15.00, '2025-11-18 14:45:00', NULL, 'pendiente', 'Comisión por financiamiento - Mantenimiento IncaMotors', 'S/.');
INSERT INTO `comisiones` VALUES (155, 82, 'financiamiento', 648, 25.00, '2025-11-10 15:48:00', NULL, 'pendiente', 'Comisión por financiamiento - Credit Yango', '$');
INSERT INTO `comisiones` VALUES (156, 82, 'financiamiento', 646, 25.00, '2025-11-10 15:10:00', NULL, 'pendiente', 'Comisión por financiamiento - Credit Yango', '$');
INSERT INTO `comisiones` VALUES (157, 82, 'financiamiento', 633, 25.00, '2025-11-10 10:54:00', NULL, 'pendiente', 'Comisión por financiamiento - Credit Yango', '$');
INSERT INTO `comisiones` VALUES (158, 106, 'financiamiento', 745, 15.00, '2025-12-27 11:07:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO BATERIAS', 'S/.');
INSERT INTO `comisiones` VALUES (159, 106, 'financiamiento', 743, 15.00, '2025-12-22 13:58:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO BATERIAS', 'S/.');
INSERT INTO `comisiones` VALUES (160, 106, 'financiamiento', 697, 15.00, '2025-11-28 14:04:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO LLANTAS', 'S/.');
INSERT INTO `comisiones` VALUES (161, 106, 'financiamiento', 636, 15.00, '2025-11-10 11:41:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO BATERIAS', 'S/.');
INSERT INTO `comisiones` VALUES (162, 106, 'financiamiento', 627, 50.00, '2025-11-07 14:10:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.');
INSERT INTO `comisiones` VALUES (163, 106, 'financiamiento', 647, 25.00, '2025-11-10 15:54:00', NULL, 'pendiente', 'Comisión por financiamiento - Credit Yango', '$');
INSERT INTO `comisiones` VALUES (164, 106, 'financiamiento', 634, 25.00, '2025-11-10 10:52:00', NULL, 'pendiente', 'Comisión por financiamiento - Credit Yango', '$');
INSERT INTO `comisiones` VALUES (165, 106, 'financiamiento', 621, 50.00, '2025-11-04 14:25:00', 'vehiculo', 'pendiente', 'Comisión por financiamiento - CrediGo auto (Grupo 3)', '$');
INSERT INTO `comisiones` VALUES (166, 104, 'financiamiento', 730, 50.00, '2025-12-17 11:03:00', 'vehiculo', 'pendiente', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$');
INSERT INTO `comisiones` VALUES (167, 104, 'financiamiento', 728, 50.00, '2025-12-16 10:13:00', 'vehiculo', 'pendiente', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$');
INSERT INTO `comisiones` VALUES (168, 104, 'financiamiento', 718, 50.00, '2025-12-13 11:32:00', 'vehiculo', 'pendiente', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$');
INSERT INTO `comisiones` VALUES (169, 104, 'financiamiento', 717, 50.00, '2025-12-13 10:42:00', 'vehiculo', 'pendiente', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$');
INSERT INTO `comisiones` VALUES (170, 92, 'financiamiento', 639, 25.00, '2025-11-10 13:42:00', NULL, 'pendiente', 'Comisión por financiamiento - Credit Yango', '$');
INSERT INTO `comisiones` VALUES (171, 92, 'financiamiento', 640, 25.00, '2025-11-10 13:42:00', NULL, 'pendiente', 'Comisión por financiamiento - Credit Yango', '$');
INSERT INTO `comisiones` VALUES (174, 82, 'financiamiento', 613, 15.00, '2025-11-03 10:22:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO ACEITE', 'S/.');
INSERT INTO `comisiones` VALUES (175, 85, 'financiamiento', 619, 50.00, '2025-11-04 12:03:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.');
INSERT INTO `comisiones` VALUES (176, 103, 'financiamiento', 673, 25.00, '2025-11-18 16:59:00', NULL, 'pendiente', 'Comisión por financiamiento - Credit Yango', '$');
INSERT INTO `comisiones` VALUES (177, 111, 'financiamiento', 685, 50.00, '2025-11-22 13:41:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.');
INSERT INTO `comisiones` VALUES (178, 125, 'financiamiento', 736, 50.00, '2025-12-18 10:49:00', NULL, 'pendiente', 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.');

SET FOREIGN_KEY_CHECKS = 1;
