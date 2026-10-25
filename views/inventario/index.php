<?php
require __DIR__.'/../layouts/auth_check.php';

if(!RolePermissions::can($currentUser['rol'],'inventario','read')){
    header('Location:/ContaTech1/views/dashboard/index.php');
    exit;
}

$pageTitle='Inventario';$currentRoute='inventario';

require __DIR__.'/../layouts/header.php';
require __DIR__.'/../layouts/sidebar.php';

$canCreate=RolePermissions::can($currentUser['rol'],'inventario','create');
$canUpdate=RolePermissions::can($currentUser['rol'],'inventario','update');
$canStock=RolePermissions::can($currentUser['rol'],'inventario','stock');
$canDelete=RolePermissions::can($currentUser['rol'],'inventario','delete');

?>
<main class="main-content">
    <div class="header">
        <h1>Inventario</h1>
    </div>
    <div class="toolbar">
        <div>
            <button class="btn btn-secondary btn-sm" onclick="loadProducts()">Productos</button>
            <button class="btn btn-secondary btn-sm" onclick="loadLow()">Alertas stock</button>
        </div>
        <?php 
            if($canCreate):?>
                <button class="btn btn-primary" onclick="openProd()">+ Producto</button>
        <?php endif;?>
    </div>

    <div class="table-wrap">
        <table class="data" id="tbl">
            <thead>

            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
    <div id="modalProd" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:200;align-items:center;justify-content:center;">
        <div class="form-card" style="margin:auto; width: 400px;">
            <h2 style="text-align:center;">Producto</h2>
            <input type="hidden" id="prid">
            <div class="form-group">
                <label>Código</label>
                <input id="codigo">
            </div>
            <div class="form-group">
                <label>Nombre</label>
                <input id="nombre">
            </div>
            <div class="form-group">
                <label>Categoría ID</label>
                <input type="number" id="categoria_id" value="1">
            </div>
            <div class="form-group">
                <label>Precio</label>
                <input type="number" id="precio" step="0.01">
            </div>
            <div class="form-group">
                <label>Costo</label>
                <input type="number" id="costo" step="0.01">
            </div>
            <div class="form-group">
                <label>Stock</label>
                <input type="number" id="stock" value="0">
            </div>
            <div class="form-group">
                <label>Stock mínimo</label>
                <input type="number" id="stock_minimo" value="5">
            </div>
            <div class="form-actions">
                <button class="btn btn-primary" onclick="saveProd()">Guardar</button>
                <button class="btn btn-secondary" onclick="modalProd.style.display='none'">Cancelar</button>
            </div>
        </div>
    </div>
</main>
<script>
    const canUpdate=<?=$canUpdate?'true':'false'?>,canStock=<?=$canStock?'true':'false'?>,canDelete=<?=$canDelete?'true':'false'?>;
    const fmt=n=>new Intl.NumberFormat('es-CO').format(n);
    async function loadProducts(){
        const r=await ContaAPI.get('productos');
        const lista = r.data.slice().sort((a, b) => String(a.codigo).localeCompare(String(b.codigo), 'es', { numeric: true })); // ASC por id
        document.querySelector('#tbl thead').innerHTML='<tr><th>Código</th><th>Nombre</th><th>Cat</th><th>Precio</th><th>Stock</th><th>Mín</th><th></th></tr>';
        document.querySelector('#tbl tbody').innerHTML= lista.map(p=>`
        <tr style="${p.stock<=p.stock_minimo?'background:#fef2f2':''}">
            <td>${p.codigo}</td>
            <td>${p.nombre}</td>
            <td>${p.categoria_nombre||''}</td>
            <td>${fmt(p.precio)}</td>
            <td>
                <strong>${p.stock}</strong>
            </td>
            <td>${p.stock_minimo}</td>
            <td>${canStock?`
                    <button class="btn btn-sm btn-secondary" onclick="adjStock(${p.id})">Stock</button>`:''}
                ${canUpdate?`
                    <button class="btn btn-sm btn-secondary" onclick='editProd(${JSON.stringify(p)})'>Editar</button>`:''}
                ${canDelete?`
                    <button class="btn btn-sm btn-danger" onclick="deact(${p.id})">Off</button>`:''}
            </td>
        </tr>`).join('');
    }
    async function loadLow(){
        const r=await ContaAPI.get('productos/alertas');
        const lista = r.data.slice().sort((a, b) => String(a.codigo).localeCompare(String(b.codigo), 'es', { numeric: true })); // ASC por id
        document.querySelector('#tbl thead').innerHTML='<tr><th>Código</th><th>Nombre</th><th>Stock</th><th>Mín</th></tr>';
        document.querySelector('#tbl tbody').innerHTML=(lista.length?lista.map(p=>`
            <tr>
                <td>${p.codigo}</td>
                <td>${p.nombre}</td>
                <td>${p.stock}</td>
                <td>${p.stock_minimo}</td>
            </tr>`).join(''):'<tr><td colspan="4" class="empty">Sin alertas</td></tr>');
        }
    function openProd(){
        prid.value='';
        codigo.value=nombre.value='';
        precio.value=costo.value=0;
        stock.value=0;
        modalProd.style.display='flex';
    }
    function editProd(p){
        prid.value=p.id;
        codigo.value=p.codigo;
        nombre.value=p.nombre;
        precio.value=p.precio;
        costo.value=p.costo;
        stock_minimo.value=p.stock_minimo;
        categoria_id.value=p.categoria_id||1;
        modalProd.style.display='flex';
    }
    async function saveProd(){
        const id=prid.value;
        const body={
            codigo:codigo.value,
            nombre:nombre.value,
            categoria_id:+categoria_id.value||null,
            precio:+precio.value,
            costo:+costo.value,
            stock:+stock.value,
            stock_minimo:+stock_minimo.value
        };
        try{
            if(id) {
                await ContaAPI.post('productos/'+id,body);
            } else {
                await ContaAPI.post('productos',body);
                modalProd.style.display='none';
                loadProducts();
            }
        }catch(e){
            alert(e.message);
        }
    }
            
    async function adjStock(id){
        const cant=prompt('Cantidad (use +N o -N para sumar/restar):');
        if(cant===null){
            return;
        }
        let op='set',qty=parseInt(cant,10);
        if(cant.startsWith('+')){
            op='add';
            qty=parseInt(cant.slice(1),10);
        }
        if(cant.startsWith('-')){
            op='sub';
            qty=parseInt(cant.slice(1),10);
        }
        
        await ContaAPI.post('productos/'+id+'/stock',{
            cantidad:qty,
            operacion:op
        });
        loadProducts();
    }
    async function deact(id){
        if(!confirm('¿Desactivar?')) {
            return;
        }
        await ContaAPI.post('productos/'+id+'/desactivar');
        loadProducts();
    }
    loadProducts();
</script>
</body>
</html>
