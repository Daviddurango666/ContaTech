<?php
require __DIR__ . '/../layouts/auth_check.php';
if (!RolePermissions::can($currentUser['rol'], 'pagos', 'read')) {
	header('Location: /ContaTech1/views/dashboard/index.php'); 
	exit;
}
$pageTitle = 'Pagos';
$currentRoute = 'pagos';

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';

$canCreate = RolePermissions::can($currentUser['rol'], 'pagos', 'create');
$canBold = RolePermissions::can($currentUser['rol'], 'pagos', 'pasarela');
$canConc = RolePermissions::can($currentUser['rol'], 'pagos', 'conciliar');

?>
<main class="main-content">
	<div class="header">
		<h1>Gestión de Pagos · Bold · Conciliación</h1>
	</div>
  	<div class="toolbar">
		<span>

		</span>
		<div>
			<?php 
				if ($canCreate): ?>
					<button class="btn btn-secondary" onclick="regPago()">+ Registrar pago</button>
				<?php endif; 
			?>
			<?php 
				if ($canBold): ?>
				<button class="btn btn-primary" onclick="boldPay()">Cobrar con Bold</button>
				<?php endif; 
			?>
		</div>
  	</div>
  	<div class="table-wrap">
		<table class="data" id="tbl">
	  		<thead>
				<tr>
					<th>Método</th>
					<th>Monto</th>
					<th>Factura</th>
					<th>Estado</th>
					<th>Ref / Bold</th>
					<th>Acciones</th>
				</tr>
			</thead>
	  		<tbody>

			</tbody>
		</table>
	</div>

	<!-- Modal éxito -->
	<div id="modalOk" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:400;align-items:center;justify-content:center;">
		<div style="background:#fff;border-radius:16px;padding:28px 32px;text-align:center;min-width:260px;box-shadow:0 12px 40px rgba(0,0,0,.18);">
			<div style="width:56px;height:56px;margin:0 auto 12px;border-radius:50%;background:#e8f8ef;display:flex;align-items:center;justify-content:center;">
				<svg width="32" height="32" viewBox="0 0 24 24" fill="none">
					<path d="M5 13l4 4L19 7" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>
			<div style="font-size:13px;color:#666;text-transform:uppercase;letter-spacing:.04em;">Tipo</div>
			<div id="modalOkMsg" style="font-size:18px;font-weight:700;margin-top:4px;color:#111;">Pago conciliado</div>
		</div>
	</div>
</main>
<script>
	const canConc=<?= $canConc?'true':'false' ?>, canBold=<?= $canBold?'true':'false' ?>;
	const fmt=n=>new Intl.NumberFormat('es-CO',{style:'currency',currency:'COP',maximumFractionDigits:0}).format(n||0);

	async function load(){
	const r=await ContaAPI.get('pagos');
	const lista = (r.data || []).slice().sort((a, b) => {
		// Descendente por id (últimos primero)
		return (Number(b.id) || 0) - (Number(a.id) || 0);
	});
	// const lista = r.data.slice().sort((a, b) => String(a.id).localeCompare(String(b.id), 'es', { numeric: true })); // ASC por id
	document.querySelector('#tbl tbody').innerHTML=lista.map(p=>`
		<tr>
			<td>${p.metodo_nombre}</td>
			<td>${fmt(p.monto)}</td>
			<td>${p.factura_numero||'—'}</td>
			<td><span class="badge badge-${p.estado}">${p.estado}</span></td>
			<td style="font-size:11px;">${p.referencia||p.bold_payment_id||'—'}</td>
		<td>
			${canConc && p.estado !== 'conciliado' && Number(p.conciliado) !== 1
				? `<button class="btn btn-sm btn-secondary" onclick="conciliar(${p.id})">Conciliar</button>`
				: ''}
			${canBold && p.bold_payment_id && p.estado === 'pendiente'
				? `<button class="btn btn-sm btn-primary" onclick="confirmBold(${p.id})">Confirmar Bold</button>`
				: ''}
		</td>
		</tr>`).join('')||'<tr><td colspan="7" class="empty">Sin pagos</td></tr>';
	}
	async function regPago(){
		const metodos=(await ContaAPI.get('pagos/metodos')).data;
		const mid=prompt('ID método de pago:\n'+metodos.map(m=>m.id+': '+m.nombre).join('\n'));
		const monto=prompt('Monto:');
		const fid=prompt('ID factura (opcional):')||null;
		if(!mid||!monto){
			return;
		}
		await ContaAPI.post('pagos',{
			metodo_pago_id:+mid,
			monto:+monto,
			factura_id:fid?+fid:null,
			monto_factura:monto?+monto:null,
			estado:'aprobado'
		});
		load();
	}
	async function boldPay(){
		const monto=prompt('Monto a cobrar con Bold:');
		if(!monto){
			return;
		}
		const fid=prompt('ID factura (opcional):')||null;
		try{
			const r=await ContaAPI.post('pagos/bold',{
				monto:+monto,
				factura_id:fid?+fid:null,
				monto_factura:+monto,
				descripcion:'Pago ContaTech'
			});
			alert('Checkout Bold creado.\nPayment ID: '+r.data.bold.payment_id+'\nURL: '+r.data.bold.checkout_url);
			load();
		} catch(e){
			alert(e.message);
		}
	}
	async function confirmBold(id){ 
		await ContaAPI.post('pagos/'+id+'/bold-confirm'); 
		load(); 
	}

	function showOK(msg = "Pago Conciliado") {
		const modal =document.getElementById('modalOK');
		document.getElementById('modalOKmsg').innerHTML = msg;
		modal.style.display = 'flex';
		setTimeout(() => {
			modal.style.display = 'none';
		}, 3000);

	}				

	async function conciliar(id){
  		try{
			const r=await ContaAPI.post('pagos/'+id+'/conciliar');
			alert('Pago Conciliado');
		}catch(e){
			alert(e.message);
		} finally {
			load();
		}
	load();
	}
	load();
</script>
</body>
</html>
