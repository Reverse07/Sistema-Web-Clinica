<?php
// 📦 Importa configuración global (como BASE_URL)
require_once __DIR__ . '/../configuracion/app.php';

// 🧠La clase Autenticacion centraliza el manejo seguro de sesiones en PHP. Permite iniciar sesión, verificar roles, proteger formularios con tokens CSRF y cerrar sesión correctamente.
class Autenticacion {

    // 🔐 Configura sesión segura con cookies protegidas
    public static function iniciarSesionSegura() : void {
    // ✅ Solo configurar si la sesión aún no está activa
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
    public static function login(int $usuarioId, String $rol) : void {
        self::iniciarSesionSegura(); // Asegura sesión segura
        $_SESSION['usuario_id'] = $usuarioId; // Guarda ID del usuario
        $_SESSION['rol'] = $rol;              // Guarda rol del usuario
        $_SESSION['csrf'] = bin2hex(random_bytes(32)); // Genera token CSRF único
        session_regenerate_id(true); // Regenera ID de sesión para evitar fijación
    }

    // 🚪 Cierra sesión limpiando variables y destruyendo sesión
    public static function logout(): void {
        self::iniciarSesionSegura(); // Asegura sesión activa
        $_SESSION = [];              // Limpia variables de sesión

        // 🧼 Elimina cookie de sesión si está habilitada
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy(); // 🧨 Destruye sesión completamente
    }

    // 🆔 Devuelve el ID del usuario si está logueado
    public static function usuarioId() : ?int {
        self::iniciarSesionSegura(); // Asegura sesión activa
        return $_SESSION['usuario_id'] ?? null; // Retorna ID o null
    }

    // 🔐 Verifica si el usuario tiene alguno de los roles requeridos
    public static function requiereRoles(array $roles) : void {
        self::iniciarSesionSegura(); // Asegura sesión activa

        // 🚫 Redirige si el rol no está definido o no es válido
        if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $roles, true)) {
            header("Location: " . BASE_URL . "/publico/index.php");
            exit;
        }
    }

    // 🛡️ Devuelve el token CSRF actual o lo genera si no existe
    public static function tokenCsrf(): string {
        self::iniciarSesionSegura(); // Asegura sesión activa

        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32)); // Genera nuevo token
        }

        return $_SESSION['csrf']; // Retorna token CSRF
    }

    // ✅ Verifica si el token CSRF recibido es válido
    public static function verificarCsrf(?string $token): bool {
        self::iniciarSesionSegura(); // Asegura sesión activa

        // Compara token recibido con el almacenado usando comparación segura
        return isset($_SESSION['csrf'], $token) && hash_equals($_SESSION['csrf'], $token);
    }
}
