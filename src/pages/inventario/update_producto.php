<?php
session_start();
include("../../config/conexion.php");
// Verificar si se ha enviado el formulario para actualizar
if (isset($_POST['actualizar_producto'])) {
    // Validar que los campos no estén vacíos
    if (
        !empty($_POST['cod_producto']) && 
        !empty($_POST['nombre_producto']) && 
        !empty($_POST['categoria_producto']) && 
        !empty($_POST['precio']) && 
        !empty($_POST['cantidad'])
    ) {
        // Obtener los valores del formulario
        $cod_producto = trim($_POST['cod_producto']);
        $nombre_producto = trim($_POST['nombre_producto']);
        $categoria_producto = trim($_POST['categoria_producto']);
        $precio = trim($_POST['precio']);
        $cantidad = trim($_POST['cantidad']);
        // Preparar la consulta para actualizar el producto
        $query = "UPDATE productos SET nombre_producto = ?, categoria_producto = ?, precio = ?, cantidad = ? WHERE cod_producto = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            die("Error en la consulta: " . $conn->error);
        }
        // Vincular los parámetros
        $stmt->bind_param("sssss", $nombre_producto, $categoria_producto, $precio, $cantidad, $cod_producto);
        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "<script>alert('Producto actualizado exitosamente.');</script>";
        } else {
            echo "<script>alert('Error al actualizar el Producto: " . $conn->error . "');</script>";
        }
        // Cerrar la declaración
        $stmt->close();
    } else {
        echo "<script>alert('Por favor complete todos los campos.');</script>";
    }
}
?>