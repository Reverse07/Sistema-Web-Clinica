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
        $rol = $_SESSION['rol'] ?? 'admin';
        $this->redirigir($rol === 'admin' ? 'gestionarFacturas' : 'misFacturas');
    }

    $factura = $this->obtenerFacturaPorId($id);
    
    if (!$factura) {
        $this->setMensaje('error', 'Factura no encontrada');
        $rol = $_SESSION['rol'] ?? 'admin';
        $this->redirigir($rol === 'admin' ? 'gestionarFacturas' : 'misFacturas');
    }

    // Verificar permisos si es paciente
    $rol = $_SESSION['rol'] ?? 'admin';
    
    if ($rol === 'paciente') {
        $usuarioId = Autenticacion::usuarioId();
        $pacienteId = $this->obtenerPacienteIdPorUsuario($usuarioId);
        
        if ($factura['paciente_id'] != $pacienteId) {
            $this->setMensaje('error', 'No tienes permiso para ver esta factura');
            $this->redirigir('misFacturas');
        }
        
        // Vista para paciente
        $vistaInterna = __DIR__ . "/../vistas/paciente/verFactura.php";
        require __DIR__ . "/../../includes/layout-paciente.php";
    } else {
        // Vista para admin
        $vistaInterna = __DIR__ . "/../vistas/admin/verFactura.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }
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

    
// ============================================================
// 📥 Descargar factura como PDF - CORREGIDO
// ============================================================

/**
 * Genera y descarga la factura en PDF usando TCPDF
 */
public function descargar()
{
    // ⚠️ IMPORTANTE: Limpiar cualquier salida previa
    ob_clean();
    
    Autenticacion::requiereRoles(['admin', 'paciente']);

    $id = $_GET['id'] ?? null;

    if (!$id) {
        $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'ID no proporcionado'];
        $rol = $_SESSION['rol'] ?? 'admin';
        header("Location: " . BASE_URL . "/index.php?accion=" . ($rol === 'admin' ? 'gestionarFacturas' : 'misFacturas'));
        exit;
    }

    $factura = $this->obtenerFacturaPorId($id);
    
    if (!$factura) {
        $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Factura no encontrada'];
        $rol = $_SESSION['rol'] ?? 'admin';
        header("Location: " . BASE_URL . "/index.php?accion=" . ($rol === 'admin' ? 'gestionarFacturas' : 'misFacturas'));
        exit;
    }

    // Verificar permisos si es paciente
    $rol = $_SESSION['rol'] ?? 'admin';
    if ($rol === 'paciente') {
        $usuarioId = Autenticacion::usuarioId();
        $pacienteId = $this->obtenerPacienteIdPorUsuario($usuarioId);
        
        if ($factura['paciente_id'] != $pacienteId) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'No tienes permiso para descargar esta factura'];
            header("Location: " . BASE_URL . "/index.php?accion=misFacturas");
            exit;
        }
    }

    // Generar PDF
    $this->generarPDF($factura);
}

/**
 * Genera el PDF de la factura usando TCPDF
 */
private function generarPDF(array $factura)
{
    // Verificar si existe TCPDF
    if (!class_exists('TCPDF')) {
        $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'La librería TCPDF no está instalada. Ejecuta: composer require tecnickcom/tcpdf'];
        header("Location: " . BASE_URL . "/index.php?accion=verFactura&id=" . $factura['id']);
        exit;
    }

    try {
        // ⚠️ CRÍTICO: Limpiar todo el buffer de salida
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Crear nuevo PDF
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Configuración del documento
        $pdf->SetCreator('Sistema Clínica');
        $pdf->SetAuthor('Clínica Salud Total');
        $pdf->SetTitle('Factura #' . str_pad($factura['id'], 6, '0', STR_PAD_LEFT));
        $pdf->SetSubject('Factura de Consulta Médica');

        // Quitar header y footer por defecto
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Configurar márgenes COMPACTOS
        $pdf->SetMargins(12, 10, 12);
        $pdf->SetAutoPageBreak(false); // ⚠️ Desactivar salto automático

        // Agregar página
        $pdf->AddPage();

        // Fuente
        $pdf->SetFont('helvetica', '', 9);

        // Contenido HTML
        $html = $this->generarHTMLFactura($factura);

        // Escribir HTML
        $pdf->writeHTML($html, true, false, true, false, '');

        // Nombre del archivo
        $filename = 'Factura_' . str_pad($factura['id'], 6, '0', STR_PAD_LEFT) . '.pdf';

        // ⚠️ IMPORTANTE: Descargar PDF con Output limpio
        $pdf->Output($filename, 'D');
        exit;

    } catch (Exception $e) {
        error_log("Error generando PDF: " . $e->getMessage());
        $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al generar el PDF: ' . $e->getMessage()];
        header("Location: " . BASE_URL . "/index.php?accion=verFactura&id=" . $factura['id']);
        exit;
    }
}

/**
 * Genera el HTML para el PDF - DISEÑO COMPACTO UNA PÁGINA
 */
private function generarHTMLFactura(array $factura): string
{
    $numeroFactura = str_pad($factura['id'], 6, '0', STR_PAD_LEFT);
    $fecha = date('d/m/Y', strtotime($factura['emitida_en']));
    $subtotal = $factura['monto'];
    $igv = $subtotal * 0.18;
    $total = $subtotal + $igv;
    
    $estado = strtolower($factura['estado']);
    $estadoPagada = (str_contains($estado, 'pag') || str_contains($estado, 'completada'));
    $estadoColor = $estadoPagada ? '#10b981' : '#f59e0b';
    $estadoBg = $estadoPagada ? '#d1fae5' : '#fef3c7';
    $estadoTexto = strtoupper(htmlspecialchars($factura['estado']));

    $html = '
    <style>
        body { 
            font-family: "Helvetica", "Arial", sans-serif;
            color: #1e293b;
            line-height: 1.4;
            font-size: 9px;
        }
        
        /* HEADER COMPACTO */
        .header-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 12px 15px;
            margin: -10px -12px 15px -12px;
            text-align: center;
            border-radius: 0 0 10px 10px;
        }
        
        .header-title {
            color: white;
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 6px 0;
            letter-spacing: 0.5px;
        }
        
        .header-info {
            color: rgba(255,255,255,0.95);
            font-size: 8px;
            line-height: 1.5;
        }
        
        /* GRID INFO */
        .info-grid {
            margin-bottom: 12px;
        }
        
        .info-grid table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-grid td {
            padding: 8px;
            vertical-align: top;
        }
        
        .info-left {
            width: 55%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        
        .info-right {
            width: 43%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-align: right;
        }
        
        .factura-num {
            font-size: 16px;
            font-weight: bold;
            color: #667eea;
            margin: 0 0 4px 0;
        }
        
        .estado-badge {
            display: inline-block;
            padding: 4px 12px;
            background: ' . $estadoBg . ';
            color: ' . $estadoColor . ';
            border: 1px solid ' . $estadoColor . ';
            border-radius: 15px;
            font-weight: bold;
            font-size: 8px;
            margin-top: 3px;
        }
        
        /* CLIENTE BOX */
        .cliente-box {
            background: white;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #667eea;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 12px;
        }
        
        .section-title {
            color: #667eea;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 6px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .cliente-info {
            font-size: 8px;
            line-height: 1.6;
        }
        
        .cliente-info strong {
            color: #1e293b;
            font-weight: 600;
            min-width: 60px;
            display: inline-block;
        }
        
        /* TABLA SERVICIOS */
        .services-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 12px;
        }
        
        .services-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .services-table th {
            color: white;
            padding: 8px 6px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .services-table td {
            padding: 8px 6px;
            font-size: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .service-name {
            font-weight: bold;
            font-size: 9px;
            color: #1e293b;
            margin-bottom: 3px;
        }
        
        .service-detail {
            color: #64748b;
            font-size: 7px;
            line-height: 1.4;
        }
        
        /* TOTALES COMPACTO */
        .totales-box {
            width: 55%;
            margin-left: auto;
        }
        
        .totales-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .totales-table td {
            padding: 6px 10px;
            font-size: 9px;
        }
        
        .totales-row {
            border-bottom: 1px solid #e2e8f0;
            color: #64748b;
        }
        
        .totales-final {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            font-size: 12px;
        }
        
        /* FOOTER COMPACTO */
        .footer-box {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
        }
        
        .footer-thanks {
            font-size: 10px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .footer-text {
            font-size: 7px;
            color: #64748b;
            line-height: 1.4;
        }
        
        .footer-legal {
            font-size: 6px;
            color: #94a3b8;
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px solid #e2e8f0;
        }
    </style>

    <!-- HEADER -->
    <div class="header-box">
        <div class="header-title">🏥 CLÍNICA SALUD TOTAL</div>
        <div class="header-info">
            Av. Principal 123, Lima, Perú | Tel: +51 999 888 777 | Email: facturacion@clinica.com | RUC: 20123456789
        </div>
    </div>

    <!-- INFO GRID -->
    <div class="info-grid">
        <table>
            <tr>
                <td class="info-left">
                    <div class="factura-num">FACTURA N° ' . $numeroFactura . '</div>
                    <div style="font-size: 8px; color: #64748b;">Fecha de emisión: ' . $fecha . '</div>
                </td>
                <td style="width: 2%;"></td>
                <td class="info-right">
                    <div class="estado-badge">' . $estadoTexto . '</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- CLIENTE -->
    <div class="cliente-box">
        <div class="section-title">👤 DATOS DEL CLIENTE</div>
        <div class="cliente-info">
            <strong>Nombre:</strong> ' . htmlspecialchars($factura['paciente_nombre']) . '<br>';
    
    if (!empty($factura['paciente_email'])) {
        $html .= '<strong>Email:</strong> ' . htmlspecialchars($factura['paciente_email']);
    }
    
    if (!empty($factura['paciente_telefono'])) {
        $html .= ' | <strong>Tel:</strong> ' . htmlspecialchars($factura['paciente_telefono']);
    }
    
    $html .= '
        </div>
    </div>

    <!-- SERVICIOS -->
    <div class="section-title" style="margin-bottom: 6px;">📋 DETALLE DE SERVICIOS</div>
    <table class="services-table">
        <thead>
            <tr>
                <th style="width: 50%; text-align: left;">Descripción</th>
                <th style="width: 12%; text-align: center;">Cant.</th>
                <th style="width: 19%; text-align: right;">P. Unit.</th>
                <th style="width: 19%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="service-name">💊 Consulta Médica Especializada</div>
                    <div class="service-detail">';
    
    if (!empty($factura['doctor_nombre'])) {
        $html .= 'Dr. ' . htmlspecialchars($factura['doctor_nombre']);
    }
    
    if (!empty($factura['cita_fecha'])) {
        $html .= ' | ' . date('d/m/Y h:i A', strtotime($factura['cita_fecha']));
    }
    
    $html .= '
                    </div>
                </td>
                <td style="text-align: center; font-weight: bold;">1</td>
                <td style="text-align: right;">S/ ' . number_format($subtotal, 2) . '</td>
                <td style="text-align: right; font-weight: bold; color: #667eea;">S/ ' . number_format($subtotal, 2) . '</td>
            </tr>
        </tbody>
    </table>

    <!-- TOTALES -->
    <div class="totales-box">
        <table class="totales-table">
            <tr class="totales-row">
                <td style="text-align: right;"><strong>Subtotal:</strong></td>
                <td style="text-align: right; width: 40%;">S/ ' . number_format($subtotal, 2) . '</td>
            </tr>
            <tr class="totales-row">
                <td style="text-align: right;"><strong>IGV (18%):</strong></td>
                <td style="text-align: right;">S/ ' . number_format($igv, 2) . '</td>
            </tr>
            <tr class="totales-final">
                <td style="padding: 8px 10px; text-align: right;"><strong>TOTAL:</strong></td>
                <td style="text-align: right;"><strong>S/ ' . number_format($total, 2) . '</strong></td>
            </tr>
        </table>
    </div>

    <!-- FOOTER -->
    <div class="footer-box">
        <div class="footer-thanks">✨ ¡Gracias por confiar en nosotros! ✨</div>
        <div class="footer-text">
            Este documento es una representación impresa de una factura electrónica válida.
        </div>
        <div class="footer-legal">
            Generado el ' . date('d/m/Y H:i') . ' hrs | Sistema de Gestión Clínica v1.0 | © ' . date('Y') . ' Todos los derechos reservados
        </div>
    </div>
    ';

    return $html;

    
  $numeroFactura = str_pad($factura['id'], 6, '0', STR_PAD_LEFT);
    $fecha = date('d/m/Y', strtotime($factura['emitida_en']));
    $subtotal = $factura['monto'];
    $igv = $subtotal * 0.18;
    $total = $subtotal + $igv;
    
    $estado = strtolower($factura['estado']);
    $estadoPagada = (str_contains($estado, 'pag') || str_contains($estado, 'completada'));
    $estadoColor = $estadoPagada ? '#10b981' : '#f59e0b';
    $estadoBg = $estadoPagada ? '#d1fae5' : '#fef3c7';
    $estadoTexto = strtoupper(htmlspecialchars($factura['estado']));

    $html = '
    <style>
        body { 
            font-family: "Helvetica", "Arial", sans-serif;
            color: #1e293b;
            line-height: 1.6;
        }
        
        /* HEADER PREMIUM */
        .header-wrapper {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            margin: -15px -15px 25px -15px;
            text-align: center;
            border-radius: 0 0 20px 20px;
        }
        
        .header-logo {
            font-size: 36px;
            margin: 0 0 10px 0;
        }
        
        .header-title {
            color: white;
            font-size: 28px;
            font-weight: bold;
            margin: 0 0 15px 0;
            letter-spacing: 1px;
        }
        
        .header-info {
            color: rgba(255,255,255,0.95);
            font-size: 11px;
            line-height: 1.8;
        }
        
        /* FACTURA INFO */
        .factura-info-box {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .factura-info-grid {
            display: table;
            width: 100%;
        }
        
        .factura-info-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .factura-info-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
        }
        
        .factura-numero {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin: 0 0 8px 0;
        }
        
        .factura-fecha {
            font-size: 12px;
            color: #64748b;
        }
        
        .estado-badge {
            display: inline-block;
            padding: 8px 20px;
            background: ' . $estadoBg . ';
            color: ' . $estadoColor . ';
            border: 2px solid ' . $estadoColor . ';
            border-radius: 25px;
            font-weight: bold;
            font-size: 11px;
            letter-spacing: 0.5px;
            margin-top: 5px;
        }
        
        /* SECCIÓN CLIENTE */
        .section-box {
            background: white;
            border: 2px solid #e2e8f0;
            border-left: 5px solid #667eea;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 20px;
        }
        
        .section-title {
            color: #667eea;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 12px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .section-content {
            font-size: 11px;
            line-height: 1.8;
        }
        
        .section-content strong {
            color: #1e293b;
            font-weight: 600;
            display: inline-block;
            min-width: 80px;
        }
        
        /* TABLA DE SERVICIOS */
        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .services-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .services-table th {
            color: white;
            padding: 14px 12px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .services-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }
        
        .services-table tbody tr:last-child {
            border-bottom: none;
        }
        
        .services-table td {
            padding: 15px 12px;
            font-size: 11px;
            vertical-align: top;
        }
        
        .service-name {
            font-weight: bold;
            font-size: 12px;
            color: #1e293b;
            margin-bottom: 5px;
        }
        
        .service-detail {
            color: #64748b;
            font-size: 10px;
            line-height: 1.6;
        }
        
        /* TOTALES */
        .totales-section {
            margin-top: 30px;
        }
        
        .totales-table {
            width: 60%;
            margin-left: auto;
            border-collapse: collapse;
        }
        
        .totales-table td {
            padding: 10px 15px;
            font-size: 12px;
        }
        
        .totales-row {
            border-bottom: 1px solid #e2e8f0;
        }
        
        .totales-subtotal {
            color: #64748b;
        }
        
        .totales-final {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            font-size: 16px;
            border: none;
        }
        
        .totales-final td {
            padding: 15px;
        }
        
        /* FOOTER */
        .footer-section {
            margin-top: 50px;
            padding-top: 25px;
            border-top: 3px solid #e2e8f0;
            text-align: center;
        }
        
        .footer-thanks {
            font-size: 14px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 8px;
        }
        
        .footer-text {
            font-size: 10px;
            color: #64748b;
            line-height: 1.6;
        }
        
        .footer-legal {
            font-size: 9px;
            color: #94a3b8;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }
        
        /* WATERMARK */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(102, 126, 234, 0.05);
            font-weight: bold;
            z-index: -1;
            text-transform: uppercase;
        }
    </style>

    <!-- WATERMARK -->
    <div class="watermark">' . ($estadoPagada ? 'PAGADA' : 'PENDIENTE') . '</div>

    <!-- HEADER -->
    <div class="header-wrapper">
        <div class="header-logo">🏥</div>
        <h1 class="header-title">CLÍNICA SALUD TOTAL</h1>
        <div class="header-info">
            📍 Av. Principal 123, Lima, Perú<br>
            📞 Tel: +51 999 888 777 | ✉️ facturacion@clinica.com<br>
            📄 RUC: 20123456789
        </div>
    </div>

    <!-- FACTURA INFO -->
    <div class="factura-info-box">
        <div class="factura-info-grid">
            <div class="factura-info-left">
                <div class="factura-numero">FACTURA N° ' . $numeroFactura . '</div>
                <div class="factura-fecha">📅 Fecha de emisión: ' . $fecha . '</div>
            </div>
            <div class="factura-info-right">
                <div class="estado-badge">' . $estadoTexto . '</div>
            </div>
        </div>
    </div>

    <!-- DATOS DEL CLIENTE -->
    <div class="section-box">
        <div class="section-title">👤 DATOS DEL CLIENTE</div>
        <div class="section-content">
            <strong>Nombre:</strong> ' . htmlspecialchars($factura['paciente_nombre']) . '<br>';
    
    if (!empty($factura['paciente_email'])) {
        $html .= '<strong>Email:</strong> ' . htmlspecialchars($factura['paciente_email']) . '<br>';
    }
    
    if (!empty($factura['paciente_telefono'])) {
        $html .= '<strong>Teléfono:</strong> ' . htmlspecialchars($factura['paciente_telefono']) . '<br>';
    }
    
    $html .= '
        </div>
    </div>

    <!-- DETALLE DE SERVICIOS -->
    <div class="section-title" style="margin-top: 25px;">📋 DETALLE DE SERVICIOS</div>
    <table class="services-table">
        <thead>
            <tr>
                <th style="width: 50%;">Descripción del Servicio</th>
                <th style="width: 12%; text-align: center;">Cantidad</th>
                <th style="width: 19%; text-align: right;">Precio Unit.</th>
                <th style="width: 19%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="service-name">💊 Consulta Médica Especializada</div>
                    <div class="service-detail">';
    
    if (!empty($factura['doctor_nombre'])) {
        $html .= '👨‍⚕️ Atendido por: Dr. ' . htmlspecialchars($factura['doctor_nombre']) . '<br>';
    }
    
    if (!empty($factura['cita_fecha'])) {
        $html .= '📅 Fecha de consulta: ' . date('d/m/Y', strtotime($factura['cita_fecha'])) . ' a las ' . date('h:i A', strtotime($factura['cita_fecha']));
    }
    
    $html .= '
                    </div>
                </td>
                <td style="text-align: center; font-weight: bold;">1</td>
                <td style="text-align: right;">S/ ' . number_format($subtotal, 2) . '</td>
                <td style="text-align: right; font-weight: bold; color: #667eea;">S/ ' . number_format($subtotal, 2) . '</td>
            </tr>
        </tbody>
    </table>

    <!-- TOTALES -->
    <div class="totales-section">
        <table class="totales-table">
            <tr class="totales-row totales-subtotal">
                <td style="text-align: right;"><strong>Subtotal:</strong></td>
                <td style="text-align: right; width: 35%;">S/ ' . number_format($subtotal, 2) . '</td>
            </tr>
            <tr class="totales-row totales-subtotal">
                <td style="text-align: right;"><strong>IGV (18%):</strong></td>
                <td style="text-align: right;">S/ ' . number_format($igv, 2) . '</td>
            </tr>
            <tr class="totales-final">
                <td style="text-align: right;"><strong>TOTAL A PAGAR:</strong></td>
                <td style="text-align: right;"><strong>S/ ' . number_format($total, 2) . '</strong></td>
            </tr>
        </table>
    </div>

    <!-- FOOTER -->
    <div class="footer-section">
        <div class="footer-thanks">✨ ¡Gracias por confiar en nosotros! ✨</div>
        <div class="footer-text">
            Este documento es una representación impresa de una factura electrónica válida.<br>
            Para cualquier consulta, contáctenos a través de nuestros canales oficiales.
        </div>
        <div class="footer-legal">
            🔒 Documento generado el ' . date('d/m/Y') . ' a las ' . date('H:i') . ' hrs.<br>
            Sistema de Gestión Clínica v1.0 | Todos los derechos reservados © ' . date('Y') . '
        </div>
    </div>
    ';

    return $html;
}



private function esAdmin(): bool
{
    return ($_SESSION['rol'] ?? '') === 'admin';
}

/**
 * Obtiene el paciente_id desde el usuario_id
 */
private function obtenerPacienteIdPorUsuario(int $usuarioId): ?int
{
    try {
        $pdo = BaseDatos::pdo();
        $sql = "SELECT id FROM pacientes WHERE usuario_id = :usuario_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':usuario_id' => $usuarioId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? (int)$result['id'] : null;
    } catch (Exception $e) {
        error_log("Error obteniendo paciente_id: " . $e->getMessage());
        return null;
    }
}
}