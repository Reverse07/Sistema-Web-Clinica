<?php
require_once __DIR__ . "/../../nucleo/Autenticacion.php";
require_once __DIR__ . "/../../nucleo/BaseDatos.php";
require_once __DIR__ . "/../modelos/Cita.php";
require_once __DIR__ . "/../modelos/Paciente.php";
require_once __DIR__ . "/../modelos/Doctor.php";

/**
 * CitaControlador
 * 
 * Responsabilidad:
 * - CRUD de citas desde admin y doctor
 * - Gestión de citas desde rol paciente
 */
class CitaControlador
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

    // ===========================================================
    // 📅 VISTAS PARA PACIENTE
    // ===========================================================

    /**
     * Lista citas del paciente autenticado
     */
    public function misCitas()
    {
        Autenticacion::requiereRoles(['paciente']);
        $usuarioId = Autenticacion::usuarioId();

        $citas = Cita::obtenerPorPaciente($usuarioId);
        $vistaInterna = __DIR__ . "/../vistas/paciente/misCitas.php";
        require __DIR__ . "/../../includes/layout-paciente.php";
    }

    /**
     * Formulario para crear cita (Paciente)
     */
    public function crearCitaPaciente()
    {
         Autenticacion::requiereRoles(['paciente']);
    
    // Obtener doctores con datos completos (nombre, especialidad, email)
    $doctores = $this->obtenerDoctoresDisponibles();

    $vistaInterna = __DIR__ . "/../vistas/paciente/crearCita.php";
    require __DIR__ . "/../../includes/layout-paciente.php";
}


/**
 * Obtiene lista de doctores disponibles con sus datos completos
 */
private function obtenerDoctoresDisponibles(): array
{
    try {
        $pdo = BaseDatos::pdo();
        
        $sql = "
            SELECT 
                d.id,
                u.nombre,
                u.email,
                u.telefono,
                e.nombre as especialidad,
                d.numero_colegiatura
            FROM doctores d
            INNER JOIN usuarios u ON d.usuario_id = u.id
            LEFT JOIN especialidades e ON d.especialidad_id = e.id
            WHERE u.rol_id = 2
            ORDER BY u.nombre ASC
        ";
        
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        error_log("❌ Error obteniendo doctores disponibles: " . $e->getMessage());
        return [];
    }
 }

    /**
     * Guarda nueva cita creada por paciente
     */
    public function guardarCitaPaciente()
    {
       Autenticacion::requiereRoles(['paciente']);

    if (empty($_POST['doctor_id']) || empty($_POST['fecha'])) {
        $this->setMensaje('error', 'Todos los campos son obligatorios');
        $this->redirigir('crearCitaPaciente');
    }

    try {
        $usuarioId = Autenticacion::usuarioId();
        
        // Obtener el ID del paciente desde la tabla pacientes
        $pacienteId = $this->obtenerPacienteId($usuarioId);
        
        if (!$pacienteId) {
            throw new Exception('No se encontró el registro de paciente');
        }

        $cita = new Cita(
            null,
            $pacienteId,  // Usar el ID de la tabla pacientes
            $_POST['doctor_id'],
            $_POST['fecha'],
            'Pendiente'
        );
        $cita->crear();

        $this->setMensaje('exito', 'Cita creada correctamente ✅');
    } catch (Exception $e) {
        error_log("❌ Error al crear cita: " . $e->getMessage());
        $this->setMensaje('error', 'Error al crear cita. Intente nuevamente.');
    }

    $this->redirigir('misCitas');
    }

    /**
 * Obtiene el ID del paciente desde el usuario_id
 */
private function obtenerPacienteId(int $usuarioId): ?int
{
    try {
        $pdo = BaseDatos::pdo();
        $sql = "SELECT id FROM pacientes WHERE usuario_id = :usuario_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':usuario_id' => $usuarioId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? (int)$result['id'] : null;
    } catch (Exception $e) {
        error_log("❌ Error en obtenerPacienteId: " . $e->getMessage());
        return null;
    }
}


    // ===========================================================
    // 🩺 GESTIÓN ADMIN / DOCTOR
    // ===========================================================

    /**
     * Alias para compatibilidad con el enrutador
     */
    public function gestionar()
    {
        $this->gestionarCitas();
    }

    /**
     * Lista todas las citas con estadísticas (Admin / Doctor)
     */
    public function gestionarCitas()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);
        
        // Obtener todas las citas con datos completos
        $citas = $this->obtenerTodasLasCitasConDatos();
        
        // Estadísticas
        $totalCitas = count($citas);
        $citasConfirmadas = $this->contarPorEstado('Confirmada');
        $citasPendientes = $this->contarPorEstado('Pendiente');
        $citasCanceladas = $this->contarPorEstado('Cancelada');

        $vistaInterna = __DIR__ . "/../vistas/admin/citas.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * Obtiene todas las citas con datos de paciente y doctor
     */
    private function obtenerTodasLasCitasConDatos(): array
    {
        $pdo = BaseDatos::pdo();
        
        $sql = "
            SELECT 
                c.id,
                c.fecha,
                c.estado,
                c.paciente_id,
                c.doctor_id,
                CONCAT(up.nombre) as paciente_nombre,
                up.email as paciente_email,
                CONCAT(ud.nombre) as doctor_nombre,
                e.nombre as especialidad
            FROM citas c
            LEFT JOIN pacientes p ON c.paciente_id = p.id
            LEFT JOIN usuarios up ON p.usuario_id = up.id
            LEFT JOIN doctores d ON c.doctor_id = d.id
            LEFT JOIN usuarios ud ON d.usuario_id = ud.id
            LEFT JOIN especialidades e ON d.especialidad_id = e.id
            ORDER BY c.fecha DESC
        ";
        
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta citas por estado
     */
    private function contarPorEstado(string $estado): int
    {
        $pdo = BaseDatos::pdo();
        
        $sql = "SELECT COUNT(*) FROM citas WHERE LOWER(estado) LIKE LOWER(:estado)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':estado' => "%$estado%"]);
        
        return (int) $stmt->fetchColumn();
    }
    /**
     * Muestra formulario para crear nueva cita (Admin)
     */

    public function crear()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);
        
        // Obtener pacientes con datos de usuario
        $pacientes = $this->obtenerPacientesConDatos();
        
        // Obtener doctores con datos de usuario
        $doctores = $this->obtenerDoctoresConDatos();

        $vistaInterna = __DIR__ . "/../vistas/admin/crearCita.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    private function obtenerPacientesConDatos(): array
    {
        $pdo = BaseDatos::pdo();
        
        $sql = "
            SELECT 
                p.id,
                u.nombre,
                u.email,
                u.telefono
            FROM pacientes p
            INNER JOIN usuarios u ON p.usuario_id = u.id
            ORDER BY u.nombre ASC
        ";
        
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene lista de doctores con datos de usuario y especialidad
     */
    private function obtenerDoctoresConDatos(): array
    {
        $pdo = BaseDatos::pdo();
        
        $sql = "
            SELECT 
                d.id,
                u.nombre,
                u.email,
                e.nombre as especialidad
            FROM doctores d
            INNER JOIN usuarios u ON d.usuario_id = u.id
            LEFT JOIN especialidades e ON d.especialidad_id = e.id
            ORDER BY u.nombre ASC
        ";
        
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Guarda nueva cita (Admin)
     */
    public function guardar()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);

        if (empty($_POST['paciente_id']) || empty($_POST['doctor_id']) || empty($_POST['fecha'])) {
            $this->setMensaje('error', 'Todos los campos obligatorios deben ser completados');
            $this->redirigir('crear');
        }

        try {
            $cita = new Cita(
                null,
                $_POST['paciente_id'],
                $_POST['doctor_id'],
                $_POST['fecha'],
                $_POST['estado'] ?? 'Pendiente'
            );
            $cita->crear();

            $this->setMensaje('exito', 'Cita creada correctamente ✅');
        } catch (Exception $e) {
            error_log("Error al crear cita: " . $e->getMessage());
            $this->setMensaje('error', 'Error al crear cita. Intente nuevamente.');
        }

        $this->redirigir('gestionarCitas');
    }

     /**
     * Muestra formulario de edición
     */
    public function editar()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarCitas');
        }

        $cita = Cita::obtenerPorId($id);
        if (!$cita) {
            $this->setMensaje('error', 'Cita no encontrada');
            $this->redirigir('gestionarCitas');
        }

        // Obtener pacientes y doctores con datos completos
        $pacientes = $this->obtenerPacientesConDatos();
        $doctores = $this->obtenerDoctoresConDatos();

        $vistaInterna = __DIR__ . "/../vistas/admin/editarCita.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }   

    /**
     * Alias para compatibilidad
     */
    public function editarCita()
    {
        $this->editar();
    }

    /**
     * Actualiza una cita
     */
    public function actualizar()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);
        $id = $_GET['id'] ?? $_POST['id'] ?? null;

        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarCitas');
        }

        if (empty($_POST['paciente_id']) || empty($_POST['doctor_id']) || empty($_POST['fecha']) || empty($_POST['estado'])) {
            $this->setMensaje('error', 'Todos los campos son obligatorios');
            $this->redirigir('editar', ['id' => $id]);
        }

        try {
            $cita = new Cita(
                (int)$id,
                $_POST['paciente_id'],
                $_POST['doctor_id'],
                $_POST['fecha'],
                $_POST['estado']
            );
            $cita->actualizar();
            $this->setMensaje('exito', 'Cita actualizada correctamente ✅');
        } catch (Exception $e) {
            error_log("Error al actualizar cita: " . $e->getMessage());
            $this->setMensaje('error', 'Error al actualizar cita');
        }

        $this->redirigir('gestionarCitas');
    }

    /**
     * Alias para compatibilidad
     */
    public function actualizarCita()
    {
        $this->actualizar();
    }

    /**
     * Confirma una cita (cambia estado a Confirmada)
     */
    public function confirmar()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarCitas');
        }

        try {
            $pdo = BaseDatos::pdo();
            $stmt = $pdo->prepare("UPDATE citas SET estado = 'Confirmada' WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            $this->setMensaje('exito', 'Cita confirmada exitosamente ✅');
        } catch (Exception $e) {
            error_log("Error al confirmar cita: " . $e->getMessage());
            $this->setMensaje('error', 'Error al confirmar cita');
        }

        $this->redirigir('gestionarCitas');
    }

    /**
     * Cancela una cita (cambia estado a Cancelada)
     */
    public function cancelar()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarCitas');
        }

        try {
            $pdo = BaseDatos::pdo();
            $stmt = $pdo->prepare("UPDATE citas SET estado = 'Cancelada' WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            $this->setMensaje('exito', 'Cita cancelada exitosamente 🗑️');
        } catch (Exception $e) {
            error_log("Error al cancelar cita: " . $e->getMessage());
            $this->setMensaje('error', 'Error al cancelar cita');
        }

        $this->redirigir('gestionarCitas');
    }

    /**
     * Elimina una cita
     */
    public function eliminarCita()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarCitas');
        }

        try {
            Cita::eliminar((int)$id);
            $this->setMensaje('exito', 'Cita eliminada exitosamente 🗑️');
        } catch (Exception $e) {
            error_log("Error al eliminar cita: " . $e->getMessage());
            $this->setMensaje('error', 'Error al eliminar cita');
        }

        $this->redirigir('gestionarCitas');
    }
}