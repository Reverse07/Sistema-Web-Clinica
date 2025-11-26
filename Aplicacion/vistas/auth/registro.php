<?php
require_once __DIR__ . "/../../../nucleo/Autenticacion.php";
Autenticacion::iniciarSesionSegura();
$csrf = Autenticacion::tokenCsrf();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Paciente</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/recursos/css/app.css">
</head>
<body>

<div class="registro-container">
    <div class="logo-container">
        <img src="<?= BASE_URL ?>/recursos/img/logoClinica.jpg" alt="Logo de la aplicación" class="logo-login">
    </div>

    <h2>Crear cuenta de Paciente</h2>
    
    <?php if(isset($error)): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if(isset($_GET['msg']) && $_GET['msg'] === 'registrado'): ?>
        <div class="success-msg">✅ Registro exitoso. Ya puedes iniciar sesión.</div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/index.php?accion=registro" method="POST">
        <input type="hidden" name="csrf" value="<?= $csrf ?>">

        <div class="form-grupo">
            <label for="nombre">👤 Nombre completo</label>
            <input type="text" 
                   name="nombre" 
                   id="nombre" 
                   placeholder="Juan Pérez" 
                   value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                   required>
        </div>

        <div class="form-grupo">
            <label for="email">📧 Correo Electrónico</label>
            <input type="email" 
                   name="email" 
                   id="email" 
                   placeholder="ejemplo@correo.com"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   required>
        </div>

        <div class="form-grupo">
            <label for="telefono">📱 Teléfono</label>
            <input type="text" 
                   name="telefono" 
                   id="telefono" 
                   placeholder="987654321"
                   value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>"
                   required>
        </div>

        <div class="form-grupo">
            <label for="password">🔑 Contraseña</label>
            <input type="password" 
                   name="password" 
                   id="password" 
                   placeholder="Mínimo 6 caracteres"
                   minlength="6"
                   required>
        </div>

        <div class="form-grupo">
            <label for="password_confirm">🔑 Confirmar Contraseña</label>
            <input type="password" 
                   name="password_confirm" 
                   id="password_confirm" 
                   placeholder="Repite tu contraseña"
                   minlength="6"
                   required>
        </div>

        <!-- ✅ Ya NO hay selector de rol -->

        <button type="submit" class="btn">Registrarse como Paciente</button>
    </form>

    <p class="link-login">
        ¿Ya tienes cuenta?
        <a href="<?= BASE_URL ?>/index.php?accion=loginVista">Inicia sesión aquí</a>
    </p>

    <p class="info-text" style="margin-top: 1rem; font-size: 0.9rem; color: #64748b; text-align: center;">
        ℹ️ Solo puedes registrarte como paciente.<br>
        Para cuentas de Doctor o Admin, contacta al administrador.
    </p>
</div>

<?php include __DIR__ . "/../../../includes/footer.php"; ?>

<script>
// Validar que las contraseñas coincidan
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirm').value;
    
    if (password !== confirm) {
        e.preventDefault();
        alert('❌ Las contraseñas no coinciden');
        return false;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('❌ La contraseña debe tener al menos 6 caracteres');
        return false;
    }
});
</script>

</body>
</html>