<?php
require_once __DIR__ . "/../../nucleo/Autenticacion.php";
require_once __DIR__ . "/../../nucleo/BaseDatos.php";
require_once __DIR__ . "/../modelos/Usuario.php";
require_once __DIR__ . "/../modelos/Paciente.php";

/**
 * PacienteControlador
 * 
 * Responsabilidad: TODO relacionado con pacientes
 * - Vistas del rol paciente (dashboard, perfil, citas, historial)
 * - CRUD de pacientes desde admin
 */
class PacienteControlador
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
    // VISTAS PARA EL ROL PACIENTE
    // ========================================

    /**
     * Dashboard del paciente logueado
     */
    public function dashboard()
    {
        Autenticacion::requiereRoles(['paciente']);
        
        $vistaInterna = __DIR__ . "/../vistas/paciente/inicio.php";
        require __DIR__ . "/../../includes/layout-paciente.php";
    }

    /**
     * Perfil del paciente logueado
     */
    public function perfil()
    {
        Autenticacion::requiereRoles(['paciente']);
        
        $vistaInterna = __DIR__ . "/../vistas/paciente/perfil.php";
        require __DIR__ . "/../../includes/layout-paciente.php";
    }

    /**
     * Citas del paciente logueado
     */
    public function misCitas()
    {
        Autenticacion::requiereRoles(['paciente']);
        
        $vistaInterna = __DIR__ . "/../vistas/paciente/citas.php";
        require __DIR__ . "/../../includes/layout-paciente.php";
    }

    /**
     * Historial médico del paciente
     */
    public function miHistorial()
    {
        Autenticacion::requiereRoles(['paciente']);
        
        $vistaInterna = __DIR__ . "/../vistas/paciente/historial.php";
        require __DIR__ . "/../../includes/layout-paciente.php";
    }

    /**
     * Facturas del paciente
     */
    public function misFacturas()
    {
        Autenticacion::requiereRoles(['paciente']);
        
        $vistaInterna = __DIR__ . "/../vistas/paciente/facturas.php";
        require __DIR__ . "/../../includes/layout-paciente.php";
    }

    // ========================================
    // GESTIÓN ADMIN DE PACIENTES
    // ========================================

    /**
     * Lista todos los pacientes (Vista Admin)
     */
    public function gestionarPacientes()
    {
        Autenticacion::requiereRoles(['admin']);
        
        $pacientes = Paciente::todos();
        $vistaInterna = __DIR__ . "/../vistas/admin/pacientes.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * Muestra formulario para crear paciente
     */
    public function crearPaciente()
    {
        Autenticacion::requiereRoles(['admin']);
        
        $vistaInterna = __DIR__ . "/../vistas/admin/crearPaciente.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * Guarda un nuevo paciente en la base de datos
     */
    public function guardarPaciente()
    {
        Autenticacion::requiereRoles(['admin']);

        // Validación básica
        if (empty($_POST['nombre']) || empty($_POST['email']) || empty($_POST['password'])) {
            $this->setMensaje('error', 'Campos obligatorios incompletos');
            $_SESSION['datos_formulario'] = $_POST;
            $this->redirigir('crearPaciente');
        }

        // Validar email
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $this->setMensaje('error', 'Email inválido');
            $_SESSION['datos_formulario'] = $_POST;
            $this->redirigir('crearPaciente');
        }

        // Validar DNI
        if (empty($_POST['dni'])) {
            $this->setMensaje('error', 'DNI es obligatorio');
            $_SESSION['datos_formulario'] = $_POST;
            $this->redirigir('crearPaciente');
        }

        try {
            BaseDatos::pdo()->beginTransaction();

            // Crear usuario
            $usuario = new Usuario([
                'nombre'   => $_POST['nombre'],
                'email'    => $_POST['email'],
                'telefono' => $_POST['telefono'] ?? '',
                'password' => $_POST['password'],
                'rol_id'   => 3
            ]);
            $usuario->crearUsuario();
            $usuarioId = BaseDatos::pdo()->lastInsertId();

            // Crear registro de paciente
            Paciente::crear(
                $usuarioId,
                $_POST['fecha_nacimiento'] ?? null,
                $_POST['genero'] ?? '',
                $_POST['direccion'] ?? '',
                $_POST['dni']
            );

            BaseDatos::pdo()->commit();
            $this->setMensaje('exito', 'Paciente creado exitosamente');
        } catch (Exception $e) {
            BaseDatos::pdo()->rollBack();
            error_log("Error al crear paciente: " . $e->getMessage());
            $this->setMensaje('error', 'Error al crear el paciente. Intente nuevamente.');
        }

        $this->redirigir('gestionarPacientes');
    }

    /**
     * Muestra formulario para editar paciente
     */
    public function editarPaciente()
    {
        Autenticacion::requiereRoles(['admin']);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setMensaje('error', 'ID de paciente no proporcionado');
            $this->redirigir('gestionarPacientes');
        }

        $paciente = Usuario::buscarPorId($id);
        if (!$paciente || $paciente->getRolId() != 3) {
            $this->setMensaje('error', 'Paciente no encontrado');
            $this->redirigir('gestionarPacientes');
        }

        $pacienteData = Paciente::buscarPorUsuarioId($id);
        
        $vistaInterna = __DIR__ . "/../vistas/admin/editarPaciente.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * Actualiza los datos de un paciente
     */
    public function actualizarPaciente()
    {
        Autenticacion::requiereRoles(['admin']);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarPacientes');
        }

        // Validación básica
        if (empty($_POST['nombre']) || empty($_POST['email'])) {
            $this->setMensaje('error', 'Campos obligatorios incompletos');
            $this->redirigir('editarPaciente', ['id' => $id]);
        }

        try {
            BaseDatos::pdo()->beginTransaction();

            // Actualizar usuario
            $password = !empty($_POST['password']) ? $_POST['password'] : null;
            Usuario::actualizarUsuario(
                (int)$id,
                $_POST['nombre'],
                $_POST['email'],
                $_POST['telefono'] ?? '',
                $password
            );

            // Actualizar datos específicos del paciente
            $paciente = Paciente::buscarPorUsuarioId((int)$id);
            if ($paciente) {
                Paciente::actualizar(
                    $paciente->getId(),
                    $_POST['fecha_nacimiento'] ?? null,
                    $_POST['genero'] ?? '',
                    $_POST['direccion'] ?? '',
                    $_POST['dni'] ?? ''
                );
            }

            BaseDatos::pdo()->commit();
            $this->setMensaje('exito', 'Paciente actualizado exitosamente');
        } catch (Exception $e) {
            BaseDatos::pdo()->rollBack();
            error_log("Error al actualizar paciente: " . $e->getMessage());
            $this->setMensaje('error', 'Error al actualizar el paciente');
        }

        $this->redirigir('gestionarPacientes');
    }

    /**
     * Elimina un paciente del sistema
     */
    public function eliminarPaciente()
    {
        Autenticacion::requiereRoles(['admin']);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarPacientes');
        }

        try {
            Usuario::eliminarPaciente((int)$id);
            $this->setMensaje('exito', 'Paciente eliminado exitosamente');
        } catch (Exception $e) {
            error_log("Error al eliminar paciente: " . $e->getMessage());
            $this->setMensaje('error', 'Error al eliminar el paciente');
        }

        $this->redirigir('gestionarPacientes');
    }
}