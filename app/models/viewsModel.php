<?php
    namespace app\models;

    class viewsModel{
        protected function obtenerVistasModelo($vista){
            $listaBlanca=[
                "dashboard",
                "cashierNew","cashierList","cashierSearch","cashierUpdate",
                "userNew","userList","userProfile", "userUpdate","userSearch","userPhoto",
                "clientNew","clientList","clientSearch","clientUpdate",
                "categoryNew","categoryList","categorySearch","categoryUpdate","subcategoryNew","subcategorylist","subcategorySearch","subcategoryUpdate","productNew","productList","productSearch","productUpdate","productPhoto","productCategory", "inventoryReport",
                "companyNew",
                "saleNew","saleList","saleSearch","saleDetail", "saleReport",
                "logOut", "backup","providerNew", "providerList", "providerUpdate",
                "purchaseNew", "purchaseList","purchasePayment", "purchase_reception", "purchaseDetail", "purchaseReceptionDetail","purchaseReport", "purchaseSearch","purchasePay", "auditList",
            ];

            if(in_array($vista, $listaBlanca)){
                
                if(isset($_SESSION['rol'])){
                    $rol_usuario = $_SESSION['rol']; 
                    
                    $vistas_vendedor = [
                        "dashboard", "logOut", 
                        "saleNew", "saleList", "saleSearch", "saleDetail", "saleReport", 
                        "clientNew", "clientList", "clientSearch", "clientUpdate", 
                        "userProfile", "userPhoto" 
                    ];

                    $vistas_supervisor = [
                        "dashboard", "logOut", 
                        "clientNew", "clientList", "clientSearch", "clientUpdate", 
                        "providerNew", "providerList", "providerUpdate", 
                        "purchaseNew", "purchaseList", "purchasePayment", "purchase_reception", "purchaseDetail", "purchaseReceptionDetail", "purchaseReport", "purchaseSearch", "purchasePay", 
                        "categoryNew", "categoryList", "categorySearch", "categoryUpdate", "subcategoryNew", "subcategorylist", "subcategorySearch", "subcategoryUpdate", 
                        "productNew", "productList", "productSearch", "productUpdate", "productPhoto", "productCategory", "inventoryReport", 
                        "saleNew", "saleList", "saleSearch", "saleDetail", "saleReport", 
                        "userProfile", "userPhoto" 
                    ];

                    if($rol_usuario == 2 && !in_array($vista, $vistas_vendedor)){
                        return "404"; 
                    }

                    if($rol_usuario == 3 && !in_array($vista, $vistas_supervisor)){
                        return "404"; 
                    }
                }

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