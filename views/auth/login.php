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
        <script src="<?= $base ?>/public/assets/js/api.js"></script>
        <style>.login-box .error{color:#dc2626;font-size:13px;margin-bottom:12px;display:none}.demo-users{margin-top:24px;font-size:12px;color:#666;text-align:left;max-width:320px;margin-left:auto;margin-right:auto}.demo-users code{background:#f0f0f0;padding:1px 5px;border-radius:4px}</style>
    </head>
<body>
    <div class="login-container">
        <h1 class="app-title">ContaTech</h1>
        <div class="login-box">
            <h2>Iniciar sesión</h2>
            <p>Ingrese su usuario o email y contraseña</p>
            <div class="error" id="errorMsg"></div>
            <form id="loginForm">
                <input class="input-email" type="text" id="login" placeholder="Usuario o email" required>
                <input class="input-email" type="password" id="password" placeholder="Contraseña" required>
                <button type="submit" class="btn-email">Continuar</button>
            </form>
            <p style="font-size:12px;"><a href="forgot.php" style="color:#000;">¿Olvidó su contraseña?</a></p>
            <div class="demo-users"><strong>Usuarios de prueba</strong> (clave: <code>Admin123!</code>)<br>admin · gerente · jefe · contable · cajero · bodeguero · soporte</div>
        </div>
    </div>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault(); 
            const err=document.getElementById('errorMsg'); 
            err.style.display='none';
            try {
                const res=await ContaAPI.post('auth/login',{
                    login:document.getElementById('login').value.trim(),password:document.getElementById('password').value
                });

                ContaAPI.setSession(res.data.token, res.data.user);
                
                window.location.href='<?= $base ?>/views/dashboard/index.php';

            } catch(ex){ 
                err.textContent=ex.message; 
                err.style.display='block'; 
            }
        });
    </script>
</body>
</html>
