<?php
// =====================================================
// 📌 PUNTO DE ENTRADA PRINCIPAL DEL SISTEMA
// Sistema de Gestión de Clínica Médica
// =====================================================

session_start();

// =====================================================
// 🔹 CONFIGURACIÓN Y NÚCLEO
// =====================================================
require_once __DIR__ . "/../configuracion/app.php";
require_once __DIR__ . "/../nucleo/Autenticacion.php";
require_once __DIR__ . "/../nucleo/Enrutador.php";
require_once __DIR__ . "/../nucleo/BaseDatos.php";

// ✅ Composer Autoload (TCPDF, PHPMailer, etc.)
require_once __DIR__ . '/../vendor/autoload.php';

// =====================================================
// 🔹 CONTROLADORES
// =====================================================
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

// =====================================================
// 🚦 ENRUTAMIENTO
// =====================================================

// Detectar acción desde URL
$accion = $_GET['accion'] ?? (Autenticacion::usuarioId() ? 'dashboardAdmin' : 'loginVista');

// Mapa de rutas del sistema
$rutas = [

    // ========================================
    // 🔐 AUTENTICACIÓN
    // ========================================
    "loginVista"    => [AuthControlador::class, "loginVista"],
    "login"         => [AuthControlador::class, "login"],
    "registroVista" => [AuthControlador::class, "registroVista"],
    "registro"      => [AuthControlador::class, "registro"],
    "logout"        => [AuthControlador::class, "logout"],
    "terminos"      => [AuthControlador::class, "terminos"],
    "privacidad"    => [AuthControlador::class, "privacidad"],

    // ========================================
    // 👨‍💼 ADMINISTRADOR
    // ========================================
    "dashboardAdmin"             => [AdminControlador::class, "dashboard"],
    "adminDashboard"             => [AdminControlador::class, "dashboard"],
    "verReportes"                => [ReporteControlador::class, "index"],
    "adminReportes"              => [ReporteControlador::class, "index"],
    "contacto"                   => [AdminControlador::class, "contacto"],
    "enviarContacto"             => [AdminControlador::class, "enviarContacto"],

    // ⚙️ Configuración del Sistema
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
    "configuracionPaciente"         => [PacienteControlador::class, "configuracion"],
    "guardarNotificacionesPaciente" => [PacienteControlador::class, "guardarNotificacionesPaciente"],
    "guardarPrivacidadPaciente"     => [PacienteControlador::class, "guardarPrivacidadPaciente"],

    // ========================================
    // 🩺 DOCTORES
    // ========================================
    // Vistas del rol doctor
    "doctorDashboard"            => [DoctorControlador::class, "dashboard"],
    "doctorPerfil"               => [DoctorControlador::class, "perfil"],
    "doctorCitas"                => [DoctorControlador::class, "misCitas"],
    "doctorHistorias"            => [DoctorControlador::class, "misHistorias"],
    "doctorRecetas"              => [DoctorControlador::class, "misRecetas"],

    // Gestión desde admin
    "gestionarDoctores"          => [DoctorControlador::class, "gestionarDoctores"],
    "crearDoctor"                => [DoctorControlador::class, "crearDoctor"],
    "guardarDoctor"              => [DoctorControlador::class, "guardarDoctor"],
    "editarDoctor"               => [DoctorControlador::class, "editarDoctor"],
    "actualizarDoctor"           => [DoctorControlador::class, "actualizarDoctor"],
    "eliminarDoctor"             => [DoctorControlador::class, "eliminarDoctor"],

    // ========================================
    // 🧑‍🤝‍🧑 PACIENTES
    // ========================================
    // Vistas del rol paciente
    "pacienteDashboard"          => [PacienteControlador::class, "dashboard"],
    "dashboardPaciente"          => [PacienteControlador::class, "dashboard"],
    "pacientePerfil"             => [PacienteControlador::class, "perfil"],
    "perfilPaciente"             => [PacienteControlador::class, "perfil"],
    "pacienteCitas"              => [PacienteControlador::class, "misCitas"],
    "misCitas"                   => [PacienteControlador::class, "misCitas"],
    "pacienteHistorial"          => [PacienteControlador::class, "miHistorial"],
    "miHistorial"                => [PacienteControlador::class, "miHistorial"],
    "historialMedico"            => [PacienteControlador::class, "miHistorial"],
    "verHistoriaPaciente"        => [PacienteControlador::class, "verHistoria"],
    "descargarHistorial"         => [PacienteControlador::class, "descargarHistorial"],
    "imprimirHistoria"           => [PacienteControlador::class, "imprimirHistoria"],
    "pacienteFacturas"           => [PacienteControlador::class, "misFacturas"],
    "misFacturas"                => [PacienteControlador::class, "misFacturas"],
    "crearCitaPaciente"          => [CitaControlador::class, "crearCitaPaciente"],
    "guardarCitaPaciente"        => [CitaControlador::class, "guardarCitaPaciente"],

    // Gestión desde admin
    "gestionarPacientes"         => [PacienteControlador::class, "gestionarPacientes"],
    "crearPaciente"              => [PacienteControlador::class, "crearPaciente"],
    "guardarPaciente"            => [PacienteControlador::class, "guardarPaciente"],
    "editarPaciente"             => [PacienteControlador::class, "editarPaciente"],
    "actualizarPaciente"         => [PacienteControlador::class, "actualizarPaciente"],
    "eliminarPaciente"           => [PacienteControlador::class, "eliminarPaciente"],

    // ========================================
    // 👥 USUARIOS
    // ========================================
    "gestionarUsuarios"          => [UsuarioControlador::class, "gestionarUsuarios"],
    "editarUsuario"              => [UsuarioControlador::class, "editarUsuario"],
    "actualizarUsuario"          => [UsuarioControlador::class, "actualizarUsuario"],
    "eliminarUsuario"            => [UsuarioControlador::class, "eliminarUsuario"],
    "cambiarRol"                 => [UsuarioControlador::class, "cambiarRol"],
    "miPerfil"                   => [UsuarioControlador::class, "miPerfil"],
    "actualizarMiPerfil"         => [UsuarioControlador::class, "actualizarMiPerfil"],
    "cambiarPassword"            => [UsuarioControlador::class, "cambiarPassword"],

    // ========================================
    // 📅 CITAS
    // ========================================
    // Gestión general
    "gestionarCitas"             => [CitaControlador::class, "gestionar"],
    "citas"                      => [CitaControlador::class, "gestionar"],
    "crearCita"                  => [CitaControlador::class, "crear"],
    "guardarCita"                => [CitaControlador::class, "guardar"],
    "editarCita"                 => [CitaControlador::class, "editar"],
    "actualizarCita"             => [CitaControlador::class, "actualizar"],
    "verDetalleCita"             => [CitaControlador::class, "verDetalle"],
    "confirmarCita"              => [CitaControlador::class, "confirmar"],
    "cancelarCita"               => [CitaControlador::class, "cancelar"],
    "eliminarCita"               => [CitaControlador::class, "eliminarCita"],
    "reprogramarCita"            => [CitaControlador::class, "reprogramarCita"],
    "guardarReprogramacion"      => [CitaControlador::class, "guardarReprogramacion"],
    "obtenerHorariosDisponibles" => [CitaControlador::class, "obtenerHorariosDisponibles"],

    // ========================================
    // 💳 FACTURAS
    // ========================================
    "gestionarFacturas"          => [FacturaControlador::class, "gestionarFacturas"],
    "crearFactura"               => [FacturaControlador::class, "crear"],
    "guardarFactura"             => [FacturaControlador::class, "guardar"],
    "verFactura"                 => [FacturaControlador::class, "ver"],
    "editarFactura"              => [FacturaControlador::class, "editarFactura"],
    "actualizarFactura"          => [FacturaControlador::class, "actualizarFactura"],
    "eliminarFactura"            => [FacturaControlador::class, "eliminarFactura"],
    "descargarFactura"           => [FacturaControlador::class, "descargar"],

    // ========================================
    // 📋 HISTORIAS CLÍNICAS
    // ========================================
    "gestionarHistorias"         => [HistoriaControlador::class, "gestionar"],
    "crearHistoria"              => [HistoriaControlador::class, "crear"],
    "guardarHistoria"            => [HistoriaControlador::class, "guardar"],
    "verHistoria"                => [HistoriaControlador::class, "ver"],
    "editarHistoria"             => [HistoriaControlador::class, "editar"],
    "actualizarHistoria"         => [HistoriaControlador::class, "actualizar"],

    // ========================================
    // 💊 RECETAS
    // ========================================
    // Vistas del paciente
    "misRecetas"                 => [RecetaControlador::class, "misRecetas"],
    "verMiReceta"                => [RecetaControlador::class, "verMiReceta"],

    // Gestión admin/doctor
    "gestionarRecetas"           => [RecetaControlador::class, "gestionar"],
    "crearReceta"                => [RecetaControlador::class, "crear"],
    "guardarReceta"              => [RecetaControlador::class, "guardar"],
    "verReceta"                  => [RecetaControlador::class, "ver"],
    "imprimirReceta"             => [RecetaControlador::class, "imprimir"],

    // ========================================
    // 📊 REPORTES
    // ========================================
    "reportes"                   => [ReporteControlador::class, "index"],
    "exportarPDF"                => [ReporteControlador::class, "exportarPDF"],
    "exportarExcel"              => [ReporteControlador::class, "exportarExcel"],
];

// =====================================================
// 🚀 EJECUTAR ENRUTADOR
// =====================================================
Enrutador::resolver($accion, $rutas);
