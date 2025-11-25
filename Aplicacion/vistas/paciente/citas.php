<?php
// =====================
// 📅 Vista: Mis Citas del Paciente
// =====================
$citas = $citas ?? [];
$totalCitas = $totalCitas ?? 0;
$citasPendientes = $citasPendientes ?? 0;
$citasConfirmadas = $citasConfirmadas ?? 0;
$citasCanceladas = $citasCanceladas ?? 0;
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
    }

    .citas-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }

    /* ========================
       ANIMACIONES
       ======================== */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    /* ========================
       HEADER
       ======================== */
    .page-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 3rem 2.5rem;
        border-radius: 1.5rem;
        margin-bottom: 2.5rem;
        box-shadow: var(--shadow-lg);
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.6s ease;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
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

    .header-title h1 {
        font-family: 'Poppins', sans-serif;
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0;
    }

    .header-icon {
        font-size: 3rem;
    }

    .btn-nueva-cita {
        background: white;
        color: var(--primary);
        border: none;
        padding: 1rem 2rem;
        border-radius: 1rem;
        font-weight: 700;
        font-size: 1rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .btn-nueva-cita:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        color: var(--primary-dark);
    }

    /* ========================
       STATS CARDS
       ======================== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 1.25rem;
        padding: 2rem;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        border-left: 5px solid var(--card-color);
        animation: slideIn 0.6s ease forwards;
        position: relative;
        overflow: hidden;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 100px;
        height: 100px;
        background: var(--card-color);
        opacity: 0.1;
        border-radius: 50%;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; --card-color: var(--info); }
    .stat-card:nth-child(2) { animation-delay: 0.2s; --card-color: var(--warning); }
    .stat-card:nth-child(3) { animation-delay: 0.3s; --card-color: var(--success); }
    .stat-card:nth-child(4) { animation-delay: 0.4s; --card-color: var(--danger); }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-md);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        background: var(--card-color);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }

    .stat-label {
        font-size: 0.85rem;
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
        color: var(--card-color);
        margin: 0;
    }

    /* ========================
       FILTROS
       ======================== */
    .filters-section {
        background: white;
        border-radius: 1.25rem;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        gap: 1.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-group label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0.5rem;
        display: block;
    }

    .filter-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .filter-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* ========================
       TABLA DE CITAS
       ======================== */
    .citas-table-section {
        background: white;
        border-radius: 1.25rem;
        padding: 2rem;
        box-shadow: var(--shadow-sm);
        animation: fadeInUp 0.6s ease 0.4s backwards;
    }

    .section-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark-text);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .table-responsive {
        border-radius: 1rem;
        overflow: hidden;
    }

    .citas-table {
        width: 100%;
        margin: 0;
    }

    .citas-table thead {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: white;
    }

    .citas-table thead th {
        padding: 1.25rem 1rem;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
    }

    .citas-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }

    .citas-table tbody tr:hover {
        background: #f8f9ff;
        transform: scale(1.01);
    }

    .citas-table tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        color: #334155;
    }

    .cita-doctor {
        font-weight: 700;
        color: var(--dark-text);
        margin-bottom: 0.25rem;
    }

    .cita-especialidad {
        font-size: 0.85rem;
        color: #64748b;
    }

    .cita-fecha {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .fecha-dia {
        font-weight: 700;
        color: var(--dark-text);
    }

    .fecha-hora {
        font-size: 0.85rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-confirmada {
        background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%);
        color: #1e4620;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.15);
    }

    .badge-pendiente {
        background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
        color: #5a3e00;
        box-shadow: 0 4px 10px rgba(245, 158, 11, 0.15);
    }

    .badge-cancelada {
        background: linear-gradient(135deg, #fab1a0 0%, #fd79a8 100%);
        color: #5a1a1a;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.15);
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        border: none;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-view {
        background: var(--info);
        color: white;
    }

    .btn-view:hover {
        background: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-cancel {
        background: var(--danger);
        color: white;
    }

    .btn-cancel:hover {
        background: #dc2626;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
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
        opacity: 0.3;
    }

    .empty-state h3 {
        font-family: 'Poppins', sans-serif;
        font-size: 1.5rem;
        color: #64748b;
        margin-bottom: 0.75rem;
    }

    .empty-state p {
        color: #94a3b8;
        margin-bottom: 2rem;
    }

    .btn-empty-action {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        border: none;
        padding: 1rem 2.5rem;
        border-radius: 1rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
    }

    .btn-empty-action:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        color: white;
    }

    /* ========================
       RESPONSIVE
       ======================== */
    @media (max-width: 1024px) {
        .header-content {
            flex-direction: column;
            gap: 1.5rem;
            text-align: center;
        }

        .header-title {
            justify-content: center;
        }
    }

    @media (max-width: 768px) {
        .citas-container {
            padding: 1rem;
        }

        .page-header {
            padding: 2rem 1.5rem;
        }

        .header-title h1 {
            font-size: 1.8rem;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .filters-section {
            flex-direction: column;
            align-items: stretch;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .citas-table {
            min-width: 800px;
        }
    }

    @media (max-width: 480px) {
        .header-title h1 {
            font-size: 1.5rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .btn-nueva-cita {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="citas-container">

    <!-- HEADER -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-title">
                <span class="header-icon">📅</span>
                <h1>Mis Citas</h1>
            </div>
            <a href="<?= BASE_URL ?>/index.php?accion=crearCitaPaciente" class="btn-nueva-cita">
                <i class="bi bi-plus-circle-fill"></i>
                Nueva Cita
            </a>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">📊</div>
            </div>
            <div class="stat-label">Total Citas</div>
            <div class="stat-value"><?= number_format($totalCitas) ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">⏳</div>
            </div>
            <div class="stat-label">Pendientes</div>
            <div class="stat-value"><?= number_format($citasPendientes) ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">✅</div>
            </div>
            <div class="stat-label">Confirmadas</div>
            <div class="stat-value"><?= number_format($citasConfirmadas) ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">❌</div>
            </div>
            <div class="stat-label">Canceladas</div>
            <div class="stat-value"><?= number_format($citasCanceladas) ?></div>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="filters-section">
        <div class="filter-group">
            <label>🔍 Buscar</label>
            <input type="text" id="searchInput" class="filter-input" placeholder="Buscar doctor, especialidad...">
        </div>
        <div class="filter-group">
            <label>📋 Estado</label>
            <select id="statusFilter" class="filter-input">
                <option value="">Todos los estados</option>
                <option value="pendiente">Pendiente</option>
                <option value="confirmada">Confirmada</option>
                <option value="cancelada">Cancelada</option>
            </select>
        </div>
        <div class="filter-group">
            <label>📅 Ordenar por</label>
            <select id="sortFilter" class="filter-input">
                <option value="fecha-desc">Más recientes</option>
                <option value="fecha-asc">Más antiguas</option>
                <option value="doctor">Doctor (A-Z)</option>
            </select>
        </div>
    </div>

    <!-- TABLA DE CITAS -->
    <div class="citas-table-section">
        <h2 class="section-title">
            <i class="bi bi-list-ul"></i>
            Listado de Citas
        </h2>

        <?php if (empty($citas)): ?>
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <h3>No tienes citas registradas</h3>
                <p>Comienza agendando tu primera cita médica</p>
                <a href="<?= BASE_URL ?>/index.php?accion=crearCitaPaciente" class="btn-empty-action">
                    <i class="bi bi-plus-circle-fill"></i>
                    Agendar Primera Cita
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="citas-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Doctor / Especialidad</th>
                            <th>Fecha y Hora</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="citasTableBody">
                        <?php foreach ($citas as $cita): ?>
                            <tr data-estado="<?= strtolower($cita['estado']) ?>" 
                                data-doctor="<?= htmlspecialchars($cita['doctor_nombre'] ?? '') ?>"
                                data-fecha="<?= $cita['fecha'] ?>">
                                <td>
                                    <span style="background: linear-gradient(135deg, var(--primary), var(--primary-dark)); 
                                                 color: white; padding: 0.5rem 1rem; border-radius: 0.75rem; 
                                                 font-weight: 700; font-size: 0.85rem;">
                                        #<?= str_pad($cita['id'], 4, '0', STR_PAD_LEFT) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="cita-doctor">
                                        Dr. <?= htmlspecialchars($cita['doctor_nombre'] ?? 'Por asignar') ?>
                                    </div>
                                    <div class="cita-especialidad">
                                        <?= htmlspecialchars($cita['especialidad'] ?? 'Medicina General') ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="cita-fecha">
                                        <span class="fecha-dia">
                                            <?= date('d/m/Y', strtotime($cita['fecha'])) ?>
                                        </span>
                                        <span class="fecha-hora">
                                            <i class="bi bi-clock"></i>
                                            <?= date('h:i A', strtotime($cita['fecha'])) ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge badge-<?= strtolower(str_replace(' ', '', $cita['estado'])) ?>">
                                        <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i>
                                        <?= htmlspecialchars($cita['estado']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-view" onclick="verDetalle(<?= $cita['id'] ?>)">
                                            <i class="bi bi-eye"></i>
                                            Ver
                                        </button>
                                        <?php if (strtolower($cita['estado']) !== 'cancelada'): ?>
                                            <button class="btn-action btn-cancel" onclick="cancelarCita(<?= $cita['id'] ?>)">
                                                <i class="bi bi-x-circle"></i>
                                                Cancelar
                                            </button>
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
// Filtro de búsqueda
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    filterTable();
});

// Filtro de estado
document.getElementById('statusFilter')?.addEventListener('change', function() {
    filterTable();
});

// Ordenar
document.getElementById('sortFilter')?.addEventListener('change', function() {
    sortTable(this.value);
});

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const rows = document.querySelectorAll('#citasTableBody tr');

    rows.forEach(row => {
        const doctor = row.dataset.doctor.toLowerCase();
        const estado = row.dataset.estado.toLowerCase();
        
        const matchSearch = doctor.includes(searchTerm);
        const matchStatus = !statusFilter || estado.includes(statusFilter);
        
        row.style.display = (matchSearch && matchStatus) ? '' : 'none';
    });
}

function sortTable(sortBy) {
    const tbody = document.getElementById('citasTableBody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        if (sortBy === 'fecha-desc') {
            return new Date(b.dataset.fecha) - new Date(a.dataset.fecha);
        } else if (sortBy === 'fecha-asc') {
            return new Date(a.dataset.fecha) - new Date(b.dataset.fecha);
        } else if (sortBy === 'doctor') {
            return a.dataset.doctor.localeCompare(b.dataset.doctor);
        }
    });
    
    rows.forEach(row => tbody.appendChild(row));
}

function verDetalle(citaId) {
    window.location.href = '<?= BASE_URL ?>/index.php?accion=verDetalleCita&id=' + citaId;
}
function cancelarCita(citaId) {
    if (confirm('¿Estás seguro de que deseas cancelar esta cita?')) {
        window.location.href = '<?= BASE_URL ?>/index.php?accion=cancelarCita&id=' + citaId;
    }
}
</script>