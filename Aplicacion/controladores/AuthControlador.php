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

    // =====================
    // 📌 Logout
    // =====================
    public function logout() {
    session_start();              // 🔐 Inicia sesión si no está iniciada
    session_unset();              // 🧹 Limpia variables de sesión
    session_destroy();            // 🔥 Destruye la sesión

    header("Location: ?accion=loginVista"); // 🚀 Redirige al login
    exit;
}

}
