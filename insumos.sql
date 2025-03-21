-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 17-03-2025 a las 22:07:42
-- Versión del servidor: 8.0.41-0ubuntu0.20.04.1
-- Versión de PHP: 8.1.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `apvelas_crm`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumos`
--

CREATE TABLE `insumos` (
  `idinsumo` int NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `idunidad` int NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `costo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `mayorista` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `insumos`
--

INSERT INTO `insumos` (`idinsumo`, `nombre`, `idunidad`, `stock`, `costo`, `mayorista`, `created_at`, `updated_at`) VALUES
(26, 'Frasco recarga', 6, 0, 400.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(27, 'Colirio c/pipeta 10cc', 3, 0, 625.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(28, 'Tapa ciega plastica petacas', 6, 0, 95.00, 0, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(29, 'Endurecedor', 1, 0, 4400.00, 0, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(30, 'Envase pet 30cc t/c (repuesto difusor auto)', 6, 0, 250.00, 0, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(31, 'Embudo plastico', 6, 0, 200.00, 0, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(32, 'Caramelera Emilia 200cc / PARIS NEGRA', 6, 0, 4400.00, 0, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(33, 'Tapa Barcelona', 6, 0, 620.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(34, 'Caramelera con pie Pedras', 6, 0, 5350.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(35, 'Caramelera oriental', 6, 0, 2800.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(36, 'Cupula chica cristal', 1, 0, 7600.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(37, 'Cupula chica Miel', 1, 0, 8990.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(38, 'Cupula Miel', 1, 0, 10950.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(39, 'Caramelera Luisiana pequeña', 1, 0, 2350.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(40, 'Caramelera Emilia Negra brillo', 1, 0, 4350.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(41, 'Caramelera Emilia Grande', 1, 0, 4950.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(42, 'Centro de mesa hueco', 1, 0, 9390.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(43, 'Caramelera  Emilia 100cc / PARIS', 1, 0, 2300.00, 0, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(44, 'Caramelera  Emilia 250cc / PARIS', 1, 0, 4950.00, 0, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(45, 'Caramelera Emilia Negra', 6, 0, 4400.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(46, 'Cupula', 6, 0, 8760.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(47, 'Cupula Miel', 6, 0, 10950.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(48, 'Cera', 1, 0, 3520.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(49, 'Jabon', 6, 0, 2180.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(50, 'Bolsa', 6, 0, 150.00, 0, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(51, 'Tapa metalica', 6, 0, 85.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(52, 'Varillas', 6, 0, 75.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(53, 'Petaca 200cc', 6, 0, 720.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(54, 'Tubo ensayo', 6, 0, 630.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(55, 'Cuenco 8 de madera', 6, 0, 850.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(56, 'Cuenco 10 de madera', 6, 0, 1330.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(57, 'Cuenco 12 de madera', 6, 0, 1570.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(58, 'Cuenco 14 de madera', 6, 0, 1920.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(59, 'Cuenco 16 de madera', 6, 0, 2560.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(60, 'Cuenco 20 de madera', 6, 0, 0.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(61, 'Envase Pet cristal 500cc', 6, 0, 695.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(62, 'Envase Pet cristal 250cc', 6, 0, 265.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(63, 'Fragancia Perfumina x litro', 2, 0, 4900.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(64, 'Fragancia Difusor x litro', 2, 0, 7700.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(65, 'Fragancia pura', 3, 0, 87.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(66, 'Frasco anchoero', 6, 0, 240.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(67, 'Gatillo Negro 500', 6, 0, 450.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(68, 'Envase auto', 6, 0, 960.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(69, 'Mini Moscu', 6, 0, 609.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(70, 'Ojalillo', 6, 0, 9.50, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(71, 'Pabilo', 4, 0, 2.25, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(72, 'Sticker', 6, 0, 100.00, 0, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(73, 'Mini Conico Pekin', 6, 0, 1515.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(74, 'Vaso Tennesse', 6, 0, 1180.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(75, 'Tapa de bambu', 6, 0, 330.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(76, 'Jabonera', 6, 0, 750.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(77, 'Tabla lisa', 6, 0, 800.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(78, 'Jabonera grande', 6, 0, 1300.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(79, 'Moria XL', 6, 0, 6800.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(80, 'Vasitos Argelia', 6, 0, 2450.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(81, 'Amanda mediano', 6, 0, 4720.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(82, 'Amanda chico', 6, 0, 2590.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(83, 'Mar del plata/Moria', 6, 0, 3200.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(84, 'Caramelera Cupula', 6, 0, 8800.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(85, 'Envase imperial con tapa', 6, 0, 2550.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(86, 'Caramelera Emilia 200cc / PARIS transparente', 6, 0, 2990.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(87, 'Caramelera Emilia mini', 6, 0, 2300.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(88, 'Caramelera Italo', 6, 0, 2650.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(89, 'Bandeja oval pino', 6, 0, 1940.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(90, 'Caramelera facetada con pie', 6, 0, 5350.00, 1, '2025-03-17 21:18:05', '2025-03-17 21:18:05'),
(91, 'Bolsa estampada', 6, 0, 566.00, 0, '2025-03-17 21:18:05', '2025-03-17 21:18:05');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `insumos`
--
ALTER TABLE `insumos`
  ADD PRIMARY KEY (`idinsumo`),
  ADD KEY `fk_insumos_unidad` (`idunidad`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `insumos`
--
ALTER TABLE `insumos`
  MODIFY `idinsumo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `insumos`
--
ALTER TABLE `insumos`
  ADD CONSTRAINT `fk_insumos_unidad` FOREIGN KEY (`idunidad`) REFERENCES `unidades_medida` (`idunidad`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
