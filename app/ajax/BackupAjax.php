<?php
	
	require_once "../../config/app.php";
	require_once "../views/inc/session_start.php";
	require_once "../../autoload.php";
	
	use app\controllers\backupController;

	if(isset($_POST['modulo_backup'])){

		$insBackup = new backupController();

		if($_POST['modulo_backup']=="backup"){
			echo $insBackup->generarRespaldoControlador();
		}
		
	}else{
		session_destroy();
		header("Location: ".APP_URL."login/");
	}