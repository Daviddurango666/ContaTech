<?php
require __DIR__ . '/../layouts/auth_check.php';

if (!RolePermissions::can($currentUser['rol'], 'soporte', 'view')) {
    header('Location: /ContaTech1/views/dashboard/index.php'); 
    exit;
}

$pageTitle = 'Soporte';
$currentRoute = 'soporte';

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';

?>
<main class="main-content">
    <div class="header">
        <h1>Soporte técnico</h1>
    </div>
    <div class="form-card">
        <p>Módulo de soporte para el rol <strong>Soporte</strong>.</p>
        <p style="margin-top:12px;color:#666;">Desde aquí se puede consultar usuarios y brindar asistencia sobre el sistema ContaTech.</p>
        <p style="margin-top:16px;">
            <a class="btn btn-secondary" href="/ContaTech/views/usuarios/index.php">Ver usuarios</a>
        </p>
    </div>
</main>
</body>
</html>
