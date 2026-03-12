<div class="main-container">

    <div class="box login" id="login-espejismo">
    	<p class="has-text-centered">
            <i class="fas fa-user-circle fa-5x"></i>
        </p>
		<h5 class="title is-5 has-text-centered">Inicia sesión con tu cuenta</h5>

		<?php
			if(isset($_POST['login_email']) && isset($_POST['login_clave'])){
				$insLogin->iniciarSesionControlador();
			}
		?>

        <div class="field">
            <label class="label"><i class="fas fa-envelope"></i> &nbsp; Correo Electrónico</label>
            <div class="control">
                <input class="input" type="email" id="fake_email" maxlength="70" placeholder="ejemplo@correo.com" autocomplete="off" spellcheck="false">
            </div>
        </div>

		<div class="field">
		  	<label class="label"><i class="fas fa-key"></i> &nbsp; Clave</label>
		  	<div class="control">
                <input class="input" type="text" id="fake_clave" maxlength="100" autocomplete="off" spellcheck="false" style="-webkit-text-security: disc; text-security: disc;">
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
                <input class="input" type="text" id="fake_captcha" maxlength="3" placeholder="Escribe el resultado" autocomplete="off">
            </div>
        </div>

		<p class="has-text-centered mb-4 mt-3">
            <button type="button" onclick="ejecutarLoginInmune()" class="button is-info is-rounded">Iniciar Sesión</button>
		</p>

        <div class="has-text-centered mb-4 mt-4">
            <a href="#" onclick="Swal.fire('Atención', 'Contacte al administrador del sistema para crear una cuenta o restablecer su clave', 'info');" class="is-size-7" style="color: #777;">¿Problemas para ingresar?</a>
        </div>

	</div>

    <script>
        function ejecutarLoginInmune() {
            let email = document.getElementById('fake_email').value.trim();
            let clave = document.getElementById('fake_clave').value.trim();
            let captcha = document.getElementById('fake_captcha').value.trim();

            if(email === '' || clave === '' || captcha === ''){
                Swal.fire('Atención', 'Por favor, completa todos los campos para ingresar.', 'warning');
                return;
            }

            
            document.getElementById('fake_email').value = '';
            document.getElementById('fake_clave').value = '';
            document.getElementById('fake_captcha').value = '';

            
            let formFantasma = document.createElement('form');
            formFantasma.method = 'POST';
            formFantasma.action = '';
            formFantasma.style.display = 'none';

            
            let inputEmail = document.createElement('input');
            inputEmail.type = 'hidden';
            inputEmail.name = 'login_email';
            inputEmail.value = email;
            formFantasma.appendChild(inputEmail);

            let inputClave = document.createElement('input');
            inputClave.type = 'hidden';
            inputClave.name = 'login_clave';
            inputClave.value = clave;
            formFantasma.appendChild(inputClave);

            let inputCaptcha = document.createElement('input');
            inputCaptcha.type = 'hidden';
            inputCaptcha.name = 'login_captcha';
            inputCaptcha.value = captcha;
            formFantasma.appendChild(inputCaptcha);

            
            document.body.appendChild(formFantasma);
            formFantasma.submit();
        }

        // Hacer que funcione al presionar Enter
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                ejecutarLoginInmune();
            }
        });
    </script>
</div>