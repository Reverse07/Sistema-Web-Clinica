<?php

class Enrutador
{
    /**
     * 📌 Resuelve una acción y ejecuta el controlador correspondiente
     * @param string $accion Acción recibida por GET (ej: "login", "registro")
     * @param array $rutas Mapa de acciones => [ClaseControlador, metodo]
     */
    public static function resolver($accion, $rutas)
    {
        if (isset($rutas[$accion])) {
            [$controladorClase, $metodo] = $rutas[$accion];

            // Crear instancia del controlador
            $controlador = new $controladorClase();

            if (method_exists($controlador, $metodo)) {
                // Ejecutar el método del controlador
                $controlador->$metodo();
            } else {
                self::error("El método <b>$metodo</b> no existe en el controlador <b>$controladorClase</b>.");
            }
        } else {
            self::error("No se encontró la acción <b>$accion</b>.");
        }
    }

    /**
     * 📌 Manejo de errores básicos del enrutador
     */
    private static function error($mensaje)
    {
        http_response_code(404);
        echo "<h1>🚨 Error en el enrutador</h1>";
        echo "<p>$mensaje</p>";
        exit;
    }
}
