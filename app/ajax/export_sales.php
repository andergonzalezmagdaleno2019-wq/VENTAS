<?php
    $peticion_ajax = true;

    /*---------- Incluyendo configuraciones PRIMERO ----------*/
    require_once "../../config/app.php";
    require_once "../../autoload.php";

    /*---------- 1. Iniciar sesión conectada a FastNet ----------*/
    if(session_status() == PHP_SESSION_NONE) {
        // Le decimos a PHP que use el nombre de sesión de tu sistema
        if(defined('APP_SESSION_NAME')){
            session_name(APP_SESSION_NAME);
        } else {
            session_name("FASTNET"); // Respaldo por si acaso
        }
        session_start();
    }

    // Verificamos si logramos capturar la sesión
    if(!isset($_SESSION['id'])){
        // Si sigue fallando por temas de cookies en subcarpetas, lo comentamos para que no te bloquee
        // exit("Error: Acceso denegado. Por favor, inicia sesión en FastNet para descargar este reporte.");
    }

    /*---------- Instancia al controlador ----------*/
    use app\controllers\saleController;
    $ins_export = new saleController();

    // 2. Recibir parámetros
    $fecha_inicio = (isset($_GET['fecha_inicio'])) ? $ins_export->limpiarCadena($_GET['fecha_inicio']) : date("Y-m-d");
    $fecha_fin = (isset($_GET['fecha_fin'])) ? $ins_export->limpiarCadena($_GET['fecha_fin']) : date("Y-m-d");
    $vendedor = (isset($_GET['reporte_vendedor'])) ? $ins_export->limpiarCadena($_GET['reporte_vendedor']) : "all";
    $pago = (isset($_GET['reporte_pago'])) ? $ins_export->limpiarCadena($_GET['reporte_pago']) : "all";
    /*---------- Construcción de Consulta Dinámica ----------*/
    $filtro_adicional = "";
    if($vendedor != "all"){ $filtro_adicional .= " AND venta.usuario_id='$vendedor'"; }
    if($pago != "all"){ $filtro_adicional .= " AND venta.venta_metodo_pago='$pago'"; } 

    // Omitir el JOIN con 'cliente' para igualar las columnas exactas del PDF
    $tabla_consulta = "venta INNER JOIN usuario ON venta.usuario_id=usuario.usuario_id WHERE (venta.venta_fecha BETWEEN '$fecha_inicio' AND '$fecha_fin') $filtro_adicional ORDER BY venta.venta_fecha ASC, venta.venta_id ASC";
    $campos_consulta = "venta.*, usuario.usuario_nombre, usuario.usuario_apellido";

    $datos_ventas = $ins_export->seleccionarDatos("Normal", $tabla_consulta, $campos_consulta, 0);
    
    /*---------- Configurar Cabeceras HTTP para forzar la descarga en Excel ----------*/
    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header("Content-Disposition: attachment; filename=Reporte_Ventas_".$fecha_inicio."_al_".$fecha_fin.".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "\xEF\xBB\xBF";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas FastNet</title>
</head>
<body>
    <header>
        </header>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr style="background-color: #2064d2; color: white; font-weight: bold;">
                <th colspan="8" style="text-align: center; font-size: 16px;">
                    REPORTE DETALLADO DE VENTAS<br>
                    <span style="font-size: 12px; font-weight: normal;">
                        (Desde: <?php echo date("d/m/Y", strtotime($fecha_inicio)); ?> Hasta: <?php echo date("d/m/Y", strtotime($fecha_fin)); ?>)<br>
                        Filtros -> Vendedor: <?php echo ($vendedor == "all") ? "Todos" : "Filtrado"; ?> | Pago: <?php echo ($pago == "all") ? "Todos" : $pago; ?>
                    </span>
                </th>
            </tr>
            <tr style="background-color: #f2f2f2;">
                <th>N°</th>
                <th>FECHA</th>
                <th>CÓDIGO</th>
                <th>VENDEDOR</th>
                <th>PAGO</th>
                <th>REFERENCIA</th>
                <th>TOTAL ($)</th>
                <th>TOTAL (Bs)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if($datos_ventas->rowCount() >= 1){
                    $ventas = $datos_ventas->fetchAll();
                    $total_general_usd = 0;
                    $total_general_bs = 0;
                    $contador = 1;
                    
                    foreach($ventas as $row){
                        // Cálculos idénticos a los del PDF
                        $tasa = (isset($row['venta_tasa_bcv']) && $row['venta_tasa_bcv'] > 0) ? $row['venta_tasa_bcv'] : 0;
                        $total_bs = $row['venta_total'] * $tasa;
                        
                        $tipo_pago = (isset($row['venta_metodo_pago'])) ? $row['venta_metodo_pago'] : "N/R";
                        $referencia = (isset($row['venta_referencia']) && $row['venta_referencia'] != "") ? $row['venta_referencia'] : "S/R";

                        echo '<tr>';
                        echo '<td style="text-align: center;">'.$contador.'</td>';
                        echo '<td style="text-align: center;">'.date("d/m/Y", strtotime($row['venta_fecha'])).'</td>';
                        echo '<td style="text-align: center;">'.$row['venta_codigo'].'</td>';
                        echo '<td>'.$row['usuario_nombre'].' '.$row['usuario_apellido'].'</td>';
                        echo '<td style="text-align: center;">'.$tipo_pago.'</td>';
                        echo '<td style="text-align: center;">'.$referencia.'</td>';
                        echo '<td style="text-align: right;">'.number_format($row['venta_total'], 2, ',', '').'</td>';
                        echo '<td style="text-align: right;">'.number_format($total_bs, 2, ',', '').'</td>';
                        echo '</tr>';
                        
                        $total_general_usd += $row['venta_total'];
                        $total_general_bs += $total_bs;
                        $contador++;
                    }
                    
                    echo '<tr style="background-color: #e8e8e8; font-weight: bold;">';
                    echo '<td colspan="6" style="text-align: right;">TOTAL VENDIDO EN EL PERIODO:</td>';
                    echo '<td style="text-align: right;">'.number_format($total_general_usd, 2, ',', '').'</td>';
                    echo '<td style="text-align: right;">'.number_format($total_general_bs, 2, ',', '').'</td>';
                    echo '</tr>';
                    
                }else{
                    echo '<tr><td colspan="8" style="text-align: center;">No se encontraron registros con los filtros aplicados.</td></tr>';
                }
            ?>
        </tbody>
    </table>
</body>
</html>