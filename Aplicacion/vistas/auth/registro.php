<?php
require_once __DIR__ . "/../../../nucleo/Autenticacion.php";
Autenticacion::iniciarSesionSegura();
$csrf = Autenticacion::tokenCsrf();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel = "stylesheet" href="<?= BASE_URL?>/publico/recursos/css/app.css">

</head>
<body>

<div class = "registro-container">
    <div class = "logo-container">
        <img src = "<?= BASE_URL ?>/publico/img/logo.png" alt="Logo de la aplicación" class="logo-login">
    </div>

    <h2>Crear cuenta</h2>
    <?php if(isset($error)) : ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/publico/index.php?accion=registro" method="POST">
        <input type="hidden" name="csrf" value="<?= $csrf ?>">

        <div class = "form-grupo">
            <label for = "nombre">👤 Nombre completo</label>
            <input type="text" name = "nombre" id = "nombre" placeholder="Juan Pérez" required>

        </div>

        <div class = "form-grupo">
            <label for = "email">📧 Correo Electrónico</label>
            <input type="email" name="email" id = "email" placeholder="ejemplo@correo.com" required>
        </div>

        <div class = "form-grupo">
            <label for = "telefono">📱 Teléfono</label>
            <input type="text" name="telefono" id = "telefono" placeholder="987654321" required>
        </div>

        <div class = "form-grupo">
            <label for = "password">🔑 Contraseña</label>
            <input type="password" name="password" id = "password" placeholder="********" required>
        </div>

        <div class = "form-grupo">
            <label for = "rol_id">🎭 Rol</label>
            <select name = "rol_id" id = "rol_id" required>
                <option value = "3">Paciente</option>
                <option value = "2">Doctor</option>
                <option value = "1">Administrador</option>
            </select>
        </div>

        <button type="submit" class = "btn">Registrarse</button>
    </form>

<p class = "link-login">
    ¿Ya tienes cuenta?
    <a href="<?= BASE_URL ?>/publico/index.php?accion=loginVista">Inici sesion aqui</a>
</p>

</div>

<?php include __DIR__ . "/../../../includes/footer.php"; ?>
    
</body>
</html>