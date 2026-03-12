<div class="container is-fluid mb-6">
	<h1 class="title">Categorías</h1>
	<h2 class="subtitle"><i class="fas fa-tags fa-fw"></i> &nbsp; Gestión y Búsqueda de categorías</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        if($_SESSION['rol'] != 1){
            echo '<div class="notification is-danger is-light has-text-centered"><h4 class="title is-4">¡Acceso Denegado!</h4></div>';
        } else { 
		    $insCategoria = new app\controllers\categoryController();
            $busqueda = isset($_SESSION[$url[0]]) ? $_SESSION[$url[0]] : "";
    ?>
    
    <div class="box">
        <?php if(empty($busqueda)){ ?>
        <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/buscadorAjax.php" method="POST" autocomplete="off" >
            <input type="hidden" name="modulo_buscador" value="buscar">
            <input type="hidden" name="modulo_url" value="<?php echo $url[0]; ?>">
            <div class="field is-grouped">
                <p class="control is-expanded">
                    <input class="input is-rounded" type="text" name="txt_buscador" placeholder="Busca por nombre o ubicación de la categoría..." pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" maxlength="30" autocomplete="off">
                </p>
                <p class="control">
                    <button class="button is-info is-rounded" type="submit" ><i class="fas fa-search"></i> &nbsp; Buscar</button>
                </p>
            </div>
        </form>
        <?php }else{ ?>
        <form class="has-text-left FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/buscadorAjax.php" method="POST" autocomplete="off" >
            <input type="hidden" name="modulo_buscador" value="eliminar">
            <input type="hidden" name="modulo_url" value="<?php echo $url[0]; ?>">
            <p><i class="fas fa-search fa-fw"></i> &nbsp; Estás buscando: <strong>“<?php echo $busqueda; ?>”</strong>
                &nbsp; <button type="submit" class="button is-danger is-rounded is-small"><i class="fas fa-times"></i> &nbsp; Limpiar búsqueda</button>
            </p>
        </form>
        <?php } ?>
    </div>

	<div class="form-rest mb-4 mt-4"></div>

	<?php
            $pagina_actual = (isset($url[1]) && $url[1] != "") ? $url[1] : 1;
		    echo $insCategoria->listarCategoriaControlador($pagina_actual, 15, $url[0], $busqueda);
        } 
    ?>
</div>