<?php
    require_once "../../config/app.php";
    require_once "../views/inc/session_start.php";
    require_once "../../autoload.php";

    use app\controllers\purchaseController;

    $ins_Compra = new purchaseController();

    if(isset($_POST['modulo_compra'])){

        if($_POST['modulo_compra']=="buscar_producto"){
            echo $ins_Compra->buscarProductoCompraControlador();
        }
        if($_POST['modulo_compra']=="agregar"){
            echo $ins_Compra->agregarProductoCompraControlador();
        }
        if($_POST['modulo_compra']=="vaciar"){
            echo $ins_Compra->vaciarCompraControlador();
        }
        if($_POST['modulo_compra']=="registrar"){
            echo $ins_Compra->registrarCompraControlador();
        }
        if($_POST['modulo_compra']=="eliminar"){
            echo $ins_Compra->eliminarCompraControlador();
        }
        if($_POST['modulo_compra']=="buscar_por_categoria"){
            echo $ins_Compra->buscarPorCategoriaCompraControlador();
        }
        if($_POST['modulo_compra']=="registrar_recepcion"){
            echo $ins_Compra->registrarRecepcionControlador();
        }
        if($_POST['modulo_compra']=="registrar_abono"){
            echo $ins_Compra->registrarAbonoControlador();
        }
        if($_POST['modulo_compra']=="ver_historial_abonos"){
            echo $ins_Compra->listarAbonosCompraControlador($_POST['compra_id']);
        }
        if($_POST['modulo_compra']=="eliminar_abono"){
            echo $ins_Compra->eliminarAbonoControlador();
        }
        if($_POST['modulo_compra']=="eliminar_producto_carrito"){
            echo $ins_Compra->eliminarProductoCarritoControlador();
        }
        if($_POST['modulo_compra'] == "eliminar_compra"){
            echo $ins_Compra->eliminarCompraControlador();
        }
        if($_POST['modulo_compra']=="registrar_factura"){
            echo $ins_Compra->registrarFacturaPendienteControlador();
        }

        if($_POST['modulo_compra'] == "filtrar_stock_categoria"){
            echo $ins_Compra->filtrarStockCategoriaControlador();
        }

        if($_POST['modulo_compra'] == "buscar_por_proveedor"){
            echo $ins_Compra->buscarProductoProveedorControlador();
        }

        // --- FILTRADO POR PROVEEDOR ---
        if($_POST['modulo_compra'] == "buscar_por_proveedor"){
            // Obtenemos el ID que viene del JS
            $id_proveedor = (isset($_POST['proveedor_id'])) ? $_POST['proveedor_id'] : 0;
            
            // Llamamos al método del controlador
            $productos = $ins_Compra->listarProductosProveedorControlador($id_proveedor);
            
            if($productos->rowCount() > 0){
                echo '<option value="" selected="">Seleccione un producto</option>';
                while($rows = $productos->fetch()){
                    echo '<option value="'.$rows['producto_id'].'">📦 '.$rows['producto_nombre'].' ('.$rows['producto_codigo'].')</option>';
                }
            } else {
                echo '<option value="" disabled selected>⚠️ Este proveedor no tiene productos vinculados</option>';
            }
        }

    }else{
        session_destroy();
        header("Location: ".APP_URL."login/");
    }