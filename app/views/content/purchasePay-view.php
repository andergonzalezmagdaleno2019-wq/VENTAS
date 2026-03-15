<div class="container is-fluid mb-6">
    <h1 class="title">Cuentas por Pagar</h1>
    <h2 class="subtitle">Gestión de facturas pendientes y vencimientos</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        use app\controllers\purchaseController;
        $insCompra = new purchaseController();

        /* --- 1. CÁLCULO DE INDICADORES (KPIs) --- */
        $total_deuda = $insCompra->ejecutarConsulta("SELECT SUM(compra_saldo_pendiente) as total FROM compra WHERE compra_saldo_pendiente > 0");
        $total_deuda = $total_deuda->fetch()['total'] ?? 0;

        $vencido = $insCompra->ejecutarConsulta("SELECT SUM(compra_saldo_pendiente) as total FROM compra WHERE compra_fecha_vencimiento < CURRENT_DATE AND compra_saldo_pendiente > 0");
        $vencido = $vencido->fetch()['total'] ?? 0;

        $proximo = $insCompra->ejecutarConsulta("SELECT SUM(compra_saldo_pendiente) as total FROM compra WHERE compra_fecha_vencimiento BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY) AND compra_saldo_pendiente > 0");
        $proximo = $proximo->fetch()['total'] ?? 0;
    ?>

    <div class="columns is-multiline mb-6">
        <div class="column is-one-third">
            <div class="box has-background-info-light" style="border-left: 5px solid #209cee;">
                <div class="level is-mobile">
                    <div class="level-item has-text-centered">
                        <div>
                            <p class="heading">Total por Pagar</p>
                            <p class="title is-4"><?php echo number_format($total_deuda, 2); ?> $</p>
                        </div>
                    </div>
                    <div class="level-item has-text-centered">
                        <i class="fas fa-money-bill-wave fa-2x has-text-info"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="column is-one-third">
            <div class="box has-background-danger-light" style="border-left: 5px solid #ff3860;">
                <div class="level is-mobile">
                    <div class="level-item has-text-centered">
                        <div>
                            <p class="heading">Monto Vencido</p>
                            <p class="title is-4"><?php echo number_format($vencido, 2); ?> $</p>
                        </div>
                    </div>
                    <div class="level-item has-text-centered">
                        <i class="fas fa-exclamation-circle fa-2x has-text-danger"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="column is-one-third">
            <div class="box has-background-warning-light" style="border-left: 5px solid #ffdd57;">
                <div class="level is-mobile">
                    <div class="level-item has-text-centered">
                        <div>
                            <p class="heading">Vence en 7 días</p>
                            <p class="title is-4"><?php echo number_format($proximo, 2); ?> $</p>
                        </div>
                    </div>
                    <div class="level-item has-text-centered">
                        <i class="fas fa-calendar-alt fa-2x has-text-warning-dark"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
        $consulta = "SELECT c.*, p.proveedor_nombre, 
            DATEDIFF(c.compra_fecha_vencimiento, CURRENT_DATE) as dias_restantes 
            FROM compra c 
            INNER JOIN proveedor p ON c.proveedor_id = p.proveedor_id 
            WHERE c.compra_saldo_pendiente > 0 
            ORDER BY c.compra_fecha_vencimiento ASC";

        $datos = $insCompra->ejecutarConsulta($consulta);
        
        if($datos->rowCount() > 0){
            $datos = $datos->fetchAll();
    ?>
    
    <div class="table-container">
        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
            <thead>
                <tr class="is-link">
                    <th class="has-text-centered has-text-white">Código</th>
                    <th class="has-text-centered has-text-white">Proveedor</th>
                    <th class="has-text-centered has-text-white">Estado Pago</th>
                    <th class="has-text-centered has-text-white">Vencimiento</th>
                    <th class="has-text-centered has-text-white">Saldo Pendiente</th>
                    <th class="has-text-centered has-text-white">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($datos as $c){
                        $dias = $c['dias_restantes'];
                        
                        // Lógica de Semáforo de Vencimiento
                        if($dias < 0){
                            $clase_color = "has-text-danger has-text-weight-bold";
                            $mensaje = "Vencida hace " . abs($dias) . " días";
                            $icono = "fa-exclamation-triangle";
                        } elseif($dias <= 3){
                            $clase_color = "has-text-warning has-text-weight-bold";
                            $mensaje = "Vence en $dias días";
                            $icono = "fa-clock";
                        } else {
                            $clase_color = "has-text-info";
                            $mensaje = "Al día ($dias días)";
                            $icono = "fa-check-circle";
                        }

                        // Lógica de colores para el Estado de Pago
                        $tag_color = "is-info"; // Parcial
                        if($c['compra_estado_pago'] == "Pendiente") $tag_color = "is-danger";
                        if($c['compra_estado_pago'] == "Pagado") $tag_color = "is-success";
                ?>
                    <tr class="has-text-centered">
                        <td><?php echo $c['compra_codigo']; ?></td>
                        <td><?php echo $c['proveedor_nombre']; ?></td>
                        <td>
                            <span class="tag <?php echo $tag_color; ?> is-light is-rounded">
                                <?php echo $c['compra_estado_pago']; ?>
                            </span>
                        </td>
                        <td class="<?php echo $clase_color; ?>">
                            <i class="fas <?php echo $icono; ?>"></i><br>
                            <?php echo date("d/m/Y", strtotime($c['compra_fecha_vencimiento'])); ?>
                            <br><small><?php echo $mensaje; ?></small>
                        </td>
                        <td class="is-size-5 has-text-weight-bold">
                            <?php echo number_format($c['compra_saldo_pendiente'], 2); ?> $
                        </td>
                        <td>
                            <div class="buttons is-centered">
                                <button class="button is-success is-small is-rounded" onclick="abrirModalAbono(
                                    '<?php echo $c['compra_id']; ?>', 
                                    '<?php echo $c['compra_codigo']; ?>', 
                                    '<?php echo $c['compra_saldo_pendiente']; ?>'
                                )">
                                    <i class="fas fa-hand-holding-usd"></i>&nbsp; Abonar
                                </button>
                                <button class="button is-info is-small is-rounded" onclick="verHistorialAbonos('<?php echo $c['compra_id']; ?>', '<?php echo $c['compra_codigo']; ?>')">
                                    <i class="fas fa-history"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php } else { ?>
        <p class="has-text-centered">No hay cuentas pendientes por pagar.</p>
    <?php } ?>
</div>

<div class="modal" id="modal-abono">
    <div class="modal-background" onclick="cerrarModalAbono()"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Registrar Abono - <span id="txt_codigo_compra"></span></p>
            <button class="delete" aria-label="close" onclick="cerrarModalAbono()"></button>
        </header>
        <section class="modal-card-body">
            <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/compraAjax.php" method="POST" autocomplete="off">
                <input type="hidden" name="modulo_compra" value="registrar_abono">
                <input type="hidden" name="pago_compra_id" id="pago_compra_id">
                <div class="columns">
                    <div class="column">
                        <div class="control"><label>Saldo Pendiente ($)</label><input class="input" type="text" id="pago_saldo_actual" readonly></div>
                    </div>
                    <div class="column">
                        <div class="control"><label>Monto a Abonar</label><input class="input" type="number" name="pago_monto" id="pago_monto" step="0.01" required></div>
                    </div>
                </div>
                <div class="columns">
                    <div class="column">
                        <div class="control"><label>Método de Pago</label>
                            <div class="select is-fullwidth">
                                <select name="pago_metodo" id="pago_metodo">
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Transferencia">Transferencia</option>
                                    <option value="Divisas">Divisas</option>
                                    <option value="Debito">Débito</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="control"><label>Referencia</label><input class="input" type="text" name="pago_referencia" id="pago_referencia" placeholder="Nro de operación"></div>
                    </div>
                </div>
                <p class="has-text-centered mt-4"><button type="submit" class="button is-info is-rounded">Confirmar Pago</button></p>
            </form>
        </section>
    </div>
</div>

<div class="modal" id="modal-historial">
    <div class="modal-background" onclick="cerrarModalHistorial()"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Historial de Pagos - <span id="historial_codigo"></span></p>
            <button class="delete" aria-label="close" onclick="cerrarModalHistorial()"></button>
        </header>
        <section class="modal-card-body">
            <div id="contenido_historial"></div>
        </section>
    </div>
</div>

<script>
    const selectMetodo = document.querySelector('#pago_metodo');
    const inputReferencia = document.querySelector('#pago_referencia');

    if(selectMetodo && inputReferencia){
        selectMetodo.addEventListener('change', function() {
            if (this.value === "Efectivo" || this.value === "Divisas") {
                inputReferencia.value = "EFECTIVO/DIVISA";
                inputReferencia.readOnly = true;
                inputReferencia.classList.add('is-static');
            } else {
                inputReferencia.value = "";
                inputReferencia.readOnly = false;
                inputReferencia.classList.remove('is-static');
                inputReferencia.placeholder = "Nro de operación";
            }
        });
    }

    function abrirModalAbono(id, codigo, saldo) {
        document.getElementById('pago_compra_id').value = id;
        document.getElementById('txt_codigo_compra').innerText = codigo;
        document.getElementById('pago_saldo_actual').value = saldo;
        document.getElementById('pago_monto').max = saldo;
        document.getElementById('pago_monto').value = "";
        
        selectMetodo.value = "Efectivo"; 
        inputReferencia.value = "EFECTIVO/DIVISA";
        inputReferencia.readOnly = true;
        inputReferencia.classList.add('is-static');

        document.getElementById('modal-abono').classList.add('is-active');
    }

    function cerrarModalAbono() { document.getElementById('modal-abono').classList.remove('is-active'); }

    function verHistorialAbonos(id, codigo) {
        document.getElementById('historial_codigo').innerText = codigo;
        document.getElementById('contenido_historial').innerHTML = '<div class="has-text-centered"><i class="fas fa-sync fa-spin fa-2x"></i><p>Cargando historial...</p></div>';
        document.getElementById('modal-historial').classList.add('is-active');

        let datos = new FormData();
        datos.append("modulo_compra", "ver_historial_abonos");
        datos.append("compra_id", id);

        fetch("<?php echo APP_URL; ?>app/ajax/compraAjax.php", {
            method: 'POST',
            body: datos
        })
        .then(res => res.text())
        .then(res => {
            document.getElementById('contenido_historial').innerHTML = res;
        });
    }

    function cerrarModalHistorial() { document.getElementById('modal-historial').classList.remove('is-active'); }
</script>