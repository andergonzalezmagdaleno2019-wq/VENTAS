<?php
    require_once "../../config/app.php";
    require_once "../views/inc/session_start.php";
    require_once "../../autoload.php";

    use app\controllers\purchaseController;

    $ins_compra = new purchaseController();

    if(isset($_POST['modulo_compra'])){
        $insCompra = new purchaseController();

        if($_POST['modulo_compra']=="registrar_factura"){
            echo $insCompra->registrarFacturaCompraControlador();
        }

        if($_POST['modulo_compra']=="ver_historial_facturas"){
           echo $insCompra->listarFacturaCompraControlador($_POST['compra_id']);
        }
        
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
        if($_POST['modulo_compra']=="eliminar"){
            echo $insCompra->eliminarCompraControlador();
        }
        if($_POST['modulo_compra']=="buscar_por_categoria"){
            echo $insCompra->buscarPorCategoriaCompraControlador();
        }
        if($_POST['modulo_compra']=="registrar_recepcion"){
            echo $insCompra->registrarRecepcionControlador();
        }
        if($_POST['modulo_compra']=="registrar_abono"){
            echo $insCompra->registrarAbonoControlador();
        }
        if($_POST['modulo_compra']=="ver_historial_abonos"){
            echo $insCompra->listarAbonosCompraControlador($_POST['compra_id']);
        }
        if($_POST['modulo_compra']=="eliminar_abono"){
        echo $insCompra->eliminarAbonoControlador();
        }
        if($_POST['modulo_compra']=="eliminar_producto_carrito"){
            echo $insCompra->eliminarProductoCarritoControlador();
        }
        if($_POST['modulo_compra'] == "eliminar_compra"){
            echo $ins_compra->eliminarCompraControlador();
        }
       

    }else{
        session_destroy();
        header("Location: ".APP_URL."login/");
    }