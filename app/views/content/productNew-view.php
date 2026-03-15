<div class="container is-fluid mb-6">
    <h1 class="title">Productos</h1>
    <h2 class="subtitle">Nuevo producto</h2>
</div>

<div class="container is-fluid pb-6">

    <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/productoAjax.php" method="POST" autocomplete="off" enctype="multipart/form-data">

        <input type="hidden" name="modulo_producto" value="registrar">

        <div class="columns">
            <div class="column">
                <div class="control">
                    <label>Código de barra <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="text" name="producto_codigo"
                        pattern="[0-9]{1,13}"
                        maxlength="13"
                        placeholder="Solo números (máx. 13)"
                        required autocomplete="off">
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Nombre <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="text" name="producto_nombre" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,70}" maxlength="70" required autocomplete="off">
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="control">
                    <label>Marca</label>
                    <input class="input" type="text" name="producto_marca" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()/\- ]{4,50}" maxlength="30" style="text-transform: uppercase"
                    oninput="this.value = this.value.toUpperCase()" 
                    autocomplete="off">
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Modelo</label>
                    <input class="input" type="text" name="producto_modelo" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,30}" maxlength="30" autocomplete="off">
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="control">
                    <label>Costo de Compra (Neto) $</label>
                    <input class="input" type="text" name="producto_costo" id="producto_costo" 
                        pattern="[0-9.]{1,25}" maxlength="25" required 
                        placeholder="0.00" autocomplete="off">
                    <p class="help is-info has-text-weight-bold" id="costo_bs_label">Bs. 0.00</p>
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Precio de Venta (Costo + 20%) $</label>
                    <input class="input" type="text" name="producto_precio" id="producto_precio" pattern="[0-9.]{1,25}" maxlength="25" required value="0.00" readonly style="background-color: #f0f0f0;">
                    <p class="help is-link has-text-weight-bold" id="precio_bs_label">Bs. 0.00</p>
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Tipo de Producto <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <div class="select is-fullwidth">
                        <select name="producto_unidad" required>
                            <option value="" selected="">Seleccione una opción</option>
                            <?php
                            echo $insLogin->generarSelect(PRODUCTO_UNIDAD, "VACIO");
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="control">
                    <label>Stock Mínimo (Alerta) <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="text" name="producto_stock_min" pattern="[0-9]{1,25}" maxlength="25" required value="5">
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Stock Máximo (Límite) <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="text" name="producto_stock_max" pattern="[0-9]{1,25}" maxlength="25" required value="100">
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
        <div class="control">
            <label>Categoría / Subcategoría</label><br>
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
        <div class="column is-full">
            <div class="notification is-link is-light">
                <button class="delete"></button>
                <i class="fas fa-info-circle"></i> &nbsp; 
                <strong>Nota informativa:</strong> El inventario inicial de este producto se registrará con un valor de <strong>0</strong>. 
                Para surtir o aumentar el stock, debe realizar una <strong>Orden de Compra</strong> en el módulo correspondiente una vez creado el producto.
            </div>
         </div>
        <br>
        <div class="columns">
            <div class="column">
                <label>Foto o imagen del producto</label><br>
                <div class="file is-small has-name">
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
        <p class="has-text-centered">
            <button type="reset" class="button is-link is-light is-rounded">Limpiar</button>
            <button type="submit" class="button is-info is-rounded">Guardar</button>
        </p>
    </form>

    <script>
        const inputCosto = document.getElementById('producto_costo');
        const inputPrecio = document.getElementById('producto_precio');
        const costoBsLabel = document.getElementById('costo_bs_label');
        const precioBsLabel = document.getElementById('precio_bs_label');
        let tasa_bcv = parseFloat(localStorage.getItem('tasa_bcv')) || 0;

        inputCosto.addEventListener('input', function() {
            let costo = parseFloat(this.value);
            if (!isNaN(costo)) {
                let ganancia = costo * 0.20;
                let precioFinal = costo + ganancia;
                inputPrecio.value = precioFinal.toFixed(2);

                if (tasa_bcv > 0) {
                    let formatBs = new Intl.NumberFormat('es-VE', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    costoBsLabel.innerText = "Bs. " + formatBs.format(costo * tasa_bcv);
                    precioBsLabel.innerText = "Bs. " + formatBs.format(precioFinal * tasa_bcv);
                } else {
                    costoBsLabel.innerText = "Sin conexión BCV";
                    precioBsLabel.innerText = "Sin conexión BCV";
                }
            } else {
                inputPrecio.value = "0.00";
                costoBsLabel.innerText = "Bs. 0.00";
                precioBsLabel.innerText = "Bs. 0.00";
            }
        });

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