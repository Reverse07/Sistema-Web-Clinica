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

    // ========================================
    // CRUD
    // ========================================

    /**
     * Crea un detalle de receta (método de instancia)
     */
    private function guardar(): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("
            INSERT INTO detalle_recetas (receta_id, medicamento_id, dosis, frecuencia, duracion, cantidad)
            VALUES (:receta_id, :medicamento_id, :dosis, :frecuencia, :duracion, :cantidad)
        ");
        $ok = $stmt->execute([
            ':receta_id' => $this->receta_id,
            ':medicamento_id' => $this->medicamento_id,
            ':dosis' => $this->dosis,
            ':frecuencia' => $this->frecuencia,
            ':duracion' => $this->duracion,
            ':cantidad' => $this->cantidad
        ]);
        if ($ok) {
            $this->id = (int)$pdo->lastInsertId();
        }
        return $ok;
    }

    /**
     * Método estático para crear desde array (usar en controladores)
     */
    public static function crear(array $data): bool
    {
        $detalle = new DetalleReceta($data);
        return $detalle->guardar();
    }

    // ========================================
    // CONSULTAS
    // ========================================

    /**
     * Obtiene todos los detalles de una receta (objetos DetalleReceta)
     */
    public static function obtenerPorReceta(int $receta_id): array
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("SELECT * FROM detalle_recetas WHERE receta_id = :receta_id");
        $stmt->execute([':receta_id' => $receta_id]);
        $lista = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lista[] = new DetalleReceta($fila);
        }
        return $lista;
    }

    /**
     * Obtiene detalles de una receta con información del medicamento
     */
    public static function obtenerPorRecetaConMedicamento(int $receta_id): array
    {
        $pdo = BaseDatos::pdo();
        $sql = "
            SELECT 
                dr.id,
                dr.receta_id,
                dr.medicamento_id,
                dr.dosis,
                dr.frecuencia,
                dr.duracion,
                dr.cantidad,
                m.nombre as medicamento_nombre,
                m.presentacion,
                m.descripcion
            FROM detalle_recetas dr
            INNER JOIN medicamentos m ON dr.medicamento_id = m.id
            WHERE dr.receta_id = :receta_id
            ORDER BY dr.id ASC
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':receta_id' => $receta_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Elimina todos los detalles de una receta
     */
    public static function eliminarPorReceta(int $receta_id): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("DELETE FROM detalle_recetas WHERE receta_id = :receta_id");
        return $stmt->execute([':receta_id' => $receta_id]);
    }

    /**
     * Elimina un detalle específico
     */
    public static function eliminar(int $id): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("DELETE FROM detalle_recetas WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Actualiza un detalle de receta
     */
    public function actualizar(): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("
            UPDATE detalle_recetas 
            SET dosis = :dosis,
                frecuencia = :frecuencia,
                duracion = :duracion,
                cantidad = :cantidad
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id' => $this->id,
            ':dosis' => $this->dosis,
            ':frecuencia' => $this->frecuencia,
            ':duracion' => $this->duracion,
            ':cantidad' => $this->cantidad
        ]);
    }

    // ========================================
    // GETTERS
    // ========================================

    public function getId(): int
    {
        return $this->id;
    }

    public function getRecetaId(): int
    {
        return $this->receta_id;
    }

    public function getMedicamentoId(): int
    {
        return $this->medicamento_id;
    }

    public function getDosis(): string
    {
        return $this->dosis;
    }

    public function getFrecuencia(): string
    {
        return $this->frecuencia;
    }

    public function getDuracion(): string
    {
        return $this->duracion;
    }

    public function getCantidad(): int
    {
        return $this->cantidad;
    }

    // ========================================
    // SETTERS
    // ========================================

    public function setDosis(string $dosis): void
    {
        $this->dosis = $dosis;
    }

    public function setFrecuencia(string $frecuencia): void
    {
        $this->frecuencia = $frecuencia;
    }

    public function setDuracion(string $duracion): void
    {
        $this->duracion = $duracion;
    }

    public function setCantidad(int $cantidad): void
    {
        $this->cantidad = $cantidad;
    }
}