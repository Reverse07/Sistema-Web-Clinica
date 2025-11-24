<?php
// =====================
// 👨‍⚕️ Vista: Dashboard del Paciente PREMIUM
// =====================
$totalCitas = $totalCitas ?? 0;
$citasPendientes = $citasPendientes ?? 0;
$citasCompletadas = $citasCompletadas ?? 0;
$facturasPorPagar = $facturasPorPagar ?? 0;
$proximasCitas = $proximasCitas ?? [];
$nombrePaciente = $_SESSION['nombre'] ?? 'Paciente';
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

    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
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
        justify-content: center;
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
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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

    .stat-card.primary { --card-color: var(--info); border-color: var(--info); }
    .stat-card.warning { --card-color: var(--warning); border-color: var(--warning); }
    .stat-card.success { --card-color: var(--success); border-color: var(--success); }
    .stat-card.danger { --card-color: var(--danger); border-color: var(--danger); }

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
        color: #64748b;
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
       CITAS SECTION
       ======================== */
    .citas-section {
        background: white;
        border-radius: 1.5rem;
        padding: 2.5rem;
        box-shadow: var(--shadow-sm);
    }

    .cita-card {
        background: #f8fafc;
        border-radius: 1.25rem;
        padding: 2rem;
        margin-bottom: 1.5rem;
        border-left: 5px solid var(--primary);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .cita-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: -50px;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .cita-card:hover {
        transform: translateX(10px);
        box-shadow: var(--shadow-sm);
        background: white;
    }

    .cita-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .doctor-info h4 {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        color: var(--dark-text);
    }

    .doctor-info p {
        font-size: 0.95rem;
        color: #64748b;
        margin: 0;
    }

    .status-badge {
        padding: 0.6rem 1.25rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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

    .cita-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.95rem;
        color: #334155;
    }

    .detail-item i {
        font-size: 1.25rem;
        color: var(--primary);
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
        .dashboard-container {
            padding: 0 1rem;
        }

        .header-hero {
            padding: 2.5rem 1.5rem;
        }

        .header-text h1 {
            font-size: 1.8rem;
        }

        .section-header::after {
            display: none;
        }

        .stat-value {
            font-size: 2rem;
        }

        .action-buttons {
            grid-template-columns: repeat(2, 1fr);
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

<div class="dashboard-container">

    <!-- HEADER HERO -->
    <div class="header-hero animate-fade-in-up">
        <div class="header-content">
            <div class="header-text">
                <h1>👋 ¡Hola, <?= htmlspecialchars($nombrePaciente) ?>!</h1>
                <p>Bienvenido a tu portal personal de salud</p>
            </div>
            <div class="header-badge">
                <div class="fecha">
                    <i class="bi bi-calendar-check"></i>
                    <span id="current-date">Fecha</span>
                </div>
                <div class="hora" id="current-time">00:00</div>
            </div>
        </div>
    </div>

    <!-- STATS GRID -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-header">
                <div class="stat-icon">📊</div>
            </div>
            <div class="stat-label">Total Citas</div>
            <div class="stat-value"><?= number_format($totalCitas) ?></div>
            <div class="stat-change">
                <i class="bi bi-graph-up"></i>
                <span>Todas las citas</span>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-header">
                <div class="stat-icon">⏳</div>
            </div>
            <div class="stat-label">Pendientes</div>
            <div class="stat-value"><?= number_format($citasPendientes) ?></div>
            <div class="stat-change">
                <i class="bi bi-clock-history"></i>
                <span>Por confirmar</span>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-header">
                <div class="stat-icon">✅</div>
            </div>
            <div class="stat-label">Completadas</div>
            <div class="stat-value"><?= number_format($citasCompletadas) ?></div>
            <div class="stat-change">
                <i class="bi bi-check-circle"></i>
                <span>Finalizadas</span>
            </div>
        </div>

        <div class="stat-card danger">
            <div class="stat-header">
                <div class="stat-icon">💳</div>
            </div>
            <div class="stat-label">Por Pagar</div>
            <div class="stat-value"><?= number_format($facturasPorPagar) ?></div>
            <div class="stat-change">
                <i class="bi bi-exclamation-circle"></i>
                <span>Facturas pendientes</span>
            </div>
        </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="quick-actions">
        <div class="section-header">
            <h2>⚡ Acciones Rápidas</h2>
        </div>
        <div class="action-buttons">
            <a href="<?= BASE_URL ?>/index.php?accion=crearCitaPaciente" class="action-btn">
                <span class="action-icon">➕</span>
                <span>Nueva Cita</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?accion=misCitas" class="action-btn">
                <span class="action-icon">📅</span>
                <span>Mis Citas</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?accion=miHistorial" class="action-btn">
                <span class="action-icon">📁</span>
                <span>Historial</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?accion=misRecetas" class="action-btn">
                <span class="action-icon">💊</span>
                <span>Recetas</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?accion=misFacturas" class="action-btn">
                <span class="action-icon">💳</span>
                <span>Facturas</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?accion=perfil" class="action-btn">
                <span class="action-icon">👤</span>
                <span>Mi Perfil</span>
            </a>
        </div>
    </div>

    <!-- PROXIMAS CITAS -->
    <div class="citas-section">
        <div class="section-header">
            <h2>📅 Próximas Citas</h2>
        </div>

        <?php if (empty($proximasCitas)): ?>
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <h3>No tienes citas programadas</h3>
                <p>Agenda una nueva cita con tu doctor de confianza</p>
            </div>
        <?php else: ?>
            <?php foreach ($proximasCitas as $cita): ?>
                <div class="cita-card">
                    <div class="cita-header">
                        <div class="doctor-info">
                            <h4>Dr. <?= htmlspecialchars($cita['doctor_nombre'] ?? 'Por asignar') ?></h4>
                            <p><?= htmlspecialchars($cita['especialidad'] ?? 'Medicina General') ?></p>
                        </div>
                        <span class="status-badge badge-<?= strtolower(str_replace(' ', '', $cita['estado'])) ?>">
                            <?= htmlspecialchars($cita['estado']) ?>
                        </span>
                    </div>
                    <div class="cita-details">
                        <div class="detail-item">
                            <i class="bi bi-calendar-event"></i>
                            <span><?= date('d/m/Y', strtotime($cita['fecha'])) ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-clock"></i>
                            <span><?= date('h:i A', strtotime($cita['fecha'])) ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-geo-alt"></i>
                            <span>Consultorio <?= htmlspecialchars($cita['consultorio'] ?? '1') ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

</div>

<script>
// Reloj en tiempo real
function updateClock() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    document.getElementById('current-time').textContent = `${hours}:${minutes}`;
    
    const days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    const dateStr = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]}`;
    document.getElementById('current-date').textContent = dateStr;
}

updateClock();
setInterval(updateClock, 1000);
</script>