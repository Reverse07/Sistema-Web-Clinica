<div class="form-edicion">
    <h1>✏️ Editar Paciente</h1>

    <form method="POST" action="?accion=actualizarPaciente">
        <input type="hidden" name="id" value="<?= $paciente->getId() ?>">

        <div class="campo">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($paciente->getNombre()) ?>" required>
        </div>

        <div class="campo">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($paciente->getEmail()) ?>" required>
        </div>

        <div class="campo">
            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono" value="<?= htmlspecialchars($paciente->getTelefono()) ?>" required>
        </div>

        <div class="campo">
            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="<?= htmlspecialchars($paciente->getFechaNacimiento()) ?>" required>
        </div>

        <div class="campo">
            <label for="genero">Género:</label>
            <select name="genero" id="genero" required>
                <option value="">-- Selecciona --</option>
                <option value="Masculino" <?= $paciente->getGenero() === 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                <option value="Femenino" <?= $paciente->getGenero() === 'Femenino' ? 'selected' : '' ?>>Femenino</option>
                <option value="Otro" <?= $paciente->getGenero() === 'Otro' ? 'selected' : '' ?>>Otro</option>
            </select>
        </div>

        <div class="campo">
            <label for="direccion">Dirección:</label>
            <textarea name="direccion" id="direccion" rows="3" required><?= htmlspecialchars($paciente->getDireccion()) ?></textarea>
        </div>

        <div class="campo">
            <label for="dni">DNI:</label>
            <input type="text" name="dni" id="dni" value="<?= htmlspecialchars($paciente->getDni()) ?>" required maxlength="15">
        </div>

        <div class="acciones-formulario">
            <button type="submit" class="btn-guardar">💾 Actualizar Paciente</button>
            <a href="?accion=gestionarPacientes" class="btn-cancelar">↩️ Cancelar</a>
        </div>
    </form>
</div>
