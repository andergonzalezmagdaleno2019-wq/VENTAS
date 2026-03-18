<?php
    use app\controllers\purchaseController;
    $insCompra = new purchaseController();
    $url = explode("/", $_GET['views']);
    $compra_id = (isset($url[1]) && $url[1] != "") ? $insCompra->limpiarCadena($url[1]) : 0;
    if($compra_id == 0){ echo '<div class="notification is-danger mt-4">ID de compra no válido.</div>'; exit(); }

    $datos_compra = $insCompra->ejecutarConsulta("SELECT c.*, p.proveedor_nombre, p.proveedor_rif, u.usuario_nombre, u.usuario_apellido FROM compra c INNER JOIN proveedor p ON c.proveedor_id = p.proveedor_id INNER JOIN usuario u ON c.usuario_id = u.usuario_id WHERE c.compra_id='$compra_id'");
    if($datos_compra->rowCount() <= 0){ echo '<div class="notification is-danger mt-4">La compra no existe.</div>'; exit(); }
    $datos_compra = $datos_compra->fetch();

    $tasa_bcv = (float)$datos_compra['compra_tasa_bcv'];
    $tasa_bcv = ($tasa_bcv > 0) ? $tasa_bcv : 1;
    $pagos = $insCompra->ejecutarConsulta("SELECT SUM(pago_monto) as anticipo FROM compra_pagos WHERE compra_id='$compra_id'");
    $anticipo = (float)$pagos->fetch()['anticipo'];
    $recepciones = $insCompra->ejecutarConsulta("SELECT * FROM recepcion WHERE compra_id='$compra_id' ORDER BY recepcion_id ASC")->fetchAll();
?>

<div class="container pb-6 pt-6">
    <?php include "./app/views/inc/btn_back.php"; ?>
    <h1 class="title">Auditoría de Orden</h1>
    <h2 class="subtitle">Código: <strong><?php echo $datos_compra['compra_codigo']; ?></strong></h2>

    <div class="box">
        <div class="columns is-vcentered">
            <div class="column is-4">
                <p><strong><i class="fas fa-building"></i> Proveedor:</strong> <?php echo $datos_compra['proveedor_nombre']; ?> (RIF: <?php echo $datos_compra['proveedor_rif']; ?>)</p>
                <p><strong><i class="fas fa-user-tie"></i> Registrado por:</strong> <?php echo $datos_compra['usuario_nombre']." ".$datos_compra['usuario_apellido']; ?></p>
                <p><strong><i class="fas fa-money-bill-wave"></i> Tasa BCV (Día Orden):</strong> Bs <?php echo number_format($tasa_bcv, 2); ?></p>
            </div>
            <div class="column is-4 has-text-centered">
                <p class="is-size-5 mb-2"><strong>Estado Actual:</strong> 
                    <?php
                        $color_estado = "is-info";
                        if($datos_compra['compra_estado'] == "Completado" || $datos_compra['compra_estado'] == "Facturada") $color_estado = "is-success";
                        if($datos_compra['compra_estado'] == "Parcial" || $datos_compra['compra_estado'] == "Pendiente Factura") $color_estado = "is-warning";
                        if($datos_compra['compra_estado'] == "Anulada") $color_estado = "is-danger";
                    ?>
                    <span class="tag <?php echo $color_estado; ?> is-medium has-text-weight-bold"><?php echo $datos_compra['compra_estado']; ?></span>
                </p>
                <p class="is-size-6"><strong>Deuda Pendiente:</strong> <span class="has-text-danger-dark has-text-weight-bold">$<?php echo number_format($datos_compra['compra_saldo_pendiente'], 2); ?></span></p>
                <p class="is-size-6"><strong>Abonado / Pagado:</strong> <span class="has-text-success-dark has-text-weight-bold">$<?php echo number_format($anticipo, 2); ?></span></p>
            </div>
        </div>

        <h3 class="title is-5 has-text-link mt-5 mb-4"><i class="fas fa-folder-open"></i> Expediente de la Compra</h3>
        <div class="columns is-multiline">
            
            <div class="column is-4">
                <div class="notification is-link is-light" style="height: 100%; border-left: 4px solid #3273dc;">
                    <p class="heading"><i class="fas fa-file-contract"></i> Orden Interna</p>
                    <p class="title is-5 mb-1"><?php echo $datos_compra['compra_codigo']; ?></p>
                    <p class="is-size-7"><strong>Emitida:</strong> <?php echo date("d-m-Y", strtotime($datos_compra['compra_fecha'])); ?></p>
                    <?php if($datos_compra['compra_nota_interna'] != "" && strpos($datos_compra['compra_nota_interna'], 'Factura') === false){ ?>
                        <p class="is-size-7 mt-2"><strong>Nota:</strong> <?php echo $datos_compra['compra_nota_interna']; ?></p>
                    <?php } ?>
                </div>
            </div>

            <?php 
                $doc_llegada = "En espera de camión...";
                $clase_llegada = "is-warning";
                $icono_llegada = "fa-clock";
                if(count($recepciones) > 0){
                    $clase_llegada = "is-info";
                    $icono_llegada = "fa-box-open";
                    $rec_base = $recepciones[0]['recepcion_nota'];
                    $rec_texto = explode("] - ", $rec_base);
                    $datos_doc = str_replace("[", "", $rec_texto[0]);
                    $nota_almacen = isset($rec_texto[1]) && $rec_texto[1] != "" ? $rec_texto[1] : "Sin observaciones";
                    
                    // Separar para verse mejor
                    $datos_separados = explode(" | ", $datos_doc);
                    $doc_llegada = "<strong class='has-text-link-dark'>".$datos_separados[0]."</strong><br>";
                    if(isset($datos_separados[1])) $doc_llegada .= $datos_separados[1] . "<br>";
                    if(isset($datos_separados[2])) $doc_llegada .= $datos_separados[2] . "<br>";
                    $doc_llegada .= "<hr class='my-2'><strong>Nota de Almacén:</strong> " . $nota_almacen;
                }
            ?>
            <div class="column is-4">
                <div class="notification <?php echo $clase_llegada; ?> is-light" style="height: 100%; border-left: 4px solid #209cee;">
                    <p class="heading"><i class="fas <?php echo $icono_llegada; ?>"></i> Ingreso a Almacén</p>
                    <p class="is-size-7"><?php echo $doc_llegada; ?></p>
                </div>
            </div>

            <?php 
                $doc_factura = "Aún no se ha registrado Factura Oficial.";
                $clase_factura = "is-danger";
                $icono_factura = "fa-file-excel";
                if($datos_compra['compra_estado'] == "Facturada" || $datos_compra['compra_estado'] == "Completado"){
                    $clase_factura = "is-success";
                    $icono_factura = "fa-file-invoice-dollar";
                    preg_match('/\[(Factura Oficial Nro: [^\]]+)\]/', $datos_compra['compra_nota_interna'], $match_fac);
                    $num_fac = isset($match_fac[1]) ? $match_fac[1] : "Registrada de Contado";
                    
                    $vence = date("d-m-Y", strtotime($datos_compra['compra_fecha_vencimiento']));
                    $doc_factura = "<strong class='has-text-success-dark is-size-6'>".$num_fac."</strong><br><br><strong>Vencimiento Pago:</strong> ".$vence;
                }
            ?>
            <div class="column is-4">
                <div class="notification <?php echo $clase_factura; ?> is-light" style="height: 100%; border-left: 4px solid #48c774;">
                    <p class="heading"><i class="fas <?php echo $icono_factura; ?>"></i> Facturación</p>
                    <p class="is-size-7"><?php echo $doc_factura; ?></p>
                </div>
            </div>
        </div>

        <hr>

        <table class="table is-bordered is-striped is-fullwidth mt-5">
            <thead>
                <tr class="has-background-link-dark">
                    <th class="has-text-white">Producto</th>
                    <th class="has-text-centered has-text-white" style="width: 140px;">Cantidades</th>
                    <th class="has-text-centered has-text-white">Precio ($)</th>
                    <th class="has-text-centered has-text-white">Precio (Bs)</th>
                    <th class="has-text-centered has-text-white has-background-primary-dark">Subtotal ($)</th>
                    <th class="has-text-centered has-text-white has-background-primary-dark">Subtotal (Bs)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $detalles = $insCompra->ejecutarConsulta("
                        SELECT cd.*, p.producto_nombre,
                        (SELECT IFNULL(SUM(rd.cantidad_recibida), 0) FROM recepcion_detalle rd INNER JOIN recepcion r ON rd.recepcion_id = r.recepcion_id WHERE r.compra_id = cd.compra_id AND rd.producto_id = cd.producto_id) as cantidad_recibida
                        FROM compra_detalle cd 
                        INNER JOIN producto p ON cd.producto_id = p.producto_id 
                        WHERE cd.compra_id = '$compra_id'
                    ");
                    
                    foreach($detalles->fetchAll() as $items){
                        $cant_pedida = $items['compra_detalle_cantidad'];
                        $cant_recibida = $items['cantidad_recibida'];
                        
                        $cant_a_cobrar = (count($recepciones) > 0) ? $cant_recibida : $cant_pedida;
                        $sub_item = $cant_a_cobrar * $items['compra_detalle_precio'];

                        $etiqueta_cantidad = "";
                        if(count($recepciones) > 0){
                            if($cant_recibida < $cant_pedida){
                                $etiqueta_cantidad = "<br><span class='help is-danger is-size-7'>Pedida: $cant_pedida | Faltaron: ".($cant_pedida - $cant_recibida)."</span>";
                            } else {
                                $etiqueta_cantidad = "<br><span class='help is-success is-size-7'>Llegó completo</span>";
                            }
                        } else {
                            $etiqueta_cantidad = "<br><span class='help is-info is-size-7'>Aún no llega</span>";
                        }
                ?>
                <tr>
                    <td style="vertical-align: middle;"><?php echo $items['producto_nombre']; ?></td>
                    <td class="has-text-centered" style="vertical-align: middle; line-height: 1;">
                        <strong class="is-size-5"><?php echo $cant_a_cobrar; ?></strong>
                        <?php echo $etiqueta_cantidad; ?>
                    </td>
                    <td class="has-text-centered" style="vertical-align: middle;">$<?php echo number_format($items['compra_detalle_precio'], 2); ?></td>
                    <td class="has-text-centered" style="vertical-align: middle;">Bs <?php echo number_format($items['compra_detalle_precio'] * $tasa_bcv, 2); ?></td>
                    <td class="has-text-centered has-text-weight-bold" style="vertical-align: middle;">$<?php echo number_format($sub_item, 2); ?></td>
                    <td class="has-text-centered has-text-weight-bold has-text-info" style="vertical-align: middle;">Bs <?php echo number_format($sub_item * $tasa_bcv, 2); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="columns is-vcentered mt-5">
            <div class="column is-6"></div>
            <div class="column is-6 has-text-right">
                <div class="box has-background-white-ter" style="display: inline-block; text-align: right; border: 2px solid #ccc;">
                    <h4 class="title is-4 has-text-link mb-2">TOTAL COMPROBADO: $<?php echo number_format($datos_compra['compra_total'], 2); ?></h4>
                    <h5 class="subtitle is-5 has-text-success-dark m-0">EQUIVALENTE: Bs <?php echo number_format($datos_compra['compra_total'] * $tasa_bcv, 2); ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>