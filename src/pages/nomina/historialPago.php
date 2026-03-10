<?php
include("../../config/conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Pagos | Conta Tech</title>
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
                <h1>Historial de Pagos</h1>
            </div>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <!-- <p class="user"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p> -->
                <button class="logout"><a href="../../auth/logout.php">Log Out</a></button>
            <?php else: ?>
                <button class="logout"><a href="../../auth/login.php">Log In</a></button>
            <?php endif; ?>
        </div>
        <div class="top-bar">
            <h2>Pagos de <?php 
                $statement = $conn->prepare("SELECT nombre_empleado FROM empleados WHERE id_empleado = ?");
                $statement->bind_param("i", $_GET['id_empleado']);
                $statement->execute();
                $result = $statement->get_result();
                if ($result->num_rows > 0) {
                    $result = $result->fetch_assoc();
                    echo $result['nombre_empleado'];
                } else {
                    die("Empleado no encontrado.");
                }
            ?></h2>
            <div class="search-box">
                <span class="icon">🔍</span>
                <input type="text" placeholder="Buscar.....">
            </div>
        </div>
        <form>
            <table>
                <thead>
                    <tr>
                        <th>ID Pago</th>
                        <th>Fecha de Pago</th>
                        <th>Salario Bruto</th>
                        <th>Deducciones</th>
                        <th>Salario Neto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(isset($_GET['id_empleado'])) {
                        $id_empleado = $_GET['id_empleado'];
                        
                        $query = "SELECT * FROM historial_pagos WHERE id_empleado = ?";
                        $statement = $conn->prepare($query);
                        $statement->bind_param("i", $id_empleado);
                        $statement->execute();
                        $result = $statement->get_result();
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$row['id_pago']}</td>";
                                echo "<td>{$row['fecha_pago']}</td>";
                                echo "<td>{$row['salario_bruto']}</td>";
                                echo "<td>{$row['deducciones']}</td>";
                                echo "<td>{$row['salario_neto']}</td>";
                            }
                        } else {
                            echo "<p>no hay historial de pagos</p>";
                        }
                    } else {
                        die("ID de empleado no proporcionado.");
                    }
                    ?>

                </tbody>
            </table>
        </form>
    </div>
</body>
</html>