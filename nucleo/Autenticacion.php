<?php
// 📦 Importa configuración global (como BASE_URL)
require_once __DIR__ . '/../configuracion/app.php';

class Autenticacion
{

    // 🔐 Configura sesión segura
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

            session_start();
        }
    }

    /**
     * Comprueba si el usuario tiene uno o varios roles permitidos.
     *
     * @param string|array $roles
     * @return bool
     */
    public static function tieneRol($roles): bool
    {
        self::iniciarSesionSegura();

        if (empty($_SESSION['rol'])) {
            return false;
        }

        $rolActual = $_SESSION['rol'];

        if (is_string($roles)) {
            return $rolActual === $roles;
        }

        if (is_array($roles)) {
            return in_array($rolActual, $roles, true);
        }

        return false;
    }

    // 🔑 Guarda ID, rol y CSRF al iniciar sesión
    public static function login(int $usuarioId, string $rol): void
    {
        self::iniciarSesionSegura();

        $_SESSION['usuario_id'] = $usuarioId;
        $_SESSION['rol'] = $rol;
        $_SESSION['csrf'] = bin2hex(random_bytes(32));

        session_regenerate_id(true);
    }

    // 🚪 Cierra sesión limpiamente
    public static function logout(): void
    {
        self::iniciarSesionSegura();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    // 🆔 Devuelve el ID del usuario logueado
    public static function usuarioId(): ?int
    {
        self::iniciarSesionSegura();
        return $_SESSION['usuario_id'] ?? null;
    }

    // 🔒 Exige que el usuario tenga ciertos roles
    public static function requiereRoles(array $roles): void
    {
        self::iniciarSesionSegura();

        if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $roles, true)) {
            header("Location: " . BASE_URL . "/publico/index.php");
            exit;
        }
    }

    // 🏷️ Devuelve el rol actual
    public static function rol(): ?string
    {
        self::iniciarSesionSegura();
        return $_SESSION['rol'] ?? null;
    }

    // 🔐 Obliga a que el usuario esté logueado
    public static function requiereLogin(): void
    {
        self::iniciarSesionSegura();

        if (empty($_SESSION['usuario_id'])) {
            header("Location: " . BASE_URL . "/publico/login.php");
            exit;
        }
    }

    // 🔑 Obtiene o genera token CSRF
    public static function tokenCsrf(): string
    {
        self::iniciarSesionSegura();

        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf'];
    }

    // 👤 Obtiene al usuario logueado desde la base de datos
    public static function usuario()
    {
        self::iniciarSesionSegura();

        if (!isset($_SESSION['usuario_id'])) {
            return null;
        }

        // 📌 Ruta corregida: modelos está dentro de Aplicacion/
        require_once __DIR__ . '/../Aplicacion/modelos/Usuario.php';

        return Usuario::buscarPorId($_SESSION['usuario_id']);
    }

    // 🛡️ Verifica token CSRF
    public static function verificarCsrf(?string $token): bool
    {
        self::iniciarSesionSegura();

        return isset($_SESSION['csrf'], $token)
            && hash_equals($_SESSION['csrf'], $token);
    }
}

