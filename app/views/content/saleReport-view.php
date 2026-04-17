<div class="container is-fluid mb-6">
    <h1 class="title">Ventas</h1>
    <h2 class="subtitle"><i class="fas fa-chart-bar fa-fw"></i> &nbsp; Reporte de Ventas por Rango de Fechas</h2>
</div>

<div class="container pb-6 pt-6">
    <div class="box">
        <h3 class="title is-5 has-text-centered">Seleccione los criterios del reporte</h3>
        <hr>
        <form method="GET" target="_blank" autocomplete="off">

            <div class="columns is-centered mb-4">
                <div class="column is-12 has-text-centered">
                    <button type="button" class="button is-link is-rounded shadow-sm" onclick="setFechaHoy()">
                        <i class="fas fa-calendar-day"></i> &nbsp; HOY
                    </button>
                </div>
            </div>
            
            <div class="columns is-centered">
                <div class="column is-4">
                    <div class="control">
                        <label class="has-text-weight-bold">Fecha de Inicio:</label>
                        <input class="input is-rounded mt-2" type="date" name="fecha_inicio" id="fecha_inicio" required>
                    </div>
                </div>
                <div class="column is-4">
                    <div class="control">
                        <label class="has-text-weight-bold">Fecha de Fin:</label>
                        <input class="input is-rounded mt-2" type="date" name="fecha_fin" id="fecha_fin" required>
                    </div>
                </div>
            </div>

            <div class="columns is-centered">
                <div class="column is-4">
                    <div class="control">
                        <label class="has-text-weight-bold">Filtrar por Vendedor:</label>
                        <div class="select is-rounded is-fullwidth mt-2">
                            <select name="reporte_vendedor">
                               <?php
    // 1. Capturamos el ID del usuario actual desde la sesión
    $id_actual = $_SESSION['id'];
    
    // 2. Consultamos qué rol tiene este usuario en la base de datos
    $check_rol = $insLogin->ejecutarConsulta("SELECT rol_id FROM usuario WHERE usuario_id='$id_actual'")->fetch();
    $rol_usuario = $check_rol['rol_id'];

    // 3. Lógica de visualización según el Rol
    if($rol_usuario == 1 || $rol_usuario == 3){ 
        
        // Si es ADMIN (1) o SUPERVISOR (3):
        echo '<option value="all">Todos los vendedores</option>';
        
        $consulta_sql = "SELECT usuario_id, usuario_usuario 
                         FROM usuario 
                         WHERE usuario_id != 1 OR usuario_id = '$id_actual'
                         ORDER BY usuario_usuario ASC";
                         
    } else {
        
        // Si es CAJERO / VENDEDOR (Rol 2):
        $consulta_sql = "SELECT usuario_id, usuario_usuario 
                         FROM usuario 
                         WHERE usuario_id = '$id_actual'";
    }

    // 4. Ejecutamos la consulta correspondiente y pintamos las opciones
    $vendedores = $insLogin->ejecutarConsulta($consulta_sql);
    
    while($v = $vendedores->fetch()){
        // Guardamos el nombre en una variable temporal
        $nombre_vendedor = $v['usuario_usuario'];
        
        // Si el usuario listado es el mismo que está conectado Y tiene rol gerencial (Admin o Supervisor)
        if(($rol_usuario == 1 || $rol_usuario == 3) && $v['usuario_id'] == $id_actual){
            $nombre_vendedor .= " (YO)";
        }
        
        // Imprimimos la opción con el nombre modificado (si aplicó)
        echo '<option value="'.$v['usuario_id'].'">'.$nombre_vendedor.'</option>';
    }
?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="column is-4">
                    <div class="control">
                        <label class="has-text-weight-bold">Método de Pago:</label>
                        <div class="select is-rounded is-fullwidth mt-2">
                            <select name="reporte_pago">
                                <option value="all">Todos los métodos</option>
                                <option value="Pago Móvil">Pago Móvil</option>
                                <option value="Transferencia">Transferencia</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="columns is-centered mt-5">
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
<script>
    function setFechaHoy() {
        // Sacamos la fecha actual exacta
        const fecha = new Date();
        const anio = fecha.getFullYear();
        const mes = String(fecha.getMonth() + 1).padStart(2, '0');
        const dia = String(fecha.getDate()).padStart(2, '0');
        const hoy = `${anio}-${mes}-${dia}`;

        // Reemplaza 'fecha_inicio' y 'fecha_fin' con los IDs de tus inputs
        const inputInicio = document.getElementById('fecha_inicio');
        const inputFin = document.getElementById('fecha_fin');

        if(inputInicio) inputInicio.value = hoy;
        if(inputFin) inputFin.value = hoy;
    }
</script>