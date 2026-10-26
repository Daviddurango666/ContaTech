<?php
require __DIR__.'/../layouts/auth_check.php'; 
$pageTitle='Dashboard'; 
$currentRoute='dashboard';
require __DIR__.'/../layouts/header.php'; 
require __DIR__.'/../layouts/sidebar.php';
?>
<main class="main-content">
    
    <div class="header">
        <h1>Dashboard — <?= htmlspecialchars($currentUser['nombre']) ?></h1>
        <div class="user-info">
            <span><?= htmlspecialchars($appCfg['roles'][$currentUser['rol']]??$currentUser['rol']) ?></span>
        </div>
    </div>

    <div class="cards">
        <div class="card">
            <div class="label">Ingresos</div>
            <div class="value positive" id="kpiIngresos">—</div>
        </div>
        
        <div class="card">
            <div class="label">Egresos</div>
            <div class="value negative" id="kpiEgresos">—</div>
        </div>
        
        <div class="card">
            <div class="label">Saldo</div>
            <div class="value" id="kpiSaldo">—</div>
        </div>
        
        <div class="card">
            <div class="label">Productos</div>
            <div class="value" id="kpiProd">—</div>
        </div>
        
        <div class="card">
            <div class="label">Alertas stock</div>
            <div class="value negative" id="kpiLow">—</div>
        </div>
        
        <div class="card">
            <div class="label">Órdenes pendientes</div>
            <div class="value" id="kpiOV">—</div>
        </div>
    </div>

    <div class="chart-grid">
        <!-- Izquierda: barras -->
        <div class="chart-box">
            <h3>Ingresos / Egresos</h3>
            <canvas id="chartIE"></canvas>
        </div>

        <!-- Derecha: últimas órdenes -->
        <div class="chart-box">
            <h3>Últimas órdenes de venta</h3>
            <div class="table-wrap" style="box-shadow:none;border-radius:0;">
            <table class="data" id="tblUltimasOrdenes">
                <thead>
                <tr>
                    <th>Número</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <tr><td colspan="4" class="empty">Cargando…</td></tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>

</main>
<script>
const fmt = n => new Intl.NumberFormat('es-CO', {
  style: 'currency',
  currency: 'COP',
  maximumFractionDigits: 0
}).format(n || 0);

function barOptions() {
  return {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: (ctx) => fmt(ctx.raw)
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: (v) => new Intl.NumberFormat('es-CO', {
            notation: 'compact',
            maximumFractionDigits: 1
          }).format(v)
        },
        grid: { color: '#f0f0f0' }
      },
      x: { grid: { display: false } }
    }
  };
}

async function load() {
  try {
    const res = await ContaAPI.get('informes/dashboard');
    const d = res.data;

    kpiIngresos.textContent = fmt(d.ingresos);
    kpiEgresos.textContent = fmt(d.egresos);
    kpiSaldo.textContent = fmt(d.saldo);
    kpiProd.textContent = d.productos;
    kpiLow.textContent = d.alertas_stock;
    kpiOV.textContent = d.ordenes_pendientes;

    // 1) Una sola gráfica de barras: Ingresos + Egresos
    new Chart(document.getElementById('chartIE'), {
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

    // Tabla últimas 5 órdenes
    const lista = d.ultimas_ordenes || [];
    const tbody = document.querySelector('#tblUltimasOrdenes tbody');
    tbody.innerHTML = lista.length
      ? lista.map(o => `
          <tr>
            <td>${o.numero || ('#' + o.id)}</td>
            <td>${o.cliente_nombre || '—'}</td>
            <td><span class="badge badge-${o.estado}">${o.estado}</span></td>
            <td>${fmt(o.total)}</td>
          </tr>
        `).join('') : '<tr><td colspan="4" class="empty">Sin órdenes recientes</td></tr>';
  } catch (e) {
    console.error(e);
  }
}

load();
</script>
</body>
</html>
