<?php

require_once __DIR__ . "/../../nucleo/Autenticacion.php";
require_once __DIR__ . "/../../nucleo/BaseDatos.php";
require_once __DIR__ . "/../modelos/Historia.php";
require_once __DIR__ . "/../modelos/Paciente.php";
require_once __DIR__ . "/../modelos/Doctor.php";
require_once __DIR__ . "/../modelos/Cita.php";

/**
 * HistoriaControlador
 * 
 * Gestión de Historias Clínicas
 * - Admin: CRUD completo
 * - Doctor: Ver sus historias y crear/editar
 * - Paciente: Ver solo sus propias historias
 */
class HistoriaControlador
{
    // ============================================================
    // 🔁 Helpers
    // ============================================================
    private function redirigir($accion, $params = [])
    {
        $query = http_build_query(array_merge(['accion' => $accion], $params));
        header("Location: ?$query");
        exit;
    }

    private function setMensaje($tipo, $texto)
    {
        $_SESSION['mensaje'] = ['tipo' => $tipo, 'texto' => $texto];
    }

    // ============================================================
    // 📋 Gestionar Historias (Admin/Doctor)
    // ============================================================
    public function gestionar()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);

        $usuario = Autenticacion::usuario();
        $rol = Autenticacion::rol();

        // Si es doctor, solo ve sus propias historias
        if ($rol === 'doctor') {
            $historias = $this->obtenerHistoriasPorDoctor($usuario->getId());
        } else {
            // Admin ve todas
            $historias = $this->obtenerTodasLasHistorias();
        }

        $vistaInterna = __DIR__ . "/../vistas/admin/historias.php";
        
        if ($rol === 'admin') {
            require __DIR__ . "/../../includes/layout-admin.php";
        } else {
            require __DIR__ . "/../../includes/layout-doctor.php";
        }
    }

    // ============================================================
    // 📝 Crear Historia Clínica
    // ============================================================
    public function crear()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);

        $pacientes = $this->obtenerPacientesConDatos();
        $doctores = $this->obtenerDoctoresConDatos();
        $citas = $this->obtenerCitasDisponibles();

        $usuario = Autenticacion::usuario();
        $rol = Autenticacion::rol();

        $vistaInterna = __DIR__ . "/../vistas/admin/crearHistoria.php";
        
        if ($rol === 'admin') {
            require __DIR__ . "/../../includes/layout-admin.php";
        } else {
            require __DIR__ . "/../../includes/layout-doctor.php";
        }
    }

    // ============================================================
    // 💾 Guardar Historia Clínica
    // ============================================================
    public function guardar()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);

        // Validación de campos obligatorios
        if (empty($_POST['cita_id']) || empty($_POST['paciente_id']) || 
            empty($_POST['doctor_id']) || empty($_POST['motivo']) || 
            empty($_POST['diagnostico'])) {
            
            $this->setMensaje('error', '❌ Todos los campos obligatorios deben ser completados');
            $this->redirigir('crearHistoria');
        }

        try {
            $pdo = BaseDatos::pdo();

            // Verificar que la cita no tenga ya una historia clínica
            $sqlVerificar = "SELECT id FROM historias_clinicas WHERE cita_id = :cita_id LIMIT 1";
            $stmtVerificar = $pdo->prepare($sqlVerificar);
            $stmtVerificar->execute([':cita_id' => $_POST['cita_id']]);

            if ($stmtVerificar->fetch()) {
                $this->setMensaje('error', '⚠️ Esta cita ya tiene una historia clínica asociada');
                $this->redirigir('crearHistoria');
            }

            // Insertar nueva historia clínica
            $sql = "INSERT INTO historias_clinicas 
                    (cita_id, doctor_id, paciente_id, motivo, diagnostico, indicaciones, observaciones, creada_en)
                    VALUES 
                    (:cita_id, :doctor_id, :paciente_id, :motivo, :diagnostico, :indicaciones, :observaciones, CURRENT_TIMESTAMP)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':cita_id'       => $_POST['cita_id'],
                ':doctor_id'     => $_POST['doctor_id'],
                ':paciente_id'   => $_POST['paciente_id'],
                ':motivo'        => $_POST['motivo'],
                ':diagnostico'   => $_POST['diagnostico'],
                ':indicaciones'  => $_POST['indicaciones'] ?? '',
                ':observaciones' => $_POST['observaciones'] ?? ''
            ]);

            $this->setMensaje('exito', '✅ Historia clínica creada exitosamente');

        } catch (Exception $e) {
            error_log("Error al crear historia clínica: " . $e->getMessage());
            $this->setMensaje('error', '❌ Error al crear historia clínica. Intente nuevamente.');
        }

        $this->redirigir('gestionarHistorias');
    }

    // ============================================================
    // 👁️ Ver Historia Clínica
    // ============================================================
    public function ver()
    {
        Autenticacion::requiereRoles(['admin', 'doctor', 'paciente']);

        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->setMensaje('error', '❌ ID no proporcionado');
            $this->redirigir('gestionarHistorias');
        }

        $historia = $this->obtenerHistoriaPorId($id);

        if (!$historia) {
            $this->setMensaje('error', '❌ Historia clínica no encontrada');
            $this->redirigir('gestionarHistorias');
        }

        // Verificación de permisos
        $usuario = Autenticacion::usuario();
        $rol = Autenticacion::rol();

        if ($rol === 'paciente') {
            // El paciente solo puede ver sus propias historias
            $pacienteId = $this->obtenerPacienteIdPorUsuarioId($usuario->getId());
            
            if ($historia['paciente_id'] != $pacienteId) {
                $this->setMensaje('error', '⛔ No tiene permiso para ver esta historia clínica');
                $this->redirigir('miHistorial');
            }
        } elseif ($rol === 'doctor') {
            // El doctor solo puede ver historias donde él es el doctor
            $doctorId = $this->obtenerDoctorIdPorUsuarioId($usuario->getId());
            
            if ($historia['doctor_id'] != $doctorId) {
                $this->setMensaje('error', '⛔ No tiene permiso para ver esta historia clínica');
                $this->redirigir('gestionarHistorias');
            }
        }

        $vistaInterna = __DIR__ . "/../vistas/admin/verHistoria.php";
        
        if ($rol === 'admin') {
            require __DIR__ . "/../../includes/layout-admin.php";
        } elseif ($rol === 'doctor') {
            require __DIR__ . "/../../includes/layout-doctor.php";
        } else {
            require __DIR__ . "/../../includes/layout-paciente.php";
        }
    }

    // ============================================================
    // ✏️ Editar Historia Clínica
    // ============================================================
    public function editar()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);

        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->setMensaje('error', '❌ ID no proporcionado');
            $this->redirigir('gestionarHistorias');
        }

        $historia = $this->obtenerHistoriaPorId($id);

        if (!$historia) {
            $this->setMensaje('error', '❌ Historia clínica no encontrada');
            $this->redirigir('gestionarHistorias');
        }

        // Verificar permisos para doctor
        $rol = Autenticacion::rol();
        if ($rol === 'doctor') {
            $usuario = Autenticacion::usuario();
            $doctorId = $this->obtenerDoctorIdPorUsuarioId($usuario->getId());
            
            if ($historia['doctor_id'] != $doctorId) {
                $this->setMensaje('error', '⛔ No tiene permiso para editar esta historia clínica');
                $this->redirigir('gestionarHistorias');
            }
        }

        $pacientes = $this->obtenerPacientesConDatos();
        $doctores = $this->obtenerDoctoresConDatos();
        $citas = $this->obtenerCitasDisponibles();

        $vistaInterna = __DIR__ . "/../vistas/admin/editarHistoria.php";
        
        if ($rol === 'admin') {
            require __DIR__ . "/../../includes/layout-admin.php";
        } else {
            require __DIR__ . "/../../includes/layout-doctor.php";
        }
    }

    // ============================================================
    // 🔁 Actualizar Historia Clínica
    // ============================================================
    public function actualizar()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);

        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->setMensaje('error', '❌ ID no proporcionado');
            $this->redirigir('gestionarHistorias');
        }

        if (empty($_POST['motivo']) || empty($_POST['diagnostico'])) {
            $this->setMensaje('error', '❌ Motivo y diagnóstico son obligatorios');
            $this->redirigir('editarHistoria', ['id' => $id]);
        }

        try {
            $pdo = BaseDatos::pdo();

            $sql = "UPDATE historias_clinicas 
                    SET motivo = :motivo,
                        diagnostico = :diagnostico,
                        indicaciones = :indicaciones,
                        observaciones = :observaciones
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id'            => $id,
                ':motivo'        => $_POST['motivo'],
                ':diagnostico'   => $_POST['diagnostico'],
                ':indicaciones'  => $_POST['indicaciones'] ?? '',
                ':observaciones' => $_POST['observaciones'] ?? ''
            ]);

            $this->setMensaje('exito', '✅ Historia clínica actualizada correctamente');

        } catch (Exception $e) {
            error_log("Error al actualizar historia clínica: " . $e->getMessage());
            $this->setMensaje('error', '❌ Error al actualizar historia clínica');
        }

        $this->redirigir('gestionarHistorias');
    }

    // ============================================================
    // 📌 Métodos auxiliares privados
    // ============================================================

    /**
     * Obtener todas las historias clínicas con datos completos
     */
    private function obtenerTodasLasHistorias(): array
    {
        $pdo = BaseDatos::pdo();

        $sql = "
            SELECT 
                h.id,
                h.cita_id,
                h.doctor_id,
                h.paciente_id,
                h.motivo,
                h.diagnostico,
                h.indicaciones,
                h.observaciones,
                h.creada_en,
                up.nombre AS paciente_nombre,
                up.email AS paciente_email,
                ud.nombre AS doctor_nombre,
                e.nombre AS especialidad,
                c.fecha AS cita_fecha,
                c.estado AS cita_estado
            FROM historias_clinicas h
            LEFT JOIN pacientes p ON h.paciente_id = p.id
            LEFT JOIN usuarios up ON p.usuario_id = up.id
            LEFT JOIN doctores d ON h.doctor_id = d.id
            LEFT JOIN usuarios ud ON d.usuario_id = ud.id
            LEFT JOIN especialidades e ON d.especialidad_id = e.id
            LEFT JOIN citas c ON h.cita_id = c.id
            ORDER BY h.creada_en DESC
        ";

        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener historias clínicas de un doctor específico
     */
    private function obtenerHistoriasPorDoctor(int $usuarioId): array
    {
        $pdo = BaseDatos::pdo();

        // Primero obtenemos el doctor_id
        $sqlDoctor = "SELECT id FROM doctores WHERE usuario_id = :usuario_id";
        $stmtDoctor = $pdo->prepare($sqlDoctor);
        $stmtDoctor->execute([':usuario_id' => $usuarioId]);
        $doctor = $stmtDoctor->fetch(PDO::FETCH_ASSOC);

        if (!$doctor) {
            return [];
        }

        $sql = "
            SELECT 
                h.id,
                h.cita_id,
                h.doctor_id,
                h.paciente_id,
                h.motivo,
                h.diagnostico,
                h.indicaciones,
                h.observaciones,
                h.creada_en,
                up.nombre AS paciente_nombre,
                up.email AS paciente_email,
                c.fecha AS cita_fecha,
                c.estado AS cita_estado
            FROM historias_clinicas h
            LEFT JOIN pacientes p ON h.paciente_id = p.id
            LEFT JOIN usuarios up ON p.usuario_id = up.id
            LEFT JOIN citas c ON h.cita_id = c.id
            WHERE h.doctor_id = :doctor_id
            ORDER BY h.creada_en DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':doctor_id' => $doctor['id']]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener historia clínica por ID
     */
    private function obtenerHistoriaPorId(int $id): ?array
    {
        $pdo = BaseDatos::pdo();

        $sql = "
            SELECT 
                h.id,
                h.cita_id,
                h.doctor_id,
                h.paciente_id,
                h.motivo,
                h.diagnostico,
                h.indicaciones,
                h.observaciones,
                h.creada_en,
                up.nombre AS paciente_nombre,
                up.email AS paciente_email,
                up.telefono AS paciente_telefono,
                ud.nombre AS doctor_nombre,
                e.nombre AS especialidad,
                d.numero_colegiatura,
                c.fecha AS cita_fecha,
                c.estado AS cita_estado
            FROM historias_clinicas h
            LEFT JOIN pacientes p ON h.paciente_id = p.id
            LEFT JOIN usuarios up ON p.usuario_id = up.id
            LEFT JOIN doctores d ON h.doctor_id = d.id
            LEFT JOIN usuarios ud ON d.usuario_id = ud.id
            LEFT JOIN especialidades e ON d.especialidad_id = e.id
            LEFT JOIN citas c ON h.cita_id = c.id
            WHERE h.id = :id
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $historia = $stmt->fetch(PDO::FETCH_ASSOC);

        return $historia ?: null;
    }

    /**
     * Obtener pacientes con datos de usuario
     */
    private function obtenerPacientesConDatos(): array
    {
        $pdo = BaseDatos::pdo();

        $sql = "
            SELECT 
                p.id,
                u.nombre,
                u.email,
                u.telefono,
                p.dni
            FROM pacientes p
            INNER JOIN usuarios u ON p.usuario_id = u.id
            ORDER BY u.nombre ASC
        ";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener doctores con datos de usuario
     */
    private function obtenerDoctoresConDatos(): array
    {
        $pdo = BaseDatos::pdo();

        $sql = "
            SELECT 
                d.id,
                u.nombre,
                u.email,
                e.nombre AS especialidad,
                d.numero_colegiatura
            FROM doctores d
            INNER JOIN usuarios u ON d.usuario_id = u.id
            LEFT JOIN especialidades e ON d.especialidad_id = e.id
            ORDER BY u.nombre ASC
        ";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener citas disponibles (completadas sin historia clínica)
     */
    private function obtenerCitasDisponibles(): array
    {
        $pdo = BaseDatos::pdo();

        $sql = "
            SELECT 
                c.id,
                c.paciente_id,
                c.doctor_id,
                c.fecha,
                c.estado,
                up.nombre AS paciente_nombre,
                ud.nombre AS doctor_nombre
            FROM citas c
            LEFT JOIN historias_clinicas h ON c.id = h.cita_id
            LEFT JOIN pacientes p ON c.paciente_id = p.id
            LEFT JOIN usuarios up ON p.usuario_id = up.id
            LEFT JOIN doctores d ON c.doctor_id = d.id
            LEFT JOIN usuarios ud ON d.usuario_id = ud.id
            WHERE h.id IS NULL
            AND LOWER(c.estado) IN ('completada', 'confirmada')
            ORDER BY c.fecha DESC
        ";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener paciente_id a partir de usuario_id
     */
    private function obtenerPacienteIdPorUsuarioId(int $usuarioId): ?int
    {
        $pdo = BaseDatos::pdo();
        $sql = "SELECT id FROM pacientes WHERE usuario_id = :usuario_id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':usuario_id' => $usuarioId]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado ? (int)$resultado['id'] : null;
    }

    /**
     * Obtener doctor_id a partir de usuario_id
     */
    private function obtenerDoctorIdPorUsuarioId(int $usuarioId): ?int
    {
        $pdo = BaseDatos::pdo();
        $sql = "SELECT id FROM doctores WHERE usuario_id = :usuario_id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':usuario_id' => $usuarioId]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado ? (int)$resultado['id'] : null;
    }
}