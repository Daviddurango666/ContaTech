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

    <!-- MODAL NUEVA ORDEN -->
    <div id="modalOrdenFactura" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:300;overflow:auto;padding:20px;">
      <div style="max-width:820px;margin:0 auto;background:#fff;border-radius:12px;padding:24px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
          <h2 style="margin:0;font-size:18px;">Nueva orden de venta</h2>
          <button type="button" class="btn btn-secondary btn-sm" onclick="cerrarModal()">✕</button>
        </div>

        <div class="form-group">
          <label>Cliente (ID)</label>
          <input type="number" id="ord_cliente" value="1" min="1" style="width:120px;padding:8px;border:1px solid #ccc;border-radius:8px;">
          <span style="font-size:12px;color:#666;margin-left:8px;">Debe existir en la tabla clientes</span>
        </div>

        <div class="form-group">
          <label>Método de pago</label>
          <select id="ord_metodo" style="padding:8px;border-radius:8px;border:1px solid #ccc;">
            <option value="1">Efectivo</option>
            <option value="2">Tarjeta</option>
            <option value="3">Transferencia</option>
            <option value="4">Bold</option>
          </select>
        </div>

        <div style="display:flex;gap:8px;margin:12px 0;flex-wrap:wrap;align-items:center;">
          <input
            type="text"
            id="ord_buscar"
            placeholder="Buscar por ID o nombre..."
            style="flex:1;max-width:180px;padding:8px;border-radius:8px;border:1px solid #ccc;"
            oninput="filtrarProductos()"
          >
          <select id="ord_producto" style="flex:1;min-width:200px;padding:8px;border-radius:8px;border:1px solid #ccc;"></select>
          <input type="number" id="ord_cant" value="1" min="1" style="width:80px;padding:8px;border-radius:8px;border:1px solid #ccc;">
          <button type="button" class="btn btn-secondary" onclick="addItem()">+ Agregar</button>
        </div>

        <div class="table-wrap" style="box-shadow:none;">
          <table class="data" id="tblItems">
            <thead>
              <tr>
                <th>Producto</th>
                <th>Cant.</th>
                <th>P. unit.</th>
                <th>Subtotal</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

        <div style="text-align:right;margin-top:12px;font-size:14px; border: 1px solid #D9D9D9;">
          <div>Subtotal: <strong id="ord_subtotal">$0</strong></div>
          <div>IVA (19%): <strong id="ord_iva">$0</strong></div>
          <div style="font-size:18px;margin-top:4px;">Total: <strong id="ord_total">$0</strong></div>
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
          <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
          <button type="button" class="btn btn-primary" onclick="guardarOrden()">Guardar orden + factura + pago</button>
        </div>
      </div>
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
    
    let products = [];
    let cart = []; // { producto_id, nombre, cantidad, precio_unitario, subtotal }

    async function load() {
      const r = await ContaAPI.get('pedidos');
      const lista = (r.data || []).slice().sort((a, b) => b.id - a.id);
      document.querySelector('#tbl tbody').innerHTML = lista.length
        ? lista.map(o => `
            <tr>
              <td>${o.numero}</td>
              <td>${o.cliente_nombre || '—'}</td>
              <td>${fmt(o.total)}</td>
              <td><span class="badge badge-${o.estado}">${o.estado}</span></td>
              <td>${(o.created_at || '').toString().slice(0, 10)}</td>
              <td>
                <button class="btn btn-sm btn-secondary" onclick="verOrden(${o.id})">Ver / Imprimir</button>
                ${o.factura_id && o.estado_dian !== 'enviada_dian'
                  ? `<button class="btn btn-sm btn-primary" onclick="enviarDian(${o.factura_id})">DIAN</button>`
                  : ''}
              </td>
            </tr>
          `).join('')
        : '<tr><td colspan="6" class="empty">Sin órdenes</td></tr>';
    }

    async function openNew() {
      cart = [];
      renderCart();
      const pr = await ContaAPI.get('productos');
      products = (pr.data || []).filter(p => p.activo == 1);
      const sel = document.getElementById('ord_producto');
      sel.innerHTML = products.map(p =>
        `<option value="${p.id}" data-precio="${p.precio}">${p.nombre} — ${fmt(p.precio)} (stock ${p.stock})</option>`
      ).join('') || '<option value="">Sin productos</option>';
      document.getElementById('modalOrdenFactura').style.display = 'block';
    }

    function cerrarModal() {
      document.getElementById('modalOrdenFactura').style.display = 'none';
    }

    function renderProductSelect(lista) {
      const sel = document.getElementById('ord_producto');
      if (!lista.length) {
        sel.innerHTML = '<option value="">Sin coincidencias</option>';
        return;
      }
      sel.innerHTML = lista.map(p =>
        `<option value="${p.id}" data-precio="${p.precio}">
          #${p.id} — ${p.nombre} — ${fmt(p.precio)} (stock ${p.stock})
        </option>`
      ).join('');
    }

    function filtrarProductos() {
      const q = (document.getElementById('ord_buscar').value || '').trim().toLowerCase();

      if (!q) {
        renderProductSelect(products);
        return;
      }

      const filtrados = products.filter(p => {
        const idMatch = String(p.id) === q || String(p.id).includes(q);
        const nombreMatch = (p.nombre || '').toLowerCase().includes(q);
        const codigoMatch = (p.codigo || '').toLowerCase().includes(q);
        return idMatch || nombreMatch || codigoMatch;
      });

      renderProductSelect(filtrados);
    }

    function addItem() {
      const sel = document.getElementById('ord_producto');
      const id = +sel.value;
      const cant = Math.max(1, +document.getElementById('ord_cant').value || 1);
      const prod = products.find(p => p.id == id);
      if (!prod) return alert('Selecciona un producto');

      const existing = cart.find(c => c.producto_id === id);
      if (existing) {
        existing.cantidad += cant;
        existing.subtotal = existing.cantidad * existing.precio_unitario;
      } else {
        cart.push({
          producto_id: id,
          nombre: prod.nombre,
          cantidad: cant,
          precio_unitario: +prod.precio,
          subtotal: cant * +prod.precio
        });
      }
      renderCart();
    }

    function removeItem(i) {
      cart.splice(i, 1);
      renderCart();
    }

    function renderCart() {
      const sub = cart.reduce((s, c) => s + c.subtotal, 0);
      const iva = Math.round(sub * 0.19);
      const tot = sub + iva;

      document.querySelector('#tblItems tbody').innerHTML = cart.length
        ? cart.map((c, i) => `
            <tr>
              <td>${c.nombre}</td>
              <td>${c.cantidad}</td>
              <td>${fmt(c.precio_unitario)}</td>
              <td>${fmt(c.subtotal)}</td>
              <td><button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${i})">Quitar</button></td>
            </tr>
          `).join('')
        : '<tr><td colspan="5" class="empty">Agrega productos</td></tr>';

      document.getElementById('ord_subtotal').textContent = fmt(sub);
      document.getElementById('ord_iva').textContent = fmt(iva);
      document.getElementById('ord_total').textContent = fmt(tot);
    }

    async function guardarOrden() {
      if (!cart.length) return alert('Agrega al menos un producto');

      const cliente_id = +document.getElementById('ord_cliente').value || null;
      const metodo_pago_id = +document.getElementById('ord_metodo').value || 1;

      try {
        const res = await ContaAPI.post('pedidos', {
          cliente_id,
          metodo_pago_id,
          auto_factura: true,  // genera factura
          auto_pago: true,     // registra pago
          items: cart.map(c => ({
            producto_id: c.producto_id,
            cantidad: c.cantidad,
            precio_unitario: c.precio_unitario
          }))
        });
        alert('Orden + factura + pago creados: ' + (res.data?.orden?.numero || res.data?.numero || 'OK'));
        cerrarModal();
        await load();
      } catch (e) {
        alert('Error: ' + e.message);
      }
    }

    async function enviarDian(facturaId) {
      if (!confirm('¿Enviar factura electrónica a DIAN?')) return;
      try {
        await ContaAPI.post('facturas/' + facturaId + '/dian');
        alert('Enviada a DIAN');
        load();
      } catch (e) {
        alert(e.message);
      }
    }

    load();
    
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
