<div class="container is-fluid mb-6">
    <div class="columns is-vcentered">
        <div class="column is-8">
            <h1 class="title">Ventas</h1>
            <h2 class="subtitle"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista y Búsqueda de ventas</h2>
        </div>
    </div>
</div>

<div class="container pb-6 pt-6">
	<?php
		use app\controllers\saleController;
		$insVenta = new saleController();

        $busqueda = isset($_SESSION[$url[0]]) ? $_SESSION[$url[0]] : "";
	?>

    <div class="columns is-vcentered">
        <div class="column is-8">
            <?php if(empty($busqueda)){ ?>
            <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/buscadorAjax.php" method="POST" autocomplete="off" >
                <input type="hidden" name="modulo_buscador" value="buscar">
                <input type="hidden" name="modulo_url" value="<?php echo $url[0]; ?>">
                <div class="field is-grouped">
                    <p class="control is-expanded">
                        <input class="input is-rounded" type="text" name="txt_buscador" placeholder="¿Qué estas buscando? (Ej. Código de Venta o Número de Referencia)" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\- ]{1,30}" maxlength="30" autocomplete="off">
                    </p>
                    <p class="control">
                        <button class="button is-info is-rounded" type="submit" >Buscar</button>
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
    </div>

    <?php
        $pagina_actual = (isset($url[1]) && $url[1] != "") ? $url[1] : 1;
		echo $insVenta->listarVentaControlador($pagina_actual,15,$url[0],$busqueda);

		include "./app/views/inc/print_invoice_script.php";
	?>
</div>