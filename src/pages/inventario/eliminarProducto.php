<?php
session_start();
include("../../config/conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario | Conta Tech</title>
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
            </div>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <button class="logout"><a href="../../auth/logout.php">Log Out</a></button>
            <?php else: ?>
                <button class="logout"><a href="../../auth/login.php">Log In</a></button>
            <?php endif; ?>
        </div>
        <form method="POST" action="">
            <?php
                // Verificar si se ha enviado el formulario para eliminar
                if (isset($_POST['eliminarProducto'])) {
                    $cod_producto = $_POST['cod_producto'];
                    // Preparar la consulta para eliminar el producto
                    $query = "DELETE FROM productos WHERE cod_producto = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("s", $cod_producto);
                    if ($stmt->execute()) {
                        echo "<script>alert('Producto eliminado exitosamente');</script>";
                    } else {
                        echo "<script>alert('Error al eliminar el producto');</script>";
                    }
                    $stmt->close();
                }
                // Obtener el producto a eliminar
                $statement = $conn->prepare("SELECT * FROM productos WHERE cod_producto = ?");
                $statement->bind_param("s", $_GET['cod_producto']);
                $statement->execute();
                $result = $statement->get_result();
                if($result -> num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo "<h2 style='text-align:center; padding: 30px 20px ;'>Informacion del Producto</h2>";
                    echo "<hr>";
                    echo "<p style='text-align:center; font-size:18px; font-weight:700;'>Codigo del Producto: {$row['cod_producto']}</p>";
                    echo "<p style='text-align:center; font-size:18px;'>Nombre del Producto: {$row['nombre_producto']}</p>";
                    echo "<p style='text-align:center; font-size:18px;'>Categoria: {$row['categoria_producto']}</p>";
                    echo "<p style='text-align:center; font-size:18px;'>Precio: {$row['precio']}</p>";
                    echo "<p style='text-align:center; font-size:18px;'>Cantidad: {$row['cantidad']}</p>";
                    // Campo oculto para el código del producto
                    echo "<input type='hidden' name='cod_producto' value='{$row['cod_producto']}'>";
                } else {
                    echo "<p>Producto no encontrado.</p>";
                }
            ?>
            <!-- Botones --> 
            <div class="botones">
                <button type="submit" name="eliminarProducto">Eliminar Producto</button>
            </div>
        </form>
    </div>
</body>
</html>