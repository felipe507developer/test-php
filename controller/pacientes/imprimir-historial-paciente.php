<?php 
	// ini_set("display_errors", 1); ini_set("display_startup_errors", 1); error_reporting(E_ALL);

    include("../conexion_s3.php");
    require_once('../PDFMerger/PDFMerger.php');
    use PDFMerger\PDFMerger;
use PhpOffice\PhpSpreadsheet\Reader\Xls\RC4;

	include_once("../conexion.php");
	include_once("../conexion_s3.php");
    include("../mpdf/mpdf.php"); 
    global $mysqli;
    setlocale(LC_ALL,"es_ES");
    $id_paciente = $_GET['id_paciente'];
    $inicio = $_GET['inicio'];
    $fin = $_GET['fin'];
    $arreglo_color = array(
        "1"=>["#FADBD8","#F1948A","#E74C3C"],
        "2"=>["#E8DAEF","#BB8FCE","#7D3C98"],
        "3"=>["#D5F5E3","#82E0AA","#2ECC71"],
        "4"=>["#D0ECE7","#73C6B6","#16A085"],
        "5"=>["#D1F2EB","#76D7C4","#1ABC9C"],
        "6"=>["#D6EAF8","#85C1E9","#3498DB"],
        "7"=>["#D4E6F1","#7FB3D5","#2980B9"],
        "8"=>["#FCF3CF","#F7DC6F","#F1C40F"],
    );
    // Configuración de cabecera de reporte 
    $conf = json_decode(utf8_decode($_COOKIE['configuraciones']));
    $logo = $conf->ruta_logo;
    $logo_2 = $conf->ruta_logo_2;
    $vitae = '';
    $moneda = $conf->moneda;
    $impuesto= $conf->nombre_impuesto;
    $vitae = $conf->nombre.'<br>';
    $vitae.= $conf->tipo_identificacion.': '.$conf->identificacion.'<br>';
    $vitae.= $conf->direccion.'<br>';
    $vitae.= 'Telefono: '.$conf->telefono.'<br>';
    $vitae.= 'Whatsapp: '.$conf->whatsapp;
    //configuración por región
    $txt_hospitalizacion ='Hospitalización';
    if($_COOKIE['region'] == 'VIV Care')
        $txt_hospitalizacion = "Evento";
    $html = '<!-- DEFINE HEADERS & FOOTERS -->
                <style>
                    h1,h2,h3,h4,h5,h6,a,p,div,span,li,td,th {
                        font-family: Arial, sans-serif;
                    }
                    table{
                        border-collapse: collapse;
                    }
                    .notas{
                        font-family: Arial, sans-serif;
                        text-align: justify;
                    }
                    td {
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                        text-align: justify;
                        border: 1
                    }
                    .campo {
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                        text-align: left !important;
                    }
                    h1,h2,h3,h4,h5,h6 {
                        /*color: rgb(21, 98, 158);*/
                        text-align:center;
                        margin-top: 5px;
                        margin-bottom: 5px;
                    }
                    .blue {
                        color: rgb(21, 98, 158);
                    }
                    th {
                        font-size: 12px;
                        background-color: #0a5897;
                        color: white
                    }									
                    .center {
                        text-align:center;
                    }
                    .cabecera{					
                        border: 0				
                    }
                    * {
                        box-sizing: border-box;
                      }
                      
                      /* Create two equal columns that floats next to each other */
                      .column {
                        float: left;
                        width: 50%;
                        height: auto
                      }
                      
                      /* Clear floats after the columns */
                      .row:after {
                        content: "";
                        display: table;
                        clear: both;
                      }
                </style>	
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>';
                
    if($empresa_id == '7' && $_COOKIE['region'] == 'Panamá'){
        $html .= '
            <div align="center" >
                <img src="../../images/union.jpg">
            </div>
        ';
    }else{
        $html .= '
            <div align="center">
                <img src="../../'.$logo_2.'">
            </div>	
        ';
    }
                //$html .= '<div align="center"><img src="'.$servidor.'images/logo-homecare2.jpg"></div>';
    $html.='<pagebreak>	
            <table class="cabecera"><tr class="cabecera">';
    if($empresa_id == '7' && $_COOKIE['region'] == 'Panamá'){
        $html .= '<td class="cabecera" style="width:33%"><img width="230" height="85" src="../../images/logo-hmm.png"></td>
                    <td class="cabecera" style="width:33%"><img src="../../'.$logo.'"></td>';
    }else{
        $html.='<td class="cabecera" style="width:33%"><img src="../../'.$logo.'"></td><td class="cabecera" style="width:33%"></td>';
    }
    $html.='
                <td class="cabecera" style="width:33%; font-size: 10px; text-align:right">'.$vitae.'</td>
                </tr>
            </table>
            <br>
            <br>
            <br>
            <div>
            <h3>REPORTE DE SALUD <br>DEL <span style="color:blue">'.strftime("%d de %B de %Y",strtotime($inicio)).'</span> AL <span style="color:blue">'.strftime("%d de %B de %Y",strtotime($fin)).'</span></h3>
            </div>
            <br>
            <br>
    ';
                    
    // ********************* DATOS PACIENTE ************************* //
        $q="SELECT 	
                h.paciente AS id_paciente,
                h.cod_hospitalizacion, 
                DATE_FORMAT(h.fecha_inicio,'%d-%m-%Y') AS fecha_inicio,
                h.estatus AS estado_clinico, 
                h.fin_administrativo,
                IFNULL(DATE_FORMAT(ha.fecha_fin,'%d-%m-%Y'),h.fecha_inicio) AS fecha_fin,
                DATEDIFF(IFNULL(ha.fecha_fin,h.fecha_inicio),h.fecha_inicio) AS duracion,
                p.nombre,
                DATE_FORMAT(p.fechanac,'%d-%m-%Y') AS fechanac,
                p.cedula,
                FLOOR(TIMESTAMPDIFF(DAY , p.fechanac, CURDATE() ) /365 ) AS edad,
                (CASE WHEN p.tiposangre LIKE '%N' THEN CONCAT(p.tiposangre,' -') ELSE CONCAT(p.tiposangre,'+') END) AS tipo_sangre ,
                (CASE p.sexo WHEN 'F' THEN 'Femenino' ELSE 'Masculino' END) AS sexo,
                p.email,
                pa.address AS direccion, 
                hc.triage,
                CONCAT(mt.nombre,' ',mt.apellido) AS medico_tratante,
                IFNULL(GROUP_CONCAT(DISTINCT(CONCAT(omt.nombre,' ',omt.apellido,'<br>')) SEPARATOR ''),'') AS otros_medicos_tratantes,
                IFNULL(GROUP_CONCAT(DISTINCT(CONCAT('<b>',e.codigo,'</b> | ',e.nombre,'<br>')) SEPARATOR ''),'') AS diagnosticos
            FROM hospitalizacion h 
            LEFT JOIN hospitalizacion_perfil_administrativo ha ON ha.id_hospitalizacion = h.id AND ha.estatus = 1
            LEFT JOIN hospitalizacion_perfil_clinico hc ON hc.id_hospitalizacion = h.id AND hc.estatus = 1
            JOIN pacientes p ON h.paciente = p.id
            LEFT JOIN seguros s ON s.id = ha.id_seguro
            LEFT JOIN medicos mt ON mt.id = hc.id_medico_tratante
            LEFT JOIN medicos omt ON FIND_IN_SET(omt.id,hc.otros_medicos_tratantes)
            LEFT JOIN enfermedades e ON FIND_IN_SET(e.id,hc.diagnosticos)
            LEFT JOIN patient_addresses pa ON pa.patient_id = p.id
            WHERE p.id =  $id_paciente
            GROUP BY h.id LIMIT 1";

        $r = $mysqli->query($q);
        $datospaciente = '';
        if($p = $r->fetch_assoc()){
            $paciente = $p['nombre'];
            if($p['duracion'] == 0)
                $duracion = 1;
            else 
                $duracion  =$p['duracion']+1;
            if($empresa_id == '7' && $_COOKIE['region'] == 'Panamá'){
                $img_header ='  <div>
                                    <img style="float:left" src="../../'.$logo.'">
                                    <img style="float:right" width="225" height="80" src="../../images/logo-hmm.png">
                                </div><br/><br/>'; 
            }else{
                $img_header = ' <div>							
                                    <img style="float:right" src="../../'.$logo.'">
                                </div><br/><br/>'; 
            }

            $q_seguros="SELECT 
                            s.nombre AS seguro,
                            ps.numeropoliza AS numero_poliza
                        FROM pacienteseguros ps
                        INNER JOIN seguros s ON s.id = ps.idseguros
                        WHERE ps.idpacientes = $id_paciente";
            $res_seg = $mysqli->query($q_seguros);
            $seguro ='';
            if($res_seg->num_rows >=1){
                while($row_seg = $res_seg->fetch_assoc()){
                    $seguro .= '<b>Seguro :</b>'.$row_seg['seguro'].' / <b>Nro Póliza:</b><span style="color:red">'.$row_seg['numero_poliza'].'</span><br>';
                }
            }else{
                $seguro = '<b>Paciente no asegurado</b>';
            }
            $datospaciente .= '<table style="width:100%;border:1;">
                                    <thead><tr><th colspan="3">Datos del paciente</th></tr></thead>
                                        <tr>
                                            <td style="border:1;width:33%"><p><b>Paciente</b>: '.$p['nombre'].'</td>
                                            <td style="border:1;width:33%"><p><b>'.$conf->doc_identificacion.'</b>: '.$p['cedula'].' </td>
                                            <td style="border:1;width:33%"><p><b>Fecha de nacimiento</b>: '.$p['fechanac'].'</p></td>						
                        </tr>
                        <tr>
                            <td style="border:1;width:33%"><p><b>Edad</b>: '.$p['edad'].'</p></td>
                            <td style="border:1;width:33%"><p><b>Sexo</b>: '.$p['sexo'].'</p></td>
                            <td style="border:1;width:33%"><p><b>Tipo de sangre</b>: '.$p['tipo_sangre'].'</p></td>
                        </tr>
                        <tr colspan >
                            <td style="border:1;"><p>'.$seguro.'</p></td>
                            <td colspan="2" style="border:1;width:33%"><p><b>Dirección</b>: '.$p['direccion'].'</p></td>
                        </tr>
                        
                    </table><br>';		
            // DATOS DEL SERVICIO 
                $otros_medicos_tratantes = '';
                if($p['otros_medicos_tratantes'] != '')
                    $otros_medicos_tratantes = '<tr>
                                                    <th style="border:1;width:35%">Otros médicos tratantes</th>
                                                    <td style="border:1;">'.$p['otros_medicos_tratantes'].'</td>
                                                </tr>';
                $detalle_servicio = '
                <table style="width:100%;border:1;">
                    <tr>
                        <th style="border:1;width:35%">Código de '.$txt_hospitalizacion.'</th>
                        <td style="border:1;"><b>'.$p['cod_hospitalizacion'].'</b></td>
                    </tr>
                    <tr>
                        <th style="border:1;width:35%">Duración</th>
                        <td style="border:1;"><b>'.$p['fecha_inicio'].'</b> al <b>'.$p['fecha_fin'].'</b> ('.$duracion.' dias)</td>
                    </tr>
                    <tr>
                        <th style="border:1;width:35%">Médico tratante</th>
                        <td style="border:1;"><b>'.$p['medico_tratante'].'</b></td>
                    </tr>
                    '.$otros_medicos_tratantes.'                
                    <tr>
                        <th style="border:1;width:35%">Diagnósticos</th>
                        <td style="border:1;">'.$p['diagnosticos'].'</td>
                    </tr>
                </table>';
        
        

        }            

    // ********************* HISTORIAL PACIENTE ************************* //
        $historial = '';
        $alergias = '';
        $historial .= '<table style="width:100%;border:1;">
                            <thead><tr><th colspan="2">Historial Clínico</th></tr></thead>';
        //************ ALERGIAS
            $query = "	SELECT DISTINCT(b.nombre) as alergias FROM pac_alergias a 
                        LEFT JOIN maestro_alergias b on b.id = a.idalergia
                        WHERE a.idpaciente = '$id_paciente' AND a.idalergia != 0";
            $rA = $mysqli->query($query);
            $cntrows = $rA->num_rows;
            $alergias .= '<tr><td class="campo" style="width:35%"><b style="color:red;">Alergias<b></td><td>';					
            if($cntrows > 0){
                while($pA = $rA->fetch_assoc()){
                    if($pA['alergias'] != ''){				
                        $alergias .= $pA['alergias'].'<br>';				
                    }
                }		
            }else{
                    $alergias .='<hr style="color:blue; width: 75%">';
            }
            $alergias .= '</td></tr>';	
        // HABITOS PERSONALES
            $habitos_personales = '';
            $queryENP = "SELECT mh.nombre as antecedente FROM pac_habitosper ph
                            inner join maestro_antecedenteshabitosper mh ON mh.id = ph.idhabitosp
                            where ph.idpaciente = $id_paciente";
            $resultENP = $mysqli->query($queryENP);
                $cntrows = $resultENP->num_rows;
    
            $habitos_personales .= '<tr><td class="campo" style="width:35%"><b class="blue">Hábitos personales<b></td><td>';	
            if($cntrows > 0){
                while ($rowENP = $resultENP->fetch_assoc()){
                    $antecedente = $rowENP['antecedente'];
                    if($antecedente != ''){ 
                        $habitos_personales .= $antecedente.'<br>';	
                    }
                } 
            }else{
                    $habitos_personales .='<hr style="color:blue; width: 75%">';
            }
            $habitos_personales .= '</td></tr>';
        //ANTECEDENTES PERSONALES PATOLOGICOS
            $antecedentes_p = '';
            $queryEP = " SELECT concat('<b>',e.codigo,'</b>  ',e.nombre) AS enfermedad 
                            FROM pacientes a 
                            INNER JOIN pac_antecedentesper b ON a.id = b.idpaciente 
                            INNER JOIN enfermedades e ON e.id = b.idenfermedad
                            WHERE b.idpaciente = $id_paciente";
            $resultEP = $mysqli->query($queryEP);
                $cntrows = $resultEP->num_rows;
    
            $antecedentes_p .= '<tr><td class="campo" style="width:35%"><b class="blue">Antecedentes personales patológicos<b></td><td>';	
            if($cntrows > 0){
                while ($rowEP = $resultEP->fetch_assoc()){
                    $enfermedad = $rowEP['enfermedad'];
                    if($enfermedad != ''){ 
                        $antecedentes_p .= $enfermedad.'<br>';	
                    }
                } 
            }else{
                $antecedentes_p .= '<hr style="color:blue; width: 75%">';				
            }
                $antecedentes_p .= '</td></tr>';
        //ANTECEDENTES FAMILIARES
            $antecedentes_f = '';
            $queryF = "SELECT CONCAT(mf.nombre, (CASE WHEN pf.parentesco != '' THEN CONCAT(' (',pf.parentesco,')') ELSE '' END)) as antecedente
                            from pac_antecedentesfam pf
                            INNER JOIN maestro_antecedentesfam mf ON mf.id = pf.idantfam
                            WHERE pf.idpaciente = $id_paciente";
            $resultF = $mysqli->query($queryF);
                $cntrows = $resultEP->num_rows;
    
            $antecedentes_f .= '<tr><td class="campo" style="width:35%"><b class="blue">Antecedentes Familiares<b></td><td>';	
                if($cntrows > 0){
                while ($rowF = $resultF->fetch_assoc()){
                    $antecedente = $rowF['antecedente'];
                    if($antecedente != ''){ 
                        $antecedentes_f .= $antecedente.'<br>';	
                    }
                } 
                }else{
                $antecedentes_f .= '<hr style="color:blue; width: 75%">';
                }
                $antecedentes_f .= '</td></tr>';
        //ANTECEDENTES QUIRURGICOS
            $antecedentes_q = '';
            $queryQ = "SELECT CONCAT('<b>',(CASE pq.fecha WHEN '0000-00-00' THEN 'No hay fecha' ELSE DATE_FORMAT(pq.fecha,'%d-%m-%Y') END),'</b>  ',mq.nombre) as antecedente
                        FROM pac_antecedentesqui pq
                        INNER JOIN maestro_antecedentesqui mq ON mq.id = pq.idantqui
                        WHERE pq.idpaciente = $id_paciente";
            $resultQ = $mysqli->query($queryQ);
                $cntrows = $resultQ->num_rows;
    
            $antecedentes_q .= '<tr><td class="campo" style="width:35%"><b class="blue">Antecedentes Quirúrgicos<b></td><td>';	
                if($cntrows > 0){
                while ($rowQ = $resultQ->fetch_assoc()){
                    $antecedente = $rowQ['antecedente'];
                    if($antecedente != ''){ 
                        $antecedentes_q .= $antecedente.'<br>';	
                    }
                } 
                }else{
                $antecedentes_q .= '<hr style="color:blue; width: 75%">';
                }
                $antecedentes_q .= '</td></tr>';				 
        //ANTECEDENTES VACUNA
            $antecedentes_v = '';
            $queryV = "SELECT 
                            pv.id, v.nombre AS nombre, 
                            (CASE pv.fecha WHEN '0000-00-00' THEN 'No hay fecha' ELSE DATE_FORMAT(pv.fecha,'%d-%m-%Y') END) as fecha, 
                            (CASE pv.dosis WHEN '' THEN '' ELSE CONCAT('(',pv.dosis,')') END) as dosis
                        FROM pac_vacuna pv
                        JOIN maestro_vacuna v ON v.id = pv.idvacuna
                        WHERE idpaciente = $id_paciente";	
                    			
            $resultV = $mysqli->query($queryV);
            $cntrows = $resultV->num_rows;
            $antecedentes_v .= '<tr><td class="campo" style="width:35%"><b class="blue">Tarjeta de vacunas<b></td><td>';	 
                if($cntrows > 0){
                while ($rowV = $resultV->fetch_assoc()){
                    $antecedente = '<b>'.$rowV['nombre'].'</b> '.$rowV['dosis'].' '.$rowV['fecha'];
                    if($antecedente != ''){ 
                        $antecedentes_v .= $antecedente.'<br>'; 	
                    }
                } 
                }else{
                $antecedentes_v .= '<hr style="color:blue; width: 75%">';	
                }
                $antecedentes_v .= '</td></tr>';

        $historial .= 
                    $alergias	.
                    $antecedentes_p	. 
                    $habitos_personales.
                    $antecedentes_f.
                    $antecedentes_q.
                    $antecedentes_v
        ;		
        $historial .= '</table><br>';
    // ALERTAS DE SIGNOS VITALES 
        $query_alertas = "SELECT vsa.id, u.nombre AS usuario, vs.name AS signo_vital, vsa.minimum_value AS valor_minimo, 
					vsa.maximum_value AS valor_maximo, vsa.updated_at AS fecha_actualizacion,vsa.vital_sign_id as id_signo_vital
					FROM vital_sign_alerts vsa 
					JOIN vital_signs vs ON vs.id = vsa.vital_sign_id 
					JOIN hospitalizacion h ON h.paciente =vsa.patient_id AND h.estatus ='activa'
					JOIN usuarios u ON u.id = vsa.user_id
					WHERE  vsa.patient_id = $id_paciente order by vsa.vital_sign_id asc";
		$result_alertas = $mysqli->query($query_alertas);
        $tabla_alertas = '';
		if($result_alertas->num_rows >=1){
            $tabla_alertas.= '<h4>SIGNOS VITALES ESPERADOS</h4>
                                <table style="width:100%;border:1;">
                                    <thead>
                                        <tr>
                                            <th style="width:40%;">Signo vital</th>
                                            <th style="width:30%;">Rango normal</th>
                                            <th style="width:40%;">Fecha de actualizacion</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                        
            while($row_alertas = $result_alertas->fetch_assoc()){                            
                $tabla_alertas.='
                                        <tr>
                                            <td class="center"><b class="blue">'.$row_alertas['signo_vital'].'<b></td>
                                            <td class="center"><b>'.$row_alertas['valor_minimo'].' - '.$row_alertas['valor_maximo'].'</b></td>
                                            <td class="center">'.strftime("%d de %B de %Y",strtotime($row_alertas['fecha_actualizacion'])).' '. date("h:i a",strtotime($row_alertas['fecha_actualizacion'])).'</td>
                                        </tr>';
            };
            $tabla_alertas.='      </tbody>
                                </table>';
        }       
        
     

		
    // CONFIGURACION PARA IMPRIMIR
        $documentos = array();
        $html.=$datospaciente;
        $html.=$historial;
        $html.=$detalle_servicio;
        // $html.=$tabla_alertas;
        $reporte = "Reporte de salud - ".$paciente." -".$inicio.' al '.$fin.'.pdf';
        $mpdf=new mPDF('c'); 
        $mpdf->mirrorMargins = true;
        $mpdf->SetDisplayMode('fullpage','two');    
        $mpdf->WriteHTML($html);
        if(intval($mpdf->y) >= 120){
            $mpdf->WriteHTML('<pagebreak />'.$img_header);    
        }

        // AQUI SE LLAMAN LAS FUNCIONES DE LO QUE SE DEBE MOSTRAR 
            // VISITA MEDICA
                $visita_medica = visita_medica($id_paciente, $inicio,$fin);     
                $mpdf->WriteHTML('<br><br>');
                if(intval($mpdf->y) >= 195){
                    $mpdf->WriteHTML('<pagebreak />'.$img_header);    
                }
                $mpdf->WriteHTML($visita_medica);
            // ACTUALIZACIONES OM, OL Y TM
                $actualizaciones = actualizaciones_om($id_paciente,$inicio,$fin);     
                $mpdf->WriteHTML('<br><br>');
                if(intval($mpdf->y) >= 195){
                    $mpdf->WriteHTML('<pagebreak />'.$img_header);    
                }
                $mpdf->WriteHTML($actualizaciones);
        
            // VISITAS POR TIPO DE RECURSO
                $visitas_por_tipo = visitas_por_tipo($id_paciente,$inicio,$fin);     
                $mpdf->WriteHTML('<br><br>');
                if(intval($mpdf->y) >= 195){
                    $mpdf->WriteHTML('<pagebreak />'.$img_header);    
                }
                $mpdf->WriteHTML($visitas_por_tipo);
            //SIGNOS VITALES CON ALERTAS
                $signos_vitales = valores_fuera_de_rango($id_paciente,$inicio,$fin);     
                $mpdf->WriteHTML('<br><br>');
                if(intval($mpdf->y) >= 195){
                    $mpdf->WriteHTML('<pagebreak />'.$img_header);    
                }
                $mpdf->WriteHTML($signos_vitales);
            // NOTA DE COORDINACION CLINICA
                $notas_coordinacion_clinica = notas_de_coordinacion_clinica($id_paciente,$inicio,$fin);     
                $mpdf->WriteHTML('<br><br>');
                if(intval($mpdf->y) >= 195){
                    $mpdf->WriteHTML('<pagebreak />'.$img_header);    
                }
                $mpdf->WriteHTML($notas_coordinacion_clinica);
        
          
            

        // TERMINA LA CONFIGURACION


        $mpdf->Output($reporte, 'I');				
        mysqli_close($mysqli);
        exit;

    function condicion_paciente($id_paciente,$inicio,$fin){
        global $mysqli;
        $condicion = '';
        $query = "SELECT feeling AS como_se_siente, (CASE eating WHEN 1 THEN 'Si' ELSE 'No' END) AS comio_bien, IFNULL(comments,'') AS comentarios, DATE_FORMAT(created_at,'%d-%m-%Y %h:%i %p')  AS fecha
                    FROM conditions WHERE patient_id = $id_paciente AND DATE(created_at) BETWEEN '$inicio' AND '$fin' ORDER BY created_at DESC";
            $r = $mysqli->query($query);
            if($r->num_rows >= 1){
                $condicion  = '<h4>CONDICIÓN DE PACIENTE</h4>
                            <table style="width: 100%">
                                <thead>
                                    <tr>
                                        <th  class="center" style="width: 20%">Fecha</th>
                                        <th  class="center" style="width: 15%">Como se siente</th>
                                        <th  class="center" style="width: 15%">Comió bien</th>
                                        <th  class="center" style="width: 50%">Comentarios</th>
                                    </tr>
                                </thead>
                                <tbody>
                ';
                while($row = $r->fetch_assoc()){
                    $condicion.='<tr>
                                <td class="center">'.$row['fecha'].'</td>
                                <td class="center">'.$row['como_se_siente'].'</td>
                                <td class="center">'.$row['comio_bien'].'</td>
                                <td>'.$row['comentarios'].'</td>
                            </tr>';
                }
                $condicion.='</tbody></table>';
            }
            return $condicion;
    }
    function valores_fuera_de_rango($id_paciente,$inicio,$fin){
        global $mysqli; 
        $query = "SELECT id_sv, signo_vital, valor, fecha,condicion, (CASE color WHEN 'red' THEN 1 ELSE 0 END) AS orden  FROM (
            SELECT 
                vs.id as id_sv,
                vs.name AS signo_vital, 
                vvs.value AS valor,
                vvs.take_at AS fecha,
                resultado_signos_vitales(vs.id,p.fechanac,vvs.value) AS condicion,
                alerta_signos_vitales(vs.id,p.fechanac,vvs.value) AS color	
            FROM visit_vital_sign vvs 
            JOIN vital_signs vs ON vs.id = vvs.vital_sign_id
            JOIN visitas v ON v.id = vvs.visit_id
            JOIN pacientes p ON p.id = v.idpaciente
            WHERE v.idpaciente = $id_paciente
                UNION
            SELECT 
                vs.id as id_sv,
                vs.name AS signo_vital, 
                peg.valor AS valor,
                IFNULL(peg.fecha,CONCAT(v.fecha,' ',v.horainicioplan)) AS fecha,
                resultado_signos_vitales(vs.id,p.fechanac,peg.valor) AS condicion,
                alerta_signos_vitales(vs.id,p.fechanac,peg.valor) AS color	
            FROM pacientesexamenesg peg
            JOIN vital_signs vs ON vs.id = peg.idexamenesg
            JOIN pacientesvisitas v ON v.id = peg.idconsulta
            JOIN pacientes p ON p.id = v.idpacientes 
            WHERE v.idpacientes = $id_paciente
                UNION 
            SELECT 
                vs.id as id_sv,
                vs.name AS signo_vital, 
                pvs.value AS valor,
                pvs.take_at AS fecha,
                resultado_signos_vitales(vs.id,p.fechanac,pvs.value) AS condicion,
                alerta_signos_vitales(vs.id,p.fechanac,pvs.value) AS color	
            FROM patient_vital_sign pvs 
            JOIN vital_signs vs ON vs.id = pvs.vital_sign_id
            JOIN pacientes p ON p.id = pvs.patient_id
            WHERE pvs.patient_id= $id_paciente
        ) AS tabla 
        WHERE  DATE(fecha) BETWEEN '$inicio' AND '$fin' order by id_sv  asc";
        $tabla = '';
        global $arreglo_color;
        $resultSV=  $mysqli->query($query);		
        if($resultSV->num_rows >= 1){
            while($row_sv = $resultSV->fetch_assoc()){            
                if($row_sv['condicion'] != ''){
                    $data_sv[$row_sv['signo_vital']]['color'] = $arreglo_color[$row_sv['id_sv']];
                    $data_sv[$row_sv['signo_vital']]['titulo'] = $row_sv['signo_vital'];            
                    $data_sv[$row_sv['signo_vital']]['valores'][] = $row_sv;
                }
            }
            $tabla .= '<h4>SIGNOS VITALES CON ALERTAS</h4>
                        <div class="row">';
            // echo json_encode($data_sv);
            foreach($data_sv as $registros){
                $tabla.='   <div class="column">
                                <h4>'.$registros['titulo'].'</h4>
                                <table style=" margin-left:2.5%; margin-right:2.5%; margin-top:10px;margin-bottom:10px" >
                                    <thead>
                                        <tr>
                                            <th style="background-color: '.$registros['color'][2].';">Condición</th>
                                            <th style="background-color: '.$registros['color'][2].';">Cantidad de tomas</th>
                                            <th style="background-color: '.$registros['color'][2].';">Valor m&iacute;nimo</th>
                                            <th style="background-color: '.$registros['color'][2].';">Promedio</th>
                                            <th style="background-color: '.$registros['color'][2].';">Valor m&aacute;ximo</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                $arreglo_condicion = array();                
                $total = 0;        
                foreach($registros['valores'] as $valor){
                    $arreglo_condicion[$valor['condicion']]['name'] = $valor['condicion'];
                    $arreglo_condicion[$valor['condicion']]['y'] = isset($arreglo_condicion[$valor['condicion']]['y'])?intval($arreglo_condicion[$valor['condicion']]['y']) +1:1;
                    $arreglo_condicion[$valor['condicion']]['total'] = isset($arreglo_condicion[$valor['condicion']]['total'])?intval($arreglo_condicion[$valor['condicion']]['total']) +$valor['valor']:$valor['valor'];
                    $arreglo_condicion[$valor['condicion']]['valores'][] = $valor['valor'];
                    
                }
               
                $series = array();
                foreach($arreglo_condicion as $condicion){
                    $tabla.=    '<tr>
                        <td class="center">'.$condicion['name'].'</td>
                        <td class="center">'.$condicion['y'].'</td>
                        <td class="center">'.min($condicion['valores']).'</td>
                        <td class="center">'.number_format($condicion['total']/$condicion['y'],2).'</td>
                        <td class="center">'.max($condicion['valores']).'</td>
                    </tr>';
                    $series['data'][] = array(
                        "name" => $condicion['name'],
                        "y" => intVal($condicion['y'])
                    );
        
                }
                $tabla.='           </tbody>
                </table>
                </div>';
                $series['name']= 'SV';
                $data = array(
                    "titulo"=> "",
                    "categoria"=> "",
                    "tipo" =>"pie",
                    "colores" =>$registros['color'],
                    "series"=> $series
                );
                $graf = generar_grafico($data);
                // graficos 
                $tabla.= '<div class="column" style="text-align:center;"><img width="500px" height="400px" src="data:image/png;base64,' . base64_encode($graf) . '" /></div><br><br><br><br>';
            }       
            $tabla .='</div>';
        }

        return $tabla;
    }
    function notas_de_coordinacion_clinica($id_paciente,$inicio,$fin){
        global $mysqli;
        $notas = '';
        $q_notas_coordinacion = "SELECT  u.nombre as recurso, DATE_FORMAT( e.start,'%d-%m-%Y %h:%i %p')  AS inicio, DATE_FORMAT( e.end,'%d-%m-%Y %h:%i %p')  AS fin,nc.nota 
                                FROM notas_coordinacion_clinica nc
                                JOIN nursing_notes n ON n.id = nc.idnota
                                JOIN visitas v ON v.id = n.visit_id 
                                JOIN eventoscalendario e ON e.idgcal = v.idgcal
                                JOIN usuarios u ON u.id = v.idusuario
                                WHERE e.idpaciente = $id_paciente
                                AND DATE(e.start) BETWEEN '$inicio' AND '$fin' ORDER BY nc.id DESC LIMIT 1";	
        $result = $mysqli->query($q_notas_coordinacion);
        if($result->num_rows >= 1){
            $notas  = '<h4> NOTA DE COORDINACIÓN CLÍNICA</h4>
                        <table style="width: 100%">
                            <thead>
                                <tr>
                                    <th  class="center" style="width: 15%">Recurso</th>
                                    <th  class="center" style="width: 65%">Nota</th>
                                    <th  class="center" style="width: 20%">Turno</th>
                                </tr>
                            </thead>
                            <tbody>
            ';
            while($row = $result->fetch_assoc()){
                $notas.='<tr>
                            <td class="center">'.$row['recurso'].'</td>
                            <td>'.$row['nota'].'</td>
                            <td class="center">'.$row['inicio'].'<br>al<br>'.$row['fin'].'</td>
                        </tr>';
            }
            $notas.='</tbody></table>';
        }
        return $notas;
    }
    function objetivo_recomendiaciones_visita_medica($id_paciente,$inicio,$fin){
        global $mysqli;
        $qmg = "SELECT 	a.id, ec.tipo, m.nombre AS motivo, DATE(ec.start) AS fecha,TIME(ec.start) AS horainicio, a.valor, 
                a.detalle,b.id as id_paciente, b.nombre, b.cedula, a.idpacientes, 
                c.nombre AS medico, a.enfermedadactual, b.fechanac,
                a.evo_plan, a.evo_analisis, a.evo_objetiva, a.evo_subjetiva,a.aspecto_general
            FROM 		pacientesvisitas a
            JOIN 	pacientes b ON b.id = a.idpacientes
            INNER JOIN eventoscalendario ec ON ec.idgcal = a.idgcal AND ec.tipo ='medico' 
            JOIN 	usuarios c ON c.id = a.idusuario
            LEFT JOIN maestro_motivos_visita m ON m.id = a.idmotivo 
            WHERE b.id = $id_paciente AND
            ec.start BETWEEN '$inicio' AND '$fin' ORDER BY ec.start ASC LIMIT 1";
        $rmg = $mysqli->query($qmg);
        $cntrows_vm = $rmg->num_rows;
        $visitas_medicas = '';
        if($cntrows_vm > 0){
            while ($visita = $rmg->fetch_assoc()){

                $visitas_medicas .= '<h4>Evolución</h4>';
                if ($visita['evo_objetiva'] != ''){
                    $visitas_medicas .= '<p>'.$visita['evo_objetiva'].'</p>';
                }
                $q_recomendaciones = "SELECT * from pacientesrecomendaciones where idconsulta = ".$visita['id'];
                $resultado = $mysqli->query($q_recomendaciones);
                $rowR = $resultado->fetch_assoc();
                if($rowR['valor'] != ''){
                    $visitas_medicas .="<br />";
                    $visitas_medicas.='<br><h4>Recomendaciones</h4>
                        <p class="notas">'.$rowR['valor'].'</p>
                    ';
                }
            }
        }
        return $visitas_medicas;
    }
    function actualizaciones_om($id_paciente,$inicio,$fin){
        global $mysqli;
        $actualizaciones = '';
        $query = "SELECT 'Tarjeta de medicamentos' AS tipo, MAX(fecha) AS ultima_actualizacion, COUNT(id) AS cantidad_actualizaciones 
                    FROM tarjeta_medicamentos  WHERE id_paciente = $id_paciente
                    AND fecha BETWEEN '$inicio' AND '$fin'
                    UNION
                    SELECT 'Orden médica' AS tipo, MAX(created_at) AS ultima_actualizacion, COUNT(id) AS cantidad_actualizaciones 
                    FROM ordenmedicav1 WHERE paciente_id = $id_paciente
                    AND created_at BETWEEN '$inicio' AND '$fin' 
                ";
            $r = $mysqli->query($query);
            if($r->num_rows >= 1){
                $actualizaciones  = '<h4>ACTUALIZACIONES</h4>
                            <table style="width: 100%">
                                <thead>
                                    <tr>
                                        <th  class="center" style="width: 15%">Tipo</th>
                                        <th  class="center" style="width: 15%">Cantidad de actualizaciones</th>
                                        <th  class="center" style="width: 20%">Fecha de ultima actializacion</th>
                                    </tr>
                                </thead>
                                <tbody>
                ';
                while($row = $r->fetch_assoc()){
                    $actualizaciones.='<tr>
                                <td class="center">'.$row['tipo'].'</td>
                                <td class="center">'.$row['cantidad_actualizaciones'].'</td>
                                <td class="center">'.strftime("%d de %B de %Y",strtotime($row['ultima_actualizacion'])).'</td>
                            </tr>';
                }
                $actualizaciones.='</tbody></table>';
            }
            $q = "SELECT 'Orden de laboratorio' AS tipo, IFNULL(MAX(fechamuestra),'') AS ultima_actualizacion, COUNT(id) AS cantidad_actualizaciones 
                    FROM orden_laboratorio WHERE idpaciente = $id_paciente AND fechamuestra BETWEEN '$inicio' AND '$fin' ";

            $rol = $mysqli->query($q);
            if($rol->num_rows >= 1){
                $actualizaciones  .= ' <br>
                <table style="width: 100%">
                    <thead>
                        <tr>
                            <th  class="center" style="width: 15%">Tipo</th>
                            <th  class="center" style="width: 15%">Cantidad de ordenes</th>
                            <th  class="center" style="width: 20%">Fecha de última orden</th>
                        </tr>
                    </thead>
                    <tbody>
                ';
                while($row = $rol->fetch_assoc()){
                    $actualizaciones.='<tr>
                                <td class="center">'.$row['tipo'].'</td>
                                <td class="center">'.$row['cantidad_actualizaciones'].'</td>
                                <td class="center">'.strftime("%d de %B de %Y",strtotime($row['ultima_actualizacion'])).'</td>
                            </tr>';
                }
                $actualizaciones.='</tbody></table>';
            }
            return $actualizaciones;
    }
    function visitas_por_tipo($id_paciente,$inicio,$fin){
        global $mysqli;
        global $arreglo_color;
        $tipo_visita = '';
        $query = "SELECT COUNT(e.id) AS cantidad, n.nombre AS tipo_recurso, max(e.start) as ultima_visia
                    FROM eventoscalendario e
                    JOIN usuarios u ON u.id = e.recurso
                    JOIN niveles n ON n.id = u.nivel
                    WHERE e.idpaciente = $id_paciente AND DATE(e.start)  BETWEEN '$inicio' AND '$fin'
                    GROUP BY u.nivel 
                ";
        $r = $mysqli->query($query);
        if($r->num_rows >= 1){
            $tipo_visita  = '<h4>VISITAS POR TIPO DE RECURSO</h4>
                        <table style="width: 100%">
                            <thead>
                                <tr>
                                    <th  class="center">Tipo de recurso</th>
                                    <th  class="center">Cantidad de visitas</th>
                                    <th  class="center">&Uacute;ltima visita</th>
                                </tr>
                            </thead>
                            <tbody>
            ';
            while($row = $r->fetch_assoc()){
                $tipo_visita.='<tr>
                            <td class="center">'.$row['tipo_recurso'].'</td>
                            <td class="center">'.$row['cantidad'].'</td>
                            <td class="center">'.strftime("%d de %B de %Y",strtotime($row['ultima_visia'])).'</td>
                        </tr>';
            }
            $tipo_visita.='</tbody>
                        </table>';
        }
        return $tipo_visita;
    }
    function visita_medica($id_paciente, $inicio,$fin){
        global $mysqli;
        global $img_header;
        $visitas_medicas = '';
        $qmg = "SELECT 	a.id, ec.tipo, m.nombre AS motivo, DATE(ec.start) AS fecha,TIME(ec.start) AS horainicio, a.valor, 
                    a.detalle,b.id as id_paciente, b.nombre, b.cedula, a.idpacientes, 
                    c.nombre AS medico, a.enfermedadactual, b.fechanac,
                    IFNULL(a.evo_plan,'') AS evo_plan, IFNULL(a.evo_analisis,'') AS evo_analisis, IFNULL(a.evo_objetiva,'') AS evo_objetiva, IFNULL(a.evo_subjetiva,'') AS evo_subjetiva,a.aspecto_general
                FROM 		pacientesvisitas a
                LEFT JOIN 	pacientes b ON b.id = a.idpacientes
                INNER JOIN eventoscalendario ec ON ec.idgcal = a.idgcal AND ec.tipo ='medico' 
                LEFT JOIN 	usuarios c ON c.id = a.idusuario
                LEFT JOIN maestro_motivos_visita m ON m.id = a.idmotivo 
                WHERE ec.idpaciente = $id_paciente AND a.reportable = 1 AND
                ec.start BETWEEN '$inicio' AND '$fin' ORDER BY a.id DESC LIMIT 1";
        $rmg = $mysqli->query($qmg);
        $cntrows_vm = $rmg->num_rows;
        if($cntrows_vm > 0){
            while ($visita = $rmg->fetch_assoc()){
                $fecha_visita 		= $visita['fecha'];
                $horainicio 		= $visita['horainicio'];
                $motivo 	= $visita['motivo'];
                $id_paciente 	= $visita['id_paciente'];
                $tipo 	= $visita['tipo'];
                $detalle 	= $visita['detalle'];
                $medico 	= $visita['medico'];
                $nombrepac 	= $visita['nombre'];
                $cedula 	= $visita['cedula'];
                $idpaciente	= $visita['idpacientes'];
                $firma		= $visita['firma'];
                $idvisita	= $visita['id'];
                $fecha_nac	= $visita['fechanac'];
                $enfermedadactual = $visita['enfermedadactual'];
                $aspecto_general = $visita['aspecto_general'];
                $evo_plan = $visita['evo_plan'];
                $evo_analisis = $visita['evo_analisis'];
                $evo_objetiva = $visita['evo_objetiva'];
                $evo_subjetiva = $visita['evo_subjetiva'];
                
                // EXAMEN FISICO
                $q = "	SELECT a.valor, b.nombre, b.id as idsignovital,
                        (CASE WHEN b.id < 9 THEN  resultado_signos_vitales(b.id,'$fecha_nac',a.valor) ELSE '' END) as condicion,
                        alerta_signos_vitales(b.id,'$fecha_nac',a.valor) as color
                        FROM pacientesexamenesg a
                        LEFT JOIN examenesgenerales b ON b.id = a.idexamenesg
                        WHERE idconsulta = '$idvisita' AND valor <> '' AND nombre <> ''  
                        ";
                $r = $mysqli->query($q);
                $examenesgenerales = array();
                
                while ($examen = $r->fetch_assoc()){
                    $examenesgenerales[] = array(
                        'nombre' => $examen['nombre'],
                        'valor'=> $examen['valor'],
                        'condicion'=> $examen['condicion'],
                        'color'=> $examen['color']
                        );
                }
                
                $q = "	SELECT a.observaciones, a.tipovalor, b.nombre 
                        FROM pacientesexamenesf a
                        LEFT JOIN examenesfisicos b ON b.id = a.idexamenesf
                        WHERE idconsulta = '$idvisita' AND tipovalor <> '0'
                        ";
                $r = $mysqli->query($q);
                $examenesfisicos = array();
                while ($examen = $r->fetch_assoc()){
                    $examenesfisicos[] = array(
                        'nombre' => $examen['nombre'],
                        'tipovalor'=> $examen['tipovalor'],
                        'observaciones'=> $examen['observaciones']
                        );
                }
                if($tipo == 'medicoespecialista'){
                    $visitas_medicas.= '<h2><span class="blue">VISITA MÉDICA ESPECIALISTA</span></h2>';
                }else if($tipo == 'nutricionista'){
                    $visitas_medicas.= '<h2><span class="blue">NUTRICIONISTA</span></h2>';
                }else{
                    $visitas_medicas.= '<h2><span class="blue">VISITA MÉDICA</span></h2>';
                }
                                                                    
                $visitas_medicas.= '<table style="width:100%;border:0;">
                                <tr>
                                    <td style="width:33.33%;border:0; text-align:left"><h3><b>Médico</b>: '.$medico.'</h3></td>
                                    <td style="width:33.33%;border:0; text-align:center"><h3><b>Fecha de inicio</b>: '.$fecha_visita.'</h3></td>
                                    <td style="width:33.33%;border:0; text-align:right"><h3><b>Hora de inicio</b>: '.$horainicio.'</h3></td>									
                                </tr>
                            </table>';
                
                $visitas_medicas .= '<hr/>';
                    
                if ($motivo != ''){  
                    $visitas_medicas .= '<p><b>Motivo</b>: '.$motivo.'</p>';
                }else{
                    $visitas_medicas .= '<p><b>Motivo</b>:<hr style="color:blue; width: 10%"> </p>';								
                }
                if ($enfermedadactual != ''){
                    $visitas_medicas .= '<p><b>Enfermedad actual</b>: '.$enfermedadactual.'</p>';
                }else{
                    $visitas_medicas .= '<p><b>Enfermedad actual</b>:<hr style="color:blue; width: 10%"> </p>';								
                }
                if(!empty($examenesgenerales)){
                    $visitas_medicas .= '<h4>Signos vitales</h4>';
                    
                    $visitas_medicas.= '<div style="margin: auto;width: 75%;">
                                <table style="width:100%;">
                                <thead style="color:#0a5897;">					
                                    <tr>
                                        <th style="text-align: center;">Exámen</th>
                                        <th style="text-align: center;">Valor</th>
                                        <th style="text-align: center;">Condición</th>
                                    </tr>
                                </thead>
                                <tbody id="signos-vitales">';
                    foreach($examenesgenerales as $registro){
                        if($registro['valor'] != ''){
                            $visitas_medicas.='
                                <tr>
                                    <td style="text-align: center;"><span>'.$registro['nombre'].'</span></td>
                                    <td style="text-align: center;"><span>'.$registro['valor'].'</span></td>
                                    <td style="text-align: center;"><span style="color:'.$registro['color'].'">'.$registro['condicion'].'</span></td>
                                </tr>';
                        }
                    }
                    $visitas_medicas.='
                        </tbody>						
                    </table></div><br>';
                }
                    
                if(!empty($examenesfisicos)){
                    $visitas_medicas .= '<h4>Exámen F&iacute;sico</h4>';
                        if ($aspecto_general != ''){
                            $visitas_medicas .= '<p><b>Aspecto general</b>: '.$aspecto_general.'</p>';
                        }						
                    $visitas_medicas.= '<div style="margin: auto;width: 75%;">
                                <table style="width:100%;">
                                <thead style="color:#0a5897;">					
                                    <tr>
                                        <th style="text-align: center;">Nombre</th>
                                        <th style="text-align: center;">Condición</th>
                                        <th style="text-align: center;">Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody id="signos-vitales">';
                    foreach($examenesfisicos as $examen ){
                        if($examen['observaciones'] != ''){
                            $observaciones = $examen['observaciones'];
                        }else{
                            $observaciones = '<hr style="color:blue; width: 10%">';
                        }
                        
                        if($examen['tipovalor'] == '1'){
                            $vexamen = "Normal";
                        }
                        
                        if($examen['tipovalor'] == '2'){
                            $vexamen = "Anormal";
                        }
                        
                        //($examen['tipovalor'] == '1' ) ? "Normal" : "Anormal"	
                            
                        $visitas_medicas.='
                            <tr>
                                <td style="text-align: center;"><label>'.$examen['nombre'].'</label></td>
                                <td style="text-align: center;"><label>'.$vexamen.'</label></td>
                                <td style="text-align: center;"><label>'.$observaciones.'</label></td>
                            </tr>';
                    }
                    $visitas_medicas.='
                        </tbody>						
                    </table></div>';
                }
                // EQUIPOS
                $visitas_medicas .= '<br>';
                $q_equipos ="SELECT * FROM visita_equipos WHERE idvisita = '$idvisita'";
                $r_e = $mysqli->query($q_equipos);
                if($row_e = $r_e->fetch_assoc()){
                    if($row_e['equipos']!= ''){
                        $visitas_medicas .= '<h4>El paciente cuenta con los siguientes equipos en casa</h4>';
                        $visitas_medicas .= "<ul>";
                        $arreglo = explode(',',$row_e['equipos']);
                        foreach($arreglo as $equipo){
                            switch($equipo){
                                case 1 :
                                $visitas_medicas.="<li>Concentrador de Oxígeno</li>";
                                break;
                                case 2 :
                                $visitas_medicas.="<li>Tensiometro</li>";
                                break;
                                case 3 :
                                $visitas_medicas.="<li>Glucómetro</li>";
                                break;
                                case 4 :
                                $visitas_medicas.="<li>Otros: ".$row_e['otros']."</li>";
                                break;					
                            }												
                        }
                        $visitas_medicas .= "</ul><br>";
                    }
                }
                                    
                //Diagnosticos
                $q_diagnosticos = "SELECT DISTINCT e.id, CONCAT(e.codigo,' | ',e.nombre) as nombre FROM pacienteenfermedadesactuales pe INNER JOIN enfermedades e ON e.id = pe.idenfermedad WHERE pe.idvisita = '$idvisita'";
                //die($q_diagnosticos);
                $r_diagnosticos = $mysqli->query($q_diagnosticos);
                $arratdiagnosticos = array();
                // die($q_diagnosticos)
                while ($diagnostico = $r_diagnosticos->fetch_assoc()){
                    $arratdiagnosticos[] = array(
                        'id' => $diagnostico['id'],
                        'nombre' => $diagnostico['nombre']
                    );
                }
                
                
                if(!empty($arratdiagnosticos)){
                    $visitas_medicas .= '<h4>Diagnósticos de la visita</h4>';
                                                    
                    $visitas_medicas.= '<div style="margin: auto;width: 75%;">
                                <table style="width:100%;">
                                <thead style="color:#0a5897;">					
                                    <tr>												
                                        <th style="text-align: center;">Diagnóstico</th>
                                    </tr>
                                </thead>
                                <tbody id="diagnosticos-visita">';
                    foreach($arratdiagnosticos as $diagnostico ){
                        
                        $visitas_medicas.='
                            <tr>
                                <td style="text-align: center;"><label>'.$diagnostico['nombre'].'</label></td>
                            </tr>';
                    }
                    $visitas_medicas.='
                        </tbody>						
                    </table></div><br>';
                }
                if($evo_analisis!='' || $evo_objetiva!= '' || $evo_subjetiva !='' || $evo_plan!=''){
                    $visitas_medicas .= '<h4>Evolución</h4>';
                    if ($evo_subjetiva != ''){
                        $visitas_medicas .= '<p><b>Subjetivo</b>: '.$evo_subjetiva.'</p>';
                    }
                    if ($evo_objetiva != ''){
                        $visitas_medicas .= '<p><b>Objetivo</b>: '.$evo_objetiva.'</p>';
                    }
                    if ($evo_analisis != ''){
                        $visitas_medicas .= '<p><b>Análisis</b>: '.$evo_analisis.'</p>';
                    }
                    if ($evo_plan != ''){
                        $visitas_medicas .= '<p><b>Plan de tratamiento</b>: '.$evo_plan.'</p>';
                    }
                }	
                $q_recomendaciones = "select * from pacientesrecomendaciones where idconsulta = $idvisita";
                $resultado = $mysqli->query($q_recomendaciones);
                $rowR = $resultado->fetch_assoc();
                if($rowR['valor'] != ''){
                    $visitas_medicas .="<br />";
                    $visitas_medicas.='<br><h4>Recomendaciones</h4>
                        <p class="notas">'.$rowR['valor'].'</p>
                    ';
                }
                
                
                $query_Evaluaciones = "SELECT pe.id, e.name as evaluacion,e.test, CONCAT(' ',pe.total) as total,  re.resultado, re.color  from pac_evaluaciones pe 
                            INNER JOIN evaluaciones e ON e.id = pe.idevaluacion
                            INNER JOIN resultado_evaluacion re ON re.id = pe.idresultado
                            where pe.idvisita = $idvisita;";
                $result =$mysqli->query($query_Evaluaciones);
                $cntrows_ex = $result->num_rows;
                if($cntrows_ex > 0){
                    $visitas_medicas .= '<br><br>';
                    while($row = $result->fetch_assoc()){
                        $visitas_medicas .= '<h3>'.$row['evaluacion'].'</h3>';						
                        $visitas_medicas.= '<div style="margin: auto;width: 75%;">
                                    <table style="width:100%;">
                                    <thead style="color:#0a5897;">					
                                        <tr>
                                            <th style="text-align: center;">'.$row['test'].'</th>
                                            <th style="text-align: center;">Respuesta</th>									
                                        </tr>
                                    </thead>
                                    <tbody>';	
                            $q_e_det ="SELECT distinct  pe.texto, rpe.respuesta, pe.posicion FROM pac_evaluaciones_det ped 
                                    INNER JOIN preguntas_evaluacion pe ON pe.id = ped.idpregunta
                                    INNER JOIN respuestas_preguntas_evaluacion rpe ON rpe.id = ped.idrespuesta
                                    WHERE ped.idevaluacion = '".$row['id']."' ORDER BY pe.posicion ASC";
                            $re_ev =$mysqli->query($q_e_det);
                            while($row_ev = $re_ev->fetch_assoc()){	
                                $visitas_medicas.= '<tr>
                                                    <td style="text-align: center;">'.$row_ev['texto'].'</td>
                                                    <td style="text-align: center;"><span style="color:blue;">'.$row_ev['respuesta'].'</span></td>											
                                                </tr>';
                            }
                    
                        $visitas_medicas.='
                                            <tr>
                                                <td colspan="2" style="text-align:center">
                                                    <strong style="color:blue">RESULTADO</strong>
                                                </td>
                                            </tr>
                                            <tr>	
                                                <td style="text-align:center" >';
                        if($row['total'] == 0){
                            $visitas_medicas.='
                                <strong style"color:red">TOTAL:  '.strval($row['total']).'</strong>';
                        }else{
                            $visitas_medicas.='
                                <strong style"color:black">TOTAL:  '.strval($row['total']).'</strong>';
                        }
                        $visitas_medicas.='						</td>
                                                <td style="text-align:center">
                                                    <strong style="color:'.$row['color'].'">'.$row['resultado'].'</strong>
                                                </td>
                                            </tr>
                        </tbody></table></div><br>';				
                    }
                }
                //Tarjeta medicamnetos (Tratamientos)
                $q_tarjeta = "SELECT pt.*, m.nombre, mf.nombre nombrefrecuencia, md.nombre nombreduracion 
                                FROM pac_tratamientos pt 
                            LEFT JOIN maestro_frecuencia mf ON mf.id = pt.frecuencia
                            LEFT JOIN maestro_duracion md ON md.id = pt.duracion
                            LEFT JOIN medicamentos m ON m.id = pt.idmedicamento WHERE `idvisita` = '$idvisita' ";
                $r_tarjeta = $mysqli->query($q_tarjeta);
                $arraytarjeta = array();
                while ($tarjeta = $r_tarjeta->fetch_assoc()){
                    $arraytarjeta[] = array(
                        'nombre' => $tarjeta['nombre'],
                        'dosis' => $tarjeta['dosis'],
                        'fechainicio' => $tarjeta['fechainicio'],
                        'fechafin' => $tarjeta['fechafin'],
                        'horario' => $tarjeta['horainicio'],
                        'frecuencia' => $tarjeta['nombrefrecuencia'],
                        'duracion' => $tarjeta['nombreduracion']
                    );
                }
                if(!empty($arraytarjeta)){
                    $visitas_medicas .= '<h4>Tratamiento actual</h4>';
                    $visitas_medicas.= '<div style="margin: auto;width: 75%;">
                                <table style="width:100%;">
                                <thead style="color:#0a5897;">					
                                    <tr>												
                                        <th style="text-align: center;">Nombre</th>
                                        <th style="text-align: center;">Dosis</th>	
                                        <th style="text-align: center;">Fecha Inicio</th>
                                        <th style="text-align: center;">Fecha Fin</th>
                                        <th style="text-align: center;">Horario</th>
                                        <th style="text-align: center;">Frecuencia</th>
                                        <th style="text-align: center;">Duracion</th>
                                    </tr>
                                </thead>
                                <tbody id="plan-visita">';
                    foreach($arraytarjeta as $tarjeta ){
                        $visitas_medicas.='
                            <tr>
                                <td style="text-align: center;"><label>'.$tarjeta['nombre'].'</label></td>
                                <td style="text-align: center;"><label>'.$tarjeta['dosis'].'</label></td>
                                <td style="text-align: center;"><label>'.$tarjeta['fechainicio'].'</label></td>
                                <td style="text-align: center;"><label>'.$tarjeta['fechafin'].'</label></td>
                                <td style="text-align: center;"><label>'.$tarjeta['horario'].'</label></td>
                                <td style="text-align: center;"><label>'.$tarjeta['frecuencia'].'</label></td>
                                <td style="text-align: center;"><label>'.$tarjeta['duracion'].'</label></td>
                            </tr>';
                    }
                    $visitas_medicas.='
                        </tbody>						
                    </table></div><pagebreak>';
                }
            }	
        }
        return $visitas_medicas;
    }

    function generar_grafico($data){
        switch($data['tipo']){
            case "pie":
                $DataEncoded = json_encode(
                    array(
                        "infile"=> array(
                            "chart"=> array(
                                "type"=> "pie",
                                // "width"=> "100px",
                                // "height"=> "100"
                            ),
                            "credits"=>[array("enabled"=>"false")],
                            "plotOptions"=> array(
                                "pie"=> array(
                                    "showInLegend"=> "true",
                                    "allowPointSelect"=> "true",
                                    "cursor"=> "pointer",
                                    "depth"=> 35,
                                    "dataLabels"=> array(
                                        "enabled"=> "true",
                                        "format"=> "{point.percentage:.1f} %"
                                    ),
                                    "colors"=> $data['colores']                                    
                                )
                            ),     
                            "title"=> array(
                                "text"=> $data['titulos']
                            ),
                            "series"=> [$data['series']]
                        )
                    )
                );
            break;
            default:
                return "";
            break;
        }
        // echo $DataEncoded;die();
        //$dataString = ('type=image/jpeg&width=800&options='.$DataEncoded);
        //print_r($dataString);
        $url ='https://highcharts.vitae-health.com/?';
        $ch = curl_init();            
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$DataEncoded);
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = array();
        $headers[] = "Accept: application/json";
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if(curl_errno($ch)){
            echo "Entro al curl Error";
            throw new Exception(curl_error($ch));
            die("ERROR AL GENERAR GRAFICOS");
        }else{
            curl_close($ch);
            $link='https://highcharts.vitae-health.com/'.$result;
            // echo $link;die;
            return $result;
            return $link;
        }
    }
?>