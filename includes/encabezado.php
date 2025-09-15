<?php  
require_once __DIR__ . "/../configuracion/app.php"; 
require_once __DIR__ . "/../nucleo/Autenticacion.php";  

// 🔐 Inicia sesión segura 
Autenticacion::iniciarSesionSegura();  

// 🧑‍💼 Datos del usuario 
$usuarioId = Autenticacion::usuarioId(); 
$rol = $_SESSION['rol'] ?? 'invitado';  

// 🧾 Carga nombre desde el modelo si se desea mostrar  
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