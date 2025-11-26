<?php
// =====================
// 🩺 Vista: Dashboard Doctor
// =====================
$totalCitas = $totalCitas ?? 0;
$citasHoy = $citasHoy ?? 0;
$citasPendientes = $citasPendientes ?? 0;
$citasCompletadas = $citasCompletadas ?? 0;
$pacientesAtendidos = $pacientesAtendidos ?? 0;
$citasRecientes = $citasRecientes ?? [];
$proximasCitas = $proximasCitas ?? [];
$doctorNombre = $doctorNombre ?? 'Doctor';
$especialidad = $especialidad ?? 'Medicina General';

// Calcular porcentajes
$porcentajeCitasHoy = $totalCitas > 0 ? round(($citasHoy / $totalCitas) * 100) : 0;
$porcentajeCompletadas = $totalCitas > 0 ? round(($citasCompletadas / $totalCitas) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Doctor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0ea5e9;
            --primary-dark: #0284c7;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --purple: #a855f7;
            --teal: #14b8a6;
            --light-bg: #f8fafc;
            --dark-text: #0f172a;
            --shadow-sm: 0 4px 15px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 12px 30px rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 20px 50px rgba(0, 0, 0, 0.2);
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        /* ========================
           ANIMACIONES
           ======================== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(14, 165, 233, 0.7); }
            50% { box-shadow: 0 0 0 15px rgba(14, 165, 233, 0); }
        }

        .animate-fade-in { animation: fadeInUp 0.6s ease forwards; }

        /* ========================
           HEADER HERO
           ======================== */
        .doctor-hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 3rem 2rem;
            border-radius: 2rem;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .doctor-hero::before {
            content: '🩺';
            position: absolute;
            font-size: 20rem;
            opacity: 0.1;
            top: -5rem;
            right: -5rem;
            animation: pulse 3s infinite;
        }

        .doctor-info {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 2rem;
            align-items: center;
        }

        .doctor-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            border: 4px solid rgba(255,255,255,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            backdrop-filter: blur(10px);
        }

        .doctor-text h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0 0 0.5rem 0;
        }

        .doctor-text .specialty {
            font-size: 1.2rem;
            opacity: 0.95;
            font-weight: 500;
        }

        .time-badge {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 1rem 1.5rem;
            border-radius: 1rem;
            text-align: center;
            min-width: 200px;
        }

        .time-badge .date {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
        }

        .time-badge .time {
            font-size: 2rem;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
        }

        /* ========================
           STATS CARDS
           ======================== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--card-color);
        }

        .stat-card.primary { --card-color: var(--primary); }
        .stat-card.success { --card-color: var(--success); }
        .stat-card.warning { --card-color: var(--warning); }
        .stat-card.purple { --card-color: var(--purple); }
        .stat-card.teal { --card-color: var(--teal); }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-md);
            border-color: var(--card-color);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 1rem;
            background: linear-gradient(135deg, var(--card-color) 0%, rgba(14, 165, 233, 0.5) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .stat-value {
            font-size: 2.5rem;
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            color: var(--card-color);
            margin: 0.5rem 0;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-trend {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        .stat-trend.up { color: var(--success); }
        .stat-trend.down { color: var(--danger); }

        .progress-bar-mini {
            height: 6px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 1rem;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--card-color) 0%, rgba(14, 165, 233, 0.5) 100%);
            transition: width 0.5s ease;
        }

        /* ========================
           QUICK ACTIONS
           ======================== */
        .quick-actions {
            margin-bottom: 3rem;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .section-title h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            color: var(--dark-text);
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(to right, #e2e8f0, transparent);
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .action-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 1.25rem;
            padding: 2rem 1.5rem;
            text-align: center;
            text-decoration: none;
            color: var(--dark-text);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            transition: left 0.4s ease;
            z-index: 0;
        }

        .action-card:hover::before {
            left: 0;
        }

        .action-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-md);
            color: white;
            border-color: var(--primary);
        }

        .action-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease;
        }

        .action-card:hover .action-icon {
            transform: scale(1.2) rotateY(360deg);
        }

        .action-label {
            font-weight: 600;
            font-size: 0.95rem;
            position: relative;
            z-index: 1;
        }

        /* ========================
           APPOINTMENTS TABLE
           ======================== */
        .appointments-section {
            background: white;
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }

        .table-responsive {
            border-radius: 1rem;
            overflow: hidden;
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
            padding: 1.25rem 1rem;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: #f0f9ff;
            transform: scale(1.01);
        }

        .table tbody td {
            padding: 1.25rem 1rem;
            vertical-align: middle;
        }

        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .badge-confirmed {
            background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%);
            color: #1e4620;
        }

        .badge-pending {
            background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
            color: #5a3e00;
        }

        .badge-cancelled {
            background: linear-gradient(135deg, #fab1a0 0%, #fd79a8 100%);
            color: #5a1a1a;
        }

        .badge-completed {
            background: linear-gradient(135deg, #a8e6cf 0%, #3bb2d0 100%);
            color: #0a3d3d;
        }

        .patient-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .patient-avatar {
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

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        /* ========================
           RESPONSIVE
           ======================== */
        @media (max-width: 1024px) {
            .doctor-info {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .time-badge {
                margin: 0 auto;
            }
        }

        @media (max-width: 768px) {
            .doctor-hero {
                padding: 2rem 1.5rem;
            }

            .doctor-text h1 {
                font-size: 1.8rem;
            }

            .stat-value {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">

    <!-- ========================
         DOCTOR HERO HEADER
         ======================== -->
    <div class="doctor-hero animate-fade-in">
        <div class="doctor-info">
            <div class="doctor-avatar">
                🩺
            </div>
            <div class="doctor-text">
                <h1>Dr. <?= htmlspecialchars($doctorNombre) ?></h1>
                <p class="specialty">📋 <?= htmlspecialchars($especialidad) ?></p>
            </div>
            <div class="time-badge">
                <div class="date">
                    <i class="bi bi-calendar-check"></i>
                    <span><?= ucfirst((new DateTime())->format('l, d M')) ?></span>
                </div>
                <div class="time" id="current-time">00:00:00</div>
            </div>
        </div>
    </div>

    <!-- ========================
         STATISTICS CARDS
         ======================== -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Citas de Hoy</div>
                    <p class="stat-value"><?= number_format($citasHoy) ?></p>
                    <div class="stat-trend up">
                        <i class="bi bi-arrow-up"></i>
                        <span><?= $porcentajeCitasHoy ?>% del total</span>
                    </div>
                </div>
                <div class="stat-icon">📅</div>
            </div>
            <div class="progress-bar-mini">
                <div class="progress-fill" style="width: <?= $porcentajeCitasHoy ?>%;"></div>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Citas Completadas</div>
                    <p class="stat-value"><?= number_format($citasCompletadas) ?></p>
                    <div class="stat-trend up">
                        <i class="bi bi-check-circle"></i>
                        <span><?= $porcentajeCompletadas ?>% completadas</span>
                    </div>
                </div>
                <div class="stat-icon">✅</div>
            </div>
            <div class="progress-bar-mini">
                <div class="progress-fill" style="width: <?= $porcentajeCompletadas ?>%;"></div>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Pendientes</div>
                    <p class="stat-value"><?= number_format($citasPendientes) ?></p>
                    <div class="stat-trend">
                        <i class="bi bi-clock-history"></i>
                        <span>Por confirmar</span>
                    </div>
                </div>
                <div class="stat-icon">⏳</div>
            </div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Citas</div>
                    <p class="stat-value"><?= number_format($totalCitas) ?></p>
                    <div class="stat-trend up">
                        <i class="bi bi-graph-up"></i>
                        <span>Este mes</span>
                    </div>
                </div>
                <div class="stat-icon">📊</div>
            </div>
        </div>

        <div class="stat-card teal">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Pacientes</div>
                    <p class="stat-value"><?= number_format($pacientesAtendidos) ?></p>
                    <div class="stat-trend up">
                        <i class="bi bi-people"></i>
                        <span>Atendidos</span>
                    </div>
                </div>
                <div class="stat-icon">👥</div>
            </div>
        </div>
    </div>

    <!-- ========================
         QUICK ACTIONS
         ======================== -->
    <div class="quick-actions">
        <div class="section-title">
            <h2>⚡ Acciones Rápidas</h2>
        </div>
        <div class="actions-grid">
            <a href="?accion=doctorCitas" class="action-card">
                <div class="action-icon">📅</div>
                <div class="action-label">Mis Citas</div>
            </a>
            <a href="?accion=doctorHistorias" class="action-card">
                <div class="action-icon">📋</div>
                <div class="action-label">Historias Clínicas</div>
            </a>
            <a href="?accion=doctorRecetas" class="action-card">
                <div class="action-icon">💊</div>
                <div class="action-label">Recetas</div>
            </a>
            <a href="?accion=doctorPerfil" class="action-card">
                <div class="action-icon">👤</div>
                <div class="action-label">Mi Perfil</div>
            </a>
        </div>
    </div>

    <!-- ========================
         PRÓXIMAS CITAS
         ======================== -->
    <div class="appointments-section">
        <div class="section-title">
            <h2>📅 Próximas Citas</h2>
        </div>

        <?php if (empty($proximasCitas)): ?>
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <h3>No hay citas programadas</h3>
                <p class="text-muted">Las próximas citas aparecerán aquí</p>
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
                            <th><i class="bi bi-tag"></i> Estado</th>
                            <th><i class="bi bi-gear"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($proximasCitas, 0, 10) as $cita): ?>
                            <tr>
                                <td><strong>#<?= htmlspecialchars($cita['id']) ?></strong></td>
                                <td>
                                    <div class="patient-info">
                                        <div class="patient-avatar">
                                            <?= strtoupper(substr($cita['paciente'] ?? 'P', 0, 1)) ?>
                                        </div>
                                        <strong><?= htmlspecialchars($cita['paciente'] ?? 'N/A') ?></strong>
                                    </div>
                                </td>
                                <td><?= date('d/m/Y', strtotime($cita['fecha'])) ?></td>
                                <td><?= date('h:i A', strtotime($cita['fecha'])) ?></td>
                                <td>
                                    <?php 
                                    $estado = strtolower($cita['estado']);
                                    $badgeClass = 'badge-pending';
                                    $icon = '⏳';
                                    if (str_contains($estado, 'confirmada')) {
                                        $badgeClass = 'badge-confirmed';
                                        $icon = '✅';
                                    } elseif (str_contains($estado, 'completada')) {
                                        $badgeClass = 'badge-completed';
                                        $icon = '✔️';
                                    } elseif (str_contains($estado, 'cancelada')) {
                                        $badgeClass = 'badge-cancelled';
                                        $icon = '❌';
                                    }
                                    ?>
                                    <span class="badge-status <?= $badgeClass ?>">
                                        <?= $icon ?> <?= htmlspecialchars($cita['estado']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="?accion=verDetalleCita&id=<?= $cita['id'] ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div>

<script>
    // Reloj en tiempo real
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('current-time').textContent = `${hours}:${minutes}:${seconds}`;
    }

    updateClock();
    setInterval(updateClock, 1000);

    // Animaciones al cargar
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.stat-card, .action-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('animate-fade-in');
        });
    });
</script>

</body>
</html>