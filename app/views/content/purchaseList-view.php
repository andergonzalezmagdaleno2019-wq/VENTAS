<div class="container is-fluid mb-6">
    <h1 class="title">Compras</h1>
    <h2 class="subtitle">Lista de órdenes y facturas</h2>
</div>

<div class="container is-fluid pb-6">
    <?php
        /*---------- Bloque de seguridad: Admin (1) y Supervisor (3) ----------*/
        if($_SESSION['rol'] != 1 && $_SESSION['rol'] != 3){
            echo '
            <div class="notification is-danger is-light has-text-centered">
                <br>
                <i class="fas fa-ban fa-3x"></i><br>
                <h1 class="title mt-4">¡Acceso Denegado!</h1>
                <p>No tienes los permisos necesarios para acceder a este módulo.</p>
                <br>
                <a href="'.APP_URL.'dashboard/" class="button is-danger is-rounded">Regresar al Inicio</a>
                <br><br>
            </div>';
            exit(); 
        }
    ?>
    <?php
        if($_SESSION['rol'] != 1 && $_SESSION['rol'] != 3){
            echo '<div class="notification is-danger is-light has-text-centered"><i class="fas fa-ban fa-3x"></i><br><h1 class="title">¡Acceso Denegado!</h1></div>';
            exit(); 
        }

        use app\controllers\purchaseController;
        $insCompra = new purchaseController();

        $estado_actual = (isset($url[1]) && !is_numeric($url[1]) && $url[1] != "") ? $url[1] : "EnProceso";
        $pagina_actual = (isset($url[2]) && is_numeric($url[2])) ? $url[2] : ( (isset($url[1]) && is_numeric($url[1])) ? $url[1] : 1 );
        
        $busqueda = (isset($_SESSION['purchaseList']) && !empty($_SESSION['purchaseList'])) ? $_SESSION['purchaseList'] : "";
    ?>

    <div class="tabs is-toggle is-toggle-rounded is-centered mb-6" id="contenedor_pestanas" style="<?php echo !empty($busqueda) ? 'display: none;' : ''; ?>">
        <ul>
            <li class="<?php echo ($estado_actual == "EnProceso") ? 'is-active' : ''; ?>">
                <a href="<?php echo APP_URL; ?>purchaseList/EnProceso/">
                    <span class="icon is-small"><i class="fas fa-truck-loading"></i></span><span>En Proceso</span>
                </a>
            </li>
            <li class="<?php echo ($estado_actual == "PorFacturar") ? 'is-active' : ''; ?>">
                <a href="<?php echo APP_URL; ?>purchaseList/PorFacturar/">
                    <span class="icon is-small"><i class="fas fa-file-signature"></i></span><span>Por Facturar</span>
                </a>
            </li>
            <li class="<?php echo ($estado_actual == "PorPagar") ? 'is-active' : ''; ?>">
                <a href="<?php echo APP_URL; ?>purchaseList/PorPagar/">
                    <span class="icon is-small"><i class="fas fa-hand-holding-usd"></i></span><span>Por Pagar</span>
                </a>
            </li>
            <li class="<?php echo ($estado_actual == "Completadas") ? 'is-active' : ''; ?>">
                <a href="<?php echo APP_URL; ?>purchaseList/Completadas/">
                    <span class="icon is-small"><i class="fas fa-check-double"></i></span><span>Completadas</span>
                </a>
            </li>
            <li class="<?php echo ($estado_actual == "Anuladas") ? 'is-active' : ''; ?>">
                <a href="<?php echo APP_URL; ?>purchaseList/Anuladas/">
                    <span class="icon is-small"><i class="fas fa-ban"></i></span><span>Anuladas</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="columns is-vcentered">
        <div class="column">
            <form id="form-buscador" method="POST" autocomplete="off" onsubmit="event.preventDefault();">
                <input type="hidden" name="modulo_buscador" id="modulo_buscador_input" value="buscar">
                <input type="hidden" name="modulo_url" value="purchaseList"> 
                
                <div class="field">
                    <p class="control has-icons-left has-icons-right" id="control_buscador">
                        <input class="input is-rounded" type="text" name="txt_buscador" id="input_buscador_vivo" placeholder="Escribe el Código o Proveedor para escanear toda la base de datos..." maxlength="30" value="<?php echo $busqueda; ?>" autocomplete="off">
                        <span class="icon is-small is-left has-text-info"><i class="fas fa-search"></i></span>
                        <span class="icon is-small is-right is-clickable has-text-danger" id="btn_limpiar" style="pointer-events: auto; cursor: pointer; <?php echo empty($busqueda) ? 'display: none;' : ''; ?>" title="Limpiar búsqueda">
                            <i class="fas fa-times"></i>
                        </span>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <div class="columns">
        <div class="column" id="tabla_compras">
            <?php if(!empty($busqueda)){ ?>
                <div class="notification is-info is-light has-text-centered p-2 mb-4">
                    <i class="fas fa-search"></i> &nbsp; Mostrando resultados globales para: <strong><?php echo $busqueda; ?></strong>
                </div>
            <?php } ?>
            <?php echo $insCompra->listarCompraControlador($pagina_actual, 15, "purchaseList/".$estado_actual, $busqueda); ?>
        </div>
    </div>
</div>

<div class="modal" id="modal-factura">
    <div class="modal-background" onclick="cerrarModalFactura()"></div>
    <div class="modal-card">
        <header class="modal-card-head has-background-primary">
            <p class="modal-card-title has-text-white">Ingresar Factura Oficial - <span id="factura_codigo"></span></p>
            <button class="delete" aria-label="close" onclick="cerrarModalFactura()"></button>
        </header>
        <section class="modal-card-body">
            <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/compraAjax.php" method="POST" autocomplete="off">
                <input type="hidden" name="modulo_compra" value="registrar_factura">
                <input type="hidden" name="factura_compra_id" id="factura_compra_id">
                
                <div class="notification is-info is-light">
                    <i class="fas fa-info-circle"></i> La mercancía ya está en el almacén con Nota de Entrega. Registre la factura oficial para habilitar el pago de la deuda.
                </div>

                <div class="columns">
                    <div class="column">
                        <div class="control">
                            <label>Nro. de Factura Oficial</label>
                            <input class="input" type="text" name="factura_numero" placeholder="Ej: 004588" required>
                        </div>
                    </div>
                </div>
                <div class="columns">
                    <div class="column">
                        <div class="control">
                            <label>Fecha de Emisión</label>
                            <input class="input" type="date" name="factura_fecha" value="<?php echo date("Y-m-d"); ?>" required>
                        </div>
                    </div>
                    <div class="column">
                        <div class="control">
                            <label>Fecha de Vencimiento de Pago</label>
                            <input class="input" type="date" name="factura_vencimiento" value="<?php echo date("Y-m-d"); ?>" min="<?php echo date("Y-m-d"); ?>" required>
                        </div>
                    </div>
                </div>
                <p class="has-text-centered mt-4">
                    <button type="submit" class="button is-primary is-rounded"><i class="fas fa-save"></i> Guardar Factura</button>
                </p>
            </form>
        </section>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const formBuscador = document.getElementById('form-buscador');
        const inputVivo = document.getElementById('input_buscador_vivo');
        const controlBuscador = document.getElementById('control_buscador');
        const contenedorTabla = document.getElementById('tabla_compras');
        const btnLimpiar = document.getElementById('btn_limpiar');
        const contenedorPestanas = document.getElementById('contenedor_pestanas');
        let timer;

        function ejecutarBusquedaSilenciosa() {
            let formData = new FormData(formBuscador);
            let textoBusqueda = inputVivo.value.trim();
            
            if(textoBusqueda === '') {
                formData.set('modulo_buscador', 'eliminar');
                btnLimpiar.style.display = 'none';
                contenedorPestanas.style.display = ''; // Mostramos pestañas
            } else {
                formData.set('modulo_buscador', 'buscar');
                btnLimpiar.style.display = 'inline-flex';
                contenedorPestanas.style.display = 'none'; // Ocultamos pestañas
            }

            fetch('<?php echo APP_URL; ?>app/ajax/buscadorAjax.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.tipo === 'redireccionar') {
                    // Carga la nueva tabla silenciosamente desde el servidor
                    fetch(data.url)
                    .then(respuesta => respuesta.text())
                    .then(html => {
                        let doc = new DOMParser().parseFromString(html, 'text/html');
                        let nuevaTabla = doc.getElementById('tabla_compras');
                        if(nuevaTabla) {
                            contenedorTabla.innerHTML = nuevaTabla.innerHTML;
                        }
                        controlBuscador.classList.remove('is-loading');
                        
                        // Si se limpió, volvemos a la URL base de la pestaña para no perdernos
                        if(textoBusqueda === '') {
                            window.history.replaceState(null, '', '<?php echo APP_URL; ?>purchaseList/<?php echo $estado_actual; ?>/');
                        } else {
                            window.history.replaceState(null, '', data.url);
                        }
                    });
                }
            });
        }

        if(inputVivo && formBuscador) {
            inputVivo.addEventListener('keyup', function(e) {
                // No buscar al presionar shift, enter, etc.
                if(e.key === 'Tab' || e.key === 'ArrowUp' || e.key === 'ArrowDown' || e.key === 'ArrowLeft' || e.key === 'ArrowRight' || e.key === 'Shift') return;
                
                clearTimeout(timer);
                controlBuscador.classList.add('is-loading');
                
                timer = setTimeout(() => {
                    ejecutarBusquedaSilenciosa();
                }, 400); // 400ms es perfecto para no sobrecargar
            });
        }

        if(btnLimpiar) {
            btnLimpiar.addEventListener('click', function() {
                inputVivo.value = '';
                controlBuscador.classList.add('is-loading');
                ejecutarBusquedaSilenciosa();
            });
        }
    });

    /* Funciones de la tabla */
    function print_invoice(url) { window.open(url, '_blank'); }

    function abrirModalFactura(id, codigo) {
        document.getElementById('factura_compra_id').value = id;
        document.getElementById('factura_codigo').innerText = codigo;
        document.getElementById('modal-factura').classList.add('is-active');
    }
    
    function cerrarModalFactura() { document.getElementById('modal-factura').classList.remove('is-active'); }

    function anularCompraConMotivo(id){
        Swal.fire({
            title: '¿Anular esta orden?', text: "Indique el motivo para la auditoría:", input: 'textarea', inputPlaceholder: 'Ej: Proveedor sin stock...',
            icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Sí, Anular', cancelButtonText: 'Cancelar',
            inputValidator: (value) => {
                let text = value.trim();
                if (!text) { return '¡Escriba un motivo!'; }
                if (text.length < 10) { return 'Explique mejor (Mínimo 10 letras).'; }
                if (/^\d+$/.test(text)) { return '¡No puede contener sólo números!'; }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let datos = new FormData(); datos.append('modulo_compra', 'eliminar'); datos.append('compra_id', id); datos.append('motivo_anulacion', result.value);
                fetch('<?php echo APP_URL; ?>app/ajax/compraAjax.php', { method: 'POST', body: datos }).then(res => res.json()).then(res => { return alertas_ajax(res); });
            }
        });
    }
</script>