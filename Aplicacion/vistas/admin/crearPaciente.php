<?php
// =====================
// 🧑‍⚕️ Vista: Crear Paciente
// =====================

// Recuperar datos del formulario si hubo error
$datos = $_SESSION['datos_formulario'] ?? [];
unset($_SESSION['datos_formulario']);

// Mostrar mensaje si existe
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '<?= $mensaje['tipo'] === 'exito' ? 'success' : 'error' ?>',
                title: '<?= $mensaje['tipo'] === 'exito' ? 'Éxito' : 'Error' ?>',
                text: '<?= htmlspecialchars($mensaje['texto']) ?>',
                confirmButtonColor: '#3b82f6'
            });
        });
    </script>
    <?php
}
?>

<div class="form-edicion">
    <h1>➕ Crear Nuevo Paciente</h1>

    <form method="POST" action="<?= BASE_URL ?>/index.php?accion=guardarPaciente" class="formulario" data-validate="true">
        
        <!-- Información del Usuario -->
        <div class="campo">
            <label for="nombre">Nombre Completo <span style="color: red;">*</span></label>
            <input 
                type="text" 
                id="nombre" 
                name="nombre" 
                placeholder="Ej: Juan Pérez García"
                value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>"
                required>
        </div>

        <div class="campo">
            <label for="email">Correo Electrónico <span style="color: red;">*</span></label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                placeholder="ejemplo@correo.com"
                value="<?= htmlspecialchars($datos['email'] ?? '') ?>"
                required>
        </div>

        <div class="campo">
            <label for="password">Contraseña <span style="color: red;">*</span></label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Mínimo 6 caracteres"
                minlength="6"
                required>
        </div>

        <div class="campo">
            <label for="telefono">Teléfono</label>
            <input 
                type="tel" 
                id="telefono" 
                name="telefono" 
                placeholder="Ej: +51 987654321"
                value="<?= htmlspecialchars($datos['telefono'] ?? '') ?>">
        </div>

        <!-- Información Específica del Paciente -->
        <div class="campo">
            <label for="dni">DNI <span style="color: red;">*</span></label>
            <input 
                type="text" 
                id="dni" 
                name="dni" 
                placeholder="Ej: 12345678"
                maxlength="8"
                pattern="[0-9]{8}"
                value="<?= htmlspecialchars($datos['dni'] ?? '') ?>"
                required>
        </div>

        <div class="campo">
            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
            <input 
                type="date" 
                id="fecha_nacimiento" 
                name="fecha_nacimiento"
                max="<?= date('Y-m-d') ?>"
                value="<?= htmlspecialchars($datos['fecha_nacimiento'] ?? '') ?>">
        </div>

        <div class="campo">
            <label for="genero">Género</label>
            <select id="genero" name="genero">
                <option value="">Seleccionar</option>
                <option value="Masculino" <?= ($datos['genero'] ?? '') === 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                <option value="Femenino" <?= ($datos['genero'] ?? '') === 'Femenino' ? 'selected' : '' ?>>Femenino</option>
                <option value="Otro" <?= ($datos['genero'] ?? '') === 'Otro' ? 'selected' : '' ?>>Otro</option>
            </select>
        </div>

        <div class="campo">
            <label for="direccion">Dirección</label>
            <input 
                type="text" 
                id="direccion" 
                name="direccion" 
                placeholder="Ej: Av. Principal 123, Lima"
                value="<?= htmlspecialchars($datos['direccion'] ?? '') ?>">
        </div>

        <!-- Botones de Acción -->
        <div class="acciones-formulario">
            <button type="submit" class="btn-guardar">💾 Guardar Paciente</button>
            <a href="<?= BASE_URL ?>/index.php?accion=gestionarPacientes" class="btn-cancelar">❌ Cancelar</a>
        </div>
    </form>
</div>

<style>
    .campo {
        margin-bottom: 1.5rem;
    }

    .campo label {
        display: block;
        font-weight: 700;
        font-size: 0.875rem;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    .campo label span {
        color: #ef4444;
    }

    .campo input,
    .campo select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid var(--gray-200);
        border-radius: 0.5rem;
        font-size: 0.875rem;
        color: var(--gray-900);
        transition: all 0.15s;
        font-weight: 500;
        background: var(--white);
        font-family: var(--font-family);
    }

    .campo input:focus,
    .campo select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .acciones-formulario {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .acciones-formulario .btn-guardar,
    .acciones-formulario .btn-cancelar {
        flex: 1;
        text-align: center;
        padding: 1rem;
        border-radius: 999px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.25s;
        cursor: pointer;
        border: none;
        font-size: 0.875rem;
    }

    .acciones-formulario .btn-guardar {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3);
    }

    .acciones-formulario .btn-guardar:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
    }

    .acciones-formulario .btn-cancelar {
        background: #e5e7eb;
        color: #374151;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .acciones-formulario .btn-cancelar:hover {
        background: #d1d5db;
        transform: translateY(-3px);
    }
</style>