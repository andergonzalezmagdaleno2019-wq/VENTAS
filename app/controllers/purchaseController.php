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
			$costo=$this->limpiarCadena($_POST['compra_costo']);
			
			if($cantidad<=0){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"La cantidad debe ser mayor a 0","icono"=>"error"]); exit(); }
            
            $check_producto=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='$id'");
            if($check_producto->rowCount()<=0){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"El producto no existe","icono"=>"error"]); exit(); }
            $campos=$check_producto->fetch();

            if(!isset($_SESSION['datos_compra'])){ $_SESSION['datos_compra']=[]; }
            
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

        /*----------  Registrar Orden de Compra (Basado en Cotización PACTADA)  ----------*/
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
            $cantidades = $_POST['detalle_cantidad'];
            $precios = $_POST['detalle_precio'];

            $total_exacto = 0;
            foreach($_SESSION['datos_compra'] as $detalle){
                $id = $detalle['producto_id'];
                $cant_real = isset($cantidades[$id]) ? (int)$cantidades[$id] : 0;
                $precio_pactado = isset($precios[$id]) ? (float)$precios[$id] : 0;
                $costo_ref = (float)$detalle['costo_referencia'];

                if($cant_real <= 0){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"La cantidad no puede ser 0.","icono"=>"error"]); exit(); }
                if($precio_pactado < $costo_ref){ return json_encode(["tipo"=>"simple","titulo"=>"Error de Auditoría","texto"=>"El precio de ".$detalle['producto_nombre']." no puede ser menor a $".$costo_ref,"icono"=>"error"]); exit(); }

                $total_exacto += ($cant_real * $precio_pactado);
            }
            
            if($pago_inicial > $total_exacto && $total_exacto > 0){ return json_encode(["tipo"=>"simple","titulo"=>"Anticipo Inválido","texto"=>"No puedes dar un anticipo mayor al total.","icono"=>"error"]); exit(); }

            $saldo_pendiente = $total_exacto - $pago_inicial;
            $estado_pago = ($saldo_pendiente <= 0 && $pago_inicial > 0) ? "Pagado" : "Pendiente";
            $fecha_vencimiento = date("Y-m-d"); 
            
            $consulta_correlativo = $this->ejecutarConsulta("SELECT MAX(compra_id) AS id_maximo FROM compra");
            $siguiente_numero = (int)$consulta_correlativo->fetch()['id_maximo'] + 1;
            $codigo_compra = "COM-" . str_pad($siguiente_numero, 6, "0", STR_PAD_LEFT);
            
            $datos_compra_reg=[
                ["campo_nombre"=>"compra_codigo","campo_marcador"=>":Codigo","campo_valor"=>$codigo_compra],
                ["campo_nombre"=>"compra_fecha","campo_marcador"=>":Fecha","campo_valor"=>$fecha],
                ["campo_nombre"=>"compra_total","campo_marcador"=>":Total","campo_valor"=>$total_exacto],
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

                if($pago_inicial > 0){
                    $datos_pago_inicial=[
                        ["campo_nombre"=>"compra_id","campo_marcador"=>":IdCompra","campo_valor"=>$id_compra_p],
                        ["campo_nombre"=>"usuario_id","campo_marcador"=>":Usuario","campo_valor"=>$_SESSION['id']],
                        ["campo_nombre"=>"pago_fecha","campo_marcador"=>":Fecha","campo_valor"=>$fecha],
                        ["campo_nombre"=>"pago_monto","campo_marcador"=>":Monto","campo_valor"=>$pago_inicial],
                        ["campo_nombre"=>"pago_metodo","campo_marcador"=>":Metodo","campo_valor"=>"Anticipo Efectivo"],
                        ["campo_nombre"=>"pago_referencia","campo_marcador"=>":Ref","campo_valor"=>"Anticipo Cotización"]
                    ];
                    $this->guardarDatos("compra_pagos",$datos_pago_inicial);
                }

                foreach($_SESSION['datos_compra'] as $detalle){
                    $id = $detalle['producto_id'];
                    $datos_detalle=[ 
                        ["campo_nombre"=>"compra_id","campo_marcador"=>":IdCompra","campo_valor"=>$id_compra_p], 
                        ["campo_nombre"=>"producto_id","campo_marcador"=>":Producto","campo_valor"=>$id], 
                        ["campo_nombre"=>"compra_detalle_cantidad","campo_marcador"=>":Cantidad","campo_valor"=>(int)$cantidades[$id]], 
                        ["campo_nombre"=>"compra_detalle_precio","campo_marcador"=>":Precio","campo_valor"=>(float)$precios[$id]]
                    ];
                    $this->guardarDatos("compra_detalle",$datos_detalle);
                }
                
                unset($_SESSION['datos_compra']);
                return json_encode(["tipo"=>"confirmar", "titulo"=>"Orden ".$codigo_compra." Generada", "texto"=>"¿Desea imprimir la Orden de Compra?", "icono"=>"success", "confirmButtonText" => "Sí, imprimir", "cancelButtonText" => "No, después", "url"=>APP_URL."app/pdf/purchase_order.php?code=".$codigo_compra]);
            }else{ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo registrar","icono"=>"error"]); }
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

        /*---------- Recibir Mercancía (Lógica ERP con Auto-Pago y Precios Inteligentes) ----------*/
        public function registrarRecepcionControlador() {
            $compra_id = $this->limpiarCadena($_POST['compra_id']);
            
            if(!isset($_POST['productos_recibidos'])){
                return json_encode(["tipo" => "simple", "titulo" => "Error", "texto" => "No se enviaron productos.", "icono" => "error"]);
                exit();
            }
            
            $productos = $_POST['productos_recibidos']; 
            
            $tipo_doc = isset($_POST['recepcion_tipo_doc']) ? $this->limpiarCadena($_POST['recepcion_tipo_doc']) : "Nota de Entrega";
            $numero_doc = isset($_POST['recepcion_numero_doc']) ? $this->limpiarCadena($_POST['recepcion_numero_doc']) : "S/N";
            $fecha_emision = isset($_POST['recepcion_fecha_emision']) ? $this->limpiarCadena($_POST['recepcion_fecha_emision']) : date("Y-m-d");
            $fecha_vencimiento = isset($_POST['compra_fecha_vencimiento']) ? $this->limpiarCadena($_POST['compra_fecha_vencimiento']) : date("Y-m-d");
            $condicion_pago = isset($_POST['compra_condicion']) ? $this->limpiarCadena($_POST['compra_condicion']) : "Contado";
            $nota_usuario = isset($_POST['recepcion_nota']) ? $this->limpiarCadena($_POST['recepcion_nota']) : "";

            // --- ESCUDO ANTI-DUPLICADOS ---
            if($numero_doc != "S/N" && $numero_doc != ""){
                $check_doc = $this->ejecutarConsulta("SELECT recepcion_id FROM recepcion WHERE recepcion_nota LIKE '%[$tipo_doc Nro: $numero_doc %'");
                if($check_doc->rowCount() > 0){ return json_encode(["tipo" => "simple", "titulo" => "Documento Duplicado", "texto" => "El número de $tipo_doc ($numero_doc) ya está registrado en otra orden.", "icono" => "error"]); exit(); }
                
                if($tipo_doc == "Factura"){
                    $check_fac = $this->ejecutarConsulta("SELECT compra_id FROM compra WHERE compra_nota_interna LIKE '%[Factura Oficial Nro: $numero_doc %'");
                    if($check_fac->rowCount() > 0){ return json_encode(["tipo" => "simple", "titulo" => "Factura Duplicada", "texto" => "La Factura Nro $numero_doc ya fue asociada a otra compra.", "icono" => "error"]); exit(); }
                }
            }
            
            if($tipo_doc == "Nota de Entrega") { $condicion_pago = "Contado"; }

            $nuevo_estado_documento = ($tipo_doc == "Factura") ? "Facturada" : "Pendiente Factura";
            if($tipo_doc == "Factura" && $condicion_pago == "Contado"){ $nuevo_estado_documento = "Completado"; }

            $nota_final = "[$tipo_doc Nro: $numero_doc | Emisión: $fecha_emision | Pago: $condicion_pago] - " . $nota_usuario;
            
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
                    if($cantidad_real > 0) {
                        // 1. Guarda el registro de llegada
                        $this->ejecutarConsulta("INSERT INTO recepcion_detalle (recepcion_id, producto_id, cantidad_recibida) VALUES ('$recepcion_id', '$id_prod', '$cantidad_real')");
                        
                        // 2. Suma al inventario
                        $this->ejecutarConsulta("UPDATE producto SET producto_stock = producto_stock + $cantidad_real WHERE producto_id = '$id_prod'");
                        
                        // 3. MAGIA: ACTUALIZA EL PRECIO DE VENTA EN EL CATÁLOGO
                        $this->actualizarPrecioInteligente($id_prod, $compra_id);
                    }
                }

                $query_recalculo = $this->ejecutarConsulta("SELECT IFNULL(SUM(rd.cantidad_recibida * cd.compra_detalle_precio),0) as deuda_real FROM recepcion_detalle rd INNER JOIN recepcion r ON rd.recepcion_id = r.recepcion_id INNER JOIN compra_detalle cd ON cd.compra_id = r.compra_id AND cd.producto_id = rd.producto_id WHERE r.compra_id='$compra_id'")->fetch();
                $nuevo_total_real = (float) $query_recalculo['deuda_real'];
                
                $pagado_hasta_ahora = (float) $this->ejecutarConsulta("SELECT IFNULL(SUM(pago_monto),0) FROM compra_pagos WHERE compra_id='$compra_id'")->fetchColumn();
                $nuevo_saldo_pendiente = $nuevo_total_real - $pagado_hasta_ahora;
                
                // AUTO-PAGO DE CONTADO
                if($condicion_pago == "Contado" && $nuevo_saldo_pendiente > 0){
                    $datos_pago_auto = [
                        ["campo_nombre"=>"compra_id","campo_marcador"=>":Compra","campo_valor"=>$compra_id],
                        ["campo_nombre"=>"usuario_id","campo_marcador"=>":Usuario","campo_valor"=>$_SESSION['id']],
                        ["campo_nombre"=>"pago_fecha","campo_marcador"=>":Fecha","campo_valor"=>date("Y-m-d")],
                        ["campo_nombre"=>"pago_monto","campo_marcador"=>":Monto","campo_valor"=>$nuevo_saldo_pendiente],
                        ["campo_nombre"=>"pago_metodo","campo_marcador"=>":Metodo","campo_valor"=>"Pago Automático (Contado)"],
                        ["campo_nombre"=>"pago_referencia","campo_marcador"=>":Ref","campo_valor"=>"Cierre automático por ".$tipo_doc]
                    ];
                    $this->guardarDatos("compra_pagos", $datos_pago_auto);
                    $nuevo_saldo_pendiente = 0; 
                }

                $estado_pago = ($nuevo_saldo_pendiente <= 0 && $nuevo_total_real > 0) ? "Pagado" : "Pendiente";
                if ($nuevo_total_real == 0) { $estado_pago = "Pendiente"; }

                $this->ejecutarConsulta("UPDATE compra SET compra_total = '$nuevo_total_real', compra_fecha_vencimiento = '$fecha_vencimiento', compra_estado = '$nuevo_estado_documento', compra_saldo_pendiente = '$nuevo_saldo_pendiente', compra_estado_pago = '$estado_pago' WHERE compra_id = '$compra_id'");

                return json_encode(["tipo" => "confirmar", "titulo" => "¡Almacén Actualizado!", "texto" => "Mercancía ingresada y precios de venta actualizados con éxito. ¿Desea imprimir la NOTA DE RECEPCIÓN?", "icono" => "success", "confirmButtonText" => "Sí, Imprimir Nota", "cancelButtonText" => "No, salir al listado", "url" => APP_URL."app/pdf/purchaseReceipt.php?id=".$compra_id]);
            } else { return json_encode(["tipo" => "simple", "titulo" => "Error", "texto" => "No se pudo registrar.", "icono" => "error"]); }
        }
        
        /*---------- Registrar Factura a una Nota de Entrega (Con Validación Duplicados) ----------*/
        public function registrarFacturaPendienteControlador() {
            $id = $this->limpiarCadena($_POST['factura_compra_id']);
            $numero_doc = $this->limpiarCadena($_POST['factura_numero']);
            $fecha_emision = $this->limpiarCadena($_POST['factura_fecha']);
            $fecha_vencimiento = $this->limpiarCadena($_POST['factura_vencimiento']);

            // --- ESCUDO ANTI-DUPLICADOS ---
            if($numero_doc != ""){
                $check_fac_recepcion = $this->ejecutarConsulta("SELECT recepcion_id FROM recepcion WHERE recepcion_nota LIKE '%[Factura Nro: $numero_doc %'");
                $check_fac_compra = $this->ejecutarConsulta("SELECT compra_id FROM compra WHERE compra_nota_interna LIKE '%[Factura Oficial Nro: $numero_doc %'");
                if($check_fac_recepcion->rowCount() > 0 || $check_fac_compra->rowCount() > 0){ return json_encode(["tipo" => "simple", "titulo" => "Factura Duplicada", "texto" => "La Factura Nro $numero_doc ya está registrada.", "icono" => "error"]); exit(); }
            }

            $check = $this->ejecutarConsulta("SELECT compra_estado, compra_estado_pago, compra_saldo_pendiente, compra_nota_interna FROM compra WHERE compra_id='$id'");
            if($check->rowCount() <= 0) { return json_encode(["tipo" => "simple", "titulo" => "Error", "texto" => "La orden no existe.", "icono" => "error"]); }

            $datos = $check->fetch();
            $nota_nueva = $datos['compra_nota_interna'] . " | [Factura Oficial Nro: $numero_doc ingresada el ".date("Y-m-d")."]";

            $estado_nuevo = ($datos['compra_estado_pago'] == "Pagado" || $datos['compra_saldo_pendiente'] <= 0) ? 'Completado' : 'Facturada';

            $upd = $this->ejecutarConsulta("UPDATE compra SET compra_estado='$estado_nuevo', compra_fecha_vencimiento='$fecha_vencimiento', compra_nota_interna='$nota_nueva' WHERE compra_id='$id'");

            if($upd->rowCount() > 0) { return json_encode(["tipo" => "recargar", "titulo" => "¡Factura Registrada!", "texto" => "Factura vinculada exitosamente. Estado: $estado_nuevo.", "icono" => "success"]);
            } else { return json_encode(["tipo" => "simple", "titulo" => "Error", "texto" => "No se pudo actualizar.", "icono" => "error"]); }
        }

        /*----------  Cerrar Compra Incompleta  ----------*/
        public function cerrarCompraControlador(){
            $id=$this->limpiarCadena($_POST['compra_id']);
            
            $detalles = $this->ejecutarConsulta("SELECT cd.producto_id, (SELECT IFNULL(SUM(rd.cantidad_recibida), 0) FROM recepcion_detalle rd INNER JOIN recepcion r ON rd.recepcion_id = r.recepcion_id WHERE r.compra_id = cd.compra_id AND rd.producto_id = cd.producto_id) as total_recibido FROM compra_detalle cd WHERE cd.compra_id = '$id'")->fetchAll();

            foreach($detalles as $row){
                $prod_id = $row['producto_id'];
                $recibido = $row['total_recibido'];
                
                if($recibido == 0){ $this->ejecutarConsulta("DELETE FROM compra_detalle WHERE compra_id='$id' AND producto_id='$prod_id'"); } 
                else { $this->ejecutarConsulta("UPDATE compra_detalle SET compra_detalle_cantidad='$recibido' WHERE compra_id='$id' AND producto_id='$prod_id'"); }
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
            } else { $nuevo_precio_venta = $nuevo_costo * 1.20; }
            $this->ejecutarConsulta("UPDATE producto SET producto_costo='$nuevo_costo', producto_precio='$nuevo_precio_venta' WHERE producto_id='$producto_id'");
        }

        protected function actualizarEstadoCompra($compra_id){
            $total_pedido = (float) $this->ejecutarConsulta("SELECT SUM(compra_detalle_cantidad) FROM compra_detalle WHERE compra_id='$compra_id'")->fetchColumn();
            $total_recibido = (float) $this->ejecutarConsulta("SELECT SUM(rd.cantidad_recibida) FROM recepcion_detalle rd INNER JOIN recepcion r ON rd.recepcion_id=r.recepcion_id WHERE r.compra_id='$compra_id'")->fetchColumn();

            if($total_recibido >= $total_pedido && $total_pedido > 0){ $nuevo_estado = "Completado"; } 
            elseif($total_recibido > 0 && $total_recibido < $total_pedido){ $nuevo_estado = "Parcial"; } 
            else { $nuevo_estado = "Pendiente"; }
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
            if($monto > $saldo_actual){ return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"El monto supera el saldo ($$saldo_actual)", "icono"=>"error"]); }

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

        /*---------- Anulación de Compra Segura ----------*/
        public function eliminarCompraControlador() {
            $id = $this->limpiarCadena($_POST['compra_id']);
            $motivo = isset($_POST['motivo_anulacion']) ? $this->limpiarCadena($_POST['motivo_anulacion']) : "Anulada sin motivo";

            $check = $this->ejecutarConsulta("SELECT * FROM compra WHERE compra_id='$id'");
            if($check->rowCount() <= 0){ return json_encode(["tipo" => "simple", "titulo" => "Error", "texto" => "La compra no existe.", "icono" => "error"]); }
            
            $datos_compra = $check->fetch();
            $nueva_nota_auditoria = $datos_compra['compra_nota_interna'] . " | [ANULADA]: " . $motivo;

            $recepciones = $this->ejecutarConsulta("SELECT rd.producto_id, rd.cantidad_recibida FROM recepcion_detalle rd INNER JOIN recepcion r ON rd.recepcion_id = r.recepcion_id WHERE r.compra_id='$id'");
            foreach($recepciones->fetchAll() as $prod){
                $upd = $this->conectar()->prepare("UPDATE producto SET producto_stock = producto_stock - :Cant WHERE producto_id = :ID");
                $upd->execute([":Cant" => $prod['cantidad_recibida'], ":ID" => $prod['producto_id']]);
            }

            $anular = $this->ejecutarConsulta("UPDATE compra SET compra_estado='Anulada', compra_saldo_pendiente='0', compra_nota_interna='$nueva_nota_auditoria' WHERE compra_id='$id'");

            if($anular->rowCount() == 1){ return json_encode(["tipo" => "recargar", "titulo" => "Anulada", "texto" => "El inventario fue revertido y la compra se anuló.", "icono" => "success"]); } 
            else { return json_encode(["tipo" => "simple", "titulo" => "Error", "texto" => "Error al anular.", "icono" => "error"]); }
        }

        /*---------- Registrar Devolución de Dinero ----------*/
        public function registrarReintegroControlador(){
            $id = $this->limpiarCadena($_POST['reintegro_compra_id']);
            $monto = (float) $this->limpiarCadena($_POST['reintegro_monto']);
            $metodo = $this->limpiarCadena($_POST['reintegro_metodo']);
            $referencia = $this->limpiarCadena($_POST['reintegro_referencia']);
            
            if($monto <= 0){ return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"El monto debe ser mayor a cero", "icono"=>"error"]); }
            if($referencia == "" && ($metodo == "Efectivo" || $metodo == "Divisas")){ $referencia = "DEVOLUCION"; } 
            elseif ($referencia == "") { return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"La referencia es obligatoria", "icono"=>"error"]); }

            $saldo_actual = (float) $this->ejecutarConsulta("SELECT compra_saldo_pendiente FROM compra WHERE compra_id='$id'")->fetchColumn();
            if($saldo_actual >= 0){ return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"Esta compra no tiene saldo a favor.", "icono"=>"error"]); }
            if($monto > abs($saldo_actual)){ return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"Monto supera el saldo a favor", "icono"=>"error"]); }

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
                return json_encode(["tipo"=>"recargar", "titulo"=>"¡Recuperado!", "texto"=>"El reintegro se registró.", "icono"=>"success"]);
            }
            return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"Error al registrar", "icono"=>"error"]);
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

        /*---------- Listado Principal de Compras (Con Columna de Documentos y Buscador Avanzado) ----------*/
        public function listarCompraControlador($pagina, $registros, $url, $busqueda) {
            $pagina = $this->limpiarCadena($pagina); $registros = $this->limpiarCadena($registros); $busqueda = $this->limpiarCadena($busqueda);
            $url_split = explode("/", $url); 
            $estado_view = (isset($url_split[1]) && $url_split[1] != "") ? $url_split[1] : "EnProceso";
            $url = APP_URL . $url . "/"; $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

            if(isset($busqueda) && $busqueda != ""){ 
                $filtro_sql = "WHERE (c.compra_codigo LIKE '%$busqueda%' OR p.proveedor_nombre LIKE '%$busqueda%' OR c.compra_nota_interna LIKE '%$busqueda%' OR c.compra_id IN (SELECT compra_id FROM recepcion WHERE recepcion_nota LIKE '%$busqueda%'))"; 
            } else {
                if($estado_view == "Anuladas"){ $filtro_sql = "WHERE c.compra_estado='Anulada'"; } 
                elseif($estado_view == "Completadas"){ $filtro_sql = "WHERE c.compra_estado='Completado'"; } 
                elseif($estado_view == "PorPagar"){ $filtro_sql = "WHERE c.compra_estado='Facturada'"; } 
                elseif($estado_view == "PorFacturar"){ $filtro_sql = "WHERE c.compra_estado='Pendiente Factura'"; } 
                else { $filtro_sql = "WHERE c.compra_estado IN ('Pendiente', 'Parcial')"; } 
            }

            $datos = $this->ejecutarConsulta("SELECT c.*, p.proveedor_nombre, (SELECT recepcion_nota FROM recepcion WHERE compra_id = c.compra_id ORDER BY recepcion_id ASC LIMIT 1) as recepcion_base FROM compra c INNER JOIN proveedor p ON c.proveedor_id = p.proveedor_id $filtro_sql ORDER BY c.compra_id DESC LIMIT $inicio, $registros")->fetchAll();

            $tabla = '<div class="table-container"><table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth"><thead><tr class="has-background-link-dark"><th class="has-text-centered has-text-white">Código</th><th class="has-text-centered has-text-white">Proveedor</th><th class="has-text-centered has-text-white">Nro. Documento</th><th class="has-text-centered has-text-white">Total $</th><th class="has-text-centered has-text-white">Deuda $</th><th class="has-text-centered has-text-white">Pago</th><th class="has-text-centered has-text-white">Mercancía</th><th class="has-text-centered has-text-white">Opciones</th></tr></thead><tbody>';

            if (count($datos) >= 1) {
                foreach ($datos as $rows) {
                    
                    $doc_info = "Sin recibir";
                    $doc_color = "is-light";
                    
                    if($rows['recepcion_base'] != ""){
                        preg_match('/\[(.*?) \|/', $rows['recepcion_base'], $match_rec);
                        if(isset($match_rec[1])) { $doc_info = $match_rec[1]; $doc_color = "is-info is-light"; }
                    }
                    if($rows['compra_nota_interna'] != ""){
                        preg_match('/\[(Factura Oficial Nro: [^ ]+)/', $rows['compra_nota_interna'], $match_fac);
                        if(isset($match_fac[1])) { $doc_info = str_replace("Oficial ", "", $match_fac[1]); $doc_color = "is-primary is-light"; }
                    }

                    if($rows['compra_estado'] == "Anulada"){ $color_pago = "is-dark"; $texto_pago = "Anulada"; } 
                    else {
                        if($rows['compra_estado_pago'] == "Pagado"){ $color_pago = "is-success"; $texto_pago = "Pagado"; } 
                        elseif($rows['compra_estado_pago'] == "Parcial"){ $color_pago = "is-warning"; $texto_pago = "Parcial"; } 
                        else { $color_pago = "is-danger"; $texto_pago = "Pendiente"; }
                    }

                    $color_fisico = ($rows['compra_estado'] == "Completado" || $rows['compra_estado'] == "Facturada") ? "has-text-success" : "has-text-info";
                    if($rows['compra_estado'] == "Pendiente Factura") { $color_fisico = "has-text-warning-dark"; }

                    $tabla .= '<tr class="has-text-centered">
                        <td style="vertical-align: middle;">' . $rows['compra_codigo'] . '</td>
                        <td style="vertical-align: middle;">' . $rows['proveedor_nombre'] . '</td>
                        <td style="vertical-align: middle;"><span class="tag is-medium has-text-weight-bold '.$doc_color.'">' . $doc_info . '</span></td>
                        <td style="vertical-align: middle;">$' . number_format($rows['compra_total'], 2) . '</td>
                        <td class="has-text-weight-bold has-text-danger-dark" style="vertical-align: middle;">$' . number_format($rows['compra_saldo_pendiente'], 2) . '</td>
                        <td style="vertical-align: middle;"><span class="tag ' . $color_pago . ' is-rounded">' . $texto_pago . '</span></td>
                        <td class="'.$color_fisico.' has-text-weight-bold" style="vertical-align: middle;">' . $rows['compra_estado'] . '</td>
                        <td style="vertical-align: middle;">
                            <div class="buttons is-centered is-flex-wrap-nowrap">';

                    $tabla .= '<a href="' . APP_URL . 'purchaseDetail/' . $rows['compra_id'] . '/" class="button is-info is-rounded is-small" title="Ver Detalles"><i class="fas fa-eye"></i></a>';

                    if($rows['compra_estado'] != "Anulada"){
                        
                        if($rows['compra_estado'] == "Pendiente" || $rows['compra_estado'] == "Parcial"){
                            $tabla .= '<a href="' . APP_URL . 'purchaseReceptionDetail/' . $rows['compra_id'] . '/" class="button is-success is-rounded is-small" title="Recibir Mercancía"><i class="fas fa-truck-loading"></i></a>';
                        }
                        if($rows['compra_estado'] == "Pendiente Factura"){
                            $tabla .= '<button type="button" class="button is-primary is-rounded is-small" onclick="abrirModalFactura(\'' . $rows['compra_id'] . '\', \'' . $rows['compra_codigo'] . '\')" title="Registrar Factura Oficial"><i class="fas fa-file-invoice"></i></button>';
                        }
                        if($rows['compra_estado'] == "Completado" || $rows['compra_estado'] == "Facturada"){
                            $tabla .= '<button type="button" class="button is-warning is-rounded is-small" onclick="print_invoice(\'' . APP_URL . 'app/pdf/purchaseReceipt.php?id=' . $rows['compra_id'] . '\')" title="Imprimir Nota de Recepción"><i class="fas fa-file-invoice-dollar"></i></button>';
                        } else {
                            $tabla .= '<button type="button" class="button is-danger is-rounded is-small" onclick="print_invoice(\'' . APP_URL . 'app/pdf/purchase_order.php?code=' . $rows['compra_codigo'] . '\')" title="Imprimir Orden"><i class="fas fa-file-pdf"></i></button>';
                        }
                        if($rows['compra_estado'] == "Pendiente"){
                            $tabla .= '<button type="button" class="button is-dark is-rounded is-small ml-1" onclick="anularCompraConMotivo(\'' . $rows['compra_id'] . '\')" title="Anular Orden"><i class="fas fa-ban"></i></button>';
                        }
                        if($rows['compra_saldo_pendiente'] > 0 && ($rows['compra_estado'] == "Facturada" || $rows['compra_estado'] == "Completado")){
                            $tabla .= '<a href="' . APP_URL . 'purchasePay/' . $rows['compra_id'] . '/" class="button is-success is-light is-rounded is-small" title="Abonar a Deuda"><i class="fas fa-money-bill-wave"></i></a>';
                        }
                    }
                    $tabla .= '</div></td></tr>';
                }
            } else { $tabla .= '<tr class="has-text-centered"><td colspan="8">No hay registros en esta etapa.</td></tr>'; }
            return $tabla . '</tbody></table></div>';
        }

	}