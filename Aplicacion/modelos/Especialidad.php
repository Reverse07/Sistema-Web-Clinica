<?php

require_once __DIR__ . "/../../nucleo/BaseDatos.php";

class Especialidad
{
    private int $id;
    private string $nombre;

    public function __construct(array $data = [])
    {
        if ($data) {
            $this->id = $data['id'] ?? 0;
            $this->nombre = $data['nombre'] ?? '';
        }
    }

    // 🔍 Obtener todas las especialidades
  public static function todas()
{
    $sql = "SELECT * FROM especialidades ORDER BY nombre ASC";
    $stmt = BaseDatos::pdo()->query($sql);
    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $especialidades = [];
    foreach ($datos as $dato) {
        $especialidades[] = new self($dato);
    }
    
    return $especialidades;
}


    // 🔍 Buscar por ID
    public static function buscarPorId(int $id): ?Especialidad
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("SELECT * FROM especialidades WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new Especialidad($data) : null;
    }

    // 📌 Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }
}
