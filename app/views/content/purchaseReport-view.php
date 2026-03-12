<div class="container is-fluid mb-6">
    <h1 class="title">Compras</h1>
    <h2 class="subtitle"><i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; Reporte de Compras por Fecha</h2>
</div>

<div class="container is-fluid pb-6">
    <div class="box">
        <h3 class="title is-5 has-text-centered pb-4">Seleccione el rango de fechas para el reporte</h3>
        
        <form action="<?php echo APP_URL; ?>app/pdf/report_purchases.php" method="GET" target="_blank" autocomplete="off">
            <div class="columns is-centered is-vcentered">
                
                <div class="column is-3">
                    <div class="field">
                        <label class="label">Fecha Inicial</label>
                        <div class="control has-icons-left">
                            <input class="input is-rounded" type="date" name="fecha_inicio" required value="<?php echo date("Y-m-d"); ?>">
                            <span class="icon is-small is-left"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                    </div>
                </div>

                <div class="column is-3">
                    <div class="field">
                        <label class="label">Fecha Final</label>
                        <div class="control has-icons-left">
                            <input class="input is-rounded" type="date" name="fecha_fin" required value="<?php echo date("Y-m-d"); ?>">
                            <span class="icon is-small is-left"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                    </div>
                </div>

                <div class="column is-3 has-text-centered mt-4">
                    <button type="submit" class="button is-info is-rounded is-medium">
                        <i class="fas fa-file-pdf"></i> &nbsp; Generar Reporte
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>