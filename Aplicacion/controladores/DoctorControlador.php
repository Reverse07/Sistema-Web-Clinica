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
     * 🏠 Dashboard principal del doctor
     */
    public function dashboard()
    {
        // 🔐 Verificar autenticación
        Autenticacion::requiereRoles(['doctor']);
        
        $usuarioId = Autenticacion::usuarioId();
        $pdo = BaseDatos::pdo();
        
        try {
            // 🩺 Obtener información del doctor
            $stmtDoctor = $pdo->prepare("
                SELECT d.*, u.nombre, u.email, u.telefono, e.nombre as especialidad
                FROM doctores d
                INNER JOIN usuarios u ON d.usuario_id = u.id
                LEFT JOIN especialidades e ON d.especialidad_id = e.id
                WHERE d.usuario_id = :usuario_id
            ");
            $stmtDoctor->execute([':usuario_id' => $usuarioId]);
            $doctor = $stmtDoctor->fetch(PDO::FETCH_ASSOC);
            
            if (!$doctor) {
                throw new Exception("Doctor no encontrado");
            }
            
            $doctorId = $doctor['id'];
            $doctorNombre = $doctor['nombre'];
            $especialidad = $doctor['especialidad'] ?? 'Medicina General';
            
            // 📊 ESTADÍSTICAS GENERALES
            
            // Total de citas del doctor
            $stmtTotal = $pdo->prepare("
                SELECT COUNT(*) as total 
                FROM citas 
                WHERE doctor_id = :doctor_id
            ");
            $stmtTotal->execute([':doctor_id' => $doctorId]);
            $totalCitas = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Citas de hoy
            $stmtHoy = $pdo->prepare("
                SELECT COUNT(*) as total 
                FROM citas 
                WHERE doctor_id = :doctor_id 
                AND DATE(fecha) = CURRENT_DATE
            ");
            $stmtHoy->execute([':doctor_id' => $doctorId]);
            $citasHoy = $stmtHoy->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Citas pendientes
            $stmtPendientes = $pdo->prepare("
                SELECT COUNT(*) as total 
                FROM citas 
                WHERE doctor_id = :doctor_id 
                AND estado = 'pendiente'
            ");
            $stmtPendientes->execute([':doctor_id' => $doctorId]);
            $citasPendientes = $stmtPendientes->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Citas completadas
            $stmtCompletadas = $pdo->prepare("
                SELECT COUNT(*) as total 
                FROM citas 
                WHERE doctor_id = :doctor_id 
                AND estado = 'completada'
            ");
            $stmtCompletadas->execute([':doctor_id' => $doctorId]);
            $citasCompletadas = $stmtCompletadas->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Pacientes atendidos (únicos)
            $stmtPacientes = $pdo->prepare("
                SELECT COUNT(DISTINCT paciente_id) as total 
                FROM citas 
                WHERE doctor_id = :doctor_id
            ");
            $stmtPacientes->execute([':doctor_id' => $doctorId]);
            $pacientesAtendidos = $stmtPacientes->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // 📅 PRÓXIMAS CITAS (10 más recientes)
            $stmtProximas = $pdo->prepare("
                SELECT 
                    c.id,
                    c.fecha,
                    TO_CHAR(c.fecha, 'DD/MM/YYYY') as fecha_formateada,
                    TO_CHAR(c.fecha, 'HH24:MI') as hora_formateada,
                    c.estado,
                    u.nombre as paciente,
                    p.id as paciente_id,
                    COALESCE(hc.motivo, 'Sin especificar') as motivo
                FROM citas c
                INNER JOIN pacientes p ON c.paciente_id = p.id
                INNER JOIN usuarios u ON p.usuario_id = u.id
                LEFT JOIN historia_clinica hc ON c.id = hc.cita_id
                WHERE c.doctor_id = :doctor_id
                AND c.fecha >= NOW()
                ORDER BY c.fecha ASC
                LIMIT 10
            ");
            $stmtProximas->execute([':doctor_id' => $doctorId]);
            $proximasCitas = $stmtProximas->fetchAll(PDO::FETCH_ASSOC);
            
            // 📋 CITAS RECIENTES (últimas 10 para historial)
            $stmtRecientes = $pdo->prepare("
                SELECT 
                    c.id,
                    c.fecha,
                    TO_CHAR(c.fecha, 'DD/MM/YYYY') as fecha_formateada,
                    TO_CHAR(c.fecha, 'HH24:MI') as hora_formateada,
                    c.estado,
                    u.nombre as paciente,
                    p.id as paciente_id,
                    COALESCE(hc.motivo, 'Sin especificar') as motivo
                FROM citas c
                INNER JOIN pacientes p ON c.paciente_id = p.id
                INNER JOIN usuarios u ON p.usuario_id = u.id
                LEFT JOIN historia_clinica hc ON c.id = hc.cita_id
                WHERE c.doctor_id = :doctor_id
                ORDER BY c.fecha DESC
                LIMIT 10
            ");
            $stmtRecientes->execute([':doctor_id' => $doctorId]);
            $citasRecientes = $stmtRecientes->fetchAll(PDO::FETCH_ASSOC);
            
            // 📊 Preparar datos para la vista
            $datos = compact(
                'doctorNombre',
                'especialidad',
                'totalCitas',
                'citasHoy',
                'citasPendientes',
                'citasCompletadas',
                'pacientesAtendidos',
                'proximasCitas',
                'citasRecientes'
            );
            
            // 🎨 Extraer variables para la vista
            extract($datos);
            
            // 📄 Definir la vista interna
            $vistaInterna = __DIR__ . "/../vistas/doctor/inicio.php";
            
            // 🖼️ Cargar el layout - ✅ RUTA CORREGIDA
            require __DIR__ . "/../../includes/layout-doctor.php";
            
        } catch (PDOException $e) {
            error_log("❌ Error en DoctorControlador::dashboard() - " . $e->getMessage());
            echo "<div class='alert alert-danger'>Error al cargar el dashboard: " . htmlspecialchars($e->getMessage()) . "</div>";
        } catch (Exception $e) {
            error_log("❌ Error general en DoctorControlador::dashboard() - " . $e->getMessage());
            echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
    
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
    // VISTAS PARA EL ROL DOCTOR
    // ========================================

 /**
 * Perfil del doctor logueado
 */
public function perfil()
{
    Autenticacion::requiereRoles(['doctor']);
    
    $usuarioId = Autenticacion::usuarioId();
    $pdo = BaseDatos::pdo();
    
    try {
        // Obtener información completa del doctor
        $stmtDoctor = $pdo->prepare("
            SELECT 
                d.id as doctor_id,
                d.numero_colegiatura,
                u.nombre,
                u.email,
                u.telefono,
                e.nombre as especialidad,
                e.id as especialidad_id
            FROM doctores d
            INNER JOIN usuarios u ON d.usuario_id = u.id
            LEFT JOIN especialidades e ON d.especialidad_id = e.id
            WHERE d.usuario_id = :usuario_id
        ");
        $stmtDoctor->execute([':usuario_id' => $usuarioId]);
        $doctor = $stmtDoctor->fetch(PDO::FETCH_ASSOC);
        
        if (!$doctor) {
            throw new Exception("Doctor no encontrado");
        }
        
        // Obtener todas las especialidades para el selector
        $stmtEspecialidades = $pdo->query("SELECT id, nombre FROM especialidades ORDER BY nombre");
        $especialidades = $stmtEspecialidades->fetchAll(PDO::FETCH_ASSOC);
        
        // Estadísticas del doctor
        $doctorId = $doctor['doctor_id'];
        
        // Total de citas atendidas
        $stmtCitas = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM citas 
            WHERE doctor_id = :doctor_id
        ");
        $stmtCitas->execute([':doctor_id' => $doctorId]);
        $totalCitas = $stmtCitas->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Pacientes únicos atendidos
        $stmtPacientes = $pdo->prepare("
            SELECT COUNT(DISTINCT paciente_id) as total 
            FROM citas 
            WHERE doctor_id = :doctor_id
        ");
        $stmtPacientes->execute([':doctor_id' => $doctorId]);
        $totalPacientes = $stmtPacientes->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Historias clínicas creadas
        $stmtHistorias = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM historia_clinica 
            WHERE doctor_id = :doctor_id
        ");
        $stmtHistorias->execute([':doctor_id' => $doctorId]);
        $totalHistorias = $stmtHistorias->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Recetas emitidas
        $stmtRecetas = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM recetas 
            WHERE doctor_id = :doctor_id
        ");
        $stmtRecetas->execute([':doctor_id' => $doctorId]);
        $totalRecetas = $stmtRecetas->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Obtener fecha de la cita más antigua como "miembro desde"
        $stmtFecha = $pdo->prepare("
            SELECT MIN(fecha) as primera_cita
            FROM citas 
            WHERE doctor_id = :doctor_id
        ");
        $stmtFecha->execute([':doctor_id' => $doctorId]);
        $resultFecha = $stmtFecha->fetch(PDO::FETCH_ASSOC);
        
        // Si no tiene citas, usar fecha actual
        $fechaRegistro = $resultFecha['primera_cita'] 
            ? date('d/m/Y', strtotime($resultFecha['primera_cita']))
            : date('d/m/Y');
        
        // Preparar datos para la vista
        $datos = compact(
            'doctor',
            'especialidades',
            'totalCitas',
            'totalPacientes',
            'totalHistorias',
            'totalRecetas',
            'fechaRegistro'
        );
        
        // Extraer variables
        extract($datos);
        
        // Cargar vista
        $vistaInterna = __DIR__ . "/../vistas/doctor/perfil.php";
        require __DIR__ . "/../../includes/layout-doctor.php";
        
    } catch (Exception $e) {
        error_log("❌ Error en DoctorControlador::perfil() - " . $e->getMessage());
        echo "<div class='alert alert-danger'>Error al cargar el perfil: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

/**
 * ⚙️ Configuración del perfil del doctor
 */
public function configuracion()
{
    Autenticacion::requiereRoles(['doctor']);
    
    $usuarioId = Autenticacion::usuarioId();
    $pdo = BaseDatos::pdo();
    
    try {
        // Obtener información del doctor
        $stmtDoctor = $pdo->prepare("
            SELECT 
                d.id as doctor_id,
                d.numero_colegiatura,
                u.nombre,
                u.email,
                u.telefono,
                e.nombre as especialidad,
                e.id as especialidad_id
            FROM doctores d
            INNER JOIN usuarios u ON d.usuario_id = u.id
            LEFT JOIN especialidades e ON d.especialidad_id = e.id
            WHERE d.usuario_id = :usuario_id
        ");
        $stmtDoctor->execute([':usuario_id' => $usuarioId]);
        $doctor = $stmtDoctor->fetch(PDO::FETCH_ASSOC);
        
        if (!$doctor) {
            throw new Exception("Doctor no encontrado");
        }
        
        // Preparar datos para la vista
        extract(['doctor' => $doctor]);
        
        $vistaInterna = __DIR__ . "/../vistas/doctor/configuracion.php";
        require __DIR__ . "/../../includes/layout-doctor.php";
        
    } catch (Exception $e) {
        error_log("❌ Error en DoctorControlador::configuracion() - " . $e->getMessage());
        echo "<div class='alert alert-danger'>Error al cargar configuración: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}


/**
 * ⚙️ Guardar configuración del doctor
 */
public function guardarConfiguracionDoctor()
{
    Autenticacion::requiereRoles(['doctor']);
    
    try {
        $seccion = $_POST['seccion'] ?? '';
        
        // Por ahora solo mostramos mensaje de éxito
        // En el futuro se puede guardar en la tabla configuraciones
        
        $this->setMensaje('exito', '✅ Configuración guardada exitosamente');
        
    } catch (Exception $e) {
        error_log("❌ Error al guardar configuración: " . $e->getMessage());
        $this->setMensaje('error', 'Error al guardar la configuración');
    }
    
    $this->redirigir('configuracionDoctor');
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
     * 📋 Ver detalle de una cita específica (NUEVO)
     */
    public function verDetalle()
    {
        Autenticacion::requiereRoles(['doctor']);
        
        $vistaInterna = __DIR__ . "/../vistas/doctor/verDetalle.php";
        require __DIR__ . "/../../includes/layout-doctor.php";
    }

    public function imprimirReceta()
{
    Autenticacion::requiereRoles(['doctor']);
    
    // Esta vista NO usa layout, es una página independiente para imprimir
    $vistaInterna = __DIR__ . "/../vistas/doctor/imprimirReceta.php";
    require $vistaInterna;
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
        
        error_log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        error_log("🔵 [CONTROLADOR] Método eliminarDoctor() iniciado");
        
        $id = $_GET['id'] ?? null;
        error_log("🔵 [CONTROLADOR] ID recibido: " . ($id ?? 'NULL'));
        
        if (!$id) {
            error_log("❌ [CONTROLADOR] ID no proporcionado");
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarDoctores');
        }
        
        try {
            error_log("🔵 [CONTROLADOR] Llamando a Usuario::eliminarDoctor($id)");
            $resultado = Usuario::eliminarDoctor((int)$id);
            
            if ($resultado) {
                error_log("✅ [CONTROLADOR] Eliminación exitosa");
                $this->setMensaje('exito', '✅ Doctor eliminado exitosamente');
            } else {
                error_log("⚠️ [CONTROLADOR] Eliminación falló (retornó false)");
                $this->setMensaje('error', '⚠️ No se pudo eliminar el doctor');
            }
            
        } catch (Exception $e) {
            error_log("❌ [CONTROLADOR] Excepción capturada: " . $e->getMessage());
            error_log("❌ [CONTROLADOR] Stack trace: " . $e->getTraceAsString());
            $this->setMensaje('error', '❌ Error al eliminar el doctor: ' . $e->getMessage());
        }
        
        error_log("🔵 [CONTROLADOR] Redirigiendo a gestionarDoctores");
        error_log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->redirigir('gestionarDoctores');
    }

    /**
 * 💊 Recetas emitidas por el doctor
 */
public function misRecetas()
{
    Autenticacion::requiereRoles(['doctor']);
    
    $vistaInterna = __DIR__ . "/../vistas/doctor/recetas.php";
    require __DIR__ . "/../../includes/layout-doctor.php";
}

/**
 * 💊 Ver detalle completo de una receta
 */
public function verReceta()
{
    Autenticacion::requiereRoles(['doctor']);
    
    $vistaInterna = __DIR__ . "/../vistas/doctor/verReceta.php";
    require __DIR__ . "/../../includes/layout-doctor.php";
}

/**
 * 💊 Formulario para crear nueva receta
 */
public function crearReceta()
{
    Autenticacion::requiereRoles(['doctor']);
    
    $vistaInterna = __DIR__ . "/../vistas/doctor/crearReceta.php";
    require __DIR__ . "/../../includes/layout-doctor.php";
}

/**
 * 💊 Guardar nueva receta
 */
public function guardarReceta()
{
    Autenticacion::requiereRoles(['doctor']);
    
    try {
        $pdo = BaseDatos::pdo();
        $usuarioId = Autenticacion::usuarioId();
        
        // Obtener ID del doctor
        $stmtDoctor = $pdo->prepare("SELECT id FROM doctores WHERE usuario_id = :usuario_id");
        $stmtDoctor->execute([':usuario_id' => $usuarioId]);
        $doctor = $stmtDoctor->fetch(PDO::FETCH_ASSOC);
        
        if (!$doctor) {
            throw new Exception("Doctor no encontrado");
        }
        
        $doctorId = $doctor['id'];
        
        // Validaciones
        if (empty($_POST['paciente_id'])) {
            throw new Exception("Debe seleccionar un paciente");
        }
        
        if (empty($_POST['medicamentos']) || !is_array($_POST['medicamentos'])) {
            throw new Exception("Debe agregar al menos un medicamento");
        }
        
        $pdo->beginTransaction();
        
        // Insertar receta
        $stmtReceta = $pdo->prepare("
            INSERT INTO recetas (doctor_id, paciente_id, fecha_emision, observaciones)
            VALUES (:doctor_id, :paciente_id, NOW(), :observaciones)
            RETURNING id
        ");
        
        $stmtReceta->execute([
            ':doctor_id' => $doctorId,
            ':paciente_id' => $_POST['paciente_id'],
            ':observaciones' => $_POST['observaciones'] ?? ''
        ]);
        
        $recetaId = $stmtReceta->fetch(PDO::FETCH_ASSOC)['id'];
        
        // Insertar medicamentos
        $stmtDetalle = $pdo->prepare("
            INSERT INTO detalle_recetas 
            (receta_id, medicamento_id, dosis, frecuencia, duracion, cantidad)
            VALUES (:receta_id, :medicamento_id, :dosis, :frecuencia, :duracion, :cantidad)
        ");
        
        foreach ($_POST['medicamentos'] as $medicamento) {
            if (empty($medicamento['medicamento_id'])) continue;
            
            $stmtDetalle->execute([
                ':receta_id' => $recetaId,
                ':medicamento_id' => $medicamento['medicamento_id'],
                ':dosis' => $medicamento['dosis'] ?? '',
                ':frecuencia' => $medicamento['frecuencia'] ?? '',
                ':duracion' => $medicamento['duracion'] ?? '',
                ':cantidad' => $medicamento['cantidad'] ?? 1
            ]);
        }
        
        $pdo->commit();
        
        $this->setMensaje('exito', '✅ Receta creada exitosamente');
        $this->redirigir('doctorRecetas');
        
    } catch (Exception $e) {
        if (isset($pdo)) {
            $pdo->rollBack();
        }
        error_log("❌ Error al crear receta: " . $e->getMessage());
        $this->setMensaje('error', 'Error: ' . $e->getMessage());
        $this->redirigir('doctorCrearReceta', ['paciente_id' => $_POST['paciente_id'] ?? '']);
    }
}

/**
 * 📋 Formulario para crear historia clínica
 */
public function crearHistoria()
{
    Autenticacion::requiereRoles(['doctor']);
    
    $vistaInterna = __DIR__ . "/../vistas/doctor/crearHistoria.php";
    require __DIR__ . "/../../includes/layout-doctor.php";
}

/**
 * 📋 Guardar nueva historia clínica
 */
public function guardarHistoria()
{
    Autenticacion::requiereRoles(['doctor']);
    
    try {
        $pdo = BaseDatos::pdo();
        $usuarioId = Autenticacion::usuarioId();
        
        // Obtener ID del doctor
        $stmtDoctor = $pdo->prepare("SELECT id FROM doctores WHERE usuario_id = :usuario_id");
        $stmtDoctor->execute([':usuario_id' => $usuarioId]);
        $doctor = $stmtDoctor->fetch(PDO::FETCH_ASSOC);
        
        if (!$doctor) {
            throw new Exception("Doctor no encontrado");
        }
        
        $doctorId = $doctor['id'];
        
        // Validaciones
        if (empty($_POST['cita_id']) || empty($_POST['paciente_id'])) {
            throw new Exception("Debe seleccionar una cita válida");
        }
        
        if (empty($_POST['motivo']) || empty($_POST['diagnostico']) || empty($_POST['indicaciones'])) {
            throw new Exception("Todos los campos obligatorios deben ser completados");
        }
        
        // Verificar que la cita no tenga ya una historia clínica
        $stmtCheck = $pdo->prepare("SELECT id FROM historia_clinica WHERE cita_id = :cita_id");
        $stmtCheck->execute([':cita_id' => $_POST['cita_id']]);
        if ($stmtCheck->fetch()) {
            throw new Exception("Esta cita ya tiene una historia clínica registrada");
        }
        
        $pdo->beginTransaction();
        
        // Insertar historia clínica
        $stmtHistoria = $pdo->prepare("
            INSERT INTO historia_clinica 
            (cita_id, doctor_id, paciente_id, motivo, diagnostico, indicaciones, observaciones, creada_en)
            VALUES (:cita_id, :doctor_id, :paciente_id, :motivo, :diagnostico, :indicaciones, :observaciones, NOW())
            RETURNING id
        ");
        
        $stmtHistoria->execute([
            ':cita_id' => $_POST['cita_id'],
            ':doctor_id' => $doctorId,
            ':paciente_id' => $_POST['paciente_id'],
            ':motivo' => trim($_POST['motivo']),
            ':diagnostico' => trim($_POST['diagnostico']),
            ':indicaciones' => trim($_POST['indicaciones']),
            ':observaciones' => trim($_POST['observaciones'] ?? '')
        ]);
        
        $historiaId = $stmtHistoria->fetch(PDO::FETCH_ASSOC)['id'];
        
        // Actualizar estado de la cita a 'completada'
        $stmtUpdateCita = $pdo->prepare("
            UPDATE citas 
            SET estado = 'completada' 
            WHERE id = :cita_id
        ");
        $stmtUpdateCita->execute([':cita_id' => $_POST['cita_id']]);
        
        $pdo->commit();
        
        $this->setMensaje('exito', '✅ Historia clínica creada exitosamente');
        $this->redirigir('doctorHistorias');
        
    } catch (Exception $e) {
        if (isset($pdo)) {
            $pdo->rollBack();
        }
        error_log("❌ Error al crear historia clínica: " . $e->getMessage());
        $this->setMensaje('error', 'Error: ' . $e->getMessage());
        $this->redirigir('crearHistoria', [
            'cita_id' => $_POST['cita_id'] ?? '',
            'paciente_id' => $_POST['paciente_id'] ?? ''
        ]);
    }
}

/**
 * 📋 Editar historia clínica existente
 */
public function editarHistoria()
{
    Autenticacion::requiereRoles(['doctor']);
    
    $vistaInterna = __DIR__ . "/../vistas/doctor/editarHistoria.php";
    require __DIR__ . "/../../includes/layout-doctor.php";
}

/**
 * 📋 Actualizar historia clínica
 */
public function actualizarHistoria()
{
    Autenticacion::requiereRoles(['doctor']);
    
    try {
        $pdo = BaseDatos::pdo();
        $usuarioId = Autenticacion::usuarioId();
        $historiaId = $_POST['historia_id'] ?? null;
        
        if (!$historiaId) {
            throw new Exception("ID de historia no proporcionado");
        }
        
        // Obtener ID del doctor
        $stmtDoctor = $pdo->prepare("SELECT id FROM doctores WHERE usuario_id = :usuario_id");
        $stmtDoctor->execute([':usuario_id' => $usuarioId]);
        $doctor = $stmtDoctor->fetch(PDO::FETCH_ASSOC);
        
        if (!$doctor) {
            throw new Exception("Doctor no encontrado");
        }
        
        // Verificar que la historia pertenezca al doctor
        $stmtCheck = $pdo->prepare("
            SELECT id FROM historia_clinica 
            WHERE id = :historia_id AND doctor_id = :doctor_id
        ");
        $stmtCheck->execute([
            ':historia_id' => $historiaId,
            ':doctor_id' => $doctor['id']
        ]);
        
        if (!$stmtCheck->fetch()) {
            throw new Exception("No tiene permisos para editar esta historia clínica");
        }
        
        // Actualizar historia clínica
        $stmtUpdate = $pdo->prepare("
            UPDATE historia_clinica 
            SET motivo = :motivo,
                diagnostico = :diagnostico,
                indicaciones = :indicaciones,
                observaciones = :observaciones
            WHERE id = :historia_id
        ");
        
        $stmtUpdate->execute([
            ':historia_id' => $historiaId,
            ':motivo' => trim($_POST['motivo']),
            ':diagnostico' => trim($_POST['diagnostico']),
            ':indicaciones' => trim($_POST['indicaciones']),
            ':observaciones' => trim($_POST['observaciones'] ?? '')
        ]);
        
        $this->setMensaje('exito', '✅ Historia clínica actualizada exitosamente');
        $this->redirigir('doctorHistorias');
        
    } catch (Exception $e) {
        error_log("❌ Error al actualizar historia: " . $e->getMessage());
        $this->setMensaje('error', 'Error: ' . $e->getMessage());
        $this->redirigir('editarHistoria', ['id' => $historiaId ?? '']);
    }
}

}