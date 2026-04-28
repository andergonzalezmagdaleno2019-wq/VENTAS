<div class="container is-fluid mb-6">
    <h1 class="title">Compras</h1>
    <h2 class="subtitle"><i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; Reporte de Compras por Fecha</h2>
</div>

<div class="container is-fluid pb-6">
    <?php
        /*---------- Bloque de seguridad: Admin (1) y Supervisor (3) ----------*/
        if($_SESSION['rol'] != 1 && $_SESSION['rol'] != 3){
            echo '
            <div class="notification is-danger is-light has-text-centered">
                <br>
                <i class="fas fa-ban fa-3x"></i><br>
                <h1 class="title mt-4">¡Acceso Denegado!</h1>
                <p>No tienes los permisos necesarios para acceder a este módulo.</p>
                <br>
                <a href="'.APP_URL.'dashboard/" class="button is-danger is-rounded">Regresar al Inicio</a>
                <br><br>
            </div>';
            exit(); 
        }
    ?>
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