<?php include './app/views/content/logo.php'; ?>
<div class="container is-fluid mb-6">
    <h1 class="title">Ventas</h1>
    <h2 class="subtitle"><i class="fas fa-cart-plus fa-fw"></i> &nbsp; Nueva venta</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
    use app\controllers\saleController;
    $insVenta = new saleController();
    
    $check_empresa = $insVenta->seleccionarDatos("Normal", "empresa LIMIT 1", "*", 0);

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
                                    <input class="input" type="text" pattern="[a-zA-Z0-9- ]{1,70}" maxlength="70" autofocus="autofocus" placeholder="Ingrese el Código de barras" id="sale-barcode-input" required>
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
                    echo '<div class="notification is-success is-light">' . $_SESSION['alerta_producto_agregado'] . '</div>';
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
                                            <strong><?php echo MONEDA_SIMBOLO . number_format($productos['venta_detalle_precio_venta'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR); ?></strong><br>
                                            <span class="is-size-7 has-text-grey precio-bcv-cart" data-usd="<?php echo $productos['venta_detalle_precio_venta']; ?>">Calculando Bs...</span>
                                        </td>
                                        <td>
                                            <strong><?php echo MONEDA_SIMBOLO . number_format($productos['venta_detalle_total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR); ?></strong><br>
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
                                    <td class="has-text-weight-bold">TOTAL GENERAL</td>
                                    <td class="has-text-weight-bold is-size-5">
                                        <?php echo MONEDA_SIMBOLO . number_format($_SESSION['venta_total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR); ?>
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            <?php
                            } else {
                                $_SESSION['venta_total'] = 0;
                                echo '<tr class="has-text-centered"><td colspan="8">No hay productos agregados</td></tr>';
                            } ?>
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
                        if (!isset($_SESSION['datos_cliente_venta'])) {
                            $datos_cliente = $insVenta->seleccionarDatos("Normal", "cliente WHERE cliente_id='1'", "*", 0);
                            if ($datos_cliente->rowCount() == 1) {
                                $datos_cliente = $datos_cliente->fetch();
                                $_SESSION['datos_cliente_venta'] = [
                                    "cliente_id" => $datos_cliente['cliente_id'],
                                    "cliente_tipo_documento" => $datos_cliente['cliente_tipo_documento'],
                                    "cliente_numero_documento" => $datos_cliente['cliente_numero_documento'],
                                    "cliente_nombre" => $datos_cliente['cliente_nombre'],
                                    "cliente_apellido" => $datos_cliente['cliente_apellido']
                                ];
                            }
                        }
                        ?>
                        <div class="field has-addons mb-5">
                            <div class="control">
                                <input class="input" type="text" readonly id="venta_cliente" value="<?php echo $_SESSION['datos_cliente_venta']['cliente_nombre'] . " " . $_SESSION['datos_cliente_venta']['cliente_apellido']; ?>">
                            </div>
                            <div class="control">
                                <a class="button is-info js-modal-trigger" data-target="modal-js-client" title="Cambiar cliente" id="btn_add_client">
                                    <i class="fas fa-users fa-fw"></i>
                                </a>
                            </div>
                        </div>

                        <div class="columns">
                            <div class="column is-half">
                                <div class="control mb-5">
                                    <label>Método de Pago <?php echo CAMPO_OBLIGATORIO; ?></label>
                                    <div class="select is-fullwidth">
                                        <select name="venta_metodo_pago" id="venta_metodo_pago" required>
                                            <option value="Pago Movil">Pago Móvil</option>
                                            <option value="Transferencia">Transferencia</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-half">
                                <div class="control mb-5">
                                    <label>Referencia <span id="req_referencia" style="color:red; display:none;">*</span></label>
                                    <input class="input" type="text" name="venta_referencia" id="venta_referencia" pattern="[0-9]{6}" maxlength="6" placeholder="Ej: 123456" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="control mb-3">
                            <label>Monto Pagado / Confirmado ($) <?php echo CAMPO_OBLIGATORIO; ?></label>
                            <input class="input has-text-weight-bold is-info is-static" type="number" name="venta_abono" id="venta_abono" value="<?php echo number_format($_SESSION['venta_total'], MONEDA_DECIMALES, '.', ''); ?>" readonly>
                            <p class="help is-info">El sistema procesará el cobro exacto de la factura.</p>
                        </div>

                        <div class="box mt-4 p-5" style="border-top: 5px solid #004595; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                            <p class="has-text-centered has-text-weight-bold has-text-grey-dark mb-4 is-size-5">
                                <i class="fas fa-dollar-sign"></i> TOTAL A COBRAR: <?php echo number_format($_SESSION['venta_total'], MONEDA_DECIMALES, MONEDA_SEPARADOR_DECIMAL, MONEDA_SEPARADOR_MILLAR); ?>
                            </p>
                            <hr>
                            <p class="title is-3 has-text-centered has-text-link mt-4" id="total_bs_label">Calculando Bs...</p>
                        </div>

                        <?php if ($_SESSION['venta_total'] > 0) { ?>
                            <p class="has-text-centered">
                                <button type="submit" class="button is-info is-rounded is-medium mt-4"><i class="far fa-save"></i> &nbsp; Procesar Venta</button>
                            </p>
                        <?php } ?>
                        <input type="hidden" value="<?php echo number_format($_SESSION['venta_total'], MONEDA_DECIMALES, '.', ""); ?>" id="venta_total_hidden">
                        <input type="hidden" name="venta_tasa_bcv" id="venta_tasa_bcv" value="0">
                        </form>
            </div>

        </div>
    <?php } else { ?>
        <article class="message is-warning">
            <div class="message-body has-text-centered"><i class="fas fa-exclamation-triangle fa-2x"></i><br>Faltan datos de la empresa.</div>
        </article>
    <?php } ?>
</div>

<div class="modal" id="modal-js-product">
    <div class="modal-background"></div>
    <div class="modal-card" style="width:90%; max-width:1000px;">
        <header class="modal-card-head"><p class="modal-card-title is-uppercase"><i class="fas fa-search"></i> Buscar producto</p><button class="delete" aria-label="close"></button></header>
        <section class="modal-card-body">
            <?php use app\controllers\productController; $insProductoModal = new productController(); ?>
            <div class="columns">
                <div class="column is-one-third">
                    <h3 class="title is-6 has-text-centered">Categorías</h3>
                    <div class="categories-navigation" style="max-height: 400px; overflow-y: auto;">
                        <?php
                        $datos_cat = $insProductoModal->seleccionarDatos("Normal", "categoria", "*", 0);
                        if ($datos_cat->rowCount() > 0) {
                            $todas = $datos_cat->fetchAll();
                            foreach ($todas as $p) {
                                if (empty($p['categoria_padre_id']) || $p['categoria_padre_id'] == "0") {
                                    echo '<button type="button" class="button is-link is-light is-fullwidth mb-1 btn-padre-modal" style="justify-content: flex-start; font-weight: bold;"><i class="fas fa-folder mr-2"></i> ' . $p['categoria_nombre'] . '</button>';
                                    echo '<div class="sub-modal-container" style="display: none; padding-left: 15px; margin-bottom: 10px;">';
                                    foreach ($todas as $h) {
                                        if ($h['categoria_padre_id'] == $p['categoria_id']) {
                                            echo '<button type="button" class="button is-fullwidth is-small mb-1" onclick="cargar_por_categoria(' . $h['categoria_id'] . ')" style="justify-content: flex-start;"><i class="fas fa-arrow-right mr-2"></i> ' . $h['categoria_nombre'] . '</button>';
                                        }
                                    }
                                    echo '</div>';
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="column">
                    <div class="field">
                        <label class="label">Filtros de búsqueda</label>
                        <input type="hidden" id="id_categoria_variable" value="">
                        <div class="columns is-multiline">
                            <div class="column is-6">
                                <div class="select is-fullwidth"><select id="filtro_marca" onchange="buscar_codigo()"><option value="">Todas las Marcas</option>
                                    <?php $marcas = $insProductoModal->seleccionarDatos("Normal", "producto GROUP BY producto_marca", "producto_marca", 0); while ($m = $marcas->fetch()) { if ($m['producto_marca'] != "") echo '<option value="' . $m['producto_marca'] . '">' . $m['producto_marca'] . '</option>'; } ?>
                                </select></div>
                            </div>
                            <div class="column is-6">
                                <div class="select is-fullwidth"><select id="filtro_modelo" onchange="buscar_codigo()"><option value="">Todos los Modelos</option>
                                    <?php $modelos = $insProductoModal->seleccionarDatos("Normal", "producto GROUP BY producto_modelo", "producto_modelo", 0); while ($mo = $modelos->fetch()) { if ($mo['producto_modelo'] != "") echo '<option value="' . $mo['producto_modelo'] . '">' . $mo['producto_modelo'] . '</option>'; } ?>
                                </select></div>
                            </div>
                            <div class="column is-12">
                                <div class="field has-addons">
                                    <div class="control is-expanded"><input class="input" type="text" id="input_codigo" placeholder="Escribe nombre, marca o modelo..." autocomplete="off"></div>
                                    <div class="control"><button type="button" class="button is-link" onclick="buscar_codigo()"><i class="fas fa-search"></i></button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tabla_productos" class="table-container mt-4"></div>
                </div>
            </div>
            <script>
                document.querySelectorAll('.btn-padre-modal').forEach(btn => {
                    btn.addEventListener('click', function() {
                        let contenedor = this.nextElementSibling;
                        contenedor.style.display = (contenedor.style.display === "none") ? "block" : "none";
                    });
                });
                function cargar_por_categoria(id) {
                    document.querySelector('#id_categoria_variable').value = id;
                    let datosFiltros = new FormData(); datosFiltros.append("modulo_venta", "obtener_filtros_categoria"); datosFiltros.append("categoria_id", id);
                    fetch('<?php echo APP_URL; ?>app/ajax/ventaAjax.php', { method: 'POST', body: datosFiltros })
                    .then(res => res.json())
                    .then(data => {
                        document.querySelector('#filtro_marca').innerHTML = '<option value="">Todas</option>' + data.marcas.map(m => m?`<option value="${m}">${m}</option>`:'').join('');
                        document.querySelector('#filtro_modelo').innerHTML = '<option value="">Todos</option>' + data.modelos.map(mo => mo?`<option value="${mo}">${mo}</option>`:'').join('');
                    });
                    let datosTabla = new FormData(); datosTabla.append("categoria_id", id); datosTabla.append("modulo_venta", "buscar_por_categoria");
                    fetch('<?php echo APP_URL; ?>app/ajax/ventaAjax.php', { method: 'POST', body: datosTabla }).then(r => r.text()).then(r => { document.querySelector('#tabla_productos').innerHTML = r; });
                }
                document.querySelector('#input_codigo').addEventListener('keyup', function() { setTimeout(buscar_codigo, 300); });
            </script>
        </section>
    </div>
</div>

<div class="modal" id="modal-js-client">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head"><p class="modal-card-title is-uppercase"><i class="fas fa-search"></i> Buscar cliente</p><button class="delete" aria-label="close"></button></header>
        <section class="modal-card-body">
            <div class="field mt-2 mb-4"><div class="control"><input class="input" type="text" placeholder="Documento, Nombre o Apellido" name="input_cliente" id="input_cliente" autocomplete="off"></div></div>
            <div class="container" id="tabla_clientes"></div>
        </section>
    </div>
</div>

<script>
    /* Lógica Matemática de Bs */
    document.addEventListener('DOMContentLoaded', function() {
        let tasa_bcv = parseFloat(localStorage.getItem('tasa_bcv')) || 0;
        let total_input = document.querySelector('#venta_total_hidden');

        // Calcula Totales de Tabla
        document.querySelectorAll('.precio-bcv-cart').forEach(function(el) {
            let usd = parseFloat(el.getAttribute('data-usd')) || 0;
            if (tasa_bcv > 0) { el.innerHTML = `Bs. ` + new Intl.NumberFormat('es-VE', {minimumFractionDigits: 2}).format(usd * tasa_bcv); } 
            else { el.innerHTML = `<span class="has-text-danger">Sin BCV</span>`; }
        });

        // Calcula Total General
        if (total_input && tasa_bcv > 0) {
            let total_bs = parseFloat(total_input.value) * tasa_bcv;
            document.querySelector('#total_bs_label').innerHTML = `Bs. ` + new Intl.NumberFormat('es-VE', {minimumFractionDigits: 2}).format(total_bs);
            document.querySelector('#venta_tasa_bcv').value = tasa_bcv;
        }
    });

    /* Select Método Pago */
    document.getElementById('venta_metodo_pago').addEventListener('change', function() {
        let metodo = this.value; let refInput = document.getElementById('venta_referencia'); let reqSpan = document.getElementById('req_referencia');
        if (metodo === 'Pago Movil' || metodo === 'Transferencia') {
            refInput.disabled = false; refInput.required = true; reqSpan.style.display = 'inline';
        } else {
            refInput.disabled = true; refInput.required = false; refInput.value = ''; reqSpan.style.display = 'none';
        }
    });

    let form_sale_action = document.querySelector("form[name='formsale']");
    if (form_sale_action) {
        form_sale_action.addEventListener('submit', function(e) {
            document.querySelector('#venta_tasa_bcv').value = parseFloat(localStorage.getItem('tasa_bcv')) || 0;
        });
    }

    document.querySelector("#sale-barcode-form").addEventListener('submit', function(e) { e.preventDefault(); setTimeout('agregar_producto()', 100); });
    function agregar_producto() {
        let codigo_producto = document.querySelector('#sale-barcode-input').value.trim();
        if (codigo_producto != "") {
            let datos = new FormData(); datos.append("producto_codigo", codigo_producto); datos.append("modulo_venta", "agregar_producto");
            fetch('<?php echo APP_URL; ?>app/ajax/ventaAjax.php', { method: 'POST', body: datos }).then(res => res.json()).then(res => { return alertas_ajax(res); });
        }
    }

    function buscar_codigo() {
        let input_codigo = document.querySelector('#input_codigo').value.trim();
        let categoria_id = document.querySelector('#id_categoria_variable').value;
        let marca = document.querySelector('#filtro_marca').value;
        let modelo = document.querySelector('#filtro_modelo').value;
        if (input_codigo != "" || marca != "" || modelo != "") {
            let datos = new FormData(); datos.append("buscar_codigo", input_codigo); datos.append("categoria_id", categoria_id); datos.append("filtro_marca", marca); datos.append("filtro_modelo", modelo); datos.append("modulo_venta", "buscar_codigo");
            fetch('<?php echo APP_URL; ?>app/ajax/ventaAjax.php', { method: 'POST', body: datos }).then(res => res.text()).then(res => { document.querySelector('#tabla_productos').innerHTML = res; });
        } else { document.querySelector('#tabla_productos').innerHTML = ""; }
    }

    function agregar_codigo($codigo) { document.querySelector('#sale-barcode-input').value = $codigo; setTimeout('agregar_producto()', 100); }
    function actualizar_cantidad(id, codigo) {
        let cantidad = document.querySelector(id).value.trim();
        if (cantidad > 0) {
            let datos = new FormData(); datos.append("producto_codigo", codigo); datos.append("producto_cantidad", cantidad); datos.append("modulo_venta", "actualizar_producto");
            fetch('<?php echo APP_URL; ?>app/ajax/ventaAjax.php', { method: 'POST', body: datos }).then(r => r.json()).then(r => { return alertas_ajax(r); });
        }
    }

    document.querySelector('#input_cliente').addEventListener('keyup', function(e) {
        let texto = this.value.trim();
        if(texto != ""){
            let datos = new FormData(); datos.append("buscar_cliente", texto); datos.append("modulo_venta", "buscar_cliente");
            fetch('<?php echo APP_URL; ?>app/ajax/ventaAjax.php', { method: 'POST', body: datos }).then(r => r.text()).then(r => { document.querySelector('#tabla_clientes').innerHTML = r; });
        } else { document.querySelector('#tabla_clientes').innerHTML = ""; }
    });

    function agregar_cliente(id) {
        let datos = new FormData(); datos.append("cliente_id", id); datos.append("modulo_venta", "agregar_cliente");
        fetch('<?php echo APP_URL; ?>app/ajax/ventaAjax.php', { method: 'POST', body: datos }).then(r => r.json()).then(r => { return alertas_ajax(r); });
    }
</script>

<?php
include "./app/views/inc/print_invoice_script.php";
if (isset($_SESSION['venta_codigo_factura']) && $_SESSION['venta_codigo_factura'] != "") {
?>
    <script>
        Swal.fire({
            title: '¡Venta Procesada!', text: 'El pago ingresó y el stock se restó.', icon: 'success', showCancelButton: true, confirmButtonText: '<i class="fas fa-file-pdf"></i> Imprimir', cancelButtonText: 'Cerrar'
        }).then((result) => {
            if (result.isConfirmed) { print_invoice('<?php echo APP_URL . "app/pdf/invoice.php?code=" . $_SESSION['venta_codigo_factura']; ?>'); }
        });
    </script>
<?php unset($_SESSION['venta_codigo_factura']); } ?>