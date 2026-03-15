<?php
	namespace app\controllers;
	use app\models\mainModel;

	class purchaseController extends mainModel{

		/*=============================================
		=            CARRITO Y BÚSQUEDAS              =
		=============================================*/

		/*----------  Buscar producto (SIN COSTO) ----------*/
		public function buscarProductoCompraControlador(){
			$codigo=$this->limpiarCadena($_POST['buscar_producto']);
			if($codigo==""){ return '<div class="notification is-danger is-light">Debes introducir un Nombre o Código</div>'; }
			$datos=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_nombre LIKE '%$codigo%' OR producto_codigo LIKE '%$codigo%' ORDER BY producto_nombre ASC");

			if($datos->rowCount()>=1){
				$datos=$datos->fetchAll();
				$tabla='<div class="table-container"><table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth"><thead><tr><th class="has-text-centered">Producto</th><th class="has-text-centered">Stock</th><th class="has-text-centered">Pedir</th></tr></thead><tbody>';
				foreach($datos as $rows){
					$tabla.='<tr class="has-text-centered"><td>'.$rows['producto_nombre'].' ('.$rows['producto_codigo'].')</td><td>'.$rows['producto_stock'].'</td><td><form class="FormularioAjax" action="'.APP_URL.'app/ajax/compraAjax.php" method="POST" autocomplete="off"><input type="hidden" name="modulo_compra" value="agregar"><input type="hidden" name="producto_id" value="'.$rows['producto_id'].'"><input type="hidden" name="compra_costo" value="0"><div class="field has-addons is-justify-content-center"><div class="control"><input class="input is-small" type="number" name="compra_cantidad" placeholder="Cant." required min="1" style="width: 100px;"></div><div class="control"><button type="submit" class="button is-success is-small">Añadir</button></div></div></form></td></tr>';
				}
				$tabla.='</tbody></table></div>';
				return $tabla;
			}else{ return '<div class="notification is-warning is-light">No encontrado.</div>'; }
		}

        /*----------  Buscar por categoría (SIN COSTO) ----------*/
		public function buscarPorCategoriaCompraControlador(){
			$categoria_id = $this->limpiarCadena($_POST['categoria_id']);
			if($categoria_id=="" || !is_numeric($categoria_id)){ return '<div class="notification is-warning is-light">Categoría inválida</div>'; exit(); }
			$datos=$this->ejecutarConsulta("SELECT * FROM producto WHERE categoria_id='$categoria_id' ORDER BY producto_nombre ASC");

			if($datos->rowCount()>=1){
				$datos=$datos->fetchAll();
				$tabla='<div class="table-container"><table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth"><thead><tr><th class="has-text-centered">Producto</th><th class="has-text-centered">Stock</th><th class="has-text-centered">Pedir</th></tr></thead><tbody>';
				foreach($datos as $rows){
					$tabla.='<tr class="has-text-centered"><td>'.$rows['producto_nombre'].' ('.$rows['producto_codigo'].')</td><td>'.$rows['producto_stock'].'</td><td><form class="FormularioAjax" action="'.APP_URL.'app/ajax/compraAjax.php" method="POST" autocomplete="off"><input type="hidden" name="modulo_compra" value="agregar"><input type="hidden" name="producto_id" value="'.$rows['producto_id'].'"><input type="hidden" name="compra_costo" value="0"><div class="field has-addons is-justify-content-center"><div class="control"><input class="input is-small" type="number" name="compra_cantidad" placeholder="Cant." required min="1" style="width: 100px;"></div><div class="control"><button type="submit" class="button is-success is-small">Añadir</button></div></div></form></td></tr>';
				}
				$tabla.='</tbody></table></div>';
				return $tabla;
			}else{ return '<div class="notification is-warning is-light">No hay productos en esta categoría.</div>'; }
		}

		/*----------  Controlador agregar producto al carrito  ----------*/
		public function agregarProductoCompraControlador(){
			$id=$this->limpiarCadena($_POST['producto_id']);
			$cantidad=$this->limpiarCadena($_POST['compra_cantidad']);
			$costo=$this->limpiarCadena($_POST['compra_costo']); // Siempre vendrá en 0 por la orden
			
			if($cantidad<=0){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"La cantidad debe ser mayor a 0","icono"=>"error"]); exit(); }
            
            $check_producto=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='$id'");
            if($check_producto->rowCount()<=0){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"El producto no existe","icono"=>"error"]); exit(); }
            $campos=$check_producto->fetch();

            if(!isset($_SESSION['datos_compra'])){ $_SESSION['datos_compra']=[]; }
            
            // Guardamos el costo de la base de datos como "Referencia Estimada"
            $costo_ref = $campos['producto_costo'];

            $detalle=[ 
                "producto_id"=>$campos['producto_id'], 
                "producto_codigo"=>$campos['producto_codigo'], 
                "producto_nombre"=>$campos['producto_nombre'], 
                "compra_cantidad"=>$cantidad, 
                "compra_costo"=>$costo, 
                "subtotal"=>$cantidad*$costo,
                "costo_referencia"=>$costo_ref 
            ];
            
            $_SESSION['datos_compra'][$id]=$detalle;

			return json_encode(["tipo"=>"recargar","titulo"=>"Agregado","texto"=>"Se agregó a la orden","icono"=>"success"]);
		}

        /*----------  Quitar un producto del carrito ----------*/
        public function eliminarProductoCarritoControlador(){
            if(!isset($_POST['producto_id'])){ return json_encode(["tipo" => "simple", "titulo" => "Error", "texto" => "Sin ID", "icono" => "error"]); }
            $id = $this->limpiarCadena($_POST['producto_id']);
            if(isset($_SESSION['datos_compra'][$id])){
                unset($_SESSION['datos_compra'][$id]);
                return json_encode(["tipo"=>"recargar", "titulo"=>"Producto quitado", "texto"=>"Se eliminó de la lista", "icono"=>"success"]);
            }
            return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"No encontrado", "icono"=>"error"]);
        }

        public function vaciarCompraControlador(){
            unset($_SESSION['datos_compra']);
            return json_encode(["tipo"=>"recargar","titulo"=>"Vaciado","texto"=>"Orden limpiada","icono"=>"success"]);
        }

		/*=============================================
		=       REGISTRO DE ORDEN Y FINANZAS          =
		=============================================*/

		
        
    
        public function registrarCompraControlador(){
            if(!isset($_SESSION['datos_compra']) || count($_SESSION['datos_compra'])<=0){ 
                return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"No tienes productos","icono"=>"error"]); exit(); 
            }

            $proveedor=$this->limpiarCadena($_POST['compra_proveedor']);
            $compra_tasa_bcv = $this->limpiarCadena($_POST['compra_tasa_bcv']);
            $compra_nota = isset($_POST['compra_nota']) ? $this->limpiarCadena($_POST['compra_nota']) : "";
            $pago_inicial = isset($_POST['compra_pago_inicial']) ? (float)$this->limpiarCadena($_POST['compra_pago_inicial']) : 0;
           

            if(!is_numeric($compra_tasa_bcv)){ $compra_tasa_bcv = 0; }
            if($proveedor==""){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"Selecciona proveedor","icono"=>"error"]); exit(); }

            $fecha=date("Y-m-d"); 
            
            
            $total_estimado = 0;
            foreach($_SESSION['datos_compra'] as $detalle){
                $total_estimado += ($detalle['compra_cantidad'] * $detalle['costo_referencia']);
            }
            
            
            if($pago_inicial > $total_estimado && $total_estimado > 0){
                return json_encode(["tipo"=>"simple","titulo"=>"Anticipo Inválido","texto"=>"No puedes dar un anticipo ($".$pago_inicial.") mayor al total estimado de la orden ($".$total_estimado.").","icono"=>"error"]); exit();
            }

            
            $saldo_pendiente = $total_estimado - $pago_inicial;
            $estado_pago = ($saldo_pendiente <= 0 && $pago_inicial > 0) ? "Pagado" : "Pendiente";
            $fecha_vencimiento = date("Y-m-d"); 
            
            $consulta_correlativo = $this->ejecutarConsulta("SELECT MAX(compra_id) AS id_maximo FROM compra");
            $siguiente_numero = (int)$consulta_correlativo->fetch()['id_maximo'] + 1;
            $codigo_compra = "COM-" . str_pad($siguiente_numero, 6, "0", STR_PAD_LEFT);
            
            $datos_compra_reg=[
                ["campo_nombre"=>"compra_codigo","campo_marcador"=>":Codigo","campo_valor"=>$codigo_compra],
                ["campo_nombre"=>"compra_fecha","campo_marcador"=>":Fecha","campo_valor"=>$fecha],
                ["campo_nombre"=>"compra_total","campo_marcador"=>":Total","campo_valor"=>$total_estimado],
                ["campo_nombre"=>"compra_tasa_bcv","campo_marcador"=>":Tasa","campo_valor"=>$compra_tasa_bcv],
                ["campo_nombre"=>"usuario_id","campo_marcador"=>":Usuario","campo_valor"=>$_SESSION['id']],
                ["campo_nombre"=>"proveedor_id","campo_marcador"=>":Proveedor","campo_valor"=>$proveedor],
                ["campo_nombre"=>"compra_estado","campo_marcador"=>":Estado","campo_valor"=>"Pendiente"],
                ["campo_nombre"=>"compra_nota_interna","campo_marcador"=>":Nota","campo_valor"=>$compra_nota],
                ["campo_nombre"=>"compra_saldo_pendiente","campo_marcador"=>":Saldo","campo_valor"=>$saldo_pendiente],
                ["campo_nombre"=>"compra_estado_pago","campo_marcador"=>":EstadoPago","campo_valor"=>$estado_pago],
                ["campo_nombre"=>"compra_fecha_vencimiento","campo_marcador"=>":Vencimiento","campo_valor"=>$fecha_vencimiento]
            ];

            $registrar_compra=$this->guardarDatos("compra",$datos_compra_reg);

            if($registrar_compra->rowCount()==1){
                $id_compra_p = $this->ejecutarConsulta("SELECT compra_id FROM compra WHERE compra_codigo='$codigo_compra'")->fetch()['compra_id'];

                // Registrar anticipo si el cajero metió dinero
                if($pago_inicial > 0){
                    $datos_pago_inicial=[
                        ["campo_nombre"=>"compra_id","campo_marcador"=>":IdCompra","campo_valor"=>$id_compra_p],
                        ["campo_nombre"=>"usuario_id","campo_marcador"=>":Usuario","campo_valor"=>$_SESSION['id']],
                        ["campo_nombre"=>"pago_fecha","campo_marcador"=>":Fecha","campo_valor"=>$fecha],
                        ["campo_nombre"=>"pago_monto","campo_marcador"=>":Monto","campo_valor"=>$pago_inicial],
                        ["campo_nombre"=>"pago_metodo","campo_marcador"=>":Metodo","campo_valor"=>"Efectivo"],
                        ["campo_nombre"=>"pago_referencia","campo_marcador"=>":Ref","campo_valor"=>"Anticipo al generar orden"]
                    ];
                    $this->guardarDatos("compra_pagos",$datos_pago_inicial);
                }

                foreach($_SESSION['datos_compra'] as $detalle){
                    $datos_detalle=[ 
                        ["campo_nombre"=>"compra_id","campo_marcador"=>":IdCompra","campo_valor"=>$id_compra_p], 
                        ["campo_nombre"=>"producto_id","campo_marcador"=>":Producto","campo_valor"=>$detalle['producto_id']], 
                        ["campo_nombre"=>"compra_detalle_cantidad","campo_marcador"=>":Cantidad","campo_valor"=>$detalle['compra_cantidad']], 
                        ["campo_nombre"=>"compra_detalle_precio","campo_marcador"=>":Precio","campo_valor"=>0] // El precio real queda en 0 esperando la factura
                    ];
                    $this->guardarDatos("compra_detalle",$datos_detalle);
                }
                
                unset($_SESSION['datos_compra']);
                return json_encode([
                    "tipo"=>"confirmar",
                    "titulo"=>"Orden ".$codigo_compra." Generada",
                    "texto"=>"¿Desea imprimir la NOTA DE ENTREGA para enviarla al proveedor ahora?",
                    "icono"=>"success",
                    "confirmButtonText" => "Sí, imprimir",
                    "cancelButtonText" => "No, después",
                    "url"=>APP_URL."app/pdf/purchase_order.php?code=".$codigo_compra
                ]);
            }else{ 
                return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo registrar","icono"=>"error"]); 
            }
        }

        /*---------- Función Inteligente: Actualizar Saldos Financieros ----------*/
        protected function actualizarSaldosCompra($compra_id){
            $total_factura = (float) $this->ejecutarConsulta("SELECT compra_total FROM compra WHERE compra_id='$compra_id'")->fetchColumn();
            $total_pagado = (float) $this->ejecutarConsulta("SELECT SUM(pago_monto) FROM compra_pagos WHERE compra_id='$compra_id'")->fetchColumn();
            
            $nuevo_saldo = $total_factura - $total_pagado;
            
            if($nuevo_saldo <= 0 && $total_factura > 0){
                $estado = "Pagado";
            } elseif ($total_pagado > 0 && $nuevo_saldo > 0) {
                $estado = "Parcial";
            } else {
                $estado = "Pendiente";
            }

            $this->ejecutarConsulta("UPDATE compra SET compra_saldo_pendiente='$nuevo_saldo', compra_estado_pago='$estado' WHERE compra_id='$compra_id'");
        }

		/*=============================================
		=    RECEPCIÓN DE CAMIÓN Y PRECIOS REALES     =
		=============================================*/

        
        /*---------- Recibir Mercancía (Corrige el total y establece Condición) ----------*/
        public function registrarRecepcionControlador() {
            $compra_id = $this->limpiarCadena($_POST['compra_id']);
            $productos = $_POST['productos_recibidos']; 
            $costos_nuevos = $_POST['costos_recibidos'];
            
            // CAPTURAMOS LOS NUEVOS DATOS FINANCIEROS
            $condicion_pago = $this->limpiarCadena($_POST['compra_condicion']);
            $fecha_vencimiento = $this->limpiarCadena($_POST['compra_fecha_vencimiento']);
            $nota_usuario = isset($_POST['recepcion_nota']) ? $this->limpiarCadena($_POST['recepcion_nota']) : "";
            
            // Unimos la nota del usuario con la condición para que quede en el historial
            $nota_final = "Condición: " . $condicion_pago . " | " . $nota_usuario;
            
            $datos_recepcion = [
                ["campo_nombre"=>"compra_id","campo_marcador"=>":Compra","campo_valor"=>$compra_id],
                ["campo_nombre"=>"usuario_id","campo_marcador"=>":Usuario","campo_valor"=>$_SESSION['id']],
                ["campo_nombre"=>"recepcion_fecha","campo_marcador"=>":Fecha","campo_valor"=>date("Y-m-d")],
                ["campo_nombre"=>"recepcion_nota","campo_marcador"=>":Nota","campo_valor"=>$nota_final]
            ];
            
            $guardar = $this->guardarDatos("recepcion", $datos_recepcion);

            if($guardar->rowCount() == 1){
                $recepcion_id = $this->ejecutarConsulta("SELECT recepcion_id FROM recepcion WHERE compra_id='$compra_id' ORDER BY recepcion_id DESC LIMIT 1")->fetchColumn();

                foreach($productos as $id_prod => $cant_llego) {
                    $cantidad_real = (int)$this->limpiarCadena($cant_llego);
                    $costo_final = isset($costos_nuevos[$id_prod]) ? (float)$this->limpiarCadena($costos_nuevos[$id_prod]) : 0;

                    // Actualizar costo real en la orden
                    if($costo_final > 0){
                        $this->ejecutarConsulta("UPDATE compra_detalle SET compra_detalle_precio = '$costo_final' WHERE compra_id = '$compra_id' AND producto_id = '$id_prod'");
                    }

                    if($cantidad_real > 0) {
                        $this->ejecutarConsulta("INSERT INTO recepcion_detalle (recepcion_id, producto_id, cantidad_recibida) VALUES ('$recepcion_id', '$id_prod', '$cantidad_real')");
                        $this->ejecutarConsulta("UPDATE producto SET producto_stock = producto_stock + $cantidad_real WHERE producto_id = '$id_prod'");
                        
                        if(method_exists($this, 'actualizarPrecioInteligente')){ $this->actualizarPrecioInteligente($id_prod, $compra_id); }
                    }
                }

                // MAGIA FINANCIERA: Recalcular TOTAL de la compra y ACTUALIZAR FECHA DE VENCIMIENTO
                $nuevo_total = (float) $this->ejecutarConsulta("SELECT SUM(compra_detalle_cantidad * compra_detalle_precio) FROM compra_detalle WHERE compra_id='$compra_id'")->fetchColumn();
                
                $this->ejecutarConsulta("UPDATE compra SET compra_total = '$nuevo_total', compra_fecha_vencimiento = '$fecha_vencimiento' WHERE compra_id = '$compra_id'");

                // Sincronizar estados
                $this->actualizarEstadoCompra($compra_id); // Físico
                $this->actualizarSaldosCompra($compra_id); // Financiero

                return json_encode(["tipo" => "redireccionar", "titulo" => "¡Factura Ingresada!", "texto" => "Inventario y Cuentas por Pagar actualizados.", "icono" => "success", "url" => APP_URL."purchaseList/"]);
            } else {
                return json_encode(["tipo" => "simple", "titulo" => "Ocurrió un error", "texto" => "No se pudo registrar.", "icono" => "error"]);
            }
        }

        /*----------  Cerrar Compra Incompleta  ----------*/
        public function cerrarCompraControlador(){
            $id=$this->limpiarCadena($_POST['compra_id']);
            
            $detalles = $this->ejecutarConsulta("SELECT cd.producto_id, 
                (SELECT IFNULL(SUM(rd.cantidad_recibida), 0) FROM recepcion_detalle rd INNER JOIN recepcion r ON rd.recepcion_id = r.recepcion_id WHERE r.compra_id = cd.compra_id AND rd.producto_id = cd.producto_id) as total_recibido
                FROM compra_detalle cd WHERE cd.compra_id = '$id'")->fetchAll();

            foreach($detalles as $row){
                $prod_id = $row['producto_id'];
                $recibido = $row['total_recibido'];
                
                if($recibido == 0){
                    $this->ejecutarConsulta("DELETE FROM compra_detalle WHERE compra_id='$id' AND producto_id='$prod_id'");
                } else {
                    $this->ejecutarConsulta("UPDATE compra_detalle SET compra_detalle_cantidad='$recibido' WHERE compra_id='$id' AND producto_id='$prod_id'");
                }
            }

            $nuevo_total = (float) $this->ejecutarConsulta("SELECT SUM(compra_detalle_cantidad * compra_detalle_precio) FROM compra_detalle WHERE compra_id='$id'")->fetchColumn();
            $this->ejecutarConsulta("UPDATE compra SET compra_estado='Completado', compra_total='$nuevo_total' WHERE compra_id='$id'");
            
            $this->actualizarSaldosCompra($id);

            return json_encode(["tipo"=>"recargar","titulo"=>"Orden Finalizada","texto"=>"Las cantidades se ajustaron a lo real.","icono"=>"success"]);
        }

        /*---------- Funciones Auxiliares ----------*/
        protected function actualizarPrecioInteligente($producto_id, $compra_id){
            $nuevo_costo = $this->ejecutarConsulta("SELECT compra_detalle_precio FROM compra_detalle WHERE compra_id='$compra_id' AND producto_id='$producto_id'")->fetchColumn();
            $info_prod = $this->ejecutarConsulta("SELECT producto_costo, producto_precio FROM producto WHERE producto_id='$producto_id'")->fetch();

            if($info_prod['producto_costo'] > 0){
                $porcentaje_ganancia = ($info_prod['producto_precio'] - $info_prod['producto_costo']) / $info_prod['producto_costo'];
                $nuevo_precio_venta = $nuevo_costo + ($nuevo_costo * $porcentaje_ganancia);
            } else {
                $nuevo_precio_venta = $nuevo_costo * 1.20; 
            }
            $this->ejecutarConsulta("UPDATE producto SET producto_costo='$nuevo_costo', producto_precio='$nuevo_precio_venta' WHERE producto_id='$producto_id'");
        }

        protected function actualizarEstadoCompra($compra_id){
            $total_pedido = (float) $this->ejecutarConsulta("SELECT SUM(compra_detalle_cantidad) FROM compra_detalle WHERE compra_id='$compra_id'")->fetchColumn();
            $total_recibido = (float) $this->ejecutarConsulta("SELECT SUM(rd.cantidad_recibida) FROM recepcion_detalle rd INNER JOIN recepcion r ON rd.recepcion_id=r.recepcion_id WHERE r.compra_id='$compra_id'")->fetchColumn();

            if($total_recibido >= $total_pedido && $total_pedido > 0){
                $nuevo_estado = "Completado";
            } elseif($total_recibido > 0 && $total_recibido < $total_pedido){
                $nuevo_estado = "Parcial";
            } else {
                $nuevo_estado = "Pendiente";
            }
            $this->ejecutarConsulta("UPDATE compra SET compra_estado='$nuevo_estado' WHERE compra_id='$compra_id'");
        }

		/*=============================================
		=   ABONOS, PAGOS Y ANULACIONES (COMPAÑERO)   =
		=============================================*/

        public function registrarAbonoControlador(){
            $id = $this->limpiarCadena($_POST['pago_compra_id']);
            $monto = $this->limpiarCadena($_POST['pago_monto']);
            $metodo = $this->limpiarCadena($_POST['pago_metodo']);
            $referencia = $this->limpiarCadena($_POST['pago_referencia']);
            
            if($monto <= 0){ return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"El monto debe ser mayor a cero", "icono"=>"error"]); }
            if($referencia == "" && ($metodo == "Efectivo" || $metodo == "Divisas")){ $referencia = "EFECTIVO/DIVISA"; } 
            elseif ($referencia == "") { return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"La referencia es obligatoria", "icono"=>"error"]); }

            $saldo_actual = (float) $this->ejecutarConsulta("SELECT compra_saldo_pendiente FROM compra WHERE compra_id='$id'")->fetchColumn();
            if($monto > $saldo_actual){ return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"El monto no puede superar el saldo actual ($$saldo_actual)", "icono"=>"error"]); }

            $datos_pago = [
                ["campo_nombre"=>"compra_id","campo_marcador"=>":Id","campo_valor"=>$id],
                ["campo_nombre"=>"pago_monto","campo_marcador"=>":Monto","campo_valor"=>$monto],
                ["campo_nombre"=>"pago_fecha","campo_marcador"=>":Fecha","campo_valor"=>date("Y-m-d")],
                ["campo_nombre"=>"pago_metodo","campo_marcador"=>":Metodo","campo_valor"=>$metodo],
                ["campo_nombre"=>"pago_referencia","campo_marcador"=>":Referencia","campo_valor"=>$referencia],
                ["campo_nombre"=>"usuario_id","campo_marcador"=>":Usuario","campo_valor"=>$_SESSION['id']]
            ];

            if($this->guardarDatos("compra_pagos", $datos_pago)->rowCount() >= 1){
                $this->actualizarSaldosCompra($id);
                return json_encode(["tipo"=>"recargar", "titulo"=>"¡Abono registrado!", "texto"=>"Se procesó el pago correctamente", "icono"=>"success"]);
            }
            return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"Error al registrar", "icono"=>"error"]);
        }

        public function listarAbonosCompra($id){
            $id = $this->limpiarCadena($id);
            return $this->ejecutarConsulta("SELECT cp.*, u.usuario_nombre, u.usuario_apellido FROM compra_pagos cp INNER JOIN usuario u ON cp.usuario_id = u.usuario_id WHERE cp.compra_id='$id' ORDER BY cp.pago_fecha DESC");
        }

        public function listarAbonosCompraControlador($id){
            $datos = $this->listarAbonosCompra($id);
            if($datos->rowCount() > 0){
                $tabla = '<table class="table is-bordered is-striped is-narrow is-fullwidth"><thead><tr class="is-info"><th class="has-text-centered">Fecha</th><th class="has-text-centered">Monto</th><th class="has-text-centered">Método</th><th class="has-text-centered">Acciones</th></tr></thead><tbody>';
                foreach($datos->fetchAll() as $pago){
                    $tabla .= '<tr class="has-text-centered"><td>'.date("d/m/Y", strtotime($pago['pago_fecha'])).'</td><td class="has-text-weight-bold">$'.number_format($pago['pago_monto'], 2).'</td><td>'.$pago['pago_metodo'].'</td><td><form class="FormularioAjax" action="'.APP_URL.'app/ajax/compraAjax.php" method="POST" style="display: inline-block;"><input type="hidden" name="modulo_compra" value="eliminar_abono"><input type="hidden" name="pago_id" value="'.$pago['pago_id'].'"><input type="hidden" name="compra_id" value="'.$id.'"><button type="submit" class="button is-danger is-outline is-small is-rounded"><i class="far fa-trash-alt"></i></button></form></td></tr>';
                }
                $tabla .= '</tbody></table>';
            } else { $tabla = '<p class="has-text-centered">No hay abonos registrados.</p>'; }
            return $tabla;
        }

        public function eliminarAbonoControlador(){
            $pago_id = $this->limpiarCadena($_POST['pago_id']);
            $compra_id = $this->limpiarCadena($_POST['compra_id']);
            if($this->ejecutarConsulta("DELETE FROM compra_pagos WHERE pago_id='$pago_id'")->rowCount() == 1){
                $this->actualizarSaldosCompra($compra_id);
                return json_encode(["tipo"=>"recargar", "titulo"=>"Eliminado", "texto"=>"Abono borrado y saldo restaurado", "icono"=>"success"]);
            }
            return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"No se pudo eliminar", "icono"=>"error"]);
        }

        /*---------- Anulación de Compra Segura (Con Motivo de Auditoría) ----------*/
        public function eliminarCompraControlador() {
            $id = $this->limpiarCadena($_POST['compra_id']);
            $motivo = isset($_POST['motivo_anulacion']) ? $this->limpiarCadena($_POST['motivo_anulacion']) : "Anulada sin motivo";

            $check = $this->ejecutarConsulta("SELECT * FROM compra WHERE compra_id='$id'");
            if($check->rowCount() <= 0){ return json_encode(["tipo" => "simple", "titulo" => "Error", "texto" => "La compra no existe.", "icono" => "error"]); }
            
            $datos_compra = $check->fetch();
            $nota_anterior = $datos_compra['compra_nota_interna'];
            
            // Unimos la nota vieja (si tenía) con la nueva advertencia de anulación
            $nueva_nota_auditoria = $nota_anterior . " | [ANULADA]: " . $motivo;

            // 1. REVERTIR EL INVENTARIO 
            $recepciones = $this->ejecutarConsulta("SELECT rd.producto_id, rd.cantidad_recibida FROM recepcion_detalle rd INNER JOIN recepcion r ON rd.recepcion_id = r.recepcion_id WHERE r.compra_id='$id'");
            foreach($recepciones->fetchAll() as $prod){
                $upd = $this->conectar()->prepare("UPDATE producto SET producto_stock = producto_stock - :Cant WHERE producto_id = :ID");
                $upd->execute([":Cant" => $prod['cantidad_recibida'], ":ID" => $prod['producto_id']]);
            }

            // 2. SOFT DELETE (Mover a Anuladas, limpiar la deuda y GUARDAR EL MOTIVO)
            $anular = $this->ejecutarConsulta("UPDATE compra SET compra_estado='Anulada', compra_saldo_pendiente='0', compra_nota_interna='$nueva_nota_auditoria' WHERE compra_id='$id'");

            if($anular->rowCount() == 1){ 
                return json_encode(["tipo" => "recargar", "titulo" => "Anulada", "texto" => "El inventario fue revertido y la compra se anuló correctamente.", "icono" => "success"]); 
            } else { return json_encode(["tipo" => "simple", "titulo" => "Error", "texto" => "Error al anular.", "icono" => "error"]); }
        }

        /*---------- Registrar Devolución de Dinero (Saldo a Favor) ----------*/
        public function registrarReintegroControlador(){
            $id = $this->limpiarCadena($_POST['reintegro_compra_id']);
            $monto = (float) $this->limpiarCadena($_POST['reintegro_monto']);
            $metodo = $this->limpiarCadena($_POST['reintegro_metodo']);
            $referencia = $this->limpiarCadena($_POST['reintegro_referencia']);
            
            if($monto <= 0){ return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"El monto debe ser mayor a cero", "icono"=>"error"]); }
            if($referencia == "" && ($metodo == "Efectivo" || $metodo == "Divisas")){ $referencia = "DEVOLUCION EFECTIVO/DIVISA"; } 
            elseif ($referencia == "") { return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"La referencia es obligatoria", "icono"=>"error"]); }

            // Verificamos el saldo a favor actual
            $saldo_actual = (float) $this->ejecutarConsulta("SELECT compra_saldo_pendiente FROM compra WHERE compra_id='$id'")->fetchColumn();
            
            if($saldo_actual >= 0){ return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"Esta compra no tiene saldo a favor.", "icono"=>"error"]); }
            
            if($monto > abs($saldo_actual)){ return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"El monto no puede superar el saldo a favor ($".abs($saldo_actual).")", "icono"=>"error"]); }

            // EL TRUCO CONTABLE: Guardamos el pago en NEGATIVO para restar lo que "Sobró"
            $monto_negativo = -$monto;

            $datos_pago = [
                ["campo_nombre"=>"compra_id","campo_marcador"=>":Id","campo_valor"=>$id],
                ["campo_nombre"=>"pago_monto","campo_marcador"=>":Monto","campo_valor"=>$monto_negativo],
                ["campo_nombre"=>"pago_fecha","campo_marcador"=>":Fecha","campo_valor"=>date("Y-m-d")],
                ["campo_nombre"=>"pago_metodo","campo_marcador"=>":Metodo","campo_valor"=>"Reintegro (".$metodo.")"],
                ["campo_nombre"=>"pago_referencia","campo_marcador"=>":Referencia","campo_valor"=>$referencia],
                ["campo_nombre"=>"usuario_id","campo_marcador"=>":Usuario","campo_valor"=>$_SESSION['id']]
            ];

            if($this->guardarDatos("compra_pagos", $datos_pago)->rowCount() >= 1){
                $this->actualizarSaldosCompra($id);
                return json_encode(["tipo"=>"recargar", "titulo"=>"¡Dinero Recuperado!", "texto"=>"El reintegro se registró y la cuenta regresó a $0", "icono"=>"success"]);
            }
            return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"Error al registrar el reintegro", "icono"=>"error"]);
        }

		/*=============================================
		=             LISTADOS Y TABLAS               =
		=============================================*/

        /*---------- Listar Órdenes para Recepción ----------*/
        public function listarRecepcionesControlador($pagina, $registros, $url) {
            $pagina = $this->limpiarCadena($pagina); $registros = $this->limpiarCadena($registros); $url = APP_URL . $url . "/";
            $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

            $datos = $this->ejecutarConsulta("SELECT c.*, p.proveedor_nombre FROM compra c INNER JOIN proveedor p ON c.proveedor_id = p.proveedor_id WHERE c.compra_estado IN ('Pendiente', 'Parcial') ORDER BY c.compra_id DESC LIMIT $inicio, $registros")->fetchAll();

            $tabla = '<div class="table-container"><table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth"><thead><tr class="has-background-link-dark"><th class="has-text-centered has-text-white">Código</th><th class="has-text-centered has-text-white">Proveedor</th><th class="has-text-centered has-text-white">Estado Físico</th><th class="has-text-centered has-text-white">Progreso</th><th class="has-text-centered has-text-white">Acciones</th></tr></thead><tbody>';

            if (count($datos) >= 1) {
                foreach ($datos as $rows) {
                    $compra_id = $rows['compra_id'];
                    $pedido = (float) $this->ejecutarConsulta("SELECT SUM(compra_detalle_cantidad) FROM compra_detalle WHERE compra_id='$compra_id'")->fetchColumn();
                    $recibido = (float) $this->ejecutarConsulta("SELECT SUM(rd.cantidad_recibida) FROM recepcion_detalle rd INNER JOIN recepcion r ON rd.recepcion_id=r.recepcion_id WHERE r.compra_id='$compra_id'")->fetchColumn();
                    
                    $porcentaje = ($pedido > 0) ? ($recibido * 100) / $pedido : 0;
                    $color_tag = ($rows['compra_estado'] == 'Pendiente') ? 'is-info' : 'is-warning';

                    $tabla .= '<tr class="has-text-centered"><td>' . $rows['compra_codigo'] . '</td><td>' . $rows['proveedor_nombre'] . '</td><td><span class="tag ' . $color_tag . ' is-light">' . $rows['compra_estado'] . '</span></td><td style="vertical-align: middle;"><progress class="progress is-link is-small" value="' . $porcentaje . '" max="100">' . $porcentaje . '%</progress><small>' . $recibido . ' de ' . $pedido . ' cajas</small></td><td><a href="' . APP_URL . 'purchaseReceptionDetail/' . $compra_id . '/" class="button is-link is-rounded is-small"><i class="fas fa-boxes"></i> &nbsp; Recibir</a></td></tr>';
                }
            } else { $tabla .= '<tr class="has-text-centered"><td colspan="5">No hay mercancía pendiente de recibir</td></tr>'; }
            return $tabla . '</tbody></table></div>';
        }

        /*---------- Listado Principal de Compras ----------*/
        public function listarCompraControlador($pagina, $registros, $url, $busqueda) {
            $pagina = $this->limpiarCadena($pagina); $registros = $this->limpiarCadena($registros); $busqueda = $this->limpiarCadena($busqueda);
            $url_split = explode("/", $url); $estado_view = (isset($url_split[1]) && $url_split[1] != "") ? $url_split[1] : "Pendiente";
            $url = APP_URL . $url . "/"; $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

            if(isset($busqueda) && $busqueda != ""){
                $filtro_sql = "WHERE (c.compra_codigo LIKE '%$busqueda%' OR p.proveedor_nombre LIKE '%$busqueda%')";
            } else {
                if($estado_view == "Anulada"){ $filtro_sql = "WHERE c.compra_estado='Anulada'"; } 
                elseif($estado_view == "Pagada"){ $filtro_sql = "WHERE c.compra_estado_pago='Pagado' AND c.compra_estado!='Anulada'"; } 
                else { $filtro_sql = "WHERE c.compra_estado_pago!='Pagado' AND c.compra_estado!='Anulada'"; }
            }

            $datos = $this->ejecutarConsulta("SELECT c.*, p.proveedor_nombre FROM compra c INNER JOIN proveedor p ON c.proveedor_id = p.proveedor_id $filtro_sql ORDER BY c.compra_id DESC LIMIT $inicio, $registros")->fetchAll();

            $tabla = '<div class="table-container"><table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth"><thead><tr class="has-background-link-dark"><th class="has-text-centered has-text-white">Código</th><th class="has-text-centered has-text-white">Proveedor</th><th class="has-text-centered has-text-white">Factura $</th><th class="has-text-centered has-text-white">Deuda $</th><th class="has-text-centered has-text-white">Pago</th><th class="has-text-centered has-text-white">Mercancía</th><th class="has-text-centered has-text-white">Opciones</th></tr></thead><tbody>';

            if (count($datos) >= 1) {
                foreach ($datos as $rows) {
                    
                    if($rows['compra_estado'] == "Anulada"){ $color_pago = "is-dark"; $texto_pago = "Anulada"; } 
                    else {
                        if($rows['compra_estado_pago'] == "Pagado"){ $color_pago = "is-success"; $texto_pago = "Pagado"; } 
                        elseif($rows['compra_estado_pago'] == "Parcial"){ $color_pago = "is-warning"; $texto_pago = "Parcial"; } 
                        else { $color_pago = "is-danger"; $texto_pago = "Pendiente"; }
                    }

                    $color_fisico = ($rows['compra_estado'] == "Completado") ? "has-text-success" : "has-text-warning-dark";

                    $tabla .= '<tr class="has-text-centered">
                        <td>' . $rows['compra_codigo'] . '</td>
                        <td>' . $rows['proveedor_nombre'] . '</td>
                        <td>$' . number_format($rows['compra_total'], 2) . '</td>
                        <td class="has-text-weight-bold">$' . number_format($rows['compra_saldo_pendiente'], 2) . '</td>
                        <td><span class="tag ' . $color_pago . ' is-rounded">' . $texto_pago . '</span></td>
                        <td class="'.$color_fisico.' has-text-weight-bold">' . $rows['compra_estado'] . '</td>
                        <td>
                            <div class="buttons is-centered is-flex-wrap-nowrap">
                                <button type="button" class="button is-link is-light is-rounded is-small" onclick="verHistorialAbonos(\'' . $rows['compra_id'] . '\', \'' . $rows['compra_codigo'] . '\')" title="Ver Pagos"><i class="fas fa-history"></i></button>
                                <a href="' . APP_URL . 'purchaseDetail/' . $rows['compra_id'] . '/" class="button is-info is-rounded is-small" title="Ver Detalle"><i class="fas fa-eye"></i></a>';

                    if($rows['compra_estado'] != "Anulada"){
                        
                        // Si falta mercancía:
                        if($rows['compra_estado'] != "Completado"){
                            $tabla .= '<a href="' . APP_URL . 'purchaseReceptionDetail/' . $rows['compra_id'] . '/" class="button is-success is-rounded is-small" title="Recibir Camión"><i class="fas fa-truck-loading"></i></a>
                                       <form class="FormularioAjax ml-1" action="' . APP_URL . 'app/ajax/compraAjax.php" method="POST"><input type="hidden" name="modulo_compra" value="cerrar"><input type="hidden" name="compra_id" value="' . $rows['compra_id'] . '"><button type="submit" class="button is-link is-rounded is-small" title="Cerrar Incompleta"><i class="fas fa-check-double"></i></button></form>';
                        }

                        // Si hay deuda:
                        if($rows['compra_saldo_pendiente'] > 0){
                            $tabla .= '<a href="' . APP_URL . 'purchasePay/' . $rows['compra_id'] . '/" class="button is-primary is-rounded is-small" title="Abonar Dinero"><i class="fas fa-money-bill-wave"></i></a>';
                        }

                        // PDF
                        if($rows['compra_estado'] == "Completado"){
                            $tabla .= '<button type="button" class="button is-warning is-rounded is-small" onclick="print_invoice(\'' . APP_URL . 'app/pdf/purchaseReceipt.php?id=' . $rows['compra_id'] . '\')" title="Factura Interna"><i class="fas fa-file-invoice-dollar"></i></button>';
                        } else {
                            $tabla .= '<button type="button" class="button is-danger is-rounded is-small" onclick="print_invoice(\'' . APP_URL . 'app/pdf/purchase_order.php?code=' . $rows['compra_codigo'] . '\')" title="Orden a Proveedor"><i class="fas fa-file-pdf"></i></button>';
                        }

                        
                        // Anular (Solo si no han pagado)
                        if($rows['compra_estado_pago'] != "Pagado"){
                            $tabla .= '<button type="button" class="button is-dark is-rounded is-small ml-1" onclick="anularCompraConMotivo(\'' . $rows['compra_id'] . '\')" title="Anular Compra"><i class="fas fa-ban"></i></button>';
                        }
                    }
                    $tabla .= '</div></td></tr>';
                }
            } else { $tabla .= '<tr class="has-text-centered"><td colspan="7">No hay registros</td></tr>'; }
            return $tabla . '</tbody></table></div>';
        }
	}