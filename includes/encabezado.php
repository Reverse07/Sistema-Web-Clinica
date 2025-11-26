<?php  
// =====================================================
// 🔐 ENCABEZADO GENERAL
// =====================================================

// Solo requerir configuración (la sesión YA está iniciada en index.php)
require_once __DIR__ . "/../configuracion/app.php"; 

// 🧑‍💼 Obtener datos del usuario desde la sesión YA INICIADA
$usuarioId = $_SESSION['usuario_id'] ?? null; 
$rol = $_SESSION['rol'] ?? 'invitado';  

// 🧾 Cargar nombre desde el modelo solo si hay usuario logueado
$nombre = 'Invitado';
if ($usuarioId) {
    require_once __DIR__ . "/../Aplicacion/modelos/Usuario.php"; 
    $usuario = Usuario::buscarPorId($usuarioId);
    if ($usuario) {
        $nombre = $usuario->getNombre();
    }
}
?>  

<!-- ===================================================== -->
<!-- 🎨 HEADER GENERAL (ADMIN Y PACIENTE) -->
<!-- ===================================================== -->
<header class="header-container">     
    <div class="header-left">         
        <img src="<?= BASE_URL ?>/recursos/img/logoClinica.jpg" alt="Logo Clínica" class="logo-header">         
        <span class="sistema-nombre">Sistema Clínico Reverse</span>     
    </div>      

    <div class="header-center">         
        <div class="bienvenida-container">
            <span class="bienvenida">👋 Bienvenido, <strong><?= htmlspecialchars($nombre) ?></strong></span>
            <span class="rol">🎭 Rol: <strong><?= ucfirst($rol) ?></strong></span>
        </div>
    </div>      

    <div class="header-right">         
        <a href="<?= BASE_URL ?>/index.php?accion=logout" class="btn-salir" 
           onclick="return confirm('¿Estás seguro de que deseas cerrar sesión?');">
            🚪 Cerrar sesión
        </a>    
    </div> 
</header>