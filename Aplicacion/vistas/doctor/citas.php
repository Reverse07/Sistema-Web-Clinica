<?php
// =====================================================
// 🩺 Vista: Mis Citas - Dashboard Doctor
// Archivo: Aplicacion/vistas/doctor/citas.php
// =====================================================

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'doctor') {
    header("Location: " . BASE_URL . "/index.php?accion=loginVista");
    exit;
}

// BaseDatos ya está cargado desde el controlador
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

// Filtros
$filtroEstado = $_GET['estado'] ?? 'todas';
$filtroFecha = $_GET['fecha'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';

// Construir consulta base
$sql = "
    SELECT 
        c.id,
        c.fecha,
        TO_CHAR(c.fecha, 'DD/MM/YYYY') as fecha_formateada,
        TO_CHAR(c.fecha, 'HH24:MI') as hora_formateada,
        c.estado,
        u.nombre as paciente_nombre,
        u.telefono as paciente_telefono,
        u.email as paciente_email,
        p.id as paciente_id,
        p.dni as paciente_dni,
        COALESCE(hc.motivo, 'Sin especificar') as motivo,
        COALESCE(hc.diagnostico, 'Pendiente') as diagnostico
    FROM citas c
    INNER JOIN pacientes p ON c.paciente_id = p.id
    INNER JOIN usuarios u ON p.usuario_id = u.id
    LEFT JOIN historia_clinica hc ON c.id = hc.cita_id
    WHERE c.doctor_id = :doctor_id
";

$params = [':doctor_id' => $doctorId];

// Aplicar filtros
if ($filtroEstado !== 'todas') {
    $sql .= " AND LOWER(c.estado) = LOWER(:estado)";  // ✅ AÑADE LOWER() AQUÍ
    $params[':estado'] = $filtroEstado;
}

if (!empty($filtroFecha)) {
    $sql .= " AND DATE(c.fecha) = :fecha";
    $params[':fecha'] = $filtroFecha;
}

if (!empty($busqueda)) {
    $sql .= " AND (u.nombre ILIKE :busqueda OR p.dni ILIKE :busqueda)";
    $params[':busqueda'] = "%$busqueda%";
}

$sql .= " ORDER BY c.fecha DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas rápidas
// Estadísticas rápidas - CORREGIDO PARA MAYÚSCULAS
$stmtStats = $pdo->prepare("
    SELECT 
        COUNT(CASE WHEN LOWER(estado) = 'pendiente' THEN 1 END) as pendientes,
        COUNT(CASE WHEN LOWER(estado) = 'confirmada' THEN 1 END) as confirmadas,
        COUNT(CASE WHEN LOWER(estado) = 'completada' THEN 1 END) as completadas,
        COUNT(CASE WHEN LOWER(estado) = 'cancelada' THEN 1 END) as canceladas,
        COUNT(*) as total
    FROM citas 
    WHERE doctor_id = :doctor_id
");
$stmtStats->execute([':doctor_id' => $doctorId]);
$stats = $stmtStats->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Citas - Doctor</title>
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
            --dark-text: #0f172a;
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

        .stat-card.pendiente { --card-color: var(--warning); }
        .stat-card.confirmada { --card-color: var(--info); }
        .stat-card.completada { --card-color: var(--success); }
        .stat-card.cancelada { --card-color: var(--danger); }
        .stat-card.total { --card-color: var(--primary); }

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

        .table-container {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
        }

        .table {
            margin: 0;
        }

        .table thead {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
        }

        .table thead th {
            border: none;
            padding: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: #f0f9ff;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .badge-estado {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
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
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-ver {
            background: var(--info);
            color: white;
            border: none;
        }

        .btn-ver:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-icon {
            font-size: 5rem;
            opacity: 0.3;
            margin-bottom: 1rem;
        }

        .paciente-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .paciente-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.75rem;
            }

            .stats-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="page-header">
        <h1><i class="bi bi-calendar-check"></i> Mis Citas</h1>
        <p class="mb-0">Gestiona y revisa todas tus citas médicas</p>
    </div>

    <!-- ESTADÍSTICAS -->
    <div class="stats-cards">
        <div class="stat-card pendiente">
            <div class="stat-value"><?= $stats['pendientes'] ?? 0 ?></div>
            <div class="stat-label">Pendientes</div>
        </div>
        <div class="stat-card confirmada">
            <div class="stat-value"><?= $stats['confirmadas'] ?? 0 ?></div>
            <div class="stat-label">Confirmadas</div>
        </div>
        <div class="stat-card completada">
            <div class="stat-value"><?= $stats['completadas'] ?? 0 ?></div>
            <div class="stat-label">Completadas</div>
        </div>
        <div class="stat-card cancelada">
            <div class="stat-value"><?= $stats['canceladas'] ?? 0 ?></div>
            <div class="stat-label">Canceladas</div>
        </div>
        <div class="stat-card total">
            <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
            <div class="stat-label">Total</div>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="filters-section">
        <form method="GET" action="" class="row g-3">
            <input type="hidden" name="accion" value="doctorCitas">
            
            <div class="col-md-3">
                <label class="form-label fw-bold"><i class="bi bi-funnel"></i> Estado</label>
                <select name="estado" class="form-select">
                    <option value="todas" <?= $filtroEstado === 'todas' ? 'selected' : '' ?>>Todas</option>
                    <option value="pendiente" <?= $filtroEstado === 'pendiente' ? 'selected' : '' ?>>Pendientes</option>
                    <option value="confirmada" <?= $filtroEstado === 'confirmada' ? 'selected' : '' ?>>Confirmadas</option>
                    <option value="completada" <?= $filtroEstado === 'completada' ? 'selected' : '' ?>>Completadas</option>
                    <option value="cancelada" <?= $filtroEstado === 'cancelada' ? 'selected' : '' ?>>Canceladas</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold"><i class="bi bi-calendar-date"></i> Fecha</label>
                <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($filtroFecha) ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold"><i class="bi bi-search"></i> Buscar Paciente</label>
                <input type="text" name="busqueda" class="form-control" placeholder="Nombre o DNI..." value="<?= htmlspecialchars($busqueda) ?>">
            </div>

            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filtrar
                </button>
                <a href="?accion=doctorCitas" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- TABLA DE CITAS -->
    <div class="table-container">
        <?php if (empty($citas)): ?>
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <h3>No se encontraron citas</h3>
                <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><i class="bi bi-hash"></i> ID</th>
                            <th><i class="bi bi-person"></i> Paciente</th>
                            <th><i class="bi bi-calendar-event"></i> Fecha</th>
                            <th><i class="bi bi-clock"></i> Hora</th>
                            <th><i class="bi bi-clipboard-pulse"></i> Motivo</th>
                            <th><i class="bi bi-tag"></i> Estado</th>
                            <th><i class="bi bi-gear"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($citas as $cita): ?>
                            <tr>
                                <td><strong>#<?= htmlspecialchars($cita['id']) ?></strong></td>
                                <td>
                                    <div class="paciente-info">
                                        <div class="paciente-avatar">
                                            <?= strtoupper(substr($cita['paciente_nombre'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($cita['paciente_nombre']) ?></div>
                                            <small class="text-muted">DNI: <?= htmlspecialchars($cita['paciente_dni']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($cita['fecha_formateada']) ?></td>
                                <td><?= htmlspecialchars($cita['hora_formateada']) ?></td>
                                <td>
                                    <small><?= htmlspecialchars(substr($cita['motivo'], 0, 50)) ?><?= strlen($cita['motivo']) > 50 ? '...' : '' ?></small>
                                </td>
                                <td>
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
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/index.php?accion=verDetalleCita&id=<?= $cita['id'] ?>" class="btn btn-ver btn-sm">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 text-muted">
                <small><i class="bi bi-info-circle"></i> Mostrando <?= count($citas) ?> cita(s)</small>
            </div>
        <?php endif; ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>