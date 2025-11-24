<?php
// =====================
// 📅 Vista: Crear Nueva Cita (Paciente)
// =====================
$doctores = $doctores ?? [];
?>

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@600;700;800;900&display=swap" rel="stylesheet">

<style>
    :root {
        --primary: #667eea;
        --primary-dark: #764ba2;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
        --light-bg: #f8fafc;
        --dark-text: #0f172a;
        --shadow-sm: 0 4px 15px rgba(0, 0, 0, 0.08);
        --shadow-md: 0 12px 30px rgba(0, 0, 0, 0.15);
        --shadow-lg: 0 20px 50px rgba(0, 0, 0, 0.2);
    }

    * {
        font-family: 'Inter', sans-serif;
    }

    html, body {
        background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
        min-height: 100vh;
    }

    .crear-cita-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem;
    }

    /* ========================
       ANIMACIONES
       ======================== */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-30px); }
        to { opacity: 1; transform: translateX(0); }
    }

    /* ========================
       HEADER
       ======================== */
    .page-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 3rem 2.5rem;
        border-radius: 1.5rem;
        margin-bottom: 2.5rem;
        box-shadow: var(--shadow-lg);
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.6s ease;
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
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0;
    }

    .header-icon {
        font-size: 3rem;
    }

    .btn-back {
        background: white;
        color: var(--primary);
        border: none;
        padding: 0.875rem 1.75rem;
        border-radius: 1rem;
        font-weight: 700;
        font-size: 0.95rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        color: var(--primary-dark);
    }

    /* ========================
       FORMULARIO
       ======================== */
    .form-card {
        background: white;
        border-radius: 1.5rem;
        padding: 3rem;
        box-shadow: var(--shadow-md);
        animation: fadeInUp 0.6s ease 0.2s backwards;
    }

    .form-section {
        margin-bottom: 2.5rem;
    }

    .section-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark-text);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .section-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
    }

    .required {
        color: var(--danger);
        font-weight: 700;
    }

    .form-control {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8fafc;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        background: white;
    }

    .form-control:disabled {
        background: #f1f5f9;
        cursor: not-allowed;
    }

    /* ========================
       SELECTOR DE DOCTOR
       ======================== */
    .doctor-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.25rem;
        margin-top: 1rem;
    }

    .doctor-card {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 1.25rem;
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .doctor-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .doctor-card:hover {
        border-color: var(--primary);
        box-shadow: var(--shadow-sm);
        transform: translateY(-4px);
    }

    .doctor-card:hover::before {
        transform: scaleX(1);
    }

    .doctor-card.selected {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.05) 100%);
        border-color: var(--primary);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.2);
    }

    .doctor-card.selected::before {
        transform: scaleX(1);
    }

    .doctor-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .doctor-avatar {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.75rem;
        font-weight: 700;
        box-shadow: 0 6px 15px rgba(102, 126, 234, 0.3);
    }

    .doctor-info h4 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--dark-text);
        margin: 0 0 0.25rem 0;
    }

    .doctor-especialidad {
        font-size: 0.85rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .check-icon {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 32px;
        height: 32px;
        background: var(--success);
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .doctor-card.selected .check-icon {
        display: flex;
    }

    /* ========================
       FECHA Y HORA
       ======================== */
    .datetime-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .input-icon {
        position: relative;
    }

    .input-icon i {
        position: absolute;
        left: 1.25rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary);
        font-size: 1.25rem;
        pointer-events: none;
    }

    .input-icon .form-control {
        padding-left: 3.25rem;
    }

    /* ========================
       INFO BOX
       ======================== */
    .info-box {
        background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);
        border-left: 4px solid var(--info);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .info-box-title {
        font-weight: 700;
        color: var(--info);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-box-text {
        color: #334155;
        font-size: 0.95rem;
        margin: 0;
        line-height: 1.6;
    }

    /* ========================
       RESUMEN
       ======================== */
    .summary-card {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-radius: 1.25rem;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .summary-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--dark-text);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 0.875rem 0;
        border-bottom: 1px solid #cbd5e1;
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-label {
        color: #64748b;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .summary-value {
        color: var(--dark-text);
        font-weight: 700;
        text-align: right;
    }

    /* ========================
       BOTONES
       ======================== */
    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2.5rem;
    }

    .btn {
        flex: 1;
        padding: 1.25rem 2rem;
        border: none;
        border-radius: 1rem;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: white;
        color: #64748b;
        border: 2px solid #e2e8f0;
    }

    .btn-secondary:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateY(-2px);
    }

    /* ========================
       RESPONSIVE
       ======================== */
    @media (max-width: 768px) {
        .crear-cita-container {
            padding: 1rem;
        }

        .page-header {
            padding: 2rem 1.5rem;
        }

        .header-content {
            flex-direction: column;
            gap: 1.5rem;
        }

        .header-title {
            justify-content: center;
        }

        .header-title h1 {
            font-size: 1.8rem;
        }

        .form-card {
            padding: 2rem 1.5rem;
        }

        .doctor-grid {
            grid-template-columns: 1fr;
        }

        .datetime-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }
    }
</style>

<div class="crear-cita-container">

    <!-- HEADER -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-title">
                <span class="header-icon">➕</span>
                <h1>Nueva Cita Médica</h1>
            </div>
            <a href="<?= BASE_URL ?>/index.php?accion=misCitas" class="btn-back">
                <i class="bi bi-arrow-left"></i>
                Volver a Mis Citas
            </a>
        </div>
    </div>

    <!-- FORMULARIO -->
    <div class="form-card">
        
        <!-- INFO BOX -->
        <div class="info-box">
            <div class="info-box-title">
                <i class="bi bi-info-circle-fill"></i>
                Información Importante
            </div>
            <p class="info-box-text">
                Por favor, selecciona un doctor disponible y elige la fecha y hora de tu preferencia. 
                Recibirás una confirmación una vez que el doctor apruebe tu solicitud.
            </p>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/index.php?accion=guardarCitaPaciente" id="formCita">
            
            <!-- SECCIÓN: SELECCIONAR DOCTOR -->
            <div class="form-section">
                <div class="section-title">
                    <span class="section-icon">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    Selecciona tu Doctor
                </div>

                <input type="hidden" name="doctor_id" id="doctor_id" required>

                <?php if (empty($doctores)): ?>
                    <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); 
                                border-left: 4px solid var(--warning); 
                                border-radius: 1rem; 
                                padding: 1.5rem; 
                                display: flex; 
                                align-items: center; 
                                gap: 1rem;">
                        <i class="bi bi-exclamation-triangle" style="font-size: 2rem; color: var(--warning);"></i>
                        <div>
                            <strong style="color: #92400e;">No hay doctores disponibles</strong>
                            <p style="margin: 0.25rem 0 0 0; color: #78350f;">
                                Por favor, intenta más tarde o contacta con administración.
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="doctor-grid">
                        <?php foreach ($doctores as $doctor): ?>
                            <div class="doctor-card" 
                                 onclick="selectDoctor(<?= $doctor['id'] ?>, '<?= htmlspecialchars($doctor['nombre'], ENT_QUOTES) ?>', '<?= htmlspecialchars($doctor['especialidad'] ?? 'Medicina General', ENT_QUOTES) ?>')"
                                 data-doctor-id="<?= $doctor['id'] ?>"
                                 data-doctor-nombre="<?= htmlspecialchars($doctor['nombre']) ?>"
                                 data-doctor-especialidad="<?= htmlspecialchars($doctor['especialidad'] ?? 'Medicina General') ?>">
                                <div class="doctor-header">
                                    <div class="doctor-avatar">
                                        <?= strtoupper(substr($doctor['nombre'], 0, 2)) ?>
                                    </div>
                                    <div class="doctor-info">
                                        <h4>Dr. <?= htmlspecialchars($doctor['nombre']) ?></h4>
                                        <div class="doctor-especialidad">
                                            <i class="bi bi-heart-pulse"></i>
                                            <?= htmlspecialchars($doctor['especialidad'] ?? 'Medicina General') ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($doctor['numero_colegiatura'])): ?>
                                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.5rem;">
                                        <i class="bi bi-award"></i>
                                        CMP: <?= htmlspecialchars($doctor['numero_colegiatura']) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="check-icon">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- SECCIÓN: FECHA Y HORA -->
            <div class="form-section">
                <div class="section-title">
                    <span class="section-icon">
                        <i class="bi bi-calendar-check"></i>
                    </span>
                    Fecha y Hora de la Cita
                </div>

                <div class="datetime-grid">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-calendar3"></i>
                            Fecha <span class="required">*</span>
                        </label>
                        <div class="input-icon">
                            <i class="bi bi-calendar-event"></i>
                            <input type="date" 
                                   name="fecha" 
                                   id="fecha" 
                                   class="form-control" 
                                   required
                                   min="<?= date('Y-m-d') ?>"
                                   onchange="updateSummary()">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-clock"></i>
                            Hora <span class="required">*</span>
                        </label>
                        <div class="input-icon">
                            <i class="bi bi-alarm"></i>
                            <input type="time" 
                                   name="hora" 
                                   id="hora" 
                                   class="form-control" 
                                   required
                                   min="08:00"
                                   max="18:00"
                                   onchange="updateSummary()">
                        </div>
                    </div>
                </div>

                <small style="color: #64748b; display: block; margin-top: 0.5rem;">
                    <i class="bi bi-info-circle"></i>
                    Horario de atención: Lunes a Viernes de 8:00 AM a 6:00 PM
                </small>
            </div>

            <!-- SECCIÓN: MOTIVO (OPCIONAL) -->
            <div class="form-section">
                <div class="section-title">
                    <span class="section-icon">
                        <i class="bi bi-file-text"></i>
                    </span>
                    Motivo de Consulta (Opcional)
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-pencil"></i>
                        Describe brevemente el motivo de tu consulta
                    </label>
                    <textarea name="motivo" 
                              id="motivo" 
                              class="form-control" 
                              rows="4"
                              placeholder="Ej: Dolor de cabeza persistente, chequeo general, control de presión, etc."
                              style="resize: vertical;"></textarea>
                </div>
            </div>

            <!-- RESUMEN -->
            <div class="summary-card" id="summaryCard" style="display: none;">
                <div class="summary-title">
                    <i class="bi bi-clipboard-check"></i>
                    Resumen de tu Cita
                </div>
                <div class="summary-item">
                    <span class="summary-label">Doctor:</span>
                    <span class="summary-value" id="summaryDoctor">-</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Especialidad:</span>
                    <span class="summary-value" id="summaryEspecialidad">-</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Fecha:</span>
                    <span class="summary-value" id="summaryFecha">-</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Hora:</span>
                    <span class="summary-value" id="summaryHora">-</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Estado:</span>
                    <span class="summary-value" style="color: var(--warning);">
                        <i class="bi bi-clock-history"></i> Pendiente de confirmación
                    </span>
                </div>
            </div>

            <!-- BOTONES -->
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                    <i class="bi bi-x-circle"></i>
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>
                    <i class="bi bi-calendar-plus"></i>
                    Agendar Cita
                </button>
            </div>

        </form>
    </div>

</div>

<script>
// Almacenar datos de doctores
const doctoresData = <?= json_encode($doctores) ?>;
let selectedDoctorId = null;
let selectedDoctorNombre = '';
let selectedDoctorEspecialidad = '';

// Seleccionar doctor
function selectDoctor(doctorId, doctorNombre, doctorEspecialidad) {
    // Remover selección anterior
    document.querySelectorAll('.doctor-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Seleccionar nuevo doctor
    const selectedCard = document.querySelector(`[data-doctor-id="${doctorId}"]`);
    if (selectedCard) {
        selectedCard.classList.add('selected');
    }
    
    // Guardar datos del doctor seleccionado
    selectedDoctorId = doctorId;
    selectedDoctorNombre = doctorNombre;
    selectedDoctorEspecialidad = doctorEspecialidad;
    
    document.getElementById('doctor_id').value = doctorId;
    
    // Actualizar resumen
    updateSummary();
    validateForm();
}

// Actualizar resumen
function updateSummary() {
    const doctorId = document.getElementById('doctor_id').value;
    const fecha = document.getElementById('fecha').value;
    const hora = document.getElementById('hora').value;
    
    if (doctorId && fecha && hora) {
        // Usar los datos almacenados del doctor seleccionado
        if (selectedDoctorNombre && selectedDoctorEspecialidad) {
            document.getElementById('summaryDoctor').textContent = 'Dr. ' + selectedDoctorNombre;
            document.getElementById('summaryEspecialidad').textContent = selectedDoctorEspecialidad;
        }
        
        // Formatear fecha
        const fechaObj = new Date(fecha + 'T00:00:00');
        const fechaFormateada = fechaObj.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        document.getElementById('summaryFecha').textContent = fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);
        
        // Formatear hora
        const [hours, minutes] = hora.split(':');
        const horaFormateada = new Date(2000, 0, 1, hours, minutes).toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
        document.getElementById('summaryHora').textContent = horaFormateada;
        
        document.getElementById('summaryCard').style.display = 'block';
    }
    
    validateForm();
}

// Validar formulario
function validateForm() {
    const doctorId = document.getElementById('doctor_id').value;
    const fecha = document.getElementById('fecha').value;
    const hora = document.getElementById('hora').value;
    const btnSubmit = document.getElementById('btnSubmit');
    
    if (doctorId && fecha && hora) {
        btnSubmit.disabled = false;
    } else {
        btnSubmit.disabled = true;
    }
}

// Validar hora dentro del rango permitido
document.getElementById('hora')?.addEventListener('change', function() {
    const hora = this.value;
    const [hours] = hora.split(':');
    
    if (parseInt(hours) < 8 || parseInt(hours) >= 18) {
        alert('Por favor, selecciona una hora entre 8:00 AM y 6:00 PM');
        this.value = '';
    }
});

// Combinar fecha y hora antes de enviar
document.getElementById('formCita')?.addEventListener('submit', function(e) {
    const fecha = document.getElementById('fecha').value;
    const hora = document.getElementById('hora').value;
    
    // Crear input hidden con fecha y hora combinadas
    const fechaHoraInput = document.createElement('input');
    fechaHoraInput.type = 'hidden';
    fechaHoraInput.name = 'fecha';
    fechaHoraInput.value = `${fecha} ${hora}:00`;
    
    // Remover campos originales del envío
    document.getElementById('fecha').removeAttribute('name');
    document.getElementById('hora').removeAttribute('name');
    
    this.appendChild(fechaHoraInput);
});

// Inicializar validación
document.getElementById('fecha')?.addEventListener('change', updateSummary);
document.getElementById('hora')?.addEventListener('change', updateSummary);
</script>