document.addEventListener("DOMContentLoaded", () => {
    const btnSalir = document.querySelector(".btn-salir");

    if (btnSalir) {
        btnSalir.addEventListener("click", function(e) {
            e.preventDefault(); // Detiene el enlace por defecto

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
                    window.location.href = btnSalir.href; // Redirige al logout
                }
            });
        });
    }
});

    // 🗑️ Confirmación visual para eliminar usuario o doctor
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

