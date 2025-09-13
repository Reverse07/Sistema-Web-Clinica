<?php

require_once __DIR__ . "/../../nucleo/BaseDatos.php";  // Incluye la clase BaseDatos desde un directorio superior

class Rol {
    private int $id;             // Propiedad privada para el ID del rol
    private string $nombre;     // Propiedad privada para el nombre del rol

    // Constructor para inicializar el objeto con un array asociativo (por ejemplo, datos de la base de datos)
    public function __construct(array $data = []) {
        if ($data) {                                               // Si se proporcionaron datos
            $this->id = $data['id'] ?? 0;                          // Asigna el ID si existe, o 0 si no
            $this->nombre = $data['nombre'] ?? '';                // Asigna el nombre si existe, o cadena vacía si no
        }
    }

    // =====================
    // 📌 Métodos CRUD
    // =====================

    // 🔹 Buscar rol por ID (método estático porque no necesita una instancia creada)
    public static function buscarPorId(int $id): ?Rol {
        $pdo = BaseDatos::pdo();                                   // Obtiene la conexión PDO desde BaseDatos
        $stmt = $pdo->prepare("SELECT * FROM roles WHERE id = :id LIMIT 1");  // Prepara la consulta SQL con marcador
        $stmt->execute(['id' => $id]);                             // Ejecuta la consulta pasando el ID
        $data = $stmt->fetch();                                    // Obtiene el resultado como array asociativo

        return $data ? new Rol($data) : null;                      // Si hay resultado, devuelve un objeto Rol, si no, null
    }

    // 🔹 Obtener todos los roles
    public static function obtenerTodos(): array {
        $pdo = BaseDatos::pdo();                                   // Obtiene la conexión PDO
        $stmt = $pdo->query("SELECT * FROM roles ORDER BY id ASC"); // Ejecuta la consulta para obtener todos los roles
        $roles = [];                                               // Inicializa un array para almacenar los roles

        while ($fila = $stmt->fetch()) {                           // Recorre cada fila del resultado
            $roles[] = new Rol($fila);                             // Crea un nuevo objeto Rol y lo agrega al array
        }

        return $roles;                                             // Devuelve el array con los roles
    }

    // 🔹 Crear nuevo rol
    public function crearRol(): bool {
        $pdo = BaseDatos::pdo();                                   // Obtiene la conexión PDO
        $stmt = $pdo->prepare("INSERT INTO roles (nombre) VALUES (:nombre)"); // Prepara la consulta SQL de inserción
        return $stmt->execute(['nombre' => $this->nombre]);        // Ejecuta la consulta con el nombre del rol y retorna true/false
    }

    // =====================
    // 📌 Getters
    // =====================

    public function getId(): int {
        return $this->id;                                          // Devuelve el ID del rol
    }

    public function getNombre(): string {
        return $this->nombre;                                      // Devuelve el nombre del rol
    }
}