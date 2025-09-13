<?php

require_once __DIR__ . "/../../nucleo/BaseDatos.php";

class Usuario {
    private int $id;
    private string $nombre;
    private string $email;
    private string $password;
    private string $telefono;
    private int $rol_id;

    public function __construct(array $data = []) {
        if ($data) {
            $this->id       = $data['id'] ?? 0;
            $this->nombre   = $data['nombre'] ?? '';
            $this->email    = $data['email'] ?? '';
            $this->password = $data['password'] ?? '';
            $this->telefono = $data['telefono'] ?? '';
            $this->rol_id   = $data['rol_id'] ?? 0;
        }
    }

    // 🔹 Buscar usuario por email
    public static function buscarPorEmail(string $email): ?Usuario {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch();

        return $data ? new Usuario($data) : null;
    }

    // 🔹 Crear nuevo usuario
    public function crearUsuario(): bool {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare(
            "INSERT INTO usuarios (nombre, email, password, telefono, rol_id) 
             VALUES (:nombre, :email, :password, :telefono, :rol_id)"
        );

        return $stmt->execute([
            'nombre'   => $this->nombre,
            'email'    => $this->email,
            'password' => password_hash($this->password, PASSWORD_BCRYPT),
            'telefono' => $this->telefono,
            'rol_id'   => $this->rol_id
        ]);
    }

    // 🔹 Verificar contraseña
    public function verificarContraseña(string $password): bool {
        return password_verify($password, $this->password);
    }

    // 🔹 Obtener todos los usuarios
    public static function obtenerTodos(): array {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->query("SELECT * FROM usuarios ORDER BY id ASC");
        $usuarios = [];

        while ($fila = $stmt->fetch()) {
            $usuarios[] = new Usuario($fila);
        }

        return $usuarios;
    }

    // =====================
    // 📌 Getters
    // =====================
    public function getId(): int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    public function getEmail(): string { return $this->email; }
    public function getTelefono(): string { return $this->telefono; }
    public function getRolId(): int { return $this->rol_id; }
}
