<?php
require_once "../../config/app.php";
require_once "../../autoload.php";
require_once "../views/inc/session_start.php";

use app\controllers\loginController;

if (isset($_POST['modulo_recuperacion'])) {

    $insLogin = new loginController();

    // VALIDAR RESPUESTA DE SEGURIDAD
    if ($_POST['modulo_recuperacion'] == "validar_respuesta") {
        $email = $insLogin->limpiarCadena($_POST['user_email']);
        
        // Capturamos, limpiamos espacios y pasamos a minúsculas
        $respuesta_usuario_limpia = strtolower(trim($insLogin->limpiarCadena($_POST['user_resp'])));
        $num = (int)$_POST['pregunta_num']; // 1, 2 o 3

        // Definimos las columnas según el número enviado desde el JS
        $columna_respuesta = "usuario_respuesta_" . $num;

        $check_user = $insLogin->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_email='$email' AND usuario_id != '1'");

        if ($check_user->rowCount() == 1) {
            $datos = $check_user->fetch();
            
            // Comparamos usando password_verify para leer el Hash de la base de datos
            if (password_verify($respuesta_usuario_limpia, $datos[$columna_respuesta])) {
                echo json_encode([
                    "error" => false,
                    "mensaje" => "Identidad confirmada"
                ]);
            } else {
                echo json_encode([
                    "error" => true,
                    "mensaje" => "La respuesta es incorrecta para la pregunta seleccionada."
                ]);
            }
        } else {
            echo json_encode(["error" => true, "mensaje" => "Usuario no encontrado."]);
        }
    }

    // ACTUALIZAR CONTRASEÑA
    if ($_POST['modulo_recuperacion'] == "cambiar_password") {
        $email = $insLogin->limpiarCadena($_POST['user_email']);
        $nueva_clave_cruda = $insLogin->limpiarCadena($_POST['nueva_clave']);

        // 1. Buscamos la contraseña actual en la base de datos
        $check_usuario = $insLogin->ejecutarConsulta("SELECT usuario_clave FROM usuario WHERE usuario_email='$email' AND usuario_id != '1'");
        
        if($check_usuario->rowCount() == 1){
            $datos_user = $check_usuario->fetch();

            // 2. Evitar que ponga la misma contraseña
            if(password_verify($nueva_clave_cruda, $datos_user['usuario_clave'])){
                echo json_encode([
                    "error" => true,
                    "mensaje" => "Por razones de seguridad, la nueva contraseña no puede ser exactamente igual a tu contraseña actual. Elige una diferente."
                ]);
                exit(); 
            }

            // 3. Si pasa la validación, procedemos a encriptar y actualizar
            $nueva_clave_hash = password_hash($nueva_clave_cruda, PASSWORD_BCRYPT, ["cost" => 10]);

            $actualizar = $insLogin->ejecutarConsulta("UPDATE usuario SET usuario_clave='$nueva_clave_hash' WHERE usuario_email='$email' AND usuario_id != '1'");

            if ($actualizar) {
                echo json_encode([
                    "error" => false, 
                    "mensaje" => "Tu contraseña ha sido actualizada con éxito."
                ]);
            } else {
                echo json_encode([
                    "error" => true, 
                    "mensaje" => "No se pudo actualizar la contraseña, intenta más tarde."
                ]);
            }
        } else {
            echo json_encode([
                "error" => true, 
                "mensaje" => "Error de validación de usuario. Intenta el proceso nuevamente."
            ]);
        }
    }
} else {
    session_destroy();
    header("Location: " . APP_URL . "login/");
}