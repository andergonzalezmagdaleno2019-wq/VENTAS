<?php
	$peticion_ajax=true;
	$code=(isset($_GET['code'])) ? $_GET['code'] : 0;

	/*---------- Incluyendo configuraciones ----------*/
	require_once "../../config/app.php";
    require_once "../../autoload.php";

	/*---------- Instancia al controlador venta ----------*/
	use app\controllers\saleController;
	$ins_venta = new saleController();

	$datos_venta=$ins_venta->seleccionarDatos("Normal","venta INNER JOIN cliente ON venta.cliente_id=cliente.cliente_id INNER JOIN usuario ON venta.usuario_id=usuario.usuario_id INNER JOIN caja ON venta.caja_id=caja.caja_id WHERE (venta_codigo='$code')","*",0);

	if($datos_venta->rowCount()==1){

		$datos_venta=$datos_venta->fetch();
		$datos_empresa=$ins_venta->seleccionarDatos("Normal","empresa LIMIT 1","*",0);
		$datos_empresa=$datos_empresa->fetch();

		require "./code128.php";

		$pdf = new PDF_Code128('P','mm','Letter');
		$pdf->SetMargins(17,17,17);
		$pdf->AddPage();
		if(is_file('../views/img/logo.png')){ $pdf->Image('../views/img/logo.png',165,12,35,35,'PNG'); }

		$pdf->SetFont('Arial','B',16);
		$pdf->SetTextColor(32,100,210);
		$pdf->Cell(150,10,iconv("UTF-8", "ISO-8859-1",strtoupper($datos_empresa['empresa_nombre'])),0,0,'L');
		$pdf->Ln(9);

		$pdf->SetFont('Arial','',10);
		$pdf->SetTextColor(39,39,51);
		$pdf->Cell(150,9,iconv("UTF-8", "ISO-8859-1","RIF: ".$datos_empresa['empresa_rif']),0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(150,9,iconv("UTF-8", "ISO-8859-1",$datos_empresa['empresa_direccion']),0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(150,9,iconv("UTF-8", "ISO-8859-1","Teléfono: ".$datos_empresa['empresa_telefono']),0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(150,9,iconv("UTF-8", "ISO-8859-1","Email: ".$datos_empresa['empresa_email']),0,0,'L');
		$pdf->Ln(10);

		$pdf->SetFont('Arial','',10);
		$pdf->Cell(30,7,iconv("UTF-8", "ISO-8859-1",'Fecha de emisión:'),0,0);
		$pdf->SetTextColor(97,97,97);
		$pdf->Cell(116,7,iconv("UTF-8", "ISO-8859-1",date("d/m/Y", strtotime($datos_venta['venta_fecha']))." ".$datos_venta['venta_hora']),0,0,'L');
		$pdf->SetFont('Arial','B',10);
		$pdf->SetTextColor(39,39,51);
		$pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1",strtoupper('CÓDIGO VENTA')),0,0,'C');
		$pdf->Ln(7);

		$pdf->SetFont('Arial','',10);
		$pdf->Cell(20,7,iconv("UTF-8", "ISO-8859-1",'Vendedor:'),0,0,'L');
		$pdf->SetTextColor(97,97,97);
		$pdf->Cell(126,7,iconv("UTF-8", "ISO-8859-1",$datos_venta['usuario_nombre']." ".$datos_venta['usuario_apellido']),0,0,'L');
		$pdf->SetFont('Arial','B',10);
		$pdf->SetTextColor(97,97,97);
		$pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1",strtoupper($datos_venta['venta_codigo'])),0,0,'C');
		$pdf->Ln(10);

		if($datos_venta['cliente_id']==1){
			$pdf->SetFont('Arial','',10); $pdf->SetTextColor(39,39,51); $pdf->Cell(13,7,iconv("UTF-8", "ISO-8859-1",'Cliente:'),0,0);
			$pdf->SetTextColor(97,97,97); $pdf->Cell(60,7,iconv("UTF-8", "ISO-8859-1","N/A"),0,0,'L');
			$pdf->SetTextColor(39,39,51); $pdf->Cell(8,7,iconv("UTF-8", "ISO-8859-1","Doc: "),0,0,'L');
			$pdf->SetTextColor(97,97,97); $pdf->Cell(60,7,iconv("UTF-8", "ISO-8859-1","N/A"),0,0,'L');
			$pdf->SetTextColor(39,39,51); $pdf->Cell(7,7,iconv("UTF-8", "ISO-8859-1",'Tel:'),0,0,'L');
			$pdf->SetTextColor(97,97,97); $pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1","N/A"),0,0);
			$pdf->Ln(7);
			$pdf->SetTextColor(39,39,51); $pdf->Cell(6,7,iconv("UTF-8", "ISO-8859-1",'Dir:'),0,0);
			$pdf->SetTextColor(97,97,97); $pdf->Cell(109,7,iconv("UTF-8", "ISO-8859-1","N/A"),0,0);
		}else{
			$pdf->SetFont('Arial','',10); $pdf->SetTextColor(39,39,51); $pdf->Cell(13,7,iconv("UTF-8", "ISO-8859-1",'Cliente:'),0,0);
			$pdf->SetTextColor(97,97,97); $pdf->Cell(60,7,iconv("UTF-8", "ISO-8859-1",$datos_venta['cliente_nombre']." ".$datos_venta['cliente_apellido']),0,0,'L');
			$pdf->SetTextColor(39,39,51); $pdf->Cell(8,7,iconv("UTF-8", "ISO-8859-1","Doc: "),0,0,'L');
			$pdf->SetTextColor(97,97,97); $pdf->Cell(60,7,iconv("UTF-8", "ISO-8859-1",$datos_venta['cliente_tipo_documento']." ".$datos_venta['cliente_numero_documento']),0,0,'L');
			$pdf->SetTextColor(39,39,51); $pdf->Cell(7,7,iconv("UTF-8", "ISO-8859-1",'Tel:'),0,0,'L');
			$pdf->SetTextColor(97,97,97); $pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1",$datos_venta['cliente_telefono']),0,0);
			$pdf->Ln(7);
			$pdf->SetTextColor(39,39,51); $pdf->Cell(6,7,iconv("UTF-8", "ISO-8859-1",'Dir:'),0,0);
			$pdf->SetTextColor(97,97,97); $pdf->Cell(109,7,iconv("UTF-8", "ISO-8859-1",$datos_venta['cliente_provincia'].", ".$datos_venta['cliente_ciudad'].", ".$datos_venta['cliente_direccion']),0,0);
		}
		$pdf->Ln(9);

		$pdf->SetFillColor(23,83,201);
		$pdf->SetDrawColor(23,83,201);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell(100,8,iconv("UTF-8", "ISO-8859-1",'Descripción'),1,0,'C',true);
		$pdf->Cell(15,8,iconv("UTF-8", "ISO-8859-1",'Cant.'),1,0,'C',true);
		$pdf->Cell(32,8,iconv("UTF-8", "ISO-8859-1",'Precio'),1,0,'C',true);
		$pdf->Cell(34,8,iconv("UTF-8", "ISO-8859-1",'Subtotal'),1,0,'C',true);
		$pdf->Ln(8);

		$pdf->SetFont('Arial','',9);
		$pdf->SetTextColor(39,39,51);

		/* JOIN CON PRODUCTO PARA EXTRAER MARCA Y MODELO */
		$venta_detalle=$ins_venta->seleccionarDatos("Normal","venta_detalle LEFT JOIN producto ON venta_detalle.producto_id=producto.producto_id WHERE venta_codigo='".$datos_venta['venta_codigo']."'","*",0);
		$venta_detalle=$venta_detalle->fetchAll();

		foreach($venta_detalle as $detalle){
            $descripcion = $detalle['venta_detalle_descripcion'];
            $marca_modelo = trim((isset($detalle['producto_marca']) ? $detalle['producto_marca'] : "") . " " . (isset($detalle['producto_modelo']) ? $detalle['producto_modelo'] : ""));
            
            if($marca_modelo != ""){
                $descripcion .= " - " . $marca_modelo;
            }

            // MAGIA DE LA RAYITA SEPARADORA (L = Izquierda, B = Abajo, R = Derecha)
			$pdf->Cell(100,7,iconv("UTF-8", "ISO-8859-1",$ins_venta->limitarCadena($descripcion,80,"...")),'L B',0,'C');
			$pdf->Cell(15,7,iconv("UTF-8", "ISO-8859-1",$detalle['venta_detalle_cantidad']),'L B',0,'C');
			$pdf->Cell(32,7,iconv("UTF-8", "ISO-8859-1",MONEDA_SIMBOLO.number_format($detalle['venta_detalle_precio_venta'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)),'L B',0,'C');
			$pdf->Cell(34,7,iconv("UTF-8", "ISO-8859-1",MONEDA_SIMBOLO.number_format($detalle['venta_detalle_total'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)),'L R B',0,'C');
			$pdf->Ln(7);
		}

		/* =======================================================
           TOTALES FIJADOS AL FINAL DE LA HOJA
           ======================================================= */
        
        // Si la lista de productos es muy larga y no caben los totales, creamos una nueva página
        if($pdf->GetY() > 220){
            $pdf->AddPage();
        }

        // Esta es la magia: Empujamos el cursor a 55 milímetros del fondo de la hoja (Se va para abajo)
        $pdf->SetY(-55);

        // Fila 1: Método de Pago (Izquierda) | Total USD (Derecha)
        $pdf->SetFont('Arial','',10);
        $metodo = isset($datos_venta['venta_metodo_pago']) ? $datos_venta['venta_metodo_pago'] : "N/A";
        $pdf->Cell(90, 7, iconv("UTF-8", "ISO-8859-1", "Método de Pago: " . $metodo), 0, 0, 'L');
        
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(57, 7, iconv("UTF-8", "ISO-8859-1", "TOTAL A PAGAR ($):"), 0, 0, 'R');
        $pdf->Cell(34, 7, iconv("UTF-8", "ISO-8859-1", MONEDA_SIMBOLO.number_format($datos_venta['venta_total'], 2, '.', ',')), 0, 1, 'C');

        // Fila 2: Referencia (Izquierda) | Tasa BCV (Derecha)
        $pdf->SetFont('Arial','',10);
        $referencia = (isset($datos_venta['venta_referencia']) && $datos_venta['venta_referencia']!="") ? $datos_venta['venta_referencia'] : "N/A";
        $pdf->Cell(90, 7, iconv("UTF-8", "ISO-8859-1", "Ref. Operación: " . $referencia), 0, 0, 'L');

        $tasa_bcv = (isset($datos_venta['venta_tasa_bcv']) && $datos_venta['venta_tasa_bcv'] > 0) ? $datos_venta['venta_tasa_bcv'] : 0;
        $str_tasa = ($tasa_bcv > 0) ? 'Bs. '.number_format($tasa_bcv, 2, ',', '.') : 'N/A';
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(57, 7, iconv("UTF-8", "ISO-8859-1", "TASA BCV OFICIAL:"), 0, 0, 'R');
        $pdf->Cell(34, 7, iconv("UTF-8", "ISO-8859-1", $str_tasa), 0, 1, 'C');

        // Fila 3: Vacío (Izquierda) | Total BS (Derecha)
        $total_bs = $datos_venta['venta_total'] * $tasa_bcv;
        $str_total_bs = ($tasa_bcv > 0) ? 'Bs. '.number_format($total_bs, 2, ',', '.') : 'N/A';
        $pdf->Cell(90, 7, "", 0, 0, 'L'); // Espacio vacío para empujar a la derecha
        
        $pdf->SetTextColor(32,100,210);
        $pdf->Cell(57, 7, iconv("UTF-8", "ISO-8859-1", "TOTAL PAGADO (Bs):"), 0, 0, 'R');
        $pdf->Cell(34, 7, iconv("UTF-8", "ISO-8859-1", $str_total_bs), 0, 1, 'C');
        $pdf->SetTextColor(39,39,51); // Reset de color
		
       

		$pdf->SetFont('Arial','',9);
		$pdf->MultiCell(0,9,iconv("UTF-8", "ISO-8859-1","*** Para poder realizar un reclamo o devolución debe de presentar esta factura ***"),0,'C',false);

		$pdf->Output("I","Factura_".$datos_venta['venta_codigo'].".pdf",true);

	}else{
        echo "Factura no encontrada";
    } 
?>