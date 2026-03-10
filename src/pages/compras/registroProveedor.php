<?php
include("../../config/conexion.php");
session_start();

if(isset($_POST['registro_proveedor'])) {
    if(
        !empty($_POST['id_proveedor']) && 
        !empty($_POST['nombre_proveedor']) && 
        !empty($_POST['contacto']) && 
        !empty($_POST['condiciones_pago'])
    ){
        $id_proveedor = trim($_POST['id_proveedor']);
        $nombre_proveedor = trim($_POST['nombre_proveedor']);
        $contacto = trim($_POST['contacto']);
        $condiciones_pago = trim($_POST['condiciones_pago']);

        $statement = $conn->prepare("INSERT INTO proveedores (id_proveedor, nombre_proveedor, contacto, condiciones_pago) VALUES (?, ?, ?, ?)");
        if($statement === false) {
            die("Error en la consulta: " . $conn->error);
        }
        $statement->bind_param("sssss", $id_proveedor, $nombre_proveedor, $contacto, $condiciones_pago);

        $verificar = $conn->prepare("SELECT id_proveedor FROM proveedores WHERE id_proveedor = ?");
        $verificar->bind_param("s", $id_proveedor);
        $verificar->execute();
        $result = $verificar->get_result();
        
        if($result->num_rows > 0) {
            $proveedor = $result->fetch_assoc();
            if($id_proveedor === $proveedor['id_proveedor']) {
                echo "El ID del proveedor ya existe. Por favor, elija otro.";
                exit();
                $verificar->close();
            } else {
                if($statement->execute()) {
                    echo "Proveedor registrado con éxito.";
                } else { 
                    echo "Error al registrar el proveedor: " . $conn->error;
                }
                $statement->close();
            }
        }
    } else {
        echo "Por favor complete todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Proveedor | Conta Tech</title>
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
      <li><a href="../compras/ComprasProveedores.php" class="active">🛒 Compras y proveedores</a></li>
      <li><a href="../gastos_ingresos/gasto_ingreso.php">💸 Ingresos y Egresos</a></li>
      <li><a href="../reportes/ingresos.php">📊 Reportes contables</a></li>
      <li><a href="../inventario/listaProductos.php">📦 Inventario</a></li>
      <li><a href="../nomina/nomina.php">📋 Nómina</a></li>
      <li><a href="support.php">🧰 Soporte</a></li>
    </ul>
  </div>
    <div class="main-content">
        <div class="header">
            <div>
                <h1>Proveedor</h1>
                <p>Registrar Proveedor</p>
            </div>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <!-- <p class="user"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p> -->
                <button class="logout"><a href="src/auth/logout.php">Log Out</a></button>
            <?php else: ?>
                <button class="logout"><a href="src/auth/login.php">Log In</a></button>
            <?php endif; ?>
        </div>
        <!-- formulario para registrar un proveedor -->
         <div class="form">

             <form method="post">
                 <label>ID del Proveedor</label>
                 <input type="number" name="id_proveedor" placeholder="ID del proveedor" required>
     
                 <label>Nombre del Proveedor</label>
                 <input type="text" name="nombre_proveedor" placeholder="Nombre del proveedor" required>
                 
                 <label>Contacto</label>
                 <input type="number" name="contacto" placeholder="contacto del proveedor" required>
                 
                 <label>Condiciones de Pago</label>
                 <select name="condiciones_pago" id="condiciones_pago" required>
                     <option value="" disabled selected>Seleccione una condicion de pago</option>
                     <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                     <option value="Cheque">Cheque</option>
                     <option value="Tarjetas de Crédito y Débito">Tarjetas de Crédito y Débito</option>
                     <option value="Pagos en Efectivo">Pagos en Efectivo</option>
                     <option value="PayPal y otros Sistemas de Pago en Línea">PayPal y otros Sistemas de Pago en Línea</option>
                     <option value="Débitos Automáticos">Débitos Automáticos</option>
                     <option value="Criptomonedas">Criptomonedas</option>
                     <option value="Financiación">Financiación</option>
                 </select>

                 <button type="submit" name="registro_proveedor" class="btn-email">Registrar Proveedor</button>
             </form>
         </div>
  </div>
</body>
</html>
