<?php
    require_once "./config/app.php";
    require_once "./autoload.php";
    require_once "./app/views/inc/session_start.php";

    use app\controllers\loginController;
    $insLogin = new loginController();

    $user_email = (isset($_GET['user'])) ? base64_decode($_GET['user']) : "";

    // Traemos al usuario con sus 3 preguntas
    $check_user = $insLogin->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_email='$user_email'");
    
    if($check_user->rowCount()==1){
        $datos = $check_user->fetch();
        // Guardamos las preguntas en un array para usarlas en JS
        $preguntas = [
            1 => $datos['usuario_pregunta_1'],
            2 => $datos['usuario_pregunta_2'],
            3 => $datos['usuario_pregunta_3']
        ];
    } else {
        header("Location: ".APP_URL."login/");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once "./app/views/inc/head.php"; ?>
    <title>Seguridad - <?php echo APP_NAME; ?></title>
</head>
<body>
    <div style="position: fixed; top: 1rem; right: 1rem; z-index: 999;">
        <button id="theme-toggle" class="button is-rounded">
            <span class="icon"><i id="theme-icon" class="fas fa-moon"></i></span>
        </button>
    </div>

    <div class="main-container has-background-dark" style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
        
        <div class="container">
            <div class="columns is-centered is-mobile">
                <div class="column is-11-mobile is-8-tablet is-4-desktop">
                    
                    <div class="box has-text-centered p-6" style="border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                        <span class="icon is-large has-text-info mb-5">
                            <i class="fas fa-user-shield fa-3x"></i>
                        </span>
                        
                        <h1 class="title is-4">Verificación</h1>
                        <p class="subtitle is-6 mb-5">Usuario: <span class="has-text-info"><?php echo $user_email; ?></span></p>
                        
                        <hr style="opacity: 0.1;">

                        <div id="paso-pregunta">
                            <div class="field mb-5">
                                <label class="label">Pregunta de Seguridad</label>
                                <div class="notification is-link is-light is-size-6 py-3">
                                    <p id="texto-pregunta">"<?php echo $preguntas[1]; ?>"</p>
                                </div>
                                <button class="button is-small is-ghost is-fullwidth" onclick="rotarPregunta()">
                                    <span class="icon is-small"><i class="fas fa-sync"></i></span>
                                    <span>Usar otra pregunta</span>
                                </button>
                            </div>

                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-rounded" type="text" id="respuesta_seguridad" placeholder="Escribe tu respuesta">
                                    <span class="icon is-left"><i class="fas fa-comment"></i></span>
                                </div>
                            </div>

                            <button class="button is-link is-rounded is-fullwidth mt-5" onclick="validarRespuesta('<?php echo $user_email; ?>')">
                                <strong>Verificar Identidad</strong>
                            </button>
                        </div>

                        <div id="paso-nueva-clave" style="display: none;">
                            <p class="has-text-success is-size-7 mb-4">Identidad confirmada. Ingresa tu nueva clave:</p>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-rounded" type="password" id="nueva_clave" placeholder="Nueva clave">
                                    <span class="icon is-left"><i class="fas fa-lock"></i></span>
                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input is-rounded" type="password" id="confirmar_clave" placeholder="Confirmar clave">
                                    <span class="icon is-left"><i class="fas fa-check-double"></i></span>
                                </div>
                            </div>
                            <button class="button is-success is-rounded is-fullwidth mt-5" onclick="cambiarPassword('<?php echo $user_email; ?>')">
                                <strong>Actualizar Contraseña</strong>
                            </button>
                        </div>

                        <div class="mt-5">
                            <a href="<?php echo APP_URL; ?>login/" class="is-size-7 has-text-grey">Volver al inicio</a>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <?php require_once "./app/views/inc/script.php"; ?>

    <script>
        // Lógica para manejar las 3 preguntas
        const listaPreguntas = <?php echo json_encode($preguntas); ?>;
        let preguntaActualNum = 1;

        function rotarPregunta() {
            // Ciclo: 1 -> 2 -> 3 -> 1...
            preguntaActualNum = (preguntaActualNum % 3) + 1;
            
            // Si la pregunta está vacía en la DB, saltamos a la siguiente automáticamente
            if (!listaPreguntas[preguntaActualNum] || listaPreguntas[preguntaActualNum].trim() === "") {
                rotarPregunta();
                return;
            }

            document.getElementById('texto-pregunta').innerText = `"${listaPreguntas[preguntaActualNum]}"`;
            document.getElementById('respuesta_seguridad').value = ""; // Limpiar respuesta anterior
        }

        function validarRespuesta(email) {
            let resp = document.getElementById('respuesta_seguridad').value;
            if(resp == "") {
                Swal.fire('Atención', 'Ingresa la respuesta', 'warning');
                return;
            }

            let datos = new FormData();
            datos.append("modulo_recuperacion", "validar_respuesta");
            datos.append("user_email", email);
            datos.append("user_resp", resp);
            // Enviamos el número de pregunta para que el Ajax sepa contra qué columna comparar
            datos.append("pregunta_num", preguntaActualNum); 

            fetch('<?php echo APP_URL; ?>app/ajax/recuperarAjax.php', {
                method: 'POST',
                body: datos
            })
            .then(res => res.json())
            .then(res => {
                if(!res.error){
                    document.getElementById('paso-pregunta').style.display = 'none';
                    document.getElementById('paso-nueva-clave').style.display = 'block';
                } else {
                    Swal.fire('Error', res.mensaje, 'error');
                }
            });
        }

        function cambiarPassword(email) {
            let p1 = document.getElementById('nueva_clave').value;
            let p2 = document.getElementById('confirmar_clave').value;

            if(p1 !== p2 || p1 == "") {
                Swal.fire('Error', 'Las claves no coinciden', 'error');
                return;
            }

            let datos = new FormData();
            datos.append("modulo_recuperacion", "cambiar_password");
            datos.append("user_email", email);
            datos.append("nueva_clave", p1);

            fetch('<?php echo APP_URL; ?>app/ajax/recuperarAjax.php', {
                method: 'POST',
                body: datos
            })
            .then(res => res.json())
            .then(res => {
                if(!res.error){
                    Swal.fire('¡Éxito!', res.mensaje, 'success').then(() => {
                        window.location.href = '<?php echo APP_URL; ?>login/';
                    });
                } else {
                    Swal.fire('Error', res.mensaje, 'error');
                }
            });
        }
    </script>
</body>
</html>