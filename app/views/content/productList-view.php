<div class="container is-fluid mb-6">
    <div class="columns is-vcentered">
        <div class="column is-8">
            <h1 class="title">Productos</h1>
            <h2 class="subtitle"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista y Búsqueda de productos</h2>
        </div>
    </div>
</div>

<div class="container is-fluid pb-6">
    <?php
        // Guardar el filtro de orden en la memoria si se seleccionó uno
        if(isset($_POST['orden_producto'])){
            $_SESSION['orden_producto'] = $_POST['orden_producto'];
        }
        // Determinar qué filtro mostrar seleccionado (por defecto A-Z)
        $orden_actual = isset($_SESSION['orden_producto']) ? $_SESSION['orden_producto'] : "nombre_asc";

        $insProducto = new app\controllers\productController();
        $busqueda = isset($_SESSION[$url[0]]) ? $_SESSION[$url[0]] : "";
        $categoria = isset($_SESSION['categoria_id']) ? $_SESSION['categoria_id'] : 0;
    ?>

    <div class="columns is-vcentered">
        <div class="column is-8">
            <?php if(empty($busqueda)){ ?>
            <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/buscadorAjax.php" method="POST" autocomplete="off" >
                <input type="hidden" name="modulo_buscador" value="buscar">
                <input type="hidden" name="modulo_url" value="<?php echo $url[0]; ?>">
                <div class="field is-grouped">
                    <p class="control is-expanded">
                        <input class="input is-rounded" type="text" name="txt_buscador" placeholder="¿Qué estas buscando?" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" maxlength="30" autocomplete="off">
                    </p>
                    <p class="control">
                        <button class="button is-info is-rounded" type="submit" ><i class="fas fa-search"></i> Buscar</button>
                    </p>
                </div>
            </form>
            <?php }else{ ?>
            <form class="has-text-left FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/buscadorAjax.php" method="POST" autocomplete="off" >
                <input type="hidden" name="modulo_buscador" value="eliminar">
                <input type="hidden" name="modulo_url" value="<?php echo $url[0]; ?>">
                <p><i class="fas fa-search fa-fw"></i> &nbsp; Estas buscando <strong>“<?php echo $busqueda; ?>”</strong>
                    &nbsp; <button type="submit" class="button is-danger is-rounded is-small"><i class="fas fa-trash-restore"></i> &nbsp; Eliminar busqueda</button>
                </p>
            </form>
            <?php } ?>
        </div>

        <div class="column is-4">
            <form action="" method="POST" autocomplete="off" >
                <div class="field is-grouped is-grouped-right">
                    <div class="control mt-2">
                        <label class="has-text-grey has-text-weight-bold">Ordenar por:</label>
                    </div>
                    <p class="control">
                        <span class="select is-rounded is-small">
                            <select name="orden_producto" onchange="this.form.submit()">
                                <option value="nombre_asc" <?php if($orden_actual=='nombre_asc'){ echo 'selected'; } ?> >Nombre (A - Z)</option>
                                <option value="menor_stock" <?php if($orden_actual=='menor_stock'){ echo 'selected'; } ?> >Menor Stock (Crítico primero)</option>
                                <option value="mayor_stock" <?php if($orden_actual=='mayor_stock'){ echo 'selected'; } ?> >Mayor Stock (Excedentes primero)</option>
                            </select>
                        </span>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <div class="columns mt-4">
		<div class="column">
            <?php
                $pagina_actual = (isset($url[1]) && $url[1] != "") ? $url[1] : 1;
                echo $insProducto->listarProductoControlador($pagina_actual, 15, $url[0], $categoria, $busqueda);
            ?>
		</div>
	</div>
</div>

<script>
    function calcularPreciosBs() {
        let tasa_bcv = parseFloat(localStorage.getItem('tasa_bcv')) || 0;
        let elementos_precio = document.querySelectorAll('.precio-bcv:not(.calculado)');

        elementos_precio.forEach(function(el) {
            let precio_usd = parseFloat(el.getAttribute('data-usd')) || 0;
            if(tasa_bcv > 0) {
                let total_bs = precio_usd * tasa_bcv;
                let formato_bs = new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(total_bs);
                el.innerHTML = `Bs. ${formato_bs}`;
                el.classList.add('calculado'); // Marcar para no recalcularlo
            } else {
                el.innerHTML = `<span class="has-text-danger">Sin BCV</span>`;
                el.classList.add('calculado');
            }
        });
    }

    // Se ejecuta apenas carga la página
    document.addEventListener('DOMContentLoaded', calcularPreciosBs);

    // Se mantiene vigilando por si buscas o cambias de página
    const observer = new MutationObserver(calcularPreciosBs);
    observer.observe(document.body, { childList: true, subtree: true });
</script>