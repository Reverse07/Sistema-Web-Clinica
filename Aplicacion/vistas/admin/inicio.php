<?php
// =====================
// 📊 Vista: Dashboard Administrativo
// =====================
$totalUsuarios = $totalUsuarios ?? 0;
$totalDoctores = $totalDoctores ?? 0;
$totalPacientes = $totalPacientes ?? 0;
$totalCitas = $totalCitas ?? 0;
$citasHoy = $citasHoy ?? 0;
$citasPendientes = $citasPendientes ?? 0;
$citasRecientes = $citasRecientes ?? [];

// Datos para porcentajes/comparativas
$porcentajeCitas = $totalCitas > 0 ? round(($citasHoy / $totalCitas) * 100) : 0;
$porcentajePendientes = $totalCitas > 0 ? round(($citasPendientes / $totalCitas) * 100) : 0;

?>

<style>
        :root {
            --primary: #667eea;
            --primary-dark: #764ba2;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --purple: #a855f7;
            --orange: #f97316;
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

        /* ========================
           ANIMACIONES GLOBALES
           ======================== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7); }
            50% { box-shadow: 0 0 0 15px rgba(102, 126, 234, 0); }
        }

        .animate-fade-in-up { animation: fadeInUp 0.6s ease forwards; }
        .animate-slide-in { animation: slideInLeft 0.6s ease forwards; }

        /* ========================
           HEADER HERO
           ======================== */
        .header-hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 4rem 2rem;
            border-radius: 2rem;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .header-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulse 3s infinite;
        }

        .header-hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
            border-radius: 50%;
        }

        .header-content {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 3rem;
            align-items: center;
        }

        .header-text h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 3.5rem;
            font-weight: 800;
            margin: 0 0 1rem 0;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .header-text p {
            font-size: 1.2rem;
            opacity: 0.95;
            margin: 0;
            font-weight: 500;
        }

        .header-badge {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 1.25rem 2rem;
            border-radius: 1.5rem;
            text-align: center;
            min-width: 250px;
        }

        .header-badge .fecha {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .header-badge .hora {
            font-size: 2.5rem;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
        }

        /* ========================
           STATS GRID
           ======================== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease forwards;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        .stat-card:nth-child(5) { animation-delay: 0.5s; }
        .stat-card:nth-child(6) { animation-delay: 0.6s; }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--card-color);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, var(--card-color) 0%, transparent 70%);
            opacity: 0.08;
            border-radius: 50%;
        }

        .stat-card.primary { --card-color: var(--primary); border-color: var(--primary); }
        .stat-card.success { --card-color: var(--success); border-color: var(--success); }
        .stat-card.warning { --card-color: var(--warning); border-color: var(--warning); }
        .stat-card.danger { --card-color: var(--danger); border-color: var(--danger); }
        .stat-card.info { --card-color: var(--info); border-color: var(--info); }
        .stat-card.purple { --card-color: var(--purple); border-color: var(--purple); }

        .stat-card:hover {
            transform: translateY(-12px);
            box-shadow: var(--shadow-md);
            border-color: var(--card-color);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .stat-icon {
            width: 80px;
            height: 80px;
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            background: linear-gradient(135deg, var(--card-color) 0%, rgba(102, 126, 234, 0.5) 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1) rotateY(10deg);
        }

        .stat-label {
            font-size: 0.9rem;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .stat-value {
            font-size: 3rem;
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            color: var(--card-color);
            margin: 0 0 1rem 0;
            position: relative;
            z-index: 1;
        }

        .stat-change {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .stat-change.positive { color: var(--success); }
        .stat-change.negative { color: var(--danger); }

        /* Progress bar mini */
        .progress-mini {
            height: 6px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 1rem;
            position: relative;
            z-index: 1;
        }

        .progress-mini-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--card-color) 0%, rgba(102, 126, 234, 0.5) 100%);
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        /* ========================
           QUICK ACTIONS
           ======================== */
        .quick-actions {
            margin-bottom: 3rem;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .section-header h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            color: var(--dark-text);
        }

        .section-header::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(to right, #e2e8f0, transparent);
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.5rem;
        }

        .action-btn {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 1.25rem;
            padding: 2rem 1.5rem;
            text-align: center;
            text-decoration: none;
            color: var(--dark-text);
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            z-index: -1;
            transition: left 0.4s ease;
        }

        .action-btn:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-md);
            color: white;
            border-color: var(--primary);
        }

        .action-btn:hover::before {
            left: 0;
        }

        .action-icon {
            font-size: 2.5rem;
            transition: all 0.3s ease;
        }

        .action-btn:hover .action-icon {
            transform: scale(1.2) rotateY(360deg);
        }

        /* ========================
           RECENT ACTIVITY TABLE
           ======================== */
        .activity-section {
            background: white;
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: var(--shadow-sm);
        }

        .table-responsive {
            border-radius: 1rem;
            overflow: hidden;
        }

        .table {
            margin: 0;
            font-size: 0.95rem;
        }

        .table thead {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
        }

        .table thead th {
            border: none;
            padding: 1.5rem 1.25rem;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: 'Poppins', sans-serif;
        }

        .table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: #f8f9ff;
            transform: scale(1.01);
            box-shadow: inset 0 4px 10px rgba(102, 126, 234, 0.1);
        }

        .table tbody td {
            padding: 1.5rem 1.25rem;
            vertical-align: middle;
            color: #334155;
        }

        .id-badge {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            font-weight: 700;
            font-size: 0.85rem;
            display: inline-block;
            font-family: 'Poppins', sans-serif;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-family: 'Poppins', sans-serif;
        }

        .badge-confirmada {
            background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%);
            color: #1e4620;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
        }

        .badge-pendiente {
            background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
            color: #5a3e00;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.2);
        }

        .badge-cancelada {
            background: linear-gradient(135deg, #fab1a0 0%, #fd79a8 100%);
            color: #5a1a1a;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2);
        }

        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
        }

        .empty-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            color: #64748b;
            margin-bottom: 0.5rem;
            font-family: 'Poppins', sans-serif;
        }

        .empty-state p {
            color: #94a3b8;
            margin: 0;
        }

        /* ========================
           RESPONSIVE
           ======================== */
        @media (max-width: 1024px) {
            .header-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .header-text h1 {
                font-size: 2.5rem;
                justify-content: center;
            }

            .header-badge {
                margin: 0 auto;
            }
        }

        @media (max-width: 768px) {
            .header-hero {
                padding: 2.5rem 1.5rem;
            }

            .header-text h1 {
                font-size: 1.8rem;
            }

            .section-header::after {
                display: none;
            }

            .table thead th,
            .table tbody td {
                padding: 1rem 0.75rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 480px) {
            .header-text h1 {
                gap: 0.5rem;
                font-size: 1.5rem;
            }

            .stat-value {
                font-size: 2rem;
            }

            .action-buttons {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .action-btn {
                padding: 1.5rem 1rem;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">

    <!-- ========================
         HEADER HERO
         ======================== -->
    <div class="header-hero">
        <div class="header-content">
            <div class="header-text">
                <h1>
                    <i class="bi bi-graph-up-arrow"></i>
                    Panel Control
                </h1>
                <p>Bienvenido de nuevo al sistema de Reverse</p>
            </div>
            <div class="header-badge">
                <div class="fecha">
                    <i class="bi bi-calendar-check"></i>
                    <span><?= ucfirst((new DateTime())->format('l, d \d\e F')) ?></span>
                </div>
                <div class="hora" id="current-time">00:00:00</div>
            </div>
        </div>
    </div>

    <!-- ========================
         STATS GRID
         ======================== -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Usuarios</div>
                    <p class="stat-value"><?= number_format($totalUsuarios) ?></p>
                </div>
                <div class="stat-icon">👥</div>
            </div>
            <div class="stat-change positive">
                <i class="bi bi-arrow-up"></i>
                <span>Activos</span>
            </div>
            <div class="progress-mini">
                <div class="progress-mini-bar" style="width: 85%;"></div>
            </div>
        </div>

        <div class="stat-card orange">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Doctores</div>
                    <p class="stat-value"><?= number_format($totalDoctores) ?></p>
                </div>
                <div class="stat-icon">🩺</div>
            </div>
            <div class="stat-change positive">
                <i class="bi bi-arrow-up"></i>
                <span>Disponibles</span>
            </div>
            <div class="progress-mini">
                <div class="progress-mini-bar" style="width: 72%; background: linear-gradient(90deg, #f97316 0%, rgba(249, 115, 22, 0.5) 100%);"></div>
            </div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Pacientes</div>
                    <p class="stat-value"><?= number_format($totalPacientes) ?></p>
                </div>
                <div class="stat-icon">👨‍⚕️</div>
            </div>
            <div class="stat-change positive">
                <i class="bi bi-arrow-up"></i>
                <span>Registrados</span>
            </div>
            <div class="progress-mini">
                <div class="progress-mini-bar" style="width: 65%; background: linear-gradient(90deg, #a855f7 0%, rgba(168, 85, 247, 0.5) 100%);"></div>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Citas</div>
                    <p class="stat-value" id="totalCitasValue"><?= number_format($totalCitas) ?></p>
                </div>
                <div class="stat-icon">📅</div>
            </div>
            <div class="stat-change positive">
                <i class="bi bi-arrow-up"></i>
                <span>+15% este mes</span>
            </div>
            <div class="progress-mini">
                <div class="progress-mini-bar" style="width: 90%; background: linear-gradient(90deg, #10b981 0%, rgba(16, 185, 129, 0.5) 100%);"></div>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Citas Hoy</div>
                    <p class="stat-value"><?= number_format($citasHoy) ?></p>
                </div>
                <div class="stat-icon">🕐</div>
            </div>
            <div class="stat-change">
                <i class="bi bi-info-circle"></i>
                <span><?= $porcentajeCitas ?>% del total</span>
            </div>
            <div class="progress-mini">
                <div class="progress-mini-bar" style="width: <?= $porcentajeCitas ?>%; background: linear-gradient(90deg, #f59e0b 0%, rgba(245, 158, 11, 0.5) 100%);"></div>
            </div>
        </div>

        <div class="stat-card danger">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Pendientes</div>
                    <p class="stat-value"><?= number_format($citasPendientes) ?></p>
                </div>
                <div class="stat-icon">⏳</div>
            </div>
            <div class="stat-change negative">
                <i class="bi bi-exclamation-circle"></i>
                <span><?= $porcentajePendientes ?>% requieren atención</span>
            </div>
            <div class="progress-mini">
                <div class="progress-mini-bar" style="width: <?= $porcentajePendientes ?>%; background: linear-gradient(90deg, #ef4444 0%, rgba(239, 68, 68, 0.5) 100%);"></div>
            </div>
        </div>
    </div>

    <!-- ========================
         QUICK ACTIONS
         ======================== -->
    <div class="quick-actions">
        <div class="section-header">
            <h2>⚡ Acciones Rápidas</h2>
        </div>
        <div class="action-buttons">
            <a href="?accion=gestionarUsuarios" class="action-btn">
                <span class="action-icon">👥</span>
                <span>Gestionar Usuarios</span>
            </a>
            <a href="?accion=gestionarDoctores" class="action-btn">
                <span class="action-icon">🩺</span>
                <span>Gestionar Doctores</span>
            </a>
            <a href="?accion=gestionarPacientes" class="action-btn">
                <span class="action-icon">👨‍⚕️</span>
                <span>Gestionar Pacientes</span>
            </a>
            <a href="?accion=gestionarCitas" class="action-btn">
                <span class="action-icon">📅</span>
                <span>Gestionar Citas</span>
            </a>
            <a href="?accion=gestionarFacturas" class="action-btn">
                <span class="action-icon">💳</span>
                <span>Gestionar Facturas</span>
            </a>
            <a href="?accion=verReportes" class="action-btn">
                <span class="action-icon">📊</span>
                <span>Ver Reportes</span>
            </a>
        </div>
    </div>

    <!-- ========================
         RECENT ACTIVITY
         ======================== -->
    <div class="activity-section">
        <div class="section-header">
            <h2>📋 Citas Recientes</h2>
        </div>

        <?php if (empty($citasRecientes)): ?>
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <h3>No hay citas registradas</h3>
                <p>Las citas aparecerán aquí una vez que sean creadas en el sistema</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><i class="bi bi-hash"></i> ID</th>
                            <th><i class="bi bi-person"></i> Paciente</th>
                            <th><i class="bi bi-stethoscope"></i> Doctor</th>
                            <th><i class="bi bi-calendar-event"></i> Fecha</th>
                            <th><i class="bi bi-clock"></i> Hora</th>
                            <th><i class="bi bi-tag"></i> Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($citasRecientes, 0, 10) as $cita): ?>
                            <tr>
                                <td><span class="id-badge">#<?= htmlspecialchars($cita['id']) ?></span></td>
                                <td><strong><?= htmlspecialchars($cita['paciente'] ?? 'N/A') ?></strong></td>
                                <td><?= htmlspecialchars($cita['doctor'] ?? 'N/A') ?></td>
                                <td><?= date('d/m/Y', strtotime($cita['fecha'])) ?></td>
                                <td><?= date('h:i A', strtotime($cita['fecha'])) ?></td>
                                <td>
                                    <?php 
                                    $estado = strtolower($cita['estado']);
                                    $claseEstado = 'status-badge ';
                                    $iconoEstado = '';
                                    if (str_contains($estado, 'confirmada')) {
                                        $claseEstado .= 'badge-confirmada';
                                        $iconoEstado = '✅';
                                    } elseif (str_contains($estado, 'pendiente')) {
                                        $claseEstado .= 'badge-pendiente';
                                        $iconoEstado = '⏳';
                                    } else {
                                        $claseEstado .= 'badge-cancelada';
                                        $iconoEstado = '❌';
                                    }
                                    ?>
                                    <span class="<?= $claseEstado ?>">
                                        <?= $iconoEstado ?>
                                        <?= htmlspecialchars($cita['estado']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Reloj en tiempo real
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('current-time').textContent = `${hours}:${minutes}:${seconds}`;
    }

    // Actualizar reloj cada segundo
    updateClock();
    setInterval(updateClock, 1000);

    // Auto-refresh del contador de citas cada 30 segundos
    setInterval(function() {
        fetch('?accion=dashboardAdmin&ajax=1')
            .then(response => response.json())
            .then(data => {
                if (data.totalCitas) {
                    document.getElementById('totalCitasValue').textContent = 
                        new Intl.NumberFormat().format(data.totalCitas);
                }
            })
            .catch(error => console.log('Error al actualizar:', error));
    }, 30000);

    // Animaciones al cargar
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.stat-card, .action-btn');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('animate-fade-in-up');
        });
    });
</script>