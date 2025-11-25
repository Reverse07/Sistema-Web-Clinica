<?php
require_once __DIR__ . "/../../nucleo/BaseDatos.php";
require_once __DIR__ . "/DetalleReceta.php";

class Receta
{
    private int $id = 0;
    private int $doctor_id = 0;
    private int $paciente_id = 0;
    private string $fecha_emision = '';
    private string $observaciones = '';

    public function __construct(array $data = [])
    {
        $this->id = (int)($data['id'] ?? 0);
        $this->doctor_id = (int)($data['doctor_id'] ?? 0);
        $this->paciente_id = (int)($data['paciente_id'] ?? 0);
        $this->fecha_emision = $data['fecha_emision'] ?? '';
        $this->observaciones = $data['observaciones'] ?? '';
    }

    // ========================================
    // CRUD BÁSICO
    // ========================================

    /**
     * Crea una nueva receta en la BD
     */
    public function crear(): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("
            INSERT INTO recetas (doctor_id, paciente_id, fecha_emision, observaciones)
            VALUES (:doctor_id, :paciente_id, CURRENT_TIMESTAMP, :observaciones)
        ");
        $ok = $stmt->execute([
            ':doctor_id' => $this->doctor_id,
            ':paciente_id' => $this->paciente_id,
            ':observaciones' => $this->observaciones
        ]);
        if ($ok) $this->id = (int)$pdo->lastInsertId();
        return $ok;
    }

    /**
     * Método estático para crear desde array (compatibilidad con controlador)
     */
    public static function crearDesdeArray(array $data): int
    {
        $receta = new Receta($data);
        if ($receta->crear()) {
            return $receta->getId();
        }
        throw new Exception("Error al crear receta");
    }

    /**
     * Actualiza una receta existente
     */
    public function actualizar(): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("
            UPDATE recetas 
            SET observaciones = :observaciones
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id' => $this->id,
            ':observaciones' => $this->observaciones
        ]);
    }

    /**
     * Elimina una receta por ID
     */
    public static function eliminar(int $id): bool
    {
        $pdo = BaseDatos::pdo();
        
        // Primero eliminar detalles asociados
        $stmt = $pdo->prepare("DELETE FROM detalle_recetas WHERE receta_id = :id");
        $stmt->execute([':id' => $id]);
        
        // Luego eliminar la receta
        $stmt = $pdo->prepare("DELETE FROM recetas WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // ========================================
    // CONSULTAS
    // ========================================

    /**
     * Obtiene todas las recetas con datos de doctor y paciente
     */
    public static function todas(): array
    {
        $pdo = BaseDatos::pdo();
        $sql = "
            SELECT 
                r.id,
                r.doctor_id,
                r.paciente_id,
                r.fecha_emision,
                r.observaciones,
                CONCAT(ud.nombre) as doctor_nombre,
                CONCAT(up.nombre) as paciente_nombre,
                up.email as paciente_email,
                e.nombre as especialidad
            FROM recetas r
            LEFT JOIN doctores d ON r.doctor_id = d.id
            LEFT JOIN usuarios ud ON d.usuario_id = ud.id
            LEFT JOIN pacientes p ON r.paciente_id = p.id
            LEFT JOIN usuarios up ON p.usuario_id = up.id
            LEFT JOIN especialidades e ON d.especialidad_id = e.id
            ORDER BY r.fecha_emision DESC
        ";
        
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todas las recetas (objetos Receta)
     */
    public static function obtenerTodas(): array
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->query("SELECT * FROM recetas ORDER BY fecha_emision DESC");
        $lista = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lista[] = new Receta($fila);
        }
        return $lista;
    }

    /**
     * Busca una receta por ID
     */
    public static function buscarPorId(int $id): ?Receta
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("SELECT * FROM recetas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? new Receta($fila) : null;
    }

    /**
     * Obtiene una receta por ID con datos completos
     */
    public static function obtenerPorId(int $id): ?array
    {
        $pdo = BaseDatos::pdo();
        $sql = "
            SELECT 
                r.id,
                r.doctor_id,
                r.paciente_id,
                r.fecha_emision,
                r.observaciones,
                CONCAT(ud.nombre) as doctor_nombre,
                ud.email as doctor_email,
                CONCAT(up.nombre) as paciente_nombre,
                up.email as paciente_email,
                up.telefono as paciente_telefono,
                e.nombre as especialidad,
                d.numero_colegiatura
            FROM recetas r
            LEFT JOIN doctores d ON r.doctor_id = d.id
            LEFT JOIN usuarios ud ON d.usuario_id = ud.id
            LEFT JOIN pacientes p ON r.paciente_id = p.id
            LEFT JOIN usuarios up ON p.usuario_id = up.id
            LEFT JOIN especialidades e ON d.especialidad_id = e.id
            WHERE r.id = :id
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /**
     * Obtiene recetas de un doctor específico
     */
    public static function obtenerPorDoctor(int $doctorId): array
    {
        $pdo = BaseDatos::pdo();
        $sql = "
            SELECT 
                r.id,
                r.doctor_id,
                r.paciente_id,
                r.fecha_emision,
                r.observaciones,
                CONCAT(ud.nombre) as doctor_nombre,
                CONCAT(up.nombre) as paciente_nombre,
                up.email as paciente_email
            FROM recetas r
            LEFT JOIN doctores d ON r.doctor_id = d.id
            LEFT JOIN usuarios ud ON d.usuario_id = ud.id
            LEFT JOIN pacientes p ON r.paciente_id = p.id
            LEFT JOIN usuarios up ON p.usuario_id = up.id
            WHERE d.id = :doctor_id
            ORDER BY r.fecha_emision DESC
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':doctor_id' => $doctorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene recetas de un paciente específico
     */
    public static function obtenerPorPaciente(int $pacienteId): array
    {
        $pdo = BaseDatos::pdo();
        $sql = "
            SELECT 
                r.id,
                r.doctor_id,
                r.paciente_id,
                r.fecha_emision,
                r.observaciones,
                u.nombre as doctor_nombre,
                u.email as doctor_email,
                u.telefono as doctor_telefono,
                e.nombre as especialidad,
                d.numero_colegiatura
            FROM recetas r
            INNER JOIN doctores d ON r.doctor_id = d.id
            INNER JOIN usuarios u ON d.usuario_id = u.id
            LEFT JOIN especialidades e ON d.especialidad_id = e.id
            WHERE r.paciente_id = :paciente_id
            ORDER BY r.fecha_emision DESC
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':paciente_id' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Obtiene los detalles (medicamentos) de esta receta
     */
    public function obtenerDetalles(): array
    {
        return DetalleReceta::obtenerPorReceta($this->id);
    }

    // ========================================
    // GETTERS
    // ========================================

    public function getId(): int 
    { 
        return $this->id; 
    }

    public function getDoctorId(): int 
    { 
        return $this->doctor_id; 
    }

    public function getPacienteId(): int 
    { 
        return $this->paciente_id; 
    }

    public function getFechaEmision(): string 
    { 
        return $this->fecha_emision; 
    }

    public function getObservaciones(): string 
    { 
        return $this->observaciones; 
    }

    // ========================================
    // SETTERS
    // ========================================

    public function setObservaciones(string $observaciones): void
    {
        $this->observaciones = $observaciones;
    }
}