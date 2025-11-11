<?php

session_start();

include '../../../includes/config.php';

include '../../../includes/models/Vendedor.php';


if(isset($_GET['id'])){

    $database = new Database();

    $db = $database->getConnection();

    

    $vendedor = new Vendedor($db);

    $vendedor->id = $_GET['id'];

    

    // Eliminar vendedor

    if($vendedor->eliminar()){

        $_SESSION['success'] = "Vendedor eliminado exitosamente.";

    } else{

        $_SESSION['error'] = "No se pudo eliminar el vendedor.";

    }

    

    header("Location: ../index.php");

    exit();

} else {

    $_SESSION['error'] = "ID no especificado.";

    header("Location: ../index.php");

    exit();

}

?>

modules/vendedores/acciones/verificar_id.php

<?php

include '../../../includes/config.php';

include '../../../includes/models/Vendedor.php';


if(isset($_POST['id'])) {

    $database = new Database();

    $db = $database->getConnection();

    

    $vendedor = new Vendedor($db);

    $vendedor->id = $_POST['id'];

    

    header('Content-Type: application/json');

    echo json_encode(['existe' => $vendedor->idExiste()]);

    exit();

}

?>