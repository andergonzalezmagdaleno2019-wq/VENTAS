<?php
    use app\controllers\purchaseController;
    $insCompra = new purchaseController();

    /*---------- Obtener el ID de la URL ----------*/
    $url = explode("/", $_GET['views']);
    // Extraemos el ID del segundo segmento de la URL (ejemplo: purchaseDetail/4/)
    $compra_id = (isset($url[1]) && $url[1] != "") ? $insCompra->limpiarCadena($url[1]) : 0;

    if($compra_id == 0){
        echo '<div class="notification is-danger mt-4">ID de compra no válido o no proporcionado.</div>';
        exit();
    }

    /*---------- Datos generales de la compra ----------*/
    $datos_compra = $insCompra->ejecutarConsulta("SELECT c.*, p.proveedor_nombre, u.usuario_nombre, u.usuario_apellido 
        FROM compra c 
        INNER JOIN proveedor p ON c.proveedor_id = p.proveedor_id 
        INNER JOIN usuario u ON c.usuario_id = u.usuario_id 
        WHERE c.compra_id='$compra_id'");

    if($datos_compra->rowCount() <= 0){
        echo '<div class="notification is-danger mt-4">La compra con ID '.$compra_id.' no existe en el sistema.</div>';
        exit();
    }
    $datos_compra = $datos_compra->fetch();
?>

<div class="container pb-6 pt-6">
    <h1 class="title">Detalle de Compra</h1>
    <h2 class="subtitle">Orden: <strong><?php echo $datos_compra['compra_codigo']; ?></strong></h2>

    <div class="box">
        <div class="columns">
            <div class="column">
                <p><strong>Proveedor:</strong> <?php echo $datos_compra['proveedor_nombre']; ?></p>
                <p><strong>Fecha:</strong> <?php echo date("d-m-Y", strtotime($datos_compra['compra_fecha'])); ?></p>
            </div>
            <div class="column">
                <p><strong>Registrado por:</strong> <?php echo $datos_compra['usuario_nombre']." ".$datos_compra['usuario_apellido']; ?></p>
                <p><strong>Estado:</strong> 
                    <?php
                        $color = "is-info";
                        if($datos_compra['compra_estado'] == "Completado") $color = "is-success";
                        if($datos_compra['compra_estado'] == "Parcial") $color = "is-warning";
                    ?>
                    <span class="tag <?php echo $color; ?>"><?php echo $datos_compra['compra_estado']; ?></span>
                </p>
            </div>
        </div>

        <table class="table is-bordered is-striped is-hoverable is-fullwidth mt-4">
            <thead>
                <tr class="has-background-grey-lighter">
                    <th>Producto</th>
                    <th class="has-text-centered">Cantidad Pedida</th>
                    <th class="has-text-centered">Precio Unitario</th>
                    <th class="has-text-centered">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Usamos compra_detalle_cantidad para evitar el error de "Undefined array key"
                    $detalles = $insCompra->ejecutarConsulta("SELECT cd.*, p.producto_nombre 
                        FROM compra_detalle cd 
                        INNER JOIN producto p ON cd.producto_id = p.producto_id 
                        WHERE cd.compra_id = '$compra_id'");
                    
                    $detalles_array = $detalles->fetchAll();
                    foreach($detalles_array as $items){
                ?>
                <tr>
                    <td><?php echo $items['producto_nombre']; ?></td>
                    <td class="has-text-centered"><?php echo $items['compra_detalle_cantidad']; ?></td>
                    <td class="has-text-centered"><?php echo MONEDA_SIMBOLO.number_format($items['compra_detalle_precio'], 2, '.', ','); ?></td>
                    <td class="has-text-centered"><?php echo MONEDA_SIMBOLO.number_format($items['compra_detalle_cantidad'] * $items['compra_detalle_precio'], 2, '.', ','); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="has-text-right">
            <h4 class="title is-4">TOTAL: <?php echo MONEDA_SIMBOLO.number_format($datos_compra['compra_total'], 2, '.', ','); ?></h4>
        </div>
        
        <hr>
        <p class="has-text-centered">
            <a href="<?php echo APP_URL; ?>purchaseList/" class="button is-link is-rounded">Volver a la lista</a>
        </p>
    </div>
</div>