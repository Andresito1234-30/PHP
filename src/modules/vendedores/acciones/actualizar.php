<?php

session_start();

include '../../../includes/config.php';

include '../../../includes/models/Vendedor.php';


if($_POST){

    $database = new Database();

    $db = $database->getConnection();

    

    $vendedor = new Vendedor($db);

    

    // Setear valores

    $vendedor->id = $_POST['id'];

    $vendedor->vendedor = $_POST['vendedor'];

    $vendedor->direccion = $_POST['direccion'];

    $vendedor->fechaventa = $_POST['fechaventa'];

    

    // Actualizar vendedor

    if($vendedor->actualizar()){

        $_SESSION['success'] = "Vendedor actualizado exitosamente.";

    } else{

        $_SESSION['error'] = "No se pudo actualizar el vendedor.";

    }

    

    header("Location: ../index.php");

    exit();

} else {

    $_SESSION['error'] = "Método no permitido.";

    header("Location: ../index.php");

    exit();

}

?>