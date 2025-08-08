<?php

class CuotaFinanciamiento
{
    private $idcuotas_financiamiento;
    private $id_financiamiento;
    private $numero_cuota;
    private $monto;
    private $fecha_vencimiento;
    private $estado;
    private $fecha_pago;
    private $comentarios;
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    // Obtener cuotas por cliente
    public function obtenerCuotasPorCliente($id_conductor)
    {
        try {
            $sql = "SELECT cf.fecha_vencimiento, cf.monto, cf.estado 
                    FROM cuotas_financiamiento cf
                    INNER JOIN financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
                    WHERE f.id_conductor = ?";
    
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_conductor);
            $stmt->execute();
            $result = $stmt->get_result();
    
            $cuotas = [];
            while ($row = $result->fetch_assoc()) {
                $cuotas[] = $row;
            }
    
            return $cuotas;
    
        } catch (Exception $e) {
            throw $e;
        }
    }

    

    public function guardarCuota($idFinanciamiento, $numeroCuota, $monto, $fechaVencimiento)
    {
        // Asignar valores a variables
        $estado = 'En Progreso'; // Asignación a variable
        $fechaPago = null; // Asignación a variable

        $query = "INSERT INTO cuotas_financiamiento (id_financiamiento, numero_cuota, monto, fecha_vencimiento, estado, fecha_pago)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conectar->prepare($query);
        $stmt->bind_param("iidsss", 
            $idFinanciamiento,
            $numeroCuota,
            $monto,
            $fechaVencimiento, // Se utiliza la fecha de vencimiento proporcionada
            $estado, // Usamos la variable $estado
            $fechaPago // Usamos la variable $fechaPago
        );
        $stmt->execute();
    }

    public function obtenerCuotasPorFinanciamiento($id_financiamiento)
    {
        try {
            $sql = "SELECT fecha_vencimiento, monto, estado 
                    FROM cuotas_financiamiento
                    WHERE id_financiamiento = ?
                    ORDER BY numero_cuota ASC";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_financiamiento);
            $stmt->execute();
            $result = $stmt->get_result();

            $cuotas = [];
            while ($row = $result->fetch_assoc()) {
                $cuotas[] = $row;
            }

            return $cuotas;

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getCuotasforFinanciamientoList($id_financiamiento) { // Modificado
        $sql = "SELECT * FROM cuotas_financiamiento WHERE id_financiamiento = ? ORDER BY numero_cuota ASC"; // Modificado
        $stmt = $this->conectar->prepare($sql); // Modificado
        $stmt->bind_param('i', $id_financiamiento); // Modificado
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
}