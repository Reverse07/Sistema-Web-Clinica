<?php
require_once __DIR__ . "/../configuracion/app.php";
require_once __DIR__ . "/../includes/encabezado.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Paciente</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- 🎨 Estilos base y específicos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/recursos/css/app.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/recursos/css/panel.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="panel-paciente">
    <!-- 🧭 Menú lateral del paciente -->
    <aside class="sidebar">
        <?php include __DIR__ . "/../includes/menu-paciente.php"; ?>
    </aside>

    <!-- 📦 Contenido principal -->
    <main class="contenido">
        <?php if (isset($vistaInterna)) include $vistaInterna; ?>
    </main>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>


<!-- 📜 Scripts base -->
<script src="<?= BASE_URL ?>/recursos/js/app.js"></script>
</body>
</html>
