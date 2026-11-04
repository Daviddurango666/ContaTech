<?php
require __DIR__ . '/../layouts/auth_check.php';
if (!RolePermissions::can($currentUser['rol'], 'soporte', 'view')) {
    header('Location: ' . $appCfg['base_url'] . '/views/dashboard/index.php');
    exit;
}
$pageTitle = 'Soporte';
$currentRoute = 'soporte';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';

$base = $appCfg['base_url'] ?? '';
$whatsappNumber = '+57 300 535 1013 - +57 301 663 3097';
$whatsappLink1   = 'https://wa.me/573005351013';
$whatsappLink2   = 'https://wa.me/573016633097';
?>
<main class="main-content">
  <div class="header">
    <h1>Soporte técnico</h1>
    <div class="user-info">
      <span><?= htmlspecialchars($appCfg['roles'][$currentUser['rol']] ?? $currentUser['rol']) ?></span>
    </div>
  </div>

  <!-- Canal de contacto -->
  <div class="form-card" style="max-width:100%;margin-bottom:20px;border-left:4px solid #16a34a;">
    <h2 style="margin:0 0 8px;font-size:18px;">Canal de soporte por WhatsApp</h2>
    <p style="margin:0 0 12px;color:#555;">
      Número oficial: <strong><?= htmlspecialchars($whatsappNumber) ?></strong>
    </p>
    <a class="btn btn-success" href="<?= htmlspecialchars($whatsappLink1) ?>" target="_blank" rel="noopener">
        Abrir conversación en WhatsApp
    </a>
    <br>
    <br>
    <a class="btn btn-success" href="<?= htmlspecialchars($whatsappLink2) ?>" target="_blank" rel="noopener">
      Abrir conversación en WhatsApp
    </a>
    <p style="margin-top:12px;font-size:13px;color:#777;">
      Verifique con el responsable del canal el horario de atención y los tiempos de respuesta.
      No envíe contraseñas ni datos bancarios completos por este medio.
    </p>
  </div>

  <!-- Acciones rápidas -->
  <div class="cards" style="margin-bottom:24px;">
    <div class="card">
      <div class="label">Consulta de usuarios</div>
      <div class="value" style="font-size:16px;font-weight:600;margin:8px 0 12px;">Asistencia a usuarios del sistema</div>
      <a class="btn btn-secondary btn-sm" href="<?= htmlspecialchars($base) ?>/views/usuarios/index.php">Ver usuarios</a>
    </div>
    <div class="card">
      <div class="label">Módulos principales</div>
      <div style="font-size:13px;color:#555;line-height:1.6;margin-top:8px;">
        Usuarios · Proveedores · Inventario · Compras · Pedidos · Pagos · Facturas · Ingresos/Egresos · Informes
      </div>
    </div>
    <div class="card">
      <div class="label">Objetivo de la mesa de ayuda</div>
      <div style="font-size:13px;color:#555;line-height:1.6;margin-top:8px;">
        Orientar el uso, recibir reportes de incidentes y facilitar el seguimiento de las solicitudes.
      </div>
    </div>
  </div>

  <!-- Cómo solicitar soporte -->
  <div class="form-card" style="max-width:100%;margin-bottom:20px;">
    <h2 style="margin:0 0 14px;font-size:18px;">¿Cómo solicitar soporte?</h2>
    <ol style="margin:0;padding-left:20px;color:#444;line-height:1.7;">
      <li><strong>Identifique el módulo:</strong> usuarios, proveedores, inventario, compras, pedidos, pagos, facturas o informes.</li>
      <li><strong>Describa lo ocurrido:</strong> qué intentaba hacer, qué esperaba y qué sucedió realmente.</li>
      <li><strong>Incluya evidencia:</strong> captura de pantalla sin contraseñas, datos bancarios ni información personal innecesaria.</li>
      <li><strong>Comparta referencias:</strong> fecha/hora aproximadas, usuario o rol involucrado e identificador del registro (si existe).</li>
      <li><strong>Espere la orientación:</strong> el equipo podrá solicitar datos adicionales para reproducir el inconveniente.</li>
    </ol>
  </div>

  <!-- Datos del reporte -->
  <div class="form-card" style="max-width:100%;margin-bottom:20px;">
    <h2 style="margin:0 0 14px;font-size:18px;">Datos que debe contener un reporte</h2>
    <div class="table-wrap" style="box-shadow:none;">
      <table class="data">
        <thead>
          <tr>
            <th>Campo</th>
            <th>Información a proporcionar</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>Nombre o identificación</td><td>Nombre o código de usuario (sin contraseñas)</td></tr>
          <tr><td>Módulo afectado</td><td>Usuarios, proveedores, inventario, compras, pedidos, pagos, facturas o informes</td></tr>
          <tr><td>Descripción</td><td>Qué intentaba hacer y qué ocurrió</td></tr>
          <tr><td>Fecha y hora</td><td>Momento aproximado del inconveniente</td></tr>
          <tr><td>Evidencia</td><td>Captura o mensaje de error, ocultando datos sensibles</td></tr>
          <tr><td>Referencia</td><td>Código del registro o transacción, si aplica</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Clasificación -->
  <div class="form-card" style="max-width:100%;margin-bottom:20px;">
    <h2 style="margin:0 0 14px;font-size:18px;">Clasificación sugerida de solicitudes</h2>
    <div class="table-wrap" style="box-shadow:none;">
      <table class="data">
        <thead>
          <tr>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Ejemplo</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><span class="badge" style="background:#fee2e2;color:#991b1b;">Crítico</span></td>
            <td>Impide el uso general del sistema o compromete la integridad de la información</td>
            <td>No es posible iniciar sesión para ningún usuario</td>
          </tr>
          <tr>
            <td><span class="badge" style="background:#ffedd5;color:#9a3412;">Alto</span></td>
            <td>Afecta una función importante y no existe alternativa razonable</td>
            <td>No se puede registrar una compra necesaria para la operación</td>
          </tr>
          <tr>
            <td><span class="badge" style="background:#fef3c7;color:#92400e;">Medio</span></td>
            <td>Afecta una función, pero existe alternativa temporal</td>
            <td>Un filtro de consulta no funciona correctamente</td>
          </tr>
          <tr>
            <td><span class="badge" style="background:#dbeafe;color:#1e40af;">Consulta</span></td>
            <td>Duda de uso, orientación o petición de mejora</td>
            <td>Consulta sobre cómo generar un informe</td>
          </tr>
        </tbody>
      </table>
    </div>
    <p style="margin:12px 0 0;font-size:13px;color:#777;">
      Los tiempos de respuesta y solución los define el equipo responsable del proyecto.
    </p>
  </div>

  <!-- FAQ -->
  <div class="form-card" style="max-width:100%;margin-bottom:20px;">
    <h2 style="margin:0 0 16px;font-size:18px;">Preguntas frecuentes (FAQ)</h2>

    <details style="margin-bottom:12px;border:1px solid #eee;border-radius:8px;padding:12px 14px;">
      <summary style="cursor:pointer;font-weight:600;">¿Qué es ContaTech?</summary>
      <p style="margin:10px 0 0;color:#555;line-height:1.6;">
        Sistema orientado a apoyar procesos administrativos, comerciales y contables mediante módulos para gestionar usuarios, proveedores, inventario, compras, pedidos, pagos, facturas, ingresos, egresos e informes.
      </p>
    </details>

    <details style="margin-bottom:12px;border:1px solid #eee;border-radius:8px;padding:12px 14px;">
      <summary style="cursor:pointer;font-weight:600;">¿Cómo puedo ingresar al sistema?</summary>
      <p style="margin:10px 0 0;color:#555;line-height:1.6;">
        Utilice la dirección web y las credenciales entregadas por el administrador. Si no puede iniciar sesión, contacte a soporte indicando su usuario y el mensaje de error. <strong>Nunca envíe su contraseña.</strong>
      </p>
    </details>

    <details style="margin-bottom:12px;border:1px solid #eee;border-radius:8px;padding:12px 14px;">
      <summary style="cursor:pointer;font-weight:600;">¿Qué hago si olvidé mi contraseña?</summary>
      <p style="margin:10px 0 0;color:#555;line-height:1.6;">
        Use el mecanismo de recuperación en la pantalla de acceso (si está habilitado). Si no aparece o no funciona, solicite asistencia al administrador por el canal de soporte.
      </p>
    </details>

    <details style="margin-bottom:12px;border:1px solid #eee;border-radius:8px;padding:12px 14px;">
      <summary style="cursor:pointer;font-weight:600;">¿Por qué no puedo ver un módulo o realizar una acción?</summary>
      <p style="margin:10px 0 0;color:#555;line-height:1.6;">
        El acceso depende del rol y los permisos asignados. Solicite al administrador que verifique su perfil e indique el módulo y la acción que necesita realizar.
      </p>
    </details>

    <details style="margin-bottom:12px;border:1px solid #eee;border-radius:8px;padding:12px 14px;">
      <summary style="cursor:pointer;font-weight:600;">¿Cómo registro un proveedor?</summary>
      <p style="margin:10px 0 0;color:#555;line-height:1.6;">
        Ingrese al módulo de proveedores, complete el formulario con los campos obligatorios y revise la información antes de guardar. Si aparece un error o el registro ya existe, reporte el mensaje a soporte.
      </p>
    </details>

    <details style="margin-bottom:12px;border:1px solid #eee;border-radius:8px;padding:12px 14px;">
      <summary style="cursor:pointer;font-weight:600;">¿Qué debo revisar antes de registrar una compra?</summary>
      <p style="margin:10px 0 0;color:#555;line-height:1.6;">
        Verifique que el proveedor esté registrado y que los productos estén disponibles en el sistema. Confirme cantidades, valores y demás datos antes de guardar.
      </p>
    </details>

    <details style="margin-bottom:12px;border:1px solid #eee;border-radius:8px;padding:12px 14px;">
      <summary style="cursor:pointer;font-weight:600;">¿Qué hago si las existencias del inventario no coinciden?</summary>
      <p style="margin:10px 0 0;color:#555;line-height:1.6;">
        Evite registrar movimientos duplicados. Reúna el código del producto, la cantidad del sistema, la cantidad verificada y los movimientos recientes; luego solicite una revisión.
      </p>
    </details>

    <details style="margin-bottom:12px;border:1px solid #eee;border-radius:8px;padding:12px 14px;">
      <summary style="cursor:pointer;font-weight:600;">¿Cómo consulto o gestiono un pedido?</summary>
      <p style="margin:10px 0 0;color:#555;line-height:1.6;">
        Acceda al módulo de pedidos, localice el registro con los filtros disponibles y revise su estado. Si no aparece o no se actualiza, comparta el identificador con soporte.
      </p>
    </details>

    <details style="margin-bottom:12px;border:1px solid #eee;border-radius:8px;padding:12px 14px;">
      <summary style="cursor:pointer;font-weight:600;">¿Qué hago si un pago aparece pendiente o no se refleja?</summary>
      <p style="margin:10px 0 0;color:#555;line-height:1.6;">
        Compruebe el estado y conserve el comprobante o referencia. No repita el pago sin confirmar. Envíe la referencia a soporte ocultando datos financieros sensibles.
      </p>
    </details>

    <details style="margin-bottom:12px;border:1px solid #eee;border-radius:8px;padding:12px 14px;">
      <summary style="cursor:pointer;font-weight:600;">¿Qué hago si una factura presenta errores?</summary>
      <p style="margin:10px 0 0;color:#555;line-height:1.6;">
        Revise datos del tercero, conceptos, valores y campos obligatorios. En facturación electrónica conserve el mensaje o código de respuesta y solicite revisión. No dé por emitida una factura hasta confirmar su estado.
      </p>
    </details>

    <details style="margin-bottom:12px;border:1px solid #eee;border-radius:8px;padding:12px 14px;">
      <summary style="cursor:pointer;font-weight:600;">¿Cómo consulto ingresos y egresos?</summary>
      <p style="margin:10px 0 0;color:#555;line-height:1.6;">
        Ingrese al módulo correspondiente y aplique filtros de fecha o categoría. Si hay diferencias, indique el periodo y los identificadores de los movimientos relacionados.
      </p>
    </details>

    <details style="margin-bottom:12px;border:1px solid #eee;border-radius:8px;padding:12px 14px;">
      <summary style="cursor:pointer;font-weight:600;">¿Por qué un informe no muestra información?</summary>
      <p style="margin:10px 0 0;color:#555;line-height:1.6;">
        Verifique el rango de fechas, los filtros y que existan registros para el periodo. Si sigue vacío, envíe una captura y describa los filtros aplicados.
      </p>
    </details>

    <details style="margin-bottom:12px;border:1px solid #eee;border-radius:8px;padding:12px 14px;">
      <summary style="cursor:pointer;font-weight:600;">¿Puedo compartir mi usuario y contraseña con soporte?</summary>
      <p style="margin:10px 0 0;color:#555;line-height:1.6;">
        <strong>No.</strong> Las credenciales son personales y confidenciales. El equipo de soporte no debe solicitar su contraseña. Describa el problema sin revelar secretos de autenticación.
      </p>
    </details>

    <details style="border:1px solid #eee;border-radius:8px;padding:12px 14px;">
      <summary style="cursor:pointer;font-weight:600;">¿El soporte por WhatsApp está disponible las 24 horas?</summary>
      <p style="margin:10px 0 0;color:#555;line-height:1.6;">
        El horario no está fijado en esta guía. Consulte con el responsable del canal los días, horarios de servicio y tiempos estimados de respuesta.
      </p>
    </details>
  </div>

  <!-- Seguridad -->
  <div class="form-card" style="max-width:100%;margin-bottom:20px;">
    <h2 style="margin:0 0 12px;font-size:18px;">Recomendaciones de seguridad</h2>
    <ul style="margin:0;padding-left:20px;color:#444;line-height:1.7;">
      <li>No comparta contraseñas, códigos de verificación ni enlaces privados de recuperación.</li>
      <li>No envíe números completos de tarjetas, claves bancarias ni documentos personales por WhatsApp.</li>
      <li>Comparta únicamente la información necesaria para diagnosticar la solicitud.</li>
      <li>Cierre la sesión en equipos compartidos y proteja sus dispositivos.</li>
      <li>Verifique que los cambios de inventario, pagos y facturas correspondan a operaciones autorizadas.</li>
    </ul>
  </div>

  <!-- Nota administrativa -->
  <div class="alert" style="background:#f8fafc;color:#475569;border:1px solid #e2e8f0;">
    <strong>Nota:</strong> Antes de publicar esta guía de forma definitiva, confirme con el responsable de ContaTech:
    (1) que el número de WhatsApp está activo y bajo control del equipo de soporte;
    (2) el horario de atención;
    (3) los responsables de atender y escalar solicitudes;
    (4) los tiempos objetivo de primera respuesta y resolución;
    (5) los procedimientos reales de recuperación de contraseña, facturación y pagos implementados en el sistema.
  </div>
</main>
</body>
</html>
