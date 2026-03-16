<div class="container is-fluid mb-6">
    <h1 class="title">Compras</h1>
    <h2 class="subtitle">Lista de compras realizadas</h2>
</div>

<div class="container is-fluid pb-6">
    <?php
        /*---------- Bloque de seguridad ----------*/
        if($_SESSION['rol'] != 1 && $_SESSION['rol'] != 3){
            echo '
            <div class="notification is-danger is-light has-text-centered">
                <i class="fas fa-ban fa-3x"></i><br>
                <h1 class="title">¡Acceso Denegado!</h1>
                <p>No tienes los permisos necesarios para acceder a este módulo.</p>
                <br>
                <a href="'.APP_URL.'dashboard/" class="button is-danger is-rounded">Regresar al Inicio</a>
            </div>';
            exit(); 
        }

        use app\controllers\purchaseController;
        $insCompra = new purchaseController();

        $estado_actual = (isset($url[1]) && !is_numeric($url[1]) && $url[1] != "") ? $url[1] : "Pendiente";
        $pagina_actual = (isset($url[2]) && is_numeric($url[2])) ? $url[2] : ( (isset($url[1]) && is_numeric($url[1])) ? $url[1] : 1 );
    ?>

    <div class="tabs is-toggle is-toggle-rounded is-centered mb-6">
        <ul>
            <li class="<?php echo ($estado_actual == "Pendiente") ? 'is-active' : ''; ?>">
                <a href="<?php echo APP_URL; ?>purchaseList/Pendiente/">
                    <span class="icon is-small"><i class="fas fa-clock"></i></span>
                    <span>Pendientes</span>
                </a>
            </li>
            <li class="<?php echo ($estado_actual == "Pagada") ? 'is-active' : ''; ?>">
                <a href="<?php echo APP_URL; ?>purchaseList/Pagada/">
                    <span class="icon is-small"><i class="fas fa-check-circle"></i></span>
                    <span>Pagadas</span>
                </a>
            </li>
            <li class="<?php echo ($estado_actual == "Anulada") ? 'is-active' : ''; ?>">
                <a href="<?php echo APP_URL; ?>purchaseList/Anulada/">
                    <span class="icon is-small"><i class="fas fa-ban"></i></span>
                    <span>Anuladas</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="modal" id="modal-factura">
    <div class="modal-background" onclick="cerrarModalFactura()"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Registrar Factura: <span id="factura_codigo_compra"></span></p>
            <button class="delete" aria-label="close" onclick="cerrarModalFactura()"></button>
        </header>
        <section class="modal-card-body">
            <form id="form-vincular-factura" autocomplete="off">
                <input type="hidden" id="factura_compra_id" name="compra_id">
                <input type="hidden" name="modulo_compra" value="registrar_factura">

                <div class="field">
                    <label class="label">Número de Factura</label>
                    <div class="control">
                        <input class="input" type="text" name="factura_numero" placeholder="Ej: FAC-0001" required>
                    </div>
                </div>

                <div class="columns">
                    <div class="column">
                        <div class="field">
                            <label class="label">Fecha de Emisión</label>
                            <div class="control">
                                <input class="input" type="date" name="factura_emision" required>
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="field">
                            <label class="label">Fecha de Vencimiento</label>
                            <div class="control">
                                <input class="input" type="date" name="factura_vencimiento" required>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success is-rounded" onclick="guardarFactura()">Guardar Factura</button>
            <button class="button is-link is-rounded" onclick="cerrarModalFactura()">Cancelar</button>
        </footer>
    </div>
</div>

    <div class="columns is-vcentered">
        <div class="column">
            <h2 class="subtitle">Buscar compra en <strong><?php echo $estado_actual; ?>s</strong></h2>
            <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/buscadorAjax.php" method="POST" autocomplete="off" >
                <input type="hidden" name="modulo_buscador" value="buscar">
                <input type="hidden" name="modulo_url" value="purchaseList/<?php echo $estado_actual; ?>">
                <div class="field is-grouped">
                    <p class="control is-expanded">
                        <input class="input is-rounded" type="text" name="txt_buscador" placeholder="Código de compra o Proveedor" maxlength="30" autocomplete="off">
                    </p>
                    <p class="control">
                        <button class="button is-info" type="submit" >Buscar</button>
                    </p>
                </div>
            </form>
        </div>
        </div>

    <div class="columns">
        <div class="column">
            <?php
                if(!isset($_SESSION['busqueda_purchaseList']) || empty($_SESSION['busqueda_purchaseList'])){
                    $busqueda = "";
                } else {
                    $busqueda = $_SESSION['busqueda_purchaseList'];
                }

                echo $insCompra->listarCompraControlador($pagina_actual, 15, "purchaseList/".$estado_actual, $busqueda);
            ?>
        </div>
    </div>
</div>

<div class="modal" id="modal-historial">
    <div class="modal-background" onclick="cerrarModalHistorial()"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Pagos de la Compra: <span id="historial_codigo"></span></p>
            <button class="delete" aria-label="close" onclick="cerrarModalHistorial()"></button>
        </header>
        <section class="modal-card-body">
            <div id="contenido_historial"></div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-link is-rounded" onclick="cerrarModalHistorial()">Cerrar</button>
        </footer>
    </div>
</div>

<script>
    /* --- Modal de Pagos --- */
    function verHistorialAbonos(id, codigo) {
        let modal = document.getElementById('modal-historial');
        let titulo = document.getElementById('historial_codigo');
        let contenido = document.getElementById('contenido_historial');

        titulo.innerText = codigo;
        contenido.innerHTML = '<div class="has-text-centered"><i class="fas fa-sync fa-spin fa-2x"></i><br><p>Cargando pagos...</p></div>';
        modal.classList.add('is-active');

        let datos = new FormData();
        datos.append("modulo_compra", "ver_historial_abonos");
        datos.append("compra_id", id);

        fetch("<?php echo APP_URL; ?>app/ajax/compraAjax.php", {
            method: 'POST',
            body: datos
        })
        .then(res => res.text())
        .then(res => {
            contenido.innerHTML = res;
            if(typeof load_ajax_forms === 'function'){ load_ajax_forms(); }
        });
    }

    function cerrarModalHistorial() {
        document.getElementById('modal-historial').classList.remove('is-active');
    }

    /* --- Ventana Emergente Inteligente para Anular --- */
    function anularCompraConMotivo(id){
        Swal.fire({
            title: '¿Anular esta compra?',
            text: "Esta acción devolverá los productos al proveedor. Por favor, indique el motivo:",
            input: 'text',
            inputPlaceholder: 'Ej: Proveedor no tenía stock, error de transcripción...',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, Anular',
            cancelButtonText: 'Cancelar',
            inputValidator: (value) => {
                if (!value) {
                    return '¡Debe escribir un motivo para la auditoría!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let motivo = result.value;
                let datos = new FormData();
                datos.append('modulo_compra', 'eliminar');
                datos.append('compra_id', id);
                datos.append('motivo_anulacion', motivo); // Enviamos el motivo al servidor

                fetch('<?php echo APP_URL; ?>app/ajax/compraAjax.php', {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.json())
                .then(respuesta => {
                    return alertas_ajax(respuesta);
                });
            }
        });
    }

    /* --- Función para abrir el modal de factura --- */
    function abrirModalFactura(id, codigo) {
        document.getElementById('factura_compra_id').value = id;
        document.getElementById('factura_codigo_compra').innerText = codigo;
        document.getElementById('modal-factura').classList.add('is-active');
    }

    function cerrarModalFactura() {
        document.getElementById('modal-factura').classList.remove('is-active');
        document.getElementById('form-vincular-factura').reset();
    }

    /* --- Función para enviar los datos por AJAX --- */
    function guardarFactura() {
        let form = document.getElementById('form-vincular-factura');
        let emision = form.querySelector('input[name="factura_emision"]').value;
        let vencimiento = form.querySelector('input[name="factura_vencimiento"]').value;

        /* --- Validación de fechas --- */
        if (vencimiento < emision) {
            Swal.fire({
                icon: 'error',
                title: 'Error en las fechas',
                text: 'La fecha de vencimiento no puede ser menor a la fecha de emisión.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        let datos = new FormData(form);

        fetch("<?php echo APP_URL; ?>app/ajax/compraAjax.php", {
            method: 'POST',
            body: datos
        })
        .then(res => res.text()) // ← Cambia temporalmente a .text() para ver la respuesta cruda
        .then(res => {
            console.log('Respuesta cruda del servidor:', res); // ← Mira la consola del navegador
            
            try {
                // Intenta parsear el JSON
                let jsonRespuesta = JSON.parse(res);
                cerrarModalFactura();
                return alertas_ajax(jsonRespuesta);
            } catch(e) {
                console.error('Error al parsear JSON:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error del servidor',
                    text: 'La respuesta del servidor no es válida: ' + res.substring(0, 100),
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    }

    function verHistorialFacturas(id) {
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
    </script>

<?php include "./app/views/inc/print_invoice_script.php"; ?>