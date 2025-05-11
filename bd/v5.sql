-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-05-2025 a las 20:44:43
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
-- Base de datos: `v4`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas`
--

CREATE TABLE `cuentas` (
  `id` int(11) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `contrasena_correo` varchar(255) NOT NULL,
  `contrasena_gpt` varchar(255) NOT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `dias` int(11) DEFAULT NULL,
  `usuarios` int(11) DEFAULT 0,
  `ganancia` decimal(10,2) DEFAULT NULL,
  `costo` decimal(10,2) NOT NULL,
  `estado` enum('activa','inactiva','suspendida','baneada') DEFAULT 'activa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuentas`
--

INSERT INTO `cuentas` (`id`, `correo`, `contrasena_correo`, `contrasena_gpt`, `codigo`, `fecha_inicio`, `fecha_fin`, `dias`, `usuarios`, `ganancia`, `costo`, `estado`, `created_at`, `updated_at`) VALUES
(17, 'laurad3@outlook.es', 'Rv4jL0sW', 'Syf3ebbfd3fasS', 'G73KEA1TP6QYYJ85TGF6LSB', '2025-04-14', '2025-05-14', NULL, 0, NULL, 320.62, 'activa', '2025-05-11 18:37:02', '2025-05-11 18:37:02'),
(18, 'epcilo2@outlook.com', 'V8U#L2S3', 'asdf3211853saS', 'EJE53TZXM9JRNM95W4GCSL3A', '2025-04-21', '2025-05-21', NULL, 0, NULL, 320.62, 'activa', '2025-05-11 18:37:02', '2025-05-11 18:37:02'),
(19, 'reslm13@outlook.com', 'asdf43fFS', 'asdf7f34nSLSfnS', 'WP8BG49EK7UNPNJKXZBJJ757', '2025-04-29', '2025-05-29', NULL, 0, NULL, 320.62, 'activa', '2025-05-11 18:37:02', '2025-05-11 18:37:02'),
(20, 'ramone2y@outlook.com', 'HSF78jgS81', 'nbv23af5vb123', 'KCD721GJWTJAGDX9ZVWTUC7N', '2025-05-06', '2025-06-05', NULL, 0, NULL, 139.40, 'activa', '2025-05-11 18:37:02', '2025-05-11 18:37:02'),
(21, 'badd2m@outlook.es', 'V8U#L2S4', 'q346sdfhDggs', 'ASR8MDGMN6MVVEH6SD9ZGV97', '2025-05-10', '2025-06-09', NULL, 0, NULL, 139.40, 'activa', '2025-05-11 18:37:02', '2025-05-11 18:37:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `extensiones_ventas`
--

CREATE TABLE `extensiones_ventas` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `dias_adicionales` int(11) NOT NULL,
  `vendedor_id` int(11) NOT NULL,
  `fecha_extension` datetime DEFAULT current_timestamp(),
  `motivo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vendedores`
--

CREATE TABLE `vendedores` (
  `id` int(11) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vendedores`
--

INSERT INTO `vendedores` (`id`, `usuario`, `contrasena`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$u7y5rL6f9O8QzT8wRz1Q9eQZpQ4sO1kW8cQ2tU7wG5yK3wA1xYzQ9e', 1, '2025-05-07 16:40:23', '2025-05-07 16:40:23'),
(2, 'juan', '$2y$10$QW8hJ4kL9u7eT8rF6gH5sO2lP3qW1vN2bC4xZ8aS6dF7jH3kL5nM1b', 1, '2025-05-07 16:40:23', '2025-05-07 16:40:23'),
(3, 'maria', '$2y$10$Z1xY2wV3uT4rS5qP6oN7mL8kJ9hG0fE1dC2bA3zX4yW5vU6tR7sQ8p', 1, '2025-05-07 16:40:23', '2025-05-07 16:40:23'),
(4, 'jimi', '$2y$10$w8npR/p6MQFy2C7yk6Is7.W8lxuGxSGycSfOTLQ8ntTVu8yLZxY4W', 1, '2025-05-08 13:13:37', '2025-05-08 13:16:32'),
(5, '', '$2y$10$SY0prLj9Finr6i69MfThDOexZq20hJH/wJj7Ai14m77z2ZrjtQGz.', 1, '2025-05-08 13:43:29', '2025-05-08 13:43:29'),
(6, 'joe', '$2y$10$UlB.2TDdDBfWh7EqMwXlP.HnZ3Qfo1dNhM4UozZk42OKlIHjAZs12', 1, '2025-05-08 13:47:13', '2025-05-08 13:47:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `numero_celular` varchar(20) NOT NULL,
  `vendedor_id` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `pago` decimal(10,2) NOT NULL,
  `cuenta_id` int(11) NOT NULL,
  `dias` int(11) GENERATED ALWAYS AS (to_days(`fecha_fin`) - to_days(`fecha_inicio`)) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `numero_celular`, `vendedor_id`, `fecha_inicio`, `fecha_fin`, `pago`, `cuenta_id`, `created_at`, `updated_at`) VALUES
(152, '67463558', 6, '2025-04-21', '2025-05-21', 26.00, 18, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(153, '7378399', 6, '2025-04-21', '2025-05-21', 26.00, 18, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(154, '67439775', 6, '2025-04-21', '2025-05-21', 26.00, 18, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(155, '7309505', 6, '2025-04-22', '2025-05-22', 26.00, 18, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(156, '67716220', 6, '2025-04-22', '2025-05-22', 26.00, 18, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(157, '70540633', 6, '2025-04-23', '2025-05-23', 26.00, 18, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(158, '75211000', 6, '2025-04-23', '2025-05-23', 26.00, 18, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(159, '75481935', 6, '2025-04-23', '2025-05-23', 26.00, 18, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(160, '993 654 867', 6, '2025-04-24', '2025-05-24', 26.00, 18, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(161, '71133396', 6, '2025-04-25', '2025-05-25', 26.00, 18, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(162, '62220187', 6, '2025-04-25', '2025-05-25', 26.00, 18, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(163, '64348782', 6, '2025-04-25', '2025-05-25', 26.00, 18, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(164, '75479287', 6, '2025-04-25', '2025-05-25', 26.00, 18, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(165, '64067179', 6, '2025-04-26', '2025-05-26', 26.00, 18, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(166, '74018766', 6, '2025-04-28', '2025-05-28', 26.00, 18, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(167, '64050690', 6, '2025-04-28', '2025-05-28', 26.00, 18, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(168, '71865804', 6, '2025-04-21', '2025-07-20', 75.00, 18, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(169, '51 906 633 634', 6, '2025-04-29', '2025-05-29', 26.00, 19, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(170, '76589792', 6, '2025-04-29', '2025-05-29', 26.00, 19, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(171, '73880125', 6, '2025-04-29', '2025-05-29', 26.00, 19, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(172, '70747702', 6, '2025-04-29', '2025-05-29', 26.00, 19, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(173, '67051761', 6, '2025-04-29', '2025-05-29', 26.00, 19, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(174, '73432052', 6, '2025-04-30', '2025-05-30', 26.00, 19, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(175, '79149988', 6, '2025-04-30', '2025-05-30', 26.00, 19, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(176, '61844323', 6, '2025-04-30', '2025-05-30', 26.00, 19, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(177, '72137522', 6, '2025-05-02', '2025-06-01', 26.00, 19, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(178, '68613321', 6, '2025-05-02', '2025-06-01', 26.00, 19, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(179, '77437339', 6, '2025-05-02', '2025-06-01', 26.00, 19, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(180, '62639339', 6, '2025-05-04', '2025-06-03', 26.00, 19, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(181, '60790425', 6, '2025-05-04', '2025-06-03', 26.00, 19, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(182, '74064047', 6, '2025-05-04', '2025-06-03', 26.00, 19, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(183, '74218123', 6, '2025-05-05', '2025-06-04', 26.00, 19, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(184, '73767500', 6, '2025-05-05', '2025-06-04', 26.00, 19, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(185, '63138586', 6, '2025-05-02', '2025-07-31', 90.00, 19, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(186, '77718989', 6, '2025-05-07', '2025-08-05', 26.00, 20, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(187, '75189627', 6, '2025-05-06', '2025-08-04', 26.00, 20, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(188, '76426052', 6, '2025-05-07', '2025-06-06', 26.00, 20, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(189, '74836834', 6, '2025-05-07', '2025-06-06', 26.00, 20, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(190, '75986349', 6, '2025-05-08', '2025-06-07', 26.00, 20, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(191, '79382493', 6, '2025-05-09', '2025-06-08', 26.00, 20, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(192, '71224961', 6, '2025-05-06', '2025-06-05', 26.00, 20, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(193, '60752451', 6, '2025-05-06', '2025-06-05', 26.00, 20, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(194, '68612334', 6, '2025-05-07', '2025-06-06', 26.00, 20, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(195, '77012491', 6, '2025-05-07', '2025-06-06', 26.00, 20, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(196, '61214567', 6, '2025-05-07', '2025-06-06', 26.00, 20, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(197, '74541516', 6, '2025-05-08', '2025-06-07', 26.00, 20, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(198, '60642928', 6, '2025-05-08', '2025-06-07', 26.00, 20, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(199, '69414168', 6, '2025-05-08', '2025-06-07', 26.00, 20, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(200, '65334767', 6, '2025-05-09', '2025-06-08', 26.00, 20, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(201, '65034450', 6, '2025-05-10', '2025-06-09', 26.00, 20, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(202, '62742536', 6, '2025-05-10', '2025-06-09', 26.00, 20, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(203, '70363060', 6, '2025-05-10', '2025-06-09', 26.00, 20, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(204, '76435463', 6, '2025-05-10', '2025-06-09', 26.00, 21, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(205, '61606167', 6, '2025-05-10', '2025-06-09', 26.00, 21, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(206, '72301708', 6, '2025-05-10', '2025-08-08', 26.00, 21, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(207, '79969975', 6, '2025-05-10', '2025-06-09', 26.00, 21, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(208, '63186663', 6, '2025-05-10', '2025-08-08', 26.00, 21, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(209, '76956452', 6, '2025-05-10', '2025-06-09', 26.00, 21, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(210, '78918847', 6, '2025-05-11', '2025-06-10', 26.00, 21, '2025-05-11 18:42:20', '2025-05-11 18:42:20'),
(211, '77876123', 6, '2025-05-10', '2025-06-09', 26.00, 21, '2025-05-11 18:42:20', '2025-05-11 18:42:20');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `idx_cuentas_estado` (`estado`);

--
-- Indices de la tabla `extensiones_ventas`
--
ALTER TABLE `extensiones_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `vendedor_id` (`vendedor_id`);

--
-- Indices de la tabla `vendedores`
--
ALTER TABLE `vendedores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cuenta_id` (`cuenta_id`),
  ADD KEY `idx_ventas_vendedor` (`vendedor_id`),
  ADD KEY `idx_ventas_fechas` (`fecha_inicio`,`fecha_fin`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `extensiones_ventas`
--
ALTER TABLE `extensiones_ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `vendedores`
--
ALTER TABLE `vendedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=212;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `extensiones_ventas`
--
ALTER TABLE `extensiones_ventas`
  ADD CONSTRAINT `extensiones_ventas_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `extensiones_ventas_ibfk_2` FOREIGN KEY (`vendedor_id`) REFERENCES `vendedores` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cuenta_id`) REFERENCES `cuentas` (`id`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`vendedor_id`) REFERENCES `vendedores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
