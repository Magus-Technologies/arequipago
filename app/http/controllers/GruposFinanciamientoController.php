<?php
require_once "app/models/GrupoFinanciamientoModel.php";

class GruposFinanciamientoController extends Controller

{

    private $conexion;
    //private $conductor;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
       // $this->conductor = new Conductor();
    }


    public function guardarPlanFinanciamiento()
    {
        // Asegurar que la respuesta sea JSON
        header('Content-Type: application/json');
        
        // Limpiar cualquier output previo que pueda interferir
        ob_clean();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            
            $nombrePlan = $_POST["nombre_plan"] ?? "";
            $cuotaInicial = $_POST["cuota_inicial"] !== "" ? $_POST["cuota_inicial"] : null; 
            $montoCuota = $_POST["monto_cuota"] ?? "";
            $cantidadCuotas = $_POST["cantidad_cuotas"] ?? "";
            $frecuenciaPago = $_POST["frecuencia_pago"] ?? "";
            $moneda = $_POST["moneda"] ?? ""; 
            $tasaInteres = $_POST["tasa_interes"] ?? "";
            $monto = $_POST["monto"] ?? "";  // 🔹 Nuevo: Recibir el monto desde el formulario
            $montoSinInteres = $_POST["monto_sin_interes"] ?? ""; 

            $fechaInicio = $_POST["fecha_inicio"] ?? null; // 🔹 Recibir fecha de inicio
            $fechaFin = $_POST["fecha_fin"] ?? null;
            $tipoVehicular = $_POST["tipo_vehicular"] ?? null;

            if (empty($nombrePlan) || empty($frecuenciaPago) || empty($moneda)) { 
                echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios excepto la cuota inicial, monto de cuota y cantidad de cuotas."]);
                exit;
            }

            $grupoFinanciamiento = new GrupoFinanciamientoModel();
            $idPlan = $grupoFinanciamiento->insertarPlan($nombrePlan, $cuotaInicial, $montoCuota, $cantidadCuotas, $frecuenciaPago, $moneda, $tasaInteres, $monto, $montoSinInteres, $fechaInicio, $fechaFin, $tipoVehicular);

           if ($idPlan) {
                // Verificar si hay variantes para guardar
                if (isset($_POST['variantes'])) {
                    $variantes = json_decode($_POST['variantes'], true);
                    
                    if (!empty($variantes)) {
                        $resultadoVariantes = $grupoFinanciamiento->insertVariante($idPlan, $variantes);
                        
                        if (!$resultadoVariantes) {
                            echo json_encode([
                                "success" => false,
                                "message" => "Error al guardar las variantes del plan."
                            ]);
                            exit;
                        }
                    }
                }

                echo json_encode([
                    "success" => true,
                    "message" => "Plan de financiamiento y variantes guardados correctamente."
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Error al guardar el plan de financiamiento."
                ]);
            }
        }

        exit;
    }

    public function getAllPlanes() {
        $modelo = new GrupoFinanciamientoModel();
        $planes = $modelo->getAllPlanes();

        header("Content-Type: application/json");
        echo json_encode(["success" => true, "planes" => $planes]);
    }

    public function editarGrupo() {
        // Comprobar que la solicitud sea POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];  // ID del plan

            // Validar que los campos obligatorios estén presentes
            $nombrePlan = !empty($_POST['nombre_plan']) ? $_POST['nombre_plan'] : null;
            $moneda = !empty($_POST['moneda']) ? $_POST['moneda'] : null;

            // Si faltan los campos obligatorios, devolver error
            if (empty($nombrePlan) || empty($moneda)) {
                echo json_encode(['status' => 'error', 'message' => 'Los campos obligatorios deben completarse.']);
                exit;
            }

            // Tomar los valores del formulario (asignar null si están vacíos)
            $cuotaInicial = $_POST['cuota_inicial'] !== null ? $_POST['cuota_inicial'] : null;
            $montoCuota = $_POST['monto_cuota'] !== null ? $_POST['monto_cuota'] : null;
            $cantidadCuotas = $_POST['cantidad_cuotas'] !== null ? $_POST['cantidad_cuotas'] : null;
            $frecuenciaPago = $_POST['frecuencia_pago'] !== null ? $_POST['frecuencia_pago'] : null;
            $monto = $_POST['monto'] !== null ? $_POST['monto'] : null;
            $montoSinInteres = $_POST['monto_sin_interes'] !== null ? $_POST['monto_sin_interes'] : null;
            $tasaInteres = $_POST['tasa_interes'] !== null ? $_POST['tasa_interes'] : null;
            $fechaInicio = $_POST['fecha_inicio'] !== null ? $_POST['fecha_inicio'] : null;
            $fechaFin = $_POST['fecha_fin'] !== null ? $_POST['fecha_fin'] : null;
            // NUEVO: Capturar tipo vehicular del formulario
            $tipoVehicular = null;
            if (isset($_POST['tipo_vehicular'])) {
                $tipoVehicular = $_POST['tipo_vehicular'];
            } else {
                // Determinar tipo vehicular basado en los checkboxes (para compatibilidad)
                if (isset($_POST['checkAuto']) || (isset($_POST['tipo_vehiculo']) && $_POST['tipo_vehiculo'] === 'auto')) {
                    $tipoVehicular = 'vehiculo'; // Mapear 'auto' a 'vehiculo' según tu enum
                } elseif (isset($_POST['checkMoto']) || (isset($_POST['tipo_vehiculo']) && $_POST['tipo_vehiculo'] === 'moto')) {
                    $tipoVehicular = 'moto';
                }
            }

            try {
                $modelo = new GrupoFinanciamientoModel();  // Instanciar correctamente el modelo antes de usarlo (EDITADO)

                $modelo->editarGrupo(  
                    $id, $nombrePlan, $cuotaInicial, $montoCuota, $cantidadCuotas, $frecuenciaPago,
                    $moneda, $monto, $montoSinInteres, $tasaInteres, $fechaInicio, $fechaFin, $tipoVehicular
                );

                // Modificación para variantes: Manejar actualización de variantes si están presentes
                if (isset($_POST['variantes']) && is_array($_POST['variantes'])) {
                    foreach ($_POST['variantes'] as $variante) { 
                        if (isset($variante['idgrupos_variantes'])) {
                            // Actualizar variante existente
                            $modelo->actualizarVariante(
                                $variante['idgrupos_variantes'], $id, $variante['nombre_variante'],
                                $variante['cuota_inicial'], $variante['monto_cuota'], $variante['cantidad_cuotas'],
                                $variante['frecuencia_pago'], $variante['moneda'], $variante['monto'],
                                $variante['monto_sin_interes'], $variante['tasa_interes'],
                                $variante['fecha_inicio'], $variante['fecha_fin']
                            );
                        }
                    }
                }

                // Manejar nuevas variantes
                if (isset($_POST['nuevas_variantes'])) {
                    $nuevasVariantes = $_POST['nuevas_variantes'];
                    
                    if (!empty($nuevasVariantes)) {
                        $resultadoVariantes = $modelo->insertVariante($id, $nuevasVariantes);
                        
                        if (!$resultadoVariantes) {
                            echo json_encode([
                                'status' => 'error',
                                'message' => 'Error al guardar las nuevas variantes del plan.'
                            ]);
                            exit;
                        }
                    }
                }
    
                echo json_encode(['status' => 'success', 'message' => 'El plan ha sido actualizado correctamente.']);

            } catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no permitido.']);
        }
    }

    public function obtenerVariantesGrupo() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idPlan = isset($_POST['idplan_financiamiento']) ? $_POST['idplan_financiamiento'] : null;
            
            if (!$idPlan) {
                echo json_encode(['status' => 'error', 'message' => 'ID del plan no especificado']);
                return;
            }
            
            try {
                $modelo = new GrupoFinanciamientoModel();
                $variantes = $modelo->getVariantesGrupo($idPlan);
                
                echo json_encode(['status' => 'success', 'variantes' => $variantes]);
            } catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no permitido']);
        }
    }
    
    // Modificación para variantes: Método para actualizar una variante
    public function actualizarVariante() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? $_POST['id'] : null;
            $idPlanFinanciamiento = isset($_POST['idplan_financiamiento']) ? $_POST['idplan_financiamiento'] : null;
            
            // Validar datos obligatorios
            $nombreVariante = !empty($_POST['nombre_variante']) ? $_POST['nombre_variante'] : null;
            $moneda = !empty($_POST['moneda']) ? $_POST['moneda'] : null;
            
            if (empty($nombreVariante) || empty($moneda) || empty($id) || empty($idPlanFinanciamiento)) {
                echo json_encode(['status' => 'error', 'message' => 'Datos obligatorios incompletos']);
                return;
            }
            
            // Obtener resto de campos
            $cuotaInicial = isset($_POST['cuota_inicial']) ? $_POST['cuota_inicial'] : null;
            $montoCuota = isset($_POST['monto_cuota']) ? $_POST['monto_cuota'] : null;
            $cantidadCuotas = isset($_POST['cantidad_cuotas']) ? $_POST['cantidad_cuotas'] : null;
            $frecuenciaPago = isset($_POST['frecuencia_pago']) ? $_POST['frecuencia_pago'] : null;
            $monto = isset($_POST['monto']) ? $_POST['monto'] : null;
            $montoSinInteres = isset($_POST['monto_sin_interes']) ? $_POST['monto_sin_interes'] : null;
            $tasaInteres = isset($_POST['tasa_interes']) ? $_POST['tasa_interes'] : null;
            $fechaInicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
            $fechaFin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;
            
            try {
                $modelo = new GrupoFinanciamientoModel();
                $modelo->actualizarVariante(
                    $id, $idPlanFinanciamiento, $nombreVariante, $cuotaInicial, $montoCuota,
                    $cantidadCuotas, $frecuenciaPago, $moneda, $monto, $montoSinInteres,
                    $tasaInteres, $fechaInicio, $fechaFin
                );
                
                echo json_encode(['status' => 'success', 'message' => 'Variante actualizada correctamente']);
            } catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no permitido']);
        }
    }

    public function deleteGroup() {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
            $id = $_POST["id"];
            $model = new GrupoFinanciamientoModel();
            $resultado = $model->deleteGroup($id);

            echo json_encode(["success" => $resultado]);
        } else {
            echo json_encode(["success" => false]);
        }
    }

    // Agregar al final del archivo del controlador, antes del cierre de la clase
    public function obtenerTipoVehicular() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idPlan = isset($_POST['idplan_financiamiento']) ? $_POST['idplan_financiamiento'] : null;
            
            if (!$idPlan) {
                echo json_encode(['status' => 'error', 'message' => 'ID del plan no especificado']);
                return;
            }
            
            try {
                $modelo = new GrupoFinanciamientoModel();
                $tipoVehicular = $modelo->getTipoVehicular($idPlan);
                
                echo json_encode(['status' => 'success', 'tipo_vehicular' => $tipoVehicular]);
            } catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no permitido']);
        }
    }

}
?>
