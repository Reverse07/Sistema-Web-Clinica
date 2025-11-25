<?php
require_once __DIR__ . "/../../nucleo/BaseDatos.php";

class Medicamento
{
    private int $id = 0;
    private string $nombre = '';
    private string $descripcion = '';
    private string $presentacion = '';
    private int $stock = 0;
    private float $precio = 0.0;

    public function __construct(array $data = [])
    {
        $this->id = (int)($data['id'] ?? 0);
        $this->nombre = $data['nombre'] ?? '';
        $this->descripcion = $data['descripcion'] ?? '';
        $this->presentacion = $data['presentacion'] ?? '';
        $this->stock = (int)($data['stock'] ?? 0);
        $this->precio = (float)($data['precio'] ?? 0);
    }

    public static function obtenerTodos(): array
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->query("SELECT * FROM medicamentos ORDER BY id ASC");
        $lista = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lista[] = new Medicamento($fila);
        }
        return $lista;
    }

    public function crear(): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("INSERT INTO medicamentos (nombre, descripcion, presentacion, stock, precio)
                               VALUES (:nombre, :descripcion, :presentacion, :stock, :precio)");
        $ok = $stmt->execute([
            ':nombre' => $this->nombre,
            ':descripcion' => $this->descripcion,
            ':presentacion' => $this->presentacion,
            ':stock' => $this->stock,
            ':precio' => $this->precio
        ]);
        if ($ok) $this->id = (int)$pdo->lastInsertId();
        return $ok;
    }

    public static function buscarPorId(int $id): ?Medicamento
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("SELECT * FROM medicamentos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? new Medicamento($fila) : null;
    }

    public static function actualizar(int $id, array $data): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("UPDATE medicamentos 
                               SET nombre = :nombre, descripcion = :descripcion, presentacion = :presentacion, stock = :stock, precio = :precio
                               WHERE id = :id");
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'],
            ':presentacion' => $data['presentacion'],
            ':stock' => $data['stock'],
            ':precio' => $data['precio'],
            ':id' => $id
        ]);
    }

    public static function eliminar(int $id): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("DELETE FROM medicamentos WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function todos(): array
{
    $pdo = BaseDatos::pdo();
    $stmt = $pdo->query("SELECT * FROM medicamentos ORDER BY id ASC");

    $lista = [];
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $lista[] = new Medicamento($fila);
    }

    return $lista;
}


    // Getters
    public function getId(): int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
}
