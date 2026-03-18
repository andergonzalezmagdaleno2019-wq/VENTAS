<?php
if(isset($_POST['verificar_correo_ajax']) && $_POST['verificar_correo_ajax'] === 'true' && isset($_POST['recuperar_email'])) {
    ob_clean();
    header('Content-Type: application/json');
    
    $email = $_POST['recuperar_email'];
    $email_codificado = base64_encode($email); 

    $existe = (strpos($email, '@') !== false); 
    
    if($existe) {
        echo json_encode([
            'existe' => true, 
            'mensaje' => 'Correo encontrado',
            'redirect' => APP_URL . "loginAnswer/" . $email_codificado . "/" 
        ]);
    } else {
        echo json_encode(['existe' => false, 'mensaje' => 'Correo no registrado']);
    }
    exit; 
}

if(isset($_POST['login_email']) && isset($_POST['login_clave'])){
    $insLogin->iniciarSesionControlador();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!--  SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="main-container">

    <div class="box login" id="login-espejismo">
    	<p class="has-text-centered">
            <i class="fas fa-user-circle fa-5x"></i>
        </p>
		<h5 class="title is-5 has-text-centered">Inicia sesión con tu cuenta</h5>

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
            <a href="#" onclick="recuperarCuenta()" class="is-size-7" style="color: #777;">¿Olvidaste tu contraseña o tienes problemas?</a>
        </div>

	</div>

    <script>
        // Función de login 
        function ejecutarLoginInmune() {
            let email = document.getElementById('fake_email').value.trim();
            let clave = document.getElementById('fake_clave').value.trim();
            let captcha = document.getElementById('fake_captcha').value.trim();

            if(email === '' || clave === '' || captcha === ''){
                Swal.fire('Atención', 'Por favor, completa todos los campos para ingresar.', 'warning');
                return;
            }

            // Validar formato de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                Swal.fire('Error', 'Por favor ingresa un correo electrónico válido', 'error');
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

        // ===== FUNCIONES DE RECUPERACIÓN =====

function recuperarCuenta() {
    Swal.fire({
        title: 'Recuperar Cuenta',
        text: 'Ingresa tu correo electrónico registrado:',
        input: 'email',
        inputPlaceholder: 'tu-correo@ejemplo.com',
        showCancelButton: true,
        confirmButtonText: 'Verificar correo',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3085d6',
        didOpen: () => {
            const input = Swal.getInput();
            input.setAttribute('autocomplete', 'off');
        },
        preConfirm: (email) => {
            if (!email) {
                Swal.showValidationMessage('Por favor ingresa un correo electrónico');
                return false;
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                Swal.showValidationMessage('Por favor ingresa un correo válido');
                return false;
            }
            return email;
        }
    }).then((result) => {
        if (result.value) {
            verificarCorreoEnBD(result.value);
        }
    });
}

function verificarCorreoEnBD(email) {
    // Mostrar loading
    Swal.fire({
        title: 'Verificando correo...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Crear FormData
    let formData = new FormData();
    formData.append('recuperar_email_ajax', email);
    formData.append('verificar_email_ajax', 'true');

    // Usar fetch con la ruta CORRECTA al archivo AJAX
    fetch('<?php echo APP_URL; ?>app/ajax/verificarEmail.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Verificar el content-type
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('Respuesta no JSON:', text.substring(0, 200));
                throw new Error('El servidor no respondió correctamente');
            });
        }
        return response.json();
    })
    .then(data => {
        Swal.close();
        
        if (data.existe) {
            Swal.fire({
                icon: 'success',
                title: 'Correo verificado',
                text: 'Redirigiendo...',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = data.redirect;
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Correo no registrado',
                text: data.mensaje,
                confirmButtonText: 'Intentar de nuevo',
                confirmButtonColor: '#3085d6',
                showCancelButton: true,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    recuperarCuenta();
                }
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor. Intenta de nuevo.',
            confirmButtonColor: '#3085d6'
        });
    });
}

// Mantener función original por compatibilidad
function enviarRecuperacionPaso1(email) {
    window.location.href = '<?php echo APP_URL; ?>loginAnswer/' + btoa(email) + '/';
}
    </script>
</div>
</body>
</html>