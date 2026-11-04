<?php
$appCfg = require __DIR__ . '/../../config/app.php'; 
$base = $appCfg['base_url'];

if (!empty($_COOKIE['contatech_token'])) {
	try { 
        require_once __DIR__.'/../../core/JWT.php'; 
        JWT::decode($_COOKIE['contatech_token']);
		header('Location: '.$base.'/views/dashboard/index.php'); 
        exit;
	} catch (Exception $e) {}
}

?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Iniciar sesión | ContaTech</title>
        <link rel="stylesheet" href="<?= $base ?>/public/assets/css/login.css">
        <link rel="stylesheet" href="<?= $base ?>/public/assets/css/app.css">
        <link rel="icon" href="<?= $base ?>/public/assets/img/favicon_io/favicon.ico">
        <script src="<?= $base ?>/public/assets/js/api.js"></script>
        <style>
            .login-box 
            .error{
                color:#dc2626;
                font-size:13px;
                margin-bottom:12px;
                display:none
            }
                
            .demo-users{
                margin-top:24px;
                font-size:12px;
                color:#666;
                text-align:left;
                max-width:320px;
                margin-left:auto;
                margin-right:auto
            }
            .demo-users code{
                background:#f0f0f0;
                padding:1px 5px;
                border-radius:4px
            }
        </style>
    </head>
<body>
    <div class="login-container">
        <h1 class="app-title">ContaTech</h1>
        <div class="login-box">
            <h2>Registro</h2>
            <p>Ingrese los datos para crear su cuenta</p>
            <div class="error" id="errorMsg"></div>
            <form id="loginForm">
                <input class="input-email" type="text" id="nombre" placeholder="Nombre completo" required>
                <input class="input-email" type="text" id="usuario" placeholder="Usuario" required>
                <input class="input-email" type="text" id="email" placeholder="Email" required>
                <input class="input-email" type="password" id="password" placeholder="Contraseña" required>
                <button type="submit" class="btn-email">Continuar</button>
            </form>
            <p style="font-size:12px;">Tienes Cuenta?<a href="login.php" style="color:#000;"> Inicia sesión aquí</a></p>
        </div>
    </div>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault(); 
            const err=document.getElementById('errorMsg'); 
            err.style.display='none';
            try {
                const res=await ContaAPI.post('auth/register',{
                    nombre:document.getElementById('nombre').value.trim(),
                    email:document.getElementById('email').value.trim(),
                    usuario:document.getElementById('usuario').value.trim(),
                    password:document.getElementById('password').value
                });

                ContaAPI.setSession(res.data.token, res.data.user);
                
                window.location.href='<?= $base ?>/views/auth/login.php';

            } catch(ex){ 
                err.textContent=ex.message; 
                err.style.display='block'; 
            }
        });
    </script>
</body>
</html>
