<?php

require_once __DIR__ . "/../../nucleo/Autenticacion.php";
require_once __DIR__ . "/../../nucleo/BaseDatos.php";
require_once __DIR__ . "/../modelos/Factura.php";
require_once __DIR__ . "/../modelos/Paciente.php";
require_once __DIR__ . "/../modelos/Cita.php";

/**
 * FacturaControlador
 *
 * CRUD para admin + vista de facturas del paciente
 */
class FacturaControlador
{
    // ============================================================
    // 🔁 Helpers
    // ============================================================
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

    // ============================================================
    // 👤 Vistas paciente
    // ============================================================
    public function misFacturas()
    {
        Autenticacion::requiereRoles(['paciente']);

        $usuario = Autenticacion::usuario();
        $facturas = Factura::obtenerPorPaciente($usuario->getId());

        $vistaInterna = __DIR__ . "/../vistas/paciente/facturas.php";
        require __DIR__ . "/../../includes/layout-paciente.php";
    }

    // ============================================================
    // 👨‍💼 ADMIN – Gestión de facturas
    // ============================================================

    /**
     * 📄 Listado de facturas con datos completos
     */
    public function gestionarFacturas()
    {
        Autenticacion::requiereRoles(['admin']);

        $facturas = $this->obtenerTodasLasFacturasConDatos();

        $vistaInterna = __DIR__ . "/../vistas/admin/facturas.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * Devuelve todas las facturas con datos del paciente
     */
    private function obtenerTodasLasFacturasConDatos(): array
    {
        $pdo = BaseDatos::pdo();

        $sql = "
            SELECT 
                f.id,
                f.paciente_id,
                f.cita_id,
                f.monto,
                f.estado,
                f.emitida_en,
                u.nombre AS paciente_nombre,
                u.email AS paciente_email
            FROM facturas f
            LEFT JOIN pacientes p ON f.paciente_id = p.id
            LEFT JOIN usuarios u ON p.usuario_id = u.id
            ORDER BY f.emitida_en DESC
        ";

        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // 📝 Crear factura
    // ============================================================

    public function crear()
    {
        Autenticacion::requiereRoles(['admin']);

        $pacientes = $this->obtenerPacientesConDatos();
        $citas = $this->obtenerCitasConDatos();

        $vistaInterna = __DIR__ . "/../vistas/admin/crearFactura.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * 💾 Guardar nueva factura
     */
    public function guardar()
    {
        Autenticacion::requiereRoles(['admin']);

        if (empty($_POST['paciente_id']) || empty($_POST['cita_id']) || empty($_POST['monto'])) {
            $this->setMensaje('error', 'Todos los campos obligatorios deben ser completados');
            $this->redirigir('crearFactura');
        }

        // Validar monto
        $monto = floatval($_POST['monto']);
        if ($monto <= 0) {
            $this->setMensaje('error', 'El monto debe ser mayor a 0');
            $this->redirigir('crearFactura');
        }

        try {
            $pdo = BaseDatos::pdo();

            $sql = "INSERT INTO facturas (paciente_id, cita_id, monto, estado, emitida_en)
                    VALUES (:paciente_id, :cita_id, :monto, :estado, CURRENT_TIMESTAMP)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':paciente_id' => $_POST['paciente_id'],
                ':cita_id'     => $_POST['cita_id'],
                ':monto'       => $monto,
                ':estado'      => $_POST['estado'] ?? 'Pendiente'
            ]);

            $this->setMensaje('exito', '✅ Factura creada exitosamente');
        } catch (Exception $e) {
            error_log("Error al crear factura: " . $e->getMessage());
            $this->setMensaje('error', '❌ Error al crear factura. Intente nuevamente.');
        }

        $this->redirigir('gestionarFacturas');
    }

    // ============================================================
    // ✏️ Editar factura
    // ============================================================

    public function editarFactura()
    {
        Autenticacion::requiereRoles(['admin']);

        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarFacturas');
        }

        $factura = $this->obtenerFacturaPorId($id);
        if (!$factura) {
            $this->setMensaje('error', 'Factura no encontrada');
            $this->redirigir('gestionarFacturas');
        }

        $pacientes = $this->obtenerPacientesConDatos();
        $citas = $this->obtenerCitasConDatos();

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
            $pdo = BaseDatos::pdo();
            
            $sql = "UPDATE facturas 
                    SET paciente_id = :paciente_id,
                        cita_id = :cita_id,
                        monto = :monto,
                        estado = :estado
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id'          => $id,
                ':paciente_id' => $_POST['paciente_id'],
                ':cita_id'     => $_POST['cita_id'],
                ':monto'       => $_POST['monto'],
                ':estado'      => $_POST['estado']
            ]);

            $this->setMensaje('exito', 'Factura actualizada correctamente ✅');
        } catch (Exception $e) {
            error_log("Error al actualizar factura: " . $e->getMessage());
            $this->setMensaje('error', 'Error al actualizar factura');
        }

        $this->redirigir('gestionarFacturas');
    }

    // ============================================================
    // 🗑️ Eliminar factura
    // ============================================================

    public function eliminarFactura()
    {
        Autenticacion::requiereRoles(['admin']);

        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarFacturas');
        }

        try {
            $pdo = BaseDatos::pdo();
            
            $sql = "DELETE FROM facturas WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            $this->setMensaje('exito', 'Factura eliminada exitosamente 🗑️');
        } catch (Exception $e) {
            error_log("Error al eliminar factura: " . $e->getMessage());
            $this->setMensaje('error', 'Error al eliminar factura');
        }

        $this->redirigir('gestionarFacturas');
    }

    // ============================================================
    // 👁️ Ver factura
    // ============================================================

    public function ver()
    {
        Autenticacion::requiereRoles(['admin', 'paciente']);

        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarFacturas');
        }

        $factura = $this->obtenerFacturaPorId($id);
        
        if (!$factura) {
            $this->setMensaje('error', 'Factura no encontrada');
            $this->redirigir('gestionarFacturas');
        }

        $vistaInterna = __DIR__ . "/../vistas/admin/verFactura.php";
        
        if (Autenticacion::tieneRol('admin')) {
            require __DIR__ . "/../../includes/layout-admin.php";
        } else {
            require __DIR__ . "/../../includes/layout-paciente.php";
        }
    }

    // ============================================================
    // 📥 Descargar factura (PDF)
    // ============================================================

    public function descargar()
    {
        Autenticacion::requiereRoles(['admin', 'paciente']);

        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarFacturas');
        }

        // TODO: Implementar generación de PDF con TCPDF o similar
        $this->setMensaje('error', 'Funcionalidad de descarga en desarrollo');
        $this->redirigir('verFactura', ['id' => $id]);
    }

    // ============================================================
    // 📌 Métodos auxiliares privados
    // ============================================================

    /**
     * Obtener factura por ID con todos sus datos
     */
    private function obtenerFacturaPorId(int $id): ?array
    {
        $pdo = BaseDatos::pdo();

        $sql = "
            SELECT 
                f.id,
                f.paciente_id,
                f.cita_id,
                f.monto,
                f.estado,
                f.emitida_en,
                u.nombre AS paciente_nombre,
                u.email AS paciente_email,
                u.telefono AS paciente_telefono,
                c.fecha AS cita_fecha,
                ud.nombre AS doctor_nombre
            FROM facturas f
            LEFT JOIN pacientes p ON f.paciente_id = p.id
            LEFT JOIN usuarios u ON p.usuario_id = u.id
            LEFT JOIN citas c ON f.cita_id = c.id
            LEFT JOIN doctores d ON c.doctor_id = d.id
            LEFT JOIN usuarios ud ON d.usuario_id = ud.id
            WHERE f.id = :id
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);

        return $factura ?: null;
    }

    /**
     * Pacientes con datos de usuario
     */
    private function obtenerPacientesConDatos(): array
    {
        $pdo = BaseDatos::pdo();

        $sql = "
            SELECT 
                p.id,
                u.nombre,
                u.email,
                u.telefono
            FROM pacientes p
            INNER JOIN usuarios u ON p.usuario_id = u.id
            ORDER BY u.nombre ASC
        ";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Citas disponibles con datos completos (no canceladas)
     */
    private function obtenerCitasConDatos(): array
    {
        $pdo = BaseDatos::pdo();

        $sql = "
            SELECT 
                c.id,
                c.paciente_id,
                c.fecha,
                c.estado,
                up.nombre AS paciente_nombre,
                ud.nombre AS doctor_nombre
            FROM citas c
            LEFT JOIN pacientes p ON c.paciente_id = p.id
            LEFT JOIN usuarios up ON p.usuario_id = up.id
            LEFT JOIN doctores d ON c.doctor_id = d.id
            LEFT JOIN usuarios ud ON d.usuario_id = ud.id
            WHERE LOWER(c.estado) != 'cancelada'
            ORDER BY c.fecha DESC
        ";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}