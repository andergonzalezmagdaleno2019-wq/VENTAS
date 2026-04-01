<?php
	
	require_once "../../config/app.php";
	require_once "../views/inc/session_start.php";
	require_once "../../autoload.php";
	
	use app\controllers\saleController;

	if(isset($_POST['modulo_venta'])){

		$insVenta = new saleController();

		/*--------- Obtener marcas y modelos por categoría ---------*/
		if($_POST['modulo_venta'] == "obtener_filtros_categoria"){
			$id_cat = $insVenta->limpiarCadena($_POST['categoria_id']);
			
			// Consultamos marcas únicas de esa categoría
			$query_marcas = $insVenta->seleccionarDatos("Normal", "producto WHERE categoria_id='$id_cat' GROUP BY producto_marca", "producto_marca", 0);
			$marcas = $query_marcas->fetchAll(PDO::FETCH_COLUMN);

			// Consultamos modelos únicos de esa categoría
			$query_modelos = $insVenta->seleccionarDatos("Normal", "producto WHERE categoria_id='$id_cat' GROUP BY producto_modelo", "producto_modelo", 0);
			$modelos = $query_modelos->fetchAll(PDO::FETCH_COLUMN);

			echo json_encode([
				"marcas" => $marcas,
				"modelos" => $modelos
			]);
			exit(); // Importante para que no siga ejecutando el resto del script
		}

		/*--------- Buscar producto por codigo ---------*/
		if($_POST['modulo_venta']=="buscar_codigo"){
			echo $insVenta->buscarCodigoVentaControlador();
		}

		/*--------- Buscar productos por categoria ---------*/
		if($_POST['modulo_venta']=="buscar_por_categoria"){
			echo $insVenta->buscarPorCategoriaVentaControlador();
		}

		/*--------- Agregar producto a carrito ---------*/
		if($_POST['modulo_venta']=="agregar_producto"){
			echo $insVenta->agregarProductoCarritoControlador();
        }

        /*--------- Remover producto de carrito ---------*/
		if($_POST['modulo_venta']=="remover_producto"){
			echo $insVenta->removerProductoCarritoControlador();
		}

		/*--------- Actualizar producto de carrito ---------*/
		if($_POST['modulo_venta']=="actualizar_producto"){
			echo $insVenta->actualizarProductoCarritoControlador();
		}

		/*--------- Buscar cliente ---------*/
		if($_POST['modulo_venta']=="buscar_cliente"){
			echo $insVenta->buscarClienteVentaControlador();
		}

		/*--------- Agregar cliente a carrito ---------*/
		if($_POST['modulo_venta']=="agregar_cliente"){
			echo $insVenta->agregarClienteVentaControlador();
		}

		/*--------- Remover cliente de carrito ---------*/
		if($_POST['modulo_venta']=="remover_cliente"){
			echo $insVenta->removerClienteVentaControlador();
		}

		/*--------- Registrar venta ---------*/
		if($_POST['modulo_venta']=="registrar_venta"){
			echo $insVenta->registrarVentaControlador();
		}

		/*--------- Vaciar carrito de venta ---------*/
		if($_POST['modulo_venta']=="vaciar_carrito"){
			echo $insVenta->vaciarCarritoVentaControlador();
		}

		/*--------- Eliminar venta ---------*/
		if($_POST['modulo_venta']=="eliminar_venta"){
			echo $insVenta->eliminarVentaControlador();
		}
		
	}else{
		session_destroy();
		header("Location: ".APP_URL."login/");
	}