<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Producto | Conta Tech</title>
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
      <li><a href="../inventario/listaProductos.php" class="active">📦 Inventario</a></li>
      <li><a href="../nomina/nomina.php">📋 Nómina</a></li>
      <li><a href="support.php">🧰 Soporte</a></li>
    </ul>
  </div>
    <div class="main-content">
        <div class="header">
            <div>
                <h1>Inventario</h1>
                <p>Registrar</p>
            </div>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <!-- <p class="user"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p> -->
                <button class="logout"><a href="../../auth/logout.php">Log Out</a></button>
            <?php else: ?>
                <button class="logout"><a href="../../auth/login.php">Log In</a></button>
            <?php endif; ?>
        </div>
        <div class="top-bar">
            <div class="tabs">
                <button class="tab active"><a href="listaProductos.php">Lista de Productos</a></button>
                <button class="tab"><a href="alertasStock.php">Alerta de Stock</a></button>
            </div>
            <div class="search-box">
                <span class="icon">🔍</span>
                <input type="text" placeholder="Buscar.....">
            </div>
        </div>
        <div class="form">

            <!-- formulario para registrar un producto -->
            <form method="post" action="createProducto.php">
                <label>Nombre del producto</label>
                <input type="text" name="nombre_producto" placeholder="Nombre del producto" required>
    
                <label>Categoria</label>
                <select name="categoria_producto" id="categoria" required>
                    <option value="" disabled selected>Seleccione una categoría</option>
                    <option value="Lácteos y Derivados">Lácteos y Derivados</option>
                    <option value="Bebidas">Bebidas</option>
                    <option value="Cereales y granos">Cereales y granos</option>
                    <option value="Harina">Harina</option>
                    <option value="Panaderia y Reposteria">Panaderia y Reposteria</option>
                    <option value="Carnes y Embutidos">Carnes y Embutidos</option>
                    <option value="Pescados y Mariscos">Pescados y Mariscos</option>
                    <option value="Frutas">Frutas</option>
                    <option value="Verduras y Hortalizas">Verduras y Hortalizas</option>
                    <option value="Aceites y grasas">Aceites y grasas</option>
                    <option value="Condimentos y especias">Condimentos y especias</option>
                    <option value="Legumbres">Legumbres</option>
                    <option value="Frutos secos y semillas">Frutos secos y semillas</option>
                </select>
    
                <label>Precio</label>
                <input type="number" name="precio" placeholder="Precio del producto" required>
    
                <label>Cantidad</label>
                <input type="number" name="cantidad" placeholder="Cantidad del producto" required>
                
                <button type="submit" name="registro_producto" class="btn-email">Registrar Producto</button>
            </form>
        </div>

        

  </div>

</body>
</html>
