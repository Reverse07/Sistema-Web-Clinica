<?php
// =====================================================
// 📧 CONFIGURACIÓN DE CORREO ELECTRÓNICO - GMAIL
// =====================================================

if (!defined('MAIL_HOST')) {
    define('MAIL_HOST', 'smtp.gmail.com');
}

if (!defined('MAIL_PORT')) {
    define('MAIL_PORT', 587);
}

if (!defined('MAIL_ENCRYPTION')) {
    define('MAIL_ENCRYPTION', 'tls');
}

if (!defined('MAIL_USERNAME')) {
    define('MAIL_USERNAME', 'tmldiego7@gmail.com');
}

if (!defined('MAIL_PASSWORD')) {
    // ⚠️ USAR trim() para eliminar espacios invisibles
    define('MAIL_PASSWORD', trim('qplkyntmuwtrqwfx'));
}

if (!defined('MAIL_FROM_ADDRESS')) {
    define('MAIL_FROM_ADDRESS', 'tmldiego7@gmail.com');
}

if (!defined('MAIL_FROM_NAME')) {
    define('MAIL_FROM_NAME', 'Sistema Clínico');
}

if (!defined('CONTACTO_EMAIL')) {
    define('CONTACTO_EMAIL', 'tmldiego7@gmail.com');
}

if (!defined('MAIL_DEBUG')) {
    define('MAIL_DEBUG', false); // Cambiar a false en producción
}
?>