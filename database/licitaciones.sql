-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 28-01-2026 a las 00:08:29
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
-- Base de datos: `licitaciones`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

CREATE TABLE `actividades` (
  `id` int(11) NOT NULL,
  `codigo_segmento` int(11) DEFAULT NULL,
  `segmento` varchar(200) DEFAULT NULL,
  `codigo_familia` int(11) DEFAULT NULL,
  `familia` varchar(200) DEFAULT NULL,
  `codigo_clase` int(11) DEFAULT NULL,
  `clase` varchar(200) DEFAULT NULL,
  `codigo_producto` int(11) DEFAULT NULL,
  `producto` varchar(200) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`id`, `codigo_segmento`, `segmento`, `codigo_familia`, `familia`, `codigo_clase`, `clase`, `codigo_producto`, `producto`, `creado_en`) VALUES
(1, 10, 'Servicios', 11, 'Servicios de TI', 12, 'Desarrollo de Software', 13, 'Desarrollo Web', '2026-01-27 21:02:02'),
(2, 20, 'Bienes', 21, 'Equipos de Oficina', 22, 'Computadoras', 23, 'Laptops', '2026-01-27 21:02:02'),
(3, 30, 'Construcción', 31, 'Materiales', 32, 'Materiales Eléctricos', 33, 'Cables', '2026-01-27 21:02:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ofertas`
--

CREATE TABLE `ofertas` (
  `id` int(11) NOT NULL,
  `consecutivo` varchar(50) DEFAULT NULL,
  `objeto` varchar(150) NOT NULL,
  `descripcion` varchar(400) NOT NULL,
  `moneda` varchar(3) DEFAULT 'COP',
  `presupuesto` decimal(15,2) DEFAULT 0.00,
  `actividad_id` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `fecha_cierre` date DEFAULT NULL,
  `hora_cierre` time DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'pendiente',
  `creado_en` datetime DEFAULT current_timestamp(),
  `actualizado_en` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ofertas`
--

INSERT INTO `ofertas` (`id`, `consecutivo`, `objeto`, `descripcion`, `moneda`, `presupuesto`, `actividad_id`, `fecha_inicio`, `hora_inicio`, `fecha_cierre`, `hora_cierre`, `estado`, `creado_en`, `actualizado_en`) VALUES
(1, 'O-0001-24', 'Sistema de Gestión de Licitaciones', 'Desarrollo de plataforma web completa para licitaciones', 'COP', 75000000.00, 1, '2024-01-15', '08:00:00', '2024-02-28', '17:00:00', 'activa', '2026-01-27 16:02:32', '2026-01-27 16:02:32'),
(2, 'O-0002-24', 'Adquisición de Equipos Informáticos', 'Compra de 30 computadores portátiles para personal', 'USD', 45000.00, 2, '2024-01-20', '09:00:00', '2024-02-10', '18:00:00', 'pendiente', '2026-01-27 16:02:32', '2026-01-27 16:02:32'),
(3, 'O-0003-24', 'Suministro de Materiales de Construcción', 'Materiales eléctricos para proyecto residencial', 'EUR', 28000.00, 3, '2024-01-10', '08:30:00', '2024-01-25', '16:00:00', 'cerrada', '2026-01-27 16:02:32', '2026-01-27 16:02:32'),
(4, 'O-0004-26', 'hola', 'asfds', 'COP', 12321312.00, 1, '2026-01-27', '08:00:00', '2026-02-03', '17:00:00', 'pendiente', '2026-01-27 23:26:24', '2026-01-27 17:26:24'),
(5, 'O-0005-26', 'hola', 'asfds', 'COP', 12321312.00, 1, '2026-01-27', '08:00:00', '2026-02-03', '17:00:00', 'pendiente', '2026-01-27 23:26:58', '2026-01-27 17:26:58'),
(8, 'O-0006-26', '1', 'adsfasd', 'COP', 111.00, 1, '2026-01-27', '08:00:00', '2026-02-03', '17:00:00', 'pendiente', '2026-01-27 23:41:20', '2026-01-27 17:41:20');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ofertas`
--
ALTER TABLE `ofertas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `consecutivo` (`consecutivo`),
  ADD KEY `actividad_id` (`actividad_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ofertas`
--
ALTER TABLE `ofertas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ofertas`
--
ALTER TABLE `ofertas`
  ADD CONSTRAINT `ofertas_ibfk_1` FOREIGN KEY (`actividad_id`) REFERENCES `actividades` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
