-- ============================================================
-- ContaTech - Base de datos FULL relacionada
-- MySQL 5.7+ / MariaDB 10.3+  |  utf8mb4
-- ============================================================
SET NAMES utf8mb4;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET FOREIGN_KEY_CHECKS = 0;
SET time_zone = '+00:00';

CREATE DATABASE IF NOT EXISTS contatech
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE contatech;

-- ============================================================
-- LIMPIEZA (tablas nuevas + residuales del monolito)
-- ============================================================
DROP TABLE IF EXISTS asientos_contables;
DROP TABLE IF EXISTS pagos;
DROP TABLE IF EXISTS facturas_detalle;
DROP TABLE IF EXISTS facturas;
DROP TABLE IF EXISTS ordenes_venta_detalle;
DROP TABLE IF EXISTS ordenes_venta;
DROP TABLE IF EXISTS ordenes_compra_detalle;
DROP TABLE IF EXISTS ordenes_compra;
DROP TABLE IF EXISTS movimientos;
DROP TABLE IF EXISTS metodos_pago;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS proveedores;
DROP TABLE IF EXISTS usuarios;

DROP TABLE IF EXISTS historial_pagos;
DROP TABLE IF EXISTS compra_proveedores;
DROP TABLE IF EXISTS inventario;
DROP TABLE IF EXISTS gastos;
DROP TABLE IF EXISTS ingresos;
DROP TABLE IF EXISTS empleados;
DROP TABLE IF EXISTS usuario;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 1. USUARIOS (núcleo de autenticación y auditoría)
-- ============================================================
CREATE TABLE usuarios (
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre            VARCHAR(150) NOT NULL,
  usuario           VARCHAR(80)  NOT NULL,
  email             VARCHAR(150) NOT NULL,
  password          VARCHAR(255) NOT NULL,
  rol               ENUM(
                      'administrador',
                      'gerente',
                      'jefe',
                      'contable',
                      'soporte',
                      'cajero',
                      'bodeguero'
                    ) NOT NULL DEFAULT 'cajero',
  activo            TINYINT(1)   NOT NULL DEFAULT 1,
  token_reset       VARCHAR(100) DEFAULT NULL,
  token_reset_expira DATETIME    DEFAULT NULL,
  created_at        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_usuarios_usuario (usuario),
  UNIQUE KEY uk_usuarios_email (email),
  KEY idx_usuarios_rol (rol),
  KEY idx_usuarios_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- password se actualiza en install.php a Admin123!
INSERT INTO usuarios (nombre, usuario, email, password, rol, activo) VALUES
('Administrador Sistema', 'admin',     'admin@contatech.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', 1),
('Carlos Gerente',        'gerente',   'gerente@contatech.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'gerente', 1),
('Laura Jefe',            'jefe',      'jefe@contatech.com',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jefe', 1),
('Ana Contable',          'contable',  'contable@contatech.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'contable', 1),
('Pedro Cajero',          'cajero',    'cajero@contatech.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cajero', 1),
('Luis Bodeguero',        'bodeguero', 'bodeguero@contatech.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'bodeguero', 1),
('Maria Soporte',         'soporte',   'soporte@contatech.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'soporte', 1);

-- ============================================================
-- 2. PROVEEDORES
-- ============================================================
CREATE TABLE proveedores (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nit           VARCHAR(20)  NOT NULL,
  razon_social  VARCHAR(200) NOT NULL,
  contacto      VARCHAR(150) DEFAULT NULL,
  email         VARCHAR(150) DEFAULT NULL,
  telefono      VARCHAR(30)  DEFAULT NULL,
  direccion     VARCHAR(255) DEFAULT NULL,
  activo        TINYINT(1)   NOT NULL DEFAULT 1,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_proveedores_nit (nit),
  KEY idx_proveedores_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO proveedores (nit, razon_social, contacto, email, telefono, direccion, activo) VALUES
('900123456-1', 'Distribuidora Andina SAS', 'Juan Pérez',  'contacto@andina.com', '3001234567', 'Calle 10 #20-30, Bogotá', 1),
('800987654-2', 'Suministros del Valle',    'María López', 'ventas@valle.com',    '3109876543', 'Av. 5 #15-22, Cali', 1);

-- ============================================================
-- 3. CATEGORÍAS → PRODUCTOS
-- ============================================================
CREATE TABLE categorias (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre      VARCHAR(100) NOT NULL,
  descripcion VARCHAR(255) DEFAULT NULL,
  activo      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_categorias_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categorias (nombre, descripcion, activo) VALUES
('Electrónica', 'Dispositivos y accesorios electrónicos', 1),
('Papelería',   'Útiles de oficina y papelería', 1),
('Alimentos',   'Productos alimenticios', 1),
('Servicios',   'Servicios prestados', 1);

CREATE TABLE productos (
  id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  codigo        VARCHAR(50)   NOT NULL,
  nombre        VARCHAR(200)  NOT NULL,
  categoria_id  INT UNSIGNED  DEFAULT NULL,
  precio        DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  costo         DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  stock         INT           NOT NULL DEFAULT 0,
  stock_minimo  INT           NOT NULL DEFAULT 5,
  unidad        VARCHAR(20)   NOT NULL DEFAULT 'UND',
  activo        TINYINT(1)    NOT NULL DEFAULT 1,
  created_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_productos_codigo (codigo),
  KEY idx_productos_categoria (categoria_id),
  KEY idx_productos_activo (activo),
  KEY idx_productos_stock (stock, stock_minimo),
  CONSTRAINT fk_productos_categoria
    FOREIGN KEY (categoria_id) REFERENCES categorias (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO productos (codigo, nombre, categoria_id, precio, costo, stock, stock_minimo, unidad, activo) VALUES
('PROD-001', 'Mouse inalámbrico', 1,  45000.00,  28000.00,  50, 10, 'UND', 1),
('PROD-002', 'Teclado mecánico',  1, 180000.00, 110000.00,  25,  5, 'UND', 1),
('PROD-003', 'Resma papel A4',    2,  18000.00,  12000.00, 100, 20, 'UND', 1),
('PROD-004', 'Café premium 500g', 3,  25000.00,  15000.00,  40,  8, 'UND', 1);

-- ============================================================
-- 4. CLIENTES
-- ============================================================
CREATE TABLE clientes (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  tipo_doc    ENUM('CC','NIT','CE','PP') NOT NULL DEFAULT 'CC',
  documento   VARCHAR(30)  NOT NULL,
  nombre      VARCHAR(200) NOT NULL,
  email       VARCHAR(150) DEFAULT NULL,
  telefono    VARCHAR(30)  DEFAULT NULL,
  direccion   VARCHAR(255) DEFAULT NULL,
  activo      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_clientes_doc (tipo_doc, documento),
  KEY idx_clientes_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO clientes (tipo_doc, documento, nombre, email, telefono, activo) VALUES
('CC',  '1234567890',  'Cliente Mostrador', 'cliente@email.com', '3000000000', 1),
('NIT', '900111222-3', 'Empresa Demo SAS',  'demo@empresa.com',  '6015551234', 1);

-- ============================================================
-- 5. MÉTODOS DE PAGO
-- ============================================================
CREATE TABLE metodos_pago (
  id     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(80)  NOT NULL,
  tipo   ENUM('efectivo','tarjeta','transferencia','bold','otro') NOT NULL DEFAULT 'efectivo',
  activo TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO metodos_pago (nombre, tipo, activo) VALUES
('Efectivo',               'efectivo',      1),
('Tarjeta débito/crédito', 'tarjeta',       1),
('Transferencia bancaria', 'transferencia', 1),
('Bold (pasarela)',        'bold',          1);

-- ============================================================
-- 6. ÓRDENES DE VENTA (+ detalle)
--    usuario_id → usuarios
--    cliente_id → clientes
--    detalle.producto_id → productos
-- ============================================================
CREATE TABLE ordenes_venta (
  id             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  numero         VARCHAR(30)   NOT NULL,
  cliente_id     INT UNSIGNED  DEFAULT NULL,
  usuario_id     INT UNSIGNED  NOT NULL,
  estado         ENUM('pendiente','facturado','cobrado','anulado') NOT NULL DEFAULT 'pendiente',
  subtotal       DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  impuestos      DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  total          DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  observaciones  TEXT          DEFAULT NULL,
  created_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_ov_numero (numero),
  KEY idx_ov_cliente (cliente_id),
  KEY idx_ov_usuario (usuario_id),
  KEY idx_ov_estado (estado),
  KEY idx_ov_created (created_at),
  CONSTRAINT fk_ov_cliente
    FOREIGN KEY (cliente_id) REFERENCES clientes (id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_ov_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ordenes_venta_detalle (
  id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  orden_id         INT UNSIGNED  NOT NULL,
  producto_id      INT UNSIGNED  NOT NULL,
  cantidad         INT           NOT NULL,
  precio_unitario  DECIMAL(12,2) NOT NULL,
  subtotal         DECIMAL(14,2) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_ovd_orden (orden_id),
  KEY idx_ovd_producto (producto_id),
  CONSTRAINT fk_ovd_orden
    FOREIGN KEY (orden_id) REFERENCES ordenes_venta (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_ovd_producto
    FOREIGN KEY (producto_id) REFERENCES productos (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. FACTURAS ELECTRÓNICAS (+ detalle + asientos)
--    orden_id → ordenes_venta
--    cliente_id → clientes
--    usuario_id → usuarios
-- ============================================================
CREATE TABLE facturas (
  id                 INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  numero             VARCHAR(40)   NOT NULL,
  orden_id           INT UNSIGNED  DEFAULT NULL,
  cliente_id         INT UNSIGNED  DEFAULT NULL,
  usuario_id         INT UNSIGNED  NOT NULL,
  fecha              DATE          NOT NULL,
  subtotal           DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  impuestos          DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  total              DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  estado             ENUM(
                       'borrador',
                       'emitida',
                       'enviada_dian',
                       'aceptada',
                       'rechazada',
                       'anulada'
                     ) NOT NULL DEFAULT 'borrador',
  cufe               VARCHAR(100)  DEFAULT NULL,
  dian_response      TEXT          DEFAULT NULL,
  registro_contable  TINYINT(1)    NOT NULL DEFAULT 0,
  created_at         TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_facturas_numero (numero),
  KEY idx_fac_orden (orden_id),
  KEY idx_fac_cliente (cliente_id),
  KEY idx_fac_usuario (usuario_id),
  KEY idx_fac_estado (estado),
  KEY idx_fac_fecha (fecha),
  CONSTRAINT fk_fac_orden
    FOREIGN KEY (orden_id) REFERENCES ordenes_venta (id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_fac_cliente
    FOREIGN KEY (cliente_id) REFERENCES clientes (id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_fac_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE facturas_detalle (
  id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  factura_id       INT UNSIGNED  NOT NULL,
  producto_id      INT UNSIGNED  DEFAULT NULL,
  descripcion      VARCHAR(255)  NOT NULL,
  cantidad         INT           NOT NULL,
  precio_unitario  DECIMAL(12,2) NOT NULL,
  subtotal         DECIMAL(14,2) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_fd_factura (factura_id),
  KEY idx_fd_producto (producto_id),
  CONSTRAINT fk_fd_factura
    FOREIGN KEY (factura_id) REFERENCES facturas (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_fd_producto
    FOREIGN KEY (producto_id) REFERENCES productos (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. PAGOS
--    factura_id → facturas
--    orden_id → ordenes_venta
--    metodo_pago_id → metodos_pago
--    usuario_id → usuarios
-- ============================================================
CREATE TABLE pagos (
  id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  factura_id       INT UNSIGNED  DEFAULT NULL,
  orden_id         INT UNSIGNED  DEFAULT NULL,
  metodo_pago_id   INT UNSIGNED  NOT NULL,
  monto            DECIMAL(14,2) NOT NULL,
  monto_factura    DECIMAL(14,2) DEFAULT NULL,
  referencia       VARCHAR(100)  DEFAULT NULL,
  bold_payment_id  VARCHAR(100)  DEFAULT NULL,
  estado           ENUM('pendiente','aprobado','rechazado','conciliado') NOT NULL DEFAULT 'pendiente',
  conciliado       TINYINT(1)    NOT NULL DEFAULT 0,
  usuario_id       INT UNSIGNED  DEFAULT NULL,
  created_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_pagos_factura (factura_id),
  KEY idx_pagos_orden (orden_id),
  KEY idx_pagos_metodo (metodo_pago_id),
  KEY idx_pagos_usuario (usuario_id),
  KEY idx_pagos_estado (estado),
  CONSTRAINT fk_pagos_factura
    FOREIGN KEY (factura_id) REFERENCES facturas (id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_pagos_orden
    FOREIGN KEY (orden_id) REFERENCES ordenes_venta (id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_pagos_metodo
    FOREIGN KEY (metodo_pago_id) REFERENCES metodos_pago (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_pagos_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. ÓRDENES DE COMPRA (+ detalle)
--    proveedor_id → proveedores
--    usuario_id → usuarios
-- ============================================================
CREATE TABLE ordenes_compra (
  id             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  numero         VARCHAR(30)   NOT NULL,
  proveedor_id   INT UNSIGNED  NOT NULL,
  usuario_id     INT UNSIGNED  NOT NULL,
  estado         ENUM('solicitado','facturado_proveedor','entregado','anulado') NOT NULL DEFAULT 'solicitado',
  subtotal       DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  total          DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  observaciones  TEXT          DEFAULT NULL,
  fecha_entrega  DATE          DEFAULT NULL,
  created_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_oc_numero (numero),
  KEY idx_oc_proveedor (proveedor_id),
  KEY idx_oc_usuario (usuario_id),
  KEY idx_oc_estado (estado),
  CONSTRAINT fk_oc_proveedor
    FOREIGN KEY (proveedor_id) REFERENCES proveedores (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_oc_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ordenes_compra_detalle (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  orden_id        INT UNSIGNED  NOT NULL,
  producto_id     INT UNSIGNED  NOT NULL,
  cantidad        INT           NOT NULL,
  costo_unitario  DECIMAL(12,2) NOT NULL,
  subtotal        DECIMAL(14,2) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_ocd_orden (orden_id),
  KEY idx_ocd_producto (producto_id),
  CONSTRAINT fk_ocd_orden
    FOREIGN KEY (orden_id) REFERENCES ordenes_compra (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_ocd_producto
    FOREIGN KEY (producto_id) REFERENCES productos (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. MOVIMIENTOS (ingresos / egresos)
--     usuario_id → usuarios (opcional)
-- ============================================================
CREATE TABLE movimientos (
  id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  tipo         ENUM('ingreso','egreso') NOT NULL,
  descripcion  VARCHAR(255)  NOT NULL,
  monto        DECIMAL(14,2) NOT NULL,
  categoria    VARCHAR(100)  DEFAULT NULL,
  fecha        DATE          NOT NULL,
  usuario_id   INT UNSIGNED  DEFAULT NULL,
  referencia   VARCHAR(100)  DEFAULT NULL,
  created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_mov_tipo (tipo),
  KEY idx_mov_fecha (fecha),
  KEY idx_mov_usuario (usuario_id),
  CONSTRAINT fk_mov_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO movimientos (tipo, descripcion, monto, categoria, fecha, usuario_id) VALUES
('ingreso', 'Ventas del mes',       15000000.00, 'ventas',    CURDATE(), 1),
('egreso',  'Alquiler local',        2500000.00, 'alquiler',  CURDATE(), 1),
('egreso',  'Servicios públicos',     450000.00, 'servicios', CURDATE(), 1);

-- ============================================================
-- 11. ASIENTOS CONTABLES
--     factura_id → facturas
-- ============================================================
CREATE TABLE asientos_contables (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  factura_id      INT UNSIGNED  DEFAULT NULL,
  fecha           DATE          NOT NULL,
  cuenta_debito   VARCHAR(50)   NOT NULL,
  cuenta_credito  VARCHAR(50)   NOT NULL,
  monto           DECIMAL(14,2) NOT NULL,
  descripcion     VARCHAR(255)  DEFAULT NULL,
  created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_ac_factura (factura_id),
  KEY idx_ac_fecha (fecha),
  CONSTRAINT fk_ac_factura
    FOREIGN KEY (factura_id) REFERENCES facturas (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- FIN
-- ============================================================
SET FOREIGN_KEY_CHECKS = 1;
