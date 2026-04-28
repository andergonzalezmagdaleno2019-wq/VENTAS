<?php include './app/views/content/logo.php'; ?>
<div class="container is-fluid mb-6">
	<h1 class="title">Categorías</h1>
	<h2 class="subtitle"><i class="fas fa-tag fa-fw"></i> &nbsp; Nueva categoría</h2>
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

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/categoriaAjax.php" method="POST" autocomplete="off" >

		<input type="hidden" name="modulo_categoria" value="registrar">

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Nombre <?php echo CAMPO_OBLIGATORIO; ?></label>
				  	<input class="input" type="text" name="categoria_nombre" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}" maxlength="50" required autocomplete="off">
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Codificación en Almacén </label>
				  	<input class="input" type="text" name="categoria_ubicacion" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,150}" maxlength="150" >
				</div>
		  	</div>
		</div>
		<div class="columns">
            <div class="column is-full">
                <label>Tipos de producto permitidos <?php echo CAMPO_OBLIGATORIO; ?></label>
                <div class="control mt-2">
                    <?php foreach(PRODUCTO_UNIDAD as $unidad){ ?>
                        <label class="control mt-2 dark-mode">
                            <input type="checkbox" name="categoria_unidades[]" value="<?php echo $unidad; ?>"> <?php echo $unidad; ?>
                        </label>
                    <?php } ?>
                </div>
                <p class="help is-info">Selecciona cómo se puede vender esta categoría (Ej: Unidad, Kilogramo...)</p>
            </div>
        </div>
        <p class="has-text-centered"></p>
		<p class="has-text-centered">
			<button type="reset" class="button is-link is-light is-rounded"><i class="fas fa-paint-roller"></i> &nbsp; Limpiar</button>
			<button type="submit" class="button is-info is-rounded"><i class="far fa-save"></i> &nbsp; Guardar</button>
		</p>
		<p class="has-text-centered pt-6">
            <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
        </p>
	</form>
</div>