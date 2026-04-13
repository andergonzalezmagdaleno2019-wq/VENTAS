<?php
	namespace app\controllers;
	use app\models\mainModel;

	class providerController extends mainModel{

		/*---------- Controlador registrar proveedor ----------*/
		public function registrarProveedorControlador(){
			$nombre=$this->limpiarCadena($_POST['proveedor_nombre']);
			$direccion=$this->limpiarCadena($_POST['proveedor_direccion']);
			
			// --- RIF Unificado con guion obligatorio ---
			$rif_tipo = $this->limpiarCadena($_POST['proveedor_rif_tipo']); 
			$rif_numero = $this->limpiarCadena($_POST['proveedor_rif_numero']);
			
			// Limpiamos el número de cualquier guion accidental para garantizar solo uno
			$rif_numero = str_replace("-", "", $rif_numero);
			
			// Formato legal SENIAT: Letra-Número
			$rif = $rif_tipo . "-" . $rif_numero;

			// Teléfono
			$telefono_prefijo = $this->limpiarCadena($_POST['proveedor_telefono_codigo']);
			$telefono_numero = $this->limpiarCadena($_POST['proveedor_telefono']);

			// Validaciones básicas
			if($nombre=="" || $rif_numero==""){
				$alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Faltan campos obligatorios","icono"=>"error"]; 
				return json_encode($alerta); exit();
			}
			
			// Unificación lógica del teléfono
			$telefono_final = ""; 
			if ($telefono_numero != "") {
				if($telefono_prefijo == ""){ 
					$alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Seleccione código de área.","icono" => "error"]; 
					return json_encode($alerta); exit(); 
				}
				$telefono_final = $telefono_prefijo . $telefono_numero;
			}

			// Verificar RIF duplicado 
			$check_rif=$this->ejecutarConsulta("SELECT proveedor_rif FROM proveedor WHERE proveedor_rif='$rif'");
			if($check_rif->rowCount()>0){
				$alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"El RIF ($rif) ya está registrado","icono"=>"error"]; 
				return json_encode($alerta); exit();
			}

			$datos_proveedor_reg = [
				["campo_nombre"=>"proveedor_nombre","campo_marcador"=>":Nombre","campo_valor"=>$nombre],
				["campo_nombre"=>"proveedor_rif","campo_marcador"=>":Rif","campo_valor"=>$rif],
				["campo_nombre"=>"proveedor_telefono","campo_marcador"=>":Telefono","campo_valor"=>$telefono_final],
				["campo_nombre"=>"proveedor_direccion","campo_marcador"=>":Direccion","campo_valor"=>$direccion]
			];

			$registrar_proveedor=$this->guardarDatos("proveedor",$datos_proveedor_reg);

			if($registrar_proveedor->rowCount()==1){
				$alerta=["tipo"=>"limpiar","titulo"=>"¡Éxito!","texto"=>"Proveedor registrado correctamente bajo el RIF: $rif","icono"=>"success"];
			}else{
				$alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo registrar el proveedor","icono"=>"error"];
			}
			return json_encode($alerta);
		}

		/*----------  Controlador listar proveedor ----------*/
        public function listarProveedorControlador($pagina,$registros,$url,$busqueda){
            $pagina=$this->limpiarCadena($pagina); $registros=$this->limpiarCadena($registros); $url=$this->limpiarCadena($url); $url=APP_URL.$url."/"; $busqueda=$this->limpiarCadena($busqueda); $tabla="";
            $pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1; $inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;

            if(isset($busqueda) && $busqueda!=""){
                $consulta_datos="SELECT * FROM proveedor WHERE proveedor_nombre LIKE '%$busqueda%' OR proveedor_rif LIKE '%$busqueda%' ORDER BY proveedor_nombre ASC LIMIT $inicio,$registros";
                $consulta_total="SELECT COUNT(proveedor_id) FROM proveedor WHERE proveedor_nombre LIKE '%$busqueda%' OR proveedor_rif LIKE '%$busqueda%'";
            }else{
                $consulta_datos="SELECT * FROM proveedor ORDER BY proveedor_nombre ASC LIMIT $inicio,$registros";
                $consulta_total="SELECT COUNT(proveedor_id) FROM proveedor";
            }

            $datos = $this->ejecutarConsulta($consulta_datos); $datos = $datos->fetchAll(); $total = $this->ejecutarConsulta($consulta_total); $total = (int) $total->fetchColumn(); $numeroPaginas =ceil($total/$registros);

            $tabla.='<div class="table-container"><table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth"><thead><tr class="has-background-link-light"><th class="has-text-centered">#</th><th class="has-text-centered">Nombre</th><th class="has-text-centered">RIF</th><th class="has-text-centered">Teléfono</th><th class="has-text-centered">Dirección</th><th class="has-text-centered" colspan="2">Opciones</th></tr></thead><tbody>';

            if($total>=1 && $pagina<=$numeroPaginas){
                $contador=$inicio+1; $pag_inicio=$inicio+1;
                foreach($datos as $rows){
                    // Formatear teléfono para vista
                    $tel_tabla = ($rows['proveedor_telefono'] != "") ? substr($rows['proveedor_telefono'], 0, 4)."-".substr($rows['proveedor_telefono'], 4) : "N/A";
                    

                    $rif_tabla = $rows['proveedor_rif'];
                    if(preg_match('/^[0-9]/', $rif_tabla)){
                        $rif_tabla = "J-" . $rif_tabla;
                    }

                    $tabla.='<tr class="has-text-centered" ><td>'.$contador.'</td><td><strong>'.$rows['proveedor_nombre'].'</strong></td><td>'.$rif_tabla.'</td><td>'.$tel_tabla.'</td><td>'.$rows['proveedor_direccion'].'</td><td><a href="'.APP_URL.'providerUpdate/'.$rows['proveedor_id'].'/" class="button is-success is-rounded is-small"><i class="fas fa-sync"></i></a></td><td><form class="FormularioAjax" action="'.APP_URL.'app/ajax/proveedorAjax.php" method="POST" autocomplete="off" data-pregunta="¿Desea eliminar este proveedor? Se perderá el enlace directo para futuras órdenes de compra, aunque el historial de compras pasadas se mantendrá intacto."><input type="hidden" name="modulo_proveedor" value="eliminar"><input type="hidden" name="proveedor_id" value="'.$rows['proveedor_id'].'"><button type="submit" class="button is-danger is-rounded is-small"><i class="far fa-trash-alt"></i></button></form></td></tr>';
                    $contador++;
                }
                $pag_final=$contador-1;
            }else{ $tabla.='<tr class="has-text-centered" ><td colspan="7">No hay proveedores registrados</td></tr>'; }

            $tabla.='</tbody></table></div>';
            if($total>0 && $pagina<=$numeroPaginas){ $tabla.='<p class="has-text-right">Mostrando proveedores <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>'; $tabla.=$this->paginadorTablas($pagina,$numeroPaginas,$url,7); }
            return $tabla;
        }
		
		/*----------  Controlador eliminar proveedor  ----------*/
		public function eliminarProveedorControlador(){
			$id=$this->limpiarCadena($_POST['proveedor_id']);
		    $datos=$this->ejecutarConsulta("SELECT * FROM proveedor WHERE proveedor_id='$id'");
		    if($datos->rowCount()<=0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Proveedor no encontrado","icono"=>"error"]; return json_encode($alerta); exit(); }
            $datos = $datos->fetch();

            /* AUDITORÍA: Validar que el proveedor NO tenga Compras asociadas */
            $check_compras = $this->ejecutarConsulta("SELECT proveedor_id FROM compra WHERE proveedor_id='$id' LIMIT 1");
            if($check_compras->rowCount() > 0){
                $alerta=["tipo"=>"simple","titulo"=>"Error de Integridad","texto"=>"No se puede eliminar el proveedor porque tiene Órdenes de Compra o Facturas asociadas.","icono"=>"error"]; return json_encode($alerta); exit();
            }

		    $eliminarProveedor=$this->eliminarRegistro("proveedor","proveedor_id",$id);
		    if($eliminarProveedor->rowCount()==1){
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

            // Verificando proveedor
            $datos=$this->ejecutarConsulta("SELECT * FROM proveedor WHERE proveedor_id='$id'");
            if($datos->rowCount()<=0){ 
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Proveedor no encontrado","icono"=>"error"]; 
                return json_encode($alerta); exit(); 
            }
            $datos = $datos->fetch();

            $nombre=$this->limpiarCadena($_POST['proveedor_nombre']);
            $direccion=$this->limpiarCadena($_POST['proveedor_direccion']);

            // --- CAPTURAMOS EL RIF DIVIDIDO ---
            $rif_tipo = $this->limpiarCadena($_POST['proveedor_rif_tipo']);
            $rif_numero = $this->limpiarCadena($_POST['proveedor_rif_numero']);
            $rif = $rif_tipo . "-" . $rif_numero; 

            // CAPTURAMOS EL TELÉFONO DIVIDIDO
            $telefono_prefijo = $this->limpiarCadena($_POST['proveedor_telefono_codigo']);
            $telefono_numero = $this->limpiarCadena($_POST['proveedor_telefono']);

            if($nombre=="" || $rif_numero==""){ 
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Los campos Nombre y RIF son obligatorios","icono"=>"error"]; 
                return json_encode($alerta); exit(); 
            }
            
            # VALIDACIÓN: El nombre debe tener letras #
            if (!preg_match("/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/", $nombre)) {
                $alerta = ["tipo" => "simple", "titulo" => "Nombre Inválido", "texto" => "El nombre del proveedor debe contener letras.", "icono" => "error"]; 
                return json_encode($alerta); exit();
            }

            // Validamos formato de RIF unificado
            if(!preg_match("/^[V|E|J|G|P]-[0-9]{6,10}$/", $rif)){ 
                $alerta=["tipo"=>"simple","titulo"=>"Formato Inválido","texto"=>"El RIF no tiene un formato válido (Letra-Número).","icono"=>"error"]; 
                return json_encode($alerta); exit(); 
            }
            
            # VALIDACIÓN Y UNIFICACIÓN DE TELÉFONO #
            $telefono_final = "";
            if ($telefono_numero != "") {
                if($telefono_prefijo == ""){ 
                    $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "Debe seleccionar un código de área.", "icono" => "error"]; 
                    return json_encode($alerta); exit(); 
                }
                if (!preg_match("/^[0-9]{7}$/", $telefono_numero)) { 
                    $alerta = ["tipo" => "simple", "titulo" => "Error", "texto" => "El número de teléfono debe tener 7 dígitos.", "icono" => "error"]; 
                    return json_encode($alerta); exit(); 
                }
                $telefono_final = $telefono_prefijo . $telefono_numero;
            }

            // Verificar si el RIF cambió y si el nuevo ya existe
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
                ["campo_nombre"=>"proveedor_telefono","campo_marcador"=>":Telefono","campo_valor"=>$telefono_final],
                ["campo_nombre"=>"proveedor_direccion","campo_marcador"=>":Direccion","campo_valor"=>$direccion]
            ];

            $condicion=["condicion_campo"=>"proveedor_id","condicion_marcador"=>":ID","condicion_valor"=>$id];

            if($this->actualizarDatos("proveedor",$proveedor_datos_up,$condicion)){
                $this->guardarBitacora("Proveedores", "Actualización", "Se actualizaron datos del proveedor: ".$nombre);
                $alerta=[
					"tipo"=>"redireccionar",
					"titulo"=>"¡Éxito!",
					"texto"=>"Proveedor actualizado correctamente",
					"icono"=>"success",
					"url"=>APP_URL."providerList/" 
				];
            }else{
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo actualizar los datos","icono"=>"error"];
            }
            return json_encode($alerta);
        }
	}