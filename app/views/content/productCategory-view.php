<div class="container is-fluid mb-6">
    <h1 class="title">Productos</h1>
    <h2 class="subtitle"><i class="fas fa-boxes fa-fw"></i> &nbsp; Productos por categoría</h2>
</div>

<div class="container pb-6 pt-6">
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
        use app\controllers\productController;
        $insProducto = new productController();
    ?>
    <div class="columns">
        <div class="column is-one-third">
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">
                        <i class="fas fa-tags"></i> &nbsp; Categorías
                    </p>
                </header>
                <div class="card-content" style="max-height: 450px; overflow-y: auto;">
                    <?php
                        // Obtenemos todos los datos para procesarlos manualmente
                        $datos_todas = $insProducto->seleccionarDatos("Normal","categoria","*","ORDER BY categoria_nombre ASC");

                        if($datos_todas->rowCount() > 0){
                            $todas = $datos_todas->fetchAll();

                            foreach($todas as $p){
                                // Filtramos las Categorías Principales (Padres)
                                if($p['categoria_padre_id'] == NULL || $p['categoria_padre_id'] == "0" || $p['categoria_padre_id'] == ""){
                                    
                                    // Verificar si tiene hijos
                                    $tiene_hijos = false;
                                    foreach($todas as $h){
                                        if($h['categoria_padre_id'] == $p['categoria_id']){ 
                                            $tiene_hijos = true; 
                                            break; 
                                        }
                                    }
                                    
                                    if($tiene_hijos) {
                                        // Mostrar como acordeón (con hijos)
                                        echo '<div class="mb-2">';
                                        echo '<button class="button is-fullwidth has-text-left p-2 mb-1 acordeon-btn" style="border: none; background-color: #f0f0f0; border-radius: 4px; cursor: pointer;" onclick="toggleAcordeon(this)">
                                                <span style="display: flex; align-items: center; width: 100%;">
                                                    <i class="fas fa-folder-open" style="margin-right: 8px;"></i>
                                                    <span style="flex-grow: 1; font-weight: bold;">'.mb_strtoupper($p['categoria_nombre'], 'UTF-8').'</span>
                                                    <i class="fas fa-chevron-down acordeon-icono"></i>
                                                </span>
                                              </button>';
                                        
                                        // Contenido del acordeón (hijos)
                                        echo '<div class="acordeon-contenido" style="display: none; padding-left: 15px;">';
                                        foreach($todas as $h){
                                            if($h['categoria_padre_id'] == $p['categoria_id']){
                                                // Las subcategorías mantienen el enlace para filtrar productos
                                                echo '<a href="'.APP_URL.$url[0].'/'.$h['categoria_id'].'/" class="button is-fullwidth is-small is-outlined is-link mb-1" style="justify-content: flex-start;">
                                                        <i class="fas fa-arrow-right"></i> &nbsp; '.$h['categoria_nombre'].'
                                                      </a>';
                                            }
                                        }
                                        echo '</div></div>';
                                        
                                    } else {
                                        // Mostrar como botón simple (sin hijos) - pero con enlace
                                        echo '<a href="'.APP_URL.$url[0].'/'.$p['categoria_id'].'/" class="button is-fullwidth has-text-left p-2 mb-2" style="border: none; background-color: #f0f0f0; border-radius: 4px; text-decoration: none; color: inherit;">
                                                <span style="display: flex; align-items: center;">
                                                    <i class="fas fa-folder" style="margin-right: 8px;"></i>
                                                    <span style="flex-grow: 1; font-weight: bold;">'.mb_strtoupper($p['categoria_nombre'], 'UTF-8').'</span>
                                                </span>
                                              </a>';
                                    }
                                }
                            }
                        } else {
                            echo '<p class="has-text-centered">No hay categorías registradas</p>';
                        }
                    ?>
                </div>
            </div>
        </div>

        <div class="column">
            <?php
                $categoria_id = (isset($url[1])) ? $url[1] : 0;

                $categoria = $insProducto->seleccionarDatos("Unico","categoria","categoria_id",$categoria_id);
                if($categoria->rowCount() > 0){
                    $categoria = $categoria->fetch();

                    echo '
                        <div class="card">
                            <header class="card-header">
                                <p class="card-header-title">
                                    <i class="fas fa-folder-open"></i> &nbsp; '.$categoria['categoria_nombre'].'
                                </p>
                            </header>
                            <div class="card-content">
                                <p class="has-text-centered pb-4">'.$categoria['categoria_ubicacion'].'</p>';
                                
                                // Listar productos de la categoría seleccionada
                                echo $insProducto->listarProductoControlador(
                                    isset($url[2]) ? $url[2] : 1, 
                                    10, 
                                    $url[0], 
                                    $categoria_id, 
                                    ""
                                );
                    echo '  </div>
                        </div>';
                } else {
                    echo '
                        <div class="card">
                            <div class="card-content has-text-centered">
                                <p class="pb-4"><i class="far fa-grin-wink fa-5x"></i></p>
                                <h2 class="title is-4">Seleccione una categoría para empezar</h2>
                                <p class="subtitle is-6">Haga clic en cualquier categoría de la izquierda para ver sus productos</p>
                            </div>
                        </div>';
                }
            ?>
        </div>
    </div>
</div>

<script>
    function toggleAcordeon(boton) {
        let contenido = boton.nextElementSibling;
        let icono = boton.querySelector('.acordeon-icono');
        
        if (contenido.style.display === 'none' || contenido.style.display === '') {
            contenido.style.display = 'block';
            if (icono) { 
                icono.style.transform = 'rotate(180deg)'; 
                icono.style.transition = 'transform 0.3s ease'; 
            }
        } else {
            contenido.style.display = 'none';
            if (icono) { 
                icono.style.transform = 'rotate(0deg)'; 
                icono.style.transition = 'transform 0.3s ease'; 
            }
        }
    }
</script>