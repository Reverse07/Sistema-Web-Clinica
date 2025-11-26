<?php
// =====================================================
// 🩺 Vista: Editar Historia Clínica - Doctor
// Archivo: Aplicacion/vistas/doctor/editarHistoria.php
// =====================================================

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'doctor') {
    header("Location: " . BASE_URL . "/index.php?accion=loginVista");
    exit;
}

$usuarioId = $_SESSION['usuario_id'];
$historiaId = $_GET['id'] ?? null;
$pdo = BaseDatos::pdo();

if (!$historiaId) {
    echo "<div class='alert alert-danger'>ID de historia no proporcionado</div>";
    exit;
}

try {
    // Obtener ID del doctor
    $stmtDoctor = $pdo->prepare("SELECT id FROM doctores WHERE usuario_id = :usuario_id");
    $stmtDoctor->execute([':usuario_id' => $usuarioId]);
    $doctor = $stmtDoctor->fetch(PDO::FETCH_ASSOC);
    
    if (!$doctor) {
        throw new Exception("Doctor no encontrado");
    }
    
    $doctorId = $doctor['id'];
    
    // Obtener datos de la historia clínica
    $stmtHistoria = $pdo->prepare("
        SELECT 
            hc.*,
            c.fecha as fecha_cita,
            TO_CHAR(c.fecha, 'DD/MM/YYYY HH24:MI') as fecha_cita_formateada,
            up.nombre as paciente_nombre,
            p.dni as paciente_dni,
            p.id as paciente_id
        FROM historia_clinica hc
        INNER JOIN citas c ON hc.cita_id = c.id
        INNER JOIN pacientes p ON hc.paciente_id = p.id
        INNER JOIN usuarios up ON p.usuario_id = up.id
        WHERE hc.id = :historia_id 
        AND hc.doctor_id = :doctor_id
    ");
    
    $stmtHistoria->execute([
        ':historia_id' => $historiaId,
        ':doctor_id' => $doctorId
    ]);
    
    $historia = $stmtHistoria->fetch(PDO::FETCH_ASSOC);
    
    if (!$historia) {
        throw new Exception("Historia clínica no encontrada o no tienes permisos para editarla");
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Historia Clínica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0ea5e9;
            --primary-dark: #0284c7;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --light-bg: #f8fafc;
            --shadow-sm: 0 4px 15px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 12px 30px rgba(0, 0, 0, 0.15);
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .page-header {
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
            color: white;
            padding: 2rem;
            border-radius: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
        }

        .page-header h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .form-container {
            background: white;
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: var(--shadow-md);
        }

        .paciente-card {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            padding: 1.5rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            border-left: 4px solid var(--primary);
        }

        .paciente-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .paciente-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.5rem;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
        }

        .paciente-detalles h4 {
            margin: 0;
            font-weight: 700;
            color: #0f172a;
        }

        .paciente-detalles p {
            margin: 0.25rem 0 0;
            color: #475569;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 0.75rem;
            border: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.1);
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .required-field::after {
            content: " *";
            color: var(--danger);
            font-weight: 700;
        }

        .char-counter {
            font-size: 0.875rem;
            color: #64748b;
            text-align: right;
            margin-top: 0.25rem;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            padding-top: 1.5rem;
            border-top: 2px solid #e2e8f0;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }

        .btn-secondary {
            background: #64748b;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #475569;
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 1rem;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-info {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.75rem;
            }

            .form-container {
                padding: 1.5rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .paciente-info {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <!-- HEADER -->
    <div class="page-header">
        <h1>
            <i class="bi bi-pencil-square"></i>
            Editar Historia Clínica
        </h1>
        <p class="mb-0">Actualiza la información de la historia clínica del paciente</p>
    </div>

    <!-- INFORMACIÓN DEL PACIENTE -->
    <div class="paciente-card">
        <div class="paciente-info">
            <div class="paciente-avatar">
                <?= strtoupper(substr($historia['paciente_nombre'], 0, 1)) ?>
            </div>
            <div class="paciente-detalles">
                <h4><?= htmlspecialchars($historia['paciente_nombre']) ?></h4>
                <p>
                    <i class="bi bi-card-text"></i> DNI: <?= htmlspecialchars($historia['paciente_dni']) ?>
                    | <i class="bi bi-calendar-check"></i> Cita: <?= htmlspecialchars($historia['fecha_cita_formateada']) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- MENSAJES -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['mensaje']['tipo'] === 'exito' ? 'success' : 'danger' ?>">
            <?= htmlspecialchars($_SESSION['mensaje']['texto']) ?>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <!-- FORMULARIO -->
    <div class="form-container">
        <form method="POST" action="<?= BASE_URL ?>/index.php?accion=actualizarHistoria" id="formEditarHistoria">
            <input type="hidden" name="historia_id" value="<?= $historiaId ?>">

            <!-- MOTIVO DE CONSULTA -->
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-clipboard-heart"></i>
                    Motivo de Consulta
                </div>
                <div class="mb-3">
                    <label for="motivo" class="form-label required-field">¿Por qué vino el paciente?</label>
                    <textarea 
                        class="form-control" 
                        id="motivo" 
                        name="motivo" 
                        required
                        maxlength="500"
                        placeholder="Ej: Dolor abdominal de 3 días de evolución..."><?= htmlspecialchars($historia['motivo']) ?></textarea>
                    <div class="char-counter">
                        <span id="motivo-counter">0</span> / 500 caracteres
                    </div>
                </div>
            </div>

            <!-- DIAGNÓSTICO -->
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-clipboard-check"></i>
                    Diagnóstico
                </div>
                <div class="mb-3">
                    <label for="diagnostico" class="form-label required-field">Diagnóstico clínico</label>
                    <textarea 
                        class="form-control" 
                        id="diagnostico" 
                        name="diagnostico" 
                        required
                        maxlength="1000"
                        placeholder="Ej: Gastroenteritis aguda..."><?= htmlspecialchars($historia['diagnostico']) ?></textarea>
                    <div class="char-counter">
                        <span id="diagnostico-counter">0</span> / 1000 caracteres
                    </div>
                </div>
            </div>

            <!-- INDICACIONES -->
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-file-medical"></i>
                    Indicaciones y Tratamiento
                </div>
                <div class="mb-3">
                    <label for="indicaciones" class="form-label required-field">Plan de tratamiento</label>
                    <textarea 
                        class="form-control" 
                        id="indicaciones" 
                        name="indicaciones" 
                        required
                        maxlength="2000"
                        placeholder="Ej: Reposo relativo, dieta blanda, omeprazol 20mg c/12h..."><?= htmlspecialchars($historia['indicaciones']) ?></textarea>
                    <div class="char-counter">
                        <span id="indicaciones-counter">0</span> / 2000 caracteres
                    </div>
                </div>
            </div>

            <!-- OBSERVACIONES -->
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-journal-text"></i>
                    Observaciones Adicionales
                </div>
                <div class="mb-3">
                    <label for="observaciones" class="form-label">Notas adicionales (opcional)</label>
                    <textarea 
                        class="form-control" 
                        id="observaciones" 
                        name="observaciones"
                        maxlength="1000"
                        placeholder="Ej: Control en 7 días, paciente alérgico a penicilina..."><?= htmlspecialchars($historia['observaciones'] ?? '') ?></textarea>
                    <div class="char-counter">
                        <span id="observaciones-counter">0</span> / 1000 caracteres
                    </div>
                </div>
            </div>

            <!-- INFORMACIÓN -->
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                Los campos marcados con <strong class="text-danger">*</strong> son obligatorios
            </div>

            <!-- BOTONES DE ACCIÓN -->
            <div class="form-actions">
                <a href="<?= BASE_URL ?>/index.php?accion=doctorHistorias" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Contadores de caracteres
    const contadores = {
        'motivo': 500,
        'diagnostico': 1000,
        'indicaciones': 2000,
        'observaciones': 1000
    };

    // Inicializar contadores
    Object.keys(contadores).forEach(campo => {
        const textarea = document.getElementById(campo);
        const counter = document.getElementById(`${campo}-counter`);
        
        function actualizarContador() {
            const length = textarea.value.length;
            counter.textContent = length;
            
            // Cambiar color según porcentaje
            if (length > contadores[campo] * 0.9) {
                counter.style.color = '#ef4444';
            } else if (length > contadores[campo] * 0.7) {
                counter.style.color = '#f59e0b';
            } else {
                counter.style.color = '#64748b';
            }
        }
        
        // Actualizar al cargar
        actualizarContador();
        
        // Actualizar al escribir
        textarea.addEventListener('input', actualizarContador);
    });

    // Validación del formulario
    document.getElementById('formEditarHistoria').addEventListener('submit', function(e) {
        const campos = ['motivo', 'diagnostico', 'indicaciones'];
        let valido = true;
        
        campos.forEach(campo => {
            const textarea = document.getElementById(campo);
            if (!textarea.value.trim()) {
                e.preventDefault();
                textarea.classList.add('is-invalid');
                valido = false;
            } else {
                textarea.classList.remove('is-invalid');
            }
        });
        
        if (!valido) {
            alert('⚠️ Por favor complete todos los campos obligatorios');
        }
    });

    // Confirmar antes de cancelar si hay cambios
    let formModificado = false;
    const inputs = document.querySelectorAll('textarea');
    inputs.forEach(input => {
        input.addEventListener('input', () => {
            formModificado = true;
        });
    });

    document.querySelector('.btn-secondary').addEventListener('click', function(e) {
        if (formModificado) {
            if (!confirm('¿Seguro que deseas cancelar? Los cambios no guardados se perderán.')) {
                e.preventDefault();
            }
        }
    });
</script>

</body>
</html>