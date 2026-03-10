<?php
header("Content-Type: application/json");
// Include database connection
include("../config/conexion.php");
$sql = "SELECT categoria, SUM(monto) AS total FROM ingresos GROUP BY categoria";
$resultado = $conn->query($sql);
$data = [];
while($fila = $resultado->fetch_assoc()) {
    $data[] = $fila;
}
echo json_encode($data);
$conn->close();
?>