<?php
// =====================================================
// 💳 Vista: Gestión de Facturas (ADMIN)
// Ubicación: Aplicacion/vistas/admin/facturas.php
// =====================================================
$facturas = $facturas ?? [];
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

    .facturas-container {
        max-width: 1600px;
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

    /* HEADER */
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
        margin-top: 0.5rem;
    }

    .btn-nueva-factura {
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

    .btn-nueva-factura:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        color: var(--primary-dark);
    }

    /* STATS CARDS */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

    .stat-card:nth-child(1) { animation-delay: 0.1s; --card-color: var(--info); }
    .stat-card:nth-child(2) { animation-delay: 0.2s; --card-color: var(--success); }
    .stat-card:nth-child(3) { animation-delay: 0.3s; --card-color: var(--warning); }
    .stat-card:nth-child(4) { animation-delay: 0.4s; --card-color: var(--primary); }

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
        margin: 0;
    }

    /* FILTROS */
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

    /* TABLA */
    .facturas-section {
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

    .facturas-table {
        width: 100%;
        margin: 0;
    }

    .facturas-table thead {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: white;
    }

    .facturas-table thead th {
        padding: 1.25rem 1rem;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
    }

    .facturas-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }

    .facturas-table tbody tr:hover {
        background: #f8f9ff;
        transform: scale(1.01);
    }

    .facturas-table tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        color: #334155;
    }

    .factura-id {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 0.9rem;
        display: inline-block;
    }

    .paciente-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .paciente-avatar {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .paciente-nombre {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }

    .paciente-email {
        font-size: 0.85rem;
        color: #64748b;
    }

    .fecha-cell {
        font-weight: 700;
        color: #0f172a;
    }

    .fecha-hora {
        font-size: 0.85rem;
        color: #64748b;
        margin-top: 0.25rem;
    }

    .monto {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--success);
        font-family: 'Poppins', sans-serif;
    }

    .estado-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-pagada {
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
        justify-content: center;
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

    .btn-edit {
        background: var(--warning);
        color: white;
    }

    .btn-edit:hover {
        background: #d97706;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }

    .btn-delete {
        background: var(--danger);
        color: white;
    }

    .btn-delete:hover {
        background: #dc2626;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    /* EMPTY STATE */
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

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .facturas-container {
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

        .facturas-table {
            min-width: 900px;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="facturas-container">

    <!-- HEADER -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-title">
                <span class="header-icon">💳</span>
                <div>
                    <h1>Gestión de Facturas</h1>
                    <div class="header-subtitle">Administra todas las facturas del sistema</div>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/index.php?accion=crearFactura" class="btn-nueva-factura">
                <i class="bi bi-plus-circle-fill"></i>
                Nueva Factura
            </a>
        </div>
    </div>

    <!-- MENSAJES -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['mensaje']['tipo'] === 'exito' ? 'success' : 'danger' ?> alert-dismissible fade show" style="animation: fadeInUp 0.6s ease; border-radius: 1rem; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <i class="bi bi-<?= $_SESSION['mensaje']['tipo'] === 'exito' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
            <strong><?= $_SESSION['mensaje']['tipo'] === 'exito' ? '¡Éxito!' : '¡Error!' ?></strong>
            <?= htmlspecialchars($_SESSION['mensaje']['texto']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <!-- STATS CARDS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-label">Total Facturas</div>
            <div class="stat-value"><?= count($facturas) ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-label">Pagadas</div>
            <div class="stat-value">
                <?= count(array_filter($facturas, fn($f) => strtolower($f['estado']) === 'pagada')) ?>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-label">Pendientes</div>
            <div class="stat-value">
                <?= count(array_filter($facturas, fn($f) => strtolower($f['estado']) === 'pendiente')) ?>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-label">Monto Total</div>
            <div class="stat-value" style="font-size: 1.8rem;">S/ <?= number_format(array_sum(array_column($facturas, 'monto')), 2) ?></div>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="filters-section">
        <div class="filter-group">
            <label>🔍 Buscar</label>
            <input type="text" id="searchInput" class="filter-input" placeholder="Buscar por paciente, ID...">
        </div>
        <div class="filter-group">
            <label>📋 Estado</label>
            <select id="statusFilter" class="filter-input">
                <option value="">Todos los estados</option>
                <option value="pagada">Pagadas</option>
                <option value="pendiente">Pendientes</option>
                <option value="cancelada">Canceladas</option>
            </select>
        </div>
        <div class="filter-group">
            <label>📅 Ordenar por</label>
            <select id="sortFilter" class="filter-input">
                <option value="fecha-desc">Más recientes</option>
                <option value="fecha-asc">Más antiguas</option>
                <option value="monto-desc">Mayor monto</option>
                <option value="monto-asc">Menor monto</option>
            </select>
        </div>
    </div>

    <!-- TABLA -->
    <div class="facturas-section">
        <h2 class="section-title">
            <i class="bi bi-list-ul"></i>
            Listado de Facturas
        </h2>

        <?php if (empty($facturas)): ?>
            <div class="empty-state">
                <div class="empty-icon">💳</div>
                <h3>No hay facturas registradas</h3>
                <p>Comienza creando tu primera factura</p>
                <a href="<?= BASE_URL ?>/index.php?accion=crearFactura" class="btn-nueva-factura">
                    <i class="bi bi-plus-circle-fill"></i>
                    Crear Primera Factura
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="facturas-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Paciente</th>
                            <th>Fecha Emisión</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="facturasTableBody">
                        <?php foreach ($facturas as $factura): ?>
                            <tr data-estado="<?= strtolower($factura['estado']) ?>"
                                data-paciente="<?= htmlspecialchars($factura['paciente_nombre'] ?? '') ?>"
                                data-fecha="<?= $factura['emitida_en'] ?>"
                                data-monto="<?= $factura['monto'] ?>">
                                <td>
                                    <span class="factura-id">
                                        #<?= str_pad($factura['id'], 4, '0', STR_PAD_LEFT) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="paciente-info">
                                        <div class="paciente-avatar">
                                            <?= strtoupper(substr($factura['paciente_nombre'] ?? 'S', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="paciente-nombre">
                                                <?= htmlspecialchars($factura['paciente_nombre'] ?? 'Sin paciente') ?>
                                            </div>
                                            <div class="paciente-email">
                                                <i class="bi bi-envelope"></i>
                                                <?= htmlspecialchars($factura['paciente_email'] ?? 'N/A') ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fecha-cell">
                                        <?= date('d/m/Y', strtotime($factura['emitida_en'])) ?>
                                    </div>
                                    <div class="fecha-hora">
                                        <i class="bi bi-clock"></i>
                                        <?= date('H:i', strtotime($factura['emitida_en'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="monto">S/ <?= number_format($factura['monto'], 2) ?></span>
                                </td>
                                <td>
                                    <span class="estado-badge badge-<?= strtolower($factura['estado']) ?>">
                                        <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i>
                                        <?= ucfirst(htmlspecialchars($factura['estado'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= BASE_URL ?>/index.php?accion=verFactura&id=<?= $factura['id'] ?>" 
                                           class="btn-action btn-view" title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>/index.php?accion=editarFactura&id=<?= $factura['id'] ?>" 
                                           class="btn-action btn-edit" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn-action btn-delete swal-eliminar-factura"
                                                data-id="<?= $factura['id'] ?>"
                                                data-paciente="<?= htmlspecialchars($factura['paciente_nombre'] ?? 'Paciente') ?>"
                                                title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Filtros
document.getElementById('searchInput')?.addEventListener('input', function() {
    filterFacturas();
});

document.getElementById('statusFilter')?.addEventListener('change', function() {
    filterFacturas();
});

document.getElementById('sortFilter')?.addEventListener('change', function() {
    sortFacturas(this.value);
});

function filterFacturas() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const rows = document.querySelectorAll('#facturasTableBody tr');

    rows.forEach(row => {
        const paciente = row.dataset.paciente.toLowerCase();
        const estado = row.dataset.estado.toLowerCase();
        
        const matchSearch = paciente.includes(searchTerm) || 
                          row.querySelector('.factura-id').textContent.toLowerCase().includes(searchTerm);
        const matchStatus = !statusFilter || estado === statusFilter;
        
        row.style.display = (matchSearch && matchStatus) ? '' : 'none';
    });
}

function sortFacturas(sortBy) {
    const tbody = document.getElementById('facturasTableBody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        if (sortBy === 'fecha-desc') {
            return new Date(b.dataset.fecha) - new Date(a.dataset.fecha);
        } else if (sortBy === 'fecha-asc') {
            return new Date(a.dataset.fecha) - new Date(b.dataset.fecha);
        } else if (sortBy === 'monto-desc') {
            return parseFloat(b.dataset.monto) - parseFloat(a.dataset.monto);
        } else if (sortBy === 'monto-asc') {
            return parseFloat(a.dataset.monto) - parseFloat(b.dataset.monto);
        }
    });
    
    rows.forEach(row => tbody.appendChild(row));
}

// SweetAlert para eliminar
document.querySelectorAll('.swal-eliminar-factura').forEach(btn => {
    btn.addEventListener('click', function() {
        const facturaId = this.getAttribute('data-id');
        const pacienteNombre = this.getAttribute('data-paciente');

        Swal.fire({
            title: '¿Eliminar factura?',
            html: `
                <p>Estás a punto de eliminar la factura del paciente:</p>
                <p><strong>${pacienteNombre}</strong> (ID: #${facturaId.padStart(4, '0')})</p>
                <p style="color: #dc3545; font-weight: bold;">⚠️ Esta acción NO se puede deshacer</p>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `${window.BASE_URL}/index.php?accion=eliminarFactura&id=${facturaId}`;
            }
        });
    });
});
</script>