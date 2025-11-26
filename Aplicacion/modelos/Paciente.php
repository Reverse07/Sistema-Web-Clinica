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
    // 📌 GETTERS MEJORADOS (manejan null)
    // =============================
    public function getId(): int { return $this->id; }
    public function getUsuarioId(): int { return $this->usuario_id; }
    public function getFechaNacimiento(): string { return $this->fecha_nacimiento ?? ''; }
    public function getGenero(): string { return $this->genero ?? ''; }
    public function getDireccion(): string { return $this->direccion ?? ''; }
    public function getDni(): string { return $this->dni ?? ''; }
    public function getUsuario(): Usuario { return $this->usuario; }

    // =============================
    // 📋 Obtener todos los pacientes COMO ARRAY (para la vista actual)
    // =============================
    public static function todosArray(): array
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->query("SELECT p.*, u.nombre, u.email, u.telefono 
                             FROM pacientes p
                             JOIN usuarios u ON p.usuario_id = u.id
                             ORDER BY p.id ASC");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =============================
    // 📝 Crear nuevo paciente
    // =============================
public static function crear(int $usuario_id, string $fecha_nacimiento, string $genero, string $direccion, string $dni): bool
{
    try {
        $pdo = BaseDatos::pdo();

        // Convertir valores vacíos a NULL
        $fecha_nacimiento = ($fecha_nacimiento === '' || $fecha_nacimiento === null) ? null : $fecha_nacimiento;
        $genero           = ($genero === '') ? null : $genero;
        $direccion        = ($direccion === '') ? null : $direccion;
        $dni              = ($dni === '') ? null : $dni;

        $stmt = $pdo->prepare(
            "INSERT INTO pacientes (usuario_id, fecha_nacimiento, genero, direccion, dni)
             VALUES (:usuario_id, :fecha_nacimiento, :genero, :direccion, :dni)"
        );

        $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(':fecha_nacimiento', $fecha_nacimiento, $fecha_nacimiento ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':genero', $genero, $genero ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':direccion', $direccion, $direccion ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':dni', $dni, $dni ? PDO::PARAM_STR : PDO::PARAM_NULL);

        return $stmt->execute();

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

 public static function contarPacientes(): int
{
    try {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->query("SELECT COUNT(*) FROM pacientes");
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error contarPacientes: " . $e->getMessage());
        return 0;
    }
}

/**
 * Busca un paciente por su ID
 * @param int $id
 * @return Paciente|null
 */
public static function buscarPorId(int $id): ?Paciente
{
    $pdo = BaseDatos::pdo();
    
    $sql = "
        SELECT 
            p.id,
            p.usuario_id,
            p.fecha_nacimiento,
            p.direccion,
            p.historial_medico,
            u.nombre,
            u.email,
            u.telefono,
            u.rol
        FROM pacientes p
        INNER JOIN usuarios u ON p.usuario_id = u.id
        WHERE p.id = :id
        LIMIT 1
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$row) {
        return null;
    }
    
    // Crear objeto Usuario
    $usuario = new Usuario(
        $row['usuario_id'],
        $row['nombre'],
        $row['email'],
        '', // password no es necesario aquí
        $row['rol'],
        $row['telefono']
    );
    
    // Crear objeto Paciente
    return new Paciente(
        $row['id'],
        $usuario,
        $row['fecha_nacimiento'],
        $row['direccion'],
        $row['historial_medico']
    );
}

}
