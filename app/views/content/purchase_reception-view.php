<div class="container pb-6 pt-6">
    <h1 class="title">Recepción de Mercancía</h1>
    <h2 class="subtitle">Gestión de entradas de inventario</h2>

    <div class="container pb-6">
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