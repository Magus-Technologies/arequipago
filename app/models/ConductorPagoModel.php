<?php

class ConductorPagoModel
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function existeRegistro($id_conductor)
    {
        $query = "SELECT COUNT(*) as count FROM conductor_pago WHERE id_conductor = ?";
        $stmt = $this->conectar->prepare($query);
        $stmt->bind_param("i", $id_conductor);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }
    
    public function registrarPago($id_conductor, $tipo_pago, $fecha_pago, $monto_pago)
    {
        $id_tipopago = ($tipo_pago == 'contado') ? 1 : 2;
        
        // Obtener el usuario_id de la sesión
        $usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
        
        $query = "INSERT INTO conductor_pago (id_conductor, id_tipopago, fecha_pago, monto_pago, usuario_registro) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conectar->prepare($query);
        $stmt->bind_param("iisdi", $id_conductor, $id_tipopago, $fecha_pago, $monto_pago, $usuario_id);
        
        if ($stmt->execute()) {
            return $this->conectar->insert_id;
        }
        return false;
    }

    public function obtenerTipoPago($idConductor) {
        try {
            $sql = "SELECT id_tipopago 
                    FROM conductor_pago 
                    WHERE id_conductor = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $idConductor);
            $stmt->execute();
            $result = $stmt->get_result();
    
            return $result->fetch_assoc()['id_tipopago'] ?? null;
        } catch (Exception $e) {
            return null;
        }
    }

    
    public function obtenerPagosPorConductor($idConductor) {
        $datos = []; // Inicializar un array vacío para almacenar los resultados
    
        $sql = "SELECT * FROM conductor_pago WHERE id_conductor = ?"; // Consulta SQL para obtener los pagos del conductor
        $stmt = $this->conectar->prepare($sql); // Preparar la consulta
        $stmt->bind_param("i", $idConductor); // Vincular el parámetro ID del conductor como entero
        $stmt->execute(); // Ejecutar la consulta
    
        $resultado = $stmt->get_result(); // Obtener el resultado de la consulta
    
        while ($fila = $resultado->fetch_assoc()) { // Recorrer cada fila del resultado
            $datos[] = $fila; // Almacenar cada fila en el array de datos
        }
    
        $stmt->close(); // Cerrar la consulta preparada
    
        return $datos; // Devolver el array con los pagos encontrados
    }


   
}
