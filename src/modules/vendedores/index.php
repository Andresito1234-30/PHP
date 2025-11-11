<?php

$page_title = "Gestión de Vendedores";

include '../../admin/header.php';
include '../../admin/navbar.php';
include '../../admin/sidebar.php';

include '../../includes/config.php';
include '../../includes/models/Vendedor.php';
 

$database = new Database();
$db = $database->getConnection();
$vendedor = new Vendedor($db);
?>

<!-- Estilos locales para espaciar botones de la columna Acciones -->
<style>
  /* Espaciado entre botones en la columna Acciones (seguro para Bootstrap 4/5) */
  .btn-actions .btn { margin-right: .5rem; }
  .btn-actions .btn:last-child { margin-right: 0; }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Gestión de Vendedores</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../../admin/index.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Vendedores</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <!-- Alertas -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="icon fas fa-check"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="icon fas fa-ban"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Lista de Vendedores Registrados</h3>
                            <div class="card-tools">
                                <a href="pdf.php" class="btn btn-danger btn-pdf" target="_blank">
                                <i class="fas fa-file-pdf me-1"></i>Generar PDF
                                </a>
                                
                                <a href="crear.php" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Nuevo Vendedor
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <table id="tablaVendedores" class="table table-bordered table-striped table-hover datatable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Vendedor</th>
                                        <th>Dirección</th>
                                        <th>Fecha de Venta</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $vendedor->leer();
                                    $num = $stmt->rowCount();

                                    if($num > 0) {
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            extract($row);
                                            ?>
                                            <tr>
                                                <td><?php echo $id; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($vendedor); ?></strong>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if(strlen($direccion) > 30) {
                                                        echo '<span title="' . htmlspecialchars($direccion) . '">'
                                                             . substr(htmlspecialchars($direccion), 0, 30) . '...</span>';
                                                    } else {
                                                        echo htmlspecialchars($direccion);
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        <?php echo date('d/m/Y', strtotime($fechaventa)); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-actions" role="group">
                                                        <a href="detalle.php?id=<?php echo $id; ?>" 
                                                           class="btn btn-info btn-sm" 
                                                           title="Ver Detalles">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="editar.php?id=<?php echo $id; ?>" 
                                                           class="btn btn-warning btn-sm"
                                                           title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-danger btn-sm btn-eliminar"
                                                                data-id="<?php echo $id; ?>"
                                                                data-nombre="<?php echo htmlspecialchars($vendedor); ?>"
                                                                title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                <div class="py-4">
                                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">No hay vendedores registrados</h5>
                                                    <p class="text-muted">Comienza agregando tu primer vendedor.</p>
                                                    <a href="crear.php" class="btn btn-primary">
                                                        <i class="fas fa-plus"></i> Agregar Primer Vendedor
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody> 
                            </table>
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Total de vendedores:</strong> <?php echo $num; ?>
                                </div>
                                <div class="col-md-6 text-right">
                                    <small class="text-muted">Última actualización: <?php echo date('d/m/Y H:i:s'); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->

                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Modal de Confirmación para Eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar al vendedor: <strong id="nombreVendedor"></strong>?</p>
                <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <a href="#" id="btnConfirmarEliminar" class="btn btn-danger">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<?php include '../../admin/footer.php'; ?>

<script>
$(document).ready(function() {
    // Configuración de DataTables
    $('#tablaVendedores').DataTable({
        "responsive": true,
        "autoWidth": false,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "order": [[3, 'desc']], // Ordenar por fecha de venta descendente
        "columnDefs": [
            { "orderable": false, "targets": [4] } // Columna de acciones no ordenable
        ]
    });

    // Modal de eliminación
    $('.btn-eliminar').on('click', function() {
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        $('#nombreVendedor').text(nombre);
        $('#btnConfirmarEliminar').attr('href', 'acciones/eliminar.php?id=' + id);
        $('#modalEliminar').modal('show');
    });

    // Auto-ocultar alertas después de 5 segundos
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
