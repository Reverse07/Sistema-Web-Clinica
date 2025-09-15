<div class="form-edicion">
    <h1>✏️ Editar Doctor</h1>

    <form method="POST" action="?accion=actualizarDoctor&id=<?= $doctor->getId() ?>" class="formulario">
        <div class="campo">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($doctor->getNombre()) ?>" required>
        </div>

        <div class="campo">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($doctor->getEmail()) ?>" required>
        </div>

        <div class="campo">
            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono" value="<?= htmlspecialchars($doctor->getTelefono()) ?>" required>
        </div>

        <div class="campo">
            <label for="especialidad">Especialidad:</label>
            <input type="text" name="especialidad" id="especialidad" value="<?= htmlspecialchars($doctor->getEspecialidad()) ?>" required>
        </div>

        <div class="acciones-formulario">
            <button type="submit" class="btn-guardar">💾 Guardar cambios</button>
            <a href="?accion=gestionarDoctores" class="btn-cancelar">↩️ Cancelar</a>
        </div>
    </form>
</div>
