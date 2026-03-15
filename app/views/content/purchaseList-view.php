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
        
        <?php if($estado_actual == "Anulada"): ?>
        <div class="column is-narrow">
            <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/compraAjax.php" method="POST" data-confirm="¿Estás seguro? Esta acción eliminará permanentemente TODAS las compras anuladas y sus detalles.">
                <input type="hidden" name="modulo_compra" value="vaciar_anuladas">
                <button type="submit" class="button is-danger is-outlined is-rounded">
                    <span class="icon"><i class="fas fa-trash-sweep"></i></span>
                    <span>Vaciar Papelera</span>
                </button>
            </form>
        </div>
        <?php endif; ?>
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
</script>

<?php include "./app/views/inc/print_invoice_script.php"; ?>