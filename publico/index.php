<?php
// =====================
// 📌 Punto de entrada principal del sistema
// =====================

// 🔹 Configuración global
require_once __DIR__ . "/../configuracion/app.php";

// 🔹 Núcleo del sistema
require_once __DIR__ . "/../nucleo/Autenticacion.php";
require_once __DIR__ . "/../nucleo/Enrutador.php";

// 🔹 Controladores principales
require_once __DIR__ . "/../aplicacion/controladores/AuthControlador.php";

// =====================
// 🚦 Enrutamiento
// =====================

// Lee la acción desde la URL (?accion=login, ?accion=registro, etc.)
$accion = $_GET['accion'] ?? 'loginVista';

// Definir las rutas disponibles
$rutas = [
    // Auth
    "loginVista"    => [AuthControlador::class, "loginVista"],
    "login"         => [AuthControlador::class, "login"],
    "registroVista" => [AuthControlador::class, "registroVista"],
    "registro"      => [AuthControlador::class, "registro"],
    "logout"        => [AuthControlador::class, "logout"],

    // 🔹 Aquí luego añadiremos rutas de Usuarios, Doctores, Pacientes...
];

// Ejecutar el enrutador
Enrutador::resolver($accion, $rutas);
