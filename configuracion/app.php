<?php
// =====================================================
// ⚙️ CONFIGURACIÓN PRINCIPAL DE LA APLICACIÓN
// =====================================================

// 🧭 Entorno de ejecución
define('APP_ENV', 'dev');

// 🌎 Zona horaria
date_default_timezone_set('America/Lima');

// ⚠️ Manejo de errores
if (APP_ENV === 'dev') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}

// 🌐 URL base del proyecto
define('BASE_URL', '/Sistema-Web-Clinica/publico');

// 📁 Rutas absolutas del sistema
define('RUTA_PUBLICO', __DIR__ . '/../publico');
define('RUTA_VISTAS',  __DIR__ . '/../Aplicacion/vistas');
define('RUTA_INCLUDES',__DIR__ . '/../includes');
define('RUTA_STORAGE', __DIR__ . '/../publico/archivos');

// =====================================================
// 📦 CARGAR CONFIGURACIONES ADICIONALES
// =====================================================

// 🗄️ Configuración de base de datos (PostgreSQL)
require_once __DIR__ . '/base_datos.php';

// 📧 Configuración de correo electrónico (Gmail/SMTP)
require_once __DIR__ . '/email.php';