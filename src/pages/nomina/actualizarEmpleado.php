<?php
session_start();
include("../../config/conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Empleado | Conta Tech</title>
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
      <li><a href="../inventario/listaProductos.php">📦 Inventario</a></li>
      <li><a href="../nomina/nomina.php" class="active">📋 Nómina</a></li>
      <li><a href="support.php">🧰 Soporte</a></li>
    </ul>
  </div>
  <div class="main-content">
    <div class="header">
      <div>
        <h1>Nómina</h1>
        <p>Actualizar Empleado</p>
      </div>
      <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
        <button class="logout"><a href="../../auth/logout.php">Log Out</a></button>
      <?php else: ?>
        <button class="logout"><a href="../../auth/login.php">Log In</a></button>
      <?php endif; ?>
    </div>
    <!-- Formulario para actualizar un empleado -->
    <div class="form">
      <form method="post" action="update_Empleado.php">
        <?php
        // Obtener el empleado a actualizar
        if (isset($_GET['id_empleado'])) {
            $id_empleado = $_GET['id_empleado'];
            $statement = $conn->prepare("SELECT * FROM empleados WHERE id_empleado = ?");
            $statement->bind_param("s", $id_empleado);
            $statement->execute();
            $result = $statement->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                ?>
                <input type="hidden" name="id_empleado" value="<?php echo htmlspecialchars($row['id_empleado']); ?>">
                <label for="nombre_empleado">Nombre del Empleado:</label>
                <input type="text" name="nombre_empleado" value="<?php echo htmlspecialchars($row['nombre_empleado']); ?>" required>
                <label for="salario">Salario:</label>
                <input type="text" name="salario" value="<?php echo htmlspecialchars($row['salario']); ?>" required>
                <label for="tipo_contrato">Tipo de Contrato:</label>
                <select name="tipo_contrato" id="categoria">
                    <option value="<?php echo htmlspecialchars($row['fecha_ingreso']); ?>" disabled selected></option>
                    <option value="Contrato indefinido">Contrato indefinido</option>
                    <option value="Contrato temporal">Contrato temporal</option>
                    <option value="Contrato a tiempo completo">Contrato a tiempo completo</option>
                    <option value="Contrato a tiempo parcial">Contrato a tiempo parcial</option>
                    <option value="Contrato por horas">Contrato por horas</option>
                    <option value="Contrato de obra o servicio">Contrato de obra o servicio</option>
                    <option value="Contrato eventual">Contrato eventual</option>
                    <option value="Contrato presencial">Contrato presencial</option>
                    <option value="Contrato de teletrabajo (o trabajo remoto)">Contrato de teletrabajo (o trabajo remoto)</option>
                    <option value="Contrato de prácticas">Contrato de prácticas</option>
                    <option value="Contrato de aprendizaje">Contrato de aprendizaje</option>
                </select>
                <label for="fecha_ingreso">Fecha de Ingreso:</label>
                <input type="date" name="fecha_ingreso" >
                <button type="submit" name="actualizar_empleado">Actualizar Empleado</button>
                <?php
            } else {
                echo "<p>Empleado no encontrado.</p>";
            }
        }
        ?>
      </form>
    </div>
  </div>
</body>
</html>