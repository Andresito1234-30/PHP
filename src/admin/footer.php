<?php

// admin/footer.php

?>

</div>

<!-- ./wrapper -->


<!-- REQUIRED SCRIPTS -->


<!-- jQuery -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap 4 -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- AdminLTE App -->

<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>

<!-- DataTables -->

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

<!-- Toastr -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Chart.js -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<!-- Custom Scripts -->

<script src="../assets/js/custom.js"></script>


<script>

// Configuración global

$(document).ready(function() {

    // Inicializar tooltips

    $('[data-toggle="tooltip"]').tooltip();

   

    // Configurar Toastr

    toastr.options = {

        "closeButton": true,

        "progressBar": true,

        "positionClass": "toast-top-right",

        "timeOut": "5000"

    };

   

    // Configuración global para DataTables

    $.extend(true, $.fn.dataTable.defaults, {

        "responsive": true,

        "autoWidth": false,

        "language": {

            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"

        },

        "pageLength": 10,

        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]]

    });

});

</script>

</body>

</html>