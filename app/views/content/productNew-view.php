<div class="container is-fluid mb-6">
    <h1 class="title">Productos</h1>
    <h2 class="subtitle"><i class="fas fa-box"></i> &nbsp; Nuevo producto (Catálogo Maestro)</h2>
</div>

<div class="container is-fluid pb-6">
    
    <?php
        /*---------- Bloque de seguridad: Admin (1) y Supervisor (3) ----------*/
        if($_SESSION['rol'] != 1 && $_SESSION['rol'] != 3){
            echo '
            <div class="notification is-danger is-light has-text-centered">
                <br>
                <i class="fas fa-ban fa-3x"></i><br>
                <h1 class="title mt-4">¡Acceso Denegado!</h1>
                <p>No tienes los permisos necesarios para acceder a este módulo.</p>
                <br>
                <a href="'.APP_URL.'dashboard/" class="button is-danger is-rounded">Regresar al Inicio</a>
                <br><br>
            </div>';
            exit(); 
        }
    ?>

    <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/productoAjax.php" method="POST" autocomplete="off" enctype="multipart/form-data">

        <input type="hidden" name="modulo_producto" value="registrar">

        <div class="columns">
            <div class="column is-4">
                <div class="control">
                    <label>Código de barra <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="text" name="producto_codigo"
                        pattern="[0-9]{1,13}"
                        maxlength="13"
                        placeholder="Solo números (máx. 13)"
                        required autocomplete="off">
                </div>
            </div>
            <div class="column is-8">
                <div class="control">
                    <label>Nombre del Producto <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="text" name="producto_nombre" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,100}" maxlength="100" placeholder="Ej: Laptop Gamer ASUS" required autocomplete="off">
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column is-4">
                <div class="control">
                    <label>Marca</label>
                    <input class="input" type="text" name="producto_marca" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()/\- ]{4,50}" maxlength="30" style="text-transform: uppercase"
                    oninput="this.value = this.value.toUpperCase()" 
                    autocomplete="off">
                </div>
            </div>
            <div class="column is-4">
                <div class="control">
                    <label>Modelo</label>
                    <input class="input" type="text" name="producto_modelo" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,30}" maxlength="30" autocomplete="off">
                </div>
            </div>
            <div class="column is-4">
                <div class="control">
                    <label>Categoría / Subcategoría <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <div class="select is-fullwidth">
                        <select name="producto_categoria" required>
                            <option value="" selected="">Seleccione una opción</option>
                            <?php
                                use app\controllers\categoryController;
                                $insCategory = new categoryController();
                                $datos = $insCategory->seleccionarDatos("Normal", "categoria", "*", "ORDER BY categoria_nombre ASC");
                                $todas = $datos->fetchAll();

                                foreach($todas as $p){
                                    if($p['categoria_padre_id'] == NULL || $p['categoria_padre_id'] == "" || $p['categoria_padre_id'] == "0"){
                                        echo '<optgroup label="📂 '.$p['categoria_nombre'].'">';
                                        foreach($todas as $h){
                                            if($h['categoria_padre_id'] == $p['categoria_id']){
                                                $mis_unidades = isset($h['categoria_unidades']) ? $h['categoria_unidades'] : "Unidad";
                                                echo '<option value="'.$h['categoria_id'].'" data-unidades="'.$mis_unidades.'">'.$h['categoria_nombre'].'</option>';
                                            }
                                        }
                                        echo '</optgroup>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

        </div>

        <div class="columns">
            <div class="column is-4">
                <div class="control">
                    <label>Stock Mínimo (Alerta) <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="text" name="producto_stock_min" pattern="[0-9]{1,25}" maxlength="25" required value="5">
                </div>
            </div>
            <div class="column is-4">
                <div class="control">
                    <label>Stock Máximo (Límite) <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="text" name="producto_stock_max" pattern="[0-9]{1,25}" maxlength="25" required value="100">
                </div>
            </div>
            <div class="column is-4">
                <div class="control">
                    <label>Tipo de Producto (Presentación) <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <div class="select is-fullwidth">
                        <select name="producto_unidad" required>
                            <option value="" selected="">Seleccione una opción</option>
                            <?php echo $insLogin->generarSelect(PRODUCTO_UNIDAD, "VACIO"); ?>
                        </select>
                    </div>
                </div>
            </div>
            
        </div>

        <div class="columns" id="div_unidades_caja" style="display: none;">
            <div class="column is-half">
                <div class="notification is-primary is-light p-3">
                    <label class="has-text-weight-bold"><i class="fas fa-box-open"></i> &nbsp; Unidades por Caja <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <div class="control mt-2">
                        <input class="input" type="number" name="producto_unidades_caja" id="producto_unidades_caja" value="1" min="1" pattern="[0-9]{1,10}" placeholder="Ej: 24">
                    </div>
                    <p class="help is-dark">Indique cuántas unidades individuales contiene esta caja.</p>
                </div>
            </div>
        </div>

        <div class="column is-full pl-0 pr-0">
            <div class="notification is-link is-light" style="border-left: 5px solid #3273dc;">
                <i class="fas fa-info-circle fa-lg"></i> &nbsp; 
                <strong>POLÍTICA DE INVENTARIO:</strong> Todo nuevo producto se registrará en el catálogo con <strong>0 existencias y Costo $0.00</strong>. 
                Para ingresar mercancía y establecer su precio real, debe procesar una factura de proveedor en el módulo de <strong>Compras -> Recibir Camión</strong>.
            </div>
        </div>

        <div class="columns">
            <div class="column is-full">
                <div class="field">
                    <label class="label">Proveedores que distribuyen este producto <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <div class="control">
                        <div class="select is-multiple is-fullwidth">
                            <select name="producto_proveedores[]" id="producto_proveedores" multiple size="5" required>
                                <?php
                                    $query_prov = $insLogin->seleccionarDatos("Normal", "proveedor", "*", "ORDER BY proveedor_nombre ASC");
                                    $provs = $query_prov->fetchAll();
                                    
                                    if(count($provs) > 0){
                                        foreach($provs as $prov){
                                            echo '<option value="'.$prov['proveedor_id'].'">📦 '.$prov['proveedor_nombre'].' ('.$prov['proveedor_rif'].')</option>';
                                        }
                                    } else {
                                        echo '<option value="" disabled>⚠️ No hay proveedores registrados</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <p class="help is-danger" id="msg-error-proveedor" style="display: none;">Debe seleccionar al menos un proveedor.</p>
                        <p class="help"><i class="fas fa-info-circle"></i> Use <strong>Ctrl + Clic</strong> para seleccionar varios proveedores simultáneamente.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="columns">
            <div class="column">
                <label>Foto o imagen del producto</label><br>
                <div class="file is-small has-name is-boxed">
                    <label class="file-label">
                        <input class="file-input" type="file" name="producto_foto" accept=".jpg, .png, .jpeg">
                        <span class="file-cta">
                            <span class="file-icon"><i class="fas fa-upload"></i></span>
                            <span class="file-label">Seleccione una imagen</span>
                        </span>
                        <span class="file-name">JPG, JPEG, PNG. (MAX 3MB)</span>
                    </label>
                </div>
            </div>
        </div>

        <p class="has-text-centered mt-4">
            <button type="reset" class="button is-link is-light is-rounded">Limpiar</button>
            <button type="submit" class="button is-info is-rounded"><i class="fas fa-save"></i> &nbsp; Guardar Producto</button>
        </p>
    </form>

    <script>
        // --- FILTRADO INTELIGENTE DE UNIDADES ---
        const selectCategoria = document.querySelector('select[name="producto_categoria"]');
        const selectUnidad = document.querySelector('select[name="producto_unidad"]');
        
        const opcionesUnidadOriginales = Array.from(selectUnidad.options).map(opt => ({value: opt.value, text: opt.text}));

        selectCategoria.addEventListener('change', function() {
            let selectedOption = this.options[this.selectedIndex];
            let unidadesPermitidas = selectedOption.getAttribute('data-unidades');
            
            selectUnidad.innerHTML = '<option value="" selected="">Seleccione una opción</option>';

            if (unidadesPermitidas) {
                let arrayPermitidas = unidadesPermitidas.split(',');
                
                opcionesUnidadOriginales.forEach(opt => {
                    if(opt.value !== "" && arrayPermitidas.includes(opt.value)) {
                        let nuevaOpcion = document.createElement('option');
                        nuevaOpcion.value = opt.value;
                        nuevaOpcion.text = opt.text;
                        selectUnidad.appendChild(nuevaOpcion);
                    }
                });
            }
        });

        // Para que sea obligatorio el proveedor
        document.querySelector('.FormularioAjax').addEventListener('submit', function(e){
            let proveedores = document.getElementById('producto_proveedores');
            if (proveedores.selectedOptions.length === 0) {
                e.preventDefault(); 
                Swal.fire({
                    title: "Falta Información",
                    text: "Es obligatorio asignar al menos un proveedor a este producto.",
                    icon: "warning"
                });
                document.getElementById('msg-error-proveedor').style.display = "block";
            }
        });

        // --- MOSTRAR/OCULTAR UNIDADES POR CAJA ---
        document.addEventListener('change', function(e) {
            if (e.target && e.target.name === 'producto_unidad') {
                const divUnidadesCaja = document.getElementById('div_unidades_caja');
                const inputUnidadesCaja = document.getElementById('producto_unidades_caja');
                
                if (e.target.value === "Caja") {
                    divUnidadesCaja.style.display = "flex";
                    inputUnidadesCaja.value = "";
                    inputUnidadesCaja.setAttribute("required", "true");
                    inputUnidadesCaja.focus();
                } else {
                    divUnidadesCaja.style.display = "none";
                    inputUnidadesCaja.value = "1";
                    inputUnidadesCaja.removeAttribute("required");
                }
            }
        });
    </script>
</div>