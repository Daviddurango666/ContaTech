<?php
require __DIR__.'/../layouts/auth_check.php';
if(!RolePermissions::can($currentUser['rol'],'usuarios','read')){
    header('Location:/ContaTech1/views/dashboard/index.php');
    exit;
}

$pageTitle='Usuarios';
$currentRoute='usuarios';

require __DIR__.'/../layouts/header.php';
require __DIR__.'/../layouts/sidebar.php';

$canCreate=RolePermissions::can($currentUser['rol'],'usuarios','create');
$canUpdate=RolePermissions::can($currentUser['rol'],'usuarios','update');
$canDelete=RolePermissions::can($currentUser['rol'],'usuarios','delete');
$roles=$appCfg['roles'];

?>
<main class="main-content">
    <div class="header">
        <h1>Gestión de Usuarios</h1>
    </div>
    <div class="toolbar">
        <span></span>
        <?php 
            if($canCreate):?>
                <button class="btn btn-primary" onclick="openForm()">+ Crear usuario</button>
            <?php endif;
        ?>
    </div>
    <div class="table-wrap">
        <table class="data" id="tbl">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
    <div id="modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:200;align-items:center;justify-content:center;">
        <div class="form-card" style="margin:auto;max-width:480px;">
            <h2 id="formTitle" style="margin-bottom:16px;">Usuario</h2>
            <input type="hidden" id="uid">
            <div class="form-group">
                <label>Nombre</label>
                <input id="nombre">
            </div>
            <div class="form-group">
                <label>Usuario</label>
                <input id="usuario">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" id="email">
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" id="password" placeholder="Vacío = no cambiar">
            </div>
            <div class="form-group">
                <label>Rol</label>
                <select id="rol">
                    <?php 
                        foreach($roles as $k=>$v):?>
                            <option value="<?=$k?>"><?=$v?></option>
                        <?php endforeach;
                    ?>
                </select>
            </div>
            <div class="form-actions">
                <button class="btn btn-primary" onclick="saveUser()">Guardar</button>
                <button class="btn btn-secondary" onclick="closeForm()">Cancelar</button>
            </div>
        </div>
    </div>
</main>
<script>
    const canUpdate=<?=$canUpdate?'true':'false'?>,canDelete=<?=$canDelete?'true':'false'?>;
    const rolesMap=<?=json_encode($roles)?>;
    async function load(){
        const res=await ContaAPI.get('usuarios');
        const lista = res.data.slice().sort((a, b) => a.id - b.id); // ASC por id
        document.querySelector('#tbl tbody').innerHTML=lista.map(u=>`
            <tr>
                <td>${u.id}</td>
                <td>${u.nombre}</td>
                <td>${u.usuario}</td>
                <td>${u.email}</td>
                <td>${rolesMap[u.rol]||u.rol}</td>
                <td>${u.activo==1?'Activo':'Inactivo'}</td>
                <td>${canUpdate?`
                        <button class="btn btn-sm btn-secondary" onclick='editUser(${JSON.stringify(u)})'>Editar</button>`:''} 
                    ${canDelete?`
                        <button class="btn btn-sm btn-danger" onclick="delUser(${u.id})">Eliminar</button>`:''}
                </td>
            </tr>
        `).join('');
    }
    function openForm(){
        uid.value='';
        ['nombre','usuario','email','password'].forEach(i=>document.getElementById(i).value='');
        formTitle.textContent='Crear usuario';
        modal.style.display='flex';
    }
    function editUser(u){
        uid.value=u.id;
        nombre.value=u.nombre;
        usuario.value=u.usuario;
        email.value=u.email;
        password.value='';
        rol.value=u.rol;
        formTitle.textContent='Editar';
        modal.style.display='flex';
    }
    function closeForm(){
        modal.style.display='none';
    }
    async function saveUser(){
        const id=uid.value;
        const body={
            nombre:nombre.value,
            usuario:usuario.value,
            email:email.value,
            rol:rol.value};
        if(password.value) {
            body.password=password.value;
        }
        try{
            if(id) {
                await ContaAPI.post('usuarios/'+id,body);
            }else{
                body.password=password.value||'Admin123!';
                await ContaAPI.post('usuarios',body);
            }
            closeForm();
            load();
        }catch(e){
            alert(e.message);
        }
    }
    async function delUser(id){
        if(!confirm('¿Desactivar?')){    
            return;
        }
        await ContaAPI.post('usuarios/'+id+'/delete');
        load();
    }
    load();
</script>
</body>
</html>
