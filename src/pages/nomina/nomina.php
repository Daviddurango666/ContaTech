<?php
session_start();
// Verifica si el usuario ha iniciado sesión
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nomina | Conta Tech</title>
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
        <li><a href="../inventario/listaProductos.php">📦 Inventario</a></li>
        <li><a href="../nomina/nomina.php" class="active">📋 Nómina</a></li>
        <li><a href="support.php">🧰 Soporte</a></li>
    </ul>
  </div>
    <div class="main-content">
        <div class="header">
            <div>
                <h1>Nomina</h1>
            </div>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <!-- <p class="user"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p> -->
                <button class="logout"><a href="../../auth/logout.php">Log Out</a></button>
            <?php else: ?>
                <button class="logout"><a href="../../auth/login.php">Log In</a></button>
            <?php endif; ?>
        </div>
        <div class="top-bar">
            <h2>Lista de Empleados</h2>
            <div class="search-box">
                <span class="icon">🔍</span>
                <input type="text" placeholder="Buscar.....">
            </div>
        </div>
        <form>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Empleado</th>
                        <th>Salario</th>
                        <th>Tipo de Contrato</th>
                        <th>Fecha de Ingreso</th>
                        <th>Operaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include("../../config/conexion.php");
                    $query = "SELECT * FROM empleados";
                    $result = $conn->query($query);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['id_empleado']}</td>";
                            echo "<td>{$row['nombre_empleado']}</td>";
                            echo "<td>{$row['salario']}</td>";
                            echo "<td>{$row['tipo_contrato']}</td>";
                            echo "<td>{$row['fecha_ingreso']}</td>";
                            echo "<td><a href='actualizarEmpleado.php?id_empleado={$row['id_empleado']}'>Actualizar</a>
                                <br>
                                <a href='eliminarEmpleado.php?id_empleado={$row['id_empleado']}'>Eliminar</a>
                                <br>
                                <a href='historialPago.php?id_empleado={$row['id_empleado']}'>Ver Historial de Pagos</a>
                                
                                </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No hay datos disponibles</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </form>
        <!-- Botones -->
        <div class="botones">
            <button><a href="registroEmpleados.php">Registrar Empleado</a></button>
            <button><a href="calcular_salarios.php">Calcular Salarios</a></button>
        </div>
    </div>

</body>
</html>
