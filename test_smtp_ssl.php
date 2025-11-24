<?php
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "<h2>🧪 Prueba Final - Contraseña Limpia</h2><hr>";

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = 'html';
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'tmldiego7@gmail.com';
    
    // ⚠️ CONTRASEÑA LIMPIA - SIN ESPACIOS
    $mail->Password   = trim('qplkyntmuwtrqwfx'); // trim() elimina espacios
    
    // Probar primero con TLS (587)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';
    
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    
    $mail->setFrom('tmldiego7@gmail.com', 'Test Final');
    $mail->addAddress('tmldiego7@gmail.com');
    
    $mail->isHTML(true);
    $mail->Subject = 'Test Final - ' . date('H:i:s');
    $mail->Body    = '<h1>✅ ¡FUNCIONA!</h1><p>Problema resuelto.</p>';
    
    $mail->send();
    
    echo "<div style='background:#d4edda;padding:20px;color:#155724;border-radius:8px;margin-top:20px;'>";
    echo "<h3>✅ ¡ÉXITO TOTAL!</h3>";
    echo "<p><strong>El correo se envió correctamente.</strong></p>";
    echo "<p>Revisa tu bandeja: <strong>tmldiego7@gmail.com</strong></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background:#f8d7da;padding:20px;color:#721c24;border-radius:8px;margin-top:20px;'>";
    echo "<h3>❌ ERROR</h3>";
    echo "<pre>" . htmlspecialchars($mail->ErrorInfo) . "</pre>";
    echo "</div>";
}
?>