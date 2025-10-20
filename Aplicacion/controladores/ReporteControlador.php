<?php
require_once __DIR__ . '/../../nucleo/Autenticacion.php';
require_once __DIR__ . '/../../nucleo/BaseDatos.php';
require_once __DIR__ . '/../modelos/Paciente.php';
require_once __DIR__ . '/../modelos/Cita.php';
require_once __DIR__ . '/../modelos/Factura.php';
// Al inicio del archivo ReporteControlador.php
require_once __DIR__ . '/../../includes/generador-pdf-config.php';

use User\Clinica\GeneradorPDFConfiguracion;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


class ReporteControlador
{
    public function index()
    {
        Autenticacion::requiereRoles(['admin']);

        // ==============================
        // 📊 ESTADÍSTICAS PRINCIPALES
        // ==============================

        // Total de pacientes
        $totalPacientes = Paciente::contarPacientes();

        // Total de citas
        $totalCitas = Cita::contarCitas();

        // Total de facturas
        $totalFacturas = Factura::contarFacturas();

        // Total recaudado
        $totalRecaudado = Factura::sumarRecaudado();

        // Facturas recientes
        $facturasRecientes = Factura::obtenerUltimas(5);

        // ==============================
        // 📈 DATOS PARA GRÁFICOS
        // ==============================

        // Tendencia mensual (últimos 6 meses)
        $tendenciaMensual = $this->obtenerTendenciaMensual();

        // Distribución de facturas por estado
        $distribucionEstados = $this->obtenerDistribucionEstados();

        // ==============================
        // 🧾 CARGAR VISTA
        // ==============================
        $vistaInterna = __DIR__ . '/../vistas/admin/reportes.php';
        require __DIR__ . '/../../includes/layout-admin.php';
    }

    /**
     * Obtiene ingresos mensuales de los últimos 6 meses
     */
    private function obtenerTendenciaMensual(): array
{
    $pdo = BaseDatos::pdo();

    // Obtener datos de los últimos 6 meses
    $sql = "
        SELECT 
            TO_CHAR(emitida_en, 'YYYY-MM') as mes,
            TO_CHAR(emitida_en, 'Month') as mes_nombre,
            SUM(monto) as total
        FROM facturas
        WHERE emitida_en >= CURRENT_DATE - INTERVAL '6 months'
        GROUP BY TO_CHAR(emitida_en, 'YYYY-MM'), TO_CHAR(emitida_en, 'Month')
        ORDER BY mes ASC
    ";

    $stmt = $pdo->query($sql);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Preparar arrays para los últimos 6 meses
    $meses = [];
    $montos = [];

    for ($i = 5; $i >= 0; $i--) {
        $fecha = date('Y-m', strtotime("-$i months"));
        $mesNombre = $this->nombreMesEspanol(date('n', strtotime("-$i months")));
        $meses[] = $mesNombre;

        // Buscar si hay datos para este mes
        $monto = 0;
        foreach ($resultados as $resultado) {
            if ($resultado['mes'] === $fecha) {
                $monto = (float) $resultado['total'];
                break;
            }
        }

        $montos[] = $monto;
    }

    return [
        'meses' => $meses,
        'montos' => $montos
    ];
}

    /**
     * Obtiene la distribución de facturas por estado
     */
    private function obtenerDistribucionEstados(): array
    {
        $pdo = BaseDatos::pdo();

        $sql = "
            SELECT 
                estado,
                COUNT(*) as cantidad
            FROM facturas
            GROUP BY estado
        ";

        $stmt = $pdo->query($sql);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $distribucion = [
            'Pagada' => 0,
            'Pendiente' => 0,
            'Cancelada' => 0
        ];

        foreach ($resultados as $resultado) {
            $estado = ucfirst(strtolower(trim($resultado['estado'])));
            $cantidad = (int) $resultado['cantidad'];

            if (isset($distribucion[$estado])) {
                $distribucion[$estado] = $cantidad;
            } else {
                // Si el estado no coincide exactamente, intentar mapear
                if (str_contains(strtolower($resultado['estado']), 'pag')) {
                    $distribucion['Pagada'] += $cantidad;
                } elseif (str_contains(strtolower($resultado['estado']), 'pend')) {
                    $distribucion['Pendiente'] += $cantidad;
                } elseif (str_contains(strtolower($resultado['estado']), 'cancel')) {
                    $distribucion['Cancelada'] += $cantidad;
                }
            }
        }

        return $distribucion;
    }

    /**
     * Convierte número de mes a nombre en español
     */
    private function nombreMesEspanol(int $numeroMes): string
    {
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        return $meses[$numeroMes] ?? 'Desconocido';
    }

    /**
     * 📄 Exporta el reporte completo a PDF
     */
    public function exportarPDF()
    {
        try {
            Autenticacion::requiereRoles(['admin']);

            // Recopilar todos los datos
            $datos = [
                'totalPacientes' => Paciente::contarPacientes(),
                'totalCitas' => Cita::contarCitas(),
                'totalFacturas' => Factura::contarFacturas(),
                'totalRecaudado' => Factura::sumarRecaudado(),
                'facturasRecientes' => Factura::obtenerUltimas(10),
                'tendenciaMensual' => $this->obtenerTendenciaMensual(),
                'distribucionEstados' => $this->obtenerDistribucionEstados(),
                'citasPorEstado' => $this->obtenerCitasPorEstado(),
                'topPacientes' => $this->obtenerTopPacientes(5)
            ];

            // Información del usuario
            $usuarioNombre = $_SESSION['nombre'] ?? 'Administrador';
            $usuarioEmail = $_SESSION['email'] ?? '';

            // Generar el PDF directamente aquí
            $pdf = new GeneradorPDFConfiguracion();
            $pdf->generarPDF($datos, $usuarioNombre, $usuarioEmail);
        } catch (Exception $e) {
            error_log("Error al exportar reporte PDF: " . $e->getMessage());
            $_SESSION['error'] = 'Error al generar PDF: ' . $e->getMessage();
            header("Location: ?accion=reportes");
            exit;
        }
    }

    /**
     * 📊 Obtiene el resumen de citas por estado
     */
    private function obtenerCitasPorEstado(): array
    {
        $pdo = BaseDatos::pdo();

        $sql = "
            SELECT 
                estado,
                COUNT(*) as cantidad
            FROM citas
            GROUP BY estado
            ORDER BY cantidad DESC
        ";

        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 👥 Obtiene los pacientes con más citas
     */
    private function obtenerTopPacientes(int $limite = 5): array
    {
        $pdo = BaseDatos::pdo();

        $sql = "
            SELECT 
                u.nombre,
                COUNT(c.id) as total_citas,
                COALESCE(SUM(f.monto), 0) as total_gastado
            FROM usuarios u
            INNER JOIN pacientes p ON u.id = p.usuario_id
            LEFT JOIN citas c ON p.id = c.paciente_id
            LEFT JOIN facturas f ON c.id = f.cita_id
            GROUP BY u.id, u.nombre
            ORDER BY total_citas DESC
            LIMIT :limite
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 📑 Exporta reportes a Excel (CSV)
     */

   /**
 * 📑 Exporta reportes a Excel con formato profesional
 */
public function exportarExcel() {
    try {
        Autenticacion::requiereRoles(['admin']);

        // Obtener todos los datos necesarios
        $facturas = Factura::todos();
        $totalPacientes = Paciente::contarPacientes();
        $totalCitas = Cita::contarCitas();
        $totalFacturas = Factura::contarFacturas();
        $totalRecaudado = Factura::sumarRecaudado();

        // Crear el objeto Spreadsheet
        $spreadsheet = new Spreadsheet();
        
        // ===================================
        // 📊 HOJA 1: RESUMEN EJECUTIVO
        // ===================================
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Resumen Ejecutivo');

        // Logo/Título principal
        $sheet1->mergeCells('A1:G1');
        $sheet1->setCellValue('A1', '🏥 CLÍNICA SALUD TOTAL');
        $sheet1->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 20, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $sheet1->getRowDimension(1)->setRowHeight(35);

        // Subtítulo
        $sheet1->mergeCells('A2:G2');
        $sheet1->setCellValue('A2', 'Reporte Financiero y Estadísticas - ' . date('F Y'));
        $sheet1->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 12, 'color' => ['rgb' => '7F8C8D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Información del reporte
        $sheet1->setCellValue('A4', 'Fecha de Generación:');
        $sheet1->setCellValue('B4', date('d/m/Y H:i:s'));
        $sheet1->setCellValue('A5', 'Generado por:');
        $sheet1->setCellValue('B5', $_SESSION['nombre'] ?? 'Administrador');
        $sheet1->getStyle('A4:A5')->getFont()->setBold(true);

        // KPIs Principales
        $sheet1->mergeCells('A7:G7');
        $sheet1->setCellValue('A7', '📈 INDICADORES CLAVE (KPIs)');
        $sheet1->getStyle('A7')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3498DB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Tabla de KPIs
        $kpis = [
            ['Métrica', 'Valor', 'Icono'],
            ['Total Pacientes', $totalPacientes, '👥'],
            ['Total Citas', $totalCitas, '📅'],
            ['Total Facturas', $totalFacturas, '🧾'],
            ['Ingresos Totales', 'S/. ' . number_format($totalRecaudado, 2), '💰']
        ];

        $sheet1->fromArray($kpis, null, 'B9');
        
        // Estilo para encabezados de KPIs
        $sheet1->getStyle('B9:D9')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '27AE60']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Estilo para datos de KPIs
        $sheet1->getStyle('B10:D13')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Colorear filas alternadas
        for ($i = 10; $i <= 13; $i++) {
            if ($i % 2 == 0) {
                $sheet1->getStyle("B{$i}:D{$i}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('ECF0F1');
            }
        }

        // Ajustar anchos de columnas
        $sheet1->getColumnDimension('A')->setWidth(5);
        $sheet1->getColumnDimension('B')->setWidth(25);
        $sheet1->getColumnDimension('C')->setWidth(20);
        $sheet1->getColumnDimension('D')->setWidth(10);

        // ===================================
        // 📋 HOJA 2: DETALLE DE FACTURAS
        // ===================================
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Facturas Detalladas');

        // Título
        $sheet2->mergeCells('A1:H1');
        $sheet2->setCellValue('A1', '📋 REGISTRO COMPLETO DE FACTURAS');
        $sheet2->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '8E44AD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $sheet2->getRowDimension(1)->setRowHeight(30);

        // Encabezados
        $headers = ['ID', 'Paciente', 'Fecha Emisión', 'Monto (S/.)', 'Estado', 'Método de Pago', 'Cita ID', 'Descripción'];
        $sheet2->fromArray($headers, null, 'A3');

        // Estilo de encabezados
        $sheet2->getStyle('A3:H3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '34495E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2C3E50']]]
        ]);
        $sheet2->getRowDimension(3)->setRowHeight(25);

        // Agregar datos con formato condicional
        $row = 4;
        $totalGeneral = 0;
        
        foreach ($facturas as $factura) {
            $sheet2->setCellValue("A{$row}", $factura['id']);
            $sheet2->setCellValue("B{$row}", $factura['paciente_nombre'] ?? 'N/A');
            $sheet2->setCellValue("C{$row}", $factura['fecha_emision'] ?? date('Y-m-d'));
            $sheet2->setCellValue("D{$row}", $factura['monto']);
            $sheet2->setCellValue("E{$row}", $factura['estado']);
            $sheet2->setCellValue("F{$row}", $factura['metodo_pago'] ?? 'Efectivo');
            $sheet2->setCellValue("G{$row}", $factura['cita_id'] ?? 'N/A');
            $sheet2->setCellValue("H{$row}", $factura['descripcion'] ?? '');

            // Formato condicional por estado
            $estado = strtolower($factura['estado']);
            if (strpos($estado, 'pag') !== false) {
                $color = '27AE60'; // Verde
            } elseif (strpos($estado, 'pend') !== false) {
                $color = 'F39C12'; // Naranja
            } else {
                $color = 'E74C3C'; // Rojo
            }
            
            $sheet2->getStyle("E{$row}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);

            // Bordes y alineación
            $sheet2->getStyle("A{$row}:H{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BDC3C7']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ]);

            // Alternar colores de fila
            if ($row % 2 == 0) {
                $sheet2->getStyle("A{$row}:H{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8F9FA');
            }

            $totalGeneral += $factura['monto'];
            $row++;
        }

        $lastRow = $row - 1;

        // Fila de totales
        $sheet2->mergeCells("A{$row}:C{$row}");
        $sheet2->setCellValue("A{$row}", 'TOTAL GENERAL');
        $sheet2->setCellValue("D{$row}", $totalGeneral);
        $sheet2->getStyle("A{$row}:H{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THICK]]
        ]);

        // Formato de números
        $sheet2->getStyle("D4:D{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet2->getStyle("D{$row}")->getNumberFormat()->setFormatCode('"S/. "#,##0.00');
        $sheet2->getStyle("C4:C{$lastRow}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');

        // Autoajustar columnas
        foreach (range('A', 'H') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        // Filtros automáticos
        $sheet2->setAutoFilter("A3:H{$lastRow}");

        // Inmovilizar paneles (freeze panes)
        $sheet2->freezePane('A4');

        // ===================================
        // 📊 HOJA 3: ESTADÍSTICAS
        // ===================================
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Estadísticas');

        // Título
        $sheet3->mergeCells('A1:E1');
        $sheet3->setCellValue('A1', '📊 ANÁLISIS ESTADÍSTICO');
        $sheet3->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E67E22']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        $sheet3->getRowDimension(1)->setRowHeight(30);

        // Distribución por estado
        $distribucion = $this->obtenerDistribucionEstados();
        $sheet3->setCellValue('A3', 'Distribución por Estado');
        $sheet3->getStyle('A3')->getFont()->setBold(true)->setSize(12);
        
        $estadisticasData = [
            ['Estado', 'Cantidad', 'Porcentaje'],
        ];
        
        $totalFacturasEstado = array_sum($distribucion);
        foreach ($distribucion as $estado => $cantidad) {
            $porcentaje = $totalFacturasEstado > 0 ? ($cantidad / $totalFacturasEstado) * 100 : 0;
            $estadisticasData[] = [$estado, $cantidad, number_format($porcentaje, 2) . '%'];
        }
        
        $sheet3->fromArray($estadisticasData, null, 'A4');
        
        // Estilo para tabla de estadísticas
        $sheet3->getStyle('A4:C4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '16A085']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $lastStatRow = 4 + count($estadisticasData) - 1;
        $sheet3->getStyle("A5:C{$lastStatRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Autoajustar columnas
        foreach (range('A', 'E') as $col) {
            $sheet3->getColumnDimension($col)->setAutoSize(true);
        }

        // ===================================
        // 🎨 CONFIGURACIONES FINALES
        // ===================================
        
        // Proteger hojas (opcional)
        // $sheet1->getProtection()->setSheet(true);
        
        // Propiedades del documento
        $spreadsheet->getProperties()
            ->setCreator('Clínica Salud Total')
            ->setLastModifiedBy($_SESSION['nombre'] ?? 'Sistema')
            ->setTitle('Reporte Financiero')
            ->setSubject('Facturas y Estadísticas')
            ->setDescription('Reporte completo generado automáticamente')
            ->setKeywords('facturas reporte estadísticas clínica')
            ->setCategory('Reportes Financieros');

        // Seleccionar la primera hoja por defecto
        $spreadsheet->setActiveSheetIndex(0);

        // ===================================
        // 💾 EXPORTAR ARCHIVO
        // ===================================
        
        // Limpiar cualquier salida previa
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Headers para descarga
        $filename = 'Reporte_Completo_' . date('Y-m-d_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1'); // IE 9
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        
        // Liberar memoria
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        
        exit;

    } catch (Exception $e) {
        error_log("❌ Error al exportar Excel: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        $_SESSION['error'] = 'Error al generar el reporte Excel: ' . $e->getMessage();
        header("Location: ?accion=reportes");
        exit;
    }
}
}

