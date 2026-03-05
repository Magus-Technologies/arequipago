<?php

class DocumentoEmpresa
{
    private $id_empresa;
    private $id_tido;
    private $serie;
    private $numero;
    private $conectar;

    private $sql;
    private $sql_error;

    /**
     * DocumentoEmpresa constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
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
    public function getIdTido()
    {
        return $this->id_tido;
    }

    /**
     * @param mixed $id_tido
     */
    public function setIdTido($id_tido)
    {
        $this->id_tido = $id_tido;
    }

    /**
     * @return mixed
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * @param mixed $serie
     */
    public function setSerie($serie)
    {
        $this->serie = $serie;
    }

    /**
     * @return mixed
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param mixed $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    public function exeSQL($sql)
    {
        $this->sql = $sql;
        $result = $this->conectar->query($sql);
        
        if (!$result) {
            $this->sql_error = $this->conectar->error;
        }
        
        return $result;
    }

    public function getSql (){
        return $this->sql;
    }
    public function setSql($sql) {
        $this->sql = $sql;
    }

    public function getSqlError()
    {
        return $this->sql_error;
    }
    
    public function setSqlError($sql_error)
    {
        $this->sql_error = $sql_error;
    }

    public function insertar()
    {
        $sql = "insert into documentos_empresas 
        values ('$this->id_empresa', '$this->id_tido', '$this->serie', '$this->numero')";
        
        $this->sql = $sql;
        $result = $this->conectar->query($sql);
        
        if (!$result) {
            $this->sql_error = $this->conectar->error;
            error_log("Error en DocumentoEmpresa::insertar(): " . $this->sql_error);
            return false;
        }
        
        return true;
    }

    public function modificar()
    {
        $sucursal = isset($_SESSION['sucursal']) ? $_SESSION['sucursal'] : '1';
        
        $sql = "update documentos_empresas 
        set serie = '$this->serie', numero = '$this->numero'
        where id_empresa = '$this->id_empresa' and id_tido = '$this->id_tido' and sucursal = '$sucursal'";
        
        $this->sql = $sql;
        $result = $this->conectar->query($sql);
        
        if (!$result) {
            $this->sql_error = $this->conectar->error;
            error_log("Error en DocumentoEmpresa::modificar(): " . $this->sql_error);
            return false;
        }
        
        return true;
    }

    public function obtenerDatos()
    {
        $sql = "select * 
        from documentos_empresas 
        where id_empresa = '$this->id_empresa' and id_tido = '$this->id_tido' and sucursal='{$_SESSION['sucursal']}'";
        $fila = $this->conectar->query($sql)->fetch_assoc();
        $this->serie = $fila['serie'];
        $this->numero = $fila['numero'];
    }

    /**
     * ✅ NUEVO: Obtener datos buscando por serie específica
     * Usado para NC con correlativos independientes (BC01, FC01)
     */
    public function obtenerDatosPorSerie()
    {
        $sql = "select * 
        from documentos_empresas 
        where id_empresa = '$this->id_empresa' 
        and id_tido = '$this->id_tido' 
        and serie = '$this->serie'
        and sucursal='{$_SESSION['sucursal']}'";
        $result = $this->conectar->query($sql);
        
        if ($result && $fila = $result->fetch_assoc()) {
            $this->numero = $fila['numero'];
        } else {
            // Si no existe la serie, crear con número 1
            $this->numero = 1;
        }
    }

    /**
     * ✅ NUEVO: Modificar buscando por serie específica
     * Usado para incrementar el correlativo de una serie específica
     */
    public function modificarPorSerie()
    {
        $sucursal = isset($_SESSION['sucursal']) ? $_SESSION['sucursal'] : '1';
        
        $sql = "update documentos_empresas 
        set numero = '$this->numero'
        where id_empresa = '$this->id_empresa' 
        and id_tido = '$this->id_tido' 
        and serie = '$this->serie'
        and sucursal = '$sucursal'";
        
        // ✅ LOG para debug
        error_log("modificarPorSerie SQL: " . $sql);
        error_log("Valores: empresa={$this->id_empresa}, tido={$this->id_tido}, serie={$this->serie}, numero={$this->numero}, sucursal={$sucursal}");
        
        $this->sql = $sql;
        $result = $this->conectar->query($sql);
        
        if (!$result) {
            $this->sql_error = $this->conectar->error;
            error_log("Error en DocumentoEmpresa::modificarPorSerie(): " . $this->sql_error);
            return false;
        }
        
        error_log("modificarPorSerie ejecutado correctamente. Filas afectadas: " . $this->conectar->affected_rows);
        
        return true;
    }

    public function verFilas($texto)
    {
        $sql = "select de.id_tido, ds.abreviatura, ds.cod_sunat, ds.nombre from documentos_empresas as de 
        inner join documentos_sunat ds on de.id_tido = ds.id_tido 
        where de.id_empresa = '$this->id_empresa' and de.id_tido in ($texto)";
        return $this->conectar->get_Cursor($sql);
    }
    public function consultarProveedor($doc)
    {
        $sql = "SELECT  
        proveedor_id FROM proveedores 
        where ruc = '$doc'";
        return $this->conectar->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
    public function insertarProveedor($ruc, $razon_social)
    {
        $sql = "INSERT INTO proveedores (ruc,razon_social)values ('$ruc', '$razon_social') ";
        $result = $this->conectar->query($sql);

        if ($result) {
            return $this->conectar->insert_id;
        }
    }
}