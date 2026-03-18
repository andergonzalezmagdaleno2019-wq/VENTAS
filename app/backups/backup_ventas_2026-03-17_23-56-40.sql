-- --------------------------------------------------------
-- Respaldo del Sistema de Ventas
-- Fecha de generación: 2026-03-17 23:56:40
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `caja`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `caja`;
CREATE TABLE `caja` (
  `caja_id` int(5) NOT NULL AUTO_INCREMENT,
  `caja_numero` int(5) NOT NULL,
  `caja_nombre` varchar(100) NOT NULL,
  `caja_efectivo` decimal(30,2) NOT NULL,
  PRIMARY KEY (`caja_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `caja`
INSERT INTO `caja` VALUES
("1", "1", "Caja Principal", "0.00");

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `rol`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `rol`;
CREATE TABLE `rol` (
  `rol_id` int(11) NOT NULL AUTO_INCREMENT,
  `rol_nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`rol_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `rol`
INSERT INTO `rol` VALUES
("1", "Administrador"),
("2", "Vendedor"),
("3", "Supervisor");

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `usuario`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `usuario_id` int(7) NOT NULL AUTO_INCREMENT,
  `usuario_tipo_documento` varchar(7) NOT NULL DEFAULT 'V',
  `usuario_dni` varchar(30) NOT NULL DEFAULT '0',
  `usuario_nombre` varchar(50) NOT NULL,
  `usuario_apellido` varchar(50) NOT NULL,
  `usuario_email` varchar(50) NOT NULL,
  `usuario_usuario` varchar(30) NOT NULL,
  `usuario_clave` varchar(535) NOT NULL,
  `usuario_foto` varchar(200) NOT NULL,
  `caja_id` int(5) NOT NULL,
  `rol_id` int(11) NOT NULL DEFAULT 2,
  `usuario_pregunta_1` varchar(150) DEFAULT NULL,
  `usuario_respuesta_1` varchar(150) DEFAULT NULL,
  `usuario_pregunta_2` varchar(150) DEFAULT NULL,
  `usuario_respuesta_2` varchar(150) DEFAULT NULL,
  `usuario_pregunta_3` varchar(150) DEFAULT NULL,
  `usuario_respuesta_3` varchar(150) DEFAULT NULL,
  `usuario_estado` varchar(20) DEFAULT 'Activo',
  PRIMARY KEY (`usuario_id`),
  KEY `caja_id` (`caja_id`),
  KEY `fk_usuario_rol` (`rol_id`),
  CONSTRAINT `fk_usuario_caja` FOREIGN KEY (`caja_id`) REFERENCES `caja` (`caja_id`),
  CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`rol_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `usuario`
INSERT INTO `usuario` VALUES
("1", "V", "0", "Administrador", "Principal", "Administrador@gmail.com", "Administrador", "$2y$10$Jgm6xFb5Onz/BMdIkNK2Tur8yg/NYEMb/tdnhoV7kB1BwIG4R05D2", "", "1", "1", NULL, NULL, NULL, NULL, NULL, NULL, "Activo"),
("5", "V", "32600641", "Ander alexander", "Peña gonzalez", "ander@gmail.com", "Andflizzz", "$2y$10$hBGEXJUfeBngsSoVtuS/m.pbYohmSN/R0nuabLT3vS2qitvMsTgCu", "", "1", "2", "Ciudad de nacimiento", "Maracay", "Color favorito", "Negro", "Nombre de tu abuelo", "Jose", "Activo");

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `bitacora`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `bitacora`;
CREATE TABLE `bitacora` (
  `bitacora_id` int(10) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(7) NOT NULL,
  `bitacora_fecha` date NOT NULL,
  `bitacora_hora` varchar(20) NOT NULL,
  `bitacora_modulo` varchar(50) NOT NULL,
  `bitacora_accion` varchar(50) NOT NULL,
  `bitacora_descripcion` text NOT NULL,
  PRIMARY KEY (`bitacora_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `bitacora_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `bitacora`
INSERT INTO `bitacora` VALUES
("1", "1", "2026-03-15", "11:40:03 am", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("2", "1", "2026-03-15", "12:10:26 pm", "Productos", "Actualización", "Datos actualizados del producto: Laptop gamer"),
("3", "1", "2026-03-15", "06:47:47 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("4", "1", "2026-03-15", "06:59:29 pm", "Categorías", "Registro", "Se registró la categoría: Computación"),
("5", "1", "2026-03-15", "06:59:56 pm", "Categorías", "Registro", "Se registró la categoría: Laptops"),
("6", "1", "2026-03-15", "07:01:59 pm", "Categorías", "Eliminación", "Se eliminó la categoría: Laptops"),
("7", "1", "2026-03-15", "07:02:14 pm", "Categorías", "Registro", "Se registró la categoría: Laptops"),
("8", "1", "2026-03-15", "07:02:57 pm", "Productos", "Registro", "Se registró el producto: Laptop Dell Latitude 5500 (Refurbished) | Intel Core i5 8va Gen (Inicia con stock 0)"),
("9", "1", "2026-03-15", "09:37:04 pm", "Sistema", "Backup", "Se generó una copia de seguridad: backup_ventas_2026-03-15_21-37-04.sql"),
("10", "1", "2026-03-16", "06:58:35 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("11", "1", "2026-03-16", "07:13:51 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("14", "1", "2026-03-16", "08:10:46 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("15", "1", "2026-03-16", "08:11:36 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("16", "1", "2026-03-16", "08:19:55 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("17", "1", "2026-03-16", "08:26:39 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("18", "1", "2026-03-16", "08:29:04 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("19", "1", "2026-03-16", "08:29:14 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("20", "1", "2026-03-16", "08:29:40 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("21", "1", "2026-03-16", "08:30:40 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("22", "1", "2026-03-16", "08:33:55 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("23", "1", "2026-03-16", "08:37:03 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("24", "1", "2026-03-16", "08:37:07 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("25", "1", "2026-03-16", "08:46:28 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("26", "1", "2026-03-16", "08:49:55 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("30", "5", "2026-03-16", "09:13:30 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("31", "5", "2026-03-16", "09:13:40 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("32", "5", "2026-03-16", "09:14:48 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("33", "1", "2026-03-16", "09:15:14 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("34", "1", "2026-03-16", "09:15:33 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("35", "1", "2026-03-16", "09:41:01 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("36", "1", "2026-03-16", "09:41:06 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("37", "5", "2026-03-16", "11:16:30 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("38", "5", "2026-03-16", "11:16:37 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("39", "1", "2026-03-16", "11:24:44 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("40", "1", "2026-03-16", "11:25:28 pm", "Sistema", "Backup", "Se generó una copia de seguridad: backup_ventas_2026-03-16_23-25-28.sql"),
("41", "1", "2026-03-16", "11:27:18 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("42", "1", "2026-03-17", "08:51:07 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("43", "1", "2026-03-17", "08:52:00 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("44", "5", "2026-03-17", "08:52:11 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("45", "5", "2026-03-17", "09:01:02 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("46", "5", "2026-03-17", "09:25:44 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("47", "5", "2026-03-17", "09:31:12 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("48", "1", "2026-03-17", "09:48:34 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("49", "1", "2026-03-17", "11:56:14 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema.");

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `categoria`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `categoria`;
CREATE TABLE `categoria` (
  `categoria_id` int(7) NOT NULL AUTO_INCREMENT,
  `categoria_nombre` varchar(50) NOT NULL,
  `categoria_padre_id` int(11) DEFAULT NULL,
  `categoria_ubicacion` varchar(150) NOT NULL,
  `categoria_unidades` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`categoria_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `cliente`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE `cliente` (
  `cliente_id` int(10) NOT NULL AUTO_INCREMENT,
  `cliente_tipo_documento` varchar(20) NOT NULL,
  `cliente_numero_documento` varchar(35) NOT NULL,
  `cliente_nombre` varchar(50) NOT NULL,
  `cliente_apellido` varchar(50) NOT NULL,
  `cliente_provincia` varchar(30) NOT NULL,
  `cliente_ciudad` varchar(30) NOT NULL,
  `cliente_direccion` varchar(70) NOT NULL,
  `cliente_telefono` varchar(20) NOT NULL,
  `cliente_email` varchar(50) NOT NULL,
  PRIMARY KEY (`cliente_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `cliente`
INSERT INTO `cliente` VALUES
("1", "Otro", "N/A", "Publico", "General", "N/A", "N/A", "N/A", "N/A", "N/A");

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `proveedor`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `proveedor`;
CREATE TABLE `proveedor` (
  `proveedor_id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_nombre` varchar(100) NOT NULL,
  `proveedor_rif` varchar(30) NOT NULL,
  `proveedor_telefono` varchar(20) DEFAULT NULL,
  `proveedor_direccion` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`proveedor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `proveedor`
INSERT INTO `proveedor` VALUES
("1", "Conputodo", "71281237-2", "2236789", "");

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `producto`
-- --------------------------------------------------------
-- MOVIDA ANTES de compra y compra_detalle
DROP TABLE IF EXISTS `producto`;
CREATE TABLE `producto` (
  `producto_id` int(20) NOT NULL AUTO_INCREMENT,
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
  `producto_unidades_caja` int(10) NOT NULL DEFAULT 1,
  PRIMARY KEY (`producto_id`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`categoria_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `compra`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `compra`;
CREATE TABLE `compra` (
  `compra_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `compra_fecha_vencimiento` date DEFAULT NULL,
  PRIMARY KEY (`compra_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `proveedor_id` (`proveedor_id`),
  CONSTRAINT `compra_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`),
  CONSTRAINT `compra_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedor` (`proveedor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `compra_detalle`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `compra_detalle`;
CREATE TABLE `compra_detalle` (
  `compra_detalle_id` int(11) NOT NULL AUTO_INCREMENT,
  `compra_id` int(11) NOT NULL,
  `producto_id` int(20) NOT NULL,
  `compra_detalle_cantidad` int(10) NOT NULL,
  `compra_detalle_precio` decimal(30,2) NOT NULL,
  PRIMARY KEY (`compra_detalle_id`),
  KEY `producto_id` (`producto_id`),
  KEY `fk_detalle_compra` (`compra_id`),
  CONSTRAINT `compra_detalle_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`),
  CONSTRAINT `fk_detalle_compra` FOREIGN KEY (`compra_id`) REFERENCES `compra` (`compra_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `compra_factura`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `compra_factura`;
CREATE TABLE `compra_factura` (
  `factura_id` int(11) NOT NULL AUTO_INCREMENT,
  `compra_id` int(11) NOT NULL,
  `factura_numero` varchar(50) NOT NULL,
  `factura_emision` date NOT NULL,
  `factura_vencimiento` date NOT NULL,
  `factura_fecha_registro` datetime NOT NULL,
  PRIMARY KEY (`factura_id`),
  KEY `fk_factura_compra` (`compra_id`),
  CONSTRAINT `fk_factura_compra` FOREIGN KEY (`compra_id`) REFERENCES `compra` (`compra_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `compra_pagos`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `compra_pagos`;
CREATE TABLE `compra_pagos` (
  `pago_id` int(11) NOT NULL AUTO_INCREMENT,
  `compra_id` int(11) NOT NULL,
  `usuario_id` int(7) NOT NULL,
  `pago_fecha` date NOT NULL,
  `pago_monto` decimal(30,2) NOT NULL,
  `pago_metodo` enum('Efectivo','Transferencia','Divisas','Debito','Anticipo') NOT NULL,
  `pago_referencia` varchar(100) DEFAULT NULL,
  `pago_nota` text DEFAULT NULL,
  PRIMARY KEY (`pago_id`),
  KEY `compra_id` (`compra_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `compra_pagos_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compra` (`compra_id`),
  CONSTRAINT `compra_pagos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `empresa`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `empresa`;
CREATE TABLE `empresa` (
  `empresa_id` int(11) NOT NULL AUTO_INCREMENT,
  `empresa_nombre` varchar(90) NOT NULL,
  `empresa_rif` varchar(40) NOT NULL,
  `empresa_telefono` varchar(20) NOT NULL,
  `empresa_emailKV` varchar(50) NOT NULL,
  `empresa_direccion` varchar(100) NOT NULL,
  PRIMARY KEY (`empresa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `empresa`
INSERT INTO `empresa` VALUES
("1", "Fasnet Lideres en Tecnología", "J-29665886-2", "04127465438", "Fasnet.comunicaciones@gmail.com", "Av. Anthons Phillips cc Av Merida local galpon Nro Sn Zona Industrial La Hamaca");

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `recepcion`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `recepcion`;
CREATE TABLE `recepcion` (
  `recepcion_id` int(11) NOT NULL AUTO_INCREMENT,
  `compra_id` int(11) NOT NULL,
  `usuario_id` int(7) NOT NULL,
  `recepcion_fecha` date NOT NULL,
  `recepcion_nota` text DEFAULT NULL,
  PRIMARY KEY (`recepcion_id`),
  KEY `compra_id` (`compra_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `recepcion_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compra` (`compra_id`),
  CONSTRAINT `recepcion_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `recepcion_detalle`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `recepcion_detalle`;
CREATE TABLE `recepcion_detalle` (
  `recepcion_detalle_id` int(11) NOT NULL AUTO_INCREMENT,
  `recepcion_id` int(11) NOT NULL,
  `producto_id` int(20) NOT NULL,
  `cantidad_recibida` int(11) NOT NULL,
  PRIMARY KEY (`recepcion_detalle_id`),
  KEY `recepcion_id` (`recepcion_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `recepcion_detalle_ibfk_1` FOREIGN KEY (`recepcion_id`) REFERENCES `recepcion` (`recepcion_id`),
  CONSTRAINT `recepcion_detalle_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `venta`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `venta`;
CREATE TABLE `venta` (
  `venta_id` int(30) NOT NULL AUTO_INCREMENT,
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
  `venta_referencia` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`venta_id`),
  UNIQUE KEY `venta_codigo` (`venta_codigo`),
  KEY `usuario_id` (`usuario_id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `caja_id` (`caja_id`),
  CONSTRAINT `venta_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`),
  CONSTRAINT `venta_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`cliente_id`),
  CONSTRAINT `venta_ibfk_3` FOREIGN KEY (`caja_id`) REFERENCES `caja` (`caja_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `venta_detalle`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `venta_detalle`;
CREATE TABLE `venta_detalle` (
  `venta_detalle_id` int(100) NOT NULL AUTO_INCREMENT,
  `venta_detalle_cantidad` int(10) NOT NULL,
  `venta_detalle_precio_compra` decimal(30,2) NOT NULL,
  `venta_detalle_precio_venta` decimal(30,2) NOT NULL,
  `venta_detalle_total` decimal(30,2) NOT NULL,
  `venta_detalle_descripcion` varchar(200) NOT NULL,
  `venta_codigo` varchar(200) NOT NULL,
  `producto_id` int(20) NOT NULL,
  PRIMARY KEY (`venta_detalle_id`),
  KEY `venta_id` (`venta_codigo`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `venta_detalle_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`),
  CONSTRAINT `venta_detalle_ibfk_3` FOREIGN KEY (`venta_codigo`) REFERENCES `venta` (`venta_codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Reactivar restricciones de llaves foráneas
SET FOREIGN_KEY_CHECKS = 1;