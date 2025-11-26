<?php
// 📦 Importa la clase BaseDatos para acceder a la conexión PDO
require_once __DIR__ . "/../../nucleo/BaseDatos.php";


class Usuario
{
    // =====================
    // Propiedades
    // =====================
    private int $id = 0;
    private string $nombre = '';
    private string $email = '';
    private string $password = '';
    private string $telefono = '';
    private int $rol_id = 0;

    // =====================
    // Constructor
    // =====================
    public function __construct(array $data = [])
    {
        $this->id = (int) ($data['id'] ?? $data['ID'] ?? 0);
        $this->nombre = $data['nombre'] ?? $data['name'] ?? '';
        $this->email = $data['email'] ?? $data['correo'] ?? '';
        $this->password = $data['password'] ?? $data['passwd'] ?? '';
        $this->telefono = $data['telefono'] ?? $data['telefono_contacto'] ?? '';
        $this->rol_id = (int) ($data['rol_id'] ?? $data['role_id'] ?? 0);
    }

    // =====================
    // Métodos estáticos CRUD
    // =====================

    /**
     * Busca un usuario por email
     */
    public static function buscarPorEmail(string $email): ?Usuario
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? new Usuario($fila) : null;
    }

    /**
     * Crea un nuevo usuario en la base de datos
     */
    public function crearUsuario(): bool
    {
        try {
            $pdo = BaseDatos::pdo();
            $stmt = $pdo->prepare(
                "INSERT INTO usuarios (nombre, email, password, telefono, rol_id) 
                 VALUES (:nombre, :email, :password, :telefono, :rol_id)"
            );

            $pwd = $this->password;
            if (!empty($pwd) && !str_starts_with($pwd, '$2y$') && !str_starts_with($pwd, '$2a$')) {
                $pwd = password_hash($pwd, PASSWORD_BCRYPT);
            }

            $ok = $stmt->execute([
                ':nombre' => $this->nombre,
                ':email' => $this->email,
                ':password' => $pwd,
                ':telefono' => $this->telefono,
                ':rol_id' => $this->rol_id
            ]);

            if ($ok) {
                $this->id = (int) $pdo->lastInsertId();
            }

            return (bool) $ok;
        } catch (PDOException $e) {
            error_log("Usuario::crearUsuario error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Devuelve todos los usuarios
     */
    public static function obtenerTodos(): array
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->query("SELECT * FROM usuarios ORDER BY id ASC");
        $usuarios = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = new Usuario($fila);
        }
        return $usuarios;
    }

    /**
     * Busca un usuario por ID
     */
    public static function buscarPorId(int $id): ?Usuario
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? new Usuario($fila) : null;
    }

    /**
     * Actualiza datos de un usuario
     */
    public static function actualizarUsuario($id, $nombre, $email, $telefono, $password = null): bool
    {
        $pdo = BaseDatos::pdo();
        try {
            $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, telefono = :telefono";
            $params = [
                ':nombre' => $nombre,
                ':email' => $email,
                ':telefono' => $telefono,
                ':id' => $id
            ];

            if (!empty($password)) {
                $sql .= ", password = :password";
                $params[':password'] = str_starts_with($password, '$2y$') || str_starts_with($password, '$2a$')
                    ? $password
                    : password_hash($password, PASSWORD_BCRYPT);
            }

            $sql .= " WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return (bool) $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Usuario::actualizarUsuario error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un usuario por ID
     */
    public static function eliminarUsuario(int $id): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        return (bool) $stmt->execute([':id' => $id]);
    }

    // =====================
    // Métodos adicionales
    // =====================

    /**
     * Cambia el rol de un usuario
     */
    public static function cambiarRol(int $id, int $rol_id): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("UPDATE usuarios SET rol_id = :rol_id WHERE id = :id");
        return (bool) $stmt->execute([':rol_id' => $rol_id, ':id' => $id]);
    }

    /**
     * Contar todos los usuarios
     */
    public static function contarTodos(): int
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Contar usuarios por rol
     */
    public static function contarPorRol($rol): int
    {
        $pdo = BaseDatos::pdo();

        if (is_numeric($rol)) {
            $rolId = (int) $rol;
        } else {
            $stmtRol = $pdo->prepare("SELECT id FROM roles WHERE nombre = :nombre LIMIT 1");
            $stmtRol->execute([':nombre' => $rol]);
            $rolId = $stmtRol->fetchColumn();
        }

        if (!$rolId) return 0;

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE rol_id = :rol_id");
        $stmt->execute([':rol_id' => $rolId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Obtener usuarios por rol
     */
    public static function obtenerPorRol($rol): array
    {
        $pdo = BaseDatos::pdo();

        if (is_numeric($rol)) {
            $rolId = (int) $rol;
        } else {
            $stmtRol = $pdo->prepare("SELECT id FROM roles WHERE nombre = :nombre LIMIT 1");
            $stmtRol->execute([':nombre' => $rol]);
            $rolId = $stmtRol->fetchColumn();
        }

        if (!$rolId) return [];

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE rol_id = :rol_id ORDER BY id ASC");
        $stmt->execute([':rol_id' => $rolId]);

        $usuarios = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = new Usuario($fila);
        }

        return $usuarios;
    }

    /**
     * Verificar contraseña en login
     */
    public function verificarContraseña(string $password): bool
    {
        if (empty($this->password)) return false;
        return password_verify($password, $this->password);
    }

/**
 * 🗑️ Elimina un paciente del sistema (SIN historias_clinicas)
 */
public static function eliminarPaciente(int $id): bool
{
    $pdo = BaseDatos::pdo();
    
    try {
        error_log("🔍 [ELIMINARPACIENTE] Iniciando eliminación - Usuario ID: $id");
        
        // ========================================
        // PASO 1: Verificar que el usuario existe y es paciente
        // ========================================
        $stmtVerificar = $pdo->prepare("
            SELECT u.id, u.nombre, u.rol_id, p.id as paciente_id 
            FROM usuarios u 
            LEFT JOIN pacientes p ON u.id = p.usuario_id 
            WHERE u.id = :id
        ");
        $stmtVerificar->execute([':id' => $id]);
        $usuario = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            error_log("❌ [ELIMINARPACIENTE] Usuario no encontrado: $id");
            return false;
        }
        
        if ($usuario['rol_id'] != 3) {
            error_log("❌ [ELIMINARPACIENTE] Usuario no es paciente (rol_id: {$usuario['rol_id']})");
            return false;
        }
        
        if (!$usuario['paciente_id']) {
            error_log("❌ [ELIMINARPACIENTE] No existe registro en tabla pacientes");
            return false;
        }
        
        $pacienteId = (int)$usuario['paciente_id'];
        error_log("✅ [ELIMINARPACIENTE] Usuario verificado: {$usuario['nombre']} (Paciente ID: $pacienteId)");
        
        // ========================================
        // PASO 2: Iniciar transacción
        // ========================================
        $pdo->beginTransaction();
        error_log("🔄 [ELIMINARPACIENTE] Transacción iniciada");
        
        // ========================================
        // ORDEN DE ELIMINACIÓN (solo tablas que existen)
        // ========================================
        
        // 1. Eliminar detalles de recetas (si la tabla existe)
        try {
            $sql = "DELETE FROM detalle_recetas 
                    WHERE receta_id IN (
                        SELECT id FROM recetas WHERE paciente_id = :paciente_id
                    )";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':paciente_id' => $pacienteId]);
            error_log("🔄 [ELIMINARPACIENTE] Detalles de recetas eliminados: " . $stmt->rowCount());
        } catch (PDOException $e) {
            error_log("⚠️ [ELIMINARPACIENTE] Tabla detalle_recetas no existe o está vacía");
        }
        
        // 2. Eliminar recetas
        try {
            $stmt = $pdo->prepare("DELETE FROM recetas WHERE paciente_id = :paciente_id");
            $stmt->execute([':paciente_id' => $pacienteId]);
            error_log("🔄 [ELIMINARPACIENTE] Recetas eliminadas: " . $stmt->rowCount());
        } catch (PDOException $e) {
            error_log("⚠️ [ELIMINARPACIENTE] Tabla recetas no existe o está vacía");
        }
        
        // 3. Eliminar historia_clinica (SINGULAR, no plural)
        try {
            $stmt = $pdo->prepare("DELETE FROM historia_clinica WHERE paciente_id = :paciente_id");
            $stmt->execute([':paciente_id' => $pacienteId]);
            error_log("🔄 [ELIMINARPACIENTE] Historia clínica eliminada: " . $stmt->rowCount());
        } catch (PDOException $e) {
            error_log("⚠️ [ELIMINARPACIENTE] Error en historia_clinica: " . $e->getMessage());
        }
        
        // 4. Eliminar facturas - CRÍTICO
        error_log("🔍 [ELIMINARPACIENTE] Eliminando facturas del paciente_id: $pacienteId");
        
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM facturas WHERE paciente_id = :paciente_id");
        $stmtCheck->execute([':paciente_id' => $pacienteId]);
        $numFacturas = $stmtCheck->fetchColumn();
        error_log("🔍 [ELIMINARPACIENTE] Facturas encontradas: $numFacturas");
        
        if ($numFacturas > 0) {
            $stmt = $pdo->prepare("DELETE FROM facturas WHERE paciente_id = :paciente_id");
            $stmt->execute([':paciente_id' => $pacienteId]);
            $eliminadas = $stmt->rowCount();
            error_log("🔄 [ELIMINARPACIENTE] Facturas eliminadas: $eliminadas");
            
            // Verificar eliminación
            $stmtVerif = $pdo->prepare("SELECT COUNT(*) FROM facturas WHERE paciente_id = :paciente_id");
            $stmtVerif->execute([':paciente_id' => $pacienteId]);
            $restantes = $stmtVerif->fetchColumn();
            
            if ($restantes > 0) {
                throw new Exception("ERROR: Quedan $restantes facturas sin eliminar");
            }
            
            error_log("✅ [ELIMINARPACIENTE] Todas las facturas eliminadas correctamente");
        }
        
        // 5. Eliminar citas
        try {
            $stmt = $pdo->prepare("DELETE FROM citas WHERE paciente_id = :paciente_id");
            $stmt->execute([':paciente_id' => $pacienteId]);
            error_log("🔄 [ELIMINARPACIENTE] Citas eliminadas: " . $stmt->rowCount());
        } catch (PDOException $e) {
            error_log("⚠️ [ELIMINARPACIENTE] Error en citas: " . $e->getMessage());
        }
        
        // 6. Eliminar registro en tabla pacientes
        error_log("🔍 [ELIMINARPACIENTE] Eliminando registro de paciente ID: $pacienteId");
        $stmt = $pdo->prepare("DELETE FROM pacientes WHERE id = :paciente_id");
        $stmt->execute([':paciente_id' => $pacienteId]);
        $countPaciente = $stmt->rowCount();
        error_log("🔄 [ELIMINARPACIENTE] Registro de paciente eliminado: $countPaciente");
        
        if ($countPaciente === 0) {
            throw new Exception("ERROR: No se pudo eliminar el registro de paciente");
        }
        
        // 7. Eliminar usuario
        error_log("🔍 [ELIMINARPACIENTE] Eliminando usuario ID: $id");
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :usuario_id");
        $stmt->execute([':usuario_id' => $id]);
        $countUsuario = $stmt->rowCount();
        error_log("🔄 [ELIMINARPACIENTE] Usuario eliminado: $countUsuario");
        
        if ($countUsuario === 0) {
            throw new Exception("ERROR: No se pudo eliminar el usuario");
        }
        
        // ========================================
        // PASO 3: Confirmar transacción
        // ========================================
        $pdo->commit();
        error_log("✅✅✅ [ELIMINARPACIENTE] ÉXITO TOTAL - Paciente eliminado completamente ✅✅✅");
        
        return true;
        
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
            error_log("🔙 [ELIMINARPACIENTE] Transacción revertida");
        }
        
        error_log("❌ [ELIMINARPACIENTE] Error PDO: " . $e->getMessage());
        error_log("❌ [ELIMINARPACIENTE] SQL State: " . $e->getCode());
        error_log("❌ [ELIMINARPACIENTE] Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
        
        return false;
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
            error_log("🔙 [ELIMINARPACIENTE] Transacción revertida");
        }
        
        error_log("❌ [ELIMINARPACIENTE] Error general: " . $e->getMessage());
        
        return false;
    }
}

/**
 * Elimina un doctor del sistema (usuario + registro de doctor)
 * @param int $usuarioId ID del usuario (rol doctor)
 * @return bool
 */
public static function eliminarDoctor(int $usuarioId): bool
{
    error_log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
    error_log("🔵 [MODELO] Usuario::eliminarDoctor($usuarioId) iniciado");
    
    try {
        $pdo = BaseDatos::pdo();
        
        // Verificar que sea un doctor (rol_id = 2)
        $sqlVerificar = "SELECT rol_id FROM usuarios WHERE id = :id LIMIT 1";
        $stmtVerificar = $pdo->prepare($sqlVerificar);
        $stmtVerificar->execute([':id' => $usuarioId]);
        $usuario = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            error_log("❌ [MODELO] Usuario no encontrado con ID: $usuarioId");
            return false;
        }
        
        if ($usuario['rol_id'] != 2) {
            error_log("❌ [MODELO] El usuario $usuarioId no es un doctor (rol_id: {$usuario['rol_id']})");
            return false;
        }
        
        // Iniciar transacción
        $pdo->beginTransaction();
        error_log("✅ [MODELO] Transacción iniciada");
        
        // 1. Obtener el ID del doctor
        $sqlDoctorId = "SELECT id FROM doctores WHERE usuario_id = :usuario_id LIMIT 1";
        $stmtDoctorId = $pdo->prepare($sqlDoctorId);
        $stmtDoctorId->execute([':usuario_id' => $usuarioId]);
        $doctor = $stmtDoctorId->fetch(PDO::FETCH_ASSOC);
        
        if ($doctor) {
            $doctorId = $doctor['id'];
            error_log("✅ [MODELO] Doctor encontrado con ID: $doctorId");
            
            // 2. Eliminar historias clínicas del doctor (tabla: historia_clinica)
            $sqlHistorias = "DELETE FROM historia_clinica WHERE doctor_id = :doctor_id";
            $stmtHistorias = $pdo->prepare($sqlHistorias);
            $stmtHistorias->execute([':doctor_id' => $doctorId]);
            $historiasEliminadas = $stmtHistorias->rowCount();
            error_log("✅ [MODELO] Historias clínicas eliminadas: $historiasEliminadas");
            
            // 3. Eliminar recetas del doctor
            $sqlRecetas = "DELETE FROM recetas WHERE doctor_id = :doctor_id";
            $stmtRecetas = $pdo->prepare($sqlRecetas);
            $stmtRecetas->execute([':doctor_id' => $doctorId]);
            $recetasEliminadas = $stmtRecetas->rowCount();
            error_log("✅ [MODELO] Recetas eliminadas: $recetasEliminadas");
            
            // 4. Actualizar citas (poner doctor_id en NULL para mantener historial)
            $sqlCitas = "UPDATE citas SET doctor_id = NULL WHERE doctor_id = :doctor_id";
            $stmtCitas = $pdo->prepare($sqlCitas);
            $stmtCitas->execute([':doctor_id' => $doctorId]);
            $citasActualizadas = $stmtCitas->rowCount();
            error_log("✅ [MODELO] Citas actualizadas (doctor_id = NULL): $citasActualizadas");
            
            // 5. Eliminar registro de la tabla doctores
            $sqlEliminarDoctor = "DELETE FROM doctores WHERE id = :id";
            $stmtEliminarDoctor = $pdo->prepare($sqlEliminarDoctor);
            $stmtEliminarDoctor->execute([':id' => $doctorId]);
            error_log("✅ [MODELO] Registro de doctores eliminado");
        } else {
            error_log("⚠ [MODELO] No se encontró registro en tabla doctores para usuario_id: $usuarioId");
        }
        
        // 6. Eliminar el usuario
        $sqlEliminarUsuario = "DELETE FROM usuarios WHERE id = :id";
        $stmtEliminarUsuario = $pdo->prepare($sqlEliminarUsuario);
        $resultado = $stmtEliminarUsuario->execute([':id' => $usuarioId]);
        
        if ($resultado && $stmtEliminarUsuario->rowCount() > 0) {
            error_log("✅ [MODELO] Usuario eliminado correctamente");
            
            // Confirmar transacción
            $pdo->commit();
            error_log("✅ [MODELO] Transacción confirmada (COMMIT)");
            error_log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            return true;
        } else {
            error_log("❌ [MODELO] No se pudo eliminar el usuario");
            $pdo->rollBack();
            error_log("❌ [MODELO] Transacción revertida (ROLLBACK)");
            error_log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            return false;
        }
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
            error_log("❌ [MODELO] Transacción revertida por excepción");
        }
        error_log("❌ [MODELO] Error al eliminar doctor: " . $e->getMessage());
        error_log("❌ [MODELO] Stack trace: " . $e->getTraceAsString());
        error_log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        throw $e;
    }
}

    // =====================
    // Getters
    // =====================
    public function getId(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelefono(): string
    {
        return $this->telefono;
    }

    public function getRolId(): int
    {
        return $this->rol_id;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    // =====================
    // Setters
    // =====================
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
