<?php
session_start();
include("../../config/conexion.php");
// Verificar si se ha enviado el formulario para actualizar
if (isset($_POST['actualizar_empleado'])) {
    // Validar que los campos no estén vacíos
    if (
        !empty($_POST['id_empleado']) && 
        !empty($_POST['nombre_empleado']) && 
        !empty($_POST['salario']) && 
        !empty($_POST['tipo_contrato']) && 
        !empty($_POST['fecha_ingreso'])
    ) {
        // Obtener los valores del formulario
        $id_empleado = trim($_POST['id_empleado']);
        $nombre_empleado = trim($_POST['nombre_empleado']);
        $salario = trim($_POST['salario']);
        $tipo_contrato = trim($_POST['tipo_contrato']);
        $fecha_ingreso = trim($_POST['fecha_ingreso']);
        // Preparar la consulta para actualizar el empleado
        $query = "UPDATE empleados SET nombre_empleado = ?, salario = ?, tipo_contrato = ?, fecha_ingreso = ? WHERE id_empleado = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            die("Error en la consulta: " . $conn->error);
        }
        // Vincular los parámetros
        $stmt->bind_param("sssss", $nombre_empleado, $salario, $tipo_contrato, $fecha_ingreso, $id_empleado);
        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "<script>alert('Empleado actualizado exitosamente.');</script>";
        } else {
            echo "<script>alert('Error al actualizar el empleado: " . $conn->error . "');</script>";
        }
        // Cerrar la declaración
        $stmt->close();
    } else {
        echo "<script>alert('Por favor complete todos los campos.');</script>";
    }
}
?>