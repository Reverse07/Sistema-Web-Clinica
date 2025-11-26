<?php
/**
 * Servicio para envío de SMS con Twilio
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/BaseDatos.php';

use Twilio\Rest\Client;

class TwilioService
{
    private $client;
    private $phoneNumber;
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../configuracion/twilio.php';
        
        $this->client = new Client(
            $this->config['account_sid'],
            $this->config['auth_token']
        );
        
        $this->phoneNumber = $this->config['phone_number'];
    }

    /**
     * Genera un código aleatorio de 6 dígitos
     */
    private function generarCodigo(): string
    {
        $length = $this->config['code_length'];
        return str_pad((string)random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    /**
     * Envía un SMS con el código de verificación
     */
    public function enviarCodigoVerificacion(string $telefono, string $codigo): bool
    {
        try {
            // Asegurarse de que el teléfono tenga el formato correcto
            $telefonoFormateado = $this->formatearTelefono($telefono);
            
            $mensaje = "Tu código de verificación para Clínica es: {$codigo}\n\n"
                     . "Este código expira en {$this->config['code_expiry_minutes']} minutos.\n"
                     . "No compartas este código con nadie.";
            
            $this->client->messages->create(
                $telefonoFormateado,
                [
                    'from' => $this->phoneNumber,
                    'body' => $mensaje
                ]
            );
            
            error_log("✅ SMS enviado exitosamente a: $telefonoFormateado");
            return true;
            
        } catch (Exception $e) {
            error_log("❌ Error al enviar SMS: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Formatea el número de teléfono al formato internacional
     * Asume que los números sin prefijo son de Perú (+51)
     */
    private function formatearTelefono(string $telefono): string
    {
        // Eliminar espacios y caracteres especiales
        $telefono = preg_replace('/[^0-9+]/', '', $telefono);
        
        // Si ya tiene +, devolverlo tal cual
        if (strpos($telefono, '+') === 0) {
            return $telefono;
        }
        
        // Si empieza con 51, agregar +
        if (strpos($telefono, '51') === 0) {
            return '+' . $telefono;
        }
        
        // Si es un número de 9 dígitos (formato peruano), agregar +51
        if (strlen($telefono) === 9) {
            return '+51' . $telefono;
        }
        
        return $telefono;
    }

    /**
 * Guarda el código de verificación en la base de datos
 */
public function guardarCodigo(string $telefono, string $codigo): bool
{
    try {
        $pdo = BaseDatos::pdo();
        
        error_log("🔍 [TWILIO] Guardando código para teléfono: $telefono");
        
        // Eliminar códigos anteriores del mismo teléfono
        $stmtDelete = $pdo->prepare("DELETE FROM codigos_recuperacion WHERE telefono = :telefono");
        $stmtDelete->execute([':telefono' => $telefono]);
        error_log("✅ [TWILIO] Códigos anteriores eliminados: " . $stmtDelete->rowCount());
        
        // Insertar nuevo código
        $expiracion = date('Y-m-d H:i:s', strtotime("+{$this->config['code_expiry_minutes']} minutes"));
        error_log("🔍 [TWILIO] Expiración: $expiracion");
        
        $stmt = $pdo->prepare("
            INSERT INTO codigos_recuperacion (telefono, codigo, expiracion, usado)
            VALUES (:telefono, :codigo, :expiracion, FALSE)
        ");
        
        $resultado = $stmt->execute([
            ':telefono' => $telefono,
            ':codigo' => $codigo,
            ':expiracion' => $expiracion
        ]);
        
        if ($resultado) {
            error_log("✅ [TWILIO] Código guardado exitosamente");
        } else {
            error_log("❌ [TWILIO] Error al ejecutar INSERT");
        }
        
        return $resultado;
        
    } catch (PDOException $e) {
        error_log("❌ [TWILIO] Error PDO al guardar código: " . $e->getMessage());
        error_log("❌ [TWILIO] SQL State: " . $e->getCode());
        error_log("❌ [TWILIO] Stack trace: " . $e->getTraceAsString());
        return false;
    } catch (Exception $e) {
        error_log("❌ [TWILIO] Error general al guardar código: " . $e->getMessage());
        return false;
    }
}

    /**
 * Verifica si un código es válido
 */
public function verificarCodigo(string $telefono, string $codigo): bool
{
    try {
        $pdo = BaseDatos::pdo();
        
        error_log("🔍 [TWILIO] Verificando código");
        error_log("   - Teléfono: $telefono");
        error_log("   - Código: $codigo");
        
        $stmt = $pdo->prepare("
            SELECT id, codigo, expiracion, usado
            FROM codigos_recuperacion 
            WHERE telefono = :telefono 
            AND codigo = :codigo 
            AND usado = FALSE 
            AND expiracion > NOW()
            LIMIT 1
        ");
        
        $stmt->execute([
            ':telefono' => $telefono,
            ':codigo' => $codigo
        ]);
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado) {
            error_log("✅ [TWILIO] Código válido encontrado (ID: {$resultado['id']})");
            return true;
        } else {
            error_log("❌ [TWILIO] Código no válido");
            
            // Debug: Ver por qué falló
            $stmtDebug = $pdo->prepare("
                SELECT id, telefono, codigo, expiracion, usado,
                       CASE 
                           WHEN expiracion <= NOW() THEN 'Expirado'
                           WHEN usado = TRUE THEN 'Ya usado'
                           ELSE 'OK'
                       END as problema
                FROM codigos_recuperacion 
                WHERE telefono = :telefono 
                AND codigo = :codigo
                LIMIT 1
            ");
            $stmtDebug->execute([
                ':telefono' => $telefono,
                ':codigo' => $codigo
            ]);
            $debug = $stmtDebug->fetch(PDO::FETCH_ASSOC);
            
            if ($debug) {
                error_log("❌ [TWILIO] Código existe pero: {$debug['problema']}");
            } else {
                error_log("❌ [TWILIO] Código no encontrado en BD");
            }
            
            return false;
        }
        
    } catch (PDOException $e) {
        error_log("❌ [TWILIO] Error PDO al verificar código: " . $e->getMessage());
        return false;
    }
}

    /**
     * Marca un código como usado
     */
    public function marcarCodigoUsado(string $telefono, string $codigo): bool
    {
        try {
            $pdo = BaseDatos::pdo();
            
            $stmt = $pdo->prepare("
                UPDATE codigos_recuperacion 
                SET usado = 1 
                WHERE telefono = :telefono AND codigo = :codigo
            ");
            
            return $stmt->execute([
                ':telefono' => $telefono,
                ':codigo' => $codigo
            ]);
            
        } catch (PDOException $e) {
            error_log("❌ Error al marcar código como usado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Proceso completo: generar y enviar código
     */
    public function enviarCodigoRecuperacion(string $telefono): array
    {
        // Verificar que el teléfono existe en la BD
        if (!$this->verificarTelefonoExiste($telefono)) {
            return [
                'success' => false,
                'message' => 'No existe ninguna cuenta con este número de teléfono'
            ];
        }
        
        // Generar código
        $codigo = $this->generarCodigo();
        
        // Guardar en BD
        if (!$this->guardarCodigo($telefono, $codigo)) {
            return [
                'success' => false,
                'message' => 'Error al generar el código de verificación'
            ];
        }
        
        // Enviar SMS
        if (!$this->enviarCodigoVerificacion($telefono, $codigo)) {
            return [
                'success' => false,
                'message' => 'Error al enviar el SMS. Verifica tu número de teléfono'
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Código enviado exitosamente'
        ];
    }

    /**
     * Verifica si un teléfono está registrado
     */
    private function verificarTelefonoExiste(string $telefono): bool
    {
        try {
            $pdo = BaseDatos::pdo();
            
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE telefono = :telefono LIMIT 1");
            $stmt->execute([':telefono' => $telefono]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
            
        } catch (PDOException $e) {
            error_log("❌ Error al verificar teléfono: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Limpia códigos expirados (ejecutar periódicamente)
     */
    public static function limpiarCodigosExpirados(): void
    {
        try {
            $pdo = BaseDatos::pdo();
            $stmt = $pdo->prepare("DELETE FROM codigos_recuperacion WHERE expiracion < NOW()");
            $stmt->execute();
            
            error_log("🧹 Códigos expirados eliminados: " . $stmt->rowCount());
            
        } catch (PDOException $e) {
            error_log("❌ Error al limpiar códigos: " . $e->getMessage());
        }
    }
}