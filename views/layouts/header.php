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
        <style>
            /* —— Tema oscuro global —— */
            html[data-theme="dark"] body { background:#0f1115; color:#e8eaed; }
            html[data-theme="dark"] .sidebar { background:#1a1d24; border-right-color:#2a2f3a; }
            html[data-theme="dark"] .sidebar .logo { color:#e8eaed; }
            html[data-theme="dark"] .sidebar .role-badge { color:#9aa0a6; }
            html[data-theme="dark"] .menu a,
            html[data-theme="dark"] .menu button { color:#e8eaed; }
            html[data-theme="dark"] .menu a:hover,
            html[data-theme="dark"] .menu a.active,
            html[data-theme="dark"] .menu button:hover { background:#252a35; }
            html[data-theme="dark"] .main-content { background:#0f1115; color:#e8eaed; }
            html[data-theme="dark"] .form-card,
            html[data-theme="dark"] .card,
            html[data-theme="dark"] .table-wrap,
            html[data-theme="dark"] .chart-box { background:#1a1d24; box-shadow:0 1px 3px rgba(0,0,0,.35); color:#e8eaed; }
            html[data-theme="dark"] table.data th { background:#22262f; color:#9aa0a6; }
            html[data-theme="dark"] table.data td,
            html[data-theme="dark"] table.data th { border-bottom-color:#2a2f3a; color:#e8eaed; }
            html[data-theme="dark"] .btn-secondary { background:#2a2f3a; color:#e8eaed; }
            html[data-theme="dark"] .btn-primary { background:#e8eaed; color:#111; }
            html[data-theme="dark"] .form-group input,
            html[data-theme="dark"] .form-group select,
            html[data-theme="dark"] .form-group textarea { background:#12151c; border-color:#2a2f3a; color:#e8eaed; }
            html[data-theme="dark"] .header h1 { color:#e8eaed; }
            html[data-theme="dark"] .card .label { color:#9aa0a6; }

            /* Iconos del sidebar: PNG negros → blancos en modo oscuro */
            html[data-theme="dark"] .sidebar .menu img.icon,
            html[data-theme="dark"] .sidebar img.icon {
                filter: invert(1) brightness(1.15);
            }

            /* Densidad compacta */
            html.compact-ui .card { padding:14px; }
            html.compact-ui table.data th,
            html.compact-ui table.data td { padding:8px 10px; }
        </style>
        <script src="<?= $base ?>/public/assets/js/api.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    </head>
<body>


<script>
(function () {
  try {
    var pref = localStorage.getItem('contatech_theme') || 'system';
    var theme = pref;
    if (pref === 'system') {
      theme = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
    }
    document.documentElement.setAttribute('data-theme', theme);
    if (localStorage.getItem('contatech_pref_compact') === '1') {
      document.documentElement.classList.add('compact-ui');
    }
  } catch (e) {}
})();
</script>
