<?php
    use app\controllers\purchaseController;
    $insCompra = new purchaseController();
    $url = explode("/", $_GET['views']);
    $compra_id = (isset($url[1]) && $url[1] != "") ? $insCompra->limpiarCadena($url[1]) : 0;
    if($compra_id == 0){ echo '<div class="notification is-danger mt-4">Error de ID.</div>'; exit(); }
    $datos_compra = $insCompra->ejecutarConsulta("SELECT c.*, p.proveedor_nombre FROM compra c INNER JOIN proveedor p ON c.proveedor_id = p.proveedor_id WHERE c.compra_id='$compra_id'")->fetch();
    $detalles = $insCompra->ejecutarConsulta("SELECT cd.*, p.producto_nombre, (SELECT IFNULL(SUM(rd.cantidad_recibida), 0) FROM recepcion_detalle rd INNER JOIN recepcion r ON rd.recepcion_id = r.recepcion_id WHERE r.compra_id = cd.compra_id AND rd.producto_id = cd.producto_id) as cantidad_ya_recibida FROM compra_detalle cd INNER JOIN producto p ON cd.producto_id = p.producto_id WHERE cd.compra_id = '$compra_id'")->fetchAll();
?>

<div class="container pb-6 pt-6">
    <?php include "./app/views/inc/btn_back.php"; ?>
    <h1 class="title">Almacén: Recepción de Mercancía</h1>
    <h2 class="subtitle">Orden: <strong><?php echo $datos_compra['compra_codigo']; ?></strong></h2>
    
    <form id="form-recepcion" action="<?php echo APP_URL; ?>app/ajax/compraAjax.php" method="POST" autocomplete="off">
        <input type="hidden" name="modulo_compra" value="registrar_recepcion">
        <input type="hidden" name="compra_id" value="<?php echo $compra_id; ?>">

        <div class="box has-background-light">
            <h3 class="title is-5 has-text-link"><i class="fas fa-file-invoice"></i> Documento de Llegada</h3>
            
            <div class="columns">
                <div class="column is-4">
                    <label class="label">Llegó con:</label>
                    <div class="select is-fullwidth">
                        <select name="recepcion_tipo_doc" id="recepcion_tipo_doc">
                            <option value="Nota de Entrega" selected>Nota de Entrega</option>
                            <option value="Factura">Factura Legal</option>
                        </select>
                    </div>
                </div>
                <div class="column is-4">
                    <label class="label">Nro. Documento</label>
                    <input class="input" type="text" name="recepcion_numero_doc" required>
                </div>
                <div class="column is-4">
                    <label class="label">Fecha Emisión</label>
                    <input class="input" type="date" name="recepcion_fecha_emision" value="<?php echo date("Y-m-d"); ?>" required>
                </div>
            </div>
            
            <div class="columns">
                <div class="column is-6">
                    <label class="label">Condición de Pago</label>
                    <div class="select is-fullwidth">
                        <select name="compra_condicion" id="compra_condicion">
                            <option value="Contado" selected>Al Contado</option>
                            <option value="Consignacion">Consignación</option>
                            <option value="Credito">Crédito / Plazo</option>
                        </select>
                    </div>
                </div>
                <div class="column is-6">
                    <label class="label">Vencimiento (Solo Facturas)</label>
                    <input class="input" type="date" name="compra_fecha_vencimiento" id="compra_fecha_vencimiento" value="<?php echo date("Y-m-d"); ?>" min="<?php echo date("Y-m-d"); ?>" required>
                </div>
            </div>
        </div>

        <div class="box">
            <table class="table is-bordered is-striped is-fullwidth">
                <thead class="has-background-link-dark">
                    <tr>
                        <th class="has-text-white">Producto Esperado</th>
                        <th class="has-text-centered has-text-white">Precio Pactado</th>
                        <th class="has-text-centered has-text-white">Pendiente</th>
                        <th class="has-text-centered has-text-white has-background-primary-dark">Llegaron Físicas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach($detalles as $items){ 
                            $pendiente = $items['compra_detalle_cantidad'] - $items['cantidad_ya_recibida'];
                    ?>
                    <tr>
                        <td style="vertical-align:middle;"><strong><?php echo $items['producto_nombre']; ?></strong></td>
                        <td class="has-text-centered has-text-success-dark" style="vertical-align:middle;">$<?php echo number_format($items['compra_detalle_precio'], 2); ?></td>
                        <td class="has-text-centered" style="vertical-align:middle;"><span class="tag is-info is-light is-medium" id="pendiente_<?php echo $items['producto_id']; ?>" data-pendiente="<?php echo $pendiente; ?>"><?php echo $pendiente; ?></span></td>
                        <td>
                            <input class="input is-primary has-text-centered has-text-weight-bold" type="number" name="productos_recibidos[<?php echo $items['producto_id']; ?>]" value="<?php echo ($pendiente > 0) ? $pendiente : 0; ?>" min="0" max="<?php echo $pendiente; ?>" oninput="calcularFaltante(this, <?php echo $items['producto_id']; ?>)">
                            <p class="help is-danger" id="faltante_<?php echo $items['producto_id']; ?>" style="display: none;">¡Faltan productos!</p>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <p class="has-text-centered mt-5"><button type="submit" class="button is-success is-rounded is-medium"><i class="fas fa-check"></i> Procesar Recepción</button></p>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const selectTipo = document.getElementById('recepcion_tipo_doc');
        const inputVencimiento = document.getElementById('compra_fecha_vencimiento');
        const selectCondicion = document.getElementById('compra_condicion');

        function toggleBloqueos() {
            if(selectTipo.value === 'Nota de Entrega') {
                inputVencimiento.readOnly = true; 
                inputVencimiento.style.opacity = '0.5';
                inputVencimiento.title = "Se asignará al facturar.";
                
                selectCondicion.style.pointerEvents = 'none';
                selectCondicion.style.opacity = '0.5';
            } else {
                inputVencimiento.readOnly = false; 
                inputVencimiento.style.opacity = '1';
                selectCondicion.style.pointerEvents = 'auto';
                selectCondicion.style.opacity = '1';
            }
        }
        selectTipo.addEventListener('change', toggleBloqueos);
        toggleBloqueos();
    });

    function calcularFaltante(input, id) {
        let pen = parseInt(document.getElementById('pendiente_'+id).dataset.pendiente);
        let ing = parseInt(input.value) || 0;
        let txt = document.getElementById('faltante_'+id);
        if(ing < pen) { txt.innerText = "Faltarán: " + (pen - ing); txt.style.display = "block"; } 
        else { txt.style.display = "none"; }
    }

    // Interceptar el envío del formulario para lanzar la Nota de Recepción
    let formRecepcion = document.getElementById('form-recepcion');
    if(formRecepcion){
        formRecepcion.addEventListener('submit', function(e){
            e.preventDefault();
            let datos = new FormData(this);
            fetch(this.getAttribute("action"), { method: 'POST', body: datos })
            .then(res => res.json())
            .then(respuesta => {
                if(respuesta.tipo == "confirmar"){
                    Swal.fire({
                        title: respuesta.titulo, 
                        text: respuesta.texto, 
                        icon: respuesta.icono, 
                        showCancelButton: true, 
                        confirmButtonText: respuesta.confirmButtonText, 
                        cancelButtonText: respuesta.cancelButtonText
                    }).then((result) => {
                        // Si dice que SÍ, abre el PDF en una nueva pestaña
                        if (result.isConfirmed) { 
                            window.open(respuesta.url, '_blank'); 
                        }
                        // Sin importar si dijo Sí o No, lo mandamos al listado general
                        window.location.href = "<?php echo APP_URL; ?>purchaseList/";
                    });
                } else { 
                    return alertas_ajax(respuesta); 
                }
            });
        });
    }
</script>