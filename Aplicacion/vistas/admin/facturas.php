<?php
// Aplicacion/vistas/admin/facturas.php
?>
<div class="container-fluid px-4 py-5">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">
                        <i class="fas fa-file-invoice text-primary me-2"></i>
                        Gestión de Facturas
                    </h1>
                    <p class="text-muted mb-0">Administra todas las facturas del sistema</p>
                </div>
                <a href="?accion=crearFactura" class="btn btn-primary btn-lg shadow-sm">
                    <i class="fas fa-plus-circle me-2"></i> Nueva Factura
                </a>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-<?= $_SESSION['mensaje']['tipo'] === 'exito' ? 'success' : 'danger' ?> alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-<?= $_SESSION['mensaje']['tipo'] === 'exito' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                    <strong><?= $_SESSION['mensaje']['tipo'] === 'exito' ? '¡Éxito!' : '¡Error!' ?></strong>
                    <?= htmlspecialchars($_SESSION['mensaje']['texto']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Facturas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($facturas) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pagadas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($facturas, fn($f) => strtolower($f['estado']) === 'pagada')) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pendientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($facturas, fn($f) => strtolower($f['estado']) === 'pendiente')) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Monto Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                S/ <?= number_format(array_sum(array_column($facturas, 'monto')), 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-white py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-list me-2"></i>Listado de Facturas
                            </h6>
                        </div>
                        <div class="col-auto">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="searchInput" placeholder="Buscar...">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="facturas-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">
                                        <i class="fas fa-hashtag me-1"></i>ID
                                    </th>
                                    <th class="py-3">
                                        <i class="fas fa-user me-1"></i>Paciente
                                    </th>
                                    <th class="py-3">
                                        <i class="fas fa-envelope me-1"></i>Email
                                    </th>
                                    <th class="py-3">
                                        <i class="fas fa-calendar me-1"></i>Fecha Emisión
                                    </th>
                                    <th class="py-3">
                                        <i class="fas fa-money-bill-wave me-1"></i>Monto
                                    </th>
                                    <th class="py-3">
                                        <i class="fas fa-info-circle me-1"></i>Estado
                                    </th>
                                    <th class="text-center py-3">
                                        <i class="fas fa-cog me-1"></i>Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($facturas)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="py-5">
                                                <i class="fas fa-folder-open fa-4x text-muted mb-3 d-block"></i>
                                                <h5 class="text-muted">No hay facturas registradas</h5>
                                                <p class="text-muted mb-4">Comienza creando tu primera factura</p>
                                                <a href="?accion=crearFactura" class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i>Crear Factura
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($facturas as $factura): ?>
                                        <tr class="align-middle">
                                            <td class="px-4">
                                                <span class="badge bg-secondary fs-6">#<?= str_pad($factura['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-primary text-white me-2">
                                                        <?= strtoupper(substr($factura['paciente_nombre'] ?? 'S', 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold"><?= htmlspecialchars($factura['paciente_nombre'] ?? 'Sin paciente') ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    <?= htmlspecialchars($factura['paciente_email'] ?? 'N/A') ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-semibold"><?= date('d/m/Y', strtotime($factura['emitida_en'])) ?></div>
                                                    <small class="text-muted"><?= date('H:i', strtotime($factura['emitida_en'])) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fs-5 fw-bold text-success">S/ <?= number_format($factura['monto'], 2) ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                $estadoClass = match(strtolower($factura['estado'])) {
                                                    'pagada' => 'success',
                                                    'pendiente' => 'warning',
                                                    'cancelada' => 'danger',
                                                    default => 'secondary'
                                                };
                                                $estadoIcon = match(strtolower($factura['estado'])) {
                                                    'pagada' => 'check-circle',
                                                    'pendiente' => 'clock',
                                                    'cancelada' => 'times-circle',
                                                    default => 'info-circle'
                                                };
                                                ?>
                                                <span class="badge rounded-pill bg-<?= $estadoClass ?> px-3 py-2">
                                                    <i class="fas fa-<?= $estadoIcon ?> me-1"></i>
                                                    <?= ucfirst(htmlspecialchars($factura['estado'])) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="?accion=verFactura&id=<?= $factura['id'] ?>" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       data-bs-toggle="tooltip"
                                                       title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="?accion=editarFactura&id=<?= $factura['id'] ?>" 
                                                       class="btn btn-sm btn-outline-warning"
                                                       data-bs-toggle="tooltip"
                                                       title="Editar factura">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?accion=eliminarFactura&id=<?= $factura['id'] ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       data-bs-toggle="tooltip"
                                                       title="Eliminar factura"
                                                       onclick="return confirm('⚠️ ¿Está seguro de eliminar esta factura?\n\nEsta acción no se puede deshacer.')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if (!empty($facturas)): ?>
                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Mostrando <?= count($facturas) ?> facturas
                        </small>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#"><i class="fas fa-chevron-left"></i></a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary-color: #4e73df;
    --success-color: #1cc88a;
    --warning-color: #f6c23e;
    --danger-color: #e74a3b;
    --info-color: #36b9cc;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.border-left-primary {
    border-left: 0.25rem solid var(--primary-color) !important;
}

.border-left-success {
    border-left: 0.25rem solid var(--success-color) !important;
}

.border-left-warning {
    border-left: 0.25rem solid var(--warning-color) !important;
}

.border-left-info {
    border-left: 0.25rem solid var(--info-color) !important;
}

.card {
    border-radius: 0.75rem;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.shadow-lg {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
}

.table-hover tbody tr {
    transition: all 0.2s ease;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fc;
    transform: scale(1.01);
}

.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.1rem;
}

.btn-group .btn {
    transition: all 0.2s ease;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.badge {
    font-weight: 500;
    letter-spacing: 0.5px;
}

.text-xs {
    font-size: 0.7rem;
    font-weight: 700;
}

.input-group-sm .form-control {
    border-radius: 0.5rem 0 0 0.5rem;
}

.card-header {
    border-bottom: 1px solid #e3e6f0;
}

.card-footer {
    border-top: 1px solid #e3e6f0;
}

@media (max-width: 768px) {
    .btn-lg {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}
</style>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Search functionality
    document.getElementById('searchInput')?.addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#facturas-table tbody tr');
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });
});
</script>