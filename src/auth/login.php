<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Conta Tech</title>
    <link rel="stylesheet" href="../../assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <form action="authenticate.php" method="POST">
            <h1 class="app-title">Conta Tech</h1>
            <div class="login-box">
                <h2>Inicia Sesion</h2>
                <p>Ingresa tu E-mail y Contraseña para Iniciar Sesion en la App</p>
                <input type="email" name="email" placeholder="email@domain.com" class="input-email">
                <input type="password" name="password" placeholder="Contraseña" class="input-email">
                <button class="btn-email">Iniciar Sesion</button>
                
                <div class="divider">
                    No tienes cuenta?
                </div>
                
                <button class="btn-google">
                    <a href="signup.php">Registrate</a>
                </button>
                
                <p class="terms">
                    Al hacer clic en continuar, aceptas nuestros 
                    <a href="#">Términos de servicio</a> y 
                    <a href="#">Política de privacidad</a>.
                </p>
            </div>
        </form>
    </div>
</body>
</html>




</html>