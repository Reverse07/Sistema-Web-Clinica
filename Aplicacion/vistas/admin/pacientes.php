<?php
// =====================
// 🧑‍⚕️ Vista: Gestión de Pacientes (Administrador)
// =====================
// $pacientes viene del controlador PacienteControlador::gestionarPacientes()
// =====================
?>

<div class="admin-usuarios">
    <h1>👨‍⚕️ Gestión de Pacientes</h1>

    <table class="tabla-usuarios">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>DNI</th>
                <th>Fecha Nacimiento</th>
                <th>Género</th>
                <th>Dirección</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pacientes)): ?>
                <tr>
                    <td colspan="9" style="text-align:center; color:#777;">⚠️ No hay pacientes registrados</td>
                </tr>
            <?php else: ?>
                <?php foreach ($pacientes as $paciente): ?>
                    <tr>
                        <td><?= htmlspecialchars($paciente->getUsuarioId()) ?></td>
                        <td><?= htmlspecialchars($paciente->getUsuario()->getNombre()) ?></td>
                        <td><?= htmlspecialchars($paciente->getUsuario()->getEmail()) ?></td>
                        <td><?= htmlspecialchars($paciente->getUsuario()->getTelefono()) ?></td>
                        <td><?= htmlspecialchars($paciente->getDni()) ?></td>
                        <td><?= htmlspecialchars($paciente->getFechaNacimiento()) ?></td>
                        <td><?= htmlspecialchars($paciente->getGenero()) ?></td>
                        <td><?= htmlspecialchars($paciente->getDireccion()) ?></td>
                        <td>
                            <!-- ✏️ Editar -->
                            <a href="<?= BASE_URL ?>/index.php?accion=editarPaciente&id=<?= $paciente->getUsuarioId() ?>" 
                               class="btn-editar">✏️ Editar</a>

                            <!-- 🗑️ Eliminar -->
                            <a href="<?= BASE_URL ?>/index.php?accion=eliminarPaciente&id=<?= $paciente->getUsuarioId() ?>" 
                               class="btn-eliminar"
                               onclick="return confirm('⚠️ ¿Estás seguro que deseas eliminar este paciente?')">
                                🗑️ Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
