<?php
require_once __DIR__ . "/../modelos/Usuario.php";
require_once __DIR__ . "/../../nucleo/Autenticacion.php";

class AdminControlador
{

    // 📊 Dashboard con KPIs simulados
    public function dashboard()
    {
        Autenticacion::requiereRoles(['admin']);

        // 🔢 Simulación de KPIs
        $totalUsuarios   = Usuario::contarTodos(); // Método que puedes crear
        $totalDoctores   = Usuario::contarPorRol('doctor');
        $totalPacientes  = Usuario::contarPorRol('paciente');

        // 📦 Vista interna
        $vistaInterna = __DIR__ . "/../vistas/admin/inicio.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    // 👥 Gestión de usuarios
    public function gestionarUsuarios()
    {
        Autenticacion::requiereRoles(['admin']);

        $usuarios = Usuario::obtenerTodos(); // Método que puedes crear
        $vistaInterna = __DIR__ . "/../vistas/admin/usuarios.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    // 🩺 Gestión de doctores
    public function gestionarDoctores()
    {
        Autenticacion::requiereRoles(['admin']);

        $doctores = Usuario::obtenerPorRol('doctor');
        $vistaInterna = __DIR__ . "/../vistas/admin/doctores.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    // 🧑‍🤝‍🧑 Gestión de pacientes
    public function gestionarPacientes()
    {
        Autenticacion::requiereRoles(['admin']);

        $pacientes = Usuario::obtenerPorRol('paciente');
        $vistaInterna = __DIR__ . "/../vistas/admin/pacientes.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    // 💳 Facturas
    public function gestionarFacturas()
    {
        Autenticacion::requiereRoles(['admin']);
        $vistaInterna = __DIR__ . "/../vistas/admin/facturas.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    // 📈 Reportes
    public function verReportes()
    {
        Autenticacion::requiereRoles(['admin']);
        $vistaInterna = __DIR__ . "/../vistas/admin/reportes.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    public function actualizarDoctor()
    {
        $id = $_GET['id'];
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];
        $especialidad = $_POST['especialidad'];

        Usuario::actualizarDoctor($id, $nombre, $email, $telefono, $especialidad);
        header("Location: ?accion=gestionarDoctores");
    }

    public function editarDoctor()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo "ID de doctor no proporcionado.";
            return;
        }

        $doctor = Usuario::buscarPorId($id);

        if (!$doctor || $doctor->getRolId() != 2) {
            echo "Doctor no encontrado o rol inválido.";
            return;
        }

        $vistaInterna = __DIR__ . "/../vistas/admin/editarDoctor.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }


    public function crearDoctor()
    {
        $vistaInterna = __DIR__ . "/../vistas/admin/crearDoctor.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

   public function guardarDoctor() {
    $nombre     = $_POST['nombre'] ?? '';
    $email      = $_POST['email'] ?? '';
    $telefono   = $_POST['telefono'] ?? '';
    $password   = $_POST['password'] ?? '';

    $usuario = new Usuario([
        'nombre'   => $nombre,
        'email'    => $email,
        'telefono' => $telefono,
        'password' => $password,
        'rol_id'   => 2,
        'especialidad' => 'No definida'
    ]);

    $usuario->crearUsuario(); // Usa el método que ya encripta la contraseña
    header("Location: ?accion=gestionarDoctores");
}

    // ⚙️ Configuración
    public function configuracion()
    {
        Autenticacion::requiereRoles(['admin']);
        $vistaInterna = __DIR__ . "/../vistas/admin/configuracion.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }
}
