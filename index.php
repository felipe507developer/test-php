<?php
    include_once("controller/funciones.php");

if (isset($_SERVER['HTTP_COOKIE'])) {
	$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
	foreach($cookies as $cookie) {
		$parts = explode('=', $cookie);
		$name = trim($parts[0]);
		setcookie($name, '', time()-1000);
		setcookie($name, '', time()-1000, '/');
	}
}
?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
	<link rel="manifest" href="manifest.json"></link>
	<meta name="description" content="PWA de PMC Vitae">
	<meta name="theme-color" content="#186099">
	<meta name="apple-mobile-web-app-title" content="PWA de PMC Vitae">
	<meta name="appre-mobile-web-app-status-bar-style" content="black">
	<meta name="appre-mofle-web-app-capable" content="yes">
	<link rel="apple-touch-icon" href="icons/pwa/maskable_icons/192X192.png">
	
	<link href="https://cdn.lineicons.com/2.0/LineIcons.css" rel="stylesheet">

	
	
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Vitae - Login</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="./images/favicon.png">
    <link href="./css/style.css" rel="stylesheet">
	<!-- Ajustes -->
    <link href="./css/ajustes.css" rel="stylesheet">
	<style>
		.form-control > button {
			display:none;
		}
		
		input.text {
			height: 28px;
		}
	</style>
</head>

<body class="h-100" style="background-color: #e0fbed !important;">
    <div class="authincation h-100" >
        <div class="container h-100" >
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12" style=" border-radius: 10px;" >
                                <div class="auth-form" >
									<div class="deznav-scroll d-none"></div>
									<div class="profile-photo text-center mb-2">
										<img  id="logo" src="images/logo.png" width="52" class="img-fluid" alt="">
									</div> 
									<?php 
										include('controller/config.php');
										if($TITTLE!=''){
											echo '
											<h3 class="text-center">
												<i class="lni lni-warning" style="color:'.$TITTLE_COLOR.'"></i><span class="nav-text " style="color:'.$TITTLE_COLOR.'">'.$TITTLE.'</span>
											</h3>';
										}
									?>
                                    <h3 class="text-center mb-4" style="color:#293f76">Inicie sesión en su cuenta<br> PMC</h3> 
                                    <form role="form" action="" id="frmAcceso" name="frmAcceso" method="POST" autocomplete="off">
										<label class="control-label" for="val-username">
											<strong><span class="text-danger">* </span><label class="mb-1">Usuario</label></strong>
										</label>
										<div class="input-group">
											<div class="input-group-prepend" style="height: 28px !important;">
												<span class="input-group-text "> <i class="fa fa-user"></i> </span>
											</div>
											<input type="text" class="form-control text" id="txtUsuario" name="txtUsuario" placeholder="Usuario" autocomplete="false" readonly onfocus="this.removeAttribute('readonly');">
										</div>
										<div class="input-group">
											<strong><span class="text-danger">* </span><label class="mb-1">Contraña</label></strong>
											<div class="input-group ">
												<div class="input-group-prepend" style="height: 28px !important;">
													<span class="input-group-text"> <i class="fa fa-lock"></i> </span>
												</div>
												<input type="password" class="form-control text" id="txtClave" name="txtClave" placeholder="Contraseña" autocomplete="false" readonly onfocus="this.removeAttribute('readonly');">
												<div class="input-group-append" style="height: 28px !important;">
													<button style="height:28px" title="Mostrar contraseña" id="showPassword" class="btn btn-sm input-group-text" type="button"> <span class="fa fa-eye" id="icono"></span>
													</button>
												</div>
											</div>
										</div>
                                        <div class="form-group text-center">
											<p><a hidden href="page-forgot-password.html">¿Se te olvidó tu contraseña?</a></p>
										</div>
                                        <div class="text-center">
                                            <button disabled type="submit" id="inicio_sesion" class="btn btn-primary btn-block" style="height: 35px;padding: 0px;">Iniciar sesión</button>
                                        </div>
                                    </form> 
                                    <div hidden class="new-account mt-3 text-center">
                                        <p>¿No tienes una cuenta? <a class="text-primary" href="./page-register.html">Regístrate</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="./vendor/global/global.min.js"></script>
	<script src="./vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
    <script src="./js/custom.min.js"></script>
    <script src="./js/deznav-init.js"></script>
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<!-- Select2 -->
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="js/index/index.js"></script>


</body>

</html>