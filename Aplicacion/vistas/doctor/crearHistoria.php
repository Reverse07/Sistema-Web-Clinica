<?php
// =====================================================
// 📋 Vista: Crear Historia Clínica - Dashboard Doctor
// Archivo: Aplicacion/vistas/doctor/crearHistoria.php
// =====================================================

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'doctor') {
    header("Location: " . BASE_URL . "/index.php?accion=loginVista");
    exit;
}

$usuarioId = $_SESSION['usuario_id'];
$pdo = BaseDatos::pdo();

// Obtener ID del doctor
$stmtDoctor = $pdo->prepare("SELECT id FROM doctores WHERE usuario_id = :usuario_id");
$stmtDoctor->execute([':usuario_id' => $usuarioId]);
$doctor = $stmtDoctor->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    echo "<div class='alert alert-danger'>Error: Doctor no encontrado</div>";
    exit;
}

$doctorId = $doctor['id'];

// Obtener cita_id y paciente_id desde la URL (si vienen desde una cita)
$citaIdParam = $_GET['cita_id'] ?? '';
$pacienteIdParam = $_GET['paciente_id'] ?? '';

// Obtener lista de citas del doctor que AÚN NO tienen historia clínica
$stmtCitas = $pdo->prepare("
    SELECT 
        c.id,
        c.fecha,
        TO_CHAR(c.fecha, 'DD/MM/YYYY HH24:MI') as fecha_formateada,
        p.id as paciente_id,
        up.nombre as paciente_nombre,
        p.dni as paciente_dni
    FROM citas c
    INNER JOIN pacientes p ON c.paciente_id = p.id
    INNER JOIN usuarios up ON p.usuario_id = up.id
    LEFT JOIN historia_clinica hc ON c.id = hc.cita_id
    WHERE c.doctor_id = :doctor_id 
    AND hc.id IS NULL
    AND c.estado != 'cancelada'
    ORDER BY c.fecha DESC
");
$stmtCitas->execute([':doctor_id' => $doctorId]);
$citas = $stmtCitas->fetchAll(PDO::FETCH_ASSOC);

// Si viene cita_id, obtener información de la cita
$citaSeleccionada = null;
if ($citaIdParam) {
    $stmtCitaSel = $pdo->prepare("
        SELECT 
            c.id,
            c.fecha,
            p.id as paciente_id,
            up.nombre as paciente_nombre,
            p.dni as paciente_dni,
            p.fecha_nacimiento,
            p.genero
        FROM citas c
        INNER JOIN pacientes p ON c.paciente_id = p.id
        INNER JOIN usuarios up ON p.usuario_id = up.id
        WHERE c.id = :cita_id AND c.doctor_id = :doctor_id
    ");
    $stmtCitaSel->execute([':cita_id' => $citaIdParam, ':doctor_id' => $doctorId]);
    $citaSeleccionada = $stmtCitaSel->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Historia Clínica</title>
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
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
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
        }

        .form-container {
            background: white;
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }

        .section-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid var(--primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.15);
        }

        .patient-info-card {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border: 2px solid #3b82f6;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .patient-info-title {
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 1rem;
        }

        .info-row {
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 0.5rem;
            padding: 0.5rem 0;
        }

        .info-label {
            font-weight: 600;
            color: #1e40af;
        }

        .info-value {
            color: #1e3a8a;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.3);
        }

        .btn-cancel {
            background: #64748b;
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: #475569;
        }

        .required {
            color: var(--danger);
            font-weight: 700;
        }

        .alert-info-custom {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: none;
            border-left: 4px solid var(--warning);
            color: #78350f;
            border-radius: 0.75rem;
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="page-header">
        <h1><i class="bi bi-clipboard2-pulse"></i> Nueva Historia Clínica</h1>
        <p class="mb-0">Registra el diagnóstico y tratamiento del paciente</p>
    </div>

    <!-- FORMULARIO -->
    <form action="<?= BASE_URL ?>/index.php?accion=guardarHistoria" method="POST" id="formHistoria">
        
        <!-- SELECCIÓN DE CITA -->
        <div class="form-container">
            <h2 class="section-title">
                <i class="bi bi-calendar-check"></i>
                Información de la Cita
            </h2>

            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">
                        <i class="bi bi-calendar-event"></i> Seleccionar Cita <span class="required">*</span>
                    </label>
                    <select name="cita_id" id="citaSelect" class="form-select" required>
                        <option value="">Seleccione una cita...</option>
                        <?php foreach ($citas as $cita): ?>
                            <option value="<?= $cita['id'] ?>" 
                                    data-paciente-id="<?= $cita['paciente_id'] ?>"
                                    data-paciente-nombre="<?= htmlspecialchars($cita['paciente_nombre']) ?>"
                                    data-paciente-dni="<?= htmlspecialchars($cita['paciente_dni']) ?>"
                                    <?= $citaIdParam == $cita['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cita['fecha_formateada']) ?> - 
                                <?= htmlspecialchars($cita['paciente_nombre']) ?> 
                                (DNI: <?= htmlspecialchars($cita['paciente_dni']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- INFO DEL PACIENTE (se muestra al seleccionar cita) -->
            <div id="patientInfo" class="patient-info-card" style="display: <?= $citaSeleccionada ? 'block' : 'none' ?>;">
                <div class="patient-info-title">
                    <i class="bi bi-person-badge"></i> Información del Paciente
                </div>
                <div class="info-row">
                    <div class="info-label">Nombre:</div>
                    <div class="info-value" id="infoNombre">
                        <?= $citaSeleccionada ? htmlspecialchars($citaSeleccionada['paciente_nombre']) : '' ?>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">DNI:</div>
                    <div class="info-value" id="infoDni">
                        <?= $citaSeleccionada ? htmlspecialchars($citaSeleccionada['paciente_dni']) : '' ?>
                    </div>
                </div>
            </div>

            <input type="hidden" name="paciente_id" id="pacienteIdInput" value="<?= $citaSeleccionada['paciente_id'] ?? '' ?>">
        </div>

        <!-- DATOS CLÍNICOS -->
        <div class="form-container">
            <h2 class="section-title">
                <i class="bi bi-file-medical"></i>
                Datos Clínicos
            </h2>

            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">
                        <i class="bi bi-clipboard-heart"></i> Motivo de Consulta <span class="required">*</span>
                    </label>
                    <textarea name="motivo" class="form-control" rows="3" 
                              placeholder="Describa el motivo por el cual el paciente acude a consulta..." required></textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label">
                        <i class="bi bi-clipboard-check"></i> Diagnóstico <span class="required">*</span>
                    </label>
                    <textarea name="diagnostico" class="form-control" rows="4" 
                              placeholder="Diagnóstico médico del paciente..." required></textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label">
                        <i class="bi bi-prescription"></i> Indicaciones y Tratamiento <span class="required">*</span>
                    </label>
                    <textarea name="indicaciones" class="form-control" rows="4" 
                              placeholder="Tratamiento prescrito, indicaciones médicas..." required></textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label">
                        <i class="bi bi-chat-left-text"></i> Observaciones Adicionales
                    </label>
                    <textarea name="observaciones" class="form-control" rows="3" 
                              placeholder="Notas adicionales, recomendaciones..."></textarea>
                </div>
            </div>

            <div class="alert alert-info-custom mt-3">
                <i class="bi bi-info-circle"></i> 
                <strong>Importante:</strong> Todos los campos marcados con <span class="required">*</span> son obligatorios.
                Esta información quedará registrada permanentemente en el historial del paciente.
            </div>
        </div>

        <!-- BOTONES DE ACCIÓN -->
        <div class="form-container">
            <div class="d-flex gap-3 justify-content-end">
                <a href="<?= BASE_URL ?>/index.php?accion=doctorHistorias" class="btn btn-cancel">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-submit">
                    <i class="bi bi-check-circle"></i> Guardar Historia Clínica
                </button>
            </div>
        </div>

    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Mostrar información del paciente al seleccionar cita
document.getElementById('citaSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const patientInfo = document.getElementById('patientInfo');
    
    if (this.value) {
        const pacienteId = selectedOption.dataset.pacienteId;
        const pacienteNombre = selectedOption.dataset.pacienteNombre;
        const pacienteDni = selectedOption.dataset.pacienteDni;
        
        document.getElementById('infoNombre').textContent = pacienteNombre;
        document.getElementById('infoDni').textContent = pacienteDni;
        document.getElementById('pacienteIdInput').value = pacienteId;
        
        patientInfo.style.display = 'block';
    } else {
        patientInfo.style.display = 'none';
        document.getElementById('pacienteIdInput').value = '';
    }
});

// Validar formulario
document.getElementById('formHistoria').addEventListener('submit', function(e) {
    const citaId = document.getElementById('citaSelect').value;
    
    if (!citaId) {
        e.preventDefault();
        alert('⚠️ Debe seleccionar una cita para crear la historia clínica.');
        return false;
    }
});
</script>

</body>
</html>