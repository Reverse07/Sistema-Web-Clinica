<?php
// =====================
// 💊 Vista: Mis Recetas del Paciente
// =====================
$recetas = $recetas ?? [];
$totalRecetas = $totalRecetas ?? 0;
$totalRecetasRecientes = $totalRecetasRecientes ?? 0;
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
        --info: #3b82f6;
        --purple: #8b5cf6;
    }

    * {
        font-family: 'Inter', sans-serif;
    }

    html, body {
        background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
        min-height: 100vh;
    }

    .recetas-container {
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
        background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        color: white;
        padding: 3rem 2.5rem;
        border-radius: 1.5rem;
        margin-bottom: 2.5rem;
        box-shadow: 0 20px 50px rgba(139, 92, 246, 0.3);
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
        margin-bottom: 1rem;
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

    .stat-card:nth-child(1) { animation-delay: 0.1s; --card-color: var(--purple); }
    .stat-card:nth-child(2) { animation-delay: 0.2s; --card-color: var(--success); }
    .stat-card:nth-child(3) { animation-delay: 0.3s; --card-color: var(--info); }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
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
       RECETAS GRID
       ======================== */
    .recetas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 2rem;
        animation: fadeInUp 0.6s ease 0.4s backwards;
    }

    .receta-card {
        background: white;
        border-radius: 1.25rem;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
    }

    .receta-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .receta-header {
        background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        color: white;
        padding: 1.5rem;
    }

    .receta-id {
        font-family: 'Poppins', sans-serif;
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .receta-fecha {
        font-size: 0.9rem;
        opacity: 0.9;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .receta-body {
        padding: 1.5rem;
    }

    .doctor-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: #f8f9ff;
        border-radius: 0.75rem;
    }

    .doctor-avatar {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .doctor-details h4 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 0.25rem 0;
    }

    .doctor-especialidad {
        font-size: 0.85rem;
        color: #64748b;
    }

    .observaciones {
        background: #fef3c7;
        border-left: 4px solid #fbbf24;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .observaciones-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #92400e;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .observaciones-text {
        color: #78350f;
        font-size: 0.9rem;
        line-height: 1.6;
    }

    .receta-actions {
        display: flex;
        gap: 0.75rem;
        border-top: 2px solid #f1f5f9;
        padding-top: 1.5rem;
    }

    .btn-action {
        flex: 1;
        padding: 0.75rem;
        border-radius: 0.5rem;
        border: none;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
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

    .btn-print {
        background: var(--success);
        color: white;
    }

    .btn-print:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    /* ========================
       EMPTY STATE
       ======================== */
    .empty-state {
        text-align: center;
        padding: 5rem 2rem;
        background: white;
        border-radius: 1.25rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
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

    /* ========================
       RESPONSIVE
       ======================== */
    @media (max-width: 768px) {
        .recetas-container {
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

        .recetas-grid {
            grid-template-columns: 1fr;
        }

        .filters-section {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<div class="recetas-container">

    <!-- HEADER -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-title">
                <span class="header-icon">💊</span>
                <h1>Mis Recetas</h1>
            </div>
            <div class="header-subtitle">
                Administra y consulta todas tus recetas médicas
            </div>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">📋</div>
            </div>
            <div class="stat-label">Total de Recetas</div>
            <div class="stat-value"><?= number_format($totalRecetas) ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">🆕</div>
            </div>
            <div class="stat-label">Últimos 30 días</div>
            <div class="stat-value"><?= number_format($totalRecetasRecientes) ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">👨‍⚕️</div>
            </div>
            <div class="stat-label">Doctores Consultados</div>
            <div class="stat-value">
                <?php 
                $doctoresUnicos = count(array_unique(array_column($recetas, 'doctor_nombre')));
                echo number_format($doctoresUnicos);
                ?>
            </div>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="filters-section">
        <div class="filter-group">
            <label>🔍 Buscar</label>
            <input type="text" id="searchInput" class="filter-input" placeholder="Buscar por doctor...">
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

    <!-- GRID DE RECETAS -->
    <?php if (empty($recetas)): ?>
        <div class="empty-state">
            <div class="empty-icon">💊</div>
            <h3>No tienes recetas registradas</h3>
            <p>Tus recetas médicas aparecerán aquí después de tus consultas</p>
        </div>
    <?php else: ?>
        <div class="recetas-grid" id="recetasGrid">
            <?php foreach ($recetas as $receta): ?>
                <div class="receta-card" 
                     data-doctor="<?= htmlspecialchars($receta['doctor_nombre'] ?? '') ?>"
                     data-fecha="<?= $receta['fecha_emision'] ?>">
                    
                    <!-- Header -->
                    <div class="receta-header">
                        <div class="receta-id">
                            Receta #<?= str_pad($receta['id'], 4, '0', STR_PAD_LEFT) ?>
                        </div>
                        <div class="receta-fecha">
                            <i class="bi bi-calendar"></i>
                            <?= date('d/m/Y', strtotime($receta['fecha_emision'])) ?>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="receta-body">
                        <!-- Doctor Info -->
                        <div class="doctor-info">
                            <div class="doctor-avatar">👨‍⚕️</div>
                            <div class="doctor-details">
                                <h4>Dr. <?= htmlspecialchars($receta['doctor_nombre'] ?? 'Por asignar') ?></h4>
                                <div class="doctor-especialidad">
                                    <?= htmlspecialchars($receta['especialidad'] ?? 'Medicina General') ?>
                                </div>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <?php if (!empty($receta['observaciones'])): ?>
                        <div class="observaciones">
                            <div class="observaciones-label">📝 Observaciones</div>
                            <div class="observaciones-text">
                                <?= htmlspecialchars($receta['observaciones']) ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Actions -->
                        <div class="receta-actions">
                            <button class="btn-action btn-view" onclick="verReceta(<?= $receta['id'] ?>)">
                                <i class="bi bi-eye"></i>
                                Ver Detalle
                            </button>
                            <button class="btn-action btn-print" onclick="imprimirReceta(<?= $receta['id'] ?>)">
                                <i class="bi bi-printer"></i>
                                Imprimir
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<script>
// Filtro de búsqueda
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    filterRecetas();
});

// Ordenar
document.getElementById('sortFilter')?.addEventListener('change', function() {
    sortRecetas(this.value);
});

function filterRecetas() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const cards = document.querySelectorAll('.receta-card');

    cards.forEach(card => {
        const doctor = card.dataset.doctor.toLowerCase();
        const matchSearch = doctor.includes(searchTerm);
        
        card.style.display = matchSearch ? '' : 'none';
    });
}

function sortRecetas(sortBy) {
    const grid = document.getElementById('recetasGrid');
    const cards = Array.from(grid.querySelectorAll('.receta-card'));
    
    cards.sort((a, b) => {
        if (sortBy === 'fecha-desc') {
            return new Date(b.dataset.fecha) - new Date(a.dataset.fecha);
        } else if (sortBy === 'fecha-asc') {
            return new Date(a.dataset.fecha) - new Date(b.dataset.fecha);
        } else if (sortBy === 'doctor') {
            return a.dataset.doctor.localeCompare(b.dataset.doctor);
        }
    });
    
    cards.forEach(card => grid.appendChild(card));
}

function verReceta(recetaId) {
    window.location.href = '<?= BASE_URL ?>/index.php?accion=verMiReceta&id=' + recetaId;
}

function imprimirReceta(recetaId) {
    window.open('<?= BASE_URL ?>/index.php?accion=imprimirReceta&id=' + recetaId, '_blank');
}
</script>