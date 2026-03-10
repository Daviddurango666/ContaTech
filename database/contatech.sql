-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-03-2026 a las 13:34:44
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
-- Base de datos: `contatech`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra_proveedores`
--

CREATE TABLE `compra_proveedores` (
  `cod_producto` int(11) NOT NULL,
  `nombre_producto` int(11) NOT NULL,
  `precio` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id_empleado` int(11) NOT NULL,
  `nombre_empleado` varchar(255) NOT NULL,
  `salario` double NOT NULL,
  `tipo_contrato` varchar(50) NOT NULL,
  `fecha_ingreso` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id_empleado`, `nombre_empleado`, `salario`, `tipo_contrato`, `fecha_ingreso`) VALUES
(1035973271, 'Daniel', 2500000, 'Contrato indefinido', '2025-06-02'),
(1038927151, 'David', 3000000, 'Contrato indefinido', '2025-06-02'),
(1038927666, 'Sebastian Cruz', 2500000, 'Contrato indefinido', '2025-11-26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id_factura` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `id_gasto` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  `categoria` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gastos`
--

INSERT INTO `gastos` (`id_gasto`, `descripcion`, `monto`, `fecha`, `categoria`) VALUES
(1, 'Alquiler', 1000000.00, '2025-05-31', 'alquiler'),
(2, 'Pago de nómina empleado ID: 1035973271', 2500000.00, '2025-06-01', 'sueldos y salarios'),
(3, 'Pago de nómina empleado ID: 1038927151', 3000000.00, '2025-06-02', 'sueldos y salarios'),
(4, 'Pago de nómina empleado ID: 1035973271', 2500000.00, '2025-06-02', 'sueldos y salarios'),
(5, 'Pago de nómina empleado ID: 1038927151', 3000000.00, '2025-06-02', 'sueldos y salarios'),
(6, 'Pago de nómina empleado ID: 1038927151', 3000000.00, '2025-06-04', 'sueldos y salarios'),
(7, 'Pago de nómina empleado ID: 1035973271', 2500000.00, '2025-06-04', 'sueldos y salarios'),
(8, 'Pago de nómina empleado ID: 1038927151', 3000000.00, '2025-06-06', 'sueldos y salarios'),
(9, 'Pago de nómina empleado ID: 1035973271', 2500000.00, '2025-06-13', 'sueldos y salarios'),
(17, 'Pago de nómina empleado ID: 1038927151', 3000000.00, '2026-01-24', 'sueldos y salarios'),
(18, 'Pago de nómina empleado ID: 1035973271', 2500000.00, '2026-01-24', 'sueldos y salarios');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_pagos`
--

CREATE TABLE `historial_pagos` (
  `id_pago` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `fecha_pago` date NOT NULL,
  `salario_bruto` decimal(10,2) NOT NULL,
  `deducciones` decimal(10,2) NOT NULL,
  `salario_neto` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_pagos`
--

INSERT INTO `historial_pagos` (`id_pago`, `id_empleado`, `fecha_pago`, `salario_bruto`, `deducciones`, `salario_neto`) VALUES
(6, 1038927151, '2025-05-30', 3000000.00, 0.00, 3000000.00),
(8, 1035973271, '2025-05-31', 2500000.00, 0.00, 2500000.00),
(10, 1035973271, '2025-06-01', 2500000.00, 0.00, 2500000.00),
(11, 1035973271, '2025-06-01', 2500000.00, 0.00, 2500000.00),
(12, 1038927151, '2025-06-02', 3000000.00, 0.00, 3000000.00),
(13, 1035973271, '2025-06-02', 2500000.00, 0.00, 2500000.00),
(14, 1038927151, '2025-06-02', 3000000.00, 0.00, 3000000.00),
(15, 1038927151, '2025-06-04', 3000000.00, 0.00, 3000000.00),
(16, 1035973271, '2025-06-04', 2500000.00, 0.00, 2500000.00),
(17, 1038927151, '2025-06-06', 3000000.00, 0.00, 3000000.00),
(18, 1035973271, '2025-06-13', 2500000.00, 0.00, 2500000.00),
(19, 1038927151, '2025-08-20', 3000000.00, 0.00, 3000000.00),
(20, 1035973271, '2025-09-02', 2500000.00, 0.00, 2500000.00),
(21, 1038927151, '2025-09-23', 3000000.00, 0.00, 3000000.00),
(22, 1038927151, '2025-10-12', 3000000.00, 0.00, 3000000.00),
(23, 1038927151, '2025-11-03', 3000000.00, 0.00, 3000000.00),
(24, 1038927666, '2025-11-27', 3000000.00, 0.00, 3000000.00),
(25, 1038927666, '2025-11-27', 2500000.00, 0.00, 2500000.00),
(26, 1038927151, '2026-01-24', 3000000.00, 0.00, 3000000.00),
(27, 1035973271, '2026-01-24', 2500000.00, 0.00, 2500000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos`
--

CREATE TABLE `ingresos` (
  `id_ingreso` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  `categoria` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ingresos`
--

INSERT INTO `ingresos` (`id_ingreso`, `descripcion`, `monto`, `fecha`, `categoria`) VALUES
(1, 'Donacion', 1000000.00, '2025-05-31', 'donaciones'),
(2, 'propina', 500000.00, '2025-05-31', 'comisiones'),
(3, 'Ventas', 9000000.00, '2025-05-31', 'ventas'),
(4, 'Donacion', 3000000.00, '2025-06-04', 'comisiones'),
(5, 'Activision', 10000000.00, '2025-06-06', 'donaciones'),
(6, 'propina', 200000.00, '2025-06-12', 'comisiones'),
(7, 'Ventas', 90000000.00, '2025-12-01', 'ventas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `cod_producto` int(11) NOT NULL,
  `nombre_producto` varchar(255) NOT NULL,
  `categoria` varchar(255) NOT NULL,
  `precio_producto` double NOT NULL,
  `cantidad_producto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `cod_producto` int(11) NOT NULL,
  `nombre_producto` varchar(255) NOT NULL,
  `categoria_producto` varchar(255) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`cod_producto`, `nombre_producto`, `categoria_producto`, `precio`, `cantidad`) VALUES
(4, 'Arroz', 'Cereales y granos', 2500.00, 9),
(5, 'Lentejas', 'Cereales y granos', 2300.00, 0),
(7, 'Chocolate', 'Bebidas', 4800.00, 29),
(8, 'Queso', 'Lácteos y Derivados', 9000.00, 1),
(9, 'Arepa', 'Harina', 2500.00, 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id_proveedor` int(11) NOT NULL,
  `nombre_proveedor` varchar(255) NOT NULL,
  `contacto` varchar(255) NOT NULL,
  `condiciones_pago` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `name`, `usuario`, `email`, `password`) VALUES
(98638331, 'jaime', 'jaime', 'jgomezar1@tdea.edu.co', '$2y$10$h4e/xPsAbN7auI9bylvunuzcevnkoyysbnoFPZ38OjKKHWXpFjKSq'),
(1038927151, 'David Durango', 'david_durango06', 'daviddurango666@gmail.com', '$2y$10$V8nmU0vqvl78B.E/I8bGGOkLBToqynK7CMFoGrQjRCsRsWeGrO0qa');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id_empleado`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id_factura`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`id_gasto`);

--
-- Indices de la tabla `historial_pagos`
--
ALTER TABLE `historial_pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `historial_pagos_ibfk_1` (`id_empleado`);

--
-- Indices de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD PRIMARY KEY (`id_ingreso`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`cod_producto`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`cod_producto`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id_factura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id_gasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `historial_pagos`
--
ALTER TABLE `historial_pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `id_ingreso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `cod_producto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `cod_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);

--
-- Filtros para la tabla `historial_pagos`
--
ALTER TABLE `historial_pagos`
  ADD CONSTRAINT `historial_pagos_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id_empleado`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
