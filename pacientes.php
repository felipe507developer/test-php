<?php
	include_once("controller/funciones.php");
	include_once("controller/conexion.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Vitae - Pacientes </title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="./images/favicon.png">
	<link href="./vendor/owl-carousel/owl.carousel.css" rel="stylesheet">
	
	<link href="./vendor/bootstrap-select/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
	<link href="https://cdn.lineicons.com/2.0/LineIcons.css" rel="stylesheet">
	<!-- Datatable -->
    <link href="./vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
    <!-- Ajustes -->
    <link href="./css/ajustes.css" rel="stylesheet">
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            width:auto !important;
        }
    </style>
    <!-- Daterange picker -->
    <link href="js/daterangepicker-master/daterangepicker.css" rel="stylesheet">
    
</head>
<body>
    <!--*******
        ORVERLAY
    ********-->
    <div id="overlay">
		<div id="text"><strong>Procesando...</strong></div>
    </div>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper" class="show">

        <!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
          

            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->
        <!--**********************************
            Configuración start
        ***********************************-->
		<div class="config">
        <div class="config-close"></div>
				<div class="custom-tab-1">
					<ul class="nav nav-tabs">
						<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#filtrosconfig">Configuración</a>
						</li>
					</ul>
					<div class="card mb-sm-3 mb-md-0">
						<div class="card-body p-0 dz-scroll" id="DZ_W_Filtros_Body">
							<div class="form-config">
								<form>
									
									<div class="form-row col-12">
										
										<div class="col-md-12 col-sm-12 col-xs-12">
											<div class="form-group label-floating">
												<label class="control-label">Organización</label>                                        
												<select class="form-control" id="select-organizacion" style="width:100%">
												</select>
												<span class="material-input"></span>
											</div>
										</div>
										<div class="col-md-12 col-sm-12 col-xs-12">
											<div class="form-group label-floating">
												<label class="control-label">Empresa</label>                                        
												<select class="form-control" id="select-empresa" style="width:100%">
												</select>
												<span class="material-input"></span>
											</div>
										</div>
									</div>	
									<br>
									<div class="card-footer" style="text-align:center">
										<button type="button" class="btn btn-primary light" id="boton-aplicar-filtros-globales">Aplicar</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
		</div>
		<!--**********************************
            Configuración End
        ***********************************-->
		<!--**********************************
            Header start
        ***********************************-->
        <div class="header" name="top">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <a  href="#top">
							<a href="#" class="btn btn-primary ir-arriba"><i class="las la-arrow-up"></i></a>
                            <div class="dashboard_bar">
                                Pacientes
                            </div>
                          </a>  
                        </div>

                        <ul class="navbar-nav header-right">
                            <li class="nav-item notification_dropdown d-none" id="filtros">   
                                <a class="nav-link ai-icon"  role="button" >
                                    <i class="fas fa-filter text-info"></i>
                                    <span class="badge light text-white bg-primary" id="filtro_masivo_indicador"></span>
                                </a>
                            </li>

							<li class="nav-item dropdown notification_dropdown" style="display: none;">
                                <a class="nav-link bell bell-link" href="javascript:;">
                                    <i class="fas fa-comments text-success"></i>
									<!--<span class="badge light text-white bg-primary">5</span>-->
                                </a>
							</li>
							<li class="nav-item dropdown notification_dropdown" style="display:none;">
                                <a class="nav-link bell config-link" href="javascript:;">
                                    <i class="fas fa-cogs text-success"></i>
									<!--<span class="badge light text-white bg-primary">5</span>-->
                                </a>
							</li>
							<li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="javascript:;" role="button" data-toggle="dropdown">
                                    <!--<img src="images/logo.png" width="20" alt=""/>-->
                                    <div class="round-header" id="div-inicial-usuario"></div>
									<div class="header-info">
										<span id="span-nombre-usuario"></span>
									</div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
									<?php opciones_usuario(); ?>

                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
            <!--**********************************
                        FILTROS 
            ***********************************-->
            <div class="pos-f-t">
                <div class=" form-group col-md-12 col-xs-12 col-sm-12 text-center collapse" id="navbarToggleExternalContent">
                    <div class="card">
                        <div class="card-header ">
                            <h4 class="card-title">Filtros generales</h4>                                            
                        </div>
                        <div class="card-body">
                            <div class="form-row col-12">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group label-floating">
                                            <label class="control-label">Por Estado</label>                                        
                                            <select class="form-control" id="select-filtro-estado" style="width:100%">
                                            </select>
                                            <span class="material-input"></span>
                                        </div>
                                    </div>
                                
                            </div>	
                            <div class="card-footer">
                                <button type="button" id="cerrar_filtros" class="btn btn-danger light">Cerrar</button>
                                <button type="button" class="btn btn-info light" id="boton-limpiar-filtros">Limpiar</button>
                                <button type="button" class="btn btn-primary light" id="boton-aplicar-filtros">Aplicar</button>
                            </div>	
                        </div>
                    </div>
                </div>
                <nav class="navbar ">
                    <button id="btn_filtro" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                    </button>
                </nav>
            </div>
            <!--**********************************
                FILTROS 
            ***********************************-->

        </div>
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->


        <!--**********************************
            Sidebar start
        ***********************************-->
        <?php menu(); ?>
        <!--**********************************
            Sidebar end
        ***********************************-->
		
		<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid" style="display:padding-top: 0px !important">
				<div class="form-head d-flex mb-3 mb-md-4 align-items-start">
					<div class="mr-auto d-none d-lg-block" style="display: none !important">
						<h3 class="text-black font-w600">Bienvenido a Vitae!</h3>
						<p class="mb-0 fs-18">Tu aliado de salud en casa</p>
					</div>
					<!--
					<div class="input-group search-area ml-auto d-inline-flex">
						<input type="text" class="form-control" placeholder="Buscar...">
						<div class="input-group-append">
							<button type="button" class="input-group-text"><i class="flaticon-381-search-2"></i></button>
						</div>
					</div>
					-->
				</div>
				
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Lista de pacientes</h4>
                            <div>                                
                                <a href="paciente-nuevo.php" class="btn btn-sm light btn-success" ><i class="fas fa-user"></i> Nuevo paciente</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tablapacientes" class="display min-w850 table" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Acciones</th>
                                            <th class="doc_identificacion">Cédula</th>
                                            <th>Nombre completo</th>
                                            <th>Edad</th>
                                            <th>Sexo</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                        
            </div>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->


        <!--**********************************
            Footer start
        ***********************************-->
        <div class="footer">
            <div class="copyright">
                <p>Copyright © <a href="https://vitae-health.com" target="_blank">Vitae Health</a> 2021</p>
            </div>
        </div>
        <!--**********************************
            Footer end
        ***********************************-->
    </div>

    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="./vendor/global/global.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<script src="./vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
    <script src="./js/custom.min.js"></script>
	<script src="./js/deznav-init.js"></script>
	<script src="./vendor/owl-carousel/owl.carousel.js"></script>
	
	<!-- Apex Chart -->
	<script src="./vendor/apexchart/apexchart.js"></script>
	
	<!-- Datatable -->
    <script src="./vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="./js/plugins-init/datatables.init.js"></script>

    <!-- momment js is must -->
    <script src="./vendor/moment/moment.min.js"></script>
    <!-- Daterangepicker -->
    
    <?php linksFooter(); ?>

    <script src="./assets/js/sweetalert.min.js"></script>
    <script src="https://kit.fontawesome.com/7f9e31f86a.js" crossorigin="anonymous"></script>	
	<script src="./js/pacientes/pacientes.js" ></script>
</body>
</html>