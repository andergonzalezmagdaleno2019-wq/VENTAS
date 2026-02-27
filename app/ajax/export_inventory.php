<?php
    $peticion_ajax = true;
    require_once "../../config/app.php";
    require_once "../../autoload.php";

    use app\controllers\productController;
    $ins_export = new productController();

    $cat = $_GET['categoria'];
    $est = $_GET['estado'];
    $ord = $_GET['orden'];

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

    $datos = $ins_export->seleccionarDatos("Normal", "producto".$where.$order_by, "*", 0);

    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header("Content-Disposition: attachment; filename=Inventario_".date("d_m_Y").".xls");
    echo "\xEF\xBB\xBF"; // BOM para acentos
?>
<table border="1">
    <tr style="background-color: #3273dc; color: white; font-weight: bold;">
        <th colspan="5">REPORTE DE INVENTARIO - <?php echo date("d/m/Y"); ?></th>
    </tr>
    <tr style="background-color: #f2f2f2;">
        <th>CÓDIGO</th>
        <th>PRODUCTO</th>
        <th>STOCK</th>
        <th>COSTO UNITARIO</th>
        <th>SUBTOTAL</th>
    </tr>
    <?php
        $total = 0;
        foreach($datos->fetchAll() as $row){
            $sub = $row['producto_stock'] * $row['producto_costo'];
            echo "<tr>
                <td>{$row['producto_codigo']}</td>
                <td>{$row['producto_nombre']}</td>
                <td style='text-align:center'>{$row['producto_stock']}</td>
                <td>".number_format($row['producto_costo'],2)."</td>
                <td>".number_format($sub,2)."</td>
            </tr>";
            $total += $sub;
        }
    ?>
    <tr style="font-weight:bold; background-color: #e8e8e8;">
        <td colspan="4" style="text-align:right">TOTAL INVENTARIO:</td>
        <td><?php echo number_format($total, 2); ?></td>
    </tr>
</table>