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
    <title>Términos y Condiciones - Sistema Clínico</title>
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

        .terminos-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .terminos-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }

        .terminos-header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            font-weight: 800;
        }

        .terminos-header p {
            font-size: 1.1rem;
            opacity: 0.95;
        }

        .terminos-content {
            padding: 3rem 2.5rem;
            line-height: 1.8;
            color: #334155;
        }

        .terminos-seccion {
            margin-bottom: 2.5rem;
        }

        .terminos-seccion h2 {
            color: #667eea;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .terminos-seccion h3 {
            color: #334155;
            font-size: 1.2rem;
            margin: 1.5rem 0 0.75rem 0;
            font-weight: 600;
        }

        .terminos-seccion p {
            margin-bottom: 1rem;
            text-align: justify;
        }

        .terminos-seccion ul {
            margin-left: 2rem;
            margin-bottom: 1rem;
        }

        .terminos-seccion li {
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

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .terminos-header {
                padding: 2rem 1.5rem;
            }

            .terminos-header h1 {
                font-size: 1.8rem;
            }

            .terminos-content {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="terminos-container">
        <div class="terminos-header">
            <h1>📜 Términos y Condiciones</h1>
            <p>Sistema de Gestión Clínica</p>
        </div>

        <div class="terminos-content">
            <!-- Introducción -->
            <div class="terminos-seccion">
                <h2>🔍 1. Introducción</h2>
                <p>
                    Bienvenido al Sistema de Gestión Clínica. Estos Términos y Condiciones regulan el acceso y uso 
                    de nuestra plataforma. Al utilizar nuestros servicios, usted acepta estar sujeto a estos términos 
                    en su totalidad.
                </p>
                <div class="highlight-box">
                    <strong>⚠️ Importante:</strong>
                    Si no está de acuerdo con alguno de estos términos, por favor no utilice nuestra plataforma.
                </div>
            </div>

            <!-- Definiciones -->
            <div class="terminos-seccion">
                <h2>📖 2. Definiciones</h2>
                <ul>
                    <li><strong>Plataforma:</strong> Sistema de Gestión Clínica y todos sus componentes.</li>
                    <li><strong>Usuario:</strong> Cualquier persona que acceda y utilice la plataforma.</li>
                    <li><strong>Datos Médicos:</strong> Información de salud almacenada en el sistema.</li>
                    <li><strong>Administrador:</strong> Personal autorizado para gestionar el sistema.</li>
                    <li><strong>Doctor:</strong> Profesional médico registrado en la plataforma.</li>
                    <li><strong>Paciente:</strong> Usuario que recibe servicios médicos.</li>
                </ul>
            </div>

            <!-- Uso del Sistema -->
            <div class="terminos-seccion">
                <h2>💻 3. Uso del Sistema</h2>
                
                <h3>3.1. Registro y Cuenta</h3>
                <p>
                    Para acceder a las funcionalidades del sistema, debe crear una cuenta proporcionando información 
                    veraz, precisa y completa. Es su responsabilidad mantener la confidencialidad de sus credenciales 
                    de acceso.
                </p>

                <h3>3.2. Conducta del Usuario</h3>
                <p>Al utilizar nuestra plataforma, usted se compromete a:</p>
                <ul>
                    <li>No usar el sistema para fines ilícitos o no autorizados</li>
                    <li>No intentar acceder a áreas restringidas del sistema</li>
                    <li>No compartir información confidencial de otros usuarios</li>
                    <li>Mantener la seguridad de su cuenta y contraseña</li>
                    <li>Notificar inmediatamente cualquier uso no autorizado de su cuenta</li>
                </ul>

                <h3>3.3. Restricciones</h3>
                <p>Está prohibido:</p>
                <ul>
                    <li>Modificar, copiar o distribuir el software del sistema</li>
                    <li>Realizar ingeniería inversa o descompilar el código</li>
                    <li>Usar bots, scrapers o herramientas automatizadas</li>
                    <li>Intentar vulnerar la seguridad del sistema</li>
                    <li>Interferir con el funcionamiento normal de la plataforma</li>
                </ul>
            </div>

            <!-- Privacidad y Datos -->
            <div class="terminos-seccion">
                <h2>🔐 4. Privacidad y Protección de Datos</h2>
                
                <h3>4.1. Recopilación de Datos</h3>
                <p>
                    Recopilamos información personal y médica necesaria para proporcionar nuestros servicios. 
                    Esto incluye datos de identificación, contacto e información de salud.
                </p>

                <div class="highlight-box">
                    <strong>🛡️ Compromiso de Privacidad:</strong>
                    Nos comprometemos a proteger su información personal de acuerdo con las leyes de protección 
                    de datos aplicables y mejores prácticas de seguridad de la información.
                </div>

                <h3>4.2. Uso de la Información</h3>
                <p>Sus datos se utilizan para:</p>
                <ul>
                    <li>Proporcionar y mejorar nuestros servicios médicos</li>
                    <li>Gestionar citas y tratamientos</li>
                    <li>Comunicarnos con usted sobre su atención médica</li>
                    <li>Cumplir con obligaciones legales y regulatorias</li>
                    <li>Generar estadísticas anónimas para mejorar el servicio</li>
                </ul>

                <h3>4.3. Confidencialidad Médica</h3>
                <p>
                    Toda la información médica está protegida bajo el secreto profesional y solo será accesible 
                    por personal autorizado directamente involucrado en su atención.
                </p>
            </div>

            <!-- Responsabilidades -->
            <div class="terminos-seccion">
                <h2>⚖️ 5. Responsabilidades y Limitaciones</h2>
                
                <h3>5.1. Disponibilidad del Servicio</h3>
                <p>
                    Nos esforzamos por mantener la plataforma disponible 24/7, pero no garantizamos acceso 
                    ininterrumpido. Pueden existir períodos de mantenimiento o interrupciones técnicas.
                </p>

                <h3>5.2. Limitación de Responsabilidad</h3>
                <p>
                    El sistema es una herramienta de gestión y no sustituye el criterio médico profesional. 
                    No nos hacemos responsables por:
                </p>
                <ul>
                    <li>Decisiones médicas tomadas basándose exclusivamente en el sistema</li>
                    <li>Pérdida de datos debido a problemas técnicos fuera de nuestro control</li>
                    <li>Daños indirectos o consecuenciales del uso del sistema</li>
                    <li>Accesos no autorizados resultantes de negligencia del usuario</li>
                </ul>

                <h3>5.3. Respaldo de Información</h3>
                <p>
                    Realizamos respaldos regulares de la información, pero recomendamos que los usuarios 
                    mantengan copias de seguridad de sus datos importantes.
                </p>
            </div>

            <!-- Propiedad Intelectual -->
            <div class="terminos-seccion">
                <h2>©️ 6. Propiedad Intelectual</h2>
                <p>
                    Todo el contenido, diseño, código, logotipos y materiales de la plataforma son propiedad 
                    exclusiva del Sistema de Gestión Clínica y están protegidos por leyes de propiedad intelectual.
                </p>
                <p>
                    No se permite la reproducción, distribución o uso comercial sin autorización expresa por escrito.
                </p>
            </div>

            <!-- Modificaciones -->
            <div class="terminos-seccion">
                <h2>✏️ 7. Modificaciones a los Términos</h2>
                <p>
                    Nos reservamos el derecho de modificar estos términos en cualquier momento. Las modificaciones 
                    entrarán en vigor inmediatamente después de su publicación en la plataforma.
                </p>
                <p>
                    Es responsabilidad del usuario revisar periódicamente estos términos. El uso continuado de la 
                    plataforma después de modificaciones constituye aceptación de los nuevos términos.
                </p>
            </div>

            <!-- Terminación -->
            <div class="terminos-seccion">
                <h2>🚫 8. Terminación del Servicio</h2>
                <p>
                    Nos reservamos el derecho de suspender o cancelar su acceso al sistema en caso de:
                </p>
                <ul>
                    <li>Violación de estos términos y condiciones</li>
                    <li>Uso fraudulento o abusivo de la plataforma</li>
                    <li>Actividades que pongan en riesgo la seguridad del sistema</li>
                    <li>Solicitud del usuario de cancelación de cuenta</li>
                </ul>
            </div>

            <!-- Ley Aplicable -->
            <div class="terminos-seccion">
                <h2>⚖️ 9. Ley Aplicable y Jurisdicción</h2>
                <p>
                    Estos términos se rigen por las leyes del país donde opera el sistema. Cualquier disputa 
                    será resuelta en los tribunales competentes de dicha jurisdicción.
                </p>
            </div>

            <!-- Contacto -->
            <div class="terminos-seccion">
                <h2>📧 10. Contacto</h2>
                <p>
                    Para cualquier pregunta o inquietud sobre estos términos, puede contactarnos a través de:
                </p>
                <div class="highlight-box">
                    <strong>📧 Email:</strong> soporte@sistemaclinico.com<br>
                    <strong>📞 Teléfono:</strong> +51 XXX XXX XXX<br>
                    <strong>📍 Dirección:</strong> San Isidro, Lima, Perú
                </div>
            </div>

            <!-- Aceptación -->
            <div class="terminos-seccion">
                <div class="highlight-box">
                    <strong>✅ Aceptación de Términos:</strong>
                    Al hacer clic en "Acepto" durante el registro o al continuar usando la plataforma, 
                    usted confirma que ha leído, comprendido y aceptado estos Términos y Condiciones en su totalidad.
                </div>
            </div>
        </div>

        <div class="fecha-actualizacion">
            <strong>Última actualización:</strong> <?= date('d \d\e F \d\e Y') ?>
        </div>

        <div class="btn-container">
            <a href="<?= BASE_URL ?>/index.php?accion=<?= $volverA ?>" class="btn-volver">
    ← Volver al Inicio
</a>
        </div>
    </div>
</body>
</html>