<?php
    include("../conexion.php");
	/** Error reporting */
	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	global $mysqli;
	require '../phpspreadsheet/vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\IOFactory;
        // Create new PHPExcel object
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
	$sheet->setTitle('ACTIVIDADES RESUMEN');
	// Set document properties
	$fontColor = new \PhpOffice\PhpSpreadsheet\Style\Color();
	$fontColor->setRGB('ffffff');
	$style = array(
			'alignment' => array(
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
			)
	);
	$style2 = array(
			'alignment' => array(
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
			)
	);
	$spreadsheet->getProperties()->setCreator("VITAE")
	->setLastModifiedBy("VITAE")
	->setTitle("Pacientes")
	->setSubject("Pacientes")
	->setDescription("Pacientes")
	->setKeywords("Pacientes")
	->setCategory("Reportes");

	//TITULO	
	$spreadsheet->getActiveSheet()->setCellValue('A1', 'Lista de pacientes');
	$spreadsheet->getActiveSheet()->getStyle("A1")->getFont()->setBold(true)->setSize(14);
	$spreadsheet->getActiveSheet()->getStyle("A1")->applyFromArray($style);
	
    // ENCABEZADO 
     //--phpexcel--//        $hoja            
                                        //->setCellValue('A1', 'Id')

        //--spreadsheet//       $spreadsheet->getActiveSheet()          
                                        //->setCellValue('A1', 'Id')
	$spreadsheet->getActiveSheet()
        ->setCellValue('A1', 'Id')
        ->setCellValue('B1', 'Cedula')
        ->setCellValue('C1', 'Nombre')
        ->setCellValue('D1', 'Fechanac')
        ->setCellValue('E1', 'Estadocivil')
        ->setCellValue('F1', 'Sexo')
        ->setCellValue('G1', 'Tiposangre')
        ->setCellValue('H1', 'Email')
        ->setCellValue('I1', 'Ocupacion')
        ->setCellValue('J1', 'Telefonocasa')
        ->setCellValue('K1', 'Celular')
        ->setCellValue('L1', 'Nacionalidad')
        ->setCellValue('M1', 'Comoseentero')
        ->setCellValue('N1', 'Jubilado')
        ->setCellValue('O1', 'Grupodescuento')
        ->setCellValue('P1', 'Medicotratante')
        ->setCellValue('Q1', 'Otrosmedicos')
        ->setCellValue('R1', 'Enfermedadesactuales')
        ->setCellValue('S1', 'Enfermedadespasadas')
        ->setCellValue('T1', 'Contactos')
        ->setCellValue('U1', 'Healthmanager')
        ->setCellValue('V1', 'Polizas')
        ->setCellValue('W1', 'Direccion')
        ->setCellValue('X1', 'Comentariosdireccion')
        ->setCellValue('Y1', 'Descripcion')
        ->setCellValue('Z1', 'Comentariosimportantes')
        ->setCellValue('AA1', 'Ubicaciongeografica');
	
	//LETRA
	$spreadsheet->getActiveSheet()->getStyle('A1:AA1')->getFont()->setBold(true)->setSize(12)->setColor($fontColor);
	$spreadsheet->getActiveSheet()->getStyle("A1:AA1")->applyFromArray($style);
	//FONDO
    $spreadsheet->getActiveSheet()->getStyle('A1:AA1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('293F76');	
    //SENTENCIA BASE
    $query  ="SELECT * FROM vistapacientes;";
    $result = $mysqli->query($query);
    $i = 2;    
    //Definir fuente
	$spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);					
	
    while($row = $result->fetch_assoc()){  
        //--phpexcel--//        $hoja            
                                        //->setCellValue('A'.$i, $row['id'])

        //--spreadsheet//       $spreadsheet->getActiveSheet()          
                                        //->setCellValue('A'.$i, $row['id'])
        $spreadsheet->getActiveSheet()          
            ->setCellValue('A'.$i, $row['id'])
            ->setCellValue('B'.$i, $row['cedula'])
            ->setCellValue('C'.$i, $row['nombre'])
            ->setCellValue('D'.$i, $row['fechanac'])
            ->setCellValue('E'.$i, $row['estadocivil'])
            ->setCellValue('F'.$i, $row['sexo'])
            ->setCellValue('G'.$i, $row['tiposangre'])
            ->setCellValue('H'.$i, $row['email'])
            ->setCellValue('I'.$i, $row['ocupacion'])
            ->setCellValue('J'.$i, $row['telefonocasa'])
            ->setCellValue('K'.$i, $row['celular'])
            ->setCellValue('L'.$i, $row['nacionalidad'])
            ->setCellValue('M'.$i, $row['comoseentero'])
            ->setCellValue('N'.$i, $row['jubilado'])
            ->setCellValue('O'.$i, $row['grupodescuento'])
            ->setCellValue('P'.$i, $row['medicotratante'])
            ->setCellValue('Q'.$i, $row['otrosmedicos'])
            ->setCellValue('R'.$i, $row['enfermedadesactuales'])
            ->setCellValue('S'.$i, $row['enfermedadespasadas'])
            ->setCellValue('T'.$i, $row['contactos'])
            ->setCellValue('U'.$i, $row['healthmanager'])
            ->setCellValue('V'.$i, $row['polizas'])
            ->setCellValue('W'.$i, $row['direccion'])
            ->setCellValue('X'.$i, $row['comentariosdireccion'])
            ->setCellValue('Y'.$i, $row['descripcion'])
            ->setCellValue('Z'.$i, $row['comentariosimportantes'])
            ->setCellValue('AA'.$i,$row['ubicaciongeografica']);
        $i++;		
    }
    //--phpexcel--//                              $hoja->getColumnDimension('A')->setAutoSize(true);
    //--spreadsheet//    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('AA')->setAutoSize(true);
    
    //Renombrar hoja de Excel
	$spreadsheet->getActiveSheet()->setTitle('Pacientes');
    //Redirigir la salida al navegador del cliente
    $hoy = date('dmY');
    $nombreArc = 'DATA-PACIENTES - '.$hoy.'.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename='.$nombreArc);
    header('Cache-Control: max-age=0');	
    $writer = IOFactory::createWriter($spreadsheet,'Xlsx');
    //save into php output
    $writer->save('php://output');
	mysqli_close($mysqli);
    exit();
?>