<?php
// =====================================================
// 🖨️ Vista: Imprimir Receta - Versión para Impresión
// Archivo: Aplicacion/vistas/doctor/imprimirReceta.php
// =====================================================

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'doctor') {
    header("Location: " . BASE_URL . "/index.php?accion=loginVista");
    exit;
}

$usuarioId = $_SESSION['usuario_id'];
$recetaId = $_GET['id'] ?? null;

if (!$recetaId) {
    echo "<script>alert('ID de receta no proporcionado'); window.close();</script>";
    exit;
}

$pdo = BaseDatos::pdo();

// Obtener ID del doctor
$stmtDoctor = $pdo->prepare("SELECT id FROM doctores WHERE usuario_id = :usuario_id");
$stmtDoctor->execute([':usuario_id' => $usuarioId]);
$doctor = $stmtDoctor->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    echo "<script>alert('Doctor no encontrado'); window.close();</script>";
    exit;
}

$doctorId = $doctor['id'];

// Obtener información completa de la receta
$stmtReceta = $pdo->prepare("
    SELECT 
        r.id,
        r.fecha_emision,
        TO_CHAR(r.fecha_emision, 'DD/MM/YYYY') as fecha_formateada,
        TO_CHAR(r.fecha_emision, 'HH24:MI') as hora_formateada,
        r.observaciones,
        p.id as paciente_id,
        up.nombre as paciente_nombre,
        p.dni as paciente_dni,
        p.fecha_nacimiento,
        p.genero,
        p.direccion,
        up.telefono as paciente_telefono,
        up.email as paciente_email,
        d.id as doctor_id,
        ud.nombre as doctor_nombre,
        ud.telefono as doctor_telefono,
        ud.email as doctor_email,
        d.numero_colegiatura,
        e.nombre as especialidad
    FROM recetas r
    INNER JOIN pacientes p ON r.paciente_id = p.id
    INNER JOIN usuarios up ON p.usuario_id = up.id
    INNER JOIN doctores d ON r.doctor_id = d.id
    INNER JOIN usuarios ud ON d.usuario_id = ud.id
    LEFT JOIN especialidades e ON d.especialidad_id = e.id
    WHERE r.id = :receta_id AND r.doctor_id = :doctor_id
");

$stmtReceta->execute([
    ':receta_id' => $recetaId,
    ':doctor_id' => $doctorId
]);

$receta = $stmtReceta->fetch(PDO::FETCH_ASSOC);

if (!$receta) {
    echo "<script>alert('Receta no encontrada o no tiene permisos'); window.close();</script>";
    exit;
}

// Obtener medicamentos
$stmtMedicamentos = $pdo->prepare("
    SELECT 
        dr.id,
        dr.dosis,
        dr.frecuencia,
        dr.duracion,
        dr.cantidad,
        m.nombre as medicamento_nombre,
        m.descripcion as medicamento_descripcion
    FROM detalle_recetas dr
    INNER JOIN medicamentos m ON dr.medicamento_id = m.id
    WHERE dr.receta_id = :receta_id
    ORDER BY dr.id
");

$stmtMedicamentos->execute([':receta_id' => $recetaId]);
$medicamentos = $stmtMedicamentos->fetchAll(PDO::FETCH_ASSOC);

// Calcular edad
$edad = '';
if ($receta['fecha_nacimiento']) {
    $fechaNac = new DateTime($receta['fecha_nacimiento']);
    $hoy = new DateTime();
    $edad = $hoy->diff($fechaNac)->y . ' años';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receta Médica #<?= htmlspecialchars($recetaId) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        @media print {
            @page {
                size: A4;
                margin: 1cm;
            }
            
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-after: always;
            }
        }

        body {
            font-family: 'Arial', sans-serif;
            background: white;
            padding: 20px;
            font-size: 12pt;
            line-height: 1.5;
        }

        .receta-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border: 2px solid #333;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #f59e0b;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24pt;
            font-weight: bold;
            color: #d97706;
            margin: 0;
        }

        .header .subtitle {
            font-size: 14pt;
            color: #666;
            margin: 5px 0;
        }

        .section {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #d97706;
            margin-bottom: 10px;
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 5px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            width: 150px;
            color: #333;
        }

        .info-value {
            color: #555;
        }

        .medicamento {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .medicamento-number {
            display: inline-block;
            background: #d97706;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            font-weight: bold;
            margin-right: 10px;
        }

        .medicamento-nombre {
            font-size: 14pt;
            font-weight: bold;
            color: #78350f;
            margin-bottom: 5px;
        }

        .medicamento-detalles {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .detalle-item {
            background: white;
            padding: 8px;
            border-left: 2px solid #f59e0b;
        }

        .detalle-label {
            font-size: 9pt;
            text-transform: uppercase;
            color: #92400e;
            font-weight: bold;
        }

        .detalle-value {
            font-size: 11pt;
            color: #451a03;
        }

        .observaciones {
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
        }

        .firma-section {
            margin-top: 60px;
            text-align: center;
        }

        .firma-line {
            width: 300px;
            border-top: 2px solid #333;
            margin: 0 auto 10px;
        }

        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .btn-print:hover {
            background: #059669;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120pt;
            color: rgba(217, 119, 6, 0.05);
            font-weight: bold;
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body>

<!-- Botón de imprimir -->
<button class="btn-print no-print" onclick="window.print()">
    🖨️ Imprimir
</button>

<!-- Marca de agua -->
<div class="watermark">RECETA MÉDICA</div>

<div class="receta-container">

    <!-- HEADER -->
    <div class="header">
        <h1>📋 RECETA MÉDICA</h1>
        <div class="subtitle">Prescripción Médica Profesional</div>
        <div style="margin-top: 10px; color: #666;">
            <strong>Receta N°:</strong> <?= str_pad($recetaId, 6, '0', STR_PAD_LEFT) ?>
        </div>
        <div style="color: #666;">
            <strong>Fecha de emisión:</strong> <?= htmlspecialchars($receta['fecha_formateada']) ?> 
            | <?= htmlspecialchars($receta['hora_formateada']) ?>
        </div>
    </div>

    <!-- INFORMACIÓN DEL MÉDICO -->
    <div class="section">
        <div class="section-title">👨‍⚕️ Datos del Médico Prescriptor</div>
        <div class="info-row">
            <div class="info-label">Nombre:</div>
            <div class="info-value"><?= htmlspecialchars($receta['doctor_nombre']) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Especialidad:</div>
            <div class="info-value"><?= htmlspecialchars($receta['especialidad'] ?? 'Medicina General') ?></div>
        </div>
        <?php if ($receta['numero_colegiatura']): ?>
        <div class="info-row">
            <div class="info-label">N° Colegiatura:</div>
            <div class="info-value"><?= htmlspecialchars($receta['numero_colegiatura']) ?></div>
        </div>
        <?php endif; ?>
        <?php if ($receta['doctor_telefono']): ?>
        <div class="info-row">
            <div class="info-label">Teléfono:</div>
            <div class="info-value"><?= htmlspecialchars($receta['doctor_telefono']) ?></div>
        </div>
        <?php endif; ?>
    </div>

    <!-- INFORMACIÓN DEL PACIENTE -->
    <div class="section">
        <div class="section-title">👤 Datos del Paciente</div>
        <div class="info-row">
            <div class="info-label">Nombre:</div>
            <div class="info-value"><?= htmlspecialchars($receta['paciente_nombre']) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">DNI:</div>
            <div class="info-value"><?= htmlspecialchars($receta['paciente_dni']) ?></div>
        </div>
        <?php if ($edad): ?>
        <div class="info-row">
            <div class="info-label">Edad:</div>
            <div class="info-value"><?= htmlspecialchars($edad) ?></div>
        </div>
        <?php endif; ?>
        <?php if ($receta['genero']): ?>
        <div class="info-row">
            <div class="info-label">Género:</div>
            <div class="info-value"><?= htmlspecialchars(ucfirst($receta['genero'])) ?></div>
        </div>
        <?php endif; ?>
    </div>

    <!-- MEDICAMENTOS PRESCRITOS -->
    <div class="section">
        <div class="section-title">💊 Medicamentos Prescritos</div>
        
        <?php if (empty($medicamentos)): ?>
            <p style="text-align: center; color: #999;">No se encontraron medicamentos</p>
        <?php else: ?>
            <?php foreach ($medicamentos as $index => $med): ?>
                <div class="medicamento">
                    <div>
                        <span class="medicamento-number"><?= $index + 1 ?></span>
                        <span class="medicamento-nombre">
                            <?= htmlspecialchars($med['medicamento_nombre']) ?>
                        </span>
                    </div>
                    
                    <?php if ($med['medicamento_descripcion']): ?>
                        <div style="color: #92400e; font-style: italic; margin: 5px 0 10px 40px;">
                            <?= htmlspecialchars($med['medicamento_descripcion']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="medicamento-detalles">
                        <div class="detalle-item">
                            <div class="detalle-label">💧 Dosis</div>
                            <div class="detalle-value"><?= htmlspecialchars($med['dosis'] ?: 'No especificada') ?></div>
                        </div>
                        <div class="detalle-item">
                            <div class="detalle-label">🕒 Frecuencia</div>
                            <div class="detalle-value"><?= htmlspecialchars($med['frecuencia'] ?: 'No especificada') ?></div>
                        </div>
                        <div class="detalle-item">
                            <div class="detalle-label">📅 Duración</div>
                            <div class="detalle-value"><?= htmlspecialchars($med['duracion'] ?: 'No especificada') ?></div>
                        </div>
                        <div class="detalle-item">
                            <div class="detalle-label">🔢 Cantidad</div>
                            <div class="detalle-value"><?= htmlspecialchars($med['cantidad']) ?> unidad(es)</div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- OBSERVACIONES -->
    <?php if ($receta['observaciones']): ?>
        <div class="observaciones">
            <div style="font-weight: bold; margin-bottom: 10px; color: #1e3a8a;">
                📝 Observaciones e Indicaciones Generales:
            </div>
            <div style="color: #1e40af;">
                <?= nl2br(htmlspecialchars($receta['observaciones'])) ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- FIRMA -->
    <div class="firma-section">
        <div class="firma-line"></div>
        <div style="font-weight: bold;"><?= htmlspecialchars($receta['doctor_nombre']) ?></div>
        <div style="color: #666;">
            <?= htmlspecialchars($receta['especialidad'] ?? 'Medicina General') ?>
        </div>
        <?php if ($receta['numero_colegiatura']): ?>
        <div style="color: #666;">
            Reg. N° <?= htmlspecialchars($receta['numero_colegiatura']) ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <small style="color: #999;">
            Este documento es una prescripción médica oficial. 
            Conserve este documento para su control y seguimiento médico.
        </small>
    </div>

</div>

<script>
// Auto-imprimir al cargar (opcional)
// window.onload = function() { window.print(); }
</script>

</body>
</html>