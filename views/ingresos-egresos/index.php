<?php
require __DIR__ . '/../layouts/auth_check.php';

if (!RolePermissions::can($currentUser['rol'], 'ingresos_egresos', 'read')) {
    header('Location: /ContaTech1/views/dashboard/index.php'); exit;
}

$pageTitle = 'Ingresos / Egresos';
$currentRoute = 'ingresos-egresos';

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';

$canCreate = RolePermissions::can($currentUser['rol'], 'ingresos_egresos', 'create');

?>
<main class="main-content">
    <div class="header">
        <h1>Control de Ingresos y Egresos</h1>
    </div>
    <div class="cards" id="totales">
        <div class="card">
            <div class="label">Ingresos</div>
            <div class="value positive" id="tIng">—</div>
        </div>
        <div class="card">
            <div class="label">Egresos</div>
            <div class="value negative" id="tEgr">—</div>
        </div>
        <div class="card">
            <div class="label">Saldo</div>
            <div class="value" id="tSal">—</div>
        </div>
    </div>
    <div class="toolbar">
        <span></span>
        <?php if ($canCreate): ?>
            <div>
                <button class="btn btn-success" onclick="reg('ingreso')">+ Ingreso</button>
                <button class="btn btn-danger" onclick="reg('egreso')">+ Egreso</button>
            </div>
        <?php endif; ?>
    </div>
    <div class="table-wrap">
        <table class="data" id="tbl">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
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
        const r=await ContaAPI.get('movimientos');
        document.getElementById('tIng').textContent=fmt(r.data.totales.ingresos);
        document.getElementById('tEgr').textContent=fmt(r.data.totales.egresos);
        document.getElementById('tSal').textContent=fmt(r.data.totales.saldo);
        document.querySelector('#tbl tbody').innerHTML=r.data.movimientos.map(m=>`
            <tr>
            <td>${m.fecha}</td>
            <td>
                <span class="badge badge-${m.tipo==='ingreso'?'cobrado':'anulado'}">${m.tipo}</span>
            </td>
            <td>${m.descripcion}</td>
            <td>${m.categoria||'—'}</td>
            <td style="color:${m.tipo==='ingreso'?'#16a34a':'#dc2626'}">${fmt(m.monto)}</td>
            </tr>`
        ).join('');
    }
    async function reg(tipo){
        const desc=prompt('Descripción:');
        const monto=prompt('Monto:');
        const cat=prompt('Categoría:','general');
        if(!desc||!monto) {
            return;
        }
        await ContaAPI.post('movimientos',{
            tipo,
            descripcion:desc,
            monto:+monto,
            categoria:cat,
            fecha:new Date().toISOString().slice(0,10)
        });
        load();
    }
    load();
</script>
</body>
</html>
