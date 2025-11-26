<?php
// =====================================================
// 🧑‍🤝‍🧑 Vista: Gestión de Pacientes (ADMIN)
// Ubicación: Aplicacion/vistas/admin/pacientes.php
// =====================================================
?>

<div class="admin-usuarios">
    <h1>🧑‍🤝‍🧑 Gestión de Pacientes</h1>

    <!-- ✅ Mostrar mensajes flash -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="mensaje mensaje-<?= $_SESSION['mensaje']['tipo'] ?>">
            <?= htmlspecialchars($_SESSION['mensaje']['texto']) ?>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <div class="acciones-superiores">
        <a href="<?= BASE_URL ?>/index.php?accion=crearPaciente" class="btn-crear">
            ➕ Crear Paciente
        </a>
    </div>

    <?php if (empty($pacientes)): ?>
        <p class="mensaje-vacio">📋 No hay pacientes registrados en el sistema.</p>
    <?php else: ?>
        <div class="tabla-responsive">
            <table class="tabla-usuarios">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>DNI</th>
                        <th>Género</th>
                        <th>Fecha Nac.</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pacientes as $paciente): ?>
                        <?php 
                        // Obtener el usuario_id correcto
                        $usuarioId = $paciente['usuario_id'] ?? $paciente['id'] ?? 0;
                        ?>
                        <tr>
                            <td><?= $usuarioId ?></td>
                            <td><?= htmlspecialchars($paciente['nombre'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($paciente['email'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($paciente['telefono'] ?? 'No definido') ?></td>
                            <td><?= htmlspecialchars($paciente['dni'] ?? 'N/A') ?></td>
                            <td>
                                <?php 
                                $genero = $paciente['genero'] ?? '';
                                echo match($genero) {
                                    'M', 'Masculino' => '👨 Masculino',
                                    'F', 'Femenino' => '👩 Femenino',
                                    default => '⚪ No especificado'
                                };
                                ?>
                            </td>
                            <td>
                                <?php 
                                if (!empty($paciente['fecha_nacimiento'])) {
                                    $fecha = new DateTime($paciente['fecha_nacimiento']);
                                    echo $fecha->format('d/m/Y');
                                } else {
                                    echo 'No registrado';
                                }
                                ?>
                            </td>
                            <td class="acciones">
                                <!-- ✅ Botón Editar -->
                                <a href="<?= BASE_URL ?>/index.php?accion=editarPaciente&id=<?= $usuarioId ?>" 
                                   class="btn-editar" 
                                   title="Editar paciente">
                                    ✏️ Editar
                                </a>

                                <!-- ✅ Botón Eliminar -->
                                <button type="button"
                                        class="btn-eliminar swal-eliminar-paciente" 
                                        data-id="<?= $usuarioId ?>"
                                        data-nombre="<?= htmlspecialchars($paciente['nombre'] ?? 'Paciente') ?>"
                                        title="Eliminar paciente">
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
            <p>📊 Total de pacientes: <strong><?= count($pacientes) ?></strong></p>
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
        document.addEventListener('DOMContentLoaded', inicializarEliminarPacientes);
    } else {
        inicializarEliminarPacientes();
    }

    function inicializarEliminarPacientes() {
        const botones = document.querySelectorAll('.swal-eliminar-paciente');
        console.log(`✅ Botones de eliminar encontrados: ${botones.length}`);

        botones.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                const pacienteId = this.getAttribute('data-id');
                const pacienteNombre = this.getAttribute('data-nombre');

                console.log("📋 Eliminando paciente:");
                console.log("  - ID:", pacienteId);
                console.log("  - Nombre:", pacienteNombre);

                Swal.fire({
                    title: '¿Eliminar paciente?',
                    html: `
                        <p>Estás a punto de eliminar al paciente:</p>
                        <p><strong>${pacienteNombre}</strong> (ID: ${pacienteId})</p>
                        <p style="color: #dc3545; font-weight: bold;">⚠️ Esta acción NO se puede deshacer</p>
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
                        const url = `${BASE_URL}/index.php?accion=eliminarPaciente&id=${pacienteId}`;
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
/* =====================================================
   🎨 ESTILOS ADICIONALES
   ===================================================== */

.acciones {
    display: flex;
    gap: 0.5rem;
    white-space: nowrap;
}

.btn-editar,
.btn-eliminar {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
    white-space: nowrap;
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

/* Estadísticas */
.estadisticas-footer {
    margin-top: 1.5rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 8px;
    text-align: center;
}

.estadisticas-footer p {
    margin: 0;
    color: #475569;
    font-size: 1rem;
}

.estadisticas-footer strong {
    color: #1e293b;
    font-size: 1.2rem;
}

/* Mensajes flash */
.mensaje {
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-weight: 500;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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

/* Estilos para SweetAlert */
.text-danger {
    color: #dc2626;
    font-weight: 600;
    margin: 0.5rem 0;
}

.text-left {
    text-align: left;
    margin: 0.5rem 0;
    padding-left: 1rem;
}

.text-left li {
    margin: 0.25rem 0;
    color: #475569;
}

/* Responsive */
@media (max-width: 1200px) {
    .tabla-usuarios {
        font-size: 0.85rem;
    }
    
    .tabla-usuarios th,
    .tabla-usuarios td {
        padding: 0.75rem 0.5rem;
    }
}

@media (max-width: 768px) {
    .admin-usuarios {
        padding: 1rem;
    }
    
    .acciones {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .btn-editar,
    .btn-eliminar {
        width: 100%;
        text-align: center;
    }
}
</style>