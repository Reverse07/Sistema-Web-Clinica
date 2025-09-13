<?php
require_once __DIR__ . "/../configuracion/base_datos.php";

class BaseDatos {
    private static ?PDO $pdo = null;

    public static function pdo(): PDO {
        if (self::$pdo === null) {
            try {
                $pdo = new PDO(
                    "pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                    DB_USER,
                    DB_PASS
                );
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

                self::$pdo = $pdo;
            } catch (PDOException $e) {
                die("Error en la conexión: " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
