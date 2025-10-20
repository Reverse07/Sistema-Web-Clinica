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

    <?php if (empty($doctores)): ?>
        <p class="mensaje-vacio">No hay doctores registrados en el sistema.</p>
    <?php else: ?>
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
                        <!-- Usar usuario_id en lugar de id -->
                        <td><?= $doctor['usuario_id'] ?? $doctor['id'] ?? 'N/A' ?></td>
                        <td><?= htmlspecialchars($doctor['nombre'] ?? '') ?></td>
                        <td><?= htmlspecialchars($doctor['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($doctor['telefono'] ?? 'No definido') ?></td>
                        <td><?= htmlspecialchars($doctor['especialidad'] ?? 'No definida') ?></td>
                        <td class="acciones">
                            <a href="<?= BASE_URL ?>/index.php?accion=editarDoctor&id=<?= $doctor['usuario_id'] ?? $doctor['id'] ?>" 
                               class="btn-editar" 
                               title="Editar doctor">
                                ✏️ Editar
                            </a>
                            <a href="<?= BASE_URL ?>/index.php?accion=eliminarDoctor&id=<?= $doctor['usuario_id'] ?? $doctor['id'] ?>" 
                               class="btn-eliminar" 
                               title="Eliminar doctor"
                               onclick="return confirm('¿Está seguro de eliminar este doctor?')">
                                🗑️ Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>