<?php
include("../../config/conexion.php");

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fecha = date('Y-m-d');
    $total = $_POST['monto'];

    $query = "INSERT INTO factura (fecha, total) VALUES ( ?, ?)";
    $statement = $conn->prepare($query);
    $statement->bind_param("sdss", $descripcion, $monto, $fecha, $categoria);
    if($statement->execute()) {
        echo "Gasto registrado exitosamente.";
    } else {
        echo "Error al registrar el gasto: " . $statement->error;
    }
    $statement->close();
    $conn->close();
} else {
    echo "No se recibieron datos.";
}

?>