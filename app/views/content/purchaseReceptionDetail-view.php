<?php
    use app\controllers\purchaseController;
    $insCompra = new purchaseController();

    $url = explode("/", $_GET['views']);
    $compra_id = (isset($url[1]) && $url[1] != "") ? $insCompra->limpiarCadena($url[1]) : 0;

    if($compra_id == 0){
        echo '<div class="notification is-danger mt-4">ID de compra no válido o no proporcionado.</div>';
        exit();
    }

    $datos_compra = $insCompra->ejecutarConsulta("SELECT c.*, p.proveedor_nombre FROM compra c INNER JOIN proveedor p ON c.proveedor_id = p.proveedor_id WHERE c.compra_id='$compra_id'");
    if($datos_compra->rowCount() <= 0){
        echo '<div class="notification is-danger mt-4">La compra no existe.</div>';
        exit();
    }
    $datos_compra = $datos_compra->fetch();

    $consulta_detalles = "SELECT cd.*, p.producto_nombre, p.producto_costo, 
        (SELECT IFNULL(SUM(rd.cantidad_recibida), 0) FROM recepcion_detalle rd INNER JOIN recepcion r ON rd.recepcion_id = r.recepcion_id WHERE r.compra_id = cd.compra_id AND rd.producto_id = cd.producto_id) as cantidad_ya_recibida
        FROM compra_detalle cd
        INNER JOIN producto p ON cd.producto_id = p.producto_id
        WHERE cd.compra_id = '$compra_id'";

    $detalles = $insCompra->ejecutarConsulta($consulta_detalles);
    $detalles_array = $detalles->fetchAll();
?>

<div class="container pb-6 pt-6">
    <?php include "./app/views/inc/btn_back.php"; ?>
    <h1 class="title">Recepción y Facturación de Proveedor</h1>
    <h2 class="subtitle">Orden de Compra: <strong><?php echo $datos_compra['compra_codigo']; ?></strong> - <?php echo $datos_compra['proveedor_nombre']; ?></h2>
    
    <div class="notification is-info is-light">
        <i class="fas fa-info-circle"></i> &nbsp; <strong>Instrucciones:</strong> Indique la cantidad que está recibiendo físicamente y el <strong>Costo Unitario Final</strong> según la factura del proveedor.
    </div>

    <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/compraAjax.php" method="POST" autocomplete="off">
        <input type="hidden" name="modulo_compra" value="registrar_recepcion">
        <input type="hidden" name="compra_id" value="<?php echo $compra_id; ?>">

        <div class="box">
            <table class="table is-bordered is-striped is-hoverable is-fullwidth">
                <thead>
                    <tr class="has-background-link-dark">
                        <th class="has-text-white">Producto</th>
                        <th class="has-text-centered has-text-white">Pedida</th>
                        <th class="has-text-centered has-text-white">Pendiente</th>
                        <th class="has-text-centered has-text-white" style="width: 120px;">Cant. Recibida</th>
                        <th class="has-text-centered has-text-white" style="width: 110px;">Costo Ant.</th>
                        <th class="has-text-centered has-text-white has-background-primary-dark" style="width: 150px;">Costo Final ($)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach($detalles_array as $items){ 
                            $pedido = $items['compra_detalle_cantidad']; 
                            $recibido = $items['cantidad_ya_recibida'];
                            $pendiente = $pedido - $recibido;
                            
                            $txt_costo_anterior = ($items['producto_costo'] > 0) ? "$" . number_format($items['producto_costo'], 2, '.', '') : "N/A";
                            
                            if ($items['compra_detalle_precio'] > 0) {
                                $costo_sugerido = $items['compra_detalle_precio'];
                            } else if ($items['producto_costo'] > 0) {
                                $costo_sugerido = $items['producto_costo'];
                            } else {
                                $costo_sugerido = "";
                            }
                    ?>
                    <tr>
                        <td style="vertical-align: middle;"><strong><?php echo $items['producto_nombre']; ?></strong></td>
                        <td class="has-text-centered" style="vertical-align: middle;"><?php echo $pedido; ?></td>
                        <td class="has-text-centered" style="vertical-align: middle;">
                            <span class="tag <?php echo ($pendiente > 0) ? 'is-danger' : 'is-success'; ?> is-light is-medium">
                                <?php echo $pendiente; ?>
                            </span>
                        </td>
                        <td>
                            <div class="control">
                                <input class="input is-link has-text-centered has-text-weight-bold" type="number" 
                                       name="productos_recibidos[<?php echo $items['producto_id']; ?>]" 
                                       value="<?php echo ($pendiente > 0) ? $pendiente : 0; ?>" 
                                       min="0" max="<?php echo $pendiente; ?>" <?php if($pendiente <= 0) echo "readonly"; ?>>
                            </div>
                        </td>
                        
                        <td class="has-text-centered" style="vertical-align: middle;">
                            <strong class="has-text-grey"><?php echo $txt_costo_anterior; ?></strong>
                        </td>

                        <td>
                            <div class="control has-icons-left">
                                <input class="input is-primary has-text-right has-text-weight-bold" type="text" 
                                       name="costos_recibidos[<?php echo $items['producto_id']; ?>]" 
                                       value="<?php echo $costo_sugerido; ?>" 
                                       pattern="[0-9.]{1,25}" placeholder="0.00" <?php if($pendiente <= 0) echo "readonly"; ?> <?php if($pendiente > 0) echo "required"; ?>>
                                <span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="box has-background-light">
            <h3 class="title is-5 has-text-link"><i class="fas fa-file-invoice-dollar"></i> Condiciones de Facturación</h3>
            <div class="columns">
                <div class="column is-4">
                    <div class="field">
                        <label class="label">Condición de Pago de la Factura</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select name="compra_condicion" id="compra_condicion" required>
                                    <option value="Contado" selected>Al Contado (Pago Inmediato)</option>
                                    <option value="Consignacion">A Consignación (Se paga al vender)</option>
                                    <option value="Credito 15">Crédito a 15 Días</option>
                                    <option value="Credito 30">Crédito a 30 Días</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-4">
                    <div class="field">
                        <label class="label">Fecha Límite de Pago Real</label>
                        <div class="control">
                            <input class="input" type="date" name="compra_fecha_vencimiento" id="compra_fecha_vencimiento" value="<?php echo date("Y-m-d"); ?>" min="<?php echo date("Y-m-d"); ?>" required>
                        </div>
                        <p class="help">Se ajustará automáticamente según la condición.</p>
                    </div>
                </div>
                <div class="column is-4">
                    <div class="field">
                        <label class="label">Nota de Recepción (Opcional)</label>
                        <div class="control">
                            <input class="input" type="text" name="recepcion_nota" placeholder="Ej: Factura N° 4589. Llegó todo correcto.">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p class="has-text-centered mt-5">
            <button type="submit" class="button is-success is-rounded is-medium">
                <i class="fas fa-check-circle"></i> &nbsp; Confirmar Recepción y Cerrar Factura
            </button>
        </p>
    </form>
</div>

<script>
    document.getElementById('compra_condicion').addEventListener('change', function() {
        let dias = 0;
        if(this.value === 'Credito 15') dias = 15;
        if(this.value === 'Credito 30') dias = 30;
        if(this.value === 'Consignacion') dias = 90; 

        let fechaHoy = new Date();
        fechaHoy.setDate(fechaHoy.getDate() + dias);
        
        let mes = ('0' + (fechaHoy.getMonth() + 1)).slice(-2);
        let dia = ('0' + fechaHoy.getDate()).slice(-2);
        let fechaFormateada = fechaHoy.getFullYear() + '-' + mes + '-' + dia;
        
        document.getElementById('compra_fecha_vencimiento').value = fechaFormateada;
    });
</script>