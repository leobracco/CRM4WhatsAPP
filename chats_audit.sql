-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 14-03-2025 a las 13:52:12
-- Versión del servidor: 8.0.41-0ubuntu0.20.04.1
-- Versión de PHP: 7.4.3-4ubuntu2.28

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
-- Estructura de tabla para la tabla `chats_audit`
--

CREATE TABLE `chats_audit` (
  `id` int NOT NULL,
  `idchat` int NOT NULL,
  `accion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `chats_audit`
--

INSERT INTO `chats_audit` (`id`, `idchat`, `accion`, `fecha`, `timestamp`) VALUES
(44, 2, 'marked_read', '2025-03-14 13:49:26', '2025-03-14 13:49:27'),
(45, 4, 'marked_read', '2025-03-14 13:50:26', '2025-03-14 13:50:26');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `chats_audit`
--
ALTER TABLE `chats_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idchat` (`idchat`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `chats_audit`
--
ALTER TABLE `chats_audit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `chats_audit`
--
ALTER TABLE `chats_audit`
  ADD CONSTRAINT `chats_audit_ibfk_1` FOREIGN KEY (`idchat`) REFERENCES `chats` (`idchat`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
