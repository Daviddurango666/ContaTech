<?php 
$appCfg=require __DIR__.'/../../config/app.php'; 
$base=$appCfg['base_url']; 
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Recuperar | ContaTech</title>
        <link rel="stylesheet" href="<?= $base ?>/public/assets/css/login.css">
        <link rel="stylesheet" href="<?= $base ?>/public/assets/css/app.css">
        <script src="<?= $base ?>/public/assets/js/api.js"></script>
    </head>
<body>
    <div class="login-container">
        <h1 class="app-title">ContaTech</h1>
        <div class="login-box">
            <h2>Recuperar contraseña</h2>
            <div id="msg" class="alert" style="display:none;">
            </div>
            <form id="f">
                <input class="input-email" type="email" id="email" placeholder="Email" required>
                <button type="submit" class="btn-email">Enviar</button></form>
                <p style="margin-top:16px;font-size:13px;"><a href="login.php" style="color:#000;">Volver</a></p>
            </div>
        </div>
    <script>
        document.getElementById('f').addEventListener('submit',async e=>{
        e.preventDefault(); const msg=document.getElementById('msg');
        try{ 
            const res=await ContaAPI.post('auth/forgot-password',{
                email:email.value
            });
            msg.className='alert alert-success'; 
            msg.style.display='block';
            let t=res.message; 
            if(res.data&&res.data.reset_token) {
                t+='<br><small>Token: '+res.data.reset_token+' <a href="reset.php?token='+res.data.reset_token+'">Restablecer</a></small>';
            }
            msg.innerHTML=t;
        }catch(ex){
            msg.className='alert alert-error';
            msg.style.display='block';
            msg.textContent=ex.message;
        }
        });
    </script>
</body>
</html>
