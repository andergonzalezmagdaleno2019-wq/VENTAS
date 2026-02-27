<?php

	namespace app\controllers;
	use app\models\mainModel;

	class loginController extends mainModel{

		/*----------  Controlador iniciar sesion  ----------*/
		public function iniciarSesionControlador(){

			$usuario=$this->limpiarCadena($_POST['login_usuario']);
		    $clave=$this->limpiarCadena($_POST['login_clave']);
            
            $captcha = isset($_POST['login_captcha']) ? $this->limpiarCadena($_POST['login_captcha']) : "";

		    if($usuario=="" || $clave=="" || $captcha==""){
				echo '<article class="message is-danger">
				  <div class="message-body">
				    <strong>Ocurrió un error inesperado</strong><br>
				    No has llenado todos los campos que son obligatorios
				  </div>
				</article>';
		    }else{

                if(!isset($_SESSION['captcha_resultado']) || $_SESSION['captcha_resultado'] != $captcha){
                    echo '<article class="message is-danger">
                      <div class="message-body">
                        <strong>Error de Seguridad</strong><br>
                        La suma del captcha es incorrecta. Intenta de nuevo.
                      </div>
                    </article>';
                    return; 
                }

			    if($this->verificarDatos("[a-zA-Z0-9]{4,20}",$usuario)){
					echo '<article class="message is-danger">
					  <div class="message-body">
					    <strong>Ocurrió un error inesperado</strong><br>
					    El USUARIO no coincide con el formato solicitado
					  </div>
					</article>';
			    }else{
				    if($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$clave)){
						echo '<article class="message is-danger">
						  <div class="message-body">
						    <strong>Ocurrió un error inesperado</strong><br>
						    La CLAVE no coincide con el formato solicitado
						  </div>
						</article>';
				    }else{
					    $check_usuario=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_usuario='$usuario'");

					    if($check_usuario->rowCount()==1){
					    	$check_usuario=$check_usuario->fetch();

					    	if($check_usuario['usuario_usuario']==$usuario && password_verify($clave,$check_usuario['usuario_clave'])){

					    		$_SESSION['id']=$check_usuario['usuario_id'];
					            $_SESSION['nombre']=$check_usuario['usuario_nombre'];
					            $_SESSION['apellido']=$check_usuario['usuario_apellido'];
					            $_SESSION['usuario']=$check_usuario['usuario_usuario'];
					            $_SESSION['foto']=$check_usuario['usuario_foto'];
					            $_SESSION['caja']=$check_usuario['caja_id'];
								$_SESSION['rol']=$check_usuario['rol_id'];

                                /*== AUDITORIA INICIO SESION ==*/
                                $this->guardarBitacora("Seguridad", "Inicio de Sesión", "El usuario ".$usuario." entró al sistema.");

					            if(headers_sent()){
					                echo "<script> window.location.href='".APP_URL."dashboard/'; </script>";
					            }else{
					                header("Location: ".APP_URL."dashboard/");
					            }

					    	}else{
					    		echo '<article class="message is-warning">
								  <div class="message-body">
								    <strong>Atención</strong><br>
								    La CONTRASEÑA ingresada es incorrecta.
								  </div>
								</article>';
					    	}
					    }else{
							echo '<article class="message is-warning">
							  <div class="message-body">
							    <strong>Atención</strong><br>
							    El USUARIO ingresado no existe en el sistema.
							  </div>
							</article>';
					    }
				    }
			    }
		    }
		}

		/*----------  Controlador cerrar sesion  ----------*/
		public function cerrarSesionControlador(){
            /*== AUDITORIA CIERRE SESION ==*/
            if(isset($_SESSION['usuario'])){
                $this->guardarBitacora("Seguridad", "Cierre de Sesión", "El usuario ".$_SESSION['usuario']." salió del sistema.");
            }
			session_destroy();
		    if(headers_sent()){
                echo "<script> window.location.href='".APP_URL."login/'; </script>";
            }else{
                header("Location: ".APP_URL."login/");
            }
		}

        /*----------  Controlador para Dashboard (Alertas de Stock) ----------*/
        public function obtenerAlertasDashboard($tipo){
            if($tipo=="bajo"){
                $consulta = "SELECT producto_nombre, producto_stock, producto_stock_min FROM producto WHERE producto_stock <= producto_stock_min";
            } elseif($tipo=="alto"){
                $consulta = "SELECT producto_nombre, producto_stock, producto_stock_max FROM producto WHERE producto_stock >= producto_stock_max";
            } else {
                return null;
            }
            return $this->ejecutarConsulta($consulta);
        }
	}