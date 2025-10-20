<?php
// =====================
// 📊 Vista: Reportes Generales (Administrador)
// =====================
$totalPacientes    = $totalPacientes    ?? 0;
$totalCitas        = $totalCitas        ?? 0;
$totalFacturas     = $totalFacturas     ?? 0;
$totalRecaudado    = $totalRecaudado    ?? 0;
$facturasRecientes = $facturasRecientes ?? [];
?>

<style>
/* 📊 Estilos para reportes */
.reportes-container {
    padding: 25px;
    max-width: 1400px;
    margin: 0 auto;
}

.reportes-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 35px;
    flex-wrap: wrap;
    gap: 20px;
}

.reportes-header h1 {
    font-size: 32px;
    color: #2c3e50;
    margin: 0;
    font-weight: 700;
}

.botones-exportar {
    display: flex;
    gap: 12px;
}

.btn-exportar {
    padding: 12px 24px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
}

.btn-pdf {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
}

.btn-pdf:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
}

.btn-excel {
    background: linear-gradient(135deg, #27ae60, #229954);
    color: white;
}

.btn-excel:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
}

/* 📈 Grid de tarjetas */
.estadisticas-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    margin-bottom: 40px;
}

.tarjeta-estadistica {
    background: white;
    border-radius: 16px;
    padding: 35px 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.tarjeta-estadistica::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 6px;
    background: var(--color-acento);
}

.tarjeta-estadistica:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
}

.tarjeta-estadistica.citas { --color-acento: #9b59b6; }
.tarjeta-estadistica.facturas { --color-acento: #e67e22; }
.tarjeta-estadistica.recaudado { --color-acento: #27ae60; }

.tarjeta-icono {
    width: 80px;
    height: 80px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    background: var(--color-acento);
    color: white;
    margin-bottom: 18px;
}

.tarjeta-titulo {
    font-size: 13px;
    color: #7f8c8d;
    font-weight: 600;
    margin: 0 0 15px 0;
    text-transform: uppercase;
    letter-spacing: 1.5px;
}

.tarjeta-valor {
    font-size: 48px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    line-height: 1;
}

.tarjeta-valor.moneda {
    font-size: 42px;
}

.tarjeta-valor.moneda::before {
    content: 'S/ ';
    font-size: 26px;
    color: #95a5a6;
}

/* 📊 Sección de gráficos */
.graficos-wrapper {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 40px;
}

.grafico-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.grafico-card h3 {
    font-size: 20px;
    color: #2c3e50;
    margin: 0 0 25px 0;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.canvas-container {
    position: relative;
    height: 320px;
}

/* 🧾 Tabla de facturas */
.facturas-section {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.facturas-section h2 {
    font-size: 22px;
    color: #2c3e50;
    margin: 0 0 25px 0;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.tabla-facturas {
    width: 100%;
    border-collapse: collapse;
}

.tabla-facturas thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.tabla-facturas th {
    padding: 16px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: white;
}

.tabla-facturas tbody tr {
    border-bottom: 1px solid #ecf0f1;
    transition: background 0.2s ease;
}

.tabla-facturas tbody tr:hover {
    background: #f8f9fa;
}

.tabla-facturas td {
    padding: 16px;
    color: #2c3e50;
    font-size: 14px;
}

.estado-badge {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.estado-pagada {
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
    padding: 60px 20px;
    color: #95a5a6;
    font-size: 16px;
}

.mensaje-vacio::before {
    content: '📊';
    display: block;
    font-size: 64px;
    margin-bottom: 15px;
    opacity: 0.5;
}

/* 📱 Responsive */
@media (max-width: 1200px) {
    .estadisticas-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    
    .graficos-wrapper {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .reportes-container {
        padding: 15px;
    }
    
    .estadisticas-grid {
        grid-template-columns: 1fr;
    }
    
    .reportes-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .tabla-facturas {
        font-size: 12px;
    }
    
    .tabla-facturas th,
    .tabla-facturas td {
        padding: 12px 8px;
    }
}
</style>

<div class="reportes-container">
    <!-- 📊 Header -->
    <div class="reportes-header">
        <h1>📊 Reportes y Estadísticas</h1>
        
        <div class="botones-exportar">
            <a href="<?= BASE_URL ?>/index.php?accion=exportarPDF" class="btn-exportar btn-pdf">
                <span>📄</span> Exportar PDF
            </a>
            <a href="<?= BASE_URL ?>/index.php?accion=exportarExcel" class="btn-exportar btn-excel">
                <span>📊</span> Exportar Excel
            </a>
        </div>
    </div>

    <!-- 📈 Tarjetas de estadísticas -->
    <div class="estadisticas-grid">
        <div class="tarjeta-estadistica citas">
            <div class="tarjeta-icono">📅</div>
            <h3 class="tarjeta-titulo">Total Citas</h3>
            <p class="tarjeta-valor"><?= number_format($totalCitas) ?></p>
        </div>

        <div class="tarjeta-estadistica facturas">
            <div class="tarjeta-icono">🧾</div>
            <h3 class="tarjeta-titulo">Total Facturas</h3>
            <p class="tarjeta-valor"><?= number_format($totalFacturas) ?></p>
        </div>

        <div class="tarjeta-estadistica recaudado">
            <div class="tarjeta-icono">💰</div>
            <h3 class="tarjeta-titulo">Total Recaudado</h3>
            <p class="tarjeta-valor moneda"><?= number_format($totalRecaudado, 2) ?></p>
        </div>
    </div>

    <!-- 📊 Gráficos -->
    <div class="graficos-wrapper">
        <div class="grafico-card">
            <h3>📈 Tendencia Mensual</h3>
            <div class="canvas-container">
                <canvas id="graficoTendencia"></canvas>
            </div>
        </div>
        
        <div class="grafico-card">
            <h3>🥧 Distribución por Estado</h3>
            <div class="canvas-container">
                <canvas id="graficoEstados"></canvas>
            </div>
        </div>
    </div>

    <!-- 🧾 Tabla de facturas -->
    <div class="facturas-section">
        <h2>🧾 Facturas Recientes</h2>

        <?php if (empty($facturasRecientes)): ?>
            <div class="mensaje-vacio">
                No hay facturas registradas en el sistema
            </div>
        <?php else: ?>
            <table class="tabla-facturas">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Fecha Emisión</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($facturasRecientes as $factura): ?>
                        <tr>
                            <td><strong>#<?= htmlspecialchars($factura['id']) ?></strong></td>
                            <td><?= htmlspecialchars($factura['paciente']) ?></td>
                            <td><strong>S/ <?= number_format($factura['monto'], 2) ?></strong></td>
                            <td>
                                <?php 
                                $estado = strtolower($factura['estado']);
                                $claseEstado = 'estado-badge ';
                                if (str_contains($estado, 'pagada') || str_contains($estado, 'completada')) {
                                    $claseEstado .= 'estado-pagada';
                                } elseif (str_contains($estado, 'pendiente')) {
                                    $claseEstado .= 'estado-pendiente';
                                } else {
                                    $claseEstado .= 'estado-cancelada';
                                }
                                ?>
                                <span class="<?= $claseEstado ?>">
                                    <?= htmlspecialchars($factura['estado']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- 📊 Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 📈 Gráfico de tendencia - DATOS REALES
    const ctxTendencia = document.getElementById('graficoTendencia');
    if (ctxTendencia) {
        <?php 
        $meses = $tendenciaMensual['meses'] ?? [];
        $montos = $tendenciaMensual['montos'] ?? [];
        ?>
        
        new Chart(ctxTendencia, {
            type: 'line',
            data: {
                labels: <?= json_encode($meses) ?>,
                datasets: [{
                    label: 'Ingresos Mensuales',
                    data: <?= json_encode($montos) ?>,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointRadius: 5,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        display: true,
                        position: 'top',
                        labels: { font: { size: 13 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Ingresos: S/ ' + context.parsed.y.toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => 'S/ ' + value.toLocaleString('es-PE')
                        }
                    }
                }
            }
        });
    }

    // 🥧 Gráfico de estados - DATOS REALES
    const ctxEstados = document.getElementById('graficoEstados');
    if (ctxEstados) {
        <?php 
        $pagadas = $distribucionEstados['Pagada'] ?? 0;
        $pendientes = $distribucionEstados['Pendiente'] ?? 0;
        $canceladas = $distribucionEstados['Cancelada'] ?? 0;
        ?>
        
        new Chart(ctxEstados, {
            type: 'doughnut',
            data: {
                labels: ['Pagadas', 'Pendientes', 'Canceladas'],
                datasets: [{
                    data: [<?= $pagadas ?>, <?= $pendientes ?>, <?= $canceladas ?>],
                    backgroundColor: ['#27ae60', '#f39c12', '#e74c3c'],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { 
                            padding: 20, 
                            font: { size: 13 },
                            generateLabels: function(chart) {
                                const data = chart.data;
                                const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return {
                                        text: `${label}: ${value} (${percentage}%)`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.parsed;
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${context.label}: ${value} facturas (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>