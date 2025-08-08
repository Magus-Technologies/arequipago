<?php

class Cliente
{
    private $id_cliente;
    private $documento;
    private $datos;
    private $direccion;
    private $direccion2;
    private $id_empresa;
    private $telefono;
    private $telefono2;
    private $email;
    private $total_venta;
    private $ultima_venta;
    private $conectar;
    private $sql;
    private $sql_error;
    /**
     * Cliente constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getDireccion2()
    {
        return $this->direccion2;
    }

    /**
     * @param mixed $direccion2
     */
    public function setDireccion2($direccion2): void
    {
        $this->direccion2 = $direccion2;
    }

    /**
     * @return mixed
     */
    public function getTelefono2()
    {
        return $this->telefono2;
    }

    /**
     * @param mixed $telefono2
     */
    public function setTelefono2($telefono2): void
    {
        $this->telefono2 = $telefono2;
    }



    /**
     * @return mixed
     */
    public function getIdCliente()
    {
        return $this->id_cliente;
    }

    /**
     * @param mixed $id_cliente
     */
    public function setIdCliente($id_cliente)
    {
        $this->id_cliente = $id_cliente;
    }

    /**
     * @return mixed
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    /**
     * @param mixed $documento
     */
    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    /**
     * @return mixed
     */
    public function getDatos()
    {
        return $this->datos;
    }

    /**
     * @param mixed $datos
     */
    public function setTelefono($telefono)
    {
        $this->telefono = strtoupper($telefono);
    }
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * @param mixed $datos
     */
    public function setEmail($email)
    {
        $this->email = strtoupper($email);
    }
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $datos
     */
    public function setDatos($datos)
    {
        $this->datos = strtoupper($datos);
    }

    /**
     * @return mixed
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * @param mixed $direccion
     */
    public function setDireccion($direccion)
    {
        $this->direccion = strtoupper($direccion);
    }

    /**
     * @return mixed
     */
    public function getIdEmpresa()
    {
        return $this->id_empresa;
    }

    /**
     * @param mixed $id_empresa
     */
    public function setIdEmpresa($id_empresa)
    {
        $this->id_empresa = $id_empresa;
    }

    /**
     * @return mixed
     */
    public function getTotalVenta()
    {
        return $this->total_venta;
    }

    /**
     * @param mixed $total_venta
     */
    public function setTotalVenta($total_venta)
    {
        $this->total_venta = $total_venta;
    }

    /**
     * @return mixed
     */
    public function getUltimaVenta()
    {
        return $this->ultima_venta;
    }

    /**
     * @param mixed $ultima_venta
     */
    public function setUltimaVenta($ultima_venta)
    {
        $this->ultima_venta = $ultima_venta;
    }
    public function getSql()
    {
        return $this->sql;
    }
    
    public function setSql($sql)
    {
        $this->sql = $sql;
    }
    
    public function getSqlError()
    {
        return $this->sql_error;
    }
    
    public function setSqlError($sql_error)
    {
        $this->sql_error = $sql_error;
    }
    public function insertar()
    {
        // First get the next available ID
        $sql = "SELECT IFNULL(MAX(id_cliente) + 1, 1) as next_id FROM clientes";
        $result = $this->conectar->query($sql);
        if ($row = $result->fetch_assoc()) {
            $next_id = $row['next_id'];
        } else {
            $next_id = 1;
        }
        
        // Now use the next_id in the insert
        $this->sql = "INSERT INTO clientes VALUES (
            $next_id,
            '$this->documento',
            '$this->datos',
            '$this->direccion',
            '$this->direccion2',
            '$this->telefono',
            '$this->telefono2',
            '$this->email',
            {$_SESSION['id_empresa']},
            '1000-01-01',
            '0'
        )";
        
        $result = $this->conectar->query($this->sql);
    
        if (!$result) {
            $this->sql_error = $this->conectar->error;
            error_log("Error inserting client: " . $this->sql_error . "\nSQL: " . $this->sql);
        } else {
            $this->id_cliente = $next_id; // Set the ID that we used
        }
        
        return $result;
    }

public function modificar($documento, $datos, $id_cliente)
{
    $this->sql = "update clientes 
    set documento = '$documento', datos = '$datos' 
    where id_cliente = '$id_cliente'";
    
    $result = $this->conectar->query($this->sql);
    
    if (!$result) {
        $this->sql_error = $this->conectar->error;
    } else {
        $this->id_cliente = $this->conectar->insert_id;
    }
    
    return $result;
}

    public function obtenerId()
    {
        $sql = "select ifnull(max(id_cliente) + 1, 1) as codigo from clientes";
        $this->id_cliente = $this->conectar->get_valor_query($sql, 'codigo');
    }

    public function obtenerPorCliente($id_cliente)
    {
        try {
            $sql = "SELECT codigo_asociado, grupo_financiamiento, estado
                    FROM financiamiento
                    WHERE id_cliente = ?";
            
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_cliente);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $financiamientos = [];
            while ($row = $result->fetch_assoc()) {
                $financiamientos[] = $row;
            }
            
            return $financiamientos;
        } catch (Exception $e) {
            error_log("Error en Financiamiento::obtenerPorCliente(): " . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerDetalleCliente($id_cliente)
    {
        try {
            $sql = "SELECT * FROM clientes WHERE id_cliente = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_cliente);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (Exception $e) {
            return ['error' => 'Error al obtener el cliente: ' . $e->getMessage()];
        }
    }

    public function obtenerTodosClientes($pagina = 1, $cantidadPorPagina = 12)  // Cambiar de 20 a 12
    {
        try {
            // Calcular el desplazamiento (offset) basado en la página y la cantidad por página
            $offset = ($pagina - 1) * $cantidadPorPagina;
            
            // Consulta para obtener los clientes y sus datos de financiamiento
            $sql = "SELECT c.id_cliente, c.documento, c.datos, c.direccion, c.id_empresa, c.telefono, 
                    f.codigo_asociado, f.grupo_financiamiento, f.estado 
                    FROM clientes c
                    LEFT JOIN financiamiento f ON c.id_cliente = f.id_cliente
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("ii", $cantidadPorPagina, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $clientes = [];
            while ($row = $result->fetch_assoc()) {
                $clientes[] = $row;
            }

            return $clientes;
        } catch (Exception $e) {
            error_log("Error en Cliente::obtenerTodosClientes(): " . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerTotalClientes()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM clientes";
            $result = $this->conectar->query($sql);
            $row = $result->fetch_assoc();
            return $row['total'];
        } catch (Exception $e) {
            error_log("Error en Cliente::obtenerTotalClientes(): " . $e->getMessage());
            throw $e;
        }
    }

    
    public function obtenerDatos()
    {
        $sql = "select * 
        from clientes 
        where id_cliente = '$this->id_cliente'";
        $fila = $this->conectar->query($sql)->fetch_assoc();
        $this->documento = $fila['documento'];
        $this->datos = $fila['datos'];
        $this->direccion = $fila['direccion'];
        $this->id_empresa = $fila['id_empresa'];
        $this->ultima_venta = $fila['ultima_venta'];
        $this->total_venta = $fila['total_venta'];
    }

    // public function verificarDocumento()
    // {
    //     $sql = "select *
    //     from clientes 
    //     where documento = '$this->documento' and id_empresa = '$this->id_empresa'";
    //     $result = $this->conectar->query($sql);
    //     if ($row = $result->fetch_assoc()) {
    //         $this->id_cliente = $row['id_cliente'];
    //         $this->datos = $row['datos'];
    //         $this->documento = $row['documento'];
    //         $this->email = $row['email'];
    //         $this->telefono = $row['telefono'];
    //         return true;
    //     }
    //     return false;
    // }
    public function verificarDocumento()
{
    $this->sql = "SELECT * FROM clientes 
                  WHERE documento = '$this->documento' 
                  AND id_empresa = '$this->id_empresa'";
    
    $result = $this->conectar->query($this->sql);
    
    if ($row = $result->fetch_assoc()) {
        $this->id_cliente = $row['id_cliente'];
        $this->datos = $row['datos'];
        $this->documento = $row['documento'];
        $this->email = $row['email'];
        $this->telefono = $row['telefono'];
        $this->direccion = $row['direccion'];
        $this->direccion2 = $row['direccion2'];
        return true;
    }
    return false;
}

    public function verFilas()
    {
        $sql = "select * from clientes where id_empresa = '$this->id_empresa'";
        return $this->conectar->query($sql);
    }

    /*public function buscarClientes($termino)
    {
        $sql = "select * from clientes 
        where id_empresa = '$this->id_empresa' and (datos like '%$termino%' or documento like '%$termino%') 
        order by datos asc";
        return $this->conectar->query($sql);
    }*/
    public function idLast()
    {

        try {
            $sql = "SELECT id_cliente,documento,datos,direccion,telefono,email,ultima_venta,total_venta FROM clientes  ORDER BY id_cliente DESC LIMIT 1";
            $fila = $this->conectar->query($sql)->fetch_object();
            return $fila;
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }
    public function getAllData()
    {
        try {
            $sql = "SELECT id_cliente,documento,datos,email,telefono,ultima_venta,total_venta FROM clientes where id_empresa='{$_SESSION['id_empresa']}'";
            $fila = mysqli_query($this->conectar, $sql);
            return mysqli_fetch_all($fila, MYSQLI_ASSOC);
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }
    public function getOne($id)
    {
        try {
            $sql = "SELECT * FROM clientes WHERE id_cliente = '$id' ";
            $fila = mysqli_query($this->conectar, $sql);
            return mysqli_fetch_all($fila, MYSQLI_ASSOC);
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }
    public function cuentasCobrar()
    {
        try {
            $sql = "SELECT ventas.id_venta,ventas.fecha_emision,ventas.fecha_vencimiento,c.datos,dv.estado,dv.dias_venta_id FROM ventas LEFT JOIN dias_ventas AS dv ON
            ventas.id_venta=dv.id_venta 
            LEFT JOIN clientes AS c ON 
            ventas.id_cliente = c.id_cliente 
            WHERE ventas.id_tipo_pago = 2";
            $fila = mysqli_query($this->conectar, $sql);
            return mysqli_fetch_all($fila, MYSQLI_ASSOC);
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }
    public function cuentasCobrarEstado($id)
    {
        try {
            $sql = "UPDATE dias_ventas set estado = 0 WHERE dias_venta_id = $id";
            $result =  $this->conectar->query($sql);
            return $result;
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }
    public function editar($id)
    {
        $sql = "UPDATE clientes SET datos ='$this->datos',documento ='$this->documento',direccion ='$this->direccion',direccion2 ='$this->direccion2',telefono ='$this->telefono',telefono2 ='$this->telefono2',email='$this->email' WHERE id_cliente = $id";
        $result =  $this->conectar->query($sql);
        return $result;
    }
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM clientes WHERE  id_cliente = '$id' ";
            $fila = mysqli_query($this->conectar, $sql);
            return $fila;
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }

    public function buscarPorDocumento($documento)
    {
        $sql = "SELECT * FROM clientes WHERE documento = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("s", $documento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado && $resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        } else {
            return null;
        }
    }

    public function buscarClienteFinanciar($documento)
    {
        $sql = "SELECT * FROM clientes_financiar WHERE n_documento = ?";   // MODIFICADO: Cambiado nombre de tabla y campo
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("s", $documento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado && $resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        } else {
            return null;
        }
    }

    /**
     * Inserta un nuevo cliente en la base de datos
     * @param string $documento Número de documento
     * @param string $datos Nombre completo
     * @param string $email Correo electrónico (opcional)
     * @param string $telefono Número de teléfono (opcional)
     * @param string $direccion Dirección del cliente (opcional)
     * @return int|false ID del cliente insertado o false en caso de error
     */
    public function insertarCliente($documento, $datos, $email = '', $telefono = '', $direccion = '', $id_empresa = 12)
{
    // Obtener el último ID de cliente
    $queryUltimoID = "SELECT MAX(id_cliente) AS ultimo_id FROM clientes";
    $resultado = $this->conectar->query($queryUltimoID);

    if ($resultado && $fila = $resultado->fetch_assoc()) {
        $nuevo_id = $fila['ultimo_id'] + 1;
    } else {
        $nuevo_id = 1; // Si no hay registros, empezamos desde 1
    }

    // Preparar la consulta SQL
    $sql = "INSERT INTO clientes (id_cliente, documento, datos, email, telefono, direccion, id_empresa) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $this->conectar->prepare($sql);

    if (!$stmt) {
        die("Error al preparar la consulta: " . $this->conectar->error);
    }

    if (!$stmt->bind_param("isssssi", $nuevo_id, $documento, $datos, $email, $telefono, $direccion, $id_empresa)) {
        die("Error en bind_param: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        die("Error al ejecutar: " . $stmt->error);
    }

    if ($stmt->affected_rows > 0) {
        return $nuevo_id;
    } else {
        return false;
    }
}

public function documentoExistente($numeroDocumento)
{
    $query = "SELECT COUNT(*) as total FROM clientes_financiar WHERE n_documento = ?";
    $stmt = $this->conectar->prepare($query);
    $stmt->bind_param("s", $numeroDocumento);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return ($row['total'] > 0);
}

/**
 * Guarda los datos de un nuevo cliente en la base de datos
 * 
 * @param array $datos Datos del cliente a guardar
 * @return bool Retorna true si se guardó correctamente, false en caso contrario
 */
public function guardarCliente($datos) 
{ 
   try { 
       // Iniciar transacción 
       $this->conectar->begin_transaction(); 
        
       // Insertar datos personales y dirección 
       $query = "INSERT INTO clientes_financiar ( 
           tipo_doc, n_documento, nombres, apellido_paterno, apellido_materno,  
           num_cod_finan, nacionalidad, fecha_nacimiento, telefono, correo,  
           departamento, provincia, distrito, direccion_detallada,  
           emergencia_nombre, emergencia_telefono, emergencia_parentesco, 
           laboral_nombre, laboral_telefono, laboral_puesto, laboral_empresa, 
           recibo_servicios, doc_identidad, otro_doc_1, otro_doc_2, otro_doc_3, 
           comentarios, fecha_registro, password 
       ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)"; 
        
       $stmt = $this->conectar->prepare($query); 
       $passwordDefault = null; // MODIFICADO: cambiar de password_hash a null 
       $stmt->bind_param( 
           "ssssssssssssssssssssssssssss", 
           $datos['tipo_doc'], 
           $datos['n_documento'], 
           $datos['nombres'], 
           $datos['apellido_paterno'], 
           $datos['apellido_materno'], 
           $datos['num_cod_finan'], 
           $datos['nacionalidad'], 
           $datos['fecha_nacimiento'], 
           $datos['telefono'], 
           $datos['correo'], 
           $datos['departamento'], 
           $datos['provincia'], 
           $datos['distrito'], 
           $datos['direccion_detallada'], 
           $datos['emergencia_nombre'], 
           $datos['emergencia_telefono'], 
           $datos['emergencia_parentesco'], 
           $datos['laboral_nombre'], 
           $datos['laboral_telefono'], 
           $datos['laboral_puesto'], 
           $datos['laboral_empresa'], 
           $datos['recibo_servicios'], 
           $datos['doc_identidad'], 
           $datos['otro_doc_1'], 
           $datos['otro_doc_2'], 
           $datos['otro_doc_3'], 
           $datos['comentarios'], 
           $passwordDefault 
       ); 
        
       $stmt->execute(); 
        
       // Confirmar transacción 
       $this->conectar->commit(); 
        
       return true; 
   } catch (Exception $e) { 
       // Revertir transacción en caso de error 
       $this->conectar->rollback(); 
        
       // Registrar error en logs 
       error_log("Error al guardar cliente: " . $e->getMessage()); 
        
       return false; 
   } 
}


/**
 * Obtiene los datos de un cliente por su número de documento
 * 
 * @param string $numeroDocumento Número de documento del cliente
 * @return array|false Datos del cliente o false si no se encuentra
 */
public function obtenerClientePorDocumento($numeroDocumento)
{
    $query = "SELECT * FROM clientes WHERE n_documento = ? LIMIT 1";
    $stmt = $this->conectar->prepare($query);
    $stmt->bind_param("s", $numeroDocumento);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return false;
}

/**
 * Obtiene una lista de clientes
 * 
 * @param int $limit Límite de registros a retornar
 * @param int $offset Posición inicial para la consulta
 * @return array Lista de clientes
 */
public function listarClientes($limit = 10, $offset = 0)
{
    $query = "SELECT id, tipo_doc, n_documento, CONCAT(nombres, ' ', apellido_paterno, ' ', apellido_materno) as nombre_completo, 
             telefono, correo, fecha_registro 
             FROM clientes 
             ORDER BY fecha_registro DESC 
             LIMIT ?, ?";
             
    $stmt = $this->conectar->prepare($query);
    $stmt->bind_param("ii", $offset, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $clientes = [];
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }
    
    return $clientes;
}


public function obtenerClientes($inicio, $registrosPorPagina, $busqueda = "")
{
    $condicion = "";
    if (!empty($busqueda)) {
        $busqueda = "%$busqueda%";
        $condicion = "WHERE nombres LIKE ? OR apellido_paterno LIKE ? OR apellido_materno LIKE ? 
                     OR n_documento LIKE ? OR correo LIKE ? OR telefono LIKE ? OR num_cod_finan LIKE ?";
    }
    
    $query = "SELECT * FROM clientes_financiar $condicion ORDER BY id DESC LIMIT ?, ?";
    $stmt = $this->conectar->prepare($query);
    
    if (!empty($busqueda)) {
        $stmt->bind_param("sssssssii", $busqueda, $busqueda, $busqueda, $busqueda, $busqueda, $busqueda, $busqueda, $inicio, $registrosPorPagina);
    } else {
        $stmt->bind_param("ii", $inicio, $registrosPorPagina);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $clientes = [];
    
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }
    
    return $clientes;
}

/**
 * Obtiene el total de clientes para la paginación
 * 
 * @param string $busqueda Término de búsqueda
 * @return int Total de clientes
 */
public function totalClientes($busqueda = "")
{
    $condicion = "";
    if (!empty($busqueda)) {
        $busqueda = "%$busqueda%";
        $condicion = "WHERE nombres LIKE ? OR apellido_paterno LIKE ? OR apellido_materno LIKE ? 
                     OR n_documento LIKE ? OR correo LIKE ? OR telefono LIKE ? OR num_cod_finan LIKE ?";
    }
    
    $query = "SELECT COUNT(*) as total FROM clientes_financiar $condicion";
    $stmt = $this->conectar->prepare($query);
    
    if (!empty($busqueda)) {
        $stmt->bind_param("sssssss", $busqueda, $busqueda, $busqueda, $busqueda, $busqueda, $busqueda, $busqueda);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total'];
}

/**
 * Obtiene los datos de un cliente específico
 * 
 * @param int $id ID del cliente
 * @return array Datos del cliente
 */
public function obtenerCliente($id)
{
    $query = "SELECT c.*, 
              d.nombre as departamento_nombre, 
              p.nombre as provincia_nombre, 
              dt.nombre as distrito_nombre 
              FROM clientes_financiar c
              LEFT JOIN depast d ON c.departamento = d.iddepast
              LEFT JOIN provincet p ON c.provincia = p.idprovincet
              LEFT JOIN distritot dt ON c.distrito = dt.iddistritot
              WHERE c.id = ?";
    $stmt = $this->conectar->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

/**
 * Actualiza los datos de un cliente
 * 
 * @param array $datos Datos del cliente a actualizar
 * @return bool Resultado de la operación
 */
public function actualizarCliente($datos)
{
    $query = "UPDATE clientes_financiar SET 
              tipo_doc = ?, n_documento = ?, nombres = ?, 
              apellido_paterno = ?, apellido_materno = ?, 
              nacionalidad = ?, fecha_nacimiento = ?, 
              telefono = ?, correo = ?, 
              departamento = ?, provincia = ?, distrito = ?, 
              direccion_detallada = ?, emergencia_nombre = ?, 
              emergencia_telefono = ?, emergencia_parentesco = ?, 
              laboral_nombre = ?, laboral_telefono = ?, 
              laboral_puesto = ?, laboral_empresa = ?, 
              recibo_servicios = ?, doc_identidad = ?, 
              otro_doc_1 = ?, otro_doc_2 = ?, otro_doc_3 = ?, 
              comentarios = ?, fecha_actualizacion = NOW() 
              WHERE id = ?";
              
    $stmt = $this->conectar->prepare($query);
    
    // Corregido: la cadena de tipos tenía menos tipos que variables a vincular
    $stmt->bind_param(
        "ssssssssssssssssssssssssssi", // <- Corregido: Se añadió un 's' adicional para distrito y se verificó el total
        $datos['tipo_doc'],
        $datos['n_documento'],
        $datos['nombres'],
        $datos['apellido_paterno'],
        $datos['apellido_materno'],
        $datos['nacionalidad'],
        $datos['fecha_nacimiento'],
        $datos['telefono'],
        $datos['correo'],
        $datos['departamento'],
        $datos['provincia'],
        $datos['distrito'],
        $datos['direccion_detallada'],
        $datos['emergencia_nombre'],
        $datos['emergencia_telefono'],
        $datos['emergencia_parentesco'],
        $datos['laboral_nombre'],
        $datos['laboral_telefono'],
        $datos['laboral_puesto'],
        $datos['laboral_empresa'],
        $datos['recibo_servicios'],
        $datos['doc_identidad'],
        $datos['otro_doc_1'],
        $datos['otro_doc_2'],
        $datos['otro_doc_3'],
        $datos['comentarios'],
        $datos['id']
    );
    
    return $stmt->execute();
}
/**
 * Elimina un cliente y sus archivos
 * 
 * @param int $id ID del cliente
 * @return bool Resultado de la operación
 */
public function eliminarCliente($id)
{
    // Primero obtenemos la información del cliente para eliminar los archivos
    $cliente = $this->obtenerCliente($id);
    
    if ($cliente) {
        // Eliminamos los archivos asociados
        $this->eliminarArchivosCliente($cliente);
        
        // Eliminamos el registro de la base de datos
        $query = "DELETE FROM clientes_financiar WHERE id = ?";
        $stmt = $this->conectar->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    return false;
}

/**
 * Elimina los archivos asociados a un cliente
 * 
 * @param array $cliente Datos del cliente
 */
private function eliminarArchivosCliente($cliente)
{
    $campos = ['recibo_servicios', 'doc_identidad', 'otro_doc_1', 'otro_doc_2', 'otro_doc_3'];
    
    foreach ($campos as $campo) {
        if (!empty($cliente[$campo])) {
            $rutaArchivo = $cliente[$campo];
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo);
            }
        }
    }
}

public function verEditarCliente($id)
{
    try {
        // Consulta principal para obtener los datos del cliente
        $sql = "SELECT c.*, 
                d.nombre AS nombre_departamento,
                p.nombre AS nombre_provincia,
                dt.nombre AS nombre_distrito,
                CONCAT(d.nombre, ', ', p.nombre, ', ', dt.nombre, ', ', c.direccion_detallada) AS direccion_completa
               FROM clientes_financiar c
               LEFT JOIN depast d ON c.departamento = d.iddepast
               LEFT JOIN provincet p ON c.provincia = p.idprovincet
               LEFT JOIN distritot dt ON c.distrito = dt.iddistritot
               WHERE c.id = ?";
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            $cliente = $resultado->fetch_assoc();
            
            // Asegurar que tenemos los nombres de ubicación, no solo IDs
            $cliente['departamento'] = $cliente['departamento'] ?? '';
            $cliente['provincia'] = $cliente['provincia'] ?? '';
            $cliente['distrito'] = $cliente['distrito'] ?? '';
            
            return $cliente;
        } else {
            return null;
        }
    } catch (Exception $e) {
        error_log("Error en verEditarCliente: " . $e->getMessage());
        return null;
    }
}


public function obtenerDepartamentos()
    {
        $query = "SELECT * FROM depast ORDER BY nombre";
        $result = $this->conectar->query($query);
        $departamentos = [];
        
        while ($row = $result->fetch_assoc()) {
            $departamentos[] = $row;
        }
        
        return $departamentos;
    }
    
    /**
     * Obtiene las provincias de un departamento
     * 
     * @param int $idDepartamento ID del departamento
     * @return array Lista de provincias
     */
    public function obtenerProvincias($idDepartamento)
    {
        $query = "SELECT * FROM provincet WHERE iddepast = ? ORDER BY nombre";
        $stmt = $this->conectar->prepare($query);
        $stmt->bind_param("i", $idDepartamento);
        $stmt->execute();
        $result = $stmt->get_result();
        $provincias = [];
        
        while ($row = $result->fetch_assoc()) {
            $provincias[] = $row;
        }
        
        return $provincias;
    }
    
    /**
     * Obtiene los distritos de una provincia
     * 
     * @param int $idProvincia ID de la provincia
     * @return array Lista de distritos
     */
    public function obtenerDistritos($idProvincia)
    {
        $query = "SELECT * FROM distritot WHERE idprovincet = ? ORDER BY nombre";
        $stmt = $this->conectar->prepare($query);
        $stmt->bind_param("i", $idProvincia);
        $stmt->execute();
        $result = $stmt->get_result();
        $distritos = [];
        
        while ($row = $result->fetch_assoc()) {
            $distritos[] = $row;
        }
        
        return $distritos;
    }

    public function obtenerPorDni($dni) {
        // Preparar la consulta con parámetros para prevenir SQL injection
        $sql = "SELECT * FROM clientes_financiar WHERE n_documento = ?";
        $stmt = $this->conectar->prepare($sql);
        
        // Vincular parámetros
        $stmt->bind_param('s', $dni);
        
        // Ejecutar la consulta
        $stmt->execute();
        
        // Obtener el resultado
        $result = $stmt->get_result();
        
        // Si hay un resultado, devolverlo como array asociativo
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        // Si no hay resultados, devolver null
        return null;
    }

    public function getClienteList($id_cliente) {
        // CAMBIO: Nombre de tabla corregido a 'clientes_financiar'
        $sql = "SELECT * FROM clientes_financiar WHERE id = ?"; // 🛠 CAMBIO: se corrigió el nombre de la tabla
    
        $stmt = $this->conectar->prepare($sql);
    
        if (!$stmt) {
            // 🛠 CAMBIO: validación agregada para mostrar error si prepare() falla
            die("Error al preparar la consulta: " . $this->conectar->error); // 🛠 CAMBIO: mensaje de error claro
        }
    
        $stmt->bind_param('i', $id_cliente);
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        return $result->fetch_assoc();
    }
    
    public function obtenerDatosDireccionCliente($id_cliente) {
        $sql = "SELECT 
                    dep.nombre AS nombre_departamento, 
                    prov.nombre AS nombre_provincia, 
                    dist.nombre AS nombre_distrito,
                    dc.direccion_detallada
                FROM clientes_financiar dc
                LEFT JOIN depast dep ON dc.departamento = dep.iddepast
                LEFT JOIN provincet prov ON dc.provincia = prov.idprovincet
                LEFT JOIN distritot dist ON dc.distrito = dist.iddistritot
                WHERE dc.id = ?";
                
        $stmt = $this->conectar->prepare($sql);
    
        if (!$stmt) {
            die('Error al preparar la consulta dirección cliente: ' . $this->conectar->error);
        }
    
        $stmt->bind_param('i', $id_cliente);
        $stmt->execute();
        $result = $stmt->get_result();
        $direccion = $result->fetch_assoc();
    
        if ($direccion) {
            // ✅ MODIFICADO: ahora devolvemos directamente el array sin anidar en "direccion"
            return [
                "departamento" => $direccion['nombre_departamento'] ?? '',
                "provincia" => $direccion['nombre_provincia'] ?? '',
                "distrito" => $direccion['nombre_distrito'] ?? '',
                "direccion_detalle" => $direccion['direccion_detallada'] ?? ''
            ]; // <-- FIN MODIFICACIÓN
        }
    
        // ✅ MODIFICADO: devolver claves vacías directamente también sin anidar
        return [
            "departamento" => '',
            "provincia" => '',
            "distrito" => '',
            "direccion_detalle" => ''
        ]; // <-- FIN MODIFICACIÓN
    }  

    public function getClienteById($id) {
        $sql = "SELECT * FROM clientes_financiar WHERE id = ?";  // ✅ campo correcto
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            die('Error al preparar la consulta cliente: ' . $this->conectar->error);
        }
        
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        // Verificamos si el resultado es null
        if ($result === null) {
            return []; // Retornamos un array vacío si no se encuentra el cliente
        }
        
        return $result;
    }
    
}
