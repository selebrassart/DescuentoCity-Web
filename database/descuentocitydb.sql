-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-11-2025 a las 20:49:20
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
-- Base de datos: `descuentocitydb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes`
--

CREATE TABLE `imagenes` (
  `IdImg` int(11) NOT NULL,
  `tipoImg` enum('logo','galeria','portada','carrusel') NOT NULL,
  `nombreImg` varchar(255) NOT NULL,
  `rutaArchivo` varchar(255) NOT NULL,
  `tipoIdentidad` enum('local','novedad','promocion') NOT NULL,
  `idIdentidad` int(11) NOT NULL,
  `fechaSubida` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `imagenes`
--

INSERT INTO `imagenes` (`IdImg`, `tipoImg`, `nombreImg`, `rutaArchivo`, `tipoIdentidad`, `idIdentidad`, `fechaSubida`) VALUES
(1, 'logo', '1762280842_logoWilson.webp', 'uploads/logos/1762280842_logoWilson.webp', 'local', 32, '2025-11-04 15:27:22'),
(2, 'logo', '1762280903_logo-vector-rolex.webp', 'uploads/logos/1762280903_logo-vector-rolex.webp', 'local', 33, '2025-11-04 15:28:23'),
(3, 'logo', '1762280944_ADIDAS-logo.jpg', 'uploads/logos/1762280944_ADIDAS-logo.jpg', 'local', 34, '2025-11-04 15:29:04'),
(4, 'logo', '1762281044_mclogo.png', 'uploads/logos/1762281044_mclogo.png', 'local', 35, '2025-11-04 15:30:44'),
(5, 'portada', '1762281194_novedadPortada.jpg', 'uploads/fondoNovedad/1762281194_novedadPortada.jpg', 'novedad', 4, '2025-11-04 15:33:14'),
(6, 'portada', '1762283738_815693b4.png', 'uploads/fondoNovedad/1762283738_815693b4.png', 'novedad', 5, '2025-11-04 16:15:38'),
(7, 'portada', '1762283751_ac1fb90d.png', 'uploads/fondoNovedad/1762283751_ac1fb90d.png', 'novedad', 6, '2025-11-04 16:15:51'),
(8, 'portada', '1762284067_wilsonPromo.webp', 'uploads/fondoPromo/1762284067_wilsonPromo.webp', 'promocion', 12, '2025-11-04 16:21:07'),
(9, 'logo', '1762284360_6d6d8946.jpg', 'uploads/logos/1762284360_6d6d8946.jpg', 'local', 27, '2025-11-04 16:26:00'),
(10, 'logo', '1762284367_bc2d6be7.png', 'uploads/logos/1762284367_bc2d6be7.png', 'local', 26, '2025-11-04 16:26:07'),
(11, 'logo', '1762284391_eb0c66f8.webp', 'uploads/logos/1762284391_eb0c66f8.webp', 'local', 25, '2025-11-04 16:26:31'),
(12, 'portada', '1762284494_original-b4379fe2bd81dd9decaf632c1be59ba8.webp', 'uploads/fondoPromo/1762284494_original-b4379fe2bd81dd9decaf632c1be59ba8.webp', 'promocion', 13, '2025-11-04 16:28:14'),
(13, 'portada', '1762284654_promoMC.jpeg', 'uploads/fondoPromo/1762284654_promoMC.jpeg', 'promocion', 14, '2025-11-04 16:30:54'),
(14, 'portada', '1762284788_promoAD.avif', 'uploads/fondoPromo/1762284788_promoAD.avif', 'promocion', 15, '2025-11-04 16:33:08'),
(15, 'portada', '1762285450_F1_480x450_2c8f1e187e.jpg', 'uploads/fondoPromo/1762285450_F1_480x450_2c8f1e187e.jpg', 'promocion', 16, '2025-11-04 16:44:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `locales`
--

CREATE TABLE `locales` (
  `codLocal` int(11) NOT NULL,
  `nombreLocal` varchar(100) NOT NULL,
  `ubicacionLocal` varchar(150) DEFAULT NULL,
  `rubroLocal` varchar(50) DEFAULT NULL,
  `codUsuario` int(11) NOT NULL,
  `estadoLocal` enum('activo','eliminado') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `locales`
--

INSERT INTO `locales` (`codLocal`, `nombreLocal`, `ubicacionLocal`, `rubroLocal`, `codUsuario`, `estadoLocal`) VALUES
(25, 'Rolex', 'Piso 3 - Local 1', 'Relojeria', 25, 'activo'),
(26, 'McDonald', 'Piso 1 - Patio comidas', 'Comida rapida', 26, 'activo'),
(27, 'Adidas', 'Piso 4 - Local 1', 'Indumentaria', 27, 'activo'),
(32, 'Wilson', 'Piso 2 - Local 1', 'Indumentaria', 24, 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `novedades`
--

CREATE TABLE `novedades` (
  `codNovedad` int(11) NOT NULL,
  `tituloNovedad` varchar(64) NOT NULL,
  `textoNovedad` text NOT NULL,
  `fechaDesdeNovedad` date NOT NULL,
  `fechaHastaNovedad` date NOT NULL,
  `categoriaCliente` enum('inicial','medium','premium') NOT NULL,
  `estado` enum('activa','eliminada') NOT NULL DEFAULT 'activa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `novedades`
--

INSERT INTO `novedades` (`codNovedad`, `tituloNovedad`, `textoNovedad`, `fechaDesdeNovedad`, `fechaHastaNovedad`, `categoriaCliente`, `estado`) VALUES
(4, 'Halloween en Descuento City', '¡Llega la noche más aterradora del año! 👻Del 25 al 31 de octubre, viví el espíritu de Halloween en Descuento City. Disfrutá de decoraciones temáticas, ofertas espeluznantes en tus locales favoritos y actividades especiales para toda la familia. Trae tu mejor disfraz y participá del concurso de disfraces con premios sorpresa.¡No te lo pierdas, te esperamos para una semana de miedo y diversión! 🕸️🕷️', '2025-11-04', '2025-12-13', 'inicial', 'activa'),
(5, '“Beneficios Exclusivos Premium”', '¡Ser Premium tiene sus ventajas! ✨Durante este mes, los clientes Premium disfrutan de acceso anticipado a las mejores promociones del shopping y regalos sorpresa en locales seleccionados.;Además, podés acceder a la Zona VIP los fines de semana con degustaciones y experiencias exclusivas.Mostrá tu categoría Premium en la app o al ingresar al local y descubrí todos tus beneficios. 🛍️', '2025-11-04', '2025-12-07', 'premium', 'activa'),
(6, '“Subí de nivel y ganá más”', '¡Estás cada vez más cerca del nivel Premium! 🚀;Como cliente Medium, ya accedés a descuentos especiales en moda, tecnología y gastronomía.;Pero eso no es todo: cada compra te acerca al siguiente nivel, donde te esperan beneficios aún más grandes.Consultá tu progreso en tu perfil y seguí aprovechando las mejores ofertas de Descuento City. 💳', '2025-11-04', '2025-12-19', 'medium', 'activa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `reset_token_hash` varchar(255) NOT NULL,
  `reset_token_expire` datetime NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promociones`
--

CREATE TABLE `promociones` (
  `codPromo` int(11) NOT NULL,
  `textoPromo` varchar(200) NOT NULL,
  `fechaDesdePromo` date NOT NULL,
  `fechaHastaPromo` date NOT NULL,
  `categoriaCliente` enum('Inicial','Medium','Premium') NOT NULL,
  `diasSemana` set('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') DEFAULT NULL,
  `estadoPromo` enum('pendiente','aprobada','denegada') NOT NULL DEFAULT 'pendiente',
  `codLocal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `promociones`
--

INSERT INTO `promociones` (`codPromo`, `textoPromo`, `fechaDesdePromo`, `fechaHastaPromo`, `categoriaCliente`, `diasSemana`, `estadoPromo`, `codLocal`) VALUES
(12, '40% accesorios de tenis', '2025-11-04', '2025-11-04', 'Medium', 'Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo', 'aprobada', 32),
(13, '5% en productos seleccionados', '2025-11-04', '2025-11-04', 'Premium', 'Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo', 'aprobada', 25),
(15, '50% en toda la tienda', '2025-11-04', '2025-11-04', 'Medium', 'Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo', 'aprobada', 27),
(16, '20% Edición limitada F1', '2025-11-04', '2025-12-26', 'Inicial', 'Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo', 'aprobada', 26);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_descuentos`
--

CREATE TABLE `solicitudes_descuentos` (
  `id_solicitud` int(11) NOT NULL,
  `codCliente` int(11) NOT NULL,
  `codPromo` int(11) NOT NULL,
  `fecha_solicitud` datetime DEFAULT current_timestamp(),
  `estado` enum('pendiente','aceptada','rechazada','eliminada') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes_descuentos`
--

INSERT INTO `solicitudes_descuentos` (`id_solicitud`, `codCliente`, `codPromo`, `fecha_solicitud`, `estado`) VALUES
(26, 22, 13, '2025-11-04 16:44:47', 'pendiente'),
(27, 22, 12, '2025-11-04 16:45:30', 'aceptada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `uso_promociones`
--

CREATE TABLE `uso_promociones` (
  `idUso` int(11) NOT NULL,
  `codCliente` int(11) NOT NULL,
  `codPromo` int(11) NOT NULL,
  `fechaUsoPromo` datetime DEFAULT current_timestamp(),
  `estado` enum('enviada','aceptada','rechazada','eliminada') DEFAULT 'enviada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `uso_promociones`
--

INSERT INTO `uso_promociones` (`idUso`, `codCliente`, `codPromo`, `fechaUsoPromo`, `estado`) VALUES
(23, 22, 13, '2025-11-04 16:44:47', 'eliminada'),
(24, 22, 12, '2025-11-04 16:45:30', 'aceptada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `codUsuario` int(11) NOT NULL,
  `nombreUsuario` varchar(100) NOT NULL,
  `claveUsuario` varchar(255) NOT NULL,
  `tipoUsuario` enum('admin','dueño','cliente') NOT NULL,
  `categoriaCliente` enum('inicial','medium','premium') DEFAULT 'inicial',
  `estadoUsuario` enum('activo','pendiente','eliminado') DEFAULT 'pendiente',
  `token` varchar(64) DEFAULT NULL,
  `fechaRegistro` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`codUsuario`, `nombreUsuario`, `claveUsuario`, `tipoUsuario`, `categoriaCliente`, `estadoUsuario`, `token`, `fechaRegistro`) VALUES
(1, 'admin@dc.com', '$2y$10$qCL0YTwTjf961QJ3HbVJlO88WsbZ5idzZfFtVeb6DUjcgBxiAE4dG', 'admin', 'inicial', 'activo', NULL, '2025-10-20'),
(22, 'gabilovato45@gmail.com', '$2y$10$XeOhsTY6VAs3BKlDd6Mc2.SmSIVugjCG8MA3zEla0RzzC8ObAx7vm', 'cliente', 'premium', 'activo', 'bfc8ecb163b1b23155c59243ef9ec21f', '2025-11-04'),
(24, 'wilson@gmail.com', '$2y$10$hK..IV.MdsqVHIGKKC1d6.qeMSGJ8pho.LLeB5laCJOgFKgh11w6G', 'dueño', 'inicial', 'activo', 'ee7363623075ab552b35a4c179bffa0d', '2025-11-04'),
(25, 'rolex@gmail.com', '$2y$10$hK..IV.MdsqVHIGKKC1d6.qeMSGJ8pho.LLeB5laCJOgFKgh11w6G', 'dueño', 'inicial', 'activo', 'ee7363623075ab552b35a4c179bffa0d', '2025-11-04'),
(26, 'mc@gmail.com', '$2y$10$hK..IV.MdsqVHIGKKC1d6.qeMSGJ8pho.LLeB5laCJOgFKgh11w6G', 'dueño', 'inicial', 'activo', 'ee7363623075ab552b35a4c179bffa0d', '2025-11-04'),
(27, 'adidas@gmail.com', '$2y$10$hK..IV.MdsqVHIGKKC1d6.qeMSGJ8pho.LLeB5laCJOgFKgh11w6G', 'dueño', 'inicial', 'activo', 'ee7363623075ab552b35a4c179bffa0d', '2025-11-04');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `imagenes`
--
ALTER TABLE `imagenes`
  ADD PRIMARY KEY (`IdImg`);

--
-- Indices de la tabla `locales`
--
ALTER TABLE `locales`
  ADD PRIMARY KEY (`codLocal`),
  ADD KEY `codUsuario` (`codUsuario`);

--
-- Indices de la tabla `novedades`
--
ALTER TABLE `novedades`
  ADD PRIMARY KEY (`codNovedad`);

--
-- Indices de la tabla `promociones`
--
ALTER TABLE `promociones`
  ADD PRIMARY KEY (`codPromo`),
  ADD KEY `codLocal` (`codLocal`);

--
-- Indices de la tabla `solicitudes_descuentos`
--
ALTER TABLE `solicitudes_descuentos`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `codCliente` (`codCliente`),
  ADD KEY `codPromo` (`codPromo`);

--
-- Indices de la tabla `uso_promociones`
--
ALTER TABLE `uso_promociones`
  ADD PRIMARY KEY (`idUso`),
  ADD UNIQUE KEY `codCliente` (`codCliente`,`codPromo`),
  ADD KEY `codPromo` (`codPromo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`codUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `imagenes`
--
ALTER TABLE `imagenes`
  MODIFY `IdImg` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `locales`
--
ALTER TABLE `locales`
  MODIFY `codLocal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `novedades`
--
ALTER TABLE `novedades`
  MODIFY `codNovedad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `promociones`
--
ALTER TABLE `promociones`
  MODIFY `codPromo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `solicitudes_descuentos`
--
ALTER TABLE `solicitudes_descuentos`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `uso_promociones`
--
ALTER TABLE `uso_promociones`
  MODIFY `idUso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `codUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `locales`
--
ALTER TABLE `locales`
  ADD CONSTRAINT `locales_ibfk_1` FOREIGN KEY (`codUsuario`) REFERENCES `usuarios` (`codUsuario`);

--
-- Filtros para la tabla `promociones`
--
ALTER TABLE `promociones`
  ADD CONSTRAINT `promociones_ibfk_1` FOREIGN KEY (`codLocal`) REFERENCES `locales` (`codLocal`);

--
-- Filtros para la tabla `solicitudes_descuentos`
--
ALTER TABLE `solicitudes_descuentos`
  ADD CONSTRAINT `solicitudes_descuentos_ibfk_1` FOREIGN KEY (`codCliente`) REFERENCES `usuarios` (`codUsuario`),
  ADD CONSTRAINT `solicitudes_descuentos_ibfk_2` FOREIGN KEY (`codPromo`) REFERENCES `promociones` (`codPromo`);

--
-- Filtros para la tabla `uso_promociones`
--
ALTER TABLE `uso_promociones`
  ADD CONSTRAINT `uso_promociones_ibfk_1` FOREIGN KEY (`codCliente`) REFERENCES `usuarios` (`codUsuario`),
  ADD CONSTRAINT `uso_promociones_ibfk_2` FOREIGN KEY (`codPromo`) REFERENCES `promociones` (`codPromo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
