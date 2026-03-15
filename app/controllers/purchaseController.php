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

        /*---------- Controlador eliminar producto individual del carrito ----------*/
    public function eliminarProductoCarritoControlador(){
        // Forzamos que el ID sea capturado correctamente
        if(!isset($_POST['producto_id'])){
            return json_encode([
                "tipo" => "simple",
                "titulo" => "Error de envío",
                "texto" => "No se recibió el ID del producto",
                "icono" => "error"
            ]);
        }

        $id = $this->limpiarCadena($_POST['producto_id']);

        if(isset($_SESSION['datos_compra'][$id])){
            unset($_SESSION['datos_compra'][$id]);
            $alerta = ["tipo"=>"recargar", "titulo"=>"Producto quitado", "texto"=>"Se eliminó de la lista", "icono"=>"success"];
        } else {
            // Esto nos dirá si el JS mandó un ID que no existe
            $alerta = ["tipo"=>"simple", "titulo"=>"Error", "texto"=>"ID no encontrado: ".$id, "icono"=>"error"];
        }
        return json_encode($alerta);
    }

/*----------  Controlador registrar compra con Cuentas por Pagar (FastNet)  ----------*/
public function registrarCompraControlador(){
    if(!isset($_SESSION['datos_compra']) || count($_SESSION['datos_compra'])<=0){ 
        $alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error","texto"=>"No tienes productos agregados","icono"=>"error"]; 
        return json_encode($alerta); exit(); 
    }

    $proveedor=$this->limpiarCadena($_POST['compra_proveedor']);
    $compra_tasa_bcv = $this->limpiarCadena($_POST['compra_tasa_bcv']);
    $compra_nota = isset($_POST['compra_nota']) ? $this->limpiarCadena($_POST['compra_nota']) : "";
    
    $fecha_vencimiento = $this->limpiarCadena($_POST['compra_fecha_vencimiento']);
    $pago_inicial = isset($_POST['compra_pago_inicial']) ? $this->limpiarCadena($_POST['compra_pago_inicial']) : 0;

    if(!is_numeric($compra_tasa_bcv) || $compra_tasa_bcv == ""){ $compra_tasa_bcv = 0; }
    if(!is_numeric($pago_inicial)){ $pago_inicial = 0; }
    
    if($proveedor==""){ 
        $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Debe seleccionar un proveedor","icono"=>"error"]; 
        return json_encode($alerta); exit(); 
    }

    if($fecha_vencimiento == ""){ $fecha_vencimiento = date("Y-m-d"); }

    $fecha=date("Y-m-d"); 
    $total=0;
    foreach($_SESSION['datos_compra'] as $productos){ $total+=$productos['subtotal']; }
    
    $saldo_pendiente = $total - $pago_inicial;

    if($saldo_pendiente <= 0){
        $estado_pago = "Pagado";
    } elseif($pago_inicial > 0) {
        $estado_pago = "Parcial";
    } else {
        $estado_pago = "Pendiente";
    }
    
    $consulta_correlativo = $this->ejecutarConsulta("SELECT MAX(compra_id) AS id_maximo FROM compra");
    $resultado_correlativo = $consulta_correlativo->fetch();
    $siguiente_numero = (int)$resultado_correlativo['id_maximo'] + 1;
    $codigo_compra = "COM-" . str_pad($siguiente_numero, 6, "0", STR_PAD_LEFT);
    
    $datos_compra_reg=[
        ["campo_nombre"=>"compra_codigo","campo_marcador"=>":Codigo","campo_valor"=>$codigo_compra],
        ["campo_nombre"=>"compra_fecha","campo_marcador"=>":Fecha","campo_valor"=>$fecha],
        ["campo_nombre"=>"compra_total","campo_marcador"=>":Total","campo_valor"=>$total],
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
        $id_compra_p = $this->ejecutarConsulta("SELECT compra_id FROM compra WHERE compra_codigo='$codigo_compra'");
        $id_compra_p = $id_compra_p->fetch()['compra_id'];

        if($pago_inicial > 0){
            $datos_pago_inicial=[
                ["campo_nombre"=>"compra_id","campo_marcador"=>":IdCompra","campo_valor"=>$id_compra_p],
                ["campo_nombre"=>"usuario_id","campo_marcador"=>":Usuario","campo_valor"=>$_SESSION['id']],
                ["campo_nombre"=>"pago_fecha","campo_marcador"=>":Fecha","campo_valor"=>$fecha],
                ["campo_nombre"=>"pago_monto","campo_marcador"=>":Monto","campo_valor"=>$pago_inicial],
                ["campo_nombre"=>"pago_metodo","campo_marcador"=>":Metodo","campo_valor"=>"Efectivo"],
                ["campo_nombre"=>"pago_referencia","campo_marcador"=>":Ref","campo_valor"=>"Pago inicial al registrar"]
            ];
            $this->guardarDatos("compra_pagos",$datos_pago_inicial);
        }

        foreach($_SESSION['datos_compra'] as $detalle){
            $datos_detalle=[ 
                ["campo_nombre"=>"compra_id","campo_marcador"=>":IdCompra","campo_valor"=>$id_compra_p], 
                ["campo_nombre"=>"producto_id","campo_marcador"=>":Producto","campo_valor"=>$detalle['producto_id']], 
                ["campo_nombre"=>"compra_detalle_cantidad","campo_marcador"=>":Cantidad","campo_valor"=>$detalle['compra_cantidad']], 
                ["campo_nombre"=>"compra_detalle_precio","campo_marcador"=>":Precio","campo_valor"=>$detalle['compra_costo']] 
            ];
            $this->guardarDatos("compra_detalle",$datos_detalle);
        }
        
        unset($_SESSION['datos_compra']);

        // --- EL DISPARADOR AQUÍ ---
        $alerta=[
            "tipo"=>"confirmar",
            "titulo"=>"Orden ".$codigo_compra." Generada",
            "texto"=>"La compra se registró con éxito. ¿Desea imprimir el comprobante PDF ahora?",
            "icono"=>"success",
            "confirmButtonText" => "Sí, imprimir",
            "cancelButtonText" => "No, después",
            "url"=>APP_URL."app/pdf/report_purchase.php?code=".$codigo_compra
        ];

    }else{ 
        $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo registrar la orden","icono"=>"error"]; 
    }
    return json_encode($alerta);
}

    /*---------- Controlador para Anular Compra (Antes era Eliminar) ----------*/
    public function eliminarCompraControlador() {
        // Recibimos el ID de la compra
        $id = $this->limpiarCadena($_POST['compra_id']);

        // 1. Verificamos que la compra exista
        $check_compra = $this->ejecutarConsulta("SELECT * FROM compra WHERE compra_id='$id'");
        
        if($check_compra->rowCount() <= 0){ 
            return json_encode([
                "tipo" => "simple",
                "titulo" => "Error",
                "texto" => "La compra no existe en el sistema.",
                "icono" => "error"
            ]); 
        }

        // 2. IMPORTANTE: En lugar de DELETE, hacemos UPDATE
        // Esto mueve la compra a la pestaña de "Anuladas"
        $anular = $this->ejecutarConsulta("UPDATE compra SET compra_estado='Anulada' WHERE compra_id='$id'");

        if($anular->rowCount() == 1){ 
            return json_encode([
                "tipo" => "recargar",
                "titulo" => "Orden Anulada",
                "texto" => "La orden ha sido movida a la sección de Anuladas correctamente.",
                "icono" => "success"
            ]); 
        } else { 
            return json_encode([
                "tipo" => "simple",
                "titulo" => "Error",
                "texto" => "No se pudo anular la orden en este momento.",
                "icono" => "error"
            ]); 
        }
    }
/*---------- Controlador para recibir mercancía ----------*/
    public function registrarRecepcionControlador() {
        $compra_id = $this->limpiarCadena($_POST['compra_id']);
        $productos = $_POST['productos_recibidos']; 
        
        // 1. Crear el encabezado de la recepción
        $datos_recepcion = [
            ["campo_nombre"=>"compra_id","campo_marcador"=>":Compra","campo_valor"=>$compra_id],
            ["campo_nombre"=>"usuario_id","campo_marcador"=>":Usuario","campo_valor"=>$_SESSION['id']],
            ["campo_nombre"=>"recepcion_fecha","campo_marcador"=>":Fecha","campo_valor"=>date("Y-m-d")]
        ];
        
        $guardar = $this->guardarDatos("recepcion", $datos_recepcion);

        if($guardar->rowCount() == 1){
            
            // SOLUCIÓN AL ERROR 1452: Obtener el ID de la recepción que acabamos de crear
            $consulta_id = $this->ejecutarConsulta("SELECT recepcion_id FROM recepcion WHERE compra_id='$compra_id' ORDER BY recepcion_id DESC LIMIT 1");
            $re_id = $consulta_id->fetch();
            $recepcion_id = $re_id['recepcion_id'];

            foreach($productos as $id_prod => $cant_llegó) {
                
                // Forzamos que la cantidad sea un número entero limpio
                $cantidad_real = (int)$this->limpiarCadena($cant_llegó);

                if($cantidad_real > 0) {
                    // 2. Guardar detalle con la cantidad REAL que escribió Ander
                    $this->ejecutarConsulta("INSERT INTO recepcion_detalle (recepcion_id, producto_id, cantidad_recibida) 
                                            VALUES ('$recepcion_id', '$id_prod', '$cantidad_real')");

                    // 3. Actualizamos el stock físico sumando solo esos 7
                    $this->ejecutarConsulta("UPDATE producto SET producto_stock = producto_stock + $cantidad_real WHERE producto_id = '$id_prod'");
                    
                    // 4. Actualizamos el costo y precio inteligente
                    if(method_exists($this, 'actualizarPrecioInteligente')){
                        $this->actualizarPrecioInteligente($id_prod, $compra_id);
                    }
                }
            }

            // 5. Verificar si la compra se completó o sigue parcial
            $this->actualizarEstadoCompra($compra_id);

            $alerta = [
                "tipo" => "recargar",
                "titulo" => "¡Entrada registrada!",
                "texto" => "La mercancía ha sido ingresada al inventario con éxito.",
                "icono" => "success"
            ];
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "No se pudo registrar la recepción, por favor intente nuevamente.",
                "icono" => "error"
            ];
        }
        return json_encode($alerta);
    }

    /*---------- Función interna: Actualizar Precio Inteligente ----------*/
    protected function actualizarPrecioInteligente($producto_id, $compra_id){
        // 1. Buscamos el costo que se pactó en la compra para este producto
        $check_costo = $this->ejecutarConsulta("SELECT compra_detalle_precio FROM compra_detalle WHERE compra_id='$compra_id' AND producto_id='$producto_id'");
        $nuevo_costo = $check_costo->fetch()['compra_detalle_precio'];

        // 2. Buscamos el precio de venta actual para mantener el margen de ganancia
        $check_prod = $this->ejecutarConsulta("SELECT producto_costo, producto_precio FROM producto WHERE producto_id='$producto_id'");
        $info_prod = $check_prod->fetch();

        if($info_prod['producto_costo'] > 0){
            $porcentaje_ganancia = ($info_prod['producto_precio'] - $info_prod['producto_costo']) / $info_prod['producto_costo'];
            $nuevo_precio_venta = $nuevo_costo + ($nuevo_costo * $porcentaje_ganancia);
        } else {
            $nuevo_precio_venta = $nuevo_costo * 1.20; // 20% por defecto si era costo 0
        }

        // 3. Actualizamos la ficha del producto
        $this->ejecutarConsulta("UPDATE producto SET producto_costo='$nuevo_costo', producto_precio='$nuevo_precio_venta' WHERE producto_id='$producto_id'");
    }

/*---------- Función interna: Actualizar Estado de Compra ----------*/
    protected function actualizarEstadoCompra($compra_id){
        
        // 1. Obtenemos el total pedido
        $pedido = $this->ejecutarConsulta("SELECT SUM(compra_detalle_cantidad) FROM compra_detalle WHERE compra_id='$compra_id'");
        $total_pedido = $pedido->fetchColumn();
        
        // Forzamos a que sea un número (si es NULL, será 0)
        $total_pedido = ($total_pedido) ? (float)$total_pedido : 0;

        // 2. Obtenemos el total recibido sumando TODAS las recepciones de esta compra
        $recibido = $this->ejecutarConsulta("SELECT SUM(rd.cantidad_recibida) 
            FROM recepcion_detalle rd 
            INNER JOIN recepcion r ON rd.recepcion_id=r.recepcion_id 
            WHERE r.compra_id='$compra_id'");
        $total_recibido = $recibido->fetchColumn();
        
        // Forzamos a que sea un número
        $total_recibido = ($total_recibido) ? (float)$total_recibido : 0;

        // 3. Lógica de comparación exacta
        if($total_recibido >= $total_pedido && $total_pedido > 0){
            $nuevo_estado = "Completado";
        } elseif($total_recibido > 0 && $total_recibido < $total_pedido){
            $nuevo_estado = "Parcial";
        } else {
            $nuevo_estado = "Pendiente";
        }

        // 4. Actualizamos el estado
        $this->ejecutarConsulta("UPDATE compra SET compra_estado='$nuevo_estado' WHERE compra_id='$compra_id'");
    }

    /*---------- Controlador listar órdenes para recepción ----------*/
    public function listarRecepcionesControlador($pagina, $registros, $url) {
        $pagina = $this->limpiarCadena($pagina);
        $registros = $this->limpiarCadena($registros);
        $url = APP_URL . $url . "/";
        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        // Solo traemos compras que NO estén completadas ni anuladas
        $consulta_datos = "SELECT c.*, p.proveedor_nombre FROM compra c 
                        INNER JOIN proveedor p ON c.proveedor_id = p.proveedor_id 
                        WHERE c.compra_estado IN ('Pendiente', 'Parcial') 
                        ORDER BY c.compra_id DESC LIMIT $inicio, $registros";

        $datos = $this->ejecutarConsulta($consulta_datos);
        $datos = $datos->fetchAll();

        $tabla .= '<div class="table-container">
                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                    <thead>
                        <tr class="has-background-link-dark">
                            <th class="has-text-centered has-text-white">Código</th>
                            <th class="has-text-centered has-text-white">Fecha</th>
                            <th class="has-text-centered has-text-white">Proveedor</th>
                            <th class="has-text-centered has-text-white">Estado</th>
                            <th class="has-text-centered has-text-white">Progreso</th>
                            <th class="has-text-centered has-text-white">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>';

        if (count($datos) >= 1) {
            foreach ($datos as $rows) {
                $compra_id = $rows['compra_id'];

                // Calculamos cuánto se pidió vs cuánto ha llegado
                $pedido = $this->ejecutarConsulta("SELECT SUM(compra_detalle_cantidad) FROM compra_detalle WHERE compra_id='$compra_id'")->fetchColumn();
                $recibido = $this->ejecutarConsulta("SELECT SUM(rd.cantidad_recibida) FROM recepcion_detalle rd INNER JOIN recepcion r ON rd.recepcion_id=r.recepcion_id WHERE r.compra_id='$compra_id'")->fetchColumn();
                
                $recibido = ($recibido) ? $recibido : 0;
                $porcentaje = ($pedido > 0) ? ($recibido * 100) / $pedido : 0;

                // Color del tag según estado
                $color_tag = ($rows['compra_estado'] == 'Pendiente') ? 'is-info' : 'is-warning';

                $tabla .= '
                <tr class="has-text-centered">
                    <td>' . $rows['compra_codigo'] . '</td>
                    <td>' . date("d-m-Y", strtotime($rows['compra_fecha'])) . '</td>
                    <td>' . $rows['proveedor_nombre'] . '</td>
                    <td><span class="tag ' . $color_tag . ' is-light">' . $rows['compra_estado'] . '</span></td>
                    <td style="vertical-align: middle;">
                        <progress class="progress is-link is-small" value="' . $porcentaje . '" max="100">' . $porcentaje . '%</progress>
                        <small>' . $recibido . ' de ' . $pedido . ' unidades</small>
                    </td>
                    <td>
                        <a href="' . APP_URL . 'purchaseReceptionDetail/' . $compra_id . '/" class="button is-link is-rounded is-small">
                            <i class="fas fa-boxes"></i> &nbsp; Recibir
                        </a>
                    </td>
                </tr>';
            }
        } else {
            $tabla .= '<tr class="has-text-centered"><td colspan="6">No hay órdenes pendientes por recibir mercancía</td></tr>';
        }

        $tabla .= '</tbody></table></div>';
        return $tabla;
    }
/*---------- Controlador para listar compras (FILTRADO POR ESTADO) ----------*/
    public function listarCompraControlador($pagina, $registros, $url, $busqueda) {
        $pagina = $this->limpiarCadena($pagina);
        $registros = $this->limpiarCadena($registros);
        $busqueda = $this->limpiarCadena($busqueda);
        $tabla = "";

        // Separamos la URL para identificar si estamos en /Pendiente, /Pagada o /Anulada
        $url_split = explode("/", $url);
        $estado_view = (isset($url_split[1]) && $url_split[1] != "") ? $url_split[1] : "Pendiente";
        
        $url = APP_URL . $url . "/";

        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        // DEFINICIÓN DE FILTROS SQL
        if(isset($busqueda) && $busqueda != ""){
            $filtro_sql = "WHERE (c.compra_codigo LIKE '%$busqueda%' OR p.proveedor_nombre LIKE '%$busqueda%')";
        } else {
            if($estado_view == "Anulada"){
                $filtro_sql = "WHERE c.compra_estado='Anulada'";
            } elseif($estado_view == "Pagada"){
                $filtro_sql = "WHERE c.compra_estado_pago='Pagado' AND c.compra_estado!='Anulada'";
            } else {
                // Por defecto: Pendientes (No pagadas y no anuladas)
                $filtro_sql = "WHERE c.compra_estado_pago!='Pagado' AND c.compra_estado!='Anulada'";
            }
        }

        $consulta_datos = "SELECT c.*, p.proveedor_nombre, u.usuario_nombre, u.usuario_apellido 
                            FROM compra c 
                            INNER JOIN proveedor p ON c.proveedor_id = p.proveedor_id 
                            INNER JOIN usuario u ON c.usuario_id = u.usuario_id 
                            $filtro_sql
                            ORDER BY c.compra_id DESC LIMIT $inicio, $registros";

        $datos = $this->ejecutarConsulta($consulta_datos);
        $datos = $datos->fetchAll();

        $tabla .= '<div class="table-container">
                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                    <thead>
                        <tr class="has-background-link-dark">
                            <th class="has-text-centered has-text-white">Código</th>
                            <th class="has-text-centered has-text-white">Fecha</th>
                            <th class="has-text-centered has-text-white">Proveedor</th>
                            <th class="has-text-centered has-text-white">Total</th>
                            <th class="has-text-centered has-text-white">Saldo</th>
                            <th class="has-text-centered has-text-white">Estado</th>
                            <th class="has-text-centered has-text-white">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>';

        if (count($datos) >= 1) {
            foreach ($datos as $rows) {
                
                // Lógica de colores basada en el estado de pago
                if($rows['compra_estado'] == "Anulada"){
                    $color_tag = "is-dark"; 
                    $texto_estado = "Anulada";
                } else {
                    if($rows['compra_estado_pago'] == "Pagado"){
                        $color_tag = "is-success";
                        $texto_estado = "Pagado";
                    } elseif($rows['compra_estado_pago'] == "Parcial"){
                        $color_tag = "is-warning";
                        $texto_estado = "Parcial";
                    } else {
                        $color_tag = "is-danger";
                        $texto_estado = "Pendiente";
                    }
                }

                $tabla .= '
                <tr class="has-text-centered">
                    <td>' . $rows['compra_codigo'] . '</td>
                    <td>' . date("d-m-Y", strtotime($rows['compra_fecha'])) . '</td>
                    <td>' . $rows['proveedor_nombre'] . '</td>
                    <td>' . MONEDA_SIMBOLO . number_format($rows['compra_total'], 2, '.', ',') . '</td>
                    <td class="has-text-weight-bold">' . MONEDA_SIMBOLO . number_format($rows['compra_saldo_pendiente'], 2, '.', ',') . '</td>
                    <td><span class="tag ' . $color_tag . ' is-rounded">' . $texto_estado . '</span></td>
                    <td>
                        <div class="buttons is-centered">
                            <button type="button" class="button is-link is-light is-rounded is-small" onclick="verHistorialAbonos(\'' . $rows['compra_id'] . '\', \'' . $rows['compra_codigo'] . '\')" title="Ver Historial de Pagos">
                                <i class="fas fa-history"></i>
                            </button>

                            <a href="' . APP_URL . 'purchaseDetail/' . $rows['compra_id'] . '/" class="button is-info is-rounded is-small" title="Ver Detalle Compra">
                                <i class="fas fa-eye"></i>
                            </a>';

                            if($rows['compra_estado'] != "Anulada"){
                                // Botón de Recibir Mercancía
                                $tabla .= '
                                    <a href="' . APP_URL . 'purchaseReceptionDetail/' . $rows['compra_id'] . '/" class="button is-success is-rounded is-small" title="Recibir Mercancía">
                                        <i class="fas fa-truck-loading"></i>
                                    </a>';

                                // --- NUEVO: BOTÓN DE ABONO (Solo si hay saldo pendiente) ---
                                if($rows['compra_saldo_pendiente'] > 0){
                                    $tabla .= '
                                        <a href="' . APP_URL . 'purchasePay/' . $rows['compra_id'] . '/" class="button is-primary is-rounded is-small" title="Realizar Abono">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </a>';
                                }

                                // Botones de Impresión
                                $tabla .= '
                                    <button type="button" class="button is-warning is-rounded is-small" onclick="print_invoice(\'' . APP_URL . 'app/pdf/purchaseReceipt.php?id=' . $rows['compra_id'] . '\')" title="Imprimir Nota de Entrega">
                                        <i class="fas fa-file-signature"></i>
                                    </button>

                                    <button type="button" class="button is-link is-rounded is-small" onclick="print_invoice(\'' . APP_URL . 'app/pdf/report_purchase.php?code=' . $rows['compra_codigo'] . '\')" title="Imprimir Orden de Compra">
                                        <i class="fas fa-file-invoice"></i>
                                    </button>';

                                // Botón de Anular (Solo si no está pagada)
                                if($rows['compra_estado_pago'] != "Pagado"){
                                    $tabla .= '
                                        <form class="FormularioAjax" action="' . APP_URL . 'app/ajax/compraAjax.php" method="POST" style="display: inline-block; margin-left: 5px;">
                                            <input type="hidden" name="modulo_compra" value="eliminar_compra">
                                            <input type="hidden" name="compra_id" value="' . $rows['compra_id'] . '">
                                            <button type="submit" class="button is-danger is-rounded is-small" title="Anular Compra">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>';
                                }
                            }
                $tabla .= '</div></td></tr>';
            }
        } else {
            $tabla .= '<tr class="has-text-centered"><td colspan="7">No hay registros en esta categoría</td></tr>';
        }

        $tabla .= '</tbody></table></div>';
        return $tabla;
    }
 /*---------- Controlador para registrar un abono ----------*/
    public function registrarAbonoControlador(){
        // Almacenando datos del formulario
        $id = $this->limpiarCadena($_POST['pago_compra_id']);
        $monto = $this->limpiarCadena($_POST['pago_monto']);
        $metodo = $this->limpiarCadena($_POST['pago_metodo']);
        $referencia = $this->limpiarCadena($_POST['pago_referencia']);
        
        // 1. Verificación de seguridad del monto
        if($monto <= 0){
            return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"El monto debe ser mayor a cero", "icono"=>"error"]);
        }

        // --- NUEVA VALIDACIÓN PARA REFERENCIA ---
        if($referencia == "" && ($metodo == "Efectivo" || $metodo == "Divisas")){
            $referencia = "EFECTIVO/DIVISA";
        } elseif ($referencia == "") {
            // Por si es transferencia y se le olvidó poner el número
            return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"Para este método de pago la referencia es obligatoria", "icono"=>"error"]);
        }

        // 2. Consultamos el saldo actual de la compra
        $check_compra = $this->ejecutarConsulta("SELECT compra_saldo_pendiente FROM compra WHERE compra_id='$id'");
        
        if($check_compra->rowCount() <= 0){
            return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"La compra no existe", "icono"=>"error"]);
        }
        
        $datos = $check_compra->fetch();
        $saldo_actual = $datos['compra_saldo_pendiente'];

        // 3. Validar que no abone más de lo que debe
        if($monto > $saldo_actual){
            return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"El monto no puede superar el saldo actual ($$saldo_actual)", "icono"=>"error"]);
        }

        $nuevo_saldo = $saldo_actual - $monto;
        $estado_pago = ($nuevo_saldo <= 0) ? "Pagado" : "Parcial";

        // 4. Registrar el abono en 'compra_pagos'
        $datos_pago = [
            ["campo_nombre"=>"compra_id","campo_marcador"=>":Id","campo_valor"=>$id],
            ["campo_nombre"=>"pago_monto","campo_marcador"=>":Monto","campo_valor"=>$monto],
            ["campo_nombre"=>"pago_fecha","campo_marcador"=>":Fecha","campo_valor"=>date("Y-m-d")],
            ["campo_nombre"=>"pago_metodo","campo_marcador"=>":Metodo","campo_valor"=>$metodo],
            ["campo_nombre"=>"pago_referencia","campo_marcador"=>":Referencia","campo_valor"=>$referencia],
            ["campo_nombre"=>"usuario_id","campo_marcador"=>":Usuario","campo_valor"=>$_SESSION['id']]
        ];

        $guardar_pago = $this->guardarDatos("compra_pagos", $datos_pago);

        if($guardar_pago->rowCount() >= 1){
            // 5. ACTUALIZACIÓN CRÍTICA: Bajamos el saldo en la tabla compra
            $datos_update_compra = [
                ["campo_nombre"=>"compra_saldo_pendiente","campo_marcador"=>":Saldo","campo_valor"=>$nuevo_saldo],
                ["campo_nombre"=>"compra_estado_pago","campo_marcador"=>":Estado","campo_valor"=>$estado_pago]
            ];
            
            $condicion = ["condicion_campo"=>"compra_id", "condicion_marcador"=>":ID", "condicion_valor"=>$id];
            $this->actualizarDatos("compra", $datos_update_compra, $condicion);

            return json_encode([
                "tipo"=>"recargar", 
                "titulo"=>"¡Abono registrado!", 
                "texto"=>"Se han abonado $$monto correctamente. Saldo actual: $$nuevo_saldo",
                "icono"=>"success"
            ]);
        } else {
            return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"No se pudo registrar el pago en la base de datos", "icono"=>"error"]);
        }
    }

    /*---------- Controlador para listar abonos ----------*/
    public function listarAbonosCompra($id){
        $id = $this->limpiarCadena($id);
        
        $consulta = "SELECT cp.*, u.usuario_nombre, u.usuario_apellido 
            FROM compra_pagos cp 
            INNER JOIN usuario u ON cp.usuario_id = u.usuario_id 
            WHERE cp.compra_id='$id' 
            ORDER BY cp.pago_fecha DESC";
            
        return $this->ejecutarConsulta($consulta);
    }

    /*---------- Controlador para generar la tabla HTML del historial ----------*/
    public function listarAbonosCompraControlador($id){
        $datos = $this->listarAbonosCompra($id);
        
        if($datos->rowCount() > 0){
            $datos = $datos->fetchAll();
            $tabla = '
            <table class="table is-bordered is-striped is-narrow is-fullwidth">
                <thead>
                    <tr class="is-info">
                        <th class="has-text-centered">Fecha</th>
                        <th class="has-text-centered">Monto</th>
                        <th class="has-text-centered">Método</th>
                        <th class="has-text-centered">Acciones</th>
                    </tr>
                </thead>
                <tbody>';
                
            foreach($datos as $pago){
                $tabla .= '
                    <tr class="has-text-centered">
                        <td>'.date("d/m/Y", strtotime($pago['pago_fecha'])).'</td>
                        <td class="has-text-weight-bold">$'.number_format($pago['pago_monto'], 2).'</td>
                        <td>'.$pago['pago_metodo'].'</td>
                        <td>
                            <form class="FormularioAjax" action="'.APP_URL.'app/ajax/compraAjax.php" method="POST" style="display: inline-block;">
                                <input type="hidden" name="modulo_compra" value="eliminar_abono">
                                <input type="hidden" name="pago_id" value="'.$pago['pago_id'].'">
                                <input type="hidden" name="compra_id" value="'.$id.'">
                                <button type="submit" class="button is-danger is-outline is-small is-rounded">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>';
            }
            $tabla .= '</tbody></table>';
        } else {
            $tabla = '<p class="has-text-centered">No hay abonos registrados.</p>';
        }
        return $tabla;
    }
    /*---------- Controlador para eliminar abono ----------*/
    public function eliminarAbonoControlador(){
        $pago_id = $this->limpiarCadena($_POST['pago_id']);
        $compra_id = $this->limpiarCadena($_POST['compra_id']);

        // 1. Obtener los datos del pago antes de borrarlo
        $check_pago = $this->ejecutarConsulta("SELECT * FROM compra_pagos WHERE pago_id='$pago_id'");
        
        if($check_pago->rowCount() <= 0){
            return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"El pago no existe", "icono"=>"error"]);
        }
        $datos_pago = $check_pago->fetch();
        $monto_a_devolver = $datos_pago['pago_monto'];

        // 2. Eliminar el pago
        $eliminar = $this->ejecutarConsulta("DELETE FROM compra_pagos WHERE pago_id='$pago_id'");

        if($eliminar->rowCount() == 1){
            // 3. Recalcular saldo en la tabla compra
            $check_compra = $this->ejecutarConsulta("SELECT compra_saldo_pendiente FROM compra WHERE compra_id='$compra_id'");
            $datos_c = $check_compra->fetch();
            
            $nuevo_saldo = $datos_c['compra_saldo_pendiente'] + $monto_a_devolver;
            $estado = ($nuevo_saldo > 0) ? "Parcial" : "Pagado";

            $datos_upd = [
                ["campo_nombre"=>"compra_saldo_pendiente","campo_marcador"=>":Saldo","campo_valor"=>$nuevo_saldo],
                ["campo_nombre"=>"compra_estado_pago","campo_marcador"=>":Estado","campo_valor"=>$estado]
            ];
            $cond = ["condicion_campo"=>"compra_id", "condicion_marcador"=>":ID", "condicion_valor"=>$compra_id];
            $this->actualizarDatos("compra", $datos_upd, $cond);

            return json_encode(["tipo"=>"recargar", "titulo"=>"Abono eliminado", "texto"=>"El saldo ha sido restaurado correctamente", "icono"=>"success"]);
        } else {
            return json_encode(["tipo"=>"simple", "titulo"=>"Error", "texto"=>"No se pudo eliminar el abono", "icono"=>"error"]);
        }
    }

    /*---------- Controlador para recibir producto ----------*/
    public function recibirProductoControlador() {
        $compra_id = $this->limpiarCadena($_POST['compra_id']);
        $producto_id = $this->limpiarCadena($_POST['producto_id']);
        $cantidad_recibida = $this->limpiarCadena($_POST['cantidad_recibida']);

        // 1. Actualizar el stock en la tabla producto
        $update_stock = $this->ejecutarConsulta("UPDATE producto 
            SET producto_stock = producto_stock + $cantidad_recibida 
            WHERE producto_id = '$producto_id'");

        if($update_stock) {
            // 2. Opcional: Registrar en una tabla de movimientos para auditoría
            /* $this->guardarMovimiento($producto_id, $cantidad_recibida, "Entrada por Compra", $compra_id); */
            
            return json_encode([
                "tipo" => "recargar",
                "titulo" => "Stock Actualizado",
                "texto" => "Se han sumado $cantidad_recibida unidades al inventario.",
                "icono" => "success"
            ]);
        }
    }

    /*---------- Vaciar TODAS las Anuladas (Eliminación Real) ----------*/
    public function vaciarAnuladasControlador(){
        // 1. Buscamos todas las compras anuladas para saber qué detalles borrar
        $check = $this->ejecutarConsulta("SELECT compra_id FROM compra WHERE compra_estado='Anulada'");
        
        if($check->rowCount() > 0){
            // Borramos detalles primero por integridad referencial
            $this->ejecutarConsulta("DELETE FROM compra_detalle WHERE compra_id IN (SELECT compra_id FROM compra WHERE compra_estado='Anulada')");
            
            // Borramos las cabeceras de compra
            $eliminar = $this->ejecutarConsulta("DELETE FROM compra WHERE compra_estado='Anulada'");

            return json_encode([
                "tipo" => "recargar",
                "titulo" => "Papelera Vaciada",
                "texto" => "Se eliminaron todos los registros anulados permanentemente.",
                "icono" => "success"
            ]);
        } else {
            return json_encode([
                "tipo" => "simple",
                "titulo" => "Aviso",
                "texto" => "No hay compras anuladas para eliminar.",
                "icono" => "info"
            ]);
        }
    }
}