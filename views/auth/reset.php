<?php 

$appCfg=require __DIR__.'/../../config/app.php'; 
$base=$appCfg['base_url']; 
$token=htmlspecialchars($_GET['token']??''); 

?>

<!DOCTYPE html>
<html lang="es">
  	<head>
		<meta charset="UTF-8">
		<title>Nueva contraseña</title>
		<link rel="stylesheet" href="<?= $base ?>/public/assets/css/login.css">
		<link rel="stylesheet" href="<?= $base ?>/public/assets/css/app.css">
		<script src="<?= $base ?>/public/assets/js/api.js"></script>
  	</head>
  	<body>
		<div class="login-container">
			<h1 class="app-title">ContaTech</h1>
			<div class="login-box">
				<h2>Nueva contraseña</h2>
				<div id="msg" class="alert" style="display:none;">

				</div>
				<form id="f">
					<input type="hidden" id="token" value="<?= $token ?>">
					<input class="input-email" type="password" id="password" placeholder="Nueva contraseña" required minlength="6">
					<button type="submit" class="btn-email">Guardar</button>
				</form>
			</div>
		</div>
  	<script>
  		document.getElementById('f').addEventListener('submit',async e=>{
			e.preventDefault(); 
			const msg=document.getElementById('msg');
			try{ 
				await ContaAPI.post('auth/reset-password',{
					token:token.value,password:password.value
				});

				msg.className='alert alert-success';
				msg.style.display='block';
				msg.innerHTML='Listo. <a href="login.php">Login</a>';

			}catch(ex){
				msg.className='alert alert-error';
				msg.style.display='block';
				msg.textContent=ex.message;
			}
		});
  	</script>
</body>
</html>
