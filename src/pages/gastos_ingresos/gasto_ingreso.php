<?php
session_start();
include("../../config/conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gastos e Ingresos | Conta Tech</title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
    <script src="../../../assets/js/script.js"></script>
    <link rel="icon" href="../../../assets/img/favicon_io/favicon.ico" type="image/x-icon">
</head>
<body>

  <div class="sidebar">
    <h2 class="app-title">Conta Tech</h2>
    <ul class="menu">
      <li><a href="../../../index.php">🏠 DashBoard</a></li>
      <li><a href="../facturacion/crearFacturacion.php">🧾 Facturación</a></li>
      <li><a href="../compras/ComprasProveedores.php">🛒 Compras y proveedores</a></li>
      <li><a href="../gastos_ingresos/gasto_ingreso.php" class="active">💸 Ingresos y Egresos</a></li>
      <li><a href="../reportes/ingresos.php">📊 Reportes contables</a></li>
      <li><a href="../inventario/listaProductos.php">📦 Inventario</a></li>
      <li><a href="../nomina/nomina.php">📋 Nómina</a></li>
      <li><a href="support.php">🧰 Soporte</a></li>
    </ul>
  </div>
    <div class="main-content">
        <div class="header">
            <div>
                <h1>Gestion</h1>
            </div>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <!-- <p class="user"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p> -->
                <button class="logout"><a href="../../auth/logout.php">Log Out</a></button>
            <?php else: ?>
                <button class="logout"><a href="../../auth/login.php">Log In</a></button>
            <?php endif; ?>
        </div>
        <!-- form de gastos e ingresos -->
         <div class="gastos_ingresos-grid">
            <div class="form">

                <h2>Registrar Ingresos</h2>
                <form method="post" action="procesar_ingreso.php">
                    <label>Descripcion</label>
                    <input type="text" name="descripcion" placeholder="Descripcion" required>
                    
                    <label>Monto</label>
                    <input type="number" name="monto" placeholder="Monto" required>
                    
                    <label>Fecha</label>
                    <input type="date" name="fecha" required>
        
                    <label>Categoria</label>
                    <select name="categoria" required>
                        <option value="ventas">Ingreso</option>
                        <option value="intereses">intereses</option>
                        <option value="alquiler">alquiler</option>
                        <option value="comisiones">comisiones</option>
                        <option value="servicios">servicios</option>
                        <option value="donaciones">donaciones</option>
                        <option value="subvenciones">subvenciones</option>
                        <option value="otros ingreos">otros ingreos</option>
                    </select>
                    <button type="submit">Registrar Ingreso</button>
                </form>
            </div>

            <div class="form">
                <h2>Registrar Gasto</h2>
                <form method="post" action="procesar_gasto.php">
                    <label>Descripcion</label>
                    <input type="text" name="descripcion" placeholder="Descripcion" required>
                    
                    <label>Monto</label>
                    <input type="number" name="monto" placeholder="Monto" required>
                    
                    <label>Fecha</label>
                    <input type="date" name="fecha" required>
        
                    <label>Categoria</label>
                    <select name="categoria" required>
                        <option value="alquiler">alquiler</option>
                        <option value="sueldos y salarios">sueldos y salarios</option>
                        <option value="servicios publicos">servicios publicos</option>
                        <option value="materiales">materiales</option>
                        <option value="marketing y publicidad">marketing y publicidad</option>
                        <option value="mantenimiento">mantenimiento</option>
                        <option value="transporte">transporte</option>
                        <option value="impuestos">impuestos</option>
                        <option value="seguros">seguros</option>
                        <option value="otros gastos">otros gastos</option>
                    </select>
                    <button type="submit">Registrar Gasto</button>
                </form>   
            </div>

         </div>


  </div>

</body>
</html>
