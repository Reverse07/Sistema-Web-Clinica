<?php
// =====================================================
// 🩺 Vista: Perfil del Doctor
// Archivo: Aplicacion/vistas/doctor/perfil.php
// =====================================================

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'doctor') {
    header("Location: " . BASE_URL . "/index.php?accion=loginVista");
    exit;
}

$usuarioId = $_SESSION['usuario_id'];
$pdo = BaseDatos::pdo();

try {
    // Obtener información completa del doctor
    $stmtDoctor = $pdo->prepare("
        SELECT 
            d.id as doctor_id,
            d.numero_colegiatura,
            u.nombre,
            u.email,
            u.telefono,
            e.nombre as especialidad,
            e.id as especialidad_id
        FROM doctores d
        INNER JOIN usuarios u ON d.usuario_id = u.id
        LEFT JOIN especialidades e ON d.especialidad_id = e.id
        WHERE d.usuario_id = :usuario_id
    ");
    $stmtDoctor->execute([':usuario_id' => $usuarioId]);
    $doctor = $stmtDoctor->fetch(PDO::FETCH_ASSOC);
    
    if (!$doctor) {
        throw new Exception("Doctor no encontrado");
    }
    
    // Obtener todas las especialidades para el selector
    $stmtEspecialidades = $pdo->query("SELECT id, nombre FROM especialidades ORDER BY nombre");
    $especialidades = $stmtEspecialidades->fetchAll(PDO::FETCH_ASSOC);
    
    // Estadísticas del doctor
    $doctorId = $doctor['doctor_id'];
    
    // Total de citas atendidas
    $stmtCitas = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM citas 
        WHERE doctor_id = :doctor_id
    ");
    $stmtCitas->execute([':doctor_id' => $doctorId]);
    $totalCitas = $stmtCitas->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Pacientes únicos atendidos
    $stmtPacientes = $pdo->prepare("
        SELECT COUNT(DISTINCT paciente_id) as total 
        FROM citas 
        WHERE doctor_id = :doctor_id
    ");
    $stmtPacientes->execute([':doctor_id' => $doctorId]);
    $totalPacientes = $stmtPacientes->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Historias clínicas creadas
    $stmtHistorias = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM historia_clinica 
        WHERE doctor_id = :doctor_id
    ");
    $stmtHistorias->execute([':doctor_id' => $doctorId]);
    $totalHistorias = $stmtHistorias->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Recetas emitidas
    $stmtRecetas = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM recetas 
        WHERE doctor_id = :doctor_id
    ");
    $stmtRecetas->execute([':doctor_id' => $doctorId]);
    $totalRecetas = $stmtRecetas->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Obtener fecha de la primera cita como "miembro desde"
    $stmtFecha = $pdo->prepare("
        SELECT MIN(fecha) as primera_cita
        FROM citas 
        WHERE doctor_id = :doctor_id
    ");
    $stmtFecha->execute([':doctor_id' => $doctorId]);
    $resultFecha = $stmtFecha->fetch(PDO::FETCH_ASSOC);
    
    // Si no tiene citas, usar fecha actual
    $fechaRegistro = $resultFecha['primera_cita'] 
        ? date('d/m/Y', strtotime($resultFecha['primera_cita']))
        : date('d/m/Y');
    
} catch (Exception $e) {
    error_log("Error al cargar perfil del doctor: " . $e->getMessage());
    echo "<div class='alert alert-danger'>Error al cargar el perfil</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Doctor</title>
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
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .profile-container {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .profile-card {
            background: white;
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: var(--shadow-md);
            text-align: center;
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            font-weight: 800;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 24px rgba(14, 165, 233, 0.3);
        }

        .profile-name {
            font-size: 1.75rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .profile-role {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .profile-info {
            text-align: left;
            padding: 1.5rem;
            background: var(--light-bg);
            border-radius: 1rem;
            margin-bottom: 1rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 0.75rem;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.25rem;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: #0f172a;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .stat-box {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            text-align: center;
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--stat-color);
        }

        .stat-box.citas { --stat-color: var(--primary); }
        .stat-box.pacientes { --stat-color: var(--success); }
        .stat-box.historias { --stat-color: var(--warning); }
        .stat-box.recetas { --stat-color: var(--info); }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--stat-color);
            font-family: 'Poppins', sans-serif;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }

        .form-card {
            background: white;
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: var(--shadow-md);
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
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

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
        }

        .alert {
            border-radius: 1rem;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .password-section {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            padding: 1.5rem;
            border-radius: 1rem;
            margin-top: 2rem;
        }

        @media (max-width: 992px) {
            .profile-container {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.75rem;
            }

            .profile-avatar {
                width: 120px;
                height: 120px;
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="page-header">
        <h1>
            <i class="bi bi-person-circle"></i>
            Mi Perfil Profesional
        </h1>
        <p class="mb-0">Gestiona tu información personal y credenciales</p>
    </div>

    <!-- MENSAJES -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['mensaje']['tipo'] === 'exito' ? 'success' : 'danger' ?>">
            <i class="bi bi-<?= $_SESSION['mensaje']['tipo'] === 'exito' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
            <?= htmlspecialchars($_SESSION['mensaje']['texto']) ?>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <div class="profile-container">
        
        <!-- COLUMNA IZQUIERDA: INFORMACIÓN GENERAL -->
        <div>
            <div class="profile-card">
                <div class="profile-avatar">
                    <?= strtoupper(substr($doctor['nombre'], 0, 2)) ?>
                </div>
                <div class="profile-name"><?= htmlspecialchars($doctor['nombre']) ?></div>
                <div class="profile-role">
                    <i class="bi bi-stethoscope"></i> Doctor(a)
                </div>

                <div class="profile-info">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?= htmlspecialchars($doctor['email']) ?></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Teléfono</div>
                            <div class="info-value"><?= htmlspecialchars($doctor['telefono'] ?: 'No registrado') ?></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-heart-pulse"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Especialidad</div>
                            <div class="info-value"><?= htmlspecialchars($doctor['especialidad'] ?: 'Sin especialidad') ?></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-award"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Colegiatura</div>
                            <div class="info-value"><?= htmlspecialchars($doctor['numero_colegiatura'] ?: 'No registrado') ?></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Miembro desde</div>
                            <div class="info-value"><?= $fechaRegistro ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ESTADÍSTICAS -->
            <div class="stats-grid">
                <div class="stat-box citas">
                    <div class="stat-value"><?= $totalCitas ?></div>
                    <div class="stat-label">Citas Totales</div>
                </div>
                <div class="stat-box pacientes">
                    <div class="stat-value"><?= $totalPacientes ?></div>
                    <div class="stat-label">Pacientes</div>
                </div>
                <div class="stat-box historias">
                    <div class="stat-value"><?= $totalHistorias ?></div>
                    <div class="stat-label">Historias</div>
                </div>
                <div class="stat-box recetas">
                    <div class="stat-value"><?= $totalRecetas ?></div>
                    <div class="stat-label">Recetas</div>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: FORMULARIOS DE EDICIÓN -->
        <div>
            
            <!-- ACTUALIZAR INFORMACIÓN PERSONAL -->
            <div class="form-card mb-4">
                <div class="section-title">
                    <i class="bi bi-person-gear"></i>
                    Actualizar Información Personal
                </div>

                <form method="POST" action="<?= BASE_URL ?>/index.php?accion=actualizarMiPerfil" id="formPerfil">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">
                                <i class="bi bi-person"></i> Nombre Completo
                            </label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?= htmlspecialchars($doctor['nombre']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope"></i> Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($doctor['email']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="telefono" class="form-label">
                                <i class="bi bi-telephone"></i> Teléfono
                            </label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                   value="<?= htmlspecialchars($doctor['telefono']) ?>" 
                                   placeholder="+51 999 999 999">
                        </div>

                        <div class="col-md-6">
                            <label for="especialidad_id" class="form-label">
                                <i class="bi bi-heart-pulse"></i> Especialidad
                            </label>
                            <select class="form-select" id="especialidad_id" name="especialidad_id">
                                <option value="">Sin especialidad</option>
                                <?php foreach ($especialidades as $esp): ?>
                                    <option value="<?= $esp['id'] ?>" 
                                            <?= $esp['id'] == $doctor['especialidad_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($esp['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label for="numero_colegiatura" class="form-label">
                                <i class="bi bi-award"></i> Número de Colegiatura
                            </label>
                            <input type="text" class="form-control" id="numero_colegiatura" 
                                   name="numero_colegiatura" 
                                   value="<?= htmlspecialchars($doctor['numero_colegiatura']) ?>"
                                   placeholder="Ej: CMP 123456">
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- CAMBIAR CONTRASEÑA -->
            <div class="form-card">
                <div class="section-title">
                    <i class="bi bi-shield-lock"></i>
                    Cambiar Contraseña
                </div>

                <form method="POST" action="<?= BASE_URL ?>/index.php?accion=cambiarPassword" id="formPassword">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="password_actual" class="form-label">
                                <i class="bi bi-key"></i> Contraseña Actual
                            </label>
                            <input type="password" class="form-control" id="password_actual" 
                                   name="password_actual" required>
                        </div>

                        <div class="col-md-6">
                            <label for="password_nueva" class="form-label">
                                <i class="bi bi-lock"></i> Nueva Contraseña
                            </label>
                            <input type="password" class="form-control" id="password_nueva" 
                                   name="password_nueva" required minlength="6">
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmar" class="form-label">
                                <i class="bi bi-lock-fill"></i> Confirmar Contraseña
                            </label>
                            <input type="password" class="form-control" id="password_confirmar" 
                                   name="password_confirmar" required minlength="6">
                        </div>

                        <div class="col-12">
                            <div class="password-section">
                                <h6><i class="bi bi-info-circle"></i> Requisitos de seguridad:</h6>
                                <ul class="mb-0">
                                    <li>Mínimo 6 caracteres</li>
                                    <li>Se recomienda usar letras, números y símbolos</li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-shield-check"></i> Cambiar Contraseña
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Validación del formulario de contraseña
    document.getElementById('formPassword').addEventListener('submit', function(e) {
        const nueva = document.getElementById('password_nueva').value;
        const confirmar = document.getElementById('password_confirmar').value;
        
        if (nueva !== confirmar) {
            e.preventDefault();
            alert('⚠️ Las contraseñas no coinciden');
            return false;
        }
        
        if (nueva.length < 6) {
            e.preventDefault();
            alert('⚠️ La contraseña debe tener al menos 6 caracteres');
            return false;
        }
    });

    // Confirmación antes de cambiar datos
    document.getElementById('formPerfil').addEventListener('submit', function(e) {
        if (!confirm('¿Confirmar actualización de datos personales?')) {
            e.preventDefault();
        }
    });
</script>

</body>
</html>