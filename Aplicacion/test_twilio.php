<?php
/**
 * Script de prueba para verificar la configuración de Twilio
 * Guarda este archivo como: test_twilio.php en la raíz de tu proyecto
 * Accede desde: http://tu-dominio/test_twilio.php
 */

require_once __DIR__ . "/configuracion/app.php";
require_once __DIR__ . "/nucleo/BaseDatos.php";

echo "<h1>🔍 Verificación de Twilio</h1>";
echo "<hr>";

// 1. Verificar que existe el archivo de configuración
echo "<h2>1. Archivo de configuración</h2>";
$configPath = __DIR__ . "/config/twilio.php";
if (file_exists($configPath)) {
    echo "✅ Archivo config/twilio.php existe<br>";
    $config = require $configPath;
    echo "<pre>";
    echo "Account SID: " . substr($config['account_sid'], 0, 10) . "...<br>";
    echo "Auth Token: " . substr($config['auth_token'], 0, 10) . "...<br>";
    echo "Phone Number: " . $config['phone_number'] . "<br>";
    echo "</pre>";
} else {
    echo "❌ NO EXISTE config/twilio.php<br>";
    echo "Ruta buscada: $configPath<br>";
}

echo "<hr>";

// 2. Verificar que existe vendor/autoload.php (Twilio SDK)
echo "<h2>2. Twilio SDK (Composer)</h2>";
$vendorPath = __DIR__ . "/vendor/autoload.php";
if (file_exists($vendorPath)) {
    echo "✅ vendor/autoload.php existe<br>";
    require_once $vendorPath;
    
    if (class_exists('Twilio\Rest\Client')) {
        echo "✅ Clase Twilio\\Rest\\Client disponible<br>";
    } else {
        echo "❌ Clase Twilio\\Rest\\Client NO encontrada<br>";
        echo "Ejecuta: composer require twilio/sdk<br>";
    }
} else {
    echo "❌ vendor/autoload.php NO existe<br>";
    echo "Ejecuta: composer install<br>";
}

echo "<hr>";

// 3. Verificar tabla codigos_recuperacion
echo "<h2>3. Tabla codigos_recuperacion</h2>";
try {
    $pdo = BaseDatos::pdo();
    $stmt = $pdo->query("SHOW TABLES LIKE 'codigos_recuperacion'");
    $existe = $stmt->fetch();
    
    if ($existe) {
        echo "✅ Tabla codigos_recuperacion existe<br>";
        
        // Ver estructura
        $stmt = $pdo->query("DESCRIBE codigos_recuperacion");
        echo "<pre>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo $row['Field'] . " - " . $row['Type'] . "<br>";
        }
        echo "</pre>";
    } else {
        echo "❌ Tabla codigos_recuperacion NO existe<br>";
        echo "Ejecuta el SQL de creación de tabla<br>";
    }
} catch (Exception $e) {
    echo "❌ Error al conectar a la BD: " . $e->getMessage();
}

echo "<hr>";

// 4. Verificar que existe el archivo TwilioService.php
echo "<h2>4. TwilioService.php</h2>";
$servicePath = __DIR__ . "/nucleo/TwilioService.php";
if (file_exists($servicePath)) {
    echo "✅ nucleo/TwilioService.php existe<br>";
} else {
    echo "❌ nucleo/TwilioService.php NO existe<br>";
}

echo "<hr>";

// 5. Verificar método en AuthControlador
echo "<h2>5. AuthControlador.php</h2>";
$authPath = __DIR__ . "/Aplicacion/controladores/AuthControlador.php";
if (file_exists($authPath)) {
    echo "✅ AuthControlador.php existe<br>";
    $contenido = file_get_contents($authPath);
    
    if (strpos($contenido, 'enviarCodigoRecuperacion') !== false) {
        echo "✅ Método enviarCodigoRecuperacion() encontrado<br>";
    } else {
        echo "❌ Método enviarCodigoRecuperacion() NO encontrado<br>";
    }
} else {
    echo "❌ AuthControlador.php NO existe<br>";
}

echo "<hr>";

// 6. Verificar método buscarPorTelefono en Usuario.php
echo "<h2>6. Usuario.php</h2>";
$usuarioPath = __DIR__ . "/Aplicacion/modelos/Usuario.php";
if (file_exists($usuarioPath)) {
    echo "✅ Usuario.php existe<br>";
    $contenido = file_get_contents($usuarioPath);
    
    if (strpos($contenido, 'buscarPorTelefono') !== false) {
        echo "✅ Método buscarPorTelefono() encontrado<br>";
    } else {
        echo "❌ Método buscarPorTelefono() NO encontrado<br>";
    }
} else {
    echo "❌ Usuario.php NO existe<br>";
}

echo "<hr>";
echo "<h2>✅ Verificación completa</h2>";
echo "<p>Si todo está en verde, el sistema debería funcionar.</p>";
echo "<p>Si hay errores en rojo, corrígelos y vuelve a cargar esta página.</p>";