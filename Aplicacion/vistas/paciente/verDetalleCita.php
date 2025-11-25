<?php
// =====================
// 📋 Vista: Detalle de Cita del Paciente
// =====================
$cita = $cita ?? null;

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

    .detalle-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .page-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 2.5rem;
        border-radius: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-title h1 {
        font-family: 'Poppins', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        margin: 0;
    }

    .cita-id {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.95rem;
    }

    .btn-back {
        background: white;
        color: var(--primary);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
    }

    .card {
        background: white;
        border-radius: 1.25rem;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
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

    .info-row {
        display: flex;
        padding: 1rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #64748b;
        min-width: 150px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-value {
        color: #0f172a;
        font-weight: 500;
        flex: 1;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .badge-confirmada {
        background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%);
        color: #1e4620;
    }

    .badge-pendiente {
        background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
        color: #5a3e00;
    }

    .badge-cancelada {
        background: linear-gradient(135deg, #fab1a0 0%, #fd79a8 100%);
        color: #5a1a1a;
    }

    .doctor-card {
        background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
        border: 2px solid #e0e7ff;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .doctor-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .doctor-avatar {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
    }

    .doctor-info h3 {
        font-family: 'Poppins', sans-serif;
        font-size: 1.3rem;
        font-weight: 700;
        margin: 0 0 0.25rem 0;
        color: #0f172a;
    }

    .doctor-especialidad {
        color: #64748b;
        font-size: 0.95rem;
    }

    .contact-info {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #475569;
        font-size: 0.95rem;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .btn-action {
        flex: 1;
        padding: 1rem;
        border-radius: 0.75rem;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .btn-danger {
        background: var(--danger);
        color: white;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .fecha-destacada {
        background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
        padding: 1.5rem;
        border-radius: 1rem;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .fecha-dia {
        font-family: 'Poppins', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        color: #5a3e00;
        margin-bottom: 0.5rem;
    }

    .fecha-hora {
        font-size: 1.2rem;
        font-weight: 600;
        color: #7c5d00;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .alert-info {
        background: #e0f2fe;
        border: 2px solid #bae6fd;
        border-radius: 0.75rem;
        padding: 1rem;
        color: #075985;
        display: flex;
        align-items: start;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }

    @media (max-width: 768px) {
        .content-grid {
            grid-template-columns: 1fr;
        }

        .detalle-container {
            padding: 1rem;
        }

        .header-content {
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>

<div class="detalle-container">
    <!-- HEADER -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-title">
                <h1>Detalle de la Cita</h1>
                <span class="cita-id">#<?= str_pad($cita['id'], 4, '0', STR_PAD_LEFT) ?></span>
            </div>
            <a href="<?= BASE_URL ?>/index.php?accion=misCitas" class="btn-back">
                <i class="bi bi-arrow-left"></i>
                Volver
            </a>
        </div>
    </div>

    <div class="content-grid">
        <!-- COLUMNA PRINCIPAL -->
        <div>
            <!-- Información del Doctor -->
            <div class="card">
                <h2 class="card-title">
                    <i class="bi bi-person-badge"></i>
                    Información del Médico
                </h2>

                <div class="doctor-card">
                    <div class="doctor-header">
                        <div class="doctor-avatar">👨‍⚕️</div>
                        <div class="doctor-info">
                            <h3>Dr. <?= htmlspecialchars($cita['doctor_nombre'] ?? 'Por asignar') ?></h3>
                            <div class="doctor-especialidad">
                                <?= htmlspecialchars($cita['especialidad'] ?? 'Medicina General') ?>
                            </div>
                        </div>
                    </div>

                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="bi bi-envelope"></i>
                            <?= htmlspecialchars($cita['doctor_email'] ?? 'Sin email') ?>
                        </div>
                        <div class="contact-item">
                            <i class="bi bi-telephone"></i>
                            <?= htmlspecialchars($cita['doctor_telefono'] ?? 'Sin teléfono') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles de la Cita -->
            <div class="card" style="margin-top: 2rem;">
                <h2 class="card-title">
                    <i class="bi bi-info-circle"></i>
                    Detalles de la Cita
                </h2>

                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-hash"></i>
                        ID de Cita
                    </div>
                    <div class="info-value">
                        #<?= str_pad($cita['id'], 4, '0', STR_PAD_LEFT) ?>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-flag"></i>
                        Estado
                    </div>
                    <div class="info-value">
                        <span class="status-badge badge-<?= strtolower(str_replace(' ', '', $cita['estado'])) ?>">
                            <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i>
                            <?= htmlspecialchars($cita['estado']) ?>
                        </span>
                    </div>
                </div>

                <?php if (!empty($cita['motivo'])): ?>
                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-file-text"></i>
                        Motivo
                    </div>
                    <div class="info-value">
                        <?= htmlspecialchars($cita['motivo']) ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($cita['observaciones'])): ?>
                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-chat-left-text"></i>
                        Observaciones
                    </div>
                    <div class="info-value">
                        <?= htmlspecialchars($cita['observaciones']) ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="alert-info">
                    <i class="bi bi-info-circle" style="font-size: 1.5rem;"></i>
                    <div>
                        <strong>Importante:</strong> Por favor llega 15 minutos antes de tu cita.
                        Trae tu documento de identidad y cualquier resultado de exámenes previos.
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMNA LATERAL -->
        <div>
            <!-- Fecha y Hora -->
            <div class="card">
                <h2 class="card-title">
                    <i class="bi bi-calendar-check"></i>
                    Fecha y Hora
                </h2>

                <div class="fecha-destacada">
                    <div class="fecha-dia">
                        <?= date('d/m/Y', strtotime($cita['fecha'])) ?>
                    </div>
                    <div class="fecha-hora">
                        <i class="bi bi-clock"></i>
                        <?= date('h:i A', strtotime($cita['fecha'])) ?>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-calendar2-event"></i>
                        Día
                    </div>
                    <div class="info-value">
                        <?php
                        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                        echo $dias[date('w', strtotime($cita['fecha']))];
                        ?>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-hourglass-split"></i>
                        Duración estimada
                    </div>
                    <div class="info-value">30 minutos</div>
                </div>
            </div>

            <!-- Acciones -->
            <?php if (strtolower($cita['estado']) !== 'cancelada'): ?>
            <div class="action-buttons">
                <button class="btn-action btn-primary" onclick="reprogramarCita(<?= $cita['id'] ?>)">
                    <i class="bi bi-arrow-repeat"></i>
                    Reprogramar
                </button>
                <button class="btn-action btn-danger" onclick="cancelarCita(<?= $cita['id'] ?>)">
                    <i class="bi bi-x-circle"></i>
                    Cancelar
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function reprogramarCita(citaId) {
    Swal.fire({
        title: "¿Reprogramar cita?",
        text: "Podrás elegir una nueva fecha y hora.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, reprogramar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#667eea",
        cancelButtonColor: "#d33"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= BASE_URL ?>/index.php?accion=reprogramarCita&id=' + citaId;
        }
    });
}

function cancelarCita(citaId) {
    Swal.fire({
        title: "¿Cancelar cita?",
        text: "Esta acción no se puede deshacer.",
        icon: "error",
        showCancelButton: true,
        confirmButtonText: "Sí, cancelar",
        cancelButtonText: "Volver",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= BASE_URL ?>/index.php?accion=cancelarCita&id=' + citaId;
        }
    });
}
</script>