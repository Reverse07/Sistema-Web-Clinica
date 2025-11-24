<?php
echo "<h2>🔍 Verificación de Contraseña</h2>";

$password = 'qplkyntmuwtrqwfx'; // ⚠️ Pega aquí tu contraseña

echo "<strong>Contraseña ingresada:</strong> <code>" . htmlspecialchars($password) . "</code><br>";
echo "<strong>Longitud:</strong> " . strlen($password) . " caracteres<br>";
echo "<strong>Debe ser:</strong> 16 caracteres<br><br>";

if (strlen($password) === 16) {
    echo "✅ <span style='color:green'>La longitud es correcta</span><br>";
} else {
    echo "❌ <span style='color:red'>ERROR: La contraseña debe tener exactamente 16 caracteres</span><br>";
}

// Mostrar cada carácter
echo "<br><strong>Caracteres individuales:</strong><br>";
for ($i = 0; $i < strlen($password); $i++) {
    echo "Posición $i: <code>" . $password[$i] . "</code><br>";
}
?>