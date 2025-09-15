<div class="form-edicion">
    <h1>➕ Crear Doctor</h1>

    <form method="POST" action="?accion=guardarDoctor" class="formulario">
        <div class="campo">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required>
        </div>

        <div class="campo">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div class="campo">
            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono" required>
        </div>

        <div class="campo">
            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required minlength="6">
        </div>

        <div class="acciones-formulario">
            <button type="submit" class="btn-guardar">💾 Guardar Doctor</button>
            <a href="?accion=gestionarDoctores" class="btn-cancelar">↩️ Cancelar</a>
        </div>
    </form>
</div>
