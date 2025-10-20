<?php
// =====================
// 📅 Vista: Gestión de Citas (Administrador)
// =====================
$citas = $citas ?? [];
$totalCitas = $totalCitas ?? 0;
$citasConfirmadas = $citasConfirmadas ?? 0;
$citasPendientes = $citasPendientes ?? 0;
$citasCanceladas = $citasCanceladas ?? 0;
?>

<style>
.gestion-citas {
    padding: 25px;
    max-width: 1400px;
    margin: 0 auto;
}

.citas-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 20px;
}

.citas-header h1 {
    font-size: 32px;
    color: #2c3e50;
    margin: 0;
    font-weight: 700;
}

.btn-crear {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 28px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
}

.btn-crear:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

/* Estadísticas rápidas */
.estadisticas-citas {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-left: 4px solid var(--color);
}

.stat-card.total { --color: #3498db; }
.stat-card.confirmadas { --color: #27ae60; }
.stat-card.pendientes { --color: #f39c12; }
.stat-card.canceladas { --color: #e74c3c; }

.stat-card h3 {
    font-size: 13px;
    color: #7f8c8d;
    margin: 0 0 8px 0;
    text-transform: uppercase;
    font-weight: 600;
}

.stat-card p {
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
}

/* Tabla de citas */
.tabla-container {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow-x: auto;
}

.tabla-citas {
    width: 100%;
    border-collapse: collapse;
}

.tabla-citas thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.tabla-citas th {
    padding: 14px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: white;
}

.tabla-citas tbody tr {
    border-bottom: 1px solid #ecf0f1;
    transition: background 0.2s ease;
}

.tabla-citas tbody tr:hover {
    background: #f8f9fa;
}

.tabla-citas td {
    padding: 14px;
    color: #2c3e50;
    font-size: 14px;
}

.estado-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.estado-confirmada {
    background: #d4edda;
    color: #155724;
}

.estado-pendiente {
    background: #fff3cd;
    color: #856404;
}

.estado-cancelada {
    background: #f8d7da;
    color: #721c24;
}

.acciones {
    display: flex;
    gap: 8px;
}

.btn-accion {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-confirmar {
    background: #d4edda;
    color: #155724;
}

.btn-confirmar:hover {
    background: #27ae60;
    color: white;
}

.btn-editar {
    background: #d1ecf1;
    color: #0c5460;
}

.btn-editar:hover {
    background: #17a2b8;
    color: white;
}

.btn-cancelar {
    background: #f8d7da;
    color: #721c24;
}

.btn-cancelar:hover {
    background: #e74c3c;
    color: white;
}

.mensaje-vacio {
    text-align: center;
    padding: 60px 20px;
    color: #95a5a6;
    font-size: 16px;
}

.mensaje-vacio::before {
    content: '📅';
    display: block;
    font-size: 64px;
    margin-bottom: 15px;
    opacity: 0.5;
}

/* Alertas */
.alerta {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
}

.alerta-exito {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
}

.alerta-error {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
}

@media (max-width: 768px) {
    .gestion-citas {
        padding: 15px;
    }
    
    .estadisticas-citas {
        grid-template-columns: 1fr;
    }
    
    .tabla-citas {
        font-size: 12px;
    }
    
    .acciones {
        flex-direction: column;
    }
}
</style>

<div class="gestion-citas">
    <!-- Header -->
    <div class="citas-header">
        <h1>📅 Gestión de Citas</h1>
        <a href="<?= BASE_URL ?>/index.php?accion=crearCita" class="btn-crear">
            <span>➕</span> Nueva Cita
        </a>
    </div>

    <!-- Alertas -->
    <?php if (isset($_SESSION['exito'])): ?>
        <div class="alerta alerta-exito">
            ✅ <?= htmlspecialchars($_SESSION['exito']) ?>
        </div>
        <?php unset($_SESSION['exito']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alerta alerta-error">
            ❌ <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Estadísticas -->
    <div class="estadisticas-citas">
        <div class="stat-card total">
            <h3>Total Citas</h3>
            <p><?= number_format($totalCitas) ?></p>
        </div>
        <div class="stat-card confirmadas">
            <h3>Confirmadas</h3>
            <p><?= number_format($citasConfirmadas) ?></p>
        </div>
        <div class="stat-card pendientes">
            <h3>Pendientes</h3>
            <p><?= number_format($citasPendientes) ?></p>
        </div>
        <div class="stat-card canceladas">
            <h3>Canceladas</h3>
            <p><?= number_format($citasCanceladas) ?></p>
        </div>
    </div>

    <!-- Tabla de citas -->
    <div class="tabla-container">
        <?php if (empty($citas)): ?>
            <div class="mensaje-vacio">
                No hay citas registradas en el sistema
            </div>
        <?php else: ?>
            <table class="tabla-citas">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Doctor</th>
                        <th>Especialidad</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($citas as $cita): ?>
                        <tr>
                            <td><strong>#<?= htmlspecialchars($cita['id']) ?></strong></td>
                            <td><?= htmlspecialchars($cita['paciente_nombre'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($cita['doctor_nombre'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($cita['especialidad'] ?? 'N/A') ?></td>
                            <td><?= date('d/m/Y', strtotime($cita['fecha'])) ?></td>
                            <td>
                                <?php 
                                $estado = strtolower($cita['estado']);
                                $claseEstado = 'estado-badge ';
                                if (str_contains($estado, 'confirmada')) {
                                    $claseEstado .= 'estado-confirmada';
                                } elseif (str_contains($estado, 'pendiente')) {
                                    $claseEstado .= 'estado-pendiente';
                                } else {
                                    $claseEstado .= 'estado-cancelada';
                                }
                                ?>
                                <span class="<?= $claseEstado ?>">
                                    <?= htmlspecialchars($cita['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="acciones">
                                    <?php if (str_contains(strtolower($cita['estado']), 'pendiente')): ?>
                                        <a href="<?= BASE_URL ?>/index.php?accion=confirmarCita&id=<?= $cita['id'] ?>" 
                                           class="btn-accion btn-confirmar"
                                           onclick="return confirm('¿Confirmar esta cita?')">
                                            ✓ Confirmar
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="<?= BASE_URL ?>/index.php?accion=editar&id=<?= $cita['id'] ?>" 
                                       class="btn-accion btn-editar">
                                        ✎ Editar
                                    </a>
                                    
                                                                        <?php if (!str_contains(strtolower($cita['estado']), 'cancelada')): ?>
                                                                            <a href="<?= BASE_URL ?>/index.php?accion=cancelar&id=<?= $cita['id'] ?>" 
                                                                               class="btn-accion btn-cancelar"
                                                                               onclick="return confirm('¿Cancelar esta cita?')">
                                                                                ✗ Cancelar
                                                                            </a>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            <?php endif; ?>
                                        </div>
                                    </div>