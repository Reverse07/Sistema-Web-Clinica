<?php
// 📦 Carga las constantes de configuración de la base de datos (host, nombre, usuario, contraseña)
require_once __DIR__ . "/../configuracion/base_datos.php";

/**
 * 🧠 Clase BaseDatos
 * La clase BaseDatos en PHP crea y gestiona una única conexión segura a PostgreSQL usando PDO. Evita repetir código, mejora la seguridad y permite acceder a la base de datos desde cualquier parte del sistema con BaseDatos::pdo(). Ideal para proyectos modulares y escalables.
 * Usa patrón Singleton para evitar múltiples conexiones y centralizar el acceso.
 */
class BaseDatos {
    // 🔒 Propiedad estática para almacenar la instancia PDO (solo se crea una vez)
    private static ?PDO $pdo = null;

    /**
     * 🔌 Método público para obtener la instancia PDO
     * Si no existe, la crea con configuración segura y la devuelve.
     */
    public static function pdo(): PDO {
        if (self::$pdo === null) {
            try {
                // 🛠️ Crea nueva conexión PDO usando PostgreSQL y constantes de configuración
                $pdo = new PDO(
                    "pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                    DB_USER,
                    DB_PASS
                );

                // ⚙️ Configura atributos de seguridad y manejo de errores
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Lanza excepciones
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Devuelve arrays asociativos
                $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Usa prepares reales (más seguro)

                // 🧱 Guarda la instancia para reutilizarla
                self::$pdo = $pdo;
            } catch (PDOException $e) {
                // 🚨 Muestra mensaje si falla la conexión
                die("Error en la conexión: " . $e->getMessage());
            }
        }

        // 🔁 Devuelve la instancia PDO lista para usar
        return self::$pdo;
    }
}
