<?php
	require_once "../../config/app.php";
	require_once "../views/inc/session_start.php";
	require_once "../../autoload.php";

	use app\controllers\purchaseController;

	if(isset($_POST['modulo_compra'])){
		$insCompra = new purchaseController();

		if($_POST['modulo_compra']=="buscar_producto"){
			echo $insCompra->buscarProductoCompraControlador();
		}
        if($_POST['modulo_compra']=="agregar"){
            echo $insCompra->agregarProductoCompraControlador();
        }
        if($_POST['modulo_compra']=="vaciar"){
            echo $insCompra->vaciarCompraControlador();
        }
        if($_POST['modulo_compra']=="registrar"){
            echo $insCompra->registrarCompraControlador();
        }
        // NUEVO: Para eliminar
        if($_POST['modulo_compra']=="eliminar"){
            echo $insCompra->eliminarCompraControlador();
        }
	}else{
		session_destroy();
		header("Location: ".APP_URL."login/");
	}