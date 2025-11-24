// =============================================
// 🎯 APP.JS - Sistema Clínico
// Versión limpia sin duplicados
// =============================================

document.addEventListener("DOMContentLoaded", () => {
    
    // ==========================================
    // 🚪 CONFIRMACIÓN DE CERRAR SESIÓN
    // ==========================================
    const btnSalir = document.querySelector(".btn-salir");

    if (btnSalir) {
        btnSalir.addEventListener("click", function(e) {
            e.preventDefault();

            Swal.fire({
                title: '¿Desea cerrar sesión?',
                text: 'Se cerrará su sesión actual y volverá al login.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = btnSalir.href;
                }
            });
        });
    }

    // ==========================================
    // 🗑️ CONFIRMACIÓN PARA ELIMINAR REGISTROS
    // ==========================================
    const botonesEliminar = document.querySelectorAll(".btn-eliminar");
    
    botonesEliminar.forEach(boton => {
        boton.addEventListener("click", function(e) {
            e.preventDefault();
            const url = this.href;

            Swal.fire({
                title: '¿Eliminar registro?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });

    // ==========================================
    // 📱 MENÚ MÓVIL (SIDEBAR TOGGLE)
    // ==========================================
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');

    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });

        // Cerrar sidebar al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 1024) {
                if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });
    }

    // ==========================================
    // 📅 FECHA ACTUAL EN DASHBOARD
    // ==========================================
    const fechaActual = document.querySelector('.fecha-actual');
    
    if (fechaActual) {
        const ahora = new Date();
        const opciones = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        const fechaFormateada = ahora.toLocaleDateString('es-ES', opciones);
        const fechaCapitalizada = fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);
        fechaActual.textContent = `📅 ${fechaCapitalizada}`;
    }

    // ==========================================
    // 🎨 MENÚ ACTIVO (HIGHLIGHT)
    // ==========================================
    const menuLinks = document.querySelectorAll('.menu-lista a');
    const urlParams = new URLSearchParams(window.location.search);
    const accionActual = urlParams.get('accion');

    menuLinks.forEach(link => {
        const linkUrl = new URL(link.href);
        const linkAccion = new URLSearchParams(linkUrl.search).get('accion');
        
        if (linkAccion === accionActual) {
            link.classList.add('active');
        }
    });

    // ==========================================
    // 📊 ANIMACIÓN DE NÚMEROS EN KPIs
    // ==========================================
    const animarNumeros = () => {
        const numeros = document.querySelectorAll('.card-kpi p');
        
        numeros.forEach(numero => {
            const valorFinal = parseInt(numero.textContent) || 0;
            if (valorFinal === 0) return;
            
            const duracion = 1000;
            const incremento = valorFinal / (duracion / 16);
            let valorActual = 0;

            const animar = () => {
                valorActual += incremento;
                if (valorActual < valorFinal) {
                    numero.textContent = Math.floor(valorActual);
                    requestAnimationFrame(animar);
                } else {
                    numero.textContent = valorFinal;
                }
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animar();
                        observer.disconnect();
                    }
                });
            });

            observer.observe(numero);
        });
    };

    if (document.querySelector('.card-kpi')) {
        animarNumeros();
    }

    // ==========================================
    // 🔔 NOTIFICACIONES GLOBALES
    // ==========================================
    window.mostrarNotificacion = function(mensaje, tipo = 'success') {
        const iconos = {
            success: 'success',
            error: 'error',
            warning: 'warning',
            info: 'info'
        };

        Swal.fire({
            icon: iconos[tipo] || 'info',
            title: mensaje,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    };

    // ==========================================
    // 📈 LOG DE INICIALIZACIÓN
    // ==========================================
    console.log('%c🩺 Sistema Clínico Cargado', 'color: #3b82f6; font-size: 16px; font-weight: bold;');
    console.log('%c✅ JavaScript inicializado correctamente', 'color: #10b981; font-size: 12px;');
});