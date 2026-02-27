<?php
	$peticion_ajax=true;
	$code=(isset($_GET['code'])) ? $_GET['code'] : 0;

	/*---------- Incluyendo configuraciones ----------*/
	require_once "../../config/app.php";
    require_once "../../autoload.php";

	/*---------- Instancia al controlador de compras ----------*/
	use app\controllers\purchaseController;
	$ins_compra = new purchaseController();

    /*---------- Seleccionando datos de la compra ----------*/
	$datos_compra=$ins_compra->seleccionarDatos("Normal","compra INNER JOIN proveedor ON compra.proveedor_id=proveedor.proveedor_id INNER JOIN usuario ON compra.usuario_id=usuario.usuario_id WHERE (compra_codigo='$code')","*",0);

	if($datos_compra->rowCount()==1){

		$datos_compra=$datos_compra->fetch();

		/*---------- Seleccion de datos de la empresa ----------*/
		$datos_empresa=$ins_compra->seleccionarDatos("Normal","empresa LIMIT 1","*",0);
		$datos_empresa=$datos_empresa->fetch();

		require "./code128.php";

		$pdf = new PDF_Code128('P','mm','Letter');
		$pdf->SetMargins(17,17,17);
		$pdf->AddPage();
		
        // Logo
        if(is_file("../views/img/logo.png")){
            $pdf->Image('../views/img/logo.png',165,12,35,35,'PNG');
        }

		// Encabezado de Empresa
		$pdf->SetFont('Arial','B',16);
		$pdf->SetTextColor(32,100,210); // Azul
		$pdf->Cell(150,10,iconv("UTF-8", "ISO-8859-1",strtoupper($datos_empresa['empresa_nombre'])),0,0,'L');
		$pdf->Ln(9);

		$pdf->SetFont('Arial','',10);
		$pdf->SetTextColor(39,39,51); // Gris Oscuro
        $rif_empresa = (isset($datos_empresa['empresa_rif'])) ? $datos_empresa['empresa_rif'] : "N/A";
		$pdf->Cell(150,7,iconv("UTF-8", "ISO-8859-1","RIF: ".$rif_empresa),0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(150,7,iconv("UTF-8", "ISO-8859-1",$datos_empresa['empresa_direccion']),0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(150,7,iconv("UTF-8", "ISO-8859-1","Teléfono: ".$datos_empresa['empresa_telefono']),0,0,'L');
		$pdf->Ln(15);

		// Título del Documento
		$pdf->SetFont('Arial','B',14);
        $pdf->SetTextColor(32,100,210);
		$pdf->Cell(0,10,iconv("UTF-8", "ISO-8859-1",strtoupper('ORDEN DE COMPRA')),0,1,'C');
        
        $pdf->SetFont('Arial','',10);
        $pdf->SetTextColor(39,39,51);
		$pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1",'No. de Orden: '.$datos_compra['compra_codigo']),0,1,'C');
        $pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1",'Fecha: '.date("d/m/Y", strtotime($datos_compra['compra_fecha']))),0,1,'C');
		$pdf->Ln(10);

       /*---------- SECCIÓN: DATOS DEL PROVEEDOR Y RESPONSABLE ----------*/
        $pdf->SetFillColor(245,245,245);
        $pdf->SetDrawColor(200,200,200);
        $pdf->SetFont('Arial','B',10);
        $pdf->SetTextColor(32,100,210);
        
        // Títulos de columnas
        $pdf->Cell(91,8,iconv("UTF-8", "ISO-8859-1",'DATOS DEL PROVEEDOR'),'B',0,'L',true);
        $pdf->Cell(91,8,iconv("UTF-8", "ISO-8859-1",'REGISTRADO POR'),'B',1,'L',true);
        
        $pdf->Ln(3);
        
        $pdf->SetFont('Arial','',10);
        $pdf->SetTextColor(39,39,51);
        
        // Fila 1: Nombre del Proveedor y Nombre del Usuario
        $pdf->Cell(91,7,iconv("UTF-8", "ISO-8859-1","Nombre: ".$datos_compra['proveedor_nombre']),0,0,'L');
        $pdf->Cell(91,7,iconv("UTF-8", "ISO-8859-1","Nombre: ".$datos_compra['usuario_nombre']." ".$datos_compra['usuario_apellido']),0,1,'L');
        
        // Fila 2: RIF del Proveedor y Email del Usuario
        $pdf->Cell(91,7,iconv("UTF-8", "ISO-8859-1","RIF: ".$datos_compra['proveedor_rif']),0,0,'L');
        $pdf->Cell(91,7,iconv("UTF-8", "ISO-8859-1","Email: ".$datos_compra['usuario_email']),0,1,'L');
        
        // Fila 3: Teléfono del Proveedor (Opcional)
        $telefono_p = (isset($datos_compra['proveedor_telefono']) && $datos_compra['proveedor_telefono']!="") ? $datos_compra['proveedor_telefono'] : "N/A";
        $pdf->Cell(91,7,iconv("UTF-8", "ISO-8859-1","Teléfono: ".$telefono_p),0,0,'L');
        $pdf->Cell(91,7,"",0,1,'L'); // Espacio vacío a la derecha
		
		$pdf->Ln(10);

        // Tabla de productos (AZUL)
		$pdf->SetFillColor(32,100,210); 
		$pdf->SetDrawColor(32,100,210);
		$pdf->SetTextColor(255,255,255);
        $pdf->SetFont('Arial','B',10);
		$pdf->Cell(100,8,iconv("UTF-8", "ISO-8859-1",'Descripción del Producto'),1,0,'C',true);
		$pdf->Cell(15,8,iconv("UTF-8", "ISO-8859-1",'Cant.'),1,0,'C',true);
		$pdf->Cell(32,8,iconv("UTF-8", "ISO-8859-1",'Costo Unit.'),1,0,'C',true);
		$pdf->Cell(35,8,iconv("UTF-8", "ISO-8859-1",'Subtotal'),1,0,'C',true);
		$pdf->Ln(8);

		$pdf->SetFont('Arial','',9);
		$pdf->SetTextColor(39,39,51);

		/*---------- Consulta Detalle ----------*/
		$compra_detalle=$ins_compra->seleccionarDatos("Normal","compra_detalle INNER JOIN producto ON compra_detalle.producto_id=producto.producto_id WHERE compra_codigo='".$datos_compra['compra_codigo']."'","*",0);
		$compra_detalle=$compra_detalle->fetchAll();

		foreach($compra_detalle as $detalle){
            $subtotal = $detalle['compra_detalle_cantidad'] * $detalle['compra_detalle_precio'];

			$pdf->Cell(100,7,iconv("UTF-8", "ISO-8859-1", $detalle['producto_nombre']),'B',0,'L');
			$pdf->Cell(15,7,iconv("UTF-8", "ISO-8859-1",$detalle['compra_detalle_cantidad']),'B',0,'C');
			$pdf->Cell(32,7,iconv("UTF-8", "ISO-8859-1",MONEDA_SIMBOLO.number_format($detalle['compra_detalle_precio'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)),'B',0,'C');
			$pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1",MONEDA_SIMBOLO.number_format($subtotal,MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)),'B',0,'R');
			$pdf->Ln(7);
		}

		/*========================================================================*/
		/*== NUEVOS TOTALES CON TASA BCV Y BOLÍVARES (ORDEN DE COMPRA)          ==*/
		/*========================================================================*/
		$pdf->Ln(3);
		$pdf->SetFont('Arial','B',11);
        $pdf->SetTextColor(39,39,51); // Color negro/gris oscuro
		$pdf->Cell(147,8,iconv("UTF-8", "ISO-8859-1",'TOTAL COMPRA ($): '),0,0,'R');
		$pdf->Cell(35,8,iconv("UTF-8", "ISO-8859-1",MONEDA_SIMBOLO.number_format($datos_compra['compra_total'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)),0,0,'R');

        $pdf->Ln(7);

        // Cálculo para Bolívares usando la tasa guardada en la base de datos
        $tasa_bcv = (isset($datos_compra['compra_tasa_bcv']) && $datos_compra['compra_tasa_bcv'] > 0) ? $datos_compra['compra_tasa_bcv'] : 0;
        $total_bs = $datos_compra['compra_total'] * $tasa_bcv;

        $str_tasa = ($tasa_bcv > 0) ? 'Bs. '.number_format($tasa_bcv, 2, ',', '.') : 'N/A';
        $str_total_bs = ($tasa_bcv > 0) ? 'Bs. '.number_format($total_bs, 2, ',', '.') : 'N/A';

        $pdf->SetFont('Arial','B',10);
		$pdf->Cell(147,8,iconv("UTF-8", "ISO-8859-1",'TASA BCV OFICIAL: '),0,0,'R');
		$pdf->Cell(35,8,iconv("UTF-8", "ISO-8859-1",$str_tasa),0,0,'R');

        $pdf->Ln(7);

        // Resaltar el Total en Bolívares en Azul
        $pdf->SetFont('Arial','B',11);
        $pdf->SetTextColor(32,100,210);
		$pdf->Cell(147,8,iconv("UTF-8", "ISO-8859-1",'TOTAL COMPRA (Bs): '),0,0,'R');
		$pdf->Cell(35,8,iconv("UTF-8", "ISO-8859-1",$str_total_bs),0,0,'R');

		$pdf->Ln(25);
		$pdf->SetFont('Arial','I',9);
        $pdf->SetTextColor(150,150,150);
		$pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","*** Este documento es un comprobante de registro de entrada de mercancía al inventario ***"),0,'C');

		$pdf->Output("I","Orden_Compra_".$datos_compra['compra_codigo'].".pdf",true);

	}else{
        echo "Orden de compra no encontrada.";
    }
?>