<?php

class ContactoEmergencia
{
    private $id_contacto;
    private $id_conductor;
    private $nombres;
    private $telefono;
    private $parentesco;
    private $conectar;

    /**
     * ContactoEmergencia constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getIdContacto()
    {
        return $this->id_contacto;
    }

    /**
     * @param mixed $id_contacto
     */
    public function setIdContacto($id_contacto)
    {
        $this->id_contacto = $id_contacto;
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
    public function getNombres()
    {
        return $this->nombres;
    }

    /**
     * @param mixed $nombres
     */
    public function setNombres($nombres)
    {
        $this->nombres = $nombres;
    }

    /**
     * @return mixed
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * @param mixed $telefono
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    /**
     * @return mixed
     */
    public function getParentesco()
    {
        return $this->parentesco;
    }

    /**
     * @param mixed $parentesco
     */
    public function setParentesco($parentesco)
    {
        $this->parentesco = $parentesco;
    }

    public function insertar()
    {
        $sql = "INSERT INTO contacto_emergencia 
                (id_conductor, nombres, telefono, parentesco) 
                VALUES 
                ('$this->id_conductor', '$this->nombres', '$this->telefono', '$this->parentesco')";
        
        return $this->conectar->query($sql);
    }

    public function modificar()
    {
        $sql = "UPDATE contacto_emergencia 
                SET id_conductor = '$this->id_conductor', nombres = '$this->nombres', telefono = '$this->telefono', parentesco = '$this->parentesco'
                WHERE id_contacto = '$this->id_contacto'";
        
        return $this->conectar->query($sql);
    }

   public function obtenerDatos()
    {
        $sql = "SELECT * 
                FROM contacto_emergencia 
                WHERE id_contacto = '$this->id_contacto'";
        $fila = $this->conectar->get_Row($sql);
        $this->id_conductor = $fila['id_conductor'];
        $this->nombres = $fila['nombres'];
        $this->telefono = $fila['telefono'];
        $this->parentesco = $fila['parentesco'];
    }
  
  public function obtenerDatosporConductor()
    {
        $sql = "SELECT * 
                FROM contacto_emergencia 
                WHERE id_conductor = '$this->id_conductor'"; // Se cambió 'id_contacto' por 'id_conductor' (comentado)
        
        // Ejecuta la consulta
        $resultado = $this->conectar->query($sql);
        
        // Verifica si la consulta se ejecutó correctamente
        if ($resultado) {
            // Verifica si se obtienen filas
            if ($resultado->num_rows > 0) {
                // Obtiene la fila como un array asociativo
                $fila = $resultado->fetch_assoc();
    
                // Muestra el contenido de la fila
                 // Esto debería mostrar los datos si la fila existe
                
                // Asigna los valores a las propiedades
                $this->nombres = $fila['nombres'];           // Line 149
                $this->telefono = $fila['telefono'];         // Line 150
                $this->parentesco = $fila['parentesco'];
            } else {
                echo "No se encontraron resultados para el id_contacto: $this->id_contacto";
            }
        } else {
            // Manejo de errores en caso de que no haya resultados
            echo "Error al ejecutar la consulta: " . $this->conectar->error;
        }
    }
  


    public function verFilas()
    {
        $sql = "SELECT * FROM contacto_emergencia ORDER BY id_contacto DESC";
        return $this->conectar->query($sql);
    }

    public function verFilasId($id)
    {
        $sql = "SELECT * FROM contacto_emergencia WHERE id_contacto = '$id'";
        return $this->conectar->query($sql)->fetch_assoc();
    }

    public function buscarContactos($term)
    {
        $sql = "SELECT * FROM contacto_emergencia 
                WHERE nombres LIKE '%$term%' OR telefono LIKE '%$term%' 
                ORDER BY nombres ASC";
        return $this->conectar->get_Cursor($sql);
    }

    public function obtenerDatosporConductorEdit()
    {
        $sql = "SELECT * 
                FROM contacto_emergencia 
                WHERE id_conductor = '$this->id_conductor'"; // Se cambió 'id_contacto' por 'id_conductor' (comentado)
        
        // Ejecuta la consulta
        $resultado = $this->conectar->query($sql);
        
        // Verifica si la consulta se ejecutó correctamente
        if ($resultado) {
            // Verifica si se obtienen filas
            if ($resultado->num_rows > 0) {
                // Obtiene la fila como un array asociativo
                $fila = $resultado->fetch_assoc();
    
                // Muestra el contenido de la fila
                 // Esto debería mostrar los datos si la fila existe
                
                // Asigna los valores a las propiedades
                $this->nombres = $fila['nombres'];           // Line 149
                $this->telefono = $fila['telefono'];         // Line 150
                $this->parentesco = $fila['parentesco'];
            } else {
                echo "No se encontraron resultados para el id_contacto: $this->id_contacto";
            }
        } else {
            // Manejo de errores en caso de que no haya resultados
            echo "Error al ejecutar la consulta: " . $this->conectar->error;
        }

        return $this->toArray();
    }

    public function toArray() // Método agregado
    {
        return [
            'nombres' => $this->nombres,
            'telefono' => $this->telefono,
            'parentesco' => $this->parentesco
        ];
    }

    public function editar()
    {
        try {
            $sql = "UPDATE contacto_emergencia SET 
                    nombres = ?, telefono = ?, parentesco = ?
                    WHERE id_conductor = ?";

            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $this->conectar->error);
            }

            $stmt->bind_param("sssi", 
                $this->nombres,
                $this->telefono,
                $this->parentesco,
                $this->id_conductor
            );

            $resultado = $stmt->execute();

            if (!$resultado) {
                throw new Exception("Error ejecutando la consulta: " . $stmt->error);
            }

            $stmt->close();

            return true;
        } catch (Exception $e) {
            error_log("Error en ContactoEmergencia::editar: " . $e->getMessage());
            return false;
        }
    }
}
?>
