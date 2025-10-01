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

    

    public function guardarCuota($idFinanciamiento, $numeroCuota, $monto, $fechaVencimiento, $grupoFinanciamiento = null, $moneda = null)
    {
        // Asignar valores a variables
        $estado = 'En Progreso';
        $fechaPago = null;

        // Si es el plan corporativo de chips (ID 36), ajustar la fecha al día 24
        if ($grupoFinanciamiento == 36) {
            $fecha = new DateTime($fechaVencimiento);
            $fecha->setDate($fecha->format('Y'), $fecha->format('n'), 24);
            $fechaVencimiento = $fecha->format('Y-m-d');
        }

        // ✅ NUEVO: Calcular campos de trazabilidad
        $monto_cuota_base = $monto; // El monto original sin comisiones
        
        // Determinar comisión según moneda
        $comision_canal_digital = null;
        if ($moneda === 'S/.') {
            $comision_canal_digital = 0.50;
        } elseif ($moneda === '$') {
            $comision_canal_digital = 0.20;
        }
        
        // El descuento aplicado será 0 por defecto (se calculará en la vista de pagos según el producto)
        $descuento_aplicado = 0.00;
        
        // Guardar la moneda de la cuota
        $moneda_cuota = $moneda;

        // ✅ MODIFICADO: Query actualizado con los nuevos campos
        $query = "INSERT INTO cuotas_financiamiento (
                    id_financiamiento, 
                    numero_cuota, 
                    monto, 
                    monto_cuota_base,
                    comision_canal_digital,
                    descuento_aplicado,
                    moneda_cuota,
                    fecha_vencimiento, 
                    estado, 
                    fecha_pago
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conectar->prepare($query);
        $stmt->bind_param("iiddddssss",
            $idFinanciamiento,
            $numeroCuota,
            $monto,
            $monto_cuota_base,           // ✅ NUEVO
            $comision_canal_digital,     // ✅ NUEVO
            $descuento_aplicado,         // ✅ NUEVO
            $moneda_cuota,               // ✅ NUEVO
            $fechaVencimiento,
            $estado,
            $fechaPago
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