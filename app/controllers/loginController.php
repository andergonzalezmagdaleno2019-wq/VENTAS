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
            // Solo se ejecuta si NO se envió recuperar_email
            if(isset($_POST['login_email']) && isset($_POST['login_clave'])){
                
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
                                $_SESSION['rol']=$check_usuario['rol_id']; 

                                /*== AUDITORIA INICIO SESION ==*/
                                $this->guardarBitacora("Seguridad", "Inicio de Sesión", "El usuario ".$check_usuario['usuario_usuario']." entró al sistema.");

                                /*== MARCAR PENDIENTES (Solo para Vendedores ID 2) ==*/
                                if($_SESSION['rol'] == 2 && ($check_usuario['usuario_pregunta_1'] == "" || is_null($check_usuario['usuario_pregunta_1']))){
                                    $_SESSION['seguridad_pendiente'] = true;
                                } else {
                                    $_SESSION['seguridad_pendiente'] = false;
                                }

                                /*== REDIRECCIÓN DIRECTA PARA TODOS ==*/
                                if(!headers_sent()){
                                    header("Location: ".APP_URL."dashboard/");
                                }
                                echo "<script> window.location.replace('".APP_URL."dashboard/'); </script>";
                                exit();

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