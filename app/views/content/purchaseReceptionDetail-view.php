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
                        <select name="recepcion_tipo_doc" id="recepcion_tipo_doc" onchange="toggleBloqueos()">
                            <option value="Nota de Entrega" selected>Nota de Entrega</option>
                            <option value="Factura">Factura Legal</option>
                        </select>
                    </div>
                </div>
                <div class="column is-4">
                    <label class="label">Nro. Documento</label>
                    <input 
                        class="input" 
                        type="text" 
                        name="recepcion_numero_doc" 
                        id="recepcion_numero_doc" 
                        placeholder="Ej: ABC-123"
                        pattern=".*[0-9].*" 
                        title="El número de documento debe contener al menos un número."
                        oninput="validarDocumento(this)"
                        required
                    >
                    <p id="error-doc" class="help is-danger" style="display:none;">Debe contener al menos un número.</p>
                </div>
                <div class="column is-4">
                    <label class="label">Fecha Emisión</label>
                    <input class="input" type="date" name="recepcion_fecha_emision" value="<?php echo date("Y-m-d"); ?>" required>
                </div>
            </div>
            
            <div class="columns is-multiline"> <div class="column is-6">
                    <label class="label">Condición de Pago</label>
                    <div class="select is-fullwidth">
                        <select name="compra_condicion" id="compra_condicion" onchange="toggleBloqueos()">
                            <option value="Contado" selected>Al Contado</option>
                            <option value="Consignacion">Consignación</option>
                            <option value="Credito">Crédito / Plazo</option>
                        </select>
                    </div>
                </div>

                <div class="column is-6" id="col-vencimiento-simple">
                    <label class="label">Vencimiento (Solo Facturas)</label>
                    <input class="input" type="date" name="compra_fecha_vencimiento" id="compra_fecha_vencimiento" value="<?php echo date("Y-m-d"); ?>" min="<?php echo date("Y-m-d"); ?>" required>
                </div>

                <div class="column is-12" id="seccion-cuotas" style="display: none;">
                    <div class="notification is-light" style="border: 1px solid #dbdbdb; padding: 1.25rem;">
                        <div class="columns is-multiline is-mobile">
                            <div class="column is-2-desktop is-6-mobile">
                                <label class="label is-small">Cuotas</label>
                                <input class="input" type="number" name="compra_cuotas_num" id="compra_cuotas_num" min="2" value="2">
                            </div>
                            <div class="column is-2-desktop is-6-mobile">
                                <label class="label is-small">Días (Frec.)</label>
                                <input class="input" type="number" name="compra_frecuencia_dias" id="compra_frecuencia_dias" min="1" value="7">
                            </div>
                            <div class="column is-8-desktop is-12-mobile">
                                <label class="label is-small">Justificación del Crédito</label>
                                <input class="input" type="text" name="compra_justificacion" id="compra_justificacion" placeholder="Ej: Acuerdo por volumen">
                            </div>
                        </div>
                    </div>
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
    // 1. Definimos la función de forma global para que el HTML (onchange) pueda verla
    function toggleBloqueos() {
    // 1. Identificación de elementos del DOM
    const selectTipo = document.getElementById('recepcion_tipo_doc');
    const selectCondicion = document.getElementById('compra_condicion');
    const inputVencimiento = document.getElementById('compra_fecha_vencimiento');
    const seccionCuotas = document.getElementById('seccion-cuotas');
    const colVencimientoSimple = document.getElementById('col-vencimiento-simple');
    const inputJustificacion = document.getElementById('compra_justificacion');

    // Validación de existencia para evitar errores en consola
    if (!selectTipo || !selectCondicion) return;

    // 2. Lógica para "Nota de Entrega" (Bloquea opciones de crédito)
    if (selectTipo.value === 'Nota de Entrega') {
        selectCondicion.value = "Contado"; 
        selectCondicion.disabled = true; 
        if (inputVencimiento) {
            inputVencimiento.disabled = true;
            inputVencimiento.style.opacity = '0.5';
        }
    } else {
        selectCondicion.disabled = false;
        if (inputVencimiento) {
            inputVencimiento.disabled = false;
            inputVencimiento.style.opacity = '1';
        }
    }

    // 3. Lógica de Cuotas: Solo se activa con FACTURA + CRÉDITO
    if (selectCondicion.value === 'Credito' && selectTipo.value === 'Factura') {
        // Mostramos la sección como bloque para que salte a la siguiente línea
        seccionCuotas.style.display = 'block'; 
        
        // Ocultamos el campo de vencimiento simple para evitar duplicidad de datos
        if (colVencimientoSimple) colVencimientoSimple.style.display = 'none';
        
        // Hacemos obligatoria la justificación
        if (inputJustificacion) inputJustificacion.required = true;
    } else {
        // Ocultamos la sección de cuotas
        seccionCuotas.style.display = 'none';
        
        // Restauramos el campo de vencimiento normal
        if (colVencimientoSimple) colVencimientoSimple.style.display = 'block';
        
        // Quitamos la obligatoriedad
        if (inputJustificacion) inputJustificacion.required = false;
    }
}

    function validarDocumento(input) {
        const regex = /[0-9]/; 
        const errorMsg = document.getElementById('error-doc');
        
        if (input.value !== "" && !regex.test(input.value)) {
            input.classList.add('is-danger');
            errorMsg.style.display = 'block';
        } else {
            input.classList.remove('is-danger');
            errorMsg.style.display = 'none';
        }
    }

    function calcularFaltante(input, id) {
        let pen = parseInt(document.getElementById('pendiente_'+id).dataset.pendiente);
        let ing = parseInt(input.value) || 0;
        let txt = document.getElementById('faltante_'+id);
        if(ing < pen) { 
            txt.innerText = "Faltarán: " + (pen - ing); 
            txt.style.display = "block"; 
        } else { 
            txt.style.display = "none"; 
        }
    }

    // 2. Inicialización de eventos al cargar el DOM
    document.addEventListener("DOMContentLoaded", () => {
        const selectTipo = document.getElementById('recepcion_tipo_doc');
        const selectCondicion = document.getElementById('compra_condicion');

        // Escuchar cambios también por JS por si acaso
        if(selectTipo) selectTipo.addEventListener('change', toggleBloqueos);
        if(selectCondicion) selectCondicion.addEventListener('change', toggleBloqueos);

        // Ejecutar al inicio para establecer el estado actual
        toggleBloqueos();
    });

    // 3. Manejo del envío del formulario (AJAX)
    let formRecepcion = document.getElementById('form-recepcion');
    if(formRecepcion){
        formRecepcion.addEventListener('submit', function(e){
            e.preventDefault();

            // Antes de enviar, se habilitan los campos para que el FormData los capture
            document.getElementById('compra_condicion').disabled = false;
            document.getElementById('compra_fecha_vencimiento').disabled = false;

            let nroDoc = document.getElementById('recepcion_numero_doc').value;
            if(!/[0-9]/.test(nroDoc)){
                Swal.fire({
                    icon: 'error',
                    title: 'Documento Inválido',
                    text: 'El número de documento debe incluir al menos un número.',
                    confirmButtonText: 'Corregir'
                });
                // Si falla, se vuelven a aplicar los bloqueos visuales
                toggleBloqueos();
                return false;
            }
            
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
                        confirmButtonText: 'Sí, imprimir', 
                        cancelButtonText: 'No, solo guardar',
                        confirmButtonColor: '#48c78e',
                        cancelButtonColor: '#7a77d9',  
                        reverseButtons: true         
                    }).then((result) => {
                        if (result.isConfirmed) { 
                            window.open(respuesta.url, '_blank'); 
                        }
                        window.location.href = "<?php echo APP_URL; ?>purchaseList/";
                    });
                } else { 
                    toggleBloqueos(); 
                    return alertas_ajax(respuesta); 
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toggleBloqueos();
            });
        });
    }
</script>