<?php

require_once "utils/lib/mpdf/vendor/autoload.php";  // Incluir el autoload de MPDF

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


    // Función para obtener clientes filtrados para autocompletado
    public function obtenerClientesAutocompletado()
        {
            try {
                $conductorModel = new Conductor();
        
                // Obtener el término de búsqueda desde la solicitud GET
                $searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';
        
                // Obtener los conductores filtrados
                $conductores = $conductorModel->obtenerConductoresConCodigo($searchTerm);
        
                // Devolver los resultados en formato JSON
                echo json_encode($conductores);
                exit;
            } catch (Exception $e) {
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

        public function obtenerProductos()
        {
            try {
                // Obtener la página actual desde $_GET
                $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                $productosPorPagina = 5; // Número de productos por página


                // Mostrar los valores de entrada
                //var_dump(['pagina' => $pagina, 'productosPorPagina' => $productosPorPagina]);


                // Crear una instancia del modelo ProductoV2
                $productoV2 = new ProductoV2();

                // Obtener los productos con paginación
                $productos = $productoV2->obtenerProductos($pagina, $productosPorPagina);


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

        public function guardarGrupoFinanciamiento() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nombreGrupo = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';

                if (empty($nombreGrupo)) {
                    echo json_encode(['success' => false, 'message' => 'El campo nombre está vacío.']);
                    return;
                }

                $model = new GrupoFinanciamientoModel(); // Instanciar el modelo

                $resultado = $model->guardarGrupoFinanciamiento($nombreGrupo); // Guardar el grupo

                if ($resultado) {
                    // Obtener el ID del nuevo grupo
                    $nuevoId = $model->getUltimoIdInsertado();
                    echo json_encode([
                        'success' => true,
                        'nuevoGrupoFinanciamiento' => [
                            'idgrupoVehicular_financiamiento' => $nuevoId,
                            'nombre' => $nombreGrupo
                        ]
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se pudo guardar el grupo de financiamiento.']);
                }
            }
        }

        public function cargarGruposFinanciamiento() {
            $grupoFinanciamientoModel = new GrupoFinanciamientoModel();
            $grupos = $grupoFinanciamientoModel->obtenerGruposFinanciamiento();
            echo json_encode($grupos);
        }

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
                $documentoIdentidad = $_POST['documento_identidad'] ?? null;
                $metodoPago = $_POST['metodo_pago'] ?? null;
                $totalPagar = $_POST['total_pagar'] ?? null;
                $efectivoRecibido = $_POST['efectivo_recibido'] ?? null;
                $monedaEfectivo = $_POST['moneda_efectivo'] ?? null;
                $vuelto = $_POST['vuelto'] ?? null;
                $cuotasJson = $_POST['cuotas'] ?? '[]';
                $cuotasSeleccionadas = json_decode($cuotasJson, true);
                
                // Obtener el rol del usuario desde la sesión 🌎
                $rolUsuario = $_SESSION['id_rol'] ?? null; 

                if (!$documentoIdentidad || !$metodoPago || empty($cuotasSeleccionadas)) {
                    echo json_encode(['success' => false, 'message' => 'Datos incompletos para procesar el pago']);
                    return;
                }
    
                $financiamientoModel = new Financiamiento();

                // Verificar si el usuario tiene permisos para actualizar cuotas 🌎
                if ($rolUsuario == 1 || $rolUsuario == 3) { 
                    $resultado = $financiamientoModel->actualizarCuotas($cuotasSeleccionadas); 
                } else { 
                    // Si es rol 2, no actualiza las cuotas pero devuelve éxito para continuar 🌎
                    $resultado = ['success' => true, 'message' => 'Cuotas pendientes de aprobación']; 
                } 

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

                    $reportController = new ReportFinanciamientoController();
                    $pdfBase64 = $reportController->generateNotaVenta(
                        $idConductor ?: $idCliente, // MODIFICADO: Usamos el operador ternario para pasar el id disponible
                        $idAsesor, 
                        $cuotasSeleccionadas, 
                        $idPago, 
                        $monedaEfectivo
                    );
                    
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
    
            $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
            $search = isset($_POST['search']) ? trim($_POST['search']) : '';
            // NUEVO: Capturar fechas
            $fechaInicio = isset($_POST['fechaInicio']) ? trim($_POST['fechaInicio']) : '';
            $fechaFin = isset($_POST['fechaFin']) ? trim($_POST['fechaFin']) : '';
            $limit = 10;
            $offset = ($page - 1) * $limit; 
             
             // MODIFICADO: Pasar parámetros de fecha
            $resultados = $financiamiento->obtenerReportesPagos($offset, $limit, $search, $fechaInicio, $fechaFin);
            $totalRegistros = $financiamiento->contarReportes($search, $fechaInicio, $fechaFin);
            
            // Paginación
            $totalPaginas = ceil($totalRegistros / $limit);
            $paginacion = '<nav><ul class="pagination justify-content-center">';

            // Botón "Anterior"
            if ($page > 1) {
                $prevPage = $page - 1;
                $paginacion .= "<li class='page-item'><button class='page-link' onclick='cargarReportes($prevPage, \"$search\", \"$fechaInicio\", \"$fechaFin\")'>Anterior</button></li>";
            } else {
                $paginacion .= "<li class='page-item disabled'><span class='page-link'>Anterior</span></li>";
            }

            $rango = 2; // Número de enlaces a cada lado de la página actual

            // Enlaces de páginas
            for ($i = 1; $i <= $totalPaginas; $i++) {
                // Mostrar siempre la primera página, la última página, y las páginas en el rango de la actual
                if ($i == 1 || $i == $totalPaginas || ($i >= $page - $rango && $i <= $page + $rango)) {
                    if ($i == $page) {
                        $paginacion .= "<li class='page-item active'><span class='page-link'>$i</span></li>";
                    } else {
                        $paginacion .= "<li class='page-item'><button class='page-link' onclick='cargarReportes($i, \"$search\", \"$fechaInicio\", \"$fechaFin\")'>$i</button></li>";
                    }
                } 
                // Mostrar puntos suspensivos si es necesario
                elseif ($i == $page - $rango - 1 || $i == $page + $rango + 1) {
                    $paginacion .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                }
            }

            // Botón "Siguiente"
            if ($page < $totalPaginas) {
                $nextPage = $page + 1;
                $paginacion .= "<li class='page-item'><button class='page-link' onclick='cargarReportes($nextPage, \"$search\", \"$fechaInicio\", \"$fechaFin\")'>Siguiente</button></li>";
            } else {
                $paginacion .= "<li class='page-item disabled'><span class='page-link'>Siguiente</span></li>";
            }

            $paginacion .= '</ul></nav>';
    
            // Modificación: Enviar número de página y límite para que el frontend pueda hacer una numeración continua
            echo json_encode([
                'data' => $resultados,
                'pagination' => $paginacion,
                'page' => $page, // Modificación: Enviar el número de página actual
                'limit' => $limit,
                // NUEVO: Incluir fechas en la respuesta para mantener el filtro
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin
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
        $financiamientos = $financiamientoModel->getAllFinanciamientos();

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
        // Obtener todos los financiamientos
        $financiamientos = $financiamientoModel->getAllFinanciamientos();
        
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

        // Responder éxito
       
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Financiamiento aprobado correctamente']);
        
       
    }

        // Método para rechazar un financiamiento
    public function rechazarFinanciamiento() {

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

        // Actualizar el estado del financiamiento a rechazado (2)
        $queryActualizar = "UPDATE financiamiento SET aprobado = 2 WHERE idfinanciamiento = $id";
        $resultActualizar = $this->conexion->query($queryActualizar);

        if (!$resultActualizar) {
           
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Error al rechazar el financiamiento']);
            return;
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

        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
            return;
        }

        $stmt = $this->conexion->prepare("DELETE FROM financiamiento WHERE idfinanciamiento = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Financiamiento eliminado correctamente']);
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

        $financiamientos = $financiamientoModel->getAllFinanciamientos();
        
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
                            c.id_conductor, p.nombre

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
                            cl.id, p.nombre 
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
    }
    