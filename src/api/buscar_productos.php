<?php
include("../config/conexion.php");

$query = $_GET['query'] ?? '';

$stmt = $conn->prepare("SELECT cod_producto, nombre_producto FROM productos WHERE nombre_producto LIKE CONCAT('%', ?, '%') LIMIT 10");
$stmt->bind_param("s", $query);
$stmt->execute();
$result = $stmt->get_result();

$productos = [];
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}

header('Content-Type: application/json');
echo json_encode($productos);