<?php

class ConductorCuotaModel
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function registrarCuota($id_financiamiento, $numero_cuota, $fecha_vencimiento, $monto_cuota, $estado_cuota)
    {
        $query = "INSERT INTO conductor_cuotas (idconductor_Financiamiento, numero_cuota, fecha_vencimiento, monto_cuota, estado_cuota) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conectar->prepare($query);
        $stmt->bind_param("iisds", $id_financiamiento, $numero_cuota, $fecha_vencimiento, $monto_cuota, $estado_cuota);
        return $stmt->execute();
    }

    

        // Método para obtener el cronograma de pagos (cuotas) de un financiamiento
        public function obtenerCronogramaPagos($idFinanciamiento) {


           
            // Consulta SQL para obtener las cuotas asociadas a un financiamiento
            $query = "SELECT numero_cuota, fecha_vencimiento, monto_cuota, estado_cuota FROM conductor_cuotas WHERE idconductor_Financiamiento = ?";
            
            // Preparar y ejecutar la consulta
            $stmt = $this->conectar->prepare($query);
            $stmt->bind_param("i", $idFinanciamiento[0]); // Enlazar el parámetro idconductor_Financiamiento
            $stmt->execute();
            $result = $stmt->get_result();
    
            // Almacenar las cuotas
            $cuotas = [];
            while ($row = $result->fetch_assoc()) {
                $cuotas[] = [
                    'numero_cuota' => $row['numero_cuota'],
                    'fecha_vencimiento' => $row['fecha_vencimiento'],
                    'monto_cuota' => $row['monto_cuota'],
                    'estado_cuota' => $row['estado_cuota']
                ];
            }

    
            // Retornar las cuotas obtenidas
            return $cuotas;
        }
  
    
}
