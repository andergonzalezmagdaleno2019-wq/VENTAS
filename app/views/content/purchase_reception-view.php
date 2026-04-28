<div class="container pb-6 pt-6">
    <h1 class="title">Recepción de Mercancía</h1>
    <h2 class="subtitle">Gestión de entradas de inventario</h2>

    <div class="container pb-6">
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
        <div class="notification is-info is-light">
            <i class="fas fa-truck-loading"></i> &nbsp;
            Seleccione una orden de compra para registrar la entrada de productos al almacén.
        </div>

        <div class="table-container">
            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                <thead>
                    <tr class="has-background-link-dark">
                        <th class="has-text-centered has-text-white">Código</th>
                        <th class="has-text-centered has-text-white">Fecha</th>
                        <th class="has-text-centered has-text-white">Proveedor</th>
                        <th class="has-text-centered has-text-white">Estado</th>
                        <th class="has-text-centered has-text-white">Progreso</th>
                        <th class="has-text-centered has-text-white">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="has-text-centered">
                        <td>COM-000001</td>
                        <td>13-03-2026</td>
                        <td>Inversiones PC Master, C.A.</td>
                        <td>
                            <span class="tag is-warning is-light">Parcial</span>
                        </td>
                        <td style="vertical-align: middle;">
                            <progress class="progress is-link is-small" value="70" max="100">70%</progress>
                            <small>7 de 10 unidades</small>
                        </td>
                        <td>
                            <a href="<?php echo APP_URL; ?>purchaseReceptionDetail/1/" class="button is-link is-rounded is-small">
                                <i class="fas fa-boxes"></i> &nbsp; Recibir
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>