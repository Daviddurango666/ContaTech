<?php
session_start();
include("../../config/conexion.php");
if (isset($_POST['registro_producto'])) {
    if (
        !empty($_POST['nombre_producto']) && 
        !empty($_POST['categoria_producto']) && 
        !empty($_POST['precio']) && 
        !empty($_POST['cantidad'])
    ) {
        $nombre_producto = trim($_POST['nombre_producto']);
        $categoria_producto = trim($_POST['categoria_producto']);
        $precio_producto = floatval(trim($_POST['precio']));
        $cantidad_producto = intval(trim($_POST['cantidad'])); // Asegúrate de convertir a entero
        // Verificar si el producto ya existe
        $stmnt = $conn->prepare("SELECT nombre_producto FROM productos WHERE nombre_producto = ?");
        if ($stmnt === false) {
            die("Error en la consulta: " . $conn->error);
        }
        $stmnt->bind_param("s", $nombre_producto);
        $stmnt->execute();
        $result = $stmnt->get_result();
        if ($result->num_rows > 0) {
            echo "El producto ya existe. <a href='listaProductos.php'>Ver Productos</a>";
            $stmnt->close(); // Cerrar el statement
            exit();
        }
        // Si el producto no existe, proceder a la inserción
        $statement = $conn->prepare("INSERT INTO productos (nombre_producto, categoria_producto, precio, cantidad) VALUES (?, ?, ?, ?)");
        if ($statement === false) {
            die("Error en la consulta: " . $conn->error);
        }
        $statement->bind_param("ssdi", $nombre_producto, $categoria_producto, $precio_producto, $cantidad_producto);
        if ($statement->execute()) {
            echo "Producto creado con éxito. <a href='listaProductos.php'>Ver Productos</a>";
        } else {
            echo "Error al crear el producto: " . $conn->error;
        }
        $statement->close();
        $stmnt->close(); // Cerrar el statement
    } else {
        echo "Por favor complete todos los campos.";
    }
} else {
    echo "No se ha enviado el formulario.";
}
?>