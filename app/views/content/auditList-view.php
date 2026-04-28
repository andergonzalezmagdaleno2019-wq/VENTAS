   <div class="container pb-6 pt-6">
    <?php
        /*---------- Bloque de seguridad: Solo Administrador (1) ----------*/
        if($_SESSION['rol'] != 1){
            echo '
            <div class="notification is-danger is-light has-text-centered">
                <i class="fas fa-ban fa-3x"></i><br>
                <h1 class="title">¡Acceso Denegado!</h1>
                <p>No tienes los permisos necesarios para acceder a este módulo de administración.</p>
                <br>
                <a href="'.APP_URL.'dashboard/" class="button is-danger is-rounded">Regresar al Inicio</a>
            </div>';
            exit(); 
        }

        // Si es Admin, el código continúa sin el "else" gigante
        $insCategoria = new app\controllers\categoryController();
        $busqueda = isset($_SESSION[$url[0]]) ? $_SESSION[$url[0]] : "";
    ?>
    </div>
<div class="container is-fluid mb-6">
    <h1 class="title">Auditoría</h1>
    <h2 class="subtitle"><i class="fas fa-history fa-fw"></i> &nbsp; Bitácora de movimientos del sistema</h2>
</div>

<div class="container is-fluid pb-6">
    <?php
        use app\controllers\auditController;
        $insAudit = new auditController();

        if(!isset($_SESSION['auditList'])){
            $_SESSION['auditList'] = "";
        }

        $busqueda = $_SESSION['auditList'];
    ?>

    <div class="columns">
        <div class="column">
            <form class="FormularioAjax" id="form-audit" action="<?php echo APP_URL; ?>app/ajax/buscadorAjax.php" method="POST" autocomplete="off" >
                <input type="hidden" name="modulo_buscador" id="modulo_buscador" value="buscar">
                <input type="hidden" name="modulo_url" value="auditList">
                
                <div class="field is-grouped">
                    <p class="control">
                        <span class="select is-rounded">
                            <select id="filter_modulo" onchange="filtrarAutomatico(this.value)">
                                <option value="">Todos los Módulos</option>
                                <option value="Seguridad" <?php echo ($busqueda=="Seguridad") ? 'selected' : ''; ?>>Seguridad</option>
                                <option value="Productos" <?php echo ($busqueda=="Productos") ? 'selected' : ''; ?>>Productos</option>
                                <option value="Categorías" <?php echo ($busqueda=="Categorías") ? 'selected' : ''; ?>>Categorías</option>
                                <option value="Sistema" <?php echo ($busqueda=="Sistema") ? 'selected' : ''; ?>>Sistema</option>
                                <option value="Proveedores" <?php echo ($busqueda=="Proveedores") ? 'selected' : ''; ?>>Proveedores</option>
                                <option value="Clientes" <?php echo ($busqueda=="Clientes") ? 'selected' : ''; ?>>Clientes</option>
                                <option value="Usuarios" <?php echo ($busqueda=="Usuarios") ? 'selected' : ''; ?>>Usuarios</option>
                                <option value="Compras" <?php echo ($busqueda=="Compras") ? 'selected' : ''; ?>>Compras</option>
                                <option value="Ventas" <?php echo ($busqueda=="Ventas") ? 'selected' : ''; ?>>Ventas</option>
                            </select>
                        </span>
                    </p>

                    <p class="control is-expanded">
                        <input class="input is-rounded" type="text" id="input_buscador" name="txt_buscador" value="<?php echo $busqueda; ?>" placeholder="Módulo, acción o descripción..." maxlength="30" >
                    </p>
                    
                    <p class="control">
                        <button class="button is-info is-rounded" type="submit">Buscar</button>
                    </p>

                    <?php if($busqueda != ""): ?>
                    <p class="control">
                        <button class="button is-danger is-rounded" type="button" onclick="limpiarFiltro()">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </p>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="columns">
        <div class="column">
            <?php
                $pagina = (isset($url[1]) && $url[1]!="") ? $url[1] : 1;
                echo $insAudit->listarBitacoraControlador($pagina, 15, $url[0], $busqueda);
            ?>
        </div>
    </div>
</div>

<script>
    // Función para el Select
    function filtrarAutomatico(valor) {
        const inputBuscador = document.getElementById('input_buscador');
        const moduloBuscador = document.getElementById('modulo_buscador');
        const form = document.getElementById('form-audit');
        
        if(valor === "") {
            limpiarFiltro(); //SE LIMPIA EL FILTRO SI SE SELECCIONA LA OPCIÓN "TODOS LOS MÓDULOS"
            return;
        }

        moduloBuscador.value = "buscar";
        inputBuscador.value = valor;
        dispararSubmit(form);
    }

    // Función para la Papelera
    function limpiarFiltro() {
        const inputBuscador = document.getElementById('input_buscador');
        const moduloBuscador = document.getElementById('modulo_buscador');
        const form = document.getElementById('form-audit');

        moduloBuscador.value = "eliminar";
        inputBuscador.value = "";
        dispararSubmit(form);
    }

    // Función auxiliar para activar el AJAX
    function dispararSubmit(form) {
        setTimeout(() => {
            const event = new Event('submit', {
                bubbles: true,
                cancelable: true
            });
            form.dispatchEvent(event);
        }, 100);
    }
</script>