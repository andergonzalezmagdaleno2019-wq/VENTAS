<?php
	namespace app\models;

	class viewsModel{
		protected function obtenerVistasModelo($vista){
			/* LISTA BLANCA DE PÁGINAS PERMITIDAS */
			$listaBlanca=[
				"dashboard",
				"cashierNew","cashierList","cashierSearch","cashierUpdate",
				"userNew","userList","userUpdate","userSearch","userPhoto",
				"clientNew","clientList","clientSearch","clientUpdate",
				"categoryNew","categoryList","categorySearch","categoryUpdate","subcategoryNew","subcategorylist","subcategorySearch","subcategoryUpdate","productNew","productList","productSearch","productUpdate","productPhoto","productCategory", "inventoryReport",
				"companyNew",
				"saleNew","saleList","saleSearch","saleDetail", "saleReport",
				"logOut","backup",
				"providerNew", "providerList", "providerUpdate",
				"purchaseNew", "purchaseList", "purchaseReport", "purchaseSearch", "auditList",
			];

			if(in_array($vista, $listaBlanca)){
				if(is_file("./app/views/content/".$vista."-view.php")){
					$contenido="./app/views/content/".$vista."-view.php";
				}else{
					$contenido="404";
				}
			}elseif($vista=="login" || $vista=="index"){
				$contenido="login";
			}else{
				$contenido="404";
			}
			return $contenido;
		}
	}