<div class="container is-fluid mb-6">
	<h1 class="title">Usuarios</h1>
	<h2 class="subtitle"><i class="fas fa-user-plus fa-fw"></i> &nbsp; Nuevo usuario</h2>
</div>

<div class="container pb-6 pt-6">

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/usuarioAjax.php" method="POST" autocomplete="off" enctype="multipart/form-data">

		<input type="hidden" name="modulo_usuario" value="registrar">

		<input type="hidden" name="usuario_caja" value="1">

		<div class="columns">
			<div class="column">
				<div class="control">
					<label>Nombres <?php echo CAMPO_OBLIGATORIO;?></label>
					<input class="input" type="text" name="usuario_nombre" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required autocomplete="off">
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Apellidos <?php echo CAMPO_OBLIGATORIO;?></label>
					<input class="input" type="text" name="usuario_apellido" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required autocomplete="off">
				</div>
			</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>Usuario <?php echo CAMPO_OBLIGATORIO;?></label>
					<input class="input" type="text" name="usuario_usuario" pattern="[a-zA-Z0-9]{4,20}" maxlength="20" required autocomplete="off">
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Email <?php echo CAMPO_OBLIGATORIO;?></label>
					<input class="input" type="email" name="usuario_email" maxlength="70" required autocomplete="off">
				</div>
			</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>Clave (Mínimo 7 caracteres) <?php echo CAMPO_OBLIGATORIO;?></label>
					<input class="input" type="password" name="usuario_clave_1"
						pattern="[a-zA-Z0-9$@.-]{7,100}"
						maxlength="100" required>
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Repetir clave <?php echo CAMPO_OBLIGATORIO;?></label>
					<input class="input" type="password" name="usuario_clave_2"
						pattern="[a-zA-Z0-9$@.-]{7,100}"
						maxlength="100" required>
				</div>
			</div>
              </div>
             <div class="columns">
	          <div class="column">
		<div class="file has-name is-boxed">
			<label class="file-label">
				<input class="file-input" type="file" name="usuario_foto" accept=".jpg, .png, .jpeg">
				<span class="file-cta">
					<span class="file-icon">
						<i class="fas fa-upload"></i>
					</span>
					<span class="file-label">
						Seleccione una foto
					</span>
				</span>
				<span class="file-name">JPG, JPEG, PNG. (MAX 5MB)</span>
			</label>
		</div>
	</div>
	<div class="column">
		<label>Rol de usuario</label><br>
		<div class="select is-rounded">
			<select name="usuario_rol">
				<option value="1">1 - Administrador</option>
				<option value="2">2 - Cajero / Vendedor</option>
				<option value="3">3 - Supervisor / Inventario</option>
			</select>
		</div>
	</div>
</div>
<p class="has-text-centered">
	<button type="reset" class="button is-link is-light is-rounded">Limpiar</button>
	<button type="submit" class="button is-info is-rounded">Guardar</button>
</p>
</form>
</div>