<?php

require_once __DIR__ . "/../../nucleo/BaseDatos.php";
require_once __DIR__ . "/Usuario.php";

class Paciente
{
    private int $id;
    private int $usuario_id;
    private string $fecha_nacimiento;
    private string $genero;
    private string $direccion;
    private string $dni;

    private Usuario $usuario;

    public function __construct(array $data = [])
    {
        $this->id               = $data['id'] ?? 0;
        $this->usuario_id       = $data['usuario_id'] ?? 0;
        $this->fecha_nacimiento = $data['fecha_nacimiento'] ?? '';
        $this->genero           = $data['genero'] ?? '';
        $this->direccion        = $data['direccion'] ?? '';
        $this->dni              = $data['dni'] ?? '';

        // Usuario embebido
        $this->usuario = new Usuario([
            'id'       => $data['usuario_id'] ?? 0,
            'nombre'   => $data['nombre'] ?? '',
            'email'    => $data['email'] ?? '',
            'telefono' => $data['telefono'] ?? ''
        ]);
    }

    // =============================
    // 📌 GETTERS
    // =============================
    public function getId(): int { return $this->id; }
    public function getUsuarioId(): int { return $this->usuario_id; }
    public function getFechaNacimiento(): string { return $this->fecha_nacimiento; }
    public function getGenero(): string { return $this->genero; }
    public function getDireccion(): string { return $this->direccion; }
    public function getDni(): string { return $this->dni; }
    public function getUsuario(): Usuario { return $this->usuario; }

    // =============================
    // 📝 Crear nuevo paciente
    // =============================
    public static function crear(int $usuario_id, string $fecha_nacimiento, string $genero, string $direccion, string $dni): bool
    {
        try {
            $pdo = BaseDatos::pdo();
            $stmt = $pdo->prepare(
                "INSERT INTO pacientes (usuario_id, fecha_nacimiento, genero, direccion, dni)
                 VALUES (:usuario_id, :fecha_nacimiento, :genero, :direccion, :dni)"
            );

            return $stmt->execute([
                ':usuario_id'       => $usuario_id,
                ':fecha_nacimiento' => $fecha_nacimiento,
                ':genero'           => $genero,
                ':direccion'        => $direccion,
                ':dni'              => $dni
            ]);
        } catch (PDOException $e) {
            error_log("❌ Error en Paciente::crear(): " . $e->getMessage());
            return false;
        }
    }

    // =============================
    // 📋 Obtener todos los pacientes
    // =============================
    public static function todos(): array
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->query("SELECT p.*, u.nombre, u.email, u.telefono 
                             FROM pacientes p
                             JOIN usuarios u ON p.usuario_id = u.id
                             ORDER BY p.id ASC");

        $pacientes = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pacientes[] = new Paciente($fila);
        }

        return $pacientes;
    }

    // =============================
    // 🔍 Buscar por usuario_id
    // =============================
    public static function buscarPorUsuarioId(int $usuario_id): ?Paciente
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("SELECT p.*, u.nombre, u.email, u.telefono 
                               FROM pacientes p
                               JOIN usuarios u ON p.usuario_id = u.id
                               WHERE p.usuario_id = :usuario_id
                               LIMIT 1");
        $stmt->execute([':usuario_id' => $usuario_id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return $fila ? new Paciente($fila) : null;
    }

    // =============================
    // ✍️ Actualizar paciente
    // =============================
    public static function actualizar(int $id, string $fecha_nacimiento, string $genero, string $direccion, string $dni): bool
    {
        try {
            $pdo = BaseDatos::pdo();
            $stmt = $pdo->prepare(
                "UPDATE pacientes
                 SET fecha_nacimiento = :fecha_nacimiento,
                     genero = :genero,
                     direccion = :direccion,
                     dni = :dni
                 WHERE id = :id"
            );

            return $stmt->execute([
                ':fecha_nacimiento' => $fecha_nacimiento,
                ':genero'           => $genero,
                ':direccion'        => $direccion,
                ':dni'              => $dni,
                ':id'               => $id
            ]);
        } catch (PDOException $e) {
            error_log("❌ Error en Paciente::actualizar(): " . $e->getMessage());
            return false;
        }
    }

    // =============================
    // 🗑️ Eliminar paciente
    // =============================
    public static function eliminar(int $usuario_id): bool
    {
        try {
            $pdo = BaseDatos::pdo();
            $stmt = $pdo->prepare("DELETE FROM pacientes WHERE usuario_id = :usuario_id");
            return $stmt->execute([':usuario_id' => $usuario_id]);
        } catch (PDOException $e) {
            error_log("❌ Error en Paciente::eliminar(): " . $e->getMessage());
            return false;
        }
    }

    public static function contarPacientes() {
    $db = BaseDatos::pdo();
    $stmt = $db->query("SELECT COUNT(*) FROM pacientes");
    return $stmt->fetchColumn();
}

}
