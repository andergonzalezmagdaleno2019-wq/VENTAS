<?php
    use app\controllers\purchaseController;
    $insCompra = new purchaseController();

    /*---------- Obtener el ID de la URL ----------*/
    $url = explode("/", $_GET['views']);
    $compra_id = (isset($url[1]) && $url[1] != "") ? $insCompra->limpiarCadena($url[1]) : 0;

    if($compra_id == 0){
        echo '<div class="notification is-danger mt-4">ID de compra no válido o no proporcionado.</div>';
        exit();
    }

    /*---------- Datos generales de la compra ----------*/
    $datos_compra = $insCompra->ejecutarConsulta("SELECT c.*, p.proveedor_nombre 
        FROM compra c 
        INNER JOIN proveedor p ON c.proveedor_id = p.proveedor_id 
        WHERE c.compra_id='$compra_id'");

    if($datos_compra->rowCount() <= 0){
        echo '<div class="notification is-danger mt-4">La compra no existe.</div>';
        exit();
    }
    $datos_compra = $datos_compra->fetch();

    /*---------- Consulta de productos y cálculo de cantidades recibidas ----------*/
    $consulta_detalles = "SELECT cd.*, p.producto_nombre, 
        (SELECT IFNULL(SUM(rd.cantidad_recibida), 0) 
         FROM recepcion_detalle rd 
         INNER JOIN recepcion r ON rd.recepcion_id = r.recepcion_id 
         WHERE r.compra_id = cd.compra_id AND rd.producto_id = cd.producto_id) as cantidad_ya_recibida
        FROM compra_detalle cd
        INNER JOIN producto p ON cd.producto_id = p.producto_id
        WHERE cd.compra_id = '$compra_id'";

    $detalles = $insCompra->ejecutarConsulta($consulta_detalles);
    $detalles_array = $detalles->fetchAll();
?>

<div class="container pb-6 pt-6">
    <h1 class="title">Registrar Entrada de Mercancía</h1>
    <h2 class="subtitle">Orden de Compra: <strong><?php echo $datos_compra['compra_codigo']; ?></strong></h2>

    <div class="box">
        <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/compraAjax.php" method="POST" autocomplete="off">
            
            <input type="hidden" name="modulo_compra" value="registrar_recepcion">
            <input type="hidden" name="compra_id" value="<?php echo $compra_id; ?>">

            <table class="table is-bordered is-striped is-hoverable is-fullwidth">
                <thead>
                    <tr class="has-background-link-dark">
                        <th class="has-text-white">Producto</th>
                        <th class="has-text-centered has-text-white">Cant. Pedida</th>
                        <th class="has-text-centered has-text-white">Ya Recibido</th>
                        <th class="has-text-centered has-text-white">Pendiente</th>
                        <th class="has-text-centered has-text-white" style="width: 150px;">Llega Hoy</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach($detalles_array as $items){ 
    
                            $pedido = $items['compra_detalle_cantidad']; 
                            $recibido = $items['cantidad_ya_recibida'];
                            $pendiente = $pedido - $recibido;
                    ?>
                    <tr>
                        <td><?php echo $items['producto_nombre']; ?></td>
                        <td class="has-text-centered"><?php echo $pedido; ?></td>
                        <td class="has-text-centered"><?php echo $recibido; ?></td>
                        <td class="has-text-centered">
                            <span class="tag <?php echo ($pendiente > 0) ? 'is-danger' : 'is-success'; ?> is-light">
                                <?php echo $pendiente; ?>
                            </span>
                        </td>
                        <td>
                            <div class="control">
                                <input class="input is-primary" type="number" 
                                       name="productos_recibidos[<?php echo $items['producto_id']; ?>]" 
                                       value="<?php echo ($pendiente > 0) ? $pendiente : 0; ?>" 
                                       min="0" 
                                       max="<?php echo $pendiente; ?>"
                                       <?php if($pendiente <= 0) echo "disabled"; ?>>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="field mt-5">
                <label class="label">Nota de Recepción (Opcional)</label>
                <div class="control">
                    <textarea class="textarea" name="recepcion_nota" placeholder="Ej: Se reciben 7 unidades, las otras 3 llegan mañana por falta de espacio."></textarea>
                </div>
            </div>

            <div class="buttons is-centered mt-4">
                <a href="<?php echo APP_URL; ?>purchaseList/" class="button is-link is-light is-rounded is-medium">
                    <i class="fas fa-arrow-left"></i> &nbsp; Volver al Listado
                </a>
                <button type="submit" class="button is-success is-rounded is-medium">
                    <i class="fas fa-check-circle"></i> &nbsp; Confirmar Ingreso
                </button>
            </div>
        </form>
    </div>
</div>