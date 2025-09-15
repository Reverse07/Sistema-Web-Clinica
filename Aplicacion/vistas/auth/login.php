<?php
require_once __DIR__ . "/../../../nucleo/Autenticacion.php"; // 📦 Importa clase de autenticación
Autenticacion::iniciarSesionSegura();                        // 🔐 Inicia sesión segura (cookies, HTTPS, etc.)
$csrf = Autenticacion::tokenCsrf();                          // 🛡️ Genera token CSRF para proteger el formulario
?>

<!DOCTYPE html> <!-- 📄 Documento HTML5 -->
<html lang="en"> <!-- 🌐 Idioma del documento -->

<head>
    <meta charset="UTF-8"> <!-- 🔤 Codificación UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- 📱 Diseño responsive -->
    <title>Iniciar Sesión</title> <!-- 🧾 Título de la pestaña -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/recursos/css/app.css"><!-- 🎨 Enlace a hoja de estilos -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>

<body>

    <div class="login-container"> <!-- 📦 Contenedor principal del login -->
        <div class="logo-container"> <!-- 🖼️ Contenedor del logo -->
            <img src="<?= BASE_URL ?>/recursos/img/logoClinica.jpg" alt="Logo de la aplicación" class="logo-login">
        </div>

        <h2>Iniciar Sesión</h2> <!-- 🧾 Título del formulario -->

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

            <!-- ✅ Checkbox Recordarme -->
            <div class="checkbox-grupo">
                <input type="checkbox" id="recordarme" name="recordarme">
                <label for="recordarme">Recordarme</label>
            </div>

            <button type="submit" class="btn">Ingresar</button>
        </form>

        <p class="link-registro"> <!-- 🔗 Enlace a registro -->
            ¿No tienes cuenta?
            <a href="<?= BASE_URL ?>/index.php?accion=registroVista">Regístrate aquí</a> <!-- 📝 Link a registro -->
        </p>
    </div>

    <?php include __DIR__ . "/../../../includes/footer.php"; ?> <!-- 📎 Pie de página común -->
</body>

</html>