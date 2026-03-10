<?php
include("../../config/conexion.php");
if(isset($_POST['registro_empleado'])) {
    if(
        !empty($_POST['id_empleado']) && 
        !empty($_POST['nombre_empleado']) && 
        !empty($_POST['salario']) && 
        !empty($_POST['tipo_contrato']) && 
        !empty($_POST['fecha_ingreso'])
    ){
        $id_empleado = trim($_POST['id_empleado']);
        $nombre_empleado = trim($_POST['nombre_empleado']);
        $salario = trim($_POST['salario']);
        $tipo_contrato = trim($_POST['tipo_contrato']);
        $fecha_ingreso = trim($_POST['fecha_ingreso']);
        
        // Verificar si el ID ya existe
        $verificar = $conn->prepare("SELECT id_empleado FROM empleados WHERE id_empleado = ?");
        $verificar->bind_param("s", $id_empleado);
        $verificar->execute();
        $result = $verificar->get_result();
        
        if($result->num_rows > 0) {
            echo "El ID del empleado ya existe. Por favor, elija otro.";
        } else {
            $statement = $conn->prepare("INSERT INTO empleados (id_empleado, nombre_empleado, salario, tipo_contrato, fecha_ingreso) VALUES (?, ?, ?, ?, ?)");
            if($statement === false) {
                die("Error en la consulta: " . $conn->error);
            }
            $statement->bind_param("sssss", $id_empleado, $nombre_empleado, $salario, $tipo_contrato, $fecha_ingreso);
            if($statement->execute()) {
                echo "Empleado registrado con éxito.";
            } else { 
                echo "Error al registrar el empleado: " . $conn->error;
            }
            $statement->close();
        }
        $verificar->close();
        $conn->close();
    } else {
        echo "Por favor complete todos los campos.";
    }
}
?>