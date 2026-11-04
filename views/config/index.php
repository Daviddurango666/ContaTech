<?php
require __DIR__ . '/../layouts/auth_check.php';
if (!RolePermissions::can($currentUser['rol'], 'config', 'view')) {
    header('Location: ' . $appCfg['base_url'] . '/views/dashboard/index.php');
    exit;
}
$pageTitle = 'Configuración';
$currentRoute = 'config';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';

$canUpdate = RolePermissions::can($currentUser['rol'], 'config', 'update');
$base = $appCfg['base_url'] ?? '';
$assets = '../../public/assets';
$appName = $appCfg['app_name'] ?? 'ContaTech';
$appVersion = $appCfg['app_version'] ?? '—';
$timezone = $appCfg['timezone'] ?? 'America/Bogota';
$roleLabel = $appCfg['roles'][$currentUser['rol'] ?? ''] ?? ($currentUser['rol'] ?? '');
$boldEnabled = !empty($appCfg['bold']['enabled']);
$dianEnabled = !empty($appCfg['dian']['enabled']);
$dianTest   = !empty($appCfg['dian']['test_mode']);
?>
<style>
  /* Preferencias de apariencia (modo claro / oscuro) */
  :root {
    --cfg-bg: #f5f5f5;
    --cfg-card: #fff;
    --cfg-text: #222;
    --cfg-muted: #666;
    --cfg-border: #e5e5e5;
    --cfg-hover: #f0f0f0;
    --cfg-shadow: 0 1px 3px rgba(0,0,0,.06);
  }
  html[data-theme="dark"] {
    --cfg-bg: #0f1115;
    --cfg-card: #1a1d24;
    --cfg-text: #e8eaed;
    --cfg-muted: #9aa0a6;
    --cfg-border: #2a2f3a;
    --cfg-hover: #252a35;
    --cfg-shadow: 0 1px 3px rgba(0,0,0,.35);
  }
  html[data-theme="dark"] body {
    background: var(--cfg-bg);
    color: var(--cfg-text);
  }
  html[data-theme="dark"] .sidebar {
    background: var(--cfg-card);
    border-right-color: var(--cfg-border);
  }
  html[data-theme="dark"] .sidebar .logo { color: var(--cfg-text); }
  html[data-theme="dark"] .sidebar .role-badge { color: var(--cfg-muted); }
  html[data-theme="dark"] .menu a,
  html[data-theme="dark"] .menu button { color: var(--cfg-text); }
  html[data-theme="dark"] .menu a:hover,
  html[data-theme="dark"] .menu a.active,
  html[data-theme="dark"] .menu button:hover { background: var(--cfg-hover); }
  html[data-theme="dark"] .main-content { background: var(--cfg-bg); color: var(--cfg-text); }
  html[data-theme="dark"] .form-card,
  html[data-theme="dark"] .card,
  html[data-theme="dark"] .table-wrap,
  html[data-theme="dark"] .chart-box {
    background: var(--cfg-card);
    box-shadow: var(--cfg-shadow);
    color: var(--cfg-text);
  }
  html[data-theme="dark"] table.data th {
    background: #22262f;
    color: var(--cfg-muted);
  }
  html[data-theme="dark"] table.data td,
  html[data-theme="dark"] table.data th {
    border-bottom-color: var(--cfg-border);
    color: var(--cfg-text);
  }
  html[data-theme="dark"] .btn-secondary {
    background: #2a2f3a;
    color: var(--cfg-text);
  }
  html[data-theme="dark"] .btn-primary {
    background: #e8eaed;
    color: #111;
  }
  html[data-theme="dark"] .form-group input,
  html[data-theme="dark"] .form-group select,
  html[data-theme="dark"] .form-group textarea {
    background: #12151c;
    border-color: var(--cfg-border);
    color: var(--cfg-text);
  }
  html[data-theme="dark"] .header h1 { color: var(--cfg-text); }
  html[data-theme="dark"] .card .label { color: var(--cfg-muted); }

  .theme-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 12px;
  }
  .theme-option {
    border: 2px solid var(--cfg-border, #e5e5e5);
    border-radius: 12px;
    padding: 16px 12px;
    text-align: center;
    cursor: pointer;
    background: var(--cfg-card, #fff);
    transition: border-color .15s, background .15s;
    user-select: none;
  }
  .theme-option:hover { border-color: #999; }
  .theme-option.active {
    border-color: #000;
    background: #f8f8f8;
  }
  html[data-theme="dark"] .theme-option.active {
    border-color: #e8eaed;
    background: #252a35;
  }
  .theme-option .icon { font-size: 28px; line-height: 1; margin-bottom: 8px; }
  .theme-option .label { font-size: 13px; font-weight: 600; }
  .theme-option .hint { font-size: 11px; color: var(--cfg-muted, #777); margin-top: 4px; }

  .switch-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 12px 0;
    border-bottom: 1px solid var(--cfg-border, #eee);
  }
  .switch-row:last-child { border-bottom: none; }
  .switch-row .meta { flex: 1; }
  .switch-row .meta strong { display: block; font-size: 14px; margin-bottom: 2px; }
  .switch-row .meta span { font-size: 12px; color: var(--cfg-muted, #777); }

  .toggle {
    position: relative;
    width: 44px;
    height: 24px;
    flex-shrink: 0;
  }
  .toggle input { opacity: 0; width: 0; height: 0; }
  .toggle .slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background: #ccc;
    border-radius: 24px;
    transition: .2s;
  }
  .toggle .slider:before {
    content: "";
    position: absolute;
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background: #fff;
    border-radius: 50%;
    transition: .2s;
  }
  .toggle input:checked + .slider { background: #16a34a; }
  .toggle input:checked + .slider:before { transform: translateX(20px); }

  .status-pill {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
  }
  .status-on { background: #dcfce7; color: #166534; }
  .status-off { background: #fee2e2; color: #991b1b; }
  .status-test { background: #fef3c7; color: #92400e; }

  #cfgToast {
    display: none;
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: #166534;
    color: #fff;
    padding: 12px 18px;
    border-radius: 10px;
    font-size: 14px;
    z-index: 9999;
    box-shadow: 0 4px 16px rgba(0,0,0,.2);
  }
</style>

<main class="main-content">
  <div class="header">
    <h1>Configuración</h1>
    <div class="user-info">
      <span><?= htmlspecialchars($roleLabel) ?></span>
    </div>
  </div>

  <div class="cards" style="margin-bottom:24px;">
    <div class="card">
      <div class="label">Aplicación</div>
      <div class="value" style="font-size:18px;"><?= htmlspecialchars($appName) ?></div>
      <div style="font-size:13px;color:var(--cfg-muted,#777);margin-top:6px;">Versión <?= htmlspecialchars($appVersion) ?></div>
    </div>
    <div class="card">
      <div class="label">Usuario actual</div>
      <div class="value" style="font-size:18px;"><?= htmlspecialchars($currentUser['nombre'] ?? $currentUser['usuario'] ?? '—') ?></div>
      <div style="font-size:13px;color:var(--cfg-muted,#777);margin-top:6px;"><?= htmlspecialchars($roleLabel) ?></div>
    </div>
    <div class="card">
      <div class="label">Zona horaria</div>
      <div class="value" style="font-size:18px;"><?= htmlspecialchars($timezone) ?></div>
      <div style="font-size:13px;color:var(--cfg-muted,#777);margin-top:6px;">America/Bogota por defecto</div>
    </div>
  </div>

  <!-- Apariencia -->
  <div class="form-card" style="max-width:100%;margin-bottom:20px;">
    <h2 style="margin:0 0 6px;font-size:18px;">Apariencia</h2>
    <p style="margin:0 0 16px;font-size:13px;color:var(--cfg-muted,#777);">
      Elige el tema de la interfaz. La preferencia se guarda en este navegador.
    </p>

    <div class="theme-options" id="themeOptions">
      <div class="theme-option" data-theme="light" role="button" tabindex="0">
        <div class="icon">☀️</div>
        <div class="label">Claro</div>
        <div class="hint">Fondo claro</div>
      </div>
      <div class="theme-option" data-theme="dark" role="button" tabindex="0">
        <div class="icon">🌙</div>
        <div class="label">Oscuro</div>
        <div class="hint">Menor fatiga visual</div>
      </div>
      <div class="theme-option" data-theme="system" role="button" tabindex="0">
        <div class="icon">💻</div>
        <div class="label">Sistema</div>
        <div class="hint">Según tu dispositivo</div>
      </div>
    </div>
  </div>

  <!-- Preferencias de interfaz -->
  <div class="form-card" style="max-width:100%;margin-bottom:20px;">
    <h2 style="margin:0 0 6px;font-size:18px;">Preferencias de interfaz</h2>
    <p style="margin:0 0 8px;font-size:13px;color:var(--cfg-muted,#777);">
      Ajustes locales de este navegador (no afectan a otros usuarios).
    </p>

    <div class="switch-row">
      <div class="meta">
        <strong>Densidad compacta</strong>
        <span>Reduce el espaciado de tablas y tarjetas para ver más información.</span>
      </div>
      <label class="toggle">
        <input type="checkbox" id="prefCompact">
        <span class="slider"></span>
      </label>
    </div>

    <div class="switch-row">
      <div class="meta">
        <strong>Recordar última sección</strong>
        <span>Al iniciar sesión, volver al último módulo visitado (cuando esté disponible).</span>
      </div>
      <label class="toggle">
        <input type="checkbox" id="prefRememberRoute">
        <span class="slider"></span>
      </label>
    </div>

    <div class="switch-row">
      <div class="meta">
        <strong>Confirmar acciones destructivas</strong>
        <span>Pedir confirmación extra antes de anular o eliminar registros.</span>
      </div>
      <label class="toggle">
        <input type="checkbox" id="prefConfirmDestructive" checked>
        <span class="slider"></span>
      </label>
    </div>
  </div>

  <!-- Integraciones (solo lectura) -->
  <div class="form-card" style="max-width:100%;margin-bottom:20px;">
    <h2 style="margin:0 0 6px;font-size:18px;">Integraciones del sistema</h2>
    <p style="margin:0 0 14px;font-size:13px;color:var(--cfg-muted,#777);">
      Estado según la configuración del servidor. Los secretos y claves no se muestran aquí.
    </p>
    <div class="table-wrap" style="box-shadow:none;">
      <table class="data">
        <thead>
          <tr>
            <th>Servicio</th>
            <th>Estado</th>
            <th>Notas</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Bold (pasarela de pagos)</td>
            <td>
              <?php if ($boldEnabled): ?>
                <span class="status-pill status-on">Habilitado</span>
              <?php else: ?>
                <span class="status-pill status-off">Deshabilitado</span>
              <?php endif; ?>
            </td>
            <td>Configurado en <code>config/app.php</code></td>
          </tr>
          <tr>
            <td>DIAN (facturación electrónica)</td>
            <td>
              <?php if ($dianEnabled): ?>
                <span class="status-pill status-on">Habilitado</span>
                <?php if ($dianTest): ?>
                  <span class="status-pill status-test">Modo prueba</span>
                <?php endif; ?>
              <?php else: ?>
                <span class="status-pill status-off">Deshabilitado</span>
              <?php endif; ?>
            </td>
            <td>Ambiente de habilitación / producción según configuración</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Cuenta -->
  <div class="form-card" style="max-width:100%;margin-bottom:20px;">
    <h2 style="margin:0 0 12px;font-size:18px;">Cuenta y sesión</h2>
    <div class="switch-row" style="border:none;padding-top:0;">
      <div class="meta">
        <strong>Sesión actual</strong>
        <span>
          Usuario: <?= htmlspecialchars($currentUser['usuario'] ?? '—') ?>
          <?php if (!empty($currentUser['email'])): ?>
            · <?= htmlspecialchars($currentUser['email']) ?>
          <?php endif; ?>
        </span>
      </div>
      <button type="button" class="btn btn-secondary btn-sm" onclick="logout()">Cerrar sesión</button>
    </div>
    <p style="margin:8px 0 0;font-size:13px;color:var(--cfg-muted,#777);">
      Para cambiar contraseña o datos de perfil, use el módulo de Usuarios (si su rol lo permite) o contacte al administrador.
    </p>
  </div>

  <!-- Acciones -->
  <div class="form-card" style="max-width:100%;margin-bottom:20px;">
    <h2 style="margin:0 0 12px;font-size:18px;">Acciones</h2>
    <div style="display:flex;flex-wrap:wrap;gap:10px;">
      <button type="button" class="btn btn-primary" id="btnSavePrefs" <?= $canUpdate ? '' : '' ?>>
        Guardar preferencias
      </button>
      <button type="button" class="btn btn-secondary" id="btnResetPrefs">
        Restablecer preferencias locales
      </button>
      <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/views/dashboard/index.php">
        Volver al dashboard
      </a>
    </div>
    <p style="margin:12px 0 0;font-size:12px;color:var(--cfg-muted,#777);">
      Las preferencias de apariencia se aplican de inmediato y se guardan en este navegador (localStorage).
      <?php if (!$canUpdate): ?>
        Su rol puede consultar esta pantalla; algunos cambios de sistema requieren perfil Administrador.
      <?php endif; ?>
    </p>
  </div>
</main>

<div id="cfgToast">Preferencias guardadas</div>

<script>
(function () {
  const KEY_THEME = 'contatech_theme';
  const KEY_COMPACT = 'contatech_pref_compact';
  const KEY_REMEMBER = 'contatech_pref_remember_route';
  const KEY_CONFIRM = 'contatech_pref_confirm_destructive';

  function systemTheme() {
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
      ? 'dark'
      : 'light';
  }

  function resolveTheme(pref) {
    if (pref === 'dark' || pref === 'light') return pref;
    return systemTheme();
  }

  function applyTheme(pref) {
    const resolved = resolveTheme(pref);
    document.documentElement.setAttribute('data-theme', resolved);
    document.querySelectorAll('.theme-option').forEach(function (el) {
      el.classList.toggle('active', el.getAttribute('data-theme') === pref);
    });
  }

  function applyCompact(on) {
    document.documentElement.classList.toggle('compact-ui', !!on);
  }

  function loadPrefs() {
    const theme = localStorage.getItem(KEY_THEME) || 'system';
    const compact = localStorage.getItem(KEY_COMPACT) === '1';
    const remember = localStorage.getItem(KEY_REMEMBER) === '1';
    const confirm = localStorage.getItem(KEY_CONFIRM) !== '0'; // default true

    applyTheme(theme);
    applyCompact(compact);

    const elCompact = document.getElementById('prefCompact');
    const elRemember = document.getElementById('prefRememberRoute');
    const elConfirm = document.getElementById('prefConfirmDestructive');
    if (elCompact) elCompact.checked = compact;
    if (elRemember) elRemember.checked = remember;
    if (elConfirm) elConfirm.checked = confirm;
  }

  function savePrefs() {
    const active = document.querySelector('.theme-option.active');
    const theme = active ? active.getAttribute('data-theme') : 'system';
    const compact = document.getElementById('prefCompact').checked;
    const remember = document.getElementById('prefRememberRoute').checked;
    const confirm = document.getElementById('prefConfirmDestructive').checked;

    localStorage.setItem(KEY_THEME, theme);
    localStorage.setItem(KEY_COMPACT, compact ? '1' : '0');
    localStorage.setItem(KEY_REMEMBER, remember ? '1' : '0');
    localStorage.setItem(KEY_CONFIRM, confirm ? '1' : '0');

    applyTheme(theme);
    applyCompact(compact);
    showToast('Preferencias guardadas');
  }

  function resetPrefs() {
    localStorage.removeItem(KEY_THEME);
    localStorage.removeItem(KEY_COMPACT);
    localStorage.removeItem(KEY_REMEMBER);
    localStorage.removeItem(KEY_CONFIRM);
    loadPrefs();
    showToast('Preferencias restablecidas');
  }

  function showToast(msg) {
    const t = document.getElementById('cfgToast');
    if (!t) return;
    t.textContent = msg;
    t.style.display = 'block';
    clearTimeout(t._timer);
    t._timer = setTimeout(function () { t.style.display = 'none'; }, 2200);
  }

  document.querySelectorAll('.theme-option').forEach(function (el) {
    el.addEventListener('click', function () {
      document.querySelectorAll('.theme-option').forEach(function (x) { x.classList.remove('active'); });
      el.classList.add('active');
      applyTheme(el.getAttribute('data-theme'));
    });
    el.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        el.click();
      }
    });
  });

  document.getElementById('btnSavePrefs').addEventListener('click', savePrefs);
  document.getElementById('btnResetPrefs').addEventListener('click', resetPrefs);

  if (window.matchMedia) {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function () {
      const pref = localStorage.getItem(KEY_THEME) || 'system';
      if (pref === 'system') applyTheme('system');
    });
  }

  loadPrefs();
})();
</script>
</body>
</html>
