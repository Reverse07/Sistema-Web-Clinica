<?php
// 📦 Importa la clase BaseDatos para acceder a la conexión PDO
require_once __DIR__ . "/../../nucleo/BaseDatos.php";

/**
 * Clase Usuario
 * 
 * Representa a un usuario en el sistema.
 * Proporciona métodos CRUD y utilidades para autenticación y gestión de roles.
 */
class Usuario
{
    // =====================
    // Propiedades
    // =====================
    private int $id = 0;
    private string $nombre = '';
    private string $email = '';
    private string $password = '';
    private string $telefono = '';
    private int $rol_id = 0;

    // =====================
    // Constructor
    // =====================
    public function __construct(array $data = [])
    {
        $this->id = (int) ($data['id'] ?? $data['ID'] ?? 0);
        $this->nombre = $data['nombre'] ?? $data['name'] ?? '';
        $this->email = $data['email'] ?? $data['correo'] ?? '';
        $this->password = $data['password'] ?? $data['passwd'] ?? '';
        $this->telefono = $data['telefono'] ?? $data['telefono_contacto'] ?? '';
        $this->rol_id = (int) ($data['rol_id'] ?? $data['role_id'] ?? 0);
    }

    // =====================
    // Métodos estáticos CRUD
    // =====================

    /**
     * Busca un usuario por email
     */
    public static function buscarPorEmail(string $email): ?Usuario
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? new Usuario($fila) : null;
    }

    /**
     * Crea un nuevo usuario en la base de datos
     */
    public function crearUsuario(): bool
    {
        try {
            $pdo = BaseDatos::pdo();
            $stmt = $pdo->prepare(
                "INSERT INTO usuarios (nombre, email, password, telefono, rol_id) 
                 VALUES (:nombre, :email, :password, :telefono, :rol_id)"
            );

            $pwd = $this->password;
            if (!empty($pwd) && !str_starts_with($pwd, '$2y$') && !str_starts_with($pwd, '$2a$')) {
                $pwd = password_hash($pwd, PASSWORD_BCRYPT);
            }

            $ok = $stmt->execute([
                ':nombre' => $this->nombre,
                ':email' => $this->email,
                ':password' => $pwd,
                ':telefono' => $this->telefono,
                ':rol_id' => $this->rol_id
            ]);

            if ($ok) {
                $this->id = (int) $pdo->lastInsertId();
            }

            return (bool) $ok;
        } catch (PDOException $e) {
            error_log("Usuario::crearUsuario error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Devuelve todos los usuarios
     */
    public static function obtenerTodos(): array
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->query("SELECT * FROM usuarios ORDER BY id ASC");
        $usuarios = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = new Usuario($fila);
        }
        return $usuarios;
    }

    /**
     * Busca un usuario por ID
     */
    public static function buscarPorId(int $id): ?Usuario
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? new Usuario($fila) : null;
    }

    /**
     * Actualiza datos de un usuario
     */
    public static function actualizarUsuario($id, $nombre, $email, $telefono, $password = null): bool
    {
        $pdo = BaseDatos::pdo();
        try {
            $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, telefono = :telefono";
            $params = [
                ':nombre' => $nombre,
                ':email' => $email,
                ':telefono' => $telefono,
                ':id' => $id
            ];

            if (!empty($password)) {
                $sql .= ", password = :password";
                $params[':password'] = str_starts_with($password, '$2y$') || str_starts_with($password, '$2a$')
                    ? $password
                    : password_hash($password, PASSWORD_BCRYPT);
            }

            $sql .= " WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return (bool) $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Usuario::actualizarUsuario error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un usuario por ID
     */
    public static function eliminarUsuario(int $id): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        return (bool) $stmt->execute([':id' => $id]);
    }

    // =====================
    // Métodos adicionales
    // =====================

    /**
     * Cambia el rol de un usuario
     */
    public static function cambiarRol(int $id, int $rol_id): bool
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->prepare("UPDATE usuarios SET rol_id = :rol_id WHERE id = :id");
        return (bool) $stmt->execute([':rol_id' => $rol_id, ':id' => $id]);
    }

    /**
     * Contar todos los usuarios
     */
    public static function contarTodos(): int
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Contar usuarios por rol
     */
    public static function contarPorRol($rol): int
    {
        $pdo = BaseDatos::pdo();

        if (is_numeric($rol)) {
            $rolId = (int) $rol;
        } else {
            $stmtRol = $pdo->prepare("SELECT id FROM roles WHERE nombre = :nombre LIMIT 1");
            $stmtRol->execute([':nombre' => $rol]);
            $rolId = $stmtRol->fetchColumn();
        }

        if (!$rolId) return 0;

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE rol_id = :rol_id");
        $stmt->execute([':rol_id' => $rolId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Obtener usuarios por rol
     */
    public static function obtenerPorRol($rol): array
    {
        $pdo = BaseDatos::pdo();

        if (is_numeric($rol)) {
            $rolId = (int) $rol;
        } else {
            $stmtRol = $pdo->prepare("SELECT id FROM roles WHERE nombre = :nombre LIMIT 1");
            $stmtRol->execute([':nombre' => $rol]);
            $rolId = $stmtRol->fetchColumn();
        }

        if (!$rolId) return [];

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE rol_id = :rol_id ORDER BY id ASC");
        $stmt->execute([':rol_id' => $rolId]);

        $usuarios = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = new Usuario($fila);
        }

        return $usuarios;
    }

    /**
     * Verificar contraseña en login
     */
    public function verificarContraseña(string $password): bool
    {
        if (empty($this->password)) return false;
        return password_verify($password, $this->password);
    }

        public static function eliminarPaciente(int $id): bool
    {
        $pdo = BaseDatos::pdo();
        try {
            // 🚨 Inicia transacción
            $pdo->beginTransaction();

            // 🧼 Eliminar primero el registro en pacientes
            $stmtPaciente = $pdo->prepare("DELETE FROM pacientes WHERE usuario_id = :id");
            $stmtPaciente->execute([':id' => $id]);

            // 🧼 Luego eliminar el usuario
            $stmtUsuario = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmtUsuario->execute([':id' => $id]);

            // ✅ Confirma cambios
            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Usuario::eliminarPaciente error: " . $e->getMessage());
            return false;
        }
    }

     public static function eliminarDoctor(int $id): bool
    {
        $pdo = BaseDatos::pdo();
        try {
            // 🚨 Inicia transacción
            $pdo->beginTransaction();

            // 🧼 Eliminar primero el registro en doctores
            $stmtDoctor = $pdo->prepare("DELETE FROM doctores WHERE usuario_id = :id");
            $stmtDoctor->execute([':id' => $id]);

            // 🧼 Luego eliminar el usuario
            $stmtUsuario = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmtUsuario->execute([':id' => $id]);

            // ✅ Confirma cambios
            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Usuario::eliminarDoctor error: " . $e->getMessage());
            return false;
        }
    }


    // =====================
    // Getters
    // =====================
    public function getId(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelefono(): string
    {
        return $this->telefono;
    }

    public function getRolId(): int
    {
        return $this->rol_id;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    // =====================
    // Setters
    // =====================
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
