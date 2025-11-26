<?php
require_once __DIR__ . "/../../../nucleo/Autenticacion.php";
Autenticacion::iniciarSesionSegura();
$csrf = Autenticacion::tokenCsrf();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/recursos/css/app.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Estilos del modal - MISMOS QUE ANTES */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            backdrop-filter: blur(4px);
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 1.5rem;
            padding: 2rem;
            max-width: 450px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.4s ease;
            position: relative;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { 
                opacity: 0;
                transform: translateY(50px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-header h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            color: #1e293b;
            margin: 0;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.8rem;
            color: #64748b;
            cursor: pointer;
            transition: color 0.3s;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-modal:hover {
            color: #ef4444;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 25%;
            width: 50%;
            height: 2px;
            background: #e2e8f0;
            z-index: 0;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            z-index: 1;
            flex: 1;
        }

        .step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .step.active .step-circle {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .step.completed .step-circle {
            background: #10b981;
            color: white;
        }

        .step-label {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 500;
        }

        .step.active .step-label {
            color: #667eea;
            font-weight: 600;
        }

        .recovery-step {
            display: none;
        }

        .recovery-step.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        .form-grupo-modal {
            margin-bottom: 1.5rem;
        }

        .form-grupo-modal label {
            display: block;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-grupo-modal input {
            width: 100%;
            padding: 0.875rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-grupo-modal input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .btn-modal {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
        }

        .btn-modal:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-modal:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
            transform: none;
        }

        .info-text-modal {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .code-inputs {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            margin: 1.5rem 0;
        }

        .code-input {
            width: 50px !important;
            height: 55px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
        }

        .code-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .resend-code {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #64748b;
        }

        .resend-code button {
            background: none;
            border: none;
            color: #667eea;
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
        }

        .resend-code button:disabled {
            color: #cbd5e1;
            cursor: not-allowed;
            text-decoration: none;
        }

        .alert-success, .alert-error {
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #10b981;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #ef4444;
        }

        .link-recuperar {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        .link-recuperar a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .link-recuperar a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <main>
        <div class="login-container">
            <div class="logo-container">
                <img src="<?= BASE_URL ?>/recursos/img/logoClinica.jpg" alt="Logo de la aplicación" class="logo-login">
            </div>

            <h2>Iniciar Sesión</h2>

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'registrado') : ?>
                <div class="success-msg">✅ Registro exitoso. Ahora puedes iniciar sesión.</div>
            <?php endif; ?>

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'password_cambiada') : ?>
                <div class="success-msg">✅ Contraseña actualizada. Ya puedes iniciar sesión.</div>
            <?php endif; ?>

            <?php if (isset($error)) : ?>
                <div class="error-msg"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/index.php?accion=login" method="POST">
                <input type="hidden" name="csrf" value="<?= $csrf ?>">

                <div class="form-grupo">
                    <label for="email">📧 Correo Electrónico</label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           placeholder="ejemplo@correo.com" 
                           autocomplete="email"
                           required>

                    <label for="password">🔑 Contraseña</label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           placeholder="********" 
                           autocomplete="current-password"
                           required>
                </div>

                <div class="checkbox-grupo">
                    <input type="checkbox" id="recordarme" name="recordarme">
                    <label for="recordarme">Recordarme</label>
                </div>

                <button type="submit" class="btn">Ingresar</button>
            </form>

            <div class="link-recuperar">
                <a href="#" id="btnAbrirRecuperacion">¿Olvidaste tu contraseña?</a>
            </div>

            <p class="link-registro">
                ¿No tienes cuenta?
                <a href="<?= BASE_URL ?>/index.php?accion=registroVista">Regístrate aquí</a>
            </p>
        </div>
    </main>

    <!-- MODAL DE RECUPERACIÓN -->
    <div class="modal-overlay" id="modalRecuperacion">
        <div class="modal-content">
            <div class="modal-header">
                <h3>🔐 Recuperar Contraseña</h3>
                <button class="close-modal" id="btnCerrarModal">&times;</button>
            </div>

            <!-- Indicador de pasos -->
            <div class="step-indicator">
                <div class="step active" id="indicator-step1">
                    <div class="step-circle">1</div>
                    <div class="step-label">Teléfono</div>
                </div>
                <div class="step" id="indicator-step2">
                    <div class="step-circle">2</div>
                    <div class="step-label">Código</div>
                </div>
                <div class="step" id="indicator-step3">
                    <div class="step-circle">3</div>
                    <div class="step-label">Nueva Clave</div>
                </div>
            </div>

            <!-- PASO 1: Ingresar teléfono -->
            <div class="recovery-step active" id="step1">
                <p class="info-text-modal">
                    📱 Ingresa tu número de teléfono registrado. Te enviaremos un código de verificación.
                </p>
                
                <div id="mensaje-step1"></div>

                <form id="formTelefono">
                    <div class="form-grupo-modal">
                        <label for="telefono">Número de Teléfono</label>
                        <input type="tel" 
                               id="telefono" 
                               name="telefono" 
                               placeholder="987654321" 
                               pattern="[0-9]{9}" 
                               maxlength="9"
                               autocomplete="tel"
                               required>
                    </div>
                    <button type="submit" class="btn-modal" id="btnEnviarCodigo">
                        Enviar Código
                    </button>
                </form>
            </div>

            <!-- PASO 2: Verificar código -->
            <div class="recovery-step" id="step2">
                <p class="info-text-modal">
                    ✉️ Hemos enviado un código de 6 dígitos a tu teléfono <strong id="telefonoMostrado"></strong>
                </p>

                <div id="mensaje-step2"></div>

                <form id="formCodigo">
                    <div class="code-inputs">
                        <input type="text" class="code-input" maxlength="1" pattern="[0-9]" autocomplete="off" required>
                        <input type="text" class="code-input" maxlength="1" pattern="[0-9]" autocomplete="off" required>
                        <input type="text" class="code-input" maxlength="1" pattern="[0-9]" autocomplete="off" required>
                        <input type="text" class="code-input" maxlength="1" pattern="[0-9]" autocomplete="off" required>
                        <input type="text" class="code-input" maxlength="1" pattern="[0-9]" autocomplete="off" required>
                        <input type="text" class="code-input" maxlength="1" pattern="[0-9]" autocomplete="off" required>
                    </div>

                    <button type="submit" class="btn-modal" id="btnVerificarCodigo">
                        Verificar Código
                    </button>
                </form>

                <div class="resend-code">
                    ¿No recibiste el código? 
                    <button id="btnReenviarCodigo" disabled>
                        Reenviar (<span id="countdown">60</span>s)
                    </button>
                </div>
            </div>

            <!-- PASO 3: Nueva contraseña -->
            <div class="recovery-step" id="step3">
                <p class="info-text-modal">
                    🔑 Ingresa tu nueva contraseña (mínimo 6 caracteres)
                </p>

                <div id="mensaje-step3"></div>

                <form id="formNuevaPassword">
                    <div class="form-grupo-modal">
                        <label for="nueva_password">Nueva Contraseña</label>
                        <input type="password" 
                               id="nueva_password" 
                               name="nueva_password" 
                               placeholder="Mínimo 6 caracteres"
                               minlength="6"
                               autocomplete="new-password"
                               required>
                    </div>
                    <div class="form-grupo-modal">
                        <label for="confirmar_password">Confirmar Contraseña</label>
                        <input type="password" 
                               id="confirmar_password" 
                               name="confirmar_password" 
                               placeholder="Repite la contraseña"
                               minlength="6"
                               autocomplete="new-password"
                               required>
                    </div>
                    <button type="submit" class="btn-modal" id="btnCambiarPassword">
                        Cambiar Contraseña
                    </button>
                </form>
            </div>
        </div>
    </div>

    <?php include __DIR__ . "/../../../includes/footer.php"; ?>

    <script>
        const BASE_URL = '<?= BASE_URL ?>';
        let telefonoUsuario = '';
        let countdownTimer = null;

        // Abrir modal
        document.getElementById('btnAbrirRecuperacion').addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('modalRecuperacion').classList.add('active');
        });

        // Cerrar modal
        document.getElementById('btnCerrarModal').addEventListener('click', () => {
            document.getElementById('modalRecuperacion').classList.remove('active');
            resetModal();
        });

        // Cerrar al hacer clic fuera
        document.getElementById('modalRecuperacion').addEventListener('click', (e) => {
            if (e.target === e.currentTarget) {
                e.currentTarget.classList.remove('active');
                resetModal();
            }
        });

        // PASO 1: Enviar código
        document.getElementById('formTelefono').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const telefono = document.getElementById('telefono').value;
            const btn = document.getElementById('btnEnviarCodigo');
            const mensaje = document.getElementById('mensaje-step1');
            
            btn.disabled = true;
            btn.textContent = 'Enviando...';
            
            console.log('📤 Enviando solicitud a:', `${BASE_URL}/index.php?accion=enviarCodigoRecuperacion`);
            console.log('📞 Teléfono:', telefono);
            
            try {
                const response = await fetch(`${BASE_URL}/index.php?accion=enviarCodigoRecuperacion`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ telefono })
                });
                
                console.log('📥 Status:', response.status);
                const responseText = await response.text();
                console.log('📥 Response raw:', responseText);
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('❌ Error al parsear JSON:', e);
                    console.error('Respuesta recibida:', responseText);
                    throw new Error('Respuesta inválida del servidor: ' + responseText.substring(0, 100));
                }
                
                console.log('📥 Response parsed:', data);
                
                if (data.success) {
                    telefonoUsuario = telefono;
                    mensaje.innerHTML = '<div class="alert-success">✅ Código enviado exitosamente</div>';
                    setTimeout(() => {
                        goToStep(2);
                        startCountdown();
                    }, 1000);
                } else {
                    mensaje.innerHTML = `<div class="alert-error">❌ ${data.message}</div>`;
                    btn.disabled = false;
                    btn.textContent = 'Enviar Código';
                }
            } catch (error) {
                console.error('❌ Error completo:', error);
                mensaje.innerHTML = `<div class="alert-error">❌ Error: ${error.message}</div>`;
                btn.disabled = false;
                btn.textContent = 'Enviar Código';
            }
        });

        // PASO 2: Verificar código
        const codeInputs = document.querySelectorAll('.code-input');
        
        // Auto-avanzar entre inputs
        codeInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && index < codeInputs.length - 1) {
                    codeInputs[index + 1].focus();
                }
            });
            
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    codeInputs[index - 1].focus();
                }
            });
        });

        document.getElementById('formCodigo').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const codigo = Array.from(codeInputs).map(input => input.value).join('');
            const btn = document.getElementById('btnVerificarCodigo');
            const mensaje = document.getElementById('mensaje-step2');
            
            if (codigo.length !== 6) {
                mensaje.innerHTML = '<div class="alert-error">❌ Ingresa los 6 dígitos</div>';
                return;
            }
            
            btn.disabled = true;
            btn.textContent = 'Verificando...';
            
            try {
                const response = await fetch(`${BASE_URL}/index.php?accion=verificarCodigoRecuperacion`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ telefono: telefonoUsuario, codigo })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    mensaje.innerHTML = '<div class="alert-success">✅ Código verificado</div>';
                    setTimeout(() => goToStep(3), 1000);
                } else {
                    mensaje.innerHTML = `<div class="alert-error">❌ ${data.message}</div>`;
                    btn.disabled = false;
                    btn.textContent = 'Verificar Código';
                    codeInputs.forEach(input => input.value = '');
                    codeInputs[0].focus();
                }
            } catch (error) {
                mensaje.innerHTML = '<div class="alert-error">❌ Error al verificar código</div>';
                btn.disabled = false;
                btn.textContent = 'Verificar Código';
            }
        });

        // Reenviar código
        document.getElementById('btnReenviarCodigo').addEventListener('click', async () => {
            const btn = document.getElementById('btnReenviarCodigo');
            btn.disabled = true;
            
            try {
                const response = await fetch(`${BASE_URL}/index.php?accion=enviarCodigoRecuperacion`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ telefono: telefonoUsuario })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    startCountdown();
                    document.getElementById('mensaje-step2').innerHTML = 
                        '<div class="alert-success">✅ Código reenviado</div>';
                }
            } catch (error) {
                btn.disabled = false;
            }
        });

      // PASO 3: Cambiar contraseña
document.getElementById('formNuevaPassword').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const password = document.getElementById('nueva_password').value;
    const confirmar = document.getElementById('confirmar_password').value;
    const btn = document.getElementById('btnCambiarPassword');
    const mensaje = document.getElementById('mensaje-step3');
    
    console.log('🔑 [PASO 3] Iniciando cambio de contraseña');
    console.log('📞 Teléfono guardado:', telefonoUsuario);
    console.log('🔑 Password length:', password.length);
    
    if (password !== confirmar) {
        mensaje.innerHTML = '<div class="alert-error">❌ Las contraseñas no coinciden</div>';
        return;
    }
    
    if (password.length < 6) {
        mensaje.innerHTML = '<div class="alert-error">❌ La contraseña debe tener al menos 6 caracteres</div>';
        return;
    }
    
    btn.disabled = true;
    btn.textContent = 'Actualizando...';
    
    try {
        console.log('📤 [PASO 3] Enviando solicitud a:', `${BASE_URL}/index.php?accion=cambiarPasswordRecuperacion`);
        
        const response = await fetch(`${BASE_URL}/index.php?accion=cambiarPasswordRecuperacion`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                telefono: telefonoUsuario, 
                nueva_password: password 
            })
        });
        
        console.log('📥 [PASO 3] Status:', response.status);
        const responseText = await response.text();
        console.log('📥 [PASO 3] Response raw:', responseText);
        
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            console.error('❌ Error al parsear JSON:', e);
            console.error('Respuesta recibida:', responseText);
            throw new Error('Respuesta inválida del servidor');
        }
        
        console.log('📥 [PASO 3] Response parsed:', data);
        
        if (data.success) {
            mensaje.innerHTML = '<div class="alert-success">✅ Contraseña actualizada exitosamente</div>';
            setTimeout(() => {
                window.location.href = `${BASE_URL}/index.php?accion=loginVista&msg=password_cambiada`;
            }, 2000);
        } else {
            console.error('❌ [PASO 3] Error del servidor:', data.message);
            mensaje.innerHTML = `<div class="alert-error">❌ ${data.message}</div>`;
            btn.disabled = false;
            btn.textContent = 'Cambiar Contraseña';
        }
    } catch (error) {
        console.error('❌ [PASO 3] Error completo:', error);
        mensaje.innerHTML = `<div class="alert-error">❌ Error: ${error.message}</div>`;
        btn.disabled = false;
        btn.textContent = 'Cambiar Contraseña';
    }
});

       // Busca esta función (alrededor de la línea 730-750)
function goToStep(step) {
    // Ocultar todos los pasos
    document.querySelectorAll('.recovery-step').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.step').forEach(s => {
        s.classList.remove('active');
        s.classList.remove('completed');
    });
    
    // Mostrar paso actual
    document.getElementById(`step${step}`).classList.add('active');
    document.getElementById(`indicator-step${step}`).classList.add('active');
    
    // Marcar pasos anteriores como completados
    for (let i = 1; i < step; i++) {
        document.getElementById(`indicator-step${i}`).classList.add('completed');
    }
    
    // Actualizar info
    if (step === 2) {
        // ✅ FIX: Verificar que el elemento existe antes de modificarlo
        const telefonoElement = document.getElementById('telefonoMostrado');
        if (telefonoElement) {
            telefonoElement.textContent = telefonoUsuario.replace(/(\d{3})(\d{3})(\d{3})/, '$1 $2 $3');
        }
        
        // Limpiar inputs de código
        const codeInputs = document.querySelectorAll('.code-input');
        codeInputs.forEach(input => input.value = '');
        
        // Enfocar primer input
        if (codeInputs[0]) {
            codeInputs[0].focus();
        }
    }
}

        function startCountdown() {
            let seconds = 60;
            const btn = document.getElementById('btnReenviarCodigo');
            const countdown = document.getElementById('countdown');
            
            btn.disabled = true;
            
            if (countdownTimer) clearInterval(countdownTimer);
            
            countdownTimer = setInterval(() => {
                seconds--;
                countdown.textContent = seconds;
                
                if (seconds <= 0) {
                    clearInterval(countdownTimer);
                    btn.disabled = false;
                    btn.innerHTML = 'Reenviar código';
                }
            }, 1000);
        }

        function resetModal() {
            document.querySelectorAll('.recovery-step').forEach(s => s.classList.remove('active'));
            document.getElementById('step1').classList.add('active');
            
            document.querySelectorAll('.step').forEach(s => {
                s.classList.remove('active');
                s.classList.remove('completed');
            });
            document.getElementById('indicator-step1').classList.add('active');
            
            document.querySelectorAll('form').forEach(f => f.reset());
            document.querySelectorAll('[id^="mensaje-"]').forEach(m => m.innerHTML = '');
            
            if (countdownTimer) clearInterval(countdownTimer);
        }
    </script>
</body>
</html>