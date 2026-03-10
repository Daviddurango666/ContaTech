    <?php
session_start();
// Verifica si el usuario ha iniciado sesión
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Facturacion | Conta Tech</title>
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
      <li><a href="../compras/ComprasProveedores.php" class="active">🛒 Compras y proveedores</a></li>
      <li><a href="../gastos_ingresos/gasto_ingreso.php">💸 Ingresos y Egresos</a></li>
      <li><a href="../reportes/ingresos.php">📊 Reportes contables</a></li>
      <li><a href="../inventario/listaProductos.php">📦 Inventario</a></li>
      <li><a href="../nomina/nomina.php">📋 Nómina</a></li>
      <li><a href="support.php">🧰 Soporte</a></li>
    </ul>
  </div>
    <div class="main-content">
        <div class="header">
            <div>
                <h1>Compras</h1>
            </div>
            <button class="logout">Log Out</button>
        </div>
        <div class="top-bar">
            <div class="tabs">
                <button class="tab"><a href="ComprasProveedores.php">Factura de Compra</a></button>
                <button class="tab"> <a href="gestionProveedores.php">Gestion de Proveedores</a></button>
                <button class="tab  active"><a href="ordenesCompra.php">Ordenes de Compra</a></button>
            </div>
            <div class="search-box">
                <span class="icon">🔍</span>
                <input type="text" placeholder="Buscar.....">
            </div>
        </div>
        <div class="compras-grid">

            <!-- Tarjetas de Compra -->
            <div class="compras">
                <h4>Compra 1</h4>
                <h2>98,353</h2>
                <p class="desc">-8% descuento Especial</p>
            </div>
    
            <div class="compras">
                <h4>Compra 2</h4>
                <p>Reporte Ingresos Compra</p>
                <h2>32,353</h2>
                <p class="desc">-8% descuento Especial</p>
            </div>
    
            <div class="compras">
                <h4>Compra 3</h4>
                <h2>20,353</h2>
                <p class="desc">-8% descuento Especial</p>
            </div>
    
            <!-- Reportes de ingreso -->
            <div class="compras">
                <p>Reporte Ingresos Compra</p>
                <h2>$72,405.40</h2>
                <p class="light">+33% month over month</p>
            </div>
    
            <div class="compras">
                <p>Reporte Ingresos Compra</p>
                <h2>$232,678.90</h2>
                <p class="light">+33% month over month</p>
            </div>
    
            <div class="compras">
                <p>Reporte Ingresos Compra</p>
                <h2>$57,405.40</h2>
                <p class="light">+33% month over month</p>
            </div>
    
            <!-- Totales -->
            <div class="compras">
                <p>Total compra</p>
                <h2>$215,678.90</h2>
                <p class="light">+20% month over month</p>
            </div>
    
            <div class="compras">
                <p>Total compra</p>
                <h2>$232,678.90</h2>
                <p class="light">+20% month over month</p>
            </div>
    
            <div class="compras">
                <p>Total compra</p>
                <h2>$145,678.90</h2>
                <p class="light">+20% month over month</p>
            </div>
        </div>

  </div>

</body>
</html>

    
    
    