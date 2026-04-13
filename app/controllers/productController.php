<?php

namespace app\controllers;

use app\models\mainModel;

class productController extends mainModel
{

    /*----------  Controlador registrar producto  ----------*/
    public function registrarProductoControlador()
    {
        $codigo = $this->limpiarCadena($_POST['producto_codigo']);
        $nombre = $this->limpiarCadena($_POST['producto_nombre']);
        $marca = $this->limpiarCadena($_POST['producto_marca']);
        $modelo = $this->limpiarCadena($_POST['producto_modelo']);

        //Capturar el proveedor
        $proveedores = isset($_POST['producto_proveedores']) ? $_POST['producto_proveedores'] : [];

        // FORZAMOS LOS VALORES FINANCIEROS A CERO (Se llenarán por Compras)
        $precio = "0.00";
        $costo = "0.00";
        $stock = 0; 

        $stock_min = $this->limpiarCadena($_POST['producto_stock_min']);
        $stock_max = $this->limpiarCadena($_POST['producto_stock_max']);

        $categoria = $this->limpiarCadena($_POST['producto_categoria']);
        $unidad = $this->limpiarCadena($_POST['producto_unidad']);

        // VALIDACIÓN OBLIGATORIA DE PROVEEDORES
        if (empty($proveedores)) {
            $alerta = [
                "tipo" => "simple", 
                "titulo" => "Falta Proveedor", 
                "texto" => "Debe asignar al menos un proveedor para poder registrar este producto.", 
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Validando Código de Barras (Solo números, máx 13) #
        if ($this->verificarDatos("[0-9]{1,13}", $codigo)) {
            $alerta = ["tipo" => "simple", "titulo" => "Error en Código", "texto" => "El código de barras solo permite números (máx. 13)", "icono" => "error"];
            return json_encode($alerta);
            exit();
        }

		# Validando que el nombre no sea solo números #
        if (!preg_match("/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/", $nombre)) {
            $alerta = ["tipo" => "simple", "titulo" => "Nombre Inválido", "texto" => "El nombre del producto debe contener letras (no puede ser solo números)", "icono" => "error"];
            return json_encode($alerta);
            exit();
        }

        if ($codigo == "" || $nombre == "" || $categoria == "" || $unidad == "" || $stock_min ==  "" || $stock_max == "") {
            $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "No has llenado todos los campos que son obligatorios", "icono" => "error"];
            return json_encode($alerta);
            exit();
        }

        /*---------- VALIDACIONES LOGICAS DE STOCK ----------*/
    
        // Validación para Stock Máximo
        if ((int)$stock_max <= 0) {
            $alerta = [
                "tipo" => "simple", 
                "titulo" => "Error en Límite de Stock", 
                "texto" => "El stock máximo debe ser mayor a cero para poder registrar el producto.", 
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        // Validación para Stock Mínimo 
        if ((int)$stock_min <= 0) {
            $alerta = [
                "tipo" => "simple", 
                "titulo" => "Stock Mínimo Insuficiente", 
                "texto" => "Debe establecer un stock mínimo (alerta) mayor a cero para el control de inventario.", 
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        // Validación de coherencia
        if ((int)$stock_min >= (int)$stock_max) {
            $alerta = [
                "tipo" => "simple", 
                "titulo" => "Incoherencia de Valores", 
                "texto" => "El stock mínimo no puede superar o ser igual al límite máximo permitido.", 
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        $check_codigo = $this->ejecutarConsulta("SELECT producto_codigo FROM producto WHERE producto_codigo='$codigo'");
        if ($check_codigo->rowCount() > 0) {
            $alerta = ["tipo" => "simple", "titulo" => "Código Duplicado", "texto" => "El código ya existe asignado a otro producto.", "icono" => "error"];
            return json_encode($alerta);
            exit();
        }

        /*---------- PROCESAMIENTO DE IMAGEN ----------*/
        $img_dir = "../views/productos/";
        $foto = "";
        if (isset($_FILES['producto_foto']) && $_FILES['producto_foto']['name'] != "" && $_FILES['producto_foto']['size'] > 0) {
            if (!file_exists($img_dir)) { mkdir($img_dir, 0777); }
            if (mime_content_type($_FILES['producto_foto']['tmp_name']) != "image/jpeg" && mime_content_type($_FILES['producto_foto']['tmp_name']) != "image/png") {
                $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Formato de imagen no permitido", "icono" => "error"];
                return json_encode($alerta);
                exit();
            }
            $foto = str_ireplace(" ", "_", $nombre) . "_" . rand(0, 100);
            switch (mime_content_type($_FILES['producto_foto']['tmp_name'])) {
                case 'image/jpeg': $foto = $foto . ".jpg"; break;
                case 'image/png': $foto = $foto . ".png"; break;
            }
            chmod($img_dir, 0777);
            move_uploaded_file($_FILES['producto_foto']['tmp_name'], $img_dir . $foto);
        }

        $producto_datos_reg = [
            ["campo_nombre" => "producto_codigo", "campo_marcador" => ":Codigo", "campo_valor" => $codigo],
            ["campo_nombre" => "producto_nombre", "campo_marcador" => ":Nombre", "campo_valor" => $nombre],
            ["campo_nombre" => "producto_marca", "campo_marcador" => ":Marca", "campo_valor" => $marca],
            ["campo_nombre" => "producto_modelo", "campo_marcador" => ":Modelo", "campo_valor" => $modelo],
            ["campo_nombre" => "producto_precio", "campo_marcador" => ":Precio", "campo_valor" => $precio],
            ["campo_nombre" => "producto_costo", "campo_marcador" => ":Costo", "campo_valor" => $costo],
            ["campo_nombre" => "producto_stock", "campo_marcador" => ":Stock", "campo_valor" => $stock],
            ["campo_nombre" => "producto_stock_min", "campo_marcador" => ":StockMin", "campo_valor" => $stock_min],
            ["campo_nombre" => "producto_stock_max", "campo_marcador" => ":StockMax", "campo_valor" => $stock_max],
            ["campo_nombre" => "producto_estado", "campo_marcador" => ":Estado", "campo_valor" => "Activo"],
            ["campo_nombre" => "producto_foto", "campo_marcador" => ":Foto", "campo_valor" => $foto],
            ["campo_nombre" => "categoria_id", "campo_marcador" => ":Categoria", "campo_valor" => $categoria],
            ["campo_nombre" => "producto_unidad", "campo_marcador" => ":Unidad", "campo_valor" => $unidad]
        ];

        $registrar_producto = $this->guardarDatos("producto", $producto_datos_reg);

        if ($registrar_producto->rowCount() == 1) {
            
            // REGISTRO DE RELACIÓN PRODUCTO-PROVEEDOR
            // Primero obtenemos el ID del producto que acabamos de registrar
            $check_id = $this->ejecutarConsulta("SELECT producto_id FROM producto WHERE producto_codigo='$codigo' ORDER BY producto_id DESC LIMIT 1");
            $prod_id = $check_id->fetch()['producto_id'];

            foreach ($proveedores as $prov_id) {
                $relacion_datos = [
                    ["campo_nombre" => "producto_id", "campo_marcador" => ":ProdID", "campo_valor" => $prod_id],
                    ["campo_nombre" => "proveedor_id", "campo_marcador" => ":ProvID", "campo_valor" => $prov_id]
                ];
                $this->guardarDatos("producto_proveedor", $relacion_datos);
            }

            $this->guardarBitacora("Productos", "Registro", "Se registró el producto: " . $nombre . " con sus proveedores correspondientes.");
            
            $alerta = ["tipo" => "redireccionar", "titulo" => "Catálogo Actualizado", "texto" => "Producto registrado y vinculado a sus proveedores correctamente.", "icono" => "success", "url" => APP_URL."productList/"];
        } else {
            if (is_file($img_dir . $foto)) { chmod($img_dir . $foto, 0777); unlink($img_dir . $foto); }
            $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "No se pudo registrar en la base de datos.", "icono" => "error"];
        }
        return json_encode($alerta);
    }

    /*----------  Controlador listar productos ----------*/
    public function listarProductoControlador($pagina, $registros, $url, $categoria_id, $busqueda)
    {
        $pagina = $this->limpiarCadena($pagina);
        $registros = $this->limpiarCadena($registros);
        $url = $this->limpiarCadena($url);
        $url = APP_URL . $url . "/";
        $categoria_id = $this->limpiarCadena($categoria_id);
        $busqueda = $this->limpiarCadena($busqueda);
        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        $orden_actual = isset($_SESSION['orden_producto']) ? $_SESSION['orden_producto'] : "nombre_asc";
        $orden_sql = "producto.producto_nombre ASC";
        if ($orden_actual == "menor_stock") {
            $orden_sql = "producto.producto_stock ASC, producto.producto_nombre ASC";
        } elseif ($orden_actual == "mayor_stock") {
            $orden_sql = "producto.producto_stock DESC, producto.producto_nombre ASC";
        }

        $campos = "producto.producto_id,producto.producto_codigo,producto.producto_nombre,producto.producto_marca,producto.producto_modelo,producto.producto_precio,producto.producto_costo,producto.producto_stock,producto.producto_stock_min,producto.producto_stock_max,producto.producto_estado,producto.producto_foto,producto.producto_unidad,categoria.categoria_nombre AS subcategoria_nombre, padre.categoria_nombre AS categoria_padre_nombre";

        $join_sql = " FROM producto 
              INNER JOIN categoria ON producto.categoria_id=categoria.categoria_id 
              LEFT JOIN categoria AS padre ON categoria.categoria_padre_id=padre.categoria_id ";

        if (isset($busqueda) && $busqueda != "") {
            $consulta_datos = "SELECT $campos $join_sql WHERE producto_codigo LIKE '%$busqueda%' OR producto_nombre LIKE '%$busqueda%' ORDER BY $orden_sql LIMIT $inicio,$registros";
            $consulta_total = "SELECT COUNT(producto_id) FROM producto WHERE producto_codigo LIKE '%$busqueda%' OR producto_nombre LIKE '%$busqueda%'";
        } elseif ($categoria_id > 0) {
            $consulta_datos = "SELECT $campos $join_sql WHERE producto.categoria_id='$categoria_id' ORDER BY $orden_sql LIMIT $inicio,$registros";
            $consulta_total = "SELECT COUNT(producto_id) FROM producto WHERE categoria_id='$categoria_id'";
        } else {
            $consulta_datos = "SELECT $campos $join_sql ORDER BY $orden_sql LIMIT $inicio,$registros";
            $consulta_total = "SELECT COUNT(producto_id) FROM producto";
        }

        $datos = $this->ejecutarConsulta($consulta_datos);
        $datos = $datos->fetchAll();
        $total = $this->ejecutarConsulta($consulta_total);
        $total = (int) $total->fetchColumn();
        $numeroPaginas = ceil($total / $registros);

        $tabla .= '<div class="table-container">
                <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                    <thead>
                        <tr>
                            <th class="has-text-centered">#</th>
                            <th class="has-text-centered">Código</th>
                            <th class="has-text-centered">Nombre</th>
                            <th class="has-text-centered">Marca/Modelo</th>
                            <th class="has-text-centered">Categoría</th>
                            <th class="has-text-centered">Costo</th>
                            <th class="has-text-centered">Precio de Venta</th>
                            <th class="has-text-centered">Stock</th>
                            <th class="has-text-centered">Estado</th>
                            <th class="has-text-centered">Foto</th>
                            <th class="has-text-centered" colspan="3">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>';

        if ($total >= 1 && $pagina <= $numeroPaginas) {
            $contador = $inicio + 1;
            $pag_inicio = $inicio + 1;
            foreach ($datos as $rows) {

                $stock_alerta = "";
                if ($rows['producto_estado'] == 'Activo') {
                    if ($rows['producto_stock'] <= $rows['producto_stock_min']) {
                        $stock_alerta = 'has-background-danger-light has-text-danger-dark font-weight-bold';
                    } elseif ($rows['producto_stock'] >= $rows['producto_stock_max']) {
                        $stock_alerta = 'has-background-warning-light has-text-warning-dark';
                    }
                }

                if ($rows['producto_estado'] == 'Activo') {
                    $estado_tag = '<span class="tag is-success is-light">Activo</span>';
                    $btn_estado = 'warning';
                    $icon_estado = 'toggle-off';
                    $txt_estado = 'Inactivo';
                } else {
                    $estado_tag = '<span class="tag is-danger is-light">Inactivo</span>';
                    $btn_estado = 'success';
                    $icon_estado = 'toggle-on';
                    $txt_estado = 'Activo';
                }

                $tabla .= '
                        <tr class="has-text-centered ' . $stock_alerta . '">
                            <td>' . $contador . '</td>
                            <td>' . $rows['producto_codigo'] . '</td>
                            <td>' . $rows['producto_nombre'] . '</td>
                            <td>' . $rows['producto_marca'] . ' ' . $rows['producto_modelo'] . '</td>
                            <td>
                                <span class="has-text-weight-bold">' . ($rows['categoria_padre_nombre'] ?? 'Sin Categoría') . '</span><br>
                                <span class="is-size-7 has-text-grey"><i class="fas fa-level-up-alt fa-rotate-90"></i> ' . $rows['subcategoria_nombre'] . '</span>
                            </td>
                            
                            <td>
                                <span>$' . $rows['producto_costo'] . '</span><br>
                                <span class="is-size-7 has-text-grey precio-bcv" data-usd="' . $rows['producto_costo'] . '">Calculando Bs...</span>
                            </td>

                            <td>
                                <strong>$' . $rows['producto_precio'] . '</strong><br>
                                <span class="is-size-7 has-text-link has-text-weight-bold precio-bcv" data-usd="' . $rows['producto_precio'] . '">Calculando Bs...</span>
                            </td>

                            <td class="has-text-weight-bold">' . $rows['producto_stock'] . ' ' . $rows['producto_unidad'] . '</td>
                            <td>' . $estado_tag . '</td>
                            <td>
                                <a href="' . APP_URL . 'productPhoto/' . $rows['producto_id'] . '/" class="button is-info is-rounded is-small" title="Foto"><i class="fas fa-camera"></i></a>
                            </td>
                            <td>
                                <form class="FormularioAjax" action="' . APP_URL . 'app/ajax/productoAjax.php" method="POST" autocomplete="off" data-pregunta="¿Está seguro de que desea cambiar el estado de este producto a '.$txt_estado.'? Los productos inactivos no aparecerán disponibles en el módulo de ventas.">
                                    <input type="hidden" name="modulo_producto" value="estado">
                                    <input type="hidden" name="producto_id" value="' . $rows['producto_id'] . '">
                                    <input type="hidden" name="producto_estado" value="' . $txt_estado . '">
                                    <button type="submit" class="button is-' . $btn_estado . ' is-rounded is-small" title="Cambiar a ' . $txt_estado . '"><i class="fas fa-' . $icon_estado . '"></i></button>
                                </form>
                            </td>

                            <td>
                                <button class="button is-link is-rounded is-small js-modal-trigger" data-target="modal-detalle-' . $rows['producto_id'] . '" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>

                            <td>
                                <a href="' . APP_URL . 'productUpdate/' . $rows['producto_id'] . '/" class="button is-success is-rounded is-small" title="Actualizar"><i class="fas fa-sync"></i></a>
                            </td>
                            <td>
                                <form class="FormularioAjax" action="' . APP_URL . 'app/ajax/productoAjax.php" method="POST" autocomplete="off" data-pregunta="¿Está seguro de que desea ELIMINAR este producto? Esta acción lo eliminará de forma permanente del inventario y no podrá ser seleccionado en futuras transacciones.">
                                    <input type="hidden" name="modulo_producto" value="eliminar">
                                    <input type="hidden" name="producto_id" value="' . $rows['producto_id'] . '">
                                    <button type="submit" class="button is-danger is-rounded is-small" title="Eliminar"><i class="far fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>';

                            // MODAL DE DETALLE 
                            $estado_modal = ($rows['producto_estado'] == 'Activo') ? '<span class="tag is-success is-light">Activo</span>' : '<span class="tag is-danger is-light">Inactivo</span>';
                            
                            $tabla .= '
                            <div id="modal-detalle-' . $rows['producto_id'] . '" class="modal">
                                <div class="modal-background"></div>
                                <div class="modal-card" style="width: 800px; max-width: 95vw;">
                                    <header class="modal-card-head">
                                        <p class="modal-card-title has-text-dark">Detalle Completo del Producto</p>
                                        <button class="delete" aria-label="close"></button>
                                    </header>
                                    <section class="modal-card-body has-text-dark has-text-left">
                                        <div class="columns">
                                            <div class="column is-4 has-text-centered">
                                                <figure class="image is-4by3 mb-4">';
                                                    
                                                    $foto_ruta = "./app/views/productos/" . $rows['producto_foto'];
                                                    
                                                    if (is_file($foto_ruta)) {
                                                        $tabla .= '<img src="' . APP_URL . 'app/views/productos/' . $rows['producto_foto'] . '" alt="Producto" style="border-radius: 8px; object-fit: cover;">';
                                                    } else {
                                                        $tabla .= '<img src="' . APP_URL . 'app/views/productos/default.png" alt="Sin foto" style="border-radius: 8px;">';
                                                    }

                                    $tabla .= ' </figure>
                                                ' . $estado_modal . '
                                                <div class="mt-4 has-text-left">
                                                    <p class="is-size-7 has-text-grey"><strong>Distribuido por:</strong></p>
                                                    <div class="tags mt-1">';
                                                        $id_p = $rows['producto_id'];
                                                        $cons_prov = $this->ejecutarConsulta("SELECT p.proveedor_nombre 
                                                            FROM producto_proveedor pp 
                                                            INNER JOIN proveedor p ON pp.proveedor_id = p.proveedor_id 
                                                            WHERE pp.producto_id = '$id_p'");
                                                        
                                                        if($cons_prov->rowCount() > 0){
                                                            while($prov = $cons_prov->fetch()){
                                                                $tabla .= '<span class="tag is-link is-light">' . $prov['proveedor_nombre'] . '</span>';
                                                            }
                                                        } else {
                                                            $tabla .= '<span class="tag is-warning is-light">Sin proveedores</span>';
                                                        }
                                           
                                    $tabla .= '     </div>
                                                </div>
                                            </div>
                                            
                                            <div class="column is-8">
                                                <p class="is-size-4 has-text-weight-bold">' . $rows['producto_nombre'] . '</p>
                                                <hr style="margin: 10px 0;">
                                                
                                                <div class="columns is-multiline is-mobile">
                                                    <div class="column is-6">
                                                        <p><strong>Código:</strong> ' . $rows['producto_codigo'] . '</p>
                                                        <p><strong>Marca:</strong> ' . ($rows['producto_marca'] != "" ? $rows['producto_marca'] : "N/A") . '</p>
                                                        <p><strong>Modelo:</strong> ' . ($rows['producto_modelo'] != "" ? $rows['producto_modelo'] : "N/A") . '</p>
                                                        <p><strong>Categoría:</strong> ' . ($rows['categoria_padre_nombre'] ?? 'Sin Categoría') . ' <i class="fas fa-angle-right"></i> ' . $rows['subcategoria_nombre'] . '</p>
                                                    </div>
                                                    
                                                    <div class="column is-6">
                                                        <p><strong>Tipo Unidad:</strong> ' . $rows['producto_unidad'] . '</p>
                                                        <p><strong>Stock Mínimo:</strong> ' . $rows['producto_stock_min'] . ' ' . $rows['producto_unidad'] . '</p>
                                                        <p><strong>Stock Máximo:</strong> ' . $rows['producto_stock_max'] . ' ' . $rows['producto_unidad'] . '</p>
                                                        <p><strong>Stock Actual:</strong> <span class="has-text-weight-bold is-size-6">' . $rows['producto_stock'] . ' ' . $rows['producto_unidad'] . '</span></p>
                                                    </div>
                                                </div>
                                                
                                                <br>
                                                <div class="columns is-mobile">
                                                    <div class="column is-6">
                                                        <div class="notification is-light is-warning has-text-centered p-3">
                                                            <p class="is-size-7 has-text-grey-dark"><strong>Costo (Compra)</strong></p>
                                                            <p class="is-size-5 has-text-dark">$' . $rows['producto_costo'] . '</p>
                                                            <p class="is-size-7 has-text-dark precio-bcv" data-usd="' . $rows['producto_costo'] . '">Calculando Bs...</p>
                                                        </div>
                                                    </div>
                                                    <div class="column is-6">
                                                        <div class="notification is-light is-info has-text-centered p-3">
                                                            <p class="is-size-7 has-text-link"><strong>Precio (Venta)</strong></p>
                                                            <p class="is-size-4 has-text-link has-text-weight-bold">$' . $rows['producto_precio'] . '</p>
                                                            <p class="is-size-7 has-text-link has-text-weight-bold precio-bcv" data-usd="' . $rows['producto_precio'] . '">Calculando Bs...</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </section>
                                    <footer class="modal-card-foot is-justify-content-flex-end">
                                        <button class="button is-dark js-modal-close">Cerrar Detalle</button>
                                    </footer>
                                </div>
                            </div>';
                $contador++;
            }
            $pag_final = $contador - 1;
        } else {
            $tabla .= '<tr class="has-text-centered" ><td colspan="13">No hay registros</td></tr>';
        }
        $tabla .= '</tbody></table></div>';
        if ($total > 0 && $pagina <= $numeroPaginas) {
            $tabla .= '<p class="has-text-right">Mostrando productos <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';
            $tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);
        }
        return $tabla;
    }

	/*----------  Controlador eliminar producto  ----------*/
	public function eliminarProductoControlador()
	{
		$id = $this->limpiarCadena($_POST['producto_id']);
		$datos = $this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Producto no encontrado", "icono" => "error"];
			return json_encode($alerta);
			exit();
		}
		$datos = $datos->fetch();

		$check_ventas = $this->ejecutarConsulta("SELECT producto_id FROM venta_detalle WHERE producto_id='$id' LIMIT 1");
		if ($check_ventas->rowCount() > 0) {
			$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "No se puede eliminar, tiene ventas asociadas", "icono" => "error"];
			return json_encode($alerta);
			exit();
		}

		$eliminarProducto = $this->eliminarRegistro("producto", "producto_id", $id);
		if ($eliminarProducto->rowCount() == 1) {
			$this->guardarBitacora("Productos", "Eliminación", "Se eliminó el producto: " . $datos['producto_nombre']);
			if (is_file("../views/productos/" . $datos['producto_foto'])) {
				chmod("../views/productos/" . $datos['producto_foto'], 0777);
				unlink("../views/productos/" . $datos['producto_foto']);
			}
			$alerta = ["tipo" => "recargar", "titulo" => "Éxito", "texto" => "Producto eliminado", "icono" => "success"];
		} else {
			$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "No se pudo eliminar", "icono" => "error"];
		}
		return json_encode($alerta);
	}

    /*----------  Controlador actualizar producto (BLINDADO + MULTI-PROVEEDOR) ----------*/
    public function actualizarProductoControlador()
    {
        $id = $this->limpiarCadena($_POST['producto_id']);

        $datos = $this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='$id'");
        if ($datos->rowCount() <= 0) {
            $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Producto no encontrado", "icono" => "error"];
            return json_encode($alerta);
            exit();
        }
        $datos = $datos->fetch();

        $codigo = $this->limpiarCadena($_POST['producto_codigo']);
        $nombre = $this->limpiarCadena($_POST['producto_nombre']);
        $marca = $this->limpiarCadena($_POST['producto_marca']);
        $modelo = $this->limpiarCadena($_POST['producto_modelo']);
        
        // STOCK, COSTO Y PRECIO ESTÁN BLINDADOS. Se mantienen los de la Base de Datos.
        $stock = $datos['producto_stock']; 
        $costo = $datos['producto_costo'];
        $precio = $datos['producto_precio'];

        $stock_min = $this->limpiarCadena($_POST['producto_stock_min']);
        $stock_max = $this->limpiarCadena($_POST['producto_stock_max']);
        $categoria = $this->limpiarCadena($_POST['producto_categoria']);
        $unidad = $this->limpiarCadena($_POST['producto_unidad']);

        /* Recepción de proveedores */
        $proveedores = (isset($_POST['producto_proveedores'])) ? $_POST['producto_proveedores'] : [];

        /*---------- NUEVAS VALIDACIONES DE INTEGRIDAD ----------*/
        if (!preg_match("/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/", $nombre)) {
            $alerta = ["tipo" => "simple", "titulo" => "Nombre Inválido", "texto" => "El nombre debe contener letras", "icono" => "error"];
            return json_encode($alerta);
            exit();
        }

        if ($this->verificarDatos("[0-9]{1,13}", $codigo)) {
            $alerta = ["tipo" => "simple", "titulo" => "Error en Código", "texto" => "El código de barras solo permite números (máx. 13)", "icono" => "error"];
            return json_encode($alerta);
            exit();
        }

        if ($codigo == "" || $nombre == "" || $categoria == "" || $unidad == "" || $stock_min == "" || $stock_max == "") {
            $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Faltan campos obligatorios", "icono" => "error"];
            return json_encode($alerta);
            exit();
        }

        /* Validación de que haya al menos un proveedor*/
        if (empty($proveedores)) {
            $alerta = ["tipo" => "simple", "titulo" => "Error de Proveedor", "texto" => "Debe seleccionar al menos un proveedor", "icono" => "error"];
            return json_encode($alerta);
            exit();
        }

        /*---------- VALIDACIONES LOGICAS DE STOCK ----------*/
        if ((int)$stock_max <= 0) {
            $alerta = ["tipo" => "simple", "titulo" => "Límite de Stock Inválido", "texto" => "El Stock Máximo debe ser mayor a 0", "icono" => "error"];
            return json_encode($alerta);
            exit();
        }

        if ((int)$stock_min >= (int)$stock_max) {
            $alerta = ["tipo" => "simple", "titulo" => "Lógica de Stock Incorrecta", "texto" => "El Stock Mínimo no puede ser mayor o igual al Máximo", "icono" => "error"];
            return json_encode($alerta);
            exit();
        }

        if ($datos['producto_codigo'] != $codigo) {
            $check_codigo = $this->ejecutarConsulta("SELECT producto_codigo FROM producto WHERE producto_codigo='$codigo'");
            if ($check_codigo->rowCount() > 0) {
                $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "El código ya existe", "icono" => "error"];
                return json_encode($alerta);
                exit();
            }
        }

        $producto_datos_up = [
            ["campo_nombre" => "producto_codigo", "campo_marcador" => ":Codigo", "campo_valor" => $codigo],
            ["campo_nombre" => "producto_nombre", "campo_marcador" => ":Nombre", "campo_valor" => $nombre],
            ["campo_nombre" => "producto_marca", "campo_marcador" => ":Marca", "campo_valor" => $marca],
            ["campo_nombre" => "producto_modelo", "campo_marcador" => ":Modelo", "campo_valor" => $modelo],
            ["campo_nombre" => "producto_stock_min", "campo_marcador" => ":StockMin", "campo_valor" => $stock_min],
            ["campo_nombre" => "producto_stock_max", "campo_marcador" => ":StockMax", "campo_valor" => $stock_max],
            ["campo_nombre" => "categoria_id", "campo_marcador" => ":Categoria", "campo_valor" => $categoria],
            ["campo_nombre" => "producto_unidad", "campo_marcador" => ":Unidad", "campo_valor" => $unidad]
        ];

        $condicion = ["condicion_campo" => "producto_id", "condicion_marcador" => ":ID", "condicion_valor" => $id];

        if ($this->actualizarDatos("producto", $producto_datos_up, $condicion)) {
            /*Actualización de Proveedores*/
            $this->ejecutarConsulta("DELETE FROM producto_proveedor WHERE producto_id='$id'");
            
            foreach ($proveedores as $prov_id) {
                $id_p = $this->limpiarCadena($prov_id);
                
                $datos_prov_reg = [
                    [
                        "campo_nombre" => "producto_id", 
                        "campo_marcador" => ":ProdID", // ESTO ES LO QUE FALTA
                        "campo_valor" => $id
                    ],
                    [
                        "campo_nombre" => "proveedor_id", 
                        "campo_marcador" => ":ProvID", // ESTO ES LO QUE FALTA
                        "campo_valor" => $id_p
                    ]
                ];
                
                // Ahora guardarDatos() encontrará los índices y no dará error en la línea 57 y 63
                $this->guardarDatos("producto_proveedor", $datos_prov_reg);
            }

            $this->guardarBitacora("Productos", "Actualización", "Datos actualizados del producto: " . $nombre);
            $alerta = ["tipo" => "recargar", "titulo" => "Éxito", "texto" => "Producto actualizado con éxito", "icono" => "success"];
        } else {
            $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "No se pudo actualizar o no hubo cambios", "icono" => "error"];
        }
        return json_encode($alerta);
    }
/*---------- Controlador actualizar foto producto  ----------*/
    public function actualizarFotoProductoControlador()
    {
        $id = $this->limpiarCadena($_POST['producto_id']);

        // Verificando producto
        $datos = $this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='$id'");
        if ($datos->rowCount() <= 0) {
            $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Producto no encontrado", "icono" => "error"];
            return json_encode($alerta);
        }
        $datos = $datos->fetch();

    
        $img_dir = $_SERVER['DOCUMENT_ROOT'] . '/VENTAS/app/views/productos/';

        if ($_FILES['producto_foto']['name'] == "" && $_FILES['producto_foto']['size'] <= 0) {
            $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "No ha seleccionado una imagen válida", "icono" => "error"];
            return json_encode($alerta);
        }

        // 2. Validar que sea imagen
        $tipo_archivo = mime_content_type($_FILES['producto_foto']['tmp_name']);
        if (strpos($tipo_archivo, "image/") === false) {
            $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "El archivo seleccionado no es una imagen válida", "icono" => "error"];
            return json_encode($alerta);
        }

        // 3. Obtener extensión y limpiar nombre
        $extension = pathinfo($_FILES['producto_foto']['name'], PATHINFO_EXTENSION);
        $nombre_limpio = preg_replace('/[|\\/<>:*?"]/', '', $datos['producto_nombre']);
        $nombre_limpio = str_ireplace(" ", "_", $nombre_limpio);

        // 4. Nombre final único
        $foto = $nombre_limpio . "_" . rand(0, 1000) . "." . $extension;

        // Verificar permisos y carpeta (Sin chmod en Windows)
        if (!file_exists($img_dir)) {
            mkdir($img_dir, 0777, true);
        }

        // 5. MOVER EL ARCHIVO 
        if (!move_uploaded_file($_FILES['producto_foto']['tmp_name'], $img_dir . $foto)) {
            $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "PHP no pudo mover el archivo. Verifique permisos en Windows de la carpeta 'productos'.", "icono" => "error"];
            return json_encode($alerta);
        }

        // 6. Eliminar foto anterior si existe y no es la por defecto
        if (is_file($img_dir . $datos['producto_foto']) && $datos['producto_foto'] != "default.png") {
            unlink($img_dir . $datos['producto_foto']);
        }

        // 7. Actualizar DB
        $producto_datos_up = [
            ["campo_nombre" => "producto_foto", "campo_marcador" => ":Foto", "campo_valor" => $foto]
        ];

        $condicion = ["condicion_campo" => "producto_id", "condicion_marcador" => ":ID", "condicion_valor" => $id];

        if ($this->actualizarDatos("producto", $producto_datos_up, $condicion)) {
            $alerta = ["tipo" => "recargar", "titulo" => "¡Foto actualizada!", "texto" => "La imagen se actualizó con éxito", "icono" => "success"];
        } else {
            $alerta = ["tipo" => "recargar", "titulo" => "Foto subida", "texto" => "La foto se guardó pero el nombre no cambió en la BD", "icono" => "warning"];
        }

        return json_encode($alerta);
    }
}