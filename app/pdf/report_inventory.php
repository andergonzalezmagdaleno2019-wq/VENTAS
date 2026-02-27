<?php
	$peticion_ajax = true;
	require_once "../../config/app.php";
    require_once "../../autoload.php";

	use app\controllers\productController;
	$ins_reporte = new productController();

	$datos_empresa = $ins_reporte->seleccionarDatos("Normal","empresa LIMIT 1","*",0);
	$datos_empresa = $datos_empresa->fetch();

	require "./code128.php";
	$pdf = new PDF_Code128('P','mm','Letter');
	$pdf->SetMargins(15,15,15);
	$pdf->AddPage();
	if(is_file('../views/img/logo.png')){ $pdf->Image('../views/img/logo.png',165,12,35,35,'PNG'); }

    // Encabezado
	$pdf->SetFont('Arial','B',16);
	$pdf->SetTextColor(32,100,210);
	$pdf->Cell(150,10,iconv("UTF-8", "ISO-8859-1",strtoupper($datos_empresa['empresa_nombre'])),0,0,'L');
	$pdf->Ln(9);
	$pdf->SetFont('Arial','',10);
	$pdf->SetTextColor(39,39,51);
	$pdf->Cell(150,9,iconv("UTF-8", "ISO-8859-1","RIF: ".$datos_empresa['empresa_rif']),0,0,'L');
	$pdf->Ln(5);
	$pdf->Cell(150,9,iconv("UTF-8", "ISO-8859-1",$datos_empresa['empresa_direccion']),0,0,'L');
	$pdf->Ln(15);

    /*---------- LÓGICA DE FILTROS ----------*/
    $cat = isset($_GET['categoria']) ? $_GET['categoria'] : 'todas';
    $est = isset($_GET['estado']) ? $_GET['estado'] : 'todos';
    $ord = isset($_GET['orden']) ? $_GET['orden'] : 'nombre_asc';
    $tasa_actual = isset($_GET['tasa']) ? $_GET['tasa'] : 0; // NUEVO

    $condiciones = [];
    if($cat != "todas") $condiciones[] = "categoria_id = '$cat'";
    if($est == "critico") $condiciones[] = "producto_stock <= 10 AND producto_stock > 0";
    elseif($est == "agotado") $condiciones[] = "producto_stock = 0";
    elseif($est == "disponible") $condiciones[] = "producto_stock > 0";

    $where = (count($condiciones) > 0) ? " WHERE " . implode(" AND ", $condiciones) : "";

    $order_by = " ORDER BY producto_nombre ASC";
    if($ord == "stock_desc") $order_by = " ORDER BY producto_stock DESC";
    elseif($ord == "stock_asc") $order_by = " ORDER BY producto_stock ASC";
    elseif($ord == "precio_desc") $order_by = " ORDER BY producto_precio DESC";

    // Título
    $pdf->SetFont('Arial','B',14);
	$pdf->Cell(0,10,iconv("UTF-8", "ISO-8859-1",'REPORTE DE INVENTARIO DETALLADO'),0,1,'C');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,6,iconv("UTF-8", "ISO-8859-1",'Fecha: '.date("d/m/Y h:i A")),0,1,'C');
    $pdf->Ln(5);

    // Cabecera Tabla
	$pdf->SetFillColor(32,100,210);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFont('Arial','B',9);
	$pdf->Cell(35,8,'Codigo',1,0,'C',true);
	$pdf->Cell(75,8,'Producto',1,0,'C',true);
	$pdf->Cell(20,8,'Stock',1,0,'C',true);
	$pdf->Cell(25,8,'Costo',1,0,'C',true);
	$pdf->Cell(30,8,'Total ($)',1,0,'C',true);
	$pdf->Ln(8);

	$productos = $ins_reporte->seleccionarDatos("Normal", "producto".$where.$order_by, "*", 0);
	$productos = $productos->fetchAll();

    $pdf->SetFont('Arial','',8);
	$pdf->SetTextColor(39,39,51);
    $total_valor = 0;

    foreach($productos as $row){
        $subtotal = $row['producto_stock'] * $row['producto_costo'];
        $pdf->Cell(35,7,$row['producto_codigo'],'L',0,'C');
        $pdf->Cell(75,7,substr($row['producto_nombre'],0,45),'L',0,'L');
        $pdf->Cell(20,7,$row['producto_stock'],'L',0,'C');
        $pdf->Cell(25,7,number_format($row['producto_costo'],2),'L',0,'C');
        $pdf->Cell(30,7,number_format($subtotal,2),'LR',0,'R');
        $pdf->Ln(7);
        $total_valor += $subtotal;
    }

    $total_valor_bs = $total_valor * $tasa_actual;
    $str_bs = ($tasa_actual > 0) ? 'Bs. '.number_format($total_valor_bs, 2, ',', '.') : 'Sin BCV';

	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(130,8,'VALOR TOTAL DEL INVENTARIO: ','T',0,'R');
	$pdf->Cell(55,8,MONEDA_SIMBOLO.number_format($total_valor,2).' | '.$str_bs,'T',0,'R');

	$pdf->Output("I","Reporte_Inventario.pdf",true);
?>