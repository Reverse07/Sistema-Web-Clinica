<style>
    .crear-cita-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }

    .header-cita {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2.5rem;
        border-radius: 20px 20px 0 0;
        margin-bottom: 0;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .header-cita h2 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .header-cita p {
        margin: 0.5rem 0 0 0;
        opacity: 0.95;
        font-size: 1rem;
    }

    .form-cita-card {
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
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        background-color: white;
        outline: none;
    }

    .form-control-modern:hover,
    .form-select-modern:hover {
        border-color: #cbd5e0;
        background-color: white;
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
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

    .icon-input {
        font-size: 1.2rem;
    }

    .required-asterisk {
        color: #ef4444;
        margin-left: 0.25rem;
    }

    @media (max-width: 768px) {
        .form-cita-card {
            padding: 1.5rem;
        }

        .header-cita {
            padding: 1.5rem;
        }

        .header-cita h2 {
            font-size: 1.5rem;
        }

        .btn-actions {
            flex-direction: column-reverse;
        }

        .btn-modern {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="crear-cita-container">
    <!-- Header -->
    <div class="header-cita">
        <h2>
            <span>🩺</span>
            Registrar Nueva Cita
        </h2>
        <p>Complete el formulario para agendar una nueva cita médica</p>
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
    <form action="?accion=guardarCita" method="POST" class="form-cita-card">
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
                            <option value="<?= $p['id'] ?>">
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
                            <option value="<?= $d['id'] ?>">
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
                        Fecha y hora
                        <span class="required-asterisk">*</span>
                    </label>
                    <input 
                        type="datetime-local" 
                        class="form-control form-control-modern" 
                        id="fecha" 
                        name="fecha" 
                        min="<?= date('Y-m-d\TH:i') ?>"
                        required
                    >
                </div>
            </div>

            <!-- Estado -->
            <div class="col-md-6">
                <div class="form-group-modern">
                    <label for="estado" class="form-label-modern">
                        <span class="icon-input">📌</span>
                        Estado inicial
                        <span class="required-asterisk">*</span>
                    </label>
                    <select class="form-select form-select-modern" id="estado" name="estado" required>
                        <option value="Pendiente" selected>⏳ Pendiente</option>
                        <option value="Confirmada">✅ Confirmada</option>
                        <option value="Cancelada">❌ Cancelada</option>
                    </select>
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
                Guardar Cita
            </button>
        </div>
    </form>
</div>

<script>
// Establecer fecha mínima como hoy
document.addEventListener('DOMContentLoaded', function() {
    const fechaInput = document.getElementById('fecha');
    if (fechaInput) {
        // Establecer fecha por defecto: mañana a las 9:00 AM
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow.setHours(9, 0, 0, 0);
        
        const year = tomorrow.getFullYear();
        const month = String(tomorrow.getMonth() + 1).padStart(2, '0');
        const day = String(tomorrow.getDate()).padStart(2, '0');
        const hours = String(tomorrow.getHours()).padStart(2, '0');
        const minutes = String(tomorrow.getMinutes()).padStart(2, '0');
        
        fechaInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
    }
});
</script>