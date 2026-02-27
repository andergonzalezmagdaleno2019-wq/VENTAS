<div class="main-container">

    <form class="box login" action="" method="POST" autocomplete="off" >
    	<p class="has-text-centered">
            <i class="fas fa-user-circle fa-5x"></i>
        </p>
		<h5 class="title is-5 has-text-centered">Inicia sesión con tu cuenta</h5>

		<?php
			if(isset($_POST['login_usuario']) && isset($_POST['login_clave'])){
				$insLogin->iniciarSesionControlador();
			}
		?>

		<div class="field">
			<label class="label"><i class="fas fa-user-secret"></i> &nbsp; Usuario</label>
			<div class="control">
			    <input class="input" type="text" name="login_usuario" pattern="[a-zA-Z0-9]{4,20}" maxlength="20" required >
			</div>
		</div>

		<div class="field">
		  	<label class="label"><i class="fas fa-key"></i> &nbsp; Clave</label>
		  	<div class="control">
		    	<input class="input" type="password" name="login_clave" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required >
		  	</div>
		</div>

        <?php
            $n1 = rand(1, 9);
            $n2 = rand(1, 9);
            $_SESSION['captcha_resultado'] = $n1 + $n2;
        ?>
        <div class="field">
            <label class="label"><i class="fas fa-robot"></i> &nbsp; Resuelve: <?php echo "$n1 + $n2"; ?> = ?</label>
            <div class="control">
                <input class="input" type="text" name="login_captcha" pattern="[0-9]{1,3}" maxlength="3" required placeholder="Escribe el resultado">
            </div>
        </div>

		<p class="has-text-centered mb-4 mt-3">
			<button type="submit" class="button is-info is-rounded">LOG IN</button>
		</p>

        <div class="has-text-centered mb-4">
            <a href="<?php echo APP_URL; ?>register/" class="is-size-6">¿No tienes cuenta? Regístrate aquí</a>
            <br>
            <a href="#" onclick="Swal.fire('Atención', 'Contacte al administrador del sistema para restablecer su clave', 'info');" class="is-size-7" style="color: #777;">Olvidé mi contraseña</a>
        </div>

	</form>
</div>