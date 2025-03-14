-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 14-03-2025 a las 13:25:22
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
-- Estructura de tabla para la tabla `chats`
--

CREATE TABLE `chats` (
  `idchat` int NOT NULL,
  `idcliente` int NOT NULL,
  `mensaje` text NOT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `visto` tinyint(1) DEFAULT '0',
  `remitente` enum('cliente','bot','humano') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(39, 78, 'marked_read', '2025-03-12 14:42:27', '2025-03-12 14:42:28'),
(40, 80, 'marked_read', '2025-03-12 14:53:25', '2025-03-12 14:53:25'),
(41, 82, 'marked_read', '2025-03-12 14:55:54', '2025-03-12 14:55:55'),
(42, 84, 'marked_read', '2025-03-12 15:00:48', '2025-03-12 15:00:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `idcliente` int NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventlog`
--

CREATE TABLE `eventlog` (
  `id` int NOT NULL,
  `timestamp` datetime NOT NULL,
  `evento` varchar(100) NOT NULL,
  `texto` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `idgrupo` int NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`idgrupo`, `nombre`) VALUES
(1, 'Administradores');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos_permisos`
--

CREATE TABLE `grupos_permisos` (
  `id` int NOT NULL,
  `idpermiso` int NOT NULL,
  `idgrupo` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `grupos_permisos`
--

INSERT INTO `grupos_permisos` (`id`, `idpermiso`, `idgrupo`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumos`
--

CREATE TABLE `insumos` (
  `idinsumo` int NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `stock` int DEFAULT '0',
  `costo` decimal(10,2) NOT NULL,
  `usa_para_precio_mayorista` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lista_precios`
--

CREATE TABLE `lista_precios` (
  `idlista` int NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lista_precio_producto`
--

CREATE TABLE `lista_precio_producto` (
  `id` int NOT NULL,
  `idlista` int NOT NULL,
  `idproducto` int NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `idpermiso` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`idpermiso`, `nombre`, `descripcion`) VALUES
(1, 'VER_PANEL', 'Acceso al panel principal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `idproducto` int NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `idtipo` int NOT NULL,
  `stock` int DEFAULT '0',
  `costo` decimal(10,2) NOT NULL,
  `descripcion` text,
  `imagenes` text,
  `peso` decimal(10,2) DEFAULT NULL,
  `ancho` decimal(10,2) DEFAULT NULL,
  `alto` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_insumo`
--

CREATE TABLE `producto_insumo` (
  `id` int NOT NULL,
  `idproducto` int NOT NULL,
  `idinsumo` int NOT NULL,
  `cantidad` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idusuario` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `celular` varchar(50) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `dni` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idusuario`, `username`, `password`, `email`, `nombre`, `apellido`, `telefono`, `celular`, `direccion`, `dni`) VALUES
(1, 'admin', 'admin123', 'admin@dominio.com', 'Admin', 'Principal', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_grupos`
--

CREATE TABLE `usuarios_grupos` (
  `id` int NOT NULL,
  `idusuario` int NOT NULL,
  `idgrupo` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios_grupos`
--

INSERT INTO `usuarios_grupos` (`id`, `idusuario`, `idgrupo`) VALUES
(1, 1, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`idchat`),
  ADD KEY `idcliente` (`idcliente`);

--
-- Indices de la tabla `chats_audit`
--
ALTER TABLE `chats_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idchat` (`idchat`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`idcliente`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `telefono` (`telefono`);

--
-- Indices de la tabla `eventlog`
--
ALTER TABLE `eventlog`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`idgrupo`);

--
-- Indices de la tabla `grupos_permisos`
--
ALTER TABLE `grupos_permisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_gp_permiso` (`idpermiso`),
  ADD KEY `fk_gp_grupo` (`idgrupo`);

--
-- Indices de la tabla `insumos`
--
ALTER TABLE `insumos`
  ADD PRIMARY KEY (`idinsumo`);

--
-- Indices de la tabla `lista_precios`
--
ALTER TABLE `lista_precios`
  ADD PRIMARY KEY (`idlista`);

--
-- Indices de la tabla `lista_precio_producto`
--
ALTER TABLE `lista_precio_producto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idlista` (`idlista`),
  ADD KEY `idproducto` (`idproducto`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`idpermiso`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`idproducto`);

--
-- Indices de la tabla `producto_insumo`
--
ALTER TABLE `producto_insumo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idproducto` (`idproducto`),
  ADD KEY `idinsumo` (`idinsumo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idusuario`);

--
-- Indices de la tabla `usuarios_grupos`
--
ALTER TABLE `usuarios_grupos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ug_usuario` (`idusuario`),
  ADD KEY `fk_ug_grupo` (`idgrupo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `chats`
--
ALTER TABLE `chats`
  MODIFY `idchat` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `chats_audit`
--
ALTER TABLE `chats_audit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `idcliente` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `eventlog`
--
ALTER TABLE `eventlog`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `idgrupo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `grupos_permisos`
--
ALTER TABLE `grupos_permisos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `insumos`
--
ALTER TABLE `insumos`
  MODIFY `idinsumo` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lista_precios`
--
ALTER TABLE `lista_precios`
  MODIFY `idlista` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lista_precio_producto`
--
ALTER TABLE `lista_precio_producto`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `idpermiso` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `idproducto` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto_insumo`
--
ALTER TABLE `producto_insumo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idusuario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios_grupos`
--
ALTER TABLE `usuarios_grupos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `chats_ibfk_1` FOREIGN KEY (`idcliente`) REFERENCES `clientes` (`idcliente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `chats_audit`
--
ALTER TABLE `chats_audit`
  ADD CONSTRAINT `chats_audit_ibfk_1` FOREIGN KEY (`idchat`) REFERENCES `chats` (`idchat`);

--
-- Filtros para la tabla `grupos_permisos`
--
ALTER TABLE `grupos_permisos`
  ADD CONSTRAINT `fk_gp_grupo` FOREIGN KEY (`idgrupo`) REFERENCES `grupos` (`idgrupo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_gp_permiso` FOREIGN KEY (`idpermiso`) REFERENCES `permisos` (`idpermiso`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `lista_precio_producto`
--
ALTER TABLE `lista_precio_producto`
  ADD CONSTRAINT `lista_precio_producto_ibfk_1` FOREIGN KEY (`idlista`) REFERENCES `lista_precios` (`idlista`) ON DELETE CASCADE,
  ADD CONSTRAINT `lista_precio_producto_ibfk_2` FOREIGN KEY (`idproducto`) REFERENCES `productos` (`idproducto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `producto_insumo`
--
ALTER TABLE `producto_insumo`
  ADD CONSTRAINT `producto_insumo_ibfk_1` FOREIGN KEY (`idproducto`) REFERENCES `productos` (`idproducto`) ON DELETE CASCADE,
  ADD CONSTRAINT `producto_insumo_ibfk_2` FOREIGN KEY (`idinsumo`) REFERENCES `insumos` (`idinsumo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios_grupos`
--
ALTER TABLE `usuarios_grupos`
  ADD CONSTRAINT `fk_ug_grupo` FOREIGN KEY (`idgrupo`) REFERENCES `grupos` (`idgrupo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ug_usuario` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
