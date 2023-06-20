<?php
    include('../conexion.php');
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

    function login(){
        
        global $mysqli;
        $datos = json_decode(file_get_contents('php://input'),true);
        $usuario = $datos['usuario'];
        $clave = $datos['clave'];

        $stmt = $mysqli->prepare("SELECT id,nombre From usuarios WHERE usuario =? AND clave = ?");
        $stmt->bind_param("ss",$usuario,$clave);
        $stmt->execute();
        $registro = $stmt->get_result()->fetch_all(MYSQLI_ASSOC)[0];
        $stmt->close();
        if($registro){
            
            session_start(['cookie_lifetime' => 86400,]);
            $_SESSION['usuario'] = $usuario;

            return print(json_encode($registro));
        }else{
            return print(
                json_encode(
                    array(
                        "error"=>true,"mensaje"=>"Conbinación usuario y clave invalidos"
                    )
                )
            );
        }
    }

 
?>
