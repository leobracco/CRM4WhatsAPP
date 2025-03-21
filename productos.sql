-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 17-03-2025 a las 22:07:26
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
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `idproducto` int NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `idtipo` int NOT NULL,
  `markup` decimal(5,2) NOT NULL DEFAULT '0.00',
  `markupMayorista` decimal(5,2) NOT NULL DEFAULT '0.00',
  `stock` int DEFAULT '0',
  `descripcion` text,
  `imagenes` text,
  `peso` decimal(10,2) DEFAULT NULL,
  `ancho` decimal(10,2) DEFAULT NULL,
  `alto` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`idproducto`, `nombre`, `idtipo`, `markup`, `markupMayorista`, `stock`, `descripcion`, `imagenes`, `peso`, `ancho`, `alto`) VALUES
(1, 'Cuenco de madera 16cm', 1, 65.00, 59.00, 0, NULL, NULL, NULL, NULL, NULL),
(2, 'Recarga Auto', 2, 78.00, 50.00, 0, NULL, NULL, NULL, NULL, NULL),
(3, 'Petaca 200cc con Tapa', 2, 68.00, 59.00, 0, NULL, NULL, NULL, NULL, NULL),
(4, 'Caramelera Emilia 200cc / PARIS NEGRA', 1, 65.00, 50.00, 0, NULL, NULL, NULL, NULL, NULL),
(5, 'Varillas Encendedoras', 5, 65.00, 59.00, 0, NULL, NULL, NULL, NULL, NULL),
(6, 'Marsala', 1, 50.00, 10.00, 0, NULL, NULL, NULL, NULL, NULL),
(7, 'Caramelera Emilia Mini', 1, 65.00, 59.00, 0, NULL, NULL, NULL, NULL, NULL),
(8, 'Berlin XL', 1, 63.00, 59.00, 0, NULL, NULL, NULL, NULL, NULL),
(9, 'Tapa Barcelona', 5, 65.00, 59.00, 0, NULL, NULL, NULL, NULL, NULL),
(10, 'Cuenco de 12cm', 1, 66.00, 59.00, 0, NULL, NULL, NULL, NULL, NULL),
(11, 'Jabon', 4, 56.00, 40.00, 0, NULL, NULL, NULL, NULL, NULL),
(12, 'Cuenco 14cm', 1, 65.00, 59.00, 0, NULL, NULL, NULL, NULL, NULL),
(13, 'Juego varillas', 5, 65.00, 59.00, 0, NULL, NULL, NULL, NULL, NULL),
(14, 'Caramelera oriental', 1, 65.00, 50.00, 0, NULL, NULL, NULL, NULL, NULL),
(15, 'Caramelera de pie', 1, 65.00, 59.00, 0, NULL, NULL, NULL, NULL, NULL),
(16, 'Caramelera Emilia 100cc / PARIS', 1, 65.00, 50.00, 0, NULL, NULL, NULL, NULL, NULL),
(17, 'Repuesto Perfumina 500cc', 3, 60.00, 45.00, 0, NULL, NULL, NULL, NULL, NULL),
(18, 'Difusor Botanico 125cc', 2, 66.00, 50.00, 0, NULL, NULL, NULL, NULL, NULL),
(19, 'Difusor Botanico 250cc', 2, 65.00, 50.00, 0, NULL, NULL, NULL, NULL, NULL),
(20, 'Repuesto Difusor 500cc', 2, 60.50, 45.00, 0, NULL, NULL, NULL, NULL, NULL),
(21, 'Difusor Petaca 200cc', 2, 65.00, 50.00, 0, NULL, NULL, NULL, NULL, NULL),
(22, 'Difusor de auto/placard', 2, 65.00, 45.00, 0, NULL, NULL, NULL, NULL, NULL),
(23, 'Caramelera Emilia 300cc / PARIS', 1, 65.00, 50.00, 0, NULL, NULL, NULL, NULL, NULL),
(24, 'Cupula VENECIA', 1, 60.00, 45.00, 0, NULL, NULL, NULL, NULL, NULL),
(25, 'Caramelera Moria / BERLIN', 1, 65.00, 50.00, 0, NULL, NULL, NULL, NULL, NULL),
(26, 'Cupula Miel/ VENECIA MIEL', 1, 60.00, 45.00, 0, NULL, NULL, NULL, NULL, NULL),
(27, 'Cupula mini transparente / VENECIA', 1, 55.00, 45.00, 0, NULL, NULL, NULL, NULL, NULL),
(28, 'Cupula mini Miel / VENECIA MIEL', 1, 55.00, 45.00, 0, NULL, NULL, NULL, NULL, NULL),
(29, 'Repuesto Perfumina x litro', 3, 65.00, 50.00, 0, NULL, NULL, NULL, NULL, NULL),
(30, 'Caramelera Emilia 200cc / PARIS', 1, 65.00, 50.00, 0, NULL, NULL, NULL, NULL, NULL),
(31, 'ojalillos', 5, 65.00, 59.00, 0, NULL, NULL, NULL, NULL, NULL),
(32, 'Barcelona', 1, 65.00, 50.00, 0, NULL, NULL, NULL, NULL, NULL),
(33, 'Cuenco de 8cm', 1, 67.00, 55.00, 0, NULL, NULL, NULL, NULL, NULL),
(34, 'Caramelera Luisana pequeña', 1, 65.00, 50.00, 0, NULL, NULL, NULL, NULL, NULL),
(35, 'Cuenco de 10cm', 1, 67.00, 59.00, 0, NULL, NULL, NULL, NULL, NULL),
(36, 'Perfumina 250cc', 3, 65.00, 50.00, 0, NULL, NULL, NULL, NULL, NULL),
(37, 'Perfumina 500 gatillo', 3, 65.00, 50.00, 0, NULL, NULL, NULL, NULL, NULL),
(38, 'Bolsa Estampada', 5, 65.00, 59.00, 0, NULL, NULL, NULL, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`idproducto`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `idproducto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
