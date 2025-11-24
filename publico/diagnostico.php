<?php
/**
 * 🔍 SCRIPT DE DIAGNÓSTICO PARA REPORTES
 * Guardar como: diagnostico.php en la raíz del proyecto
 * Acceder a: http://tudominio.com/diagnostico.php
 */

require_once __DIR__ . "/../configuracion/app.php";
require_once __DIR__ . "/../nucleo/BaseDatos.php";

// Protección básica
if (!isset($_GET['debug_key']) || $_GET['debug_key'] !== 'debug123') {
    die('❌ Acceso denegado. Añade ?debug_key=debug123');
}

echo "<h1>🔍 Diagnóstico de Reportes</h1>";
echo "<style>body { font-family: Arial; margin: 20px; } .box { border: 2px solid #333; padding: 15px; margin: 20px 0; background: #f5f5f5; } .success { border-color: green; } .error { border-color: red; } .info { border-color: blue; } code { background: #f0f0f0; padding: 5px; display: block; margin: 10px 0; }</style>";

try {
    $pdo = BaseDatos::pdo();
    
    echo '<div class="box success">';
    echo '<h2>✅ Conexión a Base de Datos: OK</h2>';
    echo '</div>';
    
    // ============================================
    // 1️⃣ VERIFICAR TABLAS EXISTEN
    // ============================================
    echo '<div class="box info"><h2>1️⃣ Verificando tablas...</h2>';
    
    $tablas = ['citas', 'facturas', 'pacientes', 'usuarios'];
    foreach ($tablas as $tabla) {
        try {
            $result = $pdo->query("SELECT COUNT(*) as total FROM $tabla");
            $total = $result->fetchColumn();
            echo "✅ Tabla <code>$tabla</code>: $total registros<br>";
        } catch (Exception $e) {
            echo "❌ Tabla <code>$tabla</code>: NO EXISTE - " . $e->getMessage() . "<br>";
        }
    }
    echo '</div>';
    
    // ============================================
    // 2️⃣ CONTAR REGISTROS
    // ============================================
    echo '<div class="box info"><h2>2️⃣ Conteo de Registros</h2>';
    
    try {
        $totalCitas = $pdo->query("SELECT COUNT(*) FROM citas")->fetchColumn();
        echo "📅 Total Citas: <strong>$totalCitas</strong><br>";
    } catch (Exception $e) {
        echo "❌ Error en citas: " . $e->getMessage() . "<br>";
    }
    
    try {
        $totalFacturas = $pdo->query("SELECT COUNT(*) FROM facturas")->fetchColumn();
        echo "🧾 Total Facturas: <strong>$totalFacturas</strong><br>";
    } catch (Exception $e) {
        echo "❌ Error en facturas: " . $e->getMessage() . "<br>";
    }
    
    try {
        $totalPacientes = $pdo->query("SELECT COUNT(*) FROM pacientes")->fetchColumn();
        echo "👥 Total Pacientes: <strong>$totalPacientes</strong><br>";
    } catch (Exception $e) {
        echo "❌ Error en pacientes: " . $e->getMessage() . "<br>";
    }
    
    echo '</div>';
    
    // ============================================
    // 3️⃣ SUMA DE FACTURAS
    // ============================================
    echo '<div class="box info"><h2>3️⃣ Suma de Facturas Pagadas</h2>';
    
    try {
        $result = $pdo->query("SELECT COALESCE(SUM(monto), 0) as total FROM facturas WHERE LOWER(estado) IN ('pagada', 'completada', 'pagado', 'completado')")->fetch();
        echo "💰 Total Recaudado: <strong>S/. " . number_format($result['total'], 2) . "</strong><br>";
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
    
    echo '</div>';
    
    // ============================================
    // 4️⃣ DISTRIBUCIÓN DE ESTADOS
    // ============================================
    echo '<div class="box info"><h2>4️⃣ Distribución de Estados en Facturas</h2>';
    
    try {
        $result = $pdo->query("
            SELECT estado, COUNT(*) as cantidad 
            FROM facturas 
            GROUP BY estado
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($result)) {
            echo "⚠️ No hay facturas registradas<br>";
        } else {
            foreach ($result as $row) {
                echo "📊 Estado '<strong>" . htmlspecialchars($row['estado']) . "</strong>': " . $row['cantidad'] . " facturas<br>";
            }
        }
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
    
    echo '</div>';
    
    // ============================================
    // 5️⃣ CITAS POR ESTADO
    // ============================================
    echo '<div class="box info"><h2>5️⃣ Distribución de Estados en Citas</h2>';
    
    try {
        $result = $pdo->query("
            SELECT estado, COUNT(*) as cantidad 
            FROM citas 
            GROUP BY estado
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($result)) {
            echo "⚠️ No hay citas registradas<br>";
        } else {
            foreach ($result as $row) {
                echo "📊 Estado '<strong>" . htmlspecialchars($row['estado']) . "</strong>': " . $row['cantidad'] . " citas<br>";
            }
        }
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
    
    echo '</div>';
    
    // ============================================
    // 6️⃣ ÚLTIMAS 5 CITAS
    // ============================================
    echo '<div class="box info"><h2>6️⃣ Últimas 5 Citas</h2>';
    
    try {
        $result = $pdo->query("
            SELECT c.id, c.fecha, c.estado, u.nombre 
            FROM citas c
            LEFT JOIN pacientes p ON c.paciente_id = p.id
            LEFT JOIN usuarios u ON p.usuario_id = u.id
            ORDER BY c.fecha DESC LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($result)) {
            echo "⚠️ No hay citas<br>";
        } else {
            echo "<table style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background: #ddd;'><th style='border: 1px solid #999; padding: 8px;'>ID</th><th style='border: 1px solid #999; padding: 8px;'>Paciente</th><th style='border: 1px solid #999; padding: 8px;'>Fecha</th><th style='border: 1px solid #999; padding: 8px;'>Estado</th></tr>";
            foreach ($result as $row) {
                echo "<tr>";
                echo "<td style='border: 1px solid #999; padding: 8px;'>#" . $row['id'] . "</td>";
                echo "<td style='border: 1px solid #999; padding: 8px;'>" . ($row['nombre'] ?? 'Sin paciente') . "</td>";
                echo "<td style='border: 1px solid #999; padding: 8px;'>" . $row['fecha'] . "</td>";
                echo "<td style='border: 1px solid #999; padding: 8px;'>" . $row['estado'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
    
    echo '</div>';
    
    // ============================================
    // 7️⃣ ÚLTIMAS 5 FACTURAS
    // ============================================
    echo '<div class="box info"><h2>7️⃣ Últimas 5 Facturas</h2>';
    
    try {
        $result = $pdo->query("
            SELECT f.id, f.monto, f.estado, f.emitida_en, u.nombre 
            FROM facturas f
            LEFT JOIN pacientes p ON f.paciente_id = p.id
            LEFT JOIN usuarios u ON p.usuario_id = u.id
            ORDER BY f.emitida_en DESC LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($result)) {
            echo "⚠️ No hay facturas<br>";
        } else {
            echo "<table style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background: #ddd;'><th style='border: 1px solid #999; padding: 8px;'>ID</th><th style='border: 1px solid #999; padding: 8px;'>Paciente</th><th style='border: 1px solid #999; padding: 8px;'>Monto</th><th style='border: 1px solid #999; padding: 8px;'>Estado</th><th style='border: 1px solid #999; padding: 8px;'>Fecha</th></tr>";
            foreach ($result as $row) {
                echo "<tr>";
                echo "<td style='border: 1px solid #999; padding: 8px;'>#" . $row['id'] . "</td>";
                echo "<td style='border: 1px solid #999; padding: 8px;'>" . ($row['nombre'] ?? 'Sin paciente') . "</td>";
                echo "<td style='border: 1px solid #999; padding: 8px;'>S/. " . number_format($row['monto'], 2) . "</td>";
                echo "<td style='border: 1px solid #999; padding: 8px;'>" . $row['estado'] . "</td>";
                echo "<td style='border: 1px solid #999; padding: 8px;'>" . substr($row['emitida_en'], 0, 10) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
    
    echo '</div>';
    
    // ============================================
    // 8️⃣ QUERY DE TENDENCIA MENSUAL
    // ============================================
    echo '<div class="box info"><h2>8️⃣ Tendencia Mensual (Últimos 6 meses)</h2>';
    
    try {
        for ($i = 5; $i >= 0; $i--) {
            $fecha = date('Y-m', strtotime("-$i months"));
            $result = $pdo->prepare("
                SELECT COALESCE(SUM(monto), 0) as total
                FROM facturas
                WHERE CAST(emitida_en AS VARCHAR(7)) = :mes
                AND LOWER(estado) IN ('pagada', 'completada', 'pagado', 'completado')
            ");
            $result->execute([':mes' => $fecha]);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            echo "📈 Mes $fecha: <strong>S/. " . number_format($row['total'], 2) . "</strong><br>";
        }
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
    
    echo '</div>';
    
    // ============================================
    // RESULTADO FINAL
    // ============================================
    echo '<div class="box success"><h2>✅ Diagnóstico Completado</h2>';
    echo '<p>Si todos los datos muestran 0 o están vacíos, significa que:</p>';
    echo '<ul>';
    echo '<li>❌ Las tablas no tienen registros (crea datos de prueba)</li>';
    echo '<li>❌ Las consultas SQL tienen errores</li>';
    echo '<li>❌ La base de datos no está conectada correctamente</li>';
    echo '</ul>';
    echo '</div>';
    
} catch (Exception $e) {
    echo '<div class="box error">';
    echo '<h2>❌ Error de Conexión</h2>';
    echo '<p>' . $e->getMessage() . '</p>';
    echo '</div>';
}