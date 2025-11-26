<?php
// =====================================================
// ✏️ Vista: Editar Factura
// Ubicación: Aplicacion/vistas/admin/editarFactura.php
// =====================================================
$factura = $factura ?? null;
$pacientes = $pacientes ?? [];
$citas = $citas ?? [];

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
        --danger: #ef4444;
        --warning: #f59e0b;
        --info: #3b82f6;
    }

    * {
        font-family: 'Inter', sans-serif;
    }

    html, body {
        background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
        min-height: 100vh;
    }

    .container-form {
        max-width: 900px;
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
        padding: 2rem;
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
        font-size: 1.8rem;
        font-weight: 800;
        margin: 0;
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
        transform: translateY(-2px);
    }

    /* CARD FORM */
    .form-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        animation: fadeInUp 0.6s ease 0.2s backwards;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .section-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.2rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 3px solid var(--primary);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* FORM GROUPS */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label .required {
        color: var(--danger);
        font-size: 1.2rem;
    }

    .form-control, .form-select {
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.875rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .form-control:hover, .form-select:hover {
        border-color: #cbd5e1;
    }

    .form-text {
        font-size: 0.85rem;
        color: #64748b;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* SELECT CUSTOM */
    .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23667eea' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 16px 12px;
    }

    /* INFO ACTUAL */
    .info-actual {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 2px solid #bae6fd;
        border-radius: 1rem;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .info-actual-title {
        font-weight: 700;
        color: #0369a1;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-actual-content {
        font-size: 0.9rem;
        color: #075985;
        line-height: 1.6;
    }

    .info-actual-content strong {
        color: #0c4a6e;
    }

    /* ESTADO BADGES */
    .estado-option {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 600;
        display: inline-block;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .estado-pendiente {
        background: #fef3c7;
        color: #92400e;
        border: 2px solid #fbbf24;
    }

    .estado-pagada {
        background: #d1fae5;
        color: #065f46;
        border: 2px solid #10b981;
    }

    .estado-cancelada {
        background: #fee2e2;
        color: #991b1b;
        border: 2px solid #ef4444;
    }

    /* BUTTONS */
    .btn-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 2px solid #f1f5f9;
    }

    .btn-custom {
        padding: 0.875rem 2rem;
        border-radius: 0.75rem;
        border: none;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .btn-secondary-custom {
        background: #e2e8f0;
        color: #475569;
    }

    .btn-secondary-custom:hover {
        background: #cbd5e1;
        color: #334155;
        transform: translateY(-2px);
    }

    /* ALERT */
    .alert-warning-custom {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 2px solid #f59e0b;
        border-radius: 1rem;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: start;
        gap: 1rem;
    }

    .alert-warning-custom i {
        font-size: 1.5rem;
        color: #d97706;
    }

    .alert-warning-custom .alert-content {
        flex: 1;
    }

    .alert-warning-custom .alert-title {
        font-weight: 700;
        color: #92400e;
        margin-bottom: 0.25rem;
    }

    .alert-warning-custom .alert-text {
        color: #78350f;
        font-size: 0.9rem;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .container-form {
            padding: 1rem;
        }

        .form-card {
            padding: 1.5rem;
        }

        .header-content {
            flex-direction: column;
            gap: 1rem;
        }

        .btn-actions {
            flex-direction: column;
        }

        .btn-custom {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="container-form">

    <!-- HEADER -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-title">
                <h1>✏️ Editar Factura</h1>
            </div>
            <a href="<?= BASE_URL ?>/index.php?accion=verFactura&id=<?= $factura['id'] ?>" class="btn-back">
                <i class="bi bi-arrow-left"></i>
                Volver
            </a>
        </div>
    </div>

    <!-- ALERT WARNING -->
    <div class="alert-warning-custom">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div class="alert-content">
            <div class="alert-title">⚠️ Atención</div>
            <div class="alert-text">
                Estás editando una factura existente. Los cambios afectarán los registros del sistema.
            </div>
        </div>
    </div>

    <!-- FORMULARIO -->
    <div class="form-card">
        <form action="<?= BASE_URL ?>/index.php?accion=actualizarFactura&id=<?= $factura['id'] ?>" method="POST" id="formEditarFactura">
            
            <!-- INFORMACIÓN ACTUAL -->
            <div class="info-actual">
                <div class="info-actual-title">
                    <i class="bi bi-info-circle-fill"></i>
                    Información Actual
                </div>
                <div class="info-actual-content">
                    <strong>Factura N°:</strong> #<?= str_pad($factura['id'], 4, '0', STR_PAD_LEFT) ?><br>
                    <strong>Emitida el:</strong> <?= date('d/m/Y H:i', strtotime($factura['emitida_en'])) ?><br>
                    <strong>Paciente actual:</strong> <?= htmlspecialchars($factura['paciente_nombre'] ?? 'Sin paciente') ?><br>
                    <strong>Estado actual:</strong> 
                    <span class="estado-option estado-<?= strtolower($factura['estado']) ?>">
                        <?= ucfirst(htmlspecialchars($factura['estado'])) ?>
                    </span>
                </div>
            </div>

            <!-- SECCIÓN 1: DATOS PRINCIPALES -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-receipt"></i>
                    Datos Principales
                </h3>

                <div class="row">
                    <!-- PACIENTE -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-person"></i>
                                Paciente
                                <span class="required">*</span>
                            </label>
                            <select name="paciente_id" class="form-select" required>
                                <option value="">-- Seleccionar Paciente --</option>
                                <?php foreach ($pacientes as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $factura['paciente_id'] == $p['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['nombre']) ?> 
                                        <?php if (!empty($p['email'])): ?>
                                            - <?= htmlspecialchars($p['email']) ?>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i>
                                Paciente al que se emitirá la factura
                            </div>
                        </div>
                    </div>

                    <!-- CITA -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-calendar-check"></i>
                                Cita Asociada
                                <span class="required">*</span>
                            </label>
                            <select name="cita_id" class="form-select" required>
                                <option value="">-- Seleccionar Cita --</option>
                                <?php foreach ($citas as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= $factura['cita_id'] == $c['id'] ? 'selected' : '' ?>>
                                        #<?= str_pad($c['id'], 4, '0', STR_PAD_LEFT) ?> - 
                                        <?= htmlspecialchars($c['paciente_nombre'] ?? 'Sin paciente') ?> 
                                        (<?= date('d/m/Y', strtotime($c['fecha'])) ?>)
                                        <?php if (!empty($c['doctor_nombre'])): ?>
                                            - Dr. <?= htmlspecialchars($c['doctor_nombre']) ?>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i>
                                Cita médica relacionada con esta factura
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: INFORMACIÓN FINANCIERA -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="bi bi-cash-stack"></i>
                    Información Financiera
                </h3>

                <div class="row">
                    <!-- MONTO -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-currency-dollar"></i>
                                Monto Total (S/)
                                <span class="required">*</span>
                            </label>
                            <input type="number" 
                                   name="monto" 
                                   class="form-control" 
                                   step="0.01" 
                                   min="0.01" 
                                   value="<?= htmlspecialchars($factura['monto']) ?>"
                                   required
                                   placeholder="150.00">
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i>
                                Monto en soles (debe ser mayor a 0)
                            </div>
                        </div>
                    </div>

                    <!-- ESTADO -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-flag"></i>
                                Estado de la Factura
                                <span class="required">*</span>
                            </label>
                            <select name="estado" class="form-select" required>
                                <option value="Pendiente" <?= strtolower($factura['estado']) === 'pendiente' ? 'selected' : '' ?>>
                                    ⏳ Pendiente
                                </option>
                                <option value="Pagada" <?= strtolower($factura['estado']) === 'pagada' ? 'selected' : '' ?>>
                                    ✅ Pagada
                                </option>
                                <option value="Cancelada" <?= strtolower($factura['estado']) === 'cancelada' ? 'selected' : '' ?>>
                                    ❌ Cancelada
                                </option>
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i>
                                Estado actual del pago
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOTONES -->
            <div class="btn-actions">
                <a href="<?= BASE_URL ?>/index.php?accion=verFactura&id=<?= $factura['id'] ?>" 
                   class="btn-custom btn-secondary-custom">
                    <i class="bi bi-x-circle"></i>
                    Cancelar
                </a>
                <button type="submit" class="btn-custom btn-primary-custom">
                    <i class="bi bi-save"></i>
                    Guardar Cambios
                </button>
            </div>

        </form>
    </div>

</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Validación del formulario
document.getElementById('formEditarFactura').addEventListener('submit', function(e) {
    const monto = parseFloat(document.querySelector('input[name="monto"]').value);
    
    if (monto <= 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Error de Validación',
            text: 'El monto debe ser mayor a 0',
            confirmButtonColor: '#667eea'
        });
        return false;
    }

    // Confirmación antes de guardar
    e.preventDefault();
    
    Swal.fire({
        title: '¿Guardar cambios?',
        text: 'Se actualizará la información de la factura',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#667eea',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
    });
});

// Preview del monto con formato
document.querySelector('input[name="monto"]').addEventListener('input', function(e) {
    const monto = parseFloat(e.target.value) || 0;
    if (monto > 0) {
        console.log('Monto ingresado: S/ ' + monto.toFixed(2));
    }
});
</script>