<?php
session_start();
include("../../config/conexion.php");

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_empleado = $_POST['id_empleado'];
    $salario_bruto = floatval($_POST['salario']);
    $deducciones = 0;
    $salario_neto = $salario_bruto - $deducciones;
    $fecha_pago = date('Y-m-d');

    $query = "INSERT INTO historial_pagos(id_empleado, fecha_pago, salario_bruto, deducciones, salario_neto) VALUES (?, ?, ?, ?, ?)";
    $statement = $conn->prepare($query);
    if($statement === false) {
        die("error en la consulta: " . $conn->error);
    } 

    $statement->bind_param("sssss", $id_empleado, $fecha_pago, $salario_bruto, $deducciones, $salario_neto);
    if($statement->execute()) {
        $stmnt = $conn->prepare("INSERT INTO gastos (descripcion, monto, fecha, categoria) VALUES (?, ?, ?, ?)");
        if($stmnt === false) {
            die("error en la consulta: " . $conn->error);
        }
        $descripcion = "Pago de nómina empleado ID: $id_empleado";
        $salario_neto = number_format($salario_neto, 2, '.', '');
        $categoria = "sueldos y salarios";
        $stmnt->bind_param("sdss", $descripcion, $salario_neto, $fecha_pago, $categoria);
        $stmnt->execute();
        $stmnt->close();
        echo "pago registrado exitosamente <a href='nomina.php'>Volver al inicio</a>";
    } else {
        echo "error al registrar el pago:" . $statement->error;
    }

    $statement->close();
    $conn->close();
} else {
    echo "metodo de solicitud no permitido";
}

?>