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
require_once __DIR__ . "/../aplicacion/controladores/AdminControlador.php";

// =====================
// 🚦 Enrutamiento
// =====================

// 🔍 Detecta acción desde la URL (?accion=login, ?accion=dashboardAdmin, etc.)
$accion = $_GET['accion'] ?? (Autenticacion::usuarioId() ? 'dashboardAdmin' : 'loginVista');

// 📦 Mapa de rutas disponibles
$rutas = [
    // 🔐 Autenticación
    "loginVista"    => [AuthControlador::class, "loginVista"],
    "login"         => [AuthControlador::class, "login"],
    "registroVista" => [AuthControlador::class, "registroVista"],
    "registro"      => [AuthControlador::class, "registro"],
    "logout"        => [AuthControlador::class, "logout"],

    // 🛠 Panel Administrativo
    "dashboardAdmin"      => [AdminControlador::class, "dashboard"],
    "gestionarUsuarios"   => [AdminControlador::class, "gestionarUsuarios"],
    "gestionarDoctores"   => [AdminControlador::class, "gestionarDoctores"],
    "crearDoctor"   => [AdminControlador::class, "crearDoctor"],
    "guardarDoctor" => [AdminControlador::class, "guardarDoctor"],
    "editarDoctor"     => [AdminControlador::class, "editarDoctor"],
    "actualizarDoctor" => [AdminControlador::class, "actualizarDoctor"],
    "eliminarDoctor"   => [AdminControlador::class, "eliminarDoctor"],
    "gestionarPacientes"  => [AdminControlador::class, "gestionarPacientes"],
    "gestionarFacturas"   => [AdminControlador::class, "gestionarFacturas"],
    "verReportes"         => [AdminControlador::class, "verReportes"],
    "configuracion"       => [AdminControlador::class, "configuracion"],

    // 🔜 Aquí luego añadiremos rutas de Doctor y Paciente
];

// 🚀 Ejecuta el enrutador
Enrutador::resolver($accion, $rutas);
