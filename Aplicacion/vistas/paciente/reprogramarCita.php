<?php
// =====================
// 🔄 Vista: Reprogramar Cita del Paciente
// =====================
$cita = $cita ?? null;
$doctores = $doctores ?? [];

if (!$cita): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i>
        Cita no encontrada
    </div>
    <?php return; endif;
?>

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --primary: #667eea;
        --primary-dark: #764ba2;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
    }

    * {
        font-family: 'Inter', sans-serif;
    }

    .reprogramar-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .page-header {
        background: linear-gradient(135deg, var(--warning) 0%, #f97316 100%);
        color: white;
        padding: 2.5rem;
        border-radius: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        animation: fadeInUp 0.6s ease;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .header-content {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-title {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .header-title h1 {
        font-family: 'Poppins', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        margin: 0;
    }

    .header-icon {
        font-size: 3rem;
    }

    .btn-back {
        background: white;
        color: var(--warning);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    .card {
        background: white;
        border-radius: 1.25rem;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        animation: fadeInUp 0.6s ease;
    }

    .card-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #0f172a;
    }

    .alert-info {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border: 2px solid #93c5fd;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: start;
        gap: 1rem;
    }

    .alert-icon {
        font-size: 2rem;
        color: #1e40af;
    }

    .alert-content {
        flex: 1;
    }

    .alert-title {
        font-weight: 700;
        color: #1e40af;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }

    .alert-text {
        color: #1e3a8a;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .cita-actual {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 2px solid #fcd34d;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .cita-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #92400e;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .cita-valor {
        font-family: 'Poppins', sans-serif;
        font-size: 1.2rem;
        font-weight: 700;
        color: #78350f;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label .required {
        color: var(--danger);
    }

    .form-input,
    .form-select {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: white;
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-input:disabled,
    .form-select:disabled {
        background: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
    }

    .doctor-option {
        padding: 1rem;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        margin-bottom: 0.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .doctor-option:hover {
        background: #f0f4ff;
        border-color: var(--primary);
        transform: translateX(5px);
    }

    .doctor-option input[type="radio"] {
        margin-right: 1rem;
    }

    .doctor-info-inline {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .doctor-name {
        font-weight: 700;
        color: #0f172a;
    }

    .doctor-especialidad {
        font-size: 0.85rem;
        color: #64748b;
    }

    .horarios-disponibles {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .horario-btn {
        padding: 0.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        font-weight: 600;
        font-size: 0.9rem;
        color: #475569;
    }

    .horario-btn:hover {
        background: #f0f4ff;
        border-color: var(--primary);
        color: var(--primary);
    }

    .horario-btn.selected {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border-color: var(--primary);
        color: white;
    }

    .horario-btn.disabled {
        opacity: 0.4;
        cursor: not-allowed;
        background: #f1f5f9;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 2px solid #f1f5f9;
    }

    .btn-submit {
        flex: 1;
        padding: 1rem 2rem;
        border-radius: 0.75rem;
        border: none;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
    }

    .comparison-table {
        width: 100%;
        margin-top: 1rem;
    }

    .comparison-row {
        display: grid;
        grid-template-columns: 120px 1fr 1fr;
        gap: 1rem;
        padding: 0.75rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .comparison-row:last-child {
        border-bottom: none;
    }

    .comparison-label {
        font-weight: 600;
        color: #64748b;
        font-size: 0.85rem;
    }

    .comparison-old {
        color: #ef4444;
        text-decoration: line-through;
        opacity: 0.7;
    }

    .comparison-new {
        color: #10b981;
        font-weight: 700;
    }

    @media (max-width: 968px) {
        .content-grid {
            grid-template-columns: 1fr;
        }

        .reprogramar-container {
            padding: 1rem;
        }

        .header-content {
            flex-direction: column;
            gap: 1rem;
        }

        .horarios-disponibles {
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>

<div class="reprogramar-container">
    <!-- HEADER -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-title">
                <span class="header-icon">🔄</span>
                <div>
                    <h1>Reprogramar Cita</h1>
                    <div style="font-size: 0.9rem; opacity: 0.9; margin-top: 0.5rem;">
                        Cita #<?= str_pad($cita['id'], 4, '0', STR_PAD_LEFT) ?>
                    </div>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/index.php?accion=verDetalleCita&id=<?= $cita['id'] ?>" class="btn-back">
                <i class="bi bi-arrow-left"></i>
                Volver
            </a>
        </div>
    </div>

    <!-- ALERTA INFORMATIVA -->
    <div class="alert-info">
        <div class="alert-icon">ℹ️</div>
        <div class="alert-content">
            <div class="alert-title">Importante antes de reprogramar</div>
            <div class="alert-text">
                • La cita actual será cancelada automáticamente<br>
                • Podrás elegir una nueva fecha y hora disponible<br>
                • Recibirás una confirmación por correo electrónico<br>
                • Si tienes dudas, contacta con nosotros
            </div>
        </div>
    </div>

    <div class="content-grid">
        <!-- COLUMNA IZQUIERDA: DATOS ACTUALES -->
        <div>
            <div class="card">
                <h2 class="card-title">
                    <i class="bi bi-calendar-x"></i>
                    Cita Actual
                </h2>

                <div class="cita-actual">
                    <div style="margin-bottom: 1rem;">
                        <div class="cita-label">📅 Fecha y Hora</div>
                        <div class="cita-valor">
                            <i class="bi bi-calendar-event"></i>
                            <?= date('d/m/Y', strtotime($cita['fecha'])) ?>
                            <span style="margin: 0 0.5rem;">•</span>
                            <i class="bi bi-clock"></i>
                            <?= date('h:i A', strtotime($cita['fecha'])) ?>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <div class="cita-label">👨‍⚕️ Doctor</div>
                        <div class="cita-valor">
                            Dr. <?= htmlspecialchars($cita['doctor_nombre']) ?>
                        </div>
                    </div>

                    <div>
                        <div class="cita-label">🏥 Especialidad</div>
                        <div class="cita-valor">
                            <?= htmlspecialchars($cita['especialidad']) ?>
                        </div>
                    </div>
                </div>

                <div style="background: #fef2f2; border: 2px solid #fecaca; border-radius: 0.75rem; padding: 1rem; margin-top: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; color: #991b1b;">
                        <i class="bi bi-exclamation-triangle" style="font-size: 1.5rem;"></i>
                        <div>
                            <strong>Esta cita será cancelada</strong><br>
                            <small>Al confirmar la reprogramación</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: NUEVA CITA -->
        <div>
            <div class="card">
                <h2 class="card-title">
                    <i class="bi bi-calendar-plus"></i>
                    Nueva Fecha y Hora
                </h2>

                <form method="POST" action="<?= BASE_URL ?>/index.php?accion=guardarReprogramacion" id="formReprogramar">
                    <input type="hidden" name="cita_id" value="<?= $cita['id'] ?>">
                    <input type="hidden" name="paciente_id" value="<?= $cita['paciente_id'] ?>">

                    <!-- Doctor (opcional: puede cambiar) -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-person-badge"></i>
                            Doctor
                        </label>
                        <select name="doctor_id" id="doctorSelect" class="form-select" required>
                            <option value="<?= $cita['doctor_id'] ?>" selected>
                                Dr. <?= htmlspecialchars($cita['doctor_nombre']) ?> - <?= htmlspecialchars($cita['especialidad']) ?>
                            </option>
                            <?php foreach ($doctores as $doctor): ?>
                                <?php if ($doctor['id'] != $cita['doctor_id']): ?>
                                <option value="<?= $doctor['id'] ?>">
                                    Dr. <?= htmlspecialchars($doctor['nombre']) ?> - <?= htmlspecialchars($doctor['especialidad'] ?? 'Medicina General') ?>
                                </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <small style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem; display: block;">
                            💡 Puedes mantener el mismo doctor o elegir otro
                        </small>
                    </div>

                    <!-- Nueva Fecha -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-calendar"></i>
                            Nueva Fecha <span class="required">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="fecha" 
                            id="fechaInput" 
                            class="form-input" 
                            min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                            max="<?= date('Y-m-d', strtotime('+90 days')) ?>"
                            required
                        >
                        <small style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem; display: block;">
                            📅 Puedes agendar hasta 90 días en adelante
                        </small>
                    </div>

                    <!-- Horarios Disponibles -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-clock"></i>
                            Horario <span class="required">*</span>
                        </label>
                        <div class="horarios-disponibles" id="horariosContainer">
                            <div style="grid-column: 1 / -1; text-align: center; color: #94a3b8; padding: 2rem;">
                                Selecciona una fecha para ver horarios disponibles
                            </div>
                        </div>
                        <input type="hidden" name="hora" id="horaSeleccionada" required>
                    </div>

                    <!-- Resumen de Cambios -->
                    <div style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; margin: 1.5rem 0;">
                        <div style="font-weight: 700; margin-bottom: 1rem; color: #0f172a;">
                            📊 Resumen de Cambios
                        </div>
                        <div class="comparison-table">
                            <div class="comparison-row">
                                <div class="comparison-label">Fecha:</div>
                                <div class="comparison-old"><?= date('d/m/Y', strtotime($cita['fecha'])) ?></div>
                                <div class="comparison-new" id="nuevaFechaDisplay">-</div>
                            </div>
                            <div class="comparison-row">
                                <div class="comparison-label">Hora:</div>
                                <div class="comparison-old"><?= date('h:i A', strtotime($cita['fecha'])) ?></div>
                                <div class="comparison-new" id="nuevaHoraDisplay">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="form-actions">
                        <button type="button" class="btn-submit btn-secondary" onclick="window.history.back()">
                            <i class="bi bi-x-circle"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn-submit btn-primary">
                            <i class="bi bi-check-circle"></i>
                            Confirmar Reprogramación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Horarios disponibles por defecto
const horariosDisponibles = [
    '08:00', '08:30', '09:00', '09:30', '10:00', '10:30',
    '11:00', '11:30', '14:00', '14:30', '15:00', '15:30',
    '16:00', '16:30', '17:00', '17:30'
];

// Evento: cambio de fecha
document.getElementById('fechaInput')?.addEventListener('change', function(e) {
    const fecha = e.target.value;
    if (fecha) {
        cargarHorariosDisponibles(fecha);
        actualizarResumen();
    }
});

// Cargar horarios disponibles
function cargarHorariosDisponibles(fecha) {
    const container = document.getElementById('horariosContainer');
    container.innerHTML = '';

    horariosDisponibles.forEach(hora => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'horario-btn';
        btn.textContent = formatearHora(hora);
        btn.dataset.hora = hora;
        
        btn.addEventListener('click', function() {
            document.querySelectorAll('.horario-btn').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('horaSeleccionada').value = hora;
            actualizarResumen();
        });
        
        container.appendChild(btn);
    });
}

// Formatear hora de 24h a 12h
function formatearHora(hora) {
    const [h, m] = hora.split(':');
    const hour = parseInt(h);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const hour12 = hour % 12 || 12;
    return `${hour12}:${m} ${ampm}`;
}

// Actualizar resumen de cambios
function actualizarResumen() {
    const fecha = document.getElementById('fechaInput').value;
    const hora = document.getElementById('horaSeleccionada').value;

    if (fecha) {
        const fechaObj = new Date(fecha + 'T00:00:00');
        const opciones = { day: '2-digit', month: '2-digit', year: 'numeric' };
        document.getElementById('nuevaFechaDisplay').textContent = 
            fechaObj.toLocaleDateString('es-ES', opciones);
    }

    if (hora) {
        document.getElementById('nuevaHoraDisplay').textContent = formatearHora(hora);
    }
}

// ===============================
//    VALIDACIÓN + SWEETALERT2
// ===============================
document.getElementById('formReprogramar')?.addEventListener('submit', function (e) {
    e.preventDefault(); // Detener envío temporal

    const hora = document.getElementById('horaSeleccionada').value;

    // Validar hora seleccionada
    if (!hora) {
        Swal.fire({
            icon: 'warning',
            title: 'Horario requerido',
            text: 'Por favor selecciona un horario antes de continuar.',
            confirmButtonColor: '#F59E0B'
        });
        return;
    }

    // SweetAlert de confirmación
    Swal.fire({
        title: '¿Reprogramar cita?',
        html: `
            <p style="font-size: 15px; margin-top: 10px;">
                La cita actual será <b>cancelada</b> y se creará una nueva con la fecha y hora seleccionadas.
            </p>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, reprogramar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#6366F1',
        cancelButtonColor: '#EF4444'
    }).then(result => {
        if (result.isConfirmed) {

            // Mostrar cargando
            Swal.fire({
                title: 'Reprogramando...',
                text: 'Por favor espera un momento',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            // Enviar formulario después del loading
            document.getElementById('formReprogramar').submit();
        }
    });
});
</script>
