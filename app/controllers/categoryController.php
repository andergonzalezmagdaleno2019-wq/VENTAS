<?php

	namespace app\controllers;
	use app\models\mainModel;

	class categoryController extends mainModel{

		/*----------  Controlador registrar categoria  ----------*/
		public function registrarCategoriaControlador(){

			# Almacenando datos#
		    $nombre=$this->limpiarCadena($_POST['categoria_nombre']);
		    $ubicacion=$this->limpiarCadena($_POST['categoria_ubicacion']);

			# Almacenando datos de categoría padre #
			$padre_id=isset($_POST['categoria_padre_id']) ? $this->limpiarCadena($_POST['categoria_padre_id']) : "";

		    # Verificando campos obligatorios #
            if($nombre==""){
            	$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"No has llenado todos los campos que son obligatorios","icono"=>"error"]; return json_encode($alerta); exit();
            }

            # Verificando integridad de los datos #
		    if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}",$nombre)){
		    	$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"El NOMBRE no coincide con el formato solicitado","icono"=>"error"]; return json_encode($alerta); exit();
		    }

            # VALIDACIÓN: El nombre debe tener letras #
            if (!preg_match("/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/", $nombre)) {
                $alerta = ["tipo" => "simple", "titulo" => "Nombre Inválido", "texto" => "El nombre de la categoría no puede ser solo números.", "icono" => "error"]; return json_encode($alerta); exit();
            }

		    if($ubicacion!=""){
		    	if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,150}",$ubicacion)){
			    	$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"La UBICACION no coincide con el formato solicitado","icono"=>"error"]; return json_encode($alerta); exit();
			    }
		    }

		    # Verificando nombre #
		    $check_nombre=$this->ejecutarConsulta("SELECT categoria_nombre FROM categoria WHERE categoria_nombre='$nombre'");
		    if($check_nombre->rowCount()>0){
		    	$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"El NOMBRE ingresado ya se encuentra registrado, por favor elija otro","icono"=>"error"]; return json_encode($alerta); exit();
		    }

            # --- LÓGICA INTELIGENTE DE HERENCIA DE UNIDADES ---
            $unidades_str = "";
            if($padre_id == ""){
                // Es Categoría Principal: Exigimos los checkboxes
                $unidades = isset($_POST['categoria_unidades']) ? $_POST['categoria_unidades'] : [];
                if(empty($unidades)){
                    $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Debes seleccionar al menos un Tipo de Producto permitido para esta categoría","icono"=>"error"]; return json_encode($alerta); exit();
                }
                $unidades_str = implode(",", $unidades);
            } else {
                // Es Subcategoría: Heredamos las unidades del padre de forma silenciosa
                $check_padre = $this->ejecutarConsulta("SELECT categoria_unidades FROM categoria WHERE categoria_id='$padre_id'");
                if($check_padre->rowCount() > 0){
                    $datos_padre = $check_padre->fetch();
                    $unidades_str = $datos_padre['categoria_unidades'];
                } else {
                    $unidades_str = "Unidad"; // Respaldo por seguridad
                }
            }
            # --------------------------------------------------

		    $categoria_datos_reg=[
				["campo_nombre"=>"categoria_nombre","campo_marcador"=>":Nombre","campo_valor"=>$nombre],
				["campo_nombre"=>"categoria_ubicacion","campo_marcador"=>":Ubicacion","campo_valor"=>$ubicacion],
                ["campo_nombre"=>"categoria_unidades","campo_marcador"=>":Unidades","campo_valor"=>$unidades_str]
			];

            // Si hay un padre seleccionado, lo agregamos al array de datos
            if ($padre_id != "") {
                $categoria_datos_reg[] = ["campo_nombre" => "categoria_padre_id", "campo_marcador" => ":Padre", "campo_valor" => $padre_id];
            }

			$registrar_categoria=$this->guardarDatos("categoria",$categoria_datos_reg);

			if($registrar_categoria->rowCount()==1){
                /*== AUDITORIA ==*/
                $this->guardarBitacora("Categorías", "Registro", "Se registró la categoría: ".$nombre);

				$alerta=["tipo"=>"limpiar","titulo"=>"Categoría registrada","texto"=>"La categoría ".$nombre." se registró con éxito","icono"=>"success"];
			}else{
				$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"No se pudo registrar la categoría, por favor intente nuevamente","icono"=>"error"];
			}

			return json_encode($alerta);
		}


		/*----------  Controlador listar categoria (CON DISEÑO MEJORADO) ----------*/
		public function listarCategoriaControlador($pagina,$registros,$url,$busqueda){

			$pagina=$this->limpiarCadena($pagina);
			$registros=$this->limpiarCadena($registros);
			$url=$this->limpiarCadena($url);
			$url=APP_URL.$url."/";
			$busqueda=$this->limpiarCadena($busqueda);
			$tabla="";

			$pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1;
			$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;

            if (isset($busqueda) && $busqueda != "") {
                $consulta_datos = "SELECT * FROM categoria WHERE (categoria_nombre LIKE '%$busqueda%' OR categoria_ubicacion LIKE '%$busqueda%') AND categoria_padre_id IS NULL ORDER BY categoria_nombre ASC LIMIT $inicio,$registros";
                $consulta_total = "SELECT COUNT(categoria_id) FROM categoria WHERE (categoria_nombre LIKE '%$busqueda%' OR categoria_ubicacion LIKE '%$busqueda%') AND categoria_padre_id IS NULL";
            } else {
                $consulta_datos = "SELECT * FROM categoria WHERE categoria_padre_id IS NULL ORDER BY categoria_nombre ASC LIMIT $inicio,$registros";
                $consulta_total = "SELECT COUNT(categoria_id) FROM categoria WHERE categoria_padre_id IS NULL";
            }
			$datos = $this->ejecutarConsulta($consulta_datos);
			$datos = $datos->fetchAll();
			$total = $this->ejecutarConsulta($consulta_total);
			$total = (int) $total->fetchColumn();
			$numeroPaginas =ceil($total/$registros);

			$tabla.='
		        <div class="table-container">
		        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
		            <thead>
		                <tr class="has-background-link-light">
		                    <th class="has-text-centered">#</th>
		                    <th class="has-text-left">Nombre de Categoría</th>
		                    <th class="has-text-left">Codificación en Almacén</th>
		                    <th class="has-text-centered" colspan="3">Opciones</th>
		                </tr>
		            </thead>
		            <tbody>
		    ';

		    if($total>=1 && $pagina<=$numeroPaginas){
				$contador=$inicio+1;
				$pag_inicio=$inicio+1;
				foreach($datos as $rows){

                    $ubicacion = ($rows['categoria_ubicacion'] != "") ? $rows['categoria_ubicacion'] : '<em class="has-text-grey-light">Sin ubicación registrada</em>';

				$tabla.='
					<tr>
						<td class="has-text-centered">'.$contador.'</td>
						<td class="has-text-left has-text-weight-bold">'.$rows['categoria_nombre'].'</td>
						<td class="has-text-left">'.$ubicacion.'</td>

						<td class="has-text-centered">
							<a href="'.APP_URL.'categoryUpdate/'.$rows['categoria_id'].'/" class="button is-success is-rounded is-small" title="Editar datos de categoría">
								<i class="fas fa-sync fa-fw"></i>
							</a>
						</td>
						<td class="has-text-centered">
							<form class="FormularioAjax" action="'.APP_URL.'app/ajax/categoriaAjax.php" method="POST" autocomplete="off" >
								<input type="hidden" name="modulo_categoria" value="eliminar">
								<input type="hidden" name="categoria_id" value="'.$rows['categoria_id'].'">
								<button type="submit" class="button is-danger is-rounded is-small" title="Eliminar categoría">
									<i class="far fa-trash-alt fa-fw"></i>
								</button>
							</form>
						</td>
					</tr>
				';
					$contador++;
				}
				$pag_final=$contador-1;
			}else{
				if($total>=1){
					$tabla.='<tr class="has-text-centered" ><td colspan="6"><a href="'.$url.'1/" class="button is-link is-rounded is-small mt-4 mb-4">Haga clic acá para recargar el listado</a></td></tr>';
				}else{
					$tabla.='<tr class="has-text-centered" ><td colspan="6">No hay registros en el sistema</td></tr>';
				}
			}

			$tabla.='</tbody></table></div>';

			if($total>0 && $pagina<=$numeroPaginas){
				$tabla.='<p class="has-text-right">Mostrando categorías <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';
				$tabla.=$this->paginadorTablas($pagina,$numeroPaginas,$url,7);
			}

			return $tabla;
		}

		/*---------- Controlador listar subcategoría ----------*/
		public function listarSubcategoriaControlador($pagina,$registros,$url,$busqueda){

			$pagina=$this->limpiarCadena($pagina);
			$registros=$this->limpiarCadena($registros);
			$url=$this->limpiarCadena($url);
			$url=APP_URL.$url."/";
			$busqueda=$this->limpiarCadena($busqueda);
			$tabla="";

			$pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1;
			$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;

			if(isset($busqueda) && $busqueda!=""){
				$consulta_datos="SELECT * FROM categoria WHERE (categoria_nombre LIKE '%$busqueda%') AND categoria_padre_id IS NOT NULL ORDER BY categoria_nombre ASC LIMIT $inicio,$registros";
				$consulta_total="SELECT COUNT(categoria_id) FROM categoria WHERE (categoria_nombre LIKE '%$busqueda%') AND categoria_padre_id IS NOT NULL";
			}else{
				$consulta_datos="SELECT * FROM categoria WHERE categoria_padre_id IS NOT NULL ORDER BY categoria_nombre ASC LIMIT $inicio,$registros";
				$consulta_total="SELECT COUNT(categoria_id) FROM categoria WHERE categoria_padre_id IS NOT NULL";
			}

			$datos = $this->ejecutarConsulta($consulta_datos);
			$datos = $datos->fetchAll();
			$total = $this->ejecutarConsulta($consulta_total);
			$total = (int) $total->fetchColumn();
			$numeroPaginas = ceil($total/$registros);

			$tabla.='
				<div class="table-container">
				<table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
					<thead>
						<tr>
							<th class="has-text-centered">#</th>
							<th class="has-text-centered">Nombre</th>
							<th class="has-text-centered">Ubicación</th>
							<th class="has-text-centered">Categoría Principal</th>
							<th class="has-text-centered" colspan="2">Opciones</th>
						</tr>
					</thead>
					<tbody>
			';

			if($total>=1 && $pagina<=$numeroPaginas){
				$contador=$inicio+1;
				$pag_inicio=$inicio+1;
				foreach($datos as $rows){
					$check_padre=$this->ejecutarConsulta("SELECT categoria_nombre FROM categoria WHERE categoria_id='".$rows['categoria_padre_id']."'");
					$padre=$check_padre->fetch();

					$tabla.='
						<tr class="has-text-centered" >
							<td>'.$contador.'</td>
							<td>'.$rows['categoria_nombre'].'</td>
							<td>'.$rows['categoria_ubicacion'].'</td>
							<td><strong>'.$padre['categoria_nombre'].'</strong></td>
							<td>
								<a href="'.APP_URL.'categoryUpdate/'.$rows['categoria_id'].'/" class="button is-success is-rounded is-small">
									<i class="fas fa-sync fa-fw"></i> &nbsp; Actualizar
								</a>
							</td>
							<td>
								<form class="FormularioAjax" action="'.APP_URL.'app/ajax/categoriaAjax.php" method="POST" autocomplete="off" >
									<input type="hidden" name="modulo_categoria" value="eliminar">
									<input type="hidden" name="categoria_id" value="'.$rows['categoria_id'].'">
									<button type="submit" class="button is-danger is-rounded is-small">
										<i class="far fa-trash-alt fa-fw"></i> &nbsp; Eliminar
									</button>
								</form>
							</td>
						</tr>
					';
					$contador++;
				}
				$pag_final=$contador-1;
			}else{
				if($total>=1){
					$tabla.='<tr class="has-text-centered" ><td colspan="6"><a href="'.$url.'" class="button is-link is-rounded is-small mt-4 mb-4">Haga clic acá para recargar la lista</a></td></tr>';
				}else{
					$tabla.='<tr class="has-text-centered" ><td colspan="6">No hay registros en el sistema</td></tr>';
				}
			}

			$tabla.='</tbody></table></div>';

			if($total>0 && $pagina<=$numeroPaginas){
				$tabla.='<p class="has-text-right">Mostrando subcategorías <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';
				$tabla.=$this->paginadorTablas($pagina,$numeroPaginas,$url,7);
			}

			return $tabla;
		}

		/*----------  Controlador eliminar categoria  ----------*/
		public function eliminarCategoriaControlador(){

			$id=$this->limpiarCadena($_POST['categoria_id']);

		    $datos=$this->ejecutarConsulta("SELECT * FROM categoria WHERE categoria_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"No hemos encontrado la categoría en el sistema","icono"=>"error"]; return json_encode($alerta); exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

		    $check_productos=$this->ejecutarConsulta("SELECT categoria_id FROM producto WHERE categoria_id='$id' LIMIT 1");
		    if($check_productos->rowCount()>0){
		        $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No podemos eliminar la categoría del sistema ya que tiene productos asociados. Debe eliminar o cambiar de categoría los productos primero.","icono"=>"error"]; return json_encode($alerta); exit();
		    }

		    $eliminarCategoria=$this->eliminarRegistro("categoria","categoria_id",$id);

		    if($eliminarCategoria->rowCount()==1){
                $this->guardarBitacora("Categorías", "Eliminación", "Se eliminó la categoría: ".$datos['categoria_nombre']);
		        $alerta=["tipo"=>"recargar","titulo"=>"Categoría eliminada","texto"=>"La categoría ".$datos['categoria_nombre']." ha sido eliminada del sistema correctamente","icono"=>"success"];
		    }else{
		    	$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"No hemos podido eliminar la categoría ".$datos['categoria_nombre']." del sistema, por favor intente nuevamente","icono"=>"error"];
		    }

		    return json_encode($alerta);
		}


		/*----------  Controlador actualizar categoria  ----------*/
		public function actualizarCategoriaControlador(){

			$id=$this->limpiarCadena($_POST['categoria_id']);

		    $datos=$this->ejecutarConsulta("SELECT * FROM categoria WHERE categoria_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"No hemos encontrado la categoría en el sistema","icono"=>"error"]; return json_encode($alerta); exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

		    $nombre=$this->limpiarCadena($_POST['categoria_nombre']);
		    $ubicacion=$this->limpiarCadena($_POST['categoria_ubicacion']);

            if($nombre==""){
            	$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"No has llenado todos los campos que son obligatorios","icono"=>"error"]; return json_encode($alerta); exit();
            }

		    if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}",$nombre)){
		    	$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"El NOMBRE no coincide con el formato solicitado","icono"=>"error"]; return json_encode($alerta); exit();
		    }

            # VALIDACIÓN: El nombre debe tener letras #
            if (!preg_match("/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/", $nombre)) {
                $alerta = ["tipo" => "simple", "titulo" => "Nombre Inválido", "texto" => "El nombre de la categoría no puede ser solo números.", "icono" => "error"]; return json_encode($alerta); exit();
            }

		    if($ubicacion!=""){
		    	if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,150}",$ubicacion)){
			    	$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"La UBICACION no coincide con el formato solicitado","icono"=>"error"]; return json_encode($alerta); exit();
			    }
		    }

		    if($datos['categoria_nombre']!=$nombre){
			    $check_nombre=$this->ejecutarConsulta("SELECT categoria_nombre FROM categoria WHERE categoria_nombre='$nombre'");
			    if($check_nombre->rowCount()>0){
			    	$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"El NOMBRE ingresado ya se encuentra registrado, por favor elija otro","icono"=>"error"]; return json_encode($alerta); exit();
			    }
		    }

            # --- LÓGICA DE ACTUALIZACIÓN DE UNIDADES ---
            $es_subcategoria = ($datos['categoria_padre_id'] != NULL && $datos['categoria_padre_id'] != "");
            $unidades_str = "";

            if(!$es_subcategoria){
                // Si es Categoría Principal, recibimos los checkboxes
                $unidades = isset($_POST['categoria_unidades']) ? $_POST['categoria_unidades'] : [];
                if(empty($unidades)){
                    $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Debes seleccionar al menos un Tipo de Producto permitido","icono"=>"error"]; return json_encode($alerta); exit();
                }
                $unidades_str = implode(",", $unidades);
            }
            # ---------------------------------------------

		    $categoria_datos_up=[
				["campo_nombre"=>"categoria_nombre","campo_marcador"=>":Nombre","campo_valor"=>$nombre],
				["campo_nombre"=>"categoria_ubicacion","campo_marcador"=>":Ubicacion","campo_valor"=>$ubicacion]
			];

            // Solo actualizamos el campo unidades si es la categoría padre
            if(!$es_subcategoria){
                $categoria_datos_up[] = ["campo_nombre"=>"categoria_unidades","campo_marcador"=>":Unidades","campo_valor"=>$unidades_str];
            }

			$condicion=["condicion_campo"=>"categoria_id","condicion_marcador"=>":ID","condicion_valor"=>$id];

            $tipo_txt = (isset($_POST['tipo_elemento']) && $_POST['tipo_elemento'] == "subcategoria") ? "Subcategoría" : "Categoría";
            $url_listado = ($tipo_txt == "Subcategoría") ? "subcategorylist/" : "categoryList/";

            if($this->actualizarDatos("categoria",$categoria_datos_up,$condicion)){

                // SINCRONIZACIÓN AUTOMÁTICA: Si se actualizan las unidades de la categoría principal,
                // le pasamos esas mismas unidades a todas sus subcategorías para que no se desfasen.
                if(!$es_subcategoria){
                    $this->ejecutarConsulta("UPDATE categoria SET categoria_unidades='$unidades_str' WHERE categoria_padre_id='$id'");
                }

                $this->guardarBitacora("Categorías", "Actualización", "Se actualizaron los datos de la ".$tipo_txt.": ".$nombre);

                $alerta=[
                    "tipo"=>"redireccionar",
                    "titulo"=>$tipo_txt." actualizada",
                    "texto"=>"Los datos de la ".strtolower($tipo_txt)." se actualizaron correctamente",
                    "icono"=>"success",
                    "url"=>APP_URL.$url_listado
                ];
            }else{
                $alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"No hemos podido actualizar los datos, por favor intente nuevamente","icono"=>"error"];
            }

            return json_encode($alerta);
	    }
    }