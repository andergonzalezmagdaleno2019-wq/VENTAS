<?php
    $peticion_ajax = true;
    
    // 1. Recibir parámetros del formulario
    $fecha_inicio = (isset($_GET['fecha_inicio'])) ? $_GET['fecha_inicio'] : date("Y-m-d");
    $fecha_fin = (isset($_GET['fecha_fin'])) ? $_GET['fecha_fin'] : date("Y-m-d");
    $vendedor = (isset($_GET['reporte_vendedor'])) ? $_GET['reporte_vendedor'] : "all";
    $pago = (isset($_GET['reporte_pago'])) ? $_GET['reporte_pago'] : "all";

    /*---------- Incluyendo configuraciones ----------*/
    require_once "../../config/app.php";
    require_once "../../autoload.php";

    use app\controllers\saleController;
    $ins_reporte = new saleController();

    /*---------- Seleccion de datos de la empresa ----------*/
    $datos_empresa = $ins_reporte->seleccionarDatos("Normal","empresa LIMIT 1","*",0);
    $datos_empresa = $datos_empresa->fetch();

    /* --- Formatos de Teléfono y RIF --- */
    $emp_tel = $datos_empresa['empresa_telefono'];
    $emp_tel_format = (strlen($emp_tel) == 11) ? substr($emp_tel, 0, 4)."-".substr($emp_tel, 4) : $emp_tel;
    $emp_rif = $datos_empresa['empresa_rif'];
    if(preg_match('/^[0-9]/', $emp_rif)){ $emp_rif = "J-" . $emp_rif; }

    require "./code128.php";
    $pdf = new PDF_Code128('P','mm','Letter');
    $pdf->SetMargins(15,15,15);
    $pdf->AddPage();
    
    // Logo
    if(is_file('../views/img/logo.png')){ 
        $pdf->Image('../views/img/logo.png',165,12,35,35,'PNG'); 
    }

    // Encabezado de Empresa
    $pdf->SetFont('Arial','B',16);
    $pdf->SetTextColor(32,100,210);
    $pdf->Cell(150,10,iconv("UTF-8", "ISO-8859-1",strtoupper($datos_empresa['empresa_nombre'])),0,0,'L');
    $pdf->Ln(9);
    $pdf->SetFont('Arial','',10);
    $pdf->SetTextColor(39,39,51);
    $pdf->Cell(150,9,iconv("UTF-8", "ISO-8859-1",$datos_empresa['empresa_direccion']),0,0,'L');
    $pdf->Ln(5);
    $pdf->Cell(150,9,iconv("UTF-8", "ISO-8859-1","Teléfono: ".$emp_tel_format),0,0,'L');
    $pdf->Ln(5);
    $pdf->Cell(150,9,iconv("UTF-8", "ISO-8859-1","RIF: ".$emp_rif),0,0,'L');
    $pdf->Ln(15);

    // Título y Filtros
    $pdf->SetFont('Arial','B',14);
    $pdf->SetTextColor(39,39,51);
    $pdf->Cell(0,10,iconv("UTF-8", "ISO-8859-1",strtoupper('REPORTE DETALLADO DE VENTAS')),0,1,'C');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,7,iconv("UTF-8", "ISO-8859-1",'Desde: '.date("d/m/Y", strtotime($fecha_inicio)).'  Hasta: '.date("d/m/Y", strtotime($fecha_fin))),0,1,'C');
    
    $texto_filtros = "Vendedor: " . (($vendedor == "all") ? "Todos" : "Filtrado");
    $texto_filtros .= " | Pago: " . (($pago == "all") ? "Todos" : $pago);
    $pdf->SetFont('Arial','I',9);
    $pdf->Cell(0,7,iconv("UTF-8", "ISO-8859-1", $texto_filtros),0,1,'C');
    $pdf->Ln(5);

    // Cabecera de Tabla (Anchos optimizados)
    $pdf->SetFillColor(32,100,210);
    $pdf->SetDrawColor(32,100,210);
    $pdf->SetTextColor(255,255,255);
    $pdf->SetFont('Arial','B',8);
    
    $pdf->Cell(10,8,iconv("UTF-8", "ISO-8859-1",'N°'),1,0,'C',true);
    $pdf->Cell(20,8,iconv("UTF-8", "ISO-8859-1",'Fecha'),1,0,'C',true);
    $pdf->Cell(28,8,iconv("UTF-8", "ISO-8859-1",'Código'),1,0,'C',true);
    $pdf->Cell(35,8,iconv("UTF-8", "ISO-8859-1",'Vendedor'),1,0,'C',true);
    $pdf->Cell(25,8,iconv("UTF-8", "ISO-8859-1",'Pago'),1,0,'C',true);
    $pdf->Cell(25,8,iconv("UTF-8", "ISO-8859-1",'Referencia'),1,0,'C',true);
    $pdf->Cell(20,8,iconv("UTF-8", "ISO-8859-1",'Total ($)'),1,0,'C',true);
    $pdf->Cell(22,8,iconv("UTF-8", "ISO-8859-1",'Total (Bs)'),1,0,'C',true);
    $pdf->Ln(8);

    /*---------- Construcción de Consulta Dinámica ----------*/
    $filtro_adicional = "";
    if($vendedor != "all"){ $filtro_adicional .= " AND venta.usuario_id='$vendedor'"; }
    if($pago != "all"){ $filtro_adicional .= " AND venta.venta_metodo_pago='$pago'"; } 

    $tabla_consulta = "venta INNER JOIN usuario ON venta.usuario_id=usuario.usuario_id WHERE (venta_fecha BETWEEN '$fecha_inicio' AND '$fecha_fin') $filtro_adicional ORDER BY venta_fecha ASC, venta_id ASC";
    $campos_consulta = "venta.*, usuario.usuario_nombre, usuario.usuario_apellido";
                
    $ventas = $ins_reporte->seleccionarDatos("Normal", $tabla_consulta, $campos_consulta, 0);
    $ventas = $ventas->fetchAll();

    $pdf->SetFont('Arial','',8);
    $pdf->SetTextColor(39,39,51);
    
    $total_general_usd = 0;
    $total_general_bs = 0;
    $contador = 1;

    if(count($ventas) > 0){
        foreach($ventas as $row){
            $tasa = (isset($row['venta_tasa_bcv']) && $row['venta_tasa_bcv'] > 0) ? $row['venta_tasa_bcv'] : 0;
            $total_bs = $row['venta_total'] * $tasa;
            
            $tipo_pago = (isset($row['venta_metodo_pago'])) ? $row['venta_metodo_pago'] : "N/R";
            $referencia = (isset($row['venta_referencia']) && $row['venta_referencia'] != "") ? $row['venta_referencia'] : "S/R";

            $pdf->Cell(10,7,iconv("UTF-8", "ISO-8859-1",$contador),'L',0,'C');
            $pdf->Cell(20,7,iconv("UTF-8", "ISO-8859-1",date("d/m/Y", strtotime($row['venta_fecha']))),'L',0,'C');
            $pdf->Cell(28,7,iconv("UTF-8", "ISO-8859-1",$row['venta_codigo']),'L',0,'C');
            $pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1", substr($row['usuario_nombre']." ".$row['usuario_apellido'], 0, 18)),'L',0,'L');
            $pdf->Cell(25,7,iconv("UTF-8", "ISO-8859-1",$tipo_pago),'L',0,'C');
            $pdf->Cell(25,7,iconv("UTF-8", "ISO-8859-1",$referencia),'L',0,'C');
            $pdf->Cell(20,7,iconv("UTF-8", "ISO-8859-1",number_format($row['venta_total'],2,'.',',')),'L',0,'R');
            $pdf->Cell(22,7,iconv("UTF-8", "ISO-8859-1",number_format($total_bs,2,',','.')),'LR',0,'R');
            $pdf->Ln(7);
            
            $total_general_usd += $row['venta_total'];
            $total_general_bs += $total_bs;
            $contador++;
        }
    } else {
        $pdf->Cell(185,10,iconv("UTF-8", "ISO-8859-1","No se encontraron registros con los filtros aplicados."),'LBR',0,'C');
        $pdf->Ln(10);
    }

    // Pie de Tabla - Totales
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(143,8,iconv("UTF-8", "ISO-8859-1",'TOTAL VENDIDO EN EL PERIODO:'),'T',0,'R');
    $pdf->Cell(20,8,iconv("UTF-8", "ISO-8859-1",'$'.number_format($total_general_usd,2)),'T',0,'R');
    $pdf->SetTextColor(32,100,210);
    $pdf->Cell(22,8,iconv("UTF-8", "ISO-8859-1",'Bs.'.number_format($total_general_bs,2,',','.')),'T',0,'R');
    $pdf->Ln(12);

    $pdf->SetFont('Arial','',9);
    $pdf->SetTextColor(97,97,97);
    $pdf->Cell(0,7,iconv("UTF-8", "ISO-8859-1",'Reporte generado por FastNet el '.date("d/m/Y h:i A")),0,1,'C');

    $pdf->Output("I","Reporte_Ventas_Completo.pdf",true);