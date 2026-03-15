SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";



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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;






CREATE TABLE `caja` (
  `caja_id` int(5) NOT NULL AUTO_INCREMENT,
  `caja_numero` int(5) NOT NULL,
  `caja_nombre` varchar(100) NOT NULL,
  `caja_efectivo` decimal(30,2) NOT NULL,
  PRIMARY KEY (`caja_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;


INSERT INTO caja VALUES
("1","1","Caja Principal","960.00");




CREATE TABLE `categoria` (
  `categoria_id` int(7) NOT NULL AUTO_INCREMENT,
  `categoria_nombre` varchar(50) NOT NULL,
  `categoria_padre_id` int(11) DEFAULT NULL,
  `categoria_ubicacion` varchar(150) NOT NULL,
  `categoria_unidades` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`categoria_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;


INSERT INTO categoria VALUES
("1","Computación","","",""),
("2","Laptops","1","",""),
("3","Monitores","1","",""),
("4","All in one","1","",""),
("6","PC de escritorio","1","",""),
("7","Impresión y oficina","","",""),
("8","Consumibles","7","",""),
("9","Componentes de PC","","",""),
("10","Unidades de almacenamiento","9","",""),
("11","Gabinetes","9","",""),
("12","Periféricos","","",""),
("13","Audífonos","12","","");




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


INSERT INTO cliente VALUES
("1","Otro","N/A","Publico","General","N/A","N/A","N/A","N/A","N/A");




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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;


INSERT INTO compra VALUES
("1","COM-000001","2026-03-15","700.00","446.80","1","1","Completado","","0.00","Pagado","2026-03-15"),
("2","COM-000002","2026-03-15","800.00","446.80","1","1","Completado","","0.00","Pagado","2026-03-15"),
("3","COM-000003","2026-03-15","800.00","446.80","1","1","Completado","","0.00","Pagado","2026-03-15"),
("4","COM-000004","2026-03-15","800.00","446.80","1","1","Completado","","-100.00","Pagado","2026-03-15");




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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;


INSERT INTO compra_detalle VALUES
("1","1","1","1","700.00"),
("2","2","1","1","800.00"),
("3","3","1","1","800.00"),
("4","4","1","1","800.00");




CREATE TABLE `compra_pagos` (
  `pago_id` int(11) NOT NULL AUTO_INCREMENT,
  `compra_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
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


INSERT INTO compra_pagos VALUES
("1","1","1","2026-03-15","100.00","Efectivo","EFECTIVO/DIVISA",""),
("2","1","1","2026-03-15","600.00","Efectivo","EFECTIVO/DIVISA",""),
("3","2","1","2026-03-15","800.00","Efectivo","EFECTIVO/DIVISA",""),
("4","4","1","2026-03-15","900.00","Anticipo","Pago al ordenar",""),
("5","3","1","2026-03-15","800.00","Efectivo","EFECTIVO/DIVISA","");




CREATE TABLE `empresa` (
  `empresa_id` int(11) NOT NULL AUTO_INCREMENT,
  `empresa_nombre` varchar(90) NOT NULL,
  `empresa_rif` varchar(40) NOT NULL,
  `empresa_telefono` varchar(20) NOT NULL,
  `empresa_emailKV` varchar(50) NOT NULL,
  `empresa_direccion` varchar(100) NOT NULL,
  PRIMARY KEY (`empresa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;


INSERT INTO empresa VALUES
("1","Fasnet Lideres en Tecnología","J-29665886-2","04127465438","Fasnet.comunicaciones@gmail.com","Av. Anthons Phillips cc Av Merida local galpon Nro Sn Zona Industrial La Hamaca");




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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;


INSERT INTO producto VALUES
("1","2498217649872","Laptop gamer","0","","0.00","0.00","HP","I9 9700k","Activo","","2","800.00","5","100","960.00","8","Unidad","1");




CREATE TABLE `proveedor` (
  `proveedor_id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_nombre` varchar(100) NOT NULL,
  `proveedor_rif` varchar(30) NOT NULL,
  `proveedor_telefono` varchar(20) DEFAULT NULL,
  `proveedor_direccion` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`proveedor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;


INSERT INTO proveedor VALUES
("1","Conputodo","71281237-2","2236789","");




CREATE TABLE `recepcion` (
  `recepcion_id` int(11) NOT NULL AUTO_INCREMENT,
  `compra_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `recepcion_fecha` date NOT NULL,
  `recepcion_nota` text DEFAULT NULL,
  PRIMARY KEY (`recepcion_id`),
  KEY `compra_id` (`compra_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `recepcion_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compra` (`compra_id`),
  CONSTRAINT `recepcion_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO recepcion VALUES
("1","1","1","2026-03-15",""),
("2","2","1","2026-03-15","Se subio el precio"),
("3","3","1","2026-03-15",""),
("4","4","1","2026-03-15","");




CREATE TABLE `recepcion_detalle` (
  `recepcion_detalle_id` int(11) NOT NULL AUTO_INCREMENT,
  `recepcion_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad_recibida` int(11) NOT NULL,
  PRIMARY KEY (`recepcion_detalle_id`),
  KEY `recepcion_id` (`recepcion_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `recepcion_detalle_ibfk_1` FOREIGN KEY (`recepcion_id`) REFERENCES `recepcion` (`recepcion_id`),
  CONSTRAINT `recepcion_detalle_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO recepcion_detalle VALUES
("1","1","1","1"),
("2","2","1","1"),
("3","3","1","1"),
("4","4","1","1");




CREATE TABLE `rol` (
  `rol_id` int(11) NOT NULL AUTO_INCREMENT,
  `rol_nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`rol_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;


INSERT INTO rol VALUES
("1","Administrador"),
("2","Vendedor"),
("3","Supervisor");




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
  `usuario_estado` varchar(20) DEFAULT 'Activo',
  PRIMARY KEY (`usuario_id`),
  KEY `caja_id` (`caja_id`),
  KEY `fk_usuario_rol` (`rol_id`),
  CONSTRAINT `fk_usuario_caja` FOREIGN KEY (`caja_id`) REFERENCES `caja` (`caja_id`),
  CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`rol_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;


INSERT INTO usuario VALUES
("1","V","0","Administrador","Principal","Administrador@gmail.com","Administrador","$2y$10$Jgm6xFb5Onz/BMdIkNK2Tur8yg/NYEMb/tdnhoV7kB1BwIG4R05D2","","1","1","Activo"),
("2","V","31209801","Fabio","Cadenas","fabio.informatico@gmail.com","Fabio123","$2y$10$xQNJn7GW1Ds0q.DNap8IDOJTtGVviz.BScZ/G8pJTvCV98Pqq4oHi","","1","2","Activo");




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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;


INSERT INTO venta VALUES
("1","VEN-000001","2026-03-15","10:57 am","960.00","960.00","0.00","446.80","1","1","1","Efectivo","");




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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;


INSERT INTO venta_detalle VALUES
("1","1","800.00","960.00","960.00","Laptop gamer","VEN-000001","1");


