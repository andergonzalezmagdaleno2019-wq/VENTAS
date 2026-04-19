<?php

    namespace app\controllers;
    use app\models\mainModel;

    class loginController extends mainModel{

        /*----------  Controlador iniciar sesion  ----------*/
        public function iniciarSesionControlador(){

    /*============= Verificación AJAX para recuperar contraseña =============*/
    if(isset($_POST['verificar_email_ajax']) && $_POST['verificar_email_ajax'] == "true"){
        
        // Limpiar cualquier salida previa
        while (ob_get_level()) ob_end_clean();
        
        // Establecer cabecera JSON
        header('Content-Type: application/json');
        
        // Verificar que llegó el email
        if(isset($_POST['recuperar_email_ajax']) && $_POST['recuperar_email_ajax'] != ""){
            
            $email = $this->limpiarCadena($_POST['recuperar_email_ajax']);
            
            // Verificar si existe en la base de datos
            $check_email = $this->ejecutarConsulta("SELECT usuario_id FROM usuario WHERE usuario_email='$email' AND usuario_id != '1'");

            if($check_email && $check_email->rowCount() == 1){
                $email_codificado = base64_encode($email);
                echo json_encode([
                    'existe' => true,
                    'mensaje' => 'Correo encontrado',
                    'redirect' => APP_URL."loginAnswer/".$email_codificado."/"
                ]);
            } else {
                echo json_encode([
                'existe' => false,
                'mensaje' => 'El correo electrónico ingresado no está registrado o no tiene permisos de recuperación.'
                ]);
            }
        } else {
            echo json_encode([
                'existe' => false,
                'mensaje' => 'No se recibió el correo electrónico'
            ]);
        }
        exit; 
    }

    /*---------- RECUPERACIÓN DE CUENTA (Paso 1) ----------*/
    if(isset($_POST['recuperar_email']) && $_POST['recuperar_email'] != ""){
        $email = $this->limpiarCadena($_POST['recuperar_email']);
        
        $check_email = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_email='$email' AND usuario_id != '1'");

        if($check_email->rowCount() == 1){
            $email_codificado = base64_encode($email);
            $url_final = APP_URL."loginAnswer/".$email_codificado."/";
        
            if(!headers_sent()){
                header("Location: ".$url_final);
            }
       
            echo "<script> window.location.replace('".$url_final."'); </script>";
            exit(); 
        } else {
            echo '<article class="message is-danger"><div class="message-body"><strong>Error:</strong> El correo ingresado no está registrado.</div></article>';
            return;
        }
    } 

           /*---------- LOGIN NORMAL ----------*/
            if(isset($_POST['login_email']) && isset($_POST['login_clave'])){
                
                $email=$this->limpiarCadena($_POST['login_email']);
                $clave = $_POST['login_clave'];
                $captcha = isset($_POST['login_captcha']) ? $this->limpiarCadena($_POST['login_captcha']) : "";

                /*== 1. VERIFICAR CORREO ==*/
                    if($email == "" || !filter_var($email, FILTER_VALIDATE_EMAIL)){
                        echo '<article class="message is-danger"><div class="message-body"><strong>Error:</strong> Ingrese un correo electrónico válido.</div></article>';
                        return;
                    }

                /*== 2. VERIFICAR CONTRASEÑA ==*/
                if($clave==""){
                    echo '<article class="message is-danger"><div class="message-body"><strong>Campo Requerido:</strong><br>Por favor, ingresa tu <strong>Clave (Contraseña)</strong> para continuar.</div></article>';
                    return;
                }

                // Validamos solo el largo (mínimo 7 caracteres) para permitir cualquier símbolo o letra
                if(strlen($clave) < 7 || strlen($clave) > 100){
                    echo '<article class="message is-danger"><div class="message-body"><strong>Formato de clave incorrecto</strong><br>La contraseña debe tener entre 7 y 100 caracteres.</div></article>';
                    return;
                }

                /*== 3. VERIFICAR CAPTCHA ==*/
                if($captcha==""){
                    echo '<article class="message is-danger"><div class="message-body"><strong>Campo Requerido:</strong><br>Por favor, resuelve el <strong>Captcha matemático</strong> para verificar que no eres un robot.</div></article>';
                    return;
                }
                if(!isset($_SESSION['captcha_resultado']) || $_SESSION['captcha_resultado'] != $captcha){
                    echo '<article class="message is-danger"><div class="message-body"><strong>Error de Seguridad</strong><br>La suma del captcha es incorrecta. Intenta de nuevo.</div></article>';
                    return; 
                }

                /*== 4. VERIFICAR EN BASE DE DATOS ==*/
                $check_usuario=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_email='$email'");

                if($check_usuario->rowCount()==1){
                    $check_usuario=$check_usuario->fetch();

                    /*== VALIDACIÓN: ESTADO DEL USUARIO ==*/
                    if($check_usuario['usuario_estado'] == "Inactivo" || $check_usuario['usuario_estado'] == "Inhabilitado" || $check_usuario['usuario_estado'] == "Bloqueado"){
                        
                        # REGISTRO EN BITÁCORA: Intento en cuenta restringida #
                        $this->guardarBitacora("Seguridad", "Seguridad", "Intento de acceso a cuenta restringida (".$check_usuario['usuario_estado']."): ".$email);

                        echo '<article class="message is-danger"><div class="message-body"><strong>Acceso Restringido</strong><br>Tu cuenta ha sido bloqueada o inhabilitada. Por favor, contacta al administrador del sistema.</div></article>';
                        return;
                    }

                    if(password_verify($clave, $check_usuario['usuario_clave'])){
                        

                        # (ÉXITO) RESETEAR INTENTOS FALLIDOS #
                        if(isset($_SESSION['intentos_fallidos'][$email])){
                            unset($_SESSION['intentos_fallidos'][$email]);
                        }

                        // VARIABLES DE SESIÓN 
                        $_SESSION['id']=$check_usuario['usuario_id'];
                        $_SESSION['nombre']=$check_usuario['usuario_nombre'];
                        $_SESSION['apellido']=$check_usuario['usuario_apellido'];
                        $_SESSION['usuario']=$check_usuario['usuario_usuario'];
                        $_SESSION['email']=$check_usuario['usuario_email'];
                        $_SESSION['foto']=$check_usuario['usuario_foto'];
                        $_SESSION['caja']=$check_usuario['caja_id'];
                        $_SESSION['rol']=$check_usuario['rol_id']; 

                        /*== MARCAR PENDIENTES DE SEGURIDAD ==*/
                        if($_SESSION['id'] != 1){
                            if(empty($check_usuario['usuario_pregunta_1']) || 
                            empty($check_usuario['usuario_pregunta_2']) || 
                            empty($check_usuario['usuario_pregunta_3'])){
                                
                                $_SESSION['seguridad_pendiente'] = true;
                            } else {
                                $_SESSION['seguridad_pendiente'] = false;
                            }
                        } else {
                            $_SESSION['seguridad_pendiente'] = false;
                        }

                        /*== REGISTRO EN BITÁCORA: Éxito ==*/
                        $this->guardarBitacora("Seguridad", "Seguridad", "El usuario ".$_SESSION['usuario']." inició sesión correctamente.");

                        /*== REDIRECCIÓN AL DASHBOARD ==*/
                        if(!headers_sent()){
                            header("Location: ".APP_URL."dashboard/");
                        }
                        echo "<script> window.location.replace('".APP_URL."dashboard/'); </script>";
                        exit();

                    }else{
                        # ====================================================================
                        # (FALLO) LÍMITE DE INTENTOS FALLIDOS
                        # ====================================================================
                        if($check_usuario['usuario_id'] != 1){ 
                            
                            if(!isset($_SESSION['intentos_fallidos'][$email])){
                                $_SESSION['intentos_fallidos'][$email] = 1;
                            } else {
                                $_SESSION['intentos_fallidos'][$email]++;
                            }

                            $intentos_restantes = 3 - $_SESSION['intentos_fallidos'][$email];

                            if($_SESSION['intentos_fallidos'][$email] >= 3){
                                $this->ejecutarConsulta("UPDATE usuario SET usuario_estado='Inactivo' WHERE usuario_id='".$check_usuario['usuario_id']."'");
                                
                                # REGISTRO EN BITÁCORA: Bloqueo de cuenta #
                                $this->guardarBitacora("Seguridad", "Seguridad", "Cuenta bloqueada por exceso de intentos fallidos: ".$email);

                                unset($_SESSION['intentos_fallidos'][$email]); 
                                
                                echo '<article class="message is-danger"><div class="message-body"><strong>¡Cuenta Bloqueada!</strong><br>Ha superado el límite de 3 intentos fallidos. Por seguridad, su cuenta ha sido bloqueada automáticamente. Contacte al Administrador.</div></article>';
                                return;
                            }else{
                                # REGISTRO EN BITÁCORA: Intento fallido #
                                $this->guardarBitacora("Seguridad", "Seguridad", "Contraseña incorrecta para el usuario: ".$email);

                                echo '<article class="message is-warning"><div class="message-body"><strong>Credenciales incorrectas</strong><br>La contraseña es incorrecta. Le quedan '.$intentos_restantes.' intento(s) antes de que la cuenta sea bloqueada.</div></article>';
                                return;
                            }
                        }else{
                            # REGISTRO EN BITÁCORA: Fallo admin #
                            $this->guardarBitacora("Seguridad", "Seguridad", "Intento de acceso fallido a cuenta Administrador.");

                            echo '<article class="message is-danger"><div class="message-body"><strong>Atención Administrador</strong><br>La CONTRASEÑA ingresada es incorrecta.</div></article>';
                            return;
                        }
                    }
                }else{
                    # REGISTRO EN BITÁCORA: Usuario inexistente #
                    $this->guardarBitacora("Seguridad", "Seguridad", "Intento de acceso con correo no registrado: ".$email);

                    echo '<article class="message is-warning"><div class="message-body"><strong>Atención</strong><br>El correo electrónico ingresado no existe en el sistema.</div></article>';
                }
            }
        }

        /*----------  Controlador cerrar sesion  ----------*/
        public function cerrarSesionControlador(){
            if(isset($_SESSION['usuario'])){
                # REGISTRO EN BITÁCORA: Cierre de sesión #
                $this->guardarBitacora("Seguridad", "Seguridad", "El usuario ".$_SESSION['usuario']." cerró su sesión.");
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

        /*----------  Controlador para verificar email via AJAX  ----------*/
        public function verificarEmailAjaxControlador(){
            // Limpiar buffer de salida
            if (ob_get_level()) ob_clean();
            header('Content-Type: application/json');
            
            if(isset($_POST['recuperar_email_ajax']) && $_POST['recuperar_email_ajax'] != ""){
                $email = $this->limpiarCadena($_POST['recuperar_email_ajax']);
                
                $check_email = $this->ejecutarConsulta("SELECT usuario_id FROM usuario WHERE usuario_email='$email'");

                if($check_email->rowCount() == 1){
                    // Email existe
                    $email_codificado = base64_encode($email);
                    echo json_encode([
                        'existe' => true,
                        'mensaje' => 'Correo encontrado',
                        'redirect' => APP_URL."loginAnswer/".$email_codificado."/"
                    ]);
                } else {
                    // Email no existe
                    echo json_encode([
                        'existe' => false,
                        'mensaje' => 'El correo electrónico ingresado no está registrado en nuestro sistema.'
                    ]);
                }
            } else {
                echo json_encode([
                    'existe' => false,
                    'mensaje' => 'No se recibió el correo electrónico'
                ]);
            }
            exit;
        }
    }
