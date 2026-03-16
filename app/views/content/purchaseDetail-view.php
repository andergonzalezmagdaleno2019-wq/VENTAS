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
    
    <?php include "./app/views/inc/btn_back.php"; ?>

    <h1 class="title">Detalle de Compra</h1>
    <h2 class="subtitle">Orden: <strong><?php echo $datos_compra['compra_codigo']; ?></strong></h2>

    <div class="box">
        <div class="columns">
            <div class="column">
                <p><strong>Proveedor:</strong> <?php echo $datos_compra['proveedor_nombre']; ?></p>
                <p><strong>Fecha de Orden:</strong> <?php echo date("d-m-Y", strtotime($datos_compra['compra_fecha'])); ?></p>
            </div>
            <div class="column">
                <p><strong>Registrado por:</strong> <?php echo $datos_compra['usuario_nombre']." ".$datos_compra['usuario_apellido']; ?></p>
                <p><strong>Estado Físico:</strong> 
                    <?php
                        $color = "is-info";
                        if($datos_compra['compra_estado'] == "Completado") $color = "is-success";
                        if($datos_compra['compra_estado'] == "Parcial") $color = "is-warning";
                    ?>
                    <span class="tag <?php echo $color; ?> is-light"><?php echo $datos_compra['compra_estado']; ?></span>
                </p>
                <p><strong>Estado de Pago:</strong> 
                    <?php
                        $color_pago = "is-danger";
                        if($datos_compra['compra_estado_pago'] == "Pagado") $color_pago = "is-success";
                        if($datos_compra['compra_estado_pago'] == "Parcial") $color_pago = "is-warning";
                    ?>
                    <span class="tag <?php echo $color_pago; ?> is-light"><?php echo $datos_compra['compra_estado_pago']; ?></span>
                </p>
            </div>
        </div>

        <hr>

        <h3 class="subtitle is-5"><i class="fas fa-file-invoice mr-2"></i>Facturas de Proveedor Vinculadas</h3>
        <?php
            $facturas = $insCompra->ejecutarConsulta("SELECT * FROM compra_factura WHERE compra_id='$compra_id' ORDER BY factura_id ASC");
            if($facturas->rowCount() > 0){
                $facturas = $facturas->fetchAll();
        ?>
            <div class="table-container mb-5">
                <table class="table is-bordered is-narrow is-fullwidth">
                    <thead class="has-background-white-ter">
                        <tr>
                            <th class="has-text-centered">N° Factura</th>
                            <th class="has-text-centered">Fecha Emisión</th>
                            <th class="has-text-centered">Vencimiento</th>
                            <th class="has-text-centered">Registrada el</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($facturas as $f){ ?>
                            <tr class="has-text-centered">
                                <td class="has-text-weight-bold"><?php echo $f['factura_numero']; ?></td>
                                <td><?php echo date("d/m/Y", strtotime($f['factura_emision'])); ?></td>
                                <td><?php echo date("d/m/Y", strtotime($f['factura_vencimiento'])); ?></td>
                                <td class="is-size-7 has-text-grey"><?php echo date("d/m/Y H:i", strtotime($f['factura_fecha_registro'])); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="notification is-warning is-light py-2 has-text-centered">
                <i class="fas fa-exclamation-triangle mr-2"></i> No hay facturas de proveedor vinculadas a esta orden.
            </div>
        <?php } ?>

        <hr>

        <h3 class="subtitle is-5"><i class="fas fa-boxes mr-2"></i>Detalle de Productos</h3>
        <table class="table is-bordered is-striped is-hoverable is-fullwidth mt-4">
            <thead>
                <tr class="has-background-link-dark">
                    <th class="has-text-white">Producto</th>
                    <th class="has-text-centered has-text-white">Cantidad Pedida</th>
                    <th class="has-text-centered has-text-white">Precio Unitario</th>
                    <th class="has-text-centered has-text-white">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $detalles = $insCompra->ejecutarConsulta("SELECT cd.*, p.producto_nombre 
                        FROM compra_detalle cd 
                        INNER JOIN producto p ON cd.producto_id = p.producto_id 
                        WHERE cd.compra_id = '$compra_id'");
                    
                    $detalles_array = $detalles->fetchAll();
                    foreach($detalles_array as $items){
                        
                        $precio_unitario = $items['compra_detalle_precio'];
                        $subtotal_item = $items['compra_detalle_cantidad'] * $precio_unitario;

                        if($precio_unitario > 0){
                            $txt_precio = MONEDA_SIMBOLO.number_format($precio_unitario, 2, '.', ',');
                            $txt_subtotal = MONEDA_SIMBOLO.number_format($subtotal_item, 2, '.', ',');
                        } else {
                            $txt_precio = '<span class="has-text-grey-light is-italic">POR DEFINIR</span>';
                            $txt_subtotal = '<span class="has-text-grey-light is-italic">POR DEFINIR</span>';
                        }
                ?>
                <tr>
                    <td style="vertical-align: middle;"><?php echo $items['producto_nombre']; ?></td>
                    <td class="has-text-centered is-size-5" style="vertical-align: middle;"><?php echo $items['compra_detalle_cantidad']; ?></td>
                    <td class="has-text-centered" style="vertical-align: middle;"><?php echo $txt_precio; ?></td>
                    <td class="has-text-centered has-text-weight-bold" style="vertical-align: middle;"><?php echo $txt_subtotal; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="has-text-right mt-5">
            <?php if($datos_compra['compra_total'] > 0){ ?>
                <h4 class="title is-4 has-text-link">TOTAL FACTURA: <?php echo MONEDA_SIMBOLO.number_format($datos_compra['compra_total'], 2, '.', ','); ?></h4>
            <?php } else { ?>
                <h4 class="title is-4 has-text-grey">TOTAL FACTURA: <span class="is-italic">POR DEFINIR</span></h4>
            <?php } ?>
        </div>

    </div>
</div>