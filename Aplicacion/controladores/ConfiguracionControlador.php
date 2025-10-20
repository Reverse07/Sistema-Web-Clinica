<?php
require_once __DIR__ . "/../../nucleo/Autenticacion.php";
require_once __DIR__ . "/../../nucleo/BaseDatos.php";
use User\Clinica\GeneradorPDFConfiguracion;
/**
 * ConfiguracionControlador
 * 
 * Responsabilidad: Gestión de configuraciones del sistema
 * - Configuración general de la clínica
 * - Configuración de notificaciones
 * - Configuración de seguridad
 * - Sistema de auditoría
 * - Solo accesible por administradores
 */



class ConfiguracionControlador
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
     * Establece mensaje flash en sesión
     */
    private function setMensaje($tipo, $texto)
    {
        $_SESSION['mensaje'] = ['tipo' => $tipo, 'texto' => $texto];
    }

    // ========================================
    // VISTA PRINCIPAL DE CONFIGURACIÓN
    // ========================================

    /**
     * Muestra la vista principal de configuración
     */
    public function index()
    {
        Autenticacion::requiereRoles(['admin']);
        
        // Obtener todas las configuraciones
        $configs = $this->obtenerTodasConfiguraciones();
        
        // Valores por defecto si no existen
        $configs = array_merge([
            'nombre_clinica' => 'Clínica Salud Total',
            'ruc' => '',
            'telefono' => '',
            'email' => '',
            'direccion' => '',
            'horario_atencion' => "Lunes a Viernes: 8:00 AM - 8:00 PM\nSábados: 9:00 AM - 2:00 PM\nDomingos: Cerrado",
            'sesion_timeout' => '30',
            'max_login_attempts' => '3',
            'notif_email' => '1',
            'notif_recordatorios' => '1',
            'notif_facturas' => '0',
            'notif_reportes' => '1',
            'two_factor_auth' => '0',
            'audit_log' => '1'
        ], $configs);
        
        $vistaInterna = __DIR__ . "/../vistas/admin/configuracion.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    // ========================================
    // OBTENCIÓN DE CONFIGURACIONES
    // ========================================

    /**
     * Obtiene todas las configuraciones del sistema
     */
    private function obtenerTodasConfiguraciones()
    {
        try {
            $sql = "SELECT clave, valor, tipo, descripcion 
                    FROM configuraciones 
                    ORDER BY categoria, clave";
            $stmt = BaseDatos::pdo()->prepare($sql);
            $stmt->execute();
            
            $configs = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $configs[$row['clave']] = $row['valor'];
            }
            
            return $configs;
        } catch (PDOException $e) {
            error_log("Error al obtener configuraciones: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene una configuración específica
     */
    public static function obtenerConfiguracion($clave, $valorPorDefecto = null)
    {
        try {
            $sql = "SELECT valor FROM configuraciones WHERE clave = :clave";
            $stmt = BaseDatos::pdo()->prepare($sql);
            $stmt->bindParam(':clave', $clave);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['valor'] : $valorPorDefecto;
        } catch (PDOException $e) {
            error_log("Error al obtener configuración: " . $e->getMessage());
            return $valorPorDefecto;
        }
    }

    // ========================================
    // GUARDAR CONFIGURACIONES
    // ========================================

    /**
     * Guarda la configuración general de la clínica
     */
    public function guardarConfiguracion()
    {
        Autenticacion::requiereRoles(['admin']);

        // Configuraciones a guardar
        $configuraciones = [
            'nombre_clinica' => $_POST['nombre_clinica'] ?? '',
            'ruc' => $_POST['ruc'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'email' => $_POST['email'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'horario_atencion' => $_POST['horario_atencion'] ?? ''
        ];

        // Validaciones básicas
        if (empty($configuraciones['nombre_clinica'])) {
            $this->setMensaje('error', 'El nombre de la clínica es obligatorio');
            $this->redirigir('configuracion');
        }

        if (!empty($configuraciones['ruc']) && !preg_match('/^[0-9]{11}$/', $configuraciones['ruc'])) {
            $this->setMensaje('error', 'El RUC debe tener 11 dígitos');
            $this->redirigir('configuracion');
        }

        if (!empty($configuraciones['email']) && !filter_var($configuraciones['email'], FILTER_VALIDATE_EMAIL)) {
            $this->setMensaje('error', 'El email no es válido');
            $this->redirigir('configuracion');
        }

        try {
            BaseDatos::pdo()->beginTransaction();

            // Guardar cada configuración
            foreach ($configuraciones as $clave => $valor) {
                $this->actualizarOInsertarConfig($clave, $valor, 'general');
            }

            BaseDatos::pdo()->commit();

            // Registrar en auditoría
            $this->registrarAuditoria(
                'actualizar_configuracion',
                'Configuración general actualizada',
                'configuraciones'
            );

            $this->setMensaje('exito', '✅ Configuración guardada exitosamente');
        } catch (Exception $e) {
            BaseDatos::pdo()->rollBack();
            error_log("Error al guardar configuración: " . $e->getMessage());
            $this->setMensaje('error', 'Error al guardar la configuración');
        }

        $this->redirigir('configuracion');
    }

    /**
     * Guarda las configuraciones de notificaciones
     */
    public function guardarNotificaciones()
    {
        Autenticacion::requiereRoles(['admin']);

        try {
            BaseDatos::pdo()->beginTransaction();

            $notificaciones = [
                'notif_email' => isset($_POST['notif_email']) ? '1' : '0',
                'notif_recordatorios' => isset($_POST['notif_recordatorios']) ? '1' : '0',
                'notif_facturas' => isset($_POST['notif_facturas']) ? '1' : '0',
                'notif_reportes' => isset($_POST['notif_reportes']) ? '1' : '0'
            ];

            foreach ($notificaciones as $clave => $valor) {
                $this->actualizarOInsertarConfig($clave, $valor, 'notificaciones');
            }

            BaseDatos::pdo()->commit();

            // Registrar en auditoría
            $this->registrarAuditoria(
                'actualizar_configuracion',
                'Configuración de notificaciones actualizada',
                'configuraciones'
            );

            $this->setMensaje('exito', '✅ Configuración de notificaciones guardada');
        } catch (Exception $e) {
            BaseDatos::pdo()->rollBack();
            error_log("Error al guardar notificaciones: " . $e->getMessage());
            $this->setMensaje('error', 'Error al guardar notificaciones');
        }

        $this->redirigir('configuracion');
    }

    /**
     * Guarda las configuraciones de seguridad
     */
    public function guardarSeguridadConfig()
    {
        Autenticacion::requiereRoles(['admin']);

        $sesionTimeout = filter_var($_POST['sesion_timeout'] ?? 30, FILTER_VALIDATE_INT);
        $maxLoginAttempts = filter_var($_POST['max_login_attempts'] ?? 3, FILTER_VALIDATE_INT);

        // Validaciones
        if ($sesionTimeout < 5 || $sesionTimeout > 120) {
            $this->setMensaje('error', 'El tiempo de sesión debe estar entre 5 y 120 minutos');
            $this->redirigir('configuracion');
        }

        if ($maxLoginAttempts < 3 || $maxLoginAttempts > 10) {
            $this->setMensaje('error', 'Los intentos de login deben estar entre 3 y 10');
            $this->redirigir('configuracion');
        }

        try {
            BaseDatos::pdo()->beginTransaction();

            $seguridad = [
                'sesion_timeout' => $sesionTimeout,
                'max_login_attempts' => $maxLoginAttempts,
                'two_factor_auth' => isset($_POST['two_factor_auth']) ? '1' : '0',
                'audit_log' => isset($_POST['audit_log']) ? '1' : '0'
            ];

            foreach ($seguridad as $clave => $valor) {
                $this->actualizarOInsertarConfig($clave, $valor, 'seguridad');
            }

            BaseDatos::pdo()->commit();

            // Actualizar el tiempo de sesión en PHP
            if ($sesionTimeout) {
                ini_set('session.gc_maxlifetime', $sesionTimeout * 60);
            }

            // Registrar en auditoría
            $this->registrarAuditoria(
                'actualizar_configuracion',
                'Configuración de seguridad actualizada',
                'configuraciones'
            );

            $this->setMensaje('exito', '🔒 Configuración de seguridad guardada');
        } catch (Exception $e) {
            BaseDatos::pdo()->rollBack();
            error_log("Error al guardar configuración de seguridad: " . $e->getMessage());
            $this->setMensaje('error', 'Error al guardar configuración de seguridad');
        }

        $this->redirigir('configuracion');
    }

    // ========================================
    // UTILIDADES DE CONFIGURACIÓN
    // ========================================

    /**
     * Actualiza o inserta una configuración
     */
    private function actualizarOInsertarConfig($clave, $valor, $categoria = 'general')
    {
        try {
            // Intentar actualizar primero
            $sql = "UPDATE configuraciones 
                    SET valor = :valor, fecha_actualizacion = NOW() 
                    WHERE clave = :clave";
            $stmt = BaseDatos::pdo()->prepare($sql);
            $stmt->bindParam(':clave', $clave);
            $stmt->bindParam(':valor', $valor);
            $stmt->execute();

            // Si no se actualizó ninguna fila, insertar
            if ($stmt->rowCount() === 0) {
                $sql = "INSERT INTO configuraciones (clave, valor, categoria, tipo, fecha_creacion) 
                        VALUES (:clave, :valor, :categoria, 'string', NOW())";
                $stmt = BaseDatos::pdo()->prepare($sql);
                $stmt->bindParam(':clave', $clave);
                $stmt->bindParam(':valor', $valor);
                $stmt->bindParam(':categoria', $categoria);
                $stmt->execute();
            }

            return true;
        } catch (PDOException $e) {
            error_log("Error al actualizar/insertar configuración: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Restaura configuraciones por defecto
     */
    public function restaurarPorDefecto()
    {
        Autenticacion::requiereRoles(['admin']);

        try {
            BaseDatos::pdo()->beginTransaction();

            $configsDefecto = [
                'nombre_clinica' => 'Clínica Salud Total',
                'ruc' => '',
                'telefono' => '',
                'email' => '',
                'direccion' => '',
                'horario_atencion' => "Lunes a Viernes: 8:00 AM - 8:00 PM\nSábados: 9:00 AM - 2:00 PM\nDomingos: Cerrado",
                'sesion_timeout' => '30',
                'max_login_attempts' => '3',
                'notif_email' => '1',
                'notif_recordatorios' => '1',
                'notif_facturas' => '0',
                'notif_reportes' => '1',
                'two_factor_auth' => '0',
                'audit_log' => '1'
            ];

            foreach ($configsDefecto as $clave => $valor) {
                $this->actualizarOInsertarConfig($clave, $valor);
            }

            BaseDatos::pdo()->commit();

            // Registrar en auditoría
            $this->registrarAuditoria(
                'restaurar_configuracion',
                'Configuraciones restauradas por defecto',
                'configuraciones'
            );

            $this->setMensaje('exito', '♻️ Configuraciones restauradas por defecto');
        } catch (Exception $e) {
            BaseDatos::pdo()->rollBack();
            error_log("Error al restaurar configuraciones: " . $e->getMessage());
            $this->setMensaje('error', 'Error al restaurar configuraciones');
        }

        $this->redirigir('configuracion');
    }

    // ========================================
    // SISTEMA DE AUDITORÍA
    // ========================================

    /**
     * Registra una acción en el log de auditoría
     */
    private function registrarAuditoria($accion, $descripcion, $tabla = null, $registroId = null)
    {
        try {
            // Verificar si el log de auditoría está activo
            $auditActivo = self::obtenerConfiguracion('audit_log', '1');

            if ($auditActivo === '0') {
                return; // Auditoría desactivada
            }

            $sql = "INSERT INTO auditoria 
                    (usuario_id, accion, descripcion, tabla, registro_id, ip_address, user_agent, fecha_accion) 
                    VALUES (:usuario_id, :accion, :descripcion, :tabla, :registro_id, :ip, :user_agent, NOW())";

            $stmt = BaseDatos::pdo()->prepare($sql);
            
            $usuarioId = $_SESSION['usuario_id'] ?? null;
            $stmt->bindParam(':usuario_id', $usuarioId);
            $stmt->bindParam(':accion', $accion);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':tabla', $tabla);
            $stmt->bindParam(':registro_id', $registroId);

            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

            $stmt->bindParam(':ip', $ip);
            $stmt->bindParam(':user_agent', $userAgent);
            $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al registrar auditoría: " . $e->getMessage());
        }
    }

    /**
     * Obtiene el log de auditoría
     */
    public function verAuditoria()
    {
        Autenticacion::requiereRoles(['admin']);

        $limite = $_GET['limite'] ?? 100;
        $logs = $this->obtenerLogAuditoria((int)$limite);

        $vistaInterna = __DIR__ . "/../vistas/admin/auditoria.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * Obtiene registros del log de auditoría
     */
    private function obtenerLogAuditoria($limite = 100)
    {
        try {
            $sql = "SELECT a.*, u.nombre as usuario_nombre, u.email as usuario_email 
                    FROM auditoria a 
                    LEFT JOIN usuarios u ON a.usuario_id = u.id 
                    ORDER BY a.fecha_accion DESC 
                    LIMIT :limite";

            $stmt = BaseDatos::pdo()->prepare($sql);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener log de auditoría: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Limpia registros antiguos de auditoría
     */
    public function limpiarAuditoria()
    {
        Autenticacion::requiereRoles(['admin']);

        $dias = $_POST['dias'] ?? 90;

        try {
            $sql = "DELETE FROM auditoria 
                    WHERE fecha_accion < NOW() - INTERVAL '$dias days'";
            $stmt = BaseDatos::pdo()->prepare($sql);
            $stmt->execute();

            $eliminados = $stmt->rowCount();

            $this->registrarAuditoria(
                'limpiar_auditoria',
                "Limpieza de auditoría: $eliminados registros eliminados",
                'auditoria'
            );

            $this->setMensaje('exito', "🗑️ $eliminados registros de auditoría eliminados");
        } catch (Exception $e) {
            error_log("Error al limpiar auditoría: " . $e->getMessage());
            $this->setMensaje('error', 'Error al limpiar registros de auditoría');
        }

        $this->redirigir('verAuditoria');
    }

    // ========================================
    // EXPORTACIÓN E IMPORTACIÓN
    // ========================================

    /**
     * Exporta todas las configuraciones a JSON
     */
    public function exportarConfiguraciones()
    {
        Autenticacion::requiereRoles(['admin']);

        try {
            $configs = $this->obtenerTodasConfiguraciones();

            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="configuraciones_' . date('Y-m-d') . '.json"');

            echo json_encode($configs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            // Registrar en auditoría
            $this->registrarAuditoria(
                'exportar_configuracion',
                'Configuraciones exportadas a JSON',
                'configuraciones'
            );

            exit;

        } catch (Exception $e) {
            error_log("Error al exportar configuraciones: " . $e->getMessage());
            $this->setMensaje('error', 'Error al exportar configuraciones');
            $this->redirigir('configuracion');
        }
    }

    /**
     * Exporta todas las configuraciones a PDF
     */
    public function exportarConfiguracionesPDF()
{
    try {
        Autenticacion::requiereRoles(['admin']);
        
        // Obtener todas las configuraciones organizadas por categoría
        $configs = $this->obtenerConfiguracionesPorCategoria();
        
        // Obtener información del usuario que exporta
        $usuarioNombre = $_SESSION['nombre'] ?? 'Administrador';
        $usuarioEmail = $_SESSION['email'] ?? '';

        // ✅ INCLUIR EL GENERADOR (ruta corregida)
        require_once __DIR__ . '/../../includes/generador-pdf-config.php';
        
        // ✅ USAR NAMESPACE CORRECTO
        $pdf = new \User\Clinica\GeneradorPDFConfiguracion();
        $pdf->generarPDF($configs, $usuarioNombre, $usuarioEmail);

        // Registrar en auditoría (esto no se ejecutará porque generarPDF hace exit)
        $this->registrarAuditoria(
            'exportar_configuracion_pdf',
            'Configuraciones exportadas a PDF',
            'configuraciones'
        );

    } catch (Exception $e) {
        error_log("Error al exportar configuraciones a PDF: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        $_SESSION['error'] = 'Error al generar PDF: ' . $e->getMessage();
        header("Location: ?accion=configuracion");
        exit;
    }
}


    /**
     * Obtiene configuraciones organizadas por categoría
     */
   private function obtenerConfiguracionesPorCategoria()
{
    try {
        $sql = "SELECT clave, valor, tipo, descripcion, categoria 
                FROM configuraciones 
                ORDER BY categoria, clave";
        $stmt = BaseDatos::pdo()->prepare($sql);
        $stmt->execute();
        
        $configsPorCategoria = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categoria = $row['categoria'] ?? 'general';
            if (!isset($configsPorCategoria[$categoria])) {
                $configsPorCategoria[$categoria] = [];
            }
            $configsPorCategoria[$categoria][] = $row;
        }
        
        return $configsPorCategoria;
        
    } catch (PDOException $e) {
        error_log("Error al obtener configuraciones por categoría: " . $e->getMessage());
        return [];
    }
}
}