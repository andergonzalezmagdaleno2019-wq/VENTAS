-- --------------------------------------------------------
-- Respaldo del Sistema de Ventas
-- Fecha de generación: 2026-04-03 19:23:31
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
) ENGINE=InnoDB AUTO_INCREMENT=265 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

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
("30", "1", "2026-03-16", "09:13:30 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("31", "1", "2026-03-16", "09:13:40 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("32", "1", "2026-03-16", "09:14:48 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("33", "1", "2026-03-16", "09:15:14 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("34", "1", "2026-03-16", "09:15:33 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("35", "1", "2026-03-16", "09:41:01 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("36", "1", "2026-03-16", "09:41:06 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("37", "1", "2026-03-16", "11:16:30 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("38", "1", "2026-03-16", "11:16:37 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("39", "1", "2026-03-16", "11:24:44 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("40", "1", "2026-03-16", "11:25:28 pm", "Sistema", "Backup", "Se generó una copia de seguridad: backup_ventas_2026-03-16_23-25-28.sql"),
("41", "1", "2026-03-16", "11:27:18 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("42", "1", "2026-03-17", "08:51:07 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("43", "1", "2026-03-17", "08:52:00 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("44", "1", "2026-03-17", "08:52:11 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("45", "1", "2026-03-17", "09:01:02 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("46", "1", "2026-03-17", "09:25:44 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("47", "1", "2026-03-17", "09:31:12 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("48", "1", "2026-03-17", "09:48:34 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("49", "1", "2026-03-17", "11:56:14 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("50", "1", "2026-03-18", "09:47:09 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("51", "1", "2026-03-18", "09:47:57 pm", "Categorías", "Registro", "Se registró la categoría: Computación"),
("52", "1", "2026-03-18", "09:48:16 pm", "Categorías", "Registro", "Se registró la categoría: Laptops"),
("53", "1", "2026-03-18", "09:49:09 pm", "Productos", "Registro", "Se registró el producto: Laptop Acer Aspire Go 15 | Intel Core i5 13va Gen (Inicia con stock 0)"),
("54", "1", "2026-03-18", "10:34:46 pm", "Proveedores", "Actualización", "Se actualizaron datos del proveedor: Conputodo"),
("55", "1", "2026-03-18", "10:55:17 pm", "Proveedores", "Actualización", "Se actualizaron datos del proveedor: Conputodo"),
("56", "1", "2026-03-18", "10:56:37 pm", "Proveedores", "Actualización", "Se actualizaron datos del proveedor: Conputodo"),
("57", "1", "2026-03-18", "11:15:43 pm", "Proveedores", "Actualización", "Se actualizaron datos del proveedor: Conputodo"),
("58", "1", "2026-03-18", "11:20:09 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("59", "1", "2026-03-18", "11:23:16 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("60", "1", "2026-03-18", "11:24:45 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("61", "1", "2026-03-18", "11:25:58 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("62", "1", "2026-03-18", "11:27:52 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("63", "1", "2026-03-18", "11:29:41 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("64", "1", "2026-03-18", "11:30:04 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("65", "6", "2026-03-18", "11:30:16 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("66", "6", "2026-03-18", "11:41:38 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("67", "6", "2026-03-18", "11:41:49 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("68", "6", "2026-03-18", "11:41:53 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("69", "6", "2026-03-18", "11:43:05 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("70", "6", "2026-03-18", "11:43:05 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("71", "6", "2026-03-18", "11:56:47 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("72", "1", "2026-03-18", "11:57:09 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("73", "1", "2026-03-18", "11:57:10 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("74", "1", "2026-03-18", "11:57:30 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("75", "6", "2026-03-18", "11:57:41 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("76", "6", "2026-03-18", "11:57:41 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("77", "6", "2026-03-19", "12:08:03 am", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("78", "1", "2026-03-19", "12:08:28 am", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("79", "1", "2026-03-19", "12:08:28 am", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("80", "1", "2026-03-19", "12:08:59 am", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("81", "6", "2026-03-19", "12:09:11 am", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("82", "6", "2026-03-19", "12:09:11 am", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("83", "6", "2026-03-19", "12:12:49 am", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("84", "6", "2026-03-19", "12:14:00 am", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("85", "6", "2026-03-19", "12:14:00 am", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("86", "6", "2026-03-19", "12:17:57 am", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("87", "1", "2026-03-19", "12:18:09 am", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("88", "1", "2026-03-19", "12:18:09 am", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("89", "1", "2026-03-19", "12:18:22 am", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("90", "1", "2026-03-19", "12:19:27 am", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("91", "1", "2026-03-19", "12:19:27 am", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("92", "1", "2026-03-19", "12:20:54 am", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("93", "6", "2026-03-19", "12:21:03 am", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("94", "6", "2026-03-19", "12:21:03 am", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("95", "6", "2026-03-19", "12:23:39 am", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("96", "1", "2026-03-19", "12:26:31 am", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("97", "1", "2026-03-19", "12:26:31 am", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("98", "1", "2026-03-19", "08:46:15 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("99", "1", "2026-03-19", "08:46:15 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("100", "1", "2026-03-19", "09:09:16 pm", "Productos", "Registro", "Se registró el producto: Laptop Lenovo IdeaPad 3 17IAU7 | Intel Core I3 12va Gen (Inicia con stock 0)"),
("101", "1", "2026-03-19", "09:10:01 pm", "Productos", "Registro", "Se registró el producto: Laptop Lenovo IdeaPad 1 15AMN7 Touch | AMD Ryzen 5 7520U (Inicia con stock 0)"),
("102", "1", "2026-03-19", "09:11:31 pm", "Productos", "Registro", "Se registró el producto: Laptop Lenovo IdeaPad 1 15AMN7 | AMD Ryzen 3 7320U (Inicia con stock 0)"),
("103", "1", "2026-03-19", "09:12:43 pm", "Productos", "Registro", "Se registró el producto: Laptop Dell Latitude 5490| Intel Core i5 8va Gen (Inicia con stock 0)"),
("104", "1", "2026-03-19", "09:13:27 pm", "Categorías", "Registro", "Se registró la categoría: All in one"),
("105", "1", "2026-03-19", "09:14:12 pm", "Productos", "Registro", "Se registró el producto: Tiny-In-One Lenovo ThinkCentre M910Q 21.5\" | Intel Core i5 6ta Gen (Inicia con stock 0)");
INSERT INTO `bitacora` VALUES
("106", "1", "2026-03-19", "09:14:54 pm", "Productos", "Registro", "Se registró el producto: All In One HP EliteOne 800 G2 24\" | Intel Core i5 6ta Gen (Inicia con stock 0)"),
("107", "1", "2026-03-19", "09:20:39 pm", "Categorías", "Registro", "Se registró la categoría: Monitores"),
("108", "1", "2026-03-19", "09:22:19 pm", "Productos", "Registro", "Se registró el producto: Monitor Lenovo L25e-40 de 24.5\" FHD Panel VA 75Hz (Inicia con stock 0)"),
("109", "1", "2026-03-19", "09:22:50 pm", "Productos", "Registro", "Se registró el producto: Monitor MSI PRO MP273AW 27\" Full HD Panel IPS 100Hz (Inicia con stock 0)"),
("110", "1", "2026-03-19", "09:24:22 pm", "Categorías", "Registro", "Se registró la categoría: Pc de escritorio"),
("111", "1", "2026-03-19", "09:26:40 pm", "Productos", "Registro", "Se registró el producto: PC Gamer Cooler Master | AMD Ryzen 7 5700 + AMD RX 7600 XT 16GB (Inicia con stock 0)"),
("112", "1", "2026-03-19", "09:27:55 pm", "Productos", "Registro", "Se registró el producto: PC Gamer XPG Starker Air | Intel Core i7 12va Gen RTX 4060 8GB (Inicia con stock 0)"),
("113", "1", "2026-03-19", "09:31:18 pm", "Categorías", "Registro", "Se registró la categoría: Impresión y oficina"),
("114", "1", "2026-03-19", "09:31:36 pm", "Categorías", "Registro", "Se registró la categoría: Consumibles"),
("115", "1", "2026-03-19", "09:33:00 pm", "Productos", "Registro", "Se registró el producto: Pasta Térmica Gamemax TG3 de Alto Rendimiento (Inicia con stock 0)"),
("116", "1", "2026-03-19", "09:36:53 pm", "Productos", "Registro", "Se registró el producto: Tinta Original HP GT52/GT53 Colores (Cyan, Magenta, Amarillo, Negro) para Ink Tank y Smart Tank (Inicia con stock 0)"),
("117", "1", "2026-03-19", "09:37:23 pm", "Categorías", "Registro", "Se registró la categoría: Impresoras"),
("118", "1", "2026-03-19", "09:38:53 pm", "Productos", "Registro", "Se registró el producto: Impresora Multifuncional Epson EcoTank L4260 con Dúplex Automático y Wi-Fi (Inicia con stock 0)"),
("119", "1", "2026-03-19", "09:39:55 pm", "Productos", "Registro", "Se registró el producto: Impresora Multifuncional Canon PIXMA G3180 MegaTank con Wi-Fi (Inicia con stock 0)"),
("120", "1", "2026-03-19", "09:40:45 pm", "Categorías", "Registro", "Se registró la categoría: Componentes de pC"),
("121", "1", "2026-03-19", "09:41:14 pm", "Categorías", "Registro", "Se registró la categoría: Fan cooler"),
("122", "1", "2026-03-19", "09:41:54 pm", "Productos", "Registro", "Se registró el producto: Kit Fan Gamemax RQ300 ARGB 120mm 3-en-1 con Control Remoto (Inicia con stock 0)"),
("123", "1", "2026-03-19", "09:42:54 pm", "Productos", "Registro", "Se registró el producto: Fan Gamemax FN-12Rainbow-Q-Infinity ARGB 120mm (Inicia con stock 0)"),
("124", "1", "2026-03-19", "09:44:03 pm", "Productos", "Registro", "Se registró el producto: Fan Kit Cooler Master MasterFan MF120 Halo 3 en 1 RGB (Inicia con stock 0)"),
("125", "1", "2026-03-19", "09:44:40 pm", "Categorías", "Registro", "Se registró la categoría: Refrigeración"),
("126", "1", "2026-03-19", "09:45:12 pm", "Categorías", "Actualización", "Se actualizaron los datos de la Categoría: Componentes de pC"),
("127", "1", "2026-03-19", "09:46:04 pm", "Productos", "Registro", "Se registró el producto: Disipador Original Intel Laminar RM1 Socket LGA 1700 | Cooler de Stock (Inicia con stock 0)"),
("128", "1", "2026-03-19", "09:46:52 pm", "Productos", "Registro", "Se registró el producto: Disipador de Torre Jemip Basic JP-CP1 (Inicia con stock 0)"),
("129", "1", "2026-03-19", "09:47:29 pm", "Categorías", "Registro", "Se registró la categoría: Redes y conectividad"),
("130", "1", "2026-03-19", "09:47:50 pm", "Categorías", "Registro", "Se registró la categoría: Adaptadores wiFi"),
("131", "1", "2026-03-19", "09:48:35 pm", "Productos", "Registro", "Se registró el producto: Adaptador USB WIFI y Bluetooth TP-Link Archer T2UB Nano AC600 Doble Banda (Inicia con stock 0)"),
("132", "1", "2026-03-19", "09:49:41 pm", "Productos", "Registro", "Se registró el producto: Adaptador WIFI USB 2.4GHz Imexx (Inicia con stock 0)"),
("133", "1", "2026-03-19", "09:50:09 pm", "Categorías", "Registro", "Se registró la categoría: Routers"),
("134", "1", "2026-03-19", "09:50:51 pm", "Productos", "Registro", "Se registró el producto: Router Wi-Fi 6 TP-Link EX222 AX1800 Gigabit Doble Banda (Inicia con stock 0)"),
("135", "1", "2026-03-19", "09:51:37 pm", "Productos", "Registro", "Se registró el producto: Router Inalámbrico Mercusys MW330HP 300 Mbps Alta Potencia Rompemuros (Inicia con stock 0)"),
("136", "1", "2026-03-19", "09:56:15 pm", "Categorías", "Registro", "Se registró la categoría: Periféricos"),
("137", "1", "2026-03-19", "09:57:16 pm", "Categorías", "Registro", "Se registró la categoría: Gamepads"),
("138", "1", "2026-03-19", "09:58:02 pm", "Productos", "Registro", "Se registró el producto: Control Gamer Redragon Ceres G812 Inalámbrico para Android y PC (Inicia con stock 0)"),
("139", "1", "2026-03-19", "09:58:42 pm", "Productos", "Registro", "Se registró el producto: Control Marvo GT-903 Onaga 30 Inalámbrico para PS4, PC y Android (Inicia con stock 0)"),
("140", "1", "2026-03-23", "06:28:34 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("141", "1", "2026-03-23", "06:28:34 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("142", "1", "2026-03-23", "06:30:07 pm", "Productos", "Registro", "Se registró el producto: Ajksghkajf (Inicia con stock 0)"),
("143", "1", "2026-03-23", "06:30:44 pm", "Productos", "Eliminación", "Se eliminó el producto: Ajksghkajf"),
("144", "1", "2026-03-23", "06:33:41 pm", "Productos", "Registro", "Se registró el producto: Algo xs (Inicia con stock 0)"),
("145", "1", "2026-03-23", "06:34:05 pm", "Productos", "Eliminación", "Se eliminó el producto: Algo xs"),
("146", "1", "2026-03-23", "10:21:16 pm", "Productos", "Actualización", "Datos actualizados del producto: Disipador de Torre Jemip Basic JP-CP1"),
("147", "1", "2026-03-23", "10:29:24 pm", "Productos", "Actualización", "Datos actualizados del producto: Disipador de Torre Jemip Basic JP-CP1"),
("148", "1", "2026-03-24", "12:08:44 am", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("149", "1", "2026-03-24", "12:08:44 am", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("150", "1", "2026-03-24", "12:08:53 am", "Sistema", "Backup", "Se generó una copia de seguridad: backup_ventas_2026-03-24_00-08-53.sql"),
("151", "1", "2026-03-24", "12:13:21 am", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("152", "1", "2026-03-24", "07:52:19 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("153", "1", "2026-03-24", "08:07:14 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("154", "7", "2026-03-24", "08:07:28 pm", "Seguridad", "Inicio de Sesión", "El usuario Anflizzz entró al sistema."),
("155", "7", "2026-03-24", "08:09:09 pm", "Seguridad", "Cierre de Sesión", "El usuario Anflizzz salió del sistema."),
("156", "1", "2026-03-24", "08:09:21 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("157", "1", "2026-03-24", "08:26:47 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("158", "7", "2026-03-24", "08:27:01 pm", "Seguridad", "Inicio de Sesión", "El usuario Anflizzz entró al sistema."),
("159", "7", "2026-03-24", "08:28:31 pm", "Seguridad", "Cierre de Sesión", "El usuario Anflizzz salió del sistema."),
("160", "1", "2026-03-24", "08:28:45 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("161", "1", "2026-03-24", "08:59:06 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("162", "7", "2026-03-24", "08:59:21 pm", "Seguridad", "Inicio de Sesión", "El usuario Anflizzz entró al sistema."),
("163", "7", "2026-03-24", "09:18:36 pm", "Seguridad", "Cierre de Sesión", "El usuario Anflizzz salió del sistema."),
("164", "1", "2026-03-24", "09:18:47 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("165", "1", "2026-03-24", "09:19:00 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("166", "1", "2026-03-24", "09:24:23 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("167", "1", "2026-03-25", "10:05:56 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("168", "1", "2026-03-25", "10:46:24 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("169", "1", "2026-03-25", "11:35:41 pm", "Sistema", "Backup", "Se generó una copia de seguridad: backup_ventas_2026-03-25_23-35-41.sql"),
("170", "1", "2026-03-25", "11:43:46 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("171", "1", "2026-03-26", "09:51:48 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("172", "1", "2026-03-28", "08:19:37 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("173", "1", "2026-03-28", "08:34:10 pm", "Clientes", "Registro", "Se registró el cliente: Josue Galindez"),
("174", "1", "2026-03-29", "05:02:12 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("175", "1", "2026-03-29", "05:50:00 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("176", "1", "2026-03-29", "05:50:19 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("177", "1", "2026-03-31", "10:58:39 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("178", "1", "2026-03-31", "10:59:33 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("179", "8", "2026-03-31", "10:59:44 pm", "Seguridad", "Inicio de Sesión", "El usuario Adflicc entró al sistema."),
("180", "8", "2026-03-31", "11:00:29 pm", "Seguridad", "Cierre de Sesión", "El usuario Adflicc salió del sistema."),
("181", "8", "2026-03-31", "11:00:37 pm", "Seguridad", "Inicio de Sesión", "El usuario Adflicc entró al sistema."),
("182", "8", "2026-03-31", "11:01:56 pm", "Seguridad", "Cierre de Sesión", "El usuario Adflicc salió del sistema."),
("183", "1", "2026-03-31", "11:06:50 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("184", "1", "2026-03-31", "11:11:59 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("185", "8", "2026-03-31", "11:12:15 pm", "Seguridad", "Inicio de Sesión", "El usuario Adflicc entró al sistema."),
("186", "8", "2026-03-31", "11:21:58 pm", "Seguridad", "Cierre de Sesión", "El usuario Adflicc salió del sistema."),
("187", "1", "2026-03-31", "11:25:00 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("188", "1", "2026-03-31", "11:26:18 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("189", "1", "2026-03-31", "11:27:14 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("190", "1", "2026-03-31", "11:28:34 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("191", "1", "2026-03-31", "11:28:41 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("192", "1", "2026-03-31", "11:28:46 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("193", "1", "2026-03-31", "11:29:20 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("194", "1", "2026-03-31", "11:37:52 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("195", "1", "2026-03-31", "11:38:06 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("196", "1", "2026-04-01", "06:51:04 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("197", "1", "2026-04-01", "06:57:39 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("198", "1", "2026-04-01", "06:58:25 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("199", "1", "2026-04-01", "07:06:10 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("200", "1", "2026-04-01", "07:09:28 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("201", "1", "2026-04-01", "07:13:09 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("202", "1", "2026-04-01", "07:29:50 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("203", "1", "2026-04-01", "07:35:47 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("204", "14", "2026-04-01", "07:46:33 pm", "Seguridad", "Inicio de Sesión", "El usuario Adner entró al sistema."),
("205", "14", "2026-04-01", "07:46:56 pm", "Seguridad", "Cierre de Sesión", "El usuario Adner salió del sistema.");
INSERT INTO `bitacora` VALUES
("206", "1", "2026-04-01", "07:50:38 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("207", "1", "2026-04-01", "07:56:30 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("208", "14", "2026-04-01", "07:59:22 pm", "Seguridad", "Inicio de Sesión", "El usuario Adner entró al sistema."),
("209", "14", "2026-04-01", "08:04:03 pm", "Seguridad", "Cierre de Sesión", "El usuario Adner salió del sistema."),
("210", "14", "2026-04-01", "08:04:11 pm", "Seguridad", "Inicio de Sesión", "El usuario Adner entró al sistema."),
("211", "14", "2026-04-01", "08:04:27 pm", "Seguridad", "Cierre de Sesión", "El usuario Adner salió del sistema."),
("212", "14", "2026-04-01", "08:05:02 pm", "Seguridad", "Inicio de Sesión", "El usuario Adner entró al sistema."),
("213", "14", "2026-04-01", "08:05:10 pm", "Seguridad", "Cierre de Sesión", "El usuario Adner salió del sistema."),
("214", "1", "2026-04-01", "08:11:02 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("215", "1", "2026-04-01", "08:11:23 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("216", "1", "2026-04-01", "08:16:40 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("217", "1", "2026-04-01", "08:16:45 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("218", "1", "2026-04-01", "08:24:59 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("219", "1", "2026-04-01", "08:25:08 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("220", "14", "2026-04-01", "08:26:30 pm", "Seguridad", "Inicio de Sesión", "El usuario Adner entró al sistema."),
("221", "14", "2026-04-01", "08:26:51 pm", "Seguridad", "Cierre de Sesión", "El usuario Adner salió del sistema."),
("222", "1", "2026-04-01", "08:32:51 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("223", "1", "2026-04-01", "08:35:07 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("224", "1", "2026-04-01", "08:44:28 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("225", "1", "2026-04-01", "08:44:33 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("226", "14", "2026-04-01", "08:45:22 pm", "Seguridad", "Inicio de Sesión", "El usuario Adner entró al sistema."),
("227", "14", "2026-04-01", "08:45:29 pm", "Seguridad", "Cierre de Sesión", "El usuario Adner salió del sistema."),
("228", "14", "2026-04-01", "08:49:07 pm", "Seguridad", "Inicio de Sesión", "El usuario Adner entró al sistema."),
("229", "14", "2026-04-01", "08:49:10 pm", "Seguridad", "Cierre de Sesión", "El usuario Adner salió del sistema."),
("230", "1", "2026-04-03", "04:32:34 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("231", "1", "2026-04-03", "05:12:39 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("232", "1", "2026-04-03", "05:24:03 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("233", "1", "2026-04-03", "05:24:23 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("234", "14", "2026-04-03", "05:24:41 pm", "Seguridad", "Inicio de Sesión", "El usuario Adner entró al sistema."),
("235", "14", "2026-04-03", "05:24:55 pm", "Seguridad", "Cierre de Sesión", "El usuario Adner salió del sistema."),
("236", "14", "2026-04-03", "05:25:27 pm", "Seguridad", "Inicio de Sesión", "El usuario Adner entró al sistema."),
("237", "14", "2026-04-03", "05:25:30 pm", "Seguridad", "Cierre de Sesión", "El usuario Adner salió del sistema."),
("238", "1", "2026-04-03", "05:27:53 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("239", "1", "2026-04-03", "05:28:04 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("240", "14", "2026-04-03", "05:28:13 pm", "Seguridad", "Inicio de Sesión", "El usuario Adner entró al sistema."),
("241", "14", "2026-04-03", "05:28:18 pm", "Seguridad", "Cierre de Sesión", "El usuario Adner salió del sistema."),
("242", "14", "2026-04-03", "05:47:03 pm", "Seguridad", "Inicio de Sesión", "El usuario Adner entró al sistema."),
("243", "14", "2026-04-03", "05:47:06 pm", "Seguridad", "Cierre de Sesión", "El usuario Adner salió del sistema."),
("244", "1", "2026-04-03", "05:57:15 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("245", "1", "2026-04-03", "05:57:55 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("246", "15", "2026-04-03", "05:58:06 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("247", "15", "2026-04-03", "06:05:51 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("248", "15", "2026-04-03", "06:06:04 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("249", "15", "2026-04-03", "06:06:11 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("250", "1", "2026-04-03", "06:06:22 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("251", "1", "2026-04-03", "06:06:34 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("252", "15", "2026-04-03", "06:06:45 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("253", "15", "2026-04-03", "06:27:22 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("254", "1", "2026-04-03", "06:27:28 pm", "Seguridad", "Inicio de Sesión", "El usuario Lenriquez entró al sistema."),
("255", "1", "2026-04-03", "06:28:20 pm", "Seguridad", "Cierre de Sesión", "El usuario Lenriquez salió del sistema."),
("256", "1", "2026-04-03", "06:28:43 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("257", "1", "2026-04-03", "06:29:03 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("258", "18", "2026-04-03", "06:29:09 pm", "Seguridad", "Inicio de Sesión", "El usuario Mari entró al sistema."),
("259", "18", "2026-04-03", "06:32:33 pm", "Seguridad", "Cierre de Sesión", "El usuario Mari salió del sistema."),
("260", "1", "2026-04-03", "06:32:42 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema."),
("261", "1", "2026-04-03", "06:41:04 pm", "Seguridad", "Cierre de Sesión", "El usuario Administrador salió del sistema."),
("262", "15", "2026-04-03", "06:41:12 pm", "Seguridad", "Inicio de Sesión", "El usuario Andflizzz entró al sistema."),
("263", "15", "2026-04-03", "06:50:46 pm", "Seguridad", "Cierre de Sesión", "El usuario Andflizzz salió del sistema."),
("264", "1", "2026-04-03", "06:50:57 pm", "Seguridad", "Inicio de Sesión", "El usuario Administrador entró al sistema.");


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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `categoria`
INSERT INTO `categoria` VALUES
("1", "Computación", NULL, "", "Unidad"),
("2", "Laptops", "1", "", "Unidad"),
("3", "All in one", "1", "", "Unidad"),
("4", "Monitores", "1", "", "Unidad"),
("5", "Pc de escritorio", "1", "", "Unidad"),
("6", "Impresión y oficina", NULL, "", "Unidad,Litro"),
("7", "Consumibles", "6", "", "Unidad,Litro"),
("8", "Impresoras", "6", "", "Unidad,Litro"),
("9", "Componentes de pC", NULL, "", "Unidad"),
("10", "Fan cooler", "9", "", "Unidad"),
("11", "Refrigeración", "9", "", "Unidad"),
("12", "Redes y conectividad", NULL, "", "Unidad"),
("13", "Adaptadores wiFi", "12", "", "Unidad"),
("14", "Routers", "12", "", "Unidad"),
("15", "Periféricos", NULL, "", "Unidad"),
("16", "Gamepads", "15", "", "Unidad");


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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `cliente`
INSERT INTO `cliente` VALUES
("1", "Otro", "N/A", "Publico", "General", "N/A", "N/A", "N/A", "N/A", "N/A"),
("2", "V", "14231212", "Josue", "Galindez", "Zulia", "Maracaibo", "NOSE", "04121231232", "JOS21@GMAIL.COM");


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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `compra`
INSERT INTO `compra` VALUES
("1", "COM-000001", "2026-03-18", "5200.00", "451.51", "1", "1", "Completado", "", "0.00", "Pagado", "2026-03-18", "Crédito", "1", "0", "0"),
("2", "COM-000002", "2026-03-19", "22740.00", "451.51", "1", "1", "Completado", "", "0.00", "Pagado", "2026-03-19", "Crédito", "1", "0", "0"),
("3", "COM-000003", "2026-03-19", "4887.00", "451.51", "1", "1", "Facturada", "", "4887.00", "Pendiente", "2026-03-19", "Crédito", "1", "0", "0"),
("4", "COM-000004", "2026-03-19", "1316.00", "451.51", "1", "1", "Facturada", "", "1316.00", "Pendiente", "2026-03-31", "Crédito", "1", "0", "0"),
("5", "COM-000005", "2026-03-19", "1224.00", "451.51", "1", "1", "Facturada", "", "1224.00", "Pendiente", "2026-03-19", "Crédito", "1", "0", "0"),
("6", "COM-000006", "2026-03-19", "13495.00", "451.51", "1", "1", "Completado", " | [Factura Oficial Nro: Fac-of005 ingresada el 2026-03-24]", "0.00", "Pagado", "2026-03-24", "Crédito", "1", "0", "0"),
("7", "COM-000007", "2026-03-23", "50.00", "457.08", "1", "1", "Completado", "", "0.00", "Pagado", "2026-03-24", "Contado", "1", "0", "0"),
("8", "COM-000008", "2026-03-23", "0.00", "457.08", "1", "1", "Anulada", " | [ANULADA]: pq si\r\nyyaaaaaaaaaaaaaaaaaaaa", "0.00", "Pendiente", "2026-03-23", "Crédito", "1", "0", "0"),
("9", "COM-000009", "2026-03-23", "870.00", "457.08", "1", "1", "Facturada", "", "870.00", "Pendiente", "2026-03-23", "Crédito", "1", "0", "0"),
("10", "COM-000010", "2026-03-23", "14.00", "457.08", "1", "1", "Facturada", "", "0.00", "Pagado", "2026-03-23", "Crédito", "1", "0", "0"),
("11", "COM-000011", "2026-03-25", "15.00", "462.67", "1", "2", "Facturada", "", "15.00", "Pendiente", "2026-03-25", "Crédito", "5", "0", "15"),
("12", "COM-000012", "2026-03-25", "90.00", "462.67", "1", "2", "Anulada", " | [ANULADA]: powjerhpoiawjerwrw", "0.00", "Pendiente", "2026-03-25", "Crédito", "1", "0", "0"),
("13", "COM-000013", "2026-03-25", "75.00", "462.67", "1", "2", "Anulada", " | [ANULADA]: erwwwwwwwwwwwwwww", "0.00", "Pendiente", "2026-03-25", "Crédito", "1", "0", "0"),
("14", "COM-000014", "2026-03-25", "75.00", "462.67", "1", "2", "Anulada", " | [ANULADA]: fggggggggggggggg", "0.00", "Pendiente", "2026-03-25", "Crédito", "1", "0", "0"),
("15", "COM-000015", "2026-03-26", "60.00", "462.67", "1", "2", "Anulada", " | [ANULADA]: fgfgfgfgfgfgfgfgfgfgfgfgfgfgfgfg", "0.00", "Pendiente", "2026-03-26", "Crédito", "1", "0", "0"),
("16", "COM-000016", "2026-03-26", "15.00", "466.60", "1", "2", "Anulada", " | [ANULADA]: dddddddddddf", "0.00", "Pendiente", "2026-03-26", "Crédito", "1", "0", "0"),
("17", "COM-000017", "2026-03-26", "20.00", "466.60", "1", "1", "Anulada", " | [ANULADA]: sdffffffffffffff", "0.00", "Pendiente", "2026-03-26", "Crédito", "1", "0", "0"),
("18", "COM-000018", "2026-03-26", "15.00", "466.60", "1", "2", "Anulada", " | [ANULADA]: fsdsdsdsdsdsdsdsdsdsdsdsd | [ANULADA]: sdffffffffffff", "0.00", "Pendiente", "2026-03-26", "Crédito", "1", "0", "0"),
("19", "COM-000019", "2026-03-26", "20.00", "466.60", "1", "1", "Anulada", " | [ANULADA]: werrrrrrrrr", "0.00", "Pendiente", "2026-03-26", "Crédito", "1", "0", "0"),
("20", "COM-000020", "2026-03-26", "20.00", "466.60", "1", "1", "Anulada", " | [ANULADA]: werrrrrewrrrrrrrrrrr", "0.00", "Pendiente", "2026-03-26", "Crédito", "1", "0", "0"),
("21", "COM-000021", "2026-03-26", "15.00", "466.60", "1", "2", "Anulada", " | [ANULADA]: qwerqwewqeqw", "0.00", "Pendiente", "2026-03-26", "Crédito", "1", "0", "0"),
("22", "COM-000022", "2026-03-26", "20.00", "466.60", "1", "1", "Anulada", " | [ANULADA]: eeeeeeeeeeeeer", "0.00", "Pendiente", "2026-03-26", "Crédito", "1", "0", "0"),
("23", "COM-000023", "2026-03-26", "15.00", "466.60", "1", "2", "Completado", "", "0.00", "Pagado", "2026-03-26", "Contado", "0", "0", "0"),
("24", "COM-000024", "2026-03-29", "520.00", "468.51", "1", "1", "Facturada", "", "520.00", "Pendiente", "2026-03-29", "Crédito", "2", "0", "7"),
("25", "COM-000025", "2026-04-03", "20551.66", "473.92", "1", "1", "Completado", "", "0.00", "Pagado", "2026-04-03", "Contado", "0", "0", "0");


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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `compra_cuotas`
INSERT INTO `compra_cuotas` VALUES
("1", "COM-000010", "1", "4.67", "2026-04-07", "Pagado", "Prueba de recepcion", NULL),
("2", "COM-000010", "2", "4.67", "2026-04-22", "Pagado", "Prueba de recepcion", NULL),
("3", "COM-000010", "3", "4.67", "2026-05-07", "Pagado", "Prueba de recepcion", NULL),
("4", "COM-000009", "1", "435.00", "2026-03-30", "Pendiente", "Pq si", NULL),
("5", "COM-000009", "2", "435.00", "2026-04-06", "Pendiente", "Pq si", NULL),
("6", "COM-000009", "1", "435.00", "2026-03-30", "Pendiente", "Edwerwer", NULL),
("7", "COM-000009", "2", "435.00", "2026-04-06", "Pendiente", "Edwerwer", NULL),
("8", "COM-000011", "1", "3.00", "2026-04-09", "Pendiente", "Acuerdo con el proveedor", NULL),
("9", "COM-000011", "2", "3.00", "2026-04-24", "Pendiente", "Acuerdo con el proveedor", NULL),
("10", "COM-000011", "3", "3.00", "2026-05-09", "Pendiente", "Acuerdo con el proveedor", NULL),
("11", "COM-000011", "4", "3.00", "2026-05-24", "Pendiente", "Acuerdo con el proveedor", NULL),
("12", "COM-000011", "5", "3.00", "2026-06-08", "Pendiente", "Acuerdo con el proveedor", NULL),
("13", "COM-000024", "1", "260.00", "2026-04-05", "Pendiente", "EL vendedor lo prefirio asi", NULL),
("14", "COM-000024", "2", "260.00", "2026-04-12", "Pendiente", "EL vendedor lo prefirio asi", NULL);


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
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `compra_detalle`
INSERT INTO `compra_detalle` VALUES
("1", "1", "1", "10", "520.00"),
("2", "2", "5", "10", "600.00"),
("3", "2", "4", "12", "520.00"),
("4", "2", "3", "14", "750.00"),
("5", "3", "2", "8", "590.00"),
("6", "3", "12", "5", "10.00"),
("7", "3", "13", "9", "13.00"),
("8", "4", "17", "34", "14.00"),
("9", "4", "18", "21", "20.00"),
("10", "4", "16", "12", "35.00"),
("11", "5", "21", "8", "20.00"),
("12", "5", "22", "12", "12.00"),
("13", "5", "25", "9", "35.00"),
("14", "5", "26", "11", "55.00"),
("15", "6", "7", "7", "400.00"),
("16", "6", "6", "9", "410.00"),
("17", "6", "8", "12", "100.00"),
("18", "6", "9", "11", "175.00"),
("19", "6", "15", "10", "199.00"),
("20", "6", "14", "9", "210.00"),
("21", "7", "12", "5", "10.00"),
("22", "8", "10", "1", "0.00"),
("23", "9", "10", "1", "870.00"),
("24", "10", "17", "1", "14.00"),
("25", "11", "20", "1", "15.00"),
("26", "12", "20", "6", "15.00"),
("27", "13", "20", "5", "15.00"),
("28", "14", "20", "5", "15.00"),
("29", "15", "20", "4", "15.00"),
("30", "16", "20", "1", "15.00"),
("31", "17", "21", "1", "20.00"),
("32", "18", "20", "1", "15.00"),
("33", "19", "21", "1", "20.00"),
("34", "20", "21", "1", "20.00"),
("35", "21", "20", "1", "15.00"),
("36", "22", "21", "1", "20.00"),
("37", "23", "20", "1", "15.00"),
("38", "24", "1", "1", "520.00"),
("39", "25", "11", "22", "600.99"),
("40", "25", "19", "12", "15.00"),
("41", "25", "23", "12", "39.99"),
("42", "25", "24", "10", "25.00"),
("43", "25", "10", "7", "870.00"),
("44", "25", "20", "22", "15.00");


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
  `pago_metodo` enum('Pago Movil','Transferencia','Anticipo') NOT NULL,
  `pago_referencia` varchar(100) DEFAULT NULL,
  `pago_nota` text DEFAULT NULL,
  PRIMARY KEY (`pago_id`),
  KEY `compra_id` (`compra_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `compra_pagos_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compra` (`compra_id`),
  CONSTRAINT `compra_pagos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `compra_pagos`
INSERT INTO `compra_pagos` VALUES
("1", "1", "1", "2026-03-18", "4000.00", "", "Anticipo Cotización", NULL),
("2", "1", "1", "2026-03-18", "1200.00", "", "Cierre automático por Factura", NULL),
("3", "2", "1", "2026-03-19", "22740.00", "", "Cierre automático por Factura", NULL),
("4", "6", "1", "2026-03-19", "13000.00", "", "Anticipo Cotización", NULL),
("5", "6", "1", "2026-03-19", "495.00", "", "Cierre automático por Nota de Entrega", NULL),
("6", "7", "1", "2026-03-23", "50.00", "", "Anticipo Cotización", NULL),
("7", "10", "1", "2026-03-23", "14.00", "", "675756", NULL),
("8", "12", "1", "2026-03-25", "30.00", "", "Anticipo Cotización", NULL),
("9", "23", "1", "2026-03-26", "15.00", "", "Cierre por Factura", NULL),
("10", "25", "1", "2026-04-03", "20551.66", "", "Anticipo Cotización", NULL);


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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `producto`
INSERT INTO `producto` VALUES
("1", "9823143242134", "Laptop Acer Aspire Go 15 | Intel Core i5 13va Gen", "0", "", "0.00", "0.00", "ACER", "Intel Core i5 13va Gen", "Activo", "Laptop_Acer_Aspire_Go_15__Intel_Core_i5_13va_Gen_49.png", "2", "520.00", "5", "100", "624.00", "9", "Unidad", "1"),
("2", "1234189798237", "Laptop Lenovo IdeaPad 3 17IAU7 | Intel Core I3 12va Gen", "0", "", "0.00", "0.00", "LENOVO", "Intel Core I3 12va Gen", "Activo", "", "2", "590.00", "5", "100", "708.00", "8", "Unidad", "1"),
("3", "1047209857289", "Laptop Lenovo IdeaPad 1 15AMN7 Touch | AMD Ryzen 5 7520U", "0", "", "0.00", "0.00", "LENOVO", "AMD Ryzen 5 7520U", "Activo", "", "2", "750.00", "5", "100", "900.00", "14", "Unidad", "1"),
("4", "9283509723690", "Laptop Lenovo IdeaPad 1 15AMN7 | AMD Ryzen 3 7320U", "0", "", "0.00", "0.00", "LENOVO", "AMD Ryzen 3 7320U", "Activo", "", "2", "520.00", "5", "100", "624.00", "12", "Unidad", "1"),
("5", "1402198409278", "Laptop Dell Latitude 5490| Intel Core i5 8va Gen", "0", "", "0.00", "0.00", "DELL", "Intel Core i5 8va Gen", "Activo", "", "2", "600.00", "5", "100", "720.00", "10", "Unidad", "1"),
("6", "1289742134678", "Tiny-In-One Lenovo ThinkCentre M910Q 21.5\" | Intel Core i5 6ta Gen", "0", "", "0.00", "0.00", "LENOVO", "Intel Core i5 6ta Gen", "Activo", "", "3", "410.00", "5", "100", "492.00", "9", "Unidad", "1"),
("7", "3335427985723", "All In One HP EliteOne 800 G2 24\" | Intel Core i5 6ta Gen", "0", "", "0.00", "0.00", "HP", "Intel Core i5 6ta Gen", "Activo", "", "3", "400.00", "5", "100", "480.00", "7", "Unidad", "1"),
("8", "4892136498236", "Monitor Lenovo L25e-40 de 24.5\" FHD Panel VA 75Hz", "0", "", "0.00", "0.00", "LENOVO", "24.5\"", "Activo", "", "4", "100.00", "5", "100", "120.00", "12", "Unidad", "1"),
("9", "9184721984721", "Monitor MSI PRO MP273AW 27\" Full HD Panel IPS 100Hz", "0", "", "0.00", "0.00", "MSI", "27\"", "Activo", "", "4", "175.00", "5", "100", "210.00", "11", "Unidad", "1"),
("10", "1232141234211", "PC Gamer Cooler Master | AMD Ryzen 7 5700 + AMD RX 7600 XT 16GB", "0", "", "0.00", "0.00", "AMD", "AMD Ryzen 7 5700", "Activo", "", "5", "870.00", "5", "100", "1044.00", "8", "Unidad", "1"),
("11", "0952385098237", "PC Gamer XPG Starker Air | Intel Core i7 12va Gen RTX 4060 8GB", "0", "", "0.00", "0.00", "XPG STARKER AIR", "Intel Core i7 12va Gen", "Activo", "", "5", "600.99", "5", "100", "721.19", "22", "Unidad", "1"),
("12", "8907230542705", "Pasta Térmica Gamemax TG3 de Alto Rendimiento", "0", "", "0.00", "0.00", "GAMEMAX", "TG3", "Activo", "", "7", "10.00", "5", "100", "12.00", "10", "Unidad", "1"),
("13", "2134089723968", "Tinta Original HP GT52/GT53 Colores (Cyan, Magenta, Amarillo, Negro) para Ink Tank y Smart Tank", "0", "", "0.00", "0.00", "HP", "GT52/GT53", "Activo", "", "7", "13.00", "5", "100", "15.60", "9", "Unidad", "1"),
("14", "4023957893958", "Impresora Multifuncional Epson EcoTank L4260 con Dúplex Automático y Wi-Fi", "0", "", "0.00", "0.00", "EPSON", "EcoTank L4260", "Activo", "", "8", "210.00", "5", "100", "252.00", "9", "Unidad", "1"),
("15", "0219480215709", "Impresora Multifuncional Canon PIXMA G3180 MegaTank con Wi-Fi", "0", "", "0.00", "0.00", "CANON", "PIXMA G3180", "Activo", "", "8", "199.00", "5", "100", "238.80", "10", "Unidad", "1"),
("16", "1928471928479", "Kit Fan Gamemax RQ300 ARGB 120mm 3-en-1 con Control Remoto", "0", "", "0.00", "0.00", "GAMEMAX", "RQ300", "Activo", "", "10", "35.00", "5", "100", "42.00", "12", "Unidad", "1"),
("17", "2109348014979", "Fan Gamemax FN-12Rainbow-Q-Infinity ARGB 120mm", "0", "", "0.00", "0.00", "GAMEMAX", "Infinity ARGB", "Activo", "", "10", "14.00", "5", "100", "16.80", "35", "Unidad", "1"),
("18", "1228540973552", "Fan Kit Cooler Master MasterFan MF120 Halo 3 en 1 RGB", "0", "", "0.00", "0.00", "COOLER MASTER", "MF120 HALO", "Activo", "", "10", "20.00", "5", "100", "24.00", "21", "Unidad", "1"),
("19", "0198401927480", "Disipador Original Intel Laminar RM1 Socket LGA 1700 | Cooler de Stock", "0", "", "0.00", "0.00", "INTEL", "Laminar RM1", "Activo", "", "11", "15.00", "5", "100", "18.00", "12", "Unidad", "1"),
("20", "2394802357809", "Disipador de Torre Jemip Basic JP-CP1", "0", "", "0.00", "0.00", "JEMIP", "Basic jP-cP1", "Activo", "", "11", "15.00", "5", "100", "18.00", "26", "Unidad", "1"),
("21", "6567384672371", "Adaptador USB WIFI y Bluetooth TP-Link Archer T2UB Nano AC600 Doble Banda", "0", "", "0.00", "0.00", "TP-LINK", "Archer T2UB Nano (AC600)", "Activo", "", "13", "20.00", "5", "100", "24.00", "8", "Unidad", "1"),
("22", "1234023978403", "Adaptador WIFI USB 2.4GHz Imexx", "0", "", "0.00", "0.00", "IMEXX", "USB 2.4Ghz (150Mbps)", "Activo", "", "13", "12.00", "5", "100", "14.40", "12", "Unidad", "1"),
("23", "2123189417284", "Router Wi-Fi 6 TP-Link EX222 AX1800 Gigabit Doble Banda", "0", "", "0.00", "0.00", "TP-LINK", "EX222 (AX1800)", "Activo", "", "14", "39.99", "5", "100", "47.99", "12", "Unidad", "1"),
("24", "1082103947895", "Router Inalámbrico Mercusys MW330HP 300 Mbps Alta Potencia Rompemuros", "0", "", "0.00", "0.00", "MERCUSYS", "MW330HP", "Activo", "", "14", "25.00", "5", "100", "30.00", "10", "Unidad", "1"),
("25", "0793865985619", "Control Gamer Redragon Ceres G812 Inalámbrico para Android y PC", "0", "", "0.00", "0.00", "REDRAGON", "Ceres G812", "Activo", "", "16", "35.00", "5", "100", "42.00", "9", "Unidad", "1"),
("26", "0921730918648", "Control Marvo GT-903 Onaga 30 Inalámbrico para PS4, PC y Android", "0", "", "0.00", "0.00", "MARVO", "Gt-903", "Activo", "", "16", "55.00", "5", "100", "66.00", "11", "Unidad", "1");


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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `producto_proveedor`
INSERT INTO `producto_proveedor` VALUES
("1", "1", "1", "0.00", NULL),
("2", "2", "1", "0.00", NULL),
("3", "3", "1", "0.00", NULL),
("4", "4", "1", "0.00", NULL),
("5", "5", "1", "0.00", NULL),
("6", "6", "1", "0.00", NULL),
("7", "7", "1", "0.00", NULL),
("8", "8", "1", "0.00", NULL),
("9", "9", "1", "0.00", NULL),
("10", "10", "1", "0.00", NULL),
("11", "11", "1", "0.00", NULL),
("12", "12", "1", "0.00", NULL),
("13", "13", "1", "0.00", NULL),
("14", "14", "1", "0.00", NULL),
("15", "15", "1", "0.00", NULL),
("16", "16", "1", "0.00", NULL),
("17", "17", "1", "0.00", NULL),
("18", "18", "1", "0.00", NULL),
("19", "19", "1", "0.00", NULL),
("21", "21", "1", "0.00", NULL),
("22", "22", "1", "0.00", NULL),
("23", "23", "1", "0.00", NULL),
("24", "24", "1", "0.00", NULL),
("25", "25", "1", "0.00", NULL),
("26", "26", "1", "0.00", NULL),
("32", "20", "1", "0.00", NULL),
("33", "20", "2", "0.00", NULL);


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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `proveedor`
INSERT INTO `proveedor` VALUES
("1", "Conputodo", "V-3434212134", "04223386378", "Maracay"),
("2", "Andys Corporation", "J-2489234213", "04221323124", "Maracay");


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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `recepcion`
INSERT INTO `recepcion` VALUES
("1", "1", "1", "2026-03-18", "[Factura Nro: Fac-001 | Emisión: 2026-03-18 | Pago: Contado] - "),
("2", "2", "1", "2026-03-19", "[Factura Nro: Fac-002 | Emisión: 2026-03-19 | Pago: Contado] - "),
("3", "3", "1", "2026-03-19", "[Factura Nro: Fac-003 | Emisión: 2026-03-19 | Pago: Credito] - "),
("4", "4", "1", "2026-03-19", "[Factura Nro: Fac-004 | Emisión: 2026-03-19 | Pago: Credito] - "),
("5", "5", "1", "2026-03-19", "[Factura Nro: Fac-005 | Emisión: 2026-03-19 | Pago: Consignacion] - "),
("6", "6", "1", "2026-03-19", "[Nota de Entrega Nro: NE-001 | Emisión: 2026-03-19 | Pago: Contado] - "),
("7", "10", "1", "2026-03-23", "[Factura Nro: Fac32 | Emisión: 2026-03-23 | Pago: Credito] - "),
("8", "9", "1", "2026-03-23", "[Factura Nro: 23qwd | Emisión: 2026-03-23 | Pago: Credito] - "),
("9", "9", "1", "2026-03-23", "[Factura Nro: Ert54 | Emisión: 2026-03-23 | Pago: Credito] - "),
("10", "7", "1", "2026-03-24", "[Factura Nro: Weqe-331 | Emisión: 2026-03-24 | Pago: Contado] - "),
("11", "11", "1", "2026-03-25", "[Factura Nro: FACC_origi123 | Emisión: 2026-03-25 | Pago: Credito] - "),
("14", "23", "1", "2026-03-26", "[Factura Nro: 21eqwddsa | Emisión: 2026-03-26 | Pago: Contado] - "),
("15", "24", "1", "2026-03-29", "[Factura Nro: Uisadfiw34234 | Emisión: 2026-03-29 | Pago: Credito] - "),
("16", "25", "1", "2026-04-03", "[Factura Nro: FACX-222 | Emisión: 2026-04-03 | Pago: Contado] - ");


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
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `recepcion_detalle`
INSERT INTO `recepcion_detalle` VALUES
("1", "1", "1", "10"),
("2", "2", "5", "10"),
("3", "2", "4", "12"),
("4", "2", "3", "14"),
("5", "3", "2", "8"),
("6", "3", "12", "5"),
("7", "3", "13", "9"),
("8", "4", "17", "34"),
("9", "4", "18", "21"),
("10", "4", "16", "12"),
("11", "5", "21", "8"),
("12", "5", "22", "12"),
("13", "5", "25", "9"),
("14", "5", "26", "11"),
("15", "6", "7", "7"),
("16", "6", "6", "9"),
("17", "6", "8", "12"),
("18", "6", "9", "11"),
("19", "6", "15", "10"),
("20", "6", "14", "9"),
("21", "7", "17", "1"),
("22", "8", "10", "1"),
("23", "10", "12", "5"),
("24", "11", "20", "1"),
("25", "12", "20", "1"),
("26", "13", "20", "1"),
("27", "14", "20", "1"),
("28", "15", "1", "1"),
("29", "16", "11", "22"),
("30", "16", "19", "12"),
("31", "16", "23", "12"),
("32", "16", "24", "10"),
("33", "16", "10", "7"),
("34", "16", "20", "22");


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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `usuario`
INSERT INTO `usuario` VALUES
("1", "V", "0", "Administrador", "Principal", "Administrador@gmail.com", "Administrador", "$2y$10$Jgm6xFb5Onz/BMdIkNK2Tur8yg/NYEMb/tdnhoV7kB1BwIG4R05D2", "Administrador_23.jpg", "1", "1", NULL, NULL, NULL, NULL, NULL, NULL, "Activo"),
("15", "V", "32600641", "Ander", "Peña", "super@gmail.com", "Andflizzz", "$2y$10$hMZQklYXGwAIopq17RdtbObW2vlWizTzYYq3XNZ30DloZbP/IHwaS", "Ander_39.jpg", "1", "1", "Nombre de tu primera mascota", "Notengo", "Nombre de tu escuela primaria", "Notengo", "Marca de tu primer carro", "Notengo", "Activo"),
("17", "V", "12321444", "Richard alexander", "Pereira gil", "richi@gmail.com", "Richis", "$2y$10$Qzr/Pjxao/sGyY/uokNIwe9PFR5u.K.OK0K7TCq.1uk1M.m1YmW3K", "", "1", "1", NULL, NULL, NULL, NULL, NULL, NULL, "Activo"),
("18", "V", "18645123", "Maria elizabeth", "Coromoto ortiz", "mariaeli@gmail.com", "Mari", "$2y$10$qJfMRkA0yWmT9e71hkcXYuXvr.5.uapJJESRkb6Pm5.v6SrXl9gLS", "", "1", "3", "Nombre de tu primera mascota", "Nose", "Nombre de tu escuela primaria", "Nose", "Marca de tu primer carro", "Nose", "Activo");


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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `venta`
INSERT INTO `venta` VALUES
("3", "VEN-000001", "2026-03-28", "08:34 pm", "624.00", "624.00", "0.00", "468.51", "1", "2", "1", "Pago Movil", "232131"),
("4", "VEN-000004", "2026-04-03", "06:50 pm", "624.00", "624.00", "0.00", "473.92", "15", "2", "1", "Transferencia", "213323");


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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `venta_detalle`
INSERT INTO `venta_detalle` VALUES
("3", "1", "520.00", "624.00", "624.00", "Laptop Acer Aspire Go 15 | Intel Core i5 13va Gen", "VEN-000001", "1"),
("4", "1", "520.00", "624.00", "624.00", "Laptop Acer Aspire Go 15 | Intel Core i5 13va Gen", "VEN-000004", "1");


-- Reactivar restricciones de llaves foráneas
SET FOREIGN_KEY_CHECKS = 1;
