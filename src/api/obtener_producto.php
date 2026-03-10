<?php
include("../config/conexion.php");

$codigo = $_GET['codigo'] ?? '';

$stmt = $conn->prepare("SELECT precio, cantidad FROM productos WHERE cod_producto = ?");
$stmt->bind_param("s", $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
    
} else {
    echo json_encode(['precio' => 0, 'cantidad' => 0]);
}
?>
