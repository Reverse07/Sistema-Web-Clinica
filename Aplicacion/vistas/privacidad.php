<?php
// Cargar configuración y autenticación
require_once __DIR__ . "/../../configuracion/app.php";
require_once __DIR__ . "/../../nucleo/Autenticacion.php";

// Iniciar sesión segura
Autenticacion::iniciarSesionSegura();

// Determinar a dónde volver según si el usuario está logueado
$volverA = 'loginVista'; // Por defecto al login

if (isset($_SESSION['usuario_id'])) {
    // Si está logueado, ir al dashboard según su rol
    $rol = $_SESSION['rol'] ?? '';
    if ($rol === 'admin') {
        $volverA = 'dashboardAdmin';
    } elseif ($rol === 'doctor') {
        $volverA = 'doctorDashboard';
    } elseif ($rol === 'paciente') {
        $volverA = 'pacienteDashboard';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidad - Sistema Clínico</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .privacidad-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .privacidad-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }

        .privacidad-header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            font-weight: 800;
        }

        .privacidad-header p {
            font-size: 1.1rem;
            opacity: 0.95;
        }

        .privacidad-content {
            padding: 3rem 2.5rem;
            line-height: 1.8;
            color: #334155;
        }

        .privacidad-seccion {
            margin-bottom: 2.5rem;
        }

        .privacidad-seccion h2 {
            color: #667eea;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .privacidad-seccion h3 {
            color: #334155;
            font-size: 1.2rem;
            margin: 1.5rem 0 0.75rem 0;
            font-weight: 600;
        }

        .privacidad-seccion p {
            margin-bottom: 1rem;
            text-align: justify;
        }

        .privacidad-seccion ul {
            margin-left: 2rem;
            margin-bottom: 1rem;
        }

        .privacidad-seccion li {
            margin-bottom: 0.5rem;
        }

        .highlight-box {
            background: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%);
            border-left: 4px solid #667eea;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1.5rem 0;
        }

        .highlight-box strong {
            color: #667eea;
            display: block;
            margin-bottom: 0.5rem;
        }

        .alert-box {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%);
            border-left: 4px solid #f59e0b;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1.5rem 0;
        }

        .alert-box strong {
            color: #f59e0b;
            display: block;
            margin-bottom: 0.5rem;
        }

        .success-box {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-left: 4px solid #28a745;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1.5rem 0;
        }

        .success-box strong {
            color: #28a745;
            display: block;
            margin-bottom: 0.5rem;
        }

        .fecha-actualizacion {
            text-align: center;
            padding: 1.5rem;
            background: #f8fafc;
            border-top: 2px solid #e2e8f0;
            color: #64748b;
            font-size: 0.95rem;
        }

        .btn-volver {
            display: inline-block;
            margin: 2rem auto;
            padding: 1rem 2.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-volver:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-container {
            text-align: center;
            padding: 2rem;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            background: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
        }

        .data-table th {
            background: #667eea;
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }

        .data-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .privacidad-header {
                padding: 2rem 1.5rem;
            }

            .privacidad-header h1 {
                font-size: 1.8rem;
            }

            .privacidad-content {
                padding: 2rem 1.5rem;
            }

            .data-table {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="privacidad-container">
        <div class="privacidad-header">
            <h1>🔐 Política de Privacidad</h1>
            <p>Sistema de Gestión Clínica</p>
        </div>

        <div class="privacidad-content">
            <!-- Introducción -->
            <div class="privacidad-seccion">
                <h2>📋 1. Introducción</h2>
                <p>
                    En el Sistema de Gestión Clínica, nos comprometemos a proteger la privacidad y seguridad 
                    de su información personal y médica. Esta Política de Privacidad describe cómo recopilamos, 
                    usamos, almacenamos y protegemos sus datos.
                </p>
                <div class="success-box">
                    <strong>✅ Nuestro Compromiso:</strong>
                    Tratamos sus datos con el más alto nivel de confidencialidad y cumplimos con todas las 
                    leyes de protección de datos aplicables, incluyendo normativas de salud y privacidad médica.
                </div>
            </div>

            <!-- Información que Recopilamos -->
            <div class="privacidad-seccion">
                <h2>📊 2. Información que Recopilamos</h2>
                
                <h3>2.1. Datos Personales</h3>
                <p>Recopilamos la siguiente información personal:</p>
                <ul>
                    <li><strong>Identificación:</strong> Nombre completo, DNI/Cédula, fecha de nacimiento</li>
                    <li><strong>Contacto:</strong> Email, teléfono, dirección</li>
                    <li><strong>Profesional:</strong> Número de colegiatura (para doctores), especialidad médica</li>
                    <li><strong>Cuenta:</strong> Usuario, contraseña encriptada, rol en el sistema</li>
                </ul>

                <h3>2.2. Información Médica</h3>
                <p>Para pacientes, también recopilamos:</p>
                <ul>
                    <li>Historial médico y diagnósticos</li>
                    <li>Tratamientos y medicamentos prescritos</li>
                    <li>Resultados de exámenes y análisis</li>
                    <li>Recetas médicas</li>
                    <li>Información de citas y consultas</li>
                    <li>Alergias y condiciones preexistentes</li>
                </ul>

                <h3>2.3. Datos de Uso del Sistema</h3>
                <ul>
                    <li>Registros de acceso (logs)</li>
                    <li>Dirección IP</li>
                    <li>Fecha y hora de acceso</li>
                    <li>Acciones realizadas en el sistema</li>
                </ul>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tipo de Dato</th>
                            <th>Propósito</th>
                            <th>Base Legal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Datos Personales</td>
                            <td>Identificación y contacto</td>
                            <td>Consentimiento</td>
                        </tr>
                        <tr>
                            <td>Datos Médicos</td>
                            <td>Atención sanitaria</td>
                            <td>Interés vital / Consentimiento</td>
                        </tr>
                        <tr>
                            <td>Datos de Uso</td>
                            <td>Seguridad y auditoría</td>
                            <td>Interés legítimo</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Cómo Usamos su Información -->
            <div class="privacidad-seccion">
                <h2>🎯 3. Cómo Usamos su Información</h2>
                
                <h3>3.1. Propósitos Principales</h3>
                <ul>
                    <li><strong>Atención Médica:</strong> Proporcionar servicios de salud, gestionar tratamientos y diagnósticos</li>
                    <li><strong>Gestión de Citas:</strong> Programar, confirmar y recordar citas médicas</li>
                    <li><strong>Comunicación:</strong> Enviar notificaciones importantes sobre su salud y citas</li>
                    <li><strong>Facturación:</strong> Generar y gestionar facturas por servicios médicos</li>
                    <li><strong>Mejora del Servicio:</strong> Analizar y mejorar la calidad de atención</li>
                </ul>

                <h3>3.2. Usos Secundarios</h3>
                <ul>
                    <li>Cumplimiento de obligaciones legales y regulatorias</li>
                    <li>Investigación médica anónima (con consentimiento)</li>
                    <li>Estadísticas agregadas y reportes (datos anonimizados)</li>
                    <li>Auditorías internas de calidad</li>
                </ul>

                <div class="alert-box">
                    <strong>⚠️ Importante:</strong>
                    Nunca vendemos, alquilamos o compartimos sus datos personales o médicos con terceros 
                    con fines comerciales o de marketing.
                </div>
            </div>

            <!-- Protección de Datos -->
            <div class="privacidad-seccion">
                <h2>🛡️ 4. Cómo Protegemos sus Datos</h2>
                
                <h3>4.1. Medidas de Seguridad Técnicas</h3>
                <ul>
                    <li><strong>Encriptación:</strong> Todas las contraseñas se encriptan con algoritmos seguros (bcrypt)</li>
                    <li><strong>Conexiones Seguras:</strong> Uso de HTTPS para todas las transmisiones de datos</li>
                    <li><strong>Control de Acceso:</strong> Sistema de roles y permisos estrictos</li>
                    <li><strong>Autenticación:</strong> Sesiones seguras con tokens CSRF</li>
                    <li><strong>Respaldos:</strong> Copias de seguridad automáticas y cifradas</li>
                </ul>

                <h3>4.2. Medidas Organizativas</h3>
                <ul>
                    <li>Acceso restringido solo a personal autorizado</li>
                    <li>Capacitación en seguridad y privacidad de datos</li>
                    <li>Auditorías de seguridad regulares</li>
                    <li>Políticas de confidencialidad para todo el personal</li>
                    <li>Registro de accesos y actividades (logs de auditoría)</li>
                </ul>

                <div class="highlight-box">
                    <strong>🔒 Protección de Datos Médicos:</strong>
                    Los datos médicos están sujetos a protección especial bajo el secreto profesional médico. 
                    Solo el personal médico directamente involucrado en su atención puede acceder a su historial.
                </div>
            </div>

            <!-- Compartir Información -->
            <div class="privacidad-seccion">
                <h2>🤝 5. Compartir Información</h2>
                
                <h3>5.1. Con Quién Compartimos</h3>
                <p>Podemos compartir su información únicamente en las siguientes circunstancias:</p>
                <ul>
                    <li><strong>Personal Médico:</strong> Doctores y profesionales de salud involucrados en su atención</li>
                    <li><strong>Personal Administrativo:</strong> Solo para gestión de citas y facturación</li>
                    <li><strong>Requisitos Legales:</strong> Cuando sea requerido por ley o autoridades competentes</li>
                    <li><strong>Emergencias Médicas:</strong> Para proteger su vida o salud en situaciones críticas</li>
                </ul>

                <h3>5.2. NO Compartimos Con</h3>
                <ul>
                    <li>Empresas de marketing o publicidad</li>
                    <li>Terceros con fines comerciales</li>
                    <li>Redes sociales</li>
                    <li>Ninguna entidad no autorizada explícitamente</li>
                </ul>
            </div>

            <!-- Derechos del Usuario -->
            <div class="privacidad-seccion">
                <h2>⚖️ 6. Sus Derechos</h2>
                <p>Como usuario, usted tiene los siguientes derechos sobre sus datos:</p>
                
                <h3>6.1. Derecho de Acceso</h3>
                <p>Puede solicitar una copia de todos sus datos personales y médicos almacenados en nuestro sistema.</p>

                <h3>6.2. Derecho de Rectificación</h3>
                <p>Puede solicitar la corrección de datos incorrectos o incompletos.</p>

                <h3>6.3. Derecho de Supresión</h3>
                <p>Puede solicitar la eliminación de sus datos, sujeto a requisitos legales de conservación médica.</p>

                <h3>6.4. Derecho de Portabilidad</h3>
                <p>Puede solicitar sus datos en un formato estructurado y de uso común.</p>

                <h3>6.5. Derecho de Oposición</h3>
                <p>Puede oponerse al procesamiento de sus datos para ciertos fines.</p>

                <div class="success-box">
                    <strong>✉️ Cómo Ejercer sus Derechos:</strong>
                    Para ejercer cualquiera de estos derechos, contáctenos a través de:<br>
                    <strong>Email:</strong> privacidad@sistemaclinico.com<br>
                    <strong>Teléfono:</strong> +51 XXX XXX XXX
                </div>
            </div>

            <!-- Retención de Datos -->
            <div class="privacidad-seccion">
                <h2>⏱️ 7. Retención de Datos</h2>
                
                <h3>7.1. Datos Médicos</h3>
                <p>
                    Los historiales médicos se conservan de acuerdo con las regulaciones de salud aplicables, 
                    típicamente por un período mínimo de 10 años después de la última consulta.
                </p>

                <h3>7.2. Datos Personales</h3>
                <p>
                    Los datos personales se conservan mientras su cuenta esté activa o según sea necesario 
                    para cumplir con obligaciones legales.
                </p>

                <h3>7.3. Logs de Auditoría</h3>
                <p>
                    Los registros de acceso y actividad se conservan por 12 meses con fines de seguridad.
                </p>
            </div>

            <!-- Cookies -->
            <div class="privacidad-seccion">
                <h2>🍪 8. Cookies y Tecnologías Similares</h2>
                <p>Utilizamos cookies de sesión esenciales para:</p>
                <ul>
                    <li>Mantener su sesión activa mientras navega</li>
                    <li>Recordar sus preferencias de usuario</li>
                    <li>Proteger contra ataques CSRF</li>
                </ul>
                <p>
                    <strong>No utilizamos cookies de terceros para publicidad o seguimiento.</strong>
                </p>
            </div>

            <!-- Menores de Edad -->
            <div class="privacidad-seccion">
                <h2>👶 9. Menores de Edad</h2>
                <p>
                    Los datos de menores de edad son manejados con especial cuidado. Los padres o tutores 
                    legales tienen derecho a acceder y gestionar la información médica de sus hijos menores.
                </p>
                <div class="alert-box">
                    <strong>⚠️ Importante:</strong>
                    Para usuarios menores de 18 años, se requiere el consentimiento de un padre o tutor legal.
                </div>
            </div>

            <!-- Cambios en la Política -->
            <div class="privacidad-seccion">
                <h2>📝 10. Cambios en esta Política</h2>
                <p>
                    Nos reservamos el derecho de actualizar esta Política de Privacidad. Le notificaremos 
                    sobre cambios significativos mediante:
                </p>
                <ul>
                    <li>Notificación en el sistema</li>
                    <li>Email a su dirección registrada</li>
                    <li>Aviso destacado en el sitio web</li>
                </ul>
            </div>

            <!-- Contacto -->
            <div class="privacidad-seccion">
                <h2>📧 11. Contacto</h2>
                <p>
                    Para cualquier pregunta sobre esta Política de Privacidad o el manejo de sus datos:
                </p>
                <div class="highlight-box">
                    <strong>📧 Email de Privacidad:</strong> privacidad@sistemaclinico.com<br>
                    <strong>📞 Teléfono:</strong> +51 XXX XXX XXX<br>
                    <strong>📍 Dirección:</strong> San Isidro, Lima, Perú<br>
                    <strong>👤 Responsable de Datos:</strong> Oficial de Protección de Datos
                </div>
            </div>

            <!-- Consentimiento -->
            <div class="privacidad-seccion">
                <div class="success-box">
                    <strong>✅ Consentimiento:</strong>
                    Al utilizar nuestro sistema, usted reconoce que ha leído y comprendido esta Política 
                    de Privacidad y consiente el procesamiento de sus datos según lo descrito aquí.
                </div>
            </div>
        </div>

        <div class="fecha-actualizacion">
            <strong>Última actualización:</strong> <?= date('d \d\e F \d\e Y') ?><br>
            <strong>Versión:</strong> 1.0
        </div>

        <div class="btn-container">
            <a href="<?= BASE_URL ?>/index.php?accion=<?= $volverA ?>" class="btn-volver">
                ← Volver al Inicio
            </a>
        </div>
    </div>
</body>
</html>