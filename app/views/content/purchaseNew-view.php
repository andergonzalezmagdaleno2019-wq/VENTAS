<?php include './app/views/content/logo.php'; ?>
<div class="container is-fluid mb-4">
    <h1 class="title">Compras</h1>
    <h2 class="subtitle">Generar Orden de Compra - <strong>FastNet</strong></h2>
</div>

<div class="container is-fluid pb-6">
    <div class="box mb-5">
        <div class="columns is-vcentered">
            <div class="column is-5">
                <label class="label"><i class="fas fa-truck"></i> Seleccione Proveedor</label>
                <div class="field">
                    <div class="control has-icons-left">
                        <div class="select is-fullwidth">
                            <?php
                                // 1. Verificamos si realmente hay productos en el carrito actualmente
                                $carrito_vacio = (!isset($_SESSION['datos_compra']) || empty($_SESSION['datos_compra']));
                                
                                // 2. Si el carrito NO está vacío, bloqueamos el select
                                $estado_select = (!$carrito_vacio) ? "disabled" : "";
                            ?>
                            
                            <select name="compra_proveedor" id="compra_proveedor" required onchange="actualizarEstadoProveedor()" <?php echo $estado_select; ?>>
                                <option value="">Seleccione una opción</option>
                                <?php
                                    $datos_proveedores = $insLogin->seleccionarDatos("Normal", "proveedor", "*", 0);
                                    
                                    // 1. Verificamos si realmente hay productos en el carrito
                                    $carrito_con_productos = (isset($_SESSION['datos_compra']) && !empty($_SESSION['datos_compra']));

                                    while ($campos_proveedor = $datos_proveedores->fetch()) {
                                        
                                        // 2. Solo marcamos "selected" si el carrito tiene productos Y coincide el ID
                                        $selected = "";
                                        if ($carrito_con_productos && isset($_SESSION['compra_proveedor_id'])) {
                                            if ($_SESSION['compra_proveedor_id'] == $campos_proveedor['proveedor_id']) {
                                                $selected = "selected";
                                            }
                                        }

                                        echo '<option value="' . $campos_proveedor['proveedor_id'] . '" ' . $selected . '>' 
                                            . $campos_proveedor['proveedor_nombre'] . ' (RIF: ' . $campos_proveedor['proveedor_rif'] . ')</option>';
                                    }
                                ?>
                            </select>

                            <?php 
                                // 3. El Hidden solo es necesario si el select está bloqueado
                                if(!$carrito_vacio && isset($_SESSION['compra_proveedor_id'])): 
                            ?>
                                <input type="hidden" name="compra_proveedor" value="<?php echo $_SESSION['compra_proveedor_id']; ?>">
                            <?php endif; ?>
                        </div>
                        <div class="icon is-small is-left"><i class="fas fa-handshake"></i></div>
                    </div>
                </div>
            </div>
            <div class="column has-text-right">
                <h3 class="title is-4 has-text-link mt-0"><i class="fas fa-clipboard-list"></i> Orden de Compra</h3>
                <p class="subtitle is-6 has-text-grey">Tasa BCV del día: <strong class="has-text-success">Bs <span id="display_tasa_bcv">0.00</span></strong></p>
            </div>
        </div>
    </div>

    <div class="columns">
        <div class="column is-one-third">
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title"><i class="fas fa-tags"></i> &nbsp; Categorías</p>
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
                                            echo '<button type="button" class="button is-fullwidth is-small is-outlined is-link mb-1" onclick="cargar_por_categoria_compra('.$h['categoria_id'].', \''.addslashes($h['categoria_nombre']).'\')">
                                                    <i class="fas fa-arrow-right"></i> &nbsp; '.$h['categoria_nombre'].'
                                                </button>';
                                        }
                                    }
                                    echo '</div></div>';
                                } else {
                                    echo '<button type="button" class="button is-fullwidth has-text-left p-2 mb-2" style="border: none; background-color: #f0f0f0; border-radius: 4px; cursor: pointer;" onclick="cargar_por_categoria_compra('.$p['categoria_id'].', \''.addslashes($p['categoria_nombre']).'\')">
                                            <span style="display: flex; align-items: center;">
                                                <i class="fas fa-folder" style="margin-right: 8px;"></i>
                                                <span style="flex-grow: 1; font-weight: bold;">'.mb_strtoupper($p['categoria_nombre'], 'UTF-8').'</span>
                                            </span>
                                        </button>';
                                }
                                }
                            }
                        } else { echo '<p class="has-text-centered">No hay categorías registradas</p>'; }
                    ?>
                </div>
            </div>
        </div>

        <div class="column">
            <div class="card">
                <header class="card-header" style="display: flex; align-items: center; justify-content: space-between; padding: 0 15px; min-height: 3.25rem;">
                    <p class="card-header-title mb-0" style="padding: 0;"><i class="fas fa-search"></i> &nbsp; Buscar producto</p>
                    <div class="field mb-0">
                        <div class="control has-icons-left">
                            <div class="select"> 
                                <select id="filtro_stock" onchange="aplicarFiltroStock()" style="max-width: 220px; border-radius: 6px;">
                                    <option value="todos" selected>📦 Todos los productos</option>
                                    <option value="bajo">📉 Stock Bajo (Crítico)</option>
                                    <option value="alto">📈 Stock Suficiente</option>
                                </select>
                            </div>
                            <div class="icon is-left"><i class="fas fa-filter"></i></div>
                        </div>
                    </div>
                </header>

                <div class="card-content">
                    <form action="" method="POST" autocomplete="off" id="form-buscar-compra">
                        <div class="field has-addons">
                            <div class="control is-expanded">
                                <input class="input" type="text" name="buscar_producto" id="buscar_producto" placeholder="Nombre o código" required>
                            </div>
                            <div class="control">
                                <button type="submit" class="button is-info"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                    <div id="resultados_busqueda" class="mt-4" style="max-height: 400px; overflow-y: auto;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="columns mt-4">
        <div class="column">
            <form action="<?php echo APP_URL; ?>app/ajax/compraAjax.php" class="FormularioEnvioManual" method="POST" autocomplete="off" id="form-generar-orden">
                <input type="hidden" name="modulo_compra" value="registrar">
                <input type="hidden" name="compra_proveedor" id="hidden_compra_proveedor" value="">
                <input type="hidden" name="compra_tasa_bcv" id="compra_tasa_bcv" value="">
                <div class="box">
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
                                        $costo_ref = (isset($detalle['costo_referencia'])) ? $detalle['costo_referencia'] : 0;
                                        $subtotal_est = $cant_actual * $costo_ref;
                                        $total_estimado_orden += $subtotal_est;

                                        echo '<tr class="fila-producto">
                                            <td style="vertical-align: middle;"><strong>'.$detalle['producto_nombre'].'</strong></td>
                                            <td class="has-text-centered has-text-grey">'.($costo_ref > 0 ? "$".number_format($costo_ref, 2) : '$0.00').'</td>
                                            <td><input class="input input-precio" type="number" step="0.01" name="detalle_precio['.$id_prod.']" value="'.$costo_ref.'" oninput="recalcularTotales()"></td>
                                            <td><input class="input input-cantidad has-text-centered" type="number" name="detalle_cantidad['.$id_prod.']" value="'.$cant_actual.'" min="1" oninput="recalcularTotales()"></td>
                                            <td class="has-text-centered has-text-weight-bold subtotal-txt">$'.number_format($subtotal_est, 2).'</td>
                                            <td class="has-text-centered">
                                                <button type="button" class="button is-danger is-small" onclick="eliminarDelCarrito('.$id_prod.')"><i class="fas fa-trash-alt"></i></button>
                                            </td>
                                        </tr>';
                                    }
                                    echo '<tr class="has-background-light">
                                            <td colspan="4" class="has-text-right has-text-weight-bold">TOTAL:</td>
                                            <td class="has-text-centered has-text-weight-bold has-text-link" id="total-orden-txt">$'.number_format($total_estimado_orden, 2).'</td>
                                            <td></td></tr>';
                                } else {
                                    echo '<tr class="has-text-centered"><td colspan="6">Aún no hay productos</td></tr>';
                                }
                            ?>
                        </tbody>
                    </table>

                    <?php if(isset($_SESSION['datos_compra']) && count($_SESSION['datos_compra'])>=1){ ?>
                    <div class="columns is-centered mt-4">
                        <div class="column is-4">
                            <label class="label">Anticipo ($)</label>
                            <input class="input" type="number" step="0.01" name="compra_pago_inicial" id="compra_pago_inicial" placeholder="0.00">
                        </div>
                        <div class="column is-6">
                            <label class="label">Condiciones (Nota)</label>
                            <input class="input" type="text" name="compra_nota" placeholder="Ej: Pago a 30 días">
                        </div>
                    </div>
                    <p class="has-text-centered mt-5">
                        <button type="submit" class="button is-success is-large is-rounded">GENERAR ORDEN</button>
                    </p>
                    <?php } ?>
                </div>
            </form>
        </div>
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
        let categoriaActual = "";

        function cargar_por_categoria_compra(id, nombre = "") {
            // 0. Validación de Proveedor: Obtenemos el ID del proveedor seleccionado
            let id_prov = document.getElementById('compra_proveedor').value;

            if (id_prov === "") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Proveedor requerido',
                    text: 'Por favor, seleccione un proveedor antes de buscar productos por categoría.',
                    confirmButtonColor: '#3085d6'
                });
                return; 
            }

            categoriaActual = id;

            // 1. Resaltado Visual
            document.querySelectorAll('.acordeon-contenido button, .card-content > button').forEach(btn => {
                btn.classList.remove('is-active-category');
            });

            if (event && event.currentTarget) {
                event.currentTarget.classList.add('is-active-category');
            }

            // 2. Reiniciar filtro de stock
            let filtroStock = document.getElementById('filtro_stock');
            if (filtroStock) filtroStock.value = "";

            // 3. Actualizar título
            let tituloVisible = document.getElementById('nombre_categoria_visible');
            if (tituloVisible && nombre !== "") {
                tituloVisible.innerText = nombre.toUpperCase();
            }

            // 4. Fetch AJAX incluyendo el proveedor_id
            let datos = new FormData();
            datos.append('categoria_id', id);
            datos.append('proveedor_id', id_prov); 
            datos.append('modulo_compra', 'buscar_por_categoria');

            fetch('<?php echo APP_URL; ?>app/ajax/compraAjax.php', {
                method: 'POST',
                body: datos
            })
            .then(res => res.text())
            .then(res => {
                if (resultadoBusqueda) {
                    resultadoBusqueda.innerHTML = res;
                    reactivarFormularios();
                }
            });
        }

        function aplicarFiltroStock() {
            let filtro = document.getElementById('filtro_stock');
            let criterio = filtro ? filtro.value : "";
            
            // CAPTURAMOS EL PROVEEDOR (independiente de si está bloqueado o no)
            let id_prov = document.getElementById('compra_proveedor').value;

            if (id_prov === "") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Proveedor requerido',
                    text: 'Seleccione un proveedor antes de filtrar por stock.'
                });
                return;
            }

            let datos = new FormData();
            datos.append('modulo_compra', 'filtrar_stock_categoria'); 
            datos.append('categoria_id', categoriaActual); 
            datos.append('criterio_stock', criterio);
            datos.append('proveedor_id', id_prov); // AGREGAMOS EL PROVEEDOR AL ENVÍO

            fetch('<?php echo APP_URL; ?>app/ajax/compraAjax.php', { 
                method: 'POST', 
                body: datos 
            })
            .then(res => res.text())
            .then(res => {
                if(resultadoBusqueda){
                    resultadoBusqueda.innerHTML = res;
                    reactivarFormularios();
                }
            });
        }

        document.getElementById('form-buscar-compra').addEventListener('submit', function(e){
            e.preventDefault();
            let datos = new FormData(this); datos.append('modulo_compra', 'buscar_producto');
            fetch('<?php echo APP_URL; ?>app/ajax/compraAjax.php', { method: 'POST', body: datos }).then(res => res.text()).then(res => { resultadoBusqueda.innerHTML = res; reactivarFormularios(); });
        });

        function reactivarFormularios(){
            resultadoBusqueda.querySelectorAll(".FormularioAjax").forEach(form => {
                form.addEventListener("submit", function(e){
                    e.preventDefault(); 
                    
                    // 1. Creamos los datos del formulario (producto_id, cantidad, costo)
                    let data = new FormData(this);
                    
                    // 2. CAPTURAMOS EL PROVEEDOR 
                    let id_prov = document.getElementById('compra_proveedor').value;
                    data.append("compra_proveedor", id_prov);

                    // 3. Enviamos todo al controlador
                    fetch(this.getAttribute("action"), { 
                        method: this.getAttribute("method"), 
                        body: data 
                    })
                    .then(res => res.json())
                    .then(res => alertas_ajax(res));
                });
            });
        }

        // ==========================================
        // 3.1 FILTRADO DINÁMICO POR PROVEEDOR
        // ==========================================
        const selectProveedor = document.getElementById('compra_proveedor');

        if(selectProveedor){
            selectProveedor.addEventListener('change', function() {
                let id_prov = this.value;

                // Si selecciona un proveedor
                if(id_prov !== "") {
                    let datos = new FormData();
                    datos.append("modulo_compra", "buscar_por_proveedor"); 
                    datos.append("proveedor_id", id_prov);

                    fetch("<?php echo APP_URL; ?>app/ajax/compraAjax.php", {
                        method: 'POST',
                        body: datos
                    })
                    .then(res => res.text())
                    .then(response => {
                        if(resultadoBusqueda){
                            // Inyectamos la tabla de productos en el contenedor principal
                            resultadoBusqueda.innerHTML = response;
                            
                            // IMPORTANTE: Reactivamos los eventos de los botones "Añadir"
                            // que acabamos de traer vía AJAX
                            reactivarFormularios(); 
                        }
                    });
                } else {
                    // Si vuelve a la opción por defecto, limpiamos o mostramos mensaje
                    if(resultadoBusqueda) {
                        resultadoBusqueda.innerHTML = '<div class="notification is-info is-light">Seleccione un proveedor para listar sus productos.</div>';
                    }
                }
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
            actualizarProveedorHidden();
        });

        // === NUEVA FUNCIÓN: Sincronizar el proveedor con el campo hidden del formulario ===
        function actualizarProveedorHidden() {
            let selectProveedor = document.getElementById('compra_proveedor');
            let hiddenProveedor = document.getElementById('hidden_compra_proveedor');
            
            if(selectProveedor && hiddenProveedor) {
                if(selectProveedor.disabled) {
                    // Si está disabled, buscar el hidden que ya existe fuera del formulario
                    let hiddenExterno = document.querySelector('input[name="compra_proveedor"]');
                    if(hiddenExterno && hiddenExterno.value) {
                        hiddenProveedor.value = hiddenExterno.value;
                    }
                } else {
                    hiddenProveedor.value = selectProveedor.value;
                }
            }
        }

        function recalcularTotales() {
            let total = 0;
            document.querySelectorAll('tr.fila-producto').forEach(fila => {
                let inputPrecio = fila.querySelector('.input-precio');
                let inputCantidad = fila.querySelector('.input-cantidad');
                let minCosto = parseFloat(inputPrecio.getAttribute('data-min')) || 0;
                
                let precio = parseFloat(inputPrecio.value) || 0;
                let cantidad = parseInt(inputCantidad.value) || 0;

                // Si es 0 o menor, ERROR CRÍTICO
                if (precio <= 0) {
                    inputPrecio.classList.add('is-danger');
                    inputPrecio.title = "El precio debe ser mayor a 0";
                } 
                // Si es menor al costo anterior
                else if (precio < minCosto) {
                    inputPrecio.classList.add('is-warning'); 
                    inputPrecio.title = "Aviso: Menor al costo anterior";
                } 
                else {
                    inputPrecio.classList.remove('is-danger', 'is-warning');
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
                
                // === NUEVO: Actualizar el hidden antes de enviar ===
                actualizarProveedorHidden();
                
                // 1. Reiniciamos los estados en cada intento
                let hayPrecioBajo = false;
                let hayMenorAlAnterior = false;

                // 2. Limpiamos clases de error previas para que no se queden marcadas si ya corregiste
                document.querySelectorAll('.input-precio').forEach(input => {
                    input.classList.remove('is-danger');
                });

                // 3. Validación final de los precios actuales en el carrito
                document.querySelectorAll('.input-precio').forEach(input => {
                    let valor = parseFloat(input.value) || 0;
                    let minCostoAnterior = parseFloat(input.getAttribute('data-min')) || 0;
                    
                    // Regla de los $1.00
                    if (valor < 1) { 
                        hayPrecioBajo = true;
                        input.classList.add('is-danger');
                    }

                    // Regla de Auditoría (Costo anterior)
                    if (valor < minCostoAnterior) {
                        hayMenorAlAnterior = true;
                        // Opcional: podrías usar 'is-warning' aquí si no quieres bloquear, 
                        // pero si quieres bloquear, usa 'is-danger'
                        input.classList.add('is-danger');
                    }
                });

                // 4. Mostramos alertas si hay errores
                if (hayPrecioBajo) {
                    Swal.fire({ 
                        title: "Precio no permitido", 
                        text: "El costo de los productos debe ser igual o mayor a $1.00 para procesar la compra.", 
                        icon: "error" 
                    });
                    return; // Detenemos el envío
                }

                // 2. Bloqueo por validación de costo anterior
                if (hayMenorAlAnterior) {
                    Swal.fire({ 
                        title: "Precios Inválidos", 
                        text: "Hay productos con precios menores a la compra anterior. Por favor corríjalos (casillas en rojo).", 
                        icon: "error" 
                    });
                    return;
                }

                // Si pasa las validaciones, procedemos con el envío

                // --- 1. PRIMERA ALERTA: ¿ESTÁS SEGURO? ---
                Swal.fire({
                    title: "¿Estás seguro?",
                    text: "¿Quieres realizar la acción solicitada?",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "Sí, realizar",
                    cancelButtonText: "No, cancelar"
                }).then((result) => {
                    
                    // SI EL USUARIO CONFIRMA, SE EJECUTA TODO LO DEMÁS
                    if (result.isConfirmed) {

                        // --- 2. PREPARACIÓN Y ENVÍO ---
                        let datos = new FormData(this);

                        fetch(this.getAttribute("action"), { 
                            method: this.getAttribute("method"), 
                            body: datos 
                        })
                        .then(res => res.json())
                        .then(res => {
                            
                            // Si el servidor responde con éxito
                            if (res.status == "orden_ok") {
                                
                                // --- 3. SEGUNDA ALERTA: ÉXITO (Confirmación de Registro) ---
                                Swal.fire({
                                    title: res.mensaje_confirmacion,
                                    text: "La orden se registró con éxito en el sistema.",
                                    icon: "success",
                                    confirmButtonText: 'Aceptar'
                                }).then((confirmExit) => {
                                    
                                    // --- 4. TERCERA ALERTA: ¿DESEA IMPRIMIR? ---
                                    // Solo aparece después de que el usuario acepta la de éxito
                                    if (confirmExit.isConfirmed || confirmExit.isDismissed) {
                                        Swal.fire({
                                            title: "¿Desea imprimir la Orden de Compra?",
                                            text: "Se abrirá el reporte en una nueva pestaña",
                                            icon: "question",
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: "Sí, imprimir",
                                            cancelButtonText: "No, después"
                                        }).then((printResult) => {
                                            if (printResult.isConfirmed) {
                                                // Abre el PDF en pestaña nueva
                                                window.open(res.url_pdf, '_blank');
                                            }
                                            // Recarga la página para limpiar el carrito/formulario
                                            location.reload();
                                        });
                                    }
                                }); 

                            } else {
                                // Si hay un error 
                                if (typeof alertas_ajax === 'function') {
                                    alertas_ajax(res);
                                }
                            }
                        })
                        .catch(error => {
                            console.error("Error en la petición:", error);
                        });
                    }
                }); // Cierre final de la alerta "¿Estás seguro?"
            });
        }

        function cargarTodosLosProductos() {
    // Rescatamos el ID del proveedor (funciona aunque esté disabled)
    let id_prov = document.getElementById('compra_proveedor').value;

    if(id_prov === "") {
        Swal.fire({ icon: 'warning', title: 'Proveedor requerido', text: 'Seleccione un proveedor primero.' });
        return;
    }

    // Limpiamos visualmente las categorías seleccionadas
    document.querySelectorAll('.is-active-category').forEach(btn => btn.classList.remove('is-active-category'));
    categoriaActual = ""; 

    let datos = new FormData();
    datos.append("modulo_compra", "buscar_por_proveedor"); 
    datos.append("proveedor_id", id_prov);

    fetch("<?php echo APP_URL; ?>app/ajax/compraAjax.php", {
        method: 'POST',
        body: datos
    })
    .then(res => res.text())
    .then(response => {
        if(resultadoBusqueda){
            resultadoBusqueda.innerHTML = response;
            reactivarFormularios(); 
        }
    });
}
    </script>