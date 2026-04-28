<div class="container is-fluid mb-6">
	<?php 
        use app\controllers\userController;
        $insUsuario = new userController();
        $id = $insUsuario->limpiarCadena($url[1]);

        if($id==$_SESSION['id']){ 
    ?>
		<h1 class="title">Mi cuenta</h1>
		<h2 class="subtitle"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar mis datos y contraseña</h2>
	<?php }else{ ?>
		<h1 class="title">Usuarios</h1>
		<h2 class="subtitle"><i class="fas fa-user-edit"></i> &nbsp; Actualizar datos del empleado</h2>
	<?php } ?>
</div>

<div class="container pb-6 pt-6">
	<?php
        /*---------- Bloque de seguridad: Solo Administrador (1) ----------*/
        if($_SESSION['rol'] != 1){
            echo '
            <div class="notification is-danger is-light has-text-centered">
                <br>
                <i class="fas fa-ban fa-3x"></i><br>
                <h1 class="title mt-4">¡Acceso Denegado!</h1>
                <p>No tienes los permisos necesarios para acceder a este módulo de administración.</p>
                <br>
                <a href="'.APP_URL.'dashboard/" class="button is-danger is-rounded">Regresar al Inicio</a>
                <br><br>
            </div>';
            exit(); 
        }
    ?>
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
						<select name="usuario_tipo_documento" id="doc_tipo" required title="Seleccione tipo">
							<option value="V" <?php if($datos['usuario_tipo_documento']=="V"){ echo 'selected'; } ?> >V - Venezolano</option>
							<option value="E" <?php if($datos['usuario_tipo_documento']=="E"){ echo 'selected'; } ?> >E - Extranjero</option>
						</select>
					</div>
				</div>
			</div>
			<div class="column is-8">
				<div class="control">
					<label>Número de documento (Cédula) <?php echo CAMPO_OBLIGATORIO;?></label>
					<input class="input" type="text" name="usuario_dni" id="doc_numero" data-filtro="numeros" value="<?php echo $datos['usuario_dni']; ?>" pattern="[0-9]{7,10}" minlength="7" maxlength="10" required title="La cédula debe tener al menos 7 números." >
				</div>
			</div>
		</div>

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Nombres <?php echo CAMPO_OBLIGATORIO;?></label>
				  	<input class="input only-letters" type="text" name="usuario_nombre" value="<?php echo $datos['usuario_nombre']; ?>" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" minlength="3" maxlength="40" required autocomplete="off" title="Mínimo 3 caracteres.">
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Apellidos <?php echo CAMPO_OBLIGATORIO;?></label>
				  	<input class="input only-letters" type="text" name="usuario_apellido" value="<?php echo $datos['usuario_apellido']; ?>" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" minlength="3" maxlength="40" required autocomplete="off" title="Mínimo 3 caracteres.">
				</div>
		  	</div>
		</div>

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Nombre de Usuario <?php echo CAMPO_OBLIGATORIO;?></label>
				  	<input class="input" type="text" name="usuario_usuario" data-filtro="usuario" value="<?php echo $datos['usuario_usuario']; ?>" pattern="[a-zA-Z0-9_]{4,20}" minlength="4" maxlength="20" required autocomplete="off" title="Mínimo 4 caracteres (Letras, números o guión bajo).">
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Email <?php echo CAMPO_OBLIGATORIO;?></label>
				  	<input class="input" type="email" name="usuario_email" value="<?php echo $datos['usuario_email']; ?>" maxlength="70" required autocomplete="off" title="Ingrese un correo electrónico válido.">
				</div>
		  	</div>
		</div>

        <?php if($_SESSION['rol'] == 1 && $datos['usuario_id'] != 1){ ?>
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
		
		<!-- La zona de contraseña SOLO se muestra si el usuario está editando su propia cuenta -->
		<?php if($id == $_SESSION['id']): ?>
			<p class="has-text-centered has-text-info mb-4">
				<i class="fas fa-lock"></i> <strong>ZONA DE SEGURIDAD:</strong> Si desea actualizar la contraseña, llene ambos campos. Si NO desea cambiarla, déjelos vacíos.
			</p>
			
			<div class="columns">
			  	<div class="column">
			    	<div class="control">
						<label>Nueva contraseña (Mínimo 7 caracteres)</label>
					  	<input class="input" type="password" name="usuario_clave_1" pattern="[a-zA-Z0-9$@.-]{7,100}" minlength="7" maxlength="100" title="Mínimo 7 caracteres permitidos." >
					</div>
			  	</div>
			  	<div class="column">
			    	<div class="control">
						<label>Repetir nueva contraseña</label>
					  	<input class="input" type="password" name="usuario_clave_2" pattern="[a-zA-Z0-9$@.-]{7,100}" minlength="7" maxlength="100" title="Debe coincidir con la clave anterior." >
					</div>
			  	</div>
			</div>
		<?php else: ?>
			<!-- Si es admin editando otro usuario, NO se muestran campos de contraseña -->
			<input type="hidden" name="usuario_clave_1" value="">
			<input type="hidden" name="usuario_clave_2" value="">
		<?php endif; ?>

		<p class="has-text-centered mt-5">
			<button type="submit" class="button is-success is-rounded"><i class="fas fa-sync-alt"></i> &nbsp; Guardar Cambios</button>
		</p>
	</form>
	<?php }else{ include "./app/views/inc/error_alert.php"; } ?>
</div>

<?php include "./app/views/inc/script_validador.php"; ?>