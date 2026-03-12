<?php
    $peticion_ajax = true;

    // Recibir fechas (si no llegan, usa la fecha actual por defecto)
    $fecha_inicio = (isset($_GET['fecha_inicio'])) ? $_GET['fecha_inicio'] : date("Y-m-d");
	$fecha_fin = (isset($_GET['fecha_fin'])) ? $_GET['fecha_fin'] : date("Y-m-d");

    /*---------- Incluyendo configuraciones ----------*/
    require_once "../../config/app.php";
    require_once "../../autoload.php";

    /*---------- Instancia al controlador (Seguro) ----------*/
    use app\controllers\saleController;
    $ins_export = new saleController();

    /*---------- Consultar Ventas por Fechas (Forma Segura) ----------*/
    $tabla_consulta = "venta INNER JOIN usuario ON venta.usuario_id=usuario.usuario_id INNER JOIN cliente ON venta.cliente_id=cliente.cliente_id WHERE venta.venta_fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' ORDER BY venta.venta_fecha ASC, venta.venta_id ASC";
    $campos_consulta = "venta.venta_id, venta.venta_codigo, venta.venta_fecha, venta.venta_hora, venta.venta_total, usuario.usuario_nombre, usuario.usuario_apellido, cliente.cliente_nombre, cliente.cliente_apellido";

    $datos_ventas = $ins_export->seleccionarDatos("Normal", $tabla_consulta, $campos_consulta, 0);
    
    /*---------- Configurar Cabeceras HTTP para forzar la descarga en Excel ----------*/
    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header("Content-Disposition: attachment; filename=Reporte_Ventas_".$fecha_inicio."_al_".$fecha_fin.".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    /*---------- Imprimir BOM para que Excel lea los acentos UTF-8 correctamente ----------*/
    echo "\xEF\xBB\xBF";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr style="background-color: #48c774; color: white; font-weight: bold;">
                <th colspan="7" style="text-align: center; font-size: 16px;">REPORTE DE VENTAS (Desde: <?php echo date("d/m/Y", strtotime($fecha_inicio)); ?> Hasta: <?php echo date("d/m/Y", strtotime($fecha_fin)); ?>)</th>
            </tr>
            <tr style="background-color: #f2f2f2;">
                <th>NRO.</th>
                <th>CÓDIGO DE VENTA</th>
                <th>FECHA</th>
                <th>HORA</th>
                <th>CLIENTE</th>
                <th>VENDEDOR</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if($datos_ventas->rowCount() >= 1){
                    $ventas = $datos_ventas->fetchAll();
                    $total_general = 0;
                    $contador = 1;
                    
                    foreach($ventas as $row){
                        echo '<tr>';
                        echo '<td style="text-align: center;">'.$contador.'</td>';
                        echo '<td style="text-align: center;">'.$row['venta_codigo'].'</td>';
                        echo '<td style="text-align: center;">'.date("d/m/Y", strtotime($row['venta_fecha'])).'</td>';
                        echo '<td style="text-align: center;">'.$row['venta_hora'].'</td>';
                        echo '<td>'.$row['cliente_nombre'].' '.$row['cliente_apellido'].'</td>';
                        echo '<td>'.$row['usuario_nombre'].' '.$row['usuario_apellido'].'</td>';
                        echo '<td style="text-align: right;">'.number_format($row['venta_total'], 2, ',', '').'</td>';
                        echo '</tr>';
                        
                        $total_general += $row['venta_total'];
                        $contador++;
                    }
                    
                    echo '<tr style="background-color: #e8e8e8; font-weight: bold;">';
                    echo '<td colspan="6" style="text-align: right;">TOTAL GENERAL RECAUDADO:</td>';
                    echo '<td style="text-align: right;">'.number_format($total_general, 2, ',', '').'</td>';
                    echo '</tr>';
                    
                }else{
                    echo '<tr><td colspan="7" style="text-align: center;">No hay ventas registradas en este rango de fechas.</td></tr>';
                }
            ?>
        </tbody>
    </table>
</body>
</html>