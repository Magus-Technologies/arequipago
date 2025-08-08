<?php
require_once "app/models/Conductor.php";
require_once "app/models/Vehiculo.php";
require_once "app/models/Inscripcion.php";

class ListarConductoresController extends Controller {
    
    public function listarConductores() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            $conductor = new Conductor();
            $conductores = $conductor->obtenerTodos();
            
            if ($conductores === false) {
                throw new Exception("Error al obtener los conductores");
            }
            
            echo json_encode($conductores);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
    
    public function eliminarConductor($id) {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        
        try {
            $conductor = new Conductor();
            $conductor->setIdConductor($id);
            
            if ($conductor->eliminar()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Conductor eliminado correctamente'
                ]);
            } else {
                throw new Exception('Error al eliminar el conductor');
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function buscarConductores() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
    
        try {
            $query = isset($_POST['query']) ? $_POST['query'] : ''; // Validar si 'query' está definido
            if (empty($query)) { // Línea añadida
                throw new Exception("El criterio de búsqueda no puede estar vacío."); // Línea añadida
            }
    
            $conductor = new Conductor();
    
            // Llama al método que realiza la búsqueda
            $conductores = $conductor->buscarPorCriterioAvanzado($query);
    
            if ($conductores === false) {
                echo json_encode([]); // CAMBIO: Se envía una respuesta vacía en lugar de lanzar una excepción
                exit;
            }
    
            echo json_encode($conductores);
    
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function listarConductoresPorFecha() {
        // Obtener las fechas desde la solicitud POST
        $data = json_decode(file_get_contents('php://input'), true);
        $fechaInicio = $data['fecha_inicio'];
        $fechaFin = $data['fecha_fin'];
    
        // Instanciar el modelo de conductor
        $conductorModel = new Conductor();
    
        // Obtener los conductores en el rango de fechas
        $conductores = $conductorModel->buscarPorRangoDeFechas($fechaInicio, $fechaFin);
    
        if (empty($conductores)) {
            echo json_encode(['error' => 'No se encontraron conductores en el rango de fechas especificado.']);
        } else {
            echo json_encode($conductores);
        }
    }
    
}
?>