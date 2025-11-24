<?php
require_once __DIR__ . "/../../nucleo/Autenticacion.php";
require_once __DIR__ . "/../modelos/Usuario.php";
require_once __DIR__ . "/../modelos/Doctor.php";
require_once __DIR__ . "/../modelos/Paciente.php";
require_once __DIR__ . "/../modelos/Cita.php"; // ✅ Importar modelo Cita

// 📧 Importar PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * AdminControlador
 * 
 * Responsabilidad: Solo gestión administrativa general
 * - Dashboard con KPIs
 * - Reportes del sistema
 * - Configuración general
 * - Contacto y soporte
 */
class AdminControlador
{
    /**
     * Valida que el usuario sea administrador
     */
    private function validarAutenticacion()
    {
        Autenticacion::requiereRoles(['admin']);
    }

    /**
     * Dashboard principal del administrador
     * Muestra KPIs generales del sistema
     */
    public function dashboard()
    {
        $this->validarAutenticacion();

        // 📊 KPIs de usuarios
        $totalUsuarios  = Usuario::contarTodos();
        $totalDoctores  = Usuario::contarPorRol(2);
        $totalPacientes = Usuario::contarPorRol(3);

        // 📅 KPIs de citas
        $totalCitas = Cita::contarCitas();
        $citasHoy = $this->contarCitasHoy();
        $citasPendientes = $this->contarCitasPendientes();
        $citasRecientes = $this->obtenerCitasRecientes();

        $vistaInterna = __DIR__ . "/../vistas/admin/inicio.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * 📅 Cuenta las citas de hoy
     */
    private function contarCitasHoy()
    {
        $pdo = \BaseDatos::pdo();
        $sql = "SELECT COUNT(*) FROM citas WHERE DATE(fecha) = CURRENT_DATE";
        $stmt = $pdo->query($sql);
        return $stmt->fetchColumn();
    }

    /**
     * ⏳ Cuenta las citas con estado pendiente
     */
    private function contarCitasPendientes()
    {
        $pdo = \BaseDatos::pdo();
        $sql = "SELECT COUNT(*) FROM citas WHERE estado = 'Pendiente'";
        $stmt = $pdo->query($sql);
        return $stmt->fetchColumn();
    }

    /**
     * 📋 Obtiene las últimas 10 citas registradas con datos de paciente y doctor
     */
    private function obtenerCitasRecientes()
    {
        $pdo = \BaseDatos::pdo();
        $sql = "
            SELECT 
                c.id,
                c.fecha,
                c.estado,
                u_paciente.nombre as paciente,
                u_doctor.nombre as doctor
            FROM citas c
            LEFT JOIN pacientes p ON c.paciente_id = p.id
            LEFT JOIN usuarios u_paciente ON p.usuario_id = u_paciente.id
            LEFT JOIN doctores d ON c.doctor_id = d.id
            LEFT JOIN usuarios u_doctor ON d.usuario_id = u_doctor.id
            ORDER BY c.fecha DESC
            LIMIT 10
        ";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Muestra reportes y estadísticas del sistema
     */
    public function verReportes()
    {
        $this->validarAutenticacion();
        
        $vistaInterna = __DIR__ . "/../vistas/admin/reportes.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * Configuración general del sistema
     */
    public function configuracion()
    {
        $this->validarAutenticacion();
        
        $vistaInterna = __DIR__ . "/../vistas/admin/configuracion.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    // =====================================================
    // 📧 MÉTODOS DE CONTACTO Y SOPORTE
    // =====================================================

    /**
     * 📄 Muestra el formulario de contacto y soporte
     */
    public function contacto()
    {
        $this->validarAutenticacion();
        
        $vistaInterna = __DIR__ . "/../vistas/admin/contacto.php";
        require __DIR__ . "/../../includes/layout-admin.php";
    }

    /**
     * 📤 Procesa y envía el formulario de contacto
     * Envía email al administrador Y copia de confirmación al usuario
     */
    public function enviarContacto()
    {
        $this->validarAutenticacion();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/index.php?accion=contacto");
            exit;
        }
        
        try {
            // 📥 Capturar datos del formulario
            $asunto = trim($_POST['asunto'] ?? '');
            $prioridad = trim($_POST['prioridad'] ?? 'media');
            $mensaje = trim($_POST['mensaje'] ?? '');
            $emailUsuario = trim($_POST['email_contacto'] ?? $_SESSION['email'] ?? '');
            
            // ✅ Validaciones
            if (empty($asunto) || empty($mensaje)) {
                throw new Exception("El asunto y el mensaje son obligatorios");
            }
            
            if (strlen($mensaje) < 10) {
                throw new Exception("El mensaje debe tener al menos 10 caracteres");
            }
            
            if (!filter_var($emailUsuario, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("El email no es válido");
            }
            
            // 📧 Datos del usuario que envía
            $nombreUsuario = $_SESSION['nombre'] ?? 'Usuario';
            $apellidoUsuario = $_SESSION['apellido'] ?? '';
            $nombreCompleto = trim($nombreUsuario . ' ' . $apellidoUsuario);
            $rolUsuario = $_SESSION['rol'] ?? 'admin';
            
            // 🎯 Email del administrador (donde llegarán los mensajes)
            $emailAdmin = defined('CONTACTO_EMAIL') ? CONTACTO_EMAIL : 'soporte@sistemaclinico.com';
            
            // 🎨 Mapeo de asuntos con emojis
            $asuntosMap = [
                'soporte_tecnico' => '🔧 Soporte Técnico',
                'error_sistema' => '🐛 Reportar Error',
                'sugerencia' => '💡 Sugerencia',
                'consulta_general' => '❓ Consulta General',
                'facturacion' => '💳 Facturación',
                'otro' => '📋 Otro'
            ];
            $asuntoTexto = $asuntosMap[$asunto] ?? $asunto;
            
            // 🎨 Mapeo de prioridades
            $prioridadesMap = [
                'baja' => '🟢 Baja',
                'media' => '🟡 Media',
                'alta' => '🔴 Alta',
                'critica' => '🚨 Crítica'
            ];
            $prioridadTexto = $prioridadesMap[$prioridad] ?? $prioridad;
            
            // 📅 Fecha y hora actual
            $fechaHora = date('d/m/Y H:i:s');
            
            // =====================================================
            // 📧 1. ENVIAR EMAIL AL ADMINISTRADOR
            // =====================================================
            $mailAdmin = new PHPMailer(true);
            
            // Configuración del servidor SMTP
            $mailAdmin->isSMTP();
            $mailAdmin->Host       = MAIL_HOST;
            $mailAdmin->SMTPAuth   = true;
            $mailAdmin->Username   = MAIL_USERNAME;
            $mailAdmin->Password   = MAIL_PASSWORD;
            $mailAdmin->SMTPSecure = MAIL_ENCRYPTION;
            $mailAdmin->Port       = MAIL_PORT;
            $mailAdmin->CharSet    = 'UTF-8';
            
            // 🐛 Debug (solo si está activado en config)
            if (defined('MAIL_DEBUG') && MAIL_DEBUG) {
                $mailAdmin->SMTPDebug = 2;
                $mailAdmin->Debugoutput = 'html';
            }
            
            // Remitente y destinatario
            $mailAdmin->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $mailAdmin->addAddress($emailAdmin, 'Administrador Sistema');
            $mailAdmin->addReplyTo($emailUsuario, $nombreCompleto);
            
            // Contenido del email
            $mailAdmin->isHTML(true);
            $mailAdmin->Subject = "Nuevo mensaje de contacto: $asuntoTexto [$prioridadTexto]";
            $mailAdmin->Body    = $this->plantillaEmailAdmin(
                $nombreCompleto, 
                $emailUsuario, 
                $rolUsuario, 
                $asuntoTexto, 
                $prioridadTexto, 
                $mensaje, 
                $fechaHora, 
                $prioridad
            );
            
            // Enviar email al admin
            if (!$mailAdmin->send()) {
                throw new Exception("Error al enviar email al administrador: " . $mailAdmin->ErrorInfo);
            }
            
            // =====================================================
            // 📧 2. ENVIAR EMAIL DE CONFIRMACIÓN AL USUARIO
            // =====================================================
            $mailUsuario = new PHPMailer(true);
            
            // Configuración del servidor SMTP
            $mailUsuario->isSMTP();
            $mailUsuario->Host       = MAIL_HOST;
            $mailUsuario->SMTPAuth   = true;
            $mailUsuario->Username   = MAIL_USERNAME;
            $mailUsuario->Password   = MAIL_PASSWORD;
            $mailUsuario->SMTPSecure = MAIL_ENCRYPTION;
            $mailUsuario->Port       = MAIL_PORT;
            $mailUsuario->CharSet    = 'UTF-8';
            
            // 🐛 Debug (solo si está activado en config)
            if (defined('MAIL_DEBUG') && MAIL_DEBUG) {
                $mailUsuario->SMTPDebug = 2;
                $mailUsuario->Debugoutput = 'html';
            }
            
            // Remitente y destinatario
            $mailUsuario->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $mailUsuario->addAddress($emailUsuario, $nombreCompleto);
            $mailUsuario->addReplyTo($emailAdmin, 'Soporte Sistema Clínico');
            
            // Contenido del email
            $mailUsuario->isHTML(true);
            $mailUsuario->Subject = "✅ Confirmación: Tu mensaje ha sido recibido - $asuntoTexto";
            $mailUsuario->Body    = $this->plantillaEmailUsuario(
                $nombreCompleto, 
                $asuntoTexto, 
                $prioridadTexto, 
                $mensaje, 
                $fechaHora, 
                $emailAdmin
            );
            
            // Enviar email de confirmación al usuario
            if (!$mailUsuario->send()) {
                throw new Exception("Error al enviar confirmación al usuario: " . $mailUsuario->ErrorInfo);
            }
            
            // ✅ Ambos emails enviados correctamente
            $_SESSION['mensaje'] = [
                'tipo' => 'exito',
                'texto' => '✅ Mensaje enviado correctamente. Recibirás una confirmación en tu correo.'
            ];
            
        } catch (Exception $e) {
            // ❌ Error al enviar
            $_SESSION['mensaje'] = [
                'tipo' => 'error',
                'texto' => '❌ Error al enviar el mensaje: ' . $e->getMessage()
            ];
        }
        
        header("Location: " . BASE_URL . "/index.php?accion=contacto");
        exit;
    }

    // =====================================================
    // 📄 PLANTILLAS HTML PARA EMAILS
    // =====================================================

    /**
     * 📧 Plantilla HTML para email al administrador
     */
    private function plantillaEmailAdmin($nombre, $email, $rol, $asunto, $prioridad, $mensaje, $fecha, $prioridadClass)
    {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7fa; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; color: white; }
                .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
                .content { padding: 30px; }
                .info-box { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea; }
                .info-item { margin: 15px 0; }
                .info-label { font-weight: 600; color: #5a6c7d; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; margin-bottom: 5px; }
                .info-value { color: #2c3e50; font-size: 16px; margin-top: 5px; }
                .mensaje-box { background: #fff8e1; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107; }
                .prioridad { display: inline-block; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 13px; }
                .prioridad-critica { background: #ffebee; color: #c62828; }
                .prioridad-alta { background: #ffccbc; color: #d84315; }
                .prioridad-media { background: #fff9c4; color: #f57f17; }
                .prioridad-baja { background: #c8e6c9; color: #2e7d32; }
                .footer { background: #2c3e50; color: white; padding: 20px; text-align: center; font-size: 13px; }
                .btn { display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 6px; margin: 15px 0; font-weight: 600; }
                .btn:hover { background: #5568d3; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📧 Nuevo Mensaje de Contacto</h1>
                </div>
                <div class='content'>
                    <p style='color: #5a6c7d; margin-bottom: 20px;'>Has recibido un nuevo mensaje de contacto desde el sistema.</p>
                    
                    <div class='info-box'>
                        <div class='info-item'>
                            <div class='info-label'>👤 Remitente</div>
                            <div class='info-value'><strong>$nombre</strong></div>
                        </div>
                        <div class='info-item'>
                            <div class='info-label'>📧 Email de Contacto</div>
                            <div class='info-value'><a href='mailto:$email' style='color: #667eea;'>$email</a></div>
                        </div>
                        <div class='info-item'>
                            <div class='info-label'>🎭 Rol en el Sistema</div>
                            <div class='info-value'>" . strtoupper($rol) . "</div>
                        </div>
                        <div class='info-item'>
                            <div class='info-label'>📋 Tipo de Asunto</div>
                            <div class='info-value'>$asunto</div>
                        </div>
                        <div class='info-item'>
                            <div class='info-label'>⚡ Nivel de Prioridad</div>
                            <div class='info-value'>
                                <span class='prioridad prioridad-$prioridadClass'>$prioridad</span>
                            </div>
                        </div>
                        <div class='info-item'>
                            <div class='info-label'>📅 Fecha y Hora</div>
                            <div class='info-value'>$fecha</div>
                        </div>
                    </div>
                    
                    <div class='mensaje-box'>
                        <div class='info-label' style='color: #f57f17; margin-bottom: 10px;'>💬 Mensaje Completo</div>
                        <div style='white-space: pre-wrap; line-height: 1.8; color: #2c3e50; font-size: 15px;'>$mensaje</div>
                    </div>
                    
                    <div style='text-align: center; margin-top: 30px;'>
                        <a href='mailto:$email' class='btn'>📨 Responder a $nombre</a>
                    </div>
                </div>
                <div class='footer'>
                    <p style='margin: 0;'><strong>Sistema de Gestión Clínica</strong></p>
                    <p style='margin: 5px 0 0 0; opacity: 0.8;'>Notificación automática - No responder a este correo</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * ✅ Plantilla HTML para email de confirmación al usuario
     */
    private function plantillaEmailUsuario($nombre, $asunto, $prioridad, $mensaje, $fecha, $emailAdmin)
    {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7fa; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 30px; text-align: center; color: white; }
                .header h1 { margin: 10px 0 0 0; font-size: 24px; font-weight: 600; }
                .check-icon { font-size: 50px; margin: 0; }
                .content { padding: 30px; line-height: 1.6; }
                .success-box { background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #28a745; text-align: center; }
                .info-box { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .info-item { margin: 15px 0; }
                .info-label { font-weight: 600; color: #5a6c7d; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; margin-bottom: 5px; }
                .info-value { color: #2c3e50; font-size: 15px; margin-top: 5px; }
                .mensaje-box { background: #e8f4f8; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #17a2b8; }
                .footer { background: #2c3e50; color: white; padding: 20px; text-align: center; font-size: 13px; }
                .horarios { background: #fff3cd; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 3px solid #ffc107; }
                .tiempos { background: #e7f3ff; padding: 15px; border-radius: 6px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='check-icon'>✅</div>
                    <h1>Mensaje Recibido</h1>
                </div>
                <div class='content'>
                    <div class='success-box'>
                        <h2 style='color: #155724; margin: 0; font-size: 20px;'>¡Gracias por contactarnos!</h2>
                        <p style='color: #155724; margin: 10px 0 0 0; font-size: 14px;'>
                            Hemos recibido tu mensaje y lo atenderemos a la brevedad.
                        </p>
                    </div>
                    
                    <p style='color: #2c3e50; font-size: 15px;'>Hola <strong>$nombre</strong>,</p>
                    <p style='color: #5a6c7d;'>Tu mensaje ha sido registrado exitosamente en nuestro sistema de soporte. A continuación, encontrarás un resumen de tu solicitud:</p>
                    
                    <div class='info-box'>
                        <div class='info-item'>
                            <div class='info-label'>📋 Asunto</div>
                            <div class='info-value'>$asunto</div>
                        </div>
                        <div class='info-item'>
                            <div class='info-label'>⚡ Prioridad</div>
                            <div class='info-value'>$prioridad</div>
                        </div>
                        <div class='info-item'>
                            <div class='info-label'>📅 Fecha de Registro</div>
                            <div class='info-value'>$fecha</div>
                        </div>
                    </div>
                    
                    <div class='mensaje-box'>
                        <div class='info-label' style='color: #17a2b8; margin-bottom: 10px;'>💬 Tu Mensaje</div>
                        <div style='white-space: pre-wrap; line-height: 1.7; color: #2c3e50;'>$mensaje</div>
                    </div>
                    
                    <div class='tiempos'>
                        <p style='margin: 0; font-weight: 600; color: #0056b3; font-size: 14px;'>💡 Tiempo Estimado de Respuesta:</p>
                        <ul style='margin: 10px 0 0 20px; padding: 0; color: #5a6c7d; font-size: 13px;'>
                            <li>Prioridad Crítica/Alta: <strong>2-4 horas</strong></li>
                            <li>Prioridad Media: <strong>24 horas</strong></li>
                            <li>Prioridad Baja: <strong>48 horas</strong></li>
                        </ul>
                    </div>
                    
                    <div class='horarios'>
                        <p style='margin: 0; font-weight: 600; color: #856404; font-size: 14px;'>🕐 Horarios de Atención:</p>
                        <p style='margin: 10px 0 0 0; color: #856404; font-size: 13px; line-height: 1.8;'>
                            <strong>Lunes - Viernes:</strong> 8:00 AM - 6:00 PM<br>
                            <strong>Sábados:</strong> 9:00 AM - 1:00 PM<br>
                            <strong>Domingos:</strong> Cerrado
                        </p>
                    </div>
                    
                    <p style='color: #5a6c7d; font-size: 14px; margin-top: 25px;'>
                        Si necesitas más ayuda o tienes preguntas adicionales, no dudes en contactarnos:
                    </p>
                    <p style='color: #2c3e50; font-weight: 600; font-size: 15px; margin: 10px 0;'>
                        📧 <a href='mailto:$emailAdmin' style='color: #667eea; text-decoration: none;'>$emailAdmin</a><br>
                        📞 +51 XXX XXX XXX
                    </p>
                </div>
                <div class='footer'>
                    <p style='margin: 0; font-weight: 600;'>Sistema de Gestión Clínica</p>
                    <p style='margin: 5px 0; opacity: 0.9;'>San Isidro, Lima, Perú</p>
                    <p style='margin: 5px 0; opacity: 0.7; font-size: 12px;'>© " . date('Y') . " Todos los derechos reservados</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}