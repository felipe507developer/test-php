<?php

	include("conexion.php");

	//ini_set('display_errors', 0);ini_set('display_startup_errors', 0);
	$oper = '';
	if (isset($_REQUEST['oper'])) {
		$oper = $_REQUEST['oper'];
	}
	
	switch($oper){
		case "listar":
			  listar();
		break;

		case "cambiar_bd":
			cambiar_bd();
			break;
		default:
			echo "{failure:true}";
		break;
	}	

	function listarq(){
		header('Access-Control-Allow-Origin: *');
		//$mysqli_bd = new mysqli("vmoq6v31svkm65.ckp2cxcczvoc.us-west-2.rds.amazonaws.com", "vitae_user", "uOyZm8H7JAkvByOZufWP",  "db_maestro");
        include('config.php');
        $mysqli_bd = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME );

		if ($mysqli_bd->connect_error) {
			echo "Fallo al conectar a MySQL: (" . $mysqli_bd->connect_error . ") " . $mysqli_bd->connect_error;
			die();
		}else{
			$mysqli_bd->query("SET NAMES utf8"); 
			$mysqli_bd->query("SET CHARACTER SET utf8");
			$usuario = $_REQUEST['txtUsuario'];		
			
			$option = "";
			$lista_ambientes = array();
			$contador  = 0;
			if ($usuario!="") {
				$query = "Select bd.nombre as nombre, bd.region as region
							From usuarios u
							INNER JOIN bases_de_datos bd ON bd.id = u.id_bd
							Where u.usuario = '$usuario'";
				if($consulta = $mysqli_bd->query($query)){
                    while($registro=$consulta->fetch_assoc()) {
                        $contador = $contador +1;
                        $option .= '<option value="'.$registro['nombre'].'">'.$registro['region'].'</option>';
                        //$lista_ambientes .='<a href="#"><li class="db_select" data-bd="'.$registro['nombre'].'">'.$registro['region'].'</li></a>';

                        $lista_ambientes[] = array(
                            'nombre'  =>  $registro['nombre'],
                            'region'  =>  $registro['region']
                        );

                    }
                    setcookie("lista_ambientes",json_encode($lista_ambientes), time() + 31536000,"/");
                    if($contador >= 2){
                        $_SESSION['multiacceso'] = 1;
                    }else{
                        $_SESSION['multiacceso'] = 0;
                    }
                    echo json_encode(array("count"=>$contador,"option"=>$option));
				}else{
					die($mysqli_bd->error);			
				}
			}	
		}
	}
	
	function listar(){
		include('config.php');
		header('Access-Control-Allow-Origin: *');
		$mysqli_bd = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
		if ($mysqli_bd->connect_error) {
			echo "Fallo al conectar a MySQL: (" . $mysqli_bd->connect_error . ") " . $mysqli_bd->connect_error;
			die();
		}else{
			$mysqli_bd->query("SET NAMES utf8"); 
			$mysqli_bd->query("SET CHARACTER SET utf8");
			$usuario = $_REQUEST['txtUsuario'];		
			
			$option = "";
			$lista_ambientes = array();
			$contador  = 0;
			if ($usuario!="") {
				$query = "SELECT bd.nombre AS nombre, bd.region AS region, c.bandera,c.ruta_logo_3 AS logo, bd.token_base
							FROM usuarios u
							INNER JOIN bases_de_datos bd ON bd.id = u.id_bd
							INNER JOIN configuraciones c ON c.id_bd = bd.id  
							WHERE u.usuario ='$usuario'";
				if($consulta = $mysqli_bd->query($query)){
					while($registro=$consulta->fetch_assoc()) {
						$contador = $contador +1;
						$option .= '<option data-logo="'.$registro['logo'].'" value="'.$registro['nombre'].'">'.$registro['region'].'</option>';
						$lista_ambientes[]=array(
							'bd' =>$registro['nombre'], 
							'token_base' =>$registro['token_base'], 
							'region'=>$registro['region'],
							'logo'=>$registro['logo']
						);
					}
					setcookie('lista_ambientes',json_encode($lista_ambientes), time() + 84000, "/"); 

					if($contador >= 2){
						$_SESSION['multiacceso'] = 1;
					}else{
						$_SESSION['multiacceso'] = 0;
					}
					echo json_encode(array("count"=>$contador,"option"=>$option,"lista_ambientes"=>$lista_ambientes));
				}else{
					echo $mysqli_bd->error; 
					mysqli_close($mysqli);
				}
			}	
		}
	}


	function cambiar_bd(){

		include('config.php');

		$bd = $_REQUEST['bd'];
		ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
		// $_SESSION['nombre_bd'] = $bd;
		// setcookie("nombre_bd",$bd);
		global $mysqli;


		if($mysqli->select_db($bd)){
			$usuario= $_SESSION['usuario'];

			$consulta = $mysqli->query("Select * From usuarios Where usuario = '$usuario'");
			if ($registro=$consulta->fetch_assoc()) {
				$_SESSION['usuario']		= $registro['usuario'];
				$_SESSION['clave']			= $registro['clave'];
				$_SESSION['user_id']		= $registro['id'];
				$_SESSION['nombreUsuario']	= $registro['nombre'];
				$_SESSION['nivel']			= $registro['nivel'];
				$_SESSION['correo_user']			= $registro['correo'];
				$_SESSION['soporte']		= $registro['soporte'];
				$_SESSION['supervisor']		= $registro['supervisor'];
				$_SESSION['nombre_bd'] = $bd;
				$consulta2 = $mysqli->query("Insert into bitacora values(0, '$usuario', now(), 'LOGIN', '-', '-', '-')");
				// COOKIES
				$mysqli_bd = new mysqli($DB_HOST, $DB_USER, $DB_PASS,$DB_NAME);
				$q_bd = "SELECT * FROM bases_de_datos where nombre = '$bd'";
				$result_bd = $mysqli_bd->query($q_bd);
				$r_bd= $result_bd->fetch_assoc();
				$id_bd= $r_bd['id'];
				$configura = "SELECT * FROM configuraciones where id_bd = '$id_bd' ";
				$config = $mysqli_bd->query($configura);
				$resultado = array();
				$cfg = $config->fetch_assoc();
				$resultado['data'][] = array(
					'nombre'  =>   $cfg['nombre'],
					'direccion'  =>   $cfg['direccion'],
					'telefono'  =>   $cfg['telefono'],
					'whatsapp'  =>   $cfg['whatsapp'],
					'tipo_identificacion'  =>   $cfg['tipo_identificacion'],
					'identificacion'  =>   $cfg['identificacion'],
					'banco'  =>   $cfg['banco'],
					'tipo_cuenta'  =>   $cfg['tipo_cuenta'],
					'nombre_cuenta'  =>   $cfg['nombre_cuenta'],
					'numero_cuenta'  =>   $cfg['numero_cuenta'],
					'correo_comprobante'  =>   $cfg['correo_comprobante'],
					'telefono_comprobante'  =>   $cfg['telefono_comprobante'],
					'nombre_impuesto'  =>   $cfg['nombre_impuesto'],
					'porcentaje_impuesto'  =>   $cfg['porcentaje_impuesto'],
					'moneda'  =>   $cfg['moneda'],
					'cuenta_yapy'  =>   $cfg['cuenta_yapy'],
					'pais'  =>   $cfg['pais'],
					'bandera_img'  =>   $cfg['bandera'],
					'doc_identificacion'  =>  $cfg['doc_identificacion'],
					'chk_impuesto'  =>  $cfg['chk_impuesto'],
					'url_api'  =>  $cfg['url_api'],
					'icono_notificacion'  =>   $cfg['icono_notificacion'],
					'correos_factura_proveedor'  =>   $cfg['correos_factura_proveedor'],
					'latitud'  =>   $cfg['latitud'],
					'longitud'  =>   $cfg['longitud'],
					'abreviatura_pais'  =>   $cfg['abreviatura_pais'],
					'ruta_logo'  =>   $cfg['ruta_logo'],
					'ruta_logo_txt'  =>   $cfg['ruta_logo_txt'],
					'ruta_logo_2'  =>   $cfg['ruta_logo_2'],
					'ruta_logo_3'  =>   $cfg['ruta_logo_3'],
					'jubilado'  =>   $cfg['jubilado'],
				);


                $configuraciones_json= $cfg;

				$latitud  =   $cfg['latitud'];
				$longitud  =   $cfg['longitud'];
                setcookie('configuraciones_json',utf8_encode(json_encode($configuraciones_json)), time() + 31536000,"/");
				setcookie('moneda',  $cfg['moneda'],0,'/');
				setcookie('impuesto',  $cfg['nombre_impuesto'],0,'/');
				setcookie('tipo_identificacion', $cfg['tipo_identificacion'],0,'/');
				setcookie('doc_identificacion',  $cfg['doc_identificacion'],0,'/');
				setcookie('chk_impuesto',  $cfg['chk_impuesto'],0,'/');

				setcookie("token_base",$r_bd['token_base'],0,'/');
				setcookie("nombre_bd",$bd,0,'/');
				setcookie("db_name",$bd,0,'/');
				setcookie('url_api',  $cfg['url_api'],0,'/');
				setcookie("usuario",$registro['usuario'],0,'/');
				setcookie("user_id",$registro['id'],0,'/');
				setcookie("nivel",$registro['nivel'],0,'/');
				setcookie("correo_user",$registro['correo'],0,'/');
				setcookie("soporte",$registro['soporte'],0,'/');
				setcookie("clave",$registro['clave'],0,'/');
				setcookie("region",$cfg['pais'],0,'/');
				echo json_encode(array("success"=> 1, "filtros" => Cargar_filtros($registro['id'],$registro['nivel']),'latitud'=>$latitud, 'longitud'=>$longitud ));

			}else{
				echo 0;
			}

		}else{
			die($mysqli->error);
			echo 0;
		}

	}

	function cargar_filtros($idusuario,$nivel){
		//ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

		global $mysqli;

		$organizaciones = array();

		$q_org = "SELECT DISTINCT o.id , o.nombre  
					  FROM usuarios_organizacion_empresa u 
					  LEFT JOIN organizaciones o ON o.id = u.idorganizacion 
					  WHERE idusuario = $idusuario ORDER BY o.id ASC";
		$result_org = $mysqli->query($q_org);
		if($result_org->num_rows > 0){
			while($r_org = $result_org->fetch_assoc()){
				if($r_org['id'] == 0){
					// si el id de la organizacion es 0 entonces el usuario tiene acceso a todas las organizaciones y empresas
					$q_org2 = "SELECT DISTINCT o.id , o.nombre  FROM organizaciones o ORDER BY o.id ASC";
					$result_org2 = $mysqli->query($q_org2);
					while($r_org2 = $result_org2->fetch_assoc()){
						$empresas = array();
						$empresas[] = array(
							'id' => 0,
							'nombre' => 'Todas',
							'hospitalizacion_automatica' => 0,
							'reporte_diario' => 0,
							'precargar_notas' => 0,
							'dashboard_phm' => 0
						);
						// se consultan las empresas a las que el usuario tiene acceso para esa organizacion
						$q_emp = "SELECT DISTINCT e.id , e.nombre , 
										IFNULL(ce.hospitalizacion_automatica,0) as hospitalizacion_automatica, 
										IFNULL(ce.reporte_diario,0) as reporte_diario, 
										IFNULL(ce.precargar_notas,0) as precargar_notas, 
										IFNULL(ce.dashboard_phm,0) as dashboard_phm 
									  FROM  empresas e
									  LEFT JOIN configuracion_empresas ce on ce.id_empresa = e.id						  
									  WHERE e.idorganizacion = '".$r_org2['id']."' ORDER BY e.id DESC";
						$result_emp = $mysqli->query($q_emp);
						while($r_emp = $result_emp->fetch_assoc()){
							$empresas[] = array(
								'id' => $r_emp['id'],
								'nombre' => $r_emp['nombre'],
								'hospitalizacion_automatica' => $r_emp['hospitalizacion_automatica'],
								'reporte_diario' => $r_emp['reporte_diario'],
								'precargar_notas' => $r_emp['precargar_notas'],
								'dashboard_phm' => $r_emp['dashboard_phm']
							);
						}
						$organizaciones[] = array(
							'id' => $r_org2['id'],
							'nombre' => $r_org2['nombre'],
							'empresas' => $empresas
						);
					}

				}else{
					$empresas = array();
					// se consultan las empresas a las que el usuario tiene acceso para esa organizacion
					$q_emp = "SELECT DISTINCT e.id , e.nombre , 
									IFNULL(ce.hospitalizacion_automatica,0) as hospitalizacion_automatica, 
									IFNULL(ce.reporte_diario,0) as reporte_diario, 
									IFNULL(ce.precargar_notas,0) as precargar_notas, 
									IFNULL(ce.dashboard_phm,0) as dashboard_phm 
								  FROM usuarios_organizacion_empresa u 
								  LEFT JOIN empresas e ON e.id = u.idempresa
								  LEFT JOIN configuracion_empresas ce on ce.id_empresa = e.id						  
								  WHERE idusuario = $idusuario AND e.idorganizacion = '".$r_org['id']."' ORDER BY e.id DESC";

					$result_emp = $mysqli->query($q_emp);
					if($result_emp->num_rows >= 1){
						while($r_emp = $result_emp->fetch_assoc()){
							$empresas[] = array(
								'id' => $r_emp['id'],
								'nombre' => $r_emp['nombre'],
								'hospitalizacion_automatica' => $r_emp['hospitalizacion_automatica'],
								'reporte_diario' => $r_emp['reporte_diario'],
								'precargar_notas' => $r_emp['precargar_notas'],
								'dashboard_phm' => $r_emp['dashboard_phm'],
							);
						}
					}else{
						$empresas = array();
						$empresas[] = array(
							'id' => 0,
							'nombre' => 'Todas',
							'hospitalizacion_automatica' => 0,
							'reporte_diario' => 0,
							'precargar_notas' => 0,
							'dashboard_phm' => 0
						);
						// se consultan las empresas a las que el usuario tiene acceso para esa organizacion
						$q_emp2 = "SELECT DISTINCT e.id , e.nombre , 
										IFNULL(ce.hospitalizacion_automatica,0) as hospitalizacion_automatica, 
										IFNULL(ce.reporte_diario,0) as reporte_diario, 
										IFNULL(ce.precargar_notas,0) as precargar_notas, 
										IFNULL(ce.dashboard_phm,0) as dashboard_phm 
									  FROM  empresas e
									  LEFT JOIN configuracion_empresas ce on ce.id_empresa = e.id						  
									  WHERE e.idorganizacion = '".$r_org['id']."' ORDER BY e.id DESC";
						$result_emp2 = $mysqli->query($q_emp2);
						while($r_emp2 = $result_emp2->fetch_assoc()){
							$empresas[] = array(
								'id' => $r_emp2['id'],
								'nombre' => $r_emp2['nombre'],
								'hospitalizacion_automatica' => $r_emp2['hospitalizacion_automatica'],
								'reporte_diario' => $r_emp2['reporte_diario'],
								'precargar_notas' => $r_emp2['precargar_notas'],
								'dashboard_phm' => $r_emp2['dashboard_phm']
							);
						}
					}
					$organizaciones[] = array(
						'id' => $r_org['id'],
						'nombre' => $r_org['nombre'],
						'empresas' => $empresas,
					);
				}
			}
			setcookie('organizacion', $organizaciones[0]['id'], time() + 31536000,'/');
            setcookie('empresa', $organizaciones[0]['empresas'][0]['id'], time() + 31536000,'/');
			/*if($organizaciones[0]['empresas'][0]['id']!=0){
				setcookie('empresa', $organizaciones[0]['empresas'][0]['id']);
				setcookie('hospitalizacion_automatica', $organizaciones[0]['empresas'][0]['hospitalizacion_automatica'], time() + 84000, "/");
				setcookie('reporte_diario', $organizaciones[0]['empresas'][0]['reporte_diario'], time() + 84000, "/");
				setcookie('dashboard_phm', $organizaciones[0]['empresas'][0]['dashboard_phm'], time() + 84000, "/");
			}else{
				setcookie('empresa', $organizaciones[0]['empresas'][0]['id']);
				setcookie('hospitalizacion_automatica', $organizaciones[0]['empresas'][0]['hospitalizacion_automatica'], time() + 84000, "/");
				setcookie('reporte_diario', $organizaciones[0]['empresas'][0]['reporte_diario'], time() + 84000, "/");
				setcookie('dashboard_phm', $organizaciones[0]['empresas'][0]['dashboard_phm'], time() + 84000, "/");

			}*/
			return json_encode($organizaciones);
		}

	}

?>
