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
   class="btn-eliminar swal-eliminar-doctor" 
   data-id="<?= $doctor['usuario_id'] ?? $doctor['id'] ?>"
   title="Eliminar doctor">
    🗑️ Eliminar
</a>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// SweetAlert para eliminar un doctor
document.querySelectorAll('.swal-eliminar-doctor').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();

        const doctorId = this.getAttribute('data-id');
        const url = `<?= BASE_URL ?>/index.php?accion=eliminarDoctor&id=${doctorId}`;

        Swal.fire({
            title: "¿Eliminar doctor?",
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6"
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});
</script>
