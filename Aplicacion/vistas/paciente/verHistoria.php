<?php
// =====================================================
// 📋 Vista: Ver Historia Clínica - Dashboard Paciente
// Archivo: Aplicacion/vistas/paciente/verHistoria.php
// =====================================================

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'paciente') {
    header("Location: " . BASE_URL . "/index.php?accion=loginVista");
    exit;
}

$usuarioId = $_SESSION['usuario_id'];
$historiaId = $_GET['id'] ?? null;

if (!$historiaId) {
    header("Location: " . BASE_URL . "/index.php?accion=miHistorial");
    exit;
}

$pdo = BaseDatos::pdo();

// Obtener ID del paciente
$stmtPaciente = $pdo->prepare("SELECT id FROM pacientes WHERE usuario_id = :usuario_id");
$stmtPaciente->execute([':usuario_id' => $usuarioId]);
$paciente = $stmtPaciente->fetch(PDO::FETCH_ASSOC);

if (!$paciente) {
    echo "<div class='alert alert-danger'>Error: Paciente no encontrado</div>";
    exit;
}

$pacienteId = $paciente['id'];

// Obtener historia clínica completa
$stmtHistoria = $pdo->prepare("
    SELECT 
        hc.id,
        hc.motivo,
        hc.diagnostico,
        hc.indicaciones,
        hc.observaciones,
        hc.creada_en,
        TO_CHAR(hc.creada_en, 'DD/MM/YYYY') as fecha_formateada,
        TO_CHAR(hc.creada_en, 'HH24:MI') as hora_formateada,
        ud.nombre as doctor_nombre,
        ud.email as doctor_email,
        ud.telefono as doctor_telefono,
        d.numero_colegiatura,
        e.nombre as especialidad,
        c.fecha as fecha_cita,
        TO_CHAR(c.fecha, 'DD/MM/YYYY') as fecha_cita_formateada
    FROM historia_clinica hc
    INNER JOIN doctores d ON hc.doctor_id = d.id
    INNER JOIN usuarios ud ON d.usuario_id = ud.id
    LEFT JOIN especialidades e ON d.especialidad_id = e.id
    INNER JOIN citas c ON hc.cita_id = c.id
    WHERE hc.id = :historia_id AND hc.paciente_id = :paciente_id
");

$stmtHistoria->execute([
    ':historia_id' => $historiaId,
    ':paciente_id' => $pacienteId
]);

$historia = $stmtHistoria->fetch(PDO::FETCH_ASSOC);

if (!$historia) {
    echo "<div class='alert alert-danger'>Historia clínica no encontrada o no tiene permisos para verla</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historia Clínica #<?= htmlspecialchars($historiaId) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #764ba2;
            --success: #10b981;
            --info: #3b82f6;
            --shadow-sm: 0 4px 15px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 12px 30px rgba(0, 0, 0, 0.15);
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .historia-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 2rem;
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
        }

        .content-card {
            background: white;
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }

        .section-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid var(--primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .info-item {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 0.75rem;
            border-left: 3px solid var(--primary);
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 1rem;
            color: #0f172a;
            font-weight: 600;
        }

        .clinical-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-left: 4px solid var(--info);
            padding: 1.5rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .clinical-label {
            font-weight: 700;
            color: #0369a1;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .clinical-content {
            color: #0c4a6e;
            line-height: 1.8;
            font-size: 0.95rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-download {
            background: var(--success);
            color: white;
        }

        .btn-download:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .btn-back {
            background: #64748b;
            color: white;
        }

        .btn-back:hover {
            background: #475569;
            color: white;
        }

        @media (max-width: 768px) {
            .page-header h1 { font-size: 1.75rem; }
            .info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="historia-container">

    <!-- HEADER -->
    <div class="page-header">
        <h1><i class="bi bi-file-medical-fill"></i> Historia Clínica</h1>
        <p class="mb-0">Registro N° <?= str_pad($historiaId, 6, '0', STR_PAD_LEFT) ?></p>
    </div>

    <!-- INFORMACIÓN GENERAL -->
    <div class="content-card">
        <h2 class="section-title">
            <i class="bi bi-info-circle"></i>
            Información General
        </h2>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label"><i class="bi bi-calendar-event"></i> Fecha de Consulta</div>
                <div class="info-value"><?= htmlspecialchars($historia['fecha_cita_formateada']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-label"><i class="bi bi-clock"></i> Registrado el</div>
                <div class="info-value">
                    <?= htmlspecialchars($historia['fecha_formateada']) ?> 
                    <?= htmlspecialchars($historia['hora_formateada']) ?>
                </div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label"><i class="bi bi-person-badge"></i> Médico Tratante</div>
                <div class="info-value">Dr. <?= htmlspecialchars($historia['doctor_nombre']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-label"><i class="bi bi-hospital"></i> Especialidad</div>
                <div class="info-value"><?= htmlspecialchars($historia['especialidad'] ?? 'Medicina General') ?></div>
            </div>
            <?php if ($historia['numero_colegiatura']): ?>
            <div class="info-item">
                <div class="info-label"><i class="bi bi-award"></i> N° Colegiatura</div>
                <div class="info-value"><?= htmlspecialchars($historia['numero_colegiatura']) ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- DATOS CLÍNICOS -->
    <div class="content-card">
        <h2 class="section-title">
            <i class="bi bi-clipboard2-pulse"></i>
            Datos Clínicos
        </h2>

        <div class="clinical-section">
            <div class="clinical-label">
                <i class="bi bi-clipboard-heart"></i>
                Motivo de Consulta
            </div>
            <div class="clinical-content">
                <?= nl2br(htmlspecialchars($historia['motivo'])) ?>
            </div>
        </div>

        <div class="clinical-section">
            <div class="clinical-label">
                <i class="bi bi-clipboard-check"></i>
                Diagnóstico
            </div>
            <div class="clinical-content">
                <?= nl2br(htmlspecialchars($historia['diagnostico'])) ?>
            </div>
        </div>

        <div class="clinical-section">
            <div class="clinical-label">
                <i class="bi bi-prescription"></i>
                Indicaciones y Tratamiento
            </div>
            <div class="clinical-content">
                <?= nl2br(htmlspecialchars($historia['indicaciones'])) ?>
            </div>
        </div>

        <?php if ($historia['observaciones']): ?>
        <div class="clinical-section">
            <div class="clinical-label">
                <i class="bi bi-chat-left-text"></i>
                Observaciones Adicionales
            </div>
            <div class="clinical-content">
                <?= nl2br(htmlspecialchars($historia['observaciones'])) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- BOTONES DE ACCIÓN -->
    <div class="action-buttons">
        <a href="<?= BASE_URL ?>/index.php?accion=miHistorial" class="btn-action btn-back">
            <i class="bi bi-arrow-left-circle"></i> Volver al Historial
        </a>
        <a href="<?= BASE_URL ?>/index.php?accion=descargarHistoria&id=<?= $historiaId ?>" 
           class="btn-action btn-download" target="_blank">
            <i class="bi bi-download"></i> Descargar PDF
        </a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>