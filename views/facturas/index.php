<?php
require __DIR__ . '/../layouts/auth_check.php';

if (!RolePermissions::can($currentUser['rol'], 'facturas', 'read')) {
    header('Location: /ContaTech1/views/dashboard/index.php'); exit;
}

$pageTitle = 'Facturación';
$currentRoute = 'facturas';

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';

$canCreate = RolePermissions::can($currentUser['rol'], 'facturas', 'create');
$canProcess = RolePermissions::can($currentUser['rol'], 'facturas', 'process');
$canDian = RolePermissions::can($currentUser['rol'], 'facturas', 'dian');

?>
<style>
.fe-doc {
  max-width: 720px;
  margin: 0 auto;
  background: #fff;
  border-radius: 12px;
  padding: 28px 32px;
  box-shadow: 0 8px 30px rgba(0,0,0,.15);
  font-family: Arial, Helvetica, sans-serif;
  color: #111;
  font-size: 13px;
}
.fe-head {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  border-bottom: 2px solid #111;
  padding-bottom: 14px;
  margin-bottom: 18px;
}
.fe-brand { font-size: 22px; font-weight: 700; }
.fe-muted { color: #666; font-size: 12px; margin-top: 2px; }
.fe-head-right { text-align: right; }
.fe-numero { font-size: 18px; font-weight: 700; }
.fe-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 18px;
}
.fe-label { font-weight: 700; margin-bottom: 4px; }
.fe-cufe {
  word-break: break-all;
  font-size: 11px;
  color: #444;
  background: #f7f7f7;
  padding: 6px 8px;
  border-radius: 6px;
  margin-top: 4px;
}
.fe-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
.fe-table th, .fe-table td {
  padding: 8px 10px;
  border-bottom: 1px solid #eee;
  text-align: left;
}
.fe-table th { background: #f5f5f5; font-size: 12px; }
.fe-table .r, .r { text-align: right; white-space: nowrap; }
.fe-totales { max-width: 260px; margin-left: auto; }
.fe-row { display: flex; justify-content: space-between; padding: 4px 0; }
.fe-total {
  border-top: 2px solid #111;
  margin-top: 6px;
  padding-top: 10px;
  font-size: 16px;
}
.fe-legal {
  margin-top: 20px;
  font-size: 11px;
  color: #777;
  text-align: center;
}
.fe-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  margin-top: 22px;
  flex-wrap: wrap;
}

/* Impresión carta / PDF */
@media print {
  body * { visibility: hidden !important; }
  #modalFactura, #modalFactura * { visibility: visible !important; }
  #modalFactura {
    position: static !important;
    background: none !important;
    padding: 0 !important;
    display: block !important;
  }
  .fe-doc {
    box-shadow: none !important;
    border-radius: 0 !important;
    max-width: 100% !important;
    padding: 0 !important;
  }
  .no-print { display: none !important; }
}

/* Ticket térmico 80mm */
body.thermal-print .fe-doc {
  width: 80mm !important;
  max-width: 80mm !important;
  padding: 4mm !important;
  font-size: 11px !important;
  font-family: "Courier New", monospace !important;
}
body.thermal-print .fe-grid { grid-template-columns: 1fr !important; }
body.thermal-print .fe-brand { font-size: 14px !important; }
body.thermal-print .fe-numero { font-size: 13px !important; }
body.thermal-print .fe-totales { max-width: 100% !important; }
body.thermal-print .fe-table th { background: none !important; }

@media print {
  body.thermal-print {
    width: 80mm;
  }
  body.thermal-print #modalFactura {
    width: 80mm;
  }
}
</style>


<main class="main-content">
    <div class="header">
        <h1>Facturación Electrónica (DIAN)</h1>
    </div>
    <div class="toolbar">
        <span>Generar desde orden de venta · Contabilizar · Enviar DIAN</span>
        <?php if ($canCreate): ?>
            <button class="btn btn-primary" onclick="generar()">+ Generar factura desde orden</button>
        <?php endif; ?>
    </div>
    <div class="table-wrap">
        <table class="data" id="tbl">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>CUFE</th>
                    <th>Contable</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <!-- Modal Factura electrónica DIAN -->
    <div id="modalFactura" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:300;overflow:auto;padding:20px;">
        <div id="facturaPrint" class="fe-doc">

            <div class="fe-head">
            <div>
                <div class="fe-brand">ContaTech</div>
                <div class="fe-muted">Factura electrónica de venta · DIAN</div>
                <div class="fe-muted">NIT: 900000000-1</div>
            </div>
            <div class="fe-head-right">
                <div class="fe-numero" id="fe_numero">—</div>
                <div class="fe-muted" id="fe_fecha">—</div>
                <div><span class="badge" id="fe_estado">—</span></div>
            </div>
            </div>

            <div class="fe-grid">
            <div>
                <div class="fe-label">Adquiriente / Cliente</div>
                <div id="fe_cliente">—</div>
                <div class="fe-muted" id="fe_cliente_doc"></div>
            </div>
            <div>
                <div class="fe-label">Información DIAN</div>
                <div>CUFE:</div>
                <div class="fe-cufe" id="fe_cufe">No generado</div>
                <div class="fe-muted" style="margin-top:6px;">Contabilizada: <span id="fe_contable">—</span></div>
                <div class="fe-muted">Usuario: <span id="fe_usuario">—</span></div>
            </div>
            </div>

            <table class="fe-table">
            <thead>
                <tr>
                <th>Descripción</th>
                <th class="r">Cant.</th>
                <th class="r">V. unitario</th>
                <th class="r">Subtotal</th>
                </tr>
            </thead>
            <tbody id="fe_detalle"></tbody>
            </table>

            <div class="fe-totales">
            <div class="fe-row"><span>Subtotal</span><strong id="fe_subtotal">—</strong></div>
            <div class="fe-row"><span>IVA (19%)</span><strong id="fe_impuestos">—</strong></div>
            <div class="fe-row fe-total"><span>Total a pagar</span><strong id="fe_total">—</strong></div>
            </div>

            <div id="contenedor-qr"></div>

            <div class="fe-legal">
            Documento equivalente a factura electrónica de venta.
            Representación gráfica para consulta e impresión.
            </div>

            <div class="no-print fe-actions">
            <button type="button" class="btn btn-secondary" onclick="cerrarFactura()">Cerrar</button>
            <button type="button" class="btn btn-primary" onclick="imprimirFactura()">Imprimir / PDF</button>
            <button type="button" class="btn btn-secondary" onclick="imprimirTermicaFE()">Ticket térmica</button>
            </div>
        </div>
    </div>




</main>
<script>
const canProcess=<?= $canProcess?'true':'false' ?>, canDian=<?= $canDian?'true':'false' ?>;
const fmt=n=>new Intl.NumberFormat(
    'es-CO',{
        style:'currency',
        currency:'COP',
        maximumFractionDigits:0
    }).format(n||0);

async function load(){
  
    const r=await ContaAPI.get('facturas');
    const lista = r.data.slice().sort((a, b) => a.numero - b.numero); // ASC por id
    document.querySelector('#tbl tbody').innerHTML=lista.map(f=>`
        <tr>
            <td>${f.numero}</td>
            <td>${f.cliente_nombre||'—'}</td>
            <td>${fmt(f.total)}</td>
            <td>
                <span class="badge badge-${f.estado}">${f.estado}</span>
            </td>
            <td style="font-size:10px;max-width:120px;overflow:hidden;text-overflow:ellipsis;">${f.cufe?f.cufe.slice(0,16)+'…':'—'}</td>
            <td>${f.registro_contable==1?'Sí':'No'}</td>
            <td>
                <button class="btn btn-sm btn-secondary" onclick="ver(${f.id})">Ver</button>
                ${canProcess && !f.registro_contable?`<button class="btn btn-sm btn-secondary" onclick="procesar(${f.id})">Contabilizar</button>`:''}
                ${canDian && f.estado!=='enviada_dian'?`<button class="btn btn-sm btn-primary" onclick="dian(${f.id})">DIAN</button>`:''}
            </td>
            </tr>`
        ).join('')||'<tr><td colspan="7" class="empty">Sin facturas</td></tr>';
}

async function generar(){
    const ordenId=prompt('ID de la orden de venta a facturar:');
    if(!ordenId){
        return;
    }
    
    try{
        await ContaAPI.post('facturas/generar',{orden_id:+ordenId});
        load();
    }catch(e){
        alert(e.message);
    }
}

async function procesar(id){ 
    await ContaAPI.post('facturas/'+id+'/procesar'); 
    load(); 
}

async function dian(id){
    if(!confirm('¿Enviar factura electrónica a DIAN?')){
        return;
    }
    try{ 
        await ContaAPI.post('facturas/'+id+'/dian'); 
        alert('Enviada a DIAN'); 
        load(); 
    }catch(e){alert(e.message);}
}


async function ver(id) {
  try {
    const fac = await ContaAPI.get('facturas/' + id);
    const f = fac.data;

    // Limpiar QR anterior
    const contQr = document.getElementById('contenedor-qr');
    contQr.innerHTML = '';

    if (f.cufe) {
      const imagen_qr = document.createElement('img');
      imagen_qr.src =
        'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' +
        encodeURIComponent(f.cufe);
      imagen_qr.alt = 'Código QR CUFE';
      imagen_qr.style.display = 'block';
      imagen_qr.style.margin = '16px auto 0';
      contQr.appendChild(imagen_qr);
    }

    document.getElementById('fe_numero').textContent = f.numero || ('#' + f.id);
    document.getElementById('fe_fecha').textContent =
      f.fecha || (f.created_at || '').toString().slice(0, 10);

    const est = document.getElementById('fe_estado');
    est.textContent = f.estado || '—';
    est.className = 'badge badge-' + (f.estado || '');

    document.getElementById('fe_cliente').textContent =
      f.cliente_nombre || ('Cliente #' + (f.cliente_id || '—'));
    document.getElementById('fe_cliente_doc').textContent =
      f.cliente_documento
        ? (f.cliente_tipo_doc || 'Doc') + ' ' + f.cliente_documento
        : '';

    document.getElementById('fe_cufe').textContent =
      f.cufe || 'No generado (pendiente DIAN)';
    document.getElementById('fe_contable').textContent =
      f.registro_contable == 1 ? 'Sí' : 'No';
    document.getElementById('fe_usuario').textContent =
      f.usuario_nombre || ('#' + (f.usuario_id || '—'));

    const det = f.detalle || [];
    document.getElementById('fe_detalle').innerHTML = det.length
      ? det.map(d => `
          <tr>
            <td>${d.descripcion || '—'}</td>
            <td class="r">${d.cantidad}</td>
            <td class="r">${fmt(d.precio_unitario)}</td>
            <td class="r">${fmt(d.subtotal)}</td>
          </tr>
        `).join('')
      : '<tr><td colspan="4" style="text-align:center;color:#888;">Sin detalle</td></tr>';

    document.getElementById('fe_subtotal').textContent = fmt(f.subtotal);
    document.getElementById('fe_impuestos').textContent = fmt(f.impuestos);
    document.getElementById('fe_total').textContent = fmt(f.total);

    document.getElementById('modalFactura').style.display = 'block';
  } catch (e) {
    alert(e.message);
  }
}

function cerrarFactura() {
  document.getElementById('modalFactura').style.display = 'none';
  document.body.classList.remove('thermal-print');
}

function imprimirFactura() {
  document.body.classList.remove('thermal-print');
  window.print(); // carta / PDF
}

function imprimirTermicaFE() {
  document.body.classList.add('thermal-print');
  window.print(); // elige impresora térmica 80mm
  setTimeout(() => document.body.classList.remove('thermal-print'), 500);
}


load();

</script>
</body>
</html>
