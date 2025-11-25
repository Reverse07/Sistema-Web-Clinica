<?php
// =====================
// ⚙️ Vista: Configuración del Paciente PREMIUM
// =====================
$nombrePaciente = $_SESSION['nombre'] ?? 'Paciente';
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
    }

    * {
        font-family: 'Inter', sans-serif;
    }

    html, body {
        background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
        min-height: 100vh;
    }

    body {
        padding: 2rem 0;
        color: var(--dark-text);
    }

    .config-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    /* ========================
       ANIMACIONES
       ======================== */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fade-in-up { animation: fadeInUp 0.6s ease forwards; }

    /* ========================
       HEADER
       ======================== */
    .page-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 3rem 2.5rem;
        border-radius: 2rem;
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-md);
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 500px;
        height: 500px;
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

    .header-icon {
        font-size: 4rem;
    }

    .header-title h1 {
        font-family: 'Poppins', sans-serif;
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0 0 0.5rem 0;
    }

    .header-title p {
        font-size: 1.1rem;
        opacity: 0.95;
        margin: 0;
    }

    .btn-back {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255,255,255,0.3);
        color: white;
        padding: 1rem 2rem;
        border-radius: 1rem;
        text-decoration: none;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background: white;
        color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    /* ========================
       TABS
       ======================== */
    .tabs-container {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        box-shadow: var(--shadow-sm);
        margin-bottom: 2rem;
    }

    .nav-tabs {
        border: none;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .nav-tabs .nav-link {
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1rem 2rem;
        color: var(--dark-text);
        font-weight: 600;
        transition: all 0.3s ease;
        background: white;
    }

    .nav-tabs .nav-link:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
    }

    .nav-tabs .nav-link.active {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border-color: var(--primary);
    }

    /* ========================
       SECTION CARDS
       ======================== */
    .section-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2.5rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-sm);
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .section-card:hover {
        border-color: var(--primary);
        box-shadow: var(--shadow-md);
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #f1f5f9;
    }

    .section-icon {
        width: 60px;
        height: 60px;
        border-radius: 1rem;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
    }

    .section-title h3 {
        font-family: 'Poppins', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 0.25rem 0;
        color: var(--dark-text);
    }

    .section-title p {
        font-size: 0.95rem;
        color: #64748b;
        margin: 0;
    }

    /* ========================
       INFO BOX
       ======================== */
    .info-box {
        background: var(--light-bg);
        border-left: 4px solid var(--info);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .info-box h4 {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0 0 1rem 0;
        color: var(--dark-text);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.95rem;
    }

    .info-item i {
        color: var(--primary);
        font-size: 1.25rem;
    }

    .info-item strong {
        color: var(--dark-text);
        margin-right: 0.5rem;
    }

    /* ========================
       FORM ELEMENTS
       ======================== */
    .form-section {
        margin-bottom: 2rem;
    }

    .form-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--dark-text);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .switch-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem;
        background: var(--light-bg);
        border-radius: 1rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .switch-container:hover {
        background: #e2e8f0;
    }

    .switch-info h5 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0 0 0.25rem 0;
        color: var(--dark-text);
    }

    .switch-info p {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0;
    }

    /* Custom Switch */
    .custom-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .custom-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }

    /* ========================
       BUTTONS
       ======================== */
    .btn-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn {
        padding: 1rem 2.5rem;
        border-radius: 1rem;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: var(--light-bg);
        color: var(--dark-text);
        border: 2px solid #e2e8f0;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
    }

    /* ========================
       ALERTS
       ======================== */
    .alert {
        padding: 1.25rem 1.5rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        font-weight: 500;
        animation: fadeInUp 0.4s ease;
    }

    .alert i {
        font-size: 1.5rem;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 2px solid #10b981;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 2px solid #ef4444;
    }

    /* ========================
       RESPONSIVE
       ======================== */
    @media (max-width: 768px) {
        .config-container {
            padding: 0 1rem;
        }

        .page-header {
            padding: 2rem 1.5rem;
        }

        .header-content {
            flex-direction: column;
            gap: 1.5rem;
            text-align: center;
        }

        .header-title {
            flex-direction: column;
        }

        .header-title h1 {
            font-size: 1.75rem;
        }

        .nav-tabs {
            flex-direction: column;
        }

        .btn-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="config-container">

    <!-- HEADER -->
    <div class="page-header animate-fade-in-up">
        <div class="header-content">
            <div class="header-title">
                <div class="header-icon">⚙️</div>
                <div>
                    <h1>Configuración</h1>
                    <p>Personaliza tu experiencia</p>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/index.php?accion=dashboardPaciente" class="btn-back">
                <i class="bi bi-arrow-left"></i>
                Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- MENSAJES -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['mensaje']['tipo'] === 'exito' ? 'success' : 'error' ?>">
            <i class="bi bi-<?= $_SESSION['mensaje']['tipo'] === 'exito' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"></i>
            <span><?= htmlspecialchars($_SESSION['mensaje']['texto']) ?></span>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <!-- TABS -->
    <div class="tabs-container">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#info">
                    <i class="bi bi-info-circle"></i> Información
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#notificaciones">
                    <i class="bi bi-bell"></i> Notificaciones
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#privacidad">
                    <i class="bi bi-shield-check"></i> Privacidad
                </a>
            </li>
        </ul>

        <div class="tab-content">
            
            <!-- TAB: INFORMACIÓN DE LA CLÍNICA -->
            <div class="tab-pane fade show active" id="info">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="bi bi-hospital"></i>
                        </div>
                        <div class="section-title">
                            <h3>Información de la Clínica</h3>
                            <p>Datos de contacto y horarios de atención</p>
                        </div>
                    </div>

                    <div class="info-box">
                        <h4><i class="bi bi-building"></i> Clínica Salud Total</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span><strong>Dirección:</strong> Av. Principal 123, Lima</span>
                            </div>
                            <div class="info-item">
                                <i class="bi bi-telephone-fill"></i>
                                <span><strong>Teléfono:</strong> +51 999 888 777</span>
                            </div>
                            <div class="info-item">
                                <i class="bi bi-envelope-fill"></i>
                                <span><strong>Email:</strong> contacto@clinica.com</span>
                            </div>
                            <div class="info-item">
                                <i class="bi bi-card-text"></i>
                                <span><strong>RUC:</strong> 20123456789</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-box" style="border-left-color: var(--success);">
                        <h4><i class="bi bi-clock-fill"></i> Horarios de Atención</h4>
                        <div style="line-height: 2;">
                            <p><strong>Lunes a Viernes:</strong> 8:00 AM - 8:00 PM</p>
                            <p><strong>Sábados:</strong> 9:00 AM - 2:00 PM</p>
                            <p><strong>Domingos:</strong> Cerrado</p>
                        </div>
                    </div>

                    <div class="info-box" style="border-left-color: var(--warning);">
                        <h4><i class="bi bi-calendar-event"></i> Servicios Disponibles</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <i class="bi bi-check-circle-fill" style="color: var(--success);"></i>
                                <span>Consultas Médicas</span>
                            </div>
                            <div class="info-item">
                                <i class="bi bi-check-circle-fill" style="color: var(--success);"></i>
                                <span>Laboratorio Clínico</span>
                            </div>
                            <div class="info-item">
                                <i class="bi bi-check-circle-fill" style="color: var(--success);"></i>
                                <span>Farmacia</span>
                            </div>
                            <div class="info-item">
                                <i class="bi bi-check-circle-fill" style="color: var(--success);"></i>
                                <span>Emergencias 24/7</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB: NOTIFICACIONES -->
            <div class="tab-pane fade" id="notificaciones">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="bi bi-bell"></i>
                        </div>
                        <div class="section-title">
                            <h3>Preferencias de Notificaciones</h3>
                            <p>Configura cómo y cuándo recibir notificaciones</p>
                        </div>
                    </div>

                    <form method="POST" action="<?= BASE_URL ?>/index.php?accion=guardarNotificacionesPaciente">
                        
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-envelope"></i>
                                Notificaciones por Email
                            </div>

                            <div class="switch-container">
                                <div class="switch-info">
                                    <h5>Recordatorios de Citas</h5>
                                    <p>Recibe un email 24 horas antes de tu cita</p>
                                </div>
                                <label class="custom-switch">
                                    <input type="checkbox" name="notif_citas" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="switch-container">
                                <div class="switch-info">
                                    <h5>Confirmación de Citas</h5>
                                    <p>Notificación cuando tu cita sea confirmada</p>
                                </div>
                                <label class="custom-switch">
                                    <input type="checkbox" name="notif_confirmacion" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="switch-container">
                                <div class="switch-info">
                                    <h5>Resultados de Exámenes</h5>
                                    <p>Aviso cuando tus resultados estén disponibles</p>
                                </div>
                                <label class="custom-switch">
                                    <input type="checkbox" name="notif_resultados" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="switch-container">
                                <div class="switch-info">
                                    <h5>Facturas y Pagos</h5>
                                    <p>Notificaciones sobre facturas pendientes</p>
                                </div>
                                <label class="custom-switch">
                                    <input type="checkbox" name="notif_facturas" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="switch-container">
                                <div class="switch-info">
                                    <h5>Promociones y Novedades</h5>
                                    <p>Recibe información sobre campañas de salud</p>
                                </div>
                                <label class="custom-switch">
                                    <input type="checkbox" name="notif_promociones">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="btn-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i>
                                Guardar Configuración
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <!-- TAB: PRIVACIDAD -->
            <div class="tab-pane fade" id="privacidad">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="section-title">
                            <h3>Privacidad y Seguridad</h3>
                            <p>Gestiona la privacidad de tu información médica</p>
                        </div>
                    </div>

                    <div class="info-box" style="border-left-color: var(--success);">
                        <h4><i class="bi bi-shield-fill-check"></i> Tu Información está Protegida</h4>
                        <p>Todos tus datos médicos están encriptados y protegidos según las normativas de salud vigentes. Solo el personal médico autorizado puede acceder a tu historial clínico.</p>
                    </div>

                    <form method="POST" action="<?= BASE_URL ?>/index.php?accion=guardarPrivacidadPaciente">
                        
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-eye"></i>
                                Control de Privacidad
                            </div>

                            <div class="switch-container">
                                <div class="switch-info">
                                    <h5>Compartir Historial con Especialistas</h5>
                                    <p>Permitir que especialistas accedan a tu historial en interconsultas</p>
                                </div>
                                <label class="custom-switch">
                                    <input type="checkbox" name="compartir_historial" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="switch-container">
                                <div class="switch-info">
                                    <h5>Recordar Sesión</h5>
                                    <p>Mantener sesión iniciada en este dispositivo</p>
                                </div>
                                <label class="custom-switch">
                                    <input type="checkbox" name="recordar_sesion">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="btn-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-shield-check"></i>
                                Guardar Configuración
                            </button>
                            <a href="<?= BASE_URL ?>/index.php?accion=cambiarPassword" class="btn btn-secondary">
                                <i class="bi bi-key"></i>
                                Cambiar Contraseña
                            </a>
                        </div>

                    </form>

                    <div class="info-box" style="border-left-color: var(--danger); margin-top: 2rem;">
                        <h4><i class="bi bi-exclamation-triangle-fill"></i> Eliminar Cuenta</h4>
                        <p>Si deseas eliminar tu cuenta y todos tus datos, contacta con nuestro equipo de soporte. Este proceso es irreversible.</p>
                        <button class="btn btn-secondary" style="margin-top: 1rem;">
                            <i class="bi bi-envelope"></i>
                            Contactar Soporte
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Confirmación antes de guardar cambios importantes
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const formAction = this.getAttribute('action');
        if (formAction.includes('guardarPrivacidadPaciente')) {
            if (!confirm('¿Estás seguro de cambiar tu configuración de privacidad?')) {
                e.preventDefault();
            }
        }
    });
});
</script>