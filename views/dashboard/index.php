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
            <h3>Ingresos / Egresos</h3>
            <canvas id="chartIE" height="100%"></canvas>
        </div>
    </div>

</main>

<script>
const fmt=n=>new Intl.NumberFormat(
    'es-CO',{
        style:'currency',
        currency:'COP',
        maximumFractionDigits:0
    }).format(n||0);

async function load(){ 
    try{
        const res=await ContaAPI.get('informes/dashboard'); 
        const d=res.data;
        kpiIngresos.textContent=fmt(d.ingresos); 
        kpiEgresos.textContent=fmt(d.egresos); 
        kpiSaldo.textContent=fmt(d.saldo);
        kpiProd.textContent=d.productos; 
        kpiLow.textContent=d.alertas_stock; 
        kpiOV.textContent=d.ordenes_pendientes;

        new Chart(document.getElementById('chartIE'),{
            type:'bar',
            data:{
                labels:['Ingresos','Egresos'],
                datasets:[
                    {
                        data:[d.ingresos,d.egresos],
                        backgroundColor:['#16a34a','#dc2626']
                    }
                ]
            },
            options:{
                responsive:true,
                plugins:{
                    legend:{
                        display:false
                    }
                },
                scales:{
                    y:{
                        beginAtZero:true
                    }
                }
            }
        });
    } catch(e){
        console.error(e);
    } 
}

load();

</script>
</body>
</html>
