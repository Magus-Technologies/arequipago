<?php

require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/vendor/autoload.php';
require_once "app/models/Venta.php";
require_once "app/models/Cliente.php";
require_once "app/models/DocumentoEmpresa.php";
require_once "app/models/ProductoVenta.php";
require_once "app/models/VentaServicio.php";
require_once "app/models/Varios.php";
require_once "app/clases/SendURL.php";
require_once "app/models/Usuario.php";
require_once "app/models/Financiamiento.php";
require_once "app/models/Conductor.php";

class ReportFinanciamientoController extends Controller
{
    private $mpdf;
    private $conexion;
  
    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    public function generateNotaVenta($idConductor, $idAsesor, $cuotasSeleccionadas, $idPago, $monedaEfectivo) 
    {
      // var_dump($idConductor, $idAsesor, $cuotasSeleccionadas, $idPago, $monedaEfectivo); 
        try {

            $this->mpdf = new \Mpdf\Mpdf([
                'format' => [168, 297]  // 168 mm de ancho y 297 mm de alto (manteniendo el alto estándar de A4)
            ]);

            // Obtener datos del pago
            $sql = "SELECT * FROM pagos_financiamiento WHERE idpagos_financiamiento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idPago);
            $stmt->execute();
            $resultPago = $stmt->get_result();
            $pago = $resultPago->fetch_assoc();
            
            if (!$pago) {
                throw new Exception("No se encontró el pago con ID: " . $idPago);
            }

            // Obtener la moneda del pago, si no existe usar S/. por defecto // MODIFICADO: Obtener moneda del pago
            $monedaPago = !empty($pago['moneda']) ? $pago['moneda'] : 'S/.';
            
            if (!empty($pago['fecha_pago']) && $pago['fecha_pago'] !== '0000-00-00 00:00:00') {
                $timestamp = strtotime($pago['fecha_pago']);  // Convertir a timestamp
                $fechaActual = date("d/m/Y H:i:s", $timestamp);  // Formatear la fecha y hora
            } else {
                $fechaActual = 'Fecha inválida';
            }
            
            // Generar número de boleta con ceros a la izquierda
            $nroBoleta = str_pad($idPago, 6, "0", STR_PAD_LEFT);
            
            $idFinanciamiento = null;
            $detalleCuotasHTML = '';

            if (!empty($cuotasSeleccionadas) && isset($cuotasSeleccionadas[0]['idCuota'])) {

                // Obtener información del producto y grupo de financiamiento
                // Tomamos el primer idCuota del array para obtener la información del financiamiento
                $primeraCuota = $cuotasSeleccionadas[0]['idCuota'];
                
                // Obtener datos de la cuota
                $sql = "SELECT * FROM cuotas_financiamiento WHERE idcuotas_financiamiento = ?";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param("i", $primeraCuota);
                $stmt->execute();
                $resultCuota = $stmt->get_result();
                $cuota = $resultCuota->fetch_assoc();
                
                if ($cuota) {
                    $idFinanciamiento = $cuota['id_financiamiento'];
                }
            }

            if (!$idFinanciamiento && isset($pago['id_financiamiento'])) {
                $idFinanciamiento = $pago['id_financiamiento'];
            }

            if (!$idFinanciamiento) {
                throw new Exception("No se pudo obtener el ID de financiamiento");
            }
            
            // Obtener datos del financiamiento
            $sql = "SELECT * FROM financiamiento WHERE idfinanciamiento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idFinanciamiento);
            $stmt->execute();
            $resultFinanciamiento = $stmt->get_result();
            $financiamiento = $resultFinanciamiento->fetch_assoc();
            
            if (!$financiamiento) {
                throw new Exception("No se encontró el financiamiento con ID: " . $idFinanciamiento);
            }

            // Obtener la moneda del financiamiento, si no existe usar S/. por defecto // MODIFICADO: Obtener moneda del financiamiento
            $monedaFinanciamiento = !empty($financiamiento['moneda']) ? $financiamiento['moneda'] : 'S/.';
            
            // Obtener datos del producto
            $sql = "SELECT * FROM productosv2 WHERE idproductosv2 = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $financiamiento['idproductosv2']);
            $stmt->execute();
            $resultProducto = $stmt->get_result();
            $producto = $resultProducto->fetch_assoc();
            
            if (!$producto) {
                throw new Exception("No se encontró el producto con ID: " . $financiamiento['idproductosv2']);
            }
            
            if ($financiamiento['grupo_financiamiento'] === "notGrupo") {
                $plan = ["nombre_plan" => "Sin grupo de financiamiento"]; // ✅ Se asigna un valor por defecto
            } else {
                // Obtener datos del plan de financiamiento
                $sql = "SELECT * FROM planes_financiamiento WHERE idplan_financiamiento = ?";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param("i", $financiamiento['grupo_financiamiento']);
                $stmt->execute();
                $resultPlan = $stmt->get_result();
                $plan = $resultPlan->fetch_assoc();
                
                if (!$plan) {
                    throw new Exception("No se encontró el plan de financiamiento con ID: " . $financiamiento['grupo_financiamiento']);
                }
            }

            // MODIFICADO: Verificar si tenemos un conductor o un cliente
            $nombrePersona = '';
            $tipoDocumento = '';
            $numeroDocumento = '';

            // MODIFICADO: Verificar en la tabla pagos_financiamiento si hay un id_conductor o un id_cliente
            if ($pago['id_conductor']) {
                // MODIFICADO: Es un conductor, usamos la clase Conductor
                $conductor = new Conductor();
                $conductor->setIdConductor($pago['id_conductor']);
                $conductor->obtenerDatos();
                
                $nombrePersona = $conductor->getNombres() . ' ' . $conductor->getApellidoPaterno() . ' ' . $conductor->getApellidoMaterno();
                $tipoDocumento = $conductor->getTipoDoc();
                $numeroDocumento = $conductor->getNroDocumento();
            } elseif ($pago['id_cliente']) {
                // MODIFICADO: Es un cliente, obtenemos datos del cliente
                $sql = "SELECT * FROM clientes_financiar WHERE id = ?";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param("i", $pago['id_cliente']);
                $stmt->execute();
                $resultCliente = $stmt->get_result();
                $cliente = $resultCliente->fetch_assoc();
                
                if ($cliente) {
                    $nombrePersona = $cliente['nombres'] . ' ' . $cliente['apellido_paterno'] . ' ' . $cliente['apellido_materno'];
                    $tipoDocumento = $cliente['tipo_doc'];
                    $numeroDocumento = $cliente['n_documento'];
                } else {
                    $nombrePersona = "Cliente no encontrado";
                    $tipoDocumento = "N/A";
                    $numeroDocumento = "N/A";
                }
            } else {
                // MODIFICADO: No hay ni conductor ni cliente registrado en el pago, buscamos por el ID proporcionado
                if ($idPersona) {
                    // MODIFICADO: Primero intentamos buscar como conductor
                    $conductor = new Conductor();
                    $conductor->setIdConductor($idPersona);
                    if ($conductor->obtenerDatos()) {
                        // MODIFICADO: Es un conductor
                        $nombrePersona = $conductor->getNombres() . ' ' . $conductor->getApellidoPaterno() . ' ' . $conductor->getApellidoMaterno();
                        $tipoDocumento = $conductor->getTipoDoc();
                        $numeroDocumento = $conductor->getNroDocumento();
                    } else {
                        // MODIFICADO: No es un conductor, buscamos como cliente
                        $sql = "SELECT * FROM clientes_financiar WHERE id = ?";
                        $stmt = $this->conexion->prepare($sql);
                        $stmt->bind_param("i", $idPersona);
                        $stmt->execute();
                        $resultCliente = $stmt->get_result();
                        $cliente = $resultCliente->fetch_assoc();
                        
                        if ($cliente) {
                            $nombrePersona = $cliente['nombres'] . ' ' . $cliente['apellido_paterno'] . ' ' . $cliente['apellido_materno'];
                            $tipoDocumento = $cliente['tipo_doc'];
                            $numeroDocumento = $cliente['n_documento'];
                        } else {
                            $nombrePersona = "Persona no encontrada";
                            $tipoDocumento = "N/A";
                            $numeroDocumento = "N/A";
                        }
                    }
                } else {
                    $nombrePersona = "No se proporcionó ID de persona";
                    $tipoDocumento = "N/A";
                    $numeroDocumento = "N/A";
                }
            }
            
            // Obtener datos del asesor
            $usuario = new Usuario();
            $datosAsesor = $usuario->getData($idAsesor);
            
            // Calcular el monto total
            $montoTotal = 0;
            
            if (!empty($cuotasSeleccionadas)) {
                foreach ($cuotasSeleccionadas as $cuotaSeleccionada) {
                    // Obtener datos completos de la cuota
                    $sql = "SELECT * FROM cuotas_financiamiento WHERE idcuotas_financiamiento = ?";
                    $stmt = $this->conexion->prepare($sql);
                    $stmt->bind_param("i", $cuotaSeleccionada['idCuota']);
                    $stmt->execute();
                    $resultDetalleCuota = $stmt->get_result();
                    $detalleCuota = $resultDetalleCuota->fetch_assoc();
                    
                    // Agregar el monto de la cuota al total
                    $montoTotal += $cuotaSeleccionada['monto'];
                    
                    // Generar HTML para la cuota
                    $detalleCuotasHTML .= "<div class='cuota-item'>
                                            <span>Cuota N° {$detalleCuota['numero_cuota']}</span>
                                            <span>{$monedaFinanciamiento} {$cuotaSeleccionada['monto']}</span>
                                        </div>";
                    
                    // Si hay mora, agregarla también
                    if (isset($cuotaSeleccionada['mora']) && $cuotaSeleccionada['mora'] > 0) {
                        $montoTotal += $cuotaSeleccionada['mora'];
                        $detalleCuotasHTML .= "<div class='cuota-item'>
                                                <span>Mora de Cuota N° {$detalleCuota['numero_cuota']}</span>
                                                <span>{$monedaFinanciamiento} {$cuotaSeleccionada['mora']}</span>
                                            </div>";
                    }
                }
            } else {
                // Si no hay cuotas, mostrar solo el monto total // MODIFICADO: Mostrar monto total cuando no hay cuotas
                $detalleCuotasHTML = "<div class='cuota-item'>
                                    <span>Pago</span>
                                    <span>{$monedaFinanciamiento} {$pago['monto']}</span>
                                </div>";
            }
            
            // Nombre completo del asesor
            $nombreAsesor = $datosAsesor['nombres'] . ' ' . $datosAsesor['apellidos'];
            
            $html = '
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Boleta de Pago - Financiamiento</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        width: 80%;
                        max-width: 600px;
                        margin: auto;
                        border: 1px solid black;
                        padding: 10px;
                        background-color: #f9f9f9;
                        box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2);
                        line-height: 1.1;
                    }
                    .header {
                        text-align: center;
                        border-bottom: 1px solid black;
                        padding-bottom: 5px;
                    }
                    .title {
                        margin-top: 6px;
                        text-transform: uppercase;
                        font-weight: bold;
                        font-size: 15px;
                    }
                    .info-section {
                        font-size: 12px;
                        padding: 6px;
                        background-color: #e8f4fc;
                        border-radius: 4px;
                    }
                    .cuota-item {
                        display: flex;
                        justify-content: space-between;
                        border-bottom: 1px solid #ccc;
                        padding: 2px 0;
                        font-size: 12px;
                    }
                    .totales-section {
                        margin-top: 6px;
                        padding: 5px;
                        background-color: #f0f0f0;
                        border-radius: 4px;
                    }
                    .totales-section p {
                        margin: 3px 0;
                        font-size: 12px;
                    }
                    .resumen {
                        text-align: right;
                        font-weight: bold;
                        margin-top: 5px;
                        font-size: 13px;
                    }
                    .logo {
                        width: 530px; /* Cambié el tamaño a más del doble del anterior (de 150px a 320px) */ 
                        margin-bottom: 4px; /* Espacio debajo del logo */
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <img src="' . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'logo-ticket.png' . '" alt="Logo Empresa" class="logo">
                    <h3>AREQUIPA GO E.I.R.L.</h3>
                    <p>RUC: 20612112763</p>
                    <p>Urb. Adepa Mz L Lt 15 - Distrito de José Luis Bustamante y Rivero, Arequipa</p>
                </div>

                <h4 class="title">Boleta de Pago de Financiamiento</h4>

                <div class="info-section">
                    <p>Fecha: ' . $fechaActual . '</p>
                    <p>Boleta N°: ' . $nroBoleta . '</p>
                    <p>Producto: ' . $producto['nombre'] . '</p>
                    <p>Nombre del Financiamiento: ' . $producto['nombre'] . '</p>
                    <p>Grupo de Financiamiento: ' . $plan['nombre_plan'] . '</p>
                    <p>Nombre: ' . $nombrePersona . '</p>  <!-- MODIFICADO: Cambiado "Conductor" por "Nombre" -->
                <p>Documento: ' . $tipoDocumento . ' N° ' . $numeroDocumento . '</p>  <!-- MODIFICADO: Usando variables -->
                    <p>Método de Pago: ' . $pago['metodo_pago'] . '</p>
                </div>

                <div class="detalle-section">
                    <h5>Detalle de Cuotas</h5>
                    ' . $detalleCuotasHTML . '
                </div>

                <div class="totales-section">
                    <p><strong>Total a Pagar:</strong> ' . $monedaFinanciamiento . ' ' . number_format($pago['monto'], 2) . '</p>  <!-- MODIFICADO: Usar moneda del financiamiento -->
                    <p><strong>Efectivo recibido:</strong> ' . $monedaPago . ' ' . number_format($pago['efectivo_recibido'], 2) . '</p>  <!-- MODIFICADO: Usar moneda del pago -->
                    <p><strong>Vuelto:</strong> ' . $monedaPago . ' ' . number_format($pago['vuelto'], 2) . '</p>  <!-- MODIFICADO: Usar moneda del pago -->
                </div>

                <p class="resumen">Total Ingresado: ' . $monedaFinanciamiento . ' ' . number_format($pago['monto'], 2) . '</p>  <!-- MODIFICADO: Usar moneda del financiamiento -->
                <p><strong>Asesor de Cobro:</strong> ' . $nombreAsesor . '</p>
            </body>
            </html>';

            // Crear el documento PDF usando mPDF
            $this->mpdf->WriteHTML($html);
            $pdfOutput = $this->mpdf->Output('', 'S'); 

            // Convertir el contenido PDF a base64
            $pdfBase64 = base64_encode($pdfOutput); 

            return $pdfBase64; 

        } catch (Exception $e) {
            // Manejar errores
            error_log("Error en generateNotaVenta: " . $e->getMessage());
            echo "Error al generar la boleta: " . $e->getMessage();
        }
    }

    public function downloadReportFinance() {
        try {
            $idPago = $_POST['idPago'];
    
            // Obtener los datos del pago desde el modelo Financiamiento
            $financiamientoModel = new Financiamiento(); 
            $datosPago = $financiamientoModel->getDataPago($idPago);
    
            if ($datosPago) {
                // Preparar los parámetros para generateNotaVenta con el tipo de datos correcto
                $idConductor = (int)$datosPago['id_conductor']; // Convertir a entero
                $idAsesor = (string)$datosPago['id_asesor']; // Convertir a string
                $monedaEfectivo = $datosPago['moneda'];
    
                // Construir el array de cuotas seleccionadas
                $cuotasSeleccionadas = [];
                foreach ($datosPago['cuotas'] as $cuota) {
                    $cuotasSeleccionadas[] = [
                        'idCuota' => $cuota['id_cuota'],
                        'monto' => $cuota['monto'],
                        'mora' => $cuota['mora'],
                    ];
                }
    
           
                // Llamar a generateNotaVenta para generar el PDF en base64
                $pdfBase64 = $this->generateNotaVenta($idConductor, $idAsesor, $cuotasSeleccionadas, $idPago, $monedaEfectivo);
    
                // Retornar el PDF en base64
                echo json_encode(['pdfBase64' => $pdfBase64]);
            } else {
                echo json_encode(['error' => 'No se encontraron datos del pago']);
            }
        } catch (Exception $e) {
            error_log("Error en downloadReportFinance: " . $e->getMessage());
            echo json_encode(['error' => 'Error al generar el PDF']);
        }
    }    

    public function generateBoletaFinance() {
        try {
            // Obtener datos del POST
            $data = json_decode(file_get_contents('php://input'), true);
    
            if (!isset($data['id']) || !isset($data['pagos'])) {
                throw new Exception("Datos incompletos.");
            }
    
            $id = $data['id'];
            $pagos = $data['pagos']; // Array de pagos
            $metodoPago = isset($data['metodoPago']) ? $data['metodoPago'] : ''; // Obtener método d
    
            // Modificado: Obtener tanto id_conductor como id_cliente
            $stmt = $this->conexion->prepare("SELECT id_conductor, id_cliente, moneda FROM financiamiento WHERE idfinanciamiento = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $financiamiento = $result->fetch_assoc();
    
            if (!$financiamiento) {
                throw new Exception("Financiamiento no encontrado.");
            }
    
            // Modificado: Determinar si usar conductor o cliente
            $idConductor = $financiamiento['id_conductor'];
            $idCliente = $financiamiento['id_cliente'];
            $moneda = $financiamiento['moneda'];
            $idAsesor = $_SESSION['usuario_id'] ?? null;
    
            if (!$idAsesor) {
                throw new Exception("Sesión de asesor no encontrada.");
            }
    
            $pdfs = []; // Array para almacenar los PDFs generados
    
            // Instanciar modelo de Financiamiento
            $financiamientoModel = new Financiamiento();
    
            // Iterar sobre los pagos
            foreach ($pagos as $pago) {
                // Validar que las claves existan antes de usarlas
                if (!isset($pago['monto']) || !isset($pago['tipo'])) {
                    throw new Exception("Datos de pago incompletos.");
                }
    
                $monto = $pago['monto'];
                $concepto = $pago['tipo'];
    
                // Registrar el pago
                $pagoResult = $financiamientoModel->newPago(
                    $idConductor,
                    $idAsesor,
                    $monto,
                    $concepto,
                    $monto, // efectivo recibido igual al monto
                    0, // vuelto
                    $moneda,
                    $id,
                    $idCliente,
                    $metodoPago
                );
    
                if (!$pagoResult['success']) {
                    throw new Exception($pagoResult['message']);
                }
    
                // Generar PDF
                $pdfBase64 = $this->generateNotaVentaPagosInstant(
                    $idConductor,
                    $id,
                    $idAsesor,
                    $monto,
                    $pagoResult['id_pago'],
                    $moneda,
                    $concepto,
                    $idCliente
                );
    
                // Guardar en el array de PDFs
                $pdfs[] = [
                    'base64' => $pdfBase64,
                    'tipo' => $concepto
                ];
            }
    
            echo json_encode(['pdfs' => $pdfs]);
    
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    


    public function generateNotaVentaPagosInstant($idConductor, $idFinanciamiento, $idAsesor, $monto, $idPago, $monedaEfectivo, $tipoPago, $idCliente = null)
    {
      // var_dump($idConductor, $idAsesor, $cuotasSeleccionadas, $idPago, $monedaEfectivo); 
        try {
            $this->mpdf = new \Mpdf\Mpdf([
                'format' => [168, 297]  // 168 mm de ancho y 297 mm de alto (manteniendo el alto estándar de A4)
            ]);

            // Obtener datos del pago
            $sql = "SELECT * FROM pagos_financiamiento WHERE idpagos_financiamiento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idPago);
            $stmt->execute();
            $resultPago = $stmt->get_result();
            $pago = $resultPago->fetch_assoc();
            
            if (!$pago) {
                throw new Exception("No se encontró el pago con ID: " . $idPago);
            }

            // Obtener la moneda del pago, si no existe usar S/. por defecto // MODIFICADO: Obtener moneda del pago
            $monedaPago = !empty($pago['moneda']) ? $pago['moneda'] : 'S/.';
            
            if (!empty($pago['fecha_pago']) && $pago['fecha_pago'] !== '0000-00-00 00:00:00') {
                $timestamp = strtotime($pago['fecha_pago']);  // Convertir a timestamp
                $fechaActual = date("d/m/Y H:i:s", $timestamp);  // Formatear la fecha y hora
            } else {
                $fechaActual = 'Fecha inválida';
            }
            
            // Generar número de boleta con ceros a la izquierda
            $nroBoleta = str_pad($idPago, 6, "0", STR_PAD_LEFT);
                        
            // Obtener datos del financiamiento
            $sql = "SELECT * FROM financiamiento WHERE idfinanciamiento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idFinanciamiento);
            $stmt->execute();
            $resultFinanciamiento = $stmt->get_result();
            $financiamiento = $resultFinanciamiento->fetch_assoc();
            
            if (!$financiamiento) {
                throw new Exception("No se encontró el financiamiento con ID: " . $idFinanciamiento);
            }

            // Obtener la moneda del financiamiento, si no existe usar S/. por defecto // MODIFICADO: Obtener moneda del financiamiento
            $monedaFinanciamiento = !empty($financiamiento['moneda']) ? $financiamiento['moneda'] : 'S/.';
            
            // 🔴 Inicializar la variable para el certificado
            $certificadoHtml = '';
            $mostrarCertificado = false;
            $valorCertificado = 0;
            
            // 🔴 CONDICIÓN 1: Verificar si hay un valor válido en id_variante
            if (!empty($financiamiento['id_variante']) && $financiamiento['id_variante'] != '0') {
                // 🔴 Consultar la tabla grupos_variantes para obtener el monto_sin_interes
                $sql = "SELECT monto_sin_interes FROM grupos_variantes WHERE idgrupos_variantes = ?"; // 🔴 Corregido el nombre del campo
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param("i", $financiamiento['id_variante']);
                $stmt->execute();
                $resultVariante = $stmt->get_result();
                $variante = $resultVariante->fetch_assoc();
                
                if ($variante) {
                    // 🔴 Se cumple la condición 1, mostrar certificado con valor de grupos_variantes
                    $mostrarCertificado = true;
                    $valorCertificado = $variante['monto_sin_interes'];
                }
            }
            
            // 🔴 CONDICIÓN 2: Verificar si grupo_financiamiento es numérico y tiene fechas válidas en planes_financiamiento
            if (!$mostrarCertificado && !empty($financiamiento['grupo_financiamiento']) && is_numeric($financiamiento['grupo_financiamiento'])) {
                // 🔴 Consultar la tabla planes_financiamiento para verificar fechas y obtener monto_sin_interes
                $sql = "SELECT monto_sin_interes, fecha_inicio, fecha_fin FROM planes_financiamiento WHERE idplan_financiamiento = ?";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param("i", $financiamiento['grupo_financiamiento']);
                $stmt->execute();
                $resultPlan = $stmt->get_result();
                $planFinanciamiento = $resultPlan->fetch_assoc();
                
                if ($planFinanciamiento && !empty($planFinanciamiento['fecha_inicio']) && !empty($planFinanciamiento['fecha_fin'])) {
                    // 🔴 Se cumple la condición 2, mostrar certificado con valor de planes_financiamiento
                    $mostrarCertificado = true;
                    $valorCertificado = $planFinanciamiento['monto_sin_interes'];
                }
            }
            
            // 🔴 Si se cumple alguna de las condiciones, crear la línea de certificado
            if ($mostrarCertificado) {
                $certificadoHtml = '<p><strong>Certificado:</strong> ' . $monedaFinanciamiento . ' ' . number_format($valorCertificado, 2) . '</p>';
            }

            // Obtener datos del producto
            $sql = "SELECT * FROM productosv2 WHERE idproductosv2 = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $financiamiento['idproductosv2']);
            $stmt->execute();
            $resultProducto = $stmt->get_result();
            $producto = $resultProducto->fetch_assoc();
            
            if (!$producto) {
                throw new Exception("No se encontró el producto con ID: " . $financiamiento['idproductosv2']);
            }
            
            if ($financiamiento['grupo_financiamiento'] === "notGrupo") {
                $plan = ["nombre_plan" => "Sin grupo de financiamiento"]; // ✅ Se asigna un valor por defecto
            } else {
                // Obtener datos del plan de financiamiento
                $sql = "SELECT * FROM planes_financiamiento WHERE idplan_financiamiento = ?";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param("i", $financiamiento['grupo_financiamiento']);
                $stmt->execute();
                $resultPlan = $stmt->get_result();
                $plan = $resultPlan->fetch_assoc();
                
                if (!$plan) {
                    throw new Exception("No se encontró el plan de financiamiento con ID: " . $financiamiento['grupo_financiamiento']);
                }
            }

            $nombreCompleto = '';
            $tipoDoc = '';
            $nroDocumento = '';

            if ($idConductor && $idConductor > 0) {
            // Obtener datos del conductor
            $conductor = new Conductor();
            $conductor->setIdConductor($idConductor);
            $conductor->obtenerDatos();
            $nombreCompleto = $conductor->getNombres() . ' ' . $conductor->getApellidoPaterno() . ' ' . $conductor->getApellidoMaterno();
            $tipoDoc = $conductor->getTipoDoc();
            $nroDocumento = $conductor->getNroDocumento();
            } else if ($idCliente) {
                // Obtener datos del cliente
                $sql = "SELECT documento, datos FROM clientes WHERE id_cliente = ?";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param("i", $idCliente);
                $stmt->execute();
                $resultCliente = $stmt->get_result();
                $cliente = $resultCliente->fetch_assoc();
                
                if ($cliente) {
                    $nombreCompleto = $cliente['datos'];
                    $nroDocumento = $cliente['documento'];
                    $tipoDoc = strlen($cliente['documento']) == 8 ? "DNI" : "";
                }
            }

            // Obtener datos del asesor
            $usuario = new Usuario();
            $datosAsesor = $usuario->getData($idAsesor);

            // Nombre completo del asesor
            $nombreAsesor = $datosAsesor['nombres'] . ' ' . $datosAsesor['apellidos'];
          
            // Asignar valores por defecto a método de pago y concepto // MODIFICADO: Asignar valores por defecto
            $metodoPago = !empty($pago['metodo_pago']) ? $pago['metodo_pago'] : ""; // MODIFICADO: Asignar valor por defecto
            $concepto = !empty($pago['concepto']) ? $pago['concepto'] : "";


            $html = '
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Boleta de Pago - Financiamiento</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        width: 80%;
                        max-width: 600px;
                        margin: auto;
                        border: 1px solid black;
                        padding: 10px;
                        background-color: #f9f9f9;
                        box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2);
                        line-height: 1.1;
                    }
                    .header {
                        text-align: center;
                        border-bottom: 1px solid black;
                        padding-bottom: 5px;
                    }
                    .title {
                        margin-top: 6px;
                        text-transform: uppercase;
                        font-weight: bold;
                        font-size: 15px;
                    }
                    .info-section {
                        font-size: 12px;
                        padding: 6px;
                        background-color: #e8f4fc;
                        border-radius: 4px;
                    }
                    .cuota-item {
                        display: flex;
                        justify-content: space-between;
                        border-bottom: 1px solid #ccc;
                        padding: 2px 0;
                        font-size: 12px;
                    }
                    .totales-section {
                        margin-top: 6px;
                        padding: 5px;
                        background-color: #f0f0f0;
                        border-radius: 4px;
                    }
                    .totales-section p {
                        margin: 3px 0;
                        font-size: 12px;
                    }
                    .resumen {
                        text-align: right;
                        font-weight: bold;
                        margin-top: 5px;
                        font-size: 13px;
                    }
                    .logo {
                        width: 530px; /* Cambié el tamaño a más del doble del anterior (de 150px a 320px) */ 
                        margin-bottom: 4px; /* Espacio debajo del logo */
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <img src="' . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'logo-ticket.png' . '" alt="Logo Empresa" class="logo">
                    <h3>AREQUIPA GO E.I.R.L.</h3>
                    <p>RUC: 20612112763</p>
                    <p>Urb. Adepa Mz L Lt 15 - Distrito de José Luis Bustamante y Rivero, Arequipa</p>
                </div>

                <h4 class="title">Boleta de Pago de Financiamiento</h4>

                <div class="info-section">
                    <p>Fecha: ' . $fechaActual . '</p>
                    <p>Boleta N°: ' . $nroBoleta . '</p>
                    <p>Producto: ' . $producto['nombre'] . '</p>
                    <p>Nombre del Financiamiento: ' . $producto['nombre'] . '</p>
                    <p>Grupo de Financiamiento: ' . $plan['nombre_plan'] . '</p>
                     <p>Cliente: ' . $nombreCompleto . '</p>
                    <p>Documento: ' . $tipoDoc . ' N° ' . $nroDocumento . '</p>
                    <p>Método de Pago: ' . $metodoPago . '</p> 
                    <p>Concepto: ' . $concepto . '</p> 
                    ' . $certificadoHtml . '
                </div>

                <div class="detalle-section">
                      ' . $metodoPago . ': ' . $monedaEfectivo . ' ' . $pago['monto']. '
                </div>

                <div class="totales-section">
                    <p><strong>Total a Pagar:</strong> ' . $monedaFinanciamiento . ' ' . number_format($pago['monto'], 2) . '</p>  <!-- MODIFICADO: Usar moneda del financiamiento -->
                    <p><strong>Efectivo recibido:</strong> ' . $monedaPago . ' ' . number_format($pago['efectivo_recibido'], 2) . '</p>  <!-- MODIFICADO: Usar moneda del pago -->
                    <p><strong>Vuelto:</strong> ' . $monedaPago . ' ' . number_format($pago['vuelto'], 2) . '</p>  <!-- MODIFICADO: Usar moneda del pago -->
                </div>

                <p class="resumen">Total Ingresado: ' . $monedaFinanciamiento . ' ' . number_format($pago['monto'], 2) . '</p>  <!-- MODIFICADO: Usar moneda del financiamiento -->
                <p><strong>Asesor de Cobro:</strong> ' . $nombreAsesor . '</p>
            </body>
            </html>';

            // Crear el documento PDF usando mPDF
            
            $this->mpdf->WriteHTML($html);
            $pdfOutput = $this->mpdf->Output('', 'S'); 

            // Convertir el contenido PDF a base64
            $pdfBase64 = base64_encode($pdfOutput); 

            return $pdfBase64; 

        } catch (Exception $e) {
            // Manejar errores
            error_log("Error en generateNotaVenta: " . $e->getMessage());
            echo "Error al generar la boleta: " . $e->getMessage();
        }
    }
}