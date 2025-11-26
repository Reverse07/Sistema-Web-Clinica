<?php
// =====================================================
// 🩺 Vista: Mis Historias Clínicas - Dashboard Doctor
// Archivo: Aplicacion/vistas/doctor/historias.php
// =====================================================

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'doctor') {
    header("Location: " . BASE_URL . "/index.php?accion=loginVista");
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

// Filtros - ✅ INICIALIZAR TODAS LAS VARIABLES
$filtroPaciente = $_GET['paciente'] ?? '';
$filtroFecha = $_GET['fecha'] ?? '';
$busqueda = $_GET['busqueda'] ?? ''; // ✅ AÑADIDO

// Construir consulta base
$sql = "
    SELECT 
        hc.id,
        hc.motivo,
        hc.diagnostico,
        hc.indicaciones,
        hc.observaciones,
        hc.creada_en,
        TO_CHAR(hc.creada_en, 'DD/MM/YYYY') as fecha_formateada,
        TO_CHAR(hc.creada_en, 'HH24:MI') as hora_formateada,
        c.id as cita_id,
        c.fecha as fecha_cita,
        TO_CHAR(c.fecha, 'DD/MM/YYYY') as fecha_cita_formateada,
        up.nombre as paciente_nombre,
        p.id as paciente_id,
        p.dni as paciente_dni
    FROM historia_clinica hc
    INNER JOIN citas c ON hc.cita_id = c.id
    INNER JOIN pacientes p ON hc.paciente_id = p.id
    INNER JOIN usuarios up ON p.usuario_id = up.id
    WHERE hc.doctor_id = :doctor_id
";

$params = [':doctor_id' => $doctorId];

// Aplicar filtros
if (!empty($filtroPaciente)) {
    $sql .= " AND p.id = :paciente_id";
    $params[':paciente_id'] = $filtroPaciente;
}

if (!empty($filtroFecha)) {
    $sql .= " AND DATE(hc.creada_en) = :fecha";
    $params[':fecha'] = $filtroFecha;
}

if (!empty($busqueda)) {
    $sql .= " AND (up.nombre ILIKE :busqueda OR p.dni ILIKE :busqueda OR hc.diagnostico ILIKE :busqueda)";
    $params[':busqueda'] = "%$busqueda%";
}

$sql .= " ORDER BY hc.creada_en DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$historias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas
$stmtStats = $pdo->prepare("
    SELECT 
        COUNT(*) as total,
        COUNT(DISTINCT paciente_id) as pacientes_unicos,
        COUNT(*) FILTER (WHERE DATE(creada_en) = CURRENT_DATE) as hoy,
        COUNT(*) FILTER (WHERE DATE(creada_en) >= CURRENT_DATE - INTERVAL '7 days') as semana,
        COUNT(*) FILTER (WHERE DATE(creada_en) >= CURRENT_DATE - INTERVAL '30 days') as mes
    FROM historia_clinica 
    WHERE doctor_id = :doctor_id
");
$stmtStats->execute([':doctor_id' => $doctorId]);
$stats = $stmtStats->fetch(PDO::FETCH_ASSOC);

// Obtener lista de pacientes para filtro
$stmtPacientes = $pdo->prepare("
    SELECT DISTINCT p.id, u.nombre
    FROM historia_clinica hc
    INNER JOIN pacientes p ON hc.paciente_id = p.id
    INNER JOIN usuarios u ON p.usuario_id = u.id
    WHERE hc.doctor_id = :doctor_id
    ORDER BY u.nombre
");
$stmtPacientes->execute([':doctor_id' => $doctorId]);
$pacientes = $stmtPacientes->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Historias Clínicas - Doctor</title>
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
        }

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .btn-new-history {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
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

        .btn-new-history:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--card-color);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.total { --card-color: var(--primary); }
        .stat-card.pacientes { --card-color: var(--success); }
        .stat-card.hoy { --card-color: var(--warning); }
        .stat-card.semana { --card-color: var(--info); }
        .stat-card.mes { --card-color: #8b5cf6; }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--card-color);
            font-family: 'Poppins', sans-serif;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }

        .filters-section {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
        }

        .historia-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--primary);
            transition: all 0.3s ease;
        }

        .historia-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .historia-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .paciente-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .paciente-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .paciente-detalles h5 {
            margin: 0;
            font-weight: 700;
            color: #0f172a;
        }

        .paciente-detalles small {
            color: #64748b;
        }

        .historia-fecha {
            text-align: right;
        }

        .fecha-badge {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #78350f;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-block;
        }

        .historia-content {
            margin-bottom: 1rem;
        }

        .info-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .info-item {
            padding: 0.75rem;
            background: var(--light-bg);
            border-radius: 0.5rem;
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 0.95rem;
            font-weight: 500;
            color: #0f172a;
        }

        .historia-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .btn-ver {
            background: var(--info);
            color: white;
        }

        .btn-ver:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            color: white;
        }

        .btn-editar {
            background: var(--warning);
            color: white;
        }

        .btn-editar:hover {
            background: #d97706;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
            color: white;
        }

        .btn-receta {
            background: var(--success);
            color: white;
        }

        .btn-receta:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 1rem;
            box-shadow: var(--shadow-sm);
        }

        .empty-icon {
            font-size: 5rem;
            opacity: 0.3;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.75rem;
            }

            .header-actions {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .stats-cards {
                grid-template-columns: repeat(2, 1fr);
            }

            .historia-header {
                flex-direction: column;
                gap: 1rem;
            }

            .historia-fecha {
                text-align: left;
            }

            .historia-actions {
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">

    <!-- HEADER CON BOTÓN NUEVA HISTORIA -->
    <div class="page-header">
        <div class="header-actions">
            <div>
                <h1><i class="bi bi-clipboard2-pulse"></i> Mis Historias Clínicas</h1>
                <p class="mb-0">Gestiona y revisa todas las historias clínicas que has creado</p>
            </div>
            <a href="<?= BASE_URL ?>/index.php?accion=crearHistoria" class="btn-new-history">
                <i class="bi bi-plus-circle"></i> Nueva Historia
            </a>
        </div>
    </div>

    <!-- ESTADÍSTICAS -->
    <div class="stats-cards">
        <div class="stat-card total">
            <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
            <div class="stat-label">Total Historias</div>
        </div>
        <div class="stat-card pacientes">
            <div class="stat-value"><?= $stats['pacientes_unicos'] ?? 0 ?></div>
            <div class="stat-label">Pacientes Únicos</div>
        </div>
        <div class="stat-card hoy">
            <div class="stat-value"><?= $stats['hoy'] ?? 0 ?></div>
            <div class="stat-label">Creadas Hoy</div>
        </div>
        <div class="stat-card semana">
            <div class="stat-value"><?= $stats['semana'] ?? 0 ?></div>
            <div class="stat-label">Esta Semana</div>
        </div>
        <div class="stat-card mes">
            <div class="stat-value"><?= $stats['mes'] ?? 0 ?></div>
            <div class="stat-label">Este Mes</div>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="filters-section">
        <form method="GET" action="" class="row g-3">
            <input type="hidden" name="accion" value="doctorHistorias">
            
            <div class="col-md-3">
                <label class="form-label fw-bold"><i class="bi bi-person"></i> Paciente</label>
                <select name="paciente" class="form-select">
                    <option value="">Todos los pacientes</option>
                    <?php foreach ($pacientes as $pac): ?>
                        <option value="<?= $pac['id'] ?>" <?= $filtroPaciente == $pac['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pac['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold"><i class="bi bi-calendar-date"></i> Fecha</label>
                <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($filtroFecha) ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold"><i class="bi bi-search"></i> Buscar</label>
                <input type="text" name="busqueda" class="form-control" 
                       placeholder="Paciente, DNI o diagnóstico..." 
                       value="<?= htmlspecialchars($busqueda) ?>">
            </div>

            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filtrar
                </button>
                <a href="?accion=doctorHistorias" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- LISTA DE HISTORIAS -->
    <?php if (empty($historias)): ?>
        <div class="empty-state">
            <div class="empty-icon">📋</div>
            <h3>No se encontraron historias clínicas</h3>
            <p class="text-muted">No hay historias clínicas que coincidan con los filtros seleccionados</p>
            <a href="<?= BASE_URL ?>/index.php?accion=crearHistoria" class="btn-new-history mt-3">
                <i class="bi bi-plus-circle"></i> Crear Primera Historia
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($historias as $historia): ?>
            <div class="historia-card">
                <div class="historia-header">
                    <div class="paciente-info">
                        <div class="paciente-avatar">
                            <?= strtoupper(substr($historia['paciente_nombre'], 0, 1)) ?>
                        </div>
                        <div class="paciente-detalles">
                            <h5><?= htmlspecialchars($historia['paciente_nombre']) ?></h5>
                            <small>
                                <i class="bi bi-card-text"></i> DNI: <?= htmlspecialchars($historia['paciente_dni']) ?>
                                | <i class="bi bi-calendar-check"></i> Cita: <?= htmlspecialchars($historia['fecha_cita_formateada']) ?>
                            </small>
                        </div>
                    </div>
                    <div class="historia-fecha">
                        <span class="fecha-badge">
                            <i class="bi bi-clock"></i> 
                            <?= htmlspecialchars($historia['fecha_formateada']) ?> 
                            <?= htmlspecialchars($historia['hora_formateada']) ?>
                        </span>
                    </div>
                </div>

                <div class="historia-content">
                    <div class="info-row">
                        <div class="info-item">
                            <div class="info-label"><i class="bi bi-clipboard-heart"></i> Motivo</div>
                            <div class="info-value">
                                <?= htmlspecialchars(substr($historia['motivo'], 0, 80)) ?>
                                <?= strlen($historia['motivo']) > 80 ? '...' : '' ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="bi bi-clipboard-check"></i> Diagnóstico</div>
                            <div class="info-value">
                                <?= htmlspecialchars(substr($historia['diagnostico'], 0, 80)) ?>
                                <?= strlen($historia['diagnostico']) > 80 ? '...' : '' ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($historia['indicaciones']): ?>
                        <div class="info-item mb-2">
                            <div class="info-label"><i class="bi bi-file-medical"></i> Indicaciones</div>
                            <div class="info-value">
                                <?= htmlspecialchars(substr($historia['indicaciones'], 0, 150)) ?>
                                <?= strlen($historia['indicaciones']) > 150 ? '...' : '' ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="historia-actions">
                    <a href="<?= BASE_URL ?>/index.php?accion=verDetalleCita&id=<?= $historia['cita_id'] ?>" 
                       class="btn-action btn-ver">
                        <i class="bi bi-eye"></i> Ver Detalle
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?accion=editarHistoria&id=<?= $historia['id'] ?>" 
                       class="btn-action btn-editar">
                        <i class="bi bi-pencil-square"></i> Editar
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?accion=doctorCrearReceta&paciente_id=<?= $historia['paciente_id'] ?>" 
                       class="btn-action btn-receta">
                        <i class="bi bi-prescription2"></i> Nueva Receta
                    </a>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="mt-3 text-muted text-center">
            <small><i class="bi bi-info-circle"></i> Mostrando <?= count($historias) ?> historia(s) clínica(s)</small>
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>