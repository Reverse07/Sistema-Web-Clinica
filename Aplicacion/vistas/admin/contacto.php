<?php
// =====================
// 📧 Vista: Contacto y Soporte (Admin)
// =====================
// ❌ ELIMINAR ESTAS LÍNEAS - La autenticación se hace en el controlador
// require_once __DIR__ . "/../../nucleo/Autenticacion.php";
// Autenticacion::requiereRoles(['admin']);
?>

<style>
.contacto-admin {
    padding: 25px;
    max-width: 1200px;
    margin: 0 auto;
}

.contacto-header {
    margin-bottom: 30px;
}

.contacto-header h1 {
    font-size: 32px;
    color: #2c3e50;
    margin: 0 0 10px 0;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

.contacto-header p {
    color: #7f8c8d;
    font-size: 16px;
}

/* Grid de 2 columnas */
.contacto-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 30px;
}

/* Tarjetas */
.contacto-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.contacto-card h2 {
    font-size: 20px;
    color: #2c3e50;
    margin: 0 0 20px 0;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Formulario */
.form-contacto {
    display: grid;
    gap: 20px;
}

.form-grupo {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-grupo label {
    font-size: 13px;
    font-weight: 600;
    color: #5a6c7d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-grupo input,
.form-grupo select,
.form-grupo textarea {
    padding: 12px;
    border: 2px solid #e1e8ed;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    font-family: inherit;
}

.form-grupo input:focus,
.form-grupo select:focus,
.form-grupo textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-grupo textarea {
    resize: vertical;
    min-height: 120px;
}

.btn-enviar {
    padding: 14px 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
}

.btn-enviar:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-enviar:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* Información de contacto */
.info-contacto {
    display: grid;
    gap: 20px;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.info-item:hover {
    background: #f0f4ff;
    transform: translateX(5px);
}

.info-icono {
    width: 45px;
    height: 45px;
    min-width: 45px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
}

.info-contenido {
    flex: 1;
}

.info-contenido h3 {
    font-size: 14px;
    color: #7f8c8d;
    margin: 0 0 5px 0;
    text-transform: uppercase;
    font-weight: 600;
}

.info-contenido p {
    font-size: 16px;
    color: #2c3e50;
    margin: 0;
    font-weight: 600;
}

.info-contenido a {
    color: #667eea;
    text-decoration: none;
    transition: color 0.3s ease;
}

.info-contenido a:hover {
    color: #764ba2;
    text-decoration: underline;
}

/* FAQ Section */
.faq-section {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.faq-section h2 {
    font-size: 22px;
    color: #2c3e50;
    margin: 0 0 25px 0;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.faq-item {
    margin-bottom: 20px;
    border-left: 4px solid #667eea;
    padding-left: 20px;
}

.faq-pregunta {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 8px 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
}

.faq-respuesta {
    font-size: 14px;
    color: #5a6c7d;
    line-height: 1.6;
    margin: 0;
}

/* Horarios */
.horarios-card {
    background: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%);
    border-radius: 12px;
    padding: 20px;
    margin-top: 20px;
}

.horarios-card h3 {
    font-size: 16px;
    color: #667eea;
    margin: 0 0 15px 0;
    font-weight: 700;
}

.horario-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid rgba(102, 126, 234, 0.2);
}

.horario-item:last-child {
    border-bottom: none;
}

.horario-dia {
    font-weight: 600;
    color: #2c3e50;
}

.horario-horas {
    color: #5a6c7d;
}

/* Alertas */
.alerta {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alerta-exito {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
}

.alerta-error {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
}

/* Responsive */
@media (max-width: 768px) {
    .contacto-admin {
        padding: 15px;
    }
    
    .contacto-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="contacto-admin">
    <!-- Header -->
    <div class="contacto-header">
        <h1>📧 Contacto y Soporte</h1>
        <p>¿Necesitas ayuda? Estamos aquí para asistirte</p>
    </div>

    <!-- Alertas -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alerta alerta-<?= $_SESSION['mensaje']['tipo'] ?>">
            <?= $_SESSION['mensaje']['tipo'] === 'exito' ? '✅' : '❌' ?>
            <?= htmlspecialchars($_SESSION['mensaje']['texto']) ?>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <!-- Grid Principal -->
    <div class="contacto-grid">
        <!-- Formulario de Contacto -->
        <div class="contacto-card">
            <h2>📝 Enviar Mensaje</h2>
            <form method="POST" action="<?= BASE_URL ?>/index.php?accion=enviarContacto" class="form-contacto">
                <div class="form-grupo">
                    <label for="asunto">Asunto *</label>
                    <select id="asunto" name="asunto" required>
                        <option value="">Seleccione un asunto</option>
                        <option value="soporte_tecnico">🔧 Soporte Técnico</option>
                        <option value="error_sistema">🐛 Reportar Error</option>
                        <option value="sugerencia">💡 Sugerencia</option>
                        <option value="consulta_general">❓ Consulta General</option>
                        <option value="facturacion">💳 Facturación</option>
                        <option value="otro">📋 Otro</option>
                    </select>
                </div>

                <div class="form-grupo">
                    <label for="prioridad">Prioridad *</label>
                    <select id="prioridad" name="prioridad" required>
                        <option value="baja">🟢 Baja</option>
                        <option value="media" selected>🟡 Media</option>
                        <option value="alta">🔴 Alta</option>
                        <option value="critica">🚨 Crítica</option>
                    </select>
                </div>

                <div class="form-grupo">
                    <label for="mensaje">Mensaje *</label>
                    <textarea id="mensaje" name="mensaje" required 
                              placeholder="Describe tu consulta o problema en detalle..."></textarea>
                </div>

                <div class="form-grupo">
                    <label for="email_contacto">Email de Respuesta</label>
                    <input type="email" id="email_contacto" name="email_contacto" 
                           value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>"
                           placeholder="tu@email.com">
                </div>

                <button type="submit" class="btn-enviar">
                    📤 Enviar Mensaje
                </button>
            </form>
        </div>

        <!-- Información de Contacto -->
        <div class="contacto-card">
            <h2>📞 Información de Contacto</h2>
            <div class="info-contacto">
                <div class="info-item">
                    <div class="info-icono">📧</div>
                    <div class="info-contenido">
                        <h3>Email</h3>
                        <p><a href="mailto:soporte@sistemaclinico.com">soporte@sistemaclinico.com</a></p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icono">📞</div>
                    <div class="info-contenido">
                        <h3>Teléfono</h3>
                        <p>+51 XXX XXX XXX</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icono">💬</div>
                    <div class="info-contenido">
                        <h3>WhatsApp</h3>
                        <p><a href="https://wa.me/51XXXXXXXXX" target="_blank">+51 XXX XXX XXX</a></p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icono">📍</div>
                    <div class="info-contenido">
                        <h3>Dirección</h3>
                        <p>San Isidro, Lima, Perú</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icono">🌐</div>
                    <div class="info-contenido">
                        <h3>Sitio Web</h3>
                        <p><a href="#" target="_blank">www.sistemaclinico.com</a></p>
                    </div>
                </div>
            </div>

            <!-- Horarios de Atención -->
            <div class="horarios-card">
                <h3>🕐 Horarios de Atención</h3>
                <div class="horario-item">
                    <span class="horario-dia">Lunes - Viernes</span>
                    <span class="horario-horas">8:00 AM - 6:00 PM</span>
                </div>
                <div class="horario-item">
                    <span class="horario-dia">Sábados</span>
                    <span class="horario-horas">9:00 AM - 1:00 PM</span>
                </div>
                <div class="horario-item">
                    <span class="horario-dia">Domingos</span>
                    <span class="horario-horas">Cerrado</span>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="faq-section">
        <h2>❓ Preguntas Frecuentes</h2>
        
        <div class="faq-item">
            <p class="faq-pregunta">💡 ¿Cómo recupero mi contraseña?</p>
            <p class="faq-respuesta">
                Puedes usar la opción "¿Olvidaste tu contraseña?" en la página de login. 
                Recibirás un email con instrucciones para crear una nueva contraseña.
            </p>
        </div>

        <div class="faq-item">
            <p class="faq-pregunta">💡 ¿Cómo cambio los permisos de un usuario?</p>
            <p class="faq-respuesta">
                Ve a Gestionar Usuarios, selecciona el usuario y usa la opción "Cambiar Rol" 
                para asignar los permisos correspondientes (Admin, Doctor, Paciente).
            </p>
        </div>

        <div class="faq-item">
            <p class="faq-pregunta">💡 ¿Cómo genero reportes del sistema?</p>
            <p class="faq-respuesta">
                Accede a la sección "Reportes" desde el menú principal. Puedes generar reportes 
                en formato PDF o Excel con estadísticas de citas, pacientes y doctores.
            </p>
        </div>

        <div class="faq-item">
            <p class="faq-pregunta">💡 ¿Qué hago si encuentro un error?</p>
            <p class="faq-respuesta">
                Usa el formulario de contacto seleccionando "Reportar Error" y describe el problema 
                en detalle. Nuestro equipo técnico lo revisará y te responderá a la brevedad.
            </p>
        </div>

        <div class="faq-item">
            <p class="faq-pregunta">💡 ¿Cómo realizo backups del sistema?</p>
            <p class="faq-respuesta">
                Ve a Configuración > Respaldos. Puedes crear backups manuales o programar 
                respaldos automáticos. Se recomienda hacer backups semanalmente.
            </p>
        </div>

        <div class="faq-item">
            <p class="faq-pregunta">💡 ¿El sistema tiene soporte 24/7?</p>
            <p class="faq-respuesta">
                El soporte por email y formulario está disponible 24/7. Las consultas urgentes 
                por teléfono están disponibles en horario de oficina (Lun-Vie 8AM-6PM).
            </p>
        </div>
    </div>
</div>

<script>
// Validación del formulario
document.querySelector('.form-contacto').addEventListener('submit', function(e) {
    const mensaje = document.getElementById('mensaje').value.trim();
    
    if (mensaje.length < 10) {
        e.preventDefault();
        alert('❌ El mensaje debe tener al menos 10 caracteres');
        return false;
    }
    
    // Deshabilitar botón para evitar doble envío
    const btn = this.querySelector('.btn-enviar');
    btn.disabled = true;
    btn.textContent = '📤 Enviando...';
});

// Efecto en FAQs
document.querySelectorAll('.faq-pregunta').forEach(pregunta => {
    pregunta.addEventListener('click', function() {
        const respuesta = this.nextElementSibling;
        respuesta.style.display = respuesta.style.display === 'none' ? 'block' : 'none';
    });
});
</script>