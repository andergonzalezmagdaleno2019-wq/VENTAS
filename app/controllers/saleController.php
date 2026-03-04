<?php

	namespace app\controllers;
	use app\models\mainModel;

	class saleController extends mainModel{

		/*---------- Controlador buscar codigo de producto ----------*/
        public function buscarCodigoVentaControlador(){
			$producto=$this->limpiarCadena($_POST['buscar_codigo']);
			if($producto==""){
				return '<article class="message is-warning mt-4 mb-4"><div class="message-header"><p>¡Ocurrió un error inesperado!</p></div><div class="message-body has-text-centered"><i class="fas fa-exclamation-triangle fa-2x"></i><br>Debes de introducir el Nombre, Marca o Modelo del producto</div></article>'; exit();
            }
            $datos_productos=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_estado='Activo' AND (producto_nombre LIKE '%$producto%' OR producto_marca LIKE '%$producto%' OR producto_codigo LIKE '%$producto%' OR producto_modelo LIKE '%$producto%') ORDER BY producto_nombre ASC");
            if($datos_productos->rowCount()>=1){
				$datos_productos=$datos_productos->fetchAll();
				$tabla='<div class="table-container mb-6"><table class="table is-striped is-narrow is-hoverable is-fullwidth"><tbody>';
				foreach($datos_productos as $rows){
					$tabla.='<tr class="has-text-left" ><td><i class="fas fa-box fa-fw"></i> &nbsp; '.$rows['producto_nombre'].' (Stock: '.$rows['producto_stock'].')</td><td class="has-text-centered"><button type="button" class="button is-link is-rounded is-small" onclick="agregar_codigo(\''.$rows['producto_codigo'].'\')"><i class="fas fa-plus-circle"></i></button></td></tr>';
				}
				$tabla.='</tbody></table></div>'; return $tabla;
			}else{
				return '<article class="message is-warning mt-4 mb-4"><div class="message-header"><p>¡Ocurrió un error inesperado!</p></div><div class="message-body has-text-centered"><i class="fas fa-exclamation-triangle fa-2x"></i><br>No hemos encontrado ningún producto ACTIVO en el sistema que coincida con <strong>“'.$producto.'”</strong></div></article>'; exit();
			}
        }

        /*---------- Controlador buscar productos por categoría ----------*/
        public function buscarPorCategoriaVentaControlador(){
            $categoria_id = $this->limpiarCadena($_POST['categoria_id']);
            if($categoria_id=="" || !is_numeric($categoria_id)){
                return '<article class="message is-warning mt-4 mb-4"><div class="message-header"><p>¡Ocurrió un error inesperado!</p></div><div class="message-body has-text-centered"><i class="fas fa-exclamation-triangle fa-2x"></i><br>Categoría inválida</div></article>'; exit();
            }

            $datos_productos=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_estado='Activo' AND categoria_id='$categoria_id' ORDER BY producto_nombre ASC");
            if($datos_productos->rowCount()>=1){
                $datos_productos=$datos_productos->fetchAll();
                $tabla='<div class="table-container mb-6"><table class="table is-striped is-narrow is-hoverable is-fullwidth"><tbody>';
                foreach($datos_productos as $rows){
                    $tabla.='<tr class="has-text-left" ><td><i class="fas fa-box fa-fw"></i> &nbsp; '.$rows['producto_nombre'].' (Stock: '.$rows['producto_stock'].')</td><td class="has-text-centered"><button type="button" class="button is-link is-rounded is-small" onclick="agregar_codigo(\''.$rows['producto_codigo'].'\')"><i class="fas fa-plus-circle"></i></button></td></tr>';
                }
                $tabla.='</tbody></table></div>'; return $tabla;
            }else{
                return '<article class="message is-warning mt-4 mb-4"><div class="message-header"><p>¡Ocurrió un error inesperado!</p></div><div class="message-body has-text-centered"><i class="fas fa-exclamation-triangle fa-2x"></i><br>No hemos encontrado ningún producto ACTIVO en esta categoría</div></article>'; exit();
            }
        }

        /*---------- Controlador agregar producto a venta ----------*/
        public function agregarProductoCarritoControlador(){
            $codigo=$this->limpiarCadena($_POST['producto_codigo']);
            if($codigo==""){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Debes introducir el código de barras","icono"=>"error"]; return json_encode($alerta); exit(); }

            $check_producto=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_codigo='$codigo'");
            if($check_producto->rowCount()<=0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No hemos encontrado el producto","icono"=>"error"]; return json_encode($alerta); exit(); }else{ $campos=$check_producto->fetch(); }

            if($campos['producto_estado'] != 'Activo'){ $alerta=["tipo"=>"simple","titulo"=>"Producto Inactivo","texto"=>"Este producto está marcado como INACTIVO y no puede ser vendido","icono"=>"warning"]; return json_encode($alerta); exit(); }

            $codigo=$campos['producto_codigo'];
            if(empty($_SESSION['datos_producto_venta'][$codigo])){
                $detalle_cantidad=1;
                $stock_total=$campos['producto_stock']-$detalle_cantidad;
                if($stock_total<0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Lo sentimos, no hay existencias disponibles","icono"=>"error"]; return json_encode($alerta); exit(); }
                $detalle_total=$detalle_cantidad*$campos['producto_precio']; $detalle_total=number_format($detalle_total,MONEDA_DECIMALES,'.','');
                $_SESSION['datos_producto_venta'][$codigo]=[ "producto_id"=>$campos['producto_id'], "producto_codigo"=>$campos['producto_codigo'], "producto_stock_total"=>$stock_total, "producto_stock_total_old"=>$campos['producto_stock'], "venta_detalle_precio_compra"=>$campos['producto_costo'], "venta_detalle_precio_venta"=>$campos['producto_precio'], "venta_detalle_cantidad"=>1, "venta_detalle_total"=>$detalle_total, "venta_detalle_descripcion"=>$campos['producto_nombre'] ];
                $_SESSION['alerta_producto_agregado']="Se agregó <strong>".$campos['producto_nombre']."</strong> a la venta";
            }else{
                $detalle_cantidad=($_SESSION['datos_producto_venta'][$codigo]['venta_detalle_cantidad'])+1;
                $stock_total=$campos['producto_stock']-$detalle_cantidad;
                if($stock_total<0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Lo sentimos, no hay existencias disponibles","icono"=>"error"]; return json_encode($alerta); exit(); }
                $detalle_total=$detalle_cantidad*$campos['producto_precio']; $detalle_total=number_format($detalle_total,MONEDA_DECIMALES,'.','');
                $_SESSION['datos_producto_venta'][$codigo]=[ "producto_id"=>$campos['producto_id'], "producto_codigo"=>$campos['producto_codigo'], "producto_stock_total"=>$stock_total, "producto_stock_total_old"=>$campos['producto_stock'], "venta_detalle_precio_compra"=>$campos['producto_costo'], "venta_detalle_precio_venta"=>$campos['producto_precio'], "venta_detalle_cantidad"=>$detalle_cantidad, "venta_detalle_total"=>$detalle_total, "venta_detalle_descripcion"=>$campos['producto_nombre'] ];
                $_SESSION['alerta_producto_agregado']="Se agregó +1 <strong>".$campos['producto_nombre']."</strong> a la venta. Total: <strong>$detalle_cantidad</strong>";
            }
            $alerta=["tipo"=>"redireccionar","url"=>APP_URL."saleNew/"]; return json_encode($alerta);
        }

        /*---------- Controlador remover producto de venta ----------*/
        public function removerProductoCarritoControlador(){
            $codigo=$this->limpiarCadena($_POST['producto_codigo']);
            unset($_SESSION['datos_producto_venta'][$codigo]);
            if(empty($_SESSION['datos_producto_venta'][$codigo])){ $alerta=["tipo"=>"recargar","titulo"=>"¡Producto removido!","texto"=>"El producto se ha removido de la venta","icono"=>"success"]; }else{ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo remover el producto","icono"=>"error"]; }
            return json_encode($alerta);
        }

        /*---------- Controlador actualizar producto de venta ----------*/
        public function actualizarProductoCarritoControlador(){
            $codigo=$this->limpiarCadena($_POST['producto_codigo']);
            $cantidad=$this->limpiarCadena($_POST['producto_cantidad']);
            if($codigo=="" || $cantidad==""){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Faltan parámetros","icono"=>"error"]; return json_encode($alerta); exit(); }
            if($cantidad<=0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Cantidad mayor a 0","icono"=>"error"]; return json_encode($alerta); exit(); }
            $check_producto=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_codigo='$codigo'");
            if($check_producto->rowCount()<=0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Producto no encontrado","icono"=>"error"]; return json_encode($alerta); exit(); }else{ $campos=$check_producto->fetch(); }
            if(!empty($_SESSION['datos_producto_venta'][$codigo])){
                if($_SESSION['datos_producto_venta'][$codigo]["venta_detalle_cantidad"]==$cantidad){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No has modificado la cantidad","icono"=>"error"]; return json_encode($alerta); exit(); }
                if($cantidad>$_SESSION['datos_producto_venta'][$codigo]["venta_detalle_cantidad"]){ $diferencia_productos="agregó +".($cantidad-$_SESSION['datos_producto_venta'][$codigo]["venta_detalle_cantidad"]); }else{ $diferencia_productos="quitó -".($_SESSION['datos_producto_venta'][$codigo]["venta_detalle_cantidad"]-$cantidad); }
                $detalle_cantidad=$cantidad; $stock_total=$campos['producto_stock']-$detalle_cantidad;
                if($stock_total<0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No hay existencias suficientes. Disponibles: ".($stock_total+$detalle_cantidad),"icono"=>"error"]; return json_encode($alerta); exit(); }
                $detalle_total=$detalle_cantidad*$campos['producto_precio']; $detalle_total=number_format($detalle_total,MONEDA_DECIMALES,'.','');
                $_SESSION['datos_producto_venta'][$codigo]=[ "producto_id"=>$campos['producto_id'], "producto_codigo"=>$campos['producto_codigo'], "producto_stock_total"=>$stock_total, "producto_stock_total_old"=>$campos['producto_stock'], "venta_detalle_precio_compra"=>$campos['producto_costo'], "venta_detalle_precio_venta"=>$campos['producto_precio'], "venta_detalle_cantidad"=>$detalle_cantidad, "venta_detalle_total"=>$detalle_total, "venta_detalle_descripcion"=>$campos['producto_nombre'] ];
                $_SESSION['alerta_producto_agregado']="Se $diferencia_productos <strong>".$campos['producto_nombre']."</strong> en la venta.";
                $alerta=["tipo"=>"redireccionar","url"=>APP_URL."saleNew/"]; return json_encode($alerta);
            }else{
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Producto no encontrado en carrito","icono"=>"error"]; return json_encode($alerta);
            }
        }

        /*---------- Controlador buscar cliente ----------*/
        public function buscarClienteVentaControlador(){
			$cliente=$this->limpiarCadena($_POST['buscar_cliente']);
			if($cliente==""){ return '<article class="message is-warning"><div class="message-body">Debes introducir un dato del cliente</div></article>'; exit(); }
            $datos_cliente=$this->ejecutarConsulta("SELECT * FROM cliente WHERE (cliente_id!='1') AND (cliente_numero_documento LIKE '%$cliente%' OR cliente_nombre LIKE '%$cliente%' OR cliente_apellido LIKE '%$cliente%' OR cliente_telefono LIKE '%$cliente%') ORDER BY cliente_nombre ASC");
            if($datos_cliente->rowCount()>=1){
				$datos_cliente=$datos_cliente->fetchAll();
				$tabla='<div class="table-container mb-6"><table class="table is-striped is-narrow is-hoverable is-fullwidth"><tbody>';
				foreach($datos_cliente as $rows){
					$tabla.='<tr><td class="has-text-left" ><i class="fas fa-male fa-fw"></i> &nbsp; '.$rows['cliente_nombre'].' '.$rows['cliente_apellido'].' ('.$rows['cliente_tipo_documento'].': '.$rows['cliente_numero_documento'].')</td><td class="has-text-centered" ><button type="button" class="button is-link is-rounded is-small" onclick="agregar_cliente('.$rows['cliente_id'].')"><i class="fas fa-user-plus"></i></button></td></tr>';
				}
				$tabla.='</tbody></table></div>'; return $tabla;
			}else{ return '<article class="message is-warning"><div class="message-body">No se encontró el cliente</div></article>'; exit(); }
        }

        /*---------- Controlador agregar cliente ----------*/
        public function agregarClienteVentaControlador(){
			$id=$this->limpiarCadena($_POST['cliente_id']);
			$check_cliente=$this->ejecutarConsulta("SELECT * FROM cliente WHERE cliente_id='$id'");
			if($check_cliente->rowCount()<=0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No hemos podido agregar el cliente","icono"=>"error"]; return json_encode($alerta); exit(); }else{ $campos=$check_cliente->fetch(); }
			if($_SESSION['datos_cliente_venta']['cliente_id']==1){
                $_SESSION['datos_cliente_venta']=[ "cliente_id"=>$campos['cliente_id'], "cliente_tipo_documento"=>$campos['cliente_tipo_documento'], "cliente_numero_documento"=>$campos['cliente_numero_documento'], "cliente_nombre"=>$campos['cliente_nombre'], "cliente_apellido"=>$campos['cliente_apellido'] ];
				$alerta=["tipo"=>"recargar","titulo"=>"¡Cliente agregado!","texto"=>"El cliente se agregó para realizar una venta","icono"=>"success"];
			}else{ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Error al agregar","icono"=>"error"]; }
            return json_encode($alerta);
        }

        /*---------- Controlador remover cliente ----------*/
        public function removerClienteVentaControlador(){
			unset($_SESSION['datos_cliente_venta']);
			if(empty($_SESSION['datos_cliente_venta'])){ $alerta=["tipo"=>"recargar","titulo"=>"¡Cliente removido!","texto"=>"Se ha quitado el cliente de la venta","icono"=>"success"]; }else{ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Error al remover","icono"=>"error"]; }
			return json_encode($alerta);
        }

        /*---------- Controlador registrar venta ----------*/
        public function registrarVentaControlador(){
            $caja=$this->limpiarCadena($_POST['venta_caja']);
            $venta_pagado=$this->limpiarCadena($_POST['venta_abono']);
            $venta_metodo_pago = $this->limpiarCadena($_POST['venta_metodo_pago']);
            
            // Si la caja estaba deshabilitada (Efectivo), PHP no recibe la variable, así que prevenimos ese error
            $venta_referencia = isset($_POST['venta_referencia']) ? $this->limpiarCadena($_POST['venta_referencia']) : "";

            /*== VALIDACIÓN ESTRICTA DE MÉTODOS DE PAGO ==*/
            if($venta_metodo_pago == "Pago Movil" || $venta_metodo_pago == "Transferencia"){
                
                // Exigimos que sean exactamente 6 dígitos numéricos usando preg_match
                if(!preg_match("/^[0-9]{6}$/", $venta_referencia)){
                    $alerta=[
                        "tipo"=>"simple",
                        "titulo"=>"Referencia Inválida",
                        "texto"=>"Para pagos electrónicos (Pago Móvil / Transferencia) la referencia debe ser de EXACTAMENTE 6 NÚMEROS.",
                        "icono"=>"error"
                    ];
                    return json_encode($alerta);
                    exit();
                }

            } else {
                // Si es Efectivo o Divisa, por seguridad forzamos a que la referencia quede vacía en la base de datos
                $venta_referencia = "";
            }
            // Capturar la Tasa BCV
            $venta_tasa_bcv=$this->limpiarCadena($_POST['venta_tasa_bcv']);
            if(!is_numeric($venta_tasa_bcv) || $venta_tasa_bcv == ""){ $venta_tasa_bcv = 0; }

            if($this->verificarDatos("[0-9.]{1,25}",$venta_pagado)){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Formato de pago no válido","icono"=>"error"]; return json_encode($alerta); exit(); }
            if($_SESSION['venta_total']<=0 || (!isset($_SESSION['datos_producto_venta']) && count($_SESSION['datos_producto_venta'])<=0)){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No hay productos en la venta","icono"=>"error"]; return json_encode($alerta); exit(); }
            if(!isset($_SESSION['datos_cliente_venta'])){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No hay cliente seleccionado","icono"=>"error"]; return json_encode($alerta); exit(); }
			$check_cliente=$this->ejecutarConsulta("SELECT cliente_id FROM cliente WHERE cliente_id='".$_SESSION['datos_cliente_venta']['cliente_id']."'");
			if($check_cliente->rowCount()<=0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Cliente no registrado","icono"=>"error"]; return json_encode($alerta); exit(); }
            $check_caja=$this->ejecutarConsulta("SELECT * FROM caja WHERE caja_id='$caja'");
			if($check_caja->rowCount()<=0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Caja no válida","icono"=>"error"]; return json_encode($alerta); exit(); }else{ $datos_caja=$check_caja->fetch(); }

            $venta_pagado=number_format($venta_pagado,MONEDA_DECIMALES,'.','');
            $venta_total=number_format($_SESSION['venta_total'],MONEDA_DECIMALES,'.','');
            $venta_fecha=date("Y-m-d"); $venta_hora=date("h:i a"); $venta_total_final=$venta_total;

            if($venta_pagado<$venta_total_final){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"El pagado no puede ser menor al total","icono"=>"error"]; return json_encode($alerta); exit(); }
            $venta_cambio=$venta_pagado-$venta_total_final; $venta_cambio=number_format($venta_cambio,MONEDA_DECIMALES,'.','');
            $movimiento_cantidad=$venta_pagado-$venta_cambio; $movimiento_cantidad=number_format($movimiento_cantidad,MONEDA_DECIMALES,'.','');
            if($venta_metodo_pago == "Efectivo" || $venta_metodo_pago == "Divisas"){
    $total_caja=$datos_caja['caja_efectivo']+$movimiento_cantidad; 
} else {
    
    $total_caja=$datos_caja['caja_efectivo']; 
}
$total_caja=number_format($total_caja,MONEDA_DECIMALES,'.','');

            $errores_productos=0;
			foreach($_SESSION['datos_producto_venta'] as $productos){
                $check_producto=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='".$productos['producto_id']."' AND producto_codigo='".$productos['producto_codigo']."'");
                if($check_producto->rowCount()<1){ $errores_productos=1; break; }else{ $datos_producto=$check_producto->fetch(); }
                $_SESSION['datos_producto_venta'][$productos['producto_codigo']]['producto_stock_total']=$datos_producto['producto_stock']-$_SESSION['datos_producto_venta'][$productos['producto_codigo']]['venta_detalle_cantidad'];
                $_SESSION['datos_producto_venta'][$productos['producto_codigo']]['producto_stock_total_old']=$datos_producto['producto_stock'];
                $datos_producto_up=[ ["campo_nombre"=>"producto_stock","campo_marcador"=>":Stock","campo_valor"=>$_SESSION['datos_producto_venta'][$productos['producto_codigo']]['producto_stock_total']] ];
                $condicion=["condicion_campo"=>"producto_id","condicion_marcador"=>":ID","condicion_valor"=>$productos['producto_id']];
                if(!$this->actualizarDatos("producto",$datos_producto_up,$condicion)){ $errores_productos=1; break; }
            }

            if($errores_productos==1){
                foreach($_SESSION['datos_producto_venta'] as $producto){
                    $datos_producto_rs=[ ["campo_nombre"=>"producto_stock","campo_marcador"=>":Stock","campo_valor"=>$producto['producto_stock_total_old']] ];
                    $condicion=["condicion_campo"=>"producto_id","condicion_marcador"=>":ID","condicion_valor"=>$producto['producto_id']];
                    $this->actualizarDatos("producto",$datos_producto_rs,$condicion);
                }
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo actualizar inventario","icono"=>"error"]; return json_encode($alerta); exit();
            }

            $correlativo=$this->ejecutarConsulta("SELECT venta_id FROM venta");
			$correlativo=($correlativo->rowCount())+1;
            $codigo_venta=$this->generarCodigoAleatorio(10,$correlativo);

			$datos_venta_reg=[
				["campo_nombre"=>"venta_codigo","campo_marcador"=>":Codigo","campo_valor"=>$codigo_venta],
				["campo_nombre"=>"venta_fecha","campo_marcador"=>":Fecha","campo_valor"=>$venta_fecha],
				["campo_nombre"=>"venta_hora","campo_marcador"=>":Hora","campo_valor"=>$venta_hora],
				["campo_nombre"=>"venta_total","campo_marcador"=>":Total","campo_valor"=>$venta_total_final],
				["campo_nombre"=>"venta_pagado","campo_marcador"=>":Pagado","campo_valor"=>$venta_pagado],
				["campo_nombre"=>"venta_cambio","campo_marcador"=>":Cambio","campo_valor"=>$venta_cambio],
                ["campo_nombre"=>"venta_tasa_bcv","campo_marcador"=>":Tasa","campo_valor"=>$venta_tasa_bcv],
                
                ["campo_nombre"=>"venta_metodo_pago","campo_marcador"=>":Metodo","campo_valor"=>$venta_metodo_pago],
                ["campo_nombre"=>"venta_referencia","campo_marcador"=>":Referencia","campo_valor"=>$venta_referencia],
                
				["campo_nombre"=>"usuario_id","campo_marcador"=>":Usuario","campo_valor"=>$_SESSION['id']],
				["campo_nombre"=>"cliente_id","campo_marcador"=>":Cliente","campo_valor"=>$_SESSION['datos_cliente_venta']['cliente_id']],
				["campo_nombre"=>"caja_id","campo_marcador"=>":Caja","campo_valor"=>$caja]
            ];
            $agregar_venta=$this->guardarDatos("venta",$datos_venta_reg);

            if($agregar_venta->rowCount()!=1){
                foreach($_SESSION['datos_producto_venta'] as $producto){
                    $datos_producto_rs=[ ["campo_nombre"=>"producto_stock","campo_marcador"=>":Stock","campo_valor"=>$producto['producto_stock_total_old']] ];
                    $condicion=["condicion_campo"=>"producto_id","condicion_marcador"=>":ID","condicion_valor"=>$producto['producto_id']];
                    $this->actualizarDatos("producto",$datos_producto_rs,$condicion);
                }
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Error al registrar venta (001)","icono"=>"error"]; return json_encode($alerta); exit();
            }

            $errores_venta_detalle=0;
            foreach($_SESSION['datos_producto_venta'] as $venta_detalle){
                $datos_venta_detalle_reg=[
                	["campo_nombre"=>"venta_detalle_cantidad","campo_marcador"=>":Cantidad","campo_valor"=>$venta_detalle['venta_detalle_cantidad']],
					["campo_nombre"=>"venta_detalle_precio_compra","campo_marcador"=>":PrecioCompra","campo_valor"=>$venta_detalle['venta_detalle_precio_compra']],
					["campo_nombre"=>"venta_detalle_precio_venta","campo_marcador"=>":PrecioVenta","campo_valor"=>$venta_detalle['venta_detalle_precio_venta']],
					["campo_nombre"=>"venta_detalle_total","campo_marcador"=>":Total","campo_valor"=>$venta_detalle['venta_detalle_total']],
					["campo_nombre"=>"venta_detalle_descripcion","campo_marcador"=>":Descripcion","campo_valor"=>$venta_detalle['venta_detalle_descripcion']],
					["campo_nombre"=>"venta_codigo","campo_marcador"=>":VentaCodigo","campo_valor"=>$codigo_venta],
					["campo_nombre"=>"producto_id","campo_marcador"=>":Producto","campo_valor"=>$venta_detalle['producto_id']]
                ];
                $agregar_detalle_venta=$this->guardarDatos("venta_detalle",$datos_venta_detalle_reg);
                if($agregar_detalle_venta->rowCount()!=1){ $errores_venta_detalle=1; break; }
            }

            if($errores_venta_detalle==1){
                $this->eliminarRegistro("venta_detalle","venta_codigo",$codigo_venta); $this->eliminarRegistro("venta","venta_codigo",$codigo_venta);
                foreach($_SESSION['datos_producto_venta'] as $producto){
                    $datos_producto_rs=[ ["campo_nombre"=>"producto_stock","campo_marcador"=>":Stock","campo_valor"=>$producto['producto_stock_total_old']] ];
                    $condicion=["condicion_campo"=>"producto_id","condicion_marcador"=>":ID","condicion_valor"=>$producto['producto_id']];
                    $this->actualizarDatos("producto",$datos_producto_rs,$condicion);
                }
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Error al guardar detalle (002)","icono"=>"error"]; return json_encode($alerta); exit();
            }

            $datos_caja_up=[ ["campo_nombre"=>"caja_efectivo","campo_marcador"=>":Efectivo","campo_valor"=>$total_caja] ];
            $condicion_caja=["condicion_campo"=>"caja_id","condicion_marcador"=>":ID","condicion_valor"=>$caja];
            if(!$this->actualizarDatos("caja",$datos_caja_up,$condicion_caja)){
                $this->eliminarRegistro("venta_detalle","venta_codigo",$codigo_venta); $this->eliminarRegistro("venta","venta_codigo",$codigo_venta);
                foreach($_SESSION['datos_producto_venta'] as $producto){
                    $datos_producto_rs=[ ["campo_nombre"=>"producto_stock","campo_marcador"=>":Stock","campo_valor"=>$producto['producto_stock_total_old']] ];
                    $condicion=["condicion_campo"=>"producto_id","condicion_marcador"=>":ID","condicion_valor"=>$producto['producto_id']];
                    $this->actualizarDatos("producto",$datos_producto_rs,$condicion);
                }
                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Error en caja (003)","icono"=>"error"]; return json_encode($alerta); exit();
            }

            unset($_SESSION['venta_total']); unset($_SESSION['datos_cliente_venta']); unset($_SESSION['datos_producto_venta']);
            $_SESSION['venta_codigo_factura']=$codigo_venta;
            $this->guardarBitacora("Ventas", "Nueva Venta", "Venta realizada con código: ".$codigo_venta." por un total de ".MONEDA_SIMBOLO.$venta_total_final);
            $alerta=["tipo"=>"recargar","titulo"=>"¡Venta registrada!","texto"=>"La venta se registró con éxito en el sistema","icono"=>"success"];
			return json_encode($alerta); exit();
        }

        /*----------  Controlador listar venta (CON BOLÍVARES EN TABLA) ----------*/
		/*----------  Controlador listar venta (CON BOLÍVARES Y MÉTODOS DE PAGO) ----------*/
		public function listarVentaControlador($pagina,$registros,$url,$busqueda){
			$pagina=$this->limpiarCadena($pagina); $registros=$this->limpiarCadena($registros); $url=$this->limpiarCadena($url); $url=APP_URL.$url."/"; $busqueda=$this->limpiarCadena($busqueda); $tabla="";
			$pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1; $inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;
			
            // EXTRAEMOS METODO DE PAGO Y REFERENCIA DE LA BD
            $campos_tablas="venta.venta_id,venta.venta_codigo,venta.venta_fecha,venta.venta_hora,venta.venta_total,venta.venta_tasa_bcv,venta.venta_metodo_pago,venta.venta_referencia,venta.usuario_id,venta.cliente_id,venta.caja_id,usuario.usuario_id,usuario.usuario_nombre,usuario.usuario_apellido,cliente.cliente_id,cliente.cliente_nombre,cliente.cliente_apellido";
			
            if(isset($busqueda) && $busqueda!=""){
                // AHORA BUSCA POR CÓDIGO O POR NÚMERO DE REFERENCIA
				$consulta_datos="SELECT $campos_tablas FROM venta INNER JOIN cliente ON venta.cliente_id=cliente.cliente_id INNER JOIN usuario ON venta.usuario_id=usuario.usuario_id WHERE (venta.venta_codigo='$busqueda' OR venta.venta_referencia='$busqueda') ORDER BY venta.venta_id DESC LIMIT $inicio,$registros";
				$consulta_total="SELECT COUNT(venta_id) FROM venta WHERE (venta.venta_codigo='$busqueda' OR venta.venta_referencia='$busqueda')";
			}else{
				$consulta_datos="SELECT $campos_tablas FROM venta INNER JOIN cliente ON venta.cliente_id=cliente.cliente_id INNER JOIN usuario ON venta.usuario_id=usuario.usuario_id ORDER BY venta.venta_id DESC LIMIT $inicio,$registros";
				$consulta_total="SELECT COUNT(venta_id) FROM venta";
			}
			$datos = $this->ejecutarConsulta($consulta_datos); $datos = $datos->fetchAll(); $total = $this->ejecutarConsulta($consulta_total); $total = (int) $total->fetchColumn(); $numeroPaginas =ceil($total/$registros);
			
            // AGREGAMOS LA COLUMNA "PAGO"
            $tabla.='<div class="table-container"><table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth"><thead><tr class="has-background-link-light"><th class="has-text-centered">NRO.</th><th class="has-text-centered">Codigo</th><th class="has-text-centered">Fecha</th><th class="has-text-centered">Cliente</th><th class="has-text-centered">Vendedor</th><th class="has-text-centered">Pago</th><th class="has-text-centered">Total Facturado</th><th class="has-text-centered">Opciones</th></tr></thead><tbody>';
		    
            if($total>=1 && $pagina<=$numeroPaginas){
				$contador=$inicio+1; $pag_inicio=$inicio+1;
				foreach($datos as $rows){
                    
                    // Cálculo de Bolívares
                    $tasa = (isset($rows['venta_tasa_bcv']) && $rows['venta_tasa_bcv'] > 0) ? $rows['venta_tasa_bcv'] : 0;
                    $total_bs = $rows['venta_total'] * $tasa;
                    $str_bs = ($tasa > 0) ? 'Bs. '.number_format($total_bs, 2, ',', '.') : '<small class="has-text-grey">N/A</small>';

                    // Método de Pago y Referencia visual
                    $metodo = isset($rows['venta_metodo_pago']) ? $rows['venta_metodo_pago'] : "N/A";
                    $referencia = (isset($rows['venta_referencia']) && $rows['venta_referencia']!="") ? "<br><small class='has-text-grey'>Ref: ".$rows['venta_referencia']."</small>" : "";

					$tabla.='<tr class="has-text-centered" >
                                <td>'.$rows['venta_id'].'</td>
                                <td>'.$rows['venta_codigo'].'</td>
                                <td>'.date("d-m-Y", strtotime($rows['venta_fecha'])).' '.$rows['venta_hora'].'</td>
                                <td>'.$this->limitarCadena($rows['cliente_nombre'].' '.$rows['cliente_apellido'],30,"...").'</td>
                                <td>'.$this->limitarCadena($rows['usuario_nombre'].' '.$rows['usuario_apellido'],30,"...").'</td>
                                <td>
                                    <strong>'.$metodo.'</strong>'.$referencia.'
                                </td>
                                <td>
                                    <strong>'.MONEDA_SIMBOLO.number_format($rows['venta_total'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR).' '.MONEDA_NOMBRE.'</strong><br>
                                    <span class="has-text-link is-size-7">'.$str_bs.'</span>
                                </td>
                                <td>
                                    <button type="button" class="button is-link is-outlined is-rounded is-small btn-sale-options" onclick="print_invoice(\''.APP_URL.'app/pdf/invoice.php?code='.$rows['venta_codigo'].'\')" title="Imprimir Factura (PDF)" ><i class="fas fa-file-pdf fa-fw"></i></button> 
                                    <a href="'.APP_URL.'saleDetail/'.$rows['venta_codigo'].'/" class="button is-link is-rounded is-small" title="Información de venta" ><i class="fas fa-shopping-bag fa-fw"></i></a> 
                                    <form class="FormularioAjax is-inline-block" action="'.APP_URL.'app/ajax/ventaAjax.php" method="POST" autocomplete="off" ><input type="hidden" name="modulo_venta" value="eliminar_venta"><input type="hidden" name="venta_id" value="'.$rows['venta_id'].'"><button type="submit" class="button is-danger is-rounded is-small" title="Eliminar venta / Devolver Stock" ><i class="far fa-trash-alt fa-fw"></i></button></form>
                                </td>
                            </tr>';
					$contador++;
				} $pag_final=$contador-1;
			}else{ $tabla.='<tr class="has-text-centered" ><td colspan="8">No hay registros en el sistema</td></tr>'; }
			$tabla.='</tbody></table></div>';
			if($total>0 && $pagina<=$numeroPaginas){ $tabla.='<p class="has-text-right">Mostrando ventas <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>'; $tabla.=$this->paginadorTablas($pagina,$numeroPaginas,$url,7); }
			return $tabla;
		}

		/*----------  Controlador eliminar venta ----------*/
		public function eliminarVentaControlador(){
			$id=$this->limpiarCadena($_POST['venta_id']);
		    $datos=$this->ejecutarConsulta("SELECT * FROM venta WHERE venta_id='$id'");
		    if($datos->rowCount()<=0){ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Venta no encontrada","icono"=>"error"]; return json_encode($alerta); exit(); }else{ $datos=$datos->fetch(); }
		    $check_detalle_venta=$this->ejecutarConsulta("SELECT * FROM venta_detalle WHERE venta_codigo='".$datos['venta_codigo']."'");
            $detalles = $check_detalle_venta->fetchAll();
            foreach($detalles as $prod){
                $update = $this->conectar()->prepare("UPDATE producto SET producto_stock = producto_stock + :Cantidad WHERE producto_id = :ID");
                $update->bindValue(":Cantidad", $prod['venta_detalle_cantidad']); $update->bindValue(":ID", $prod['producto_id']); $update->execute();
            }
            $dinero_a_restar = $datos['venta_total'];
            $update_caja = $this->conectar()->prepare("UPDATE caja SET caja_efectivo = caja_efectivo - :Efectivo WHERE caja_id = :CajaID");
            $update_caja->bindValue(":Efectivo", $dinero_a_restar); $update_caja->bindValue(":CajaID", $datos['caja_id']); $update_caja->execute();
		    $this->eliminarRegistro("venta_detalle","venta_codigo",$datos['venta_codigo']);
		    $eliminarVenta=$this->eliminarRegistro("venta","venta_id",$id);
		    if($eliminarVenta->rowCount()==1){
                $this->guardarBitacora("Ventas", "Anulación", "Se anuló la venta con código: ".$datos['venta_codigo']);
		        $alerta=["tipo"=>"recargar","titulo"=>"Venta anulada","texto"=>"La venta se eliminó y el stock se restauró","icono"=>"success"];
		    }else{ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo eliminar la cabecera de la venta","icono"=>"error"]; }
		    return json_encode($alerta);
		}
	}