<?php
// =====================================================
// ⚙️ Vista: Configuración - Doctor
// Archivo: Aplicacion/vistas/doctor/configuracion.php
// =====================================================

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'doctor') {
    header("Location: " . BASE_URL . "/index.php?accion=loginVista");
    exit;
}

$usuarioId = $_SESSION['usuario_id'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - Doctor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0ea5e9;
            --primary-dark: #0284c7;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --light-bg: #f8fafc;
            --shadow-sm: 0 4px 15px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 12px 30px rgba(0, 0, 0, 0.15);
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2rem;
            border-radius: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
        }

        .page-header h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .config-card {
            background: white;
            border-radius: 1.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            transition: transform 0.3s ease;
        }

        .config-card:hover {
            transform: translateY(-5px);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid var(--section-color, var(--primary));
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-icon {
            width: 50px;
            height: 50px;
            border-radius: 1rem;
            background: linear-gradient(135deg, var(--section-color, var(--primary)) 0%, var(--section-dark, var(--primary-dark)) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .config-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem;
            background: var(--light-bg);
            border-radius: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .config-item:hover {
            background: #e0f2fe;
            transform: translateX(10px);
        }

        .config-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .config-icon-small {
            width: 45px;
            height: 45px;
            border-radius: 0.75rem;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: var(--item-color, var(--primary));
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .config-text h5 {
            margin: 0;
            font-weight: 700;
            color: #0f172a;
        }

        .config-text p {
            margin: 0;
            font-size: 0.875rem;
            color: #64748b;
        }

        .form-switch {
            transform: scale(1.5);
        }

        .form-switch .form-check-input:checked {
            background-color: var(--success);
            border-color: var(--success);
        }

        .btn-save {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .alert {
            border-radius: 1rem;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-banner {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            padding: 1.5rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            border-left: 4px solid var(--info);
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.75rem;
            }

            .config-item {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="page-header">
        <h1>
            <i class="bi bi-gear-fill"></i>
            Configuración
        </h1>
        <p class="mb-0">Personaliza tus preferencias y notificaciones</p>
    </div>

    <!-- MENSAJES -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['mensaje']['tipo'] === 'exito' ? 'success' : 'danger' ?>">
            <i class="bi bi-<?= $_SESSION['mensaje']['tipo'] === 'exito' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
            <?= htmlspecialchars($_SESSION['mensaje']['texto']) ?>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <!-- INFO BANNER -->
    <div class="info-banner">
        <h5><i class="bi bi-info-circle"></i> Información</h5>
        <p class="mb-0">Esta página te permite configurar tus preferencias de notificaciones y privacidad. Los cambios se guardan automáticamente.</p>
    </div>

    <!-- NOTIFICACIONES -->
    <div class="config-card" style="--section-color: var(--primary); --section-dark: var(--primary-dark);">
        <div class="section-title">
            <div class="section-icon">
                <i class="bi bi-bell-fill"></i>
            </div>
            Notificaciones
        </div>

        <form method="POST" action="<?= BASE_URL ?>/index.php?accion=guardarConfiguracionDoctor">
            <input type="hidden" name="seccion" value="notificaciones">

            <div class="config-item" style="--item-color: var(--success);">
                <div class="config-info">
                    <div class="config-icon-small">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <div class="config-text">
                        <h5>Notificaciones por Email</h5>
                        <p>Recibir recordatorios de citas por correo electrónico</p>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="email_citas" id="email_citas" checked>
                </div>
            </div>

            <div class="config-item" style="--item-color: var(--info);">
                <div class="config-info">
                    <div class="config-icon-small">
                        <i class="bi bi-phone"></i>
                    </div>
                    <div class="config-text">
                        <h5>Notificaciones por SMS</h5>
                        <p>Recibir alertas importantes por mensaje de texto</p>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="sms_alertas" id="sms_alertas">
                </div>
            </div>

            <div class="config-item" style="--item-color: var(--warning);">
                <div class="config-info">
                    <div class="config-icon-small">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div class="config-text">
                        <h5>Recordatorio de Citas</h5>
                        <p>Recordatorio 24 horas antes de cada cita programada</p>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="recordatorio_citas" id="recordatorio_citas" checked>
                </div>
            </div>

            <button type="submit" class="btn btn-save w-100 mt-3">
                <i class="bi bi-check-circle"></i> Guardar Preferencias de Notificaciones
            </button>
        </form>
    </div>

    <!-- PRIVACIDAD -->
    <div class="config-card" style="--section-color: var(--success); --section-dark: #059669;">
        <div class="section-title">
            <div class="section-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            Privacidad y Seguridad
        </div>

        <form method="POST" action="<?= BASE_URL ?>/index.php?accion=guardarConfiguracionDoctor">
            <input type="hidden" name="seccion" value="privacidad">

            <div class="config-item" style="--item-color: var(--success);">
                <div class="config-info">
                    <div class="config-icon-small">
                        <i class="bi bi-eye"></i>
                    </div>
                    <div class="config-text">
                        <h5>Perfil Visible</h5>
                        <p>Permitir que otros médicos vean tu perfil profesional</p>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="perfil_visible" id="perfil_visible" checked>
                </div>
            </div>

            <div class="config-item" style="--item-color: var(--info);">
                <div class="config-info">
                    <div class="config-icon-small">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <div class="config-text">
                        <h5>Verificación en Dos Pasos</h5>
                        <p>Seguridad adicional al iniciar sesión (recomendado)</p>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="verificacion_dos_pasos" id="verificacion_dos_pasos">
                </div>
            </div>

            <button type="submit" class="btn btn-save w-100 mt-3">
                <i class="bi bi-check-circle"></i> Guardar Configuración de Privacidad
            </button>
        </form>
    </div>

    <!-- ACCESO RÁPIDO -->
    <div class="config-card" style="--section-color: var(--warning); --section-dark: #d97706;">
        <div class="section-title">
            <div class="section-icon">
                <i class="bi bi-lightning-fill"></i>
            </div>
            Acceso Rápido
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <a href="<?= BASE_URL ?>/index.php?accion=doctorPerfil" class="config-item text-decoration-none" style="--item-color: var(--primary);">
                    <div class="config-info">
                        <div class="config-icon-small">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div class="config-text">
                            <h5>Mi Perfil</h5>
                            <p>Editar información personal</p>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            </div>

            <div class="col-md-6">
                <a href="<?= BASE_URL ?>/index.php?accion=cambiarPassword" class="config-item text-decoration-none" style="--item-color: var(--danger);">
                    <div class="config-info">
                        <div class="config-icon-small">
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <div class="config-text">
                            <h5>Cambiar Contraseña</h5>
                            <p>Actualizar credenciales de acceso</p>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Confirmación al cambiar configuraciones
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('¿Guardar estos cambios de configuración?')) {
                e.preventDefault();
            }
        });
    });

    // Feedback visual al activar/desactivar switches
    document.querySelectorAll('.form-check-input').forEach(input => {
        input.addEventListener('change', function() {
            const item = this.closest('.config-item');
            if (this.checked) {
                item.style.borderLeft = '4px solid var(--success)';
            } else {
                item.style.borderLeft = 'none';
            }
        });
    });
</script>

</body>
</html>