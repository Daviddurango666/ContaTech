<?php
require __DIR__ . '/../layouts/auth_check.php';
if (!RolePermissions::can($currentUser['rol'], 'compras', 'read')) {
    header('Location: ' . ($appCfg['base_url'] ?? '/ContaTech1') . '/views/dashboard/index.php');
    exit;
}
$pageTitle = 'Compras';
$currentRoute = 'compras';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
$canCreate = RolePermissions::can($currentUser['rol'], 'compras', 'create');
$canUpdate = RolePermissions::can($currentUser['rol'], 'compras', 'update');
$canCancel = RolePermissions::can($currentUser['rol'], 'compras', 'cancel');
?>
<main class="main-content">
  <div class="header">
    <h1>Órdenes de Compra (Proveedores)</h1>
  </div>
  <div class="toolbar">
    <span>Flujo: solicitado → facturado_proveedor → entregado | anulado</span>
    <?php if ($canCreate): ?>
      <button class="btn btn-primary" onclick="openNew()">+ Nueva OC</button>
    <?php endif; ?>
  </div>
  <div class="table-wrap">
    <table class="data" id="tbl">
      <thead>
        <tr>
          <th>Número</th>
          <th>Proveedor</th>
          <th>Total</th>
          <th>Estado</th>
          <th>Fecha</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <!-- MODAL NUEVA OC -->
  <div id="modalOC" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:300;overflow:auto;padding:20px;">
    <div style="max-width:860px;margin:0 auto;background:#fff;border-radius:12px;padding:24px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h2 style="margin:0;font-size:18px;">Nueva orden de compra</h2>
        <button type="button" class="btn btn-secondary btn-sm" onclick="cerrarModal()">✕</button>
      </div>

      <div class="form-group" style="margin-bottom:12px;">
        <label>Proveedor</label>
        <select id="oc_proveedor" style="width:100%;max-width:420px;padding:8px;border:1px solid #ccc;border-radius:8px;">
          <option value="">Cargando...</option>
        </select>
      </div>

      <div style="display:flex;gap:8px;margin:12px 0;flex-wrap:wrap;align-items:center;">
        <input
          type="text"
          id="oc_buscar"
          placeholder="Buscar producto por ID o nombre..."
          style="flex:1;max-width:200px;padding:8px;border-radius:8px;border:1px solid #ccc;"
          oninput="filtrarProductos()"
        >
        <select id="oc_producto" style="flex:1;min-width:220px;padding:8px;border-radius:8px;border:1px solid #ccc;"></select>
        <input type="number" id="oc_cant" value="1" min="1" style="width:80px;padding:8px;border-radius:8px;border:1px solid #ccc;" title="Cantidad">
        <input type="number" id="oc_costo" value="0" min="0" step="0.01" style="width:110px;padding:8px;border-radius:8px;border:1px solid #ccc;" title="Costo unitario">
        <button type="button" class="btn btn-secondary" onclick="addItem()">+ Agregar</button>
      </div>

      <div class="table-wrap" style="box-shadow:none;">
        <table class="data" id="tblItems">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cant.</th>
              <th>Costo unit.</th>
              <th>Subtotal</th>
              <th></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <div style="text-align:right;margin-top:12px;font-size:14px;border:1px solid #D9D9D9;padding:10px;border-radius:8px;">
        <div style="font-size:18px;">Total: <strong id="oc_total">$0</strong></div>
      </div>

      <div class="form-group" style="margin-top:12px;">
        <label>Observaciones</label>
        <input type="text" id="oc_obs" placeholder="Opcional" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:8px;">
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
        <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="guardarOC()">Guardar orden de compra</button>
      </div>
    </div>
  </div>

  <!-- MODAL DETALLE OC -->
  <div id="modalDetalle" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:300;overflow:auto;padding:20px;">
    <div style="max-width:640px;margin:0 auto;background:#fff;border-radius:12px;padding:24px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <h2 style="margin:0;font-size:18px;" id="det_titulo">Detalle OC</h2>
        <button type="button" class="btn btn-secondary btn-sm" onclick="cerrarDetalle()">✕</button>
      </div>
      <div id="det_meta" style="font-size:13px;color:#555;margin-bottom:12px;"></div>
      <div class="table-wrap" style="box-shadow:none;">
        <table class="data">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cant.</th>
              <th>Costo unit.</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody id="det_body"></tbody>
        </table>
      </div>
      <div style="text-align:right;margin-top:12px;font-size:16px;">
        Total: <strong id="det_total">$0</strong>
      </div>
      <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:16px;">
        <button type="button" class="btn btn-secondary" onclick="cerrarDetalle()">Cerrar</button>
      </div>
    </div>
  </div>
</main>
<script>
const canUpdate = <?= $canUpdate ? 'true' : 'false' ?>;
const canCancel = <?= $canCancel ? 'true' : 'false' ?>;
const fmt = n => new Intl.NumberFormat('es-CO', {
  style: 'currency', currency: 'COP', maximumFractionDigits: 0
}).format(n || 0);

let products = [];
let providers = [];
let cart = []; // { producto_id, nombre, cantidad, costo_unitario, subtotal }

async function load() {
  const r = await ContaAPI.get('compras');
  const lista = (r.data || []).slice().sort((a, b) => b.id - a.id);
  document.querySelector('#tbl tbody').innerHTML = lista.length
    ? lista.map(o => `
      <tr>
        <td>${o.numero}</td>
        <td>${o.proveedor_nombre || '—'}</td>
        <td>${fmt(o.total)}</td>
        <td><span class="badge badge-${o.estado}">${o.estado}</span></td>
        <td>${(o.created_at || '').toString().slice(0, 10)}</td>
        <td>
          <button class="btn btn-sm btn-secondary" onclick="verDetalle(${o.id})">Ver</button>
          ${canUpdate && o.estado === 'solicitado'
            ? `<button class="btn btn-sm btn-secondary" onclick="setEstado(${o.id},'facturado_proveedor')">Fact. proveedor</button>` : ''}
          ${canUpdate && (o.estado === 'solicitado' || o.estado === 'facturado_proveedor')
            ? `<button class="btn btn-sm btn-success" onclick="recibir(${o.id})">Recibir + egreso</button>` : ''}
          ${canCancel && o.estado !== 'entregado' && o.estado !== 'anulado'
            ? `<button class="btn btn-sm btn-danger" onclick="cancelar(${o.id})">Anular</button>` : ''}
        </td>
      </tr>`).join('')
    : '<tr><td colspan="6" class="empty">Sin órdenes</td></tr>';
}

async function openNew() {
  cart = [];
  renderCart();
  const [pr, pv] = await Promise.all([
    ContaAPI.get('productos'),
    ContaAPI.get('proveedores')
  ]);
  products = (pr.data || []).filter(p => p.activo == 1);
  providers = (pv.data || []).filter(p => p.activo == 1);

  const selProv = document.getElementById('oc_proveedor');
  selProv.innerHTML = providers.length
    ? providers.map(p => `<option value="${p.id}">${p.razon_social || p.nombre}</option>`).join('')
    : '<option value="">Sin proveedores</option>';

  renderProductSelect(products);
  document.getElementById('oc_obs').value = '';
  document.getElementById('oc_cant').value = 1;
  document.getElementById('modalOC').style.display = 'block';

  // Precargar costo al cambiar producto
  document.getElementById('oc_producto').onchange = function () {
    const p = products.find(x => x.id == this.value);
    document.getElementById('oc_costo').value = p ? (p.costo || 0) : 0;
  };
  if (products.length) {
    document.getElementById('oc_costo').value = products[0].costo || 0;
  }
}

function cerrarModal() {
  document.getElementById('modalOC').style.display = 'none';
}

function renderProductSelect(lista) {
  const sel = document.getElementById('oc_producto');
  if (!lista.length) {
    sel.innerHTML = '<option value="">Sin coincidencias</option>';
    return;
  }
  sel.innerHTML = lista.map(p =>
    `<option value="${p.id}" data-costo="${p.costo || 0}">
      #${p.id} — ${p.nombre} — costo ${fmt(p.costo)} (stock ${p.stock})
    </option>`
  ).join('');
}

function filtrarProductos() {
  const q = (document.getElementById('oc_buscar').value || '').trim().toLowerCase();
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
  const sel = document.getElementById('oc_producto');
  const id = +sel.value;
  const cant = Math.max(1, +document.getElementById('oc_cant').value || 1);
  const costo = Math.max(0, +document.getElementById('oc_costo').value || 0);
  const prod = products.find(p => p.id == id);
  if (!prod) return alert('Selecciona un producto');
  if (costo <= 0) return alert('Indica un costo unitario válido');

  const existing = cart.find(c => c.producto_id === id);
  if (existing) {
    existing.cantidad += cant;
    existing.costo_unitario = costo;
    existing.subtotal = existing.cantidad * existing.costo_unitario;
  } else {
    cart.push({
      producto_id: id,
      nombre: prod.nombre,
      cantidad: cant,
      costo_unitario: costo,
      subtotal: cant * costo
    });
  }
  renderCart();
}

function removeItem(i) {
  cart.splice(i, 1);
  renderCart();
}

function renderCart() {
  const tot = cart.reduce((s, c) => s + c.subtotal, 0);
  document.querySelector('#tblItems tbody').innerHTML = cart.length
    ? cart.map((c, i) => `
        <tr>
          <td>${c.nombre}</td>
          <td>${c.cantidad}</td>
          <td>${fmt(c.costo_unitario)}</td>
          <td>${fmt(c.subtotal)}</td>
          <td><button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${i})">Quitar</button></td>
        </tr>
      `).join('')
    : '<tr><td colspan="5" class="empty">Agrega productos</td></tr>';
  document.getElementById('oc_total').textContent = fmt(tot);
}

async function guardarOC() {
  if (!cart.length) return alert('Agrega al menos un producto');
  const proveedor_id = +document.getElementById('oc_proveedor').value;
  if (!proveedor_id) return alert('Selecciona un proveedor');

  try {
    await ContaAPI.post('compras', {
      proveedor_id,
      observaciones: document.getElementById('oc_obs').value || null,
      items: cart.map(c => ({
        producto_id: c.producto_id,
        cantidad: c.cantidad,
        costo_unitario: c.costo_unitario
      }))
    });
    cerrarModal();
    load();
  } catch (e) {
    alert(e.message || 'Error al guardar');
  }
}

async function setEstado(id, estado) {
  try {
    await ContaAPI.post('compras/' + id, { estado });
    load();
  } catch (e) {
    alert(e.message || 'Error');
  }
}

async function recibir(id) {
  if (!confirm('¿Recibir mercancía?\n\nEsto:\n• Aumenta el stock\n• Actualiza el costo del producto\n• Registra el EGRESO en movimientos')) return;
  try {
    await ContaAPI.post('compras/' + id, { estado: 'entregado' });
    load();
  } catch (e) {
    alert(e.message || 'Error al recibir');
  }
}

async function cancelar(id) {
  if (!confirm('¿Anular esta orden de compra?')) return;
  try {
    await ContaAPI.post('compras/' + id + '/cancelar');
    load();
  } catch (e) {
    alert(e.message || 'Error');
  }
}

async function verDetalle(id) {
  try {
    const r = await ContaAPI.get('compras/' + id);
    const o = r.data;
    document.getElementById('det_titulo').textContent = o.numero || ('OC #' + id);
    document.getElementById('det_meta').innerHTML =
      `Proveedor: <strong>${o.proveedor_nombre || '—'}</strong> · Estado: <span class="badge badge-${o.estado}">${o.estado}</span> · Fecha: ${(o.created_at || '').toString().slice(0, 10)}`;
    document.getElementById('det_body').innerHTML = (o.detalle || []).map(d => `
      <tr>
        <td>${d.producto_nombre || d.producto_id}</td>
        <td>${d.cantidad}</td>
        <td>${fmt(d.costo_unitario)}</td>
        <td>${fmt(d.subtotal)}</td>
      </tr>
    `).join('') || '<tr><td colspan="4" class="empty">Sin detalle</td></tr>';
    document.getElementById('det_total').textContent = fmt(o.total);
    document.getElementById('modalDetalle').style.display = 'block';
  } catch (e) {
    alert(e.message || 'No se pudo cargar el detalle');
  }
}

function cerrarDetalle() {
  document.getElementById('modalDetalle').style.display = 'none';
}

load();
</script>
</body>
</html>
