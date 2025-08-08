<?php
require_once "app/models/DireccionConductor.php";

class Conductor
{
    private $id_conductor;
    private $tipo_doc;
    private $nro_documento;
    private $nombres;
    private $apellido_paterno;
    private $apellido_materno;
    private $nacionalidad;
    private $nro_licencia;
    private $telefono;
    private $correo;
    private $categoria_licencia;
    private $fech_nac;
    private $foto;
    private $numeroCodFi;
    private $numUnidad;
    private $fotoPath;
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    // Método para establecer la ruta de la foto
    public function setFotoPath($path) // Nuevo método añadido
    {
        $this->fotoPath = $path; // Asigna la ruta de la foto a la propiedad privada
    }

    // Método para obtener la ruta de la foto
    public function getFotoPath() // Nuevo método añadido
    {
        return $this->fotoPath; // Devuelve la ruta almacenada en la propiedad
    }

    public function guardarFoto($archivo)
    {
        try {
            // Definir la ruta pública para las fotos
            $rutaPublica = 'public/fotos/conductores/';
            
            // Asegurarse que el directorio existe
            if (!file_exists($rutaPublica)) {
                if (!mkdir($rutaPublica, 0755, true)) {
                    error_log("Error al crear el directorio: " . $rutaPublica);
                    return false;
                }
            }



            // Generar nombre único para el archivo
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombreArchivo = uniqid('conductor_', true) . '.' . $extension;
            $rutaCompleta = $rutaPublica . $nombreArchivo;

            // Mover el archivo
            if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
                // Guardar solo la ruta relativa en la base de datos
                $this->foto = 'fotos/conductores/' . $nombreArchivo;

                // Establecer la ruta completa de la foto
                $this->setFotoPath($rutaCompleta); // Llamada al nuevo método setFotoPath

                return true;
            }
            
            error_log("Error al mover el archivo a: " . $rutaCompleta);
            return false;

        } catch (Exception $e) {
            error_log("Error en guardarFoto: " . $e->getMessage());
            return false;
        }
    }

public function insertar()
{
    try {

        // Obtener usuario_id de la sesión 🔹 Agregado para obtener el usuario
        $usuario_id = $_SESSION['usuario_id'] ?? null; // 🔹 NUEVO
        if (!$usuario_id) { // 🔹 NUEVO
            error_log("Error: No se encontró el ID del usuario en la sesión"); // 🔹 NUEVO
            throw new Exception('No se pudo obtener el ID del usuario.'); // 🔹 NUEVO
        } // 🔹 NUEVO

        // Validar fecha de nacimiento
        if (empty($this->fech_nac)) {
            error_log("Error: fecha de nacimiento vacía");
            throw new Exception('La fecha de nacimiento es requerida');
        }

        $sql = "INSERT INTO conductores (
            usuario_id, tipo_doc, nro_documento, nombres, apellido_paterno, 
            apellido_materno, nacionalidad, nro_licencia, telefono, 
            correo, categoria_licencia, fech_nac, foto, numeroCodFi, numUnidad
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $this->conectar->error);
            throw new Exception('Error al preparar la consulta');
        }

        $stmt->bind_param("issssssssssssii", 
            $usuario_id,
            $this->tipo_doc,
            $this->nro_documento,
            $this->nombres,
            $this->apellido_paterno,
            $this->apellido_materno,
            $this->nacionalidad,
            $this->nro_licencia,
            $this->telefono,
            $this->correo,
            $this->categoria_licencia,
            $this->fech_nac,
            $this->foto,
            $this->numeroCodFi,
            $this->numUnidad
        );

        // Log de los valores antes de insertar
        error_log("Insertando conductor con datos: " . print_r([
            'tipo_doc' => $this->tipo_doc,
            'nro_documento' => $this->nro_documento,
            'nombres' => $this->nombres,
            'fech_nac' => $this->fech_nac,
            'foto' => $this->foto
        ], true));

        if (!$stmt->execute()) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
        }

        $id = $stmt->insert_id;
        $stmt->close();
        
        if ($id > 0) {
            error_log("Conductor insertado exitosamente con ID: " . $id);
            return $id;
        } else {
            error_log("Error: No se obtuvo ID después de la inserción");
            throw new Exception('Error al obtener el ID del conductor insertado');
        }

    } catch (Exception $e) {
        error_log("Error en Conductor::insertar(): " . $e->getMessage());
        throw $e; // Relanzar la excepción para que sea manejada por el controlador
    }
}

public function obtenerNumDocFiltrado($searchTerm = '')
{
    try {
         $sql = "SELECT id_conductor, nro_documento, nombres, apellido_paterno, apellido_materno, numeroCodFi
                FROM conductores
                WHERE nro_documento LIKE ?
                LIMIT 10";

        $stmt = $this->conectar->prepare($sql);
        $searchTermLike = "%$searchTerm%";
        $stmt->bind_param("s", $searchTermLike);
        $stmt->execute();
        $result = $stmt->get_result();

        $conductores = [];
        while ($row = $result->fetch_assoc()) {
            $conductores[] = $row;
        }

        return $conductores;
    } catch (Exception $e) {
        error_log("Error en Conductor::obtenerNumDocFiltrado(): " . $e->getMessage());
        throw $e;
    }
}

    public function obtenerTodos() {
        try {
            $sql = "SELECT * FROM conductores ORDER BY nombres ASC";
            $result = $this->conectar->query($sql);
            
            if (!$result) {
                throw new Exception("Error al obtener los conductores");
            }
            
            $conductores = [];
            $vehiculo = new Vehiculo();
            $inscripcion = new Inscripcion();
            
            while ($row = $result->fetch_assoc()) {
                // Procesar la foto
                if ($row['foto'] && !empty($row['foto'])) {
                    $row['foto'] = '/arequipago/public/' . $row['foto'];
                }
                
                // Obtener datos del vehículo
                $datosVehiculo = $vehiculo->obtenerPlacaPorConductor($row['id_conductor']);
                $row['placa'] = $datosVehiculo ? $datosVehiculo['placa'] : 'Sin asignar';
                $row['numero_unidad'] = $datosVehiculo ? $datosVehiculo['numero_unidad'] : 'Sin asignar';
                $row['tipo_vehiculo'] = $datosVehiculo ? $datosVehiculo['tipo_vehiculo'] : null; // NUEVA LÍNEA

                // NUEVO: Obtener departamento del conductor
                $direccionConductor = new DireccionConductor();
                $datosDireccion = $direccionConductor->obtenerDireccionConductor($row['id_conductor']);
                $row['departamento_id'] = $datosDireccion ? $datosDireccion['departamento'] : null;
                
                // Obtener datos de inscripción
                $setare = $inscripcion->obtenerSetarePorConductor($row['id_conductor']);
                $row['setare'] = $setare ?: 'Sin asignar';
                
                // Verificar tipo de pago
                $tipoPagoSql = "SELECT id_tipopago FROM conductor_pago WHERE id_conductor = " . $row['id_conductor']; // NUEVO CAMBIO
                $tipoPagoResult = $this->conectar->query($tipoPagoSql); // NUEVO CAMBIO
                $row['tipo_pago'] = ($tipoPagoResult && $tipoPagoResult->num_rows > 0) ? $tipoPagoResult->fetch_assoc()['id_tipopago'] : null; // NUEVO CAMBIO

                $conductores[] = $row;
            }
            
            return $conductores;
            
        } catch (Exception $e) {
            error_log("Error en Conductor::obtenerTodos(): " . $e->getMessage());
            return false;
        }
    } 

    public function obtenerTodosConductores($pagina, $cantidadPorPagina, $sortField = null, $sortDirection = null)
    {
        $offset = ($pagina - 1) * $cantidadPorPagina;
        
        // 🔴 Base de la consulta SQL
        $query = "SELECT 
                    c.id_conductor, 
                    c.nombres, 
                    c.apellido_paterno, 
                    c.apellido_materno, 
                    c.numUnidad,
                    COALESCE(MAX(f.codigo_asociado), '') AS codigo_asociado, 
                    COALESCE(MAX(f.grupo_financiamiento), '') AS grupo_financiamiento,
                    COUNT(f.idfinanciamiento) AS cantidad_financiamientos,
                    MAX(f.fecha_creacion) AS fecha_ultimo_financiamiento
                FROM conductores c
                INNER JOIN financiamiento f ON c.id_conductor = f.id_conductor
                GROUP BY c.id_conductor, c.nombres, c.apellido_paterno, c.apellido_materno";
        
        // 🔴 Aplicar ordenamiento si se proporciona
        if ($sortField && $sortDirection) {
            if ($sortField === 'fecha_ultimo_financiamiento') {
                $query .= " ORDER BY MAX(f.fecha_creacion) " . ($sortDirection === 'asc' ? 'ASC' : 'DESC');
            }
        }
        
        $query .= " LIMIT ? OFFSET ?";

        $stmt = $this->conectar->prepare($query);
        $stmt->bind_param('ii', $cantidadPorPagina, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        $conductores = [];
        while ($row = $result->fetch_assoc()) {
            $grupoFinanciamiento = $row['grupo_financiamiento'];

            $nombrePlan = '';
            if ($grupoFinanciamiento) {
                $queryPlan = "SELECT nombre_plan FROM planes_financiamiento WHERE idplan_financiamiento = ?";
                $stmtPlan = $this->conectar->prepare($queryPlan);
                $stmtPlan->bind_param('i', $grupoFinanciamiento);
                $stmtPlan->execute();
                $stmtPlan->bind_result($nombrePlan);
                $stmtPlan->fetch();
                $stmtPlan->close();
            }

            $row['grupo_financiamiento'] = $nombrePlan;
            
            $conductores[] = $row;
        }

        // 🔴 Construcción de la consulta para clientes no conductores
        $queryClientes = "SELECT 
            cl.id, 
            cl.nombres, 
            cl.apellido_paterno, 
            cl.apellido_materno, 
            NULL AS numUnidad,
            COALESCE(MAX(f.codigo_asociado), '') AS codigo_asociado, 
            COALESCE(MAX(f.grupo_financiamiento), '') AS grupo_financiamiento, 
            COUNT(f.idfinanciamiento) AS cantidad_financiamientos,
            MAX(f.fecha_creacion) AS fecha_ultimo_financiamiento  
        FROM clientes_financiar cl
        INNER JOIN financiamiento f ON cl.id = f.id_cliente
        GROUP BY cl.id, cl.nombres, cl.apellido_paterno, cl.apellido_materno";
        
        // 🔴 Aplicar ordenamiento a la consulta de clientes
        if ($sortField && $sortDirection) {
            if ($sortField === 'fecha_ultimo_financiamiento') {
                $queryClientes .= " ORDER BY MAX(f.fecha_creacion) " . ($sortDirection === 'asc' ? 'ASC' : 'DESC');
            }
        }
        
        $queryClientes .= " LIMIT ? OFFSET ?";

        $stmtClientes = $this->conectar->prepare($queryClientes);
        $stmtClientes->bind_param('ii', $cantidadPorPagina, $offset);
        $stmtClientes->execute();
        $resultClientes = $stmtClientes->get_result();

        while ($row = $resultClientes->fetch_assoc()) {
            $grupoFinanciamiento = $row['grupo_financiamiento'];

            $nombrePlan = '';
            if ($grupoFinanciamiento) {
                $queryPlan = "SELECT nombre_plan FROM planes_financiamiento WHERE idplan_financiamiento = ?";
                $stmtPlan = $this->conectar->prepare($queryPlan);
                $stmtPlan->bind_param('i', $grupoFinanciamiento);
                $stmtPlan->execute();
                $stmtPlan->bind_result($nombrePlan);
                $stmtPlan->fetch();
                $stmtPlan->close();
            }

            $row['grupo_financiamiento'] = $nombrePlan;

            $conductores[] = $row;
        }

        // 🔴 Si hay que ordenar y tenemos datos de ambas consultas, ordenamos el resultado combinado
        if ($sortField === 'fecha_ultimo_financiamiento' && $sortDirection) {
            usort($conductores, function($a, $b) use ($sortDirection) {
                $fechaA = $a['fecha_ultimo_financiamiento'] ? strtotime($a['fecha_ultimo_financiamiento']) : 0;
                $fechaB = $b['fecha_ultimo_financiamiento'] ? strtotime($b['fecha_ultimo_financiamiento']) : 0;
                
                if ($sortDirection === 'asc') {
                    return $fechaA <=> $fechaB;
                } else {
                    return $fechaB <=> $fechaA;
                }
            });
        }

        return $conductores;
    }

public function obtenerConductoresFiltrados($searchTerm, $pagina, $cantidadPorPagina, $sortField = null, $sortDirection = null)
{
    $offset = ($pagina - 1) * $cantidadPorPagina;
    $searchTermLike = "%$searchTerm%";

    $grupo_id = null;
    
    // 🐱 Agregado: Consulta para buscar si el término coincide exactamente con algún nombre de plan
    $queryGrupo = "SELECT idplan_financiamiento FROM planes_financiamiento WHERE nombre_plan = ?";
    $stmtGrupo = $this->conectar->prepare($queryGrupo);
    $stmtGrupo->bind_param('s', $searchTerm);
    $stmtGrupo->execute();
    $resultGrupo = $stmtGrupo->get_result();
    
    if ($resultGrupo->num_rows > 0) {
        $rowGrupo = $resultGrupo->fetch_assoc();
        $grupo_id = $rowGrupo['idplan_financiamiento'];
    }
    $stmtGrupo->close();

    // 🐱 Agregado: También verificar si coincide con un nombre parcial (LIKE)
    if (!$grupo_id) {
        $queryGrupoLike = "SELECT idplan_financiamiento FROM planes_financiamiento WHERE nombre_plan LIKE ?";
        $stmtGrupoLike = $this->conectar->prepare($queryGrupoLike);
        $stmtGrupoLike->bind_param('s', $searchTermLike);
        $stmtGrupoLike->execute();
        $resultGrupoLike = $stmtGrupoLike->get_result();
        
        if ($resultGrupoLike->num_rows > 0) {
            $rowGrupoLike = $resultGrupoLike->fetch_assoc();
            $grupo_id = $rowGrupoLike['idplan_financiamiento'];
        }
        $stmtGrupoLike->close();
    }

    // 🔴 Consulta base para conductores con búsqueda
    $queryConductores = "SELECT 
            c.id_conductor, 
            c.nombres, 
            c.apellido_paterno, 
            c.apellido_materno, 
            c.numUnidad,
            COALESCE(MAX(f.codigo_asociado), '') AS codigo_asociado, 
            COALESCE(MAX(f.grupo_financiamiento), '') AS grupo_financiamiento,
            COUNT(f.idfinanciamiento) AS cantidad_financiamientos,
            MAX(f.fecha_creacion) AS fecha_ultimo_financiamiento
        FROM conductores c
        INNER JOIN financiamiento f ON c.id_conductor = f.id_conductor
        WHERE ";

        // 🐱 Modificado: Agregar condición de búsqueda por grupo si se encontró uno
    if ($grupo_id) {
        $queryConductores .= "f.grupo_financiamiento = ? ";
    } else if ($searchTerm === "Sin Grupo" || $searchTerm === "sin grupo") {
        // 🐱 Agregado: Caso especial para buscar financiamientos sin grupo
        $queryConductores .= "f.grupo_financiamiento = 'notGrupo' ";
    } else {
        // 🐱 Mantenemos la lógica original de búsqueda
        $queryConductores .= "c.nombres LIKE ? 
           OR c.apellido_paterno LIKE ? 
           OR c.apellido_materno LIKE ?
           OR f.codigo_asociado LIKE ?
           OR c.numUnidad LIKE ? "; // 🐱 Agregado: Búsqueda por número de unidad
    }
        
    // 🔴 Aplicar ordenamiento a conductores
    $queryConductores .= " GROUP BY c.id_conductor "; // ☁️ Añadido GROUP BY que faltaba
        
    if ($sortField && $sortDirection) {
        if ($sortField === 'fecha_ultimo_financiamiento') {
            $queryConductores .= " ORDER BY MAX(f.fecha_creacion) " . ($sortDirection === 'asc' ? 'ASC' : 'DESC');
        }
    }
    
    $queryConductores .= " LIMIT ? OFFSET ?";
    
    $stmt = $this->conectar->prepare($queryConductores);
    if ($grupo_id) {
        $stmt->bind_param('sii', $grupo_id, $cantidadPorPagina, $offset);
    } else if ($searchTerm === "Sin Grupo" || $searchTerm === "sin grupo") {
        $stmt->bind_param('ii', $cantidadPorPagina, $offset);
    } else {
        $stmt->bind_param('sssssii', $searchTermLike, $searchTermLike, $searchTermLike, $searchTermLike, $searchTermLike, $cantidadPorPagina, $offset); // ☁️ Corregido el número de 's', ahora son 5 parámetros 's'
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $conductores = [];
    
    // ☁️ Añadida verificación para asegurar que $result no es booleano
    if ($result && $result !== true) { // ☁️ Verificar que $result no es booleano (false o true)
        while ($row = $result->fetch_assoc()) {
            // Procesar grupo de financiamiento igual que antes
            $grupoFinanciamiento = $row['grupo_financiamiento'];
            
            $nombrePlan = '';
            if ($grupoFinanciamiento) {
                $queryPlan = "SELECT nombre_plan FROM planes_financiamiento WHERE idplan_financiamiento = ?";
                $stmtPlan = $this->conectar->prepare($queryPlan);
                $stmtPlan->bind_param('i', $grupoFinanciamiento);
                $stmtPlan->execute();
                $stmtPlan->bind_result($nombrePlan);
                $stmtPlan->fetch();
                $stmtPlan->close();
            }
            
            $row['grupo_financiamiento'] = $nombrePlan;
            $conductores[] = $row;
        }
    }
    
    // 🔴 Consulta para clientes no conductores con búsqueda
    $queryClientes = "SELECT 
            cl.id, 
            cl.nombres, 
            cl.apellido_paterno, 
            cl.apellido_materno, 
            NULL AS numUnidad,
            COALESCE(MAX(f.codigo_asociado), '') AS codigo_asociado, 
            COALESCE(MAX(f.grupo_financiamiento), '') AS grupo_financiamiento,
            COUNT(f.idfinanciamiento) AS cantidad_financiamientos,
            MAX(f.fecha_creacion) AS fecha_ultimo_financiamiento
        FROM clientes_financiar cl
        INNER JOIN financiamiento f ON cl.id = f.id_cliente
        WHERE ";
    
    // 🐱 Modificado: Agregar condición de búsqueda por grupo si se encontró uno
    if ($grupo_id) {
        $queryClientes .= "f.grupo_financiamiento = ? ";
    } else if ($searchTerm === "Sin Grupo" || $searchTerm === "sin grupo") {
        // 🐱 Agregado: Caso especial para buscar financiamientos sin grupo
        $queryClientes .= "f.grupo_financiamiento = 'notGrupo' ";
    } else {
        // 🐱 Mantenemos la lógica original de búsqueda
        $queryClientes .= "cl.nombres LIKE ? 
           OR cl.apellido_paterno LIKE ? 
           OR cl.apellido_materno LIKE ?
           OR f.codigo_asociado LIKE ? ";
    }
    
    $queryClientes .= " GROUP BY cl.id "; // ☁️ Añadido GROUP BY que faltaba
    
    // 🔴 Aplicar ordenamiento a clientes
    if ($sortField && $sortDirection) {
        if ($sortField === 'fecha_ultimo_financiamiento') {
            $queryClientes .= " ORDER BY MAX(f.fecha_creacion) " . ($sortDirection === 'asc' ? 'ASC' : 'DESC');
        }
    }
    
    $queryClientes .= " LIMIT ? OFFSET ?";
    
    $stmtClientes = $this->conectar->prepare($queryClientes);
    
     // 🐱 Modificado: Diferentes bind_param según el tipo de búsqueda
     if ($grupo_id) {
        $stmtClientes->bind_param('sii', $grupo_id, $cantidadPorPagina, $offset);
    } else if ($searchTerm === "Sin Grupo" || $searchTerm === "sin grupo") {
        $stmtClientes->bind_param('ii', $cantidadPorPagina, $offset);
    } else {
        $stmtClientes->bind_param('ssssii', $searchTermLike, $searchTermLike, $searchTermLike, $searchTermLike, $cantidadPorPagina, $offset);
    }
    
    $stmtClientes->execute();
    $resultClientes = $stmtClientes->get_result();
    
    // ☁️ Añadida verificación para asegurar que $resultClientes no es booleano
    if ($resultClientes && $resultClientes !== true) { // ☁️ Verificar que $resultClientes no es booleano (false o true)
        while ($row = $resultClientes->fetch_assoc()) {
            // Procesar igual que antes
            $grupoFinanciamiento = $row['grupo_financiamiento'];
            
            $nombrePlan = '';
            if ($grupoFinanciamiento) {
                if ($grupoFinanciamiento === 'notGrupo') {
                    $nombrePlan = 'Sin Grupo';
                } else {
                    $queryPlan = "SELECT nombre_plan FROM planes_financiamiento WHERE idplan_financiamiento = ?";
                    $stmtPlan = $this->conectar->prepare($queryPlan);
                    $stmtPlan->bind_param('i', $grupoFinanciamiento);
                    $stmtPlan->execute();
                    $stmtPlan->bind_result($nombrePlan);
                    $stmtPlan->fetch();
                    $stmtPlan->close();
                }
            }
            
            $row['grupo_financiamiento'] = $nombrePlan;
            $conductores[] = $row;
        }
    }
    
    // 🔴 Si hay que ordenar y tenemos datos de ambas consultas, ordenamos el resultado combinado
    if ($sortField === 'fecha_ultimo_financiamiento' && $sortDirection) {
        usort($conductores, function($a, $b) use ($sortDirection) {
            $fechaA = $a['fecha_ultimo_financiamiento'] ? strtotime($a['fecha_ultimo_financiamiento']) : 0;
            $fechaB = $b['fecha_ultimo_financiamiento'] ? strtotime($b['fecha_ultimo_financiamiento']) : 0;
            
            if ($sortDirection === 'asc') {
                return $fechaA <=> $fechaB;
            } else {
                return $fechaB <=> $fechaA;
            }
        });
    }
    
    return $conductores;
}



// Función para normalizar cadenas (sin cambios)
private function normalizeString($string) {
    return strtolower(str_replace(
        ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', ' '],
        ['a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', ''],
        $string
    ));
}


    public function obtenerConductoresConCodigo($searchTerm = '', $pagina = 1, $cantidadPorPagina = 12)
    {
        try {
            $offset = ($pagina - 1) * $cantidadPorPagina;

            // Consulta ajustada para tomar numeroCodFi de conductores
            $sql = "SELECT 
                        c.id_conductor, 
                        c.nro_documento, 
                        CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS datos, 
                        COALESCE(c.numeroCodFi, '') AS codigo_asociado, 
                        COALESCE(MAX(f.grupo_financiamiento), '') AS grupo_financiamiento, 
                        COUNT(f.idfinanciamiento) AS cantidad_financiamientos
                    FROM conductores c
                    LEFT JOIN financiamiento f ON c.id_conductor = f.id_conductor
                    WHERE c.nombres LIKE ? 
                    OR c.apellido_paterno LIKE ? 
                    OR c.apellido_materno LIKE ? 
                    OR c.numeroCodFi LIKE ?
                    GROUP BY c.id_conductor, c.nro_documento, c.nombres, c.apellido_paterno, c.apellido_materno, c.numeroCodFi
                    LIMIT ? OFFSET ?";

            $stmt = $this->conectar->prepare($sql);
            $searchTermLike = "%$searchTerm%";
            $stmt->bind_param("ssssii", $searchTermLike, $searchTermLike, $searchTermLike, $searchTermLike, $cantidadPorPagina, $offset);
            $stmt->execute();
            $result = $stmt->get_result();

            $conductores = [];
            while ($row = $result->fetch_assoc()) {
                $conductores[] = $row;
            }

            return $conductores;
        } catch (Exception $e) {
            error_log("Error en Conductor::obtenerConductoresConCodigo(): " . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerTotalClientesBusqueda($searchTerm = '') {
        try {
            $searchTermLike = "%$searchTerm%";
            
            // ☁️ Eliminado el código inicial que usa variables no definidas
            // ☁️ Añadida inicialización de la variable $grupo_id
            $grupo_id = null; // ☁️ Inicializamos $grupo_id
            
            // ☁️ Agregado código para buscar si el término coincide con algún nombre de plan
            if ($searchTerm) {
                $queryGrupo = "SELECT idplan_financiamiento FROM planes_financiamiento WHERE nombre_plan = ?";
                $stmtGrupo = $this->conectar->prepare($queryGrupo);
                $stmtGrupo->bind_param('s', $searchTerm);
                $stmtGrupo->execute();
                $resultGrupo = $stmtGrupo->get_result();
                
                if ($resultGrupo->num_rows > 0) {
                    $rowGrupo = $resultGrupo->fetch_assoc();
                    $grupo_id = $rowGrupo['idplan_financiamiento'];
                }
                $stmtGrupo->close();
    
                // También verificar si coincide con un nombre parcial (LIKE)
                if (!$grupo_id) {
                    $queryGrupoLike = "SELECT idplan_financiamiento FROM planes_financiamiento WHERE nombre_plan LIKE ?";
                    $stmtGrupoLike = $this->conectar->prepare($queryGrupoLike);
                    $stmtGrupoLike->bind_param('s', $searchTermLike);
                    $stmtGrupoLike->execute();
                    $resultGrupoLike = $stmtGrupoLike->get_result();
                    
                    if ($resultGrupoLike->num_rows > 0) {
                        $rowGrupoLike = $resultGrupoLike->fetch_assoc();
                        $grupo_id = $rowGrupoLike['idplan_financiamiento'];
                    }
                    $stmtGrupoLike->close();
                }
            }
            
            // ☁️ Eliminado todo el código inicial redundante que utilizaba variables no definidas
                
            // Contar conductores con al menos un financiamiento
            // CAMBIO: Añadido GROUP BY y HAVING para contar solo conductores con financiamientos
            $sqlConductores = "SELECT COUNT(*) AS total FROM (
                    SELECT c.id_conductor
                    FROM conductores c
                    LEFT JOIN financiamiento f ON c.id_conductor = f.id_conductor
                    WHERE ";
    
            // 🐱 Modificado: Diferentes condiciones según el tipo de búsqueda
            if ($grupo_id) {
                $sqlConductores .= "f.grupo_financiamiento = ? ";
            } else if ($searchTerm === "Sin Grupo" || $searchTerm === "sin grupo") {
                $sqlConductores .= "f.grupo_financiamiento = 'notGrupo' ";
            } else {
                $sqlConductores .= "c.nombres LIKE ? 
                    OR c.apellido_paterno LIKE ? 
                    OR c.apellido_materno LIKE ? 
                    OR f.codigo_asociado LIKE ?
                    OR c.numUnidad LIKE ? "; // 🐱 Agregado: Búsqueda por número de unidad
            }
            
            $sqlConductores .= "GROUP BY c.id_conductor
                    HAVING COUNT(f.idfinanciamiento) > 0
                ) AS conductores_con_financiamiento";
            
            $stmtConductores = $this->conectar->prepare($sqlConductores);
            
            // 🐱 Modificado: Diferentes bind_param según el tipo de búsqueda
            if ($grupo_id) {
                $stmtConductores->bind_param("s", $grupo_id);
            } else if ($searchTerm === "Sin Grupo" || $searchTerm === "sin grupo") {
                // No necesita parámetros
            } else {
                $stmtConductores->bind_param("sssss", $searchTermLike, $searchTermLike, $searchTermLike, $searchTermLike, $searchTermLike);
            }
            
            $stmtConductores->execute();
            $resultConductores = $stmtConductores->get_result();
            $rowConductores = $resultConductores->fetch_assoc();
            $totalConductores = $rowConductores['total'] ?? 0;
            
            // Contar clientes con al menos un financiamiento
            $sqlClientes = "SELECT COUNT(*) AS total FROM (
                    SELECT cl.id
                    FROM clientes_financiar cl
                    LEFT JOIN financiamiento f ON cl.id = f.id_cliente
                    WHERE ";
            
            // 🐱 Modificado: Diferentes condiciones según el tipo de búsqueda
            if ($grupo_id) {
                $sqlClientes .= "f.grupo_financiamiento = ? ";
            } else if ($searchTerm === "Sin Grupo" || $searchTerm === "sin grupo") {
                $sqlClientes .= "f.grupo_financiamiento = 'notGrupo' ";
            } else {
                $sqlClientes .= "cl.nombres LIKE ? 
                    OR cl.apellido_paterno LIKE ? 
                    OR cl.apellido_materno LIKE ? 
                    OR f.codigo_asociado LIKE ? ";
            }
            
            $sqlClientes .= "GROUP BY cl.id
                    HAVING COUNT(f.idfinanciamiento) > 0
                ) AS clientes_con_financiamiento";
            
            $stmtClientes = $this->conectar->prepare($sqlClientes); // ☁️ Ya no usa una variable no definida
            
            // 🐱 Modificado: Diferentes bind_param según el tipo de búsqueda
            if ($grupo_id) {
                $stmtClientes->bind_param("s", $grupo_id);
            } else if ($searchTerm === "Sin Grupo" || $searchTerm === "sin grupo") {
                // No necesita parámetros
            } else {
                $stmtClientes->bind_param("ssss", $searchTermLike, $searchTermLike, $searchTermLike, $searchTermLike);
            }
            
            $stmtClientes->execute();
            $resultClientes = $stmtClientes->get_result();
            $rowClientes = $resultClientes->fetch_assoc();
            $totalClientes = $rowClientes['total'] ?? 0;
            
            // Retornar la suma
            return $totalConductores + $totalClientes;
        } catch (Exception $e) {
            error_log("Error en Conductor::obtenerTotalClientesBusqueda(): " . $e->getMessage());
            throw $e;
        }
    }

    // Método para obtener el total de conductores
    public function obtenerTotalConductores()
    {
        $query = "SELECT COUNT(*) as total FROM conductores";
        $result = $this->conectar->query($query);
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    } 

public function eliminar() {
    try {
        // Primero obtener la foto para eliminarla
        $sql = "SELECT foto FROM conductores WHERE id_conductor = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $this->id_conductor);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        // Eliminar el registro
        $sql = "DELETE FROM conductores WHERE id_conductor = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $this->id_conductor);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al eliminar conductor: " . $stmt->error);
        }
        
        // Si se eliminó correctamente y existe la foto, eliminarla
        if ($stmt->affected_rows > 0 && $row && $row['foto']) {
            $rutaFoto = 'public/' . $row['foto'];
            if (file_exists($rutaFoto)) {
                unlink($rutaFoto);
            }
        }
        
        return true;
        
    } catch (Exception $e) {
        error_log("Error en Conductor::eliminar(): " . $e->getMessage());
        return false;
    }
}

    public function modificar()
    {
        try {
            $sql = "UPDATE conductores SET 
                    tipo_doc = ?, 
                    nro_documento = ?, 
                    nombres = ?, 
                    apellido_paterno = ?, 
                    apellido_materno = ?, 
                    nacionalidad = ?, 
                    nro_licencia = ?, 
                    telefono = ?, 
                    correo = ?,
                    categoria_licencia = ?,
                    fech_nac = ?,
                    foto = ?,
                    numeroCodFi = ?,
                    numUnidad = ?
                WHERE id_conductor = ?";

            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparing statement: " . $this->conectar->error);
            }

            $stmt->bind_param("ssssssssssssiis",
                $this->tipo_doc,
                $this->nro_documento,
                $this->nombres,
                $this->apellido_paterno,
                $this->apellido_materno,
                $this->nacionalidad,
                $this->nro_licencia,
                $this->telefono,
                $this->correo,
                $this->categoria_licencia,
                $this->fech_nac,
                $this->foto,
                $this->numeroCodFi,
                $this->numUnidad,
                $this->id_conductor
            );

            if (!$stmt->execute()) {
                throw new Exception("Error executing statement: " . $stmt->error);
            }

            $stmt->close();
            return true;
        } catch (Exception $e) {
            error_log("Error in Conductor::modificar(): " . $e->getMessage());
            return false;
        }
    }

    public function obtenerDetalleConductor($id_conductor)
    {
        try {
            // Consulta para obtener los detalles del conductor
            $sql = "SELECT tipo_doc, nro_documento, CONCAT(nombres, ' ', apellido_paterno, ' ', apellido_materno) AS nombre_completo, telefono 
                    FROM conductores 
                    WHERE id_conductor = ?";
            
            // Preparar la consulta
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_conductor); // El id_conductor es un entero
            $stmt->execute();
            
            // Obtener el resultado
            $result = $stmt->get_result();
            
            // Devolver los datos del conductor
            return $result->fetch_assoc();
        } catch (Exception $e) {
            // Manejo de errores
            return ['error' => 'Error al obtener el conductor: ' . $e->getMessage()];
        }

        
    }

    public function buscarPorCriterioAvanzado($query) {
        try {
            // Normalizar el criterio de búsqueda eliminando espacios extra y manejando mayúsculas/minúsculas
            $query = trim(preg_replace('/\s+/', ' ', $query));
    
            $sql = "
                SELECT * 
                FROM conductores 
                WHERE 
                    LOWER(TRIM(REPLACE(nombres, '  ', ' '))) LIKE ? OR 
                    LOWER(TRIM(REPLACE(apellido_paterno, '  ', ' '))) LIKE ? OR 
                    LOWER(TRIM(REPLACE(apellido_materno, '  ', ' '))) LIKE ? OR 
                    nro_documento LIKE ?
                ORDER BY nombres ASC
            ";
    
            // Preparar la consulta
            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $this->conectar->error);
            }
    
            // Añadir comodines para la búsqueda parcial
            $likeQuery = '%' . strtolower($query) . '%';
            $stmt->bind_param("ssss", $likeQuery, $likeQuery, $likeQuery, $likeQuery);
    
            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }
    
            $result = $stmt->get_result();
            if (!$result) {
                throw new Exception("Error al obtener los resultados: " . $this->conectar->error);
            }
    
            $conductores = [];
            $vehiculo = new Vehiculo();
            $inscripcion = new Inscripcion();
    
            while ($row = $result->fetch_assoc()) {
                // Procesar la foto
                if ($row['foto'] && !empty($row['foto'])) {
                    $row['foto'] = '/arequipago/public/' . $row['foto'];
                }
    
                // Obtener datos del vehículo
                $datosVehiculo = $vehiculo->obtenerPlacaPorConductor($row['id_conductor']);
                $row['placa'] = $datosVehiculo ? $datosVehiculo['placa'] : 'Sin asignar';
                $row['numero_unidad'] = $datosVehiculo ? $datosVehiculo['numero_unidad'] : 'Sin asignar';
    
                // Obtener datos de inscripción
                $setare = $inscripcion->obtenerSetarePorConductor($row['id_conductor']);
                $row['setare'] = $setare ?: 'Sin asignar';
    
                $conductores[] = $row;
            }
    
            return $conductores;
    
        } catch (Exception $e) {
            
            return [];
        }
    }

    public function buscarPorRangoDeFechas($fechaInicio, $fechaFin) {
        try {
            // Consulta para obtener los conductores registrados dentro del rango de fechas
            $sql = "
                SELECT c.*, i.fecha_inscripcion, i.setare, i.nro_unidad
                FROM conductores c
                JOIN inscripciones i ON c.id_conductor = i.id_conductor
                WHERE i.fecha_inscripcion BETWEEN ? AND ?
                ORDER BY i.fecha_inscripcion ASC
            ";
    
            // Preparar la consulta
            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $this->conectar->error);
            }
    
            // Vincular los parámetros
            $stmt->bind_param("ss", $fechaInicio, $fechaFin);
    
            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }
    
            $result = $stmt->get_result();
            if (!$result) {
                throw new Exception("Error al obtener los resultados: " . $this->conectar->error);
            }
    
            $conductores = [];
            $vehiculo = new Vehiculo();
            $inscripcion = new Inscripcion();
    
            while ($row = $result->fetch_assoc()) {
                // Procesar la foto
                if ($row['foto'] && !empty($row['foto'])) {
                    $row['foto'] = '/arequipago/public/' . $row['foto'];
                }
    
                // Obtener datos del vehículo
                $datosVehiculo = $vehiculo->obtenerPlacaPorConductor($row['id_conductor']);
                $row['placa'] = $datosVehiculo ? $datosVehiculo['placa'] : 'Sin asignar';
                $row['numero_unidad'] = $datosVehiculo ? $datosVehiculo['numero_unidad'] : 'Sin asignar';
    
                // Obtener datos de inscripción
                $setare = $inscripcion->obtenerSetarePorConductor($row['id_conductor']);
                $row['setare'] = $setare ?: 'Sin asignar';
    
                $conductores[] = $row;
            }
    
            return $conductores;
    
        } catch (Exception $e) {
            return [];
        }
    }

        public function obtenerDatosConductor($id_conductor) // Cambiado de private a public
        {
            $query = "SELECT * FROM conductores WHERE id_conductor = ?";
            $stmt = $this->conectar->prepare($query); // Conexión a la base de datos preparada
            $stmt->bind_param("i", $id_conductor); // Vincular parámetro id_conductor
            $stmt->execute(); // Ejecutar la consulta
            $result = $stmt->get_result(); // Obtener el resultado

            if ($result->num_rows > 0) {
            $data = $result->fetch_assoc(); // Obtener los datos como un arreglo asociativo
            $conductor = new Conductor(); // Crear una nueva instancia de Conductor
            $conductor->setIdConductor($data['id_conductor']); // Asignar el id del conductor
            $conductor->setNombres($data['nombres']); // Asignar nombres
            $conductor->setApellidoPaterno($data['apellido_paterno']); // Asignar apellido paterno
            $conductor->setApellidoMaterno($data['apellido_materno']); // Asignar apellido materno
            
            // Procesar la foto para agregar la ruta completa
            if ($data['foto'] && !empty($data['foto'])) {
                $conductor->setFoto('/arequipago/public/' . $data['foto']); // Modificado: Añadir ruta completa
            } else {
                $conductor->setFoto(null); // Modificado: Devolver null si no hay foto
            }
            
            return $conductor; // Retorna el objeto Conductor
            }

            return null;
        
        }

    public function obtenerTipoDocumento($nroDocumento)
    {
        $query = "SELECT tipo_doc FROM conductores WHERE nro_documento = ?";
        $stmt = $this->conectar->prepare($query);
        $stmt->bind_param("s", $nroDocumento);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['tipo_doc'];
        }
        
        return null; // Retorna null si no se encuentra el documento
    }

    public function getMissingData($idConductor) {
        $sql = "
            SELECT 
                telefono,
                apellido_paterno,
                apellido_materno,
                nombres,
                nro_licencia,
                correo,
                numUnidad,
                tipo_doc
            FROM conductores
            WHERE id_conductor = ?
        ";
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            die('Error al preparar la consulta conductor: ' . $this->conectar->error);
        }
        
        $stmt->bind_param('i', $idConductor);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc(); // Devuelve un array asociativo con los datos
    }
    
    
    
    

    // Getters and Setters
    public function getIdConductor() { return $this->id_conductor; }
    public function setIdConductor($id_conductor) { $this->id_conductor = $id_conductor; }
    
    public function getTipoDoc() { return $this->tipo_doc; }
    public function setTipoDoc($tipo_doc) { $this->tipo_doc = $tipo_doc; }
    
    public function getNroDocumento() { return $this->nro_documento; }
    public function setNroDocumento($nro_documento) { $this->nro_documento = $nro_documento; }
    
    public function getNombres() { return $this->nombres; }
    public function setNombres($nombres) { $this->nombres = $nombres; }
    
    public function getApellidoPaterno() { return $this->apellido_paterno; }
    public function setApellidoPaterno($apellido_paterno) { $this->apellido_paterno = $apellido_paterno; }
    
    public function getApellidoMaterno() { return $this->apellido_materno; }
    public function setApellidoMaterno($apellido_materno) { $this->apellido_materno = $apellido_materno; }
    
    public function getNacionalidad() { return $this->nacionalidad; }
    public function setNacionalidad($nacionalidad) { $this->nacionalidad = $nacionalidad; }
    
    public function getNroLicencia() { return $this->nro_licencia; }
    public function setNroLicencia($nro_licencia) { $this->nro_licencia = $nro_licencia; }
    
    public function getTelefono() { return $this->telefono; }
    public function setTelefono($telefono) { $this->telefono = $telefono; }
    
    public function getCorreo() { return $this->correo; }
    public function setCorreo($correo) { $this->correo = $correo; }
    
    public function getCategoriaLicencia() { return $this->categoria_licencia; }
    public function setCategoriaLicencia($categoria_licencia) { $this->categoria_licencia = $categoria_licencia; }
    
    public function getFechNac() { return $this->fech_nac; }
    public function setFechNac($fech_nac) { $this->fech_nac = $fech_nac; }
    
    public function getFoto() { return $this->foto; }
    public function setFoto($foto) { $this->foto = $foto; }
    
    public function getNumeroCodFi() { return $this->numeroCodFi; }
    public function setNumeroCodFi($numeroCodFi) { $this->numeroCodFi = $numeroCodFi; }
    
    public function getNumUnidad() { return $this->numUnidad; }
    public function setNumUnidad($numUnidad) { $this->numUnidad = $numUnidad; }

    // Database operations
    public function obtenerDatos()
    {
        try {
            $sql = "SELECT * FROM conductores WHERE id_conductor = ?";
            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparing statement: " . $this->conectar->error);
            }

            $stmt->bind_param("i", $this->id_conductor);
            
            if (!$stmt->execute()) {
                throw new Exception("Error executing statement: " . $stmt->error);
            }

            $result = $stmt->get_result();
            if ($fila = $result->fetch_assoc()) {
                foreach ($fila as $key => $value) {
                    if (property_exists($this, $key)) {
                        $this->$key = $value;
                    }
                }
            }

            $stmt->close();
            return true;
        } catch (Exception $e) {
            error_log("Error in Conductor::obtenerDatos(): " . $e->getMessage());
            return false;
        }
    }

    public function buscarConductores($term)
    {
        try {
            $term = "%{$term}%";
            $sql = "SELECT * FROM conductores 
                    WHERE nombres LIKE ? OR apellido_paterno LIKE ? 
                    ORDER BY nombres ASC";
            
            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparing statement: " . $this->conectar->error);
            }

            $stmt->bind_param("ss", $term, $term);
            
            if (!$stmt->execute()) {
                throw new Exception("Error executing statement: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $conductores = [];
            
            while ($row = $result->fetch_assoc()) {
                $conductores[] = $row;
            }

            $stmt->close();
            return $conductores;
        } catch (Exception $e) {
            error_log("Error in Conductor::buscarConductores(): " . $e->getMessage());
            return [];
        }
    }

    public function buscarPorDocumento($nroDocumento) {
        $query = "SELECT id_conductor FROM conductores WHERE nro_documento = ?";
        $stmt = $this->conectar->prepare($query);
        $stmt->bind_param("s", $nroDocumento);
        $stmt->execute();
        $stmt->bind_result($idConductor);
        $stmt->fetch();

        return $idConductor;  // Devuelve el id del conductor si lo encuentra, o null si no
    }

    public function obtenerCumpleaniosHoy()
    {
        $hoy = date('m-d'); // Obtiene el mes y día actual en formato MM-DD
        
        $sql = "SELECT id_conductor, nombres, apellido_paterno, apellido_materno, correo 
                FROM conductores 
                WHERE DATE_FORMAT(fech_nac, '%m-%d') = ? 
                AND correo IS NOT NULL AND correo != ''";
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("s", $hoy);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $cumpleaneros = [];
        
        while ($fila = $resultado->fetch_assoc()) {
            $cumpleaneros[] = $fila;
        }
        
        $stmt->close();
        return $cumpleaneros;
    }

    public function obtenerDatosEdit()
    {
        try {
            $sql = "SELECT * FROM conductores WHERE id_conductor = ?";
            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparing statement: " . $this->conectar->error);
            }

            $stmt->bind_param("i", $this->id_conductor);
            
            if (!$stmt->execute()) {
                throw new Exception("Error executing statement: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
           
            $stmt->close();
            
            if (!$data) { // Check if no data was found
                return null; // Return null instead of false for empty results
            }
    
            return $data; // Return the data directly, no need for toArray()
        } catch (Exception $e) {
            error_log("Error in Conductor::obtenerDatos(): " . $e->getMessage());
            return false;
        }
    }

    public function toArray() // Método agregado
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        
        $array = [];
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $array[$propertyName] = $this->$propertyName;
        }
        
        return $array;
    }

    public function editarFoto($id_conductor, $archivo)
{
    try {
        // Asegurar que el id_conductor está seteado
        if (empty($id_conductor)) {
            error_log("Error en editarFoto: id_conductor no está seteado.");
            return false;
        }

        // Consultar la foto actual en la BD
        $sql = "SELECT foto FROM conductores WHERE id_conductor = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id_conductor); 
        $stmt->execute();
        $stmt->bind_result($fotoActual);
        $stmt->fetch();
        $stmt->close();

        // Si existe una foto anterior, eliminarla
        if (!empty($fotoActual)) {
            $rutaFotoAnterior = 'public/' . $fotoActual; // Ajustar la ruta completa
            if (file_exists($rutaFotoAnterior)) {
                unlink($rutaFotoAnterior); // Eliminación de la foto anterior
            }
        }

        // PROCESO DE GUARDADO DE LA NUEVA FOTO
        $rutaPublica = 'public/fotos/conductores/'; // Definir la ruta pública para las fotos

        // Asegurarse que el directorio existe
        if (!file_exists($rutaPublica)) {
            if (!mkdir($rutaPublica, 0755, true)) {
                error_log("Error al crear el directorio: " . $rutaPublica);
                return false;
            }
        }

        // Generar nombre único para el archivo
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = uniqid('conductor_', true) . '.' . $extension;
        $rutaCompleta = $rutaPublica . $nombreArchivo;

        // Mover el archivo
        if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            error_log("Error al mover el archivo a: " . $rutaCompleta);
            return false;
        }

        // Guardar solo la ruta relativa en la base de datos
        $foto = 'fotos/conductores/' . $nombreArchivo;

        // ACTUALIZAR LA BASE DE DATOS CON LA NUEVA FOTO
        $sqlUpdate = "UPDATE conductores SET foto = ? WHERE id_conductor = ?";
        $stmtUpdate = $this->conectar->prepare($sqlUpdate);
        $stmtUpdate->bind_param("si", $foto, $id_conductor);
        if ($stmtUpdate->execute()) {
            $stmtUpdate->close();
            return true;
        }

        $stmtUpdate->close();

        error_log("Error en editarFoto: No se pudo actualizar la foto en la base de datos.");
        return false;

    } catch (Exception $e) {
        error_log("Error en editarFoto: " . $e->getMessage());
        return false;
    }
}





    public function editar()
    {
        try {
            $sql = "UPDATE conductores SET 
                    tipo_doc = ?, nro_documento = ?, nombres = ?, apellido_paterno = ?, 
                    apellido_materno = ?, nacionalidad = ?, nro_licencia = ?, telefono = ?, 
                    correo = ?, categoria_licencia = ?, fech_nac = ?, numeroCodFi = ?, numUnidad = ?
                    WHERE id_conductor = ?";

            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando la consulta: " . $this->conectar->error);
                throw new Exception('Error al preparar la consulta');
            }

            $stmt->bind_param("ssssssssssssis",
                $this->tipo_doc,
                $this->nro_documento,
                $this->nombres,
                $this->apellido_paterno,
                $this->apellido_materno,
                $this->nacionalidad,
                $this->nro_licencia,
                $this->telefono,
                $this->correo,
                $this->categoria_licencia,
                $this->fech_nac,
                $this->numeroCodFi,
                $this->numUnidad,
                $this->id_conductor
            );

            if (!$stmt->execute()) {
                error_log("Error ejecutando la consulta: " . $stmt->error);
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $stmt->close();
            return $this->id_conductor;

        } catch (Exception $e) {
            error_log("Error en Conductor::editar(): " . $e->getMessage());
            throw $e;
        }
    }

        public function obtenerDatosPago($id_conductor)
    {
        // Consultar si el conductor tiene un registro en la tabla conductor_pago
        $sql = "SELECT * FROM conductor_pago WHERE id_conductor = $id_conductor";
        $resultado = $this->conectar->query($sql);

        if ($resultado->num_rows === 0) {
            return null; // Si no hay datos en conductor_pago, retorna nada
        }

        $datosPago = $resultado->fetch_assoc();

        // Obtener el tipo de pago desde la tabla tipo_pago_conductor
        $sqlTipoPago = "SELECT tipo_pago FROM tipo_pago_conductor WHERE id_tipopago = " . $datosPago['id_tipopago'];
        $resultadoTipoPago = $this->conectar->query($sqlTipoPago);
        $tipoPago = $resultadoTipoPago->fetch_assoc()['tipo_pago'];

        $datosPago['tipo_pago'] = $tipoPago;

        // Si el tipo de pago es "Financiamiento", buscar más detalles en conductor_regfinanciamiento
        if ($tipoPago === 'Financiamiento') {
            $sqlFinanciamiento = "SELECT * FROM conductor_regfinanciamiento WHERE id_conductor = $id_conductor";
            $resultadoFinanciamiento = $this->conectar->query($sqlFinanciamiento);

            if ($resultadoFinanciamiento->num_rows > 0) {
                $datosFinanciamiento = $resultadoFinanciamiento->fetch_assoc();
                $datosPago['financiamiento'] = $datosFinanciamiento;

                // Buscar las cuotas en conductor_cuotas
                $idFinanciamiento = $datosFinanciamiento['idconductor_regfinanciamiento'];
                $sqlCuotas = "SELECT * FROM conductor_cuotas WHERE idconductor_Financiamiento = $idFinanciamiento";
                $resultadoCuotas = $this->conectar->query($sqlCuotas);

                $datosPago['cuotas'] = [];
                while ($fila = $resultadoCuotas->fetch_assoc()) {
                    $datosPago['cuotas'][] = $fila;
                }
            }
        }

        return $datosPago;
    }

    public function eliminarPago($id_conductor) {
        // Verificar si el conductor tiene un registro en la tabla conductor_pago
        $sql = "SELECT id_tipopago FROM conductor_pago WHERE id_conductor = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id_conductor);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_tipopago = $row['id_tipopago'];

            // Si el tipo de pago es 2 (financiado), eliminar también de conductor_regfinanciamiento
            if ($id_tipopago == 2) {
               // Verificar el id_conductor_regfinanciamiento para este conductor
                $sqlFinanciamiento = "SELECT idconductor_regfinanciamiento FROM conductor_regfinanciamiento WHERE id_conductor = ?";
                $stmtFinanciamiento = $this->conectar->prepare($sqlFinanciamiento);
                $stmtFinanciamiento->bind_param("i", $id_conductor);
                $stmtFinanciamiento->execute();
                $resultFinanciamiento = $stmtFinanciamiento->get_result();

                if ($resultFinanciamiento->num_rows > 0) {
                    // Obtener el idconductor_regfinanciamiento
                    $rowFinanciamiento = $resultFinanciamiento->fetch_assoc();
                    $idconductor_regfinanciamiento = $rowFinanciamiento['idconductor_regfinanciamiento'];
    
                    // Verificar en la tabla conductor_cuotas si hay algún registro con estado 'pagado'
                    $sqlCuotas = "SELECT estado_cuota FROM conductor_cuotas WHERE idconductor_Financiamiento = ? AND estado_cuota = 'pagado'";
                    $stmtCuotas = $this->conectar->prepare($sqlCuotas);
                    $stmtCuotas->bind_param("i", $idconductor_regfinanciamiento);
                    $stmtCuotas->execute();
                    $resultCuotas = $stmtCuotas->get_result();
    
                    if ($resultCuotas->num_rows > 0) {
                        // Si alguna cuota está en estado 'pagado', no realizar ninguna eliminación
                        return false; // Detener el método, no eliminar ningún registro
                    }
                }

                // Eliminar el registro de conductor_regfinanciamiento si no se encontró ningún 'pagado'
                $sqlFinanciamientoDelete = "DELETE FROM conductor_regfinanciamiento WHERE id_conductor = ?";
                $stmtFinanciamientoDelete = $this->conectar->prepare($sqlFinanciamientoDelete);
                $stmtFinanciamientoDelete->bind_param("i", $id_conductor);
                $stmtFinanciamientoDelete->execute();
                $stmtFinanciamientoDelete->close();
            }

            // Eliminar el registro de conductor_pago
            $sqlDeletePago = "DELETE FROM conductor_pago WHERE id_conductor = ?";
            $stmtDeletePago = $this->conectar->prepare($sqlDeletePago);
            $stmtDeletePago->bind_param("i", $id_conductor);
            $stmtDeletePago->execute();
            $stmtDeletePago->close();

            return true;
        }

        return false;
    }

    public function getConductorFinanceList($id_conductor)
    {
        $sql = "SELECT * FROM conductores WHERE id_conductor = ?"; // Consulta para obtener los datos del conductor
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param('i', $id_conductor);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Retornar los datos del conductor
    }

    public function eliminarConductor($id_conductor)
    {
        $conn = $this->conectar;

        // Verificar si el conductor tiene financiamiento
        $sql = "SELECT id_conductor FROM financiamiento WHERE id_conductor = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_conductor);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            return ["success" => false, "message" => "Operación no permitida: el conductor cuenta con un financiamiento registrado en el sistema."];
        }
        $stmt->close();

        // Obtener el id_inscripcion relacionado al conductor
        $sql = "SELECT id_inscripcion FROM inscripciones WHERE id_conductor = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_conductor);
        $stmt->execute();
        $stmt->bind_result($id_inscripcion);
        $stmt->fetch();
        $stmt->close();

        if ($id_inscripcion) {

            // Eliminar archivos de los requisitos si existen y cumplen con la restricción de fecha (agregado)
        $sql = "SELECT * FROM requisitos WHERE id_inscripcion = ?";
        $stmt = $conn->prepare($sql); // Preparar la consulta (agregado)
        $stmt->bind_param("i", $id_inscripcion); // Asignar id_inscripcion (agregado)
        $stmt->execute(); // Ejecutar la consulta (agregado)
        $resultadoRequisitos = $stmt->get_result(); // Obtener resultados (agregado)

        if ($resultadoRequisitos->num_rows > 0) { // Si hay registros (agregado)
            $requisito = $resultadoRequisitos->fetch_assoc(); // Obtener la fila de requisitos (agregado)
            $hoy = new DateTime(); // Obtener la fecha actual (agregado)
            $fechaInicioRestriccion = new DateTime('2025-03-26'); // Fecha desde la cual se pueden eliminar archivos (agregado)

            // Eliminar archivos si existen y cumplen con la restricción de fecha (agregado)
            $archivosCampos = [
                'recibo_servicios', 'carta_desvinculacion', 'revision_tecnica', 'soat_doc', 
                'seguro_doc', 'tarjeta_propiedad', 'licencia_doc', 'doc_identidad', 
                'doc_otro1', 'doc_otro2', 'doc_otro3'
            ]; // Lista de campos con rutas de archivos (agregado)

            foreach ($archivosCampos as $campo) { // Iterar sobre los campos de archivos (agregado)
                $rutaArchivo = $requisito[$campo]; // Obtener la ruta del archivo (agregado)
                if ($rutaArchivo && file_exists($rutaArchivo)) { // Si el archivo existe (agregado)
                    $fechaArchivo = new DateTime('@' . filemtime($rutaArchivo)); // Obtener la fecha de modificación del archivo (agregado)
                    if ($fechaArchivo >= $fechaInicioRestriccion) { // Verificar que el archivo sea posterior a la fecha restringida (agregado)
                        unlink($rutaArchivo); // Eliminar el archivo (agregado)
                    }
                }
            }
        }
        $stmt->close(); // Cerrar el statement (agregado)
            // Eliminar de observaciones
            $conn->query("DELETE FROM observaciones WHERE id_inscripcion = $id_inscripcion");
            // Eliminar de kits
            $conn->query("DELETE FROM kits WHERE id_inscripcion = $id_inscripcion");
            // Eliminar de requisitos
            $conn->query("DELETE FROM requisitos WHERE id_inscripcion = $id_inscripcion");
            // Eliminar de inscripciones
            $conn->query("DELETE FROM inscripciones WHERE id_inscripcion = $id_inscripcion");
        }

        // Eliminar de direccion_conductor
        $conn->query("DELETE FROM direccion_conductor WHERE id_conductor = $id_conductor");
        // Eliminar de contacto_emergencia
        $conn->query("DELETE FROM contacto_emergencia WHERE id_conductor = $id_conductor");
        // Eliminar de vehiculos
        $conn->query("DELETE FROM vehiculos WHERE id_conductor = $id_conductor");

        // Manejo de pagos y financiamiento
        $sql = "SELECT id_tipopago FROM conductor_pago WHERE id_conductor = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_conductor);
        $stmt->execute();
        $stmt->bind_result($id_tipopago);
        $stmt->fetch();
        $stmt->close();

        if ($id_tipopago == 1) {
            // Si el pago es tipo 1, eliminar de conductor_pago
            $conn->query("DELETE FROM conductor_pago WHERE id_conductor = $id_conductor");
        } elseif ($id_tipopago == 2) {
            // Si el pago es tipo 2, eliminar de cuotas y financiamiento
            $sql = "SELECT idconductor_regfinanciamiento FROM conductor_regfinanciamiento WHERE id_conductor = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_conductor);
            $stmt->execute();
            $stmt->bind_result($id_financiamiento);
            $stmt->fetch();
            $stmt->close();

            if ($id_financiamiento) {
                $conn->query("DELETE FROM conductor_cuotas WHERE idconductor_Financiamiento = $id_financiamiento");
                $conn->query("DELETE FROM conductor_regfinanciamiento WHERE id_conductor = $id_conductor");
            }
            $conn->query("DELETE FROM conductor_pago WHERE id_conductor = $id_conductor");
        }

        // Finalmente, eliminar el conductor
        $resultado = $conn->query("DELETE FROM conductores WHERE id_conductor = $id_conductor");

        if ($resultado) {
            return ["success" => true, "message" => "Conductor eliminado correctamente."];
        } else {
            return ["success" => false, "message" => "Error al eliminar el conductor."];
        }
    }

    public function obtenerConductoresDataBase() {
        $conductoresData = [];
        $sql = "SELECT 
                    c.id_conductor,
                    c.usuario_id,  
                    c.tipo_doc,
                    c.nro_documento,
                    CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS nombre_completo,
                    c.fech_nac,
                    c.categoria_licencia,
                    c.nro_licencia,
                    c.telefono,
                    c.correo,
                    c.numUnidad
                FROM conductores c";
        
        $result = $this->conectar->query($sql);
        
        if (!$result) {
            die('Error en la consulta de conductores: ' . $this->conectar->error);
        }
        
        while ($row = $result->fetch_assoc()) {
            $idConductor = $row['id_conductor'];
            
            // Obtener Dirección
            $direccionModel = new DireccionConductor();
            $direccion = $direccionModel->obtenerDatosDireccion($idConductor);
            $row['direccion'] = $direccion ? ($direccion['direccion_detalle'] . ', ' . $direccion['distrito'] . ', ' . $direccion['provincia'] . ', ' . $direccion['departamento']) : 'No especificado';
            
            // Obtener Datos del Vehículo
            $sqlVehiculo = "SELECT placa, marca, modelo, anio, color, condicion, vehiculo_flota, fech_soat, fech_seguro 
                FROM vehiculos 
                WHERE id_conductor = $idConductor 
                LIMIT 1";
            $vehiculoRes = $this->conectar->query($sqlVehiculo);
            $vehiculo = $vehiculoRes->fetch_assoc();

            $row['placa'] = $vehiculo['placa'] ?? 'No registrado';
            $row['marca'] = $vehiculo['marca'] ?? 'No registrado';
            $row['modelo'] = $vehiculo['modelo'] ?? 'No registrado';
            $row['anio'] = $vehiculo['anio'] ?? 'No registrado';
            $row['color'] = $vehiculo['color'] ?? 'No registrado';
            $row['condicion'] = $vehiculo['condicion'] ?? 'No especificado';
            $row['vehiculo_flota'] = $vehiculo['vehiculo_flota'] ?? 'No especificado';
            $row['fech_soat'] = $vehiculo['fech_soat'] ?? 'No registrado';
            $row['fech_seguro'] = $vehiculo['fech_seguro'] ?? 'No registrado';
            
            // Obtener Tipo de Servicio
            $sqlServicio = "SELECT setare FROM inscripciones WHERE id_conductor = $idConductor LIMIT 1";
            $servicioRes = $this->conectar->query($sqlServicio);
            $row['tipo_servicio'] = $servicioRes->num_rows > 0 ? $servicioRes->fetch_assoc()['setare'] : 'No registrado';
            
            // Obtener Datos de Pago
            $datosPago = $this->obtenerDatosPago($idConductor);
            
            $row['tipo_pago'] = $datosPago['tipo_pago'] ?? 'No';
            $row['monto_pago'] = isset($datosPago['monto_pago']) ? number_format($datosPago['monto_pago'], 2) : '0.00';
            
            // Obtener Observaciones
            $sqlInscripcion = "SELECT id_inscripcion FROM inscripciones WHERE id_conductor = $idConductor LIMIT 1";
            $inscripcionRes = $this->conectar->query($sqlInscripcion);
            $idInscripcion = $inscripcionRes->num_rows > 0 ? $inscripcionRes->fetch_assoc()['id_inscripcion'] : null;
            
            if ($idInscripcion) {
                $sqlObservaciones = "SELECT descripcion FROM observaciones WHERE id_inscripcion = $idInscripcion LIMIT 1";
                $observacionesRes = $this->conectar->query($sqlObservaciones);
                $row['observaciones'] = $observacionesRes->num_rows > 0 ? $observacionesRes->fetch_assoc()['descripcion'] : 'No hay observaciones';
            } else {
                $row['observaciones'] = 'No hay observaciones';
            }
            
            $conductoresData[] = $row;
        }
        
        return $conductoresData;
    }    

    public function obtenerNumUnidad() {
        // Consultar todos los números de unidad existentes
        $query = "SELECT numUnidad FROM conductores WHERE numUnidad IS NOT NULL ORDER BY numUnidad ASC";
        $resultado = $this->conectar->query($query);
        
        if (!$resultado) {
            die('Error en la consulta: ' . $this->conectar->error);
        }
        
        // Crear un array con los números de unidad existentes
        $numerosExistentes = [];
        while ($fila = $resultado->fetch_assoc()) {
            $numerosExistentes[] = (int)$fila['numUnidad'];
        }
        
        // Si no hay números, el primero disponible es 1
        if (count($numerosExistentes) === 0) {
            return 1;
        }
        
        // Encontrar el primer número faltante
        $numeroLibre = 1; // Empezamos desde 1
        
        foreach ($numerosExistentes as $numero) {
            if ($numero === $numeroLibre) {
                // El número actual está ocupado, incrementamos para buscar el siguiente
                $numeroLibre++;
            } elseif ($numero > $numeroLibre) {
                // Encontramos un hueco, este es el número libre
                return $numeroLibre;
            }
        }
        
        // Si llegamos aquí, todos los números están ocupados secuencialmente
        // El siguiente número libre será el último + 1
        return $numeroLibre;
    }

    // NUEVA FUNCIÓN EN EL MODELO
    // Agrega esta función nueva al final del archivo Modelo/Conductor.php (después de la función obtenerNumUnidad() existente):

    public function obtenerNumUnidadPorTipo($tipoVehiculo) {
        // Consultar números de unidad existentes para el tipo de vehículo específico
        $query = "SELECT c.numUnidad 
                FROM conductores c 
                INNER JOIN vehiculos v ON c.id_conductor = v.id_conductor 
                WHERE v.tipo_vehiculo = ? AND c.numUnidad IS NOT NULL 
                ORDER BY c.numUnidad ASC";
                
        $stmt = $this->conectar->prepare($query);
        $stmt->bind_param("s", $tipoVehiculo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if (!$resultado) {
            die('Error en la consulta: ' . $this->conectar->error);
        }
        
        // Crear un array con los números de unidad existentes
        $numerosExistentes = [];
        while ($fila = $resultado->fetch_assoc()) {
            $numerosExistentes[] = (int)$fila['numUnidad'];
        }
        
        // Si no hay números, el primero disponible es 1
        if (count($numerosExistentes) === 0) {
            return 1;
        }
        
        // Encontrar el primer número faltante
        $numeroLibre = 1; // Empezamos desde 1
        
        foreach ($numerosExistentes as $numero) {
            if ($numero === $numeroLibre) {
                // El número actual está ocupado, incrementamos para buscar el siguiente
                $numeroLibre++;
            } elseif ($numero > $numeroLibre) {
                // Encontramos un hueco, este es el número libre
                return $numeroLibre;
            }
        }
        
        // Si llegamos aquí, todos los números están ocupados secuencialmente
        // El siguiente número libre será el último + 1
        return $numeroLibre;
    }
    
    public function obtenerNumUnidadLimaPorTipo($tipoVehiculo) {
        // Consultar números de unidad existentes para conductores de Lima y tipo de vehículo específico
        $query = "SELECT c.numUnidad 
                FROM conductores c 
                INNER JOIN vehiculos v ON c.id_conductor = v.id_conductor 
                INNER JOIN direccion_conductor d ON c.id_conductor = d.id_conductor
                WHERE v.tipo_vehiculo = ? AND d.departamento = '19' AND c.numUnidad IS NOT NULL 
                ORDER BY c.numUnidad ASC";
                
        $stmt = $this->conectar->prepare($query);
        $stmt->bind_param("s", $tipoVehiculo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if (!$resultado) {
            die('Error en la consulta: ' . $this->conectar->error);
        }
        
        // Crear un array con los números de unidad existentes
        $numerosExistentes = [];
        while ($fila = $resultado->fetch_assoc()) {
            $numerosExistentes[] = (int)$fila['numUnidad'];
        }
        
        // Si no hay números, el primero disponible es 1
        if (count($numerosExistentes) === 0) {
            return 1;
        }
        
        // Encontrar el primer número faltante
        $numeroLibre = 1; // Empezamos desde 1
        
        foreach ($numerosExistentes as $numero) {
            if ($numero === $numeroLibre) {
                // El número actual está ocupado, incrementamos para buscar el siguiente
                $numeroLibre++;
            } elseif ($numero > $numeroLibre) {
                // Encontramos un hueco, este es el número libre
                return $numeroLibre;
            }
        }
        
        // Si llegamos aquí, todos los números están ocupados secuencialmente
        // El siguiente número libre será el último + 1
        return $numeroLibre;
    }
}   

?>