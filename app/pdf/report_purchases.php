<?php
	$peticion_ajax = true;
    
    // Recibir fechas del formulario
	$fecha_inicio = (isset($_GET['fecha_inicio'])) ? $_GET['fecha_inicio'] : date("Y-m-d");
	$fecha_fin = (isset($_GET['fecha_fin'])) ? $_GET['fecha_fin'] : date("Y-m-d");

	/*---------- Incluyendo configuraciones ----------*/
	require_once "../../config/app.php";
    require_once "../../autoload.php";

    /*---------- Instancia al controlador ----------*/
	use app\controllers\purchaseController;
	$ins_reporte = new purchaseController();

	/*---------- Seleccion de datos de la empresa ----------*/
	$datos_empresa = $ins_reporte->seleccionarDatos("Normal","empresa LIMIT 1","*",0);
	$datos_empresa = $datos_empresa->fetch();

	require "./code128.php";
	$pdf = new PDF_Code128('P','mm','Letter');
	$pdf->SetMargins(15,15,15);
	$pdf->AddPage();
	if(is_file('../views/img/logo.png')){ $pdf->Image('../views/img/logo.png',165,12,35,35,'PNG'); }

    // Encabezado de la empresa
	$pdf->SetFont('Arial','B',16);
	$pdf->SetTextColor(32,100,210);
	$pdf->Cell(150,10,iconv("UTF-8", "ISO-8859-1",strtoupper($datos_empresa['empresa_nombre'])),0,0,'L');
	$pdf->Ln(9);
	$pdf->SetFont('Arial','',10);
	$pdf->SetTextColor(39,39,51);
	$pdf->Cell(150,9,iconv("UTF-8", "ISO-8859-1",$datos_empresa['empresa_direccion']),0,0,'L');
	$pdf->Ln(5);
	$pdf->Cell(150,9,iconv("UTF-8", "ISO-8859-1","Teléfono: ".$datos_empresa['empresa_telefono']),0,0,'L');
	$pdf->Ln(5);
    $pdf->Cell(150,9,iconv("UTF-8", "ISO-8859-1","RIF: ".$datos_empresa['empresa_rif']),0,0,'L');
    $pdf->Ln(15);

    // Título del Reporte
    $pdf->SetFont('Arial','B',14);
	$pdf->SetTextColor(39,39,51);
	$pdf->Cell(0,10,iconv("UTF-8", "ISO-8859-1",strtoupper('REPORTE GENERAL DE COMPRAS')),0,1,'C');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,7,iconv("UTF-8", "ISO-8859-1",'Desde: '.date("d/m/Y", strtotime($fecha_inicio)).'  Hasta: '.date("d/m/Y", strtotime($fecha_fin))),0,1,'C');
    $pdf->Ln(5);

    // Tabla Cabecera
	$pdf->SetFillColor(32,100,210);
	$pdf->SetDrawColor(32,100,210);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFont('Arial','B',9);
	$pdf->Cell(10,8,iconv("UTF-8", "ISO-8859-1",'N°'),1,0,'C',true);
	$pdf->Cell(25,8,iconv("UTF-8", "ISO-8859-1",'Fecha'),1,0,'C',true);
	$pdf->Cell(35,8,iconv("UTF-8", "ISO-8859-1",'Código'),1,0,'C',true);
	$pdf->Cell(50,8,iconv("UTF-8", "ISO-8859-1",'Proveedor'),1,0,'C',true);
    $pdf->Cell(20,8,iconv("UTF-8", "ISO-8859-1",'Tasa'),1,0,'C',true);
	$pdf->Cell(20,8,iconv("UTF-8", "ISO-8859-1",'Total ($)'),1,0,'C',true);
    $pdf->Cell(25,8,iconv("UTF-8", "ISO-8859-1",'Total (Bs)'),1,0,'C',true);
	$pdf->Ln(8);

	/*---------- Consultar Compras en Rango de Fechas ----------*/
    $tabla_consulta = "compra INNER JOIN proveedor ON compra.proveedor_id=proveedor.proveedor_id WHERE compra_fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' ORDER BY compra_fecha ASC, compra_id ASC";
    $campos_consulta = "compra.compra_id, compra.compra_codigo, compra.compra_fecha, compra.compra_total, compra.compra_tasa_bcv, proveedor.proveedor_nombre";
                 
	$compras = $ins_reporte->seleccionarDatos("Normal", $tabla_consulta, $campos_consulta, 0);
	$compras = $compras->fetchAll();

    $pdf->SetFont('Arial','',9);
	$pdf->SetTextColor(39,39,51);
    
    $total_general_usd = 0;
    $total_general_bs = 0;
    $contador = 1;

    if(count($compras) > 0){
        foreach($compras as $row){
            
            $tasa = (isset($row['compra_tasa_bcv']) && $row['compra_tasa_bcv'] > 0) ? $row['compra_tasa_bcv'] : 0;
            $total_bs = $row['compra_total'] * $tasa;
            
            $str_tasa = ($tasa > 0) ? number_format($tasa, 2, ',', '.') : 'N/A';
            $str_bs = ($tasa > 0) ? number_format($total_bs, 2, ',', '.') : 'N/A';

            $pdf->Cell(10,7,iconv("UTF-8", "ISO-8859-1",$contador),'L',0,'C');
            $pdf->Cell(25,7,iconv("UTF-8", "ISO-8859-1",date("d/m/Y", strtotime($row['compra_fecha']))),'L',0,'C');
            $pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1",$row['compra_codigo']),'L',0,'C');
            $pdf->Cell(50,7,iconv("UTF-8", "ISO-8859-1", substr($row['proveedor_nombre'], 0, 25)),'L',0,'L');
            $pdf->Cell(20,7,iconv("UTF-8", "ISO-8859-1",$str_tasa),'L',0,'C');
            $pdf->Cell(20,7,iconv("UTF-8", "ISO-8859-1",number_format($row['compra_total'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)),'L',0,'R');
            $pdf->Cell(25,7,iconv("UTF-8", "ISO-8859-1",$str_bs),'LR',0,'R');
            $pdf->Ln(7);
            
            $total_general_usd += $row['compra_total'];
            $total_general_bs += $total_bs;
            $contador++;
        }
    }else{
        $pdf->Cell(185,10,iconv("UTF-8", "ISO-8859-1","No se encontraron compras en este rango de fechas."),'LBR',0,'C');
        $pdf->Ln(10);
    }

    // Pie de Tabla con Totales
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(140,8,iconv("UTF-8", "ISO-8859-1",'TOTAL INVERTIDO EN EL PERIODO:'),'T',0,'R');
	$pdf->Cell(20,8,iconv("UTF-8", "ISO-8859-1",MONEDA_SIMBOLO.number_format($total_general_usd,MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)),'T',0,'R');
    $pdf->SetTextColor(32,100,210); // Resaltar Bolívares
    $pdf->Cell(25,8,iconv("UTF-8", "ISO-8859-1",'Bs. '.number_format($total_general_bs,2,',','.')),'T',0,'R');
	$pdf->Ln(12);

    $pdf->SetFont('Arial','',9);
    $pdf->SetTextColor(97,97,97);
    $pdf->Cell(0,7,iconv("UTF-8", "ISO-8859-1",'Reporte generado por el sistema el '.date("d/m/Y h:i A")),0,1,'C');

	$pdf->Output("I","Reporte_Compras_".$fecha_inicio."_al_".$fecha_fin.".pdf",true);
?>