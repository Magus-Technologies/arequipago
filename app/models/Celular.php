<?php

class Celular {
    private $conectar;
    
    public function __construct() {
        $this->conectar = (new Conexion())->getConexion();
    }
    
    /**
     * Guarda los datos de un celular en la tabla celulares
     * 
     * @param array $data Datos del celular a guardar
     * @return int|bool ID del celular insertado o false en caso de error
     */
    public function saveCelular($data) {
        // Preparar la consulta SQL
        $query = "INSERT INTO celulares (
            idproductosv2, 
            chip_linea, 
            marca, 
            modelo, 
            imei, 
            imei2, 
            color, 
            cargador, 
            cable_usb, 
            manual_usuario,
            estuche -- AÑADIDO: campo 'estuche' incluido en la consulta
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; // CAMBIADO: ahora hay 11 signos de interrogación (antes estaban bien)
    
        // Preparar la sentencia
        $stmt = $this->conectar->prepare($query);
    
        if (!$stmt) {
            error_log("Error en la preparación de la consulta: " . $this->conectar->error);
            return false;
        }
    
        // Vincular parámetros
        $stmt->bind_param(
            "issssssssss", // CAMBIADO: antes era "isssssssss", ahora se agregaron 11 tipos (1 entero y 10 strings)
            $data['idproductosv2'],
            $data['chip_linea'],
            $data['marca'],
            $data['modelo'],
            $data['imei'],
            $data['imei2'],
            $data['color'],
            $data['cargador'],
            $data['cable_usb'],
            $data['manual_usuario'],
            $data['estuche'] // AÑADIDO: se agregó 'estuche' en el bind
        );
    
        // Ejecutar la consulta
        if (!$stmt->execute()) {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            $stmt->close();
            return false;
        }
    
        // Obtener el ID insertado
        $idCelular = $this->conectar->insert_id;
    
        // Cerrar la sentencia
        $stmt->close();
    
        return $idCelular;
    }

    public function guardarCelularesMasivos($celulares) {
        if (empty($celulares)) {
            return true; // No hay nada que guardar
        }
        
        $this->conectar->begin_transaction();
        
        try {
            foreach ($celulares as $celular) {
                // Verificar si ya existe un registro para este producto
                $idProducto = $celular['idproductosv2'];
                $query = "SELECT idcelulares FROM celulares WHERE idproductosv2 = ?";
                $stmt = $this->conectar->prepare($query);
                $stmt->bind_param("i", $idProducto);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    // Actualizar registro existente
                    $row = $result->fetch_assoc();
                    $idCelular = $row['idcelulares'];
                    
                    $query = "UPDATE celulares SET 
                        chip_linea = ?, 
                        marca = ?, 
                        modelo = ?, 
                        imei = ?, 
                        imei2 = ?, 
                        color = ?, 
                        cargador = ?, 
                        cable_usb = ?, 
                        manual_usuario = ?,
                        estuche = ?
                        WHERE idcelulares = ?";
                    
                    $stmt = $this->conectar->prepare($query);
                    $stmt->bind_param(
                        "ssssssssssi",
                        $celular['chip_linea'],
                        $celular['marca'],
                        $celular['modelo'],
                        $celular['imei'],
                        $celular['imei2'],
                        $celular['color'],
                        $celular['cargador'],
                        $celular['cable_usb'],
                        $celular['manual_usuario'],
                        $celular['estuche'],
                        $idCelular
                    );
                } else {
                    // Insertar nuevo registro
                    $query = "INSERT INTO celulares (
                        idproductosv2, 
                        chip_linea, 
                        marca, 
                        modelo, 
                        imei, 
                        imei2, 
                        color, 
                        cargador, 
                        cable_usb, 
                        manual_usuario,
                        estuche
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    
                    $stmt = $this->conectar->prepare($query);
                    $stmt->bind_param(
                        "issssssssss",
                        $celular['idproductosv2'],
                        $celular['chip_linea'],
                        $celular['marca'],
                        $celular['modelo'],
                        $celular['imei'],
                        $celular['imei2'],
                        $celular['color'],
                        $celular['cargador'],
                        $celular['cable_usb'],
                        $celular['manual_usuario'],
                        $celular['estuche']
                    );
                }
                
                if (!$stmt->execute()) {
                    throw new \Exception("Error al guardar celular: " . $this->conectar->error);
                }
                
                $stmt->close();
            }
            
            $this->conectar->commit();
            return true;
        } catch (\Exception $e) {
            $this->conectar->rollback();
            error_log("Error en guardarCelularesMasivos: " . $e->getMessage());
            return false;
        }
    }

    
    /**
     * Actualiza o crea las características de un celular
     * @param array $datos Datos del celular a actualizar/crear
     * @return bool Resultado de la operación
     */
    public function actualizarCaracteristicasCelular($datos) {
        try {
            // Validar que tengamos al menos el ID del producto
            if (!isset($datos['idproductosv2']) || empty($datos['idproductosv2'])) {
                error_log("Error: ID de producto no proporcionado para actualizar celular");
                return false;
            }
            
            $idProducto = intval($datos['idproductosv2']);
            
            // Verificar si ya existe un registro para este producto
            $query = "SELECT idcelulares FROM celulares WHERE idproductosv2 = ?";
            $stmt = $this->conectar->prepare($query);
            $stmt->bind_param("i", $idProducto);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            if ($resultado->num_rows > 0) {
                // Existe un registro, actualizar
                $fila = $resultado->fetch_assoc();
                $idCelular = $fila['idcelulares'];
                return $this->actualizarCelular($idCelular, $datos);
            } else {
                // No existe, insertar nuevo
                return $this->insertarCelular($datos);
            }
        } catch (Exception $e) {
            error_log("Error en actualizarCaracteristicasCelular: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Inserta un nuevo registro de celular
     * @param array $datos Datos del celular a insertar
     * @return bool Resultado de la operación
     */
    private function insertarCelular($datos) {
        try {
            $query = "INSERT INTO celulares (
                idproductosv2, chip_linea, marca, modelo, imei, imei2, 
                color, cargador, cable_usb, manual_usuario, estuche
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conectar->prepare($query);
            
            // Asegurarse de que todos los campos existan, si no usar NULL
            $idProducto = intval($datos['idproductosv2']);
            $chipLinea = $datos['chip_linea'] ?? null;
            $marca = $datos['marca'] ?? null;
            $modelo = $datos['modelo'] ?? null;
            $imei = $datos['imei'] ?? null;
            $imei2 = $datos['imei2'] ?? null;
            $color = $datos['color'] ?? null;
            $cargador = $datos['cargador'] ?? null;
            $cableUsb = $datos['cable_usb'] ?? null;
            $manualUsuario = $datos['manual_usuario'] ?? null;
            $estuche = $datos['estuche'] ?? null;
            
            $stmt->bind_param(
                "issssssssss", 
                $idProducto, $chipLinea, $marca, $modelo, $imei, $imei2,
                $color, $cargador, $cableUsb, $manualUsuario, $estuche
            );
            
            $resultado = $stmt->execute();
            
            if (!$resultado) {
                error_log("Error al insertar celular: " . $stmt->error);
            }
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error en insertarCelular: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualiza un registro de celular existente
     * @param int $idCelular ID del registro a actualizar
     * @param array $datos Datos del celular a actualizar
     * @return bool Resultado de la operación
     */
    private function actualizarCelular($idCelular, $datos) {
        try {
            $query = "UPDATE celulares SET 
                chip_linea = ?, marca = ?, modelo = ?, imei = ?, imei2 = ?,
                color = ?, cargador = ?, cable_usb = ?, manual_usuario = ?, estuche = ?
                WHERE idcelulares = ?";
            
            $stmt = $this->conectar->prepare($query);
            
            // Asegurarse de que todos los campos existan, si no usar NULL
            $chipLinea = $datos['chip_linea'] ?? null;
            $marca = $datos['marca'] ?? null;
            $modelo = $datos['modelo'] ?? null;
            $imei = $datos['imei'] ?? null;
            $imei2 = $datos['imei2'] ?? null;
            $color = $datos['color'] ?? null;
            $cargador = $datos['cargador'] ?? null;
            $cableUsb = $datos['cable_usb'] ?? null;
            $manualUsuario = $datos['manual_usuario'] ?? null;
            $estuche = $datos['estuche'] ?? null;
            
            $stmt->bind_param(
                "ssssssssssi", 
                $chipLinea, $marca, $modelo, $imei, $imei2,
                $color, $cargador, $cableUsb, $manualUsuario, $estuche, $idCelular
            );
            
            $resultado = $stmt->execute();
            
            if (!$resultado) {
                error_log("Error al actualizar celular: " . $stmt->error);
            }
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error en actualizarCelular: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina un registro de celular por ID de producto
     * @param int $idProducto ID del producto asociado al celular
     * @return bool Resultado de la operación
     */
    public function eliminarCelularPorProductoId($idProducto) {
        try {
            $query = "DELETE FROM celulares WHERE idproductosv2 = ?";
            $stmt = $this->conectar->prepare($query);
            
            $idProducto = intval($idProducto);
            $stmt->bind_param("i", $idProducto);
            
            $resultado = $stmt->execute();
            
            if (!$resultado) {
                error_log("Error al eliminar celular: " . $stmt->error);
            }
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error en eliminarCelularPorProductoId: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene los datos de un celular por ID de producto
     * @param int $idProducto ID del producto asociado al celular
     * @return array|false Datos del celular o false si no existe
     */
    public function obtenerCelularPorProductoId($idProducto) {
        try {
            $query = "SELECT * FROM celulares WHERE idproductosv2 = ?";
            $stmt = $this->conectar->prepare($query);
            
            $idProducto = intval($idProducto);
            $stmt->bind_param("i", $idProducto);
            $stmt->execute();
            
            $resultado = $stmt->get_result();
            
            if ($resultado->num_rows > 0) {
                return $resultado->fetch_assoc();
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error en obtenerCelularPorProductoId: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerCaracteristicasCelulares($idProducto) {
        try {
            // Preparamos la consulta SQL para obtener los datos de la tabla celulares
            $query = "SELECT * FROM celulares WHERE idproductosv2 = ?";
            $stmt = $this->conectar->prepare($query);
            $stmt->bind_param("i", $idProducto);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            // Verificamos si se encontró el celular
            if ($resultado->num_rows === 0) {
                var_dump("No se encontraron características para el celular con ID: " . $idProducto);
                return [];
            }
            
            // Obtenemos los datos del celular
            $celular = $resultado->fetch_assoc();
            
            // Transformamos los datos al formato esperado por el controlador (mismo formato que obtenerCaracteristicas)
            $caracteristicasFormateadas = [
                [
                    'nombre_caracteristicas' => 'chip_linea',
                    'valor_caracteristica' => $celular['chip_linea'] ?? ''
                ],
                [
                    'nombre_caracteristicas' => 'marca_equipo',
                    'valor_caracteristica' => $celular['marca'] ?? ''
                ],
                [
                    'nombre_caracteristicas' => 'modelo',
                    'valor_caracteristica' => $celular['modelo'] ?? ''
                ],
                [
                    'nombre_caracteristicas' => 'nro_imei',
                    'valor_caracteristica' => $celular['imei'] ?? ''
                ],
                [
                    'nombre_caracteristicas' => 'nro_serie',
                    'valor_caracteristica' => $celular['imei2'] ?? '' // Asumiendo que 'nro_serie' mapea a 'imei2'
                ],
                [
                    'nombre_caracteristicas' => 'colorc',
                    'valor_caracteristica' => $celular['color'] ?? ''
                ],
                [
                    'nombre_caracteristicas' => 'cargador',
                    'valor_caracteristica' => $celular['cargador'] ?? ''
                ],
                [
                    'nombre_caracteristicas' => 'cable_usb',
                    'valor_caracteristica' => $celular['cable_usb'] ?? ''
                ],
                [
                    'nombre_caracteristicas' => 'manual_usuario',
                    'valor_caracteristica' => $celular['manual_usuario'] ?? ''
                ],
                [
                    'nombre_caracteristicas' => 'estuche',
                    'valor_caracteristica' => $celular['estuche'] ?? ''
                ]
            ];
            
            return $caracteristicasFormateadas;
            
        } catch (Exception $e) {
            var_dump("Error al obtener características del celular: " . $e->getMessage());
            return [];
        }
    }
    
}