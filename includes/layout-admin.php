<?php
require_once __DIR__ . "/../configuracion/app.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrativo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- 🎨 Estilos base y específicos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/recursos/css/panel.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<!-- 🔝 Encabezado con saludo y botón de logout -->
<?php include __DIR__ . "/../includes/encabezado.php"; ?>

<div class="panel-admin">
    <!-- 🧭 Menú lateral -->
    <aside class="sidebar">
        <?php include __DIR__ . "/../includes/menu-admin.php"; ?>
    </aside>

    <!-- 📦 Contenido principal -->
    <main class="contenido">
        <?php if (isset($vistaInterna)) include $vistaInterna; ?>
    </main>
</div>

<!-- 🔻 Footer -->
<?php include __DIR__ . "/../includes/footer.php"; ?>

<!-- 📜 Scripts base -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= BASE_URL ?>/recursos/js/app.js"></script>
<script src="<?= BASE_URL ?>/recursos/js/app.js"></script>
</body>
</html>
