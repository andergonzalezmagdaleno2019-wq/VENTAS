-- --------------------------------------------------------
-- Respaldo del Sistema de Ventas
-- Fecha de generación: 2026-04-27 13:41:25
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `bitacora`
INSERT INTO `bitacora` VALUES
("1", "1", "2026-04-27", "01:40:00 pm", "Seguridad", "Seguridad", "El usuario Administrador inició sesión correctamente.");


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
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `categoria`
INSERT INTO `categoria` VALUES
("1", "Computadoras", NULL, "Pasillo 1 - Estantes 1-8", NULL),
("2", "Componentes PC", NULL, "Pasillo 2 - Estantes 9-16", NULL),
("3", "Periféricos", NULL, "Pasillo 3 - Estantes 17-24", NULL),
("4", "Almacenamiento", NULL, "Pasillo 4 - Estantes 25-32", NULL),
("5", "Redes y Conectividad", NULL, "Pasillo 5 - Estantes 33-40", NULL),
("6", "Software y Licencias", NULL, "Vitrina Digital - Mostrador 1", NULL),
("7", "Audio y Sonido", NULL, "Pasillo 6 - Estantes 41-48", NULL),
("8", "Impresión y Consumibles", NULL, "Pasillo 7 - Estantes 49-56", NULL),
("9", "Laptops", "1", "Pasillo 1 - Sección A", "unidades"),
("10", "PC de Escritorio", "1", "Pasillo 1 - Sección B", "unidades"),
("11", "All-in-One", "1", "Pasillo 1 - Sección C", "unidades"),
("12", "Procesadores", "2", "Pasillo 2 - Sección A", "unidades"),
("13", "Memorias RAM", "2", "Pasillo 2 - Sección B", "unidades"),
("14", "Motherboards", "2", "Pasillo 2 - Sección C", "unidades"),
("15", "Tarjetas de Video", "2", "Pasillo 2 - Sección D", "unidades"),
("16", "Fuentes de Poder", "2", "Pasillo 2 - Sección E", "unidades"),
("17", "Gabinetes", "2", "Pasillo 2 - Sección F", "unidades"),
("18", "Coolers y Ventilación", "2", "Pasillo 2 - Sección G", "unidades"),
("19", "Teclados", "3", "Pasillo 3 - Sección A", "unidades"),
("20", "Mouse", "3", "Pasillo 3 - Sección B", "unidades"),
("21", "Monitores", "3", "Pasillo 3 - Sección C", "unidades"),
("22", "Webcams", "3", "Pasillo 3 - Sección D", "unidades"),
("23", "Audífonos", "3", "Pasillo 3 - Sección E", "unidades"),
("24", "SSD", "4", "Pasillo 4 - Sección A", "unidades"),
("25", "HDD", "4", "Pasillo 4 - Sección B", "unidades"),
("26", "USB Flash", "4", "Pasillo 4 - Sección C", "unidades"),
("27", "Tarjetas SD", "4", "Pasillo 4 - Sección D", "unidades"),
("28", "Routers", "5", "Pasillo 5 - Sección A", "unidades"),
("29", "Switches", "5", "Pasillo 5 - Sección B", "unidades"),
("30", "Cables de Red", "5", "Pasillo 5 - Sección C", "unidades"),
("31", "Adaptadores WiFi", "5", "Pasillo 5 - Sección D", "unidades"),
("32", "Parlantes", "7", "Pasillo 6 - Sección A", "unidades"),
("33", "Micrófonos", "7", "Pasillo 6 - Sección B", "unidades"),
("34", "Impresoras", "8", "Pasillo 7 - Sección A", "unidades"),
("35", "Tintas y Toners", "8", "Pasillo 7 - Sección B", "unidades");


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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `cliente`
INSERT INTO `cliente` VALUES
("1", "V", "V-12345678", "Carlos", "Mendoza", "Caracas", "Caracas", "Av. Libertador, Edif. Central, Apto 4B", "0412-1234567", "carlos.mendoza@gmail.com"),
("2", "V", "V-23456789", "María", "Fernández", "Miranda", "Los Teques", "Calle Bolívar, Casa 15", "0414-2345678", "maria.fernandez@hotmail.com"),
("3", "E", "E-34567890", "TechSolutions C.A.", "TechSolutions C.A.", "Caracas", "Caracas", "Av. Francisco de Miranda, Centro Plaza, Torre A, Piso 5", "0212-3456789", "info@techsolutions.com"),
("4", "V", "V-45678901", "Luis", "González", "Carabobo", "Valencia", "Urb. Prebo, Calle 45, Casa 23", "0424-4567890", "luis.gonzalez@gmail.com"),
("5", "J", "J-56789012", "Inversiones Digitales 2025 C.A.", "Inversiones Digitales 2025 C.A.", "Caracas", "Caracas", "Av. Principal de Las Mercedes, Torre Corp, Piso 10", "0212-5678901", "compras@inversionesdigitales.com"),
("6", "V", "V-67890123", "Ana", "Rodríguez", "Miranda", "Baruta", "Calle El Parque, Residencias Sol, Apto 8C", "0416-6789012", "ana.rodriguez@gmail.com"),
("7", "V", "V-78901234", "Pedro", "Martínez", "Distrito Capital", "Caracas", "Av. Sucre, Edif. Los Jardines, PB", "0426-7890123", "pedro.martinez@yahoo.com"),
("8", "E", "E-89012345", "DataCenter Express", "DataCenter Express", "Caracas", "Caracas", "Zona Industrial La Urbina, Galpón 5", "0212-8901234", "ventas@datacenterexpress.com.ve"),
("9", "V", "V-90123456", "Gabriela", "López", "Lara", "Barquisimeto", "Carrera 15, Casa 45", "0412-9012345", "gabriela.lopez@gmail.com"),
("10", "J", "J-01234567", "CyberNet C.A.", "CyberNet C.A.", "Zulia", "Maracaibo", "Av. 5 de Julio, Edif. Empresarial, Piso 3", "0261-0123456", "contacto@cybernet.com.ve");


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
  `compra_condicion` enum('Contado','Crédito','Consignación') NOT NULL DEFAULT 'Crédito',
  `compra_cuotas_total` int(11) DEFAULT 1,
  `compra_cuotas_pagadas` int(11) DEFAULT 0,
  `compra_frecuencia` int(11) DEFAULT 0,
  PRIMARY KEY (`compra_id`),
  UNIQUE KEY `compra_codigo` (`compra_codigo`),
  KEY `usuario_id` (`usuario_id`),
  KEY `proveedor_id` (`proveedor_id`),
  CONSTRAINT `compra_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`),
  CONSTRAINT `compra_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedor` (`proveedor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `compra`
INSERT INTO `compra` VALUES
("1", "COMP-2026-001", "2026-04-10", "3200.00", "35.50", "1", "1", "Recibida", "Compra de laptops y procesadores", "0.00", "Pagado", "2026-04-10", "Contado", "1", "1", "0"),
("2", "COMP-2026-002", "2026-04-15", "1800.00", "35.80", "1", "2", "Recibida", "Componentes para ensamblaje de PCs", "600.00", "Parcial", "2026-07-15", "Crédito", "3", "2", "30"),
("3", "COMP-2026-003", "2026-04-18", "3200.00", "36.00", "1", "3", "Recibida", "Monitores y SSDs Samsung", "0.00", "Pagado", "2026-04-18", "Contado", "1", "1", "0"),
("4", "COMP-2026-004", "2026-04-20", "1800.00", "36.20", "1", "4", "Recibida", "Gabinetes, coolers y router mesh", "900.00", "Parcial", "2026-06-20", "Crédito", "2", "1", "30");


-- --------------------------------------------------------
-- Estructura de tabla para la tabla `compra_cuotas`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `compra_cuotas`;
CREATE TABLE `compra_cuotas` (
  `cuota_id` int(11) NOT NULL AUTO_INCREMENT,
  `compra_codigo` varchar(100) NOT NULL,
  `cuota_numero` int(11) NOT NULL,
  `cuota_monto` decimal(30,2) NOT NULL,
  `cuota_fecha_vencimiento` date NOT NULL,
  `cuota_estado` enum('Pendiente','Pagado','Vencido') DEFAULT 'Pendiente',
  `cuota_justificacion` text DEFAULT NULL,
  `cuota_fecha_pago` datetime DEFAULT NULL,
  PRIMARY KEY (`cuota_id`),
  KEY `compra_codigo` (`compra_codigo`),
  CONSTRAINT `fk_cuotas_compra` FOREIGN KEY (`compra_codigo`) REFERENCES `compra` (`compra_codigo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `compra_cuotas`
INSERT INTO `compra_cuotas` VALUES
("1", "COMP-2026-002", "1", "600.00", "2026-05-15", "Pagado", NULL, "2026-05-14 10:00:00"),
("2", "COMP-2026-002", "2", "600.00", "2026-06-15", "Pagado", NULL, "2026-06-15 09:00:00"),
("3", "COMP-2026-002", "3", "600.00", "2026-07-15", "Pendiente", NULL, NULL),
("4", "COMP-2026-004", "1", "900.00", "2026-05-20", "Pagado", NULL, "2026-05-18 11:00:00"),
("5", "COMP-2026-004", "2", "900.00", "2026-06-20", "Pendiente", NULL, NULL);


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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `compra_detalle`
INSERT INTO `compra_detalle` VALUES
("1", "1", "1", "5", "450.00"),
("2", "1", "2", "3", "520.00"),
("3", "1", "10", "10", "150.00"),
("4", "2", "6", "2", "550.00"),
("5", "2", "13", "10", "22.00"),
("6", "2", "14", "8", "40.00"),
("7", "2", "20", "6", "55.00"),
("8", "2", "22", "5", "45.00"),
("9", "3", "4", "3", "850.00"),
("10", "3", "30", "5", "120.00"),
("11", "3", "31", "3", "280.00"),
("12", "3", "36", "5", "75.00"),
("13", "3", "40", "10", "15.00"),
("14", "4", "8", "5", "180.00"),
("15", "4", "15", "8", "110.00"),
("16", "4", "22", "5", "45.00"),
("17", "4", "23", "4", "65.00"),
("18", "4", "42", "4", "130.00");


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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `compra_factura`
INSERT INTO `compra_factura` VALUES
("1", "1", "FAC-001-INT", "2026-04-10", "2026-04-10", "2026-04-10 11:00:00"),
("2", "2", "FAC-002-DTS", "2026-04-15", "2026-07-15", "2026-04-15 14:30:00"),
("3", "3", "FAC-2026-0456", "2026-04-18", "2026-04-18", "2026-04-18 16:00:00"),
("4", "4", "FAC-IMP-789", "2026-04-20", "2026-06-20", "2026-04-20 10:00:00");


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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `compra_pagos`
INSERT INTO `compra_pagos` VALUES
("1", "1", "1", "2026-04-10", "3200.00", "Transferencia", "TRF-10042026-001", "Pago total compra inicial"),
("2", "2", "1", "2026-05-14", "600.00", "Transferencia", "TRF-14052026-001", "Cuota 1 de 3"),
("3", "2", "1", "2026-06-15", "600.00", "Transferencia", "TRF-15062026-001", "Cuota 2 de 3"),
("4", "3", "1", "2026-04-18", "3200.00", "Transferencia", "TRF-18042026-SAM", "Pago total Samsung"),
("5", "4", "1", "2026-05-18", "900.00", "Efectivo", NULL, "Cuota 1 de 2");


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
-- Estructura de tabla para la tabla `producto`
-- --------------------------------------------------------

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
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `producto`
INSERT INTO `producto` VALUES
("1", "LAP-001", "Laptop HP 15.6 Core i5 8GB 512GB SSD", "15", "unidad", "450.00", "599.00", "HP", "15s-fq5000la", "Activo", "hp15_i5.jpg", "9", "450.00", "3", "20", "599.00", "15", "unidad", "1"),
("2", "LAP-002", "Laptop Dell Inspiron 15 12GB 256GB SSD", "10", "unidad", "520.00", "699.00", "Dell", "Inspiron 15 3525", "Activo", "dell_inspiron.jpg", "9", "520.00", "2", "15", "699.00", "10", "unidad", "1"),
("3", "LAP-003", "Laptop Lenovo IdeaPad 3 Ryzen 5 8GB 512GB", "8", "unidad", "480.00", "649.00", "Lenovo", "IdeaPad 3 15ABA7", "Activo", "lenovo_ideapad.jpg", "9", "480.00", "2", "12", "649.00", "8", "unidad", "1"),
("4", "LAP-004", "MacBook Air M1 8GB 256GB", "5", "unidad", "850.00", "1099.00", "Apple", "M1 2020", "Activo", "macbook_air.jpg", "9", "850.00", "1", "8", "1099.00", "5", "unidad", "1"),
("5", "LAP-005", "Laptop Asus VivoBook 15 Core i3 8GB 256GB", "12", "unidad", "350.00", "459.00", "Asus", "X515EA", "Activo", "asus_vivobook.jpg", "9", "350.00", "3", "18", "459.00", "12", "unidad", "1"),
("6", "PC-001", "PC Escritorio Intel Core i5 16GB 512GB SSD", "6", "unidad", "550.00", "749.00", "Ensamble", "Custom-Build", "Activo", "pc_i5.jpg", "10", "550.00", "2", "10", "749.00", "6", "unidad", "1"),
("7", "PC-002", "PC Gamer Ryzen 7 32GB RTX 4060 1TB", "3", "unidad", "1200.00", "1599.00", "Ensamble", "Gamer-Pro", "Activo", "pc_gamer.jpg", "10", "1200.00", "1", "5", "1599.00", "3", "unidad", "1"),
("8", "PC-003", "Mini PC Intel N95 8GB 256GB", "8", "unidad", "180.00", "259.00", "Beelink", "Mini S12", "Activo", "minipc.jpg", "10", "180.00", "3", "12", "259.00", "8", "unidad", "1"),
("9", "AIO-001", "All-in-One HP 24 Core i3 8GB 512GB", "4", "unidad", "600.00", "799.00", "HP", "24-cb0000la", "Activo", "hp_aio.jpg", "11", "600.00", "1", "6", "799.00", "4", "unidad", "1"),
("10", "CPU-001", "Procesador Intel Core i5-12400F", "20", "unidad", "150.00", "199.00", "Intel", "i5-12400F", "Activo", "cpu_intel_i5.jpg", "12", "150.00", "5", "30", "199.00", "20", "unidad", "1"),
("11", "CPU-002", "Procesador AMD Ryzen 5 5600X", "15", "unidad", "170.00", "229.00", "AMD", "Ryzen 5 5600X", "Activo", "cpu_ryzen5.jpg", "12", "170.00", "4", "25", "229.00", "15", "unidad", "1"),
("12", "CPU-003", "Procesador Intel Core i7-13700K", "8", "unidad", "350.00", "459.00", "Intel", "i7-13700K", "Activo", "cpu_intel_i7.jpg", "12", "350.00", "2", "12", "459.00", "8", "unidad", "1"),
("13", "RAM-001", "Memoria RAM DDR4 8GB 3200MHz", "30", "unidad", "22.00", "39.00", "Kingston", "Fury Beast", "Activo", "ram_8gb.jpg", "13", "22.00", "8", "50", "39.00", "30", "unidad", "10"),
("14", "RAM-002", "Memoria RAM DDR4 16GB 3200MHz", "25", "unidad", "40.00", "69.00", "Corsair", "Vengeance LPX", "Activo", "ram_16gb.jpg", "13", "40.00", "5", "40", "69.00", "25", "unidad", "10"),
("15", "RAM-003", "Memoria RAM DDR5 32GB 5600MHz", "10", "unidad", "110.00", "159.00", "Crucial", "CT32G56C46U5", "Activo", "ram_32gb.jpg", "13", "110.00", "3", "15", "159.00", "10", "unidad", "5"),
("16", "MOBO-001", "Motherboard ASUS Prime H610M-E", "12", "unidad", "85.00", "129.00", "ASUS", "Prime H610M-E", "Activo", "mobo_asus.jpg", "14", "85.00", "3", "18", "129.00", "12", "unidad", "1"),
("17", "MOBO-002", "Motherboard MSI B550-A Pro", "8", "unidad", "120.00", "179.00", "MSI", "B550-A Pro", "Activo", "mobo_msi.jpg", "14", "120.00", "2", "12", "179.00", "8", "unidad", "1"),
("18", "GPU-001", "Tarjeta Video NVIDIA RTX 4060 8GB", "7", "unidad", "320.00", "449.00", "MSI", "RTX 4060 Ventus", "Activo", "rtx4060.jpg", "15", "320.00", "2", "10", "449.00", "7", "unidad", "1"),
("19", "GPU-002", "Tarjeta Video AMD Radeon RX 7600 8GB", "5", "unidad", "280.00", "379.00", "Gigabyte", "RX 7600 Gaming", "Activo", "rx7600.jpg", "15", "280.00", "1", "8", "379.00", "5", "unidad", "1"),
("20", "PSU-001", "Fuente Poder 650W 80+ Bronze", "18", "unidad", "55.00", "89.00", "EVGA", "650 BR", "Activo", "psu_650.jpg", "16", "55.00", "5", "25", "89.00", "18", "unidad", "1"),
("21", "PSU-002", "Fuente Poder 850W 80+ Gold Modular", "10", "unidad", "110.00", "159.00", "Corsair", "RM850x", "Activo", "psu_850.jpg", "16", "110.00", "3", "15", "159.00", "10", "unidad", "1"),
("22", "CASE-001", "Gabinete ATX Vidrio Templado Negro", "10", "unidad", "45.00", "75.00", "Cougar", "MX410", "Activo", "case_cougar.jpg", "17", "45.00", "3", "15", "75.00", "10", "unidad", "1"),
("23", "CASE-002", "Gabinete ITX Mini Blanco", "6", "unidad", "65.00", "99.00", "NZXT", "H210", "Activo", "case_nzxt.jpg", "17", "65.00", "2", "10", "99.00", "6", "unidad", "1"),
("24", "COOL-001", "Cooler CPU Hyper 212 Spectrum", "15", "unidad", "30.00", "55.00", "Cooler Master", "Hyper 212", "Activo", "cooler_212.jpg", "18", "30.00", "4", "20", "55.00", "15", "unidad", "1"),
("25", "COOL-002", "Ventilador 120mm RGB 3-pack", "20", "unidad", "25.00", "45.00", "Corsair", "SP120 RGB", "Activo", "fan_corsair.jpg", "18", "25.00", "5", "30", "45.00", "20", "unidad", "3"),
("26", "TEC-001", "Teclado Mecánico RGB Switch Red", "20", "unidad", "40.00", "69.00", "Redragon", "Kumara K552", "Activo", "teclado_redragon.jpg", "19", "40.00", "5", "30", "69.00", "20", "unidad", "1"),
("27", "TEC-002", "Teclado Inalámbrico Slim", "25", "unidad", "18.00", "35.00", "Logitech", "K380", "Activo", "teclado_logi.jpg", "19", "18.00", "6", "35", "35.00", "25", "unidad", "1"),
("28", "MOU-001", "Mouse Gamer 8 Botones RGB", "30", "unidad", "15.00", "29.00", "Logitech", "G203", "Activo", "mouse_gamer.jpg", "20", "15.00", "8", "45", "29.00", "30", "unidad", "10"),
("29", "MOU-002", "Mouse Inalámbrico Ergonómico", "22", "unidad", "25.00", "45.00", "Microsoft", "Sculpt Ergonomic", "Activo", "mouse_ergo.jpg", "20", "25.00", "5", "30", "45.00", "22", "unidad", "1"),
("30", "MON-001", "Monitor 24 IPS 75Hz Full HD", "10", "unidad", "120.00", "179.00", "LG", "24MP400-B", "Activo", "mon_lg24.jpg", "21", "120.00", "3", "15", "179.00", "10", "unidad", "1"),
("31", "MON-002", "Monitor Gamer 27 165Hz 1440p", "6", "unidad", "280.00", "399.00", "ASUS", "TUF VG27AQ", "Activo", "mon_asus27.jpg", "21", "280.00", "2", "10", "399.00", "6", "unidad", "1"),
("32", "CAM-001", "Webcam 1080p Full HD con Micrófono", "15", "unidad", "30.00", "55.00", "Logitech", "C920", "Activo", "webcam_c920.jpg", "22", "30.00", "4", "20", "55.00", "15", "unidad", "1"),
("33", "AUD-001", "Audífonos Gamer 7.1 Surround", "12", "unidad", "45.00", "79.00", "HyperX", "Cloud II", "Activo", "aud_hyperx.jpg", "23", "45.00", "3", "18", "79.00", "12", "unidad", "1"),
("34", "AUD-002", "Audífonos Bluetooth ANC", "8", "unidad", "80.00", "129.00", "Sony", "WH-CH720N", "Activo", "aud_sony.jpg", "23", "80.00", "2", "12", "129.00", "8", "unidad", "1"),
("35", "SSD-001", "SSD 500GB NVMe M.2", "25", "unidad", "35.00", "59.00", "Kingston", "NV2", "Activo", "ssd_nvme.jpg", "24", "35.00", "6", "40", "59.00", "25", "unidad", "10"),
("36", "SSD-002", "SSD 1TB NVMe M.2 Gen4", "15", "unidad", "75.00", "119.00", "Samsung", "980 Pro", "Activo", "ssd_samsung.jpg", "24", "75.00", "4", "20", "119.00", "15", "unidad", "5"),
("37", "HDD-001", "Disco Duro 2TB 5400RPM SATA", "12", "unidad", "55.00", "89.00", "Seagate", "Barracuda 2TB", "Activo", "hdd_2tb.jpg", "25", "55.00", "3", "18", "89.00", "12", "unidad", "1"),
("38", "USB-001", "USB Flash 64GB 3.1", "40", "unidad", "6.00", "14.00", "SanDisk", "Ultra Flair", "Activo", "usb_64.jpg", "26", "6.00", "10", "60", "14.00", "40", "unidad", "20"),
("39", "USB-002", "USB Flash 128GB 3.2", "30", "unidad", "12.00", "24.00", "Kingston", "DataTraveler", "Activo", "usb_128.jpg", "26", "12.00", "8", "45", "24.00", "30", "unidad", "20"),
("40", "SD-001", "Tarjeta SD 128GB U3 V30", "20", "unidad", "15.00", "29.00", "Samsung", "Pro Plus", "Activo", "sd_128.jpg", "27", "15.00", "5", "30", "29.00", "20", "unidad", "10"),
("41", "ROU-001", "Router WiFi 6 AX3000", "10", "unidad", "65.00", "99.00", "TP-Link", "Archer AX50", "Activo", "router_ax.jpg", "28", "65.00", "3", "15", "99.00", "10", "unidad", "1"),
("42", "ROU-002", "Router Mesh WiFi 6 2-pack", "6", "unidad", "130.00", "199.00", "Xiaomi", "Mesh AX3000", "Activo", "router_mesh.jpg", "28", "130.00", "2", "8", "199.00", "6", "unidad", "1"),
("43", "SWI-001", "Switch Gigabit 8 Puertos", "15", "unidad", "22.00", "39.00", "TP-Link", "TL-SG108", "Activo", "switch_8.jpg", "29", "22.00", "4", "20", "39.00", "15", "unidad", "1"),
("44", "CAB-001", "Cable Red Cat6 3m", "35", "unidad", "3.50", "10.00", "Nexxt", "Cat6-3m", "Activo", "cable_cat6.jpg", "30", "3.50", "10", "50", "10.00", "35", "unidad", "25"),
("45", "CAB-002", "Cable Red Cat6 10m", "25", "unidad", "7.00", "18.00", "Nexxt", "Cat6-10m", "Activo", "cable_cat6_10.jpg", "30", "7.00", "8", "40", "18.00", "25", "unidad", "20"),
("46", "ADP-001", "Adaptador WiFi USB AC1200", "18", "unidad", "12.00", "24.00", "TP-Link", "Archer T3U", "Activo", "adap_wifi.jpg", "31", "12.00", "5", "25", "24.00", "18", "unidad", "10"),
("47", "SPK-001", "Parlantes Bluetooth 20W", "10", "unidad", "40.00", "69.00", "JBL", "Flip 6", "Activo", "spk_jbl.jpg", "32", "40.00", "3", "15", "69.00", "10", "unidad", "1"),
("48", "MIC-001", "Micrófono Condensador USB", "8", "unidad", "50.00", "89.00", "Blue", "Yeti Nano", "Activo", "mic_yeti.jpg", "33", "50.00", "2", "12", "89.00", "8", "unidad", "1"),
("49", "IMP-001", "Impresora Multifuncional Tinta Continua", "7", "unidad", "180.00", "259.00", "Epson", "L3250", "Activo", "imp_epson.jpg", "34", "180.00", "2", "10", "259.00", "7", "unidad", "1"),
("50", "TINT-001", "Tinta Original Epson 4 Colores", "20", "unidad", "25.00", "45.00", "Epson", "T544", "Activo", "tinta_epson.jpg", "35", "25.00", "5", "30", "45.00", "20", "unidad", "4"),
("51", "SW-001", "Microsoft Office 365 Personal 1 año", "50", "licencia", "45.00", "69.00", "Microsoft", "Office 365", "Activo", "office365.jpg", "6", "45.00", "10", "100", "69.00", "50", "licencia", "1"),
("52", "SW-002", "Windows 11 Pro Licencia Digital", "30", "licencia", "80.00", "129.00", "Microsoft", "Win11 Pro", "Activo", "win11.jpg", "6", "80.00", "8", "50", "129.00", "30", "licencia", "1"),
("53", "SW-003", "Antivirus Kaspersky 1 año 3 dispositivos", "25", "licencia", "20.00", "39.00", "Kaspersky", "Total Security", "Activo", "kaspersky.jpg", "6", "20.00", "5", "40", "39.00", "25", "licencia", "1");


-- --------------------------------------------------------
-- Estructura de tabla para la tabla `producto_proveedor`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `producto_proveedor`;
CREATE TABLE `producto_proveedor` (
  `relacion_id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `proveedor_id` int(11) NOT NULL,
  `precio_compra_referencial` decimal(30,2) DEFAULT 0.00,
  `ultima_compra` datetime DEFAULT NULL,
  PRIMARY KEY (`relacion_id`),
  KEY `producto_id` (`producto_id`),
  KEY `proveedor_id` (`proveedor_id`),
  CONSTRAINT `producto_proveedor_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`) ON DELETE CASCADE,
  CONSTRAINT `producto_proveedor_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedor` (`proveedor_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `producto_proveedor`
INSERT INTO `producto_proveedor` VALUES
("1", "1", "1", "450.00", "2026-04-20 10:00:00"),
("2", "2", "1", "520.00", "2026-04-18 11:30:00"),
("3", "3", "1", "480.00", "2026-04-19 09:15:00"),
("4", "4", "3", "850.00", "2026-04-15 14:00:00"),
("5", "5", "1", "350.00", "2026-04-17 10:45:00"),
("6", "6", "2", "550.00", "2026-04-21 08:00:00"),
("7", "7", "2", "1200.00", "2026-04-10 16:00:00"),
("8", "8", "4", "180.00", "2026-04-12 11:00:00"),
("9", "9", "5", "600.00", "2026-04-14 13:30:00"),
("10", "10", "1", "150.00", "2026-04-20 10:00:00"),
("11", "11", "1", "170.00", "2026-04-20 10:30:00"),
("12", "12", "1", "350.00", "2026-04-19 14:00:00"),
("13", "13", "2", "22.00", "2026-04-22 09:00:00"),
("14", "14", "2", "40.00", "2026-04-22 09:30:00"),
("15", "15", "4", "110.00", "2026-04-18 15:00:00"),
("16", "16", "2", "85.00", "2026-04-21 10:00:00"),
("17", "17", "2", "120.00", "2026-04-21 10:30:00"),
("18", "18", "1", "320.00", "2026-04-19 11:00:00"),
("19", "19", "1", "280.00", "2026-04-19 11:30:00"),
("20", "20", "2", "55.00", "2026-04-22 14:00:00"),
("21", "21", "2", "110.00", "2026-04-22 14:30:00"),
("22", "22", "4", "45.00", "2026-04-20 16:00:00"),
("23", "23", "4", "65.00", "2026-04-20 16:30:00"),
("24", "24", "2", "30.00", "2026-04-22 15:00:00"),
("25", "25", "2", "25.00", "2026-04-22 15:30:00"),
("26", "26", "1", "40.00", "2026-04-20 08:00:00"),
("27", "27", "1", "18.00", "2026-04-20 08:30:00"),
("28", "28", "3", "15.00", "2026-04-21 14:00:00"),
("29", "29", "3", "25.00", "2026-04-21 14:30:00"),
("30", "30", "3", "120.00", "2026-04-19 10:00:00"),
("31", "31", "3", "280.00", "2026-04-19 10:30:00"),
("32", "32", "1", "30.00", "2026-04-20 12:00:00"),
("33", "33", "2", "45.00", "2026-04-22 11:00:00"),
("34", "34", "3", "80.00", "2026-04-18 09:00:00"),
("35", "35", "1", "35.00", "2026-04-21 16:00:00"),
("36", "36", "3", "75.00", "2026-04-21 16:30:00"),
("37", "37", "1", "55.00", "2026-04-20 17:00:00"),
("38", "38", "1", "6.00", "2026-04-22 08:00:00"),
("39", "39", "1", "12.00", "2026-04-22 08:30:00"),
("40", "40", "3", "15.00", "2026-04-21 15:00:00"),
("41", "41", "1", "65.00", "2026-04-20 14:00:00"),
("42", "42", "4", "130.00", "2026-04-18 12:00:00"),
("43", "43", "1", "22.00", "2026-04-21 12:00:00"),
("44", "44", "5", "3.50", "2026-04-16 10:00:00"),
("45", "45", "5", "7.00", "2026-04-16 10:30:00"),
("46", "46", "1", "12.00", "2026-04-21 13:00:00"),
("47", "47", "3", "40.00", "2026-04-19 15:00:00"),
("48", "48", "5", "50.00", "2026-04-17 14:00:00"),
("49", "49", "5", "180.00", "2026-04-17 14:30:00"),
("50", "50", "5", "25.00", "2026-04-17 15:00:00");


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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `proveedor`
INSERT INTO `proveedor` VALUES
("1", "Intcomex Venezuela", "J-11111111-1", "0212-1110001", "Av. Ppal. Los Ruices, Caracas"),
("2", "Distribuidora Tecnológica del Sur", "J-22222222-2", "0241-2220002", "Av. Bolívar Norte, Valencia"),
("3", "Samsung Electronics Venezuela", "J-33333333-3", "0212-3330003", "Torre Samsung, Chacao"),
("4", "Importaciones PC World C.A.", "J-44444444-4", "0261-4440004", "Av. 5 de Julio, Maracaibo"),
("5", "HP Inc. Venezuela", "J-55555555-5", "0212-5550005", "Centro Empresarial Sabana Grande, Caracas");


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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `recepcion`
INSERT INTO `recepcion` VALUES
("1", "1", "1", "2026-04-11", "Mercancía recibida en perfecto estado"),
("2", "2", "1", "2026-04-16", "Componentes recibidos con factura, todo OK"),
("3", "3", "1", "2026-04-19", "Recibido completo"),
("4", "4", "1", "2026-04-21", "Todo recibido correctamente");


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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `recepcion_detalle`
INSERT INTO `recepcion_detalle` VALUES
("1", "1", "1", "5"),
("2", "1", "2", "3"),
("3", "1", "10", "10"),
("4", "2", "6", "2"),
("5", "2", "13", "10"),
("6", "2", "14", "8"),
("7", "2", "20", "6"),
("8", "2", "22", "5"),
("9", "3", "4", "3"),
("10", "3", "30", "5"),
("11", "3", "31", "3"),
("12", "3", "36", "5"),
("13", "3", "40", "10"),
("14", "4", "8", "5"),
("15", "4", "15", "8"),
("16", "4", "22", "5"),
("17", "4", "23", "4"),
("18", "4", "42", "4");


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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `usuario`
INSERT INTO `usuario` VALUES
("1", "V", "0", "Administrador", "Principal", "Administrador@gmail.com", "Administrador", "$2y$10$Jgm6xFb5Onz/BMdIkNK2Tur8yg/NYEMb/tdnhoV7kB1BwIG4R05D2", "Administrador_23.jpg", "1", "1", NULL, NULL, NULL, NULL, NULL, NULL, "Activo"),
("2", "V", "32600641", "Ander", "Peña", "ander@gmail.com", "Andflizzz", "$2y$10$W3MLRVLBLol2TzHFvzNPSuKKmmkwouQeXlMcJFVbcfHJJ9sIc8LbG", "", "1", "3", "Nombre de tu primera mascota", "$2y$10$jjVpY6e9YCarAQkmkeeqeu49sRJkmtOQlRCuQpeqPw5nFsJxNmjXC", "Nombre de tu escuela primaria", "$2y$10$j7sy5itpzo5G4JUYe94JfOjvMwrrei9/6JOBgHqmsmxTx5v0ZU.f2", "Marca de tu primer carro", "$2y$10$DNHThBnGDS5QoTaruaFHzeYaatQaLpMbSUwuIlXgja.y11eAiU0pG", "Activo");


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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `venta`
INSERT INTO `venta` VALUES
("1", "VENT-2026-001", "2026-04-12", "10:30:00", "948.00", "1000.00", "52.00", "35.50", "1", "1", "1", "Efectivo", NULL),
("2", "VENT-2026-002", "2026-04-15", "14:00:00", "2298.00", "2298.00", "0.00", "35.80", "1", "3", "1", "Transferencia", "TRF-TECHSOL-15042026"),
("3", "VENT-2026-003", "2026-04-18", "11:00:00", "1049.00", "1100.00", "51.00", "36.00", "1", "6", "1", "Efectivo", NULL),
("4", "VENT-2026-004", "2026-04-20", "16:00:00", "3108.00", "3108.00", "0.00", "36.20", "1", "5", "1", "Transferencia", "TRF-INVDIG-20042026"),
("5", "VENT-2026-005", "2026-04-22", "09:30:00", "563.00", "563.00", "0.00", "36.50", "1", "7", "1", "Débito", NULL),
("6", "VENT-2026-006", "2026-04-25", "15:00:00", "4845.00", "4845.00", "0.00", "36.80", "1", "10", "1", "Transferencia", "TRF-CYBNET-25042026"),
("7", "VENT-2026-007", "2026-04-26", "12:00:00", "514.00", "520.00", "6.00", "37.00", "1", "4", "1", "Efectivo", NULL),
("8", "VENT-2026-008", "2026-04-26", "16:00:00", "2523.00", "2523.00", "0.00", "37.00", "1", "8", "1", "Transferencia", "TRF-DCEXP-26042026");


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
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `venta_detalle`
INSERT INTO `venta_detalle` VALUES
("1", "1", "450.00", "599.00", "599.00", "Laptop HP 15.6 Core i5", "VENT-2026-001", "1"),
("2", "1", "35.00", "59.00", "59.00", "SSD 500GB NVMe", "VENT-2026-001", "35"),
("3", "2", "40.00", "69.00", "138.00", "Teclado Mecánico RGB", "VENT-2026-001", "26"),
("4", "2", "15.00", "29.00", "58.00", "Mouse Gamer 8 Botones", "VENT-2026-001", "28"),
("5", "1", "25.00", "45.00", "45.00", "Ventilador 120mm RGB", "VENT-2026-001", "25"),
("6", "1", "6.00", "14.00", "14.00", "USB Flash 64GB", "VENT-2026-001", "38"),
("7", "1", "35.00", "35.00", "35.00", "Oferta especial", "VENT-2026-001", "27"),
("8", "2", "520.00", "699.00", "1398.00", "Laptop Dell Inspiron 15", "VENT-2026-002", "2"),
("9", "2", "120.00", "179.00", "358.00", "Monitor 24 IPS", "VENT-2026-002", "30"),
("10", "2", "30.00", "55.00", "110.00", "Webcam 1080p", "VENT-2026-002", "32"),
("11", "1", "180.00", "259.00", "259.00", "Mini PC Intel N95", "VENT-2026-002", "8"),
("12", "1", "12.00", "24.00", "24.00", "Adaptador WiFi USB", "VENT-2026-002", "46"),
("13", "1", "45.00", "69.00", "69.00", "Office 365 Personal", "VENT-2026-002", "48"),
("14", "2", "20.00", "39.00", "78.00", "Antivirus Kaspersky", "VENT-2026-002", "50"),
("15", "1", "2.00", "2.00", "2.00", "Ajuste por redondeo", "VENT-2026-002", "44"),
("16", "1", "480.00", "649.00", "649.00", "Laptop Lenovo IdeaPad 3 Ryzen 5", "VENT-2026-003", "3"),
("17", "1", "320.00", "449.00", "449.00", "Tarjeta Video RTX 4060", "VENT-2026-003", "18"),
("18", "1", "15.00", "29.00", "29.00", "Tarjeta SD 128GB", "VENT-2026-003", "40"),
("19", "1", "-78.00", "-78.00", "-78.00", "Descuento por combo", "VENT-2026-003", "50"),
("20", "2", "550.00", "749.00", "1498.00", "PC Escritorio Intel Core i5", "VENT-2026-004", "6"),
("21", "1", "1200.00", "1599.00", "1599.00", "PC Gamer Ryzen 7 RTX 4060", "VENT-2026-004", "7"),
("22", "2", "65.00", "99.00", "198.00", "Router WiFi 6 AX3000", "VENT-2026-004", "41"),
("23", "2", "22.00", "39.00", "78.00", "Switch Gigabit 8 Puertos", "VENT-2026-004", "43"),
("24", "10", "3.50", "10.00", "100.00", "Cable Red Cat6 3m", "VENT-2026-004", "44"),
("25", "5", "7.00", "18.00", "90.00", "Cable Red Cat6 10m", "VENT-2026-004", "45"),
("26", "1", "-455.00", "-455.00", "-455.00", "Descuento por volumen", "VENT-2026-004", "49"),
("27", "1", "350.00", "459.00", "459.00", "Laptop Asus VivoBook 15", "VENT-2026-005", "5"),
("28", "1", "150.00", "199.00", "199.00", "Procesador Intel Core i5-12400F", "VENT-2026-005", "10"),
("29", "1", "85.00", "129.00", "129.00", "Motherboard ASUS Prime H610M-E", "VENT-2026-005", "16"),
("30", "1", "-224.00", "-224.00", "-224.00", "Promoción Arma tu PC", "VENT-2026-005", "50"),
("31", "5", "450.00", "599.00", "2995.00", "Laptop HP 15.6 Core i5", "VENT-2026-006", "1"),
("32", "5", "120.00", "179.00", "895.00", "Monitor 24 IPS Full HD", "VENT-2026-006", "30"),
("33", "5", "30.00", "55.00", "275.00", "Webcam 1080p", "VENT-2026-006", "32"),
("34", "5", "40.00", "69.00", "345.00", "Teclado Mecánico RGB", "VENT-2026-006", "26"),
("35", "5", "15.00", "29.00", "145.00", "Mouse Gamer 8 Botones", "VENT-2026-006", "28"),
("36", "5", "35.00", "59.00", "295.00", "SSD 500GB NVMe M.2", "VENT-2026-006", "35"),
("37", "5", "45.00", "69.00", "345.00", "Office 365 Personal", "VENT-2026-006", "48"),
("38", "1", "-450.00", "-450.00", "-450.00", "Descuento compra corporativa 5 equipos", "VENT-2026-006", "50"),
("39", "1", "170.00", "229.00", "229.00", "Procesador AMD Ryzen 5 5600X", "VENT-2026-007", "11"),
("40", "1", "120.00", "179.00", "179.00", "Motherboard MSI B550-A Pro", "VENT-2026-007", "17"),
("41", "2", "40.00", "69.00", "138.00", "Memoria RAM 16GB DDR4", "VENT-2026-007", "14"),
("42", "1", "-32.00", "-32.00", "-32.00", "Combo motherboard + RAM", "VENT-2026-007", "50"),
("43", "2", "110.00", "159.00", "318.00", "Fuente Poder 850W 80+ Gold", "VENT-2026-008", "21"),
("44", "4", "75.00", "119.00", "476.00", "SSD 1TB NVMe Gen4", "VENT-2026-008", "36"),
("45", "4", "55.00", "89.00", "356.00", "Disco Duro 2TB SATA", "VENT-2026-008", "37"),
("46", "2", "130.00", "199.00", "398.00", "Router Mesh WiFi 6", "VENT-2026-008", "42"),
("47", "2", "180.00", "259.00", "518.00", "Impresora Multifuncional Tinta Continua", "VENT-2026-008", "49"),
("48", "10", "25.00", "45.00", "450.00", "Tinta Original Epson", "VENT-2026-008", "50"),
("49", "1", "7.00", "7.00", "7.00", "Ajuste facturación", "VENT-2026-008", "45");


-- Reactivar restricciones de llaves foráneas
SET FOREIGN_KEY_CHECKS = 1;
