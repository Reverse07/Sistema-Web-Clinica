<?php
// =====================================================
// 💊 Vista: Ver Detalle de Receta - Dashboard Doctor
// Archivo: Aplicacion/vistas/doctor/verReceta.php
// =====================================================

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'doctor') {
    header("Location: " . BASE_URL . "/index.php?accion=loginVista");
    exit;
}

$usuarioId = $_SESSION['usuario_id'];
$recetaId = $_GET['id'] ?? null;

if (!$recetaId) {
    header("Location: " . BASE_URL . "/index.php?accion=doctorRecetas");
    exit;
}

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

// Obtener información completa de la receta
$stmtReceta = $pdo->prepare("
    SELECT 
        r.id,
        r.fecha_emision,
        TO_CHAR(r.fecha_emision, 'DD/MM/YYYY') as fecha_formateada,
        TO_CHAR(r.fecha_emision, 'HH24:MI') as hora_formateada,
        r.observaciones,
        p.id as paciente_id,
        up.nombre as paciente_nombre,
        p.dni as paciente_dni,
        p.fecha_nacimiento,
        p.genero,
        p.direccion,
        up.telefono as paciente_telefono,
        up.email as paciente_email,
        d.id as doctor_id,
        ud.nombre as doctor_nombre,
        d.numero_colegiatura,
        e.nombre as especialidad
    FROM recetas r
    INNER JOIN pacientes p ON r.paciente_id = p.id
    INNER JOIN usuarios up ON p.usuario_id = up.id
    INNER JOIN doctores d ON r.doctor_id = d.id
    INNER JOIN usuarios ud ON d.usuario_id = ud.id
    LEFT JOIN especialidades e ON d.especialidad_id = e.id
    WHERE r.id = :receta_id AND r.doctor_id = :doctor_id
");

$stmtReceta->execute([
    ':receta_id' => $recetaId,
    ':doctor_id' => $doctorId
]);

$receta = $stmtReceta->fetch(PDO::FETCH_ASSOC);

if (!$receta) {
    echo "<div class='alert alert-danger'>Receta no encontrada o no tiene permisos para verla</div>";
    exit;
}

// Obtener medicamentos de la receta
$stmtMedicamentos = $pdo->prepare("
    SELECT 
        dr.id,
        dr.dosis,
        dr.frecuencia,
        dr.duracion,
        dr.cantidad,
        m.nombre as medicamento_nombre,
        m.descripcion as medicamento_descripcion
    FROM detalle_recetas dr
    INNER JOIN medicamentos m ON dr.medicamento_id = m.id
    WHERE dr.receta_id = :receta_id
    ORDER BY dr.id
");

$stmtMedicamentos->execute([':receta_id' => $recetaId]);
$medicamentos = $stmtMedicamentos->fetchAll(PDO::FETCH_ASSOC);

// Calcular edad del paciente
$edad = '';
if ($receta['fecha_nacimiento']) {
    $fechaNac = new DateTime($receta['fecha_nacimiento']);
    $hoy = new DateTime();
    $edad = $hoy->diff($fechaNac)->y . ' años';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Receta #<?= htmlspecialchars($recetaId) ?></title>
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
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .page-header {
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
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

        .receta-container {
            background: white;
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }

        .receta-header {
            text-align: center;
            padding-bottom: 2rem;
            border-bottom: 3px solid #fbbf24;
            margin-bottom: 2rem;
        }

        .receta-number {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #78350f;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .fecha-emision {
            color: #64748b;
            font-size: 0.95rem;
        }

        .info-section {
            background: #fffbeb;
            border-left: 4px solid var(--warning);
            padding: 1.5rem;
            border-radius: 0.75rem;
            margin-bottom: 2rem;
        }

        .info-section-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: #78350f;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-row {
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #fde68a;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #92400e;
            font-size: 0.875rem;
        }

        .info-value {
            color: #451a03;
            font-size: 0.95rem;
        }

        .medicamentos-section {
            margin-top: 2rem;
        }

        .section-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--warning);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid var(--warning);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .medicamento-card {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 2px solid #fbbf24;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            position: relative;
        }

        .medicamento-number {
            position: absolute;
            top: -12px;
            left: 20px;
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
        }

        .medicamento-nombre {
            font-family: 'Poppins', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: #78350f;
            margin-bottom: 0.5rem;
            padding-left: 0.5rem;
        }

        .medicamento-descripcion {
            color: #92400e;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            padding-left: 0.5rem;
            font-style: italic;
        }

        .medicamento-detalles {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .detalle-item {
            background: white;
            padding: 1rem;
            border-radius: 0.5rem;
            border-left: 3px solid var(--warning);
        }

        .detalle-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #92400e;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .detalle-value {
            font-size: 1rem;
            color: #451a03;
            font-weight: 600;
        }

        .observaciones-section {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-left: 4px solid var(--info);
            padding: 1.5rem;
            border-radius: 0.75rem;
            margin-top: 2rem;
        }

        .observaciones-title {
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .observaciones-text {
            color: #1e40af;
            line-height: 1.6;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
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

        .btn-imprimir {
            background: var(--success);
            color: white;
        }

        .btn-imprimir:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .btn-volver {
            background: #64748b;
            color: white;
        }

        .btn-volver:hover {
            background: #475569;
            transform: translateY(-2px);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #64748b;
        }

        @media print {
            body {
                background: white;
            }
            .action-buttons, .page-header {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.75rem;
            }

            .info-row {
                grid-template-columns: 1fr;
                gap: 0.25rem;
            }

            .medicamento-detalles {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="bi bi-file-medical"></i> Detalle de Receta</h1>
                <p class="mb-0">Información completa de la prescripción médica</p>
            </div>
            <div class="receta-number">
                <i class="bi bi-hash"></i> Receta #<?= htmlspecialchars($recetaId) ?>
            </div>
        </div>
    </div>

    <!-- CONTENIDO DE LA RECETA -->
    <div class="receta-container">

        <!-- HEADER DE LA RECETA -->
        <div class="receta-header">
            <h2 class="mb-3">📋 RECETA MÉDICA</h2>
            <div class="fecha-emision">
                <i class="bi bi-calendar-event"></i>
                Fecha de emisión: <strong><?= htmlspecialchars($receta['fecha_formateada']) ?></strong>
                a las <strong><?= htmlspecialchars($receta['hora_formateada']) ?></strong>
            </div>
        </div>

        <!-- INFORMACIÓN DEL DOCTOR -->
        <div class="info-section">
            <div class="info-section-title">
                <i class="bi bi-person-badge"></i>
                Datos del Médico
            </div>
            <div class="info-row">
                <div class="info-label">Nombre:</div>
                <div class="info-value"><?= htmlspecialchars($receta['doctor_nombre']) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Especialidad:</div>
                <div class="info-value"><?= htmlspecialchars($receta['especialidad'] ?? 'Medicina General') ?></div>
            </div>
            <?php if ($receta['numero_colegiatura']): ?>
            <div class="info-row">
                <div class="info-label">N° Colegiatura:</div>
                <div class="info-value"><?= htmlspecialchars($receta['numero_colegiatura']) ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- INFORMACIÓN DEL PACIENTE -->
        <div class="info-section">
            <div class="info-section-title">
                <i class="bi bi-person-heart"></i>
                Datos del Paciente
            </div>
            <div class="info-row">
                <div class="info-label">Nombre:</div>
                <div class="info-value"><?= htmlspecialchars($receta['paciente_nombre']) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">DNI:</div>
                <div class="info-value"><?= htmlspecialchars($receta['paciente_dni']) ?></div>
            </div>
            <?php if ($edad): ?>
            <div class="info-row">
                <div class="info-label">Edad:</div>
                <div class="info-value"><?= htmlspecialchars($edad) ?></div>
            </div>
            <?php endif; ?>
            <?php if ($receta['genero']): ?>
            <div class="info-row">
                <div class="info-label">Género:</div>
                <div class="info-value"><?= htmlspecialchars(ucfirst($receta['genero'])) ?></div>
            </div>
            <?php endif; ?>
            <?php if ($receta['paciente_telefono']): ?>
            <div class="info-row">
                <div class="info-label">Teléfono:</div>
                <div class="info-value"><?= htmlspecialchars($receta['paciente_telefono']) ?></div>
            </div>
            <?php endif; ?>
            <?php if ($receta['direccion']): ?>
            <div class="info-row">
                <div class="info-label">Dirección:</div>
                <div class="info-value"><?= htmlspecialchars($receta['direccion']) ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- MEDICAMENTOS PRESCRITOS -->
        <div class="medicamentos-section">
            <h2 class="section-title">
                <i class="bi bi-capsule-pill"></i>
                Medicamentos Prescritos
            </h2>

            <?php if (empty($medicamentos)): ?>
                <div class="empty-state">
                    <i class="bi bi-exclamation-circle" style="font-size: 3rem;"></i>
                    <p>No se encontraron medicamentos en esta receta</p>
                </div>
            <?php else: ?>
                <?php foreach ($medicamentos as $index => $med): ?>
                    <div class="medicamento-card">
                        <div class="medicamento-number"><?= $index + 1 ?></div>
                        
                        <div class="medicamento-nombre">
                            <?= htmlspecialchars($med['medicamento_nombre']) ?>
                        </div>
                        
                        <?php if ($med['medicamento_descripcion']): ?>
                            <div class="medicamento-descripcion">
                                <?= htmlspecialchars($med['medicamento_descripcion']) ?>
                            </div>
                        <?php endif; ?>

                        <div class="medicamento-detalles">
                            <div class="detalle-item">
                                <div class="detalle-label">
                                    <i class="bi bi-droplet"></i> Dosis
                                </div>
                                <div class="detalle-value">
                                    <?= htmlspecialchars($med['dosis'] ?: 'No especificada') ?>
                                </div>
                            </div>

                            <div class="detalle-item">
                                <div class="detalle-label">
                                    <i class="bi bi-clock-history"></i> Frecuencia
                                </div>
                                <div class="detalle-value">
                                    <?= htmlspecialchars($med['frecuencia'] ?: 'No especificada') ?>
                                </div>
                            </div>

                            <div class="detalle-item">
                                <div class="detalle-label">
                                    <i class="bi bi-calendar-range"></i> Duración
                                </div>
                                <div class="detalle-value">
                                    <?= htmlspecialchars($med['duracion'] ?: 'No especificada') ?>
                                </div>
                            </div>

                            <div class="detalle-item">
                                <div class="detalle-label">
                                    <i class="bi bi-123"></i> Cantidad
                                </div>
                                <div class="detalle-value">
                                    <?= htmlspecialchars($med['cantidad']) ?> unidad(es)
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- OBSERVACIONES GENERALES -->
        <?php if ($receta['observaciones']): ?>
            <div class="observaciones-section">
                <div class="observaciones-title">
                    <i class="bi bi-chat-left-text"></i>
                    Observaciones e Indicaciones Generales
                </div>
                <div class="observaciones-text">
                    <?= nl2br(htmlspecialchars($receta['observaciones'])) ?>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- BOTONES DE ACCIÓN -->
    <div class="action-buttons">
        <a href="<?= BASE_URL ?>/index.php?accion=doctorRecetas" class="btn btn-action btn-volver">
            <i class="bi bi-arrow-left-circle"></i> Volver a Recetas
        </a>
        <a href="<?= BASE_URL ?>/index.php?accion=imprimirReceta&id=<?= $recetaId ?>" 
           class="btn btn-action btn-imprimir" target="_blank">
            <i class="bi bi-printer"></i> Imprimir Receta
        </a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>