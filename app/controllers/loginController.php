<?php

    namespace app\controllers;
    use app\models\mainModel;

    class loginController extends mainModel{

        /*----------  Controlador iniciar sesion  ----------*/
        public function iniciarSesionControlador(){

            $email=$this->limpiarCadena($_POST['login_email']);
            $clave=$this->limpiarCadena($_POST['login_clave']);
            
            $captcha = isset($_POST['login_captcha']) ? $this->limpiarCadena($_POST['login_captcha']) : "";

            if($email=="" || $clave=="" || $captcha==""){
                echo '<article class="message is-danger"><div class="message-body"><strong>Ocurrió un error inesperado</strong><br>No has llenado todos los campos que son obligatorios</div></article>';
            }else{

                if(!isset($_SESSION['captcha_resultado']) || $_SESSION['captcha_resultado'] != $captcha){
                    echo '<article class="message is-danger"><div class="message-body"><strong>Error de Seguridad</strong><br>La suma del captcha es incorrecta. Intenta de nuevo.</div></article>';
                    return; 
                }

                if($this->verificarDatos("[a-zA-Z0-9@.-]{7,100}",$email)){
                    echo '<article class="message is-danger">
                            <div class="message-body">
                                <strong>Formato de correo incorrecto</strong><br>
                                El correo electrónico no es válido. Asegúrese de que cumple con lo siguiente:<br>
                                • Tener entre <strong>7 y 100 caracteres</strong>.<br>
                                • Solo se permiten letras, números y los símbolos <strong>@ . -</strong><br>
                                <em>(No se permiten espacios en blanco ni caracteres especiales como #, !, %, etc.)</em>
                            </div>
                          </article>';
                }else{
                    if($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$clave)){
                        echo '<article class="message is-danger">
                                <div class="message-body">
                                    <strong>Formato de clave incorrecto</strong><br>
                                    La contraseña no cumple con los requisitos de seguridad. Debe tener:<br>
                                    • Entre <strong>7 y 100 caracteres</strong>.<br>
                                    • Solo se permiten letras, números y los símbolos especiales <strong>$ @ . -</strong>
                                </div>
                              </article>';
                    }else{
                        // Buscamos por correo 
                        $check_usuario=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_email='$email'");

                        if($check_usuario->rowCount()==1){
                            $check_usuario=$check_usuario->fetch();

                            /*== VALIDACIÓN: ESTADO DEL USUARIO ==*/
                            if($check_usuario['usuario_estado'] == "Inactivo" || $check_usuario['usuario_estado'] == "Inhabilitado" || $check_usuario['usuario_estado'] == "Bloqueado"){
                                echo '<article class="message is-danger"><div class="message-body"><strong>Acceso Restringido</strong><br>Tu cuenta ha sido bloqueada o inhabilitada. Por favor, contacta al administrador del sistema.</div></article>';
                                return;
                            }

                            if(password_verify($clave,$check_usuario['usuario_clave'])){

                                # (ÉXITO) RESETEAR INTENTOS FALLIDOS #
                                if(isset($_SESSION['intentos_fallidos'][$email])){
                                    unset($_SESSION['intentos_fallidos'][$email]);
                                }

                                // VARIABLES DE SESIÓN (¡AQUÍ ESTABA EL ERROR DEL ROL!)
                                $_SESSION['id']=$check_usuario['usuario_id'];
                                $_SESSION['nombre']=$check_usuario['usuario_nombre'];
                                $_SESSION['apellido']=$check_usuario['usuario_apellido'];
                                $_SESSION['usuario']=$check_usuario['usuario_usuario'];
                                $_SESSION['email']=$check_usuario['usuario_email'];
                                $_SESSION['foto']=$check_usuario['usuario_foto'];
                                $_SESSION['caja']=$check_usuario['caja_id'];
                                $_SESSION['rol']=$check_usuario['rol_id']; // <--- LA SOLUCIÓN

                                /*== AUDITORIA INICIO SESION ==*/
                                $this->guardarBitacora("Seguridad", "Inicio de Sesión", "El usuario ".$check_usuario['usuario_usuario']." entró al sistema.");

                                if(headers_sent()){
                                    echo "<script> window.location.href='".APP_URL."dashboard/'; </script>";
                                }else{
                                    header("Location: ".APP_URL."dashboard/");
                                }

                            }else{
                                # ====================================================================
                                # (FALLO) SISTEMA DE SEGURIDAD: LÍMITE DE INTENTOS FALLIDOS
                                # ====================================================================
                                if($check_usuario['usuario_id'] != 1){ // Si NO es el administrador (id=1)
                                    
                                    if(!isset($_SESSION['intentos_fallidos'][$email])){
                                        $_SESSION['intentos_fallidos'][$email] = 1;
                                    } else {
                                        $_SESSION['intentos_fallidos'][$email]++;
                                    }

                                    $intentos_restantes = 3 - $_SESSION['intentos_fallidos'][$email];

                                    if($_SESSION['intentos_fallidos'][$email] >= 3){
                                        // Bloqueo directo en la base de datos
                                        $this->ejecutarConsulta("UPDATE usuario SET usuario_estado='Inactivo' WHERE usuario_id='".$check_usuario['usuario_id']."'");
                                        unset($_SESSION['intentos_fallidos'][$email]); 
                                        
                                        echo '<article class="message is-danger"><div class="message-body"><strong>¡Cuenta Bloqueada!</strong><br>Ha superado el límite de 3 intentos fallidos. Por seguridad, su cuenta ha sido bloqueada automáticamente. Contacte al Administrador.</div></article>';
                                        return;
                                    }else{
                                        echo '<article class="message is-warning"><div class="message-body"><strong>Credenciales incorrectas</strong><br>La contraseña es incorrecta. Le quedan '.$intentos_restantes.' intento(s) antes de que la cuenta sea bloqueada.</div></article>';
                                        return;
                                    }
                                }else{
                                    // El Admin falla, pero es INMUNE al bloqueo
                                    echo '<article class="message is-danger"><div class="message-body"><strong>Atención Administrador</strong><br>La CONTRASEÑA ingresada es incorrecta.</div></article>';
                                    return;
                                }
                                # ====================================================================
                            }
                        }else{
                            echo '<article class="message is-warning"><div class="message-body"><strong>Atención</strong><br>El correo electrónico ingresado no existe en el sistema.</div></article>';
                        }
                    }
                }
            }
        }
        /*----------  Controlador cerrar sesion  ----------*/
        public function cerrarSesionControlador(){
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

        /*----------  Controlador para Dashboard  ----------*/
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