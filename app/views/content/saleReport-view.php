<div class="container is-fluid mb-6">
	<h1 class="title">Ventas</h1>
	<h2 class="subtitle"><i class="fas fa-chart-bar fa-fw"></i> &nbsp; Reporte de Ventas por Rango de Fechas</h2>
</div>

<div class="container pb-6 pt-6">
    <div class="box">
        <h3 class="title is-5 has-text-centered">Seleccione el período a consultar</h3>
        <hr>
        <form method="GET" target="_blank" autocomplete="off">
            <div class="columns is-centered">
                <div class="column is-4">
                    <div class="control">
                        <label class="has-text-weight-bold">Fecha de Inicio:</label>
                        <input class="input is-rounded mt-2" type="date" name="fecha_inicio" required>
                    </div>
                </div>
                <div class="column is-4">
                    <div class="control">
                        <label class="has-text-weight-bold">Fecha de Fin:</label>
                        <input class="input is-rounded mt-2" type="date" name="fecha_fin" required>
                    </div>
                </div>
            </div>
            
            <div class="columns is-centered mt-4">
                <div class="column is-3 has-text-centered">
                    <button type="submit" class="button is-info is-rounded is-fullwidth" formaction="<?php echo APP_URL; ?>app/pdf/report_sales.php">
                        <i class="fas fa-file-pdf"></i> &nbsp; Generar PDF
                    </button>
                </div>
                <div class="column is-3 has-text-centered">
                    <button type="submit" class="button is-success is-rounded is-fullwidth" formaction="<?php echo APP_URL; ?>app/ajax/export_sales.php">
                        <i class="fas fa-file-excel"></i> &nbsp; Exportar a Excel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>