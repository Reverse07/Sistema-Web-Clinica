<?php
// =====================================================
// 📌 PUNTO DE ENTRADA PRINCIPAL DEL SISTEMA
// Sistema de Gestión de Clínica Médica
// Ubicación: publico/index.php
// =====================================================

// =====================================================
// 🔹 PASO 1: CONFIGURACIÓN
// =====================================================
require_once __DIR__ . "/../configuracion/app.php";

// =====================================================
// 🔹 PASO 2: NÚCLEO DEL SISTEMA
// =====================================================
require_once __DIR__ . "/../nucleo/Autenticacion.php";
require_once __DIR__ . "/../nucleo/Enrutador.php";
require_once __DIR__ . "/../nucleo/BaseDatos.php";

// ✅ Iniciar sesión segura UNA SOLA VEZ
Autenticacion::iniciarSesionSegura();

// =====================================================
// 🔹 PASO 3: AUTOLOAD DE COMPOSER (TCPDF, PHPMailer, etc.)
// =====================================================
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    error_log("⚠️ ADVERTENCIA: vendor/autoload.php no encontrado. Ejecuta: composer install");
}

// =====================================================
// 🔹 PASO 4: CONTROLADORES
// =====================================================
$controladores = [
    'AuthControlador',
    'AdminControlador',
    'DoctorControlador',
    'PacienteControlador',
    'UsuarioControlador',
    'CitaControlador',
    'FacturaControlador',
    'HistoriaControlador',
    'RecetaControlador',
    'ReporteControlador',
    'ConfiguracionControlador'
];

foreach ($controladores as $controlador) {
    $ruta = __DIR__ . "/../Aplicacion/controladores/{$controlador}.php";
    if (file_exists($ruta)) {
        require_once $ruta;
    } else {
        error_log("⚠️ ADVERTENCIA: Controlador no encontrado - $controlador");
    }
}

// =====================================================
// 🚦 PASO 5: ENRUTAMIENTO
// =====================================================

// Detectar acción desde URL
$accion = $_GET['accion'] ?? (Autenticacion::usuarioId() ? 'dashboardAdmin' : 'loginVista');

// Mapa de rutas del sistema
$rutas = [

    // ========================================
    // 🔐 AUTENTICACIÓN
    // ========================================
    "loginVista"                    => [AuthControlador::class, "loginVista"],
    "login"                         => [AuthControlador::class, "login"],
    "registroVista"                 => [AuthControlador::class, "registroVista"],
    "registro"                      => [AuthControlador::class, "registro"],
    "logout"                        => [AuthControlador::class, "logout"],
    "terminos"                      => [AuthControlador::class, "terminos"],
    "privacidad"                    => [AuthControlador::class, "privacidad"],
    
    // 🔐 Recuperación de contraseña por SMS
    "enviarCodigoRecuperacion"      => [AuthControlador::class, "enviarCodigoRecuperacion"],
    "verificarCodigoRecuperacion"   => [AuthControlador::class, "verificarCodigoRecuperacion"],
    "cambiarPasswordRecuperacion"   => [AuthControlador::class, "cambiarPasswordRecuperacion"],

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
    "Dashboard"                  => [DoctorControlador::class, "dashboard"],
    "dashboardDoctor"            => [DoctorControlador::class, "dashboard"],
    "doctorDashboard"            => [DoctorControlador::class, "dashboard"],
    "doctorPerfil"               => [DoctorControlador::class, "perfil"],
    "perfilDoctor"               => [DoctorControlador::class, "perfil"], // ✅ NUEVA RUTA AÑADIDA
    "doctorCitas"                => [DoctorControlador::class, "misCitas"],
    "verCitas"                   => [DoctorControlador::class, "misCitas"],
    "configuracionDoctor"        => [DoctorControlador::class, "configuracion"], // ✅ AÑADIR ESTA LÍNEA
    "guardarConfiguracionDoctor"    => [DoctorControlador::class, "guardarConfiguracionDoctor"], // ✅ AÑADIR
    "verDetalleCita"             => [DoctorControlador::class, "verDetalle"],

    // ========================================
    // 📋 HISTORIAS CLÍNICAS - Doctor
    // ========================================
    "doctorHistorias"            => [DoctorControlador::class, "misHistorias"],
    "historialPacientes"         => [DoctorControlador::class, "misHistorias"],
    "crearHistoria"              => [DoctorControlador::class, "crearHistoria"],
    "guardarHistoria"            => [DoctorControlador::class, "guardarHistoria"],
    "editarHistoria"             => [DoctorControlador::class, "editarHistoria"],
    "actualizarHistoria"         => [DoctorControlador::class, "actualizarHistoria"],

    // ========================================
    // 💊 RECETAS - DOCTOR (RUTAS CORREGIDAS)
    // ========================================
    "doctorRecetas"              => [DoctorControlador::class, "misRecetas"],
    "doctorCrearReceta"          => [DoctorControlador::class, "crearReceta"],
    "doctorGuardarReceta"        => [DoctorControlador::class, "guardarReceta"],
    "verReceta"                  => [DoctorControlador::class, "verReceta"],
    "imprimirReceta"             => [DoctorControlador::class, "imprimirReceta"],
    
    // Alternativas para mayor compatibilidad:
    "recetasEmitidas"            => [DoctorControlador::class, "misRecetas"],
    "misRecetas"                 => [DoctorControlador::class, "misRecetas"],
    "verMiReceta"                => [DoctorControlador::class, "verReceta"],
    "crearReceta"                => [DoctorControlador::class, "crearReceta"],
    "guardarReceta"              => [DoctorControlador::class, "guardarReceta"],

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
    "pacienteFacturas"           => [PacienteControlador::class, "misFacturas"],
    "misFacturas"                => [PacienteControlador::class, "misFacturas"],
    "crearCitaPaciente"          => [CitaControlador::class, "crearCitaPaciente"],
    "guardarCitaPaciente"        => [CitaControlador::class, "guardarCitaPaciente"],

    // ========================================
    // 📋 HISTORIAS CLÍNICAS - Paciente
    // ========================================
    "pacienteHistorial"          => [PacienteControlador::class, "miHistorial"],
    "miHistorial"                => [PacienteControlador::class, "miHistorial"],
    "historialMedico"            => [PacienteControlador::class, "miHistorial"],
    "verHistoria"                => [PacienteControlador::class, "verHistoria"],
    "verHistoriaPaciente"        => [PacienteControlador::class, "verHistoria"],
    "descargarHistoria"          => [PacienteControlador::class, "descargarHistoria"],
    "imprimirHistoria"           => [PacienteControlador::class, "imprimirHistoria"],

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
    "gestionarCitas"             => [CitaControlador::class, "gestionar"],
    "citas"                      => [CitaControlador::class, "gestionar"],
    "crearCita"                  => [CitaControlador::class, "crear"],
    "guardarCita"                => [CitaControlador::class, "guardar"],
    "editarCita"                 => [CitaControlador::class, "editar"],
    "editar"                     => [CitaControlador::class, "editar"],
    "actualizarCita"             => [CitaControlador::class, "actualizar"],
    "confirmarCita"              => [CitaControlador::class, "confirmar"],
    "confirmar"                  => [CitaControlador::class, "confirmar"],
    "cancelarCita"               => [CitaControlador::class, "cancelar"],
    "cancelar"                   => [CitaControlador::class, "cancelar"],
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
    "marcarPagada"               => [FacturaControlador::class, "marcarPagada"],
    "descargarFactura"           => [FacturaControlador::class, "descargar"],

    // ========================================
    // 📊 REPORTES
    // ========================================
    "reportes"                   => [ReporteControlador::class, "index"],
    "exportarPDF"                => [ReporteControlador::class, "exportarPDF"],
    "exportarExcel"              => [ReporteControlador::class, "exportarExcel"],
];

// =====================================================
// 🚀 PASO 6: EJECUTAR ENRUTADOR
// =====================================================
try {
    Enrutador::resolver($accion, $rutas);
} catch (Exception $e) {
    if (APP_ENV === 'dev') {
        echo "<h1>❌ Error en el sistema</h1>";
        echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        error_log("Error en enrutador: " . $e->getMessage());
        header("Location: " . BASE_URL . "/index.php?accion=loginVista");
    }
}