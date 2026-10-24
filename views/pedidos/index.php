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
                    <th>

                    </th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</main>
<script>
    const canCancel=<?=$canCancel?'true':'false'?>;
    const fmt=n=>new Intl.NumberFormat(
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
                <td><button class="btn btn-sm btn-secondary" onclick="ver(${o.id})">Ver</button> 
                    ${canCancel&&o.estado!=='anulado'&&o.estado!=='cobrado'?`
                        <button class="btn btn-sm btn-danger" onclick="cancelar(${o.id})">Anular</button>`:''}
                </td></tr>`).join('');
    }
    
    async function openNew(){
        const pr=await ContaAPI.get('productos');
        const products=pr.data.filter(p=>p.activo==1);
        if(!products.length){
            alert('Sin productos');
            return;
        }
    
        const pid=prompt('ID producto:\n'+products.map(p=>p.id+': '+p.nombre+' ($'+p.precio+')').join('\n'));
        const cant=prompt('Cantidad:','1');
    
        if(!pid) {
            return;
            const prod=products.find(p=>p.id==pid);
            try{
                await ContaAPI.post('pedidos',{
                    cliente_id:1,
                    items:[{
                        producto_id:+pid,
                        cantidad:+cant,
                        precio_unitario:+prod.precio
                    }]
                });
                load();
            }catch(e){
                alert(e.message);
            }
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
    
    load();
</script>
</body>
</html>
