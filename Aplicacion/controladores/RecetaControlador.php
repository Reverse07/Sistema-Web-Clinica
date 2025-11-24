<?php
require_once __DIR__ . "/../../nucleo/Autenticacion.php";
require_once __DIR__ . "/../../nucleo/BaseDatos.php";
require_once __DIR__ . "/../modelos/Receta.php";
require_once __DIR__ . "/../modelos/Medicamento.php";
require_once __DIR__ . "/../modelos/DetalleReceta.php";
require_once __DIR__ . "/../modelos/Paciente.php";

/**
 * RecetaControlador
 *
 * Responsabilidad:
 * - CRUD de recetas (admin y doctor)
 * - Ver recetas de un paciente (doctor/paciente)
 */
class RecetaControlador
{
    private function redirigir($accion, $params = [])
    {
        $query = http_build_query(array_merge(['accion' => $accion], $params));
        header("Location: ?$query");
        exit;
    }

    private function setMensaje($tipo, $texto)
    {
        $_SESSION['mensaje'] = ['tipo' => $tipo, 'texto' => $texto];
    }

    // ========================================
    // LISTADO Y VISUALIZACIÓN
    // ========================================

    /**
     * Muestra todas las recetas (admin) o del doctor actual
     */
    public function listar()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);

        $rol = $_SESSION['rol'] ?? '';
        $usuarioId = $_SESSION['usuario_id'] ?? null;

        if ($rol === 'doctor') {
            $recetas = Receta::obtenerPorDoctor($usuarioId);
        } else {
            $recetas = Receta::todas();
        }

        $vistaInterna = __DIR__ . "/../vistas/recetas/listar.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * Muestra los detalles de una receta
     */
    public function ver()
    {
        Autenticacion::requiereLogin();

        $id = $_GET['id'] ?? null;
        if (!$id) $this->redirigir('listar');

        $receta = Receta::buscarPorId($id);
        $detalles = DetalleReceta::obtenerPorReceta($id);

        if (!$receta) {
            $this->setMensaje('error', 'Receta no encontrada');
            $this->redirigir('listar');
        }

        $vistaInterna = __DIR__ . "/../vistas/recetas/ver.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    // ========================================
    // CREAR Y GUARDAR
    // ========================================

    /**
     * Muestra formulario para crear una nueva receta
     */
    public function crear()
    {
        Autenticacion::requiereRoles(['doctor']);

        $pacientes = Paciente::todos();
        $medicamentos = Medicamento::todos();

        $vistaInterna = __DIR__ . "/../vistas/recetas/crear.php";
        require __DIR__ . "/../../includes/layout-doctor.php";
    }

    /**
     * Guarda la receta con sus detalles
     */
    public function guardar()
    {
        Autenticacion::requiereRoles(['doctor']);

        if (empty($_POST['paciente_id']) || empty($_POST['fecha'])) {
            $this->setMensaje('error', 'Campos obligatorios incompletos');
            $this->redirigir('crear');
        }

        try {
            BaseDatos::pdo()->beginTransaction();

            // Crear receta principal
            $recetaId = Receta::crear([
                'doctor_id' => $_SESSION['usuario_id'],
                'paciente_id' => $_POST['paciente_id'],
                'fecha' => $_POST['fecha'],
                'diagnostico' => $_POST['diagnostico'] ?? ''
            ]);

            // Insertar los medicamentos asociados
            if (!empty($_POST['medicamentos']) && is_array($_POST['medicamentos'])) {
                foreach ($_POST['medicamentos'] as $med) {
                    DetalleReceta::crear([
                        'receta_id' => $recetaId,
                        'medicamento_id' => $med['id'],
                        'dosis' => $med['dosis'],
                        'frecuencia' => $med['frecuencia']
                    ]);
                }
            }

            BaseDatos::pdo()->commit();
            $this->setMensaje('exito', 'Receta registrada exitosamente');
        } catch (Exception $e) {
            BaseDatos::pdo()->rollBack();
            error_log("Error al guardar receta: " . $e->getMessage());
            $this->setMensaje('error', 'Error al registrar la receta');
        }

        $this->redirigir('listar');
    }

    // ========================================
    // ELIMINAR
    // ========================================

    /**
     * Elimina una receta (solo admin o doctor creador)
     */
    public function eliminar()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);

        $id = $_GET['id'] ?? null;
        if (!$id) $this->redirigir('listar');

        $receta = Receta::buscarPorId($id);

        if (!$receta) {
            $this->setMensaje('error', 'Receta no encontrada');
            $this->redirigir('listar');
        }

        // Validar permiso del doctor
        if ($_SESSION['rol'] === 'doctor' && $receta->getDoctorId() != $_SESSION['usuario_id']) {
            $this->setMensaje('error', 'No tienes permiso para eliminar esta receta');
            $this->redirigir('listar');
        }

        try {
            Receta::eliminar($id);
            $this->setMensaje('exito', 'Receta eliminada exitosamente');
        } catch (Exception $e) {
            error_log("Error al eliminar receta: " . $e->getMessage());
            $this->setMensaje('error', 'Error al eliminar la receta');
        }

        $this->redirigir('listar');
    }
}
