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

 Date: 06/11/2025 13:56:13
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for beneficios
-- ----------------------------
DROP TABLE IF EXISTS `beneficios`;
CREATE TABLE `beneficios`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `plan_financiamiento_id` int NOT NULL,
  `categoria` int NULL DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `cuota_inicial` decimal(10, 2) NOT NULL,
  `cantidad_cuotas` int NOT NULL,
  `cuota_mensual` decimal(10, 2) NOT NULL,
  `moneda` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'Moneda personalizada para plan editable (S/. o $)',
  `nombre_plan_personalizado` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'Nombre/título personalizado para plan editable',
  `frecuencia_pago` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'Frecuencia de pago para plan editable (semanal, mensual)',
  `imagen` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `disponible` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `departamento_id` int NULL DEFAULT NULL COMMENT 'ID del departamento - NULL = disponible en todos',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_plan_financiamiento`(`plan_financiamiento_id` ASC) USING BTREE,
  INDEX `idx_categoria`(`categoria` ASC) USING BTREE,
  INDEX `idx_disponible`(`disponible` ASC) USING BTREE,
  INDEX `idx_nombre`(`nombre` ASC) USING BTREE,
  INDEX `idx_plan_disponible`(`plan_financiamiento_id` ASC, `disponible` ASC) USING BTREE,
  INDEX `idx_departamento`(`departamento_id` ASC) USING BTREE,
  CONSTRAINT `fk_beneficios_departamento` FOREIGN KEY (`departamento_id`) REFERENCES `depast` (`iddepast`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 30 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of beneficios
-- ----------------------------
INSERT INTO `beneficios` VALUES (1, 'CASTROL', 15, 0, 'Aprovecha el precio incluye S/ 20 de descuentos para instalación.\r\n✅ Precio al contado.\r\n✅ También disponible con financiamiento, con un 10% adicional.\r\n✅ La inicial del 50%\r\n Ideal para mantener tu vehículo en óptimas condiciones al mejor precio. 🚗✨', 120.61, 3, 40.20, 'S/.', NULL, NULL, 'img/beneficios/beneficio_1758140440_68cb1818ab92f.png', 1, '2025-09-17 15:20:40', '2025-10-29 15:28:45', 8);
INSERT INTO `beneficios` VALUES (4, 'CEPSA', 15, 0, 'Aprovecha el precio incluye S/ 20 de descuentos para instalación.\r\n✅ Precio al contado.\r\n✅ También disponible con financiamiento, con un 10% adicional.\r\n✅ La inicial del 50%\r\n Ideal para mantener tu vehículo en óptimas condiciones al mejor precio. 🚗✨', 78.87, 2, 39.43, 'S/.', NULL, NULL, 'img/beneficios/beneficio_1761091537_68f81fd127cef.png', 1, '2025-09-17 15:47:32', '2025-10-29 15:28:26', 8);
INSERT INTO `beneficios` VALUES (6, 'REDMI NOTE 14', 2, 0, 'Ahora puedes acceder al nuevo Redmi Note 14, un smartphone potente, confiable y perfecto para trabajar con el aplicativo sin interrupciones. 🚗💨\r\n💡 No dejes pasar esta oportunidad. Renueva tu celular y sigue creciendo con nosotros.', 200.00, 7, 100.00, NULL, NULL, NULL, 'img/beneficios/beneficio_1761231672_68fa43388fc5f.png', 1, '2025-09-28 19:13:25', '2025-10-23 10:01:12', NULL);
INSERT INTO `beneficios` VALUES (7, 'REDMI NOTE 14 PRO', 3, 0, 'Pensamos en el crecimiento de nuestros clientes, por eso ofrecemos el nuevo Redmi Note 14 pro, un equipo moderno y potente para que trabajes con total confianza. 🚗💨\r\n\r\n✅ Cuotas semanales accesibles\r\n\r\n💡 Aprovecha esta oportunidad y lleva tu celular.', 300.00, 7, 130.00, NULL, NULL, NULL, 'img/beneficios/beneficio_1761231708_68fa435c0c0d7.png', 1, '2025-09-28 19:22:05', '2025-10-23 10:01:48', NULL);
INSERT INTO `beneficios` VALUES (8, 'REDMI NOTE 14 PRO 5G', 4, 0, 'Pensamos en el crecimiento de nuestros clientes, por eso ofrecemos el nuevo Redmi Note 14 pro 5G, un equipo moderno y potente para que trabajes con total confianza. 🚗💨 ✅ Cuotas semanales accesibles 💡 Aprovecha esta oportunidad y lleva tu celular.', 400.00, 7, 150.00, NULL, NULL, NULL, 'img/beneficios/beneficio_1761231832_68fa43d80a299.png', 1, '2025-09-28 19:23:10', '2025-10-23 10:03:52', NULL);
INSERT INTO `beneficios` VALUES (9, 'HYUNDAI', 15, 0, 'Aprovecha el precio incluye S/ 20 de descuentos para instalación.\r\n✅ Precio al contado.\r\n✅ También disponible con financiamiento, con un 10% adicional.\r\n✅ La inicial del 50%\r\n Ideal para mantener tu vehículo en óptimas condiciones al mejor precio. 🚗✨', 72.43, 2, 36.21, 'S/.', NULL, NULL, 'img/beneficios/beneficio_1759105935_68d9d38fef9d3.png', 1, '2025-09-28 19:32:15', '2025-10-29 15:26:55', 8);
INSERT INTO `beneficios` VALUES (10, 'LIQUI MOLY', 15, 0, 'Aprovecha el precio incluye S/ 20 de descuentos para instalación.\r\n✅ Precio al contado.\r\n✅ También disponible con financiamiento, con un 10% adicional.\r\n✅ La inicial del 50%\r\n Ideal para mantener tu vehículo en óptimas condiciones al mejor precio. 🚗✨', 119.73, 3, 39.91, 'S/.', NULL, NULL, 'img/beneficios/beneficio_1759106055_68d9d4070e601.png', 1, '2025-09-28 19:34:15', '2025-10-29 15:26:29', 8);
INSERT INTO `beneficios` VALUES (11, 'Certificado $13 000', 38, NULL, '🚗✨ ¡Estrena tu carro más rápido! ✨🚗\r\n✔️ Pagar en cómodas cuotas semanales.\r\n✔️ Acceder a entrega inmediata adelantando 52 semanas.\r\n✔️ Oportunidad de adjudicación anticipada.\r\nNo esperes más para tener tu propio vehículo, ¡esta es tu oportunidad!\r\n📲 Escríbenos y te contamos cómo empezar hoy mismo.', 260.00, 215, 80.00, NULL, NULL, NULL, 'img/beneficios/beneficio_1759270278_68dc5586bf87c.png', 1, '2025-09-30 17:11:18', '2025-09-30 17:11:18', NULL);
INSERT INTO `beneficios` VALUES (12, 'CERTIFICADO $15 000', 38, 0, '🚗✨ ¡Estrena tu carro más rápido! ✨🚗\r\n✔️ Pagar en cómodas cuotas semanales.\r\n✔️ Acceder a entrega inmediata adelantando 52 semanas.\r\n✔️ Oportunidad de adjudicación anticipada.\r\nNo esperes más para tener tu propio vehículo, ¡esta es tu oportunidad!\r\n📲 Escríbenos y te contamos cómo empezar hoy mismo.', 300.00, 215, 90.00, NULL, NULL, NULL, 'img/beneficios/beneficio_1759270333_68dc55bd3f939.png', 1, '2025-09-30 17:12:13', '2025-09-30 17:14:15', NULL);
INSERT INTO `beneficios` VALUES (13, 'CERTIFICADO $17 000', 38, NULL, '🚗✨ ¡Estrena tu carro más rápido! ✨🚗\r\n✔️ Pagar en cómodas cuotas semanales.\r\n✔️ Acceder a entrega inmediata adelantando 52 semanas.\r\n✔️ Oportunidad de adjudicación anticipada.\r\nNo esperes más para tener tu propio vehículo, ¡esta es tu oportunidad!\r\n📲 Escríbenos y te contamos cómo empezar hoy mismo.', 340.00, 215, 100.00, NULL, NULL, NULL, 'img/beneficios/beneficio_1759270387_68dc55f3f0548.png', 1, '2025-09-30 17:13:07', '2025-09-30 17:13:07', NULL);
INSERT INTO `beneficios` VALUES (14, 'DUAL 200', 33, NULL, '🏍️ ¡Tu moto te espera con entrega inmediata! 🔥\r\n\r\nLlévate la Dual 200 con solo S/1500 de inicial y cuotas semanales de S/150 por 52 semanas.\r\n🚀 ¡Sin esperas, sin complicaciones, y lista para salir a rodar hoy mismo!\r\n\r\n📲 ¡Aprovecha esta oportunidad y súmate a los que ya manejan su independencia con MotosYa!', 1500.00, 52, 150.00, NULL, NULL, NULL, 'img/beneficios/beneficio_1761059913_68f7a4492f080.png', 1, '2025-10-21 10:18:33', '2025-10-21 10:18:33', NULL);
INSERT INTO `beneficios` VALUES (15, 'NK 250', 33, NULL, '🏍️ ¡Tu moto te espera con entrega inmediata! 🔥\r\n\r\nLlévate la NK 250 con solo S/1500 de inicial y cuotas semanales de S/210 por 52 semanas.\r\n🚀 ¡Sin esperas, sin complicaciones, y lista para salir a rodar hoy mismo!\r\n\r\n📲 ¡Aprovecha esta oportunidad y súmate a los que ya manejan su independencia con CrediGo Motos!', 1500.00, 52, 210.00, NULL, NULL, NULL, 'img/beneficios/beneficio_1761059988_68f7a4942f2d7.png', 1, '2025-10-21 10:19:48', '2025-10-21 10:19:48', NULL);
INSERT INTO `beneficios` VALUES (16, 'SR 250', 33, NULL, '🏍️ ¡Tu moto te espera con entrega inmediata! 🔥\r\n\r\nLlévate la SR 250 con solo S/1500 de inicial y cuotas semanales de S/240 por 52 semanas.\r\n🚀 ¡Sin esperas, sin complicaciones, y lista para salir a rodar hoy mismo!\r\n\r\n📲 ¡Aprovecha esta oportunidad y súmate a los que ya manejan su independencia con CrediGo Motos!', 1500.00, 52, 240.00, NULL, NULL, NULL, 'img/beneficios/beneficio_1761060050_68f7a4d27234c.png', 1, '2025-10-21 10:20:50', '2025-10-21 10:20:50', NULL);
INSERT INTO `beneficios` VALUES (17, 'CERTIFICADO CREDIMOTOS  S/4500', 22, 0, '🏍️ ¡Llévate tu moto con CrediMotos! 🔥\r\nOfrecemos un Certificado de CrediMotos con un valor de S/4500 por 58 semanas.\r\n💥 Pero si adelantas 15 semanas (S/1650), ¡te llevas tu moto de inmediato! 🙌\r\n\r\nTus cuotas quedan en solo S/110 semanales.\r\n🚀 ¡Fácil, rápido y con entrega asegurada!\r\n📲 Contáctanos y descubre lo sencillo que es tener tu moto con CrediGo.', 150.00, 58, 110.00, NULL, NULL, NULL, 'img/beneficios/beneficio_1761060459_68f7a66b1bbef.png', 1, '2025-10-21 10:27:39', '2025-10-21 23:00:59', NULL);
INSERT INTO `beneficios` VALUES (18, 'CERTIFICADO CREDIMOTOS S/5500', 22, 0, '🏍️ ¡Llévate tu moto con CrediMotos! 🔥\r\nOfrecemos un Certificado de CrediMotos con un valor de S/5500 por 58 semanas.\r\n💥 Pero si adelantas 15 semanas, ¡te llevas tu moto de inmediato! 🙌\r\nTus cuotas quedan en solo S/130 semanales.\r\n🚀 ¡Fácil, rápido y con entrega asegurada!\r\n📲 Contáctanos y descubre lo sencillo que es tener tu moto con CrediGo.', 150.00, 58, 130.00, NULL, NULL, NULL, 'img/beneficios/beneficio_1761060623_68f7a70f4a6e3.png', 1, '2025-10-21 10:30:23', '2025-10-21 23:00:43', NULL);
INSERT INTO `beneficios` VALUES (19, 'CERTIFICADO CREDIMOTOS S/6500', 22, 0, '🏍️ ¡Llévate tu moto con CrediMotos! 🔥\r\nOfrecemos un Certificado de CrediMotos con un valor de S/6500 por 58 semanas.\r\n💥 Pero si adelantas 15 semanas, ¡te llevas tu moto de inmediato! 🙌\r\nTus cuotas quedan en solo S/150 semanales.\r\n🚀 ¡Fácil, rápido y con entrega asegurada!\r\n📲 Contáctanos y descubre lo sencillo que es tener tu moto con CrediGo.', 150.00, 58, 150.00, NULL, NULL, NULL, 'img/beneficios/beneficio_1761060717_68f7a76dd7715.png', 1, '2025-10-21 10:31:57', '2025-10-21 23:00:10', NULL);
INSERT INTO `beneficios` VALUES (20, 'CERTIFICADO CREDIMOTOS S/7500', 22, NULL, '🏍️ ¡Llévate tu moto con CrediMotos! 🔥\r\nOfrecemos un Certificado de CrediMotos con un valor de S/7500 por 58 semanas.\r\n💥 Pero si adelantas 15 semanas, ¡te llevas tu moto de inmediato! 🙌\r\nTus cuotas quedan en solo S/175 semanales.\r\n🚀 ¡Fácil, rápido y con entrega asegurada!\r\n📲 Contáctanos y descubre lo sencillo que es tener tu moto con CrediGo.', 200.00, 58, 175.00, NULL, NULL, NULL, 'img/beneficios/beneficio_1761060790_68f7a7b6b972a.png', 1, '2025-10-21 10:33:10', '2025-10-21 10:33:10', NULL);
INSERT INTO `beneficios` VALUES (21, 'CERTIFICADO CREDIMOTOS S/8500', 22, 0, '🏍️ ¡Llévate tu moto con CrediMotos! 🔥\r\nOfrecemos un Certificado de CrediMotos con un valor de S/8500 por 58 semanas.\r\n💥 Pero si adelantas 15 semanas, ¡te llevas tu moto de inmediato! 🙌\r\nTus cuotas quedan en solo S/200 semanales.\r\n🚀 ¡Fácil, rápido y con entrega asegurada!\r\n📲 Contáctanos y descubre lo sencillo que es tener tu moto con CrediGo.', 200.00, 58, 200.00, 'S/.', NULL, NULL, 'img/beneficios/beneficio_1761060929_68f7a841b844a.png', 1, '2025-10-21 10:35:29', '2025-10-29 15:25:56', NULL);
INSERT INTO `beneficios` VALUES (22, 'BATERIAS', 16, 0, '🔋 ¡Renueva tu batería con CrediGo! 🚗\r\nFinancia tu batería con solo el 30% de inicial 😎\r\nEl precio varía según la cotización.\r\nPara recibir la tuya, envíanos:\r\n📌 Marca del vehículo\r\n📌 Modelo\r\n📌 Año\r\n📌 Número de placa\r\n💬 Escríbenos y recibe la mejor opción para tu vehículo.', 70.00, 3, 60.00, 'S/.', NULL, NULL, 'img/beneficios/beneficio_1761061399_68f7aa170f6f7.png', 1, '2025-10-21 10:43:19', '2025-11-03 10:19:30', 8);
INSERT INTO `beneficios` VALUES (24, 'CHIP CORPORATIVO', 36, 0, '📲 ¡Activa tu Chip Corporativo con CrediGo! 🚀\r\nDisfruta de un plan ilimitado por solo S/35 mensuales 🔥\r\nIdeal para conductores que necesitan estar siempre conectados.', 5.00, 12, 35.00, 'S/.', NULL, NULL, 'img/beneficios/beneficio_1761061720_68f7ab58236c8.png', 1, '2025-10-21 10:48:40', '2025-10-29 15:18:36', NULL);
INSERT INTO `beneficios` VALUES (25, 'MANTENIMIENTO INCAMOTORS', 15, 0, '🚗 ¡Dale mantenimiento a tu vehículo con IncaMotors y CrediGo! 🔧\r\nAprovecha nuestro financiamiento con solo el 30% de inicial 💥\r\nY por esta promoción, te regalamos el filtro de aceite y el escaneo 🛠️\r\n\r\n💬 Agenda tu cita y mantén tu vehículo en perfectas condiciones con IncaMotors y CrediGo.', 72.00, 3, 56.00, 'S/.', NULL, NULL, 'img/beneficios/beneficio_1761082233_68f7fb79cdbc4.png', 1, '2025-10-21 10:54:30', '2025-10-29 15:18:15', 8);
INSERT INTO `beneficios` VALUES (26, 'LLANTAS', 14, 0, '¡Renueva tus llantas con CrediGo! 🚗\r\nFinancia tus llantas con solo el 30% de inicial 💥\r\nContamos con gran variedad de marcas y medidas para todo tipo de vehículo.\r\nEl precio varía según:\r\n📍 Medida\r\n📍 Cantidad\r\n📍 Marca y modelo del vehículo\r\n📍 Año\r\n📍 Calidad (económica o intermedia)\r\n💬 Solicita tu cotización personalizada y equípate con seguridad y estilo con CrediGo.', 89.00, 4, 51.50, 'S/.', NULL, NULL, 'img/beneficios/beneficio_1761062504_68f7ae6894ea4.png', 1, '2025-10-21 11:01:44', '2025-10-27 10:04:29', 8);
INSERT INTO `beneficios` VALUES (27, 'KIA SOLUTO', 42, 0, 'Financia tu Kia Soluto con Leasy y empieza a trabajar rápido.\r\n Inicial desde USD 500 y cuota semanal desde S/ 520 (afiliado a plataforma Yango).  \r\nIncluye SOAT, seguro vehicular, GPS, conversión a GLP, monitoreo 24/7, impuesto vehicular.\r\n Requisitos claros, flexibilidad para reparaciones. \r\nEmpieza hoy: elige tu Kia Soluto  y pon tu auto a producir.\r\n\r\nVALIDO SOLO AREQUIPA', 500.00, 262, 153.00, '$', NULL, 'semanal', 'img/beneficios/beneficio_1761233463_68fa4a3718fe8.jpg', 1, '2025-10-23 10:31:03', '2025-10-29 15:17:38', 8);
INSERT INTO `beneficios` VALUES (28, 'GEELY EMGRAND', 42, 0, 'Financia tu Geely Emgrand con Leasy y conduce un auto moderno desde USD 750 de inicial y S/ 535 semanales (afiliado a plataforma Yango). \r\nIncluye SOAT, seguro vehicular, GPS, conversión a GLP, monitoreo 24/7, impuesto vehicular. \r\nFlexibilidad para reparaciones. \r\nEmpieza hoy: elige tu Geely Emgrand  y pon tu auto a producir.\r\n\r\nVALIDO SOLO PARA AREQUIPA', 750.00, 262, 158.00, '$', NULL, 'semanal', 'img/beneficios/beneficio_1761233801_68fa4b89b0690.jpg', 1, '2025-10-23 10:36:41', '2025-10-29 11:35:58', 8);
INSERT INTO `beneficios` VALUES (29, 'CREDI YANGO', 45, NULL, '¡Tu carro te espera con Credi Yango! 🚗\r\nAccede con una inicial de solo $2000 y paga cómodamente $100 semanales por 200 semanas.\r\n🔥 ¡Así de simple, así de tuyo!', 2000.00, 200, 100.00, NULL, NULL, NULL, 'img/beneficios/beneficio_1762270673_690a1dd143287.png', 1, '2025-11-04 09:37:53', '2025-11-04 09:37:53', NULL);

SET FOREIGN_KEY_CHECKS = 1;
