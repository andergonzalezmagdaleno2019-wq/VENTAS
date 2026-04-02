<?php include './app/views/content/logo.php'; ?>
<div class="container is-fluid mb-6">
	<h1 class="title">Usuarios</h1>
	<h2 class="subtitle"><i class="fas fa-user-plus fa-fw"></i> &nbsp; Nuevo usuario</h2>
</div>

<div class="container pb-4 pt-4">
	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/usuarioAjax.php" method="POST" autocomplete="off" enctype="multipart/form-data">

		<input type="hidden" name="modulo_usuario" value="registrar">
		<input type="hidden" name="usuario_caja" value="1">

		<div class="columns">
			<div class="column is-4">
				<div class="control">
					<label>Tipo de documento <?php echo CAMPO_OBLIGATORIO;?></label><br>
					<div class="select is-fullwidth">
						<select name="usuario_tipo_documento" id="doc_tipo" required title="Seleccione tipo de documento.">
							<option value="" selected="">Seleccione una opción</option>
							<option value="V">V - Venezolano</option>
							<option value="E">E - Extranjero</option>
						</select>
					</div>
				</div>
			</div>

			<div class="column is-8">
				<div class="control">
					<label>Número de documento (Cédula) <?php echo CAMPO_OBLIGATORIO;?></label>
					<input class="input" type="text" name="usuario_dni" id="doc_numero"
						placeholder="Seleccione el tipo de documento primero" 
						pattern="[0-9]{7,8}" minlength="7" maxlength="8" required 
						autocomplete="off" title="La cédula debe tener entre 7 y 8 números." disabled>
				</div>
			</div>
		</div>

		<div class="columns">
			<div class="column">
				<div class="control">
					<label>Nombres <?php echo CAMPO_OBLIGATORIO;?></label>
					<input class="input only-letters" type="text" name="usuario_nombre" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" minlength="3" maxlength="40" required autocomplete="off" title="Mínimo 3 letras.">
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Apellidos <?php echo CAMPO_OBLIGATORIO;?></label>
					<input class="input only-letters" type="text" name="usuario_apellido" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" minlength="3" maxlength="40" required autocomplete="off" title="Mínimo 3 letras.">
				</div>
			</div>
		</div>

		<div class="columns">
			<div class="column">
				<div class="control">
					<label>Usuario<?php echo CAMPO_OBLIGATORIO;?></label>
					<input class="input" type="text" name="usuario_usuario" data-filtro="usuario" pattern="[a-zA-Z0-9_]{4,20}" minlength="4" maxlength="20" required autocomplete="off" title="El usuario debe tener al menos 4 caracteres (letras, números o guión bajo).">
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Email <?php echo CAMPO_OBLIGATORIO;?></label>
					<input class="input" type="email" name="usuario_email" maxlength="70" required autocomplete="off" title="Ingrese un correo electrónico válido.">
				</div>
			</div>
		</div>

		<div class="columns">
			<div class="column">
				<div class="control has-icons-right">
					<label>Clave (Mínimo 7 caracteres) <?php echo CAMPO_OBLIGATORIO;?></label>
					<input class="input" type="password" name="usuario_clave_1" id="usuario_clave_1"
						pattern="[a-zA-Z0-9$@.-]{7,100}" minlength="7" maxlength="100" required title="Mínimo 7 caracteres permitidos.">
					<span class="icon is-small is-right" onclick="togglePassword('usuario_clave_1', 'icon_1')" style="pointer-events: all; cursor: pointer; margin-top: 1.5rem;">
						<i class="fas fa-eye" id="icon_1"></i>
					</span>
					<p class="help is-info mt-0" style="font-size: 0.85rem; line-height: 1.2;">
						<i class="fas fa-info-circle"></i> 
						Nota: Si tu clave inicia con una letra, se marcará en <strong>Mayúscula</strong> por defecto.
					</p>
				</div>
			</div>
			<div class="column">
				<div class="control has-icons-right">
					<label>Repetir clave <?php echo CAMPO_OBLIGATORIO;?></label>
					<input class="input" type="password" name="usuario_clave_2" id="usuario_clave_2"
						pattern="[a-zA-Z0-9$@.-]{7,100}" minlength="7" maxlength="100" required title="Debe coincidir con la clave anterior.">
					<span class="icon is-small is-right" onclick="togglePassword('usuario_clave_2', 'icon_2')" style="pointer-events: all; cursor: pointer; margin-top: 1.5rem;">
						<i class="fas fa-eye" id="icon_2"></i>
					</span>
				</div>
			</div>
		</div>



        <div class="columns">
	        <div class="column">
				<div class="file has-name is-boxed">
					<label class="file-label">
						<input class="file-input" type="file" name="usuario_foto" accept=".jpg, .png, .jpeg">
						<span class="file-cta">
							<span class="file-icon"><i class="fas fa-upload"></i></span>
							<span class="file-label">Seleccione una foto</span>
						</span>
						<span class="file-name">JPG, JPEG, PNG. (MAX 5MB)</span>
					</label>
				</div>
			</div>
			<div class="column">
				<label>Rol de usuario <?php echo CAMPO_OBLIGATORIO;?></label><br>
				<div class="select is-rounded is-fullwidth">
					<select name="usuario_rol" required>
						<option value="1">1 - Administrador</option>
						<option value="2" selected>2 - Cajero / Vendedor</option>
						<option value="3">3 - Supervisor / Inventario</option>
					</select>
				</div>
			</div>
		</div>
		
		<p class="has-text-centered mt-5">
			<button type="reset" class="button is-link is-light is-rounded">Limpiar</button>
			<button type="submit" class="button is-info is-rounded"><i class="fas fa-save"></i> &nbsp; Guardar Usuario</button>
		</p>
	</form>
</div>

<script>
    function togglePassword(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            icon.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            passwordInput.type = "password";
            icon.classList.replace("fa-eye-slash", "fa-eye");
        }
    }

	document.getElementById('doc_tipo').addEventListener('change', function() {
    let inputDni = document.getElementById('doc_numero');
    if(this.value != "") {
        inputDni.placeholder = "Ingrese el número de cédula";
        inputDni.disabled = false;
    } else {
        inputDni.placeholder = "Seleccione el tipo de documento primero";
        inputDni.disabled = true;
    }
});
</script>



<?php include "./app/views/inc/script_validador.php"; ?>