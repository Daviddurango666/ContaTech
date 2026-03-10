
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Signup - Conta Tech</title>
    <link rel="stylesheet" href="../../assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <form action="../config/registro.php" method="POST">
            <h1 class="app-title">Conta Tech</h1>
            <div class="login-box">
                <h2>Registrate</h2>
                <p>Ingresa tu ID, Nombre, Usuario, E-mail y Contraseña para Registrarte en la App</p>
                <input type="number" name="id_user" placeholder="ID" id="id_user" class="input-email" required>
                <input type="text" name="name" placeholder="Nombre" id="name" class="input-email"required>
                <input type="text" name="user" placeholder="Usuario" id="user" class="input-email" required>
                <input type="email" name="email" placeholder="email@domain.com" id="email" class="input-email" required>
                <input type="password" name="password" placeholder="Contraseña" id="password" class="input-email" required>
                <button type="submit" name="registro" class="btn-email">Registrar</button>
                
                <div class="divider">
                    Tienes cuenta?
                </div>
                
                <button class="btn-google">
                    <a href="login.php">Inicia Sesion</a>
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