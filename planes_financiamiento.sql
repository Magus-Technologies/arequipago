-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-09-2025 a las 22:02:27
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
-- Estructura de tabla para la tabla `planes_financiamiento`
--

CREATE TABLE `planes_financiamiento` (
  `idplan_financiamiento` int(10) UNSIGNED NOT NULL,
  `nombre_plan` varchar(50) NOT NULL,
  `cuota_inicial` decimal(10,2) DEFAULT NULL,
  `monto_cuota` decimal(10,2) DEFAULT NULL,
  `cantidad_cuotas` decimal(10,2) DEFAULT NULL,
  `penalizacion_mora` decimal(10,2) DEFAULT NULL,
  `frecuencia_pago` varchar(45) DEFAULT NULL,
  `moneda` varchar(12) DEFAULT NULL,
  `tasa_interes` decimal(10,2) DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `monto_sin_interes` decimal(10,2) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `tipo_vehicular` enum('moto','vehiculo') DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `planes_financiamiento`
--

INSERT INTO `planes_financiamiento` (`idplan_financiamiento`, `nombre_plan`, `cuota_inicial`, `monto_cuota`, `cantidad_cuotas`, `penalizacion_mora`, `frecuencia_pago`, `moneda`, `tasa_interes`, `monto`, `monto_sin_interes`, `fecha_inicio`, `fecha_fin`, `tipo_vehicular`, `estado`) VALUES
(2, 'Redmi 14', '300.00', '100.00', '6.00', NULL, 'mensual', 'S/.', '0.00', '900.00', '900.00', NULL, NULL, NULL, 'activo'),
(3, 'Redmi 14 Pro', '400.00', '130.00', '6.00', NULL, 'mensual', 'S/.', '0.00', '1180.00', '1180.00', NULL, NULL, NULL, 'activo'),
(4, 'Redmi 14 Pro 5G', '500.00', '150.00', '6.00', NULL, 'mensual', 'S/.', '0.00', '1400.00', '1400.00', NULL, NULL, NULL, 'activo'),
(9, 'Financiamiento Vehicular Grupo 1', '0.00', '100.00', '156.00', NULL, 'semanal', '$', '0.00', '15600.00', '14000.00', '2024-04-08', '2027-04-06', NULL, 'inactivo'),
(12, 'Financiamiento Vehicular Grupo 2', '0.00', '100.00', '160.00', NULL, 'semanal', '$', '0.00', '16000.00', '14000.00', '2024-09-09', '2027-10-05', 'vehiculo', 'activo'),
(14, 'FINANCIAMIENTO LLANTAS', '0.00', '0.00', '4.00', NULL, 'semanal', 'S/.', '10.00', NULL, NULL, NULL, NULL, NULL, 'activo'),
(15, 'FINANCIAMIENTO ACEITE', '0.00', '0.00', '4.00', NULL, 'semanal', 'S/.', '10.00', NULL, NULL, NULL, NULL, NULL, 'activo'),
(16, 'FINANCIAMIENTO BATERIAS', '0.00', '0.00', '4.00', NULL, 'semanal', 'S/.', '10.00', NULL, NULL, NULL, NULL, NULL, 'activo'),
(19, 'CrediGo auto (Grupo 3)', '0.00', '0.00', '210.00', NULL, 'semanal', '$', '0.00', NULL, NULL, '2025-06-16', '2029-06-26', 'vehiculo', 'activo'),
(22, 'CrediGo Motos (Grupo 1)', '0.00', '150.00', '52.00', NULL, 'semanal', 'S/.', '0.00', '7800.00', '6500.00', '2025-07-21', '2026-07-21', 'moto', 'activo'),
(33, 'MotosYa', '2000.00', '0.00', '52.00', NULL, 'semanal', 'S/.', '0.00', NULL, NULL, NULL, NULL, NULL, 'activo'),
(36, 'COORPORATIVO CLARO', '0.00', '35.00', '12.00', NULL, 'mensual', 'S/.', '0.00', '420.00', '420.00', '2025-07-24', '2026-07-20', NULL, 'activo'),
(37, 'CANASTA NAVIDEÑA + PAVO', '0.00', '10.00', '14.00', NULL, 'semanal', 'S/.', '0.00', '140.00', '140.00', NULL, NULL, NULL, 'activo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `planes_financiamiento`
--
ALTER TABLE `planes_financiamiento`
  ADD PRIMARY KEY (`idplan_financiamiento`) USING BTREE;

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `planes_financiamiento`
--
ALTER TABLE `planes_financiamiento`
  MODIFY `idplan_financiamiento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
