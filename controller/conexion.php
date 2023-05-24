<?php
	include('config.php');
    ini_set('display_errors', 0);ini_set('display_startup_errors', 0);
	$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME );
	if ($mysqli->connect_error) {
		return print(
			json_encode(
				array(
					"error" => true,
					"mensaje" => "Error al conectar con la base de datos",
					"mysqli_error" => $mysqli->connect_error
				)
			)
		);
	}

?>
