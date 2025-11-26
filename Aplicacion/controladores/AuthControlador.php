<?php

// 🔹 Importa la clase para conexión a la base de datos
require_once __DIR__ . "/../../nucleo/BaseDatos.php";

// 🔹 Importa la clase para manejar autenticación de usuarios
require_once __DIR__ . "/../../nucleo/Autenticacion.php";

// 🔹 Importa el modelo de Usuario
require_once __DIR__ . "/../modelos/Usuario.php";

// 🔹 Importa el modelo de Rol (aunque no se usa directamente aquí)
require_once __DIR__ . "/../modelos/Rol.php";

class AuthControlador
{

    // =====================
    // 📌 Vista Login
    // =====================
    public function loginVista()
    {
        // 🔹 Muestra la vista del formulario de login
        require __DIR__ . "/../vistas/auth/login.php";
    }

    /**
 * Muestra la página de Términos y Condiciones
 */
public function terminos()
{
    require __DIR__ . "/../vistas/terminos.php";
}

/**
 * Muestra la página de Política de Privacidad
 */
public function privacidad()
{
    require __DIR__ . "/../vistas/privacidad.php";
}

    // =====================
    // 📌 Procesar Login
    // =====================
    public function login()
    {
        // 🔹 Verifica si el formulario fue enviado por POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 🔹 Obtiene el email y la contraseña del formulario
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // 🔹 Busca el usuario en la base de datos por su email
            $usuario = Usuario::buscarPorEmail($email);

            // 🔹 Verifica si el usuario existe y la contraseña es correcta
            if ($usuario && $usuario->verificarContraseña($password)) {

                // 🔹 Mapea el rol_id a un nombre legible
                $rolesMap = [
                    1 => "admin",
                    2 => "doctor",
                    3 => "paciente"
                ];
                $rolNombre = $rolesMap[$usuario->getRolId()] ?? "invitado"; // 🔹 Si no coincide, se asigna "invitado"

                // 🔹 Inicia sesión segura con el ID del usuario y su rol
                Autenticacion::login($usuario->getId(), $rolNombre);

                // 🔹 Redirige al usuario según su rol
                switch ($usuario->getRolId()) {
                    case 1:
                        header("Location: " . BASE_URL . "/index.php?accion=dashboardAdmin");
                        break;
                    case 2:
                        header("Location: " . BASE_URL . "/index.php?accion=dashboardDoctor"); // 🔹 Doctor
                        break;
                    case 3:
                         header("Location: " . BASE_URL . "/index.php?accion=dashboardPaciente"); // 🔹 Paciente
                        break;
                    default:
                        header("Location: " . BASE_URL . "/publico/index.php"); // 🔹 Rol desconocido
                        break;
                }
                exit; // 🔹 Finaliza la ejecución después de redirigir

            } else {
                // 🔹 Si las credenciales son incorrectas, muestra error
                $error = "Credenciales incorrectas";
                require __DIR__ . "/../vistas/auth/login.php"; // 🔹 Vuelve a mostrar el login
            }
        } else {
            // 🔹 Si no es POST, simplemente muestra la vista de login
            $this->loginVista();
        }
    }

    // =====================
    // 📌 Vista Registro
    // =====================
    public function registroVista()
    {
        // 🔹 Muestra la vista del formulario de registro
        require __DIR__ . "/../vistas/auth/registro.php";
    }

// =====================
// 📌 Procesar Registro - CORREGIDO Y MEJORADO
// =====================
public function registro()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $nombre   = trim($_POST['nombre'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        $telefono = trim($_POST['telefono'] ?? '');
        $rol_id   = 3;

        // Validaciones (mantener igual)
        if (empty($nombre) || empty($email) || empty($password)) {
            $error = "Todos los campos son obligatorios";
            require __DIR__ . "/../vistas/auth/registro.php";
            return;
        }

        if ($password !== $password_confirm) {
            $error = "Las contraseñas no coinciden";
            require __DIR__ . "/../vistas/auth/registro.php";
            return;
        }

        if (strlen($password) < 6) {
            $error = "La contraseña debe tener al menos 6 caracteres";
            require __DIR__ . "/../vistas/auth/registro.php";
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Email no válido";
            require __DIR__ . "/../vistas/auth/registro.php";
            return;
        }

        if (Usuario::buscarPorEmail($email)) {
            $error = "Este email ya está registrado";
            require __DIR__ . "/../vistas/auth/registro.php";
            return;
        }

        try {
            $pdo = BaseDatos::pdo();
            
            // 🔍 DEBUG: Verificar conexión
            error_log("DEBUG: Conexión BD establecida");
            
            $pdo->beginTransaction();

            // 🔹 PASO 1: Crear usuario - VERSIÓN DIRECTA
            $sqlUsuario = "
                INSERT INTO usuarios (nombre, email, password, telefono, rol_id) 
                VALUES (:nombre, :email, :password, :telefono, :rol_id)
            ";
            
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            
            $stmtUsuario = $pdo->prepare($sqlUsuario);
            $resultadoUsuario = $stmtUsuario->execute([
                ':nombre' => $nombre,
                ':email' => $email,
                ':password' => $passwordHash,
                ':telefono' => $telefono,
                ':rol_id' => $rol_id
            ]);

            if (!$resultadoUsuario) {
                $errorInfo = $stmtUsuario->errorInfo();
                throw new Exception("Error al crear usuario: " . $errorInfo[2]);
            }

            $usuarioId = $pdo->lastInsertId();
            error_log("DEBUG: Usuario creado con ID: " . $usuarioId);

            // 🔹 PASO 2: Crear paciente - VERSIÓN MÍNIMA
            $sqlPaciente = "INSERT INTO pacientes (usuario_id) VALUES (:usuario_id)";
            $stmtPaciente = $pdo->prepare($sqlPaciente);
            $resultadoPaciente = $stmtPaciente->execute([
                ':usuario_id' => $usuarioId
            ]);

            if (!$resultadoPaciente) {
                $errorInfo = $stmtPaciente->errorInfo();
                throw new Exception("Error al crear paciente: " . $errorInfo[2]);
            }

            $pdo->commit();
            error_log("✅ TRANSACCIÓN COMPLETADA - Usuario ID: $usuarioId");

            header("Location: " . BASE_URL . "/index.php?accion=loginVista&msg=registrado");
            exit;

        } catch (PDOException $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("❌ ERROR PDO: " . $e->getMessage());
            error_log("❌ CÓDIGO ERROR: " . $e->getCode());
            $error = "Error de base de datos: " . $e->getMessage();
            require __DIR__ . "/../vistas/auth/registro.php";
        } catch (Exception $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("❌ ERROR GENERAL: " . $e->getMessage());
            $error = $e->getMessage();
            require __DIR__ . "/../vistas/auth/registro.php";
        }
    } else {
        $this->registroVista();
    }
}

   public function logout() 
{
    error_log("🔓 [AUTH] Iniciando logout...");
    
    Autenticacion::cerrarSesion();
    
    error_log("✅ [AUTH] Redirigiendo a login...");
    
    header("Location: " . BASE_URL . "/index.php?accion=loginVista");
    exit();
}

// =====================
// 📌 Recuperación de Contraseña por SMS
// =====================

/**
 * Envía código de verificación por SMS
 */
public function enviarCodigoRecuperacion()
{
    header('Content-Type: application/json');
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $telefono = $input['telefono'] ?? '';
        
        if (empty($telefono)) {
            echo json_encode([
                'success' => false,
                'message' => 'El teléfono es obligatorio'
            ]);
            return;
        }
        
        require_once __DIR__ . "/../../nucleo/TwilioService.php";
        $twilioService = new TwilioService();
        
        $resultado = $twilioService->enviarCodigoRecuperacion($telefono);
        
        echo json_encode($resultado);
        
    } catch (Exception $e) {
        error_log("Error en enviarCodigoRecuperacion: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error al procesar la solicitud'
        ]);
    }
    
    exit;
}

/**
 * Verifica el código de recuperación
 */
public function verificarCodigoRecuperacion()
{
    header('Content-Type: application/json');
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $telefono = $input['telefono'] ?? '';
        $codigo = $input['codigo'] ?? '';
        
        error_log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        error_log("🔍 [VERIFICAR CODIGO] Iniciando verificación");
        error_log("📞 Teléfono recibido: $telefono");
        error_log("🔢 Código recibido: $codigo");
        
        if (empty($telefono) || empty($codigo)) {
            error_log("❌ [VERIFICAR CODIGO] Datos incompletos");
            echo json_encode([
                'success' => false,
                'message' => 'Teléfono y código son obligatorios'
            ]);
            return;
        }
        
        require_once __DIR__ . "/../../nucleo/TwilioService.php";
        $twilioService = new TwilioService();
        
        // ✅ DEBUG: Ver qué hay en la BD antes de verificar
        $pdo = BaseDatos::pdo();
        $stmtDebug = $pdo->prepare("
            SELECT id, telefono, codigo, expiracion, usado, 
                   CASE WHEN expiracion > NOW() THEN 'Válido' ELSE 'Expirado' END as estado
            FROM codigos_recuperacion 
            WHERE telefono = :telefono
            ORDER BY id DESC
            LIMIT 3
        ");
        $stmtDebug->execute([':telefono' => $telefono]);
        $registros = $stmtDebug->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("🔍 [VERIFICAR CODIGO] Códigos en BD para este teléfono:");
        foreach ($registros as $reg) {
            error_log("   - ID: {$reg['id']}, Código: {$reg['codigo']}, Estado: {$reg['estado']}, Usado: {$reg['usado']}, Expira: {$reg['expiracion']}");
        }
        
        if ($twilioService->verificarCodigo($telefono, $codigo)) {
            error_log("✅ [VERIFICAR CODIGO] Código verificado correctamente");
            echo json_encode([
                'success' => true,
                'message' => 'Código verificado correctamente'
            ]);
        } else {
            error_log("❌ [VERIFICAR CODIGO] Código inválido o expirado");
            error_log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            echo json_encode([
                'success' => false,
                'message' => 'Código inválido o expirado'
            ]);
        }
        
    } catch (Exception $e) {
        error_log("❌ [VERIFICAR CODIGO] Error: " . $e->getMessage());
        error_log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        echo json_encode([
            'success' => false,
            'message' => 'Error al verificar el código'
        ]);
    }
    
    exit;
}

/**
 * Cambia la contraseña después de verificar el código
 */
public function cambiarPasswordRecuperacion()
{
    header('Content-Type: application/json');
    
    try {
        error_log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        error_log("🔑 [CAMBIAR PASSWORD] Iniciando cambio de contraseña");
        
        $input = json_decode(file_get_contents('php://input'), true);
        $telefono = $input['telefono'] ?? '';
        $nuevaPassword = $input['nueva_password'] ?? '';
        
        error_log("🔑 [CAMBIAR PASSWORD] Teléfono recibido: $telefono");
        error_log("🔑 [CAMBIAR PASSWORD] Password recibida: " . (empty($nuevaPassword) ? 'VACÍA' : 'OK'));
        
        if (empty($telefono) || empty($nuevaPassword)) {
            error_log("❌ [CAMBIAR PASSWORD] Datos incompletos");
            echo json_encode([
                'success' => false,
                'message' => 'Teléfono y contraseña son obligatorios'
            ]);
            return;
        }
        
        if (strlen($nuevaPassword) < 6) {
            error_log("❌ [CAMBIAR PASSWORD] Contraseña muy corta");
            echo json_encode([
                'success' => false,
                'message' => 'La contraseña debe tener al menos 6 caracteres'
            ]);
            return;
        }
        
        require_once __DIR__ . "/../../nucleo/TwilioService.php";
        $twilioService = new TwilioService();
        
        // Buscar usuario por teléfono
        error_log("🔍 [CAMBIAR PASSWORD] Buscando usuario con teléfono: $telefono");
        $usuario = Usuario::buscarPorTelefono($telefono);
        
        if (!$usuario) {
            error_log("❌ [CAMBIAR PASSWORD] Usuario no encontrado");
            echo json_encode([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ]);
            return;
        }
        
        error_log("✅ [CAMBIAR PASSWORD] Usuario encontrado: {$usuario->getNombre()} (ID: {$usuario->getId()})");
        
        // Actualizar contraseña usando el método del modelo
        $passwordHash = password_hash($nuevaPassword, PASSWORD_BCRYPT);
        error_log("🔍 [CAMBIAR PASSWORD] Hash generado: " . substr($passwordHash, 0, 20) . "...");
        
        $pdo = BaseDatos::pdo();
        
        // Verificar que el usuario existe antes de actualizar
        $stmtVerif = $pdo->prepare("SELECT id FROM usuarios WHERE telefono = :telefono");
        $stmtVerif->execute([':telefono' => $telefono]);
        $existe = $stmtVerif->fetch(PDO::FETCH_ASSOC);
        
        if (!$existe) {
            error_log("❌ [CAMBIAR PASSWORD] Usuario no encontrado en verificación");
            echo json_encode([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ]);
            return;
        }
        
        error_log("✅ [CAMBIAR PASSWORD] Usuario verificado, procediendo a actualizar");
        
        $stmt = $pdo->prepare("UPDATE usuarios SET password = :password WHERE telefono = :telefono");
        $resultado = $stmt->execute([
            ':password' => $passwordHash,
            ':telefono' => $telefono
        ]);
        
        $filasAfectadas = $stmt->rowCount();
        error_log("🔍 [CAMBIAR PASSWORD] Filas afectadas: $filasAfectadas");
        
        if ($resultado && $filasAfectadas > 0) {
            error_log("✅ [CAMBIAR PASSWORD] Contraseña actualizada en BD");
            
            // Marcar código como usado
            $stmtUpdate = $pdo->prepare("UPDATE codigos_recuperacion SET usado = 1 WHERE telefono = :telefono AND usado = 0");
            $stmtUpdate->execute([':telefono' => $telefono]);
            error_log("✅ [CAMBIAR PASSWORD] Códigos marcados como usados: " . $stmtUpdate->rowCount());
            
            error_log("✅✅✅ [CAMBIAR PASSWORD] PROCESO COMPLETADO EXITOSAMENTE ✅✅✅");
            error_log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            
            echo json_encode([
                'success' => true,
                'message' => 'Contraseña actualizada exitosamente'
            ]);
        } else {
            error_log("❌ [CAMBIAR PASSWORD] No se actualizó ninguna fila");
            error_log("❌ [CAMBIAR PASSWORD] Resultado: " . ($resultado ? 'TRUE' : 'FALSE'));
            error_log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo actualizar la contraseña'
            ]);
        }
        
    } catch (PDOException $e) {
        error_log("❌ [CAMBIAR PASSWORD] Error PDO: " . $e->getMessage());
        error_log("❌ [CAMBIAR PASSWORD] SQL State: " . $e->getCode());
        error_log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        echo json_encode([
            'success' => false,
            'message' => 'Error de base de datos al cambiar la contraseña'
        ]);
    } catch (Exception $e) {
        error_log("❌ [CAMBIAR PASSWORD] Error general: " . $e->getMessage());
        error_log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        echo json_encode([
            'success' => false,
            'message' => 'Error al cambiar la contraseña'
        ]);
    }
    
    exit;
}

}
