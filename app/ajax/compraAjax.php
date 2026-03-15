<?php
    require_once "../../config/app.php";
    require_once "../views/inc/session_start.php";
    require_once "../../autoload.php";

    use app\controllers\purchaseController;

    $ins_compra = new purchaseController();

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
        if($_POST['modulo_compra']=="buscar_por_categoria"){
            echo $insCompra->buscarPorCategoriaCompraControlador();
        }

        /*---------- Registrar Recepción de Mercancía ----------*/
        if($_POST['modulo_compra']=="registrar_recepcion"){
            echo $insCompra->registrarRecepcionControlador();
        }

        /*---------- Registrar Abono (Cuentas por Pagar) ----------*/
        if($_POST['modulo_compra']=="registrar_abono"){
            echo $insCompra->registrarAbonoControlador();
        }

        /*---------- Ver historial de abonos ----------*/
        if($_POST['modulo_compra']=="ver_historial_abonos"){
            echo $insCompra->listarAbonosCompraControlador($_POST['compra_id']);
        }


        /*---------- Eliminación ----------*/
        if($_POST['modulo_compra']=="eliminar_abono"){
        echo $insCompra->eliminarAbonoControlador();
        }
        
        /*---------- Eliminar producto individual de la sesión ----------*/
        if($_POST['modulo_compra']=="eliminar_producto_carrito"){
            echo $insCompra->eliminarProductoCarritoControlador();
        }

        if($_POST['modulo_compra'] == "eliminar_compra"){
            echo $ins_compra->eliminarCompraControlador();
        }

        if($_POST['modulo_compra'] == "vaciar_anuladas"){
            echo $ins_compra->vaciarAnuladasControlador();
        }

    }else{
        session_destroy();
        header("Location: ".APP_URL."login/");
    }