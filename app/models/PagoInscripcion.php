<?php

class PagoInscripcion
{
    private $id_pago;
    private $id_inscripcion;
    private $medio_pago;
    private $monto;
    private $estado;
    private $conectar;

    /**
     * PagoInscripcion constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getIdPago()
    {
        return $this->id_pago;
    }

    /**
     * @param mixed $id_pago
     */
    public function setIdPago($id_pago)
    {
        $this->id_pago = $id_pago;
    }

    /**
     * @return mixed
     */
    public function getIdInscripcion()
    {
        return $this->id_inscripcion;
    }

    /**
     * @param mixed $id_inscripcion
     */
    public function setIdInscripcion($id_inscripcion)
    {
        $this->id_inscripcion = $id_inscripcion;
    }

    /**
     * @return mixed
     */
    public function getMedioPago()
    {
        return $this->medio_pago;
    }

    /**
     * @param mixed $medio_pago
     */
    public function setMedioPago($medio_pago)
    {
        $this->medio_pago = $medio_pago;
    }

    /**
     * @return mixed
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * @param mixed $monto
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;
    }

    /**
     * @return mixed
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param mixed $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function insertar()
    {
        $sql = "INSERT INTO pagos_inscripcion (id_inscripcion, medio_pago, monto, estado) 
                VALUES ('$this->id_inscripcion', '$this->medio_pago', '$this->monto', '$this->estado')";
        
        return $this->conectar->ejecutar_idu($sql);
    }

    public function modificar()
    {
        $sql = "UPDATE pagos_inscripcion 
                SET id_inscripcion = '$this->id_inscripcion', medio_pago = '$this->medio_pago', monto = '$this->monto', estado = '$this->estado' 
                WHERE id_pago = '$this->id_pago'";
        
        return $this->conectar->ejecutar_idu($sql);
    }

    public function obtenerDatos()
    {
        $sql = "SELECT * FROM pagos_inscripcion WHERE id_pago = '$this->id_pago'";
        $fila = $this->conectar->get_Row($sql);
        $this->id_inscripcion = $fila['id_inscripcion'];
        $this->medio_pago = $fila['medio_pago'];
        $this->monto = $fila['monto'];
        $this->estado = $fila['estado'];
    }

    public function verFilas()
    {
        $sql = "SELECT * FROM pagos_inscripcion ORDER BY id_pago DESC";
        return $this->conectar->query($sql);
    }

    public function verFilasId($id)
    {
        $sql = "SELECT * FROM pagos_inscripcion WHERE id_pago = '$id'";
        return $this->conectar->query($sql)->fetch_assoc();
    }

    public function registrarPago($idInscripcion, $metodoPago, $monto, $idConductor, $idAsesor, $fechaPago, $efectivoRecibido, $vuelto) {
        $sql = "INSERT INTO pagos_inscripcion (id_inscripcion, medio_pago, monto, id_conductor, id_asesor, fecha_pago, efectivo_recibido, vuelto) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("isdiisss", $idInscripcion, $metodoPago, $monto, $idConductor, $idAsesor, $fechaPago, $efectivoRecibido, $vuelto);
        
        if ($stmt->execute()) {
            return $stmt->insert_id; // Retorna el ID autogenerado
        } else {
            return false;
        }
    }

    public function registrarDetallePago($idPago, $idInscripcion, $cuotas) {
            $this->conectar = (new Conexion())->getConexion();

            // 1. Obtener las cuotas de la BD asociadas a la inscripción
            $query = "SELECT id_conductorcuota, numero_cuota FROM conductor_cuotas WHERE idconductor_Financiamiento = ?";
            $stmt = $this->conectar->prepare($query);
            $stmt->bind_param("i", $idInscripcion);
            $stmt->execute();
            $result = $stmt->get_result();

            $cuotasBD = [];
            while ($row = $result->fetch_assoc()) {
                $cuotasBD[$row['numero_cuota']] = $row['id_conductorcuota'];
            }
            $stmt->close();

            // 2. Recorrer el array de cuotas del frontend y verificar cuáles deben registrarse
            $registros = [];
            foreach ($cuotas as $cuota) {
                $numeroCuota = $cuota['numero_cuota'];
                $pagoH = $cuota['pagoH']; // Indica si se pagó (1) o no (0)
                $mora = !empty($cuota['mora']) ? floatval($cuota['mora']) : 0.00;

                // Si pagoH es 1 y la cuota existe en la BD, tomamos su ID
                if ($pagoH == "1" && isset($cuotasBD[$numeroCuota])) {
                    $idCuota = $cuotasBD[$numeroCuota];

                    // Agregar el registro a la lista
                    $registros[] = [
                        'idpagos_inscripcion' => $idPago,
                        'id_cuota' => $idCuota,
                        'mora' => $mora
                    ];
                }
            }

            // 3. Insertar los registros en la tabla detalle_pago_inscripcion
            if (!empty($registros)) {
                $queryInsert = "INSERT INTO detalle_pago_inscripcion (idpagos_inscripcion, id_cuota, mora) VALUES (?, ?, ?)";
                $stmtInsert = $this->conectar->prepare($queryInsert);

                foreach ($registros as $registro) {
                    $stmtInsert->bind_param("iid", $registro['idpagos_inscripcion'], $registro['id_cuota'], $registro['mora']);
                    $stmtInsert->execute();
                }
                $stmtInsert->close();
            }

            return true; // Indicar que se registraron los pagos
        }
        
        public function actualizarCuotas($idInscripcion, $cuotas, $fechaPago, $metodoPago) { 
        $sql = "SELECT id_conductorcuota, numero_cuota, monto_cuota FROM conductor_cuotas WHERE idconductor_Financiamiento = ?"; // Agregado monto_cuota
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $idInscripcion);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $cuotasBD = [];
        while ($row = $result->fetch_assoc()) {
            $cuotasBD[$row['numero_cuota']] = [
                'id' => $row['id_conductorcuota'],
                'monto_cuota' => $row['monto_cuota'] // Guardamos monto_cuota para actualizar monto_pagado
            ];
        }
        $stmt->close();


        foreach ($cuotas as $cuota) {
            $numeroCuota = $cuota['numero_cuota'];
            $estadoCuota = $cuota['pagado'];
            $mora = !empty($cuota['mora']) && $cuota['mora'] !== "0.00" ? $cuota['mora'] : null; // Si es vacío o 0.00, no actualiz
            $pagoH = isset($cuota['pagoH']) ? $cuota['pagoH'] : "0"; 

      

            if ($pagoH === "1") { // Agregado: Solo actualizar si pagoH es "1"
                if (isset($cuotasBD[$numeroCuota])) {
                    $idCuota = $cuotasBD[$numeroCuota]['id'];
                    $montoPagado = $cuotasBD[$numeroCuota]['monto_cuota']; 
    
                    $sqlUpdate = "UPDATE conductor_cuotas SET estado_cuota = ?, fecha_pago = ?, metodo_pago = ?, monto_pagado = ?";
                    $params = ["sssd", $estadoCuota, $fechaPago, $metodoPago, $montoPagado];
                    
                    if ($mora !== null) {
                        $sqlUpdate .= ", mora = ?";
                        $params[0] .= "d"; 
                        $params[] = $mora;
                    }
    
                    $sqlUpdate .= " WHERE id_conductorcuota = ?";
                    $params[0] .= "i";
                    $params[] = $idCuota;
    
                  
                    
                    $stmtUpdate = $this->conectar->prepare($sqlUpdate);
                    $stmtUpdate->bind_param(...$params);
                    $stmtUpdate->execute();
                    $stmtUpdate->close();
                }
            }
        }
    }

    public function guardarNotaVenta($idPago, $idConductor, $idAsesor, $monto, $fechaEmision, $pdfPath) {
        $sql = "INSERT INTO notas_venta_inscripcion (id_pagosinscripcion, id_conductor, id_asesor, monto, fecha_emision, ruta) 
            VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("iiidss", $idPago, $idConductor, $idAsesor, $monto, $fechaEmision, $pdfPath);
        return $stmt->execute();
    }

}
?>
