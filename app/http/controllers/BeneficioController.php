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
     */
    public function listarBeneficios()
    {
        try {
            $filtros = [];
            
            // Filtros desde GET o POST
            if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
                $filtros['categoria'] = $_GET['categoria'];
            }
            if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
                $filtros['busqueda'] = $_GET['busqueda'];
            }
            if (isset($_GET['disponible']) && $_GET['disponible'] !== '') {
                $filtros['disponible'] = (int)$_GET['disponible'];
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

            $beneficios = $this->beneficioModel->obtenerTodos($filtros);

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

            if (empty($_POST['categoria'])) {
                echo json_encode(['success' => false, 'message' => 'La categoría es obligatoria.']);
                return;
            }

            if (empty($_POST['precio_contado']) || $_POST['precio_contado'] <= 0) {
                echo json_encode(['success' => false, 'message' => 'El precio al contado es obligatorio y debe ser mayor a 0.']);
                return;
            }

          // Validar categoría contra la base de datos
if (!$this->validarCategoriaExiste($_POST['categoria'])) {
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

            // Debug: Log de datos POST recibidos (comentado tras resolver el problema)
            // error_log('BeneficioController::crearBeneficio() - POST data: ' . print_r($_POST, true));

            // Preparar datos
            $datos = [
                'nombre' => trim($_POST['nombre']),
                'categoria' => (int)$_POST['categoria'],
                'descripcion' => isset($_POST['descripcion']) && !empty($_POST['descripcion']) ? trim($_POST['descripcion']) : null,
                'precio_contado' => (float)$_POST['precio_contado'],
                'precio_financiado' => isset($_POST['precio_financiado']) && !empty($_POST['precio_financiado']) && $_POST['precio_financiado'] > 0 ? (float)$_POST['precio_financiado'] : null,
                'cuotas_disponibles' => isset($_POST['cuotas_disponibles']) && !empty($_POST['cuotas_disponibles']) ? trim($_POST['cuotas_disponibles']) : null,
                'tasa_interes' => isset($_POST['tasa_interes']) && !empty($_POST['tasa_interes']) && $_POST['tasa_interes'] > 0 ? (float)$_POST['tasa_interes'] : null,
                'requisitos' => isset($_POST['requisitos']) && !empty($_POST['requisitos']) ? trim($_POST['requisitos']) : null,
                'imagen_principal' => $imagenPrincipal,
                'stock_disponible' => isset($_POST['stock_disponible']) && !empty($_POST['stock_disponible']) && $_POST['stock_disponible'] > 0 ? (int)$_POST['stock_disponible'] : null,
                'disponible' => isset($_POST['disponible']) ? (int)$_POST['disponible'] : 1,
                'activo' => isset($_POST['activo']) ? (int)$_POST['activo'] : 1,
                'codigo_producto' => isset($_POST['codigo_producto']) && !empty($_POST['codigo_producto']) ? trim($_POST['codigo_producto']) : null,
                'marca' => isset($_POST['marca']) && !empty($_POST['marca']) ? trim($_POST['marca']) : null,
                'modelo' => isset($_POST['modelo']) && !empty($_POST['modelo']) ? trim($_POST['modelo']) : null,
                'especificaciones' => isset($_POST['especificaciones']) && !empty($_POST['especificaciones']) ? $_POST['especificaciones'] : null,
                'peso' => isset($_POST['peso']) && !empty($_POST['peso']) && $_POST['peso'] > 0 ? (float)$_POST['peso'] : null,
                'dimensiones' => isset($_POST['dimensiones']) && !empty($_POST['dimensiones']) ? trim($_POST['dimensiones']) : null,
                'garantia_meses' => isset($_POST['garantia_meses']) && !empty($_POST['garantia_meses']) && $_POST['garantia_meses'] > 0 ? (int)$_POST['garantia_meses'] : null,
                'proveedor' => isset($_POST['proveedor']) && !empty($_POST['proveedor']) ? trim($_POST['proveedor']) : null,
            ];

            $idBeneficio = $this->beneficioModel->crear($datos);

            if ($idBeneficio) {
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

            if (empty($_POST['categoria'])) {
                echo json_encode(['success' => false, 'message' => 'La categoría es obligatoria.']);
                return;
            }

            if (empty($_POST['precio_contado']) || $_POST['precio_contado'] <= 0) {
                echo json_encode(['success' => false, 'message' => 'El precio al contado es obligatorio y debe ser mayor a 0.']);
                return;
            }

            // Validar categoría contra la base de datos
            if (!$this->validarCategoriaExiste($_POST['categoria'])) {
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
            $imagenPrincipal = $beneficioExistente['imagen_principal']; // Mantener la actual por defecto
            if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
                $nuevaImagen = $this->procesarImagenUpload($_FILES['imagen_principal']);
                if ($nuevaImagen) {
                    // Eliminar imagen anterior si existe
                    if ($imagenPrincipal && file_exists('public/' . $imagenPrincipal)) {
                        unlink('public/' . $imagenPrincipal);
                    }
                    $imagenPrincipal = $nuevaImagen;
                }
            }

            // Preparar datos
            $datos = [
                'nombre' => trim($_POST['nombre']),
                'categoria' => (int)$_POST['categoria'],
                'descripcion' => isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null,
                'precio_contado' => (float)$_POST['precio_contado'],
                'precio_financiado' => isset($_POST['precio_financiado']) && $_POST['precio_financiado'] > 0 ? (float)$_POST['precio_financiado'] : null,
                'cuotas_disponibles' => isset($_POST['cuotas_disponibles']) ? trim($_POST['cuotas_disponibles']) : null,
                'tasa_interes' => isset($_POST['tasa_interes']) && $_POST['tasa_interes'] > 0 ? (float)$_POST['tasa_interes'] : null,
                'requisitos' => isset($_POST['requisitos']) ? trim($_POST['requisitos']) : null,
                'imagen_principal' => $imagenPrincipal,
                'stock_disponible' => isset($_POST['stock_disponible']) && $_POST['stock_disponible'] > 0 ? (int)$_POST['stock_disponible'] : null,
                'disponible' => isset($_POST['disponible']) ? (int)$_POST['disponible'] : 1,
                'codigo_producto' => isset($_POST['codigo_producto']) ? trim($_POST['codigo_producto']) : null,
                'marca' => isset($_POST['marca']) ? trim($_POST['marca']) : null,
                'modelo' => isset($_POST['modelo']) ? trim($_POST['modelo']) : null,
                'especificaciones' => isset($_POST['especificaciones']) ? $_POST['especificaciones'] : null,
                'peso' => isset($_POST['peso']) && $_POST['peso'] > 0 ? (float)$_POST['peso'] : null,
                'dimensiones' => isset($_POST['dimensiones']) ? trim($_POST['dimensiones']) : null,
                'garantia_meses' => isset($_POST['garantia_meses']) && $_POST['garantia_meses'] > 0 ? (int)$_POST['garantia_meses'] : null,
                'proveedor' => isset($_POST['proveedor']) ? trim($_POST['proveedor']) : null,
            ];

            $actualizado = $this->beneficioModel->actualizar((int)$id, $datos);

            if ($actualizado) {
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

            $eliminado = $this->beneficioModel->eliminar((int)$id);

            if ($eliminado) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Beneficio eliminado exitosamente.'
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

}