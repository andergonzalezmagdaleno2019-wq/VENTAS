<?php
	namespace app\controllers;
	use app\models\mainModel;

	class searchController extends mainModel{
		public function modulosBusquedaControlador($modulo){
			$listaModulos=[
                'userSearch','cashierSearch','subcategorySearch',"subcategorylist",'clientSearch','categorySearch','productSearch','saleSearch',
                'userList', 'productList', 'cashierList', 'clientList', 'categoryList', 'saleList', 'purchaseList', 'providerList'
            ];
			if(in_array($modulo, $listaModulos)){
				return false;
			}else{
				return true;
			}
		}

		public function iniciarBuscadorControlador(){
		    $url=$this->limpiarCadena($_POST['modulo_url']);
			$texto=$this->limpiarCadena($_POST['txt_buscador']);
			if($this->modulosBusquedaControlador($url)){
				$alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Módulo no permitido","icono"=>"error"]; return json_encode($alerta); exit();
			}
			if($texto==""){
				$alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Introduce un termino de busqueda","icono"=>"error"]; return json_encode($alerta); exit();
			}
			$_SESSION[$url]=$texto;
			$alerta=["tipo"=>"redireccionar","url"=>APP_URL.$url."/"];
			return json_encode($alerta);
		}

		public function eliminarBuscadorControlador(){
			$url=$this->limpiarCadena($_POST['modulo_url']);
			if($this->modulosBusquedaControlador($url)){
				$alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Módulo no permitido","icono"=>"error"]; return json_encode($alerta); exit();
			}
			unset($_SESSION[$url]);
			$alerta=["tipo"=>"redireccionar","url"=>APP_URL.$url."/"];
			return json_encode($alerta);
		}
	}