-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-05-2026 a las 00:10:53
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
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo_admin` enum('vinculacion','alumnos') NOT NULL DEFAULT 'vinculacion'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id_admin`, `nombre`, `email`, `password`, `fecha_creacion`, `tipo_admin`) VALUES
(2, 'Admin Vinculación UJS', 'admin@ujsierra.com.mx', '$2y$10$b5B0pE1j9vK.h0d8F0U.0J9.S9.V9u9v6Y5i9s5k5O5r5d5V5j1I.', '2025-10-29 05:04:18', 'vinculacion'),
(3, 'Servicios Escolares', 'escolares@ujsierra.com.mx', '123456...', '2026-05-10 21:16:00', 'alumnos');

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
(1, 'Ernesto', 'Gómez Romero', 'ernestogomez@ujsierra.com.mx', '$2y$10$D1ShPO5fZxWYDdbmq8ByEunt1RU5cyvpB75TO87GB6StlItPScyqe', '221188', 'sistemas', 7, '1_1761803238_cv_1_currculum3_1761428426__1_.pdf', 'https://www.linkedin.com/in/ernesto-g%C3%B3mez-romero-53938234a/', '2025-10-29 02:28:05'),
(2, 'luis ', 'leonel quiroz ', 'luisquiroz@ujsierra.com.mx', '$2y$10$ewEaQ04HpuunG8PLBDxKC.xVHPeSkxYH9Ivp/CzkyCtnv4lUyv25m', '231795', 'sistemas', 7, NULL, '', '2026-01-08 02:02:57');

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
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `es_catalogo_sspp` tinyint(1) DEFAULT 0,
  `vigencia_sspp` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id_empresa`, `nombre_empresa`, `email_contacto`, `password`, `rfc`, `descripcion`, `sitio_web`, `logo_url`, `estado_validacion`, `fecha_registro`, `es_catalogo_sspp`, `vigencia_sspp`) VALUES
(1, 'TechSolutions Innova', 'techsolutions.innova@ejemplo.com', '$2y$10$4NQLiDidj6CLLMD.xJ4lB.XETAyTPFwwqxwTwj9EChJJ3uJDkkNYC', NULL, 'TechSolutions Innova es una empresa dedicada a ofrecer soluciones tecnológicas de vanguardia. Nos especializamos en el desarrollo de software personalizado, consultoría IT y servicios de integración de sistemas, ayudando a nuestros clientes a optimizar sus procesos y a impulsar la transformación digital de sus negocios. Nuestro enfoque está en la innovación, la eficiencia y la calidad de servicio.', '', NULL, 'aprobada', '2025-10-29 05:42:41', 1, '2029-05-10'),
(2, 'Soluciones Corporativas Vértice S.A. de C.V.', 'reclutamiento@grupovertice.mx', '$2y$10$YxFKK1g2Yamu1zSa7jkWwukC82PRYAObLifONHHCSLz0jaw6yDhjm', NULL, 'Soluciones Corporativas Vértice es una firma líder en consultoría tecnológica y gestión empresarial con más de 10 años de experiencia en el mercado mexicano. Nos especializamos en el desarrollo de software a medida, automatización de procesos y estrategias de transformación digital para el sector financiero y educativo. Estamos comprometidos con la innovación y buscamos integrar talento joven y proactivo de la institución Justo Sierra para formar parte de nuestros programas de prácticas profesio', '', NULL, 'rechazada', '2026-01-08 02:08:50', 0, NULL),
(3, 'Cisco', 'cisco@gmail.com', '$2y$10$xpayc43sDfr3Y8dOqpHYD.raPqmcbH/hBDxKST.w31euGW8SefyY2', NULL, 'Trabajo', '', NULL, 'aprobada', '2026-01-08 16:12:07', 1, '2029-05-10'),
(8, '123456', '123456@gmail.com', '$2y$10$YLsyG.Zxsok72QgPVmHuuOT7GkUyg11eomNfFKuOUPyNz62c4Wywa', NULL, '1234', '', NULL, 'aprobada', '2026-05-10 06:29:55', 1, '2029-05-10');

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
(1, 1, 3, '2025-10-29 07:21:39', 'en_proceso'),
(2, 2, 2, '2026-01-08 02:04:07', 'enviada'),
(3, 2, 3, '2026-01-08 02:15:22', 'vista'),
(4, 1, 4, '2026-01-08 16:13:56', 'rechazada'),
(5, 1, 2, '2026-03-13 02:57:07', 'enviada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_sspp`
--

CREATE TABLE `solicitudes_sspp` (
  `id_solicitud` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `estado_tramite` enum('Solicitud Inicial','Formato Enviado','Datos Recibidos','Validado por Teléfono','Aprobado Catálogo','Expirado') DEFAULT 'Solicitud Inicial',
  `fecha_inicio` date NOT NULL,
  `fecha_validacion` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `archivo_solicitud_dir` varchar(255) DEFAULT NULL COMMENT 'Ruta del archivo de Dirección',
  `archivo_catalogo_generado` varchar(255) DEFAULT NULL COMMENT 'Ruta del PDF final',
  `notas_admin` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes_sspp`
--

INSERT INTO `solicitudes_sspp` (`id_solicitud`, `id_empresa`, `estado_tramite`, `fecha_inicio`, `fecha_validacion`, `fecha_vencimiento`, `archivo_solicitud_dir`, `archivo_catalogo_generado`, `notas_admin`) VALUES
(2, 8, 'Validado por Teléfono', '2026-05-10', '2026-05-10', NULL, NULL, NULL, 'archivos_sspp/formatos_empresas/1778395101_SSPP_FORMATO REGISTRO EMPRESA SS Y PP.docx');

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
  `carrera_afin` varchar(100) DEFAULT NULL,
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
(3, 1, 'Analista de Soporte Técnico', 'Responsable de brindar soporte técnico de primer nivel (Help Desk) al personal interno, incluyendo hardware, software y problemas de red básicos. Requisitos: Excelente manejo de Windows/Linux y habilidades de comunicación. Horario flexible adaptable a estudiantes.', 'medio_tiempo', 'presencial', NULL, 'Campus Sur, CDMX', 'abierta', '2025-10-29 06:55:11'),
(4, 3, 'Redes', 'trabajo', 'tiempo_completo', 'presencial', NULL, 'cdmx', 'abierta', '2026-01-08 16:13:43'),
(5, 1, 'Desarollador PHP', 'hasjdkhasdw3adadq', 'medio_tiempo', 'hibrido', NULL, 'Híbrido (Oficina en CDMX / Remoto)', 'abierta', '2026-03-13 03:16:43');

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
-- Indices de la tabla `solicitudes_sspp`
--
ALTER TABLE `solicitudes_sspp`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `fk_solicitud_empresa` (`id_empresa`);

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
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id_alumno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `postulaciones`
--
ALTER TABLE `postulaciones`
  MODIFY `id_postulacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `solicitudes_sspp`
--
ALTER TABLE `solicitudes_sspp`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `vacantes`
--
ALTER TABLE `vacantes`
  MODIFY `id_vacante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `postulaciones`
--
ALTER TABLE `postulaciones`
  ADD CONSTRAINT `postulaciones_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  ADD CONSTRAINT `postulaciones_ibfk_2` FOREIGN KEY (`id_vacante`) REFERENCES `vacantes` (`id_vacante`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitudes_sspp`
--
ALTER TABLE `solicitudes_sspp`
  ADD CONSTRAINT `fk_solicitud_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `vacantes`
--
ALTER TABLE `vacantes`
  ADD CONSTRAINT `vacantes_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
