-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 21-03-2025 a las 14:39:19
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
-- Estructura de tabla para la tabla `chats`
--

CREATE TABLE `chats` (
  `idchat` int NOT NULL,
  `idcliente` int NOT NULL,
  `mensaje` text NOT NULL,
  `original_idchat` int DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `visto` tinyint(1) DEFAULT '0',
  `sender` enum('user','assistant') NOT NULL,
  `tipo` enum('text','image','document','audio') DEFAULT 'text',
  `archivo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `chats`
--

INSERT INTO `chats` (`idchat`, `idcliente`, `mensaje`, `original_idchat`, `timestamp`, `visto`, `sender`, `tipo`, `archivo`) VALUES
(2, 1, 'test', NULL, '2025-03-14 13:49:24', 1, 'user', 'text', NULL),
(3, 1, '¡Hola! 🌟 En AP Velas estamos aquí para ayudarte. Te envío nuestro catálogo actualizado. 🕯️📦', 2, '2025-03-14 13:49:26', 1, 'assistant', 'text', NULL),
(4, 1, 'catalogo', NULL, '2025-03-14 13:50:02', 1, 'user', 'text', NULL),
(5, 1, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 4, '2025-03-14 13:50:26', 1, 'assistant', 'text', NULL),
(6, 2, 'Todo muy bueno', NULL, '2025-03-14 19:51:21', 1, 'user', 'text', NULL),
(7, 2, 'Me encantan la así velas', NULL, '2025-03-14 19:51:21', 1, 'user', 'text', NULL),
(8, 2, '¡Hola! 🌟 En AP Velas estamos aquí para ayudarte. Te envío nuestro catálogo actualizado. 🕯️📦', 6, '2025-03-14 19:51:27', 1, 'assistant', 'text', NULL),
(9, 2, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 7, '2025-03-14 19:51:27', 1, 'assistant', 'text', NULL),
(10, 2, 'Gracias!', NULL, '2025-03-14 19:52:35', 1, 'user', 'text', NULL),
(11, 2, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 10, '2025-03-14 19:52:56', 1, 'assistant', 'text', NULL),
(12, 3, 'Hola', NULL, '2025-03-14 21:13:30', 1, 'user', 'text', NULL),
(13, 3, '¡Hola! 🌟 En AP Velas estamos aquí para ayudarte. Te envío nuestro catálogo actualizado. 🕯️📦', 12, '2025-03-14 21:13:57', 1, 'assistant', 'text', NULL),
(14, 3, 'hola', 549232, '2025-03-14 19:27:35', 1, 'assistant', 'text', NULL),
(15, 3, 'hola', 549232, '2025-03-14 19:28:13', 1, 'assistant', 'text', NULL),
(16, 3, 'hola', 549232, '2025-03-14 19:28:53', 1, 'assistant', 'text', NULL),
(17, 3, 'te paso informacion', 549232, '2025-03-14 19:29:07', 1, 'assistant', 'text', NULL),
(18, 3, 'Dale', NULL, '2025-03-14 22:29:18', 1, 'user', 'text', NULL),
(19, 3, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 18, '2025-03-14 22:29:26', 1, 'assistant', 'text', NULL),
(20, 3, 'ok', 549232, '2025-03-14 19:32:15', 1, 'assistant', 'text', NULL),
(21, 3, 'Test', NULL, '2025-03-14 22:32:36', 1, 'user', 'text', NULL),
(22, 3, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 21, '2025-03-14 22:32:56', 1, 'assistant', 'text', NULL),
(23, 3, 'Hola', NULL, '2025-03-14 23:38:12', 1, 'user', 'text', NULL),
(24, 3, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 23, '2025-03-14 23:38:26', 1, 'assistant', 'text', NULL),
(25, 3, 'Hola', 549232, '2025-03-14 20:41:29', 1, 'assistant', 'text', NULL),
(26, 3, 'Qué tal', NULL, '2025-03-14 23:41:43', 1, 'user', 'text', NULL),
(27, 3, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 26, '2025-03-14 23:41:56', 1, 'assistant', 'text', NULL),
(28, 4, 'mayorista', NULL, '2025-03-14 23:43:26', 1, 'user', 'text', NULL),
(29, 4, 'HOla', 549232, '2025-03-14 20:43:36', 1, 'assistant', 'text', NULL),
(30, 4, 'HOla', 549232, '2025-03-14 20:43:37', 1, 'assistant', 'text', NULL),
(31, 4, '¡Hola! 🌟 En AP Velas estamos aquí para ayudarte. Te envío nuestro catálogo actualizado. 🕯️📦', 28, '2025-03-14 23:43:57', 1, 'assistant', 'text', NULL),
(32, 2, 'Garcia', 549232, '2025-03-14 20:50:31', 1, 'assistant', 'text', NULL),
(33, 3, 'Hola', NULL, '2025-03-15 00:10:23', 1, 'user', 'text', NULL),
(34, 3, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 33, '2025-03-15 00:10:26', 1, 'assistant', 'text', NULL),
(35, 3, 'Hola', 549232, '2025-03-14 22:21:55', 1, 'assistant', 'text', NULL),
(36, 3, 'Hola', 549232, '2025-03-14 22:21:55', 1, 'assistant', 'text', NULL),
(37, 3, 'Hi', NULL, '2025-03-15 15:23:20', 1, 'user', 'text', NULL),
(38, 3, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 37, '2025-03-15 15:23:27', 1, 'assistant', 'text', NULL),
(39, 3, 'Desde chat', 549232, '2025-03-16 18:37:25', 1, 'assistant', 'text', NULL),
(40, 3, 'Hi', NULL, '2025-03-16 21:37:57', 1, 'user', 'text', NULL),
(41, 3, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 40, '2025-03-16 21:37:58', 1, 'assistant', 'text', NULL),
(42, 3, 'Test', 549232, '2025-03-16 18:39:32', 1, 'assistant', 'text', NULL),
(43, 5, 'cuantas velas', NULL, '2025-03-16 21:41:45', 1, 'user', 'text', NULL),
(44, 5, 'Hola', NULL, '2025-03-16 21:41:45', 1, 'user', 'text', NULL),
(45, 5, '¡Hola! 🌟 En AP Velas estamos aquí para ayudarte. Te envío nuestro catálogo actualizado. 🕯️📦', 43, '2025-03-16 21:41:59', 1, 'assistant', 'text', NULL),
(46, 5, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 44, '2025-03-16 21:41:59', 1, 'assistant', 'text', NULL),
(47, 3, 'Hola', NULL, '2025-03-18 15:28:17', 1, 'user', 'text', NULL),
(48, 3, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 47, '2025-03-18 15:28:30', 1, 'assistant', 'text', NULL),
(49, 5, 'Hola', NULL, '2025-03-18 15:31:25', 1, 'user', 'text', NULL),
(50, 5, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 49, '2025-03-18 15:31:30', 1, 'assistant', 'text', NULL),
(51, 3, 'Hola', NULL, '2025-03-21 00:40:39', 1, 'user', 'text', NULL),
(52, 3, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 51, '2025-03-21 00:40:41', 1, 'assistant', 'text', NULL),
(53, 3, 'Hola', NULL, '2025-03-21 01:51:55', 1, 'user', 'text', NULL),
(54, 3, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 53, '2025-03-21 01:52:11', 1, 'assistant', 'text', NULL),
(55, 3, 'Archivo image recibido', NULL, '2025-03-21 12:48:51', 1, 'user', 'image', '/opt/APVelas/WhtasApp/2326509583/recibe/uploads/666881045837996.jpeg'),
(56, 3, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 55, '2025-03-21 12:48:53', 1, 'assistant', 'text', NULL),
(57, 3, 'Archivo image recibido', NULL, '2025-03-21 12:48:53', 1, 'user', 'image', '/opt/APVelas/WhtasApp/2326509583/recibe/uploads/666881045837996.jpeg'),
(58, 3, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 55, '2025-03-21 12:48:54', 1, 'assistant', 'text', NULL),
(59, 3, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 57, '2025-03-21 12:48:55', 1, 'assistant', 'text', NULL),
(60, 3, 'Archivo image recibido', NULL, '2025-03-21 12:54:31', 1, 'user', 'image', '/opt/APVelas/WhtasApp/2326509583/recibe/uploads/670332608785593.jpeg'),
(61, 3, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 60, '2025-03-21 12:54:33', 1, 'assistant', 'text', NULL),
(62, 3, 'Archivo image recibido', NULL, '2025-03-21 12:54:34', 1, 'user', 'image', '/opt/APVelas/WhtasApp/2326509583/recibe/uploads/670332608785593.jpeg'),
(63, 3, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 62, '2025-03-21 12:54:36', 1, 'assistant', 'text', NULL),
(64, 3, 'Archivo image recibido', NULL, '2025-03-21 12:57:31', 1, 'user', 'image', '/opt/APVelas/WhtasApp/2326509583/recibe/uploads/1363126108058328.jpeg'),
(65, 3, '¡Hola! 🌟 ¿En qué más podemos ayudarte hoy? 🕯️✨', 64, '2025-03-21 12:57:32', 1, 'assistant', 'text', NULL),
(66, 3, 'Archivo image recibido', NULL, '2025-03-21 13:04:19', 0, 'user', 'image', '/opt/APVelas/WhtasApp/2326509583/recibe/uploads/648559364535893.jpeg'),
(67, 3, 'Archivo image recibido', NULL, '2025-03-21 13:05:16', 0, 'user', 'image', '/opt/APVelas/WhtasApp/2326509583/recibe/uploads/1633867110829523.jpeg'),
(68, 3, 'Archivo audio recibido', NULL, '2025-03-21 13:05:36', 0, 'user', 'audio', '/opt/APVelas/WhtasApp/2326509583/recibe/uploads/988863643196914.ogg; codecs=opus'),
(69, 3, 'Archivo document recibido', NULL, '2025-03-21 13:08:29', 0, 'user', 'document', '/opt/APVelas/WhtasApp/2326509583/recibe/uploads/1353837545643814.pdf'),
(70, 3, 'Archivo image recibido', NULL, '2025-03-21 13:23:21', 0, 'user', 'image', '/opt/APVelas/WhtasApp/2326509583/recibe/uploads/3632107463762457.jpg');

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
(45, 4, 'marked_read', '2025-03-14 13:50:26', '2025-03-14 13:50:26'),
(46, 6, 'marked_read', '2025-03-14 19:51:27', '2025-03-14 19:51:27'),
(47, 7, 'marked_read', '2025-03-14 19:51:27', '2025-03-14 19:51:27'),
(48, 10, 'marked_read', '2025-03-14 19:52:56', '2025-03-14 19:52:56'),
(49, 12, 'marked_read', '2025-03-14 21:13:57', '2025-03-14 21:13:57'),
(50, 18, 'marked_read', '2025-03-14 22:29:26', '2025-03-14 22:29:27'),
(51, 21, 'marked_read', '2025-03-14 22:32:56', '2025-03-14 22:32:57'),
(52, 23, 'marked_read', '2025-03-14 23:38:26', '2025-03-14 23:38:27'),
(53, 26, 'marked_read', '2025-03-14 23:41:56', '2025-03-14 23:41:57'),
(54, 28, 'marked_read', '2025-03-14 23:43:57', '2025-03-14 23:43:57'),
(55, 33, 'marked_read', '2025-03-15 00:10:26', '2025-03-15 00:10:27'),
(56, 37, 'marked_read', '2025-03-15 15:23:27', '2025-03-15 15:23:28'),
(57, 40, 'marked_read', '2025-03-16 21:37:58', '2025-03-16 21:37:59'),
(58, 43, 'marked_read', '2025-03-16 21:41:59', '2025-03-16 21:41:59'),
(59, 44, 'marked_read', '2025-03-16 21:41:59', '2025-03-16 21:42:00'),
(60, 47, 'marked_read', '2025-03-18 15:28:31', '2025-03-18 15:28:32'),
(61, 49, 'marked_read', '2025-03-18 15:31:30', '2025-03-18 15:31:30'),
(62, 51, 'marked_read', '2025-03-21 00:40:42', '2025-03-21 00:40:42'),
(63, 53, 'marked_read', '2025-03-21 01:52:11', '2025-03-21 01:52:12'),
(64, 55, 'marked_read', '2025-03-21 12:48:54', '2025-03-21 12:48:55'),
(65, 57, 'marked_read', '2025-03-21 12:48:55', '2025-03-21 12:48:55'),
(66, 60, 'marked_read', '2025-03-21 12:54:33', '2025-03-21 12:54:33'),
(67, 62, 'marked_read', '2025-03-21 12:54:36', '2025-03-21 12:54:36'),
(68, 64, 'marked_read', '2025-03-21 12:57:32', '2025-03-21 12:57:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `idcliente` int NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`idcliente`, `nombre`, `apellido`, `email`, `telefono`, `direccion`) VALUES
(1, 'Ayelen', 'Paulucci', 'ayelen@apvelas.com.ar', '5492326400341', 'Saigos 1194'),
(2, 'Leticia', 'Garcia', 'leticiabracco@hotmail.com', '5492983417196', ''),
(3, 'Leonardo', 'Bracco', 'leonardo@leonardobracco.com', '5492326403502', 'Saigos 1194'),
(4, 'Catalina', 'Bracco', '', '5492326495527', ''),
(5, 'Jorge Luis', 'Paulucci', 'consultas@jlpaulucci.com.ar', '5492326422601', 'Petrilli 746');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventlog`
--

CREATE TABLE `eventlog` (
  `id` int NOT NULL,
  `timestamp` datetime NOT NULL,
  `evento` varchar(100) NOT NULL,
  `texto` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `eventlog`
--

INSERT INTO `eventlog` (`id`, `timestamp`, `evento`, `texto`) VALUES
(1, '2025-03-18 12:27:48', 'Informacion', '<b>admin</b> ha ingresado al sistema desde 181.2.177.5'),
(2, '2025-03-21 10:25:16', 'Informacion', '<b>admin</b> ha ingresado al sistema desde 181.2.174.3');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `idgrupo` int NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `grupos_permisos`
--

INSERT INTO `grupos_permisos` (`id`, `idpermiso`, `idgrupo`) VALUES
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1),
(7, 7, 1),
(8, 8, 1),
(9, 9, 1),
(10, 10, 1),
(11, 11, 1),
(12, 12, 1),
(13, 13, 1),
(14, 14, 1),
(15, 15, 1),
(16, 16, 1),
(17, 17, 1),
(18, 18, 1),
(19, 19, 1),
(20, 20, 1),
(21, 21, 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lista_precios`
--

CREATE TABLE `lista_precios` (
  `idlista` int NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lista_precio_producto`
--

CREATE TABLE `lista_precio_producto` (
  `id` int NOT NULL,
  `idlista` int NOT NULL,
  `idproducto` int NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `idpermiso` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`idpermiso`, `nombre`, `descripcion`) VALUES
(2, 'Usuarios::lectura', 'Permiso para leer información de usuarios'),
(3, 'Usuarios::grabar', 'Permiso para modificar o agregar usuarios'),
(4, 'Usuarios::borrar', 'Permiso para eliminar usuarios'),
(5, 'Usuarios::enviar', 'Permiso para enviar notificaciones a usuarios'),
(6, 'Clientes::lectura', 'Permiso para leer información de clientes'),
(7, 'Clientes::grabar', 'Permiso para modificar o agregar clientes'),
(8, 'Clientes::borrar', 'Permiso para eliminar clientes'),
(9, 'Clientes::enviar', 'Permiso para enviar mensajes a clientes'),
(10, 'Grupos::lectura', 'Permiso para leer información de grupos'),
(11, 'Grupos::grabar', 'Permiso para modificar o agregar grupos'),
(12, 'Grupos::borrar', 'Permiso para eliminar grupos'),
(13, 'Grupos::enviar', 'Permiso para enviar notificaciones a grupos'),
(14, 'Insumos::lectura', 'Permiso para leer información de insumos'),
(15, 'Insumos::grabar', 'Permiso para modificar o agregar insumos'),
(16, 'Insumos::borrar', 'Permiso para eliminar insumos'),
(17, 'Insumos::enviar', 'Permiso para enviar información sobre insumos'),
(18, 'Productos::lectura', 'Permiso para leer información de productos'),
(19, 'Productos::grabar', 'Permiso para modificar o agregar productos'),
(20, 'Productos::borrar', 'Permiso para eliminar productos'),
(21, 'Productos::enviar', 'Permiso para enviar información sobre productos');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_insumo`
--

CREATE TABLE `producto_insumo` (
  `id` int NOT NULL,
  `idproducto` int NOT NULL,
  `idinsumo` int NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `markup` decimal(5,2) NOT NULL DEFAULT '0.00',
  `markupMayorista` decimal(5,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto_insumo`
--

INSERT INTO `producto_insumo` (`id`, `idproducto`, `idinsumo`, `cantidad`, `markup`, `markupMayorista`) VALUES
(1, 1, 59, 1.00, 0.00, 0.00),
(2, 1, 48, 0.35, 0.00, 0.00),
(3, 1, 71, 10.00, 0.00, 0.00),
(4, 1, 65, 20.00, 0.00, 0.00),
(5, 1, 70, 3.00, 0.00, 0.00),
(6, 2, 65, 30.00, 0.00, 0.00),
(7, 2, 72, 1.00, 0.00, 0.00),
(8, 2, 50, 1.00, 0.00, 0.00),
(9, 2, 30, 1.00, 0.00, 0.00),
(10, 2, 31, 1.00, 0.00, 0.00),
(11, 3, 53, 1.00, 0.00, 0.00),
(12, 3, 64, 0.20, 0.00, 0.00),
(13, 3, 52, 6.00, 0.00, 0.00),
(14, 3, 51, 1.00, 0.00, 0.00),
(15, 3, 72, 1.00, 0.00, 0.00),
(16, 3, 50, 1.00, 0.00, 0.00),
(17, 4, 32, 1.00, 0.00, 0.00),
(18, 4, 48, 0.20, 0.00, 0.00),
(19, 4, 29, 0.02, 0.00, 0.00),
(20, 4, 71, 15.00, 0.00, 0.00),
(21, 4, 70, 1.00, 0.00, 0.00),
(22, 4, 65, 20.00, 0.00, 0.00),
(23, 5, 52, 1.00, 0.00, 0.00),
(24, 5, 72, 2.00, 0.00, 0.00),
(25, 5, 50, 1.00, 0.00, 0.00),
(26, 6, 28, 1.00, 0.00, 0.00),
(27, 6, 48, 0.10, 0.00, 0.00),
(28, 6, 71, 5.00, 0.00, 0.00),
(29, 6, 70, 1.00, 0.00, 0.00),
(30, 6, 72, 1.00, 0.00, 0.00),
(31, 6, 50, 1.00, 0.00, 0.00),
(32, 6, 65, 10.00, 0.00, 0.00),
(33, 7, 87, 1.00, 0.00, 0.00),
(34, 7, 48, 0.10, 0.00, 0.00),
(35, 7, 71, 5.00, 0.00, 0.00),
(36, 7, 70, 1.00, 0.00, 0.00),
(37, 7, 65, 10.00, 0.00, 0.00),
(38, 7, 50, 1.00, 0.00, 0.00),
(39, 8, 79, 1.00, 0.00, 0.00),
(40, 8, 48, 0.60, 0.00, 0.00),
(41, 8, 71, 10.00, 0.00, 0.00),
(42, 8, 70, 2.00, 0.00, 0.00),
(43, 8, 72, 2.00, 0.00, 0.00),
(44, 8, 50, 1.00, 0.00, 0.00),
(45, 8, 65, 60.00, 0.00, 0.00),
(46, 9, 33, 1.00, 0.00, 0.00),
(47, 9, 51, 1.00, 0.00, 0.00),
(48, 10, 57, 1.00, 0.00, 0.00),
(49, 10, 48, 0.20, 0.00, 0.00),
(50, 10, 71, 10.00, 0.00, 0.00),
(51, 10, 70, 2.00, 0.00, 0.00),
(52, 10, 50, 1.00, 0.00, 0.00),
(53, 10, 65, 20.00, 0.00, 0.00),
(54, 11, 49, 1.00, 0.00, 0.00),
(55, 11, 50, 1.00, 0.00, 0.00),
(56, 11, 72, 1.00, 0.00, 0.00),
(57, 12, 58, 1.00, 0.00, 0.00),
(58, 12, 48, 0.25, 0.00, 0.00),
(59, 12, 71, 15.00, 0.00, 0.00),
(60, 12, 70, 3.00, 0.00, 0.00),
(61, 12, 72, 1.00, 0.00, 0.00),
(62, 12, 50, 1.00, 0.00, 0.00),
(63, 12, 65, 25.00, 0.00, 0.00),
(64, 13, 52, 6.00, 0.00, 0.00),
(65, 14, 35, 1.00, 0.00, 0.00),
(66, 14, 48, 0.08, 0.00, 0.00),
(67, 14, 71, 1.00, 0.00, 0.00),
(68, 14, 50, 1.00, 0.00, 0.00),
(69, 14, 72, 2.00, 0.00, 0.00),
(70, 14, 65, 8.00, 0.00, 0.00),
(71, 14, 70, 1.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos`
--

CREATE TABLE `tipos` (
  `idtipo` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipos`
--

INSERT INTO `tipos` (`idtipo`, `nombre`) VALUES
(5, 'accesorio'),
(2, 'difusor'),
(4, 'jabon'),
(3, 'perfumina'),
(1, 'vela');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades_medida`
--

CREATE TABLE `unidades_medida` (
  `idunidad` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `unidades_medida`
--

INSERT INTO `unidades_medida` (`idunidad`, `nombre`) VALUES
(3, 'cc'),
(4, 'cm'),
(1, 'kg'),
(2, 'litro'),
(5, 'metro'),
(6, 'unidad');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios_grupos`
--

INSERT INTO `usuarios_grupos` (`id`, `idusuario`, `idgrupo`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_clientes_chat`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_clientes_chat` (
`apellido` varchar(255)
,`idcliente` int
,`last_message` varchar(19)
,`mensaje` mediumtext
,`nombre` varchar(255)
,`telefono` varchar(20)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_clientes_ordenados`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_clientes_ordenados` (
`apellido` varchar(255)
,`idcliente` int
,`last_message` timestamp
,`mensaje` text
,`nombre` varchar(255)
,`telefono` varchar(20)
);

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
  ADD UNIQUE KEY `telefono` (`telefono`),
  ADD UNIQUE KEY `email` (`email`);

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
  ADD PRIMARY KEY (`idinsumo`),
  ADD KEY `fk_insumos_unidad` (`idunidad`);

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
-- Indices de la tabla `tipos`
--
ALTER TABLE `tipos`
  ADD PRIMARY KEY (`idtipo`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  ADD PRIMARY KEY (`idunidad`),
  ADD UNIQUE KEY `nombre` (`nombre`);

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
  MODIFY `idchat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de la tabla `chats_audit`
--
ALTER TABLE `chats_audit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `idcliente` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `eventlog`
--
ALTER TABLE `eventlog`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `idgrupo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `grupos_permisos`
--
ALTER TABLE `grupos_permisos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `insumos`
--
ALTER TABLE `insumos`
  MODIFY `idinsumo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

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
  MODIFY `idpermiso` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `idproducto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `producto_insumo`
--
ALTER TABLE `producto_insumo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de la tabla `tipos`
--
ALTER TABLE `tipos`
  MODIFY `idtipo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  MODIFY `idunidad` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_clientes_chat`
--
DROP TABLE IF EXISTS `vista_clientes_chat`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_clientes_chat`  AS SELECT `c`.`idcliente` AS `idcliente`, `c`.`nombre` AS `nombre`, `c`.`apellido` AS `apellido`, `c`.`telefono` AS `telefono`, coalesce(`ch`.`mensaje`,'Sin mensajes') AS `mensaje`, coalesce(`ch`.`last_message`,'0000-00-00 00:00:00') AS `last_message` FROM (`clientes` `c` left join (select `ch1`.`idcliente` AS `idcliente`,max(`ch1`.`timestamp`) AS `last_message`,substring_index(group_concat(`ch1`.`mensaje` order by `ch1`.`timestamp` DESC separator '||'),'||',1) AS `mensaje` from `chats` `ch1` group by `ch1`.`idcliente`) `ch` on((`c`.`idcliente` = `ch`.`idcliente`))) ORDER BY `ch`.`last_message` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_clientes_ordenados`
--
DROP TABLE IF EXISTS `vista_clientes_ordenados`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_clientes_ordenados`  AS SELECT `c`.`idcliente` AS `idcliente`, `c`.`nombre` AS `nombre`, `c`.`apellido` AS `apellido`, `c`.`telefono` AS `telefono`, `ch`.`mensaje` AS `mensaje`, `ch`.`last_message` AS `last_message` FROM (`clientes` `c` join (select `ch1`.`idcliente` AS `idcliente`,`ch1`.`mensaje` AS `mensaje`,`ch1`.`timestamp` AS `last_message` from `chats` `ch1` where (`ch1`.`timestamp` = (select max(`ch2`.`timestamp`) from `chats` `ch2` where (`ch1`.`idcliente` = `ch2`.`idcliente`)))) `ch` on((`c`.`idcliente` = `ch`.`idcliente`))) ORDER BY `ch`.`last_message` DESC ;

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
-- Filtros para la tabla `insumos`
--
ALTER TABLE `insumos`
  ADD CONSTRAINT `fk_insumos_unidad` FOREIGN KEY (`idunidad`) REFERENCES `unidades_medida` (`idunidad`);

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
  ADD CONSTRAINT `producto_insumo_ibfk_1` FOREIGN KEY (`idproducto`) REFERENCES `productos` (`idproducto`),
  ADD CONSTRAINT `producto_insumo_ibfk_2` FOREIGN KEY (`idinsumo`) REFERENCES `insumos` (`idinsumo`);

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
