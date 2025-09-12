<?php

class ConductorRegFinanciamientoModel
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function registrarFinanciamiento($id_conductor, $numero_cuotas, $frecuencia_pago, $fechainicio_pago, $fechafin_pago, $monto_cuota, $tasa_interes, $monto_inicial = null)
    {
        $query = "INSERT INTO conductor_regfinanciamiento (id_conductor, numero_cuotas, frecuencia_pago, fechainicio_pago, fechafin_pago, monto_cuota, tasa_interes, monto_inicial) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conectar->prepare($query);
        $stmt->bind_param("iisssdds", $id_conductor, $numero_cuotas, $frecuencia_pago, $fechainicio_pago, $fechafin_pago, $monto_cuota, $tasa_interes, $monto_inicial); 
        if ($stmt->execute()) {
            return $this->conectar->insert_id;
        }
        return false;
    }

    

        // Método para obtener el ID del financiamiento asociado a un conductor
        public function obtenerIdFinanciamiento($idConductor) {
            // Consulta SQL para obtener los financiamientos asociados al conductor
            $query = "SELECT idconductor_regfinanciamiento FROM conductor_regfinanciamiento WHERE id_conductor = ?";
            
            // Preparar y ejecutar la consulta
            $stmt = $this->conectar->prepare($query);
            $stmt->bind_param("i", $idConductor); // Enlazar el parámetro id_conductor
            $stmt->execute();
            $result = $stmt->get_result();
    
            // Obtener los IDs de los financiamientos
            $financiamientos = [];
            while ($row = $result->fetch_assoc()) {
                $financiamientos[] = $row['idconductor_regfinanciamiento']; // Almacenar el ID del financiamiento
            }
    
            // Retornar los IDs de los financiamientos
            return $financiamientos;
        }
   
}


