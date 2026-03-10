<?php
session_start();
include("../../config/conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gastos | Conta Tech</title>
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
        <li><a href="../reportes/ingresos.php" class="active">📊 Reportes contables</a></li>
        <li><a href="../inventario/listaProductos.php">📦 Inventario</a></li>
        <li><a href="../nomina/nomina.php">📋 Nómina</a></li>
        <li><a href="support.php">🧰 Soporte</a></li>
    </ul>
  </div>
    <div class="main-content">
        <div class="header">
            <div>
                <h1>Reportes</h1>
                <p>Gastos e Ingresos</p>
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
                <button class="tab"><a href="ingresos.php">Ingresos</a></button>
                <button class="tab active"><a href="gastos.php">Gastos</a></button>
            </div>
        </div>
        <form>
            <table>
                <thead>
                    <tr>
                        <th>Cod Gasto</th>
                        <th>Descripcion</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                        <th>Categoria</th>
                        <th>Operaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include("../../config/conexion.php");
                    $query = "SELECT * FROM gastos"; // Cambia 'inventario' por el nombre de tu tabla
                    // Asegúrate de que la consulta sea correcta según tu base de datos
                    $result = $conn->query($query);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['id_gasto']}</td>";
                            echo "<td>{$row['descripcion']}</td>";
                            echo "<td>{$row['monto']}</td>";
                            echo "<td>{$row['fecha']}</td>";
                            echo "<td>{$row['categoria']}</td>";
                            echo "<td>
                                <a href='actualizarIngreso.php?cod={$row['id_gasto']}'>Actualizar</a>
                                <br>
                                <a href='eliminarIngreso.php?cod={$row['id_gasto']}'>Eliminar</a></td>";
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
                <button><a href="../gastos_ingresos/gasto_ingreso.php">Agregar Gasto</a></button>
            </div>
        </form>
        

  </div>

</body>
</html>

