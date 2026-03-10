<?php
include("../../config/conexion.php");

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = $_POST['descripcion'];
    $monto = $_POST['monto'];
    $fecha = $_POST['fecha'];
    $categoria = $_POST['categoria'];

    // Prepare and bind
    $query = "INSERT INTO ingresos (descripcion, monto, fecha, categoria) VALUES (?, ?, ?, ?)";
    $statement = $conn->prepare($query);
    $statement->bind_param("sdss", $descripcion, $monto, $fecha, $categoria);

    // Execute the statement
    if($statement->execute()) {
        echo "Ingreso registrado exitosamente.";
    } else {
        echo "Error al registrar el ingreso: " . $statement->error;
    }
    // Close the statement and connection
    $statement->close();
    $conn->close();
} else {
    echo "No se recibieron datos.";
}

?>