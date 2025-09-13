<?php
// Entorno: 'dev' o 'prod'
define('APP_ENV', 'dev');

// Zona horaria (Perú)
date_default_timezone_set('America/Lima');

// Manejo de errores según entorno
if (APP_ENV === 'dev') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}

// URL base (ajusta si usas subcarpeta, p.ej. '/sistema-clinica/publico')
define('BASE_URL', '/Clinica/publico');

// Rutas útiles
define('RUTA_PUBLICO', __DIR__ . '/../publico');
define('RUTA_VISTAS',  __DIR__ . '/../aplicacion/vistas');
define('RUTA_INCLUDES',__DIR__ . '/../includes');
define('RUTA_STORAGE', __DIR__ . '/../publico/archivos');
