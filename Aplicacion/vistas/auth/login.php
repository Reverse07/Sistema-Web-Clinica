<?php
require_once __DIR__ . "/../../../nucleo/Autenticacion.php";
Autenticacion::iniciarSesionSegura();
$csrf = Autenticacion::tokenCsrf();

// 🔍 DEBUGGING: Muestra la ruta completa del CSS
$rutaCSS = BASE_URL . "/recursos/css/app.css";
$rutaAbsoluta = $_SERVER['DOCUMENT_ROOT'] . $rutaCSS;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    
    <!-- 🎨 CSS con ruta corregida -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/recursos/css/app.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- 🔍 DEBUG: Eliminar después de verificar -->
    <script>
        console.log("BASE_URL:", "<?= BASE_URL ?>");
        console.log("Ruta CSS:", "<?= $rutaCSS ?>");
        console.log("Archivo existe:", <?= file_exists($rutaAbsoluta) ? 'true' : 'false' ?>);
    </script>
</head>

<body>
    <main> <!-- 🔹 Agregado para el layout del footer -->
        <div class="login-container">
            <div class="logo-container">
                <img src="<?= BASE_URL ?>/recursos/img/logoClinica.jpg" alt="Logo de la aplicación" class="logo-login">
            </div>

            <h2>Iniciar Sesión</h2>

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'registrado') : ?>
                <div class="success-msg">✅ Registro exitoso. Ahora puedes iniciar sesión.</div>
            <?php endif; ?>

            <?php if (isset($error)) : ?>
                <div class="error-msg"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/index.php?accion=login" method="POST">
                <input type="hidden" name="csrf" value="<?= $csrf ?>">

                <div class="form-grupo">
                    <label for="email">📧 Correo Electrónico</label>
                    <input type="email" name="email" id="email" placeholder="ejemplo@correo.com" required>

                    <label for="password">🔑 Contraseña</label>
                    <input type="password" name="password" id="password" placeholder="********" required>
                </div>

                <div class="checkbox-grupo">
                    <input type="checkbox" id="recordarme" name="recordarme">
                    <label for="recordarme">Recordarme</label>
                </div>

                <button type="submit" class="btn">Ingresar</button>
            </form>

            <p class="link-registro">
                ¿No tienes cuenta?
                <a href="<?= BASE_URL ?>/index.php?accion=registroVista">Regístrate aquí</a>
            </p>
        </div>
    </main> <!-- 🔹 Cierre de main -->

    <?php include __DIR__ . "/../../../includes/footer.php"; ?>
</body>

</html>