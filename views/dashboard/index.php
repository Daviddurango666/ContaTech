<?php
require __DIR__ . '/../layouts/auth_check.php';
$pageTitle = 'Dashboard';
$currentRoute = 'dashboard';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';

$rol = strtolower($currentUser['rol'] ?? '');
$roleLabel = $appCfg['roles'][$currentUser['rol'] ?? ''] ?? ($currentUser['rol'] ?? '');

// Agrupar roles por tipo de dashboard
$isAdmin     = in_array($rol, ['administrador', 'gerente'], true);
$isOps       = in_array($rol, ['jefe'], true);
$isContable  = ($rol === 'contable');
$isCajero    = ($rol === 'cajero');
$isBodeguero = ($rol === 'bodeguero');
$isSoporte   = ($rol === 'soporte');

// Fallback: si llega un rol desconocido, vista admin reducida
if (!$isAdmin && !$isOps && !$isContable && !$isCajero && !$isBodeguero && !$isSoporte) {
    $isAdmin = true;
}
?>
<main class="main-content">
  <div class="header">
    <?php
    $toggle = __DIR__ . '/../layouts/menu_toggle.php';
    if (is_file($toggle)) require $toggle;
    ?>
    <h1>Dashboard — <?= htmlspecialchars($currentUser['nombre']) ?></h1>
    <div class="user-info">
      <span><?= htmlspecialchars($roleLabel) ?></span>
    </div>
  </div>

  <?php if ($isAdmin): ?>
  <!-- ═══════════ ADMIN / GERENTE ═══════════ -->
  <div class="cards" id="cardsAdmin">
    <div class="card"><div class="label">Ingresos</div><div class="value positive" id="kpiIngresos">—</div></div>
    <div class="card"><div class="label">Egresos</div><div class="value negative" id="kpiEgresos">—</div></div>
    <div class="card"><div class="label">Saldo</div><div class="value" id="kpiSaldo">—</div></div>
    <div class="card"><div class="label">Productos</div><div class="value" id="kpiProd">—</div></div>
    <div class="card"><div class="label">Alertas stock</div><div class="value negative" id="kpiLow">—</div></div>
    <div class="card"><div class="label">Órdenes pendientes</div><div class="value" id="kpiOV">—</div></div>
  </div>
  <div class="chart-grid">
    <div class="chart-box">
      <h3>Ingresos / Egresos</h3>
      <canvas id="chartIE"></canvas>
    </div>
    <div class="chart-box">
      <h3>Últimas órdenes de venta</h3>
      <div class="table-wrap" style="box-shadow:none;">
        <table class="data" id="tblUltimasOrdenes">
          <thead>
            <tr><th>Número</th><th>Cliente</th><th>Estado</th><th>Total</th></tr>
          </thead>
          <tbody><tr><td colspan="4" class="empty">Cargando…</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>

  <?php elseif ($isOps): ?>
  <!-- ═══════════ JEFE (operaciones) ═══════════ -->
  <div class="cards" id="cardsOps">
    <div class="card"><div class="label">Órdenes pendientes</div><div class="value" id="kpiOV">—</div></div>
    <div class="card"><div class="label">Órdenes facturadas</div><div class="value" id="kpiFactOV">—</div></div>
    <div class="card"><div class="label">Órdenes cobradas</div><div class="value positive" id="kpiCobr">—</div></div>
    <div class="card"><div class="label">Productos</div><div class="value" id="kpiProd">—</div></div>
    <div class="card"><div class="label">Alertas stock</div><div class="value negative" id="kpiLow">—</div></div>
    <div class="card"><div class="label">Compras solicitadas</div><div class="value" id="kpiCompSol">—</div></div>
  </div>
  <div class="chart-grid">
    <div class="chart-box">
      <h3>Órdenes por estado</h3>
      <canvas id="chartEstados"></canvas>
    </div>
    <div class="chart-box">
      <h3>Últimas órdenes de venta</h3>
      <div class="table-wrap" style="box-shadow:none;">
        <table class="data" id="tblUltimasOrdenes">
          <thead>
            <tr><th>Número</th><th>Cliente</th><th>Estado</th><th>Total</th></tr>
          </thead>
          <tbody><tr><td colspan="4" class="empty">Cargando…</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>

  <?php elseif ($isContable): ?>
  <!-- ═══════════ CONTABLE ═══════════ -->
  <div class="cards" id="cardsContable">
    <div class="card"><div class="label">Ingresos</div><div class="value positive" id="kpiIngresos">—</div></div>
    <div class="card"><div class="label">Egresos</div><div class="value negative" id="kpiEgresos">—</div></div>
    <div class="card"><div class="label">Saldo</div><div class="value" id="kpiSaldo">—</div></div>
    <div class="card"><div class="label">Facturas</div><div class="value" id="kpiFact">—</div></div>
    <div class="card"><div class="label">Facturas pendientes</div><div class="value negative" id="kpiFactPend">—</div></div>
    <div class="card"><div class="label">Órdenes pendientes</div><div class="value" id="kpiOV">—</div></div>
  </div>
  <div class="chart-grid">
    <div class="chart-box">
      <h3>Ingresos / Egresos</h3>
      <canvas id="chartIE"></canvas>
    </div>
    <div class="chart-box">
      <h3>Últimas órdenes de venta</h3>
      <div class="table-wrap" style="box-shadow:none;">
        <table class="data" id="tblUltimasOrdenes">
          <thead>
            <tr><th>Número</th><th>Cliente</th><th>Estado</th><th>Total</th></tr>
          </thead>
          <tbody><tr><td colspan="4" class="empty">Cargando…</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>

  <?php elseif ($isCajero): ?>
  <!-- ═══════════ CAJERO ═══════════ -->
  <div class="cards" id="cardsCajero">
    <div class="card"><div class="label">Órdenes de hoy</div><div class="value" id="kpiHoy">—</div></div>
    <div class="card"><div class="label">Órdenes pendientes</div><div class="value" id="kpiOV">—</div></div>
    <div class="card"><div class="label">Facturas</div><div class="value" id="kpiFact">—</div></div>
    <div class="card"><div class="label">Productos</div><div class="value" id="kpiProd">—</div></div>
  </div>
  <div class="chart-grid">
    <div class="chart-box" style="grid-column:1/-1;">
      <h3>Últimas órdenes de venta</h3>
      <div class="table-wrap" style="box-shadow:none;">
        <table class="data" id="tblUltimasOrdenes">
          <thead>
            <tr><th>Número</th><th>Cliente</th><th>Estado</th><th>Total</th></tr>
          </thead>
          <tbody><tr><td colspan="4" class="empty">Cargando…</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>

  <?php elseif ($isBodeguero): ?>
  <!-- ═══════════ BODEGUERO ═══════════ -->
  <div class="cards" id="cardsBodega">
    <div class="card"><div class="label">Productos activos</div><div class="value" id="kpiProd">—</div></div>
    <div class="card"><div class="label">Alertas stock</div><div class="value negative" id="kpiLow">—</div></div>
    <div class="card"><div class="label">Compras solicitadas</div><div class="value" id="kpiCompSol">—</div></div>
    <div class="card"><div class="label">Por entregar</div><div class="value" id="kpiCompEnt">—</div></div>
    <div class="card"><div class="label">Proveedores</div><div class="value" id="kpiProv">—</div></div>
  </div>
  <div class="chart-grid">
    <div class="chart-box">
      <h3>Productos con stock bajo</h3>
      <div class="table-wrap" style="box-shadow:none;">
        <table class="data" id="tblBajoStock">
          <thead>
            <tr><th>Código</th><th>Producto</th><th>Stock</th><th>Mínimo</th></tr>
          </thead>
          <tbody><tr><td colspan="4" class="empty">Cargando…</td></tr></tbody>
        </table>
      </div>
    </div>
    <div class="chart-box">
      <h3>Últimas órdenes de compra</h3>
      <div class="table-wrap" style="box-shadow:none;">
        <table class="data" id="tblUltimasCompras">
          <thead>
            <tr><th>Número</th><th>Proveedor</th><th>Estado</th><th>Total</th></tr>
          </thead>
          <tbody><tr><td colspan="4" class="empty">Cargando…</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>

  <?php elseif ($isSoporte): ?>
  <!-- ═══════════ SOPORTE ═══════════ -->
  <div class="cards" id="cardsSoporte">
    <div class="card"><div class="label">Usuarios activos</div><div class="value" id="kpiUsers">—</div></div>
    <div class="card">
      <div class="label">Tu rol</div>
      <div class="value" style="font-size:16px;"><?= htmlspecialchars($roleLabel) ?></div>
    </div>
  </div>
  <div class="chart-box" style="margin-top:8px;">
    <h3>Bienvenido al panel de soporte</h3>
    <p style="color:#666;font-size:14px;margin-top:8px;line-height:1.5;">
      Desde aquí puedes consultar usuarios y gestionar tickets de soporte.
      Usa el menú lateral para ir a <strong>Soporte</strong> o <strong>Usuarios</strong>.
    </p>
  </div>
  <?php endif; ?>
</main>

<script>
const ROLE = <?= json_encode($rol) ?>;
const fmt = n => new Intl.NumberFormat('es-CO', {
  style: 'currency', currency: 'COP', maximumFractionDigits: 0
}).format(n || 0);

function setText(id, val) {
  const el = document.getElementById(id);
  if (el) el.textContent = val;
}

function barOptions() {
  return {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: (v) => new Intl.NumberFormat('es-CO', {
            notation: 'compact', maximumFractionDigits: 1
          }).format(v)
        },
        grid: { color: '#f0f0f0' }
      },
      x: { grid: { display: false } }
    }
  };
}

function renderOrdenes(lista) {
  const tbody = document.querySelector('#tblUltimasOrdenes tbody');
  if (!tbody) return;
  tbody.innerHTML = lista.length
    ? lista.map(o => `
        <tr>
          <td>${o.numero || ('#' + o.id)}</td>
          <td>${o.cliente_nombre || '—'}</td>
          <td><span class="badge badge-${o.estado}">${o.estado}</span></td>
          <td>${fmt(o.total)}</td>
        </tr>`).join('')
    : '<tr><td colspan="4" class="empty">Sin órdenes recientes</td></tr>';
}

function renderCompras(lista) {
  const tbody = document.querySelector('#tblUltimasCompras tbody');
  if (!tbody) return;
  tbody.innerHTML = lista.length
    ? lista.map(o => `
        <tr>
          <td>${o.numero || ('#' + o.id)}</td>
          <td>${o.proveedor_nombre || '—'}</td>
          <td><span class="badge badge-${o.estado}">${o.estado}</span></td>
          <td>${fmt(o.total)}</td>
        </tr>`).join('')
    : '<tr><td colspan="4" class="empty">Sin compras recientes</td></tr>';
}

function renderBajoStock(lista) {
  const tbody = document.querySelector('#tblBajoStock tbody');
  if (!tbody) return;
  tbody.innerHTML = lista.length
    ? lista.map(p => `
        <tr>
          <td>${p.codigo}</td>
          <td>${p.nombre}</td>
          <td class="value negative" style="font-size:14px;font-weight:600;">${p.stock} ${p.unidad || ''}</td>
          <td>${p.stock_minimo}</td>
        </tr>`).join('')
    : '<tr><td colspan="4" class="empty">Sin alertas de stock</td></tr>';
}

function chartIE(d) {
  const canvas = document.getElementById('chartIE');
  if (!canvas) return;
  new Chart(canvas, {
    type: 'bar',
    data: {
      labels: ['Ingresos', 'Egresos'],
      datasets: [{
        data: [d.ingresos, d.egresos],
        backgroundColor: ['#57CE23', '#F02222'],
        borderWidth: 0,
        borderRadius: 12,
        borderSkipped: false,
        maxBarThickness: 80
      }]
    },
    options: barOptions()
  });
}

function chartEstados(d) {
  const canvas = document.getElementById('chartEstados');
  if (!canvas) return;
  const e = d.ordenes_por_estado || {};
  new Chart(canvas, {
    type: 'doughnut',
    data: {
      labels: ['Pendiente', 'Facturado', 'Cobrado', 'Anulado'],
      datasets: [{
        data: [e.pendiente || 0, e.facturado || 0, e.cobrado || 0, e.anulado || 0],
        backgroundColor: ['#fbbf24', '#3b82f6', '#22c55e', '#ef4444'],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { position: 'bottom' } }
    }
  });
}

async function load() {
  try {
    const res = await ContaAPI.get('informes/dashboard');
    const d = res.data || {};

    // KPIs comunes (solo se llenan si el elemento existe en el DOM del rol)
    setText('kpiIngresos', fmt(d.ingresos));
    setText('kpiEgresos', fmt(d.egresos));
    setText('kpiSaldo', fmt(d.saldo));
    setText('kpiProd', d.productos);
    setText('kpiLow', d.alertas_stock);
    setText('kpiOV', d.ordenes_pendientes);
    setText('kpiFact', d.facturas);
    setText('kpiFactPend', d.facturas_pendientes);
    setText('kpiHoy', d.ordenes_hoy);
    setText('kpiCompSol', d.compras_solicitadas);
    setText('kpiCompEnt', d.compras_por_entregar);
    setText('kpiProv', d.proveedores);
    setText('kpiUsers', d.usuarios_activos);
    setText('kpiFactOV', (d.ordenes_por_estado || {}).facturado);
    setText('kpiCobr', (d.ordenes_por_estado || {}).cobrado);

    // Secciones según rol
    if (['administrador', 'gerente', 'contable'].includes(ROLE)) {
      chartIE(d);
      renderOrdenes(d.ultimas_ordenes || []);
    } else if (ROLE === 'jefe') {
      chartEstados(d);
      renderOrdenes(d.ultimas_ordenes || []);
    } else if (ROLE === 'cajero') {
      renderOrdenes(d.ultimas_ordenes || []);
    } else if (ROLE === 'bodeguero') {
      renderBajoStock(d.productos_bajo_stock || []);
      renderCompras(d.ultimas_compras || []);
    }
    // soporte: solo KPIs de texto
  } catch (e) {
    console.error(e);
  }
}

load();
</script>
</body>
</html>
