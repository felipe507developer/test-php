<?php
	include("../conexion.php");	
	include("../conexion_s3.php");	
	// ini_set('display_errors', 1);ini_set('display_startup_errors', 1);error_reporting(E_ALL);

	$oper = '';
	if (isset($_REQUEST['oper'])) {
		$oper = $_REQUEST['oper'];   
	}

	switch($oper){
		case "antecedentes":
			antecedentes();
			mysqli_close($mysqli);
			break;
		case "quitar_antecedente":
			quitar_antecedente();
			mysqli_close($mysqli);
			break;
		case "agregar_antecedente":
			agregar_antecedente();
			mysqli_close($mysqli);
			break;
		case "cargar_evaluaciones_medicas":
			cargar_evaluaciones_medicas();
			mysqli_close($mysqli);
			break;
		case "signos_vitales":
			signos_vitales();
			mysqli_close($mysqli);
			break;
		case "notas_medicos":
			notas_medicos();
			mysqli_close($mysqli);
			break;
		case "notas_enfermeria":
			notas_enfermeria();
			mysqli_close($mysqli);
			break;
		case "receta_medica":
			receta_medica();
			mysqli_close($mysqli);
			break;
		case "notas_evolucion":
			notas_evolucion();
			mysqli_close($mysqli);
			break;
		case "listar_resultados_laboratorios":
			listar_resultados_laboratorios();
			mysqli_close($mysqli);
			break;
		case "listar_documentos":
			listar_documentos();
			mysqli_close($mysqli);
			break;
		case "subir_documento":
			subir_documento();
			mysqli_close($mysqli);
			break;
		case "eliminar_documento":
			eliminar_documento();
			mysqli_close($mysqli);
			break;
		case "editar_documentos":
			editar_documentos();
			mysqli_close($mysqli);
			break;
		case "cargar_alertas_sv":
			cargar_alertas_sv();
			mysqli_close($mysqli);
			break;
		default:
			echo json_encode(array('error'=>true, 'mensaje'=>'Operador no definido',"oper"=> $oper));
			mysqli_close($mysqli);
		break;
	}
	
	function antecedentes(){
		global $mysqli;
		$id_paciente = $_GET['id_paciente'];
		$response = array();
		$q_alergias="SELECT pa.id, a.nombre AS nombre FROM pac_alergias pa 
					JOIN maestro_alergias a ON a.id = pa.idalergia 
					WHERE pa.idpaciente = $id_paciente";
					$r_alergias = $mysqli->query($q_alergias);
					while($row_alergias = $r_alergias->fetch_assoc()){
						$response['alergias'][] = array(
							"id" => $row_alergias['id'],"nombre" => $row_alergias['nombre']
						);
					}
		$q_ant_patologicos="SELECT pa.id, e.nombre  FROM pac_antecedentesper pa
							JOIN enfermedades e ON e.id = pa.idenfermedad
							WHERE pa.idpaciente = $id_paciente";
							$r_ant_patologicos = $mysqli->query($q_ant_patologicos);
							while($row_ant_patologicos = $r_ant_patologicos->fetch_assoc()){
								$response['ant_patologicos'][] = array(
									"id" => $row_ant_patologicos['id'],"nombre" => $row_ant_patologicos['nombre']
								);
							}
		$q_habitos="SELECT pa.id, h.nombre FROM pac_habitosper pa
					JOIN maestro_antecedenteshabitosper h ON h.id = pa.idhabitosp
					WHERE pa.idpaciente = $id_paciente";
					$r_habitos = $mysqli->query($q_habitos);
					while($row_habitos = $r_habitos->fetch_assoc()){
						$response['habitos'][] = array(
							"id" => $row_habitos['id'],"nombre" => $row_habitos['nombre']
						);
					}
		$q_ant_quirurgicos="SELECT pa.id, q.nombre, pa.fecha FROM pac_antecedentesqui pa
							JOIN maestro_antecedentesqui q ON q.id = pa.idantqui
							WHERE pa.idpaciente = $id_paciente";
					$r_quirurgicos = $mysqli->query($q_ant_quirurgicos);
					while($row_quirurgicos = $r_quirurgicos->fetch_assoc()){
						$response['ant_quirurgicos'][] = array(
							"id" => $row_quirurgicos['id'],
							"nombre" => $row_quirurgicos['nombre'],
							"fecha" => $row_quirurgicos['fecha']
						);
					}
		$q_ant_familiares="SELECT pa.id, f.nombre, pa.parentesco FROM pac_antecedentesfam pa
							JOIN maestro_antecedentesfam f ON f.id = pa.idantfam
							WHERE pa.idpaciente = $id_paciente";
					$r_familiares = $mysqli->query($q_ant_familiares);
					while($row_familiares = $r_familiares->fetch_assoc()){
						$response['ant_familiares'][] = array(
							"id" => $row_familiares['id'],
							"nombre" => $row_familiares['nombre'],
							"parentesco" => $row_familiares['parentesco']
						);
					}
		$q_vacuna="SELECT pv.id, v.nombre AS nombre, pv.fecha, pv.dosis FROM pac_vacuna pv
					JOIN maestro_vacuna v ON v.id = pv.idvacuna
					WHERE idpaciente = $id_paciente";
					$r_vacunas = $mysqli->query($q_vacuna);
					while($row_vacunas = $r_vacunas->fetch_assoc()){
						$response['vacunas'][] = array(
							"id" => $row_vacunas['id'],
							"nombre" => $row_vacunas['nombre'],
							"fecha" => $row_vacunas['fecha'],
							"dosis" => $row_vacunas['dosis']
						);
					}
		echo json_encode($response);
	}

	function quitar_antecedente(){
		global $mysqli;
		$id = $_POST['id'];
		$tipo = $_POST['tipo'];		
		switch($tipo){
			case "alergias":
				$query = "DELETE FROM pac_alergias WHERE id = $id";
			break;
			case "patologicos":
				$query = "DELETE FROM pac_antecedentesper WHERE id = $id";
			break;
			case "habitos":
				$query = "DELETE FROM pac_habitosper WHERE id = $id";
			break;
			case "quirurgicos":
				$query = "DELETE FROM pac_antecedentesqui WHERE id = $id";
			break;
			case "familiares":
				$query = "DELETE FROM pac_antecedentesfam WHERE id = $id";
			break;
			case "vacunas":
				$query = "DELETE FROM pac_vacuna WHERE id = $id";
			break;
		}
		if(!$mysqli->query($query)){
			$respuesta= array("error"=> true, "mensaje"=>"Ha ocurrido un error al guardar","mysqli_error" => $mysqli->error);
		}else{
			$respuesta= array("exito"=> true, "mensaje"=>"Guardado exitosamente");
		}
		echo json_encode($respuesta);
	}

	function agregar_antecedente(){
		global $mysqli;
		$id = $_POST['id'];
		$id_paciente = $_POST['id_paciente'];
		$tipo = $_POST['tipo'];		
		$fecha = isset($_POST['fecha'])?$_POST['fecha']:'';		
		$dosis = isset($_POST['dosis'])?$_POST['dosis']:'';		
		$parentesco = isset($_POST['parentesco'])?$_POST['parentesco']:'';		
		switch($tipo){
			case "alergias":
				$query = "INSERT IGNORE INTO pac_alergias (idpaciente,idalergia) VALUES ($id_paciente,$id)";
			break;
			case "patologicos":
				$query = "INSERT IGNORE INTO pac_antecedentesper (idpaciente,idenfermedad) VALUES ($id_paciente,$id)";
			break;
			case "habitos":
				$query = "INSERT IGNORE INTO pac_habitosper (idpaciente,idhabitosp) VALUES ($id_paciente,$id)";
			break;
			case "quirurgicos":
				$query = "INSERT IGNORE INTO pac_antecedentesqui (idpaciente,idantqui,fecha) VALUES ($id_paciente,$id,'$fecha')";
			break;
			case "familiares":
				$query = "INSERT IGNORE INTO pac_antecedentesfam (idpaciente,idantfam,parentesco) VALUES ($id_paciente,$id,'$parentesco');";
			break;
			case "vacunas":
				$query = "INSERT IGNORE INTO pac_vacuna (idpaciente,idvacuna,fecha,dosis) VALUES ($id_paciente,$id,'$fecha','$dosis');";
			break;
		}
		if(!$mysqli->query($query)){
			$respuesta= array("error"=> true, "mensaje"=>"Ha ocurrido un error al guardar","mysqli_error" => $mysqli->error,"query"=>$query);
		}else{
			$respuesta= array("exito"=> true, "mensaje"=>"Guardado exitosamente");
		}
		echo json_encode($respuesta);
	}

	function cargar_evaluaciones_medicas(){
		global $mysqli;
		$id_paciente = $_GET['id_paciente'];
		$query="SELECT pe.id,  v.fecha, e.name AS evaluacion,e.test, CONCAT(' ',pe.total) AS total,  re.resultado, re.color  
				FROM pac_evaluaciones pe 
				JOIN evaluaciones e ON e.id = pe.idevaluacion
				JOIN resultado_evaluacion re ON re.id = pe.idresultado
				JOIN pacientesvisitas v ON v.id = pe.idvisita
				WHERE pe.id IN (SELECT max(id) FROM pac_evaluaciones WHERE idpaciente= $id_paciente GROUP BY idevaluacion)";
		$respuesta = array();
		$result = $mysqli->query($query);
		while($row = $result->fetch_assoc()){
			$preguntas = array();
			$q_e_det ="SELECT distinct  pe.texto, rpe.respuesta, pe.posicion FROM pac_evaluaciones_det ped 
					INNER JOIN preguntas_evaluacion pe ON pe.id = ped.idpregunta
					INNER JOIN respuestas_preguntas_evaluacion rpe ON rpe.id = ped.idrespuesta
					WHERE ped.idevaluacion = '".$row['id']."' ORDER BY pe.posicion ASC";
			$re_ev =$mysqli->query($q_e_det);
			while($row_ev = $re_ev->fetch_assoc()){	
				$preguntas[] = array(
					"texto" => $row_ev['texto'],
					"respuesta" => $row_ev['respuesta']
				);
			}
			$respuesta[] = array(
				"id" => $row['id'],
				"evaluacion" => $row['evaluacion'],
				"fecha" => $row['fecha'],
				"test" => $row['test'],
				"total" => $row['total'],
				"resultado" => $row['resultado'],
				"color" => $row['color'],
				"preguntas" => $preguntas
			);
		}
		echo json_encode($respuesta);
	}
	

	function notas_medicos(){
		global $mysqli;
		$id_paciente = $_GET['id_paciente'];
		$respuesta = array();
		$query ="SELECT v.id,
				v.evo_subjetiva, v.evo_objetiva, v.evo_analisis, v.evo_plan, v.fecha, u.nombre AS medico 
				FROM pacientesvisitas v 
				JOIN usuarios u ON u.id = v.idusuario
				WHERE v.idpacientes = $id_paciente ORDER BY v.fecha DESC LIMIT 3";
		$result = $mysqli->query($query);
		while($row = $result->fetch_assoc()){
			$respuesta[] = array(
				"id" => $row['id'],
				"medico" => $row['medico'],
				"fecha" => $row['fecha'],
				"subjetiva" => $row['evo_subjetiva'],
				"objetiva" => $row['evo_objetiva'],
				"analisis" => $row['evo_analisis'],
				"plan" => $row['evo_plan'],
			);
		}
		if(empty($respuesta)){
			$respuesta = array("vacio" => true);
		}
		echo json_encode($respuesta);
	}

	function notas_enfermeria(){
		global $mysqli;
		$id_paciente = $_GET['id_paciente'];
		$respuesta = array();
		$queryenfermedades = "SELECT v.id, v.fecha,u.nombre AS recurso, DATE_FORMAT(n.`created_at`, '%Y-%m-%d %h:%i:%p') as fechan,
							p.nombre AS paciente,IFNULL(n.description,v.comentario) AS comentario 
							FROM nursing_notes n
							INNER JOIN visitas v ON v.id= n.visit_id 
							LEFT JOIN usuarios u ON u.id = v.idusuario 
							LEFT JOIN pacientes p ON p.id = v.idpaciente
							WHERE v.idpaciente = $id_paciente AND u.nivel = 3 GROUP BY v.id ORDER BY v.fecha DESC LIMIT 3";
		//die($queryenfermedades);
		$result = $mysqli->query($queryenfermedades);
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()){
				$respuesta[] = array(
					"id" => $row["id"],
					"recurso" => $row["recurso"],
					"fecha" => $row["fecha"],
					"comentario" => $row["comentario"],
					"fechan" => $row["fechan"],
				);
	
			}
		}
		if(empty($respuesta)){
			$respuesta = array("vacio" => true);
		}
		echo json_encode($respuesta);
	}
	
	function receta_medica(){
		global $mysqli;
		$id_paciente = $_GET['id_paciente'];
		$respuesta = array();
		$query=" SELECT p.id, p.description AS receta, u.nombre AS medico, date(created_at) AS fecha 
				FROM prescriptions p 
				INNER JOIN usuarios u ON u.id = p.user_id 
				WHERE p.patient_id = $id_paciente 
				ORDER BY p.created_at DESC";
		
		$result = $mysqli->query($query);
		while($row = $result->fetch_assoc()){
			$respuesta['data'][]=array(
				"id" => $row["id"],
				"receta" => $row["receta"],
				"medico" => $row["medico"],
				"fecha" => $row["fecha"],
			);
		}
		if(empty($respuesta)){
			$respuesta['data'][]=array(
				"id" =>"",
				"receta" =>"",
				"medico" =>"",
				"fecha" =>"",
			);

		}
		echo json_encode($respuesta);
	}

	function notas_evolucion(){
		global $mysqli;
		$id_paciente = $_GET['id_paciente'];
		$respuesta = array();
		$query="SELECT n.id, n.comentario AS nota, CONCAT(m.nombre,' ',m.apellido) AS medico, date(n.fecha) AS fecha 
		FROM notas_evaluacion n
		INNER JOIN medicos m ON m.id = n.idmedico
		WHERE n.idpaciente = $id_paciente 
		ORDER BY n.fecha DESC";
		
		$result = $mysqli->query($query);
		while($row = $result->fetch_assoc()){
			$respuesta['data'][]=array(
				"id" => $row["id"],
				"nota" => $row["nota"],
				"medico" => $row["medico"],
				"fecha" => $row["fecha"],
			);
		}
		if(empty($respuesta)){
			$respuesta['data'][]=array(
				"id" => "",
				"nota" => "",
				"medico" => "",
				"fecha" => ""
			);
		}
		echo json_encode($respuesta);
	}

	function listar_documentos(){
		global $mysqli;
		$id_paciente = $_GET['id_paciente'] ?? 0;
		$id_hospitalizacion = $_GET['id_hospitalizacion'] ?? 0;
		$inicio = $_GET['inicio'] ?? '';
		$fin = $_GET['fin'] ?? '';
		if($id_paciente !=0 ){
			$where = " idpaciente = '$id_paciente' ";
		}
		if($id_hospitalizacion !=0 ){
			$id_paciente = getValor("paciente","hospitalizacion",$id_hospitalizacion);
			$cod_hospitalizacion = getValor("cod_hospitalizacion","hospitalizacion",$id_hospitalizacion);
			$where = " cod_hospitalizacion = '$cod_hospitalizacion' AND fecha BETWEEN '$inicio' AND '$fin'";
		}
		$query=" SELECT id,fecha,motivo,cod_hospitalizacion,tipodocumento,nombre,extension,mostrar_reporte
					FROM documentospacientes 
					WHERE $where
					AND tipodocumento != 'Resultados de laboratorio'
					ORDER BY fecha DESC ";
		$result = $mysqli->query($query);
		$resultado = array();
		while($row = $result->fetch_assoc()){  	
			$file = getURL('homecare/pacientesbudget/'.$id_paciente.'/' .$row['cod_hospitalizacion']. '/documentos/' .$row['nombre']. '.'.$row['extension']);	
			$resultado['data'][] = array(
				'id'		             =>  $row['id'],
				'fecha'	                 =>  $row['fecha'],
				'motivo'	             =>  $row['motivo'],
				'hospitalizacion'        =>  $row['cod_hospitalizacion'],
				'tipo'	                 =>  $row['tipodocumento'],
				'mostrar_reporte'	                 =>  $row['mostrar_reporte'],
				'file'	=> $file
			);
		}	
		if(empty($resultado)){
			$resultado['data'][] = array(
					'id'		         =>  '',
					'fecha'	             =>  '',
					'motivo'	         =>  '',
					'hospitalizacion'    =>  '',
					'tipo'	             =>  '',
					'mostrar_reporte'	             =>  '',
					'file'    =>  '',
			);		
		}
		echo json_encode($resultado); 
		
	}

	function listar_resultados_laboratorios(){
		global $mysqli;
		$id_paciente = $_GET['id_paciente'] ?? 0;
		$id_hospitalizacion = $_GET['id_hospitalizacion'] ?? 0;
		$inicio = $_GET['inicio'] ?? '';
		$fin = $_GET['fin'] ?? '';
		if($id_paciente !=0 ){
			$where = " idpaciente = '$id_paciente' ";
		}
		if($id_hospitalizacion !=0 ){
			$id_paciente = getValor("paciente","hospitalizacion",$id_hospitalizacion);
			$cod_hospitalizacion = getValor("cod_hospitalizacion","hospitalizacion",$id_hospitalizacion);
			$where = " cod_hospitalizacion = '$cod_hospitalizacion' AND fecha BETWEEN '$inicio' AND '$fin'";
		}
		$query=" SELECT id,fecha,motivo,cod_hospitalizacion,tipodocumento,nombre,extension ,mostrar_reporte
				FROM documentospacientes 
				WHERE $where AND tipodocumento = 'Resultados de laboratorio' ORDER BY fecha DESC ";
		$result = $mysqli->query($query);
		$resultado = array();
		while($row = $result->fetch_assoc()){  	
			$file = getURL('homecare/pacientesbudget/'.$id_paciente.'/' .$row['cod_hospitalizacion']. '/documentos/' .$row['nombre']. '.'.$row['extension']);	
			$resultado['data'][] = array(
				'id'		             =>  $row['id'],
				'fecha'	                 =>  $row['fecha'],
				'motivo'	             =>  $row['motivo'],
				'hospitalizacion'        =>  $row['cod_hospitalizacion'],
				'tipo'	                 =>  $row['tipodocumento'],
				'mostrar_reporte'	                 =>  $row['mostrar_reporte'],
				'file'	=> $file
			);
		}	
		if(empty($resultado)){
			$resultado['data'][] = array(
					'id'		         =>  '',
					'fecha'	             =>  '',
					'motivo'	         =>  '',
					'hospitalizacion'    =>  '',
					'tipo'	             =>  '',
					'mostrar_reporte'	             =>  '',
					'file'    =>  '',
			);		
		}
		echo json_encode($resultado); 
		
	}

	function subir_documento(){
			global $mysqli;
			global $mail;
			$tipodocumento = (isset($_POST['tipodocumento'])) ? $_POST['tipodocumento'] : "EF";
			$fechadocumento = (isset($_POST['fecha'])) ? $_POST['fecha'] : getFechaServidor();
			$motivodocumento = (isset($_POST['motivo'])) ? $_POST['motivo'] : '';		
			$id_paciente = (isset($_POST['id_paciente'])) ? $_POST['id_paciente'] : '';		
			$hospitalizacion  = (isset($_POST['$hospitalizacion '])) ? $_POST['$hospitalizacion '] : '';		
			$respuesta  = array();
			if (!empty($_FILES)) {
				if(!isset($_POST['cod_hospitalizacion']) || $hospitalizacion == ""){
					$queryhospitalizacion = "SELECT cod_hospitalizacion FROM hospitalizacion WHERE paciente = '$id_paciente'  AND (estatus='activa' OR fin_administrativo = 0) LIMIT 1";
					$result = $mysqli->query($queryhospitalizacion);
					if($result->num_rows == 1){
						$hospitalizacion = $result->fetch_assoc()['cod_hospitalizacion'];
					}else{
						$queryhospitalizacion = "SELECT cod_hospitalizacion FROM hospitalizacion WHERE paciente = '$id_paciente' ORDER BY id DESC LIMIT 1";
						$result = $mysqli->query($queryhospitalizacion);
						if($result->num_rows == 1){
							$hospitalizacion = $result->fetch_assoc()['cod_hospitalizacion'];
						}else{
							$hospitalizacion ="NO_HOSP";
						}
					}
				}
				if($hospitalizacion != ''){
					$tempFile = $_FILES['file']['tmp_name'];          	  
					$nuevoNombre = $_FILES['file']['name'];
					$partes_ruta = pathinfo($nuevoNombre);
					$extension =  $partes_ruta['extension'];
					$nombreArchivo = rand(1000, 1100000);
					$q = "INSERT INTO documentospacientes (cod_hospitalizacion, idpaciente , tipodocumento, fecha, motivo, nombre, extension) VALUES ('$hospitalizacion', '$id_paciente','$tipodocumento', '$fechadocumento', '$motivodocumento','$nombreArchivo','$extension')";
					if($mysqli->query($q)){
						$s3 = uploadFile("homecare/pacientesbudget/". $id_paciente . "/" . $hospitalizacion . "/documentos/",$nombreArchivo.'.'.$extension);
						$respuesta = array(			
							"id" => $mysqli->insert_id,		
							"mensaje" => "Documento cargado exitosamente",						
						);
					} else {
						$respuesta = array(
							"error" => true,
							"mensaje" => "Ha ocurrido un error al guardar el documento",
							"mysqli_error" => $mysqli->error,
							"query" => $q
						);
					}
				}else{
					$respuesta = array(
						"error" => true,
						"mensaje" => "Paciente no posee hospitalizacion",
					);
				}			
			}else{
				$respuesta = array(
					"error" => true,
					"mensaje" => "No se ha conseguido un documento para cargar"
				);
			}
			echo json_encode($respuesta);
	}

	function eliminar_documento(){
		global $mysqli;
		$id = $_POST['id'];
		$respuesta = array();
		$q = "DELETE FROM documentospacientes WHERE id = $id";
		if($mysqli->query($q)){
			$respuesta = array("mensaje"=>"Documento eliminado exitosamente");
		}else{
			$respuesta = array(
				"error"=> true,
				"mensaje"=>"Ha ocurrido un error al eliminar el documento",
				"mysqli_error"=> $mysqli->error
			);
		}
		echo json_encode($respuesta);
	}


	
	function signos_vitales(){
		global $mysqli;
		$idpaciente = isset($_REQUEST['idpaciente'])?$_REQUEST['idpaciente']:0;	
		
		$fecha_inicio = isset( $_REQUEST['fecha_inicio'] )?$_REQUEST['fecha_inicio']: "";
		$fecha_fin = isset($_REQUEST['fecha_fin'])?$_REQUEST['fecha_fin']:'';
		$empresa = isset($_COOKIE['empresa'])?$_COOKIE['empresa']:0; 
		$organizacion = isset($_COOKIE['organizacion'])?$_COOKIE['organizacion']:0;
		$nivel_usuario = $_SESSION['nivel'];
		$id_usuario = $_SESSION['user_id'];
		$resultado = array();
		$arrCategorias = array();
		$arrFrecuenciaCardiaca = array();
		$arrFrecuenciaRespiratoria = array();
		$arrOximetria = array();
		$arrSistolica = array();
		$arrDiastolica = array();
		$arrTemperatura = array();
		$arrDolor = array();
		$arrGlicemia = array();
		
		$q = "SELECT max(v.id) as idvisita,  p.id as id, p.condicion_id as condicion		
				FROM pacientes p
				INNER join visitas v ON p.id = v.idpaciente	
				LEFT JOIN empresas emp ON emp.id = p.empresa_id
				LEFT JOIN organizaciones o ON o.id = emp.idorganizacion
				LEFT JOIN paciente_usuario pu ON pu.paciente_id = p.id																		
				WHERE 1 ";
				
		if ($nivel_usuario == 2 && $nivel_usuario == 5){
			$idmedico = $mysqli->query("Select id from medicos where idusuario = $id_usuario")->fetch_assoc()['id'];
			$q .= " AND (pu.usuario_id = '$id_usuario' OR p.idmedicotratante = $idmedico )";
		} else{
			if( $empresa != 0 ){
				$q .= " AND p.empresa_id = '".$empresa."' ";
			}else{
				if($organizacion != 0){
					$q .= " AND o.id = '".$organizacion."' ";
				}
			}		
		}
		if($idpaciente != 0 && $idpaciente != -1){
			$q.="AND p.id = '$idpaciente' ";				
			if($fecha_inicio != ''){
				if ($fecha_fin != ''){
					$q.=" AND v.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' ";
				}else{
					$q.=" AND v.fecha >= '$fecha_inicio' ";
				}
			}					
			$q.=" group by v.id ORDER BY v.fecha desc";			
		}
		$q .= " limit 6";
		$res = $mysqli->query($q);
		while ($ro = $res->fetch_assoc()){
			$idvisita = $ro['idvisita'];
			$query = "
				SELECT distinct(p.nombre) as paciente, p.condicion_id as condicion,	v.fecha as fecha,p.id as idpaciente, 
				IFNULL((SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=1 AND t.visit_id='$idvisita' LIMIT 1) ,'') as frecuenciacardiaca,
				resultado_examen('Frecuencia cardiaca',IFNULL(p.fechanac,now()),(SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=1 AND t.visit_id='$idvisita' LIMIT 1) ) as resultado_frecuenciacardiaca,
				IFNULL( (SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=2 AND t.visit_id='$idvisita' LIMIT 1),'') as frecuenciarespiratoria,
				resultado_examen('Frecuencia respiratoria',IFNULL(p.fechanac,now()), (SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=2 AND t.visit_id='$idvisita' LIMIT 1) ) as resultado_frecuenciarespiratoria,
				IFNULL((SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=3 AND t.visit_id='$idvisita' LIMIT 1) ,'') as oximetria,
				resultado_examen('Saturación de oxígeno',IFNULL(p.fechanac,now()), (SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=3 AND t.visit_id='$idvisita' LIMIT 1)) as resultado_oximetria,
				IFNULL((SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=4 AND t.visit_id='$idvisita' LIMIT 1),'') as sistolica,
				resultado_examen('Presión sistolica',IFNULL(p.fechanac,now()),(SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=4 AND t.visit_id='$idvisita' LIMIT 1) ) as resultado_sistolica,
				IFNULL((SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=5 AND t.visit_id='$idvisita' LIMIT 1),'') as diastolica,
				resultado_examen('Presión diastolica',IFNULL(p.fechanac,now()),(SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=5 AND t.visit_id='$idvisita' LIMIT 1) ) as resultado_diastolica,
				IFNULL((SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=6 AND t.visit_id='$idvisita' LIMIT 1) ,'') as temperatura,
				resultado_examen('Temperatura',IFNULL(p.fechanac,now()),(SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=6 AND t.visit_id='$idvisita' LIMIT 1  )) as resultado_temperatura,
				IFNULL((SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=7 AND t.visit_id='$idvisita' LIMIT 1  ),'') as dolor,
				resultado_examen('Dolor',IFNULL(p.fechanac,now()), (SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=7 AND t.visit_id='$idvisita' LIMIT 1  ) ) as resultado_dolor,
				IFNULL((SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=8 AND t.visit_id='$idvisita' LIMIT 1  ),'') as glicemia,
				resultado_examen('Glicemia capilar',IFNULL(p.fechanac,now()), (SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=8 AND t.visit_id='$idvisita' LIMIT 1  ) ) as resultado_glicemia,
				IFNULL((SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=11 AND t.visit_id='$idvisita' LIMIT 1  ) ,'') as estatura,
				IFNULL((SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=11 AND t.visit_id='$idvisita' LIMIT 1  ) ,'') as peso,
				IFNULL(v.imc,'') as imc,
				resultado_examen('IMC',IFNULL(p.fechanac,now()),v.imc) as resultado_imc,
				IFNULL(resultado_examen_valor('Frecuencia cardiaca',p.fechanac,IFNULL((SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=1 AND t.visit_id='$idvisita' LIMIT 1),0)),'baja') as condicion_cardiaca,
				IFNULL(resultado_examen_valor('Frecuencia respiratoria',p.fechanac,IFNULL((SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=2 AND t.visit_id='$idvisita' LIMIT 1),0)),'baja') as condicion_respiratoria,
				IFNULL(resultado_examen_valor('Saturación de oxígeno',p.fechanac,IFNULL((SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=3 AND t.visit_id='$idvisita' LIMIT 1),0)),'baja') as condicion_oximetria,
				IFNULL(resultado_examen_valor('Presión sistolica',p.fechanac,IFNULL((SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=4 AND t.visit_id='$idvisita' LIMIT 1),0)),'baja') as condicion_sistolica,
				IFNULL(resultado_examen_valor('Presión diastolica',p.fechanac,IFNULL((SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=5 AND t.visit_id='$idvisita' LIMIT 1),0)),'baja') as condicion_diastolica, 
				IFNULL(resultado_examen_valor('Temperatura',p.fechanac,IFNULL((SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=6 AND t.visit_id='$idvisita' LIMIT 1),0)),'baja') as condicion_temp,
				IFNULL(resultado_examen_valor('Dolor',p.fechanac,IFNULL((SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=7 AND t.visit_id='$idvisita' LIMIT 1),0)),'baja') as condicion_dolor,
				IFNULL(resultado_examen_valor('Glicemia capilar',p.fechanac,IFNULL((SELECT DISTINCT t.value FROM visit_vital_sign t WHERE t.vital_sign_id=8 AND t.visit_id='$idvisita' LIMIT 1),0)),'baja') as condicion_glicemia
				FROM visitas v 
				LEFT JOIN pacientes p ON p.id = v.idpaciente
				LEFT JOIN visit_vital_sign vs ON v.id = vs.visit_id
				where v.id = '$idvisita' AND vs.id is not null
			";
			
			
			$query.=" 
				GROUP BY p.id
				ORDER BY p.id DESC
			";
			
			$r 	= $mysqli->query($query);
			while($row= $r->fetch_assoc()){	
				if($row['condicion'] == 1){
					$condicion = '<span class="icon-col fa fa-heartbeat green" data-toggle="tooltip" data-original-title="Paciente estable" data-placement="left"></span>';
				}
				elseif($row['condicion'] == 2){
					$condicion = '<span class="icon-col fa fa-heartbeat yellow" data-toggle="tooltip" data-original-title="Paciente requiere atención"  data-placement="left"></span>';
				}elseif($row['condicion'] == 3){
					$condicion = '<span class="icon-col fa fa-heartbeat red" data-toggle="tooltip" data-original-title="Paciente requiere atención inmediata"  data-placement="left"></span>';
				}else{
					$condicion = '<span class="icon-col fa fa-heartbeat blue" data-toggle="tooltip" data-original-title="Valor no registrado"  data-placement="left"></span>';
				}
				
				$resultado['data'][] = array(
					'id'	=>  $idvisita,
					'acciones'	=>  "<div style='float:left;margin-right:10px;' class='ui-pg-div ui-inline-custom'>
										<span class='icon-col blue fa fa-eye ver-examenfisicoPacSolo' data-id='".$idvisita."'data-toggle='tooltip' data-original-title='Ver valores' data-placement='right'></span>
										<span class='icon-col blue fa fa-folder-open boton-historia ver-historial-p' data-id='".$ro['id']."' data-toggle='tooltip' data-original-title='Ir al historial' data-placement='right'></span>
										<span class='icon-col green fa fa-print boton-reporte-diario1 ver-reporte-diario' data-id='".$ro['id']."' data-fecha='".$row['fecha']."' data-toggle='tooltip' data-original-title='Imprimir reporte diario' data-placement='right'></span>
									 </div>",
					'nombre'	=>  $row['paciente'],
					'condicion'	=>  $condicion,
					'riesgo'	=>  $row['idpaciente'],
					'fecha'	    =>  $row['fecha'],
					'idvisita'	=>  $idvisita,
					'fc'		=>	$row['frecuenciacardiaca'],
					'rfc'		=>  $row['resultado_frecuenciacardiaca'],
					'fr'		=>	$row['frecuenciarespiratoria'],
					'rfr'     	=>  $row['resultado_frecuenciarespiratoria'],
					'so'		=>	$row['oximetria'],
					'rox'		=>  $row['resultado_oximetria'],
					'paa'		=>	$row['sistolica'],
					'rsi'		=>  $row['resultado_sistolica'],
					'pab'		=>	$row['diastolica'],
					'rdi'		=>  $row['resultado_diastolica'],
					'tc'		=>	$row['temperatura'],
					'rtm'		=>  $row['resultado_temperatura'],
					'dolor'		=>	$row['dolor'],
					'rdl'		=>  $row['resultado_dolor'],
					'gc'		=>	$row['glicemia'],
					'rgc'		=>  $row['resultado_glicemia'],
					'e'			=>	$row['estatura'],
					'peso'		=>	$row['peso'],
					'imc'		=>	$row['imc'],
					'rim'		=>  $row['resultado_imc'],
					'condicion_cardiaca' => $row['condicion_cardiaca'],
					'condicion_respiratoria' => $row['condicion_respiratoria'],
					'condicion_oximetria' => $row['condicion_oximetria'],
					'condicion_sistolica' => $row['condicion_sistolica'],
					'condicion_diastolica' => $row['condicion_diastolica'],
					'condicion_temp' => $row['condicion_temp'],
					'condicion_dolor' => $row['condicion_dolor'],			
					'condicion_glicemia' => $row['condicion_glicemia']			
				);		
                // se carga la información para la gráfica
                if($idpaciente != 0){
                    $arrCategorias[] = $row['fecha'];
                }else{
                    $arrCategorias[] = $row['paciente'];
                }
                $arrIdVisita[] =  $idvisita;
                $arrFrecuenciaCardiaca[] = array(
                    'id' => $idvisita,
                    'y' =>  $row['frecuenciacardiaca'],
                );
                $arrFrecuenciaRespiratoria[] = array(
                    'id' => $idvisita,
                    'y'  => $row['frecuenciarespiratoria']
                );
                $arrOximetria[] = array(
                    'id' => $idvisita,
                    'y'  => $row['oximetria']
                );
                $arrSistolica[] = array(
                    'id' => $idvisita,
                    'y'  => $row['sistolica']
                );
                $arrDiastolica[] = array(
                    'id' => $idvisita,
                    'y'  => $row['diastolica']
                );
                $arrTemperatura[] = array(
                    'id' => $idvisita,
                    'y'  => $row['temperatura']
                );
                $arrDolor[] = array(
                    'id' => $idvisita,
                    'y'  => $row['dolor']
                );
                $arrGlicemia[] = array(
                    'id' => $idvisita,
                    'y'  => $row['glicemia']
                );
                
			}
		}
        $series = array(
            'arrFrecuenciaCardiaca' => array( 
                                'data' => $arrFrecuenciaCardiaca,
                                'name' => 'Frec. Cardíaca',
                                'color' => '#D50000'
                            ),
            'arrFrecuenciaRespiratoria'=> array( 
                                'data' => $arrFrecuenciaRespiratoria,
                                'name' => 'Frec. Respiratoria',
                                'color' => '#AA00FF'
                            ),
            'arrOximetria' => array( 
                                'data' => $arrOximetria,
                                'name' => 'Sat. Oxigeno',
                                'color' => '#304FFE',
                            ),
            'arrSistolica' => array( 
                                'data' => $arrSistolica,
                                'name' => 'Sistólica',
                                'color' => '#2962FF',
                            ),
            'arrDiastolica' => array( 
                                'data' => $arrDiastolica,
                                'name' => 'Diastólica',
                                'color' => '#00BFA5',
                            ),
            'arrTemperatura'=> array( 
                                'data' => $arrTemperatura,
                                'name' => 'Temperatura',
                                'color' => '#00C853',
                            ),
            'arrDolor' => array( 
                                'data' => $arrDolor,
                                'name' => 'Nivel de Dolor',
                                'color' => '#AEEA00',
                            ),
            'arrGlicemia' => array(  
                                'data' => $arrGlicemia,
                                'name' => 'Glicemia Capilar',
                                'color' => '#FFAB00',
                            )
        );
		if(!empty($resultado)){
            $resultado['grafica'] = array(
                'series' => $series,
                'arrCategorias' => $arrCategorias
            );
		}else{
			$resultado = array(
				'data' => array(
					'id' => '',
					'acciones' => '',
					'nombre' => '',
					'condicion' => '',
					'riesgo' => '',
					'fecha' => '',
					'idvisita' => '',
					'fc' => '',
					'rfc' => '',
					'fr' => '',
					'rfr' => '',
					'so' => '',
					'rox' => '',
					'paa' => '',
					'rsi' => '',
					'pab' => '',
					'rdi' => '',
					'tc' => '',
					'rtm' => '',
					'dolor' => '',
					'rdl' => '',
					'gc' => '',
					'rgc' => '',
					'e' => '',
					'peso' => '',
					'imc' => '',
					'rim' => '',
					'condicion_cardiaca' => '',
					'condicion_respiratoria' => '',
					'condicion_oximetria' => '',
					'condicion_sistolica' => '',
					'condicion_diastolica' => '',
					'condicion_temp' => '',
					'condicion_dolor' => '',
					'condicion_glicemia' => ''
				),
				'arrCategoria' => $arrCategorias,
				'series' =>  $series 
			);
		}
		echo json_encode($resultado);
	}

	function editar_documentos(){
		global $mysqli;
		$id = $_POST['id'];
		$fecha = $_POST['fecha'];
		$motivo= $_POST['motivo'];
		$resultado = array();
		$query ="UPDATE documentospacientes SET fecha ='$fecha', motivo='$motivo' WHERE id = '$id'";
		if($mysqli->query($query)){
			$resultado = array("mensaje"=>"Registro actualizado exitosamente");
		}else{
			$resultado = array("mensaje"=>"ha ocurrido un error al actualizar registro", "error"=>true);
		}
		echo json_encode($resultado);
	}

	function cargar_alertas_sv(){
		global $mysqli;
		$id_paciente = $_GET['id_paciente'];
		$query_alertas ="SELECT vsa.id, u.nombre AS usuario, vs.name AS signo_vital, vsa.minimum_value AS valor_minimo,
					vsa.maximum_value AS valor_maximo, vsa.updated_at AS fecha_actualizacion,vsa.vital_sign_id as id_signo_vital
					FROM vital_sign_alerts vsa
					JOIN vital_signs vs ON vs.id = vsa.vital_sign_id
					JOIN usuarios u ON u.id = vsa.user_id
					WHERE  vsa.patient_id = $id_paciente order by vsa.vital_sign_id asc";
		$result_alertas = $mysqli->query($query_alertas);
        $respuesta = array();
		if($result_alertas->num_rows >=1){
            while($row_alertas = $result_alertas->fetch_assoc()){
                $respuesta['data'][] = $row_alertas;
            }
        }
		if(empty($respuesta)){
			$respuesta['data']= array();
		}
		echo json_encode($respuesta);
	}

?>