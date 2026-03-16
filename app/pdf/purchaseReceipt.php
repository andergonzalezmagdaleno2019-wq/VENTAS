<?php
    ob_start();
    if (!function_exists('iconv')) {
        function iconv($in, $out, $str) { return utf8_decode($str); }
    }
    $peticion_ajax=true;
    $id = (isset($_GET['id'])) ? $_GET['id'] : 0;

    /*---------- Incluyendo configuraciones ----------*/
    require_once "../../config/app.php";
    require_once "../../autoload.php";

    /*---------- Instancia al controlador compra ----------*/
    use app\controllers\purchaseController;
    $ins_compra = new purchaseController();

    // Consultamos datos de la compra, proveedor y usuario
    $datos_compra = $ins_compra->ejecutarConsulta("SELECT c.*, p.*, u.usuario_nombre, u.usuario_apellido 
        FROM compra c 
        INNER JOIN proveedor p ON c.proveedor_id = p.proveedor_id 
        INNER JOIN usuario u ON c.usuario_id = u.usuario_id 
        WHERE c.compra_id='$id'");

    if($datos_compra->rowCount()==1){

        $datos_compra = $datos_compra->fetch();
        
        // Obtenemos los datos de la empresa para el encabezado
        $datos_empresa = $ins_compra->ejecutarConsulta("SELECT * FROM empresa LIMIT 1");
        $datos_empresa = $datos_empresa->fetch();

        // Buscamos la última recepción registrada para esta compra
        $recepcion = $ins_compra->ejecutarConsulta("SELECT r.*, u.usuario_nombre, u.usuario_apellido 
            FROM recepcion r 
            INNER JOIN usuario u ON r.usuario_id=u.usuario_id 
            WHERE r.compra_id='$id' 
            ORDER BY r.recepcion_id DESC LIMIT 1");

        if($recepcion->rowCount() >= 1){
            $recepcion = $recepcion->fetch();
            $re_id = $recepcion['recepcion_id'];

            require "./code128.php";

            $pdf = new PDF_Code128('P','mm','Letter');
            $pdf->SetMargins(17,17,17);
            $pdf->AddPage();

            // Logo de la empresa
            if(is_file('../views/img/logo.png')){ 
                $pdf->Image('../views/img/logo.png',165,12,35,35,'PNG'); 
            }

            // Datos de la Empresa (Estilo Azul)
            $pdf->SetFont('Arial','B',16);
            $pdf->SetTextColor(32,100,210);
            $pdf->Cell(150,10,iconv("UTF-8", "ISO-8859-1//TRANSLIT",strtoupper($datos_empresa['empresa_nombre'])),0,0,'L');
            $pdf->Ln(9);

            $pdf->SetFont('Arial','',10);
            $pdf->SetTextColor(39,39,51);
            $rif_empresa = isset($datos_empresa['empresa_rif']) ? $datos_empresa['empresa_rif'] : "";
            $pdf->Cell(150,9,iconv("UTF-8", "ISO-8859-1//TRANSLIT","RIF: ".$rif_empresa),0,0,'L');
            $pdf->Ln(5);
            $pdf->Cell(150,9,iconv("UTF-8", "ISO-8859-1//TRANSLIT",$datos_empresa['empresa_direccion']),0,0,'L');
            $pdf->Ln(5);
            $pdf->Cell(150,9,iconv("UTF-8", "ISO-8859-1//TRANSLIT","Teléfono: ".$datos_empresa['empresa_telefono']),0,0,'L');
            $pdf->Ln(10);

            // TÍTULO: FACTURA DE COMPRA RECIBIDA
            $pdf->SetFont('Arial','B',14);
            $pdf->SetTextColor(39,39,51);
            $pdf->Cell(0,10,iconv("UTF-8", "ISO-8859-1//TRANSLIT","FACTURA DE COMPRA RECIBIDA"),0,1,'C');
            $pdf->Ln(2);

            // Información de la Recepción
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Fecha recepción:'),0,0);
            $pdf->SetTextColor(97,97,97);
            $pdf->Cell(111,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",date("d/m/Y", strtotime($recepcion['recepcion_fecha']))),0,0,'L');
            
            $pdf->SetFont('Arial','B',10);
            $pdf->SetTextColor(39,39,51);
            $pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",strtoupper('CÓDIGO COMPRA')),0,0,'C');
            $pdf->Ln(7);

            $pdf->SetFont('Arial','',10);
            $pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Recibido por:'),0,0,'L');
            $pdf->SetTextColor(97,97,97);
            $pdf->Cell(111,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",$recepcion['usuario_nombre']." ".$recepcion['usuario_apellido']),0,0,'L');
            
            $pdf->SetFont('Arial','B',10);
            $pdf->SetTextColor(97,97,97);
            $pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",strtoupper($datos_compra['compra_codigo'])),0,0,'C');
            $pdf->Ln(10);

            // Datos del Proveedor
            $pdf->SetFont('Arial','',10); $pdf->SetTextColor(39,39,51); $pdf->Cell(20,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Proveedor:'),0,0);
            $pdf->SetTextColor(97,97,97); $pdf->Cell(60,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",$datos_compra['proveedor_nombre']),0,0,'L');
            $pdf->SetTextColor(39,39,51); $pdf->Cell(10,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT","RIF: "),0,0,'L');
            $pdf->SetTextColor(97,97,97); $pdf->Cell(50,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",$datos_compra['proveedor_rif']),0,0,'L');
            $pdf->Ln(7);
            $pdf->SetTextColor(39,39,51); $pdf->Cell(18,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Dirección:'),0,0);
            $pdf->SetTextColor(97,97,97); $pdf->Cell(109,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",$datos_compra['proveedor_direccion']),0,0);
            $pdf->Ln(7);

            /* --- SECCIÓN NUEVA: FACTURAS DEL PROVEEDOR --- */
            $pdf->SetTextColor(39,39,51); 
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(38,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Facturas Proveedor:'),0,0);
            
            // Consultamos los números registrados en compra_factura
            $query_facturas = $ins_compra->ejecutarConsulta("SELECT factura_numero FROM compra_factura WHERE compra_id='$id'");
            $facturas_lista = $query_facturas->fetchAll(PDO::FETCH_COLUMN);
            
            $pdf->SetFont('Arial','',10);
            $pdf->SetTextColor(32,100,210); // Color azul distintivo
            $texto_facturas = (count($facturas_lista) > 0) ? implode(", ", $facturas_lista) : "N/A";
            $pdf->Cell(100,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT", $texto_facturas),0,0);
            /* --- FIN SECCIÓN NUEVA --- */

            $pdf->Ln(9);

            // Encabezado de Tabla (CON COSTOS RECUPERADOS)
            $pdf->SetFillColor(23,83,201);
            $pdf->SetDrawColor(23,83,201);
            $pdf->SetTextColor(255,255,255);
            $pdf->Cell(85,8,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Descripción del Producto'),1,0,'C',true);
            $pdf->Cell(25,8,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Recibida'),1,0,'C',true);
            $pdf->Cell(35,8,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Costo Unit.'),1,0,'C',true);
            $pdf->Cell(36,8,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Subtotal'),1,0,'C',true);
            $pdf->Ln(8);

            // Detalles de la Recepción
            $pdf->SetFont('Arial','',9);
            $pdf->SetTextColor(39,39,51);

            $detalles = $ins_compra->ejecutarConsulta("SELECT rd.*, p.producto_nombre, p.producto_marca, p.producto_modelo, cd.compra_detalle_precio 
                FROM recepcion_detalle rd 
                INNER JOIN producto p ON rd.producto_id=p.producto_id 
                INNER JOIN compra_detalle cd ON rd.producto_id=cd.producto_id AND cd.compra_id='$id'
                WHERE rd.recepcion_id='$re_id'");
            
            $detalles_filas = $detalles->fetchAll();

            if(count($detalles_filas) > 0){
                foreach($detalles_filas as $item){
                    $descripcion = $item['producto_nombre'];
                    $marca_modelo = trim(($item['producto_marca'] ?? "") . " " . ($item['producto_modelo'] ?? ""));
                    if($marca_modelo != ""){ $descripcion .= " - " . $marca_modelo; }

                    $cantidad = (isset($item['cantidad_recibida'])) ? $item['cantidad_recibida'] : 0;
                    $precio_unitario = (isset($item['compra_detalle_precio'])) ? $item['compra_detalle_precio'] : 0;
                    $subtotal_item = $cantidad * $precio_unitario;

                    $x_pos = $pdf->GetX();
                    $y_pos = $pdf->GetY();
                    $pdf->MultiCell(85,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",$descripcion),'L B','L');
                    $new_y = $pdf->GetY();
                    $h_row = $new_y - $y_pos;
                    $pdf->SetXY($x_pos + 85, $y_pos);
                    
                    $pdf->Cell(25,$h_row,$cantidad,'L R B',0,'C');
                    $pdf->Cell(35,$h_row,"$" . number_format($precio_unitario,2,'.',','),'L R B',0,'C');
                    $pdf->Cell(36,$h_row,"$" . number_format($subtotal_item,2,'.',','),'L R B',0,'R');
                    $pdf->Ln($h_row);
                }
            } else {
                $pdf->SetTextColor(200,0,0);
                $pdf->Cell(181,10,iconv("UTF-8", "ISO-8859-1//TRANSLIT","No se encontraron productos en el detalle (ID: $re_id)"),1,1,'C');
            }

            // TOTALES DE LA FACTURA
            $pdf->Ln(5);
            $pdf->SetFont('Arial','B',11);
            $pdf->SetTextColor(39,39,51); 
            $pdf->Cell(145,8,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'TOTAL FACTURADO ($): '),0,0,'R');
            $pdf->Cell(36,8,iconv("UTF-8", "ISO-8859-1//TRANSLIT","$" . number_format($datos_compra['compra_total'],2,'.',',')),0,0,'R');

            $tasa_bcv = (isset($datos_compra['compra_tasa_bcv']) && $datos_compra['compra_tasa_bcv'] > 0) ? $datos_compra['compra_tasa_bcv'] : 0;
            if($tasa_bcv > 0){
                $total_bs = $datos_compra['compra_total'] * $tasa_bcv;
                $pdf->Ln(7);
                $pdf->SetFont('Arial','B',10);
                $pdf->Cell(145,8,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'TASA BCV OFICIAL: '),0,0,'R');
                $pdf->Cell(36,8,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Bs. '.number_format($tasa_bcv, 2, ',', '.')),0,0,'R');

                $pdf->Ln(7);
                $pdf->SetFont('Arial','B',11);
                $pdf->SetTextColor(32,100,210);
                $pdf->Cell(145,8,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'TOTAL FACTURADO (Bs): '),0,0,'R');
                $pdf->Cell(36,8,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Bs. '.number_format($total_bs, 2, ',', '.')),0,0,'R');
            }

            // Espacio para firmas
            if($pdf->GetY() > 220){ $pdf->AddPage(); }
            
            $pdf->Ln(25);
            $pdf->SetTextColor(39,39,51);
            $pdf->Cell(85,0,'',1,0);
            $pdf->Cell(11,0,'',0,0);
            $pdf->Cell(85,0,'',1,1);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(85,7,"Firma Almacen (Recibe)",0,0,'C');
            $pdf->Cell(11,7,'',0,0);
            $pdf->Cell(85,7,"Firma Proveedor (Entrega)",0,1,'C');

            $pdf->SetFont('Arial','',8);
            $pdf->SetY(-20);
            $pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1//TRANSLIT","*** Este documento certifica la entrada física de mercancía al inventario con sus respectivos costos. ***"),0,0,'C');

            ob_end_clean();
            $pdf->Output("I","Factura_Recibida_".$datos_compra['compra_codigo'].".pdf",true);

        } else {
            echo "No hay recepciones registradas para esta compra.";
        }
    } else {
        echo "Error: Compra no encontrada.";
    }
?>