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

		$precio = $this->limpiarCadena($_POST['producto_precio']);
		$costo = $this->limpiarCadena($_POST['producto_costo']);

		$stock = $this->limpiarCadena($_POST['producto_stock']);
		$stock_min = $this->limpiarCadena($_POST['producto_stock_min']);
		$stock_max = $this->limpiarCadena($_POST['producto_stock_max']);

		$categoria = $this->limpiarCadena($_POST['producto_categoria']);
		$unidad = $this->limpiarCadena($_POST['producto_unidad']);

		/*---------- NUEVAS VALIDACIONES DE INTEGRIDAD ----------*/
		# Validando Código de Barras (Solo números, máx 13) #
		if ($this->verificarDatos("[0-9]{1,13}", $codigo)) {
			$alerta = ["tipo" => "simple", "titulo" => "Error en Código", "texto" => "El código de barras solo permite números (máx. 13)", "icono" => "error"];
			return json_encode($alerta);
			exit();
		}

		# Validando Costo y Precio (Números con punto decimal) #
		if ($this->verificarDatos("[0-9.]{1,25}", $costo)) {
			$alerta = ["tipo" => "simple", "titulo" => "Error en Costo", "texto" => "El costo de compra no tiene un formato válido", "icono" => "error"];
			return json_encode($alerta);
			exit();
		}

		if ($this->verificarDatos("[0-9.]{1,25}", $precio)) {
			$alerta = ["tipo" => "simple", "titulo" => "Error en Precio", "texto" => "El precio de venta no tiene un formato válido", "icono" => "error"];
			return json_encode($alerta);
			exit();
		}

		if ($codigo == "" || $nombre == "" || $precio == "" || $stock == "" || $categoria == "" || $unidad == "" || $costo == "" || $stock_min ==  "" || $stock_max == "") {
			$alerta = ["tipo" => "simple", "titulo" => "Ocurrió un error inesperado", "texto" => "No has llenado todos los campos que son obligatorios", "icono" => "error"];
			return json_encode($alerta);
			exit();
		}

		if ((float)$costo <= 0 || (float)$precio <= 0) {
            $alerta = [
                "tipo" => "simple", 
                "titulo" => "Error de Precio/Costo", 
                "texto" => "El costo de compra y el precio de venta deben ser mayores a 0", 
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

		/*---------- VALIDACIONES LOGICAS DE STOCK ----------*/
		if ((int)$stock_max <= 0) {
            $alerta = [
                "tipo" => "simple", 
                "titulo" => "Límite de Stock Inválido", 
                "texto" => "El Stock Máximo (Límite) debe ser mayor a 0", 
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

		if ((int)$stock_min >= (int)$stock_max) {
            $alerta = [
                "tipo" => "simple", 
                "titulo" => "Lógica de Stock Incorrecta", 
                "texto" => "El Stock Mínimo (Alerta) no puede ser mayor o igual al Stock Máximo", 
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

		$check_codigo = $this->ejecutarConsulta("SELECT producto_codigo FROM producto WHERE producto_codigo='$codigo'");
		if ($check_codigo->rowCount() > 0) {
			$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "El código ya existe", "icono" => "error"];
			return json_encode($alerta);
			exit();
		}

		$img_dir = "../views/productos/";
		$foto = "";
		if (isset($_FILES['producto_foto']) && $_FILES['producto_foto']['name'] != "" && $_FILES['producto_foto']['size'] > 0) {
			if (!file_exists($img_dir)) {
				if (!mkdir($img_dir, 0777)) {
					$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Error al crear directorio", "icono" => "error"];
					return json_encode($alerta);
					exit();
				}
			}
			if (mime_content_type($_FILES['producto_foto']['tmp_name']) != "image/jpeg" && mime_content_type($_FILES['producto_foto']['tmp_name']) != "image/png") {
				$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Formato de imagen no permitido", "icono" => "error"];
				return json_encode($alerta);
				exit();
			}
			$foto = str_ireplace(" ", "_", $nombre) . "_" . rand(0, 100);
			switch (mime_content_type($_FILES['producto_foto']['tmp_name'])) {
				case 'image/jpeg':
					$foto = $foto . ".jpg";
					break;
				case 'image/png':
					$foto = $foto . ".png";
					break;
			}
			chmod($img_dir, 0777);
			if (!move_uploaded_file($_FILES['producto_foto']['tmp_name'], $img_dir . $foto)) {
				$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "No se pudo subir la imagen", "icono" => "error"];
				return json_encode($alerta);
				exit();
			}
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
			$this->guardarBitacora("Productos", "Registro", "Se registró el producto: " . $nombre . " (Cód: " . $codigo . ")");
			$alerta = ["tipo" => "redireccionar", "titulo" => "Éxito", "texto" => "Producto registrado", "icono" => "success", "url" => APP_URL."productList/"];
		} else {
			if (is_file($img_dir . $foto)) {
				chmod($img_dir . $foto, 0777);
				unlink($img_dir . $foto);
			}
			$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "No se pudo registrar", "icono" => "error"];
		}
		return json_encode($alerta);
	}


	/*----------  Controlador listar productos (TABLA CON PRECIOS EN BS Y ALERTAS CORREGIDAS) ----------*/
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

		// Definimos la unión de tablas una sola vez para simplificar
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

				// NUEVO: Las alertas visuales SOLO se muestran si el producto está ACTIVO
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
                                <form class="FormularioAjax" action="' . APP_URL . 'app/ajax/productoAjax.php" method="POST" autocomplete="off" >
			                		<input type="hidden" name="modulo_producto" value="estado">
			                		<input type="hidden" name="producto_id" value="' . $rows['producto_id'] . '">
                                    <input type="hidden" name="producto_estado" value="' . $txt_estado . '">
			                    	<button type="submit" class="button is-' . $btn_estado . ' is-rounded is-small" title="Cambiar a ' . $txt_estado . '"><i class="fas fa-' . $icon_estado . '"></i></button>
			                    </form>
                            </td>
			                <td>
			                    <a href="' . APP_URL . 'productUpdate/' . $rows['producto_id'] . '/" class="button is-success is-rounded is-small" title="Actualizar"><i class="fas fa-sync"></i></a>
			                </td>
			                <td>
			                	<form class="FormularioAjax" action="' . APP_URL . 'app/ajax/productoAjax.php" method="POST" autocomplete="off" >
			                		<input type="hidden" name="modulo_producto" value="eliminar">
			                		<input type="hidden" name="producto_id" value="' . $rows['producto_id'] . '">
			                    	<button type="submit" class="button is-danger is-rounded is-small" title="Eliminar"><i class="far fa-trash-alt"></i></button>
			                    </form>
			                </td>
						</tr>';
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

	/*---------- Controlador para Activar / Desactivar producto ----------*/
	public function actualizarEstadoProductoControlador()
	{
		$id = $this->limpiarCadena($_POST['producto_id']);
		$estado = $this->limpiarCadena($_POST['producto_estado']);

		$datos = $this->ejecutarConsulta("SELECT producto_nombre FROM producto WHERE producto_id='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Producto no encontrado", "icono" => "error"];
			return json_encode($alerta);
			exit();
		}
		$datos = $datos->fetch();

		$producto_datos_up = [
			["campo_nombre" => "producto_estado", "campo_marcador" => ":Estado", "campo_valor" => $estado]
		];
		$condicion = ["condicion_campo" => "producto_id", "condicion_marcador" => ":ID", "condicion_valor" => $id];

		if ($this->actualizarDatos("producto", $producto_datos_up, $condicion)) {
			$this->guardarBitacora("Productos", "Estado", "Se cambió el estado de " . $datos['producto_nombre'] . " a " . $estado);
			$alerta = ["tipo" => "recargar", "titulo" => "Éxito", "texto" => "El estado del producto fue actualizado", "icono" => "success"];
		} else {
			$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "No se pudo actualizar el estado", "icono" => "error"];
		}
		return json_encode($alerta);
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

	/*----------  Controlador actualizar producto  ----------*/
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
		$precio = $this->limpiarCadena($_POST['producto_precio']);
		$costo = $this->limpiarCadena($_POST['producto_costo']);
		$stock = $this->limpiarCadena($_POST['producto_stock']);
		$stock_min = $this->limpiarCadena($_POST['producto_stock_min']);
		$stock_max = $this->limpiarCadena($_POST['producto_stock_max']);
		$categoria = $this->limpiarCadena($_POST['producto_categoria']);
		$unidad = $this->limpiarCadena($_POST['producto_unidad']);

				/*---------- NUEVAS VALIDACIONES DE INTEGRIDAD ----------*/
		# Validando Código de Barras (Solo números, máx 13) #
		if ($this->verificarDatos("[0-9]{1,13}", $codigo)) {
			$alerta = ["tipo" => "simple", "titulo" => "Error en Código", "texto" => "El código de barras solo permite números (máx. 13)", "icono" => "error"];
			return json_encode($alerta);
			exit();
		}

		# Validando Costo y Precio (Números con punto decimal) #
		if ($this->verificarDatos("[0-9.]{1,25}", $costo)) {
			$alerta = ["tipo" => "simple", "titulo" => "Error en Costo", "texto" => "El costo de compra no tiene un formato válido", "icono" => "error"];
			return json_encode($alerta);
			exit();
		}

		if ($this->verificarDatos("[0-9.]{1,25}", $precio)) {
			$alerta = ["tipo" => "simple", "titulo" => "Error en Precio", "texto" => "El precio de venta no tiene un formato válido", "icono" => "error"];
			return json_encode($alerta);
			exit();
		}

		if ($codigo == "" || $nombre == "" || $precio == "" || $stock == "" || $categoria == "" || $unidad == "") {
			$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Faltan campos obligatorios", "icono" => "error"];
			return json_encode($alerta);
			exit();
		}

		if ((float)$costo <= 0 || (float)$precio <= 0) {
            $alerta = [
                "tipo" => "simple", 
                "titulo" => "Error de Precio/Costo", 
                "texto" => "El costo de compra y el precio de venta deben ser mayores a 0", 
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

		/*---------- VALIDACIONES LOGICAS DE STOCK ----------*/
		if ((int)$stock_max <= 0) {
            $alerta = [
                "tipo" => "simple", 
                "titulo" => "Límite de Stock Inválido", 
                "texto" => "El Stock Máximo (Límite) debe ser mayor a 0", 
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

		if ((int)$stock_min >= (int)$stock_max) {
            $alerta = [
                "tipo" => "simple", 
                "titulo" => "Lógica de Stock Incorrecta", 
                "texto" => "El Stock Mínimo (Alerta) no puede ser mayor o igual al Stock Máximo", 
                "icono" => "error"
            ];
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
			["campo_nombre" => "producto_precio", "campo_marcador" => ":Precio", "campo_valor" => $precio],
			["campo_nombre" => "producto_costo", "campo_marcador" => ":Costo", "campo_valor" => $costo],
			["campo_nombre" => "producto_stock", "campo_marcador" => ":Stock", "campo_valor" => $stock],
			["campo_nombre" => "producto_stock_min", "campo_marcador" => ":StockMin", "campo_valor" => $stock_min],
			["campo_nombre" => "producto_stock_max", "campo_marcador" => ":StockMax", "campo_valor" => $stock_max],
			["campo_nombre" => "categoria_id", "campo_marcador" => ":Categoria", "campo_valor" => $categoria],
			["campo_nombre" => "producto_unidad", "campo_marcador" => ":Unidad", "campo_valor" => $unidad]
		];

		$condicion = ["condicion_campo" => "producto_id", "condicion_marcador" => ":ID", "condicion_valor" => $id];

		if ($this->actualizarDatos("producto", $producto_datos_up, $condicion)) {
			$this->guardarBitacora("Productos", "Actualización", "Datos actualizados del producto: " . $nombre);
			$alerta = ["tipo" => "recargar", "titulo" => "Éxito", "texto" => "Producto actualizado", "icono" => "success"];
		} else {
			$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "No se pudo actualizar", "icono" => "error"];
		}
		return json_encode($alerta);
	}

	public function actualizarFotoProductoControlador()
	{
		$id = $this->limpiarCadena($_POST['producto_id']);
		$datos = $this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Producto no encontrado", "icono" => "error"];
			return json_encode($alerta);
			exit();
		}
		$datos = $datos->fetch();
		$img_dir = "../views/productos/";
		if (!isset($_FILES['producto_foto']) || $_FILES['producto_foto']['name'] == "" || $_FILES['producto_foto']['size'] <= 0) {
			$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Seleccione una foto", "icono" => "error"];
			return json_encode($alerta);
			exit();
		}
		if (!file_exists($img_dir)) {
			if (!mkdir($img_dir, 0777)) {
				$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Error directorio", "icono" => "error"];
				return json_encode($alerta);
				exit();
			}
		}
		if (mime_content_type($_FILES['producto_foto']['tmp_name']) != "image/jpeg" && mime_content_type($_FILES['producto_foto']['tmp_name']) != "image/png") {
			$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Formato incorrecto", "icono" => "error"];
			return json_encode($alerta);
			exit();
		}

		if ($datos['producto_foto'] != "") {
			$foto = explode(".", $datos['producto_foto']);
			$foto = $foto[0];
		} else {
			$foto = str_ireplace(" ", "_", $datos['producto_nombre']) . "_" . rand(0, 100);
		}
		switch (mime_content_type($_FILES['producto_foto']['tmp_name'])) {
			case 'image/jpeg':
				$foto = $foto . ".jpg";
				break;
			case 'image/png':
				$foto = $foto . ".png";
				break;
		}
		chmod($img_dir, 0777);
		if (!move_uploaded_file($_FILES['producto_foto']['tmp_name'], $img_dir . $foto)) {
			$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Error al subir imagen", "icono" => "error"];
			return json_encode($alerta);
			exit();
		}
		if (is_file($img_dir . $datos['producto_foto']) && $datos['producto_foto'] != $foto) {
			chmod($img_dir . $datos['producto_foto'], 0777);
			unlink($img_dir . $datos['producto_foto']);
		}

		$producto_datos_up = [["campo_nombre" => "producto_foto", "campo_marcador" => ":Foto", "campo_valor" => $foto]];
		$condicion = ["condicion_campo" => "producto_id", "condicion_marcador" => ":ID", "condicion_valor" => $id];
		if ($this->actualizarDatos("producto", $producto_datos_up, $condicion)) {
			$this->guardarBitacora("Productos", "Actualización de Foto", "Se cambió la foto del producto: " . $datos['producto_nombre']);
			$alerta = ["tipo" => "recargar", "titulo" => "Éxito", "texto" => "Foto actualizada", "icono" => "success"];
		} else {
			$alerta = ["tipo" => "recargar", "titulo" => "Alerta", "texto" => "No se pudo actualizar BD", "icono" => "warning"];
		}
		return json_encode($alerta);
	}

	public function eliminarFotoProductoControlador()
	{
		$id = $this->limpiarCadena($_POST['producto_id']);
		$datos = $this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='$id'");
		if ($datos->rowCount() <= 0) {
			$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Producto no encontrado", "icono" => "error"];
			return json_encode($alerta);
			exit();
		}
		$datos = $datos->fetch();
		$img_dir = "../views/productos/";
		chmod($img_dir, 0777);
		if (is_file($img_dir . $datos['producto_foto'])) {
			chmod($img_dir . $datos['producto_foto'], 0777);
			if (!unlink($img_dir . $datos['producto_foto'])) {
				$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Error al borrar archivo", "icono" => "error"];
				return json_encode($alerta);
				exit();
			}
		}
		$producto_datos_up = [["campo_nombre" => "producto_foto", "campo_marcador" => ":Foto", "campo_valor" => ""]];
		$condicion = ["condicion_campo" => "producto_id", "condicion_marcador" => ":ID", "condicion_valor" => $id];
		if ($this->actualizarDatos("producto", $producto_datos_up, $condicion)) {
			$this->guardarBitacora("Productos", "Eliminación de Foto", "Se eliminó la foto del producto: " . $datos['producto_nombre']);
			$alerta = ["tipo" => "recargar", "titulo" => "Éxito", "texto" => "Foto eliminada", "icono" => "success"];
		} else {
			$alerta = ["tipo" => "recargar", "titulo" => "Alerta", "texto" => "Foto borrada, error en BD", "icono" => "warning"];
		}
		return json_encode($alerta);
	}
}
