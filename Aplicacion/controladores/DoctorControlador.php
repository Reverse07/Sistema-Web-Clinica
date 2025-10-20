<?php
require_once __DIR__ . "/../../nucleo/Autenticacion.php";
require_once __DIR__ . "/../../nucleo/BaseDatos.php";
require_once __DIR__ . "/../modelos/Usuario.php";
require_once __DIR__ . "/../modelos/Doctor.php";
require_once __DIR__ . "/../modelos/Especialidad.php";

/**
 * DoctorControlador
 * 
 * Responsabilidad: TODO relacionado con doctores
 * - Vistas del rol doctor (dashboard, perfil, citas, recetas)
 * - CRUD de doctores desde admin
 */
class DoctorControlador
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
    // VISTAS PARA EL ROL DOCTOR
    // ========================================

    /**
     * Dashboard del doctor logueado
     */
    public function dashboard()
    {
        Autenticacion::requiereRoles(['doctor']);
        
        $vistaInterna = __DIR__ . "/../vistas/doctor/inicio.php";
        require __DIR__ . "/../../includes/layout-doctor.php";
    }

    /**
     * Perfil del doctor logueado
     */
    public function perfil()
    {
        Autenticacion::requiereRoles(['doctor']);
        
        $vistaInterna = __DIR__ . "/../vistas/doctor/perfil.php";
        require __DIR__ . "/../../includes/layout-doctor.php";
    }

    /**
     * Citas del doctor logueado
     */
    public function misCitas()
    {
        Autenticacion::requiereRoles(['doctor']);
        
        $vistaInterna = __DIR__ . "/../vistas/doctor/citas.php";
        require __DIR__ . "/../../includes/layout-doctor.php";
    }

    /**
     * Historias clínicas manejadas por el doctor
     */
    public function misHistorias()
    {
        Autenticacion::requiereRoles(['doctor']);
        
        $vistaInterna = __DIR__ . "/../vistas/doctor/historias.php";
        require __DIR__ . "/../../includes/layout-doctor.php";
    }

    /**
     * Recetas emitidas por el doctor
     */
    public function misRecetas()
    {
        Autenticacion::requiereRoles(['doctor']);
        
        $vistaInterna = __DIR__ . "/../vistas/doctor/recetas.php";
        require __DIR__ . "/../../includes/layout-doctor.php";
    }

    // ========================================
    // GESTIÓN ADMIN DE DOCTORES
    // ========================================

    /**
     * Lista todos los doctores (Vista Admin)
     */
    public function gestionarDoctores()
    {
        Autenticacion::requiereRoles(['admin']);
        
        $doctores = Doctor::todosConUsuario();
        $vistaInterna = __DIR__ . "/../vistas/admin/doctores.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * Muestra formulario para crear doctor
     */
    public function crearDoctor()
    {
        Autenticacion::requiereRoles(['admin']);
        
        $especialidades = Especialidad::todas();
        $vistaInterna = __DIR__ . "/../vistas/admin/crearDoctor.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * Guarda un nuevo doctor en la base de datos
     */
    public function guardarDoctor()
    {
        Autenticacion::requiereRoles(['admin']);

        // Validación básica
        if (empty($_POST['nombre']) || empty($_POST['email']) || empty($_POST['password'])) {
            $this->setMensaje('error', 'Campos obligatorios incompletos');
            $_SESSION['datos_formulario'] = $_POST;
            $this->redirigir('crearDoctor');
        }

        // Validar email
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $this->setMensaje('error', 'Email inválido');
            $_SESSION['datos_formulario'] = $_POST;
            $this->redirigir('crearDoctor');
        }

        try {
            BaseDatos::pdo()->beginTransaction();

            // Crear usuario
            $usuario = new Usuario([
                'nombre'   => $_POST['nombre'],
                'email'    => $_POST['email'],
                'telefono' => $_POST['telefono'] ?? '',
                'password' => $_POST['password'],
                'rol_id'   => 2
            ]);
            $usuario->crearUsuario();
            $usuarioId = BaseDatos::pdo()->lastInsertId();

            // Crear registro de doctor
            Doctor::crear(
                $usuarioId,
                $_POST['especialidad_id'] ?? 0,
                $_POST['numero_colegiatura'] ?? ''
            );

            BaseDatos::pdo()->commit();
            $this->setMensaje('exito', 'Doctor creado exitosamente');
        } catch (Exception $e) {
            BaseDatos::pdo()->rollBack();
            error_log("Error al crear doctor: " . $e->getMessage());
            $this->setMensaje('error', 'Error al crear el doctor. Intente nuevamente.');
        }

        $this->redirigir('gestionarDoctores');
    }

    /**
     * Muestra formulario para editar doctor
     */
    public function editarDoctor()
    {
        Autenticacion::requiereRoles(['admin']);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setMensaje('error', 'ID de doctor no proporcionado');
            $this->redirigir('gestionarDoctores');
        }

        $doctor = Usuario::buscarPorId($id);
        if (!$doctor || $doctor->getRolId() != 2) {
            $this->setMensaje('error', 'Doctor no encontrado');
            $this->redirigir('gestionarDoctores');
        }

        $especialidades = Especialidad::todas();
        $doctorData = Doctor::buscarPorUsuarioId($id);
        
        $vistaInterna = __DIR__ . "/../vistas/admin/editarDoctor.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * Actualiza los datos de un doctor
     */
    public function actualizarDoctor()
    {
        Autenticacion::requiereRoles(['admin']);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarDoctores');
        }

        // Validación básica
        if (empty($_POST['nombre']) || empty($_POST['email'])) {
            $this->setMensaje('error', 'Campos obligatorios incompletos');
            $this->redirigir('editarDoctor', ['id' => $id]);
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

            // Actualizar datos específicos del doctor
            $doctor = Doctor::buscarPorUsuarioId((int)$id);
            if ($doctor) {
                Doctor::actualizar(
                    $doctor->getId(),
                    $_POST['especialidad_id'] ?? 0,
                    $_POST['numero_colegiatura'] ?? ''
                );
            }

            BaseDatos::pdo()->commit();
            $this->setMensaje('exito', 'Doctor actualizado exitosamente');
        } catch (Exception $e) {
            BaseDatos::pdo()->rollBack();
            error_log("Error al actualizar doctor: " . $e->getMessage());
            $this->setMensaje('error', 'Error al actualizar el doctor');
        }

        $this->redirigir('gestionarDoctores');
    }

    /**
     * Elimina un doctor del sistema
     */
    public function eliminarDoctor()
    {
        Autenticacion::requiereRoles(['admin']);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarDoctores');
        }

        try {
            Usuario::eliminarDoctor((int)$id);
            $this->setMensaje('exito', 'Doctor eliminado exitosamente');
        } catch (Exception $e) {
            error_log("Error al eliminar doctor: " . $e->getMessage());
            $this->setMensaje('error', 'Error al eliminar el doctor');
        }

        $this->redirigir('gestionarDoctores');
    }
}