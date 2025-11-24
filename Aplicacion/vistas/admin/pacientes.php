<?php
// =====================
// 🧑‍⚕️ Vista: Gestión de Pacientes (Administrador)
// =====================
?>

<div class="admin-usuarios">
    <h1>👨‍⚕️ Gestión de Pacientes</h1>

    <div class="acciones-superiores">
        <a href="<?= BASE_URL ?>/index.php?accion=crearPaciente" class="btn-crear">
            ➕ Crear Paciente
        </a>
    </div>

    <?php if (empty($pacientes)): ?>
        <p class="mensaje-vacio">No hay pacientes registrados en el sistema.</p>
    <?php else: ?>
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
                        <td class="acciones">
                            <a href="<?= BASE_URL ?>/index.php?accion=editarPaciente&id=<?= $paciente->getUsuarioId() ?>" 
                               class="btn-editar"
                               title="Editar paciente">
                                ✏️ Editar
                            </a>
                            <a href="<?= BASE_URL ?>/index.php?accion=eliminarPaciente&id=<?= $paciente->getUsuarioId() ?>" 
                               class="btn-eliminar"
                               title="Eliminar paciente">
                                🗑️ Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>