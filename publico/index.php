<?php
// =====================
// 📌 Punto de entrada principal del sistema
// =====================

// 🔹 Configuración global
require_once __DIR__ . "/../configuracion/app.php";

// 🔹 Núcleo del sistema
require_once __DIR__ . "/../nucleo/Autenticacion.php";
require_once __DIR__ . "/../nucleo/Enrutador.php";
require_once __DIR__ . "/../nucleo/BaseDatos.php";

// ✅ RUTA CORREGIDA: vendor/ está un nivel arriba de publico/
require_once __DIR__ . '/../vendor/autoload.php';

// 🔹 Controladores principales
require_once __DIR__ . "/../Aplicacion/controladores/AuthControlador.php";
require_once __DIR__ . "/../Aplicacion/controladores/AdminControlador.php";
require_once __DIR__ . "/../Aplicacion/controladores/DoctorControlador.php";
require_once __DIR__ . "/../Aplicacion/controladores/PacienteControlador.php";
require_once __DIR__ . "/../Aplicacion/controladores/UsuarioControlador.php";
require_once __DIR__ . "/../Aplicacion/controladores/CitaControlador.php";
require_once __DIR__ . "/../Aplicacion/controladores/FacturaControlador.php";
require_once __DIR__ . "/../Aplicacion/controladores/HistoriaControlador.php";
require_once __DIR__ . "/../Aplicacion/controladores/RecetaControlador.php";
require_once __DIR__ . "/../Aplicacion/controladores/ReporteControlador.php";
require_once __DIR__ . "/../Aplicacion/controladores/ConfiguracionControlador.php";

// =====================
// 🚦 Enrutamiento
// =====================

// 🔍 Detecta acción desde la URL (?accion=login, ?accion=dashboardAdmin, etc.)
$accion = $_GET['accion'] ?? (Autenticacion::usuarioId() ? 'dashboardAdmin' : 'loginVista');

// 📦 Mapa de rutas disponibles
$rutas = [
    // ========================================
    // 🔐 AUTENTICACIÓN
    // ========================================
    "loginVista"    => [AuthControlador::class, "loginVista"],
    "login"         => [AuthControlador::class, "login"],
    "registroVista" => [AuthControlador::class, "registroVista"],
    "registro"      => [AuthControlador::class, "registro"],
    "logout"        => [AuthControlador::class, "logout"],

    // ========================================
    // 👨‍💼 ADMINISTRADOR - Dashboard y Generales
    // ========================================
    "dashboardAdmin"     => [AdminControlador::class, "dashboard"],
    "adminDashboard"     => [AdminControlador::class, "dashboard"],
    "verReportes"        => [AdminControlador::class, "verReportes"],
    "adminReportes"      => [AdminControlador::class, "verReportes"],

    // ========================================
    // ⚙️ CONFIGURACIÓN DEL SISTEMA (Solo Admin)
    // ========================================
    "configuracion"              => [ConfiguracionControlador::class, "index"],
    "adminConfiguracion"         => [ConfiguracionControlador::class, "index"],
    "guardarConfiguracion"       => [ConfiguracionControlador::class, "guardarConfiguracion"],
    "guardarNotificaciones"      => [ConfiguracionControlador::class, "guardarNotificaciones"],
    "guardarSeguridadConfig"     => [ConfiguracionControlador::class, "guardarSeguridadConfig"],
    "restaurarPorDefecto"        => [ConfiguracionControlador::class, "restaurarPorDefecto"],
    "exportarConfiguraciones"    => [ConfiguracionControlador::class, "exportarConfiguraciones"],
    "exportarConfiguracionesPDF" => [ConfiguracionControlador::class, "exportarConfiguracionesPDF"],
    "verAuditoria"               => [ConfiguracionControlador::class, "verAuditoria"],
    "limpiarAuditoria"           => [ConfiguracionControlador::class, "limpiarAuditoria"],

    // ========================================
    // 🩺 DOCTORES - Vistas del rol doctor
    // ========================================
    "doctorDashboard" => [DoctorControlador::class, "dashboard"],
    "doctorPerfil"    => [DoctorControlador::class, "perfil"],
    "doctorCitas"     => [DoctorControlador::class, "misCitas"],
    "doctorHistorias" => [DoctorControlador::class, "misHistorias"],
    "doctorRecetas"   => [DoctorControlador::class, "misRecetas"],

    // 🩺 DOCTORES - Gestión desde admin
    "gestionarDoctores" => [DoctorControlador::class, "gestionarDoctores"],
    "crearDoctor"       => [DoctorControlador::class, "crearDoctor"],
    "guardarDoctor"     => [DoctorControlador::class, "guardarDoctor"],
    "editarDoctor"      => [DoctorControlador::class, "editarDoctor"],
    "actualizarDoctor"  => [DoctorControlador::class, "actualizarDoctor"],
    "eliminarDoctor"    => [DoctorControlador::class, "eliminarDoctor"],

    // ========================================
    // 🧑‍🤝‍🧑 PACIENTES - Vistas del rol paciente
    // ========================================
    "pacienteDashboard" => [PacienteControlador::class, "dashboard"],
    "pacientePerfil"    => [PacienteControlador::class, "perfil"],
    "pacienteCitas"     => [PacienteControlador::class, "misCitas"],
    "pacienteHistorial" => [PacienteControlador::class, "miHistorial"],
    "pacienteFacturas"  => [PacienteControlador::class, "misFacturas"],

    // 🧑‍🤝‍🧑 PACIENTES - Gestión desde admin
    "gestionarPacientes" => [PacienteControlador::class, "gestionarPacientes"],
    "crearPaciente"      => [PacienteControlador::class, "crearPaciente"],
    "guardarPaciente"    => [PacienteControlador::class, "guardarPaciente"],
    "editarPaciente"     => [PacienteControlador::class, "editarPaciente"],
    "actualizarPaciente" => [PacienteControlador::class, "actualizarPaciente"],
    "eliminarPaciente"   => [PacienteControlador::class, "eliminarPaciente"],

    // ========================================
    // 👥 USUARIOS - Gestión genérica
    // ========================================
    "gestionarUsuarios"  => [UsuarioControlador::class, "gestionarUsuarios"],
    "editarUsuario"      => [UsuarioControlador::class, "editarUsuario"],
    "actualizarUsuario"  => [UsuarioControlador::class, "actualizarUsuario"],
    "eliminarUsuario"    => [UsuarioControlador::class, "eliminarUsuario"],
    "cambiarRol"         => [UsuarioControlador::class, "cambiarRol"],

    // 👤 Perfil propio (cualquier usuario)
    "miPerfil"           => [UsuarioControlador::class, "miPerfil"],
    "actualizarMiPerfil" => [UsuarioControlador::class, "actualizarMiPerfil"],
    "cambiarPassword"    => [UsuarioControlador::class, "cambiarPassword"],

    // ========================================
    // 📅 CITAS
    // ========================================
    "gestionarCitas" => [CitaControlador::class, "gestionar"],
    "crearCita"      => [CitaControlador::class, "crear"],
    "guardarCita"    => [CitaControlador::class, "guardar"],
    "editarCita"     => [CitaControlador::class, "editar"],
    "actualizarCita" => [CitaControlador::class, "actualizar"],
    "cancelarCita"   => [CitaControlador::class, "cancelar"],
    "confirmarCita"  => [CitaControlador::class, "confirmar"],

    // ========================================
    // 💳 FACTURAS
    // ========================================
    "gestionarFacturas" => [FacturaControlador::class, "gestionarFacturas"],
    "crearFactura"      => [FacturaControlador::class, "crear"],
    "guardarFactura"    => [FacturaControlador::class, "guardar"],
    "verFactura"        => [FacturaControlador::class, "ver"],
    "descargarFactura"  => [FacturaControlador::class, "descargar"],

    // ========================================
    // 📋 HISTORIAS CLÍNICAS
    // ========================================
    "gestionarHistorias" => [HistoriaControlador::class, "gestionar"],
    "crearHistoria"      => [HistoriaControlador::class, "crear"],
    "guardarHistoria"    => [HistoriaControlador::class, "guardar"],
    "verHistoria"        => [HistoriaControlador::class, "ver"],
    "editarHistoria"     => [HistoriaControlador::class, "editar"],
    "actualizarHistoria" => [HistoriaControlador::class, "actualizar"],

    // ========================================
    // 📊 REPORTES
    // ========================================
    "reportes"        => [ReporteControlador::class, "index"],
    "exportarPDF"     => [ReporteControlador::class, "exportarPDF"],
    "exportarExcel"   => [ReporteControlador::class, "exportarExcel"],

    // ========================================
    // 💊 RECETAS
    // ========================================
    "gestionarRecetas" => [RecetaControlador::class, "gestionar"],
    "crearReceta"      => [RecetaControlador::class, "crear"],
    "guardarReceta"    => [RecetaControlador::class, "guardar"],
    "verReceta"        => [RecetaControlador::class, "ver"],
    "imprimirReceta"   => [RecetaControlador::class, "imprimir"],
];

// 🚀 Ejecuta el enrutador
Enrutador::resolver($accion, $rutas);