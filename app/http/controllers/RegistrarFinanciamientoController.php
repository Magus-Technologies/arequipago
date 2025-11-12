<?php
require_once "app/models/Financiamiento.php";
require_once "app/models/CuotaFinanciamiento.php";
require_once "app/models/Conductor.php";
require_once "app/models/Productov2.php";
require_once "app/models/Cliente.php";
require_once "app/http/controllers/FinanciamientoController.php";

class RegistrarFinanciamientoController extends Controller
{
    private $conexion;
    
        public function __construct()
        {
            $this->conexion = (new Conexion())->getConexion();

        }

    public function guardarFinanciamiento()
    {
        try {
            // Obtener los datos recibidos por POST
            $datos = $_POST;

            // NUEVO: Detectar si es CrediYango al inicio
            $esCrediYango = isset($datos['grupo_financiamiento']) &&
                           ($datos['grupo_financiamiento'] == '45' || $datos['grupo_financiamiento'] == 45);

            // ✅ CORREGIDO: Normalizar estado_entrega ANTES de cualquier validación
            // Si NO viene el campo, establecerlo como NULL por defecto
            if (!isset($datos['estado_entrega'])) {
                $datos['estado_entrega'] = null;
            } else {
                // Si viene, validar que sea un valor correcto
                $estadoEntregaRaw = trim($datos['estado_entrega']);
                if ($estadoEntregaRaw === '' || $estadoEntregaRaw === 'null' || $estadoEntregaRaw === 'undefined') {
                    $datos['estado_entrega'] = null;
                } else if (!in_array($estadoEntregaRaw, ['pendiente', 'entregado'], true)) {
                    // Valor inválido, establecer como NULL
                    error_log("⚠️ WARNING: estado_entrega inválido recibido: '" . $estadoEntregaRaw . "'. Estableciendo como NULL");
                    $datos['estado_entrega'] = null;
                }
            }

            // ✅ NUEVO: Para CrediYango, SIEMPRE establecer estado_entrega como 'pendiente'
            // Ya no se asigna producto ID 37, ahora se selecciona el vehículo real
            if ($esCrediYango) {
                $datos['estado_entrega'] = 'pendiente';
                $datos['cantidad_producto'] = isset($datos['cantidad_producto']) ? $datos['cantidad_producto'] : 1;
                error_log("🚗 CrediYango detectado - Estableciendo estado_entrega='pendiente'");
                error_log("🔍 Controlador - estado_entrega después de asignar: '" . var_export($datos['estado_entrega'], true) . "' (tipo: " . gettype($datos['estado_entrega']) . ")");
                error_log("CrediYango: Registrando con producto real ID " . ($datos['id_producto'] ?? 'N/A') . " y estado_entrega='pendiente'");
            }

            // 🆕 NUEVO: Detectar si id_producto es "No disponible" y establecer estado_entrega
            if (isset($datos['id_producto']) && $datos['id_producto'] === "No disponible") {
                $datos['id_producto'] = 37;
                $datos['estado_entrega'] = 'pendiente';
                $datos['cantidad_producto'] = 0;
                error_log("Producto No disponible: Cambiado a ID 37 con estado_entrega='pendiente'");
            }

            // 🆕 NUEVO: Si NO es CrediYango y NO es "No disponible", estado_entrega debe ser NULL
            if (!$esCrediYango && (!isset($datos['estado_entrega']) || $datos['estado_entrega'] === null)) {
                $datos['estado_entrega'] = null;
            }

            // Obtener valor de cobrar mora con valor por defecto
            $datos['cobrar_mora'] = isset($datos['cobrar_mora']) ? intval($datos['cobrar_mora']) : 1;

                    // NUEVO: Obtener nombre personalizado si existe
        $datos['nombre_personalizado'] = isset($datos['nombre_personalizado']) && !empty($datos['nombre_personalizado'])
            ? trim($datos['nombre_personalizado'])
            : null;

        // ✅ NUEVO: Obtener flag de entrega especial (solo para plan 42)
        $datos['es_entrega_especial'] = isset($datos['es_entrega_especial']) && intval($datos['es_entrega_especial']) === 1
            ? 1
            : 0;

            // Obtener valor de verificación domiciliaria
            $verificacion_domiciliaria = isset($datos['verificacion_domiciliaria']) ? intval($datos['verificacion_domiciliaria']) : null;

            // NUEVO: Campos para CrediYango
            $fechaEntrega = isset($datos['fecha_entrega']) && !empty($datos['fecha_entrega']) ? $datos['fecha_entrega'] : null;
            $fechaInicioPagosCalculada = null;
            
            // Si hay fecha de entrega, calcular fecha de inicio de pagos automáticamente
            if ($fechaEntrega) {
                $fechaEntregaObj = new DateTime($fechaEntrega);
                $fechaEntregaObj->add(new DateInterval('P7D')); // Agregar 7 días
                $fechaInicioPagosCalculada = $fechaEntregaObj->format('Y-m-d');
                
                // Actualizar las fechas del cronograma si es CrediYango (ID 45)
                if (isset($datos['grupo_financiamiento']) && $datos['grupo_financiamiento'] == '45') {
                    $datos['fecha_inicio'] = $fechaInicioPagosCalculada;
                    // Recalcular fecha_fin basándose en la nueva fecha de inicio
                    $cantidadCuotas = intval($datos['cuotas']);
                    $frecuencia = $datos['frecuencia'] ?? 'semanal';
                    
                    $fechaFinObj = new DateTime($fechaInicioPagosCalculada);
                    if ($frecuencia === 'semanal') {
                        $fechaFinObj->add(new DateInterval('P' . ($cantidadCuotas * 7) . 'D'));
                    } else {
                        $fechaFinObj->add(new DateInterval('P' . $cantidadCuotas . 'M'));
                    }
                    $datos['fecha_fin'] = $fechaFinObj->format('Y-m-d');
                }
            }
            
            // Agregar los campos de CrediYango a los datos
            $datos['fecha_entrega'] = $fechaEntrega;
            $datos['fecha_inicio_pagos_calculada'] = $fechaInicioPagosCalculada;

            // Para CrediYango, fechasVencimiento puede ser null o vacío (se generan al entregar)
            $fechasVencimiento = isset($datos['fechas_vencimiento']) ? $datos['fechas_vencimiento'] : [];
            
// NUEVO: Detectar si es plan editable (ID 42)
$esPlanPersonalizado = (isset($datos['grupo_financiamiento']) && 
                        ($datos['grupo_financiamiento'] == '42' || 
                         $datos['grupo_financiamiento'] == 42));

// NUEVO: Para planes personalizados, validar campos adicionales
if ($esPlanPersonalizado) {
    // Normalizar campos que pueden venir con nombres diferentes
    if (isset($datos['monto_sin_intereses']) && !isset($datos['monto_sin_interes'])) {
        $datos['monto_sin_interes'] = $datos['monto_sin_intereses'];
    }
    
    // Normalizar frecuencia_pago (puede venir como 'frecuencia')
    if (isset($datos['frecuencia']) && !isset($datos['frecuencia_pago'])) {
        $datos['frecuencia_pago'] = $datos['frecuencia'];
    }
    
    // Normalizar tasa_interes (puede venir como 'tasa')
    if (isset($datos['tasa']) && !isset($datos['tasa_interes'])) {
        $datos['tasa_interes'] = $datos['tasa'];
    }
    
    // Normalizar monto_cuota (puede venir como 'valorCuota')
    if (isset($datos['valorCuota']) && !isset($datos['monto_cuota'])) {
        $datos['monto_cuota'] = $datos['valorCuota'];
    }
    
    $camposPersonalizados = [
        'monto_sin_interes' => 'Monto sin intereses',
        'cuota_inicial' => 'Cuota inicial',
        'monto_cuota' => 'Monto de cuota',
        'tasa_interes' => 'Tasa de interés',
        'frecuencia_pago' => 'Frecuencia de pago'
    ];
    
    $camposFaltantes = [];
    foreach ($camposPersonalizados as $campo => $nombre) {
        // Verificar si el campo existe y no está vacío
        $valor = isset($datos[$campo]) ? trim($datos[$campo]) : '';
        
        // Convertir a número si es necesario
        if (in_array($campo, ['monto_sin_interes', 'cuota_inicial', 'monto_cuota', 'tasa_interes'])) {
            $valor = floatval(str_replace(',', '', $valor));
        }
        
        // Para frecuencia_pago, solo verificar que no esté vacío
        if ($campo === 'frecuencia_pago') {
            if ($valor === '' || $valor === null) {
                $camposFaltantes[] = $nombre;
            }
        } else {
            // Para campos numéricos, verificar que no sean 0 o vacíos
            if ($valor === '' || ($valor === 0 && $campo !== 'tasa_interes')) {
                $camposFaltantes[] = $nombre;
            }
        }
    }
    
    if (!empty($camposFaltantes)) {
        throw new Exception("Para financiamientos personalizados, faltan los siguientes campos: " . implode(', ', $camposFaltantes));
    }
    
    // Calcular monto total si no viene
    if (empty($datos['monto_total'])) {
        $cuotaInicial = floatval(str_replace(',', '', $datos['cuota_inicial']));
        $montoCuota = floatval(str_replace(',', '', $datos['monto_cuota']));
        $cantidadCuotas = intval($datos['cuotas']);
        $datos['monto_total'] = $cuotaInicial + ($montoCuota * $cantidadCuotas);
    }
}

// 🔹 MODIFICADO: Para plan personalizado (ID 42), id_producto y cantidad_producto NO son obligatorios
if ($esPlanPersonalizado) {
    $camposRequeridos = ['monto_total', 'grupo_financiamiento', 'cuotas', 'estado', 'fecha_inicio', 'fecha_fin', 'fecha_creacion'];
    
    // Establecer valores por defecto para campos no obligatorios
    if (empty($datos['id_producto'])) {
        $datos['id_producto'] = 37; // ID genérico para servicios/planes personalizados
    }
    if (empty($datos['cantidad_producto'])) {
        $datos['cantidad_producto'] = 1;
    }
} else {
    $camposRequeridos = ['id_producto', 'monto_total', 'grupo_financiamiento', 'cuotas', 'estado', 'fecha_inicio', 'fecha_fin', 'fecha_creacion', 'cantidad_producto'];
}

foreach ($camposRequeridos as $campo) {
    if (empty($datos[$campo])) {
        throw new Exception("Falta el campo obligatorio: $campo");
    }
}



            // NUEVO: Verificar que al menos uno de id_conductor o id_cliente esté presente
            if (empty($datos['id_conductor']) && empty($datos['id_cliente'])) {
                throw new Exception("Debe especificar al menos un id_conductor o id_cliente");
            }

            // Obtener usuario_id de la sesión 🔹 Agregado para obtener el usuario
            $usuario_id = $_SESSION['usuario_id'] ?? null;
            if (!$usuario_id) {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo obtener el ID del usuario.']);
                return;
            }

            $rol_usuario = isset($_SESSION['id_rol']) ? intval($_SESSION['id_rol']) : null;

            // Instanciar el modelo ProductoV2 para actualizar el stock
            $productoModel = new ProductoV2(); // 🔹 Agregué esta línea para instanciar el modelo ProductoV2

            // 💥 Modificado: Obtener los datos del producto antes de actualizar el stock
            $producto = $productoModel->obtenerProductoPorId($datos['id_producto']);
            if (!$producto) {
                throw new Exception("No se encontró el producto con ID: " . $datos['id_producto']);
            }

            // 💥 Modificado: Determinar si el producto pertenece a una categoría especial
            $categoria_producto = strtolower(trim($producto['CATEGORIA']));
            $categorias_especiales = ['celular', 'celulares', 'llantas', 'llanta', 'aceites', 'aceite', 'baterias', 'batería', 'baterías', 'bateria'];
            $es_categoria_especial = false;

            foreach ($categorias_especiales as $categoria) {
                if (strpos($categoria_producto, $categoria) !== false) {
                    $es_categoria_especial = true;
                    break;
                }
            }

            // 💥 Modificado: Determinar si se debe registrar el movimiento en almacén
            $registrar_movimiento = true;
            $aprobado = 1; // Por defecto, aprobado

            // 💥 MEJORADO: Si es ROL 2 (Asesor), marcar como pendiente de aprobación
            // Los asesores SIEMPRE deben tener sus financiamientos pendientes de aprobación
            if ($rol_usuario === 2) {
                $registrar_movimiento = false; // No registrar movimiento hasta que se apruebe
                $aprobado = 0; // No aprobado para asesores - requiere aprobación del director
            }

            // 💥 Modificado: Solo actualizar stock si se debe registrar el movimiento
            // 🆕 NUEVO: NO descontar stock para CrediYango (grupo 45) ni para producto ID 37
            $esCrediYangoDesc = isset($datos['grupo_financiamiento']) &&
                ($datos['grupo_financiamiento'] == '45' || $datos['grupo_financiamiento'] == 45);
            $esProductoNoEntregado = $datos['id_producto'] == 37;

            if ($registrar_movimiento && !$esCrediYangoDesc && !$esProductoNoEntregado) {
                $productoModel->actualizarStock($datos['id_producto'], $datos['cantidad_producto']);
                error_log("✅ Stock actualizado para producto ID: " . $datos['id_producto']);
            } else if ($esCrediYangoDesc || $esProductoNoEntregado) {
                error_log("⏸️ Stock NO descontado - CrediYango o producto no entregado");
            }

            // Determinar el código del producto 🔹 Agregado
            $codigo_producto = $producto['CODIGO'] ?? $producto['CODIGO_BARRA'];

            // Determinar la razón social 🔹 Agregado
            $razon_social = $producto['RAZON_SOCIAL'] ?? null;

            // Determinar el nombre del producto 🔹 Agregado
            $nombre_producto = $producto['NOMBRE'];



            $montoTotal = floatval(str_replace(['S/. ', 'US$ ', '$'], '', $datos['monto_total']));

            // 💥 Modificado: Añadir aprobado y usuario_id a los datos
            $datos['aprobado'] = $aprobado;   

            // Siempre pasar usuario_id para todos los financiamientos
            $datos['usuario_id'] = $usuario_id;

            $financiamientoModel = new Financiamiento();
            $idFinanciamiento = $financiamientoModel->guardarFinanciamiento($datos);

            // Actualizar verificación domiciliaria si es financiamiento vehicular y hay valor
            if ($verificacion_domiciliaria !== null) {
                $this->actualizarVerificacionDomiciliaria($datos['id_conductor'], $datos['id_cliente'], $verificacion_domiciliaria);
            }

            // Después de obtener $idFinanciamiento
            $this->registrarComisionAutomatica($idFinanciamiento);

            $cuotas = $datos['cuotas'];
            $valorCuota = $datos['valorCuota'];

            // MODIFICADO: Solo generar cuotas si NO es CrediYango
            // Para CrediYango, las cuotas se generan cuando se entrega el vehículo
            if (!$esCrediYango && !empty($fechasVencimiento) && is_array($fechasVencimiento)) {
                // Iterar sobre las fechas de vencimiento y guardar cada cuota
                for ($i = 0; $i < count($fechasVencimiento); $i++) {
                    $cuotaModel = new CuotaFinanciamiento();
                    // Convertir la fecha de vencimiento a formato 'Y-m-d'
                    $fechaVencimiento = date('Y-m-d', strtotime($fechasVencimiento[$i]));

                    // ✅ MODIFICADO: Pasar la moneda a guardarCuota
                    $cuotaModel->guardarCuota(
                        $idFinanciamiento,
                        $i + 1,
                        $valorCuota,
                        $fechaVencimiento,
                        $datos['grupo_financiamiento'],
                        $datos['tipo_moneda']  // ✅ NUEVO: Pasar la moneda
                    );
                }
            } else if ($esCrediYango) {
                // Log para CrediYango
                error_log("CrediYango: Financiamiento guardado sin cuotas. ID: $idFinanciamiento. Las cuotas se generarán al entregar el vehículo.");
            }

            // 💥 Modificado: Solo registrar el movimiento si corresponde
            if ($registrar_movimiento) {
                // Registrar el movimiento en el almacén
                $reportesModel = new Reportes(); 
                $tipo_movimiento = "Salida";
                $subtipo_movimiento = "financiamiento";
                $cantidad_producto = $datos['cantidad_producto'];

                $reportesModel->registrarMovimiento(
                    $usuario_id, 
                    $datos['id_producto'], 
                    $codigo_producto, 
                    $nombre_producto, 
                    $tipo_movimiento, 
                    $subtipo_movimiento, 
                    $cantidad_producto, 
                    $razon_social
                );
            }


            $this->enviarRespuesta(true, 'Registro completado con éxito.', $idFinanciamiento);
        } catch (Exception $e) {
            $this->enviarRespuesta(false, 'Error: ' . $e->getMessage());
        }
    }

    // Método para buscar conductor
    public function buscarConductor() {
        header('Content-Type: application/json');
        // Recibimos el número de documento del front-end
        $nroDocumento = $_GET['nro_documento'];

        // Llamamos al modelo para buscar el conductor
        $conductorModel = new Conductor();
        $idConductor = $conductorModel->buscarPorDocumento($nroDocumento);

        // Verificamos si encontramos al conductor
        if ($idConductor) {
            echo json_encode(['success' => true, 'id_conductor' => $idConductor]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Conductor no encontrado']);
        }
    }

    private function enviarRespuesta($success, $message, $idFinanciamiento = null)
    {
        $response = [
            'success' => $success,
            'message' => $message
        ];

        if ($idFinanciamiento !== null) { // 🔹 Si hay un ID, lo agregamos a la respuesta
            $response['id_financiamiento'] = $idFinanciamiento;
        }

        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }


    function SaveFinanciamientoVehicular()
    {
        // 💥 Modificado: Obtenemos el rol del usuario desde la sesión
        $rol_usuario = isset($_SESSION['id_rol']) ? intval($_SESSION['id_rol']) : null;
        // 💥 Modificado: Obtenemos el ID del usuario desde la sesión
        $usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

        if (!$usuario_id) {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo obtener el ID del usuario.']);
            return;
        }

        // Recibir datos del POST
        $cliente = $_POST['cliente'];
        $idProducto = $_POST['idProducto'];
        $codigoAsociado = $_POST['codigoAsociado'];
        $grupo_financiamiento = $_POST['grupo_financiamiento'];

        // MODIFICADO: Validar fechas de vencimiento solo si NO es CrediYango (grupo 45)
        // CrediYango generará el cronograma cuando se entregue el vehículo
        $esCrediYango = ($grupo_financiamiento == '45' || $grupo_financiamiento == 45);

        if (!$esCrediYango && (!isset($_POST['fechasVencimiento']) || empty($_POST['fechasVencimiento']))) {
            echo json_encode(['status' => 'error', 'message' => 'Las fechas de vencimiento son obligatorias.']);
            return;
        }
        $monto_total = $_POST['monto_total'];
        $cuota_inicial = $_POST['cuota_inicial'];
        $cuotas = $_POST['cuotas'];
        $valor_cuota = $_POST['valor_cuota'];
        $estado = $_POST['estado'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];
        $fecha_creacion = $_POST['fecha_creacion'];
        $frecuencia_pago = $_POST['frecuencia_pago'];
        $second_product = $_POST['second_product'];
        $monto_inscrip = $_POST['monto_inscrip'];
        $moneda = $_POST['moneda'];
        $fechasVencimiento = $_POST['fechasVencimiento'];
        $monto_recalculado = $_POST['monto_recalculado'];
       
        $monto_sin_intereses = $_POST['monto_sin_intereses'];
        $tasa = isset($_POST['tasa']) && !empty($_POST['tasa']) ? $_POST['tasa'] : null; 
      
        // 🙂 Recibir el ID de la variante (puede ser null)
        $id_variante = (isset($_POST['id_variante']) && $_POST['id_variante'] !== '' && $_POST['id_variante'] != 0) ? intval($_POST['id_variante']) : null;
        // Recibir valor de cobrar mora
        $cobrar_mora = isset($_POST['cobrar_mora']) ? intval($_POST['cobrar_mora']) : 1;

        // Obtener valor de verificación domiciliaria
        $verificacion_domiciliaria = isset($_POST['verificacion_domiciliaria']) ? intval($_POST['verificacion_domiciliaria']) : null;

        // Recibir id_conductor e id_cliente del POST - MODIFICADO: Ahora recibimos ambos IDs
        $idConductor = isset($_POST['id_conductor']) ? (intval($_POST['id_conductor']) !== 0 ? intval($_POST['id_conductor']) : null) : null; // ✅ MODIFICADO: Si id_conductor es 0, lo convertimos en null
    
        $idCliente = (isset($_POST['id_cliente']) && $_POST['id_cliente'] !== '' && $_POST['id_cliente'] != 0) ? intval($_POST['id_cliente']) : null;

        // Validar el idProducto y establecer estado_entrega
        $cantidad_producto = ($idProducto === "No disponible") ? 0 : 1;
        $estado_entrega = null; // Por defecto, sin estado de entrega

        if ($idProducto === "No disponible") {
            $idProducto = 37;
            $estado_entrega = 'pendiente'; // 🆕 Establecer estado_entrega como pendiente cuando no se entrega vehículo
        }
    
        // 💥 MEJORADO: Determinar el valor de aprobado según el rol
        // Los asesores (rol 2) SIEMPRE requieren aprobación del director
        $aprobado = ($rol_usuario === 2) ? 0 : 1;
    
        /*
        // Instanciar modelo Conductor y obtener el id_conductor
        $conductorModel = new Conductor();
        $idConductor = $conductorModel->buscarPorDocumento($cliente);
        */        
        
        // Preparar datos para insertar en la base de datos
        $datos = [
            'id_conductor' => $idConductor, // MODIFICADO: Ahora usamos el id_conductor del POST (puede ser null)
            'id_cliente' => $idCliente, 
            'id_producto' => intval($idProducto),
            'id_coti' => 0,
            'codigo_asociado' => intval($codigoAsociado),
            'grupo_financiamiento' => strval($grupo_financiamiento),
            'id_variante' => $id_variante,
            'cantidad_producto' => strval($cantidad_producto),
            'monto_total' => floatval($monto_total),
            'cuota_inicial' => floatval($cuota_inicial),
            'cuotas' => intval($cuotas),
            'estado' => strval($estado),
            'fecha_inicio' => strval($fecha_inicio),
            'fecha_fin' => strval($fecha_fin),
            'fecha_creacion' => strval($fecha_creacion),
            'frecuencia' => strval($frecuencia_pago),
            'second_product' => strval($second_product ?: ""),
            'monto_inscrip' => floatval($monto_inscrip),
            'moneda' => strval($moneda),
            'monto_recalculado' => floatval($monto_recalculado),
            'monto_sin_interes' => floatval($monto_sin_intereses),
            'tasa' => $tasa !== null ? floatval($tasa) : null, 
            // 💥 Modificado: Agregar usuario_id al array de datos si el rol es 2
            // Siempre agregar usuario_id al array de datos
            'usuario_id' => $usuario_id,
            // 💥 Modificado: Agregar aprobado al array de datos
            'aprobado' => $aprobado,
            'cobrar_mora' => $cobrar_mora,
            'estado_entrega' => $estado_entrega, // 🆕 Agregar estado_entrega al array de datos
             'nombre_personalizado' => isset($_POST['nombre_personalizado']) && !empty($_POST['nombre_personalizado'])
        ? trim($_POST['nombre_personalizado'])
        : null
        ];
    
        // ⏩ Modificado: Comprobar si conexión está disponible
        if (!isset($this->conexion) || !$this->conexion) {
            echo "Error: No hay conexión a la base de datos disponible.";
            return;
        }
        
        $conexion = $this->conexion; // ⏩ Asegurar que la conexión esté disponible
        
        // 🙂 Modificar la consulta SQL para incluir id_variante y estado_entrega
        $query = "INSERT INTO financiamiento
        (id_conductor, id_cliente, idproductosv2, id_coti, codigo_asociado, grupo_financiamiento, id_variante, cantidad_producto,
        monto_total, cuota_inicial, cuotas, estado, fecha_inicio, fecha_fin, fecha_creacion,
        frecuencia, second_product, monto_inscrip, moneda, monto_recalculado, monto_sin_interes, tasa, usuario_id, aprobado, cobrar_mora, estado_entrega)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conexion->prepare($query);
    
        // ⏩ Añadido: Verificación de éxito en la preparación
        if ($stmt === false) {
            echo "Error en la preparación de la consulta: " . $conexion->error;
            return;
        }
    
        // ⭐ Modificado: Manejo adecuado de valores nulos
        $tipos = '';
        $params = [];
        
        // ⭐ Preparar los tipos y parámetros dinámicamente
        // Para cada valor, determinamos su tipo y lo añadimos a los arreglos
        
        // id_conductor (puede ser null)
        if ($datos['id_conductor'] === null) {
            $tipos .= 'i';
            $params[] = NULL;
        } else {
            $tipos .= 'i';
            $params[] = $datos['id_conductor'];
        }
        
        // id_cliente (puede ser null)
        if ($datos['id_cliente'] === null) {
            $tipos .= 'i';
            $params[] = NULL;
        } else {
            $tipos .= 'i';
            $params[] = $datos['id_cliente'];
        }
        
        // Resto de parámetros
        // id_producto
        $tipos .= 'i';
        $params[] = $datos['id_producto'];
        
        // id_coti
        $tipos .= 'i';
        $params[] = $datos['id_coti'];
        
        // codigo_asociado
        $tipos .= 's';
        $params[] = $datos['codigo_asociado'];
        
        // grupo_financiamiento
        $tipos .= 's';
        $params[] = $datos['grupo_financiamiento'];
        
        // id_variante (puede ser null)
        if ($datos['id_variante'] === null) {
            $tipos .= 'i';
            $params[] = NULL;
        } else {
            $tipos .= 'i';
            $params[] = $datos['id_variante'];
        }

        // cantidad_producto
        $tipos .= 's';
        $params[] = $datos['cantidad_producto'];
        
        // monto_total
        $tipos .= 'd';
        $params[] = $datos['monto_total'];
        
        // cuota_inicial
        $tipos .= 'd';
        $params[] = $datos['cuota_inicial'];
        
        // cuotas
        $tipos .= 'i';
        $params[] = $datos['cuotas'];
        
        // estado
        $tipos .= 's';
        $params[] = $datos['estado'];
        
        // fecha_inicio
        $tipos .= 's';
        $params[] = $datos['fecha_inicio'];
        
        // fecha_fin
        $tipos .= 's';
        $params[] = $datos['fecha_fin'];
        
        // fecha_creacion
        $tipos .= 's';
        $params[] = $datos['fecha_creacion'];
        
        // frecuencia
        $tipos .= 's';
        $params[] = $datos['frecuencia'];
        
        // second_product
        $tipos .= 's';
        $params[] = $datos['second_product'];
        
        // monto_inscrip
        $tipos .= 'd';
        $params[] = $datos['monto_inscrip'];
        
        // moneda
        $tipos .= 's';
        $params[] = $datos['moneda'];
        
        // monto_recalculado
        $tipos .= 'd';
        $params[] = $datos['monto_recalculado'];
        
        // monto_sin_interes
        $tipos .= 'd';
        $params[] = $datos['monto_sin_interes'];
        
        // tasa (puede ser null)
        if ($datos['tasa'] === null) {
            $tipos .= 'd';
            $params[] = NULL;
        } else {
            $tipos .= 'd';
            $params[] = $datos['tasa'];
        }
        
        // usuario_id (puede ser null)
        if ($datos['usuario_id'] === null) {
            $tipos .= 'i';
            $params[] = NULL;
        } else {
            $tipos .= 'i';
            $params[] = $datos['usuario_id'];
        }
        
        // aprobado (puede ser null)
        if ($datos['aprobado'] === null) {
            $tipos .= 'i';
            $params[] = NULL;
        } else {
            $tipos .= 'i';
            $params[] = $datos['aprobado'];
        }

        // cobrar_mora
        $tipos .= 'i';
        $params[] = $datos['cobrar_mora'];

        // estado_entrega (puede ser null) - 🆕 NUEVO
        // Validar que solo sean valores del ENUM o NULL
        $estadoEntregaValue = $datos['estado_entrega'];
        if ($estadoEntregaValue === '' || $estadoEntregaValue === null) {
            $tipos .= 's';
            $params[] = NULL;
        } else if (in_array($estadoEntregaValue, ['pendiente', 'entregado'])) {
            $tipos .= 's';
            $params[] = $estadoEntregaValue;
        } else {
            // Valor inválido, usar NULL
            error_log("⚠️ WARNING: estado_entrega inválido: '" . $estadoEntregaValue . "'. Usando NULL");
            $tipos .= 's';
            $params[] = NULL;
        }

        // ⭐ Modificado: Vinculación dinámica de parámetros
        $stmt->bind_param($tipos, ...$params);
    
        // AÑADIDO: Validación para asegurar que al menos uno de los IDs esté presente
        if ($idConductor === null && $idCliente === null) {
            echo json_encode(['status' => 'error', 'message' => 'Se requiere al menos un id_conductor o un id_cliente.']);
            return;
        }
    
        // Ejecutar el INSERT y obtener el id generado
        if ($stmt->execute()) {
            $idFinanciamiento = $conexion->insert_id;
          
        } else {
            echo "Error al registrar financiamiento: " . $stmt->error;
            return;
        }
    
        // Actualizar verificación domiciliaria si hay valor
        if ($verificacion_domiciliaria !== null) {
            $this->actualizarVerificacionDomiciliaria($idConductor, $idCliente, $verificacion_domiciliaria);
        }

        $stmt->close();
    
        // Después de obtener $idFinanciamiento
        $this->registrarComisionAutomatica($idFinanciamiento);

        $numeroCuotaInicial = isset($_POST['numeroCuotaInicial']) ? (int)$_POST['numeroCuotaInicial'] : 1; 
       
        // Guardar cuotas de financiamiento
        for ($i = 0; $i < count($fechasVencimiento); $i++) {
            $cuotaModel = new CuotaFinanciamiento();
            $fechaVencimiento = date('Y-m-d', strtotime($fechasVencimiento[$i]));
            $numeroCuota = $numeroCuotaInicial + $i;
            
            // ✅ MODIFICADO: Pasar la moneda a guardarCuota
            $cuotaModel->guardarCuota(
                $idFinanciamiento, 
                $numeroCuota, 
                $valor_cuota, 
                $fechaVencimiento, 
                $grupo_financiamiento,
                $moneda  // ✅ NUEVO: Pasar la moneda
            );
        }
    
        // Registrar movimiento en el almacén solo si hay un id_producto válido
        // 🆕 MODIFICADO: Agregar verificación para CrediYango (no descontar stock)
        $esCrediYango = isset($datos['grupo_financiamiento']) &&
            ($datos['grupo_financiamiento'] == '45' || $datos['grupo_financiamiento'] == 45);

        if ($idProducto !== 37 && $rol_usuario !== 2 && !$esCrediYango) {

            // Instanciar el modelo ProductoV2 para actualizar el stock
            $productoModel = new ProductoV2(); // 🔹 Agregué esta línea para instanciar el modelo ProductoV2
            $productoModel->actualizarStock($datos['id_producto'], $datos['cantidad_producto']); // 🔹 Llamo al método para actualizar el stock del producto
            error_log("✅ Stock actualizado para producto ID: " . $datos['id_producto']);
    
            // Obtener los datos del producto para registrar el movimiento 🔹 Agregado
            $producto = $productoModel->obtenerProductoPorId($datos['id_producto']); 
            if (!$producto) {
                throw new Exception("No se encontró el producto con ID: " . $datos['id_producto']);
            }
    
            // Determinar el código del producto 🔹 Agregado
            $codigo_producto = $producto['CODIGO'] ?? $producto['CODIGO_BARRA'];
    
            // Determinar la razón social 🔹 Agregado
            $razon_social = $producto['RAZON_SOCIAL'] ?? null;
    
            // Determinar el nombre del producto 🔹 Agregado
            $nombre_producto = $producto['NOMBRE'];
    
            $reportesModel = new Reportes();
            $tipo_movimiento = "Salida";
            $subtipo_movimiento = "financiamiento";
    
            $reportesModel->registrarMovimiento(
                $usuario_id, // Este valor debe estar definido en el controlador
                $idProducto,
                $codigo_producto, // Suponiendo que es el código del producto
                $nombre_producto, // Usando el nombre del grupo como nombre del producto
                $tipo_movimiento,
                $subtipo_movimiento,
                $cantidad_producto,
                $razon_social // Suponiendo que este valor también está definido
            );
        } else if ($esCrediYango || $idProducto === 37) {
            // 🆕 NUEVO: Log cuando NO se descuenta stock
            error_log("⏸️ Stock NO descontado - CrediYango (grupo 45) o producto no entregado (ID 37)");
        }

        echo json_encode(['status' => 'success', 'idFinanciamiento' => $idFinanciamiento]); // ← MODIFICADO: Agregado 'status' para consistencia
        exit; // ← AGREGADO: Asegurar que no haya más output después de enviar la respuesta JSON
    }

    public function buscarClienteExiste()
    {
        // Obtener el documento desde POST
        $dni = $_POST['dni'] ?? '';
        
        if (empty($dni)) {
            echo json_encode(['success' => false, 'message' => 'Documento no proporcionado']);
            return;
        }
        
        $conexion = (new Conexion())->getConexion();
        $sql = "SELECT id_cliente, datos, email, telefono, direccion FROM clientes WHERE documento = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $dni);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado && $resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            echo json_encode([
                'success' => true, 
                'existe' => true, 
                'id_cliente' => $fila['id_cliente'],
                'datos' => $fila['datos'],
                'email' => $fila['email'],
                'telefono' => $fila['telefono'],
                'direccion' => $fila['direccion']
            ]);
        } else {
            echo json_encode(['success' => true, 'existe' => false]);
        }
    }

    public function buscarOCrearCliente()
    {
        header('Content-Type: application/json');

        try {
            // Obtener datos del formulario
            $documento = $_POST['documento'] ?? '';
                      

            // Validar datos obligatorios
            if (empty($documento)) {                                     // MODIFICADO: Solo validamos documento como obligatorio
                throw new Exception("El número de documento es obligatorio");
            }
            
            // Instanciar el modelo Cliente
            $clienteModel = new Cliente();
            
            // Buscar si el cliente existe en clientes_financiar                  
            $clienteExistente = $clienteModel->buscarClienteFinanciar($documento); // MODIFICADO: Llamada al nuevo método
            
            if ($clienteExistente) {
                // Si existe, retornar su ID
                echo json_encode(['success' => true, 'id_cliente' => $clienteExistente['id'], 'message' => 'Cliente existente']); // MODIFICADO: Cambiado 'id_cliente' a 'id' según estructura de la tabla
                return;
            } 

            // Si no existe, retornar error                                       // MODIFICADO: Ya no crea cliente automáticamente
            throw new Exception("El cliente no está registrado en el sistema");

            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }

        private function registrarComisionAutomatica($idFinanciamiento) {
            // Verificar si el usuario puede tener comisión automática

            if (!in_array($_SESSION['id_rol'], [1,2, 3])) {
                return; // Solo roles 1 y 3 tienen comisión automática
            }
            
            // Obtener el financiamiento recién registrado
            $financiamientoModel = new Financiamiento();
            $financiamiento = $financiamientoModel->getFinanciamientoById($idFinanciamiento);

            
            if ($financiamiento) {
                // REUTILIZAR la misma lógica de comisiones que ya existe
                $financiamientoController = new FinanciamientoController();
                $financiamientoController->registrarComisionFinanciamiento($financiamiento);
            }
        }

        private function actualizarVerificacionDomiciliaria($idConductor, $idCliente, $verificacionDomiciliaria)
        {
            try {
                $actualizado = false;
                
                // Intentar actualizar conductor si el ID es válido
                if (!empty($idConductor) && $idConductor > 0) {
                    $query = "UPDATE conductores SET verificacion_domiciliaria = ? WHERE id_conductor = ?";
                    $stmt = $this->conexion->prepare($query);
                    $stmt->bind_param('ii', $verificacionDomiciliaria, $idConductor);
                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        $actualizado = true;
                    }
                    $stmt->close();
                }
                
                // Intentar actualizar cliente si el ID es válido Y no se actualizó conductor
                if (!$actualizado && !empty($idCliente) && $idCliente > 0) {
                    $query = "UPDATE clientes_financiar SET verificacion_domiciliaria = ? WHERE id = ?";
                    $stmt = $this->conexion->prepare($query);
                    $stmt->bind_param('ii', $verificacionDomiciliaria, $idCliente);
                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        $actualizado = true;
                    }
                    $stmt->close();
                }
                
            } catch (Exception $e) {
                error_log("Error al actualizar verificación domiciliaria: " . $e->getMessage());
            }
        }   
        
}

