<?php
    ob_start();
    if (!function_exists('iconv')) {
        function iconv($in, $out, $str) { return utf8_decode($str); }
    }
    $peticion_ajax=true;
    $id = (isset($_GET['id'])) ? $_GET['id'] : 0;

    require_once "../../config/app.php";
    require_once "../../autoload.php";

    use app\controllers\purchaseController;
    $ins_compra = new purchaseController();

    $datos_compra = $ins_compra->ejecutarConsulta("SELECT c.*, p.*, u.usuario_nombre, u.usuario_apellido 
        FROM compra c 
        INNER JOIN proveedor p ON c.proveedor_id = p.proveedor_id 
        INNER JOIN usuario u ON c.usuario_id = u.usuario_id 
        WHERE c.compra_id='$id'");

    if($datos_compra->rowCount()==1){

        $datos_compra = $datos_compra->fetch();
        $datos_empresa = $ins_compra->ejecutarConsulta("SELECT * FROM empresa LIMIT 1");
        $datos_empresa = $datos_empresa->fetch();

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

            if(is_file('../views/img/logo.png')){ 
                $pdf->Image('../views/img/logo.png',165,12,32,32,'PNG'); 
            }

            // Datos Empresa
            $pdf->SetFont('Arial','B',16);
            $pdf->SetTextColor(32,100,210);
            $pdf->Cell(150,10,iconv("UTF-8", "ISO-8859-1//TRANSLIT",strtoupper($datos_empresa['empresa_nombre'])),0,0,'L');
            $pdf->Ln(9);
            $pdf->SetFont('Arial','',10);
            $pdf->SetTextColor(39,39,51);
            $pdf->Cell(150,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT","RIF: ".$datos_empresa['empresa_rif']),0,1,'L');
            $pdf->Cell(150,5,iconv("UTF-8", "ISO-8859-1//TRANSLIT",$datos_empresa['empresa_direccion']),0,1,'L');
            $pdf->Ln(10);

            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(0,10,iconv("UTF-8", "ISO-8859-1//TRANSLIT","NOTA DE COMPRA RECIBIDA"),0,1,'C');
            $pdf->Ln(5);

            // Información Recepción
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Fecha:'),0,0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(100,7,date("d/m/Y", strtotime($recepcion['recepcion_fecha'])),0,1);
            
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Proveedor:'),0,0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(100,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",$datos_compra['proveedor_nombre']),0,1);

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(35,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Facturas:'),0,0);
            $query_facturas = $ins_compra->ejecutarConsulta("SELECT factura_numero FROM compra_factura WHERE compra_id='$id'");
            $facturas_lista = $query_facturas->fetchAll(PDO::FETCH_COLUMN);
            $pdf->SetFont('Arial','',10);
            $pdf->SetTextColor(32,100,210);
            $pdf->Cell(100,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT", (count($facturas_lista) > 0 ? implode(", ", $facturas_lista) : "N/A")),0,1);
            $pdf->Ln(5);

            // Tabla
            $pdf->SetFillColor(23,83,201);
            $pdf->SetDrawColor(23,83,201);
            $pdf->SetTextColor(255,255,255);
            $pdf->Cell(85,8,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Descripción'),1,0,'C',true);
            $pdf->Cell(25,8,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Cant.'),1,0,'C',true);
            $pdf->Cell(35,8,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Costo Unit.'),1,0,'C',true);
            $pdf->Cell(36,8,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Subtotal'),1,1,'C',true);

            $pdf->SetFont('Arial','',9);
            $pdf->SetTextColor(39,39,51);

            $detalles = $ins_compra->ejecutarConsulta("SELECT rd.*, p.producto_nombre, p.producto_marca, p.producto_modelo, cd.compra_detalle_precio 
                FROM recepcion_detalle rd 
                INNER JOIN producto p ON rd.producto_id=p.producto_id 
                INNER JOIN compra_detalle cd ON rd.producto_id=cd.producto_id AND cd.compra_id='$id'
                WHERE rd.recepcion_id='$re_id'");
            
            foreach($detalles->fetchAll() as $item){
                $descripcion = $item['producto_nombre'] . " " . ($item['producto_marca'] ?? "");
                $pdf->Cell(85,7,iconv("UTF-8", "ISO-8859-1//TRANSLIT",substr($descripcion,0,45)),'LB',0,'L');
                $pdf->Cell(25,7,$item['cantidad_recibida'],'LB',0,'C');
                $pdf->Cell(35,7,"$".number_format($item['compra_detalle_precio'],2),'LB',0,'C');
                $pdf->Cell(36,7,"$".number_format($item['cantidad_recibida']*$item['compra_detalle_precio'],2),'LRB',1,'R');
            }

            /* --- CIERRE DE PÁGINA (TODO EN UNA SOLA HOJA) --- */
            $pdf->SetAutoPageBreak(false); // Desactivamos el salto automático

            // Totales (Ajustados a 200mm)
            $pdf->SetY(200); 
            $pdf->SetFont('Arial','B',10);
            $pdf->SetTextColor(39,39,51); 
            $pdf->Cell(145,6,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'TOTAL FACTURADO ($): '),0,0,'R');
            $pdf->Cell(36,6,"$".number_format($datos_compra['compra_total'],2),0,1,'R');

            $tasa = (isset($datos_compra['compra_tasa_bcv'])) ? $datos_compra['compra_tasa_bcv'] : 0;
            if($tasa > 0){
                $pdf->Cell(145,6,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'TASA BCV: '),0,0,'R');
                $pdf->Cell(36,6,"Bs. ".number_format($tasa,2),0,1,'R');
                $pdf->SetTextColor(32,100,210);
                $pdf->Cell(145,6,iconv("UTF-8", "ISO-8859-1//TRANSLIT",'TOTAL (Bs): '),0,0,'R');
                $pdf->Cell(36,6,"Bs. ".number_format($datos_compra['compra_total']*$tasa,2),0,1,'R');
            }

            // Firmas (Subidas a 230mm para que no toquen el pie)
            $pdf->SetY(230);
            $pdf->SetTextColor(39,39,51);
            $pdf->Cell(85,0,'',1,0); $pdf->Cell(11,0,'',0,0); $pdf->Cell(85,0,'',1,1);
            $pdf->Cell(85,7,"Firma Almacen (Recibe)",0,0,'C');
            $pdf->Cell(11,7,'',0,0);
            $pdf->Cell(85,7,"Firma Proveedor (Entrega)",0,1,'C');

            // Pie de página final
            $pdf->SetY(-15);
            $pdf->SetFont('Arial','I',7);
            $pdf->SetTextColor(150,150,150);
            $pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1//TRANSLIT","*** Este documento certifica la entrada física de mercancía al inventario ***"),0,0,'C');

            ob_end_clean();
            $pdf->Output("I","Recepcion_".$id.".pdf",true);

        } else { echo "No hay recepciones."; }
    } else { echo "Compra no encontrada."; }
?>