-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-03-2026 a las 05:13:34
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ventas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `bitacora_id` int(10) NOT NULL,
  `usuario_id` int(7) NOT NULL,
  `bitacora_fecha` date NOT NULL,
  `bitacora_hora` varchar(20) NOT NULL,
  `bitacora_modulo` varchar(50) NOT NULL,
  `bitacora_accion` varchar(50) NOT NULL,
  `bitacora_descripcion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `bitacora`
--

INSERT INTO `bitacora` (`bitacora_id`, `usuario_id`, `bitacora_fecha`, `bitacora_hora`, `bitacora_modulo`, `bitacora_accion`, `bitacora_descripcion`) VALUES
(1, 1, '2026-03-11', '05:32:17 pm', 'Seguridad', 'Cierre de Sesión', 'El usuario Administrador salió del sistema.'),
(2, 1, '2026-03-11', '05:32:29 pm', 'Seguridad', 'Inicio de Sesión', 'El usuario Administrador entró al sistema.'),
(3, 1, '2026-03-11', '05:35:25 pm', 'Categorías', 'Registro', 'Se registró la categoría: Computación'),
(4, 1, '2026-03-11', '05:36:14 pm', 'Categorías', 'Registro', 'Se registró la categoría: Laptops'),
(5, 1, '2026-03-11', '05:38:19 pm', 'Productos', 'Registro', 'Se registró el producto: Laptop HP 14-EP2012WM | Intel N150 (Cód: 4631283426491)'),
(6, 1, '2026-03-11', '05:39:12 pm', 'Productos', 'Registro', 'Se registró el producto: Laptop HP 15-fd0095wm | Intel Core i5 12va Gen (Cód: 0958230752183)'),
(7, 1, '2026-03-11', '05:39:55 pm', 'Productos', 'Registro', 'Se registró el producto: Laptop HP 15-fc0037la | AMD Ryzen 5 7520U (Cód: 0568057689758)'),
(8, 1, '2026-03-11', '05:40:50 pm', 'Productos', 'Registro', 'Se registró el producto: Laptop Gamer HP Victus 15-fa2013dx | Intel Core i5 13va Gen (Cód: 8972323789782)'),
(9, 1, '2026-03-11', '05:42:03 pm', 'Productos', 'Registro', 'Se registró el producto: Laptop Acer Aspire Go 15 | Intel Core i5 13va Gen (Cód: 1231293891038)'),
(10, 1, '2026-03-11', '05:42:55 pm', 'Productos', 'Registro', 'Se registró el producto: Laptop Gamer Acer Predator Helios Neo 14 | Intel Core Ultra 7 155H (Cód: 5678956789567)'),
(11, 1, '2026-03-11', '05:43:49 pm', 'Productos', 'Registro', 'Se registró el producto: Laptop Gamer MSI Katana 15 HX B14WGK-016US | Intel Core i9 14va Gen (Cód: 5689567577838)'),
(12, 1, '2026-03-11', '05:44:33 pm', 'Productos', 'Registro', 'Se registró el producto: Laptop Gamer MSI Katana 15 HX B14WGK-293US | Intel Core i7 14va Gen (Cód: 9029419824078)'),
(13, 1, '2026-03-11', '05:45:10 pm', 'Productos', 'Registro', 'Se registró el producto: Laptop Gamer MSI Thin 15 B13UC | Intel Core i5 13va Gen (Cód: 0976509034860)'),
(14, 1, '2026-03-11', '05:45:41 pm', 'Categorías', 'Registro', 'Se registró la categoría: Monitores'),
(15, 1, '2026-03-11', '05:46:37 pm', 'Productos', 'Registro', 'Se registró el producto: Monitor MSI PRO MP243XW 24\" Full HD 100Hz Altavoces Integrados (Cód: 1990456809809)'),
(16, 1, '2026-03-11', '05:49:49 pm', 'Categorías', 'Registro', 'Se registró la categoría: All in one'),
(17, 1, '2026-03-11', '05:50:36 pm', 'Productos', 'Registro', 'Se registró el producto: All In One MSI PRO AP162T | Intel N100 (Serie N) (Cód: 9825784028740)'),
(18, 1, '2026-03-11', '05:57:06 pm', 'Categorías', 'Registro', 'Se registró la categoría: PC de escritorio'),
(19, 1, '2026-03-11', '05:57:21 pm', 'Categorías', 'Eliminación', 'Se eliminó la categoría: PC de escritorio'),
(20, 1, '2026-03-11', '05:58:37 pm', 'Categorías', 'Registro', 'Se registró la categoría: PC de escritorio'),
(21, 1, '2026-03-11', '05:59:34 pm', 'Productos', 'Registro', 'Se registró el producto: PC ConAstron Design Master | AMD Ryzen 7 5700 RTX 3060 12GB (Cód: 7897856854745)'),
(22, 1, '2026-03-11', '06:00:24 pm', 'Productos', 'Registro', 'Se registró el producto: HP Pavilion TP01-1247c | Intel Core i5 10ma Gen (Cód: 0921887421789)'),
(23, 1, '2026-03-11', '06:01:21 pm', 'Productos', 'Registro', 'Se registró el producto: HP Slim Desktop S01-PF2033w | Intel Core i7 12va Gen (Cód: 1031859765467)'),
(24, 1, '2026-03-11', '06:06:53 pm', 'Categorías', 'Registro', 'Se registró la categoría: Impresión y oficina'),
(25, 1, '2026-03-11', '06:07:13 pm', 'Categorías', 'Registro', 'Se registró la categoría: Consumibles'),
(26, 1, '2026-03-11', '06:10:30 pm', 'Productos', 'Registro', 'Se registró el producto: Tinta Original HP GT52/GT53 Colores (Cyan, Magenta, Amarillo, Negro) p (Cód: 7979780789067)'),
(27, 1, '2026-03-11', '06:12:42 pm', 'Productos', 'Registro', 'Se registró el producto: Pasta Térmica Gamemax TG3 de Alto Rendimiento (Cód: 1340917596834)'),
(28, 1, '2026-03-11', '06:13:15 pm', 'Categorías', 'Registro', 'Se registró la categoría: Componentes de PC'),
(29, 1, '2026-03-11', '06:28:09 pm', 'Categorías', 'Registro', 'Se registró la categoría: Unidades de almacenamiento'),
(30, 1, '2026-03-11', '06:30:02 pm', 'Productos', 'Registro', 'Se registró el producto: Disco Duro Interno 3.5 Seagate BarraCuda 2TB SATA 6Gb/s 7200 RPM (Cód: 1080971203412)'),
(31, 1, '2026-03-11', '06:30:50 pm', 'Categorías', 'Registro', 'Se registró la categoría: Gabinetes'),
(32, 1, '2026-03-11', '06:31:50 pm', 'Productos', 'Registro', 'Se registró el producto: Case Gamer REDRAGON Wheeljack GC-606BK Mid-Tower (Cód: 2034278573495)'),
(33, 1, '2026-03-11', '06:32:41 pm', 'Productos', 'Registro', 'Se registró el producto: Case Gamer GameMax Vista Mid-Tower Panoramic Glass Dual Chamber ARGB (Cód: 2498234826394)'),
(34, 1, '2026-03-11', '06:33:32 pm', 'Productos', 'Registro', 'Se registró el producto: Case Gamer GameMax Diamond COC Black Mid-Tower ARGB (Cód: 5394729649821)'),
(35, 1, '2026-03-11', '06:34:28 pm', 'Categorías', 'Registro', 'Se registró la categoría: Periféricos'),
(36, 1, '2026-03-11', '06:34:47 pm', 'Categorías', 'Registro', 'Se registró la categoría: Audífonos'),
(37, 1, '2026-03-11', '06:35:31 pm', 'Productos', 'Registro', 'Se registró el producto: Audífonos Argom Tech Dynamic 63 ARG-HS-0063 USB con Micrófono (Cód: 4021947928125)'),
(38, 1, '2026-03-11', '06:37:00 pm', 'Productos', 'Registro', 'Se registró el producto: Audífonos In-Ear Genius HS-M320 con Micrófono y Conector 3.5mm (Cód: 2398782173109)'),
(39, 1, '2026-03-13', '11:07:54 pm', 'Productos', 'Registro', 'Se registró el producto: Arasdfad (Inicia con stock 0)'),
(40, 1, '2026-03-13', '11:08:08 pm', 'Productos', 'Actualización', 'Datos actualizados del producto: 1321321'),
(41, 1, '2026-03-13', '11:23:19 pm', 'Proveedores', 'Registro', 'Se registró el proveedor: Conputodo (RIF: 71281237-2)'),
(42, 1, '2026-03-14', '12:33:53 am', 'Productos', 'Registro', 'Se registró el producto: Laptop lenovo (Inicia con stock 0)'),
(43, 1, '2026-03-14', '05:51:20 pm', 'Seguridad', 'Inicio de Sesión', 'El usuario Administrador entró al sistema.'),
(44, 1, '2026-03-14', '06:30:30 pm', 'Productos', 'Registro', 'Se registró el producto: Avion de guerra (Inicia con stock 0)'),
(45, 1, '2026-03-14', '11:11:04 pm', 'Productos', 'Registro', 'Se registró el producto: Laptop gamer (Inicia con stock 0)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja`
--

CREATE TABLE `caja` (
  `caja_id` int(5) NOT NULL,
  `caja_numero` int(5) NOT NULL,
  `caja_nombre` varchar(100) NOT NULL,
  `caja_efectivo` decimal(30,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `caja`
--

INSERT INTO `caja` (`caja_id`, `caja_numero`, `caja_nombre`, `caja_efectivo`) VALUES
(1, 1, 'Caja Principal', 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `categoria_id` int(7) NOT NULL,
  `categoria_nombre` varchar(50) NOT NULL,
  `categoria_padre_id` int(11) DEFAULT NULL,
  `categoria_ubicacion` varchar(150) NOT NULL,
  `categoria_unidades` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`categoria_id`, `categoria_nombre`, `categoria_padre_id`, `categoria_ubicacion`, `categoria_unidades`) VALUES
(1, 'Computación', NULL, '', NULL),
(2, 'Laptops', 1, '', NULL),
(3, 'Monitores', 1, '', NULL),
(4, 'All in one', 1, '', NULL),
(6, 'PC de escritorio', 1, '', NULL),
(7, 'Impresión y oficina', NULL, '', NULL),
(8, 'Consumibles', 7, '', NULL),
(9, 'Componentes de PC', NULL, '', NULL),
(10, 'Unidades de almacenamiento', 9, '', NULL),
(11, 'Gabinetes', 9, '', NULL),
(12, 'Periféricos', NULL, '', NULL),
(13, 'Audífonos', 12, '', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `cliente_id` int(10) NOT NULL,
  `cliente_tipo_documento` varchar(20) NOT NULL,
  `cliente_numero_documento` varchar(35) NOT NULL,
  `cliente_nombre` varchar(50) NOT NULL,
  `cliente_apellido` varchar(50) NOT NULL,
  `cliente_provincia` varchar(30) NOT NULL,
  `cliente_ciudad` varchar(30) NOT NULL,
  `cliente_direccion` varchar(70) NOT NULL,
  `cliente_telefono` varchar(20) NOT NULL,
  `cliente_email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`cliente_id`, `cliente_tipo_documento`, `cliente_numero_documento`, `cliente_nombre`, `cliente_apellido`, `cliente_provincia`, `cliente_ciudad`, `cliente_direccion`, `cliente_telefono`, `cliente_email`) VALUES
(1, 'Otro', 'N/A', 'Publico', 'General', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra`
--

CREATE TABLE `compra` (
  `compra_id` int(11) NOT NULL,
  `compra_codigo` varchar(50) NOT NULL,
  `compra_fecha` date NOT NULL,
  `compra_total` decimal(30,2) NOT NULL,
  `compra_tasa_bcv` decimal(20,2) NOT NULL DEFAULT 0.00,
  `usuario_id` int(7) NOT NULL,
  `proveedor_id` int(11) NOT NULL,
  `compra_estado` varchar(20) DEFAULT 'Pendiente',
  `compra_nota_interna` text DEFAULT NULL,
  `compra_saldo_pendiente` decimal(30,2) NOT NULL,
  `compra_estado_pago` enum('Pendiente','Parcial','Pagado') DEFAULT 'Pendiente',
  `compra_fecha_vencimiento` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `compra`
--

INSERT INTO `compra` (`compra_id`, `compra_codigo`, `compra_fecha`, `compra_total`, `compra_tasa_bcv`, `usuario_id`, `proveedor_id`, `compra_estado`, `compra_nota_interna`, `compra_saldo_pendiente`, `compra_estado_pago`, `compra_fecha_vencimiento`) VALUES
(1, 'COM-000001', '2026-03-14', 6300.00, 446.80, 1, 1, 'Parcial', '', 6300.00, 'Pendiente', '2026-03-27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra_detalle`
--

CREATE TABLE `compra_detalle` (
  `compra_detalle_id` int(11) NOT NULL,
  `compra_id` int(11) NOT NULL,
  `producto_id` int(20) NOT NULL,
  `compra_detalle_cantidad` int(10) NOT NULL,
  `compra_detalle_precio` decimal(30,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `compra_detalle`
--

INSERT INTO `compra_detalle` (`compra_detalle_id`, `compra_id`, `producto_id`, `compra_detalle_cantidad`, `compra_detalle_precio`) VALUES
(1, 1, 1, 9, 700.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra_pagos`
--

CREATE TABLE `compra_pagos` (
  `pago_id` int(11) NOT NULL,
  `compra_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `pago_fecha` date NOT NULL,
  `pago_monto` decimal(30,2) NOT NULL,
  `pago_metodo` enum('Efectivo','Transferencia','Divisas','Debito') NOT NULL,
  `pago_referencia` varchar(100) DEFAULT NULL,
  `pago_nota` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `empresa_id` int(11) NOT NULL,
  `empresa_nombre` varchar(90) NOT NULL,
  `empresa_rif` varchar(40) NOT NULL,
  `empresa_telefono` varchar(20) NOT NULL,
  `empresa_emailKV` varchar(50) NOT NULL,
  `empresa_direccion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`empresa_id`, `empresa_nombre`, `empresa_rif`, `empresa_telefono`, `empresa_emailKV`, `empresa_direccion`) VALUES
(1, 'Fasnet Lideres en Tecnología', 'J-29665886-2', '04127465438', 'Fasnet.comunicaciones@gmail.com', 'Av. Anthons Phillips cc Av Merida local galpon Nro Sn Zona Industrial La Hamaca');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `producto_id` int(20) NOT NULL,
  `producto_codigo` varchar(77) NOT NULL,
  `producto_nombre` varchar(100) NOT NULL,
  `producto_stock_total` int(25) NOT NULL,
  `producto_tipo_unidad` varchar(20) NOT NULL,
  `producto_precio_compra` decimal(30,2) NOT NULL,
  `producto_precio_venta` decimal(30,2) NOT NULL,
  `producto_marca` varchar(35) NOT NULL,
  `producto_modelo` varchar(35) NOT NULL,
  `producto_estado` varchar(20) NOT NULL,
  `producto_foto` varchar(500) NOT NULL,
  `categoria_id` int(7) NOT NULL,
  `producto_costo` decimal(30,2) NOT NULL DEFAULT 0.00,
  `producto_stock_min` int(10) NOT NULL DEFAULT 5,
  `producto_stock_max` int(10) NOT NULL DEFAULT 100,
  `producto_precio` decimal(30,2) NOT NULL,
  `producto_stock` int(25) NOT NULL,
  `producto_unidad` varchar(100) NOT NULL,
  `producto_unidades_caja` int(10) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`producto_id`, `producto_codigo`, `producto_nombre`, `producto_stock_total`, `producto_tipo_unidad`, `producto_precio_compra`, `producto_precio_venta`, `producto_marca`, `producto_modelo`, `producto_estado`, `producto_foto`, `categoria_id`, `producto_costo`, `producto_stock_min`, `producto_stock_max`, `producto_precio`, `producto_stock`, `producto_unidad`, `producto_unidades_caja`) VALUES
(1, '2498217649872', 'Laptop gamer', 0, '', 0.00, 0.00, 'HP', 'I9 9700k', 'Activo', '', 2, 700.00, 5, 100, 840.00, 5, 'Unidad', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `proveedor_id` int(11) NOT NULL,
  `proveedor_nombre` varchar(100) NOT NULL,
  `proveedor_rif` varchar(30) NOT NULL,
  `proveedor_telefono` varchar(20) DEFAULT NULL,
  `proveedor_direccion` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`proveedor_id`, `proveedor_nombre`, `proveedor_rif`, `proveedor_telefono`, `proveedor_direccion`) VALUES
(1, 'Conputodo', '71281237-2', '2236789', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recepcion`
--

CREATE TABLE `recepcion` (
  `recepcion_id` int(11) NOT NULL,
  `compra_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `recepcion_fecha` date NOT NULL,
  `recepcion_nota` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recepcion`
--

INSERT INTO `recepcion` (`recepcion_id`, `compra_id`, `usuario_id`, `recepcion_fecha`, `recepcion_nota`) VALUES
(1, 4, 1, '2026-03-14', NULL),
(2, 4, 1, '2026-03-14', NULL),
(3, 4, 1, '2026-03-14', NULL),
(4, 4, 1, '2026-03-14', NULL),
(5, 5, 1, '2026-03-14', NULL),
(6, 6, 1, '2026-03-14', NULL),
(7, 16, 1, '2026-03-14', NULL),
(8, 16, 1, '2026-03-14', NULL),
(9, 1, 1, '2026-03-14', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recepcion_detalle`
--

CREATE TABLE `recepcion_detalle` (
  `recepcion_detalle_id` int(11) NOT NULL,
  `recepcion_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad_recibida` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recepcion_detalle`
--

INSERT INTO `recepcion_detalle` (`recepcion_detalle_id`, `recepcion_id`, `producto_id`, `cantidad_recibida`) VALUES
(3, 3, 23, 10),
(4, 5, 24, 7),
(5, 6, 25, 7),
(6, 7, 23, 9),
(7, 9, 1, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `rol_id` int(11) NOT NULL,
  `rol_nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`rol_id`, `rol_nombre`) VALUES
(1, 'Administrador'),
(2, 'Vendedor'),
(3, 'Supervisor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `usuario_id` int(7) NOT NULL,
  `usuario_nombre` varchar(50) NOT NULL,
  `usuario_apellido` varchar(50) NOT NULL,
  `usuario_email` varchar(50) NOT NULL,
  `usuario_usuario` varchar(30) NOT NULL,
  `usuario_clave` varchar(535) NOT NULL,
  `usuario_foto` varchar(200) NOT NULL,
  `caja_id` int(5) NOT NULL,
  `rol_id` int(11) NOT NULL DEFAULT 2,
  `usuario_estado` varchar(20) DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`usuario_id`, `usuario_nombre`, `usuario_apellido`, `usuario_email`, `usuario_usuario`, `usuario_clave`, `usuario_foto`, `caja_id`, `rol_id`, `usuario_estado`) VALUES
(1, 'Administrador', 'Principal', 'Administrador@gmail.com', 'Administrador', '$2y$10$Jgm6xFb5Onz/BMdIkNK2Tur8yg/NYEMb/tdnhoV7kB1BwIG4R05D2', '', 1, 1, 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--

CREATE TABLE `venta` (
  `venta_id` int(30) NOT NULL,
  `venta_codigo` varchar(200) NOT NULL,
  `venta_fecha` date NOT NULL,
  `venta_hora` varchar(17) NOT NULL,
  `venta_total` decimal(30,2) NOT NULL,
  `venta_pagado` decimal(30,2) NOT NULL,
  `venta_cambio` decimal(30,2) NOT NULL,
  `venta_tasa_bcv` decimal(20,2) NOT NULL DEFAULT 0.00,
  `usuario_id` int(7) NOT NULL,
  `cliente_id` int(10) NOT NULL,
  `caja_id` int(5) NOT NULL,
  `venta_metodo_pago` varchar(30) NOT NULL DEFAULT 'Efectivo',
  `venta_referencia` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_detalle`
--

CREATE TABLE `venta_detalle` (
  `venta_detalle_id` int(100) NOT NULL,
  `venta_detalle_cantidad` int(10) NOT NULL,
  `venta_detalle_precio_compra` decimal(30,2) NOT NULL,
  `venta_detalle_precio_venta` decimal(30,2) NOT NULL,
  `venta_detalle_total` decimal(30,2) NOT NULL,
  `venta_detalle_descripcion` varchar(200) NOT NULL,
  `venta_codigo` varchar(200) NOT NULL,
  `producto_id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`bitacora_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `caja`
--
ALTER TABLE `caja`
  ADD PRIMARY KEY (`caja_id`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`categoria_id`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`cliente_id`);

--
-- Indices de la tabla `compra`
--
ALTER TABLE `compra`
  ADD PRIMARY KEY (`compra_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `proveedor_id` (`proveedor_id`);

--
-- Indices de la tabla `compra_detalle`
--
ALTER TABLE `compra_detalle`
  ADD PRIMARY KEY (`compra_detalle_id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `fk_detalle_compra` (`compra_id`);

--
-- Indices de la tabla `compra_pagos`
--
ALTER TABLE `compra_pagos`
  ADD PRIMARY KEY (`pago_id`),
  ADD KEY `compra_id` (`compra_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`empresa_id`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`producto_id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`proveedor_id`);

--
-- Indices de la tabla `recepcion`
--
ALTER TABLE `recepcion`
  ADD PRIMARY KEY (`recepcion_id`),
  ADD KEY `compra_id` (`compra_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `recepcion_detalle`
--
ALTER TABLE `recepcion_detalle`
  ADD PRIMARY KEY (`recepcion_detalle_id`),
  ADD KEY `recepcion_id` (`recepcion_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`rol_id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`usuario_id`),
  ADD KEY `caja_id` (`caja_id`),
  ADD KEY `fk_usuario_rol` (`rol_id`);

--
-- Indices de la tabla `venta`
--
ALTER TABLE `venta`
  ADD PRIMARY KEY (`venta_id`),
  ADD UNIQUE KEY `venta_codigo` (`venta_codigo`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `caja_id` (`caja_id`);

--
-- Indices de la tabla `venta_detalle`
--
ALTER TABLE `venta_detalle`
  ADD PRIMARY KEY (`venta_detalle_id`),
  ADD KEY `venta_id` (`venta_codigo`),
  ADD KEY `producto_id` (`producto_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `bitacora_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `caja`
--
ALTER TABLE `caja`
  MODIFY `caja_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `categoria_id` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `cliente_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `compra`
--
ALTER TABLE `compra`
  MODIFY `compra_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `compra_detalle`
--
ALTER TABLE `compra_detalle`
  MODIFY `compra_detalle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `compra_pagos`
--
ALTER TABLE `compra_pagos`
  MODIFY `pago_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `empresa_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `producto_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `proveedor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `recepcion`
--
ALTER TABLE `recepcion`
  MODIFY `recepcion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `recepcion_detalle`
--
ALTER TABLE `recepcion_detalle`
  MODIFY `recepcion_detalle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `rol_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `usuario_id` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `venta`
--
ALTER TABLE `venta`
  MODIFY `venta_id` int(30) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `venta_detalle`
--
ALTER TABLE `venta_detalle`
  MODIFY `venta_detalle_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `bitacora_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);

--
-- Filtros para la tabla `compra`
--
ALTER TABLE `compra`
  ADD CONSTRAINT `compra_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`),
  ADD CONSTRAINT `compra_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedor` (`proveedor_id`);

--
-- Filtros para la tabla `compra_detalle`
--
ALTER TABLE `compra_detalle`
  ADD CONSTRAINT `compra_detalle_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`),
  ADD CONSTRAINT `fk_detalle_compra` FOREIGN KEY (`compra_id`) REFERENCES `compra` (`compra_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compra_pagos`
--
ALTER TABLE `compra_pagos`
  ADD CONSTRAINT `compra_pagos_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compra` (`compra_id`),
  ADD CONSTRAINT `compra_pagos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`categoria_id`);

--
-- Filtros para la tabla `recepcion`
--
ALTER TABLE `recepcion`
  ADD CONSTRAINT `recepcion_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compra` (`compra_id`),
  ADD CONSTRAINT `recepcion_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);

--
-- Filtros para la tabla `recepcion_detalle`
--
ALTER TABLE `recepcion_detalle`
  ADD CONSTRAINT `recepcion_detalle_ibfk_1` FOREIGN KEY (`recepcion_id`) REFERENCES `recepcion` (`recepcion_id`),
  ADD CONSTRAINT `recepcion_detalle_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_caja` FOREIGN KEY (`caja_id`) REFERENCES `caja` (`caja_id`),
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`rol_id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `venta`
--
ALTER TABLE `venta`
  ADD CONSTRAINT `venta_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`),
  ADD CONSTRAINT `venta_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`cliente_id`),
  ADD CONSTRAINT `venta_ibfk_3` FOREIGN KEY (`caja_id`) REFERENCES `caja` (`caja_id`);

--
-- Filtros para la tabla `venta_detalle`
--
ALTER TABLE `venta_detalle`
  ADD CONSTRAINT `venta_detalle_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`),
  ADD CONSTRAINT `venta_detalle_ibfk_3` FOREIGN KEY (`venta_codigo`) REFERENCES `venta` (`venta_codigo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
