<?php
include("../../config/conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Empleado | Conta Tech</title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
    <script src="../../assets/js/script.js"></script>
    <link rel="icon" href="../../../assets/img/favicon_io/favicon.ico" type="image/x-icon">
</head>
<body>

  <div class="sidebar">
    <h2 class="app-title">Conta Tech</h2>
    <ul class="menu">
      <li><a href="../../../index.php">🏠 DashBoard</a></li>
        <li><a href="../facturacion/crearFacturacion.php">🧾 Facturación</a></li>
        <li><a href="../compras/ComprasProveedores.php">🛒 Compras y proveedores</a></li>
        <li><a href="../gastos_ingresos/gasto_ingreso.php">💸 Ingresos y Egresos</a></li>
        <li><a href="../reportes/ingresos.php">📊 Reportes contables</a></li>
        <li><a href="../inventario/listaProductos.php">📦 Inventario</a></li>
        <li><a href="../nomina/nomina.php" class="active">📋 Nómina</a></li>
        <li><a href="support.php">🧰 Soporte</a></li>
    </ul>
  </div>
    <div class="main-content">
        <div class="header">
            <div>
                <h1>Nomina</h1>
                <p>Registrar Empleado</p>
            </div>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <!-- <p class="user"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p> -->
                <button class="logout"><a href="../../auth/logout.php">Log Out</a></button>
            <?php else: ?>
                <button class="logout"><a href="../../auth/login.php">Log In</a></button>
            <?php endif; ?>
        </div>
        <!-- formulario para registrar un empleado -->
         <div class="form">

             <form method="post" action="registrar_empleado.php">
                 <label>ID del Empleado</label>
                 <input type="number" name="id_empleado" placeholder="ID del empleado" required>
     
                 <label>Nombre del Empleado</label>
                 <input type="text" name="nombre_empleado" placeholder="Nombre del empleado" required>
                 
                 <label>Salario</label>
                 <input type="number" name="salario" placeholder="Salario del empleado" required>
                 
                 <label>Tipo de Contrato</label>
                 <select name="tipo_contrato" id="categoria" required>
                     <option value="" disabled selected>Seleccione una categoría</option>
                     <option value="Contrato indefinido">Contrato indefinido</option>
                     <option value="Contrato temporal">Contrato temporal</option>
                     <option value="Contrato a tiempo completo">Contrato a tiempo completo</option>
                     <option value="Contrato a tiempo parcial">Contrato a tiempo parcial</option>
                     <option value="Contrato por horas">Contrato por horas</option>
                     <option value="Contrato de obra o servicio">Contrato de obra o servicio</option>
                     <option value="Contrato eventual">Contrato eventual</option>
                     <option value="Contrato presencial">Contrato presencial</option>
                     <option value="Contrato de teletrabajo (o trabajo remoto)">Contrato de teletrabajo (o trabajo remoto)</option>
                     <option value="Contrato de prácticas">Contrato de prácticas</option>
                     <option value="Contrato de aprendizaje">Contrato de aprendizaje</option>
                 </select>
     
                 <label>Fecha de Ingreso</label>
                 <input type="date" name="fecha_ingreso" placeholder="Fecha de Ingreso" required>
     
                 <button type="submit" name="registro_empleado" class="btn-email">Registrar Empleado</button>
             </form>
         </div>
  </div>
</body>
</html>
