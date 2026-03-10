<?php
include("../config/conexion.php");
$codigo = $_POST['codigo'] ?? '';
$cantidad = intval($_POST['cantidad'] ?? 0);

if($codigo && $cantidad > 0) {
    $statement = $conn->prepare("UPDATE productos SET cantidad = cantidad - ? WHERE cod_producto = ? AND cantidad >= ?");
    $statement->bind_param("isi", $cantidad, $codigo, $cantidad);
    $statement->execute();


    if($statement->affected_rows > 0) {
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'stock insuficiente o error']);
    }
} else {
    echo json_encode(['status' => 'error', 'msg' => 'datos invalidos']);
}

?>