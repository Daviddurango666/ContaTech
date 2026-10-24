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
					<th>ID</th>
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
</main>
<script>
	const canConc=<?= $canConc?'true':'false' ?>, canBold=<?= $canBold?'true':'false' ?>;
	const fmt=n=>new Intl.NumberFormat('es-CO',{style:'currency',currency:'COP',maximumFractionDigits:0}).format(n||0);

	async function load(){
	const r=await ContaAPI.get('pagos');
	document.querySelector('#tbl tbody').innerHTML=r.data.map(p=>`
		<tr>
		<td>${p.id}</td>
		<td>${p.metodo_nombre}</td>
		<td>${fmt(p.monto)}</td>
		<td>${p.factura_numero||'—'}</td>
		<td><span class="badge badge-${p.estado}">${p.estado}</span></td>
		<td style="font-size:11px;">${p.referencia||p.bold_payment_id||'—'}</td>
		<td>
			${canConc && !p.conciliado?`<button class="btn btn-sm btn-secondary" onclick="conciliar(${p.id})">Conciliar</button>`:''}
			${canBold && p.bold_payment_id && p.estado==='pendiente'?`<button class="btn btn-sm btn-primary" onclick="confirmBold(${p.id})">Confirmar Bold</button>`:''}
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
	async function conciliar(id){
  		try{
			const r=await ContaAPI.post('pagos/'+id+'/conciliar');
			alert('Conciliado OK');
		}catch(e){
			alert(e.message);
		}
	load();
	}
	load();
</script>
</body>
</html>
