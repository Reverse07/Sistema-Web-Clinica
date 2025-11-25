<?php
require_once __DIR__ . "/../../nucleo/Autenticacion.php";
require_once __DIR__ . "/../../nucleo/BaseDatos.php";
require_once __DIR__ . "/../modelos/Usuario.php";
require_once __DIR__ . "/../modelos/Paciente.php";
require_once __DIR__ . "/../modelos/Cita.php";
require_once __DIR__ . "/../modelos/Factura.php";

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
     * 🏠 Dashboard del paciente logueado (MEJORADO)
     */
    public function dashboard()
    {
        Autenticacion::requiereRoles(['paciente']);
        
        try {
            $pdo = BaseDatos::pdo();
            $usuarioId = Autenticacion::usuarioId();
            
            // Obtener ID del paciente
            $pacienteId = $this->obtenerPacienteId($usuarioId);
            
            if (!$pacienteId) {
                throw new Exception('No se encontró el registro de paciente');
            }
            
            // Estadísticas para el dashboard
            $totalCitas = $this->contarCitasPaciente($pacienteId);
            $citasPendientes = $this->contarCitasPorEstado($pacienteId, 'Pendiente');
            $citasCompletadas = $this->contarCitasPorEstado($pacienteId, 'Confirmada');
            $facturasPorPagar = $this->contarFacturasPendientes($pacienteId);
            
            // Próximas citas (5 más próximas)
            $proximasCitas = $this->obtenerProximasCitas($pacienteId, 5);
            
            // Historial de citas (últimas 10)
            $historialCitas = $this->obtenerHistorialCitas($pacienteId, 10);
            
            // Facturas pendientes
            $facturasPendientes = $this->obtenerFacturasPendientes($pacienteId);
            
            // Cargar vista del dashboard premium
            $vistaInterna = __DIR__ . "/../vistas/paciente/inicio.php";
            require __DIR__ . "/../../includes/layout-paciente.php";
            
        } catch (Exception $e) {
            error_log("❌ Error en PacienteControlador::dashboard: " . $e->getMessage());
            $this->setMensaje('error', 'Error al cargar el dashboard');
            $this->redirigir('loginVista');
        }
    }

    /**
     * 👤 Perfil del paciente logueado
     */
    public function perfil()
{
    Autenticacion::requiereRoles(['paciente']);
    
    $usuarioId = Autenticacion::usuarioId();
    $paciente = $this->obtenerDatosPaciente($usuarioId);
    
    $vistaInterna = __DIR__ . "/../vistas/paciente/perfil.php";
    require __DIR__ . "/../../includes/layout-paciente.php";
}


    /**
     * 📅 Citas del paciente logueado
     */
    public function misCitas()
    {
        Autenticacion::requiereRoles(['paciente']);
        
        try {
            $usuarioId = Autenticacion::usuarioId();
            $pacienteId = $this->obtenerPacienteId($usuarioId);
            
            // Obtener todas las citas del paciente
            $citas = $this->obtenerTodasLasCitasConDatos($pacienteId);
            
            // Estadísticas para la vista
            $totalCitas = count($citas);
            $citasPendientes = 0;
            $citasConfirmadas = 0;
            $citasCanceladas = 0;
            
            foreach ($citas as $cita) {
                $estado = strtolower($cita['estado']);
                if (str_contains($estado, 'pendiente')) {
                    $citasPendientes++;
                } elseif (str_contains($estado, 'confirmada')) {
                    $citasConfirmadas++;
                } elseif (str_contains($estado, 'cancelada')) {
                    $citasCanceladas++;
                }
            }
            
            $vistaInterna = __DIR__ . "/../vistas/paciente/citas.php";
            require __DIR__ . "/../../includes/layout-paciente.php";
            
        } catch (Exception $e) {
            error_log("❌ Error en misCitas: " . $e->getMessage());
            $this->setMensaje('error', 'Error al cargar las citas');
            $this->redirigir('dashboardPaciente');
        }
    }

    /**
     * 📁 Historial médico del paciente
     */
    public function miHistorial()
    {
        Autenticacion::requiereRoles(['paciente']);
        
        try {
            $usuarioId = Autenticacion::usuarioId();
            $pacienteId = $this->obtenerPacienteId($usuarioId);
            
            $historial = $this->obtenerHistorialCompleto($pacienteId);
            
            $vistaInterna = __DIR__ . "/../vistas/paciente/historial.php";
            require __DIR__ . "/../../includes/layout-paciente.php";
            
        } catch (Exception $e) {
            error_log("❌ Error en miHistorial: " . $e->getMessage());
            $this->setMensaje('error', 'Error al cargar el historial');
            $this->redirigir('dashboardPaciente');
        }
    }

    /**
     * 💳 Facturas del paciente
     */
    public function misFacturas()
    {
        Autenticacion::requiereRoles(['paciente']);
        
        try {
            $usuarioId = Autenticacion::usuarioId();
            $pacienteId = $this->obtenerPacienteId($usuarioId);
            
            $facturas = Factura::obtenerPorPaciente($pacienteId);
            
            // Estadísticas
            $totalFacturas = count($facturas);
            $totalPagado = 0;
            $totalPendiente = 0;
            
            foreach ($facturas as $factura) {
                if (str_contains(strtolower($factura->getEstado()), 'pag')) {
                    $totalPagado += $factura->getMonto();
                } else {
                    $totalPendiente += $factura->getMonto();
                }
            }
            
            $vistaInterna = __DIR__ . "/../vistas/paciente/facturas.php";
            require __DIR__ . "/../../includes/layout-paciente.php";
            
        } catch (Exception $e) {
            error_log("❌ Error en misFacturas: " . $e->getMessage());
            $this->setMensaje('error', 'Error al cargar las facturas');
            $this->redirigir('dashboardPaciente');
        }
    }

    // ========================================
    // MÉTODOS AUXILIARES PRIVADOS
    // ========================================

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

    /**
     * Cuenta total de citas del paciente
     */
    private function contarCitasPaciente(int $pacienteId): int
    {
        try {
            $pdo = BaseDatos::pdo();
            $sql = "SELECT COUNT(*) FROM citas WHERE paciente_id = :paciente_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':paciente_id' => $pacienteId]);
            return (int)$stmt->fetchColumn();
        } catch (Exception $e) {
            error_log("❌ Error contando citas: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Cuenta citas por estado
     */
    private function contarCitasPorEstado(int $pacienteId, string $estado): int
    {
        try {
            $pdo = BaseDatos::pdo();
            $sql = "SELECT COUNT(*) FROM citas 
                    WHERE paciente_id = :paciente_id 
                    AND LOWER(estado) LIKE LOWER(:estado)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':paciente_id' => $pacienteId,
                ':estado' => "%$estado%"
            ]);
            return (int)$stmt->fetchColumn();
        } catch (Exception $e) {
            error_log("❌ Error contando por estado: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Cuenta facturas pendientes
     */
    private function contarFacturasPendientes(int $pacienteId): int
    {
        try {
            $pdo = BaseDatos::pdo();
            $sql = "SELECT COUNT(*) FROM facturas 
                    WHERE paciente_id = :paciente_id 
                    AND LOWER(estado) IN ('pendiente', 'por pagar')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':paciente_id' => $pacienteId]);
            return (int)$stmt->fetchColumn();
        } catch (Exception $e) {
            error_log("❌ Error contando facturas pendientes: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtiene próximas citas del paciente
     */
    private function obtenerProximasCitas(int $pacienteId, int $limite = 5): array
    {
        try {
            $pdo = BaseDatos::pdo();
            $sql = "
                SELECT 
                    c.id,
                    c.fecha,
                    c.estado,
                    u.nombre as doctor_nombre,
                    e.nombre as especialidad,
                    '1' as consultorio
                FROM citas c
                LEFT JOIN doctores d ON c.doctor_id = d.id
                LEFT JOIN usuarios u ON d.usuario_id = u.id
                LEFT JOIN especialidades e ON d.especialidad_id = e.id
                WHERE c.paciente_id = :paciente_id
                AND c.fecha >= CURRENT_DATE
                AND LOWER(c.estado) NOT IN ('cancelada', 'cancelado')
                ORDER BY c.fecha ASC
                LIMIT :limite
            ";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':paciente_id', $pacienteId, PDO::PARAM_INT);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("❌ Error obteniendo próximas citas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene historial de citas
     */
    private function obtenerHistorialCitas(int $pacienteId, int $limite = 10): array
    {
        try {
            $pdo = BaseDatos::pdo();
            $sql = "
                SELECT 
                    c.id,
                    c.fecha,
                    c.estado,
                    u.nombre as doctor_nombre,
                    e.nombre as especialidad
                FROM citas c
                LEFT JOIN doctores d ON c.doctor_id = d.id
                LEFT JOIN usuarios u ON d.usuario_id = u.id
                LEFT JOIN especialidades e ON d.especialidad_id = e.id
                WHERE c.paciente_id = :paciente_id
                ORDER BY c.fecha DESC
                LIMIT :limite
            ";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':paciente_id', $pacienteId, PDO::PARAM_INT);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("❌ Error obteniendo historial: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene todas las citas con datos completos
     */
    private function obtenerTodasLasCitasConDatos(int $pacienteId): array
    {
        try {
            $pdo = BaseDatos::pdo();
            $sql = "
                SELECT 
                    c.id,
                    c.fecha,
                    c.estado,
                    u.nombre as doctor_nombre,
                    e.nombre as especialidad,
                    c.doctor_id,
                    c.paciente_id
                FROM citas c
                LEFT JOIN doctores d ON c.doctor_id = d.id
                LEFT JOIN usuarios u ON d.usuario_id = u.id
                LEFT JOIN especialidades e ON d.especialidad_id = e.id
                WHERE c.paciente_id = :paciente_id
                ORDER BY c.fecha DESC
            ";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':paciente_id' => $pacienteId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("❌ Error obteniendo todas las citas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene facturas pendientes
     */
    private function obtenerFacturasPendientes(int $pacienteId): array
    {
        try {
            return Factura::obtenerPorPaciente($pacienteId);
        } catch (Exception $e) {
            error_log("❌ Error obteniendo facturas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene historial médico completo
     */
    private function obtenerHistorialCompleto(int $pacienteId): array
    {
        try {
            $pdo = BaseDatos::pdo();
            $sql = "
                SELECT 
                    c.id,
                    c.fecha,
                    u.nombre as doctor_nombre,
                    e.nombre as especialidad,
                    c.estado
                FROM citas c
                LEFT JOIN doctores d ON c.doctor_id = d.id
                LEFT JOIN usuarios u ON d.usuario_id = u.id
                LEFT JOIN especialidades e ON d.especialidad_id = e.id
                WHERE c.paciente_id = :paciente_id
                ORDER BY c.fecha DESC
            ";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':paciente_id' => $pacienteId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("❌ Error obteniendo historial completo: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene datos completos del paciente
     */
    private function obtenerDatosPaciente(int $usuarioId): ?array
    {
        try {
            $pdo = BaseDatos::pdo();
            $sql = "
                SELECT 
                    u.id,
                    u.nombre,
                    u.email,
                    u.telefono,
                    p.id as paciente_id,
                    p.fecha_nacimiento,
                    p.direccion,
                    p.genero,
                    p.dni
                FROM usuarios u
                INNER JOIN pacientes p ON u.id = p.usuario_id
                WHERE u.id = :usuario_id
            ";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':usuario_id' => $usuarioId]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Exception $e) {
            error_log("❌ Error obteniendo datos paciente: " . $e->getMessage());
            return null;
        }
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
            $this->setMensaje('exito', 'Paciente creado exitosamente ✅');
        } catch (Exception $e) {
            BaseDatos::pdo()->rollBack();
            error_log("❌ Error al crear paciente: " . $e->getMessage());
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
            $this->setMensaje('exito', 'Paciente actualizado exitosamente ✅');
        } catch (Exception $e) {
            BaseDatos::pdo()->rollBack();
            error_log("❌ Error al actualizar paciente: " . $e->getMessage());
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
            $this->setMensaje('exito', 'Paciente eliminado exitosamente 🗑️');
        } catch (Exception $e) {
            error_log("❌ Error al eliminar paciente: " . $e->getMessage());
            $this->setMensaje('error', 'Error al eliminar el paciente');
        }

        $this->redirigir('gestionarPacientes');
    }


// ============================================================
// 📌 Métodos auxiliares para historial médico
// ============================================================

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
 * Obtener historial médico completo del paciente
 * Con datos del doctor, especialidad y cita
 */
private function obtenerHistorialMedicoPorPaciente(int $pacienteId): array
{
    $pdo = BaseDatos::pdo();

    $sql = "
        SELECT 
            h.id,
            h.cita_id,
            h.motivo,
            h.diagnostico,
            h.indicaciones,
            h.observaciones,
            h.creada_en,
            ud.nombre AS doctor_nombre,
            e.nombre AS especialidad,
            d.numero_colegiatura,
            c.fecha AS cita_fecha,
            c.estado AS cita_estado
        FROM historias_clinicas h
        LEFT JOIN citas c ON h.cita_id = c.id
        LEFT JOIN doctores d ON h.doctor_id = d.id
        LEFT JOIN usuarios ud ON d.usuario_id = ud.id
        LEFT JOIN especialidades e ON d.especialidad_id = e.id
        WHERE h.paciente_id = :paciente_id
        ORDER BY h.creada_en DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':paciente_id' => $pacienteId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtener estadísticas del historial médico
 * Total de consultas, especialidades visitadas, etc.
 */
private function obtenerEstadisticasHistorial(int $pacienteId): array
{
    $pdo = BaseDatos::pdo();

    // Total de historias clínicas
    $sqlTotal = "SELECT COUNT(*) as total FROM historias_clinicas WHERE paciente_id = :paciente_id";
    $stmtTotal = $pdo->prepare($sqlTotal);
    $stmtTotal->execute([':paciente_id' => $pacienteId]);
    $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

    // Especialidades más visitadas
    $sqlEspecialidades = "
        SELECT 
            e.nombre AS especialidad,
            COUNT(*) as cantidad
        FROM historias_clinicas h
        LEFT JOIN doctores d ON h.doctor_id = d.id
        LEFT JOIN especialidades e ON d.especialidad_id = e.id
        WHERE h.paciente_id = :paciente_id
        GROUP BY e.id, e.nombre
        ORDER BY cantidad DESC
        LIMIT 5
    ";
    $stmtEspecialidades = $pdo->prepare($sqlEspecialidades);
    $stmtEspecialidades->execute([':paciente_id' => $pacienteId]);
    $especialidades = $stmtEspecialidades->fetchAll(PDO::FETCH_ASSOC);

    // Última consulta
    $sqlUltima = "
        SELECT 
            h.creada_en,
            ud.nombre AS doctor_nombre,
            e.nombre AS especialidad
        FROM historias_clinicas h
        LEFT JOIN doctores d ON h.doctor_id = d.id
        LEFT JOIN usuarios ud ON d.usuario_id = ud.id
        LEFT JOIN especialidades e ON d.especialidad_id = e.id
        WHERE h.paciente_id = :paciente_id
        ORDER BY h.creada_en DESC
        LIMIT 1
    ";
    $stmtUltima = $pdo->prepare($sqlUltima);
    $stmtUltima->execute([':paciente_id' => $pacienteId]);
    $ultimaConsulta = $stmtUltima->fetch(PDO::FETCH_ASSOC);

    return [
        'total' => $total,
        'especialidades' => $especialidades,
        'ultima_consulta' => $ultimaConsulta
    ];
}

// ============================================================
// 👁️ Ver historia clínica específica (paciente)
// ============================================================

/**
 * Permite al paciente ver una historia clínica específica
 * Solo si pertenece a él
 */
public function verHistoria()
{
    Autenticacion::requiereRoles(['paciente']);

    $id = $_GET['id'] ?? null;

    if (!$id) {
        $this->setMensaje('error', '❌ ID no proporcionado');
        $this->redirigir('miHistorial');
    }

    $usuario = Autenticacion::usuario();
    $pacienteId = $this->obtenerPacienteIdPorUsuarioId($usuario->getId());

    // Obtener la historia clínica
    $historia = $this->obtenerHistoriaPorId($id);

    if (!$historia) {
        $this->setMensaje('error', '❌ Historia clínica no encontrada');
        $this->redirigir('miHistorial');
    }

    // Verificar que la historia pertenezca al paciente
    if ($historia['paciente_id'] != $pacienteId) {
        $this->setMensaje('error', '⛔ No tiene permiso para ver esta historia clínica');
        $this->redirigir('miHistorial');
    }

    $vistaInterna = __DIR__ . "/../vistas/paciente/verHistoria.php";
    require __DIR__ . "/../../includes/layout-paciente.php";
}

/**
 * Obtener historia clínica por ID con todos los datos
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
            ud.nombre AS doctor_nombre,
            ud.email AS doctor_email,
            ud.telefono AS doctor_telefono,
            e.nombre AS especialidad,
            d.numero_colegiatura,
            c.fecha AS cita_fecha,
            c.estado AS cita_estado
        FROM historias_clinicas h
        LEFT JOIN citas c ON h.cita_id = c.id
        LEFT JOIN doctores d ON h.doctor_id = d.id
        LEFT JOIN usuarios ud ON d.usuario_id = ud.id
        LEFT JOIN especialidades e ON d.especialidad_id = e.id
        WHERE h.id = :id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $historia = $stmt->fetch(PDO::FETCH_ASSOC);

    return $historia ?: null;
}

// ============================================================
// 📥 Descargar/Imprimir historia clínica (OPCIONAL)
// ============================================================

/**
 * Genera PDF del historial médico completo
 * Requiere librería TCPDF o similar
 */
public function descargarHistorial()
{
    Autenticacion::requiereRoles(['paciente']);

    $usuario = Autenticacion::usuario();
    $pacienteId = $this->obtenerPacienteIdPorUsuarioId($usuario->getId());

    if (!$pacienteId) {
        $this->setMensaje('error', '❌ Error al obtener datos del paciente');
        $this->redirigir('miHistorial');
    }

    // TODO: Implementar generación de PDF con TCPDF
    // Por ahora, redirigir con mensaje
    $this->setMensaje('info', 'ℹ️ Funcionalidad de descarga en desarrollo');
    $this->redirigir('miHistorial');
}

/**
 * Imprimir una historia clínica específica
 */
public function imprimirHistoria()
{
    Autenticacion::requiereRoles(['paciente']);

    $id = $_GET['id'] ?? null;

    if (!$id) {
        $this->setMensaje('error', '❌ ID no proporcionado');
        $this->redirigir('miHistorial');
    }

    $usuario = Autenticacion::usuario();
    $pacienteId = $this->obtenerPacienteIdPorUsuarioId($usuario->getId());

    $historia = $this->obtenerHistoriaPorId($id);

    if (!$historia || $historia['paciente_id'] != $pacienteId) {
        $this->setMensaje('error', '⛔ No tiene permiso para imprimir esta historia');
        $this->redirigir('miHistorial');
    }

    // TODO: Implementar vista de impresión optimizada
    $this->setMensaje('info', 'ℹ️ Funcionalidad de impresión en desarrollo');
    $this->redirigir('verHistoria', ['id' => $id]);
}

/**
 * Descarga el historial médico en PDF
 */
public function descargarHistoria()
{
    Autenticacion::requiereRoles(['paciente']);
    
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        $this->setMensaje('error', 'ID no proporcionado');
        $this->redirigir('historialMedico');
    }

    // Aquí implementar la generación de PDF
    // Por ahora redirige a ver
    $this->redirigir('verHistoria', ['id' => $id]);
}

/**
 * Muestra configuración del paciente
 */
public function configuracion()
{
    Autenticacion::requiereRoles(['paciente']);
    
    $vistaInterna = __DIR__ . "/../vistas/paciente/configuracion.php";
    require __DIR__ . "/../../includes/layout-paciente.php";
}

/**
 * Guarda notificaciones del paciente
 */
public function guardarNotificacionesPaciente()
{
    Autenticacion::requiereRoles(['paciente']);
    
    // Guardar en la sesión o BD (según tu implementación)
    $_SESSION['notif_citas'] = isset($_POST['notif_citas']);
    $_SESSION['notif_confirmacion'] = isset($_POST['notif_confirmacion']);
    $_SESSION['notif_resultados'] = isset($_POST['notif_resultados']);
    $_SESSION['notif_facturas'] = isset($_POST['notif_facturas']);
    $_SESSION['notif_promociones'] = isset($_POST['notif_promociones']);
    
    $_SESSION['mensaje'] = ['tipo' => 'exito', 'texto' => '✅ Configuración guardada exitosamente'];
    header("Location: " . BASE_URL . "/index.php?accion=configuracionPaciente");
    exit;
}

/**
 * Guarda privacidad del paciente
 */
public function guardarPrivacidadPaciente()
{
    Autenticacion::requiereRoles(['paciente']);
    
    $_SESSION['compartir_historial'] = isset($_POST['compartir_historial']);
    $_SESSION['recordar_sesion'] = isset($_POST['recordar_sesion']);
    
    $_SESSION['mensaje'] = ['tipo' => 'exito', 'texto' => '🔒 Configuración de privacidad guardada'];
    header("Location: " . BASE_URL . "/index.php?accion=configuracionPaciente");
    exit;
}



}