-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-05-2025 a las 23:22:35
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
(1, 'cuenta1@email.com', 'correo123', 'gpt123', 'C001', '2025-05-01', '2025-06-01', 31, 2, 100.00, 50.00, 'activa', '2025-05-07 16:40:23', '2025-05-07 16:40:23'),
(2, 'cuenta2@email.com', 'correo234', 'gpt234', 'C002', '2025-04-15', '2025-05-15', 30, 1, 60.00, 30.00, 'inactiva', '2025-05-07 16:40:23', '2025-05-08 16:10:00'),
(3, 'cuenta3@email.com', 'correo345', 'gpt345', 'C003', '2025-03-10', '2025-04-10', 31, 3, 150.00, 70.00, 'inactiva', '2025-05-07 16:40:23', '2025-05-07 16:40:23'),
(4, 'anius@gmail.com', '23451234', '412341234', '1234123412', '2025-05-07', NULL, NULL, 0, NULL, 12.00, 'activa', '2025-05-08 13:22:10', '2025-05-08 13:22:10'),
(5, 'milinforme@gmail.com', 'asdkfj2897', 'AKSJDBNFLKJBH1', 'asdfasrtf34ga', '2025-05-08', NULL, NULL, 0, NULL, 100.00, 'activa', '2025-05-08 15:43:50', '2025-05-08 16:08:23');

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

--
-- Volcado de datos para la tabla `extensiones_ventas`
--

INSERT INTO `extensiones_ventas` (`id`, `venta_id`, `dias_adicionales`, `vendedor_id`, `fecha_extension`, `motivo`) VALUES
(1, 1, 5, 1, '2025-05-07 12:40:23', 'Extensión por viaje del cliente'),
(2, 2, 3, 2, '2025-05-07 12:40:23', 'Promoción especial de primavera');

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
(1, '987654321', 1, '2025-05-01', '2025-05-31', 40.00, 1, '2025-05-07 16:40:23', '2025-05-07 16:40:23'),
(2, '912345678', 2, '2025-04-16', '2025-05-15', 30.00, 2, '2025-05-07 16:40:23', '2025-05-07 16:40:23'),
(3, '900123456', 3, '2025-03-10', '2025-03-25', 20.00, 3, '2025-05-07 16:40:23', '2025-05-07 16:40:23'),
(7, '74111111', 6, '2025-05-08', '2025-05-27', 21.00, 4, '2025-05-08 16:45:53', '2025-05-08 16:45:53'),
(8, '76555555', 6, '2025-05-08', '2025-05-29', 33.00, 4, '2025-05-08 17:29:08', '2025-05-08 17:29:08');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
