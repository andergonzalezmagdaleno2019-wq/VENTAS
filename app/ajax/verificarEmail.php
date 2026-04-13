<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../autoload.php';

use app\controllers\loginController;

// Limpiar cualquier salida previa
while (ob_get_level()) ob_end_clean();

// Establecer cabecera JSON
header('Content-Type: application/json');

// Verificar que llegaron los datos correctos
if(isset($_POST['recuperar_email_ajax']) && $_POST['recuperar_email_ajax'] != "" && isset($_POST['verificar_email_ajax']) && $_POST['verificar_email_ajax'] == "true") {
    
    $email = trim($_POST['recuperar_email_ajax']);
    
    // Instanciar controlador
    $insLogin = new loginController();
    
    // Limpiar email
    $email = $insLogin->limpiarCadena($email);
    
    /* CORRECCIÓN CRÍTICA: 
        Cambiamos SELECT usuario_id por SELECT * para obtener las preguntas de seguridad
    */
    $check_usuario = $insLogin->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_email='$email' AND usuario_id != '1'");

    if($check_usuario && $check_usuario->rowCount() == 1) {
        $datos = $check_usuario->fetch();

        /* VERIFICACIÓN DE PREGUNTAS:
            Si alguna pregunta está vacía, enviamos existe: false y el tipo seguridad
        */
        if(empty($datos['usuario_pregunta_1']) || empty($datos['usuario_pregunta_2']) || empty($datos['usuario_pregunta_3'])) {
            echo json_encode([
                'existe' => false,
                'tipo' => 'seguridad',
                'mensaje' => 'Todavía no puede recuperar su cuenta porque no tiene preguntas de seguridad registradas.'
            ]);
        } else {
            // Si tiene sus preguntas registradas, permitimos la redirección normal
            $email_codificado = base64_encode($email);
            echo json_encode([
                'existe' => true,
                'mensaje' => 'Correo encontrado',
                'redirect' => APP_URL . "recuperar.php?user=" . $email_codificado
            ]);
        }
    } else {
        // El correo no existe en la BD
        echo json_encode([
            'existe' => false,
            'tipo' => 'error',
            'mensaje' => 'El correo electrónico ingresado no está registrado en nuestro sistema.'
        ]);
    }
} else {
    echo json_encode([
        'existe' => false,
        'mensaje' => 'Solicitud inválida'
    ]);
}
exit;