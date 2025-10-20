<?php
require_once __DIR__ . "/../configuracion/app.php";
?>

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