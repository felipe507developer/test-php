<?php		
									

include_once("../conexion.php");
include("../mpdf/mpdf.php");
	//ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
	$id =(!empty($_REQUEST['id']) ? $_REQUEST['id'] : 0);
	
	global $mysqli;	
    $query = "SELECT p.id, p.description AS receta, CONCAT(m.nombre,' ',m.apellido) AS medico,user_id,DATE_FORMAT(p.created_at, '%Y-%m-%d %h:%i:%p') created_at, u.firma, 
              pa.nombre AS paciente, pa.cedula, pa.fechanac, u.correo, u.telefono, m.logo,
              (CASE WHEN m.sexo = 'M' THEN 'Dr.' ELSE 'Dra.' END) AS prefijo,
              TIMESTAMPDIFF(YEAR, pa.fechanac, CURRENT_DATE) AS edad,GROUP_CONCAT(e.nombre) AS especialidades 
              FROM prescriptions p 
              INNER JOIN pacientes pa ON pa.id = p.patient_id
              INNER JOIN usuarios u ON u.id = p.user_id 
              INNER JOIN medicos m ON m.idusuario = u.id
              LEFT JOIN especialidades e ON FIND_IN_SET(e.id, m.idespecialidad)
            where p.id = $id";
    $result = $mysqli->query($query);
    $row = $result->fetch_assoc();
    $logo = '<div style="color:#3a5854;font-size: 50px; text-align: center; font-family: "Great Vibes"; " id="doctor">'.$row['prefijo'].''.$row['medico'].'</div>';
    if($row['logo'] != ''){
      $logo = '<div  id="logo"><img src="'.$row['logo'].'"></div>';
    }
    
    $html .= '
          <div id="body">
            '.$logo.'
            <div style="color:#3a5854;font-size: 15px; text-align: center;" id="esp">'.$row['especialidades'].'</div>
        
            <div id="all"
              style="display: flex; margin-top: 10px;">
              <div id="correo" style="color:#3a5854;margin-left:0px;">
                Correo:
                <span style="border-bottom-width: 1px; border-bottom-style: solid; padding-right: 10px;">'.$row['correo'].'</span>
              </div>
              <div style="color:#3a5854;margin-left:360px; margin-top: -25px;">
                Teléfono:
                <span
                  style="border-bottom-width: 1px; border-bottom-style: solid; padding-right: 10px;">'.$row['telefono'].'</span>
              </div>
              <div style="color: #f7685b; margin-left:580px; margin-top: -26px;">N° '.$row['id'].'</div>
            </div>
            <div id="divider-line"
              style="align-self: center; width: 650px; border-width: 2px; border-color: green; border-top-style: dashed; margin-top: 20px">
            </div>
        
            <div id="paciente"
              style="color:#3a5854;display: flex;flex-direction: row; justify-content: space-between; margin-top: 20px">
              <span>
                Nombre:
                <span
                  style="border-bottom-width: 1px; border-bottom-style: solid; padding-right: 10px; text-transform: capitalize;">'.$row['paciente'].'</span>
              </span>
              <div style="margin-left:480px; margin-top: -22px;">
                Cédula:
                <span
                  style="border-bottom-width: 1px; border-bottom-style: solid; padding-right: 10px;">'.$row['cedula'].'</span>
              </div>
        
            </div>
            <div id="paciente2" style="color:#3a5854;display: flex;flex-direction: row; justify-content: space-between; margin-top: 20px">
              <span style="width: 100px;">
                Edad:
                <span
                  style="border-bottom-width: 1px; border-bottom-style: solid; padding-right: 10px;">'.$row['edad'].'</span>
              </span>
              <div style="color:#3a5854;margin-left:400px; margin-top: -25px;"> Fecha de Nac.:
                <span style="border-bottom-width: 1px; border-bottom-style: solid; padding-right: 10px;">
                '.$row['fechanac'].'</span>
              </div>
            </div>
            <div id="date" style="color:#3a5854;display: flex;flex-direction: row; justify-content: space-between; margin-top: 20px">
            <br><br>
              <span>
                Fecha:
                <span style="border-bottom-width: 1px; border-bottom-style: solid; padding-right: 10px;">'.$row['created_at'].'</span>
              </span>
              <span></span>
            </div>
            <div id="prescription-container" >
              <span style="color:#3a5854;font-size: 40px">R<sub>x</sub></span>
              <p>'.str_replace(array("\r\n", "\n\r", "\r", "\n"), "<br />", $row['receta']).'</p>
            </div>
            <br><br>
            <div id="sign-container" style="display:flex; flex-direction: row-reverse; margin: 20px 0 0 0">
              <div style="color:#3a5854;margin-left:300px;display: flex; justify-content: center; align-items: center">
                <span>
                  Firma:
                </span>
                <span style="padding-right: 10px;"> <img style="width: 250px;" src="'.$row['firma'].'" /> </span>
              </div>
              <div style="flex: 0.8;">
              </div>
        
            </div>
          </div>
          ';

		

		$mpdf=new mpdf();
   // die($html);
		$mpdf->mirrorMargins = true;
		$mpdf->SetDisplayMode('fullpage','two');	
		$mpdf->addPage('P');
  
	 
			
    
    $mpdf->WriteHTML('<style>@import url("https://fonts.googleapis.com/css?family=Great Vibes");
    #body {
        width: 700px; height: 1000px; display: flex;flex-direction: column; padding-left: 30px; padding-top: 30px; background-color: #fff
    }
    #logo {
        margin-left: auto;
        margin-right: auto;
        display: block;
        width: 550px;
    }
    #doctor {
        font-size: 50px; text-align: center; font-family: "Great Vibes"; 
    }
    #all,#paciente,#paciente2,#date,#prescription-container,#sign-container,#esp {
        font-family: "helvetica"; 
        font-size: 20px;
    }</style>'.$html);
    // $mpdf->WriteHTML($html,2);			  

    // die('<style>'.$stylesheet.'</style>'.$html);
		$mpdf->Output();
  
  
		exit;  
  
?>