<?php
// =====================
// 💳 Vista: Crear Nueva Factura
// =====================
$pacientes = $pacientes ?? [];
$citas = $citas ?? [];
?>

<style>
    .crear-factura-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }

    .header-factura {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2.5rem;
        border-radius: 20px 20px 0 0;
        margin-bottom: 0;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .header-factura h2 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .header-factura p {
        margin: 0.5rem 0 0 0;
        opacity: 0.95;
        font-size: 1rem;
    }

    .form-factura-card {
        background: white;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        padding: 3rem;
        border: none;
        margin-top: 0;
    }

    .form-group-modern {
        margin-bottom: 2rem;
    }

    .form-label-modern {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-control-modern,
    .form-select-modern {
        width: 100%;
        padding: 0.875rem 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #f8fafc;
    }

    .form-control-modern:focus,
    .form-select-modern:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        background-color: white;
        outline: none;
    }

    .form-control-modern:hover,
    .form-select-modern:hover {
        border-color: #cbd5e0;
        background-color: white;
    }

    .input-group {
        position: relative;
    }

    .input-group-prepend {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-weight: 600;
        font-size: 1rem;
        pointer-events: none;
    }

    .input-group .form-control-modern {
        padding-left: 3rem;
    }

    .alert-modern {
        border-radius: 12px;
        padding: 1rem 1.5rem;
        border: none;
        margin-bottom: 2rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-success-modern {
        background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%);
        color: #1e4620;
    }

    .alert-danger-modern {
        background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%);
        color: #5a1a1a;
    }

    .btn-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 2px solid #f1f5f9;
    }

    .btn-modern {
        padding: 0.875rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        color: white;
    }

    .btn-secondary-modern {
        background: #e2e8f0;
        color: #475569;
    }

    .btn-secondary-modern:hover {
        background: #cbd5e0;
        transform: translateY(-2px);
        color: #1e293b;
    }

    .icon-input {
        font-size: 1.2rem;
    }

    .required-asterisk {
        color: #ef4444;
        margin-left: 0.25rem;
    }

    .help-text {
        font-size: 0.85rem;
        color: #64748b;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-box {
        background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
        border-left: 4px solid #3b82f6;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
    }

    .info-box h4 {
        color: #1e40af;
        margin: 0 0 0.5rem 0;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-box p {
        color: #1e3a8a;
        margin: 0;
        font-size: 0.9rem;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .form-factura-card {
            padding: 1.5rem;
        }

        .header-factura {
            padding: 1.5rem;
        }

        .header-factura h2 {
            font-size: 1.5rem;
        }

        .btn-actions {
            flex-direction: column-reverse;
        }

        .btn-modern {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="crear-factura-container">
    <!-- Header -->
    <div class="header-factura">
        <h2>
            <span>💳</span>
            Registrar Nueva Factura
        </h2>
        <p>Complete el formulario para emitir una nueva factura</p>
    </div>

    <!-- Mensajes de alerta -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert-modern <?= $_SESSION['mensaje']['tipo'] === 'error' ? 'alert-danger-modern' : 'alert-success-modern' ?>">
            <span><?= $_SESSION['mensaje']['tipo'] === 'error' ? '⚠️' : '✅' ?></span>
            <span><?= htmlspecialchars($_SESSION['mensaje']['texto']) ?></span>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <!-- Información importante -->
    <div class="info-box">
        <h4>
            <span>ℹ️</span>
            Información Importante
        </h4>
        <p>
            Las facturas se emiten automáticamente con la fecha actual. 
            Asegúrese de seleccionar la cita correcta y verificar el monto antes de guardar.
            El estado inicial será "Pendiente" hasta que se confirme el pago.
        </p>
    </div>

    <!-- Formulario -->
    <form action="?accion=guardarFactura" method="POST" class="form-factura-card" id="formFactura">
        <div class="row">
            <!-- Paciente -->
            <div class="col-md-6">
                <div class="form-group-modern">
                    <label for="paciente_id" class="form-label-modern">
                        <span class="icon-input">👤</span>
                        Paciente
                        <span class="required-asterisk">*</span>
                    </label>
                    <select class="form-select form-select-modern" id="paciente_id" name="paciente_id" required>
                        <option value="">Seleccione un paciente</option>
                        <?php foreach ($pacientes as $p): ?>
                            <option value="<?= $p['id'] ?>">
                                <?= htmlspecialchars($p['nombre']) ?>
                                <?php if (!empty($p['email'])): ?>
                                    - <?= htmlspecialchars($p['email']) ?>
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="help-text">
                        💡 Seleccione el paciente al que se le emitirá la factura
                    </div>
                </div>
            </div>

            <!-- Cita -->
            <div class="col-md-6">
                <div class="form-group-modern">
                    <label for="cita_id" class="form-label-modern">
                        <span class="icon-input">🩺</span>
                        Cita Médica
                        <span class="required-asterisk">*</span>
                    </label>
                    <select class="form-select form-select-modern" id="cita_id" name="cita_id" required>
                        <option value="">Seleccione una cita</option>
                        <?php foreach ($citas as $c): ?>
                            <option value="<?= $c['id'] ?>" data-paciente="<?= $c['paciente_id'] ?? '' ?>">
                                #<?= $c['id'] ?> - <?= htmlspecialchars($c['paciente_nombre'] ?? 'N/A') ?> 
                                (<?= date('d/m/Y', strtotime($c['fecha'])) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="help-text">
                        💡 Cita relacionada con esta factura
                    </div>
                </div>
            </div>

            <!-- Monto -->
            <div class="col-md-6">
                <div class="form-group-modern">
                    <label for="monto" class="form-label-modern">
                        <span class="icon-input">💰</span>
                        Monto Total
                        <span class="required-asterisk">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-prepend">S/</span>
                        <input 
                            type="number" 
                            class="form-control form-control-modern" 
                            id="monto" 
                            name="monto" 
                            step="0.01"
                            min="0.01"
                            placeholder="0.00"
                            required
                        >
                    </div>
                    <div class="help-text">
                        💡 Ingrese el monto total en soles (S/)
                    </div>
                </div>
            </div>

            <!-- Estado -->
            <div class="col-md-6">
                <div class="form-group-modern">
                    <label for="estado" class="form-label-modern">
                        <span class="icon-input">📌</span>
                        Estado del Pago
                        <span class="required-asterisk">*</span>
                    </label>
                    <select class="form-select form-select-modern" id="estado" name="estado" required>
                        <option value="Pendiente" selected>⏳ Pendiente</option>
                        <option value="Pagada">✅ Pagada</option>
                        <option value="Cancelada">❌ Cancelada</option>
                    </select>
                    <div class="help-text">
                        💡 Estado inicial del pago de la factura
                    </div>
                </div>
            </div>

            <!-- Método de Pago (Opcional) -->
            <div class="col-md-12">
                <div class="form-group-modern">
                    <label for="metodo_pago" class="form-label-modern">
                        <span class="icon-input">💳</span>
                        Método de Pago
                        <span style="color: #94a3b8; font-weight: 400; font-size: 0.85rem;">(Opcional)</span>
                    </label>
                    <select class="form-select form-select-modern" id="metodo_pago" name="metodo_pago">
                        <option value="">Seleccionar método de pago</option>
                        <option value="Efectivo">💵 Efectivo</option>
                        <option value="Tarjeta de Débito">💳 Tarjeta de Débito</option>
                        <option value="Tarjeta de Crédito">💳 Tarjeta de Crédito</option>
                        <option value="Transferencia Bancaria">🏦 Transferencia Bancaria</option>
                        <option value="Yape">📱 Yape</option>
                        <option value="Plin">📱 Plin</option>
                    </select>
                    <div class="help-text">
                        💡 Método utilizado para realizar el pago
                    </div>
                </div>
            </div>

            <!-- Notas adicionales (Opcional) -->
            <div class="col-md-12">
                <div class="form-group-modern">
                    <label for="notas" class="form-label-modern">
                        <span class="icon-input">📝</span>
                        Notas Adicionales
                        <span style="color: #94a3b8; font-weight: 400; font-size: 0.85rem;">(Opcional)</span>
                    </label>
                    <textarea 
                        class="form-control form-control-modern" 
                        id="notas" 
                        name="notas" 
                        rows="4"
                        placeholder="Ingrese observaciones o notas adicionales sobre esta factura..."
                        style="resize: vertical;"
                    ></textarea>
                    <div class="help-text">
                        💡 Información adicional que desee agregar a la factura
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="btn-actions">
            <a href="?accion=gestionarFacturas" class="btn-modern btn-secondary-modern">
                <span>↩️</span>
                Cancelar
            </a>
            <button type="submit" class="btn-modern btn-primary-modern">
                <span>💾</span>
                Guardar Factura
            </button>
        </div>
    </form>
</div>

<script>
// Filtrar citas según el paciente seleccionado
document.getElementById('paciente_id')?.addEventListener('change', function() {
    const pacienteId = this.value;
    const citaSelect = document.getElementById('cita_id');
    const opciones = citaSelect.querySelectorAll('option');
    
    opciones.forEach(opcion => {
        if (opcion.value === '') {
            opcion.style.display = 'block';
            return;
        }
        
        const citaPacienteId = opcion.getAttribute('data-paciente');
        if (!pacienteId || citaPacienteId === pacienteId) {
            opcion.style.display = 'block';
        } else {
            opcion.style.display = 'none';
        }
    });
    
    // Resetear selección de cita si no corresponde al paciente
    citaSelect.value = '';
});

// Auto-completar paciente cuando se selecciona una cita
document.getElementById('cita_id')?.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const citaPacienteId = selectedOption.getAttribute('data-paciente');
    
    if (citaPacienteId) {
        const pacienteSelect = document.getElementById('paciente_id');
        pacienteSelect.value = citaPacienteId;
    }
});

// Validación del formulario antes de enviar
document.getElementById('formFactura')?.addEventListener('submit', function(e) {
    const monto = parseFloat(document.getElementById('monto').value);
    
    if (monto <= 0) {
        e.preventDefault();
        alert('⚠️ El monto debe ser mayor a 0');
        return false;
    }
    
    if (monto > 10000) {
        if (!confirm('⚠️ El monto es mayor a S/ 10,000. ¿Está seguro de continuar?')) {
            e.preventDefault();
            return false;
        }
    }
    
    // Confirmación final
    return confirm('✅ ¿Confirma que desea crear esta factura?');
});

// Formatear el input de monto mientras se escribe
document.getElementById('monto')?.addEventListener('blur', function() {
    const valor = parseFloat(this.value);
    if (!isNaN(valor)) {
        this.value = valor.toFixed(2);
    }
});
</script>