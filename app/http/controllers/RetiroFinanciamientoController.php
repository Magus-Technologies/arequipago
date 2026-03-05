<?php

require_once 'app/models/Financiamiento.php';
require_once 'app/models/Comision.php';

class RetiroFinanciamientoController extends Controller
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    /**
     * Calcular penalidad según tabla de retiro
     * Solo aplica para planes 19 (CrediGo auto Grupo 3) y 38 (CrediGo Autos Grupo 4)
     */
    public function calcularPenalidad()
    {
        try {
            $idFinanciamiento = $_POST['id_financiamiento'] ?? 0;

            if (!$idFinanciamiento) {
                echo json_encode(['success' => false, 'message' => 'ID de financiamiento no proporcionado']);
                return;
            }

            // Obtener datos del financiamiento
            $query = "SELECT f.*, 
                             TIMESTAMPDIFF(MONTH, f.fecha_inicio, CURDATE()) as meses_permanencia,
                             p.nombre_plan,
                             CASE 
                                WHEN f.id_conductor IS NOT NULL THEN CONCAT(cond.nombres, ' ', cond.apellido_paterno, ' ', cond.apellido_materno)
                                WHEN f.id_cliente IS NOT NULL THEN CONCAT(clf.nombres, ' ', clf.apellido_paterno, ' ', clf.apellido_materno)
                                ELSE 'Sin nombre'
                             END as nombre_conductor,
                             CASE 
                                WHEN f.id_conductor IS NOT NULL THEN cond.nro_documento
                                WHEN f.id_cliente IS NOT NULL THEN clf.n_documento
                                ELSE 'Sin documento'
                             END as nro_documento
                      FROM financiamiento f
                      LEFT JOIN conductores cond ON f.id_conductor = cond.id_conductor
                      LEFT JOIN clientes_financiar clf ON f.id_cliente = clf.id
                      LEFT JOIN planes_financiamiento p ON f.grupo_financiamiento = p.idplan_financiamiento
                      WHERE f.idfinanciamiento = ?";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param('i', $idFinanciamiento);
            $stmt->execute();
            $financiamiento = $stmt->get_result()->fetch_assoc();

            if (!$financiamiento) {
                echo json_encode(['success' => false, 'message' => 'Financiamiento no encontrado']);
                return;
            }

            // Verificar que sea plan 19 o 38
            $grupoFinanciamiento = intval($financiamiento['grupo_financiamiento']);
            if ($grupoFinanciamiento != 19 && $grupoFinanciamiento != 38) {
                echo json_encode(['success' => false, 'message' => 'Este plan no permite retiros con penalidad']);
                return;
            }

            // Verificar que no esté ya retirado
            if ($financiamiento['estado_retiro'] == 'retirado') {
                echo json_encode(['success' => false, 'message' => 'Este financiamiento ya fue retirado']);
                return;
            }

            $mesesPermanencia = intval($financiamiento['meses_permanencia']);
            $montoTotal = floatval($financiamiento['monto_total']);

            // Calcular penalidad según tabla
            $penalidad = $this->obtenerPenalidad($mesesPermanencia, $montoTotal);

            // Obtener comisión del asesor si existe
            $comisionAsesor = $this->obtenerComisionAsesor($idFinanciamiento);

            echo json_encode([
                'success' => true,
                'data' => [
                    'id_financiamiento' => $idFinanciamiento,
                    'nombre_conductor' => $financiamiento['nombre_conductor'],
                    'nro_documento' => $financiamiento['nro_documento'],
                    'nombre_plan' => $financiamiento['nombre_plan'],
                    'monto_total' => number_format($montoTotal, 2),
                    'fecha_inicio' => $financiamiento['fecha_inicio'],
                    'meses_permanencia' => $mesesPermanencia,
                    'penalidad' => number_format($penalidad, 2),
                    'moneda' => $financiamiento['moneda'] ?? '$',
                    'comision_asesor' => $comisionAsesor
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Obtener penalidad según tabla de retiro
     */
    private function obtenerPenalidad($meses, $montoTotal)
    {
        // Determinar rango de certificado
        $rangoCertificado = 0; // 0 = $13k, 1 = $15k, 2 = $17k
        
        if ($montoTotal >= 17000) {
            $rangoCertificado = 2; // $17k
        } elseif ($montoTotal >= 15000) {
            $rangoCertificado = 1; // $15k
        } else {
            $rangoCertificado = 0; // $13k
        }

        // Tabla de penalidades
        $tablaPenalidades = [
            // 2-4 meses
            [250, 350, 450],
            // 5-8 meses
            [600, 700, 800],
            // 9+ meses
            [850, 950, 1050]
        ];

        // Determinar rango de meses
        if ($meses >= 2 && $meses <= 4) {
            return $tablaPenalidades[0][$rangoCertificado];
        } elseif ($meses >= 5 && $meses <= 8) {
            return $tablaPenalidades[1][$rangoCertificado];
        } elseif ($meses >= 9) {
            return $tablaPenalidades[2][$rangoCertificado];
        } else {
            // Menos de 2 meses, sin penalidad
            return 0;
        }
    }

    /**
     * Obtener comisión del asesor relacionada al financiamiento
     */
    private function obtenerComisionAsesor($idFinanciamiento)
    {
        $query = "SELECT c.*, 
                         COALESCE(CONCAT(u.nombres, ' ', u.apellidos), u.usuario, 'Usuario desconocido') as nombre_asesor
                  FROM comisiones c
                  LEFT JOIN usuarios u ON c.usuario_id = u.usuario_id
                  WHERE c.tipo_comision = 'financiamiento' 
                  AND c.referencia_id = ?
                  ORDER BY c.fecha_comision DESC
                  LIMIT 1";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param('i', $idFinanciamiento);
        $stmt->execute();
        $comision = $stmt->get_result()->fetch_assoc();

        if ($comision) {
            return [
                'existe' => true,
                'id_comision' => $comision['id_comision'],
                'usuario_id' => $comision['usuario_id'],
                'nombre_asesor' => $comision['nombre_asesor'] ?? 'Usuario desconocido',
                'monto_comision' => floatval($comision['monto_comision']),
                'estado_comision' => $comision['estado_comision'],
                'moneda' => $comision['moneda'] ?? 'S/.'
            ];
        }

        return ['existe' => false];
    }

    /**
     * Procesar retiro del financiamiento
     */
    public function procesarRetiro()
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $idFinanciamiento = $_POST['id_financiamiento'] ?? 0;
            $observaciones = $_POST['observaciones'] ?? '';
            $usuarioId = $_SESSION['usuario_id'] ?? null;

            if (!$idFinanciamiento) {
                echo json_encode(['success' => false, 'message' => 'ID de financiamiento no proporcionado']);
                return;
            }

            if (!$usuarioId) {
                echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
                return;
            }

            // Iniciar transacción
            $this->conexion->begin_transaction();

            // Obtener datos del financiamiento
            $query = "SELECT f.*, 
                             TIMESTAMPDIFF(MONTH, f.fecha_inicio, CURDATE()) as meses_permanencia
                      FROM financiamiento f
                      WHERE f.idfinanciamiento = ?";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param('i', $idFinanciamiento);
            $stmt->execute();
            $financiamiento = $stmt->get_result()->fetch_assoc();

            if (!$financiamiento) {
                $this->conexion->rollback();
                echo json_encode(['success' => false, 'message' => 'Financiamiento no encontrado']);
                return;
            }

            // Verificar que sea plan 19 o 38
            $grupoFinanciamiento = intval($financiamiento['grupo_financiamiento']);
            if ($grupoFinanciamiento != 19 && $grupoFinanciamiento != 38) {
                $this->conexion->rollback();
                echo json_encode(['success' => false, 'message' => 'Este plan no permite retiros con penalidad']);
                return;
            }

            // Verificar que no esté ya retirado
            if ($financiamiento['estado_retiro'] == 'retirado') {
                $this->conexion->rollback();
                echo json_encode(['success' => false, 'message' => 'Este financiamiento ya fue retirado']);
                return;
            }

            $mesesPermanencia = intval($financiamiento['meses_permanencia']);
            $montoTotal = floatval($financiamiento['monto_total']);
            $penalidad = $this->obtenerPenalidad($mesesPermanencia, $montoTotal);

            // Actualizar financiamiento
            $queryUpdate = "UPDATE financiamiento 
                           SET estado_retiro = 'retirado',
                               fecha_retiro = NOW(),
                               monto_penalidad = ?,
                               meses_permanencia = ?,
                               usuario_proceso_retiro = ?,
                               observaciones_retiro = ?,
                               estado = 'Retirado'
                           WHERE idfinanciamiento = ?";
            
            $stmtUpdate = $this->conexion->prepare($queryUpdate);
            $stmtUpdate->bind_param('diisi', $penalidad, $mesesPermanencia, $usuarioId, $observaciones, $idFinanciamiento);
            $stmtUpdate->execute();

            // Cancelar comisión del asesor si existe y está pendiente
            $queryComision = "UPDATE comisiones 
                             SET estado_comision = 'cancelada',
                                 observaciones = CONCAT(IFNULL(observaciones, ''), ' | Cancelada por retiro de financiamiento')
                             WHERE tipo_comision = 'financiamiento' 
                             AND referencia_id = ?
                             AND estado_comision = 'pendiente'";
            
            $stmtComision = $this->conexion->prepare($queryComision);
            $stmtComision->bind_param('i', $idFinanciamiento);
            $stmtComision->execute();

            // Si la comisión ya fue pagada, crear un registro de descuento
            $queryComisionPagada = "SELECT * FROM comisiones 
                                   WHERE tipo_comision = 'financiamiento' 
                                   AND referencia_id = ?
                                   AND estado_comision = 'pagada'";
            
            $stmtComisionPagada = $this->conexion->prepare($queryComisionPagada);
            $stmtComisionPagada->bind_param('i', $idFinanciamiento);
            $stmtComisionPagada->execute();
            $comisionPagada = $stmtComisionPagada->get_result()->fetch_assoc();

            if ($comisionPagada) {
                // Crear registro de descuento (comisión negativa)
                $montoDescuento = -abs(floatval($comisionPagada['monto_comision']));
                $observacionDescuento = "Descuento por retiro de financiamiento ID: {$idFinanciamiento}";
                
                $queryDescuento = "INSERT INTO comisiones 
                                  (usuario_id, tipo_comision, referencia_id, monto_comision, 
                                   fecha_comision, estado_comision, observaciones, moneda)
                                  VALUES (?, 'financiamiento', ?, ?, NOW(), 'pendiente', ?, ?)";
                
                $stmtDescuento = $this->conexion->prepare($queryDescuento);
                $stmtDescuento->bind_param('iidss', 
                    $comisionPagada['usuario_id'], 
                    $idFinanciamiento, 
                    $montoDescuento, 
                    $observacionDescuento,
                    $comisionPagada['moneda']
                );
                $stmtDescuento->execute();
            }

            // Commit de la transacción
            $this->conexion->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Retiro procesado correctamente',
                'data' => [
                    'penalidad' => number_format($penalidad, 2),
                    'meses_permanencia' => $mesesPermanencia,
                    'comision_descontada' => $comisionPagada ? true : false
                ]
            ]);

        } catch (Exception $e) {
            $this->conexion->rollback();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Obtener lista de financiamientos retirados
     */
    public function obtenerFinanciamientosRetirados()
    {
        try {
            $query = "SELECT f.*,
                             CASE 
                                WHEN f.id_conductor IS NOT NULL THEN CONCAT(cond.nombres, ' ', cond.apellido_paterno, ' ', cond.apellido_materno)
                                WHEN f.id_cliente IS NOT NULL THEN CONCAT(clf.nombres, ' ', clf.apellido_paterno, ' ', clf.apellido_materno)
                                ELSE 'Sin nombre'
                             END as nombre_conductor,
                             CASE 
                                WHEN f.id_conductor IS NOT NULL THEN cond.nro_documento
                                WHEN f.id_cliente IS NOT NULL THEN clf.n_documento
                                ELSE 'Sin documento'
                             END as nro_documento,
                             p.nombre_plan,
                             CONCAT(u.nombres, ' ', u.apellidos) as usuario_proceso
                      FROM financiamiento f
                      LEFT JOIN conductores cond ON f.id_conductor = cond.id_conductor
                      LEFT JOIN clientes_financiar clf ON f.id_cliente = clf.id
                      LEFT JOIN planes_financiamiento p ON f.grupo_financiamiento = p.idplan_financiamiento
                      LEFT JOIN usuarios u ON f.usuario_proceso_retiro = u.usuario_id
                      WHERE f.estado_retiro = 'retirado'
                      ORDER BY f.fecha_retiro DESC";
            
            $result = $this->conexion->query($query);
            $retirados = [];

            while ($row = $result->fetch_assoc()) {
                $retirados[] = $row;
            }

            echo json_encode(['success' => true, 'data' => $retirados]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
