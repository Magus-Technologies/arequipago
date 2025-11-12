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

 Date: 07/11/2025 13:03:03
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for caracteristicas_producto
-- ----------------------------
DROP TABLE IF EXISTS `caracteristicas_producto`;
CREATE TABLE `caracteristicas_producto`  (
  `idcaracteristica` int NOT NULL AUTO_INCREMENT,
  `idproductosv2` int NOT NULL,
  `nombre_caracteristicas` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `valor_caracteristica` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`idcaracteristica`) USING BTREE,
  INDEX `fk_caracteristicas_productos`(`idproductosv2` ASC) USING BTREE,
  CONSTRAINT `fk_caracteristicas_productos` FOREIGN KEY (`idproductosv2`) REFERENCES `productosv2` (`idproductosv2`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 514 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of caracteristicas_producto
-- ----------------------------
INSERT INTO `caracteristicas_producto` VALUES (1, 21, 'Perfil', 'R14');
INSERT INTO `caracteristicas_producto` VALUES (2, 21, 'Aro', '185/70 ');
INSERT INTO `caracteristicas_producto` VALUES (3, 22, 'Perfil', '65');
INSERT INTO `caracteristicas_producto` VALUES (4, 22, 'Aro', '14');
INSERT INTO `caracteristicas_producto` VALUES (5, 23, 'Perfil', '65');
INSERT INTO `caracteristicas_producto` VALUES (6, 23, 'Aro', '15');
INSERT INTO `caracteristicas_producto` VALUES (13, 26, 'chip_linea', 'Entel');
INSERT INTO `caracteristicas_producto` VALUES (14, 26, 'marca_equipo', 'Prueba');
INSERT INTO `caracteristicas_producto` VALUES (15, 26, 'modelo', 'modelo');
INSERT INTO `caracteristicas_producto` VALUES (16, 26, 'nro_imei', '16565454');
INSERT INTO `caracteristicas_producto` VALUES (17, 26, 'nro_serie', '52');
INSERT INTO `caracteristicas_producto` VALUES (18, 26, 'colorc', 'Azul');
INSERT INTO `caracteristicas_producto` VALUES (19, 26, 'cargador', 'Si');
INSERT INTO `caracteristicas_producto` VALUES (20, 26, 'cable_usb', 'Si');
INSERT INTO `caracteristicas_producto` VALUES (21, 26, 'manual_usuario', 'No');
INSERT INTO `caracteristicas_producto` VALUES (22, 26, 'estuche', 'Si');
INSERT INTO `caracteristicas_producto` VALUES (62, 33, 'chip_linea', NULL);
INSERT INTO `caracteristicas_producto` VALUES (63, 33, 'marca_equipo', 'Xiaomi');
INSERT INTO `caracteristicas_producto` VALUES (64, 33, 'modelo', 'Redmi Note 14 Pro');
INSERT INTO `caracteristicas_producto` VALUES (65, 33, 'nro_imei', '86498074204222');
INSERT INTO `caracteristicas_producto` VALUES (66, 33, 'nro_serie', '864908074204230');
INSERT INTO `caracteristicas_producto` VALUES (67, 33, 'colorc', 'Negro');
INSERT INTO `caracteristicas_producto` VALUES (68, 33, 'cargador', 'Si');
INSERT INTO `caracteristicas_producto` VALUES (69, 33, 'cable_usb', 'USB Tipo_C');
INSERT INTO `caracteristicas_producto` VALUES (70, 33, 'manual_usuario', 'Si');
INSERT INTO `caracteristicas_producto` VALUES (71, 33, 'estuche', 'Si');
INSERT INTO `caracteristicas_producto` VALUES (132, 44, 'fecha_venc_soat', '2026-02-20');
INSERT INTO `caracteristicas_producto` VALUES (133, 44, 'fecha_venc_seguro', '2026-02-20');
INSERT INTO `caracteristicas_producto` VALUES (134, 44, 'chasis', 'JLY4G15RAUB0100582');
INSERT INTO `caracteristicas_producto` VALUES (135, 44, 'vin', 'LLV2C3A22S0202052');
INSERT INTO `caracteristicas_producto` VALUES (136, 44, 'color', 'BLANCO');
INSERT INTO `caracteristicas_producto` VALUES (137, 44, 'anio', '2025');
INSERT INTO `caracteristicas_producto` VALUES (138, 45, 'fecha_venc_soat', '2026-02-20');
INSERT INTO `caracteristicas_producto` VALUES (139, 45, 'fecha_venc_seguro', '2026-02-20');
INSERT INTO `caracteristicas_producto` VALUES (140, 45, 'chasis', 'JLY4G15RAUB0100582');
INSERT INTO `caracteristicas_producto` VALUES (141, 45, 'vin', 'LLV2C3A22S0202052');
INSERT INTO `caracteristicas_producto` VALUES (142, 45, 'color', 'BLANCO');
INSERT INTO `caracteristicas_producto` VALUES (143, 45, 'anio', '2025');
INSERT INTO `caracteristicas_producto` VALUES (162, 49, 'aro', 'R14');
INSERT INTO `caracteristicas_producto` VALUES (163, 49, 'perfil', '185/70');
INSERT INTO `caracteristicas_producto` VALUES (188, 54, 'fecha_venc_soat', '2025-09-27');
INSERT INTO `caracteristicas_producto` VALUES (189, 54, 'fecha_venc_seguro', '2025-09-20');
INSERT INTO `caracteristicas_producto` VALUES (190, 54, 'chasis', 'SQRE4G15CBCNJ00617');
INSERT INTO `caracteristicas_producto` VALUES (191, 54, 'vin', 'LVVDB11B5RD950805');
INSERT INTO `caracteristicas_producto` VALUES (192, 54, 'color', 'BLANCO');
INSERT INTO `caracteristicas_producto` VALUES (193, 54, 'anio', '2024');
INSERT INTO `caracteristicas_producto` VALUES (200, 56, 'fecha_venc_soat', '2025-11-07');
INSERT INTO `caracteristicas_producto` VALUES (201, 56, 'fecha_venc_seguro', '2025-11-04');
INSERT INTO `caracteristicas_producto` VALUES (202, 56, 'chasis', 'SQRDG15BYRC60218');
INSERT INTO `caracteristicas_producto` VALUES (203, 56, 'vin', 'LVVDB11B5SE003134');
INSERT INTO `caracteristicas_producto` VALUES (204, 56, 'color', 'PLATA CLARO');
INSERT INTO `caracteristicas_producto` VALUES (205, 56, 'anio', '2025');
INSERT INTO `caracteristicas_producto` VALUES (206, 57, 'fecha_venc_soat', '2026-01-10');
INSERT INTO `caracteristicas_producto` VALUES (207, 57, 'fecha_venc_seguro', '2025-12-31');
INSERT INTO `caracteristicas_producto` VALUES (208, 57, 'chasis', 'SQRE4G15CBCNK02342');
INSERT INTO `caracteristicas_producto` VALUES (209, 57, 'vin', 'LVVDB11BXRD950881');
INSERT INTO `caracteristicas_producto` VALUES (210, 57, 'color', 'ROJO');
INSERT INTO `caracteristicas_producto` VALUES (211, 57, 'anio', '2024');
INSERT INTO `caracteristicas_producto` VALUES (212, 58, 'fecha_venc_soat', '2025-11-20');
INSERT INTO `caracteristicas_producto` VALUES (213, 58, 'fecha_venc_seguro', '2025-11-13');
INSERT INTO `caracteristicas_producto` VALUES (214, 58, 'chasis', 'SQRE4G15CBCNJ00602');
INSERT INTO `caracteristicas_producto` VALUES (215, 58, 'vin', 'LVVDB11B0RD950808');
INSERT INTO `caracteristicas_producto` VALUES (216, 58, 'color', 'BLANCO');
INSERT INTO `caracteristicas_producto` VALUES (217, 58, 'anio', '2024');
INSERT INTO `caracteristicas_producto` VALUES (218, 59, 'fecha_venc_soat', '2025-11-22');
INSERT INTO `caracteristicas_producto` VALUES (219, 59, 'fecha_venc_seguro', '2025-11-11');
INSERT INTO `caracteristicas_producto` VALUES (220, 59, 'chasis', 'SQRD4G15BYRB60144');
INSERT INTO `caracteristicas_producto` VALUES (221, 59, 'vin', 'LVVDB11BXSE002044');
INSERT INTO `caracteristicas_producto` VALUES (222, 59, 'color', 'ROJO');
INSERT INTO `caracteristicas_producto` VALUES (223, 59, 'anio', '2025');
INSERT INTO `caracteristicas_producto` VALUES (270, 64, 'chip_linea', 'NO');
INSERT INTO `caracteristicas_producto` VALUES (271, 64, 'marca_equipo', 'REDMI');
INSERT INTO `caracteristicas_producto` VALUES (272, 64, 'modelo', 'REDMI NOTE 14 PRO 5G');
INSERT INTO `caracteristicas_producto` VALUES (273, 64, 'nro_imei', '866745071466984');
INSERT INTO `caracteristicas_producto` VALUES (274, 64, 'nro_serie', '866745071466984');
INSERT INTO `caracteristicas_producto` VALUES (275, 64, 'colorc', 'MIDNIGHT BLACK');
INSERT INTO `caracteristicas_producto` VALUES (276, 64, 'cargador', 'SI');
INSERT INTO `caracteristicas_producto` VALUES (277, 64, 'cargador', 'SI');
INSERT INTO `caracteristicas_producto` VALUES (278, 64, 'cable_usb', 'SI');
INSERT INTO `caracteristicas_producto` VALUES (279, 64, 'manual_usuario', 'SI');
INSERT INTO `caracteristicas_producto` VALUES (280, 64, 'estuche', 'SI');
INSERT INTO `caracteristicas_producto` VALUES (281, 66, 'fecha_venc_soat', '2026-04-04');
INSERT INTO `caracteristicas_producto` VALUES (282, 66, 'fecha_venc_seguro', '2026-04-04');
INSERT INTO `caracteristicas_producto` VALUES (283, 66, 'chasis', 'GW4G15K24498087701');
INSERT INTO `caracteristicas_producto` VALUES (284, 66, 'vin', 'LGWEE4A56TK601857');
INSERT INTO `caracteristicas_producto` VALUES (285, 66, 'color', 'BLANCO TITANIO');
INSERT INTO `caracteristicas_producto` VALUES (286, 66, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (287, 67, 'fecha_venc_soat', '2026-04-04');
INSERT INTO `caracteristicas_producto` VALUES (288, 67, 'fecha_venc_seguro', '2026-04-04');
INSERT INTO `caracteristicas_producto` VALUES (289, 67, 'chasis', 'GW4G15K24498087701');
INSERT INTO `caracteristicas_producto` VALUES (290, 67, 'vin', 'LGWEE4A56TK601857');
INSERT INTO `caracteristicas_producto` VALUES (291, 67, 'color', 'BLANCO TITANIO');
INSERT INTO `caracteristicas_producto` VALUES (292, 67, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (293, 68, 'fecha_venc_soat', '2026-04-04');
INSERT INTO `caracteristicas_producto` VALUES (294, 68, 'fecha_venc_seguro', '2026-04-04');
INSERT INTO `caracteristicas_producto` VALUES (295, 68, 'chasis', 'GW4G15K24498087701');
INSERT INTO `caracteristicas_producto` VALUES (296, 68, 'vin', 'LGWEE4A56TK601857');
INSERT INTO `caracteristicas_producto` VALUES (297, 68, 'color', 'BLANCO TITANIO');
INSERT INTO `caracteristicas_producto` VALUES (298, 68, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (299, 69, 'fecha_venc_soat', '2026-04-07');
INSERT INTO `caracteristicas_producto` VALUES (300, 69, 'fecha_venc_seguro', '2026-04-04');
INSERT INTO `caracteristicas_producto` VALUES (301, 69, 'chasis', 'GW4G15K24498087701');
INSERT INTO `caracteristicas_producto` VALUES (302, 69, 'vin', 'LGWEE4A56TK601857');
INSERT INTO `caracteristicas_producto` VALUES (303, 69, 'color', 'BLANCO TITANIO');
INSERT INTO `caracteristicas_producto` VALUES (304, 69, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (305, 70, 'fecha_venc_soat', '2026-04-07');
INSERT INTO `caracteristicas_producto` VALUES (306, 70, 'fecha_venc_seguro', '2026-04-04');
INSERT INTO `caracteristicas_producto` VALUES (307, 70, 'chasis', 'GW4G15K24498087701');
INSERT INTO `caracteristicas_producto` VALUES (308, 70, 'vin', 'LGWEE4A56TK601857');
INSERT INTO `caracteristicas_producto` VALUES (309, 70, 'color', 'BLANCO TITANIO');
INSERT INTO `caracteristicas_producto` VALUES (310, 70, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (311, 71, 'fecha_venc_soat', '2026-04-07');
INSERT INTO `caracteristicas_producto` VALUES (312, 71, 'fecha_venc_seguro', '2026-04-04');
INSERT INTO `caracteristicas_producto` VALUES (313, 71, 'chasis', 'GW4G15K24498087701');
INSERT INTO `caracteristicas_producto` VALUES (314, 71, 'vin', 'LGWEE4A56TK601857');
INSERT INTO `caracteristicas_producto` VALUES (315, 71, 'color', 'BLANCO ');
INSERT INTO `caracteristicas_producto` VALUES (316, 71, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (317, 72, 'fecha_venc_soat', '2026-04-07');
INSERT INTO `caracteristicas_producto` VALUES (318, 72, 'fecha_venc_seguro', '2026-04-04');
INSERT INTO `caracteristicas_producto` VALUES (319, 72, 'chasis', 'GW4G15K24498087701');
INSERT INTO `caracteristicas_producto` VALUES (320, 72, 'vin', 'LGWEE4A56TK601857');
INSERT INTO `caracteristicas_producto` VALUES (321, 72, 'color', 'BLANCO ');
INSERT INTO `caracteristicas_producto` VALUES (322, 72, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (323, 73, 'fecha_venc_soat', '2026-04-07');
INSERT INTO `caracteristicas_producto` VALUES (324, 73, 'fecha_venc_seguro', '2026-04-04');
INSERT INTO `caracteristicas_producto` VALUES (325, 73, 'chasis', 'GW4G15K24498087701');
INSERT INTO `caracteristicas_producto` VALUES (326, 73, 'vin', 'LGWEE4A56TK601857');
INSERT INTO `caracteristicas_producto` VALUES (327, 73, 'color', 'BLANCO ');
INSERT INTO `caracteristicas_producto` VALUES (328, 73, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (329, 74, 'fecha_venc_soat', '2026-04-07');
INSERT INTO `caracteristicas_producto` VALUES (330, 74, 'fecha_venc_seguro', '2026-04-04');
INSERT INTO `caracteristicas_producto` VALUES (331, 74, 'chasis', 'GW4G15K24498087701');
INSERT INTO `caracteristicas_producto` VALUES (332, 74, 'vin', 'LGWEE4A56TK601857');
INSERT INTO `caracteristicas_producto` VALUES (333, 74, 'color', 'BLANCO ');
INSERT INTO `caracteristicas_producto` VALUES (334, 74, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (335, 75, 'fecha_venc_soat', '2026-04-07');
INSERT INTO `caracteristicas_producto` VALUES (336, 75, 'fecha_venc_seguro', '2026-04-04');
INSERT INTO `caracteristicas_producto` VALUES (337, 75, 'chasis', 'GW4G15K24498087701');
INSERT INTO `caracteristicas_producto` VALUES (338, 75, 'vin', 'LGWEE4A56TK601857');
INSERT INTO `caracteristicas_producto` VALUES (339, 75, 'color', 'BLANCO ');
INSERT INTO `caracteristicas_producto` VALUES (340, 75, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (341, 76, 'fecha_venc_soat', '2025-04-26');
INSERT INTO `caracteristicas_producto` VALUES (342, 76, 'fecha_venc_seguro', '2025-04-27');
INSERT INTO `caracteristicas_producto` VALUES (343, 76, 'chasis', '07499309');
INSERT INTO `caracteristicas_producto` VALUES (344, 76, 'vin', '0809174072');
INSERT INTO `caracteristicas_producto` VALUES (345, 76, 'color', 'Blanco');
INSERT INTO `caracteristicas_producto` VALUES (346, 76, 'anio', '2000');
INSERT INTO `caracteristicas_producto` VALUES (347, 77, 'fecha_venc_soat', '2025-04-17');
INSERT INTO `caracteristicas_producto` VALUES (348, 77, 'fecha_venc_seguro', '2025-04-03');
INSERT INTO `caracteristicas_producto` VALUES (349, 77, 'chasis', '1654545');
INSERT INTO `caracteristicas_producto` VALUES (350, 77, 'vin', '5545456');
INSERT INTO `caracteristicas_producto` VALUES (351, 77, 'color', 'Red');
INSERT INTO `caracteristicas_producto` VALUES (352, 77, 'anio', '2000');
INSERT INTO `caracteristicas_producto` VALUES (373, 82, 'Aro', '60');
INSERT INTO `caracteristicas_producto` VALUES (374, 82, 'Perfil', '10');
INSERT INTO `caracteristicas_producto` VALUES (375, 82, 'Aro', '60');
INSERT INTO `caracteristicas_producto` VALUES (376, 82, 'Perfil', '10');
INSERT INTO `caracteristicas_producto` VALUES (379, 164, 'aro', 'R14');
INSERT INTO `caracteristicas_producto` VALUES (380, 164, 'perfil', '185/70');
INSERT INTO `caracteristicas_producto` VALUES (381, 168, 'fecha_venc_soat', '2026-04-04');
INSERT INTO `caracteristicas_producto` VALUES (382, 168, 'fecha_venc_seguro', '2026-04-04');
INSERT INTO `caracteristicas_producto` VALUES (383, 168, 'chasis', 'GW4G15K24498087701');
INSERT INTO `caracteristicas_producto` VALUES (384, 168, 'vin', 'LGWEE4A56TK601857');
INSERT INTO `caracteristicas_producto` VALUES (385, 168, 'color', 'BLANCO TITANIO');
INSERT INTO `caracteristicas_producto` VALUES (386, 168, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (387, 169, 'fecha_venc_soat', '2026-04-25');
INSERT INTO `caracteristicas_producto` VALUES (388, 169, 'fecha_venc_seguro', '2026-04-25');
INSERT INTO `caracteristicas_producto` VALUES (389, 169, 'chasis', 'G4NLRW698658');
INSERT INTO `caracteristicas_producto` VALUES (390, 169, 'vin', 'MALPC815ATM907476');
INSERT INTO `caracteristicas_producto` VALUES (391, 169, 'color', 'ROJO');
INSERT INTO `caracteristicas_producto` VALUES (392, 169, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (393, 170, 'fecha_venc_soat', '2026-04-26');
INSERT INTO `caracteristicas_producto` VALUES (394, 170, 'fecha_venc_seguro', '2026-04-26');
INSERT INTO `caracteristicas_producto` VALUES (395, 170, 'chasis', 'G4NLRW698658');
INSERT INTO `caracteristicas_producto` VALUES (396, 170, 'vin', 'MALPC815ATM907476');
INSERT INTO `caracteristicas_producto` VALUES (397, 170, 'color', 'ROJO ');
INSERT INTO `caracteristicas_producto` VALUES (398, 170, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (399, 171, 'aro', 'R14');
INSERT INTO `caracteristicas_producto` VALUES (400, 171, 'perfil', '175/65');
INSERT INTO `caracteristicas_producto` VALUES (401, 172, 'aro', 'R14');
INSERT INTO `caracteristicas_producto` VALUES (402, 172, 'perfil', '175/70');
INSERT INTO `caracteristicas_producto` VALUES (409, 179, 'aro', 'R14');
INSERT INTO `caracteristicas_producto` VALUES (410, 179, 'perfil', '185/70');
INSERT INTO `caracteristicas_producto` VALUES (411, 181, 'aro', '14');
INSERT INTO `caracteristicas_producto` VALUES (412, 181, 'perfil', '165');
INSERT INTO `caracteristicas_producto` VALUES (413, 193, 'aro', 'R14');
INSERT INTO `caracteristicas_producto` VALUES (414, 193, 'perfil', '185/65');
INSERT INTO `caracteristicas_producto` VALUES (415, 204, 'aro', 'R14');
INSERT INTO `caracteristicas_producto` VALUES (416, 204, 'perfil', '185/70');
INSERT INTO `caracteristicas_producto` VALUES (417, 208, 'aro', 'R15');
INSERT INTO `caracteristicas_producto` VALUES (418, 208, 'perfil', '195/65');
INSERT INTO `caracteristicas_producto` VALUES (419, 210, 'aro', 'R14');
INSERT INTO `caracteristicas_producto` VALUES (420, 210, 'perfil', '175/70');
INSERT INTO `caracteristicas_producto` VALUES (421, 216, 'aro', 'R16');
INSERT INTO `caracteristicas_producto` VALUES (422, 216, 'perfil', '205/50');
INSERT INTO `caracteristicas_producto` VALUES (423, 242, 'plan_mensual', '35');
INSERT INTO `caracteristicas_producto` VALUES (424, 242, 'operadora', 'CLARO');
INSERT INTO `caracteristicas_producto` VALUES (425, 243, 'aro', '14');
INSERT INTO `caracteristicas_producto` VALUES (426, 243, 'perfil', '185/65');
INSERT INTO `caracteristicas_producto` VALUES (427, 253, 'aro', 'R13');
INSERT INTO `caracteristicas_producto` VALUES (428, 253, 'perfil', '175/70');
INSERT INTO `caracteristicas_producto` VALUES (429, 254, 'aro', 'R13');
INSERT INTO `caracteristicas_producto` VALUES (430, 254, 'perfil', '175/70');
INSERT INTO `caracteristicas_producto` VALUES (431, 256, 'aro', 'R14');
INSERT INTO `caracteristicas_producto` VALUES (432, 256, 'perfil', '165/65');
INSERT INTO `caracteristicas_producto` VALUES (433, 257, 'aro', 'R14');
INSERT INTO `caracteristicas_producto` VALUES (434, 257, 'perfil', '175/70');
INSERT INTO `caracteristicas_producto` VALUES (435, 262, 'aro', '14');
INSERT INTO `caracteristicas_producto` VALUES (436, 262, 'perfil', '165/65');
INSERT INTO `caracteristicas_producto` VALUES (437, 263, 'aro', 'R14');
INSERT INTO `caracteristicas_producto` VALUES (438, 263, 'perfil', '175/65');
INSERT INTO `caracteristicas_producto` VALUES (439, 275, 'fecha_venc_soat', '2025-09-10');
INSERT INTO `caracteristicas_producto` VALUES (440, 275, 'fecha_venc_seguro', '2025-09-02');
INSERT INTO `caracteristicas_producto` VALUES (451, 48, 'Color', 'rojo');
INSERT INTO `caracteristicas_producto` VALUES (452, 48, 'Año del vehículo', '2025');
INSERT INTO `caracteristicas_producto` VALUES (453, 295, 'fecha_venc_soat', '2025-09-23');
INSERT INTO `caracteristicas_producto` VALUES (454, 295, 'fecha_venc_seguro', '2026-09-18');
INSERT INTO `caracteristicas_producto` VALUES (455, 295, 'chasis', 'G4FLSQ480722');
INSERT INTO `caracteristicas_producto` VALUES (456, 295, 'vin', 'MALGT41DATM168240');
INSERT INTO `caracteristicas_producto` VALUES (457, 295, 'color', 'ROJO');
INSERT INTO `caracteristicas_producto` VALUES (458, 295, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (459, 295, 'placa', 'VCY-643');
INSERT INTO `caracteristicas_producto` VALUES (460, 295, 'transmision', 'Manual');
INSERT INTO `caracteristicas_producto` VALUES (461, 296, 'fecha_venc_soat', '2026-06-27');
INSERT INTO `caracteristicas_producto` VALUES (462, 296, 'fecha_venc_seguro', '2026-06-27');
INSERT INTO `caracteristicas_producto` VALUES (463, 296, 'chasis', 'JL474QAKSEQA006284');
INSERT INTO `caracteristicas_producto` VALUES (464, 296, 'vin', 'LS4ASL2E6TG800226');
INSERT INTO `caracteristicas_producto` VALUES (465, 296, 'color', 'PLATA');
INSERT INTO `caracteristicas_producto` VALUES (466, 296, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (467, 296, 'placa', 'VCT-331');
INSERT INTO `caracteristicas_producto` VALUES (468, 296, 'transmision', 'Manual');
INSERT INTO `caracteristicas_producto` VALUES (469, 297, 'fecha_venc_soat', '2026-10-03');
INSERT INTO `caracteristicas_producto` VALUES (470, 297, 'fecha_venc_seguro', '2026-09-23');
INSERT INTO `caracteristicas_producto` VALUES (471, 297, 'chasis', 'BHE15AFDS5GA0818580');
INSERT INTO `caracteristicas_producto` VALUES (472, 297, 'vin', 'LB3SX2042TX802232');
INSERT INTO `caracteristicas_producto` VALUES (473, 297, 'color', 'PLATA');
INSERT INTO `caracteristicas_producto` VALUES (474, 297, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (475, 299, 'fecha_venc_soat', '2026-10-20');
INSERT INTO `caracteristicas_producto` VALUES (476, 299, 'fecha_venc_seguro', '2026-11-06');
INSERT INTO `caracteristicas_producto` VALUES (477, 299, 'chasis', 'G4LCS1024313');
INSERT INTO `caracteristicas_producto` VALUES (478, 299, 'vin', 'LID0AA29AT0341915');
INSERT INTO `caracteristicas_producto` VALUES (479, 299, 'color', 'BLANCO CLARO');
INSERT INTO `caracteristicas_producto` VALUES (480, 299, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (481, 299, 'placa', 'VDB-452');
INSERT INTO `caracteristicas_producto` VALUES (482, 299, 'transmision', 'Manual');
INSERT INTO `caracteristicas_producto` VALUES (483, 300, 'fecha_venc_soat', '2026-11-06');
INSERT INTO `caracteristicas_producto` VALUES (484, 300, 'fecha_venc_seguro', '2026-11-06');
INSERT INTO `caracteristicas_producto` VALUES (485, 300, 'chasis', 'G4LCS1024285');
INSERT INTO `caracteristicas_producto` VALUES (486, 300, 'vin', 'LID0AA29AT0341919');
INSERT INTO `caracteristicas_producto` VALUES (487, 300, 'color', 'BLANCO CLARO');
INSERT INTO `caracteristicas_producto` VALUES (488, 300, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (489, 300, 'placa', 'VDC-009');
INSERT INTO `caracteristicas_producto` VALUES (490, 301, 'fecha_venc_soat', '2026-11-06');
INSERT INTO `caracteristicas_producto` VALUES (491, 301, 'fecha_venc_seguro', '2026-11-06');
INSERT INTO `caracteristicas_producto` VALUES (492, 301, 'chasis', 'G4LCSE602780');
INSERT INTO `caracteristicas_producto` VALUES (493, 301, 'vin', '3KPFB41GATE127361');
INSERT INTO `caracteristicas_producto` VALUES (494, 301, 'color', 'BLANCO NIEVE');
INSERT INTO `caracteristicas_producto` VALUES (495, 301, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (496, 301, 'placa', 'VDC-074');
INSERT INTO `caracteristicas_producto` VALUES (497, 301, 'transmision', 'Manual');
INSERT INTO `caracteristicas_producto` VALUES (498, 302, 'fecha_venc_soat', '2026-11-06');
INSERT INTO `caracteristicas_producto` VALUES (499, 302, 'fecha_venc_seguro', '2026-11-06');
INSERT INTO `caracteristicas_producto` VALUES (500, 302, 'chasis', 'BHE15AFDS4GA1007339');
INSERT INTO `caracteristicas_producto` VALUES (501, 302, 'vin', 'LB3F31049TG012370');
INSERT INTO `caracteristicas_producto` VALUES (502, 302, 'color', 'BLANCO');
INSERT INTO `caracteristicas_producto` VALUES (503, 302, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (504, 302, 'placa', 'VDC-178');
INSERT INTO `caracteristicas_producto` VALUES (505, 302, 'transmision', 'Manual');
INSERT INTO `caracteristicas_producto` VALUES (506, 303, 'fecha_venc_soat', '2026-10-09');
INSERT INTO `caracteristicas_producto` VALUES (507, 303, 'fecha_venc_seguro', '2026-10-07');
INSERT INTO `caracteristicas_producto` VALUES (508, 303, 'chasis', 'JL473ZQ9SM0AD004178');
INSERT INTO `caracteristicas_producto` VALUES (509, 303, 'vin', 'LS5A3DKR2TA961208');
INSERT INTO `caracteristicas_producto` VALUES (510, 303, 'color', 'BLANCO');
INSERT INTO `caracteristicas_producto` VALUES (511, 303, 'anio', '2026');
INSERT INTO `caracteristicas_producto` VALUES (512, 303, 'placa', 'VCZ-422');
INSERT INTO `caracteristicas_producto` VALUES (513, 303, 'transmision', 'Automático');

SET FOREIGN_KEY_CHECKS = 1;
