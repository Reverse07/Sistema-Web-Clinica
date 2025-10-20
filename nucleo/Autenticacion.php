<?php
// 📦 Importa configuración global (como BASE_URL)
require_once __DIR__ . '/../configuracion/app.php';

class Autenticacion
{

    // 🔐 Configura sesión segura con cookies protegidas
    public static function iniciarSesionSegura(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            session_start(); // 🔄 Inicia sesión después de configurar
        }
    }

    // ✅ Inicia sesión del usuario, guarda ID, rol y token CSRF
    public static function login(int $usuarioId, string $rol): void
    {
        self::iniciarSesionSegura();
        $_SESSION['usuario_id'] = $usuarioId;
        $_SESSION['rol'] = $rol;
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        session_regenerate_id(true);
    }

    // 🚪 Cierra sesión limpiando variables y destruyendo sesión
    public static function logout(): void
    {
        self::iniciarSesionSegura();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
    }

    // 🆔 Devuelve el ID del usuario si está logueado
    public static function usuarioId(): ?int
    {
        self::iniciarSesionSegura();
        return $_SESSION['usuario_id'] ?? null;
    }

    // 🔐 Verifica si el usuario tiene alguno de los roles requeridos
    public static function requiereRoles(array $roles): void
    {
        self::iniciarSesionSegura();

        if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $roles, true)) {
            header("Location: " . BASE_URL . "/publico/index.php");
            exit;
        }
    }

    // ✅ Verifica si el usuario ha iniciado sesión; si no, redirige al login
    public static function requiereLogin(): void
    {
        self::iniciarSesionSegura();

        if (empty($_SESSION['usuario_id'])) {
            header("Location: " . BASE_URL . "/publico/login.php");
            exit;
        }
    }

    // 🛡️ Devuelve el token CSRF actual o lo genera si no existe
    public static function tokenCsrf(): string
    {
        self::iniciarSesionSegura();

        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf'];
    }

    public static function usuario()
    {
        self::iniciarSesionSegura();
        if (!isset($_SESSION['usuario_id'])) {
            return null;
        }

        // ⚠️ Asegúrate de tener la clase Usuario y su método findById()
        require_once __DIR__ . '/../modelos/Usuario.php';
        return Usuario::buscarPorId($_SESSION['usuario_id']);
    }

    // ✅ Verifica si el token CSRF recibido es válido
    public static function verificarCsrf(?string $token): bool
    {
        self::iniciarSesionSegura();
        return isset($_SESSION['csrf'], $token) && hash_equals($_SESSION['csrf'], $token);
    }
}
