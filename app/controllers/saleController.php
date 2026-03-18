<?php

	namespace app\controllers;
	use app\models\mainModel;

	class saleController extends mainModel{

        /*---------- Controlador buscar codigo de producto ----------*/
        public function buscarCodigoVentaControlador() {
            $producto = $this->limpiarCadena($_POST['buscar_codigo']);
            $id_categoria = (isset($_POST['categoria_id'])) ? $this->limpiarCadena($_POST['categoria_id']) : "";
            $marca = isset($_POST['filtro_marca']) ? $this->limpiarCadena($_POST['filtro_marca']) : "";
            $modelo = isset($_POST['filtro_modelo']) ? $this->limpiarCadena($_POST['filtro_modelo']) : "";

            if ($producto == "" && $marca == "" && $modelo == "") {
                return '<article class="message is-warning mt-4 mb-4"><div class="message-header"><p>¡Atención!</p></div><div class="message-body has-text-centered"><i class="fas fa-exclamation-triangle fa-2x"></i><br>Debes introducir un Nombre, Marca o Modelo</div></article>'; exit();
            }

            $consulta_sql = "SELECT * FROM producto WHERE producto_estado='Activo'";
            if ($id_categoria != "") { $consulta_sql .= " AND categoria_id = '$id_categoria'"; }
            if ($producto != "") { $consulta_sql .= " AND (producto_nombre LIKE '%$producto%' OR producto_codigo LIKE '%$producto%')"; }
            if ($marca != "") { $consulta_sql .= " AND producto_marca = '$marca'"; }
            if ($modelo != "") { $consulta_sql .= " AND producto_modelo = '$modelo'"; }
            $consulta_sql .= " ORDER BY producto_nombre ASC";

            $datos_productos = $this->ejecutarConsulta($consulta_sql);

            if ($datos_productos->rowCount() >= 1) {
                $datos_productos = $datos_productos->fetchAll();
                $tabla = '
                    <div class="columns is-mobile has-text-grey is-size-7 has-text-weight-bold has-text-centered mb-0 p-2" style="border-bottom: 2px solid #edeff2; margin: 0;">
                        <div class="column is-narrow" style="width: 40px;"></div> 
                        <div class="column is-4 has-text-left">NOMBRE</div>
                        <div class="column is-3">MARCA</div>
                        <div class="column is-3">MODELO | STOCK</div>
                        <div class="column is-narrow" style="width: 40px;"></div>
                    </div>';

                foreach ($datos_productos as $rows) {
                    $tabla .= '
                    <div class="columns is-mobile is-vcentered mb-0 p-2" style="border-bottom: 1px solid #f1f1f1; margin: 0;">
                        <div class="column is-narrow"><i class="fas fa-box has-text-grey-light"></i></div>
                        <div class="column is-4 has-text-left"><span class="is-size-7 has-text-weight-semibold">' . $rows['producto_nombre'] . '</span></div>
                        <div class="column is-3 has-text-centered"><span class="tag is-light">' . $rows['producto_marca'] . '</span></div>
                        <div class="column is-3 has-text-centered"><span class="is-size-7 has-text-grey">' . $rows['producto_modelo'] . '</span><br><small class="has-text-link">Stock: ' . $rows['producto_stock'] . '</small></div>
                        <div class="column is-narrow"><button type="button" class="button is-link is-rounded is-small" onclick="agregar_codigo(\'' . $rows['producto_codigo'] . '\')"><i class="fas fa-plus-circle"></i></button></div>
                    </div>';
                }
                return $tabla;
            } else {
                return '<article class="message is-warning mt-4 mb-4"><div class="message-header"><p>¡No hay coincidencias!</p></div><div class="message-body has-text-centered"><i class="fas fa-exclamation-triangle fa-2x"></i><br>No se encontraron productos con esos filtros.</div></article>'; exit();
            }
        }

        /*---------- Controlador buscar productos por categoría ----------*/
        public function buscarPorCategoriaVentaControlador(){
            $categoria_id = $this->limpiarCadena($_POST['categoria_id']);
            $datos_productos=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_estado='Activo' AND categoria_id='$categoria_id' ORDER BY producto_nombre ASC");
            if($datos_productos->rowCount()>=1){
                $datos_productos=$datos_productos->fetchAll();
                $tabla='<div class="table-container mb-6"><table class="table is-striped is-narrow is-hoverable is-fullwidth"><tbody>';
                $tabla .= '<div class="columns is-gapless mb-2 is-mobile has-text-grey is-size-7 has-text-weight-bold has-text-centered" style="border-bottom: 2px solid #edeff2; padding-bottom: 5px; margin-left: 35px; margin-right: 45px;"><div class="column is-4">NOMBRE</div><div class="column is-4">MARCA</div><div class="column is-4">MODELO | STOCK</div></div>';
                
                foreach($datos_productos as $rows){
                    $tabla .= '<div class="columns is-mobile is-vcentered mb-0 p-2" style="border-bottom: 1px solid #f1f1f1; margin: 0;"><div class="column is-narrow"><i class="fas fa-box has-text-grey-light"></i></div><div class="column is-4 has-text-left"><span class="is-size-7 has-text-weight-semibold">' . $rows['producto_nombre'] . '</span></div><div class="column is-3 has-text-centered"><span class="tag is-light">' . $rows['producto_marca'] . '</span></div><div class="column is-3 has-text-centered"><span class="is-size-7 has-text-grey">' . $rows['producto_modelo'] . '</span><br><small class="has-text-link">Stock: ' . $rows['producto_stock'] . '</small></div><div class="column is-narrow"><button type="button" class="button is-link is-rounded is-small" onclick="agregar_codigo(\'' . $rows['producto_codigo'] . '\')"><i class="fas fa-plus-circle"></i></button></div></div>';
                }
                $tabla.='</tbody></table></div>'; return $tabla;
            }else{
                return '<article class="message is-warning mt-4 mb-4"><div class="message-body has-text-centered"><i class="fas fa-exclamation-triangle fa-2x"></i><br>No hay productos activos aquí</div></article>'; exit();
            }
        }

        /*---------- Controlador agregar producto a venta ----------*/
        public function agregarProductoCarritoControlador(){
            $codigo=$this->limpiarCadena($_POST['producto_codigo']);
            if($codigo==""){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"Debes introducir el código","icono"=>"error"]); exit(); }

            $check_producto=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_codigo='$codigo'");
            if($check_producto->rowCount()<=0){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"No encontrado","icono"=>"error"]); exit(); }else{ $campos=$check_producto->fetch(); }
            if($campos['producto_estado'] != 'Activo'){ return json_encode(["tipo"=>"simple","titulo"=>"Inactivo","texto"=>"Producto inactivo","icono"=>"warning"]); exit(); }

            $codigo=$campos['producto_codigo'];
            if(empty($_SESSION['datos_producto_venta'][$codigo])){
                $detalle_cantidad=1;
                $stock_total=$campos['producto_stock']-$detalle_cantidad;
                if($stock_total<0){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"Sin stock disponible","icono"=>"error"]); exit(); }
                $detalle_total=$detalle_cantidad*$campos['producto_precio']; $detalle_total=number_format($detalle_total,MONEDA_DECIMALES,'.','');
                $_SESSION['datos_producto_venta'][$codigo]=[ "producto_id"=>$campos['producto_id'], "producto_codigo"=>$campos['producto_codigo'], "producto_stock_total"=>$stock_total, "producto_stock_total_old"=>$campos['producto_stock'], "venta_detalle_precio_compra"=>$campos['producto_costo'], "venta_detalle_precio_venta"=>$campos['producto_precio'], "venta_detalle_cantidad"=>1, "venta_detalle_total"=>$detalle_total, "venta_detalle_descripcion"=>$campos['producto_nombre'] ];
                $_SESSION['alerta_producto_agregado']="Se agregó <strong>".$campos['producto_nombre']."</strong>";
            }else{
                $detalle_cantidad=($_SESSION['datos_producto_venta'][$codigo]['venta_detalle_cantidad'])+1;
                $stock_total=$campos['producto_stock']-$detalle_cantidad;
                if($stock_total<0){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"Sin stock disponible","icono"=>"error"]); exit(); }
                $detalle_total=$detalle_cantidad*$campos['producto_precio']; $detalle_total=number_format($detalle_total,MONEDA_DECIMALES,'.','');
                $_SESSION['datos_producto_venta'][$codigo]=[ "producto_id"=>$campos['producto_id'], "producto_codigo"=>$campos['producto_codigo'], "producto_stock_total"=>$stock_total, "producto_stock_total_old"=>$campos['producto_stock'], "venta_detalle_precio_compra"=>$campos['producto_costo'], "venta_detalle_precio_venta"=>$campos['producto_precio'], "venta_detalle_cantidad"=>$detalle_cantidad, "venta_detalle_total"=>$detalle_total, "venta_detalle_descripcion"=>$campos['producto_nombre'] ];
                $_SESSION['alerta_producto_agregado']="Se agregó +1 <strong>".$campos['producto_nombre']."</strong>. Total: <strong>$detalle_cantidad</strong>";
            }
            return json_encode(["tipo"=>"redireccionar","titulo"=>"Agregado","texto"=>"Añadido a la venta","icono"=>"success","url"=>APP_URL."saleNew/"]);
        }

        /*---------- Controlador remover producto de venta ----------*/
        public function removerProductoCarritoControlador(){
            $codigo=$this->limpiarCadena($_POST['producto_codigo']);
            unset($_SESSION['datos_producto_venta'][$codigo]);
            if(empty($_SESSION['datos_producto_venta'][$codigo])){ $alerta=["tipo"=>"recargar","titulo"=>"Removido","texto"=>"Se quitó el producto","icono"=>"success"]; }else{ $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo quitar","icono"=>"error"]; }
            return json_encode($alerta);
        }

        /*---------- Controlador actualizar producto de venta ----------*/
        public function actualizarProductoCarritoControlador(){
            $codigo=$this->limpiarCadena($_POST['producto_codigo']);
            $cantidad=$this->limpiarCadena($_POST['producto_cantidad']);
            if($codigo=="" || $cantidad==""){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"Faltan parámetros","icono"=>"error"]); exit(); }
            if($cantidad<=0){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"Cantidad mayor a 0","icono"=>"error"]); exit(); }
            $check_producto=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_codigo='$codigo'");
            if($check_producto->rowCount()<=0){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"Producto no encontrado","icono"=>"error"]); exit(); }else{ $campos=$check_producto->fetch(); }
            if(!empty($_SESSION['datos_producto_venta'][$codigo])){
                if($_SESSION['datos_producto_venta'][$codigo]["venta_detalle_cantidad"]==$cantidad){ return json_encode(["tipo"=>"simple","titulo"=>"Aviso","texto"=>"Misma cantidad","icono"=>"error"]); exit(); }
                $detalle_cantidad=$cantidad; $stock_total=$campos['producto_stock']-$detalle_cantidad;
                if($stock_total<0){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"Stock insuficiente. Disponibles: ".($stock_total+$detalle_cantidad),"icono"=>"error"]); exit(); }
                $detalle_total=$detalle_cantidad*$campos['producto_precio']; $detalle_total=number_format($detalle_total,MONEDA_DECIMALES,'.','');
                $_SESSION['datos_producto_venta'][$codigo]=[ "producto_id"=>$campos['producto_id'], "producto_codigo"=>$campos['producto_codigo'], "producto_stock_total"=>$stock_total, "producto_stock_total_old"=>$campos['producto_stock'], "venta_detalle_precio_compra"=>$campos['producto_costo'], "venta_detalle_precio_venta"=>$campos['producto_precio'], "venta_detalle_cantidad"=>$detalle_cantidad, "venta_detalle_total"=>$detalle_total, "venta_detalle_descripcion"=>$campos['producto_nombre'] ];
                return json_encode(["tipo"=>"recargar","titulo"=>"Actualizado","texto"=>"Cantidad modificada","icono"=>"success"]);
            }
        }

        /*---------- Controlador buscar cliente ----------*/
        public function buscarClienteVentaControlador(){
			$cliente=$this->limpiarCadena($_POST['buscar_cliente']);
			if($cliente==""){ return '<article class="message is-warning"><div class="message-body">Introduce un dato</div></article>'; exit(); }
            $datos_cliente=$this->ejecutarConsulta("SELECT * FROM cliente WHERE (cliente_id!='1') AND (cliente_numero_documento LIKE '%$cliente%' OR cliente_nombre LIKE '%$cliente%' OR cliente_apellido LIKE '%$cliente%') ORDER BY cliente_nombre ASC");
            if($datos_cliente->rowCount()>=1){
				$datos_cliente=$datos_cliente->fetchAll();
				$tabla='<div class="table-container mb-6"><table class="table is-striped is-narrow is-hoverable is-fullwidth"><tbody>';
				foreach($datos_cliente as $rows){
					$tabla.='<tr><td class="has-text-left" ><i class="fas fa-male fa-fw"></i> &nbsp; '.$rows['cliente_nombre'].' '.$rows['cliente_apellido'].' ('.$rows['cliente_numero_documento'].')</td><td class="has-text-centered" ><button type="button" class="button is-link is-rounded is-small" onclick="agregar_cliente('.$rows['cliente_id'].')"><i class="fas fa-user-plus"></i></button></td></tr>';
				}
				$tabla.='</tbody></table></div>'; return $tabla;
			}else{ return '<article class="message is-warning">
                    <div class="message-body has-text-centered">
                        <p class="mb-3">El cliente <strong>"'.$cliente.'"</strong> no existe.</p>
                        <a href="'.APP_URL.'clientNew/" class="button is-info is-rounded is-small">
                            <i class="fas fa-user-plus"></i> &nbsp; Crear nuevo cliente
                        </a>
                    </div>
                </article>'; 
                exit(); }
        }

        /*---------- Controlador agregar cliente ----------*/
        public function agregarClienteVentaControlador(){
			$id=$this->limpiarCadena($_POST['cliente_id']);
			$check_cliente=$this->ejecutarConsulta("SELECT * FROM cliente WHERE cliente_id='$id'");
			if($check_cliente->rowCount()<=0){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"No existe","icono"=>"error"]); exit(); }else{ $campos=$check_cliente->fetch(); }
			
            $_SESSION['datos_cliente_venta']=[ "cliente_id"=>$campos['cliente_id'], "cliente_tipo_documento"=>$campos['cliente_tipo_documento'], "cliente_numero_documento"=>$campos['cliente_numero_documento'], "cliente_nombre"=>$campos['cliente_nombre'], "cliente_apellido"=>$campos['cliente_apellido'] ];
			return json_encode(["tipo"=>"recargar","titulo"=>"Cliente seleccionado","texto"=>"Asignado a la venta","icono"=>"success"]);
        }

        /*---------- Controlador remover cliente ----------*/
        public function removerClienteVentaControlador(){
			unset($_SESSION['datos_cliente_venta']);
			return json_encode(["tipo"=>"recargar","titulo"=>"Removido","texto"=>"Cliente desasignado","icono"=>"success"]);
        }

        /*---------- Controlador registrar venta ----------*/
        public function registrarVentaControlador(){
            $caja=$this->limpiarCadena($_POST['venta_caja']);
            $venta_metodo_pago = $this->limpiarCadena($_POST['venta_metodo_pago']);
            $venta_referencia = isset($_POST['venta_referencia']) ? $this->limpiarCadena($_POST['venta_referencia']) : "";

            if($venta_metodo_pago == "Pago Movil" || $venta_metodo_pago == "Transferencia"){
                if(!preg_match("/^[0-9]{6}$/", $venta_referencia)){
                    return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"Referencia debe ser de 6 números exactos","icono"=>"error"]); exit();
                }
            } else { $venta_referencia = ""; }

            $venta_tasa_bcv=$this->limpiarCadena($_POST['venta_tasa_bcv']);
            if(!is_numeric($venta_tasa_bcv) || $venta_tasa_bcv == ""){ $venta_tasa_bcv = 0; }

            if($_SESSION['venta_total']<=0 || count($_SESSION['datos_producto_venta'])<=0){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"Carrito vacío","icono"=>"error"]); exit(); }
            if(!isset($_SESSION['datos_cliente_venta'])){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"Sin cliente","icono"=>"error"]); exit(); }
			
            $check_caja=$this->ejecutarConsulta("SELECT * FROM caja WHERE caja_id='$caja'");
			if($check_caja->rowCount()<=0){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"Caja inválida","icono"=>"error"]); exit(); }else{ $datos_caja=$check_caja->fetch(); }

            // BLINDAJE DE AUDITORÍA: Forzamos el pago exacto.
            $venta_total_final = number_format($_SESSION['venta_total'],MONEDA_DECIMALES,'.','');
            $venta_pagado = $venta_total_final; 
            $venta_cambio = 0.00; 
            
            $venta_fecha=date("Y-m-d"); $venta_hora=date("h:i a"); 

            /* LA CAJA EFECTIVO SOLO SUMA SI EL PAGO ES FÍSICO */
            if($venta_metodo_pago == "Efectivo" || $venta_metodo_pago == "Divisas"){
                $movimiento_cantidad = $venta_total_final; 
                $total_caja = $datos_caja['caja_efectivo'] + $movimiento_cantidad;
                $total_caja = number_format($total_caja,MONEDA_DECIMALES,'.','');
            } else {
                $total_caja = $datos_caja['caja_efectivo']; 
            }

            $errores_productos=0;
			foreach($_SESSION['datos_producto_venta'] as $productos){
                $check_producto=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='".$productos['producto_id']."'");
                if($check_producto->rowCount()<1){ $errores_productos=1; break; }else{ $datos_producto=$check_producto->fetch(); }
                $nuevo_stock = $datos_producto['producto_stock'] - $productos['venta_detalle_cantidad'];
                if(!$this->actualizarDatos("producto",[ ["campo_nombre"=>"producto_stock","campo_marcador"=>":S","campo_valor"=>$nuevo_stock] ],["condicion_campo"=>"producto_id","condicion_marcador"=>":I","condicion_valor"=>$productos['producto_id']])){ $errores_productos=1; break; }
            }

            if($errores_productos==1){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"Error actualizando stock","icono"=>"error"]); exit(); }

            $siguiente_numero = (int)$this->ejecutarConsulta("SELECT MAX(venta_id) AS id_maximo FROM venta")->fetch()['id_maximo'] + 1;
            $codigo_venta = "VEN-" . str_pad($siguiente_numero, 6, "0", STR_PAD_LEFT);

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
            if($this->guardarDatos("venta",$datos_venta_reg)->rowCount()!=1){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"Error al crear factura","icono"=>"error"]); exit(); }

            foreach($_SESSION['datos_producto_venta'] as $venta_detalle){
                $this->guardarDatos("venta_detalle",[
                	["campo_nombre"=>"venta_detalle_cantidad","campo_marcador"=>":Cantidad","campo_valor"=>$venta_detalle['venta_detalle_cantidad']],
					["campo_nombre"=>"venta_detalle_precio_compra","campo_marcador"=>":PrecioCompra","campo_valor"=>$venta_detalle['venta_detalle_precio_compra']],
					["campo_nombre"=>"venta_detalle_precio_venta","campo_marcador"=>":PrecioVenta","campo_valor"=>$venta_detalle['venta_detalle_precio_venta']],
					["campo_nombre"=>"venta_detalle_total","campo_marcador"=>":Total","campo_valor"=>$venta_detalle['venta_detalle_total']],
					["campo_nombre"=>"venta_detalle_descripcion","campo_marcador"=>":Descripcion","campo_valor"=>$venta_detalle['venta_detalle_descripcion']],
					["campo_nombre"=>"venta_codigo","campo_marcador"=>":VentaCodigo","campo_valor"=>$codigo_venta],
					["campo_nombre"=>"producto_id","campo_marcador"=>":Producto","campo_valor"=>$venta_detalle['producto_id']]
                ]);
            }

            $this->actualizarDatos("caja",[ ["campo_nombre"=>"caja_efectivo","campo_marcador"=>":E","campo_valor"=>$total_caja] ],["condicion_campo"=>"caja_id","condicion_marcador"=>":I","condicion_valor"=>$caja]);

            unset($_SESSION['venta_total']); unset($_SESSION['datos_cliente_venta']); unset($_SESSION['datos_producto_venta']);
            $_SESSION['venta_codigo_factura']=$codigo_venta;
			return json_encode(["tipo"=>"recargar","titulo"=>"¡Venta registrada!","texto"=>"Código: $codigo_venta","icono"=>"success"]); exit();
        }

		/*----------  Controlador listar venta  ----------*/
		public function listarVentaControlador($pagina,$registros,$url,$busqueda){
			$pagina=$this->limpiarCadena($pagina); $registros=$this->limpiarCadena($registros); $url=$this->limpiarCadena($url); $url=APP_URL.$url."/"; $busqueda=$this->limpiarCadena($busqueda); $tabla="";
			$pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1; $inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;
			
            $campos_tablas="venta.venta_id,venta.venta_codigo,venta.venta_fecha,venta.venta_hora,venta.venta_total,venta.venta_tasa_bcv,venta.venta_metodo_pago,venta.venta_referencia,venta.usuario_id,venta.cliente_id,venta.caja_id,usuario.usuario_id,usuario.usuario_nombre,usuario.usuario_apellido,cliente.cliente_id,cliente.cliente_nombre,cliente.cliente_apellido";
			
            if(isset($busqueda) && $busqueda!=""){
				$consulta_datos="SELECT $campos_tablas FROM venta INNER JOIN cliente ON venta.cliente_id=cliente.cliente_id INNER JOIN usuario ON venta.usuario_id=usuario.usuario_id WHERE (venta.venta_codigo='$busqueda' OR venta.venta_referencia='$busqueda') ORDER BY venta.venta_id DESC LIMIT $inicio,$registros";
				$consulta_total="SELECT COUNT(venta_id) FROM venta WHERE (venta.venta_codigo='$busqueda' OR venta.venta_referencia='$busqueda')";
			}else{
				$consulta_datos="SELECT $campos_tablas FROM venta INNER JOIN cliente ON venta.cliente_id=cliente.cliente_id INNER JOIN usuario ON venta.usuario_id=usuario.usuario_id ORDER BY venta.venta_id DESC LIMIT $inicio,$registros";
				$consulta_total="SELECT COUNT(venta_id) FROM venta";
			}
			$datos = $this->ejecutarConsulta($consulta_datos); $datos = $datos->fetchAll(); $total = $this->ejecutarConsulta($consulta_total); $total = (int) $total->fetchColumn(); $numeroPaginas =ceil($total/$registros);
			
            $tabla.='<div class="table-container"><table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth"><thead><tr class="has-background-link-light"><th class="has-text-centered">NRO.</th><th class="has-text-centered">Codigo</th><th class="has-text-centered">Fecha</th><th class="has-text-centered">Cliente</th><th class="has-text-centered">Vendedor</th><th class="has-text-centered">Pago</th><th class="has-text-centered">Total Facturado</th><th class="has-text-centered">Opciones</th></tr></thead><tbody>';
		    
            if($total>=1 && $pagina<=$numeroPaginas){
				$contador=$inicio+1; $pag_inicio=$inicio+1;
				foreach($datos as $rows){
                    $tasa = (isset($rows['venta_tasa_bcv']) && $rows['venta_tasa_bcv'] > 0) ? $rows['venta_tasa_bcv'] : 0;
                    $total_bs = $rows['venta_total'] * $tasa;
                    $str_bs = ($tasa > 0) ? 'Bs. '.number_format($total_bs, 2, ',', '.') : '<small class="has-text-grey">N/A</small>';
                    $metodo = isset($rows['venta_metodo_pago']) ? $rows['venta_metodo_pago'] : "N/A";
                    $referencia = (isset($rows['venta_referencia']) && $rows['venta_referencia']!="") ? "<br><small class='has-text-grey'>Ref: ".$rows['venta_referencia']."</small>" : "";

					$tabla.='<tr class="has-text-centered" >
                                <td>'.$rows['venta_id'].'</td><td>'.$rows['venta_codigo'].'</td><td>'.date("d-m-Y", strtotime($rows['venta_fecha'])).' '.$rows['venta_hora'].'</td>
                                <td>'.$this->limitarCadena($rows['cliente_nombre'].' '.$rows['cliente_apellido'],30,"...").'</td>
                                <td>'.$this->limitarCadena($rows['usuario_nombre'].' '.$rows['usuario_apellido'],30,"...").'</td>
                                <td><strong>'.$metodo.'</strong>'.$referencia.'</td>
                                <td><strong>'.MONEDA_SIMBOLO.number_format($rows['venta_total'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR).' '.MONEDA_NOMBRE.'</strong><br><span class="has-text-link is-size-7">'.$str_bs.'</span></td>
                                <td>
                                    <button type="button" class="button is-link is-outlined is-rounded is-small btn-sale-options" onclick="print_invoice(\''.APP_URL.'app/pdf/invoice.php?code='.$rows['venta_codigo'].'\')" title="Factura Fiscal (Bs + IVA)" ><i class="fas fa-file-invoice-dollar fa-fw"></i></button> 
                                    <button type="button" class="button is-info is-outlined is-rounded is-small btn-sale-options" onclick="print_invoice(\''.APP_URL.'app/pdf/delivery_note.php?code='.$rows['venta_codigo'].'\')" title="Nota de Entrega ($ sin IVA)" ><i class="fas fa-file-alt fa-fw"></i></button> 
                                    <a href="'.APP_URL.'saleDetail/'.$rows['venta_codigo'].'/" class="button is-link is-rounded is-small" title="Información de venta" ><i class="fas fa-shopping-bag fa-fw"></i></a> 
                                    <form class="FormularioAjax is-inline-block" action="'.APP_URL.'app/ajax/ventaAjax.php" method="POST" autocomplete="off" ><input type="hidden" name="modulo_venta" value="eliminar_venta"><input type="hidden" name="venta_id" value="'.$rows['venta_id'].'"><button type="submit" class="button is-danger is-rounded is-small" title="Anular venta" ><i class="far fa-trash-alt fa-fw"></i></button></form>
                                </td>
                            </tr>';
					$contador++;
				} $pag_final=$contador-1;
			}else{ $tabla.='<tr class="has-text-centered" ><td colspan="8">No hay registros</td></tr>'; }
			$tabla.='</tbody></table></div>';
			if($total>0 && $pagina<=$numeroPaginas){ $tabla.='<p class="has-text-right">Mostrando <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de <strong>'.$total.'</strong></p>'; $tabla.=$this->paginadorTablas($pagina,$numeroPaginas,$url,7); }
			return $tabla;
		}

		/*----------  Controlador eliminar venta ----------*/
		public function eliminarVentaControlador(){
			$id=$this->limpiarCadena($_POST['venta_id']);
		    $datos=$this->ejecutarConsulta("SELECT * FROM venta WHERE venta_id='$id'");
		    if($datos->rowCount()<=0){ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"Venta no encontrada","icono"=>"error"]); exit(); }else{ $datos=$datos->fetch(); }
		    
            $detalles = $this->ejecutarConsulta("SELECT * FROM venta_detalle WHERE venta_codigo='".$datos['venta_codigo']."'")->fetchAll();
            
            // OPTIMIZACIÓN: Abrir conexión una sola vez
            $conexion_db = $this->conectar();
            $stmt_stock = $conexion_db->prepare("UPDATE producto SET producto_stock = producto_stock + :Cantidad WHERE producto_id = :ID");
            
            foreach($detalles as $prod){
                $stmt_stock->execute([
                    ':Cantidad' => $prod['venta_detalle_cantidad'],
                    ':ID' => $prod['producto_id']
                ]);
            }
            
            /* SOLO SE RESTA DE LA CAJA SI EL PAGO FUE EN EFECTIVO/DIVISAS */
            if($datos['venta_metodo_pago'] == "Efectivo" || $datos['venta_metodo_pago'] == "Divisas"){
                $update_caja = $conexion_db->prepare("UPDATE caja SET caja_efectivo = caja_efectivo - :Efectivo WHERE caja_id = :CajaID");
                $update_caja->execute([
                    ':Efectivo' => $datos['venta_total'],
                    ':CajaID' => $datos['caja_id']
                ]);
            }

		    $this->eliminarRegistro("venta_detalle","venta_codigo",$datos['venta_codigo']);
		    if($this->eliminarRegistro("venta","venta_id",$id)->rowCount()==1){
		        return json_encode(["tipo"=>"recargar","titulo"=>"Venta anulada","texto"=>"Stock y cuentas restauradas correctamente","icono"=>"success"]);
		    }else{ return json_encode(["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo eliminar la cabecera de la venta","icono"=>"error"]); }
		}
	}