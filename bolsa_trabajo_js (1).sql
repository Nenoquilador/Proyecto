-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-12-2025 a las 04:39:46
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
-- Base de datos: `bolsa_trabajo_js`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id_admin` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id_admin`, `nombre`, `email`, `password`, `fecha_creacion`) VALUES
(1, 'Admin Central UJS', 'ujs.admin@mail.com', '$2y$10$b5B0pE1j9vK.h0d8F0U.0J9.S9.V9u9v6Y5i9s5k5O5r5d5V5j1I.', '2025-10-29 04:55:37'),
(2, 'Admin Vinculación UJS', 'admin@ujsierra.com.mx', '$2y$10$b5B0pE1j9vK.h0d8F0U.0J9.S9.V9u9v6Y5i9s5k5O5r5d5V5j1I.', '2025-10-29 05:04:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id_alumno` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(150) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `carrera` varchar(100) DEFAULT NULL,
  `semestre` int(11) DEFAULT NULL,
  `cv_url` varchar(255) DEFAULT NULL,
  `perfil_linkedin` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id_alumno`, `nombre`, `apellidos`, `email`, `password`, `matricula`, `carrera`, `semestre`, `cv_url`, `perfil_linkedin`, `fecha_registro`) VALUES
(1, 'Ernesto', 'Gómez Romero', 'ernestogomez@ujsierra.com.mx', '$2y$10$D1ShPO5fZxWYDdbmq8ByEunt1RU5cyvpB75TO87GB6StlItPScyqe', '221188', 'sistemas', 7, '1_1761803238_cv_1_currculum3_1761428426__1_.pdf', 'https://www.linkedin.com/in/ernesto-g%C3%B3mez-romero-53938234a/', '2025-10-29 02:28:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `id_empresa` int(11) NOT NULL,
  `nombre_empresa` varchar(255) NOT NULL,
  `email_contacto` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rfc` varchar(13) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `sitio_web` varchar(255) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `estado_validacion` enum('pendiente','aprobada','rechazada') DEFAULT 'pendiente',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id_empresa`, `nombre_empresa`, `email_contacto`, `password`, `rfc`, `descripcion`, `sitio_web`, `logo_url`, `estado_validacion`, `fecha_registro`) VALUES
(1, 'TechSolutions Innova', 'techsolutions.innova@ejemplo.com', '$2y$10$4NQLiDidj6CLLMD.xJ4lB.XETAyTPFwwqxwTwj9EChJJ3uJDkkNYC', NULL, 'TechSolutions Innova es una empresa dedicada a ofrecer soluciones tecnológicas de vanguardia. Nos especializamos en el desarrollo de software personalizado, consultoría IT y servicios de integración de sistemas, ayudando a nuestros clientes a optimizar sus procesos y a impulsar la transformación digital de sus negocios. Nuestro enfoque está en la innovación, la eficiencia y la calidad de servicio.', '', NULL, 'aprobada', '2025-10-29 05:42:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `postulaciones`
--

CREATE TABLE `postulaciones` (
  `id_postulacion` int(11) NOT NULL,
  `id_alumno` int(11) NOT NULL,
  `id_vacante` int(11) NOT NULL,
  `fecha_postulacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado_postulacion` enum('enviada','vista','en_proceso','rechazada') DEFAULT 'enviada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `postulaciones`
--

INSERT INTO `postulaciones` (`id_postulacion`, `id_alumno`, `id_vacante`, `fecha_postulacion`, `estado_postulacion`) VALUES
(1, 1, 3, '2025-10-29 07:21:39', 'enviada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacantes`
--

CREATE TABLE `vacantes` (
  `id_vacante` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `tipo_contrato` enum('tiempo_completo','medio_tiempo','practicas','temporal','proyecto') NOT NULL,
  `modalidad` enum('presencial','remoto','hibrido') NOT NULL,
  `salario_ofrecido` decimal(10,2) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `estado` enum('abierta','cerrada') DEFAULT 'abierta',
  `fecha_publicacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vacantes`
--

INSERT INTO `vacantes` (`id_vacante`, `id_empresa`, `titulo`, `descripcion`, `tipo_contrato`, `modalidad`, `salario_ofrecido`, `ubicacion`, `estado`, `fecha_publicacion`) VALUES
(1, 1, 'Desarrollador(a) Backend Junior', 'Buscamos un recién egresado o estudiante avanzado con conocimientos sólidos en PHP y MySQL para apoyar en el desarrollo y mantenimiento de APIs internas. Deberá colaborar con el equipo de frontend y asegurar la optimización del código. Se requiere manejo de bases de datos relacionales y Git.', 'tiempo_completo', 'hibrido', NULL, 'Ciudad de México, CDMX', 'abierta', '2025-10-29 06:25:18'),
(2, 1, 'Practicante de Diseño UX/UI', 'Oportunidad para estudiantes de Diseño o carreras afines. Aprenderás a crear wireframes, prototipos y diseños de alta fidelidad utilizando Figma. Necesario conocimiento básico de principios de usabilidad (UX) y tener un portafolio de proyectos personales (incluso académicos).', 'practicas', 'remoto', NULL, 'Remoto (Nacional)', 'abierta', '2025-10-29 06:54:36'),
(3, 1, 'Analista de Soporte Técnico', 'Responsable de brindar soporte técnico de primer nivel (Help Desk) al personal interno, incluyendo hardware, software y problemas de red básicos. Requisitos: Excelente manejo de Windows/Linux y habilidades de comunicación. Horario flexible adaptable a estudiantes.', 'medio_tiempo', 'presencial', NULL, 'Campus Sur, CDMX', 'abierta', '2025-10-29 06:55:11');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id_alumno`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `matricula` (`matricula`);

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id_empresa`),
  ADD UNIQUE KEY `email_contacto` (`email_contacto`),
  ADD UNIQUE KEY `rfc` (`rfc`);

--
-- Indices de la tabla `postulaciones`
--
ALTER TABLE `postulaciones`
  ADD PRIMARY KEY (`id_postulacion`),
  ADD UNIQUE KEY `uk_alumno_vacante` (`id_alumno`,`id_vacante`),
  ADD KEY `id_vacante` (`id_vacante`);

--
-- Indices de la tabla `vacantes`
--
ALTER TABLE `vacantes`
  ADD PRIMARY KEY (`id_vacante`),
  ADD KEY `id_empresa` (`id_empresa`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id_alumno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `postulaciones`
--
ALTER TABLE `postulaciones`
  MODIFY `id_postulacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `vacantes`
--
ALTER TABLE `vacantes`
  MODIFY `id_vacante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `postulaciones`
--
ALTER TABLE `postulaciones`
  ADD CONSTRAINT `postulaciones_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`),
  ADD CONSTRAINT `postulaciones_ibfk_2` FOREIGN KEY (`id_vacante`) REFERENCES `vacantes` (`id_vacante`);

--
-- Filtros para la tabla `vacantes`
--
ALTER TABLE `vacantes`
  ADD CONSTRAINT `vacantes_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
