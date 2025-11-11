<?php

session_start();

include '../../../includes/config.php';

include '../../../includes/models/Vendedor.php';

if($_POST){

    $database = new Database();

    $db = $database->getConnection();

    

    $vendedor = new Vendedor($db);

    

    // No es necesario verificar si el ID existe, ya que es autoincremental y no se proporciona

    

    // Setear valores (sin id, ya que es autoincremental)

    $vendedor->vendedor = $_POST['vendedor'];

    $vendedor->direccion = $_POST['direccion'];

    $vendedor->fechaventa = $_POST['fechaventa'];

    

    // Crear vendedor

    if($vendedor->crear()){

        $_SESSION['success'] = "Vendedor creado exitosamente.";

    } else{

        $_SESSION['error'] = "No se pudo crear el vendedor.";

    }

    

    header("Location: ../index.php");

    exit();

} else {

    $_SESSION['error'] = "Método no permitido.";

    header("Location: ../index.php");

    exit();

}

?>