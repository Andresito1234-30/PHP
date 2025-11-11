<?php
session_start();
require '../../../includes/config.php';
require '../../../includes/models/Venta.php';

if ($_POST) {
    $db = (new Database())->getConnection();
    $venta = new Venta($db);

    $venta->id_venta    = $_POST['id_venta'] ?? null;
    $venta->id_vendedor = $_POST['id_vendedor'] ?? null;
    $venta->fecha       = $_POST['fecha'] ?? null;
    $venta->monto       = $_POST['monto'] ?? 0;
    $venta->metodo_pago = $_POST['metodo_pago'] ?? '';
    $venta->nota        = $_POST['nota'] ?? '';

    if ($venta->actualizar()) {
        $_SESSION['success'] = "Venta actualizada correctamente.";
    } else {
        $_SESSION['error'] = "No se pudo actualizar la venta.";
    }
    header("Location: ../index.php"); exit;
}
$_SESSION['error'] = "Método no permitido.";
header("Location: ../index.php"); exit;
