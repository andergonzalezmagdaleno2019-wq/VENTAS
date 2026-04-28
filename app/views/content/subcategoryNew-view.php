<?php include './app/views/content/logo.php'; ?>
<div class="container is-fluid mb-6">
    <h1 class="title">Subcategorías</h1>
    <h2 class="subtitle"><i class="fas fa-sitemap"></i> &nbsp; Nueva subcategoría</h2>
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

    use app\controllers\categoryController;

    $insCategory = new categoryController();
    ?>

    <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/categoriaAjax.php" method="POST" autocomplete="off">
        <input type="hidden" name="modulo_categoria" value="registrar">

        <div class="columns">
            <div class="column">
                <div class="control">
                    <label>Nombre de la Subcategoría</label>
                    <input class="input" type="text" name="categoria_nombre" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}" maxlength="50" required autocomplete="off"> 
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Categoría Principal</label><br>
                    <div class="select is-fullwidth">
                        <select name="categoria_padre_id" required>
    <option value="" selected="">Seleccione una opción</option>
    <?php
        
        $datos = $insCategory->seleccionarDatos("Normal", "categoria", "*", "ORDER BY categoria_nombre ASC");
        
        while ($campos = $datos->fetch()) {
            /* FILTRO MANUAL: Solo mostramos la opción si NO tiene un padre asignado */
            if ($campos['categoria_padre_id'] == NULL || $campos['categoria_padre_id'] == "" || $campos['categoria_padre_id'] == "0") {
                echo '<option value="' . $campos['categoria_id'] . '">' . $campos['categoria_nombre'] . '</option>';
            }
        }
    ?>
</select>
                    </div>
                </div>
            </div>

            <div class="column">
                <div class="control">
                    <label>Ubicación</label>
                    <input class="input" type="text" name="categoria_ubicacion" maxlength="150">
                </div>
            </div>
        </div>
        <p class="has-text-centered">
            <button type="submit" class="button is-info is-rounded">Guardar Subcategoría</button>
        </p>
    </form>
</div>