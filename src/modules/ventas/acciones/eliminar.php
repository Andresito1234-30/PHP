<?php
session_start();
require '../../../includes/config.php';
require '../../../includes/models/Venta.php';

if (isset($_GET['id'])) {
    $db = (new Database())->getConnection();
    $venta = new Venta($db);
    $venta->id_venta = (int)$_GET['id'];

    if ($venta->eliminar()) {
        $_SESSION['success'] = "Venta eliminada correctamente.";
    } else {
        $_SESSION['error'] = "No se pudo eliminar la venta.";
    }
    header("Location: ../index.php"); exit;
}
$_SESSION['error'] = "ID no especificado.";
header("Location: ../index.php"); exit;
