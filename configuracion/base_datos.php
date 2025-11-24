<?php
// =====================================================
// 🗄️ CONFIGURACIÓN DE BASE DE DATOS - POSTGRESQL
// =====================================================

// Solo definir si no están definidas (evitar duplicados)
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}

if (!defined('DB_PORT')) {
    define('DB_PORT', '5432');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', 'sistema_clinica');
}

if (!defined('DB_USER')) {
    define('DB_USER', 'postgres');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', 'Nomeacuerdo2006');
}

if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8');
}

if (!defined('DB_TYPE')) {
    define('DB_TYPE', 'pgsql');
}