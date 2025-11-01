<?php
require_once "utils/lib/mpdf/vendor/autoload.php";  // Incluir el autoload de MPDF

use Mpdf\Mpdf;

require_once "app/models/Financiamiento.php";
require_once "app/models/Conductor.php";
require_once "app/models/Productov2.php";
require_once "app/models/CaracteristicaProducto.php";
require_once "app/models/CuotaFinanciamiento.php";
require_once "app/models/Vehiculo.php";
require_once "app/models/ConductorPagoModel.php";
require_once "app/models/ConductorCuotaModel.php";
require_once "app/models/ConductorRegFinanciamientoModel.php";
require_once "app/models/DireccionConductor.php";
require_once "app/models/Inscripcion.php";
require_once "app/models/Requisito.php";
require_once "app/models/Observacion.php";
require_once "app/models/ContactoEmergencia.php";
require_once "app/models/Cliente.php";
require_once "app/models/GrupoFinanciamientoModel.php";

require_once 'utils/lib/vendor/autoload.php'; // Importar PhpSpreadsheet
require_once 'utils/lib/exel/vendor/autoload.php'; // Importar PhpSpreadsheet

class GenerarContratosController extends controller
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->mpdf = new Mpdf();  // Crear una instancia de Mpdf
    }

    public function searchFinanciamientos()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        $query = $input['query'] ?? '';

        $financiamientoModel = new Financiamiento();
        $resultados = $financiamientoModel->buscarFinanciamientos($query);

        header('Content-Type: application/json');
        echo json_encode($resultados);
    }

    public function obtenerFinanciamientoDetalle() {
        $idFinanciamiento = $_GET['id_financiamiento'];
    
        // Consulta principal: Financiamiento
        $model = new Financiamiento();
        $financiamiento = $model->getFinanciamientoById($idFinanciamiento); // Se usa el modelo correctamente
    
        if (!$financiamiento) {
            echo json_encode(['error' => 'No se encontró el financiamiento.']);
            return;
        }
    
        // Información del conductor
        $conductor = $model->getConductorById($financiamiento['id_conductor']); // Se usa el modelo correctamente
        $direccion = $model->getDireccionCompleta($financiamiento['id_conductor']); 
    
        // Información del producto
        $producto = null;
        if ($financiamiento['idproductosv2'] !== null) {  // Verificamos si idproductosv2 no es null
            $producto = $model->getProductoById($financiamiento['idproductosv2']); // Solo buscamos el producto si existe
        }
        $producto = $model->getProductoById($financiamiento['idproductosv2']); // Se obtiene el producto
    
        // Si no se encuentra el producto, se maneja el caso y se devuelve un objeto vacío
        if (!$producto) {
            $producto = ['codigo' => 'N/A', 'nombre' => 'Producto no disponible']; // Se asigna un valor por defecto si no existe
        }
    
        // Respuesta
        $response = [
            'financiamiento' => $financiamiento,
            'conductor' => array_merge($conductor, ['direccion' => $direccion]),
            'producto' => $producto, // Se agrega el producto
        ];
    
        echo json_encode($response);
    }

    public function obtenerFinanciamientosPorFecha()
    {
        // Obtener el rango de fechas desde la solicitud AJAX
        $input = json_decode(file_get_contents("php://input"), true);
        $fechaInicio = $input['fecha_inicio'] ?? '';
        $fechaFin = $input['fecha_fin'] ?? '';

        // Validar que ambas fechas estén presentes
        if (empty($fechaInicio) || empty($fechaFin)) {
            echo json_encode([]);
            return;
        }

        // Llamar al modelo para obtener los financiamientos
        $financiamientoModel = new Financiamiento();
        $resultados = $financiamientoModel->buscarFinanciamientosPorFecha($fechaInicio, $fechaFin);

        // Devolver los resultados como respuesta JSON
        header('Content-Type: application/json');
        echo json_encode($resultados);
    }

    
    public function generar()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $ids = $input['ids'] ?? [];

        if (empty($ids)) {
            echo json_encode(['success' => false, 'errores' => ['No se enviaron IDs.']]);
            return;
        }

        $financiamientoModel = new Financiamiento();
        $conductorModel = new Conductor();
        $clienteModel = new Cliente(); 
        $productoModel = new Productov2();
        $celularModel = new Celular();
        $caracteristicasModel = new CaracteristicaProducto();
        $cuotaModel = new CuotaFinanciamiento();
        $requisitosModel = new Requisito();
        $errores = [];
        $pdfs = [];
        $excels = [];

        foreach ($ids as $idFinanciamiento) {
            try {
                $financiamiento = $financiamientoModel->getFinanciamientoById($idFinanciamiento);

                if (isset($financiamiento['aprobado']) && $financiamiento['aprobado'] == 2) { 
                    echo json_encode([ 
                        'success' => false,  
                        'errores' => ["No se puede generar contrato, El financiamiento fue rechazado"], 
                        'pdfs' => [], 
                        'mensaje' => "No se puede generar contrato, El financiamiento fue rechazado" // 
                    ]); // 🚀
                    return; // 🚀
                } elseif (isset($financiamiento['aprobado']) && $financiamiento['aprobado'] === 0) { // 🚀
                    echo json_encode([ // 🚀
                        'success' => false, // 🚀
                        'errores' => ["No se puede generar contrato, El financiamiento está pendiente"], // 🚀
                        'pdfs' => [], // 🚀
                        'mensaje' => "No se puede generar contrato, El financiamiento está pendiente" // 🚀
                    ]); // 🚀
                    return; // 🚀
                }

                // Determinar si es conductor o cliente
                $tipoPersona = 'conductor'; // Por defecto, asumimos conductor
                $persona = null;
                
                // Verificamos si tiene id_conductor
                if (empty($financiamiento['id_conductor'])) {
                    // Es un cliente, cargamos sus datos
                    $tipoPersona = 'cliente';
                    $persona = $clienteModel->getClienteById($financiamiento['id_cliente']);
                    // Concatenamos nombre completo del cliente
                    $nombrePersona = trim(
                        $persona['nombres'] . ' ' .
                        $persona['apellido_paterno'] . ' ' .
                        $persona['apellido_materno']
                    );
                } else {
                    // Es un conductor, usamos el código existente
                    $tipoPersona = 'conductor';
                    $persona = $financiamientoModel->getConductorById($financiamiento['id_conductor']);
                    // Reusamos el código existente para concatenar nombre
                    $nombrePersona = trim(
                        $persona['nombres'] . ' ' .
                        $persona['apellido_paterno'] . ' ' .
                        $persona['apellido_materno']
                    );
                }

                $producto = $financiamientoModel->obtenerProductoConCategoria($financiamiento['idproductosv2']);
                
                // Normalización de la categoría del producto
                $categoriaProducto = trim(strtolower(str_replace(['é','á'], ['e','a'], $producto['categoria'])));
                $esCelular = preg_match('/^celular(es)?$/', $categoriaProducto);
                $categoriaProducto = trim(strtolower(str_replace(
                    ['é', 'á', 'í', 'ó', 'ú'],
                    ['e', 'a', 'i', 'o', 'u'],
                    $producto['categoria']
                )));
                
                     
                // Comparación
                $esVehiculo = in_array($categoriaProducto, ['vehiculo', 'vehiculo(s)', 'vehiculos']);
         

                // Obtener características según la categoría
                if ($esCelular) {
                    // Si es un celular, usamos el método específico para celulares
                    $caracteristicas = $celularModel->obtenerCaracteristicasCelulares($financiamiento['idproductosv2']);
                } else {
                    // Para otros productos, usamos el método original
                    $caracteristicas = $caracteristicasModel->obtenerCaracteristicas($financiamiento['idproductosv2']);
                }

                $cuotas = $cuotaModel->obtenerCuotasPorFinanciamiento($idFinanciamiento);

                // 😊 Generar contrato de Excel para vehículos (excluir grupo 19, 33 y 38)
                if ($esVehiculo && $financiamiento['grupo_financiamiento'] != 33 && $financiamiento['grupo_financiamiento'] != 19 && $financiamiento['grupo_financiamiento'] != 38) {
                    try {
                        $excelFile = $this->generarContratoExcelVehiculo(
                            $financiamiento,
                            $persona,
                            $tipoPersona,
                            $producto,
                            $caracteristicas,
                            $cuotas,
                            $nombrePersona,
                            $requisitosModel
                        );
                       
                        if ($excelFile) {
                            $excels[] = [
                                'content' => base64_encode($excelFile),
                                'nombre' => "contrato_vehiculo_{$idFinanciamiento}_{$nombrePersona}.xlsx"
                            ];
                        }
                        
                        // NUEVO: Generar acta de entrega si el vehículo ya fue entregado
                        if (isset($financiamiento['vehiculo_entregado']) && $financiamiento['vehiculo_entregado'] == 1) {
                            try {
                                $cliente = $this->obtenerDatosClienteEntrega($financiamiento);
                                $vehiculo = $this->obtenerDatosVehiculoEntrega($financiamiento);
                                $html = $this->cargarYLlenarTemplateEntrega($financiamiento, $cliente, $vehiculo);
                                $pdfEntrega = $this->generarPDFEntrega($html);
                                
                                $pdfs[] = [
                                    'content' => base64_encode($pdfEntrega),
                                    'nombre' => "acta_entrega_vehiculo_{$idFinanciamiento}_{$nombrePersona}.pdf"
                                ];
                            } catch (\Exception $e) {
                                error_log("Error generando acta de entrega para financiamiento ID $idFinanciamiento: " . $e->getMessage());
                            }
                        }
                        
                        continue;
                    } catch (\Exception $e) {
                        error_log("Error generando contrato Excel de vehículo ID $idFinanciamiento: " . $e->getMessage());
                        continue;
                    }
                }
                
                // Generar contrato PDF para CrediGo Autos Grupo 4 (grupo 38)
                if ($esVehiculo && $financiamiento['grupo_financiamiento'] == 38) {
                    try {
                        $htmlContrato = $this->generarContratoGrupo38($financiamiento, $persona, $tipoPersona, $nombrePersona);
                        
                        $mpdf = new \Mpdf\Mpdf([
                            'format' => 'A4',
                            'margin_left' => 15,
                            'margin_right' => 15,
                            'margin_top' => 15,
                            'margin_bottom' => 15
                        ]);
                        
                        $mpdf->WriteHTML($htmlContrato);
                        $pdfContent = $mpdf->Output('', 'S');
                        
                        $pdfs[] = [
                            'content' => base64_encode($pdfContent),
                            'nombre' => "contrato_credigo_grupo4_{$idFinanciamiento}_{$nombrePersona}.pdf"
                        ];
                        
                        continue;
                    } catch (\Exception $e) {
                        error_log("Error generando contrato PDF Grupo 4 ID $idFinanciamiento: " . $e->getMessage());
                        continue;
                    }
                }
               
             
                if (!$esVehiculo || $financiamiento['grupo_financiamiento'] == 33 || $financiamiento['grupo_financiamiento'] == 19) {
                    if (!in_array($producto['categoria'], ['Llantas', 'Aceites', 'Celular', 'Chip (Linea corporativa)', 'Baterías']) && !in_array($financiamiento['grupo_financiamiento'], [33, 35, 22, 19])) {
                        throw new Exception("No hay un modelo de contrato para este producto.");
                    }

                    $plantillas = $this->generarPlantillaContrato(
                        $producto['categoria'],
                        $financiamiento,
                        $persona,
                        $tipoPersona,
                        $producto,
                        $caracteristicas,
                        $cuotas
                    );

                    foreach ($plantillas as $nombrePlantilla => $html) {
                       
                        // Condicional para márgenes
                        if (isset($financiamiento['grupo_financiamiento']) && $financiamiento['grupo_financiamiento'] == 22) {
                            // Sin márgenes para el contrato de Motos
                            $mpdf = new \Mpdf\Mpdf([
                                'margin_left' => 0,
                                'margin_right' => 0,
                                'margin_top' => 0,
                                'margin_bottom' => 0,
                                'margin_header' => 0,
                                'margin_footer' => 0
                            ]);
                        } else {
                            // Márgenes por defecto para los otros contratos
                            $mpdf = new \Mpdf\Mpdf([
                                'margin_left' => 30, // Margen izquierdo (en milímetros)
                                'margin_right' => 30,
                            ]);
                        }
                        $mpdf->WriteHTML($html);
        
                        // Crear un nombre único para cada archivo
                        $nombreArchivo = "contrato_{$idFinanciamiento}_{$nombrePersona}_{$nombrePlantilla}.pdf";
        
                        $pdfContent = $mpdf->Output('', 'S'); // Devuelve el contenido directamente
                        $pdfs[] = [
                            'content' => base64_encode($pdfContent), // Codificado en Base64
                            'nombre' => $nombreArchivo
                        ];
                    }
                }
            } catch (\Exception $e) {
                $errores[] = $idFinanciamiento;
                error_log("Error generando contrato ID $idFinanciamiento: " . $e->getMessage());
            }
        }

        echo json_encode([
            'success' => empty($errores),
            'errores' => $errores,
            'pdfs' => $pdfs,
            'excels' => $excels,
            'mensaje' => !empty($errores) ? "Error" : null
        ]);
    }

      // 😊 Nuevo método para generar contrato de Excel para vehículos
      private function generarContratoExcelVehiculo($financiamiento, $persona, $tipoPersona, $producto, $caracteristicas, $cuotas, $nombrePersona, $requisitosModel)
      {
      
          // 😊 Aumentar límite de memoria y optimizar configuración para Excel
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 300); // 5 minutos

          $GrupoFinanciamientoModel = new GrupoFinanciamientoModel();
          // Ruta al archivo Excel de plantilla
          $rutaBase = "app" . DIRECTORY_SEPARATOR . "contratos" . DIRECTORY_SEPARATOR . "exel";
          $rutaArchivo = $rutaBase . DIRECTORY_SEPARATOR . "Financiamiento-Vehicular.xlsx";
          
          // Verificar que el archivo existe
          if (!file_exists($rutaArchivo)) {
              throw new Exception("Plantilla de contrato de vehículo no encontrada: $rutaArchivo");
          }
          
          // Cargar el archivo Excel
          $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($rutaArchivo);
          $worksheet = $spreadsheet->getActiveSheet();
          
          // Obtener datos adicionales necesarios
          $idPersona = $tipoPersona === 'conductor' ? $financiamiento['id_conductor'] : $financiamiento['id_cliente'];
          
          // Obtener la dirección completa según el tipo de persona
          if ($tipoPersona === 'conductor') {
              $datosDireccion = $this->obtenerDatosDireccionConductor($idPersona);
              $estadosRequisitos = $requisitosModel->obtenerEstadoRequisitos($idPersona);
          } else {
              $direccionClienteModel = new Cliente(); // 😊 Crear instancia del modelo
              $datosDireccion = $direccionClienteModel->obtenerDatosDireccionCliente($idPersona);
              $estadosRequisitos = $this->obtenerEstadosRequisitoCliente($idPersona);
          }
          
          // Obtener datos del grupo de financiamiento
            $grupoInfo = $GrupoFinanciamientoModel->obtenerDatosGrupoFinanciamiento($financiamiento);

            // 😊 Obtener tipo vehicular para determinar la moneda correcta
            $idGrupoFinanciamiento = $financiamiento['grupo_financiamiento'];
            $tipoVehicular = $GrupoFinanciamientoModel->getTipoVehicular($idGrupoFinanciamiento);

            // Llenar el Excel con los datos
          
          // 1. Número de teléfono - Celda H6
          $worksheet->setCellValue('H6', $persona['telefono'] ?? '');
          
          // 2. Apellido Paterno - Celda B9
          $worksheet->setCellValue('B9', $persona['apellido_paterno'] ?? '');
          
          // 3. Apellido Materno - Celda D9
          $worksheet->setCellValue('D9', $persona['apellido_materno'] ?? '');
          
          // 4. Nombres - Celda F9
          $worksheet->setCellValue('F9', $persona['nombres'] ?? '');
          
          // 5. Número de documento - Celda H9
          $worksheet->setCellValue('H9', $tipoPersona === 'conductor' ? 
              ($persona['nro_documento'] ?? '') : 
              ($persona['n_documento'] ?? ''));
          
          // 6. Dirección - Celda B13
          $worksheet->setCellValue('B13', $datosDireccion['direccion_detalle'] ?? '');
          
          // 7. Distrito - Celda H13
          $worksheet->setCellValue('H13', $datosDireccion['distrito'] ?? '');
          
          // 8. Provincia - Celda B15
          $worksheet->setCellValue('B15', $datosDireccion['provincia'] ?? '');
          
          // 9. Departamento - Celda E15
          $worksheet->setCellValue('E15', $datosDireccion['departamento'] ?? '');
          
          // 10. Nro de licencia - Celda H15 (solo para conductor)
          if ($tipoPersona === 'conductor') {
              $worksheet->setCellValue('H15', $persona['nro_licencia'] ?? '');
          }

          $worksheet->setCellValue('B17', $persona['correo'] ?? '');
          
          // 12. Código de Asociado - Celda C20
          $worksheet->setCellValue('B20', $financiamiento['codigo_asociado'] ?? '');
          
          // 13. Grupo de financiamiento - Celda D20
          $worksheet->setCellValue('D20', $grupoInfo['nombre'] ?? '');
          
          // 14. Duración del grupo - Celda H20
          $worksheet->setCellValue('H20', $grupoInfo['duracion'] ?? '');
          
          // 15. Duración del contrato - Celda B22
          $duracionContrato = $this->calcularDuracion(
              $financiamiento['fecha_inicio'] ?? null, 
              $financiamiento['fecha_fin'] ?? null
          );
          $worksheet->setCellValue('B22', $duracionContrato);
          
          // 16. Fecha de inicio - Celda E22
          if (isset($grupoInfo['fecha_inicio'])) {
              $fechaInicio = date('d/m/Y', strtotime($grupoInfo['fecha_inicio']));
              $worksheet->setCellValue('E22', $fechaInicio);
          }
          
          // 17. Periodicidad - Celda H22
          $worksheet->setCellValue('H22', $grupoInfo['frecuencia'] ?? '');
          
          // 18. Monto sin intereses - Celda C25
          if (isset($grupoInfo['monto_sin_interes']) && isset($grupoInfo['moneda'])) {
              $montoConPrefijo = $grupoInfo['moneda'] . ' ' . $grupoInfo['monto_sin_interes'];
              $worksheet->setCellValue('B25', $montoConPrefijo);
          }
          
          // 19. Monto de inscripción - Celda I27
          $worksheet->setCellValue('I27', $financiamiento['monto_inscrip'] ?? '0.00');
          
          // 😊 20. Configurar moneda en celda H27 según tipo vehicular con alineación
            if ($tipoVehicular === 'moto') {
                // Para motos usar soles (S/)
                $worksheet->setCellValue('H27', 'S/');
                $worksheet->getStyle('H27')->getAlignment()->setIndent(1);
            } else {
                // Para vehículos usar dólares (US$)
                $worksheet->setCellValue('H27', 'US$');
                $worksheet->getStyle('H27')->getAlignment()->setIndent(1);
            }

          // 21. Marcar documentos según estados
          $this->marcarDocumentosEnExcel($worksheet, $estadosRequisitos, $tipoPersona);

          // 22. Fecha actual con formato personalizado - Celda C44
            setlocale(LC_TIME, 'es_ES.UTF-8'); // Para sistemas que soportan UTF-8 (Linux/macOS)
            $fechaFormateada = strftime('%d de %B del %Y', strtotime(date('Y-m-d')));

            // Para Windows o en caso strftime no funcione bien con español, usa esta alternativa:
            $meses = [
                '01' => 'enero', '02' => 'febrero', '03' => 'marzo', '04' => 'abril',
                '05' => 'mayo', '06' => 'junio', '07' => 'julio', '08' => 'agosto',
                '09' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre'
            ];
            $dia = date('d');
            $mes = $meses[date('m')];
            $anio = date('Y');
            $fechaFormateada = "$dia de $mes del $anio";

            $worksheet->setCellValue('C44', $fechaFormateada);

          
          // Guardar el archivo en un flujo de salida
          $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
          ob_start();
          $writer->save('php://output');
          $excelContent = ob_get_clean();
          
          return $excelContent;
      }

        // 😊 Nuevo método para calcular duración
    private function calcularDuracion($fechaInicio, $fechaFin) {
        if (!$fechaInicio || !$fechaFin) {
            return '';
        }
        
        $inicio = new DateTime($fechaInicio);
        $fin = new DateTime($fechaFin);
        $diff = $inicio->diff($fin);
        
        // Si es más de 30 días, mostrar en meses
        if ($diff->days > 30) {
            $meses = floor($diff->days / 30);
            return $meses . ' meses';
        } else {
            return $diff->days . ' días';
        }
    }

    // 😊 Nuevo método para obtener dirección del conductor
    private function obtenerDatosDireccionConductor($idConductor) {
        $direccionConductorModel = new DireccionConductor(); // 😊 Suponiendo que existe este modelo
        return $direccionConductorModel->obtenerDatosDireccion($idConductor);
    }

     // 😊 Nuevo método para obtener estados de requisitos del cliente
     private function obtenerEstadosRequisitoCliente($idCliente) {
        $resultado = [
            'doc_identidad' => 0,
            'recibo_servicios' => 0,
            'licencia_doc' => 0,
            'soat_doc' => 0,
            'tarjeta_propiedad' => 0
        ];
        
        // Consultar datos de la tabla clientes_financiar
        $sql = "SELECT doc_identidad, recibo_servicios FROM clientes_financiar WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param('i', $idCliente);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            
            if ($result) {
                $resultado['doc_identidad'] = !empty($result['doc_identidad']) ? 1 : 0;
                $resultado['recibo_servicios'] = !empty($result['recibo_servicios']) ? 1 : 0;
            }
        }
        
        return $resultado;
    }

     // 😊 Nuevo método para marcar documentos en el Excel
     private function marcarDocumentosEnExcel($worksheet, $estados, $tipoPersona) {
        // Mapeo de estados a celdas en el Excel
        $mapeo = [
            'doc_identidad' => 'C32',
            'recibo_servicios' => 'F32',
            'licencia_doc' => 'H32',
            'soat_doc' => 'E33',
            'tarjeta_propiedad' => 'H33'
        ];
        
        // Valor para marcar como entregado
        $marcaEntregado = 'X';
        
        // Marcar los documentos según su estado
        foreach ($mapeo as $documento => $celda) {
            // Solo marcamos si el estado es 1 (documento entregado)
            if (isset($estados[$documento]) && $estados[$documento] == 1) {
                $worksheet->setCellValue($celda, $marcaEntregado);
            } else {
                $worksheet->setCellValue($celda, '');
            }
            
            // Para cliente, solo marcar doc_identidad y recibo_servicios
            if ($tipoPersona === 'cliente' && !in_array($documento, ['doc_identidad', 'recibo_servicios'])) {
                $worksheet->setCellValue($celda, '');
            }
        }
    }
    
    private function generarPlantillaContrato($categoria, $financiamiento, $persona, $tipoPersona, $producto, $caracteristicas, $cuotas)
    {

        $idPersona = $tipoPersona === 'conductor' ? $financiamiento['id_conductor'] : $financiamiento['id_cliente'];
        
        if ($tipoPersona === 'conductor') {
            $datosDireccion = $this->obtenerDatosDireccionConductor($idPersona);
        } else {
            $direccionClienteModel = new Cliente();
            $datosDireccion = $direccionClienteModel->obtenerDatosDireccionCliente($idPersona);
        }
        
        $provincia = $datosDireccion['provincia'] ?? 'AREQUIPA';
        $provinciaCapitalizada = ucwords(strtolower($provincia));

        $rutaBase = "app" . DIRECTORY_SEPARATOR . "contratos";  // Usamos DIRECTORY_SEPARATOR

        // Formatear fecha y hora
        $fechaCreacion = strtotime($financiamiento['fecha_creacion']);
        $hora = date('h:i A', $fechaCreacion);
        $dia = date('d', $fechaCreacion);
        $mes = date('m', $fechaCreacion);
        $anio = date('Y', $fechaCreacion);

        // 🔧 NUEVO: Obtener el nombre del mes en español
        $nombreMes = $this->obtenerNombreMes($mes);

        // Concatenar nombre completo de la persona (conductor o cliente)
        $nombrePersona = trim(
            $persona['nombres'] . ' ' .
            $persona['apellido_paterno'] . ' ' .
            $persona['apellido_materno']
        );

        // Generar textos dinámicos según tipo de persona
        $textoRol = $tipoPersona === 'conductor' ? 'conductor' : 'cliente';
        $fraseAfiliacion = $tipoPersona === 'conductor' ? ', afiliado a la empresa Arequipa Go donde actualmente labora' : '';
        $bloqueLicencia = $tipoPersona === 'conductor' ? ', N° licencia ' . $persona['nro_licencia'] : '';

        // Generar texto para la cláusula de descuento según tipo de persona
        $clausulaConductor = '';
        if ($tipoPersona === 'conductor') {
            $clausulaConductor = '<p>En caso de que <strong>EL CONDUCTOR</strong> incurra en morosidad respecto a las obligaciones económicas adquiridas con <strong>LA EMPRESA AREQUIPA GO</strong>, <strong>EL CONDUCTOR</strong> autoriza expresamente a la empresa a realizar un descuento automático de la cantidad adeudada de su bono semanal, con el fin de saldar el monto correspondiente.</p>
            <p>Dicho descuento se efectuará sin necesidad de notificación adicional, y será aplicado a la mayor brevedad posible para regularizar la deuda.</p>
            <p><strong>EL CONDUCTOR</strong> reconoce que esta autorización se otorga como parte de las condiciones contractuales y que, en caso de no contar con fondos suficientes, la empresa podrá tomar las medidas legales necesarias para recuperar la deuda.</p>';
        }

        $aro = ''; 
        $perfil = '';
        // Selección de la plantilla según la categoría
        if (isset($financiamiento['grupo_financiamiento']) && $financiamiento['grupo_financiamiento'] == 33) {
            $rutaArchivo = $rutaBase . DIRECTORY_SEPARATOR . "contrato_MotosYa.html";
        } elseif (isset($financiamiento['grupo_financiamiento']) && $financiamiento['grupo_financiamiento'] == 35) {
            $rutaArchivo = $rutaBase . DIRECTORY_SEPARATOR . "contrato_chipLinea.html";
        } elseif (isset($financiamiento['grupo_financiamiento']) && $financiamiento['grupo_financiamiento'] == 19) {
            $rutaArchivo = $rutaBase . DIRECTORY_SEPARATOR . "credigo_autos.html";
        } elseif (isset($financiamiento['grupo_financiamiento']) && $financiamiento['grupo_financiamiento'] == 22) {
            $rutaArchivo = $rutaBase . DIRECTORY_SEPARATOR . "contrato_Motos.html";
        } elseif (isset($financiamiento['grupo_financiamiento']) && $financiamiento['grupo_financiamiento'] == 44) {
            $rutaArchivo = $rutaBase . DIRECTORY_SEPARATOR . "contrato_incamotors.html";
        } elseif ($categoria === 'Llantas') {
            $rutaArchivo = $rutaBase . DIRECTORY_SEPARATOR . "contrato_llantas.html";
        } elseif ($categoria === 'Aceites') {
            $rutaArchivo = $rutaBase . DIRECTORY_SEPARATOR . "contrato_aceites.html";
        } elseif ($categoria === 'Baterías') {
            $rutaArchivo = $rutaBase . DIRECTORY_SEPARATOR . "contrato_baterias.html";
        } elseif (in_array(strtolower($categoria), ['chip (linea corporativa)', 'chip', 'chip corporativo'])) {
            $rutaArchivo = $rutaBase . DIRECTORY_SEPARATOR . "contrato_chipLinea.html";
        } elseif ($categoria === 'Celular') {
            $rutaArchivo = $rutaBase . DIRECTORY_SEPARATOR . "contrato_celular.html";
            if (isset($financiamiento['second_product']) && $financiamiento['second_product'] !== null) {
                $rutaArchivoChip = $rutaBase . DIRECTORY_SEPARATOR . "contrato_chipLinea.html";
                if (!file_exists($rutaArchivoChip)) {
                    throw new Exception("Archivo de contrato no encontrado: $rutaArchivoChip");
                }
                $plantillaChip = file_get_contents($rutaArchivoChip);

                $financiamientoModel = new Financiamiento();
                $caracteristicasModel = new CaracteristicaProducto();
                
                $producto2 = $financiamientoModel->obtenerProductoConCategoria($financiamiento['second_product']);
                $caracteristicas2 = $caracteristicasModel->obtenerCaracteristicas($financiamiento['second_product']);

                $planMensual = '';
                $operadora = '';
                $plan = ""; 
                foreach ($caracteristicas2 as $caracteristica2) { // ***Recorrer características***
                    if ($caracteristica2['nombre_caracteristicas'] === 'plan_mensual') {
                        $plan = $caracteristica2['valor_caracteristica']; 
                    } elseif ($caracteristica2['nombre_caracteristicas'] === 'operadora') {
                        $operadora = $caracteristica2['valor_caracteristica']; 
                    }
                }

                // Reemplazar los valores en la plantilla
                $plantillaChip = str_replace('<span id="hora">', $hora, $plantillaChip);
                $plantillaChip = str_replace('<span id="dia">', $dia, $plantillaChip);
                $plantillaChip = str_replace('<span id="mes">', $mes, $plantillaChip);
                $plantillaChip = str_replace('<span id="anio">', $anio, $plantillaChip);
                $plantillaChip = str_replace('<span id="conductor">', $nombrePersona, $plantillaChip);
                $plantillaChip = str_replace('<span id="dni">', $persona['nro_documento'] ?? $persona['n_documento'] ?? '', $plantillaChip);
                $plantillaChip = str_replace('<span id="licencia">', $persona['nro_licencia'] ?? '', $plantillaChip);
                $plantillaChip = str_replace('<span id="cantidad">', $financiamiento['cantidad_producto'], $plantillaChip);

                 // Solo incluir licencia si es conductor
                if ($tipoPersona === 'conductor') {
                    $plantillaChip = str_replace('<span id="licencia">', $persona['nro_licencia'], $plantillaChip);
                } else {
                    $plantillaChip = str_replace('<span id="licencia">', '', $plantillaChip);
                }

                // Reemplazos específicos para la plantilla de chip
                $plantillaChip = str_replace('<span id="empresa_chip">', $operadora, $plantillaChip);
                $plantillaChip = str_replace('<span id="precio">', $producto2['precio'], $plantillaChip);
                $plantillaChip = str_replace('<span id="precio2">', $producto2['precio'], $plantillaChip);
                $plantillaChip = str_replace('<span id="plan_mensual">', $plan, $plantillaChip);
                $plantillaChip = str_replace('<span id="plan_mensual2">', $plan, $plantillaChip);

                // Guardar la plantilla con los datos reemplazados en un archivo nuevo o devolverla
                $rutaArchivoSalida = "$rutaBase\contrato_chipLinea_relleno.html";
                file_put_contents($rutaArchivoSalida, $plantillaChip);

                
                $plantillas['plantillaChip'] = $plantillaChip;
            }
        } else {
            throw new Exception("Categoría desconocida: $categoria"); // Manejo de errores para categorías no soportadas
        }

        if (!file_exists($rutaArchivo)) {
            throw new Exception("Archivo de contrato no encontrado: $rutaArchivo");
        }
            
        $plantilla = file_get_contents($rutaArchivo);
         

        if ($categoria === 'Aceites') {
            $cantidadTotal = $financiamiento['cantidad_producto'] * $producto['cantidad_unidad']; // Multiplicación de cantidad por cantidad_unidad
        }

        
        if ($categoria === 'Llantas') {
            $aro = null; // Inicializa la variable
            $perfil = null; // Inicializa la variable
            foreach ($caracteristicas as $caracteristica) {
               
                $nombreCaracteristica = strtolower($caracteristica['nombre_caracteristicas']); // Convertir a minúsculas
        
                if ($nombreCaracteristica === 'aro') {
                    $aro = $caracteristica['valor_caracteristica']; // Asignar aro
                } elseif ($nombreCaracteristica === 'perfil') {
                    $perfil = $caracteristica['valor_caracteristica']; // Asignar perfil
                }
            }
      
        }

            // Inicializar variables para celular
            $chipLinea = ''; // Valor por defecto
            $marcaEquipo = ''; // Valor por defecto
            $modelo = ''; // Valor por defecto
            $imei = ''; // Valor por defecto
            $serie = ''; // Valor por defecto
            $color = ''; // Valor por defecto
            $cargador = ''; // Valor por defecto
            $cableUsb = ''; // Valor por defecto
            $manualUsuario = ''; // Valor por defecto
            $cajaEstuche = ''; // Valor por defecto

            
            // Si la categoría es Celular, asignar las características correspondientes
            if ($categoria === 'Celular') { // ***Modificación para celular***
                foreach ($caracteristicas as $caracteristica) { // ***Recorrer características***
                    if ($caracteristica['nombre_caracteristicas'] === 'chip_linea') {
                        $chipLinea = $caracteristica['valor_caracteristica']; // Asignar chip de línea
                    } elseif ($caracteristica['nombre_caracteristicas'] === 'marca_equipo') {
                        $marcaEquipo = $caracteristica['valor_caracteristica']; // Asignar marca
                    } elseif ($caracteristica['nombre_caracteristicas'] === 'modelo') {
                        $modelo = $caracteristica['valor_caracteristica']; // Asignar modelo
                    } elseif ($caracteristica['nombre_caracteristicas'] === 'nro_imei') {
                        $imei = $caracteristica['valor_caracteristica']; // Asignar IMEI
                    } elseif ($caracteristica['nombre_caracteristicas'] === 'nro_serie') {
                        $serie = $caracteristica['valor_caracteristica']; // Asignar serie
                    } elseif ($caracteristica['nombre_caracteristicas'] === 'colorc') {
                        $color = $caracteristica['valor_caracteristica']; // Asignar color
                    } elseif ($caracteristica['nombre_caracteristicas'] === 'cargador') {
                        $cargador = $caracteristica['valor_caracteristica']; // Asignar cargador
                    } elseif ($caracteristica['nombre_caracteristicas'] === 'cable_usb') {
                        $cableUsb = $caracteristica['valor_caracteristica']; // Asignar cable USB
                    } elseif ($caracteristica['nombre_caracteristicas'] === 'manual_usuario') {
                        $manualUsuario = $caracteristica['valor_caracteristica']; // Asignar manual del usuario
                    } elseif ($caracteristica['nombre_caracteristicas'] === 'estuche') {
                        $cajaEstuche = $caracteristica['valor_caracteristica']; // Asignar caja/estuche
                    }
                }
            }

             

        // Determinar el texto de frecuencia // ***Cambio añadido aquí***
        $frecuencyTexto='';
        $frecuenciaTexto = ''; // Valor por defecto
        if ($financiamiento['frecuencia'] === 'mensual') {
            $frecuenciaTexto = 'mensualmente';
            $frecuencyTexto = 'mensuales'; // Si frecuencia es mensual, se usa "mensualmente"
        } elseif ($financiamiento['frecuencia'] === 'semanal') {
            $frecuenciaTexto = 'semanalmente'; // Si frecuencia es semanal, se usa "semanalmente"
            $frecuencyTexto = 'semanales';
        }

       
    
        // Reemplazar etiquetas en la plantilla
        $reemplazos = [
            'hora' => $hora,
            'dia' => $dia,
            'mes' => $mes,
            'mes_nombre' => $nombreMes,  // 🆕 Nueva variable solo para contratos que lo necesiten
            'anio' => $anio,
            'provincia' => $provinciaCapitalizada,
            'nombre_conductor' => $nombrePersona,
            'dni' => $persona['nro_documento'] ?? $persona['n_documento'] ?? '',
        
            'cantidad' => $categoria === 'Aceites' ? $cantidadTotal : $financiamiento['cantidad_producto'], // Usar cantidad calculada para aceites
            'unidad_medida' => $producto['unidad_medida'], // Añadido para aceites
            'marca' => $producto['nombre'],
            'precio_total' => $financiamiento['monto_total'],
            'num_cuotas' => $financiamiento['cuotas'],
            'cuota_inicial' => $financiamiento['cuota_inicial'] ?? '0',
            'cuotas_semanales' => $financiamiento['cuotas'],
            'monto_cuota' => number_format($cuotas[0]['monto'], 2),
            'aro' => $aro,
            'perfil' => $perfil,
            'chip_linea' => $chipLinea, // ***Nuevo campo para chip de línea***
            'marca_equipo' => $marcaEquipo, // ***Nuevo campo para marca de equipo***
            'modelo' => $modelo, // ***Nuevo campo para modelo***
            'imei' => $imei, // ***Nuevo campo para IMEI***
            'serie' => $serie, // ***Nuevo campo para serie***
            'color' => $color, // ***Nuevo campo para color***
            'cargador' => $cargador, // ***Nuevo campo para cargador***
            'cable_usb' => $cableUsb, // ***Nuevo campo para cable USB***
            'manual_usuario' => $manualUsuario, // ***Nuevo campo para manual del usuario***
            'caja_estuche' => $cajaEstuche,
            'frecuency' => $frecuencyTexto,
            'frecuencia' => $frecuenciaTexto,
            'producto' => $producto['nombre'],
            'cuotas' => $financiamiento['cuotas'],
            'cuota_mensual' => number_format($cuotas[0]['monto'], 2), 
            'conductor' => $nombrePersona,
            
            'plan_mensual' => $chipLinea,
            'precio' => $producto['precio'],

            // Nuevos campos dinámicos
            'persona' => $textoRol,
            'persona_mayus' => strtoupper($textoRol),
            'licencia_bloque' => $bloqueLicencia,
            'frase_afiliacion' => $fraseAfiliacion,
            'clausula_conductor' => $clausulaConductor,
            'nombre_conductor' => $nombrePersona,
            'dni_firma' => $persona['nro_documento'] ?? $persona['n_documento'] ?? '',
            'operadora' => 'AREQUIPA GO',
            'precio_letras' => $this->numeroALetras($producto['precio']),
            'numero_linea' => '', // Se llenará con las características del producto

            // Nuevos campos para Plan Chip Movil
            'plan_descripcion' => '', // Default empty
            'numero_linea' => '' // Default empty
        ];

        // Lógica para Plan Chip Movil (ID 35)
        if (isset($financiamiento['grupo_financiamiento']) && $financiamiento['grupo_financiamiento'] == 35) {
            foreach ($caracteristicas as $caracteristica) {
                $nombreCaracteristica = strtolower($caracteristica['nombre_caracteristicas']);
                if ($nombreCaracteristica === 'descripcion_plan') {
                    $reemplazos['plan_descripcion'] = $caracteristica['valor_caracteristica'];
                } elseif ($nombreCaracteristica === 'numero_linea') {
                    $reemplazos['numero_linea'] = $caracteristica['valor_caracteristica'];
                }
            }
        }

        // Lógica específica para chips corporativos
        if (in_array(strtolower($categoria), ['chip (linea corporativa)', 'chip', 'chip corporativo'])) {
            foreach ($caracteristicas as $caracteristica) {
                $nombreCaracteristica = strtolower($caracteristica['nombre_caracteristicas']);
                if ($nombreCaracteristica === 'operadora') {
                    $reemplazos['operadora'] = $caracteristica['valor_caracteristica'];
                } elseif ($nombreCaracteristica === 'numero_linea') {
                    $reemplazos['numero_linea'] = $caracteristica['valor_caracteristica'];
                } elseif ($nombreCaracteristica === 'plan_mensual') {
                    $reemplazos['plan_mensual'] = $caracteristica['valor_caracteristica'];
                    $reemplazos['plan_mensual2'] = $caracteristica['valor_caracteristica'];
                }
            }
        }
    
        // Si es conductor, incluir licencia; si no, dejarla en blanco
        if ($tipoPersona === 'conductor') {
            $reemplazos['licencia'] = $persona['nro_licencia'];
        } else {
            $reemplazos['licencia'] = '';
        }
    
        foreach ($reemplazos as $id => $valor) {
            $plantilla = str_replace("<span id=\"$id\"></span>", $valor, $plantilla);
            
        }

        // Reemplazos específicos para credigo_autos.html (dobles llaves) - SOLO grupo 19
        if (isset($financiamiento['grupo_financiamiento']) && $financiamiento['grupo_financiamiento'] == 19) {
            // Solo aplicar estos reemplazos si es el grupo 19 (credigo_autos)
            $plantilla = str_replace('{{NOMBRE_COMPLETO}}', $nombrePersona, $plantilla);
            $plantilla = str_replace('{{DNI_RUC}}', $persona['nro_documento'] ?? $persona['n_documento'] ?? '', $plantilla);
            $plantilla = str_replace('{{DIA}}', $dia, $plantilla);
            $plantilla = str_replace('{{MES}}', $this->obtenerNombreMes($mes), $plantilla);
            $plantilla = str_replace('{{ANIO}}', $anio, $plantilla);
            $plantilla = str_replace('{{FECHA_ACTUAL}}', "$dia/" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "/$anio", $plantilla);
        }   
        
        // Generar lista de cuotas
        $listaCuotas = '';
        foreach ($cuotas as $index => $cuota) {
            $fechaCuota = date('d/m/Y', strtotime($cuota['fecha_vencimiento']));
            $listaCuotas .= "<li><strong>" . ($index + 1) . "a cuota:</strong> S/ {$cuota['monto']} - Fecha: $fechaCuota</li>";
        }
        $plantilla = str_replace("<ul id=\"lista_cuotas\"></ul>", "<ul>$listaCuotas</ul>", $plantilla);
    
        // Lógica específica para Plan Mantenimiento IncaMotors (ID 44)
        if (isset($financiamiento['grupo_financiamiento']) && $financiamiento['grupo_financiamiento'] == 44) {
            // Reemplazos específicos para el contrato de IncaMotors
            $plantilla = str_replace('<span id="textoRol"></span>', $textoRol, $plantilla);
            $plantilla = str_replace('<span id="textoRol2"></span>', $textoRol, $plantilla);
            $plantilla = str_replace('<span id="textoRol3"></span>', $textoRol, $plantilla);
            $plantilla = str_replace('<span id="textoRol4"></span>', $textoRol, $plantilla);
            $plantilla = str_replace('<span id="conductor"></span>', $nombrePersona, $plantilla);
            $plantilla = str_replace('<span id="nro_documento"></span>', $persona['nro_documento'] ?? $persona['n_documento'] ?? '', $plantilla);
            $plantilla = str_replace('<span id="bloqueLicencia"></span>', $bloqueLicencia, $plantilla);
            $plantilla = str_replace('<span id="cantidad"></span>', $financiamiento['cantidad_producto'], $plantilla);
            $plantilla = str_replace('<span id="nombreProducto"></span>', $producto['nombre'], $plantilla);
            $plantilla = str_replace('<span id="marca"></span>', $producto['nombre'], $plantilla);
            $plantilla = str_replace('<span id="moneda"></span>', $financiamiento['moneda'] ?? 'S/.', $plantilla);
            $plantilla = str_replace('<span id="moneda_total"></span>', $financiamiento['moneda'] ?? 'S/.', $plantilla);
            $plantilla = str_replace('<span id="moneda2"></span>', $financiamiento['moneda'] ?? 'S/.', $plantilla);
            $plantilla = str_replace('<span id="moneda3"></span>', $financiamiento['moneda'] ?? 'S/.', $plantilla);
            // Precio del producto (lo que les costó)
            $plantilla = str_replace('<span id="precio_producto"></span>', number_format($producto['precio'], 2), $plantilla);
            // Precio total del financiamiento (lo que pagará el cliente)
            $plantilla = str_replace('<span id="precio_total"></span>', number_format($financiamiento['monto_total'], 2), $plantilla);
            $plantilla = str_replace('<span id="n_cuotas"></span>', $financiamiento['cuotas'], $plantilla);
            $plantilla = str_replace('<span id="nro_cuotas"></span>', $financiamiento['cuotas'], $plantilla);
            $plantilla = str_replace('<span id="monto_inicial"></span>', number_format($financiamiento['cuota_inicial'] ?? 0, 2), $plantilla);
            $plantilla = str_replace('<span id="monto_cuota"></span>', number_format($cuotas[0]['monto'], 2), $plantilla);
            $plantilla = str_replace('<span id="frecuencia"></span>', $frecuencyTexto, $plantilla);
            $plantilla = str_replace('<span id="frecuencia2"></span>', $frecuenciaTexto, $plantilla);
            $plantilla = str_replace('<span id="clausulaConductor"></span>', $clausulaConductor, $plantilla);
            $plantilla = str_replace('<span id="nombre_firma"></span>', $nombrePersona, $plantilla);
            $plantilla = str_replace('<span id="dni_firma"></span>', $persona['nro_documento'] ?? $persona['n_documento'] ?? '', $plantilla);
            
            // Generar cronograma de cuotas para IncaMotors
            $cronogramaCuotas = '';
            foreach ($cuotas as $index => $cuota) {
                $fechaCuota = date('d/m/Y', strtotime($cuota['fecha_vencimiento']));
                $montoCuota = number_format($cuota['monto'], 2);
                $moneda = $financiamiento['moneda'] ?? 'S/.';
                $cronogramaCuotas .= "<p><strong>Cuota " . ($index + 1) . ":</strong> $moneda $montoCuota - Vencimiento: $fechaCuota</p>";
            }
            // Reemplazar el contenido del div cronogramaCuotas
            $plantilla = str_replace('<!-- Las cuotas se cargarán dinámicamente aquí -->', $cronogramaCuotas, $plantilla);
        }

        $plantillas['plantillaGeneral'] = $plantilla;

        return $plantillas;
    }

    public function generarContratosRegistro() {

        $input = file_get_contents('php://input'); // Leer el cuerpo de la solicitud
        $data = json_decode($input, true); // Decodificar el JSON recibido
    
        $financiamientoModel = new Financiamiento();
        $vehiculoModel = new Vehiculo();
        $pagoModel = new ConductorPagoModel();
        $cuotasModel = new ConductorCuotaModel();
        $conductorRegFinanciamientoModel = new ConductorRegFinanciamientoModel(); // Nuevo modelo para obtener financiamiento
        $conductorModel = new Conductor();
        $direccionConductorModel = new DireccionConductor();
        $inscripcionModel = new Inscripcion();
        $requisitosModel = new Requisito();
        $observacionModel = new Observacion();
        $contactoEmergenciaModel = new ContactoEmergencia();
        $conductorPago = new ConductorPagoModel();
        
        $resultados = []; // Inicializar el array de resultados
        $pdfs = []; 
        $errores = []; // Para registrar errores
    
        // Cargar la plantilla Excel
        $rutaBase = "app" . DIRECTORY_SEPARATOR . "contratos" . DIRECTORY_SEPARATOR . "exel";
        $rutaArchivo = $rutaBase . DIRECTORY_SEPARATOR . "DATOS GENERALES Lonely.xlsx";
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($rutaArchivo);
        $sheet = $spreadsheet->getActiveSheet();

        // Iterar sobre los conductores recibidos en el array
        foreach ($data['conductores'] as $conductor) {
            // Usar las claves correctas según el JSON
            $idConductor = $conductor['id_conductor']; // Cambiado de 'id' a 'id_conductor'
            $dni = $conductor['dni'];
            $nombresCompletos = $conductor['nombre_completo']; // Cambiado de 'nombres_completos' a 'nombre_completo'
    
            try {

                // Obtener los datos relacionados con el conductor
                $datosConductor = $conductorModel->getMissingData($idConductor);
                $datosDireccion = $direccionConductorModel->obtenerDatosDireccion($idConductor);
                $datosInscripcion = $inscripcionModel->obtenerInscripcionPorConductor($idConductor);
                $estadoRequisitos = $requisitosModel->obtenerEstadoRequisitos($idConductor);
                $observacion = $observacionModel->obtenerObservacion($idConductor);
                $direccion = $financiamientoModel->getDireccionCompleta($idConductor); // Obtener la dirección completa
                $vehiculo = $vehiculoModel->obtenerDatosVehiculo($idConductor); // Obtener los datos del vehículo
                $tipoPago = $pagoModel->obtenerTipoPago($idConductor); // Obtener el tipo de pago
                $datoPago = $conductorPago->obtenerPagosPorConductor($idConductor);
                // Obtener los datos del contacto de emergencia
                $contactoEmergenciaModel->setIdConductor($idConductor); // Establecer el id del conductor en el modelo de contacto de emergencia
                $contactoEmergenciaModel->obtenerDatosporConductor(); // Llamar al método para extraer los datos
                $contactoEmergencia = [
                    'nombres' => $contactoEmergenciaModel->getNombres(), // Obtener nombres
                    'telefono' => $contactoEmergenciaModel->getTelefono(), // Obtener teléfono
                    'parentesco' => $contactoEmergenciaModel->getParentesco(), // Obtener parentesco
                ];


                // Crear el array de datos para este conductor
                $datos = [
                    'id_conductor' => $idConductor, // Almacenar el id del conductor
                    'tipo_doc' => $datosConductor['tipo_doc'] ?? 'DNI', // Agregar el tipo de documento
                    'telefono' => $datosConductor['telefono'] ?? 'No registrado',
                    'apellido_paterno' => $datosConductor['apellido_paterno'] ?? 'No registrado',
                    'apellido_materno' => $datosConductor['apellido_materno'] ?? 'No registrado',
                    'nombres' => $datosConductor['nombres'] ?? 'No registrado',
                    'nombres_completos' => $conductor['nombre_completo'], // Cambiado de $data['nombres_completo']
                    'dni' => $conductor['dni'] ?? 'Sin DNI',
                    'direccion_completa' => $direccion, 
                    'placa' => $vehiculo['placa'] ?? 'Sin placa', // Placa, con valor por defecto
                    'marca' => $vehiculo['marca'] ?? 'Sin marca', // Marca, con valor por defecto
                    'modelo' => $vehiculo['modelo'] ?? 'Sin modelo', // Modelo, con valor por defecto
                    'color' => $vehiculo['color'] ?? 'Sin color', // Color, con valor por defecto
                    'anio' => $vehiculo['anio'] ?? 'Sin año', 
                    'condicion' => $vehiculo['condicion'] ?? 'Sin condición',
                    'monto_pago' => isset($datoPago[0]['monto_pago']) ? $datoPago[0]['monto_pago'] : 'No registrado',
                    'tipo_pago' => $tipoPago, // Tipo de pago
                    'nro_licencia' => $datosConductor['nro_licencia'] ?? 'No registrado',
                    'correo' => $datosConductor['correo'] ?? 'No registrado',
                    'numUnidad' => $datosConductor['numUnidad'] ?? 'No registrado',
                    'direccion_completa2' => [
                        'detalle' => $datosDireccion['direccion_detalle'] ?? 'No registrado',
                        'departamento' => $datosDireccion['departamento'] ?? 'No registrado',
                        'provincia' => $datosDireccion['provincia'] ?? 'No registrado',
                        'distrito' => $datosDireccion['distrito'] ?? 'No registrado',
                    ],
                    'setare' => $datosInscripcion['setare'] ?? 'No registrado',
                    'fecha_inscripcion' => $datosInscripcion['fecha_inscripcion'] ?? 'No registrado',
                    'estado_requisitos' => $estadoRequisitos,
                    'observacion' => $observacion ?? 'Sin observaciones',
                    'contacto_emergencia' => $contactoEmergencia, // Añadido: incluir datos del contacto de emergencia
                ];

                // Obtener la fecha actual en formato "d/m/Y"
                $fechaActual = date('d/m/Y'); // Nueva línea para obtener la fecha actual

                // Rellenar el archivo Excel con los datos
                $sheet->setCellValue('G5', $datos['telefono']);
                $sheet->setCellValue('A8', $datos['apellido_paterno']);
                $sheet->setCellValue('C8', $datos['apellido_materno']);
                $sheet->setCellValue('E8', $datos['nombres']);
                $sheet->setCellValue('G8', $datos['dni']);
                $sheet->setCellValue('A10', $datos['contacto_emergencia']['telefono']);
                $sheet->setCellValue('C10', $datos['contacto_emergencia']['parentesco']);
                $sheet->setCellValue('E10', $datos['contacto_emergencia']['nombres']);
                $sheet->setCellValue('A12', $datos['direccion_completa2']['detalle']);//Les cambie el nómbre a 2 para evitar que se confunda con el direccion_completa de arriba
                $sheet->setCellValue('G12', $datos['direccion_completa2']['distrito']);
                $sheet->setCellValue('A14', $datos['direccion_completa2']['provincia']);
                $sheet->setCellValue('D14', $datos['direccion_completa2']['departamento']);
                $sheet->setCellValue('G14', $datos['nro_licencia']);
                $sheet->setCellValue('A16', $datos['correo']);
                $sheet->setCellValue('A19', $datos['setare']);
                $sheet->setCellValue('E19', $datos['numUnidad']);
                $sheet->setCellValue('G19', $datos['monto_pago']);
                $sheet->setCellValue('D21', $datos['fecha_inscripcion']);
                $sheet->setCellValue('C26', $datos['observacion']);
                $provincia = $datosDireccion['provincia'] ?? 'AREQUIPA';
                $provinciaUpper = strtoupper($provincia);
                $sheet->setCellValue('A36', $provinciaUpper);
                $sheet->setCellValue('B36', $fechaActual);
                $sheet->getStyle('A36')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('B36')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                // Centrar el contenido de las celdas principales
                $sheet->getStyle('G5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A8:H8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A10:H10')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('G12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A14')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D14')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G14')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A16')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A19:H19')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D21')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C26')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                // Centrar las marcas de verificación de documentos
                $sheet->getStyle('H23')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D25')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G25')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D24')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B25')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B24')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B23')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D23')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Configuración para impresión - centrar contenido en la página física
                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                    ->setHorizontalCentered(true)  // ESTO centra horizontalmente al imprimir
                    ->setVerticalCentered(false);   // Mantener arriba, no centrar verticalmente

                // Configurar márgenes para impresión
                $sheet->getPageMargins()
                    ->setTop(0.75)
                    ->setRight(0.25)
                    ->setLeft(0.25)
                    ->setBottom(0.75)
                    ->setHeader(0.3)
                    ->setFooter(0.3);

                // Configurar el área de impresión si es necesario
                $sheet->getPageSetup()->setPrintArea('A1:H40'); // Ajusta el rango según tu contenido

                // Escalar para que quepa en una página
                $sheet->getPageSetup()->setFitToPage(true);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0); // 0 = automático

                // Marcar los documentos presentados
                $documentos = $datos['estado_requisitos'];
                $sheet->setCellValue('H23', ($documentos['recibo_servicios'] ?? 0) == 1 ? '✔' : '');
                $sheet->setCellValue('D25', ($documentos['carta_desvinculacion'] ?? 0) == 1 ? '✔' : '');
                $sheet->setCellValue('G25', ($documentos['revision_tecnica'] ?? 0) == 1 ? '✔' : '');
                $sheet->setCellValue('D24', ($documentos['soat_doc'] ?? 0) == 1 ? '✔' : '');
                // $sheet->setCellValue('B25', ($documentos['seguro_doc'] ?? 0) == 1 ? '✔' : '');
                $sheet->setCellValue('B25', ($documentos['tarjeta_propiedad'] ?? 0) == 1 ? '✔' : '');
                $sheet->setCellValue('B24', ($documentos['licencia_doc'] ?? 0) == 1 ? '✔' : '');
                // -------------------- MARCAR DOCUMENTO DE IDENTIDAD (B23 o D23) SOLO SI ESTÁ PRESENTADO --------------------
                if (($documentos['doc_identidad'] ?? 0) == 1) {
                    if ($datosConductor['tipo_doc'] === 'DNI') {
                        $sheet->setCellValue('B23', '✔');
                        $sheet->setCellValue('D23', '');
                    } else {
                        $sheet->setCellValue('D23', '✔');
                        $sheet->setCellValue('B23', '');
                    }
                } else {
                    $sheet->setCellValue('B23', '');
                    $sheet->setCellValue('D23', '');
                }

                //$sheet->setCellValue('B29', $documentos['doc_otro1'] == 1 ? '✔' : '');
                //$sheet->setCellValue('B30', $documentos['doc_otro2'] == 1 ? '✔' : '');
                //$sheet->setCellValue('B31', $documentos['doc_otro3'] == 1 ? '✔' : '');
        
                // Verificar si el tipo de pago es 2 (financiamiento)
                if ($tipoPago == 2) {
                    // Obtener el ID del financiamiento para este conductor
                    $idFinanciamiento = $conductorRegFinanciamientoModel->obtenerIdFinanciamiento($idConductor);
                    // Obtener las cuotas asociadas a este financiamiento
               
                    $datos['cuotas'] = $cuotasModel->obtenerCronogramaPagos($idFinanciamiento);
                   
                    // Obtener el financiamiento del conductor usando el modelo Conductor
                    $financiamiento = $conductorModel->obtenerDatosPago($idConductor); // Agregado: Se obtiene el financiamiento del conductor

                    $datos['monto_inicial'] = $financiamiento['financiamiento']['monto_inicial']; // Corregido: Ahora accede dentro de 'financiamiento'
                    $datos['fecha_pago'] = $financiamiento['fecha_pago']; // Esto está bien, ya que 'fecha_pago' está en el nivel principal

                }

          
                $html = $this->generarPlantillahtmltoPdf($datos);
              

                // Crear PDF
                $mpdf = new \Mpdf\Mpdf();
                
                
                $provincia = $datosDireccion['provincia'] ?? 'AREQUIPA';
                $provinciaUpper = strtoupper($provincia);
                $fechaActual = date('d/m/Y'); 

                $htmlCompleto = $this->generarPlantillahtmltoPdf($datos);

                $secciones = explode('<div style="page-break-after: always;"></div>', $htmlCompleto);

                $htmlSeccion1 = $secciones[0] ?? '';
                $htmlSeccion2 = $secciones[1] ?? '';

                $mpdf->WriteHTML($htmlSeccion1);

                $mpdf->SetHTMLFooter('<div style="text-align: left; font-weight: normal; border-top: none;">' . $provinciaUpper . ', ' . $fechaActual . '</div>');

                $mpdf->AddPage();

                $mpdf->SetHTMLFooter('<div style="text-align: left; font-weight: normal; border-top: none;">' . $provinciaUpper . ', ' . $fechaActual . '</div>');

                $mpdf->WriteHTML($htmlSeccion2);


                $nombreArchivo = "contrato_{$conductor['dni']}.pdf";
                $pdfContent = $mpdf->Output('', 'S'); // Generar el PDF en memoria

                // Almacenar PDF en base64
                $pdfs[] = [ // Changed from associative array to indexed array
                    'content' => base64_encode($pdfContent),
                    'nombre' => $nombreArchivo
                ];
                // Guardar los cambios en el archivo Excel
                $nombreArchivoExcel = "ANEXO 01 - DT FLOTA_{$conductor['dni']}.xlsx";
                $spreadsheet->getActiveSheet()->getProtection()->setSheet(true); // Activar protección de la hoja
                $spreadsheet->getActiveSheet()->getProtection()->setPassword('tu_contraseña'); // Establecer contraseña para la protección
                
                $spreadsheet->getActiveSheet()->getStyle('G5')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('H5')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                
                // Desbloquear las celdas A8, B8, C8, D8, E8, F8, G8, H8
                $spreadsheet->getActiveSheet()->getStyle('A8')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('B8')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('C8')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('D8')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('E8')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('F8')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('G8')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('H8')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                
                // Desbloquear las celdas A10, B10, C10, D10, E10, F10, G10, H10
                $spreadsheet->getActiveSheet()->getStyle('A10')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('B10')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('C10')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('D10')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('E10')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('F10')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('G10')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('H10')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                
                // Desbloquear las celdas A12, B12, C12, D12, E12, F12, G12, H12
                $spreadsheet->getActiveSheet()->getStyle('A12')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('B12')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('C12')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('D12')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('E12')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('F12')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('G12')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('H12')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                
                // Desbloquear las celdas A14, B14, C14, D14, E14, F14, G14, H14
                $spreadsheet->getActiveSheet()->getStyle('A14')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('B14')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('C14')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('D14')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('E14')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('F14')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('G14')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('H14')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                
                // Desbloquear las celdas A16, B16, C16, D16, E16, F16, G16, H16
                $spreadsheet->getActiveSheet()->getStyle('A16')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('B16')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('C16')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('D16')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('E16')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('F16')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('G16')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('H16')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                
                // Desbloquear las celdas A19, B19, C19, D19, E19, F19, G19, H19
                $spreadsheet->getActiveSheet()->getStyle('A19')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('B19')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('C19')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('D19')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('E19')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('F19')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('G19')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('H19')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                
                // Desbloquear las celdas A21, B21, C21, D21, E21, F21, G21, H21
                $spreadsheet->getActiveSheet()->getStyle('A21')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('B21')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('C21')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('D21')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('E21')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('F21')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('G21')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('H21')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('C26')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('D26')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('E26')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('F26')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('G26')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $spreadsheet->getActiveSheet()->getStyle('H26')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                ob_start();
                $writer->save('php://output');
                $excelContent = ob_get_clean();
                $base64Excel = base64_encode($excelContent);
                $resultados[] = $nombreArchivoExcel;

                $exels[] = [
                    'excel' => $base64Excel,
                    'nombre_excel' => $nombreArchivoExcel
                ];

                

            } catch (\Exception $e) {
                $errores[] = [
                    'id_conductor' => $idConductor,
                    'error' => $e->getMessage()
                ];
            }
            

            
        }
        // Retornar respuesta JSON
        echo json_encode([
            'success' => empty($errores),
            'resultados' => $resultados,
            'pdfs' => $pdfs, // Changed from associative array to indexed array
            'exels' => $exels,
            'errores' => $errores
        ]);
        
    }

    public function generarPlantillahtmltoPdf($datos) {
        $rutaBase = "app" . DIRECTORY_SEPARATOR . "contratos";  // Usamos DIRECTORY_SEPARATOR
        $rutaArchivo =  $rutaBase . DIRECTORY_SEPARATOR . "contratoSyA.html";

        
        $html = file_get_contents($rutaArchivo);

        // Reemplazar los valores de los spans con los datos del conductor
        $html = str_replace('<span id="nombre_afiliado">', $datos['nombres_completos'], $html);
        $html = str_replace('<span id="dni_afiliado">', $datos['dni'], $html);
        // Reemplazar el tipo de documento en todas las ocurrencias
        $html = str_replace('DNI N°', $datos['tipo_doc'] . ' N°', $html);
        $html = str_replace('DNI:', $datos['tipo_doc'] . ':', $html);
        $html = str_replace('<span id="domicilio_afiliado">', $datos['direccion_completa'], $html);
        $html = str_replace('<span id="placa_vehiculo">', $datos['placa'], $html);
        $html = str_replace('<span id="marca_vehiculo">', $datos['marca'], $html);
        $html = str_replace('<span id="modelo_vehiculo">', $datos['modelo'], $html);
        $html = str_replace('<span id="color_vehiculo">', $datos['color'], $html);
        $html = str_replace('<span id="anio_fabricacion">', $datos['anio'], $html);
        $html = str_replace('<span id="placa_vehiculo2">', $datos['placa'], $html);
        $html = str_replace('<span id="nombre_conductor">', $datos['nombres_completos'], $html);
        $html = str_replace('<span id="dni_conductor">', $datos['dni'], $html);
        
        // Formatear el monto solo si es un número
        $montoFormateado = is_numeric($datos['monto_pago']) ? number_format($datos['monto_pago'], 2) : $datos['monto_pago'];
        $html = str_replace('<span id="monto_pago"></span>', '.' . $montoFormateado, $html);
        $html = str_replace('<span id="nombre_conductor2">', $datos['nombres_completos'], $html);
        $html = str_replace('<span id="dni_conductor2">', $datos['dni'], $html);
        
        $condicionMin = strtolower(trim($datos['condicion'])); // Convertir toda la cadena a minúsculas y eliminar espacios
       
        $html = str_replace('<span id="condicion_vehiculo">', $condicionMin, $html); // Usar la condición modificada

        // Marcar el tipo de pago
        if ($datos['tipo_pago'] == 1) {
            // Marcar "Pago al contado"
            $html = str_replace('<span class="checkbox"></span> PAGO AL CONTADO', '<span class="checkbox">X</span> PAGO AL CONTADO', $html);
            // Eliminar "Pago financiado"
            $html = str_replace('<span class="checkbox"></span> PAGO FINANCIADO', '', $html);
        } elseif ($datos['tipo_pago'] == 2) {
            // Marcar "Pago financiado"
            $html = str_replace('<span class="checkbox"></span> PAGO FINANCIADO', '<span class="checkbox">X</span> PAGO FINANCIADO', $html);
            // Eliminar "Pago al contado"
            $html = str_replace('<span class="checkbox"></span> PAGO AL CONTADO', '', $html);

            // Generar el HTML para la información del monto inicial
            $infoHtml = '<div id="info-inicial">'; // Contenedor para el monto inicial y fecha de pago
            $infoHtml .= '<p style="margin-left: 25px;">◉ Inicial Monto: S/. ' . $datos['monto_inicial'] . ' Fecha: ' . $datos['fecha_pago'] . '</p>'; // Modificado: Agregado margen de 15px a la izquierda
            $infoHtml .= '</div>'; // Cierre del contenedor

            // Generar el HTML para el cronograma de cuotas
            $cuotasHtml = '<div id="cronograma-cuotas">'; // Contenedor de cuotas
           
            $cuotasHtml = '<ul>';
            foreach ($datos['cuotas'] as $index => $cuota) {
                $cuotasHtml .= "<li>Cuota " . ($index + 1) . ": Monto: <span id='cuota" . ($index + 1) . "_monto'>" . $cuota['monto_cuota'] . "</span> Fecha: <span id='cuota" . ($index + 1) . "_fecha'>" . $cuota['fecha_vencimiento'] . "</span></li>";
            }
            $cuotasHtml .= '</ul>';
            $cuotasHtml .= '</div>'; // Cerrar el contenedor de c

            // Reemplazar el marcador de cuotas en el HTML
            $html = str_replace('<div id="info-inicial"></div>', $infoHtml, $html); // Agregado: Inserta la información del monto inicial en su div
            $html = str_replace('<div id="cronograma-cuotas"></div>', $cuotasHtml, $html);
        }

        //var_dump($html); // Esto mostrará el HTML ya con los datos reemplazados
        ///exit(); // Detener la ejecución para inspeccionar el resultado
        return $html; // Retornar el HTML generado
    }
    
    private function numeroALetras($numero) {
        $unidades = ['', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve'];
        $decenas = ['', '', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'];
        $especiales = ['diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve'];
        
        $entero = (int)$numero;
        
        if ($entero < 10) {
            return $unidades[$entero];
        } elseif ($entero < 20) {
            return $especiales[$entero - 10];
        } elseif ($entero < 100) {
            $dec = (int)($entero / 10);
            $uni = $entero % 10;
            return $decenas[$dec] . ($uni > 0 ? ' y ' . $unidades[$uni] : '');
        }
        
        return (string)$entero; // Para números mayores, retornar como string
    }
    
    private function obtenerNombreMes($numeroMes) {
        $meses = [
            '01' => 'enero', '02' => 'febrero', '03' => 'marzo', '04' => 'abril',
            '05' => 'mayo', '06' => 'junio', '07' => 'julio', '08' => 'agosto',
            '09' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre'
        ];
        return $meses[str_pad($numeroMes, 2, '0', STR_PAD_LEFT)] ?? 'enero';
    }

    // ========================================
    // MÉTODOS PARA GENERAR CONTRATO DE ENTREGA DE VEHÍCULO
    // ========================================

    /**
     * Método principal para generar contrato de entrega de vehículo
     */
    public function generarContratoEntregaVehiculo()
    {
        try {
            // Recibir ID del financiamiento
            $input = json_decode(file_get_contents('php://input'), true);
            $idFinanciamiento = $input['id_financiamiento'] ?? null;
            
            // Validar ID
            if (!$idFinanciamiento) {
                echo json_encode([
                    'success' => false,
                    'error' => 'ID de financiamiento no proporcionado'
                ]);
                return;
            }
            
            // Obtener datos necesarios
            $financiamiento = $this->obtenerDatosFinanciamientoEntrega($idFinanciamiento);
            $cliente = $this->obtenerDatosClienteEntrega($financiamiento);
            $vehiculo = $this->obtenerDatosVehiculoEntrega($financiamiento);
            
            // Cargar y llenar template
            $html = $this->cargarYLlenarTemplateEntrega($financiamiento, $cliente, $vehiculo);
            
            // Generar PDF
            $pdf = $this->generarPDFEntrega($html);
            
            // Preparar nombre del archivo
            $nombreCliente = trim(
                ($cliente['nombres'] ?? '') . '_' . 
                ($cliente['apellido_paterno'] ?? '') . '_' . 
                ($cliente['apellido_materno'] ?? '')
            );
            $nombreCliente = preg_replace('/[^A-Za-z0-9_]/', '_', $nombreCliente);
            $nombreArchivo = "acta_entrega_vehiculo_{$idFinanciamiento}_{$nombreCliente}.pdf";
            
            // Retornar PDF en base64
            echo json_encode([
                'success' => true,
                'pdf' => base64_encode($pdf),
                'nombre' => $nombreArchivo
            ]);
            
        } catch (Exception $e) {
            error_log("Error en generarContratoEntregaVehiculo: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener datos del financiamiento por ID
     */
    private function obtenerDatosFinanciamientoEntrega($idFinanciamiento)
    {
        $financiamientoModel = new Financiamiento();
        $financiamiento = $financiamientoModel->getFinanciamientoById($idFinanciamiento);
        
        if (!$financiamiento) {
            throw new Exception("Financiamiento no encontrado con ID: $idFinanciamiento");
        }
        
        return $financiamiento;
    }

    /**
     * Obtener datos del cliente o conductor
     */
    private function obtenerDatosClienteEntrega($financiamiento)
    {
        $cliente = [];
        $direccion = [];
        
        // Determinar si es conductor o cliente
        if (!empty($financiamiento['id_conductor'])) {
            // Es un conductor
            $financiamientoModel = new Financiamiento();
            $cliente = $financiamientoModel->getConductorById($financiamiento['id_conductor']);
            
            // Obtener dirección del conductor
            $direccionConductorModel = new DireccionConductor();
            $direccion = $direccionConductorModel->obtenerDatosDireccion($financiamiento['id_conductor']);
        } else {
            // Es un cliente
            $clienteModel = new Cliente();
            $cliente = $clienteModel->getClienteById($financiamiento['id_cliente']);
            
            // Obtener dirección del cliente
            $direccion = $clienteModel->obtenerDatosDireccionCliente($financiamiento['id_cliente']);
        }
        
        // Combinar datos del cliente con su dirección
        $cliente['direccion'] = $direccion;
        
        return $cliente;
    }

    /**
     * Obtener datos del vehículo (producto y características)
     */
    private function obtenerDatosVehiculoEntrega($financiamiento)
    {
        $vehiculo = [];

        // Obtener SOLO los datos del producto del almacén (productosv2 y caracteristicas_producto)
        // NO usar la tabla vehiculos porque esos son vehículos propios del conductor
        $financiamientoModel = new Financiamiento();
        $caracteristicasModel = new CaracteristicaProducto();

        // Obtener el producto del almacén
        $producto = $financiamientoModel->obtenerProductoConCategoria($financiamiento['idproductosv2']);

        if (!$producto) {
            throw new Exception("Producto no encontrado con ID: " . $financiamiento['idproductosv2']);
        }

        // Obtener características del producto del almacén
        $caracteristicas = $caracteristicasModel->obtenerCaracteristicas($financiamiento['idproductosv2']);

        // Agregar el nombre del producto
        $vehiculo['producto'] = $producto;

        // Obtener marca y modelo directamente del producto (si existen)
        if (isset($producto['marca']) && !empty($producto['marca'])) {
            $vehiculo['marca'] = $producto['marca'];
        }
        if (isset($producto['modelo']) && !empty($producto['modelo'])) {
            $vehiculo['modelo'] = $producto['modelo'];
        }

        // Convertir características a array asociativo
        if ($caracteristicas && is_array($caracteristicas)) {
            foreach ($caracteristicas as $caract) {
                $nombreCaract = strtolower($caract['nombre_caracteristicas']);

                // Mapear nombres de características
                switch ($nombreCaract) {
                    case 'chasis':
                        $vehiculo['numero_chasis'] = $caract['valor_caracteristica'];
                        $vehiculo['chasis'] = $caract['valor_caracteristica'];
                        break;
                    case 'marca':
                        // Solo sobrescribir si no hay marca en el producto
                        if (!isset($vehiculo['marca'])) {
                            $vehiculo['marca'] = $caract['valor_caracteristica'];
                        }
                        break;
                    case 'modelo':
                        // Solo sobrescribir si no hay modelo en el producto
                        if (!isset($vehiculo['modelo'])) {
                            $vehiculo['modelo'] = $caract['valor_caracteristica'];
                        }
                        break;
                    case 'color':
                        $vehiculo['color'] = $caract['valor_caracteristica'];
                        break;
                    case 'anio':
                    case 'año':
                        $vehiculo['anio'] = $caract['valor_caracteristica'];
                        break;
                    case 'placa':
                        $vehiculo['placa'] = $caract['valor_caracteristica'];
                        break;
                    default:
                        $vehiculo[$caract['nombre_caracteristicas']] = $caract['valor_caracteristica'];
                        break;
                }
            }
        }

        return $vehiculo;
    }

    /**
     * Formatear dirección completa
     */
    private function formatearDireccionEntrega($direccion)
    {
        if (!$direccion || !is_array($direccion)) {
            return 'Dirección no disponible';
        }
        
        $partes = [];
        
        if (!empty($direccion['direccion_detalle'])) {
            $partes[] = $direccion['direccion_detalle'];
        }
        if (!empty($direccion['distrito'])) {
            $partes[] = $direccion['distrito'];
        }
        if (!empty($direccion['provincia'])) {
            $partes[] = $direccion['provincia'];
        }
        if (!empty($direccion['departamento'])) {
            $partes[] = $direccion['departamento'];
        }
        
        return !empty($partes) ? implode(', ', $partes) : 'Dirección no disponible';
    }

    /**
     * Obtener nombre del mes en español (mayúsculas)
     */
    private function obtenerNombreMesEntrega($numeroMes)
    {
        $meses = [
            '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL',
            '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO',
            '09' => 'SEPTIEMBRE', '10' => 'OCTUBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE'
        ];
        
        $mesFormateado = str_pad($numeroMes, 2, '0', STR_PAD_LEFT);
        return $meses[$mesFormateado] ?? 'ENERO';
    }

    /**
     * Cargar template HTML y llenar con datos
     */
    private function cargarYLlenarTemplateEntrega($financiamiento, $cliente, $vehiculo)
    {
        $rutaTemplate = "app" . DIRECTORY_SEPARATOR . "contratos" . DIRECTORY_SEPARATOR . "entrga_vehiculo.html";
        
        if (!file_exists($rutaTemplate)) {
            throw new Exception("Template no encontrado: $rutaTemplate");
        }
        
        $html = file_get_contents($rutaTemplate);
        
        // Preparar datos para reemplazo
        $datos = [
            // Datos del vehículo (producto del almacén)
            'nombre_producto' => isset($vehiculo['producto']['nombre']) ? strtoupper($vehiculo['producto']['nombre']) : 'N/A',
            'marca' => $vehiculo['marca'] ?? 'N/A',
            'modelo' => $vehiculo['modelo'] ?? 'N/A',
            'chasis' => $vehiculo['numero_chasis'] ?? $vehiculo['chasis'] ?? 'N/A',
            'placa' => $vehiculo['placa'] ?? 'N/A',
            'color' => $vehiculo['color'] ?? 'N/A',
            'anio' => $vehiculo['anio'] ?? 'N/A',
            
            // Datos del asociado
            'nombre_asociado' => trim(
                ($cliente['nombres'] ?? '') . ' ' . 
                ($cliente['apellido_paterno'] ?? '') . ' ' . 
                ($cliente['apellido_materno'] ?? '')
            ),
            'dni_asociado' => $cliente['nro_documento'] ?? $cliente['n_documento'] ?? 'N/A',
            'domicilio_asociado' => $this->formatearDireccionEntrega($cliente['direccion'] ?? []),
            'celular_asociado' => $cliente['telefono'] ?? 'N/A',
            'correo_asociado' => $cliente['correo'] ?? 'N/A',
            
            // Fecha de firma
            'dia_firma' => date('d'),
            'mes_firma' => $this->obtenerNombreMesEntrega(date('m')),
            'anio_firma' => date('Y'),
            
            // Datos para firma (repetidos para la sección de firmas)
            'nombre_firma' => trim(
                ($cliente['nombres'] ?? '') . ' ' . 
                ($cliente['apellido_paterno'] ?? '') . ' ' . 
                ($cliente['apellido_materno'] ?? '')
            ),
            'dni_firma' => $cliente['nro_documento'] ?? $cliente['n_documento'] ?? 'N/A'
        ];
        
        // Reemplazar placeholders en el HTML
        foreach ($datos as $campo => $valor) {
            $html = str_replace("{{{$campo}}}", $valor, $html);
        }
        
        return $html;
    }

    /**
     * Generar PDF desde HTML
     */
    private function generarPDFEntrega($html)
    {
        try {
            $mpdf = new \Mpdf\Mpdf([
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 15,
                'margin_bottom' => 15,
                'format' => 'A4'
            ]);
            
            $mpdf->WriteHTML($html);
            
            return $mpdf->Output('', 'S'); // Retornar como string
        } catch (Exception $e) {
            throw new Exception("Error al generar PDF: " . $e->getMessage());
        }
    }
    
    private function generarContratoGrupo38($financiamiento, $persona, $tipoPersona, $nombrePersona) {
        // Cargar la plantilla HTML
        $templatePath = 'app/contratos/contrato_credigo_grupo4.html';
        
        if (!file_exists($templatePath)) {
            throw new Exception("No se encontró la plantilla del contrato para el grupo 38");
        }
        
        $html = file_get_contents($templatePath);
        
        // Preparar los datos para reemplazar
        $nombreCompleto = strtoupper($nombrePersona);
        $tipoDocumento = strtoupper($persona['tipo_doc'] ?? 'DNI');
        $numeroDocumento = $persona['nro_documento'] ?? $persona['n_documento'] ?? '';
        $fechaActual = date('d') . ' DE ' . strtoupper($this->obtenerNombreMes(date('m'))) . ' DE ' . date('Y');
        
        // Reemplazar los placeholders en el HTML
        $html = str_replace('{{NOMBRE_COMPLETO}}', $nombreCompleto, $html);
        $html = str_replace('{{TIPO_DOCUMENTO}}', $tipoDocumento, $html);
        $html = str_replace('{{NUMERO_DOCUMENTO}}', $numeroDocumento, $html);
        $html = str_replace('{{FECHA_ACTUAL}}', $fechaActual, $html);
        
        return $html;
    }
}
