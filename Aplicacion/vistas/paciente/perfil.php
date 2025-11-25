<?php
// =====================
// 👤 Vista: Perfil del Paciente PREMIUM
// =====================
$paciente = $paciente ?? null;
$nombrePaciente = $_SESSION['nombre'] ?? 'Paciente';

// Si no hay datos del paciente, usar valores de sesión
if (!$paciente) {
    $paciente = [
        'nombre' => $_SESSION['nombre'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'telefono' => '',
        'fecha_nacimiento' => '',
        'genero' => '',
        'direccion' => '',
        'dni' => ''
    ];
}
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
        scroll-behavior: smooth;
    }

    body {
        padding: 2rem 0;
        color: var(--dark-text);
    }

    .perfil-container {
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

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
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
        box-shadow: var(--shadow-lg);
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
       PROFILE CARD
       ======================== */
    .profile-grid {
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .profile-sidebar {
        background: white;
        border-radius: 2rem;
        padding: 3rem 2rem;
        box-shadow: var(--shadow-sm);
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .profile-sidebar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 120px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        z-index: 0;
    }

    .avatar-container {
        position: relative;
        z-index: 1;
        margin-bottom: 2rem;
    }

    .avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        color: white;
        margin: 0 auto 1rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        border: 5px solid white;
        animation: pulse 3s infinite;
    }

    .profile-name {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--dark-text);
        margin-bottom: 0.5rem;
        font-family: 'Poppins', sans-serif;
    }

    .profile-role {
        display: inline-block;
        background: linear-gradient(135deg, var(--info), var(--primary));
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    .profile-stats {
        display: grid;
        gap: 1rem;
        margin-top: 2rem;
    }

    .stat-item {
        background: var(--light-bg);
        padding: 1.25rem;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .stat-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .stat-info h4 {
        font-size: 0.85rem;
        color: #64748b;
        margin: 0 0 0.25rem 0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-info p {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--dark-text);
        margin: 0;
    }

    /* ========================
       FORM SECTION
       ======================== */
    .profile-content {
        background: white;
        border-radius: 2rem;
        padding: 3rem;
        box-shadow: var(--shadow-sm);
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark-text);
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-family: 'Poppins', sans-serif;
    }

    .section-title i {
        font-size: 1.75rem;
        color: var(--primary);
    }

    .form-group {
        margin-bottom: 1.75rem;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        color: var(--dark-text);
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }

    .form-group label i {
        color: var(--primary);
        margin-right: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .form-control:disabled {
        background: var(--light-bg);
        cursor: not-allowed;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    /* ========================
       BUTTONS
       ======================== */
    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 2px solid #f1f5f9;
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
       MODAL CAMBIAR CONTRASEÑA
       ======================== */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 1.5rem;
        padding: 2.5rem;
        max-width: 500px;
        width: 90%;
        box-shadow: var(--shadow-lg);
        animation: fadeInUp 0.4s ease;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .modal-header h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        color: var(--dark-text);
    }

    .btn-close-modal {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-close-modal:hover {
        color: var(--danger);
        transform: rotate(90deg);
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
    @media (max-width: 1024px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }

        .profile-sidebar {
            order: 1;
        }

        .profile-content {
            order: 2;
        }
    }

    @media (max-width: 768px) {
        .perfil-container {
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

        .form-row {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="perfil-container">

    <!-- HEADER -->
    <div class="page-header animate-fade-in-up">
        <div class="header-content">
            <div class="header-title">
                <div class="header-icon">👤</div>
                <div>
                    <h1>Mi Perfil</h1>
                    <p>Administra tu información personal</p>
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

    <!-- PROFILE GRID -->
    <div class="profile-grid">
        
        <!-- SIDEBAR -->
        <div class="profile-sidebar">
            <div class="avatar-container">
                <div class="avatar">
                    <?= strtoupper(substr($nombrePaciente, 0, 2)) ?>
                </div>
                <h2 class="profile-name"><?= htmlspecialchars($nombrePaciente) ?></h2>
                <span class="profile-role">
                    <i class="bi bi-person-badge"></i>
                    Paciente
                </span>
            </div>

            <div class="profile-stats">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Email</h4>
                        <p><?= htmlspecialchars($paciente['email']) ?></p>
                    </div>
                </div>

                <?php if (!empty($paciente['telefono'])): ?>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="bi bi-telephone"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Teléfono</h4>
                        <p><?= htmlspecialchars($paciente['telefono']) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($paciente['dni'])): ?>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="bi bi-card-text"></i>
                    </div>
                    <div class="stat-info">
                        <h4>DNI</h4>
                        <p><?= htmlspecialchars($paciente['dni']) ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="profile-content">
            
            <!-- FORM DATOS PERSONALES -->
            <form method="POST" action="<?= BASE_URL ?>/index.php?accion=actualizarMiPerfil">
                
                <div class="section-title">
                    <i class="bi bi-person-circle"></i>
                    Información Personal
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <i class="bi bi-person"></i>
                            Nombre Completo *
                        </label>
                        <input type="text" 
                               name="nombre" 
                               class="form-control" 
                               value="<?= htmlspecialchars($paciente['nombre']) ?>" 
                               required>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="bi bi-envelope"></i>
                            Correo Electrónico *
                        </label>
                        <input type="email" 
                               name="email" 
                               class="form-control" 
                               value="<?= htmlspecialchars($paciente['email']) ?>" 
                               required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <i class="bi bi-telephone"></i>
                            Teléfono
                        </label>
                        <input type="text" 
                               name="telefono" 
                               class="form-control" 
                               value="<?= htmlspecialchars($paciente['telefono'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="bi bi-calendar"></i>
                            Fecha de Nacimiento
                        </label>
                        <input type="date" 
                               name="fecha_nacimiento" 
                               class="form-control" 
                               value="<?= htmlspecialchars($paciente['fecha_nacimiento'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <i class="bi bi-gender-ambiguous"></i>
                            Género
                        </label>
                        <select name="genero" class="form-control">
                            <option value="">Seleccionar...</option>
                            <option value="Masculino" <?= ($paciente['genero'] ?? '') === 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                            <option value="Femenino" <?= ($paciente['genero'] ?? '') === 'Femenino' ? 'selected' : '' ?>>Femenino</option>
                            <option value="Otro" <?= ($paciente['genero'] ?? '') === 'Otro' ? 'selected' : '' ?>>Otro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="bi bi-card-text"></i>
                            DNI
                        </label>
                        <input type="text" 
                               name="dni" 
                               class="form-control" 
                               value="<?= htmlspecialchars($paciente['dni'] ?? '') ?>"
                               disabled>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        <i class="bi bi-geo-alt"></i>
                        Dirección
                    </label>
                    <input type="text" 
                           name="direccion" 
                           class="form-control" 
                           value="<?= htmlspecialchars($paciente['direccion'] ?? '') ?>">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i>
                        Guardar Cambios
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="openPasswordModal()">
                        <i class="bi bi-lock"></i>
                        Cambiar Contraseña
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

<!-- MODAL CAMBIAR CONTRASEÑA -->
<div class="modal-overlay" id="passwordModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>🔒 Cambiar Contraseña</h3>
            <button type="button" class="btn-close-modal" onclick="closePasswordModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/index.php?accion=cambiarPassword">
            
            <div class="form-group">
                <label>
                    <i class="bi bi-lock"></i>
                    Contraseña Actual *
                </label>
                <input type="password" 
                       name="password_actual" 
                       class="form-control" 
                       required>
            </div>

            <div class="form-group">
                <label>
                    <i class="bi bi-key"></i>
                    Nueva Contraseña *
                </label>
                <input type="password" 
                       name="password_nueva" 
                       class="form-control" 
                       minlength="6"
                       required>
            </div>

            <div class="form-group">
                <label>
                    <i class="bi bi-key-fill"></i>
                    Confirmar Nueva Contraseña *
                </label>
                <input type="password" 
                       name="password_confirmar" 
                       class="form-control" 
                       minlength="6"
                       required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i>
                    Actualizar Contraseña
                </button>
                <button type="button" class="btn btn-secondary" onclick="closePasswordModal()">
                    <i class="bi bi-x-circle"></i>
                    Cancelar
                </button>
            </div>

        </form>
    </div>
</div>

<script>
// Modal de contraseña
function openPasswordModal() {
    document.getElementById('passwordModal').classList.add('active');
}

function closePasswordModal() {
    document.getElementById('passwordModal').classList.remove('active');
}

// Cerrar modal al hacer clic fuera
document.getElementById('passwordModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePasswordModal();
    }
});

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePasswordModal();
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>