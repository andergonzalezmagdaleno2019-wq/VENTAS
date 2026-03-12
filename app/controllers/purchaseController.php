<?php
	namespace app\controllers;
	use app\models\mainModel;

	class purchaseController extends mainModel{

		/*----------  Controlador buscar producto para compra  ----------*/
		public function buscarProductoCompraControlador(){
			$codigo=$this->limpiarCadena($_POST['buscar_producto']);
			if($codigo==""){
				return '<div class="notification is-danger is-light"><strong>¡Ocurrió un error!</strong><br>Debes introducir el Nombre o Código del producto</div>';
			}
			$datos=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_nombre LIKE '%$codigo%' OR producto_codigo LIKE '%$codigo%' ORDER BY producto_nombre ASC");

			if($datos->rowCount()>=1){
				$datos=$datos->fetchAll();
				$tabla='<div class="table-container"><table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth"><thead><tr><th class="has-text-centered">Producto</th><th class="has-text-centered">Stock Actual</th><th class="has-text-centered">Costo Actual</th><th class="has-text-centered">Agregar</th></tr></thead><tbody>';
				foreach($datos as $rows){
					$tabla.='<tr class="has-text-centered"><td>'.$rows['producto_nombre'].' ('.$rows['producto_codigo'].')</td><td>'.$rows['producto_stock'].'</td><td>$'.$rows['producto_costo'].'</td><td><form class="FormularioAjax" action="'.APP_URL.'app/ajax/compraAjax.php" method="POST" autocomplete="off"><input type="hidden" name="modulo_compra" value="agregar"><input type="hidden" name="producto_id" value="'.$rows['producto_id'].'"><div class="field has-addons"><div class="control"><input class="input is-small" type="number" name="compra_cantidad" placeholder="Cant." required min="1" style="width: 70px;"></div><div class="control"><input class="input is-small" type="text" name="compra_costo" placeholder="Costo $" required style="width: 80px;"></div><div class="control"><button type="submit" class="button is-success is-small"><i class="fas fa-plus"></i></button></div></div></form></td></tr>';
				}
				$tabla.='</tbody></table></div>';
				return $tabla;
			}else{ return '<div class="notification is-warning is-light"><strong>¡No encontrado!</strong><br>No hemos encontrado ningún producto con ese código o nombre.</div>'; }
		}

        /*----------  Controlador buscar producto por categoría (COMPRA) ----------*/
		public function buscarPorCategoriaCompraControlador(){
			$categoria_id = $this->limpiarCadena($_POST['categoria_id']);
			if($categoria_id=="" || !is_numeric($categoria_id)){
				return '<div class="notification is-warning is-light"><strong>¡Ocurrió un error!</strong><br>Categoría inválida</div>'; exit();
			}

			$datos=$this->ejecutarConsulta("SELECT * FROM producto WHERE categoria_id='$categoria_id' ORDER BY producto_nombre ASC");

			if($datos->rowCount()>=1){
				$datos=$datos->fetchAll();
				$tabla='<div class="table-container"><table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth"><thead><tr><th class="has-text-centered">Producto</th><th class="has-text-centered">Stock Actual</th><th class="has-text-centered">Costo Actual</th><th class="has-text-centered">Agregar</th></tr></thead><tbody>';
				foreach($datos as $rows){
                    // NOTA: Pre-cargamos el input de Costo con el costo actual de la BD para que sea más rápido
					$tabla.='<tr class="has-text-centered"><td>'.$rows['producto_nombre'].' ('.$rows['producto_codigo'].')</td><td>'.$rows['producto_stock'].'</td><td>$'.$rows['producto_costo'].'</td><td><form class="FormularioAjax" action="'.APP_URL.'app/ajax/compraAjax.php" method="POST" autocomplete="off"><input type="hidden" name="modulo_compra" value="agregar"><input type="hidden" name="producto_id" value="'.$rows['producto_id'].'"><div class="field has-addons is-justify-content-center"><div class="control"><input class="input is-small" type="number" name="compra_cantidad" placeholder="Cant." required min="1" style="width: 70px;"></div><div class="control"><input class="input is-small" type="text" name="compra_costo" placeholder="Costo $" value="'.$rows['producto_costo'].'" required style="width: 80px;"></div><div class="control"><button type="submit" class="button is-success is-small"><i class="fas fa-plus"></i></button></div></div></form></td></tr>';
				}
				$tabla.='</tbody></table></div>';
				return $tabla;
			}else{ return '<div class="notification is-warning is-light"><strong>¡No encontrado!</strong><br>No hemos encontrado ningún producto en esta categoría.</div>'; }
		}

		/*----------  Controlador agregar producto al carrito  ----------*/
		public function agregarProductoCompraControlador(){
			$id=$this->limpiarCadena($_POST['producto_id']);
			$cantidad=$this->limpiarCadena($_POST['compra_cantidad']);
			$costo=$this->limpiarCadena($_POST['compra_costo']);
			if($cantidad<=0 || $costo<=0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"La cantidad y el costo deben ser mayores a 0","icono"=>"error"]; return json_encode($alerta); exit(); }
            $check_producto=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='$id'");
            if($check_producto->rowCount()<=0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"El producto no existe","icono"=>"error"]; return json_encode($alerta); exit(); }
            $campos=$check_producto->fetch();

            if(!isset($_SESSION['datos_compra'])){ $_SESSION['datos_compra']=[]; }
            $detalle=[ "producto_id"=>$campos['producto_id'], "producto_codigo"=>$campos['producto_codigo'], "producto_nombre"=>$campos['producto_nombre'], "compra_cantidad"=>$cantidad, "compra_costo"=>$costo, "subtotal"=>$cantidad*$costo ];
            $_SESSION['datos_compra'][$id]=$detalle;

            $alerta=["tipo"=>"recargar","titulo"=>"Producto agregado","texto"=>"El producto se agregó a la compra","icono"=>"success"];
			return json_encode($alerta);
		}

        /*----------  Controlador vaciar carrito  ----------*/
        public function vaciarCompraControlador(){
            unset($_SESSION['datos_compra']);
            $alerta=["tipo"=>"recargar","titulo"=>"Compra vaciada","texto"=>"Se han quitado todos los productos","icono"=>"success"];
			return json_encode($alerta);
        }

		/*----------  Controlador registrar compra  ----------*/
		public function registrarCompraControlador(){
			if(!isset($_SESSION['datos_compra']) || count($_SESSION['datos_compra'])<=0){ $alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error","texto"=>"No tienes productos agregados para comprar","icono"=>"error"]; return json_encode($alerta); exit(); }

			$proveedor=$this->limpiarCadena($_POST['compra_proveedor']);
            $compra_tasa_bcv = $this->limpiarCadena($_POST['compra_tasa_bcv']);
            if(!is_numeric($compra_tasa_bcv) || $compra_tasa_bcv == ""){ $compra_tasa_bcv = 0; }

            $fecha=date("Y-m-d"); $total=0;
            foreach($_SESSION['datos_compra'] as $productos){ $total+=$productos['subtotal']; }
            
            // --- GENERADOR DE CÓDIGO BLINDADO (COMPRAS) ---
            $consulta_correlativo = $this->ejecutarConsulta("SELECT MAX(compra_id) AS id_maximo FROM compra");
            $resultado_correlativo = $consulta_correlativo->fetch();
            $siguiente_numero = (int)$resultado_correlativo['id_maximo'] + 1;
            
            $codigo_compra = "COM-" . str_pad($siguiente_numero, 6, "0", STR_PAD_LEFT);
            // ----------------------------------------------
            
            $datos_compra_reg=[
				["campo_nombre"=>"compra_codigo","campo_marcador"=>":Codigo","campo_valor"=>$codigo_compra],
				["campo_nombre"=>"compra_fecha","campo_marcador"=>":Fecha","campo_valor"=>$fecha],
				["campo_nombre"=>"compra_total","campo_marcador"=>":Total","campo_valor"=>$total],
                ["campo_nombre"=>"compra_tasa_bcv","campo_marcador"=>":Tasa","campo_valor"=>$compra_tasa_bcv],
				["campo_nombre"=>"usuario_id","campo_marcador"=>":Usuario","campo_valor"=>$_SESSION['id']],
                ["campo_nombre"=>"proveedor_id","campo_marcador"=>":Proveedor","campo_valor"=>$proveedor]
			];

            $registrar_compra=$this->guardarDatos("compra",$datos_compra_reg);

            if($registrar_compra->rowCount()==1){
                foreach($_SESSION['datos_compra'] as $detalle){
                    $datos_detalle=[ ["campo_nombre"=>"compra_codigo","campo_marcador"=>":Codigo","campo_valor"=>$codigo_compra], ["campo_nombre"=>"producto_id","campo_marcador"=>":Producto","campo_valor"=>$detalle['producto_id']], ["campo_nombre"=>"compra_detalle_cantidad","campo_marcador"=>":Cantidad","campo_valor"=>$detalle['compra_cantidad']], ["campo_nombre"=>"compra_detalle_precio","campo_marcador"=>":Precio","campo_valor"=>$detalle['compra_costo']] ];
                    $this->guardarDatos("compra_detalle",$datos_detalle);

                    // --- AJUSTE DE PRECIO INTELIGENTE ---
                    $info_prod = $this->conectar()->prepare("SELECT producto_costo, producto_precio FROM producto WHERE producto_id=:ID");
                    $info_prod->bindValue(":ID", $detalle['producto_id']);
                    $info_prod->execute();
                    $info_prod = $info_prod->fetch();

                    $nuevo_costo = $detalle['compra_costo'];

                    if($info_prod['producto_costo'] > 0){
                        $porcentaje_ganancia = ($info_prod['producto_precio'] - $info_prod['producto_costo']) / $info_prod['producto_costo'];
                        $nuevo_precio_venta = $nuevo_costo + ($nuevo_costo * $porcentaje_ganancia);
                    } else {
                        $nuevo_precio_venta = $nuevo_costo * 1.20; 
                    }
                    // ------------------------------------
                    
                    $update_producto = $this->conectar()->prepare("UPDATE producto SET producto_stock = producto_stock + :Cantidad, producto_costo = :Costo, producto_precio = :Precio WHERE producto_id = :ID");
                    $update_producto->bindValue(":Cantidad", $detalle['compra_cantidad']);
                    $update_producto->bindValue(":Costo", $nuevo_costo);
                    $update_producto->bindValue(":Precio", $nuevo_precio_venta);
                    $update_producto->bindValue(":ID", $detalle['producto_id']);
                    $update_producto->execute();
                }
                unset($_SESSION['datos_compra']);
                $alerta=["tipo"=>"recargar","titulo"=>"Compra registrada","texto"=>"La compra se registró y el inventario se actualizó correctamente","icono"=>"success"];
            }else{ $alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error","texto"=>"No se pudo registrar la compra","icono"=>"error"]; }
			return json_encode($alerta);
		}

        /*----------  Controlador listar compras (CON BS) ----------*/
        public function listarCompraControlador($pagina,$registros,$url,$busqueda){
            $pagina=$this->limpiarCadena($pagina); $registros=$this->limpiarCadena($registros); $url=$this->limpiarCadena($url); $url=APP_URL.$url."/"; $busqueda=$this->limpiarCadena($busqueda); $tabla="";
			$pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1; $inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;

            if(isset($busqueda) && $busqueda!=""){
				$consulta_datos="SELECT * FROM compra INNER JOIN proveedor ON compra.proveedor_id=proveedor.proveedor_id INNER JOIN usuario ON compra.usuario_id=usuario.usuario_id WHERE compra.compra_codigo LIKE '%$busqueda%' OR proveedor.proveedor_nombre LIKE '%$busqueda%' ORDER BY compra.compra_id DESC LIMIT $inicio,$registros";
				$consulta_total="SELECT COUNT(compra_id) FROM compra INNER JOIN proveedor ON compra.proveedor_id=proveedor.proveedor_id WHERE compra.compra_codigo LIKE '%$busqueda%' OR proveedor.proveedor_nombre LIKE '%$busqueda%'";
			}else{
				$consulta_datos="SELECT * FROM compra INNER JOIN proveedor ON compra.proveedor_id=proveedor.proveedor_id INNER JOIN usuario ON compra.usuario_id=usuario.usuario_id ORDER BY compra.compra_id DESC LIMIT $inicio,$registros";
				$consulta_total="SELECT COUNT(compra_id) FROM compra";
			}

            $datos = $this->ejecutarConsulta($consulta_datos); $datos = $datos->fetchAll(); $total = $this->ejecutarConsulta($consulta_total); $total = (int) $total->fetchColumn(); $numeroPaginas =ceil($total/$registros);

            $tabla.='<div class="table-container"><table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth"><thead><tr class="has-background-link-light"><th class="has-text-centered">N°</th><th class="has-text-centered">Código</th><th class="has-text-centered">Fecha</th><th class="has-text-centered">Proveedor</th><th class="has-text-centered">Usuario</th><th class="has-text-centered">Monto Invertido</th><th class="has-text-centered">Opciones</th></tr></thead><tbody>';

            if($total>=1 && $pagina<=$numeroPaginas){
				$contador=$inicio+1; $pag_inicio=$inicio+1;
				foreach($datos as $rows){

                    // Cálculo de Bolívares para la tabla de compras
                    $tasa = (isset($rows['compra_tasa_bcv']) && $rows['compra_tasa_bcv'] > 0) ? $rows['compra_tasa_bcv'] : 0;
                    $total_bs = $rows['compra_total'] * $tasa;
                    $str_bs = ($tasa > 0) ? 'Bs. '.number_format($total_bs, 2, ',', '.') : '<small class="has-text-grey">N/A</small>';

					$tabla.='<tr class="has-text-centered" >
                                <td>'.$contador.'</td>
                                <td>'.$rows['compra_codigo'].'</td>
                                <td>'.date("d-m-Y", strtotime($rows['compra_fecha'])).'</td>
                                <td>'.$rows['proveedor_nombre'].'</td>
                                <td>'.$rows['usuario_usuario'].'</td>
                                <td>
                                    <strong>$'.number_format($rows['compra_total'],2).'</strong><br>
                                    <span class="has-text-link is-size-7">'.$str_bs.'</span>
                                </td>
                                <td>
                                    <button type="button" class="button is-link is-outlined is-rounded is-small" onclick="print_invoice(\''.APP_URL.'app/pdf/purchase_order.php?code='.$rows['compra_codigo'].'\')" title="Imprimir Orden de Compra (PDF)" ><i class="fas fa-file-pdf fa-fw"></i></button>
                                    <form class="FormularioAjax is-inline-block" action="'.APP_URL.'app/ajax/compraAjax.php" method="POST" autocomplete="off" ><input type="hidden" name="modulo_compra" value="eliminar"><input type="hidden" name="compra_id" value="'.$rows['compra_id'].'"><button type="submit" class="button is-danger is-rounded is-small" title="Anular Compra y Devolver Stock"><i class="far fa-trash-alt fa-fw"></i></button></form>
                                </td>
                            </tr>';
					$contador++;
				} $pag_final=$contador-1;
			}else{
				if($total>=1){ $tabla.='<tr class="has-text-centered"><td colspan="7"><a href="'.$url.'1/" class="button is-link is-rounded is-small mt-4 mb-4">Haga clic acá para recargar el listado</a></td></tr>';
				}else{ $tabla.='<tr class="has-text-centered"><td colspan="7">No hay registros en el sistema</td></tr>'; }
			}
			$tabla.='</tbody></table></div>';
			if($total>0 && $pagina<=$numeroPaginas){ $tabla.='<p class="has-text-right">Mostrando compras <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>'; $tabla.=$this->paginadorTablas($pagina,$numeroPaginas,$url,7); }
			return $tabla;
        }

        /*----------  Controlador eliminar compra (ANULAR)  ----------*/
        public function eliminarCompraControlador(){
            $id=$this->limpiarCadena($_POST['compra_id']);
            $datos=$this->ejecutarConsulta("SELECT * FROM compra WHERE compra_id='$id'");
            if($datos->rowCount()<=0){ $alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error","texto"=>"La compra no existe","icono"=>"error"]; return json_encode($alerta); exit(); }
            $datos_compra=$datos->fetch();
            $detalle = $this->ejecutarConsulta("SELECT * FROM compra_detalle WHERE compra_codigo='".$datos_compra['compra_codigo']."'");
            $detalle = $detalle->fetchAll();
            foreach($detalle as $producto_comprado){
                $update = $this->conectar()->prepare("UPDATE producto SET producto_stock = producto_stock - :Cantidad WHERE producto_id = :ID");
                $update->bindValue(":Cantidad", $producto_comprado['compra_detalle_cantidad']); $update->bindValue(":ID", $producto_comprado['producto_id']); $update->execute();
            }
            $eliminarDetalle = $this->eliminarRegistro("compra_detalle","compra_codigo",$datos_compra['compra_codigo']);
            $eliminarCompra = $this->eliminarRegistro("compra","compra_id",$id);
            if($eliminarCompra->rowCount()==1){ $alerta=["tipo"=>"recargar","titulo"=>"Compra anulada","texto"=>"La compra ha sido eliminada y el stock ha sido revertido","icono"=>"success"]; }else{ $alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error","texto"=>"No se pudo eliminar la compra","icono"=>"error"]; }
            return json_encode($alerta);
        }
	}