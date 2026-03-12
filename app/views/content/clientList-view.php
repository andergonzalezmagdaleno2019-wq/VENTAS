<div class="container is-fluid mb-6">
	<h1 class="title">Clientes</h1>
	<h2 class="subtitle"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista y Búsqueda de clientes</h2>
</div>
<div class="container pb-6 pt-6">
    <?php
        $insCliente = new app\controllers\clientController();
        $busqueda = isset($_SESSION[$url[0]]) ? $_SESSION[$url[0]] : "";
    ?>
    <?php if(empty($busqueda)){ ?>
    <div class="columns">
        <div class="column">
            <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/buscadorAjax.php" method="POST" autocomplete="off" >
                <input type="hidden" name="modulo_buscador" value="buscar">
                <input type="hidden" name="modulo_url" value="<?php echo $url[0]; ?>">
                <div class="field is-grouped">
                    <p class="control is-expanded"><input class="input is-rounded" type="text" name="txt_buscador" placeholder="¿Qué estas buscando?" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" maxlength="30" autocomplete="off"></p>
                    <p class="control"><button class="button is-info" type="submit" >Buscar</button></p>
                </div>
            </form>
        </div>
    </div>
    <?php }else{ ?>
    <div class="columns">
        <div class="column">
            <form class="has-text-centered mt-4 mb-4 FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/buscadorAjax.php" method="POST" autocomplete="off" >
                <input type="hidden" name="modulo_buscador" value="eliminar">
                <input type="hidden" name="modulo_url" value="<?php echo $url[0]; ?>">
                <p><i class="fas fa-search fa-fw"></i> &nbsp; Estas buscando <strong>“<?php echo $busqueda; ?>”</strong></p><br>
                <button type="submit" class="button is-danger is-rounded"><i class="fas fa-trash-restore"></i> &nbsp; Eliminar busqueda</button>
            </form>
        </div>
    </div>
    <?php } ?>
	<div class="form-rest mb-6 mt-6"></div>
	<?php
        $pagina_actual = (isset($url[1]) && $url[1] != "") ? $url[1] : 1;
		echo $insCliente->listarClienteControlador($pagina_actual, 15, $url[0], $busqueda);
	?>
</div>