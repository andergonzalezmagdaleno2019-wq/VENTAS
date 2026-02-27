<div class="container is-fluid mb-6">
	<h1 class="title">Compras</h1>
	<h2 class="subtitle">Buscar compra</h2>
</div>

<div class="container is-fluid pb-6">
    <?php
        use app\controllers\purchaseController;
        $insCompra = new purchaseController();

        if(!isset($_SESSION['busqueda_purchaseList']) || empty($_SESSION['busqueda_purchaseList'])){
    ?>
    <div class="columns">
        <div class="column">
            <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/buscadorAjax.php" method="POST" autocomplete="off" >
                <input type="hidden" name="modulo_buscador" value="buscar">
                <input type="hidden" name="modulo_url" value="purchaseList">
                <div class="field is-grouped">
                    <p class="control is-expanded">
                        <input class="input is-rounded" type="text" name="txt_buscador" placeholder="¿Qué estas buscando?" maxlength="30" >
                    </p>
                    <p class="control">
                        <button class="button is-info" type="submit" >Buscar</button>
                    </p>
                </div>
            </form>
        </div>
    </div>
    <?php }else{ ?>
    <div class="columns">
        <div class="column">
            <form class="FormularioAjax has-text-centered mt-6 mb-6" action="<?php echo APP_URL; ?>app/ajax/buscadorAjax.php" method="POST" autocomplete="off" >
                <input type="hidden" name="modulo_buscador" value="eliminar">
                <input type="hidden" name="modulo_url" value="purchaseList">
                <p>Estás buscando <strong>"<?php echo $_SESSION['busqueda_purchaseList']; ?>"</strong></p>
                <br>
                <button type="submit" class="button is-danger is-rounded">Eliminar busqueda</button>
            </form>
        </div>
    </div>
    <?php
            if(isset($url[1]) && $url[1] != ""){
                $pagina_actual = $url[1];
            } else {
                $pagina_actual = 1;
            }
            echo $insCompra->listarCompraControlador($pagina_actual, 15, $url[0], $_SESSION['busqueda_purchaseList']);
        }
    ?>
</div>