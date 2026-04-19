<?php

	namespace app\controllers;
	use app\models\mainModel;

	class companyController extends mainModel{

		/*----------  Controlador registrar empresa  ----------*/
		public function registrarEmpresaControlador(){

			# Almacenando datos#
		    $nombre=$this->limpiarCadena($_POST['empresa_nombre']);
		    $rif=$this->limpiarCadena($_POST['empresa_rif']);
		    $telefono=$this->limpiarCadena($_POST['empresa_telefono']);
		    $email=$this->limpiarCadena($_POST['empresa_email']);
		    $direccion=$this->limpiarCadena($_POST['empresa_direccion']);

		    # Verificando campos obligatorios #
            if($nombre==""){
            	$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"No has llenado todos los campos que son obligatorios","icono"=>"error"]; return json_encode($alerta); exit();
            }

            # Verificando integridad de los datos #
		    if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ., ]{4,85}",$nombre)){
		    	$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"El NOMBRE no coincide con el formato solicitado","icono"=>"error"]; return json_encode($alerta); exit();
		    }

            # VALIDACIÓN: El nombre de la empresa debe tener letras #
            if (!preg_match("/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/", $nombre)) {
                $alerta = ["tipo" => "simple", "titulo" => "Nombre Inválido", "texto" => "El nombre de la Empresa no puede ser solo números.", "icono" => "error"]; return json_encode($alerta); exit();
            }

            if($rif!=""){
                if($this->verificarDatos("[a-zA-Z0-9\- ]{5,40}",$rif)){
                    $alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"El RIF no coincide con el formato solicitado","icono"=>"error"]; return json_encode($alerta); exit();
                }
            }

		    if($telefono!=""){
		    	if($this->verificarDatos("[0-9()+]{8,20}",$telefono)){
			    	$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"El TELEFONO no coincide con el formato solicitado","icono"=>"error"]; return json_encode($alerta); exit();
			    }
		    }

		    if($direccion!=""){
		    	if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{4,97}",$direccion)){
			    	$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"La DIRECCION no coincide con el formato solicitado","icono"=>"error"]; return json_encode($alerta); exit();
			    }
		    }

		    if($email!=""){
				if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
					$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"Ha ingresado un correo electrónico no valido","icono"=>"error"]; return json_encode($alerta); exit();
				}
            }

			/*== PROCESANDO EL LOGO DE LA EMPRESA ==*/
            if(isset($_FILES['empresa_foto']) && $_FILES['empresa_foto']['name'] != "" && $_FILES['empresa_foto']['size'] > 0){
                
                $img_dir = "../views/img/";
                
                // Comprobando el formato de la imagen
                if($_FILES['empresa_foto']['type']=="image/jpeg" || $_FILES['empresa_foto']['type']=="image/png" || $_FILES['empresa_foto']['type']=="image/jpg"){
                    
                    // Comprobando el peso (Máximo 3MB)
                    if(($_FILES['empresa_foto']['size']/1024) <= 3072){
                        
                        // Forzamos el nombre a "logo.png" que es el que buscan los reportes PDF
                        $foto_nombre = "logo.png";
                        
                        // Otorgamos permisos y sobreescribimos la imagen
                        chmod($img_dir, 0777);
                        if(!move_uploaded_file($_FILES['empresa_foto']['tmp_name'], $img_dir.$foto_nombre)){
                            $alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"No se pudo subir el logo al servidor","icono"=>"error"]; return json_encode($alerta); exit();
                        }

                    }else{
                        $alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"La imagen supera el peso máximo permitido de 3MB","icono"=>"error"]; return json_encode($alerta); exit();
                    }

                }else{
                    $alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"El formato del logo no está permitido (solo JPG o PNG)","icono"=>"error"]; return json_encode($alerta); exit();
                }
            }

			$empresa_datos_reg=[
				["campo_nombre"=>"empresa_nombre","campo_marcador"=>":Nombre","campo_valor"=>$nombre],
				["campo_nombre"=>"empresa_rif","campo_marcador"=>":Rif","campo_valor"=>$rif],
				["campo_nombre"=>"empresa_telefono","campo_marcador"=>":Telefono","campo_valor"=>$telefono],
				["campo_nombre"=>"empresa_emailKV","campo_marcador"=>":Email","campo_valor"=>$email], 
				["campo_nombre"=>"empresa_direccion","campo_marcador"=>":Direccion","campo_valor"=>$direccion]
			];

			$registrar_empresa=$this->guardarDatos("empresa",$empresa_datos_reg);

			if($registrar_empresa->rowCount()==1){
                # REGISTRO EN BITÁCORA #
                $this->guardarBitacora("Sistema", "Registro", "Se registraron los datos iniciales de la empresa: " . $nombre);
				$alerta=["tipo"=>"recargar","titulo"=>"Empresa registrada","texto"=>"Los datos de la empresa se registraron con exito","icono"=>"success"];
			}else{
				$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"No se pudo registrar los datos de la empresa, por favor intente nuevamente","icono"=>"error"];
			}
			return json_encode($alerta);
		}


		/*----------  Controlador actualizar empresa  ----------*/
		public function actualizarEmpresaControlador(){

			$id=$this->limpiarCadena($_POST['empresa_id']);

			# Verificando empresa #
		    $datos=$this->ejecutarConsulta("SELECT * FROM empresa WHERE empresa_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"No hemos encontrado la empresa en el sistema","icono"=>"error"]; return json_encode($alerta); exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

		    # Almacenando datos#
		    $nombre=$this->limpiarCadena($_POST['empresa_nombre']);
            $rif=$this->limpiarCadena($_POST['empresa_rif']);
		    $telefono=$this->limpiarCadena($_POST['empresa_telefono']);
		    $email=$this->limpiarCadena($_POST['empresa_email']);
		    $direccion=$this->limpiarCadena($_POST['empresa_direccion']);

		    # Verificando campos obligatorios #
            if($nombre==""){
            	$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"No has llenado todos los campos que son obligatorios","icono"=>"error"]; return json_encode($alerta); exit();
            }

            # Verificando integridad de los datos #
		    if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ., ]{4,85}",$nombre)){
		    	$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"El NOMBRE no coincide con el formato solicitado","icono"=>"error"]; return json_encode($alerta); exit();
		    }

            # VALIDACIÓN: El nombre de la empresa debe tener letras #
            if (!preg_match("/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/", $nombre)) {
                $alerta = ["tipo" => "simple", "titulo" => "Nombre Inválido", "texto" => "El nombre de la Empresa no puede ser solo números.", "icono" => "error"]; return json_encode($alerta); exit();
            }

            if($rif!=""){
                if($this->verificarDatos("[a-zA-Z0-9\- ]{5,40}",$rif)){
                    $alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"El RIF no coincide con el formato solicitado","icono"=>"error"]; return json_encode($alerta); exit();
                }
            }

		    if($telefono!=""){
		    	if($this->verificarDatos("[0-9()+]{8,20}",$telefono)){
			    	$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"El TELEFONO no coincide con el formato solicitado","icono"=>"error"]; return json_encode($alerta); exit();
			    }
		    }

		    if($direccion!=""){
		    	if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{4,97}",$direccion)){
			    	$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"La DIRECCION no coincide con el formato solicitado","icono"=>"error"]; return json_encode($alerta); exit();
			    }
		    }

		    if($email!=""){
				if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
					$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"Ha ingresado un correo electrónico no valido","icono"=>"error"]; return json_encode($alerta); exit();
				}
            }

			$empresa_datos_up=[
				["campo_nombre"=>"empresa_nombre","campo_marcador"=>":Nombre","campo_valor"=>$nombre],
				["campo_nombre"=>"empresa_rif","campo_marcador"=>":Rif","campo_valor"=>$rif],
				["campo_nombre"=>"empresa_telefono","campo_marcador"=>":Telefono","campo_valor"=>$telefono],
				["campo_nombre"=>"empresa_emailKV","campo_marcador"=>":Email","campo_valor"=>$email], 
				["campo_nombre"=>"empresa_direccion","campo_marcador"=>":Direccion","campo_valor"=>$direccion]
			];

			/*== 1. PROCESANDO EL LOGO PRINCIPAL (MODO CLARO) ==*/
            $img_dir = "../views/img/";
            $cambio_logo = false;
            
            if(isset($_FILES['empresa_foto']) && $_FILES['empresa_foto']['name'] != "" && $_FILES['empresa_foto']['size'] > 0){
                
                if($_FILES['empresa_foto']['type']=="image/jpeg" || $_FILES['empresa_foto']['type']=="image/png" || $_FILES['empresa_foto']['type']=="image/jpg"){
                    if(($_FILES['empresa_foto']['size']/1024) <= 3072){
                        
                        $foto_nombre = "logo.png";
                        chmod($img_dir, 0777);

                        if(is_file($img_dir.$foto_nombre)){ chmod($img_dir.$foto_nombre, 0777); unlink($img_dir.$foto_nombre); }
                        
                        if(move_uploaded_file($_FILES['empresa_foto']['tmp_name'], $img_dir.$foto_nombre)){
                            $cambio_logo = true;
                            // Si NO activó el check de logo oscuro y NO existe un logo oscuro, usamos este como comodín para ambos
                            if(!isset($_POST['usar_logo_oscuro']) && !is_file($img_dir."logo_black.png")){
                                copy($img_dir.$foto_nombre, $img_dir."logo_black.png");
                            }
                        } else {
                            $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo subir el logo principal.","icono"=>"error"]; return json_encode($alerta); exit();
                        }
                    }else{
                        $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"El logo principal supera los 3MB","icono"=>"error"]; return json_encode($alerta); exit();
                    }
                }else{
                    $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Formato de logo principal no válido","icono"=>"error"]; return json_encode($alerta); exit();
                }
            }

            /*== 2. PROCESANDO EL LOGO SECUNDARIO (MODO OSCURO) ==*/
            if(isset($_POST['usar_logo_oscuro']) && $_POST['usar_logo_oscuro'] == "si"){
                
                if(isset($_FILES['empresa_foto_dark']) && $_FILES['empresa_foto_dark']['name'] != "" && $_FILES['empresa_foto_dark']['size'] > 0){
                    
                    if($_FILES['empresa_foto_dark']['type']=="image/jpeg" || $_FILES['empresa_foto_dark']['type']=="image/png" || $_FILES['empresa_foto_dark']['type']=="image/jpg"){
                        if(($_FILES['empresa_foto_dark']['size']/1024) <= 3072){
                            
                            $foto_oscura = "logo_black.png";
                            chmod($img_dir, 0777);
    
                            if(is_file($img_dir.$foto_oscura)){ chmod($img_dir.$foto_oscura, 0777); unlink($img_dir.$foto_oscura); }
                            
                            if(move_uploaded_file($_FILES['empresa_foto_dark']['tmp_name'], $img_dir.$foto_oscura)){
                                $cambio_logo = true;
                            } else {
                                $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"No se pudo subir el logo para el modo oscuro.","icono"=>"error"]; return json_encode($alerta); exit();
                            }
                        }else{
                            $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"El logo oscuro supera los 3MB","icono"=>"error"]; return json_encode($alerta); exit();
                        }
                    }else{
                        $alerta=["tipo"=>"simple","titulo"=>"Error","texto"=>"Formato de logo oscuro no válido","icono"=>"error"]; return json_encode($alerta); exit();
                    }
                }
            }

			$condicion=["condicion_campo"=>"empresa_id","condicion_marcador"=>":ID","condicion_valor"=>$id];

			if($this->actualizarDatos("empresa",$empresa_datos_up,$condicion)){
                # REGISTRO EN BITÁCORA #
                $detalles = ($cambio_logo) ? " (Incluyendo actualización de logotipos)" : "";
                $this->guardarBitacora("Sistema", "Actualización", "Se actualizaron los datos generales de la empresa: " . $nombre . $detalles);

				$alerta=["tipo"=>"recargar","titulo"=>"Empresa actualizada","texto"=>"Los datos de la empresa se actualizaron correctamente","icono"=>"success"];
			}else{
				$alerta=["tipo"=>"simple","titulo"=>"Ocurrió un error inesperado","texto"=>"No hemos podido actualizar los datos de la empresa, por favor intente nuevamente","icono"=>"error"];
			}
			return json_encode($alerta);
		}
	}