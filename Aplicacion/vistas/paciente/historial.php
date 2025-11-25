<?php
/**
 * Vista: Historial Médico del Paciente
 * Ruta: Aplicacion/vistas/paciente/historial.php
 */
$historias = $historias ?? [];
$estadisticas = $estadisticas ?? [];
?>

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --primary: #667eea;
        --primary-dark: #764ba2;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
    }

    * {
        font-family: 'Inter', sans-serif;
    }

    html, body {
        background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
        min-height: 100vh;
    }

    .historial-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }

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
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
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
    }

    .header-title {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 0.75rem;
    }

    .header-icon {
        font-size: 3rem;
    }

    .header-title h1 {
        font-family: 'Poppins', sans-serif;
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0;
    }

    .header-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-left: 4.5rem;
    }

    /* ========================
       STATS CARDS
       ======================== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 1.25rem;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
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

    .stat-card:nth-child(1) { animation-delay: 0.1s; --card-color: var(--primary); }
    .stat-card:nth-child(2) { animation-delay: 0.2s; --card-color: var(--success); }
    .stat-card:nth-child(3) { animation-delay: 0.3s; --card-color: var(--info); }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
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
        margin-bottom: 1rem;
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
        margin: 0.5rem 0;
    }

    .stat-detail {
        font-size: 0.9rem;
        color: #475569;
        margin-top: 0.75rem;
    }

    .especialidades-list {
        list-style: none;
        padding: 0;
        margin: 1rem 0 0 0;
    }

    .especialidades-list li {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }

    .especialidades-list li:last-child {
        border-bottom: none;
    }

    .count-badge {
        background: var(--card-color);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    /* ========================
       FILTROS
       ======================== */
    .filters-section {
        background: white;
        border-radius: 1.25rem;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
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
       TABLA
       ======================== */
    .historias-section {
        background: white;
        border-radius: 1.25rem;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        animation: fadeInUp 0.6s ease 0.4s backwards;
    }

    .section-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .table-responsive {
        border-radius: 1rem;
        overflow: hidden;
    }

    .tabla-historias {
        width: 100%;
        margin: 0;
    }

    .tabla-historias thead {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: white;
    }

    .tabla-historias thead th {
        padding: 1.25rem 1rem;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
    }

    .tabla-historias tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }

    .tabla-historias tbody tr:hover {
        background: #f8f9ff;
        transform: scale(1.01);
    }

    .tabla-historias tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        color: #334155;
    }

    .fecha-cell {
        font-weight: 700;
        color: #0f172a;
    }

    .fecha-hora {
        font-size: 0.85rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.25rem;
    }

    .doctor-cell {
        font-weight: 700;
        color: #0f172a;
    }

    .doctor-cmp {
        font-size: 0.85rem;
        color: #64748b;
        margin-top: 0.25rem;
    }

    .badge {
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

    .badge-especialidad {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e40af;
    }

    .text-truncate-custom {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
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
        text-decoration: none;
    }

    .btn-view {
        background: var(--info);
        color: white;
    }

    .btn-view:hover {
        background: #2563eb;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-download {
        background: var(--success);
        color: white;
    }

    .btn-download:hover {
        background: #059669;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
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
    }

    /* ========================
       RESPONSIVE
       ======================== */
    @media (max-width: 768px) {
        .historial-container {
            padding: 1rem;
        }

        .page-header {
            padding: 2rem 1.5rem;
        }

        .header-title h1 {
            font-size: 1.8rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .filters-section {
            flex-direction: column;
            align-items: stretch;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .tabla-historias {
            min-width: 800px;
        }
    }
</style>

<div class="historial-container">

    <!-- HEADER -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-title">
                <span class="header-icon">📋</span>
                <h1>Mi Historial Médico</h1>
            </div>
            <div class="header-subtitle">
                Consulta completa de tus visitas médicas y diagnósticos
            </div>
        </div>
    </div>

    <!-- ESTADÍSTICAS -->
    <?php if (!empty($estadisticas)): ?>
    <div class="stats-grid">
        <!-- Total de consultas -->
        <div class="stat-card">
            <div class="stat-icon">🏥</div>
            <div class="stat-label">Consultas Totales</div>
            <div class="stat-value"><?= number_format($estadisticas['total'] ?? 0) ?></div>
        </div>

        <!-- Última consulta -->
        <?php if (!empty($estadisticas['ultima_consulta'])): ?>
        <div class="stat-card">
            <div class="stat-icon">📅</div>
            <div class="stat-label">Última Consulta</div>
            <div class="stat-value" style="font-size: 1.3rem;">
                <?= date('d/m/Y', strtotime($estadisticas['ultima_consulta']['creada_en'])) ?>
            </div>
            <div class="stat-detail">
                Dr. <?= htmlspecialchars($estadisticas['ultima_consulta']['doctor_nombre']) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Especialidades visitadas -->
        <?php if (!empty($estadisticas['especialidades'])): ?>
        <div class="stat-card">
            <div class="stat-icon">⭐</div>
            <div class="stat-label">Especialidades</div>
            <div class="stat-value"><?= count($estadisticas['especialidades']) ?></div>
            <ul class="especialidades-list">
                <?php foreach (array_slice($estadisticas['especialidades'], 0, 3) as $esp): ?>
                <li>
                    <span><?= htmlspecialchars($esp['especialidad'] ?? 'General') ?></span>
                    <span class="count-badge"><?= $esp['cantidad'] ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- FILTROS -->
    <div class="filters-section">
        <div class="filter-group">
            <label>🔍 Buscar</label>
            <input type="text" id="searchInput" class="filter-input" placeholder="Buscar por doctor, diagnóstico...">
        </div>
        <div class="filter-group">
            <label>🏥 Especialidad</label>
            <select id="especialidadFilter" class="filter-input">
                <option value="">Todas las especialidades</option>
                <?php if (!empty($estadisticas['especialidades'])): ?>
                    <?php foreach ($estadisticas['especialidades'] as $esp): ?>
                        <option value="<?= htmlspecialchars($esp['especialidad']) ?>">
                            <?= htmlspecialchars($esp['especialidad']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
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

    <!-- TABLA DE HISTORIAS -->
    <div class="historias-section">
        <h2 class="section-title">
            <i class="bi bi-file-medical"></i>
            Registro de Consultas
        </h2>

        <?php if (empty($historias)): ?>
            <div class="empty-state">
                <div class="empty-icon">🏥</div>
                <h3>No tienes historias clínicas registradas</h3>
                <p>Cuando asistas a consultas, aquí aparecerán tus historias médicas.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="tabla-historias">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Doctor</th>
                            <th>Especialidad</th>
                            <th>Motivo</th>
                            <th>Diagnóstico</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="historiasTableBody">
                        <?php foreach ($historias as $historia): ?>
                        <tr data-doctor="<?= htmlspecialchars($historia['doctor_nombre'] ?? '') ?>"
                            data-especialidad="<?= htmlspecialchars($historia['especialidad'] ?? '') ?>"
                            data-fecha="<?= $historia['creada_en'] ?>"
                            data-diagnostico="<?= htmlspecialchars($historia['diagnostico'] ?? '') ?>">
                            <td>
                                <div class="fecha-cell">
                                    <?= date('d/m/Y', strtotime($historia['creada_en'])) ?>
                                </div>
                                <div class="fecha-hora">
                                    <i class="bi bi-clock"></i>
                                    <?= date('H:i', strtotime($historia['creada_en'])) ?>
                                </div>
                            </td>
                            <td>
                                <div class="doctor-cell">
                                    Dr. <?= htmlspecialchars($historia['doctor_nombre']) ?>
                                </div>
                                <div class="doctor-cmp">
                                    CMP: <?= htmlspecialchars($historia['numero_colegiatura'] ?? 'N/A') ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-especialidad">
                                    <?= htmlspecialchars($historia['especialidad'] ?? 'General') ?>
                                </span>
                            </td>
                            <td>
                                <div class="text-truncate-custom" title="<?= htmlspecialchars($historia['motivo']) ?>">
                                    <?= htmlspecialchars($historia['motivo']) ?>
                                </div>
                            </td>
                            <td>
                                <div class="text-truncate-custom" title="<?= htmlspecialchars($historia['diagnostico']) ?>">
                                    <?= htmlspecialchars($historia['diagnostico']) ?>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="?accion=verHistoria&id=<?= $historia['id'] ?>" 
                                       class="btn-action btn-view">
                                        <i class="bi bi-eye"></i>
                                        Ver
                                    </a>
                                    <a href="?accion=descargarHistoria&id=<?= $historia['id'] ?>" 
                                       class="btn-action btn-download">
                                        <i class="bi bi-download"></i>
                                        PDF
                                    </a>
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
    filterHistorias();
});

// Filtro de especialidad
document.getElementById('especialidadFilter')?.addEventListener('change', function() {
    filterHistorias();
});

// Ordenar
document.getElementById('sortFilter')?.addEventListener('change', function() {
    sortHistorias(this.value);
});

function filterHistorias() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const especialidad = document.getElementById('especialidadFilter').value.toLowerCase();
    const rows = document.querySelectorAll('#historiasTableBody tr');

    rows.forEach(row => {
        const doctor = row.dataset.doctor.toLowerCase();
        const especialidadRow = row.dataset.especialidad.toLowerCase();
        const diagnostico = row.dataset.diagnostico.toLowerCase();
        
        const matchSearch = doctor.includes(searchTerm) || diagnostico.includes(searchTerm);
        const matchEspecialidad = !especialidad || especialidadRow.includes(especialidad);
        
        row.style.display = (matchSearch && matchEspecialidad) ? '' : 'none';
    });
}

function sortHistorias(sortBy) {
    const tbody = document.getElementById('historiasTableBody');
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
</script>