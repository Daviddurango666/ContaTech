<?php
/**
 * ContaTech - Landing / punto de entrada
 */
$appCfg = require __DIR__ . '/config/app.php';
$base = rtrim($appCfg['base_url'] ?? '/ContaTech1', '/');

// Si ya hay sesión válida → dashboard
$token = $_COOKIE['contatech_token'] ?? null;
if ($token) {
    require_once __DIR__ . '/core/JWT.php';
    try {
        JWT::decode($token);
        header('Location: ' . $base . '/views/dashboard/index.php');
        exit;
    } catch (Exception $e) {
        setcookie('contatech_token', '', time() - 3600, '/');
    }
}

$loginUrl = $base . '/views/auth/login.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ContaTech — Sistema contable y facturación</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: Arial, Helvetica, sans-serif;
      background: #0a0a0a;
      color: #f5f5f5;
      min-height: 100vh;
      line-height: 1.5;
    }
    .nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 8%;
      border-bottom: 1px solid #222;
    }
    .logo { font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
    .nav a {
      color: #fff;
      text-decoration: none;
      padding: 10px 18px;
      border-radius: 8px;
      background: #fff;
      color: #000;
      font-weight: 600;
      font-size: 14px;
    }
    .nav a:hover { opacity: 0.9; }

    .hero {
      padding: 80px 8% 60px;
      max-width: 1100px;
      margin: 0 auto;
    }
    .hero h1 {
      font-size: clamp(2rem, 5vw, 3.2rem);
      font-weight: 800;
      line-height: 1.15;
      margin-bottom: 18px;
      max-width: 700px;
    }
    .hero p {
      font-size: 1.1rem;
      color: #aaa;
      max-width: 540px;
      margin-bottom: 32px;
    }
    .cta {
      display: inline-flex;
      gap: 12px;
      flex-wrap: wrap;
    }
    .btn {
      display: inline-block;
      padding: 14px 28px;
      border-radius: 10px;
      font-weight: 600;
      font-size: 15px;
      text-decoration: none;
      border: none;
      cursor: pointer;
    }
    .btn-primary { background: #fff; color: #000; }
    .btn-ghost {
      background: transparent;
      color: #fff;
      border: 1px solid #444;
    }
    .btn:hover { opacity: 0.9; }

    .features {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 16px;
      padding: 0 8% 80px;
      max-width: 1100px;
      margin: 0 auto;
    }
    .card {
      background: #141414;
      border: 1px solid #222;
      border-radius: 12px;
      padding: 22px;
    }
    .card h3 { font-size: 16px; margin-bottom: 8px; }
    .card p { font-size: 13px; color: #999; }

    footer {
      text-align: center;
      padding: 24px;
      color: #555;
      font-size: 12px;
      border-top: 1px solid #1a1a1a;
    }
  </style>
</head>
<body>
  <nav class="nav">
    <div class="logo">ContaTech</div>
    <a href="<?= htmlspecialchars($loginUrl) ?>">Iniciar sesión</a>
  </nav>

  <section class="hero">
    <h1>Contabilidad, inventario y facturación electrónica en un solo lugar</h1>
    <p>
      Gestiona usuarios, ventas, compras, pagos, DIAN y reportes contables
      con control por roles para tu equipo.
    </p>
    <div class="cta">
      <a class="btn btn-primary" href="<?= htmlspecialchars($loginUrl) ?>">Entrar al sistema</a>
      <a class="btn btn-ghost" href="#modulos">Ver módulos</a>
    </div>
  </section>

  <section class="features" id="modulos">
    <div class="card">
      <h3>Órdenes y facturación</h3>
      <p>Ventas, facturas electrónicas y envío a DIAN desde un solo flujo.</p>
    </div>
    <div class="card">
      <h3>Inventario</h3>
      <p>Productos, categorías, stock mínimo y control de bodega.</p>
    </div>
    <div class="card">
      <h3>Pagos y caja</h3>
      <p>Métodos de pago, conciliación e ingresos/egresos.</p>
    </div>
    <div class="card">
      <h3>Roles y seguridad</h3>
      <p>Admin, gerente, cajero, contable y más, cada uno con su panel.</p>
    </div>
  </section>

  <footer>ContaTech · Sistema de gestión empresarial</footer>
</body>
</html>