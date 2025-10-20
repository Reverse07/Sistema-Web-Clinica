<?php

require_once __DIR__ . "/../../nucleo/BaseDatos.php";

class Doctor
{
    private int $id;
    private int $usuario_id;
    private int $especialidad_id;
    private string $numero_colegiatura;

    public function __construct(array $data = [])
    {
        if ($data) {
            $this->id = $data['id'] ?? 0;
            $this->usuario_id = $data['usuario_id'] ?? 0;
            $this->especialidad_id = $data['especialidad_id'] ?? 0;
            $this->numero_colegiatura = $data['numero_colegiatura'] ?? '';
        }
    }

    // 🔍 Obtener todos los doctores
    public static function todos(): array
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->query("SELECT * FROM doctores ORDER BY id ASC");
        $doctores = [];

        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $doctores[] = new Doctor($fila);
        }

        return $doctores;
    }

    // 🔍 Buscar por ID
    public static function buscarPorUsuarioId($usuarioId)
{
    $sql = "SELECT * FROM doctores WHERE usuario_id = :usuario_id";
    $stmt = BaseDatos::pdo()->prepare($sql);
    $stmt->execute(['usuario_id' => $usuarioId]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($datos) {
        return new self($datos);
    }
    
    return null;
}
    // 📝 Crear nuevo doctor
    public static function crear(int $usuario_id, int $especialidad_id, string $numero_colegiatura): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare(
            "INSERT INTO doctores (usuario_id, especialidad_id, numero_colegiatura)
             VALUES (:usuario_id, :especialidad_id, :numero_colegiatura)"
        );

        return $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':especialidad_id' => $especialidad_id,
            ':numero_colegiatura' => $numero_colegiatura
        ]);
    }

    // ✏️ Actualizar doctor
    public static function actualizar(int $id, int $especialidad_id, string $numero_colegiatura): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare(
            "UPDATE doctores SET especialidad_id = :especialidad_id, numero_colegiatura = :numero_colegiatura WHERE id = :id"
        );

        return $stmt->execute([
            ':especialidad_id' => $especialidad_id,
            ':numero_colegiatura' => $numero_colegiatura,
            ':id' => $id
        ]);
    }

    // 🗑️ Eliminar doctor
    public static function eliminar(int $id): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("DELETE FROM doctores WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

// En Aplicacion/modelos/Doctor.php
public static function todosConUsuario()
{
    $sql = "
        SELECT 
            u.id as usuario_id,
            u.nombre,
            u.email,
            u.telefono,
            d.id as doctor_id,
            d.numero_colegiatura,
            e.nombre as especialidad
        FROM usuarios u
        INNER JOIN doctores d ON u.id = d.usuario_id
        LEFT JOIN especialidades e ON d.especialidad_id = e.id
        WHERE u.rol_id = 2
    ";
    
    $stmt = BaseDatos::pdo()->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // =====================
    // 📌 Getters
    // =====================
    public function getId(): int
    {
        return $this->id;
    }
    public function getUsuarioId(): int
    {
        return $this->usuario_id;
    }
    public function getEspecialidadId(): int
    {
        return $this->especialidad_id;
    }
    public function getNumeroColegiatura(): string
    {
        return $this->numero_colegiatura;
    }
}
