<?php

require_once "utils/lib/mpdf/vendor/autoload.php";

use Mpdf\Mpdf;

class ReportesCredigoController extends Controller
{
    /**
     * Muestra la vista principal de reportes CrediGo
     */
   
    /**
     * Obtiene datos para las gráficas vía AJAX
     */
    public function obtenerDatosGraficas()
    {
        try {
            $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-01-01');
            $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
            $grupo = $_GET['grupo'] ?? '';
            
            $conexion = (new Conexion())->getConexion();
            
            // Obtener datos para cada gráfica
            $ventasAnuales = $this->obtenerVentasAnuales($conexion, $fecha_inicio, $fecha_fin, $grupo);
            $ventasCategoria = $this->obtenerVentasPorCategoria($conexion, $fecha_inicio, $fecha_fin, $grupo);
            $tiempoEntrega = $this->obtenerTiempoEntrega($conexion, $fecha_inicio, $fecha_fin);
            
            // Calcular resumen
            $resumen = $this->obtenerResumen($conexion, $fecha_inicio, $fecha_fin, $grupo);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'ventasAnuales' => $ventasAnuales,
                'ventasCategoria' => $ventasCategoria,
                'tiempoEntrega' => $tiempoEntrega,
                'resumen' => $resumen
            ]);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener datos: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Obtiene ventas anuales mensualizadas
     */
    private function obtenerVentasAnuales($conexion, $fecha_inicio, $fecha_fin, $grupo = '')
    {
        $grupoCondition = $grupo ? "AND f.id_grupo_financiamiento = '$grupo'" : "";
        
        $sql = "
            SELECT 
                DATE_FORMAT(f.fecha_creacion, '%Y-%m') as mes,
                SUM(f.monto_total) as total,
                COUNT(f.idfinanciamiento) as cantidad
            FROM financiamiento f
            WHERE f.fecha_creacion BETWEEN '$fecha_inicio' AND '$fecha_fin'
            AND f.estado_eliminado = 0
            AND f.incobrable = 0
            $grupoCondition
            GROUP BY mes
            ORDER BY mes ASC
        ";
        
        $result = $conexion->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    /**
     * Obtiene ventas por categoría de producto
     */
    private function obtenerVentasPorCategoria($conexion, $fecha_inicio, $fecha_fin, $grupo = '')
    {
        $grupoCondition = $grupo ? "AND f.id_grupo_financiamiento = '$grupo'" : "";
        
        $sql = "
            SELECT 
                COALESCE(p.categoria, 'Sin categoría') as categoria,
                COUNT(f.idfinanciamiento) as cantidad,
                SUM(f.monto_total) as total
            FROM financiamiento f
            INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
            WHERE f.fecha_creacion BETWEEN '$fecha_inicio' AND '$fecha_fin'
            AND f.estado_eliminado = 0
            AND f.incobrable = 0
            $grupoCondition
            GROUP BY p.categoria
            ORDER BY total DESC
        ";
        
        $result = $conexion->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    /**
     * Obtiene duración de financiamientos (diferencia entre fecha_fin y fecha_inicio)
     */
    private function obtenerTiempoEntrega($conexion, $fecha_inicio, $fecha_fin)
    {
        $sql = "
            SELECT 
                DATEDIFF(fecha_fin, fecha_inicio) as dias_duracion,
                COUNT(*) as cantidad
            FROM financiamiento
            WHERE fecha_creacion BETWEEN '$fecha_inicio' AND '$fecha_fin'
            AND estado_eliminado = 0
            AND incobrable = 0
            GROUP BY dias_duracion
            ORDER BY dias_duracion ASC
            LIMIT 10
        ";
        
        $result = $conexion->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    /**
     * Obtiene resumen general para el período
     */
    private function obtenerResumen($conexion, $fecha_inicio, $fecha_fin, $grupo = '')
    {
        $grupoCondition = $grupo ? "AND f.id_grupo_financiamiento = '$grupo'" : "";
        
        $sql = "
            SELECT 
                SUM(f.monto_total) as total_ventas,
                COUNT(f.idfinanciamiento) as total_financiamientos,
                COUNT(CASE WHEN f.estado = 'Activo' THEN 1 END) as financiamientos_activos
            FROM financiamiento f
            WHERE f.fecha_creacion BETWEEN '$fecha_inicio' AND '$fecha_fin'
            AND f.estado_eliminado = 0
            AND f.incobrable = 0
            $grupoCondition
        ";
        
        $result = $conexion->query($sql);
        $data = $result ? $result->fetch_assoc() : [];
        
        // Calcular ganancias
        $sqlGanancias = "
            SELECT SUM(pf.monto * 0.1) as ganancias
            FROM pagos_financiamiento pf
            INNER JOIN financiamiento f ON pf.id_financiamiento = f.idfinanciamiento
            WHERE pf.fecha_pago BETWEEN '$fecha_inicio' AND '$fecha_fin'
            AND f.estado_eliminado = 0
            AND f.incobrable = 0
            $grupoCondition
        ";
        
        $resultGanancias = $conexion->query($sqlGanancias);
        $ganancias = $resultGanancias ? $resultGanancias->fetch_assoc() : [];
        
        return [
            'total_ventas' => $data['total_ventas'] ?? 0,
            'total_ganancias' => $ganancias['ganancias'] ?? 0,
            'financiamientos_activos' => $data['financiamientos_activos'] ?? 0
        ];
    }
    
    /**
     * Exporta el reporte a PDF usando mPDF
     */
    public function exportarPDF()
    {
        try {
            $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-01-01');
            $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
            $grupo = $_GET['grupo'] ?? '';
            
            $conexion = (new Conexion())->getConexion();
            
            // Obtener datos
            $ventasAnuales = $this->obtenerVentasAnuales($conexion, $fecha_inicio, $fecha_fin, $grupo);
            $ventasCategoria = $this->obtenerVentasPorCategoria($conexion, $fecha_inicio, $fecha_fin, $grupo);
            $resumen = $this->obtenerResumen($conexion, $fecha_inicio, $fecha_fin, $grupo);
            
            // Generar HTML del reporte (sin el botón de imprimir)
            $html = $this->generarHTMLReporte($ventasAnuales, $ventasCategoria, $resumen, $fecha_inicio, $fecha_fin, true);
            
            // Crear instancia de mPDF
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 15,
                'margin_bottom' => 15,
            ]);
            
            // Escribir HTML al PDF
            $mpdf->WriteHTML($html);
            
            // Descargar el PDF
            $mpdf->Output('Reporte_CrediGo_' . date('Y-m-d') . '.pdf', 'D');
            
        } catch (Exception $e) {
            // Si falla, mostrar error
            header('Content-Type: text/html; charset=utf-8');
            echo '<h1>Error al generar PDF</h1>';
            echo '<p>' . $e->getMessage() . '</p>';
            echo '<p><a href="javascript:history.back()">Volver</a></p>';
        }
    }
    
    /**
     * Genera el HTML del reporte para imprimir
     */
    private function generarHTMLReporte($ventasAnuales, $ventasCategoria, $resumen, $fecha_inicio, $fecha_fin, $paraPDF = false)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Reporte CrediGo</title>
            <style>
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #7852a2; text-align: center; margin-bottom: 10px; }
                h2 { color: #333; border-bottom: 2px solid #7852a2; padding-bottom: 5px; margin-top: 30px; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #7852a2; color: white; }
                tr:nth-child(even) { background-color: #f8f9fa; }
                .resumen { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #ddd; }
                .resumen-item { display: inline-block; margin: 10px 20px; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; border-top: 1px solid #ddd; padding-top: 10px; }
                .btn-print { 
                    position: fixed; 
                    top: 20px; 
                    right: 20px; 
                    padding: 10px 20px; 
                    background-color: #7852a2; 
                    color: white; 
                    border: none; 
                    border-radius: 5px; 
                    cursor: pointer;
                    font-size: 14px;
                }
                .btn-print:hover { background-color: #5e3d82; }
            </style>
        </head>
        <body>';
        
        if (!$paraPDF) {
            $html .= '<button class="btn-print no-print" onclick="window.print()">🖨️ Imprimir / Guardar como PDF</button>';
        }
        
        $html .= '
            <h1>Reporte de Analíticas CrediGo</h1>
            <p style="text-align: center; color: #666; margin-bottom: 30px;">
                Período: ' . date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin)) . '
            </p>
            
            <div class="resumen">
                <h2 style="margin-top: 0;">Resumen General</h2>
                <div class="resumen-item">
                    <strong>Total Ventas:</strong> S/ ' . number_format($resumen['total_ventas'], 2, '.', ',') . '
                </div>
                <div class="resumen-item">
                    <strong>Total Ganancias:</strong> S/ ' . number_format($resumen['total_ganancias'], 2, '.', ',') . '
                </div>
                <div class="resumen-item">
                    <strong>Financiamientos Activos:</strong> ' . $resumen['financiamientos_activos'] . '
                </div>
            </div>';
        
        if (!empty($ventasAnuales)) {
            $html .= '
            <h2>Ventas Mensuales</h2>
            <table>
                <thead>
                    <tr>
                        <th>Mes</th>
                        <th style="text-align: right;">Total Ventas</th>
                        <th style="text-align: center;">Cantidad</th>
                    </tr>
                </thead>
                <tbody>';
            
            foreach ($ventasAnuales as $venta) {
                $html .= '<tr>
                    <td>' . $venta['mes'] . '</td>
                    <td style="text-align: right;">S/ ' . number_format($venta['total'], 2, '.', ',') . '</td>
                    <td style="text-align: center;">' . $venta['cantidad'] . '</td>
                </tr>';
            }
            
            $html .= '</tbody>
            </table>';
        }
        
        if (!empty($ventasCategoria)) {
            $html .= '
            <h2>Ventas por Categoría</h2>
            <table>
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th style="text-align: center;">Cantidad</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>';
            
            foreach ($ventasCategoria as $categoria) {
                $html .= '<tr>
                    <td>' . $categoria['categoria'] . '</td>
                    <td style="text-align: center;">' . $categoria['cantidad'] . '</td>
                    <td style="text-align: right;">S/ ' . number_format($categoria['total'], 2, '.', ',') . '</td>
                </tr>';
            }
            
            $html .= '</tbody>
            </table>';
        }
        
        $html .= '
            <div class="footer">
                <p><strong>Generado el ' . date('d/m/Y H:i:s') . '</strong></p>
                <p>AREQUIPAGO ERP - Sistema de Gestión CrediGo</p>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}
