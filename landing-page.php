<?php
// Aquí puedes agregar lógica si en el futuro deseas validar sesión, etc.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ContaTech | Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            color: #000;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* NAVBAR */
        .menu {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .menu ul {
            list-style: none;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .menu li {
            cursor: pointer;
            font-weight: 500;
        }

        .menu .btn button {
            padding: 8px 16px;
            border-radius: 10px;
            border: 1px solid #ccc;
            background: transparent;
            cursor: pointer;
            font-weight: 500;
        }

        /* HERO */
        .content {
            text-align: center;
            padding: 120px 20px;
        }

        .content h1 {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .content p {
            font-size: 20px;
            margin-bottom: 30px;
        }

        .content .actions {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .content button {
            padding: 12px 24px;
            border-radius: 10px;
            border: 1px solid #ccc;
            background: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        /* FOOTER */
        footer {
            text-align: center;
            padding: 12px;
            position: fixed;
            bottom: 0;
            width: 100%;
            background: #fff;
            box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
        }
    </style>

</head>
<body>

    <!-- NAVBAR -->
    <nav class="menu">
        <div class="logo">
            <h1>ContaTech</h1>
        </div>

        <ul>
            <li><a href="#">Inicio</a></li>
            <li><a href="#">Quiénes Somos?</a></li>

            <div class="btn">
                <button><a href="src/auth/login.php">Log In</a></button>
                <button><a href="src/auth/signup.php">Sign Up</a></button>
            </div>
        </ul>
    </nav>

    <!-- CONTENIDO -->
    <section class="content" id="slides">
        <h1>Welcome to ContaTech</h1>
        <p>A software for your Business</p>

        <div class="actions">
            <button onclick="window.location.href='src/auth/login.php'">Log In</button>
            <button onclick="window.location.href='src/auth/signup.php'">Sign Up</button>
        </div>
    </section>

    <!-- FOOTER -->
    <footer id="footer"></footer>

    <script>
        const year = new Date().getFullYear();
        document.getElementById('footer').innerHTML = `&copy; ${year} ContaTech. All rights reserved.`;
    </script>

</body>
</html>
