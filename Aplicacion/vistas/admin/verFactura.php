<?php
// =====================================================
// 💳 Vista: Ver Detalle de Factura
// Ubicación: Aplicacion/vistas/admin/verFactura.php
// =====================================================
$factura = $factura ?? null;

if (!$factura): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i>
        Factura no encontrada
    </div>
    <?php return; 
endif;
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

    .factura-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* HEADER */
    .page-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 2.5rem;
        border-radius: 1.5rem;
        margin-bottom: 2rem;
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
        gap: 1rem;
    }

    .header-title h1 {
        font-family: 'Poppins', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        margin: 0;
    }

    .factura-numero {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.2rem;
        margin-left: 1rem;
    }

    .btn-group-actions {
        display: flex;
        gap: 0.75rem;
    }

    .btn-action-header {
        background: white;
        color: var(--primary);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .btn-action-header:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .btn-back:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
    }

    /* LAYOUT */
    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
    }

    .card {
        background: white;
        border-radius: 1.25rem;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        animation: fadeInUp 0.6s ease;
    }

    .card-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* DATOS DEL PACIENTE */
    .paciente-card {
        background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
        border: 2px solid #e0e7ff;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .paciente-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .paciente-avatar {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        font-weight: 700;
    }

    .paciente-info h3 {
        font-family: 'Poppins', sans-serif;
        font-size: 1.3rem;
        font-weight: 700;
        margin: 0;
        color: #0f172a;
    }

    .paciente-email {
        color: #64748b;
        font-size: 0.95rem;
        margin-top: 0.25rem;
    }

    .info-row {
        display: flex;
        padding: 1rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #64748b;
        min-width: 150px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-value {
        color: #0f172a;
        font-weight: 500;
        flex: 1;
    }

    /* ESTADO BADGE */
    .estado-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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

    /* MONTO DESTACADO */
    .monto-destacado {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        padding: 2rem;
        border-radius: 1rem;
        text-align: center;
        color: white;
        margin-bottom: 1.5rem;
    }

    .monto-label {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .monto-valor {
        font-family: 'Poppins', sans-serif;
        font-size: 3rem;
        font-weight: 800;
        margin: 0;
    }

    /* FECHAS */
    .fecha-destacada {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        padding: 1.5rem;
        border-radius: 1rem;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .fecha-label {
        font-size: 0.85rem;
        color: #1e40af;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .fecha-valor {
        font-family: 'Poppins', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e3a8a;
    }

    /* CITA ASOCIADA */
    .cita-card {
        background: #fff8e1;
        border-left: 4px solid #fbbf24;
        padding: 1rem;
        border-radius: 0.5rem;
    }

    .cita-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #92400e;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .cita-info {
        color: #78350f;
        font-size: 0.95rem;
    }

    /* BOTONES DE ACCIÓN */
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }

    .btn-action-full {
        width: 100%;
        padding: 1rem;
        border-radius: 0.75rem;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .btn-success {
        background: var(--success);
        color: white;
    }

    .btn-danger {
        background: var(--danger);
        color: white;
    }

    .btn-action-full:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        color: white;
    }

    .alert-info {
        background: #e0f2fe;
        border: 2px solid #bae6fd;
        border-radius: 0.75rem;
        padding: 1rem;
        color: #075985;
        display: flex;
        align-items: start;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }

    /* RESPONSIVE */
    @media (max-width: 968px) {
        .content-grid {
            grid-template-columns: 1fr;
        }

        .factura-container {
            padding: 1rem;
        }

        .header-content {
            flex-direction: column;
            gap: 1rem;
        }

        .btn-group-actions {
            width: 100%;
            flex-direction: column;
        }

        .btn-action-header {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="factura-container">

    <!-- HEADER -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-title">
                <h1>💳 Factura</h1>
                <span class="factura-numero">#<?= str_pad($factura['id'], 4, '0', STR_PAD_LEFT) ?></span>
            </div>
            <div class="btn-group-actions">
                <a href="<?= BASE_URL ?>/index.php?accion=gestionarFacturas" class="btn-action-header btn-back">
                    <i class="bi bi-arrow-left"></i>
                    Volver
                </a>
                <a href="<?= BASE_URL ?>/index.php?accion=descargarFactura&id=<?= $factura['id'] ?>" 
                   class="btn-action-header" target="_blank">
                    <i class="bi bi-download"></i>
                    Descargar PDF
                </a>
            </div>
        </div>
    </div>

    <div class="content-grid">
        <!-- COLUMNA PRINCIPAL -->
        <div>
            <!-- INFORMACIÓN DEL PACIENTE -->
            <div class="card">
                <h2 class="card-title">
                    <i class="bi bi-person-badge"></i>
                    Información del Paciente
                </h2>

                <div class="paciente-card">
                    <div class="paciente-header">
                        <div class="paciente-avatar">
                            <?= strtoupper(substr($factura['paciente_nombre'] ?? 'P', 0, 1)) ?>
                        </div>
                        <div class="paciente-info">
                            <h3><?= htmlspecialchars($factura['paciente_nombre'] ?? 'Sin paciente') ?></h3>
                            <div class="paciente-email">
                                <i class="bi bi-envelope"></i>
                                <?= htmlspecialchars($factura['paciente_email'] ?? 'N/A') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DETALLES DE LA FACTURA -->
            <div class="card" style="margin-top: 2rem;">
                <h2 class="card-title">
                    <i class="bi bi-info-circle"></i>
                    Detalles de la Factura
                </h2>

                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-hash"></i>
                        ID de Factura
                    </div>
                    <div class="info-value">
                        #<?= str_pad($factura['id'], 4, '0', STR_PAD_LEFT) ?>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-flag"></i>
                        Estado
                    </div>
                    <div class="info-value">
                        <span class="estado-badge badge-<?= strtolower($factura['estado']) ?>">
                            <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i>
                            <?= ucfirst(htmlspecialchars($factura['estado'])) ?>
                        </span>
                    </div>
                </div>

                <?php if (!empty($factura['cita_id'])): ?>
                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-calendar-check"></i>
                        Cita Asociada
                    </div>
                    <div class="info-value">
                        <div class="cita-card">
                            <div class="cita-label">📅 Cita Médica</div>
                            <div class="cita-info">
                                ID: #<?= str_pad($factura['cita_id'], 4, '0', STR_PAD_LEFT) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="alert-info">
                    <i class="bi bi-info-circle" style="font-size: 1.5rem;"></i>
                    <div>
                        <strong>Nota:</strong> Esta factura fue generada automáticamente por el sistema.
                        Para cualquier consulta, contacte con el departamento de facturación.
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMNA LATERAL -->
        <div>
            <!-- MONTO -->
            <div class="card">
                <h2 class="card-title">
                    <i class="bi bi-cash-stack"></i>
                    Información Financiera
                </h2>

                <div class="monto-destacado">
                    <div class="monto-label">Monto Total</div>
                    <div class="monto-valor">S/ <?= number_format($factura['monto'], 2) ?></div>
                </div>
            </div>

            <!-- FECHAS -->
            <div class="card" style="margin-top: 1.5rem;">
                <h2 class="card-title">
                    <i class="bi bi-calendar-event"></i>
                    Fechas Importantes
                </h2>

                <div class="fecha-destacada">
                    <div class="fecha-label">📅 Fecha de Emisión</div>
                    <div class="fecha-valor">
                        <?= date('d/m/Y', strtotime($factura['emitida_en'])) ?>
                    </div>
                    <div style="color: #3b82f6; font-size: 0.9rem; margin-top: 0.5rem;">
                        <i class="bi bi-clock"></i>
                        <?= date('H:i', strtotime($factura['emitida_en'])) ?>
                    </div>
                </div>
            </div>

            <!-- ACCIONES -->
            <div class="action-buttons">
                <a href="<?= BASE_URL ?>/index.php?accion=editarFactura&id=<?= $factura['id'] ?>" 
                   class="btn-action-full btn-primary">
                    <i class="bi bi-pencil"></i>
                    Editar Factura
                </a>

                <?php if (strtolower($factura['estado']) === 'pendiente'): ?>
                <button class="btn-action-full btn-success" onclick="marcarComoPagada(<?= $factura['id'] ?>)">
                    <i class="bi bi-check-circle"></i>
                    Marcar como Pagada
                </button>
                <?php endif; ?>

                <button class="btn-action-full btn-danger" onclick="eliminarFactura(<?= $factura['id'] ?>)">
                    <i class="bi bi-trash"></i>
                    Eliminar Factura
                </button>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function marcarComoPagada(facturaId) {
    Swal.fire({
        title: '¿Marcar como pagada?',
        text: 'Esta acción cambiará el estado de la factura a PAGADA.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, marcar como pagada',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `${window.BASE_URL}/index.php?accion=marcarPagada&id=${facturaId}`;
        }
    });
}

function eliminarFactura(facturaId) {
    Swal.fire({
        title: '¿Eliminar factura?',
        html: `
            <p>Estás a punto de eliminar la factura <strong>#${String(facturaId).padStart(4, '0')}</strong></p>
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
}
</script>