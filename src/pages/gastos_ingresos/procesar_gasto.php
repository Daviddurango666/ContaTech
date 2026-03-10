<?php
include("../../config/conexion.php");

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descripcion = $_POST['descripcion'];
    $monto = $_POST['monto'];
    $fecha = $_POST['fecha'];
    $categoria = $_POST['categoria'];

    $query = "INSERT INTO gastos (descripcion, monto, fecha, categoria) VALUES ( ?, ?, ?, ?)";
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