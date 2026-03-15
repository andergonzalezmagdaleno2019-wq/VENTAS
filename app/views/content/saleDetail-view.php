<div class="container is-fluid mb-6">
	<h1 class="title">Ventas</h1>
	<h2 class="subtitle"><i class="fas fa-shopping-bag fa-fw"></i> &nbsp; Información de venta</h2>
</div>

<div class="container pb-6 pt-6">
	<?php
		include "./app/views/inc/btn_back.php";
        
        /* Instanciar el controlador de Ventas correctamente */
        use app\controllers\saleController;
		$insVenta = new saleController();

		$code = $insVenta->limpiarCadena($url[1]);

		$datos = $insVenta->seleccionarDatos("Normal","venta INNER JOIN cliente ON venta.cliente_id=cliente.cliente_id INNER JOIN usuario ON venta.usuario_id=usuario.usuario_id INNER JOIN caja ON venta.caja_id=caja.caja_id WHERE (venta_codigo='".$code."')","*",0);

		if($datos->rowCount() == 1){
			$datos_venta = $datos->fetch();

            /* Lógica para calcular la tasa y los Bs guardados en esta venta */
            $tasa_bcv = (isset($datos_venta['venta_tasa_bcv']) && $datos_venta['venta_tasa_bcv'] > 0) ? $datos_venta['venta_tasa_bcv'] : 0;
            $total_bs = $datos_venta['venta_total'] * $tasa_bcv;
            
            $str_tasa = ($tasa_bcv > 0) ? 'Bs. '.number_format($tasa_bcv, 2, ',', '.') : '<span class="has-text-grey-light">N/A (Antigua)</span>';
            $str_total_bs = ($tasa_bcv > 0) ? 'Bs. '.number_format($total_bs, 2, ',', '.') : '<span class="has-text-grey-light">N/A</span>';
	?>
	<h2 class="title has-text-centered">Datos de la Factura <?php echo " (".$code.")"; ?></h2>
	
    <div class="columns pb-6 pt-6">
		<div class="column is-4">
            <div class="box">
                <div class="full-width sale-details text-condensedLight">
                    <div class="has-text-weight-bold">Fecha y Hora</div>
                    <span class="has-text-link"><?php echo date("d/m/Y", strtotime($datos_venta['venta_fecha']))." a las ".$datos_venta['venta_hora']; ?></span>
                </div>
                <div class="full-width sale-details text-condensedLight">
                    <div class="has-text-weight-bold">Código Interno</div>
                    <span class="has-text-link"><?php echo $datos_venta['venta_codigo']; ?></span>
                </div>
                <div class="full-width sale-details text-condensedLight">
                    <div class="has-text-weight-bold">Vendedor / Asesor</div>
                    <span class="has-text-link"><?php echo $datos_venta['usuario_nombre']." ".$datos_venta['usuario_apellido']; ?></span>
                </div>
                <div class="full-width sale-details text-condensedLight">
                    <div class="has-text-weight-bold">Cliente</div>
                    <span class="has-text-link"><?php echo $datos_venta['cliente_nombre']." ".$datos_venta['cliente_apellido']; ?></span>
                </div>
            </div>
		</div>

		<div class="column is-4">
            <div class="box">
                <div class="full-width sale-details text-condensedLight">
                    <div class="has-text-weight-bold">Método de Pago</div>
                    <span class="tag is-info is-light"><?php echo isset($datos_venta['venta_metodo_pago']) ? $datos_venta['venta_metodo_pago'] : 'N/A'; ?></span>
                </div>
                <div class="full-width sale-details text-condensedLight">
                    <div class="has-text-weight-bold">Referencia</div>
                    <span class="has-text-link"><?php echo (isset($datos_venta['venta_referencia']) && $datos_venta['venta_referencia'] != "") ? $datos_venta['venta_referencia'] : 'No aplica'; ?></span>
                </div>
                <div class="full-width sale-details text-condensedLight">
                    <div class="has-text-weight-bold">Tasa BCV del Día</div>
                    <span class="has-text-danger-dark has-text-weight-bold"><?php echo $str_tasa; ?></span>
                </div>
            </div>
		</div>

		<div class="column is-4">
            <div class="box has-background-light">
                <div class="full-width sale-details text-condensedLight mb-2">
                    <div class="has-text-weight-bold is-size-5">Total Pagado ($)</div>
                    <span class="has-text-link is-size-3 has-text-weight-bold"><?php echo MONEDA_SIMBOLO.number_format($datos_venta['venta_total'],2,'.',','); ?></span>
                </div>
                <hr class="mt-4 mb-4">
                <div class="full-width sale-details text-condensedLight">
                    <div class="has-text-weight-bold is-size-5 has-text-info-dark">Equivalente Total (Bs)</div>
                    <span class="has-text-info-dark is-size-4 has-text-weight-bold"><?php echo $str_total_bs; ?></span>
                </div>
            </div>
		</div>
	</div>

	<div class="columns pb-6 pt-2">
		<div class="column">
			<div class="table-container">
                <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                    <thead>
                        <tr class="has-background-link-dark">
                            <th class="has-text-centered has-text-white">#</th>
                            <th class="has-text-centered has-text-white">Producto</th>
                            <th class="has-text-centered has-text-white">Cant.</th>
                            <th class="has-text-centered has-text-white">Precio Unit.</th>
                            <th class="has-text-centered has-text-white">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        	$detalle_venta = $insVenta->seleccionarDatos("Normal","venta_detalle WHERE venta_codigo='".$datos_venta['venta_codigo']."'","*",0);

                            if($detalle_venta->rowCount()>=1){
                                $detalle_venta = $detalle_venta->fetchAll();
                            	$cc = 1;

                                foreach($detalle_venta as $detalle){
                                    /* Desglose en Bs dentro de la tabla */
                                    $subtotal_usd = $detalle['venta_detalle_total'];
                                    $precio_usd = $detalle['venta_detalle_precio_venta'];
                                    
                                    $str_sub_bs = ($tasa_bcv > 0) ? 'Bs. '.number_format($subtotal_usd * $tasa_bcv, 2, ',', '.') : '';
                                    $str_precio_bs = ($tasa_bcv > 0) ? 'Bs. '.number_format($precio_usd * $tasa_bcv, 2, ',', '.') : '';
                        ?>
                        <tr class="has-text-centered" >
                            <td style="vertical-align: middle;"><?php echo $cc; ?></td>
                            <td style="vertical-align: middle;"><strong><?php echo $detalle['venta_detalle_descripcion']; ?></strong></td>
                            <td style="vertical-align: middle;" class="is-size-5"><?php echo $detalle['venta_detalle_cantidad']; ?></td>
                            <td style="vertical-align: middle;">
                                <strong><?php echo MONEDA_SIMBOLO.number_format($precio_usd,2,'.',','); ?></strong><br>
                                <span class="is-size-7 has-text-grey"><?php echo $str_precio_bs; ?></span>
                            </td>
                            <td style="vertical-align: middle;">
                                <strong><?php echo MONEDA_SIMBOLO.number_format($subtotal_usd,2,'.',','); ?></strong><br>
                                <span class="is-size-7 has-text-link has-text-weight-bold"><?php echo $str_sub_bs; ?></span>
                            </td>
                        </tr>
                        <?php
                                $cc++;
                            }
                        ?>
                        <tr class="has-text-centered" >
                            <td colspan="3"></td>
                            <td class="has-text-weight-bold is-size-5">
                                TOTAL GENERAL
                            </td>
                            <td class="has-text-weight-bold is-size-5 has-text-link">
                                <?php echo MONEDA_SIMBOLO.number_format($datos_venta['venta_total'],2,'.',','); ?>
                            </td>
                        </tr>
                        <?php }else{ ?>
                        <tr class="has-text-centered" >
                            <td colspan="5">No hay productos registrados en esta factura.</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
		</div>
	</div>

	<div class="columns pb-6 pt-2">
		<p class="has-text-centered full-width">
			<button type="button" class="button is-info is-medium is-rounded" onclick="print_invoice('<?php echo APP_URL; ?>app/pdf/invoice.php?code=<?php echo $datos_venta['venta_codigo']; ?>')" title="Imprimir factura Fiscal" >
			    <i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; Imprimir Factura Fiscal
			</button>
            &nbsp;&nbsp;
            <button type="button" class="button is-link is-outlined is-medium is-rounded" onclick="print_invoice('<?php echo APP_URL; ?>app/pdf/delivery_note.php?code=<?php echo $datos_venta['venta_codigo']; ?>')" title="Imprimir Nota de Entrega" >
			    <i class="fas fa-file-alt fa-fw"></i> &nbsp; Imprimir Nota de Entrega
			</button>
		</p>
	</div>
	<?php
			include "./app/views/inc/print_invoice_script.php";
		}else{
			include "./app/views/inc/error_alert.php";
		}
	?>
</div>