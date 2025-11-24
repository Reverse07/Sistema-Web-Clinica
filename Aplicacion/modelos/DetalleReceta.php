<?php
require_once __DIR__ . "/../../nucleo/BaseDatos.php";

class DetalleReceta
{
    private int $id = 0;
    private int $receta_id = 0;
    private int $medicamento_id = 0;
    private string $dosis = '';
    private string $frecuencia = '';
    private string $duracion = '';
    private int $cantidad = 1;

    public function __construct(array $data = [])
    {
        $this->id = (int)($data['id'] ?? 0);
        $this->receta_id = (int)($data['receta_id'] ?? 0);
        $this->medicamento_id = (int)($data['medicamento_id'] ?? 0);
        $this->dosis = $data['dosis'] ?? '';
        $this->frecuencia = $data['frecuencia'] ?? '';
        $this->duracion = $data['duracion'] ?? '';
        $this->cantidad = (int)($data['cantidad'] ?? 1);
    }

    public function crear(): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("INSERT INTO detalle_receta (receta_id, medicamento_id, dosis, frecuencia, duracion, cantidad)
                               VALUES (:receta_id, :medicamento_id, :dosis, :frecuencia, :duracion, :cantidad)");
        return $stmt->execute([
            ':receta_id' => $this->receta_id,
            ':medicamento_id' => $this->medicamento_id,
            ':dosis' => $this->dosis,
            ':frecuencia' => $this->frecuencia,
            ':duracion' => $this->duracion,
            ':cantidad' => $this->cantidad
        ]);
    }

    public static function obtenerPorReceta(int $receta_id): array
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("SELECT * FROM detalle_receta WHERE receta_id = :receta_id");
        $stmt->execute([':receta_id' => $receta_id]);
        $lista = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lista[] = new DetalleReceta($fila);
        }
        return $lista;
    }

    public static function eliminarPorReceta(int $receta_id): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("DELETE FROM detalle_receta WHERE receta_id = :receta_id");
        return $stmt->execute([':receta_id' => $receta_id]);
    }
}
