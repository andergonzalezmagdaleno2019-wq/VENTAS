<div class="container is-fluid mb-6">
    <h1 class="title">Empresa</h1>
    <h2 class="subtitle"><i class="fas fa-store-alt fa-fw"></i> &nbsp; Datos de empresa y Logo</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        $datos=$insLogin->seleccionarDatos("Normal","empresa LIMIT 1","*",0);

        // Rutas físicas fijas para comprobación (relativas a index.php)
        $dir_img = "./app/views/img/";
        $path_logo = $dir_img . "logo.png";
        $path_black = $dir_img . "logo_black.png";
        
        // Rutas URL fijas
        $url_img = APP_URL . "app/views/img/";

        if($datos->rowCount()==1){
            $datos=$datos->fetch();
    ?>
    
<div class="has-text-centered mb-6">
    <figure class="image is-128x128 is-inline-block" style="border: 2px solid #ccc; border-radius: 10px; padding: 5px; background-color: #f5f5f5; overflow: hidden;">
        <?php 
            if(is_file($path_logo)): 
                $version_light = filemtime($path_logo);
                $version_dark = (is_file($path_black)) ? filemtime($path_black) : time();
        ?>
                <img src="<?php echo $url_img; ?>logo.png?v=<?php echo $version_light; ?>" class="logo-light logo-preview-target" alt="Logo Empresa" style="object-fit: contain; width: 100%; height: 100%; transition: opacity 0.2s;">
                
                <img src="<?php echo $url_img; ?>logo_black.png?v=<?php echo $version_dark; ?>" class="logo-dark logo-preview-target" style="display: none; object-fit: contain; width: 100%; height: 100%; transition: opacity 0.2s;" alt="Logo Empresa Oscuro">
                
                <?php if(!is_file($path_black)): ?>
                    <script>console.warn("Fasnet: Falta el archivo logo_black.png en app/views/img/ para el modo oscuro.");</script>
                <?php endif; ?>
                
        <?php else: ?>
            <img src="<?php echo $url_img; ?>default.png" class="logo-preview-target" alt="Logo por defecto" style="object-fit: contain; width: 100%; height: 100%; transition: opacity 0.2s;">
        <?php endif; ?>
    </figure>
</div>
    <h2 class="title has-text-centered"><?php echo $datos['empresa_nombre']; ?></h2>

    <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/empresaAjax.php" method="POST" autocomplete="off" enctype="multipart/form-data">

        <input type="hidden" name="modulo_empresa" value="actualizar">
        <input type="hidden" name="empresa_id" value="<?php echo $datos['empresa_id']; ?>">

        <div class="columns">
            <div class="column">
                <div class="control">
                    <label class="label">Nombre <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="text" name="empresa_nombre" value="<?php echo $datos['empresa_nombre']; ?>" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ., ]{4,85}" maxlength="85" required >
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label class="label">RIF</label>
                    <input class="input" type="text" name="empresa_rif" value="<?php echo isset($datos['empresa_rif']) ? $datos['empresa_rif'] : ''; ?>" pattern="[a-zA-Z0-9\- ]{5,40}" maxlength="40" placeholder="Ej: J-12345678-9">
                </div>
            </div>
        </div>
        <div class="columns">
            <div class="column">
                <div class="control">
                    <label class="label">Teléfono</label>
                    <input class="input" type="text" name="empresa_telefono" value="<?php echo $datos['empresa_telefono']; ?>" pattern="[0-9()+]{8,20}" maxlength="20" >
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label class="label">Email</label>
                    <input class="input" type="email" name="empresa_email" value="<?php echo $datos['empresa_emailKV'] ?? ''; ?>" maxlength="50" >
                </div>
            </div>
        </div>
        <div class="columns">
            <div class="column">
                <div class="control">
                    <label class="label">Dirección</label>
                    <input class="input" type="text" name="empresa_direccion" value="<?php echo $datos['empresa_direccion']; ?>" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{4,97}" maxlength="97" >
                </div>
            </div>
        </div>
        
        <div class="columns">
            <div class="column">
                <label class="label">Actualizar Logo Principal (Para facturas y modo claro)</label>
                <div class="file is-small has-name is-info is-fullwidth">
                    <label class="file-label">
                        <input class="file-input" type="file" name="empresa_foto" accept=".jpg, .png, .jpeg" >
                        <span class="file-cta">
                            <span class="file-icon"><i class="fas fa-upload"></i></span>
                            <span class="file-label">Seleccione logo principal</span>
                        </span>
                        <span class="file-name has-text-left" style="max-width: 100%;">JPG, JPEG, PNG. (Máx. 3MB)</span>
                    </label>
                </div>
                
                <div class="field mt-4">
                    <label class="checkbox has-text-weight-bold" style="user-select: none;">
                        <input type="checkbox" id="check-logo-oscuro" name="usar_logo_oscuro" value="si">
                        <i class="fas fa-moon has-text-grey"></i> Deseo subir un logo diferente para el Modo Oscuro
                    </label>
                </div>
            </div>
        </div>

        <div class="columns" id="div-logo-oscuro" style="display: none; transition: all 0.3s ease;">
            <div class="column pt-0">
                <article class="message is-info is-small mb-3">
                    <div class="message-body py-2">
                        Se recomienda un logo en formato <strong>PNG con letras blancas o grises claras</strong> para que resalte en el menú oscuro de FastNet.
                    </div>
                </article>
                <div class="file is-small has-name is-info is-fullwidth">
                    <label class="file-label">
                        <input class="file-input" type="file" name="empresa_foto_dark" accept=".png, .jpg, .jpeg">
                        <span class="file-cta">
                            <span class="file-icon"><i class="fas fa-upload"></i></span>
                            <span class="file-label">Seleccione logo oscuro</span>
                        </span>
                        <span class="file-name has-text-left" style="max-width: 100%;">JPG, JPEG, PNG. (Máx. 3MB)</span>
                    </label>
                </div>
            </div>
        </div>

        <p class="has-text-centered mt-4">
            <button type="submit" class="button is-success is-rounded"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar Empresa</button>
        </p>
        <p class="has-text-centered pt-6">
            <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
        </p>
    </form>

    <?php }else{ ?>

    <h2 class="title has-text-centered mb-6">Registrar Datos de Empresa</h2>

    <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/empresaAjax.php" method="POST" autocomplete="off" enctype="multipart/form-data">

        <input type="hidden" name="modulo_empresa" value="registrar">

        <div class="columns">
            <div class="column">
                <div class="control">
                    <label class="label">Nombre <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="text" name="empresa_nombre" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ., ]{4,85}" maxlength="85" required >
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label class="label">RIF</label>
                    <input class="input" type="text" name="empresa_rif" pattern="[a-zA-Z0-9\- ]{5,40}" maxlength="40" placeholder="Ej: J-12345678-9">
                </div>
            </div>
        </div>
        <div class="columns">
            <div class="column">
                <div class="control">
                    <label class="label">Teléfono</label>
                    <input class="input" type="text" name="empresa_telefono" pattern="[0-9()+]{8,20}" maxlength="20" >
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label class="label">Email</label>
                    <input class="input" type="email" name="empresa_email" maxlength="50" >
                </div>
            </div>
        </div>
        <div class="columns">
            <div class="column">
                <div class="control">
                    <label class="label">Dirección</label>
                    <input class="input" type="text" name="empresa_direccion" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{4,97}" maxlength="97" >
                </div>
            </div>
        </div>

		<div class="columns">
            <div class="column">
                <label class="label">Actualizar Logo Principal (Para facturas y modo claro)</label>
                <div class="file is-small has-name is-info is-fullwidth">
                    <label class="file-label">
                        <input class="file-input" type="file" name="empresa_foto" accept=".jpg, .png, .jpeg">
                        <span class="file-cta">
                            <span class="file-icon"><i class="fas fa-upload"></i></span>
                            <span class="file-label">Seleccione logo principal</span>
                        </span>
                        <span class="file-name has-text-left" style="max-width: 100%;">JPG, JPEG, PNG. (Máx. 3MB)</span>
                    </label>
                </div>
                
                <div class="field mt-4">
                    <label class="checkbox has-text-weight-bold" style="user-select: none;">
                        <input type="checkbox" id="check-logo-oscuro" name="usar_logo_oscuro" value="si">
                        <i class="fas fa-moon has-text-grey"></i> Deseo subir un logo diferente para el Modo Oscuro
                    </label>
                </div>
            </div>
        </div>

        <div class="columns" id="div-logo-oscuro" style="display: none; transition: all 0.3s ease;">
            <div class="column pt-0">
                <article class="message is-info is-small mb-3">
                    <div class="message-body py-2">
                        Se recomienda un logo en formato <span class="has-text-weight-bold" style="color: #ff3860 !important;">PNG con letras blancas o grises claras</span> para que resalte en el menú oscuro de FastNet.
                    </div>
                </article>
                                
                <div class="file is-small has-name is-info is-fullwidth">
                    <label class="file-label">
                        <input class="file-input" type="file" name="empresa_foto_dark" accept=".png, .jpg, .jpeg">
                        <span class="file-cta">
                            <span class="file-icon"><i class="fas fa-upload"></i></span>
                            <span class="file-label">Seleccione logo oscuro</span>
                        </span>
                        <span class="file-name has-text-left" style="max-width: 100%;">JPG, JPEG, PNG. (Máx. 3MB)</span>
                    </label>
                </div>
            </div>
        </div>

        <p class="has-text-centered mt-4">
            <button type="reset" class="button is-link is-light is-rounded"><i class="fas fa-paint-roller"></i> &nbsp; Limpiar</button>
            <button type="submit" class="button is-info is-rounded"><i class="far fa-save"></i> &nbsp; Guardar Empresa</button>
        </p>
        <p class="has-text-centered pt-6">
            <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
        </p>
    </form>

    <?php } ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const fileInputs = document.querySelectorAll('.file-input');
        
        fileInputs.forEach(input => {
            input.addEventListener('change', function() {
                const fileNameSpan = this.closest('.file').querySelector('.file-name');
                
                if (this.files && this.files.length > 0) {
                    const file = this.files[0];
                    fileNameSpan.textContent = file.name;
                    
                    // Si es una imagen, la enviamos directamente al logo principal de arriba
                    if(file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const targetImages = document.querySelectorAll('.logo-preview-target');
                            
                            targetImages.forEach(img => {
                                // Animación de parpadeo suave para indicar actualización
                                img.style.opacity = '0.3';
                                setTimeout(() => {
                                    img.src = e.target.result;
                                    img.style.opacity = '1';
                                }, 150);
                            });
                        }
                        
                        reader.readAsDataURL(file);
                    }
                    
                } else {
                    fileNameSpan.textContent = 'JPG, JPEG, PNG. (Máx. 3MB)';
                }
            });
        });

        // Mostrar/Ocultar el input del logo oscuro al hacer clic en el check
        const checkLogoOscuro = document.getElementById('check-logo-oscuro');
        const divLogoOscuro = document.getElementById('div-logo-oscuro');

        if(checkLogoOscuro && divLogoOscuro) {
            checkLogoOscuro.addEventListener('change', function() {
                if(this.checked) {
                    divLogoOscuro.style.display = 'flex';
                    divLogoOscuro.style.opacity = '0';
                    setTimeout(() => divLogoOscuro.style.opacity = '1', 50);
                } else {
                    divLogoOscuro.style.display = 'none';
                    
                    const darkInput = divLogoOscuro.querySelector('.file-input');
                    const darkFileName = divLogoOscuro.querySelector('.file-name');
                    
                    if(darkInput) darkInput.value = '';
                    if(darkFileName) darkFileName.textContent = 'JPG, JPEG, PNG. (Máx. 3MB)';
                }
            });
        }
    });
</script>