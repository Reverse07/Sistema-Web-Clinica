<style>
    .editar-cita-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }

    .header-editar {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 2.5rem;
        border-radius: 20px 20px 0 0;
        margin-bottom: 0;
        box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);
    }

    .header-editar h2 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .header-editar p {
        margin: 0.5rem 0 0 0;
        opacity: 0.95;
        font-size: 1rem;
    }

    .info-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.2);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        margin-top: 1rem;
    }

    .form-editar-card {
        background: white;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        padding: 3rem;
        border: none;
        margin-top: 0;
    }

    .form-group-modern {
        margin-bottom: 2rem;
    }

    .form-label-modern {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-control-modern,
    .form-select-modern {
        padding: 0.875rem 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #f8fafc;
    }

    .form-control-modern:focus,
    .form-select-modern:focus {
        border-color: #f5576c;
        box-shadow: 0 0 0 4px rgba(245, 87, 108, 0.1);
        background-color: white;
        outline: none;
    }

    .form-control-modern:hover,
    .form-select-modern:hover {
        border-color: #cbd5e0;
        background-color: white;
    }

    .form-control-modern:disabled,
    .form-select-modern:disabled {
        background-color: #f1f5f9;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .alert-modern {
        border-radius: 12px;
        padding: 1rem 1.5rem;
        border: none;
        margin-bottom: 2rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-success-modern {
        background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%);
        color: #1e4620;
    }

    .alert-danger-modern {
        background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%);
        color: #5a1a1a;
    }

    .alert-info-modern {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        color: #1e3a5f;
    }

    .estado-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    .estado-pendiente {
        background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
        color: #5a3e00;
    }

    .estado-confirmada {
        background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%);
        color: #1e4620;
    }

    .estado-cancelada {
        background: linear-gradient(135deg, #fab1a0 0%, #fd79a8 100%);
        color: #5a1a1a;
    }

    .btn-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 2px solid #f1f5f9;
    }

    .btn-modern {
        padding: 0.875rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
    }

    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(245, 87, 108, 0.5);
        color: white;
    }

    .btn-secondary-modern {
        background: #e2e8f0;
        color: #475569;
    }

    .btn-secondary-modern:hover {
        background: #cbd5e0;
        transform: translateY(-2px);
        color: #1e293b;
    }

    .btn-danger-modern {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: #5a1a1a;
    }

    .btn-danger-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(250, 112, 154, 0.4);
    }

    .icon-input {
        font-size: 1.2rem;
    }

    .required-asterisk {
        color: #ef4444;
        margin-left: 0.25rem;
    }

    .info-section {
        background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);
        border-left: 4px solid #0ea5e9;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
    }

    .info-section h4 {
        margin: 0 0 1rem 0;
        color: #0c4a6e;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 600;
        letter-spacing: 0.05em;
    }

    .info-value {
        font-size: 1rem;
        color: #0f172a;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .form-editar-card {
            padding: 1.5rem;
        }

        .header-editar {
            padding: 1.5rem;
        }

        .header-editar h2 {
            font-size: 1.5rem;
        }

        .btn-actions {
            flex-direction: column-reverse;
        }

        .btn-modern {
            width: 100%;
            justify-content: center;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="editar-cita-container">
    <!-- Header -->
    <div class="header-editar">
        <h2>
            <span>✏️</span>
            Editar Cita Médica
        </h2>
        <p>Modifique los datos de la cita según sea necesario</p>
        <div class="info-badge">
            <span>🆔</span>
            <span>ID de Cita: #<?= $cita->getId() ?></span>
        </div>
    </div>

    <!-- Mensajes de alerta -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert-modern <?= $_SESSION['mensaje']['tipo'] === 'error' ? 'alert-danger-modern' : 'alert-success-modern' ?>">
            <span><?= $_SESSION['mensaje']['tipo'] === 'error' ? '⚠️' : '✅' ?></span>
            <span><?= htmlspecialchars($_SESSION['mensaje']['texto']) ?></span>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <!-- Formulario -->
    <form action="?accion=actualizarCita" method="POST" class="form-editar-card">
        <!-- Campo oculto con ID -->
        <input type="hidden" name="id" value="<?= $cita->getId() ?>">

        <!-- Información de la cita actual -->
        <div class="info-section">
            <h4>
                <span>📊</span>
                Información Actual
            </h4>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Fecha Registro</span>
                    <span class="info-value"><?= date('d/m/Y', strtotime($cita->getFecha())) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Hora</span>
                    <span class="info-value"><?= date('h:i A', strtotime($cita->getFecha())) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Estado Actual</span>
                    <span class="estado-badge estado-<?= strtolower($cita->getEstado()) ?>">
                        <?php
                            $iconos = [
                                'Pendiente' => '⏳',
                                'Confirmada' => '✅',
                                'Cancelada' => '❌'
                            ];
                            echo $iconos[$cita->getEstado()] ?? '📌';
                        ?>
                        <?= htmlspecialchars($cita->getEstado()) ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Paciente -->
            <div class="col-md-6">
                <div class="form-group-modern">
                    <label for="paciente_id" class="form-label-modern">
                        <span class="icon-input">👤</span>
                        Paciente
                        <span class="required-asterisk">*</span>
                    </label>
                    <select class="form-select form-select-modern" id="paciente_id" name="paciente_id" required>
                        <option value="">Seleccione un paciente</option>
                        <?php foreach ($pacientes as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $p['id'] == $cita->getPacienteId() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nombre']) ?>
                                <?php if (!empty($p['email'])): ?>
                                    - <?= htmlspecialchars($p['email']) ?>
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Doctor -->
            <div class="col-md-6">
                <div class="form-group-modern">
                    <label for="doctor_id" class="form-label-modern">
                        <span class="icon-input">👨‍⚕️</span>
                        Doctor
                        <span class="required-asterisk">*</span>
                    </label>
                    <select class="form-select form-select-modern" id="doctor_id" name="doctor_id" required>
                        <option value="">Seleccione un doctor</option>
                        <?php foreach ($doctores as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= $d['id'] == $cita->getDoctorId() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['nombre']) ?>
                                <?php if (!empty($d['especialidad'])): ?>
                                    - <?= htmlspecialchars($d['especialidad']) ?>
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Fecha -->
            <div class="col-md-6">
                <div class="form-group-modern">
                    <label for="fecha" class="form-label-modern">
                        <span class="icon-input">📅</span>
                        Nueva Fecha y Hora
                        <span class="required-asterisk">*</span>
                    </label>
                    <input 
                        type="datetime-local" 
                        class="form-control form-control-modern" 
                        id="fecha" 
                        name="fecha" 
                        value="<?= date('Y-m-d\TH:i', strtotime($cita->getFecha())) ?>"
                        required
                    >
                    <small class="text-muted" style="font-size: 0.85rem; margin-top: 0.5rem; display: block;">
                        💡 Puede modificar la fecha y hora según disponibilidad
                    </small>
                </div>
            </div>

            <!-- Estado -->
            <div class="col-md-6">
                <div class="form-group-modern">
                    <label for="estado" class="form-label-modern">
                        <span class="icon-input">📌</span>
                        Actualizar Estado
                        <span class="required-asterisk">*</span>
                    </label>
                    <select class="form-select form-select-modern" id="estado" name="estado" required>
                        <option value="Pendiente" <?= $cita->getEstado() == 'Pendiente' ? 'selected' : '' ?>>
                            ⏳ Pendiente
                        </option>
                        <option value="Confirmada" <?= $cita->getEstado() == 'Confirmada' ? 'selected' : '' ?>>
                            ✅ Confirmada
                        </option>
                        <option value="Cancelada" <?= $cita->getEstado() == 'Cancelada' ? 'selected' : '' ?>>
                            ❌ Cancelada
                        </option>
                    </select>
                    <small class="text-muted" style="font-size: 0.85rem; margin-top: 0.5rem; display: block;">
                        📋 Estado actual: <strong><?= htmlspecialchars($cita->getEstado()) ?></strong>
                    </small>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="btn-actions">
            <a href="?accion=gestionarCitas" class="btn-modern btn-secondary-modern">
                <span>↩️</span>
                Cancelar
            </a>
            <button type="submit" class="btn-modern btn-primary-modern">
                <span>💾</span>
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const estadoSelect = document.getElementById('estado');
    const estadoActual = '<?= $cita->getEstado() ?>';

    /* ------------------------------------------------------
       1️⃣ Confirmación al cambiar a CANCELADA
    --------------------------------------------------------- */
    estadoSelect.addEventListener('change', function () {
        if (this.value !== estadoActual && this.value === 'Cancelada') {

            Swal.fire({
                title: "¿Cancelar esta cita?",
                text: "El paciente será notificado de esta cancelación.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, cancelar",
                cancelButtonText: "No, volver",
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6"
            }).then((result) => {
                if (!result.isConfirmed) {
                    this.value = estadoActual;
                }
            });
        }
    });

    /* ------------------------------------------------------
       2️⃣ Validación + Confirmación antes de guardar
    --------------------------------------------------------- */
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const paciente = document.getElementById('paciente_id').value;
        const doctor = document.getElementById('doctor_id').value;
        const fecha = document.getElementById('fecha').value;
        const estado = document.getElementById('estado').value;

        if (!paciente || !doctor || !fecha || !estado) {
            Swal.fire({
                icon: "error",
                title: "Campos incompletos",
                text: "Por favor complete todos los campos obligatorios.",
                confirmButtonColor: "#d33"
            });
            return;
        }

        Swal.fire({
            title: "¿Guardar cambios?",
            text: "Se actualizará toda la información de esta cita.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Sí, guardar",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#f5576c",
            cancelButtonColor: "#6c757d",
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });


    /* ------------------------------------------------------
       3️⃣ Confirmación SweetAlert al hacer clic en CANCELAR
    --------------------------------------------------------- */
    const btnCancelar = document.querySelector('.btn-secondary-modern');

    btnCancelar.addEventListener('click', function (e) {
        e.preventDefault();

        Swal.fire({
            title: "¿Salir sin guardar?",
            text: "Los cambios realizados no se guardarán.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, salir",
            cancelButtonText: "Volver",
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "?accion=gestionarCitas";
            }
        }); 
    });

});
</script>
