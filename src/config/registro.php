<?php
session_start();
include("conexion.php");

if(isset($_POST['registro'])) {
    
    if(
        !empty($_POST['id_user']) && 
        !empty($_POST['name']) && 
        !empty($_POST['user']) && 
        !empty($_POST['email']) && 
        !empty($_POST['password'])
    ){
        
        $id_user = trim($_POST['id_user']);
        $name = trim($_POST['name']);
        $user = trim($_POST['user']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $statement = $conn->prepare("INSERT INTO usuario (id, name, usuario, email, password) VALUES (?, ?, ?, ?, ?)");
        if($statement === false) {
            die("error en la consulta: " . $conn->error);
        }
        $statement->bind_param("sssss
        ", $id_user, $name, $user, $email, $hashed_password);
        if($statement->execute()) {
            echo "usuario registrado con exito. <a href='../auth/login.php'>iniciar sesion</a>";
        } else { 
            echo "error al registrar el usuario: " . $conn->error;
        }
        $statement->close();
    } else {
        echo "por favor complete todos los campos.";
    }   
    } else {
        echo "no se ha enviado el formulario.";
    }
?>