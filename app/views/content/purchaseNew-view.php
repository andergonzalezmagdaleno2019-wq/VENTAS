<div class="container is-fluid mb-6">
    <h1 class="title">Compras</h1>
    <h2 class="subtitle">Nueva Orden de Compra (Entrada de Almacén) - <strong>FastNet</strong></h2>
</div>

<div class="container is-fluid pb-6">
    <div class="columns">
        <div class="column is-one-third">
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">
                        <i class="fas fa-tags"></i> &nbsp; Categorías
                    </p>
                </header>
                <div class="card-content" style="max-height: 450px; overflow-y: auto;">
                    <?php
                        $datos_cat = $insLogin->seleccionarDatos("Normal", "categoria", "*", "ORDER BY categoria_nombre ASC");
                        if($datos_cat->rowCount() > 0){
                            $todas = $datos_cat->fetchAll();
                            foreach($todas as $p){
                                if($p['categoria_padre_id'] == NULL || $p['categoria_padre_id'] == "" || $p['categoria_padre_id'] == "0"){
                                    // Verificar si tiene hijos
                                    $tiene_hijos = false;
                                    foreach($todas as $h){
                                        if($h['categoria_padre_id'] == $p['categoria_id']){
                                            $tiene_hijos = true;
                                            break;
                                        }
                                    }
                                    
                                    // Si tiene hijos, mostramos como acordeón
                                    if($tiene_hijos) {
                                        echo '<div class="mb-2">';
                                        echo '<button class="button is-fullwidth has-text-left p-2 mb-1 acordeon-btn" style="border: none; background-color: #f0f0f0; border-radius: 4px; cursor: pointer;" onclick="toggleAcordeon(this)">
                                                <span style="display: flex; align-items: center; width: 100%;">
                                                    <i class="fas fa-folder-open" style="margin-right: 8px;"></i>
                                                    <span style="flex-grow: 1; font-weight: bold;">'.mb_strtoupper($p['categoria_nombre'], 'UTF-8').'</span>
                                                    <i class="fas fa-chevron-down acordeon-icono"></i>
                                                </span>
                                              </button>';
                                        echo '<div class="acordeon-contenido" style="display: none; padding-left: 15px;">';
                                        foreach($todas as $h){
                                            if($h['categoria_padre_id'] == $p['categoria_id']){
                                                echo '<button type="button" class="button is-fullwidth is-small is-outlined is-link mb-1" 
                                                        onclick="cargar_por_categoria_compra('.$h['categoria_id'].')">
                                                        <i class="fas fa-arrow-right"></i> &nbsp; '.$h['categoria_nombre'].'
                                                      </button>';
                                            }
                                        }
                                        echo '</div>';
                                        echo '</div>';
                                    } else {
                                        // Si no tiene hijos, mostramos solo el botón de categoría padre
                                        echo '<button type="button" class="button is-fullwidth has-text-left p-2 mb-2" 
                                                style="border: none; background-color: #f0f0f0; border-radius: 4px; cursor: pointer;"
                                                onclick="cargar_por_categoria_compra('.$p['categoria_id'].')">
                                                <span style="display: flex; align-items: center;">
                                                    <i class="fas fa-folder" style="margin-right: 8px;"></i>
                                                    <span style="flex-grow: 1; font-weight: bold;">'.mb_strtoupper($p['categoria_nombre'], 'UTF-8').'</span>
                                                </span>
                                              </button>';
                                    }
                                }
                            }
                        } else {
                            echo '<p class="has-text-centered">No hay categorías registradas</p>';
                        }
                    ?>
                </div>
            </div>
        </div>

        <div class="column">
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">
                        <i class="fas fa-search"></i> &nbsp; Buscar producto a comprar
                    </p>
                </header>
                <div class="card-content">
                    <form action="" method="POST" autocomplete="off" id="form-buscar-compra">
                        <div class="field has-addons">
                            <div class="control is-expanded">
                                <input class="input" type="text" name="buscar_producto" id="buscar_producto" placeholder="Nombre o código del producto" required autocomplete="off">
                            </div>
                            <div class="control">
                                <button type="submit" class="button is-info"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                    <div id="resultados_busqueda" class="mt-4" style="max-height: 250px; overflow-y: auto;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="columns mt-4">
        <div class="column">
            <form action="<?php echo APP_URL; ?>app/ajax/compraAjax.php" method="POST" autocomplete="off" name="formpurchase">
                <input type="hidden" name="modulo_compra" value="registrar">
                
                <div class="box">
                    <div class="columns">
                        <div class="column is-4">
                            <label class="label">Seleccione Proveedor</label>
                            <div class="select is-fullwidth">
                                <select name="compra_proveedor" required>
                                    <option value="" selected="">Seleccione una opción</option>
                                    <?php
                                        $datos_proveedores=$insLogin->seleccionarDatos("Normal","proveedor","*",0);
                                        while($campos_proveedor=$datos_proveedores->fetch()){
                                            echo '<option value="'.$campos_proveedor['proveedor_id'].'">'.$campos_proveedor['proveedor_nombre'].' (RIF: '.$campos_proveedor['proveedor_rif'].')</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="column is-8 has-text-right pt-2">
                            <?php 
                                $total = 0;
                                if(isset($_SESSION['datos_compra'])){
                                    foreach($_SESSION['datos_compra'] as $prod){ $total += $prod['subtotal']; }
                                }
                            ?>
                            <h4 class="title is-4 has-text-grey-dark mb-1">Total: $<?php echo number_format($total, 2); ?></h4>
                            <h3 class="title is-3 has-text-link mt-0" id="total_compra_bs_label">Calculando Bs...</h3>
                        </div>
                    </div>

                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth mt-4">
                        <thead>
                            <tr class="has-background-link-light">
                                <th>Producto</th>
                                <th class="has-text-centered">Cant.</th>
                                <th class="has-text-centered">Costo Unit.</th>
                                <th class="has-text-centered">Subtotal</th>
                                <th class="has-text-centered">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(isset($_SESSION['datos_compra']) && count($_SESSION['datos_compra'])>=1){
                                    foreach($_SESSION['datos_compra'] as $detalle){
                                        echo '<tr>
                                            <td>'.$detalle['producto_nombre'].'</td>
                                            <td class="has-text-centered">'.$detalle['compra_cantidad'].'</td>
                                            <td class="has-text-centered">
                                                <strong>$'.$detalle['compra_costo'].'</strong><br>
                                                <span class="is-size-7 has-text-grey precio-bcv-cart" data-usd="'.$detalle['compra_costo'].'">Calculando Bs...</span>
                                            </td>
                                            <td class="has-text-centered">
                                                <strong>$'.number_format($detalle['subtotal'],2).'</strong><br>
                                                <span class="is-size-7 has-text-link has-text-weight-bold precio-bcv-cart" data-usd="'.$detalle['subtotal'].'">Calculando Bs...</span>
                                            </td>
                                            <td class="has-text-centered">
                                                <button type="button" class="button is-danger is-small is-rounded" onclick="eliminarDelCarrito('.$detalle['producto_id'].')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>';
                                    }
                                }else{
                                    echo '<tr class="has-text-centered"><td colspan="5">No hay productos agregados a esta compra</td></tr>';
                                }
                            ?>
                        </tbody>
                    </table>

                    <?php if(isset($_SESSION['datos_compra']) && count($_SESSION['datos_compra'])>=1){ ?>
                    
                    <hr>
                    <div class="columns">
                        <div class="column is-4">
                            <div class="field">
                                <label class="label"><i class="fas fa-money-bill-wave"></i> Abono Inicial ($)</label>
                                <div class="control">
                                    <input class="input" type="number" step="0.01" name="compra_pago_inicial" value="0.00" min="0" max="<?php echo $total; ?>">
                                </div>
                                <p class="help">Si se paga el total, la orden quedará como "Pagada".</p>
                            </div>
                        </div>
                        <div class="column is-4">
                            <div class="field">
                                <label class="label"><i class="fas fa-calendar-alt"></i> Fecha de Vencimiento</label>
                                <div class="control">
                                    <input class="input" type="date" name="compra_fecha_vencimiento" value="<?php echo date("Y-m-d"); ?>" required>
                                </div>
                                <p class="help">¿Cuándo debe terminar de pagarse?</p>
                            </div>
                        </div>
                        <div class="column is-4">
                            <div class="field">
                                <label class="label"><i class="fas fa-sticky-note"></i> Nota Interna</label>
                                <div class="control">
                                    <input class="input" type="text" name="compra_nota" placeholder="Ej: Factura #123">
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="has-text-centered mt-5">
                        <button type="submit" class="button is-success is-large is-rounded shadow-sm">
                            <i class="fas fa-save"></i> &nbsp; PROCESAR COMPRA
                        </button>
                    </p>
                    <?php } ?>

                    <input type="hidden" id="compra_total_hidden" value="<?php echo number_format($total, 2, '.', ''); ?>">
                    <input type="hidden" name="compra_tasa_bcv" id="compra_tasa_bcv" value="0">
                </div>
            </form>
            
            <?php if(isset($_SESSION['datos_compra']) && count($_SESSION['datos_compra'])>=1){ ?>
            <div class="has-text-centered mt-2">
                <button type="button" class="button is-danger is-outlined is-small" onclick="vaciarCarritoCompleto()">
                    Vaciar lista de compra
                </button>
            </div>
            <?php } ?>
        </div>
    </div>

    <script>
        // Función para el acordeón
        function toggleAcordeon(boton) {
            // Encontrar el contenedor de contenido que sigue al botón
            let contenido = boton.nextElementSibling;
            let icono = boton.querySelector('.acordeon-icono');
            
            if (contenido.style.display === 'none' || contenido.style.display === '') {
                contenido.style.display = 'block';
                if (icono) {
                    icono.style.transform = 'rotate(180deg)';
                    icono.style.transition = 'transform 0.3s ease';
                }
            } else {
                contenido.style.display = 'none';
                if (icono) {
                    icono.style.transform = 'rotate(0deg)';
                    icono.style.transition = 'transform 0.3s ease';
                }
            }
        }

        const resultadoBusqueda = document.getElementById('resultados_busqueda');

        // Función para eliminar un producto específico
        function eliminarDelCarrito(id){
            Swal.fire({
                title: '¿Quieres quitar este producto?',
                text: "Se eliminará de la lista actual",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    let datos = new FormData();
                    datos.append('modulo_compra', 'eliminar_producto_carrito');
                    datos.append('producto_id', id);

                    fetch('<?php echo APP_URL; ?>app/ajax/compraAjax.php', {
                        method: 'POST',
                        body: datos
                    })
                    .then(respuesta => respuesta.json())
                    .then(respuesta => {
                        return alertas_ajax(respuesta);
                    });
                }
            });
        }

        // Función para vaciar todo el carrito
        function vaciarCarritoCompleto(){
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Se quitarán todos los productos de la compra",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, vaciar todo',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    let datos = new FormData();
                    datos.append('modulo_compra', 'vaciar');

                    fetch('<?php echo APP_URL; ?>app/ajax/compraAjax.php', {
                        method: 'POST',
                        body: datos
                    })
                    .then(respuesta => respuesta.json())
                    .then(respuesta => {
                        return alertas_ajax(respuesta);
                    });
                }
            });
        }

        function cargar_por_categoria_compra(id){
            let datos = new FormData();
            datos.append('categoria_id', id);
            datos.append('modulo_compra', 'buscar_por_categoria');
            fetch('<?php echo APP_URL; ?>app/ajax/compraAjax.php',{ method: 'POST', body: datos })
            .then(respuesta => respuesta.text())
            .then(respuesta =>{
                resultadoBusqueda.innerHTML = respuesta;
                reactivarFormularios();
            });
        }

        (function(){
            let timer = null;
            let input = document.querySelector('#buscar_producto');
            let form = document.querySelector('#form-buscar-compra');
            if(input){
                input.addEventListener('keyup', function(e){
                    clearTimeout(timer);
                    timer = setTimeout(function(){
                        if(input.value.trim().length > 0){ form.dispatchEvent(new Event('submit')); } 
                        else { resultadoBusqueda.innerHTML = ''; } 
                    }, 300);
                });
            }
        })();

        document.getElementById('form-buscar-compra').addEventListener('submit', function(e){
            e.preventDefault();
            let datos = new FormData(this);
            datos.append('modulo_compra', 'buscar_producto');
            fetch('<?php echo APP_URL; ?>app/ajax/compraAjax.php', { method: 'POST', body: datos })
            .then(respuesta => respuesta.text())
            .then(respuesta => {
                resultadoBusqueda.innerHTML = respuesta;
                reactivarFormularios();
            });
        });

        function reactivarFormularios(){
            resultadoBusqueda.querySelectorAll(".FormularioAjax").forEach(form => {
                form.addEventListener("submit", function(e){
                    e.preventDefault(); 
                    let data = new FormData(this);
                    fetch(this.getAttribute("action"), { method: this.getAttribute("method"), body: data })
                    .then(respuesta => respuesta.json())
                    .then(respuesta => { return alertas_ajax(respuesta); });
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            let tasa_bcv = parseFloat(localStorage.getItem('tasa_bcv')) || 0;
            document.querySelectorAll('.precio-bcv-cart').forEach(function(el) {
                let usd = parseFloat(el.getAttribute('data-usd')) || 0;
                if(tasa_bcv > 0){
                    let formatBs = new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2 }).format(usd * tasa_bcv);
                    el.innerHTML = `Bs. ${formatBs}`;
                } else { el.innerHTML = `<span class="has-text-danger">Sin BCV</span>`; }
            });

            let total_dolares = document.querySelector('#compra_total_hidden');
            if(total_dolares){
                let total_num = parseFloat(total_dolares.value) || 0;
                let label = document.querySelector('#total_compra_bs_label');
                if(tasa_bcv > 0 && total_num > 0) {
                    label.innerHTML = `Bs. ${new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2 }).format(total_num * tasa_bcv)}`;
                } else { label.innerHTML = `Bs. 0.00`; }
            }
        });

            document.querySelector("form[name='formpurchase']").addEventListener('submit', function(e){
                e.preventDefault(); // <--- CRUCIAL: Detiene el envío doble

                // 1. Asignamos la tasa al campo oculto antes de leer los datos
                let tasa = parseFloat(localStorage.getItem('tasa_bcv')) || 0;
                document.querySelector('#compra_tasa_bcv').value = tasa;

                // 2. Capturamos los datos del formulario
                let datos = new FormData(this);

                // 3. Enviamos por Fetch
                fetch(this.getAttribute("action"), {
                    method: this.getAttribute("method"),
                    body: datos
                })
                .then(respuesta => respuesta.json())
                .then(respuesta => {
                    // Manejo de la respuesta especial para el PDF
                    if(respuesta.tipo == "confirmar"){
                        Swal.fire({
                            title: respuesta.titulo,
                            text: respuesta.texto,
                            icon: respuesta.icono,
                            showCancelButton: true,
                            confirmButtonText: respuesta.confirmButtonText,
                            cancelButtonText: respuesta.cancelButtonText
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.open(respuesta.url, '_blank');
                                location.reload();
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        // Si es un error o alerta normal, usamos la función del sistema
                        return alertas_ajax(respuesta);
                    }
                });
            });
    </script>
</div>