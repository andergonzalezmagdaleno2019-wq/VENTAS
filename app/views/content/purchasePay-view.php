<div class="container is-fluid mb-6">
    <h1 class="title">Cuentas por Pagar y Saldos a Favor</h1>
    <h2 class="subtitle">Gestión de facturas pendientes y anticipos a proveedores</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        use app\controllers\purchaseController;
        $insCompra = new purchaseController();

        /* --- 1. CÁLCULO DE INDICADORES (KPIs) FINANCIEROS --- */
        
        // Deuda Total
        $total_deuda = $insCompra->ejecutarConsulta("SELECT SUM(compra_saldo_pendiente) as total FROM compra WHERE compra_saldo_pendiente > 0");
        $total_deuda = $total_deuda->fetch()['total'] ?? 0;

        // Deuda Vencida
        $vencido = $insCompra->ejecutarConsulta("SELECT SUM(compra_saldo_pendiente) as total FROM compra WHERE compra_fecha_vencimiento < CURRENT_DATE AND compra_saldo_pendiente > 0");
        $vencido = $vencido->fetch()['total'] ?? 0;

        // Próximo a vencer
        $proximo = $insCompra->ejecutarConsulta("SELECT SUM(compra_saldo_pendiente) as total FROM compra WHERE compra_fecha_vencimiento BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY) AND compra_saldo_pendiente > 0");
        $proximo = $proximo->fetch()['total'] ?? 0;

        // NUEVO: Saldo a Favor (Anticipos que superaron la factura)
        // Usamos abs() para convertir el número negativo (ej: -100) en positivo (100) para mostrarlo bonito
        $saldo_favor = $insCompra->ejecutarConsulta("SELECT SUM(compra_saldo_pendiente) as total FROM compra WHERE compra_saldo_pendiente < 0");
        $saldo_favor = abs((float)($saldo_favor->fetch()['total'] ?? 0));
    ?>

    <div class="columns is-multiline mb-6">
        <div class="column is-3">
            <div class="box has-background-info-light" style="border-left: 5px solid #209cee; height: 100%;">
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

        <div class="column is-3">
            <div class="box has-background-danger-light" style="border-left: 5px solid #ff3860; height: 100%;">
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

        <div class="column is-3">
            <div class="box has-background-warning-light" style="border-left: 5px solid #ffdd57; height: 100%;">
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

        <div class="column is-3">
            <div class="box has-background-success-light" style="border-left: 5px solid #48c774; height: 100%;">
                <div class="level is-mobile">
                    <div class="level-item has-text-centered">
                        <div>
                            <p class="heading">Saldo a Favor</p>
                            <p class="title is-4 has-text-success-dark"><?php echo number_format($saldo_favor, 2); ?> $</p>
                        </div>
                    </div>
                    <div class="level-item has-text-centered">
                        <i class="fas fa-piggy-bank fa-2x has-text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tabs is-boxed is-centered">
        <ul>
            <li class="is-active" id="tab-deudas" onclick="cambiarPestana('deudas')">
                <a>
                    <span class="icon is-small"><i class="fas fa-file-invoice-dollar" aria-hidden="true"></i></span>
                    <span>Deudas a Pagar</span>
                </a>
            </li>
            <li id="tab-favor" onclick="cambiarPestana('favor')">
                <a class="has-text-success">
                    <span class="icon is-small"><i class="fas fa-hand-holding-usd" aria-hidden="true"></i></span>
                    <span>Saldos a Favor (Anticipos)</span>
                </a>
            </li>
        </ul>
    </div>

    <div id="contenedor-deudas">
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

                            $tag_color = "is-info";
                            if($c['compra_estado_pago'] == "Pendiente") $tag_color = "is-danger";
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
                            <td class="is-size-5 has-text-weight-bold has-text-danger-dark">
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
            <div class="notification is-success is-light has-text-centered">
                <i class="fas fa-check-circle fa-2x mb-2"></i><br>
                <strong>¡Todo al día!</strong> No tienes deudas pendientes con proveedores.
            </div>
        <?php } ?>
    </div>

    <div id="contenedor-favor" style="display: none;">
        <?php
            // Buscamos saldos negativos (a favor)
            $consulta_favor = "SELECT c.*, p.proveedor_nombre FROM compra c 
                INNER JOIN proveedor p ON c.proveedor_id = p.proveedor_id 
                WHERE c.compra_saldo_pendiente < 0 ORDER BY c.compra_id DESC";

            $datos_favor = $insCompra->ejecutarConsulta($consulta_favor);
            if($datos_favor->rowCount() > 0){
                $datos_favor = $datos_favor->fetchAll();
        ?>
        <div class="table-container">
            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                <thead>
                    <tr class="has-background-success-dark">
                        <th class="has-text-centered has-text-white">Código Compra</th>
                        <th class="has-text-centered has-text-white">Proveedor</th>
                        <th class="has-text-centered has-text-white">Fecha Orden</th>
                        <th class="has-text-centered has-text-white">Total Facturado</th>
                        <th class="has-text-centered has-text-white">Monto a Favor</th>
                        <th class="has-text-centered has-text-white">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($datos_favor as $f){ ?>
                        <tr class="has-text-centered">
                            <td><?php echo $f['compra_codigo']; ?></td>
                            <td><?php echo $f['proveedor_nombre']; ?></td>
                            <td><?php echo date("d/m/Y", strtotime($f['compra_fecha'])); ?></td>
                            <td><?php echo number_format($f['compra_total'], 2); ?> $</td>
                            <td class="is-size-5 has-text-weight-bold has-text-success-dark">
                                + <?php echo number_format(abs($f['compra_saldo_pendiente']), 2); ?> $
                            </td>
                            <td>
                                <button class="button is-info is-small is-rounded" onclick="verHistorialAbonos('<?php echo $f['compra_id']; ?>', '<?php echo $f['compra_codigo']; ?>')" title="Ver Anticipos entregados">
                                    <i class="fas fa-history"></i> Ver Pagos
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
            <div class="notification is-warning is-light has-text-centered">
                <i class="fas fa-info-circle fa-2x mb-2"></i><br>
                Actualmente no tienes saldos a favor con ningún proveedor.
            </div>
        <?php } ?>
    </div>

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
    // Script para cambiar entre las pestañas de Deudas y Saldos a Favor
    function cambiarPestana(pestana) {
        document.getElementById('tab-deudas').classList.remove('is-active');
        document.getElementById('tab-favor').classList.remove('is-active');
        document.getElementById('contenedor-deudas').style.display = 'none';
        document.getElementById('contenedor-favor').style.display = 'none';

        if(pestana === 'deudas') {
            document.getElementById('tab-deudas').classList.add('is-active');
            document.getElementById('contenedor-deudas').style.display = 'block';
        } else {
            document.getElementById('tab-favor').classList.add('is-active');
            document.getElementById('contenedor-favor').style.display = 'block';
        }
    }

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