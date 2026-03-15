<?php
    ob_start();
    if (!function_exists('iconv')) {
        function iconv($in, $out, $str) { return utf8_decode($str); }
    }
    $peticion_ajax = true;
    $code = (isset($_GET['code'])) ? $_GET['code'] : 0;

    require_once "../../config/app.php";
    require_once "../../autoload.php";

    use app\controllers\purchaseController;
    $ins_compra = new purchaseController();

    $datos_compra = $ins_compra->seleccionarDatos("Normal","compra 
        INNER JOIN proveedor ON compra.proveedor_id=proveedor.proveedor_id 
        INNER JOIN usuario ON compra.usuario_id=usuario.usuario_id 
        WHERE compra_codigo='$code'","*",0);

    if($datos_compra->rowCount()==1){
        $datos_compra = $datos_compra->fetch();
        $datos_empresa = $ins_compra->seleccionarDatos("Normal","empresa LIMIT 1","*",0);
        $datos_empresa = $datos_empresa->fetch();

        require "./code128.php";
        $pdf = new PDF_Code128('P','mm','Letter');
        $pdf->SetMargins(17,17,17);
        $pdf->AddPage();
        
        if(is_file('../../views/img/logo.png')){ 
            $pdf->Image('../../views/img/logo.png',165,12,35,35,'PNG'); 
        }

        // Encabezado
        $pdf->SetFont('Arial','B',16);
        $pdf->SetTextColor(32,100,210);
        $pdf->Cell(150,10,iconv("UTF-8", "ISO-8859-1//TRANSLIT",strtoupper($datos_empresa['empresa_nombre'])),0,0,'L');
        $pdf->Ln(9);
        $pdf->SetFont('Arial','',10);
        $pdf->SetTextColor(39,39,51);
        $pdf->Cell(150,9,iconv("UTF-8", "ISO-8859-1//TRANSLIT","RIF: ".$datos_empresa['empresa_rif']),0,0,'L');
        $pdf->Ln(5);
        $pdf->Cell(150,9,iconv("UTF-8", "ISO-8859-1//TRANSLIT",$datos_empresa['empresa_direccion']),0,0,'L');
        $pdf->Ln(5);
        $pdf->Cell(150,9,iconv("UTF-8", "ISO-8859-1//TRANSLIT","Teléfono: ".$datos_empresa['empresa_telefono']),0,0,'L');
        $pdf->Ln(10);

        $pdf->SetFont('Arial','B',14);
        $pdf->SetTextColor(39,39,51);
        $pdf->Cell(0,10,iconv("UTF-8", "ISO-8859-1//TRANSLIT","COMPROBANTE DE ORDEN DE COMPRA"),0,1,'C');
        $pdf->Ln(2);

        // Datos de la Orden
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Fecha de registro:'),0,0);
        $pdf->SetTextColor(97,97,97);
        $pdf->Cell(111,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",date("d/m/Y", strtotime($datos_compra['compra_fecha']))),0,0,'L');
        $pdf->SetFont('Arial','B',10);
        $pdf->SetTextColor(39,39,51);
        $pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",strtoupper('CÓDIGO')),0,0,'C');
        $pdf->Ln(7);

        $pdf->SetFont('Arial','',10);
        $pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Registrado por:'),0,0,'L');
        $pdf->SetTextColor(97,97,97);
        $pdf->Cell(111,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",$datos_compra['usuario_nombre']." ".$datos_compra['usuario_apellido']),0,0,'L');
        $pdf->SetFont('Arial','B',10);
        $pdf->SetTextColor(97,97,97);
        $pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",strtoupper($datos_compra['compra_codigo'])),0,0,'C');
        $pdf->Ln(10);

        // Proveedor
        $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(39,39,51); $pdf->Cell(20,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Proveedor:'),0,0);
        $pdf->SetFont('Arial','',10); $pdf->SetTextColor(97,97,97); $pdf->Cell(100,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",$datos_compra['proveedor_nombre']),0,0,'L');
        $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(39,39,51); $pdf->Cell(10,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT","RIF: "),0,0,'L');
        $pdf->SetFont('Arial','',10); $pdf->SetTextColor(97,97,97); $pdf->Cell(50,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",$datos_compra['proveedor_rif']),0,0,'L');
        $pdf->Ln(12);

        // Tabla
        $pdf->SetFillColor(32,100,210);
        $pdf->SetDrawColor(32,100,210);
        $pdf->SetTextColor(255,255,255);
        $pdf->Cell(100,8,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Descripción del Producto'),1,0,'C',true);
        $pdf->Cell(15,8,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Cant.'),1,0,'C',true);
        $pdf->Cell(32,8,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Costo ($)'),1,0,'C',true);
        $pdf->Cell(34,8,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Subtotal ($)'),1,0,'C',true);
        $pdf->Ln(8);

        $pdf->SetFont('Arial','',9);
        $pdf->SetTextColor(39,39,51);

        $id_compra = $datos_compra['compra_id'];
        $detalles = $ins_compra->seleccionarDatos("Normal","compra_detalle 
            INNER JOIN producto ON compra_detalle.producto_id=producto.producto_id 
            WHERE compra_id='$id_compra'","*",0);
        $detalles = $detalles->fetchAll();

        foreach($detalles as $row){
            $subtotal = $row['compra_detalle_cantidad'] * $row['compra_detalle_precio'];
            $pdf->Cell(100,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",$row['producto_nombre']),'L B',0,'L');
            $pdf->Cell(15,7,$row['compra_detalle_cantidad'],'L B',0,'C');
            $pdf->Cell(32,7,"$ ".number_format($row['compra_detalle_precio'], 2),'L B',0,'C');
            $pdf->Cell(34,7,"$ ".number_format($subtotal, 2),'L R B',0,'C');
            $pdf->Ln(7);
        }

        // --- LÓGICA DE TASA ---
        // Sincronizado con el nombre de columna 'compra_tasa_bcv'
        $tasa_bcv = (isset($datos_compra['compra_tasa_bcv']) && $datos_compra['compra_tasa_bcv'] > 0) ? $datos_compra['compra_tasa_bcv'] : 1;
        $total_usd = $datos_compra['compra_total'];
        $total_bs = $total_usd * $tasa_bcv;

        $pdf->Ln(7);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(147, 7, "TOTAL ORDEN (USD):", 0, 0, 'R');
        $pdf->Cell(34, 7, "$ ".number_format($total_usd, 2), 0, 1, 'C');

        $pdf->SetFont('Arial','',10);
        $str_tasa = ($tasa_bcv > 1) ? 'Bs. '.number_format($tasa_bcv, 2, ',', '.') : 'N/A';
        $pdf->SetTextColor(97,97,97);
        $pdf->Cell(147, 7, iconv("UTF-8", "ISO-8859-1//TRANSLIT", "Tasa BCV Aplicada: " . $str_tasa), 0, 0, 'R');
        $pdf->Cell(34, 7, "", 0, 1, 'C');

        $pdf->SetTextColor(32,100,210);
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(147, 7, iconv("UTF-8", "ISO-8859-1//TRANSLIT", "TOTAL EN BOLÍVARES (Bs):"), 0, 0, 'R');
        $pdf->Cell(34, 7, "Bs. ".number_format($total_bs, 2, ',', '.'), 0, 1, 'C');

        $pdf->Ln(10);
        $pdf->SetTextColor(39,39,51);
        $pdf->SetFont('Arial','I',9);
        // Ajustado a 'compra_nota' según el name del input
        if(!empty($datos_compra['compra_nota'])){
            $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1//TRANSLIT","Nota interna: ".$datos_compra['compra_nota']),0,'L');
        }

        ob_end_clean();
        $pdf->Output("I","Compra_".$code.".pdf",true);

    } else {
        echo "Error: No se encontró la orden de compra.";
    }