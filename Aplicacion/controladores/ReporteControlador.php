<?php
require_once __DIR__ . '/../../nucleo/Autenticacion.php';
require_once __DIR__ . '/../../nucleo/BaseDatos.php';
require_once __DIR__ . '/../modelos/Paciente.php';
require_once __DIR__ . '/../modelos/Cita.php';
require_once __DIR__ . '/../modelos/Factura.php';
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

        try {
            $pdo = BaseDatos::pdo();

            // 📊 Obtener estadísticas usando métodos del modelo
            $totalPacientes = (int) Paciente::contarPacientes();
            $totalCitas = (int) Cita::contarCitas();
            $totalFacturas = (int) Factura::contarFacturas();
            $totalRecaudado = (float) Factura::sumarRecaudado();

            // 📋 Facturas recientes
            $facturasRecientes = $this->obtenerFacturasRecientes(10);

            // 📈 Tendencia mensual
            $tendenciaMensual = $this->obtenerTendenciaMensual();

            // 🥧 Distribución de estados
            $distribucionEstados = $this->obtenerDistribucionEstados();

            // 🔍 Logging para debugging
            error_log("✅ REPORTES CARGADOS:");
            error_log("   - Pacientes: $totalPacientes");
            error_log("   - Citas: $totalCitas");
            error_log("   - Facturas: $totalFacturas");
            error_log("   - Recaudado: S/. $totalRecaudado");
            error_log("   - Facturas Recientes: " . count($facturasRecientes));
            error_log("   - Meses en tendencia: " . count($tendenciaMensual['meses']));
            error_log("   - Estados: " . json_encode($distribucionEstados));

            // ✅ IMPORTANTE: Hacer variables disponibles en el scope de la vista
            // extract() las hace disponibles en layout-admin.php y reportes.php
            extract([
                'totalPacientes' => $totalPacientes,
                'totalCitas' => $totalCitas,
                'totalFacturas' => $totalFacturas,
                'totalRecaudado' => $totalRecaudado,
                'facturasRecientes' => $facturasRecientes,
                'tendenciaMensual' => $tendenciaMensual,
                'distribucionEstados' => $distribucionEstados
            ]);

            $vistaInterna = __DIR__ . '/../vistas/admin/reportes.php';
            require __DIR__ . '/../../includes/layout-admin.php';

        } catch (Exception $e) {
            error_log("❌ Error en ReporteControlador::index: " . $e->getMessage());
            error_log("Stack: " . $e->getTraceAsString());
            $_SESSION['error'] = 'Error al cargar reportes: ' . $e->getMessage();
            header("Location: ?accion=dashboardAdmin");
            exit;
        }
    }

    /**
     * 📋 Obtiene facturas recientes con información completa
     */
    private function obtenerFacturasRecientes(int $limite = 10): array
    {
        try {
            $pdo = BaseDatos::pdo();

            $sql = "
                SELECT 
                    f.id,
                    f.monto,
                    f.estado,
                    f.emitida_en,
                    CAST(f.emitida_en AS DATE) as fecha_emision,
                    COALESCE(u.nombre, 'Sin paciente') as paciente
                FROM facturas f
                LEFT JOIN pacientes p ON f.paciente_id = p.id
                LEFT JOIN usuarios u ON p.usuario_id = u.id
                ORDER BY f.emitida_en DESC
                LIMIT :limite
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            error_log("   ↳ obtenerFacturasRecientes: " . count($resultado) . " facturas");
            return $resultado;

        } catch (PDOException $e) {
            error_log("❌ Error en obtenerFacturasRecientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * 📊 Obtiene ingresos mensuales de los últimos 6 meses
     */
    private function obtenerTendenciaMensual(): array
    {
        try {
            $pdo = BaseDatos::pdo();
            $meses = [];
            $montos = [];

            for ($i = 5; $i >= 0; $i--) {
                $fecha = date('Y-m', strtotime("-$i months"));
                $mesNum = date('n', strtotime("-$i months"));
                $mesNombre = $this->nombreMesEspanol((int)$mesNum);
                $meses[] = $mesNombre;

                // Consultar monto para este mes
                $sql = "
                    SELECT COALESCE(SUM(monto), 0) as total
                    FROM facturas
                    WHERE CAST(emitida_en AS VARCHAR(7)) = :mes
                    AND LOWER(estado) IN ('pagada', 'completada', 'pagado', 'completado')
                ";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([':mes' => $fecha]);
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                $montos[] = (float) ($resultado['total'] ?? 0);
            }

            error_log("   ↳ obtenerTendenciaMensual: " . count($meses) . " meses");
            return [
                'meses' => $meses,
                'montos' => $montos
            ];

        } catch (Exception $e) {
            error_log("❌ Error en obtenerTendenciaMensual: " . $e->getMessage());
            return ['meses' => [], 'montos' => []];
        }
    }

    /**
     * 🥧 Obtiene la distribución de facturas por estado
     */
    private function obtenerDistribucionEstados(): array
    {
        try {
            $pdo = BaseDatos::pdo();

            $sql = "
                SELECT 
                    LOWER(TRIM(estado)) as estado_lower,
                    COUNT(*) as cantidad
                FROM facturas
                GROUP BY LOWER(TRIM(estado))
            ";

            $stmt = $pdo->query($sql);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $distribucion = [
                'Pagada' => 0,
                'Pendiente' => 0,
                'Cancelada' => 0
            ];

            foreach ($resultados as $resultado) {
                $estadoLower = $resultado['estado_lower'];
                $cantidad = (int) $resultado['cantidad'];

                if (strpos($estadoLower, 'pag') !== false || strpos($estadoLower, 'complet') !== false) {
                    $distribucion['Pagada'] += $cantidad;
                } elseif (strpos($estadoLower, 'pend') !== false) {
                    $distribucion['Pendiente'] += $cantidad;
                } else {
                    $distribucion['Cancelada'] += $cantidad;
                }
            }

            error_log("   ↳ Distribución: Pagadas=" . $distribucion['Pagada'] . ", Pendientes=" . $distribucion['Pendiente'] . ", Canceladas=" . $distribucion['Cancelada']);
            return $distribucion;

        } catch (Exception $e) {
            error_log("❌ Error en obtenerDistribucionEstados: " . $e->getMessage());
            return ['Pagada' => 0, 'Pendiente' => 0, 'Cancelada' => 0];
        }
    }

    /**
     * 📅 Convierte número de mes a nombre en español
     */
    private function nombreMesEspanol(int $numeroMes): string
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
            4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
            10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
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

            $datos = [
                'totalPacientes' => Paciente::contarPacientes(),
                'totalCitas' => Cita::contarCitas(),
                'totalFacturas' => Factura::contarFacturas(),
                'totalRecaudado' => Factura::sumarRecaudado(),
                'facturasRecientes' => $this->obtenerFacturasRecientes(10),
                'tendenciaMensual' => $this->obtenerTendenciaMensual(),
                'distribucionEstados' => $this->obtenerDistribucionEstados(),
                'citasPorEstado' => $this->obtenerCitasPorEstado(),
                'topPacientes' => $this->obtenerTopPacientes(5)
            ];

            $usuarioNombre = $_SESSION['nombre'] ?? 'Administrador';
            $usuarioEmail = $_SESSION['email'] ?? '';

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
        try {
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
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        } catch (Exception $e) {
            error_log("❌ Error en obtenerCitasPorEstado: " . $e->getMessage());
            return [];
        }
    }

    /**
     * 👥 Obtiene los pacientes con más citas
     */
    private function obtenerTopPacientes(int $limite = 5): array
    {
        try {
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

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        } catch (Exception $e) {
            error_log("❌ Error en obtenerTopPacientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * 📑 Exporta reportes a Excel con formato profesional
     */
    public function exportarExcel()
    {
        try {
            Autenticacion::requiereRoles(['admin']);

            $pdo = BaseDatos::pdo();
            
            // Obtener facturas
            $sqlFacturas = "
                SELECT 
                    f.id,
                    f.monto,
                    f.estado,
                    CAST(f.emitida_en AS DATE) as fecha_emision,
                    COALESCE(u.nombre, 'Sin paciente') as paciente_nombre,
                    f.cita_id,
                    '' as descripcion,
                    'Efectivo' as metodo_pago
                FROM facturas f
                LEFT JOIN pacientes p ON f.paciente_id = p.id
                LEFT JOIN usuarios u ON p.usuario_id = u.id
                ORDER BY f.emitida_en DESC
            ";
            $facturas = $pdo->query($sqlFacturas)->fetchAll(PDO::FETCH_ASSOC);
            
            $totalPacientes = Paciente::contarPacientes();
            $totalCitas = Cita::contarCitas();
            $totalFacturas = Factura::contarFacturas();
            $totalRecaudado = Factura::sumarRecaudado();

            // Crear Spreadsheet
            $spreadsheet = new Spreadsheet();
            
            // HOJA 1: RESUMEN EJECUTIVO
            $sheet1 = $spreadsheet->getActiveSheet();
            $sheet1->setTitle('Resumen Ejecutivo');

            $sheet1->mergeCells('A1:G1');
            $sheet1->setCellValue('A1', '🏥 CLÍNICA SALUD TOTAL');
            $sheet1->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 20, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C3E50']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
            ]);
            $sheet1->getRowDimension(1)->setRowHeight(35);

            $sheet1->mergeCells('A2:G2');
            $sheet1->setCellValue('A2', 'Reporte Financiero y Estadísticas - ' . date('F Y'));
            $sheet1->getStyle('A2')->applyFromArray([
                'font' => ['italic' => true, 'size' => 12, 'color' => ['rgb' => '7F8C8D']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);

            $sheet1->setCellValue('A4', 'Fecha de Generación:');
            $sheet1->setCellValue('B4', date('d/m/Y H:i:s'));
            $sheet1->setCellValue('A5', 'Generado por:');
            $sheet1->setCellValue('B5', $_SESSION['nombre'] ?? 'Administrador');
            $sheet1->getStyle('A4:A5')->getFont()->setBold(true);

            $sheet1->mergeCells('A7:G7');
            $sheet1->setCellValue('A7', '📈 INDICADORES CLAVE (KPIs)');
            $sheet1->getStyle('A7')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3498DB']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);

            $kpis = [
                ['Métrica', 'Valor', 'Icono'],
                ['Total Pacientes', $totalPacientes, '👥'],
                ['Total Citas', $totalCitas, '📅'],
                ['Total Facturas', $totalFacturas, '🧾'],
                ['Ingresos Totales', 'S/. ' . number_format($totalRecaudado, 2), '💰']
            ];

            $sheet1->fromArray($kpis, null, 'B9');
            
            $sheet1->getStyle('B9:D9')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '27AE60']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            $sheet1->getStyle('B10:D13')->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);

            for ($i = 10; $i <= 13; $i++) {
                if ($i % 2 == 0) {
                    $sheet1->getStyle("B{$i}:D{$i}")->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('ECF0F1');
                }
            }

            foreach (range('A', 'D') as $col) {
                $sheet1->getColumnDimension($col)->setAutoSize(true);
            }

            // HOJA 2: DETALLE DE FACTURAS
            $sheet2 = $spreadsheet->createSheet();
            $sheet2->setTitle('Facturas Detalladas');

            $sheet2->mergeCells('A1:H1');
            $sheet2->setCellValue('A1', '📋 REGISTRO COMPLETO DE FACTURAS');
            $sheet2->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '8E44AD']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
            ]);
            $sheet2->getRowDimension(1)->setRowHeight(30);

            $headers = ['ID', 'Paciente', 'Fecha Emisión', 'Monto (S/.)', 'Estado', 'Método de Pago', 'Cita ID', 'Descripción'];
            $sheet2->fromArray($headers, null, 'A3');

            $sheet2->getStyle('A3:H3')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '34495E']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2C3E50']]]
            ]);
            $sheet2->getRowDimension(3)->setRowHeight(25);

            $row = 4;
            $totalGeneral = 0;
            
            foreach ($facturas as $factura) {
                $sheet2->setCellValue("A{$row}", $factura['id']);
                $sheet2->setCellValue("B{$row}", $factura['paciente_nombre']);
                $sheet2->setCellValue("C{$row}", $factura['fecha_emision']);
                $sheet2->setCellValue("D{$row}", $factura['monto']);
                $sheet2->setCellValue("E{$row}", $factura['estado']);
                $sheet2->setCellValue("F{$row}", $factura['metodo_pago']);
                $sheet2->setCellValue("G{$row}", $factura['cita_id'] ?? 'N/A');
                $sheet2->setCellValue("H{$row}", $factura['descripcion']);

                $estado = strtolower($factura['estado']);
                if (strpos($estado, 'pag') !== false) {
                    $color = '27AE60';
                } elseif (strpos($estado, 'pend') !== false) {
                    $color = 'F39C12';
                } else {
                    $color = 'E74C3C';
                }
                
                $sheet2->getStyle("E{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                $sheet2->getStyle("A{$row}:H{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BDC3C7']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
                ]);

                if ($row % 2 == 0) {
                    $sheet2->getStyle("A{$row}:H{$row}")->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('F8F9FA');
                }

                $totalGeneral += $factura['monto'];
                $row++;
            }

            $lastRow = $row - 1;

            $sheet2->mergeCells("A{$row}:C{$row}");
            $sheet2->setCellValue("A{$row}", 'TOTAL GENERAL');
            $sheet2->setCellValue("D{$row}", $totalGeneral);
            $sheet2->getStyle("A{$row}:H{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C3E50']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THICK]]
            ]);

            $sheet2->getStyle("D4:D{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet2->getStyle("D{$row}")->getNumberFormat()->setFormatCode('"S/. "#,##0.00');
            $sheet2->getStyle("C4:C{$lastRow}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');

            foreach (range('A', 'H') as $col) {
                $sheet2->getColumnDimension($col)->setAutoSize(true);
            }

            $sheet2->setAutoFilter("A3:H{$lastRow}");
            $sheet2->freezePane('A4');

            // HOJA 3: ESTADÍSTICAS
            $sheet3 = $spreadsheet->createSheet();
            $sheet3->setTitle('Estadísticas');

            $sheet3->mergeCells('A1:E1');
            $sheet3->setCellValue('A1', '📊 ANÁLISIS ESTADÍSTICO');
            $sheet3->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E67E22']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
            $sheet3->getRowDimension(1)->setRowHeight(30);

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

            foreach (range('A', 'E') as $col) {
                $sheet3->getColumnDimension($col)->setAutoSize(true);
            }

            // Configurar propiedades del documento
            $spreadsheet->getProperties()
                ->setCreator('Clínica Salud Total')
                ->setLastModifiedBy($_SESSION['nombre'] ?? 'Sistema')
                ->setTitle('Reporte Financiero')
                ->setSubject('Facturas y Estadísticas')
                ->setDescription('Reporte completo generado automáticamente')
                ->setKeywords('facturas reporte estadísticas clínica')
                ->setCategory('Reportes Financieros');

            $spreadsheet->setActiveSheetIndex(0);

            if (ob_get_level()) {
                ob_end_clean();
            }

            $filename = 'Reporte_Completo_' . date('Y-m-d_His') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            
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