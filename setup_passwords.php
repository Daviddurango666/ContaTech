<?php
/**
 * Ejecutar UNA VEZ después de importar contatech_ms.sql
 * Actualiza las contraseñas de usuarios demo a: Admin123!
 *
 * Uso: http://localhost/ContaTech/setup_passwords.php
 * Luego ELIMINAR este archivo por seguridad.
 */

require_once __DIR__ . '/core/Database.php';

$hash = password_hash('Admin123!', PASSWORD_BCRYPT);
$db = Database::getInstance();
$stmt = $db->prepare('UPDATE usuarios SET password = ?');
$stmt->execute([$hash]);

$count = $stmt->rowCount();
echo "<h2>ContaTech — Setup de contraseñas</h2>";
echo "<p>Se actualizaron <strong>$count</strong> usuarios.</p>";
echo "<p>Contraseña para todos: <code>Admin123!</code></p>";
echo "<p>Usuarios: admin, gerente, jefe, contable, cajero, bodeguero, soporte</p>";
echo "<p><a href='views/auth/login.php'>Ir al login</a></p>";
echo "<p style='color:red;'><strong>Elimine este archivo (setup_passwords.php) después de usarlo.</strong></p>";
