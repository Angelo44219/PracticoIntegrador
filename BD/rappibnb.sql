-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-11-2023 a las 21:32:47
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
  `estado_alquiler` enum('pendiente','aceptado','completado') NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `fecha_aplicacion` date NOT NULL DEFAULT curdate(),
  `id_publicacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alquiler`
--

INSERT INTO `alquiler` (`id`, `estado_alquiler`, `fecha_inicio`, `fecha_fin`, `fecha_aplicacion`, `id_publicacion`, `id_usuario`) VALUES
(14, 'completado', '2023-11-13', '2023-11-14', '2023-11-13', 12, 26),
(15, 'aceptado', '2023-11-18', '2023-11-20', '2023-11-16', 18, 11);

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
  `etiqueta` text NOT NULL,
  `fotos` text NOT NULL,
  `servicio` text NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 0,
  `costo` decimal(10,2) NOT NULL,
  `tiempo_minimo` int(11) NOT NULL,
  `tiempo_maximo` int(11) NOT NULL,
  `cupo` int(11) NOT NULL,
  `fecha_pub_inicio` date DEFAULT NULL,
  `fecha_pub_fin` date DEFAULT NULL,
  `fecha_subida` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicacion`
--

INSERT INTO `publicacion` (`id`, `id_usuario`, `titulo`, `descripcion`, `ubicacion`, `etiqueta`, `fotos`, `servicio`, `estado`, `costo`, `tiempo_minimo`, `tiempo_maximo`, `cupo`, `fecha_pub_inicio`, `fecha_pub_fin`, `fecha_subida`) VALUES
(10, 11, 'Hotel Lakeview', 'Alquilo este Hotel que se encuentra en las orillas del lago toluca cuyo hotel posee muchas habitaciones , asensores  y un pequeño bar (El cual esta inundado por un señor de 30 años rubio que decia que buscaba a su amada creo se que llamaba \"Mary\")', 'Lago Toluca 328', 'lago , botes , cinta de video , caja musical', '[\"galeria/6549bc271d5bf_asensores.PNG\",\"galeria/6549bc271d8ea_comedor.jpg\",\"galeria/6549bc271dbac_entrada principal.jpg\",\"galeria/6549bc271de40_habitaciones.PNG\",\"galeria/6549bc271e0bc_hotel_lakeview.jpg\"]', '[\"Cocina\",\"Limpieza\",\"Internet\",\"Desayuno\",\"Merienda\",\"Cena\",\"Patio\",\"Agua\",\"Luz\",\"Ba\\u00f1o\",\"Calefaccion\"]', 1, 12678.00, 4, 34, 5, '0000-00-00', '0000-00-00', '2023-11-07'),
(12, 11, 'Terrence loves me', 'Casa amueblada con muy lindas vistas al mar , reposeras y sombrillas a su disposicion ubicada en las hermosas playas de miami.', 'Terrence382', 'Playa , mar , sombrillas , olas , anochecer , tardes', '[\"galeria/654c94f96e657_playa1.jpg\",\"galeria/654c94f96e902_playa2.jpg\",\"galeria/654c94f96eb49_playa3.jpg\"]', '[\"Cocina\",\"Internet\",\"Agua\",\"Camaras de seguridad\",\"Ba\\u00f1o\"]', 1, 56443.00, 1, 30, 5, '0000-00-00', '0000-00-00', '2023-11-09'),
(17, 26, 'Casa de campo ', 'Alquilo mi vieja casa que una pareja anterior y con un perro raro siendo los antiguos dueños me ha vendido hace muchos años lo cual me advirtieron que pasan muchas cosas extrañas ', 'Lavasse 3912', 'casa, campo , luna , molino ', '[\"galeria/655297aeaee72_casa de coraje 2.jpeg\",\"galeria/655297aeaf0d3_casa de coraje 3.jpeg\",\"galeria/655297aeaf40c_casa de coraje 4.jpeg\",\"galeria/655297aeaf644_casa de coraje.jpeg\"]', '[\"Cocina\",\"Patio\",\"Calefaccion\"]', 1, 45377.00, 1, 23, 3, '2023-11-13', '2023-11-16', '2023-11-13'),
(18, 27, 'The Poison', 'ssss', 'Lavasse 3912', 'lago , botes , cinta de video , caja musical', '[\"galeria/6552b10ba2ce9_light-road-street-house-window-building-589676-pxhere.com.jpg\",\"galeria/6552b10ba306e_playa3.jpg\",\"galeria/6552b10ba32e7_tiro-angulo-gran-edificio-nube-hermoso-cielo-azul.jpg\",\"galeria/6552b10ba3515_user_567902.png\",\"galeria/6552b10ba3738_ventana.png\"]', '[\"Cocina\",\"Piscina\",\"Aire acondicionado\"]', 1, 44443.00, 1, 14, 4, '2023-11-14', '0000-00-00', '2023-11-13'),
(19, 26, 'Casa moderna ubicada en el Barrio Morningside', 'Casa moderna amueblada con todas las comodidades nesesarias para poder satisfacer sus necesidades y con un buen patio para que sus chicos se diviertan.', 'Morningside 493', 'Casa , Morningside, patio , casa moderna', '[\"galeria\\/ai-generado-entrada-estilo-moderno.jpg\",\"galeria\\/lujo-moderno-habitacion-domestica-comoda-relajacion-generativa-ai.jpg\"]', '[\"Cocina\",\"Piscina\",\"Aire acondicionado\",\"Internet\",\"Patio\",\"Camaras de seguridad\",\"Agua\",\"Garage\"]', 1, 445332.00, 3, 20, 2, '0000-00-00', '0000-00-00', '2023-11-15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resena`
--

CREATE TABLE `resena` (
  `id` int(11) NOT NULL,
  `id_publicacion` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `puntuacion` int(11) NOT NULL CHECK (`puntuacion` between 1 and 5),
  `comentario` text DEFAULT NULL,
  `fecha_resena` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `resena`
--

INSERT INTO `resena` (`id`, `id_publicacion`, `id_usuario`, `puntuacion`, `comentario`, `fecha_resena`) VALUES
(22, 12, 26, 4, 'un hermoso lugar 10/10', '2023-11-14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuesta_resena`
--

CREATE TABLE `respuesta_resena` (
  `id` int(11) NOT NULL,
  `id_resena` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `respuesta` text NOT NULL,
  `fecha_respuesta` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `respuesta_resena`
--

INSERT INTO `respuesta_resena` (`id`, `id_resena`, `id_usuario`, `respuesta`, `fecha_respuesta`) VALUES
(44, 22, 11, 'nada', '2023-11-13 18:50:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud`
--

CREATE TABLE `solicitud` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `dni_frente` varchar(255) NOT NULL,
  `dni_dorso` varchar(255) NOT NULL,
  `fecha_solicitud` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `intereses` text NOT NULL,
  `foto` varchar(255) NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `admin` bit(1) NOT NULL,
  `email` varchar(250) NOT NULL,
  `contrasena` varchar(250) NOT NULL,
  `activacion_cuenta_hash` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombre`, `apellido`, `documento`, `certificacion`, `biografia`, `intereses`, `foto`, `fecha_vencimiento`, `admin`, `email`, `contrasena`, `activacion_cuenta_hash`) VALUES
(11, 'Luna', 'Honeymoon', 22296185, b'1', 'Me encanta Lana del rey', 'Musica , dibujo , terror , los payasos', 'galeria/6549bdf64d907-Lana.jpg', '2023-11-16', b'1', 'Lanadelrey@gmail.com', '$2y$10$3.PWw5USZdxvpmP5H4YkCO98y3fOBTMmN7optb01gnZf2KMGD4Sl6', NULL),
(26, 'Laia', 'Machado', 33282199, b'1', '                                                                                                                        la oscuridad es todo lo que puedo ver...                                                                                                                                                                                                ', '                                                                                                                              dibujar, leer, escribir , tocar el teclado                                                                                                                                                                ', 'galeria/6552538dbb1f2_emily.jpg', '2023-11-30', b'0', 'Vero29@gmail.com', '$2y$10$iH8tb/dBLluRIGRwyd5xteuO39mfSaY8xzGgnqvAzcAdEOAVstEfW', NULL),
(27, 'Angelo', 'Whitelust', 44219425, b'1', '                                                                              ', '                                                                                ', 'galeria/6552b0c418635_blank-profile-picture-973460_1280.png', '2023-11-16', b'0', 'otaku.jorge12345@gmail.com', '$2y$10$e5lyLK9EJ7Z.NdUuHNQ2/.D2Vm07QBCeORdHAPI61x8z03HlwQRD2', NULL),
(29, 'Elias', 'Coleman', 77829122, NULL, '                                        Me gusta leer libros                                                                                                                   ', '                                          ninguno                                                                                                                      ', 'galeria/655529a799873_blank-profile-picture-973460_1280.png', NULL, b'0', 'Eliascoleman@gmail.com', '$2y$10$jZQGc5w.G6gEWjWfdUYYa.KK2QE52PKLjSzJA2uvYhKvcxnCR6JyC', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alquiler`
--
ALTER TABLE `alquiler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_publicacion` (`id_publicacion`),
  ADD KEY `fk_usuario` (`id_usuario`);

--
-- Indices de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pb_usuario` (`id_usuario`);

--
-- Indices de la tabla `resena`
--
ALTER TABLE `resena`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_publicacion` (`id_publicacion`,`id_usuario`),
  ADD KEY `resenia_ibfk_2` (`id_usuario`);

--
-- Indices de la tabla `respuesta_resena`
--
ALTER TABLE `respuesta_resena`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_resena` (`id_resena`);

--
-- Indices de la tabla `solicitud`
--
ALTER TABLE `solicitud`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `activacion_cuenta_hash` (`activacion_cuenta_hash`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alquiler`
--
ALTER TABLE `alquiler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `resena`
--
ALTER TABLE `resena`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `respuesta_resena`
--
ALTER TABLE `respuesta_resena`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `solicitud`
--
ALTER TABLE `solicitud`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alquiler`
--
ALTER TABLE `alquiler`
  ADD CONSTRAINT `fk_publicacion` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD CONSTRAINT `fk_pb_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `resena`
--
ALTER TABLE `resena`
  ADD CONSTRAINT `resena_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `resena_ibfk_4` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `respuesta_resena`
--
ALTER TABLE `respuesta_resena`
  ADD CONSTRAINT `id_resena` FOREIGN KEY (`id_resena`) REFERENCES `resena` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `respuesta_resena_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitud`
--
ALTER TABLE `solicitud`
  ADD CONSTRAINT `solicitud_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
