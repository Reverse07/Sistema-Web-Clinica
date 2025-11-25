<?php
// =====================
// 💳 Vista: Detalle de Factura PREMIUM
// =====================
$factura = $factura ?? null;

if (!$factura) {
    header("Location: " . BASE_URL . "/index.php?accion=misFacturas");
    exit;
}

$nombrePaciente = $_SESSION['nombre'] ?? 'Paciente';
$esAdmin = ($_SESSION['rol'] ?? '') === 'admin';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #<?= str_pad($factura['id'], 6, '0', STR_PAD_LEFT) ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #764ba2;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --light-bg: #f8fafc;
            --dark-text: #0f172a;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .factura-wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .btn {
            padding: 0.875rem 1.75rem;
            border-radius: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-back {
            background: white;
            color: var(--dark-text);
            border: 2px solid #e2e8f0;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }

        .btn-download {
            background: linear-gradient(135deg, var(--success), #059669);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-print {
            background: linear-gradient(135deg, var(--info), var(--primary));
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }

        /* FACTURA CARD */
        .factura-card {
            background: white;
            border-radius: 1.5rem;
            padding: 3rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .factura-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        }

        /* HEADER */
        .factura-header {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 3rem;
            padding-bottom: 2rem;
            border-bottom: 3px solid #f1f5f9;
            margin-bottom: 2rem;
        }

        .company-info h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            margin: 0 0 0.5rem 0;
        }

        .company-info p {
            color: #64748b;
            margin: 0.25rem 0;
            font-size: 0.95rem;
        }

        .factura-meta {
            text-align: right;
        }

        .factura-numero {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark-text);
            margin-bottom: 0.5rem;
        }

        .factura-fecha {
            color: #64748b;
            font-size: 0.95rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 0.5rem;
        }

        .badge-pagada {
            background: linear-gradient(135deg, #d4fc79, #96e6a1);
            color: #1e4620;
        }

        .badge-pendiente {
            background: linear-gradient(135deg, #ffeaa7, #fdcb6e);
            color: #5a3e00;
        }

        /* CLIENTE INFO */
        .cliente-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: var(--light-bg);
            border-radius: 1rem;
        }

        .section-title {
            font-weight: 700;
            font-size: 0.85rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }

        .cliente-info p {
            margin: 0.5rem 0;
            color: var(--dark-text);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .cliente-info i {
            color: var(--primary);
            width: 20px;
        }

        /* DETALLES */
        .detalles-section {
            margin-bottom: 2rem;
        }

        .detalles-table {
            width: 100%;
            border-collapse: collapse;
        }

        .detalles-table thead {
            background: var(--light-bg);
        }

        .detalles-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 700;
            color: var(--dark-text);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detalles-table td {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .detalles-table tbody tr:hover {
            background: var(--light-bg);
        }

        /* TOTALES */
        .totales-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 3px solid #f1f5f9;
        }

        .totales-box {
            min-width: 350px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            font-size: 1rem;
        }

        .total-row.subtotal {
            color: #64748b;
        }

        .total-row.final {
            padding: 1.25rem 0;
            border-top: 2px solid #e2e8f0;
            margin-top: 0.5rem;
        }

        .total-row.final .label {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark-text);
        }

        .total-row.final .value {
            font-size: 2rem;
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            color: var(--primary);
        }

        /* FOOTER */
        .factura-footer {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 2px solid #f1f5f9;
            text-align: center;
            color: #64748b;
            font-size: 0.9rem;
        }

        /* PRINT STYLES */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .action-bar {
                display: none !important;
            }

            .factura-card {
                box-shadow: none;
                padding: 2rem;
            }
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .factura-wrapper {
                padding: 0 1rem;
            }

            .factura-card {
                padding: 1.5rem;
            }

            .factura-header {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .factura-meta {
                text-align: left;
            }

            .action-bar {
                flex-direction: column;
                gap: 1rem;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .totales-box {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="factura-wrapper">
    
    <!-- ACTION BAR -->
    <div class="action-bar">
        <a href="<?= BASE_URL ?>/index.php?accion=<?= $esAdmin ? 'gestionarFacturas' : 'misFacturas' ?>" class="btn btn-back">
            <i class="bi bi-arrow-left"></i>
            Volver
        </a>
        <div style="display: flex; gap: 0.75rem;">
            <button onclick="window.print()" class="btn btn-print">
                <i class="bi bi-printer"></i>
                Imprimir
            </button>
            <a href="<?= BASE_URL ?>/index.php?accion=descargarFactura&id=<?= $factura['id'] ?>" class="btn btn-download">
                <i class="bi bi-download"></i>
                Descargar PDF
            </a>
        </div>
    </div>

    <!-- FACTURA -->
    <div class="factura-card" id="factura">
        
        <!-- HEADER -->
        <div class="factura-header">
            <div class="company-info">
                <h1>🏥 Clínica Salud Total</h1>
                <p><i class="bi bi-geo-alt"></i> Av. Principal 123, Lima, Perú</p>
                <p><i class="bi bi-telephone"></i> +51 999 888 777</p>
                <p><i class="bi bi-envelope"></i> facturacion@clinica.com</p>
                <p><i class="bi bi-card-text"></i> RUC: 20123456789</p>
            </div>
            <div class="factura-meta">
                <div class="factura-numero">
                    FACTURA #<?= str_pad($factura['id'], 6, '0', STR_PAD_LEFT) ?>
                </div>
                <div class="factura-fecha">
                    <i class="bi bi-calendar"></i>
                    Fecha: <?= date('d/m/Y', strtotime($factura['emitida_en'])) ?>
                </div>
                <div>
                    <span class="status-badge badge-<?= strtolower($factura['estado']) === 'pagada' ? 'pagada' : 'pendiente' ?>">
                        <?= htmlspecialchars($factura['estado']) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- CLIENTE -->
        <div class="cliente-section">
            <div class="section-title">
                <i class="bi bi-person"></i> Datos del Cliente
            </div>
            <div class="cliente-info">
                <p>
                    <i class="bi bi-person-circle"></i>
                    <strong>Nombre:</strong> <?= htmlspecialchars($factura['paciente_nombre']) ?>
                </p>
                <p>
                    <i class="bi bi-envelope"></i>
                    <strong>Email:</strong> <?= htmlspecialchars($factura['paciente_email']) ?>
                </p>
                <?php if (!empty($factura['paciente_telefono'])): ?>
                <p>
                    <i class="bi bi-telephone"></i>
                    <strong>Teléfono:</strong> <?= htmlspecialchars($factura['paciente_telefono']) ?>
                </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- DETALLES -->
        <div class="detalles-section">
            <div class="section-title">
                <i class="bi bi-list-ul"></i> Detalle de Servicios
            </div>
            <table class="detalles-table">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th style="text-align: center;">Cantidad</th>
                        <th style="text-align: right;">P. Unitario</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>Consulta Médica</strong>
                            <?php if (!empty($factura['doctor_nombre'])): ?>
                            <br>
                            <small style="color: #64748b;">
                                <i class="bi bi-person-badge"></i>
                                Dr. <?= htmlspecialchars($factura['doctor_nombre']) ?>
                            </small>
                            <?php endif; ?>
                            <?php if (!empty($factura['cita_fecha'])): ?>
                            <br>
                            <small style="color: #64748b;">
                                <i class="bi bi-calendar-event"></i>
                                <?= date('d/m/Y h:i A', strtotime($factura['cita_fecha'])) ?>
                            </small>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">1</td>
                        <td style="text-align: right;">S/ <?= number_format($factura['monto'], 2) ?></td>
                        <td style="text-align: right;"><strong>S/ <?= number_format($factura['monto'], 2) ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- TOTALES -->
        <div class="totales-section">
            <div class="totales-box">
                <div class="total-row subtotal">
                    <span class="label">Subtotal:</span>
                    <span class="value">S/ <?= number_format($factura['monto'], 2) ?></span>
                </div>
                <div class="total-row subtotal">
                    <span class="label">IGV (18%):</span>
                    <span class="value">S/ <?= number_format($factura['monto'] * 0.18, 2) ?></span>
                </div>
                <div class="total-row final">
                    <span class="label">TOTAL:</span>
                    <span class="value">S/ <?= number_format($factura['monto'] * 1.18, 2) ?></span>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="factura-footer">
            <p><strong>Gracias por confiar en nosotros</strong></p>
            <p>Este documento es una representación impresa de una factura electrónica</p>
            <p style="margin-top: 1rem; font-size: 0.8rem;">
                Generado el <?= date('d/m/Y H:i') ?> | Sistema de Gestión Clínica v1.0
            </p>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>