<?php
// =====================
// 📋 Vista: Log de Auditoría
// =====================
?>

<style>
.auditoria-container {
    padding: 25px;
    max-width: 1400px;
    margin: 0 auto;
}

.audit-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.audit-header h1 {
    font-size: 32px;
    color: #2c3e50;
    margin: 0;
    font-weight: 700;
}

.audit-actions {
    display: flex;
    gap: 10px;
}

.btn-audit {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-primary:hover {
    background: #2980b9;
}

.btn-danger {
    background: #e74c3c;
    color: white;
}

.btn-danger:hover {
    background: #c0392b;
}

.audit-filters {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.filter-group {
    display: grid;
    gap: 5px;
}

.filter-group label {
    font-size: 13px;
    font-weight: 600;
    color: #7f8c8d;
}

.filter-group input,
.filter-group select {
    padding: 8px 12px;
    border: 2px solid #ecf0f1;
    border-radius: 6px;
    font-size: 14px;
}

.audit-table-container {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow-x: auto;
}

.audit-table {
    width: 100%;
    border-collapse: collapse;
}

.audit-table thead {
    background: #f8f9fa;
}

.audit-table th {
    padding: 12px 15px;
    text-align: left;
    font-size: 13px;
    font-weight: 600;
    color: #7f8c8d;
    text-transform: uppercase;
    border-bottom: 2px solid #ecf0f1;
}

.audit-table td {
    padding: 15px;
    border-bottom: 1px solid #ecf0f1;
    font-size: 14px;
    color: #2c3e50;
}

.audit-table tbody tr:hover {
    background: #f8f9fa;
}

.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.badge-crear { background: #d4edda; color: #155724; }
.badge-editar { background: #fff3cd; color: #856404; }
.badge-eliminar { background: #f8d7da; color: #721c24; }
.badge-login { background: #d1ecf1; color: #0c5460; }
.badge-logout { background: #e2e3e5; color: #383d41; }
.badge-default { background: #e7f3ff; color: #004085; }

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #7f8c8d;
}

.empty-state-icon {
    font-size: 64px;
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 20px;
    margin: 0 0 10px 0;
    color: #2c3e50;
}

.empty-state p {
    font-size: 14px;
    margin: 0;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 25px;
}

.page-btn {
    padding: 8px 15px;
    border: 2px solid #ecf0f1;
    background: white;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.page-btn:hover {
    border-color: #3498db;
    color: #3498db;
}

.page-btn.active {
    background: #3498db;
    color: white;
    border-color: #3498db;
}

/* Modal para limpiar */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
}

.modal-content {
    background: white;
    margin: 10% auto;
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.modal-header h2 {
    font-size: 24px;
    margin: 0;
    color: #2c3e50;
}

.close {
    font-size: 28px;
    font-weight: bold;
    color: #7f8c8d;
    cursor: pointer;
}

.close:hover {
    color: #2c3e50;
}

.modal-body {
    margin-bottom: 25px;
}

.modal-footer {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

@media (max-width: 768px) {
    .audit-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .audit-table-container {
        padding: 15px;
    }
}
</style>

<div class="auditoria-container">
    <!-- Header -->
    <div class="audit-header">
        <h1>📋 Log de Auditoría</h1>
        <div class="audit-actions">
            <a href="?accion=configuracion" class="btn-audit btn-primary">
                <span>⬅️</span> Volver a Configuración
            </a>
            <button class="btn-audit btn-danger" onclick="abrirModalLimpiar()">
                <span>🗑️</span> Limpiar Registros
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="audit-filters">
        <form method="GET">
            <input type="hidden" name="accion" value="verAuditoria">
            <div class="filters-grid">
                <div class="filter-group">
                    <label>Acción</label>
                    <select name="filtro_accion">
                        <option value="">Todas las acciones</option>
                        <option value="crear">Crear</option>
                        <option value="editar">Editar</option>
                        <option value="eliminar">Eliminar</option>
                        <option value="login">Login</option>
                        <option value="logout">Logout</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Fecha desde</label>
                    <input type="date" name="fecha_desde">
                </div>
                
                <div class="filter-group">
                    <label>Fecha hasta</label>
                    <input type="date" name="fecha_hasta">
                </div>
                
                <div class="filter-group">
                    <label>Registros</label>
                    <select name="limite">
                        <option value="50">50 registros</option>
                        <option value="100" selected>100 registros</option>
                        <option value="250">250 registros</option>
                        <option value="500">500 registros</option>
                    </select>
                </div>
            </div>
            <div style="margin-top: 15px;">
                <button type="submit" class="btn-audit btn-primary">
                    <span>🔍</span> Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de Auditoría -->
    <div class="audit-table-container">
        <?php if (empty($logs)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <h3>No hay registros de auditoría</h3>
                <p>Los registros de auditoría aparecerán aquí cuando se realicen acciones en el sistema</p>
            </div>
        <?php else: ?>
            <table class="audit-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Descripción</th>
                        <th>Tabla</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td>
                                <?php
                                $fecha = new DateTime($log['fecha_accion']);
                                echo $fecha->format('d/m/Y H:i:s');
                                ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($log['usuario_nombre'] ?? 'Sistema') ?></strong><br>
                                <small style="color: #7f8c8d;"><?= htmlspecialchars($log['usuario_email'] ?? 'N/A') ?></small>
                            </td>
                            <td>
                                <?php
                                $accion = strtolower($log['accion']);
                                $badgeClass = 'badge-default';
                                
                                if (strpos($accion, 'crear') !== false) {
                                    $badgeClass = 'badge-crear';
                                } elseif (strpos($accion, 'editar') !== false || strpos($accion, 'actualizar') !== false) {
                                    $badgeClass = 'badge-editar';
                                } elseif (strpos($accion, 'eliminar') !== false) {
                                    $badgeClass = 'badge-eliminar';
                                } elseif ($accion === 'login') {
                                    $badgeClass = 'badge-login';
                                } elseif ($accion === 'logout') {
                                    $badgeClass = 'badge-logout';
                                }
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= htmlspecialchars($log['accion']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($log['descripcion']) ?></td>
                            <td>
                                <?= $log['tabla'] ? '<code>' . htmlspecialchars($log['tabla']) . '</code>' : '-' ?>
                            </td>
                            <td>
                                <small><?= htmlspecialchars($log['ip_address'] ?? 'N/A') ?></small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para limpiar registros -->
<div id="modalLimpiar" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>🗑️ Limpiar Registros de Auditoría</h2>
            <span class="close" onclick="cerrarModalLimpiar()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="formLimpiar" method="POST" action="?accion=limpiarAuditoria">
                <div class="filter-group">
                    <label>Eliminar registros más antiguos que:</label>
                    <select name="dias" style="width: 100%; margin-top: 10px;">
                        <option value="30">30 días</option>
                        <option value="60">60 días</option>
                        <option value="90" selected>90 días</option>
                        <option value="180">180 días</option>
                        <option value="365">1 año</option>
                    </select>
                </div>
                <div style="margin-top: 15px; padding: 15px; background: #fff3cd; border-radius: 6px; border-left: 4px solid #ffc107;">
                    <strong>⚠️ Advertencia:</strong> Esta acción no se puede deshacer.
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-audit btn-primary" onclick="cerrarModalLimpiar()">
                Cancelar
            </button>
            <button type="submit" form="formLimpiar" class="btn-audit btn-danger">
                <span>🗑️</span> Eliminar Registros
            </button>
        </div>
    </div>
</div>

<script>
function abrirModalLimpiar() {
    document.getElementById('modalLimpiar').style.display = 'block';
}

function cerrarModalLimpiar() {
    document.getElementById('modalLimpiar').style.display = 'none';
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('modalLimpiar');
    if (event.target == modal) {
        cerrarModalLimpiar();
    }
}
</script>