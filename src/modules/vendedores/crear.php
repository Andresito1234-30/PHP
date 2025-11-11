<?php

$page_title = "Agregar Nuevo Vendedor";

include '../../admin/header.php';

include '../../admin/navbar.php';

include '../../admin/sidebar.php';


include '../../includes/config.php';

include '../../includes/models/Vendedor.php';


$database = new Database();

$db = $database->getConnection();

$vendedor_obj = new Vendedor($db);

?>


<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">

                    <h1>Agregar Vendedor</h1>

                </div>

                <div class="col-sm-6">

                    <ol class="breadcrumb float-sm-right">

                        <li class="breadcrumb-item"><a href="../../admin/index.php">Inicio</a></li>

                        <li class="breadcrumb-item"><a href="index.php">Vendedores</a></li>

                        <li class="breadcrumb-item active">Nuevo</li>

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

                    <div class="card card-primary">

                        <div class="card-header">

                            <h3 class="card-title">Información del Vendedor</h3>

                        </div>

                        <!-- /.card-header -->

                        

                        <!-- form start -->

                        <form action="acciones/crear.php" method="post" id="formVendedor">

                            <div class="card-body">

                                <div class="row">

                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label for="fechaventa">Fecha de Venta *</label>

                                            <input type="date" class="form-control" id="fechaventa" name="fechaventa" 

                                                   value="<?php echo date('Y-m-d'); ?>" required>

                                        </div>

                                    </div>

                                </div>


                                <div class="form-group">

                                    <label for="vendedor">Nombre del Vendedor *</label>

                                    <input type="text" class="form-control" id="vendedor" name="vendedor" 

                                           placeholder="Ingrese el nombre completo del vendedor" required>

                                </div>


                                <div class="form-group">

                                    <label for="direccion">Dirección *</label>

                                    <input type="text" class="form-control" id="direccion" name="direccion" 

                                           placeholder="Ingrese la dirección completa" required>

                                </div>

                            </div>

                            <!-- /.card-body -->


                            <div class="card-footer">

                                <button type="submit" class="btn btn-primary">

                                    <i class="fas fa-save"></i> Guardar Vendedor

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

    $('#formVendedor').validate({

        rules: {

            id: {

                required: true,

                digits: true,

                min: 1

            },

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

            id: {

                required: "Por favor ingrese el ID",

                digits: "Solo se permiten números",

                min: "El ID debe ser mayor a 0"

            },

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


    // Verificar si el ID ya existe

    $('#id').on('blur', function() {

        var id = $(this).val();

        if(id) {

            $.ajax({

                url: 'acciones/verificar_id.php',

                type: 'POST',

                data: {id: id},

                success: function(response) {

                    if(response.existe) {

                        $('#id').addClass('is-invalid');

                        $('#id').after('<span class="error invalid-feedback">Este ID ya está en uso</span>');

                    }

                }

            });

        }

    });

});

</script>