<?php
// 🧭 Define el entorno de ejecución: 'dev' para desarrollo, 'prod' para producción
define('APP_ENV', 'dev');

// 🌎 Establece la zona horaria del sistema (Perú)
date_default_timezone_set('America/Lima');

// ⚠️ Configura el manejo de errores según el entorno
if (APP_ENV === 'dev') {
    // En desarrollo: muestra todos los errores
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    // En producción: oculta errores y evita mostrar avisos o funciones obsoletas
    ini_set('display_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}

// 🌐 Define la URL base del sistema (ajustar si está en una subcarpeta)
define('BASE_URL', '/Clinica/publico');

// 📁 Define rutas absolutas para acceder a carpetas clave del proyecto
define('RUTA_PUBLICO', __DIR__ . '/../publico');           // Archivos públicos (CSS, JS, imágenes)
define('RUTA_VISTAS',  __DIR__ . '/../aplicacion/vistas'); // Vistas HTML/PHP del sistema
define('RUTA_INCLUDES',__DIR__ . '/../includes');          // Archivos compartidos (header, footer, etc.)
define('RUTA_STORAGE', __DIR__ . '/../publico/archivos');  // Carpeta para subir y guardar archivos
