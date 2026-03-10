<?php
session_start();
include("../config/conexion.php");

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $statement = $conn->prepare("SELECT email, password, usuario FROM usuario where email = ?");
    if($statement === false) {
        die("error en la consulta: " . $conn->error);
    }
    
    $statement->bind_param("s", $email);
    $statement->execute();
    $result = $statement->get_result();

    $errorMessage = "usuario o contraseña incorrecta. <a href='login.php'>volver al login</a>";

    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if(password_verify($password, $user['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_name'] = $user['usuario'];
            header("Location: ../../index.php"); 
            exit();
        } else {
            echo $errorMessage;
        }
    } else {
        echo $errorMessage;
    }
}


?>
