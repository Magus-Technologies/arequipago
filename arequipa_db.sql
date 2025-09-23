-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-09-2025 a las 00:21:55
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `arequipa_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes_financiar`
--

CREATE TABLE `clientes_financiar` (
  `id` int(11) NOT NULL,
  `tipo_doc` varchar(30) NOT NULL,
  `n_documento` varchar(20) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellido_paterno` varchar(100) NOT NULL,
  `apellido_materno` varchar(100) NOT NULL,
  `num_cod_finan` varchar(50) DEFAULT NULL,
  `nacionalidad` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `departamento` varchar(50) NOT NULL,
  `provincia` varchar(50) NOT NULL,
  `distrito` varchar(50) NOT NULL,
  `direccion_detallada` text NOT NULL,
  `emergencia_nombre` varchar(200) DEFAULT NULL,
  `emergencia_telefono` varchar(20) DEFAULT NULL,
  `emergencia_parentesco` varchar(100) DEFAULT NULL,
  `laboral_nombre` varchar(200) DEFAULT NULL,
  `laboral_telefono` varchar(20) DEFAULT NULL,
  `laboral_puesto` varchar(100) DEFAULT NULL,
  `laboral_empresa` varchar(200) DEFAULT NULL,
  `recibo_servicios` varchar(255) DEFAULT NULL,
  `doc_identidad` varchar(255) DEFAULT NULL,
  `otro_doc_1` varchar(255) DEFAULT NULL,
  `otro_doc_2` varchar(255) DEFAULT NULL,
  `otro_doc_3` varchar(255) DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `fecha_registro` datetime NOT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `verificacion_domiciliaria` tinyint(1) DEFAULT NULL COMMENT 'Campo para verificar si se realizó verificación domiciliaria'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `clientes_financiar`
--

INSERT INTO `clientes_financiar` (`id`, `tipo_doc`, `n_documento`, `nombres`, `apellido_paterno`, `apellido_materno`, `num_cod_finan`, `nacionalidad`, `fecha_nacimiento`, `telefono`, `correo`, `departamento`, `provincia`, `distrito`, `direccion_detallada`, `emergencia_nombre`, `emergencia_telefono`, `emergencia_parentesco`, `laboral_nombre`, `laboral_telefono`, `laboral_puesto`, `laboral_empresa`, `recibo_servicios`, `doc_identidad`, `otro_doc_1`, `otro_doc_2`, `otro_doc_3`, `comentarios`, `fecha_registro`, `fecha_actualizacion`, `password`, `verificacion_domiciliaria`) VALUES
(6, 'DNI', '45399830', 'FRANKLIN', 'HUAMAN', 'ALVAREZ', '10', 'PERUANO', '1987-04-15', '+51 929 449 945', 'FRANKY152020@GMAIL.COM', '8', '89', '804', 'CALLE AMAZONAS 119 ZN C ', '', '', '', '', '', '', '', 'public/clientesFiles/recibo_servicios_6808018697503.pdf', 'public/clientesFiles/doc_identidad_680801869778f.pdf', '', '', '', '', '2025-04-22 16:52:22', '2025-05-20 21:06:08', '$2y$12$aekhH3PwD8MqOK6756HZ6ehsCe9BTfmuKyL9qlD97zM28C83dG1NW', NULL),
(7, 'DNI', '47971388', 'FREDY', 'TEVES', 'AGUILAR', '28', 'PERUANO', '1993-10-26', '?+51 902026608', 'TEVES.AQP@GMAIL.COM', '8', '89', '808', 'URB.NUEVA CAMPIÑA MZ - X , LT 10 SABANDIA ', '', '', '', '', '', '', '', 'public/clientesFiles/recibo_servicios_68083410daf2d.pdf', 'public/clientesFiles/doc_identidad_68083410db083.pdf', '', '', '', '', '2025-04-22 20:28:00', '2025-07-23 13:22:45', '$2y$10$bCUSDc84gPRBlI1Ye1S8muIHesSZvHke5idiDw863.sF/F7vlkosq', NULL),
(8, 'DNI', '42879435', 'HECTOR ALFONSO', 'CUENTAS', 'ARMENGOD', '36', 'PERUANO', '1985-03-12', '918862855', 'axlhelios@gmail.com', '8', '89', '814', 'urb. salaberry coronel del solar 414  ', '', '', '', '', '', '', '', 'public/clientesFiles/recibo_servicios_6808376e013a3.pdf', 'public/clientesFiles/doc_identidad_6808376e015b5.pdf', '', '', '', '', '2025-04-22 20:42:22', '2025-07-23 13:22:45', '$2y$10$LhDXjBwQeEb1tNAPaC6mrOWeEQ7k5AYrTrbmnyyAK5y0oj/XG3QXm', NULL),
(9, 'DNI', '71559833', 'KEVYN EDINHIO', 'CORNEJO', 'MORALES', '4', 'PERUANO', '1997-03-04', '963713711', 'kevyncor@gmail.com', '8', '89', '799', 'urb. santa catalina mz. p lt.6', '', '', '', '', '', '', '', 'public/clientesFiles/recibo_servicios_6808397ab9a99.pdf', 'public/clientesFiles/doc_identidad_6808397ab9c1a.pdf', '', '', '', '', '2025-04-22 20:51:06', '2025-05-22 12:00:30', '$2y$12$N/B0ogOyZo83lKIXqUT.zuRmVLEqoFV0NvPSBUBrhvD9bY2lnOlLi', NULL),
(10, 'DNI', '48087514', 'ROBINSON BRIAN', 'RAMOS', 'APAZA', '16', 'PERUANA', '1984-01-07', '992744592', 'brianramos27a@gmail.com', '8', '89', '795', 'sorana de los angeles mz 2 lote 5 zn semirural pachacutec', ' ANA CECILIA PARAHUAYO MARROQUIN', '992743721', 'ESPOSA', 'ROBINSON BRIAN  RAMOS APAZA', '992744592', 'CONDUCTOR', 'AREQUIPA GO', '', '', '', '', '', '', '2025-05-08 15:01:11', '2025-07-23 13:22:45', '$2y$10$fyjiwz70Z2HDVwnObRfQou0p.yPhqJ3fFK3mW0pprt/RaAq0mSfm2', NULL),
(11, 'DNI', '29481856', 'VICKY BEATRIZ', 'CHAMBI', 'HOLGUIN', '86', 'PERUANO', '1974-11-13', '+51986148827', 'VICKYLINDA40@GMAIL.COM', '8', '89', '804', 'URB AV SALAVERRY Q4  - MIRAFLORES ', 'VICTOR CAJO', '982938411', 'ESPOSO', '', '', '', '', '', '', '', '', '', '', '2025-05-14 13:58:36', '2025-07-23 13:22:45', '$2y$10$GGb6vvIg/R0IPD2ZNxR2suhg7sI4kdBC/fuIapnOfEN2H2YP6MUDa', NULL),
(12, 'DNI', '72693626', 'DIANA LORENA', 'SANCHEZ', 'DIAZ', '31', 'PERUANO', '1993-11-30', '936458574', 'lorena.diaz3028@gmail.com', '8', '89', '799', 'urb villa jabiru a7 ', 'gian jesus ', '931724072', 'ESPOSO', '', '', '', '', '', '', '', '', '', '', '2025-05-15 14:18:24', NULL, '$2y$12$nAuV4N7jtm9KrfAKr9YLyeiYZ5LRqTvc/HL9i2RBKwXESrssvL/UO', NULL),
(13, 'DNI', '45589803', 'NATALY MERCEDES', 'CANAZA', 'BELIZARIO', '14', 'PERUANO', '1988-06-24', '+51 991 438 519', 'naty_lm24@hotmail', '8', '89', '795', 'calle luna Zona 4 MZ 4 SEMI RURAL  ( PACHACUTEC )', 'HAMMERLY ', '940849853', 'ESPOSO', '', '', '', '', '', '', '', '', '', '', '2025-05-15 15:03:42', NULL, '$2y$12$dbF7jKGs.krQkXxioxFOhejlE8v95G9P6VLtKk5jOFksv2TIrwzSu', NULL),
(15, 'DNI', '47606585', 'GIANCARLOS JESUS', 'RAMOS', 'LEZAMA', '31', 'PERUANO', '1991-11-07', '931724072', 'gian_jesus@hotmail.com', '8', '89', '799', 'URB VILLA JABIRU A7 ', 'LORENA SANCHEZ ', '950308205', 'ESPOSA ', '', '', '', '', '', '', '', '', '', '', '2025-05-19 11:41:11', '2025-07-23 13:22:45', '$2y$10$70HySahCRm5pPBW8CUrfv.ECxDAzrRjLSsQMla8ChYeiGN.WwP3DC', NULL),
(20, 'Carnet de Extranjería', '007655917', 'BERNIE JAVIER', 'CEBALLOS', 'PERES', '', 'VENEZOLANA', '1993-03-06', '946365624', '', '8', '89', '793', 'AREQUIPA', 'DANIEL JOSE BELLO YENDI', '949599608', 'VENDEDOR', '', '', '', '', '', '', '', '', '', '', '2025-06-25 10:56:09', '2025-07-23 13:22:45', '$2y$10$6pRnxh1lSFSbFKimC6d8OehS75H3WaZg4FE4HAa2PbKEDz1u08Mc.', NULL),
(21, 'DNI', '80614085', 'JUAN CARLOS', 'MAMANI', 'HUAYNAPATA', '28', 'PERUANO', '1980-05-04', '948803028', 'carlosmhrkt@gmail.com', '8', '89', '792', 'urb juan velazco alvarado mz . M lt.11 ', 'VICTOR CAJO', '960991738', 'cuñado ', '', '', '', '', '', '', '', '', '', '', '2025-07-03 11:46:12', '2025-07-23 13:22:45', '$2y$10$nAOwlBg2OByVZYFApDu2KuoN7kZJTRgAPWKTw8ila8Cvvi5l1Okqq', NULL),
(22, 'DNI', '73362691', 'JAVIER MARCELO', 'DIAZ', 'SUBIA', '58', 'PERUANO', '1996-02-08', '989213232', 'siegraim58@gmail.com', '8', '89', '804', 'calle 3 volcanes 107 CUIDAD BLANCA ', '', '', '', '', '', '', '', '', '', '', '', '', 'PENDIENTE ENTREGAR LOS DOCUMENTES ', '2025-08-12 13:38:26', NULL, NULL, NULL),
(23, 'DNI', '43070307', 'EYCOL DULAN', 'VELASQUEZ', 'MERINO', '', 'PERUANA', '1984-03-23', '982917931', '', '8', '89', '801', 'CALLE BERNA 216 URB SANTA ROSA', '', '', '', '', '', '', '', '', '', '', '', '', '', '2025-08-26 20:53:06', NULL, NULL, NULL),
(24, 'DNI', '45354419', 'GIANCARLO', 'MARAZO', 'COSY', '', 'PERUANA', '1986-09-16', '982942663', '', '8', '89', '799', 'URB CAMINO REAL II ETAPA MZ D LT 6 ', '', '', '', '', '', '', '', '', '', '', '', '', '', '2025-08-26 21:08:51', NULL, NULL, NULL),
(25, 'DNI', '44819866', 'RONALD EDGAR', 'ARAPA', 'MAMANI', '', 'PERUANA', '1987-01-22', '980698757', '', '8', '89', '794', 'P JOVEN FRANCISCO BOLOGNESI CALLE PROGRESO 401', '', '', '', '', '', '', '', '', '', '', '', '', '', '2025-08-26 21:18:33', NULL, NULL, NULL),
(26, 'Carnet de Extranjería', '006512242', 'EGNYS', 'JOUSEPH', 'HERNANDEZ', '', '', '1986-11-25', '923696983', '', '8', '89', '814', 'AVENIDA URB LARA 1MZ F LOTE 1 URB LARA', '', '', '', '', '', '', '', '', '', '', '', '', '', '2025-08-26 21:37:05', '2025-08-26 21:45:16', NULL, NULL),
(27, 'DNI', '29550826', 'JOSE GAVINO', 'FLORES', 'COAGUILA', '', 'PERUANO', '1966-10-25', '936252655', 'gavino.66@gmail.com', '8', '89', '799', 'calle andres razuri 214 urb.13 de enero', 'HIJO', '982945619', 'HIJO', '', '', '', '', '', '', '', '', '', '', '2025-08-27 16:00:55', NULL, NULL, NULL),
(28, 'DNI', '70462464', 'ROBERT ALONSO', 'CORNEJO', 'LUNA', '', 'PERUANO', '1991-02-22', '959727405', '', '8', '89', '799', 'URB.BANCARIOS H-14', 'COORPORATIVO', '982920717', 'COORPORATIVO', '', '', '', '', '', '', '', '', '', '', '2025-08-27 16:14:23', NULL, NULL, NULL),
(29, 'DNI', '74203312', 'PAUL DEYBIS', 'HUANCOLLO', 'HUMPIRE', '', 'PERUANO', '1985-04-05', '902789141', '', '8', '89', '801', 'CALLE TUPAC AMARU 111 PP.JJ ATALAYA', 'COORPORATIVO', '982901380', 'COORPORATIVO', '', '', '', '', '', '', '', '', '', '', '2025-08-27 16:42:31', NULL, NULL, NULL),
(30, 'DNI', '70580837', 'ANTONY STEVE', 'CARLOS', 'MAMANI', '', 'PERUANO', '1985-12-01', '982902169', '', '8', '93', '870', 'PARCELA 97 QUINTO RAMAL E7 IRRIGACIONES MAJES', '', '', '', '', '', '', '', '', '', '', '', '', '', '2025-08-27 17:09:31', NULL, NULL, NULL),
(31, 'DNI', '45751372', 'carlos enrique ', 'chambi', 'ccahuari', '', 'peruana', '1988-07-29', '942149113', 'charajara.caud@gmail.com', '8', '89', '793', 'villa la pradera mz l lt 3 miraflores ', 'maria elena', '959538383', 'esposa', '', '', '', '', '', '', '', '', '', '', '2025-09-02 10:10:28', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cupones`
--

CREATE TABLE `cupones` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo_descuento` enum('porcentaje','monto_fijo') NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `imagen_banner` varchar(500) DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `limite_usos_conductor` int(11) DEFAULT 1,
  `limite_usos_total` int(11) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `cupones`
--

INSERT INTO `cupones` (`id`, `titulo`, `descripcion`, `tipo_descuento`, `valor`, `imagen_banner`, `fecha_inicio`, `fecha_fin`, `limite_usos_conductor`, `limite_usos_total`, `activo`, `created_at`, `updated_at`) VALUES
(15, 'descuento 10 %', 'fdvsdsfvdsvdfsvdsf', 'monto_fijo', '23.00', 'img/cupones/cupon_68cdd44c49f7c7.42120687.png', '2025-09-19', '2025-10-19', 1, NULL, 1, '2025-09-19 22:08:12', '2025-09-19 22:08:12'),
(16, 'dvfdsvdsff', 'vfdsvfdsfv', 'monto_fijo', '23.00', 'img/cupones/cupon_68cdd50eefb2b1.21923507.png', '2025-09-19', '2025-10-19', 2, NULL, 1, '2025-09-19 22:11:26', '2025-09-19 22:11:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cupones_asignados`
--

CREATE TABLE `cupones_asignados` (
  `id` int(11) NOT NULL,
  `id_cupon` int(11) NOT NULL,
  `tipo_usuario` enum('conductor','cliente') NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_asignacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `cupones_asignados`
--

INSERT INTO `cupones_asignados` (`id`, `id_cupon`, `tipo_usuario`, `id_usuario`, `activo`, `fecha_asignacion`) VALUES
(755, 15, 'cliente', 30, 1, '2025-09-19 22:08:12'),
(756, 15, 'cliente', 20, 1, '2025-09-19 22:08:12'),
(757, 15, 'cliente', 31, 1, '2025-09-19 22:08:12'),
(758, 15, 'cliente', 12, 1, '2025-09-19 22:08:12'),
(759, 15, 'cliente', 26, 1, '2025-09-19 22:08:12'),
(760, 15, 'cliente', 23, 1, '2025-09-19 22:08:12'),
(761, 15, 'cliente', 6, 1, '2025-09-19 22:08:12'),
(762, 15, 'cliente', 7, 1, '2025-09-19 22:08:12'),
(763, 15, 'cliente', 24, 1, '2025-09-19 22:08:12'),
(764, 15, 'cliente', 15, 1, '2025-09-19 22:08:12'),
(765, 15, 'cliente', 8, 1, '2025-09-19 22:08:12'),
(766, 15, 'cliente', 22, 1, '2025-09-19 22:08:12'),
(767, 15, 'cliente', 27, 1, '2025-09-19 22:08:12'),
(768, 15, 'cliente', 21, 1, '2025-09-19 22:08:12'),
(769, 15, 'cliente', 9, 1, '2025-09-19 22:08:12'),
(770, 15, 'cliente', 13, 1, '2025-09-19 22:08:12'),
(771, 15, 'cliente', 29, 1, '2025-09-19 22:08:12'),
(772, 15, 'cliente', 28, 1, '2025-09-19 22:08:12'),
(773, 15, 'cliente', 10, 1, '2025-09-19 22:08:12'),
(774, 15, 'cliente', 25, 1, '2025-09-19 22:08:12'),
(775, 16, 'conductor', 286, 1, '2025-09-19 22:11:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cupones_uso_tracking`
--

CREATE TABLE `cupones_uso_tracking` (
  `id` int(11) NOT NULL,
  `id_cupon` int(11) NOT NULL,
  `id_conductor` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `tipo_usuario` enum('conductor','cliente') DEFAULT 'conductor',
  `fecha_uso` timestamp NOT NULL DEFAULT current_timestamp(),
  `monto_descuento` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `cupones_uso_tracking`
--

INSERT INTO `cupones_uso_tracking` (`id`, `id_cupon`, `id_conductor`, `id_cliente`, `tipo_usuario`, `fecha_uso`, `monto_descuento`) VALUES
(11, 15, 30, NULL, 'conductor', '2025-09-19 22:13:09', '0.00'),
(12, 16, 286, NULL, 'conductor', '2025-09-19 22:14:17', '0.00'),
(13, 16, 286, NULL, 'conductor', '2025-09-19 22:17:08', '0.00');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes_financiar`
--
ALTER TABLE `clientes_financiar`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `idx_n_documento` (`n_documento`) USING BTREE,
  ADD KEY `idx_tipo_doc` (`tipo_doc`) USING BTREE,
  ADD KEY `idx_apellidos` (`apellido_paterno`,`apellido_materno`) USING BTREE;

--
-- Indices de la tabla `cupones`
--
ALTER TABLE `cupones`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `idx_cupones_activo` (`activo`) USING BTREE,
  ADD KEY `idx_cupones_fechas` (`fecha_inicio`,`fecha_fin`) USING BTREE;

--
-- Indices de la tabla `cupones_asignados`
--
ALTER TABLE `cupones_asignados`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `idx_cupon_tipo_usuario` (`id_cupon`,`tipo_usuario`,`id_usuario`) USING BTREE;

--
-- Indices de la tabla `cupones_uso_tracking`
--
ALTER TABLE `cupones_uso_tracking`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `idx_cupones_uso_cupon` (`id_cupon`) USING BTREE,
  ADD KEY `idx_cupones_uso_conductor` (`id_conductor`) USING BTREE,
  ADD KEY `idx_cupones_uso_fecha` (`fecha_uso`) USING BTREE;

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes_financiar`
--
ALTER TABLE `clientes_financiar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `cupones`
--
ALTER TABLE `cupones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `cupones_asignados`
--
ALTER TABLE `cupones_asignados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=776;

--
-- AUTO_INCREMENT de la tabla `cupones_uso_tracking`
--
ALTER TABLE `cupones_uso_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cupones_asignados`
--
ALTER TABLE `cupones_asignados`
  ADD CONSTRAINT `cupones_asignados_ibfk_1` FOREIGN KEY (`id_cupon`) REFERENCES `cupones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cupones_uso_tracking`
--
ALTER TABLE `cupones_uso_tracking`
  ADD CONSTRAINT `cupones_uso_tracking_ibfk_1` FOREIGN KEY (`id_cupon`) REFERENCES `cupones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cupones_uso_tracking_ibfk_2` FOREIGN KEY (`id_conductor`) REFERENCES `conductores` (`id_conductor`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
