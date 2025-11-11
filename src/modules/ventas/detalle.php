<?php
$page_title = "Detalle de Venta";

include '../../admin/header.php';
include '../../admin/navbar.php';
include '../../admin/sidebar.php';

include '../../includes/config.php';
include '../../includes/models/Venta.php';

$db = (new Database())->getConnection();
$venta = new Venta($db);

if (!isset($_GET['id'])) {
  $_SESSION['error'] = "ID de venta no especificado.";
  header("Location: index.php"); exit;
}
$venta->id_venta = (int)$_GET['id'];
if (!$venta->leerUno()) {
  $_SESSION['error'] = "Venta no encontrada.";
  header("Location: index.php"); exit;
}
?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Detalle de Venta</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="../../admin/index.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="index.php">Ventas</a></li>
            <li class="breadcrumb-item active">Detalle</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row"><div class="col-md-8 mx-auto">
        <div class="card card-info">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-file-invoice-dollar"></i> Información de la Venta</h3>
            <div class="card-tools">
              <a href="editar.php?id=<?php echo $venta->id_venta; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a>
            </div>
          </div>

          <div class="card-body">
            <table class="table table-bordered">
              <tr>
                <th width="30%">ID Venta:</th>
                <td><span class="badge badge-primary"><?php echo $venta->id_venta; ?></span></td>
              </tr>
              <tr>
                <th>Vendedor:</th>
                <td><strong><?php echo htmlspecialchars($venta->vendedor_nombre); ?></strong> (ID: <?php echo (int)$venta->id_vendedor; ?>)</td>
              </tr>
              <tr>
                <th>Fecha:</th>
                <td><span class="badge badge-info"><?php echo date('d/m/Y H:i', strtotime($venta->fecha)); ?></span></td>
              </tr>
              <tr>
                <th>Monto:</th>
                <td>S/ <?php echo number_format((float)$venta->monto, 2); ?></td>
              </tr>
              <tr>
                <th>Método de Pago:</th>
                <td><?php echo htmlspecialchars($venta->metodo_pago); ?></td>
              </tr>
              <tr>
                <th>Nota:</th>
                <td><?php echo htmlspecialchars($venta->nota); ?></td>
              </tr>
            </table>
          </div>

          <div class="card-footer">
            <a href="index.php" class="btn btn-default"><i class="fas fa-arrow-left"></i> Volver</a>
          </div>
        </div>
      </div></div>
    </div>
  </section>
</div>

<?php include '../../admin/footer.php'; ?>
