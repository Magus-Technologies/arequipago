<?php

require_once 'app/models/Financiamiento.php';
require_once 'app/models/Conductor.php';
require_once 'app/models/Cliente.php';
require_once 'app/models/Productov2.php';

class AprobacionFinanciamientoController extends Controller
{
    private $conexion;
    private $financiamientoModel;
    private $conductorModel;
    private $clienteModel;
    private $productoModel;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->financiamientoModel = new Financiamiento();
        $this->conductorModel = new Conductor();
        $this->clienteModel = new Cliente();
        $this->productoModel = new Productov2();
    }

    // Obtener financiamientos pendientes y rechazados con información completa
    public function obtenerFinanciamientosPendientes()
    {
        try {
            // Obtener financiamientos pendientes (aprobado = 0) y rechazados (aprobado = 2)
            $financiamientos = $this->financiamientoModel->getFinanciamientosPendientesYRechazados();

            $pendientes = [];
            $rechazados = [];

            foreach ($financiamientos as $financiamiento) {
                // Obtener información del cliente o conductor
                if (!empty($financiamiento['id_conductor'])) {
                    $conductor = $this->conductorModel->obtenerDetalleConductor($financiamiento['id_conductor']);
                    $financiamiento['conductor'] = $conductor;
                }

                if (!empty($financiamiento['id_cliente'])) {
                    $cliente = $this->clienteModel->getClienteById($financiamiento['id_cliente']);
                    $financiamiento['cliente'] = $cliente;
                }

                // Obtener información del producto
                if (!empty($financiamiento['idproductosv2'])) {
                    $producto = $this->productoModel->obtenerProductoPorId($financiamiento['idproductosv2']);
                    $financiamiento['producto'] = $producto;
                }

                // Obtener información del usuario que creó el financiamiento
                if (!empty($financiamiento['usuario_id'])) {
                    $nombreUsuario = $this->financiamientoModel->obtenerUsuarioPorId($financiamiento['usuario_id']);
                    $financiamiento['usuario_creador'] = $nombreUsuario;
                }

                // Clasificar según estado
                if ($financiamiento['aprobado'] == 0) {
                    $pendientes[] = $financiamiento;
                } elseif ($financiamiento['aprobado'] == 2) {
                    $rechazados[] = $financiamiento;
                }
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'pendientes' => $pendientes,
                'rechazados' => $rechazados
            ]);

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener financiamientos: ' . $e->getMessage()
            ]);
        }
    }

    // Obtener detalle completo de un financiamiento
    public function obtenerDetalleFinanciamiento()
    {
        try {
            $id = $_POST['id'] ?? 0;

            if (!$id) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
                return;
            }

            // Obtener el financiamiento (sin filtrar por aprobado)
            $financiamiento = $this->financiamientoModel->getFinanciamientoByIdParaAprobacion($id);

            if (!$financiamiento) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Financiamiento no encontrado']);
                return;
            }

            // Obtener información del cliente o conductor
            if (!empty($financiamiento['id_conductor'])) {
                $conductor = $this->conductorModel->obtenerDetalleConductor($financiamiento['id_conductor']);
                $financiamiento['conductor'] = $conductor;
            }

            if (!empty($financiamiento['id_cliente'])) {
                $cliente = $this->clienteModel->getClienteById($financiamiento['id_cliente']);
                $financiamiento['cliente'] = $cliente;
            }

            // Obtener información del producto
            if (!empty($financiamiento['idproductosv2'])) {
                $producto = $this->productoModel->obtenerProductoPorId($financiamiento['idproductosv2']);
                $financiamiento['producto'] = $producto;
            }

            // Obtener información del usuario que creó el financiamiento
            if (!empty($financiamiento['usuario_id'])) {
                $nombreUsuario = $this->financiamientoModel->obtenerUsuarioPorId($financiamiento['usuario_id']);
                $financiamiento['usuario_creador'] = $nombreUsuario;
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $financiamiento
            ]);

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener detalle: ' . $e->getMessage()
            ]);
        }
    }

    // Aprobar financiamiento
    public function aprobarFinanciamiento()
    {
        try {
            $id = $_POST['id'] ?? 0;

            if (!$id) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
                return;
            }

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Obtener el financiamiento (sin filtrar por aprobado)
            $financiamiento = $this->financiamientoModel->getFinanciamientoByIdParaAprobacion($id);

            if (!$financiamiento) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Financiamiento no encontrado']);
                return;
            }

            // Obtener información del producto
            $idProducto = $financiamiento['idproductosv2'];
            $cantidadProducto = $financiamiento['cantidad_producto'];

            // Verificar stock
            $queryProducto = "SELECT cantidad FROM productosv2 WHERE idproductosv2 = ?";
            $stmt = $this->conexion->prepare($queryProducto);
            $stmt->bind_param('i', $idProducto);
            $stmt->execute();
            $producto = $stmt->get_result()->fetch_assoc();

            if (!$producto) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
                return;
            }

            if ($producto['cantidad'] < $cantidadProducto) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Stock insuficiente para aprobar']);
                return;
            }

            // Descontar stock
            $nuevaCantidad = $producto['cantidad'] - $cantidadProducto;
            $queryUpdateStock = "UPDATE productosv2 SET cantidad = ? WHERE idproductosv2 = ?";
            $stmtStock = $this->conexion->prepare($queryUpdateStock);
            $stmtStock->bind_param('ii', $nuevaCantidad, $idProducto);
            $stmtStock->execute();

            // Actualizar estado del financiamiento a aprobado
            $queryUpdate = "UPDATE financiamiento SET aprobado = 1 WHERE idfinanciamiento = ?";
            $stmtUpdate = $this->conexion->prepare($queryUpdate);
            $stmtUpdate->bind_param('i', $id);
            $stmtUpdate->execute();

            // Registrar en log
            $usuario_id = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : null;
            if ($usuario_id) {
                $queryLog = "INSERT INTO log_aprobaciones_financiamiento (id_financiamiento, accion, usuario_id, motivo)
                             VALUES (?, 'aprobado', ?, NULL)";
                $stmtLog = $this->conexion->prepare($queryLog);
                $stmtLog->bind_param('ii', $id, $usuario_id);
                $stmtLog->execute();
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Financiamiento aprobado correctamente']);

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error al aprobar: ' . $e->getMessage()]);
        }
    }

    // Rechazar financiamiento con motivo
    public function rechazarFinanciamiento()
    {
        try {
            $id = $_POST['id'] ?? 0;
            $motivo = $_POST['motivo'] ?? '';

            if (!$id) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
                return;
            }

            if (empty($motivo)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Debe proporcionar un motivo de rechazo']);
                return;
            }

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Actualizar estado a rechazado
            $queryUpdate = "UPDATE financiamiento SET aprobado = 2 WHERE idfinanciamiento = ?";
            $stmt = $this->conexion->prepare($queryUpdate);
            $stmt->bind_param('i', $id);
            $stmt->execute();

            // Registrar en log con motivo
            $usuario_id = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : null;
            if ($usuario_id) {
                $queryLog = "INSERT INTO log_aprobaciones_financiamiento (id_financiamiento, accion, usuario_id, motivo)
                             VALUES (?, 'rechazado', ?, ?)";
                $stmtLog = $this->conexion->prepare($queryLog);
                $stmtLog->bind_param('iis', $id, $usuario_id, $motivo);
                $stmtLog->execute();
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Financiamiento rechazado correctamente']);

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error al rechazar: ' . $e->getMessage()]);
        }
    }

    // Eliminar permanentemente financiamiento
    public function eliminarFinanciamiento()
    {
        try {
            $id = $_POST['id'] ?? 0;
            $motivo = $_POST['motivo'] ?? '';

            if (!$id) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
                return;
            }

            if (empty($motivo)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Debe proporcionar un motivo de eliminación']);
                return;
            }

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Obtener info del financiamiento para restaurar stock
            $queryInfo = "SELECT idproductosv2, cantidad_producto FROM financiamiento WHERE idfinanciamiento = ?";
            $stmt = $this->conexion->prepare($queryInfo);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $financiamiento = $stmt->get_result()->fetch_assoc();

            if ($financiamiento) {
                $idProducto = $financiamiento['idproductosv2'];
                $cantidad = $financiamiento['cantidad_producto'];

                // Restaurar stock
                if ($idProducto && $cantidad > 0) {
                    $queryRestaurar = "UPDATE productosv2 SET cantidad = cantidad + ? WHERE idproductosv2 = ?";
                    $stmtRestaurar = $this->conexion->prepare($queryRestaurar);
                    $stmtRestaurar->bind_param('ii', $cantidad, $idProducto);
                    $stmtRestaurar->execute();
                }
            }

            // Registrar en log antes de eliminar
            $usuario_id = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : null;
            if ($usuario_id) {
                $queryLog = "INSERT INTO log_aprobaciones_financiamiento (id_financiamiento, accion, usuario_id, motivo)
                             VALUES (?, 'eliminado', ?, ?)";
                $stmtLog = $this->conexion->prepare($queryLog);
                $stmtLog->bind_param('iis', $id, $usuario_id, $motivo);
                $stmtLog->execute();
            }

            // Eliminar el financiamiento
            $queryDelete = "DELETE FROM financiamiento WHERE idfinanciamiento = ?";
            $stmtDelete = $this->conexion->prepare($queryDelete);
            $stmtDelete->bind_param('i', $id);
            $stmtDelete->execute();

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Financiamiento eliminado correctamente y stock restaurado']);

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }

    // Reactivar financiamiento rechazado
    public function reactivarFinanciamiento()
    {
        try {
            $id = $_POST['id'] ?? 0;

            if (!$id) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
                return;
            }

            // Actualizar estado a pendiente
            $queryUpdate = "UPDATE financiamiento SET aprobado = 0 WHERE idfinanciamiento = ?";
            $stmt = $this->conexion->prepare($queryUpdate);
            $stmt->bind_param('i', $id);
            $stmt->execute();

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Financiamiento reactivado correctamente']);

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error al reactivar: ' . $e->getMessage()]);
        }
    }
}
