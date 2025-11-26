<?php
// =====================================================
// 💊 Vista: Crear Receta - Dashboard Doctor
// Archivo: Aplicacion/vistas/doctor/crearReceta.php
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

// Obtener paciente_id desde la URL (si viene de una cita o historia)
$pacienteIdParam = $_GET['paciente_id'] ?? '';

// Obtener lista de pacientes
$stmtPacientes = $pdo->prepare("
    SELECT p.id, u.nombre, p.dni
    FROM pacientes p
    INNER JOIN usuarios u ON p.usuario_id = u.id
    ORDER BY u.nombre
");
$stmtPacientes->execute();
$pacientes = $stmtPacientes->fetchAll(PDO::FETCH_ASSOC);

// Obtener medicamentos disponibles
$stmtMedicamentos = $pdo->prepare("
    SELECT id, nombre, descripcion
    FROM medicamentos
    ORDER BY nombre
");
$stmtMedicamentos->execute();
$medicamentos = $stmtMedicamentos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Receta Médica</title>
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
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
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
            color: var(--warning);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid var(--warning);
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
            border-color: var(--warning);
            box-shadow: 0 0 0 0.2rem rgba(245, 158, 11, 0.15);
        }

        .medicamento-card {
            background: #fffbeb;
            border: 2px solid #fbbf24;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            position: relative;
        }

        .medicamento-number {
            position: absolute;
            top: -12px;
            left: 20px;
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
        }

        .btn-remove {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-remove:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        .btn-add-medicamento {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add-medicamento:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
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
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
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

        .alert-info-custom {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border: none;
            border-left: 4px solid var(--info);
            color: #1e3a8a;
            border-radius: 0.75rem;
        }

        .required {
            color: var(--danger);
            font-weight: 700;
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
        <h1><i class="bi bi-prescription2"></i> Nueva Receta Médica</h1>
        <p class="mb-0">Prescribe medicamentos y tratamientos para tus pacientes</p>
    </div>

    <!-- FORMULARIO -->
   <form action="<?= BASE_URL ?>/index.php?accion=doctorGuardarReceta" method="POST">
        
        <!-- INFORMACIÓN DEL PACIENTE -->
        <div class="form-container">
            <h2 class="section-title">
                <i class="bi bi-person-badge"></i>
                Información del Paciente
            </h2>

            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">
                        <i class="bi bi-person"></i> Paciente <span class="required">*</span>
                    </label>
                    <select name="paciente_id" class="form-select" required>
                        <option value="">Seleccione un paciente...</option>
                        <?php foreach ($pacientes as $paciente): ?>
                            <option value="<?= $paciente['id'] ?>" 
                                    <?= $pacienteIdParam == $paciente['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($paciente['nombre']) ?> - DNI: <?= htmlspecialchars($paciente['dni']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">
                        <i class="bi bi-chat-left-text"></i> Observaciones Generales
                    </label>
                    <textarea name="observaciones" class="form-control" rows="3" 
                              placeholder="Indicaciones especiales, recomendaciones generales..."></textarea>
                </div>
            </div>
        </div>

        <!-- MEDICAMENTOS -->
        <div class="form-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="section-title mb-0">
                    <i class="bi bi-capsule"></i>
                    Medicamentos Prescritos
                </h2>
                <button type="button" class="btn btn-add-medicamento" onclick="agregarMedicamento()">
                    <i class="bi bi-plus-circle"></i> Agregar Medicamento
                </button>
            </div>

            <div class="alert alert-info-custom">
                <i class="bi bi-info-circle"></i> 
                <strong>Importante:</strong> Debe agregar al menos un medicamento a la receta.
            </div>

            <div id="medicamentos-container">
                <!-- Los medicamentos se agregarán aquí dinámicamente -->
            </div>
        </div>

        <!-- BOTONES DE ACCIÓN -->
        <div class="form-container">
            <div class="d-flex gap-3 justify-content-end">
                <a href="<?= BASE_URL ?>/index.php?accion=doctorRecetas" class="btn btn-cancel">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-submit">
                    <i class="bi bi-check-circle"></i> Crear Receta
                </button>
            </div>
        </div>

    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let medicamentoCounter = 0;

// Lista de medicamentos disponibles desde PHP
const medicamentosDisponibles = <?= json_encode($medicamentos) ?>;

function agregarMedicamento() {
    medicamentoCounter++;
    
    const container = document.getElementById('medicamentos-container');
    const medicamentoDiv = document.createElement('div');
    medicamentoDiv.className = 'medicamento-card';
    medicamentoDiv.id = `medicamento-${medicamentoCounter}`;
    
    let optionsMedicamentos = '<option value="">Seleccione un medicamento...</option>';
    medicamentosDisponibles.forEach(med => {
        optionsMedicamentos += `<option value="${med.id}">${med.nombre} ${med.descripcion ? '- ' + med.descripcion : ''}</option>`;
    });
    
    medicamentoDiv.innerHTML = `
        <div class="medicamento-number">${medicamentoCounter}</div>
        <button type="button" class="btn-remove" onclick="eliminarMedicamento(${medicamentoCounter})">
            <i class="bi bi-x"></i>
        </button>
        
        <div class="row g-3 mt-2">
            <div class="col-md-12">
                <label class="form-label">
                    <i class="bi bi-capsule"></i> Medicamento <span class="required">*</span>
                </label>
                <select name="medicamentos[${medicamentoCounter}][medicamento_id]" class="form-select" required>
                    ${optionsMedicamentos}
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">
                    <i class="bi bi-droplet"></i> Dosis <span class="required">*</span>
                </label>
                <input type="text" name="medicamentos[${medicamentoCounter}][dosis]" 
                       class="form-control" placeholder="Ej: 500mg" required>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">
                    <i class="bi bi-clock-history"></i> Frecuencia <span class="required">*</span>
                </label>
                <input type="text" name="medicamentos[${medicamentoCounter}][frecuencia]" 
                       class="form-control" placeholder="Ej: Cada 8 horas" required>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">
                    <i class="bi bi-calendar-range"></i> Duración <span class="required">*</span>
                </label>
                <input type="text" name="medicamentos[${medicamentoCounter}][duracion]" 
                       class="form-control" placeholder="Ej: 7 días" required>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">
                    <i class="bi bi-123"></i> Cantidad
                </label>
                <input type="number" name="medicamentos[${medicamentoCounter}][cantidad]" 
                       class="form-control" placeholder="Unidades" value="1" min="1">
            </div>
        </div>
    `;
    
    container.appendChild(medicamentoDiv);
}

function eliminarMedicamento(id) {
    const element = document.getElementById(`medicamento-${id}`);
    if (element) {
        element.remove();
    }
    
    // Renumerar los medicamentos restantes
    const medicamentos = document.querySelectorAll('.medicamento-card');
    medicamentos.forEach((med, index) => {
        const number = med.querySelector('.medicamento-number');
        if (number) {
            number.textContent = index + 1;
        }
    });
}

// Validar formulario antes de enviar
document.getElementById('formReceta').addEventListener('submit', function(e) {
    const medicamentos = document.querySelectorAll('.medicamento-card');
    
    if (medicamentos.length === 0) {
        e.preventDefault();
        alert('⚠️ Debe agregar al menos un medicamento a la receta.');
        return false;
    }
});

// Agregar un medicamento por defecto al cargar la página
window.addEventListener('load', function() {
    agregarMedicamento();
});
</script>

</body>
</html>