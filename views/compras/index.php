<?php

require __DIR__ . '/../layouts/auth_check.php';

if (!RolePermissions::can($currentUser['rol'], 'compras', 'read')) {
		header('Location: /ContaTech1/views/dashboard/index.php'); 
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
        <h1>Órdenes de Compra (Proveedores)</h1></div>
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
			<tbody>

            </tbody>
		</table>
	</div>
</main>
    <script>
        const canUpdate=<?= $canUpdate?'true':'false' ?>, canCancel=<?= $canCancel?'true':'false' ?>;
        const fmt=n=>new Intl.NumberFormat(
            'es-CO',{
                style:'currency',
                currency:'COP',
                maximumFractionDigits:0
            }).format(n||0);

        async function load(){
            const r=await ContaAPI.get('compras');
            document.querySelector('#tbl tbody').innerHTML=r.data.map(o=>`
                <tr>
                    <td>${o.numero}</td>
                    <td>${o.proveedor_nombre}</td>
                    <td>${fmt(o.total)}</td>
                    <td>
                        <span class="badge badge-${o.estado}">${o.estado}</span>
                    </td>
                    <td>${(o.created_at||'').slice(0,10)}</td>
                    <td>
                        ${canUpdate && o.estado==='solicitado'?`<button class="btn btn-sm btn-secondary" onclick="setEstado(${o.id},'facturado_proveedor')">Fact. proveedor</button>`:''}
                        ${canUpdate && (o.estado==='solicitado'||o.estado==='facturado_proveedor')?`<button class="btn btn-sm btn-success" onclick="setEstado(${o.id},'entregado')">Recibir</button>`:''}
                        ${canCancel && o.estado!=='entregado' && o.estado!=='anulado'?`<button class="btn btn-sm btn-danger" onclick="cancelar(${o.id})">Anular</button>`:''}
                    </td>
                </tr>`).join('')||'<tr><td colspan="6" class="empty">Sin órdenes</td></tr>';
        }
        async function openNew(){
            const provs=(await ContaAPI.get('proveedores')).data.filter(p=>p.activo==1);
            const prods=(await ContaAPI.get('productos')).data.filter(p=>p.activo==1);
            const pid=prompt('ID proveedor:\n'+provs.map(p=>p.id+': '+p.razon_social).join('\n'));
            if(!pid){
                return;
            }
            const prodId=prompt('ID producto:\n'+prods.map(p=>p.id+': '+p.nombre).join('\n'));
            const cant=prompt('Cantidad:','10');
            const costo=prompt('Costo unitario:', prods.find(p=>p.id==prodId)?.costo||'0');
            try{
                await ContaAPI.post('compras',{
                    proveedor_id:+pid,items:[
                        {
                            producto_id:+prodId,
                            cantidad:+cant,
                            costo_unitario:+costo
                        }
                    ]
                });
                load();
            }catch(e){
                alert(e.message);
            }
        }
        async function setEstado(id,estado){ 
            await ContaAPI.post('compras/'+id,{estado}); 
            load(); 
        }
        async function cancelar(id){ 
            if(!confirm('¿Anular?')){
                return; 
            }
            await ContaAPI.post('compras/'+id+'/cancelar'); 
            load(); 
        }
        load();
    </script>
</body>
</html>
