<div class="admin-usuarios">
    <h1>👥 Gestión de Usuarios</h1>

    <table class="tabla-usuarios">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= $usuario->getId() ?></td>
                    <td><?= $usuario->getNombre() ?></td>
                    <td><?= $usuario->getEmail() ?></td>
                    <td><?= $usuario->getTelefono() ?></td>
                    <td>
                        <?php
                        $rolId = $usuario->getRolId();
                        $roles = [1 => 'Admin', 2 => 'Doctor', 3 => 'Paciente'];
                        echo $roles[$rolId] ?? 'Desconocido';
                        ?>
                    </td>
                    <td>
                        <a href="<?= BASE_URL ?>/index.php?accion=editarUsuario&id=<?= $usuario->getId() ?>" class="btn-editar">✏️ Editar</a>
                        <a href="<?= BASE_URL ?>/index.php?accion=eliminarUsuario&id=<?= $usuario->getId() ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este usuario?')">🗑️ Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
