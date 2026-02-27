<?php
	require_once "../../config/app.php";
	require_once "../views/inc/session_start.php";
	require_once "../../autoload.php";

	use app\controllers\providerController;

	if(isset($_POST['modulo_proveedor'])){
		
        $insProveedor = new providerController();

		if($_POST['modulo_proveedor']=="registrar"){
			echo $insProveedor->registrarProveedorControlador();
		}

        /*== FALTABA ESTO: ACCIÓN ELIMINAR ==*/
        if($_POST['modulo_proveedor']=="eliminar"){
			echo $insProveedor->eliminarProveedorControlador();
		}

        /*== FALTABA ESTO: ACCIÓN ACTUALIZAR ==*/
        if($_POST['modulo_proveedor']=="actualizar"){
			echo $insProveedor->actualizarProveedorControlador();
		}

	}else{
		session_destroy();
		header("Location: ".APP_URL."login/");
	}