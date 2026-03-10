<?php
include("../../config/conexion.php");
session_start();
?>
<DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago | Conta Tech</title>
    <link rel="stylesheet" href="../../../assets/css/recibo.css">
    <script src="../../../assets/js/script.js"></script>
    <link rel="icon" href="../../../assets/img/favicon_io/favicon.ico" type="image/x-icon">

    <?php
    $id_empleado = $_GET['id'];

    $query = "SELECT * FROM empleados WHERE id_empleado = ?";

    $statement = $conn->prepare($query);
    $statement->bind_param("i", $id_empleado);
    $statement->execute();
    $result = $statement->get_result();
    $empleado = $result->fetch_assoc();
    if($empleado) {
        echo "<h1>Recibo de Pago</h1>";
        echo "<h3>Su recibo de pago se realizo correctamente</h3>";
        echo "<img src='../../../assets/img/recibo/cheque.png'>";
        echo "<p><span><strong class='monto'>Monto: </strong> $" . number_format($empleado['salario'], 2) . "</span></p>";
        echo "<h4>Datos del Empleado:</h4>";
        echo "<hr>";
        echo "<p><strong>ID del Empleado: </strong>" . $empleado['id_empleado'] . "</p>";
        echo "<p><strong>Nombre del Empleado: </strong>" . $empleado['nombre_empleado'] . "</p>";
        echo "<p><strong>Tipo de Contrato: </strong>" . $empleado['tipo_contrato'] . "</p>";
        echo "<p><strong>Fecha de Ingreso: </strong>" . $empleado['fecha_ingreso'] . "</p>";
        echo "<p><strong>Salario Neto: </strong>" . number_format($empleado['salario'], 2) . "</p>";
        echo "<hr>";
        // formulario para procesar el pago
        echo "<form method='post' action='procesarPago.php'>";
        echo "<input type='hidden' name='id_empleado' value='" . $empleado['id_empleado'] . "'>";
        echo "<input type='hidden' name='salario' value='" . $empleado['salario'] . "'>";
        echo "<button type='submit'>Registrar Pago</button>";
        // echo "<button type='submit' onclick='window.print()'>Imprimir Recibo</button>";
        echo "</form>";
    } else {
        echo "<h1>Recibo de pago</h1>";
        echo "<img src='../../../assets/img/recibo/borrar.png'>";
        echo "<p><strong>No se encontró el empleado con ID:</strong> $id_empleado</p>";
    }

    $statement->close();
    $conn->close();

    ?>
</html>




