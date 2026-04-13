<div class="container is-fluid mb-6">
    <h1 class="title">Cuentas por Pagar</h1>
    <h2 class="subtitle">Gestión de facturas pendientes y abonos a proveedores</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        use app\controllers\purchaseController;
        $insCompra = new purchaseController();

        /* --- DETECCIÓN DE REDIRECCIÓN AUTOMÁTICA --- */
        $url = explode("/", $_GET['views']);
        $auto_pago_id = (isset($url[1]) && is_numeric($url[1])) ? $insCompra->limpiarCadena($url[1]) : 0;

        /* --- CÁLCULO DE INDICADORES FINANCIEROS --- */
        $q_stats = $insCompra->ejecutarConsulta("
            SELECT 
                SUM(compra_saldo_pendiente) as total_usd,
                SUM(compra_saldo_pendiente * IF(compra_tasa_bcv > 0, compra_tasa_bcv, 1)) as total_bs,
                SUM(IF(compra_fecha_vencimiento < CURRENT_DATE, compra_saldo_pendiente, 0)) as vencido_usd,
                SUM(IF(compra_fecha_vencimiento < CURRENT_DATE, compra_saldo_pendiente * IF(compra_tasa_bcv > 0, compra_tasa_bcv, 1), 0)) as vencido_bs
            FROM compra 
            WHERE compra_saldo_pendiente > 0
        ")->fetch();

        $total_deuda = $q_stats['total_usd'] ?? 0;
        $total_deuda_bs = $q_stats['total_bs'] ?? 0;
        $vencido = $q_stats['vencido_usd'] ?? 0;
        $vencido_bs = $q_stats['vencido_bs'] ?? 0;

        $facturas_pendientes = $insCompra->ejecutarConsulta("SELECT COUNT(compra_id) as total FROM compra WHERE compra_saldo_pendiente > 0")->fetch()['total'] ?? 0;
    ?>

    <div class="columns is-multiline mb-6">
        
        <div class="column is-4">
            <div class="box has-background-info-light" style="border-left: 5px solid #209cee; height: 100%;">
                <div class="columns is-mobile is-vcentered">
                    <div class="column">
                        <p class="heading is-size-6 has-text-info-dark mb-1">Deuda Total</p>
                        <p class="title is-3 mb-2 has-text-info-dark" style="line-height: 1.1;"><?php echo number_format($total_deuda, 2); ?> $</p>
                        <span class="tag is-info has-text-weight-bold is-medium">Bs <?php echo number_format($total_deuda_bs, 2); ?></span>
                    </div>
                    <div class="column is-narrow has-text-right">
                        <i class="fas fa-money-bill-wave fa-3x has-text-info"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="column is-4">
            <div class="box has-background-danger-light" style="border-left: 5px solid #ff3860; height: 100%;">
                <div class="columns is-mobile is-vcentered">
                    <div class="column">
                        <p class="heading is-size-6 has-text-danger-dark mb-1">Monto Vencido</p>
                        <p class="title is-3 mb-2 has-text-danger-dark" style="line-height: 1.1;"><?php echo number_format($vencido, 2); ?> $</p>
                        <span class="tag is-danger has-text-weight-bold is-medium">Bs <?php echo number_format($vencido_bs, 2); ?></span>
                    </div>
                    <div class="column is-narrow has-text-right">
                        <i class="fas fa-exclamation-triangle fa-3x has-text-danger"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="column is-4">
            <div class="box has-background-primary-light" style="border-left: 5px solid #00d1b2; height: 100%;">
                <div class="columns is-mobile is-vcentered">
                    <div class="column">
                        <p class="heading is-size-6 has-text-primary-dark mb-1">Facturas por Pagar</p>
                        <p class="title is-3 mb-2 has-text-primary-dark" style="line-height: 1.1;"><?php echo $facturas_pendientes; ?> Docs</p>
                        <span class="tag is-primary has-text-weight-bold is-medium">Pendientes</span>
                    </div>
                    <div class="column is-narrow has-text-right">
                        <i class="fas fa-file-invoice fa-3x has-text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="columns is-vcentered mb-4">
        <div class="column is-12">
            <div class="field">
                <p class="control has-icons-left">
                    <input class="input is-rounded" type="text" id="buscador_cxp" placeholder="Buscar por Proveedor, Nro de Factura o Código de Orden..." autocomplete="off">
                    <span class="icon is-left has-text-info"><i class="fas fa-search"></i></span>
                </p>
            </div>
        </div>
    </div>

    <div class="box">
        <h3 class="title is-5 has-text-link mb-4"><i class="fas fa-list"></i> Lista de Deudas Activas</h3>
        <?php
    
            $consulta = "SELECT c.*, p.proveedor_nombre, r.recepcion_nota,
                    DATEDIFF(c.compra_fecha_vencimiento, CURRENT_DATE) as dias_restantes
                    FROM compra c 
                    INNER JOIN proveedor p ON c.proveedor_id = p.proveedor_id 
                    LEFT JOIN recepcion r ON c.compra_id = r.compra_id 
                    WHERE c.compra_saldo_pendiente > 0 
                    ORDER BY c.compra_fecha_vencimiento ASC";

            $datos = $insCompra->ejecutarConsulta($consulta);
            if($datos->rowCount() > 0){
                $datos = $datos->fetchAll();
        ?>
        <div class="table-container">
            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth" id="tabla_cxp">
                <thead>
                    <tr class="has-background-link-dark">
                        <th class="has-text-centered has-text-white">Orden Interna</th>
                        <th class="has-text-centered has-text-white">Proveedor</th>
                        <th class="has-text-centered has-text-white">Nro. Factura</th>
                        <th class="has-text-centered has-text-white">Condición</th>
                        <th class="has-text-centered has-text-white">Vencimiento</th>
                        <th class="has-text-centered has-text-white">Saldo Pendiente</th>
                        <th class="has-text-centered has-text-white">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach($datos as $c){
                            // 1. Lógica de días de vencimiento
                            $dias = $c['dias_restantes'];
                            if($dias < 0){ 
                                $clase_color = "has-text-danger has-text-weight-bold"; 
                                $mensaje = "Vencida hace " . abs($dias) . " días"; 
                                $icono = "fa-exclamation-triangle"; 
                            } elseif($dias <= 3){ 
                                $clase_color = "has-text-warning-dark has-text-weight-bold"; 
                                $mensaje = "Vence en $dias días"; 
                                $icono = "fa-clock"; 
                            } else { 
                                $clase_color = "has-text-info"; 
                                $mensaje = "Al día ($dias días)"; 
                                $icono = "fa-check-circle"; 
                            }
                            
                            // 2. Lógica de Factura/Documento
                                $num_fac = (!empty($c['compra_codigo'])) ? $c['compra_codigo'] : "S/N";
                                $doc_color = "is-light";

                                // Si existe una nota interna (Factura oficial), intentamos extraer el número
                                if(!empty($c['compra_nota_interna'])){
                                    if(preg_match('/Nro:\s*([^ \]]+)/', $c['compra_nota_interna'], $match_fac)) {
                                        $num_fac = trim($match_fac[1]);
                                        $doc_color = "is-primary is-light"; 
                                    }
                                }

                            // Nota Interna (Factura Oficial) usando $c
                            if(!empty($c['compra_nota_interna'])){
                                if(preg_match('/Nro:\s*([^ \]]+)/', $c['compra_nota_interna'], $match_fac)) {
                                    $num_fac = trim($match_fac[1]);
                                    $doc_color = "is-primary is-light"; 
                                }
                            }

                            $condicion = $c['compra_condicion'];
                            $cond_color = match($condicion) {
                                'Contado' => 'is-success',
                                'Credito' => 'is-link',
                                'Consignacion' => 'is-dark',
                                default => 'is-light'
                            };
                            $label_cond = ($condicion == "Credito") ? "Crédito" : (($condicion == "Consignacion") ? "Consignación" : $condicion);

                            // 3. Cálculos monetarios
                            $tasa_compra = ($c['compra_tasa_bcv'] > 0) ? $c['compra_tasa_bcv'] : 1;
                            $saldo_bs_historico = $c['compra_saldo_pendiente'] * $tasa_compra;
                    ?>
                        <tr class="has-text-centered fila-deuda">
                            <td class="has-text-centered">
                                <strong><?php echo $c['compra_codigo']; ?></strong>
                            </td>
                            <td class="has-text-weight-bold" style="vertical-align: middle;"><?php echo $c['proveedor_nombre']; ?></td>
                           <td>
                                <?php 
                                    if(!empty($c['recepcion_nota'])){
                                        // Buscamos lo que esté entre "Factura Nro: " y el símbolo "|"
                                        $inicio = strpos($c['recepcion_nota'], "Factura Nro: ") + 13;
                                        $fin = strpos($c['recepcion_nota'], " |");
                                        $nro_factura = substr($c['recepcion_nota'], $inicio, $fin - $inicio);
                                        echo $nro_factura;
                                    } else {
                                        echo '<span class="tag is-light">Sin Registro</span>';
                                    }
                                ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <span class="tag <?php echo $cond_color; ?> is-light has-text-weight-bold">
                                    <?php echo $label_cond; ?>
                                </span>
                            </td>
                                                        
                            <td class="<?php echo $clase_color; ?>" style="vertical-align: middle; line-height: 1.2;">
                                <?php if($condicion == "Crédito" || $condicion == "Credito"): ?>
                                    
                                    <?php 
                                        $frecuencia = (int)$c['compra_frecuencia'];
                                        $cuotas_pagadas = (int)$c['compra_cuotas_pagadas'];

                                        // 1. Obtenemos la fecha de la compra
                                        $fecha_compra = new DateTime($c['compra_fecha']);
                                        $hoy = new DateTime(date("Y-m-d"));

                                        // 2. Calculamos la fecha en la que "debería" empezar el ciclo actual
                                        // Si ha pagado 1 cuota de 10 días, el nuevo ciclo empezó 10 días después de la compra
                                        $dias_a_sumar = $cuotas_pagadas * $frecuencia;
                                        $fecha_inicio_ciclo_actual = clone $fecha_compra;
                                        $fecha_inicio_ciclo_actual->modify("+$dias_a_sumar days");

                                        // 3. La nueva cuenta regresiva es: Frecuencia + (Días que van del ciclo actual)
                                        $intervalo = $fecha_inicio_ciclo_actual->diff($hoy);
                                        $dias_desde_inicio_ciclo = (int)$intervalo->format("%r%a");

                                        // Frecuencia menos los días que han pasado desde que empezó el ciclo actual
                                        $cuenta_regresiva = $frecuencia - $dias_desde_inicio_ciclo;
                                    ?>

                                    <span class="has-text-weight-bold is-size-3" style="display: block;">
                                        <?php 
                                            if($cuenta_regresiva < 0){
                                                echo '<span class="has-text-danger">' . abs($cuenta_regresiva) . ' Días Vencidos</span>';
                                            } elseif($cuenta_regresiva == 0){
                                                echo '<span class="has-text-warning-dark">Paga Hoy</span>';
                                            } else {
                                                echo $cuenta_regresiva . ' Días';
                                            }
                                        ?>
                                    </span>
                                    
                                    <span class="tag is-small is-link is-light has-text-weight-bold" style="font-size: 0.7rem; margin-top: 5px;">
                                        PRÓXIMO ABONO (CADA <?php echo $frecuencia; ?> DÍAS)
                                    </span>

                                <?php else: ?>
                                    <i class="fas <?php echo $icono; ?> is-size-5 mb-1"></i><br>
                                    <span class="has-text-weight-bold">
                                        <?php echo date("d/m/Y", strtotime($c['compra_fecha_vencimiento'])); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            
                            <td style="vertical-align: middle; line-height: 1.4;">
                                <span style="font-size: 1.25rem; font-weight: bold; color: #cc0f35;">
                                    $<?php echo number_format($c['compra_saldo_pendiente'], 2); ?>
                                </span><br>
                                <span style="font-size: 0.9rem; font-weight: bold; color: #209cee;">
                                    Bs <?php echo number_format($saldo_bs_historico, 2); ?>
                                </span>
                            </td>
                            
                            <td style="vertical-align: middle;">
                                <div class="buttons is-centered">
                                    <?php 
                                        // Solo permitir abonos si ya tiene factura registrada
                                        if($c['compra_estado'] == "Facturada" || $c['compra_estado'] == "Completado"): 
                                    ?>
                                        <button class="button is-success is-small is-rounded" 
                                                onclick="abrirModalAbono(
                                                    '<?php echo $c['compra_id']; ?>',
                                                    '<?php echo $c['compra_codigo']; ?>',
                                                    '<?php echo $c['compra_saldo_pendiente']; ?>',
                                                    '<?php echo $tasa_compra; ?>',
                                                    '<?php echo $c['compra_condicion']; ?>',
                                                    '<?php echo $c['compra_total']; ?>',
                                                    '<?php echo $c['compra_cuotas_total'] ?? 1; ?>', 
                                                    '<?php echo $c['compra_cuotas_pagadas'] ?? 0; ?>',
                                                    '<?php echo $c['compra_frecuencia'] ?? 0; ?>'
                                                )">
                                            <i class="fas fa-hand-holding-usd"></i>&nbsp; Abonar
                                        </button>
                                    <?php else: ?>
                                        <span class="tag is-warning is-light has-text-weight-bold" title="Debe registrar la factura oficial para poder realizar abonos">
                                            <i class="fas fa-file-invoice"></i>&nbsp; Pendiente Factura
                                        </span>
                                    <?php endif; ?>
                                    
                                    <button class="button is-info is-small is-rounded" 
                                            onclick="verHistorialAbonos('<?php echo $c['compra_id']; ?>', '<?php echo $c['compra_codigo']; ?>')" 
                                            title="Ver Historial">
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
            <div class="notification is-success is-light has-text-centered p-6"><i class="fas fa-glass-cheers fa-4x mb-4 has-text-success"></i><br><h1 class="title is-4">¡Todo al día!</h1><p>No tienes deudas pendientes con ningún proveedor.</p></div>
        <?php } ?>
    </div>
</div>

<div class="modal" id="modal-abono">
    <div class="modal-background" onclick="cerrarModalAbono()"></div>
    <div class="modal-card">
        <header class="modal-card-head has-background-success">
            <p class="modal-card-title has-text-white"><i class="fas fa-money-bill-wave"></i> Registrar Abono - <span id="txt_codigo_compra"></span></p>
            <button class="delete" aria-label="close" onclick="cerrarModalAbono()"></button>
        </header>
        <section class="modal-card-body">
            <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/compraAjax.php" method="POST" autocomplete="off">
                <input type="hidden" name="modulo_compra" value="registrar_abono">
                <input type="hidden" name="pago_compra_id" id="pago_compra_id">
                
                <div class="columns">
                    <div class="column">
                        <div class="control">
                            <label class="has-text-weight-bold">Deuda Actual ($)</label>
                            <input class="input is-danger has-text-weight-bold" type="text" id="pago_saldo_actual" readonly>
                            <p class="help is-danger has-text-weight-bold is-size-6 mt-1" id="deuda_conversion_bs"></p>
                        </div>
                    </div>
                    <div class="column">
                        <div class="control">
                            <label class="has-text-weight-bold">Monto a Pagar ($)</label>
                            <div class="field has-addons mb-0">
                                <div class="control is-expanded">
                                    <input class="input is-success has-text-weight-bold" type="number" name="pago_monto" id="pago_monto" step="0.01" required oninput="calcularConversionBs()">
                                </div>
                                <div class="control">
                                    <button type="button" class="button is-info" onclick="aplicarPagoTotal()" title="Pagar deuda completa">
                                        <i class="fas fa-check-double"></i>&nbsp; Total
                                    </button>
                                </div>
                            </div>
                            <p class="help is-info has-text-weight-bold is-size-6 mt-1" id="monto_conversion_bs">Equivale a: Bs 0.00</p>
                        </div>
                    </div>
                </div>
                <div class="columns">
                    <div class="column">
                        <div class="control"><label>Método de Pago</label>
                            <div class="select is-fullwidth">
                                <select name="pago_metodo" id="pago_metodo">
                                    <option value="Pago Movil">Pago Móvil</option>
                                    <option value="Transferencia">Transferencia</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="control">
                            <label>Referencia Operación</label>
                            <input 
                                class="input" 
                                type="text" 
                                name="pago_referencia" 
                                id="pago_referencia" 
                                placeholder="Ej: 123456" 
                                maxlength="6" 
                                oninput="this.value = this.value.replace(/[^0-9]/g, ''); validarReferenciaDinamica(this)"
                            >
                        </div>
                    </div>
                </div>
                <p class="has-text-centered mt-4"><button type="submit" class="button is-success is-rounded is-medium shadow-sm"><i class="fas fa-check"></i> &nbsp; Procesar Pago</button></p>
            </form>
        </section>
    </div>
</div>

<div class="modal" id="modal-abono-credito">
    <div class="modal-background" onclick="cerrarModalAbonoCredito()"></div>
    <div class="modal-card">
        <header class="modal-card-head has-background-link">
            <p class="modal-card-title has-text-white"><i class="fas fa-calendar-check"></i> Pago de Cuota - <span id="txt_codigo_credito"></span></p>
            <button class="delete" aria-label="close" onclick="cerrarModalAbonoCredito()"></button>
        </header>
        <section class="modal-card-body">
            <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/compraAjax.php" method="POST" autocomplete="off">
                <input type="hidden" name="modulo_compra" value="registrar_abono">
                <input type="hidden" name="pago_compra_id" id="pago_compra_id_credito">
                <input type="hidden" name="pago_cuotas_total" id="pago_cuotas_total_hidden">
                <input type="hidden" name="pago_cuotas_pagadas" id="pago_cuotas_pagadas_hidden">
                
                <div class="notification is-link is-light has-text-centered mb-4">
                    <div class="columns is-mobile">
                        <div class="column">
                            <p class="heading">Cuota Actual</p>
                            <p class="title is-5" id="info_nro_cuota">--</p>
                        </div>
                        <div class="column">
                            <p class="heading">Frecuencia</p>
                            <p class="title is-5" id="info_frecuencia">-- días</p>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column">
                        <label class="has-text-weight-bold">Deuda Pendiente ($)</label>
                        <input class="input is-danger is-light has-text-weight-bold" type="text" id="pago_saldo_actual_credito" readonly>
                    </div>
                    <div class="column">
                        <label class="has-text-weight-bold">Monto de la Cuota ($)</label>
                        <div class="control">
                            <input class="input is-link has-text-weight-bold" type="number" name="pago_monto" id="pago_monto_credito" step="0.01" required readonly oninput="calcularConversionBsCredito()">
                        </div>
                        <p class="help is-link has-text-weight-bold" id="monto_conversion_bs_credito">Equivale a: Bs 0.00</p>
                    </div>
                </div>

                <div class="columns">
                    <div class="column">
                        <div class="control"><label>Método de Pago</label>
                            <div class="select is-fullwidth">
                                <select name="pago_metodo">
                                    <option value="Pago Movil">Pago Móvil</option>
                                    <option value="Transferencia" selected>Transferencia</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="control">
                            <label class="has-text-weight-bold">Referencia Operación</label>
                            <input class="input" 
                                type="text"
                                placeholder="6 Números" 
                                name="pago_referencia" 
                                id="pago_referencia_credito" 
                                required 
                                minlength="6" 
                                maxlength="6" 
                                pattern="[0-9]{6}"
                                title="La referencia debe tener exactamente 6 números"
                                oninput="this.value = this.value.replace(/[^0-9]/g, ''); if(this.value.length > 6) this.value = this.value.slice(0,6); validarReferenciaDinamica(this)">
                        </div>
                    </div>
                </div>

                <p class="has-text-centered mt-4">
                    <button type="submit" class="button is-link is-rounded is-medium">
                        <i class="fas fa-save"></i> &nbsp; Procesar Cuota
                    </button>
                </p>
            </form>
        </section>
    </div>
</div>

<div class="modal" id="modal-historial">
    <div class="modal-background" onclick="cerrarModalHistorial()"></div>
    <div class="modal-card">
        <header class="modal-card-head has-background-info">
            <p class="modal-card-title has-text-white"><i class="fas fa-history"></i> Historial de Pagos - <span id="historial_codigo"></span></p>
            <button class="delete" aria-label="close" onclick="cerrarModalHistorial()"></button>
        </header>
        <section class="modal-card-body">
            <div id="contenido_historial"></div>
        </section>
    </div>
</div>

<script>
    function validarReferenciaDinamica(input) {
        input.value = input.value.replace(/[^0-9]/g, '');
        
        // Buscamos ambos botones posibles (Abono normal y Cuota crédito)
        const btnAbono = document.getElementById('btn-procesar-abono');
        const btnCredito = document.querySelector('#modal-abono-credito button[type="submit"]');
        
        const esValido = input.value.length === 6;

        if (esValido) {
            input.classList.remove('is-danger');
            input.classList.add('is-success');
        } else {
            input.classList.add('is-danger');
            input.classList.remove('is-success');
        }

        // Habilitar o deshabilitar según cuál modal esté activo
        if(btnAbono) btnAbono.disabled = !esValido;
        if(btnCredito) btnCredito.disabled = !esValido;
    }

    // BÚSQUEDA EN TIEMPO REAL
    document.addEventListener('DOMContentLoaded', () => {
        const buscador = document.getElementById('buscador_cxp');
        if(buscador){
            buscador.addEventListener('keyup', function() {
                let textoBusqueda = this.value.toLowerCase();
                let filas = document.querySelectorAll('.fila-deuda');
                
                filas.forEach(fila => {
                    let contenidoFila = fila.textContent.toLowerCase();
                    if(contenidoFila.includes(textoBusqueda)) {
                        fila.style.display = '';
                    } else {
                        fila.style.display = 'none';
                    }
                });
            });
        }
    });

    const selectMetodo = document.querySelector('#pago_metodo');
    const inputReferencia = document.querySelector('#pago_referencia');

    if(selectMetodo && inputReferencia){
        inputReferencia.readOnly = false;
        inputReferencia.classList.remove('is-static');
        inputReferencia.placeholder = "Nro de operación (Solo números)";

        selectMetodo.addEventListener('change', function() {
            inputReferencia.value = "";
            inputReferencia.readOnly = false;
            inputReferencia.classList.remove('is-static');
            inputReferencia.placeholder = "Referencia de " + this.value;
            inputReferencia.focus(); 
        });
    }

    let tasaBcvModal = 1;

    function abrirModalAbono(id, codigo, saldo, tasa, condicion, total, total_cuotas, pagadas, frecuencia) {
    // 1. Configuración de Tasa
    let tasaGlobal = parseFloat(localStorage.getItem('tasa_bcv')) || 0;
    tasaBcvModal = (tasaGlobal > 0) ? tasaGlobal : (parseFloat(tasa) || 1);

    // 2. Normalización de datos
    let n_cuotas = parseInt(total_cuotas) || 1;
    let cuotasYaPagadas = parseInt(pagadas) || 0;
    let totalFloat = parseFloat(total) || 0;
    let saldoFloat = parseFloat(saldo) || 0;
    let diasFrecuencia = frecuencia || 0; // Captura el 9no parámetro

    let condicionTrim = condicion.trim();

    if (condicionTrim === "Crédito" || condicionTrim === "Credito") {
        const modalCredito = document.getElementById('modal-abono-credito');
        
        if(modalCredito) {
            // Asignación de IDs de cabecera
            document.getElementById('pago_compra_id_credito').value = id;
            document.getElementById('txt_codigo_credito').innerText = codigo;
            document.getElementById('pago_saldo_actual_credito').value = saldoFloat.toFixed(2);
            
            // ASIGNACIÓN DE FRECUENCIA 
            const txtFrecuencia = document.getElementById('info_frecuencia');
            if(txtFrecuencia) {
                txtFrecuencia.innerText = diasFrecuencia + " días";
            }

            // ASIGNACIÓN DE CUOTAS
            const txtCuota = document.getElementById('info_nro_cuota');
            if(txtCuota) {
                let proxima = cuotasYaPagadas + 1;
                if (proxima > n_cuotas) proxima = n_cuotas;
                txtCuota.innerText = proxima + " de " + n_cuotas;
                
                // Inputs ocultos para el formulario
                if(document.getElementById('pago_cuotas_pagadas_hidden')) {
                    document.getElementById('pago_cuotas_pagadas_hidden').value = proxima;
                }
            }
            
            if(document.getElementById('pago_cuotas_total_hidden')) {
                document.getElementById('pago_cuotas_total_hidden').value = n_cuotas;
            }

            // Cálculo de monto sugerido
            let montoCuota = (totalFloat / n_cuotas).toFixed(2);
            if(parseFloat(montoCuota) > saldoFloat) montoCuota = saldoFloat.toFixed(2);

            document.getElementById('pago_monto_credito').value = montoCuota;
            document.getElementById('pago_monto_credito').max = saldoFloat; 

            // Actualizar conversión a Bs
            if (typeof calcularConversionBsCredito === "function") {
                calcularConversionBsCredito();
            }
            
            modalCredito.classList.add('is-active');
        }
    } else {
        // Lógica para Contado
        const modalNormal = document.getElementById('modal-abono');
        if(modalNormal){
            document.getElementById('pago_compra_id').value = id;
            document.getElementById('txt_codigo_compra').innerText = codigo;
            document.getElementById('pago_saldo_actual').value = saldoFloat.toFixed(2);
            modalNormal.classList.add('is-active');
            calcularConversionBs();
        }
    }
}
    function aplicarPagoTotal() {
        let maxMonto = document.getElementById('pago_monto').max;
        document.getElementById('pago_monto').value = maxMonto;
        calcularConversionBs();
    }

    function calcularConversionBs() {
        let inputMonto = document.getElementById('pago_monto');
        if(!inputMonto) return;
        let monto = parseFloat(inputMonto.value) || 0;
        let maxMonto = parseFloat(inputMonto.max) || 0;

        if(monto > maxMonto){
            inputMonto.value = maxMonto;
            monto = maxMonto;
        }

        let equivalenciaBs = (monto * tasaBcvModal).toFixed(2);
        let helpText = document.getElementById('monto_conversion_bs');
        if(helpText) helpText.innerText = `Equivale a: Bs ${equivalenciaBs}`;
    }

    function calcularConversionBsCredito() {
        let inputMonto = document.getElementById('pago_monto_credito');
        if(!inputMonto) return;
        let monto = parseFloat(inputMonto.value) || 0;
        let maxMonto = parseFloat(inputMonto.max) || 0;

        if(monto > maxMonto){
            inputMonto.value = maxMonto;
            monto = maxMonto;
        }

        let equivalenciaBs = (monto * tasaBcvModal).toFixed(2);
        let helpText = document.getElementById('monto_conversion_bs_credito');
        if(helpText) helpText.innerText = `Equivale a: Bs ${equivalenciaBs}`;
    }

    function cerrarModalAbono() { document.getElementById('modal-abono').classList.remove('is-active'); }
    function cerrarModalAbonoCredito() {document.getElementById('modal-abono-credito').classList.remove('is-active'); }

    function verHistorialAbonos(id, codigo) {
        document.getElementById('historial_codigo').innerText = codigo;
        document.getElementById('contenido_historial').innerHTML = '<div class="has-text-centered my-6"><i class="fas fa-sync fa-spin fa-3x has-text-info"></i><p class="mt-3">Buscando comprobantes...</p></div>';
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
<?php 
    /* --- AUTO-APERTURA DE MODAL DESDE OTRA PANTALLA --- */
    if($auto_pago_id > 0){

        $check_deuda = $insCompra->ejecutarConsulta("SELECT compra_id, compra_codigo, 
            compra_saldo_pendiente, compra_tasa_bcv, compra_condicion, 
            compra_total, compra_cuotas_total, compra_frecuencia 
            FROM compra WHERE compra_id='$auto_pago_id' AND compra_saldo_pendiente > 0");

        if($check_deuda->rowCount() > 0){
            $datos_auto = $check_deuda->fetch();
            $t_bcv = ($datos_auto['compra_tasa_bcv'] > 0) ? $datos_auto['compra_tasa_bcv'] : 1;
            
            echo "<script>
                document.addEventListener('DOMContentLoaded', () => {
                    setTimeout(() => {
                        abrirModalAbono(
                            '".$datos_auto['compra_id']."', 
                            '".$datos_auto['compra_codigo']."', 
                            '".$datos_auto['compra_saldo_pendiente']."', 
                            '".$t_bcv."',
                            '".$datos_auto['compra_condicion']."',
                            '".$datos_auto['compra_total']."',
                            '".$datos_auto['compra_cuotas_total']."',
                            '".$datos_auto['compra_frecuencia']."'
                        );
                    }, 400); 
                });
            </script>";
        } else {
            /* --- CORRECCIÓN: DETECTAR SI ES PAGADA O ANULADA --- */
            $check_estado = $insCompra->ejecutarConsulta("SELECT compra_estado FROM compra WHERE compra_id='$auto_pago_id'");
            $info = $check_estado->fetch();

            if($info['compra_estado'] == "Anulada"){
                $titulo = "Orden Anulada";
                $texto = "No se pueden realizar abonos porque esta compra ha sido anulada.";
                $icono = "error";
            } else {
                $titulo = "Orden Pagada";
                $texto = "Esta compra ya ha sido pagada en su totalidad.";
                $icono = "success";
            }

            echo "<script>
                document.addEventListener('DOMContentLoaded', () => {
                    Swal.fire({
                        icon: '$icono',
                        title: '$titulo',
                        text: '$texto',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#3085d6'
                    });
                });
            </script>";
        }
    }
?>