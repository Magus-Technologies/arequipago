<?php
require_once 'app/models/Beneficio.php';

class BeneficioController
{
    private $request;
    private $beneficioModel;
    

    public function __construct()
    {
        $this->beneficioModel = new Beneficio();
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Obtener todos los beneficios con filtros opcionales
     * Si recibe parámetros idUsuario y tipo en la ruta, filtra por departamento del usuario
     * Rutas:
     * - GET /ajs/beneficios/listar (todos los beneficios)
     * - GET /ajs/beneficios/listar/:idUsuario/:tipo (filtrados por departamento del usuario)
     */
    public function listarBeneficios($idUsuario = null, $tipo = null)
    {
        try {
            $filtros = [];

            // Si vienen idUsuario y tipo, filtrar por departamento del usuario
            if ($idUsuario && $tipo) {
                // Validar tipo
                $tipo = strtolower($tipo);
                if (!in_array($tipo, ['conductor', 'cliente'])) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'error' => 'Tipo inválido. Debe ser "conductor" o "cliente"'
                    ]);
                    return;
                }

                // Obtener departamento del usuario
                $departamentoId = null;

                if ($tipo === 'conductor') {
                    // Buscar departamento en direccion_conductor
                    $sql = "SELECT departamento FROM direccion_conductor WHERE id_conductor = ?";
                    $stmt = $this->beneficioModel->getConexion()->prepare($sql);
                    $stmt->bind_param('i', $idUsuario);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($row = $result->fetch_assoc()) {
                        $departamentoId = $row['departamento'];
                    }
                    $stmt->close();
                } else if ($tipo === 'cliente') {
                    // Buscar departamento en clientes_financiar
                    $sql = "SELECT departamento FROM clientes_financiar WHERE id = ?";
                    $stmt = $this->beneficioModel->getConexion()->prepare($sql);
                    $stmt->bind_param('i', $idUsuario);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($row = $result->fetch_assoc()) {
                        $departamentoId = $row['departamento'];
                    }
                    $stmt->close();
                }

                // Si no se encontró el usuario o no tiene departamento
                if (!$departamentoId) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'error' => 'Usuario no encontrado o sin departamento asignado'
                    ]);
                    return;
                }

                // Aplicar filtro de departamento y disponibilidad
                $filtros['disponible'] = 1;
                $filtros['departamento_id'] = $departamentoId;
            }
            
            // Filtros desde GET o POST (aplican siempre)
            if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
                $filtros['categoria'] = $_GET['categoria'];
            }
            if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
                $filtros['busqueda'] = $_GET['busqueda'];
            }
            if (isset($_GET['disponible']) && $_GET['disponible'] !== '') {
                $filtros['disponible'] = (int)$_GET['disponible'];
            }
            if (isset($_GET['departamento_id'])) {
                $filtros['departamento_id'] = $_GET['departamento_id'];
            }

            // También desde POST para compatibilidad con AJAX
            if (isset($_POST['categoria']) && !empty($_POST['categoria'])) {
                $filtros['categoria'] = $_POST['categoria'];
            }
            if (isset($_POST['busqueda']) && !empty($_POST['busqueda'])) {
                $filtros['busqueda'] = $_POST['busqueda'];
            }
            if (isset($_POST['disponible']) && $_POST['disponible'] !== '') {
                $filtros['disponible'] = (int)$_POST['disponible'];
            }
            if (isset($_POST['departamento_id'])) {
                $filtros['departamento_id'] = $_POST['departamento_id'];
            }

            $beneficios = $this->beneficioModel->obtenerTodos($filtros);

            // Preparar respuesta
            $response = [
                'success' => true,
                'data' => $beneficios,
                'total' => count($beneficios)
            ];

            // Si se filtró por usuario, agregar información adicional
            if ($idUsuario && $tipo && isset($departamentoId)) {
                // Obtener nombre del departamento
                $departamentoNombre = null;
                $sqlDep = "SELECT nombre FROM depast WHERE iddepast = ?";
                $stmtDep = $this->beneficioModel->getConexion()->prepare($sqlDep);
                $stmtDep->bind_param('i', $departamentoId);
                $stmtDep->execute();
                $resultDep = $stmtDep->get_result();
                
                if ($rowDep = $resultDep->fetch_assoc()) {
                    $departamentoNombre = $rowDep['nombre'];
                }
                $stmtDep->close();

                $response['departamento_id'] = $departamentoId;
                $response['departamento_nombre'] = $departamentoNombre;
                $response['tipo_usuario'] = $tipo;
                $response['id_usuario'] = $idUsuario;
            }

            header('Content-Type: application/json');
            echo json_encode($response);
        } catch (Exception $e) {
            error_log('Error en listarBeneficios: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener beneficios: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Crear un nuevo beneficio
     */
    public function crearBeneficio()
    {
        try {
            // Validaciones básicas
            if (empty($_POST['nombre'])) {
                echo json_encode(['success' => false, 'message' => 'El nombre del producto es obligatorio.']);
                return;
            }

            if (empty($_POST['plan_financiamiento_id'])) {
                echo json_encode(['success' => false, 'message' => 'El plan de financiamiento es obligatorio.']);
                return;
            }

            // Cuota inicial es opcional, validar solo si está presente que no sea negativa
            if (isset($_POST['cuota_inicial']) && $_POST['cuota_inicial'] !== '' && $_POST['cuota_inicial'] < 0) {
                echo json_encode(['success' => false, 'message' => 'La cuota inicial no puede ser negativa.']);
                return;
            }

            if (!isset($_POST['cantidad_cuotas']) || $_POST['cantidad_cuotas'] === '' || $_POST['cantidad_cuotas'] <= 0) {
                echo json_encode(['success' => false, 'message' => 'La cantidad de cuotas es obligatoria y debe ser mayor a 0.']);
                return;
            }

            if (!isset($_POST['cuota_mensual']) || $_POST['cuota_mensual'] === '' || $_POST['cuota_mensual'] <= 0) {
                echo json_encode(['success' => false, 'message' => 'La cuota mensual es obligatoria y debe ser mayor a 0.']);
                return;
            }

          // Validar plan de financiamiento contra la base de datos
if (!$this->validarPlanFinanciamientoExiste($_POST['plan_financiamiento_id'])) {
    echo json_encode(['success' => false, 'message' => 'Plan de financiamiento no válido.']);
    return;
}

// Validar campos adicionales si es el plan EDITABLE (ID 42)
if ($_POST['plan_financiamiento_id'] == 42) {
    if (empty($_POST['moneda'])) {
        echo json_encode(['success' => false, 'message' => 'La moneda es obligatoria para el plan editable.']);
        return;
    }
    if (empty($_POST['frecuencia_pago'])) {
        echo json_encode(['success' => false, 'message' => 'La frecuencia de pago es obligatoria para el plan editable.']);
        return;
    }
    // Campo nombre_plan_personalizado comentado - solo se usa Nombre del Producto
    /*
    if (empty($_POST['nombre_plan_personalizado'])) {
        echo json_encode(['success' => false, 'message' => 'El nombre del plan es obligatorio para el plan editable.']);
        return;
    }
    */
}

// Validar categoría contra la base de datos (opcional)
if (!empty($_POST['categoria']) && !$this->validarCategoriaExiste($_POST['categoria'])) {
    echo json_encode(['success' => false, 'message' => 'Categoría no válida.']);
    return;
}


            // Validar código de producto único (si se proporciona)
            if (!empty($_POST['codigo_producto'])) {
                if ($this->beneficioModel->existeCodigoProducto($_POST['codigo_producto'])) {
                    echo json_encode(['success' => false, 'message' => 'El código de producto ya existe.']);
                    return;
                }
            }

            // Procesar imagen principal
            $imagenPrincipal = null;
            if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
                $imagenPrincipal = $this->procesarImagenUpload($_FILES['imagen_principal']);
                if (!$imagenPrincipal) {
                    echo json_encode(['success' => false, 'message' => 'Error al procesar la imagen.']);
                    return;
                }
            }

            // Debug: Log de datos POST recibidos
            error_log('BeneficioController::crearBeneficio() - POST data: ' . print_r($_POST, true));
            error_log('BeneficioController::crearBeneficio() - FILES data: ' . print_r($_FILES, true));
            error_log('BeneficioController::crearBeneficio() - imagenPrincipal: ' . ($imagenPrincipal ?? 'NULL'));

            // Preparar datos esenciales
            $datos = [
                'nombre' => trim($_POST['nombre']),
                'plan_financiamiento_id' => (int)$_POST['plan_financiamiento_id'],
                'categoria' => !empty($_POST['categoria']) ? (int)$_POST['categoria'] : null,
                'descripcion' => isset($_POST['descripcion']) && !empty($_POST['descripcion']) ? trim($_POST['descripcion']) : '',
                'cuota_inicial' => !empty($_POST['cuota_inicial']) ? (float)$_POST['cuota_inicial'] : 0,
                'cantidad_cuotas' => (int)$_POST['cantidad_cuotas'],
                'cuota_mensual' => (float)$_POST['cuota_mensual'],
                'pago_inscripcion' => !empty($_POST['pago_inscripcion']) ? (float)$_POST['pago_inscripcion'] : null,
                'moneda' => !empty($_POST['moneda']) ? trim($_POST['moneda']) : null,
                'nombre_plan_personalizado' => !empty($_POST['nombre_plan_personalizado']) ? trim($_POST['nombre_plan_personalizado']) : null,
                'frecuencia_pago' => !empty($_POST['frecuencia_pago']) ? trim($_POST['frecuencia_pago']) : null,
                'departamento_id' => !empty($_POST['departamento_id']) ? (int)$_POST['departamento_id'] : null,
                'imagen' => $imagenPrincipal,
                'disponible' => isset($_POST['disponible']) ? (int)$_POST['disponible'] : 1,
            ];

            $idBeneficio = $this->beneficioModel->crear($datos);

            if ($idBeneficio) {
                // ✅ NUEVO: Guardar departamentos seleccionados
                $departamentosSeleccionados = isset($_POST['departamentos']) && is_array($_POST['departamentos'])
                    ? $_POST['departamentos']
                    : [];

                if (!empty($departamentosSeleccionados)) {
                    $this->beneficioModel->guardarDepartamentosBeneficio($idBeneficio, $departamentosSeleccionados);
                }

                $beneficioCreado = $this->beneficioModel->obtenerPorId($idBeneficio);

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Beneficio creado exitosamente.',
                    'data' => $beneficioCreado
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al crear el beneficio.'
                ]);
            }
        } catch (Exception $e) {
            error_log('Error en BeneficioController::crearBeneficio(): ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener un beneficio por ID
     */
    public function obtenerBeneficio()
    {
        try {
            $id = $_POST['id'] ?? $_GET['id'] ?? null;

            if (!$id || !is_numeric($id)) {
                echo json_encode(['success' => false, 'message' => 'ID de beneficio no válido.']);
                return;
            }

            $beneficio = $this->beneficioModel->obtenerPorId((int)$id);

            if ($beneficio) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'data' => $beneficio
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Beneficio no encontrado.'
                ]);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener beneficio: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Actualizar un beneficio existente
     */
    public function actualizarBeneficio()
    {
        try {
            $id = $_POST['id'] ?? null;

            if (!$id || !is_numeric($id)) {
                echo json_encode(['success' => false, 'message' => 'ID de beneficio no válido.']);
                return;
            }

            // Verificar que el beneficio existe
            $beneficioExistente = $this->beneficioModel->obtenerPorId((int)$id);
            if (!$beneficioExistente) {
                echo json_encode(['success' => false, 'message' => 'Beneficio no encontrado.']);
                return;
            }

            // Validaciones básicas
            if (empty($_POST['nombre'])) {
                echo json_encode(['success' => false, 'message' => 'El nombre del producto es obligatorio.']);
                return;
            }

            if (empty($_POST['plan_financiamiento_id'])) {
                echo json_encode(['success' => false, 'message' => 'El plan de financiamiento es obligatorio.']);
                return;
            }

            // Cuota inicial es opcional, validar solo si está presente que no sea negativa
            if (isset($_POST['cuota_inicial']) && $_POST['cuota_inicial'] !== '' && $_POST['cuota_inicial'] < 0) {
                echo json_encode(['success' => false, 'message' => 'La cuota inicial no puede ser negativa.']);
                return;
            }

            if (!isset($_POST['cantidad_cuotas']) || $_POST['cantidad_cuotas'] === '' || $_POST['cantidad_cuotas'] <= 0) {
                echo json_encode(['success' => false, 'message' => 'La cantidad de cuotas es obligatoria y debe ser mayor a 0.']);
                return;
            }

            if (!isset($_POST['cuota_mensual']) || $_POST['cuota_mensual'] === '' || $_POST['cuota_mensual'] <= 0) {
                echo json_encode(['success' => false, 'message' => 'La cuota mensual es obligatoria y debe ser mayor a 0.']);
                return;
            }

            // Validar plan de financiamiento contra la base de datos
            if (!$this->validarPlanFinanciamientoExiste($_POST['plan_financiamiento_id'])) {
                echo json_encode(['success' => false, 'message' => 'Plan de financiamiento no válido.']);
                return;
            }

            // Validar campos adicionales si es el plan EDITABLE (ID 42)
            if ($_POST['plan_financiamiento_id'] == 42) {
                if (empty($_POST['moneda'])) {
                    echo json_encode(['success' => false, 'message' => 'La moneda es obligatoria para el plan editable.']);
                    return;
                }
                if (empty($_POST['frecuencia_pago'])) {
                    echo json_encode(['success' => false, 'message' => 'La frecuencia de pago es obligatoria para el plan editable.']);
                    return;
                }
                // Campo nombre_plan_personalizado comentado - solo se usa Nombre del Producto
                /*
                if (empty($_POST['nombre_plan_personalizado'])) {
                    echo json_encode(['success' => false, 'message' => 'El nombre del plan es obligatorio para el plan editable.']);
                    return;
                }
                */
            }

            // Validar categoría contra la base de datos (opcional)
            if (!empty($_POST['categoria']) && !$this->validarCategoriaExiste($_POST['categoria'])) {
                echo json_encode(['success' => false, 'message' => 'Categoría no válida.']);
                return;
            }

            // Validar código de producto único (si se proporciona y es diferente al actual)
            if (!empty($_POST['codigo_producto']) && $_POST['codigo_producto'] !== $beneficioExistente['codigo_producto']) {
                if ($this->beneficioModel->existeCodigoProducto($_POST['codigo_producto'], (int)$id)) {
                    echo json_encode(['success' => false, 'message' => 'El código de producto ya existe.']);
                    return;
                }
            }

            // Procesar imagen principal (si se sube una nueva)
            $imagenPrincipal = $beneficioExistente['imagen']; // Mantener la actual por defecto
            if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
                $nuevaImagen = $this->procesarImagenUpload($_FILES['imagen_principal']);
                if ($nuevaImagen) {
                    // Eliminar imagen anterior si existe
                    if ($imagenPrincipal && file_exists($_SERVER['DOCUMENT_ROOT'] . '/arequipago/public/' . $imagenPrincipal)) {
                        unlink($_SERVER['DOCUMENT_ROOT'] . '/arequipago/public/' . $imagenPrincipal);
                        error_log("Imagen anterior eliminada: " . $imagenPrincipal);
                    }
                    $imagenPrincipal = $nuevaImagen;
                }
            }

            // Preparar datos esenciales
            $datos = [
                'nombre' => trim($_POST['nombre']),
                'plan_financiamiento_id' => (int)$_POST['plan_financiamiento_id'],
                'categoria' => (int)$_POST['categoria'],
                'descripcion' => isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '',
                'cuota_inicial' => !empty($_POST['cuota_inicial']) ? (float)$_POST['cuota_inicial'] : 0,
                'cantidad_cuotas' => (int)$_POST['cantidad_cuotas'],
                'cuota_mensual' => (float)$_POST['cuota_mensual'],
                'pago_inscripcion' => !empty($_POST['pago_inscripcion']) ? (float)$_POST['pago_inscripcion'] : null,
                'moneda' => !empty($_POST['moneda']) ? trim($_POST['moneda']) : null,
                'nombre_plan_personalizado' => !empty($_POST['nombre_plan_personalizado']) ? trim($_POST['nombre_plan_personalizado']) : null,
                'frecuencia_pago' => !empty($_POST['frecuencia_pago']) ? trim($_POST['frecuencia_pago']) : null,
                'departamento_id' => !empty($_POST['departamento_id']) ? (int)$_POST['departamento_id'] : null,
                'imagen' => $imagenPrincipal,
                'disponible' => isset($_POST['disponible']) ? (int)$_POST['disponible'] : 1,
            ];

            $actualizado = $this->beneficioModel->actualizar((int)$id, $datos);

            if ($actualizado) {
                // ✅ NUEVO: Actualizar departamentos seleccionados
                $departamentosSeleccionados = isset($_POST['departamentos']) && is_array($_POST['departamentos'])
                    ? $_POST['departamentos']
                    : [];

                if (!empty($departamentosSeleccionados)) {
                    $this->beneficioModel->guardarDepartamentosBeneficio((int)$id, $departamentosSeleccionados);
                }

                $beneficioActualizado = $this->beneficioModel->obtenerPorId((int)$id);

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Beneficio actualizado exitosamente.',
                    'data' => $beneficioActualizado
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se realizaron cambios o error al actualizar.'
                ]);
            }
        } catch (Exception $e) {
            error_log('Error en BeneficioController::actualizarBeneficio(): ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Eliminar un beneficio (soft delete)
     */
    public function eliminarBeneficio()
    {
        try {
            $id = $_POST['id'] ?? null;

            if (!$id || !is_numeric($id)) {
                echo json_encode(['success' => false, 'message' => 'ID de beneficio no válido.']);
                return;
            }

            // Obtener la información del beneficio antes de eliminarlo para conseguir la imagen
            $beneficio = $this->beneficioModel->obtenerPorId((int)$id);

            if (!$beneficio) {
                echo json_encode(['success' => false, 'message' => 'Beneficio no encontrado.']);
                return;
            }

            $eliminado = $this->beneficioModel->eliminar((int)$id);

            if ($eliminado) {
                // Si el beneficio fue eliminado exitosamente, eliminar también la imagen del servidor
                if (!empty($beneficio['imagen'])) {
                    $rutaImagen = $_SERVER['DOCUMENT_ROOT'] . '/arequipago/public/' . $beneficio['imagen'];
                    if (file_exists($rutaImagen)) {
                        unlink($rutaImagen);
                        error_log("Imagen eliminada: " . $rutaImagen);
                    }
                }

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Beneficio e imagen eliminados exitosamente.'
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al eliminar el beneficio o beneficio no encontrado.'
                ]);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Error al eliminar beneficio: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Cambiar disponibilidad de un beneficio
     */
    public function cambiarDisponibilidad()
    {
        try {
            $id = $_POST['id'] ?? null;
            $disponible = $_POST['disponible'] ?? null;

            if (!$id || !is_numeric($id)) {
                echo json_encode(['success' => false, 'message' => 'ID de beneficio no válido.']);
                return;
            }

            if ($disponible === null || !in_array($disponible, ['0', '1', 0, 1])) {
                echo json_encode(['success' => false, 'message' => 'Estado de disponibilidad no válido.']);
                return;
            }

            $cambiado = $this->beneficioModel->cambiarDisponibilidad((int)$id, (int)$disponible);

            if ($cambiado) {
                $mensaje = $disponible ? 'Beneficio marcado como disponible.' : 'Beneficio marcado como no disponible.';
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => $mensaje
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al cambiar disponibilidad o beneficio no encontrado.'
                ]);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Error al cambiar disponibilidad: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Buscar beneficios
     */
    public function buscarBeneficios()
    {
        try {
            $termino = $_POST['termino'] ?? $_GET['q'] ?? '';
            $categoria = $_POST['categoria'] ?? $_GET['categoria'] ?? null;

            $beneficios = $this->beneficioModel->buscar($termino, $categoria);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $beneficios,
                'total' => count($beneficios)
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Error al buscar beneficios: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas generales
     */
    public function obtenerEstadisticas()
    {
        try {
            $estadisticas = $this->beneficioModel->obtenerEstadisticas();

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $estadisticas
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener categorías disponibles
     */
    public function obtenerCategorias()
    {
        try {
            $categorias = $this->beneficioModel->obtenerCategorias();

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $categorias
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener categorías: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Procesar upload de imagen
     */
    private function procesarImagenUpload($archivo)
    {
        try {
            $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $tamañoMaximo = 2 * 1024 * 1024; // 2MB

            // Validar tamaño
            if ($archivo['size'] > $tamañoMaximo) {
                return false;
            }

            // Validar extensión
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, $extensionesPermitidas)) {
                return false;
            }

            // Generar nombre único
            $nombreArchivo = 'beneficio_' . time() . '_' . uniqid() . '.' . $extension;
            $directorioDestino = 'public/img/beneficios/';
            
            // Crear directorio si no existe
            if (!is_dir($directorioDestino)) {
                mkdir($directorioDestino, 0755, true);
            }

            $rutaCompleta = $directorioDestino . $nombreArchivo;

            // Mover archivo
            if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
                return 'img/beneficios/' . $nombreArchivo; // Ruta relativa para BD
            }

            return false;
        } catch (Exception $e) {
            error_log('Error en procesarImagenUpload: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * FUNCIONALIDAD ELIMINADA: Los beneficios son automáticos para todos los usuarios
     * No requieren solicitud según especificaciones del cliente (hola.md)
     */
    /*
    public function solicitarBeneficio()
    {
        try {
            // OBSOLETO: Los beneficios son automáticos para todos los usuarios
            // No requieren proceso de solicitud

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Los beneficios están disponibles automáticamente en la app móvil.',
                'info' => 'No se requiere solicitud para acceder a los beneficios.'
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    */
    /**
 * Validar que una categoría existe en la base de datos
 */
private function validarCategoriaExiste($idCategoria)
{
    try {
        $sql = "SELECT COUNT(*) as total FROM categoria_producto WHERE idcategoria_producto = ?";
        $stmt = $this->beneficioModel->getConexion()->prepare($sql);
        $stmt->bind_param('i', $idCategoria);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row['total'] > 0;
    } catch (Exception $e) {
        error_log('Error al validar categoría: ' . $e->getMessage());
        return false;
    }
}

/**
 * Obtener categorías desde categoria_producto
 */
public function obtenerCategoriasProducto()
{
    try {
        $sql = "SELECT idcategoria_producto, nombre FROM categoria_producto ORDER BY nombre";
      $stmt = $this->beneficioModel->getConexion()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        $categorias = [];
        while ($row = $result->fetch_assoc()) {
            $categorias[] = $row;
        }
        $stmt->close();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $categorias
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Error al obtener categorías: ' . $e->getMessage()
        ]);
    }
}

/**
 * Validar que un plan de financiamiento existe en la base de datos
 */
private function validarPlanFinanciamientoExiste($idPlan)
{
    try {
        $sql = "SELECT COUNT(*) as total FROM planes_financiamiento WHERE idplan_financiamiento = ?";
        $stmt = $this->beneficioModel->getConexion()->prepare($sql);
        $stmt->bind_param('i', $idPlan);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['total'] > 0;
    } catch (Exception $e) {
        error_log('Error al validar plan de financiamiento: ' . $e->getMessage());
        return false;
    }
}

/**
 * Obtener departamentos habilitados para el formulario de beneficios
 */
public function obtenerDepartamentosHabilitados()
{
    try {
        $departamentos = $this->beneficioModel->obtenerDepartamentosHabilitados();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $departamentos
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Error al obtener departamentos: ' . $e->getMessage()
        ]);
    }
}

}