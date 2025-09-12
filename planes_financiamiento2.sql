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

 Date: 26/08/2025 14:41:23
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for planes_financiamiento
-- ----------------------------
DROP TABLE IF EXISTS `planes_financiamiento`;
CREATE TABLE `planes_financiamiento`  (
  `idplan_financiamiento` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre_plan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cuota_inicial` decimal(10, 2) NULL DEFAULT NULL,
  `monto_cuota` decimal(10, 2) NULL DEFAULT NULL,
  `cantidad_cuotas` decimal(10, 2) NULL DEFAULT NULL,
  `penalizacion_mora` decimal(10, 2) NULL DEFAULT NULL,
  `frecuencia_pago` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `moneda` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `tasa_interes` decimal(10, 2) NULL DEFAULT NULL,
  `monto` decimal(10, 2) NULL DEFAULT NULL,
  `monto_sin_interes` decimal(10, 2) NULL DEFAULT NULL,
  `fecha_inicio` date NULL DEFAULT NULL,
  `fecha_fin` date NULL DEFAULT NULL,
  `tipo_vehicular` enum('moto','vehiculo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`idplan_financiamiento`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 36 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of planes_financiamiento
-- ----------------------------
INSERT INTO `planes_financiamiento` VALUES (2, 'Redmi 14', 300.00, 100.00, 6.00, NULL, 'mensual', 'S/.', 0.00, 900.00, 900.00, NULL, NULL, NULL);
INSERT INTO `planes_financiamiento` VALUES (3, 'Redmi 14 Pro', 400.00, 130.00, 6.00, NULL, 'mensual', 'S/.', 0.00, 1180.00, 1180.00, NULL, NULL, NULL);
INSERT INTO `planes_financiamiento` VALUES (4, 'Redmi 14 Pro 5G', 500.00, 150.00, 6.00, NULL, 'mensual', 'S/.', 0.00, 1400.00, 1400.00, NULL, NULL, NULL);
INSERT INTO `planes_financiamiento` VALUES (9, 'Financiamiento Vehicular Grupo 1', 0.00, 100.00, 156.00, NULL, 'semanal', '$', 0.00, 15600.00, 14000.00, '2024-04-08', '2027-04-06', NULL);
INSERT INTO `planes_financiamiento` VALUES (12, 'Financiamiento Vehicular Grupo 2', 0.00, 100.00, 160.00, NULL, 'semanal', '$', 0.00, 16000.00, 14000.00, '2024-09-09', '2027-10-05', NULL);
INSERT INTO `planes_financiamiento` VALUES (14, 'FINANCIAMIENTO LLANTAS', 0.00, 0.00, 4.00, NULL, 'semanal', 'S/.', 10.00, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `planes_financiamiento` VALUES (15, 'FINANCIAMIENTO ACEITE', 0.00, 0.00, 4.00, NULL, 'semanal', 'S/.', 10.00, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `planes_financiamiento` VALUES (16, 'FINANCIAMIENTO BATERIAS', 0.00, 0.00, 4.00, NULL, 'semanal', 'S/.', 10.00, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `planes_financiamiento` VALUES (19, 'CrediGo auto (Grupo 3)', 0.00, 0.00, 210.00, NULL, 'semanal', '$', 0.00, NULL, NULL, '2025-06-16', '2029-06-26', 'vehiculo');
INSERT INTO `planes_financiamiento` VALUES (22, 'CrediGo Motos (Grupo 1)', 0.00, 150.00, 52.00, NULL, 'semanal', 'S/.', 0.00, 7800.00, 6500.00, '2025-07-21', '2026-07-21', 'moto');
INSERT INTO `planes_financiamiento` VALUES (33, 'MotosYa', 2000.00, 0.00, 52.00, NULL, 'semanal', 'S/.', 0.00, NULL, NULL, '2025-07-07', '2026-06-29', 'moto');
INSERT INTO `planes_financiamiento` VALUES (35, 'CHIP CLARO', 0.00, 2.92, 12.00, NULL, 'mensual', 'S/.', 0.00, 35.00, 35.00, NULL, NULL, NULL);

SET FOREIGN_KEY_CHECKS = 1;
