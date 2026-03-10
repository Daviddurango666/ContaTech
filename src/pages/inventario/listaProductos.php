<?php
session_start();
include("../../config/conexion.php");
// Verifica si el usuario ha iniciado sesión
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
        <form>
            <table>
                <thead>
                    <tr>
                        <th>Cod</th>
                        <th>Nombre del producto</th>
                        <th>Categoria</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Operaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include("../../config/conexion.php");
                    $query = "SELECT * FROM productos"; // Cambia 'inventario' por el nombre de tu tabla
                    // Asegúrate de que la consulta sea correcta según tu base de datos
                    $result = $conn->query($query);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['cod_producto']}</td>";
                            echo "<td>{$row['nombre_producto']}</td>";
                            echo "<td>{$row['categoria_producto']}</td>";
                            echo "<td>{$row['precio']}</td>";
                            echo "<td>{$row['cantidad']}</td>";
                            echo "<td>
                                <a href='actualizarProducto.php?cod_producto={$row['cod_producto']}'>Actualizar</a>
                                <br>
                                <a href='eliminarProducto.php?cod_producto={$row['cod_producto']}'>Eliminar</a>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No hay datos disponibles</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <!-- Botones --> 
            <div class="botones">
                <button><a href="registrarProducto.php">Agregar Producto</a></button>
            </div>

        </form>
    </div>
</body>
</html>
