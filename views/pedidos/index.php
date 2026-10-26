<?php
require __DIR__.'/../layouts/auth_check.php';
if(!RolePermissions::can($currentUser['rol'],'pedidos','read')){
    header('Location:/ContaTech1/views/dashboard/index.php');
    exit;
}

$pageTitle='Pedidos';
$currentRoute='pedidos';

require __DIR__.'/../layouts/header.php';
require __DIR__.'/../layouts/sidebar.php';

$canCreate=RolePermissions::can($currentUser['rol'],'pedidos','create');
$canCancel=RolePermissions::can($currentUser['rol'],'pedidos','cancel');

?>
<main class="main-content">

<style>
  .ticket {
    width: 80mm;
    max-width: 100%;
    margin: 0 auto;
    background: #fff;
    padding: 12px;
    border-radius: 8px;
    font-family: "Courier New", Courier, monospace;
    font-size: 12px;
    color: #111;
  }
  .ticket-title { font-size: 16px; font-weight: 700; text-align: center; }
  .ticket-sub, .ticket-num, .ticket-estado, .ticket-footer { text-align: center; }
  .ticket-num { font-weight: 700; margin-top: 4px; }
  .ticket-estado { margin: 4px 0 8px; text-transform: uppercase; font-size: 11px; }
  .ticket-sep { text-align: center; color: #666; margin: 6px 0; }
  .ticket-row { display: flex; justify-content: space-between; gap: 8px; margin: 2px 0; }
  .ticket-row.total { font-size: 14px; font-weight: 700; margin-top: 6px; }
  .ticket-items { width: 100%; border-collapse: collapse; }
  .ticket-items th, .ticket-items td { padding: 3px 0; vertical-align: top; }
  .ticket-items .r { text-align: right; white-space: nowrap; }
  .ticket-actions { display: flex; gap: 8px; justify-content: center; margin-top: 16px; }

  @media print {
    @page { size: 80mm auto; margin: 0; }
    body * { visibility: hidden !important; }
    #modalOrden, #modalOrden * { visibility: visible !important; }
    #modalOrden {
      position: static !important;
      background: none !important;
      padding: 0 !important;
      display: block !important;
    }
    .ticket {
      width: 80mm;
      box-shadow: none;
      border-radius: 0;
      padding: 4mm;
    }
    .no-print { display: none !important; }
  }
</style>


</style>

    <div class="header">
        <h1>Órdenes de Venta</h1>
    </div>
    <div class="toolbar">
        <span>pendiente → facturado → cobrado | anulado</span>
        <?php 
            if($canCreate):?>
                <button class="btn btn-primary" onclick="openNew()">+ Nueva orden</button>
            <?php endif;
        ?>
    </div>
    <div class="table-wrap">
        <table class="data" id="tbl">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    <!-- Modal orden de venta (ticket) -->
<div id="modalOrden" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:300;overflow:auto;padding:16px;">
  <div id="ticketPrint" class="ticket">
    <div class="ticket-header">
      <div class="ticket-title">ContaTech</div>
      <div class="ticket-sub">Orden de venta</div>
      <div id="tk_numero" class="ticket-num">—</div>
      <div id="tk_fecha">—</div>
      <div id="tk_estado" class="ticket-estado">—</div>
    </div>

    <div class="ticket-sep">--------------------------------</div>

    <div class="ticket-row">
      <span>Cliente:</span>
      <span id="tk_cliente">—</span>
    </div>
    <div class="ticket-row">
      <span>Atendido por:</span>
      <span id="tk_usuario">—</span>
    </div>

    <div class="ticket-sep">--------------------------------</div>

    <table class="ticket-items">
      <thead>
        <tr>
          <th>Producto</th>
          <th class="r">Cant</th>
          <th class="r">Precio</th>
          <th class="r">Subt</th>
        </tr>
      </thead>
      <tbody id="tk_detalle"></tbody>
    </table>

    <div class="ticket-sep">--------------------------------</div>

    <div class="ticket-totals">
      <div class="ticket-row"><span>Subtotal</span><strong id="tk_subtotal">—</strong></div>
      <div class="ticket-row"><span>IVA (19%)</span><strong id="tk_impuestos">—</strong></div>
      <div class="ticket-row total"><span>TOTAL</span><strong id="tk_total">—</strong></div>
    </div>

    <div class="ticket-sep">--------------------------------</div>
    <div class="ticket-footer">¡Gracias por su compra!</div>

    <div class="no-print ticket-actions">
      <button class="btn btn-secondary" onclick="cerrarOrden()">Cerrar</button>
      <button class="btn btn-primary" onclick="imprimirTermica()">Imprimir térmica</button>
    </div>
  </div>
</div>

</main>
<script>
    const canCancel=<?=$canCancel?'true':'false'?>;
    const fmt =n=>new Intl.NumberFormat(
        'es-CO',{
            style:'currency',
            currency:'COP',
            maximumFractionDigits:0
        }).format(n||0);
    
    async function load(){
        const r=await ContaAPI.get('pedidos');
        document.querySelector('#tbl tbody').innerHTML=r.data.map(o=>`
            <tr>
                <td>${o.numero}</td>
                <td>${o.cliente_nombre||'—'}</td>
                <td>${fmt(o.total)}</td>
                <td><span class="badge badge-${o.estado}">${o.estado}</span></td>
                <td>${(o.created_at||'').slice(0,10)}</td>
                <td><button class="btn btn-sm btn-secondary" onclick="verOrden(${o.id})">Ver / Imprimir</button> 
                    ${canCancel&&o.estado!=='anulado'&&o.estado!=='cobrado'?`
                        <button class="btn btn-sm btn-danger" onclick="cancelar(${o.id})">Anular</button>`:''}
                </td></tr>`).join('');
    }
    
    async function openNew() {
      const pr = await ContaAPI.get('productos');
      const products = pr.data.filter(p => p.activo == 1);
      if (!products.length) {
        alert('Sin productos');
        return;
      }

      const pid = prompt(
        'ID producto:\n' + products.map(p => p.id + ': ' + p.nombre + ' ($' + p.precio + ')').join('\n')
      );
      if (!pid) return;

      const cant = prompt('Cantidad:', '1');
      const prod = products.find(p => String(p.id) === String(pid));
      if (!prod) {
        alert('Producto no encontrado. Usa el ID de la lista.');
        return;
      }

      try {
        const res = await ContaAPI.post('pedidos', {
          cliente_id: 1, // debe existir en clientes
          items: [{
            producto_id: +pid,
            cantidad: +cant || 1,
            precio_unitario: +prod.precio
          }]
        });
        alert('Orden creada: ' + (res.data?.numero || 'OK'));
        await load();
      } catch (e) {
        alert('Error al crear: ' + e.message);
      }
    }
    
    async function ver(id){
        const r=await ContaAPI.get('pedidos/'+id);
        const o=r.data;
        alert(`Orden ${o.numero}\nEstado: ${o.estado}\nTotal: ${fmt(o.total)}\n`+(o.detalle||[]).map(d=>`- ${d.producto_nombre} x${d.cantidad}`).join('\n'));
    }
    
    async function cancelar(id){
        if(!confirm('¿Anular y devolver stock?')){
            return;
        }
        await ContaAPI.post('pedidos/'+id+'/cancelar');
        load();
    }

async function verOrden(id) {
  try {
    const r = await ContaAPI.get('pedidos/' + id); // o la ruta que uses
    const o = r.data;

    document.getElementById('tk_numero').textContent = o.numero || ('#' + o.id);
    document.getElementById('tk_fecha').textContent =
      (o.created_at || o.fecha || '').toString().slice(0, 19).replace('T', ' ');
    document.getElementById('tk_estado').textContent = o.estado || '—';
    document.getElementById('tk_cliente').textContent =
      o.cliente_nombre || ('Cliente #' + (o.cliente_id || '—'));
    document.getElementById('tk_usuario').textContent =
      o.usuario_rol || ('#' + (o.usuario_id || '—'));

    const det = o.detalle || [];
    document.getElementById('tk_detalle').innerHTML = det.length
      ? det.map(d => `
          <tr>
            <td>${d.producto_nombre || d.descripcion || '—'}</td>
            <td class="r">${d.cantidad}</td>
            <td class="r">${fmt(d.precio_unitario)}</td>
            <td class="r">${fmt(d.subtotal)}</td>
          </tr>
        `).join('')
      : '<tr><td colspan="4" style="text-align:center;">Sin productos</td></tr>';

    document.getElementById('tk_subtotal').textContent = fmt(o.subtotal);
    document.getElementById('tk_impuestos').textContent = fmt(o.impuestos);
    document.getElementById('tk_total').textContent = fmt(o.total);

    document.getElementById('modalOrden').style.display = 'block';
  } catch (e) {
    alert(e.message);
  }
}

function cerrarOrden() {
  document.getElementById('modalOrden').style.display = 'none';
}

function imprimirTermica() {
  window.print(); // elige la impresora térmica en el diálogo del sistema
}
    
    load();
</script>
</body>
</html>
