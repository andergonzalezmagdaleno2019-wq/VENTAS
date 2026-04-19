-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-04-2026 a las 03:08:28
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"; START TRANSACTION; SET time_zone = "+00:00";


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
 `bitacora_id` INT(10) NOT NULL,
 `usuario_id` INT(7) NOT NULL,
 `bitacora_fecha` DATE NOT NULL,
 `bitacora_hora` VARCHAR(20) NOT NULL,
 `bitacora_modulo` VARCHAR(50) NOT NULL,
 `bitacora_accion` VARCHAR(50) NOT NULL,
 `bitacora_descripcion` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja`
--
CREATE TABLE `caja` (
 `caja_id` INT(5) NOT NULL,
 `caja_numero` INT(5) NOT NULL,
 `caja_nombre` VARCHAR(100) NOT NULL,
 `caja_efectivo` DECIMAL(30,2) NOT NULL
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
 `categoria_id` INT(7) NOT NULL,
 `categoria_nombre` VARCHAR(50) NOT NULL,
 `categoria_padre_id` INT(11) DEFAULT NULL,
 `categoria_ubicacion` VARCHAR(150) NOT NULL,
 `categoria_unidades` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--
CREATE TABLE `cliente` (
 `cliente_id` INT(10) NOT NULL,
 `cliente_tipo_documento` VARCHAR(20) NOT NULL,
 `cliente_numero_documento` VARCHAR(35) NOT NULL,
 `cliente_nombre` VARCHAR(50) NOT NULL,
 `cliente_apellido` VARCHAR(50) NOT NULL,
 `cliente_provincia` VARCHAR(30) NOT NULL,
 `cliente_ciudad` VARCHAR(30) NOT NULL,
 `cliente_direccion` VARCHAR(70) NOT NULL,
 `cliente_telefono` VARCHAR(20) NOT NULL,
 `cliente_email` VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra`
--
CREATE TABLE `compra` (
 `compra_id` INT(11) NOT NULL,
 `compra_codigo` VARCHAR(50) NOT NULL,
 `compra_fecha` DATE NOT NULL,
 `compra_total` DECIMAL(30,2) NOT NULL,
 `compra_tasa_bcv` DECIMAL(20,2) NOT NULL DEFAULT 0.00,
 `usuario_id` INT(7) NOT NULL,
 `proveedor_id` INT(11) NOT NULL,
 `compra_estado` VARCHAR(20) DEFAULT 'Pendiente',
 `compra_nota_interna` TEXT DEFAULT NULL,
 `compra_saldo_pendiente` DECIMAL(30,2) NOT NULL,
 `compra_estado_pago` ENUM('Pendiente','Parcial','Pagado') DEFAULT 'Pendiente',
 `compra_fecha_vencimiento` DATE DEFAULT NULL,
 `compra_condicion` ENUM('Contado','Crédito','Consignación') NOT NULL DEFAULT 'Crédito',
 `compra_cuotas_total` INT(11) DEFAULT 1,
 `compra_cuotas_pagadas` INT(11) DEFAULT 0,
 `compra_frecuencia` INT(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra_cuotas`
--
CREATE TABLE `compra_cuotas` (
 `cuota_id` INT(11) NOT NULL,
 `compra_codigo` VARCHAR(100) NOT NULL,
 `cuota_numero` INT(11) NOT NULL,
 `cuota_monto` DECIMAL(30,2) NOT NULL,
 `cuota_fecha_vencimiento` DATE NOT NULL,
 `cuota_estado` ENUM('Pendiente','Pagado','Vencido') DEFAULT 'Pendiente',
 `cuota_justificacion` TEXT DEFAULT NULL,
 `cuota_fecha_pago` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra_detalle`
--
CREATE TABLE `compra_detalle` (
 `compra_detalle_id` INT(11) NOT NULL,
 `compra_id` INT(11) NOT NULL,
 `producto_id` INT(20) NOT NULL,
 `compra_detalle_cantidad` INT(10) NOT NULL,
 `compra_detalle_precio` DECIMAL(30,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra_factura`
--
CREATE TABLE `compra_factura` (
 `factura_id` INT(11) NOT NULL,
 `compra_id` INT(11) NOT NULL,
 `factura_numero` VARCHAR(50) NOT NULL,
 `factura_emision` DATE NOT NULL,
 `factura_vencimiento` DATE NOT NULL,
 `factura_fecha_registro` DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra_pagos`
--
CREATE TABLE `compra_pagos` (
 `pago_id` INT(11) NOT NULL,
 `compra_id` INT(11) NOT NULL,
 `usuario_id` INT(7) NOT NULL,
 `pago_fecha` DATE NOT NULL,
 `pago_monto` DECIMAL(30,2) NOT NULL,
 `pago_metodo` ENUM('Efectivo','Transferencia','Divisas','Debito','Anticipo') NOT NULL,
 `pago_referencia` VARCHAR(100) DEFAULT NULL,
 `pago_nota` TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--
CREATE TABLE `empresa` (
 `empresa_id` INT(11) NOT NULL,
 `empresa_nombre` VARCHAR(90) NOT NULL,
 `empresa_rif` VARCHAR(40) NOT NULL,
 `empresa_telefono` VARCHAR(20) NOT NULL,
 `empresa_emailKV` VARCHAR(50) NOT NULL,
 `empresa_direccion` VARCHAR(100) NOT NULL
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
 `producto_id` INT(20) NOT NULL,
 `producto_codigo` VARCHAR(77) NOT NULL,
 `producto_nombre` VARCHAR(100) NOT NULL,
 `producto_stock_total` INT(25) NOT NULL,
 `producto_tipo_unidad` VARCHAR(20) NOT NULL,
 `producto_precio_compra` DECIMAL(30,2) NOT NULL,
 `producto_precio_venta` DECIMAL(30,2) NOT NULL,
 `producto_marca` VARCHAR(35) NOT NULL,
 `producto_modelo` VARCHAR(35) NOT NULL,
 `producto_estado` VARCHAR(20) NOT NULL,
 `producto_foto` VARCHAR(500) NOT NULL,
 `categoria_id` INT(7) NOT NULL,
 `producto_costo` DECIMAL(30,2) NOT NULL DEFAULT 0.00,
 `producto_stock_min` INT(10) NOT NULL DEFAULT 5,
 `producto_stock_max` INT(10) NOT NULL DEFAULT 100,
 `producto_precio` DECIMAL(30,2) NOT NULL,
 `producto_stock` INT(25) NOT NULL,
 `producto_unidad` VARCHAR(100) NOT NULL,
 `producto_unidades_caja` INT(10) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_proveedor`
--
CREATE TABLE `producto_proveedor` (
 `relacion_id` INT(11) NOT NULL,
 `producto_id` INT(11) NOT NULL,
 `proveedor_id` INT(11) NOT NULL,
 `precio_compra_referencial` DECIMAL(30,2) DEFAULT 0.00,
 `ultima_compra` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--
CREATE TABLE `proveedor` (
 `proveedor_id` INT(11) NOT NULL,
 `proveedor_nombre` VARCHAR(100) NOT NULL,
 `proveedor_rif` VARCHAR(30) NOT NULL,
 `proveedor_telefono` VARCHAR(20) DEFAULT NULL,
 `proveedor_direccion` VARCHAR(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recepcion`
--
CREATE TABLE `recepcion` (
 `recepcion_id` INT(11) NOT NULL,
 `compra_id` INT(11) NOT NULL,
 `usuario_id` INT(7) NOT NULL,
 `recepcion_fecha` DATE NOT NULL,
 `recepcion_nota` TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recepcion_detalle`
--
CREATE TABLE `recepcion_detalle` (
 `recepcion_detalle_id` INT(11) NOT NULL,
 `recepcion_id` INT(11) NOT NULL,
 `producto_id` INT(20) NOT NULL,
 `cantidad_recibida` INT(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--
CREATE TABLE `rol` (
 `rol_id` INT(11) NOT NULL,
 `rol_nombre` VARCHAR(50) NOT NULL
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
 `usuario_id` INT(7) NOT NULL,
 `usuario_tipo_documento` VARCHAR(7) NOT NULL DEFAULT 'V',
 `usuario_dni` VARCHAR(30) NOT NULL DEFAULT '0',
 `usuario_nombre` VARCHAR(50) NOT NULL,
 `usuario_apellido` VARCHAR(50) NOT NULL,
 `usuario_email` VARCHAR(50) NOT NULL,
 `usuario_usuario` VARCHAR(30) NOT NULL,
 `usuario_clave` VARCHAR(535) NOT NULL,
 `usuario_foto` VARCHAR(200) NOT NULL,
 `caja_id` INT(5) NOT NULL,
 `rol_id` INT(11) NOT NULL DEFAULT 2,
 `usuario_pregunta_1` VARCHAR(150) DEFAULT NULL,
 `usuario_respuesta_1` VARCHAR(150) DEFAULT NULL,
 `usuario_pregunta_2` VARCHAR(150) DEFAULT NULL,
 `usuario_respuesta_2` VARCHAR(150) DEFAULT NULL,
 `usuario_pregunta_3` VARCHAR(150) DEFAULT NULL,
 `usuario_respuesta_3` VARCHAR(150) DEFAULT NULL,
 `usuario_estado` VARCHAR(20) DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `usuario`
--
INSERT INTO `usuario` (`usuario_id`, `usuario_tipo_documento`, `usuario_dni`, `usuario_nombre`, `usuario_apellido`, `usuario_email`, `usuario_usuario`, `usuario_clave`, `usuario_foto`, `caja_id`, `rol_id`, `usuario_pregunta_1`, `usuario_respuesta_1`, `usuario_pregunta_2`, `usuario_respuesta_2`, `usuario_pregunta_3`, `usuario_respuesta_3`, `usuario_estado`) VALUES
(1, 'V', '0', 'Administrador', 'Principal', 'Administrador@gmail.com', 'Administrador', '$2y$10$Jgm6xFb5Onz/BMdIkNK2Tur8yg/NYEMb/tdnhoV7kB1BwIG4R05D2', 'Administrador_23.jpg', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--
CREATE TABLE `venta` (
 `venta_id` INT(30) NOT NULL,
 `venta_codigo` VARCHAR(200) NOT NULL,
 `venta_fecha` DATE NOT NULL,
 `venta_hora` VARCHAR(17) NOT NULL,
 `venta_total` DECIMAL(30,2) NOT NULL,
 `venta_pagado` DECIMAL(30,2) NOT NULL,
 `venta_cambio` DECIMAL(30,2) NOT NULL,
 `venta_tasa_bcv` DECIMAL(20,2) NOT NULL DEFAULT 0.00,
 `usuario_id` INT(7) NOT NULL,
 `cliente_id` INT(10) NOT NULL,
 `caja_id` INT(5) NOT NULL,
 `venta_metodo_pago` VARCHAR(30) NOT NULL DEFAULT 'Efectivo',
 `venta_referencia` VARCHAR(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_detalle`
--
CREATE TABLE `venta_detalle` (
 `venta_detalle_id` INT(100) NOT NULL,
 `venta_detalle_cantidad` INT(10) NOT NULL,
 `venta_detalle_precio_compra` DECIMAL(30,2) NOT NULL,
 `venta_detalle_precio_venta` DECIMAL(30,2) NOT NULL,
 `venta_detalle_total` DECIMAL(30,2) NOT NULL,
 `venta_detalle_descripcion` VARCHAR(200) NOT NULL,
 `venta_codigo` VARCHAR(200) NOT NULL,
 `producto_id` INT(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora` ADD PRIMARY KEY (`bitacora_id`), ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `caja`
--
ALTER TABLE `caja` ADD PRIMARY KEY (`caja_id`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria` ADD PRIMARY KEY (`categoria_id`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente` ADD PRIMARY KEY (`cliente_id`);

--
-- Indices de la tabla `compra`
--
ALTER TABLE `compra` ADD PRIMARY KEY (`compra_id`), ADD UNIQUE KEY `compra_codigo` (`compra_codigo`), ADD KEY `usuario_id` (`usuario_id`), ADD KEY `proveedor_id` (`proveedor_id`);

--
-- Indices de la tabla `compra_cuotas`
--
ALTER TABLE `compra_cuotas` ADD PRIMARY KEY (`cuota_id`), ADD KEY `compra_codigo` (`compra_codigo`);

--
-- Indices de la tabla `compra_detalle`
--
ALTER TABLE `compra_detalle` ADD PRIMARY KEY (`compra_detalle_id`), ADD KEY `producto_id` (`producto_id`), ADD KEY `fk_detalle_compra` (`compra_id`);

--
-- Indices de la tabla `compra_factura`
--
ALTER TABLE `compra_factura` ADD PRIMARY KEY (`factura_id`), ADD KEY `fk_factura_compra` (`compra_id`);

--
-- Indices de la tabla `compra_pagos`
--
ALTER TABLE `compra_pagos` ADD PRIMARY KEY (`pago_id`), ADD KEY `compra_id` (`compra_id`), ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa` ADD PRIMARY KEY (`empresa_id`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto` ADD PRIMARY KEY (`producto_id`), ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `producto_proveedor`
--
ALTER TABLE `producto_proveedor` ADD PRIMARY KEY (`relacion_id`), ADD KEY `producto_id` (`producto_id`), ADD KEY `proveedor_id` (`proveedor_id`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor` ADD PRIMARY KEY (`proveedor_id`);

--
-- Indices de la tabla `recepcion`
--
ALTER TABLE `recepcion` ADD PRIMARY KEY (`recepcion_id`), ADD KEY `compra_id` (`compra_id`), ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `recepcion_detalle`
--
ALTER TABLE `recepcion_detalle` ADD PRIMARY KEY (`recepcion_detalle_id`), ADD KEY `recepcion_id` (`recepcion_id`), ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol` ADD PRIMARY KEY (`rol_id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario` ADD PRIMARY KEY (`usuario_id`), ADD KEY `caja_id` (`caja_id`), ADD KEY `fk_usuario_rol` (`rol_id`);

--
-- Indices de la tabla `venta`
--
ALTER TABLE `venta` ADD PRIMARY KEY (`venta_id`), ADD UNIQUE KEY `venta_codigo` (`venta_codigo`), ADD KEY `usuario_id` (`usuario_id`), ADD KEY `cliente_id` (`cliente_id`), ADD KEY `caja_id` (`caja_id`);

--
-- Indices de la tabla `venta_detalle`
--
ALTER TABLE `venta_detalle` ADD PRIMARY KEY (`venta_detalle_id`), ADD KEY `venta_id` (`venta_codigo`), ADD KEY `producto_id` (`producto_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora` MODIFY `bitacora_id` INT(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `caja`
--
ALTER TABLE `caja` MODIFY `caja_id` INT(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria` MODIFY `categoria_id` INT(7) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente` MODIFY `cliente_id` INT(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compra`
--
ALTER TABLE `compra` MODIFY `compra_id` INT(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compra_cuotas`
--
ALTER TABLE `compra_cuotas` MODIFY `cuota_id` INT(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compra_detalle`
--
ALTER TABLE `compra_detalle` MODIFY `compra_detalle_id` INT(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compra_factura`
--
ALTER TABLE `compra_factura` MODIFY `factura_id` INT(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compra_pagos`
--
ALTER TABLE `compra_pagos` MODIFY `pago_id` INT(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa` MODIFY `empresa_id` INT(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto` MODIFY `producto_id` INT(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto_proveedor`
--
ALTER TABLE `producto_proveedor` MODIFY `relacion_id` INT(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor` MODIFY `proveedor_id` INT(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recepcion`
--
ALTER TABLE `recepcion` MODIFY `recepcion_id` INT(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recepcion_detalle`
--
ALTER TABLE `recepcion_detalle` MODIFY `recepcion_detalle_id` INT(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol` MODIFY `rol_id` INT(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario` MODIFY `usuario_id` INT(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `venta`
--
ALTER TABLE `venta` MODIFY `venta_id` INT(30) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `venta_detalle`
--
ALTER TABLE `venta_detalle` MODIFY `venta_detalle_id` INT(100) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora` ADD CONSTRAINT `bitacora_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);

--
-- Filtros para la tabla `compra`
--
ALTER TABLE `compra` ADD CONSTRAINT `compra_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`), ADD CONSTRAINT `compra_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedor` (`proveedor_id`);

--
-- Filtros para la tabla `compra_cuotas`
--
ALTER TABLE `compra_cuotas` ADD CONSTRAINT `fk_cuotas_compra` FOREIGN KEY (`compra_codigo`) REFERENCES `compra` (`compra_codigo`) ON
DELETE CASCADE ON
UPDATE CASCADE;

--
-- Filtros para la tabla `compra_detalle`
--
ALTER TABLE `compra_detalle` ADD CONSTRAINT `compra_detalle_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`), ADD CONSTRAINT `fk_detalle_compra` FOREIGN KEY (`compra_id`) REFERENCES `compra` (`compra_id`) ON
DELETE CASCADE;

--
-- Filtros para la tabla `compra_factura`
--
ALTER TABLE `compra_factura` ADD CONSTRAINT `fk_factura_compra` FOREIGN KEY (`compra_id`) REFERENCES `compra` (`compra_id`) ON
DELETE CASCADE;

--
-- Filtros para la tabla `compra_pagos`
--
ALTER TABLE `compra_pagos` ADD CONSTRAINT `compra_pagos_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compra` (`compra_id`), ADD CONSTRAINT `compra_pagos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto` ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`categoria_id`);

--
-- Filtros para la tabla `producto_proveedor`
--
ALTER TABLE `producto_proveedor` ADD CONSTRAINT `producto_proveedor_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`) ON
DELETE CASCADE, ADD CONSTRAINT `producto_proveedor_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedor` (`proveedor_id`) ON
DELETE CASCADE;

--
-- Filtros para la tabla `recepcion`
--
ALTER TABLE `recepcion` ADD CONSTRAINT `recepcion_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compra` (`compra_id`), ADD CONSTRAINT `recepcion_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);

--
-- Filtros para la tabla `recepcion_detalle`
--
ALTER TABLE `recepcion_detalle` ADD CONSTRAINT `recepcion_detalle_ibfk_1` FOREIGN KEY (`recepcion_id`) REFERENCES `recepcion` (`recepcion_id`), ADD CONSTRAINT `recepcion_detalle_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario` ADD CONSTRAINT `fk_usuario_caja` FOREIGN KEY (`caja_id`) REFERENCES `caja` (`caja_id`), ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`rol_id`) ON
UPDATE CASCADE;

--
-- Filtros para la tabla `venta`
--
ALTER TABLE `venta` ADD CONSTRAINT `venta_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`), ADD CONSTRAINT `venta_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`cliente_id`), ADD CONSTRAINT `venta_ibfk_3` FOREIGN KEY (`caja_id`) REFERENCES `caja` (`caja_id`);

--
-- Filtros para la tabla `venta_detalle`
--
ALTER TABLE `venta_detalle` ADD CONSTRAINT `venta_detalle_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`), ADD CONSTRAINT `venta_detalle_ibfk_3` FOREIGN KEY (`venta_codigo`) REFERENCES `venta` (`venta_codigo`); COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
