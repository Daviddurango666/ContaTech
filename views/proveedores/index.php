<?php
require __DIR__.'/../layouts/auth_check.php';

if(!RolePermissions::can($currentUser['rol'],'proveedores','read')){
    header('Location:/ContaTech1/views/dashboard/index.php');
    exit;
}

$pageTitle='Proveedores';$currentRoute='proveedores';

require __DIR__.'/../layouts/header.php';require __DIR__.'/../layouts/sidebar.php';

$canCreate=RolePermissions::can($currentUser['rol'],'proveedores','create');
$canUpdate=RolePermissions::can($currentUser['rol'],'proveedores','update');
$canDelete=RolePermissions::can($currentUser['rol'],'proveedores','delete');

?>
<main class="main-content">
    <div class="header">
        <h1>Proveedores</h1>
    </div>
    <div class="toolbar">
        <span></span>
        <?php if($canCreate):?>
            <button class="btn btn-primary" onclick="openForm()">+ Registrar</button>
        <?php endif;?>
    </div>
    <div class="table-wrap">
        <table class="data" id="tbl">
            <thead>
                <tr>
                    <th>NIT</th>
                    <th>Razón social</th>
                    <th>Contacto</th>
                    <th>Email</th>
                    <th>Tel</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
    <div id="modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:200;align-items:center;justify-content:center;">
        <div class="form-card" style="margin:auto;width: 400px;">
            <h2 style="margin-bottom:16px;">Proveedor</h2>
            <input type="hidden" id="pid">
            <div class="form-group">
                <label>NIT</label>
                <input id="nit"></div>
                <div class="form-group">
                    <label>Razón social</label>
                    <input id="razon_social">
                </div>
                <div class="form-group">
                    <label>Contacto</label>
                    <input id="contacto">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input id="email">
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input id="telefono">
                </div>
                <div class="form-group">
                    <label>Dirección</label>
                    <input id="direccion">
                </div>
                <div class="form-actions">
                    <button class="btn btn-primary" onclick="save()">Guardar</button>
                    <button class="btn btn-secondary" onclick="modal.style.display='none'">Cancelar</button>
                </div>
            </div>
        </div>
    </main>
    <script>
        const canUpdate=<?=$canUpdate?'true':'false'?>,canDelete=<?=$canDelete?'true':'false'?>;
        async function load(){
            const r=await ContaAPI.get('proveedores');
            const lista = r.data.slice().sort((a, b) => String(a.nit).localeCompare(String(b.nit), 'es', { numeric: true })); // ASC por id
            document.querySelector('#tbl tbody').innerHTML=lista.map(p=>`
                <tr>
                    <td>${p.nit}</td>
                    <td>${p.razon_social}</td>
                    <td>${p.contacto||''}</td>
                    <td>${p.email||''}</td>
                    <td>${p.telefono||''}</td>
                    <td>${p.activo==1?'Activo':'Inactivo'}</td>
                    <td>${canUpdate?`
                            <button class="btn btn-sm btn-secondary" onclick='edit(${JSON.stringify(p)})'>Editar</button>`:''}
                        ${canDelete?`
                            <button class="btn btn-sm btn-danger" onclick="del(${p.id})">Eliminar</button>`:''}
                    </td>
                </tr>`).join('');
        }
        function openForm(){
            pid.value='';
            ['nit','razon_social','contacto','email','telefono','direccion'].forEach(
                i=>document.getElementById(i).value='');
            modal.style.display='flex';
        }
        
        function edit(p){
            pid.value=p.id;
            ['nit','razon_social','contacto','email','telefono','direccion'].forEach(
                i=>document.getElementById(i).value=p[i]||'');
            modal.style.display='flex';
        }

        async function save(){
            const id=pid.value;
            const body={
                nit:nit.value,
                razon_social:razon_social.value,
                contacto:contacto.value,
                email:email.value,
                telefono:telefono.value,
                direccion:direccion.value
            };
            try{
                if(id) {
                    await ContaAPI.post('proveedores/'+id,body);
                }else {
                    await ContaAPI.post('proveedores',body);
                    modal.style.display='none';
                    load();
                }
            }catch(e){
                    alert(e.message);
                }
            }
                
            async function del(id){
                if(!confirm('¿Desactivar?')){
                    return;
                    await ContaAPI.post('proveedores/'+id+'/delete');
                    load();
                }
            }
        load();
    </script>
</body>
</html>
