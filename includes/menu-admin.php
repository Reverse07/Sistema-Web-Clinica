<?php  
require_once __DIR__ . "/../configuracion/app.php"; 
require_once __DIR__ . "/../nucleo/Autenticacion.php";  

// 🔐 Inicia sesión segura 
Autenticacion::iniciarSesionSegura();  

// 🧑‍💼 Datos del usuario 
$usuarioId = Autenticacion::usuarioId(); 
$rol = $_SESSION['rol'] ?? 'invitado';  

// 🧾 Carga nombre desde el modelo  
require_once __DIR__ . "/../Aplicacion/modelos/Usuario.php"; 
$usuario = Usuario::buscarPorId($usuarioId); 
$nombre = $usuario ? $usuario->getNombre() : 'Invitado'; 
?>  

<header class="header-container">     
    <div class="header-left">         
        <img src="<?= BASE_URL ?>/recursos/img/logoClinica.jpg" alt="logo Clinica" class="logo-header">         
        <span class="sistema-nombre">Sistema Clinico Reverse</span>     
    </div>      

    <div class="header-center">         
        <div class="bienvenida-container">
            <span class="bienvenida">👋 Bienvenido, <strong><?= htmlspecialchars($nombre) ?></strong></span>
            <span class="rol">🎭 Rol: <strong><?= ucfirst($rol) ?></strong></span>
        </div>
    </div>      

    <div class="header-right">         
        <a href="<?= BASE_URL ?>/index.php?accion=logout" class="btn-salir">Cerrar sesión</a>    
    </div> 
</header>

<!-- ===================== -->
<!-- 🌐 MENÚ ADMINISTRADOR -->
<!-- ===================== -->

<nav class="menu-admin">
    <h2 class="menu-titulo">🛠 Panel Administrativo</h2>
    <ul class="menu-lista">
        <li><a href="<?= BASE_URL ?>/index.php?accion=dashboardAdmin" class="menu-link">📊 Dashboard</a></li>
        <li><a href="<?= BASE_URL ?>/index.php?accion=gestionarUsuarios" class="menu-link">👥 Usuarios</a></li>
        <li><a href="<?= BASE_URL ?>/index.php?accion=gestionarDoctores" class="menu-link">🩺 Doctores</a></li>
        <li><a href="<?= BASE_URL ?>/index.php?accion=gestionarPacientes" class="menu-link">🧑‍🤝‍🧑 Pacientes</a></li>
        <li><a href="<?= BASE_URL ?>/index.php?accion=gestionarCitas" class="menu-link">📅 Citas</a></li>
        <li><a href="<?= BASE_URL ?>/index.php?accion=gestionarFacturas" class="menu-link">💳 Facturas</a></li>
        <li><a href="<?= BASE_URL ?>/index.php?accion=verReportes" class="menu-link">📈 Reportes</a></li>
        <li><a href="<?= BASE_URL ?>/index.php?accion=configuracion" class="menu-link">⚙️ Configuración</a></li>
    </ul>
</nav>
