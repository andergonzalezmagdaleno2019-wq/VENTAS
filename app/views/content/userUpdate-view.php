<div class="container is-fluid mb-6">
	<?php 
        /* INSTANCIAMOS EL CONTROLADOR CORRECTO */
        use app\controllers\userController;
        $insUsuario = new userController();
        $id = $insUsuario->limpiarCadena($url[1]);

        if($id==$_SESSION['id']){ 
    ?>
		<h1 class="title">Mi cuenta</h1>
		<h2 class="subtitle"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar mis datos y contraseña</h2>
	<?php }else{ ?>
		<h1 class="title">Usuarios</h1>
		<h2 class="subtitle"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar datos del empleado</h2>
	<?php } ?>
</div>

<div class="container pb-6 pt-6">
	<?php
		include "./app/views/inc/btn_back.php";

		$datos=$insUsuario->seleccionarDatos("Unico","usuario","usuario_id",$id);

		if($datos->rowCount()==1){
			$datos=$datos->fetch();
	?>

	<h2 class="title has-text-centered has-text-link"><?php echo $datos['usuario_nombre']." ".$datos['usuario_apellido']; ?></h2>

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/usuarioAjax.php" method="POST" autocomplete="off" >

		<input type="hidden" name="modulo_usuario" value="actualizar">
		<input type="hidden" name="usuario_id" value="<?php echo $datos['usuario_id']; ?>">
        <input type="hidden" name="usuario_caja" value="1">

		<div class="columns">
			<div class="column is-4">
				<div class="control">
					<label>Tipo de documento <?php echo CAMPO_OBLIGATORIO;?></label><br>
					<div class="select is-fullwidth">
						<select name="usuario_tipo_documento" required>
							<option value="V" <?php if($datos['usuario_tipo_documento']=="V"){ echo 'selected'; } ?> >V - Venezolano</option>
							<option value="E" <?php if($datos['usuario_tipo_documento']=="E"){ echo 'selected'; } ?> >E - Extranjero</option>
						</select>
					</div>
				</div>
			</div>
			<div class="column is-8">
				<div class="control">
					<label>Número de documento (Cédula) <?php echo CAMPO_OBLIGATORIO;?></label>
					<input class="input" type="text" name="usuario_dni" value="<?php echo $datos['usuario_dni']; ?>" pattern="[0-9]{7,10}" maxlength="10" required title="Ingrese entre 7 y 10 números, sin puntos ni guiones." >
				</div>
			</div>
		</div>

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Nombres <?php echo CAMPO_OBLIGATORIO;?></label>
				  	<input class="input" type="text" name="usuario_nombre" value="<?php echo $datos['usuario_nombre']; ?>" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required autocomplete="off" title="Solo se permiten letras. Mínimo 3 caracteres.">
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Apellidos <?php echo CAMPO_OBLIGATORIO;?></label>
				  	<input class="input" type="text" name="usuario_apellido" value="<?php echo $datos['usuario_apellido']; ?>" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required autocomplete="off" title="Solo se permiten letras. Mínimo 3 caracteres.">
				</div>
		  	</div>
		</div>

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Nombre de Usuario <?php echo CAMPO_OBLIGATORIO;?></label>
				  	<input class="input" type="text" name="usuario_usuario" value="<?php echo $datos['usuario_usuario']; ?>" pattern="[a-zA-Z0-9_]{4,20}" maxlength="20" required autocomplete="off" title="Solo letras, números y guión bajo (_). Entre 4 y 20 caracteres.">
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Email <?php echo CAMPO_OBLIGATORIO;?></label>
				  	<input class="input" type="email" name="usuario_email" value="<?php echo $datos['usuario_email']; ?>" maxlength="70" required autocomplete="off" title="Ingrese un correo electrónico válido.">
				</div>
		  	</div>
		</div>

        <?php 
            /* Solo el Administrador principal puede cambiar roles */
            if($_SESSION['rol'] == 1 && $datos['usuario_id'] != 1){ 
        ?>
        <div class="columns">
		  	<div class="column">
                <label>Rol de usuario</label><br>
		    	<div class="select is-rounded is-fullwidth">
				  	<select name="usuario_rol">
                        <option value="1" <?php if($datos['rol_id']==1){ echo 'selected'; } ?> >1 - Administrador</option>
                        <option value="2" <?php if($datos['rol_id']==2){ echo 'selected'; } ?> >2 - Cajero / Vendedor</option>
                        <option value="3" <?php if($datos['rol_id']==3){ echo 'selected'; } ?> >3 - Supervisor / Inventario</option>
				  	</select>
				</div>
		  	</div>
        </div>
        <?php }else{ ?>
            <input type="hidden" name="usuario_rol" value="<?php echo $datos['rol_id']; ?>">
        <?php } ?>

		<hr>
		<p class="has-text-centered has-text-info mb-4">
			<i class="fas fa-lock"></i> <strong>ZONA DE SEGURIDAD:</strong> Si desea actualizar la contraseña, llene ambos campos. Si NO desea cambiarla, déjelos vacíos.
		</p>
		
		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Nueva contraseña (Mínimo 7 caracteres)</label>
				  	<input class="input" type="password" name="usuario_clave_1" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" title="Mínimo 7 caracteres permitidos." >
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Repetir nueva contraseña</label>
				  	<input class="input" type="password" name="usuario_clave_2" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" title="Debe coincidir con la clave anterior." >
				</div>
		  	</div>
		</div>

		<p class="has-text-centered mt-5">
			<button type="submit" class="button is-success is-rounded"><i class="fas fa-sync-alt"></i> &nbsp; Guardar Cambios</button>
		</p>
	</form>
	<?php
		}else{
			include "./app/views/inc/error_alert.php";
		}
	?>
</div>