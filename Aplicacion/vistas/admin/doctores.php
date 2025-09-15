<?php
// =====================
// 🩺 Vista: Gestión de Doctores
// =====================
?>

<div class="admin-usuarios">
    <h1>🩺 Gestión de Doctores</h1>

    <div class="acciones-superiores">
        <a href="<?= BASE_URL ?>/index.php?accion=crearDoctor" class="btn-crear">➕ Crear Doctor</a>
    </div>

    <table class="tabla-usuarios">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Especialidad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($doctores as $doctor): ?>
                <tr>
                    <td><?= $doctor->getId() ?></td>
                    <td><?= $doctor->getNombre() ?></td>
                    <td><?= $doctor->getEmail() ?></td>
                    <td><?= $doctor->getTelefono() ?></td>
                    <td><?= $doctor->getEspecialidad() ?? 'No definida' ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/index.php?accion=editarDoctor&id=<?= $doctor->getId() ?>" class="btn-editar">✏️ Editar</a>
                        <a href="<?= BASE_URL ?>/index.php?accion=eliminarDoctor&id=<?= $doctor->getId() ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este doctor?')">🗑️ Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
