<?php

$page_title = "Editar Vendedor";

include '../../admin/header.php';

include '../../admin/navbar.php';

include '../../admin/sidebar.php';


include '../../includes/config.php';

include '../../includes/models/Vendedor.php';


$database = new Database();

$db = $database->getConnection();

$vendedor = new Vendedor($db);


// Obtener datos del vendedor

if(isset($_GET['id'])) {

    $vendedor->id = $_GET['id'];

    

    if(!$vendedor->leerUno()) {

        $_SESSION['error'] = "Vendedor no encontrado.";

        header("Location: index.php");

        exit();

    }

} else {

    $_SESSION['error'] = "ID de vendedor no especificado.";

    header("Location: index.php");

    exit();

}

?>


<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">

                    <h1>Editar Vendedor</h1>

                </div>

                <div class="col-sm-6">

                    <ol class="breadcrumb float-sm-right">

                        <li class="breadcrumb-item"><a href="../../admin/index.php">Inicio</a></li>

                        <li class="breadcrumb-item"><a href="index.php">Vendedores</a></li>

                        <li class="breadcrumb-item active">Editar</li>

                    </ol>

                </div>

            </div>

        </div><!-- /.container-fluid -->

    </section>


    <!-- Main content -->

    <section class="content">

        <div class="container-fluid">

            <div class="row">

                <div class="col-md-8 mx-auto">

                    <div class="card card-warning">

                        <div class="card-header">

                            <h3 class="card-title">Editando: <?php echo htmlspecialchars($vendedor->vendedor); ?></h3>

                        </div>

                        

                        <!-- form start -->

                        <form action="acciones/actualizar.php" method="post" id="formEditarVendedor">

                            <input type="hidden" name="id" value="<?php echo $vendedor->id; ?>">

                            

                            <div class="card-body">

                                <div class="row">

                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label for="id_display">ID del Vendedor</label>

                                            <input type="text" class="form-control" id="id_display" 

                                                   value="<?php echo $vendedor->id; ?>" disabled>

                                            <small class="form-text text-muted">El ID no se puede modificar</small>

                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label for="fechaventa">Fecha de Venta *</label>

                                            <input type="date" class="form-control" id="fechaventa" name="fechaventa" 

                                                   value="<?php echo $vendedor->fechaventa; ?>" required>

                                        </div>

                                    </div>

                                </div>


                                <div class="form-group">

                                    <label for="vendedor">Nombre del Vendedor *</label>

                                    <input type="text" class="form-control" id="vendedor" name="vendedor" 

                                           value="<?php echo htmlspecialchars($vendedor->vendedor); ?>" required>

                                </div>


                                <div class="form-group">

                                    <label for="direccion">Dirección *</label>

                                    <input type="text" class="form-control" id="direccion" name="direccion" 

                                           value="<?php echo htmlspecialchars($vendedor->direccion); ?>" required>

                                </div>

                            </div>

                            <!-- /.card-body -->


                            <div class="card-footer">

                                <button type="submit" class="btn btn-warning">

                                    <i class="fas fa-save"></i> Actualizar Vendedor

                                </button>

                                <a href="index.php" class="btn btn-default float-right">

                                    <i class="fas fa-arrow-left"></i> Cancelar

                                </a>

                            </div>

                        </form>

                    </div>

                    <!-- /.card -->

                </div>

            </div>

        </div>

    </section>

    <!-- /.content -->

</div>

<!-- /.content-wrapper -->


<?php include '../../admin/footer.php'; ?>


<script>

$(document).ready(function() {

    // Validación del formulario

    $('#formEditarVendedor').validate({

        rules: {

            vendedor: {

                required: true,

                minlength: 2

            },

            direccion: {

                required: true,

                minlength: 5

            },

            fechaventa: {

                required: true,

                date: true

            }

        },

        messages: {

            vendedor: {

                required: "Por favor ingrese el nombre",

                minlength: "El nombre debe tener al menos 2 caracteres"

            },

            direccion: {

                required: "Por favor ingrese la dirección",

                minlength: "La dirección debe tener al menos 5 caracteres"

            },

            fechaventa: {

                required: "Por favor seleccione la fecha",

                date: "Por favor ingrese una fecha válida"

            }

        },

        errorElement: 'span',

        errorPlacement: function (error, element) {

            error.addClass('invalid-feedback');

            element.closest('.form-group').append(error);

        },

        highlight: function (element, errorClass, validClass) {

            $(element).addClass('is-invalid');

        },

        unhighlight: function (element, errorClass, validClass) {

            $(element).removeClass('is-invalid');

        }

    });

});

</script>