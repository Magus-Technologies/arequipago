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

                // 😊 Generar contrato de Excel para vehículos
                if ($esVehiculo) {
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
                        continue;
                    } catch (\Exception $e) {
                        error_log("Error generando contrato Excel de vehículo ID $idFinanciamiento: " . $e->getMessage());
                        continue;
                    }
                }
               
                
                if (!$esVehiculo) {
                    if (!in_array($producto['categoria'], ['Llantas', 'Aceites' , 'Celular', 'Chip (Linea corporativa)'])) {
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
                        $mpdf = new \Mpdf\Mpdf([
                            'margin_left' => 30, // Margen izquierdo (en milímetros)
                            'margin_right' => 30,
                        ]);
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
      
          ini_set('memory_limit', '512M');

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
          
          // 20. Marcar documentos según estados
          $this->marcarDocumentosEnExcel($worksheet, $estadosRequisitos, $tipoPersona);

          // 21. Fecha actual con formato personalizado - Celda C44
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
        $rutaBase = "app" . DIRECTORY_SEPARATOR . "contratos";  // Usamos DIRECTORY_SEPARATOR

        // Formatear fecha y hora
        $fechaCreacion = strtotime($financiamiento['fecha_creacion']);
        $hora = date('h:i A', $fechaCreacion);
        $dia = date('d', $fechaCreacion);
        $mes = date('m', $fechaCreacion);
        $anio = date('Y', $fechaCreacion);

        // Concatenar nombre completo de la persona (conductor o cliente)
        $nombrePersona = trim(
            $persona['nombres'] . ' ' .
            $persona['apellido_paterno'] . ' ' .
            $persona['apellido_materno']
        );

        // Generar textos dinámicos según tipo de persona
        $textoRol = $tipoPersona === 'conductor' ? 'conductor' : 'cliente';
        $fraseAfiliacion = $tipoPersona === 'conductor' ? ', afiliado a la empresa Arequipa Go donde actualmente labora' : '';
        $bloqueLicencia = $tipoPersona === 'conductor' ? ' y N° de licencia ' . $persona['nro_licencia'] : '';

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
        if ($categoria === 'Llantas') {
            $rutaArchivo = $rutaBase . DIRECTORY_SEPARATOR . "contrato_llantas.html";
        } elseif ($categoria === 'Aceites') {
            $rutaArchivo = $rutaBase . DIRECTORY_SEPARATOR . "contrato_aceites.html";
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
                $plantillaChip = str_replace('<span id="conductor">', $nombreConductor, $plantillaChip);
                $plantillaChip = str_replace('<span id="dni">', $conductor['nro_documento'], $plantillaChip);
                $plantillaChip = str_replace('<span id="licencia">', $conductor['nro_licencia'], $plantillaChip);
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
            'anio' => $anio,
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
            'licencia_bloque' => $bloqueLicencia,
            'frase_afiliacion' => $fraseAfiliacion,
            'clausula_conductor' => $clausulaConductor
        ];
    
        // Si es conductor, incluir licencia; si no, dejarla en blanco
        if ($tipoPersona === 'conductor') {
            $reemplazos['licencia'] = $persona['nro_licencia'];
        } else {
            $reemplazos['licencia'] = '';
        }
    
        foreach ($reemplazos as $id => $valor) {
            $plantilla = str_replace("<span id=\"$id\"></span>", $valor, $plantilla);
        }
    
        // Generar lista de cuotas
        $listaCuotas = '';
        foreach ($cuotas as $index => $cuota) {
            $fechaCuota = date('d/m/Y', strtotime($cuota['fecha_vencimiento']));
            $listaCuotas .= "<li><strong>" . ($index + 1) . "a cuota:</strong> S/ {$cuota['monto']} - Fecha: $fechaCuota</li>";
        }
        $plantilla = str_replace("<ul id=\"lista_cuotas\"></ul>", "<ul>$listaCuotas</ul>", $plantilla);
    

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
                    'monto_pago' => $datoPago[0]['monto_pago'],
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
                $sheet->setCellValue('B36', "$fechaActual"); // Mantiene la celda en B36
                $sheet->getStyle('B36')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

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
                
                
                // Definir la fecha actual en formato "día/mes/año"
                $fechaActual = date('d/m/Y'); 

                // Generar el contenido de la plantilla (obtenemos las dos secciones por separado)
                $htmlCompleto = $this->generarPlantillahtmltoPdf($datos);

                // Dividimos el contenido en secciones (suponiendo que el separador es "<div style='page-break-after: always;'></div>")
                $secciones = explode('<div style="page-break-after: always;"></div>', $htmlCompleto);

                // Validamos que haya al menos dos secciones
                $htmlSeccion1 = $secciones[0] ?? '';
                $htmlSeccion2 = $secciones[1] ?? '';

                // 1️⃣ Agregar la primera sección al PDF
                $mpdf->WriteHTML($htmlSeccion1);

                // Configurar el pie de página para la primera sección
                $mpdf->SetHTMLFooter('<div style="text-align: left; font-weight: normal; border-top: none;">AREQUIPA, ' . $fechaActual . '</div>');

                // 2️⃣ Agregar un salto de página manual antes de la segunda sección
                $mpdf->AddPage();

                // 3️⃣ Configurar el pie de página para la segunda sección
                $mpdf->SetHTMLFooter('<div style="text-align: left; font-weight: normal; border-top: none;">AREQUIPA, ' . $fechaActual . '</div>');

                // 4️⃣ Agregar la segunda sección al PDF
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
        $html = str_replace('<span id="domicilio_afiliado">', $datos['direccion_completa'], $html);
        $html = str_replace('<span id="placa_vehiculo">', $datos['placa'], $html);
        $html = str_replace('<span id="marca_vehiculo">', $datos['marca'], $html);
        $html = str_replace('<span id="modelo_vehiculo">', $datos['modelo'], $html);
        $html = str_replace('<span id="color_vehiculo">', $datos['color'], $html);
        $html = str_replace('<span id="anio_fabricacion">', $datos['anio'], $html);
        $html = str_replace('<span id="placa_vehiculo2">', $datos['placa'], $html);
        $html = str_replace('<span id="nombre_conductor">', $datos['nombres_completos'], $html);
        $html = str_replace('<span id="dni_conductor">', $datos['dni'], $html);
        
        $html = str_replace('<span id="monto_pago"></span>', '.' . number_format($datos['monto_pago'], 2), $html);
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
    
    
    
}
