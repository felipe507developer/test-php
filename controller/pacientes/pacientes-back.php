 <?php

use LDAP\Result;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

    include("../conexion.php");
    include("../conexion_alegra.php");
    include("../conexion_s3.php");
	//				  
	$oper = '';
	if (isset($_REQUEST['oper'])){
        try {
            $oper = $_REQUEST['oper'];
            $oper();
            mysqli_close($mysqli);
        } catch (\Throwable $th) {
            $respuesta = array(
                'error' => true,
                'respuesta' => 'No existe la función'
            );
            echo json_encode($respuesta);
        }
    } else {
        $respuesta = array(
            'error' => true,
            'respuesta' => 'Operador no definido'
        );
        echo json_encode($respuesta);
    }
	
    function pacientes(){
        global $mysqli;
        $id_usuario = $_SESSION['user_id'];
        $empresa = isset($_COOKIE['empresa'])?$_COOKIE['empresa']:0;
		$organizacion = isset($_COOKIE['organizacion'])?$_COOKIE['organizacion']:0;
		$estado = isset($_GET['estado'])?$_GET['estado']:0;
		$listado = isset($_GET['listado'])?$_GET['listado']:0;
		$nivel_usuario = $_SESSION['nivel'];        
        $resultado = array();
		$query="SELECT 
					p.id, 
					p.nombre,
					p.cedula,
					p.fecha_nacimiento,
					p.sexo,
					p.correo,
					p.telefono,
					p.estado
				FROM pacientes p ";
                
		$result = $mysqli->query($query);
		while($row = $result->fetch_assoc()){
			$resultado['data'][] = $row;
		}
        
		echo json_encode($resultado);
    }
    function get_paciente(){
        global $mysqli;
        $id = $_GET['id'];
        $resultado = array();
		$query="SELECT 
					p.id, 
					p.nombre,
					p.cedula,
					p.fecha_nacimiento,
					p.sexo,
					p.correo,
					p.telefono,
					p.estado
				FROM pacientes p 
				WHERE p.id = $id  GROUP BY p.id ";
		//debug($query);
		
		$result = $mysqli->query($query);
		if($row = $result->fetch_assoc()){
			$resultado = $row;
		}
		echo json_encode($resultado);
    }

	function buscarCedula(){
		global $mysqli;
		$cedula = $_GET['cedula'];
		$respuesta = array();
		$query = "SELECT cedula FROM pacientes where cedula = '$cedula'";
		if($result = $mysqli->query($query)){
			if($result->num_rows > 0){
				$respuesta = array(
					"error" => true,
					"mensaje" => "Cédula registrada para otro paciente"
				);
			} else {
				$respuesta = array(
					"success" => true,
					"mensaje" => "Cédula no pertenece a ningún paciente"
				);
			}
		}else{
			$respuesta = array(
				"error" => true,
				"mensaje" => "ha ocurrido un error al consultar Cédula",
				"mysql_error" => $mysqli->error,
				"query" => $query
			);

		}

		echo json_encode($respuesta);
	}

	function nuevo_paciente(){
		global $mysqli;		
		$cedula = $_POST['cedula'];
		$nombre = $_POST['nombre'];
		$fecha_nacimiento = $_POST['fecha_nacimiento'];
		$telefono = $_POST['telefono'];
		$sexo = $_POST['sexo'];
		$correo = $_POST['correo'];
		
		//guardo los datos de paciente 
		$q_paciente = "INSERT INTO pacientes(
								cedula,
								nombre,
								fecha_nacimiento,
								telefono,
								sexo,
								correo
							) VALUES (
								'$cedula',
								'$nombre',
								'$fecha_nacimiento',
								'$telefono',
								'$sexo',
								'$correo'
							)";
		if($mysqli->query($q_paciente)){
			$id_paciente = $mysqli->insert_id;
			$respuesta = array(
				"success"=> true,
				"mensaje" => "Paciente creado exitosamente",
				"id" => $id_paciente
			);
			
		}else{
			$respuesta = array(
				"error"=> true,
				"mensaje" => "Ha ocurrido un error al guardar datos del paciente",
				"mysqli_error" => $mysqli->error,
				"query" => $q_paciente
			);
		}
		echo json_encode($respuesta);
	}

	function editar_paciente(){
		global $mysqli;		
		$id_paciente = $_POST['id_paciente'];
		$cedula = $_POST['cedula'];
		$nombre = $_POST['nombre'];
		$fecha_nacimiento = $_POST['fecha_nacimiento'];
		$telefono = $_POST['telefono'];
		$correo = $_POST['correo'];
		$sexo = $_POST["sexo"];
		$q_paciente = "UPDATE pacientes SET 
							cedula = '$cedula',
							nombre = '$nombre',
							fecha_nacimiento = '$fecha_nacimiento',
							telefono = '$telefono',
							sexo = '$sexo',
							correo = '$correo'
						WHERE id = '$id_paciente'";
		if($mysqli->query($q_paciente)){
			$respuesta = array(
				"success"=> true,
				"mensaje" => "Paciente editado exitosamente",
				"id" => $id_paciente
			);
		}else{
			$respuesta = array(
				"error"=> true,
				"mensaje" => "Ha ocurrido un error al guardar datos del paciente",
				"mysqli_error" => $mysqli->error,
				"query" => $q_paciente
			);
		}
		echo json_encode($respuesta);
	}

	function cambiar_estado(){
		global $mysqli;
		$id_paciente = $_POST['id_paciente'];
		$id_estado= $_POST['id_estado'];
		$query = "UPDATE pacientes SET estado_paciente = $id_estado WHERE id = $id_paciente";
		$respuesta = array();
		if($mysqli->query($query)){
			$respuesta = array(
				"mensaje" => "Estado de paciente actualizado"
			);
		}else{
			$respuesta = array(
				"error"=> true,
				"mensaje" => "Ha ocurrido un error al actualizar el estado del paciente"
			);
		}
		echo json_encode($respuesta);
	}

	function check_cedula(){
	    global $mysqli;
	    $cedula = $_REQUEST['cedula'];
	    $q = "SELECT cedula FROM pacientes WHERE cedula = '$cedula' LIMIT 1";
	    $r = $mysqli->query($q);
	    if($r->num_rows > 0){
	        echo 0;
	    } else {
	        echo 1;
	    }
		mysqli_close($mysqli);
	}

	function check_cedula_editar(){
	    global $mysqli;
	    $cedula = $_REQUEST['cedula'];
		$id = $_REQUEST['id'];

	    $q = "SELECT id FROM pacientes WHERE cedula = '$cedula' LIMIT 1";
	    $r = $mysqli->query($q);
		$row = $r->fetch_assoc();
		if($id == $row['id']){
			echo 1;
		}else{
			if($r->num_rows > 0){
				// EXISTE
				echo 0;
			} else {
				// NO EXISTE
				echo 1;
			}
		}
		mysqli_close($mysqli);
	}
	
?>