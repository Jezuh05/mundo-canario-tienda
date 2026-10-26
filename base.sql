-- Desactivar comprobación de claves foráneas para la importación
SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

-- Tabla `categorias`
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categorias` (`id`, `nombre`, `activo`) VALUES
(1, 'Canarios de Color', 1),
(2, 'Canarios de Canto', 1),
(3, 'Alimentación y Semillas', 1),
(4, 'Medicinas y Vitaminas', 1),
(5, 'Jaulas y Accesorios', 1),
(7, 'Anillas', 1);

-- --------------------------------------------------------

-- Tabla `lineas_pedidos`
CREATE TABLE `lineas_pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `unidades` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_linea_pedido` (`pedido_id`),
  KEY `fk_linea_producto` (`producto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `lineas_pedidos` (`id`, `pedido_id`, `producto_id`, `unidades`) VALUES
(1, 1, 1, 1), (2, 1, 3, 2), (3, 2, 3, 4), (4, 3, 1, 4), (5, 4, 3, 1), 
(6, 4, 1, 1), (7, 4, 2, 1), (8, 4, 6, 1), (9, 5, 3, 1), (10, 5, 2, 1), 
(11, 5, 4, 1), (12, 6, 2, 3), (13, 7, 4, 1), (14, 7, 1, 4), (15, 8, 1, 1), 
(16, 9, 3, 1), (17, 9, 6, 3), (18, 9, 7, 1), (19, 10, 7, 1), (20, 10, 6, 2), 
(21, 11, 7, 3), (22, 11, 3, 1), (23, 12, 3, 1), (24, 12, 2, 1), (25, 12, 7, 6);

-- --------------------------------------------------------

-- Tabla `pedidos`
CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `provincia` varchar(100) NOT NULL,
  `localidad` varchar(100) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `coste` decimal(10,2) NOT NULL,
  `estado` varchar(20) DEFAULT 'confirm',
  `fecha` date DEFAULT curdate(),
  `hora` time DEFAULT curtime(),
  PRIMARY KEY (`id`),
  KEY `fk_pedido_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pedidos` (`id`, `usuario_id`, `provincia`, `localidad`, `direccion`, `coste`, `estado`, `fecha`, `hora`) VALUES
(1, 2, 'Alicante', 'Santa Pola', 'Calle Mayor 1', 34.00, 'confirm', '2026-01-25', '17:37:10'),
(2, 2, 'Alicante', 'Santa Pola', 'Calle San Fernando 12', 18.00, 'confirm', '2026-01-25', '17:38:07'),
(3, 1, 'Madrid', 'Madrid', 'Calle del Sol 23', 100.00, 'enviado', '2026-01-25', '17:39:36'),
(4, 2, 'Alicante', 'Santa Pola', 'Calle San Fernando 12', 67.00, 'entregado', '2026-01-25', '17:40:04'),
(5, 1, 'Alicante', 'Santa Pola', 'Calle del Prado 5', 40.70, 'confirm', '2026-01-25', '18:43:16'),
(6, 1, 'Alicante', 'Santa Pola', 'Calle del Prado 5', 90.00, 'confirm', '2026-01-25', '18:43:31'),
(7, 1, 'Alicante', 'Santa Pola', 'Calle del Prado 5', 106.20, 'entregado', '2026-01-25', '18:49:27'),
(8, 4, 'Barcelona', 'Badalona', 'Avenida Catalunya 8', 25.00, 'confirm', '2026-01-25', '18:59:03'),
(9, 1, 'Alicante', 'Santa Pola', 'Calle del Prado 5', 33.80, 'confirm', '2026-01-25', '19:25:08'),
(10, 1, 'Alicante', 'Elche', 'Calle Reina Victoria 4', 21.80, 'confirm', '2026-01-25', '21:36:04'),
(11, 1, 'Alicante', 'Santa Pola', 'Calle del Prado 5', 24.90, 'enviado', '2026-01-25', '21:38:09'),
(12, 1, 'Alicante', 'Santa Pola', 'Calle del Prado 5', 75.30, 'confirm', '2026-01-25', '21:56:06');

-- --------------------------------------------------------

-- Tabla `productos`
CREATE TABLE `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `oferta` varchar(20) DEFAULT NULL,
  `fecha` date DEFAULT curdate(),
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_producto_categoria` (`categoria_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `productos` (`id`, `categoria_id`, `nombre`, `descripcion`, `precio`, `stock`, `oferta`, `fecha`, `imagen`, `activo`) VALUES
(1, 1, 'Canario Amarillo Mosaico', 'Macho año 2024.', 25.00, 0, NULL, '2025-12-19', 'amarillo_mosaico.jpg', 1),
(2, 2, 'Canario Timbrado Español', 'Macho con excelente canto', 30.00, 3, NULL, '2025-12-19', '1769377036_canariotimbrado.jpg', 1),
(3, 3, 'Mixtura Premium Sin Avena', 'Saco de 1kg. Mezcla equilibrada para todo el año.', 4.50, 46, NULL, '2025-12-19', 'mixtura.jpg', 1),
(4, 3, 'Pasta de Cría Amarilla', 'Pasta húmeda mórbida, ideal para época de cría.', 6.20, 4, NULL, '2025-12-19', 'pasta_cria.jpg', 1),
(5, 4, 'Seriferol 15ml (Vitamina E)', 'Lab. LATAC. Ideal para encelar antes de la cría.', 8.90, 15, NULL, '2025-12-19', 'seriferol.jpg', 1),
(6, 4, 'Tabernil Muda', 'Suplemento vitamínico para favorecer el cambio de plumaje.', 7.50, 5, NULL, '2025-12-19', 'tabernil_muda.jpg', 1),
(7, 4, 'Tabernil Canto', 'Estimulante del canto para machos.', 6.80, 2, NULL, '2025-12-19', 'tabernil_canto.jpg', 1);

-- --------------------------------------------------------

-- Tabla `usuarios`
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dni` varchar(20) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(150) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(20) DEFAULT 'cliente',
  `activo` tinyint(1) DEFAULT 1,
  `fecha_registro` date DEFAULT curdate(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `email`, `password`, `rol`, `activo`, `fecha_registro`) VALUES
(1, 'Admin', 'Tienda', 'admin@admin.com', '$2y$10$pvZymc4rJOhJ2TwicIhUV.dV/mdafzvtwSWdG3YMf2HiNkmLTuwSi', 'admin', 1, '2025-12-19'),
(2, 'Pepe', 'Cliente', 'cliente@cliente.com', '$2y$10$HlDzHcPUnypOoFDJS8PJnO6sqVhuns6..tH.pqZU5hLoobEovrPKW', 'cliente', 1, '2026-01-07'),
(4, 'Juan', 'Perez', 'cliente2@cliente.com', '$2y$10$HlDzHcPUnypOoFDJS8PJnO6sqVhuns6..tH.pqZU5hLoobEovrPKW', 'cliente', 1, '2026-01-07'),
(5, 'Carlos', 'Gomez', 'carlos@cliente.com', '$2y$10$HlDzHcPUnypOoFDJS8PJnO6sqVhuns6..tH.pqZU5hLoobEovrPKW', 'cliente', 1, '2026-01-25'),
(6, 'Luis', 'Empleado', 'empleado@tienda.com', '$2y$10$OWClDbNmTOWQoU6NDF1SXO8cI.fhFujXcgi7E7LOaAz0cAF9cjkyu', 'empleado', 1, '2026-01-25');

-- --------------------------------------------------------

-- Restaurar claves foráneas
ALTER TABLE `lineas_pedidos`
  ADD CONSTRAINT `fk_linea_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `fk_linea_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedido_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

ALTER TABLE `productos`
  ADD CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
