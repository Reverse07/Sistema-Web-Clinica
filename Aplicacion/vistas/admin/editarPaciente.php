<?php
// =====================
// 🧑‍⚕️ Vista: Editar Paciente
// =====================
?>

<div class="formulario-container">
    <h1>✏️ Editar Paciente</h1>

    <?php if (isset($_SESSION['errores']) && !empty($_SESSION['errores'])): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($_SESSION['errores'] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errores']); ?>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/index.php?accion=actualizarPaciente&id=<?= $paciente->getId() ?>" class="form-doctor">

        <!-- Nombre -->
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input 
                type="text" 
                id="nombre" 
                name="nombre" 
                value="<?= htmlspecialchars($paciente->getNombre()) ?>" 
                required 
                class="form-control">
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email:</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="<?= htmlspecialchars($paciente->getEmail()) ?>" 
                required 
                class="form-control">
        </div>

        <!-- Teléfono -->
        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input 
                type="text" 
                id="telefono" 
                name="telefono" 
                value="<?= htmlspecialchars($paciente->getTelefono()) ?>" 
                class="form-control">
        </div>

        <!-- Contraseña (opcional) -->
        <div class="form-group">
            <label for="password">Contraseña (dejar en blanco si no desea cambiar):</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Nueva contraseña (opcional)"
                class="form-control">
        </div>

        <!-- Fecha de Nacimiento -->
        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input 
                type="date" 
                id="fecha_nacimiento" 
                name="fecha_nacimiento" 
                value="<?= htmlspecialchars($pacienteData->getFechaNacimiento()) ?>"
                class="form-control">
        </div>

        <!-- Género -->
        <div class="form-group">
            <label for="genero">Género:</label>
            <select 
                id="genero" 
                name="genero" 
                class="form-control">
                <option value="">-- Selecciona --</option>
                <option value="Masculino" <?= $pacienteData->getGenero() === 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                <option value="Femenino" <?= $pacienteData->getGenero() === 'Femenino' ? 'selected' : '' ?>>Femenino</option>
                <option value="Otro" <?= $pacienteData->getGenero() === 'Otro' ? 'selected' : '' ?>>Otro</option>
            </select>
        </div>

        <!-- Dirección -->
        <div class="form-group">
            <label for="direccion">Dirección:</label>
            <textarea 
                id="direccion" 
                name="direccion" 
                rows="3" 
                class="form-control"><?= htmlspecialchars($pacienteData->getDireccion()) ?></textarea>
        </div>

        <!-- DNI -->
        <div class="form-group">
            <label for="dni">DNI:</label>
            <input 
                type="text" 
                id="dni" 
                name="dni" 
                value="<?= htmlspecialchars($pacienteData->getDni()) ?>" 
                class="form-control">
        </div>

        <!-- Botones -->
        <div class="form-actions">
            <button type="submit" class="btn-guardar">💾 Guardar Cambios</button>
            <a href="<?= BASE_URL ?>/index.php?accion=gestionarPacientes" class="btn-cancelar">❌ Cancelar</a>
        </div>
    </form>
</div>

<style>
/* 🩺 Estilo general del contenedor */
.formulario-container {
    max-width: 600px;
    margin: 20px auto;
    padding: 30px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.formulario-container h1 {
    margin-bottom: 25px;
    color: #333;
    text-align: center;
    font-weight: 700;
}

/* 📝 Estilo de los campos del formulario */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #555;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
}

.form-control:focus {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.2);
}

/* 📌 Acciones */
.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 30px;
    justify-content: center;
}

.btn-guardar, .btn-cancelar {
    padding: 12px 25px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    font-weight: 600;
}

.btn-guardar {
    background: #4CAF50;
    color: #fff;
    border-radius: 30px;
}

.btn-guardar:hover {
    background: #45a049;
}

.btn-cancelar {
    background: #f44336;
    color: #fff;
    border-radius: 30px;
}

.btn-cancelar:hover {
    background: #da190b;
}

/* ⚠️ Alertas de error */
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
}

.alert-danger {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.alert ul {
    margin: 0;
    padding-left: 20px;
}
</style>
