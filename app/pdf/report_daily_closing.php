<?php
	$peticion_ajax = true;

	/*---------- 1. Incluyendo configuraciones PRIMERO ----------*/
	require_once "../../config/app.php";
    require_once "../../autoload.php";

    /*---------- 2. INICIAMOS SESIÓN ----------*/
    session_name(APP_SESSION_NAME);
    session_start();

    /*---------- 3. AHORA SÍ capturamos la fecha local ----------*/
	$fecha_hoy = date("Y-m-d");

    /*---------- Instancia al controlador ----------*/
	use app\controllers\saleController;
	$ins_reporte = new saleController();

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
    
    if(isset($_SESSION['rol']) && $_SESSION['rol'] == 2){
        $pdf->Cell(0,10,iconv("UTF-8", "ISO-8859-1",strtoupper('MI CIERRE DE CAJA DIARIO')),0,1,'C');
    } else {
        $pdf->Cell(0,10,iconv("UTF-8", "ISO-8859-1",strtoupper('REPORTE DE CIERRE DIARIO (GENERAL)')),0,1,'C');
    }

    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,7,iconv("UTF-8", "ISO-8859-1",'Fecha del Cuadre: '.date("d/m/Y")),0,1,'C');
    $pdf->Ln(5);

    // Tabla Cabecera
	$pdf->SetFillColor(32,100,210);
	$pdf->SetDrawColor(32,100,210); // Aquí el pincel se moja en azul
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFont('Arial','B',9);
	$pdf->Cell(15,8,iconv("UTF-8", "ISO-8859-1",'Hora'),1,0,'C',true);
	$pdf->Cell(25,8,iconv("UTF-8", "ISO-8859-1",'Código'),1,0,'C',true);
	$pdf->Cell(60,8,iconv("UTF-8", "ISO-8859-1",'Vendedor'),1,0,'C',true);
    $pdf->Cell(20,8,iconv("UTF-8", "ISO-8859-1",'Tasa'),1,0,'C',true);
	$pdf->Cell(30,8,iconv("UTF-8", "ISO-8859-1",'Total ($)'),1,0,'C',true);
    $pdf->Cell(35,8,iconv("UTF-8", "ISO-8859-1",'Total (Bs)'),1,0,'C',true);
	$pdf->Ln(8);

    // =========================================================================
    // SOLUCIÓN: Limpiamos el pincel y lo pasamos a un color gris oscuro/negro
    // =========================================================================
    $pdf->SetDrawColor(39,39,51); 

	/*---------- Consultar Ventas ----------*/
    $condicion_rol = "";
    if(isset($_SESSION['rol']) && $_SESSION['rol'] == 2){
        $condicion_rol = " AND venta.usuario_id='".$_SESSION['id']."'";
    }

    $tabla_consulta = "venta INNER JOIN usuario ON venta.usuario_id=usuario.usuario_id WHERE venta_fecha = '$fecha_hoy'".$condicion_rol." ORDER BY venta_hora ASC, venta_id ASC";
    $campos_consulta = "venta.venta_id, venta.venta_codigo, venta.venta_hora, venta.venta_total, venta.venta_tasa_bcv, usuario.usuario_nombre, usuario.usuario_apellido";
                 
	$ventas = $ins_reporte->seleccionarDatos("Normal", $tabla_consulta, $campos_consulta, 0);
	$ventas = $ventas->fetchAll();

    $pdf->SetFont('Arial','',9);
	$pdf->SetTextColor(39,39,51);
    
    $total_general_usd = 0;
    $total_general_bs = 0;

    if(count($ventas) > 0){
        foreach($ventas as $row){
            $tasa = (isset($row['venta_tasa_bcv']) && $row['venta_tasa_bcv'] > 0) ? $row['venta_tasa_bcv'] : 0;
            $total_bs = $row['venta_total'] * $tasa;
            $str_tasa = ($tasa > 0) ? number_format($tasa, 2, ',', '.') : 'N/A';
            $str_bs = ($tasa > 0) ? number_format($total_bs, 2, ',', '.') : 'N/A';
            $nombre_completo = $row['usuario_nombre']." ".$row['usuario_apellido'];
            
            $pdf->Cell(15,7,iconv("UTF-8", "ISO-8859-1", date("h:i A", strtotime($row['venta_hora']))),'L',0,'C');
            $pdf->Cell(25,7,iconv("UTF-8", "ISO-8859-1",$row['venta_codigo']),'L',0,'C');
            $pdf->Cell(60,7,iconv("UTF-8", "ISO-8859-1", substr($nombre_completo, 0, 40)),'L',0,'L');
            $pdf->Cell(20,7,iconv("UTF-8", "ISO-8859-1",$str_tasa),'L',0,'C');
            $pdf->Cell(30,7,iconv("UTF-8", "ISO-8859-1",number_format($row['venta_total'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)),'L',0,'R');
            $pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1",$str_bs),'LR',0,'R');
            $pdf->Ln(7);
            
            $total_general_usd += $row['venta_total'];
            $total_general_bs += $total_bs;
        }
        
        // Esta línea ahora saldrá gris oscura en lugar de azul
        $pdf->Line(15, $pdf->GetY(), 200, $pdf->GetY());
        
    }else{
        $pdf->Cell(185,10,iconv("UTF-8", "ISO-8859-1","No se registraron ventas en el día de hoy."),'LBR',0,'C');
    }

    // =========================================================================
    // TOTALES Y FECHA AL FONDO DEL DOCUMENTO
    // =========================================================================
    
    if($pdf->GetY() > 235){
        $pdf->AddPage();
    }
    
    $pdf->SetAutoPageBreak(false);
    $pdf->SetY(-35);

    // Esta línea decorativa también saldrá gris oscura
    $pdf->Line(15, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(2);

    // Totales
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(120,8,iconv("UTF-8", "ISO-8859-1",'TOTAL VENTAS DEL DÍA:'),0,0,'R');
	$pdf->Cell(30,8,iconv("UTF-8", "ISO-8859-1",MONEDA_SIMBOLO.number_format($total_general_usd,MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)),0,0,'R');
    $pdf->SetTextColor(32,100,210);
    $pdf->Cell(35,8,iconv("UTF-8", "ISO-8859-1",'Bs. '.number_format($total_general_bs,2,',','.')),0,0,'R');
	$pdf->Ln(10);

    // Fecha de generación 
    $pdf->SetFont('Arial','',9);
    $pdf->SetTextColor(97,97,97);
    $pdf->Cell(0,7,iconv("UTF-8", "ISO-8859-1",'Reporte generado por el sistema el '.date("d/m/Y h:i A")),0,1,'C');

	$pdf->Output("I","Cuadre_Diario_".date("Y-m-d").".pdf",true);
?>