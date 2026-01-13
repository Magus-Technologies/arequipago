<?php

require_once "app/models/PagoCajaArequipa.php";

class RecaudacionesController extends Controller
{
    private $pagoCajaArequipaModel;

    public function __construct()
    {
        $this->pagoCajaArequipaModel = new PagoCajaArequipa();
    }

    /**
     * Obtener todas las recaudaciones con filtros
     */
    public function listarRecaudaciones()
    {
        try {
            $filtros = [];

            // Obtener filtros del request
            if (isset($_POST['fecha_inicio']) && !empty($_POST['fecha_inicio'])) {
                $filtros['fecha_inicio'] = $_POST['fecha_inicio'];
            }

            if (isset($_POST['fecha_fin']) && !empty($_POST['fecha_fin'])) {
                $filtros['fecha_fin'] = $_POST['fecha_fin'];
            }

            if (isset($_POST['estado']) && !empty($_POST['estado'])) {
                $filtros['estado'] = $_POST['estado'];
            }

            if (isset($_POST['moneda']) && !empty($_POST['moneda'])) {
                $filtros['moneda'] = $_POST['moneda'];
            }

            if (isset($_POST['busqueda']) && !empty($_POST['busqueda'])) {
                $filtros['busqueda'] = $_POST['busqueda'];
            }

            $recaudaciones = $this->pagoCajaArequipaModel->obtenerRecaudaciones($filtros);

            echo json_encode([
                'success' => true,
                'data' => $recaudaciones,
                'total' => count($recaudaciones)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener recaudaciones: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener resumen de recaudaciones
     */
    public function obtenerResumen()
    {
        try {
            $filtros = [];

            if (isset($_POST['fecha_inicio']) && !empty($_POST['fecha_inicio'])) {
                $filtros['fecha_inicio'] = $_POST['fecha_inicio'];
            }

            if (isset($_POST['fecha_fin']) && !empty($_POST['fecha_fin'])) {
                $filtros['fecha_fin'] = $_POST['fecha_fin'];
            }

            if (isset($_POST['estado']) && !empty($_POST['estado'])) {
                $filtros['estado'] = $_POST['estado'];
            }

            $resumen = $this->pagoCajaArequipaModel->obtenerResumenRecaudaciones($filtros);

            echo json_encode([
                'success' => true,
                'data' => $resumen
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener resumen: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener detalle de una recaudación específica
     */
    public function obtenerDetalle()
    {
        try {
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de recaudación requerido'
                ]);
                return;
            }

            $id = $_POST['id'];
            $detalle = $this->pagoCajaArequipaModel->obtenerDetallePorId($id);

            if ($detalle) {
                echo json_encode([
                    'success' => true,
                    'data' => $detalle
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Recaudación no encontrada'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener detalle: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Exportar recaudaciones a Excel
     */
    public function exportarExcel()
    {
        try {
            $filtros = [];

            if (isset($_POST['fecha_inicio']) && !empty($_POST['fecha_inicio'])) {
                $filtros['fecha_inicio'] = $_POST['fecha_inicio'];
            }

            if (isset($_POST['fecha_fin']) && !empty($_POST['fecha_fin'])) {
                $filtros['fecha_fin'] = $_POST['fecha_fin'];
            }

            if (isset($_POST['estado']) && !empty($_POST['estado'])) {
                $filtros['estado'] = $_POST['estado'];
            }

            if (isset($_POST['moneda']) && !empty($_POST['moneda'])) {
                $filtros['moneda'] = $_POST['moneda'];
            }

            $recaudaciones = $this->pagoCajaArequipaModel->obtenerRecaudaciones($filtros);

            // Configurar headers para descarga de Excel
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="recaudaciones_caja_arequipa_' . date('Y-m-d') . '.xls"');
            header('Cache-Control: max-age=0');

            // Crear contenido HTML para Excel
            echo '<table border="1">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Fecha Pago</th>';
            echo '<th>N° Trace</th>';
            echo '<th>Cliente</th>';
            echo '<th>DNI</th>';
            echo '<th>Código Asociado</th>';
            echo '<th>Plan</th>';
            echo '<th>Moneda</th>';
            echo '<th>Monto</th>';
            echo '<th>Comisión</th>';
            echo '<th>Monto Recibido</th>';
            echo '<th>Estado</th>';
            echo '<th>Canal</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($recaudaciones as $rec) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($rec['fecha_pago']) . '</td>';
                echo '<td>' . htmlspecialchars($rec['numero_trace']) . '</td>';
                echo '<td>' . htmlspecialchars($rec['nombre_cliente']) . '</td>';
                echo '<td>' . htmlspecialchars($rec['dni_cliente']) . '</td>';
                echo '<td>' . htmlspecialchars($rec['codigo_asociado']) . '</td>';
                echo '<td>' . htmlspecialchars($rec['nombre_plan']) . '</td>';
                echo '<td>' . htmlspecialchars($rec['moneda']) . '</td>';
                echo '<td>' . number_format($rec['monto'], 2) . '</td>';
                echo '<td>' . number_format($rec['comision_asumida'], 2) . '</td>';
                echo '<td>' . number_format($rec['monto_recibido'], 2) . '</td>';
                echo '<td>' . htmlspecialchars($rec['estado']) . '</td>';
                echo '<td>' . $this->getNombreCanal($rec['canal']) . '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';

            exit;
        } catch (Exception $e) {
            echo "Error al exportar: " . $e->getMessage();
        }
    }

    /**
     * Obtener nombre descriptivo del canal
     */
    private function getNombreCanal($canal)
    {
        $canales = [
            '1' => 'Ventanilla',
            '2' => 'Cajeros',
            '3' => 'Home Banking',
            '4' => 'Corresponsal',
            '5' => 'Débito Automático',
            '6' => 'Banca Móvil'
        ];

        return isset($canales[$canal]) ? $canales[$canal] : 'Desconocido';
    }
}
