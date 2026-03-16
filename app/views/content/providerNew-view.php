<div class="container is-fluid mb-6">
    <h1 class="title">Proveedores</h1>
    <h2 class="subtitle">Nuevo proveedor</h2>
</div>

<div class="container is-fluid pb-6">
    <?php
        if($_SESSION['rol'] != 1 && $_SESSION['rol'] != 3){
            echo '<div class="notification is-danger is-light has-text-centered"><i class="fas fa-ban fa-3x"></i><br><h1 class="title">¡Acceso Denegado!</h1></div>';
            exit(); 
        }
    ?>

    <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/proveedorAjax.php" method="POST" autocomplete="off" >

        <input type="hidden" name="modulo_proveedor" value="registrar">

        <div class="columns">
            <div class="column">
                <div class="control">
                    <label>Nombre del Proveedor <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="text" name="proveedor_nombre" data-filtro="empresa" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{3,70}" minlength="3" maxlength="70" required autocomplete="off" title="El nombre de la empresa debe tener al menos 3 caracteres.">
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>RIF / Identificación <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="text" name="proveedor_rif" data-filtro="rif" pattern="[0-9\-]{8,14}" minlength="8" maxlength="14" placeholder="Ej: 12345678-9" required autocomplete="off" title="Solo números y guiones (-). Mínimo 8 caracteres.">
                </div>
            </div>

        </div>
        <div class="columns">
            <div class="column is-4">
                <div class="control">
                    <label>Teléfono de contacto <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <div class="field has-addons">
                        <p class="control">
                            <span class="select">
                                <select name="proveedor_telefono_codigo" required title="Seleccione código de área.">
                                    <option value="" selected>Cód.</option>
                                    <?php echo $insLogin->generarSelect(PREFIJOS_TELEFONICOS, "VACIO"); ?>
                                </select>
                            </span>
                        </p>
                        <p class="control is-expanded">
                            <input class="input only-numbers" type="text" name="proveedor_telefono" pattern="[0-9]{7}" minlength="7" maxlength="7" placeholder="1234567" required autocomplete="off" title="El teléfono debe tener 7 dígitos exactos.">
                        </p>
                    </div>
                </div>
            </div>
            <div class="column is-8">
                <div class="control">
                    <label>Dirección</label>
                    <input class="input" type="text" name="proveedor_direccion" maxlength="200" autocomplete="off">
                </div>
            </div>
        </div>
        <p class="has-text-centered">
            <button type="reset" class="button is-link is-light is-rounded"><i class="fas fa-paint-roller"></i> &nbsp; Limpiar</button>
            <button type="submit" class="button is-info is-rounded"><i class="far fa-save"></i> &nbsp; Guardar</button>
        </p>
    </form>
</div>

<?php include "./app/views/inc/script_validador.php"; ?>