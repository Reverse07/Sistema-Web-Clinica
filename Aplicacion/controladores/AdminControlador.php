<?php
require_once __DIR__ . "/../../nucleo/Autenticacion.php";
require_once __DIR__ . "/../modelos/Usuario.php";
require_once __DIR__ . "/../modelos/Doctor.php";
require_once __DIR__ . "/../modelos/Paciente.php";

/**
 * AdminControlador
 * 
 * Responsabilidad: Solo gestión administrativa general
 * - Dashboard con KPIs
 * - Reportes del sistema
 * - Configuración general
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

        $totalUsuarios  = Usuario::contarTodos();
        $totalDoctores  = Usuario::contarPorRol(2);
        $totalPacientes = Usuario::contarPorRol(3);

        $vistaInterna = __DIR__ . "/../vistas/admin/inicio.php";
        require __DIR__ . "/../../includes/layout-admin.php";
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
    
}