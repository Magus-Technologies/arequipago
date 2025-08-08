<?php

class Vehiculo
{
    private $id_vehiculo;
    private $id_conductor;
    private $placa;
    private $marca;
    private $modelo;
    private $anio;
    private $numero_unidad;
    private $condicion;
    private $vehiculo_flota;
    private $fech_soat;
    private $fech_seguro;
    private $color;
    private $tipo_vehiculo;
    private $conectar;

    /**
     * Vehiculo constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getIdVehiculo()
    {
        return $this->id_vehiculo;
    }

    /**
     * @param mixed $id_vehiculo
     */
    public function setIdVehiculo($id_vehiculo)
    {
        $this->id_vehiculo = $id_vehiculo;
    }

    /**
     * @return mixed
     */
    public function getIdConductor()
    {
        return $this->id_conductor;
    }

    /**
     * @param mixed $id_conductor
     */
    public function setIdConductor($id_conductor)
    {
        $this->id_conductor = $id_conductor;
    }

    /**
     * @return mixed
     */
    public function getPlaca()
    {
        return $this->placa;
    }

    /**
     * @param mixed $placa
     */
    public function setPlaca($placa)
    {
        $this->placa = $placa;
    }

    /**
     * @return mixed
     */
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * @param mixed $marca
     */
    public function setMarca($marca)
    {
        $this->marca = $marca;
    }

    /**
     * @return mixed
     */
    public function getModelo()
    {
        return $this->modelo;
    }

    /**
     * @param mixed $modelo
     */
    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
    }

    /**
     * @return mixed
     */
    public function getAnio()
    {
        return $this->anio;
    }

    /**
     * @param mixed $anio
     */
    public function setAnio($anio)
    {
        $this->anio = $anio;
    }

    public function getNumeroUnidad(){
        return $this->numero_unidad;
    }

    public function setNumeroUnidad($numero_unidad){
        $this->numero_unidad = $numero_unidad;
    }

    public function getCondicion(){
        return $this->condicion;
    }

    public function setCondicion($condicion) {
        $this->condicion =$condicion;
    }

    public function setIdVehiculoFlota($vehiculo_flota){
        $this->vehiculo_flota = $vehiculo_flota;
    }
    
    public function getVehiculoFlota() {
        return $this->vehiculo_flota;
    }

    public function setVehiculoFlota ($vehiculo_flota){
        $this-> vehiculo_flota = $vehiculo_flota;
    }

    public function getFechSoat() { 
        return $this->fech_soat;
    }

    public function setFechSoat($fech_soat) {
        $this->fech_soat = $fech_soat;
    }

    public function getFechSeguro() { 
        return $this->fech_seguro;
    }

    public function setFechSeguro($fech_seguro) { 
        $this->fech_seguro = $fech_seguro;
    }

    public function getColor() { return $this->color;}

    public function setColor($color) { $this->color = $color;}

    public function setTipoVehiculo($tipo_vehiculo) {
        $this->tipo_vehiculo = $tipo_vehiculo;
    }

    public function insertar()
    {
        // <-- CAMBIO: Convertimos las fechas vacías a NULL (sin comillas)
        $fech_soat = empty($this->fech_soat) ? "NULL" : "'$this->fech_soat'"; // <-- CAMBIO
        $fech_seguro = empty($this->fech_seguro) ? "NULL" : "'$this->fech_seguro'";

        $sql = "INSERT INTO vehiculos 
        (id_conductor, placa, marca, modelo, anio, numero_unidad, condicion, vehiculo_flota, fech_soat, fech_seguro, color, tipo_vehiculo) 
        VALUES 
        ('$this->id_conductor', '$this->placa', '$this->marca', '$this->modelo', '$this->anio', '$this->numero_unidad',
        '$this->condicion','$this->vehiculo_flota', $fech_soat, $fech_seguro, '$this->color', '$this->tipo_vehiculo'
        )";



        if ($this->conectar->query($sql)) { // <-- Verificamos si la inserción fue exitosa
        
            return $this->conectar->insert_id; // <-- Devolvemos el ID del registro insertado
        }

 
        
        return $this->conectar->insert_id;;
    }

    public function modificar()
    {
        $sql = "UPDATE vehiculos 
                SET id_conductor = '$this->id_conductor', placa = '$this->placa', marca = '$this->marca', modelo = '$this->modelo', anio = '$this->anio', numero_unidad = '$this->numero_unidad',
                condicion = '$this->condicion', vehiculo_flota = '$this->vehiculo_flota', fech_soat = '$this->fech_soat', 
                fech_seguro = '$this->fech_seguro', color = '$this->color'
                WHERE id_vehiculo = '$this->id_vehiculo'";
        
        return $this->conectar->query($sql);
    }

    public function obtenerPlacaPorConductor($id_conductor) {
        try {
            $sql = "SELECT placa, numero_unidad, tipo_vehiculo 
            FROM vehiculos 
            WHERE id_conductor = ?";
            
            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $this->conectar->error);
            }
    
            $stmt->bind_param("i", $id_conductor);
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando la consulta: " . $stmt->error);
            }
    
            $result = $stmt->get_result();
            $datos = $result->fetch_assoc();
            
            $stmt->close();
            return $datos;
            
        } catch (Exception $e) {
            error_log("Error en Vehiculo::obtenerPlacaPorConductor(): " . $e->getMessage());
            return false;
        }
    }

    public function obtenerDatos()
    {
        $sql = "SELECT * 
                FROM vehiculos 
                WHERE id_vehiculo = '$this->id_vehiculo'";
        $fila = $this->conectar->get_Row($sql);
        $this->id_conductor = $fila['id_conductor'];
        $this->placa = $fila['placa'];
        $this->marca = $fila['marca'];
        $this->modelo = $fila['modelo'];
        $this->anio = $fila['anio'];
        $this->numero_unidad = $fila ['numero_unidad'];
        $this->condicion = $fila ['condicion'];
        $this->vehiculo_flota = $fila['vehiculo_flota'];
        $this->fech_soat = $fila['fech_soat'];
        $this->fech_seguro = $fila['fech_seguro'];
        $this->color = $fila['color'];
    }

    public function verFilas()
    {
        $sql = "SELECT * FROM vehiculos ORDER BY id_vehiculo DESC";
        return $this->conectar->query($sql);
    }

    public function verFilasId($id)
    {
        $sql = "SELECT * FROM vehiculos WHERE id_vehiculo = '$id'";
        return $this->conectar->query($sql)->fetch_assoc();
    }

    public function buscarVehiculos($term)
    {
        $sql = "SELECT * FROM vehiculos 
                WHERE placa LIKE '%$term%' OR marca LIKE '%$term%' OR modelo LIKE '%$term%'
                ORDER BY placa ASC";
        return $this->conectar->get_Cursor($sql);
    }

    public function obtenerDatosVehiculo($idConductor) {
        try {
            $sql = "SELECT placa, marca, modelo, color, anio, condicion, vehiculo_flota, fech_soat, fech_seguro, tipo_vehiculo
                FROM vehiculos 
                WHERE id_conductor = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $idConductor);
            $stmt->execute();
            $result = $stmt->get_result();
    
            return $result->fetch_assoc() ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function editar()
    {
        try {
            // Obtener el id_vehiculo asociado al id_conductor antes de actualizar 🚀
            $query = "SELECT id_vehiculo FROM vehiculos WHERE id_conductor = ?"; // <-- Nueva consulta para obtener id_vehiculo
            $stmt = $this->conectar->prepare($query);

            $stmt->bind_param("i", $this->id_conductor); // <-- Asociamos el id_conductor
            $stmt->execute(); // <-- Ejecutamos la consulta
            $stmt->bind_result($id_vehiculo); // <-- Obtenemos el resultado
            $stmt->fetch(); // <-- Extraemos el id_vehiculo
            $stmt->close(); // <-- Cerramos la consulta

            if (!$id_vehiculo) { // <-- Verificamos si se encontró el id_vehiculo
                throw new Exception("No se encontró un vehículo con este id_conductor");
            }

            $sql = "UPDATE vehiculos SET 
                    placa = ?, marca = ?, modelo = ?, anio = ?, numero_unidad = ?,
                    condicion = ?, vehiculo_flota = ?, fech_soat = ?, fech_seguro = ?, color = ?, tipo_vehiculo = ?
                    WHERE id_conductor = ?";

            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando la consulta: " . $this->conectar->error);
                throw new Exception('Error al preparar la consulta');
            }

            $stmt->bind_param("ssssissssssi",
                $this->placa,
                $this->marca,
                $this->modelo,
                $this->anio,
                $this->numero_unidad,
                $this->condicion,
                $this->vehiculo_flota,
                $fech_soat,
                $fech_seguro, 
                $this->color,
                $this->tipo_vehiculo,
                $this->id_conductor
            );

            if (!$stmt->execute()) {
                error_log("Error ejecutando la consulta: " . $stmt->error);
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $stmt->close();
            return $id_vehiculo;

        } catch (Exception $e) {
            error_log("Error en Vehiculo::editar(): " . $e->getMessage());
            return false;
        }
    }
}
?>
