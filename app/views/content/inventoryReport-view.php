<div class="container is-fluid mb-6">
    <h1 class="title">Inventario</h1>
    <h2 class="subtitle"><i class="fas fa-file-pdf fa-fw"></i> &nbsp; Generar Reporte de Inventario</h2>
</div>

<div class="container is-fluid pb-6">
    <div class="box">
        <h3 class="title is-5 has-text-centered pb-4">Parámetros del Reporte</h3>
        
        <form id="form-reporte-inventario" autocomplete="off">
            <div class="columns is-multiline">
                
                <div class="column is-3">
                    <div class="field">
                        <label class="label">Categoría</label>
                        <div class="control">
                            <div class="select is-fullwidth is-rounded">
                                <select id="filtro-categoria" name="categoria">
                                    <option value="todas" selected>Todas las categorías</option>
                                    <?php
                                        use app\controllers\categoryController;
                                        $insCategoria = new categoryController();
                                        $categorias = $insCategoria->seleccionarDatos("Normal", "categoria", "*", 0);
                                        while($campos = $categorias->fetch()){
                                            echo '<option value="'.$campos['categoria_id'].'">'.$campos['categoria_nombre'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="column is-3">
                    <div class="field">
                        <label class="label">Estado del Stock</label>
                        <div class="control">
                            <div class="select is-fullwidth is-rounded">
                                <select id="filtro-estado" name="estado">
                                    <option value="todos">Todos los productos</option>
                                    <option value="critico">Stock Crítico (Menos de 10)</option>
                                    <option value="agotado">Agotados (Stock 0)</option>
                                    <option value="disponible">Con Existencia</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="column is-3">
                    <div class="field">
                        <label class="label">Ordenar por</label>
                        <div class="control">
                            <div class="select is-fullwidth is-rounded">
                                <select id="filtro-orden" name="orden">
                                    <option value="nombre_asc">Nombre (A-Z)</option>
                                    <option value="stock_desc">Mayor Stock primero</option>
                                    <option value="stock_asc">Menor Stock primero</option>
                                    <option value="precio_desc">Más caros primero</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="column is-3 has-text-centered mt-4">
                    <div class="buttons is-centered">
                        <button type="button" class="button is-danger is-rounded" id="btn-pdf-inventario">
                            <i class="fas fa-file-pdf"></i> &nbsp; PDF
                        </button>
                        <button type="button" class="button is-success is-rounded" id="btn-excel-inventario">
                            <i class="fas fa-file-excel"></i> &nbsp; Excel
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnPdf = document.getElementById('btn-pdf-inventario');
        const btnExcel = document.getElementById('btn-excel-inventario');

        function generarUrl(ruta) {
            const cat = document.getElementById('filtro-categoria').value;
            const est = document.getElementById('filtro-estado').value;
            const ord = document.getElementById('filtro-orden').value;
            const tasa = parseFloat(localStorage.getItem('tasa_bcv')) || 0; // NUEVO: Extraer la tasa guardada
            return `<?php echo APP_URL; ?>${ruta}?categoria=${cat}&estado=${est}&orden=${ord}&tasa=${tasa}`;
        }

        btnPdf.addEventListener('click', function() {
            window.open(generarUrl('app/pdf/report_inventory.php'), '_blank');
        });

        btnExcel.addEventListener('click', function() {
            window.location.href = generarUrl('app/ajax/export_inventory.php');
        });
    });
</script>