<div class="form-edicion">
    <h1>✏️ Editar Usuario</h1>

    <form method="POST" action="?accion=actualizarUsuario&id=<?= $usuarioEditar->getId() ?>" class="formulario">
        <div class="campo">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" 
                   value="<?= htmlspecialchars($usuarioEditar->getNombre()) ?>" required>
        </div>

        <div class="campo">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" 
                   value="<?= htmlspecialchars($usuarioEditar->getEmail()) ?>" required>
        </div>

        <div class="campo">
            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono" 
                   value="<?= htmlspecialchars($usuarioEditar->getTelefono()) ?>" required>
        </div>

        <div class="campo">
            <label for="password">Nueva Contraseña:</label>
            <input type="password" name="password" id="password" 
                   placeholder="Dejar vacío para no cambiar">
        </div>

        <?php if ($usuarioEditar->getRolId() === 2 && $doctor): ?>
            <div class="campo">
                <label for="especialidad_id">Especialidad:</label>
                <select name="especialidad_id" id="especialidad_id" required>
                    <?php foreach (Especialidad::todas() as $esp): ?>
                        <option value="<?= $esp['id'] ?>" 
                            <?= $esp['id'] == $doctor->getEspecialidadId() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($esp['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="colegiatura">N° Colegiatura:</label>
                <input type="text" name="numero_colegiatura" id="colegiatura" 
                       value="<?= htmlspecialchars($doctor->getNumeroColegiatura()) ?>">
            </div>
        <?php endif; ?>

        <div class="acciones-formulario">
            <button type="submit" class="btn-guardar">💾 Guardar cambios</button>
            <a href="?accion=gestionarUsuarios" class="btn-cancelar">↩️ Cancelar</a>
        </div>
    </form>
</div>
