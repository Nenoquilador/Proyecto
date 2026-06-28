-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-06-2026 a las 05:50:02
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
(2, 'Admin Vinculación UJS', 'admin@ujsierra.com.mx', '$2y$10$7U07nqal1.zQqqdSOGdJK.FtZCUWuBd8ZXkYQMBs15Ffty2WFX1R2', '2025-10-29 11:04:18', 'vinculacion'),
(3, 'Servicios Escolares', 'escolares@ujsierra.com.mx', '$2y$10$7U07nqal1.zQqqdSOGdJK.FtZCUWuBd8ZXkYQMBs15Ffty2WFX1R2', '2026-05-11 03:16:00', 'alumnos');

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
(1, 'Ernesto', 'Gómez Romero', 'ernestogomez@ujsierra.com.mx', '$2y$10$D1ShPO5fZxWYDdbmq8ByEunt1RU5cyvpB75TO87GB6StlItPScyqe', '221188', 'sistemas', 7, '1_1761803238_cv_1_currculum3_1761428426__1_.pdf', 'https://www.linkedin.com/in/ernesto-g%C3%B3mez-romero-53938234a/', '2025-10-29 08:28:05'),
(2, 'luis ', 'leonel quiroz ', 'luisquiroz@ujsierra.com.mx', '$2y$10$ewEaQ04HpuunG8PLBDxKC.xVHPeSkxYH9Ivp/CzkyCtnv4lUyv25m', '231795', 'sistemas', 7, NULL, '', '2026-01-08 08:02:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `id_empresa` int(11) NOT NULL,
  `nombre_empresa` varchar(255) NOT NULL,
  `carreras_afines` text DEFAULT NULL,
  `email_contacto` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rfc` varchar(13) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `sitio_web` varchar(255) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `estado_validacion` enum('pendiente','aprobada','rechazada') DEFAULT 'pendiente',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `es_catalogo_sspp` tinyint(1) DEFAULT 0,
  `vigencia_sspp` date DEFAULT NULL,
  `banner_url` varchar(255) DEFAULT NULL,
  `notas_internas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id_empresa`, `nombre_empresa`, `carreras_afines`, `email_contacto`, `password`, `rfc`, `descripcion`, `sitio_web`, `logo_url`, `estado_validacion`, `fecha_registro`, `es_catalogo_sspp`, `vigencia_sspp`, `banner_url`, `notas_internas`) VALUES
(1, 'TechSolutions Innova', NULL, 'techsolutions.innova@ejemplo.com', '$2y$10$4NQLiDidj6CLLMD.xJ4lB.XETAyTPFwwqxwTwj9EChJJ3uJDkkNYC', NULL, 'TechSolutions Innova es una empresa dedicada a ofrecer soluciones tecnológicas de vanguardia...', '', NULL, 'aprobada', '2025-10-29 11:42:41', 1, '2029-05-10', NULL, NULL),
(2, 'Soluciones Corporativas Vértice S.A. de C.V.', NULL, 'reclutamiento@grupovertice.mx', '$2y$10$YxFKK1g2Yamu1zSa7jkWwukC82PRYAObLifONHHCSLz0jaw6yDhjm', NULL, 'Soluciones Corporativas Vértice es una firma líder en consultoría tecnológica...', '', NULL, 'rechazada', '2026-01-08 08:08:50', 0, NULL, NULL, NULL),
(3, 'Cisco', NULL, 'cisco@gmail.com', '$2y$10$xpayc43sDfr3Y8dOqpHYD.raPqmcbH/hBDxKST.w31euGW8SefyY2', NULL, 'Trabajo', '', NULL, 'aprobada', '2026-01-08 22:12:07', 1, '2029-05-10', NULL, NULL),
(8, '123456', NULL, '123456@gmail.com', '$2y$10$YLsyG.Zxsok72QgPVmHuuOT7GkUyg11eomNfFKuOUPyNz62c4Wywa', NULL, '1234', '', NULL, 'aprobada', '2026-05-10 12:29:55', 1, '2029-06-09', NULL, NULL),
(99, 'SecureNet Latam', NULL, 'talento@securenetlatam.com', '$2y$10$clLGBv2WvAyqPPOUMBLqse4AU3tVovQpBbKuJhWdyvR/Ya67bj61a', NULL, 'Firma consultora especializada en ciberseguridad, auditoría de redes y protección de datos. Nos dedicamos a mitigar riesgos digitales y certificar infraestructuras tecnológicas para corporativos. Buscamos perfiles entusiastas en tecnologías de la información, redes y desarrollo web para integrarse a nuestros proyectos de seguridad y soporte técnico.', 'https://www.securenetlatam.com', NULL, 'pendiente', '2026-06-09 15:21:44', 0, NULL, NULL, NULL),
(100, 'justosierra', '[]', 'justosierra@gmail.com', '$2y$10$14R0.BjaG5YTzXTV0jzKFOQDK0/HkI4IHf8jua3/75Rxgk.iUhkGG', NULL, 'jhkjhfedgs', '', NULL, 'aprobada', '2026-06-12 15:45:01', 0, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `postulaciones`
--

CREATE TABLE `postulaciones` (
  `id_postulacion` int(11) NOT NULL,
  `id_alumno` int(11) NOT NULL,
  `id_vacante` int(11) NOT NULL,
  `fecha_postulacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado_postulacion` enum('enviada','vista','en_proceso','rechazada') DEFAULT 'enviada',
  `notas_empresa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `postulaciones`
--

INSERT INTO `postulaciones` (`id_postulacion`, `id_alumno`, `id_vacante`, `fecha_postulacion`, `estado_postulacion`, `notas_empresa`) VALUES
(1, 1, 3, '2025-10-29 13:21:39', 'en_proceso', NULL),
(2, 2, 2, '2026-01-08 08:04:07', 'enviada', NULL),
(3, 2, 3, '2026-01-08 08:15:22', 'vista', NULL),
(4, 1, 4, '2026-01-08 22:13:56', 'rechazada', NULL),
(5, 1, 2, '2026-03-13 08:57:07', 'enviada', NULL);

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
(99, 99, 'Validado por Teléfono', '2026-06-09', '2026-06-22', NULL, NULL, NULL, 'archivos_sspp/formatos_empresas/1781019231_SSPP_FORMATO REGISTRO EMPRESA SS Y PP (2).docx'),
(100, 100, 'Validado por Teléfono', '2026-06-12', '2026-06-12', NULL, NULL, NULL, 'archivos_sspp/formatos_empresas/7c2c1484a0bca9c3e7dfe42eec86b2f822842c166ab6d2a6901f84548f435ced.docx');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramites_servicio_social`
--

CREATE TABLE `tramites_servicio_social` (
  `id_tramite` int(11) NOT NULL,
  `id_alumno` int(11) NOT NULL,
  `id_postulacion` int(11) NOT NULL,
  `estado_tramite` enum('solicitud_creditos','pago_validado_escolares','etapa_1_iniciada','etapa_1_documentos_entregados','etapa_2_liberacion','finalizado','cancelado') DEFAULT 'solicitud_creditos',
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_pago_validado` datetime DEFAULT NULL,
  
  -- Datos Carta Presentacion / Creditos
  `empresa_nombre` varchar(255) DEFAULT NULL,
  `dirigido_a` varchar(255) DEFAULT NULL,
  `cargo_dirigido` varchar(255) DEFAULT NULL,

  -- Datos Etapa 1
  `avance_porcentaje` varchar(10) DEFAULT NULL,
  `domicilio` text DEFAULT NULL,
  `telefonos` varchar(100) DEFAULT NULL,
  `programa_ss` varchar(255) DEFAULT NULL,
  `duracion_ss` varchar(100) DEFAULT NULL,
  `tareas_especificas` text DEFAULT NULL,
  `apoyo_economico` varchar(100) DEFAULT NULL,
  `archivo_carta_aceptacion` varchar(255) DEFAULT NULL,

  -- Datos Etapa 2
  `evaluacion_empresa_amabilidad` int(11) DEFAULT NULL,
  `evaluacion_empresa_ambiente` int(11) DEFAULT NULL,
  `plantel_tramite` varchar(100) DEFAULT NULL,
  `archivo_evaluacion_desempeno` varchar(255) DEFAULT NULL,
  `archivo_reporte_global` varchar(255) DEFAULT NULL,
  `archivo_carta_terminacion` varchar(255) DEFAULT NULL,
  
  `comentarios_escolares` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

INSERT INTO `vacantes` (`id_vacante`, `id_empresa`, `titulo`, `descripcion`, `tipo_contrato`, `modalidad`, `salario_ofrecido`, `carrera_afin`, `ubicacion`, `estado`, `fecha_publicacion`) VALUES
(1, 1, 'Desarrollador(a) Backend Junior', 'Buscamos un recién egresado o estudiante avanzado con conocimientos sólidos en PHP y MySQL...', 'tiempo_completo', 'hibrido', NULL, NULL, 'Ciudad de México, CDMX', 'abierta', '2025-10-29 12:25:18'),
(2, 1, 'Practicante de Diseño UX/UI', 'Oportunidad para estudiantes de Diseño o carreras afines...', 'practicas', 'remoto', NULL, NULL, 'Remoto (Nacional)', 'abierta', '2025-10-29 12:54:36'),
(3, 1, 'Analista de Soporte Técnico', 'Responsable de brindar soporte técnico de primer nivel...', 'medio_tiempo', 'presencial', NULL, NULL, 'Campus Sur, CDMX', 'abierta', '2025-10-29 12:55:11'),
(4, 3, 'Redes', 'trabajo', 'tiempo_completo', 'presencial', NULL, NULL, 'cdmx', 'abierta', '2026-01-08 22:13:43'),
(5, 1, 'Desarollador PHP', 'hasjdkhasdw3adadq', 'medio_tiempo', 'hibrido', NULL, NULL, 'Híbrido (Oficina en CDMX / Remoto)', 'abierta', '2026-03-13 09:16:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vistas_vacantes`
--

CREATE TABLE `vistas_vacantes` (
  `id` int(11) NOT NULL,
  `vacante_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `fecha_vista` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id_alumno`);

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id_empresa`),
  ADD KEY `idx_estado_validacion` (`estado_validacion`),
  ADD KEY `idx_es_catalogo_sspp` (`es_catalogo_sspp`);

--
-- Indices de la tabla `postulaciones`
--
ALTER TABLE `postulaciones`
  ADD PRIMARY KEY (`id_postulacion`),
  ADD KEY `idx_id_vacante` (`id_vacante`),
  ADD KEY `idx_id_alumno` (`id_alumno`);

--
-- Indices de la tabla `solicitudes_sspp`
--
ALTER TABLE `solicitudes_sspp`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `idx_id_empresa_sspp` (`id_empresa`),
  ADD KEY `idx_estado_tramite` (`estado_tramite`);

--
-- Indices de la tabla `tramites_servicio_social`
--
ALTER TABLE `tramites_servicio_social`
  ADD PRIMARY KEY (`id_tramite`),
  ADD KEY `idx_id_alumno_ss` (`id_alumno`),
  ADD KEY `idx_id_postulacion_ss` (`id_postulacion`);

--
-- Indices de la tabla `vacantes`
--
ALTER TABLE `vacantes`
  ADD PRIMARY KEY (`id_vacante`),
  ADD KEY `idx_id_empresa` (`id_empresa`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `vistas_vacantes`
--
ALTER TABLE `vistas_vacantes`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de la tabla `postulaciones`
--
ALTER TABLE `postulaciones`
  MODIFY `id_postulacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `solicitudes_sspp`
--
ALTER TABLE `solicitudes_sspp`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de la tabla `tramites_servicio_social`
--
ALTER TABLE `tramites_servicio_social`
  MODIFY `id_tramite` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `vacantes`
--
ALTER TABLE `vacantes`
  MODIFY `id_vacante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `vistas_vacantes`
--
ALTER TABLE `vistas_vacantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;