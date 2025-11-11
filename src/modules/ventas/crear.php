<?php
$page_title = "Registrar Venta";

include '../../admin/header.php';
include '../../admin/navbar.php';
include '../../admin/sidebar.php';

include '../../includes/config.php';
include '../../includes/models/Venta.php';

// Necesitamos la lista de vendedores para el select
require_once '../../includes/models/Vendedor.php';

$db = (new Database())->getConnection();
$vendedorModel = new Vendedor($db);
$listaVendedores = $vendedorModel->leer(); // ya ordenas luego si quieres
?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Nueva Venta</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="../../admin/index.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="index.php">Ventas</a></li>
            <li class="breadcrumb-item active">Nueva</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row"><div class="col-md-8 mx-auto">
        <div class="card card-primary">
          <div class="card-header"><h3 class="card-title">Datos de la Venta</h3></div>

          <form action="acciones/crear.php" method="post" id="formVenta">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="fecha">Fecha *</label>
                    <input type="datetime-local" class="form-control" id="fecha" name="fecha"
                           value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="id_vendedor">Vendedor *</label>
                    <select class="form-control" id="id_vendedor" name="id_vendedor" required>
                      <option value="">— Seleccione —</option>
                      <?php while($v = $listaVendedores->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo (int)$v['id']; ?>">
                          <?php echo '#'.(int)$v['id'].' - '.htmlspecialchars($v['vendedor'],ENT_QUOTES,'UTF-8'); ?>
                        </option>
                      <?php endwhile; ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="monto">Monto (S/.) *</label>
                    <input type="number" step="0.01" min="0" class="form-control" id="monto" name="monto" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="metodo_pago">Método de pago *</label>
                    <select class="form-control" id="metodo_pago" name="metodo_pago" required>
                      <option value="Efectivo">Efectivo</option>
                      <option value="Tarjeta">Tarjeta</option>
                      <option value="Transferencia">Transferencia</option>
                      <option value="Yape/Plin">Yape/Plin</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="nota">Nota (opcional)</label>
                <input type="text" class="form-control" id="nota" name="nota" maxlength="255"
                       placeholder="Detalle o comentario (opcional)">
              </div>
            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Venta</button>
              <a href="index.php" class="btn btn-default float-right"><i class="fas fa-arrow-left"></i> Cancelar</a>
            </div>
          </form>
        </div>
      </div></div>
    </div>
  </section>
</div>

<?php include '../../admin/footer.php'; ?>

<script>
$(function(){
  $('#formVenta').validate({
    rules:{
      id_vendedor:{ required:true, digits:true, min:1 },
      fecha:{ required:true },
      monto:{ required:true, number:true, min:0 },
      metodo_pago:{ required:true },
      nota:{ maxlength:255 }
    },
    messages:{
      id_vendedor:{ required:"Seleccione un vendedor" },
      fecha:{ required:"Seleccione la fecha" },
      monto:{ required:"Ingrese el monto", number:"Monto inválido" }
    },
    errorElement:'span',
    errorPlacement:function(error,element){ error.addClass('invalid-feedback'); element.closest('.form-group').append(error); },
    highlight:function(el){ $(el).addClass('is-invalid'); },
    unhighlight:function(el){ $(el).removeClass('is-invalid'); }
  });
});
</script>
