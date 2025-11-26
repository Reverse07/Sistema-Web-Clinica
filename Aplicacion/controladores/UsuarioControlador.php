<?php
require_once __DIR__ . "/../../nucleo/Autenticacion.php";
require_once __DIR__ . "/../../nucleo/BaseDatos.php";
require_once __DIR__ . "/../modelos/Usuario.php";
require_once __DIR__ . "/../modelos/Doctor.php";

class UsuarioControlador
{
    /**
     * Redirección helper
     */
   private function redirigir($accion, $params = [])
{
    // Construir parámetros
    $params['accion'] = $accion;
    $query = http_build_query($params);
    
    // ✅ URL completa con BASE_URL
    $url = BASE_URL . "/index.php?" . $query;
    
    // Log para debugging
    error_log("🔀 [REDIRIGIR] URL generada: $url");
    
    // Redirigir
    header("Location: $url");
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

   
    // =====================================================
// 👥 USUARIOCONTROLADOR - eliminarUsuario()
// =====================================================
public function eliminarUsuario()
{
    Autenticacion::requiereRoles(['admin']);
    
    error_log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
    error_log("🟡 [CONTROLADOR] Método eliminarUsuario() iniciado");
    
    $id = $_GET['id'] ?? null;
    error_log("🟡 [CONTROLADOR] ID recibido: " . ($id ?? 'NULL'));
    
    if (!$id) {
        error_log("❌ [CONTROLADOR] ID no proporcionado");
        $this->setMensaje('error', 'ID no proporcionado');
        $this->redirigir('gestionarUsuarios');
    }
    
    // Verificar que no sea el usuario actual
    $usuarioActual = $_SESSION['usuario_id'] ?? null;
    if ($usuarioActual == $id) {
        error_log("❌ [CONTROLADOR] Intento de eliminar usuario actual ($id)");
        $this->setMensaje('error', 'No puedes eliminar tu propio usuario');
        $this->redirigir('gestionarUsuarios');
    }
    
    try {
        error_log("🟡 [CONTROLADOR] Llamando a Usuario::eliminarUsuario($id)");
        $resultado = Usuario::eliminarUsuario((int)$id);
        
        if ($resultado) {
            error_log("✅ [CONTROLADOR] Eliminación exitosa");
            $this->setMensaje('exito', '✅ Usuario eliminado exitosamente');
        } else {
            error_log("⚠️ [CONTROLADOR] Eliminación falló (retornó false)");
            $this->setMensaje('error', '⚠️ No se pudo eliminar el usuario');
        }
        
    } catch (Exception $e) {
        error_log("❌ [CONTROLADOR] Excepción capturada: " . $e->getMessage());
        error_log("❌ [CONTROLADOR] Stack trace: " . $e->getTraceAsString());
        $this->setMensaje('error', '❌ Error al eliminar el usuario: ' . $e->getMessage());
    }
    
    error_log("🟡 [CONTROLADOR] Redirigiendo a gestionarUsuarios");
    error_log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
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
    $rol = $_SESSION['rol'] ?? 'paciente';
    
    if (!$usuarioId) {
        $this->redirigir('login');
    }

    $pdo = BaseDatos::pdo();

    try {
        // Validación básica
        if (empty($_POST['nombre']) || empty($_POST['email'])) {
            throw new Exception('Campos obligatorios incompletos');
        }

        // Validar email
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido');
        }

        $pdo->beginTransaction();

        // Actualizar tabla usuarios
        $stmtUsuario = $pdo->prepare("
            UPDATE usuarios 
            SET nombre = :nombre, 
                email = :email, 
                telefono = :telefono
            WHERE id = :id
        ");
        
        $stmtUsuario->execute([
            ':nombre' => trim($_POST['nombre']),
            ':email' => trim($_POST['email']),
            ':telefono' => trim($_POST['telefono'] ?? ''),
            ':id' => $usuarioId
        ]);

        // Si es doctor, actualizar tabla doctores
        if ($rol === 'doctor') {
            $stmtDoctor = $pdo->prepare("
                UPDATE doctores 
                SET especialidad_id = :especialidad_id,
                    numero_colegiatura = :numero_colegiatura
                WHERE usuario_id = :usuario_id
            ");
            
            $especialidadId = !empty($_POST['especialidad_id']) ? (int)$_POST['especialidad_id'] : null;
            
            $stmtDoctor->execute([
                ':especialidad_id' => $especialidadId,
                ':numero_colegiatura' => trim($_POST['numero_colegiatura'] ?? ''),
                ':usuario_id' => $usuarioId
            ]);
        }

        // Si es paciente, actualizar tabla pacientes
        if ($rol === 'paciente') {
            $stmtPaciente = $pdo->prepare("
                UPDATE pacientes 
                SET fecha_nacimiento = :fecha_nacimiento,
                    genero = :genero,
                    direccion = :direccion,
                    dni = :dni
                WHERE usuario_id = :usuario_id
            ");
            
            $stmtPaciente->execute([
                ':fecha_nacimiento' => !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null,
                ':genero' => $_POST['genero'] ?? null,
                ':direccion' => trim($_POST['direccion'] ?? ''),
                ':dni' => trim($_POST['dni'] ?? 'Sin DNI'),
                ':usuario_id' => $usuarioId
            ]);
        }

        $pdo->commit();

        $this->setMensaje('exito', '✅ Perfil actualizado exitosamente');

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Error al actualizar perfil: " . $e->getMessage());
        $this->setMensaje('error', 'Error: ' . $e->getMessage());
    }

    // Redirigir según rol
    $accion = match($rol) {
        'admin' => 'miPerfil',
        'doctor' => 'doctorPerfil',
        'paciente' => 'pacientePerfil',
        default => 'miPerfil'
    };

    $this->redirigir($accion);
}

    /**
     * Cambia la contraseña del usuario logueado
     */
   public function cambiarPassword()
{
    Autenticacion::requiereLogin();

    $usuarioId = $_SESSION['usuario_id'] ?? null;
    $rol = $_SESSION['rol'] ?? 'paciente';
    
    if (!$usuarioId) {
        $this->redirigir('login');
    }

    $passwordActual = $_POST['password_actual'] ?? '';
    $passwordNueva = $_POST['password_nueva'] ?? '';
    $passwordConfirmar = $_POST['password_confirmar'] ?? '';

    try {
        // Validaciones
        if (empty($passwordActual) || empty($passwordNueva) || empty($passwordConfirmar)) {
            throw new Exception('Todos los campos son obligatorios');
        }

        if ($passwordNueva !== $passwordConfirmar) {
            throw new Exception('Las contraseñas nuevas no coinciden');
        }

        if (strlen($passwordNueva) < 6) {
            throw new Exception('La contraseña debe tener al menos 6 caracteres');
        }

        // Verificar contraseña actual
        $usuario = Usuario::buscarPorId($usuarioId);
        if (!$usuario || !password_verify($passwordActual, $usuario->getPassword())) {
            throw new Exception('Contraseña actual incorrecta');
        }

        // Actualizar contraseña
        Usuario::actualizarUsuario(
            (int)$usuarioId,
            $usuario->getNombre(),
            $usuario->getEmail(),
            $usuario->getTelefono(),
            $passwordNueva
        );

        $this->setMensaje('exito', '✅ Contraseña cambiada exitosamente');

    } catch (Exception $e) {
        error_log("Error al cambiar contraseña: " . $e->getMessage());
        $this->setMensaje('error', 'Error: ' . $e->getMessage());
    }

    // Redirigir según rol
    $accion = match($rol) {
        'admin' => 'miPerfil',
        'doctor' => 'doctorPerfil',
        'paciente' => 'pacientePerfil',
        default => 'miPerfil'
    };

    $this->redirigir($accion);
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