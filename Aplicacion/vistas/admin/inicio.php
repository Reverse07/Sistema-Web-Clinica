<?php
// =====================
// 📊 Vista: Dashboard Administrativo
// =====================
// Variables recibidas desde AdminControlador::dashboard()
// $totalUsuarios, $totalDoctores, $totalPacientes, $totalCitas, $citasRecientes, etc.
// =====================

$totalUsuarios = $totalUsuarios ?? 0;
$totalDoctores = $totalDoctores ?? 0;
$totalPacientes = $totalPacientes ?? 0;
$totalCitas = $totalCitas ?? 0;
$citasHoy = $citasHoy ?? 0;
$citasPendientes = $citasPendientes ?? 0;
$citasRecientes = $citasRecientes ?? [];
?>

<style>
/* 📊 Estilos del Dashboard */
.dashboard-admin {
    padding: 25px;
    max-width: 1400px;
    margin: 0 auto;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 35px;
    flex-wrap: wrap;
    gap: 20px;
}

.dashboard-header h1 {
    font-size: 32px;
    color: #2c3e50;
    margin: 0;
    font-weight: 700;
}

.fecha-actual {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 15px;
    color: #7f8c8d;
    background: white;
    padding: 12px 20px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

/* 📈 Grid de KPIs */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 35px;
}

.card-kpi {
    background: white;
    border-radius: 16px;
    padding: 28px 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.card-kpi::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: var(--color-acento);
}

.card-kpi:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
}

.card-kpi.usuarios { --color-acento: #3498db; }
.card-kpi.doctores { --color-acento: #e67e22; }
.card-kpi.pacientes { --color-acento: #9b59b6; }
.card-kpi.citas { --color-acento: #1abc9c; }
.card-kpi.hoy { --color-acento: #f39c12; }
.card-kpi.pendientes { --color-acento: #e74c3c; }

.card-kpi-icono {
    width: 70px;
    height: 70px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    background: var(--color-acento);
    color: white;
    margin-bottom: 15px;
}

.card-kpi h3 {
    font-size: 13px;
    color: #7f8c8d;
    font-weight: 600;
    margin: 0 0 12px 0;
    text-transform: uppercase;
    letter-spacing: 1.2px;
}

.card-kpi p {
    font-size: 42px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    line-height: 1;
}

/* 📊 Sección de accesos rápidos */
.accesos-rapidos {
    margin-bottom: 35px;
}

.accesos-rapidos h2 {
    font-size: 22px;
    color: #2c3e50;
    margin: 0 0 20px 0;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.accesos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.btn-acceso {
    background: white;
    border: 2px solid #ecf0f1;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    text-decoration: none;
    color: #2c3e50;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.btn-acceso:hover {
    border-color: #3498db;
    background: #f8f9fa;
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(52, 152, 219, 0.2);
}

.btn-acceso-icono {
    font-size: 32px;
    margin-bottom: 5px;
}

/* 📋 Sección de actividad reciente */
.actividad-reciente {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.actividad-reciente h2 {
    font-size: 22px;
    color: #2c3e50;
    margin: 0 0 25px 0;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.tabla-actividad {
    width: 100%;
    border-collapse: collapse;
}

.tabla-actividad thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.tabla-actividad th {
    padding: 14px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: white;
}

.tabla-actividad tbody tr {
    border-bottom: 1px solid #ecf0f1;
    transition: background 0.2s ease;
}

.tabla-actividad tbody tr:hover {
    background: #f8f9fa;
}

.tabla-actividad td {
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

.mensaje-vacio {
    text-align: center;
    padding: 50px 20px;
    color: #95a5a6;
    font-size: 16px;
}

.mensaje-vacio::before {
    content: '📅';
    display: block;
    font-size: 56px;
    margin-bottom: 15px;
    opacity: 0.5;
}

/* 📱 Responsive */
@media (max-width: 768px) {
    .dashboard-admin {
        padding: 15px;
    }
    
    .kpi-grid,
    .accesos-grid {
        grid-template-columns: 1fr;
    }
    
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .tabla-actividad {
        font-size: 12px;
    }
    
    .tabla-actividad th,
    .tabla-actividad td {
        padding: 10px 8px;
    }
}
</style>

<div class="dashboard-admin">
    <!-- 📊 Header con fecha -->
    <div class="dashboard-header">
        <h1>📊 Panel Administrativo</h1>
        <div class="fecha-actual">
            <span>📅</span>
            <span><?= date('l, d \d\e F \d\e Y') ?></span>
        </div>
    </div>

    <!-- 📈 KPIs principales -->
    <div class="kpi-grid">
        <div class="card-kpi usuarios">
            <div class="card-kpi-icono">👥</div>
            <h3>Total Usuarios</h3>
            <p><?= number_format($totalUsuarios) ?></p>
        </div>

        <div class="card-kpi doctores">
            <div class="card-kpi-icono">🩺</div>
            <h3>Doctores</h3>
            <p><?= number_format($totalDoctores) ?></p>
        </div>

        <div class="card-kpi pacientes">
            <div class="card-kpi-icono">🧑‍🤝‍🧑</div>
            <h3>Pacientes</h3>
            <p><?= number_format($totalPacientes) ?></p>
        </div>

        <div class="card-kpi citas">
            <div class="card-kpi-icono">📅</div>
            <h3>Total Citas</h3>
            <p><?= number_format($totalCitas) ?></p>
        </div>

        <div class="card-kpi hoy">
            <div class="card-kpi-icono">🕐</div>
            <h3>Citas Hoy</h3>
            <p><?= number_format($citasHoy) ?></p>
        </div>

        <div class="card-kpi pendientes">
            <div class="card-kpi-icono">⏳</div>
            <h3>Pendientes</h3>
            <p><?= number_format($citasPendientes) ?></p>
        </div>
    </div>

    <!-- ⚡ Accesos rápidos -->
    <div class="accesos-rapidos">
        <h2>⚡ Accesos Rápidos</h2>
        <div class="accesos-grid">
            <a href="<?= BASE_URL ?>/index.php?accion=gestionarUsuarios" class="btn-acceso">
                <span class="btn-acceso-icono">👥</span>
                Gestionar Usuarios
            </a>
            <a href="<?= BASE_URL ?>/index.php?accion=gestionarDoctores" class="btn-acceso">
                <span class="btn-acceso-icono">🩺</span>
                Gestionar Doctores
            </a>
            <a href="<?= BASE_URL ?>/index.php?accion=gestionarPacientes" class="btn-acceso">
                <span class="btn-acceso-icono">🧑‍🤝‍🧑</span>
                Gestionar Pacientes
            </a>
            <a href="<?= BASE_URL ?>/index.php?accion=gestionarCitas" class="btn-acceso">
                <span class="btn-acceso-icono">📅</span>
                Gestionar Citas
            </a>
            <a href="<?= BASE_URL ?>/index.php?accion=gestionarFacturas" class="btn-acceso">
                <span class="btn-acceso-icono">💳</span>
                Gestionar Facturas
            </a>
            <a href="<?= BASE_URL ?>/index.php?accion=verReportes" class="btn-acceso">
                <span class="btn-acceso-icono">📊</span>
                Ver Reportes
            </a>
        </div>
    </div>

    <!-- 📋 Actividad reciente -->
    <div class="actividad-reciente">
        <h2>📋 Citas Recientes</h2>

        <?php if (empty($citasRecientes)): ?>
            <div class="mensaje-vacio">
                No hay citas registradas en el sistema
            </div>
        <?php else: ?>
            <table class="tabla-actividad">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Doctor</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($citasRecientes as $cita): ?>
                        <tr>
                            <td><strong>#<?= htmlspecialchars($cita['id']) ?></strong></td>
                            <td><?= htmlspecialchars($cita['paciente']) ?></td>
                            <td><?= htmlspecialchars($cita['doctor']) ?></td>
                            <td><?= date('d/m/Y', strtotime($cita['fecha'])) ?></td>
                            <td><?= date('H:i', strtotime($cita['hora'])) ?></td>
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
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>