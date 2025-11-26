<?php
require_once __DIR__ . "/../configuracion/app.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrativo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ✅ DEFINIR BASE_URL GLOBALMENTE - DEBE IR AQUÍ PRIMERO -->
    <script>
        window.BASE_URL = "<?php echo BASE_URL; ?>";
        console.log("✅ BASE_URL cargado desde layout:", window.BASE_URL);
    </script>

    <!-- 🎨 Estilos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/recursos/css/panel.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

    <!-- 🔝 Encabezado -->
    <?php include __DIR__ . "/encabezado.php"; ?>

    <div class="panel-admin">

        <!-- 🧭 Menú lateral -->
        <aside class="sidebar">
            <?php include __DIR__ . "/menu-admin.php"; ?>
        </aside>

        <!-- 📦 Contenido principal -->
        <main class="contenido">
            <?php
            if (isset($vistaInterna) && file_exists($vistaInterna)) {
                include $vistaInterna;
            } else {
                echo '<div style="padding: 40px; text-align: center; color: #e74c3c;">';
                echo '<h2>⚠️ Vista no encontrada</h2>';
                if (isset($vistaInterna)) {
                    echo '<p><code>' . htmlspecialchars($vistaInterna) . '</code></p>';
                }
                echo '</div>';
            }
            ?>
        </main>

    </div>

    <!-- 🔻 Footer -->
    <?php include __DIR__ . "/footer.php"; ?>

    <!-- 📜 Scripts AL FINAL -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= BASE_URL ?>/recursos/js/app.js"></script>

</body>
</html>