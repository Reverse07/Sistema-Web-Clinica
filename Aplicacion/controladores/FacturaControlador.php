<?php
require_once __DIR__ . "/../../nucleo/Autenticacion.php";
require_once __DIR__ . "/../../nucleo/BaseDatos.php";
require_once __DIR__ . "/../modelos/Factura.php";
require_once __DIR__ . "/../modelos/Paciente.php";
require_once __DIR__ . "/../modelos/Cita.php";

/**
 * FacturaControlador
 * 
 * Responsabilidad:
 * - CRUD de facturas desde admin
 * - Vista de facturas desde rol paciente
 */
class FacturaControlador
{
    // ========================================
    // 🔁 Helpers comunes
    // ========================================
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
    // 🧍 VISTAS PARA PACIENTE
    // ========================================
    public function misFacturas()
    {
        Autenticacion::requiereRoles(['paciente']);

        $usuario = Autenticacion::usuario();
        $facturas = Factura::obtenerPorPaciente($usuario->getId());

        $vistaInterna = __DIR__ . "/../vistas/paciente/facturas.php";
        require __DIR__ . "/../../includes/layout-paciente.php";
    }

    // ========================================
    // 🧑‍💼 GESTIÓN ADMIN DE FACTURAS
    // ========================================

    /**
     * 📊 Listar todas las facturas (Admin)
     */
    public function gestionarFacturas()
    {
        Autenticacion::requiereRoles(['admin']);
        $facturas = Factura::todos();

        $vistaInterna = __DIR__ . "/../vistas/admin/facturas.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * 📝 Formulario de creación de factura
     */
    public function crearFactura()
    {
        Autenticacion::requiereRoles(['admin']);
        $pacientes = Paciente::todos();
        $citas = Cita::obtenerTodos();

        $vistaInterna = __DIR__ . "/../vistas/admin/crearFactura.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * 💾 Guardar nueva factura
     */
    public function guardarFactura()
    {
        Autenticacion::requiereRoles(['admin']);

        if (empty($_POST['paciente_id']) || empty($_POST['cita_id']) || empty($_POST['monto'])) {
            $this->setMensaje('error', 'Todos los campos son obligatorios');
            $_SESSION['datos_formulario'] = $_POST;
            $this->redirigir('crearFactura');
        }

        try {
            $factura = new Factura([
                'paciente_id' => $_POST['paciente_id'],
                'cita_id'     => $_POST['cita_id'],
                'monto'       => $_POST['monto'],
                'estado'      => $_POST['estado'] ?? 'Pendiente',
                'emitida_en'  => date('Y-m-d H:i:s')
            ]);
            $factura->crear($_POST['paciente_id'], $_POST['cita_id'], $_POST['monto']);

            $this->setMensaje('exito', 'Factura creada exitosamente ✅');
        } catch (Exception $e) {
            error_log("Error al crear factura: " . $e->getMessage());
            $this->setMensaje('error', 'Error al crear factura. Intente nuevamente.');
        }

        $this->redirigir('gestionarFacturas');
    }

    /**
     * ✏️ Formulario para editar factura
     */
    public function editarFactura()
    {
        Autenticacion::requiereRoles(['admin']);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarFacturas');
        }

        $factura = Factura::buscarPorId($id);
        if (!$factura) {
            $this->setMensaje('error', 'Factura no encontrada');
            $this->redirigir('gestionarFacturas');
        }

        $pacientes = Paciente::todos();
        $citas = Cita::obtenerTodos();

        $vistaInterna = __DIR__ . "/../vistas/admin/editarFactura.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * 🔁 Actualizar factura
     */
    public function actualizarFactura()
    {
        Autenticacion::requiereRoles(['admin']);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarFacturas');
        }

        if (empty($_POST['paciente_id']) || empty($_POST['cita_id']) || empty($_POST['monto']) || empty($_POST['estado'])) {
            $this->setMensaje('error', 'Todos los campos son obligatorios');
            $this->redirigir('editarFactura', ['id' => $id]);
        }

        try {
            Factura::actualizar(
                (int)$id,
                $_POST['paciente_id'],
                $_POST['cita_id'],
                $_POST['monto'],
                $_POST['estado']
            );
            $this->setMensaje('exito', 'Factura actualizada correctamente ✅');
        } catch (Exception $e) {
            error_log("Error al actualizar factura: " . $e->getMessage());
            $this->setMensaje('error', 'Error al actualizar factura');
        }

        $this->redirigir('gestionarFacturas');
    }

    /**
     * 🗑️ Eliminar factura
     */
    public function eliminarFactura()
    {
        Autenticacion::requiereRoles(['admin']);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarFacturas');
        }

        try {
            Factura::eliminar((int)$id);
            $this->setMensaje('exito', 'Factura eliminada exitosamente 🗑️');
        } catch (Exception $e) {
            error_log("Error al eliminar factura: " . $e->getMessage());
            $this->setMensaje('error', 'Error al eliminar factura');
        }

        $this->redirigir('gestionarFacturas');
    }
}
