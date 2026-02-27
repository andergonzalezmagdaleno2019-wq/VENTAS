<div class="container is-fluid mb-6">
	<h1 class="title">Compras</h1>
	<h2 class="subtitle">Nueva Orden de Compra (Entrada de Almacén)</h2>
</div>

<div class="container is-fluid pb-6">
    
    <div class="columns">
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
                                <input class="input" type="text" name="buscar_producto" id="buscar_producto" placeholder="Nombre o código del producto" required>
                            </div>
                            <div class="control">
                                <button type="submit" class="button is-info">Buscar</button>
                            </div>
                        </div>
                    </form>
                    
                    <div id="resultados_busqueda" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="columns">
        <div class="column">
            <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/compraAjax.php" method="POST" autocomplete="off" name="formpurchase">
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
                                                <small>(Para borrar, vacíe la compra)</small>
                                            </td>
                                        </tr>';
                                    }
                                }else{
                                    echo '<tr class="has-text-centered"><td colspan="5">No hay productos agregados a esta compra</td></tr>';
                                }
                            ?>
                        </tbody>
                    </table>
                    
                    <input type="hidden" id="compra_total_hidden" value="<?php echo number_format($total, 2, '.', ''); ?>">
                    <input type="hidden" name="compra_tasa_bcv" id="compra_tasa_bcv" value="0">

                    <?php if(isset($_SESSION['datos_compra']) && count($_SESSION['datos_compra'])>=1){ ?>
                    <p class="has-text-centered mt-5">
                        <button type="submit" class="button is-success is-rounded"><i class="fas fa-save"></i> &nbsp; PROCESAR COMPRA</button>
                    </p>
                    <?php } ?>
                </div>
            </form>

            <?php if(isset($_SESSION['datos_compra']) && count($_SESSION['datos_compra'])>=1){ ?>
            <form class="FormularioAjax has-text-centered mt-2" action="<?php echo APP_URL; ?>app/ajax/compraAjax.php" method="POST" autocomplete="off">
                <input type="hidden" name="modulo_compra" value="vaciar">
                <button type="submit" class="button is-danger is-outlined is-small">Vaciar lista</button>
            </form>
            <?php } ?>

        </div>
    </div>

    <script>
        /* 1. Calcular Totales en Bolívares al cargar la página */
        document.addEventListener('DOMContentLoaded', function() {
            let tasa_bcv = parseFloat(localStorage.getItem('tasa_bcv')) || 0;

            // Calcular subtotales de la tabla
            let elementos = document.querySelectorAll('.precio-bcv-cart');
            elementos.forEach(function(el) {
                let usd = parseFloat(el.getAttribute('data-usd')) || 0;
                if(tasa_bcv > 0){
                    let formatBs = new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(usd * tasa_bcv);
                    el.innerHTML = `Bs. ${formatBs}`;
                } else {
                    el.innerHTML = `<span class="has-text-danger">Sin BCV</span>`;
                }
            });

            // Total General
            let total_dolares = document.querySelector('#compra_total_hidden');
            if(total_dolares){
                let total_num = parseFloat(total_dolares.value) || 0;
                
                if(tasa_bcv > 0 && total_num > 0) {
                    let total_bs = total_num * tasa_bcv;
                    let formato_bs = new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(total_bs);
                    document.querySelector('#total_compra_bs_label').innerHTML = `Bs. ${formato_bs}`;
                } else if (total_num === 0) {
                    document.querySelector('#total_compra_bs_label').innerHTML = `Bs. 0.00`;
                } else {
                    document.querySelector('#total_compra_bs_label').innerHTML = `<small class="has-text-danger is-size-6">Sin conexión BCV</small>`;
                }
            }
        });

        /* 2. Atrapar la tasa al enviar el formulario (Igual que en ventas) */
        let form_purchase_action = document.querySelector("form[name='formpurchase']");
        if(form_purchase_action){
            form_purchase_action.addEventListener('submit', function(e){
                let input_tasa = document.querySelector('#compra_tasa_bcv');
                let tasa_bcv = parseFloat(localStorage.getItem('tasa_bcv')) || 0;
                if(input_tasa){ input_tasa.value = tasa_bcv; }
            });
        }

        const formularioBuscar = document.getElementById('form-buscar-compra');
        const resultadoBusqueda = document.getElementById('resultados_busqueda');

        formularioBuscar.addEventListener('submit', function(e){
            e.preventDefault();
            let datos = new FormData(this);
            datos.append('modulo_compra', 'buscar_producto');

            fetch('<?php echo APP_URL; ?>app/ajax/compraAjax.php', {
                method: 'POST',
                body: datos
            })
            .then(respuesta => respuesta.text())
            .then(respuesta => {
                resultadoBusqueda.innerHTML = respuesta;
                reactivarFormularios();
            });
        });

        function reactivarFormularios(){
            const nuevosFormularios = resultadoBusqueda.querySelectorAll(".FormularioAjax");

            nuevosFormularios.forEach(form => {
                form.addEventListener("submit", function(e){
                    e.preventDefault(); 
                    let data = new FormData(this);
                    let method = this.getAttribute("method");
                    let action = this.getAttribute("action");
                    let config = { method: method, body: data };

                    fetch(action, config)
                    .then(respuesta => respuesta.json())
                    .then(respuesta => {
                        return alertas_ajax(respuesta);
                    });
                });
            });
        }
    </script>
</div>