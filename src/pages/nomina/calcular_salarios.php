<?php
session_start();
include("../../config/conexion.php");
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
            <h2>Calculo de Salarios</h2>
            <div class="search-box">
                <span class="icon">🔍</span>
                <input type="text" placeholder="Buscar.....">
            </div>
        </div>
        <form>
            <table>
                <thead>
                    <tr>
                        <th>ID Empleado</th>
                        <th>Nombre del Empleado</th>
                        <th>Salario Base</th>
                        <th>Tipo de Contrato</th>
                        <th>Fecha de Ingreso</th>
                        <th>Salario Neto</th>
                        <th>Operaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM empleados";
                    $result = $conn->query($query);

                    if($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $id_empleado = $row['id_empleado'];
                            $nombre = $row['nombre_empleado'];
                            $salario_base = $row['salario'];
                            $tipo_contrato = $row['tipo_contrato'];
                            $fecha_ingreso = $row['fecha_ingreso'];
                            // Calculo del salario neto (aqui puedes agregar tu logica de calculo)
                            $salario_neto = $salario_base; // Por simplicidad, aqui no se aplica ningun descuento

                            echo "<tr>
                                    <td>$id_empleado</td>
                                    <td>$nombre</td>
                                    <td>$" . number_format($salario_base, 2) . "</td>
                                    <td>$tipo_contrato</td>
                                    <td>$fecha_ingreso</td>
                                    <td>$" . number_format($salario_neto, 2) . "</td>
                                    <td><a href='recibo.php?id=$id_empleado'>Generar Recibo</a></td>
                                </tr>";
                        }
                        echo "</table>";

                    } else {
                        echo "<p>No hay empleados registrados.</p>";
                    }

                    $conn->close();

                    ?>
                </tbody>
            </table>
        </form>
    </div>

</body>
</html>



