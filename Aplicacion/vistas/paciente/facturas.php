<?php
// =====================
// 💳 Vista: Mis Facturas del Paciente PREMIUM
// =====================
$facturas = $facturas ?? [];
$nombrePaciente = $_SESSION['nombre'] ?? 'Paciente';

// Calcular estadísticas
$totalFacturas = count($facturas);
$totalPagado = 0;
$totalPendiente = 0;
$facturasPendientes = 0;
$facturasPagadas = 0;

foreach ($facturas as $factura) {
    $estado = strtolower($factura->getEstado());
    if (str_contains($estado, 'pag') || str_contains($estado, 'completada')) {
        $totalPagado += $factura->getMonto();
        $facturasPagadas++;
    } else {
        $totalPendiente += $factura->getMonto();
        $facturasPendientes++;
    }
}
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

    .facturas-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    /* ========================
       ANIMACIONES
       ======================== */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(30px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .animate-fade-in-up { animation: fadeInUp 0.6s ease forwards; }
    .animate-slide-in { animation: slideInRight 0.6s ease forwards; }

    /* ========================
       HEADER
       ======================== */
    .page-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 3rem 2.5rem;
        border-radius: 2rem;
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .header-content {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-title {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .header-icon {
        font-size: 4rem;
    }

    .header-title h1 {
        font-family: 'Poppins', sans-serif;
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0 0 0.5rem 0;
    }

    .header-title p {
        font-size: 1.1rem;
        opacity: 0.95;
        margin: 0;
    }

    .btn-back {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255,255,255,0.3);
        color: white;
        padding: 1rem 2rem;
        border-radius: 1rem;
        text-decoration: none;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background: white;
        color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    /* ========================
       STATS SUMMARY
       ======================== */
    .stats-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .stat-box {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        box-shadow: var(--shadow-sm);
        border-left: 5px solid var(--stat-color);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-box::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -30px;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, var(--stat-color) 0%, transparent 70%);
        opacity: 0.1;
        border-radius: 50%;
    }

    .stat-box:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-md);
    }

    .stat-box.primary { --stat-color: var(--info); }
    .stat-box.success { --stat-color: var(--success); }
    .stat-box.warning { --stat-color: var(--warning); }
    .stat-box.danger { --stat-color: var(--danger); }

    .stat-content {
        position: relative;
        z-index: 1;
    }

    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2.5rem;
        font-family: 'Poppins', sans-serif;
        font-weight: 800;
        color: var(--stat-color);
        margin: 0 0 0.5rem 0;
    }

    .stat-subtitle {
        font-size: 0.85rem;
        color: #64748b;
    }

    /* ========================
       FILTERS
       ======================== */
    .filters-section {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-sm);
    }

    .filters-grid {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 1.5rem;
        align-items: end;
    }

    .filter-group label {
        display: block;
        font-weight: 600;
        color: var(--dark-text);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .filter-input {
        width: 100%;
        padding: 0.875rem 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .filter-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn-filter {
        background: var(--primary);
        color: white;
        border: none;
        padding: 0.875rem 2rem;
        border-radius: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-filter:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }

    /* ========================
       FACTURAS TABLE
       ======================== */
    .facturas-section {
        background: white;
        border-radius: 1.5rem;
        padding: 2.5rem;
        box-shadow: var(--shadow-sm);
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

    .factura-card {
        background: #f8fafc;
        border-radius: 1.25rem;
        padding: 2rem;
        margin-bottom: 1.5rem;
        border-left: 5px solid var(--primary);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .factura-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: -50px;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .factura-card:hover {
        transform: translateX(10px);
        box-shadow: var(--shadow-sm);
        background: white;
    }

    .factura-card.pendiente {
        border-left-color: var(--warning);
    }

    .factura-card.pagada {
        border-left-color: var(--success);
    }

    .factura-card.vencida {
        border-left-color: var(--danger);
    }

    .factura-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .factura-info h3 {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        color: var(--dark-text);
    }

    .factura-date {
        font-size: 0.9rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .factura-monto {
        text-align: right;
    }

    .monto-label {
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 0.25rem;
    }

    .monto-value {
        font-size: 2rem;
        font-family: 'Poppins', sans-serif;
        font-weight: 800;
        color: var(--primary);
    }

    .factura-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
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

    .factura-actions {
        display: flex;
        gap: 0.75rem;
        position: relative;
        z-index: 1;
    }

    .btn-action {
        padding: 0.75rem 1.5rem;
        border-radius: 0.875rem;
        border: 2px solid #e2e8f0;
        background: white;
        color: var(--dark-text);
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-action:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }

    .btn-action.view:hover {
        background: var(--info);
        border-color: var(--info);
        color: white;
    }

    .btn-action.download:hover {
        background: var(--success);
        border-color: var(--success);
        color: white;
    }

    .status-badge {
        padding: 0.6rem 1.25rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-pagada {
        background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%);
        color: #1e4620;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
    }

    .badge-pendiente {
        background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
        color: #5a3e00;
        box-shadow: 0 4px 10px rgba(245, 158, 11, 0.2);
    }

    .badge-vencida {
        background: linear-gradient(135deg, #fab1a0 0%, #fd79a8 100%);
        color: #5a1a1a;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2);
    }

    /* ========================
       EMPTY STATE
       ======================== */
    .empty-state {
        text-align: center;
        padding: 6rem 2rem;
    }

    .empty-icon {
        font-size: 6rem;
        margin-bottom: 1.5rem;
        opacity: 0.3;
    }

    .empty-state h3 {
        font-size: 1.75rem;
        color: #64748b;
        margin-bottom: 1rem;
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
    }

    .empty-state p {
        color: #94a3b8;
        font-size: 1.1rem;
        margin-bottom: 2rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        border: none;
        padding: 1rem 2.5rem;
        border-radius: 1rem;
        font-weight: 600;
        font-size: 1rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
    }

    /* ========================
       RESPONSIVE
       ======================== */
    @media (max-width: 1024px) {
        .filters-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .facturas-container {
            padding: 0 1rem;
        }

        .page-header {
            padding: 2rem 1.5rem;
        }

        .header-content {
            flex-direction: column;
            gap: 1.5rem;
            text-align: center;
        }

        .header-title {
            flex-direction: column;
        }

        .header-title h1 {
            font-size: 1.75rem;
        }

        .stats-summary {
            grid-template-columns: 1fr;
        }

        .factura-header {
            flex-direction: column;
            gap: 1rem;
        }

        .factura-monto {
            text-align: left;
        }

        .factura-actions {
            flex-direction: column;
        }

        .btn-action {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="facturas-container">

    <!-- HEADER -->
    <div class="page-header animate-fade-in-up">
        <div class="header-content">
            <div class="header-title">
                <div class="header-icon">💳</div>
                <div>
                    <h1>Mis Facturas</h1>
                    <p>Gestiona tus pagos y comprobantes</p>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/index.php?accion=dashboardPaciente" class="btn-back">
                <i class="bi bi-arrow-left"></i>
                Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- STATS SUMMARY -->
    <div class="stats-summary">
        <div class="stat-box primary">
            <div class="stat-content">
                <div class="stat-icon">📊</div>
                <div class="stat-label">Total Facturas</div>
                <div class="stat-value"><?= $totalFacturas ?></div>
                <div class="stat-subtitle">Todas tus facturas</div>
            </div>
        </div>

        <div class="stat-box success">
            <div class="stat-content">
                <div class="stat-icon">✅</div>
                <div class="stat-label">Pagadas</div>
                <div class="stat-value"><?= $facturasPagadas ?></div>
                <div class="stat-subtitle">S/ <?= number_format($totalPagado, 2) ?></div>
            </div>
        </div>

        <div class="stat-box warning">
            <div class="stat-content">
                <div class="stat-icon">⏳</div>
                <div class="stat-label">Pendientes</div>
                <div class="stat-value"><?= $facturasPendientes ?></div>
                <div class="stat-subtitle">S/ <?= number_format($totalPendiente, 2) ?></div>
            </div>
        </div>

        <div class="stat-box danger">
            <div class="stat-content">
                <div class="stat-icon">💰</div>
                <div class="stat-label">Total a Pagar</div>
                <div class="stat-value">S/ <?= number_format($totalPendiente, 2) ?></div>
                <div class="stat-subtitle"><?= $facturasPendientes ?> facturas</div>
            </div>
        </div>
    </div>

    <!-- FILTERS -->
    <div class="filters-section">
        <div class="filters-grid">
            <div class="filter-group">
                <label><i class="bi bi-funnel"></i> Filtrar por estado</label>
                <select class="filter-input" id="estadoFilter">
                    <option value="">Todas</option>
                    <option value="pendiente">Pendientes</option>
                    <option value="pagada">Pagadas</option>
                    <option value="vencida">Vencidas</option>
                </select>
            </div>
            <div class="filter-group">
                <label><i class="bi bi-calendar-range"></i> Filtrar por mes</label>
                <input type="month" class="filter-input" id="mesFilter">
            </div>
            <div>
                <button class="btn-filter" onclick="aplicarFiltros()">
                    <i class="bi bi-funnel"></i>
                    Filtrar
                </button>
            </div>
        </div>
    </div>

    <!-- FACTURAS LIST -->
    <div class="facturas-section">
        <div class="section-header">
            <h2>💳 Historial de Facturas</h2>
        </div>

        <?php if (empty($facturas)): ?>
            <div class="empty-state">
                <div class="empty-icon">💳</div>
                <h3>No tienes facturas registradas</h3>
                <p>Tus facturas aparecerán aquí una vez que se generen</p>
                <a href="<?= BASE_URL ?>/index.php?accion=misCitas" class="btn-primary">
                    <i class="bi bi-calendar-plus"></i>
                    Ver mis citas
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($facturas as $index => $factura): ?>
                <?php 
                    $estado = strtolower($factura->getEstado());
                    $clasEstado = str_contains($estado, 'pag') || str_contains($estado, 'completada') ? 'pagada' : 'pendiente';
                    $badgeClase = str_contains($estado, 'pag') || str_contains($estado, 'completada') ? 'badge-pagada' : 'badge-pendiente';
                ?>
                <div class="factura-card <?= $clasEstado ?> animate-slide-in" style="animation-delay: <?= $index * 0.1 ?>s;">
                    
                    <!-- HEADER -->
                    <div class="factura-header">
                        <div class="factura-info">
                            <h3>Factura #<?= str_pad($factura->getId(), 6, '0', STR_PAD_LEFT) ?></h3>
                            <div class="factura-date">
                                <i class="bi bi-calendar-event"></i>
                                <span><?= date('d/m/Y', strtotime($factura->getEmitidaEn())) ?></span>
                            </div>
                        </div>
                        <div class="factura-monto">
                            <div class="monto-label">Monto Total</div>
                            <div class="monto-value">S/ <?= number_format($factura->getMonto(), 2) ?></div>
                        </div>
                    </div>

                    <!-- DETAILS -->
                    <div class="factura-details">
                        <div class="detail-item">
                            <i class="bi bi-credit-card"></i>
                            <span>Cita #<?= $factura->getCitaId() ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-info-circle"></i>
                            <span class="status-badge <?= $badgeClase ?>">
                                <?= htmlspecialchars($factura->getEstado()) ?>
                            </span>
                        </div>
                    </div>

                    <!-- ACTIONS -->
                    <div class="factura-actions">
                        <a href="<?= BASE_URL ?>/index.php?accion=verFactura&id=<?= $factura->getId() ?>" 
                           class="btn-action view">
                            <i class="bi bi-eye"></i>
                            Ver Detalles
                        </a>
                        <a href="<?= BASE_URL ?>/index.php?accion=descargarFactura&id=<?= $factura->getId() ?>" 
                           class="btn-action download">
                            <i class="bi bi-download"></i>
                            Descargar PDF
                        </a>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

</div>

<script>
// Filtros de facturas
function aplicarFiltros() {
    const estadoFilter = document.getElementById('estadoFilter').value.toLowerCase();
    const mesFilter = document.getElementById('mesFilter').value;
    const facturaCards = document.querySelectorAll('.factura-card');

    facturaCards.forEach(card => {
        const estado = card.classList.contains('pagada') ? 'pagada' : 'pendiente';
        const fecha = card.querySelector('.factura-date span').textContent;

        let matchEstado = !estadoFilter || estado.includes(estadoFilter);
        let matchMes = !mesFilter || fecha.includes(mesFilter.split('-').reverse().join('/').substring(3));

        if (matchEstado && matchMes) {
            card.style.display = 'block';
            card.style.animation = 'fadeInUp 0.4s ease forwards';
        } else {
            card.style.display = 'none';
        }
    });
}

// Aplicar filtros al cambiar
document.getElementById('estadoFilter')?.addEventListener('change', aplicarFiltros);
document.getElementById('mesFilter')?.addEventListener('change', aplicarFiltros);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>