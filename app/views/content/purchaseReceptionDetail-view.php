<?php
    use app\controllers\purchaseController;
    $insCompra = new purchaseController();

    $url = explode("/", $_GET['views']);
    $compra_id = (isset($url[1]) && $url[1] != "") ? $insCompra->limpiarCadena($url[1]) : 0;

    if($compra_id == 0){
        echo '<div class="notification is-danger mt-4">ID de compra no válido o no proporcionado.</div>';
        exit();
    }

    $datos_compra = $insCompra->ejecutarConsulta("SELECT c.*, p.proveedor_nombre FROM compra c INNER JOIN proveedor p ON c.proveedor_id = p.proveedor_id WHERE c.compra_id='$compra_id'");
    if($datos_compra->rowCount() <= 0){
        echo '<div class="notification is-danger mt-4">La compra no existe.</div>';
        exit();
    }
    $datos_compra = $datos_compra->fetch();

    $check_facturas = $insCompra->ejecutarConsulta("SELECT factura_id FROM compra_factura WHERE compra_id='$compra_id'");
    $total_facturas_actuales = $check_facturas->rowCount();
    $es_parcial = ($datos_compra['compra_estado'] == "Parcial");

    $consulta_detalles = "SELECT cd.*, p.producto_nombre, p.producto_costo, 
        (SELECT IFNULL(SUM(rd.cantidad_recibida), 0) FROM recepcion_detalle rd INNER JOIN recepcion r ON rd.recepcion_id = r.recepcion_id WHERE r.compra_id = cd.compra_id AND rd.producto_id = cd.producto_id) as cantidad_ya_recibida
        FROM compra_detalle cd
        INNER JOIN producto p ON cd.producto_id = p.producto_id
        WHERE cd.compra_id = '$compra_id'";

    $detalles = $insCompra->ejecutarConsulta($consulta_detalles);
    $detalles_array = $detalles->fetchAll();
?>

<div class="container pb-6 pt-6">
    <?php include "./app/views/inc/btn_back.php"; ?>
    <h1 class="title">Recepción y Facturación de Proveedor</h1>
    <h2 class="subtitle">Orden de Compra: <strong><?php echo $datos_compra['compra_codigo']; ?></strong> - <?php echo $datos_compra['proveedor_nombre']; ?></h2>
    
        <?php if($es_parcial): ?>
            <div class="notification is-info is-light">
                <div class="columns is-vcentered">
                    <div class="column">
                        <i class="fas fa-info-circle"></i> &nbsp; <strong>Estado actual:</strong> 
                        Esta compra tiene <strong><?php echo $total_facturas_actuales; ?></strong> factura(s) registrada(s).
                        <br>
                        <span class="has-text-danger-dark">
                            <strong>Nota:</strong> Al ser una entrega parcial, es obligatorio registrar la nueva factura de este despacho.
                        </span>
                    </div>
                    <div class="column is-narrow">
                        <button type="button" class="button is-info" onclick="abrirModalFactura('<?php echo $compra_id; ?>', '<?php echo $datos_compra['compra_codigo']; ?>')">
                            <i class="fas fa-file-invoice mr-1"></i> Registrar Nueva Factura
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/compraAjax.php" method="POST" autocomplete="off">
        <input type="hidden" name="modulo_compra" value="registrar_recepcion">
        <input type="hidden" name="compra_id" value="<?php echo $compra_id; ?>">

        <div class="box">
            <table class="table is-bordered is-striped is-hoverable is-fullwidth">
                <thead>
                    <tr class="has-background-link-dark">
                        <th class="has-text-white">Producto</th>
                        <th class="has-text-centered has-text-white">Pedida</th>
                        <th class="has-text-centered has-text-white">Pendiente</th>
                        <th class="has-text-centered has-text-white" style="width: 120px;">Cant. Recibida</th>
                        <th class="has-text-centered has-text-white" style="width: 110px;">Costo Ant.</th>
                        <th class="has-text-centered has-text-white has-background-primary-dark" style="width: 150px;">Costo Final ($)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach($detalles_array as $items){ 
                            $pedido = $items['compra_detalle_cantidad']; 
                            $recibido = $items['cantidad_ya_recibida'];
                            $pendiente = $pedido - $recibido;
                            $txt_costo_anterior = ($items['producto_costo'] > 0) ? "$" . number_format($items['producto_costo'], 2, '.', '') : "N/A";
                            $costo_sugerido = ($items['compra_detalle_precio'] > 0) ? $items['compra_detalle_precio'] : (($items['producto_costo'] > 0) ? $items['producto_costo'] : "");
                    ?>
                    <tr>
                        <td style="vertical-align: middle;"><strong><?php echo $items['producto_nombre']; ?></strong></td>
                        <td class="has-text-centered" style="vertical-align: middle;"><?php echo $pedido; ?></td>
                        <td class="has-text-centered" style="vertical-align: middle;">
                            <span class="tag <?php echo ($pendiente > 0) ? 'is-danger' : 'is-success'; ?> is-light is-medium">
                                <?php echo $pendiente; ?>
                            </span>
                        </td>
                        <td>
                            <div class="control">
                                <input class="input is-link has-text-centered has-text-weight-bold" type="number" 
                                       name="productos_recibidos[<?php echo $items['producto_id']; ?>]" 
                                       value="<?php echo ($pendiente > 0) ? $pendiente : 0; ?>" 
                                       min="0" max="<?php echo $pendiente; ?>" <?php if($pendiente <= 0) echo "readonly"; ?>>
                            </div>
                        </td>
                        <td class="has-text-centered" style="vertical-align: middle;">
                            <strong class="has-text-grey"><?php echo $txt_costo_anterior; ?></strong>
                        </td>
                        <td>
                            <div class="control has-icons-left">
                                <input class="input is-primary has-text-right has-text-weight-bold" type="text" 
                                       name="costos_recibidos[<?php echo $items['producto_id']; ?>]" 
                                       value="<?php echo $costo_sugerido; ?>" 
                                       pattern="[0-9.]{1,25}" placeholder="0.00" <?php if($pendiente <= 0) echo "readonly"; ?> <?php if($pendiente > 0) echo "required"; ?>>
                                <span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="box has-background-light">
            <h3 class="title is-5 has-text-link"><i class="fas fa-file-invoice-dollar"></i> Condiciones de Facturación</h3>
            <div class="columns">
                <div class="column is-4">
                    <div class="field">
                        <label class="label">Condición de Pago de la Factura</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select name="compra_condicion" id="compra_condicion" required>
                                    <option value="Contado" selected>Al Contado (Pago Inmediato)</option>
                                    <option value="Consignacion">A Consignación (Se paga al vender)</option>
                                    <option value="Credito 15">Crédito a 15 Días</option>
                                    <option value="Credito 30">Crédito a 30 Días</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-4">
                    <div class="field">
                        <label class="label">Fecha Límite de Pago Real</label>
                        <div class="control">
                            <input class="input" type="date" name="compra_fecha_vencimiento" id="compra_fecha_vencimiento" value="<?php echo date("Y-m-d"); ?>" min="<?php echo date("Y-m-d"); ?>" required>
                        </div>
                    </div>
                </div>
                <div class="column is-4">
                    <div class="field">
                        <label class="label">Nota de Recepción (Opcional)</label>
                        <div class="control">
                            <input class="input" type="text" name="recepcion_nota" placeholder="Ej: Factura N° 4589. Llegó todo correcto.">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p class="has-text-centered mt-5">
            <button type="submit" id="btn-confirmar-recepcion" class="button is-success is-rounded is-medium" <?php if($es_parcial) echo "disabled"; ?>>
                <i class="fas fa-check-circle"></i> &nbsp; Confirmar Recepción y Cerrar Factura
            </button>
            <?php if($es_parcial): ?>
                <br><small class="has-text-danger">* Debe registrar una nueva factura para habilitar este botón.</small>
            <?php endif; ?>
        </p>
    </form>
</div>

<div class="modal" id="modalFactura">
    <div class="modal-background" onclick="cerrarModalFactura()"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title"><i class="fas fa-file-invoice"></i> Registrar Factura</p>
            <button class="delete" aria-label="close" type="button" onclick="cerrarModalFactura()"></button>
        </header>
        <section class="modal-card-body">
            <form id="formFacturaProveedor">
                <input type="hidden" name="modulo_compra" value="registrar_factura">
                <input type="hidden" name="compra_id" id="modal_compra_id">
                
                <p class="mb-3">Compra: <strong id="modal_compra_codigo_display"></strong></p>
                
                <div class="field">
                    <label class="label">Número de Factura</label>
                    <div class="control">
                        <input class="input" type="text" name="factura_numero" required>
                    </div>
                </div>
                <div class="columns">
                    <div class="column">
                        <label class="label">Emisión</label>
                        <input class="input" type="date" name="factura_emision" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="column">
                        <label class="label">Vencimiento</label>
                        <input class="input" type="date" name="factura_vencimiento" value="<?php echo date('Y-m-d', strtotime('+15 days')); ?>" required>
                    </div>
                </div>
            </form>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" type="button" onclick="vincularFactura()">Guardar Factura</button>
            <button class="button" type="button" onclick="cerrarModalFactura()">Cancelar</button>
        </footer>
    </div>
</div>
<script>
    /* 1. Funciones de apertura de modales */
    window.abrirModalFactura = function(id, codigo) {
        document.getElementById('modal_compra_id').value = id;
        document.getElementById('modal_compra_codigo_display').innerText = codigo;
        document.getElementById('modalFactura').classList.add('is-active');
    };

    window.cerrarModalFactura = function() {
        document.getElementById('modalFactura').classList.remove('is-active');
    };

    /* 2. FUNCIÓN CLAVE: La que trae el historial de la imagen */
    window.verHistorialFacturas = function(id) {
        let datos = new FormData();
        datos.append("modulo_compra", "ver_historial_facturas");
        datos.append("compra_id", id);

        fetch("<?php echo APP_URL; ?>app/ajax/compraAjax.php", {
            method: 'POST',
            body: datos
        })
        .then(res => res.text()) 
        .then(res => {
            Swal.fire({
                title: '<i class="fas fa-file-invoice-dollar mr-2"></i> Historial de Facturas',
                html: res, 
                showCloseButton: true,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido',
                width: '700px'
            });
        });
    }

    /* 3. Alerta inicial vinculada a la función anterior */
    document.addEventListener('DOMContentLoaded', function() {
        <?php if($es_parcial): ?>
            Swal.fire({
                title: 'Nueva Factura Requerida',
                text: 'Esta es una entrega parcial. Registre la factura para continuar o verifique las ya registradas.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-file-invoice"></i> Registrar Ahora',
                cancelButtonText: 'Verificar Cantidades',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.abrirModalFactura('<?php echo $compra_id; ?>', '<?php echo $datos_compra['compra_codigo']; ?>');
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    /* AQUÍ LLAMAMOS A LA FUNCIÓN QUE COPIAMOS ARRIBA */
                    window.verHistorialFacturas('<?php echo $compra_id; ?>');
                }
            });
        <?php endif; ?>
    });

    /* ... El resto de tus funciones (Lógica de Fechas y vincularFactura) se mantienen igual ... */
    
    document.getElementById('compra_condicion').addEventListener('change', function() {
        let dias = 0;
        if(this.value === 'Credito 15') dias = 15;
        if(this.value === 'Credito 30') dias = 30;
        if(this.value === 'Consignacion') dias = 90; 
        let fechaHoy = new Date();
        fechaHoy.setDate(fechaHoy.getDate() + dias);
        let mes = ('0' + (fechaHoy.getMonth() + 1)).slice(-2);
        let dia = ('0' + fechaHoy.getDate()).slice(-2);
        document.getElementById('compra_fecha_vencimiento').value = fechaHoy.getFullYear() + '-' + mes + '-' + dia;
    });

window.vincularFactura = function() {
    let datos = new FormData(document.getElementById('formFacturaProveedor'));
    
    fetch('<?php echo APP_URL; ?>app/ajax/compraAjax.php', {
        method: 'POST',
        body: datos
    })
    .then(res => res.json())
    .then(res => {
        // Si la factura se registró con éxito
        if (res.icono == "success") {
            window.cerrarModalFactura();
            document.getElementById('formFacturaProveedor').reset();
            
            let btnRecepcion = document.getElementById('btn-confirmar-recepcion');
            if (btnRecepcion) {
                btnRecepcion.disabled = false;
                let smallMsg = btnRecepcion.nextElementSibling;
                if (smallMsg && smallMsg.tagName === 'SMALL') {
                    smallMsg.innerHTML = '<span class="has-text-success"><i class="fas fa-check"></i> Factura vinculada correctamente.</span>';
                }
            }
            // Mostramos el éxito
            Swal.fire({ title: res.titulo, text: res.texto, icon: res.icono });
        } else {
            // AQUÍ ESTÁ EL CAMBIO:
            // Si es duplicada (warning) o cualquier otro error, usamos tu función global de alertas
            if(typeof alertas_ajax === 'function'){
                alertas_ajax(res);
            } else {
                // Por si acaso la función no está cargada, lanzamos el SweetAlert manual
                Swal.fire({ title: res.titulo, text: res.texto, icon: res.icono });
            }
        }
    });
};
</script>