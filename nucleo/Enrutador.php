<?php

/**
 * 🚦 Clase Enrutador
 * Se encarga de recibir una acción (por ejemplo, "login", "registro") y ejecutar el controlador y método correspondiente.
 * Permite modularizar el flujo del sistema sin usar frameworks externos.
 */
class Enrutador {
    /**
     * 📌 Resuelve una acción y ejecuta el controlador correspondiente
     * @param string $accion Acción recibida por GET (ej: "login", "registro")
     * @param array $rutas Mapa de acciones => [ClaseControlador, metodo]
     */
    public static function resolver($accion, $rutas)
    {
        // ✅ Verifica si la acción existe en el mapa de rutas
        if (isset($rutas[$accion])) {
            // 🔄 Extrae la clase del controlador y el método a ejecutar
            [$controladorClase, $metodo] = $rutas[$accion];

            // 🧱 Crea una instancia del controlador
            $controlador = new $controladorClase();

            // ✅ Verifica si el método existe en el controlador
            if (method_exists($controlador, $metodo)) {
                // 🚀 Ejecuta el método correspondiente
                $controlador->$metodo();
            } else {
                // ⚠️ Muestra error si el método no existe
                self::error("El método <b>$metodo</b> no existe en el controlador <b>$controladorClase</b>.");
            }
        } else {
            // ⚠️ Muestra error si la acción no está definida
            self::error("No se encontró la acción <b>$accion</b>.");
        }
    }

    /**
     * 📌 Manejo de errores básicos del enrutador
     * @param string $mensaje Mensaje de error a mostrar
     */
    private static function error($mensaje)
    {
        http_response_code(404); // 🧨 Código HTTP 404 (no encontrado)
        echo "<h1>🚨 Error en el enrutador</h1>";
        echo "<p>$mensaje</p>";
        exit; // ⛔ Detiene la ejecución
    }
}
