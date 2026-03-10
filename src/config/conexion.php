<?php
$localhost = "localhost";
$user = "root";
$password = "";
$database = "contatech";

$conn = new mysqli($localhost, $user, $password, $database);

if($conn->connect_error) {
    die("error al conectar la base de datos" . $conn->connect_error);
}

?>