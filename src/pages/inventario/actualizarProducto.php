<?php
session_start();
include("../../config/conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Producto | Conta Tech</title>
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
        <p>Actualizar Producto</p>
      </div>
      <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
        <button class="logout"><a href="../../auth/logout.php">Log Out</a></button>
      <?php else: ?>
        <button class="logout"><a href="../../auth/login.php">Log In</a></button>
      <?php endif; ?>
    </div>
    <!-- Formulario para actualizar un producto -->
    <div class="form">
      <form method="post" action="update_producto.php">
        <?php
        // Obtener el producto a actualizar
        if (isset($_GET['cod_producto'])) {
            $cod_producto = $_GET['cod_producto'];
            $statement = $conn->prepare("SELECT * FROM productos WHERE cod_producto = ?");
            $statement->bind_param("s", $cod_producto);
            $statement->execute();
            $result = $statement->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                ?>
                <input type="hidden" name="cod_producto" value="<?php echo htmlspecialchars($row['cod_producto']); ?>">
                <label for="nombre_producto">Nombre del Producto:</label>
                <input type="text" name="nombre_producto" value="<?php echo htmlspecialchars($row['nombre_producto']); ?>" required>
                
                <label for="categoria_producto">Categoria:</label>
                <select name="categoria_producto" id="categoria" required>
                    <option value="<?php echo htmlspecialchars($row['categoria_producto']); ?>" disabled selected>Seleccione una categoría</option>
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

                <label for="precio">Precio:</label>
                <input type="number" name="precio" value="<?php echo htmlspecialchars($row['precio']); ?>">

                <label for="cantidad">Cantidad:</label>
                <input type="number" name="cantidad" value="<?php echo htmlspecialchars($row['cantidad']); ?>">

                <button type="submit" name="actualizar_producto">Actualizar Producto</button>
                <?php
            } else {
                echo "<p>Producto no encontrado.</p>";
            }
        }
        ?>
      </form>
    </div>
  </div>
</body>
</html>