<?php include './app/views/content/logo.php'; ?>
<div class="container is-fluid mb-6">
    <h1 class="title">Subcategorías</h1>
    <h2 class="subtitle"><i class="fas fa-sitemap"></i> &nbsp; Nueva subcategoría</h2>
</div>

<div class="container pb-6 pt-6">
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