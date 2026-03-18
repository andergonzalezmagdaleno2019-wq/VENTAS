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
    
    // Verificar en base de datos
    $check_email = $insLogin->ejecutarConsulta("SELECT usuario_id FROM usuario WHERE usuario_email='$email' AND usuario_id != '1'");

    if($check_email && $check_email->rowCount() == 1) {
        $email_codificado = base64_encode($email);
        echo json_encode([
            'existe' => true,
            'mensaje' => 'Correo encontrado',
            'redirect' => APP_URL . "recuperar.php?user=" . $email_codificado
        ]);
    } else {
        echo json_encode([
            'existe' => false,
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
?>