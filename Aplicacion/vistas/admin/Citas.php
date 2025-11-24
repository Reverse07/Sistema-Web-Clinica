<?php
// =====================
// 📅 Vista: Gestión de Citas (Administrador)
// =====================
$citas = $citas ?? [];
$totalCitas = $totalCitas ?? 0;
$citasConfirmadas = $citasConfirmadas ?? 0;
$citasPendientes = $citasPendientes ?? 0;
$citasCanceladas = $citasCanceladas ?? 0;

// Calcular porcentajes
$porcentajeConfirmadas = $totalCitas > 0 ? round(($citasConfirmadas / $totalCitas) * 100) : 0;
$porcentajePendientes = $totalCitas > 0 ? round(($citasPendientes / $totalCitas) * 100) : 0;
$porcentajeCanceladas = $totalCitas > 0 ? round(($citasCanceladas / $totalCitas) * 100) : 0;
?>

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
    --shadow-sm: 0 4px 15px rgba(0,0,0,.08);
    --shadow-md: 0 12px 30px rgba(0,0,0,.15);
    --shadow-lg: 0 20px 50px rgba(0,0,0,.2);
}

*, *::before, *::after {
    box-sizing: border-box;
}

html, body {
    background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
    min-height: 100vh;
    scroll-behavior: smooth;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

body {
    padding: 2rem 0;
    color: var(--dark-text);
    margin: 0;
}

/* ========================
   ANIMACIONES
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
    0%, 100% { box-shadow: 0 0 0 0 rgba(102,126,234,.7); }
    50% { box-shadow: 0 0 0 15px rgba(102,126,234,0); }
}

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
    background: radial-gradient(circle, rgba(255,255,255,.1) 0%, transparent 70%);
    border-radius: 50%;
    animation: pulse 3s infinite;
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
    font-size: clamp(1.5rem, 4vw, 3rem);
    font-weight: 800;
    margin: 0 0 .5rem 0;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-text p {
    font-size: 1.1rem;
    opacity: .95;
    margin: 0;
}

.btn-new-cita {
    background: white;
    color: var(--primary);
    padding: 1.25rem 2.5rem;
    border-radius: 1.5rem;
    text-decoration: none;
    font-weight: 700;
    font-size: 1rem;
    display: inline-flex;
    align-items: center;
    gap: .75rem;
    transition: all .3s cubic-bezier(.4,0,.2,1);
    box-shadow: 0 8px 20px rgba(0,0,0,.2);
    font-family: 'Poppins', sans-serif;
    white-space: nowrap;
}

.btn-new-cita:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,.3);
    color: var(--primary-dark);
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
    transition: all .3s cubic-bezier(.4,0,.2,1);
    border-top: 5px solid;
    position: relative;
    overflow: hidden;
    animation: fadeInUp .6s ease forwards;
    opacity: 0;
}

.stat-card:nth-child(1) { animation-delay: .1s; border-top-color: var(--info); }
.stat-card:nth-child(2) { animation-delay: .2s; border-top-color: var(--success); }
.stat-card:nth-child(3) { animation-delay: .3s; border-top-color: var(--warning); }
.stat-card:nth-child(4) { animation-delay: .4s; border-top-color: var(--danger); }

.stat-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, var(--stat-color) 0%, transparent 70%);
    opacity: .08;
    border-radius: 50%;
}

.stat-card.total { --stat-color: var(--info); }
.stat-card.confirmadas { --stat-color: var(--success); }
.stat-card.pendientes { --stat-color: var(--warning); }
.stat-card.canceladas { --stat-color: var(--danger); }

.stat-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-md);
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
    width: 70px;
    height: 70px;
    border-radius: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    background: linear-gradient(135deg, var(--stat-color) 0%, rgba(102,126,234,.5) 100%);
    color: white;
    box-shadow: 0 8px 20px rgba(0,0,0,.15);
}

.stat-label {
    font-size: .9rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: .5rem;
    position: relative;
    z-index: 1;
}

.stat-value {
    font-size: clamp(2rem, 4vw, 2.75rem);
    font-family: 'Poppins', sans-serif;
    font-weight: 800;
    color: var(--stat-color);
    margin: 0 0 1rem 0;
    position: relative;
    z-index: 1;
}

.stat-progress {
    height: 6px;
    background: #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    z-index: 1;
}

.stat-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--stat-color) 0%, rgba(102,126,234,.5) 100%);
    border-radius: 10px;
    transition: width .5s ease;
}

/* ========================
   SEARCH & FILTERS
   ======================== */
.search-filter-section {
    background: white;
    border-radius: 1.5rem;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-sm);
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 1.5rem;
    align-items: end;
}

.search-box {
    position: relative;
}

.search-box label {
    display: block;
    font-weight: 600;
    margin-bottom: .5rem;
    color: #334155;
    font-size: .9rem;
}

.search-box input,
.search-box select {
    width: 100%;
    padding: .875rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 1rem;
    font-size: 1rem;
    transition: all .3s ease;
}

.search-box input {
    padding-left: 3rem;
}

.search-box input:focus,
.search-box select:focus {
    border-color: var(--primary);
    outline: none;
    box-shadow: 0 0 0 4px rgba(102,126,234,.1);
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 2.75rem;
    font-size: 1.2rem;
    color: #94a3b8;
    pointer-events: none;
}

.filter-btn {
    padding: .875rem 1.5rem;
    border: 2px solid #e2e8f0;
    background: white;
    border-radius: 1rem;
    cursor: pointer;
    font-weight: 600;
    transition: all .3s ease;
}

.filter-btn:hover {
    border-color: var(--primary);
    background: var(--primary);
    color: white;
}

/* ========================
   TABLE
   ======================== */
.table-section {
    background: white;
    border-radius: 1.5rem;
    padding: 2.5rem;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.table-header h2 {
    font-family: 'Poppins', sans-serif;
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.table-count {
    color: #64748b;
    font-weight: 600;
    font-size: 1rem;
}

.table-responsive {
    border-radius: 1rem;
    overflow: auto;
}

.table {
    width: 100%;
    margin: 0;
    font-size: .95rem;
    border-collapse: collapse;
}

.table thead {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: white;
}

.table thead th {
    border: none;
    padding: 1.5rem 1.25rem;
    font-weight: 700;
    font-size: .8rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-family: 'Poppins', sans-serif;
    text-align: left;
}

.table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: all .2s cubic-bezier(.4,0,.2,1);
}

.table tbody tr:hover {
    background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
    transform: scale(1.01);
}

.table tbody td {
    padding: 1.5rem 1.25rem;
    vertical-align: middle;
    color: #334155;
}

.id-badge {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    padding: .5rem 1rem;
    border-radius: .75rem;
    font-weight: 700;
    font-size: .85rem;
    display: inline-block;
    font-family: 'Poppins', sans-serif;
    box-shadow: 0 4px 10px rgba(102,126,234,.2);
}

.name-principal {
    font-weight: 700;
    color: #1f2937;
    display: block;
}

.name-secondary {
    font-size: .85rem;
    color: #64748b;
    display: block;
    margin-top: .25rem;
}

.date-item {
    font-weight: 600;
    color: #1f2937;
    display: block;
}

.time-item {
    font-size: .85rem;
    color: #64748b;
    display: block;
    margin-top: .25rem;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .6rem 1.25rem;
    border-radius: 50px;
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    font-family: 'Poppins', sans-serif;
}

.badge-confirmada {
    background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%);
    color: #1e4620;
    box-shadow: 0 4px 10px rgba(16,185,129,.2);
}

.badge-pendiente {
    background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
    color: #5a3e00;
    box-shadow: 0 4px 10px rgba(245,158,11,.2);
}

.badge-cancelada {
    background: linear-gradient(135deg, #fab1a0 0%, #fd79a8 100%);
    color: #5a1a1a;
    box-shadow: 0 4px 10px rgba(239,68,68,.2);
}

.actions-cell {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
}

.btn-action {
    padding: .6rem 1rem;
    border-radius: .75rem;
    text-decoration: none;
    font-size: .8rem;
    font-weight: 700;
    transition: all .2s ease;
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    border: none;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
    color: white;
}

.btn-confirm {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    box-shadow: 0 4px 10px rgba(16,185,129,.2);
}

.btn-edit {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    box-shadow: 0 4px 10px rgba(59,130,246,.2);
}

.btn-cancel {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    box-shadow: 0 4px 10px rgba(239,68,68,.2);
}

.btn-confirm:hover,
.btn-edit:hover,
.btn-cancel:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,0,0,.3);
}

/* ========================
   ALERT
   ======================== */
.alert-custom {
    padding: 1.25rem 1.5rem;
    border-radius: 1.25rem;
    margin-bottom: 2rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 1rem;
    animation: slideInLeft .3s ease;
    border-left: 5px solid;
}

.alert-success {
    background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%);
    color: #1e4620;
    border-left-color: #10b981;
}

.alert-error {
    background: linear-gradient(135deg, #fab1a0 0%, #fd79a8 100%);
    color: #5a1a1a;
    border-left-color: #ef4444;
}

/* ========================
   EMPTY STATE
   ======================== */
.empty-state {
    text-align: center;
    padding: 5rem 2rem;
}

.empty-icon {
    font-size: 5rem;
    margin-bottom: 1.5rem;
    opacity: .3;
}

.empty-state h3 {
    font-family: 'Poppins', sans-serif;
    font-size: 1.5rem;
    color: #64748b;
    margin: 0 0 .5rem 0;
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
        justify-content: center;
    }

    .search-filter-section {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    body {
        padding: 1rem 0;
    }

    .header-hero {
        padding: 2.5rem 1.5rem;
        margin-bottom: 2rem;
    }

    .table-section {
        padding: 1.5rem;
    }

    .table thead th,
    .table tbody td {
        padding: 1rem .75rem;
        font-size: .85rem;
    }

    .actions-cell {
        flex-direction: column;
        width: 100%;
    }

    .btn-action {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .search-filter-section {
        padding: 1.5rem 1rem;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="container-fluid">
    <!-- HEADER HERO -->
    <div class="header-hero">
        <div class="header-content">
            <div class="header-text">
                <h1>
                    <i class="bi bi-calendar-check"></i>
                    Gestión de Citas
                </h1>
                <p>Administre y controle todas las citas médicas del sistema</p>
            </div>
            <a href="?accion=crearCita" class="btn-new-cita">
                <i class="bi bi-plus-circle"></i>
                Nueva Cita
            </a>
        </div>
    </div>

    <!-- ALERT MESSAGE -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert-custom <?= $_SESSION['mensaje']['tipo'] === 'error' ? 'alert-error' : 'alert-success' ?>">
            <span><?= $_SESSION['mensaje']['tipo'] === 'error' ? '❌' : '✅' ?></span>
            <span><?= htmlspecialchars($_SESSION['mensaje']['texto']) ?></span>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <!-- STATS GRID -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Citas</div>
                    <p class="stat-value"><?= number_format($totalCitas) ?></p>
                </div>
                <div class="stat-icon">📊</div>
            </div>
            <div class="stat-progress">
                <div class="stat-progress-bar" style="width:100%"></div>
            </div>
        </div>

        <div class="stat-card confirmadas">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Confirmadas</div>
                    <p class="stat-value"><?= number_format($citasConfirmadas) ?></p>
                </div>
                <div class="stat-icon">✅</div>
            </div>
            <div class="stat-progress">
                <div class="stat-progress-bar" style="width:<?= $porcentajeConfirmadas ?>%"></div>
            </div>
        </div>

        <div class="stat-card pendientes">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Pendientes</div>
                    <p class="stat-value"><?= number_format($citasPendientes) ?></p>
                </div>
                <div class="stat-icon">⏳</div>
            </div>
            <div class="stat-progress">
                <div class="stat-progress-bar" style="width:<?= $porcentajePendientes ?>%"></div>
            </div>
        </div>

        <div class="stat-card canceladas">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Canceladas</div>
                    <p class="stat-value"><?= number_format($citasCanceladas) ?></p>
                </div>
                <div class="stat-icon">❌</div>
            </div>
            <div class="stat-progress">
                <div class="stat-progress-bar" style="width:<?= $porcentajeCanceladas ?>%"></div>
            </div>
        </div>
    </div>

    <!-- SEARCH & FILTERS -->
    <div class="search-filter-section">
        <div class="search-box">
            <label for="searchInput">🔍 Buscar</label>
            <span class="search-icon">🔍</span>
            <input type="text" id="searchInput" placeholder="Paciente, doctor o ID...">
        </div>
        <div class="search-box">
            <label for="filterEstado">Estado</label>
            <select id="filterEstado">
                <option value="">Todos los estados</option>
                <option value="confirmada">✅ Confirmadas</option>
                <option value="pendiente">⏳ Pendientes</option>
                <option value="cancelada">❌ Canceladas</option>
            </select>
        </div>
        <button class="filter-btn" onclick="resetFilters()">
            <i class="bi bi-arrow-clockwise"></i> Limpiar
        </button>
    </div>

    <!-- TABLE -->
    <div class="table-section">
        <div class="table-header">
            <h2>
                <i class="bi bi-list-check"></i>
                Lista de Citas
            </h2>
            <span class="table-count">
                <i class="bi bi-info-circle"></i>
                Total: <strong id="citasCount"><?= count($citas) ?></strong>
            </span>
        </div>

        <?php if (empty($citas)): ?>
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <h3>No hay citas registradas</h3>
                <p>Comience creando una nueva cita desde el botón superior</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table" id="tablaCitas">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Paciente</th>
                            <th>Doctor</th>
                            <th>Fecha y Hora</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($citas as $cita): 
                            $estado = strtolower($cita['estado']);
                            $esConfirmada = str_contains($estado, 'confirmada');
                            $esPendiente = str_contains($estado, 'pendiente');
                            $esCancelada = str_contains($estado, 'cancelada');
                            
                            $claseEstado = $esConfirmada ? 'badge-confirmada' : ($esPendiente ? 'badge-pendiente' : 'badge-cancelada');
                            $iconoEstado = $esConfirmada ? '✅' : ($esPendiente ? '⏳' : '❌');
                        ?>
                            <tr>
                                <td>
                                    <span class="id-badge">#<?= htmlspecialchars($cita['id']) ?></span>
                                </td>
                                <td>
                                    <span class="name-principal"><?= htmlspecialchars($cita['paciente_nombre'] ?? 'N/A') ?></span>
                                    <?php if (!empty($cita['paciente_email'])): ?>
                                        <span class="name-secondary"><?= htmlspecialchars($cita['paciente_email']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="name-principal"><?= htmlspecialchars($cita['doctor_nombre'] ?? 'N/A') ?></span>
                                    <?php if (!empty($cita['especialidad'])): ?>
                                        <span class="name-secondary"><?= htmlspecialchars($cita['especialidad']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="date-item"><?= date('d/m/Y', strtotime($cita['fecha'])) ?></span>
                                    <span class="time-item"><?= date('h:i A', strtotime($cita['fecha'])) ?></span>
                                </td>
                                <td>
                                    <span class="status-badge <?= $claseEstado ?>">
                                        <?= $iconoEstado ?> <?= htmlspecialchars($cita['estado']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <?php if ($esPendiente): ?>
                                            <a href="?accion=confirmar&id=<?= $cita['id'] ?>" 
                                               class="btn-action btn-confirm swal-confirmar"
                                               data-id="<?= $cita['id'] ?>">
                                                <i class="bi bi-check-circle"></i> Confirmar
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="?accion=editar&id=<?= $cita['id'] ?>" class="btn-action btn-edit">
                                            <i class="bi bi-pencil-square"></i> Editar
                                        </a>
                                        
                                        <?php if (!$esCancelada): ?>
                                            <a href="?accion=cancelar&id=<?= $cita['id'] ?>" 
                                               class="btn-action btn-cancel swal-cancelar"
                                               data-id="<?= $cita['id'] ?>">
                                                <i class="bi bi-x-circle"></i> Cancelar
                                            </a>
                                        <?php endif; ?>
                                    </div>
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
(function() {
    const searchInput = document.getElementById('searchInput');
    const filterEstado = document.getElementById('filterEstado');
    const tablaCitas = document.getElementById('tablaCitas');
    const citasCount = document.getElementById('citasCount');

    function updateVisibleCount() {
        const rows = tablaCitas?.querySelectorAll('tbody tr') || [];
        const visible = Array.from(rows).filter(r => r.style.display !== 'none').length;
        if (citasCount) citasCount.textContent = visible;
    }

    function filterTable() {
        const searchTerm = searchInput?.value.toLowerCase() || '';
        const estadoValue = filterEstado?.value.toLowerCase() || '';
        const rows = tablaCitas?.querySelectorAll('tbody tr') || [];

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const matchesSearch = !searchTerm || text.includes(searchTerm);
            
            let matchesEstado = true;
            if (estadoValue) {
                const badge = row.querySelector('.status-badge');
                matchesEstado = badge && badge.textContent.toLowerCase().includes(estadoValue);
            }

            row.style.display = matchesSearch && matchesEstado ? '' : 'none';
        });

        updateVisibleCount();
    }

    searchInput?.addEventListener('input', filterTable);
    filterEstado?.addEventListener('change', filterTable);

    window.resetFilters = function() {
        if (searchInput) searchInput.value = '';
        if (filterEstado) filterEstado.value = '';
        const rows = tablaCitas?.querySelectorAll('tbody tr') || [];
        rows.forEach(row => row.style.display = '');
        updateVisibleCount();
    };

    // SweetAlert para confirmaciones
    document.querySelectorAll('.swal-cancelar').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.href;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: "¿Cancelar esta cita?",
                    text: "El paciente será notificado de la cancelación.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí, cancelar",
                    cancelButtonText: "No, volver",
                    confirmButtonColor: "#ef4444",
                    cancelButtonColor: "#3b82f6"
                }).then(result => {
                    if (result.isConfirmed) window.location.href = url;
                });
            } else if (confirm("¿Está seguro que desea cancelar esta cita?")) {
                window.location.href = url;
            }
        });
    });

    document.querySelectorAll('.swal-confirmar').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.href;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: "¿Confirmar esta cita?",
                    text: "La cita quedará confirmada.",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Sí, confirmar",
                    cancelButtonText: "No, volver",
                    confirmButtonColor: "#10b981",
                    cancelButtonColor: "#3b82f6"
                }).then(result => {
                    if (result.isConfirmed) window.location.href = url;
                });
            } else if (confirm("¿Está seguro que desea confirmar esta cita?")) {
                window.location.href = url;
            }
        });
    });
})();
</script>