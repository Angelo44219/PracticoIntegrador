-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-10-2023 a las 21:39:13
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `rappibnb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alquiler`
--

CREATE TABLE `alquiler` (
  `id` int(11) NOT NULL,
  `estado` int(1) NOT NULL,
  `costo` decimal(65,0) NOT NULL,
  `fecha inicio` date NOT NULL,
  `fecha fin` date NOT NULL,
  `id_publicacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alquiler`
--

INSERT INTO `alquiler` (`id`, `estado`, `costo`, `fecha inicio`, `fecha fin`, `id_publicacion`, `id_usuario`) VALUES
(1, 1, 50000, '2023-10-31', '2023-11-24', 1, 11),
(2, 1, 18000, '2023-12-01', '2023-12-30', 6, 11),
(3, 1, 24000, '2024-01-19', '2024-02-13', 5, 11),
(4, 1, 30000, '2024-01-20', '2024-01-31', 4, 13),
(5, 1, 150000, '2023-10-21', '2023-10-31', 3, 13),
(6, 1, 560000, '2024-02-09', '2023-10-14', 2, 13);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagen`
--

CREATE TABLE `imagen` (
  `id_imagen` int(11) NOT NULL,
  `id_publicacion` int(11) NOT NULL,
  `imagen` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `imagen`
--

INSERT INTO `imagen` (`id_imagen`, `id_publicacion`, `imagen`) VALUES
(1, 1, './Imagenes/hotel_lakeview.jpg'),
(2, 2, './imagenes/Jacksinnsign.webp'),
(3, 3, './imagenes/WoodSide.webp'),
(4, 4, './imagenes/Hotel_South_Ashfield.webp'),
(5, 5, './imagenes/las-espuelas-casas-de.jpg'),
(6, 6, './imagenes/Diseño-de-casa-moderna.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicacion`
--

CREATE TABLE `publicacion` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `descripcion` varchar(250) NOT NULL,
  `ubicacion` varchar(250) NOT NULL,
  `tiempo_minimo` int(11) NOT NULL,
  `tiempo_maximo` int(11) NOT NULL,
  `cupo` int(11) NOT NULL,
  `id_resena` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicacion`
--

INSERT INTO `publicacion` (`id`, `id_usuario`, `titulo`, `descripcion`, `ubicacion`, `tiempo_minimo`, `tiempo_maximo`, `cupo`, `id_resena`) VALUES
(1, 11, 'Hotel Lakeview ', 'Hotel cerca del lago toluca', 'Mitre 203', 1, 7, 2, NULL),
(2, 13, 'Hotel Jack\'s inn', 'Hotel ubicado en las calles de Silent hill', 'Villa Elena 3001', 2, 10, 5, NULL),
(3, 13, 'Apartamentos Woodside', 'Un complejo de apartamentos ubicado en la calle \r\nRichard Bachman ', 'Richard Bachman 2123', 1, 8, 1, NULL),
(4, 13, 'Departamentos South Ashfield Heights', 'Complejo de apartamentos ubicado en las calles del sur de Ashfield heights se advierte que tenga cuidado con la habitacion 302 de resto que tenga un buen dia.', 'Ashfield Heights 2991', 4, 10, 6, NULL),
(5, 11, 'Casa de campo en las sierras', 'Casa de campo que se encuentra ubicada en los montañas del legendario monte Hyjald.', 'Hyjald 3001', 3, 6, 6, NULL),
(6, 11, 'Casa moderna de las Sierras Mandora', 'Casa moderna que se encuentra ubicada en las sierras de mandora ,con un amueblado limpio y moderno. ', 'Mandora029', 1, 10, 4, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicacion_servicio`
--

CREATE TABLE `publicacion_servicio` (
  `id_publicacion` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicacion_servicio`
--

INSERT INTO `publicacion_servicio` (`id_publicacion`, `id_servicio`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(3, 9),
(1, 3),
(1, 6),
(1, 8),
(1, 5),
(1, 9),
(2, 5),
(2, 6),
(4, 1),
(4, 5),
(4, 4),
(4, 2),
(4, 3),
(4, 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resena`
--

CREATE TABLE `resena` (
  `id` int(11) NOT NULL,
  `comentario` varchar(500) NOT NULL,
  `respuesta` varchar(500) NOT NULL,
  `id_publicacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `id` int(11) NOT NULL,
  `nombre` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`id`, `nombre`) VALUES
(1, 'Agua'),
(2, 'Luz'),
(3, 'Internet'),
(4, 'Gas'),
(5, 'Baño'),
(6, 'Cocina'),
(7, 'Garage'),
(8, 'Camaras de seguridad'),
(9, 'Aire acondicionado'),
(10, 'Calefaccion'),
(12, 'pisina'),
(13, 'patio'),
(14, 'vistas ala montañas'),
(15, 'vistas al mar'),
(16, 'garage');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `apellido` varchar(250) NOT NULL,
  `documento` int(8) NOT NULL,
  `certificacion` bit(1) DEFAULT NULL,
  `biografia` varchar(500) DEFAULT NULL,
  `foto` varchar(250) NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `admin` bit(1) NOT NULL,
  `email` varchar(250) NOT NULL,
  `contrasena` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombre`, `apellido`, `documento`, `certificacion`, `biografia`, `foto`, `fecha_vencimiento`, `admin`, `email`, `contrasena`) VALUES
(1, 'Angelo', 'Whitelust', 44219425, b'0', 'Hola bom dia!', 'C:\\xampp\\htdocs\\Practico_integrador\\Imagenes\\blank-profile-picture-973460_640.png', '2027-09-10', b'0', 'AngeloWhitedemon@gmail.com', 'Jawsum560'),
(2, 'Victor ', 'Crisfallen', 55321891, b'1', 'Un hombre de pocos amigos y que envidia a todo el mundo.', '', '2031-10-04', b'0', 'VictoCrhisfall@gmail.com', 'Vic593'),
(3, 'Angelina', 'Machado', 77339110, b'1', 'Amante de la literatura y tener arañas como mascota\r\ncomo hace mi madre..', './Imagenes/blank-profile-picture-973460_1280', NULL, b'0', 'AngelinaMachado@gmail.com', 'Machado391'),
(11, 'Luna', 'Honeymoon', 22296185, NULL, '                                ', 'blank-profile-picture-973460_1280.png', NULL, b'0', 'Lanadelrey@gmail.com', '$2y$10$yxymzBhjmB33ITi3rzQP4.6a2CQwuKzc/Gv5Yk0bm34N0lJen4NRS'),
(13, 'Alexandra', 'Muñoz', 45521421, NULL, '                                ', 'blank-profile-picture-973460_1280.png', NULL, b'0', 'Ale288@gmail.com', '$2y$10$MLpjHkLBn2MbblQ3XLQbu.Gbnjp0JQJGnuFrV0qCVoE8z1pA9bVMe'),
(14, 'Veronica', 'Machado', 66422122, NULL, '                                no hay nada', 'blank-profile-picture-973460_1280.png', NULL, b'0', 'Vero29@gmail.com', '$2y$10$hXC4mBWDec3Jg4rdhEFM.ODq34b6YbhFfxPu3huNb1GGVupx4yhT2');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alquiler`
--
ALTER TABLE `alquiler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_usuario` (`id_usuario`),
  ADD KEY `fk_publicacion` (`id_publicacion`);

--
-- Indices de la tabla `imagen`
--
ALTER TABLE `imagen`
  ADD PRIMARY KEY (`id_imagen`),
  ADD KEY `fk_img_publicacion` (`id_publicacion`);

--
-- Indices de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pb_usuario` (`id_usuario`);

--
-- Indices de la tabla `publicacion_servicio`
--
ALTER TABLE `publicacion_servicio`
  ADD KEY `fk_ps_publicacion` (`id_publicacion`),
  ADD KEY `fk_ps_resena` (`id_servicio`);

--
-- Indices de la tabla `resena`
--
ALTER TABLE `resena`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pb_resena` (`id_publicacion`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alquiler`
--
ALTER TABLE `alquiler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `imagen`
--
ALTER TABLE `imagen`
  MODIFY `id_imagen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `resena`
--
ALTER TABLE `resena`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alquiler`
--
ALTER TABLE `alquiler`
  ADD CONSTRAINT `fk_publicacion` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id`),
  ADD CONSTRAINT `fk_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `imagen`
--
ALTER TABLE `imagen`
  ADD CONSTRAINT `fk_img_publicacion` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id`);

--
-- Filtros para la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD CONSTRAINT `fk_pb_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `publicacion_servicio`
--
ALTER TABLE `publicacion_servicio`
  ADD CONSTRAINT `fk_ps_publicacion` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id`),
  ADD CONSTRAINT `fk_ps_resena` FOREIGN KEY (`id_servicio`) REFERENCES `servicio` (`id`);

--
-- Filtros para la tabla `resena`
--
ALTER TABLE `resena`
  ADD CONSTRAINT `fk_pb_resena` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
