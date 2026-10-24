# ContaTech 2.0 — Arquitectura modular MVC (microservicios lógicos)

Sistema de gestión contable/comercial para XAMPP (PHP + MySQL + JS + CSS).  
Sin Docker, sin Nginx, sin API Gateway.

## Requisitos

- XAMPP (PHP 8.0+, MySQL/MariaDB)
- Extensiones: pdo_mysql, json, mbstring

## Instalación en htdocs

1. Copiar la carpeta `ContaTech` a `C:\xampp\htdocs\` (o `/opt/lampp/htdocs/`).
2. Crear la base de datos:
   - Abrir phpMyAdmin → Importar `database/contatech_ms.sql`
3. Ajustar credenciales MySQL si es necesario en `config/database.php`.
4. Ejecutar una vez: `http://localhost/ContaTech/setup_passwords.php`  
   (asigna la clave `Admin123!` a todos los usuarios demo).
5. Eliminar `setup_passwords.php`.
6. Entrar en `http://localhost/ContaTech/`

Si la carpeta no se llama `ContaTech`, editar `base_url` en `config/app.php`.

## Usuarios de prueba

| Usuario    | Rol            | Clave      |
|-----------|----------------|------------|
| admin     | Administrador  | Admin123!  |
| gerente   | Gerente        | Admin123!  |
| jefe      | Jefe           | Admin123!  |
| contable  | Contable       | Admin123!  |
| cajero    | Cajero         | Admin123!  |
| bodeguero | Bodeguero      | Admin123!  |
| soporte   | Soporte        | Admin123!  |

Cada rol ve un menú y un dashboard distintos según `core/RolePermissions.php`.

## Arquitectura

```
ContaTech/
├── api/index.php          ← Router REST (único punto API)
├── config/                ← app.php, database.php
├── core/                  ← JWT, Database, Auth, Roles, Response
├── services/              ← Microservicios lógicos (MVC)
│   ├── Auth/
│   ├── Users/
│   ├── Providers/
│   ├── Inventory/
│   ├── Orders/
│   ├── Invoices/ (+ DianService)
│   ├── Payments/ (+ BoldService)
│   ├── Purchases/
│   ├── CashFlow/
│   └── Reports/
├── views/                 ← UI por módulo (diferente según rol)
├── public/assets/         ← CSS, JS, imágenes
└── database/contatech_ms.sql
```

Cada “servicio” tiene Controllers + Models. Se comunican vía la API PHP (HTTP fetch desde el frontend) o modelos compartidos en el mismo proceso. No hay contenedores ni API Gateway.

## JWT

- Login: `POST /api/index.php?path=auth/login` → devuelve token.
- El token se guarda en `localStorage` y cookie `contatech_token`.
- Todas las rutas protegidas validan `Authorization: Bearer <token>`.

## Integraciones

### Bold (pasarela de pagos)
- Configuración en `config/app.php` → `bold`.
- Endpoint: `POST pagos/bold` (crea checkout sandbox).
- `POST pagos/{id}/bold-confirm` confirma estado.

### DIAN (facturación electrónica)
- Configuración en `config/app.php` → `dian`.
- Endpoint: `POST facturas/{id}/dian`.
- En `test_mode` genera CUFE simulado (ambiente de habilitación).

## Módulos / gestiones implementadas

- Usuarios (CRUD + roles + login/logout + recuperar/cambiar contraseña)
- Proveedores (CRUD)
- Inventario (categorías, productos, stock, alertas, desactivar)
- Pagos (métodos, Bold, conciliación monto vs factura)
- Facturas (generar desde orden, contabilizar, DIAN)
- Pedidos / órdenes de venta (crear, consultar, actualizar, anular + flujo de estados)
- Compras a proveedores (crear, consultar, actualizar, anular + flujo)
- Ingresos / egresos
- Informes (balance, estado de resultados, flujo de caja, impuestos)

## API (ejemplos)

```
POST  api/index.php?path=auth/login
GET   api/index.php?path=usuarios
POST  api/index.php?path=productos
POST  api/index.php?path=pedidos
POST  api/index.php?path=facturas/generar
POST  api/index.php?path=pagos/bold
POST  api/index.php?path=pagos/1/conciliar
GET   api/index.php?path=informes/dashboard
```

Todas las peticiones autenticadas llevan header:
`Authorization: Bearer <jwt>`
