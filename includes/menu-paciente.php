<?php  
// =====================================================
// 🧭 MENÚ PACIENTE
// =====================================================

// Solo requerir configuración (la sesión YA está iniciada)
require_once __DIR__ . "/../configuracion/app.php"; 

// Obtener acción actual para marcar el menú activo
$accion = $_GET['accion'] ?? '';
?>

<!-- ===================== -->
<!-- 🌐 MENÚ PACIENTE -->
<!-- ===================== -->

<nav class="menu-paciente">
    <h2 class="menu-titulo">👤 Panel del Paciente</h2>
    <ul class="menu-lista">
        <li>
            <a href="?accion=dashboardPaciente" 
               class="menu-link <?= ($accion == 'dashboardPaciente' || $accion == 'pacienteDashboard') ? 'activo' : '' ?>">
                🏠 Inicio
            </a>
        </li>
        <li>
            <a href="?accion=pacientePerfil" 
               class="menu-link <?= ($accion == 'pacientePerfil' || $accion == 'perfilPaciente') ? 'activo' : '' ?>">
                👤 Mi Perfil
            </a>
        </li>
        <li>
            <a href="?accion=misCitas" 
               class="menu-link <?= ($accion == 'misCitas' || $accion == 'pacienteCitas') ? 'activo' : '' ?>">
                📅 Mis Citas
            </a>
        </li>
        <li>
            <a href="?accion=miHistorial" 
               class="menu-link <?= ($accion == 'miHistorial' || $accion == 'pacienteHistorial' || $accion == 'historialMedico') ? 'activo' : '' ?>">
                📋 Historial Médico
            </a>
        </li>
        <li>
            <a href="?accion=misFacturas" 
               class="menu-link <?= ($accion == 'misFacturas' || $accion == 'pacienteFacturas') ? 'activo' : '' ?>">
                💳 Mis Facturas
            </a>
        </li>
        <li>
            <a href="?accion=configuracionPaciente" 
               class="menu-link <?= ($accion == 'configuracionPaciente') ? 'activo' : '' ?>">
                ⚙️ Configuración
            </a>
        </li>
    </ul>
</nav>