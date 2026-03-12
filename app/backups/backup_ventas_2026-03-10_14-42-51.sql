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
) ENGINE=InnoDB AUTO_INCREMENT=235 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;


INSERT INTO bitacora VALUES
("1","1","2026-02-19","10:34:56 pm","Seguridad","Inicio de Sesión","El usuario Administrador accedió al sistema."),
("2","1","2026-02-19","10:45:53 pm","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("3","1","2026-02-19","10:45:58 pm","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("4","1","2026-02-20","07:47:07 am","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("5","1","2026-02-20","07:47:10 am","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("6","1","2026-02-20","07:52:56 am","Productos","Eliminación","Se eliminó el producto: CACOTA"),
("7","1","2026-02-20","07:58:22 am","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("8","1","2026-02-20","07:58:36 am","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("9","1","2026-02-20","08:04:31 am","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("10","1","2026-02-20","08:10:45 am","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("11","1","2026-02-24","08:53:25 pm","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("12","1","2026-02-24","08:53:31 pm","Productos","Estado","Se cambió el estado de ALEX a Inactivo"),
("13","1","2026-02-24","08:53:34 pm","Productos","Estado","Se cambió el estado de ALEX a Activo"),
("14","1","2026-02-24","09:20:01 pm","Clientes","Registro","Se registró el cliente: Alexander Cadena"),
("15","1","2026-02-24","09:20:09 pm","Clientes","Actualización","Se actualizaron los datos del cliente: Alexander Cadena"),
("16","1","2026-02-24","09:22:00 pm","Proveedores","Actualización","Se actualizaron datos del proveedor: Polar"),
("17","1","2026-02-24","09:34:12 pm","Ventas","Nueva Venta","Venta realizada con código: F6Y7H9Q0I5-3 por un total de $120.00"),
("18","1","2026-02-24","09:46:22 pm","Ventas","Nueva Venta","Venta realizada con código: T7X7Y6X4U2-4 por un total de $120.00"),
("19","1","2026-02-24","09:52:06 pm","Ventas","Anulación","Se anuló la venta con código: O4L2D4N9Y6-1"),
("20","1","2026-02-24","09:52:08 pm","Ventas","Anulación","Se anuló la venta con código: K3I3W8J6M7-2"),
("21","1","2026-02-24","09:52:10 pm","Ventas","Anulación","Se anuló la venta con código: F6Y7H9Q0I5-3"),
("22","1","2026-02-24","09:56:06 pm","Proveedores","Actualización","Se actualizaron datos del proveedor: xddd"),
("23","1","2026-02-24","10:14:01 pm","Clientes","Actualización","Se actualizaron los datos del cliente: Alexander Cadena"),
("24","1","2026-02-25","08:12:24 am","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("25","1","2026-02-25","08:14:26 am","Proveedores","Registro","Se registró el proveedor: Alive (RIF: J-1245466)"),
("26","1","2026-02-25","08:18:49 am","Productos","Registro","Se registró el producto: Laptop (Cód: 100)"),
("27","1","2026-02-25","08:22:29 am","Productos","Estado","Se cambió el estado de Laptop a Inactivo"),
("28","1","2026-02-25","08:22:46 am","Productos","Estado","Se cambió el estado de Laptop a Activo"),
("29","1","2026-02-25","08:34:09 am","Ventas","Nueva Venta","Venta realizada con código: T4Q6F5E0S4-2 por un total de $12.00"),
("30","1","2026-02-25","08:35:36 am","Productos","Actualización","Datos actualizados del producto: Laptop"),
("31","1","2026-02-25","08:35:47 am","Productos","Actualización","Datos actualizados del producto: Laptop"),
("32","1","2026-02-25","08:35:59 am","Productos","Actualización","Datos actualizados del producto: Laptop"),
("33","1","2026-02-25","08:36:20 am","Productos","Estado","Se cambió el estado de Laptop a Inactivo"),
("34","1","2026-02-25","08:43:41 am","Productos","Estado","Se cambió el estado de Laptop a Activo"),
("35","1","2026-02-25","08:43:50 am","Productos","Estado","Se cambió el estado de Laptop a Inactivo"),
("36","1","2026-02-25","08:45:42 am","Productos","Actualización","Datos actualizados del producto: Laptop"),
("37","1","2026-02-25","08:46:28 am","Clientes","Actualización","Se actualizaron los datos del cliente: Alexander Cadena"),
("38","1","2026-02-25","08:50:44 am","Productos","Estado","Se cambió el estado de Laptop a Activo"),
("39","1","2026-02-25","08:53:11 am","Productos","Actualización","Datos actualizados del producto: Laptop"),
("40","1","2026-02-25","08:56:31 am","Productos","Actualización","Datos actualizados del producto: Laptop"),
("41","1","2026-02-25","08:56:39 am","Productos","Actualización","Datos actualizados del producto: Laptop"),
("42","1","2026-02-25","09:48:51 am","Productos","Actualización","Datos actualizados del producto: Laptop"),
("43","1","2026-02-25","09:49:00 am","Productos","Actualización","Datos actualizados del producto: Laptop"),
("44","1","2026-02-25","09:49:49 am","Productos","Actualización","Datos actualizados del producto: Laptop"),
("45","1","2026-02-25","09:50:36 am","Proveedores","Actualización","Se actualizaron datos del proveedor: Alive"),
("46","1","2026-02-25","09:51:40 am","Proveedores","Actualización","Se actualizaron datos del proveedor: Alive"),
("47","1","2026-02-25","09:51:49 am","Proveedores","Actualización","Se actualizaron datos del proveedor: Alive"),
("48","1","2026-02-25","09:52:22 am","Productos","Actualización","Datos actualizados del producto: Laptop"),
("49","1","2026-02-25","09:53:44 am","Productos","Actualización","Datos actualizados del producto: Laptop"),
("50","1","2026-02-25","10:03:49 am","Productos","Actualización","Datos actualizados del producto: Ryzen"),
("51","1","2026-02-28","09:37:25 pm","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("52","1","2026-02-28","09:37:47 pm","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("53","1","2026-02-28","09:39:03 pm","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("54","1","2026-02-28","09:43:58 pm","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("55","1","2026-02-28","09:45:06 pm","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("56","1","2026-02-28","10:25:37 pm","Seguridad","Inicio de Sesión","El usuario con correo ander@gmail.com entró al sistema."),
("57","1","2026-02-28","10:25:41 pm","Seguridad","Cierre de Sesión","El usuario andflizzz salió del sistema."),
("58","1","2026-02-28","10:25:59 pm","Seguridad","Inicio de Sesión","El usuario con correo ander@gmail.com entró al sistema."),
("59","1","2026-02-28","10:26:07 pm","Seguridad","Cierre de Sesión","El usuario andflizzz salió del sistema."),
("60","1","2026-02-28","10:26:28 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("61","1","2026-02-28","10:36:24 pm","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("62","1","2026-02-28","10:37:15 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("63","1","2026-02-28","10:47:07 pm","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("64","1","2026-02-28","10:49:09 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("65","1","2026-02-28","11:07:31 pm","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("66","1","2026-03-01","06:52:37 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("67","1","2026-03-01","06:52:54 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("68","1","2026-03-01","07:16:22 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("69","1","2026-03-01","07:17:32 pm","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("70","1","2026-03-01","07:36:06 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("71","1","2026-03-01","07:38:44 pm","Clientes","Actualización","Se actualizaron los datos del cliente: Alexander Cadena"),
("72","1","2026-03-01","07:38:47 pm","Clientes","Actualización","Se actualizaron los datos del cliente: Alexander Cadena"),
("73","1","2026-03-01","07:59:42 pm","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("74","1","2026-03-01","08:01:10 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("75","1","2026-03-01","08:02:57 pm","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("76","11","2026-03-01","08:03:11 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador2@gmail.com entró al sistema."),
("77","11","2026-03-01","08:15:14 pm","Seguridad","Cierre de Sesión","El usuario admin salió del sistema."),
("78","1","2026-03-01","08:32:40 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("79","1","2026-03-03","10:20:28 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("80","1","2026-03-03","10:21:38 pm","Clientes","Registro","Se registró el cliente: Gabruek zapata"),
("81","1","2026-03-03","10:22:02 pm","Clientes","Actualización","Se actualizaron los datos del cliente: Alexander Cadena"),
("82","1","2026-03-03","10:22:07 pm","Clientes","Actualización","Se actualizaron los datos del cliente: Alexander Cadena"),
("83","1","2026-03-03","10:22:28 pm","Clientes","Actualización","Se actualizaron los datos del cliente: Gabruek zapata"),
("84","1","2026-03-03","10:23:50 pm","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("85","1","2026-03-03","10:27:00 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("86","1","2026-03-03","10:27:05 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("87","1","2026-03-03","10:28:23 pm","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("88","1","2026-03-03","10:29:49 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("89","1","2026-03-03","10:33:20 pm","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("90","1","2026-03-03","10:33:35 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("91","1","2026-03-03","10:47:10 pm","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("92","1","2026-03-03","10:47:15 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("93","1","2026-03-03","10:49:33 pm","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("94","1","2026-03-03","10:50:23 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("95","1","2026-03-03","10:52:28 pm","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("96","1","2026-03-03","10:53:03 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("97","1","2026-03-03","10:55:00 pm","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("98","1","2026-03-03","10:57:51 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("99","1","2026-03-03","10:57:55 pm","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("100","1","2026-03-03","11:06:09 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema.");
INSERT INTO bitacora VALUES
("101","1","2026-03-03","11:10:47 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("102","1","2026-03-03","11:11:53 pm","Proveedores","Registro","Se registró el proveedor: SSDSD (RIF: 232323)"),
("103","1","2026-03-03","11:16:40 pm","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("104","1","2026-03-03","11:34:09 pm","Productos","Registro","Se registró el producto: assdddddd (Cód: 232333232)"),
("105","1","2026-03-03","11:36:24 pm","Productos","Registro","Se registró el producto: asasss (Cód: 12121212)"),
("106","1","2026-03-03","11:36:37 pm","Productos","Registro","Se registró el producto: asasss (Cód: 121212123)"),
("107","1","2026-03-03","11:36:40 pm","Productos","Registro","Se registró el producto: asasss (Cód: 1111)"),
("108","1","2026-03-03","11:36:54 pm","Productos","Registro","Se registró el producto: asasss (Cód: 111155)"),
("109","1","2026-03-03","11:37:08 pm","Productos","Eliminación","Se eliminó el producto: assdddddd"),
("110","1","2026-03-03","11:37:10 pm","Productos","Eliminación","Se eliminó el producto: asasss"),
("111","1","2026-03-03","11:37:12 pm","Productos","Eliminación","Se eliminó el producto: asasss"),
("112","1","2026-03-03","11:37:14 pm","Productos","Eliminación","Se eliminó el producto: asasss"),
("113","1","2026-03-03","11:37:17 pm","Productos","Eliminación","Se eliminó el producto: asasss"),
("114","1","2026-03-03","11:51:45 pm","Productos","Registro","Se registró el producto: añe (Cód: 3244444444444)"),
("115","1","2026-03-03","11:53:02 pm","Productos","Registro","Se registró el producto: añe (Cód: 3244444444443)"),
("116","1","2026-03-03","11:53:11 pm","Productos","Registro","Se registró el producto: añe (Cód: 324444444443)"),
("117","1","2026-03-03","11:53:21 pm","Productos","Registro","Se registró el producto: añess (Cód: 3244444444433)"),
("118","1","2026-03-03","11:53:56 pm","Productos","Registro","Se registró el producto: asss (Cód: 23112121)"),
("119","1","2026-03-03","11:54:08 pm","Productos","Registro","Se registró el producto: asss (Cód: 231121213)"),
("120","1","2026-03-03","11:54:14 pm","Productos","Eliminación","Se eliminó el producto: añe"),
("121","1","2026-03-03","11:54:16 pm","Productos","Eliminación","Se eliminó el producto: añe"),
("122","1","2026-03-03","11:54:18 pm","Productos","Eliminación","Se eliminó el producto: añe"),
("123","1","2026-03-03","11:54:20 pm","Productos","Eliminación","Se eliminó el producto: añess"),
("124","1","2026-03-03","11:54:23 pm","Productos","Eliminación","Se eliminó el producto: asss"),
("125","1","2026-03-03","11:54:26 pm","Productos","Eliminación","Se eliminó el producto: asss"),
("126","1","2026-03-03","11:57:11 pm","Productos","Registro","Se registró el producto: alpaca (Cód: 122)"),
("127","1","2026-03-03","11:57:21 pm","Productos","Registro","Se registró el producto: alpaca3 (Cód: 1223)"),
("128","1","2026-03-03","11:57:27 pm","Productos","Eliminación","Se eliminó el producto: alpaca"),
("129","1","2026-03-03","11:57:30 pm","Productos","Eliminación","Se eliminó el producto: alpaca3"),
("130","1","2026-03-03","11:57:43 pm","Productos","Registro","Se registró el producto: asasas (Cód: 1222)"),
("131","1","2026-03-04","12:06:35 am","Productos","Registro","Se registró el producto: asasa (Cód: 121221221)"),
("132","1","2026-03-04","12:07:15 am","Productos","Registro","Se registró el producto: alask (Cód: 44444)"),
("133","1","2026-03-04","12:07:43 am","Productos","Actualización","Datos actualizados del producto: alask"),
("134","1","2026-03-04","12:07:50 am","Productos","Actualización","Datos actualizados del producto: alask"),
("135","1","2026-03-04","12:08:31 am","Productos","Actualización","Datos actualizados del producto: alask"),
("136","1","2026-03-04","12:08:41 am","Productos","Actualización","Datos actualizados del producto: alask"),
("137","1","2026-03-04","12:08:53 am","Productos","Actualización","Datos actualizados del producto: alask"),
("138","1","2026-03-04","12:15:02 am","Productos","Eliminación","Se eliminó el producto: alask"),
("139","1","2026-03-04","12:24:45 am","Categorías","Registro","Se registró la categoría: asass"),
("140","1","2026-03-04","12:24:48 am","Categorías","Registro","Se registró la categoría: sssssss"),
("141","1","2026-03-04","12:24:51 am","Categorías","Registro","Se registró la categoría: 33333"),
("142","1","2026-03-04","12:24:54 am","Categorías","Registro","Se registró la categoría: sassasas3"),
("143","1","2026-03-04","12:24:56 am","Categorías","Registro","Se registró la categoría: sdsddsdds"),
("144","1","2026-03-04","12:25:07 am","Categorías","Registro","Se registró la categoría: 4344343"),
("145","1","2026-03-04","12:25:10 am","Categorías","Registro","Se registró la categoría: sdsdsdsds"),
("146","1","2026-03-04","12:25:13 am","Categorías","Registro","Se registró la categoría: asasasasasaa"),
("147","1","2026-03-04","12:25:22 am","Categorías","Registro","Se registró la categoría: as2323"),
("148","1","2026-03-04","12:25:52 am","Categorías","Eliminación","Se eliminó la categoría: 33333"),
("149","1","2026-03-04","12:25:55 am","Categorías","Eliminación","Se eliminó la categoría: 4344343"),
("150","1","2026-03-04","12:25:57 am","Categorías","Eliminación","Se eliminó la categoría: as2323"),
("151","1","2026-03-04","12:25:59 am","Categorías","Eliminación","Se eliminó la categoría: asasasasasaa"),
("152","1","2026-03-04","12:26:02 am","Categorías","Eliminación","Se eliminó la categoría: asass"),
("153","1","2026-03-04","12:26:04 am","Categorías","Eliminación","Se eliminó la categoría: sassasas3"),
("154","1","2026-03-04","12:26:07 am","Categorías","Eliminación","Se eliminó la categoría: sdsddsdds"),
("155","1","2026-03-04","12:26:10 am","Categorías","Eliminación","Se eliminó la categoría: sdsdsdsds"),
("156","1","2026-03-04","12:26:12 am","Categorías","Eliminación","Se eliminó la categoría: sssssss"),
("157","1","2026-03-04","12:26:32 am","Productos","Eliminación","Se eliminó el producto: asasa"),
("158","1","2026-03-04","12:27:38 am","Clientes","Actualización","Se actualizaron los datos del cliente: Alexander Cadena"),
("159","1","2026-03-04","12:30:12 am","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("160","1","2026-03-04","12:34:08 am","Seguridad","Inicio de Sesión","El usuario con correo Administrador@gmail.com entró al sistema."),
("161","1","2026-03-04","12:54:25 am","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("162","1","2026-03-04","01:04:48 am","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("163","1","2026-03-04","01:05:43 am","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("164","1","2026-03-04","01:06:06 am","Seguridad","Inicio de Sesión","El usuario asas entró al sistema."),
("165","1","2026-03-04","01:06:17 am","Seguridad","Cierre de Sesión","El usuario asas salió del sistema."),
("166","1","2026-03-04","01:07:00 am","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("167","1","2026-03-04","01:07:22 am","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("168","1","2026-03-04","01:09:09 am","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("169","1","2026-03-04","01:12:06 am","Seguridad","Cierre de Sesión","El usuario Administrador salió del sistema."),
("170","1","2026-03-04","01:16:21 am","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("171","1","2026-03-04","01:38:10 am","Ventas","Nueva Venta","Venta realizada con código: U1V5V8Q8K2-3 por un total de $120.00"),
("172","1","2026-03-04","01:38:51 am","Ventas","Anulación","Se anuló la venta con código: U1V5V8Q8K2-3"),
("173","1","2026-03-04","01:39:16 am","Ventas","Nueva Venta","Venta realizada con código: V5U4M5W0R4-3 por un total de $240.00"),
("174","1","2026-03-04","01:39:22 am","Ventas","Anulación","Se anuló la venta con código: V5U4M5W0R4-3"),
("175","1","2026-03-04","01:40:05 am","Ventas","Nueva Venta","Venta realizada con código: O9Q1E5T9X0-3 por un total de $132.00"),
("176","1","2026-03-04","01:45:40 am","Ventas","Nueva Venta","Venta realizada con código: J8G3H0U2R2-4 por un total de $120.00"),
("177","1","2026-03-04","01:58:13 am","Clientes","Registro","Se registró el cliente: fabioaa cadenasaa"),
("178","1","2026-03-04","01:58:30 am","Clientes","Eliminación","Se eliminó el cliente: fabioaa cadenasaa"),
("179","1","2026-03-04","02:09:36 am","Proveedores","Registro","Se registró el proveedor: ariel (RIF: 121212121-2)"),
("180","1","2026-03-04","02:31:57 am","Proveedores","Actualización","Se actualizaron datos del proveedor: Alive"),
("181","1","2026-03-04","02:43:15 am","Clientes","Actualización","Se actualizaron los datos del cliente: Alexander Cadena"),
("182","1","2026-03-04","02:54:18 am","Proveedores","Actualización","Se actualizaron datos del proveedor: Alive"),
("183","1","2026-03-04","02:54:24 am","Proveedores","Actualización","Se actualizaron datos del proveedor: Polar"),
("184","1","2026-03-04","02:54:30 am","Proveedores","Actualización","Se actualizaron datos del proveedor: xddd"),
("185","1","2026-03-08","06:55:50 pm","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("186","1","2026-03-08","06:57:37 pm","Categorías","Registro","Se registró la categoría: laptops"),
("187","1","2026-03-09","07:40:06 pm","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("188","1","2026-03-09","07:48:07 pm","Clientes","Actualización","Se actualizaron los datos del cliente: Alexander Cadena"),
("189","1","2026-03-09","07:48:50 pm","Clientes","Actualización","Se actualizaron los datos del cliente: Alexander Cadena"),
("190","1","2026-03-09","07:49:07 pm","Clientes","Actualización","Se actualizaron los datos del cliente: Gabruek zapata"),
("191","1","2026-03-09","07:55:17 pm","Clientes","Actualización","Se actualizaron los datos del cliente: Alexander Cadena"),
("192","1","2026-03-09","07:55:48 pm","Clientes","Actualización","Se actualizaron los datos del cliente: Gabruek zapata"),
("193","1","2026-03-09","07:59:45 pm","Clientes","Actualización","Se actualizaron los datos del cliente: Alexander Cadena"),
("194","1","2026-03-09","08:00:13 pm","Clientes","Actualización","Se actualizaron los datos del cliente: Alexander Cadena"),
("195","1","2026-03-09","08:01:40 pm","Clientes","Actualización","Se actualizaron los datos del cliente: Alexander Cadena"),
("196","1","2026-03-09","08:12:18 pm","Categorías","Actualización","Se actualizaron los datos de la categoría: laptopsssss"),
("197","1","2026-03-09","08:24:42 pm","Categorías","Actualización","Se actualizaron los datos de la Subcategoría: laptop"),
("198","1","2026-03-09","08:25:42 pm","Categorías","Actualización","Se actualizaron los datos de la Subcategoría: laptop"),
("199","1","2026-03-09","08:26:05 pm","Categorías","Actualización","Se actualizaron los datos de la Categoría: computadorasxsssss"),
("200","1","2026-03-09","08:26:25 pm","Categorías","Actualización","Se actualizaron los datos de la Categoría: computadorasssss");
INSERT INTO bitacora VALUES
("201","1","2026-03-09","08:26:57 pm","Categorías","Actualización","Se actualizaron los datos de la Categoría: computadoras"),
("202","1","2026-03-09","08:27:06 pm","Categorías","Actualización","Se actualizaron los datos de la Subcategoría: lapto"),
("203","1","2026-03-09","08:31:08 pm","Categorías","Actualización","Se actualizaron los datos de la Subcategoría: laptop"),
("204","1","2026-03-09","08:31:27 pm","Categorías","Actualización","Se actualizaron los datos de la Categoría: computadorassss"),
("205","1","2026-03-09","08:31:37 pm","Categorías","Actualización","Se actualizaron los datos de la Categoría: computadoras"),
("206","1","2026-03-09","09:47:21 pm","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("207","1","2026-03-09","09:54:27 pm","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("208","1","2026-03-10","12:14:24 pm","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("209","1","2026-03-10","12:28:32 pm","Productos","Registro","Se registró el producto: Laptop Gamer MSI Katana 15 HX B14WGK-016US | Intel Core i9 14va Gen (Cód: 2345235623456)"),
("210","1","2026-03-10","12:29:38 pm","Productos","Registro","Se registró el producto: Laptop Gamer MSI Katana 15 HX B14WGK-293US | Intel Core i7 14va Gen (Cód: 1253467598756)"),
("211","1","2026-03-10","12:33:01 pm","Productos","Registro","Se registró el producto: Laptop Gamer Asus ROG Strix G17 G713 | AMD Ryzen 9 7945HX (Cód: 3480953096707)"),
("212","1","2026-03-10","12:33:52 pm","Productos","Registro","Se registró el producto: Laptop Gamer Asus ROG Strix G16 G614 | Intel Core i9 14va Gen (Cód: 5038947598273)"),
("213","1","2026-03-10","02:34:43 pm","Seguridad","Inicio de Sesión","El usuario Administrador entró al sistema."),
("214","1","2026-03-10","02:38:08 pm","Productos","Eliminación","Se eliminó el producto: ALEX"),
("215","1","2026-03-10","02:38:12 pm","Productos","Eliminación","Se eliminó el producto: asasas"),
("216","1","2026-03-10","02:38:14 pm","Productos","Eliminación","Se eliminó el producto: cacaaaa"),
("217","1","2026-03-10","02:38:21 pm","Ventas","Anulación","Se anuló la venta con código: J8G3H0U2R2-4"),
("218","1","2026-03-10","02:38:23 pm","Ventas","Anulación","Se anuló la venta con código: O9Q1E5T9X0-3"),
("219","1","2026-03-10","02:38:25 pm","Ventas","Anulación","Se anuló la venta con código: T4Q6F5E0S4-2"),
("220","1","2026-03-10","02:38:27 pm","Ventas","Anulación","Se anuló la venta con código: T7X7Y6X4U2-4"),
("221","1","2026-03-10","02:40:48 pm","Proveedores","Eliminación","Se eliminó el proveedor: xddd"),
("222","1","2026-03-10","02:40:51 pm","Proveedores","Eliminación","Se eliminó el proveedor: SSDSD"),
("223","1","2026-03-10","02:40:53 pm","Proveedores","Eliminación","Se eliminó el proveedor: Polar"),
("224","1","2026-03-10","02:40:55 pm","Proveedores","Eliminación","Se eliminó el proveedor: ariel"),
("225","1","2026-03-10","02:40:57 pm","Proveedores","Eliminación","Se eliminó el proveedor: Alive"),
("226","1","2026-03-10","02:41:04 pm","Clientes","Eliminación","Se eliminó el cliente: Gabruek zapata"),
("227","1","2026-03-10","02:41:10 pm","Clientes","Eliminación","Se eliminó el cliente: Alexander Cadena"),
("228","1","2026-03-10","02:41:31 pm","Productos","Eliminación","Se eliminó el producto: Ryzen"),
("229","1","2026-03-10","02:41:33 pm","Productos","Eliminación","Se eliminó el producto: Laptop Gamer MSI Katana 15 HX B14WGK-293US | Intel Core i7 14va Gen"),
("230","1","2026-03-10","02:41:35 pm","Productos","Eliminación","Se eliminó el producto: Laptop Gamer MSI Katana 15 HX B14WGK-016US | Intel Core i9 14va Gen"),
("231","1","2026-03-10","02:41:39 pm","Productos","Eliminación","Se eliminó el producto: Laptop Gamer Asus ROG Strix G17 G713 | AMD Ryzen 9 7945HX"),
("232","1","2026-03-10","02:41:41 pm","Productos","Eliminación","Se eliminó el producto: Laptop Gamer Asus ROG Strix G16 G614 | Intel Core i9 14va Gen"),
("233","1","2026-03-10","02:41:43 pm","Productos","Eliminación","Se eliminó el producto: Laptop"),
("234","1","2026-03-10","02:41:47 pm","Categorías","Eliminación","Se eliminó la categoría: computadoras");




CREATE TABLE `caja` (
  `caja_id` int(5) NOT NULL AUTO_INCREMENT,
  `caja_numero` int(5) NOT NULL,
  `caja_nombre` varchar(100) NOT NULL,
  `caja_efectivo` decimal(30,2) NOT NULL,
  PRIMARY KEY (`caja_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;


INSERT INTO caja VALUES
("1","1","Caja Principal","56.00");




CREATE TABLE `categoria` (
  `categoria_id` int(7) NOT NULL AUTO_INCREMENT,
  `categoria_nombre` varchar(50) NOT NULL,
  `categoria_padre_id` int(11) DEFAULT NULL,
  `categoria_ubicacion` varchar(150) NOT NULL,
  PRIMARY KEY (`categoria_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;


INSERT INTO categoria VALUES
("11","laptop","1","");




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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;


INSERT INTO cliente VALUES
("1","Otro","N/A","Publico","General","N/A","N/A","N/A","N/A","N/A");




CREATE TABLE `compra` (
  `compra_id` int(11) NOT NULL AUTO_INCREMENT,
  `compra_codigo` varchar(50) NOT NULL,
  `compra_fecha` date NOT NULL,
  `compra_total` decimal(30,2) NOT NULL,
  `compra_tasa_bcv` decimal(20,2) NOT NULL DEFAULT 0.00,
  `usuario_id` int(10) NOT NULL,
  `proveedor_id` int(10) NOT NULL,
  PRIMARY KEY (`compra_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;






CREATE TABLE `compra_detalle` (
  `compra_detalle_id` int(11) NOT NULL AUTO_INCREMENT,
  `compra_codigo` varchar(50) NOT NULL,
  `producto_id` int(10) NOT NULL,
  `compra_detalle_cantidad` int(10) NOT NULL,
  `compra_detalle_precio` decimal(30,2) NOT NULL,
  PRIMARY KEY (`compra_detalle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;






CREATE TABLE `empresa` (
  `empresa_id` int(11) NOT NULL AUTO_INCREMENT,
  `empresa_nombre` varchar(90) NOT NULL,
  `empresa_rif` varchar(40) NOT NULL,
  `empresa_telefono` varchar(20) NOT NULL,
  `empresa_email` varchar(50) NOT NULL,
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
  PRIMARY KEY (`producto_id`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`categoria_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;






CREATE TABLE `proveedor` (
  `proveedor_id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_nombre` varchar(100) NOT NULL,
  `proveedor_rif` varchar(30) NOT NULL,
  `proveedor_telefono` varchar(20) DEFAULT NULL,
  `proveedor_direccion` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`proveedor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;






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
  CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`rol_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;


INSERT INTO usuario VALUES
("1","Administrador","Principal","Administrador@gmail.com","Administrador","$2y$10$Jgm6xFb5Onz/BMdIkNK2Tur8yg/NYEMb/tdnhoV7kB1BwIG4R05D2","","1","1","Activo"),
("8","Alexander","Cadena","alexcadena19829@gmail.com","Alex123","$2y$10$zVEh7eKFBAvoz.c6if7yy.hzucvXjJtvaBl0tpE4sYj.BwqQHM28u","","1","2","Activo"),
("11","Administador","segundo","Administrador2@gmail.com","admin","$2y$10$BfVYrotl0kpKx/DzrtkGw.0pBcT0NddKdukpp3kfeizY8xBpy7WEi","","1","1","Activo");




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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;






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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;




