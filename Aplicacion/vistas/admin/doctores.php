<?php
// =====================================================
// 🩺 Vista: Gestión de Doctores (ADMIN)
// Ubicación: Aplicacion/vistas/admin/doctores.php
// =====================================================
?>

<div class="admin-usuarios">
    <h1>🩺 Gestión de Doctores</h1>

    <!-- ✅ Mostrar mensajes flash -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="mensaje mensaje-<?= $_SESSION['mensaje']['tipo'] ?>">
            <?= htmlspecialchars($_SESSION['mensaje']['texto']) ?>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <div class="acciones-superiores">
        <a href="<?= BASE_URL ?>/index.php?accion=crearDoctor" class="btn-crear">
            ➕ Crear Doctor
        </a>
    </div>

    <?php if (empty($doctores)): ?>
        <p class="mensaje-vacio">📋 No hay doctores registrados en el sistema.</p>
    <?php else: ?>
        <div class="tabla-responsive">
            <table class="tabla-usuarios">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Especialidad</th>
                        <th>N° Colegiatura</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($doctores as $doctor): ?>
                        <?php 
                        $usuarioId = $doctor['usuario_id'] ?? $doctor['id'] ?? 0;
                        ?>
                        <tr>
                            <td><?= $usuarioId ?></td>
                            <td><?= htmlspecialchars($doctor['nombre'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($doctor['email'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($doctor['telefono'] ?? 'No definido') ?></td>
                            <td><?= htmlspecialchars($doctor['especialidad'] ?? 'No definida') ?></td>
                            <td><?= htmlspecialchars($doctor['numero_colegiatura'] ?? 'N/A') ?></td>
                            <td class="acciones">
                                <!-- ✅ Botón Editar -->
                                <a href="<?= BASE_URL ?>/index.php?accion=editarDoctor&id=<?= $usuarioId ?>" 
                                   class="btn-editar" 
                                   title="Editar doctor">
                                    ✏️ Editar
                                </a>

                                <!-- ✅ Botón Eliminar -->
                                <button type="button"
                                        class="btn-eliminar swal-eliminar-doctor" 
                                        data-id="<?= $usuarioId ?>"
                                        data-nombre="<?= htmlspecialchars($doctor['nombre'] ?? 'Doctor') ?>"
                                        title="Eliminar doctor">
                                    🗑️ Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- ✅ Estadísticas -->
        <div class="estadisticas-footer">
            <p>📊 Total de doctores: <strong><?= count($doctores) ?></strong></p>
        </div>
    <?php endif; ?>
</div>

<!-- ✅ SCRIPT INLINE CON BASE_URL DE PHP -->
<script>
(function() {
    // ✅ Definir BASE_URL desde PHP directamente en este scope
    const BASE_URL = "<?= BASE_URL ?>";
    
    console.log("🔍 BASE_URL cargado:", BASE_URL);

    // ✅ Esperar a que el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializarEliminarDoctores);
    } else {
        inicializarEliminarDoctores();
    }

    function inicializarEliminarDoctores() {
        const botones = document.querySelectorAll('.swal-eliminar-doctor');
        console.log(`✅ Botones de eliminar encontrados: ${botones.length}`);

        botones.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                const doctorId = this.getAttribute('data-id');
                const doctorNombre = this.getAttribute('data-nombre');

                console.log("📋 Eliminando doctor:");
                console.log("  - ID:", doctorId);
                console.log("  - Nombre:", doctorNombre);

                Swal.fire({
                    title: '¿Eliminar doctor?',
                    html: `
                        <p>Estás a punto de eliminar al doctor:</p>
                        <p><strong>${doctorNombre}</strong> (ID: ${doctorId})</p>
                        <p style="color: #dc3545; font-weight: bold;">⚠️ Esta acción NO se puede deshacer</p>
                        <p style="color: #64748b; font-size: 0.9rem; margin-top: 0.5rem;">
                            Las citas, recetas e historias del doctor se mantendrán pero sin asignación.
                        </p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Eliminando...',
                            icon: 'info',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // ✅ Construir URL con BASE_URL local
                        const url = `${BASE_URL}/index.php?accion=eliminarDoctor&id=${doctorId}`;
                        console.log("🚀 Redirigiendo a:", url);
                        
                        window.location.href = url;
                    }
                });
            });
        });
    }

    // Auto-ocultar mensajes después de 5 segundos
    setTimeout(() => {
        const mensaje = document.querySelector('.mensaje');
        if (mensaje) {
            mensaje.style.transition = 'opacity 0.5s';
            mensaje.style.opacity = '0';
            setTimeout(() => mensaje.remove(), 500);
        }
    }, 5000);
})();
</script>

<style>


.btn-editar,
.btn-eliminar {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-editar {
    background: #3b82f6;
    color: white;
}

.btn-editar:hover {
    background: #2563eb;
    transform: translateY(-2px);
}

.btn-eliminar {
    background: #ef4444;
    color: white;
}

.btn-eliminar:hover {
    background: #dc2626;
    transform: translateY(-2px);
}

/* Mensajes flash */
.mensaje {
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-weight: 500;
}

.mensaje-exito {
    background: #d1fae5;
    color: #065f46;
    border-left: 4px solid #10b981;
}

.mensaje-error {
    background: #fee2e2;
    color: #991b1b;
    border-left: 4px solid #ef4444;
}

.mensaje-info {
    background: #dbeafe;
    color: #1e40af;
    border-left: 4px solid #3b82f6;
}

.estadisticas-footer {
    margin-top: 1.5rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 8px;
    text-align: center;
}

.estadisticas-footer p {
    margin: 0;
    color: #64748b;
}

.estadisticas-footer strong {
    color: #1e293b;
    font-size: 1.1rem;
}
</style>