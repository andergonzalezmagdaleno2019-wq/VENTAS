<?php
    require_once "../../config/app.php";
    require_once "../views/inc/session_start.php";
    require_once "../../autoload.php";

    use app\controllers\purchaseController;

    $ins_Compra = new purchaseController();

    if(isset($_POST['modulo_compra'])){

        // --- Gestión de Carrito y Búsqueda ---
        if($_POST['modulo_compra']=="buscar_producto"){
            echo $ins_Compra->buscarProductoCompraControlador();
        }
        if($_POST['modulo_compra']=="agregar"){
            echo $ins_Compra->agregarProductoCompraControlador();
        }
        if($_POST['modulo_compra']=="vaciar"){
            echo $ins_Compra->vaciarCompraControlador();
        }
        if($_POST['modulo_compra']=="eliminar_producto_carrito"){
            echo $ins_Compra->eliminarProductoCarritoControlador();
        }

        // --- Gestión de Compras ---
        if($_POST['modulo_compra']=="registrar"){
            echo $ins_Compra->registrarCompraControlador();
        }
        if($_POST['modulo_compra']=="eliminar" || $_POST['modulo_compra']=="eliminar_compra"){
            echo $ins_Compra->eliminarCompraControlador();
        }

        // --- Recepción y Facturación ---
        if($_POST['modulo_compra']=="registrar_recepcion"){
            echo $ins_Compra->registrarRecepcionControlador();
        }
        if($_POST['modulo_compra']=="registrar_factura"){
            echo $ins_Compra->registrarFacturaPendienteControlador();
        }

        // --- Gestión de Abonos (Cuentas por Pagar) ---
        if($_POST['modulo_compra']=="registrar_abono"){
            echo $ins_Compra->registrarAbonoControlador();
        }
        if($_POST['modulo_compra']=="ver_historial_abonos"){
            echo $ins_Compra->listarAbonosCompraControlador($_POST['compra_id']);
        }
        if($_POST['modulo_compra']=="eliminar_abono"){
            echo $ins_Compra->eliminarAbonoControlador();
        }

        // --- Filtros Dinámicos ---
        if($_POST['modulo_compra']=="buscar_por_categoria"){
            echo $ins_Compra->buscarPorCategoriaCompraControlador();
        }
        if($_POST['modulo_compra'] == "filtrar_stock_categoria"){
            echo $ins_Compra->filtrarStockCategoriaControlador();
        }

        // --- FILTRADO POR PROVEEDOR  ---
    if($_POST['modulo_compra'] == "buscar_por_proveedor"){
        $id_proveedor = (isset($_POST['proveedor_id'])) ? (int)$_POST['proveedor_id'] : 0;
        
        // Llamar al método que ya tienes en el controller
        echo $ins_Compra->buscarProductoProveedorControlador($id_proveedor);
    }

    }else{
        session_destroy();
        header("Location: ".APP_URL."login/");
    }