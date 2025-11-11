<?php

// admin/index.php

$page_title = "Dashboard Principal";

include 'header.php';

include 'navbar.php';

include 'sidebar.php';

?>


<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">

                    <h1>Dashboard</h1>

                </div>

                <div class="col-sm-6">

                    <ol class="breadcrumb float-sm-right">

                        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>

                        <li class="breadcrumb-item active">Dashboard</li>

                    </ol>

                </div>

            </div>

        </div><!-- /.container-fluid -->

    </section>


    <!-- Main content -->

    <section class="content">

        <div class="container-fluid">

            <!-- Small boxes (Stat box) -->

            <div class="row">

                <div class="col-lg-3 col-6">

                    <!-- small box -->

                    <div class="small-box bg-info">

                        <div class="inner">

                            <h3>150</h3>

                            <p>Total Vendedores</p>

                        </div>

                        <div class="icon">

                            <i class="fas fa-users"></i>

                        </div>

                        <a href="../modules/vendedores/index.php" class="small-box-footer">

                            Más info <i class="fas fa-arrow-circle-right"></i>

                        </a>

                    </div>

                </div>

                <!-- ./col -->

                <div class="col-lg-3 col-6">

                    <!-- small box -->

                    <div class="small-box bg-success">

                        <div class="inner">

                            <h3>53<sup style="font-size: 20px">%</sup></h3>

                            <p>Ventas del Mes</p>

                        </div>

                        <div class="icon">

                            <i class="fas fa-chart-line"></i>

                        </div>

                        <a href="../modules/ventas/historial.php" class="small-box-footer">

                            Más info <i class="fas fa-arrow-circle-right"></i>

                        </a>

                    </div>

                </div>

                <!-- ./col -->

                <div class="col-lg-3 col-6">

                    <!-- small box -->

                    <div class="small-box bg-warning">

                        <div class="inner">

                            <h3>44</h3>

                            <p>Vendedores Activos</p>

                        </div>

                        <div class="icon">

                            <i class="fas fa-user-check"></i>

                        </div>

                        <a href="../modules/vendedores/index.php" class="small-box-footer">

                            Más info <i class="fas fa-arrow-circle-right"></i>

                        </a>

                    </div>

                </div>

                <!-- ./col -->

                <div class="col-lg-3 col-6">

                    <!-- small box -->

                    <div class="small-box bg-danger">

                        <div class="inner">

                            <h3>65</h3>

                            <p>Ventas Pendientes</p>

                        </div>

                        <div class="icon">

                            <i class="fas fa-clock"></i>

                        </div>

                        <a href="../modules/ventas/historial.php" class="small-box-footer">

                            Más info <i class="fas fa-arrow-circle-right"></i>

                        </a>

                    </div>

                </div>

                <!-- ./col -->

            </div>

            <!-- /.row -->


            <!-- Main row -->

            <div class="row">

                <!-- Left col -->

                <section class="col-lg-7 connectedSortable">

                    <!-- Custom tabs (Charts with tabs)-->

                    <div class="card">

                        <div class="card-header">

                            <h3 class="card-title">

                                <i class="fas fa-chart-pie mr-1"></i>

                                Ventas por Vendedor

                            </h3>

                        </div><!-- /.card-header -->

                        <div class="card-body">

                            <div class="tab-content p-0">

                                <!-- Morris chart - Sales -->

                                <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;">

                                    <canvas id="ventasChart" height="300" style="height: 300px;"></canvas>

                                </div>

                            </div>

                        </div><!-- /.card-body -->

                    </div>

                    <!-- /.card -->

                </section>

                <!-- /.Left col -->


                <!-- right col (We are only adding the ID to make the widgets sortable)-->

                <section class="col-lg-5 connectedSortable">

                    <!-- Calendar -->

                    <div class="card bg-gradient-success">

                        <div class="card-header border-0">

                            <h3 class="card-title">

                                <i class="far fa-calendar-alt"></i>

                                Calendario

                            </h3>

                        </div>

                        <!-- /.card-header -->

                        <div class="card-body pt-0">

                            <!--The calendar -->

                            <div id="calendar" style="width: 100%"></div>

                        </div>

                        <!-- /.card-body -->

                    </div>

                    <!-- /.card -->

                </section>

                <!-- right col -->

            </div>

            <!-- /.row (main row) -->

        </div><!-- /.container-fluid -->

    </section>

    <!-- /.content -->

</div>

<!-- /.content-wrapper -->


<?php include 'footer.php'; ?>


<script>

// Gráfico de ventas

var ctx = document.getElementById('ventasChart').getContext('2d');

var ventasChart = new Chart(ctx, {

    type: 'bar',

    data: {

        labels: ['Juan Pérez', 'María García', 'Carlos López', 'Ana Martínez', 'Pedro Sánchez'],

        datasets: [{

            label: 'Ventas del Mes',

            data: [12, 19, 3, 5, 2],

            backgroundColor: [

                'rgba(255, 99, 132, 0.2)',

                'rgba(54, 162, 235, 0.2)',

                'rgba(255, 206, 86, 0.2)',

                'rgba(75, 192, 192, 0.2)',

                'rgba(153, 102, 255, 0.2)'

            ],

            borderColor: [

                'rgba(255, 99, 132, 1)',

                'rgba(54, 162, 235, 1)',

                'rgba(255, 206, 86, 1)',

                'rgba(75, 192, 192, 1)',

                'rgba(153, 102, 255, 1)'

            ],

            borderWidth: 1

        }]

    },

    options: {

        scales: {

            y: {

                beginAtZero: true

            }

        }

    }

});

</script>