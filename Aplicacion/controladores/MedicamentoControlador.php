<?php
require_once __DIR__ . "/../../nucleo/Autenticacion.php";
require_once __DIR__ . "/../../nucleo/BaseDatos.php";
require_once __DIR__ . "/../modelos/Medicamento.php";

/**
 * MedicamentoControlador
 * 
 * Gestiona CRUD de medicamentos.
 * Solo accesible para administradores y doctores.
 */
class MedicamentoControlador
{
    /**
     * Redirección helper
     */
    private function redirigir($accion, $params = [])
    {
        $query = http_build_query(array_merge(['accion' => $accion], $params));
        header("Location: ?$query");
        exit;
    }

    /**
     * Establece mensaje flash
     */
    private function setMensaje($tipo, $texto)
    {
        $_SESSION['mensaje'] = ['tipo' => $tipo, 'texto' => $texto];
    }

    // ========================================
    // LISTAR MEDICAMENTOS
    // ========================================
    public function listar()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);

        $medicamentos = Medicamento::obtenerTodos();
        $vistaInterna = __DIR__ . "/../vistas/admin/medicamentos.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    // ========================================
    // CREAR NUEVO MEDICAMENTO
    // ========================================
    public function crear()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);

        $vistaInterna = __DIR__ . "/../vistas/admin/crearMedicamento.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    public function guardar()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);

        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $presentacion = trim($_POST['presentacion'] ?? '');
        $dosis = trim($_POST['dosis'] ?? '');
        $stock = (int)($_POST['stock'] ?? 0);

        if (empty($nombre)) {
            $this->setMensaje('error', 'El nombre del medicamento es obligatorio');
            $this->redirigir('crear');
        }

        try {
            $medicamento = new Medicamento([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'presentacion' => $presentacion,
                'dosis' => $dosis,
                'stock' => $stock
            ]);

            if ($medicamento->crear()) {
                $this->setMensaje('exito', 'Medicamento registrado exitosamente');
            } else {
                $this->setMensaje('error', 'Error al registrar medicamento');
            }
        } catch (Exception $e) {
            error_log("Error al guardar medicamento: " . $e->getMessage());
            $this->setMensaje('error', 'Ocurrió un error al guardar el medicamento');
        }

        $this->redirigir('listar');
    }

    // ========================================
    // EDITAR MEDICAMENTO
    // ========================================
    public function editar()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirigir('listar');
        }

        $medicamento = Medicamento::buscarPorId((int)$id);
        if (!$medicamento) {
            $this->setMensaje('error', 'Medicamento no encontrado');
            $this->redirigir('listar');
        }

        $vistaInterna = __DIR__ . "/../vistas/admin/editarMedicamento.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    public function actualizar()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirigir('listar');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $presentacion = trim($_POST['presentacion'] ?? '');
        $dosis = trim($_POST['dosis'] ?? '');
        $stock = (int)($_POST['stock'] ?? 0);

        if (empty($nombre)) {
            $this->setMensaje('error', 'El nombre del medicamento es obligatorio');
            $this->redirigir('editar', ['id' => $id]);
        }

        try {
            if (Medicamento::actualizar(
                (int)$id,
                $nombre,
                $descripcion,
                $presentacion,
                $dosis,
                $stock
            )) {
                $this->setMensaje('exito', 'Medicamento actualizado correctamente');
            } else {
                $this->setMensaje('error', 'No se pudo actualizar el medicamento');
            }
        } catch (Exception $e) {
            error_log("Error al actualizar medicamento: " . $e->getMessage());
            $this->setMensaje('error', 'Error al actualizar medicamento');
        }

        $this->redirigir('listar');
    }

    // ========================================
    // ELIMINAR MEDICAMENTO
    // ========================================
    public function eliminar()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('listar');
        }

        try {
            if (Medicamento::eliminar((int)$id)) {
                $this->setMensaje('exito', 'Medicamento eliminado correctamente');
            } else {
                $this->setMensaje('error', 'No se pudo eliminar el medicamento');
            }
        } catch (Exception $e) {
            error_log("Error al eliminar medicamento: " . $e->getMessage());
            $this->setMensaje('error', 'Error al eliminar medicamento');
        }

        $this->redirigir('listar');
    }
}