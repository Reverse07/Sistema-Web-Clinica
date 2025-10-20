<?php
require_once __DIR__ . "/../../nucleo/Autenticacion.php";
require_once __DIR__ . "/../../nucleo/BaseDatos.php";
require_once __DIR__ . "/../modelos/Usuario.php";
require_once __DIR__ . "/../modelos/Doctor.php";

/**
 * UsuarioControlador
 * 
 * Responsabilidad: Gestión genérica de usuarios
 * - CRUD de usuarios desde admin (cualquier rol)
 * - Gestión de perfil propio (cualquier usuario logueado)
 */
class UsuarioControlador
{
    /**
     * Redirección helper
     */
    private function redirigir($accion, $params = [])
    {
        $query = http_build_query(array_merge(['accion' => $accion], $params));
        header("Location: ?$query");
        exit;
    }

    /**
     * Establece mensaje flash en sesión
     */
    private function setMensaje($tipo, $texto)
    {
        $_SESSION['mensaje'] = ['tipo' => $tipo, 'texto' => $texto];
    }

    // ========================================
    // GESTIÓN ADMIN DE USUARIOS
    // ========================================

    /**
     * Lista todos los usuarios del sistema
     */
    public function gestionarUsuarios()
    {
        Autenticacion::requiereRoles(['admin']);
        
        $usuarios = Usuario::obtenerTodos();
        $vistaInterna = __DIR__ . "/../vistas/admin/usuarios.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * Muestra formulario para editar usuario
     */
    public function editarUsuario()
    {
        Autenticacion::requiereRoles(['admin']);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirigir('gestionarUsuarios');
        }

        $usuarioEditar = Usuario::buscarPorId((int)$id);
        if (!$usuarioEditar) {
            $this->setMensaje('error', 'Usuario no encontrado');
            $this->redirigir('gestionarUsuarios');
        }

        // Si es doctor, cargar datos adicionales
        $doctor = null;
        if ($usuarioEditar->getRolId() === 2) {
            $doctor = Doctor::buscarPorUsuarioId($usuarioEditar->getId());
            require_once __DIR__ . "/../modelos/Especialidad.php";
            $especialidades = Especialidad::todas();
        }

        $vistaInterna = __DIR__ . "/../vistas/admin/editarUsuario.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * Actualiza los datos de un usuario
     */
    public function actualizarUsuario()
    {
        Autenticacion::requiereRoles(['admin']);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirigir('gestionarUsuarios');
        }

        // Validación básica
        if (empty($_POST['nombre']) || empty($_POST['email'])) {
            $this->setMensaje('error', 'Campos obligatorios incompletos');
            $this->redirigir('editarUsuario', ['id' => $id]);
        }

        // Validar email
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $this->setMensaje('error', 'Email inválido');
            $this->redirigir('editarUsuario', ['id' => $id]);
        }

        try {
            BaseDatos::pdo()->beginTransaction();

            // Actualizar usuario básico
            $password = !empty($_POST['password']) ? $_POST['password'] : null;
            Usuario::actualizarUsuario(
                (int)$id,
                $_POST['nombre'],
                $_POST['email'],
                $_POST['telefono'] ?? '',
                $password
            );

            // Si es doctor, actualizar datos adicionales
            $usuario = Usuario::buscarPorId((int)$id);
            if ($usuario && $usuario->getRolId() === 2) {
                $doctor = Doctor::buscarPorUsuarioId($usuario->getId());
                if ($doctor) {
                    Doctor::actualizar(
                        $doctor->getId(),
                        (int)$_POST['especialidad_id'],
                        $_POST['numero_colegiatura'] ?? ''
                    );
                }
            }

            BaseDatos::pdo()->commit();
            $this->setMensaje('exito', 'Usuario actualizado exitosamente');
        } catch (Exception $e) {
            BaseDatos::pdo()->rollBack();
            error_log("Error al actualizar usuario: " . $e->getMessage());
            $this->setMensaje('error', 'Error al actualizar el usuario');
        }

        $this->redirigir('gestionarUsuarios');
    }

    /**
     * Elimina un usuario del sistema
     */
    public function eliminarUsuario()
    {
        Autenticacion::requiereRoles(['admin']);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarUsuarios');
        }

        // Verificar que no sea el usuario actual
        if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id) {
            $this->setMensaje('error', 'No puedes eliminar tu propio usuario');
            $this->redirigir('gestionarUsuarios');
        }

        try {
            Usuario::eliminarUsuario((int)$id);
            $this->setMensaje('exito', 'Usuario eliminado exitosamente');
        } catch (Exception $e) {
            error_log("Error al eliminar usuario: " . $e->getMessage());
            $this->setMensaje('error', 'Error al eliminar el usuario');
        }

        $this->redirigir('gestionarUsuarios');
    }

    // ========================================
    // GESTIÓN DE PERFIL PROPIO
    // ========================================

    /**
     * Muestra el perfil del usuario logueado
     */
    public function miPerfil()
    {
        Autenticacion::requiereLogin();

        $usuarioId = $_SESSION['usuario_id'] ?? null;
        if (!$usuarioId) {
            $this->redirigir('login');
        }

        $usuario = Usuario::buscarPorId($usuarioId);
        $rol = $_SESSION['rol'] ?? 'paciente';

        // Determinar qué layout usar según el rol
        switch ($rol) {
            case 'admin':
                $vistaInterna = __DIR__ . "/../vistas/admin/perfil.php";
                require __DIR__ . "/../../includes/layout-admin.php";
                break;
            case 'doctor':
                $vistaInterna = __DIR__ . "/../vistas/doctor/perfil.php";
                require __DIR__ . "/../../includes/layout-doctor.php";
                break;
            case 'paciente':
                $vistaInterna = __DIR__ . "/../vistas/paciente/perfil.php";
                require __DIR__ . "/../../includes/layout-paciente.php";
                break;
        }
    }

    /**
     * Actualiza el perfil del usuario logueado
     */
    public function actualizarMiPerfil()
    {
        Autenticacion::requiereLogin();

        $usuarioId = $_SESSION['usuario_id'] ?? null;
        if (!$usuarioId) {
            $this->redirigir('login');
        }

        // Validación básica
        if (empty($_POST['nombre']) || empty($_POST['email'])) {
            $this->setMensaje('error', 'Campos obligatorios incompletos');
            $this->redirigir('miPerfil');
        }

        // Validar email
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $this->setMensaje('error', 'Email inválido');
            $this->redirigir('miPerfil');
        }

        try {
            $password = !empty($_POST['password']) ? $_POST['password'] : null;
            Usuario::actualizarUsuario(
                (int)$usuarioId,
                $_POST['nombre'],
                $_POST['email'],
                $_POST['telefono'] ?? '',
                $password
            );

            $this->setMensaje('exito', 'Perfil actualizado exitosamente');
        } catch (Exception $e) {
            error_log("Error al actualizar perfil: " . $e->getMessage());
            $this->setMensaje('error', 'Error al actualizar el perfil');
        }

        $this->redirigir('miPerfil');
    }

    /**
     * Cambia la contraseña del usuario logueado
     */
    public function cambiarPassword()
    {
        Autenticacion::requiereLogin();

        $usuarioId = $_SESSION['usuario_id'] ?? null;
        if (!$usuarioId) {
            $this->redirigir('login');
        }

        $passwordActual = $_POST['password_actual'] ?? '';
        $passwordNueva = $_POST['password_nueva'] ?? '';
        $passwordConfirmar = $_POST['password_confirmar'] ?? '';

        // Validaciones
        if (empty($passwordActual) || empty($passwordNueva) || empty($passwordConfirmar)) {
            $this->setMensaje('error', 'Todos los campos son obligatorios');
            $this->redirigir('miPerfil');
        }

        if ($passwordNueva !== $passwordConfirmar) {
            $this->setMensaje('error', 'Las contraseñas nuevas no coinciden');
            $this->redirigir('miPerfil');
        }

        if (strlen($passwordNueva) < 6) {
            $this->setMensaje('error', 'La contraseña debe tener al menos 6 caracteres');
            $this->redirigir('miPerfil');
        }

        try {
            // Verificar contraseña actual
            $usuario = Usuario::buscarPorId($usuarioId);
            if (!$usuario || !password_verify($passwordActual, $usuario->getPassword())) {
                $this->setMensaje('error', 'Contraseña actual incorrecta');
                $this->redirigir('miPerfil');
            }

            // Actualizar contraseña
            Usuario::actualizarUsuario(
                (int)$usuarioId,
                $usuario->getNombre(),
                $usuario->getEmail(),
                $usuario->getTelefono(),
                $passwordNueva
            );

            $this->setMensaje('exito', 'Contraseña actualizada exitosamente');
        } catch (Exception $e) {
            error_log("Error al cambiar contraseña: " . $e->getMessage());
            $this->setMensaje('error', 'Error al cambiar la contraseña');
        }

        $this->redirigir('miPerfil');
    }

    /**
     * Cambia el rol de un usuario (solo admin)
     */
    public function cambiarRol()
    {
        Autenticacion::requiereRoles(['admin']);

        $id = $_POST['usuario_id'] ?? null;
        $nuevoRol = $_POST['rol_id'] ?? null;

        if (!$id || !$nuevoRol) {
            $this->setMensaje('error', 'Datos incompletos');
            $this->redirigir('gestionarUsuarios');
        }

        // No permitir cambiar el rol del propio admin
        if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id) {
            $this->setMensaje('error', 'No puedes cambiar tu propio rol');
            $this->redirigir('gestionarUsuarios');
        }

        try {
            Usuario::cambiarRol((int)$id, (int)$nuevoRol);
            $this->setMensaje('exito', 'Rol actualizado exitosamente');
        } catch (Exception $e) {
            error_log("Error al cambiar rol: " . $e->getMessage());
            $this->setMensaje('error', 'Error al cambiar el rol');
        }

        $this->redirigir('gestionarUsuarios');
    }
}