<?php include './app/views/content/logo.php'; ?>
<div class="container is-fluid mb-4">
    <h1 class="title">Compras</h1>
    <h2 class="subtitle">Generar Orden de Compra - <strong>FastNet</strong></h2>
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
                                    $tiene_hijos = false;
                                    foreach($todas as $h){
                                        if($h['categoria_padre_id'] == $p['categoria_id']){ $tiene_hijos = true; break; }
                                    }
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
                                                echo '<button type="button" class="button is-fullwidth is-small is-outlined is-link mb-1" onclick="cargar_por_categoria_compra('.$h['categoria_id'].')">
                                                        <i class="fas fa-arrow-right"></i> &nbsp; '.$h['categoria_nombre'].'
                                                      </button>';
                                            }
                                        }
                                        echo '</div></div>';
                                    } else {
                                        echo '<button type="button" class="button is-fullwidth has-text-left p-2 mb-2" style="border: none; background-color: #f0f0f0; border-radius: 4px; cursor: pointer;" onclick="cargar_por_categoria_compra('.$p['categoria_id'].')">
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
                        <i class="fas fa-search"></i> &nbsp; Buscar producto a pedir
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
            <form action="<?php echo APP_URL; ?>app/ajax/compraAjax.php" method="POST" autocomplete="off" name="formpurchase" id="form-generar-orden">
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
                            <h3 class="title is-4 has-text-link mt-0"><i class="fas fa-clipboard-list"></i> Orden de Compra (Cotización)</h3>
                            <p class="subtitle is-6 has-text-grey">Tasa BCV del día: <strong class="has-text-success">Bs <span id="display_tasa_bcv">0.00</span></strong></p>
                        </div>
                    </div>

                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth mt-4">
                        <thead>
                            <tr class="has-background-link-light">
                                <th>Producto Pedido</th>
                                <th class="has-text-centered" style="width: 130px;">Costo Ref.</th>
                                <th class="has-text-centered" style="width: 160px;">Costo Pactado ($)</th>
                                <th class="has-text-centered" style="width: 120px;">Cantidad</th>
                                <th class="has-text-centered" style="width: 150px;">Subtotal</th>
                                <th class="has-text-centered" style="width: 60px;"><i class="fas fa-trash"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $total_estimado_orden = 0;
                                if(isset($_SESSION['datos_compra']) && count($_SESSION['datos_compra'])>=1){
                                    foreach($_SESSION['datos_compra'] as $detalle){
                                        $id_prod = $detalle['producto_id'];
                                        $cant_actual = $detalle['compra_cantidad'];
                                        $costo_ref = (isset($detalle['costo_referencia']) && $detalle['costo_referencia'] > 0) ? $detalle['costo_referencia'] : 0;
                                        
                                        $subtotal_est = $cant_actual * $costo_ref;
                                        $total_estimado_orden += $subtotal_est;

                                        $costo_ref_txt = ($costo_ref > 0) ? "$".number_format($costo_ref, 2) : 'Nuevo ($0.00)';

                                        echo '<tr class="fila-producto">
                                            <td style="vertical-align: middle;"><strong>'.$detalle['producto_nombre'].'</strong></td>
                                            <td class="has-text-centered has-text-grey" style="vertical-align: middle;">'.$costo_ref_txt.'</td>
                                            
                                            <td style="vertical-align: middle;">
                                                <div class="control">
                                                    <input class="input input-precio has-text-centered has-text-weight-bold is-primary" type="number" step="0.01" name="detalle_precio['.$id_prod.']" value="'.$costo_ref.'" min="'.$costo_ref.'" data-min="'.$costo_ref.'" required oninput="recalcularTotales()">
                                                </div>
                                            </td>
                                            
                                            <td style="vertical-align: middle;">
                                                <div class="control">
                                                    <input class="input input-cantidad has-text-centered" type="number" name="detalle_cantidad['.$id_prod.']" value="'.$cant_actual.'" min="1" required oninput="recalcularTotales()">
                                                </div>
                                            </td>

                                            <td class="has-text-centered has-text-weight-bold subtotal-txt" style="vertical-align: middle; font-size: 1.1em;">$'.number_format($subtotal_est, 2).'</td>
                                            
                                            <td class="has-text-centered" style="vertical-align: middle;">
                                                <button type="button" class="button is-danger is-small is-rounded" onclick="eliminarDelCarrito('.$id_prod.')"><i class="fas fa-trash-alt"></i></button>
                                            </td>
                                        </tr>';
                                    }
                                    echo '<tr class="has-background-light">
                                            <td colspan="4" class="has-text-right has-text-weight-bold is-size-5">TOTAL DE LA ORDEN:</td>
                                            <td class="has-text-centered has-text-weight-bold is-size-4 has-text-link" id="total-orden-txt">$'.number_format($total_estimado_orden, 2).'</td>
                                            <td></td>
                                          </tr>
                                          <tr class="has-background-success-light">
                                            <td colspan="4" class="has-text-right has-text-weight-bold">EQUIVALENTE A PAGAR:</td>
                                            <td class="has-text-centered has-text-weight-bold has-text-success-dark" id="total-orden-bs">Bs 0.00</td>
                                            <td></td>
                                          </tr>';
                                }else{
                                    echo '<tr class="has-text-centered"><td colspan="6">Aún no has agregado productos a la orden</td></tr>';
                                }
                            ?>
                        </tbody>
                    </table>

                    <?php if(isset($_SESSION['datos_compra']) && count($_SESSION['datos_compra'])>=1){ ?>
                    <hr>
                    <div class="columns is-centered">
                        <div class="column is-4">
                            <div class="field">
                                <label class="label"><i class="fas fa-money-bill-wave"></i> Anticipo a Proveedor ($)</label>
                                <div class="control">
                                    <input class="input" type="number" step="0.01" name="compra_pago_inicial" id="compra_pago_inicial" placeholder="0.00" min="0" max="<?php echo $total_estimado_orden; ?>">
                                </div>
                                <p class="help is-info">Adelanto opcional al aprobar cotización.</p>
                            </div>
                        </div>
                        <div class="column is-6">
                            <div class="field">
                                <label class="label"><i class="fas fa-sticky-note"></i> Condiciones pactadas (Nota)</label>
                                <div class="control">
                                    <input class="input" type="text" name="compra_nota" placeholder="Ej: Precios respetados según cotización #0045">
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="has-text-centered mt-5">
                        <button type="submit" class="button is-success is-large is-rounded shadow-sm">
                            <i class="fas fa-file-signature"></i> &nbsp; GENERAR ORDEN DE COMPRA
                        </button>
                    </p>
                    <?php } ?>

                    <input type="hidden" name="compra_tasa_bcv" id="compra_tasa_bcv" value="0">
                </div>
            </form>
            
            <?php if(isset($_SESSION['datos_compra']) && count($_SESSION['datos_compra'])>=1){ ?>
            <div class="has-text-centered mt-2">
                <button type="button" class="button is-danger is-outlined is-small" onclick="vaciarCarritoCompleto()">
                    Vaciar Orden
                </button>
            </div>
            <?php } ?>
        </div>
    </div>

    <script>
        // 1. FUNCIONALIDAD DE CATEGORÍAS (ACORDEÓN)
        function toggleAcordeon(boton) {
            let contenido = boton.nextElementSibling;
            let icono = boton.querySelector('.acordeon-icono');
            if (contenido.style.display === 'none' || contenido.style.display === '') {
                contenido.style.display = 'block';
                if (icono) { icono.style.transform = 'rotate(180deg)'; icono.style.transition = 'transform 0.3s ease'; }
            } else {
                contenido.style.display = 'none';
                if (icono) { icono.style.transform = 'rotate(0deg)'; icono.style.transition = 'transform 0.3s ease'; }
            }
        }

        const resultadoBusqueda = document.getElementById('resultados_busqueda');

        // 2. ELIMINAR Y VACIAR CARRITO
        function eliminarDelCarrito(id){
            Swal.fire({
                title: '¿Quitar este producto?', text: "Se eliminará de la orden", icon: 'question', showCancelButton: true, confirmButtonText: 'Sí, eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    let datos = new FormData(); datos.append('modulo_compra', 'eliminar_producto_carrito'); datos.append('producto_id', id);
                    fetch('<?php echo APP_URL; ?>app/ajax/compraAjax.php', { method: 'POST', body: datos }).then(res => res.json()).then(res => alertas_ajax(res));
                }
            });
        }

        function vaciarCarritoCompleto(){
            Swal.fire({
                title: '¿Estás seguro?', text: "Se vaciará la orden", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Sí, vaciar'
            }).then((result) => {
                if (result.isConfirmed) {
                    let datos = new FormData(); datos.append('modulo_compra', 'vaciar');
                    fetch('<?php echo APP_URL; ?>app/ajax/compraAjax.php', { method: 'POST', body: datos }).then(res => res.json()).then(res => alertas_ajax(res));
                }
            });
        }

        // 3. BUSCADORES
        function cargar_por_categoria_compra(id){
            let datos = new FormData(); datos.append('categoria_id', id); datos.append('modulo_compra', 'buscar_por_categoria');
            fetch('<?php echo APP_URL; ?>app/ajax/compraAjax.php',{ method: 'POST', body: datos }).then(res => res.text()).then(res => { resultadoBusqueda.innerHTML = res; reactivarFormularios(); });
        }

        document.getElementById('form-buscar-compra').addEventListener('submit', function(e){
            e.preventDefault();
            let datos = new FormData(this); datos.append('modulo_compra', 'buscar_producto');
            fetch('<?php echo APP_URL; ?>app/ajax/compraAjax.php', { method: 'POST', body: datos }).then(res => res.text()).then(res => { resultadoBusqueda.innerHTML = res; reactivarFormularios(); });
        });

        function reactivarFormularios(){
            resultadoBusqueda.querySelectorAll(".FormularioAjax").forEach(form => {
                form.addEventListener("submit", function(e){
                    e.preventDefault(); let data = new FormData(this);
                    fetch(this.getAttribute("action"), { method: this.getAttribute("method"), body: data }).then(res => res.json()).then(res => alertas_ajax(res));
                });
            });
        }

        // ==========================================
        // 4. LÓGICA: CÁLCULOS Y VALIDACIÓN EN VIVO 
        // ==========================================
        let tasaBcvGlobal = parseFloat(localStorage.getItem('tasa_bcv')) || 0;
        
        document.addEventListener("DOMContentLoaded", () => {
            if(document.getElementById('display_tasa_bcv')){
                document.getElementById('display_tasa_bcv').innerText = tasaBcvGlobal.toFixed(2);
            }
            if(document.getElementById('compra_tasa_bcv')){
                document.getElementById('compra_tasa_bcv').value = tasaBcvGlobal;
            }
            recalcularTotales();
        });

        function recalcularTotales() {
            let total = 0;
            document.querySelectorAll('tr.fila-producto').forEach(fila => {
                let inputPrecio = fila.querySelector('.input-precio');
                let inputCantidad = fila.querySelector('.input-cantidad');
                let minCosto = parseFloat(inputPrecio.getAttribute('data-min')) || 0;
                
                let precio = parseFloat(inputPrecio.value) || 0;
                let cantidad = parseInt(inputCantidad.value) || 0;

                // Validación visual: Si es menor al costo anterior, se pone ROJO.
                if (precio < minCosto) {
                    inputPrecio.classList.add('is-danger');
                    inputPrecio.title = "El precio no puede ser menor al costo anterior ($" + minCosto + ")";
                } else {
                    inputPrecio.classList.remove('is-danger');
                    inputPrecio.title = "";
                }

                let subtotal = precio * cantidad;
                fila.querySelector('.subtotal-txt').innerText = '$' + subtotal.toFixed(2);
                total += subtotal;
            });

            // Actualizar Totales y límites de anticipo
            if(document.getElementById('total-orden-txt')) {
                document.getElementById('total-orden-txt').innerText = '$' + total.toFixed(2);
                document.getElementById('total-orden-bs').innerText = 'Bs ' + (total * tasaBcvGlobal).toFixed(2);
                
                let inputAnticipo = document.getElementById('compra_pago_inicial');
                if(inputAnticipo) { inputAnticipo.max = total; }
            }
        }

        // Envío del formulario interceptado
        let formPurchase = document.getElementById('form-generar-orden');
        if(formPurchase){
            formPurchase.addEventListener('submit', function(e){
                e.preventDefault();
                
                // Validación final en caliente antes de enviar al servidor
                let hayErrores = false;
                document.querySelectorAll('.input-precio').forEach(input => {
                    if (input.classList.contains('is-danger')) { hayErrores = true; }
                });

                if (hayErrores) {
                    Swal.fire({ title: "Precios Inválidos", text: "Hay productos con precios menores a la compra anterior. Por favor corríjalos (casillas en rojo).", icon: "error" });
                    return;
                }

                let datos = new FormData(this);
                fetch(this.getAttribute("action"), { method: this.getAttribute("method"), body: datos })
                .then(respuesta => respuesta.json())
                .then(respuesta => {
                    if(respuesta.tipo == "confirmar"){
                        Swal.fire({
                            title: respuesta.titulo, text: respuesta.texto, icon: respuesta.icono, showCancelButton: true, confirmButtonText: respuesta.confirmButtonText, cancelButtonText: respuesta.cancelButtonText
                        }).then((result) => {
                            if (result.isConfirmed) { window.open(respuesta.url, '_blank'); location.reload(); } else { location.reload(); }
                        });
                    } else { return alertas_ajax(respuesta); }
                });
            });
        }
    </script>