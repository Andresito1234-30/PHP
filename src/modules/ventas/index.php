<?php
$page_title = "Gestión de Ventas";

include '../../admin/header.php';
include '../../admin/navbar.php';
include '../../admin/sidebar.php';

include '../../includes/config.php';
include '../../includes/models/Venta.php';

$database = new Database();
$db = $database->getConnection();
$venta = new Venta($db);
?>
<style>
  .btn-actions .btn { margin-right:.5rem; }
  .btn-actions .btn:last-child { margin-right:0; }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Gestión de Ventas</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="../../admin/index.php">Inicio</a></li>
            <li class="breadcrumb-item active">Ventas</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row"><div class="col-12">
        <?php if (isset($_SESSION['success'])): ?>
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <i class="icon fas fa-check"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
          </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <i class="icon fas fa-ban"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
          </div>
        <?php endif; ?>

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Listado de Ventas</h3>
            <div class="card-tools">
              <a href="pdf.php" class="btn btn-danger btn-pdf" target="_blank">
                <i class="fas fa-file-pdf me-1"></i> Generar PDF
                </a>
              <a href="crear.php" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Nueva Venta</a>
            </div>
          </div>

          <div class="card-body">
            <table id="tablaVentas" class="table table-bordered table-striped table-hover datatable">
              <thead>
                <tr>
                  <th>ID Venta</th>
                  <th>Vendedor</th>
                  <th>Fecha</th>
                  <th>Monto</th>
                  <th>Método</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
              <?php
                $stmt = $venta->leer();
                $num = $stmt->rowCount();
                if ($num > 0) {
                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $idv  = (int)$row['id_venta'];
                    $vend = htmlspecialchars($row['vendedor_nombre'] ?? '', ENT_QUOTES, 'UTF-8');
                    $fecha = $row['fecha'] ? date('d/m/Y H:i', strtotime($row['fecha'])) : '—';
                    $monto = number_format((float)$row['monto'], 2);
                    $met   = htmlspecialchars($row['metodo_pago'] ?? '', ENT_QUOTES, 'UTF-8');
              ?>
                <tr>
                  <td><?php echo $idv; ?></td>
                  <td><?php echo $vend; ?></td>
                  <td><span class="badge badge-info"><?php echo $fecha; ?></span></td>
                  <td class="text-right">S/ <?php echo $monto; ?></td>
                  <td><?php echo $met; ?></td>
                  <td>
                    <div class="btn-group btn-actions">
                      <a href="detalle.php?id=<?php echo $idv; ?>" class="btn btn-info btn-sm" title="Ver"><i class="fas fa-eye"></i></a>
                      <a href="editar.php?id=<?php echo $idv; ?>" class="btn btn-warning btn-sm" title="Editar"><i class="fas fa-edit"></i></a>
                      <a href="acciones/eliminar.php?id=<?php echo $idv; ?>" class="btn btn-danger btn-sm"
                         onclick="return confirm('¿Eliminar esta venta?');" title="Eliminar">
                        <i class="fas fa-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php
                  }
                } else {
              ?>
                <tr>
                  <td colspan="6" class="text-center">
                    <div class="py-4">
                      <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                      <h5 class="text-muted">No hay ventas registradas</h5>
                      <a href="crear.php" class="btn btn-primary"><i class="fas fa-plus"></i> Registrar primera venta</a>
                    </div>
                  </td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
          </div>

          <div class="card-footer">
            <div class="row">
              <div class="col-md-6"><strong>Total de ventas:</strong> <?php echo $num; ?></div>
              <div class="col-md-6 text-right">
                <small class="text-muted">Última actualización: <?php echo date('d/m/Y H:i:s'); ?></small>
              </div>
            </div>
          </div>
        </div>

      </div></div>
    </div>
  </section>
</div>

<?php include '../../admin/footer.php'; ?>

<script>
$(function(){
  $('#tablaVentas').DataTable({
    responsive: true,
    autoWidth: false,
    language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
    order: [[2,'desc']],
    columnDefs: [{ orderable:false, targets:[5] }]
  });
  setTimeout(()=>$('.alert').fadeOut('slow'), 5000);
});
</script>
