<nav class="menu-paciente">
    <h2 class="menu-titulo">🧑‍⚕️ Panel del Paciente</h2>

    <ul class="menu-lista">
        <li><a href="<?= BASE_URL ?>/index.php?accion=dashboardPaciente" class="<?= ($accion=='dashboardPaciente')?'activo':'' ?>">📊 Dashboard</a></li>

        <li><a href="<?= BASE_URL ?>/index.php?accion=misCitas" class="<?= ($accion=='misCitas')?'activo':'' ?>">📅 Mis Citas</a></li>

        <li><a href="<?= BASE_URL ?>/index.php?accion=historialMedico" class="<?= ($accion=='historialMedico')?'activo':'' ?>">📁 Historial Médico</a></li>

        <li><a href="<?= BASE_URL ?>/index.php?accion=misRecetas" class="<?= ($accion=='misRecetas')?'activo':'' ?>">💊 Mis Recetas</a></li>

        <li><a href="<?= BASE_URL ?>/index.php?accion=misFacturas" class="<?= ($accion=='misFacturas')?'activo':'' ?>">🧾 Mis Facturas</a></li>

        <li><a href="<?= BASE_URL ?>/index.php?accion=perfilPaciente" class="<?= ($accion=='perfilPaciente')?'activo':'' ?>">👤 Mi Perfil</a></li>

        <li><a href="<?= BASE_URL ?>/index.php?accion=configuracionPaciente" class="<?= ($accion=='configuracionPaciente')?'activo':'' ?>">⚙️ Configuración</a></li>
    </ul>
</nav>
