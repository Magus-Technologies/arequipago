<?php

require_once "app/models/DepartamentoConfig.php";

class DepartamentosConfigController extends Controller
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * Obtener todos los departamentos con su estado (habilitado/deshabilitado)
     */
    public function obtenerTodosDepartamentos()
    {
        try {
            // Verificar que el usuario sea director (rol 3)
            if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 3) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para acceder a esta función']);
                exit;
            }

            $model = new DepartamentoConfig();
            $departamentos = $model->obtenerTodosDepartamentos();

            echo json_encode(['success' => true, 'data' => $departamentos]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Cambiar el estado de un departamento (habilitar/deshabilitar)
     */
    public function cambiarEstadoDepartamento()
    {
        try {
            // Verificar que el usuario sea director (rol 3)
            if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 3) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
                exit;
            }

            // Validar datos recibidos
            if (!isset($_POST['iddepast']) || !isset($_POST['habilitado'])) {
                echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
                exit;
            }

            $iddepast = intval($_POST['iddepast']);
            $habilitado = intval($_POST['habilitado']);
            $usuario_id = $_SESSION['usuario_id'];
            $usuario_nombre = $_SESSION['nombre_usuario'] ?? 'Director';

            $model = new DepartamentoConfig();
            
            // Cambiar estado
            $resultado = $model->cambiarEstado($iddepast, $habilitado, $usuario_id);

            if ($resultado) {
                // Registrar en historial
                $nombre_departamento = $model->obtenerNombreDepartamento($iddepast);
                $accion = $habilitado ? 'HABILITADO' : 'DESHABILITADO';
                $model->registrarHistorial($iddepast, $nombre_departamento, $accion, $usuario_id, $usuario_nombre);

                echo json_encode([
                    'success' => true, 
                    'message' => 'Departamento ' . ($habilitado ? 'habilitado' : 'deshabilitado') . ' correctamente'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Obtener historial de cambios
     */
    public function obtenerHistorial()
    {
        try {
            // Verificar que el usuario sea director (rol 3)
            if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 3) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para acceder a esta función']);
                exit;
            }

            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;

            $model = new DepartamentoConfig();
            $historial = $model->obtenerHistorial($limit);

            echo json_encode(['success' => true, 'data' => $historial]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Habilitar múltiples departamentos a la vez
     */
    public function habilitarMultiples()
    {
        try {
            // Verificar que el usuario sea director (rol 3)
            if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 3) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
                exit;
            }

            if (!isset($_POST['departamentos']) || !is_array($_POST['departamentos'])) {
                echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
                exit;
            }

            $departamentos = $_POST['departamentos'];
            $usuario_id = $_SESSION['usuario_id'];
            $usuario_nombre = $_SESSION['nombre_usuario'] ?? 'Director';

            $model = new DepartamentoConfig();
            $exitosos = 0;

            foreach ($departamentos as $iddepast) {
                $iddepast = intval($iddepast);
                if ($model->cambiarEstado($iddepast, 1, $usuario_id)) {
                    $nombre_departamento = $model->obtenerNombreDepartamento($iddepast);
                    $model->registrarHistorial($iddepast, $nombre_departamento, 'HABILITADO', $usuario_id, $usuario_nombre);
                    $exitosos++;
                }
            }

            echo json_encode([
                'success' => true, 
                'message' => "$exitosos departamento(s) habilitado(s) correctamente"
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
}
