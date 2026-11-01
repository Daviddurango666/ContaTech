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
$registerUrl = $base . '/views/auth/register.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ContaTech — Sistema contable y facturación</title>
  <link rel="icon" href="<?= $base ?>/public/assets/img/favicon_io/favicon.ico">
  
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
      position: sticky;
      top: 0;
      background: #0a0a0a;
      z-index: 100;
    }
    .logo { 
      font-size: 22px; 
      font-weight: 700; 
      letter-spacing: -0.5px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .nav a {
      color: #fff;
      text-decoration: none;
      padding: 10px 16px;
      border-radius: 8px;
      font-weight: 500;
      font-size: 14px;
      transition: all 0.2s;
    }
    .nav a:hover {
      background: #1a1a1a;
    }
    .nav a.btn-nav {
      background: #fff;
      color: #000;
      font-weight: 600;
    }
    .nav a.btn-nav:hover {
      opacity: 0.9;
      background: #fff;
    }
    .menu {
      display: flex;
      gap: 8px;
      align-items: center;
      flex-wrap: wrap;
    }
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
      transition: opacity 0.2s;
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

    /* ===== FAQ ===== */
    .faq-section {
      padding: 60px 8% 80px;
      max-width: 900px;
      margin: 0 auto;
    }
    .faq-section h2 {
      font-size: 1.8rem;
      font-weight: 700;
      margin-bottom: 12px;
      text-align: center;
    }
    .faq-section > p {
      text-align: center;
      color: #888;
      margin-bottom: 40px;
      font-size: 15px;
    }
    .faq-item {
      background: #141414;
      border: 1px solid #222;
      border-radius: 12px;
      margin-bottom: 12px;
      overflow: hidden;
    }
    .faq-item summary {
      padding: 18px 22px;
      font-weight: 600;
      font-size: 15px;
      cursor: pointer;
      list-style: none;
      display: flex;
      justify-content: space-between;
      align-items: center;
      user-select: none;
      transition: background 0.2s;
    }
    .faq-item summary::-webkit-details-marker {
      display: none;
    }
    .faq-item summary::after {
      content: "+";
      font-size: 20px;
      color: #888;
      font-weight: 400;
      transition: transform 0.2s;
    }
    .faq-item[open] summary::after {
      content: "−";
      color: #fff;
    }
    .faq-item summary:hover {
      background: #1a1a1a;
    }
    .faq-item[open] summary {
      border-bottom: 1px solid #222;
    }
    .faq-content {
      padding: 18px 22px 22px;
      color: #bbb;
      font-size: 14px;
      line-height: 1.6;
    }

    /* ===== Contacto ===== */
    .contact-section {
      padding: 40px 8% 80px;
      max-width: 700px;
      margin: 0 auto;
      text-align: center;
    }
    .contact-section h2 {
      font-size: 1.6rem;
      margin-bottom: 12px;
    }
    .contact-section p {
      color: #888;
      font-size: 15px;
      margin-bottom: 24px;
    }
    .contact-box {
      background: #141414;
      border: 1px solid #222;
      border-radius: 12px;
      padding: 28px;
    }
    .contact-box p {
      color: #ccc;
      margin-bottom: 8px;
    }

    footer {
      text-align: center;
      padding: 24px;
      color: #555;
      font-size: 12px;
      border-top: 1px solid #1a1a1a;
    }

    @media (max-width: 700px) {
      .nav {
        flex-direction: column;
        gap: 16px;
        padding: 16px 5%;
      }
      .menu {
        justify-content: center;
      }
      .hero, .features, .faq-section, .contact-section {
        padding-left: 5%;
        padding-right: 5%;
      }
    }
  </style>
</head>
<body>
  <nav class="nav">
    <div class="logo">
      <img src="<?= $base ?>/public/assets/img/favicon_io/android-chrome-512x512.png" 
           alt="ContaTech" width="40" height="40">
      ContaTech
    </div>
    <div class="menu">
      <a href="#">Inicio</a>
      <a href="#modulos">Módulos</a>
      <a href="#faq">FAQ</a>
      <a href="#contacto">Contacto</a>
      <a class="btn-nav" href="<?= htmlspecialchars($loginUrl) ?>">Iniciar sesión</a>
      <a class="btn-nav" href="<?= htmlspecialchars($registerUrl) ?>">Registrarse</a>
    </div>
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

  <!-- ===================== FAQ ===================== -->
  <section class="faq-section" id="faq">
    <h2>Preguntas frecuentes</h2>
    <p>Respuestas a las dudas más comunes sobre el uso de ContaTech</p>

    <details class="faq-item">
      <summary>¿Qué es ContaTech?</summary>
      <div class="faq-content">
        ContaTech es un sistema orientado a apoyar procesos administrativos, comerciales y contables mediante módulos para gestionar usuarios, proveedores, inventario, compras, pedidos, pagos, facturas, ingresos, egresos e informes.
      </div>
    </details>

    <details class="faq-item">
      <summary>¿Cómo puedo ingresar al sistema?</summary>
      <div class="faq-content">
        Utilice la dirección web y las credenciales entregadas por el administrador del sistema. Si no cuenta con acceso, no puede iniciar sesión o recibe un mensaje de error, contacte al responsable de soporte indicando su usuario y el mensaje mostrado. <strong>Nunca envíe su contraseña.</strong>
      </div>
    </details>

    <details class="faq-item">
      <summary>¿Qué hago si olvidé mi contraseña?</summary>
      <div class="faq-content">
        Utilice el mecanismo de recuperación de contraseña disponible en la pantalla de acceso, si está habilitado. Si no aparece esa opción o no logra recuperar el acceso, solicite asistencia al administrador por el canal de soporte.
      </div>
    </details>

    <details class="faq-item">
      <summary>¿Por qué no puedo ver un módulo o realizar una acción?</summary>
      <div class="faq-content">
        El acceso puede depender del rol y los permisos asignados. Solicite al administrador que verifique su perfil y las autorizaciones correspondientes. Informe el módulo y la acción que necesita realizar.
      </div>
    </details>

    <details class="faq-item">
      <summary>¿Cómo registro un proveedor?</summary>
      <div class="faq-content">
        Ingrese al módulo de proveedores y siga el formulario habilitado por el sistema. Complete los campos obligatorios y revise la información antes de guardar. Si el registro ya existe o aparece un error, reporte el mensaje al soporte.
      </div>
    </details>

    <details class="faq-item">
      <summary>¿Qué debo revisar antes de registrar una compra?</summary>
      <div class="faq-content">
        Verifique que el proveedor esté registrado y que los productos o referencias requeridos estén disponibles en el sistema. Confirme cantidades, valores y demás datos solicitados antes de guardar la operación.
      </div>
    </details>

    <details class="faq-item">
      <summary>¿Qué hago si las existencias del inventario no coinciden?</summary>
      <div class="faq-content">
        Evite registrar movimientos duplicados para corregir la diferencia. Reúna el código del producto, la cantidad que muestra el sistema, la cantidad verificada y los movimientos recientes; después solicite una revisión.
      </div>
    </details>

    <details class="faq-item">
      <summary>¿Cómo consulto o gestiono un pedido?</summary>
      <div class="faq-content">
        Acceda al módulo de pedidos y localice el registro mediante los filtros disponibles. Revise su estado y los datos asociados. Si el pedido no aparece o no permite actualizarse, comparta su identificador con soporte.
      </div>
    </details>

    <details class="faq-item">
      <summary>¿Qué hago si un pago aparece pendiente o no se refleja?</summary>
      <div class="faq-content">
        Compruebe el estado de la operación y conserve el comprobante o referencia de la transacción. No repita el pago sin confirmar primero su estado. Envíe la referencia al canal de soporte, ocultando datos financieros sensibles.
      </div>
    </details>

    <details class="faq-item">
      <summary>¿Qué hago si una factura presenta errores?</summary>
      <div class="faq-content">
        Revise los datos del tercero, los conceptos, valores y campos obligatorios. Si se trata de facturación electrónica, conserve el mensaje o código de respuesta y solicite revisión. No dé por emitida o aceptada una factura hasta confirmar su estado.
      </div>
    </details>

    <details class="faq-item">
      <summary>¿Cómo consulto ingresos y egresos?</summary>
      <div class="faq-content">
        Ingrese al módulo correspondiente y aplique los filtros de fecha o categoría disponibles. Si encuentra diferencias, indique el periodo consultado y los identificadores de los movimientos relacionados.
      </div>
    </details>

    <details class="faq-item">
      <summary>¿Por qué un informe no muestra información?</summary>
      <div class="faq-content">
        Verifique el rango de fechas, los filtros y que existan registros para el periodo consultado. Si el resultado continúa vacío o parece incompleto, envíe una captura y describa los filtros aplicados.
      </div>
    </details>

    <details class="faq-item">
      <summary>¿Qué información debo enviar al reportar un error?</summary>
      <div class="faq-content">
        Incluya módulo, acción realizada, fecha y hora aproximadas, pasos para reproducir el problema, mensaje de error y captura de pantalla cuando sea posible. <strong>No comparta contraseñas ni información confidencial.</strong>
      </div>
    </details>

    <details class="faq-item">
      <summary>¿Puedo compartir mi usuario y contraseña con soporte?</summary>
      <div class="faq-content">
        No. Las credenciales son personales y confidenciales. El equipo de soporte no debe solicitar su contraseña. Si necesita ayuda con el acceso, describa el problema sin revelar secretos de autenticación.
      </div>
    </details>

    <details class="faq-item">
      <summary>¿El soporte por WhatsApp está disponible las 24 horas?</summary>
      <div class="faq-content">
        El horario de atención no está especificado en esta guía. Consulte con el responsable del canal para conocer los días y horarios de servicio, así como los tiempos estimados de respuesta.
      </div>
    </details>
  </section>

  <!-- ===================== Contacto ===================== -->
  <section class="contact-section" id="contacto">
    <h2>Contacto y soporte</h2>
    <p>¿Necesitas ayuda adicional? Comunícate con el administrador del sistema o el canal de soporte oficial.</p>
    <div class="contact-box">
      <p>Para reportar errores o solicitar acceso, incluye siempre:</p>
      <p style="color:#aaa; font-size:13px; margin-top:12px;">
        • Módulo y acción que intentabas realizar<br>
        • Fecha y hora aproximada<br>
        • Mensaje de error (si aparece)<br>
        • Captura de pantalla (si es posible)
      </p>
    </div>
  </section>

  <footer>ContaTech · Sistema de gestión empresarial</footer>
</body>
</html>