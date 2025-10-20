<?php
// =====================
// ⚙️ Vista: Configuración del Sistema (Funcional)
// =====================
?>

<style>
.configuracion-container {
    padding: 25px;
    max-width: 1200px;
    margin: 0 auto;
}

.config-header {
    margin-bottom: 35px;
}

.config-header h1 {
    font-size: 32px;
    color: #2c3e50;
    margin: 0 0 10px 0;
    font-weight: 700;
}

.config-header p {
    color: #7f8c8d;
    font-size: 15px;
    margin: 0;
}

.config-sections {
    display: grid;
    gap: 25px;
}

.config-section {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.config-section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #ecf0f1;
}

.config-section-icon {
    font-size: 28px;
}

.config-section-header h2 {
    font-size: 22px;
    color: #2c3e50;
    margin: 0;
    font-weight: 600;
}

.config-section-header p {
    color: #7f8c8d;
    font-size: 14px;
    margin: 5px 0 0 0;
}

.config-form {
    display: grid;
    gap: 20px;
}

.config-group {
    display: grid;
    gap: 8px;
}

.config-group label {
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 5px;
}

.config-group input,
.config-group select,
.config-group textarea {
    padding: 12px 15px;
    border: 2px solid #ecf0f1;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    font-family: inherit;
}

.config-group input:focus,
.config-group select:focus,
.config-group textarea:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.config-group textarea {
    resize: vertical;
    min-height: 100px;
}

.config-group small {
    color: #7f8c8d;
    font-size: 13px;
}

.config-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

/* Toggle switch */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background-color: #27ae60;
}

input:checked + .toggle-slider:before {
    transform: translateX(26px);
}

.toggle-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #ecf0f1;
}

.toggle-item:last-child {
    border-bottom: none;
}

.toggle-info h4 {
    font-size: 15px;
    color: #2c3e50;
    margin: 0 0 5px 0;
}

.toggle-info p {
    font-size: 13px;
    color: #7f8c8d;
    margin: 0;
}

.config-actions {
    display: flex;
    gap: 15px;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 2px solid #ecf0f1;
}

.btn-config {
    padding: 12px 28px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-guardar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-guardar:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-cancelar {
    background: #ecf0f1;
    color: #7f8c8d;
}

.btn-cancelar:hover {
    background: #bdc3c7;
}

.btn-restaurar {
    background: #e74c3c;
    color: white;
}

.btn-restaurar:hover {
    background: #c0392b;
}

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

.alerta-info {
    background: #d1ecf1;
    color: #0c5460;
    border-left: 4px solid #17a2b8;
}

.info-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.info-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border-left: 4px solid var(--color);
}

.info-card.primary { --color: #3498db; }
.info-card.success { --color: #27ae60; }
.info-card.warning { --color: #f39c12; }

.info-card h4 {
    font-size: 13px;
    color: #7f8c8d;
    margin: 0 0 8px 0;
    text-transform: uppercase;
    font-weight: 600;
}

.info-card p {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
}

@media (max-width: 768px) {
    .configuracion-container {
        padding: 15px;
    }
    
    .config-grid-2 {
        grid-template-columns: 1fr;
    }
    
    .info-cards {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="configuracion-container">
    <!-- Header -->
    <div class="config-header">
        <h1>⚙️ Configuración del Sistema</h1>
        <p>Administra las configuraciones generales de la clínica</p>
    </div>

    <!-- Alertas -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alerta alerta-<?= $_SESSION['mensaje']['tipo'] === 'exito' ? 'exito' : 'error' ?>">
            <span><?= $_SESSION['mensaje']['tipo'] === 'exito' ? '✅' : '❌' ?></span>
            <?= htmlspecialchars($_SESSION['mensaje']['texto']) ?>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <div class="config-sections">
        <!-- 🏥 Información General -->
        <div class="config-section">
            <div class="config-section-header">
                <span class="config-section-icon">🏥</span>
                <div>
                    <h2>Información General</h2>
                    <p>Datos básicos de la clínica</p>
                </div>
            </div>

            <form class="config-form" method="POST" action="?accion=guardarConfiguracion">
                <div class="config-grid-2">
                    <div class="config-group">
                        <label>Nombre de la Clínica</label>
                        <input type="text" name="nombre_clinica" value="<?= htmlspecialchars($configs['nombre_clinica'] ?? '') ?>" required>
                    </div>

                    <div class="config-group">
                        <label>RUC</label>
                        <input type="text" name="ruc" value="<?= htmlspecialchars($configs['ruc'] ?? '') ?>" pattern="[0-9]{11}" maxlength="11">
                        <small>11 dígitos numéricos</small>
                    </div>
                </div>

                <div class="config-grid-2">
                    <div class="config-group">
                        <label>Teléfono</label>
                        <input type="tel" name="telefono" value="<?= htmlspecialchars($configs['telefono'] ?? '') ?>">
                    </div>

                    <div class="config-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($configs['email'] ?? '') ?>">
                    </div>
                </div>

                <div class="config-group">
                    <label>Dirección</label>
                    <input type="text" name="direccion" value="<?= htmlspecialchars($configs['direccion'] ?? '') ?>">
                </div>

                <div class="config-group">
                    <label>Horario de Atención</label>
                    <textarea name="horario_atencion"><?= htmlspecialchars($configs['horario_atencion'] ?? '') ?></textarea>
                </div>

                <div class="config-actions">
                    <button type="submit" class="btn-config btn-guardar">
                        <span>💾</span> Guardar Cambios
                    </button>
                    <button type="reset" class="btn-config btn-cancelar">
                        <span>↩️</span> Restaurar
                    </button>
                </div>
            </form>
        </div>

        <!-- 🔔 Notificaciones -->
        <div class="config-section">
            <div class="config-section-header">
                <span class="config-section-icon">🔔</span>
                <div>
                    <h2>Notificaciones</h2>
                    <p>Configura las notificaciones del sistema</p>
                </div>
            </div>

            <form class="config-form" method="POST" action="?accion=guardarNotificaciones">
                <div class="toggle-item">
                    <div class="toggle-info">
                        <h4>Notificaciones por Email</h4>
                        <p>Enviar correos de confirmación de citas</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="notif_email" <?= ($configs['notif_email'] ?? '1') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="toggle-item">
                    <div class="toggle-info">
                        <h4>Recordatorios Automáticos</h4>
                        <p>Enviar recordatorios 24h antes de la cita</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="notif_recordatorios" <?= ($configs['notif_recordatorios'] ?? '1') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="toggle-item">
                    <div class="toggle-info">
                        <h4>Notificaciones de Facturas</h4>
                        <p>Alertar sobre facturas pendientes</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="notif_facturas" <?= ($configs['notif_facturas'] ?? '0') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="toggle-item">
                    <div class="toggle-info">
                        <h4>Reportes Semanales</h4>
                        <p>Recibir resumen de actividad semanal</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="notif_reportes" <?= ($configs['notif_reportes'] ?? '1') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="config-actions">
                    <button type="submit" class="btn-config btn-guardar">
                        <span>💾</span> Guardar Configuración
                    </button>
                </div>
            </form>
        </div>

        <!-- 🔐 Seguridad -->
        <div class="config-section">
            <div class="config-section-header">
                <span class="config-section-icon">🔐</span>
                <div>
                    <h2>Seguridad</h2>
                    <p>Configuraciones de seguridad del sistema</p>
                </div>
            </div>

            <form class="config-form" method="POST" action="?accion=guardarSeguridadConfig">
                <div class="config-grid-2">
                    <div class="config-group">
                        <label>Tiempo de Sesión (minutos)</label>
                        <input type="number" name="sesion_timeout" value="<?= htmlspecialchars($configs['sesion_timeout'] ?? '30') ?>" min="5" max="120" required>
                        <small>Tiempo antes de cerrar sesión automáticamente</small>
                    </div>

                    <div class="config-group">
                        <label>Intentos de Login</label>
                        <input type="number" name="max_login_attempts" value="<?= htmlspecialchars($configs['max_login_attempts'] ?? '3') ?>" min="3" max="10" required>
                        <small>Máximo de intentos fallidos antes de bloqueo</small>
                    </div>
                </div>

                <div class="toggle-item">
                    <div class="toggle-info">
                        <h4>Autenticación de Dos Factores</h4>
                        <p>Requiere verificación adicional al iniciar sesión</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="two_factor_auth" <?= ($configs['two_factor_auth'] ?? '0') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="toggle-item">
                    <div class="toggle-info">
                        <h4>Registro de Auditoría</h4>
                        <p>Guardar registro de todas las acciones del sistema</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="audit_log" <?= ($configs['audit_log'] ?? '1') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="config-actions">
                    <button type="submit" class="btn-config btn-guardar">
                        <span>🔒</span> Guardar Configuración
                    </button>
                </div>
            </form>
        </div>

        <!-- 📊 Sistema -->
        <div class="config-section">
            <div class="config-section-header">
                <span class="config-section-icon">📊</span>
                <div>
                    <h2>Información del Sistema</h2>
                    <p>Detalles técnicos y estadísticas</p>
                </div>
            </div>

            <div class="alerta alerta-info">
                <span>ℹ️</span>
                Sistema de Gestión Clínica v1.0 - Desarrollado en PHP <?= phpversion() ?>
            </div>

            <div class="info-cards">
                <div class="info-card primary">
                    <h4>Versión PHP</h4>
                    <p><?= phpversion() ?></p>
                </div>

                <div class="info-card success">
                    <h4>Base de Datos</h4>
                    <p>PostgreSQL</p>
                </div>

                <div class="info-card warning">
                    <h4>Última Actualización</h4>
                    <p><?= date('d/m/Y') ?></p>
                </div>
            </div>

            <div class="config-actions">
                <a href="?accion=verAuditoria" class="btn-config btn-guardar">
                    <span>📋</span> Ver Log de Auditoría
                </a>
                <a href="?accion=exportarConfiguracionesPDF" class="btn-config btn-guardar" target="_blank">
                    <span>📄</span> Exportar PDF
                </a>
                <a href="?accion=exportarConfiguraciones" class="btn-config btn-cancelar">
                    <span>📥</span> Exportar JSON
                </a>
                <button type="button" class="btn-config btn-restaurar" onclick="confirmarRestaurar()">
                    <span>♻️</span> Restaurar por Defecto
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Formulario oculto para restaurar -->
<form id="formRestaurar" method="POST" action="?accion=restaurarPorDefecto" style="display: none;">
</form>

<script>
// Auto-ocultar alertas después de 5 segundos
setTimeout(function() {
    const alertas = document.querySelectorAll('.alerta');
    alertas.forEach(alerta => {
        alerta.style.transition = 'opacity 0.5s';
        alerta.style.opacity = '0';
        setTimeout(() => alerta.remove(), 500);
    });
}, 5000);

// Confirmar restauración
function confirmarRestaurar() {
    if (confirm('¿Estás seguro de restaurar todas las configuraciones por defecto? Esta acción no se puede deshacer.')) {
        document.getElementById('formRestaurar').submit();
    }
}
</script>