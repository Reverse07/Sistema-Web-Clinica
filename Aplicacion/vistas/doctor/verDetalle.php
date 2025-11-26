<?php
// =====================================================
// 🩺 Vista: Detalle de Cita - Dashboard Doctor
// Archivo: Aplicacion/vistas/doctor/verDetalle.php
// =====================================================

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'doctor') {
    header("Location: " . BASE_URL . "/index.php?accion=loginVista");
    exit;
}

$citaId = $_GET['id'] ?? null;

if (!$citaId) {
    header("Location: " . BASE_URL . "/index.php?accion=doctorCitas");
    exit;
}

$usuarioId = $_SESSION['usuario_id'];
$pdo = BaseDatos::pdo();

// Obtener ID del doctor
$stmtDoctor = $pdo->prepare("SELECT id FROM doctores WHERE usuario_id = :usuario_id");
$stmtDoctor->execute([':usuario_id' => $usuarioId]);
$doctor = $stmtDoctor->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    echo "<div class='alert alert-danger'>Error: Doctor no encontrado</div>";
    exit;
}

$doctorId = $doctor['id'];

// Obtener información completa de la cita
$sql = "
    SELECT 
        c.id,
        c.fecha,
        TO_CHAR(c.fecha, 'DD/MM/YYYY') as fecha_formateada,
        TO_CHAR(c.fecha, 'HH24:MI') as hora_formateada,
        c.estado,
        up.nombre as paciente_nombre,
        up.telefono as paciente_telefono,
        up.email as paciente_email,
        p.id as paciente_id,
        p.dni as paciente_dni,
        p.fecha_nacimiento,
        p.genero,
        p.direccion as paciente_direccion,
        ud.nombre as doctor_nombre,
        e.nombre as especialidad,
        d.numero_colegiatura,
        COALESCE(hc.id, 0) as historia_id,
        COALESCE(hc.motivo, '') as motivo,
        COALESCE(hc.diagnostico, '') as diagnostico,
        COALESCE(hc.indicaciones, '') as indicaciones,
        COALESCE(hc.observaciones, '') as observaciones
    FROM citas c
    INNER JOIN pacientes p ON c.paciente_id = p.id
    INNER JOIN usuarios up ON p.usuario_id = up.id
    INNER JOIN doctores d ON c.doctor_id = d.id
    INNER JOIN usuarios ud ON d.usuario_id = ud.id
    LEFT JOIN especialidades e ON d.especialidad_id = e.id
    LEFT JOIN historia_clinica hc ON c.id = hc.cita_id
    WHERE c.id = :cita_id AND c.doctor_id = :doctor_id
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':cita_id' => $citaId, ':doctor_id' => $doctorId]);
$cita = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cita) {
    echo "<div class='alert alert-danger'>Cita no encontrada o no tiene permisos para verla</div>";
    exit;
}

// Calcular edad
$edad = '';
if ($cita['fecha_nacimiento']) {
    $fechaNac = new DateTime($cita['fecha_nacimiento']);
    $hoy = new DateTime();
    $edad = $hoy->diff($fechaNac)->y . ' años';
}

// Obtener recetas asociadas a esta cita
$stmtRecetas = $pdo->prepare("
    SELECT 
        r.id,
        r.fecha_emision,
        r.observaciones,
        TO_CHAR(r.fecha_emision, 'DD/MM/YYYY HH24:MI') as fecha_formateada
    FROM recetas r
    WHERE r.paciente_id = :paciente_id 
    AND r.doctor_id = :doctor_id
    ORDER BY r.fecha_emision DESC
");
$stmtRecetas->execute([
    ':paciente_id' => $cita['paciente_id'],
    ':doctor_id' => $doctorId
]);
$recetas = $stmtRecetas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Cita #<?= $citaId ?></title>
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
            font-size: 2rem;
            font-weight: 800;
            margin: 0;
        }

        .content-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
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
            gap: 1.5rem;
        }

        .info-item {
            padding: 1rem;
            background: var(--light-bg);
            border-radius: 0.5rem;
            border-left: 4px solid var(--primary);
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
            font-weight: 600;
            color: #0f172a;
        }

        .badge-estado {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .badge-pendiente {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #78350f;
        }

        .badge-confirmada {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e3a8a;
        }

        .badge-completada {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }

        .badge-cancelada {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #7f1d1d;
        }

        .btn-action {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.3);
            color: white;
        }

        .btn-success-custom {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
        }

        .btn-success-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .btn-warning-custom {
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
            color: white;
        }

        .btn-warning-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
            color: white;
        }

        .alert-info-custom {
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            border: none;
            border-left: 4px solid var(--info);
            color: #0c4a6e;
        }

        .receta-card {
            background: #fffbeb;
            border: 2px solid #fbbf24;
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .receta-card:hover {
            box-shadow: 0 4px 12px rgba(251, 191, 36, 0.2);
            transform: translateY(-2px);
        }

        .actions-bar {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            background: var(--light-bg);
            border-radius: 0.5rem;
        }

        .empty-icon {
            font-size: 4rem;
            opacity: 0.3;
            margin-bottom: 1rem;
        }

        @media print {
            .actions-bar, .btn-action {
                display: none;
            }
            
            body {
                background: white;
            }
            
            .content-card {
                box-shadow: none;
                border: 1px solid #e2e8f0;
            }
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.5rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="page-header">
        <h1><i class="bi bi-clipboard2-pulse"></i> Detalle de Cita #<?= $citaId ?></h1>
        <p class="mb-0">Información completa de la consulta médica</p>
    </div>

    <!-- BARRA DE ACCIONES -->
    <div class="actions-bar">
        <a href="<?= BASE_URL ?>/index.php?accion=doctorCitas" class="btn btn-action btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver a Mis Citas
        </a>
        
        <?php if ($cita['estado'] === 'confirmada' && $cita['historia_id'] == 0): ?>
            <a href="<?= BASE_URL ?>/index.php?accion=crearHistoria&cita_id=<?= $citaId ?>" class="btn btn-action btn-success-custom">
                <i class="bi bi-clipboard-plus"></i> Crear Historia Clínica
            </a>
        <?php endif; ?>

        <?php if ($cita['historia_id'] > 0): ?>
            <a href="<?= BASE_URL ?>/index.php?accion=editarHistoria&id=<?= $cita['historia_id'] ?>" class="btn btn-action btn-warning-custom">
                <i class="bi bi-pencil-square"></i> Editar Historia
            </a>
            <a href="<?= BASE_URL ?>/index.php?accion=crearReceta&paciente_id=<?= $cita['paciente_id'] ?>" class="btn btn-action btn-primary-custom">
                <i class="bi bi-prescription2"></i> Nueva Receta
            </a>
        <?php endif; ?>

        <button onclick="window.print()" class="btn btn-action btn-outline-primary">
            <i class="bi bi-printer"></i> Imprimir
        </button>
    </div>

    <!-- INFORMACIÓN DE LA CITA -->
    <div class="content-card">
        <h2 class="section-title">
            <i class="bi bi-calendar-check"></i>
            Información de la Cita
        </h2>
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label"><i class="bi bi-calendar-event"></i> Fecha</div>
                <div class="info-value"><?= htmlspecialchars($cita['fecha_formateada']) ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label"><i class="bi bi-clock"></i> Hora</div>
                <div class="info-value"><?= htmlspecialchars($cita['hora_formateada']) ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label"><i class="bi bi-tag"></i> Estado</div>
                <div class="info-value">
                    <span class="badge-estado badge-<?= strtolower($cita['estado']) ?>">
                        <?php
                        $iconos = [
                            'pendiente' => '⏳',
                            'confirmada' => '✅',
                            'completada' => '✔️',
                            'cancelada' => '❌'
                        ];
                        echo $iconos[$cita['estado']] ?? '📋';
                        ?>
                        <?= ucfirst($cita['estado']) ?>
                    </span>
                </div>
            </div>

            <div class="info-item">
                <div class="info-label"><i class="bi bi-person-badge"></i> Doctor</div>
                <div class="info-value"><?= htmlspecialchars($cita['doctor_nombre']) ?></div>
            </div>

            <div class="info-item">
                <div class="info-label"><i class="bi bi-hospital"></i> Especialidad</div>
                <div class="info-value"><?= htmlspecialchars($cita['especialidad'] ?? 'No especificada') ?></div>
            </div>

            <div class="info-item">
                <div class="info-label"><i class="bi bi-award"></i> N° Colegiatura</div>
                <div class="info-value"><?= htmlspecialchars($cita['numero_colegiatura'] ?? 'N/A') ?></div>
            </div>
        </div>
    </div>

    <!-- INFORMACIÓN DEL PACIENTE -->
    <div class="content-card">
        <h2 class="section-title">
            <i class="bi bi-person"></i>
            Datos del Paciente
        </h2>
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label"><i class="bi bi-person-circle"></i> Nombre Completo</div>
                <div class="info-value"><?= htmlspecialchars($cita['paciente_nombre']) ?></div>
            </div>

            <div class="info-item">
                <div class="info-label"><i class="bi bi-card-text"></i> DNI</div>
                <div class="info-value"><?= htmlspecialchars($cita['paciente_dni']) ?></div>
            </div>

            <div class="info-item">
                <div class="info-label"><i class="bi bi-cake2"></i> Edad</div>
                <div class="info-value"><?= htmlspecialchars($edad ?: 'N/A') ?></div>
            </div>

            <div class="info-item">
                <div class="info-label"><i class="bi bi-gender-ambiguous"></i> Género</div>
                <div class="info-value"><?= htmlspecialchars(ucfirst($cita['genero'] ?? 'N/A')) ?></div>
            </div>

            <div class="info-item">
                <div class="info-label"><i class="bi bi-telephone"></i> Teléfono</div>
                <div class="info-value"><?= htmlspecialchars($cita['paciente_telefono'] ?? 'N/A') ?></div>
            </div>

            <div class="info-item">
                <div class="info-label"><i class="bi bi-envelope"></i> Email</div>
                <div class="info-value"><?= htmlspecialchars($cita['paciente_email']) ?></div>
            </div>

            <?php if ($cita['paciente_direccion']): ?>
                <div class="info-item" style="grid-column: span 2;">
                    <div class="info-label"><i class="bi bi-geo-alt"></i> Dirección</div>
                    <div class="info-value"><?= htmlspecialchars($cita['paciente_direccion']) ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- HISTORIA CLÍNICA -->
    <?php if ($cita['historia_id'] > 0): ?>
        <div class="content-card">
            <h2 class="section-title">
                <i class="bi bi-clipboard2-pulse"></i>
                Historia Clínica
            </h2>

            <div class="row g-3">
                <?php if ($cita['motivo']): ?>
                    <div class="col-md-12">
                        <div class="info-item">
                            <div class="info-label"><i class="bi bi-clipboard-heart"></i> Motivo de Consulta</div>
                            <div class="info-value"><?= nl2br(htmlspecialchars($cita['motivo'])) ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($cita['diagnostico']): ?>
                    <div class="col-md-12">
                        <div class="info-item" style="border-left-color: var(--success);">
                            <div class="info-label"><i class="bi bi-clipboard-check"></i> Diagnóstico</div>
                            <div class="info-value"><?= nl2br(htmlspecialchars($cita['diagnostico'])) ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($cita['indicaciones']): ?>
                    <div class="col-md-12">
                        <div class="info-item" style="border-left-color: var(--info);">
                            <div class="info-label"><i class="bi bi-file-medical"></i> Indicaciones</div>
                            <div class="info-value"><?= nl2br(htmlspecialchars($cita['indicaciones'])) ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($cita['observaciones']): ?>
                    <div class="col-md-12">
                        <div class="alert alert-info-custom">
                            <strong><i class="bi bi-info-circle"></i> Observaciones:</strong>
                            <p class="mb-0 mt-2"><?= nl2br(htmlspecialchars($cita['observaciones'])) ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="content-card">
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <h3>Sin Historia Clínica</h3>
                <p class="text-muted">Aún no se ha creado la historia clínica para esta cita</p>
                <?php if ($cita['estado'] === 'confirmada'): ?>
                    <a href="<?= BASE_URL ?>/index.php?accion=crearHistoria&cita_id=<?= $citaId ?>" class="btn btn-success-custom mt-3">
                        <i class="bi bi-clipboard-plus"></i> Crear Historia Ahora
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- RECETAS -->
    <?php if (!empty($recetas)): ?>
        <div class="content-card">
            <h2 class="section-title">
                <i class="bi bi-prescription2"></i>
                Recetas Médicas (<?= count($recetas) ?>)
            </h2>

            <?php foreach ($recetas as $receta): ?>
                <div class="receta-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="mb-1">
                                <i class="bi bi-file-medical"></i> 
                                Receta #<?= htmlspecialchars($receta['id']) ?>
                            </h5>
                            <small class="text-muted">
                                <i class="bi bi-calendar"></i> 
                                Emitida: <?= htmlspecialchars($receta['fecha_formateada']) ?>
                            </small>
                        </div>
                        <a href="<?= BASE_URL ?>/index.php?accion=verReceta&id=<?= $receta['id'] ?>" 
                           class="btn btn-sm btn-warning-custom">
                            <i class="bi bi-eye"></i> Ver Detalle
                        </a>
                    </div>
                    
                    <?php if ($receta['observaciones']): ?>
                        <div class="mt-2">
                            <strong>Observaciones:</strong>
                            <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($receta['observaciones'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="content-card">
            <div class="empty-state">
                <div class="empty-icon">💊</div>
                <h3>Sin Recetas</h3>
                <p class="text-muted">No hay recetas médicas asociadas a este paciente</p>
                <?php if ($cita['historia_id'] > 0): ?>
                    <a href="<?= BASE_URL ?>/index.php?accion=crearReceta&paciente_id=<?= $cita['paciente_id'] ?>" 
                       class="btn btn-primary-custom mt-3">
                        <i class="bi bi-prescription2"></i> Crear Receta
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>