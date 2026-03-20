<div class="container is-fluid mb-6">
    <h1 class="title">Productos</h1>
    <h2 class="subtitle"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar producto</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        include "./app/views/inc/btn_back.php";

        $id=$insLogin->limpiarCadena($url[1]);
        $datos=$insLogin->seleccionarDatos("Unico","producto","producto_id",$id);

        if($datos->rowCount()==1){
            $datos=$datos->fetch();
    ?>
    
    <div class="columns is-flex is-justify-content-center">
        <figure class="full-width mb-3" style="max-width: 170px;">
            <?php
                if(is_file("./app/views/productos/".$datos['producto_foto'])){
                    echo '<img class="img-responsive" src="'.APP_URL.'app/views/productos/'.$datos['producto_foto'].'">';
                }else{
                    echo '<img class="img-responsive" src="'.APP_URL.'app/views/productos/default.png">';
                }
            ?>
        </figure>
    </div>

    <h2 class="title has-text-centered"><?php echo $datos['producto_nombre']." (Stock Actual: ".$datos['producto_stock'].")"; ?></h2>

    <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/productoAjax.php" method="POST" autocomplete="off" >

        <input type="hidden" name="modulo_producto" value="actualizar">
        <input type="hidden" name="producto_id" value="<?php echo $datos['producto_id']; ?>">

        <div class="columns">
            <div class="column">
                <div class="control">
                    <label>Código de barra</label>
                    <input class="input is-readonly" type="text" name="producto_codigo" value="<?php echo $datos['producto_codigo']; ?>" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
                    <p class="help">El código de barras no es editable</p>
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Nombre <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="text" name="producto_nombre" value="<?php echo $datos['producto_nombre']; ?>" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,100}" maxlength="100" required autocomplete="off">
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="control">
                    <label>Marca</label>
                    <input class="input" type="text" name="producto_marca" value="<?php echo $datos['producto_marca']; ?>" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,30}" maxlength="30" autocomplete="off">
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Modelo</label>
                    <input class="input" type="text" name="producto_modelo" value="<?php echo $datos['producto_modelo']; ?>" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,30}" maxlength="30" autocomplete="off">
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="control">
                    <label>Costo de Compra (Neto) $</label>
                    <input class="input is-readonly" type="text" name="producto_costo" id="producto_costo_up" value="<?php echo $datos['producto_costo']; ?>" readonly style="background-color: #f5f5f5; cursor: not-allowed; border-color: transparent;">
                    <p class="help is-info has-text-weight-bold" id="costo_bs_label_up">Bs. 0.00</p>
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Precio de Venta $</label>
                    <input class="input is-readonly" type="text" name="producto_precio" id="producto_precio_up" value="<?php echo $datos['producto_precio']; ?>" readonly style="background-color: #f5f5f5; cursor: not-allowed; border-color: transparent;">
                    <p class="help is-link has-text-weight-bold" id="precio_bs_label_up">Bs. 0.00</p>
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Tipo de Producto <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <div class="select is-fullwidth">
                        <select name="producto_unidad" required>
                            <?php
                                echo $insLogin->generarSelect(PRODUCTO_UNIDAD,$datos['producto_unidad']);
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="columns">
            <div class="column is-full mb-4">
                <div class="notification is-link is-light" style="border-left: 5px solid #3273dc; padding: 10px;">
                    <i class="fas fa-lock fa-lg"></i> &nbsp; 
                    <strong>Aviso:</strong> El Costo y el Precio de Venta están bloqueados por seguridad. Su valor solo se actualizará automáticamente al recibir mercancía en el módulo de <strong>Compras</strong>.
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="control">
                    <label>Stock Mínimo <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="text" name="producto_stock_min" value="<?php echo $datos['producto_stock_min']; ?>" pattern="[0-9]{1,25}" maxlength="25" required >
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Stock Máximo <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="text" name="producto_stock_max" value="<?php echo $datos['producto_stock_max']; ?>" pattern="[0-9]{1,25}" maxlength="25" required >
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column is-full">
                <label>Categoría / Subcategoría <?php echo CAMPO_OBLIGATORIO; ?></label>
                <div class="select is-fullwidth">
                    <select name="producto_categoria" required>
                        <?php
                            $insCategory = new app\controllers\categoryController();
                            $datos_cat = $insCategory->seleccionarDatos("Normal", "categoria", "*", "ORDER BY categoria_nombre ASC");
                            $todas = $datos_cat->fetchAll();

                            foreach($todas as $p){
                                if($p['categoria_padre_id'] == NULL || $p['categoria_padre_id'] == "" || $p['categoria_padre_id'] == "0"){
                                    echo '<optgroup label="📂 '.$p['categoria_nombre'].'">';
                                    foreach($todas as $h){
                                        if($h['categoria_padre_id'] == $p['categoria_id']){
                                            $seleccionado = ($h['categoria_id'] == $datos['categoria_id']) ? 'selected=""' : '';
                                            $texto_actual = ($h['categoria_id'] == $datos['categoria_id']) ? ' (Actual)' : '';
                                            echo '<option value="'.$h['categoria_id'].'" '.$seleccionado.'>'.$h['categoria_nombre'].$texto_actual.'</option>';
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

        <p class="has-text-centered mt-5">
            <button type="submit" class="button is-success is-rounded"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar Datos</button>
        </p>
    </form>
    
    <script>
        const inputCostoUp = document.getElementById('producto_costo_up');
        const inputPrecioUp = document.getElementById('producto_precio_up');
        const costoBsLabelUp = document.getElementById('costo_bs_label_up');
        const precioBsLabelUp = document.getElementById('precio_bs_label_up');
        let tasa_bcv = parseFloat(localStorage.getItem('tasa_bcv')) || 0;

        function calcularPrecioUp() {
            let costo = parseFloat(inputCostoUp.value);
            let precioFinal = parseFloat(inputPrecioUp.value);
            
            if (!isNaN(costo) && !isNaN(precioFinal)) {
                if(tasa_bcv > 0){
                    let formatBs = new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    costoBsLabelUp.innerText = "Bs. " + formatBs.format(costo * tasa_bcv);
                    precioBsLabelUp.innerText = "Bs. " + formatBs.format(precioFinal * tasa_bcv);
                } else {
                    costoBsLabelUp.innerText = "Sin conexión BCV";
                    precioBsLabelUp.innerText = "Sin conexión BCV";
                }
            }
        }

        // Eliminamos el evento 'input' porque los campos ya no se pueden editar manualmente
        document.addEventListener('DOMContentLoaded', calcularPrecioUp);
    </script>
    
    <?php
        }else{
            include "./app/views/inc/error_alert.php";
        }
    ?>
</div>