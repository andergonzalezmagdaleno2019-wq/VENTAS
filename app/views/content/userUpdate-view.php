<?php
    // SOLUCIÓN: Capturamos el ID del usuario desde la URL
    $id = $insLogin->limpiarCadena($url[1]);
?>
<div class="container is-fluid mb-6">
	<?php if($id==$_SESSION['id']){ ?>
		<h1 class="title">Mi cuenta</h1>
		<h2 class="subtitle"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar cuenta</h2>
	<?php }else{ ?>
		<h1 class="title">Usuarios</h1>
		<h2 class="subtitle"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar usuario</h2>
	<?php } ?>
</div>

<div class="container pb-6 pt-6">
	<?php
		include "./app/views/inc/btn_back.php";

		$datos=$insLogin->seleccionarDatos("Unico","usuario","usuario_id",$id);

		if($datos->rowCount()==1){
			$datos=$datos->fetch();
	?>

	<h2 class="title has-text-centered"><?php echo $datos['usuario_nombre']." ".$datos['usuario_apellido']; ?></h2>

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/usuarioAjax.php" method="POST" autocomplete="off" >

		<input type="hidden" name="modulo_usuario" value="actualizar">
		<input type="hidden" name="usuario_id" value="<?php echo $datos['usuario_id']; ?>">
        
        <input type="hidden" name="usuario_caja" value="1">

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Nombres <?php echo CAMPO_OBLIGATORIO;?></label>
				  	<input class="input" type="text" name="usuario_nombre" value="<?php echo $datos['usuario_nombre']; ?>" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required autocomplete="off">
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Apellidos <?php echo CAMPO_OBLIGATORIO;?></label>
				  	<input class="input" type="text" name="usuario_apellido" value="<?php echo $datos['usuario_apellido']; ?>" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required autocomplete="off">
				</div>
		  	</div>
		</div>
		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Usuario <?php echo CAMPO_OBLIGATORIO;?></label>
				  	<input class="input" type="text" name="usuario_usuario" value="<?php echo $datos['usuario_usuario']; ?>" pattern="[a-zA-Z0-9]{4,20}" maxlength="20" required autocomplete="off">
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Email <?php echo CAMPO_OBLIGATORIO;?></label>
				  	<input class="input" type="email" name="usuario_email" value="<?php echo $datos['usuario_email']; ?>" maxlength="70" required autocomplete="off">
				</div>
		  	</div>
		</div>

        <?php if($_SESSION['rol'] == 1 && $datos['usuario_id'] != 1){ ?>
        <div class="columns">
		  	<div class="column">
                <label>Rol de usuario</label><br>
		    	<div class="select is-rounded">
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

		<br><br>
		<p class="has-text-centered">
			SI desea actualizar la clave de este usuario por favor llene los 2 campos. Si NO desea actualizar la clave deje los campos vacíos.
		</p>
		<br>
		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Nueva clave</label>
				  	<input class="input" type="password" name="usuario_clave_1" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" >
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Repetir nueva clave</label>
				  	<input class="input" type="password" name="usuario_clave_2" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" >
				</div>
		  	</div>
		</div>

		<p class="has-text-centered">
			<button type="submit" class="button is-success is-rounded"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar</button>
		</p>
	</form>
	<?php
		}else{
			include "./app/views/inc/error_alert.php";
		}
	?>
</div>