<?php

// 📦 Importa la clase BaseDatos para acceder a la conexión PDO
require_once __DIR__ . "/../../nucleo/BaseDatos.php";

// 👤 Clase que representa a un usuario del sistema clínico
class Usuario
{

    // 🆔 Identificador único del usuario
    private int $id;

    // 📛 Nombre completo del usuario
    private string $nombre;

    // 📧 Correo electrónico del usuario
    private string $email;

    // 🔐 Contraseña encriptada del usuario
    private string $password;

    // 📞 Teléfono de contacto del usuario
    private string $telefono;

    // 🧩 ID del rol asignado (admin, doctor, paciente)
    private int $rol_id;

    // 🩺 Especialidad médica del doctor (solo si rol_id = 2)
    private ?string $especialidad = null;



    // 🏗️ Constructor que recibe un array de datos para inicializar el objeto
    public function __construct(array $data = [])
    {
        if ($data) {
            $this->id           = $data['id'] ?? 0;
            $this->nombre       = $data['nombre'] ?? '';
            $this->email        = $data['email'] ?? '';
            $this->password     = $data['password'] ?? '';
            $this->telefono     = $data['telefono'] ?? '';
            $this->rol_id       = $data['rol_id'] ?? 0;
            $this->especialidad = $data['especialidad'] ?? 'No definida'; // ✅ Esta línea es clave
        }
    }


    // 🔍 Busca un usuario por su email en la base de datos
    public static function buscarPorEmail(string $email): ?Usuario
    {
        $pdo = BaseDatos::pdo(); // 🔌 Obtiene la conexión PDO
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1"); // 📄 Prepara consulta
        $stmt->execute(['email' => $email]); // 🚀 Ejecuta con parámetro seguro
        $data = $stmt->fetch(); // 📥 Obtiene los datos

        return $data ? new Usuario($data) : null; // 🔁 Devuelve instancia o null si no existe
    }

    // 📝 Crea un nuevo usuario en la base de datos
    public function crearUsuario(): bool
    {
        try {
            $pdo = BaseDatos::pdo(); // 🔌 Conexión PDO
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
        } catch (PDOException $e) {
            // 👀 Mostrar error exacto
            echo "❌ Error en crearUsuario(): " . $e->getMessage();
            return false;
        }
    }


    // 🔐 Verifica si la contraseña ingresada coincide con la almacenada
    public function verificarContraseña(string $password): bool
    {
        return password_verify($password, $this->password); // 🔍 Compara usando hash seguro
    }

    // 📋 Obtiene todos los usuarios registrados en la base de datos
    public static function obtenerTodos(): array
    {
        $pdo = BaseDatos::pdo(); // 🔌 Conexión PDO
        $stmt = $pdo->query("SELECT * FROM usuarios ORDER BY id ASC"); // 📄 Consulta todos los usuarios
        $usuarios = []; // 📦 Array para almacenar resultados

        // 🔁 Recorre cada fila y crea una instancia de Usuario
        while ($fila = $stmt->fetch()) {
            $usuarios[] = new Usuario($fila); // ➕ Agrega al array
        }

        return $usuarios; // 📤 Devuelve el array completo
    }

    // 🔢 Cuenta todos los usuarios registrados
    public static function contarTodos(): int
    {
        $pdo = BaseDatos::pdo();
        $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
        return (int) $stmt->fetchColumn();
    }

    // 🔢 Cuenta usuarios por tipo de rol (admin, doctor, paciente)
    public static function contarPorRol(string $rolNombre): int
    {
        $pdo = BaseDatos::pdo();

        // 🧠 Asume que tienes una tabla 'roles' con nombre y id
        $stmtRol = $pdo->prepare("SELECT id FROM roles WHERE nombre = :nombre LIMIT 1");
        $stmtRol->execute(['nombre' => $rolNombre]);
        $rolId = $stmtRol->fetchColumn();

        if (!$rolId) return 0;

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE rol_id = :rol_id");
        $stmt->execute(['rol_id' => $rolId]);
        return (int) $stmt->fetchColumn();
    }


    public static function buscarPorId(int $id): ?Usuario
    {
        $pdo = BaseDatos::pdo();
        $sql = "SELECT * FROM usuarios WHERE id = :id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $datos = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($datos) {
            return new Usuario($datos); // Asumiendo que tienes un constructor que acepta array
        }

        return null;
    }


    // 🔍 Obtiene todos los usuarios que tienen un rol específico
    public static function obtenerPorRol(string $rolNombre): array
    {
        $pdo = BaseDatos::pdo();

        // 🧠 Si tienes tabla 'roles', busca el ID por nombre
        $stmtRol = $pdo->prepare("SELECT id FROM roles WHERE nombre = :nombre LIMIT 1");
        $stmtRol->execute(['nombre' => $rolNombre]);
        $rolId = $stmtRol->fetchColumn();

        if (!$rolId) return [];

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE rol_id = :rol_id ORDER BY id ASC");
        $stmt->execute(['rol_id' => $rolId]);

        $usuarios = [];
        while ($fila = $stmt->fetch()) {
            $usuarios[] = new Usuario($fila);
        }

        return $usuarios;
    }

    public static function insertarDoctor($nombre, $email, $telefono)
    {
        $sql = "INSERT INTO usuarios (nombre, email, telefono, rol_id, especialidad)
            VALUES (:nombre, :email, :telefono, 2, 'No definida')";

        $stmt = BaseDatos::pdo()->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':telefono' => $telefono
        ]);
    }

    public function getEspecialidad()
    {
        return $this->especialidad ?? 'No definida';
    }

    public static function actualizarDoctor($id, $nombre, $email, $telefono, $especialidad)
    {
        $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, telefono = :telefono, especialidad = :especialidad WHERE id = :id";
        $stmt = BaseDatos::pdo()->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':telefono' => $telefono,
            ':especialidad' => $especialidad,
            ':id' => $id
        ]);
    }



    // =====================
    // 📌 Métodos Getters
    // =====================

    // 🔙 Devuelve el ID del usuario
    public function getId(): int
    {
        return $this->id;
    }

    // 🔙 Devuelve el nombre del usuario
    public function getNombre(): string
    {
        return $this->nombre;
    }

    // 🔙 Devuelve el email del usuario
    public function getEmail(): string
    {
        return $this->email;
    }

    // 🔙 Devuelve el teléfono del usuario
    public function getTelefono(): string
    {
        return $this->telefono;
    }

    // 🔙 Devuelve el ID del rol del usuario
    public function getRolId(): int
    {
        return $this->rol_id;
    }
}
