<?php
$page_title = "Editar Venta";

include '../../admin/header.php';
include '../../admin/navbar.php';
include '../../admin/sidebar.php';

include '../../includes/config.php';
include '../../includes/models/Venta.php';
include '../../includes/models/Vendedor.php';

$db = (new Database())->getConnection();
$venta = new Venta($db);
$vendedorModel = new Vendedor($db);

if (!isset($_GET['id'])) {
  $_SESSION['error'] = "ID de venta no especificado.";
  header("Location: index.php"); exit;
}
$venta->id_venta = (int)$_GET['id'];
if (!$venta->leerUno()) {
  $_SESSION['error'] = "Venta no encontrada.";
  header("Location: index.php"); exit;
}
$listaVendedores = $vendedorModel->leer();
?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Editar Venta</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="../../admin/index.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="index.php">Ventas</a></li>
            <li class="breadcrumb-item active">Editar</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row"><div class="col-md-8 mx-auto">
        <div class="card card-warning">
          <div class="card-header"><h3 class="card-title">Editando venta #<?php echo $venta->id_venta; ?></h3></div>

          <form action="acciones/actualizar.php" method="post" id="formEditarVenta">
            <input type="hidden" name="id_venta" value="<?php echo $venta->id_venta; ?>">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="fecha">Fecha *</label>
                    <input type="datetime-local" class="form-control" id="fecha" name="fecha"
                           value="<?php echo date('Y-m-d\TH:i', strtotime($venta->fecha)); ?>" required>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="id_vendedor">Vendedor *</label>
                    <select class="form-control" id="id_vendedor" name="id_vendedor" required>
                      <?php while($v = $listaVendedores->fetch(PDO::FETCH_ASSOC)): 
                              $selected = ((int)$v['id'] === (int)$venta->id_vendedor) ? 'selected' : ''; ?>
                        <option value="<?php echo (int)$v['id']; ?>" <?php echo $selected; ?>>
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
                    <input type="number" step="0.01" min="0" class="form-control" id="monto" name="monto"
                           value="<?php echo htmlspecialchars($venta->monto); ?>" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="metodo_pago">Método de pago *</label>
                    <select class="form-control" id="metodo_pago" name="metodo_pago" required>
                      <?php
                        $metodos = ['Efectivo','Tarjeta','Transferencia','Yape/Plin'];
                        foreach ($metodos as $m) {
                          $sel = ($m === $venta->metodo_pago) ? 'selected' : '';
                          echo "<option value=\"{$m}\" {$sel}>{$m}</option>";
                        }
                      ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="nota">Nota (opcional)</label>
                <input type="text" class="form-control" id="nota" name="nota" maxlength="255"
                       value="<?php echo htmlspecialchars($venta->nota); ?>">
              </div>
            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Actualizar Venta</button>
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
  $('#formEditarVenta').validate({
    rules:{
      id_vendedor:{ required:true, digits:true, min:1 },
      fecha:{ required:true },
      monto:{ required:true, number:true, min:0 },
      metodo_pago:{ required:true },
      nota:{ maxlength:255 }
    }
  });
});
</script>
