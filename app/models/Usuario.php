<?php

class Usuario
{
    private $usuario_id;
    private $id_empresa;
    private $num_doc;
    private $usuario;
    private $clave;
    private $email;
    private $nombres;
    private $apellidos;
    private $token_reset;
    private $estado;
    private $userRol;

    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getUsuarioId()
    {
        return $this->usuario_id;
    }

    /**
     * @param mixed $usuario_id
     */
    public function setUsuarioId($usuario_id)
    {
        $this->usuario_id = $usuario_id;
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
    public function getNumDoc()
    {
        return $this->num_doc;
    }

    /**
     * @param mixed $num_doc
     */
    public function setNumDoc($num_doc)
    {
        $this->num_doc = $num_doc;
    }

    /**
     * @return mixed
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param mixed $usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * @return mixed
     */
    public function getClave()
    {
        return $this->clave;
    }

    /**
     * @param mixed $clave
     */
    public function setClave($clave)
    {
        $this->clave = $clave;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * @param mixed $apellidos
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;
    }

    /**
     * @return mixed
     */
    public function getTokenReset()
    {
        return $this->token_reset;
    }

    /**
     * @param mixed $token_reset
     */
    public function setTokenReset($token_reset)
    {
        $this->token_reset = $token_reset;
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
    
    /**
     * @return mixed
     */
    public function getSucursal()
    {
        return $this->$sucursal;
    }

    /**
     * @param mixed $sucursal
     */
    public function setSucursal($sucursal)
    {
        $this->sucursal = $sucursal;
    }

    public function getUserRol()
    {
        $this->userRol = $userRol;
    }

   
    public function setUserRol($userRol)
    {
        $this->userRol = $userRol;
    }

    public function setNuevaClave($nueva_clave) {
        $this->nueva_clave = $nueva_clave;
    }
    
    
    public function login(){
        $respuesta=["res"=>false];
        $sql="select * from usuarios where email='{$this->usuario}' or usuario='{$this->usuario}'";
        $resul = $this->conectar->query($sql);
        if ($row = $resul->fetch_assoc()){
            if ($row['clave'] == sha1($this->clave)) {
                if ($row["estado"] == 1) {
                    $_SESSION['nombres'] = $row['nombres'];
                    $_SESSION['apellidos'] = $row['apellidos'];
                   
                    // Nueva validación: Verificar que el id_rol coincida con userRol  
                    if ($row["id_rol"] != $this->userRol) { // Cambio: Se usa userRol en lugar de sucursal
                        $respuesta['msg'] = "Rol incorrecto"; // Cambio: Mensaje de error actualizado
                        return $respuesta;
                    }
                    
                    $sqlEmpresa = "SELECT * FROM empresas WHERE id_empresa = '{$row['id_empresa']}'";
                    $empr = $this->conectar->query($sqlEmpresa)->fetch_assoc();
    
                    // Guardamos los datos esenciales en la sesión
                    $_SESSION['usuario_id'] = $row['usuario_id'];
                    $_SESSION['usuario_fac'] = $row['usuario_id']; // Agregamos usuario_fac
                    $_SESSION['id_rol'] = $row['id_rol'];
                    $_SESSION['nombre_usuario'] = $row['usuario'];
                    $_SESSION['id_empresa'] = $empr['id_empresa'];
                    $_SESSION['nombre_empresa'] = $empr['razon_social'];
                    $_SESSION['logo_empresa'] = $empr['logo'];
                    $_SESSION['sucursal'] = 1;
                    $_SESSION['ruc_empr'] = $empr['ruc'];
                    $_SESSION['last_activity'] = time(); //Añadimos el timepo de la session
    
                    $respuesta['res'] = true;
                    $respuesta['token'] = Tools::encryptText(json_encode([
                        "usuario_id" => $row['usuario_id'],
                        "usuario_fac" => $row['usuario_id'], // Agregamos usuario_fac al token
                        "id_rol" => $row['id_rol'],
                        "nombre_usuario" => $row['usuario'],
                        "id_empresa" => $empr['id_empresa'],
                        "nombre_empresa" => $empr['razon_social'],
                        "logo_empresa" => $empr['logo'],
                        "sucursal" => 1, 
                        "ruc_empr" => $empr['ruc']
                    ]));
    
                    $respuesta['ruta'] = "/";
    
                } else {
                    $respuesta['msg'] = "Usuario Bloqueado";
                }
            } else {
                $respuesta['msg'] = "Contraseña incorrecta";
            }
        }else{
            $respuesta['msg']="Usuario no encontrado";
        }
        return $respuesta;
    }
    

    public function addNewUser($rol, $ndoc, $usuario, $clave, $email, $nombres, $sucursal, $rotativo)
    {
        // Forzar valores fijos para id_empresa y sucursal
        $id_empresa = 12;
        $sucursal = 1; // Forzamos el valor a 1 independientemente de lo que se pase como parámetro
        
        $sql = "INSERT INTO usuarios (id_empresa, id_rol, num_doc, usuario, clave, email, nombres, sucursal, rotativo, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)"; // Agregado id_empresa
    
        $stmt = $this->conectar->prepare($sql); // Preparar la consulta SQL
    
        if ($stmt === false) {
            return false; // Si falla la preparación, retornar false
        }
    
        // Vincular los parámetros (agregado id_empresa como primer parámetro)
        $stmt->bind_param("iisssssii", $id_empresa, $rol, $ndoc, $usuario, $clave, $email, $nombres, $sucursal, $rotativo);
    
        $resultado = $stmt->execute(); // Ejecutar la consulta
    
        $stmt->close(); // Cerrar la consulta
    
        return $resultado; // Retornar true si la inserción fue exitosa, false en caso contrario
    }

    // Método para obtener los datos del usuario por su ID
    public function getData($idAsesor) { // Agregado: Método para obtener datos de usuario
        $query = "SELECT * FROM usuarios WHERE usuario_id = ?"; // Agregado: Consulta SQL
        $stmt = $this->conectar->prepare($query); // Agregado: Preparar la consulta
        $stmt->bind_param("i", $idAsesor); // Agregado: Pasar el ID como parámetro
        $stmt->execute(); // Agregado: Ejecutar la consulta
        $result = $stmt->get_result(); // Agregado: Obtener resultado
        return $result->fetch_assoc(); // Agregado: Retornar los datos en un array asociativo
    }

    public function getAll() {
        $sql = "SELECT usuario_id, nombres, apellidos FROM usuarios"; // Consulta SQL
        $resultado = $this->conectar->query($sql);

        $usuarios = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $usuarios[] = $fila; // Guardar cada usuario en el array
            }
        }
        return $usuarios; // Retornar la lista de usuarios
    }

      // Método para cambiar la contraseña del usuario
      public function changePasswordUser() {
        // Validar que la contraseña actual sea correcta
        $sql = "SELECT clave FROM usuarios WHERE usuario_id = " . $this->usuario_id;
        $resul = $this->conectar->query($sql);
        
        if ($row = $resul->fetch_assoc()) {
            // Verificar si la contraseña actual coincide con la almacenada
            if ($row['clave'] == sha1($this->clave)) {
                // Actualizar la contraseña en la base de datos
                $nueva_clave_encriptada = sha1($this->nueva_clave);
                $sql_update = "UPDATE usuarios SET clave = '$nueva_clave_encriptada' WHERE usuario_id = " . $this->usuario_id;
                
                if ($this->conectar->query($sql_update)) {
                    return [
                        'status' => 'success',
                        'message' => 'Contraseña actualizada correctamente'
                    ];
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'Error al actualizar la contraseña: ' . $this->conectar->error
                    ];
                }
            } else {
                return [
                    'status' => 'error',
                    'message' => 'La contraseña actual es incorrecta'
                ];
            }
        } else {
            return [
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ];
        }
    }

}