<?php
/**
 * =====================================================
 * 📄 Generador de PDF para Configuraciones
 * =====================================================
 * Ubicación: includes/generador-pdf-config.php
 */

namespace User\Clinica;

use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class GeneradorPDFConfiguracion
{
    /**
     * Genera el PDF con las configuraciones
     */
    public function generarPDF(array $configsPorCategoria, string $usuarioNombre, string $usuarioEmail): void
    {
        try {
            $nombreClinica = $_SESSION['nombre_clinica'] ?? 'Clínica Salud Total';

            $html = $this->generarHTML($configsPorCategoria, $nombreClinica, $usuarioNombre, $usuarioEmail);

            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('defaultFont', 'Helvetica');
            $options->set('chroot', __DIR__ . '/../publico');

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $filename = 'Configuracion_Sistema_' . date('Y-m-d_H-i-s') . '.pdf';
            $dompdf->stream($filename, ['Attachment' => 0]);
            exit;

        } catch (Exception $e) {
            error_log("Error generando PDF: " . $e->getMessage());
            die("Error al generar PDF: " . $e->getMessage());
        }
    }

    /**
     * Genera el HTML del PDF
     */
    private function generarHTML(array $configsPorCategoria, string $nombreClinica, string $usuarioNombre, string $usuarioEmail): string
    {
        $html = '<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Configuración del Sistema</title>
<style>
    @page { margin: 2cm; size: A4; }
    body { font-family: "Helvetica", "Arial", sans-serif; font-size: 10pt; color: #2c3e50; line-height: 1.6; }
    .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #3498db; }
    .header h1 { font-size: 24pt; color: #2c3e50; margin-bottom: 10px; font-weight: 700; }
    .header .subtitle { font-size: 14pt; color: #7f8c8d; margin-bottom: 5px; }
    .header .date { font-size: 10pt; color: #95a5a6; }
    .info-box { background: #ecf0f1; padding: 15px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #3498db; }
    .info-box h3 { font-size: 12pt; color: #2c3e50; margin-bottom: 10px; }
    .info-box p { font-size: 10pt; color: #7f8c8d; margin: 3px 0; }
    .categoria { margin-bottom: 30px; page-break-inside: avoid; }
    .categoria-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 15px; border-radius: 8px; margin-bottom: 15px; }
    .categoria-header h2 { font-size: 14pt; font-weight: 600; margin: 0; }
    .config-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .config-table th { background: #f8f9fa; padding: 10px; text-align: left; font-size: 9pt; font-weight: 600; color: #7f8c8d; border-bottom: 2px solid #ecf0f1; }
    .config-table td { padding: 10px; border-bottom: 1px solid #ecf0f1; font-size: 10pt; }
    .config-table tr:last-child td { border-bottom: none; }
    .clave { font-weight: 600; color: #2c3e50; }
    .valor { color: #27ae60; font-weight: 500; }
    .valor-boolean { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 9pt; font-weight: 600; }
    .valor-boolean.activo { background: #d4edda; color: #155724; }
    .valor-boolean.inactivo { background: #f8d7da; color: #721c24; }
    .descripcion { color: #7f8c8d; font-size: 9pt; font-style: italic; }
    .footer { margin-top: 40px; padding-top: 20px; border-top: 2px solid #ecf0f1; text-align: center; color: #95a5a6; font-size: 9pt; }
    .watermark { position: fixed; bottom: 20px; right: 20px; opacity: 0.3; font-size: 8pt; color: #bdc3c7; }
</style>
</head>
<body>
<div class="header">
<h1>⚙️ Configuración del Sistema</h1>
<div class="subtitle">' . htmlspecialchars($nombreClinica) . '</div>
<div class="date">Generado el ' . date('d/m/Y') . ' a las ' . date('H:i:s') . '</div>
</div>

<div class="info-box">
<h3>📋 Información del Documento</h3>
<p><strong>Generado por:</strong> ' . htmlspecialchars($usuarioNombre) . '</p>
<p><strong>Email:</strong> ' . htmlspecialchars($usuarioEmail) . '</p>
<p><strong>Fecha de exportación:</strong> ' . date('d/m/Y H:i:s') . '</p>
</div>';

        $iconos = [
            'general' => '🏥',
            'notificaciones' => '🔔',
            'seguridad' => '🔐',
            'sistema' => '📊',
            'citas' => '📅',
            'facturacion' => '💳'
        ];

        $nombresCat = [
            'general' => 'Información General',
            'notificaciones' => 'Notificaciones',
            'seguridad' => 'Seguridad',
            'sistema' => 'Sistema',
            'citas' => 'Gestión de Citas',
            'facturacion' => 'Facturación'
        ];

        foreach ($configsPorCategoria as $categoria => $configs) {
            $icono = $iconos[$categoria] ?? '⚙️';
            $nombreCategoria = $nombresCat[$categoria] ?? ucfirst($categoria);

            $html .= '<div class="categoria">
                <div class="categoria-header">
                    <h2>' . $icono . ' ' . $nombreCategoria . '</h2>
                </div>
                <table class="config-table">
                <thead>
                    <tr>
                        <th>Configuración</th>
                        <th>Valor Actual</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>';

            // Si no es array, lo convertimos a uno simple para mostrar
            if (!is_array($configs)) {
                $configs = [
                    ['clave' => $nombreCategoria, 'valor' => $configs, 'tipo' => gettype($configs), 'descripcion' => '']
                ];
            }

            foreach ($configs as $config) {
                $clave = htmlspecialchars($config['clave'] ?? '');
                $valor = $this->formatearValor($config['valor'] ?? '', $config['tipo'] ?? 'string');
                $descripcion = htmlspecialchars($config['descripcion'] ?? '');

                $html .= "<tr>
                    <td class='clave'>{$this->formatearClave($clave)}</td>
                    <td class='valor'>{$valor}</td>
                    <td class='descripcion'>{$descripcion}</td>
                </tr>";
            }

            $html .= '</tbody></table></div>';
        }

        $html .= '<div class="footer">
<p><strong>' . htmlspecialchars($nombreClinica) . '</strong></p>
<p>Sistema de Gestión Clínica v1.0</p>
<p>Documento confidencial - uso autorizado</p>
</div>
<div class="watermark">Confidencial - ' . date('Y') . '</div>
</body>
</html>';

        return $html;
    }

    private function formatearClave(string $clave): string
    {
        return ucwords(str_replace('_', ' ', $clave));
    }

    private function formatearValor($valor, string $tipo): string
    {
        if ($tipo === 'boolean') {
            $esActivo = ($valor === '1' || $valor === 'true' || $valor === true);
            return $esActivo
                ? '<span class="valor-boolean activo">✓ Activo</span>'
                : '<span class="valor-boolean inactivo">✗ Inactivo</span>';
        }

        if (is_array($valor)) {
            return htmlspecialchars(json_encode($valor, JSON_PRETTY_PRINT));
        }

        $valorStr = (string)$valor;

        if (strlen($valorStr) > 50) {
            return nl2br(htmlspecialchars(wordwrap($valorStr, 50, "\n", true)));
        }

        return htmlspecialchars($valorStr);
    }
}
