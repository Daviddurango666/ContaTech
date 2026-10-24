<?php
$appCfg = $appCfg ?? require __DIR__ . '/../../config/app.php';
$base = $appCfg['base_url'];
?>

<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($pageTitle ?? 'ContaTech') ?> | ContaTech</title>
        <link rel="stylesheet" href="<?= $base ?>/public/assets/css/app.css">
        <link rel="icon" href="<?= $base ?>/public/assets/img/favicon_io/favicon.ico">
        <script src="<?= $base ?>/public/assets/js/api.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    </head>
<body>
