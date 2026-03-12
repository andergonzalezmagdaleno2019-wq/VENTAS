<div class="container is-fluid mb-6">
	<h1 class="title">Ventas</h1>
	<h2 class="subtitle"><i class="fas fa-shopping-bag fa-fw"></i> &nbsp; Información de venta</h2>
</div>

<div class="container pb-6 pt-6">
	<?php
	
		include "./app/views/inc/btn_back.php";

		$code=$insLogin->limpiarCadena($url[1]);

		$datos=$insLogin->seleccionarDatos("Normal","venta INNER JOIN cliente ON venta.cliente_id=cliente.cliente_id INNER JOIN usuario ON venta.usuario_id=usuario.usuario_id INNER JOIN caja ON venta.caja_id=caja.caja_id WHERE (venta_codigo='".$code."')","*",0);

		if($datos->rowCount()==1){
			$datos_venta=$datos->fetch();

            /* Lógica para calcular la tasa y los Bs guardados en esta venta */
            $tasa_bcv = (isset($datos_venta['venta_tasa_bcv']) && $datos_venta['venta_tasa_bcv'] > 0) ? $datos_venta['venta_tasa_bcv'] : 0;
            $total_bs = $datos_venta['venta_total'] * $tasa_bcv;
            
            $str_tasa = ($tasa_bcv > 0) ? 'Bs. '.number_format($tasa_bcv, 2, ',', '.') : '<span class="has-text-grey-light">N/A (Venta antigua)</span>';
            $str_total_bs = ($tasa_bcv > 0) ? 'Bs. '.number_format($total_bs, 2, ',', '.') : '<span class="has-text-grey-light">N/A</span>';
	?>
	<h2 class="title has-text-centered">Datos de la venta <?php echo " (".$code.")"; ?></h2>
	
    <div class="columns pb-6 pt-6">
		<div class="column">
			<div class="full-width sale-details text-condensedLight">
				<div class="has-text-weight-bold">Fecha y Hora</div>
				<span class="has-text-link"><?php echo date("d-m-Y", strtotime($datos_venta['venta_fecha']))." ".$datos_venta['venta_hora']; ?></span>
			</div>
			<div class="full-width sale-details text-condensedLight">
				<div class="has-text-weight-bold">Nro. de factura</div>
				<span class="has-text-link"><?php echo $datos_venta['venta_id']; ?></span>
			</div>
			<div class="full-width sale-details text-condensedLight">
				<div class="has-text-weight-bold">Código de venta</div>
				<span class="has-text-link"><?php echo $datos_venta['venta_codigo']; ?></span>
			</div>
		</div>

		<div class="column">
			<div class="full-width sale-details text-condensedLight">
				<div class="has-text-weight-bold">Vendedor</div>
				<span class="has-text-link"><?php echo $datos_venta['usuario_nombre']." ".$datos_venta['usuario_apellido']; ?></span>
			</div>
			<div class="full-width sale-details text-condensedLight">
				<div class="has-text-weight-bold">Cliente</div>
				<span class="has-text-link"><?php echo $datos_venta['cliente_nombre']." ".$datos_venta['cliente_apellido']; ?></span>
			</div>
			<div class="full-width sale-details text-condensedLight">
				<div class="has-text-weight-bold">Método de Pago</div>
				<span class="has-text-link"><?php echo isset($datos_venta['venta_metodo_pago']) ? $datos_venta['venta_metodo_pago'] : 'N/A'; ?></span>
			</div>
			<div class="full-width sale-details text-condensedLight">
				<div class="has-text-weight-bold">Referencia</div>
				<span class="has-text-link"><?php echo (isset($datos_venta['venta_referencia']) && $datos_venta['venta_referencia'] != "") ? $datos_venta['venta_referencia'] : 'N/A'; ?></span>
			</div>
		</div>

		<div class="column">
			<div class="full-width sale-details text-condensedLight">
				<div class="has-text-weight-bold">Tasa BCV Aplicada</div>
				<span class="has-text-info"><?php echo $str_tasa; ?></span>
			</div>
			<div class="full-width sale-details text-condensedLight">
				<div class="has-text-weight-bold is-size-5">Total ($)</div>
				<span class="has-text-link is-size-5 has-text-weight-bold"><?php echo MONEDA_SIMBOLO.number_format($datos_venta['venta_total'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR).' '.MONEDA_NOMBRE; ?></span>
			</div>
			<div class="full-width sale-details text-condensedLight">
				<div class="has-text-weight-bold is-size-5 has-text-info">Total (Bs)</div>
				<span class="has-text-info is-size-5 has-text-weight-bold"><?php echo $str_total_bs; ?></span>
			</div>
		</div>
	</div>

	<div class="columns pb-6 pt-6">
		<div class="column">
			<div class="table-container">
                <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                    <thead>
                        <tr>
                            <th class="has-text-centered">#</th>
                            <th class="has-text-centered">Producto</th>
                            <th class="has-text-centered">Cant.</th>
                            <th class="has-text-centered">Precio ($)</th>
                            <th class="has-text-centered">Subtotal ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        	$detalle_venta=$insLogin->seleccionarDatos("Normal","venta_detalle WHERE venta_codigo='".$datos_venta['venta_codigo']."'","*",0);

                            if($detalle_venta->rowCount()>=1){

                                $detalle_venta=$detalle_venta->fetchAll();
                            	$cc=1;

                                foreach($detalle_venta as $detalle){
                        ?>
                        <tr class="has-text-centered" >
                            <td><?php echo $cc; ?></td>
                            <td><?php echo $detalle['venta_detalle_descripcion']; ?></td>
                            <td><?php echo $detalle['venta_detalle_cantidad']; ?></td>
                            <td><?php echo MONEDA_SIMBOLO.number_format($detalle['venta_detalle_precio_venta'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)." ".MONEDA_NOMBRE; ?></td>
                            <td><?php echo MONEDA_SIMBOLO.number_format($detalle['venta_detalle_total'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)." ".MONEDA_NOMBRE; ?></td>
                        </tr>
                        <?php
                                $cc++;
                            }
                        ?>
                        <tr class="has-text-centered" >
                            <td colspan="3"></td>
                            <td class="has-text-weight-bold">
                                TOTAL A PAGAR ($)
                            </td>
                            <td class="has-text-weight-bold">
                                <?php echo MONEDA_SIMBOLO.number_format($datos_venta['venta_total'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)." ".MONEDA_NOMBRE; ?>
                            </td>
                        </tr>
                        <?php
                            }else{
                        ?>
                        <tr class="has-text-centered" >
                            <td colspan="5">
                                No hay productos agregados en esta venta
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
		</div>
	</div>

	<div class="columns pb-6 pt-6">
		<p class="has-text-centered full-width">
			<?php
			echo '<button type="button" class="button is-info is-medium" onclick="print_invoice(\''.APP_URL.'app/pdf/invoice.php?code='.$datos_venta['venta_codigo'].'\')" title="Imprimir factura Nro. '.$datos_venta['venta_id'].'" >
			<i class="fas fa-file-pdf fa-fw"></i> &nbsp; Generar Factura PDF
			</button>';
			?>
		</p>
	</div>
	<?php
			include "./app/views/inc/print_invoice_script.php";
		}else{
			include "./app/views/inc/error_alert.php";
		}
	?>
</div>