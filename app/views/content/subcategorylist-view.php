<div class="container is-fluid mb-6">
    <h1 class="title">Subcategorías</h1>
    <h2 class="subtitle"><i class="fas fa-list fa-fw"></i> &nbsp; Lista de subcategorías</h2>
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
    <div class="box">
        <?php
            // Definimos la variable de búsqueda basada en la sesión del módulo actual
            $busqueda = $_SESSION[$url[0]] ?? "";

            if(empty($busqueda)){ 
        ?>
        <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/buscadorAjax.php" method="POST" autocomplete="off" >
            <input type="hidden" name="modulo_buscador" value="buscar">
            <input type="hidden" name="modulo_url" value="<?php echo $url[0]; ?>"> 
            <div class="field is-grouped">
                <p class="control is-expanded">
                    <input class="input is-rounded" type="text" name="txt_buscador" placeholder="Busca subcategorías por nombre..." pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" maxlength="30" autocomplete="off">
                </p>
                <p class="control">
                    <button class="button is-info is-rounded" type="submit" >
                        <i class="fas fa-search"></i> &nbsp; Buscar
                    </button>
                </p>
            </div>
        </form>
        <?php }else{ ?>
        <form class="has-text-left FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/buscadorAjax.php" method="POST" autocomplete="off" >
            <input type="hidden" name="modulo_buscador" value="eliminar">
            <input type="hidden" name="modulo_url" value="<?php echo $url[0]; ?>"> 
            <p><i class="fas fa-search fa-fw"></i> &nbsp; Estás buscando: <strong>“<?php echo $busqueda; ?>”</strong>
                &nbsp; <button type="submit" class="button is-danger is-rounded is-small">
                    <i class="fas fa-times"></i> &nbsp; Limpiar búsqueda
                </button>
            </p>
        </form>
        <?php } ?>
    </div>

    <?php
        use app\controllers\categoryController;
        $insSubCategoria = new categoryController();

        /* Llamamos al controlador especializado en subcategorías.
            Este método ya genera:
            1. La tabla con los estilos correctos.
            2. La consulta SQL con IS NOT NULL (solo subcategorías).
            3. La columna de "Categoría Principal".
            4. El paginador de tablas.
        */
        echo $insSubCategoria->listarSubcategoriaControlador($url[1], 15, $url[0], $busqueda);
    ?>
</div>