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
        <form method="POST" action="">
            <?php
                // Verificar si se ha enviado el formulario para eliminar
                if (isset($_POST['eliminarEmpleado'])) {
                    $id_empleado = $_POST['id_empleado'];
                    
                    // Primero, eliminar los registros relacionados en historial_pagos
                    $delete_historial = "DELETE FROM historial_pagos WHERE id_empleado = ?";
                    $delete_stmt = $conn->prepare($delete_historial);
                    $delete_stmt->bind_param("s", $id_empleado);
                    $delete_stmt->execute();
                    $delete_stmt->close();
                    // Ahora, eliminar el empleado
                    $query = "DELETE FROM empleados WHERE id_empleado = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("s", $id_empleado);
                    if ($stmt->execute()) {
                        echo "<script>alert('Empleado eliminado exitosamente');</script>";
                    } else {
                        echo "<script>alert('Error al eliminar el empleado');</script>";
                    }
                    $stmt->close();
                }
                // Obtener el empleado a eliminar
                $statement = $conn->prepare("SELECT * FROM empleados WHERE id_empleado = ?");
                $statement->bind_param("s", $_GET['id_empleado']);
                $statement->execute();
                $result = $statement->get_result();
                if($result -> num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo "<h2 style='text-align:center; padding: 30px 20px ;'>Informacion del empleado</h2>";
                    echo "<hr>";
                    echo "<p style='text-align:center; font-size:18px; font-weight:700;'>Id del Empleado: {$row['id_empleado']}</p>";
                    echo "<p style='text-align:center; font-size:18px;'>Nombre del Empleado: {$row['nombre_empleado']}</p>";
                    echo "<p style='text-align:center; font-size:18px;'>Salario: {$row['salario']}</p>";
                    echo "<p style='text-align:center; font-size:18px;'>Tipo de Contrato: {$row['tipo_contrato']}</p>";
                    echo "<p style='text-align:center; font-size:18px;'>Fecha de Ingreso: {$row['fecha_ingreso']}</p>";
                    // Campo oculto para el código del empleaddo
                    echo "<input type='hidden' name='id_empleado' value='{$row['id_empleado']}'>";
                } else {
                    echo "<p>Empleado no encontrado.</p>";
                }
            ?>
            <!-- Botones --> 
            <div class="botones">
                <button type="submit" name="eliminarEmpleado">Eliminar Empleado</button>
            </div>
        </form>
    </div>
</body>
</html>