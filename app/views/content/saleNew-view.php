<div class="container is-fluid mb-6">
    <h1 class="title">Ventas</h1>
    <h2 class="subtitle"><i class="fas fa-cart-plus fa-fw"></i> &nbsp; Nueva venta</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
    $check_empresa = $insLogin->seleccionarDatos("Normal", "empresa LIMIT 1", "*", 0);

    if ($check_empresa->rowCount() == 1) {
        $check_empresa = $check_empresa->fetch();
    ?>
        <div class="columns">

            <div class="column pb-6">

                <p class="has-text-centered pt-6 pb-6">
                    <small>Para agregar productos debe de digitar el código de barras en el campo "Código de producto" y luego presionar &nbsp; <strong class="is-uppercase"><i class="far fa-check-circle"></i> &nbsp; Agregar producto</strong>. También puede agregar el producto mediante la opción &nbsp; <strong class="is-uppercase"><i class="fas fa-search"></i> &nbsp; Buscar producto</strong>.</small>
                </p>
                <form class="pt-6 pb-6" id="sale-barcode-form" autocomplete="off">
                    <div class="columns">
                        <div class="column is-one-quarter">
                            <button type="button" class="button is-link is-light js-modal-trigger" data-target="modal-js-product"><i class="fas fa-search"></i> &nbsp; Buscar producto</button>
                        </div>
                        <div class="column">
                            <div class="field is-grouped">
                                <p class="control is-expanded">
                                    <input class="input" type="text" pattern="[a-zA-Z0-9- ]{1,70}" maxlength="70" autofocus="autofocus" placeholder="Ingrese el Código de barras" id="sale-barcode-input">
                                </p>
                                <a class="control">
                                    <button type="submit" class="button is-info">
                                        <i class="far fa-check-circle"></i> &nbsp; Agregar producto
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
                <?php
                if (isset($_SESSION['alerta_producto_agregado']) && $_SESSION['alerta_producto_agregado'] != "") {
                    echo '
                    <div class="notification is-success is-light">
                      ' . $_SESSION['alerta_producto_agregado'] . '
                    </div>
                    ';
                    unset($_SESSION['alerta_producto_agregado']);
                }
                ?>
                <div class="table-container">
                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                        <thead>
                            <tr class="has-background-link-light">
                                <th class="has-text-centered">#</th>
                                <th class="has-text-centered">Código</th>
                                <th class="has-text-centered">Producto</th>
                                <th class="has-text-centered">Cant.</th>
                                <th class="has-text-centered">Precio Unit.</th>
                                <th class="has-text-centered">Subtotal</th>
                                <th class="has-text-centered">Actualizar</th>
                                <th class="has-text-centered">Remover</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($_SESSION['datos_producto_venta']) && count($_SESSION['datos_producto_venta']) >= 1) {

                                $_SESSION['venta_total'] = 0;
                                $cc = 1;

                                foreach ($_SESSION['datos_producto_venta'] as $productos) {
                            ?>
                                    <tr class="has-text-centered">
                                        <td><?php echo $cc; ?></td>
                                        <td><?php echo $productos['producto_codigo']; ?></td>
                                        <td><?php echo $productos['venta_detalle_descripcion']; ?></td>
                                        <td>
                                            <div class="control">
                                                <input class="input sale_input-cant has-text-centered" value="<?php echo $productos['venta_detalle_cantidad']; ?>" id="sale_input_<?php echo str_replace(" ", "_", $productos['producto_codigo']); ?>" type="text" style="max-width: 80px; margin: 0 auto;">
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?php echo MONEDA_SIMBOLO . number_format($productos['venta_detalle_precio_venta'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?></strong><br>
                                            <span class="is-size-7 has-text-grey precio-bcv-cart" data-usd="<?php echo $productos['venta_detalle_precio_venta']; ?>">Calculando Bs...</span>
                                        </td>
                                        <td>
                                            <strong><?php echo MONEDA_SIMBOLO . number_format($productos['venta_detalle_total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?></strong><br>
                                            <span class="is-size-7 has-text-link has-text-weight-bold precio-bcv-cart" data-usd="<?php echo $productos['venta_detalle_total']; ?>">Calculando Bs...</span>
                                        </td>
                                        <td>
                                            <button type="button" class="button is-success is-rounded is-small mt-2" onclick="actualizar_cantidad('#sale_input_<?php echo str_replace(" ", "_", $productos['producto_codigo']); ?>','<?php echo $productos['producto_codigo']; ?>')">
                                                <i class="fas fa-redo-alt fa-fw"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <form class="FormularioAjax mt-2" action="<?php echo APP_URL; ?>app/ajax/ventaAjax.php" method="POST" autocomplete="off">
                                                <input type="hidden" name="producto_codigo" value="<?php echo $productos['producto_codigo']; ?>">
                                                <input type="hidden" name="modulo_venta" value="remover_producto">
                                                <button type="submit" class="button is-danger is-rounded is-small" title="Remover producto">
                                                    <i class="fas fa-trash-restore fa-fw"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php
                                    $cc++;
                                    $_SESSION['venta_total'] += $productos['venta_detalle_total'];
                                }
                                ?>
                                <tr class="has-text-centered">
                                    <td colspan="4"></td>
                                    <td class="has-text-weight-bold">
                                        TOTAL GENERAL
                                    </td>
                                    <td class="has-text-weight-bold is-size-5">
                                        <?php echo MONEDA_SIMBOLO . number_format($_SESSION['venta_total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR) . " " . MONEDA_NOMBRE; ?>
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            <?php
                            } else {
                                $_SESSION['venta_total'] = 0;
                            ?>
                                <tr class="has-text-centered">
                                    <td colspan="8">
                                        No hay productos agregados
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="column is-one-quarter">
                <h2 class="title has-text-centered">Datos de la venta</h2>
                <hr>

                <?php if ($_SESSION['venta_total'] > 0) { ?>
                    <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/ventaAjax.php" method="POST" autocomplete="off" name="formsale">
                        <input type="hidden" name="modulo_venta" value="registrar_venta">
                    <?php } else { ?>
                        <form name="formsale">
                        <?php } ?>

                        <div class="control mb-5">
                            <label>Fecha</label>
                            <input class="input" type="date" value="<?php echo date("Y-m-d"); ?>" readonly>
                        </div>

                        <input type="hidden" name="venta_caja" value="1">

                        <label>Cliente</label>
                        <?php
                        if (isset($_SESSION['datos_cliente_venta']) && count($_SESSION['datos_cliente_venta']) >= 1 && $_SESSION['datos_cliente_venta']['cliente_id'] != 1) {
                        ?>
                            <div class="field has-addons mb-5">
                                <div class="control">
                                    <input class="input" type="text" readonly id="venta_cliente" value="<?php echo $_SESSION['datos_cliente_venta']['cliente_nombre'] . " " . $_SESSION['datos_cliente_venta']['cliente_apellido']; ?>">
                                </div>
                                <div class="control">
                                    <a class="button is-danger" title="Remove cliente" id="btn_remove_client" onclick="remover_cliente(<?php echo $_SESSION['datos_cliente_venta']['cliente_id']; ?>)">
                                        <i class="fas fa-user-times fa-fw"></i>
                                    </a>
                                </div>
                            </div>
                        <?php
                        } else {
                            $datos_cliente = $insLogin->seleccionarDatos("Normal", "cliente WHERE cliente_id='1'", "*", 0);
                            if ($datos_cliente->rowCount() == 1) {
                                $datos_cliente = $datos_cliente->fetch();

                                $_SESSION['datos_cliente_venta'] = [
                                    "cliente_id" => $datos_cliente['cliente_id'],
                                    "cliente_tipo_documento" => $datos_cliente['cliente_tipo_documento'],
                                    "cliente_numero_documento" => $datos_cliente['cliente_numero_documento'],
                                    "cliente_nombre" => $datos_cliente['cliente_nombre'],
                                    "cliente_apellido" => $datos_cliente['cliente_apellido']
                                ];
                            } else {
                                $_SESSION['datos_cliente_venta'] = [
                                    "cliente_id" => 1,
                                    "cliente_tipo_documento" => "N/A",
                                    "cliente_numero_documento" => "N/A",
                                    "cliente_nombre" => "Publico",
                                    "cliente_apellido" => "General"
                                ];
                            }
                        ?>
                            <div class="field has-addons mb-5">
                                <div class="control">
                                    <input class="input" type="text" readonly id="venta_cliente" value="<?php echo $_SESSION['datos_cliente_venta']['cliente_nombre'] . " " . $_SESSION['datos_cliente_venta']['cliente_apellido']; ?>">
                                </div>
                                <div class="control">
                                    <a class="button is-info js-modal-trigger" data-target="modal-js-client" title="Agregar cliente" id="btn_add_client">
                                        <i class="fas fa-user-plus fa-fw"></i>
                                    </a>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="columns">
                            <div class="column is-half">
                                <div class="control mb-5">
                                    <label>Método de Pago <?php echo CAMPO_OBLIGATORIO; ?></label>
                                    <div class="select is-fullwidth">
                                        <select name="venta_metodo_pago" id="venta_metodo_pago" required>
                                            <option value="" selected>Seleccione una opción</option>
                                            <option value="Pago Movil">Pago Móvil</option>
                                            <option value="Transferencia">Transferencia</option>
                                            <option value="Efectivo">Efectivo</option>
                                            <option value="Divisas">Divisas ($ / €)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-half">
                                <div class="control mb-5">
                                    <label>Referencia (Últ. 6 dígitos) <span id="req_referencia" style="color:red; display:none;">*</span></label>
                                    <input class="input" type="text" name="venta_referencia" id="venta_referencia" pattern="[0-9]{6}" maxlength="6" minlength="6" placeholder="Ej: 123456" disabled>
                                </div>
                            </div>
                        </div>

                        <script>
                            /* Lógica para bloquear/desbloquear la referencia según el método de pago */
                            document.getElementById('venta_metodo_pago').addEventListener('change', function() {
                                let metodo = this.value;
                                let refInput = document.getElementById('venta_referencia');
                                let reqSpan = document.getElementById('req_referencia');

                                // Si es pago electrónico, exigimos la referencia
                                if (metodo === 'Pago Movil' || metodo === 'Transferencia') {
                                    refInput.disabled = false;
                                    refInput.required = true;
                                    reqSpan.style.display = 'inline'; // Muestra el asterisco rojo
                                } else {
                                    // Si es efectivo, divisa o vacío, la bloqueamos y borramos
                                    refInput.disabled = true;
                                    refInput.required = false;
                                    refInput.value = '';
                                    reqSpan.style.display = 'none';
                                }
                            });
                        </script>

                        <div class="control mb-5">
                            <label>Monto Pagado / Confirmado (En <?php echo MONEDA_NOMBRE; ?>) <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input class="input has-text-weight-bold" type="text" name="venta_abono" id="venta_abono" value="<?php echo number_format($_SESSION['venta_total'], MONEDA_DECIMALES, '.', ''); ?>" pattern="[0-9.]{1,25}" maxlength="25" readonly>
                            <p class="help is-info">Monto exacto de la operación</p>
                        </div>

                        <div class="box mt-6 p-5" style="border-top: 5px solid #004595; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">

                            <p class="has-text-centered has-text-weight-bold has-text-grey-dark mb-4 is-size-5">
                                <i class="fas fa-dollar-sign"></i> TOTAL: <?php echo number_format($_SESSION['venta_total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR); ?>
                            </p>

                            <hr>

                            <p class="title is-3 has-text-centered has-text-link mt-4" id="total_bs_label">
                                Calculando Bs...
                            </p>

                        </div>

                        <?php if ($_SESSION['venta_total'] > 0) { ?>
                            <p class="has-text-centered">
                                <button type="submit" class="button is-info is-rounded is-medium mt-4"><i class="far fa-save"></i> &nbsp; Confirmar y Guardar</button>
                            </p>
                        <?php } ?>
                        <p class="has-text-centered pt-6">
                            <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
                        </p>
                        <input type="hidden" value="<?php echo number_format($_SESSION['venta_total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, ""); ?>" id="venta_total_hidden">
                        <input type="hidden" name="venta_tasa_bcv" id="venta_tasa_bcv" value="0">
                        </form>
            </div>

        </div>
    <?php } else { ?>
        <article class="message is-warning">
            <div class="message-header">
                <p>¡Ocurrio un error inesperado!</p>
            </div>
            <div class="message-body has-text-centered"><i class="fas fa-exclamation-triangle fa-2x"></i><br>No hemos podio seleccionar algunos datos sobre la empresa, por favor <a href="<?php echo APP_URL; ?>companyNew/">verifique aquí los datos de la empresa</div>
        </article>
    <?php } ?>
</div>

<div class="modal" id="modal-js-product">
    <div class="modal-background"></div>
    <div class="modal-card" style="width:90%; max-width:1000px;">
        <header class="modal-card-head">
            <p class="modal-card-title is-uppercase"><i class="fas fa-search"></i> &nbsp; Buscar producto por categoría</p>
            <button class="delete" aria-label="close"></button>
        </header>
        <section class="modal-card-body">
            <?php

            use app\controllers\productController;

            $insProductoModal = new productController();
            ?>
            <div class="columns">
                <div class="column is-one-third">
                    <h3 class="title is-6 has-text-centered">Categorías</h3>
                    <div class="categories-navigation" style="max-height: 400px; overflow-y: auto;">
                        <?php
                        // 1. Obtenemos todas las categorías
                        $datos_cat = $insProductoModal->seleccionarDatos("Normal", "categoria", "*", 0);
                        if ($datos_cat->rowCount() > 0) {
                            $todas = $datos_cat->fetchAll();

                            // 2. Primero dibujamos los PADRES (Categorías principales)
                            foreach ($todas as $p) {
                                if (empty($p['categoria_padre_id']) || $p['categoria_padre_id'] == "0") {

                                    // Botón Padre: Al hacer clic abre/cierra sus hijos (No filtra productos directamente)
                                    echo '<button type="button" class="button is-link is-light is-fullwidth mb-1 btn-padre-modal" style="justify-content: flex-start; font-weight: bold;">
                                <i class="fas fa-folder mr-2"></i> ' . $p['categoria_nombre'] . '
                              </button>';

                                    // Contenedor de subcategorías (oculto por defecto)
                                    echo '<div class="sub-modal-container" style="display: none; padding-left: 15px; margin-bottom: 10px;">';

                                    // 3. Buscamos los HIJOS de este padre
                                    foreach ($todas as $h) {
                                        if ($h['categoria_padre_id'] == $p['categoria_id']) {
                                            // Botón Hijo: Este SÍ ejecuta la función cargar_por_categoria
                                            echo '<button type="button" class="button is-fullwidth is-small mb-1" onclick="cargar_por_categoria(' . $h['categoria_id'] . ')" style="justify-content: flex-start;">
                                        <i class="fas fa-arrow-right mr-2"></i> ' . $h['categoria_nombre'] . '
                                      </button>';
                                        }
                                    }
                                    echo '</div>';
                                }
                            }
                        } else {
                            echo '<p class="has-text-centered">No hay categorías</p>';
                        }
                        ?>
                    </div>
                </div>

                <script>
                    /* Lógica para el acordeón dentro del modal */
                    document.querySelectorAll('.btn-padre-modal').forEach(btn => {
                        btn.addEventListener('click', function() {
                            let contenedor = this.nextElementSibling;
                            // Alternar visibilidad
                            if (contenedor.style.display === "none") {
                                contenedor.style.display = "block";
                                this.classList.add('is-active');
                            } else {
                                contenedor.style.display = "none";
                                this.classList.remove('is-active');
                            }
                        });
                    });
                </script>
                <div class="column">
                    <div class="field">
                        <label class="label">Filtros de búsqueda avanzada</label>
                        <input type="hidden" id="id_categoria_variable" value="">
                        <div class="columns is-multiline">

                            <div class="column is-6">
                                <div class="field">
                                    <div class="control has-icons-left">
                                        <div class="select is-fullwidth">
                                            <select id="filtro_marca" onchange="buscar_codigo()">
                                                <option value="">Todas las Marcas</option>
                                                <?php
                                                $marcas = $insProductoModal->seleccionarDatos("Normal", "producto GROUP BY producto_marca", "producto_marca", 0);
                                                while ($m = $marcas->fetch()) {
                                                    if ($m['producto_marca'] != "") echo '<option value="' . $m['producto_marca'] . '">' . $m['producto_marca'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="icon is-small is-left"><i class="fas fa-tag"></i></div>
                                    </div>
                                </div>
                            </div>

                            <div class="column is-6">
                                <div class="field">
                                    <div class="control has-icons-left">
                                        <div class="select is-fullwidth">
                                            <select id="filtro_modelo" onchange="buscar_codigo()">
                                                <option value="">Todos los Modelos</option>
                                                <?php
                                                $modelos = $insProductoModal->seleccionarDatos("Normal", "producto GROUP BY producto_modelo", "producto_modelo", 0);
                                                while ($mo = $modelos->fetch()) {
                                                    if ($mo['producto_modelo'] != "") echo '<option value="' . $mo['producto_modelo'] . '">' . $mo['producto_modelo'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="icon is-small is-left"><i class="fas fa-microchip"></i></div>
                                    </div>
                                </div>
                            </div>

                            <div class="column is-12">
                                <div class="field has-addons">
                                    <div class="control is-expanded">
                                        <input class="input" type="text" id="input_codigo" placeholder="Escribe nombre, marca o modelo..." autocomplete="off">
                                    </div>
                                    <div class="control">
                                        <button type="button" class="button is-link" onclick="buscar_codigo()">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div id="tabla_productos" class="table-container mt-4">
                    </div>
                </div>
            </div>
            <script>
                function cargar_por_categoria(id) {
                    let filtroMarca = document.querySelector('#filtro_marca');
                    let filtroModelo = document.querySelector('#filtro_modelo');
                    let inputBusqueda = document.querySelector('#input_codigo');
                    let categoriaVariable = document.querySelector('#id_categoria_variable');
                    const urlAjax = '<?php echo APP_URL; ?>app/ajax/ventaAjax.php';

                    // 1. Limpieza de campos
                    if (inputBusqueda) inputBusqueda.value = "";
                    if (categoriaVariable) categoriaVariable.value = id;

                    // --- PARTE A: Cargar Marcas y Modelos para los Selects ---
                    let datosFiltros = new FormData();
                    datosFiltros.append("modulo_venta", "obtener_filtros_categoria");
                    datosFiltros.append("categoria_id", id);

                    fetch(urlAjax, { method: 'POST', body: datosFiltros })
                    .then(res => res.json())
                    .then(data => {
                        if (filtroMarca) {
                            filtroMarca.innerHTML = '<option value="">Todas las Marcas</option>';
                            data.marcas.forEach(m => {
                                if(m) filtroMarca.innerHTML += `<option value="${m}">${m}</option>`;
                            });
                        }
                        if (filtroModelo) {
                            filtroModelo.innerHTML = '<option value="">Todos los Modelos</option>';
                            data.modelos.forEach(mo => {
                                if(mo) filtroModelo.innerHTML += `<option value="${mo}">${mo}</option>`;
                            });
                        }
                    })
                    .catch(error => console.error('Error al cargar filtros:', error));

                    // --- PARTE B: Cargar la tabla de productos (Tu lógica original) ---
                    let datosTabla = new FormData();
                    datosTabla.append("categoria_id", id);
                    datosTabla.append("modulo_venta", "buscar_por_categoria");

                    fetch(urlAjax, { method: 'POST', body: datosTabla })
                    .then(respuesta => respuesta.text())
                    .then(respuesta => {
                        let tabla_productos = document.querySelector('#tabla_productos');
                        if (tabla_productos) {
                            tabla_productos.innerHTML = respuesta;
                        }
                    });
                }

                // Búsqueda en vivo: debounce
                (function() {
                    let timer = null;
                    let input = document.querySelector('#input_codigo');
                    if (input) {
                        input.addEventListener('keyup', function(e) {
                            clearTimeout(timer);
                            timer = setTimeout(function() {
                                if (input.value.trim().length > 0) {
                                    buscar_codigo();
                                } else {
                                    document.querySelector('#tabla_productos').innerHTML = '';
                                }
                            }, 300);
                        });
                    }
                })();
            </script>
        </section>
    </div>
</div>

<div class="modal" id="modal-js-client">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title is-uppercase"><i class="fas fa-search"></i> &nbsp; Buscar y agregar cliente</p>
            <button class="delete" aria-label="close"></button>
        </header>
        <section class="modal-card-body">
            <div class="field mt-6 mb-6">
                <label class="label">Documento, Nombre y Apellido</label>
                <div class="control">
                    <input class="input" type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" name="input_cliente" id="input_cliente" maxlength="30">
                </div>
            </div>
            <div class="container" id="tabla_clientes"></div>
            <p class="has-text-centered">
                <button type="button" class="button is-link is-light" onclick="buscar_cliente()"><i class="fas fa-search"></i> &nbsp; Buscar</button>
            </p>
        </section>
    </div>
</div>

<script>
    /* Calcular Totales en Bolívares al cargar la página */
    document.addEventListener('DOMContentLoaded', function() {
        let tasa_bcv = parseFloat(localStorage.getItem('tasa_bcv')) || 0;

        // Calcular los Bolívares en la tabla de productos (subtotales)
        let elementos = document.querySelectorAll('.precio-bcv-cart');
        elementos.forEach(function(el) {
            let usd = parseFloat(el.getAttribute('data-usd')) || 0;
            if (tasa_bcv > 0) {
                let formatBs = new Intl.NumberFormat('es-VE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(usd * tasa_bcv);
                el.innerHTML = `Bs. ${formatBs}`;
            } else {
                el.innerHTML = `<span class="has-text-danger">Sin BCV</span>`;
            }
        });

        // Calcular Total General de la Factura
        let total_dolares = document.querySelector('#venta_total_hidden');
        if (total_dolares) {
            let total_num = parseFloat(total_dolares.value) || 0;

            let input_tasa = document.querySelector('#venta_tasa_bcv');
            if (input_tasa) {
                input_tasa.value = tasa_bcv;
            }

            if (tasa_bcv > 0 && total_num > 0) {
                let total_bs = total_num * tasa_bcv;
                let formato_bs = new Intl.NumberFormat('es-VE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(total_bs);
                document.querySelector('#total_bs_label').innerHTML = `Bs. ${formato_bs}`;
            } else if (total_num === 0) {
                document.querySelector('#total_bs_label').innerHTML = `Bs. 0.00`;
            } else {
                document.querySelector('#total_bs_label').innerHTML = `<small class="has-text-danger is-size-6">Sin conexión BCV</small>`;
            }
        }
    });

    /* Asegurarnos de capturar la tasa exacta justo antes de guardar la venta */
    let form_sale_action = document.querySelector("form[name='formsale']");
    if (form_sale_action) {
        form_sale_action.addEventListener('submit', function(e) {
            let input_tasa = document.querySelector('#venta_tasa_bcv');
            let tasa_bcv = parseFloat(localStorage.getItem('tasa_bcv')) || 0;
            if (input_tasa) {
                input_tasa.value = tasa_bcv;
            }
        });
    }

    /* Detectar cuando se envia el formulario para agregar producto */
    let sale_form_barcode = document.querySelector("#sale-barcode-form");
    sale_form_barcode.addEventListener('submit', function(event) {
        event.preventDefault();
        setTimeout('agregar_producto()', 100);
    });

    /* Detectar cuando escanea un codigo en formulario para agregar producto */
    let sale_input_barcode = document.querySelector("#sale-barcode-input");
    sale_input_barcode.addEventListener('paste', function() {
        setTimeout('agregar_producto()', 100);
    });

    /* Agregar producto */
    function agregar_producto() {
        let codigo_producto = document.querySelector('#sale-barcode-input').value;

        codigo_producto = codigo_producto.trim();

        if (codigo_producto != "") {
            let datos = new FormData();
            datos.append("producto_codigo", codigo_producto);
            datos.append("modulo_venta", "agregar_producto");

            fetch('<?php echo APP_URL; ?>app/ajax/ventaAjax.php', {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.json())
                .then(respuesta => {
                    return alertas_ajax(respuesta);
                });

        } else {
            Swal.fire({
                icon: 'error',
                title: 'Ocurrió un error inesperado',
                text: 'Debes de introducir el código del producto',
                confirmButtonText: 'Aceptar'
            });
        }
    }

    /*----------  Buscar codigo  ----------*/
    function buscar_codigo() {
        let input_codigo = document.querySelector('#input_codigo').value.trim();
        let categoria_id = document.querySelector('#id_categoria_variable').value;
        let marca = document.querySelector('#filtro_marca').value;
        let modelo = document.querySelector('#filtro_modelo').value;

        // Aunque el texto esté vacío, si hay marca o modelo seleccionados, permitimos la búsqueda
        if (input_codigo != "" || marca != "" || modelo != "") {

            let datos = new FormData();
            datos.append("buscar_codigo", input_codigo);
            datos.append("categoria_id", categoria_id); // Enviamos categoría seleccionada
            datos.append("filtro_marca", marca); // Enviamos marca
            datos.append("filtro_modelo", modelo); // Enviamos modelo
            datos.append("modulo_venta", "buscar_codigo");

            fetch('<?php echo APP_URL; ?>app/ajax/ventaAjax.php', {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.text())
                .then(respuesta => {
                    let tabla_productos = document.querySelector('#tabla_productos');
                    tabla_productos.innerHTML = respuesta;
                });

        } else {
            document.querySelector('#tabla_productos').innerHTML = "";
        }
    }

    /*----------  Agregar codigo  ----------*/
    function agregar_codigo($codigo) {
        document.querySelector('#sale-barcode-input').value = $codigo;
        setTimeout('agregar_producto()', 100);
    }

    /* Actualizar cantidad de producto */
    function actualizar_cantidad(id, codigo) {
        let cantidad = document.querySelector(id).value;

        cantidad = cantidad.trim();
        codigo.trim();

        if (cantidad > 0) {

            Swal.fire({
                title: '¿Estás seguro?',
                text: "Desea actualizar la cantidad de productos",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, actualizar',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {

                    let datos = new FormData();
                    datos.append("producto_codigo", codigo);
                    datos.append("producto_cantidad", cantidad);
                    datos.append("modulo_venta", "actualizar_producto");

                    fetch('<?php echo APP_URL; ?>app/ajax/ventaAjax.php', {
                            method: 'POST',
                            body: datos
                        })
                        .then(respuesta => respuesta.json())
                        .then(respuesta => {
                            return alertas_ajax(respuesta);
                        });
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Ocurrió un error inesperado',
                text: 'Debes de introducir una cantidad mayor a 0',
                confirmButtonText: 'Aceptar'
            });
        }
    }

    /*----------  Buscar cliente  ----------*/
    function buscar_cliente() {
        let input_cliente = document.querySelector('#input_cliente').value;

        input_cliente = input_cliente.trim();

        if (input_cliente != "") {

            let datos = new FormData();
            datos.append("buscar_cliente", input_cliente);
            datos.append("modulo_venta", "buscar_cliente");

            fetch('<?php echo APP_URL; ?>app/ajax/ventaAjax.php', {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.text())
                .then(respuesta => {
                    let tabla_clientes = document.querySelector('#tabla_clientes');
                    tabla_clientes.innerHTML = respuesta;
                });

        } else {
            let tabla_clientes = document.querySelector('#tabla_clientes');
            if(tabla_clientes) {
                tabla_clientes.innerHTML = "";
            }
        }
    }

    /* Búsqueda automática de cliente */
    document.addEventListener('DOMContentLoaded', function() {
        let timerCliente = null;
        let inputCliente = document.querySelector('#input_cliente');
        if (inputCliente) {
            inputCliente.addEventListener('keyup', function(e) {
                clearTimeout(timerCliente);
                timerCliente = setTimeout(function() {
                    buscar_cliente();
                }, 300);
            });
        }
    });

    /*----------  Agregar cliente  ----------*/
    function agregar_cliente(id) {

        Swal.fire({
            title: '¿Quieres agregar este cliente?',
            text: "Se va a agregar este cliente para realizar una venta",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, agregar',
            cancelButtonText: 'No, cancelar'
        }).then((result) => {
            if (result.isConfirmed) {

                let datos = new FormData();
                datos.append("cliente_id", id);
                datos.append("modulo_venta", "agregar_cliente");

                fetch('<?php echo APP_URL; ?>app/ajax/ventaAjax.php', {
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

    /*----------  Remover cliente  ----------*/
    function remover_cliente(id) {

        Swal.fire({
            title: '¿Quieres remover este cliente?',
            text: "Se va a quitar el cliente seleccionado de la venta",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, remover',
            cancelButtonText: 'No, cancelar'
        }).then((result) => {
            if (result.isConfirmed) {

                let datos = new FormData();
                datos.append("cliente_id", id);
                datos.append("modulo_venta", "remover_cliente");

                fetch('<?php echo APP_URL; ?>app/ajax/ventaAjax.php', {
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
</script>

<?php
include "./app/views/inc/print_invoice_script.php";

if (isset($_SESSION['venta_codigo_factura']) && $_SESSION['venta_codigo_factura'] != "") {
?>
    <script>
        Swal.fire({
            title: '¡Venta Registrada!',
            text: 'La venta se guardó con éxito en el sistema.',
            icon: 'success',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<i class="fas fa-file-pdf"></i> &nbsp; Generar Factura',
            cancelButtonText: 'Cerrar'
        }).then((result) => {
            if (result.isConfirmed) {
                let url_factura = '<?php echo APP_URL . "app/pdf/invoice.php?code=" . $_SESSION['venta_codigo_factura']; ?>';
                print_invoice(url_factura);
            }
        });
    </script>
<?php
    unset($_SESSION['venta_codigo_factura']);
}
?>