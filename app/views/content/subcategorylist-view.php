<div class="container is-fluid mb-6">
    <h1 class="title">Subcategorías</h1>
    <h2 class="subtitle"><i class="fas fa-list fa-fw"></i> &nbsp; Lista de subcategorías</h2>
</div>

<div class="container pb-6 pt-6">
    <div class="table-container">
        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
            <thead>
                <tr class="has-text-centered">
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Ubicación</th>
                    <th>Categoría Principal (Padre)</th>
                    <th colspan="2">Opciones</th>
                </tr>
            </thead>
<tbody>
    <?php
        use app\controllers\categoryController;
        $insCategory = new categoryController();

        // Pedimos todos los datos, ya que el controlador parece ignorar el WHERE
        $datos = $insCategory->seleccionarDatos("Normal", "categoria", "*", "");
        
        $contador = 1;
        if($datos && $datos->rowCount() > 0){
            while($rows = $datos->fetch()){

                // FILTRO MANUAL: Si categoria_padre_id es NULL o vacío, 
                // saltamos a la siguiente iteración (no mostramos las principales)
                if($rows['categoria_padre_id'] == NULL || $rows['categoria_padre_id'] == "" || $rows['categoria_padre_id'] == "0"){
                    continue; 
                }

                $id_padre = $rows['categoria_padre_id'];
                $datos_padre = $insCategory->seleccionarDatos("Normal", "categoria", "categoria_nombre", "WHERE categoria_id='$id_padre'");
                $padre = $datos_padre->fetch();

                echo '
                <tr class="has-text-centered" >
                    <td>'.$contador.'</td>
                    <td>'.$rows['categoria_nombre'].'</td>
                    <td>'.$rows['categoria_ubicacion'].'</td>
                    <td><strong>'.($padre['categoria_nombre'] ?? "Sin Padre").'</strong></td>
                    
                    <td>
                        <a href="'.APP_URL.'productCategory/'.$rows['categoria_id'].'/" class="button is-info is-rounded is-small" title="Ver productos">
                            <i class="fas fa-boxes fa-fw"></i>
                        </a>
                    </td>

                    <td>
                        <a href="'.APP_URL.'subcategoryUpdate/'.$rows['categoria_id'].'/" class="button is-success is-rounded is-small" title="Actualizar">
                            <i class="fas fa-sync fa-fw"></i>
                        </a>
                    </td>
                    <td>
                        <form class="FormularioAjax" action="'.APP_URL.'app/ajax/categoriaAjax.php" method="POST" autocomplete="off" >
                            <input type="hidden" name="modulo_categoria" value="eliminar">
                            <input type="hidden" name="categoria_id" value="'.$rows['categoria_id'].'">
                            <button type="submit" class="button is-danger is-rounded is-small" title="Eliminar">
                                <i class="far fa-trash-alt fa-fw"></i>
                            </button>
                        </form>
                    </td>
                </tr>';
                $contador++;
            }
        } else {
            echo '<tr class="has-text-centered"><td colspan="6">No hay subcategorías registradas</td></tr>';
        }
    ?>
</tbody>
        </table>
    </div>
</div>