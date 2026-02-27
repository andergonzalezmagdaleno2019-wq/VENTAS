<div class="main-container">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <form class="box login" id="form-register" autocomplete="off" enctype="multipart/form-data" >
    
        <h5 class="title is-5 has-text-centered">Regístrate en FastNet</h5>

        <input type="hidden" name="modulo_usuario" value="registrar">
        <input type="hidden" name="usuario_caja" value="1">
        <input type="hidden" name="usuario_rol" value="2">
        
        <div class="field">
            <label class="label"><i class="fas fa-id-card"></i> &nbsp; Nombres</label>
            <div class="control">
                <input class="input" type="text" name="usuario_nombre" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required >
            </div>
        </div>

        <div class="field">
            <label class="label"><i class="fas fa-id-card"></i> &nbsp; Apellidos</label>
            <div class="control">
                <input class="input" type="text" name="usuario_apellido" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required >
            </div>
        </div>

        <div class="field">
            <label class="label"><i class="fas fa-user-secret"></i> &nbsp; Usuario</label>
            <div class="control">
                <input class="input" type="text" name="usuario_usuario" pattern="[a-zA-Z0-9]{4,20}" maxlength="20" required >
            </div>
        </div>

        <div class="field">
            <label class="label"><i class="fas fa-envelope"></i> &nbsp; Email</label>
            <div class="control">
                <input class="input" type="email" name="usuario_email" maxlength="70" >
            </div>
        </div>

        <div class="field">
            <label class="label"><i class="fas fa-key"></i> &nbsp; Clave</label>
            <div class="control">
                <input class="input" type="password" name="usuario_clave_1" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required >
            </div>
        </div>

        <div class="field">
            <label class="label"><i class="fas fa-key"></i> &nbsp; Repetir Clave</label>
            <div class="control">
                <input class="input" type="password" name="usuario_clave_2" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required >
            </div>
        </div>

        <div class="field">
            <div class="file has-name is-boxed">
                <label class="file-label">
                    <input class="file-input" type="file" name="usuario_foto" accept=".jpg, .png, .jpeg" >
                    <span class="file-cta">
                        <span class="file-label">
                            Seleccione una foto (Opcional)
                        </span>
                    </span>
                    <span class="file-name">JPG, JPEG, PNG. (MAX 5MB)</span>
                </label>
            </div>
        </div>

        <p class="has-text-centered mb-4 mt-3">
            <button type="button" onclick="registrarUsuario()" class="button is-info is-rounded">Registrarse</button>
        </p>

        <div class="has-text-centered mb-4">
            <a href="<?php echo APP_URL; ?>login/" class="is-size-6">¿Ya tienes cuenta? Inicia sesión</a>
        </div>

    </form>
    
    <script>
        function registrarUsuario() {
            // 1. Obtener el formulario
            const formulario = document.getElementById("form-register");
            
            // 2. Verificar manualmente si los campos son válidos
            if(!formulario.checkValidity()){
                // Si falta algo, mostramos alerta visible
                Swal.fire({
                    icon: 'warning',
                    title: 'Faltan datos',
                    text: 'Por favor completa todos los campos obligatorios correctamente.',
                    confirmButtonText: 'Entendido'
                });
                return; // Detener aquí
            }

            // 3. Si todo está bien, procedemos a preguntar
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Se crearán tus datos de acceso",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, registrarme',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed){
                    
                    let data = new FormData(formulario);
                    // URL directa al Ajax para evitar errores de ruta
                    let url = "<?php echo APP_URL; ?>app/ajax/usuarioAjax.php";
                    
                    fetch(url, {
                        method: 'POST',
                        body: data
                    })
                    .then(response => response.json())
                    .then(respuesta => {
                        if(respuesta.tipo == "limpiar"){
                            Swal.fire({
                                icon: respuesta.icono,
                                title: respuesta.titulo,
                                text: respuesta.texto
                            }).then(() => {
                                formulario.reset(); 
                                window.location.href = "<?php echo APP_URL; ?>login/";
                            });
                        } else {
                            Swal.fire({
                                icon: respuesta.icono,
                                title: respuesta.titulo,
                                text: respuesta.texto
                            });
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire('Error', 'Ocurrió un error de conexión', 'error');
                    });
                }
            });
        }
    </script>
</div>
