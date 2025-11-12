<?php

require_once "utils/lib/mpdf/vendor/autoload.php";  // Incluir el autoload de MPDF
// NUEVO: Agregar después de require_once "app/models/Financiamiento.php";
require_once "app/models/ScoreService.php";

use Mpdf\Mpdf;

require_once "app/models/Cliente.php";
require_once "app/models/Conductor.php";
require_once "app/models/Financiamiento.php";
require_once "app/models/CuotaFinanciamiento.php";
require_once "app/models/DireccionConductor.php";
require_once "app/models/Departamento.php";
require_once "app/models/Provincia.php";
require_once "app/models/Distrito.php";
require_once "app/models/Productov2.php";
require_once "app/models/GrupoFinanciamientoModel.php";
require_once "app/http/controllers/ReportFinanciamientoController.php";
require_once 'app/models/Financiamiento.php';
require_once "app/models/Comision.php";

class FinanciamientoController extends Controller
{
    private $conexion;
    private $financiamientoModel;
    private $conductorModel;
    private $clienteModel;
    private $productoModel;
    private $reportesModel;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->mpdf = new Mpdf();  // Crear una instancia de Mpdf

        //Inicializar modelos
        $this->financiamientoModel = new Financiamiento();
        $this->conductorModel = new Conductor();
        $this->clienteModel = new Cliente();
        $this->productoModel = new Productov2();
        $this->reportesModel = new Reportes();  
    }
    
    public function obtenerClientesFinanciamiento()
    {
        try {
            $conductorModel = new Conductor();

            // Recibir la página actual, por defecto 1
            $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            $cantidadPorPagina = 12;

             // 🔴 Obtener parámetros de ordenamiento
            $sortField = isset($_GET['sortField']) ? $_GET['sortField'] : null;
            $sortDirection = isset($_GET['sortDirection']) ? $_GET['sortDirection'] : null;
            
            // 🔴 Pasar los parámetros de ordenamiento al modelo
            $conductores = $conductorModel->obtenerTodosConductores(
                $pagina, 
                $cantidadPorPagina, 
                $sortField, 
                $sortDirection
            );
            
                
            // Obtener el total de conductores (sin contar financiamientos repetidos)
            $totalConductores = $conductorModel->obtenerTotalConductores();
            $totalPaginas = ceil($totalConductores / $cantidadPorPagina);

            // Devuelve los datos en formato JSON
            echo json_encode([
                'conductores' => $conductores,
                'totalPaginas' => $totalPaginas,
                'paginaActual' => $pagina
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode(['error' => 'Hubo un error al obtener los datos']);
            exit;
        }
    }

    public function obtenerClientesFiltrados()
    {
        try {
            $clienteModel = new Conductor();

            // Obtener el término de búsqueda desde la solicitud
            $searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';
            $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            $cantidadPorPagina = 12;

        // 🔴 Obtener parámetros de ordenamiento
            $sortField = isset($_GET['sortField']) ? $_GET['sortField'] : null;
            $sortDirection = isset($_GET['sortDirection']) ? $_GET['sortDirection'] : null;

        // 🔴 Pasar los parámetros de ordenamiento al modelo
        $clientes = $clienteModel->obtenerConductoresFiltrados(
            $searchTerm, 
            $pagina, 
            $cantidadPorPagina,
            $sortField,
            $sortDirection
        );


            // Transformar los datos para que sean compatibles con la estructura original
            $conductores = $this->transformarClientesAConductores($clientes);
           
            // Obtener el total de conductores únicos para la paginación
            $totalClientes = $clienteModel->obtenerTotalClientesBusqueda($searchTerm);
            $totalPaginas = ceil($totalClientes / $cantidadPorPagina);

            // Responder en formato JSON - MODIFICADO: ahora devuelve 'conductores' en lugar de 'clientes'
            header('Content-Type: application/json');
            echo json_encode([
                'conductores' => $conductores, // MODIFICADO: nombre de clave cambiado de 'clientes' a 'conductores'
                'totalPaginas' => $totalPaginas,
                'paginaActual' => $pagina,
                'totalRegistros' => $totalClientes
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode(['error' => 'Hubo un error al obtener los datos']);
            exit;
        }
    }

        // Nuevo método para transformar los datos de la búsqueda al formato esperado por el frontend
    private function transformarClientesAConductores($clientes)
    {
        $conductores = [];
        
        foreach ($clientes as $cliente) {

        // Detecta automáticamente cuál es la clave del ID (empieza con 'id')
        $idKey = null;
        foreach ($cliente as $key => $value) {
            if (str_starts_with($key, 'id')) {
                $idKey = $key;
                break;
            }
        }

            $conductor = [
                $idKey => $cliente[$idKey],
                'nombres' => '', 
                'apellido_paterno' => '',
                'apellido_materno' => '',
                'numUnidad' => $cliente['numUnidad'],
                'grupo_financiamiento' => $cliente['grupo_financiamiento'],
                'cantidad_financiamientos' => $cliente['cantidad_financiamientos'],
                'fecha_ultimo_financiamiento' => $cliente['fecha_ultimo_financiamiento'] ?? null  // ← Nuevo campo añadido
            ];
            
            // Si tenemos datos separados de nombres y apellidos, los usamos
            if (isset($cliente['nombres']) && isset($cliente['apellido_paterno']) && isset($cliente['apellido_materno'])) {
                $conductor['nombres'] = $cliente['nombres'];
                $conductor['apellido_paterno'] = $cliente['apellido_paterno'];
                $conductor['apellido_materno'] = $cliente['apellido_materno'];
            }
            // Si solo tenemos el nombre completo en 'datos', lo dividimos
            else if (isset($cliente['datos'])) {
                // Intenta dividir el nombre completo en partes
                $partes = explode(' ', $cliente['datos']);
                if (count($partes) >= 3) {
                    $conductor['nombres'] = $partes[0];
                    $conductor['apellido_paterno'] = $partes[1];
                    $conductor['apellido_materno'] = implode(' ', array_slice($partes, 2));
                } else if (count($partes) == 2) {
                    $conductor['nombres'] = $partes[0];
                    $conductor['apellido_paterno'] = $partes[1];
                    $conductor['apellido_materno'] = '';
                } else {
                    $conductor['nombres'] = $cliente['datos'];
                    $conductor['apellido_paterno'] = '';
                    $conductor['apellido_materno'] = '';
                }
            }
            
            $conductores[] = $conductor;
        }
        
        return $conductores;
    }

    public function obtenerFinanciamientoPorCliente()
        {
            try {

                // Verificar si se proporcionó id_conductor o id
                $id_conductor = isset($_GET['id_conductor']) ? (int)$_GET['id_conductor'] : 0;
                $id_cliente = isset($_GET['id']) ? (int)$_GET['id'] : 0;


                // Crear una instancia del modelo Financiamiento
                $financiamientoModel = new Financiamiento();

                $data = null;
        
                if ($id_conductor > 0) {
                    // Obtener los detalles del financiamiento para el conductor
                    $data = $financiamientoModel->obtenerPorConductor($id_conductor);
                    
                    if (!$data) {
                        echo json_encode(['error' => 'No se encontraron financiamientos para este conductor']);
                        exit;
                    }
                } else if ($id_cliente > 0) {  // Añadido: verificar si hay id_cliente
                    // Obtener los detalles del financiamiento para el cliente
                    $data = $financiamientoModel->obtenerPorCliente($id_cliente);
                    
                    if (!$data) {
                        echo json_encode(['error' => 'No se encontraron financiamientos para este cliente']);
                        exit;
                    }
                } else {
                    echo json_encode(['error' => 'ID no proporcionado']);  // Modificado: mensaje más genérico
                    exit;
                }

                // Devolver los datos de financiamiento
                echo json_encode($data); // Cambio: Enviar directamente el array asociativo
                exit;
            } catch (Exception $e) {
                echo json_encode(['error' => 'Hubo un error al obtener los datos del financiamiento']);
                exit;
            }
        }

        public function obtenerCuotasPorCliente()
        {
            try {
                // Verificar si se proporcionó id_conductor o id
                $id_conductor = isset($_GET['id_conductor']) ? (int)$_GET['id_conductor'] : 0;
                $id_cliente = isset($_GET['id']) ? (int)$_GET['id'] : 0;  // Añadido: obtener id_cliente
                
                $financiamientos = null;
       
                // Crear instancia del modelo Financiamiento
                $financiamientoModel = new Financiamiento(); 
                
                if ($id_conductor > 0) {
                    $financiamientos = $financiamientoModel->getFinanciamientoList($id_conductor);
                } else if ($id_cliente > 0) {  // Añadido: verificar si hay id_cliente
                    $financiamientos = $financiamientoModel->getFinanciamientoListCliente($id_cliente);
                } else {
                    echo json_encode(['error' => 'ID no proporcionado']);  // Modificado: mensaje más genérico
                    return;
                }
                
                $productoModel = new Productov2();
               
                if (empty($financiamientos)) {
                    echo json_encode(['financiamientos' => null]);
                    return;
                }
        
                
                $cuotaModel = new CuotaFinanciamiento(); 

                foreach ($financiamientos as &$financiamiento) {
                    $id_financiamiento = $financiamiento['idfinanciamiento'];
        
                    // Obtener cuotas asociadas
                    $financiamiento['cuotas'] = $cuotaModel->getCuotasforFinanciamientoList($id_financiamiento);
        
                    // Obtener producto asociado
                    $id_producto = $financiamiento['idproductosv2']; // ID del producto en el financiamiento
                    $financiamiento['producto'] = $productoModel->getProductsList($id_producto);
                }
                // Responder con los financiamientos y sus cuotas asociadas
                echo json_encode(['financiamientos' => $financiamientos]); // Modificado

            } catch (Exception $e) {
                echo json_encode(['error' => 'Error al obtener cuotas: ' . $e->getMessage()]);
            }
        }

      public function obtenerClienteDetalle()
{
    try {
        // Verificar si se proporcionó id_conductor o id
        $id_conductor = isset($_GET['id_conductor']) ? (int)$_GET['id_conductor'] : 0;
        $id_cliente = isset($_GET['id']) ? (int)$_GET['id'] : 0; 
        
        $financiamientos = null;
        $persona = null;
        $direccion = null;
        
        // Crear instancia del modelo Financiamiento
        $financiamientoModel = new Financiamiento();
        $productoModel = new Productov2();
        
        if ($id_conductor > 0) {
            // Obtener financiamientos del conductor
            $financiamientos = $financiamientoModel->getFinanciamientoList($id_conductor);
            
            // Obtener información del conductor
            $conductorModel = new Conductor();
            $persona = $conductorModel->getConductorFinanceList($id_conductor);
            
            // Obtener dirección del conductor
            $direccionModel = new DireccionConductor();
            $direccion = $direccionModel->obtenerDatosDireccion($id_conductor);
            
            // NUEVO: Procesar cada financiamiento para agregar información del plan
            if ($financiamientos) {
                foreach ($financiamientos as &$financiamiento) {
                    // Obtener información del plan
                    $planQuery = "SELECT 
                        p.nombre_plan,
                        p.tipo_vehicular,
                        p.monto_sin_interes as plan_capacidad_original,
                        p.monto_cuota,
                        p.cantidad_cuotas,
                        p.fecha_inicio as plan_fecha_inicio
                    FROM planes_financiamiento p 
                    WHERE p.idplan_financiamiento = ?";
                    
                    $conexion = $this->conexion; 
                    $planStmt = $conexion->prepare($planQuery);
                    $planStmt->bind_param("i", $financiamiento['grupo_financiamiento']);
                    $planStmt->execute();
                    $planResult = $planStmt->get_result();
                    $planData = $planResult->fetch_assoc();
                    
                    // NUEVO: Calcular capacidad de compra actual si es vehículo
                    // Verificar si es vehículo por tipo_vehicular O por nombre del plan
                    $esVehiculo = ($planData && (
                        $planData['tipo_vehicular'] === 'vehiculo' || 
                        stripos($planData['nombre_plan'], 'vehicular') !== false
                    ));
                    
                    if ($esVehiculo) {
                        $semanasPerdidas = 0;
                        $dineroPerdido = 0;
                        $capacidadCompraActual = $planData['plan_capacidad_original'];
                        
                        if ($planData['plan_fecha_inicio'] && $financiamiento['fecha_creacion']) {
                            $fechaInicio = new DateTime($planData['plan_fecha_inicio']);
                            $fechaEntrada = new DateTime($financiamiento['fecha_creacion']);
                            $diferencia = $fechaEntrada->diff($fechaInicio);
                            $semanasPerdidas = floor($diferencia->days / 7);
                            $dineroPerdido = $semanasPerdidas * $planData['monto_cuota'];
                            $capacidadCompraActual = $planData['plan_capacidad_original'] - $dineroPerdido;
                        }
                        
                        $financiamiento['es_vehiculo'] = true;
                        $financiamiento['plan_capacidad_original'] = $planData['plan_capacidad_original'];
                        $financiamiento['semanas_perdidas'] = $semanasPerdidas;
                        $financiamiento['dinero_perdido'] = $dineroPerdido;
                        $financiamiento['capacidad_compra_actual'] = $capacidadCompraActual;
                    } else {
                        $financiamiento['es_vehiculo'] = false;
                    }
                    
                    $planStmt->close();
                }
            }
        } elseif ($id_cliente > 0) {
            // Obtener financiamientos del cliente
            $financiamientos = $financiamientoModel->getFinanciamientoListCliente($id_cliente);
            
            // Obtener información del cliente
            $clienteModel = new Cliente();
            $persona = $clienteModel->getClienteList($id_cliente);
            
            $direccion = $clienteModel->obtenerDatosDireccionCliente($id_cliente);
        }
        
        if (empty($financiamientos)) {
            echo json_encode(['financiamientos' => null]);
            return;
        }        
        
        $cuotaModel = new CuotaFinanciamiento(); 

        foreach ($financiamientos as &$financiamiento) {
            $id_financiamiento = $financiamiento['idfinanciamiento'];

            // Obtener cuotas asociadas
            $financiamiento['cuotas'] = $cuotaModel->getCuotasforFinanciamientoList($id_financiamiento);

            // Obtener producto asociado
            $id_producto = $financiamiento['idproductosv2'];
            $financiamiento['producto'] = $productoModel->getProductsList($id_producto);
            
            // Obtener nombre del usuario que registró el financiamiento
            $financiamiento['usuario_registro'] = $financiamientoModel->obtenerUsuarioRegistro($id_financiamiento);
            
            // NUEVO: Asegurar que se incluya el monto_sin_interes (Monto de Compra)
            if (!isset($financiamiento['monto_sin_interes']) || $financiamiento['monto_sin_interes'] === null) {
                $financiamiento['monto_sin_interes'] = $financiamiento['monto_total'] ?? 0;
            }
        }

        echo json_encode([
            'financiamientos' => $financiamientos,
            'conductor' => $persona,
            'direccion' => $direccion
        ]);

    } catch (Exception $e) {
        echo json_encode(['error' => 'Error al obtener cuotas: ' . $e->getMessage()]);
    }
}

        private function obtenerNombreDepartamento($idDepartamento)
        {
            $sql = "SELECT nombre FROM depast WHERE iddepast = ?";
            return $this->obtenerNombrePorId($sql, $idDepartamento);
        }
        
        private function obtenerNombreProvincia($idProvincia)
        {
            $sql = "SELECT nombre FROM provincet WHERE idprovincet = ?";
            return $this->obtenerNombrePorId($sql, $idProvincia);
        }
        
        private function obtenerNombreDistrito($idDistrito)
        {
            $sql = "SELECT nombre FROM distritot WHERE iddistritot = ?";
            return $this->obtenerNombrePorId($sql, $idDistrito);
        }
        
        private function obtenerNombrePorId($sql, $id)
        {
            try {
                $conexion = (new Conexion())->getConexion();
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
        
                $row = $result->fetch_assoc();
                return $row ? $row['nombre'] : 'Desconocido';
            } catch (Exception $e) {
                return 'Error';
            }
        }


        // MODIFICADO: Ahora busca tanto en conductores como en clientes
        public function obtenerClientesAutocompletado()
        {
            try {
                $conductorModel = new Conductor();
                $clienteModel = new Cliente();

                $searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';

                // Obtener conductores
                $conductores = $conductorModel->obtenerConductoresConCodigo($searchTerm);
                
                // Obtener clientes
                $clientes = $clienteModel->obtenerClientesConCodigo($searchTerm);
                
                // Marcar tipo en conductores
                foreach ($conductores as &$conductor) {
                    $conductor['tipo_registro'] = 'conductor';
                    if (!isset($conductor['datos'])) {
                        $conductor['datos'] = trim(
                            ($conductor['nombres'] ?? '') . ' ' . 
                            ($conductor['apellido_paterno'] ?? '') . ' ' . 
                            ($conductor['apellido_materno'] ?? '')
                        );
                    }
                }
                
                // Marcar tipo y adaptar clientes
                foreach ($clientes as &$cliente) {
                    $cliente['tipo_registro'] = 'cliente';
                    
                    if (!isset($cliente['datos'])) {
                        $cliente['datos'] = trim(
                            ($cliente['nombres'] ?? '') . ' ' . 
                            ($cliente['apellido_paterno'] ?? '') . ' ' . 
                            ($cliente['apellido_materno'] ?? '')
                        );
                    }
                    
                    $cliente['numeroCodFi'] = $cliente['codigo_asociado'] ?? $cliente['num_cod_finan'] ?? '';
                    $cliente['codigo_asociado'] = $cliente['numeroCodFi'];
                    
                    if (!isset($cliente['id_conductor'])) {
                        $cliente['id_conductor'] = $cliente['id'];
                    }
                }
                
                $resultadosCombinados = array_merge($conductores, $clientes);

                echo json_encode($resultadosCombinados);
                exit;
            } catch (Exception $e) {
                error_log("Error en obtenerClientesAutocompletado: " . $e->getMessage());
                echo json_encode(['error' => 'Hubo un error al obtener los datos']);
                exit;
            }
        }

        public function obtenerNumDocAutocompletado()
        {
            try {
                $conductorModel = new Conductor();

                // Obtener el término de búsqueda desde la solicitud GET
                $searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';

                // Obtener los números de documento filtrados
                $conductores = $conductorModel->obtenerNumDocFiltrado($searchTerm);

                // Devolver los resultados en formato JSON
                echo json_encode($conductores);
                exit;
            } catch (Exception $e) {
                echo json_encode(['error' => 'Hubo un error al obtener los datos']);
                exit;
            }
        }

        public function obtenerNumDocClientesAutocompletado()
        {
            try {
                $conductorModel = new Conductor();
                $clienteModel = new Cliente();

                $searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';

                // Validación de longitud mínima
                if (strlen($searchTerm) < 2) {
                    echo json_encode([]);
                    exit;
                }

                // Obtener conductores por número de documento
                $conductores = $conductorModel->obtenerNumDocFiltrado($searchTerm);
                
                // Obtener clientes
                $clientes = $clienteModel->obtenerClientesConCodigo($searchTerm);
                
                // Marcar tipo y adaptar estructura para CONDUCTORES
                foreach ($conductores as &$conductor) {
                    $conductor['tipo_registro'] = 'conductor';
                    if (!isset($conductor['datos'])) {
                        $conductor['datos'] = trim(
                            ($conductor['nombres'] ?? '') . ' ' . 
                            ($conductor['apellido_paterno'] ?? '') . ' ' . 
                            ($conductor['apellido_materno'] ?? '')
                        );
                    }
                }
                
                // Marcar tipo y adaptar estructura para CLIENTES
                foreach ($clientes as &$cliente) {
                    $cliente['tipo_registro'] = 'cliente';
                    
                    if (!isset($cliente['datos'])) {
                        $cliente['datos'] = trim(
                            ($cliente['nombres'] ?? '') . ' ' . 
                            ($cliente['apellido_paterno'] ?? '') . ' ' . 
                            ($cliente['apellido_materno'] ?? '')
                        );
                    }
                    
                    if (!isset($cliente['nombres']) && isset($cliente['datos'])) {
                        $partesNombre = explode(' ', $cliente['datos']);
                        $cliente['nombres'] = $partesNombre[0] ?? '';
                        $cliente['apellido_paterno'] = $partesNombre[1] ?? '';
                        $cliente['apellido_materno'] = $partesNombre[2] ?? '';
                    }
                    
                    $cliente['numeroCodFi'] = $cliente['codigo_asociado'] ?? $cliente['num_cod_finan'] ?? '';
                    $cliente['codigo_asociado'] = $cliente['numeroCodFi'];
                    
                    if (!isset($cliente['id_conductor'])) {
                        $cliente['id_conductor'] = $cliente['id'];
                    }
                }
                
                $resultadosCombinados = array_merge($conductores, $clientes);

                echo json_encode($resultadosCombinados);
                exit;
            } catch (Exception $e) {
                error_log("Error en obtenerNumDocClientesAutocompletado: " . $e->getMessage());
                echo json_encode(['error' => 'Hubo un error al obtener los datos: ' . $e->getMessage()]);
                exit;
            }
        }
        
        public function obtenerProductos()
        {
            try {
                // Obtener la página actual desde $_GET
                $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                $productosPorPagina = 5; // Número de productos por página

                // ✅ NUEVO: Obtener filtro de categoría si existe (para CrediYango)
                $categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : null;

                // Mostrar los valores de entrada
                //var_dump(['pagina' => $pagina, 'productosPorPagina' => $productosPorPagina, 'categoria' => $categoria]);

                // Crear una instancia del modelo ProductoV2
                $productoV2 = new ProductoV2();

                // ✅ NUEVO: Obtener los productos con paginación y filtro de categoría
                $productos = $productoV2->obtenerProductos($pagina, $productosPorPagina, $categoria);

                // Mostrar los datos obtenidos del modelo
                //var_dump($productos);

                // Devolver los productos en formato JSON
                echo json_encode([
                    'productos' => $productos['productos'],
                    'totalPaginas' => $productos['totalPages'], // Cambiamos "totalPages" por "totalPaginas"
                ]);
                exit;
            } catch (Exception $e) {
                //var_dump(['error' => $e->getMessage()]);
                echo json_encode(['error' => 'Hubo un error al obtener los productos']);
                exit;
            }

        }

        public function searchProductos() {
            try {
                // Obtener el término de búsqueda desde la solicitud
                $searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';
        
                // Instanciar el modelo
                $productoModel = new ProductoV2();
        
                // Obtener los productos filtrados
                $productos = $productoModel->buscarProductosPorNombreOCodigo($searchTerm);
        
                // Responder con los datos en formato JSON
                echo json_encode(['productos' => $productos]);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
        }

        public function obtenerTipoProducto()
        {
            if (isset($_GET['id_producto'])) {
                $idProducto = intval($_GET['id_producto']);
                $modeloProducto = new Productov2();

                $tipoProducto = $modeloProducto->obtenerTipoProductoPorId($idProducto);

                if ($tipoProducto) {
                    echo json_encode(['tipo_producto' => $tipoProducto]);
                } else {
                    echo json_encode(['error' => 'Producto no encontrado']);
                }
            } else {
                echo json_encode(['error' => 'ID de producto no proporcionado']);
            }
        }

        // public function cargarGruposFinanciamiento() {
        //     $grupoFinanciamientoModel = new GrupoFinanciamientoModel();
        //     $grupos = $grupoFinanciamientoModel->obtenerGruposFinanciamiento();
        //     echo json_encode($grupos);
        // }

        public function generarCronogramaPDF() {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);      
            
            // Validar que los datos estén presentes
            if (!$data || !isset($data['nombreCliente'], $data['numeroDocumento'], $data['fechaInicio'], $data['monto'], $data['tasaInteres'], $data['cronograma'])) {
                return json_encode([
                    'success' => false,
                    'message' => 'Datos incompletos enviados al servidor.'
                ]);
            }

            // Asignar variables desde el payload
            $nombreCliente = $data['nombreCliente'];
            $numeroDocumento = $data['numeroDocumento'];
            $fechaInicio = $data['fechaInicio'];
            $monto = $data['monto'];
            $tasaInteres = $data['tasaInteres'];
            $frecuenciaPago = $data['frecuenciaPago'];
            $cronograma = $data['cronograma'];
            $tipoMoneda = $data['tipoMoneda'];

            $conductorModel = new Conductor();     
            $tipoDoc = $conductorModel->obtenerTipoDocumento($numeroDocumento); // Cambié $datos['numeroDocumento'] a $numeroDocumento porque $datos no estaba definido
    
            if ($frecuenciaPago == 'mensual') { // Si es mensual
                $htmlCronograma = $this->generarHTMLCronogramaMensual([
                    'nombreCliente' => $nombreCliente,
                    'numeroDocumento' => $numeroDocumento,
                    'fechaInicio' => $fechaInicio,
                    'monto' => $monto,
                    'tasaInteres' => $tasaInteres,
                    'frecuenciaPago' => $frecuenciaPago,
                    'cronograma' => $cronograma,
                    'tipoMoneda' => $tipoMoneda
                ], $tipoDoc);
            } else { // Si es semanal
                $htmlCronograma = $this->generarHTMLCronograma([
                    'nombreCliente' => $nombreCliente,
                    'numeroDocumento' => $numeroDocumento,
                    'fechaInicio' => $fechaInicio,
                    'monto' => $monto,
                    'tasaInteres' => $tasaInteres,
                    'frecuenciaPago' => $frecuenciaPago,
                    'cronograma' => $cronograma,
                    'tipoMoneda' => $tipoMoneda // Pasar el tipo de moneda al HTML
                ], $tipoDoc);
            }

            // Configurar mPDF
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($htmlCronograma);

            // Generar nombre del archivo
            $nombreArchivo = "cronograma_{$numeroDocumento}.pdf";

            // Generar PDF
            $pdfContent = $mpdf->Output('', 'S'); // 'S' para obtener el contenido como string

            // Retornar el PDF en formato base64 para enviarlo a la vista
            return json_encode([
                'success' => true,
                'pdf' => base64_encode($pdfContent), // Convertir a base64 para poder enviarlo a la vista
                'nombre' => $nombreArchivo
            ]);
        }
    
        private function generarHTMLCronograma($datos, $tipoDoc) {
            // Ruta del archivo cronograma.html
            $rutaBase = "app" . DIRECTORY_SEPARATOR . "contratos";
            $rutaArchivo = $rutaBase . DIRECTORY_SEPARATOR . "cronograma.html";
        
            // Leer el contenido del archivo
            $html = file_get_contents($rutaArchivo);
            /*if ($datos['frecuenciaPago'] == 'semanal') {
                $html = str_replace('style="display:none;"', '', $html); // Eliminar el display:none si es semanal
            }*/
            
            
            // Reemplazar los valores en el HTML
            $html = str_replace('[Nombre del cliente]', $datos['nombreCliente'], $html);
            $html = str_replace('[Tipo de documento de identidad]', $tipoDoc, $html); // Cambié para asegurar que $tipoDoc provenga del modelo correctamente
            $html = str_replace('[Número de identidad]', $datos['numeroDocumento'], $html);
            $html = str_replace('[Fecha de inicio del financiamiento]', date('d/m/Y', strtotime($datos['fechaInicio'])), $html);
            $html = str_replace('[Monto del financiamiento]', $datos['tipoMoneda'] . ' ' . number_format($datos['monto'], 2), $html); // Modificado para usar el tipo de moneda dinámico
            $html = str_replace('[Tasa de interés]', $datos['tasaInteres'], $html);
        
            $tablaSemanal = ''; // Inicializo la variable para la tabla
            foreach ($datos['cronograma'] as $cuota) { // Itero sobre el cronograma
                $fechaVencimiento = DateTime::createFromFormat('d/m/Y', $cuota['vencimiento']); // Convertimos la fecha
                if ($fechaVencimiento) {
                    $fechaFormateada = $fechaVencimiento->format('d/m/Y'); // Formateamos la fecha
                } else {
                    $fechaFormateada = $cuota['vencimiento']; // Si la fecha no se puede convertir, dejamos la original
                }

                $tablaSemanal .= "<tr>\n";
                $tablaSemanal .= "<td>{$cuota['cuota']}</td>\n"; // Número de cuota
                $tablaSemanal .= "<td>{$datos['tipoMoneda']} " . number_format($cuota['valor'], 2) . "</td>\n"; // Modificado para usar el tipo de moneda dinámico
                $tablaSemanal .= "<td>{$fechaFormateada}</td>\n"; // Fecha de vencimiento con formato
                $tablaSemanal .= "</tr>\n";
            }

           
            $html = str_replace('<tbody id="tabla_semanal">', '<tbody id="tabla-cronograma">' .$tablaSemanal, $html); 
                        
            // Ocultar tabla mensual si es semanal
            $html = str_replace('<div class="section" id="mensual">', '<div class="section" id="mensual" style="display:none;">', $html); // Ocultar tabla mensual


            $html = str_replace('[NOMBRE DE LA EMPRESA]', 'AREQUIPA GO E.I.R.L.', $html);
            $html = str_replace('[Dirección de la empresa]', 'Urb. Adepa Mz L Lt 15 Distrito de José Luis Bustamante y Rivero Provincia y Departamento de Arequipa', $html);


            // Retornar el HTML generado
            return $html;

        }

            
        private function generarHTMLCronogramaMensual($datos, $tipoDoc){    
                
              // Ruta del archivo cronograma.html
            $rutaBase = "app" . DIRECTORY_SEPARATOR . "contratos";
            $rutaArchivo = $rutaBase . DIRECTORY_SEPARATOR . "cronograma.html";
        
            // Leer el contenido del archivo
            $html = file_get_contents($rutaArchivo);
        
            

            // Reemplazar los valores en el HTML
            $html = str_replace('[Nombre del cliente]', $datos['nombreCliente'], $html);
            $html = str_replace('[Tipo de documento de identidad]', $tipoDoc, $html); // Cambié para asegurar que $tipoDoc provenga del modelo correctamente
            $html = str_replace('[Número de identidad]', $datos['numeroDocumento'], $html);
            $html = str_replace('[Fecha de inicio del financiamiento]', date('d/m/Y', strtotime($datos['fechaInicio'])), $html);
            $html = str_replace('[Monto del financiamiento]', $datos['tipoMoneda'] . ' ' . number_format($datos['monto'], 2), $html); // Modificado para usar el tipo de moneda dinámico
            $html = str_replace('[Tasa de interés]', $datos['tasaInteres'], $html);
            
            
            
            // Array de traducción para los meses
                $mesesEnEspanol = [
                    'January' => 'Enero',
                    'February' => 'Febrero',
                    'March' => 'Marzo',
                    'April' => 'Abril',
                    'May' => 'Mayo',
                    'June' => 'Junio',
                    'July' => 'Julio',
                    'August' => 'Agosto',
                    'September' => 'Septiembre',
                    'October' => 'Octubre',
                    'November' => 'Noviembre',
                    'December' => 'Diciembre'
                ];

                // Rellenar la tabla de pagos mensual
                $tablaMensual = '';
                $cuotasPorMes = [];

                // Agrupar las cuotas por mes y año
                foreach ($datos['cronograma'] as $cuota) {
                    $fechaVencimiento = DateTime::createFromFormat('d/m/Y', $cuota['vencimiento']);
                    
                    if (!$fechaVencimiento) {
                        continue; // Si la fecha no es válida, continuar con el siguiente ciclo
                    }
                    
                    $mesEnIngles = $fechaVencimiento->format('F');
                    $mes = $mesesEnEspanol[$mesEnIngles];
                    $anio = $fechaVencimiento->format('Y');

                    $mesAnio = $mes . ' ' . $anio;

                    // Agrupar cuotas por mes y año
                    if (!isset($cuotasPorMes[$mesAnio])) {
                        $cuotasPorMes[$mesAnio] = [];
                    }

                    $cuotasPorMes[$mesAnio][] = $cuota;
                }

                // Generar filas para la tabla mensual
                foreach ($cuotasPorMes as $mesAnio => $cuotas) {
                    foreach ($cuotas as $cuota) {
                        $fechaVencimiento = $cuota['vencimiento']; // Ya está en formato dd/mm/yyyy
                        $valorFormateado = number_format($cuota['valor'], 2, '.', ',');
                        $tablaMensual .= "<tr>\n" .
                            "<td>{$mesAnio}</td>\n" .
                            "<td>Cuota {$cuota['cuota']}: {$datos['tipoMoneda']} {$valorFormateado}</td>\n" . // Aquí se arregló la concatenación
                            "<td>{$fechaVencimiento}</td>\n" .
                            "</tr>\n";
                    }
                }

                $html = str_replace('<tbody id="tabla_mensual">', '<tbody id="superTabla">' . $tablaMensual, $html);

                $html = str_replace('<div class="section" id="semanal">', '<div class="section" id="semanal" style="display:none;">', $html); // Ocultar tabla semanal
                
                // Rellenar los datos de la empresa
                $html = str_replace('[NOMBRE DE LA EMPRESA]', 'AREQUIPA GO E.I.R.L.', $html);
                $html = str_replace('[Dirección de la empresa]', 'Urb. Adepa Mz L Lt 15 Distrito de José Luis Bustamante y Rivero Provincia y Departamento de Arequipa', $html);


                // Retornar el HTML generado
                return $html;
               
       }

       public function obtenerTipoCambio() {
            // Intentar primero con la API principal (SUNAT)
            $tipoCambio = $this->obtenerTipoCambioSUNAT();

            if (!$tipoCambio) {
                // Si falla, usar API de respaldo
                $tipoCambio = $this->obtenerTipoCambioRespaldo();
            }

            if (!$tipoCambio) {
                // Si ambas fallan, usar valor por defecto
                $tipoCambio = 3.70;
            }

            echo json_encode(['tipo_cambio' => $tipoCambio]);
        }

        private function obtenerTipoCambioSUNAT() {
            try {
                $url = 'https://api.apis.net.pe/v2/sunat/tipo-cambio';
                $token = 'apis-token-12676.06vC22lNLuV4uUGX4CsxHcdKf2tT92T8';

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_HTTPHEADER => array(
                        'Referer: https://apis.net.pe/tipo-de-cambio-sunat-api',
                        'Authorization: Bearer ' . $token
                    ),
                ));

                $response = curl_exec($curl);
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);

                if ($httpCode === 200) {
                    $data = json_decode($response, true);
                    if ($data && isset($data['precioVenta'])) {
                        return floatval($data['precioVenta']);
                    }
                }
            } catch (Exception $e) {
                // Continuar al método de respaldo
            }

            return false;
        }

        private function obtenerTipoCambioRespaldo() {
            try {
                // API gratuita de tipo de cambio (ejemplo)
                $url = 'https://api.exchangerate-api.com/v4/latest/USD';

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_TIMEOUT => 10,
                ));

                $response = curl_exec($curl);
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);

                if ($httpCode === 200) {
                    $data = json_decode($response, true);
                    if ($data && isset($data['rates']['PEN'])) {
                        return floatval($data['rates']['PEN']);
                    }
                }
            } catch (Exception $e) {
                // Falló también el respaldo
            }

            return false;
        }

        
        public function buscarPlanesMensuales() {
            $model = new Productov2(); // Cambié el acceso estático por una instancia del modelo

            // Llamamos al método del modelo para obtener los datos
            $productos = $model->getPlanesMensuales(); // Usamos la instancia para invocar el método

            // Devolvemos los datos como JSON
            echo json_encode($productos);
        }
       
        public function obtenerPlanFinanciamiento() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_producto'])) {
                $idProducto = intval($_POST['id_producto']); // Aseguramos que sea un número entero
    
                $financiamientoModel = new Financiamiento(); // Instanciamos el modelo
                $plan = $financiamientoModel->getPlanChecker($idProducto); // Llamamos al método del modelo
    
                if ($plan) {
                    echo json_encode(['success' => true, 'plan' => $plan]); // Enviamos respuesta en JSON
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se encontró un plan de financiamiento']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
            }
        }

        public function deleteFinance() {
            if (!isset($_POST['id_financiamiento'])) {
                echo json_encode(['success' => false, 'message' => 'ID de financiamiento no recibido.']);
                return;
            }
    
            $id_financiamiento = intval($_POST['id_financiamiento']); // Convertir a entero para mayor seguridad
    
            $financiamiento = new Financiamiento();
            $resultado = $financiamiento->eliminarFinanciamiento($id_financiamiento);
    
            if ($resultado) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el financiamiento.']);
            }
        }
    
        public function getPlanFinanciamiento() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_plan'])) { 
                $idPlan = intval($_POST['id_plan']); 
        
                $financiamientoModel = new Financiamiento(); 
                $plan = $financiamientoModel->getPlan($idPlan); 

                // Obtener las variantes del plan
                $variantes = $financiamientoModel->getVariante($idPlan);
        
                if ($plan) {
                    echo json_encode(['success' => true, 'plan' => $plan,
                    'variantes' => $variantes]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se encontró un plan de financiamiento']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
            }
        }
        
        public function newPagofinance()
        {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $tiempoInicio = microtime(true); // 🔍 DEBUG: Inicio
                error_log("🔍 [TIMING] Inicio newPagofinance: " . date('H:i:s'));

                $documentoIdentidad = $_POST['documento_identidad'] ?? null;
                $metodoPago = $_POST['metodo_pago'] ?? null;
                $totalPagar = $_POST['total_pagar'] ?? null;
                $efectivoRecibido = $_POST['efectivo_recibido'] ?? null;
                $monedaEfectivo = $_POST['moneda_efectivo'] ?? null;
                $vuelto = $_POST['vuelto'] ?? null;
                $cuotasJson = $_POST['cuotas'] ?? '[]';
                $cuotasSeleccionadas = json_decode($cuotasJson, true);

                error_log("🔍 [TIMING] Después de parsear datos: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms");

                // ✅ OPTIMIZADO: Calcular y agregar el descuento_aplicado a cada cuota EN BATCH
                $financiamientoModel = new Financiamiento();

                // Extraer todos los IDs de cuotas
                $idsCuotas = array_column($cuotasSeleccionadas, 'idCuota');

                // Obtener información de TODAS las cuotas en UNA SOLA consulta
                $cuotasInfo = $financiamientoModel->obtenerInfoCuotasBatch($idsCuotas);

                // Ahora iterar y asignar la información
                foreach ($cuotasSeleccionadas as &$cuota) {
                    $idCuota = $cuota['idCuota'];

                    if (isset($cuotasInfo[$idCuota])) {
                        $cuotaInfo = $cuotasInfo[$idCuota];

                        // Obtener el descuento_cuota del producto
                        $descuentoCuotaProducto = isset($cuotaInfo['descuento_cuota']) ? floatval($cuotaInfo['descuento_cuota']) : 0.00;

                        // El descuento aplicado es el menor entre la comisión y el descuento del producto
                        $comisionCanalDigital = isset($cuotaInfo['comision_canal_digital']) ? floatval($cuotaInfo['comision_canal_digital']) : 0.00;
                        $descuentoAplicado = min($descuentoCuotaProducto, $comisionCanalDigital);

                        // Agregar al array de cuota
                        $cuota['descuento_aplicado'] = $descuentoAplicado;
                        $cuota['comision_canal_digital'] = $comisionCanalDigital;
                        $cuota['monto_cuota_base'] = isset($cuotaInfo['monto_cuota_base']) ? $cuotaInfo['monto_cuota_base'] : $cuota['monto'];
                    }

                    // NUEVO: Manejar moras pendientes
                    if (isset($cuota['moraPendiente']) && $cuota['moraPendiente'] === true) {
                        // Si la mora está marcada como pendiente, guardar el monto original y poner mora en 0
                        $cuota['monto_mora_original'] = $cuota['mora'] ?? 0;
                        $cuota['mora'] = 0; // No se cobra la mora ahora
                        $cuota['estado_mora'] = 'pendiente';
                        error_log("🔍 [MORA PENDIENTE] Cuota {$cuota['idCuota']}: mora original = {$cuota['monto_mora_original']}, mora actual = 0");
                    } else {
                        // Mora se paga normalmente
                        $cuota['estado_mora'] = 'pagada';
                        $cuota['monto_mora_original'] = null;
                        error_log("🔍 [MORA PAGADA] Cuota {$cuota['idCuota']}: mora = {$cuota['mora']}");
                    }
                }
                unset($cuota); // Romper la referencia

                error_log("🔍 [TIMING] Después de obtener info cuotas batch: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms");

                // Obtener el rol del usuario desde la sesión 🌎
                $rolUsuario = $_SESSION['id_rol'] ?? null; 

                if (!$documentoIdentidad || !$metodoPago || empty($cuotasSeleccionadas)) {
                    echo json_encode(['success' => false, 'message' => 'Datos incompletos para procesar el pago']);
                    return;
                }
    
                error_log("🔍 [TIMING] ANTES de actualizarCuotas: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms");

                // Verificar si el usuario tiene permisos para actualizar cuotas 🌎
                if ($rolUsuario == 1 || $rolUsuario == 3) {
                    $resultado = $financiamientoModel->actualizarCuotas($cuotasSeleccionadas);
                } else {
                    // Si es rol 2, no actualiza las cuotas pero devuelve éxito para continuar 🌎
                    $resultado = ['success' => true, 'message' => 'Cuotas pendientes de aprobación'];
                }

                error_log("🔍 [TIMING] DESPUÉS de actualizarCuotas: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms"); 

                // Verificamos si los datos necesarios están completos
                if (!$documentoIdentidad || !$metodoPago || empty($cuotasSeleccionadas)) {
                    echo json_encode(['success' => false, 'message' => 'Datos incompletos para procesar el pago']);
                    return;
                }

                // Obtenemos el id_conductor a partir del documento de identidad
                $conductorModel = new Conductor(); // Llamamos al modelo Conductor
                $idConductor = $conductorModel->buscarPorDocumento($documentoIdentidad); // Nueva función en el modelo Conductor
                
                $idCliente = null;

                // MODIFICADO: Si no encontramos el conductor, buscamos en la tabla clientes_financiar
                if (!$idConductor) {
                    // MODIFICADO: Instanciamos el modelo Cliente o usamos el método desde el modelo adecuado
                    $clienteModel = new Cliente(); // Asumiendo que existe el modelo Cliente
                    $cliente = $clienteModel->obtenerPorDni($documentoIdentidad);
                    
                    // MODIFICADO: Si encontramos el cliente, tomamos su ID
                    if ($cliente) {
                        $idCliente = $cliente['id']; // MODIFICADO: Obtenemos el id del cliente
                    } else {
                        echo json_encode(['success' => false, 'message' => 'No se encontró un conductor o cliente con ese documento']);
                        return;
                    }
                }

                // Obtenemos el id_asesor desde la sesión
                $idAsesor = $_SESSION['usuario_id'] ?? null;
                if (!$idAsesor) {
                    echo json_encode(['success' => false, 'message' => 'No se pudo obtener el ID del usuario']);
                    return;
                }

                if ($resultado['success']) {
                    // MODIFICADO: Pasamos también el idCliente al método newPago
                    $estado = ($rolUsuario == 1 || $rolUsuario == 3) ? 1 : 0;

                    $pagoResult = $financiamientoModel->newPago(
                        $idConductor,
                        $idAsesor,
                        $totalPagar,
                        null,
                        $efectivoRecibido,
                        $vuelto,
                        $monedaEfectivo,
                        null,
                        $idCliente, // MODIFICADO: Agregado el idCliente
                        $metodoPago,
                        $estado 
                    );
                    // *** NUEVO: Registrar las cuotas seleccionadas en detalle_pago_financiamiento ***
                    if ($pagoResult['success'] && isset($pagoResult['id_pago'])) {  
                        $idPago = $pagoResult['id_pago']; // Asignamos el id_pago retornado
                        // Si el rol es 2, guardar en pagos_pendientes_financiamiento 🌎
                        if ($rolUsuario == 2) { 
                            $financiamientoModel->guardarPagoPendiente($idPago, $cuotasJson); 
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => $pagoResult['message']]);
                        return;
                    }
                    
                    $financiamientoModel->newDetallePago($cuotasSeleccionadas, $idPago);

                    // ===== NUEVO: Aplicar puntos si el pago es directo (roles 1 y 3) =====
                    error_log("🔍 [TIMING] ANTES de aplicar puntos: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms");

                    if ($rolUsuario == 1 || $rolUsuario == 3) {
                        $scoreService = new ScoreService();

                        // Preparar cuotas con fechas para el servicio de puntos
                        $cuotasConFechas = [];
                        foreach ($cuotasSeleccionadas as $cuota) {
                            $cuotasConFechas[] = [
                                'idCuota' => $cuota['idCuota'],
                                'fechaPago' => date('Y-m-d'), // Fecha actual del registro
                                'fechaVencimiento' => $cuota['fechaVencimiento']
                            ];
                        }

                        $scoreService->aplicarPuntosEnRegistroDirecto(
                            $idPago,
                            $idConductor,
                            $idCliente,
                            $cuotasConFechas
                        );
                    }

                    error_log("🔍 [TIMING] DESPUÉS de aplicar puntos: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms");
                    // ===== FIN NUEVO =====

                    error_log("🔍 [TIMING] ANTES de generar PDF: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms");

                    $reportController = new ReportFinanciamientoController();
                    $pdfBase64 = $reportController->generateNotaVenta(
                        $idConductor ?: $idCliente, // MODIFICADO: Usamos el operador ternario para pasar el id disponible
                        $idAsesor,
                        $cuotasSeleccionadas,
                        $idPago,
                        $monedaEfectivo
                    );

                    error_log("🔍 [TIMING] DESPUÉS de generar PDF: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms");

                    $tiempoTotal = round((microtime(true) - $tiempoInicio) * 1000, 2);
                    error_log("🔍 [TIMING] TOTAL newPagofinance: {$tiempoTotal}ms");

                    echo json_encode([
                        'success' => true,
                        'message' => ($rolUsuario == 2) ? 'Pago registrado como pendiente' : 'Pago realizado con éxito',
                        'pdf' => $pdfBase64
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => $resultado['message']]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            }
        }

        public function getReportFinance() {

            $financiamiento = new Financiamiento();

            // Parámetros de DataTables para server-side processing
            $draw = isset($_POST['draw']) ? (int)$_POST['draw'] : 1;
            $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
            $length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
            $searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';

            // Parámetros de filtro de fechas
            $fechaInicio = isset($_POST['fechaInicio']) ? trim($_POST['fechaInicio']) : '';
            $fechaFin = isset($_POST['fechaFin']) ? trim($_POST['fechaFin']) : '';

            // Obtener datos paginados
            $resultados = $financiamiento->obtenerReportesPagos($start, $length, $searchValue, $fechaInicio, $fechaFin);

            // Contar total de registros sin filtrar
            $totalRegistros = $financiamiento->contarReportes('', '', '');

            // Contar total de registros con filtros aplicados
            $totalFiltrados = $financiamiento->contarReportes($searchValue, $fechaInicio, $fechaFin);

            // Respuesta en formato DataTables server-side
            echo json_encode([
                'draw' => $draw,
                'recordsTotal' => $totalRegistros,
                'recordsFiltered' => $totalFiltrados,
                'data' => $resultados
            ]);
        }

        public function deleteReportFinance()
        {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $idPago = $_POST['idpagos_financiamiento'];

                // Asegurarte de que el ID sea válido
                if (!empty($idPago) && is_numeric($idPago)) {
                    
                    $financiamiento = new Financiamiento();

                    if ($financiamiento->deleteReportFinance($idPago)) {
                        echo json_encode(['status' => 'success', 'message' => 'Pago eliminado correctamente.']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'No se pudo eliminar el pago.']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'ID de pago inválido.']);
                }
            }
        }

        public function deleteMassive()
        {
            // LÍNEA NUEVA: Verificamos que la petición sea mediante POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // LÍNEA NUEVA: Verificamos que se hayan enviado IDs para eliminar
                if (isset($_POST['ids']) && is_array($_POST['ids'])) {
                    $ids = $_POST['ids'];
                    $financiamiento = new Financiamiento();
                    $errores = [];
                    $eliminados = 0;
                    
                    // LÍNEA NUEVA: Recorremos cada ID y lo eliminamos
                    foreach ($ids as $id) {
                        // LÍNEA NUEVA: Validamos que el ID sea numérico
                        if (is_numeric($id)) {
                            // LÍNEA NUEVA: Intentamos eliminar el pago
                            if ($financiamiento->deleteReportFinance($id)) {
                                $eliminados++;
                            } else {
                                // LÍNEA NUEVA: Si hay error, lo guardamos
                                $errores[] = "No se pudo eliminar el pago con ID: $id";
                            }
                        } else {
                            // LÍNEA NUEVA: Si el ID no es válido, lo registramos
                            $errores[] = "ID inválido: $id";
                        }
                    }
                    
                    // LÍNEA NUEVA: Preparamos la respuesta
                    if (count($errores) === 0) {
                        // LÍNEA NUEVA: Si no hubo errores, devolvemos éxito
                        echo json_encode([
                            'status' => 'success',
                            'message' => "Se eliminaron $eliminados pagos correctamente."
                        ]);
                    } else {
                        // LÍNEA NUEVA: Si hubo errores, devolvemos la lista
                        echo json_encode([
                            'status' => 'error',
                            'message' => "Se eliminaron $eliminados pagos, pero hubo errores: " . implode(", ", $errores)
                        ]);
                    }
                } else {
                    // LÍNEA NUEVA: Si no se proporcionaron IDs, devolvemos error
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'No se proporcionaron IDs de pagos para eliminar.'
                    ]);
                }
            } else {
                // LÍNEA NUEVA: Si la petición no es POST, devolvemos error
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Método de solicitud no válido.'
                ]);
            }
        }

        public function obtenerFinanciamientoParaEditar()
        {
            // Agregar logs para depuración
            error_log("Método obtenerFinanciamientoParaEditar llamado");
            error_log("Método HTTP: " . $_SERVER['REQUEST_METHOD']);
            error_log("Parámetros GET: " . print_r($_GET, true));
            error_log("Parámetros POST: " . print_r($_POST, true));
            
            try {
                // Verificar si el ID viene por GET o POST
                $idFinanciamiento = isset($_GET['id_financiamiento']) ? $_GET['id_financiamiento'] : 
                                (isset($_POST['id_financiamiento']) ? $_POST['id_financiamiento'] : null);
                
                if (!$idFinanciamiento) {
                    error_log("Error: ID de financiamiento no proporcionado");
                    echo json_encode(['success' => false, 'message' => 'ID de financiamiento no proporcionado']);
                    return;
                }
                
                error_log("ID Financiamiento: " . $idFinanciamiento);
                
                // Obtener los datos del financiamiento
                $financiamiento = new Financiamiento();
                $resultado = $financiamiento->obtenerFinanciamientoPorId($idFinanciamiento);
                
                if ($resultado) {
                    error_log("Financiamiento encontrado: " . print_r($resultado, true));
                    echo json_encode(['success' => true, 'financiamiento' => $resultado]);
                } else {
                    error_log("No se encontró el financiamiento con ID: " . $idFinanciamiento);
                    echo json_encode(['success' => false, 'message' => 'No se encontró el financiamiento']);
                }
            } catch (Exception $e) {
                error_log("Excepción: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        // Método para actualizar un financiamiento
        // Añade esto al inicio del método actualizarFinanciamiento en FinanciamientoController.php
        public function actualizarFinanciamiento()
        {
            // Agregar logs para depuración
            error_log("Método actualizarFinanciamiento llamado");
            error_log("Método HTTP: " . $_SERVER['REQUEST_METHOD']);
            error_log("Parámetros GET: " . print_r($_GET, true));
            error_log("Parámetros POST: " . print_r($_POST, true));
            
            try {
                // Verificar si los datos vienen por GET o POST
                $idFinanciamiento = isset($_POST['id_financiamiento']) ? $_POST['id_financiamiento'] : 
                                (isset($_GET['id_financiamiento']) ? $_GET['id_financiamiento'] : null);
                $codigoAsociado = isset($_POST['codigo_asociado']) ? $_POST['codigo_asociado'] : 
                                (isset($_GET['codigo_asociado']) ? $_GET['codigo_asociado'] : null);
                $grupoFinanciamiento = isset($_POST['grupo_financiamiento']) ? $_POST['grupo_financiamiento'] : 
                                    (isset($_GET['grupo_financiamiento']) ? $_GET['grupo_financiamiento'] : null);
                $estado = isset($_POST['estado']) ? $_POST['estado'] : 
                        (isset($_GET['estado']) ? $_GET['estado'] : null);
                
                // Validar que se recibieron los datos necesarios
                if (!$idFinanciamiento || !$codigoAsociado || !$grupoFinanciamiento || !$estado) {
                    error_log("Error: Faltan datos requeridos");
                    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
                    return;
                }
                
                error_log("Datos a actualizar: ID=$idFinanciamiento, Código=$codigoAsociado, Grupo=$grupoFinanciamiento, Estado=$estado");
                
                // Actualizar el financiamiento
                $financiamiento = new Financiamiento();
                $resultado = $financiamiento->actualizarFinanciamiento(
                    $idFinanciamiento,
                    $codigoAsociado,
                    $grupoFinanciamiento,
                    $estado
                );
                
                if ($resultado) {
                    error_log("Financiamiento actualizado correctamente");
                    echo json_encode(['success' => true, 'message' => 'Financiamiento actualizado correctamente']);
                } else {
                    error_log("No se pudo actualizar el financiamiento");
                    echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el financiamiento']);
                }
            } catch (Exception $e) {
                error_log("Excepción: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        public function cargarGruposFinanciamiento1() {
            // Modificar para usar la tabla planes_financiamiento en lugar de grupovehicular_financiamiento
            $sql = "SELECT idplan_financiamiento, nombre_plan FROM planes_financiamiento";
            $result = $this->conexion->query($sql);
            
            $grupos = [];
            while ($row = $result->fetch_assoc()) {
                $grupos[] = $row;
            }
            
            echo json_encode($grupos);
        }

        public function obtenerDatosFinanciamientoCliente() {
            // Verificar que se recibió el DNI
            if (!isset($_POST['dni'])) {
                echo json_encode(['error' => 'DNI no proporcionado']);
                return;
            }
            
            $dni = $_POST['dni'];
            
            try {
                // Obtener cliente por DNI
                $clienteModel = new Cliente();
                $cliente = $clienteModel->obtenerPorDni($dni);
                
                if (!$cliente) {
                    echo json_encode(['error' => 'Cliente no encontrado']);
                    return;
                }
                
                // Obtener financiamientos del cliente
                $id_cliente = $cliente['id'];
                $financiamientoModel = new Financiamiento();
                $financiamientos = $financiamientoModel->getFinanciamientoByCliente($id_cliente);
                
                // Si no hay financiamientos, devolver solo el cliente
                if (empty($financiamientos)) {
                    echo json_encode([
                        'success' => true,
                        'cliente' => $cliente,
                        'financiamientos' => null
                    ]);
                    return;
                }
                
                // Modelos adicionales para enriquecer los datos
                $cuotaModel = new CuotaFinanciamiento();
                $productoModel = new Productov2();
                
                // Añadir cuotas y producto a cada financiamiento
                foreach ($financiamientos as &$financiamiento) {
                    $id_financiamiento = $financiamiento['idfinanciamiento'];
                    
                    // Obtener cuotas asociadas
                    $financiamiento['cuotas'] = $cuotaModel->getCuotasforFinanciamientoList($id_financiamiento);
                    
                    // Obtener producto asociado
                    $id_producto = $financiamiento['idproductosv2'];
                    $financiamiento['producto'] = $productoModel->getProductsList($id_producto);
                }
                
                // Preparar respuesta completa
                $response = [
                    'success' => true,
                    'cliente' => $cliente,
                    'financiamientos' => $financiamientos
                ];
                
                // Devolver respuesta como JSON
                echo json_encode($response);
                
            } catch (Exception $e) {
                echo json_encode(['error' => 'Error al obtener datos del cliente: ' . $e->getMessage()]);
            }
        }

        public function getFinanciamientos_pendientes()
        {
            $financiamientoModel = new Financiamiento();
            $financiamientos = $financiamientoModel->getFinanciamientosPendientesYRechazados();

            $pendientes = 0;
            foreach ($financiamientos as $fin) {
                if ($fin['aprobado'] !== null && $fin['aprobado'] !== '' && $fin['aprobado'] == 0) {
                    $pendientes++;
                }
            }

            header('Content-Type: application/json');
            echo json_encode(['pendientes' => $pendientes]);
        }

      
        // Método para obtener los financiamientos pendientes y rechazados
        public function getFinanciamientosAprobar() {

            $financiamientoModel = new Financiamiento(); // 🚀 Cambio: instanciado aquí
            $conductorModel = new Conductor(); // 🚀 Cambio: instanciado aquí
            $clienteModel = new Cliente(); // 🚀 Cambio: instanciado aquí
            $productoModel = new Productov2();
            // Obtener financiamientos pendientes y rechazados
            $financiamientos = $financiamientoModel->getFinanciamientosPendientesYRechazados();
            
            $pendientes = [];
            $rechazados = [];
            
            foreach ($financiamientos as $financiamiento) {
                // Procesar solo si aprobado = 0 (pendiente) o aprobado = 2 (rechazado)
                if ($financiamiento['aprobado'] === null) {
                    continue;
                }
                
                // Obtener información del cliente o conductor asociado
                if (!empty($financiamiento['id_conductor'])) {
                    $conductor = $conductorModel->obtenerDetalleConductor($financiamiento['id_conductor']);
                    $financiamiento['conductor'] = $conductor;
                }
                
                if (!empty($financiamiento['id_cliente'])) {
                    $cliente = $clienteModel->getClienteById($financiamiento['id_cliente']);
                    $financiamiento['cliente'] = $cliente;
                }
                
                // Obtener información del producto
                if (!empty($financiamiento['idproductosv2'])) {
                    $producto = $productoModel->obtenerProductoPorId($financiamiento['idproductosv2']); 
                    $financiamiento['producto'] = $producto;
                }
                
                // Clasificar según estado
                if ($financiamiento['aprobado'] == 0) {
                    $pendientes[] = $financiamiento;
                } elseif ($financiamiento['aprobado'] == 2) {
                    $rechazados[] = $financiamiento;
                }
            }
            
            // Enviar respuesta JSON
            header('Content-Type: application/json');
            echo json_encode([
                'pendientes' => $pendientes,
                'rechazados' => $rechazados
            ]);
        }
    
        // Método para obtener detalles de un financiamiento específico
        public function getDetalleFinanciamiento() {
            $id = $_POST['id'] ?? 0;
            
            if (!$id) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
                return;
            }
            
            // Obtener el financiamiento
            $financiamiento = $this->financiamientoModel->getFinanciamientoById($id);
            
            if (!$financiamiento) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Financiamiento no encontrado']);
                return;
            }
            
            // Obtener información del cliente o conductor asociado
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
            
            // Enviar respuesta JSON
            header('Content-Type: application/json');
            echo json_encode($financiamiento);
        }
    
        // Método para aprobar un financiamiento
        public function financiamientoAprobado() {
            
            $id = $_POST['id'] ?? 0;
            

            if (!$id) {
            
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
                return;
            }

            // Obtener el financiamiento
            $financiamiento = $this->financiamientoModel->getFinanciamientoById($id);

            if (!$financiamiento) {
                
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Financiamiento no encontrado']);
                return;
            }

            // Obtener información del producto
            $idProducto = $financiamiento['idproductosv2'];
            $cantidadProducto = $financiamiento['cantidad_producto'];
            $usuarioId = $financiamiento['usuario_id'];

            // Consultar datos del producto
            $queryProducto = "SELECT nombre, codigo, codigo_barra, razon_social, cantidad FROM productosv2 WHERE idproductosv2 = $idProducto";
            $resultProducto = $this->conexion->query($queryProducto);
            $producto = $resultProducto->fetch_assoc();

            if (!$producto) {
            
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Producto no encontrado']);
                return;
            }

            // Verificar si hay stock suficiente
            if ($producto['cantidad'] < $cantidadProducto) {
            
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Stock insuficiente para aprobar el financiamiento']);
                return;
            }

            // Preparar datos para registrar movimiento
            $codigoProducto = $producto['codigo'] ?: $producto['codigo_barra'];
            $nombreProducto = $producto['nombre'];
            $razonSocial = $producto['razon_social'];

            // Registrar movimiento
            $this->reportesModel->registrarMovimiento(
                $usuarioId,
                $idProducto, 
                $codigoProducto, 
                $nombreProducto, 
                "Salida", 
                "financiamiento", 
                $cantidadProducto, 
                $razonSocial
            );

            // Descontar stock del producto
            $nuevaCantidad = $producto['cantidad'] - $cantidadProducto;
            $queryUpdateStock = "UPDATE productosv2 SET cantidad = $nuevaCantidad WHERE idproductosv2 = $idProducto";
            $resultUpdateStock = $this->conexion->query($queryUpdateStock);

            if (!$resultUpdateStock) {
                
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el stock del producto']);
                return;
            }

            // Actualizar estado del financiamiento a aprobado
            $queryUpdateFinanciamiento = "UPDATE financiamiento SET aprobado = 1 WHERE idfinanciamiento = $id";
            $resultUpdateFinanciamiento = $this->conexion->query($queryUpdateFinanciamiento);

            if (!$resultUpdateFinanciamiento) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el estado del financiamiento']);
                return;
            }

            // Registrar comisión por financiamiento aprobado
            $this->registrarComisionFinanciamiento($financiamiento);

            // Registrar en log_aprobaciones_financiamiento
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $usuario_aprobacion = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : null;

            if ($usuario_aprobacion) {
                $queryLog = "INSERT INTO log_aprobaciones_financiamiento (id_financiamiento, accion, usuario_id, motivo)
                             VALUES ($id, 'aprobado', $usuario_aprobacion, NULL)";
                $resultLog = $this->conexion->query($queryLog);

                if (!$resultLog) {
                    error_log("Error al guardar en log_aprobaciones_financiamiento: " . $this->conexion->error);
                }
            }

            // Responder éxito

            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Financiamiento aprobado correctamente']);


        }

        // Método para rechazar un financiamiento
        public function rechazarFinanciamiento() {

            $id = $_POST['id'] ?? 0;
            $motivo = $_POST['motivo'] ?? '';

            if (!$id) {

                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
                return;
            }

            // Verificar si el financiamiento existe
            $queryVerificar = "SELECT idfinanciamiento FROM financiamiento WHERE idfinanciamiento = $id";
            $resultVerificar = $this->conexion->query($queryVerificar);

            if ($resultVerificar->num_rows === 0) {

                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Financiamiento no encontrado']);
                return;
            }

            // Actualizar el estado del financiamiento a rechazado (2)
            $queryActualizar = "UPDATE financiamiento SET aprobado = 2 WHERE idfinanciamiento = $id";
            $resultActualizar = $this->conexion->query($queryActualizar);

            if (!$resultActualizar) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Error al rechazar el financiamiento: ' . $this->conexion->error]);
                return;
            }

            // Registrar en log_aprobaciones_financiamiento (guarda motivo, usuario y fecha automáticamente)
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $usuario_rechazo = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : null;

            if ($usuario_rechazo) {
                $motivo_escaped = $this->conexion->real_escape_string($motivo);
                $queryLog = "INSERT INTO log_aprobaciones_financiamiento (id_financiamiento, accion, usuario_id, motivo)
                             VALUES ($id, 'rechazado', $usuario_rechazo, '$motivo_escaped')";
                $resultLog = $this->conexion->query($queryLog);

                if (!$resultLog) {
                    error_log("Error al guardar en log_aprobaciones_financiamiento (rechazo): " . $this->conexion->error);
                }
            } else {
                error_log("No se pudo guardar en log: usuario_rechazo es NULL. Sesión: " . print_r($_SESSION, true));
            }

            // Responder éxito
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Financiamiento rechazado correctamente']);

        }

     // Método para reactivar un financiamiento
    public function reactivaFinanciamiento() {
        $id = $_POST['id'] ?? 0;

        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
            return;
        }

        // Verificar si el financiamiento existe
        $queryVerificar = "SELECT idfinanciamiento FROM financiamiento WHERE idfinanciamiento = $id";
        $resultVerificar = $this->conexion->query($queryVerificar);
        
        if ($resultVerificar->num_rows === 0) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Financiamiento no encontrado']);
            return;
        }

        // Actualizar el estado del financiamiento a pendiente (0)
        $queryActualizar = "UPDATE financiamiento SET aprobado = 0 WHERE idfinanciamiento = $id";
        $resultActualizar = $this->conexion->query($queryActualizar);

        if (!$resultActualizar) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Error al reactivar el financiamiento']);
            return;
        }

        // Responder éxito
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Financiamiento reactivado correctamente']);
    }

    public function deleteFinanciamientoRechazado()
    {
        $id = $_POST['id'] ?? 0;
        $motivo = $_POST['motivo'] ?? '';

        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
            return;
        }

        // Obtener información del financiamiento antes de eliminarlo (para restaurar stock)
        $queryInfo = "SELECT idproductosv2, cantidad FROM financiamiento WHERE idfinanciamiento = ?";
        $stmtInfo = $this->conexion->prepare($queryInfo);
        if ($stmtInfo) {
            $stmtInfo->bind_param("i", $id);
            $stmtInfo->execute();
            $resultInfo = $stmtInfo->get_result();

            if ($resultInfo->num_rows > 0) {
                $financiamiento = $resultInfo->fetch_assoc();
                $idProducto = $financiamiento['idproductosv2'];
                $cantidad = $financiamiento['cantidad'];

                // Restaurar el stock del producto
                if ($idProducto && $cantidad > 0) {
                    $queryRestaurar = "UPDATE productosv2 SET CANTIDAD = CANTIDAD + ? WHERE idproductosv2 = ?";
                    $stmtRestaurar = $this->conexion->prepare($queryRestaurar);
                    if ($stmtRestaurar) {
                        $stmtRestaurar->bind_param("ii", $cantidad, $idProducto);
                        $stmtRestaurar->execute();
                        $stmtRestaurar->close();
                    }
                }
            }
            $stmtInfo->close();
        }

        // Registrar la eliminación en log_aprobaciones_financiamiento
        $motivo_escaped = $this->conexion->real_escape_string($motivo);
        $usuario_eliminacion = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : null;

        // Guardar en log_aprobaciones_financiamiento
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($usuario_eliminacion) {
            $queryLog = "INSERT INTO log_aprobaciones_financiamiento (id_financiamiento, accion, usuario_id, motivo)
                         VALUES ($id, 'eliminado', $usuario_eliminacion, '$motivo_escaped')";
            $resultLog = $this->conexion->query($queryLog);

            if (!$resultLog) {
                error_log("Error al guardar en log_aprobaciones_financiamiento (eliminación): " . $this->conexion->error);
            }
        } else {
            error_log("No se pudo guardar en log: usuario_eliminacion es NULL. Sesión: " . print_r($_SESSION, true));
        }

        // Eliminar el financiamiento
        $stmt = $this->conexion->prepare("DELETE FROM financiamiento WHERE idfinanciamiento = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Financiamiento eliminado correctamente y stock restaurado']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el financiamiento']);
            }
            $stmt->close();
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Error en la preparación de la consulta']);
        }
    }

    public function contarFinanciamientosRechazados()
    {
        $financiamientoModel = new Financiamiento();

        $financiamientos = $financiamientoModel->getFinanciamientosPendientesYRechazados();

        $contador = 0;

        foreach ($financiamientos as $financiamiento) {
            if ($financiamiento['aprobado'] == 2) {
                $contador++;
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['total' => $contador]);
    }

    public function verificarCodigoAsociado() {
        try {
            $codigoAsociado = $_POST['codigo_asociado'] ?? '';
            $grupoFinanciamiento = $_POST['grupo_financiamiento'] ?? '';
            
            if ($codigoAsociado === '' || $codigoAsociado === null || empty($grupoFinanciamiento)) {
                echo json_encode(['duplicado' => false]);
                return;
            }
            
            $financiamientoModel = new Financiamiento();
            $isDuplicado = $financiamientoModel->verificarCodigoAsociadoDuplicado($codigoAsociado, $grupoFinanciamiento);
            
            echo json_encode(['duplicado' => $isDuplicado]);
            
        } catch (Exception $e) {
            echo json_encode(['error' => 'Error al verificar código de asociado: ' . $e->getMessage()]);
        }
    }

    /**
    * Registra la comisión cuando se aprueba un financiamiento
    */
    public function registrarComisionFinanciamiento($financiamiento) {
        try {
            // Obtener el usuario que registró el financiamiento (no el que lo aprueba)
            $usuario_registra = $financiamiento['usuario_id'] ?? null;

            if (!$usuario_registra) {
                error_log("No se pudo obtener el usuario que registró el financiamiento para comisión");
                return;
            }
            
            // Instanciar modelo de comisión
            require_once "app/models/Comision.php";
            $comisionModel = new Comision();
            
            // Calcular comisión según reglas de negocio
            $datosComision = $comisionModel->calcularComisionFinanciamiento(
                $financiamiento['grupo_financiamiento'], 
                $financiamiento['id_variante']
            );
            
            // Solo registrar si aplica comisión
            if ($datosComision['aplica'] && $datosComision['monto'] > 0) {
                
                // Determinar tipo de vehículo para observaciones
                $tipoVehiculo = null;
                $observaciones = "Comisión por financiamiento";
                
                // Agregar detalles según el plan
                $planId = is_numeric($financiamiento['grupo_financiamiento']) ? intval($financiamiento['grupo_financiamiento']) : null;
                
                switch ($planId) {
                    case 19:
                        $tipoVehiculo = 'vehiculo';
                        $observaciones .= " - CREDI GO Vehículo";
                        if ($financiamiento['id_variante']) {
                            $observaciones .= " (Variante ID: {$financiamiento['id_variante']})";
                        }
                        break;
                    case 22:
                        $tipoVehiculo = 'moto';
                        $observaciones .= " - CREDI GO Moto";
                        break;
                    case 2:
                    case 3:
                    case 4:
                        $observaciones .= " - Financiamiento Celular";
                        break;
                    case 33:
                        $tipoVehiculo = 'moto';
                        $observaciones .= " - MOTO YA";
                        break;
                }
                
                // Registrar la comisión
                $comisionId = $comisionModel->registrarComision(
                    $usuario_registra,  // <- Usuario que registró el financiamiento
                    'financiamiento',
                    $financiamiento['idfinanciamiento'],
                    $datosComision['monto'],
                    $tipoVehiculo,
                    $observaciones,
                    $datosComision['moneda']
                );
                
                if ($comisionId) {
                    error_log("Comisión registrada exitosamente para usuario {$usuario_registra}: ID $comisionId, Monto: {$datosComision['moneda']} {$datosComision['monto']}");
                } else {
                    error_log("Error al registrar comisión para financiamiento {$financiamiento['idfinanciamiento']}");
                }
            }
            
        } catch (Exception $e) {
            error_log("Error al procesar comisión de financiamiento: " . $e->getMessage());
        }
    }

    public function marcarIncobrable()
    {
        if ($_POST) {
            $id_persona = $_POST['id_persona'];
            $tipo_persona = $_POST['tipo_persona'];
            
            try {
                // Marcar financiamientos como incobrables
                if ($tipo_persona == 'conductor') {
                    // Marcar financiamientos de productos como incobrables
                    $query1 = "UPDATE financiamiento SET incobrable = 1 WHERE id_conductor = ?";
                    $stmt1 = $this->conexion->prepare($query1);
                    $stmt1->bind_param("i", $id_persona);
                    $result1 = $stmt1->execute();
                    
                    // Marcar financiamientos de inscripción como incobrables
                    $query2 = "UPDATE conductor_regfinanciamiento SET incobrable = 1 WHERE id_conductor = ?";
                    $stmt2 = $this->conexion->prepare($query2);
                    $stmt2->bind_param("i", $id_persona);
                    $result2 = $stmt2->execute();
                    
                    $success = $result1 || $result2; // Al menos uno debe ejecutarse correctamente
                } else {
                    // Solo para clientes (productos)
                    $query = "UPDATE financiamiento SET incobrable = 1 WHERE id_cliente = ?";
                    $stmt = $this->conexion->prepare($query);
                    $stmt->bind_param("i", $id_persona);
                    $success = $stmt->execute();
                }
                
                if ($success) {
                    echo json_encode(['success' => true, 'message' => 'Financiamientos marcados como incobrables']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al marcar como incobrable']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
    }

        public function obtenerDetalleCuotas()
        {
            if ($_POST) {
                $id_persona = $_POST['id_persona'];
                $tipo_persona = $_POST['tipo_persona'];
                $filtro = $_POST['filtro'] ?? 'pendientes';
                
                try {
                    $detalles = [];
                    
                    if ($tipo_persona == 'conductor') {
                        // Consulta para conductores - inscripción a flota
                        $incobrable_condition_inscripcion = $filtro == 'incobrables' ? 'AND crf.incobrable = 1' : 'AND crf.incobrable = 0';
                        $incobrable_condition = $filtro == 'incobrables' ? 'AND f.incobrable = 1' : 'AND f.incobrable = 0';
                        
                        // Consulta para inscripción a flota
                        $query1 = "
                            SELECT 
                                DATE_FORMAT(cc.fecha_vencimiento, '%Y-%m') as mes_anio,
                                MONTHNAME(cc.fecha_vencimiento) as mes_nombre,
                                YEAR(cc.fecha_vencimiento) as anio,
                                cc.numero_cuota,
                                DATE_FORMAT(cc.fecha_vencimiento, '%d/%m/%Y') as fecha_vencimiento_formateada,
                                cc.monto_cuota as monto_individual,
                                'S/.' as moneda,
                                'Inscripción a Flota' as tipo_financiamiento
                            FROM conductor_cuotas cc
                            INNER JOIN conductor_regfinanciamiento crf ON cc.idconductor_Financiamiento = crf.idconductor_regfinanciamiento
                            WHERE crf.id_conductor = ? AND cc.fecha_vencimiento < CURDATE() AND cc.estado_cuota != 'pagado' $incobrable_condition_inscripcion
                            ORDER BY cc.fecha_vencimiento ASC
                        ";

                        // Consulta para productos de conductores
                        $query2 = "
                            SELECT 
                                DATE_FORMAT(cf.fecha_vencimiento, '%Y-%m') as mes_anio,
                                MONTHNAME(cf.fecha_vencimiento) as mes_nombre,
                                YEAR(cf.fecha_vencimiento) as anio,
                                cf.numero_cuota,
                                DATE_FORMAT(cf.fecha_vencimiento, '%d/%m/%Y') as fecha_vencimiento_formateada,
                                cf.monto as monto_individual,
                                p.nombre as nombre_producto,
                                f.moneda,
                                'productos' as tipo_financiamiento
                            FROM cuotas_financiamiento cf
                            INNER JOIN financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
                            INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                            WHERE f.id_conductor = ? AND cf.fecha_vencimiento < CURDATE() AND cf.estado = 'En Progreso' $incobrable_condition
                            ORDER BY cf.fecha_vencimiento ASC
                        ";
                    } else {
                        // Consulta para clientes - debe incluir campos individuales de cuotas
                        $incobrable_condition = $filtro == 'incobrables' ? 'AND f.incobrable = 1' : 'AND f.incobrable = 0';
                        $query2 = "
                            SELECT 
                                DATE_FORMAT(cf.fecha_vencimiento, '%Y-%m') as mes_anio,
                                MONTHNAME(cf.fecha_vencimiento) as mes_nombre,
                                YEAR(cf.fecha_vencimiento) as anio,
                                cf.numero_cuota,
                                DATE_FORMAT(cf.fecha_vencimiento, '%d/%m/%Y') as fecha_vencimiento_formateada,
                                cf.monto as monto_individual,
                                p.nombre as nombre_producto,
                                f.moneda,
                                'productos' as tipo_financiamiento
                            FROM cuotas_financiamiento cf
                            INNER JOIN financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
                            INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                            WHERE f.id_cliente = ? AND cf.fecha_vencimiento < CURDATE() AND cf.estado = 'En Progreso' $incobrable_condition
                            ORDER BY cf.fecha_vencimiento ASC
                        ";
                    }
                    
                    // Ejecutar consultas
                    $resultado = [];

                    // Procesar cuotas de inscripción (solo para conductores)
                    if ($tipo_persona == 'conductor' && isset($query1)) {
                        $stmt1 = $this->conexion->prepare($query1);
                        $stmt1->bind_param("i", $id_persona);
                        $stmt1->execute();
                        $result1 = $stmt1->get_result();
                        while ($row = $result1->fetch_assoc()) {
                            $key = $row['mes_anio'];
                            if (!isset($resultado[$key])) {
                                $resultado[$key] = [
                                    'mes' => $row['mes_nombre'] . ' ' . $row['anio'],
                                    'mes_ordenable' => $row['mes_anio'],
                                    'total' => 0,
                                    'cuotas' => []
                                ];
                            }
                            $resultado[$key]['total'] += $row['monto_individual'];
                            $resultado[$key]['cuotas'][] = [
                                'numero' => $row['numero_cuota'],
                                'fecha' => $row['fecha_vencimiento_formateada'],
                                'monto' => $row['monto_individual'],
                                'moneda' => $row['moneda'],
                                'tipo' => $row['tipo_financiamiento']
                            ];
                        }
                        $stmt1->close();
                    }

                    // Procesar cuotas de productos (para conductores y clientes)
                    $stmt2 = $this->conexion->prepare($query2);
                    $stmt2->bind_param("i", $id_persona);
                    $stmt2->execute();
                    $result2 = $stmt2->get_result();
                    while ($row = $result2->fetch_assoc()) {
                        $key = $row['mes_anio'];
                        if (!isset($resultado[$key])) {
                            $resultado[$key] = [
                                'mes' => $row['mes_nombre'] . ' ' . $row['anio'],
                                'mes_ordenable' => $row['mes_anio'],
                                'total' => 0,
                                'cuotas' => []
                            ];
                        }
                        $resultado[$key]['total'] += $row['monto_individual'];
                        $resultado[$key]['cuotas'][] = [
                            'numero' => $row['numero_cuota'],
                            'fecha' => $row['fecha_vencimiento_formateada'],
                            'monto' => $row['monto_individual'],
                            'moneda' => isset($row['moneda']) ? $row['moneda'] : 'S/.',
                            'tipo' => isset($row['nombre_producto']) ? $row['nombre_producto'] : 'Producto'
                        ];
                    }
                    $stmt2->close();

                    // Convertir a array indexado y ordenar por fecha (más reciente primero)
                    $detalles = array_values($resultado);
                    usort($detalles, function($a, $b) {
                        return strcmp($b['mes_ordenable'], $a['mes_ordenable']);
                    });
                    
                    echo json_encode(['success' => true, 'data' => $detalles]);
                    
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
                }
            }
        }

        // NUEVA FUNCIÓN: Marcar como cobrable (revertir incobrable)
        public function marcarComoCobrable()
        {
            if ($_POST) {
                $id_persona = $_POST['id_persona'] ?? null;
                $tipo_persona = $_POST['tipo_persona'] ?? null;

                if (!$id_persona || !$tipo_persona) {
                    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
                    return;
                }

                try {
                    mysqli_begin_transaction($this->conexion);

                    if ($tipo_persona == 'conductor') {
                        // Actualizar financiamientos de inscripción
                        $query1 = "UPDATE conductor_regfinanciamiento 
                                   SET incobrable = 0 
                                   WHERE id_conductor = ?";
                        $stmt1 = mysqli_prepare($this->conexion, $query1);
                        mysqli_stmt_bind_param($stmt1, 'i', $id_persona);
                        mysqli_stmt_execute($stmt1);
                        mysqli_stmt_close($stmt1);

                        // Actualizar financiamientos generales
                        $query2 = "UPDATE financiamiento 
                                   SET incobrable = 0 
                                   WHERE id_conductor = ?";
                        $stmt2 = mysqli_prepare($this->conexion, $query2);
                        mysqli_stmt_bind_param($stmt2, 'i', $id_persona);
                        mysqli_stmt_execute($stmt2);
                        mysqli_stmt_close($stmt2);
                    } else {
                        // Para clientes
                        $query = "UPDATE financiamiento 
                                  SET incobrable = 0 
                                  WHERE id_cliente = ?";
                        $stmt = mysqli_prepare($this->conexion, $query);
                        mysqli_stmt_bind_param($stmt, 'i', $id_persona);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                    }

                    mysqli_commit($this->conexion);
                    echo json_encode(['success' => true, 'message' => 'Marcado como cobrable exitosamente']);

                } catch (Exception $e) {
                    mysqli_rollback($this->conexion);
                    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
                }
            }
        }

        public function obtenerCuotasVencidasFiltradas()
        {
            if ($_POST) {
                $filtro = $_POST['filtro'] ?? 'pendientes';
                
                try {
                    $fecha_actual = date('Y-m-d');
                    $conductores_vencidos = [];
                    
                    // Filtro para incobrables
                    $incobrable_condition = $filtro == 'incobrables' ? 'AND f.incobrable = 1' : 'AND f.incobrable = 0';
                    
                    $query = "
                        SELECT 
                            c.id_conductor, 
                            CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS nombre_completo,
                            COUNT(cc.id_conductorcuota) AS num_cuotas,
                            SUM(cc.monto_cuota) AS deuda_total,
                            'Financiamiento de Inscripción' AS tipo_financiamiento,
                            c.numUnidad,
                            c.desvinculado,
                            c.telefono,
                            'S/.' AS moneda,
                            'conductor' AS tipo_persona 
                        FROM 
                            conductor_cuotas cc
                        INNER JOIN 
                            conductor_regfinanciamiento crf ON cc.idconductor_Financiamiento = crf.idconductor_regfinanciamiento
                        INNER JOIN 
                            conductores c ON crf.id_conductor = c.id_conductor
                        WHERE 
                            cc.fecha_vencimiento < '$fecha_actual' 
                            AND cc.estado_cuota != 'pagado'
                            " . ($filtro == 'incobrables' ? 'AND crf.incobrable = 1' : 'AND crf.incobrable = 0') . "
                        GROUP BY 
                            c.id_conductor

                        UNION 

                        SELECT
                            c.id_conductor,
                            CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS nombre_completo,
                            COUNT(cf.idcuotas_financiamiento) AS num_cuotas,
                            SUM(cf.monto) AS deuda_total,
                            p.nombre AS tipo_financiamiento,
                            c.numUnidad,
                            c.desvinculado,
                            c.telefono,
                            f.moneda,
                            'conductor' AS tipo_persona
                        FROM
                            cuotas_financiamiento cf
                        INNER JOIN
                            financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
                        INNER JOIN
                            conductores c ON f.id_conductor = c.id_conductor
                        INNER JOIN
                            productosv2 p ON f.idproductosv2 = p.idproductosv2
                        WHERE
                            cf.fecha_vencimiento < '$fecha_actual'
                            AND cf.estado = 'En Progreso'
                             AND f.estado_eliminado = 0
                            $incobrable_condition
                        GROUP BY
                            c.id_conductor, p.nombre, f.moneda, c.numUnidad, c.desvinculado, c.telefono, c.nombres, c.apellido_paterno, c.apellido_materno

                        UNION
                    
                        SELECT
                            cl.id AS id_conductor,
                            CONCAT(cl.nombres, ' ', cl.apellido_paterno, ' ', cl.apellido_materno) AS nombre_completo,
                            COUNT(cf.idcuotas_financiamiento) AS num_cuotas,
                            SUM(cf.monto) AS deuda_total,
                            p.nombre AS tipo_financiamiento,
                            NULL AS numUnidad,
                            0 AS desvinculado,
                            cl.telefono,
                            f.moneda,
                            'cliente' AS tipo_persona
                        FROM
                            cuotas_financiamiento cf
                        INNER JOIN
                            financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
                        INNER JOIN
                            clientes_financiar cl ON f.id_cliente = cl.id
                        INNER JOIN
                            productosv2 p ON f.idproductosv2 = p.idproductosv2
                        WHERE
                            cf.fecha_vencimiento < '$fecha_actual'
                            AND cf.estado = 'En Progreso'
                            AND f.id_cliente IS NOT NULL
                             AND f.estado_eliminado = 0
                            $incobrable_condition
                        GROUP BY
                            cl.id, p.nombre, f.moneda, cl.telefono, cl.nombres, cl.apellido_paterno, cl.apellido_materno 
                    ";
                    
                    $result = $this->conexion->query($query);
                    while ($row = $result->fetch_assoc()) {
                        $conductores_vencidos[] = $row;
                    }
                    
                    echo json_encode(['success' => true, 'data' => $conductores_vencidos]);

                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
                }
            }
        }

        public function getFinanciamientosEliminadosPapelera()
        {
            try {
                $financiamientosEliminados = $this->financiamientoModel->getFinanciamientosEliminados();
                echo json_encode(['success' => true, 'data' => $financiamientosEliminados]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error al obtener los financiamientos eliminados: ' . $e->getMessage()]);
            }
        }

        public function restaurarFinanciamiento()
        {
            if (isset($_POST['id_financiamiento'])) {
                $id_financiamiento = $_POST['id_financiamiento'];
                $resultado = $this->financiamientoModel->restaurarFinanciamiento($id_financiamiento);
                if ($resultado) {
                    echo json_encode(['success' => true, 'message' => 'Financiamiento restaurado con éxito.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se pudo restaurar el financiamiento.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'ID de financiamiento no proporcionado.']);
            }
        }

        public function eliminarPermanentemente()
        {
            if (isset($_POST['id_financiamiento'])) {
                $id_financiamiento = $_POST['id_financiamiento'];
                $resultado = $this->financiamientoModel->eliminarPermanentemente($id_financiamiento);
                if ($resultado) {
                    echo json_encode(['success' => true, 'message' => 'Financiamiento eliminado permanentemente.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el financiamiento permanentemente.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'ID de financiamiento no proporcionado.']);
            }
        }

        public function vaciarPapelera()
        {
            try {
                $resultado = $this->financiamientoModel->vaciarPapelera();
                if ($resultado['success']) {
                    echo json_encode([
                        'success' => true,
                        'message' => "Se eliminaron permanentemente {$resultado['eliminados']} financiamientos."
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al vaciar la papelera: ' . $resultado['error']]);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error al vaciar la papelera: ' . $e->getMessage()]);
            }
        }

        // NUEVOS MÉTODOS para entregar vehículo
    
        public function obtenerProductosVehiculos()
        {
            try {
                // Filtrar productos que pertenezcan a categoría "Vehículo" o "Vehículos"
                // Si el campo 'codigo' es NULL o está vacío, usar 'codigo_barra'
                $query = "SELECT idproductosv2, 
                                nombre, 
                                COALESCE(NULLIF(TRIM(codigo), ''), codigo_barra) AS codigo,
                                cantidad, 
                                precio_venta, 
                                categoria 
                        FROM productosv2 
                        WHERE LOWER(TRIM(categoria)) LIKE '%vehicul%' 
                        AND estado = '1'
                        ORDER BY nombre";
                
                $result = mysqli_query($this->conexion, $query);
                
                $productos = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $productos[] = $row;
                }
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'productos' => $productos
                ]);
                
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al obtener productos vehiculares: ' . $e->getMessage()
                ]);
            }
        }
        
        public function buscarProductosVehiculos()
        {
            try {
                $searchTerm = $_GET['searchTerm'] ?? '';
                
                $query = "SELECT idproductosv2, nombre, codigo, cantidad, precio_venta, categoria 
                        FROM productosv2 
                        WHERE LOWER(TRIM(categoria)) LIKE '%vehicul%' 
                        AND estado = '1'
                        AND (LOWER(nombre) LIKE ? OR LOWER(codigo) LIKE ?)
                        ORDER BY nombre";
                
                $stmt = mysqli_prepare($this->conexion, $query);
                $searchParam = '%' . strtolower($searchTerm) . '%';
                mysqli_stmt_bind_param($stmt, 'ss', $searchParam, $searchParam);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                $productos = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $productos[] = $row;
                }
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'productos' => $productos
                ]);
                
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al buscar productos vehiculares: ' . $e->getMessage()
                ]);
            }
        }
        
        public function entregarVehiculo()
        {
            try {
                $idProducto = $_POST['id_producto'] ?? null;
                $idFinanciamiento = $_POST['id_financiamiento'] ?? null;
                $fechaEntrega = $_POST['fecha_entrega'] ?? null; // ✅ NUEVO

                if (!$idProducto || !$idFinanciamiento) {
                    throw new Exception('Faltan parámetros requeridos');
                }

                // ✅ NUEVO: Si no se proporciona fecha, usar la fecha actual
                if (!$fechaEntrega) {
                    $fechaEntrega = date('Y-m-d');
                }

                // ✅ MODIFICADO: Actualizar el financiamiento con producto, fecha y estado_entrega
                $queryUpdate = "UPDATE financiamiento
                            SET idproductosv2 = ?,
                                fecha_entrega = ?,
                                estado_entrega = 'entregado',
                                cobrar_mora = 1
                            WHERE idfinanciamiento = ?";

                $stmt = mysqli_prepare($this->conexion, $queryUpdate);
                mysqli_stmt_bind_param($stmt, 'isi', $idProducto, $fechaEntrega, $idFinanciamiento);
                
                if (mysqli_stmt_execute($stmt)) {

                    // Actualizar cantidad del producto (restar 1)
                    $queryUpdateStock = "UPDATE productosv2 SET cantidad = cantidad - 1 WHERE idproductosv2 = ? AND cantidad > 0";
                    $stmtStock = mysqli_prepare($this->conexion, $queryUpdateStock);
                    mysqli_stmt_bind_param($stmtStock, 'i', $idProducto);

                    if (!mysqli_stmt_execute($stmtStock) || mysqli_stmt_affected_rows($stmtStock) === 0) {
                        throw new Exception('Error: El vehículo ya no tiene stock disponible');
                    }

                    mysqli_stmt_close($stmtStock);

                    // Actualizar cantidad_producto a 1 en el financiamiento
                    $queryUpdateCantidad = "UPDATE financiamiento SET cantidad_producto = '1' WHERE idfinanciamiento = ?";
                    $stmtCantidad = mysqli_prepare($this->conexion, $queryUpdateCantidad);
                    mysqli_stmt_bind_param($stmtCantidad, 'i', $idFinanciamiento);
                    mysqli_stmt_execute($stmtCantidad);
                    mysqli_stmt_close($stmtCantidad);
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Vehículo entregado con éxito',
                        'id_financiamiento' => $idFinanciamiento
                    ]);
                } else {
                    throw new Exception('Error al actualizar el financiamiento');
                }
                
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }

    /**
     * ✅ NUEVO: Registrar entrega de vehículo solo con fecha (sin cambiar producto)
     * Para financiamientos que ya tienen el producto asignado (no ID 37)
     * Solo actualiza: fecha_entrega y estado_entrega = 'entregado'
     */
    public function entregarVehiculoSoloFecha()
    {
        try {
            $idFinanciamiento = $_POST['id_financiamiento'] ?? null;
            $fechaEntrega = $_POST['fecha_entrega'] ?? null;

            if (!$idFinanciamiento || !$fechaEntrega) {
                throw new Exception('Faltan parámetros requeridos');
            }

            error_log("📅 Registrando entrega solo con fecha - ID Financiamiento: $idFinanciamiento, Fecha: $fechaEntrega");

            // Actualizar solo fecha_entrega y estado_entrega (mantener el producto actual)
            $queryUpdate = "UPDATE financiamiento
                           SET fecha_entrega = ?,
                               estado_entrega = 'entregado',
                               cobrar_mora = 1
                           WHERE idfinanciamiento = ?";

            $stmt = mysqli_prepare($this->conexion, $queryUpdate);
            mysqli_stmt_bind_param($stmt, 'si', $fechaEntrega, $idFinanciamiento);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);

                error_log("✅ Entrega registrada exitosamente - No se modificó el producto ni el stock");

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Entrega registrada con éxito',
                    'id_financiamiento' => $idFinanciamiento
                ]);
            } else {
                throw new Exception('Error al registrar la entrega');
            }

        } catch (Exception $e) {
            error_log("❌ Error al registrar entrega solo con fecha: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * NUEVA FUNCIÓN: Entregar vehículo CrediYango y generar cronograma de pagos
     * Se ejecuta cuando se marca un financiamiento CrediYango como entregado
     */
    public function entregarVehiculoCrediYango()
    {
        try {
            error_log("=== ENTREGA CREDIYANGO - INICIO ===");

            $idFinanciamiento = $_POST['id_financiamiento'] ?? null;
            $fechaEntrega = $_POST['fecha_entrega'] ?? null;
            $idProducto = $_POST['id_producto'] ?? null; // NUEVO: ID del producto real a entregar

            error_log("ID Financiamiento: " . $idFinanciamiento);
            error_log("Fecha Entrega: " . $fechaEntrega);
            error_log("ID Producto: " . $idProducto);

            if (!$idFinanciamiento || !$fechaEntrega) {
                throw new Exception('Faltan parámetros requeridos');
            }

            // Validar que el financiamiento existe y es CrediYango
            $query = "SELECT f.*, p.nombre_plan, p.tasa_interes as tasa, p.frecuencia_pago,
                      p.cantidad_cuotas as cuotas, p.monto_cuota
                      FROM financiamiento f
                      LEFT JOIN planes_financiamiento p ON f.grupo_financiamiento = p.idplan_financiamiento
                      WHERE f.idfinanciamiento = ?";

            $stmt = mysqli_prepare($this->conexion, $query);
            mysqli_stmt_bind_param($stmt, 'i', $idFinanciamiento);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $financiamiento = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if (!$financiamiento) {
                throw new Exception('Financiamiento no encontrado');
            }

            if ($financiamiento['grupo_financiamiento'] != 45) {
                throw new Exception('Este financiamiento no es CrediYango');
            }

            // Calcular fecha de inicio de pagos (fecha_entrega + 7 días)
            $fechaEntregaObj = new DateTime($fechaEntrega);
            $fechaInicioPagos = clone $fechaEntregaObj;
            $fechaInicioPagos->add(new DateInterval('P7D'));
            $fechaInicioPagosStr = $fechaInicioPagos->format('Y-m-d');

            // Iniciar transacción
            mysqli_begin_transaction($this->conexion);

            // ✅ MODIFICADO: Actualizar el financiamiento con las fechas, estado y estado_entrega
            // Ya no se cambia el producto, se mantiene el seleccionado en el registro
            $queryUpdate = "UPDATE financiamiento
                           SET fecha_entrega = ?,
                               fecha_inicio_pagos_calculada = ?,
                               fecha_inicio = ?,
                               estado = 'Vehiculo Entregado',
                               estado_entrega = 'entregado',
                               cobrar_mora = 1
                           WHERE idfinanciamiento = ?";

            $stmtUpdate = mysqli_prepare($this->conexion, $queryUpdate);
            mysqli_stmt_bind_param($stmtUpdate, 'sssi', $fechaEntrega, $fechaInicioPagosStr, $fechaInicioPagosStr, $idFinanciamiento);

            if (!mysqli_stmt_execute($stmtUpdate)) {
                throw new Exception('Error al actualizar el financiamiento');
            }
            mysqli_stmt_close($stmtUpdate);

            // ✅ NOTA: El stock ya fue reducido al momento del registro inicial
            // No es necesario reducir stock nuevamente aquí
            error_log("✅ Estado actualizado a 'entregado' - Stock ya fue reducido en el registro");

            // 2. Generar cronograma de pagos
            $cuotas = intval($financiamiento['cuotas']);
            $montoTotal = floatval($financiamiento['monto_total']);
            $cuotaInicial = floatval($financiamiento['cuota_inicial']);
            $montoSinIntereses = floatval($financiamiento['monto_sin_interes'] ?? 0);

            // ✅ CORREGIDO: Usar el monto del plan si existe, sino calcular
            // El plan CrediYango (ID 45) tiene monto_cuota definido en planes_financiamiento
            $montoCuotaPlan = isset($financiamiento['monto_cuota']) ? floatval($financiamiento['monto_cuota']) : null;

            if ($montoCuotaPlan && $montoCuotaPlan > 0) {
                // Usar el valor de cuota del plan
                $montoCuota = $montoCuotaPlan;
                error_log("✅ Usando monto_cuota del plan: $montoCuota");
            } else {
                // Calcular: (monto_total - cuota_inicial) / cuotas
                $montoCuota = ($montoTotal - $cuotaInicial) / $cuotas;
                error_log("⚠️ Calculando monto_cuota: $montoCuota");
            }

            error_log("Monto Total: $montoTotal");
            error_log("Cuota Inicial: $cuotaInicial");
            error_log("Monto Sin Intereses: $montoSinIntereses");
            error_log("Monto a financiar: " . ($montoTotal - $cuotaInicial));
            error_log("Monto por cuota FINAL: $montoCuota");
            error_log("Cantidad de cuotas: $cuotas");

            // ✅ VALIDACIÓN: Asegurarse de que montoCuota nunca sea 0
            if ($montoCuota <= 0) {
                throw new Exception("Error: El monto de cuota calculado es 0 o negativo. Verifique los datos del plan.");
            }

            // ✅ CORREGIDO: Usar frecuencia_pago en lugar de frecuencia
            $frecuencia = $financiamiento['frecuencia_pago'];
            $moneda = $financiamiento['moneda'];

            error_log("Frecuencia de pago: $frecuencia");
            error_log("Moneda: $moneda");

            // Calcular fechas de vencimiento según frecuencia
            $fechasVencimiento = [];
            $fechaBase = clone $fechaInicioPagos;

            for ($i = 0; $i < $cuotas; $i++) {
                $fechasVencimiento[] = $fechaBase->format('Y-m-d');

                // Incrementar según frecuencia
                switch (strtolower($frecuencia)) {
                    case 'semanal':
                        $fechaBase->add(new DateInterval('P7D'));
                        break;
                    case 'quincenal':
                        $fechaBase->add(new DateInterval('P15D'));
                        break;
                    case 'mensual':
                        $fechaBase->add(new DateInterval('P1M'));
                        break;
                    default:
                        $fechaBase->add(new DateInterval('P7D'));
                }
            }

            // 3. Insertar cuotas en la base de datos
            // ✅ CORREGIDO: Usar los mismos campos que CuotaFinanciamiento
            $queryInsertCuota = "INSERT INTO cuotas_financiamiento
                                (id_financiamiento, numero_cuota, monto, monto_cuota_base,
                                 comision_canal_digital, descuento_aplicado, moneda_cuota,
                                 fecha_vencimiento, estado, fecha_pago)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendiente', NULL)";

            $stmtInsertCuota = mysqli_prepare($this->conexion, $queryInsertCuota);

            // Calcular comisión según moneda
            $comisionCanalDigital = null;
            if ($moneda === 'S/.') {
                $comisionCanalDigital = 0.50;
            } elseif ($moneda === '$') {
                $comisionCanalDigital = 0.20;
            }
            $descuentoAplicado = 0.00;

            foreach ($fechasVencimiento as $index => $fechaVencimiento) {
                $numeroCuota = $index + 1;
                $montoCuotaBase = $montoCuota; // El monto original sin comisiones

                mysqli_stmt_bind_param(
                    $stmtInsertCuota,
                    'iiddddss',
                    $idFinanciamiento,
                    $numeroCuota,
                    $montoCuota,
                    $montoCuotaBase,
                    $comisionCanalDigital,
                    $descuentoAplicado,
                    $moneda,
                    $fechaVencimiento
                );

                if (!mysqli_stmt_execute($stmtInsertCuota)) {
                    throw new Exception('Error al insertar cuota ' . $numeroCuota);
                }
            }
            mysqli_stmt_close($stmtInsertCuota);

            error_log("✅ Cuotas insertadas exitosamente: " . count($fechasVencimiento));

            // Confirmar transacción
            mysqli_commit($this->conexion);

            error_log("✅ Transacción confirmada");
            error_log("=== ENTREGA CREDIYANGO - FIN EXITOSO ===");

            // Formatear fechas para respuesta
            $fechaEntregaFormateada = $fechaEntregaObj->format('d/m/Y');
            $fechaInicioPagosFormateada = $fechaInicioPagos->format('d/m/Y');

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Vehículo CrediYango entregado exitosamente',
                'fecha_entrega' => $fechaEntrega,
                'fecha_inicio_pagos' => $fechaInicioPagosStr,
                'fecha_entrega_formateada' => $fechaEntregaFormateada,
                'fecha_inicio_pagos_formateada' => $fechaInicioPagosFormateada,
                'total_pagos' => count($fechasVencimiento),
                'monto_cuota' => number_format($montoCuota, 2)
            ]);

        } catch (Exception $e) {
            // Revertir transacción en caso de error
            if (mysqli_connect_errno() === 0) {
                mysqli_rollback($this->conexion);
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function anularPago() {
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $idPago = $_POST['idpagos_financiamiento'];

          if (empty($idPago) || !is_numeric($idPago)) {
              echo json_encode(['status' => 'error', 'message' => 'ID de pago inválido']);
              return;
          }

          $financiamiento = new Financiamiento();
          $resultado = $financiamiento->anularPagoFinanciamiento($idPago);

          echo json_encode($resultado);
      }
    }

    // NUEVA FUNCIÓN: Obtener moras pendientes
    public function getMorasPendientes()
    {
        try {
            $conexion = (new Conexion())->getConexion();
            
            $query = "SELECT
                dp.iddetalle_pago_financiamiento,
                CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) as cliente_nombre,
                CONCAT(cf.nombres, ' ', cf.apellido_paterno, ' ', cf.apellido_materno) as cliente_financiar_nombre,
                p.nombre as producto_nombre,
                cuota.numero_cuota,
                dp.monto_mora_original as monto_mora,
                cuota.fecha_vencimiento,
                f.moneda,
                f.idfinanciamiento
            FROM detalle_pago_financiamiento dp
            JOIN cuotas_financiamiento cuota ON dp.id_cuota = cuota.idcuotas_financiamiento
            JOIN financiamiento f ON dp.idfinanciamiento = f.idfinanciamiento
            LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
            LEFT JOIN clientes_financiar cf ON f.id_cliente = cf.id
            JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
            WHERE dp.estado_mora = 'pendiente'
            ORDER BY cuota.fecha_vencimiento ASC";
            
            $result = mysqli_query($conexion, $query);
            $morasPendientes = [];
            
            while ($row = mysqli_fetch_assoc($result)) {
                $clienteNombre = $row['cliente_nombre'] ?: $row['cliente_financiar_nombre'];

                $morasPendientes[] = [
                    'id_mora_pendiente' => $row['iddetalle_pago_financiamiento'],
                    'cliente_nombre' => $clienteNombre,
                    'producto_nombre' => $row['producto_nombre'],
                    'numero_cuota' => $row['numero_cuota'],
                    'monto_mora' => $row['monto_mora'],
                    'fecha_vencimiento' => $row['fecha_vencimiento'],
                    'moneda' => $row['moneda'],
                    'id_financiamiento' => $row['idfinanciamiento']
                ];
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'moras' => $morasPendientes
            ]);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener moras pendientes: ' . $e->getMessage()
            ]);
        }
    }

    // NUEVA FUNCIÓN: Obtener contador de moras pendientes
    public function getContadorMorasPendientes()
    {
        try {
            $conexion = (new Conexion())->getConexion();
            
            $query = "SELECT COUNT(*) as cantidad FROM detalle_pago_financiamiento WHERE estado_mora = 'pendiente'";
            $result = mysqli_query($conexion, $query);
            $row = mysqli_fetch_assoc($result);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'cantidad' => $row['cantidad']
            ]);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener contador: ' . $e->getMessage()
            ]);
        }
    }

    // NUEVA FUNCIÓN: Pagar mora pendiente
    public function pagarMoraPendiente()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $idMoraPendiente = $_POST['id_mora_pendiente'] ?? null;
                $montoMora = $_POST['monto_mora'] ?? null;
                $metodoPago = $_POST['metodo_pago'] ?? 'efectivo';
                
                if (!$idMoraPendiente || !$montoMora) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Faltan datos obligatorios'
                    ]);
                    return;
                }
                
                $conexion = (new Conexion())->getConexion();
                mysqli_begin_transaction($conexion);
                
                // Obtener información del detalle de pago
                $queryDetalle = "SELECT * FROM detalle_pago_financiamiento WHERE iddetalle_pago_financiamiento = ? AND estado_mora = 'pendiente'";
                $stmtDetalle = mysqli_prepare($conexion, $queryDetalle);
                mysqli_stmt_bind_param($stmtDetalle, 'i', $idMoraPendiente);
                mysqli_stmt_execute($stmtDetalle);
                $resultDetalle = mysqli_stmt_get_result($stmtDetalle);
                $detallePago = mysqli_fetch_assoc($resultDetalle);
                
                if (!$detallePago) {
                    throw new Exception('Mora pendiente no encontrada');
                }
                
                // Crear nuevo pago solo para la mora
                $queryPago = "INSERT INTO pagos_financiamiento (id_financiamiento, monto_total, metodo_pago, fecha_pago, observaciones, estado, created_at, updated_at) VALUES (?, ?, ?, NOW(), 'Pago de mora pendiente', 1, NOW(), NOW())";
                $stmtPago = mysqli_prepare($conexion, $queryPago);
                mysqli_stmt_bind_param($stmtPago, 'ids', $detallePago['idfinanciamiento'], $montoMora, $metodoPago);
                mysqli_stmt_execute($stmtPago);
                $pagoMoraId = mysqli_insert_id($conexion);
                
                // Actualizar el detalle original
                $queryUpdate = "UPDATE detalle_pago_financiamiento SET estado_mora = 'pagada', mora = ?, updated_at = NOW() WHERE iddetalle_pago_financiamiento = ?";
                $stmtUpdate = mysqli_prepare($conexion, $queryUpdate);
                mysqli_stmt_bind_param($stmtUpdate, 'di', $montoMora, $idMoraPendiente);
                mysqli_stmt_execute($stmtUpdate);
                
                // Crear nuevo detalle para el pago de la mora
                $queryNuevoDetalle = "INSERT INTO detalle_pago_financiamiento (idfinanciamiento, id_cuota, mora, estado_mora, created_at, updated_at) VALUES (?, ?, ?, 'pagada', NOW(), NOW())";
                $stmtNuevoDetalle = mysqli_prepare($conexion, $queryNuevoDetalle);
                mysqli_stmt_bind_param($stmtNuevoDetalle, 'iid', $detallePago['idfinanciamiento'], $detallePago['id_cuota'], $montoMora);
                mysqli_stmt_execute($stmtNuevoDetalle);
                
                mysqli_commit($conexion);
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Mora pagada correctamente',
                    'pago_id' => $pagoMoraId
                ]);
                
            } catch (Exception $e) {
                mysqli_rollback($conexion);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al procesar pago de mora: ' . $e->getMessage()
                ]);
            }
        }
    }

    /**
     * NUEVO: Obtener resumen general de financiamientos por plan
     * Solo accesible para directores (rol 3)
     */
    public function obtenerResumenFinanciamientos()
    {
        try {
            error_log("=== OBTENER RESUMEN FINANCIAMIENTOS - INICIO ===");
            error_log("Sesión ID rol: " . ($_SESSION['id_rol'] ?? 'NO DEFINIDO'));
            
            // Verificar permisos
            if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 3) {
                error_log("Acceso denegado - Rol: " . ($_SESSION['id_rol'] ?? 'NO DEFINIDO'));
                echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
                return;
            }
            
            error_log("Permisos verificados correctamente");

            // Obtener resumen por plan de financiamiento
            $queryPlanes = "
                SELECT
                    pf.idplan_financiamiento,
                    pf.nombre_plan,
                    COUNT(DISTINCT
                        CASE
                            WHEN f.id_conductor IS NOT NULL AND f.id_conductor != 0 THEN CONCAT('C', f.id_conductor)
                            WHEN f.id_cliente IS NOT NULL AND f.id_cliente != 0 THEN CONCAT('L', f.id_cliente)
                            ELSE NULL
                        END
                    ) as total_conductores,
                    COUNT(f.idfinanciamiento) as total_financiamientos,
                    SUM(CAST(COALESCE(NULLIF(f.cantidad_producto, ''), '1') AS UNSIGNED)) as total_unidades,
                    SUM(f.monto_total) as monto_total_plan
                FROM financiamiento f
                INNER JOIN planes_financiamiento pf ON CAST(f.grupo_financiamiento AS UNSIGNED) = pf.idplan_financiamiento
                WHERE f.estado != 'eliminado'
                AND f.estado != 'rechazado'
                AND f.estado_eliminado = 0
                AND f.grupo_financiamiento REGEXP '^[0-9]+$'
                GROUP BY pf.idplan_financiamiento, pf.nombre_plan
                ORDER BY total_financiamientos DESC
            ";

            $stmt = $this->conexion->prepare($queryPlanes);
            $stmt->execute();
            $resultPlanes = $stmt->get_result();
            
            $resumenPlanes = [];
            $totalesGenerales = [
                'total_conductores' => 0,
                'total_financiamientos' => 0,
                'total_unidades' => 0,
                'monto_total' => 0
            ];

            while ($row = $resultPlanes->fetch_assoc()) {
                $resumenPlanes[] = $row;
                $totalesGenerales['total_financiamientos'] += $row['total_financiamientos'];
                $totalesGenerales['total_unidades'] += $row['total_unidades'];
                $totalesGenerales['monto_total'] += $row['monto_total_plan'];
            }

            // Obtener total único de conductores/clientes (sin duplicados entre planes)
            $queryTotalConductores = "
                SELECT COUNT(DISTINCT
                    CASE
                        WHEN f.id_conductor IS NOT NULL AND f.id_conductor != 0 THEN CONCAT('C', f.id_conductor)
                        WHEN f.id_cliente IS NOT NULL AND f.id_cliente != 0 THEN CONCAT('L', f.id_cliente)
                        ELSE NULL
                    END
                ) as total_conductores_unicos
                FROM financiamiento f
                WHERE f.estado != 'eliminado'
                AND f.estado != 'rechazado'
                AND f.estado_eliminado = 0
            ";
            $stmtTotal = $this->conexion->prepare($queryTotalConductores);
            $stmtTotal->execute();
            $resultTotal = $stmtTotal->get_result();
            $totalConductores = $resultTotal->fetch_assoc();
            $totalesGenerales['total_conductores'] = $totalConductores['total_conductores_unicos'];

            error_log("Datos procesados correctamente. Planes: " . count($resumenPlanes));
            error_log("Totales generales: " . json_encode($totalesGenerales));
            
            echo json_encode([
                'success' => true,
                'resumenPlanes' => $resumenPlanes,
                'totalesGenerales' => $totalesGenerales
            ]);
            
            error_log("=== OBTENER RESUMEN FINANCIAMIENTOS - FIN EXITOSO ===");

        } catch (Exception $e) {
            error_log("ERROR en obtenerResumenFinanciamientos: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener resumen: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * NUEVO: Obtener detalle de un plan específico de financiamiento
     * Solo accesible para directores (rol 3)
     */
    public function obtenerDetalleFinanciamientoPlan()
    {
        try {
            // Verificar permisos
            if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 3) {
                echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
                return;
            }

            $planId = $_GET['planId'] ?? null;
            
            if (!$planId) {
                echo json_encode(['success' => false, 'error' => 'ID de plan requerido']);
                return;
            }

            $query = "
                SELECT
                    COALESCE(c.nombres, cl.nombres) as nombres,
                    COALESCE(c.apellido_paterno, cl.apellido_paterno) as apellido_paterno,
                    COALESCE(c.apellido_materno, cl.apellido_materno) as apellido_materno,
                    COALESCE(c.nro_documento, cl.n_documento) as nro_documento,
                    c.numUnidad as numero_unidad,
                    p.nombre as producto_nombre,
                    COALESCE(NULLIF(f.cantidad_producto, ''), '1') as cantidad_unidades,
                    f.monto_total,
                    f.estado,
                    f.fecha_creacion as fecha_registro,
                    f.moneda,
                    CASE
                        WHEN f.id_conductor IS NOT NULL AND f.id_conductor != 0 THEN 'Conductor'
                        WHEN f.id_cliente IS NOT NULL AND f.id_cliente != 0 THEN 'Cliente'
                        ELSE 'Desconocido'
                    END as tipo_persona
                FROM financiamiento f
                LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
                LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
                LEFT JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                WHERE CAST(f.grupo_financiamiento AS UNSIGNED) = ?
                AND f.estado != 'eliminado'
                AND f.estado_eliminado = 0
                AND f.grupo_financiamiento REGEXP '^[0-9]+$'
                ORDER BY COALESCE(c.nombres, cl.nombres) ASC
            ";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $planId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $detalles = [];
            while ($row = $result->fetch_assoc()) {
                $detalles[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'detalles' => $detalles
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener detalle: ' . $e->getMessage()
            ]);
        }
    }
}