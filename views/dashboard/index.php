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
        <div class="chart-box">
            <h3>Egresos</h3>
            <canvas id="chartEgresos" height="220"></canvas>
        </div>
        <div class="chart-box">
            <h3>Ingresos</h3>
            <canvas id="chartIngresos" height="220"></canvas>
        </div>
    </div>
</main>
<script>
const fmt = n => new Intl.NumberFormat('es-CO', {
    style: 'currency', currency: 'COP', maximumFractionDigits: 0
}).format(n || 0);

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
            x: { 
                grid: { 
                    display: false 
                } 
            }
        }
    };
}

function makeBarChart(canvasId, label, value, color) {
    return new Chart(document.getElementById(canvasId), {
        type: 'bar',
        data: {
            labels: [label],
            datasets: [{
                label: label,
                data: [value],
                backgroundColor: color,
                borderWidth: 0,
                borderRadius: 50,       // redondeo de la barra
                borderSkipped: false,   // redondea también la base
                maxBarThickness: 80
            }]
        },
        options: barOptions()
    });
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

        makeBarChart('chartIngresos', 'Ingresos', d.ingresos, '#16a34a');
        makeBarChart('chartEgresos', 'Egresos', d.egresos, '#dc2626');
    } catch (e) {
        console.error(e);
    }
}
load();
</script>
</body>
</html>
