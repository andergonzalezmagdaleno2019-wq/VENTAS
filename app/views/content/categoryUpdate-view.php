<div class="container is-fluid mb-6">
	<h1 class="title">Categorías</h1>
	<h2 class="subtitle"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar categoría</h2>
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
	
		include "./app/views/inc/btn_back.php";

		$id=$insLogin->limpiarCadena($url[1]);

		$datos=$insLogin->seleccionarDatos("Unico","categoria","categoria_id",$id);

		if($datos->rowCount()==1){
			$datos=$datos->fetch();
            
            // --- NUEVO: Preparar las unidades actuales convirtiéndolas en un array ---
            $unidades_actuales = isset($datos['categoria_unidades']) ? explode(",", $datos['categoria_unidades']) : [];
	?>

	<h2 class="title has-text-centered"><?php echo $datos['categoria_nombre']." (".$datos['categoria_ubicacion'].")"; ?></h2>

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/categoriaAjax.php" method="POST" autocomplete="off" >

		<input type="hidden" name="modulo_categoria" value="actualizar">
		<input type="hidden" name="categoria_id" value="<?php echo $datos['categoria_id']; ?>">

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Nombre <?php echo CAMPO_OBLIGATORIO; ?></label>
				  	<input class="input" type="text" name="categoria_nombre" value="<?php echo $datos['categoria_nombre']; ?>" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}" maxlength="50" required >
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Codificación en Almacén</label>
				  	<input class="input" type="text" name="categoria_ubicacion" value="<?php echo $datos['categoria_ubicacion']; ?>" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,150}" maxlength="150" >
				</div>
		  	</div>
		</div>

        <div class="columns">
            <div class="column is-full">
                <label>Tipos de producto permitidos <?php echo CAMPO_OBLIGATORIO; ?></label>
                <div class="control mt-2">
                    <?php 
                    // Recorremos todos los tipos posibles
                    foreach(PRODUCTO_UNIDAD as $unidad){ 
                        // Si el tipo actual está guardado en la BD para esta categoría, lo marcamos
                        $chequeado = in_array($unidad, $unidades_actuales) ? 'checked' : '';
                    ?>
                        <label class="checkbox mr-4 has-text-weight-bold is-size-6">
                            <input type="checkbox" name="categoria_unidades[]" value="<?php echo $unidad; ?>" <?php echo $chequeado; ?>> <?php echo $unidad; ?>
                        </label>
                    <?php } ?>
                </div>
                <p class="help is-info">Agrega o quita los tipos de cómo se puede vender esta categoría.</p>
            </div>
        </div>
        <p class="has-text-centered">
			<button type="submit" class="button is-success is-rounded"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar</button>
		</p>
		<p class="has-text-centered pt-6">
            <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
        </p>
	</form>
	<?php
		}else{
			include "./app/views/inc/error_alert.php";
		}
	?>
</div>