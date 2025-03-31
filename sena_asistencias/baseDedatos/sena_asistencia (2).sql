-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-03-2025 a las 19:25:28
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sena_asistencia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ambientes`
--

CREATE TABLE `ambientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `centro_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ambientes`
--

INSERT INTO `ambientes` (`id`, `nombre`, `centro_id`) VALUES
(1, 'Sistemas 2', 1),
(2, 'Sistemas 1', 1),
(10, 'Apoyo 2', 1),
(12, 'Sistemas 3', 1),
(13, 'virtual 1', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aprendices`
--

CREATE TABLE `aprendices` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `numero_identificacion` varchar(20) NOT NULL,
  `ficha_id` int(11) NOT NULL,
  `centro_id` int(11) NOT NULL,
  `regional_id` int(11) NOT NULL,
  `programa_formacion_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `aprendices`
--

INSERT INTO `aprendices` (`id`, `nombre`, `numero_identificacion`, `ficha_id`, `centro_id`, `regional_id`, `programa_formacion_id`) VALUES
(1, 'Juan Pérez', '1001', 1, 1, 1, 1),
(2, 'María Gómez', '1002', 1, 1, 1, 1),
(3, 'Carlos López', '1003', 2, 1, 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

CREATE TABLE `asistencias` (
  `id` int(11) NOT NULL,
  `aprendiz_id` int(11) NOT NULL,
  `programa_id` int(11) NOT NULL,
  `ficha_id` int(11) NOT NULL,
  `ambiente_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `estado` enum('presente','ausente','excusa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencias`
--

INSERT INTO `asistencias` (`id`, `aprendiz_id`, `programa_id`, `ficha_id`, `ambiente_id`, `fecha`, `hora_inicio`, `hora_fin`, `estado`) VALUES
(28, 1, 4, 1, 10, '2025-03-31', '07:00:00', '12:00:00', 'presente'),
(29, 2, 4, 1, 10, '2025-03-31', '07:00:00', '12:00:00', 'excusa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `centros`
--

CREATE TABLE `centros` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `regional_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `centros`
--

INSERT INTO `centros` (`id`, `nombre`, `regional_id`) VALUES
(1, 'CPIC', 1),
(3, 'Minero Ambiental', 4),
(4, 'Automatizacion Industrial', 6),
(5, 'Automatizacion Industrial', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coordinadores`
--

CREATE TABLE `coordinadores` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `centro_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `coordinadores`
--

INSERT INTO `coordinadores` (`id`, `usuario_id`, `centro_id`) VALUES
(1, 4, 1),
(2, 6, 3),
(3, 7, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fichas`
--

CREATE TABLE `fichas` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `programa_formacion_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `fichas`
--

INSERT INTO `fichas` (`id`, `codigo`, `programa_formacion_id`) VALUES
(1, '2873711', 1),
(2, '2874614', 2),
(4, '2847431', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instructores`
--

CREATE TABLE `instructores` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `centro_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `instructores`
--

INSERT INTO `instructores` (`id`, `usuario_id`, `centro_id`) VALUES
(1, 5, 1),
(2, 9, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programas_formacion`
--

CREATE TABLE `programas_formacion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `centro_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `programas_formacion`
--

INSERT INTO `programas_formacion` (`id`, `nombre`, `centro_id`) VALUES
(1, 'Programa de Sistemas', 1),
(2, 'Programa de Electrónica', 1),
(4, 'ADSO', 1),
(5, 'Mantenimiento de equipos de computo', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `regionales`
--

CREATE TABLE `regionales` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `regionales`
--

INSERT INTO `regionales` (`id`, `nombre`) VALUES
(1, 'Regional Caldas'),
(4, 'Regional El Bagre'),
(6, 'Dosquebradas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `numero_identificacion` varchar(20) NOT NULL,
  `rol` enum('superadmin','coordinador','instructor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `numero_identificacion`, `rol`) VALUES
(1, 'Super Admin', 'superadmin@sena.edu.co', '123456789', 'superadmin'),
(4, 'Juan Pepe', 'pepe@hotmail.com', '12345671', 'coordinador'),
(5, 'carlitos', 'carlos@gmail.com', '123', 'instructor'),
(6, 'German ', 'germancito@gmail.com', '1213141516', 'coordinador'),
(7, 'Jose', 'Jose@gmail.com', '12345', 'coordinador'),
(9, 'Jose', 'j@gmail.com', '1456', 'instructor');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ambientes`
--
ALTER TABLE `ambientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `centro_id` (`centro_id`);

--
-- Indices de la tabla `aprendices`
--
ALTER TABLE `aprendices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ficha_id` (`ficha_id`),
  ADD KEY `centro_id` (`centro_id`),
  ADD KEY `regional_id` (`regional_id`),
  ADD KEY `programa_formacion_id` (`programa_formacion_id`);

--
-- Indices de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aprendiz_id` (`aprendiz_id`),
  ADD KEY `programa_id` (`programa_id`),
  ADD KEY `ficha_id` (`ficha_id`),
  ADD KEY `ambiente_id` (`ambiente_id`);

--
-- Indices de la tabla `centros`
--
ALTER TABLE `centros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `regional_id` (`regional_id`);

--
-- Indices de la tabla `coordinadores`
--
ALTER TABLE `coordinadores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `centro_id` (`centro_id`);

--
-- Indices de la tabla `fichas`
--
ALTER TABLE `fichas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `programa_formacion_id` (`programa_formacion_id`);

--
-- Indices de la tabla `instructores`
--
ALTER TABLE `instructores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `centro_id` (`centro_id`);

--
-- Indices de la tabla `programas_formacion`
--
ALTER TABLE `programas_formacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `centro_id` (`centro_id`);

--
-- Indices de la tabla `regionales`
--
ALTER TABLE `regionales`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_identificacion` (`numero_identificacion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ambientes`
--
ALTER TABLE `ambientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `aprendices`
--
ALTER TABLE `aprendices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `centros`
--
ALTER TABLE `centros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `coordinadores`
--
ALTER TABLE `coordinadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `fichas`
--
ALTER TABLE `fichas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `instructores`
--
ALTER TABLE `instructores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `programas_formacion`
--
ALTER TABLE `programas_formacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `regionales`
--
ALTER TABLE `regionales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ambientes`
--
ALTER TABLE `ambientes`
  ADD CONSTRAINT `ambientes_ibfk_1` FOREIGN KEY (`centro_id`) REFERENCES `centros` (`id`);

--
-- Filtros para la tabla `aprendices`
--
ALTER TABLE `aprendices`
  ADD CONSTRAINT `aprendices_ibfk_1` FOREIGN KEY (`ficha_id`) REFERENCES `fichas` (`id`),
  ADD CONSTRAINT `aprendices_ibfk_2` FOREIGN KEY (`centro_id`) REFERENCES `centros` (`id`),
  ADD CONSTRAINT `aprendices_ibfk_3` FOREIGN KEY (`regional_id`) REFERENCES `regionales` (`id`),
  ADD CONSTRAINT `aprendices_ibfk_4` FOREIGN KEY (`programa_formacion_id`) REFERENCES `programas_formacion` (`id`);

--
-- Filtros para la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`aprendiz_id`) REFERENCES `aprendices` (`id`),
  ADD CONSTRAINT `asistencias_ibfk_2` FOREIGN KEY (`programa_id`) REFERENCES `programas_formacion` (`id`),
  ADD CONSTRAINT `asistencias_ibfk_3` FOREIGN KEY (`ficha_id`) REFERENCES `fichas` (`id`),
  ADD CONSTRAINT `asistencias_ibfk_4` FOREIGN KEY (`ambiente_id`) REFERENCES `ambientes` (`id`);

--
-- Filtros para la tabla `centros`
--
ALTER TABLE `centros`
  ADD CONSTRAINT `centros_ibfk_1` FOREIGN KEY (`regional_id`) REFERENCES `regionales` (`id`);

--
-- Filtros para la tabla `coordinadores`
--
ALTER TABLE `coordinadores`
  ADD CONSTRAINT `coordinadores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `coordinadores_ibfk_2` FOREIGN KEY (`centro_id`) REFERENCES `centros` (`id`);

--
-- Filtros para la tabla `fichas`
--
ALTER TABLE `fichas`
  ADD CONSTRAINT `fichas_ibfk_1` FOREIGN KEY (`programa_formacion_id`) REFERENCES `programas_formacion` (`id`);

--
-- Filtros para la tabla `instructores`
--
ALTER TABLE `instructores`
  ADD CONSTRAINT `instructores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `instructores_ibfk_2` FOREIGN KEY (`centro_id`) REFERENCES `centros` (`id`);

--
-- Filtros para la tabla `programas_formacion`
--
ALTER TABLE `programas_formacion`
  ADD CONSTRAINT `programas_formacion_ibfk_1` FOREIGN KEY (`centro_id`) REFERENCES `centros` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
