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
    <title>Paciente nuevo</title>
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
                                Paciente nuevo

                            </div>
                          </a>  
                        </div>

                        <ul class="navbar-nav header-right">
                            <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link ai-icon" href="javascript:;" role="button" data-toggle="dropdown">
                                    <i class="fas fa-bell text-success"></i>
									
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <div id="DZ_W_Notification1" class="widget-media dz-scroll p-3 height380">
										<ul class="timeline" id="incidentesnotific">
										    
										</ul>
									</div>
                                    <a href="#tabla_incidentes" class="all-notification ancla"  name="incidentesC">Ver todos los Incidentes <i class="ti-arrow-down"></i></a>
                                </div>
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
							<li class="nav-item dropdown header-profile"  id="regiones">
                            </li>
							<li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="javascript:;" role="button" data-toggle="dropdown">
                                    <!--<img src="images/logo.png" width="20" alt=""/>-->
                                    <div class="round-header"><?php echo $inombre; ?></div>
									<div class="header-info">
										<span><?php echo $nombre; ?></span>
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
				
				
				<div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Datos del paciente</h4>
                            </div>
                            <div class="card-body" id="div-paciente">
                                <div class="form-row col-12">
                                    <div class="col-md-3 col-xs-12 col-sm-3">		
                                        <div class="form-group label-floating is-empty">
                                            <label class="control-label" for=""><span style="color:red">* </span><span class="doc_identificacion">Cédula</span> <span class="cedula-vivcare"></span></label>
                                            <input  type="text" class="form-control mandatorio" id="cedula">
                                            <span class="material-input"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xs-12 col-sm-3">		
                                        <div class="form-group label-floating is-empty">
                                            <label class="control-label " for=""><span style="color:red">* </span>Nombre completo</label>
                                            <input  type="text" class="form-control mandatorio solotexto" id="nombre">
                                            <span class="material-input"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xs-12 col-sm-3">		
                                        <div class="form-group label-floating is-empty">
                                            <label class="control-label" for=""><span style="color:red">* </span>Fecha de nacimiento</label>
                                            <input  type="text" class="form-control mandatorio" id="fecha-nacimiento">
                                            <span class="material-input"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xs-12 col-sm-3">    
                                        <label class="col-form-label"><span style="color:red">* </span>Sexo</label>
                                        <div class="basic-form">
                                            <form>
                                                <div class="form-group mb-0">
                                                    <label class="radio-inline mr-3"></label>
                                                    <label class="radio-inline mr-3"><input type="radio" name="sexo" value="M" class="mandatorio"> Masculino</label>
                                                    <label class="radio-inline mr-3"><input type="radio" name="sexo" value="F" class="mandatorio"> Femenino</label>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xs-12 col-sm-3">		
                                        <div class="form-group label-floating is-empty">
                                            <label class="control-label" for=""><span style="color:red">* </span>Teléfono</label>
                                            <input  type="text" class="form-control mandatorio solonumero" id="telefono">
                                            <span class="material-input"></span>
                                        </div>
                                    </div>
                                   
                                    <div class="col-md-4 col-xs-12 col-sm-4">		
                                        <div class="form-group label-floating is-empty">
                                            <label class="control-label" for="">Correo</label>
                                            <input  type="email" class="email form-control " id="correo">
                                            <span class="material-input"></span>
                                        </div>
                                    </div>
                                    
                                               
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-center mt-4">
                                    <a type="button" href="pacientes.php" class="btn btn-warning light btn-sm mr-2" id="#">Atrás</a>
                                    <button type="button" class="btn btn-primary btn-sm ml-2" id="boton-guardar">Guardar</button>
                                </div>
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
    <script type="text/javascript"
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC037nleP4v84LrVNzb4a0fn33Ji37zC18&libraries=places">
    </script>
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
    <script src="js/daterangepicker-master/daterangepicker.js"></script>
    <?php linksFooter(); ?>
    <script src="./assets/js/sweetalert.min.js"></script>
    <script src="https://kit.fontawesome.com/7f9e31f86a.js" crossorigin="anonymous"></script>	
	<script src="./js/pacientes/paciente-nuevo.js" ></script>

</body>
</html>