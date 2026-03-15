<?php
	namespace app\controllers;
	use app\models\mainModel;

	class providerController extends mainModel{

		/*----------  Controlador registrar proveedor  ----------*/
		public function registrarProveedorControlador(){

			$nombre=$this->limpiarCadena($_POST['proveedor_nombre']);
			$rif=$this->limpiarCadena($_POST['proveedor_rif']);
			$telefono=$this->limpiarCadena($_POST['proveedor_telefono']);
			$direccion=$this->limpiarCadena($_POST['proveedor_direccion']);

			if($nombre=="" || $rif==""){
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No has llenado los campos obligatorios (Nombre y RIF)",
					"icono"=>"error"
				];
				return json_encode($alerta);
				exit();
			}

			
			if($this->verificarDatos("[0-9\-]{1,15}", $rif)){
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Formato de RIF Inválido",
					"texto"=>"El RIF ingresado tiene caracteres no válidos. Solo se permiten números y guiones (-).",
					"icono"=>"error"
				];
				return json_encode($alerta);
				exit();
			}
			

			$check_rif=$this->ejecutarConsulta("SELECT proveedor_rif FROM proveedor WHERE proveedor_rif='$rif'");
			if($check_rif->rowCount()>0){
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El RIF ingresado ya se encuentra registrado",
					"icono"=>"error"
				];
				return json_encode($alerta);
				exit();
			}

			$datos_proveedor_reg=[
				[
					"campo_nombre"=>"proveedor_nombre",
					"campo_marcador"=>":Nombre",
					"campo_valor"=>$nombre
				],
				[
					"campo_nombre"=>"proveedor_rif",
					"campo_marcador"=>":Rif",
					"campo_valor"=>$rif
				],
				[
					"campo_nombre"=>"proveedor_telefono",
					"campo_marcador"=>":Telefono",
					"campo_valor"=>$telefono
				],
				[
					"campo_nombre"=>"proveedor_direccion",
					"campo_marcador"=>":Direccion",
					"campo_valor"=>$direccion
				]
			];

			$registrar_proveedor=$this->guardarDatos("proveedor",$datos_proveedor_reg);

			if($registrar_proveedor->rowCount()==1){
                /*== AUDITORIA ==*/
                $this->guardarBitacora("Proveedores", "Registro", "Se registró el proveedor: ".$nombre." (RIF: ".$rif.")");

				$alerta=[
					"tipo"=>"limpiar",
					"titulo"=>"Proveedor registrado",
					"texto"=>"El proveedor ".$nombre." se registró con éxito",
					"icono"=>"success"
				];
			}else{
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No se pudo registrar el proveedor, intente nuevamente",
					"icono"=>"error"
				];
			}
			return json_encode($alerta);
		}

		/*----------  Controlador listar proveedor ----------*/
		public function listarProveedorControlador($pagina,$registros,$url,$busqueda){
			$pagina=$this->limpiarCadena($pagina);
			$registros=$this->limpiarCadena($registros);
			$url=$this->limpiarCadena($url);
			$url=APP_URL.$url."/";
			$busqueda=$this->limpiarCadena($busqueda);
			$tabla="";

			$pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1;
			$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;

			if(isset($busqueda) && $busqueda!=""){
				$consulta_datos="SELECT * FROM proveedor WHERE proveedor_nombre LIKE '%$busqueda%' OR proveedor_rif LIKE '%$busqueda%' ORDER BY proveedor_nombre ASC LIMIT $inicio,$registros";
				$consulta_total="SELECT COUNT(proveedor_id) FROM proveedor WHERE proveedor_nombre LIKE '%$busqueda%' OR proveedor_rif LIKE '%$busqueda%'";
			}else{
				$consulta_datos="SELECT * FROM proveedor ORDER BY proveedor_nombre ASC LIMIT $inicio,$registros";
				$consulta_total="SELECT COUNT(proveedor_id) FROM proveedor";
			}

			$datos = $this->ejecutarConsulta($consulta_datos);
			$datos = $datos->fetchAll();
			$total = $this->ejecutarConsulta($consulta_total);
			$total = (int) $total->fetchColumn();
			$numeroPaginas =ceil($total/$registros);

			$tabla.='<div class="table-container">
				<table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
					<thead>
						<tr>
							<th class="has-text-centered">#</th>
							<th class="has-text-centered">Nombre</th>
							<th class="has-text-centered">RIF</th>
							<th class="has-text-centered">Teléfono</th>
							<th class="has-text-centered">Dirección</th>
							<th class="has-text-centered" colspan="2">Opciones</th>
						</tr>
					</thead>
					<tbody>';

			if($total>=1 && $pagina<=$numeroPaginas){
				$contador=$inicio+1;
				$pag_inicio=$inicio+1;
				foreach($datos as $rows){
					$tabla.='
						<tr class="has-text-centered" >
							<td>'.$contador.'</td>
							<td><strong>'.$rows['proveedor_nombre'].'</strong></td>
							<td>'.$rows['proveedor_rif'].'</td>
							<td>'.$rows['proveedor_telefono'].'</td>
							<td>'.$rows['proveedor_direccion'].'</td>
							<td>
								<a href="'.APP_URL.'providerUpdate/'.$rows['proveedor_id'].'/" class="button is-success is-rounded is-small"><i class="fas fa-sync"></i></a>
							</td>
							<td>
								<form class="FormularioAjax" action="'.APP_URL.'app/ajax/proveedorAjax.php" method="POST" autocomplete="off" >
									<input type="hidden" name="modulo_proveedor" value="eliminar">
									<input type="hidden" name="proveedor_id" value="'.$rows['proveedor_id'].'">
									<button type="submit" class="button is-danger is-rounded is-small"><i class="far fa-trash-alt"></i></button>
								</form>
							</td>
						</tr>';
					$contador++;
				}
				$pag_final=$contador-1;
			}else{
				$tabla.='<tr class="has-text-centered" ><td colspan="7">No hay proveedores registrados</td></tr>';
			}

			$tabla.='</tbody></table></div>';
			if($total>0 && $pagina<=$numeroPaginas){
				$tabla.='<p class="has-text-right">Mostrando proveedores <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';
				$tabla.=$this->paginadorTablas($pagina,$numeroPaginas,$url,7);
			}
			return $tabla;
		}

		/*----------  Controlador eliminar proveedor (BLINDADO) ----------*/
		public function eliminarProveedorControlador(){
			$id=$this->limpiarCadena($_POST['proveedor_id']);

		    $datos=$this->ejecutarConsulta("SELECT * FROM proveedor WHERE proveedor_id='$id'");
		    if($datos->rowCount()<=0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Proveedor no encontrado","icono"=>"error"]; return json_encode($alerta); exit(); }
            $datos = $datos->fetch();

            /* AUDITORÍA: Validar que el proveedor NO tenga Compras asociadas */
            $check_compras = $this->ejecutarConsulta("SELECT proveedor_id FROM compra WHERE proveedor_id='$id' LIMIT 1");
            if($check_compras->rowCount() > 0){
                $alerta=["tipo"=>"simple","titulo"=>"Error de Integridad","texto"=>"No se puede eliminar el proveedor porque tiene Órdenes de Compra o Facturas asociadas en el sistema.","icono"=>"error"]; 
                return json_encode($alerta); exit();
            }

		    $eliminarProveedor=$this->eliminarRegistro("proveedor","proveedor_id",$id);
		    if($eliminarProveedor->rowCount()==1){
                /*== AUDITORIA ==*/
                $this->guardarBitacora("Proveedores", "Eliminación", "Se eliminó el proveedor: ".$datos['proveedor_nombre']);

		        $alerta=["tipo"=>"recargar","titulo"=>"Éxito","texto"=>"El proveedor fue eliminado del sistema","icono"=>"success"];
		    }else{
		    	$alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo eliminar el proveedor","icono"=>"error"];
		    }
		    return json_encode($alerta);
		}

		/*----------  Controlador actualizar proveedor ----------*/
		public function actualizarProveedorControlador(){
			$id=$this->limpiarCadena($_POST['proveedor_id']);
		    
            $datos=$this->ejecutarConsulta("SELECT * FROM proveedor WHERE proveedor_id='$id'");
		    if($datos->rowCount()<=0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Proveedor no encontrado","icono"=>"error"]; return json_encode($alerta); exit(); }
            $datos = $datos->fetch();

			$nombre=$this->limpiarCadena($_POST['proveedor_nombre']);
            $rif=$this->limpiarCadena($_POST['proveedor_rif']);
			$telefono=$this->limpiarCadena($_POST['proveedor_telefono']);
			$direccion=$this->limpiarCadena($_POST['proveedor_direccion']);

			if($nombre=="" || $rif==""){
				$alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Los campos Nombre y RIF son obligatorios","icono"=>"error"]; return json_encode($alerta); exit();
			}

			
			if($this->verificarDatos("[0-9\-]{1,15}", $rif)){
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Formato de RIF Inválido",
					"texto"=>"El RIF ingresado tiene caracteres no válidos. Solo se permiten números y guiones (-).",
					"icono"=>"error"
				];
				return json_encode($alerta);
				exit();
			}
			

            // Validar si el RIF se cambió y si ya pertenece a otro proveedor
            if($datos['proveedor_rif'] != $rif){
                $check_rif=$this->ejecutarConsulta("SELECT proveedor_rif FROM proveedor WHERE proveedor_rif='$rif'");
                if($check_rif->rowCount()>0){
                    $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"El RIF ingresado ya está asignado a otro proveedor","icono"=>"error"]; 
                    return json_encode($alerta); exit();
                }
            }

			$proveedor_datos_up=[
				["campo_nombre"=>"proveedor_nombre","campo_marcador"=>":Nombre","campo_valor"=>$nombre],
                ["campo_nombre"=>"proveedor_rif","campo_marcador"=>":Rif","campo_valor"=>$rif],
				["campo_nombre"=>"proveedor_telefono","campo_marcador"=>":Telefono","campo_valor"=>$telefono],
				["campo_nombre"=>"proveedor_direccion","campo_marcador"=>":Direccion","campo_valor"=>$direccion]
			];

			$condicion=["condicion_campo"=>"proveedor_id","condicion_marcador"=>":ID","condicion_valor"=>$id];

			if($this->actualizarDatos("proveedor",$proveedor_datos_up,$condicion)){
                /*== AUDITORIA ==*/
                $this->guardarBitacora("Proveedores", "Actualización", "Se actualizaron datos del proveedor: ".$nombre);

				$alerta=["tipo"=>"recargar","titulo"=>"Éxito","texto"=>"Proveedor actualizado correctamente","icono"=>"success"];
			}else{
				$alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo actualizar los datos","icono"=>"error"];
			}
			return json_encode($alerta);
		}
	}