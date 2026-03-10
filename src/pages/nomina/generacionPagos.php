<?php
// src/pages/nomina/generacionPagos.php
session_start();
include("../../config/conexion.php");
// Verifica si el usuario ha iniciado sesión
// Datos del empleado (esto normalmente vendría de una base de datos)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generacion de Pagos | Conta Tech</title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
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
                <p>Generacion de Pagos</p>
            </div>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <button class="logout"><a href="../../auth/logout.php">Log Out</a></button></p>
            <?php else: ?>
                <button class="logout"><a href="../../auth/login.php">Log In</a></button>
            <?php endif; ?>
            </div>
        <br><br>
        <h2>Lista de empleados</h2>
        <form method="post" action="procesar_pago.php">
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
                    // Consulta para obtener la lista de empleados
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
                            echo "<td><a href='recibo.php?id={$row['id_empleado']}'>Generar Recibo</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No hay empleados registrados.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </form>
    </div>
</body>
</html>