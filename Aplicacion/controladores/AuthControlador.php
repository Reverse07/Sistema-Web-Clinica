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
    // 📌 Procesar Registro
    // =====================
    public function registro()
    {
        // 🔹 Verifica si el formulario fue enviado por POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 🔹 Obtiene los datos del formulario
            $nombre   = $_POST['nombre'] ?? '';
            $email    = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $rol_id   = $_POST['rol_id'] ?? 3; // 🔹 Si no se envía rol_id, se asigna 3 (paciente)

            $usuario = new Usuario([
                'nombre'   => $_POST['nombre'],
                'email'    => $_POST['email'],
                'password' => $_POST['password'], // ⚠️ en plano, SIN hash aquí
                'telefono' => $_POST['telefono'],
                'rol_id'   => $_POST['rol_id']
            ]);
            // 🔹 Intenta guardar el usuario en la base de datos
            if ($usuario->crearUsuario()) {
                // 🔹 Si se registra correctamente, redirige al inicio público con mensaje
                header("Location: " . BASE_URL . "/index.php?accion=loginVista&msg=registrado");
                exit;
            } else {
                // 🔹 Si falla el registro, muestra error y vuelve a la vista de registro
                $error = "Error al registrar usuario";
                require __DIR__ . "/../vistas/auth/registro.php";
            }
        } else {
            // 🔹 Si no es POST, muestra la vista de registro
            $this->registroVista();
        }
    }

    // =====================
    // 📌 Logout
    // =====================
    public function logout()
    {
        // 🔹 Cierra la sesión del usuario
        Autenticacion::logout();

        // 🔹 Redirige al inicio público
        header("Location: " . BASE_URL . "/publico/index.php");
        exit;
    }
}
