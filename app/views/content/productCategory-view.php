<div class="container is-fluid mb-6">
	<h1 class="title">Productos</h1>
	<h2 class="subtitle"><i class="fas fa-boxes fa-fw"></i> &nbsp; Productos por categoría</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        use app\controllers\productController;
        $insProducto = new productController();
    ?>
        <div class="column is-one-third">
            <h2 class="title has-text-centered">Categorías</h2>
            <?php
                // Obtenemos todos los datos para procesarlos manualmente
                $datos_todas = $insProducto->seleccionarDatos("Normal","categoria","*",0);

                if($datos_todas->rowCount() > 0){
                    $todas = $datos_todas->fetchAll();

                    foreach($todas as $padre){
                        // Filtramos las Categorías Principales (Padres)
                        if($padre['categoria_padre_id'] == NULL || $padre['categoria_padre_id'] == "0" || $padre['categoria_padre_id'] == ""){
                            
                            // CAMBIO AQUÍ: Usamos un <div> en lugar de un <a> para que no sea clickeable
                            echo '<div class="button is-static is-link is-light is-fullwidth has-text-weight-bold mb-1" style="justify-content: flex-start; border: none; cursor: default;">
                                    <i class="fas fa-folder-open"></i> &nbsp; '.$padre['categoria_nombre'].'
                                </div>';

                            // Listamos las subcategorías que dependen de este padre
                            foreach($todas as $hijo){
                                if($hijo['categoria_padre_id'] == $padre['categoria_id']){
                                    // Las subcategorías SÍ mantienen el enlace <a> para filtrar productos
                                    echo '<a href="'.APP_URL.$url[0].'/'.$hijo['categoria_id'].'/" class="button is-white is-fullwidth" style="justify-content: flex-start; padding-left: 30px; font-size: 0.9rem;">
                                            <i class="fas fa-share fa-rotate-90"></i> &nbsp; '.$hijo['categoria_nombre'].'
                                        </a>';
                                }
                            }
                            echo '<hr class="dropdown-divider">'; // Separador visual opcional entre grupos
                        }
                    }
                }else{
                    echo '<p class="has-text-centered" >No hay categorías registradas</p>';
                }
            ?>
        </div>

        <div class="column pb-6">
            <?php
                $categoria_id=(isset($url[1])) ? $url[1] : 0;

                $categoria=$insProducto->seleccionarDatos("Unico","categoria","categoria_id",$categoria_id);
                if($categoria->rowCount()>0){

                    $categoria=$categoria->fetch();

                    echo '
                        <h2 class="title has-text-centered">'.$categoria['categoria_nombre'].'</h2>
                        <p class="has-text-centered pb-6" >'.$categoria['categoria_ubicacion'].'</p>
                    ';

                    /*== EL ERROR ESTABA AQUÍ: El orden correcto es ($pagina, $registros, $url, $categoria_id, $busqueda) ==*/
                    echo $insProducto->listarProductoControlador(isset($url[2]) ? $url[2] : 1, 10, $url[0], $categoria_id, "");
                }else{
                    echo '
                    <p class="has-text-centered pb-6"><i class="far fa-grin-wink fa-5x"></i></p>
                    <h2 class="has-text-centered title" >Seleccione una categoría para empezar</h2>';
                }
            ?>
        </div>
    </div>
</div>