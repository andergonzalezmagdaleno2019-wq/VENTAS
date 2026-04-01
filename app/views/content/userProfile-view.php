<div class="container pb-6 pt-6">
    <div class="form-rest mb-6 mt-6"></div>

    <div class="box">
        <h2 class="title is-4 has-text-centered">Actualizar Mi Perfil</h2>
        
        <?php if(isset($_SESSION['seguridad_pendiente']) && $_SESSION['seguridad_pendiente']): ?>
            <div class="notification is-danger is-light">
                <button class="delete"></button>
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Configuración Obligatoria:</strong> Al ser tu primer inicio de sesión, debes establecer tus preguntas de seguridad y <strong>cambiar tu contraseña actual</strong> para poder continuar usando el sistema.
            </div>
        <?php endif; ?>

        <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/usuarioAjax.php" method="POST" autocomplete="off">
            
            <input type="hidden" name="modulo_usuario" value="actualizar">
            <input type="hidden" name="usuario_id" value="<?php echo $_SESSION['id']; ?>">
            <input type="hidden" name="tipo_edicion" value="perfil">

            <h3 class="title is-5 has-text-info"><i class="fas fa-key mr-2"></i> Cambiar Contraseña</h3>
            
            <?php if(isset($_SESSION['seguridad_pendiente']) && $_SESSION['seguridad_pendiente']): ?>
                <p class="mb-4 has-text-danger-dark"><strong>* El cambio de contraseña es obligatorio en este momento.</strong></p>
            <?php else: ?>
                <p class="mb-4">Si no deseas cambiar tu contraseña, deja estos campos vacíos.</p>
            <?php endif; ?>
            
            <div class="columns is-multiline">
                <div class="column is-6">
                    <label class="label">Nueva Contraseña <small class="has-text-grey">(mínimo 7 caracteres)</small></label>
                    <input class="input" type="password" name="usuario_clave_1" 
                        pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100"
                        title="La contraseña debe tener al menos 7 caracteres y puede incluir letras, números y los caracteres $ @ . -"
                        <?php echo (isset($_SESSION['seguridad_pendiente']) && $_SESSION['seguridad_pendiente']) ? 'required' : ''; ?>>
                </div>
                <div class="column is-6">
                    <label class="label">Repetir Nueva Contraseña <small class="has-text-grey">(mínimo 7 caracteres)</small></label>
                    <input class="input" type="password" name="usuario_clave_2" 
                        pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100"
                        title="La contraseña debe tener al menos 7 caracteres y puede incluir letras, números y los caracteres $ @ . -"
                        <?php echo (isset($_SESSION['seguridad_pendiente']) && $_SESSION['seguridad_pendiente']) ? 'required' : ''; ?>>
                </div>
            </div>
            <p class="help is-info"><i class="fas fa-info-circle"></i> La contraseña debe tener mínimo 7 caracteres (letras, números y/o $ @ . -)</p>

            <hr>

            <h3 class="title is-5 has-text-info"><i class="fas fa-shield-alt mr-2"></i> Preguntas de Seguridad</h3>
            <p class="mb-4">Estas preguntas te permitirán recuperar tu cuenta si olvidas la contraseña.</p>

            <div class="columns is-multiline">
                <div class="column is-6">
                    <label class="label">Pregunta 1</label>
                    <div class="select is-fullwidth is-info">
                        <select name="usuario_pregunta_1" <?php echo (isset($_SESSION['seguridad_pendiente']) && $_SESSION['seguridad_pendiente']) ? 'required' : ''; ?>>
                            <option value="Nombre de tu primera mascota">¿Nombre de tu primera mascota?</option>
                            <option value="Ciudad de nacimiento">¿En qué ciudad naciste?</option>
                        </select>
                    </div>
                </div>
                <div class="column is-6">
                    <label class="label">Respuesta 1</label>
                    <input class="input" type="text" name="usuario_respuesta_1" <?php echo (isset($_SESSION['seguridad_pendiente']) && $_SESSION['seguridad_pendiente']) ? 'required' : ''; ?> maxlength="100">
                </div>

                <div class="column is-6">
                    <label class="label">Pregunta 2</label>
                    <div class="select is-fullwidth is-info">
                        <select name="usuario_pregunta_2" <?php echo (isset($_SESSION['seguridad_pendiente']) && $_SESSION['seguridad_pendiente']) ? 'required' : ''; ?>>
                            <option value="Nombre de tu escuela primaria">¿Nombre de tu escuela primaria?</option>
                            <option value="Color favorito">¿Cuál es tu color favorito?</option>
                        </select>
                    </div>
                </div>
                <div class="column is-6">
                    <label class="label">Respuesta 2</label>
                    <input class="input" type="text" name="usuario_respuesta_2" <?php echo (isset($_SESSION['seguridad_pendiente']) && $_SESSION['seguridad_pendiente']) ? 'required' : ''; ?> maxlength="100">
                </div>

                <div class="column is-6">
                    <label class="label">Pregunta 3</label>
                    <div class="select is-fullwidth is-info">
                        <select name="usuario_pregunta_3" <?php echo (isset($_SESSION['seguridad_pendiente']) && $_SESSION['seguridad_pendiente']) ? 'required' : ''; ?>>
                            <option value="Marca de tu primer carro">¿Marca de tu primer carro/moto?</option>
                            <option value="Nombre de tu abuelo">¿Nombre de tu abuelo paterno?</option>
                        </select>
                    </div>
                </div>
                <div class="column is-6">
                    <label class="label">Respuesta 3</label>
                    <input class="input" type="text" name="usuario_respuesta_3" <?php echo (isset($_SESSION['seguridad_pendiente']) && $_SESSION['seguridad_pendiente']) ? 'required' : ''; ?> maxlength="100">
                </div>
            </div>

            <p class="has-text-centered mt-5">
                <button type="submit" class="button is-success is-rounded is-medium">
                    <i class="fas fa-save mr-2"></i> Guardar Configuración
                </button>
            </p>
        </form>
    </div>
</div>