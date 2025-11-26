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

    /**
     * Obtiene el ID del paciente desde el usuario_id
     */
    private function obtenerPacienteId(int $usuarioId): ?int
    {
        try {
            $pdo = BaseDatos::pdo();
            $sql = "SELECT id FROM pacientes WHERE usuario_id = :usuario_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':usuario_id' => $usuarioId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? (int)$result['id'] : null;
        } catch (Exception $e) {
            error_log("❌ Error en obtenerPacienteId: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene el ID del doctor desde el usuario_id
     */
    private function obtenerDoctorId(int $usuarioId): ?int
    {
        try {
            $pdo = BaseDatos::pdo();
            $sql = "SELECT id FROM doctores WHERE usuario_id = :usuario_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':usuario_id' => $usuarioId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? (int)$result['id'] : null;
        } catch (Exception $e) {
            error_log("❌ Error en obtenerDoctorId: " . $e->getMessage());
            return null;
        }
    }

    // ========================================
    // VISTAS PARA PACIENTE
    // ========================================

    /**
     * Muestra las recetas del paciente autenticado
     */
    public function misRecetas()
    {
        Autenticacion::requiereRoles(['paciente']);
        
        $usuarioId = Autenticacion::usuarioId();
        $pacienteId = $this->obtenerPacienteId($usuarioId);

        if (!$pacienteId) {
            $this->setMensaje('error', 'No se encontró el registro de paciente');
            $this->redirigir('dashboardPaciente');
        }

        // Obtener recetas del paciente
        $recetas = Receta::obtenerPorPaciente($pacienteId);
        
        // Estadísticas
        $totalRecetas = count($recetas);
        $recetasRecientes = array_filter($recetas, function($r) {
            return strtotime($r['fecha_emision']) > strtotime('-30 days');
        });
        $totalRecetasRecientes = count($recetasRecientes);

        $vistaInterna = __DIR__ . "/../vistas/paciente/misRecetas.php";
        require __DIR__ . "/../../includes/layout-paciente.php";
    }

    /**
     * Muestra el detalle de una receta (Paciente)
     */
    public function verMiReceta()
    {
        Autenticacion::requiereRoles(['paciente']);
        
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->setMensaje('error', 'ID de receta no proporcionado');
            $this->redirigir('misRecetas');
        }

        // Obtener receta con datos completos
        $receta = Receta::obtenerPorId($id);
        
        if (!$receta) {
            $this->setMensaje('error', 'Receta no encontrada');
            $this->redirigir('misRecetas');
        }

        // Verificar que sea del paciente autenticado
        $usuarioId = Autenticacion::usuarioId();
        $pacienteId = $this->obtenerPacienteId($usuarioId);
        
        if ($receta['paciente_id'] != $pacienteId) {
            $this->setMensaje('error', 'No tienes permiso para ver esta receta');
            $this->redirigir('misRecetas');
        }

        // Obtener detalles (medicamentos)
        $detalles = DetalleReceta::obtenerPorReceta($id);

        $vistaInterna = __DIR__ . "/../vistas/paciente/verReceta.php";
        require __DIR__ . "/../../includes/layout-paciente.php";
    }

    // ========================================
    // LISTADO Y VISUALIZACIÓN (ADMIN/DOCTOR)
    // ========================================

    /**
     * Alias para compatibilidad con enrutador
     */
    public function gestionar()
    {
        $this->listar();
    }

    /**
     * Muestra todas las recetas (admin) o del doctor actual
     */
    public function listar()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);

        $rol = Autenticacion::rol();
        $usuarioId = Autenticacion::usuarioId();

        if ($rol === 'doctor') {
            $doctorId = $this->obtenerDoctorId($usuarioId);
            $recetas = Receta::obtenerPorDoctor($doctorId);
        } else {
            $recetas = Receta::todas();
        }

        $vistaInterna = __DIR__ . "/../vistas/admin/recetas.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * Muestra los detalles de una receta (Admin/Doctor)
     */
    public function ver()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);

        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarRecetas');
        }

        $receta = Receta::obtenerPorId($id);
        
        if (!$receta) {
            $this->setMensaje('error', 'Receta no encontrada');
            $this->redirigir('gestionarRecetas');
        }

        // Obtener detalles
        $detalles = DetalleReceta::obtenerPorReceta($id);

        $vistaInterna = __DIR__ . "/../vistas/admin/verReceta.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    // ========================================
    // CREAR Y GUARDAR (DOCTOR)
    // ========================================

    /**
     * Muestra formulario para crear una nueva receta
     */
  public function crear()
{
    Autenticacion::requiereRoles(['doctor']);
    
    $vistaInterna = __DIR__ . "/../vistas/doctor/crearReceta.php";
    require __DIR__ . "/../../includes/layout-doctor.php";
}

    /**
     * Obtiene lista de pacientes con datos completos
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
        
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Guarda la receta con sus detalles
     */
    public function guardar()
    {
        Autenticacion::requiereRoles(['admin', 'doctor']);

        if (empty($_POST['paciente_id'])) {
            $this->setMensaje('error', 'Debe seleccionar un paciente');
            $this->redirigir('crearReceta');
        }

        try {
            $pdo = BaseDatos::pdo();
            $pdo->beginTransaction();

            // Obtener ID del doctor
            $usuarioId = Autenticacion::usuarioId();
            $doctorId = $this->obtenerDoctorId($usuarioId);

            if (!$doctorId) {
                throw new Exception('No se encontró el registro de doctor');
            }

            // Crear receta principal
            $recetaId = Receta::crearDesdeArray([
                'doctor_id' => $doctorId,
                'paciente_id' => $_POST['paciente_id'],
                'observaciones' => $_POST['observaciones'] ?? ''
            ]);

            // Insertar los medicamentos asociados
            if (!empty($_POST['medicamentos']) && is_array($_POST['medicamentos'])) {
                foreach ($_POST['medicamentos'] as $med) {
                    if (!empty($med['id']) && !empty($med['dosis'])) {
                        DetalleReceta::crear([
                            'receta_id' => $recetaId,
                            'medicamento_id' => $med['id'],
                            'dosis' => $med['dosis'],
                            'frecuencia' => $med['frecuencia'] ?? '',
                            'duracion' => $med['duracion'] ?? '',
                            'cantidad' => $med['cantidad'] ?? 1
                        ]);
                    }
                }
            }

            $pdo->commit();
            $this->setMensaje('exito', 'Receta registrada exitosamente ✅');
            $this->redirigir('gestionarRecetas');

        } catch (Exception $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("❌ Error al guardar receta: " . $e->getMessage());
            $this->setMensaje('error', 'Error al registrar la receta: ' . $e->getMessage());
            $this->redirigir('crearReceta');
        }
    }

    // ========================================
    // IMPRIMIR
    // ========================================

    /**
     * Genera PDF de la receta
     */
    public function imprimir()
    {
        Autenticacion::requiereLogin();

        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('misRecetas');
        }

        $receta = Receta::obtenerPorId($id);
        
        if (!$receta) {
            $this->setMensaje('error', 'Receta no encontrada');
            $this->redirigir('misRecetas');
        }

        // Verificar permisos
        $rol = Autenticacion::rol();
        $usuarioId = Autenticacion::usuarioId();
        
        if ($rol === 'paciente') {
            $pacienteId = $this->obtenerPacienteId($usuarioId);
            if ($receta['paciente_id'] != $pacienteId) {
                $this->setMensaje('error', 'No tienes permiso para imprimir esta receta');
                $this->redirigir('misRecetas');
            }
        }

        // Obtener detalles
        $detalles = DetalleReceta::obtenerPorReceta($id);

        // Cargar vista de impresión (sin layout)
        require __DIR__ . "/../vistas/recetas/imprimir.php";
    }

    public function verReceta()
{
    Autenticacion::requiereRoles(['doctor']);
    
    $vistaInterna = __DIR__ . "/../vistas/doctor/verReceta.php";
    require __DIR__ . "/../../includes/layout-doctor.php";
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
        
        if (!$id) {
            $this->setMensaje('error', 'ID no proporcionado');
            $this->redirigir('gestionarRecetas');
        }

        $receta = Receta::buscarPorId($id);

        if (!$receta) {
            $this->setMensaje('error', 'Receta no encontrada');
            $this->redirigir('gestionarRecetas');
        }

        // Validar permiso del doctor
        $rol = Autenticacion::rol();
        
        if ($rol === 'doctor') {
            $usuarioId = Autenticacion::usuarioId();
            $doctorId = $this->obtenerDoctorId($usuarioId);
            
            if ($receta->getDoctorId() != $doctorId) {
                $this->setMensaje('error', 'No tienes permiso para eliminar esta receta');
                $this->redirigir('gestionarRecetas');
            }
        }

        try {
            Receta::eliminar($id);
            $this->setMensaje('exito', 'Receta eliminada exitosamente 🗑️');
        } catch (Exception $e) {
            error_log("❌ Error al eliminar receta: " . $e->getMessage());
            $this->setMensaje('error', 'Error al eliminar la receta');
        }

        $this->redirigir('gestionarRecetas');
    }
}