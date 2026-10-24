SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;
CREATE DATABASE IF NOT EXISTS contatech DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE contatech;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(150) NOT NULL,
  usuario VARCHAR(80) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  rol ENUM('administrador','gerente','jefe','contable','soporte','cajero','bodeguero') NOT NULL DEFAULT 'cajero',
  activo TINYINT(1) NOT NULL DEFAULT 1,
  token_reset VARCHAR(100) DEFAULT NULL,
  token_reset_expira DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

INSERT INTO usuarios (nombre,usuario,email,password,rol) VALUES
('Administrador Sistema','admin','admin@contatech.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','administrador'),
('Carlos Gerente','gerente','gerente@contatech.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','gerente'),
('Laura Jefe','jefe','jefe@contatech.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','jefe'),
('Ana Contable','contable','contable@contatech.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','contable'),
('Pedro Cajero','cajero','cajero@contatech.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','cajero'),
('Luis Bodeguero','bodeguero','bodeguero@contatech.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','bodeguero'),
('Maria Soporte','soporte','soporte@contatech.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','soporte');

CREATE TABLE IF NOT EXISTS proveedores (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT, nit VARCHAR(20) NOT NULL UNIQUE, razon_social VARCHAR(200) NOT NULL,
  contacto VARCHAR(150) DEFAULT NULL, email VARCHAR(150) DEFAULT NULL, telefono VARCHAR(30) DEFAULT NULL,
  direccion VARCHAR(255) DEFAULT NULL, activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id)
) ENGINE=InnoDB;
INSERT INTO proveedores (nit,razon_social,contacto,email,telefono,direccion) VALUES
('900123456-1','Distribuidora Andina SAS','Juan Pérez','contacto@andina.com','3001234567','Calle 10 #20-30 Bogotá'),
('800987654-2','Suministros del Valle','María López','ventas@valle.com','3109876543','Av 5 #15-22 Cali');

CREATE TABLE IF NOT EXISTS categorias (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT, nombre VARCHAR(100) NOT NULL UNIQUE, descripcion VARCHAR(255) DEFAULT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id)
) ENGINE=InnoDB;
INSERT INTO categorias (nombre,descripcion) VALUES ('Electrónica','Dispositivos'),('Papelería','Útiles'),('Alimentos','Comestibles'),('Servicios','Servicios');

CREATE TABLE IF NOT EXISTS productos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT, codigo VARCHAR(50) NOT NULL UNIQUE, nombre VARCHAR(200) NOT NULL,
  categoria_id INT UNSIGNED DEFAULT NULL, precio DECIMAL(12,2) NOT NULL DEFAULT 0, costo DECIMAL(12,2) NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0, stock_minimo INT NOT NULL DEFAULT 5, unidad VARCHAR(20) DEFAULT 'UND', activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id), KEY fk_prod_cat (categoria_id), CONSTRAINT fk_prod_cat FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
) ENGINE=InnoDB;
INSERT INTO productos (codigo,nombre,categoria_id,precio,costo,stock,stock_minimo) VALUES
('PROD-001','Mouse inalámbrico',1,45000,28000,50,10),('PROD-002','Teclado mecánico',1,180000,110000,25,5),
('PROD-003','Resma papel A4',2,18000,12000,100,20),('PROD-004','Café premium 500g',3,25000,15000,40,8);

CREATE TABLE IF NOT EXISTS clientes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT, tipo_doc ENUM('CC','NIT','CE','PP') DEFAULT 'CC', documento VARCHAR(30) NOT NULL,
  nombre VARCHAR(200) NOT NULL, email VARCHAR(150) DEFAULT NULL, telefono VARCHAR(30) DEFAULT NULL, direccion VARCHAR(255) DEFAULT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id), UNIQUE KEY uk_cli (tipo_doc,documento)
) ENGINE=InnoDB;
INSERT INTO clientes (tipo_doc,documento,nombre,email,telefono) VALUES ('CC','1234567890','Cliente Mostrador','cliente@email.com','3000000000'),('NIT','900111222-3','Empresa Demo SAS','demo@empresa.com','6015551234');

CREATE TABLE IF NOT EXISTS metodos_pago (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT, nombre VARCHAR(80) NOT NULL, tipo ENUM('efectivo','tarjeta','transferencia','bold','otro') NOT NULL DEFAULT 'efectivo', activo TINYINT(1) NOT NULL DEFAULT 1, PRIMARY KEY (id)
) ENGINE=InnoDB;
INSERT INTO metodos_pago (nombre,tipo) VALUES ('Efectivo','efectivo'),('Tarjeta débito/crédito','tarjeta'),('Transferencia bancaria','transferencia'),('Bold (pasarela)','bold');

CREATE TABLE IF NOT EXISTS ordenes_venta (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT, numero VARCHAR(30) NOT NULL UNIQUE, cliente_id INT UNSIGNED DEFAULT NULL, usuario_id INT UNSIGNED NOT NULL,
  estado ENUM('pendiente','facturado','cobrado','anulado') NOT NULL DEFAULT 'pendiente',
  subtotal DECIMAL(14,2) NOT NULL DEFAULT 0, impuestos DECIMAL(14,2) NOT NULL DEFAULT 0, total DECIMAL(14,2) NOT NULL DEFAULT 0,
  observaciones TEXT DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ordenes_venta_detalle (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT, orden_id INT UNSIGNED NOT NULL, producto_id INT UNSIGNED NOT NULL,
  cantidad INT NOT NULL, precio_unitario DECIMAL(12,2) NOT NULL, subtotal DECIMAL(14,2) NOT NULL, PRIMARY KEY (id),
  KEY fk_ovd (orden_id), CONSTRAINT fk_ovd FOREIGN KEY (orden_id) REFERENCES ordenes_venta(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS facturas (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT, numero VARCHAR(40) NOT NULL UNIQUE, orden_id INT UNSIGNED DEFAULT NULL, cliente_id INT UNSIGNED DEFAULT NULL,
  usuario_id INT UNSIGNED NOT NULL, fecha DATE NOT NULL, subtotal DECIMAL(14,2) NOT NULL DEFAULT 0, impuestos DECIMAL(14,2) NOT NULL DEFAULT 0,
  total DECIMAL(14,2) NOT NULL DEFAULT 0, estado ENUM('borrador','emitida','enviada_dian','aceptada','rechazada','anulada') NOT NULL DEFAULT 'borrador',
  cufe VARCHAR(100) DEFAULT NULL, dian_response TEXT DEFAULT NULL, registro_contable TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS facturas_detalle (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT, factura_id INT UNSIGNED NOT NULL, producto_id INT UNSIGNED DEFAULT NULL,
  descripcion VARCHAR(255) NOT NULL, cantidad INT NOT NULL, precio_unitario DECIMAL(12,2) NOT NULL, subtotal DECIMAL(14,2) NOT NULL,
  PRIMARY KEY (id), KEY fk_fd (factura_id), CONSTRAINT fk_fd FOREIGN KEY (factura_id) REFERENCES facturas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pagos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT, factura_id INT UNSIGNED DEFAULT NULL, orden_id INT UNSIGNED DEFAULT NULL,
  metodo_pago_id INT UNSIGNED NOT NULL, monto DECIMAL(14,2) NOT NULL, monto_factura DECIMAL(14,2) DEFAULT NULL,
  referencia VARCHAR(100) DEFAULT NULL, bold_payment_id VARCHAR(100) DEFAULT NULL,
  estado ENUM('pendiente','aprobado','rechazado','conciliado') NOT NULL DEFAULT 'pendiente', conciliado TINYINT(1) NOT NULL DEFAULT 0,
  usuario_id INT UNSIGNED DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ordenes_compra (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT, numero VARCHAR(30) NOT NULL UNIQUE, proveedor_id INT UNSIGNED NOT NULL, usuario_id INT UNSIGNED NOT NULL,
  estado ENUM('solicitado','facturado_proveedor','entregado','anulado') NOT NULL DEFAULT 'solicitado',
  subtotal DECIMAL(14,2) NOT NULL DEFAULT 0, total DECIMAL(14,2) NOT NULL DEFAULT 0, observaciones TEXT DEFAULT NULL, fecha_entrega DATE DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ordenes_compra_detalle (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT, orden_id INT UNSIGNED NOT NULL, producto_id INT UNSIGNED NOT NULL,
  cantidad INT NOT NULL, costo_unitario DECIMAL(12,2) NOT NULL, subtotal DECIMAL(14,2) NOT NULL, PRIMARY KEY (id),
  KEY fk_ocd (orden_id), CONSTRAINT fk_ocd FOREIGN KEY (orden_id) REFERENCES ordenes_compra(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS movimientos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT, tipo ENUM('ingreso','egreso') NOT NULL, descripcion VARCHAR(255) NOT NULL,
  monto DECIMAL(14,2) NOT NULL, categoria VARCHAR(100) DEFAULT NULL, fecha DATE NOT NULL, usuario_id INT UNSIGNED DEFAULT NULL,
  referencia VARCHAR(100) DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id)
) ENGINE=InnoDB;
INSERT INTO movimientos (tipo,descripcion,monto,categoria,fecha) VALUES
('ingreso','Ventas del mes',15000000,'ventas',CURDATE()),('egreso','Alquiler local',2500000,'alquiler',CURDATE()),('egreso','Servicios públicos',450000,'servicios',CURDATE());

CREATE TABLE IF NOT EXISTS asientos_contables (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT, factura_id INT UNSIGNED DEFAULT NULL, fecha DATE NOT NULL,
  cuenta_debito VARCHAR(50) NOT NULL, cuenta_credito VARCHAR(50) NOT NULL, monto DECIMAL(14,2) NOT NULL,
  descripcion VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id)
) ENGINE=InnoDB;
COMMIT;
