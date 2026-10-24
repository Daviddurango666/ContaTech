<?php
require __DIR__ . '/../layouts/auth_check.php';
if (!RolePermissions::can($currentUser['rol'], 'informes', 'view')) {
    header('Location: /ContaTech1/views/dashboard/index.php'); exit;
}
$pageTitle = 'Informes';
$currentRoute = 'informes';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>
<main class="main-content">
  <div class="header"><h1>Informes Financieros</h1></div>
  <div class="toolbar">
    <div>
      <button class="btn btn-secondary" onclick="loadReport('balance')">Balance</button>
      <button class="btn btn-secondary" onclick="loadReport('estado-resultados')">Estado de resultados</button>
      <button class="btn btn-secondary" onclick="loadReport('flujo-caja')">Flujo de caja</button>
      <button class="btn btn-secondary" onclick="loadReport('impuestos')">Impuestos</button>
    </div>
  </div>
  <div class="form-card" id="reportBox" style="max-width:100%;">
    <p class="empty">Seleccione un informe</p>
  </div>
</main>
<script>
const fmt=n=>new Intl.NumberFormat(
  'es-CO',{
    style:'currency',
    currency:'COP',
    maximumFractionDigits:0
  }).format(n||0);

async function loadReport(tipo){
  const box=document.getElementById('reportBox');
  box.innerHTML='<p>Cargando...</p>';
  try{
    const r=await ContaAPI.get('informes/'+tipo);
    const d=r.data;
    if(tipo==='balance'){
      box.innerHTML=`<h2>Balance general — ${d.fecha}</h2>
        <table class="data">
          <tr>
            <th>Activos</th>
            <td>
            </td>
          </tr>
          <tr>
            <td>Caja / Bancos</td>
            <td>${fmt(d.activos.caja_bancos)}</td>
          </tr>
          <tr>
            <td>Inventario</td>
            <td>${fmt(d.activos.inventario)}</td>
          </tr>
          <tr>
            <th>Total activos</th>
            <th>${fmt(d.activos.total)}</th>
          </tr>
          <tr>
            <th>Patrimonio</th>
            <th>${fmt(d.patrimonio)}</th>
          </tr>
        </table>`;
    } else if(tipo==='estado-resultados'){
        box.innerHTML=`<h2>Estado de resultados — ${d.fecha}</h2>
          <table class="data">
            <tr>
              <td>Ventas</td>
              <td>${fmt(d.ingresos.ventas)}</td>
            </tr>
            <tr>
              <td>Otros ingresos</td>
              <td>${fmt(d.ingresos.otros)}</td>
            </tr>
            <tr>
              <td>Costo de ventas</td>
              <td>${fmt(d.costo_ventas)}</td>
            </tr>
            <tr>
              <td>Utilidad bruta</td>
              <td>${fmt(d.utilidad_bruta)}</td>
            </tr>
            <tr>
              <td>Gastos operativos</td>  
              <td>${fmt(d.gastos_operativos)}</td>
            </tr>
            <tr>
              <th>Utilidad neta</th>
              <th>${fmt(d.utilidad_neta)}</th>
            </tr>
          </table>`;
    } else if(tipo==='flujo-caja'){
        let rows=(d.detalle||[]).map(
          x=>`
            <tr>
              <td>${x.fecha}</td>
              <td>${fmt(x.ingresos)}</td>
              <td>${fmt(x.egresos)}</td>
            </tr>
          `).join('');

        box.innerHTML=`
          <h2>Flujo de caja (${d.desde} — ${d.hasta})</h2>
          <p>Total ingresos: ${fmt(d.totales.ingresos)} | Total egresos: ${fmt(d.totales.egresos)}</p>
          <table class="data">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Ingresos</th>
                <th>Egresos</th>
              </tr>
            </thead>
            <tbody>
              ${rows||'<tr><td colspan="3">Sin datos</td></tr>'}
            </tbody>
          </table>`;
    } else if(tipo==='impuestos'){
        box.innerHTML=`
          <h2>Reporte de impuestos — ${d.periodo}</h2>
          <p>IVA generado: <strong>${fmt(d.iva_generado)}</strong></p>
          <p style="color:#666;font-size:13px;">${d.nota||''}</p>
        `;
    }
  } catch(e){ 
    box.innerHTML='<div class="alert alert-error">'+e.message+'</div>'; 
  }
}
</script>
</body>
</html>
