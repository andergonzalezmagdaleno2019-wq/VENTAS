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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FastNet</title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/css/bulma.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/css/dark-mode.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="main-container">
    <div class="box login-card" id="login-espejismo">
        
        <div class="has-text-centered mb-5">
            <figure class="image is-inline-block mb-3" style="max-width: 180px;">
                <?php 
                    $path_logo = "./app/views/img/logo.png";
                    if(is_file($path_logo)): ?>
                        <img src="<?php echo APP_URL; ?>app/views/img/logo.png?v=<?php echo time(); ?>" class="logo-light" alt="Logo FastNet">
                        <img src="<?php echo APP_URL; ?>app/views/img/logo_black.png?v=<?php echo time(); ?>" class="logo-dark" alt="Logo FastNet">
                <?php else: ?>
                        <img src="<?php echo APP_URL; ?>app/views/img/default.png" style="width: 80px;">
                <?php endif; ?>
            </figure>
            <h1 class="title is-4 mb-2">Acceso al Sistema</h1>
        </div>

        <?php
        if(isset($_POST['login_email']) && isset($_POST['login_clave'])){
            $insLogin->iniciarSesionControlador();
        }
        ?>

        <div class="field mb-4">
            <label class="label">Correo Electrónico</label>
            <div class="control has-icons-left">
                <input class="input is-rounded" type="email" id="fake_email" 
                       maxlength="70" placeholder="usuario@correo.com" autocomplete="off">
                <span class="icon is-small is-left">
                    <i class="fas fa-envelope"></i>
                </span>
            </div>
        </div>

        <div class="field mb-4">
            <label class="label">Contraseña</label>
            <div class="control has-icons-left has-icons-right">
                <input class="input is-rounded" type="password" id="login_clave" 
                    name="login_clave" maxlength="100" placeholder="••••••••" required
                    onfocus="mostrarNota()" onblur="ocultarNota()"> 
                <span class="icon is-small is-left">
                    <i class="fas fa-lock"></i>
                </span>
                <span class="icon is-small is-right" onclick="toggleLoginPassword()" style="pointer-events: all; cursor: pointer;">
                    <i class="fas fa-eye" id="icon_login"></i>
                </span>
            </div>
            <p id="nota_mayuscula" class="help is-info mt-1 ml-1" style="font-size: 0.8rem; display: none;">
                <i class="fas fa-info-circle"></i> Inicial de letra será <strong>Mayúscula</strong>.
            </p>
        </div>

        <?php
            $n1 = rand(1, 9);
            $n2 = rand(1, 9);
            $_SESSION['captcha_resultado'] = $n1 + $n2;
        ?>
        <div class="notification is-info is-light py-3 px-4 mt-5 mb-4">
            <div class="columns is-mobile is-vcentered">
                <div class="column is-7">
                    <p class="is-size-6">¿Cuánto es <strong><?php echo "$n1 + $n2"; ?></strong>?</p>
                </div>
                <div class="column is-5">
                    <input class="input is-rounded has-text-centered" type="text" id="fake_captcha" 
                           maxlength="3" placeholder="?">
                </div>
            </div>
        </div>

        <div class="field mt-5">
            <button type="button" onclick="ejecutarLoginInmune()" 
                    class="button is-info is-rounded is-fullwidth has-text-weight-bold">
                INICIAR SESIÓN
            </button>
        </div>

        <div class="has-text-centered mt-5">
            <a href="#" onclick="recuperarCuenta()" class="is-size-7 has-text-grey">
                <strong>Recuperar Cuenta</strong>
            </a>
        </div>
    </div>
</div>

</body>
</html>
    <script>

    // Función para mostrar la nota cuando el usuario hace clic en contraseña
    function mostrarNota() {
        document.getElementById('nota_mayuscula').style.display = 'block';
    }

    // Función para ocultar la nota cuando el usuario sale del campo de contraseña
    function ocultarNota() {
        document.getElementById('nota_mayuscula').style.display = 'none';
    }

    function toggleLoginPassword() {
        const input = document.getElementById('login_clave');
        const icon = document.getElementById('icon_login');

        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

       // Función de login 
function ejecutarLoginInmune() {
    // 1. Corregimos los IDs: 'fake_email' está bien, pero la clave es 'login_clave'
    let email = document.getElementById('fake_email').value.trim();
    let clave = document.getElementById('login_clave').value.trim();
    let captcha = document.getElementById('fake_captcha').value.trim();

    // Validar que no estén vacíos antes de enviar (opcional pero recomendado)
    if(email == "" || clave == "" || captcha == ""){
        Swal.fire({
            icon: 'warning',
            title: 'Campos vacíos',
            text: 'Por favor rellena todos los campos'
        });
        return false;
    }

    // 2. Vaciamos los campos visuales por seguridad
    document.getElementById('fake_email').value = '';
    document.getElementById('login_clave').value = ''; // ID corregido aquí también
    document.getElementById('fake_captcha').value = '';

    // 3. Crear el formulario fantasma para el envío POST
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

        // ===== FUNCIONES DE RECUPERACIÓN (Mantenemos SweetAlert solo para esto) =====

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
            Swal.fire({
                title: 'Verificando correo...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            let formData = new FormData();
            formData.append('recuperar_email_ajax', email);
            formData.append('verificar_email_ajax', 'true');

            fetch('<?php echo APP_URL; ?>app/ajax/verificarEmail.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
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
    </script>
</div>
</body>
</html>