<?php
require_once __DIR__ . "/../configuracion/app.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Médico</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ✅ DEFINIR BASE_URL GLOBALMENTE -->
    <script>
        window.BASE_URL = "<?php echo BASE_URL; ?>";
    </script>

    <!-- 🎨 Estilos base y específicos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/recursos/css/panel.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<!-- 🔝 Encabezado UNIFICADO con botón cerrar sesión -->
<?php include __DIR__ . "/../includes/encabezado.php"; ?>

<div class="panel-doctor">
    <!-- 🧭 Menú lateral del médico -->
    <aside class="sidebar">
        <?php include __DIR__ . "/../includes/menu-doctor.php"; ?>
    </aside>

    <!-- 📦 Contenido principal -->
    <main class="contenido">
        <?php 
        if (isset($vistaInterna) && file_exists($vistaInterna)) {
            include $vistaInterna;
        } else {
            echo '<div style="padding: 40px; text-align: center;">';
            echo '<h2>⚠️ Vista no encontrada</h2>';
            echo '</div>';
        }
        ?>
    </main>
</div>

<!-- 🔻 Footer -->
<?php include __DIR__ . "/../includes/footer.php"; ?>

<!-- 📜 Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= BASE_URL ?>/recursos/js/app.js"></script>

</body>
</html>