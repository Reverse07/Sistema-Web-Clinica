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

    public function crear(): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("INSERT INTO recetas (doctor_id, paciente_id, fecha_emision, observaciones)
                               VALUES (:doctor_id, :paciente_id, CURRENT_TIMESTAMP, :observaciones)");
        $ok = $stmt->execute([
            ':doctor_id' => $this->doctor_id,
            ':paciente_id' => $this->paciente_id,
            ':observaciones' => $this->observaciones
        ]);
        if ($ok) $this->id = (int)$pdo->lastInsertId();
        return $ok;
    }

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

    public static function buscarPorId(int $id): ?Receta
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("SELECT * FROM recetas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? new Receta($fila) : null;
    }

    public static function eliminar(int $id): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("DELETE FROM recetas WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Obtener detalles asociados
    public function obtenerDetalles(): array
    {
        return DetalleReceta::obtenerPorReceta($this->id);
    }

    public function getId(): int { return $this->id; }
}
